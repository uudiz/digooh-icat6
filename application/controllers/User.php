<?php
class User extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('user');
        $this->load->helper('language');
    }

    /**
     * 用户列表展示页
     *
     * @param object $curpage [optional]
     * @return
     */
    public function index()
    {
        $data = $this->get_data();


        if ($this->get_auth() <= 2) {
            $data['body_file'] = 'bootstrap/404';
        } else {
            $data['body_file'] = 'bootstrap/users/index';
        }
        $this->load->model('membership');
        $data['companies'] = $this->membership->get_all_company_list();

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
        $company_id = $this->input->post('company_id');

        $filter_array = array();
        if ($name) {
            $filter_array['name'] = $name;
        }
        if ($company_id && $company_id > -1) {
            $filter_array['company_id'] = $company_id;
        }

        $rest = $this->membership->get_user_list($this->get_cid(), $offset, $limit, $order_item, $order, $filter_array);
        if ($this->config->item('with_register_feature')) {
            if ($rest['total'] > 0) {
                foreach ($rest['data'] as $user) {
                    $stores = $this->membership->get_user_store_names($user->id);

                    if ($stores) {
                        $user->stores = $stores;
                    }
                    //$user->stores = $this->membership->get_user_store_names($user->id);
                }
            }
        }

        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];

        echo json_encode($data);
    }

    /**
     * 修改用户信息
     *
     * @return
     */
    public function edit()
    {

        $this->lang->load('player');
        $this->lang->load('criteria');
        $this->addCss("/assets/bootstrap/css/select2totree.css", false);
        $this->addJs("/assets/bootstrap/js/select2totree.js", false);
        $this->addJs("/assets/js/form.js", false);
        $id = $this->input->get('id');
        $cid = $this->input->get('company_id');

        if (!isset($cid) || empty($cid)) {
            $cid = $this->get_cid();
        }


        $this->load->model('membership');
        $this->load->model('program');
        $this->load->model('device');
        $this->load->model('material');
        $data = $this->get_data();

        if ($id) {
            $data['title'] = $this->lang->line('edit.user');
            $user = $this->membership->get_user($id, true);

            if ($user) {
                $data['data'] = $user;
                $data['use_player'] = $user->use_player;
                $cid = $user->company_id;
                $company = $this->membership->get_company($cid);
            } else {
                return;
            }
        } else {
            $company = $this->membership->get_company($cid);
            $data['title'] = $this->lang->line('create.user');
            if ($company->logo) {
                $logo = array();
                $logo['logo'] = $company->logo;
                $data['data'] = (object)$logo;
            }
        }

        if ($company) {
            $pid = $company->pId;
        } else {
            $pid = null;
        }

        $data['parent_id'] = $pid;
        if ($pid) {

            $cris = $this->get_criteria($cid, $pid);
            $data['criteria'] = $cris['criteria'];
            $filter_array = array();
            if (isset($cris['filter_array'])) {
                $filter_array = array_merge($filter_array, $cris['filter_array']);
            }
            $players = $this->device->get_player_list($pid, $filter_array);
            // $data['tags'] = $this->device->get_tag_list($pid)['data'];
            $data['players'] = $players['data'];
        } else {
            $cris = $this->device->get_criteria_list($cid);
            if ($cris) {
                $data['criteria'] = $cris['data'];
            }
            $data['tags'] = $this->device->get_tag_list($cid)['data'];
            $data['players'] = $this->device->get_players_byCompany($cid);
        }
        $cams = $this->program->get_playlist_list($cid);
        if ($cams) {
            $data['campaigns'] = $cams['data'];
        }

        if ($this->config->item("with_template") && $this->config->item('user_with_more_previlege')) {
            $this->load->model('template');
            $templates = $this->template->get_template_list($cid);
            $data['templates'] = $templates['data'];
        }

        if ($this->config->item('with_register_feature')) {
            $this->load->model('store');
            $data['register_feature_on'] = isset($company->register_feature) && $company->register_feature == 1 ? true : false;
            $stores = $this->store->get_all()['data'];

            if ($this->config->item("multi_providers")) {
                $groupedStores = [];

                $company_providers = $this->store->get_company_providers($cid);
                if ($company_providers) {
                    foreach ($stores as $store) {
                        if (!in_array($store->provider_id, $company_providers)) {
                            continue;
                        }
                        $providerName = $store->provider_name;
                        if (!isset($groupedStores[$providerName])) {
                            $groupedStores[$providerName] = [];
                        }
                        $groupedStores[$providerName][] = $store;
                    }
                }
                $stores = $groupedStores;
            }
            $data['stores'] = $stores;
        }

        //$data['folders'] = json_encode($this->get_tree_folders($cid)['tree_folders']);
        $data['cur_uid'] = $this->get_uid();
        $data['uid'] = $id;

        $flag = $this->device->get_rootFolder_id($id);
        $data['flag'] = $flag;

        if ($this->config->item('user_with_more_previlege')) {
            $data['body_file'] = 'bootstrap/users/more_previlege_form';
        } else {
            $data['body_file'] = 'bootstrap/users/form';
        }
        $data['cid'] = $cid;


        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function checkName()
    {
        $name = $this->input->get('name');
        $id =  $this->input->get('id');

        $this->load->model('membership');
        $flag = $this->membership->exist_user($name, $id);

        if ($flag) {
            echo json_encode(sprintf($this->lang->line('warn.user.exist'), $name));
            return;
        }
        echo json_encode(true);
    }
    /**
     * 保存用户
     *
     * @return
     */
    public function do_save()
    {
        $cid = $this->input->post('cid');
        $id = $this->input->post('id');
        $name = trim($this->input->post('name'));
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $save_type = $this->input->post('save_type');
        $use_player = $this->input->post('use_player');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', $this->lang->line('user_name'), 'trim|required');
        $this->form_validation->set_rules('auth', $this->lang->line('rule'), 'required|numeric');

        // $this->form_validation->set_rules('cid', $this->lang->line('company'), 'required|numeric');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        // $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $auth = $this->config->item('auth_view');

        $result = array();
        $system_users = $this->config->item('system_users');

        if ($this->form_validation->run() == false) {
            $result = array('code' => 1, 'msg' => validation_errors());
        } elseif (in_array($name, $system_users)) {
            $result = array('code' => 1, 'msg' => $this->lang->line('warn.system.user'));
        } else {
            $folders = array();
            $criteria = array();
            $campaigns = array();
            $templates = array();

            $auth = $this->input->post('auth');


            if ($this->config->item('user_with_more_previlege')) {
                $all_players = $this->input->post('all_players');
                $all_templates = $this->input->post('all_templates');
                $all_folders = $this->input->post('all_folders');
                $all_campaigns = $this->input->post('all_campaigns');
                if ($auth == 0) {
                    $all_templates = 1;
                }
            } else {
                $all_campaigns = 0;
                $all_folders = 0;
                $all_players = 0;
                $all_templates = 0;
            }
            if ($auth <= 1 && $use_player === null) {
                $result = array('code' => 1, 'msg' => sprintf($this->lang->line('warn.criteria.empty')));
                echo json_encode($result);
                return;
            }

            switch ($auth) {
                case 0:
                    $folders = $this->input->post('folders');
                    $criteria = $this->input->post('criteria');


                    $players = $this->input->post('players');
                    $campaigns = $this->input->post('campaigns');

                    if (!$all_folders && !$folders) {
                        $result = array('code' => 1, 'msg' => sprintf($this->lang->line('warn.folder.empty')));
                        echo json_encode($result);
                        return;
                    }
                    if (!$all_players) {
                        if ($use_player) {
                            if (!$players) {
                                $result = array('code' => 1, 'msg' => sprintf($this->lang->line('warn.player.empty')));
                                echo json_encode($result);
                                return;
                            }
                        } elseif (!$use_player && !$criteria) {
                            $result = array('code' => 1, 'msg' => sprintf($this->lang->line('warn.criteria.empty')));
                            echo json_encode($result);
                            return;
                        }
                    }
                    if (!$all_campaigns) {
                        if (!$campaigns) {
                            $result = array('code' => 1, 'msg' => sprintf($this->lang->line('warn.campaign.empty')));
                            echo json_encode($result);
                            return;
                        }
                    }
                    break;
                case 1:
                    $auth = $this->config->item('auth_franchise');
                    $campaigns = $this->input->post('campaigns');
                    $folders = $this->input->post('folders');

                    if (!$folders) {
                        $result = array('code' => 1, 'msg' => sprintf($this->lang->line('warn.folder.empty')));
                        echo json_encode($result);
                        return;
                    }


                    $criteria = $this->input->post('criteria');
                    $players = $this->input->post('players');
                    $templates = $this->input->post('templates');


                    if ($this->config->item('new_campaign_user')) {
                        if ($use_player) {
                            if (!$players) {
                                $result = array('code' => 1, 'msg' => sprintf($this->lang->line('warn.player.empty')));
                                echo json_encode($result);
                                return;
                            }
                        } elseif (!$criteria) {
                            $result = array('code' => 1, 'msg' => sprintf($this->lang->line('warn.criteria.empty')));
                            echo json_encode($result);
                            return;
                        }

                        if ($this->config->item("with_template")) {
                            if (!$all_templates) {
                                if (!$templates) {
                                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('warn.template.empty')));
                                    echo json_encode($result);
                                    return;
                                }
                            }
                        }
                    }
                    if (!$all_campaigns) {
                        if (!$campaigns) {
                            $result = array('code' => 1, 'msg' => sprintf($this->lang->line('warn.campaign.empty')));
                            echo json_encode($result);
                            return;
                        }
                    }


                    break;
                case 2:

                    break;
                default:
                    break;
            }


            $this->load->model('membership');
            if ($this->membership->is_user_limited($cid) && $save_type) {
                $result = array('code' => 1, 'msg' => $this->lang->line('warn.user.limited'));
            } elseif ($id == 0 && $this->membership->exist_user($this->input->post('name'), $id)) {
                $result = array('code' => 1, 'msg' => $this->lang->line('warn.user.exist'));
            } else {
                $data = array(
                    'name' => $name,
                    'descr' => $this->input->post('descr'),
                    'auth' => $auth,
                    'email' => $email,
                    'use_player' => $use_player,
                    //'data_entry_text' => $this->input->post('data_entry_text')
                );


                if ($password && !empty($password)) {
                    // $data['password']= $password;
                    $data['password'] = password_hash($password, PASSWORD_DEFAULT);
                }
                if ($auth == 0) {
                    $data['can_publish'] = $this->input->post('can_publish') ?? 0;
                    if ($this->input->post('can_replace_main') != null) {
                        $data['can_replace_main'] = $this->input->post('can_replace_main') ?? 0;
                    }
                } else {
                    $data['can_publish'] = true;
                    if ($this->input->post('can_replace_main') != null) {
                        if ($auth == 5) {
                            $data['can_replace_main'] = true;
                        } else {
                            $data['can_replace_main'] = false;
                        }
                    }
                }

                if ($this->config->item('tfa_enabled') == 1) {
                    $data['tfa_enabled'] = $this->input->post('tfa_enabled');
                }

                if ($this->config->item('user_with_more_previlege')) {
                    $data['all_players'] = $all_players;
                    $data['all_folders'] = $all_folders;
                    $data['all_campaigns'] = $all_campaigns;
                    $data['all_templates'] = $all_templates;
                    $api_only = $this->input->post('api_only');

                    if ($api_only == 0 || $api_only == 1) {
                        $data['api_only'] = $api_only;
                    }
                }


                if (isset($_FILES['logo'])) {
                    $logo = $_FILES['logo'];

                    $fileName = time() . '_' . $logo['name'];
                    $destPath = "/images/logos/$fileName";
                    if ($logo['tmp_name'] != '' && move_uploaded_file($logo['tmp_name'],  "." . $destPath)) {
                        $data['logo'] = $destPath;
                        if ($id > 0) {
                            $cur_user = $this->membership->get_user($id);
                            if ($cur_user->logo != '' && file_exists($cur_user->logo)) {
                                unlink($cur_user->logo);
                            }
                        }
                    } else {
                        $data['logo'] = '';
                    }
                }

                if ($id > 0) {
                    $this->membership->update_user($data, $id);
                } else {

                    $data['company_id'] = $cid ? $cid : $this->get_cid();
                    $id = $this->membership->add_user($data);
                }


                if ($id != false) {
                    if ($this->config->item('user_with_more_previlege')) {
                        $this->membership->insertOrUpdateUserPrevilege($id, $this->input->post('privilege'));
                    }
                    if ($auth >= 4) {
                        $this->membership->delete_user_previlege($id);
                    }
                    //check assign group/folder
                    $this->load->model('device');

                    if (!empty($folders)) {
                        $this->device->delete_assign_folder($id);
                        $this->device->assign_folder($folders, $id);
                    } else {
                        $this->device->delete_assign_folder($id);
                    }

                    if (!empty($criteria)) {
                        $this->device->assign_user_criteria($criteria, $id);
                    } else {
                        $this->device->delete_user_criteria($id);
                    }

                    if (!empty($players)) {
                        $this->device->assign_user_player($players, $id);
                    } else {
                        $this->device->delete_user_player($id);
                    }


                    if (!empty($campaigns)) {
                        $this->device->assign_campaign($campaigns, $id);
                    } else {
                        $this->device->delete_assign_campaign($id);
                    }

                    if ($this->config->item("with_template") && $this->config->item("new_campaign_user") && $auth == 1) {
                        if (!empty($templates)) {
                            $this->membership->assign_user_templates($templates, $id);
                        } else {
                            $this->membership->delete_user_templates($id);
                        }
                    }
                    if ($this->config->item('with_register_feature')) {
                        $stores = $this->input->post('stores');
                        $this->membership->assign_user_stores($stores, $id);
                    }

                    $data['id'] = $id;
                    $data['add_time'] = date('Y-m-d H:i:s');
                    $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                    $result = array_merge($data, $result);
                } else {
                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('user')));
                }
            }
        }

        $result['admin'] = $this->is_super();
        echo json_encode($result);
    }


    public function resetPassword()
    {
        $this->addJs("/assets/js/form.js", false);
        $data = $this->get_data();

        $data['body_file'] = 'bootstrap/users/reset_password';
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    /**
     * 删除用户
     *
     * @return
     */
    public function do_delete()
    {
        $uid = $_POST['id'];
        $result = false;
        if ($uid > 0) {
            $this->load->model('membership');
            $result = $this->membership->delete_user($uid);
        }
        $array = array();
        if ($result) {
            $array['code'] = 0;
            $array['msg'] = $this->lang->line('delete.success');
        } else {
            $array['code'] = 1;
            $array['msg']  = sprintf($this->lang->line('delete.fail'), $this->lang->line('user'));
        }

        echo json_encode($array);
    }

    public function doVerifyPassword()
    {
        $this->load->model('membership');
        $user = $this->membership->get_user($this->get_uid());
        if ($user) {
            $password = $this->input->get('password');
            if (!password_verify($password, $user->password)) {
                echo json_encode($this->lang->line('pass.not.match'));
                return;
            }
        } else {
            echo json_encode("can not find user");
        }
        echo json_encode(true);
    }
    /**
     * 更新密码
     *
     * @return
     */
    public function doResetPassword()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('old_password', $this->lang->line('old_passd'), 'trim|required');
        $this->form_validation->set_rules('new_password', $this->lang->line('new_passd'), 'trim|required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', $this->lang->line('confirm_passd'), 'trim|required|min_length[6]');
        $result = array();
        if ($this->form_validation->run() == false) {
            $result['code'] = 1;
            $result['msg']  = validation_errors();
        } else {
            if ($this->input->post('new_password') != $this->input->post('confirm_password')) {
                $result['code'] = 1;
                $result['msg']  = $this->lang->line('warn.passd.not.same');
            } else {
                $this->load->model('membership');

                if ($this->membership->is_passd($this->get_uid(), $this->input->post('old_password'))) {
                    $result['code'] = 0;
                    if ($this->membership->change_passd($this->get_uid(), $this->input->post('new_password'))) {
                        $result['msg']  = $this->lang->line('update.passd.success');
                    }
                } else {
                    $result['code'] = 1;
                    $result['msg']  = $this->lang->line('warn.passd.not.correct');
                }
            }
        }

        echo json_encode($result);
    }
    //根据输入字段   查询数据库，获取json数组
    public function json()
    {
        $auth = $this->get_auth();
        $filter_name = $this->input->post('filter_name');
        $filter_type = $this->input->post('filter_type');
        // 根据filter_name 和filter_type  匹配相应的字符串
        $this->load->model('membership');
        $array = array();
        if ($filter_name) {
            $array = $this->membership->get_arr_by_filter($filter_name, $filter_type, $auth);
        }

        $arr_get = array();
        if ($array) {
            $arr_get[] = $filter_name;
            foreach ($array as $arr) {
                $arr_get[] = $arr->name;
            }
        }
        echo json_encode($arr_get);
    }

    public function save_PLS($id, $cid)
    {
        //pls文件生成开始
        set_time_limit(0);
        $this->sep = chr(10) . chr(10);
        $rotate = 0;
        $fit = 0;
        $this->load->model('program');
        $this->load->model('device');
        $this->load->helper('file');
        $this->load->helper('media');
        $fail_count = 0;
        $fail_media = '';
        $transmodemapping = $this->config->item('media.transmode.mapping');
        $base_path = $this->config->item('base_path');

        $playlist = $this->program->get_playlist($id);
        if ($playlist) {
            $template = $this->program->get_template($playlist->template_id);
            $areas = $this->program->get_area_list($playlist->template_id);
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . chr(10);
            $xml .= '<SignwayPoster type="playlist" version="1.0.1">' . $this->sep;
            $xml .= sprintf('<Templatename>%s</Templatename>', $this->device->xmlencode($template->name)) . $this->sep;
            $xml .= sprintf('<Programme id="%d" name="%s" playtime="%s">', $playlist->id, $this->device->xmlencode($playlist->name), $playlist->play_time) . $this->sep;
            $xml .= sprintf('<ScreenType height="%d" rotation="0" width="%d"/>', $template->height, $template->width) . $this->sep;
            $xml .= sprintf('<Screen count="%d">', count($areas)) . $this->sep;
            foreach ($areas as $area) {
                switch ($area->area_type) {
                    case $this->config->item('area_type_date'):
                    case $this->config->item('area_type_time'):
                        $setting = $this->program->get_area_time_setting($area->id, $id);

                        //$xml .= sprintf('<Area id="%d" name="%s" model="%d" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%" bold="%d" color="%s" bg_color="%s" family="%s" size="%s" style="%d"/>', $area->id, $area->name, $area->area_type, ($area->x / $template->w) * 100, ($area->y / $template->h) * 100, ($area->w / $template->w) * 100, ($area->h / $template->h) * 100, $setting->bold, $setting->color, $setting->bg_color, $setting->font_family, $setting->font_size, $setting->format) . $this->sep;
                        $xml .= sprintf('<Area id="%d" name="%s" model="%d" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%" bold="%d" color="%s" bg_color="%s" family="%s" size="%s" style="%d"/>', $area->id, $area->name, $area->area_type, ($area->x / $template->w) * 100, ($area->y / $template->h) * 100, ($area->w / $template->w) * 100, ($area->h / $template->h) * 100, 0, '#ffffff', '#000000', 'Arial', 40, 0) . $this->sep;
                        break;
                    case $this->config->item('area_type_weather'):
                        $setting = $this->program->get_area_weather_setting($area->id, $id);

                        //$xml .= sprintf('<Area id="%d" name="%s" model="%d" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%" color="%s" bg_color="%s" family="%s" size="%s" style="%d"/>', $area->id, $area->name, $area->area_type, ($area->x / $template->w) * 100, ($area->y / $template->h) * 100, ($area->w / $template->w) * 100, ($area->h / $template->h) * 100, $setting->color, $setting->bg_color, $setting->font_family, $setting->font_size, $setting->format) . $this->sep;
                        $xml .= sprintf('<Area id="%d" name="%s" model="%d" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%" color="%s" bg_color="%s" family="%s" size="%s" style="%d"/>', $area->id, $area->name, $area->area_type, ($area->x / $template->w) * 100, ($area->y / $template->h) * 100, ($area->w / $template->w) * 100, ($area->h / $template->h) * 100, '#ffffff', '#000000', 'Arial', 40, 0) . $this->sep;
                        break;
                    default:
                        $xml .= sprintf('<Area id="%d" name="%s" model="%d" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%"/>', $area->id, $area->name, $area->area_type, ($area->x / $template->w) * 100, ($area->y / $template->h) * 100, ($area->w / $template->w) * 100, ($area->h / $template->h) * 100) . $this->sep;
                        break;
                }
            }
            $xml .= '</Screen>' . $this->sep;
            $portrait = $template->w < $template->h;
            $movie_area = false;
            foreach ($areas as $area) {
                $xml .= sprintf('<AreaList id="%d" playtime="%s">', $area->id, '00:00:05') . $this->sep;
                switch ($area->area_type) {
                    case $this->config->item('area_type_movie'):
                        $movie_area = true;
                        // no break
                    case $this->config->item('area_type_bg'):
                    case $this->config->item('area_type_logo'):
                    case $this->config->item('area_type_image'):
                        $medias = $this->program->get_playlist_area_media_list($id, $area->id);
                        if ($medias['total'] > 0) {
                            foreach ($medias['data'] as $media) {
                                //只有当视频播放列表的选择标志选择了，才执行旋转
                                //--2013-9-13 13:19:14  add--
                                $movie_area = $this->program->get_area($area->id);
                                $t_width = $movie_area->w;
                                $t_height = $movie_area->h;
                                if ($media->source == 2) {
                                    $outfile = $media->full_path;
                                    $size = $media->file_size;
                                    $xml .= sprintf(
                                        '<Resource id="%d" name="%s" fid="%d_%d" size="%d" signature="%s" sw110Signature="%s" transmode="%d" duration="00:%s" transittime="%s" mode="%d">',
                                        $media->id,
                                        $this->device->xmlencode($media->name), /*$media->media_id*/
                                        $media->id,
                                        $area->id,
                                        $size,
                                        $media->signature,
                                        $media->signature,
                                        $media->transmode >= 0 ? $transmodemapping[$media->transmode] : 0,
                                        $media->duration == '--' ? '00:00' : $media->duration,
                                        $media->transtime >= 0 ? $media->transtime : 0,
                                        $area->area_type
                                    ) . $this->sep;
                                    $xml .= sprintf('<HTTP>%s</HTTP>', $outfile) . $this->sep;
                                    $xml .= '</Resource>' . $this->sep;
                                } else {
                                    $outfile = generate_client_area_media($media, $template, $area, $t_width, $t_height, ($portrait && $rotate) ? (($movie_area) ? $media->rotate : true) : false, $fit);
                                    $this->db->reconnect();
                                    $size = 0;
                                    if ($outfile && file_exists($outfile)) {
                                        $size = filesize($outfile);
                                    } else {
                                        $fail_count++;
                                        if (strlen($fail_media)) {
                                            $fail_media .= ',';
                                        }

                                        $fail_media .= $media->name;
                                        continue;
                                    }
                                    $this->program->update_area_media(array(
                                        'publish_url' => $outfile
                                    ), $media->id);
                                    $pls_media_path = $base_path . substr($outfile, 1, (strlen($outfile) - 1)); //file path
                                    $pls_signature = md5_file($pls_media_path); //取得转换后的视频的MD5标签

                                    $xml .= sprintf(
                                        '<Resource id="%d" name="%s" fid="%d_%d" size="%d" signature="%s" sw110Signature="%s" transmode="%d" duration="00:%s" transittime="%s" mode="%d">',
                                        $media->id,
                                        $this->device->xmlencode($this->device->rename_media_name($media->name, $media->media_type, $area->area_type, $rotate, $media->id)), /*$media->media_id*/
                                        $media->id,
                                        $area->id,
                                        $size,
                                        $media->signature,
                                        $pls_signature,
                                        $media->transmode >= 0 ? $transmodemapping[$media->transmode] : 0,
                                        $media->duration == '--' ? '00:00' : $media->duration,
                                        $media->transtime >= 0 ? $media->transtime : 0,
                                        $area->area_type
                                    ) . $this->sep;
                                    $xml .= sprintf('<URL>%s</URL>', $outfile) . $this->sep;
                                    $xml .= '</Resource>' . $this->sep;
                                }
                            }
                        }
                        break;

                    case $this->config->item('area_type_text'):

                        $s = $this->program->get_playlist_area_text_setting($id, $area->id);
                        if ($s) {
                            $medias = $this->program->get_playlist_area_media_list($id, $area->id);
                            $rssid = -1;

                            if ($medias['total'] > 0) {
                                $rssid = $medias['data'][0]->media_id;
                            }
                            //$fonts = $this->lang->line('font.family.list');
                            $sizes = $this->lang->line('font.size.list');
                            $xml .= sprintf('<Ticker sig="%s">', md5(rtrim($s->content))) . $this->sep;
                            $xml .= sprintf('<Text color="%s" bgcolor="%s" size="%d" direction="%d" align="0" valign="0" speed="%d" duration="00:%s" rssid="%d" bgmix="%d%%">', $s->color, $s->bg_color, $s->font_size, $s->direction, $s->speed, $s->duration, $rssid, (100 - $s->transparent)) . $this->sep;
                            $xml .= '<![CDATA[' . rtrim($s->content) . ']]></Text>' . $this->sep;
                            $xml .= '</Ticker>' . $this->sep;
                        }

                        break;
                    case $this->config->item('area_type_date'):
                        break;
                    case $this->config->item('area_type_time'):
                        break;
                    case $this->config->item('area_type_weather'):
                        break;
                }
                $movie_area = false;
                $xml .= '</AreaList>' . $this->sep;
            }
            $xml .= '</Programme>' . $this->sep;
            $xml .= '</SignwayPoster>' . chr(10);

            $playlist_path = $this->config->item('playlist_publish_path') . $cid;
            if (!file_exists($playlist_path)) {
                mkdir($playlist_path, 0777, true);
            }

            $playlist_path .= '/' . $id . '.PLS';
            saveFile($playlist_path, $xml);

            //update file_size&signature
            $file_size = filesize($playlist_path);
            $signature = md5_file($playlist_path);
            $updates = array(
                'file_size' => $file_size,
                'signature' => $signature,
                'update_time' => date('Y-m-d H:i:s'),
                'published' => $this->config->item('playlist.status.published')
            );
            $this->program->update_playlist($updates, $id);
            //-pls文件生成结束
            return true;
        }
    }
    public function upload_logo()
    {
        ini_set("upload_tmp_dir", $this->config->item('tmp'));
        $config['upload_path'] = './assets/logos/';
        $config['allowed_types'] = '*'; //'gif|jpg|jpeg|png|bmp';
        $config['max_size'] = $this->config->item('logo_max_size'); //5MB
        //   $config['max_width'] = $this->config->item('logo_max_width');
        //  $config['max_height'] = $this->config->item('logo_max_height');
        $config['encrypt_name'] = true;


        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        $result = array();

        if (!$this->upload->do_upload('qqfile')) {
            $result = array('success' => false, 'code' => 1, 'msg' => $this->upload->display_errors());
        } else {
            $result = $this->upload->data();

            $code = 0;
            $msg = '';

            if ($result['image_width'] > 400 || $result['image_width'] < 100 || $result['image_height'] > 200 || $result['image_height'] < 50) {
                @unlink($result['full_path']);
                $result = array('success' => false, 'code' => 1, 'msg' => $this->lang->line('invalid.logo.size'));
            } else {
                $result['success'] = true;
                $result['code'] = 0;
            }
        }

        @unlink($_FILES['Filedata']['tmp_name']);
        echo json_encode($result);
    }

    public function reset_logo()
    {
        $id = $this->input->get("id");
        $logo = $this->config->item("with_template") ? "/assets/logos/default_logo.png" : "/assets/logos/logo-digooh.svg";
        if ($id) {
            $this->load->model('membership');
            $user = $this->membership->get_user($id);
            if ($user) {
                $this->membership->update_user(array('logo' => null), $id);
                $cur_company = $this->membership->get_company($user->company_id);
                if ($cur_company->logo && $cur_company->logo != '') {
                    $logo = $cur_company->logo;
                }
            }
        }

        $res = array('success' => 1, 'logo' => $logo);
        echo json_encode($res);
    }
}
