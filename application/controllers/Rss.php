<?php
class Rss extends MY_Controller{
	
	public function __construct() {
        parent::__construct();
        $this->lang->load('rss');
		$this->load->helper('media');
    }
	
	/**
	 * RSS 列表页面
	 * 
	 * @param object $curpage [optional]
	 * @return 
	 */
	public function index($curpage = 1){
		$this->refresh($curpage,TRUE);
	}
	
	/**
	 * 页面刷新
	 * 
	 * @param object $curpage [optional]
	 * @return 
	 */
	public function refresh($curpage = 1, $main=FALSE){
		$this->addJs("rss.js");
		
		$limit = $this->config->item('page_default_size');
        $offset = ($curpage - 1) * $limit;
		$this->load->model('material');
		$add_user_id=FALSE;

        $rss_list = $this->material->get_rss_list($this->get_cid(), $offset, $limit, $add_user_id);
		$data = $this->get_data();
		$data['body_file'] = 'media/rss/rss';
		$data['total'] = $rss_list['total'];
		$data['data']  = $rss_list['data'];
		$data['limit'] = $limit;
		$data['curpage'] = $curpage;
		if($main){
			$this->load->view('include/main2', $data);
		}else{
        	$this->load->view('media/rss/rss', $data);
		}
	}
	
	/**
	 * 添加RSS页面
	 * 
	 * @return 
	 */
	public function addRSS(){
		
		$data = $this->get_data();
		
		$this->load->view('media/rss/add_rss', $data);
	}
	
	public function editRSS(){
		$id = $this->input->get('id');
		$this->load->model('material');
		
		$rss = $this->material->get_rss($id);
		if($rss){
			$data = $this->get_data();
			$data['rss'] = $rss;
			
			$this->load->view('media/rss/edit_rss',$data);
		}else{
			$this->show_msg($this->lang->line('param.error'), 'error');
		}
	}
	
	/**
	 * 添加Text页面
	 * 
	 * @return 
	 */
	public function addText(){
		
		$data = $this->get_data();
		
		$this->load->view('media/rss/add_text', $data);
	}
	
	public function editText(){
		$id = $this->input->get('id');
		$this->load->model('material');
		
		$rss = $this->material->get_rss($id);
		if($rss){
			$data = $this->get_data();
			$data['rss'] = $rss;
			
			$this->load->view('media/rss/edit_text',$data);
		}else{
			$this->show_msg($this->lang->line('param.error'), 'error');
		}
	}
	
	/**
	 * 保存RSS
	 * 
	 * @return 
	 */
	public function do_save(){
		$result = array();
		$id = $this->input->post('id');
		$type = $this->input->post('type');
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name',$this->lang->line('name'), 'trim|required');
		if(!$type) {
			$this->form_validation->set_rules('url',$this->lang->line('url'), 'trim|required');
			$this->form_validation->set_rules('interval',$this->lang->line('update.interval'), 'trim|required|numeric');	
		}
		
		if($this->form_validation->run() == FALSE){
			$result = array('code'=>1, 'msg'=>validation_errors());
		}else{
			$url = $this->input->post('url');
			if(!$type) {
				if (!preg_match('/http:\/\/[\w]+[.][\w]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is',$url)){ //判断输入的url
	      			$result = array('code'=>1, 'msg'=>$this->lang->line('warn.rss.url'));
				}else {
					$this->load->model('material');
					$flag = $this->material->get_rss_by_name($id, $this->get_cid(), $this->input->post('name'));
					if($flag) {
						$result = array('code'=>1, 'msg'=>sprintf($this->lang->line('rss.name.exsit'), $this->input->post('name')));
					}else {
						$data = array('name'=>$this->input->post('name'),
									  'descr'=>$this->input->post('descr'),
									  'interval'=>$this->input->post('interval'),
									  'url'=>$url);
						if($id){
							$this->material->update_rss($data, $id);
						}else{
							$id = $this->material->add_rss($data, $this->get_cid(), $this->get_uid());
						}
						if($id !== FALSE){
							$data['id'] = $id;
							$data['add_time'] = date('Y-m-d H:i:s');
							$result = array('code'=>0, 'msg'=>$this->lang->line('save.success'));
							$result = array_merge($data, $result);
						}else{
							$result = array('code'=>1, 'msg'=>sprintf($this->lang->line('save.fail'),$this->lang->line('rss')));
						}
					}
				}	
			}else {
				$this->load->model('material');
				$flag = $this->material->get_rss_by_name($id, $this->get_cid(), $this->input->post('name'));
				if($flag) {
					$result = array('code'=>1, 'msg'=>sprintf($this->lang->line('text.name.exsit'), $this->input->post('name')));
				}else {
					$data = array('name'=>$this->input->post('name'),
							  'descr'=>$this->input->post('descr'),
							  'url'=>$url,
							  'type'=>1);
					if($id){
						$this->material->update_rss($data, $id);
					}else{
						$id = $this->material->add_rss($data, $this->get_cid(), $this->get_uid());
					}
					if($id !== FALSE){
						$data['id'] = $id;
						$data['add_time'] = date('Y-m-d H:i:s');
						$result = array('code'=>0, 'msg'=>$this->lang->line('save.success'));
						$result = array_merge($data, $result);
					}else{
						$result = array('code'=>1, 'msg'=>sprintf($this->lang->line('save.fail'),$this->lang->line('rss')));
					}
				}
			}
		}
		
		echo json_encode($result);
	}
	
	/**
	 * 执行页面删除
	 * 
	 * @return 
	 */
	public function do_delete(){
		$id = $this->input->post('id');
		$code = 0;
		$msg = '';
		if($id > 0){
			$this->load->model('material');
			if($this->material->delete_rss($id)){
				$code = 0;
				$msg  = $this->lang->line('delete.success');
			}
		}else{
			$msg = $this->lang->line('warn.param');
		}
		
		echo json_encode(array('code'=>$code, 'msg'=>$msg));
	}
/**
	 * 获取Rss中内容
	 */	
	public function view() {
		$id = $this->input->get('id');
		$type = $this->input->get('type');
		$this->load->model('material');

		if($type) {
			$rss = $this->material->get_rss($id);
		}else {
			$rss = $this->material->get_rss_content($id);
		}
		$data['rss'] = $rss;
		$data['type'] = $type;
        $this->load->view('media/rss/view',$data);     
	}
}
