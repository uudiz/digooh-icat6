<?php
/*
 * Created on 2011-12-12
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class Company extends MY_Controller
{
    public $upload_path = './upload/tmp';

    public function __construct()
    {
        parent::__construct();

        $this->lang->load('company');
        $this->load->helper('language');
        $this->load->helper('date');
        $this->lang->load('my_date');
        $this->lang->load('user');

        $this->lang->load('criteria');

        if (!$this->is_super()) {
            // $this->noauth();
            //die();
        }
    }
    /**
     * 提供翻页支持
     *
     * @param object $curpage [optional]
     * @return
     */
    public function index()
    {
        $data = $this->get_data();
        if ($this->get_auth() != 10) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $this->load->model('membership');
            $data['all_online_player_count'] = $this->membership->all_online_player();
            $data['body_file'] = 'bootstrap/companies/index';
        }

        $this->load->view('bootstrap/layout/basiclayout', $data);
    }


    public function getTableData()
    {
        $this->load->model('membership');
        $name = $this->input->post('search');
        $data = $this->get_data();
        $offset = $this->input->post('offset');
        $limit = $this->input->post('limit');
        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');


        //获取所有在线的player   2014-7-28 13:05:24   add
        $rest = $this->membership->get_company_list($offset, $limit, $order_item, $order, $name);

        $this->load->model('device');


        foreach ($rest['data'] as $company) {
            $playersCnt = $this->device->get_company_players_count($company->id, $company->pId);

            $company->players_count = $playersCnt['players_cnt'];
            $company->online_count = $playersCnt['online_cnt'];
        }

        $data = $this->get_data();




        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];


        echo json_encode($data);
    }



    public function edit()
    {
        $this->addCss("/assets/bootstrap/css/select2totree.css", false);
        $this->addJs("/assets/bootstrap/js/select2totree.js", false);
        $this->addJs("/assets/js/form.js", false);
        $id = $this->input->get('id');
        $this->load->model('membership');
        $data = $this->get_data();
        $data['title'] = $this->lang->line('create.company');
        $company = false;
        if ($id) {
            $data['title'] = $this->lang->line('edit.company');
            $company = $this->membership->get_company($id);
            if ($company->total_disk > 0) {
                $company->total_disk = $this->showDiskSize($company->total_disk);
            }

            $data['data'] = $company;
            $data['session_id'] = $this->get_session_id();
            if ($this->config->item("cost_entry")) {
                $data['cost'] = $this->membership->get_cost($id);
            }
        }
        $parent_id = $this->input->get('parent_id');
        if ($parent_id || ($company && $company->pId)) {
            $filter_array = array();
            if (!$parent_id) {
                $parent_id = $company->pId;
                $filter_array['criteria'] = $company->criterion_id;
            }
            $data['parent_id'] = $parent_id;
            $this->load->model('device');


            $exp = $this->membership->get_used_criteriaid($parent_id);
            $filter_array = array();
            if ($exp) {
                $filter_array['exclude'] = $exp;
            }

            $cris = $this->device->get_criteria_list($parent_id, 0, -1, 'name', 'asc', $filter_array);


            if ($cris) {
                $data['criteria'] = $cris['data'];
            }
            $this->load->model('material');

            // $folders = $this->material->get_all_folder_list($parent_id);
            $treeFolders = $this->get_tree_folders($parent_id);

            $data['folders'] = json_encode($treeFolders['tree_folders']);

            $players = $this->device->get_player_list($parent_id, $filter_array);
            $data['players'] = $players['data'];
        }

        $data['body_file'] = 'bootstrap/companies/form';
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    /**
     * 转化为byte单位
     *
     * @param object $diskSize
     * @return
     */
    private function parseDiskSize($diskSize)
    {
        if (strlen($diskSize)) {
            $diskSize = strtolower($diskSize);
            if (strpos($diskSize, 'gb')) {
                //return intval(substr($diskSize, 0, -2)) * 1024 * 1024 * 1024;
                return substr($diskSize, 0, -2) * 1024 * 1024 * 1024;
            } elseif (strpos($diskSize, 'mb')) {
                //return intval(substr($diskSize, 0, -2)) * 1024 * 1024;
                return substr($diskSize, 0, -2) * 1024 * 1024;
            } elseif (strpos($diskSize, 'kb')) {
                //return intval(substr($diskSize, 0, -2)) * 1024;
                return substr($diskSize, 0, -2) * 1024;
            } elseif (strpos($diskSize, 'byte')) {
            } elseif (is_numeric($diskSize)) {
                return $diskSize;
            }
        }

        return false;
    }

    public function reset_logo()
    {
        $id = $this->input->get("id");

        if ($id) {
            $this->load->model('membership');
            $cur_company = $this->membership->get_company($id);
            if ($cur_company) {
                if ($cur_company->logo != '' && file_exists($cur_company->logo)) {
                    unlink($cur_company->logo);
                }
                $this->membership->update_company(array('logo' => ''), $id);
            }
        }
        $logo = $this->config->item("with_template") ? "/assets/logos/default_logo.png" : "/assets/logos/logo-digooh.svg";
        $res = array('success' => 1, 'logo' => $logo);
        echo json_encode($res);
    }

    /**
     * 保存公司信息
     * @return
     */
    public function do_save()
    {
        $dst = $this->input->post('dst');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');

        $this->form_validation->set_rules('max_user', $this->lang->line('max_user'), 'required|numeric');

        if ($this->input->post('parent_id')) {
            //$this->form_validation->set_rules('quota', $this->lang->line('quota'), 'trim|required|numeric|greater_than[0]|less_than_equal_to[100]');
            $this->form_validation->set_rules('share_block', $this->lang->line('share.block'), 'required|numeric');
        } else {
            $this->form_validation->set_rules('start_date', $this->lang->line('start_date'), 'required|regex_match[/\d{4}-\d{2}-\d{2}/]');
            $this->form_validation->set_rules('stop_date', $this->lang->line('stop_date'), 'required|regex_match[/\d{4}-\d{2}-\d{2}/]');


            $this->form_validation->set_rules('play_time', 'JPG Play Time', 'required|numeric|greater_than[0]');


            if ($this->config->item("cost_entry")) {
                $this->form_validation->set_rules('cost_default', 'Cost per play', 'trim|required|greater_than[0]');
                $this->form_validation->set_rules('cost1_condition', 'For playing ad >', 'trim|required|greater_than[0]');
                $this->form_validation->set_rules('cost1', 'Scale price 1', 'trim|required|greater_than[0]');
                $this->form_validation->set_rules('cost2_condition', 'For playing ad >', 'trim|required|greater_than[0]');
                $this->form_validation->set_rules('cost2', 'Scale price 2', 'trim|required|greater_than[0]');
            }
        }


        $result = array();
        if ($this->form_validation->run() == false) {
            //false
            $result = array('code' => 1, 'msg' => validation_errors());
        } else {
            $total_disk = $this->input->post('total_disk');
            if ($total_disk) {
                $total_disk = $this->parseDiskSize($total_disk);
                if ($total_disk === false) {
                    $result = array('code' => 1, 'msg' => $this->lang->line('error.disk.format'));
                    echo json_encode($result);
                    return;
                }
            } else {
                $total_disk = $this->parseDiskSize($this->config->item('default.storage'));
            }
            if ($this->input->post('parent_id')) {
                $criterion_id = $this->input->post('criterion_id');
                if (!$criterion_id) {
                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('warn.criteria.empty')));
                    echo json_encode($result);
                    return;
                }
                $folders = $this->input->post('folders');
                if (!$folders) {
                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('warn.folder.empty')));
                    echo json_encode($result);
                    return;
                }
            }



            if (strtotime($this->input->post('start_date')) > strtotime($this->input->post('stop_date'))) {
                $result = array('code' => 1, 'msg' => sprintf($this->lang->line('error.range.larger'), $this->lang->line('start.date'), $this->lang->line('end.date')));
            } elseif ($this->input->post("dst") && ($this->input->post("dst") == 1)) {
                if (strtotime($this->input->post('dst_start')) > strtotime($this->input->post('dst_end'))) {
                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('error.range.larger'), $this->lang->line('dst.start'), $this->lang->line('dst.end')));
                }
            }
        }




        if (empty($result)) {
            $this->load->model('membership');
            $id = $this->input->post('id');
            $data = array(
                'name' => trim($this->input->post('name')),
                'descr' => trim($this->input->post('descr')),
                'time_zone' => $this->input->post('time_zone'),
                'dst' => $this->input->post('dst') ? intval($this->input->post('dst')) : 0,
                'start_date' => $this->input->post('start_date'),
                'stop_date' => $this->input->post('stop_date'),
                'max_user' => $this->input->post('max_user'),
                'device_setup' => $this->input->post('device_setup'),
                'auto_dst' => $this->input->post('auto_dst'),
                'total_disk' => $total_disk,
                'com_interval' => $this->input->post('com_interval'),
                'default_play_time' => $this->input->post('play_time'),
                'cust_player_field1' => $this->input->post('cust_filed1'),
                'cust_player_field2' => $this->input->post('cust_filed2'),

            );

            if ($this->config->item('xslot_on')) {
                $data['nxslot'] = $this->input->post('xslot') ?: 6;
            }


            $theme_color = $this->input->post('theme_color');

            if ($theme_color) {
                $data['theme_color'] = $theme_color;
            }

            $pid = $this->input->post('parent_id');

            if ($this->config->item('with_partners') && $pid) {
                $data['pId'] = $pid;
                $parent = $this->membership->get_company($pid);
                if ($parent) {
                    $data['device_setup'] = false;
                    $data['auto_dst'] = $parent->auto_dst;
                    $data['com_interval'] = $parent->com_interval;
                    $data['default_play_time'] = $parent->default_play_time;
                    $data['start_date'] = $parent->start_date;
                    $data['stop_date'] = $parent->stop_date;
                    $data['dst_start'] = $parent->dst_start;
                    $data['dst_end'] = $parent->dst_end;
                    $data['nxslot'] = $parent->nxslot;
                }
                $data['flag'] = $this->input->post('flag');
            }

            $flag = $this->membership->get_company_by_name($id, $this->input->post('name'));
            if ($flag) {
                $result = array('code' => 1, 'msg' => sprintf($this->lang->line('company.name.exsit'), $this->input->post('name')));
            } else {
                if ($this->input->post('dst')) {
                    $data['dst_start'] =    $this->input->post('dst_start');
                    $data['dst_end'] = $this->input->post('dst_end');
                }

                if ($id > 0) {
                    $cur_company = $this->membership->get_company($id);
                }
                if (isset($_FILES['logo'])) {
                    $logo = $_FILES['logo'];

                    $fileName = time() . '_' . $logo['name'];
                    $destPath = "/assets/logos/$fileName";
                    if ($logo['tmp_name'] != '' && move_uploaded_file($logo['tmp_name'],  "." . $destPath)) {
                        $data['logo'] = $destPath;
                        if ($cur_company) {

                            if ($cur_company->logo != '' && file_exists($cur_company->logo)) {
                                unlink($cur_company->logo);
                            }
                        }
                    }
                }

                if ($id > 0) {
                    if (!$pid) {
                        if ($cur_company && ($cur_company->start_date != $data['start_date'] || $cur_company->stop_date != $data['stop_date'])) {
                            $partner_array = array('start_date' => $data['start_date'], 'stop_date' => $data['stop_date']);
                            $this->membership->update_partners($id, $partner_array);
                        }
                    }
                    $this->membership->update_company($data, $id);
                } else {
                    if ($this->input->post('auto_dst') == 0) {
                        $dstrange = $this->membership->get_dst_range();
                        if ($dstrange) {
                            $data['dst_start'] =    $dstrange['dst_start'];
                            $data['dst_end'] = $dstrange['dst_end'];
                        }
                    }
                    $id = $this->membership->add_company($data);
                }

                if ($id !== false) {
                    $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));


                    $result['id'] = $id;
                    $result = array_merge($result, $data);
                } else {
                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('company')));
                }
                if ($this->config->item('with_partners')) {
                    if ($pid) {
                        $fields['criterion_id'] = $criterion_id;

                        $fields['root_folder_id'] = $folders;

                        $fields['quota'] = $this->input->post('quota');
                        $fields['player_quota'] = $this->input->post('player_quota');

                        $fields['shareblock'] = $this->input->post('share_block');


                        $this->membership->update_cust_fieds($fields, $id);

                        $players = $this->input->post('players');

                        $this->membership->sync_partner_players($players, $id);
                    }
                }
                if ($this->config->item("cost_entry")) {
                    $cost_data['cost_per_play'] = $this->input->post('cost_default');
                    $cost_data['cost1_condition'] = $this->input->post('cost1_condition');
                    $cost_data['cost1'] = $this->input->post('cost1');
                    $cost_data['cost2_condition'] = $this->input->post('cost2_condition');
                    $cost_data['cost2'] = $this->input->post('cost2');
                    $cost_data['company_id'] = $id;
                    $this->membership->update_cost($cost_data, $id);
                }
            }
        }

        echo json_encode($result);
    }

    /**
     * 执行删除
     *
     * @return
     */
    public function do_delete()
    {
        $cid = $_POST['id'];
        $code = 1;
        $msg  = '';
        if ($cid > 0) {
            if ($this->get_auth() == $this->config->item('auth_system')) {
                $this->load->model('membership');
                $upload_dir = $this->config->item('resources') . $cid;
                $preview_dir = $this->config->item('resources') . 'preview/' . $cid;
                $publish_dir = $this->config->item('resources') . 'publish/' . $cid;
                $this->delete_dir($upload_dir);  //删除上传的图片和视频
                $this->delete_dir($preview_dir); //删除图片和视频预览文件
                $this->delete_dir($publish_dir); //删除发布的列表

                if ($this->membership->delete_company($cid)) {
                    //$this->membership->delete_company_flag1($cid);
                    $code = 0;
                    $msg  = $this->lang->line('delete.success');
                }
            } else {
                $msg = $this->lang->line('warn.no.auth');
            }
        } else {
            $msg = $this->lang->line('warn.param');
        }

        echo json_encode(array('code' => $code, 'msg' => $msg));
    }



    //删除目录及目录下的所有文件
    public function delete_dir($dir)
    {
        //如果目录存在，就删除
        if (is_dir($dir)) {
            //先删除目录下的文件：
            $dh = opendir($dir);
            while ($file = readdir($dh)) {
                if ($file != "." && $file != "..") {
                    $fullpath = $dir . "/" . $file;
                    if (!is_dir($fullpath)) {
                        unlink($fullpath);
                    } else {
                        $this->delete_dir($fullpath);
                    }
                }
            }

            closedir($dh);
            //删除当前文件夹：
            if (rmdir($dir)) {
                return true;
            } else {
                return false;
            }
        }
    }
    /*
    //或取目录的大小
    function getDirSize($dir){
        $sizeResult = 0;
        $handle = opendir($dir);//打开文件流
        while (false!==($FolderOrFile = readdir($handle))) { //循环判断文件是否可读
            if($FolderOrFile != "." && $FolderOrFile != "..") {
                if(is_dir("$dir/$FolderOrFile")) { //判断是否是目录
                     $sizeResult += $this->getDirSize("$dir/$FolderOrFile");//递归调用
                }else{
                    $sizeResult += filesize("$dir/$FolderOrFile");
                }
            }
        }
        closedir($handle);//关闭文件流
        return $sizeResult;//返回大小
    }*/
    public function get_players_of_criterion($cri_id)
    {
        $this->load->model('device');
        $players = $this->device->get_player_by_criterion($cri_id);

        echo json_encode($players);
    }

    public function settings()
    {
        $cid = $this->get_cid();

        $this->addJs("system.js");
        $data = $this->get_data();
        $this->load->model('membership');
        $this->load->model('material');
        $this->lang->load('my_date');

        $data = $this->get_data();

        $company = $this->membership->get_company($cid);
        if ($company) {

            $company->users1 = $this->membership->get_company_notification_users($cid, 0);
            $company->users2 = $this->membership->get_company_notification_users($cid, 1);

            if ($this->config->item('has_sensor')) {
                $company->users3 = $this->membership->get_company_notification_users($cid, 2);
            }
            $used = $this->material->get_used_storage($cid);
            $data['company'] = $company;

            $users = $this->membership->get_all_user_list($cid);
            $data['users'] = $users;

            //$data['storage_used']=sprintf($this->lang->line('storage.used'), $this->showDiskSize($used), $this->showDiskSize($company->total_disk));
            $data['storage_used'] = sprintf($this->lang->line('storage.used'), $this->showDiskSize($used), $this->showDiskSize($company->total_disk)) . ",  Interval time=" . $company->com_interval . ' min';
            $data['id'] = $cid;

            $data['body_file'] = 'bootstrap/companies/setting';
        } else {
            $data['body_file'] = 'bootstrap/404';
        }
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }
    public function doSaveSettings()
    {

        $this->load->library('form_validation');

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

        if ($this->config->item('has_sensor')) {
            $notification_email_flag = $this->input->post('notification_email_flag');
            $users_grp_notification = $this->input->post('users_grp_notification');
            if ($notification_email_flag && (!$users_grp_notification || empty($users_grp_notification))) {
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

            if ($this->config->item('has_sensor')) {
                $data['notification_email_flag'] = $notification_email_flag;
            }
            $cid = $this->get_cid();
            $id = $this->membership->update_company($data, $cid);


            if ($id !== FALSE) {

                if ($offline_email_flag) {
                    $this->membership->sync_notify_users($cid, $users1, 0);
                }
                if ($offline_email_flag2) {
                    $this->membership->sync_notify_users($cid, $users2, 1);
                }
                if ($this->config->item('has_sensor')) {
                    if ($notification_email_flag) {
                        $this->membership->sync_notify_users($cid, $users_grp_notification, 2);
                    }
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
}
