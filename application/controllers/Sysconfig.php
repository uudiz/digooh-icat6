<?php
class Sysconfig extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->lang->load('sysconfig');
	}



	public function edit()
	{
		$cid = $this->get_cid();

		$this->addJs("system.js");
		$data = $this->get_data();
		$this->load->model('membership');
		$this->load->model('material');
		$this->lang->load('my_date');

		//$company = $this->membership->get_company($cid);
		$company = $this->membership->get_company_with_nofifies($cid);

		$used = $this->material->get_used_storage($cid);
		$data['company'] = $company;

		$users = $this->membership->get_all_user_list($cid);
		$data['users'] = $users;


		//$data['storage_used']=sprintf($this->lang->line('storage.used'), $this->showDiskSize($used), $this->showDiskSize($company->total_disk));
		$data['storage_used'] = sprintf($this->lang->line('storage.used'), $this->showDiskSize($used), $this->showDiskSize($company->total_disk)) . ",  Interval time=" . $company->com_interval . ' min';
		$data['id'] = $cid;
		$data['body_file'] = 'system/edit';
		$this->load->view('include/main2', $data);
	}
	public function do_save()
	{

		$this->load->library('form_validation');
		//		$this->form_validation->set_rules('dst_start',$this->lang->line('dst_start'), 'regex_match[/\d{4}-\d{2}-\d{2}/]');
		//		$this->form_validation->set_rules('dst_stop', $this->lang->line('dst_stop'), 'regex_match[/\d{4}-\d{2}-\d{2}/]');
		//		$this->form_validation->set_rules('city_code', $this->lang->line('city.code'), 'trim|numeric');
		$result = array();
		//	$email = trim($this->input->post('email'));
		//	$email2 = trim($this->input->post('email2'));
		$offline_email_flag = $this->input->post('offline_email_flag');
		$playback_email_flag = $this->input->post('playback_email_flag');


		$email_interval = $this->input->post('offline_email_interval');

		$offline_email_flag2 = $this->input->post('offline_email_flag2');
		$playback_email_flag2 = $this->input->post('playback_email_flag2');



		$email_interval2 = $this->input->post('offline_email_interval2');

		$event_email_flag = $this->input->post('event_email_flag');
		$fitORfill = $this->input->post('fit');
		$img_fitORfill = $this->input->post('imgfit');
		$users1 = $this->input->post('users_grp_1');
		$users2 = $this->input->post('users_grp_2');

		/*
		if(isset($email) && !empty($email)) {
			$this->form_validation->set_rules('email',$this->lang->line('email'), 'trim|required|valid_email');
		}
		if(isset($email2) && !empty($email2)) {
			$this->form_validation->set_rules('email2',$this->lang->line('email'), 'trim|required|valid_email');
		}
		
		if($this->form_validation->run() == FALSE){
			//false
			$result = array('code'=>1, 'msg'=>validation_errors());
		}else{
			if(($offline_email_flag || $playback_email_flag || $event_email_flag) && empty($email)){
				$result = array('code'=>1, 'msg'=>$this->lang->line('warn.email.empty'));
			}
			if($offline_email_flag&&$email_interval<30){
				$result = array('code'=>1, 'msg'=>"Lowest value is 30 minutes.");
			}
		}
		*/
		if ($offline_email_flag) {
			if (!$email_interval || $email_interval < 5) {
				$result = array('code' => 1, 'msg' => "Lowest value is 5 minutes.");
			}
			if (!$users1 || empty($users1)) {
				$result = array('code' => 1, 'msg' => "Please select at least one user.");
			}
		}
		if ($offline_email_flag2) {
			if (!$email_interval || $email_interval2 < 5) {
				$result = array('code' => 1, 'msg' => "Lowest value is 5 minutes.");
			}
			if (!$users2 || empty($users2)) {
				$result = array('code' => 1, 'msg' => "Please select at least one user.");
			}
		}

		if (empty($result)) {
			$this->load->model('membership');

			/*$dst = $this->input->post('dst');
			$dst_start ='';
			$dst_end='';
			*/
			$weather_format = $this->input->post('weather_format');
			if (!in_array($weather_format, array('c', 'f'))) {
				$weather_format = 'f';
			}

			$data = array(
				'weather_format' => $weather_format,
				//'email'=>$email,
				//'email2'=>$email2,
				'offline_email_flag' => $offline_email_flag,
				//'playback_email_flag'=>$playback_email_flag,
				'offline_email_flag2' => $offline_email_flag2,
				//'event_email_flag'=>$event_email_flag,				
				//'fitORfill'=>$fitORfill
				'offline_email_inteval' => $email_interval,
				'offline_email_inteval2' => $email_interval2,
				'color_setting' => $this->input->post('colorsetting')
			);

			/*
			$data = array(
					'time_zone' => $this->input->post('time_zone'),
					'dst' => $dst,
					'dst_start' => $dst_start,
					'dst_end' => $dst_end,
					'city_code' => $this->input->post('city_code'),
					'weather_format'=>$weather_format,
					'email'=>$email,
					'email2'=>$email2,
					'offline_email_flag'=>$offline_email_flag,
					'playback_email_flag'=>$playback_email_flag,
					'event_email_flag'=>$event_email_flag,				
					'fitORfill'=>$fitORfill
				);
			*/

			$id = $this->input->post('id');
			if ($id > 0) {
				$id = $this->membership->update_company($data, $id);
			}


			if ($id !== FALSE) {

				$cid = $this->get_cid();
				if ($offline_email_flag) {
					$this->membership->sync_notify_users($cid, $users1, 0);
				}
				if ($offline_email_flag2) {
					$this->membership->sync_notify_users($cid, $users2, 1);
				}
			}
			if ($id !== FALSE) {
				$result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
				$result['id'] = $id;
				$result = array_merge($result, $data);
			} else {
				$result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('company')));
			}
		}


		echo json_encode($result);
	}
	public function password()
	{
		$this->addJs("account.js");
		$data = $this->get_data();
		$data['body_file'] = 'system/account';
		$this->load->view('include/main2', $data);
	}
	public function do_save_password()
	{
		$old = $this->input->post('old');
		$new = $this->input->post('new_pass');
		$confirm = $this->input->post('confirm');
		$code = 0;
		$msg = '';
		if (empty($old)) {
			$code = 1;
			$msg = $this->lang->line('warn.pass.empty.old');
		} elseif (empty($new) || empty($confirm)) {
			$code = 1;
			$msg = $this->lang->line('warn.pass.empty.new');
		} elseif ($new != $confirm) {
			$code = 1;
			$msg = $this->lang->line('warn.pass.new.different');
		} else {
			$this->load->model('membership');
			if ($this->membership->is_passd($this->get_uid(), $old)) {
				$this->membership->change_passd($this->get_uid(), $new);
				$msg = $this->lang->line('save.success');
			} else {
				$code = 1;
				$msg = $this->lang->line('warn.pass.error.old');
			}
		}
		echo json_encode(array('code' => $code, 'msg' => $msg));
	}
}
