<?php

use \BlueM\Tree\Serializer\HierarchicalTreeJsonSerializer;

class Folder extends MY_Controller
{
    private $parter_root_folder_id;

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('common');
        $this->lang->load('schedule');
        $this->lang->load('folder');
        $this->lang->load('media');
        $this->lang->load('time');
        $this->lang->load('tag');
        $this->load->helper('week');
        $this->load->model('material');
        $this->load->model('device');
        if ($this->get_parent_company_id()) {
            $rootfolder = $this->material->get_partner_rootFolder($this->get_cid());
            $this->parter_root_folder_id = $rootfolder;
        } else {
            $this->parter_root_folder_id = 0;
        }
    }

    public function index()
    {
        // $this->addJs("folder.js");
        $data = $this->get_data();

        $data['body_file'] = 'bootstrap/folders/index';

        $data['tree_root'] = $this->parter_root_folder_id;


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
        $cid = $this->get_cid();

        if ($this->parter_root_folder_id) {
            $cid = $data['pid'];
        }

        $folders = $this->material->get_folder_list($cid, $offset, $limit, $order_item, $order);

        $res_folders = array();
        $root_folderID = $this->parter_root_folder_id;

        if ($this->config->item("new_campaign_user") && !$this->is_partner() && $this->get_auth() == 1) {
            $root_folderID = $this->device->get_user_folderID($this->get_uid());;
        }

        if ($root_folderID) {
            $mySerializer = new HierarchicalTreeJsonSerializer('inc');
            $tree = new BlueM\Tree($folders['data'], ['jsonSerializer' => $mySerializer, 'rootId' => null, 'parent' => 'pId']);
            $node =  $tree->getNodeById($root_folderID);

            if ($node) {
                $folderList = $node->getDescendantsAndSelf();
                $folders_arr = array();
                foreach ($folderList as $child) {
                    $folders_arr[] =  $child->getId();
                }
            }
            foreach ($folders['data'] as $key => $folder) {
                if (!in_array($folder['id'], $folders_arr)) {
                    unset($folders['data'][$key]);
                    $folders['total'] = $folders['total'] - 1;
                }
                if ($folder['id'] == $root_folderID) {
                    $folders['data'][$key]['pId'] = null;
                    $folders['data'][$key]['noDel'] = 1;
                }
            }
            $res_folders = array_values($folders['data']);
        } else {
            $res_folders = $folders['data'];
            $data['root_folder'] = 0;
        }
        if ($res_folders) {
            foreach ($res_folders as $f) {
                $f['media_count'] = $this->material->get_folder_media_count($f['id']);
            }
        }



        if (!$this->config->item("new_campaign_user") && $this->get_auth() <= 2) {
            $this->load->model('device');
            $user_folders = $this->device->get_folder_ids($this->get_uid());
            if ($user_folders) {
                $data['user_folders'] = $user_folders;
            }
        }


        $data['total'] = count($res_folders);
        $data['rows'] =  $res_folders;

        echo json_encode($data);
    }

    public function edit()
    {
        $this->addJs("/assets/js/form.js", false);
        $this->load->model('device');

        $cid = $this->get_cid();
        $data = $this->get_data();
        $id = $this->input->get('id');
        $data['title'] = $this->lang->line('create.folder');

        if ($id) {
            $data['title'] = $this->lang->line('edit.folder');
            $folder = $this->material->get_folder($id);

            if ($folder->play_time) {
                if ($folder->play_time > 59) {
                    $times = sprintf("%02d:%02d", ($folder->play_time / 60), ($folder->play_time % 60));
                } else {
                    $times = sprintf("00:%02d", $folder->play_time);
                }
                $folder->play_time = $times;
            }
            $data['data'] = $folder;

            $data['parent_id'] = $folder->pId;
        } else {
            $parent_id = $this->input->get('parent_id');
            if ($parent_id) {
                $data['parent_id'] = $parent_id;
            }
        }

        $tags = $this->device->get_tag_list($this->get_cid());
        $data['tags'] = $tags['data'];

        $data['body_file'] = 'bootstrap/folders/form';
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function get_folders()
    {
        $this->load->model('material');

        $auth = $this->get_auth(); //获取用户的权限
        $pId = $this->get_parent_company_id();

        if ($pId) {
            $cid = $pId;
        } else {
            $cid = $this->get_cid();
        }
        $data = array();

        $data['cid'] = $cid;

        if ($this->config->item('with_sub_folders')) {
            $treeFolders = array();
            if ($auth == $this->config->item('auth_admin') || $auth == 4) {
                $folders = $this->get_tree_folders($this->get_cid(), $pId);
                if (isset($folders['root'])) {
                    $data['root'] = $folders['root'];
                }
                $treeFolders = $folders['tree_folders'];
                if (isset($folders['folder_id'])) {
                    $data['folder_id'] = $folders['folder_id'];
                }
            } else {
                $folders = $this->device->get_folder_ids($this->get_uid());  //获取用户分配的文件夹
                $user_folders = $this->material->get_all_folder_list($cid, $folders, false);

                $data['folder_id'] = $folders;


                foreach ($user_folders as $f) {
                    $item['id'] = $f['id'];
                    $item['text'] = $f['name'];
                    $item['pId'] = $f['pId'];
                    $treeFolders[] = $item;
                }
            }

            $data['folders'] = json_encode($treeFolders);
        } else {
            $folders = array();
            if ($auth < 5) {
                $folders = $this->device->get_folder_ids($this->get_uid());
            }  //获取用户分配的文件夹
            $data['folders'] = $this->material->get_all_folder_list($cid, $folders, false);
        }
        return $data;
    }

    public function do_save()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');
        $result = array();
        if ($this->form_validation->run() == false) {
            $result = array('code' => 1, 'msg' => validation_errors());
        } else {
            $this->load->model('material');
            $name = $this->input->post('name');
            $descr = $this->input->post('descr');

            $cid = $this->get_parent_company_id() ?: $this->get_cid();

            $id = $this->input->post('id');

            $date_flag = $this->input->post('date_flag');
            $start_date = $this->input->post('start_date');
            $end_date = $this->input->post('end_date');


            if ($date_flag == "1") {
                if (intval(str_replace('-', '', $start_date)) > intval(str_replace('-', '', $end_date))) {
                    $code = 1;
                    $msg = $this->lang->line('warn.date.flag.range');
                    echo json_encode(array('code' => $code, 'msg' => $msg));
                    return;
                }
            }


            $playtime_str = $this->input->post('play_time');


            if ($playtime_str) {
                $play_time = 30;
                if (strlen($playtime_str) != 5 || strpos($playtime_str, ':') === false) {
                    echo json_encode(array('code' => 1, 'msg' => $this->lang->line('warn.time.flag.format')));
                    return;
                } else {
                    $t_st = explode(':', $playtime_str);
                    $t_st_m = intval($t_st[0]);
                    $t_st_s = intval($t_st[1]);
                    if (($t_st_m > 59 || $t_st_m < 0 || $t_st_s > 59 || $t_st_s < 0) ||
                        ($t_st_m == 0 && $t_st_s == 0)
                    ) {
                        echo json_encode(array('code' => 1, 'msg' => $this->lang->line('warn.time.outoutbound')));
                        return;
                    }
                    $play_time = $t_st_m * 60 + $t_st_s;
                }
            }


            $tags = $this->input->post('tags_select');


            if ($id == 0 && $this->material->get_company_folder($cid, $name, $id) && !$this->config->item('with_sub_folders')) {
                $result = array('code' => 1, 'msg' => sprintf($this->lang->line('folder.exists'), $name));
            } else {
                $f = array(
                    'name' => $name,
                    'descr' => $descr,
                    //'play_count'=> $play_count,
                    'date_flag' => $date_flag,
                );

                if ($tags) {
                    $f['tags'] = $tags;
                }

                if ($playtime_str) {
                    $f['play_time'] = $play_time;
                }

                if ($date_flag == '1') {
                    $f['start_date'] = $start_date;
                    $f['end_date'] = $end_date;
                }
                if ($this->config->item('with_sub_folders')) {
                    $parent_id = $this->input->post('parent_id');
                    if ($parent_id) {
                        $f['pId'] = $parent_id;
                    } else {
                        if ($this->parter_root_folder_id) {
                            $f['pId'] = $this->parter_root_folder_id;
                        }
                    }
                }


                if ($id) {
                    $f['id'] = $id;
                    if ($this->material->update_folder($f, $id)) {
                        $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                        $result = array_merge($f, $result);
                    } else {
                        $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('folder')));
                    }
                } else {
                    $id = $this->material->add_folder($f, $cid, $this->get_uid());
                    if ($id !== false) {
                        $f['id'] = $id;
                        $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                        $result = array_merge($f, $result);
                    } else {
                        $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('folder')));
                    }
                }
            }
        }

        echo json_encode($result);
    }

    public function do_delete()
    {
        $id = $this->input->post('id');
        $pid = $this->get_parent_company_id();
        $cid = $pid ?: $this->get_cid();

        $code = 0;
        $msg = '';
        if ($id) {
            $this->load->model('material');
            if ($this->material->delete_folder($cid, $id)) {
                $msg = $this->lang->line('delete.success');
            } else {
                $code = 1;
                $msg = $this->lang->line('param.error');
            }
        } else {
            $code = 1;
            $msg = $this->lang->line('param.error');
        }
        echo json_encode(array('code' => $code, 'msg' => $msg));
    }
}
