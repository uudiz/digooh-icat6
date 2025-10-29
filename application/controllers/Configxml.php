<?php

class Configxml extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->lang->load('configxml');
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
		$this->addJs("/assets/js/device_setup.js", false);
		$data = $this->get_data();
		if ($this->get_auth() <= 2) {
			$data['body_file'] = 'bootstrap/401';
		} else {
			$data['body_file'] = 'bootstrap/device_setup/index';
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


		$rest = $this->strategy->get_config_list($this->get_cid(), $offset, $limit, $order_item, $order, $name);
		$data['total'] = $rest['total'];
		$data['rows']  = $rest['data'];

		echo json_encode($data);
	}


	public function edit()
	{
		$this->addJs("/assets/js/form.js", false);

		$cid = $this->get_cid();
		$data = $this->get_data();
		$id = $this->input->get('id');
		$data['title'] = $this->lang->line('device.config');

		if ($id) {
			//$data['title'] = $this->lang->line('edit.criteria');
			$this->load->model('strategy');
			$config = $this->strategy->get_config_setting($id);
			if ($config) {
				$data['data'] = $config;
			} else {
				$this->show_msg($this->lang->line('warn.param'), 'warn');
				return;
			}
		}
		$this->load->model('membership');
		$company = $this->membership->get_company($cid);

		$data['zones'] = array(
			"GMT - 12" => -12, "GMT - 11" => -11, "GMT - 10" => -10, "GMT - 9" => -9, "GMT - 8" => -8, "GMT - 7" => -7, "GMT - 6" => -6,
			"GMT - 5" => -5, "GMT - 4:30" => -4.5, "GMT - 4" => -4, "GMT - 3:30" => -3.5, "GMT - 3" => -3, "GMT - 2" => -2, "GMT - 1" => -1, "GMT" => 0, "GMT + 1" => 1, "GMT + 2" => 2,
			"GMT + 3" => 3, "GMT + 3:30" => 3.5, "GMT + 4" => 4, "GMT + 4:30" => 4.5, "GMT + 5" => 5, "GMT + 5:30" => 5.5, "GMT + 6" => 6, "GMT + 7" => 7, "GMT + 8" => 8, "GMT + 9" => 9,
			"GMT + 9:30" => 9.5, "GMT + 10" => 10, "GMT + 11" => 11, "GMT + 12" => 12, "GMT + 13" => 13
		);
		$data['clockpos'] = array("OFF", "TOP LEFT", "TOP RIGHT", "BOTTOM LEFT", "BOTTOM RIGHT");
		$data['storagepri'] = array("-1" => "No change", "0" => "Auto", "3" => "Internal Disk");;
		$data['device_setup'] = $company->device_setup == "on" || $company->device_setup == "1" ? 1 : 0;
		$data['orientation_list'] = $this->lang->line('orientation.list');


		$data['body_file'] = 'bootstrap/device_setup/form';
		$this->load->view('bootstrap/layout/basiclayout', $data);
	}



	public function configration($curpage = 1, $order_item = 'id', $order = 'desc', $main = FALSE)
	{
		$this->addJs('configxml.js');
		$cid = $this->get_cid();
		$auth = $this->get_auth();
		$this->load->model('strategy');
		$data = $this->get_data();
		//  $limit = $this->config->item('page_default_size');
		//  $offset = ($curpage - 1) * $limit;
		$limit = -1;
		$offset = 0;
		$rest = $this->strategy->get_config_list($cid, $offset, $limit, $order_item = 'id', $order = 'desc');

		$data['total'] = $rest['total'];
		$data['data'] = $rest['data'];
		$data['cid'] = $cid;
		$data['auth'] = $auth;
		$data['curpage'] = $curpage;
		$data['limit'] = $limit;
		$data['order_item'] = $order_item;
		$data['order'] = $order;
		$data['body_file'] = 'config/configxml/conf';

		$this->load->view('include/main2', $data);
	}

	public function do_delete()
	{
		$result = array();
		$id = $this->input->post('id');
		$this->load->model('strategy');
		$del = $this->strategy->remove_config($id);
		if ($del) {
			$result['code'] = 0;
			$result['msg'] = 'delete.success';
		} else {
			$result['code'] = 1;
			$result['msg'] = 'delete.fail';
		}
		echo json_encode($result);
	}

	public function do_save()
	{
		$this->load->model('membership');
		$company = $this->membership->get_company($this->get_cid());

		$id = $this->input->post('id');
		$this->load->model('strategy');
		$this->load->library('form_validation');

		if ($company->device_setup == 'on' || $company->device_setup == '1') {
			$domain = $this->input->post('domain');
			$connectionMode = $this->input->post('connectionMode');
			$ip = $this->input->post('ip');
			$sn = $this->input->post('sn');

			$tcpport = $this->input->post('tcpport');
			$report = $this->input->post('playback_flag');
			$networkmode = $this->input->post("networkmode");
			$wifissid = $this->input->post('wifissid');
			$wifipwd = $this->input->post('wifipwd');

			$menulock = $this->input->post('menulock');
			$lockpwd = $this->input->post('lockkey');



			//如果是域名
			if ($connectionMode == 1) {
				$ip = '';
				//$this->form_validation->set_rules('domain', 'Domain', 'trim|required');

			} else {
				$domain = '';
				if ($ip != '') {
					$this->form_validation->set_rules('ip', 'Server IP', 'trim|required|valid_ip');
				}
			}

			if ($sn == '') {
				$this->form_validation->set_rules('timezone', 'timezone', 'trim|required');
			} else {
				$this->form_validation->set_rules('sn', 'Sn', 'trim|required');
			}

			if ($this->form_validation->run() == FALSE) {
				$result = array('code' => 1, 'msg' => validation_errors());
			} else {
				//匹配sn   000-000-0068
				$sn_flag = TRUE;
				//$sn = $this->input->post('sn');
				$sn_segments = explode('-', $sn);
				if ($sn) {
					if (count($sn_segments) != 3) {
						$sn_flag = FALSE;
					} else {
						if ($sn_segments[0] == '' or preg_match("/[^0-9]/", $sn_segments[0]) or strlen($sn_segments[0]) != 3) {
							$sn_flag = FALSE;
						}
						if ($sn_segments[1] == '' or preg_match("/[^0-9]/", $sn_segments[1]) or strlen($sn_segments[1]) != 3) {
							$sn_flag = FALSE;
						}
						if ($sn_segments[2] == '' or preg_match("/[^0-9]/", $sn_segments[2]) or strlen($sn_segments[2]) != 4) {
							$sn_flag = FALSE;
						}
					}
				} else {
					$sn_flag = TRUE;
				}

				if ($sn_flag == FALSE) {
					$result = array('code' => 1, 'msg' => 'The Player ID field must contain a valid ID.');
				} else {
					//如果是域名
					if ($connectionMode == 1 && !preg_match('/[\w]+[.][\w]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is', $domain) && $domain != '') {
						$result = array('code' => 1, 'msg' => 'The Domain field must contain a valid domain.');
					} else {
						$data = $this->get_data();
						$result = array('code' => 1, 'msg' => 'save error');
						$array = array(
							'dateformat' => $this->input->post('dateformat'),
							'timeformat' => $this->input->post('timeformat'),
							'timezone' => $this->input->post('timezone'),
							'synctime' => $this->input->post('synctime'),
							'clockpos' => $this->input->post('clockpos'),
							'storagepri' => $this->input->post('storagepri'),
							'company_id' => $this->get_cid(),
							'player_type' => 1,
							'sync_playback' => $this->input->post('sync_playback'),
							'dailyRestartTime' => $this->input->post('dailyRestartTime'),
							'name' => $this->input->post('name'),
							'descr' => $this->input->post('descr'),
							'videomode' => $this->input->post('videomode'),
							'dailyRestartFlag' => $this->input->post('drflag'),
							'sn' => $this->input->post('sn'),
							'ip' => $ip,
							'connectionMode' => $connectionMode,
							'domain' => $domain,
							'port' => $this->input->post('port'),
							'orientation' => $this->input->post('orientation'),
							'tcpport' => $tcpport,
							'playback_flag' => $report,
							'networkmode' => $networkmode,
							'menulock' => $menulock,
							'lockpwd' => $lockpwd,
							'brightness' => $this->input->post('brightness'),
							'ethernetTethering' => $this->input->post('ethernetTethering')
						);

						if ($networkmode == 2) {
							$array['wifissid'] = $wifissid;
							$array['wifipwd'] = $wifipwd;
						}
						if ($networkmode == 3) {
							$array['hotssid'] = $this->input->post('hotssid');
							$array['hotpwd'] = $this->input->post('hotpwd');
						}




						if ($id <= 0) {
							$this->strategy->add_config_setting($array);
							$result = array('code' => 0, 'msg' => 'save success');
						} else {
							$this->strategy->update_config_setting($id, $array);
							$result = array('code' => 0, 'msg' => 'save success');
						}
					}
				}
			}
		} else {
			$data = $this->get_data();
			$result = array('code' => 1, 'msg' => 'save error');
			$array = array(
				'dateformat' => $this->input->post('dateformat'),
				'timeformat' => $this->input->post('timeformat'),
				'timezone' => $this->input->post('timezone'),
				'synctime' => $this->input->post('synctime'),
				'clockpos' => $this->input->post('clockpos'),
				'storagepri' => $this->input->post('storagepri'),
				'company_id' => $this->get_cid(),
				'player_type' => $this->input->post('player_type'),
				'sync_playback' => $this->input->post('sync_playback'),
				'dailyRestartFlag' => $this->input->post('drflag'),
				'dailyRestartTime' => $this->input->post('dailyRestartTime'),
				'videomode' => $this->input->post('videomode'),
				'orientation' => $this->input->post('orientation'),
				'name' => $this->input->post('name'),
				'descr' => $this->input->post('descr')
			);
			if ($id <= 0) {
				$this->strategy->add_config_setting($array);
				$result = array('code' => 0, 'msg' => 'save success');
			} else {
				$this->strategy->update_config_setting($id, $array);
				$result = array('code' => 0, 'msg' => 'save success');
			}
		}
		echo json_encode($result);
	}
	/**
	 * 更新config.xml文件
	 * 
	 * @return 
	 */
	public function do_upgrade_config()
	{

		$ids = $this->input->post('ids');
		$id = $this->input->post('id');

		$dailyRestartTime = 'N/A';
		$this->load->model('strategy');
		$config = $this->strategy->get_config_setting($id);
		if ($config && $config->dailyRestartFlag) {
			$dailyRestartTime = $config->dailyRestartTime;
		}


		$code = 0;
		$msg = '';
		if ($ids === FALSE || empty($ids)) {
			$code = 1;
			$msg = 'update failed';
		} else {
			$this->load->model('device');
			if ($this->device->update_config_player($id, $ids, $dailyRestartTime)) {
				$msg = 'Player will reflect update within next 5 minutes.';
			} else {
				$code = 1;
				$msg = 'update failed';
			}
		}
		echo json_encode(array('code' => $code, 'msg' => $msg));
	}
	function detail()
	{
		$data['config_id'] = $this->input->get('id');
		$pid = $this->get_parent_company_id();

		$cris = $this->get_criteria($this->get_cid(), $pid);
		$data['criteria'] = $cris['criteria'];
		$this->load->view('bootstrap/device_setup/detail', $data);
	}
}
