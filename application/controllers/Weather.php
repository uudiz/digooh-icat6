<?php
class Weather extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('common');
        $this->lang->load('weather');
    }
    
    /**
     * 默认展示页面
     *
     * @return
     */
    public function index()
    {
        $this->addJs('weather.js');
        $this->addCss('template.css');
        
        $this->lists(1, true);
    }
    
    /**
     * 模板列表
     *
     * @return
     */
    public function lists($curpage = 1, $main=false)
    {
        $data = $this->get_data();


        $limit = $this->config->item('page_template_size');
       
        $offset = ($curpage - 1) * $limit;
                
        $this->load->model('weathers');

        $auth = $this->get_auth(); //获取用户的权限

        $list = $this->weathers->get_staff_template_list($this->get_cid(), $offset, $limit);
     

       
        if (! empty($list['data'])) {
            for ($i = 0; $i < count($list['data']); $i++) {
                $list['data'][$i]->area_list = $this->weathers->get_staff_area_list($list['data'][$i]->id);
            }
        }
        $max_limit = false;
        if ($list['total'] >= $this->config->item('template_limit')) {
            $max_limit = true;
        }
        $data['max']=$this->config->item('template_limit');
        $data['max_limit'] = $max_limit;
        $data['total'] = $list['total'];
        $data['data'] = $list['data'];

        
        $data['auth_system'] = $this->get_auth() == $this->config->item('auth_system');
        $data['limit'] = $limit;
        $data['curpage'] = $curpage;
        
        if ($main) {
            $data['body_file'] = 'weather/index';
            $this->load->view('include/main2', $data);
        } else {
            $this->load->view('weather/index', $data);
        }
    }
    
    /**
     * 添加模板页
     * @return
     */
    public function add()
    {
        $type = $this->input->get('type');
        if ($type == $this->config->item('template_system')) {
            $type = $this->config->item('template_system');
        } else {
            $type = $this->config->item('template_user');
        }
        
 
        
        $this->load->view('weather/new');
    }
    
    public function edit()
    {
        $id = $this->input->get('id');
        $this->load->model('weathers');
        $template = $this->weathers->get_template($id);
        if ($template) {
            $data = $this->get_data();
            $data['template'] = $template;
            $data['id'] = $id;
 
            
            $this->load->view('weather/edit', $data);
        } else {
            $this->show_msg($this->lang->line('param.error'), 'error');
        }
    }
    
    
    /**
     * 保存模板数据
     *
     * @return
     */
    public function do_save()
    {
        $id = $this->input->post('id');
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');
        $result = array();
        if ($this->form_validation->run() == false) {
            //false
            $result = array('code'=>1, 'msg'=>validation_errors());
        } else {
            //true
            $this->load->model('weathers');
            $system = $this->config->item('template_user');
            
            $flag = $this->weathers->get_template_by_name($id, $this->get_cid(), $this->input->post('name'));
            if ($id >= 0 && $flag) {
                $result = array('code'=>1, 'msg'=>sprintf($this->lang->line('template.name.exsit'), $this->input->post('name')));
            } else {
                $data = array('name'=>$this->input->post('name'), 'descr'=>$this->input->post('descr'));
                if ($id) {
                    $this->weathers->update_template($data, $id);
                } else {
                    $width = $this->input->post('width');
                    $height = $this->input->post('height');
                    $wh = $this->config->item('template_size_'.$width.'_'.$height);
                    $w = $wh['w'];
                    $h = $wh['h'];
                    $data['width']=$width;
                    $data['height']=$height;
                    $data['w']=$w;
                    $data['h']=$h;
                    $data['flag'] = 0;


                    $id = $this->weathers->add_template($data, $this->get_cid(), $this->get_uid(), false);
                }
                if ($id !== false) {
                    $result = array('code'=>0, 'msg'=>$this->lang->line('save.success'));
                    $result['id'] = $id;
                    $result = array_merge($result, $data);
                } else {
                    $result = array('code'=>1, 'msg'=>sprintf($this->lang->line('save.fail'), $this->lang->line('template')));
                }
            }
        }

        
        echo json_encode($result);
    }
    
    /**
     * 创建屏幕信息
     *
     * @return
     */
    public function edit_screen()
    {
        $this->addCss('template.css');
        
        $id = $this->input->get('id');
        $this->load->model('weathers');
        $template = $this->weathers->get_template($id);
        //$using = $this->weathers->is_template_using($id);
        $area_list = $this->weathers->get_staff_area_list($id);


        if ($area_list) {
            foreach ($area_list as $area) {
                if ($area->area_type == $this->config->item('area_type_bg')||
                    $area->area_type == $this->config->item('area_type_logo')||
                    $area->area_type == $this->config->item('area_type_image')||
                    $area->area_type == $this->config->item('area_type_image2')||
                    $area->area_type == $this->config->item('area_type_image3')) {
                    $this->load->model('material');
                    $bgs = $this->material->get_media($area->mid);
  

                    if ($bgs) {
                        $area->media_id = $area->mid;
                        if ($area->area_type == $this->config->item('area_type_logo')) {
                            $area->main_url = $bgs->tiny_url;
                        } else {
                            $area->main_url = substr($bgs->full_path, 1);
                        }
                    } else {
                        $area->media_id = 0;
                        $area->main_url = '';
                    }
                }
            }
        }

        $data = $this->get_data();
        
        $data['area_list'] = $area_list;
        $data['using']=false;
        
        $data['id'] = $id;
        $data['template'] = $template;
        
        if ($template) {
            $data['width'] = $template->w;
            $data['height'] = $template->h;
        } else {
            $data['width'] = $this->config->item('screen_width');
            $data['height'] = $this->config->item('screen_height');
        }

        $this->load->view('weather/add_screen', $data);
    }
    

    public function images($curpage = 1)
    {
        $this->lang->load('folder');
        $cid = $this->get_cid();
        $folder_id = $this->input->get('folder_id');

        $orig_folderid = $folder_id;



        $type = $this->input->get('type');

        $data  = $this->get_folders_and_media($this->config->item('media_type_image'), $curpage, 'name', 'asc');

  
        $data['folder_id'] = $orig_folderid;
        
        $data['tables'] = 'weather/images_table';
        $data['type'] = $this->input->get('type');

        if ($this->input->get('refresh')) {
            $this->load->view('weather/images_table', $data);
        } else {
            $this->load->view('weather/images', $data);
        }
    }
   
    

    
    
    
    /**
     * 保存屏幕设置,创建和更新逻辑
     *
     * @return
     */
    public function save_screen()
    {
        $moive = $this->input->post("movie");
        $images = $this->input->post("image");
        $text = $this->input->post("text");
        $date = $this->input->post("date");
        $time = $this->input->post("time");
        $weather = $this->input->post("weather");
        $logo = $this->input->post("logo");
        $webpage = $this->input->post("webpage");
        $staticText = $this->input->post("staticText");
        $branchText = $this->input->post("branchText");
        $mask = $this->input->post("mask");
        $bg = $this->input->post("bg");
        $screen = $this->input->post("screen");
        $id = $this->input->post("id");
        $deletes = $this->input->post("deletes");
        

        
        $result = array();
        if (empty($screen) || (empty($images) && empty($moive) && empty($text) && empty($date) && empty($time) && empty($weather) && empty($logo) && empty($bg) && empty($webpage) && empty($staticText) && empty($mask))) {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('template.error.screen.empty');
        } else {
            $this->load->model('weathers');
    
            if (! empty($deletes)) {
                $d = json_decode($deletes, true);
                if ($d) {
                    foreach ($d as $area) {
                        $this->weathers->delete_template_area($area['id'], $this->config->item('area_type_'.$area['type']));
                    }
                }
                unset($d);
            }
  
 
            //load image lib for template
            $this->load->library('Image');
            $bg_file = false;
            $pwidth = $this->config->item('template_preview_width');
            $pheight = $this->config->item('template_preview_height');
            $swidth = 800;//屏幕宽度
            $sheight = 600;//屏幕高度
            
           
            
            if (! empty($screen)) {
                $d = json_decode($screen, true);
                $swidth = $d['w'];
                $sheight = $d['h'];
                $template_type = $d['template_type'];
                unset($d);
            }
            if ($swidth < $sheight) {
                $pwidth = $this->config->item('template_preview_reverse_width');
            }
            $rwidth = $pwidth / $swidth; //宽度比
            $rheight = $pheight / $sheight; //高度比
            $this->load->model('material');
            

            //背景区域
            if (! empty($bg)) {
                $d = json_decode($bg, true);
                
                if ($d) {
                    $dv['w'] = $d['w'];
                    $dv['h'] = $d['h'];
                    $dv['x'] = $d['x'];
                    $dv['y'] = $d['y'];
                    $area_id = $this->get_value($d, 'area_id');
                    $name = $this->get_value($d, 'name');
                    if (empty($name)) {
                        $name = $this->lang->line('template.screen.bg');
                    }
                    $dv['name'] = $name;
                    $dv['area_type'] = $this->config->item('area_type_bg');
                    $dv['mid'] =  $d['media_id'];
                    

                    
                    $media = $this->material->get_media($d['media_id']);
                    
                    if ($media) {
                        $bg_file = $media->full_path;
                    }
                    
                    if ($area_id) {
                        $this->weathers->update_template_area($dv, $area_id);
                    } else {
                        $ba = $this->weathers->get_template_bg_area($id);

                        if ($ba) {
                            $area_id = $ba->id;
                            $this->weathers->update_template_area($dv, $area_id);
                        } else {
                            $area_id = $this->weathers->add_template_area($dv, $id);
                        }
                    }
                    $dv['area_id'] = $area_id;
                    $result['bg'] = $dv;
                    unset($dv);
                }
                unset($d);
            }
            
            $this->image->create($pwidth, $pheight, $bg_file);
            


            
            //视频区域
            if (! empty($moive)) {
                $m = json_decode($moive, true);
                if ($m) {
                    $dv['w'] = $m['w'];
                    $dv['h'] = $m['h'];
                    $dv['x'] = $m['x'];
                    $dv['y'] = $m['y'];
                    $area_id = $this->get_value($m, 'area_id');
                    $name = $this->get_value($m, 'name');
                    $zindex = $this->get_value($m, 'zindex');
                    if ($zindex === false) {
                        $zindex = 10;
                    }
                    $dv['zindex'] = $zindex;
                    if (empty($name)) {
                        $name = $this->lang->line('template.screen.movie');
                    }
                    $dv['name'] = $name;
                    
                    $dv['area_type'] = $this->config->item('area_type_movie');
                    
                    if ($area_id) {
                        $this->weathers->update_template_area($dv, $area_id);
                    } else {
                        $area_id = $this->weathers->add_template_area($dv, $id);
                    }
                    $dv['area_id'] = $area_id;
                    $result['movie'] = $dv;
                    
                    //set preview image
                    $this->image->add_area($dv['x'] * $rwidth, $dv['y'] * $rheight, $dv['w'] * $rwidth, $dv['h'] * $rheight, $name, $this->config->item('area_type_movie_color'));
                    unset($dv);
                }
                unset($m);
            }

            if (! empty($logo)) {
                $d = json_decode($logo, true);
                if ($d) {
                    $dv['w'] = $d['w'];
                    $dv['h'] = $d['h'];
                    $dv['x'] = $d['x'];
                    $dv['y'] = $d['y'];
                    $dv['name'] = $this->lang->line('template.screen.logo');
                    $dv['area_type'] = $this->config->item('area_type_logo');
                    $area_id = $this->get_value($d, 'area_id');
                    $zindex = $this->get_value($d, 'zindex');
                    if ($zindex === false) {
                        $zindex = 10;
                    }
                    $dv['zindex'] = $zindex;
                        
                    //设置
                    $dv['mid'] = $d['media_id'];
                    if ($area_id) {
                        $this->weathers->update_template_area($dv, $area_id);
                    } else {
                        $area_id = $this->weathers->add_template_area($dv, $id);
                    }
                        
                    $dv['area_id'] = $area_id;
                    $result['logo'] = $dv;
                    $media = $this->material->get_media($d['media_id']);
                    $bg_file = false;
                    if ($media) {
                        $bg_file = '.'.$media->main_url;
                    }
                    //设置预览区域
                    $this->image->add_area($dv['x'] * $rwidth, $dv['y'] * $rheight, $dv['w'] * $rwidth, $dv['h'] * $rheight, "Video", $this->config->item('area_type_logo_color'));
                        
                    unset($dv);
                    unset($ds);
                }
                unset($d);
            }

            
            //照片区域
            if (! empty($images)) {
                $i = 1;
                foreach ($images as $image) {
                    $img = json_decode($image, true);
                    if ($img) {
                        $dv['w'] = $img['w'];
                        $dv['h'] = $img['h'];
                        $dv['x'] = $img['x'];
                        $dv['y'] = $img['y'];
                        $area_id = $this->get_value($img, 'area_id');
                        $name = $this->get_value($img, 'name');
                        
                        $zindex = $this->get_value($img, 'zindex');
                        if ($zindex === false) {
                            $zindex = 10;
                        }
                        $dv['zindex'] = $zindex;

                        $dv['mid'] = $img['media_id'];
                        $media = $this->material->get_media($img['media_id']);


    
                        if ($media) {
                            $bg_file = $media->full_path;
                        }

                        
                        if (empty($name)) {
                            $name = $this->lang->line('template.screen.image').$i;
                        }
                        $dv['name'] = $name;

                        if ($dv['zindex']==21) {
                            $dv['area_type'] = $this->config->item('area_type_image');
                        } elseif ($dv['zindex']==22) {
                            $dv['area_type'] = $this->config->item('area_type_image2');
                        } else {
                            $dv['area_type'] = $this->config->item('area_type_image3');
                        }
                        if ($area_id) {
                            $this->weathers->update_template_area($dv, $area_id);
                        } else {
                            $area_id = $this->weathers->add_template_area($dv, $id);
                        }

                        

                        $dv['area_id'] = $area_id;
                        $result['image'][] = $dv;
                        //set preview image
                        $this->image->add_area($dv['x'] * $rwidth, $dv['y'] * $rheight, $dv['w'] * $rwidth, $dv['h'] * $rheight, $name, $this->config->item('area_type_image'.$i.'_color'));
                        unset($dv);
                    }
                    
                    $i++;
                    unset($img);
                }
            }

        

            
            
            $template_preview_path = sprintf($this->config->item('tempate_preview_path'), $this->get_cid());
            $absolut_path = $this->config->item('base_path').$template_preview_path;
                
            $result['absolut_path'] = $absolut_path;
            
           
            if (!is_dir($absolut_path)) {
                mkdir($absolut_path);
            }
         
            if ($this->image->save($absolut_path, $id.'.jpg')) {
                $result['msg2'] = 'success'.$absolut_path;
                $this->weathers->update_template(array('preview_url'=>$template_preview_path.'/'.$id.'.jpg',  'update_time'=>date('Y-m-d H:i:s')), $id);
            } else {
                $result['msg2'] = 'fail';
            }
            
            
            $result['code'] = 0;
            $result['id'] = $id;
            $result['msg'] = $this->lang->line('save.success');
            if ($this->input->post('is_active')) {
                $this->froce_update_weahter();
            }
        }

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
        $force=$this->input->post('force');
        $result = array();
        $this->load->model('weathers');
        if ($id) {
            //check used template by playlist
           
            if ($this->weathers->delete_template($id)) {
                $result['code'] = 0;
                $result['msg'] = $this->lang->line('delete.success');
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
        $config['upload_path'] = $this->config->item('tmp');
        $config['allowed_types'] = '*';//'gif|jpg|jpeg|png|bmp';
        $config['encrypt_name'] = true;
    
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        $this->load->model('program');
        if (!$this->upload->do_upload('upfile')) {
            $result = array('success'=>false, 'code'=>1, 'msg'=>$this->upload->display_errors());
        } else {
            $data = $this->upload->data();
            if (substr($data['orig_name'], 0, 9) != 'Template.' || substr($data['orig_name'], -4) != '.xml') {
                $result = array('success'=>false, 'code'=>1, 'msg'=>$this->lang->line('warn.template.name'));
            } else {
                $template_name = substr($data['orig_name'], 9, strlen($data['orig_name'])-13);
                $flag = $this->weathers->get_template_by_name(0, $this->get_cid(), $template_name);
                if ($flag) {
                    $result = array('success'=>false, 'code'=>1, 'msg'=>$this->lang->line('warn.import.name'));
                } else {
                    $template = $this->parse_template($data['full_path'], true, $template_name);
                    
                    
                    if ($template) {
                        if ($template['template_type'] >= 0) {
                            @unlink($data['full_path']);
                            $result['template']=$template;
                                
                            $cid = $this->get_cid();
                            $uid = $this->get_uid();
                            $id = $this->weathers->add_template(array_slice($template, 0, -1), $cid, $uid);
                            if ($id) {
                                $areas = $template['areas'];
                                if (count($areas)) {
                                    foreach ($areas as $area) {
                                        $this->weathers->add_template_area($area, $id);
                                    }
                                }
                                $this->load->library('image');
                                $this->weathers->update_template_preview_url($id);
                                $result['success'] = true;
                                $result['code'] = 0;
                                $result['msg']=$this->lang->line('template.import.success');//array('code'=>0, 'msg'=>'');
                            } else {
                                $result['success'] = false;
                                $result['code'] = 1;
                                $result['msg']=$this->lang->line('warn.template.import.fail');
                                //$result = array('code'=>1, 'msg'=>$this->lang->line('warn.template.import.fail'));
                            }
                        } else {
                            $result = array('success'=>false, 'code'=>2, 'msg'=>'test', 'path'=>$data['full_path']);
                        }
                    } else {
                        $result = array('success'=>false, 'code'=>1, 'msg'=>$this->lang->line('warn.template.import.format'));
                    }
                }
            }
        }
    
        //@unlink($_FILES['Filedata']['tmp_name']);
    
        echo json_encode($result);
    }

    public function avtive_template()
    {
        $id = $this->input->post('id');
        $isactive = $this->input->post('active');

        $this->load->model('weathers');
        $this->weathers->set_active($id, $this->get_cid, $isactive);
        if ($isactive) {
            $this->froce_update_weahter();
        }
    }

    private function froce_update_weahter()
    {
        $command = '/usr/bin/php -f /home/icat/public_html/cli.php cron parse_weather  2>&1 &';
        @exec($command, $output, $return);
    }
}
