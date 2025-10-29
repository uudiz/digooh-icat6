<?php
class Interaction extends MY_Controller {

	public function Interaction() {
		parent :: __construct();
		$this->lang->load('interaction');
		$this->sep = chr(10);
		$this->tab = chr(9);
		$this->tab2 = chr(9).chr(9);
		$this->tab3 = chr(9).chr(9).chr(9);

	}
	/**
	 * 默认的首页
	 * 
	 * @param object $curpage [optional]
	 * @param object $order_item [optional]
	 * @param object $order [optional]
	 * @return 
	 */
	public function index($curpage = 1, $order_item = 'add_time', $order = 'desc') {
		//$this->addJs("fileupload/swfupload.js");
		$this->addJs('/static/fileuploader/all.fine-uploader.min.js', FALSE);
		$this->addJs("jquery/jquery-ui-latest.js");
        $this->addCss("jquery/jquery.ui.all.css");
        $this->addJs("interaction.js");
		$this->refresh($curpage, $order_item, $order, TRUE);
	}

	/**
	 * 刷新页面数据信息
	 * 
	 * @param object $curpage [optional]
	 * @return 
	 */
	public function refresh($curpage = 1, $order_item = 'add_time', $order = 'desc', $main = FALSE) {
		
		$cid = $this->get_cid();
		$this->load->model('program');
		$data = $this->get_data();
		$limit = $this->config->item('page_template_size');
		$offset = ($curpage -1) * $limit;

		$rest = $this->program->get_interaction_list($cid, $offset, $limit, $order_item, $order);
		$data['total'] = $rest['total'];
		$data['data'] = $rest['data'];
		$data['cid'] = $cid;
		$data['curpage'] = $curpage;
		$data['limit'] = $limit;
		$data['order_item'] = $order_item;
		$data['order'] = $order;
		$data['session_id'] = $this->get_session_id();
		if ($main) {
			$data['body_file'] = 'program/interaction/index';
			$this->load->view('include/main2', $data);
		} else {
			$this->load->view('program/interaction/index', $data);
		}
	}

	/**
	 * 添加互动应用
	 * @return 
	 */
	public function add() {
		$this->load->model('system');
		$screen = $this->system->get_screen_info_list();
		$data['screens'] = $screen;
		$this->load->view('program/interaction/add_interaction', $data);
	}

	/**
	 * 保存 互动应用
	 */
	public function doSave() {
		$name = $this->input->post('name');
		$descr = $this->input->post('descr');
		$screen = $this->input->post('screen');
		$id = $this->input->post('id');
		$screen_arr = explode('X', $screen);
		$width = $screen_arr[0];
		$height = $screen_arr[1];
		$cid = $this->get_cid();
		$uid = $this->get_uid();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');
		$result = array ();
		if ($this->form_validation->run() == FALSE) {
			$result = array (
				'code' => 1,
				'msg' => validation_errors()
			);
		} else {
			$this->load->model('program');
			if ($this->program->get_repeated_interaction($name, $id, $cid)) {
				$result = array (
					'code' => 1,
					'msg' => sprintf($this->lang->line('interaction.name.exsit'), $name)
				);
			} else {
				$data = array (
					'name' => $name,
					'descr' => $descr,
					'width' => $width,
					'height' => $height,
					'company_id' => $cid,
					'add_user_id' => $uid,
					'w' => $width / 2,
					'h' => $height / 2
				);
				if ($id > 0) {
					$this->program->update_interaction($data, $id);
				} else {
					$id = $this->program->add_interaction($data);
				}
				if ($id !== FALSE) {
					$result = array (
						'code' => 0,
						'msg' => $this->lang->line('save.success')
					);
					$result['id'] = $id;
					$result = array_merge($result, $data);
				} else {
					$result = array (
						'code' => 1,
						'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('interaction'))
					);
				}
			}
		}
		echo json_encode($result);
	}

	public function edit() {
		$id = $_GET['id'];
		$this->load->model('program');
		$interaction = $this->program->get_interaction($id);
		if ($interaction) {
			$data['interaction'] = $interaction;
			$this->load->view('program/interaction/edit_interaction', $data);
		} else {
			$this->show_msg($this->lang->line('warn.param'), 'warn');
		}
	}
	
	/**
	 * 将模板导出生成文件
	 * 
	 * @return 
	 */
	public function export(){
		$id = $this->input->get('id');
        $this->load->model('program');
        $template = $this->program->get_interaction($id);
        $tree = $this->program->get_interaction_tree($id);
        if ($template) {
			$area_list = $this->program->get_interaction_area_list($id);
			$xml ='<?xml version="1.0" encoding="UTF-8"?>'.$this->sep;
			$xml .= '<template>'.$this->sep;
			$xml .= $this->tab.'<name><![CDATA['.$template->name.']]></name>'.$this->sep;
			$xml .= $this->tab.'<descr><![CDATA['.$template->descr.']]></descr>'.$this->sep;
			$xml .= $this->tab.'<type>'.$template->type.'</type>'.$this->sep;
			$xml .= $this->tab.'<width>'.$template->width.'</width>'.$this->sep;
			$xml .= $this->tab.'<height>'.$template->height.'</height>'.$this->sep;
			$xml .= $this->tab.'<w>'.$template->w.'</w>'.$this->sep;
			$xml .= $this->tab.'<h>'.$template->h.'</h>'.$this->sep;
			$xml .= $this->tab.'<flag>'.$template->save_flag.'</flag>'.$this->sep;
			$xml .= $this->tab.'<period>'.$template->period.'</period>'.$this->sep;
			$xml .= $this->tab.'<action>'.$template->action.'</action>'.$this->sep;
			$xml .= $this->tab.'<tree_json><![CDATA['.$tree->tree_json.']]></tree_json>'.$this->sep;
			$xml .= $this->tab.'<lastcount>'.$tree->lastcount.'</lastcount>'.$this->sep;
			$xml .= $this->tab.'<areas>'.$this->sep;
			if($area_list){
				foreach($area_list as $a){
					$xml .= $this->tab2.'<area>'.$this->sep;
					$xml .= $this->tab3.'<name><![CDATA['.$a->name.']]></name>'.$this->sep;
					$xml .= $this->tab3.'<area_type>'.$a->area_type.'</area_type>'.$this->sep;
					$xml .= $this->tab3.'<x>'.$a->x.'</x>'.$this->sep;
					$xml .= $this->tab3.'<y>'.$a->y.'</y>'.$this->sep;
					$xml .= $this->tab3.'<w>'.$a->w.'</w>'.$this->sep;
					$xml .= $this->tab3.'<h>'.$a->h.'</h>'.$this->sep;
					$xml .= $this->tab3.'<zindex>'.$a->zindex.'</zindex>'.$this->sep;
					$xml .= $this->tab3.'<page_id>'.$a->page_id.'</page_id>'.$this->sep;
					$xml .= $this->tab3.'<page_name><![CDATA['.$a->page_name.']]></page_name>'.$this->sep;
					$xml .= $this->tab3.'<action>'.$a->action.'</action>'.$this->sep;
					$xml .= $this->tab3.'<num>'.$a->num.'</num>'.$this->sep;
					$xml .= $this->tab2.'</area>'.$this->sep;
				}
			}
			$xml .= $this->tab.'</areas>'.$this->sep;
			$xml .= '</template>';
			$xml .= '<!--'.md5($xml.'miatek').'-->';
			$this->load->helper('file');
			if(downloadContent($xml, 'xml', 0, 'Touch.Template.'.$template->name.'.xml')){
				return;
			}
        } 
        $this->show_msg($this->lang->line('param.error'), 'error');
	}
	
