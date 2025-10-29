<?php
class Settings extends MY_Controller{
	
	public function __construct() {
        parent::__construct();
        $this->lang->load('settings');
		$this->load->model('membership');
    }
	
	public function index(){
		$settings = $this->membership->get_user_settings($this->get_uid());
		$data = $this->get_data();
		$data['settings'] = $settings;
        $this->load->view('config/settings/index', $data);
	}
	
	public function do_save(){
		$this->load->library('form_validation');
		if($this->is_admin()){
			$this->form_validation->set_rules('user_page_size',$this->lang->line('user.page.size'), 'required|numeric');
		}
		$this->form_validation->set_rules('group_page_size',$this->lang->line('group.page.size'), 'required|numeric');
		$this->form_validation->set_rules('player_page_size',$this->lang->line('player.page.size'), 'required|numeric');
		$this->form_validation->set_rules('media_page_size',$this->lang->line('media.page.size'), 'required|numeric');
		$this->form_validation->set_rules('template_page_size',$this->lang->line('template.page.size'), 'required|numeric');
		$this->form_validation->set_rules('playlist_page_size',$this->lang->line('playlist.page.size'), 'required|numeric');
		$this->form_validation->set_rules('schedule_page_size',$this->lang->line('schedule.page.size'), 'required|numeric');
		$this->form_validation->set_rules('dialog_media_page_size',$this->lang->line('dialog.media.page.size'), 'required|numeric');
		if ($this->form_validation->run() == FALSE) {
            //false
            $result = array('code'=>1, 'msg'=>validation_errors());
			echo json_encode($result);
        } else {
        	$uid = $this->get_uid();
			if($this->is_admin()){
				$array['user_page_size'] = $this->input->post('user_page_size');
			}
			$array['user_page_size'] = $this->input->post('user_page_size');
			$array['group_page_size'] = $this->input->post('group_page_size');
			$array['player_page_size'] = $this->input->post('player_page_size');
			$array['media_page_size'] = $this->input->post('media_page_size');
			$array['template_page_size'] = $this->input->post('template_page_size');
			
			$array['playlist_page_size'] = $this->input->post('playlist_page_size');
			$array['schedule_page_size'] = $this->input->post('schedule_page_size');
			$array['dialog_media_page_size'] = $this->input->post('dialog_media_page_size');
			
			$this->load->model('membership');
			$this->membership->update_user_settings($uid, $array);
			$result = array('code'=>0, 'msg'=>$this->lang->line('save.success'));
			echo json_encode($result);
        }
	}
}
