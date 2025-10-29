<?php
class Authorize extends MY_Controller{
	
	public function __construct() {
        parent::__construct();
    }
	
	public function index($curpage = 1, $order_item = 'id', $order = 'desc'){
		$this->refresh($curpage, $order_item, $order, TRUE);
	}
	
	public function refresh($curpage = 1, $order_item = 'id', $order = 'desc', $main=FALSE){
		$this->addJs("authorize.js");
		$this->load->model('membership');
		$limit = $this->config->item('page_default_size');
		$offset = ($curpage - 1) * $limit;
		$name = $this->input->get('name');
		$data = $this->get_data();
		
		$authorize = $this->membership->get_authorize_list($offset, $limit, $order_item, $order, $name);
		$data['body_file'] = 'org/authorize/authorize';
		$data['total'] = $authorize['total'];
		$data['data']  = $authorize['data'];
		$data['limit'] = $limit;
		$data['curpage']=$curpage;
		$data['order_item'] = $order_item;
		$data['order']=$order;
		$data['body_view'] = 'org/authorize/authorize_list';
		
		if($main){
			$data['body_file'] = 'org/authorize/authorize';
			$this->load->view("include/main2", $data);
		} else {
			$this->load->view('org/authorize/authorize_list', $data);
		}
	}
	
	public function add() {
		$this->addJs("authorize.js");
		$data = $this->get_data();
		$this->load->model('device');
		$data['body_file'] = 'org/authorize/add_authorize';
		$this->load->view('include/main2', $data);
		//$this->load->view("org/authorize/add_authorize", $data);
	}
	
	public function do_save() {
		$count = $this->input->post('count');
		$descr = $this->input->post('descr');
		$id = $this->input->post('id');
		$data = array();
		$this->load->model('membership');
		if($id > 0) {
			$id = $this->membership->update_authorize(array('descr'=>$descr), $id);
		}else{
			for($i=1; $i <= $count; $i++) {
				//注册码
				$code = sprintf('%04X-%04X-%04X-%04X-%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535));
				$data[] = $code;
			}
			$id = $this->membership->add_authorize($data, $descr);
		}
		if($id !== FALSE){
			$result = array('code'=>0, 'msg'=>$this->lang->line('save.success'));
		}else{
			$result = array('code'=>1, 'msg'=>$this->lang->line('save.error'));
		}
		echo json_encode($result);
	}
	
	public function do_delete() {
		$id = $this->input->post('id');
		$result = FALSE;
		if($id > 0){
			$this->load->model('membership');
			$result = $this->membership->delete_authorize($id);
		}
		$array = array();
		if($result){
			$array['code'] = 0;
			$array['msg'] = $this->lang->line('delete.success');
		}else{
			$array['code'] = 1;
			$array['msg']  = sprintf($this->lang->line('delete.fail'), $this->lang->line('user'));
		}
		
		echo json_encode($array);
	}
	
	public function edit() {
		$id = $_GET['id'];
		$data = $this->get_data();
		$this->load->model('membership');
		$authorize = $this->membership->get_authorize($id);
		$data['authorize'] = $authorize;
		$this->load->view("org/authorize/edit_authorize", $data);
	}
	
	public function do_authorize() {
		$code = $this->input->post('code');
		//获取本地公网IP
		$socket = socket_create(AF_INET, SOCK_STREAM, 6); 
		$ret = socket_connect($socket,'ns1.dnspod.net',6666); 
		$buf = socket_read($socket, 16); 
		socket_close($socket); 
		//echo $buf;
		echo $code;
	}
}
?>