	public function import(){
		$config['upload_path'] = $this->config->item('tmp');
        $config['allowed_types'] = '*';
        $config['encrypt_name'] = TRUE;
        
        $this->load->model('program');
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
		if (!$this->upload->do_upload('Filedata')) {
            $result = array('code'=>1, 'msg'=>$this->upload->display_errors());
        } else {
            $data = $this->upload->data();
        	if(substr($data['orig_name'], 0, 15) != 'Touch.Template.' || substr($data['orig_name'],-4) != '.xml'){
				$result = array('code'=>1, 'msg'=>$this->lang->line('warn.touch.template.name'));
			}else {
				$template_name = substr($data['orig_name'], 15, strlen($data['orig_name'])-19);
				$flag = $this->program->get_interaction_by_name(0, $this->get_cid(), $template_name);
				if($flag) {
					$result = array('code'=>1, 'msg'=>$this->lang->line('warn.touch.import.name'));
				}else {
					$template = $this->parse_interaction($data['full_path'], TRUE, $template_name);
					if($template){
		            	if($template['type'] >= 0) {
		            		@unlink($data['full_path']);
		            		$result['template']=$template;
							$cid = $this->get_cid();
							$uid = $this->get_uid();
							$id = $this->program->add_interaction(array_slice($template, 0, -3), $cid, $uid);
							if($id){
								$areas = $template['areas'];
								if(count($areas)){
									foreach($areas as $area){
										$area_id = $this->program->add_interaction_area($area, $id);
										if($area['area_type'] == 9) {
											$this->program->add_interaction_area_image_setting(array('area_id'=>$area_id, 'media_id'=>1), $uid);
										}
									}
								}
								$tree_id = $this->program->add_interaction_tree(array('tree_json'=>$template['tree_json'], 'pls_tree_json'=>$template['tree_json'], 'lastcount'=>$template['lastcount'], 'add_user_id'=>$uid, 'interaction_id'=>$id));
								$this->load->library('image');
								$this->program->update_touch_template_preview_url($id);
								$result['code'] = 0;
								$result['msg']=$this->lang->line('template.import.success');
							}else{
								$result['code'] = 1;
								$result['msg']=$this->lang->line('warn.template.import.fail');
							}
		            	}else {
		            		$result = array('code'=>2, 'msg'=>'test', 'path'=>$data['full_path']);
		            	}
					}else{
						$result = array('code'=>1, 'msg'=>$this->lang->line('warn.template.import.format'));
					}
				}
        	}
		}
		//@unlink($_FILES['Filedata']['tmp_name']);
		echo json_encode($result);
	}
	
	public function html5_import(){
		$config['upload_path'] = $this->config->item('tmp');
		$config['allowed_types'] = '*';
		$config['encrypt_name'] = TRUE;
	
		$this->load->model('program');
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if (!$this->upload->do_upload('qqfile')) {
			$result = array('success'=>false, 'code'=>1, 'msg'=>$this->upload->display_errors());
		} else {
			$data = $this->upload->data();
			if(substr($data['orig_name'], 0, 15) != 'Touch.Template.' || substr($data['orig_name'],-4) != '.xml'){
				$result = array('success'=>false, 'code'=>1, 'msg'=>$this->lang->line('warn.touch.template.name'));
			}else {
				$template_name = substr($data['orig_name'], 15, strlen($data['orig_name'])-19);
				$flag = $this->program->get_interaction_by_name(0, $this->get_cid(), $template_name);
				if($flag) {
					$result = array('success'=>false, 'code'=>1, 'msg'=>$this->lang->line('warn.touch.import.name'));
				}else {
					$template = $this->parse_interaction($data['full_path'], TRUE, $template_name);
					if($template){
						if($template['type'] >= 0) {
							@unlink($data['full_path']);
							$result['template']=$template;
							$cid = $this->get_cid();
							$uid = $this->get_uid();
							$id = $this->program->add_interaction(array_slice($template, 0, -3), $cid, $uid);
							if($id){
								$areas = $template['areas'];
								if(count($areas)){
									foreach($areas as $area){
										$area_id = $this->program->add_interaction_area($area, $id);
										if($area['area_type'] == 9) {
											$this->program->add_interaction_area_image_setting(array('area_id'=>$area_id, 'media_id'=>1), $uid);
										}
									}
								}
								$tree_id = $this->program->add_interaction_tree(array('tree_json'=>$template['tree_json'], 'pls_tree_json'=>$template['tree_json'], 'lastcount'=>$template['lastcount'], 'add_user_id'=>$uid, 'interaction_id'=>$id));
								$this->load->library('image');
								$this->program->update_touch_template_preview_url($id);
								$result['success'] = true;
								$result['code'] = 0;
								$result['msg']=$this->lang->line('template.import.success');
							}else{
								$result['success'] = false;
								$result['code'] = 1;
								$result['msg']=$this->lang->line('warn.template.import.fail');
							}
						}else {
							$result = array('success'=>false, 'code'=>2, 'msg'=>'test', 'path'=>$data['full_path']);
						}
					}else{
						$result = array('success'=>false, 'code'=>1, 'msg'=>$this->lang->line('warn.template.import.format'));
					}
				}
			}
		}
		//@unlink($_FILES['Filedata']['tmp_name']);
		echo json_encode($result);
	}
	
