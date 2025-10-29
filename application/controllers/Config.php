<?php 
class Config extends MY_Controller {

	public function __construct(){
		parent::__construct();
        $this->lang->load('config');
    }
    
    /**
     * 下载策略列表
     *
     * @return
     */
    public function downloads($curpage = 1) {
        $this->refresh_downloads($curpage, TRUE);
    }
    
    public function refresh_downloads($curpage = 1, $main = FALSE) {
        $this->load->model('strategy');
        $this->addJs("config.js");
        
        $data = $this->get_data();
        $limit = $this->config->item('page_default_size');
        $offset = ($curpage - 1) * $limit;
		$add_user_id=FALSE;
		if($this->is_group()){
			$add_user_id=$this->get_uid();
		}
		
        $views = $this->strategy->get_download_list($this->get_cid(), $offset, $limit, $add_user_id);
        if(count($views['data']) > 0){
        	foreach($views['data'] as $d){
        		$d->extras = $this->strategy->get_download_extra($d->id);
				$d->groups = $this->strategy->get_download_group($d->id);
        	}
        }
        $data['total'] = $views['total'];
        $data['data'] = $views['data'];
        $data['curpage'] = $curpage;
        $data['limit'] = $limit;
        $data['body_file'] = 'config/download/list';
        
        if ($main) {
        
            $this->load->view('include/main2', $data);
        } else {
            $this->load->view($data['body_file'], $data);
        }
    }

    
    public function add_download() {
        $data = $this->get_data();
        
        $this->load->view('config/download/new', $data);
    }
    
    public function add_download_row() {
        $data = $this->get_data();
        
        $this->load->view('config/download/new_row', $data);
    }
    
    public function edit_download() {
        $data = $this->get_data();
        $this->load->model('strategy');
        $id = $this->input->get('id');
        $config = $this->strategy->get_download($id);
        if ($config) {
            $config->extra = $this->strategy->get_download_extra($id);
            $data['config'] = $config;
            $this->load->view('config/download/edit', $data);
        } else {
            $this->show_msg($this->lang->line('warn.param'), 'warn');
        }
    }
    
    public function do_save_download() {
        $result = array();
        
        $cid = $this->get_cid();
        $id = $this->input->post('id');
        if ($cid <= 0) {
            $result = array('code'=>1, 'msg'=>$this->lang->line('warn.system.user'));
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');
            
            if ($this->form_validation->run() == FALSE) {
                $result = array('code'=>1, 'msg'=>validation_errors());
            } else {
                $this->load->model('strategy');
				 $flag = $this->strategy->get_download_by_name($id, $cid, $this->input->post('name'));
        		if($flag) {
        			$result = array('code'=>1, 'msg'=>sprintf($this->lang->line('download.name.exsit'), $this->input->post('name')));
        		}else {
					$start_time = $this->input->post('start_time');
					$end_time = $this->input->post('end_time');
					if ( empty($start_time) || empty($end_time)) {
						$result = array('code'=>1, 'msg'=>$this->lang->line('param.error'));
					} else {
						if ($id > 0) {
							//delete extra
							$this->strategy->delete_download_extra($id);
						}
						for ($i = 0; $i < count($start_time); $i++) {
							//set first to item info
							if ($i == 0) {
								$data = array('name'=>$this->input->post('name'), 'descr'=>$this->input->post('descr'), 'start_time'=>$start_time[$i], 'end_time'=>$end_time[$i], 'company_id'=>$cid);
								if ($id > 0) {
									$this->strategy->update_download($data, $id);
								} else {
									$id = $this->strategy->add_download($data, $this->get_uid());
								}
							} else {
								$this->strategy->add_download_extra(array('strategy_id'=>$id, 'start_time'=>$start_time[$i], 'end_time'=>$end_time[$i]));
							}
						}
						
						if ($id !== FALSE) {
							$data['id'] = $id;
							$data['add_time'] = date('Y-m-d H:i:s');
							$result = array('code'=>0, 'msg'=>$this->lang->line('save.success'));
							$result = array_merge($data, $result);
						} else {
							$result = array('code'=>1, 'msg'=>sprintf($this->lang->line('save.fail'), $this->lang->line('download.strategy')));
						}
					}
                }
            }
        }
        
        echo json_encode($result);
    }
    
    public function do_delete_download() {
        $result = array();
        $id = $this->input->post("id");
        $this->load->model('strategy');
        if ($this->strategy->delete_download($id)) {
            $result['code'] = 0;
            $result['msg'] = $this->lang->line('delete.success');
        } else {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('delete.fail');
        }
        
        echo json_encode($result);
    }

    
    /**
     * 显示策略
     *
     * @return
     */
    public function views($curpage = 1) {
        $this->refresh_views($curpage, TRUE);
    }
    
