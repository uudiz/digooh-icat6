<?php
class News extends MY_Controller{
	
	public function __construct() {
		parent::__construct();
		$this->lang->load('common');
		$this->lang->load('news');
	}
	
	public function index(){
		$this->load->model('xmlnews');
		
		$news = $this->xmlnews->get_news_settings();
		$data = $this->get_data();
		$data['data'] = $news;
		$data['body_file'] = 'newssettings';
		$this->load->view('include/main2', $data);
	}
	
	public function do_save(){

		$data = array();
		$data['check_interval'] = $this->input->post('interval');
		$code = 0;
		$msg = '';
		
		$this->load->model('xmlnews');
		if($this->xmlnews->update_news_settings($data)){
			$msg = $this->lang->line('save.success');
		}else{
			$code = 1;
			$msg = $this->lang->line('warn.pass.error.old');
		}
		
		echo json_encode(array('code'=>$code, 'msg'=>$msg));
	}
	
	/*
	public function index($order_item = 'id', $order = 'desc'){
		$this->refresh($order_item, $order, TRUE);
	}
	
	public function refresh($order_item = 'id', $order = 'desc', $main = FALSE) {
		$this->load->model('xmlnews');
	
		$news = $this->xmlnews->get_news_item_list();

		$data = $this->get_data();
		$data['data'] = $news;
		$data['order_item'] = $order_item;
		$data['order'] = $order;
		if($main){
			$data['body_file'] = 'media/news/index';
			$this->load->view('include/main2', $data);
		}else{
			$this->load->view('media/news/index', $data);
		}
	}
	*/
	
}

