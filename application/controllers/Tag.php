<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use function Matrix\minors;

class Tag extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('criteria');

        $this->lang->load('tag');
    }

    public function index()
    {
        $this->addJs("/assets/js/tags.js", false);
        $data = $this->get_data();
        if ($this->get_auth() <= 2) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/tags/index';
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


        $start = microtime(true);
        $rest = $this->device->get_tag_list($this->get_cid(), false, $offset, $limit, $order_item, $order, $name);



        $this->load->model('material');

        foreach ($rest['data'] as $tag) {
            $tag->media_cnt = $this->material->get_tag_media_count($this->get_cid(), $tag->id);
            //$tag->vcount = $this->material->get_tag_media_count($this->get_cid(), $tag->id, $this->config->item('media_type_video'));
        }



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
        $data['title'] = $this->config->item('with_template') ? $this->lang->line('create') . " " . $this->lang->line('category') : $this->lang->line('create.tag');

        if ($id) {
            $data['title'] = $this->config->item('with_template') ? $this->lang->line('edit') . " " . $this->lang->line('category') : $this->lang->line('edit.tag');
            // $data['title'] = $this->lang->line('edit.tag');
            $tag = $this->device->get_tag($id, true);
            if ($tag) {
                $data['data'] = $tag;
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


        $data['body_file'] = 'bootstrap/tags/form';
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    /**
     * 刷新页面数据信息
     *
     * @param object $curpage [optional]
     * @return
     */
    public function refresh($curpage = 1, $order_item = 'name', $order = 'asc', $main = false)
    {
        $this->addJs("tag.js");

        $this->load->model('device');
        $data = $this->get_data();
        //	$limit = $this->config->item('page_default_size');
        $limit = -1;
        $offset = ($curpage - 1) * $limit;
        $name = $this->input->get('name');

        $rest = $this->device->get_tag_list($this->get_cid(), false, $offset, $limit, $order_item, $order, $name);
        $data['total'] = $rest['total'];
        $data['data']  = $rest['data'];
        $this->load->model('material');
        foreach ($data['data'] as $tag) {
            $tag->pcount = $this->material->get_tag_media_count($this->get_cid(), $tag->id, $this->config->item('media_type_image'));
            $tag->vcount = $this->material->get_tag_media_count($this->get_cid(), $tag->id, $this->config->item('media_type_video'));
        }

        $data['cid']   = $this->get_cid();
        $data['curpage'] = $curpage;
        $data['limit'] = $limit;
        $data['order_item'] = $order_item;
        $data['order'] = $order;



        if ($main) {
            $data['body_view'] = 'org/tag/tag-list';
            $data['body_file'] = 'org/tag/tag';
            $this->load->view('include/main2', $data);
        } else {
            $this->load->view('org/tag/tag-list', $data);
        }
    }

    /**
     * 添加分组页面
     * @return
     */
    public function add()
    {
        $cid = $this->get_cid();
        $data = $this->get_data();
        $this->load->model('device');

        $players = $this->device->get_player_list($cid);
        $data['players'] = $players['data'];


        $this->load->view('org/tag/add_tag', $data);
    }



    /**
     * 创建一个分组
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
            $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');

            if ($this->form_validation->run() == false) {
                $result = array('code' => 1, 'msg' => validation_errors());
            } else {
                $this->load->model('device');
                $name = $this->input->post('name');
                $descr = $this->input->post('descr');
                $data = array(
                    'name' =>  $name,
                    'descr' => $descr,
                    'company_id' => $cid,
                );
                $flag = $this->device->get_tag_byname($id, $cid, $name);
                if ($id >= 0 && $flag) {
                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('tag.name.exsit'), $this->input->post('name')));
                } else {
                    if ($id > 0) {
                        $this->device->update_tag($data, $id);
                    } else {
                        $id = $this->device->add_tag($data, $this->get_uid());
                    }
                    if ($id !== false) {
                        $data['id'] = $id;
                        $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                        $result = array_merge($data, $result);

                        $players = $this->input->post('players');

                        if ($players) {
                            $this->device->sync_tag_players($id, $players);
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

    /**
     * 执行删除某个分组
     *
     * @return
     */
    public function do_delete()
    {
        $result = array();
        $gid = $this->input->post("id");
        $this->load->model('device');
        if ($this->device->delete_tag($gid)) {
            $result['code'] = 0;
            $result['msg']  = $this->lang->line('delete.success');
        } else {
            $result['code'] = 1;
            $result['msg']  = $this->lang->line('delete.fail');
        }

        echo json_encode($result);
    }

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

            if ($highestColumnIndex != 4 || $worksheet->getCellByColumnAndRow(1, 1)->getValue() != 'Wirtschaftsbereich') {
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


                $flag = $this->device->get_tag_byname(0, $cid, $line);
                if ($flag) {
                    $skiped_cnt++;
                    continue;
                } else {
                    $data = array(
                        'name' => $line,
                        'company_id' => $cid,
                    );

                    $id = $this->device->add_tag($data, $this->get_uid());
                    if ($id !== false) {
                        $succeed_cnt++;
                    }
                }
            }

            $result = array('code' => 0, 'msg' => "New addition has $succeed_cnt trades, and skip $skiped_cnt old trades.");
        }
        echo json_encode($result);
    }
}
