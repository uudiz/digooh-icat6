<?php
class Peripheral_controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('peripheral');
        $this->load->model('peripheral');
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
            $data['body_file'] = 'bootstrap/peripheral/index';
        }


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

        $rest = $this->peripheral->get_list($this->get_cid(), $offset, $limit, $order_item, $order, $filter_array);
        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];

        echo json_encode($data);
    }


    public function edit()
    {
        $this->addJs("/assets/js/form.js", false);
        $this->load->model('device');
        $cid = $this->get_cid();
        $data = $this->get_data();
        $id = $this->input->get('id');
        $data['title'] = $this->lang->line('create') . " " . $this->lang->line('peripheral');

        if ($id) {
            $data['title'] = $this->lang->line('edit') . " " . $this->lang->line('peripheral');
            $item = $this->peripheral->get_item($id);
            if ($item) {

                $attached_players = $this->peripheral->get_peripheral_player_ids($item->id);
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

        $data['body_file'] = 'bootstrap/peripheral/form';
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

        $cid = $this->get_cid();
        $id = $this->input->post('id');
        if ($cid <= 0) {
            $result = array('code' => 1, 'msg' => $this->lang->line('warn.system.user'));
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', $this->lang->line('peripheral'), 'trim|required');

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
                $this->load->helper('chrome_logger');
                $data['company_id'] = $cid;
                $flag = $this->peripheral->get_item_by_name($id, $cid, $this->input->post('name'));
                if ($id >= 0 && $flag) {
                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('peripheral.name.exist'), $this->input->post('name')));
                } else {
                    if ($id > 0) {
                        $this->peripheral->update_item($data, $id);
                    } else {
                        $id = $this->peripheral->add_item($data);
                    }

                    if ($id !== false) {
                        $players = $this->input->post('players');


                        if ($players) {
                            $this->peripheral->sync_peripheral_player($players, $id);
                        }

                        $data['id'] = $id;
                        $data['add_time'] = date('Y-m-d H:i:s');
                        $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                        $result = array_merge($data, $result);
                    } else {
                        $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('group')));
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

        if ($this->peripheral->delete_item($gid)) {
            $result['code'] = 0;
            $result['msg']  = $this->lang->line('delete.success');
        } else {
            $result['code'] = 1;
            $result['msg']  = $this->lang->line('delete.fail');
        }

        echo json_encode($result);
    }


    function detail()
    {
        $data['peripheral_id'] = $this->input->get('id');
        $this->load->view('bootstrap/peripheral/detail', $data);
    }

    public function getCommand()
    {
        $id = $this->input->post('id');
        $item = $this->peripheral->get_command($id);
        $data['code'] = 0;
        $data['data'] = $item;
        echo json_encode($data);
    }

    public function getCommandTableData()
    {
        $name = $this->input->post('search');
        $peripheral_id = $this->input->post('peripheral_id');

        $data = $this->get_data();
        $offset = $this->input->post('offset');
        $limit = $this->input->post('limit');
        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');

        $filter_array = array();
        if ($name) {
            $filter_array['name'] = $name;
        }
        if ($peripheral_id) {
            $filter_array['peripheral_id'] = $peripheral_id;
        }


        $rest = $this->peripheral->get_peripheral_command_list($peripheral_id, $offset, $limit, $order_item, $order, $filter_array);
        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];

        echo json_encode($data);
    }

    public function do_save_command()
    {
        $result = array();

        $cid = $this->get_cid();
        $id = $this->input->post('id');
        if ($cid <= 0) {
            $result = array('code' => 1, 'msg' => $this->lang->line('warn.system.user'));
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', $this->lang->line('command'), 'trim|required');

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
                $flag = $this->peripheral->get_item_by_name($id, $cid, $this->input->post('name'));
                if ($id >= 0 && $flag) {
                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('peripheral.name.exist'), $this->input->post('name')));
                } else {
                    if ($id > 0) {
                        $this->peripheral->update_command($data, $id);
                    } else {
                        $id = $this->peripheral->add_command($data);
                    }

                    if ($id !== false) {
                        $data['id'] = $id;
                        $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                        $result = array_merge($data, $result);
                    } else {
                        $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('peripheral')));
                    }
                }
            }
        }
        $this->load->model('peripheral');

        $this->peripheral->get_scheduled_commands(true);
        echo json_encode($result);
    }
    public function deleteCommand()
    {
        $result = array();
        $gid = $this->input->post("id");


        if ($this->peripheral->delete_command($gid)) {
            $result['code'] = 0;
            $result['msg']  = $this->lang->line('delete.success');
        } else {
            $result['code'] = 1;
            $result['msg']  = $this->lang->line('delete.fail');
        }
        $this->peripheral->get_scheduled_commands(true);
        echo json_encode($result);
    }
}
