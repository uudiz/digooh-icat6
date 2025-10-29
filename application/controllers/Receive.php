<?php

/**
 * API for client
 *
 */

use Spatie\ArrayToXml\ArrayToXml;


class Receive extends CI_Controller
{
    private $sn;
    private $player;
    private $sep;
    private $params;
    private $enccom = false;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('device');
        $this->pre_check();
        $this->sep = "\n";


        /*	if($this->config->item('accesslog')){
                $this->accesslog();
            }
        */
    }

    /**
     * 打印accessLog
     *
     * @return
     */
    private function accesslog()
    {
        if (!is_dir('logs')) {
            mkdir('logs');
        }

        $h = fopen('logs/accesslog.' . date('Y-m-d') . '.log', 'a+');
        if ($h) {
            $log = date('Y-m-d H:i:s') . '|' . $_SERVER['REQUEST_METHOD'] . '|' . $_SERVER['REQUEST_URI'] . $this->sep;
            fwrite($h, $log);
            fclose($h);
        }
    }

    /**
     * 验证当前SN是否存在或者是否合法
     *
     * @return
     */


    public function pre_check()
    {
        $this->params = $this->convertUrlQuery($_SERVER['REQUEST_URI']);


        if (!isset($this->params['sn'])) {
            set_status_header(404, 'Unkown request!');
            exit();
        }
        //$this->sn = $this->input->get('sn');
        $this->sn = $this->params['sn'];


        if (($this->player = $this->device->get_player_by_sn($this->sn)) == false) {
            set_status_header(404, 'SN[' . $this->sn . '] Not found!');
            exit();
        } else {
            //update IP INFO
            $ip = $_SERVER["REMOTE_ADDR"];
            $array = array('last_ip' => $ip, 'enccom' => $this->enccom, 'last_connect' => date('Y-m-d H:i:s'));
            $this->device->update_player($array, $this->player->id);
        }
    }

    /**
     * 客户机登录
     *
     * sn: 客户机序号
     * model:终端的型号
     * mac:终端的MAC地址
     * ver:终端的软件版本
     *
     * @return
     */
    public function login()
    {
        /*
        $model = $this->input->get('model');
        $mac   = $this->input->get('mac');
        $ver   = $this->input->get('ver');
        //新添加
        $vol   = $this->input->get('vol');
        $spa   = $this->input->get('spa');
        $gmt   = $this->input->get('gmt');
        */
        $model = $this->params['model'];
        $mac   = $this->params['mac'];
        $ver   = $this->params['ver'];
        //新添加
        $vol   = $this->params['vol'];
        $spa   = $this->params['spa'];
        $gmt   = $this->params['gmt'];


        $this->device->do_login($this->sn, $model, $mac, $ver, $vol, $spa, $gmt);
        $version = $this->device->get_upgrade_version($this->sn);
        if ($version == $ver) {
            $this->device->remove_upgrade_version($this->sn);
        }
        $this->device->add_player_log($this->player->id, $this->config->item('event_type_login'), "Player[" . $this->player->name . "] login successfully, mac address[$mac], software version[$ver]");

        //获取客户端的 format_flag 标记
        $format_flag = $this->device->get_flag($this->sn, 'format_flag');
        if ($format_flag) {
            set_status_header(261, $this->sn);
            $this->device->restore_flag($this->sn, 'format_flag');
        }

        $this->load->model('membership');
        $company = $this->membership->get_company($this->player->company_id);
        if ($company && $company->offline_email_flag) {
            $this->load->library('smtp');
            $this->load->helper('serial');
            $data = array();
            $data['company'] = $company;
            $data['player'] = $this->player;
            $content = $this->load->view('email/login', $data, true);
            $this->send_mail($company->email, 'Player Login Report', $content);
            echo $content;
        }
    }

    private function send_mail($email_to, $subject, $message)
    {
        if ($email_to) {
            //mail($to, $subject, $content, $header);
            $message = preg_replace("/(^|(\r\n))(\.)/", "\1.\3", $message);
            $from_name = $this->config->item('email.from_name');
            $from_mail = $this->config->item('email.from_mail');
            $smtpserver = $this->config->item('email.smtp_server');
            $replyto = $this->config->item('email.reply_to');
            $password = $this->config->item('email.password');
            $serverport = $this->config->item('email.smtp_port');
            $smtp = new SMTP();
            $smtp->do_debug = 0;
            if ($smtp->Connect($smtpserver, $serverport)) {
                $smtp->Hello($from_mail);
                #$smtp->Authenticate("support@miatek.com","miajinan");
                $uid = md5(uniqid(time()));
                if ($smtp->Authenticate($from_mail, $password)) {
                    $smtp->Mail($from_mail);
                    $smtp->Recipient($email_to);
                    //$smtp->Recipient('gongenjian123@163.com');
                    $header = "From: " . $from_name . " <" . $from_mail . ">\r\n";
                    $header .= "To: " . $email_to . "\r\n";
                    $header .= "Subject: " . $subject . "\r\n";

                    $header .= "Reply-To: " . $replyto . "\r\n";
                    $header .= "MIME-Version: 1.0\r\n";

                    $header .= "Content-type: text/html; charset=UTF-8\r\n";
                    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                    $header .= $message . "\r\n\r\n";
                    $smtp->Data($header);
                }
                $smtp->Quit();
                $smtp->Close();
            }
        }
    }

    /**
     * 更新客户机状态信息
     * sn:
     * status:
     *  0	未知状态
        1	离线状态
        2	播放状态
        3	下载状态
        4	停止状态
        5	上线状态
        6	后台下载
        7	异常状态
        8	更新终端软件
        10	登出服务器，终端关机前的请求
        12  待机状态 Idle

     * voltage:终端当前电压值
     * elec:终端当前的电流值
     * fan:终端当前输出类型,如fan=x，默认fan=1
     *  0	VGA
        1	HDMI_50
        2   HDMI_60
     *
     * diskspace:终端硬盘空间,如diskspace=200M
     * wetless:终端湿度,如wetless=20
     * temp:终端温度,如temp=40
     *
     * @return
     */
    public function status()
    {
        /*
        $status  = $this->input->get('status');
        $voltage = $this->input->get('voltage');
        $elec    = $this->input->get('elec');
        $fan     = $this->input->get('fan'); //Alan update item for model
        $diskspace=$this->input->get('diskspace');
        $wetless  = $this->input->get('wetless');
        $temp     = $this->input->get('temp');
        $plsName  = $this->input->get('plsName');
        */
        $status  = $this->params['status'];
        $voltage = $this->params['voltage'];
        $elec    = $this->params['elec'];
        $fan     = $this->params['fan']; //Alan update item for model
        $diskspace = $this->params['diskspace'];
        $wetless  = $this->params['wetless'];
        $temp     = $this->params['temp'];
        $plsName  = $this->params['plsName'];

        $this->device->update_status($this->sn, $status, $voltage, $elec, $fan, $diskspace, $wetless, $temp);
        $this->lang->load('player');
        if (($status != $this->player->status || $this->timeout()) && $status != 6 && $status != 13) {
            //$this->device->add_player_log($this->player->id, $this->config->item('event_type_heartbeat'), "Player[".$this->player->name."] Status:".$this->lang->line('status.'.$status));
            if ($plsName == 'NULL') {
                $plsName = '';
            }
            if ($plsName != '' && $status == 2) {
                $pls = ' playlist [' . $plsName . ']';
            } else {
                $pls = '';
            }
            $this->device->add_player_log($this->player->id, $this->config->item('event_type_heartbeat'), "Player[" . $this->player->name . "] Status:" . $this->lang->line('status.' . $status) . $pls);
        }

        // $version = $this->player->upgrade_version;
        if ($this->player->upgrade_version) {
            if ($this->player->version < $this->player->upgrade_version) {
                set_status_header(501, $this->player->upgrade_version);
            } else {
                $this->device->remove_upgrade_version($this->sn);
            }
        }

        //获取客户端的 reboot_flag 标记---2013-10-10 liu
        $reboot_flag = $this->device->get_flag($this->sn, 'reboot_flag');
        if ($reboot_flag) {
            set_status_header(260, $this->sn);
            $this->device->restore_flag($this->sn, 'reboot_flag');
        }

        //获取客户端的 format_flag 标记---2013-10-10 liu
        $format_flag = $this->device->get_flag($this->sn, 'format_flag');
        if ($format_flag) {
            set_status_header(261, $this->sn);
            $this->device->restore_flag($this->sn, 'format_flag');
        }
        //检查是否有config更新
        $config_xml = $this->device->get_config_update($this->sn);
        if ($config_xml) {
            set_status_header(262, $this->sn);
        }
    }

    /**
     * Android版本有效   下载LastModify.xml
     *
     * sn：终端编号
     * hid：
     * enforceDownload=1
     * @return
     */
    public function status2()
    {
        $enforceDownload = $this->params['enforceDownload'];

        $weather_lastTime = 0;
        $schedule_lastTime = 0;
        $dedi_schedule_lastTime = 0;
        $touch_lastTime = 0;
        $timerStrategy_lastTime = 0;
        $Syspara_lastTime = 0;
        $Pwd = 0;
        $HeartBeatTime = 0;

        //获取weather.xml最后修改时间
        $this->load->model('membership');
        $this->load->helper('weather');
        $this->load->helper('date');
        $this->load->model('program');
        $company = $this->membership->get_company($this->player->company_id);

        if ($this->config->item('with_template')) {
            $schedule_lastTime = server_to_gmttimestamp($this->player->campaign_update_time);
        } else {

            $dst = $this->membership->is_dst_on($this->player->company_id);

            $today = now_to_local_date($this->player->time_zone, $dst);



            if ($dst) {
                $ptzone = $this->player->time_zone + 1;
            } else {
                $ptzone = $this->player->time_zone;
            }

            $dtformat = sprintf("%s %s%d", $today, $ptzone >= 0 ? "+" : "", $ptzone);
            $playerdate = new DateTime($dtformat);

            $todaytimestamp = $playerdate->getTimestamp();



            if ($company->publish_refresh) {
                $refresh_timestamp = server_to_gmttimestamp($company->publish_refresh);
                if ($refresh_timestamp > $todaytimestamp) {
                    $todaytimestamp = $refresh_timestamp;
                }
            }



            $campaigns = $this->program->get_published_campaign_by_player($this->player->id, $today, 9);
            $fillin_campaigns = null;
            $hour_campaigns = null;
            if ($campaigns) {
                $fillin_campaigns = array_filter($campaigns, function ($value) use ($today) {
                    if ($value->priority == 6 || $value->priority == 3) {
                        return true;
                    }
                    return false;
                });
                $hour_campaigns = array_filter($campaigns, function ($value) use ($today) {
                    if ($value->priority != 6 && $value->priority != 3 && $value->priority != 5) {
                        return true;
                    }
                    return false;
                });
            }


            //todo
            $schedule_lastTime = 0;


            if ($fillin_campaigns) {
                foreach ($fillin_campaigns as $campaign) {
                    $campaign_time = server_to_gmttimestamp($campaign->update_time);

                    if ($schedule_lastTime < $campaign_time) {
                        $schedule_lastTime = $campaign_time;
                    }
                }
                if ($schedule_lastTime < $todaytimestamp) {
                    $schedule_lastTime = $todaytimestamp;
                }
            } else {
                $schedule_lastTime = 0;
            }


            $dedi_schedule_lastTime = $schedule_lastTime;


            if ($hour_campaigns) {
                foreach ($hour_campaigns as $campaign) {
                    //chrome_log(sprintf("name=%s,curtime=%d,cam_time=%d",$campaign->name,$dedi_schedule_lastTime,strtotime($campaign->update_time)));
                    $campaign_time = server_to_gmttimestamp($campaign->update_time);
                    //chrome_log(sprintf("Instant campaign_time=%s, timestamp=%d",$campaign->update_time,$campaign_time));
                    if ($dedi_schedule_lastTime < $campaign_time) {
                        $dedi_schedule_lastTime = $campaign_time;
                    }
                    $player_update_time = server_to_gmttimestamp($this->player->add_time);
                    if ($dedi_schedule_lastTime < $player_update_time) {
                        $dedi_schedule_lastTime = $player_update_time;
                    }
                }
                //如果当前时间>=板子本地时间的的0点
                if ($dedi_schedule_lastTime < $todaytimestamp && server_to_gmttimestamp(date("Y-m-d H:i:s", time())) >= $todaytimestamp) {
                    $dedi_schedule_lastTime = $todaytimestamp;
                }
            } else {
                $dedi_schedule_lastTime = $todaytimestamp;
            }
        }

        //获取 time.cfg最后修改时间
        $this->load->model('device');
        if ($this->player->timer_config_flag == 1) {
            $timerStrategy_lastTime = server_to_gmttimestamp($this->player->timer_update);
        }

        //获取 config.xml最后修改时间
        if ($this->player->update_flag == 1) {
            $Syspara_lastTime = server_to_gmttimestamp($this->player->config_update_time);
        }

        $task = '<?xml version="1.0" encoding="UTF-8"?>' . $this->sep;
        $task .= '<LastModifyTimes>' . $this->sep;
        $task .= sprintf('<Weather>%s000</Weather>', $weather_lastTime) . $this->sep;
        $task .= sprintf('<Schedule>%s000</Schedule>', $schedule_lastTime) . $this->sep;
        $task .= sprintf('<Instant>%s000</Instant>', $dedi_schedule_lastTime) . $this->sep;
        $task .= sprintf('<Touch>%s000</Touch>', $touch_lastTime) . $this->sep;
        $task .= sprintf('<TimerStrategy>%s000</TimerStrategy>', $timerStrategy_lastTime) . $this->sep;
        $task .= sprintf('<Syspara>%s000</Syspara>', $Syspara_lastTime) . $this->sep;
        $task .= sprintf('<Pwd>%s</Pwd>', $Pwd) . $this->sep;
        $task .= sprintf('<HeartBeatTime>%s</HeartBeatTime>', $HeartBeatTime) . $this->sep;
        $task .= '</LastModifyTimes>' . $this->sep;


        $this->load->helper('file');
        $md5 = md5($task);
        $task .= sprintf('<!--%s-->', $md5);


        $headers = array();
        $headers[] = "PInterval: " . $this->player->comm_intval;
        $headers[] = "PVolume: " . $this->player->volume;

        return downloadContent($task, 'task.xml', $headers);
    }

    /**
     * 客户机心跳是否超时（5分钟）
     *
     * @return
     */
    private function timeout()
    {
        if ($this->player) {
            $log = $this->device->get_last_player_log($this->player->id, $this->config->item('event_type_heartbeat'));
            if ($log) {
                if ($log->add_time) {
                    if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/", $log->add_time, $matches)) {
                        $time = mktime(intval($matches[4]), intval($matches[5]), intval($matches[6]), intval($matches[2]), intval($matches[3]), intval($matches[1]));
                        if ((time() - $time) >= (5 * 60) - 10 || (time() - $time) < 0) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * 下载日程安排
     * sn
     * type:日程类型，如type=x，状态暂定1位数字
     *  0	播放列表日程Playlist.sch
        1	即时插播日程 Instant.sch
     *
     * @return
     */
    public function downloadSCH()
    {
        $this->load->helper('week');
        $this->load->helper('date');

        if (!isset($this->params['type'])) {
            set_status_header(400, 'request param error!');
            return;
        }
        $type = $this->params['type'];

        $this->load->helper('file');

        $this->load->model('program');
        $this->load->model('membership');
        $this->load->model('device');


        if ($this->config->item('with_template')) {
            $dst = $this->membership->is_dst_on($this->player->company_id);

            $today = server_to_local(date('Y-m-d H:i:s', time()), $this->player->time_zone, $dst, TRUE);


            if ($type == 0) {

                //Download Playlist.SCH
                $playlists = $this->program->get_published_playlist_by_player($this->player->id, $today, 2);


                if (!$playlists) {
                    $this->downloadEmptySch();
                    return;
                } else {
                    //$playlists = $this->program->get_published_playlist_by_player($this->player->id, $today);

                    $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Downloading schedule file.");
                    if ($this->downloadPlaylistNew($playlists)) {
                        $this->device->update_player(array('last_update' => date('Y-m-d H:i:s')), $this->player->id);
                    }
                    return;
                }
            } else {

                set_status_header(404, 'type[' . $type . '] NOT FOUND');
                return;
            }
        } else {
            $sch = '<?xml version="1.0" encoding="UTF-8"?>' . $this->sep;
            $sch .= '<Schedule>' . $this->sep;

            if ($this->player->screen_oritation) {

                $template_width = $this->player->mpeg_core == 4 ? 3840 : 1920;
                $template_height = $this->player->mpeg_core == 4 ? 2160 : 1080;

                $template_name = "landscape";
                $sense = 1;
            } else {
                $template_width = $this->player->mpeg_core == 4 ? 2160 : 1080;
                $template_height = $this->player->mpeg_core == 4 ? 3840 : 1920;
                $template_name = "portraint";
                $sense = 2;
            }

            $dst = $this->membership->is_dst_on($this->player->company_id);

            $today = strtotime(server_to_local(date('Y-m-d H:i:s', time()), $this->player->time_zone, $dst));
            //$today = now_to_local_date($this->player->time_zone,$dst);

            $today = date("Y-m-d", $today);


            //fill-in 列表
            if ($type == 0) {
                //生成播放
                $campaigns = $this->program->get_published_campaign_by_player($this->player->id, $today, 3);
                //$campigns = null;

                if (!$campaigns || empty($campaigns)) {
                    $this->downloadEmptySch('PlayList.SCH');
                    return;
                }

                $fillin_campaigns = array_filter($campaigns, function ($value) use ($today) {
                    if ($value->priority == 6) {
                        return true;
                    }
                    return false;
                });


                if (!$fillin_campaigns || empty($fillin_campaigns)) {
                    $fillin_campaigns = array_filter($campaigns, function ($value) use ($today) {
                        if ($value->priority == 3) {
                            return true;
                        }
                        return false;
                    });
                }



                $xml = '<?xml version="1.0" encoding="UTF-8"?>' . chr(10);
                $xml .= '<SignwayPoster type="playlist" version="1.0.1">' . $this->sep;
                $xml .= sprintf('<Templatename>%s</Templatename>', $template_name) . $this->sep;
                $xml .= sprintf('<ScreenType height="%d" rotation="0" width="%d"/>', $template_height, $template_width) . $this->sep;

                //$sch .= sprintf('<Priority>%d</Priority>',$campaign->priority).$this->sep;
                $medias = array();
                $published_time = 0;
                foreach ($fillin_campaigns as $campaign) {
                    $campaign_medias = $this->program->get_playlist_area_media_list_noexclude($campaign->id, $this->config->item('area_video'));

                    if ($campaign_medias) {
                        $medias = array_merge_recursive($medias, $campaign_medias['data']);
                    }
                    if ($published_time < $campaign->update_time) {
                        $published_time = $campaign->update_time;
                    }
                }

                $fid = sprintf("%d_FILL", $this->player->id);

                $xml .= sprintf('<Programme id="%s" name="%s">', $fid, $fid) . $this->sep;

                $xml .= '<Area id="101" name="Movie/Photo" model="0" left="0.00%" top="0.00%" width="100.00%" height="100.00%" zindex="10" />' . $this->sep;
                $xml .= '<AreaList id="101" playtime="00:00:30">' . $this->sep;
                $transmodemapping = $this->config->item('media.transmode.mapping');


                foreach ($medias as $media) {
                    //FIXME VIDEO ZONE

                    if ($media->play_time > 59) {
                        $times = sprintf("%02d:%02d", ($media->play_time / 60), ($media->play_time % 60));
                    } else {
                        $times = sprintf("00:%02d", $media->play_time);
                    }
                    $media_end_date = $media->date_flag ? ($media->pls_end_date < $media->end_date ? $media->pls_end_date : $media->end_date) : $media->pls_end_date;
                    $xml .= sprintf(
                        '<Resource id="%d" name="%s" fid="%d" size="%d" signature="%s"
								sw110Signature="%s" transmode="%d" startdate="%s" enddate="%s" duration="00:%s"
								transittime="0.5" mode="%d" reload="%d"  replacable="1">',
                        $media->id,
                        //htmlspecialchars($media->name, ENT_XML1, 'UTF-8'),
                        $this->safInputs($media->name),
                        $media->id,
                        $media->file_size,
                        $media->signature,
                        $media->signature,
                        $media->transmode >= 0 ? $transmodemapping[$media->transmode] : 0,
                        $media->date_flag ? $media->start_date : $media->pls_start_date,
                        $media_end_date, //$media->date_flag ? $media->end_date : "",
                        $times,
                        0,
                        $media->reload
                    ) . $this->sep;
                    $xml .= sprintf('<URL>%s</URL>', $media->full_path) . $this->sep;
                    $xml .= '</Resource>' . $this->sep;
                }

                $xml .= '</AreaList>' . $this->sep;
                $xml .= '</Programme>' . $this->sep;
                $xml .= '</SignwayPoster>' . chr(10);
                $md5 = md5($xml);

                $cachedPath = $this->config->item('cached_temp_path');
                if (!file_exists($cachedPath)) {
                    mkdir($cachedPath, 0777, true);
                }
                $playlist_path =  $cachedPath . $fid . '.PLS';
                saveFile($playlist_path, $xml);


                $sch .= sprintf('<Programme name="%s.PLS" id="%s" fid="%s" type="0" sense="%d" model="3">', $fid, $fid, $fid, $sense) . $this->sep;
                $sch .= sprintf('<PublishTime>%s</PublishTime>', $published_time) . $this->sep;
                $sch .= sprintf('<action>0</action>') . $this->sep;
                $sch .= sprintf('<URL>resources/cached/%s.PLS</URL>', $fid) . $this->sep;


                $sch .= sprintf('<Size>%d</Size>', filesize($playlist_path)) . $this->sep;

                $sch .= sprintf('<Signature>%s</Signature>', $md5) . $this->sep;
                $sch .= '</Programme>' . $this->sep;

                $sch1 = $sch . '</Schedule>';
                $md51 = md5($sch1);
                $sch = $sch . '</Schedule><!--' . $md51 . '-->' . $this->sep;


                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloading Fill-in schedule file.");
                return downloadContent($sch, 'Playlist.sch');
            } elseif ($type == 1) {
                //FIXME
                $this->load->library("TimeSlot");

                $this->program->fill_player_details($this->player);

                $timeslots = $this->program->do_get_today_timeslots($this->player, $today);


                if (!$timeslots) {
                    //set_status_header(404, 'Schudule file not found!');
                    $this->downloadEmptySch('Instant.SCH');
                    return;
                }


                foreach ($timeslots as $slot) {
                    //FIXEME 可能需要知道player的方向
                    //Create today's playlist for this slot;
                    $data = $this->create_playlist_by_timeslot($slot, $today);

                    //chrome_log($data);
                    if ($data) {
                        $fid = sprintf("%d_%02d_%02d", $this->player->id, $slot->startH, $slot->startM);

                        $sch .= sprintf('<Programme name="%02d%02d.PLS" id="%s" fid="%s" type="0" sense="%d" model="3">', $slot->startH, $slot->startM, $fid, $fid, $sense) . $this->sep;
                        $sch .= sprintf('<PublishTime>%s</PublishTime>', strtotime($today)) . $this->sep;
                        $sch .= sprintf('<action>0</action>') . $this->sep;
                        $sch .= sprintf('<URL>resources/cached/%d_%02d_%02d.PLS</URL>', $this->player->id, $slot->startH, $slot->startM) . $this->sep;


                        $sch .= '<Dates>' . $this->sep;
                        $sch .= '<Date>' . $this->sep;


                        // $startdate = date("Y-m-d", $today);

                        $sch .= sprintf('<StartDate>%s</StartDate>', $today) . $this->sep;

                        $sch .= sprintf('<EndDate>%s</EndDate>', date("Y-m-d", strtotime($today . " +1 months"))) . $this->sep;

                        $sch .= sprintf('<StartTime>%02d:%02d</StartTime>', $slot->startH, $slot->startM) . $this->sep;
                        $sch .= sprintf('<EndTime>%02d:%02d</EndTime>', $slot->stopH, $slot->stopM) . $this->sep;
                        $sch .= '</Date>' . $this->sep;
                        $sch .= '</Dates>' . $this->sep;

                        $sch .= sprintf('<Size>%d</Size>', $data['size']) . $this->sep;
                        $sch .= sprintf('<Signature>%s</Signature>', $data['md5']) . $this->sep;
                        $sch .= '</Programme>' . $this->sep;
                    }
                }
                $sch1 = $sch . '</Schedule>';
                $md51 = md5($sch1);
                $sch = $sch . '</Schedule><!--' . $md51 . '-->' . $this->sep;
                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloading new playlist/schedule file.");


                return np_download(NULL, $sch, 'Instant.SCH');
            } else {
                set_status_header(404, 'Schudule file not found!');
            }
        }
    }

    /**
     * NP100 配置文件下载，下载成功返回TRUE 否则返回FALSE
     *
     * @param object $schedules
     * @return
     */
    public function downloadCfg()
    {
        //$sn = $this->input->get('sn');
        $sn = $this->sn;
        $con = $this->device->get_config_setting_by_sn($sn);
        $this->load->model('membership');
        $company = $this->membership->get_company($this->player->company_id);
        $device_setup = $company->device_setup;
        $config_name = '';
        $cf = '<?xml version="1.0" encoding="UTF-8"?>' . $this->sep;
        $cf .= '<config>' . $this->sep;
        if ($con) {

            $config_name = $con->name;
            $cf .= sprintf('<dateformat>%d</dateformat>', $con->dateformat) . $this->sep;
            $cf .= sprintf('<timeformat>%d</timeformat>', $con->timeformat) . $this->sep;
            $cf .= '<timezone>' . $con->timezone . '</timezone>' . $this->sep;
            $cf .= sprintf('<synctime>%d</synctime>', $con->synctime) . $this->sep;
            $cf .= sprintf('<clockpos>%d</clockpos>', $con->clockpos) . $this->sep;
            if ($con->storagepri > -1) {
                $cf .= sprintf('<storagepri>%d</storagepri>', $con->storagepri) . $this->sep;
            }
            if ($con->dailyRestartFlag == 2) {
                $con->dailyRestartTime = '-1:00';
            }
            $cf .= sprintf('<reboottime>%s</reboottime>', $con->dailyRestartTime) . $this->sep;
            if ($con->videomode > -1) {
                $cf .= sprintf('<videomode>%s</videomode>', $con->videomode) . $this->sep;
            }
            if ($device_setup == 'on' || $device_setup == '1') {
                if ($con->port != 0) {
                    $cf .= sprintf('<serverPort>%s</serverPort>', $con->port) . $this->sep;
                }

                if ($con->sn != '') {
                    $player_sn = '';
                    $sn_segments = explode('-', $con->sn);
                    $player_sn = $sn_segments[0] . $sn_segments[1] . $sn_segments[2];
                    $cf .= sprintf('<sn>%s</sn>', $player_sn) . $this->sep;
                }
                //$cf .= sprintf('<sn>%s</sn>', $player_sn).$this-ep;
                if ($con->domain != '' || $con->ip != '') {
                    if ($con->connectionMode == 1) {
                        $cf .= sprintf('<domain>%s</domain>', $con->domain) . $this->sep;
                        $cf .= sprintf('<ConnectMode>1</ConnectMode>') . $this->sep;
                    } else {
                        $cf .= sprintf('<serverIP>%s</serverIP>', $con->ip) . $this->sep;
                        $cf .= sprintf('<ConnectMode>0</ConnectMode>') . $this->sep;
                    }
                }
            }
        }
        $cf .= '</config>' . $this->sep;

        $this->load->helper('file');

        $md5 = md5($cf);
        $cachedPath = $this->config->item('cached_temp_path');
        if (!file_exists($cachedPath)) {
            mkdir($cachedPath, 0777, true);
        }
        $cachedFile =  $cachedPath . $md5;
        if (!@file_exists($cachedFile)) {
            saveFile($cachedFile, $cf);
        }
        //return downloadNP200File($cachedFile, 'config.xml');
        if (downloadNP200File($cachedFile, 'config.xml')) {
            $this->device->re_config_xml($sn);
            $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloads config.xml [" . $config_name . "]");
        }
        //$this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[".$this->player->name."] downloads  [config.xml].");
    }

    /**
     * NP200 配置文件下载，下载成功返回TRUE 否则返回FALSE
     *
     * @return
     */
    public function downloadSysParaConfig()
    {
        //$sn = $this->input->get('sn');
        $sn = $this->sn;
        $con = $this->device->get_config_setting_by_sn($sn);
        $this->load->model('membership');
        $company = $this->membership->get_company($this->player->company_id);
        $device_setup = $company->device_setup;


        $config_name = '';
        if ($con) {

            $cfgArray = array();
            $cfgArray['timezone'] = $con->timezone;
            $cfgArray['synctime'] = $con->synctime;
            $cfgArray['clockpos'] = $con->clockpos;

            if ($con->storagepri > -1) {
                $storage = 3;
                if ($con->storagepri == 1) {
                    $storage = 3;
                } elseif ($con->storagepri == 2) {
                    $storage = 1;
                } elseif ($con->storagepri == 3) {
                    $storage = 2;
                }
                $cfgArray['storagepri'] = $storage;
            }
            $cfgArray['syncplayback'] = $con->sync_playback;
            if ($con->dailyRestartFlag == 2) {
                $con->dailyRestartTime = '-1:00';
            }
            $cfgArray['reboottime'] = $con->dailyRestartTime;
            if ($con->videomode > -1) {
                $cfgArray['videomode'] = $con->videomode;
            }
            if ($con->orientation > 0) {
                $orientation = array('1' => 0, '2' => 90, '3' => 180, '4' => 270);
                $cfgArray['orientation'] = $orientation[$con->orientation];
            }

            if ($device_setup == 'on' || $device_setup == '1') {
                if ($con->port != 0) {
                    $cfgArray['serverPort'] = $con->port;
                }

                if ($con->sn != '') {
                    $player_sn = '';
                    $sn_segments = explode('-', $con->sn);
                    $player_sn = $sn_segments[0] . $sn_segments[1] . $sn_segments[2];
                    $cfgArray['sn'] = $player_sn;
                }

                if ($con->domain != '' || $con->ip != '') {
                    if ($con->connectionMode == 1) {
                        $cfgArray['domain'] = $con->domain;
                        $cfgArray['ConnectMode'] = 1;
                    } else {
                        $cfgArray['serverIP'] = $con->ip;
                        $cfgArray['ConnectMode'] = 0;
                    }
                }

                if (isset($con->isHttps)) {
                    $cfgArray['HttpsSwitch'] = $con->isHttps;
                }

                if ($con->tcpport > 0) {
                    $cfgArray['gtvTcpPort'] = $con->tcpport;
                }
                $cfgArray['playbackLog'] = $con->playback_flag;

                if ($con->networkmode > 0 && $con->networkmode <= 3) {
                    $cfgArray['networkMode'] = $con->networkmode;
                }


                if ($con->networkmode == '2') {
                    if ($con->wifissid != '') {
                        $cfg['ssidname'] = $con->wifissid;
                        if ($con->wifipwd != '') {
                            $cfgArray['wifipwd'] = $con->wifipwd;
                        }
                    }
                } else if ($con->networkmode == '3') {
                    if (isset($con->hotssid) && $con->hotssid != '') {
                        $cfgArray['HotspotSsid'] = $con->hotssid;
                        if ($con->hotssid != '') {
                            $cfgArray['HotspotPassword'] = $con->hotpwd;
                        }
                    }
                }
                if ($con->menulock > 0) {
                    $cfgArray['menu'] = $con->menulock;

                    if ($con->lockpwd > 0) {
                        $cfgArray['menuPassword'] = $con->lockpwd;
                    }
                }

                if (isset($con->brightness) && $con->brightness > 0 && $con->brightness <= 255) {
                    $cfgArray['brightness'] = $con->brightness;
                    $cfgArray['brightnessType'] = 'pwm';
                    $cfgArray['brightnessOrder'] = 1;
                }

                if (isset($con->ethernetTethering)) {
                    $cfgArray['EthernetTetheringOnOff'] = $con->ethernetTethering;
                }
            }
            $arrayToXml = new ArrayToXml($cfgArray, '', false, "UTF-8");


            $result = $arrayToXml->prettify()->toXml();
            $this->load->helper('file');

            if (np_download(NULL, $result, 'config.xml')) {
                $this->device->delete_config_player($sn);
                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloads config.xml [" . $con->name . "]");
            }

            /*
                        $cf = '<?xml version="1.0" encoding="UTF-8"?>' . $this->sep;
            $cf .= '<config>' . $this->sep;
            $config_name = $con->name;
            $cf .= '<timezone>' . $con->timezone . '</timezone>' . $this->sep;
            $cf .= sprintf('<synctime>%d</synctime>', $con->synctime) . $this->sep;
            $cf .= sprintf('<clockpos>%d</clockpos>', $con->clockpos) . $this->sep;

            //$cf .= sprintf('<storagepri>%d</storagepri>', $con->storagepri).$this->sep;

            if ($con->storagepri > -1) {
                $storage = 3;
                if ($con->storagepri == 1) {
                    $storage = 3;
                } elseif ($con->storagepri == 2) {
                    $storage = 1;
                } elseif ($con->storagepri == 3) {
                    $storage = 2;
                }
                $cf .= sprintf('<storagepri>%d</storagepri>', $storage) . $this->sep;
            }
            $cf .= sprintf('<syncplayback>%d</syncplayback>', $con->sync_playback) . $this->sep;
            if ($con->dailyRestartFlag == 2) {
                $con->dailyRestartTime = '-1:00';
            }
            $cf .= sprintf('<reboottime>%s</reboottime>', $con->dailyRestartTime) . $this->sep;
            if ($con->videomode > 0) {
                $cf .= sprintf('<videomode>%s</videomode>', $con->videomode) . $this->sep;
            }
            if ($con->orientation > 0) {
                $orientation = array('1' => 0, '2' => 90, '3' => 180, '4' => 270);
                $cf .= sprintf('<orientation>%s</orientation>', $orientation[$con->orientation]) . $this->sep;
            }
            if ($device_setup == 'on' || $device_setup == '1') {
                if ($con->port != 0) {
                    $cf .= sprintf('<serverPort>%s</serverPort>', $con->port) . $this->sep;
                }

                if ($con->sn != '') {
                    $player_sn = '';
                    $sn_segments = explode('-', $con->sn);
                    $player_sn = $sn_segments[0] . $sn_segments[1] . $sn_segments[2];
                    $cf .= sprintf('<sn>%s</sn>', $player_sn) . $this->sep;
                }

                if ($con->domain != '' || $con->ip != '') {
                    if ($con->connectionMode == 1) {
                        $cf .= sprintf('<domain>%s</domain>', $con->domain) . $this->sep;
                        $cf .= sprintf('<ConnectMode>1</ConnectMode>') . $this->sep;
                    } else {
                        $cf .= sprintf('<serverIP>%s</serverIP>', $con->ip) . $this->sep;
                        $cf .= sprintf('<ConnectMode>0</ConnectMode>') . $this->sep;
                    }
                }

                if ($con->tcpport > 0) {
                    $cf .= sprintf("<gtvTcpPort>%d</gtvTcpPort>", $con->tcpport) . $this->sep;
                }
                $cf .= sprintf("<playbackLog>%d</playbackLog>", $con->playback_flag) . $this->sep;


                if ($con->networkmode > 0 && $con->networkmode <= 3) {
                    $cf .= sprintf("<networkMode>%d</networkMode>", $con->networkmode) . $this->sep;
                }


                if ($con->networkmode == '2') {
                    if ($con->wifissid != '') {
                        $cf .= sprintf("<ssidname>%s</ssidname>", $con->wifissid) . $this->sep;
                        if ($con->wifipwd != '') {
                            $cf .= sprintf("<wifipwd>%s</wifipwd>", $con->wifipwd) . $this->sep;
                        }
                    }
                }

                if ($con->networkmode == '3') {
                    if (isset($con->hotssid) && $con->hotssid != '') {
                        $cf .= sprintf("<HotspotSsid>%s</HotspotSsid>", $con->hotssid) . $this->sep;
                        if ($con->hotssid != '') {
                            $cf .= sprintf("<HotspotPassword>%s</HotspotPassword>", $con->hotpwd) . $this->sep;
                        }
                    }
                }
                if ($con->menulock > 0) {
                    $cf .= sprintf("<menu>%d</menu>", $con->menulock) . $this->sep;
                    if ($con->lockpwd > 0) {
                        $cf .= sprintf("<menuPassword>%d</menuPassword>", $con->lockpwd) . $this->sep;
                    }
                }

                if (isset($con->brightness) && $con->brightness > 0 && $con->brightness <= 255) {
                    $cf .= sprintf("<brightness>%d</brightness>", $con->brightness) . $this->sep;
                    $cf .= "<brightnessType>pwm</brightnessType>" . $this->sep;
                    $cf .= "<brightnessOrder>1</brightnessOrder>" . $this->sep;
                }

                if (isset($con->ethernetTethering)) {
                    $cf .= sprintf("<EthernetTetheringOnOff>%d</EthernetTetheringOnOff>", $con->ethernetTethering) . $this->sep;
                }
            }
            $cf .= '</config>';

            $this->load->helper('file');

            downloadContent($cf, 'config.xml');
            $this->device->delete_config_player($sn);
            $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloads config.xml [" . $config_name . "]");
            return;

            */
        } else {
            set_status_header(404, 'NO Config');
        }
    }


    /**
     * Android  下载空的日程安排，下载成功返回TRUE 否则返回FALSE
     *
     * @param object $schedules
     * @return
     */
    private function downloadEmptySch($filename = "PlayList.sch")
    {
        $sch = '<?xml version="1.0" encoding="UTF-8"?>' . $this->sep;
        $sch .= '<Schedule>' . $this->sep;


        //$sch .= '</Schedule>'.$this->sep;
        $sch1 = $sch . '</Schedule>';
        $md51 = md5($sch1);
        $sch = $sch . '</Schedule><!--' . $md51 . '-->' . $this->sep;
        return np_download(NULL, $sch, $filename);
        //return downloadNP200File($cachedFile, $filename);
    }


    /**
     * 下载 互动应用日程
     */
    private function downloadInteraction($schedules)
    {
        $this->load->helper('week');
        $this->load->helper('date');
        $sch = '<?xml version="1.0" encoding="UTF-8"?>' . $this->sep;
        $sch .= '<Schedule>' . $this->sep;

        $this->load->model('membership');
        $dst = $this->membership->is_dst_on($this->player->company_id);
        $server_time = date('Y-m-d H:i:s');
        $local_time = server_to_local($server_time, $this->player->time_zone, $dst);
        if (!$local_time) {
            $local_time = $server_time;
        }

        foreach ($schedules as $schedule) {
            if ($schedule->end_date . ' 23:59:59' >= $local_time) {
                if ($schedule->interactions) {
                    foreach ($schedule->interactions as $pl) {
                        //判断横屏还是竖屏
                        $this->load->model('program');
                        $template = $this->program->get_interaction($pl->interaction_id);
                        if ($template->w > $template->h) {
                            $sense = 1;
                        } else {
                            $sense = 2;
                        }
                        $fid = $this->program->add_cat_fid();
                        $sch .= sprintf('<Programme name="%d.PLS" id="%d" fid="%d%d" type="6" sense="%d" model="3">', $pl->id, $pl->id, $pl->id, $fid, $sense) . $this->sep;
                        $sch .= sprintf('<PublishTime>%s</PublishTime>', $schedule->publish_time) . $this->sep;

                        $sch .= '<Dates/>' . $this->sep;
                        $sch .= '<Weeks/>' . $this->sep;
                        $publish_path = $this->config->item('playlist_publish_path');
                        $publish_path .= $schedule->company_id . '/touch' . $pl->id . '.PLS';
                        $sch .= sprintf('<Size>%d</Size>', filesize($publish_path)) . $this->sep;
                        $sch .= sprintf('<Signature>%s</Signature>', md5_file($publish_path)) . $this->sep;
                        $sch .= '</Programme>' . $this->sep;
                    }
                }
            }
        }
        //$sch .= '</Schedule>'.$this->sep;
        $sch1 = $sch . '</Schedule>';
        $md51 = md5($sch1);
        $sch = $sch . '</Schedule><!--' . $md51 . '-->' . $this->sep;
        $this->load->helper('file');

        $md5 = md5($sch);
        $cachedPath = $this->config->item('cached_temp_path');
        if (!file_exists($cachedPath)) {
            mkdir($cachedPath, 0777, true);
        }
        //save temp sch file...
        $cachedFile =  $cachedPath . $md5;
        if (!@file_exists($cachedFile)) {
            saveFile($cachedFile, $sch);
        }
        return downloadNP200File($cachedFile, 'Touch.sch');
    }



    /**
     * 下载播放列表
     * sn
     * url:sch中指定的URL
     *
     * @return
     */
    public function downloadPLS()
    {
        $array = array();
        $array_touch = array();

        if (!isset($this->params['fid'])) {
            set_status_header(400, 'request param error!');
            return;
        }

        $fid = $this->params['fid'];

        if ($this->config->item("with_template")) {
            $this->load->model('program');
            $playlist = $this->program->get_playlist($fid);
            if ($playlist) {
                $publish_path = $this->config->item('playlist_publish_path');
                $publish_path .= $playlist->company_id . '/' . $fid . '.PLS';

                if (!file_exists($publish_path)) {
                    set_status_header(404);
                    return;
                }

                $this->load->helper('file');
                if (downloadNP200File($publish_path)) {
                    $this->device->update_player(array('last_update' => date('Y-m-d H:i:s')), $this->player->id);
                    $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloads playlist file [" . $playlist->name . ".pls].");
                    return;
                }
            }
        } else {

            $cachedPath = $this->config->item('cached_temp_path');
            $playlist_path =  $cachedPath . $fid . '.PLS';

            if (file_exists($playlist_path)) {
                //FIXME
                $this->load->helper('file');
                np_download($playlist_path, NULL, $fid . '.PLS');
                return;
            } else {
                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] can not find playlist:" . $playlist_path);
            }
        }
        set_status_header(404, 'FID[' . $fid . '] NOT FOUND');
    }


    /**
     * 下载媒体文件
     * sn
     * fid:媒体源文件的id(来自PLS文件resource的fid)_日程id(来自SCH文件)，fid=xxx_xx
     * @return
     */
    public function downloadMMF()
    {

        //$fid = $this->input->get('fid');
        if (!isset($this->params['fid'])) {
            set_status_header(400, 'request param error!');
            exit();
        }

        $fid = $this->params['fid'];

        $areaId = 0;
        $pos = strpos($fid, '_');
        $split = count(preg_split('/_/', $fid));
        if ($pos) {
            $mid = substr($fid, 0, $pos);
            $areaId = substr($fid, $pos + 1);
            $fid = $mid;
        }

        $this->load->model('material');
        $this->load->helper('file');

        if ($split == 3) {
            $media = $this->material->get_interaction_area_media($fid);
        } elseif ($split == 4) {
            $media = $this->material->get_media($fid);
            $media->publish_url = $media->full_path;
        } else {
            if ($split == 2 && $fid == "bg") {
                $media = $this->material->get_media($areaId);
            } else {
                $media = $this->material->get_area_media($fid);
            }
        }


        if ($media) {
            if ($media->source == $this->config->item('media_source_local')) {
                //$this->load->helper('file');
                $this->device->update_status($this->sn, 3); //Set download status
                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloads media file [" . $media->name . "].");
                //如果是发布成功状态，则直接返回，否则从原始文件中发布下载
                if (!empty($media->publish_url)) {

                    if (downloadNP200File($media->publish_url)) {
                        return;
                    }
                } elseif (!empty($media->full_path)) {

                    if (downloadNP200File($media->full_path)) {
                        return;
                    }
                } elseif ($this->downloadLocalFile($media, $areaId)) {
                    return;
                }
            } elseif ($media->source == $this->config->item('media_source_ftp')) {
                $this->device->update_status($this->sn, 3); //Set download status
                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloads FTP media file [" . $media->name . "].");
                set_status_header(302);
                header("Location: " . $media->full_path);
                return;

                //set_status_header(404, 'media['.$fid.'] is not local!');
                //return;
            } elseif ($media->source == $this->config->item('media_source_http')) {
                $this->device->update_status($this->sn, 3); //Set download status
                if ($media->media_type == $this->config->item('media_type_video')) {
                    set_status_header(302);
                    header("Location: " . $media->full_path);
                    return;
                } else {
                    if ($this->downloadRemoteImage($media, $areaId)) {
                        return;
                    }
                }
            }
        }
        $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] fails to download Media ID[" . $fid . "]. Not found!");
        set_status_header(404, 'media[' . $fid . '] Not found!');
    }

    /**
     * 下载本地文件
     *
     * @param object $media
     * @param object $areaId
     * @return
     */
    private function downloadLocalFile($media, $areaId)
    {
        //Load area info
        $this->load->model('program');
        $this->load->helper('media');
        $area = $this->program->get_area($areaId);

        if ($area === false) {
            return false;
        }

        //Load Template info
        $template = $this->program->get_template($area->template_id);

        if ($template === false) {
            return false;
        }
        $media_url = generate_client_area_media($media, $template, $area);
        if ($media_url) {
            return downloadNP200File($media_url);
        }

        return false;
    }


    /**
     * 下载媒体文件
     *
     * @param object $media
     * @param object $areaId
     * @return
     */
    private function downloadRemoteImage($media, $areaId)
    {
        //Load area info
        $this->load->model('program');
        $area = $this->program->get_area($areaId);
        if ($area === false) {
            return false;
        }

        //Load Template info
        $template = $this->program->get_template($area->template_id);
        if ($template === false) {
            return false;
        }

        $width  = intval(($area->w * $template->width) / $template->w);
        $height = intval(($area->h * $template->height) / $template->h);
        //修正，保持宽度和高度为4的倍数

        //Download Image for YUV
        $yuv_path = $this->config->item('playlist_publish_path') . $media->company_id . '/yuv/';
        if (!file_exists($yuv_path)) {
            mkdir($yuv_path, 0777, true);
        }

        $this->load->helper('file');
        $absPath = downloadRemoteFile($media->full_path, $yuv_path);
        if ($absPath) {

            //YUV转化库
            if (!isset($_SERVER['WINDIR'])) {
                if (@dl("jpeg2yuv.so") == false) {
                    return false;
                }
            }

            //resize image
            $size = @getimagesize($absPath);
            $dest = $absPath . '.' . $width . '.' . $height;
            $destYuv = $dest . '.yuv';

            //已经存在则下载
            if (file_exists($destYuv)) {
                return downloadNP200File($destYuv);
            }

            if ($width == $size[0] && $height == $size[1]) {
                //copy
                @copy($absPath, $dest);
            } else {
                //resize
                $thumb = @imagecreatetruecolor($width, $height);
                //load
                $source = @imagecreatefromjpeg($absPath);
                //resize
                @imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);

                @imagejpeg($thumb, $dest, 100);
            }

            //convert YUV
            if (jpeg2yuv411($dest, $destYuv, $width, $height) != 0) {
                return downloadNP200File($destYuv);
            }
        }

        return false;
    }

    /**
     * 下载系统文件
     * sn
     * name:系统文件名称，name=xxx..x.xxx，
     * 	例如：请求ST1_GB2312_32H1.FNT字库文件
     *
     * @return
     */
    public function downloadSYS()
    {
        //$name = $this->input->get('name');
        if (!isset($this->params['name'])) {
            set_status_header(400, 'request param error!');
            return;
        }
        $name = $this->params['name'];


        $this->load->helper('file');
        $full_path = $this->config->item('system_media_path') . $name;
        if (downloadNP200File($media->full_path)) {
            $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloads system file [" . $name . "]");
            return;
        }
        $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] fails to download system file [" . $name . "], Not found!");
        set_status_header(404, 'media[' . $name . '] Not found!');
    }

    /**
     * 下载系统软件信息
     * sn
     * ver:终端当前软件版本，ver=x.x.x，数字版本号，主版本号+次版本号+补丁号，用‘.’分隔
     * 	当前版本提供给服务器与服务器存储的系统软件进行版本比较
     * 	如果当前版本比服务器版本旧则返回文件进行升级，否则不予返回数据
     * @return
     */
    public function downloadSysSoft()
    {
        //$ver = $this->input->get('ver');
        if (!isset($this->params['ver'])) {
            set_status_header(400, 'request param error!');
            return;
        }
        $ver = $this->params['ver'];
        if ($ver === false) {
            set_status_header(400, 'request param error!');
            return;
        }
        $this->load->model('material');
        $this->load->model('device');

        if (isset($this->params['restart'])) {
            $restart = $this->params['restart'];
            $str = substr($restart, 0, 2) . ":" . substr($restart, 2);
            $this->device->update_player(array('daily_restart' => $str), $this->player->id);
        }


        $version = $this->device->get_upgrade_version($this->sn);


        if ($version && $ver != $version) {
            $soft = $this->material->get_version_software($version);
            if ($soft) {
                $this->load->helper('file');
                $path = $this->config->item('system_media_path');
                $this->device->update_status($this->sn, 3); //Set download status
                $this->device->remove_upgrade_version($this->sn);



                np_download($path . $soft->location, NULL, $soft->name);
                //if (downloadNP200File($path . $soft->location, $soft->name)) {
                //        $this->db->initialize();
                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloads software version[" . $version . "]");
                return;
            } else {
                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] fails to download software version[$version]. Not found!");
            }
        }

        if ($version) {
            // $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] has the latest software version[$ver] vs. [$version]");
        }

        set_status_header(404, 'target software [' . $version . '], current version [' . $ver . ']!');
    }

    /**
     * 下载动态数据，目前仅仅支持天气预报
     *
     * sn
     * dynamicType：动态xml类型， 0, 天气； 1，存款利率; 2， 贷款利率 ；3,汇率;4…
     * rotation：旋转角度
     *
     * 请求天气xml数据
     * sn=0A500001&dynamicType=0
     *
     * @return
     */
    public function downloadDynamicData()
    {
        $dynamicType = $this->params['dynamicType'];
        if ($dynamicType === false) {
            set_status_header(400, 'request param error!');
            return;
        }

        if ($dynamicType == 0) {
            $this->load->model('membership');
            $this->load->helper('weather');
            $this->load->helper('date');
            $company = $this->membership->get_company($this->player->company_id);
            //根据板子不同，下载不同的Weather.xml
            if ($this->player->player_type == 1) {
                $weather = get_yahoo_weather_3days($this->player->city_code, $company->weather_format);
                if ($weather) {
                    $city = $weather['city'];
                    $data = $weather['data'];
                    $xml = '<?xml version="1.0" encoding="utf-8"?>' . $this->sep;
                    $lang_id = $this->device->get_weather_lang_by_sn($this->sn);
                    if (!empty($lang_id) && $lang_id[0]->language == 1) {
                        $xml .= '<Weather>' . $this->sep;
                        $str1 = get_weather_lang(1, $data[0]['iconNum']);
                        $xml .= chr(9) . sprintf('<title>%s</title>', $str1) . $this->sep;
                        $xml .= chr(9) . sprintf('<City>%s</City>', $city['city']) . $this->sep;
                        $xml .= chr(9) . sprintf('<Content>%s~%s</Content>', $data[0]['low'], $data[0]['high']) . $this->sep;
                        $xml .= chr(9) . sprintf('<Pic>%s</Pic>', $data[0]['icon']) . $this->sep;
                        $xml .= chr(9) . sprintf('<Date>%s</Date>', $data[0]['date']) . $this->sep;
                        $xml .= chr(9) . '<NextOneDay>' . $this->sep;
                        $str2 = get_weather_lang(1, $data[1]['iconNum']);
                        $xml .= chr(9) . chr(9) . sprintf('<title>%s</title>', $str2) . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<City>%s</City>', $city['city']) . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<Content>%s~%s</Content>', $data[1]['low'], $data[1]['high']) . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<Pic>%s</Pic>', $data[1]['icon']) . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<Date>%s</Date>', $data[1]['date']) . $this->sep;
                        $xml .= chr(9) . '</NextOneDay>' . $this->sep;
                        $xml .= chr(9) . '<NextTwoDay>' . $this->sep;
                        $str3 = get_weather_lang(1, $data[2]['iconNum']);
                        $xml .= chr(9) . chr(9) . sprintf('<title>%s</title>', $str3) . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<City>%s</City>', $city['city']) . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<Content>%s~%s</Content>', $data[2]['low'], $data[2]['high']) . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<Pic>%s</Pic>', $data[2]['icon']) . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<Date>%s</Date>', $data[2]['date']) . $this->sep;
                        $xml .= chr(9) . '</NextTwoDay>' . $this->sep;
                        $xml .= '</Weather>' . $this->sep;
                    } else {
                        $xml .= '<Weather>' . $this->sep;
                        $xml .= chr(9) . sprintf('<City>%s</City>', $city['city']) . $this->sep;
                        $xml .= chr(9) . sprintf('<Content>%s~%s</Content>', $data[0]['low'], $data[0]['high']) . $this->sep;
                        $xml .= chr(9) . sprintf('<Pic>%s</Pic>', $data[0]['icon']) . $this->sep;
                        $xml .= chr(9) . sprintf('<Date>%s</Date>', $data[0]['date']) . $this->sep;
                        $xml .= chr(9) . '<NextOneDay>' . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<City>%s</City>', $city['city']) . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<Content>%s~%s</Content>', $data[1]['low'], $data[1]['high']) . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<Pic>%s</Pic>', $data[1]['icon']) . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<Date>%s</Date>', $data[1]['date']) . $this->sep;
                        $xml .= chr(9) . '</NextOneDay>' . $this->sep;
                        $xml .= chr(9) . '<NextTwoDay>' . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<City>%s</City>', $city['city']) . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<Content>%s~%s</Content>', $data[2]['low'], $data[2]['high']) . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<Pic>%s</Pic>', $data[2]['icon']) . $this->sep;
                        $xml .= chr(9) . chr(9) . sprintf('<Date>%s</Date>', $data[2]['date']) . $this->sep;
                        $xml .= chr(9) . '</NextTwoDay>' . $this->sep;
                        $xml .= '</Weather>' . $this->sep;
                    }

                    $this->load->helper('file');

                    $md5 = md5($xml);
                    $cachedPath = $this->config->item('cached_temp_path');
                    if (!file_exists($cachedPath)) {
                        mkdir($cachedPath, 0777, true);
                    }
                    //save temp file...
                    $cachedFile =  $cachedPath . $md5;
                    if (!file_exists($cachedFile)) {
                        saveFile($cachedFile, $xml);
                    }
                    //$this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[".$this->player->name."] downloads weather info for [".$city['city']."] city");
                    return downloadNP200File($cachedFile, 'Weather.xml');
                }
            } else {
                $weather = get_weather($this->player->city_code, $company->weather_format);
                if ($weather) {
                    $xml = '<?xml version="1.0" encoding="utf-8"?>' . $this->sep;
                    //$xml .= sprintf("<Weather>\n<City>%s</City>\n<Content>%s~%s</Content>\n<Pic>%s</Pic>\n</Weather>", $weather['city'], $weather['low'], $weather['high'], $weather['icon']);
                    $lang_id = $this->device->get_weather_lang_by_sn($this->sn);
                    if ($lang_id[0]->language == 1) {
                        $arr = explode('.', $weather['icon']);
                        $str = get_weather_lang($lang_id[0]->language, $arr[0]);
                        $xml .= sprintf("<Weather>\n<title>%s</title>\n<City>%s</City>\n<Content>%s~%s</Content>\n<Pic>%s</Pic>\n</Weather>", $str, $weather['city'], $weather['low'], $weather['high'], $weather['icon']);
                    } else {
                        $xml .= sprintf("<Weather>\n<City>%s</City>\n<Content>%s~%s</Content>\n<Pic>%s</Pic>\n</Weather>", $weather['city'], $weather['low'], $weather['high'], $weather['icon']);
                    }
                    $this->load->helper('file');

                    $md5 = md5($xml);
                    $cachedPath = $this->config->item('cached_temp_path');
                    if (!file_exists($cachedPath)) {
                        mkdir($cachedPath, 0777, true);
                    }
                    //save temp file...
                    $cachedFile =  $cachedPath . $md5;
                    if (!file_exists($cachedFile)) {
                        saveFile($cachedFile, $xml);
                    }
                    $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloads weather info for [" . $weather['city'] . "] city");
                    return downloadNP200File($cachedFile, 'Weather.xml');
                }
            }
            /*
            $this->load->model('membership');
            $this->load->helper('weather');
            $company=$this->membership->get_company($this->player->company_id);
            $weather = get_weather($this->player->city_code, $company->weather_format);
            if($weather){
                $xml = '<?xml version="1.0" encoding="utf-8"?>'.$this->sep;
                $xml .= sprintf("<Weather>\n<City>%s</City>\n<Content>%s~%s</Content>\n<Pic>%s</Pic>\n</Weather>", $weather['city'], $weather['low'], $weather['high'], $weather['icon']);
                $this->load->helper('file');

                $md5 = md5($xml);
                $cachedPath = $this->config->item('cached_temp_path');
                if(!file_exists($cachedPath)){
                    mkdir($cachedPath, 0777, TRUE);
                }
                //save temp file...
                $cachedFile =  $cachedPath.$md5;
                if(!file_exists($cachedFile)){
                    saveFile($cachedFile, $xml);
                }
                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[".$this->player->name."] downloads weather info for [".$weather['city']."] city");
                return downloadNP200File($cachedFile, 'Weather.xml');

                //return downloadContent($xml,'xml', 0, 'Weather.xml');
            }*/
        }

        set_status_header(404, 'DynamicData [' . $dynamicType . '] Not found!');
    }

    //生成天气xml（一天的）
    public function downloadWeatherOneDay($dynamicType)
    {
        if ($dynamicType == 0) {
            $this->load->model('membership');
            $this->load->helper('weather');
            $company = $this->membership->get_company($this->player->company_id);
            $weather = get_weather($this->player->city_code, $company->weather_format);
            if ($weather) {
                $xml = '<?xml version="1.0" encoding="utf-8"?>' . $this->sep;
                $xml .= sprintf("<Weather>\n<City>%s</City>\n<Content>%s~%s</Content>\n<Pic>%s</Pic>\n</Weather>", $weather['city'], $weather['low'], $weather['high'], $weather['icon']);
                $this->load->helper('file');

                $md5 = md5($xml);
                $cachedPath = $this->config->item('cached_temp_path');
                if (!file_exists($cachedPath)) {
                    mkdir($cachedPath, 0777, true);
                }
                //save temp file...
                $cachedFile =  $cachedPath . $md5;
                if (!file_exists($cachedFile)) {
                    saveFile($cachedFile, $xml);
                }
                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloads weather info for [" . $weather['city'] . "] city");
                return downloadNP200File($cachedFile, 'Weather.xml');
            }
        }
    }

    //生成天气xml（三天的）
    public function downloadWeahterThreeDays($dynamicType)
    {
        if ($dynamicType == 0) {
            $this->load->model('membership');
            $this->load->helper('weather');
            $this->load->helper('date');
            $company = $this->membership->get_company($this->player->company_id);
            $weather = get_yahoo_weather_3days($this->player->city_code, $company->weather_format);
            //echo '<pre>';
            //print_r($weather);
            //echo '</pre>';
            if ($weather) {
                $gmt = local_to_gmt(time());
                $city = $weather['city'];
                $data = $weather['data'];
                $xml = '<?xml version="1.0" encoding="utf-8"?>' . $this->sep;
                $xml .= '<Weather>' . $this->sep;
                $xml .= sprintf('<City>%s</City>', $city['city']) . $this->sep;
                $xml .= sprintf('<Content>%s~%s</Content>', $data[0]['low'], $data[0]['high']) . $this->sep;
                $xml .= sprintf('<Pic>%s</Pic>', $data[0]['icon']) . $this->sep;
                $xml .= sprintf('<Date>%s</Date>', date('Y-m-d', $gmt)) . $this->sep;
                $xml .= '<NextOneDay>' . $this->sep;
                $xml .= sprintf('<City>%s</City>', $city['city']) . $this->sep;
                $xml .= sprintf('<Content>%s~%s</Content>', $data[1]['low'], $data[1]['high']) . $this->sep;
                $xml .= sprintf('<Pic>%s</Pic>', $data[1]['icon']) . $this->sep;
                $xml .= sprintf('<Date>%s</Date>', date('Y-m-d', $gmt + 3600 * 24)) . $this->sep;
                $xml .= '</NextOneDay>' . $this->sep;
                $xml .= '<NextTwoDay>' . $this->sep;
                $xml .= sprintf('<City>%s</City>', $city['city']) . $this->sep;
                $xml .= sprintf('<Content>%s~%s</Content>', $data[2]['low'], $data[2]['high']) . $this->sep;
                $xml .= sprintf('<Pic>%s</Pic>', $data[2]['icon']) . $this->sep;
                $xml .= sprintf('<Date>%s</Date>', date('Y-m-d', $gmt + 2 * 3600 * 24)) . $this->sep;
                $xml .= '</NextTwoDay>' . $this->sep;
                $xml .= '</Weather>' . $this->sep;

                $this->load->helper('file');

                $md5 = md5($xml);
                $cachedPath = $this->config->item('cached_temp_path');
                if (!file_exists($cachedPath)) {
                    mkdir($cachedPath, 0777, true);
                }
                //save temp file...
                $cachedFile =  $cachedPath . $md5;
                if (!file_exists($cachedFile)) {
                    saveFile($cachedFile, $xml);
                }
                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloads weather info for [" . $weather['city'] . "] city");
                return downloadNP200File($cachedFile, 'Weather.xml');
            }
        }
    }

    /**
     * 终端定时开关机策略下载
     * sn
     *
     * @return
     */
    public function downloadTimecfg()
    {
        $this->load->model('device');
        $this->load->model('strategy');


        if (isset($this->params['request_at'])) {
            $reqeust_at = $this->params['request_at'];
            $request_year = date('Y', $reqeust_at / 1000);
            $this_year = date("Y");
            if ($request_year < $this_year) {
                $this->device->update_player(array('replace_battery' => 1), $this->player->id);
            } else {
                $this->device->update_player(array('replace_battery' => 0), $this->player->id);
            }
        }


        $view = $this->strategy->get_timer($this->player->timer_config_id);
        $this->load->helper('file');
        if ($view) {
            $cfg = '<?xml version="1.0" encoding="UTF-8"?>' . $this->sep;
            $cfg .= "<ClientTimeConfig>\n<TimeConfigs>" . $this->sep;

            if ($view->type == 0) {
                $extras = $this->strategy->get_timer_extra($this->player->timer_config_id, 0);
                if ($extras) {
                    $cfg .= '<DayConfigs weekCode="0">' . $this->sep;
                    $i = 1;
                    foreach ($extras as $extra) {
                        $cfg .= sprintf('<config id="%d" status="%d">%s<startTime>%s</startTime>%s<shutdownTime>%s</shutdownTime>%s</config>', $i, $extra->status, $this->sep, $extra->start_time, $this->sep, $extra->end_time, $this->sep) . $this->sep;
                        $i++;
                    }
                    $cfg .= '</DayConfigs>' . $this->sep;
                } else {
                    $cfg .= '<DayConfigs weekCode="0">' . $this->sep;
                    $cfg .= sprintf('<config id="%d" status="%d">%s<startTime>%s</startTime>%s<shutdownTime>%s</shutdownTime>%s</config>', 1, 1, $this->sep, "00:00", $this->sep, "00:00", $this->sep) . $this->sep;
                    $cfg .= '</DayConfigs>' . $this->sep;
                }
            } else {
                $offwds = explode(',', $view->offweekdays);
                for ($i = 1; $i <= 7; $i++) {
                    if (in_array($i, $offwds)) {
                        $cfg .= '<DayConfigs weekCode="' . $i . '" mode="2">' . $this->sep;
                        $cfg .= '</DayConfigs>' . $this->sep;
                    } else {
                        $extras = $this->strategy->get_timer_extra($this->player->timer_config_id, $i);
                        if ($extras) {
                            $cfg .= '<DayConfigs weekCode="' . $i . '" mode="3">' . $this->sep;
                            $j = 1;
                            foreach ($extras as $extra) {
                                $cfg .= sprintf('<config id="%d" status="%d">%s<startTime>%s</startTime>%s<shutdownTime>%s</shutdownTime>%s</config>', $j, $extra->status, $this->sep, $extra->start_time, $this->sep, $extra->end_time, $this->sep) . $this->sep;
                                $j++;
                            }
                            $cfg .= '</DayConfigs>' . $this->sep;
                        } else {
                            $cfg .= '<DayConfigs weekCode="' . $i . '" mode="3">' . $this->sep;
                            $cfg .= sprintf('<config id="%d" status="%d">%s<startTime>%s</startTime>%s<shutdownTime>%s</shutdownTime>%s</config>', 1, 1, $this->sep, "00:00", $this->sep, "00:00", $this->sep) . $this->sep;
                            $cfg .= '</DayConfigs>' . $this->sep;
                        }
                    }
                }
            }


            $cfg .= "</TimeConfigs>\n</ClientTimeConfig>";


            if (downloadContent($cfg, "TIME.cfg")) {
                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloads timer.cfg [" . $view->name . "]");
                $this->device->update_player(array('timer_config_flag' => 2), $this->player->id);
                return;
            }
            /*
            $md5 = md5($cfg);
            $cachedPath = $this->config->item('cached_temp_path');
            if(!file_exists($cachedPath)){
                mkdir($cachedPath, 0777, TRUE);
            }
            //save temp file...
            $cachedFile =  $cachedPath.$md5;
            if(!file_exists($cachedFile)){
                saveFile($cachedFile, $cfg);
            }

            if(downloadNP200File($cachedFile, 'TIME.cfg')){
                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[".$this->player->name."] downloads timer.cfg [".$view->name."]");
                $this->device->update_player(array('timer_config_flag'=>2), $this->player->id);
                return;
            }
            */
        } else {

            $cfg = '<?xml version="1.0" encoding="UTF-8"?>' . $this->sep;
            $cfg .= '<ClientTimeConfig>' . $this->sep;
            $cfg .= '<TimeConfigs>' . $this->sep;
            $cfg .= '<DayConfigs weekCode="0">' . $this->sep;
            $cfg .= '<config id="1" status="1">' . $this->sep;
            $cfg .= '<startTime>00:00</startTime>' . $this->sep;
            $cfg .= '<shutdownTime>00:00</shutdownTime>' . $this->sep;
            $cfg .= '</config>' . $this->sep;
            $cfg .= '</DayConfigs>' . $this->sep;
            $cfg .= '</TimeConfigs>' . $this->sep;
            $cfg .= '</ClientTimeConfig>' . $this->sep;

            if (downloadContent($cfg, "TIME.cfg")) {
                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Turn off timer");
                $this->device->update_player(array('timer_config_flag' => 2), $this->player->id);
                return;
            }
        }

        //需要知道终端定时开关机策略的数据定义以及接口输出定义
        set_status_header(404, 'downloadTimecfg not support');
    }

    /**
     * 终端下载策略下载
     * sn
     *
     * @return
     */
    public function downloadDownloadStrategy()
    {
        //there're no download strategy for icat6
        /*
            $this->load->model('device');
            $this->load->model('strategy');
            $group = $this->device->get_group($this->player->group_id);
            if($group){
                $view = $this->strategy->get_download($group->download_strategy_id);
                if($view){
                    $this->load->helper('file');
                    $cfg = '<?xml version="1.0" encoding="UTF-8"?>'.$this->sep;;
                    $cfg .= '<ClientDownConfigs>'.$this->sep;
                    $cfg .= sprintf('<Config><StartTime>%s</StartTime><EndTime>%s</EndTime></Config>', $view->start_time, $view->end_time).$this->sep;
                    $extras = $this->strategy->get_download_extra($group->download_strategy_id);
                    if($extras){
                        foreach($extras as $extra){
                            $cfg .= sprintf('<Config><StartTime>%s</StartTime><EndTime>%s</EndTime></Config>', $extra->start_time, $extra->end_time).$this->sep;;
                        }
                    }
                    $cfg .= '</ClientDownConfigs>';

                    $md5 = md5($cfg);
                    $cachedPath = $this->config->item('cached_temp_path');
                    if(!file_exists($cachedPath)){
                        mkdir($cachedPath, 0777, TRUE);
                    }
                    //save temp file...
                    $cachedFile =  $cachedPath.$md5;
                    if(!file_exists($cachedFile)){
                        saveFile($cachedFile, $cfg);
                    }

                    return downloadNP200File($cachedFile, 'DOWNLOAD.cfg');
                }
            }
        */
        //需要知道终端下载策略的数据定义以及接口输出定义
        set_status_header(404, 'downloadDownloadStrategy not find');
    }

    /**
     * 终端显示策略下载
     *
     * @return
     */
    public function downloadViewConfig()
    {
        //需要知道显示策略的数据定义以及接口输出定义
        /*
        $this->load->model('device');
        $this->load->model('strategy');
        $group = $this->device->get_group($this->player->group_id);
        if($group){
            $view = $this->strategy->get_view($group->view_config_id);
            if($view){
                $this->load->helper('file');
                $cfg = '<?xml version="1.0" encoding="UTF-8"?>'.$this->sep;
                $cfg .= sprintf("<ClientViewConfig>\n<startDate>%s</startDate>\n<endDate>%s</endDate>\n<brightness>%d</brightness>\n<saturation>%d</saturation>\n<contrast>%d</contrast>\n</ClientViewConfig>",
                                str_replace(' ', '_', $view->start_datetime), str_replace(' ', '_', $view->end_datetime),$view->brightness, $view->saturation, $view->contrast);

                $md5 = md5($cfg);
                $cachedPath = $this->config->item('cached_temp_path');
                if(!file_exists($cachedPath)){
                    mkdir($cachedPath, 0777, TRUE);
                }
                //save temp file...
                $cachedFile =  $cachedPath.$md5;
                if(!file_exists($cachedFile)){
                    saveFile($cachedFile, $cfg);
                }

                return downloadNP200File($cachedFile, 'CLIENTVIEW.cfg');
            }
        }
        */
        set_status_header(404, 'downloadViewConfig not support');
    }

    /**
     * RSS动态信息下载
     *
     * @return
     */
    public function downloadRss()
    {
        //$id = $this->input->get('id');
        $id = $this->params['id'];
        if ($id === false) {
            set_status_header(400, 'request param error!');
            return;
        }
        $this->load->model('material');
        $this->load->model('program');
        $a_p_id = $this->material->get_text_setting($id, $this->input->get('sn'));
        $playlist_id = $this->material->get_playlistId_by_sn($id, $this->input->get('sn'));
        $setting = $this->program->get_playlist_area_text_setting($a_p_id[1], $a_p_id[0]);

        $playlist_settings = $this->program->get_playlist($playlist_id[0]); //获取列表设置
        $rss_delimiter = $playlist_settings->rss_delimiter;     //rss分割标记

        $this->load->model('material');
        $rss = $this->material->get_rss($id);
        $rss_content = '';
        if ($rss) {
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
                            //$rss_content .= $items[$i]['description'];
                            if (isset($items[$i]['description'])) {
                                $rss_content .= $items[$i]['description'];
                            }
                            if ($i < count($items) - 1) {
                                //$rss_content .= '<<';
                                $rss_content .= $rss_delimiter;
                            }
                            break;
                        default:
                            $rss_content .= $items[$i]['title'];
                            //$rss_content .= '<<';
                            $rss_content .= $rss_delimiter;
                            //$rss_content .= $items[$i]['description'];
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
                $this->load->helper('file');
                $rss_content = str_replace("&#039;", "'", $rss_content);
                $rss_content = str_replace("&quot;", "\"", $rss_content);
                $rss_content = '<?xml version="1.0" encoding="UTF-8"?>' . chr(10) . chr(10) . '<rss><![CDATA[' . $rss_content . chr(10) . chr(10) . ']]></rss>';

                $md5 = md5($rss_content);
                $cachedPath = $this->config->item('cached_temp_path');
                if (!file_exists($cachedPath)) {
                    mkdir($cachedPath, 0777, true);
                }
                if ($this->player->player_type == 1) {
                    $md52 = md5($rss_content . '<!--' . $md5 . '-->' . chr(10));
                    $cachedFile =  $cachedPath . $md52;
                    if (!@file_exists($cachedFile)) {
                        saveFile($cachedFile, $rss_content . '<!--' . $md5 . '-->' . chr(10));
                    }
                    if (downloadNP200File($cachedFile, 'Rss_' . $id . '.xml')) {
                        return;
                    }
                } else {
                    $cachedFile =  $cachedPath . $md5;
                    if (!@file_exists($cachedFile)) {
                        saveFile($cachedFile, $rss_content . chr(10) . '<!--' . $md5 . '-->');
                    }
                    if (downloadNP200File($cachedFile, 'Rss_' . $id . '.xml')) {
                        return;
                    }
                }
            }
        }
        /*
        $this->load->model('material');
        $rss = $this->material->get_rss($id);
        if($rss){
            $this->load->library('rssparser');
            $rssObj = $this->rssparser->Get($rss->url, TRUE, TRUE);
            if($rssObj){
                $this->load->helper('file');
                $rssObj['content'] = str_replace("&#039;", "&apos;", $rssObj['content']);
                $md5 = md5($rssObj['content']);
                $cachedPath = $this->config->item('cached_temp_path');
                if(!file_exists($cachedPath)){
                    mkdir($cachedPath, 0777, TRUE);
                }
                //save temp sch file...
                $cachedFile =  $cachedPath.$md5;

                if(!@file_exists($cachedFile)){
                    //saveFile($cachedFile, $rssObj['content']);
                    saveFile($cachedFile, $rssObj['content'].chr(10).'<!--'.$md5.'-->');
                }

                if(downloadNP200File($cachedFile, 'Rss_'.$id.'.xml')){
                    $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[".$this->player->name."] downloads RSS file [Rss_".$id.".xml]");
                    return;
                }
            }
        }*/
        //下载动态RSS需要知道输出的格式是RSS的那部分
        set_status_header(404, 'RSS [' . $id . '] Not found!');
    }
    /**
     * 媒体播放记录统计
     *
     * sn:
     * text:统计信息
     */
    public function getMediaRecord()
    {

        //$date = $this->input->get('date');
        if (isset($this->params['date'])) {
            $date = $this->params['date'];
        } else {
            $date = $this->input->get('date');
        }
        if (isset($this->params['text'])) {
            $text = $this->params['text'];
        } else {
            $text = $this->input->post('text');
        }

        if ($text === false) {
            $text = $this->input->post('text');
        }


        if ($date === false || $text === false) {
            set_status_header(400, 'request param error!');
            return;
        }


        //file_put_contents('/home/icat/public_html/application/logs/reportlog.' . date('Y-m-d H:i:s') . "-" . $this->params['sn'] . '.log', $text);

        $line = preg_split('/,/', $text);
        $reports = array();
        if (count($line) > 0) {
            $this->load->model('feedback');
            $this->load->model('program');
            $result = array();
            foreach ($line as $item) {
                //list($mid, $sid, $times, $duration) = split('\|', $item);
                $items = preg_split('/\|/', $item);
                if (!$items) {
                    continue;
                }
                if (count($items) < 4) {
                    continue;
                }
                list($mid, $sid, $times, $duration) = $items;
                //	$media_id = $this->program->get_playlist_area_media_id($mid);
                //$array = array('post_date'=>$date,'player_id'=>$this->player->id, 'media_id'=>$mid, 'schedule_id'=>$sid, 'company_id'=>$this->player->company_id, 'times'=>$times,'duration'=>$duration);
                $times = intval($times);
                $duration = intval($duration);

                if (isset($reports[$mid])) {
                    $array = array('post_date' => $date, 'player_id' => $this->player->id, 'media_id' => $mid, 'company_id' => $this->player->company_id, 'times' => $times + $reports[$mid]['times'], 'duration' => $duration + $reports[$mid]['duration']);
                } else {
                    $array = array('post_date' => $date, 'player_id' => $this->player->id, 'media_id' => $mid, 'company_id' => $this->player->company_id, 'times' => $times, 'duration' => $duration);
                }

                $reports[$mid] = $array;

                // $result[] = $this->feedback->add_feedback($array);
            }
            if (!empty($reports)) {
                $this->feedback->add_feedback_batch($reports);
            }


            //echo implode(',', $result);
            $this->device->add_player_log($this->player->id, $this->config->item('event_type_playback'), "Player[" . $this->player->name . "] uploads media record");
        } else {
            set_status_header(400, 'request param error!');
        }
    }

    /**
     * 终端时间同步
     *
     * sn
     * time1
     * 终端发送请求时间戳
     *
     * @return
     */
    public function syncPlayerTime()
    {
        $time1 = $this->input->get('time1');

        if ($time1 === false) {
            set_status_header(400, 'request param error!');
            return;
        }

        $rtime = $_SERVER["REQUEST_TIME"];
        //需要知道返回的数据格式。GMT时间和毫秒值分别是？
        //$this->load->helper('file');
        header("time2=" . $rtime);
        header("time3=" . time());
        set_status_header(200);
    }

    /**
     * 时间同步函数
     *
     * @return
     */
    public function syncPlayerTime1()
    {
        if (!isset($this->params['verification'])) {
            set_status_header(400, 'request param error!');
            return;
        }
        $verification = $this->params['verification'];

        $this->load->model('membership');
        $dst = $this->membership->is_dst_on($this->player->company_id);

        list($rusec, $rsec) = preg_split("/ /", microtime());

        $reps_ver = (intval((intval(($verification) * 9 / 5) + 1078) * 11 / 10)) % 10000;
        if ($reps_ver < 10) {
            $reps_ver = '000' . $reps_ver;
        } elseif ($reps_ver < 100) {
            $reps_ver = '00' . $reps_ver;
        } elseif ($reps_ver < 1000) {
            $reps_ver = '0' . $reps_ver;
        }

        $data_template = 'Received:%s;Transmit:%s;Verification:%s;';
        list($susec, $ssec) = preg_split("/ /", microtime());
        $dst_stamp = 0;
        if ($dst) {
            //one hour
            $dst_stamp = 3600;
        }
        $data = sprintf($data_template, $this->gmt_microtime_str($rsec + $dst_stamp, $rusec), $this->gmt_microtime_str($ssec + $dst_stamp, $susec), $reps_ver);
        $sig_data = $data . "83317701";
        $sig = md5($sig_data);
        $data = $data . 'Signature:' . $sig . ';';
        header("Content-Length: " . strlen($data));
        echo $data;
        $this->device->add_player_log($this->player->id, $this->config->item('event_type_time'), "Player[" . $this->player->name . "] sync date/time successfully");
    }

    public function gmt_microtime_str($sec, $usec)
    {
        $mt = intval($usec * 1000) % 1000;
        if ($mt < 10) {
            $mt .= '00';
        } elseif ($mt < 100) {
            $mt .= '0';
        }

        return gmdate('Y-m-d H:i:s', $sec) . "." . $mt . " GMT";
    }

    public function updateRemotePLS()
    {
        $this->sn = $this->input->get('sn');
        $player = $this->player;
        $pids = $this->device->get_playlist_by_sn($this->sn);

        if ($pids) {
            set_status_header(200);
            foreach ($pids as $ids) {
                $mids = $this->device->get_HttpMedia_by_pid($ids->playlist_id);
                foreach ($mids as $mid) {
                    $this->load->model('material');
                    //或取size
                    $file_size = $this->material->get_remote_file_size($mid->full_path);
                    $this->material->update_media(array('file_size' => $file_size), $mid->id);
                }

                //播放列表中有HTTP类型的文件，重新生成一次pls
                //pls文件生成开始
                set_time_limit(0);
                $this->sep = chr(10) . chr(10);
                $id = $ids->playlist_id;
                //$rotate = $ids->rotate;
                $this->load->model('program');
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

                                $xml .= sprintf('<Area id="%d" name="%s" model="%d" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%" bold="%d" color="%s" bg_color="%s" family="%s" size="%s" style="%d"/>', $area->id, $area->name, $area->area_type, ($area->x / $template->w) * 100, ($area->y / $template->h) * 100, ($area->w / $template->w) * 100, ($area->h / $template->h) * 100, $setting->bold, $setting->color, $setting->bg_color, $setting->font_family, $setting->font_size, $setting->format) . $this->sep;
                                break;
                            case $this->config->item('area_type_weather'):
                                $setting = $this->program->get_area_weather_setting($area->id, $id);

                                $xml .= sprintf('<Area id="%d" name="%s" model="%d" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%" color="%s" bg_color="%s" family="%s" size="%s" style="%d"/>', $area->id, $area->name, $area->area_type, ($area->x / $template->w) * 100, ($area->y / $template->h) * 100, ($area->w / $template->w) * 100, ($area->h / $template->h) * 100, $setting->color, $setting->bg_color, $setting->font_family, $setting->font_size, $setting->format) . $this->sep;
                                break;
                            default:
                                $xml .= sprintf('<Area id="%d" name="%s" model="%d" left="%01.2f%%" top="%01.2f%%" width="%01.2f%%" height="%01.2f%%"/>', $area->id, $area->name, $area->area_type, ($area->x / $template->w) * 100, ($area->y / $template->h) * 100, ($area->w / $template->w) * 100, ($area->h / $template->h) * 100) . $this->sep;
                                break;
                        }
                    }
                    $xml .= '</Screen>' . $this->sep;
                    $portrait = $template->w < $template->h;
                    $rotate = $portrait;
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
                                        $this->load->model('membership');
                                        $company = $this->membership->get_company($player->company_id);
                                        $video_fit = $company->fitORfill;
                                        if ($media->media_type == 1) {
                                            $fit = $media->img_fitORfill;
                                        } else {
                                            $fit = $video_fit;
                                        }

                                        //只有当视频播放列表的选择标志选择了，才执行旋转
                                        //add template->height template->width --start--
                                        $movie_area = $this->program->get_area($area->id);
                                        $t_width = $movie_area->w;
                                        $t_height = $movie_area->h;
                                        //--end--
                                        //如果FTP
                                        $size = 0;
                                        if ($media->source == 2) {
                                            $outfile = $media->full_path;
                                            $size = $media->file_size;
                                            if ($media->status == 0) {
                                                if ($media->starttime < $media->endtime) {
                                                    $xml .= sprintf('<Resource id="%d" name="%s" fid="%d_%d" size="%d" signature="%s" sw110Signature="%s" transmode="%d"  duration="00:%s" transittime="%s" mode="%d">', $media->id, $this->device->xmlencode($media->name), /*$media->media_id*/ $media->id, $area->id, $size, $media->signature, $media->signature, $media->transmode >= 0 ? $transmodemapping[$media->transmode] : 0, $media->duration == '--' ? '00:00' : $media->duration, $media->transtime >= 0 ? $media->transtime : 0, $area->area_type) . $this->sep;
                                                    $xml .= sprintf('<HTTP>%s</HTTP>', $outfile) . $this->sep;
                                                    $xml .= '</Resource>' . $this->sep;
                                                } else {
                                                    $xml .= sprintf('<Resource id="%d" name="%s" fid="%d_%d" size="%d" signature="%s" sw110Signature="%s" transmode="%d" duration="00:%s" transittime="%s" mode="%d">', $media->id, $this->device->xmlencode($media->name), /*$media->media_id*/ $media->id, $area->id, $size, $media->signature, $media->signature, $media->transmode >= 0 ? $transmodemapping[$media->transmode] : 0, $media->duration == '--' ? '00:00' : $media->duration, $media->transtime >= 0 ? $media->transtime : 0, $area->area_type) . $this->sep;
                                                    $xml .= sprintf('<HTTP>%s</HTTP>', $outfile) . $this->sep;
                                                    $xml .= '</Resource>' . $this->sep;
                                                }
                                            }
                                        } else {
                                            if ($media->source == 1 && $media->media_type == $this->config->item('media_type_video')) {
                                                $outfile = $media->full_path;
                                                $size = $media->file_size;
                                                //如果是视频且FTP,则签名为空
                                                $pls_signature = '';
                                            } else {
                                                $outfile = generate_client_area_media($media, $template, $area, $t_width, $t_height, ($portrait && $rotate) ? (($movie_area) ? $media->rotate : true) : false, $fit);
                                                $this->db->reconnect();
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
                                                $this->program->update_area_media(array('publish_url' => $outfile), $media->id);
                                                //$pls_media_path = $base_path.substr($outfile, 1, (strlen($outfile)-1));
                                                $pls_signature = md5_file($outfile);
                                            }
                                            if ($media->status == 0) {
                                                if ($media->starttime < $media->endtime) {
                                                    $media_name = $this->device->xmlencode($this->device->rename_media_name($media->name, $media->media_type, $area->area_type, $rotate, $media->id));
                                                    if ($media->media_type == 1) {
                                                        if ($fit) {
                                                            $media_name = '[F]' . $media_name;
                                                        } else {
                                                            $media_name = '[W]' . $media_name;
                                                        }
                                                    }
                                                    $xml .= sprintf('<Resource id="%d" name="%s" fid="%d_%d" size="%d" signature="%s" sw110Signature="%s" transmode="%d" starttime="%s" endtime="%s" duration="00:%s" transittime="%s" mode="%d">', $media->id, $media_name, /*$media->media_id*/ $media->id, $area->id, $size, $media->signature, $pls_signature, $media->transmode >= 0 ? $transmodemapping[$media->transmode] : 0, $media->starttime, $media->endtime, $media->duration == '--' ? '00:00' : $media->duration, $media->transtime >= 0 ? $media->transtime : 0, $area->area_type) . $this->sep;
                                                    $xml .= sprintf('<URL>%s</URL>', $outfile) . $this->sep;
                                                    $xml .= '</Resource>' . $this->sep;
                                                } else {
                                                    $media_name = $this->device->xmlencode($this->device->rename_media_name($media->name, $media->media_type, $area->area_type, $rotate, $media->id));
                                                    if ($media->media_type == 1) {
                                                        if ($fit) {
                                                            $media_name = '[F]' . $media_name;
                                                        } else {
                                                            $media_name = '[W]' . $media_name;
                                                        }
                                                    }
                                                    $xml .= sprintf('<Resource id="%d" name="%s" fid="%d_%d" size="%d" signature="%s" sw110Signature="%s" transmode="%d" duration="00:%s" transittime="%s" mode="%d">', $media->id, $media_name, /*$media->media_id*/ $media->id, $area->id, $size, $media->signature, $pls_signature, $media->transmode >= 0 ? $transmodemapping[$media->transmode] : 0, $media->duration == '--' ? '00:00' : $media->duration, $media->transtime >= 0 ? $media->transtime : 0, $area->area_type) . $this->sep;
                                                    $xml .= sprintf('<URL>%s</URL>', $outfile) . $this->sep;
                                                    $xml .= '</Resource>' . $this->sep;
                                                }
                                            }
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


                    $playlist_path = $this->config->item('playlist_publish_path') . $player->company_id;
                    if (!file_exists($playlist_path)) {
                        mkdir($playlist_path, 0777, true);
                    }

                    $playlist_path .= '/' . $id . '.PLS';
                    saveFile($playlist_path, $xml);

                    //update file_size&signature
                    $file_size = filesize($playlist_path);
                    $signature = md5_file($playlist_path);
                    $updates = array('file_size' => $file_size, 'signature' => $signature, 'update_time' => date('Y-m-d H:i:s'), 'published' => $this->config->item('playlist.status.published'));
                    $this->program->update_playlist($updates, $id);
                    //-pls文件生成结束

                    //更新schedule的发布时间
                    $this->load->model('program');
                    $this->program->update_schedule(array('publish_time' => date('Y-m-d H:i:s')), $ids->schedule_id);
                }
            }
        } else {
            set_status_header(404, 'NO Update PLS');
        }
    }
    /**
     * HTTP类型的媒体文件，下载日志记录
     */
    private function DownloadRmtMMF()
    {
        $url = $this->input->get('url');
        $this->load->model('material');
        $media = $this->material->get_area_media_by_url($url);
        $this->device->update_status($this->sn, 3); //Set download status
        $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloads HTTP media file [" . $media->name . "].");
        set_status_header(200);
    }

    /*
     * 保存 Android 终端截屏返回的图片
     * sn
     */
    public function receiveClientScreen()
    {
        //	$sn = $this->input->get('sn');
        $sn = $this->sn;
        $screenShotPath = $this->config->item('resources') . 'preview/' . $this->player->company_id . '/';
        if (!file_exists($screenShotPath)) {
            mkdir($screenShotPath, 0777, true);
        }
        $imgname = $sn . '.png';

        $file = fopen($screenShotPath . $imgname, 'w+');
        fwrite($file, file_get_contents("php://input"));
        fclose($file);


        $config['image_library'] = 'gd2';
        $config['source_image'] = $screenShotPath . $imgname;
        $config['maintain_ratio'] = true;
        // $config['width']     = 1024;
        //$config['height']   = 600;

        $this->load->library('image_lib', $config);

        $this->image_lib->resize();

        //更新终端信息
        $this->device->add_player_log($this->player->id, $this->config->item('event_type_heartbeat'), "Screenshot has been uploaded");
        $this->load->model('device');
        $this->device->update_player(array('screenshot' => $screenShotPath . $imgname, 'screenshotDate' => date('Y-m-d H:i:s')), $this->player->id);
    }

    /**
     * 保存 Android 终端的日志
     */
    public function uploadErrorLog()
    {
        //$sn = $this->input->get('sn');
        $sn = $this->sn;
        $cachedPath = $this->config->item('cached_errorlog_path');
        if (!file_exists($cachedPath)) {
            mkdir($cachedPath, 0777, true);
        }
        $screenShotPath = $cachedPath . $sn . '_' . date('Y-m-d His') . '.zip';
        $file = fopen($screenShotPath, 'w+');
        fwrite($file, file_get_contents("php://input"));
        fclose($file);
    }


    /**
     *
     * @param TimeSlot $slot
     * @return boolean|string
     */
    private function create_playlist_by_timeslot($slot, $today)
    {
        //	chrome_log("create_playlist_by_timeslot");
        //	chrome_log(sprintf("Slot=%d:%d",$slot->startH,$slot->startM));
        //FIXME
        if (empty($slot->campaigns)) {
            return false;
        }
        $this->load->model('program');

        $this->load->helper('file');


        if ($this->player->screen_oritation) {
            $template_width = 1920;
            $template_height = 1080;
            $template_name = "landscape";
        } else {
            $template_width = 1080;
            $template_height = 1920;
            $template_name = "portraint";
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . chr(10);
        $xml .= '<SignwayPoster type="playlist" version="1.0.1">' . $this->sep;
        $xml .= sprintf('<Templatename>%s</Templatename>', $template_name) . $this->sep;
        $xml .= sprintf('<ScreenType height="%d" rotation="0" width="%d"/>', $template_height, $template_width) . $this->sep;

        //chrome_log($xml);
        $medias = array();
        $medias = $this->program->get_sorted_timeslot_medias($slot, $today);
        //chrome_log($this->db->last_query());


        if (count($medias) <= 0) {
            return false;
        }


        $fid = sprintf("%d_%02d_%02d", $this->player->id, $slot->startH, $slot->startM);

        $xml .= sprintf('<Programme id="%s" name="%s">', $fid, $fid) . $this->sep;


        $xml .= '<Area id="101" name="Movie/Photo" model="0" left="0.00%" top="0.00%" width="100.00%" height="100.00%" zindex="10" />' . $this->sep;
        $xml .= '<AreaList id="101" playtime="00:00:30">' . $this->sep;
        $transmodemapping = $this->config->item('media.transmode.mapping');

        foreach ($medias as $media) {
            //FIXME VIDEO ZONE

            if ($media->play_time > 59) {
                $times = sprintf("%02d:%02d", ($media->play_time / 60), ($media->play_time % 60));
            } else {
                $times = sprintf("00:%02d", $media->play_time);
            }
            $media_end_date = $media->date_flag ? ($media->pls_end_date < $media->end_date ? $media->pls_end_date : $media->end_date) : $media->pls_end_date;
            $xml .= sprintf(
                '<Resource id="%d" name="%s" fid="%d" size="%d" signature="%s"
								sw110Signature="%s" transmode="%d" startdate="%s" enddate="%s" duration="00:%s"
								transittime="0.5" mode="%d" reload="%d" replacable="%d">',
                $media->id,
                //htmlspecialchars($media->name, ENT_XML1, 'UTF-8'),
                $this->safInputs($media->name),
                $media->id,
                $media->file_size,
                $media->signature,
                $media->signature,
                $media->transmode >= 0 ? $transmodemapping[$media->transmode] : 0,
                $media->date_flag ? $media->start_date : $media->pls_start_date,
                $media_end_date, //$media->date_flag ? $media->end_date : "",
                $times,
                0,
                $media->reload,
                (isset($media->replacable) ? $media->replacable : 0)
            ) . $this->sep;

            $xml .= sprintf('<URL>%s</URL>', $media->full_path) . $this->sep;
            $xml .= '</Resource>' . $this->sep;
        }

        $xml .= '</AreaList>' . $this->sep;
        $xml .= '</Programme>' . $this->sep;
        $xml .= '</SignwayPoster>' . chr(10);
        $md5 = md5($xml);

        $cachedPath = $this->config->item('cached_temp_path');
        if (!file_exists($cachedPath)) {
            mkdir($cachedPath, 0777, true);
        }


        $playlist_path =  $cachedPath . $fid . '.PLS';

        saveFile($playlist_path, $xml);

        $data = array('md5' => $md5, 'size' => filesize($playlist_path));
        return $data;
    }

    private function des_encrypt($str, $key)
    {
        /*		$block = mcrypt_get_block_size('des', 'ecb');
            $pad = $block - (strlen($str) % $block);

            $str .= str_repeat(chr($pad), $pad);

            return  mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
        */
        $ostr = openssl_encrypt($str, "DES-ECB", $key);

        return $ostr;
    }
    private function des_decrypt($str, $key)
    {
        return openssl_decrypt($str, "DES-ECB", $key, OPENSSL_RAW_DATA, '');
    }

    private function convertUrlQuery($url)
    {
        $key = 'MI;A4@6!';
        $arr = parse_url($url);
        $paramstr = $arr['query'];


        if (!strstr($paramstr, "sn=")) {
            $paramstr = $this->des_decrypt(base64_decode($paramstr), $key);
        }

        parse_str($paramstr, $params);
        /*

        $queryParts = explode('&', $paramstr);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            if (isset($item[1]) && $item[1]) {
                $params[$item[0]] = $item[1];
            }
        }
        */
        return $params;
    }
    public function downloadHdmicfg()
    {
        set_status_header(404, 'NO Update PLS');
    }
    public function downloadSSPList()
    {
        $this->load->model('device');
        $redis = new Redis();
        $host = $this->config->item('redis_server') ?: '127.0.0.1';
        $port = $this->config->item('redis_port') ?: 6379;
        $redis->connect($host, $port);

        $password = $this->config->item('redis_password');
        if ($password) {
            if (!$redis->auth($password)) {
                //log_message('error', 'Redis authentication failed');
                set_status_header(404, "Redis authentication failed");
                return;
            }
        }

        $redis_key = "icatapisever_database_ssp:" . $this->player->sn;
        $current_medium = $redis->get($redis_key);

        //log_message('error', 'redishost:' . $host . ' port:' . $port . ' key:' . $redis_key . ' value:' . $current_medium);
        if ($current_medium) {
            $current = json_decode($current_medium);
            $find_media = false;
            if (!isset($current->media)) {
                set_status_header(404);
                return;
            }

            foreach ($current->media as $medium) {
                $oritation = ($medium->width > $medium->height) ? 1 : 0;
                if ($oritation == $this->player->screen_oritation) {
                    $find_media = true;
                    break;
                }
            }

            if (!$find_media) {
                set_status_header(404, 'NO Media');

                return;
            }
            /*
             $client = new Client();
             $response = $client->head($medium->src);
             $length =  $response->getHeader('Content-Length');

             if ($length) {
                 $length = $length[0];
             }
             */



            $height = $this->player->screen_oritation ? "1080" : "1920";
            $width = $this->player->screen_oritation ? "1920" : "1080";

            $array = [
                "Templatename" => $this->player->screen_oritation ? "portraint" : "portraint",
                'ScreenType' => [
                    '_attributes' => ["height" => $height, "width" => $width, "rotation" => "0"],
                ],
                'Programme' => [
                    "_attributes" => ["id" => "2cf8cda0-ad92-cd4f-b706-6a225dc09f8c", "name" => "2cf8cda0-ad92-cd4f-b706-6a225dc09f8c"],
                    "Area" => [
                        "_attributes" => ["id" => "101", "name" => "Movie/Photo", "model" => "0", "left" => "0.00%", "top" => "0.00%", "width" => "100.00%", "height" => "100.00%", "zindex" => "10"],
                        "AreaList" => [
                            '_attributes' => ["id" => "101", "playtime" => $current->duration],
                        ],
                    ],

                ],

            ];

            $root = [
                'rootElementName' => 'SignwayPoster',
                "_attributes" => ['type' => "playlist", "version" => "1.0.1"],
            ];

            $index = 0;
            foreach ($current->media as $medium) {
                $oritation = ($medium->width > $medium->height) ? 1 : 0;
                if ($oritation == $this->player->screen_oritation) {
                    $find_media = true;
                    $mid = md5($medium->src);
                    $impressions = array();
                    if ($current->impression && is_array($current->impression)) {
                        foreach ($current->impression as $imp) {
                            $impressions[] = ['_cdata' => $imp];
                        }
                    }
                    $starts = array();
                    if ($current->tracking->start && is_array($current->tracking->start)) {
                        foreach ($current->tracking->start as $start) {
                            $starts[] =  ['_cdata' => $start];;
                        }
                    }
                    $completes = array();
                    if ($current->tracking->complete && is_array($current->tracking->complete)) {
                        foreach ($current->tracking->complete as $com) {
                            $completes[] =  ['_cdata' => $com];
                        }
                    }

                    $resource = [
                        "_attributes" => [
                            "id" => $mid,
                            "name" => basename($medium->src),
                            "fid" => $mid,
                            "size" => "",
                            "signature" => "",
                            "sw110Signature" => "",
                            "transmode" => "31",
                            "startdate" => "",
                            "enddate" => "",
                            "duration" => $current->duration
                        ],
                        'URL' => [
                            '_cdata' => "$medium->src"
                        ],
                        "API" => [

                            "impression" => ['url' => $impressions],

                            //"impression"=>$current->impression,
                            "start" => ['url' => $starts],

                            "complete" => ['url' => $completes],

                        ],
                    ];
                    $array['Programme']['Area']['AreaList']["__custom:Resource:$index++"] = $resource;
                }
            }


            $arrayToXml = new ArrayToXml($array, $root, false, "UTF-8");


            $result = $arrayToXml->prettify()->toXml();
            $this->load->helper('file');

            if (np_download(NULL, $result, 'ssp.xml')) {

                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloading SSP Playlist.");
            }
            return;
        }

        set_status_header(404);
    }
    public function getExceptionPowerRecord()
    {
        $data = file_get_contents('php://input', 'r');

        $lines = explode(',', $data);
        foreach ($lines as $line) {
            $times = explode('/', $line);
            $item['player_id'] = $this->player->id;
            $item['off_at'] = $times[0];
            $item['on_at'] = $times[1];
            $this->device->save_power_record($item);
        }
    }

    private function safInputs($string)
    {
        return preg_replace('/[^\w\-_. ]/', '_', $string);
    }



    private function downloadPlaylistNew($playlists, $type = 0)
    {
        $this->load->helper('week');
        $this->load->helper('date');
        $this->load->model('program');
        $sch = '<?xml version="1.0" encoding="UTF-8"?>' . $this->sep;
        $sch .= '<Schedule>' . $this->sep;


        foreach ($playlists as $pl) {


            $playlist_path = $this->config->item('playlist_publish_path') . $pl->company_id . '/' . $pl->id . '.PLS';

            if (!file_exists($playlist_path)) {
                continue;
            }

            $template = $this->program->get_template($pl->template_id);


            if ($template->w > $template->h) {
                $sense = 1;
            } else {
                $sense = 2;
            }

            if (isset($pl->pls_custom_type) && $pl->pls_custom_type == 1) {
                $sch .= sprintf('<Programme name="%d.PLS" id="%d" fid="%d" type="%d" sense="%d" model="3" CustomType="offline">', $pl->id, $pl->id,  $pl->id, $type, $sense) . $this->sep;
            } else {
                $sch .= sprintf('<Programme name="%d.PLS" id="%d" fid="%d" type="%d" sense="%d" model="3">', $pl->id, $pl->id,  $pl->id, $type, $sense) . $this->sep;
            }

            $sch .= sprintf('<PublishTime>%s</PublishTime>', date("Y-m-d H:i:s")) . $this->sep;

            if ($type == 0) {
                $sch .= sprintf('<action>%d</action>', 0) . $this->sep;
                $sch .= sprintf('<URL>resources/publish/%d/%d.PLS</URL>', $pl->company_id, $pl->id) . $this->sep;


                $sch .= '<Dates>' . $this->sep;
                $sch .= '<Date>' . $this->sep;

                $sch .= sprintf('<StartDate>%s</StartDate>', $pl->start_date) . $this->sep;
                $sch .= sprintf('<EndDate>%s</EndDate>', date("Y-m-d", strtotime($pl->end_date . " +1 day"))) . $this->sep;

                if (!$pl->time_flag) {
                    $sch .= sprintf('<StartTime>%02d:00</StartTime>', $pl->start_timeH) . $this->sep;
                    $sch .= sprintf('<EndTime>%02d:00</EndTime>', $pl->end_timeH) . $this->sep;
                } else {
                    $sch .= '<StartTime>00:00</StartTime>' . $this->sep;
                    $sch .= '<EndTime>24:00</EndTime>' . $this->sep;
                }

                $sch .= '</Date>' . $this->sep;
                $sch .= '</Dates>' . $this->sep;


                if ($pl->week != 127) {
                    $sch .= '<Weeks>' . $this->sep;
                    $sch .= '<Week>' . $this->sep;
                    $sch .= '<WeekCode>';
                    $sch .= parseWeeks($pl->week);
                    $sch .= '</WeekCode>' . $this->sep;
                    $sch .= '</Week>' . $this->sep;
                    $sch .= '</Weeks>' . $this->sep;
                }
            }
            $publish_path = $this->config->item('playlist_publish_path');
            $publish_path .= $pl->company_id . '/' . $pl->id . '.PLS';
            $sch .= sprintf('<Size>%d</Size>', $pl->file_size) . $this->sep;
            $sch .= sprintf('<Signature>%s</Signature>', $pl->signature) . $this->sep;
            $sch .= '</Programme>' . $this->sep;
        }



        $sch1 = $sch . '</Schedule>';
        $md51 = md5($sch1);
        $sch = $sch . '</Schedule><!--' . $md51 . '-->' . $this->sep;
        $this->load->helper('file');
        return np_download(NULL, $sch, $type == 0 ? "Playlist.sch" : "Touch.sch");
        // return downloadContent($sch, $type == 0 ? "Playlist.sch" : "Touch.sch");
    }

    public function downloadSysFirmSoft()
    {
        if (!isset($this->params['ver'])) {
            set_status_header(400, 'request param error!');
            return;
        }
        $ver = $this->params['ver'];
        if ($ver === false) {
            set_status_header(400, 'request param error!');
            return;
        }
        $this->load->model('material');
        $this->load->model('device');


        $version = $this->player->upgrade_firmware_version;
        $downloadType = $this->params['downloadType'];

        if (!empty($version)) {
            if ($ver == $version || $version == $this->player->firmver) {
                $this->device->remove_firmware_upgrade_version($this->sn);
                set_status_header(404, 'target firmware [' . $version . '], current version [' . $this->player->firmver . ']!');
                return;
            }

            $this->load->model('firmware');
            $firmware = $this->firmware->get_firmware_by_version($version);
            if ($firmware) {
                $this->load->helper('file');
                $this->device->update_status($this->sn, 3);
                $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] downloads new firmware,  version[" . $version . "]");
                //$this->device->remove_firmware_upgrade_version($this->sn);
                if ($downloadType == 1) {
                    $path = 'http://' . $_SERVER['HTTP_HOST'] . substr($firmware->path, 1);
                    $res = array('name' => $firmware->name, 'version' => $firmware->version, 'path' => "$path");
                    echo json_encode($res, JSON_UNESCAPED_SLASHES);
                } else {

                    np_download($firmware->path);
                }


                return;
            } else {
                // $this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] fails to download software version[$version]. Not found!");
            }
            //$this->device->add_player_log($this->player->id, $this->config->item('event_type_download'), "Player[" . $this->player->name . "] has the latest software version[$ver] vs. [$version]");
        }


        set_status_header(404, 'target firmware [' . $version . '], current version [' . $ver . ']!');
    }
}
