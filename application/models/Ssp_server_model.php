<?php

/**
 * SSP servers / priority profile / player binding model
 *
 * ssp_priority_profile uses "same name, multiple rows" pattern:
 * one profile name maps to N rows, each row binds one ssp_server_id with h0-h23 values.
 *
 * Scheduling (effective dates / weekday) lives in player_ssp_profile (the binding table).
 */
class Ssp_server_model extends CI_Model
{
    /** "no limit" date range placeholders (effective_date_start/end are NOT NULL) */
    const DATE_NO_LIMIT_START = '1970-01-01';
    const DATE_NO_LIMIT_END   = '2099-12-31';

    const HOUR_TARGET = 10;

    /* ---------------------------------------------------------------------
     * ssp_servers
     * ------------------------------------------------------------------ */

    public function get_server_list($offset = 0, $limit = -1, $order_item = 'name', $order = 'asc', $search = '')
    {
        $sort_fields = array('id', 'name', 'host', 'parser_class', 'priority', 'is_active', 'timeout');
        if (!in_array($order_item, $sort_fields)) {
            $order_item = 'name';
        }
        $order = strtolower($order) == 'desc' ? 'DESC' : 'ASC';

        $this->db->select('*');
        $this->db->from('ssp_servers');
        if ($search) {
            $this->db->group_start()
                ->like('name', $search)
                ->or_like('host', $search)
                ->or_like('parser_class', $search)
                ->group_end();
        }

        $count_db = clone $this->db;
        $total = $count_db->count_all_results();

        $this->db->order_by($order_item, $order);
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();

        return array('total' => $total, 'data' => $query->num_rows() ? $query->result() : array());
    }

    public function get_all_servers()
    {
        $this->db->select('*');
        $this->db->from('ssp_servers');
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        return $query->num_rows() ? $query->result() : array();
    }

    public function get_server($id)
    {
        $query = $this->db->get_where('ssp_servers', array('id' => $id));
        return $query->num_rows() ? $query->row() : false;
    }

    public function get_servers_by_ids($ids)
    {
        if (empty($ids)) {
            return array();
        }
        $this->db->select('*');
        $this->db->from('ssp_servers');
        $this->db->where_in('id', $ids);
        $query = $this->db->get();
        return $query->num_rows() ? $query->result() : array();
    }

