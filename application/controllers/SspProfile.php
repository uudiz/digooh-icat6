<?php

class SspProfile extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('ssp');
        $this->lang->load('player');
        $this->load->model('Ssp_server_model');
    }

    public function index()
    {
        $data = $this->get_data();
        if ($this->get_auth() <= 2) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/ssp_profiles/index';
        }
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getTableData()
    {
        $search = $this->input->post('search');
        $offset = (int)$this->input->post('offset');
        $limit = (int)$this->input->post('limit');
        $order = $this->input->post('order');

        $ret = $this->Ssp_server_model->get_profile_list($offset, $limit, $order, $search);

        $data['total'] = $ret['total'];
        $data['rows']  = $ret['data'];

        echo json_encode($data);
    }

    public function edit()
    {
        $this->addJs("/assets/js/form.js", false);
        $data = $this->get_data();

        if ($this->get_auth() <= 2) {
            $data['body_file'] = 'bootstrap/401';
            $this->load->view('bootstrap/layout/basiclayout', $data);
            return;
        }

        $name = $this->input->get('name');
        $data['title'] = $this->lang->line('ssp.create.profile');

        $rows = array();
        $binding_groups = array();
        if ($name) {
            $data['title'] = $this->lang->line('ssp.edit.profile');
            $rows = $this->Ssp_server_model->get_rows_by_name($name);
            if (empty($rows)) {
                $this->show_msg($this->lang->line('warn.param'), 'warn');
                return;
            }
            $binding_groups = $this->Ssp_server_model->get_bindings_by_profile_name($name);
        }

        // convert profile rows to plain arrays for the JS grid
        $profile_rows = array();
        foreach ($rows as $row) {
            $h = array();
            for ($i = 0; $i < 24; $i++) {
                $h[] = (int)$row->{'h' . $i};
            }
            $profile_rows[] = array(
                'ssp_server_id' => (int)$row->ssp_server_id,
                'server_name' => $row->server_name,
                'server_priority' => (int)$row->server_priority,
                'h' => $h,
            );
        }

        // all servers for the multi-select (with priority for auto calculation)
        $servers = array();
        foreach ($this->Ssp_server_model->get_all_servers() as $s) {
            $servers[] = array(
                'id' => (int)$s->id,
                'name' => $s->name,
                'priority' => (int)$s->priority,
                'is_active' => (int)$s->is_active,
            );
        }

        $this->load->model('device');
        $players = $this->device->get_player_list($this->get_cid());

        // existing bindings of every player (across all profiles), used by the
        // client side assignment conflict check
        $player_bindings = array();
        foreach ($players['data'] as $p) {
            $player_bindings[(int)$p->id] = $this->Ssp_server_model->get_bindings_by_player($p->id);
        }

        // criteria / tags are used by the player search modal (player_map)
        $cris = $this->get_criteria($this->get_cid(), $this->get_parent_company_id());
        $tags = $this->device->get_tag_list($this->get_cid());

        $data['profile_name'] = $name ? $name : '';
        $data['profile_rows_json'] = json_encode($profile_rows);
        $data['servers_json'] = json_encode($servers);
        $data['binding_groups_json'] = json_encode($binding_groups);
        $data['players'] = $players['data'];
        $data['player_bindings_json'] = json_encode($player_bindings);
        $data['criteria'] = $cris['criteria'];
        $data['tags'] = $tags['data'];

        $data['body_file'] = 'bootstrap/ssp_profiles/form';
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    /**
     * AJAX check whether a profile name is already taken.
     */
    public function name_exists()
    {
        $name = $this->input->post('name');
        $exclude = $this->input->post('exclude');
        $exists = $name ? $this->Ssp_server_model->profile_name_exists($name, $exclude) : false;
        echo json_encode(array('code' => $exists ? 1 : 0));
    }

    public function do_save()
    {
        $result = array();

        if ($this->get_auth() < $this->config->item('auth_admin')) {
            echo json_encode(array('code' => 1, 'msg' => $this->lang->line('warn.system.user')));
            return;
        }

        $name = trim($this->input->post('name'));
        $old_name = trim($this->input->post('old_name'));

        if ($name === '') {
            echo json_encode(array('code' => 1, 'msg' => sprintf($this->lang->line('field.required'), $this->lang->line('name'))));
            return;
        }

        // rename / create duplicate check
        if ($this->Ssp_server_model->profile_name_exists($name, $old_name)) {
            echo json_encode(array('code' => 1, 'msg' => sprintf($this->lang->line('ssp.name.exists'), $name)));
            return;
        }

        $rows = json_decode($this->input->post('servers_json'), true);
        if (empty($rows) || !is_array($rows)) {
            echo json_encode(array('code' => 1, 'msg' => $this->lang->line('ssp.select.servers')));
            return;
        }

        // normalize and validate hourly sums
        $normalized = array();
        foreach ($rows as $row) {
            if (empty($row['ssp_server_id']) || empty($row['h']) || count($row['h']) != 24) {
                echo json_encode(array('code' => 1, 'msg' => $this->lang->line('warn.param')));
                return;
            }
            $h = array();
            foreach ($row['h'] as $v) {
                $v = (int)$v;
                if ($v < 0 || $v > Ssp_server_model::HOUR_TARGET) {
                    echo json_encode(array('code' => 1, 'msg' => $this->lang->line('warn.param')));
                    return;
                }
                $h[] = $v;
            }
            $normalized[] = array('ssp_server_id' => (int)$row['ssp_server_id'], 'h' => $h);
        }

        for ($i = 0; $i < 24; $i++) {
            $sum = 0;
            foreach ($normalized as $row) {
                $sum += $row['h'][$i];
            }
            if ($sum != Ssp_server_model::HOUR_TARGET) {
                echo json_encode(array('code' => 1, 'msg' => sprintf($this->lang->line('ssp.hour.sum.error'), $i, Ssp_server_model::HOUR_TARGET, $sum)));
                return;
            }
        }

        // binding rule groups (optional)
        $old_groups = array();
        if ($old_name !== '') {
            $old_groups = $this->Ssp_server_model->get_bindings_by_profile_name($old_name);
        }

        $bindings_json = $this->input->post('bindings_json');
        if ($bindings_json !== null && $bindings_json !== false && $bindings_json !== '') {
            $groups = json_decode($bindings_json, true);
            if (!is_array($groups)) {
                $groups = $old_groups;
            }
        } else {
            // bindings untouched on the client: keep existing ones on rename
            $groups = $old_groups;
        }

        // reject assignments that overlap an existing binding of the same
        // player in another profile (date range AND weekday overlap) or that
        // give a player more than one assignment without a date range
        $normalized_groups = $this->normalize_groups($groups);
        $conflicts = $this->Ssp_server_model->find_assignment_conflicts($old_name, $normalized_groups);
        if ($conflicts) {
            // use player names in the error message
            $player_names = array();
            $this->load->model('device');
            $player_list = $this->device->get_player_list($this->get_cid());
            foreach ($player_list['data'] as $p) {
                $player_names[(int)$p->id] = $p->name;
            }

            $overlap = array();
            $nolimit = array();
            foreach ($conflicts as $c) {
                $label = isset($player_names[$c['player_id']]) ? $player_names[$c['player_id']] : ('#' . $c['player_id']);
                if ($c['profile_name'] !== '') {
                    $label .= ' (' . $c['profile_name'] . ')';
                }
                if ($c['type'] === 'nolimit') {
                    $nolimit[] = $label;
                } else {
                    $overlap[] = $label;
                }
            }
            $msgs = array();
            if ($overlap) {
                $msgs[] = sprintf($this->lang->line('ssp.assignment.conflict'), implode(', ', $overlap));
            }
            if ($nolimit) {
                $msgs[] = sprintf($this->lang->line('ssp.assignment.nolimit'), implode(', ', $nolimit));
            }
            echo json_encode(array('code' => 1, 'msg' => implode(' ', $msgs)));
            return;
        }

        if ($this->Ssp_server_model->save_profile($old_name, $name, $normalized)) {
            $this->Ssp_server_model->sync_profile_bindings($name, $normalized_groups);
            $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
        } else {
            $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('ssp.profiles')));
        }

        echo json_encode($result);
    }

    /**
     * Normalize binding groups coming from the client.
     */
    private function normalize_groups($groups)
    {
        $ret = array();
        foreach ($groups as $group) {
            if (empty($group['player_ids'])) {
                continue;
            }
            $player_ids = array();
            foreach ($group['player_ids'] as $pid) {
                $pid = (int)$pid;
                if ($pid > 0) {
                    $player_ids[] = $pid;
                }
            }
            if (empty($player_ids)) {
                continue;
            }
            $ret[] = array(
                'player_ids' => $player_ids,
                'effective_date_start' => isset($group['effective_date_start']) ? $group['effective_date_start'] : '',
                'effective_date_end' => isset($group['effective_date_end']) ? $group['effective_date_end'] : '',
                'weekday' => isset($group['weekday']) ? (int)$group['weekday'] : 127,
                'match_priority' => isset($group['match_priority']) ? (int)$group['match_priority'] : 0,
                'is_active' => isset($group['is_active']) ? (int)$group['is_active'] : 1,
            );
        }
        return $ret;
    }

    /**
     * Delete a whole profile by name. remove_resource posts the name as "id".
     */
    public function do_delete()
    {
        $result = array();

        if ($this->get_auth() < $this->config->item('auth_admin')) {
            echo json_encode(array('code' => 1, 'msg' => $this->lang->line('warn.system.user')));
            return;
        }

        $name = $this->input->post('id');
        if ($name && $this->Ssp_server_model->delete_by_name($name)) {
            $result['code'] = 0;
            $result['msg']  = $this->lang->line('delete.success');
        } else {
            $result['code'] = 1;
            $result['msg']  = sprintf($this->lang->line('delete.fail'), $this->lang->line('ssp.profiles'));
        }

        echo json_encode($result);
    }
}
