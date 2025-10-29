<?php
    class Group extends MY_Controller{
    	
    	public function __construct(){
			parent::__construct();
			$this->lang->load('group');
		}
		/**
		 * 默认的首页
		 * 
		 * @param object $curpage [optional]
		 * @param object $order_item [optional]
		 * @param object $order [optional]
		 * @return 
		 */
		public function index($curpage = 1,$order_item='add_time', $order = 'desc'){
			$this->refresh($curpage, $order_item, $order, TRUE);
		}
		
		/**
		 * 刷新页面数据信息
		 * 
		 * @param object $curpage [optional]
		 * @return 
		 */
		public function refresh($curpage = 1,$order_item='add_time', $order = 'desc', $main = FALSE){
			$this->addJs("group.js");
			$this->addCss("group.css");
			
			$this->load->model('device');
			$data = $this->get_data();
			$limit = $this->config->item('page_default_size');
			$offset = ($curpage - 1) * $limit;
			$gids = 0;
			if($this->get_auth() < $this->config->item("auth_admin")){
				$gids = $this->device->get_group_ids($this->get_uid());
			}
			
			$rest = $this->device->get_group_list($this->get_cid(), $gids, $offset, $limit, $order_item, $order, 10);
			$data['total'] = $rest['total'];
			$data['data']  = $rest['data'];
			$data['cid']   = $this->get_cid();
			$data['curpage']=$curpage;
			$data['limit']=$limit;
			$data['order_item']=$order_item;
			$data['order']=$order;
			
			if($main){
				
				$data['body_file'] = 'org/group/group';
				$this->load->view('include/main2', $data);
			}else{					
				$this->load->view('org/group/group', $data);
			}
		}
		
		/**
		 * 添加分组页面
		 * @return 
		 */
		public function add(){
			$cid = $this->get_cid();
			$data = $this->get_data();
			$this->load->model('strategy');
			$views = $this->strategy->get_all_view_list($cid);
			$data['views'] = $views;
			$downloads = $this->strategy->get_all_download_list($cid);
			$data['downloads'] = $downloads;
			$timers = $this->strategy->get_all_timer_list($cid);
			$data['timers'] = $timers;
			
			$this->load->view('org/group/add_group', $data);
		}
		
		public function edit(){
			$id = $_GET['id'];
			$this->load->model('device');
			$this->load->model('strategy');
			$cid = $this->get_cid();
			$data = $this->get_data();
			
			$views = $this->strategy->get_all_view_list($cid);
			$data['views'] = $views;
			$downloads = $this->strategy->get_all_download_list($cid);
			$data['downloads'] = $downloads;
			$timers = $this->strategy->get_all_timer_list($cid);
			$data['timers'] = $timers;
			
			$group = $this->device->get_group($id);
			if($group){
				$data['group'] = $group;
				$this->load->view('org/group/edit_group', $data);
			}else{
				$this->show_msg($this->lang->line('warn.param'), 'warn');
			}
			
		}
		
		/**
		 * 创建一个分组
		 * 
		 * @return 
		 */
		public function do_save(){
			$result = array();
			
			$cid = $this->get_cid();
			$id = $this->input->post('id');
			if($cid <=0 ){
				$result = array('code'=>1, 'msg'=>$this->lang->line('warn.system.user'));
			}else{
				$this->load->library('form_validation');
				$this->form_validation->set_rules('name',$this->lang->line('group'), 'trim|required');
				
				if($this->form_validation->run() == FALSE){
					$result = array('code'=>1, 'msg'=>validation_errors());
				}else{
					$this->load->model('device');
					$data = array(
									'name' => $this->input->post('name'),
									'descr'=> $this->input->post('descr'),
									'download_strategy_id' => $this->input->post('download_strategy_id'),
									'view_config_id' => $this->input->post('view_config_id'),
									'timer_config_id'=> $this->input->post('timer_config_id'),
									'company_id' => $cid,
									'add_time' => date('Y-m-d H:i:s'),
									'type' => $this->input->post('type')
									);
					$flag = $this->device->get_group_byname($id, $cid, $this->input->post('name'));   //判断同一个公司下，是否有重名的分组
					if($id >= 0 && $flag) {
						$result = array('code'=>1, 'msg'=>sprintf($this->lang->line('group.name.exsit'), $this->input->post('name')));
					}else {
						if($id > 0){
							$this->device->update_group($data, $id);
						}else{
							$id = $this->device->add_group($data, $this->get_uid());
						}
					if($id !== FALSE){
						$data['id'] = $id;
						$data['add_time'] = date('Y-m-d H:i:s');
						$result = array('code'=>0, 'msg'=>$this->lang->line('save.success'));
						$result = array_merge($data, $result);
					}else{
						$result = array('code'=>1, 'msg'=>sprintf($this->lang->line('save.fail'),$this->lang->line('group')));
					}
				
				}
			}
			}
			echo json_encode($result);
		}
		
		/**
		 * 执行删除某个分组
		 * 
		 * @return 
		 */
		public function do_delete(){
			$result = array();
			$gid = $this->input->post("id");
			$this->load->model('device');
			if($this->device->delete_group($gid)){
				$result['code'] = 0;
				$result['msg']  = $this->lang->line('delete.success');
			}else{
				$result['code'] = 1;
				$result['msg']  = $this->lang->line('delete.fail');
			}
			
			echo json_encode($result);
		}
    }
