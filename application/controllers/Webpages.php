<?php
class Webpages extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->lang->load('webpage');
		$this->load->model('webpage');
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
		$this->addJs("/assets/js/webpage.js", false);
		$data = $this->get_data();
		if ($this->get_auth() <= 2) {
			$data['body_file'] = 'bootstrap/401';
		} else {
			$data['body_file'] = 'bootstrap/webpages/index';
		}

		$this->load->view('bootstrap/layout/basiclayout', $data);
	}

	public function getTableData()
	{
		$name = $this->input->post('search');


		$data = $this->get_data();
		$offset = $this->input->post('offset');
		$limit = $this->input->post('limit');
		$order_item = $this->input->post('sort');
		$order = $this->input->post('order');

		$filter_array = array();
		if ($name) {
			$filter_array['name'] = $name;
		}

		$rest = $this->webpage->get_list($this->get_cid(), $offset, $limit, $order_item, $order, $filter_array);
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
		$data['title'] = $this->lang->line('create.webpage');

		if ($id) {
			$data['title'] = $this->lang->line('edit.webpage');
			$region = $this->webpage->get_item($id, true);
			if ($region) {
				$data['data'] = $region;
			} else {
				$this->show_msg($this->lang->line('warn.param'), 'warn');
				return;
			}
		}

		$data['body_file'] = 'bootstrap/webpages/form';
		$this->load->view('bootstrap/layout/basiclayout', $data);
	}

	/**
	 * Save
	 *
	 * @return
	 */
	public function do_save()
	{
		$result = array();

		$cid = $this->get_cid();
		$id = $this->input->post('id');
		if ($cid <= 0) {
			$result = array('code' => 1, 'msg' => $this->lang->line('warn.system.user'));
		} else {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', $this->lang->line('webpage'), 'trim|required');

			if ($this->form_validation->run() == false) {
				$result = array('code' => 1, 'msg' => validation_errors());
			} else {
				foreach ($this->input->post() as $key => $val) {
					if ($key != 'bgImg') {
						$data[$key] = $val;
					}
				}



				$body = '<body class="mce-content-body" 
								style="background-color:' . $data['backgroundColor'] . ";";
				$bgImg = $this->input->post('bgImg');
				$bgImg = str_replace('"', "'", $bgImg);
				//$bgImg =  "http://" . $_SERVER['HTTP_HOST'] . '/' . $bgImg;
				if (!$bgImg) {
					$body .= '">';
				} else {
					$body .= "background-image:$bgImg; background-repeat: no-repeat;" . '">';
				}

				$body .= isset($data['contents']) ? $data['contents'] : "<p></p>";
				$body .= '</body>';

				//file_put_contents($destHtml, $body);
				$data['html'] = $body;

				//$data['path'] = substr($destHtml, 1);

				//$data['path'] = $destHtml;


				$data['company_id'] = $this->get_cid();
				$flag = $this->webpage->get_item_byName($id, $cid, $this->input->post('name'));
				if ($id >= 0 && $flag) {
					$result = array('code' => 1, 'msg' => sprintf($this->lang->line('webpage.name.exsit'), $this->input->post('name')));
				} else {
					if ($id > 0) {
						$this->webpage->update_item($data, $id);
						$this->load->model('program');
						$this->program->refresh_published_playlist($id);
					} else {
						$id = $this->webpage->add_item($data);
					}

					if ($id !== false) {

						$data['id'] = $id;
						$data['add_time'] = date('Y-m-d H:i:s');
						$result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
					} else {
						$result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('group')));
					}
				}
			}
		}
		echo json_encode($result);
	}

	public function do_delete()
	{
		$result = array();
		$gid = $this->input->post("id");
		$this->load->model('device');
		$this->load->model('program');

		if ($this->webpage->delete_item($gid)) {
			$result['code'] = 0;
			$result['msg']  = $this->lang->line('delete.success');
		} else {
			$result['code'] = 1;
			$result['msg']  = $this->lang->line('delete.fail');
		}

		echo json_encode($result);
	}

	public function create_webpage()
	{
		$html = $this->input->post('html');
	}
}