    public function refresh_views($curpage = 1, $main = FALSE) {
        $this->load->model('strategy');
        $this->addJs("config.js");
        $this->addJs('My97DatePicker/WdatePicker.js');
        $this->addCss('My97DatePicker/skin/WdatePicker.css');
        
        $data = $this->get_data();
        $limit = $this->config->item('page_default_size');
        $offset = ($curpage - 1) * $limit;
        $views = $this->strategy->get_view_list($this->get_cid(), $offset, $limit);
        
        $data['total'] = $views['total'];
        $data['data'] = $views['data'];
        $data['curpage'] = $curpage;
        $data['limit'] = $limit;
        $data['body_file'] = 'config/view/list';
        
        if ($main) {
        
            $this->load->view('include/main2', $data);
        } else {
            $this->load->view($data['body_file'], $data);
        }
    }
    
    public function add_view() {
        $data = $this->get_data();
        $data['start_time'] = date('Y-m-d H:i', time());
        $data['end_time'] = date('Y-m-d H:i', time() + 7 * 24 * 3600);
        
        $data['brightness'] = 50;
        $data['saturation'] = 50;
        $data['contrast'] = 50;
        
        $this->load->view('config/view/new', $data);
    }
    
    public function edit_view() {
        $data = $this->get_data();
        $this->load->model('strategy');
        $id = $this->input->get('id');
        $config = $this->strategy->get_view($id);
        if ($config) {
            $data['config'] = $config;
            $this->load->view('config/view/edit', $data);
        } else {
            $this->show_msg($this->lang->line('warn.param'), 'warn');
        }
    }
    
    /**
     * 添加或者更新显示设置
     *
     * @return
     */
    public function do_save_view() {
        $result = array();
        
        $cid = $this->get_cid();
        $id = $this->input->post('id');
        if ($cid <= 0) {
            $result = array('code'=>1, 'msg'=>$this->lang->line('warn.system.user'));
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');
            $this->form_validation->set_rules('start_datetime', $this->lang->line('start.time'), 'trim|required|regex_match[/\d{4}-\d{2}-\d{2} \d{2}:\d{2}/]');
            $this->form_validation->set_rules('end_datetime', $this->lang->line('end.time'), 'trim|required|regex_match[/\d{4}-\d{2}-\d{2} \d{2}:\d{2}/]');
            $this->form_validation->set_rules('brightness', $this->lang->line('brightness'), 'trim|required|numeric');
            $this->form_validation->set_rules('saturation', $this->lang->line('saturation'), 'trim|required|numeric');
            $this->form_validation->set_rules('contrast', $this->lang->line('contrast'), 'trim|required|numeric');

            
            if ($this->form_validation->run() == FALSE) {
                $result = array('code'=>1, 'msg'=>validation_errors());
            } else {
                $brightness = intval($this->input->post('brightness'));
                $saturation = intval($this->input->post('saturation'));
                $contrast = intval($this->input->post('contrast'));
                
                if ($brightness < 0 || $brightness > 100) {
                    $result = array('code'=>1, 'msg'=>sprintf($this->lang->line('warn.config.rang'), $this->lang->line('brightness')));
                } else if ($saturation < 0 || $saturation > 100) {
                    $result = array('code'=>1, 'msg'=>sprintf($this->lang->line('warn.config.rang'), $this->lang->line('saturation')));
                } else if ($contrast < 0 || $contrast > 100) {
                    $result = array('code'=>1, 'msg'=>sprintf($this->lang->line('warn.config.rang'), $this->lang->line('contrast')));
                } else {
                    $this->load->model('strategy');
                    $data = array('name'=>$this->input->post('name'), 'descr'=>$this->input->post('descr'), 'company_id'=>$cid, 'start_datetime'=>$this->input->post('start_datetime'), 'end_datetime'=>$this->input->post('end_datetime'), 'brightness'=>$this->input->post('brightness'), 'saturation'=>$this->input->post('saturation'), 'contrast'=>$this->input->post('contrast'), );
                    if ($id > 0) {
                        $this->strategy->update_view($data, $id);
                    } else {
                        $id = $this->strategy->add_view($data, $this->get_uid());
                    }
                    if ($id !== FALSE) {
                        $data['id'] = $id;
                        $data['add_time'] = date('Y-m-d H:i:s');
                        $result = array('code'=>0, 'msg'=>$this->lang->line('save.success'));
                        $result = array_merge($data, $result);
                    } else {
                        $result = array('code'=>1, 'msg'=>sprintf($this->lang->line('save.fail'), $this->lang->line('config.view')));
                    }
                }
            }
        }
        
        echo json_encode($result);
    }
    
