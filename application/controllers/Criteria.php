<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class Criteria extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('player');
        $this->lang->load('criteria');
        $this->lang->load('campaign');
        $this->lang->load('warn');
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
        $this->addJs("/assets/js/criteria.js", false);
        $data = $this->get_data();
        if ($this->get_auth() <= 2 || $this->is_partner()) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/criteria/index';
        }

        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getTableData()
    {
        $name = $this->input->post('search');

        $this->load->model('device');
        $data = $this->get_data();
        $offset = $this->input->post('offset');
        $limit = $this->input->post('limit');
        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');

        $filter_array = array();
        if ($name) {
            $filter_array['name'] = $name;
        }


        $rest = $this->device->get_criteria_list($this->get_cid(), $offset, $limit, $order_item, $order, $filter_array);
        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];

        echo json_encode($data);
    }


    public function edit()
    {
        $this->addJs("/assets/js/form.js", false);
        $this->load->model('device');

        $cid = $this->get_cid();
        $data = $this->get_data();
        $id = $this->input->get('id');
        $data['title'] = $this->lang->line('create.criteria');

        if ($id) {
            $data['title'] = $this->lang->line('edit.criteria');
            $region = $this->device->get_criteria($id, true);
            if ($region) {
                $data['data'] = $region;
            } else {
                $this->show_msg($this->lang->line('warn.param'), 'warn');
                return;
            }
        }


        $cris = $this->get_criteria($this->get_cid());
        $data['criteria'] = $cris['criteria'];

        $data['tags'] = $this->device->get_tag_list($cid)['data'];


        $players = $this->device->get_player_list($cid);
        $data['players'] = $players['data'];

        $data['body_file'] = 'bootstrap/criteria/form';
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
            $this->form_validation->set_rules('name', $this->lang->line('criteria'), 'trim|required');

            if ($this->form_validation->run() == false) {
                $result = array('code' => 1, 'msg' => validation_errors());
            } else {
                $this->load->model('device');
                $data = array(
                    'name' => $this->input->post('name'),
                    'descr' => $this->input->post('descr'),
                    'company_id' => $cid
                );
                $flag = $this->device->get_criteria_byname($id, $cid, $this->input->post('name'));
                if ($id >= 0 && $flag) {
                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('criteria.name.exsit'), $this->input->post('name')));
                } else {
                    if ($id > 0) {
                        $this->device->update_criteria($data, $id);
                    } else {
                        $id = $this->device->add_criteria($data);
                    }

                    if ($id !== false) {
                        $players = $this->input->post('players');

                        if ($players) {
                            $this->device->sync_criteria_players($players, $id);
                        }
                        $data['id'] = $id;
                        $data['add_time'] = date('Y-m-d H:i:s');
                        $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                        $result = array_merge($data, $result);
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
        $criteriaId = $this->input->post("id");
        $this->load->model('device');
        $this->load->model('program');

        $usedBy = $this->program->check_if_criteria_inuse($criteriaId);


        if ($usedBy) {
            $result['code'] = 2;
            $result['msg']  = $this->lang->line('criteria.cannot_be_deleted');
            echo json_encode($result);
            return;
        }

        if ($this->device->delete_criteria($criteriaId)) {
            $result['code'] = 0;
            $result['msg']  = $this->lang->line('delete.success');
        } else {
            $result['code'] = 1;
            $result['msg']  = $this->lang->line('delete.fail');
        }

        echo json_encode($result);
    }
    /**
     *
     */
    public function do_upload()
    {
        $config['upload_path'] = '/tmp';
        $config['allowed_types'] = 'xls|xlsx';
        $config['max_size'] = 10485760; //10MB
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        $this->load->model("device");
        $cid = $this->get_cid();

        if (!$this->upload->do_upload('file')) {
            $result = array('code' => 1, 'msg' => $this->upload->display_errors());
        } else {
            $data = $this->upload->data();
            /**  Identify the type of $inputFileName  **/
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($data['full_path']);
            /**  Create a new Reader of the type that has been identified  **/
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            /**  Advise the Reader that we only want to load cell data  **/
            $reader->setReadDataOnly(true);

            /**  Load $inputFileName to a Spreadsheet Object  **/

            $spreadsheet = $reader->load($data['full_path']);
            $worksheet = $spreadsheet->getActiveSheet();
            // Get the highest row and column numbers referenced in the worksheet
            $highestRow = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'


            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // e.g. 5

            if ($highestColumnIndex != 1) {
                $result = array('code' => 1, 'msg' => 'Invalid file format!');
                echo json_encode($result);
                return;
            }
            //from second line
            $succeed_cnt = 0;
            $skiped_cnt = 0;

            for ($row = 2; $row <= $highestRow; $row++) {
                $line = '';
                for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                    $value = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    if ($line == '') {
                        $line = $value;
                    } else {
                        $line = $line . ": " . $value;
                    }
                }


                $flag = $this->device->get_criteria_byname(0, $cid, $line);
                if ($flag) {
                    $skiped_cnt++;
                    continue;
                } else {
                    $data = array(
                        'name' => $line,
                        'company_id' => $cid,
                    );

                    $id = $this->device->add_criteria($data);
                    if ($id !== false) {
                        $succeed_cnt++;
                    }
                }
            }

            $result = array('code' => 0, 'msg' => "New addition has $succeed_cnt criteria, and skip $skiped_cnt old criteria.");
        }
        echo json_encode($result);
    }
}