    /**
     * Distinct profile names that use the given server.
     * Used to remind the admin to re-edit those profiles after the server's
     * priority or active state changed.
     */
    public function get_profiles_by_server($server_id)
    {
        $this->db->select('DISTINCT(name)');
        $this->db->from('ssp_priority_profile');
        $this->db->where('ssp_server_id', $server_id);
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();

        $names = array();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $names[] = $row->name;
            }
        }
        return $names;
    }

    public function get_server_by_name($exclude_id, $name)
    {
        $this->db->select('id');
        $this->db->from('ssp_servers');
        $this->db->where('name', $name);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        $query = $this->db->get();
        return $query->num_rows() ? $query->row() : false;
    }

    public function add_server($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        if ($this->db->insert('ssp_servers', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function update_server($data, $id)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update('ssp_servers', $data);
    }

    /**
     * Delete a server. Profile rows are removed by FK ON DELETE CASCADE,
     * but player bindings referencing those profile rows must be removed
     * first (their FK is ON DELETE NO ACTION).
     */
    public function delete_server($id)
    {
        $this->db->trans_start();

        $this->db->select('id');
        $this->db->from('ssp_priority_profile');
        $this->db->where('ssp_server_id', $id);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $profile_ids = array_column($query->result(), 'id');
            $this->db->where_in('ssp_profile_id', $profile_ids);
            $this->db->delete('player_ssp_profile');
        }

        $this->db->where('id', $id);
        $this->db->delete('ssp_servers');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /* ---------------------------------------------------------------------
     * ssp_priority_profile
     * ------------------------------------------------------------------ */

    /**
     * Profile list grouped by name, with related server count and bound player count.
     * DISTINCT is required: the LEFT JOIN multiplies rows per player binding.
     */
    public function get_profile_list($offset = 0, $limit = -1, $order = 'asc', $search = '')
    {
        $order = strtolower($order) == 'desc' ? 'DESC' : 'ASC';

        $sql = "SELECT spp.name, COUNT(DISTINCT spp.ssp_server_id) AS server_cnt, COUNT(DISTINCT psp.player_id) AS player_cnt
                FROM ssp_priority_profile spp
                LEFT JOIN player_ssp_profile psp ON psp.ssp_profile_id = spp.id";
        $params = array();
        if ($search) {
            $sql .= " WHERE spp.name LIKE ?";
            $params[] = '%' . $search . '%';
        }
        $sql .= " GROUP BY spp.name ORDER BY spp.name $order";

        $count_query = $this->db->query("SELECT COUNT(*) AS cnt FROM ($sql) t", $params);
        $total = $count_query->num_rows() ? (int)$count_query->row()->cnt : 0;

        if ($limit > 0) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = (int)$limit;
            $params[] = (int)$offset;
        }
        $query = $this->db->query($sql, $params);

        return array('total' => $total, 'data' => $query->num_rows() ? $query->result() : array());
    }

    /**
     * All rows of one profile (by name), joined with server info.
     */
    public function get_rows_by_name($name)
    {
        $this->db->select('spp.*, ss.name AS server_name, ss.priority AS server_priority');
        $this->db->from('ssp_priority_profile spp');
        $this->db->join('ssp_servers ss', 'ss.id = spp.ssp_server_id', 'left');
        $this->db->where('spp.name', $name);
        $this->db->order_by('ss.id', 'ASC');
        $query = $this->db->get();
        return $query->num_rows() ? $query->result() : array();
    }

    /**
     * Distinct profile names (for dropdowns).
     */
    public function get_profile_names()
    {
        $this->db->select('name');
        $this->db->from('ssp_priority_profile');
        $this->db->group_by('name');
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        if (!$query->num_rows()) {
            return array();
        }
        return array_column($query->result(), 'name');
    }

    /**
     * Check whether a profile name is already used by another profile.
     */
    public function profile_name_exists($name, $exclude_name = '')
    {
        $this->db->from('ssp_priority_profile');
        $this->db->where('name', $name);
        if ($exclude_name !== '' && $exclude_name !== null) {
            $this->db->where('name !=', $exclude_name);
        }
        return $this->db->count_all_results() > 0;
    }

    /**
     * Save a profile: remove all rows of $old_name and insert $rows under $new_name.
     * $rows: array of ['ssp_server_id' => id, 'h' => array(24 ints)]
     * Player bindings of removed rows are dropped as well.
     */
    public function save_profile($old_name, $new_name, $rows)
    {
        $this->db->trans_start();

        if ($old_name) {
            $this->delete_rows_by_name($old_name);
        }

        $now = date('Y-m-d H:i:s');
        foreach ($rows as $row) {
            $data = array(
                'name' => $new_name,
                'ssp_server_id' => $row['ssp_server_id'],
                'created_at' => $now,
                'updated_at' => $now,
            );
            for ($i = 0; $i < 24; $i++) {
                $data['h' . $i] = isset($row['h'][$i]) ? max(0, (int)$row['h'][$i]) : 0;
            }
            $this->db->insert('ssp_priority_profile', $data);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Delete all rows of a profile and the player bindings pointing to them.
     */
    public function delete_by_name($name)
    {
        $this->db->trans_start();
        $this->delete_rows_by_name($name);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    private function delete_rows_by_name($name)
    {
        $this->db->select('id');
        $this->db->from('ssp_priority_profile');
        $this->db->where('name', $name);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $ids = array_column($query->result(), 'id');
            $this->db->where_in('ssp_profile_id', $ids);
            $this->db->delete('player_ssp_profile');

            $this->db->where_in('id', $ids);
            $this->db->delete('ssp_priority_profile');
        }
    }

    /* ---------------------------------------------------------------------
     * player_ssp_profile (bindings)
     * ------------------------------------------------------------------ */

    /**
     * Bindings of one player, grouped by profile name + schedule.
     * match_priority / is_active are not editable in the UI and are ignored here.
     * Returns: array of ['profile_name','effective_date_start','effective_date_end','weekday','date_flag']
     */
    public function get_bindings_by_player($player_id)
    {
        $this->db->select('spp.name AS profile_name, psp.effective_date_start, psp.effective_date_end, psp.weekday, psp.date_flag');
        $this->db->from('player_ssp_profile psp');
        $this->db->join('ssp_priority_profile spp', 'spp.id = psp.ssp_profile_id');
        $this->db->where('psp.player_id', $player_id);
        $this->db->order_by('spp.name', 'ASC');
        $query = $this->db->get();

        $groups = array();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $key = $row->profile_name . '|' . $row->effective_date_start . '|' . $row->effective_date_end . '|' . $row->weekday;
                $groups[$key] = array(
                    'profile_name' => $row->profile_name,
                    'effective_date_start' => $row->effective_date_start,
                    'effective_date_end' => $row->effective_date_end,
                    'weekday' => (int)$row->weekday,
                    'date_flag' => (int)$row->date_flag,
                );
            }
        }
        return array_values($groups);
    }

    /**
     * Binding rule groups of one profile (by name).
     * Aggregated by schedule (dates + weekday) only: match_priority / is_active
     * are not editable in the UI and always saved with default values.
     * Returns: array of ['effective_date_start','effective_date_end','weekday','date_flag','player_ids' => []]
     */
    public function get_bindings_by_profile_name($name)
    {
        $this->db->select('psp.player_id, psp.effective_date_start, psp.effective_date_end, psp.weekday, psp.date_flag');
        $this->db->from('player_ssp_profile psp');
        $this->db->join('ssp_priority_profile spp', 'spp.id = psp.ssp_profile_id');
        $this->db->where('spp.name', $name);
        $query = $this->db->get();

        $groups = array();
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $key = $row->effective_date_start . '|' . $row->effective_date_end . '|' . $row->weekday;
                if (!isset($groups[$key])) {
                    $groups[$key] = array(
                        'effective_date_start' => $row->effective_date_start,
                        'effective_date_end' => $row->effective_date_end,
                        'weekday' => (int)$row->weekday,
                        'date_flag' => (int)$row->date_flag,
                        'player_ids' => array(),
                    );
                }
                $player_id = (int)$row->player_id;
                if (!in_array($player_id, $groups[$key]['player_ids'])) {
                    $groups[$key]['player_ids'][] = $player_id;
                }
            }
        }
        return array_values($groups);
    }

    /**
     * Replace all bindings of one player.
     * $bindings: array of ['profile_name','effective_date_start','effective_date_end',
     *                      'weekday','match_priority','is_active']
     */
    public function sync_player_bindings($player_id, $bindings)
    {
        $this->db->trans_start();

        $this->db->where('player_id', $player_id);
        $this->db->delete('player_ssp_profile');

        foreach ($bindings as $binding) {
            $this->db->select('id');
            $this->db->from('ssp_priority_profile');
            $this->db->where('name', $binding['profile_name']);
            $query = $this->db->get();
            if (!$query->num_rows()) {
                continue;
            }
            foreach ($query->result() as $row) {
                $this->db->insert('player_ssp_profile', $this->binding_row($player_id, $row->id, $binding));
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Find players whose NEW assignment would conflict with an existing binding
     * in another profile or with another assignment submitted in the same save.
     * Two assignments with real date ranges conflict when the ranges overlap
     * AND the weekday masks intersect. Additionally a player may have at most
     * one assignment without a date range.
     * $groups: same shape as sync_profile_bindings() input.
     * Returns: array of ['player_id', 'profile_name' (conflicting profile or
     *          '' for same-save conflicts), 'type' ('overlap' or 'nolimit')]
     */
    public function find_assignment_conflicts($profile_name, $groups)
    {
        $conflicts = array();
        $count = count($groups);
        for ($i = 0; $i < $count; $i++) {
            $group = $groups[$i];
            if (empty($group['player_ids'])) {
                continue;
            }
            $g_start = isset($group['effective_date_start']) ? $group['effective_date_start'] : '';
            $g_end = isset($group['effective_date_end']) ? $group['effective_date_end'] : '';
            $g_weekday = isset($group['weekday']) ? (int)$group['weekday'] : 127;
            $g_real = self::is_real_range($g_start, $g_end);

            foreach ($group['player_ids'] as $player_id) {
                // 1) against the other assignments submitted in the same save
                for ($j = $i + 1; $j < $count; $j++) {
                    $other = $groups[$j];
                    if (empty($other['player_ids']) || !in_array($player_id, $other['player_ids'])) {
                        continue;
                    }
                    $o_start = isset($other['effective_date_start']) ? $other['effective_date_start'] : '';
                    $o_end = isset($other['effective_date_end']) ? $other['effective_date_end'] : '';
                    $o_weekday = isset($other['weekday']) ? (int)$other['weekday'] : 127;
                    $o_real = self::is_real_range($o_start, $o_end);

                    if (!$g_real && !$o_real) {
                        $conflicts[] = array('player_id' => (int)$player_id, 'profile_name' => '', 'type' => 'nolimit');
                        break;
                    }
                    if ($g_real && $o_real
                        && $g_start <= $o_end && $o_start <= $g_end
                        && ($g_weekday & $o_weekday)) {
                        $conflicts[] = array('player_id' => (int)$player_id, 'profile_name' => '', 'type' => 'overlap');
                        break;
                    }
                }

                // 2) against existing bindings in other profiles
                $conflict = $this->get_binding_conflict($player_id, $profile_name, $group);
                if ($conflict) {
                    $conflicts[] = array(
                        'player_id' => (int)$player_id,
                        'profile_name' => $conflict['profile_name'],
                        'type' => $g_real ? 'overlap' : 'nolimit',
                    );
                }
            }
        }
        return $conflicts;
    }

    /**
     * Check one new assignment of one player against the bindings in OTHER
     * profiles. A new assignment with a real range conflicts with a binding
     * that also has a real range when dates AND weekdays overlap. A new
     * assignment without a date range conflicts with any existing binding
     * without a date range (only one per player allowed).
     * Returns the conflicting binding row or false.
     */
    public function get_binding_conflict($player_id, $exclude_profile_name, $new)
    {
        $new_start = isset($new['effective_date_start']) ? $new['effective_date_start'] : '';
        $new_end = isset($new['effective_date_end']) ? $new['effective_date_end'] : '';
        $new_weekday = isset($new['weekday']) ? (int)$new['weekday'] : 127;
        $new_real = self::is_real_range($new_start, $new_end);

        foreach ($this->get_bindings_by_player($player_id) as $b) {
            if ($b['profile_name'] === $exclude_profile_name) {
                continue; // bindings of the same profile are replaced on save
            }
            $b_real = self::is_real_range($b['effective_date_start'], $b['effective_date_end']);
            if (!$new_real) {
                // a player may have at most one assignment without date range
                if (!$b_real) {
                    return $b;
                }
                continue;
            }
            if (!$b_real) {
                continue;
            }
            if ($new_start <= $b['effective_date_end'] && $b['effective_date_start'] <= $new_end
                && ($new_weekday & $b['weekday'])) {
                return $b;
            }
        }
        return false;
    }

    /**
     * A range is "real" when both dates are set and none of them is a
     * "no limit" placeholder.
     */
    private static function is_real_range($start, $end)
    {
        $placeholders = array('', self::DATE_NO_LIMIT_START, self::DATE_NO_LIMIT_END, '9999-12-31', '0000-00-00');
        return !in_array($start, $placeholders) && !in_array($end, $placeholders);
    }

    /**
     * Replace all bindings of one profile (profile edit page).
     * $groups: array of ['player_ids' => [], 'effective_date_start', 'effective_date_end',
     *                    'weekday', 'match_priority', 'is_active']
     */
    public function sync_profile_bindings($profile_name, $groups)
    {
        $this->db->trans_start();

        $this->db->select('id');
        $this->db->from('ssp_priority_profile');
        $this->db->where('name', $profile_name);
        $query = $this->db->get();
        $profile_ids = $query->num_rows() ? array_column($query->result(), 'id') : array();

        if ($profile_ids) {
            $this->db->where_in('ssp_profile_id', $profile_ids);
            $this->db->delete('player_ssp_profile');

            foreach ($groups as $group) {
                if (empty($group['player_ids'])) {
                    continue;
                }
                foreach ($group['player_ids'] as $player_id) {
                    foreach ($profile_ids as $profile_id) {
                        $this->db->insert('player_ssp_profile', $this->binding_row($player_id, $profile_id, $group));
                    }
                }
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    private function binding_row($player_id, $profile_id, $binding)
    {
        return array(
            'player_id' => (int)$player_id,
            'ssp_profile_id' => (int)$profile_id,
            // date_flag: 1 = a real date range is set, 0 = no limit (placeholders)
            'date_flag' => isset($binding['date_flag']) ? (int)$binding['date_flag'] : (!empty($binding['effective_date_start']) ? 1 : 0),
            'effective_date_start' => !empty($binding['effective_date_start']) ? $binding['effective_date_start'] : self::DATE_NO_LIMIT_START,
            'effective_date_end' => !empty($binding['effective_date_end']) ? $binding['effective_date_end'] : self::DATE_NO_LIMIT_END,
            'weekday' => isset($binding['weekday']) ? (int)$binding['weekday'] : 127,
            'match_priority' => isset($binding['match_priority']) ? (int)$binding['match_priority'] : 0,
            'is_active' => isset($binding['is_active']) ? (int)$binding['is_active'] : 1,
        );
    }
}
