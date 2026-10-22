<?php
class Playbandwidth extends MY_Controller{
	
	public function __construct() {
        parent::__construct();
        $this->lang->load('playbandwidth');
    }
	
	/**
	 * 默认页面
	 * 
	 * @return 
	 */
	public function index(){
		$this->load->model('device');
		$this->load->helper('date');
		$this->addJs('playbandwidth.js');
		$this->addJs("jquery/jquery-ui-latest.js");
		$this->addCss("jquery/jquery.ui.all.css");
		$data = $this->get_data();
		$gids = 0;
		if($this->get_auth() < $this->config->item("auth_admin")){
			$gids = $this->device->get_group_ids($this->get_uid());
		}
		$data['players']=$this->device->get_all_player_list($this->get_cid(), $gids);
		$data['body_file'] = 'playbandwidth/index';
		$data['start_date']=gmdate('Y-m-').'01';
		$time_zone = $this->get_time_zone();
		$dst = $this->is_dst_on();
		$data['end_date']=now_to_local_date($time_zone, $dst);
		$this->load->view('include/main2', $data);
	}
	
	/**
	 * 查询记录信息，默认第一页数据
	 * 
	 * @param object $curpage [optional]
	 * @return 
	 */
	public function query($curpage = 1, $order_item='id', $order='desc'){
		$player_id = $this->input->get('player_id');
		$start_date= $this->input->get('start_date');
		$end_date  = $this->input->get('end_date');
		
		$this->load->model('device');
		$this->load->helper('serial');
		$limit = $this->config->item('page_default_size');
		$offset = ($curpage - 1) * $limit;
		$gids = 0;
		if($this->get_auth() < $this->config->item("auth_admin")){
			$gids = $this->device->get_group_ids($this->get_uid());
		}
		$data = $this->get_data();
		$result = $this->device->get_player_bandwidth($this->get_cid(), $gids, $player_id, $start_date, $end_date, $offset, $limit, $order_item, $order);
		$data['total'] = $result['total'];
		$data['data'] = $result['data'];
		$data['offset'] = $offset;
		$data['limit'] = $limit;
		$data['curpage'] = $curpage;
		$data['order_item'] = $order_item;
		$data['order'] = $order;
		$data['player_id']=$player_id;
		$data['start_date']=$start_date;
		$data['end_date']=$end_date;
		
		$this->load->view('playbandwidth/table_list', $data);
	}
	
	public function excel(){
		$player_id = $this->input->get('player_id');
		$start_date= $this->input->get('start_date');
		$end_date  = $this->input->get('end_date');
		$this->load->model('device');
		$this->load->helper('serial');		
		$this->load->helper('xls');
		$gids = 0;
		if($this->get_auth() < $this->config->item("auth_admin")){
			$gids = $this->device->get_group_ids($this->get_uid());
		}
		$result = $this->device->get_all_player_bandwidth($this->get_cid(), $gids, $player_id, $start_date, $end_date);
		$rows = array();
		foreach ($result as $row) {
			$rows[] = array($row->post_date, $row->group_name, $row->player_name, format_sn($row->sn), $row->media_name, $row->times, $row->duration);
		}
		xls_download("Playback.$start_date-$end_date.xls", 'Playback Report', array(
			array('title' => 'Post Date', 'width' => 15),
			array('title' => 'Group', 'width' => 15),
			array('title' => 'Player', 'width' => 15),
			array('title' => 'ID #', 'width' => 15),
			array('title' => 'Media', 'width' => 25),
			array('title' => 'Count', 'width' => 15),
			array('title' => 'Seconds', 'width' => 15),
		), $rows);
		
	}
}