	private function parse_interaction($file_name, $flag = TRUE, $interaction_name = FALSE){
		if ($f = @fopen($file_name, 'r')) {
            $xml = '';
            while (!feof($f)) {
                $xml .= fgets($f, 4096);
            }
            fclose($f);
			$sig_len=39;
			if(strlen($xml) <= $sig_len){
				return FALSE;
			}
			//<!--c6b6a49685bbe4f11bf085a996ad72fb-->
			$sig=substr($xml,-$sig_len);
			if(substr($sig, 0, 4) != '<!--' || substr($sig,-3) != '-->'){
				return FALSE;
			}
			if(md5(substr($xml, 0, -$sig_len).'miatek') != substr($sig, 4, -3)){
				return FALSE;
			}
			
            $dom = new DOMDocument();
            if (@$dom->loadXML($xml)) {
                $template = $dom->getElementsByTagName('template');
                if ($template->length) {
                
                    $t = $template->item(0);
                    //$name = trim($t->getElementsByTagName('name')->item(0)->nodeValue);
                    $name = $interaction_name;
                    $descr = trim($t->getElementsByTagName('descr')->item(0)->nodeValue);
                    if($flag) {
                    	if($t->getElementsByTagName('type')->length) {
	                    	$template_type = trim($t->getElementsByTagName('type')->item(0)->nodeValue);
	                    }else {
	                    	$template_type = -1;
	                    }
                    }else {
                    	$template_type = '';
                    }
                    $width = trim($t->getElementsByTagName('width')->item(0)->nodeValue);
                    $height = trim($t->getElementsByTagName('height')->item(0)->nodeValue);
                    $w = trim($t->getElementsByTagName('w')->item(0)->nodeValue);
                    $h = trim($t->getElementsByTagName('h')->item(0)->nodeValue);
                    $flag = trim($t->getElementsByTagName('flag')->item(0)->nodeValue);
                    $period = trim($t->getElementsByTagName('period')->item(0)->nodeValue);
                    $action = trim($t->getElementsByTagName('action')->item(0)->nodeValue);
                    $tree_json = trim($t->getElementsByTagName('tree_json')->item(0)->nodeValue);
                    $lastcount = trim($t->getElementsByTagName('lastcount')->item(0)->nodeValue);
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
                            $apid = trim($a->getElementsByTagName('page_id')->item(0)->nodeValue);
                            $apname = trim($a->getElementsByTagName('page_name')->item(0)->nodeValue);
                            $aaction = trim($a->getElementsByTagName('action')->item(0)->nodeValue);
                            $anum = trim($a->getElementsByTagName('num')->item(0)->nodeValue);
                            if ($aname && is_numeric($ax) && is_numeric($ay) && is_numeric($aw) && is_numeric($ah)) {
                                $areas[] = array('name'=>$aname, 'x'=>$ax, 'y'=>$ay, 'w'=>$aw, 'h'=>$ah,'area_type'=>$area_type, 'zindex'=>$az, 'page_id'=>$apid, 'page_name'=>$apname, 'action'=>$aaction, 'num'=>$anum);
                            }
                        }
                        
                        $tm = array();
                        $tm['name'] = $name;
                        $tm['descr'] = $descr;
                        $tm['type'] = $template_type;
                        $tm['width'] = $width;
                        $tm['height'] = $height;
                        $tm['w'] = $w;
                        $tm['h'] = $h;
                        $tm['save_flag'] = $flag;
                        $tm['period'] = $period;
                        $tm['action'] = $action;
                        $tm['tree_json'] = $tree_json;
						$tm['lastcount'] = $lastcount;
                        $tm['areas'] = $areas;
                       
						return $tm;
                    }
                    
                }
            }
        }
		
		return FALSE;
	}
	
	/**
	 * 显示时间设置
	 * @return
	 */
	public function times() {
		$this->lang->load('font');
		$data['type'] = $this->input->get('type');
		$data['formats'] = $this->lang->line('template.screen.time.format.list');
		$fl = $this->lang->line('font.family.list');
		$data['familys'] = $fl;
		$fsl = $this->lang->line('font.size.list');
		$data['sizes'] = $fsl;

		$fm = $this->lang->line('template.screen.time.format.map');

		//default
		$data['family'] = $fl[0];
		$data['size'] = $fsl[20];
		$data['preview'] = date($fm[1], time());

		$this->load->view('program/interaction/screen_time_setting', $data);
	}

	/**
	 * 显示按钮组设置
	 * @return
	 */
	public function btnGroups() {
		$data = array ();
		$this->load->view('program/interaction/screen_btnGroup_setting', $data);
	}

	public function get_time_format($format) {
		if ($format === FALSE) {
			$format = 1;
		}
		$fm = $this->lang->line('template.screen.time.format.map');
		echo date($fm[$format], time());
	}

	public function weathers() {
		$this->lang->load('font');
		$data['type'] = $this->input->get('type');
		$data['formats'] = $this->lang->line('template.screen.weather.format.list');
		$fl = $this->lang->line('font.family.list');
		$data['familys'] = $fl;
		$fsl = $this->lang->line('font.size.list');
		$data['sizes'] = $fsl;

		//default
		$data['family'] = $fl[0];
		$data['size'] = 20;

		$this->load->view('program/interaction/screen_weather_setting', $data);
	}

	/**
	* 执行删除某个互动应用
	* @return 
	*/
	public function do_delete() {
		$result = array ();
		$id = $this->input->post("id");
		$this->load->model('program');
		if ($this->program->delete_interaction($id)) {
			$result['code'] = 0;
			$result['msg'] = $this->lang->line('delete.success');
		} else {
			$result['code'] = 1;
			$result['msg'] = $this->lang->line('delete.fail');
		}

		echo json_encode($result);
	}

	public function images($curpage = 1) {
		$this->lang->load('folder');
		$cid = $this->get_cid();
		$folder_id = $this->input->get('folder_id');
		$screenID = $this->input->get('screenID');
		if ($folder_id < 0) {
			$folder_id = false;
		}
		$limit = $this->config->item('page_template_image_size');
		$offset = ($curpage -1) * $limit;
		$this->load->model('material');
		$this->load->model('device');
		$this->load->model('membership');
		$order_item = 'id';
		$order = 'desc';
		$filter = array ();
		$folders_arr = array ();
		$add_user_id = FALSE;
		$folders = '';
		//查询用户是否被分配root文件夹

		$folders_arr = array ();
		$folders = $this->device->get_folder_ids($this->get_uid()); //获取用户分配的文件夹
		if (is_array($folders)) {
			for ($i = 0; $i < count($folders); $i++) {
				$folders_arr[] = $folders[$i];
			}
		}

		$flag = $this->device->get_rootFolder_id($this->get_uid());
		$auth = $this->get_auth(); //获取用户的权限
		if ($auth == $this->config->item('auth_admin')) {
			$medias = $this->material->get_media_list($this->get_cid(), $this->config->item('media_type_image'), $bmp = 'notbmp', $offset, $limit, $order_item, $order, $filter_array = array (), $folder_id, FALSE, FALSE);
		}
		if ($auth == $this->config->item('auth_group') || $auth == $this->config->item('auth_franchise')) {
			$cid = $this->get_cid(); //公司id
			$admin_id = $this->membership->get_admin_by_cid($cid); //根据公司id获取Admin的id
			$uid = $this->get_uid(); //用户id
			$group_userId = $this->device->get_all_user_by_usergroup($uid); //获取用户所在的组 的所有用户
			$medias = $this->material->group_manager_get_media_list($cid, $this->config->item('media_type_image'), $bmp = 'notbmp', $admin_id, $uid, $group_userId, $offset, $limit, $order_item, $order, $filter_array = array (), $folder_id, $folders, FALSE);
		}

		$data = $this->get_data();
		$data['folders'] = $this->material->get_all_folder_list($this->get_cid(), $folders_arr, $add_user_id);
		$data['total'] = $medias['total'];
		$data['data'] = $medias['data'];
		$data['limit'] = $limit;
		$data['curpage'] = $curpage;
		$data['folder_id'] = $folder_id;
		$data['flag'] = $flag;
		$data['screenID'] = $screenID;

		$data['tables'] = 'program/interaction/images_table';
		$data['type'] = $this->input->get('type');

		$this->load->view('program/interaction/images', $data);
	}

	/**
	 * 制作互动应用模板
	 */
	public function create_interaction_date() {
		$this->addJs('interaction.js');
		$this->addJs('colorpicker/colorpicker.js');
		$this->addJs('colorpicker/eye.js');
		$this->addJs('colorpicker/utils.js');
		$this->addJs('jquery/jquery-ui-latest.js');
		$this->addJs('ztree/jquery.ztree.core-3.5.js');
		$this->addJs('ztree/jquery.ztree.excheck-3.5.js');
		$this->addJs('ztree/jquery.ztree.exedit-3.5.js');
		$this->addCss('zstyle/zstyle.css');
		$this->addCss('template.css');
		$this->addCss('colorpicker/layout.css');
		$this->addCss('colorpicker/colorpicker.css');
		$this->addCss("jquery/jquery-ui-1.8.16.custom.css");

		$id = $this->input->get('id');
		$this->load->model('program');
		$interaction = $this->program->get_interaction($id);
		$using = $this->program->is_touch_template_using($id);
		//$area_list = $this->program->get_area_list($id);
		$area_list = $this->program->get_interaction_area_list($id);
		$tree = $this->program->get_interaction_tree($id);
		$action_list = $this->lang->line('touch.timeout.action.list');
		$fill_list = $this->lang->line('page.fill.list');
		$model_list = $this->lang->line('page.model.list');
		$style_list = $this->lang->line('btn.style.list');
		$screen_list = $this->lang->line('btn.screen.list');
		$close_list = $this->lang->line('btn.close.list');
		$btn_action_list = $this->lang->line('btn.action.list');
		if ($area_list) {
			foreach ($area_list as $area) {
				if ($area->area_type == $this->config->item('area_type_bg')) {
					$bgs = $this->program->get_interaction_area_image_setting($area->id);
					if ($bgs) {
						$area->media_id = $bgs->media_id;
						$area->main_url = $bgs->main_url;
					} else {
						$area->media_id = 0;
						$area->main_url = '';
					}
				} else
					if ($area->area_type == $this->config->item('area_type_logo')) {
						$bgs = $this->program->get_interaction_area_image_setting($area->id);
						if ($bgs) {
							$area->media_id = $bgs->media_id;
							$area->tiny_url = $bgs->tiny_url;
						} else {
							$area->media_id = 0;
							$area->tiny_url = '';
						}
					}
			}
			//获取treeNodeCount
			//$treeNodeCount = $this->program->get_interaction_tree($id)->page_id + 1;
			//获取screenID
			$screenID = $this->program->get_interaction_screenID($id)->page_id + 1;
			$treeNodeCount = $screenID;
		} else {
			//获取treeNodeCount
			$treeNodeCount = 3;
			//获取screenID
			$screenID = 2;
		}
		$data = $this->get_data();
		$data['area_list'] = $area_list;
		$data['action_list'] = $action_list;
		$data['fill_list'] = $fill_list;
		$data['model_list'] = $model_list;
		$data['style_list'] = $style_list;
		$data['screen_list'] = $screen_list;
		$data['close_list'] = $close_list;
		$data['btn_action_list'] = $btn_action_list;
		$data['body_file'] = 'program/interaction/add_interaction_data';
		$data['using'] = $using;
		$data['id'] = $id;
		$data['interaction'] = $interaction;
		
		if (empty ($tree)) {
			$data['treejson'] = null;
			$treeNodeCount = 3;
			$screenID = 2;
		} else {
			$data['treejson'] = $tree->tree_json;
			$screenID = $tree->lastcount + 1;
			$treeNodeCount = $screenID;
		}
		
		$data['treeNodeCount'] = $treeNodeCount;
		$data['screenID'] = $screenID;

		if ($interaction) {
			$data['width'] = $interaction->width / 2;
			$data['height'] = $interaction->height / 2;
		} else {
			$data['width'] = $this->config->item('screen_width');
			$data['height'] = $this->config->item('screen_height');
		}
		$this->load->view('include/main2', $data);
	}

	public function save_screen() {
		$movies = $this->input->post("movie");
		$images = $this->input->post("image");
		$texts = $this->input->post("text");
		$dates = $this->input->post("date");
		$times = $this->input->post("time");
		$weathers = $this->input->post("weather");
		$logo = $this->input->post("logo");
		$webpages = $this->input->post("webpage");
		$staticTexts = $this->input->post("staticText");
		$btns = $this->input->post("btn");
		$bgs = $this->input->post("bgimg");
		$screens = $this->input->post("screen");
		$id = $this->input->post("id");
		$treejson = $this->input->post("treejson");
		$treejsonpls = $this->input->post("treejsonpls");
		$changeId = $this->input->post("changeid");
		$pchangeId = $this->input->post("pchangeid");
		$lastCount = $this->input->post("lastCount");
		$deletes = $this->input->post("deletes");
		$changeAreaId = $this->input->post("changeAreaId");
		$result = array ();
		if (empty ($screens) || (empty ($images) && empty ($movies) && empty ($texts) && empty ($dates) && empty ($times) && empty ($weathers) && empty ($webpages) && empty ($staticTexts) && empty ($btns))) {
			$result['code'] = 1;
			$result['msg'] = $this->lang->line('template.error.screen.empty');
		} else {
			$this->load->model('program');
			if (!empty ($deletes)) {
				$d = json_decode($deletes, true);
				if ($d) {
					foreach ($d as $area) {
						$this->program->delete_interaction_area($area['id'], $this->config->item('area_type_' . $area['type']));
					}
				}
				unset ($d);
			}
			$this->program->update_interaction(array (
				'name' => $this->input->post("touchName"),
				'period' => $this->input->post("period"),
				'action' => $this->input->post("action")
			), $id);
			//load image lib for template
			$this->load->library('image');

			$bg_file = FALSE;
			$pwidth = $this->config->item('template_preview_width');
			$pheight = $this->config->item('template_preview_height');
			$swidth = 800; //屏幕宽度
			$sheight = 600; //屏幕高度

			if (!empty ($screens)) {
				$d = json_decode($screens, true);
				$swidth = $d['w'];
				$sheight = $d['h'];
				unset ($d);
			}
			if ($swidth < $sheight) {
				$pwidth = $this->config->item('template_preview_reverse_width');
			}
			$rwidth = $pwidth / $swidth; //宽度比
			$rheight = $pheight / $sheight; //高度比
			$this->load->model('material');
			$tmp_arr = array ();

			//树形结构
			if (!empty ($treejson)) {
				$tree = $this->program->get_interaction_tree($id);
				if ($tree) {
					$array = array (
						'tree_json' => $treejson,
						'add_user_id' => $this->get_uid(),
						'lastcount' => $lastCount,
						'pls_tree_json' => $treejsonpls
					);
					$this->program->update_interaction_tree($array, $id);
				} else {
					$array = array (
						'interaction_id' => $id,
						'tree_json' => $treejson,
						'add_user_id' => $this->get_uid(),
						'lastcount' => $lastCount,
						'pls_tree_json' => $treejsonpls
					);
					$this->program->add_interaction_tree($array);
				}
				$pages_list = array ();
				$areas_list = array ();
				$tmp_json = str_replace('},', '}@<>#', substr($treejson, 0, -1));
				$tmp_json = str_replace('id', '"id"', $tmp_json);
				$tmp_json = str_replace('pId', '"pId"', $tmp_json);
				$tmp_json = str_replace('name', '"name"', $tmp_json);
				$tmp_json = str_replace('checked', '"checked"', $tmp_json);
				$tmp_json = str_replace('open', '"open"', $tmp_json);
				$tmp_json = str_replace('iconSkin', '"iconSkin"', $tmp_json);
				$tmp_json = str_replace('noR', '"noR"', $tmp_json);
				$tmp_arr = explode('@<>#', $tmp_json);

			}
			//背景区域
			if (!empty ($bgs)) {
				foreach ($bgs as $bg) {
					$d = json_decode($bg, true);
					if ($d) {
						$dv['w'] = $d['w'];
						$dv['h'] = $d['h'];
						$dv['x'] = $d['x'];
						$dv['y'] = $d['y'];
						$area_id = $this->get_value($d, 'areaId');
						$name = $this->get_value($d, 'name');
						$screenID = $this->get_value($d, 'screenID');
						if ($changeId != '') {
							$arr = array_unique(preg_split('/,/', $changeId));
							$arr2 = sort($arr);
							for ($i = 0; $i < count($arr); $i++) {
								$tmp_c_id = preg_split('/_/', $arr[$i]);
								if ($screenID == $tmp_c_id[1]) {
									$screenID = $tmp_c_id[0];
								}
							}
						}

						for ($i = 0; $i < count($tmp_arr); $i++) {
							$arr = json_decode($tmp_arr[$i], true);
							if ($arr['id'] == $screenID) {
								$pageName = $arr['name'];
								break;
							}
						}
						$dv['page_name'] = $pageName;
						if (empty ($name)) {
							$name = $this->lang->line('template.screen.bg');
						}
						$dv['name'] = $name;
						$dv['area_type'] = $this->config->item('area_type_bg');
						$dv['page_id'] = $screenID;
						$dv['zindex'] = -1;
						//设置
						$ds['media_id'] = $d['media_id'];

						$media = $this->material->get_media($d['media_id']);
						if ($media) {
							$bg_file = '.' . $media->main_url;
						}

						if ($area_id) {
							$this->program->update_interaction_area($dv, $area_id);
							$this->program->update_interaction_area_image_setting($ds, $area_id);
						} else {
							$ba = $this->program->get_interaction_bg_area($id, $screenID);
							if ($ba) {
								$area_id = $ba->id;
								//$this->program->update_interaction_area($dv, $area_id);
								$this->program->update_interaction_area_image_setting($ds, $area_id);
							} else {
								$area_id = $this->program->add_interaction_area($dv, $id);
								$ds['area_id'] = $area_id;
								$this->program->add_interaction_area_image_setting($ds, $this->get_uid());
							}
						}
						$dv['area_id'] = $area_id;
						$result['bg'] = $dv;
						if ($screenID == 2) {
							$this->image->create($pwidth, $pheight, $bg_file);
						}
						unset ($dv);
						unset ($ds);
					}
					unset ($d);
				}
			} else {
				$this->image->create($pwidth, $pheight, null);
			}

			//静态文字区域 staticText
			if (!empty ($staticTexts)) {
				foreach ($staticTexts as $staticText) {
					$m = json_decode($staticText, true);
					if ($m) {
						$dv['w'] = $m['w'];
						$dv['h'] = $m['h'];
						$dv['x'] = $m['x'];
						$dv['y'] = $m['y'];
						$area_id = $this->get_value($m, 'areaId');
						$name = $this->get_value($m, 'name');
						$screenID = $this->get_value($m, 'screenID');
						$zindex = $this->get_value($m, 'zindex');
						if ($zindex === FALSE) {
							$zindex = 10;
						}

						if ($changeId != '') {
							$arr = array_unique(preg_split('/,/', $changeId));
							$arr2 = sort($arr);
							for ($i = 0; $i < count($arr); $i++) {
								$tmp_c_id = preg_split('/_/', $arr[$i]);
								if ($screenID == $tmp_c_id[1]) {
									$screenID = $tmp_c_id[0];
								}
							}
						}

						for ($i = 0; $i < count($tmp_arr); $i++) {
							$arr = json_decode($tmp_arr[$i], true);
							if ($arr['id'] == $screenID) {
								$pageName = $arr['name'];
								break;
							}
						}
						$dv['page_name'] = $pageName;
						$dv['zindex'] = $zindex;
						$dv['page_id'] = $screenID;
						if (empty ($name)) {
							$name = $this->lang->line('template.screen.staticText');
						}

						$dv['name'] = $name;
						$dv['area_type'] = $this->config->item('area_type_staticText');
						if ($area_id) {
							$this->program->update_interaction_area($dv, $area_id);
						} else {
							$area_id = $this->program->add_interaction_area($dv, $id);
						}
						$dv['area_id'] = $area_id;
						$result['staticText'] = $dv;

						//设置预览区域
						if ($screenID == 2) {
							$this->image->add_area($dv['x'] * $rwidth, $dv['y'] * $rheight, $dv['w'] * $rwidth, $dv['h'] * $rheight, $name, $this->config->item('area_type_staticText_color'));
						}
						unset ($dv);
					}
					unset ($m);
				}
			}

			//视频区域
			if (!empty ($movies)) {
				foreach ($movies as $movie) {
					$m = json_decode($movie, true);
					if ($m) {
						$dv['w'] = $m['w'];
						$dv['h'] = $m['h'];
						$dv['x'] = $m['x'];
						$dv['y'] = $m['y'];
						$area_id = $this->get_value($m, 'areaId');
						$name = $this->get_value($m, 'name');
						$zindex = $this->get_value($m, 'zindex');
						$screenID = $this->get_value($m, 'screenID');
						if ($zindex === FALSE) {
							$zindex = 10;
						}

						if ($changeId != '') {
							$arr = array_unique(preg_split('/,/', $changeId));
							$arr2 = sort($arr);
							for ($i = 0; $i < count($arr); $i++) {
								$tmp_c_id = preg_split('/_/', $arr[$i]);
								if ($screenID == $tmp_c_id[1]) {
									$screenID = $tmp_c_id[0];
								}
							}
						}

						for ($i = 0; $i < count($tmp_arr); $i++) {
							$arr = json_decode($tmp_arr[$i], true);
							if ($arr['id'] == $screenID) {
								$pageName = $arr['name'];
								break;
							}
						}
						$dv['page_name'] = $pageName;
						$dv['zindex'] = $zindex;
						$dv['page_id'] = $screenID;
						if (empty ($name)) {
							$name = $this->lang->line('template.screen.movie');
						}
						$dv['name'] = $name;
						$dv['area_type'] = $this->config->item('area_type_movie');
						if ($area_id) {
							$this->program->update_interaction_area($dv, $area_id);
						} else {
							$area_id = $this->program->add_interaction_area($dv, $id);
						}

						$dv['area_id'] = $area_id;
						$result['movie'] = $dv;

						//set preview image
						if ($screenID == 2) {
							$this->image->add_area($dv['x'] * $rwidth, $dv['y'] * $rheight, $dv['w'] * $rwidth, $dv['h'] * $rheight, $name, $this->config->item('area_type_movie_color'));
						}
						unset ($dv);
					}
					unset ($m);
				}
			}

			//照片区域
			if (!empty ($images)) {
				$ii = 1;
				foreach ($images as $image) {
					$img = json_decode($image, true);
					if ($img) {
						$dv['w'] = $img['w'];
						$dv['h'] = $img['h'];
						$dv['x'] = $img['x'];
						$dv['y'] = $img['y'];
						$area_id = $this->get_value($img, 'areaId');
						$name = $this->get_value($img, 'name');
						$zindex = $this->get_value($img, 'zindex');
						$screenID = $this->get_value($img, 'screenID');
						$num = $this->get_value($img, 'num');
						if ($zindex === FALSE) {
							$zindex = 10;
						}

						if ($changeId != '') {
							$arr = array_unique(preg_split('/,/', $changeId));
							$arr2 = sort($arr);
							for ($i = 0; $i < count($arr); $i++) {
								$tmp_c_id = preg_split('/_/', $arr[$i]);
								if ($screenID == $tmp_c_id[1]) {
									$screenID = $tmp_c_id[0];
								}
							}
						}

						for ($i = 0; $i < count($tmp_arr); $i++) {
							$arr = json_decode($tmp_arr[$i], true);
							if ($arr['id'] == $screenID) {
								$pageName = $arr['name'];
								break;
							}
						}
						$dv['page_name'] = $pageName;
						$dv['zindex'] = $zindex;
						$dv['page_id'] = $screenID;
						if (empty ($name)) {
							$name = $this->lang->line('template.screen.image') . $ii;
						}
						$dv['name'] = $name;
						$dv['num'] = $num;
						$dv['area_type'] = $this->config->item('area_type_image');
						if ($area_id) {
							$this->program->update_interaction_area($dv, $area_id);
						} else {
							$area_id = $this->program->add_interaction_area($dv, $id);
						}

						$dv['area_id'] = $area_id;
						$result['image'][] = $dv;

						//set preview image
						if ($screenID == 2) {
							$this->image->add_area($dv['x'] * $rwidth, $dv['y'] * $rheight, $dv['w'] * $rwidth, $dv['h'] * $rheight, $name, $this->config->item('area_type_image'.$num.'_color'));
						}
						unset ($dv);
					}

					$ii++;
					unset ($img);
				}
			}

			//btn
			if (!empty ($btns)) {
				$i = 1;
				foreach ($btns as $btn) {
					$b = json_decode($btn, true);
					if ($b) {
						$dv['w'] = $b['w'];
						$dv['h'] = $b['h'];
						$dv['x'] = $b['x'];
						$dv['y'] = $b['y'];
						$area_id = $this->get_value($b, 'areaId');
						$name = $this->get_value($b, 'name');
						$zindex = $this->get_value($b, 'zindex');
						$screenID = $this->get_value($b, 'screenID');
						$num = $this->get_value($b, 'num');
						if ($zindex === FALSE) {
							$zindex = 10;
						}

						if ($changeId != '') {
							$arr = array_unique(preg_split('/,/', $changeId));
							$arr2 = sort($arr);
							for ($i = 0; $i < count($arr); $i++) {
								$tmp_c_id = preg_split('/_/', $arr[$i]);
								if ($screenID == $tmp_c_id[1]) {
									$screenID = $tmp_c_id[0];
								}
							}
						}

						for ($i = 0; $i < count($tmp_arr); $i++) {
							$arr = json_decode($tmp_arr[$i], true);
							if ($arr['id'] == $screenID) {
								$pageName = $arr['name'];
								break;
							}
						}
						$dv['page_name'] = $pageName;
						$dv['zindex'] = $zindex;
						$dv['page_id'] = $screenID;
						$dv['num'] = $num;
						if (empty ($name)) {
							$name = $this->lang->line('interaction.screen.btn');
						}
						$dv['name'] = $name;

						$dv['area_type'] = $this->config->item('area_type_btn');
						if ($area_id) {
							$this->program->update_interaction_area($dv, $area_id);
						} else {
							$area_id = $this->program->add_interaction_area($dv, $id);
						}

						$dv['area_id'] = $area_id;
						$result['btn'][] = $dv;
						if ($screenID == 2) {
							$this->image->add_area($dv['x'] * $rwidth, $dv['y'] * $rheight, $dv['w'] * $rwidth, $dv['h'] * $rheight, $name, $this->config->item('area_type_btn_color'));
						}
						unset ($dv);
					}

					$i++;
					unset ($b);
				}
			}

			//Webpage区域
			if (!empty ($webpages)) {
				foreach ($webpages as $webpage) {
					$d = json_decode($webpage, true);
					if ($d) {
						$dv['w'] = $d['w'];
						$dv['h'] = $d['h'];
						$dv['x'] = $d['x'];
						$dv['y'] = $d['y'];
						$dv['name'] = $this->lang->line('template.screen.webpage');
						$dv['area_type'] = $this->config->item('area_type_webpage');
						$area_id = $this->get_value($d, 'areaId');
						$zindex = $this->get_value($d, 'zindex');
						$screenID = $this->get_value($d, 'screenID');
						if ($zindex === FALSE) {
							$zindex = 10;
						}

						if ($changeId != '') {
							$arr = array_unique(preg_split('/,/', $changeId));
							$arr2 = sort($arr);
							for ($i = 0; $i < count($arr); $i++) {
								$tmp_c_id = preg_split('/_/', $arr[$i]);
								if ($screenID == $tmp_c_id[1]) {
									$screenID = $tmp_c_id[0];
								}
							}
						}

						for ($i = 0; $i < count($tmp_arr); $i++) {
							$arr = json_decode($tmp_arr[$i], true);
							if ($arr['id'] == $screenID) {
								$pageName = $arr['name'];
								break;
							}
						}
						$dv['page_name'] = $pageName;
						$dv['zindex'] = $zindex;
						$dv['page_id'] = $screenID;
						//设置
						if ($area_id) {
							$this->program->update_interaction_area($dv, $area_id);
						} else {

							$area_id = $this->program->add_interaction_area($dv, $id);
						}

						$dv['area_id'] = $area_id;
						$result['webpage'] = $dv;
						//$bg_file = '/images/icons/web.jpg';
						//$bg_file = $this->config->item('base_path').'/images/icons/web.jpg';
						//设置预览区域
						if ($screenID == 2) {
							$this->image->add_area($dv['x'] * $rwidth, $dv['y'] * $rheight, $dv['w'] * $rwidth, $dv['h'] * $rheight, $dv['name'], $this->config->item('area_type_webpage_color'), $this->config->item('area_border_color'));
						}
						unset ($dv);
						unset ($ds);
					}
					unset ($d);
				}
			}

			//日期区域
			if (!empty ($dates)) {
				foreach ($dates as $date) {
					$d = json_decode($date, true);
					if ($d) {
						$dv['w'] = $d['w'];
						$dv['h'] = $d['h'];
						$dv['x'] = $d['x'];
						$dv['y'] = $d['y'];
						$area_id = $this->get_value($d, 'areaId');
						$name = $this->get_value($d, 'name');
						$zindex = $this->get_value($d, 'zindex');
						$screenID = $this->get_value($d, 'screenID');
						if ($zindex === FALSE) {
							$zindex = 10;
						}

						if ($changeId != '') {
							$arr = array_unique(preg_split('/,/', $changeId));
							$arr2 = sort($arr);
							for ($i = 0; $i < count($arr); $i++) {
								$tmp_c_id = preg_split('/_/', $arr[$i]);
								if ($screenID == $tmp_c_id[1]) {
									$screenID = $tmp_c_id[0];
								}
							}
						}

						for ($i = 0; $i < count($tmp_arr); $i++) {
							$arr = json_decode($tmp_arr[$i], true);
							if ($arr['id'] == $screenID) {
								$pageName = $arr['name'];
								break;
							}
						}
						$dv['page_name'] = $pageName;
						$dv['zindex'] = $zindex;
						$dv['page_id'] = $screenID;
						if (empty ($name)) {
							$name = $this->lang->line('template.screen.date');
						}
						$dv['name'] = $name;
						$dv['area_type'] = $this->config->item('area_type_date');
						//设置
						$ds = array ();
						if (!empty ($d['setting'])) {
							$ds['format'] = $d['setting']['format'];
							$ds['font_family'] = $d['setting']['family'];
							$ds['color'] = $d['setting']['color'];
							$ds['bold'] = $d['setting']['bold'];
							$ds['font_size'] = $d['setting']['font_size'];
						}

						if ($area_id) {
							$this->program->update_interaction_area($dv, $area_id);
						} else {

							$area_id = $this->program->add_interaction_area($dv, $id);
							$ds['area_id'] = $area_id;
						}

						$dv['area_id'] = $area_id;
						$result['date'] = $dv;
						//设置预览区域
						if ($screenID == 2) {
							$this->image->add_area($dv['x'] * $rwidth, $dv['y'] * $rheight, $dv['w'] * $rwidth, $dv['h'] * $rheight, $name, $this->config->item('area_type_date_color'));
						}
						unset ($dv);
						unset ($ds);
					}
					unset ($d);
				}
			}
			//时间区域
			if (!empty ($times)) {
				foreach ($times as $time) {
					$d = json_decode($time, true);
					if ($d) {
						$dv['w'] = $d['w'];
						$dv['h'] = $d['h'];
						$dv['x'] = $d['x'];
						$dv['y'] = $d['y'];
						$area_id = $this->get_value($d, 'areaId');
						$name = $this->get_value($d, 'name');
						$screenID = $this->get_value($d, 'screenID');
						if (empty ($name)) {
							$name = $this->lang->line('template.screen.time');
						}
						$zindex = $this->get_value($d, 'zindex');
						if ($zindex === FALSE) {
							$zindex = 10;
						}

						if ($changeId != '') {
							$arr = array_unique(preg_split('/,/', $changeId));
							$arr2 = sort($arr);
							for ($i = 0; $i < count($arr); $i++) {
								$tmp_c_id = preg_split('/_/', $arr[$i]);
								if ($screenID == $tmp_c_id[1]) {
									$screenID = $tmp_c_id[0];
								}
							}
						}

						for ($i = 0; $i < count($tmp_arr); $i++) {
							$arr = json_decode($tmp_arr[$i], true);
							if ($arr['id'] == $screenID) {
								$pageName = $arr['name'];
								break;
							}
						}
						$dv['page_name'] = $pageName;
						$dv['zindex'] = $zindex;
						$dv['name'] = $name;
						$dv['area_type'] = $this->config->item('area_type_time');
						$dv['page_id'] = $screenID;
						//设置
						$ds = array ();
						if (!empty ($d['setting'])) {
							$ds['format'] = $d['setting']['format'];
							$ds['font_family'] = $d['setting']['family'];
							$ds['color'] = $d['setting']['color'];
							$ds['bold'] = $d['setting']['bold'];
							$ds['font_size'] = $d['setting']['font_size'];
						}
						if ($area_id) {
							$this->program->update_interaction_area($dv, $area_id);

							if (!empty ($ds)) {
								//$this->program->update_area_time_setting($ds, $area_id);
							}
						} else {
							$area_id = $this->program->add_interaction_area($dv, $id);
							$ds['area_id'] = $area_id;
							//$this->program->add_area_time_setting($ds, $this->get_uid());

						}
						$dv['area_id'] = $area_id;
						$result['time'] = $dv;
						//设置预览区域
						if ($screenID == 2) {
							$this->image->add_area($dv['x'] * $rwidth, $dv['y'] * $rheight, $dv['w'] * $rwidth, $dv['h'] * $rheight, $name, $this->config->item('area_type_time_color'));
						}
						unset ($dv);
						unset ($ds);
					}
					unset ($d);
				}
			}

			//天气区域
			if (!empty ($weathers)) {
				foreach ($weathers as $weather) {
					$d = json_decode($weather, true);
					if ($d) {
						$dv['w'] = $d['w'];
						$dv['h'] = $d['h'];
						$dv['x'] = $d['x'];
						$dv['y'] = $d['y'];
						$area_id = $this->get_value($d, 'areaId');
						$name = $this->get_value($d, 'name');
						$screenID = $this->get_value($d, 'screenID');
						$zindex = $this->get_value($d, 'zindex');
						if ($zindex === FALSE) {
							$zindex = 10;
						}

						if ($changeId != '') {
							$arr = array_unique(preg_split('/,/', $changeId));
							$arr2 = sort($arr);
							for ($i = 0; $i < count($arr); $i++) {
								$tmp_c_id = preg_split('/_/', $arr[$i]);
								if ($screenID == $tmp_c_id[1]) {
									$screenID = $tmp_c_id[0];
								}
							}
						}

						for ($i = 0; $i < count($tmp_arr); $i++) {
							$arr = json_decode($tmp_arr[$i], true);
							if ($arr['id'] == $screenID) {
								$pageName = $arr['name'];
								break;
							}
						}
						$dv['page_name'] = $pageName;
						$dv['zindex'] = $zindex;
						$dv['page_id'] = $screenID;
						if (empty ($name)) {
							$name = $this->lang->line('template.screen.weather');
						}
						$dv['name'] = $name;
						$dv['area_type'] = $this->config->item('area_type_weather');

						//设置
						$ds = array ();
						if (!empty ($d['setting'])) {
							$ds['format'] = $d['setting']['format'];
							$ds['font_family'] = $d['setting']['family'];
							$ds['color'] = $d['setting']['color'];
							$ds['font_size'] = $d['setting']['font_size'];
						}
						if ($area_id) {
							$this->program->update_interaction_area($dv, $area_id);

							if (!empty ($ds)) {
								// $this->program->update_area_weather_setting($ds, $area_id);
							}
						} else {
							$area_id = $this->program->add_interaction_area($dv, $id);
							$ds['area_id'] = $area_id;
						}
						$dv['area_id'] = $area_id;
						$result['weather'] = $dv;
						//设置预览区域
						if ($screenID == 2) {
							$this->image->add_area($dv['x'] * $rwidth, $dv['y'] * $rheight, $dv['w'] * $rwidth, $dv['h'] * $rheight, $name, $this->config->item('area_type_weather_color'));
						}
						unset ($dv);
						unset ($ds);
					}
					unset ($d);
				}
			}

			//文字区域
			if (!empty ($texts)) {
				foreach ($texts as $text) {
					$m = json_decode($text, true);
					if ($m) {
						$dv['w'] = $m['w'];
						$dv['h'] = $m['h'];
						$dv['x'] = $m['x'];
						$dv['y'] = $m['y'];
						$area_id = $this->get_value($m, 'areaId');
						$name = $this->get_value($m, 'name');
						$zindex = $this->get_value($m, 'zindex');
						$screenID = $this->get_value($m, 'screenID');
						if ($zindex === FALSE) {
							$zindex = 10;
						}

						if ($changeId != '') {
							$arr = array_unique(preg_split('/,/', $changeId));
							$arr2 = sort($arr);
							for ($i = 0; $i < count($arr); $i++) {
								$tmp_c_id = preg_split('/_/', $arr[$i]);
								if ($screenID == $tmp_c_id[1]) {
									$screenID = $tmp_c_id[0];
								}
							}
						}

						for ($i = 0; $i < count($tmp_arr); $i++) {
							$arr = json_decode($tmp_arr[$i], true);
							if ($arr['id'] == $screenID) {
								$pageName = $arr['name'];
								break;
							}
						}
						$dv['page_name'] = $pageName;
						$dv['zindex'] = $zindex;
						$dv['page_id'] = $screenID;
						if (empty ($name)) {
							$name = $this->lang->line('template.screen.text');
						}
						$dv['name'] = $name;
						$dv['area_type'] = $this->config->item('area_type_text');
						if ($area_id) {
							$this->program->update_interaction_area($dv, $area_id);
						} else {
							$area_id = $this->program->add_interaction_area($dv, $id);
						}
						$dv['area_id'] = $area_id;
						$result['text'] = $dv;

						//设置预览区域
						if ($screenID == 2) {
							$this->image->add_area($dv['x'] * $rwidth, $dv['y'] * $rheight, $dv['w'] * $rwidth, $dv['h'] * $rheight, $name, $this->config->item('area_type_text_color'));
						}
						unset ($dv);
					}
					unset ($m);
				}
			}

			$template_preview_path = sprintf($this->config->item('tempate_preview_path'), $this->get_cid());
			$absolut_path = $this->config->item('base_path') . $template_preview_path;
			$result['absolut_path'] = $absolut_path;
			if ($this->image->save($absolut_path, 't' . $id . '.jpg')) {
				$result['msg2'] = 'success' . $absolut_path;
				//$this->program->update_template(array('preview_url'=>$template_preview_path.'/'.$id.'.jpg', 'flag'=>1, 'update_time'=>date('Y-m-d H:i:s')), $id);
				$this->program->update_interaction(array (
					'preview_url' => $template_preview_path . '/t' . $id . '.jpg',
					'save_flag' => 1,
					'add_time' => date('Y-m-d H:i:s')
				), $id);
			} else {
				$result['msg2'] = 'fail';
			}

			//$this->program->update_interaction(array('save_flag'=>1, 'add_time'=>date('Y-m-d H:i:s')), $id);
			$result['code'] = 0;
			$result['id'] = $id;
			$result['msg'] = $this->lang->line('save.success');
		}
		echo json_encode($result);
	}
	
	function test() {
		$this->load->model('program');
		$this->load->library('image');
		//$this->program->update_touch_template_preview_url(38);
		$template_id = 38;
		$template = $this->program->get_interaction($template_id);
        if ($template) {
            $rpath = sprintf($this->config->item('tempate_preview_path'), $template->company_id);
            if ($rpath) {
                $pwidth = $this->config->item('template_preview_width');
                $pheight = $this->config->item('template_preview_height');
                $swidth = $template->w;
                $sheight = $template->h;
                if ($template->w < $template->h) {
                    $pwidth = $this->config->item('template_preview_reverse_width');
                }
				$rwidth = $pwidth / $swidth; //宽度比
                $rheight = $pheight / $sheight; //高度比
                $bg_file = FALSE;
				$logo_file = FALSE;
                
                //$this->image->create($pwidth, $pheight, $bg_file);
                $area_list = $this->program->get_interaction_area_list($template_id);
                $page_ids = $this->program->get_distinct_interaction_area_list($template_id);
                $i = 1;
                $p = 2;
				$array = array();
                if($area_list) {
	                foreach($area_list as $area){
	                	for($x = 2; $x <= count($page_ids)+1; $x++) {
	                		if($area->page_id = $x) {
	                			$array[$x-1][] = $area;
	                		}
	                	}
	                }
					$this->load->library('image');
                	$this->image->create($pwidth, $pheight, $bg_file);
                	foreach($array as $arr) {
                		foreach($arr as $area) {
                			switch ($area->area_type) {
                    			case $this->config->item('area_type_staticText'):
									$this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_staticText_color'));
									break;
	                            case $this->config->item('area_type_movie'):
	                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_movie_color'));
	                                break;
	                            case $this->config->item('area_type_image'):
	                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_image'.$i.'_color'));
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
                		$absolut_path = $this->config->item('base_path').$rpath;
                		$this->image->save($absolut_path, $p.'t'.$template_id.'.jpg');
                		$p++;
                	}
                }
				
                /*
                if ($area_list) {
                    foreach ($area_list as $area) {
                    	//if($area->page_id == 2) {
                    		switch ($area->area_type) {
                    			case $this->config->item('area_type_staticText'):
									$this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_staticText_color'));
									break;
	                            case $this->config->item('area_type_movie'):
	                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_movie_color'));
	                                break;
	                            case $this->config->item('area_type_image'):
	                                $this->image->add_area($area->x * $rwidth, $area->y * $rheight, $area->w * $rwidth, $area->h * $rheight, $area->name, $this->config->item('area_type_image'.$i.'_color'));
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
                    	//}
                    }
                }
				
                $absolut_path = $this->config->item('base_path').$rpath;
                if ($this->image->save($absolut_path, 'tt'.$template_id.'.jpg')) {
             		//$this->update_interaction(array('preview_url'=>$rpath.'/t'.$template_id.'.jpg', 'save_flag'=>1, 'add_time'=>date('Y-m-d H:i:s')), $template_id);
              		echo $rpath.'/tt'.$template_id.'.jpg';
     			}
     			*/
            }
        }
	}
}