<?php
class Media extends MY_Controller
{
    private $list_view = true;


    public function __construct()
    {
        parent::__construct();
        $this->lang->load('media');
        $this->lang->load('tag');
        $this->lang->load('ftp');
        $this->lang->load('folder');
        $this->lang->load('player');
        $this->load->helper('media');
        $this->lang->load('schedule');
        $this->lang->load('time');
        $this->load->helper('week');

        $this->load->model('membership');
        //$this->settings = $this->membership->get_user_settings($this->get_uid());
    }

    public function index()
    {
        $this->addJs("/assets/js/media.js", false);
        $this->addCss("/assets/bootstrap/css/select2totree.css", false);

        $approved = $this->input->get('fromNotification');

        $data = $this->get_data();
        if ($approved) {
            $data['approved'] = 0;
        }

        $data['media_view'] = $this->get_user_media_view();

        $data['body_file'] = 'bootstrap/media/index';

        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getTableData()
    {

        $this->load->model('device');
        $data = $this->get_data();
        $offset = $this->input->post('offset');
        $limit = $this->input->post('limit');
        $name = $this->input->post('search');

        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');
        $folder_id = $this->input->post('folders');
        $media_type = $this->input->post('media_type');
        $approved = $this->input->post('approved');

        $filter_array = array();
        if ($name) {
            $filter_array['name'] = $name;
        }

        $folders = $this->get_folders();


        if ($folder_id != null) {
            if (($folder_id == -1 && isset($folders['folder_id']))) {
                $filter_array['folder_id'] = $folders['folder_id'];
            } else {
                $filter_array['folder_id'] = $folder_id;
            }
        } else {
            if (isset($folders['folder_id'])) {
                $filter_array['folder_id'] = $folders['folder_id'];
            }
        }
        if ($media_type != null && $media_type > 0) {
            $filter_array['media_type'] = $media_type;
        }
        if ($approved !== null && $approved >= 0) {
            $filter_array['approved'] = $approved;
        }

        $cid = $folders['cid'];

        $media = $this->material->get_media_list($cid, $offset, $limit, $order_item, $order, $filter_array);

        $data['total'] = $media['total'];
        $data['rows']  = $media['data'];
        echo json_encode($data);
    }

    public function edit()
    {
        $this->addCss("/assets/bootstrap/css/select2totree.css", false);
        $this->addJs('/assets/js/form.js', false);
        // $this->addJs("/assets/bootstrap/js/select2totree.js", false);
        $this->load->model('material');
        $this->load->model('device');
        $data = $this->get_data();
        $id = $this->input->get('id');
        $cid = $this->get_cid();


        if ($id) {
            $data['title'] = $this->lang->line('edit.media');
            $media = $this->material->get_media($id);

            if ($media) {
                $data['data'] = $media;
                $data['tagstr'] = $this->device->get_tags_id_by_media($id);
                if ($media->play_time) {
                    if ($media->play_time > 59) {
                        $times = sprintf("%02d:%02d", ($media->play_time / 60), ($media->play_time % 60));
                    } else {
                        $times = sprintf("00:%02d", $media->play_time);
                    }
                    $media->play_time = $times;
                }
            } else {
                $this->show_msg($this->lang->line('warn.param'), 'warn');
                return;
            }
        }


        $tags = $this->device->get_tag_list($cid);
        $data['tags'] = $tags['data'];


        $data['body_file'] = 'bootstrap/media/form';
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }



    public function upload()
    {
        $preview = $config = $errors = [];
        $index  = 0;
        $medium_id = 0;

        $cid = $this->get_parent_company_id() ?: $this->get_cid();


        $targetDir =  './resources/' . $cid;

        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir, 0744, true)) {
                $ret =  [
                    'error' => 'Failed to create directory'
                ];
                echo json_encode($ret);
                return;
            }
        }

        $input = 'input-uploader'; // the input name for the fileinput plugin
        if (empty($_FILES[$input])) {
            return [];
        }


        $tmpFilePath = $_FILES[$input]['tmp_name'][0]; // the temp file path
        $fileName = $_FILES[$input]['name'][0]; // the file name
        $fileSize = $_FILES[$input]['size'][0]; // the file size

        $media = array();

        //Make sure we have a file path
        if ($tmpFilePath != "") {

            $ext = substr($fileName, strrpos($fileName, '.') + 1);
            $destFile = $targetDir . '/' . time() . '_' . md5($fileName) . "." . $ext;
            //Upload the file into the new path
            if (move_uploaded_file($tmpFilePath, $destFile)) {

                $allowed_pics = $this->config->item('allowed_image_types');
                $allowed_movs = $this->config->item('allowed_video_types'); //["avi", "mp4", "divx", 'mpeg', 'mpg'];
                $fileInfo = pathinfo($destFile);
                $fileExt = strtolower($fileInfo['extension']);

                $this->load->model('material');
                $folderId = $this->input->post('folderSel');

                $media = array(
                    'name' => $this->material->get_next_media_name($fileName, $cid),
                    'ext' => $fileExt,
                    'orig_name' => $fileName,
                    'full_path' => $destFile,
                    'file_size' => $fileSize,
                );
                if ($this->get_parent_company_id()) {
                    $media['approved'] = 0;
                } else {
                    $media['approved'] = 1;
                }


                if (in_array($fileExt, $allowed_pics)) {
                    $info = getimagesize($destFile);
                    if ($info) {
                        $media['width'] = $info[0];
                        $media['height'] = $info[1];
                    }
                    $media['play_time'] = 10;
                    $media['media_type'] = 1;
                } else if (in_array($fileExt, $allowed_movs)) {
                    $media['media_type'] = 2;

                    $info = get_movie_info($destFile);
                    /*
                    if ($info['has_audio']) {
                        $ret =  [
                            'error' => $this->lang->line('digooh.video.format.error')
                        ];
                        echo json_encode($ret);
                        return;
                    }
                    */

                    if ($info && isset($info['width'])) {
                        $media['width'] = $info['width'];
                        $media['height'] = $info['height'];
                        $media['play_time'] = $info['play_time'];
                    } else {
                        $ret =  [
                            'error' => 'Can not get dimension of the file, please change file name and try again'
                        ];
                        echo json_encode($ret);
                        return;
                    }
                } else {
                    $ret =  [
                        'error' => $this->lang->line('invalid.extension'),
                    ];
                    echo json_encode($ret);
                    return;
                }

                $tags = '';


                if (isset($folderId) && $folderId != 0) {
                    $media['folder_id'] = $folderId;
                    $folder = $this->material->get_folder($folderId);
                    if ($folder) {
                        if ($folder->tags) {
                            $tags = $folder->tags;
                        }

                        $media['date_flag'] =  $folder->date_flag;
                        $media['start_date'] =  $folder->start_date;
                        $media['end_date'] =  $folder->end_date;

                        if ($media['media_type'] == 1) {
                            $media['play_time'] = $folder->play_time;
                        }
                    }
                }


                $media['signature'] = md5_file($destFile);

                //get preview thumbnail
                $thumbPaths = generate_thumbnails($destFile);
                if ($thumbPaths) {
                    $media['preview_status'] = 2;
                    if (isset($thumbPaths['tiny'])) {
                        $media['tiny_url'] = $thumbPaths['tiny'];
                    }
                    if (isset($thumbPaths['main'])) {
                        $media['main_url'] = $thumbPaths['main'];
                    }
                }
                //insert into db
                $medium_id = $this->material->add_media($media, $cid, $this->get_uid(), $tags);
                $media['id'] = $medium_id;
            } else {
                $errors[] = 'Error uploading' . $fileName;
            }
        } else {
            $errors[] = 'Filename: ' . $fileName;
        }




        if (!empty($errors)) {
            $out = ['errors' => $errors];
        } else {
            $out = [
                'initialPreview' => $preview,
                'initialPreviewConfig' => $config,
            ];
            if ($medium_id) {
                $media = $this->material->get_media($medium_id);
                $out['medium'] = $media;
            }
        }
        echo json_encode($out);
    }



    /**
     * 移动目录
     *
     * @return
     */
    public function do_move_to()
    {
        $folder_id = $this->input->post('folder_id');
        $ids = $this->input->post('ids');
        $code = 0;
        $msg = '';
        if ($folder_id === null) {
            $code = 1;
            $msg = $this->lang->line('tip.empty.folder');
        } elseif (empty($ids)) {
            $code = 1;
            $msg = $this->lang->line('tip.empty.media');
        } else {
            $this->load->model('material');
            if ($this->material->move_to_folder($ids, $folder_id)) {
                $msg = $this->lang->line('save.success');
            } else {
                $code = 1;
                $msg = $this->lang->line('warn.move.to');
            }
        }

        echo json_encode(array('code' => $code, 'msg' => $msg));
    }

    /**
     * 更新媒体文件信息
     *
     * @return
     */
    public function do_save()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required');

        $result = array();
        if ($this->form_validation->run() == false) {
            //false
            $result = array('code' => 1, 'msg' => validation_errors());
        } else {
            $id = $this->input->post('id');
            if ($id) {
                $this->load->model('material');
                $media = $this->material->get_media($id);
                $name = $this->input->post('name');
                $tags = $this->input->post('tags_select');
                $ext = $media->ext;

                if (!strpos($name, $ext)) {
                    $name = $name . '.' . $ext;
                }

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

                //FIXME
                if ($this->material->get_same_media_name($name, $this->get_cid(), $id)) {
                    echo json_encode(array('code' => 1, 'msg' => $this->lang->line('msg.media.exists')));
                    return;
                }
                $data = array(
                    'name' => "$name",
                    'descr' => $this->input->post('descr'),
                    'date_flag' => $date_flag,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'folder_id' => $this->input->post('folder_id')
                );

                if ($playtime_str && ($media->media_type == $this->config->item('media_type_image'))) {
                    $data['play_time'] = $play_time;
                }

                if ($this->input->post('approved') !== null) {
                    $data['approved'] = $this->input->post('approved');
                }

                $this->material->update_media($data, $id, $tags);
                $msg = $this->lang->line('save.success');
                $result = array('code' => 0, 'msg' => $msg);
            } else {
                $result = array('code' => 1, 'msg' => $this->lang->line('param.error'));
            }
        }

        echo json_encode($result);
    }

    public function do_approval()
    {
        $id = $this->input->get('id');
        $this->load->model('material');
        $approved_type = $this->input->get('approved');

        if ($approved_type >= 0 && $approved_type <= 2) {
            $this->material->update_medium(array('approved' => $approved_type), $id, false);
        }
    }

    /**
     * 检测使用磁盘情况
     *
     * @return
     */
    public function check_storage()
    {
        $cid = $this->get_cid();
        $this->load->model('material');
        $this->load->model('membership');
        $file_size = $this->input->post('file_size');
        $code = 0;
        $msg = '';
        //$limit = $this->material->is_storage_limited($cid, $file_size);
        $company = $this->membership->get_company($cid);
        //判断存放媒体文件的文件夹的大小
        //test
        $path1 = $this->config->item('gz_path') . 'resources/' . $company->id;
        $path2 = $this->config->item('gz_path') . 'resources/preview/' . $company->id;
        if (is_dir($path1)) {
            $dirSize1 = $this->getDirSize($path1);
        }
        if (is_dir($path2)) {
            $dirSize2 = $this->getDirSize($path2);
        }
        $dirSize  = $dirSize1 + $dirSize2;
        if ($dirSize > $company->total_disk) {
            $code = 1;
            $msg = sprintf($this->lang->line('warn.storage.limit'), $this->showDiskSize($company->total_disk));
        }
        /*
        if($limit){
            $code = 1;
            $msg = sprintf($this->lang->line('warn.storage.limit'),$this->showDiskSize($limit));
        }*/

        echo json_encode(array('code' => $code, 'msg' => $msg));
    }


    /**
     * 保存FTP数据
     *
     * @return
     */
    public function do_save_ftp_media()
    {
        $result = array();
        $code = 0;
        $msg = '';
        $id = $this->input->post('id');
        $folder_id = $this->input->post('folderSel');
        // $media_type = $this->input->post('media_type');
        $media = $this->input->post('media');
        if ($id == false || $media == false) {
            $code = 1;
            $msg = $this->lang->line('param.error');
        } else {
            if (count($media) == 0) {
                $code = 1;
                $msg = $this->lang->line('ftp.media.error.param.empty');
            } else {
                $this->load->model('material');
                if ($this->material->add_ftp_media($media, $id, $this->get_parent_company_id() ?: $this->get_cid(), $this->get_uid(), $folder_id)) {
                    $code = 0;
                    $msg = $this->lang->line('save.success');
                } else {
                    $code = 1;
                    $msg = $this->lang->line('invalid.extension');
                }
            }
        }
        $result['code'] = $code;
        $result['msg'] = $msg;
        echo json_encode($result);
    }

    /**
     * 获取HTTP文件的大小
     *
     * @return
     */
    public function get_http_file_size()
    {
        $url = $this->input->post('url');
        $result = array();
        $code = 0;
        $msg = '';

        if (empty($url)) {
            $code = 1;
            $msg = $this->lang->line('http.media.error.url.empty');
        } else {
            $this->load->model('material');
            $size = $this->material->get_remote_file_size($url);
            if ($size) {
                $result['size'] = $size;
            } else {
                $code = 1;
                $msg = $this->lang->line('http.media.size.error');
            }
        }

        $result['code'] = $code;
        $result['msg'] = $msg;
        echo json_encode($result);
    }


    public function do_save_http_media()
    {
        $result = array();
        $code = 0;
        $msg = '';

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', $this->lang->line('http.name'), 'trim|required');
        $this->form_validation->set_rules('url', $this->lang->line('http.url'), 'trim|required');
        if ($this->form_validation->run() == false) {
            //false
            $code = 1;
            $msg = validation_errors();
        } else {
            $url = $this->input->post('url');
            $name = $this->input->post('name');
            $descr = $this->input->post('descr');
            $size = $this->input->post('size');
            $media_type = $this->input->post('media_type');
            $id = $this->input->post('id');

            $this->load->model('material');

            //或取size
            $file_size = $this->material->get_remote_file_size($url);
            if ($file_size) {
                $size = $file_size;
                $filename = $this->material->get_file_name($url);
                $ext = strtolower(substr($filename, -4));
                if ($ext == 'mpeg' || $ext == 'divx' || $ext == 'jpeg') {
                    $ext = $ext;
                } else {
                    $ext = strtolower(substr($filename, -3));
                }
                //$ext = $this->material->get_file_ext($filename);
                $signature = md5($url);
                if (!strstr($name, '.' . strtolower($ext))) {
                    $name .= '.' . strtolower($ext);
                }
                $this->load->model('material');
                $flag = $this->material->get_same_media_name($name, $this->get_cid());
                if (is_array($flag)) { //判断媒体文件是否有重名
                    $code = 1;
                    $msg = "The Media name '" . $name . "' already exsit ! Please use different one.";
                } else {
                    if ($media_type == 1) {
                        list($width, $height, $type) = getimagesize($url);
                        $w = $width;
                        $h = $height;
                        $data = array('name' => $name, 'ext' => strtolower($ext), 'orig_name' => $filename, 'full_path' => $url, 'main_url' => $url, 'tiny_url' => $url, 'file_size' => $size, 'media_type' => $media_type, 'source' => 2, 'width' => $w, 'height' => $h, 'signature' => $signature);
                    } else {
                        $data = array('name' => $name, 'ext' => strtolower($ext), 'orig_name' => $filename, 'full_path' => $url, 'file_size' => $size, 'media_type' => $media_type, 'source' => 2, 'signature' => $signature);
                    }

                    if ($id) {
                        $this->material->update_media($data, $id);
                    } else {
                        $id = $this->material->add_media($data, $this->get_cid(), $this->get_uid());
                    }
                    if ($id) {
                        $data['id'] = $id;
                        $msg = $this->lang->line('save.success');
                    } else {
                        $code = 1;
                        $msg = $this->lang->line('save.fail');
                    }
                }
            } else {
                $code = 1;
                $msg = $this->lang->line('http.media.size.error');
            }
        }

        $result['code'] = $code;
        $result['msg'] = $msg;
        echo json_encode($result);
    }

    /**
     * 删除媒体文件
     *
     * @return
     */
    public function do_delete()
    {
        $id = $this->input->post('id');
        $code = 0;
        $msg = '';
        if ($id) {
            $this->load->model('material');
            $medias = $this->material->get_media_del($id);
            $arr = $this->material->get_pb_id($id);
            if ($this->material->delete_media($id)) {
                $msg = $this->lang->line('delete.success');
                //获取媒体文件
                /*
                if ($medias) {
                    foreach ($medias as $media) {
                        if (strpos($media->full_path, $this->get_cid())) {
                            if ($media->media_type == 1) {
                                @unlink($media->full_path);
                                @unlink('.' . $media->main_url);
                                @unlink('.' . $media->tiny_url);
                            } else {
                                @unlink($media->full_path);
                                @unlink('.' . $media->main_url);
                                @unlink('.' . $media->tiny_url);
                                //视频的预览图
                                $new_str = substr_replace($media->full_path, 'video_', strripos($media->full_path, '/') + 1, 0);
                                $video_img = substr_replace($new_str, '.jpeg', strripos($new_str, '.'), 5);
                                @unlink($video_img);
                                //视频的预览文件
                                $video_flv = substr_replace($media->main_url, 'video/', strripos($media->main_url, '/') + 1, 50) . $media->signature . '.mp4';
                                @unlink('.' . $video_flv);
                            }
                        }
                    }
                }

                $str = '';
                if ($arr) {
                    foreach ($arr as $pid) {
                        $str .= $pid->id . ',';
                    }
                    $this->material->update_pl(substr($str, 0, -1));
                }
                */
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


    public function check_unapproved()
    {
        $this->load->model("material");
        $count =  $this->material->get_unapproved_media_cnt($this->get_cid());
        echo json_encode($count);
    }

    /**
     * 加载某个FTP配置信息
     *
     * @return
     */
    public function get_ftp_config()
    {
        $id = $this->input->get('id');

        if ($id) {
            $this->load->model('material');
            echo json_encode($this->material->get_ftp_config($id));
        } else {
            echo json_encode(false);
        }
    }


    public function ftp()
    {
        $this->load->library('ftp');
        $config = array('hostname' => 'www.icatsignage.com', 'username' => 'miacms@icatsignage.com', 'password' => 'miacms20100126');
        $this->ftp->initialize($config);
        if ($this->ftp->connect()) {
            echo 'connect success.... <br/>';
            echo 'pwd:' . $this->ftp->pwd() . '<br/>';
            print_r($this->ftp->list_media_files($this->ftp->pwd(), '.php'));
            echo $this->ftp->systype;
            $this->ftp->close();
        } else {
            echo 'fail...';
        }
    }


    /**
     * 获取预览信息
     *
     * @return
     */
    public function preview()
    {
        $id = $this->input->get('id');
        $this->load->model('material');
        $this->load->helper('number');

        $media = $this->material->get_media($id);
        $ret = array();
        if ($media) {
            $ret['status'] = 1;
            $data['medium'] = $media;
            $ret['approved'] = $media->approved;
            $ret['medium'] = $this->load->view("bootstrap/media/medium_preview", $data, true);
        } else {
            $ret['status'] = 0;
        }
        echo json_encode($ret);
    }


    /**
     * 获取视频文件预览文件，如果无法支持预览则返回相应的错误信息
     *
     * @return
     */
    public function get_preview_video($media = null)
    {
        $fun = false;
        if ($media == null) {
            $id = $this->input->get('id');
            $this->load->model('material');
            $media = $this->material->get_media($id);
        } else {
            $fun = true;
        }

        $result = array();
        $code = 0;
        $msg = "";
        if ($media) {
            if ($media->source == $this->config->item("media_source_local") && $media->media_type == $this->config->item("media_type_video")) {
                $base_path = $this->config->item('base_path');
                $relate_path = '/resources/preview/' . $this->get_cid() . '/video/';
                $dest_file = $media->signature . '.mp4';
                $dest_path = $base_path . $relate_path;
                if (!file_exists($dest_path) || !is_dir($dest_path)) {
                    mkdir($dest_path, 0777, true);
                }

                //已经存在，则直接返回播放
                if (file_exists($dest_path . $dest_file) && filesize($dest_path . $dest_file) > 0) {
                    $result['video'] = $relate_path . $dest_file;
                } elseif (generate_preview_movie($media->full_path, $media->ext, $dest_path . $dest_file)) {
                    $result['video'] = $relate_path . $dest_file;
                } else {
                    $code = 1;
                    $msg = $this->lang->line("msg.media.preview.fail");
                }
            } else {
                $code = 1;
                $msg = $this->lang->line("msg.media.not.support.preview");
            }
        } else {
            $code = 1;
            $msg = $this->lang->line("msg.media.not.exists");
        }

        $result['code'] = $code;
        $result['msg'] = $msg;

        //如果是函数，则返回值，否则为输出
        if ($fun) {
            return $result;
        } else {
            echo json_encode($result);
        }
    }

    /**
     * 生成  mp4 视频
     * @param object $file_path
     * @param object $file_name
     * @return
     */
    private function get_video_mp4($id)
    {
        $this->load->model('material');
        $media = $this->material->get_media($id);
        if ($media) {
            if ($media->source == $this->config->item("media_source_local") && $media->media_type == $this->config->item("media_type_video")) {
                $base_path = $this->config->item('base_path');
                $relate_path = '/resources/preview/' . $this->get_cid() . '/video/';
                $dest_file = $media->signature . '.mp4';
                $dest_path = $base_path . $relate_path;
                if (!file_exists($dest_path) || !is_dir($dest_path)) {
                    mkdir($dest_path, 0755, true);
                }

                //已经存在，不转
                if (file_exists($dest_path . $dest_file) && filesize($dest_path . $dest_file) > 0) {
                    $result['video'] = $relate_path . $dest_file;
                } elseif (generate_preview_movie($media->full_path, $media->ext, $dest_path . $dest_file)) {
                    $result['video'] = $relate_path . $dest_file;
                }
            }
        }
    }


    //2014-4-10 10:55:21----test
    public function media_name_check()
    {
        $cid = $this->get_cid();
        $uid = $this->get_uid();
        $name = $this->input->post('name');
        $name = substr($name, 0, -1);

        $this->load->model('material');
        $flag = $this->material->get_same_media_name($name, $cid);
        if (is_array($flag)) {
            echo 1;
        } else {
            echo 0;
        }
    }
    public function media_confirm_value()
    {
        $this->load->model('membership');
        $cid = $this->get_cid();
        $uid = $this->get_uid();
        $value = $this->input->post('value');
        $id = $this->membership->update_user_settings($uid, array('media_confirm_flag' => $value));
        echo $id;
    }

    public function download()
    {
        $id = $this->input->get('id');
        $this->load->model('material');
        $media = $this->material->get_media($id);
        if ($media) {
            $this->load->helper('file');
            np_download($media->full_path, NULL, $media->orig_name);
        }
    }


    /**
     * 加载编辑媒体信息
     *
     * @return
     */
    public function edit_property()
    {
        $this->load->model('device');
        $data = $this->get_data();
        $tags = $this->device->get_tag_list($this->get_cid());
        $data['tags'] = $tags['data'];

        $this->load->view('media/edit_medias_property', $data);
    }

    /**
     * 更新媒体文件信息
     *
     * @return
     */
    public function do_save_protery()
    {
        $result = array();
        $ids = $this->input->post('id');
        if (!empty($ids)) {
            $this->load->model('material');

            $tags = $this->input->post('tags_select');

            $date_flag = $this->input->post('date_flag');
            $start_date = $this->input->post('startDate');
            $end_date = $this->input->post('endDate');

            if ($date_flag == "1") {
                if (intval(str_replace('-', '', $start_date)) > intval(str_replace('-', '', $end_date))) {
                    $code = 1;
                    $msg = $this->lang->line('warn.date.flag.range');
                    echo json_encode(array('code' => $code, 'msg' => $msg));
                    return;
                }
            }

            $playtime_str = $this->input->post('playTime');

            if ($playtime_str) {
                $play_time = 30;
                if (strlen($playtime_str) != 5 || strpos($playtime_str, ':') === false) {
                    echo json_encode(array('code' => 1, 'msg' => $this->lang->line('warn.time.flag.format')));
                    return;
                } else {
                    $t_st = explode(':', $playtime_str);
                    $t_st_m = intval($t_st[0]);
                    $t_st_s = intval($t_st[1]);
                    if ($t_st_m > 59 || $t_st_m < 0 || $t_st_s > 59 || $t_st_s < 0) {
                        echo json_encode(array('code' => 1, 'msg' => $this->lang->line('warn.time.flag.outbound')));
                        return;
                    }
                    $play_time = $t_st_m * 60 + $t_st_s;
                }
            }
            /*
            $all_day_flag= $this->input->post('alldayFlag');
            $start_time = $this->input->post('startTime');
                      $end_time = $this->input->post('endTime');

                      if($all_day_flag == "0"){
                          $code = 0;
                          $msg = '';
                      if ((empty($start_time) || empty($end_time))) {
                          $code = 1;
                          $msg = $this->lang->line('warn.time.flag.empty');
                      } elseif (strlen($start_time) != 5 || strlen($end_time) != 5 || strpos($start_time, ':') === FALSE || strpos($end_time, ':') === FALSE) {
                          $code = 1;
                          $msg = $this->lang->line('warn.time.flag.format');
                      } elseif (intval(str_replace(':', '', $end_time)) > 0 && intval(str_replace(':', '', $start_time)) >= intval(str_replace(':', '', $end_time))) {
                          $code = 1;
                          $msg = $this->lang->line('warn.time.flag.range');
                      } else {
                          $t_st = explode(':', $start_time);
                          $t_et = explode(':', $end_time);
                          $t_st_h = intval($t_st[0]);
                          $t_st_m = intval($t_st[1]);
                          $t_et_h = intval($t_et[0]);
                          $t_et_m = intval($t_et[1]);
                          if ($t_st_h < 0 || $t_st_h > 23 || $t_st_m < 0 || $t_st_m > 59 || $t_et_h < 0 || $t_et_h > 23 || $t_et_m < 0 || $t_et_m > 59) {
                              $code = 1;
                              $msg = $this->lang->line('warn.time.flag.outbound');
                              }
                          }

                        if ($code == 1) {
                              echo json_encode(array('code'=>$code, 'msg'=>$msg));
                              return;
                          }
                      }
        */
            /*
              $play_count_flag= $this->input->post('play_count_flag');
              $play_count_value = $this->input->post('play_count');
             	$play_count = '0';
              if($play_count_flag=='1'){
              	$play_count = $play_count_value;
              }
      */
            foreach ($ids as $id) {
                $data = array();
                $data['date_flag'] = $date_flag;

                $media = $this->material->get_media($id);
                if ($playtime_str && ($media->media_type != $this->config->item('media_type_video'))) {
                    $data['play_time'] = $play_time;
                }
                if ($date_flag == "1") {
                    $data['start_date'] = $start_date;
                    $data['end_date'] = $end_date;
                }
                /*
                  if($all_day_flag=="0"){
                       $data['start_time']=$start_time;
                       $data['end_time']=$end_time;
                  }
                  */
                $this->material->update_media($data, $id, $tags);
            }

            $msg = $this->lang->line('save.success');
            $result = array('code' => 0, 'msg' => $msg);
        } else {
            $result = array('code' => 1, 'msg' => $this->lang->line('param.error'));
        }

        echo json_encode($result);
    }

    public function upload_medias()
    {
        $this->lang->load('ftp');
        $data = $this->get_data();
        $data['playlist_id'] = $this->input->get('playlist_id');
        $data['area_id'] = $this->input->get('area_id');
        $data['area_type'] = $this->input->get('area_type');
        $data['screen_id'] = $this->input->get('screenID');
        $data['touchpls'] = $this->input->get('istouch');

        $data['session_id'] = $this->get_session_id();

        $data['upload_url'] = '/campaign/campaign_upload_medias';

        $this->load->model('membership');
        $uid = $this->get_uid();
        $this->membership->update_user_settings($uid, array('media_confirm_flag' => 0));

        $this->load->model('material');
        $this->load->model('device');
        $cid = $this->get_cid();


        $folders = $this->get_folders();
        $data['folders'] = $folders['folders'];
        if (isset($folders['root'])) {
            $data['root'] = $folders['root'];
        }


        $data['sites'] = $this->material->get_ftp_config_list($cid);
        $limit  = $this->material->is_storage_limited($cid, 0);
        $limit_msg = '';

        if ($limit) {
            $limit_msg = sprintf($this->lang->line('warn.storage.limit.tip'), $this->showDiskSize($limit));
        }
        $data['limit_msg'] = $limit_msg;


        $this->load->view('media/upload_medias', $data);
    }

    public function update_media_view()
    {
        $type = $this->input->get('media_view');

        if ($type !== null && ($type == 0 || $type == 1)) {
            $this->load->model('membership');
            $this->membership->update_user(array('media_view' => $type), $this->get_uid());
            $this->set_user_media_view($type);
        }
    }
}
