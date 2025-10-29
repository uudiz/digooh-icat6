<?php
class Software extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->lang->load('software');
	}

	public function index()
	{
		$data = $this->get_data();
		if ($this->get_auth() <= 2) {
			$data['body_file'] = 'bootstrap/401';
		} else {
			$data['body_file'] = 'bootstrap/software/index';
		}
		$this->load->view('bootstrap/layout/basiclayout', $data);
	}

	public function getTableData()
	{
		$name = $this->input->post('search');

		$this->load->model('material');
		$data = $this->get_data();
		$offset = $this->input->post('offset');
		$limit = $this->input->post('limit');
		$order_item = $this->input->post('sort');
		$order = $this->input->post('order');

		$filter_array = array();
		if ($name) {
			$filter_array['name'] = $name;
		}

		$rest = $this->material->get_software_list($this->get_cid(), $offset, $limit, $order_item, $order); //model
		$data['total'] = $rest['total'];
		$data['rows']  = $rest['data'];
		if ($rest['total'] > 0) {
			$model_arr = $this->lang->line('HW.model');
			foreach ($data['rows'] as $item) {
				$item->mpeg_core_org = $item->mpeg_core;
				$item->mpeg_core = isset($model_arr[$item->mpeg_core]) ? $model_arr[$item->mpeg_core] : "N/A";
			}
		}

		echo json_encode($data);
	}


	public function edit()
	{
		$this->addJs("/assets/js/form.js", false);
		$this->load->model('device');

		$data = $this->get_data();
		$id = $this->input->get('id');
		$data['title'] = $this->lang->line('create.criteria');

		if ($id) {
			$data['title'] = $this->lang->line('edit.criteria');
			$region = $this->device->get_software($id);
			if ($region) {
				$data['data'] = $region;
			} else {
				$this->show_msg($this->lang->line('warn.param'), 'warn');
				return;
			}
		}

		$data['body_file'] = 'bootstrap/software/form';
		$this->load->view('bootstrap/layout/basiclayout', $data);
	}



	public function refresh($curpage = 1, $order_item = 'id', $order = 'desc', $main = FALSE)
	{

		$this->addJs('software.js');
		//$this->addJs("fileupload/swfupload.js");
		//$this->addJs("fileupload/fileprogress.js");
		$this->addCss('/static/fileuploader/fineuploader-gallery.css', FALSE);
		$this->addCss('/static/fileuploader/fineuploader-new.css', FALSE);
		$this->addCss('/static/fileuploader/fineuploader.css', FALSE);
		$this->addCss('/static/fileuploader/styles.css', FALSE);
		$this->addJs('/static/fileuploader/all.fine-uploader.min.js', FALSE);
		$this->addCss("media.css");

		$cid = $this->get_cid();
		$this->load->model('material');
		$data = $this->get_data();
		$limit = $this->config->item('page_default_size');
		$offset = ($curpage - 1) * $limit;
		$model_arr = $this->lang->line('HW.model');

		$rest = $this->material->get_software_list($cid, $offset, $limit, $order_item, $order); //model

		$data['total'] = $rest['total'];
		$data['data'] = $rest['data'];
		$data['body_file'] = 'media/software/list';
		$data['cid'] = $cid;
		$data['curpage'] = $curpage;
		$data['limit'] = $limit;
		$data['order_item'] = $order_item;
		$data['order'] = $order;

		if ($main) {
			$this->load->view('include/main2', $data);
		} else {
			$this->load->view($data['body_file'], $data);
		}
	}

	public function add()
	{
		$data = $this->get_data();
		$data['session_id'] = $this->get_session_id();
		$this->load->view('media/software/add', $data);
	}

	public function upload()
	{
		$preview = $config = $errors = [];
		$medium_id = 0;

		$cid = $this->get_cid();
		$uid = $this->get_uid();
		$targetDir = $this->config->item('system_media_path');
		if ($cid > 0) {
			$targetDir .= $cid;
		}

		if (!file_exists($targetDir)) {
			if (!mkdir($targetDir, 0744, true)) {
				$ret =  [
					'error' => 'Failed to create directory'
				];
				echo json_encode($ret);
				return;
			}
		}

		$input = 'input-uploader'; // the input name for the fileinput plugin
		if (empty($_FILES[$input])) {
			return [];
		}


		$tmpFilePath = $_FILES[$input]['tmp_name']; // the temp file path
		$fileName = $_FILES[$input]['name']; // the file name
		$fileSize = $_FILES[$input]['size']; // the file size

		$media = array();
		$raw_name = time() . '_' . $fileName;;
		$destFile = $targetDir . $raw_name;
		//Make sure we have a file path
		if ($tmpFilePath != "") {
			$this->load->helper('file');
			$this->load->model('material');
			if (move_uploaded_file($tmpFilePath, $destFile)) {
				$img = get_img_info($destFile);
				if ($img) {
					$version = $img['version'];
					$pos = strpos($version, '.');

					if ($pos !== false) {
						$version = substr($version, $pos + 1);
					}

					$meta_data = array(
						'name' => $fileName,
						'version' =>  $img['version'],
						'file_size' => $fileSize,
						'publish_time' => $img['publish_time'],
						'mpeg_core' => $img['mpeg_core'],
						'location' => $raw_name,
						'type' => 1,
					);
					$this->material->add_software($meta_data, $cid, $uid);
					$media['id'] = $medium_id;
				} else {
					$errors[] = 'Invalid firmware:' . $fileName;
				}
			} else {
				$errors[] = 'Error uploading' . $fileName;
			}
		} else {
			$errors[] = 'Filename: ' . $fileName;
		}

		if (!empty($errors)) {
			$out = ['errors' => $errors];
		} else {
			$out = [
				'initialPreview' => $preview,
				'initialPreviewConfig' => $config,
			];
		}
		echo json_encode($out);
	}

	public function do_upload()
	{

		set_time_limit(0); //unlimit upload time

		$cid = $this->get_cid();
		$uid = $this->get_uid();
		$path = $this->config->item('system_media_path');
		if ($cid > 0) {
			$path .= $cid;
		}

		$config['upload_path'] = $path;

		if (!file_exists($config['upload_path'])) {
			mkdir($config['upload_path'], 0777, TRUE);
		}

		$config['allowed_types'] = '*'; //'gif|jpg|jpeg|png|bmp';
		$config['max_size'] = $this->config->item('max_filesize'); //5MB
		$config['encrypt_name'] = TRUE;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		$result = array();
		if (!$this->upload->do_upload('file')) {
			$result = array('success' => false, 'code' => 1, 'msg' => $this->upload->display_errors());
		} else {
			$data = $this->upload->data();
			//print_r($data);
			$raw_name = $data['raw_name'] . $data['file_ext'];
			if ($cid > 0) {
				$raw_name = "$cid/$raw_name";
			}
			$this->load->helper('file');
			$img = get_img_info($data['file_path'] . $data['file_name']);

			if ($img) {
				$this->load->model('material');
				/*
				if (strstr($img['version'], '3.0.7.')) {
					$type = 0;
					$mpeg_core = 0;
				} else {
					if (strstr($img['mpeg_core'], '5166')) {
						$type = 1;
						$mpeg_core = 2; //5166固件类型的软件
					} else if (strstr($img['mpeg_core'], '5186')) {
						$type = 1;
						$mpeg_core = 3; //5188固件类型的软件,NP300
					} else if (strstr($img['mpeg_core'], '5161')) {
						$type = 1;
						$mpeg_core = 1; //5161固件类型的软件
					} else if (strstr($img['mpeg_core'], '3568')) {
						$type = 1;
						$mpeg_core = 4; //3568
					} else {
						$result = array('success' => false, 'code' => 1, 'msg' => $this->lang->line('warn.software.invalid'));
						echo json_encode($result);
						return;
					}
				}
				*/
				$mpeg_core = $img['mpeg_core'];
				$type = 1;
				$media = array('name' => $data['orig_name'], 'version' => $img['version'], 'publish_time' => $img['publish_time'], 'location' => $raw_name, 'file_size' => $data['file_size'], 'descr' => '', 'type' => $type, 'mpeg_core' => $mpeg_core);
				$this->material->add_software($media, $cid, $uid);
				$result = array('success' => true, 'code' => 0, 'msg' => $this->lang->line('upload.success'));
			} else {
				$result = array('success' => false, 'code' => 1, 'msg' => $this->lang->line('warn.software.invalid'));
			}
		}
		echo json_encode($result);
		//echo '<script type="text/javascript">parent.software.callbackUpload('.$result['code'].',"'.$result['msg'].'");</script>';
	}

	function do_delete()
	{
		$id = $this->input->post("id");
		$code = 1;
		$msg = '';
		if ($id) {
			$this->load->model('material');
			if ($this->material->delete_software($id)) {
				$code = 0;
				$msg = $this->lang->line('delete.success');
			} else {
				$msg  = $this->lang->line('error.software.delete');
			}
		}

		if (empty($msg)) {
			$msg = $this->lang->line('param.error');
		}

		echo json_encode(array('code' => $code, 'msg' => $msg));
	}

	function do_save()
	{
		$id = $this->input->post('id');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');
		if ($this->form_validation->run() == FALSE) {
			$result = array('code' => 1, 'msg' => validation_errors());
		} else {
			$data = array(
				'name' => $this->input->post('name'),
				'descr' => $this->input->post('descr'),
			);
			if ($id > 0) {
				$this->load->model('device');
				$this->device->update_software($data, $id);
			}
			$result = array('code' => 0, 'msg' => 'success');
		}
		echo json_encode($result);
	}
	function detail()
	{
		$data['mpeg_core'] = $this->input->get('mpeg_core');
		$data['software_id'] = $this->input->get('id');
		$data['version'] = $this->input->get('version');
		$this->load->view('bootstrap/software/detail', $data);
	}
}
