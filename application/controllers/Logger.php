<?php

class Logger extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->lang->load('logger');
	}

	public function index()
	{
		$this->addJs("/assets/js/criteria.js", false);
		$data = $this->get_data();
		if ($this->get_auth() <= 2) {
			$data['body_file'] = 'bootstrap/401';
		} else {
			$data['body_file'] = 'bootstrap/system_log/index';
		}
		$this->load->model('membership');
		$data['companies'] = $this->membership->get_all_company_list();

		$this->load->view('bootstrap/layout/basiclayout', $data);
	}

	public function getTableData()
	{

		$this->load->model('mylog');
		$data = $this->get_data();
		$offset = $this->input->post('offset');
		$limit = $this->input->post('limit');
		$order_item = $this->input->post('sort');
		$order = $this->input->post('order');
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$cid = $this->input->post('company_id');

		$filter_array = array();
		if (!empty($start_date) && !empty($end_date)) {
			$filter_array['start_date'] = $start_date;
			$filter_array['end_date'] = $end_date;
		}

		if ($cid > -1) {
			$filter_array['company_id'] = $cid;
		}

		$rest = $this->mylog->get_log_list($filter_array, $offset, $limit, $order_item, $order);

		$data['total'] = $rest['total'];
		$data['rows']  = $rest['data'];

		echo json_encode($data);
	}


	public function refresh($curpage = 1, $order_item = 'id', $order = 'desc', $main = FALSE)
	{
		$limit = $this->config->item('page_log_size');
		$offset = ($curpage - 1) * $limit;
		$this->addJs("logger.js");
		$data = $this->get_data();

		$this->load->model('mylog');
		$this->load->model('membership');
		$filter_array = array();
		$start_date = $this->input->get('start_date');
		$end_date = $this->input->get('end_date');
		$cid = $this->input->get('cid');
		if (!empty($start_date) && !empty($end_date)) {
			$filter_array['start_date'] = $start_date;
			$filter_array['end_date'] = $end_date;
		}

		if ($cid) {
			$filter_array['company_id'] = $cid;
		}

		$log = $this->mylog->get_log_list($filter_array, $offset, $limit, $order_item, $order);
		$data['total'] = $log['total'];
		$data['data'] = $log['data'];
		$data['companys'] = $this->membership->get_all_company_list();
		$data['curpage'] = $curpage;
		$data['limit'] = $limit;
		$data['order_item'] = $order_item;
		$data['order'] = $order;
		$data['cid'] = $cid;
		$data['start_date'] = $start_date;
		$data['end_date'] = $end_date;
		if ($main) {
			$data['body_file'] = "log/logger";
			$this->load->view('include/main2', $data);
		} else {
			$this->load->view("log/table_list", $data);
		}
	}
	//生成xls
	public function excel()
	{
		$filter_array = array();
		$start_date = $this->input->get('start_date');
		$end_date = $this->input->get('end_date');
		$cid = $this->input->get('cid');
		if (!empty($start_date) && !empty($end_date)) {
			$filter_array['start_date'] = $start_date;
			$filter_array['end_date'] = $end_date;
		} else {
			$start_date = gmdate('Y-m-') . '01';
			$end_date = date('Y-m-d');
			$filter_array['start_date'] = $start_date;
			$filter_array['end_date'] = $end_date;
		}

		if ($cid) {
			$filter_array['company_id'] = $cid;
		}
		$this->load->model('mylog');
		$this->load->model('membership');
		$gids = 0;

		$result = $this->mylog->get_all_log($filter_array);
		$this->load->helper('xls');
		$rows = array();
		foreach ($result as $row) {
			$rows[] = array($row->add_time, $row->user_name, $row->company_name, $row->ip, $row->detail);
		}
		xls_download("Log.$start_date-$end_date.xls", 'log', array(
			array('title' => 'Log Time', 'width' => 25),
			array('title' => 'User Account', 'width' => 20),
			array('title' => 'Company', 'width' => 15),
			array('title' => 'IP', 'width' => 25),
			array('title' => 'Action', 'width' => 15),
		), $rows);
	}
}
