<?php

/**
 * 播放列表对象
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use function Complex\ln;

class Playlist extends MY_Controller
{
    private $sep;

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('campaign');
        $this->lang->load('font');
        $this->lang->load('folder');
        $this->lang->load('schedule');
        $this->lang->load('time');
        $this->lang->load('criteria');
        $this->lang->load('media');
        $this->lang->load('tag');
        $this->lang->load('player');
        //$this->sep = chr(10).chr(10);
        $this->sep = chr(10);
    }

    /**
     * 播放列表页
     *
     * @return
     */
    public function index()
    {
        $data = $this->get_data();
        $pid = $this->get_parent_company_id();
        $cid = $this->get_cid();
        $auth = $this->get_auth();

        $filter_array = array();
        if ($auth < 2 && $pid) {
            $this->load->model('device');
            $cris = $this->device->get_paterner_criterias($cid);
            $filter_array['pids'] = $cris['player_array'];
            $data['criteria'] = $cris['data'];
        } else {
            $cris = $this->get_criteria($cid, $pid);
            $data['criteria'] = $cris['criteria'];
        }


        if (isset($cris['filter_array'])) {
            $filter_array = array_merge($filter_array, $cris['filter_array']);
        }

        $players = $this->device->get_player_list($pid ?: $cid, $filter_array);
        $data['players'] = $players['data'];


        if ($this->config->item('campaign_with_tags')) {
            $tags = $this->device->get_tag_list($cid);
            $data['tags'] = $tags['data'];
        }
        $this->load->model('membership');
        $company = $this->membership->get_company($cid);
        //$data['xslot'] = $company->nxslot;
        $data['xslot'] = $this->get_nxslot();
        $data['body_file'] = 'bootstrap/template_campaigns/index';
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getTableData()
    {
        $offset = $this->input->post('offset');
        $limit = $this->input->post('limit');
        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');
        $name = $this->input->post("search");

        $priority = $this->input->post("priority");
        $criterion = $this->input->post("criterion");

        $start = $this->input->post("start");
        $end = $this->input->post("end");

        $this->load->model('program');
        $this->load->model('device');

        $condition = array('playlist_type' => $this->config->item('playlist_type_normal'));

        if ($name != null) {
            $condition['name'] = $name;
        }

        if ($priority && $priority != -1) {
            $condition['priority'] = $priority;
        }
        if ($criterion && $criterion != -1) {
            $condition['criteria'] = $criterion;
        }


        if ($this->input->post('with_expired')) {
            $condition['withExpired'] = 1;
        } else {
            $condition['withExpired'] = 0;
        }


        $tag = $this->input->post("tag");
        if ($tag && $tag != -1) {
            $condition['tag'] = $tag;
        }

        $player =  $this->input->post("player");
        if ($player && $player != -1) {
            $condition['player'] = $player;
        }


        if ($start) {
            $condition['start'] = $start;
        }
        if ($end) {
            $condition['end'] = $end;
        }

        $auth = $this->get_auth();

        if ($auth == 0 || (!$this->config->item("new_campaign_user") && $auth == 1)) {
            $this->load->model('membership');
            $user = $this->membership->get_user($this->get_uid());
            if (!$user->campaigns) {
                $data['total'] = 0;
                $data['rows'] = [];

                echo json_encode($data);
                return;
            } else {
                $condition['campaigns'] = $user->campaigns;
            }
        } else if ($this->config->item("new_campaign_user") && $auth == 1) {
            $condition['add_user_id'] = $this->get_uid();
        }

        $cid = $this->get_cid();


        if ($this->input->post('with_partners')) {
            if (!$this->get_parent_company_id()) {
                $this->load->model('membership');
                $partners = $this->membership->get_all_partners($cid);
                if ($partners) {
                    array_push($partners, $cid);
                    $cid = $partners;
                }
            }
        }

        $playlist = $this->program->get_playlist_list($cid, $condition, true, $offset, $limit, $order_item, $order);

        $this->load->model('template');
        foreach ($playlist['data'] as $cam) {
            $cam->players_cnt = $this->program->get_campaign_players_count($cam->id);
            $cam->ex_players_cnt = $this->program->get_campaign_ex_players_count($cam->id);
            /* $template = $this->template->get_template($cam->template_id);
            if ($template) {
                $cam->template_name = $template->name;
            }
            */
        }
        $data['total'] = $playlist['total'];
        $data['rows']  = $playlist['data'];

        echo json_encode($data);
    }



    public function checkName()
    {
        $name = $this->input->get('name');
        $id =  $this->input->get('id');

        $this->load->model('program');
        $flag = $this->program->get_playlist_by_name($id, $this->get_cid(), $name);


        if ($flag) {
            echo json_encode(sprintf($this->lang->line('playlist.name.exsit'), $name));
            return;
        }
        echo json_encode(true);
    }

    public function edit()
    {
        $this->addJs("/assets/js/form.js", false);

        $playlist_id = $this->input->get('id');

        $this->lang->load('template');
        $this->load->model('device');
        $this->load->model('program');
        $this->load->model('template');
        $data = $this->get_data();

        $pid = $this->get_parent_company_id();
        if ($pid > 0) {
            $cid = $pid;
        } else {
            $cid = $this->get_cid();
        }


        $auth = $this->get_auth(); //获取用户的权限

        $editable = false;
        $filter_array = array();
        if ($auth < 2 && $pid) {
            $this->load->model('device');
            $cris = $this->device->get_paterner_criterias($this->get_cid());
            $data['criteria'] = $cris['data'];
            $filter_array['pids'] = $cris['player_array'];
        } else {
            $editable = true;
            if ($this->config->item("new_campaign_user") && $auth == 0) {
                $editable = false;
            }
            $cris = $this->get_criteria($this->get_cid(), $pid);
            $data['criteria'] = $cris['criteria'];
        }
        $data['editable'] = $editable;
        $folders = $this->get_tree_folders();
        $data['folders'] = json_encode($folders['tree_folders']);


        if (isset($cris['filter_array'])) {
            $filter_array = array_merge($filter_array, $cris['filter_array']);
        }

        $players = $this->device->get_player_list($cid, $filter_array);
        $data['players'] = $players['data'];


        if ($this->is_user()) {
            $data['tags'] = $this->device->get_tag_list($cid, $this->get_uid())['data'];
        } else {
            $data['tags'] = $this->device->get_tag_list($cid)['data'];
        }


        if ($auth == 0) {
            $this->load->model('membership');
            $user = $this->membership->get_user($this->get_uid());
            $data['can_publish'] = isset($user->can_publish) ? $user->can_publish : 0;
        } elseif ($auth >= 5) {
            $data['can_publish'] = 1;
        } else {
            $data['can_publish'] = 0;
            if ($this->config->item("new_campaign_user") && $auth == 1) {
                $data['can_publish'] = 1;
            }
        }

        if ($playlist_id) {
            $playlist = $this->program->get_campaign($playlist_id, true);

            $data['data'] = $playlist;
        }

        $filter_array = array();
        $auth = $this->get_auth();
        if ($this->config->item("new_campaign_user") && $auth == 1 && !$playlist_id) {
            $filter_array['user_id'] = $this->get_uid();
        }
        $templates = $this->template->get_template_list($cid, 0, -1, 'name', 'asc', $filter_array)['data'];
        $data['templates'] = $templates;




        $data['body_file'] = 'bootstrap/template_campaigns/form';
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getAffectedPlayers()
    {
        $id = $this->input->get('id');

        $this->load->model('program');
        $name = $this->input->get('name');
        $data['id'] = $id;
        $data['name'] = $name;

        $campaign = $this->program->get_playlist($id);


        $affect_players = $this->program->get_player_by_campaign($id);

        if ($affect_players && $campaign) {
            foreach ($affect_players as $player) {
                $workingday = 7;
                $offweekdays  = isset($player->offweekdays) && !empty($player->offweekdays) ? $player->offweekdays : null;

                if ($offweekdays) {
                    $workingday = 7 - count(explode(",", $player->offweekdays));
                }

                $days = $this->get_camapaign_working_days($campaign->start_date, $campaign->end_date, $offweekdays);
                $player->daysperweek = $workingday;
                $player->workingdays = $days;
            }
        }

        $data['affect_players'] = $affect_players;
        $exclude_players = $this->program->get_excluded_player_by_campaign($id);


        $data['exclude_players'] = $exclude_players;
        echo json_encode($data);
        //$this->load->view('program/campaign/campaign_devices', $data);
    }
    /**
     * 删除播放列表
     * @return
     */
    public function do_delete()
    {
        $id = $this->input->post('id');
        $code = 1;
        $msg = '';



        if ($id) {
            $this->load->model('program');
            $publish_urls = $this->program->get_playlist_area_media_by_playlistId($id);


            $oldpl = $this->program->get_playlist($id);
            if ($oldpl && $oldpl->published == 1) {
                //$this->program->reset_player_least_while_update_campaign($oldpl);

                $this->load->model('membership');
                $this->membership->update_company(array('publish_refresh' => date('Y-m-d H:i:s')), $this->get_cid());
            }

            if ($this->program->delete_playlist($id, FALSE)) {
                $code = 0;
                $msg = $this->lang->line('delete.success');
                $playlist_path = $this->config->item('playlist_publish_path') . $this->get_cid();
                $playlist_path .= '/' . $id . '.PLS';
                @unlink($playlist_path);

                //执行播放列表中的文件删除
                $txt = '';
                if ($publish_urls) {
                    foreach ($publish_urls as $publish_url) {
                        if (strpos($publish_url->publish_url, $this->get_cid())) {
                            if (substr_count($publish_url->publish_url, '.') == 4) {
                                $txt .= $publish_url->publish_url . chr(10);
                                //只删除从本地上传的媒体
                                if (!strstr($publish_url->publish_url, 'ftp://')) {
                                    @unlink($publish_url->publish_url);
                                }
                            }
                            if (substr_count($publish_url->publish_url, '.') == 5) {
                                $txt .= $publish_url->publish_url . chr(10);
                                $img_url = substr($publish_url->publish_url, 0, -4);
                                $txt .= $img_url . chr(10);
                                if (!strstr($publish_url->publish_url, 'ftp://')) {
                                    @unlink($publish_url->publish_url);
                                    @unlink($img_url);
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($code == 1) {
            $msg = sprintf($this->lang->line('delete.fail'), $this->lang->line('playlist'));
        }

        echo json_encode(array('code' => $code, 'msg' => $msg));
    }


    /**
     * 保存播放列表设置
     *
     * @return
     */
    public function do_save($publish = false)
    {
        $uid = $this->get_uid();
        $playlist_id = $this->input->post('id');
        //$playlist = $this->input->post('playlist');
        $this->load->model('program');
        $this->load->model('membership');

        $is_updating = $playlist_id ? true : false;

        $media = $this->input->post('media');
        $media = json_decode($media);
        $players = $this->get_affected_players();


        $hasVideo = false;
        $priority = $this->input->post('priority');
        $webpages = $this->input->post('webpages');
        $id_numbers = $this->input->post('id_numbers');

        if ($publish == true) {
            if (empty($media) && !$webpages && !$id_numbers && $priority != 5) {
                $result = array('code' => 1, 'msg' => $this->lang->line('campaign.error.media.empty'));
                echo json_encode($result);
                return;
            }
            if (!$players) {
                $result = array('code' => 1, 'msg' => $this->lang->line('minimum.player'));
                echo json_encode($result);
                return;;
            }
        }

        //删除的媒体文件
        $deletes = $this->input->post('deletes');
        //需要将状态改变的
        $ids = $this->input->post('ids');
        $result = array();


        $this->load->model('material');
        $this->load->library('image');

        foreach ($this->input->post() as $key => $val) {
            if (
                $key != "tags" && $key != "media" && $key != "criteria" && $key != "and_criteria" &&
                $key != "and_criteria_or" && $key != "ex_criteria" && $key != "players" &&
                $key != "ex_players" && $key != "ob_ids" && $key != "webpages" && $key != 'motionText' && $key != "id_numbers" && $key != "id_descrs" && $key != 'btSelectItem'
            ) {
                $data[$key] = $val;
            }
        }

        /*
        $priority = $this->input->post('priority');
        $play_type = $this->input->post('play_cnt_type');
        $play_count = $this->input->post('play_count');


        if ($play_type == 0) {
            $data['play_count'] = $play_count;
        } elseif ($play_type == 1) {
            $data['play_weight'] = $play_count;
        } elseif ($play_type == 2) {
            $data['play_total'] = $play_count;
        }


        if ($priority == 3 || $priority == 6) {
            $data['play_count'] = 1;
            $data['play_weight'] = 0;
            $data['play_total'] = 0;
        }
        $data['update_time'] = date('Y-m-d H:i:s');

        if ($priority == 6) {
            $data['is_grouped'] = 1;
        } elseif ($priority == 3 || $priority == 5) {
            $data['is_grouped'] = 0;
        }

        */


        $data['published'] = 0;



        if ($is_updating) {
            $cam = $this->program->get_playlist($playlist_id);
            if ($cam->published == 1 && $publish == false) {
                $this->program->update_affected_players($playlist_id);
            }
            $this->program->update_playlist($data, $playlist_id);
        } else {
            $playlist_id = $this->program->add_playlist($data, $this->get_cid(), $this->get_uid());
            if (!$playlist_id) {
                $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('playlist')));
                echo json_encode($result);
                return;
            }
        }

        $this->load->model('program');

        $rotate = 1;


        if (!empty($media)) {
            $area_id = 101;


            // $max_position = $this->program->get_playlist_area_media_max_position($playlist_id, $area_id);
            //$max_position++;
            $max_position = 1;
            //$this->program->soft_delete_campaign_area_media($playlist_id);
            $cur_media = array_column($media, 'area_media_id');
            $this->program->delete_playlist_area_media_not_in_list($playlist_id, false, $cur_media);
            $max_position = 1;


            $this->load->model('material');
            $pls_tags = $this->input->post('tags');
            if (!$pls_tags) {
                $pls_tags = array();
            }

            foreach ($media as $medium) {
                if ($medium->media_type == 2) {
                    $hasVideo = true;
                }

                $mAry = array(
                    'playlist_id' => $playlist_id,
                    'area_id' => $medium->area_id,
                    'media_id' => $medium->media_id,
                    'transmode' => $medium->transmode,
                    'status' => $medium->status,
                    'position' => $max_position,
                    'rotate' => $rotate,

                );
                if ($medium->area_media_id) {
                    $mAry['flag'] = 0;
                    $this->program->update_area_media(
                        $mAry,
                        $medium->area_media_id,
                    );
                } else {
                    $this->program->add_area_media(
                        $mAry,
                        $uid
                    );
                }
                $max_position++;
            }
            //$this->program->real_delete_campaign_area_media($playlist_id);
        } else {
            $this->program->delete_campaign_area_media($playlist_id);
        }



        if ($webpages) {
            $webpages = json_decode($webpages);
            $items = [];
            $max_position = 1;


            foreach ($webpages as $web) {
                $item = array(
                    'playlist_id' => $playlist_id,
                    'mce_id' => $web->mce_id,
                    'text' => isset($web->text) ? $web->text : null,
                    'position' => $max_position++,
                );

                $items[] = $item;
            }
            $this->program->update_playlist_mce_batch($items, $playlist_id);
        }

        $motionText = $this->input->post('motionText');

        $this->program->update_playlist_text(array('text' => $motionText, 'playlist_id' => $playlist_id), $playlist_id);


        $id_numbers = json_decode($id_numbers);

        if ($id_numbers) {
            foreach ($id_numbers as $id_number) {

                $mAry = array(
                    'playlist_id' => $playlist_id,
                    'area_id' => $id_number->area_id,
                    'id_number' => $id_number->id_number,
                    'name' => $id_number->name,
                    'descr' => $id_number->descr,

                );
                if (isset($id_number->type)) {
                    $mAry['type'] = $id_number->type;
                }
                if ($id_number->id) {
                    $this->program->update_area_id(
                        $mAry,
                        $id_number->id,
                    );
                } else {
                    $this->program->add_area_id(
                        $mAry,
                    );
                }
            }
        }


        $result['code'] = 0;
        $result['msg'] = $this->lang->line('save.success');

        $this->sync_campaign($playlist_id);

        $result['refresh_url'] = true;

        $result['code'] = 0;
        $result['id'] = $playlist_id;

        $result['msg'] = $this->lang->line('save.success');


        if ($is_updating) {
            $this->program->detach_campaign_player($playlist_id);
        }

        $noVideo_playersCnt = 0;
        if ($players) {
            $items = array();
            foreach ($players as $player) {
                $item = array('player_id' => $player->id, 'campaign_id' => $playlist_id);
                $items[] = $item;
                if ($player->video_playback == 0) {
                    $noVideo_playersCnt++;
                }
            }

            $this->program->saveManyCampaignPlayer($items);


            if ($hasVideo && $noVideo_playersCnt) {
                $result['code'] = 1;
                $result['id'] = $playlist_id;
                $result['msg'] = sprintf($this->lang->line('video.limit.warning'), $noVideo_playersCnt);
            }
        }

        if ($publish) {
            return $result;
        } else {
            $affect_players = $this->program->get_player_by_campaign($playlist_id);
            $sel_players['sel_players'] = $affect_players;

            $result['affected_players'] = $this->load->view("program/campaign/affect_player_list", $sel_players, true);

            echo json_encode($result);
        }
    }


    public function pdf2video($pdfpath, $videopath, $interval)
    {
        $CI = &get_instance();
        $ffmpegPath = $CI->config->item('ffmpeg');


        if (file_exists($videopath)) {
            //	echo "Already exist:".$videopath."<br>";
            return $videopath;
        }

        $ext = pathinfo($pdfpath, PATHINFO_EXTENSION);
        $filename = basename($pdfpath, "." . $ext);
        $OutputPath = dirname($pdfpath) . '/' . $filename;

        if (!extension_loaded('imagick')) {
            echo "can not find imagic lib!";
            return false;
        }
        if (!file_exists($pdfpath)) {
            echo "pdf file does not exits:" . $pdfpath;
            return false;
        }

        if (!file_exists($OutputPath)) {
            mkdir($OutputPath);
        }

        $IM = new imagick();
        $IM->readImage($pdfpath);
        $count = 0;
        foreach ($IM as $Key => $Var) {
            $Var->setImageFormat('png');
            $count++;
            $pngname = $OutputPath . '/' . $filename . '_' . $count . '.jpg';
            if ($Var->writeImage($pngname) == true) {
            }
        }
        //conver to video
        $command = $ffmpegPath . " -r " . (1 / $interval) . " -i " . $OutputPath . '/' . $filename . "_%d.jpg" . ' -b 1500 -vcodec mpeg4 ' . $videopath;
        //	$command = $ffmpegPath." -framerate ".$interval." -i ".$OutputPath.'/'.$filename."_%d.png".' -b 1500 -vcodec mpeg4 '.$videopath;
        @exec($command, $output, $return);

        //delete tempory folders

        $command = 'rm -rf ' . $OutputPath;
        @exec($command);
        return $videopath;
    }

    public function office2video($media)
    {
        //	function office2video($full_path,$interval,$last_publish_url){
        $full_path = $media->full_path;

        $timearr = explode(":", $media->duration);
        $dursec = intval($timearr[0], 10) * 60 + intval($timearr[1], 10);


        $ext = pathinfo($full_path, PATHINFO_EXTENSION);
        $filename = basename($full_path, "." . $ext);
        $OutputPath = dirname($full_path);
        $videopath = dirname($full_path) . '/' . $filename . $ext . $dursec . 's.avi';

        //if the video file alreay exsit;
        if (file_exists($videopath)) {
            //	echo "Already exist:".$videopath."<br>";
            return $videopath;
        } elseif (file_exists($media->publish_url)) {
            @unlink($media->publish_url);
        }


        $command = 'export HOME=/tmp && /opt/libreoffice5.1/program/soffice --invisible --headless --norestore --convert-to pdf:writer_pdf_Export --outdir ' . $OutputPath . ' ' . $full_path;
        @exec($command, $output, $return);
        //	var_dump($output);


        if ($return == 0) {
            $pdfpath = $OutputPath . '/' . $filename . ".pdf";
            //	 echo "pdf dir =".$pdfpath;
            $videopath = $this->pdf2video($pdfpath, $videopath, $dursec);
        } else {
            echo var_dump($output);
            return false;
        }

        $command = 'rm -f ' . $pdfpath;
        @exec($command);

        return $videopath;
    }

    public function swf2video($swfpath)
    {
        $CI = &get_instance();
        $ffmpegPath = $CI->config->item('ffmpeg');

        $outputfile = $swfpath . ".avi";

        if (file_exists($outputfile)) {
            return $outputfile;
        }
        $tmpfile = $swfpath . ".new.swf";
        $handle = fopen($swfpath, "r") or die("Unable to open file!");
        if ($handle) {
            $content = fread($handle, 1);
            fclose($handle);
            if ($content == 'C') {
                echo "compressed!";
                $command = "swfcombine -s 100% -d " . $swfpath . " -o " . $tmpfile;
                @exec($command);
                if (!file_exists($tmpfile)) {
                    echo "can not uncompress the swf file: " . $swfpath;
                    return null;
                }
                $swfpath = $tmpfile;
            }
            $command = $ffmpegPath . " -i " . $swfpath . " " . $outputfile;
            @exec($command);

            if (file_exists($tmpfile)) {
                @unlink($tmpfile);
            }
            if (file_exists($outputfile)) {
                echo "done";
                return $outputfile;
            } else {
                return "";
            }
        }
        return null;
    }

    /**
     * 发布当前播放列表,生成相应的XML文件，以供下载
     *
     * @return
     */

    public function show_ob_msg($msg)
    {
        return array('code' => 1, 'msg' => $msg);
    }


    /**
     * 发布当前播放列表,生成相应的XML文件，以供下载
     *
     * @return
     */
    public function do_publish()
    {

        //取消时间限制
        set_time_limit(0);
        $result = $this->do_save(true);

        if ($result === null) {
            return;
        }

        if ($result['code'] > 0) {
            echo json_encode($result);
            return;
        }


        //$id = $this->input->post("id");
        $id = $result['id'];



        $this->load->model('program');

        $cam_xml = $this->program->save_campaign_xml($id);

        echo json_encode($result);
        return;
    }

    private function rename_media_name($file_name, $media_type, $area_type, $rotate, $file_id)
    {
        $result = $file_name;
        if ($area_type == $this->config->item('area_type_movie')) {
            if ($rotate) {
                if ($media_type == $this->config->item('media_type_video')) {
                    $result = $this->rename($file_name, 'P');
                } else {
                    $result = $this->rename($file_name, $file_id);
                }
                //All video will convert to be mkv
                $ext = strtolower(substr($result, -4));
                if ($ext == 'mpeg' || $ext == 'divx') {
                    $result = substr($result, 0, strlen($result) - 4) . 'mkv';
                } else {
                    $ext = strtolower(substr($result, -3));
                    if ($ext == 'mp4' || $ext == 'mpg' || $ext == 'flv' || $ext == 'mov' || $ext == 'avi' || $ext == 'wmv') {
                        $result = substr($result, 0, strlen($result) - 3) . 'mkv';
                    }
                }
            } else {
                $result = $file_name;
                //if mp4 or wmv will convert to be mkv
                $ext = strtolower(substr($result, -3));
                if ($ext == 'mp4' || $ext == 'wmv' || $ext == 'mov' || $ext == 'flv') {
                    $result = substr($result, 0, strlen($result) - 3) . 'mkv';
                }
            }
        } else {
            //$result = $this->rename($file_name, $file_id);
            if ($area_type == $this->config->item('area_type_bg')) {
                $result = $file_name;
            } else {
                $result = $this->rename($file_name, $file_id);
            }
        }

        return $result;
        //($area->area_type == $this->config->item('area_type_movie') ? ( $rotate ? $this->rename($media->name, 'P') : $media->name) :$this->rename($media->name, $media->id))
    }

    private function rename($file_name, $file_id)
    {
        $tmp = explode('.', $file_name);
        $dest = '';
        for ($i = 0; $i < count($tmp) - 1; $i++) {
            $dest .= $tmp[$i];
            if ($i < count($tmp) - 2) {
                $dest .= '.';
            }
        }
        $dest .= '[' . $file_id . '].' . $tmp[count($tmp) - 1];
        return $dest;
    }

    /**
     * 模板选择器
     *
     * @return
     */
    public function template()
    {
        $this->load->view('program/campaign/template_index');
    }
    /**
     * 模板列表
     *
     * @return
     */
    public function template_list($curpage = 1)
    {
        $type = $this->input->get('type');
        if ($type == $this->config->item('template_system')) {
            $type = $this->config->item('template_system');
        } else {
            $type = $this->config->item('template_user');
        }

        $this->load->model('program');
        $this->load->model('program');
        $this->load->model('membership');
        $this->load->model('device');
        $auth = $this->get_auth(); //获取用户的权限
        if ($auth == $this->config->item('auth_group') || $auth == $this->config->item('auth_franchise')) {
            $cid = $this->get_cid(); //公司id
            $admin_id = $this->membership->get_admin_by_cid($cid); //根据公司id获取Admin的id
            $uid = $this->get_uid(); //用户id
            $group_userId = $this->device->get_all_user_by_usergroup($uid); //获取用户所在的组 的所有用户
            $ts = $this->program->get_all_published_template_list($cid, $type, $admin_id, $uid, $group_userId);
        } else {
            $ts = $this->program->get_all_published_template_list1($this->get_cid(), $type);
        }
        //$ts = $this->program->get_all_published_template_list1($this->get_cid(), $type);
        $data['data'] = $ts;
        $data['type'] = $type;
        $this->load->view('program/campaign/template_list', $data);
    }




    public function do_rotate_media()
    {
        $id = $this->input->post('id');
        $rotate = $this->input->post('rotate');
        if ($id) {
            $this->load->model('program');
            $index = $this->input->post('index');
            if ($this->program->playlist_area_media_rotate($id, $rotate)) {
                $result = array('code' => 0, 'msg' => '');
            }
        }

        if (empty($result)) {
            $result = array('code' => 1, 'msg' => $this->lang->line('warn.param'));
        }

        echo json_encode($result);
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
        $media = $this->input->post('media');

        if (!$playlist_id || !$area_id) {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('param.error');
        } elseif (!$media) {
            $result['code'] = 1;
            $result['msg'] = $this->lang->line('playlist.error.media.empty');
        } else {
            $this->load->model('program');
            $transmode = 26;
            $transtime = 0.5;
            $duration = '00:10';
            $area_type = $this->program->get_area_type($area_id);
            if ($area_type == $this->config->item('area_type_image')) {
                $transmode = 0;
            }
            $rotate = 1;


            $max_position = $this->program->get_playlist_area_media_max_position($playlist_id, $area_id);
            $max_position++;
            if ($this->config->item('campaign_with_tags')) {
                $priority = $this->input->post('priority');

                if ($priority != 3) {
                    $this->load->model('material');
                    $mediaObjs = $this->material->get_medias_byId($media);
                    $pls_tags = $this->input->post('tags');
                    if (!$pls_tags) {
                        $pls_tags = array();
                    }
                    foreach ($mediaObjs as $medium) {
                        $id = $this->program->add_area_media(array('playlist_id' => $playlist_id, 'area_id' => $area_id, 'media_id' => $medium->id,  'transmode' => $transmode, 'position' => $max_position, 'rotate' => $rotate), $uid);
                        $max_position++;
                        if ($medium->tags) {
                            $pls_tags = array_merge($pls_tags, explode(",", $medium->tags));
                        }
                    }
                    $pls_tags = array_unique($pls_tags);
                    $result['tags'] = array_values($pls_tags);
                } else {
                    foreach ($media as $medium) {
                        $id = $this->program->add_area_media(array('playlist_id' => $playlist_id, 'area_id' => $area_id, 'media_id' => $medium,  'transmode' => $transmode, 'position' => $max_position, 'rotate' => $rotate), $uid);

                        $max_position++;
                    }
                }
            } else {
                foreach ($media as $medium) {
                    $id = $this->program->add_area_media(array('playlist_id' => $playlist_id, 'area_id' => $area_id, 'media_id' => $medium,  'transmode' => $transmode, 'position' => $max_position, 'rotate' => $rotate), $uid);
                    $max_position++;
                }
            }

            //$result['media_ids'] = $media_ids;
            $result['code'] = 0;
            $result['msg'] = $this->lang->line('save.success');
        }

        echo json_encode($result);
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
        $result = array();
        if ($playlist_id && $area_id && $id) {
            $this->load->model('program');
            if ($this->config->item('campaign_with_tags')) {
                $tags = $this->input->post('tags');

                $diffs = array();
                //通过比较tags框选中值与未删除前medias的tags 获取用户输入的TAGS数组
                if ($tags) {
                    $this->load->model('material');
                    $flag = array($this->config->item('area_media_flag_temp'), $this->config->item('area_media_flag_ok'));
                    $medias = $this->material->get_medias_byPid($playlist_id, $flag);
                    $media_tags = array();

                    if ($medias) {
                        foreach ($medias as $media) {
                            if ($media->tags) {
                                $media_tags = array_merge($media_tags, explode(',', $media->tags));
                            }
                        }
                        $media_tags = array_unique($media_tags);
                        $diffs = array_diff($tags, $media_tags);
                    }
                }
            }
            $this->program->delete_playlist_area_media($playlist_id, $area_id, $id, $this->config->item('area_media_flag_temp'));
            //just delete template
            $this->program->update_media_flag($id, $this->config->item('area_media_flag_delete'));
            if ($this->config->item('campaign_with_tags')) {
                if ($tags) {
                    $flag = array($this->config->item('area_media_flag_temp'), $this->config->item('area_media_flag_ok'));
                    $medias = $this->material->get_medias_byPid($playlist_id, $flag);
                    $media_tags = array();
                    if ($medias) {
                        foreach ($medias as $media) {
                            if ($media->tags) {
                                $media_tags = array_merge($media_tags, explode(',', $media->tags));
                            }
                        }
                        $media_tags = array_unique($media_tags);
                    }
                    //将删除后的media的tags与用户输入tags拼接
                    $result['tags'] = array_values(array_unique(array_merge($media_tags, $diffs)));
                }
            }


            $msg = $this->lang->line('delete.success');
        } else {
            $code = 1;
            $msg = $this->lang->line('warn.param');
        }
        $result['code'] = $code;
        $result['msg'] = $msg;
        echo json_encode($result);
    }



    public function edit_playlist_media()
    {
        $id = $this->input->get('id');
        $area_id = $this->input->get('area_id');
        $media_id = $this->input->get('media_id');
        $this->load->model('program');
        $this->load->model('material');
        $media = $this->program->get_playlist_area_media($id);
        $playlist = $this->program->get_playlist($media->playlist_id);
        $data['media'] = $media;
        $data['media_type'] = $this->material->get_media_type($media->media_id);
        $data['transmode_type'] = $this->program->get_area_transmode_type($area_id);
        $data['transmode'] = $this->lang->line('transmode');
        $data['playlist'] = $playlist;
        $this->load->view('program/campaign/screen_area_media', $data);
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
        $this->load->view('program/campaign/add_screen_area_webpage', $data);
    }
    //修改 播放列表中的 网页信息
    public function edit_playlist_media_webpage()
    {
        $id = $this->input->get('id');
        $area_id = $this->input->get('area_id');
        $media_id = $this->input->get('media_id');
        $this->load->model('program');
        $this->load->model('material');
        $media = $this->program->get_playlist_area_media($id);
        if ($media->starttime == '00:00') {
            $media->starttime = '';
        }
        if ($media->endtime == '00:00') {
            $media->endtime = '';
        }
        $data['media'] = $media;
        $data['media_type'] = $this->material->get_media_type($media->media_id);
        $this->load->view('program/campaign/edit_screen_area_webpage', $data);
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

            /*  $this->form_validation->set_rules('duration', $this->lang->line('duration'), 'trim|required');

            if ($this->form_validation->run() == FALSE) {
            $result = array('code'=>1, 'msg'=>validation_errors());
            } else
                */ {

                //    $duration = $this->input->post('duration');
                $transmode = $this->input->post('transmode');
                $transtime = $this->input->post('transtime');
                $imgfit = $this->input->post('imgfit');

                $code = 0;
                $msg = '';
                /*   if (strlen($duration) != 5 && strpos($duration, ':') === FALSE) {
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
                 */
            }

            if ($code > 0) {
                echo json_encode(array('code' => $code, 'msg' => $msg));
                return;
            }

            //$data = array( 'transmode'=>$transmode, 'transtime'=>$transtime, 'img_fitORfill'=>$imgfit);
            $data = array('transmode' => $transmode, 'img_fitORfill' => $imgfit);
            // $data = array('duration'=>$duration, 'transmode'=>$transmode, 'transtime'=>$transtime, 'img_fitORfill'=>$imgfit);
            $this->load->model('program');
            $this->program->update_area_media($data, $id);

            $result['code'] = 0;
            $result['msg'] = $this->lang->line('save.success');
        }


        echo json_encode($result);
    }

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
                $url_type = $this->input->post('urlType');
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

            $data = array('duration' => $duration, 'starttime' => $starttime, 'endtime' => $endtime, 'publish_url' => $url, 'updateF' => $updateF, 'url_type' => $url_type);
            $this->load->model('program');
            $this->program->update_area_media($data, $id);

            $result['code'] = 0;
            $result['msg'] = $this->lang->line('save.success');
        }

        echo json_encode($result);
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
        $playlist_settings = $this->program->get_playlist($playlist_id); //获取列表设置
        $rss_delimiter = $playlist_settings->rss_delimiter;     //rss分割标记
        //$rss_delimiter = "<<";     //rss分割标记
        $this->load->model('program');
        $rss = $this->program->get_playlist_area_last_rss($playlist_id, $area_id);
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
        $setting['playlist_id'] = $playlist_id;
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
        $setting['playlist_id'] = $playlist_id;
        $setting['area_id'] = $area_id;

        return $setting;
    }

    /**
     * 预览效果
     *
     */
    public function template_preview()
    {
        $this->load->helper('media');
        $this->load->model('program');
        $pls_id = $this->input->get('id');
        $this->load->model('program');
        $temp_id = $this->input->get('temp_id');

        $template = $this->template->get_template($temp_id);
        $area_list = $this->template->get_area_list($temp_id);
        if ($template) {
            $te_width = $template->w;
            $te_height = $template->h;
        } else {
            $te_width = $this->config->item('screen_width');
            $te_height = $this->config->item('screen_height');
        }
        $city_code = '';
        $w_city = '';
        $w_low = '';
        $w_high = '';
        $w_icon = '';
        $w_text = '';
        $w_data = array();
        //取出当前playlist下template的各个area的媒体文件
        if ($area_list) {
            foreach ($area_list as $area) {
                if ($area->area_type == $this->config->item('area_type_bg')) {
                    $bgs = $this->program->get_media_url($area->id, $pls_id);
                    if ($bgs) {
                        foreach ($bgs as $media) {
                            $area->main_url = $media->main_url;
                            if (file_exists($media->main_url)) {
                            } else {
                                $area->main_url = $media->main_url;
                            }
                        }
                    } else {
                        $area->main_url = '';
                    }
                } elseif ($area->area_type == $this->config->item('area_type_logo')) {
                    $bgs = $this->program->get_media_url($area->id, $pls_id);
                    if ($bgs) {
                        foreach ($bgs as $media) {
                            $area->tiny_url = $media->tiny_url;
                        }
                    } else {
                        $area->tiny_url = '';
                    }
                } elseif ($area->area_type == $this->config->item('area_type_image')) {
                    $bgs = $this->program->get_media_url($area->id, $pls_id);
                    if ($bgs) {
                        $urls = array();
                        $duration = array();
                        $transmode = array();
                        foreach ($bgs as $media) {
                            $urls[] = substr($media->preview_url, 1, strlen($media->preview_url));
                            $duration[] =  substr($media->duration, 3, 2);
                            $transmode[] = $media->transmode;
                            $area->main_url = $urls;
                            $area->duration = $duration;
                            $area->transmode = $transmode;
                        }
                    } else {
                        $area->main_url = '';
                    }
                } elseif ($area->area_type == $this->config->item('area_type_movie')) {
                    $bgs = $this->program->get_media_url($area->id, $pls_id);
                    if ($bgs) {
                        $urls = array();
                        foreach ($bgs as $media) {
                            //判断media_type是视频还是图片
                            //如果是视频，则取出.flv的视频预览文件
                            $base_path = $this->config->item('base_path');
                            if ($media->media_type == $this->config->item("media_type_video")) {
                                if ($media->source == $this->config->item("media_source_local")) {
                                    $base_path = $this->config->item('base_path');
                                    $relate_path = '/resources/preview/' . $this->get_cid() . '/video/';
                                    $dest_file = $media->signature . '.flv';
                                    $dest_ro = $media->signature . '_rotate.flv';
                                    $dest_path = $base_path . $relate_path;
                                    if (!(file_exists($dest_path . $dest_file) && filesize($dest_path . $dest_file) > 0)) {
                                        generate_preview_movie($media->full_path, $media->ext, $dest_path . $dest_file);
                                    }


                                    if (($te_width < $te_height) && $media->rotate != 1) {
                                        if (!(file_exists($dest_path . $dest_ro) && filesize($dest_path . $dest_ro) > 0)) {
                                            rotating_movie($dest_path . $dest_file, $dest_path . $dest_ro);
                                        }
                                        $urls[] = $relate_path . $dest_ro;
                                    } else {
                                        $urls[] = $relate_path . $dest_file;
                                    }

                                    $area->main_url = $urls;
                                }
                            }
                            //如果是图片，则取出图片的预览文件
                            /*
                            if($media->media_type == $this->config->item("media_type_image")){
                            $urls[]=$media->main_url;
                            $area->main_url = $urls;
                            }*/
                            if ($media->media_type == $this->config->item("media_type_image")) {
                                if ($media->source == $this->config->item("media_source_local")) {
                                    $urls[] = substr($media->preview_url, 1, strlen($media->preview_url));
                                } else {
                                    $urls[] = $media->main_url;
                                }

                                if ($media->ext != 'bmp') {
                                    $area->main_url = $urls;
                                }
                            }
                        }
                    } else {
                        $area->main_url = '';
                    }
                } elseif ($area->area_type == $this->config->item('area_type_text')) {
                    $setting = $this->program->get_playlist_area_text_setting($pls_id, $area->id);
                    if ($setting === false) {
                        $setting = $this->get_default_text_setting($pls_id, $area->id);
                        $id = $this->program->add_area_text_setting($setting, $this->get_uid());
                        $setting = $this->program->get_area_text_setting($id);
                    }
                    $area->setting = $setting;
                } elseif ($area->area_type == $this->config->item('area_type_date')) {
                    $setting = $this->program->get_area_time_setting($area->id, $pls_id);
                    if ($setting) {
                        $area->value = date($this->config->item('area_date_format_' . $setting->format));
                    }
                    $area->setting = $setting;
                } elseif ($area->area_type == $this->config->item('area_type_weather')) {
                    $setting = $this->program->get_area_weather_setting($area->id, $pls_id);
                    $area->setting = $setting;
                    $this->load->model('membership');
                    $this->load->helper('weather');
                    $company = $this->membership->get_company($template->company_id); //获取雅虎天气城市码
                    $city_code = $company->city_code;
                    $weather_format = $company->weather_format;
                    if (!$city_code) {
                        $city_code = '12712632';
                    }

                    if ($template->template_type) {
                        $weather = get_yahoo_weather_3days($city_code, $weather_format);
                        $w_city = $weather['city'];
                        $w_data = $weather['data'];
                        $w_low = array();
                        $w_high = array();
                        $w_icon = array();
                        $num = 0;
                        foreach ($w_data as $wd) {
                            $w_low[$num] = $wd['low'];
                            $w_high[$num] = $wd['high'];
                            $w_icon[$num] = $wd['iconNum'];
                            $num++;
                        }
                    } else {
                        $weather = get_weather($city_code, $weather_format);
                        $w_city = $weather['city'];
                        $w_low = $weather['low'];
                        $w_high = $weather['high'];
                        $icon = $weather['icon'];
                        $icon = explode('.', $icon);
                        $w_icon = $icon[0];
                        $w_text = $icon[1];
                    }
                } elseif ($area->area_type == $this->config->item('area_type_time')) {
                    $setting = $this->program->get_area_time_setting($area->id, $pls_id);
                    $area->setting = $setting;
                } elseif ($area->area_type == $this->config->item('area_type_webpage')) {
                    $media = $this->program->get_area_webpag_setting($pls_id, $area->id);
                    $url = array();
                    foreach ($media['data'] as $webpage) {
                        $url[] = $webpage->publish_url;
                    }
                    $area->setting = $url;
                }
            }
            //如果Static Text没有背景图片
            $bg_area_media = $this->program->get_static_bg_area($pls_id, 1, 1);
            if ($bg_area_media) {
                $array = new stdClass;
                $array->id = 1;
                $array->name = 'BG';
                $array->x = 0;
                $array->y = 0;
                $array->w = 960;
                $array->h = 540;
                $array->area_type = 9;
                $array->zindex = 0;
                $array->main_url = substr($bg_area_media[0]->publish_url, 1, strlen($bg_area_media[0]->publish_url));
                $area_list[] = $array;
            }
        }
        $data = $this->get_data();
        $data['area_list'] = $area_list;
        $data['template'] = $template;
        $data['width'] = $te_width;
        $data['height'] = $te_height;
        $data['w_city'] = $w_city;
        $data['w_low'] = $w_low;
        $data['w_high'] = $w_high;
        $data['w_icon'] = $w_icon;
        $data['w_text'] = $w_text;
        $data['pls_type'] = $template->template_type;
        $this->load->view('program/campaign/template_preview', $data);
    }

    //xml中特殊字符替换
    public function xmlencode($tag)
    {
        $tag = str_replace("&", "&amp;", $tag);
        $tag = str_replace("<", "&lt;", $tag);
        $tag = str_replace(">", "&gt;", $tag);
        $tag = str_replace("'", "&apos;", $tag);
        $tag = str_replace("\"", '&quot;', $tag);
        return $tag;
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
        $data['transmode_type'] = $this->program->get_area_transmode_type($area_id);
        $data['transmode'] = $this->lang->line('transmode');
        $data['area_id'] = $area_id;
        $data['type'] = $type;
        $data['playlistId'] = $this->input->get('playlist_id');

        $this->load->view('program/campaign/screen_all_area_media', $data);
    }

    public function save_playlist_area_media()
    {
        $result = array();
        $cid = $this->get_cid();
        $area_id = $this->input->post('areaId');
        $type = $this->input->post('type');

        $playlistId = $this->input->post('playlistId');


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
                        $this->program->update_all_area_media($data, $area_id, $playlistId);
                        $result['code'] = 0;
                        $result['msg'] = $this->lang->line('save.success');
                    }
                }
            }
            if ($type == 'transition') {
                $transmode = $this->input->post('transmode');
                $data = array('transmode' => $transmode);
                $this->load->model('program');
                $this->program->update_all_area_media($data, $area_id, $playlistId);
                $result['code'] = 0;
                $result['msg'] = $this->lang->line('save.success');
            }
        }
        echo json_encode($result);
    }

    /**
     * 修改播放列表中startTime
     *
     * @return
     */
    public function do_editStartTime()
    {
        $result = array();
        $id = $this->input->post('id');
        $areaId = $this->input->post('areaId');
        $startTime = $this->input->post('startTime');
        $endTime = $this->input->post('endTime');

        if ($startTime > $endTime && $endTime != '00:00') {
            $result = array('code' => 1, 'msg' => 'StartTime must be less than EndTime!');
        } else {
            $this->load->model('program');
            if ($this->program->edit_startTime(array('area_id' => $areaId, 'starttime' => $startTime), $id)) {
                $result = array('code' => 0, 'msg' => '');
            }
        }

        echo json_encode($result);
    }

    /**
     * 修改播放列表中endTime
     *
     * @return
     */
    public function do_editEndTime()
    {
        $result = array();
        $id = $this->input->post('id');
        $areaId = $this->input->post('areaId');
        $endTime = $this->input->post('endTime');
        $startTime = $this->input->post('startTime');

        if ($startTime > $endTime && $endTime != '00:00') {
            $result = array('code' => 1, 'msg' => 'StartTime must be less than EndTime!');
        } else {
            $this->load->model('program');
            if ($this->program->edit_endTime(array('area_id' => $areaId, 'endtime' => $endTime), $id)) {
                $result = array('code' => 0, 'msg' => '');
            }
        }

        echo json_encode($result);
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
            if ($this->program->edit_endTime(array('area_id' => $areaId, 'duration' => $playTime), $id)) {
                $result = array('code' => 0, 'msg' => 'test');
            }
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
        if ($this->program->edit_status(array('area_id' => $areaId), $id)) {
            $result = array('code' => 0, 'msg' => '');
        }
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
        if ($this->program->edit_reload(array('area_id' => $areaId), $id)) {
            $result = array('code' => 0, 'msg' => '');
        }
    }

    //修改播放列表 网页区域的 开始结束日期
    public function do_updateWebpageDate()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $time = $this->input->post('date');

        $this->load->model('program');
        if ($type == 1) {
            if ($this->program->edit_date(array('startdate' => $time), $id)) {
                $result = array('code' => 0, 'msg' => 'OK');
            }
        } else {
            if ($this->program->edit_date(array('enddate' => $time), $id)) {
                $result = array('code' => 0, 'msg' => 'OK');
            }
        }
    }

    //修改rss的分割标记
    public function do_updateRssFlag()
    {
        $value = $this->input->post('val');
        $pid = $this->input->post('id');
        $this->load->model('program');
        $this->program->update_playlist(array('rss_delimiter' => $value), $pid);
    }

    public function do_editAreaStatus()
    {
        $status = $this->input->post('status');
        $playlistId = $this->input->post('playlistId');
        $areaId = $this->input->post('areaId');
        $this->load->model('program');
        $this->program->edit_areaStatus(array('status' => $status), $playlistId, $areaId);
    }

    /**
     * 设置字体文件信息
     *
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



    public function do_refresh_all_campaigns()
    {
        $this->load->model('program');
        set_time_limit(0);

        $campaigns = $this->program->get_published_campaign($this->get_cid());
        $this->load->model('membership');
        $company = $this->membership->get_company($this->get_cid());


        $result = array();

        if ($campaigns && $campaigns['total'] > 0) {
            $data['campaigns'] = $campaigns['data'];
            $today = strtotime(date("Y-m-d"));

            foreach ($campaigns['data'] as $campaign) {
                if (strtotime($campaign->end_date) < $today) {
                    continue;
                }

                //skipping locked campaigns
                if (isset($campaign->is_locked) && $campaign->is_locked) {
                    continue;
                }
                $update_arry = array('published' => 3);


                $bind_criteria = $campaign->and_criterias;
                if ($company->pId) {
                    if ($company->criterion_id) {
                        if ($bind_criteria) {
                            $bind_criteria = $bind_criteria . "," . $company->criterion_id;
                        } else {
                            $bind_criteria = $company->criterion_id;
                        }
                    }
                }

                $players = $this->program->get_player_by_criterias($campaign->criterias, $bind_criteria, $campaign->ex_criterias, $campaign->players, $campaign->tags, $campaign->ex_players, $campaign->and_criteria_or);


                if ($players) {
                    $this->program->detach_campaign_player($campaign->id);

                    foreach ($players as $player) {
                        $item = array('player_id' => $player->id, 'campaign_id' => $campaign->id);
                        $this->program->sync_campaign_player($campaign->id, $item);
                    }
                    $this->program->update_affected_players($campaign->id);
                }

                if ($this->config->item("digooh_timer_per_hour")) {
                    if (isset($campaign->media_cnt) && !$campaign->media_cnt) {
                        $media_cnt = $this->program->get_playlist_media_cnt($campaign->id);
                        $update_arry['media_cnt'] = $media_cnt;
                    }
                }
                $this->program->update_playlist($update_arry, $campaign->id);
            }

            $all_done = true;

            foreach ($campaigns['data'] as $campaign) {
                if (strtotime($campaign->end_date) < $today) {
                    continue;
                }

                $this->program->save_campaign_xml($campaign->id);
                $this->program->update_playlist(array('published' => 1, 'update_time' => date('Y-m-d H:i:s')), $campaign->id);
            }
        }
        if ($all_done) {
            $result['code'] = 0;
            $result['msg'] =  $this->lang->line('campaign.refresh.success');
        }
        echo json_encode($result);
    }


    private function generate_preview_images($file_path, $file_name, &$file_out_path)
    {
        $this->load->library('image_lib');
        //check output
        if (!file_exists($file_out_path)) {
            if (!is_dir($file_out_path)) {
                if (mkdir($file_out_path, 0777, true) === false) {
                    $file_out_path = $file_path;
                }
            }
        }
        if (substr($file_out_path, -1) != '/') {
            $file_out_path .= '/';
        }

        $full_path = $file_path . $file_name;
        list($width, $height) = getimagesize($full_path);

        $main = array();

        $tiny = array();
        //压缩小图
        if ($height > $this->config->item('image_tiny_height')) {
            $config['image_library'] = 'gd2';
            $config['source_image'] = $full_path;
            $config['new_image'] = $file_out_path . 'tiny_' . $file_name;
            $config['create_thumb'] = false;
            $config['maintain_ratio'] = true;
            $config['quality'] = 100;

            $config['height'] = $this->config->item('image_tiny_height');
            $config['width'] = $this->config->item('image_tiny_height') * $width / $height;
            $this->image_lib->initialize($config);


            if (!$this->image_lib->resize()) {
                $tiny = array('code' => 1, 'msg' => $this->image_lib->display_errors());
            } else {
                $tiny = array('code' => 0, 'msg' => '', 'full_path' => $config['new_image'], 'file_name' => 'tiny_' . $file_name, 'width' => $this->config->item('image_main_width'), 'height' => $this->config->item('image_main_height'));
            }
        } else {
            $tiny['code'] = 0;
            $tiny['msg'] = '';
            $tiny['file_name'] = 'tiny_' . $file_name;
            $tiny['width'] = $width;
            $tiny['height'] = $height;
            @copy($full_path, $file_out_path . 'tiny_' . $file_name);
        }

        return array('main' => $main, 'tiny' => $tiny);
    }

    private function get_first_frame($file_path, $file_name)
    {
        $tmp = preg_split('/[.]/', $file_name);
        $original_name = 'video_' . $tmp[0] . '.jpeg';
        $full_path = $file_path . $original_name;


        $absPath = $this->config->item('ffmpeg');
        //-itsoffset -3 -y -f image2 -vcodec mjpeg -vframes 1 -an
        // $command = $absPath." -ss 00:00:00 -i $movie -y -f image2  -vframes 1 $size $outfile";
        $input = $file_path . $file_name;

        $command = $absPath . " -ss 00:00:00 -i $input -vframes 1 -vf scale=-2:360 -f image2 -y  $full_path";
        @exec($command, $output, $return);

        if ($return == 0) {
            $size = @getimagesize($full_path);
            if ($size) {
                return array('width' => $size[0], 'height' => $size[1], 'file_name' => $original_name, 'full_path' => $full_path);
            } else {
                return array('width' => -1, 'height' => -1, 'file_name' => $original_name, 'full_path' => $full_path);
            }
        } else {
            return false;
        }
    }

    private function get_video_mp4($media)
    {
        if ($media) {
            if ($media['media_type'] == $this->config->item("media_type_video")) {
                $base_path = $this->config->item('base_path');
                $relate_path = '/resources/preview/' . $this->get_cid() . '/video/';
                $dest_file = $media['signature'] . '.mp4';
                $dest_path = $base_path . $relate_path;
                if (!file_exists($dest_path) || !is_dir($dest_path)) {
                    mkdir($dest_path, 0755, true);
                }

                //已经存在，不转
                if (file_exists($dest_path . $dest_file) && filesize($dest_path . $dest_file) > 0) {
                    $result['video'] = $relate_path . $dest_file;
                } elseif (generate_preview_movie($media['full_path'], $media['ext'], $dest_path . $dest_file)) {
                    $result['video'] = $relate_path . $dest_file;
                }

                return $result;
            }
        }
        return false;
    }


    public function campaign_upload_medias()
    {
        set_time_limit(0); //unlimit upload time
        ini_set("upload_tmp_dir", $this->config->item('tmp'));

        $cid = $this->get_cid();
        $uid = $this->get_uid();
        $preview_path = './resources/preview/' . $cid;

        $folderid = $this->input->post('foldersel');
        $playlist_id = $this->input->post('playlist_id');
        $area_id = $this->input->post('area_id');
        $area_type = $this->input->post('area_type');

        $tags = array();

        $this->load->model('material');


        if (!file_exists($preview_path)) {
            mkdir($preview_path);
        }

        $config['upload_path'] = './resources/' . $cid;

        if (!file_exists($config['upload_path'])) {
            mkdir($config['upload_path'], 0744, true);
        }


        $config['allowed_types'] = '*';
        $config['max_width'] = $this->config->item('image_max_width');
        $config['max_height'] = $this->config->item('image_max_height');
        $config['encrypt_name'] = true;
        $this->load->model('membership');
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        $result = array();
        if (!$this->upload->do_upload('qqfile')) {
            $result = array('code' => 1, 'msg' => $this->upload->display_errors());
        } else {
            $data = $this->upload->data();

            $media = array('name' => $this->material->get_next_media_name($data['orig_name'], $cid), 'ext' => substr($data['file_ext'], 1), 'orig_name' => $data['orig_name'], 'full_path' => $config['upload_path'] . '/' . $data['file_name'], 'file_size' => $data['file_size']);
            $media2 = array('name' => $data['orig_name'], 'ext' => substr($data['file_ext'], 1), 'orig_name' => $data['orig_name'], 'full_path' => $config['upload_path'] . '/' . $data['file_name'],  'file_size' => $data['file_size']);
            $media['signature'] = md5_file($data['full_path']);

            if (isset($folderid) && $folderid != 0) {
                $this->load->model('material');
                $folder = $this->material->get_folder($folderid);

                if ($folder->tags) {
                    $tags = explode(",", $folder->tags);
                }

                $foldersettings = array(
                    'date_flag' => $folder->date_flag,
                    'start_date' => $folder->start_date,
                    'end_date' => $folder->end_date

                );
                if ($data['is_image']) {
                    $foldersettings['play_time'] = $folder->play_time;
                }
            }
            if ($data['is_image']) {
                $preview = $this->generate_preview_images($data['file_path'], $data['file_name'], $preview_path);
                $media['media_type'] = $this->config->item('media_type_image');
                $media2['media_type'] = $this->config->item('media_type_image');

                $media['width'] = $data['image_width'];
                $media['height'] = $data['image_height'];
            } else {
                $media['media_type'] = $this->config->item('media_type_video');
                $media2['media_type'] = $this->config->item('media_type_video');


                $first = $this->get_first_frame($data['file_path'], $data['file_name']);
                if ($first) {
                    $preview = $this->generate_preview_images($data['file_path'], $first['file_name'], $preview_path);
                    @unlink($data['file_path'] . $first['file_name']);
                    $preview['enable'] = 1;
                } else {
                    $preview['enable'] = 0;
                }
                $this->load->helper('media');
                $info = get_movie_info($data['full_path']);

                if ($info) {
                    $media['width'] = $info['width'];
                    $media['height'] = $info['height'];
                    $media['play_time'] = $info['play_time'];
                }

                $this->get_video_mp4($media);
            }


            $result = array('code' => 0, 'msg' => '', 'file' => $data);
            $this->load->model('material');

            $preview_status = 0;


            if ($preview['tiny']['code'] == 0) {
                $preview_status = 2;
                $media['tiny_url'] = '/resources/preview/' . $cid . '/' . $preview['tiny']['file_name'];
            }

            $media['folder_id'] = $folderid;
            $media['preview_status'] = $preview_status;
            if (isset($foldersettings)) {
                $media = array_merge($media, $foldersettings);
            } else {
                if ($data['is_image']) {
                    $cid = $this->get_cid();
                    $company = $this->membership->get_company($cid);
                    $media['play_time'] = $company->default_play_time;
                }
            }

            $this->load->model('membership');
            $arr_settings = $this->membership->get_user_settings($this->get_uid());


            if ($arr_settings->media_confirm_flag == 1) {
                $flag = $this->material->get_same_media_name($data['orig_name'], $cid);
                if ($flag) {
                    $id = $this->material->update_media($media, $flag[0]->id);
                    $arr = $this->material->get_pb_id($flag[0]->id);
                    if ($arr) {
                        foreach ($arr as $pid) {
                            $str .= $pid->id . ',';
                        }
                        $this->material->update_pl(substr($str, 0, -1));
                    }
                } else {
                    $id = $this->material->add_media($media, $cid, $uid, $tags);
                }
            } else {
                $media['folder_id'] = $folderid;
                $id = $this->material->add_media($media, $cid, $uid, $tags);
            }
            if ($id) {
                $result['success'] = true;
                $result['id'] = $id;
            } else {
                $result['success'] = false;
                $result['code'] = 1;
                $result['msg'] = sprintf($this->lang->line('upload.file.error'), $data['orig_name']);
            }
        }
        @unlink($_FILES['qqfile']['tmp_name']);


        if ($result['success'] == true) {
            $transmode = 26;
            $transtime = 0.5;
            $duration = '00:20';
            $rotate = 1;
            if (!$data['is_image']) {
                $duration = $media['play_time'];
            }
            $this->load->model('program');
            $max_position = $this->program->get_playlist_area_media_max_position($playlist_id, $area_id);


            if ($area_type == $this->config->item('area_type_bg') || $area_type == $this->config->item('area_type_logo')) {
                $this->program->delete_playlist_area_media($playlist_id, $area_id);

                $transtime = -1;
                $transmode = -1;
                $duration = '00:00';
            }

            $max_position++;

            $id = $this->program->add_area_media(array('playlist_id' => $playlist_id, 'area_id' => $area_id, 'media_id' => $id, 'duration' => $duration, 'transmode' => $transmode, 'position' => $max_position, 'rotate' => $rotate), $uid);
        }

        $result['success'] = true;
        echo json_encode($result);
    }

    public function do_calculate()
    {
        $this->load->model('membership');
        $this->load->model('program');

        $criteria = $this->input->post('criteria');

        //$playlist = $this->input->post('playlist');
        $priority = $this->input->post('priority');

        if ($this->config->item("cam_with_player")) {
            $bind_players = $this->input->post('players');
        } else {
            $bind_players = false;
        }

        if (!$criteria && !$bind_players) {
            $result['player_num'] = 0;
            echo json_encode($result);
            return;
        }

        $players = $this->get_affected_players();

        $data['player_num'] = 0;

        if ($players) {
            $data['player_num'] = count($players);
            if ($priority != 3 && $priority != 6) {
                $playlist = array('priority' => $priority, 'start_date' => $this->input->post('start_date'), 'end_date' => $this->input->post('end_date'), 'startH' => false, 'endH' => false);
                if (!$this->input->post('time_flag')) {
                    $playlist['startH'] = $this->input->post('start_timeH');
                    $playlist['endH'] = $this->input->post('end_timeH');
                }

                $result = $this->cal_usage($players, $playlist);
                $data = $result;
                if ($result['capacitys']) {
                    $data['players'] = $result['capacitys'];
                    $data['ob_players'] =  array_filter($data['players'], function ($value) {
                        if ($value['ob']) {
                            return true;
                        }
                        return false;
                    });
                    unset($data['capacitys']);
                }
            }
        }


        $ret['data'] = $this->load->view("bootstrap/campaigns/selected_players", $data, true);
        echo json_encode($ret);
    }


    public function cal_usage($players, $playlist)
    {
        if (!$players) {
            $result['player_num'] = 0;
        } else {
            $this->load->model('program');
            $this->load->model('membership');

            // $company = $this->membership->get_company($this->get_cid());
            //$nxslot = $company->nxslot;
            $nxslot = $this->get_nxslot();

            $result['player_num'] = count($players);


            $cid = $this->get_cid();
            $capacitys = $this->program->get_players_capcity($players, $playlist['start_date'], $playlist['end_date'], $playlist['startH'], $playlist['endH'], $cid);


            if ($capacitys) {
                $total_used = 0;
                $least_free = 3600;
                $total_free = 0;
                $total_secs = 0;
                $playcnttimes = 0;
                $pl_used = 0;
                if (!isset($playlist['media_cnt']) || $playlist['media_cnt'] == 0) {
                    $media_cnt = 1;
                    $total_time = 10;
                } else {
                    $media_cnt = $playlist['media_cnt'];
                    $total_time = $playlist['total_time'];
                }
                $duration = $total_time / $media_cnt;


                $ob = false;
                foreach ($capacitys as $key => $capacity) {
                    //skipping player that was taken 100% by partners
                    $total_secs += $capacity['total_secs'];
                    $total_used += $capacity['total_capcity'];
                    $total_free += $capacity['total_free'];
                    if ($least_free > $capacity['least_free'] && $capacity['total_secs'] > 0) {
                        $least_free = $capacity['least_free'];
                    }
                }

                $ob_cnt = 0;

                $play_type = $this->input->post('play_cnt_type');
                $play_count = $this->input->post('play_count') ?: 1;

                foreach ($capacitys as $key => $capacity) {
                    //skipping player that was taken 100% by partners

                    if ($play_type == 0) {
                        $pl_used = $play_count * $duration;
                    } elseif ($play_type == 1) {
                        $pl_used = 3600 * ($play_count * $capacity['quota'] / 100) / 100;
                    } elseif ($play_type == 2) {
                        $timers_per_hour = $play_count / ($total_secs / 3600);
                        $pl_used = $timers_per_hour * $total_time;
                    } elseif ($play_type == 9) {
                        $pl_used = 360 / $nxslot * $duration;
                    }

                    if ($pl_used > $capacity['least_free']) {
                        $capacitys[$key]['ob'] = true;
                        $ob_cnt++;
                        $ob = true;
                    } else {
                        $capacitys[$key]['ob'] = false;
                    }
                }


                $result['capacitys'] = $capacitys;

                $valid_player_cnt = count($capacitys);
                if ($valid_player_cnt) {
                    $result['ava_used'] = sprintf("%.2f", $total_used / $valid_player_cnt) . '%';
                } else {
                    $result['ava_used'] = 0;
                }


                $result['lease_times'] = $least_free / $duration;

                $result['total_free'] = $total_free . " seconds";
                $result['ob_cnt'] = $ob_cnt;

                if ($playlist['priority'] == 1 || $playlist['priority'] == 2) {
                    $total_times = 0;

                    if ($ob) {
                        $result['total_times'] = " ";
                        $result['cost'] = " ";
                    } else {
                        $result['total_times'] = floor($total_times);
                    }


                    if (!$ob && $this->config->item("cost_entry")) {
                        $costs = $this->membership->get_cost($this->get_cid());
                        if ($costs) {
                            if ($costs->cost1_condition < $costs->cost2_condition) {
                                if ($total_times >= $costs->cost2_condition) {
                                    $cost = $total_times * $costs->cost2;
                                } elseif ($total_times >= $costs->cost1_condition) {
                                    $cost = $total_times * $costs->cost1;
                                } else {
                                    $cost = $total_times * $costs->cost_per_play;
                                }
                            } else {
                                if ($total_times >= $costs->cost1_condition) {
                                    $cost = $total_times * $costs->cost1;
                                } elseif ($total_times >= $costs->cost2_condition) {
                                    $cost = $total_times * $costs->cost2;
                                } else {
                                    $cost = $total_times * $costs->cost_per_play;
                                }
                            }
                            $result['cost'] = round($cost, 2);
                        }
                    }

                    $result['OverBooking'] = $ob;
                    if ($ob) {
                        $result['ob_msg'] = $this->lang->line('cal_ob_msg');
                    }
                }
            }
        }
        return $result;
    }


    public function sync_campaign($id, $priority = -1)
    {
        $tag = $this->input->post('tags');

        $criteria = $this->input->post('criteria');
        $and_criteria = $this->input->post('and_criteria');
        $and_criteria_or = $this->input->post('and_criteria_or');

        $criteria_ex = $this->input->post('ex_criteria');
        $players = $this->input->post('players');
        //$ex_players = $this->input->post('ex_players');
        $ex_players = $this->get_ob_players();



        $this->load->model('program');

        if ($tag) {

            if ($priority == 3 || $priority == 6) {
                $this->program->detach_tags($id, 'App\Playlist');
            } else {
                $this->program->sync_tags($id, $tag, 'App\Playlist');
            }
        } else {
            $this->program->detach_tags($id, 'App\Playlist');
        }

        if ($players) {
            $this->program->sync_players($id, $players);
        } else {
            $this->program->detach_players($id);
        }

        if ($ex_players) {

            $this->program->sync_players($id, $ex_players, 1);
        } else {
            $this->program->detach_players($id, 1);
        }

        if ($criteria) {
            $this->program->sync_criteria($id, $criteria, 'App\Playlist');
        } else {
            $this->program->detach_criteria($id, 'App\Playlist', 0);
        }
        if ($and_criteria) {
            $this->program->sync_criteria($id, $and_criteria, 'App\Playlist', 1);
        } else {
            $this->program->detach_criteria($id, 'App\Playlist', 1);
        }

        if ($and_criteria_or) {
            $this->program->sync_criteria($id, $and_criteria_or, 'App\Playlist', 3);
        } else {
            $this->program->detach_criteria($id, 'App\Playlist', 3);
        }


        if ($criteria_ex) {
            $this->program->sync_criteria($id, $criteria_ex, 'App\Playlist', 2);
        } else {
            $this->program->detach_criteria($id, 'App\Playlist', 2);
        }
    }

    public function devices()
    {
        $id = $this->input->get('id');

        $this->load->model('program');
        $name = $this->input->get('name');
        $data['id'] = $id;
        $data['name'] = $name;

        $campaign = $this->program->get_playlist($id);

        $affect_players = $this->program->get_player_by_campaign($id);

        if ($affect_players) {
            foreach ($affect_players as $player) {
                $workingday = 7;
                $offweekdays  = isset($player->offweekdays) && !empty($player->offweekdays) ? $player->offweekdays : null;

                if ($offweekdays) {
                    $workingday = 7 - count(explode(",", $player->offweekdays));
                }

                $days = $this->get_camapaign_working_days($campaign->start_date, $campaign->end_date, $offweekdays);
                $player->daysperweek = $workingday;
                $player->wokringdays = $days;
            }
        }

        $data['affect_players'] = $affect_players;
        $exclude_players = $this->program->get_excluded_player_by_campaign($id);

        if ($exclude_players) {
            foreach ($exclude_players as $player) {
                $workingday = 7;
                $offweekdays  = isset($player->offweekdays) && !empty($player->offweekdays) ? $player->offweekdays : null;

                if ($offweekdays) {
                    $workingday = 7 - count(explode(",", $player->offweekdays));
                }
                $days = $this->get_camapaign_working_days($campaign->start_date, $campaign->end_date, $offweekdays);
                $player->daysperweek = $workingday;
                $player->wokringdays = $days;
            }
        }

        $data['exclude_players'] = $exclude_players;

        $this->load->view('program/campaign/campaign_devices', $data);
    }

    private function get_camapaign_working_days($start_date, $end_date, $offweekdays = '')
    {
        $dateStart = new DateTime($start_date);
        $checkday = $dateStart;

        $dateEnd = new DateTime($end_date);
        $dateEnd->add(new DateInterval('P1D'));

        $days = $dateEnd->diff($dateStart)->days;

        if ($offweekdays && !empty($offweekdays)) {
            $offweekd = explode(",", $offweekdays);


            while ($checkday->format('U') < $dateEnd->format('U')) {


                //  $weekd = date("w", $checkday);
                //  $weekd = $weekd==0?7:$weekd;
                $weekd = $checkday->format('N');
                //
                if (in_array($weekd, $offweekd)) {
                    $days--;
                }
                //$checkday=strtotime("+1 days", $checkday);
                $checkday->add(new DateInterval('P1D'));
            }
        }
        return $days;
    }
    public function export_devices()
    {
        $id = $this->input->get('id');

        $this->load->model('program');
        $campaign = $this->program->get_playlist($id);
        if ($campaign) {
            $name = $campaign->name;

            $affect_players = $this->program->get_player_by_campaign($id);

            $exclude_players = $this->program->get_excluded_player_by_campaign($id);
        }
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle('Selected Devices');

        $exline = 2;

        $worksheet->setCellValueByColumnAndRow(1, 1, $this->lang->line('name'));
        $worksheet->setCellValueByColumnAndRow(2, 1, "SDAW ID");
        $worksheet->setCellValueByColumnAndRow(3, 1, "QID");
        $worksheet->setCellValueByColumnAndRow(4, 1, "Day Per Week");
        $worksheet->setCellValueByColumnAndRow(5, 1, "Days in Campaign");



        $worksheet->getColumnDimension('A')->setWidth(40);
        $worksheet->getColumnDimension('B')->setWidth(40);
        $worksheet->getColumnDimension('C')->setWidth(40);

        $worksheet->getStyle('A')->getNumberFormat()->setFormatCode('#');
        $worksheet->getStyle('B')->getNumberFormat()->setFormatCode('#');
        $worksheet->getStyle('C')->getNumberFormat()->setFormatCode('#');



        if ($affect_players) {
            $worksheet->setTitle('Selected Devices (' . count($affect_players) . ')');

            foreach ($affect_players as $player) {
                $workingday = 7;
                $offweekdays  = isset($player->offweekdays) && !empty($player->offweekdays) ? $player->offweekdays : null;

                if ($offweekdays) {
                    $workingday = 7 - count(explode(",", $player->offweekdays));
                }
                $days = $this->get_camapaign_working_days($campaign->start_date, $campaign->end_date, $offweekdays);
                $worksheet->setCellValue('A' . $exline, $player->name)
                    ->setCellValue('B' . $exline, $player->custom_sn1)
                    ->setCellValue('C' . $exline, $player->custom_sn2)
                    ->setCellValue('D' . $exline, $workingday)
                    ->setCellValue('E' . $exline, $days);
                $exline++;
            }
        }

        if ($exclude_players) {
            $spreadsheet->createSheet();
            $worksheet = $spreadsheet->getSheet(1);
            $worksheet->setTitle('Excluded Devices (' . count($exclude_players) . ')');

            $exline = 2;

            $worksheet->setCellValueByColumnAndRow(1, 1, $this->lang->line('name'));
            $worksheet->setCellValueByColumnAndRow(2, 1, "SDAW ID");
            $worksheet->setCellValueByColumnAndRow(3, 1, "QID");
            $worksheet->setCellValueByColumnAndRow(4, 1, "Day Per Week");
            $worksheet->setCellValueByColumnAndRow(5, 1, "Days in Campaign");

            $worksheet->getColumnDimension('A')->setWidth(40);
            $worksheet->getColumnDimension('B')->setWidth(40);
            $worksheet->getColumnDimension('C')->setWidth(40);

            $worksheet->getStyle('A')->getNumberFormat()->setFormatCode('#');
            $worksheet->getStyle('B')->getNumberFormat()->setFormatCode('#');
            $worksheet->getStyle('C')->getNumberFormat()->setFormatCode('#');


            foreach ($exclude_players as $player) {
                $workingday = 7;
                $offweekdays  = isset($player->offweekdays) && !empty($player->offweekdays) ? $player->offweekdays : null;

                if ($offweekdays) {
                    $workingday = 7 - count(explode(",", $player->offweekdays));
                }
                $days = $this->get_camapaign_working_days($campaign->start_date, $campaign->end_date, $offweekdays);

                $worksheet->setCellValue('A' . $exline, $player->name)
                    ->setCellValue('B' . $exline, $player->custom_sn1)
                    ->setCellValue('C' . $exline, $player->custom_sn2)
                    ->setCellValue('D' . $exline, $workingday)
                    ->setCellValue('E' . $exline, $days);
                $exline++;
            }
        }



        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);




        unset($spreadsheet);


        $filename = "Devices of " . $name . ".xlsx";
        header("Content-Disposition: attachment;filename=$filename");
        //header("Content-Transfer-Encoding:binary");
        //header("File-Name:$filename");
        $writer->save('php://output');
    }

    private function get_ob_players()
    {
        $ex_players =  $this->input->post('ex_players');
        $ob_players = $this->input->post('ob_ids');

        if ($ob_players) {
            $ob_players = json_decode($ob_players);
            if ($ex_players) {
                $ex_players = array_merge($ex_players, $ob_players);
            } else {
                $ex_players = $ob_players;
            }
        }
        return $ex_players;
    }

    private function get_affected_players()
    {

        $this->load->model('program');
        $this->load->model('membership');
        $company = $this->membership->get_company($this->get_cid());

        $bind_criteria = $this->input->post('and_criteria');

        if ($company->pId) {
            if ($company->criterion_id) {
                if ($bind_criteria) {
                    if (!is_array($bind_criteria)) {
                        $bind_criteria = explode(',', $bind_criteria);
                    }
                    $bind_criteria[] = $company->criterion_id;
                } else {
                    if ($this->input->post('criteria')) {
                        $bind_criteria[] = $company->criterion_id;
                    }
                }
            }
        }

        $ex_players =  $this->get_ob_players();

        $players = $this->program->get_player_by_criterias(
            $this->input->post('criteria'),
            $bind_criteria,
            $this->input->post('ex_criteria'),
            $this->input->post('players'),
            $this->input->post('tags'),
            $ex_players,
            $this->input->post('and_criteria_or')
        );

        return $players;
    }
    public function getAreasData()
    {
        $data = $this->get_data();
        $template_id = $this->input->get('template_id');
        $playlist_id = $this->input->get('playlist_id');
        $data['success'] = 0;
        if ($template_id) {
            $data['success'] = 1;
            $areas = $this->getAreas($template_id, $playlist_id);
            $data['area_list'] = $areas;
        }
        echo json_encode($data);
    }

    public function getAreasView()
    {
        $data = $this->get_data();
        $template_id = $this->input->get('template_id');
        $playlist_id = $this->input->get('playlist_id');
        $selected_master = $this->input->get('master_area_id');

        if (!$template_id) {
            echo [];
            return;
        }

        $areas = $this->getAreas($template_id, $playlist_id);
        $data['area_list'] = $areas;
        $data['selected_master'] = $selected_master;

        $this->load->model('material');
        $data['texts'] = $this->material->get_text_list($this->get_cid())['data'];

        if ($this->config->item('with_register_feature')) {
            $this->load->model('store');
            $stores = $this->store->get_stores_by_user($this->get_uid());


            $data['stores'] = $stores;
        }

        $this->load->view('bootstrap/template_campaigns/areas', $data);
    }

    public function getAreas($template_id, $playlist_id = 0)
    {
        if (!$template_id) {
            return [];
        }
        $this->load->model('template');
        $this->load->model('program');
        $areas = $this->template->get_area_list($template_id);
        if ($areas) {
            foreach ($areas as $area) {
                $id_number = false;
                if ($playlist_id) {
                    switch ($area->area_type) {
                        case $this->config->item('area_type_bg'):
                            $setting = $this->template->get_area_image_setting($area->id);
                            if (!empty($setting)) {
                                $area->setting = $setting;
                            }
                            break;
                        case $this->config->item('area_type_image'):
                        case $this->config->item('area_type_movie'):
                        case $this->config->item('area_type_mask'):
                        case $this->config->item('area_type_logo'):
                            $media = $this->program->getPlaylistAreaMedia($playlist_id, $area->id);
                            $area->media = json_encode($media);
                            break;
                        case $this->config->item('area_type_text'):
                            $text = $this->program->get_playlist_text($playlist_id);
                            if ($text) {
                                $area->motion_text = $text->text;
                            } else {
                                $area->motion_text = '';
                            }

                            break;
                        case $this->config->item('area_type_webpage'):
                            //$media = $this->program->get_playlist_webpage_list($playlist_id);
                            $media = $this->program->get_playlist_mce_list($playlist_id);
                            $area->media = json_encode($media);
                            break;
                        case $this->config->item('area_type_id'):

                            $id_number = $this->program->getPlaylistAreaID($playlist_id, $area->id);
                            if ($id_number) {
                                if ($id_number->type == 4 && $id_number->id_number) {
                                    $this->load->model('product');
                                    $product = $this->product->get_by_id($id_number->id_number);

                                    if ($product) {
                                        $id_number->store_id = $product->store_id;
                                    }
                                }
                            }
                            $area->idData = $id_number;
                            break;
                    }
                }
                if (!$id_number) {
                    if ($area->area_type == $this->config->item('area_type_id')) {
                        $this->load->model('template');
                        $extra_settings = $this->template->get_area_extra_setting($area->id);
                        if ($extra_settings) {
                            $id_number = new stdClass();

                            $id_number->type = $extra_settings->style;
                        }
                        $area->idData = $id_number;
                    }
                }
            }
        }

        return $areas;
    }
}
