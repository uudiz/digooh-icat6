<?php
class Threshold_controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('threshold');
    }
    /**
     * 
     *
     * @param object $curpage [optional]
     * @param object $order_item [optional]
     * @param object $order [optional]
     * @return
     */
    public function index()
    {
        $this->load->model('device');
        $data = $this->get_data();
        if ($this->get_auth() <= 2) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/sensors/index';
        }

        $this->load->model('device');
        $players = $this->device->get_player_list($this->get_cid());
        $data['players'] = $players['data'];

        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getTableData()
    {
        $name = $this->input->post('search');
        $player_id = $this->input->post('player_id');

        $data = $this->get_data();
        $offset = $this->input->post('offset');
        $limit = $this->input->post('limit');
        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');


        $filter_array = array();
        if ($name) {
            $filter_array['name'] = $name;
        }
        if ($player_id) {
            $filter_array['player_id'] = $player_id;
        }

        $player =  $this->input->post("player");
        if ($player && $player != -1) {
            $filter_array['player_id'] = $player;
        }

        $rest = $this->threshold->get_list($this->get_cid(), $offset, $limit, $order_item, $order, $filter_array);
        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];

        echo json_encode($data);
    }


    public function edit()
    {
        $this->addJs("/assets/js/form.js", false);
        $this->load->model('threshold');
        $this->load->model('device');
        $cid = $this->get_cid();
        $data = $this->get_data();
        $id = $this->input->get('id');
        $data['title'] = $this->lang->line('create.peripheral');

        if ($id) {
            $data['title'] = $this->lang->line('edit.peripheral');
            $item = $this->threshold->get_item($id);
            if ($item) {

                $attached_players = $this->threshold->get_player_ids_by_threshold($item->id);
                if ($attached_players) {
                    $item->players = $attached_players;
                }
                $data['data'] = $item;
            } else {
                $this->show_msg($this->lang->line('warn.param'), 'warn');
                return;
            }
        }

        $players = $this->device->get_player_list($cid);
        $data['players'] = $players['data'];


        $data['players'] = $players['data'];

        $data['body_file'] = 'bootstrap/sensors/form';
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    /**
     * Save
     *
     * @return
     */
    public function do_save()
    {
        $result = array();
        $this->load->model('threshold');
        $cid = $this->get_cid();
        $id = $this->input->post('id');
        if ($cid <= 0) {
            $result = array('code' => 1, 'msg' => $this->lang->line('warn.system.user'));
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', $this->lang->line('sensor_thresholds'), 'trim|required');

            if ($this->form_validation->run() == false) {
                $result = array('code' => 1, 'msg' => validation_errors());
            } else {
                $this->load->model('device');

                foreach ($this->input->post() as $key => $val) {
                    if (
                        $key != "id" && $key != "players"
                    ) {
                        $data[$key] = $val;
                    }
                }
                $data['company_id'] = $cid;
                $flag = $this->threshold->get_item_by_name($id, $cid, $this->input->post('name'));
                if ($id >= 0 && $flag) {
                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('name_exist'), $this->input->post('name')));
                } else {
                    if ($id > 0) {
                        $this->threshold->update_item($data, $id);
                    } else {
                        $id = $this->threshold->add_item($data);
                    }

                    if ($id !== false) {
                        $players = $this->input->post('players');

                        $this->threshold->sync_threshold_player($players, $id);

                        $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                    } else {
                        $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('sensor_thresholds')));
                    }
                }
            }
        }
        echo json_encode($result);
    }

    public function do_delete()
    {
        $result = array();
        $gid = $this->input->post("id");
        $this->load->model('threshold');
        if ($this->threshold->delete_item($gid)) {
            $result['code'] = 0;
            $result['msg']  = $this->lang->line('delete.success');
        } else {
            $result['code'] = 1;
            $result['msg']  = $this->lang->line('delete.fail');
        }

        echo json_encode($result);
    }
}
