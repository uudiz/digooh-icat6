<?php

/**
 * 互动应用播放列表对象
 */
class Interactionpls extends MY_Controller
{
    private $sep;

    public function Interactionpls()
    {
        parent::__construct();
        $this->lang->load('interactionpls');
        $this->lang->load('interaction');
        $this->lang->load('font');
        $this->lang->load('folder');
        $this->lang->load('time');
        $this->sep = chr(10);
        $this->tab = chr(9);
        $this->tab2 = chr(9) . chr(9);
        $this->tab3 = chr(9) . chr(9) . chr(9);
    }

    /**
     * 播放列表页
     *
     * @return
     */
    public function index($curpage = 1, $order_item = 'update_time', $order = 'desc')
    {
        $this->refresh($curpage, $order_item, $order, true);
    }

    public function refresh($curpage = 1, $order_item = 'update_time', $order = 'desc', $main = false)
    {
        $this->addJs('interactionpls.js');
        $limit = $this->config->item('page_default_size');
        $offset = ($curpage - 1) * $limit;
        $cid = $this->get_cid();

        $this->load->model('program');
        $condition = false;
        if ($this->is_supervisor()) {
            $condition['add_user_id'] = $this->get_uid();
        }
        $interactionpls = $this->program->get_interactionpls_list($cid, $condition, $offset, $limit, $order_item, $orde);

        $data = $this->get_data();
        $data['total'] = $interactionpls['total'];
        $data['data'] = $interactionpls['data'];
        $data['curpage'] = $curpage;
        $data['limit'] = $limit;
        $data['order_item'] = $order_item;
        $data['order'] = $order;
        $data['body_file'] = 'program/pls/index';

        if ($main) {
            $this->load->view('include/main2', $data);
        } else {
            $this->load->view('program/pls/index', $data);
        }
    }

    public function view_touch($curpage = 1, $order_item = 'start_date', $order = 'desc')
    {
        $this->view_refresh($curpage, $order_item, $order, true);
    }

    public function view_refresh($curpage = 1, $order_item = 'start_date', $order = 'desc', $main = false)
    {
        $this->addJs('fullcalendar/fullcalendar.js');
        $this->addCss('fullcalendar/fullcalendar.css');
        $this->addJs('schedule.js');
        $this->addCss('schedule.css');
        //搜索查询
        $value = $this->input->get('value');
        $type = $this->input->get('type');

        $cid = $this->get_cid();
        $limit = $this->config->item('page_default_size');
        $offset = ($curpage - 1) * $limit;
        $data = $this->get_data();
        $this->load->model('program');
        $this->load->model('device');
        $add_user_id = false;
        $groups = false;
        $pls_id = array();
        $touchpls_id = array();
        if ($this->is_user()) {
            //查询用户所在组
            $groups = $this->device->get_group_ids($this->get_uid());
        }

        if ($this->is_supervisor()) {
            $add_user_id = $this->get_uid();
        }
        //获取组下的sch, 通过sch获取播放列表
        $list = $this->program->get_schedule_list_group($cid, $groups);
        if (count($list) > 0) {
            foreach ($list as $sch) {
                $sch->interactions = $this->program->get_schedule_interactions($sch->id);
                $touch_pls = $this->program->get_schedule_interactions($sch->id);
                if ($touch_pls) {
                    foreach ($touch_pls as $touch) {
                        $touchpls_id[] = $touch->interaction_playlist_id;
                    }
                }
            }
        }

        $total = 0;
        $view_touch = array();
        if (count($touchpls_id) > 0) {
            $view_touch = $this->program->get_view_touch_playlist($touchpls_id);
            if (is_array($view_touch) && count($view_touch) > 0) {
                $total = count($view_touch);
            }
        }

        $data['data'] = $view_touch;
        $data['total'] = $total;
        $data['curpage'] = $curpage;
        $data['order_item'] = $order_item;
        $data['order'] = $order;
        $data['limit'] = $limit;
        $data['type'] = $type;
        $data['value'] = $value;
        $data['body_file'] = 'program/pls/view_touch';

        if ($main) {
            $this->load->view('include/main2', $data);
        } else {
            $this->load->view('program/pls/view_touch', $data);
        }
    }

    /**
     * 新建互动应用列表
     * @return
     *
     */
    public function add()
    {
        $this->addJs('interactionpls.js');
        $data = $this->get_data();
        $this->load->model('program');
        $data['interaction'] = $this->program->get_all_interaction_list($this->get_cid());
        //$this->load->view('program/pls/new', $data);
        $data['body_file'] = 'program/pls/new';
        $this->load->view('include/main2', $data);
        /*
        $this->addJs('My97DatePicker/WdatePicker.js');
        $this->addCss('My97DatePicker/skin/WdatePicker.css');
        $this->addJs('interactionpls.js');
        //$this->addJs('template.js');
        $data = $this->get_data();
        $data['body_file'] = 'program/pls/new';
        $this->load->view('include/main2', $data);
        */
    }

