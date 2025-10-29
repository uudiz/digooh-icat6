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
		$this->load->library('PHPExcel');
		$gids = 0;
		if($this->get_auth() < $this->config->item("auth_admin")){
			$gids = $this->device->get_group_ids($this->get_uid());
		}
		$result = $this->device->get_all_player_bandwidth($this->get_cid(), $gids, $player_id, $start_date, $end_date);
		$objPHPExcel = new PHPExcel();
		
		//填入表头
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'Post Date')
					->setCellValue('B1', 'Group')
					->setCellValue('C1', 'Player')
					->setCellValue('D1', 'ID #')
					->setCellValue('E1', 'Media')
					->setCellValue('F1', 'Count')
					->setCellValue('G1', 'Seconds');

		//设置字体样式
        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFont()->setSize(12);
        $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
		
		//设置单元格宽度
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		
		//设置表头行高
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
		
		// Add data
		$i = 2;
		foreach ($result as $row) {
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $row->post_date)
										  ->setCellValue('B' . $i, $row->group_name)
										  ->setCellValue('C' . $i, $row->player_name)
										  ->setCellValue('D' . $i, format_sn($row->sn))
										  ->setCellValue('E' . $i, $row->media_name)
										  ->setCellValue('F' . $i, $row->times)
										  ->setCellValue('G' . $i, $row->duration);
		$i++;
		}

		$objPHPExcel->getActiveSheet()->setTitle('Playback Report');
		$objPHPExcel->setActiveSheetIndex(0);
		
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename=Playback.$start_date-$end_date.xls");
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
		
	}
}
