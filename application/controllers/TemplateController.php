<?php

use PhpOffice\PhpSpreadsheet\Style\Style;

class TemplateController extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('template');
        $this->load->model('template');
    }

    public function index()
    {

        $this->addJs("/assets/js/template.js", false);
        $data = $this->get_data();
        if ($this->get_auth() == 1 && $this->config->item("new_campaign_user")) {
            $data['body_file'] = 'bootstrap/template/index';
        } else if ($this->get_auth() <= 2) {
            $data['body_file'] = 'bootstrap/401';
        } else {
            $data['body_file'] = 'bootstrap/template/index';
        }
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getTableData()
    {
        $name = $this->input->post('search');

        $this->load->model('template');
        $data = $this->get_data();
        $offset = $this->input->post('offset') ?: 0;
        $limit = $this->input->post('limit') ?: -1;
        $order_item = $this->input->post('sort') ?: 'update_time';
        $order = $this->input->post('order') ?: "desc";

        $filter_array = array();
        if ($name) {
            $filter_array['name'] = $name;
        }
        $auth = $this->get_auth();
        if ($this->config->item("new_campaign_user") && $auth == 1) {
            $filter_array['user_id'] = $this->get_uid();
        }
        $rest = $this->template->get_template_list($this->get_cid(), $offset, $limit, $order_item, $order, $filter_array);
        $data['total'] = $rest['total'];
        $data['rows']  = $rest['data'];

        echo json_encode($data);
    }
    /**
     * 添加模板页
     * @return
     */

    public function add()
    {
        $this->addJs("/assets/js/form.js", false);
        $data = $this->get_data();
        $type = $this->input->get('type');
        $data['title'] = $this->lang->line('create.template');
        if ($type == $this->config->item('template_system')) {
            $type = $this->config->item('template_system');
        } else {
            $type = $this->config->item('template_user');
        }

        $this->load->model('system');
        //  $screen = $this->system->get_screen_info_list();
        $data['type'] = $type;


        $this->load->view('bootstrap/template/new', $data);
    }



    public function do_create()
    {
        $id = $this->input->post('id');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');
        $result = array();
        if ($this->form_validation->run() == FALSE) {
            //false
            $result = array('code' => 1, 'msg' => validation_errors());
        } else {
            //true
            $this->load->model('template');
            $system = $this->config->item('template_user');

            $flag = $this->template->get_template_by_name($id, $this->get_cid(), $this->input->post('name'));
            if ($id >= 0 && $flag) {
                $result = array('code' => 1, 'msg' => sprintf($this->lang->line('template.name.exsit'), $this->input->post('name')));
            } else {
                $data = array('name' => $this->input->post('name'), 'descr' => $this->input->post('descr'));
                if ($id) {
                    $this->template->update_template($data, $id);
                } else {
                    $type = $this->input->post('type');
                    if ($type == $this->config->item('template_system')) {
                        $system = $this->config->item('template_system');
                    }

                    $width = $this->input->post('width');
                    $height = $this->input->post('height');
                    $wh = $this->config->item('template_size_' . $width . '_' . $height);
                    $w = $wh['w'];
                    $h = $wh['h'];
                    //'system'=>$system, 'width'=>$width, 'height'=>$height, 'w'=>$w, 'h'=>$h
                    $data['system'] = $system;
                    $data['width'] = $width;
                    $data['height'] = $height;
                    $data['w'] = $w;
                    $data['h'] = $h;
                    $data['template_type'] = $this->input->post('template_type');
                    $id = $this->template->add_template($data, $this->get_cid(), $this->get_uid(), TRUE);
                }
                if ($id !== FALSE) {
                    $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                    $result['id'] = $id;
                    $result = array_merge($result, $data);
                } else {
                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('template')));
                }
            }
        }


        echo json_encode($result);
    }
    /**
     * 将模板导出生成文件
     * 
     * @return 
     */
    public function export()
    {
        $id = $this->input->get('id');

        $template = $this->template->get_template($id);
        if ($template) {
            $area_list = $this->template->get_area_list($id);
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<template>';
            $xml .= '<name><![CDATA[' . $template->name . ']]></name>';
            $xml .= '<descr><![CDATA[' . $template->descr . ']]></descr>';
            $xml .= '<type>' . $template->template_type . '</type>';
            $xml .= '<width>' . $template->width . '</width>';
            $xml .= '<height>' . $template->height . '</height>';
            $xml .= '<w>' . $template->w . '</w>';
            $xml .= '<h>' . $template->h . '</h>';
            $xml .= '<flag>' . $template->flag . '</flag>';
            $xml .= '<areas>';
            if ($area_list) {
                foreach ($area_list as $a) {
                    $xml .= '<area>';
                    $xml .= '<name><![CDATA[' . $a->name . ']]></name>';
                    $xml .= '<area_type>' . $a->area_type . '</area_type>';
                    $xml .= '<x>' . $a->x . '</x>';
                    $xml .= '<y>' . $a->y . '</y>';
                    $xml .= '<w>' . $a->w . '</w>';
                    $xml .= '<h>' . $a->h . '</h>';
                    $xml .= '<zindex>' . $a->zindex . '</zindex>';
                    $xml .= '<area_name><![CDATA[' . $a->area_name . ']]></area_name>';
                    $xml .= '</area>';
                }
            }
            $xml .= '</areas>';

            $img_content = file_get_contents("." . $template->preview_url);
            if ($img_content) {
                $xml .= '<preview>';
                $xml .= '<![CDATA[' . base64_encode($img_content) . ']]>';
                $xml .= '</preview>';
            }

            $xml .= '</template>';


            $xml .= '<!--' . md5($xml . 'miatek') . '-->';
            $this->load->helper('file');
            if (downloadContent($xml, 'Template.' . $template->name . '.xml')) {
                return;
            }
        }
        $this->show_msg($this->lang->line('param.error'), 'error');
    }




    private function parse_template($file_name, $flag = TRUE, $template_name = FALSE)
    {

        if ($f = @fopen($file_name, 'r')) {
            $xml = '';
            while (!feof($f)) {
                $xml .= fgets($f, 4096);
            }
            fclose($f);
            $sig_len = 39;
            if (strlen($xml) <= $sig_len) {
                return FALSE;
            }
            //<!--c6b6a49685bbe4f11bf085a996ad72fb-->
            $sig = substr($xml, -$sig_len);
            if (substr($sig, 0, 4) != '<!--' || substr($sig, -3) != '-->') {
                return FALSE;
            }
            if (md5(substr($xml, 0, -$sig_len) . 'miatek') != substr($sig, 4, -3)) {
                return FALSE;
            }

            $dom = new DOMDocument();
            if (@$dom->loadXML($xml)) {
                $template = $dom->getElementsByTagName('template');

                $tm = array();


                if ($template->length) {

                    $t = $template->item(0);
                    //$name = trim($t->getElementsByTagName('name')->item(0)->nodeValue);
                    $name = $template_name;
                    $descr = trim($t->getElementsByTagName('descr')->item(0)->nodeValue);
                    if ($flag) {
                        if ($t->getElementsByTagName('type')->length) {
                            $template_type = trim($t->getElementsByTagName('type')->item(0)->nodeValue);
                        } else {
                            $template_type = -1;
                        }
                    } else {
                        $template_type = '';
                    }
                    $width = trim($t->getElementsByTagName('width')->item(0)->nodeValue);
                    $height = trim($t->getElementsByTagName('height')->item(0)->nodeValue);
                    $w = trim($t->getElementsByTagName('w')->item(0)->nodeValue);
                    $h = trim($t->getElementsByTagName('h')->item(0)->nodeValue);
                    $flag = trim($t->getElementsByTagName('flag')->item(0)->nodeValue);

                    if ($name && $width && $height) {
                        $xareas = $t->getElementsByTagName('area');
                        $areas = array();
                        foreach ($xareas as $a) {
                            $aname = trim($a->getElementsByTagName('name')->item(0)->nodeValue);
                            $area_type = trim($a->getElementsByTagName('area_type')->item(0)->nodeValue);
                            $ax = trim($a->getElementsByTagName('x')->item(0)->nodeValue);
                            $ay = trim($a->getElementsByTagName('y')->item(0)->nodeValue);
                            $aw = trim($a->getElementsByTagName('w')->item(0)->nodeValue);
                            $ah = trim($a->getElementsByTagName('h')->item(0)->nodeValue);
                            $az = trim($a->getElementsByTagName('zindex')->item(0)->nodeValue);
                            $an = trim($a->getElementsByTagName('area_name')->item(0)->nodeValue);

                            if ($aname && is_numeric($ax) && is_numeric($ay) && is_numeric($aw) && is_numeric($ah)) {
                                $areas[] = array('name' => $aname, 'x' => $ax, 'y' => $ay, 'w' => $aw, 'h' => $ah, 'area_type' => $area_type, 'zindex' => $az, 'area_name' => $an);
                            }
                        }


                        $tm['name'] = $name;
                        $tm['descr'] = $descr;
                        $tm['template_type'] = $template_type;
                        $tm['width'] = $width;
                        $tm['height'] = $height;
                        $tm['w'] = $w;
                        $tm['h'] = $h;
                        $tm['flag'] = $flag;
                        $tm['areas'] = $areas;
                        $tm['preview'] = $t->getElementsByTagName('preview')->item(0)->nodeValue;
                        return $tm;
                    }
                }
            }
        }

        return FALSE;
    }



    /**
     * 创建屏幕信息
     *
     * @return
     */
    public function edit_screen()
    {
        $this->lang->load('font');
        $this->addJs("/assets/js/form.js", false);

        $id = $this->input->get('id');
        $data = $this->get_data();
        $using = 0;
        if ($id) {
            $template = $this->template->get_template($id);

            if ($template) {
                $using = $this->template->is_template_using($id);

                $this->load->model('charger_status');
                $data['charger_settings'] = $this->charger_status->get_list($this->get_cid())['data'];


                $data['id'] = $id;
                $data['template'] = $template;
                //$data['area_list'] = $area_list;
            }
        }

        $data['using'] = $using;

        // $this->load->view('include/main2', $data);
        // $this->load->view('program/template/add_screen',$data);

        $data['body_file'] = 'bootstrap/template/add_screen';
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getAreaData()
    {
        $id = $this->input->get('id');
        $res = array();
        $area_list = $this->template->get_area_list($id);

        if ($area_list) {
            foreach ($area_list as $area) {

                if ($area->area_type == $this->config->item('area_type_bg')) {
                    $bgs = $this->template->get_area_image_setting($area->id);

                    if ($bgs) {
                        $area->media_id = $bgs->media_id;
                        $area->main_url = substr($bgs->full_path, 1);
                    } else {
                        $area->media_id = 0;
                        $area->main_url = '';
                    }
                } else if ($area->area_type == $this->config->item('area_type_logo')) {
                    $bgs = $this->template->get_area_image_setting($area->id);
                    if ($bgs) {
                        $area->media_id = $bgs->media_id;
                        $area->tiny_url = $bgs->tiny_url;
                    } else {
                        $area->media_id = 0;
                        $area->tiny_url = '';
                    }
                } else if (
                    $area->area_type == $this->config->item('area_type_time') ||
                    $area->area_type == $this->config->item('area_type_date') ||
                    $area->area_type == $this->config->item('area_type_weather') ||
                    $area->area_type == $this->config->item('area_type_text') ||
                    $area->area_type == $this->config->item('area_type_id')
                ) {


                    $settings = $this->template->get_area_extra_setting($area->id);
                    if (!$settings) {
                        $settings = new stdClass();
                        $settings->style = 1;
                        $settings->color = "#FFFFFF";
                        $settings->bg_color = '#000000';
                        $settings->style = 1;
                        $settings->font_size = 40;
                        $settings->transparent = 4;
                        $settings->charger_setting_id = null;
                    }
                    if ($area->area_type == $this->config->item('area_type_text')) {
                        $settings->style = 2;
                        $settings->direction = 2;
                    }

                    $area->settings = $settings;
                }
            }
        }
        $res = $area_list;
        echo json_encode($res);
    }


    /**
     * 保存屏幕设置,创建和更新逻辑
     *
     * @return
     */
    public function save_screen()
    {
        $this->load->helper("chrome_logger");

        $id = $this->input->post('id');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');
        $result = array();
        if ($this->form_validation->run() == FALSE) {
            //false
            $result = array('code' => 1, 'msg' => validation_errors());
            echo json_encode($result);
            return;
        }

        $flag = $this->template->get_template_by_name($id, $this->get_cid(), $this->input->post('name'));
        if ($id >= 0 && $flag) {
            $result = array('code' => 1, 'msg' => sprintf($this->lang->line('template.name.exsit'), $this->input->post('name')));
            echo json_encode($result);
            return;
        } else {
            $areas = $this->input->post('areas');


            if (!$areas) {
                $result['code'] = 1;
                $result['msg'] = $this->lang->line('template.error.screen.empty');
                echo json_encode($result);
                return;
            }

            $data = array('name' => $this->input->post('name'), 'descr' => $this->input->post('descr'));
            $orientation = $this->input->post('resolution');

            if ($orientation) {
                $resAry = explode('X', $orientation);
                $data['width'] = $resAry[0];
                $data['height'] = $resAry[1];
            }
            $data['w'] = $this->input->post('w');
            $data['h'] = $this->input->post('h');

            if ($id) {
                $this->template->update_template($data, $id);
            } else {
                $id = $this->template->add_template($data, $this->get_cid(), $this->get_uid(), FALSE);
            }
            if ($id !== FALSE) {
                $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
            } else {
                $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('template')));
                echo json_encode($result);
                return;
            }
        }




        foreach ($areas as $area) {
            $dv['w'] = $area['w'];
            $dv['h'] = $area['h'];
            $dv['x'] = $area['x'];
            $dv['y'] = $area['y'];

            $dv['zindex'] = $area['zindex'];

            $dv['name'] = $area['name'];

            $dv['area_type'] = $area['type'];
            $dv['area_name'] = $area['areaName'];

            $area_id = $area['areaId'];


            if ($area_id) {
                $this->template->update_template_area($dv, $area_id);
            } else {
                $area_id = $this->template->add_template_area($dv, $id);
            }
            if (isset($area['mediaId'])) {
                $this->template->add_area_image_setting($area_id, $area['mediaId']);
            }
            if (isset($area['settings'])) {
                $this->template->update_area_extra_setting($area['settings'], $area_id);
            }
        }

        $deletes = $this->input->post("deletes");
        if ($deletes) {
            foreach ($deletes as $area_id) {
                $this->template->delete_template_area($area_id);
            }
        }


        $template_preview_path = sprintf($this->config->item('tempate_preview_path'), $this->get_cid());
        $absolut_path = $this->config->item('base_path') . $template_preview_path;

        if (!is_dir($absolut_path)) {
            mkdir($absolut_path, 0777, true);
        }


        $img = $this->input->post('screenshot');
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $screenshot = base64_decode($img);


        $detsFile = $absolut_path . '/' . $id . '.png';
        if (file_put_contents($detsFile, $screenshot)) {
            $result['msg2'] = 'success' . $absolut_path;
            $this->template->update_template(array('preview_url' => $template_preview_path . '/' . $id . '.png', 'flag' => 1, 'update_time' => date('Y-m-d H:i:s')), $id);
        } else {
            $result['msg2'] = 'fail';
        }

        $result['code'] = 0;
        $result['id'] = $id;
        $result['msg'] = $this->lang->line('save.success');
        echo json_encode($result);
    }

    /**
     * 执行删除模板
     *
     * @return
     */
    public function do_delete()
    {
        $id = $this->input->post('id');
        $force = $this->input->post('force');
        $result = array();
        if ($id) {
            //check used template by playlist
            $this->load->model('template');
            if ($force) {
                if ($this->template->delete_template($id)) {
                    $result['code'] = 0;
                    $result['msg'] = $this->lang->line('delete.success');
                }
            } else {
                $this->load->model('program');
                $playlists = $this->program->get_playlist_by_template($id);
                if ($playlists) {
                    $result['code'] = 1;
                    $size = count($playlists);
                    $playlist = '';
                    for ($i = 0; $i < $size; $i++) {
                        $playlist .= $playlists[$i]->name;
                        if ($i < $size - 1) {
                            $playlist .= ',';
                        }
                    }
                    $result['code'] = 2;
                    $result['msg'] = sprintf($this->lang->line('warn.template.used'), $playlist);
                } else {
                    if ($this->template->delete_template($id)) {
                        $result['code'] = 0;
                        $result['msg'] = $this->lang->line('delete.success');
                    }
                }
            }
        }

        if (empty($result)) {
            $result['code'] = 1;
            $result['msg'] = sprintf($this->lang->line('delete.fail'), $this->lang->line('template'));
        }
        echo json_encode($result);
    }

    public function html5_import()
    {
        $config['upload_path'] = '/tmp';
        $config['allowed_types'] = '*'; //'gif|jpg|jpeg|png|bmp';
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        $this->load->model('template');
        if (!$this->upload->do_upload('file')) {
            $result = array('success' => false, 'code' => 1, 'msg' => $this->upload->display_errors());
        } else {
            $data = $this->upload->data();
            if (substr($data['orig_name'], 0, 9) != 'Template.' || substr($data['orig_name'], -4) != '.xml') {
                $result = array('success' => false, 'code' => 1, 'msg' => $this->lang->line('warn.template.name'));
            } else {
                $template_name = substr($data['orig_name'], 9, strlen($data['orig_name']) - 13);
                $flag = $this->template->get_template_by_name(0, $this->get_cid(), $template_name);
                if ($flag) {
                    $result = array('success' => false, 'code' => 1, 'msg' => $this->lang->line('warn.import.name'));
                } else {
                    $template = $this->parse_template($data['full_path'], TRUE, $template_name);


                    if ($template) {
                        if ($template['template_type'] >= 0) {
                            @unlink($data['full_path']);
                            $result['template'] = $template;

                            $cid = $this->get_cid();
                            $uid = $this->get_uid();
                            $id = $this->template->add_template(array_slice($template, 0, -2), $cid, $uid);
                            if ($id) {
                                $areas = $template['areas'];
                                if (count($areas)) {
                                    foreach ($areas as $area) {
                                        $this->template->add_template_area($area, $id);
                                    }
                                }
                                // $this->load->library('image');
                                // $this->template->update_template_preview_url($id);
                                $template_preview_path = sprintf($this->config->item('tempate_preview_path'), $this->get_cid());
                                $absolut_path = $this->config->item('base_path') . $template_preview_path;

                                if (!is_dir($absolut_path)) {
                                    mkdir($absolut_path, 0777, true);
                                }


                                $screenshot = base64_decode($template['preview']);

                                $detsFile = $absolut_path . '/' . $id . '.png';

                                if (file_put_contents($detsFile, $screenshot)) {
                                    $this->template->update_template(array('preview_url' => $template_preview_path . '/' . $id . '.png', 'flag' => 1, 'update_time' => date('Y-m-d H:i:s')), $id);
                                }


                                $result['success'] = true;
                                $result['code'] = 0;
                                $result['msg'] = $this->lang->line('template.import.success'); //array('code'=>0, 'msg'=>'');
                            } else {
                                $result['success'] = false;
                                $result['code'] = 1;
                                $result['msg'] = $this->lang->line('warn.template.import.fail');
                                //$result = array('code'=>1, 'msg'=>$this->lang->line('warn.template.import.fail'));
                            }
                        } else {
                            $result = array('success' => false, 'code' => 2, 'msg' => 'test', 'path' => $data['full_path']);
                        }
                    } else {
                        $result = array('success' => false, 'code' => 1, 'msg' => $this->lang->line('warn.template.import.format'));
                    }
                }
            }
        }

        //@unlink($_FILES['Filedata']['tmp_name']);

        echo json_encode($result);
    }
}