    /**
     * 保存添加的列表名称
     *
     */
    public function do_add()
    {
        $name = $this->input->post('name');
        $interactionId = $this->input->post('interactionId');
        $descr = $this->input->post('descr');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');

        $result = array();
        if ($this->form_validation->run() == false) {
            $result = array('code' => 1, 'msg' => validation_errors());
        } else {
            $this->load->model('program');
            $interaction = $this->program->get_interaction($interactionId);
            $data = array('name' => $name, 'descr' => $descr, 'interaction_id' => $interactionId);
            $id = $this->program->add_interactionpls($data, $this->get_cid(), $this->get_uid());
            if ($id !== false) {
                $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                $result['id'] = $id;
                $result = array_merge($result, $data);
            } else {
                $result = array('code' => 0, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('playlist')));
            }
        }
        echo json_encode($result);
    }

    public function do_delete()
    {
        $id = $this->input->get('id');
        $code = 1;
        $msg = '';
        if ($id) {
            $this->load->model('program');
            if ($this->program->delete_interaction_playlist($id)) {
                $code = 0;
                $msg = $this->lang->line('delete.success');
                $playlist_path = $this->config->item('playlist_publish_path') . $this->get_cid();
                $playlist_path .= '/' . $id . '.PLS';
                @unlink($playlist_path);
            }
        }
        if ($code == 1) {
            $msg = sprintf($this->lang->line('delete.fail'), $this->lang->line('playlist'));
        }

        echo json_encode(array('code' => $code, 'msg' => $msg));
    }

    /**
     * 修改当前播放列表中的详细属性
     *
     * @return
     */
    public function screen()
    {
        $id = $this->input->get('id');
        $screen_id = $this->input->get('screen_id');
        $content = '';
        $html_content = '';
        if (empty($id)) {
            set_status_header(404);
            exit();
        }

        $this->load->model('program');
        //删除临时数据
        $this->program->update_interaction_area_media_commit($id);
        $interactionpls = $this->program->get_interaction_playlist($id);

        if ($interactionpls == false) {
            redirect('/interactionpls/index');
            exit();
        }

        $interaction = $this->program->get_interaction($interactionpls->interaction_id);
        $interaction_tree = $this->program->get_interaction_tree($interactionpls->interaction_id);
        if ($interaction == false) {
            redirect('/interactionpls/index');
            exit();
        }
        $screen_list = $this->program->get_interaction_playlist_area_screen_list($interaction->id);
        if ($screen_id == '') {
            $screen_id = $screen_list[0]->page_id;
        }
        $area_list = $this->program->get_interaction_playlist_area_list($interaction->id, $screen_id);

        if (!empty($area_list)) {
            foreach ($area_list as $area) {
                if ($area->area_type == $this->config->item('area_type_staticText')) {
                    $staticText = $this->program->get_interaction_playlist_area_static_text_setting($id, $area->id);
                    if (isset($staticText->content)) {
                        $content = $staticText->content;
                    }
                    if (isset($staticText->html_content)) {
                        $html_content = $staticText->html_content;
                    }
                }
            }
        }

        //预览图保存
        $template_preview_path = sprintf($this->config->item('tempate_preview_path'), $this->get_cid());
        if ($screen_id > 2) {
            $rpath = sprintf($this->config->item('tempate_preview_path'), $interaction->company_id);
            if ($rpath) {
                $pwidth = $this->config->item('template_preview_width');
                $pheight = $this->config->item('template_preview_height');
                $swidth = $interaction->w;
                $sheight = $interaction->h;
                if ($interaction->w < $interaction->h) {
                    $pwidth = $this->config->item('template_preview_reverse_width');
                }
                $rwidth = $pwidth / $swidth; //宽度比
                $rheight = $pheight / $sheight; //高度比
                $bg_file = false;
                $logo_file = false;

                $this->load->library('image');
                $this->image->create($pwidth, $pheight, $bg_file);
                $i = 1;
                if (!empty($area_list)) {
                    foreach ($area_list as $area) {
                        switch ($area->area_type) {
                            case $this->config->item('area_type_staticText'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_staticText_color'));
                                break;
                            case $this->config->item('area_type_movie'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_movie_color'));
                                break;
                            case $this->config->item('area_type_image'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_image' . $i . '_color'));
                                $i++;
                                break;
                            case $this->config->item('area_type_btn'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_btn_color'));
                                break;
                            case $this->config->item('area_type_webpage'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_webpage_color'));
                                break;
                            case $this->config->item('area_type_date'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_date_color'));
                                break;
                            case $this->config->item('area_type_time'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_time_color'));
                                break;
                            case $this->config->item('area_type_weather'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_weather_color'));
                                break;
                            case $this->config->item('area_type_text'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_text_color'));
                                break;
                            case $this->config->item('area_type_logo'):
                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_logo_color'), $this->config->item('area_border_color'), $logo_file);
                                break;
                        }
                    }
                }
                $absolut_path = $this->config->item('base_path') . $rpath;
                $this->image->save($absolut_path, $screen_id . 't' . $interaction->id . '.jpg');
            }
            $preview_url = $template_preview_path . '/' . $screen_id . 't' . $interaction->id . '.jpg';
        } else {
            $preview_url = $template_preview_path . '/t' . $interaction->id . '.jpg';
        }

        $this->addJs('interactionpls.js');
        $this->addCss('playlist.css');
        $this->addJs('My97DatePicker/WdatePicker.js');
        $this->addJs('colorpicker/colorpicker.js');
        $this->addJs('colorpicker/eye.js');
        $this->addJs('colorpicker/utils.js');
        $this->addCss('colorpicker/layout.css');
        $this->addCss('colorpicker/colorpicker.css');
        $this->addCss('My97DatePicker/skin/WdatePicker.css');
        $this->addJs('jquery/jquery-ui-latest.js');
        $this->addCss('jquery/jquery.ui.all.css');
        $this->addJs('flowplayer/flowplayer.min.js');
        $this->addJs('ztree/jquery.ztree.core-3.5.js');
        $this->addJs('ztree/jquery.ztree.excheck-3.5.js');
        $this->addJs('ztree/jquery.ztree.exedit-3.5.js');
        $this->addCss('zstyle/zstyle.css');

        $pages_list = array();
        $areas_list = array();
        $treejsonpls = $interaction_tree->pls_tree_json;
        $treejson = $interaction_tree->tree_json;
        $tmp_json = str_replace('},', '}@<>#', substr($treejson, 0, -1));
        $tmp_json = str_replace('id', '"id"', $tmp_json);
        $tmp_json = str_replace('pId', '"pId"', $tmp_json);
        $tmp_json = str_replace('name', '"name"', $tmp_json);
        $tmp_json = str_replace('checked', '"checked"', $tmp_json);
        $tmp_json = str_replace('open', '"open"', $tmp_json);
        $tmp_json = str_replace('iconSkin', '"iconSkin"', $tmp_json);
        $tmp_json = str_replace('noR', '"noR"', $tmp_json);
        $tmp_arr = explode('@<>#', $tmp_json);
        for ($i = 0; $i < count($tmp_arr); $i++) {
            $arr = json_decode($tmp_arr[$i], true);
            $page_id = $arr['id'];
            $page_name = $this->get_value($arr, 'iconSkin');
            $pId = $this->get_value($arr, 'pId');
            if ($page_id != $screen_id && ($page_name == 'page' || $page_name == 'mainPage')) {
                for ($y = 0; $y < count($tmp_arr); $y++) {
                    $arr2 = json_decode($tmp_arr[$y], true);
                    if ($pId == $arr2['id']) {
                        //$arr['pName'] = $arr2['name'];
                        $arr['name'] = $arr2['name'] . ' - ' . $arr['name'];
                    }
                }
                $pages_list[] = $arr;
            }
            if ($arr['pId'] == $screen_id && ($page_name == 'movie' || $page_name == 'image')) {
                for ($y = 0; $y < count($tmp_arr); $y++) {
                    $arr2 = json_decode($tmp_arr[$y], true);
                    if ($pId == $arr2['id']) {
                        $arr['name'] = $arr2['name'] . ' - ' . $arr['name'];
                        /*
                        for($z = 0; $z < count($tmp_arr); $z++) {
                            $arr3 = json_decode($tmp_arr[$z], true);
                            if($arr2['pId'] == $arr3['id']) {
                                $arr['name'] = $arr3['name'].' - '.$arr['name'];
                            }
                        }*/
                    }
                }
                $areas_list[] = $arr;
            }
        }

        $data = $this->get_data();
        $data['body_file'] = 'program/pls/screen';
        $data['interactionpls'] = $interactionpls;
        $data['interaction'] = $interaction;
        $data['area_list'] = $area_list;
        $data['content'] = $content;
        $data['html_content'] = $html_content;
        $data['treejsonpls'] = $treejsonpls;
        $data['treejson'] = $treejsonpls;
        $data['screen_list'] = $screen_list;
        $data['screen_id'] = $screen_id;
        $data['pages_list'] = $pages_list;
        $data['areas_list'] = $areas_list;
        $data['preview_url'] = $preview_url;
        $this->load->view('include/main2', $data);
    }

    /**
     * 加载指定区域
     * @return
     */
    public function area()
    {
        $interactionpls_id = $this->input->get('interactionpls_id'); //播放列表ID
        $area_id = $this->input->get('area_id');
        $screen_id = $this->input->get('screen_id');

        $this->load->model('program');
        $playlist_settings = $this->program->get_interaction_playlist($interactionpls_id); //获取列表设置
        $interaction_tree = $this->program->get_interaction_tree($playlist_settings->interaction_id);

        $flag = array($this->config->item('area_media_flag_tmp'), $this->config->item('area_media_flag_ok'));
        $area = $this->program->get_one_interaction_area($area_id);
        if ($area->area_type == $this->config->item('area_type_text')) {
            $media = $this->program->get_interaction_playlist_area_rss_list($interactionpls_id, $area_id, $flag);
        } else {
            if ($area->area_type == $this->config->item('area_type_webpage')) {
                $media = $this->program->get_interaction_playlist_area_media_list($interactionpls_id, $area_id, $flag);
            } else {
                $media = $this->program->get_interaction_playlist_area_media_list($interactionpls_id, $area_id, $flag);
            }
        }
        if ($area->area_type == $this->config->item('area_type_text')) {
            $setting = $this->program->get_interaction_playlist_area_text_setting($interactionpls_id, $area->id);
            if ($setting === false) {
                $setting = $this->get_default_text_setting($interactionpls_id, $area->id);
                $id = $this->program->add_interaction_area_text_setting($setting, $this->get_uid());
                $setting = $this->program->get_interaction_area_text_setting($id);
            }

            $data['font_font'] = $this->lang->line('font.list');
            $data['font_familys'] = $this->lang->line('font.family.list');
            $data['font_sizes'] = $this->lang->line('font.size.list');
            $data['speeds'] = $this->lang->line('text.speed.list');
            //$data['directions'] = $this->lang->line('text.direction.list');
            $data['directions'] = $this->lang->line('np200.text.direction.list');
            $data['transparents'] = $this->lang->line('text.transparent.list');

            $rss_delimiter = $playlist_settings->rss_delimiter; //rss分割字符串标志
            //$rss_delimiter = '<<'; //rss分割字符串标志
            $rss = $this->program->get_interaction_playlist_area_last_rss($interactionpls_id, $area_id);
            $rss_content = '';
            if ($rss) {
                if ($rss->type) {
                    $rss_content = $rss->url;
                } else {
                    $this->load->library('rssparser');
                    $rssObj = $this->rssparser->Get($rss->url, true, true);
                    if ($rssObj) {
                        $items = $rssObj['items'];
                        for ($i = 0; $i < count($items); $i++) {
                            switch ($setting->rss_format) {
                                case $this->config->item('rss_format_title'):
                                    $rss_content .= @$items[$i]['title'];
                                    if ($i < count($items) - 1) {
                                        //$rss_content .= '<<';
                                        $rss_content .= $rss_delimiter;
                                    }
                                    break;
                                case $this->config->item('rss_format_desc'):
                                    if (isset($items[$i]['description'])) {
                                        $rss_content .= $items[$i]['description'];
                                    }
                                    if ($i < count($items) - 1) {
                                        $rss_content .= $rss_delimiter;
                                    }
                                    break;
                                default:
                                    $rss_content .= $items[$i]['title'];
                                    $rss_content .= $rss_delimiter;
                                    if (isset($items[$i]['description'])) {
                                        $rss_content .= $items[$i]['description'];
                                    }
                                    if ($i < count($items) - 1) {
                                        $rss_content .= $rss_delimiter;
                                    }
                                    break;
                            }
                        }
                    }
                }

                $setting->content = $rss_content;
                $data['rss'] = $rss;
            }

            $area->setting = $setting;
        } elseif ($area->area_type == $this->config->item('area_type_staticText')) {
            $setting = $this->program->get_interaction_playlist_area_static_text_setting($interactionpls_id, $area->id);
            if ($setting === false) {
                $setting = $this->get_default_static_text_setting($interactionpls_id, $area->id);
                $id = $this->program->add_interaction_area_static_text_setting($setting, $this->get_uid());
                $setting = $this->program->get_interaction_area_static_text_setting($id);
            }
            $data['font_familys'] = $this->lang->line('stext.font.family.list');
            $data['font_position'] = $this->lang->line('stext.font.position.list');
            $area->setting = $setting;
        } elseif ($area->area_type == $this->config->item('area_type_date')) {
            $data['font_sizes'] = $this->lang->line('font.size.setting.list');
            $setting = $this->program->get_interaction_area_time_setting($area_id, $interactionpls_id);
            $font_size = 40;
            $sid = 0;
            $color = '#ffffff';
            $bg_color = '#000000';
            $style = 1;
            $transparent = 0;
            $language = 0;
            if ($setting) {
                $sid = $setting->id;
                $font_size = $setting->font_size;
                $color = $setting->color;
                $bg_color = $setting->bg_color;
                $style = $setting->style;
                $transparent = $setting->transparent;
                $language = $setting->language;
            } else {
                $sid = $this->program->add_interaction_area_time_setting(array('font_size' => $font_size, 'area_id' => $area_id, 'interaction_playlist_id' => $interactionpls_id, 'style' => $style), $this->get_uid());
            }
            $data['sid'] = $sid;
            $data['font_size'] = $font_size;
            $data['color'] = $color;
            $data['bg_color'] = $bg_color;
            $data['style'] = $style;
            $data['transparents'] = $this->lang->line('text.transparent.list');
            $data['transparent'] = $transparent;
            $data['language'] = $language;
            $area->setting = $setting;
        } elseif ($area->area_type == $this->config->item('area_type_time')) {
            $data['font_sizes'] = $this->lang->line('font.size.setting.list');
            $setting = $this->program->get_interaction_area_time_setting($area_id, $interactionpls_id);
            $font_size = 40;
            $sid = 0;
            $color = '#ffffff';
            $bg_color = '#000000';
            $style = 2;
            $transparent = 0;
            if ($setting) {
                $sid = $setting->id;
                $font_size = $setting->font_size;
                $color = $setting->color;
                $bg_color = $setting->bg_color;
                $style = $setting->style;
                $transparent = $setting->transparent;
            } else {
                $sid = $this->program->add_interaction_area_time_setting(array('font_size' => $font_size, 'area_id' => $area_id, 'interaction_playlist_id' => $interactionpls_id, 'style' => $style), $this->get_uid());
            }
            $data['sid'] = $sid;
            $data['font_size'] = $font_size;
            $data['color'] = $color;
            $data['bg_color'] = $bg_color;
            $data['style'] = $style;
            $data['transparents'] = $this->lang->line('text.transparent.list');
            $data['transparent'] = $transparent;
            $area->setting = $setting;
        } elseif ($area->area_type == $this->config->item('area_type_weather')) {
            $data['font_sizes'] = $this->lang->line('font.size.setting.list');
            $setting = $this->program->get_interaction_area_weather_setting($area_id, $interactionpls_id);
            $font_size = 40;
            $color = '#ffffff';
            $bg_color = '#000000';
            $style = 5;
            $sid = 0;
            $transparent = 0;
            $language = 0;
            if ($setting) {
                $sid = $setting->id;
                $font_size = $setting->font_size;
                $color = $setting->color;
                $bg_color = $setting->bg_color;
                $style = $setting->style;
                $transparent = $setting->transparent;
                $language = $setting->language;
            } else {
                $sid = $this->program->add_interaction_area_weather_setting(array('font_size' => $font_size, 'area_id' => $area_id, 'interaction_playlist_id' => $interactionpls_id, 'style' => $style), $this->get_uid());
            }
            $data['sid'] = $sid;
            $data['font_size'] = $font_size;
            $data['color'] = $color;
            $data['bg_color'] = $bg_color;
            $data['style'] = $style;
            $data['transparents'] = $this->lang->line('text.transparent.list');
            $data['transparent'] = $transparent;
            $data['language'] = $language;
            $area->setting = $setting;
        } elseif ($area->area_type == $this->config->item('area_type_btn')) {
            $pages_list = array();
            $areas_list = array();
            $treejsonpls = $interaction_tree->pls_tree_json;
            $treejson = $interaction_tree->tree_json;
            $tmp_json = str_replace('},', '}@<>#', substr($treejson, 0, -1));
            $tmp_json = str_replace('id', '"id"', $tmp_json);
            $tmp_json = str_replace('pId', '"pId"', $tmp_json);
            $tmp_json = str_replace('name', '"name"', $tmp_json);
            $tmp_json = str_replace('checked', '"checked"', $tmp_json);
            $tmp_json = str_replace('open', '"open"', $tmp_json);
            $tmp_json = str_replace('iconSkin', '"iconSkin"', $tmp_json);
            $tmp_json = str_replace('noR', '"noR"', $tmp_json);
            $tmp_arr = explode('@<>#', $tmp_json);
            for ($i = 0; $i < count($tmp_arr); $i++) {
                $arr = json_decode($tmp_arr[$i], true);
                $page_id = $arr['id'];
                $page_name = $this->get_value($arr, 'iconSkin');
                $pId = $this->get_value($arr, 'pId');
                if ($page_id != $screen_id && ($page_name == 'page' || $page_name == 'mainPage')) {
                    $pages_list[] = $arr;
                }
            }
            $areas = $this->program->get_interaction_area($playlist_settings->interaction_id, false, $screen_id);
            if (is_array($areas)) {
                foreach ($areas as $a) {
                    if ($a->page_id == $screen_id && ($a->name == 'Movie' || $a->name == 'Image1' || $a->name == 'Image2' || $a->name == 'Image3' || $a->name == 'Image4')) {
                        $arr['name'] = $a->page_name . ' - ' . $a->name;
                        $arr['id'] = $a->id;
                        $arr['type'] = $a->area_type;
                        $areas_list[] = $arr;
                    }
                }
            }

            $data['pages_list'] = $pages_list;
            $data['areas_list'] = $areas_list;

            $data['btn_action_list'] = $this->lang->line('btn.action.list');
            $data['style_list'] = $this->lang->line('btn.style.list');
            $data['screen_list'] = $this->lang->line('btn.screen.list');
            $data['close_list'] = $this->lang->line('btn.close.list');

            $setting = $this->program->get_interaction_area_btn_setting($area_id, $interactionpls_id);
            $action = 1;
            $goal = '';
            $goal_type = 0;
            $style = 1;
            $show = '';
            $showName = '';
            $fullScreen = 1;
            $closeFlag = 1;
            $timeout = '00:10:00';
            $x = 0;
            $y = 0;
            $w = 1920;
            $h = 1080;
            if ($setting) {
                $action = $setting->action;
                $goal = $setting->goal;
                $goal_type = $setting->goal_type;
                $style = $setting->style;
                $show = $setting->show;
                if ($show) {
                    $this->load->model('material');
                    $mediaShow = $this->material->get_media($show);
                    if (!empty($mediaShow)) {
                        $showName = $mediaShow->name;
                    } else {
                        $showName = '';
                    }
                } else {
                    $showName = '';
                }
                $fullScreen = $setting->fullScreen;
                $closeFlag = $setting->closeFlag;
                $timeout = $setting->timeout;
                $x = $setting->x;
                $y = $setting->y;
                $w = $setting->w;
                $h = $setting->h;
                $sid = $setting->id;
            } else {
                $sid = $this->program->add_interaction_area_btn_setting(array('area_id' => $area_id, 'interaction_playlist_id' => $interactionpls_id), $this->get_uid());
            }
            $data['action'] = $action;
            $data['goal'] = $goal;
            $data['goal_type'] = $goal_type;
            $data['style'] = $style;
            $data['show'] = $show;
            $data['showName'] = $showName;
            $data['fullScreen'] = $fullScreen;
            $data['closeFlag'] = $closeFlag;
            $data['timeout'] = $timeout;
            $data['x'] = $x;
            $data['y'] = $y;
            $data['w'] = $w;
            $data['h'] = $h;
            $data['sid'] = $sid;
            $area->setting = $setting;
        }
        $data['after_media'] = $this->input->get('after_media');
        $data['media'] = $media;
        $data['area'] = $area;
        $data['playlist_id'] = $interactionpls_id;
        $data['area_id'] = $area_id;
        $data['portrait'] = $this->program->is_portrait_template_playlist($interactionpls_id);
        $data['playlist_one'] = $this->program->get_playlist($interactionpls_id);
        $data['screen_id'] = $screen_id;

        $rss_delimiter =  $playlist_settings->rss_delimiter; //rss分割字符串标志
        $data['rss_delimiter'] = $rss_delimiter;
        $this->load->view('program/pls/screen_area', $data);
    }

    /**
     * interaction pls发布
     */
    public function do_publish()
    {
        //取消时间限制
        set_time_limit(0);
        error_reporting(E_ALL ^ E_NOTICE);
        $result = $this->do_save(true);
        if ($result['code'] > 0) {
            echo json_encode($result);
            return;
        }

        $code = 0;
        $msg = '';
        $id = $this->input->post("playlist_id");
        $this->load->model('program');
        $this->load->helper('file');
        $this->load->helper('media');
        $fail_count = 0;
        $fail_media = '';
        $transmodemapping = $this->config->item('media.transmode.mapping');
        $base_path = $this->config->item('base_path');
        $playlist = $this->program->get_interaction_playlist($id);

        if ($playlist) {
            $interaction = $this->program->get_interaction($playlist->interaction_id);
            $interaction_tree = $this->program->get_interaction_tree($playlist->interaction_id);
            $areas = $this->program->get_interaction_area_list($playlist->interaction_id);
            $pageList = $this->program->get_interaction_playlist_area_screen_list($playlist->interaction_id); // interaction 页
            $treeList = $this->treejson_to_array($interaction_tree->tree_json);
            $resources = $this->tab . '<resource>' . $this->sep;
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . chr(10);
            $xml .= '<SignwayPoster type="touch" version="1.0.1">' . $this->sep;
            $xml .= $this->tab . '<syspara>' . $this->sep;
            $xml .= $this->tab2 . sprintf('<activearea height="%d" width="%d" rotation="0"></activearea>', $interaction->height, $interaction->width) . $this->sep;
            $xml .= $this->tab2 . sprintf('<timeout interval="%s:00" action="%d" duration="00:00:10"></timeout>', $interaction->period, $interaction->action == 3 ? 4 : $interaction->action) . $this->sep;
            $xml .= $this->tab . '</syspara>' . $this->sep;
            $xml .= $this->tab . sprintf('<Project id="%d" name="%s" playtime="00:00:00" showtext="0" pageId="2" programId="1"/>', $playlist->id, $playlist->name) . $this->sep;
            $xml .= $this->tab . '<Programme id="1" name="Folder">' . $this->sep;
            foreach ($pageList as $page) {
                $image_p_src = null;
                $bg_flag = 0;
                $stext_flag = 0;
                $areasList = $this->program->get_interaction_area_list_by_fp($playlist->interaction_id, $page->page_id);
                if ($areasList) {
                    $xml .= $this->tab2 . sprintf('<Page id="%d" name="%s" interval="%s:00">', $page->page_id, $treeList[$page->page_id - 1]['name'], $interaction->period) . $this->sep;
                }

                foreach ((array)$areasList as $areas => $area) {
                    if (isset($area->area_type)) {
                        switch ($area->area_type) {
                            case $this->config->item('area_type_bg'):
                                $bg_flag = 1;
                                break;
                            case $this->config->item('area_type_staticText'):
                                $stext_flag = 1;
                                break;
                        }
                    }
                }
                foreach ((array)$areasList as $areas => $area) {
                    if (isset($area->area_type)) {
                        switch ($area->area_type) {
                            case $this->config->item('area_type_date'):
                            case $this->config->item('area_type_time'):
                                $setting = $this->program->get_interaction_area_time_setting($area->id, $id);
                                $xml .= $this->tab3 . sprintf('<Area id="%d" name="%s" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%" model="%d" zindex="%d">', $area->id, $area->name, ($area->x / $interaction->w) * 100, ($area->y / $interaction->h) * 100, ($area->w / $interaction->w) * 100, ($area->h / $interaction->h) * 100, $area->area_type, $area->id) . $this->sep;
                                $xml .= $this->tab3 . $this->tab . sprintf('<textstyle boder="0" color="%s" bgcolor="%s" family="%s" size="%d" style="%d" type="0" bgmix="%d%%" lang="%d"></textstyle>', $setting->color, $setting->bg_color, $setting->font_family, $setting->font_size, $setting->style, $setting->transparent, $setting->language) . $this->sep;
                                $xml .= $this->tab3 . '</Area>' . $this->sep;
                                break;
                            case $this->config->item('area_type_weather'):
                                $setting = $this->program->get_interaction_area_weather_setting($area->id, $id);
                                $xml .= $this->tab3 . sprintf('<Area id="%d" name="%s" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%" model="%d" zindex="%d">', $area->id, $area->name, ($area->x / $interaction->w) * 100, ($area->y / $interaction->h) * 100, ($area->w / $interaction->w) * 100, ($area->h / $interaction->h) * 100, $area->area_type, $area->id) . $this->sep;
                                $xml .= $this->tab3 . $this->tab . sprintf('<textstyle boder="0" color="%s" bgcolor="%s" family="%s" size="%d" style="%d" type="0" bgmix="%d%%"></textstyle>', $setting->color, $setting->bg_color, $setting->font_family, $setting->font_size, $setting->style, $setting->transparent) . $this->sep;
                                $xml .= $this->tab3 . '</Area>' . $this->sep;
                                break;
                            case $this->config->item('area_type_text'):
                                $xml .= $this->tab3 . sprintf('<Area id="%d" name="%s" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%" model="%d" zindex="%d">', $area->id, $area->name, ($area->x / $interaction->w) * 100, ($area->y / $interaction->h) * 100, ($area->w / $interaction->w) * 100, ($area->h / $interaction->h) * 100, $area->area_type, $area->id) . $this->sep;
                                $s = $this->program->get_interaction_area_text_setting_p($id, $area->id);
                                if ($s) {
                                    $medias = $this->program->get_interaction_playlist_area_media_list($id, $area->id);
                                    $rssid = -1;
                                    if ($medias['total'] > 0) {
                                        $this->load->model('material');
                                        $rss = $this->material->get_rss_type($medias['data'][0]->media_id);
                                        if ($rss) {
                                            $rssid = $medias['data'][0]->media_id;
                                        }
                                    }
                                    $xml .= $this->tab3 . $this->tab . sprintf('<textstyle face="%s" color="%s" bgcolor="%s" size="%d" direction="%d" align="0" valign="0" speed="%d" duration="00:%s" rssid="%d" bgmix="%d%%">', $s->font_family, $s->color, $s->bg_color, $s->font_size, $s->direction, $s->speed, $s->duration, $rssid, $s->transparent) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . '<![CDATA[' . rtrim($s->content) . ']]>' . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . '</textstyle>' . $this->sep;
                                }
                                $xml .= $this->tab3 . '</Area>' . $this->sep;
                                break;
                            case $this->config->item('area_type_bg'):
                                $medias = $this->program->get_interaction_playlist_area_media_list($id, $area->id);
                                if ($stext_flag == 0) {
                                    //只显示背景图
                                    $xml .= $this->tab3 . sprintf('<Area id="%s" name="BG" left="0%%" top="0%%" width="100%%" height="100%%" model="9" zindex="-10">', $area->id) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . '<freerun bgcolor="#000000">' . $this->sep;
                                    if ($medias['total'] > 0) {
                                        foreach ($medias['data'] as $media) {
                                            $xml .= $this->tab3 . $this->tab . sprintf('<gi id="%d" image="%d_-1_1" transmode="%d" duration="00:%s" fillmode="1" startdate="" enddate="" starttime="" cleartime=""></gi>', $media->id, $media->id, $media->transmode, $media->duration) . $this->sep;
                                            $resources .= $this->tab2 . sprintf('<fi id="%d_-1_1" mode="1" model="9" name="bg.jpg" path="%s" size="%d" signature="%s" fid="%d_-1_1"></fi>', $media->id, $media->full_path, $media->file_size, $media->signature, $media->id) . $this->sep;
                                        }
                                    }
                                    $xml .= $this->tab3 . $this->tab . '</freerun>' . $this->sep;
                                    $xml .= $this->tab3 . '</Area>' . $this->sep;
                                    $this->program->update_interaction_area_media(array('publish_url' => $media->full_path), $media->id);
                                } else {
                                    //在背景图上写字
                                    $bg_arr = $this->program->get_interaction_playlist_area_media_list($id, $area->id);
                                    foreach ((array)$areasList as $areas => $area2) {
                                        if ($area2->area_type == $this->config->item('area_type_staticText')) {
                                            $staticText = $this->program->get_interaction_playlist_area_static_text_setting($id, $area2->id);
                                            $area_text = $this->program->get_one_interaction_area($area2->id);
                                        }
                                    }
                                    //$content = $staticText->content;
                                    $content = str_replace('&#039', '\'', $staticText->content);
                                    $font_size = $staticText->font_size * 0.75;  //字体大小
                                    $font = $this->set_title_font($staticText->font_family, $staticText->bold, $staticText->italic);
                                    if (!file_exists($font)) {
                                        $font = './fonts/Arial.ttf';
                                    }
                                    //阿拉伯文现在只有一种字体
                                    if ($staticText->font_family == 'Arabic') {
                                        $font = './fonts/Arabic.ttf';
                                    }
                                    $width = 2 * $interaction->w;
                                    $height = 2 * $interaction->h;
                                    $bg_image_file = $bg_arr['data'][0]->full_path;

                                    //获取模块区域的  坐标、宽度、高度
                                    $static_x = 2 * $area_text->x;
                                    $static_y = 2 * $area_text->y;
                                    $static_w = 2 * $area_text->w;
                                    $static_h = 2 * $area_text->h;

                                    //原图片转换成 1920*1080的图片
                                    $image_p_src = $this->config->item('resources') . $this->get_cid() . '/' . $bg_arr['data'][0]->signature . '.' . $bg_arr['data'][0]->id . '.jpg';
                                    list($swidth, $sheight) = getimagesize($bg_image_file);
                                    $image_p = imagecreatetruecolor(2 * $interaction->w, 2 * $interaction->h);
                                    $image_s = imagecreatefromjpeg($bg_image_file);
                                    imagecopyresized($image_p, $image_s, 0, 0, 0, 0, 2 * $interaction->w, 2 * $interaction->h, $swidth, $sheight);
                                    if ($staticText->transparent == 2) {
                                        //$staticText->bg_color = '#000000';
                                    }
                                    $bg_red   = intval('0x' . substr($staticText->bg_color, 1, 2), 16);
                                    $bg_green = intval('0x' . substr($staticText->bg_color, 3, 2), 16);
                                    $bg_blue  = intval('0x' . substr($staticText->bg_color, 5, 2), 16);
                                    $bg_color = imagecolorallocate($image_p, $bg_red, $bg_green, $bg_blue);
                                    imagefilledrectangle($image_p, $static_x, $static_y, $static_x + $static_w, $static_y + $static_h, $bg_color);
                                    imagejpeg($image_p, $image_p_src, 100);

                                    $texts = explode('<br/>', $content);
                                    $imagebox = imagettfbbox($font_size, 0, $font, "Mia NP200");
                                    $text_length = $imagebox[2] - $imagebox[6];
                                    $text_height = $imagebox[1] - $imagebox[5];
                                    $static_y = $static_y + $text_height * 1.5;
                                    $image = @imagecreatefromjpeg($image_p_src);
                                    $red   = intval('0x' . substr($staticText->color, 1, 2), 16);
                                    $green = intval('0x' . substr($staticText->color, 3, 2), 16);
                                    $blue  = intval('0x' . substr($staticText->color, 5, 2), 16);
                                    $font_color = imagecolorallocate($image, $red, $green, $blue);
                                    // Loop
                                    $word_line = 1; //字体行数
                                    foreach ($texts as $text) {
                                        if ($word_line * $text_height * 1.5 <= $static_h) {
                                            $imagebox = imagettfbbox($font_size, 0, $font, $text);
                                            $t_width = $imagebox[2] - $imagebox[6];
                                            $t_height = $imagebox[1] - $imagebox[5];
                                            $static_x_add = 0;
                                            if ($staticText->position == 2) {
                                                $static_x_add = ($static_w - $t_width) / 2;
                                            }
                                            if ($staticText->position == 3) {
                                                $static_x_add = $static_w - $t_width;
                                            }
                                            if ($static_x_add < 0) {
                                                $static_x_add = 0;
                                            }
                                            imagefttext($image, $font_size, 0, $static_x + $static_x_add, $static_y, $font_color, $font, $text);
                                            if ($staticText->underline) {  //如果有下划线
                                                imageline($image, $static_x + $static_x_add, $static_y + 1, $static_x + $t_width + $static_x_add, $static_y + 1, $font_color);
                                            }
                                            $static_y += $text_height * 1.5;
                                            $word_line++;
                                        }
                                    }
                                    imagejpeg($image, $image_p_src, 100);
                                    imagedestroy($image);
                                    imagedestroy($image_p);
                                    imagedestroy($image_s);
                                    $bg_size = filesize($image_p_src);
                                    $bg_signature = md5_file($image_p_src);
                                    $this->program->update_interaction_area_media(array('publish_url' => $image_p_src), $bg_arr['data'][0]->id);
                                    $xml .= $this->tab3 . sprintf('<Area id="%d" name="BG" left="0%%" top="0%%" width="100%%" height="100%%" model="9" zindex="-10">', $area->id) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . '<freerun bgcolor="#000000">' . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . sprintf('<gi id="" image="%d_-1_1" transmode="" duration="00:00:15" fillmode="1" startdate="" enddate="" starttime="" cleartime="" action="" target=""></gi>', $bg_arr['data'][0]->id) . $this->sep;
                                    $resources .= $this->tab2 . sprintf('<fi id="%d_-1_1" mode="1" model="9" name="bg.jpg" path="%s" size="%d" signature="%s" fid="%d_-1_1"></fi>', $bg_arr['data'][0]->id, $image_p_src, $bg_size, $bg_signature, $bg_arr['data'][0]->id) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . '</freerun>' . $this->sep;
                                    $xml .= $this->tab3 . '</Area>' . $this->sep;
                                }
                                break;
                            case $this->config->item('area_type_staticText'):
                                //无背景图   黑色图片上写字
                                if ($bg_flag == 0 && $stext_flag == 1) {
                                    //先判断cat_playlist_area_media中有没有自动添加的bg(pid, area_id=1, media_id=1)
                                    $bg_area_media = $this->program->get_interaction_static_bg_area($id, $area->id, 1);
                                    if ($bg_area_media) {
                                        $pam_id = $bg_area_media[0]->id;
                                    } else {
                                        $static_bg_area = array(
                                            'interaction_playlist_id' => $id,
                                            'area_id' => $area->id,
                                            'media_id' => 1,
                                            'add_user_id' => 0,
                                            'flag' => 1,
                                            'publish_url' => ''
                                        );
                                        $pam_id = $this->program->add_interaction_static_bg_area($static_bg_area);
                                    }

                                    $staticText = $this->program->get_interaction_playlist_area_static_text_setting($id, $area->id);
                                    //$content = $staticText->content;
                                    $content = str_replace('&#039', '\'', $staticText->content);
                                    $font_size = $staticText->font_size * 0.75; //字体大小
                                    $font = $this->set_title_font($staticText->font_family, $staticText->bold, $staticText->italic);
                                    if (!file_exists($font)) {
                                        $font = './fonts/Arial.ttf';
                                    }
                                    //阿拉伯文现在只有一种字体
                                    if ($staticText->font_family == 'Arabic') {
                                        $font = './fonts/Arabic.ttf';
                                    }
                                    $width = 2 * $interaction->w;
                                    $height = 2 * $interaction->h;
                                    $bg_image_file = $this->config->item('images') . 'black.jpg';

                                    //获取模块区域的  坐标、宽度、高度
                                    $area_text = $this->program->get_one_interaction_area($area->id);
                                    $static_x = 2 * $area_text->x;
                                    $static_y = 2 * $area_text->y;
                                    $static_w = 2 * $area_text->w;
                                    $static_h = 2 * $area_text->h;

                                    //原图片转换成 1920*1080的图片
                                    $image_p_src = $this->config->item('resources') . $this->get_cid() . '/' . $area->id . '.jpg';
                                    list($swidth, $sheight) = getimagesize($bg_image_file);
                                    $image_p = imagecreatetruecolor(2 * $interaction->w, 2 * $interaction->h);
                                    $image_s = imagecreatefromjpeg($bg_image_file);
                                    imagecopyresized($image_p, $image_s, 0, 0, 0, 0, 2 * $interaction->w, 2 * $interaction->h, $swidth, $sheight);
                                    if ($staticText->transparent == 2) {
                                        $staticText->bg_color = '#000000';
                                    }
                                    $bg_red = intval('0x' . substr($staticText->bg_color, 1, 2), 16);
                                    $bg_green = intval('0x' . substr($staticText->bg_color, 3, 2), 16);
                                    $bg_blue = intval('0x' . substr($staticText->bg_color, 5, 2), 16);
                                    $bg_color = imagecolorallocate($image_p, $bg_red, $bg_green, $bg_blue);
                                    imagefilledrectangle($image_p, $static_x, $static_y, $static_x + $static_w, $static_y + $static_h, $bg_color);
                                    imagejpeg($image_p, $image_p_src, 100);

                                    $texts = explode('<br/>', $content);
                                    $imagebox = imagettfbbox($font_size, 0, $font, "Mia NP200");
                                    $text_length = $imagebox[2] - $imagebox[6];
                                    $text_height = $imagebox[1] - $imagebox[5];
                                    $static_y = $static_y + $text_height * 1.5;
                                    $image = @imagecreatefromjpeg($image_p_src);
                                    $red = intval('0x' . substr($staticText->color, 1, 2), 16);
                                    $green = intval('0x' . substr($staticText->color, 3, 2), 16);
                                    $blue = intval('0x' . substr($staticText->color, 5, 2), 16);
                                    $font_color = imagecolorallocate($image, $red, $green, $blue);
                                    // Loop
                                    $word_line = 1; //字体行数
                                    foreach ($texts as $text) {
                                        if ($word_line * $text_height * 1.5 <= $static_h) {
                                            $imagebox = imagettfbbox($font_size, 0, $font, $text);
                                            $t_width = $imagebox[2] - $imagebox[6];
                                            $t_height = $imagebox[1] - $imagebox[5];
                                            $static_x_add = 0;
                                            if ($staticText->position == 2) {
                                                $static_x_add = ($static_w - $t_width) / 2;
                                            }
                                            if ($staticText->position == 3) {
                                                $static_x_add = $static_w - $t_width;
                                            }
                                            if ($static_x_add < 0) {
                                                $static_x_add = 0;
                                            }
                                            imagefttext($image, $font_size, 0, $static_x + $static_x_add, $static_y, $font_color, $font, $text);
                                            if ($staticText->underline) { //如果有下划线
                                                imageline($image, $static_x + $static_x_add, $static_y + 1, $static_x + $t_width + $static_x_add, $static_y + 1, $font_color);
                                            }
                                            $static_y += $text_height * 1.5;
                                            $word_line++;
                                        }
                                    }
                                    imagejpeg($image, $image_p_src, 100);
                                    imagedestroy($image);
                                    imagedestroy($image_p);
                                    imagedestroy($image_s);

                                    $bg_size = filesize($image_p_src);
                                    $bg_signature = md5_file($image_p_src);
                                    $this->program->update_interaction_area_media(array(
                                        'publish_url' => $image_p_src
                                    ), $pam_id);
                                    $resources .= $this->tab2 . sprintf('<fi id="%d_-1_1" mode="1" model="9" name="bg.jpg" path="%s" size="%d" signature="%s" fid="%d_-1_1"></fi>', $pam_id, $image_p_src, $bg_size, $bg_signature, $pam_id) . $this->sep;
                                    $xml .= $this->tab3 . '<Area id="' . $area->id . '" name="BG" left="0%" top="0%" width="100%" height="100%" model="9" zindex="-10">' . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . '<freerun bgcolor="#000000">' . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . sprintf('<gi id="" image="%d_-1_1" transmode="" duration="00:00:15" fillmode="1" startdate="" enddate="" starttime="" cleartime="" action="" target=""></gi>', $pam_id) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . '</freerun>' . $this->sep;
                                    $xml .= $this->tab3 . '</Area>' . $this->sep;
                                }
                                break;
                            case $this->config->item('area_type_webpage'):
                                $medias = $this->program->get_interaction_playlist_area_media_list($id, $area->id);
                                $xml .= $this->tab3 . sprintf('<Area id="%d" name="%s" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%" model="%d" zindex="%d">', $area->id, $area->name, ($area->x / $interaction->w) * 100, ($area->y / $interaction->h) * 100, ($area->w / $interaction->w) * 100, ($area->h / $interaction->h) * 100, $area->area_type, $area->id) . $this->sep;
                                $xml .= $this->tab3 . $this->tab . '<freerun>' . $this->sep;
                                if ($medias['total'] > 0) {
                                    foreach ($medias['data'] as $media) {
                                        $xml .= $this->tab3 . $this->tab . sprintf('<gi id="%d" image="%d_-7_7" duration="%s:00" refreshtime="00:%s" url="%s" type="7"></gi>', $media->id, $media->id, $media->duration, $media->updateF, $media->publish_url) . $this->sep;
                                    }
                                }
                                $xml .= $this->tab3 . $this->tab . '</freerun>' . $this->sep;
                                $xml .= $this->tab3 . '</Area>' . $this->sep;
                                break;
                            case $this->config->item('area_type_btn'):
                                error_reporting(E_ALL ^ E_NOTICE);
                                $setting = $this->program->get_interaction_area_btn_setting($area->id, $id);
                                $medias = $this->program->get_interaction_playlist_area_media_list($id, $area->id);
                                if ($setting->action == 1) { //跳转至指定页面 302
                                    $xml .= $this->tab3 . sprintf('<Area id="%d" name="%s" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%" model="%d" zindex="%d">', $area->id, $area->name, ($area->x / $interaction->w) * 100, ($area->y / $interaction->h) * 100, ($area->w / $interaction->w) * 100, ($area->h / $interaction->h) * 100, $area->area_type, $area->id) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . sprintf('<gi id="%d" name="%d" image="%d_%d_1_1" left="%01.2f%%" top="%01.2f%%" imgw="%01.2f%%" imgh="%01.2f%%" style="%d" action="302" target="%s">', $area->id, $area->name, $setting->show, $area->id, ($area->x / $interaction->w) * 100, ($area->y / $interaction->h) * 100, ($area->w / $interaction->w) * 100, ($area->h / $interaction->h) * 100, $setting->style, $setting->goal) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . sprintf('<imagestyle fillmode="%d"/>', 1) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . sprintf('<textstyle color="%s" family="%s" size="%s" style="%d" width="20%%" height="20%%"></textstyle>', $setting->color, $setting->font_family, $setting->font_size . 'px', $setting->style) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . '</gi>' . $this->sep;
                                    $xml .= $this->tab3 . '</Area>' . $this->sep;
                                }
                                if ($setting->action == 2) { //媒体区域播放 303
                                    $xml .= $this->tab3 . sprintf('<Area id="%d" name="%s" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%" model="%d" zindex="%d">', $area->id, $area->name, ($area->x / $interaction->w) * 100, ($area->y / $interaction->h) * 100, ($area->w / $interaction->w) * 100, ($area->h / $interaction->h) * 100, $area->area_type, $area->id) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . sprintf('<gi id="%d" name="%d" image="%d_%d_1_1" left="%01.2f%%" top="%01.2f%%" imgw="%01.2f%%" imgh="%01.2f%%" style="%d" action="303" target="%s">', $area->id, $area->name, $setting->show, $area->id, ($area->x / $interaction->w) * 100, ($area->y / $interaction->h) * 100, ($area->w / $interaction->w) * 100, ($area->h / $interaction->h) * 100, $setting->style, $setting->goal) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . '<targetrun>' . $this->sep;
                                    if ($medias['total'] > 0) {
                                        foreach ($medias['data'] as $media) {
                                            $xml .= $this->tab3 . $this->tab . sprintf(
                                                '<gi target="%d_%d_1" transmode="%d" duration="00:%s" fillmode="1"/>',
                                                $media->id,
                                                $area->id, /*$media->transmode*/
                                                23,
                                                $media->duration
                                            ) . $this->sep;
                                            $resources .= $this->tab2 . sprintf('<fi id="%d_%d_1" mode="1" model="1" name="%s" path="%s" size="%d" signature="%s" fid="%d_%d_1"></fi>', $media->id, $area->id, $media->name, $media->full_path, $media->file_size, $media->signature, $media->id, $area->id) . $this->sep;
                                            $this->program->update_interaction_area_media(array(
                                                'publish_url' => $media->full_path
                                            ), $media->id);
                                        }
                                    }
                                    $xml .= $this->tab3 . $this->tab . '</targetrun>' . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . sprintf('<imagestyle fillmode="%d"/>', 1) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . sprintf('<textstyle color="%s" family="%s" size="%s" style="%d" width="20%%" height="20%%"></textstyle>', $setting->color, $setting->font_family, $setting->font_size . 'px', $setting->style) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . '</gi>' . $this->sep;
                                    $xml .= $this->tab3 . '</Area>' . $this->sep;
                                }
                                if ($setting->action == 3) { //指定位置打开网页  401
                                    $url_goal = $setting->goal;
                                    if (!strstr($url_goal, '://')) {
                                        $url_goal = 'http://' . $url_goal;
                                    }
                                    $xml .= $this->tab3 . sprintf('<Area id="%d" name="%s" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%" model="%d" zindex="%d">', $area->id, $area->name, ($area->x / $interaction->w) * 100, ($area->y / $interaction->h) * 100, ($area->w / $interaction->w) * 100, ($area->h / $interaction->h) * 100, $area->area_type, $area->id) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . sprintf('<gi id="%d" name="%d" image="%d_%d_1_1" left="%01.2f%%" top="%01.2f%%" imgw="%01.2f%%" imgh="%01.2f%%" style="%d" action="401" target="%s" fullscreen="%d" closebutton="%d" interval="%s">', $area->id, $area->name, $setting->show, $area->id, ($area->x / $interaction->w) * 100, ($area->y / $interaction->h) * 100, ($area->w / $interaction->w) * 100, ($area->h / $interaction->h) * 100, $setting->style, $url_goal, $setting->fullScreen, $setting->closeFlag, $setting->timeout) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . sprintf('<imagestyle fillmode="%d"/>', 1) . $this->sep;
                                    $xml .= $this->tab3 . $this->tab . sprintf('<textstyle color="%s" family="%s" size="%s" style="%d" width="20%%" height="20%%"></textstyle>', $setting->color, $setting->font_family, $setting->font_size . 'px', $setting->style) . $this->sep;
                                    if ($setting->fullScreen == 2) {
                                        $xml .= $this->tab3 . $this->tab . sprintf('<target x="%01.2f%%" y="%01.2f%%" w="%01.5f%%" h="%01.5f%%"/>', ($setting->x / ($interaction->w * 2)) * 100, ($setting->y / ($interaction->h * 2)) * 100, ($setting->w / ($interaction->w * 2)) * 100, ($setting->h / ($interaction->h * 2)) * 100) . $this->sep;
                                    }
                                    $xml .= $this->tab3 . '</gi>' . $this->sep;
                                    $xml .= $this->tab3 . '</Area>' . $this->sep;
                                }
                                $mediaShow = '';
                                $this->load->model('material');
                                $mediaShow = $this->material->get_media($setting->show);
                                if ($mediaShow) {
                                    $resources .= $this->tab2 . sprintf('<fi id="%d_%d_1_1" mode="1" model="1" name="%s" path="%s" size="%d" signature="%s" fid="%d_%d_1_1"></fi>', $setting->show, $area->id, $mediaShow->name, $mediaShow->full_path, $mediaShow->file_size, $mediaShow->signature, $setting->show, $area->id) . $this->sep;
                                }
                                break;
                            case $this->config->item('area_type_image'):
                                $medias = $this->program->get_interaction_playlist_area_media_list($id, $area->id);
                                $xml .= $this->tab3 . sprintf('<Area id="%d" name="%s" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%" model="%d" zindex="%d">', $area->id, $area->name, ($area->x / $interaction->w) * 100, ($area->y / $interaction->h) * 100, ($area->w / $interaction->w) * 100, ($area->h / $interaction->h) * 100, $area->area_type, $area->id) . $this->sep;
                                $xml .= $this->tab3 . $this->tab . '<freerun>' . $this->sep;
                                if ($medias['total'] > 0) {
                                    foreach ($medias['data'] as $media) {
                                        $xml .= $this->tab3 . $this->tab . sprintf('<gi id="%d" image="%d_%d_1" transmode="%d" duration="00:%s" fillmode="1"></gi>', $media->id, $media->id, $area->id, $media->transmode, $media->duration) . $this->sep;
                                        $resources .= $this->tab2 . sprintf('<fi id="%d_%d_1" mode="1" model="1" name="%s" path="%s" size="%d" signature="%s" fid="%d_%d_1"></fi>', $media->id, $area->id, $media->name, $media->full_path, $media->file_size, $media->signature, $media->id, $area->id) . $this->sep;
                                        $this->program->update_interaction_area_media(array(
                                            'publish_url' => $media->full_path
                                        ), $media->id);
                                    }
                                }
                                $xml .= $this->tab3 . $this->tab . '</freerun>' . $this->sep;
                                $xml .= $this->tab3 . '</Area>' . $this->sep;
                                break;
                            case $this->config->item('area_type_movie'):
                                $medias = $this->program->get_interaction_playlist_area_media_list($id, $area->id);
                                $xml .= $this->tab3 . sprintf('<Area id="%d" name="%s" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%" model="%d" zindex="%d">', $area->id, $area->name, ($area->x / $interaction->w) * 100, ($area->y / $interaction->h) * 100, ($area->w / $interaction->w) * 100, ($area->h / $interaction->h) * 100, $area->area_type, $area->id) . $this->sep;
                                $xml .= $this->tab3 . $this->tab . '<mediawin left="0%" top="0%" width="100%" height="100%">' . $this->sep;
                                $xml .= $this->tab3 . $this->tab . '<freerun>' . $this->sep;
                                if ($medias['total'] > 0) {
                                    foreach ($medias['data'] as $media) {
                                        $xml .= $this->tab3 . $this->tab . sprintf('<gi id="%d" image="%d_%d_1" transmode="%d" duration="00:%s" fillmode="1" startdate="" enddate="" starttime="" cleartime=""></gi>', $media->id, $media->id, $area->id, $media->transmode, $media->duration) . $this->sep;
                                        $resources .= $this->tab2 . sprintf('<fi id="%d_%d_1" mode="0" model="0" name="%s" path="%s" size="%d" signature="%s" fid="%d_%d_1"></fi>', $media->id, $area->id, $media->name, $media->full_path, $media->file_size, $media->signature, $media->id, $area->id) . $this->sep;
                                        $this->program->update_interaction_area_media(array(
                                            'publish_url' => $media->full_path
                                        ), $media->id);
                                    }
                                }
                                $xml .= $this->tab3 . $this->tab . '</freerun>' . $this->sep;
                                $xml .= $this->tab3 . $this->tab . '</mediawin>' . $this->sep;
                                $xml .= $this->tab3 . '</Area>' . $this->sep;
                                break;
                        }
                    }
                }
                if ($areasList) {
                    $xml .= $this->tab2 . '</Page>' . $this->sep;
                }
            }
            $xml .= $this->tab . '</Programme>' . $this->sep;
            $xml .= $resources;

            $xml .= $this->tab . '</resource>' . $this->sep;
            $xml .= '</SignwayPoster>' . chr(10);

            $playlist_path = $this->config->item('playlist_publish_path') . $this->get_cid();
            if (!file_exists($playlist_path)) {
                mkdir($playlist_path, 0777, true);
            }

            $playlist_path .= '/touch' . $id . '.PLS';
            saveFile($playlist_path, $xml);

            //update file_size&signature
            $file_size = filesize($playlist_path);
            $signature = md5_file($playlist_path);
            $updates = array('file_size' => $file_size, 'signature' => $signature, 'published' => $this->config->item('playlist.status.published'));
            if ($fail_count > 0) {
                $msg = sprintf($this->lang->line('playlist.publish.part.success'), $fail_count, $fail_media);
            } else {
                $this->program->update_interaction_playlist($updates, $id);
                //更新日程最新发布时间
                $this->program->update_interaction_schedule_by_pid($id);
                $msg = $this->lang->line('playlist.publish.success');
            }
        } else {
            $code = 1;
            $msg = $this->lang->line("playlist.error.not.exist");
        }

        echo json_encode(array('code' => $code, 'msg' => $msg));
    }

    /**
     * 获取默认的文本设置
     *
     * @param object $playlist_id
     * @param object $area_id
     * @return
     */
    private function get_default_text_setting($playlist_id, $area_id)
    {
        $setting = $this->lang->line('text.setting.default');
        $setting['interaction_playlist_id'] = $playlist_id;
        $setting['area_id'] = $area_id;
        return $setting;
    }

    /**
     * 获取默认的静态文本设置
     *
     * @param object $playlist_id
     * @param object $area_id
     * @return
     */
    private function get_default_static_text_setting($playlist_id, $area_id)
    {
        $setting = $this->lang->line('static.text.setting.default');
        $setting['interaction_playlist_id'] = $playlist_id;
        $setting['area_id'] = $area_id;

        return $setting;
    }

    /**
     * 获取媒体列表信息
     * @return
     */
    public function media_panel()
    {
        $bmp = $this->input->get('bmp');
        $screenId = $this->input->get('screenId');
        $media_type = $this->input->get('media_type');
        if ($media_type == $this->config->item('media_type_rss')) {
            $this->media_panel_rss();
        } else {
            if ($media_type == $this->config->item('media_type_webpage')) {
                $this->media_panel_webpage();
            } else {
                $filter_type = $this->input->get('filter_type');
                $order_item = $this->input->get('order_item');
                $order = $this->input->get('order');
                if ($order_item === false) {
                    $order_item = 'id';
                    $order = 'desc';
                }
                if ($order === false) {
                    $order = 'desc';
                }



                $this->load->model('membership');



                $data  = $this->get_folders_and_media($this->config->item('media_type_video'), $curpage, $order_item, $order);

                // $data['data'] = $medias['data'];
                // $data['total'] = $medias['total'];
                // $data['bmp'] = $bmp;
                $area = $this->program->get_one_interaction_area($area_id);
                $data['area'] = $area;
                $tip_msg = '';
                if ($area->area_type == $this->config->item('area_type_bg')) {
                    $tip_msg = sprintf($this->lang->line('warn.area.media.limit'), $area->name);
                }

                $data['tip_msg'] = $tip_msg;
                $data['playlist_id'] = $playlist_id;
                $data['area_id'] = $area_id;
                $data['media_type'] = $media_type;
                $data['limit'] = $limit;
                $data['curpage'] = $curpage;
                $data['order_item'] = $order_item;
                $data['order'] = $order;
                $data['type'] = $settings->media_view;
                $data['flag'] = $flag;
                $data['screenId'] = $screenId;

                $data['users'] = $this->membership->get_all_user_list($this->get_cid());
                // $data['folders'] = $this->material->get_all_folder_list($this->get_cid(), $folders_arr, false);
                //campaign/加档的时候，保存folder之前的选择
                $data['folder'] = $settings->playlist_media_folder;

                if ($settings->media_view == $this->config->item('media_layout_grid')) {
                    $data['body_view'] = 'program/pls/media_panel_grid';
                } else {
                    $data['body_view'] = 'program/pls/media_panel_list';
                }

                $this->load->view('program/pls/media_panel', $data);
            }
        }
    }

    public function show_media_panel()
    {
        $bmp = $this->input->get('bmp');
        $media_type = $this->input->get('media_type');
        if ($media_type == $this->config->item('media_type_rss')) {
            $this->media_panel_rss();
        } else {
            if ($media_type == $this->config->item('media_type_webpage')) {
                $this->media_panel_webpage();
            } else {
                $filter_type = $this->input->get('filter_type');
                $order_item = $this->input->get('order_item');
                $order = $this->input->get('order');
                if ($order_item === false) {
                    $order_item = 'id';
                    $order = 'desc';
                }
                if ($order === false) {
                    $order = 'desc';
                }
                $filter = $this->input->get('filter');
                if ($filter) {
                    $filter = trim($filter);
                }
                $folder_id = $this->input->get('folder_id');
                $user_id = $this->input->get('uid');
                $filter_array = array($filter_type => $filter, 'folder_id' => $folder_id, 'add_user_id' => $user_id);

                $this->load->model('material');
                $this->load->model('device');
                $this->load->model('program');
                $this->load->model('membership');

                $folders_arr = array();
                $folders = $this->device->get_folder_ids($this->get_uid());  //获取用户分配的文件夹
                if (is_array($folders)) {
                    for ($i = 0; $i < count($folders); $i++) {
                        $folders_arr[] = $folders[$i];
                    }
                }
                if (empty($folders)) {
                    if ($this->get_auth() == $this->config->item('auth_group') || $this->get_auth() == $this->config->item('auth_franchise')) {
                        $folders_arr[] = 0;
                    }
                }
                $settings = $this->membership->get_user_settings($this->get_uid());

                $playlist_id = $this->input->get('playlist_id');
                $area_id = $this->input->get('area_id');
                $pls_type_flag = 'np200';
                $curpage = $this->input->get('curpage');
                if ($curpage == false) {
                    $curpage = 1;
                }

                $limit = $this->config->item('area_media_size');
                $offset = ($curpage - 1) * $limit;

                $cid = $this->get_cid();
                $data = $this->get_data();
                //查询用户是否被分配root文件夹
                $flag = $this->device->get_rootFolder_id($this->get_uid());

                $auth = $this->get_auth(); //获取用户的权限
                if ($auth == $this->config->item('auth_admin')) {
                    $medias = $this->material->get_media_list($this->get_cid(), $media_type, $bmp, $offset, $limit, $order_item, $order, $filter_array, $folder_id, false, $pls_type_flag);
                }
                if ($auth == $this->config->item('auth_group') || $auth == $this->config->item('auth_franchise')) {
                    $cid = $this->get_cid(); //公司id
                    $admin_id = $this->membership->get_admin_by_cid($cid); //根据公司id获取Admin的id
                    $uid = $this->get_uid(); //用户id
                    $group_userId = $this->device->get_all_user_by_usergroup($uid); //获取用户所在的组 的所有用户
                    $medias = $this->material->group_manager_get_media_list($cid, $media_type, $bmp, $admin_id, $uid, $group_userId, $offset, $limit, $order_item, $order, $filter_array, $folder_id, $folders, $pls_type_flag);
                }

                $data['data'] = $medias['data'];
                $data['total'] = $medias['total'];
                $data['bmp'] = $bmp;
                $area = $this->program->get_one_interaction_area($area_id);
                $data['area'] = $area;
                $tip_msg = '';
                if ($area->area_type == $this->config->item('area_type_bg')) {
                    $tip_msg = sprintf($this->lang->line('warn.area.media.limit'), $area->name);
                }

                $data['tip_msg'] = $tip_msg;
                $data['playlist_id'] = $playlist_id;
                $data['area_id'] = $area_id;
                $data['media_type'] = $media_type;
                $data['limit'] = $limit;
                $data['curpage'] = $curpage;
                $data['order_item'] = $order_item;
                $data['order'] = $order;
                $data['type'] = $settings->media_view;
                $data['flag'] = $flag;

                $data['users'] = $this->membership->get_all_user_list($this->get_cid());
                $data['folders'] = $this->material->get_all_folder_list($this->get_cid(), $folders_arr, false);
                //campaign/加档的时候，保存folder之前的选择
                $data['folder'] = $settings->playlist_media_folder;

                if ($settings->media_view == $this->config->item('media_layout_grid')) {
                    $data['body_view'] = 'program/pls/show_media_panel_grid';
                } else {
                    $data['body_view'] = 'program/pls/show_media_panel_list';
                }

                $this->load->view('program/pls/show_media_panel', $data);
            }
        }
    }

    public function show_media_panel_filter()
    {
        $this->load->model('material');
        $this->load->model('device');
        $this->load->model('program');
        $this->load->model('membership');
        $order_item = $this->input->get('order_item');
        $bmp = $this->input->get('bmp');
        $order = $this->input->get('order');
        if ($order_item === false) {
            $order_item = 'id';
            $order = 'desc';
        }
        if ($order === false) {
            $order = 'desc';
        }
        $settings = $this->membership->get_user_settings($this->get_uid());

        $type = $this->input->get('type');
        if ($type === false) {
            $type = $settings->media_view;
        } else {
            if ($type != $settings->media_view) {
                $this->membership->update_user_settings($this->get_uid(), array('media_view' => $type));
                $settings->media_view = $type;
            }
        }
        $folders_arr = array();
        $folders = $this->device->get_folder_ids($this->get_uid());  //获取用户分配的文件夹
        if (is_array($folders)) {
            for ($i = 0; $i < count($folders); $i++) {
                $folders_arr[] = $folders[$i];
            }
        }
        if (empty($folders)) {
            if ($this->get_auth() == $this->config->item('auth_group') || $this->get_auth() == $this->config->item('auth_franchise')) {
                $folders_arr[] = 0;
            }
        }

        $filter_type = $this->input->get('filter_type');
        $filter = $this->input->get('filter');
        if ($filter) {
            $filter = trim($filter);
        }
        $folder_id = $this->input->get('folder_id');
        $user_id = $this->input->get('uid');
        $filter_array = array($filter_type => $filter, 'add_user_id' => $user_id);
        if ($folder_id == '') {
            $this->membership->update_user_settings($this->get_uid(), array('playlist_media_folder' => -1));
        } else {
            $this->membership->update_user_settings($this->get_uid(), array('playlist_media_folder' => $folder_id));
        }


        $playlist_id = $this->input->get('playlist_id');
        $area_id = $this->input->get('area_id');
        $media_type = $this->input->get('media_type');

        $curpage = $this->input->get('curpage');
        if ($curpage == false) {
            $curpage = 1;
        }

        $limit = $this->config->item('area_media_size');
        $offset = ($curpage - 1) * $limit;

        $cid = $this->get_cid();
        $data = $this->get_data();
        $this->load->model('device');
        $auth = $this->get_auth(); //获取用户的权限
        if ($auth == $this->config->item('auth_admin')) {
            $medias = $this->material->get_media_list($this->get_cid(), $media_type, $bmp, $offset, $limit, $order_item, $order, $filter_array, $folder_id, false, false);
        }
        if ($auth == $this->config->item('auth_group') || $auth == $this->config->item('auth_franchise')) {
            $cid = $this->get_cid(); //公司id
            $admin_id = $this->membership->get_admin_by_cid($cid); //根据公司id获取Admin的id
            $uid = $this->get_uid(); //用户id
            $group_userId = $this->device->get_all_user_by_usergroup($uid); //获取用户所在的组 的所有用户
            $medias = $this->material->group_manager_get_media_list($cid, $media_type, $bmp, $admin_id, $uid, $group_userId, $offset, $limit, $order_item, $order, $filter_array, $folder_id, $folders, false);
        }

        $data['data'] = $medias['data'];
        $data['total'] = $medias['total'];
        $area = $this->program->get_one_interaction_area($area_id);
        $data['area'] = $area;
        $tip_msg = '';
        if ($area->area_type == $this->config->item('area_type_bg') || $area->area_type == $this->config->item('area_type_logo')) {
            $tip_msg = sprintf($this->lang->line('warn.area.media.limit'), $area->name);
        }
        $data['bmp'] = $bmp;
        $data['tip_msg'] = $tip_msg;
        $data['playlist_id'] = $playlist_id;
        $data['area_id'] = $area_id;
        $data['media_type'] = $media_type;
        $data['limit'] = $limit;
        $data['curpage'] = $curpage;
        $data['order_item'] = $order_item;
        $data['order'] = $order;
        $data['type'] = $settings->media_view;
        $data['folders'] = $this->material->get_all_folder_list($this->get_cid(), $folders_arr, false);

        if ($settings->media_view == $this->config->item('media_layout_grid')) {
            $data['body_view'] = 'program/pls/show_media_panel_grid';
        } else {
            $data['body_view'] = 'program/pls/show_media_panel_list';
        }

        $this->load->view($data['body_view'], $data);
    }

    public function do_save_media_check()
    {
        $code = 0;
        $msg = '';
        $media_type = $this->input->post('media_type');
        $playlist_id = $this->input->post('playlist_id');
        $area_id = $this->input->post('area_id');
        if ($media_type == $this->config->item('media_type_rss')) {
            $this->load->model('program');
            $flag = array($this->config->item('area_media_flag_tmp'), $this->config->item('area_media_flag_ok'));
            $count = $this->program->get_interaction_playlist_area_media_count($playlist_id, $area_id, $flag);
            if ($count > 0) {
                $code = 1;
                $msg = $this->lang->line('warn.rss.limit');
            }
        }

        echo json_encode(array('code' => $code, 'msg' => $msg));
    }

    /**
     * 保存播放列表区域中的媒体文件
     *
     * @return
     */
    public function do_save_media()
    {
        $uid = $this->get_uid();
        $result = array();
        $playlist_id = $this->input->post('playlist_id');
        $area_id = $this->input->post('area_id');
        $medias = $this->input->post('medias');
        $media_type = $this->input->post('media_type');

        if ($media_type == $this->config->item('media_type_rss') && count($medias) != 1) {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('playlist.error.rss.media.num');
            echo json_encode($result);
            return;
        }
        $result['medias'] = $medias;
        $result['playlist_id'] = $playlist_id;
        $result['area_id'] = $area_id;
        if (!$playlist_id || !$area_id) {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('param.error');
        } elseif (!$medias) {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('playlist.error.media.empty');
        } else {
            $this->load->model('program');
            $transmode = 26;
            $transtime = 0.5;
            $duration = '00:10';
            $area_type = $this->program->get_interaction_area_type($area_id);
            if ($area_type == $this->config->item('area_type_image')) {
                $transmode = 0;
            }
            $rotate = 1;

            $area = $this->program->get_one_interaction_area($area_id);
            if ($area->area_type == $this->config->item('area_type_bg') || $area->area_type == $this->config->item('area_type_logo')) {
                $flag = $this->config->item('area_media_flag_all');
                $count = $this->program->get_interaction_playlist_area_media_count($playlist_id, $area_id, $flag);
                if (count($medias) > 1) {
                    $result['msg'] = sprintf($this->lang->line('warn.area.media.limit'), $area->name);
                    $result['code'] = 1;
                    echo json_encode($result);
                    return;
                } else {
                    if ($count > 0 && count($medias) == 1) {
                        $this->program->delete_interaction_playlist_area_media($playlist_id, $area_id);
                    }

                    $transtime = -1;
                    $transmode = -1;
                    $duration = '00:00';
                }
            }

            $max_position = $this->program->get_playlist_area_media_max_position($playlist_id, $area_id);
            $max_position++;
            $media_ids = array();
            foreach ($medias as $media) {
                $id = $this->program->add_interaction_area_media(array('interaction_playlist_id' => $playlist_id, 'area_id' => $area_id, 'media_id' => $media, 'duration' => $duration, 'transmode' => $transmode, 'transtime' => $transtime, 'position' => $max_position, 'rotate' => $rotate), $uid);
                $max_position++;
                if ($id) {
                    $media_ids[] = $id;
                }
            }
            $result['media_ids'] = $media_ids;
            $result['code'] = 0;
            $result['msg'] = $this->lang->line('save.success');
        }

        echo json_encode($result);
    }

    public function do_save_show_media()
    {
        $uid = $this->get_uid();
        $result = array();
        $playlist_id = $this->input->post('playlist_id');
        $areaId = $this->input->post('area_id');
        $mediaId = $this->input->post('medias');
        $mediaType = $this->input->post('media_type');
        $array = array('show' => $mediaId);
        $this->load->model('program');
        $flag = $this->program->update_interaction_area_btn_show($array, $areaId, $playlist_id);
        if ($flag) {
            $this->load->model('material');
            $media = $this->material->get_media($mediaId);
            $media_name = $media->name;
            $result['code'] = 0;
            $result['name'] = $media_name;
            $result['id'] = $mediaId;
            $result['msg'] = $this->lang->line('save.success');
        } else {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('save.fail');
        }
        echo json_encode($result);
    }

    public function update_btn_setting()
    {
        $array = '';
        $pId = $this->input->post('pId');
        $areaId = $this->input->post('areaId');
        $action = $this->input->post('action');
        $goal = $this->input->post('goal');
        $goal_type = $this->input->post('goalType');
        if ($action) {
            $array = array('action' => $action);
        }
        if ($goal) {
            $array = array('goal' => $goal, 'goal_type' => $goal_type);
        }
        $this->load->model('program');
        $this->program->update_interaction_area_btn_show($array, $areaId, $pId);
    }
    //添加 播放列表中的 网页信息
    public function add_playlist_media_webpage()
    {
        $data = array();
        $area_id = $this->input->get('area_id');
        $playlist_id = $this->input->get('playlist_id');
        $media_type = $this->input->get('media_type');
        $data['area_id'] = $area_id;
        $data['playlist_id'] = $playlist_id;
        $data['media_type'] = $media_type;
        $this->load->view('program/pls/add_screen_area_webpage', $data);
    }
    //修改 播放列表中的 网页信息
    public function edit_playlist_media_webpage()
    {
        $id = $this->input->get('id');
        $area_id = $this->input->get('area_id');
        $media_id = $this->input->get('media_id');
        $this->load->model('program');
        $this->load->model('material');
        $media = $this->program->get_interaction_playlist_area_media($id);
        if ($media->starttime == '00:00') {
            $media->starttime = '';
        }
        if ($media->endtime == '00:00') {
            $media->endtime = '';
        }
        $data['media'] = $media;
        $data['media_type'] = $this->material->get_media_type($media->media_id);
        $this->load->view('program/pls/edit_screen_area_webpage', $data);
    }

    /**
     * 删除某个播放列表下某个区域的媒体文件信息
     *
     * @return
     */
    public function delete_media()
    {
        $code = 0;
        $msg = '';
        $playlist_id = $this->input->post('playlist_id');
        $area_id = $this->input->post('area_id');
        $id = $this->input->post('id');
        if ($playlist_id && $area_id && $id) {
            $this->load->model('program');
            $this->program->delete_interaction_playlist_area_media($playlist_id, $area_id, $id, $this->config->item('area_media_flag_temp'));
            //just delete template
            $this->program->update_interaction_media_flag($id, $this->config->item('area_media_flag_delete'));
            $msg = $this->lang->line('delete.success');
        } else {
            $code = 1;
            $msg = $this->lang->line('warn.param');
        }

        echo json_encode(array('code' => $code, 'msg' => $msg));
    }

    /**
     * 删除批量的媒体ID
     * @return
     */
    public function delete_all_media()
    {
        $playlist_id = $this->input->post('playlist_id');
        $area_id = $this->input->post('area_id');
        $ids = $this->input->post('ids');
        $result['ids'] = $ids;
        $result['playlist_id'] = $playlist_id;
        $result['area_id'] = $area_id;
        if (!$playlist_id || !$area_id) {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('param.error');
        } elseif (!$ids) {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('playlist.error.media.empty');
        } else {
            $this->load->model('program');
            $this->program->delete_interaction_playlist_area_media($playlist_id, $area_id, $ids);
            $result['msg'] = $this->lang->line('delete.success');
            $result['code'] = 0;
        }

        echo json_encode($result);
    }

    /**
     * 保存播放列表设置
     *
     * @return
     */
    public function do_save($publish = false)
    {
        $uid = $this->get_uid();
        $playlist_id = $this->input->post('playlist_id');
        $playlist = $this->input->post('playlist');
        $text = $this->input->post('text');
        $staticText = $this->input->post('staticText');
        $date = $this->input->post('date');
        $time = $this->input->post('time');
        $weather = $this->input->post('weather');
        $btns = $this->input->post('areaBtn');
        //删除的媒体文件
        $deletes = $this->input->post('deletes');
        //需要将状态改变的
        $ids = $this->input->post('ids');
        $result = array();

        $this->load->model('program');
        if ($playlist || $text || $staticText || $deletes || $ids) {
            $this->load->model('material');
            $this->load->library('image');

            //delete
            if ($deletes && !empty($deletes)) {
                $this->program->delete_interaction_media($deletes);
                $result['deletes'] = $deletes;
            }

            //add media
            if ($ids && !empty($ids)) {
                $this->program->update_interaction_media_flag($ids, $this->config->item('area_media_flag_ok'));
                $result['ids'] = $ids;
            }

            //delete deleted
            $this->program->delete_interaction_playlist_area_media_temp($playlist_id, $this->config->item('area_media_flag_delete'));

            //update playlist
            if ($playlist && !empty($playlist)) {
                $name = $playlist['name'];
                if (empty($name)) {
                    $name = $this->lang->line('playlist') . '-' . $playlist_id;
                }
                $descr = $playlist['descr'];
                $template_id = $playlist['template_id'];
                $data = array('name' => $name, 'descr' => $descr, 'interaction_id' => $template_id, 'update_time' => date('Y-m-d H:i:s'));
                $data['published'] = $this->config->item('playlist.status.default');
                $this->program->update_interaction_playlist($data, $playlist_id);
            }

            if ($staticText && !empty($staticText)) {
                //文本区域
                $id = $staticText['id'];
                $area_id = $staticText['area_id'];
                $content = $staticText['content'];
                $content = str_replace('\'', '&#039', $content);
                $htmlc = $staticText['htmlc'];
                $htmlc = str_replace('\'', '&#039', $htmlc);
                $color = $staticText['color'];
                $bg_color = $staticText['bg_color'];
                $data = array('area_id' => $area_id, 'interaction_playlist_id' => $playlist_id, 'content' => $content, 'html_content' => $htmlc, 'color' => $color, 'bg_color' => $bg_color);
                if (empty($id)) {
                    //insert
                    $id = $this->program->add_interaction_area_static_text_setting($data, $uid);
                } else {
                    $this->program->update_interaction_area_static_text_setting($data, $id);
                }
                $data['id'] = $id;
                $result['staticText'] = $data;
                unset($staticText);
            }

            if ($text && !empty($text)) {
                //文本区域
                $id = $text['id'];
                $area_id = $text['area_id'];
                $content = $text['content'];
                $font_size = $text['font_size'];
                $color = $text['color'];
                //$font_family = $text['font_family'];
                $font = $text['font'];
                $bg_color = $text['bg_color'];
                $speed = $text['speed'];
                $direction = $text['direction'];
                $duration = '00:05'; //$text['duration'];
                $transparent = $text['transparent'];
                $rssFormat = $text['rssFormat'];
                $data = array(
                    'area_id' => $area_id, 'interaction_playlist_id' => $playlist_id, 'content' => $content, 'font_size' => $font_size, 'font' => $font,
                    //'font_family'=>$font_family,
                    'color' => $color, 'bg_color' => $bg_color, 'speed' => $speed, 'direction' => $direction, 'duration' => $duration, 'transparent' => $transparent, 'rss_format' => $rssFormat
                );
                if (empty($id)) {
                    //insert
                    $id = $this->program->add_interaction_area_text_setting($data, $uid);
                } else {
                    $this->program->update_interaction_area_text_setting($data, $id);
                }
                $data['id'] = $id;
                $result['text'] = $data;
                unset($text);
            }

            if ($date) {
                $ds = array('color' => $date['color'], 'bg_color' => $date['bg_color'], 'font_size' => $date['font_size'], 'style' => $date['style'], 'transparent' => $date['transparent'], 'language' => $date['language']);
                $this->program->update_interaction_area_time_setting($ds, $date['id']);
            }

            if ($time) {
                $ds = array('color' => $time['color'], 'bg_color' => $time['bg_color'], 'font_size' => $time['font_size'], 'style' => $time['style'], 'transparent' => $time['transparent']);
                $this->program->update_interaction_area_time_setting($ds, $time['id']);
            }

            if ($weather) {
                $ds = array('color' => $weather['color'], 'bg_color' => $weather['bg_color'], 'font_size' => $weather['font_size'], 'style' => $weather['style'], 'transparent' => $weather['transparent'], 'language' => $weather['language']);
                $this->program->update_interaction_area_weather_setting($ds, $weather['id']);
            }

            if (!empty($btns)) {
                foreach ($btns as $btn) {
                    $d = json_decode($btn, true);
                    if ($d) {
                        $sid = $this->get_value($d, 'sid');
                        $dv['w'] = $d['w'];
                        $dv['h'] = $d['h'];
                        $dv['x'] = $d['x'];
                        $dv['y'] = $d['y'];
                        //$dv['area_id'] = $d['areaId'];
                        $dv['action'] = $d['action'];
                        $dv['goal'] = $d['goal'];
                        $dv['style'] = $d['style'];
                        $dv['show'] = $d['show'];
                        $dv['fullScreen'] = $d['fullScreen'];
                        $dv['closeFlag'] = $d['closeFlag'];
                        $dv['timeout'] = $d['timeout'];
                        $this->program->update_interaction_area_btn_setting($dv, $sid);
                    }
                }
            }

            //删除临时数据
            $this->program->update_interaction_area_media_commit($playlist_id);
            $result['refresh_url'] = true;

            $result['code'] = 0;
            $result['msg'] = $this->lang->line('save.success');
        } else {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('playlist.error.param.empty');
        }

        if ($publish) {
            return $result;
        } else {
            echo json_encode($result);
        }
    }

    public function do_save_one_screen()
    {
        $uid = $this->get_uid();
        $playlist_id = $this->input->post('playlist_id');
        $playlist = $this->input->post('playlist');
        $text = $this->input->post('text');
        $staticText = $this->input->post('staticText');
        $date = $this->input->post('date');
        $time = $this->input->post('time');
        $weather = $this->input->post('weather');
        $btns = $this->input->post('areaBtn');
        //删除的媒体文件
        $deletes = $this->input->post('deletes');
        //需要将状态改变的
        $ids = $this->input->post('ids');
        $result = array();

        $this->load->model('program');
        if ($playlist || $text || $staticText || $deletes || $ids) {
            $this->load->model('material');
            $this->load->library('image');

            //delete
            if ($deletes && !empty($deletes)) {
                $this->program->delete_interaction_media($deletes);
                $result['deletes'] = $deletes;
            }

            //add media
            if ($ids && !empty($ids)) {
                $this->program->update_interaction_media_flag($ids, $this->config->item('area_media_flag_ok'));
                $result['ids'] = $ids;
            }

            //delete deleted
            $this->program->delete_interaction_playlist_area_media_temp($playlist_id, $this->config->item('area_media_flag_delete'));

            //update playlist
            if ($playlist && !empty($playlist)) {
                $name = $playlist['name'];
                if (empty($name)) {
                    $name = $this->lang->line('playlist') . '-' . $playlist_id;
                }
                $descr = $playlist['descr'];
                $template_id = $playlist['template_id'];
                $data = array('name' => $name, 'descr' => $descr, 'interaction_id' => $template_id, 'update_time' => date('Y-m-d H:i:s'));
                //$data['published'] = $this->config->item('playlist.status.default');
                $this->program->update_interaction_playlist($data, $playlist_id);
            }

            if ($staticText && !empty($staticText)) {
                //文本区域
                $id = $staticText['id'];
                $area_id = $staticText['area_id'];
                $content = $staticText['content'];
                $htmlc = $staticText['htmlc'];
                $color = $staticText['color'];
                $bg_color = $staticText['bg_color'];
                $data = array('area_id' => $area_id, 'interaction_playlist_id' => $playlist_id, 'content' => $content, 'html_content' => $htmlc, 'color' => $color, 'bg_color' => $bg_color);
                if (empty($id)) {
                    //insert
                    $id = $this->program->add_interaction_area_static_text_setting($data, $uid);
                } else {
                    $this->program->update_interaction_area_static_text_setting($data, $id);
                }
                $data['id'] = $id;
                $result['staticText'] = $data;
                unset($staticText);
            }

            if ($text && !empty($text)) {
                //文本区域
                $id = $text['id'];
                $area_id = $text['area_id'];
                $content = $text['content'];
                $font_size = $text['font_size'];
                $color = $text['color'];
                //$font_family = $text['font_family'];
                $font = $text['font'];
                $bg_color = $text['bg_color'];
                $speed = $text['speed'];
                $direction = $text['direction'];
                $duration = '00:05'; //$text['duration'];
                $transparent = $text['transparent'];
                $rssFormat = $text['rssFormat'];
                $data = array(
                    'area_id' => $area_id, 'interaction_playlist_id' => $playlist_id, 'content' => $content, 'font_size' => $font_size, 'font' => $font,
                    //'font_family'=>$font_family,
                    'color' => $color, 'bg_color' => $bg_color, 'speed' => $speed, 'direction' => $direction, 'duration' => $duration, 'transparent' => $transparent, 'rss_format' => $rssFormat
                );
                if (empty($id)) {
                    //insert
                    $id = $this->program->add_interaction_area_text_setting($data, $uid);
                } else {
                    $this->program->update_interaction_area_text_setting($data, $id);
                }
                $data['id'] = $id;
                $result['text'] = $data;
                unset($text);
            }

            if ($date) {
                $ds = array('color' => $date['color'], 'bg_color' => $date['bg_color'], 'font_size' => $date['font_size'], 'style' => $date['style'], 'transparent' => $date['transparent'], 'language' => $date['language']);
                $this->program->update_interaction_area_time_setting($ds, $date['id']);
            }

            if ($time) {
                $ds = array('color' => $time['color'], 'bg_color' => $time['bg_color'], 'font_size' => $time['font_size'], 'style' => $time['style'], 'transparent' => $time['transparent']);
                $this->program->update_interaction_area_time_setting($ds, $time['id']);
            }

            if ($weather) {
                $ds = array('color' => $weather['color'], 'bg_color' => $weather['bg_color'], 'font_size' => $weather['font_size'], 'style' => $weather['style'], 'transparent' => $weather['transparent'], 'language' => $weather['language']);
                $this->program->update_interaction_area_weather_setting($ds, $weather['id']);
            }

            if (!empty($btns)) {
                foreach ($btns as $btn) {
                    $d = json_decode($btn, true);
                    if ($d) {
                        $sid = $this->get_value($d, 'sid');
                        $dv['w'] = $d['w'];
                        $dv['h'] = $d['h'];
                        $dv['x'] = $d['x'];
                        $dv['y'] = $d['y'];
                        //$dv['area_id'] = $d['areaId'];
                        $dv['action'] = $d['action'];
                        $dv['goal'] = $d['goal'];
                        $dv['style'] = $d['style'];
                        $dv['show'] = $d['show'];
                        $dv['fullScreen'] = $d['fullScreen'];
                        $dv['closeFlag'] = $d['closeFlag'];
                        $dv['timeout'] = $d['timeout'];
                        $this->program->update_interaction_area_btn_setting($dv, $sid);
                    }
                }
            }

            //删除临时数据
            $this->program->update_interaction_area_media_commit($playlist_id);
            $result['refresh_url'] = true;

            $result['code'] = 0;
            $result['msg'] = $this->lang->line('save.success');
        } else {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('playlist.error.param.empty');
        }

        echo json_encode($result);
    }

    /**
     * 保存播放列表区域中的Webpage路径
     * @return
     */
    public function do_save_webpage_media()
    {
        $uid = $this->get_uid();
        $result = array();
        $playlist_id = $this->input->post('playlist_id');
        $area_id = $this->input->post('area_id');
        $medias = $this->input->post('medias');
        $media_type = $this->input->post('media_type');
        $startDate = $this->input->post('startDate');
        $endDate = $this->input->post('endDate');
        $url = $this->input->post('url');
        $playTime = $this->input->post('play_time');
        $updateF = $this->input->post('updateF');

        $result['medias'] = $medias;
        $result['playlist_id'] = $playlist_id;
        $result['area_id'] = $area_id;
        if (!$playlist_id || !$area_id) {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('param.error');
        } elseif (!$medias) {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('playlist.error.media.empty');
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('url', $this->lang->line('http.url'), 'trim|required');
            if ($this->form_validation->run() == false) {
                $result = array('code' => 1, 'msg' => validation_errors());
            } else {
                //$check = preg_match('/(http|https|ftp|file){1}(:\/\/)?([\da-z-\.]+)\.([a-z]{2,6})([\/\w \.-?&%-=]*)*\/?/',$url);
                $check = preg_match("/\b(([\w-]+:\/\/?)[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/", $url);
                if (!$check) {
                    $result['code'] = 1;
                    $result['msg'] = 'Invalid Url address: ' . $url;
                } else {
                    $this->load->model('program');
                    $transmode = 26;
                    $transtime = 0.5;
                    $duration = '00:10';
                    $area_type = $this->program->get_interaction_area_type($area_id);
                    if ($area_type == $this->config->item('area_type_image')) {
                        $transmode = 0;
                    }
                    $rotate = 1;

                    $area = $this->program->get_one_interaction_area($area_id);
                    $max_position = $this->program->get_interaction_playlist_area_media_max_position($playlist_id, $area_id);
                    $max_position++;
                    $media_ids = array();

                    $id = $this->program->add_interaction_area_media(array('interaction_playlist_id' => $playlist_id, 'area_id' => $area_id, 'media_id' => 0, 'duration' => $playTime, 'transmode' => $transmode, 'transtime' => $transtime, 'startdate' => $startDate, 'enddate' => $endDate, 'position' => $max_position, 'rotate' => $rotate, 'publish_url' => $url, 'updateF' => $updateF), $uid);
                    $max_position++;
                    if ($id) {
                        $media_ids[] = $id;
                    }
                    $result['media_ids'] = $media_ids;
                    $result['code'] = 0;
                    $result['msg'] = $this->lang->line('save.success');
                }
            }
        }
        echo json_encode($result);
    }

    public function media_panel_rss()
    {
        $this->load->model('material');
        $this->load->model('program');
        $this->load->model('membership');

        $playlist_id = $this->input->get('playlist_id');
        $area_id = $this->input->get('area_id');

        $rss_list = $this->material->get_all_rss_list($this->get_cid());
        $data = $this->get_data();
        $data['data'] = $rss_list;

        $data['media_type'] = $this->config->item('media_type_rss');

        $data['playlist_id'] = $playlist_id;
        $data['area_id'] = $area_id;
        $this->load->view('program/pls/media_panel_rss', $data);
    }

    /**
     * 切换RSS格式
     *
     * @return
     */
    public function do_change_rss_format()
    {
        $playlist_id = $this->input->post('playlist_id');
        $area_id = $this->input->post('area_id');
        $format = $this->input->post('format');
        $code = 0;
        $msg = '';
        $this->load->model('program');
        $playlist_settings = $this->program->get_interaction_playlist($playlist_id); //获取列表设置
        $rss_delimiter = $playlist_settings->rss_delimiter;     //rss分割标记
        //$rss_delimiter = "<<";     //rss分割标记
        $this->load->model('program');
        $rss = $this->program->get_interaction_playlist_area_last_rss($playlist_id, $area_id);
        $rss_content = '';
        if ($rss) {
            $this->load->library('rssparser');
            $rssObj = $this->rssparser->Get($rss->url, true, true);
            if ($rssObj) {
                $items = $rssObj['items'];
                for ($i = 0; $i < count($items); $i++) {
                    switch ($format) {
                        case $this->config->item('rss_format_title'):
                            $rss_content .= $items[$i]['title'];
                            if ($i < count($items) - 1) {
                                //$rss_content .= '<<';
                                $rss_content .= $rss_delimiter;
                            }
                            break;
                        case $this->config->item('rss_format_desc'):
                            if (isset($items[$i]['description'])) {
                                $rss_content .= $items[$i]['description'];
                            }
                            if ($i < count($items) - 1) {
                                //$rss_content .= '<<';
                                $rss_content .= $rss_delimiter;
                            }
                            break;
                        case $this->config->item('rss_format_all'):
                            $rss_content .= $items[$i]['title'];
                            //$rss_content .= '<<';
                            $rss_content .= $rss_delimiter;
                            if (isset($items[$i]['description'])) {
                                $rss_content .= $items[$i]['description'];
                            }
                            if ($i < count($items) - 1) {
                                //$rss_content .= '<<';
                                $rss_content .= $rss_delimiter;
                            }
                            break;
                    }
                }
            }
        }

        echo json_encode(array('code' => $code, 'msg' => $msg, 'rss_content' => $rss_content));
    }

    //修改rss的分割标记
    public function do_updateRssFlag()
    {
        $value = $this->input->post('val');
        $pid = $this->input->post('id');
        $this->load->model('program');
        $this->program->update_interaction_playlist(array('rss_delimiter' => $value), $pid);
    }

    public function media_panel_filter()
    {
        $this->load->model('material');
        $this->load->model('device');
        $this->load->model('program');
        $this->load->model('membership');
        $order_item = $this->input->get('order_item');
        $bmp = $this->input->get('bmp');
        $order = $this->input->get('order');
        if ($order_item === false) {
            $order_item = 'id';
            $order = 'desc';
        }
        if ($order === false) {
            $order = 'desc';
        }
        $settings = $this->membership->get_user_settings($this->get_uid());

        $type = $this->input->get('type');
        if ($type === false) {
            $type = $settings->media_view;
        } else {
            if ($type != $settings->media_view) {
                $this->membership->update_user_settings($this->get_uid(), array('media_view' => $type));
                $settings->media_view = $type;
            }
        }
        $folders_arr = array();
        $folders = $this->device->get_folder_ids($this->get_uid());  //获取用户分配的文件夹
        if (is_array($folders)) {
            for ($i = 0; $i < count($folders); $i++) {
                $folders_arr[] = $folders[$i];
            }
        }
        if (empty($folders)) {
            if ($this->get_auth() == $this->config->item('auth_group') || $this->get_auth() == $this->config->item('auth_franchise')) {
                $folders_arr[] = 0;
            }
        }

        $filter_type = $this->input->get('filter_type');
        $filter = $this->input->get('filter');
        if ($filter) {
            $filter = trim($filter);
        }
        $folder_id = $this->input->get('folder_id');
        $user_id = $this->input->get('uid');
        $filter_array = array($filter_type => $filter, 'add_user_id' => $user_id);
        if ($folder_id == '') {
            $this->membership->update_user_settings($this->get_uid(), array('playlist_media_folder' => -1));
        } else {
            $this->membership->update_user_settings($this->get_uid(), array('playlist_media_folder' => $folder_id));
        }


        $playlist_id = $this->input->get('playlist_id');
        $area_id = $this->input->get('area_id');
        $media_type = $this->input->get('media_type');

        $curpage = $this->input->get('curpage');
        if ($curpage == false) {
            $curpage = 1;
        }

        $limit = $this->config->item('area_media_size');
        $offset = ($curpage - 1) * $limit;

        $cid = $this->get_cid();
        $data = $this->get_data();
        //$media = $this->material->get_media_list($cid, $media_type, $offset, $limit, $order_item, $order, $filter_array);
        $this->load->model('device');
        $auth = $this->get_auth(); //获取用户的权限
        if ($auth == $this->config->item('auth_admin')) {
            $medias = $this->material->get_media_list($this->get_cid(), $media_type, $bmp, $offset, $limit, $order_item, $order, $filter_array, $folder_id, false, false);
        }
        if ($auth == $this->config->item('auth_group') || $auth == $this->config->item('auth_franchise')) {
            $cid = $this->get_cid(); //公司id
            $admin_id = $this->membership->get_admin_by_cid($cid); //根据公司id获取Admin的id
            $uid = $this->get_uid(); //用户id
            $group_userId = $this->device->get_all_user_by_usergroup($uid); //获取用户所在的组 的所有用户
            $medias = $this->material->group_manager_get_media_list($cid, $media_type, $bmp, $admin_id, $uid, $group_userId, $offset, $limit, $order_item, $order, $filter_array, $folder_id, $folders, false);
        }

        $data['data'] = $medias['data'];
        $data['total'] = $medias['total'];
        $area = $this->program->get_one_interaction_area($area_id);
        $data['area'] = $area;
        $tip_msg = '';
        if ($area->area_type == $this->config->item('area_type_bg') || $area->area_type == $this->config->item('area_type_logo')) {
            $tip_msg = sprintf($this->lang->line('warn.area.media.limit'), $area->name);
        }
        $data['bmp'] = $bmp;
        $data['tip_msg'] = $tip_msg;
        $data['playlist_id'] = $playlist_id;
        $data['area_id'] = $area_id;
        $data['media_type'] = $media_type;
        $data['limit'] = $limit;
        $data['curpage'] = $curpage;
        $data['order_item'] = $order_item;
        $data['order'] = $order;
        $data['type'] = $settings->media_view;
        $data['folders'] = $this->material->get_all_folder_list($this->get_cid(), $folders_arr, false);

        if ($settings->media_view == $this->config->item('media_layout_grid')) {
            $data['body_view'] = 'program/pls/media_panel_grid';
        } else {
            $data['body_view'] = 'program/pls/media_panel_list';
        }

        $this->load->view($data['body_view'], $data);
    }

    /**
     * 交换顺序
     *
     * @return
     */
    public function do_change_media_order()
    {
        $fid = $this->input->post('fid'); //first id
        $sid = $this->input->post('sid'); //second id
        $code = 0;
        $msg = '';
        if ($fid && $sid) {
            $this->load->model('program');
            if ($this->program->change_interaction_playlist_area_media_order($fid, $sid) === false) {
                $code = 1;
                $msg = $this->lang->line('warn.area.media.not.found');
            }
        } else {
            $code = 1;
            $msg = $this->lang->line('warn.param');
        }

        echo json_encode(array('code' => $code, 'msg' => $msg));
    }

    public function do_move_to()
    {
        $result = array();
        $id = $this->input->post('id');
        if ($id) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('index', $this->lang->line('move.to'), 'trim|required|numeric');

            if ($this->form_validation->run() == false) {
                //false
                $result = array('code' => 1, 'msg' => validation_errors());
            } else {
                $this->load->model('program');
                $index = $this->input->post('index');
                if ($this->program->interaction_playlist_area_media_move_to($id, $index)) {
                    $result = array('code' => 0, 'msg' => '');
                }
            }
        }

        if (empty($result)) {
            $result = array('code' => 1, 'msg' => $this->lang->line('warn.param'));
        }

        echo json_encode($result);
    }

    public function btn_do_move_to()
    {
        $result = array();
        $id = $this->input->post('id');
        $cid = $this->input->post('cid');
        $index = $this->input->post('index');
        //if ($cid && $index) {
        if ($cid) {
            $this->load->model('program');
            if ($this->program->interaction_playlist_area_media_move_to($cid, $index)) {
                $result = array('code' => 0, 'msg' => '');
            }
        }

        if (empty($result)) {
            $result = array('code' => 1, 'msg' => $this->lang->line('warn.param'));
        }
        echo json_encode($result);
    }

    /**
     * 修改播放列表中status
     *
     * @return
     */
    public function do_editStatus()
    {
        $result = array();
        $id = $this->input->post('id');
        $areaId = $this->input->post('areaId');
        $endTime = $this->input->post('endTime');

        $this->load->model('program');
        if ($this->program->interaction_edit_status(array('area_id' => $areaId), $id)) {
            $result = array('code' => 0, 'msg' => '');
        }
    }

    /*
     *
     * 编辑webpage属性
     */
    public function save_playlist_webpage()
    {
        $result = array();
        $cid = $this->get_cid();
        $id = $this->input->post('id');
        $url = $this->input->post('url');
        if ($cid <= 0) {
            $result = array('code' => 1, 'msg' => $this->lang->line('warn.system.user'));
        } else {

            //print_R($arr_url);
            //判断Url是否合法
            //$check = preg_match('/(http|https|ftp|file){1}(:\/\/)?([\da-z-\.]+)\.([a-z]{2,6})([\/\w \.-?&%-=]*)*\/?/',$url);
            $check = preg_match("/\b(([\w-]+:\/\/?)[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/", $url);
            if (!$check) {
                $code = 1;
                $msg = 'Invalid Url address: ' . $url;
            } else {
                $duration = $this->input->post('duration');
                $starttime = $this->input->post('starttime');
                $endtime = $this->input->post('endtime');
                $url = $this->input->post('url');
                $updateF = $this->input->post('updateF');
                $code = 0;
                $msg = '';
                if (strlen($duration) != 5 && strpos($duration, ':') === false) {
                    $code = 1;
                    $msg = $this->lang->line('warn.playtime.format');
                } else {
                    $array = explode(':', $duration);
                    if (count($array) != 2) {
                        $code = 1;
                        $msg = $this->lang->line('warn.playtime.format');
                    } else {
                        $h = intval($array[0]);
                        $m = intval($array[1]);
                        if ($h < 0 || $h > 24 || $m < 0 || $m > 59) {
                            $code = 1;
                            $msg = $this->lang->line('warn.playtime.range');
                        }
                    }
                }
            }

            if ($code > 0) {
                echo json_encode(array('code' => $code, 'msg' => $msg));
                return;
            }

            $data = array('duration' => $duration, 'starttime' => $starttime, 'endtime' => $endtime, 'publish_url' => $url, 'updateF' => $updateF);
            $this->load->model('program');
            $this->program->update_interaction_area_media($data, $id);

            $result['code'] = 0;
            $result['msg'] = $this->lang->line('save.success');
        }

        echo json_encode($result);
    }

    //批量修改某个区域图片的播放模式和时间
    public function edit_playlist_area_media()
    {
        $area_id = $this->input->get('area_id');
        $type = $this->input->get('type');
        $media_type = $this->input->get('media_type');
        $this->load->model('program');
        $this->load->model('material');

        $data['media_type'] = $media_type;
        $data['transmode_type'] = $this->program->get_interaction_area_transmode_type($area_id);
        $data['transmode'] = $this->lang->line('transmode');
        $data['area_id'] = $area_id;
        $data['type'] = $type;
        $this->load->view('program/pls/screen_all_area_media', $data);
    }

    public function save_playlist_area_media()
    {
        $result = array();
        $cid = $this->get_cid();
        $area_id = $this->input->post('areaId');
        $type = $this->input->post('type');
        if ($cid <= 0) {
            $result = array('code' => 1, 'msg' => $this->lang->line('warn.system.user'));
        } else {
            if ($type == 'playtime') {
                $this->load->library('form_validation');
                $this->form_validation->set_rules('duration', $this->lang->line('play_time'), 'trim|required');

                if ($this->form_validation->run() == false) {
                    $result = array('code' => 1, 'msg' => validation_errors());
                } else {
                    $duration = $this->input->post('duration');

                    $code = 0;
                    $msg = '';
                    if (strlen($duration) != 5 && strpos($duration, ':') === false) {
                        $code = 1;
                        $msg = $this->lang->line('warn.playtime.format');
                    } else {
                        $array = explode(':', $duration);
                        if (count($array) != 2) {
                            $code = 1;
                            $msg = $this->lang->line('warn.playtime.format');
                        } else {
                            $h = intval($array[0]);
                            $m = intval($array[1]);
                            if ($h < 0 || $h > 59 || $m < 0 || $m > 59 || $m == '') {
                                $code = 1;
                                $msg = $this->lang->line('warn.playtime.range');
                            }
                        }
                    }

                    if ($code > 0) {
                        $result['code'] = 1;
                        $result['msg'] = $msg;
                    } else {
                        $data = array('duration' => $duration);
                        $this->load->model('program');
                        $this->program->update_interaction_all_area_media($data, $area_id);
                        $result['code'] = 0;
                        $result['msg'] = $this->lang->line('save.success');
                    }
                }
            }
            if ($type == 'transition') {
                $transmode = $this->input->post('transmode');
                $data = array('transmode' => $transmode);
                $this->load->model('program');
                $this->program->update_interaction_all_area_media($data, $area_id);
                $result['code'] = 0;
                $result['msg'] = $this->lang->line('save.success');
            }
        }
        echo json_encode($result);
    }

    public function edit_playlist_media()
    {
        $id = $this->input->get('id');
        $area_id = $this->input->get('area_id');
        $media_id = $this->input->get('media_id');
        $this->load->model('program');
        $this->load->model('material');
        $media = $this->program->get_interaction_playlist_area_media($id);
        $playlist = $this->program->get_interaction_playlist($media->interaction_playlist_id);
        $data['media'] = $media;
        $data['media_type'] = $this->material->get_media_type($media->media_id);
        $data['transmode_type'] = $this->program->get_interaction_area_transmode_type($area_id);
        $data['transmode'] = $this->lang->line('transmode');
        $data['playlist'] = $playlist;
        $this->load->view('program/pls/screen_area_media', $data);
    }

    public function save_playlist_media()
    {
        $result = array();
        $cid = $this->get_cid();
        $id = $this->input->post('id');
        if ($cid <= 0) {
            $result = array('code' => 1, 'msg' => $this->lang->line('warn.system.user'));
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('duration', $this->lang->line('duration'), 'trim|required');

            if ($this->form_validation->run() == false) {
                $result = array('code' => 1, 'msg' => validation_errors());
            } else {
                $duration = $this->input->post('duration');
                $transmode = $this->input->post('transmode');
                $transtime = $this->input->post('transtime');
                $imgfit = $this->input->post('imgfit');

                $code = 0;
                $msg = '';
                if (strlen($duration) != 5 && strpos($duration, ':') === false) {
                    $code = 1;
                    $msg = $this->lang->line('warn.playtime.format');
                } else {
                    $array = explode(':', $duration);
                    if (count($array) != 2) {
                        $code = 1;
                        $msg = $this->lang->line('warn.playtime.format');
                    } else {
                        $h = intval($array[0]);
                        $m = intval($array[1]);
                        if ($h < 0 || $h > 59 || $m < 0 || $m > 59) {
                            $code = 1;
                            $msg = $this->lang->line('warn.playtime.range');
                        }
                    }
                }
            }

            if ($code > 0) {
                echo json_encode(array('code' => $code, 'msg' => $msg));
                return;
            }

            $data = array('duration' => $duration, 'transmode' => $transmode, 'transtime' => $transtime, 'img_fitORfill' => $imgfit);
            $this->load->model('program');
            $this->program->update_interaction_area_media($data, $id);

            $result['code'] = 0;
            $result['msg'] = $this->lang->line('save.success');
        }

        echo json_encode($result);
    }

    /**
     * 修改播放列表中reload
     *
     * @return
     */
    public function do_editReload()
    {
        $result = array();
        $id = $this->input->post('id');
        $areaId = $this->input->post('areaId');

        $this->load->model('program');
        if ($this->program->edit_interaction_reload(array('area_id' => $areaId), $id)) {
            $result = array('code' => 0, 'msg' => '');
        }
    }

    /**
     * treejson 转成数组
     */
    public function treejson_to_array($treejson)
    {
        $array = array();
        $tmp_json = str_replace('},', '}@<>#', substr($treejson, 0, -1));
        $tmp_json = str_replace('id', '"id"', $tmp_json);
        $tmp_json = str_replace('pId', '"pId"', $tmp_json);
        $tmp_json = str_replace('name', '"name"', $tmp_json);
        $tmp_json = str_replace('checked', '"checked"', $tmp_json);
        $tmp_json = str_replace('open', '"open"', $tmp_json);
        $tmp_json = str_replace('iconSkin', '"iconSkin"', $tmp_json);
        $tmp_json = str_replace('noR', '"noR"', $tmp_json);
        $tmp_arr = explode('@<>#', $tmp_json);
        for ($i = 0; $i < count($tmp_arr); $i++) {
            $arr = json_decode($tmp_arr[$i], true);
            $array[] = $arr;
        }
        return $array;
    }
    /**
     * 设置字体文件信息
     * @param object $font [simfang]
     * @return
     */
    public function set_title_font($font, $bold, $italic)
    {
        if ($bold == 1 & $italic == 1) {
            $font_file = $this->get_realpath('./fonts/' . $font . ' Bold Italic.ttf');
        } else {
            if ($bold == 1) {
                $font_file = $this->get_realpath('./fonts/' . $font . ' Bold.ttf');
            } else {
                if ($italic == 1) {
                    $font_file = $this->get_realpath('./fonts/' . $font . ' Italic.ttf');
                } else {
                    $font_file = $this->get_realpath('./fonts/' . $font . '.ttf');
                }
            }
        }
        //$font_file = $this->get_realpath('./fonts/'.$font.'.ttf');
        if (file_exists($font_file)) {
            return $this->title_font_file = $font_file;
        } else {
            return false;
        }
    }

    /**
     * 获取真实路径
     *
     * @param object $path
     * @return
     */
    private function get_realpath($path)
    {
        if (function_exists('realpath') and @realpath($path) !== false) {
            $path = str_replace("\\", "/", realpath($path));
        }

        return $path;
    }

    //保存 static text设置
    public function do_save_static_text()
    {
        $playlist_id = $this->input->post('pid');
        $id = $this->input->post('id');
        $area_id = $this->input->post('area_id');
        $type = $this->input->post('type');
        switch ($type) {
            case 'bold':
                $bold = $this->input->post('bold');
                $data = array('area_id' => $area_id, 'interaction_playlist_id' => $playlist_id, 'bold' => $bold, 'add_user_id' => $this->get_uid());
                break;
            case 'italic':
                $italic = $this->input->post('italic');
                $data = array('area_id' => $area_id, 'interaction_playlist_id' => $playlist_id, 'italic' => $italic, 'add_user_id' => $this->get_uid());
                break;
            case 'underline':
                $underline = $this->input->post('underline');
                $data = array('area_id' => $area_id, 'interaction_playlist_id' => $playlist_id, 'underline' => $underline, 'add_user_id' => $this->get_uid());
                break;
            case 'font_size':
                $font_size = $this->input->post('font_size');
                $data = array('area_id' => $area_id, 'interaction_playlist_id' => $playlist_id, 'font_size' => $font_size, 'add_user_id' => $this->get_uid());
                break;
            case 'font_family':
                $font_family = $this->input->post('font_family');
                $data = array('area_id' => $area_id, 'interaction_playlist_id' => $playlist_id, 'font_family' => $font_family, 'add_user_id' => $this->get_uid());
                break;
            case 'font_position':
                $position = $this->input->post('font_position');
                $data = array('area_id' => $area_id, 'interaction_playlist_id' => $playlist_id, 'position' => $position, 'add_user_id' => $this->get_uid());
                break;
            case 'transparent':
                $transparent = $this->input->post('transparent');
                $data = array('area_id' => $area_id, 'interaction_playlist_id' => $playlist_id, 'transparent' => $transparent, 'add_user_id' => $this->get_uid());
                break;
        }

        $this->load->model('program');
        if (empty($id)) {
            //insert
            $id = $this->program->add_interaction_area_static_text_setting($data, $this->get_uid());
        } else {
            $this->program->update_interaction_area_static_text_setting($data, $id);
        }
    }

    //修改 网页播放时间
    public function do_editPlayTime()
    {
        $id = $this->input->post('id');
        $areaId = $this->input->post('areaId');
        $playTimeh = $this->input->post('playTimeh');
        $playTimem = $this->input->post('playTimem');
        $playTime = $playTimeh . ':' . $playTimem;
        //echo 'id:'.$id.', areaId: '.$areaId.', playTime: '.$playTime;

        if ($playTime == '00:00') {
            $result = array('code' => 1, 'msg' => 'PlayTime must be more than 00:00!');
        } else {
            $this->load->model('program');
            if ($this->program->interaction_edit_endTime(array('area_id' => $areaId, 'duration' => $playTime), $id)) {
                $result = array('code' => 0, 'msg' => 'test');
            }
        }

        echo json_encode($result);
    }

    public function delete_btn_media()
    {
        $areaId = $this->input->post('areaid');
        $pId = $this->input->post('pid');
        $this->load->model('program');
        return $this->program->delete_interaction_playlist_area_media($pId, $areaId);
    }
}
