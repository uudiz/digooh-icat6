<?php

class TimersController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('config');
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
        $this->addJs("/assets/js/timers.js", false);
        $data = $this->get_data();
        if ($this->get_auth() <= 2) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/timers/index';
        }
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getTableData()
    {
        $name = $this->input->post('search');

        $this->load->model('strategy');
        $data = $this->get_data();
        $offset = $this->input->post('offset');
        $limit = $this->input->post('limit');
        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');

        $filter_array = array();
        if ($name) {
            $filter_array['name'] = $name;
        }

        $rest = $this->strategy->get_timer_list($this->get_cid(), $offset, $limit, $order_item, $order, $name);
        $data['total'] = $rest['total'];
        $data['rows'] = $rest['data'];

        echo json_encode($data);
    }


    public function edit()
    {
        $this->addJs("/assets/js/form.js", false);
        $this->load->model('device');

        $cid = $this->get_cid();
        $data = $this->get_data();
        $id = $this->input->get('id');
        $data['title'] = $this->lang->line('create.timer.config');

        if ($id) {
            $data['title'] = $this->lang->line('edit.timer.config');
            $this->load->model('strategy');
            $config = $this->strategy->get_timer($id);

            $config->offweekdays = explode(',', $config->offweekdays);

            if ($config) {
                $extra_timer = $this->strategy->get_timer_extra($id);
                $extra = array();

                for ($week = 0; $week <= 7; $week++) {
                    $dayarray = array();
                    foreach ($extra_timer as $item) {
                        if ($item->week == $week) {
                            sscanf($item->start_time, "%02d:%02d", $startH, $startM);
                            sscanf($item->end_time, "%02d:%02d", $endH, $endM);

                            $dayarray[] = array('status' => $item->status, 'start_time' => $item->start_time, 'end_time' => $item->end_time);
                        }
                    }
                    $extra[$week] = $dayarray;
                }

                $config->extra = json_encode($extra);
                $data['data'] = $config;
            }
        }

        $data['body_file'] = 'bootstrap/timers/form';
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }
    public function do_save()
    {
        $result = array();
        $cid = $this->get_cid();
        $timer_id = $this->input->post('id');
        if ($cid <= 0) {
            $result = array('code' => 1, 'msg' => $this->lang->line('warn.system.user'));
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $result = array('code' => 1, 'msg' => validation_errors());
            } else {
                $this->load->model('strategy');
                $name = $this->input->post('name');
                $flag = $this->strategy->get_timer_by_name($timer_id, $cid, $this->input->post('name'));
                if ($flag) {
                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('timer.name.exsit'), $this->input->post('name')));
                } else {

                    $data = array(
                        'name' => $this->input->post('name'), 'descr' => $this->input->post('descr'), 'type' => $this->input->post('type'),
                        'company_id' => $cid, 'add_time' => date('Y-m-d H:i:s'), 'offweekdays' => $this->input->post('off_weekdays')
                    );
                    if ($timer_id > 0) {


                        $this->strategy->update_timer($data, $timer_id);
                        //更新player 
                        $this->load->model('device');
                        $this->device->update_player_add_time(array('timer_config_flag' => 1), $timer_id, $this->get_cid());
                        $this->device->update_player_add_time(array('timer_update' => date('Y-m-d H:i:s')), $timer_id, $this->get_cid());
                        //delete extra
                        $this->strategy->delete_timer_extra($timer_id);
                    } else {
                        $timer_id = $this->strategy->add_timer($data, $this->get_uid());
                    }

                    $extra = $this->input->post('extra');


                    if ($extra && !empty($extra)) {
                        foreach ($extra as $weekItems) {
                            foreach ($weekItems as $item) {
                                $item['timer_id'] = $timer_id;
                                $this->strategy->add_timer_extra($item);
                            }
                        }
                    }
                    /*
                    //save base
                    for ($i = 0; $i < count($start_time); $i++) {
                        for ($line = 0; $line < 3; $line++) {
                            //FIXME temp;
                            //if($line>=1) {
                            //	$status[$i][$line] = 1;
                            //	}

                            if ($week_id[$i][$line] > 0) {
                                $this->strategy->update_timer_extra(array('timer_id' => $id, 'start_time' => $start_time[$i][$line], 'end_time' => $end_time[$i][$line], 'week' => $i, 'status' => $status[$i][$line]), $week_id[$i][$line]);
                            } else {
                                $this->strategy->add_timer_extra(array('timer_id' => $id, 'start_time' => $start_time[$i][$line], 'end_time' => $end_time[$i][$line], 'week' => $i, 'status' => $status[$i][$line]));
                            }
                        }
                    }
                    */

                    if ($timer_id !== FALSE) {
                        $data['id'] = $timer_id;
                        $data['add_time'] = date('Y-m-d H:i:s');
                        $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                    } else {
                        $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('timer.config')));
                    }
                }
            }
        }

        echo json_encode($result);
    }

    public function do_delete()
    {
        $result = array();
        $id = $this->input->post("id");
        $this->load->model('strategy');
        if ($this->strategy->delete_timer($id)) {
            $result['code'] = 0;
            $result['msg'] = $this->lang->line('delete.success');
        } else {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('delete.fail');
        }

        echo json_encode($result);
    }

    public function edit_timer()
    {
        $this->addJs("config.js");


        $data = $this->get_data();
        $this->load->model('strategy');
        $id = $this->input->get('id');
        $config = $this->strategy->get_timer($id);

        $config->offweekdays = explode(',', $config->offweekdays);

        if ($config) {
            $extra = array();
            for ($i = 0; $i < 8; $i++) {
                //$extra[] = $this->strategy->get_timer_extra($id, $i);

                /************Backwards compatibility**************/
                $extra_timer = $this->strategy->get_timer_extra($id, $i);
                if (count($extra_timer) < 3) {
                    $tmpTimer = $extra_timer;
                    for ($j = count($extra_timer); $j < 3; $j++) {
                        $this->strategy->add_timer_extra(array('timer_id' => $id, 'start_time' => "00:00", 'end_time' => "00:00", 'week' => $i, 'status' => 1));
                    }
                }
                /************Backwards compatibility**************/

                $extra[] = $this->strategy->get_timer_extra($id, $i);
            }
            foreach ($extra as $el) {

                foreach ($el as $e) {
                    $tmp = explode(':', $e->start_time);
                    $e->start_hour = $tmp[0];
                    $e->start_minute = $tmp[1];
                    $tmp = explode(':', $e->end_time);
                    $e->end_hour = $tmp[0];
                    $e->end_minute = $tmp[1];
                }
            }
            $config->extra = $extra;
            $data['config'] = $config;
            $data['body_file'] = 'config/timer/edit';
            $this->load->view('include/main2', $data);
        } else {
            $this->show_msg($this->lang->line('warn.param'), 'warn');
        }
    }

    public function do_save_timer()
    {
        $result = array();
        $cid = $this->get_cid();
        $id = $this->input->post('id');
        if ($cid <= 0) {
            $result = array('code' => 1, 'msg' => $this->lang->line('warn.system.user'));
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $result = array('code' => 1, 'msg' => validation_errors());
            } else {
                $this->load->model('strategy');
                $week_id = $this->input->post('week_id');
                $status = $this->input->post('status');
                $start_time = $this->input->post('start_time');
                $end_time = $this->input->post('end_time');
                if (empty($status) || empty($start_time) || empty($end_time)) {
                    $result = array('code' => 1, 'msg' => $this->lang->line('param.error'));
                } else {
                    $flag = $this->strategy->get_timer_by_name($id, $cid, $this->input->post('name'));
                    if ($flag) {
                        $result = array('code' => 1, 'msg' => sprintf($this->lang->line('timer.name.exsit'), $this->input->post('name')));
                    } else {

                        $data = array(
                            'name' => $this->input->post('name'), 'descr' => $this->input->post('descr'), 'type' => $this->input->post('type'),
                            'company_id' => $cid, 'add_time' => date('Y-m-d H:i:s'), 'offweekdays' => $this->input->post('off_weekdays')
                        );
                        if ($id > 0) {
                            //delete extra
                            //$this->strategy->delete_timer_extra($id);
                            $this->strategy->update_timer($data, $id);
                            //更新player 
                            $this->load->model('device');
                            $this->device->update_player_add_time(array('timer_config_flag' => 1), $id, $this->get_cid());
                            $this->device->update_player_add_time(array('timer_update' => date('Y-m-d H:i:s')), $id, $this->get_cid());
                        } else {
                            $id = $this->strategy->add_timer($data, $this->get_uid());
                        }

                        //save base
                        for ($i = 0; $i < count($start_time); $i++) {
                            for ($line = 0; $line < 3; $line++) {
                                //FIXME temp;
                                //if($line>=1) {
                                //	$status[$i][$line] = 1;
                                //	}

                                if ($week_id[$i][$line] > 0) {
                                    $this->strategy->update_timer_extra(array('timer_id' => $id, 'start_time' => $start_time[$i][$line], 'end_time' => $end_time[$i][$line], 'week' => $i, 'status' => $status[$i][$line]), $week_id[$i][$line]);
                                } else {
                                    $this->strategy->add_timer_extra(array('timer_id' => $id, 'start_time' => $start_time[$i][$line], 'end_time' => $end_time[$i][$line], 'week' => $i, 'status' => $status[$i][$line]));
                                }
                            }
                        }

                        if ($id !== FALSE) {
                            $data['id'] = $id;
                            $data['add_time'] = date('Y-m-d H:i:s');
                            $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                            $result = array_merge($data, $result);
                        } else {
                            $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('timer.config')));
                        }
                    }
                }
            }
        }

        echo json_encode($result);
    }
}
