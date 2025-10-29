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
		/** Include PHPExcel */
		//require_once 'PHPExcel.php';
		$this->load->library('PHPExcel');

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		//填入表头
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', 'Log Time')
			->setCellValue('B1', 'User Account')
			->setCellValue('C1', 'Company')
			->setCellValue('D1', 'IP')
			->setCellValue('E1', 'Action');

		//设置字体样式
		$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

		//设置单元格宽度
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);

		//设置表头行高
		$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);

		// Add data
		$i = 2;
		foreach ($result as $row) {
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $row->add_time)
				->setCellValue('B' . $i, $row->user_name)
				->setCellValue('C' . $i, $row->company_name)
				->setCellValue('D' . $i, $row->ip)
				->setCellValue('E' . $i, $row->detail);
			$i++;
		}
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('log');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);

		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename=Log.$start_date-$end_date.xls");
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
}