    public function do_delete_view() {
        $result = array();
        $id = $this->input->post("id");
        $this->load->model('strategy');
        if ($this->strategy->delete_view($id)) {
            $result['code'] = 0;
            $result['msg'] = $this->lang->line('delete.success');
        } else {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('delete.fail');
        }
        
        echo json_encode($result);
    }
    
    public function timers($curpage = 1,$order_item='add_time', $order = 'desc') {
    	$this->refresh_timers($curpage, $order_item, $order,TRUE);
    }
    
    public function refresh_timers($curpage = 1, $order_item='add_time', $order = 'desc',$main = FALSE) {

    	$this->load->model('strategy');
        $this->addJs("config.js");
        
        $name = $this->input->get('name');
        $data = $this->get_data();
        $limit = $this->config->item('page_default_size');

        $offset = ($curpage - 1) * $limit;
        $views = $this->strategy->get_timer_list($this->get_cid(), $offset, $limit,$order_item,$order,$name);
        
        
        
        $data['total'] = $views['total'];
        $data['data'] = $views['data'];
        $data['curpage'] = $curpage;
        $data['order_item']=$order_item;
        $data['order']=$order;
        $data['limit'] = $limit;
   
        if ($main) {
        	$data['body_file'] = 'config/timer/timer';
        	$data['body_view'] = 'config/timer/list';
            $this->load->view('include/main2', $data);
        } else {
            $this->load->view('config/timer/list', $data);
        }
    }
    
    public function add_timer() {
        $this->addJs("config.js");
        
        $data = $this->get_data();
        $data['body_file'] = 'config/timer/new';
        $this->load->view('include/main2', $data);
    }
    
    public function edit_timer() {
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
                if(count($extra_timer)<3){
                	$tmpTimer = $extra_timer;
                	for($j=count($extra_timer);$j<3;$j++){
                		$this->strategy->add_timer_extra(array('timer_id'=>$id, 'start_time'=>"00:00", 'end_time'=>"00:00", 'week'=>$i, 'status'=>1));
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
    
    public function do_save_timer() {
        $result = array();
        $cid = $this->get_cid();
        $id = $this->input->post('id');
        if ($cid <= 0) {
            $result = array('code'=>1, 'msg'=>$this->lang->line('warn.system.user'));
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');
            
            if ($this->form_validation->run() == FALSE) {
                $result = array('code'=>1, 'msg'=>validation_errors());
            } else {
                $this->load->model('strategy');
                $week_id = $this->input->post('week_id');
                $status = $this->input->post('status');
                $start_time = $this->input->post('start_time');
                $end_time = $this->input->post('end_time');
                if ( empty($status) || empty($start_time) || empty($end_time)) {
                    $result = array('code'=>1, 'msg'=>$this->lang->line('param.error'));
                } 
				else {
	               	$flag = $this->strategy->get_timer_by_name($id, $cid, $this->input->post('name'));
	        		if($flag) {
	        			$result = array('code'=>1, 'msg'=>sprintf($this->lang->line('timer.name.exsit'), $this->input->post('name')));
	        		}
						else {

							$data = array('name'=>$this->input->post('name'), 'descr'=>$this->input->post('descr'), 'type'=>$this->input->post('type'), 
									'company_id'=>$cid,'add_time'=>date('Y-m-d H:i:s'),'offweekdays'=>$this->input->post('off_weekdays')
							);
							if ($id > 0) {
								//delete extra
								//$this->strategy->delete_timer_extra($id);
								$this->strategy->update_timer($data, $id);
								//更新player 
								$this->load->model('device');
								$this->device->update_player_add_time(array('timer_config_flag'=>1), $id, $this->get_cid());
								$this->device->update_player_add_time(array('timer_update'=>date('Y-m-d H:i:s')), $id, $this->get_cid());
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
										$this->strategy->update_timer_extra(array('timer_id'=>$id, 'start_time'=>$start_time[$i][$line], 'end_time'=>$end_time[$i][$line], 'week'=>$i, 'status'=>$status[$i][$line]), $week_id[$i][$line]);
									} else {
										$this->strategy->add_timer_extra(array('timer_id'=>$id, 'start_time'=>$start_time[$i][$line], 'end_time'=>$end_time[$i][$line], 'week'=>$i, 'status'=>$status[$i][$line]));
									}
								}
							}
							
							if ($id !== FALSE) {
								$data['id'] = $id;
								$data['add_time'] = date('Y-m-d H:i:s');
								$result = array('code'=>0, 'msg'=>$this->lang->line('save.success'));
								$result = array_merge($data, $result);
							} else {
								$result = array('code'=>1, 'msg'=>sprintf($this->lang->line('save.fail'), $this->lang->line('timer.config')));
							}
						}
				}
			}
        }
        
        echo json_encode($result);
    }
    
    public function do_delete_timer() {
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
}
