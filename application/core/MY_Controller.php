<?php
/*
 * Created on 2011-12-11
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

use \BlueM\Tree\Serializer\HierarchicalTreeJsonSerializer;

class MY_Controller extends CI_Controller
{
    private $jsList = array();
    private $cssList = array();

    protected $cur_lang = "english";
    //protected $cur_lang;


    public function __construct($loginFiltered = true)
    {
        parent::__construct();
        if ($loginFiltered) {
            $this->_login_filter();
        }

        //默认语言
        if ($this->config->item('mia_system_multi_language')) {
            $lang = $this->session->userdata("language");

            if (!$lang) {
                //$lang =  $this->config->item('language');
                $accept_lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
                $browser_lang = substr($accept_lang, 0, 4);
                if (preg_match("/de/i", $browser_lang)) {
                    $lang = 'germany';
                } else {
                    $lang = "english";
                }
            }

            $this->cur_lang = $lang;
        } else {
            $this->cur_lang = $this->config->item('language');
            if ($this->cur_lang != "english" && $this->cur_lang != "germany") {
                $this->cur_lang = 'germany';
            }
        }


        $this->config->set_item('language', $this->cur_lang);
        $this->lang->load('common');
        $this->lang->load('warn');
        $this->load->helper('language');
        $this->load->helper('date');
    }

    public function _login_filter()
    {
        $uid = $this->session->userdata("uid");

        if (!isset($uid) || empty($uid)) {
            redirect("/login");
            die();
        }

        if ($this->config->item('tfa_enabled') == 1 && $this->session->userdata("tfa_enabled") == 1) {
            if ($this->session->userdata('tfa_verified') != 1) {
                redirect("/login");
                die();
            }
        }
    }

    public function get_session_id()
    {
        $session_id = $this->session->userdata("session_id");
        if (empty($session_id)) {
            return false;
        } else {
            return $session_id;
        }
    }

    public function get_uid()
    {
        $uid = $this->session->userdata("uid");
        if (!isset($uid) || empty($uid)) {
            return 0;
        } else {
            return $uid;
        }
    }
    /**
     * 获取用户名
     * @return
     */
    public function get_name()
    {
        $uname = $this->session->userdata("uname");
        if (!isset($uname)) {
            return '';
        } else {
            return $uname;
        }
    }
    /**
     * 获取公司ID
     * @return
     */
    public function get_cid()
    {
        $cid = $this->session->userdata("cid");
        if (!isset($cid)) {
            return 0;
        } else {
            return $cid;
        }
    }

    /**
     * 获取权限信息
     *
     * @return
     */
    public function get_auth()
    {
        $auth = $this->session->userdata("auth");
        if (!isset($auth)) {
            return 0;
        } else {
            return $auth;
        }
    }

    /**
     * 获取当前公司的时区
     * @return
     */
    public function get_time_zone()
    {
        $time_zone = $this->session->userdata("time_zone");
        if (!isset($time_zone)) {
            return false;
        } else {
            return $time_zone;
        }
    }

    /**
     * DST是否开启，开启为TRUE，否则为FALSE
     *
     * @return
     */
    public function is_dst_on()
    {
        $dst = $this->session->userdata("dst");
        if ($dst == 1) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 判断是否为超级管理员
     * @return
     */
    public function is_super()
    {
        return $this->get_auth() == $this->config->item('auth_system');
    }

    /**
     * 是否为管理员
     *
     * @return
     */
    public function is_admin()
    {
        return $this->get_auth() == $this->config->item('auth_admin');
    }

    /**
     * 是否为分组管理员
     *
     * @return
     */
    public function is_group()
    {
        return $this->get_auth() == $this->config->item('auth_group');
    }

    /**
     * 是否为普通用户
     * @return
     */
    public function is_user()
    {
        return $this->get_auth() == $this->config->item('auth_view');
    }

    /**
     * 是否为Supervisor
     * @return
     */
    public function is_supervisor()
    {
        return $this->get_auth() == $this->config->item('auth_franchise');
    }

    public function is_partner()
    {
        return $this->get_parent_company_id() ? true : false;
    }


    /**
     * 是否具备某种权限
     *
     * @param object $action
     * @return
     */
    public function has_auth($action)
    {
        $auth = false;

        switch ($action) {
            case 'user':
            case 'create_user':
            case 'update_user':
                $auth = ($this->get_auth() >= $this->config->item('auth_admin')) ? true : false;
                break;
        }

        return $auth;
    }

    /**
     * 获取当前用户的local时间
     *
     * @param object $server_time 当前服务器存放的时间
     * @param object $time_zone [optional] 默认使用通用设置
     * @param object $dst [optional] 默认使用通用设置
     * @return 如果成功返回local的显示时间，否则返回服务器原始时间
     */
    protected function get_local_time($server_time, $time_zone = null)
    {
        if ($time_zone == null) {
            $time_zone = $this->get_time_zone();
        }

        $dst = $this->is_dst_on();



        $local_time = server_to_local($server_time, $time_zone, $dst);
        if ($local_time) {
            return $local_time;
        }

        return $server_time;
    }

    /**
     * 没有权限
     *
     * @return
     */
    public function noauth()
    {
        $this->load->view('auth');
    }

    /**
     * 显示错误提示消息
     *
     * @param object $msg
     * @param object $level
     * @return
     */
    public function show_msg($msg, $level)
    {
        $data['msg'] = $msg;
        switch ($level) {
            case 'info':
                $data['class'] = 'information';
                break;
            case 'warn':
                $data['class'] =  'attention';
                break;
            case 'error':
                $data['class'] = 'error';
                break;
            case 'success':
            default:
                $data['class'] = 'success';
                break;
        }

        $this->load->view('message', $data);
    }

    /**
     * JS相对于js目录下
     * 如：/js/jquery/jquery.js
     * $js = jquery/jquery.js
     */
    public function addJs($js, $relative = true)
    {

        if (!empty($js)) {
            if ($relative) {
                ///static/css/
                array_push($this->jsList, '/static/js/' . $js);
            } else {
                array_push($this->jsList, $js);
            }
        }
    }
    /**
     * 从数组中获取某个key的值
     *
     * @param object $array
     * @param object $key
     * @return 成功返回该值，否则返回FALSE
     */
    public function get_value($array, $key)
    {
        if (empty($array) || empty($key)) {
            return false;
        }
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        return false;
    }

    /**
     * 返回JS列表
     *
     */
    public function getJsList()
    {
        return $this->jsList;
    }

    public function addCss($css, $relative = true)
    {
        if (!empty($css)) {
            if ($relative) {
                array_push($this->cssList, '/static/css/' . $css);
            } else {
                array_push($this->cssList, $css);
            }
        }
    }

    public function getCssList()
    {
        return $this->cssList;
    }

    /**
     * 获取页面基本数据信息
     * @return
     */
    protected function get_data()
    {
        $data = array();
        $data['lang'] = $this->cur_lang;
        $data['username'] = $this->get_name();
        $data['auth'] = $this->get_auth();
        $data['cssList'] = $this->getCssList();
        $data['jsList'] = $this->getJsList();
        $data['super']  = $this->is_super();
        $data['VIEW'] = $this->config->item('auth_view');
        $data['FRANCHISE'] = $this->config->item('auth_franchise');
        $data['GROUP'] = $this->config->item('auth_group');
        $data['ADMIN'] = $this->config->item('auth_admin');
        $data['SYSTEM'] = $this->config->item('auth_system');
        $data['date'] = intval(time() / (3600 * 24));
        $data['pid'] = $this->get_parent_company_id();
        $data['bg_color'] = $this->session->userdata("theme_color");
        $data['custom_logo'] = $this->session->userdata("logo");
        $data['touch'] = $this->session->userdata("touch_function");
        $data['ssp_feature'] = $this->session->userdata("sspfeature");
        $data['tfa_secret'] = $this->session->userdata("tfa_secret");
        $data['user_email'] = $this->session->userdata("email");
        if ($this->get_auth() == 5 && !$this->get_parent_company_id()) {
            $this->load->model('material');
            $data['unproved_media_cnt'] = $this->material->get_unapproved_media_cnt($this->get_cid());
        }

        return $data;
    }

    /**
     * 转化为显示
     *
     * @param object $diskSize
     * @return
     */
    protected function showDiskSize($diskSize)
    {
        if ($diskSize) {
            if ($diskSize > 1024) {
                $diskSize /= 1024;
                if ($diskSize > 1024) {
                    return intval($diskSize / 1024) . 'MB';
                } else {
                    return intval($diskSize) . 'KB';
                }
            } else {
                return $diskSize . 'byte';
            }
        } else {
            return '0';
        }
    }

    /**
     * 获取文件夹大小
     * @param object $dir
     * @return
     */
    public function getDirSize($dir)
    {
        $sizeResult = 0;
        $handle = opendir($dir); //打开文件流
        while (false !== ($FolderOrFile = readdir($handle))) { //循环判断文件是否可读
            if ($FolderOrFile != "." && $FolderOrFile != "..") {
                if (is_dir("$dir/$FolderOrFile")) { //判断是否是目录
                    $sizeResult += $this->getDirSize("$dir/$FolderOrFile"); //递归调用
                } else {
                    $sizeResult += filesize("$dir/$FolderOrFile");
                }
            }
        }
        closedir($handle); //关闭文件流
        return $sizeResult; //返回大小
    }

    //
    public function get_parent_company_id()
    {
        $pid = $this->session->userdata("pId");
        if (!isset($pid) || empty($pid)) {
            return 0;
        } else {
            return $pid;
        }
    }

    public function get_tree_folders($cid = 0, $parent_id = 0)
    {
        $cid = $cid ? $cid : $this->get_cid();
        $parent_id = $parent_id ? $parent_id : $this->get_parent_company_id();

        $this->load->model('material');
        $this->load->model('device');
        $treeFolders = array();
        $data = array();

        $auth = $this->get_auth();
        $user_folders = [];

        if ($auth <= 1 && !$this->config->item("new_campaign_user")) {
            $this->load->model("device");
            $user_folders = $this->device->get_folder_ids($this->get_uid());  //获取用户分配的文件夹
        }


        $folders = $this->material->get_all_folder_list($parent_id ?: $cid, $user_folders);

        if (!empty($folders)) {
            if ($auth >= 4) {
                $mySerializer = new HierarchicalTreeJsonSerializer('inc');

                $tree = new BlueM\Tree($folders, ['jsonSerializer' => $mySerializer, 'rootId' => null, 'parent' => 'pId']);

                if (!$parent_id) {
                    $treeFolders = $tree;
                } else {
                    $rootFolderID = $this->material->get_partner_rootFolder($cid);
                    if ($rootFolderID) {
                        $node = $tree->getNodeById($rootFolderID);
                        if ($node) {
                            $treeFolders = $node->getDescendantsAndSelf();
                            foreach ($node->getDescendantsAndSelf() as $child) {
                                $folders_arr[] =  $child->getId();
                            }
                            $data['folder_id'] = $folders_arr;
                        }
                    }
                }
            } else {
                if ($this->config->item("new_campaign_user")) {

                    $mySerializer = new HierarchicalTreeJsonSerializer('inc');
                    $rootFolderID = $this->device->get_user_folderID($this->get_uid());
                    $tree = new BlueM\Tree($folders, ['jsonSerializer' => $mySerializer, 'rootId' => null, 'parent' => 'pId']);

                    if ($rootFolderID) {
                        $node = $tree->getNodeById($rootFolderID);
                        if ($node) {
                            $rootNode = $node->getDescendantsAndSelf();
                            $newData = array();
                            foreach ($rootNode as $child) {
                                $nodeId =  $child->getId();
                                $item = $child->toArray();
                                if ($nodeId == $rootFolderID) {
                                    $item['parent'] = null;
                                }
                                $newData[] = $item;
                                $folders_arr[] = $nodeId;
                            }
                            $newTree = new BlueM\Tree($newData, ['jsonSerializer' => $mySerializer, 'rootId' => null, 'parent' => 'parent']);
                            $treeFolders = $newTree;
                            $data['folder_id'] = $folders_arr;
                        }
                    }
                } else {
                    $data['folder_id'] = $user_folders;

                    foreach ($folders as $f) {
                        $item['id'] = $f['id'];
                        $item['text'] = $f['name'];
                        $item['pId'] = $f['pId'];
                        $treeFolders[] = $item;
                    }
                }
            }
        }

        $data['tree_folders'] = $treeFolders;
        return $data;
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
        $this->load->model('device');
        if ($this->config->item('with_sub_folders')) {
            $treeFolders = array();

            if ($this->config->item("new_campaign_user")) {
                $folders = $this->get_tree_folders($this->get_cid(), $pId);
                if (isset($folders['root'])) {
                    $data['root'] = $folders['root'];
                }
                $treeFolders = $folders['tree_folders'];
                if (isset($folders['folder_id'])) {
                    $data['folder_id'] = $folders['folder_id'];
                }
            } else {
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

    public function get_folders_and_media($media_type, $curpage = 1, $order_item = 'id', $order = 'desc')
    {
        $this->load->model('material');
        $this->load->model('membership');
        $this->load->model('device');


        $type = $this->input->get('type');
        $settings = $this->membership->get_user_settings($this->get_uid());

        if ($type === null) {
            $type = $settings->media_view;
        } else {
            if ($type != $settings->media_view) {
                $this->membership->update_user_settings($this->get_uid(), array('media_view' => $type));
                $settings->media_view = $type;
            }
        }


        $limit = $this->config->item('page_media_size');
        $offset = ($curpage - 1) * $limit;


        $filter_type = $this->input->get('filter_type');
        $filter = $this->input->get('filter');
        $media_filter_array = array();
        $media_filter_array['media_type'] = $media_type;

        if ($filter_type && $filter) {
            $media_filter_array[$filter_type] = $filter;
        }

        $folder_id = $this->input->get('folder_id') !== null ? $this->input->get('folder_id') : -1;




        $auth = $this->get_auth(); //获取用户的权限
        $pId = $this->get_parent_company_id();


        $data = $this->get_data();
        $folders = $this->get_folders();



        $data['folders'] = $folders['folders'];

        if (isset($folders['root'])) {
            $data['root'] = $folders['root'];
        }
        $media_filter_array['folder_id'] = $folder_id;

        if (($folder_id == -1 && isset($folders['folder_id']))) {
            $media_filter_array['folder_id'] = $folders['folder_id'];
        }

        $cid = $folders['cid'];

        $medias = $this->material->get_media_list($cid, $offset, $limit, $order_item, $order, $media_filter_array);

        $data['type'] = $type;
        $data['auth'] = $auth;
        $data['total'] = $medias['total'];
        $data['data'] = $medias['data'];
        $data['limit'] = $limit;
        $data['curpage'] = $curpage;
        $data['order_item'] = $order_item;
        $data['order'] = $order;

        return $data;
    }

    public function get_criteria($partner_id, $parent_id = 0)
    {

        $this->load->model('membership');
        $this->load->model('device');

        $uid = $this->get_uid();


        $filter_array = array();
        $auth = $this->get_auth();
        if ($auth == 0 || ($auth == 1 && $this->config->item("new_campaign_user"))) {
            $user = $this->membership->get_user($uid);
            $pids = false;

            if ($user->use_player) {
                $pids = $this->device->get_user_player($uid);
                if (!empty($pids)) {
                    $filter_array['pids'] = $pids;
                }
            } else {
                $pids = $this->device->get_user_criterias($uid);
                if (!empty($pids)) {
                    $filter_array['criteria_array'] = $pids;
                }
            }
            if ($parent_id) {
                $cris = $this->device->get_paterner_criterias($partner_id)['data'];
            } else {
                //$cris = $this->device->get_user_criteria_list($uid);
                $cris = $this->device->get_criteria_list($partner_id)['data'];
            }

            $data['criteria'] = $cris;
        } elseif ($this->get_auth() == 1 && !$this->config->item("new_campaign_user")) {
            $this->load->model('program');
            $pids = $this->program->get_campaign_user_players($uid);
            if ($pids) {
                $filter_array['pids'] = $pids;
            }
            $data['criteria'] = [];
        } else {
            if ($parent_id) {
                $cris = $this->device->get_paterner_criterias($partner_id);

                $filter_array['pids'] = $cris['player_array'];
            } else {
                $cris = $this->device->get_criteria_list($partner_id);
            }
            $data['criteria'] = $cris['data'];
        }


        if (!empty($filter_array)) {
            $data['filter_array'] = $filter_array;
        }

        return $data;
    }

    // combine all chunks
    // no exception handling included here - you may wish to incorporate that
    function combineChunks($chunks, $targetFile)
    {
        // open target file handle
        $handle = fopen($targetFile, 'a+');

        foreach ($chunks as $file) {
            fwrite($handle, file_get_contents($file));
        }

        // you may need to do some checks to see if file 
        // is matching the original (e.g. by comparing file size)

        // after all are done delete the chunks
        foreach ($chunks as $file) {
            @unlink($file);
        }

        // close the file handle
        fclose($handle);
    }

    public function getNestedFolders()
    {
        $cid = $this->get_cid();
        $parent_id = $this->get_parent_company_id();

        if ($this->config->item("with_template")) {
            if ($cid == 0) {
                $company_id = $this->input->get('company_id');
                $this->load->model('membership');
                $company = $this->membership->get_company($company_id);
                if ($company) {
                    $cid =  $company->id;
                    $parent_id = $company->pId;
                } else {
                    $data['success'] = false;
                    $data['data'] = [];
                    echo json_encode($data);
                    return;
                }
            }
        }


        $this->load->model('material');
        $treeFolders = array();
        $data = array();

        $ret = $this->get_tree_folders($cid, $parent_id);

        $data['success'] = true;
        $data['data'] = $ret['tree_folders'];
        echo json_encode($data);
    }

    public function get_nxslot()
    {
        return $this->session->userdata("nxslot");
    }

    public function is_sspEnabled()
    {
        return $this->session->userdata("sspfeature") ? true : false;
    }

    public function get_user_media_view()
    {
        $view = $this->session->userdata("media_view");
        if ($view !== null) {
            return $view;
        }
        return 1;
    }
    public function set_user_media_view($view_type)
    {
        $this->session->set_userdata('media_view', $view_type);
    }

    public function get_2fw_secret()
    {
        return $this->session->userdata("tfa_secret");
    }
}
