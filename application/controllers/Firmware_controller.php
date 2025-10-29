<?php
class Firmware_controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('software');
        $this->load->model('firmware');
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
        $this->load->model('device');
        $data = $this->get_data();
        if ($this->get_auth() < 10) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/firmware/index';
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

        $rest = $this->firmware->get_list($offset, $limit, $order_item, $order, $filter_array);
        $model_arr = $this->lang->line('HW.model');
        foreach ($rest['data'] as $item) {
            $item->mpeg_core_org = $item->mpeg_core;
            $item->mpeg_core = isset($model_arr[$item->mpeg_core]) ? $model_arr[$item->mpeg_core] : "N/A";
        }
        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];

        echo json_encode($data);
    }


    public function edit()
    {
        $this->addJs("/assets/js/form.js", false);

        $data = $this->get_data();
        $id = $this->input->get('id');
        $data['title'] = "";



        if ($id) {
            $data['title'] = $this->lang->line('edit') . " " . $this->lang->line('firmware');
            $region = $this->firmware->get_item($id);
            if ($region) {
                $data['data'] = $region;
            } else {
                $this->show_msg($this->lang->line('warn.param'), 'warn');
                return;
            }
        }

        $data['body_file'] = 'bootstrap/firmware/form';
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

        $id = $this->input->post('id');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', $this->lang->line('firmware'), 'trim|required');

        if ($this->form_validation->run() == false) {
            $result = array('code' => 1, 'msg' => validation_errors());
        } else {
            $this->load->model('device');

            foreach ($this->input->post() as $key => $val) {
                if (
                    $key != "id" && $key != "players"
                ) {
                    $data[$key] = $val;
                }
            }


            if ($id > 0) {
                $this->firmware->update_item($data, $id);
            } else {
                $id = $this->firmware->add_item($data);
            }

            if ($id !== false) {

                $data['id'] = $id;
                $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                $result = array_merge($data, $result);
            } else {
                $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('group')));
            }
        }

        echo json_encode($result);
    }

    public function upload()
    {
        $preview = $config = $errors = [];
        $medium_id = 0;

        $targetDir =  './resources/system/firmware/';

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
        $destFile = $targetDir . time() . '_firmware.inf';
        //Make sure we have a file path
        if ($tmpFilePath != "") {
            $this->load->helper('file');
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
                        'version' =>  $version,
                        'file_size' => $fileSize,
                        'publish_time' => $img['publish_time'],
                        'mpeg_core' => $img['mpeg_core'],
                        'path' => $destFile,
                    );


                    //insert into db
                    $medium_id = $this->firmware->add_item($meta_data);
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
            if ($medium_id) {
                //$media = $this->material->get_media($medium_id);
                //$out['medium'] = $media;
            }
        }
        echo json_encode($out);
    }

    public function do_delete()
    {
        $result = array();
        $id = $this->input->post("id");

        $firmwares = $this->firmware->get_list_byId($id);

        if ($this->firmware->delete_item($id)) {
            foreach ($firmwares as $firmware) {
                @unlink($firmware->path);
            }
            $result['code'] = 0;
            $result['msg']  = $this->lang->line('delete.success');
        } else {
            $result['code'] = 1;
            $result['msg']  = $this->lang->line('delete.fail');
        }

        echo json_encode($result);
    }


    function detail()
    {
        $data['mpeg_core'] = $this->input->get('mpeg_core');
        $data['firmware_id'] = $this->input->get('id');
        $data['version'] = $this->input->get('version');
        $this->load->view('bootstrap/firmware/detail', $data);
    }
}
