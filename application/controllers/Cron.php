<?php

use JsonMachine\JsonDecoder\PassThruDecoder;
use JsonMachine\Items;

class Cron extends CI_Controller
{

    /**
     *
     * 执行rss更新
     * @return
     */
    public function rss()
    {
        //*/10 	* 	* 	* 	* /usr/local/bin/php -f /home/tochenca/public_html/cms/np100/cli.php cron rss
        set_time_limit(-1);
        $this->load->library('rssparser');
        $this->load->model('material');

        $rss_list = $this->material->get_all_update_rss_list();
        if (!empty($rss_list)) {
            foreach ($rss_list as $r) {
                $rssObj = $this->rssparser->Get($r->url, false, false);
                if ($rssObj) {
                    $this->material->update_rss_last($r->id);
                }
            }
        }
    }

    /**
     *
     * 执行客户机离线处理
     *
     * @return
     */
    public function offline()
    {
        $this->load->model('device');


        $this->load->model('membership');

        $time = date("Y-m-d H:i:s", time() - 600);
        $players = $this->device->update_status_offline($time);
    }

    public function email()
    {
        $this->load->model('membership');
        $this->load->model('device');
        $companys = $this->membership->get_all_company_list_with_emails();



        //$this->load->helper('chrome_logger');

        $this->load->library('mailer');
        $this->lang->load('common');
        $this->lang->load('player');
        $this->load->helper('language');
        $this->load->helper('serial');



        foreach ($companys as $c) {
            $data = array();
            $data['company_name'] = $c->name;


            //$language = $c->lang;
            $language = 1;
            if ($language == 1) {
                $this->cur_lang = "germany";
            } else {
                $this->cur_lang = "english";
            }

            $this->config->set_item('language', $this->cur_lang);
            $this->lang->load('email');

            if ($c->offline_email_flag && $c->emails1 !== null) {
                if ((strtotime("now") - $c->offline_email_last_run) / 60 >= $c->offline_email_inteval) {
                    $chkpoint = time() - $c->offline_email_inteval * 60;
                    $end = date("Y-m-d H:i:s", $chkpoint);
                    $start = date("Y-m-d H:i:s", $chkpoint - $c->offline_email_inteval * 60);
                    $players = $this->device->get_disconnected_players($c->id, $start, $end);


                    if ($players) {
                        $data['players'] = $players;
                        $content = $this->load->view('email/offline', $data, true);
                        $to_emails = explode(',', $c->emails1);
                        $this->mailer->sendmail($to_emails, '', $this->lang->line('email.player.offline.title'), $content);
                    }

                    $this->membership->update_company(array('offline_email_last_run' => strtotime('now')), $c->id);
                }
            }
            if ($c->offline_email_flag2 && $c->emails2 !== null) {
                if ((strtotime("now") - $c->offline_email_last_run2) / 60 >= $c->offline_email_inteval2) {
                    $chkpoint = time() - $c->offline_email_inteval2 * 60;
                    $end = date("Y-m-d H:i:s", $chkpoint);
                    $start = date("Y-m-d H:i:s", $chkpoint - $c->offline_email_inteval * 60);
                    $players = $this->device->get_disconnected_players($c->id, $start, $end);


                    if ($players) {
                        $data['players'] = $players;
                        $content = $this->load->view('email/offline', $data, true);
                        $to_emails = explode(',', $c->emails2);
                        $this->mailer->sendmail($to_emails, '', $this->lang->line('email.player.offline.title'), $content);
                    }

                    $this->membership->update_company(array('offline_email_last_run2' => strtotime('now')), $c->id);
                }
            }
        }
    }

    /**
     *
     * company 到期提示
     *
     * @return
     */
    public function company_date_check()
    {
        $email_to = array();
        $company_name = array();
        $end = array();
        $this->load->library('smtp');
        $this->load->model('membership');
        $company = $this->membership->get_all_company_list();
        foreach ($company as $c) {
            $stop_date = strtotime($c->stop_date);
            $days = round(($stop_date - time()) / 3600 / 24);
            if ($days <= 7 && $days >= 0) {
                $company_name[] = $c->name;
                $end[] = $c->stop_date;
            }
        }

        if (count($company_name)) {
            $data = array();
            $data['company_name'] = $company_name;
            $data['end'] = $end;
            $content = $this->load->view('email/company_out_date', $data, true);
            $this->load->library('mailer');
            $this->mailer->sendmail('tochenca@yahoo.com', '', 'License will soon expire', $content);
        }
    }



    /* delete
    private function send_mail($email_to, $subject, $message){
        if ($email_to) {
            //mail($to, $subject, $content, $header);
            //$message=ereg_replace("(^|(\r\n))(\.)", "\1.\3", $message);
            $message = preg_replace("/(^|(\r\n))(\.)/", "\1.\3", $message);
            $from_name = $this->config->item('email.from_name');
            $from_mail = $this->config->item('email.from_mail');
            $smtpserver = $this->config->item('email.smtp_server');
            $replyto = $this->config->item('email.reply_to');
            $password=$this->config->item('email.password');
            $serverport = $this->config->item('email.smtp_port');
            $smtp = new SMTP();
            $smtp->do_debug=0;
            if($smtp->Connect($smtpserver, $serverport)){
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
    }*/

    /**
     * 如果DST有设定，动态获取DST开始日期：
     * 美国：三月的第二个星期日到11月的第一个星期日
     * 德国：三月的最后一个星期日到10月的最后一个星期日
     */
    public function update_dst()
    {
        $this->load->model('membership');
        $this->membership->update_company_auto_dst();
    }

    /**
     * android终端控制
     * 查询表cat_player
     *
     * 目前只有重启命令
     * 重启条件：player_type=1 and reboot_flag=1
     *
     */
    public function control()
    {
        $this->load->library('utils');
        $this->load->model('device');
        $utils = new Utils();
        $client = new swoole_client(SWOOLE_SOCK_TCP);
        if (!$client->connect('148.251.126.77', 4702)) {
            exit("connect fail\n");
        }
        $players = $this->device->get_all_reboot_flag();
        foreach ($players as $player) {
            $c_type = 0x01; //重启
            $data = pack('Ca4Ca8CC', 0x00, '1234', 0x08, $player->sn, $c_type, 0x00);

            //blowfish 加密DATA数据
            $encdata = $utils->blowfish_enc($data);
            $length = strlen($encdata);
            printf("length=%d\n", $length);
            $header = pack('C4', 0xec, 0xeb, 0x03, $length);

            //CRC校验
            $msg = $header . $encdata;
            $crc = $utils->crc16($msg);

            //拼装数据包
            $loginmsg = $msg . pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff));
            //发送登录成功/失败消息
            $client->send($loginmsg);
        }
    }

    //修改company下所有schedule的发布时间为当前时间
    public function auto_mod_publishTime()
    {
        $this->load->model('membership');
        $this->membership->auto_publish_schedule();
    }

    //定时清除cat_player_log
    public function deleteLog()
    {
        $this->load->model('device');
        $this->device->delete_data_everyWeek();
    }

    public function test()
    {
        $file = '/home/miatek/np100/application/controllers/crontab.text';
        $date = date("Y-m-d H:i:s");
        file_put_contents($file, '\r\n' . $date, FILE_APPEND);
    }

    public function list_resources()
    {
        $this->load->model('membership');
        $company_list = $this->membership->get_all_company_list();
        foreach ($company_list as $company) {
            $this->get_filetree('/home/miatek/np200/resources/' . $company->id);
        }
    }

    public function get_filetree($path)
    {
        echo "path: " . $path . "<br/>";

        $tree = array();
        foreach (glob($path . '/*') as $single) {
            if (!is_dir($single) && !strstr($single, "video_")) {
                //$tree[] = $single;
                $arr_single = explode("/", $single);
                $this->load->model('program');
                $m_flag = $this->program->get_media_by_resource_name($arr_single[6]);
                if (!$m_flag) {
                    $tree[] = $single;
                }
            }
        }
        echo '<pre>';
        print_r($tree);
        echo '</pre>';
    }

    //删除logs文件夹下的文件
    public function delete_logs()
    {
        $this->get_delete_filetree('/home/miatek/np200/logs');
    }

    public function get_delete_filetree($path)
    {
        $tree = array();
        foreach (glob($path . '/*') as $single) {
            if (!is_dir($single)) {
                $arr_single = explode("/", $single);
                $last_month = date("Y-m-d H:i:s", strtotime("-1 month"));
                $time = filemtime($single);
                if (date("Y-m-d H:i:s", $time) <= $last_month) {
                    $tree[] = $single;
                    unlink('.' . substr($single, 18, strlen($single)));
                }
            }
        }
    }

    public function offline_email()
    {
        //*/2 	* 	* 	* 	* /usr/local/bin/php -f /home/tochenca/public_html/cms/np100/cli.php cron offline
        //设置邮件语言
        if ($this->config->item('new_offline_email')) {
            $this->load->model('membership');

            $companys = $this->membership->get_offline_company_list();

            if (!$companys) {
                return;
            }
            $this->load->model('device');
            foreach ($companys as $company) {
                $now = date("Y-m-d H:i:s");

                /*
                echo "</br>";
                echo "Company name:".$company->company_name.PHP_EOL;
                echo "offline flag=".$company->offline_email_flag.PHP_EOL;
                echo "last run = ".date("Y-m-d H:i:s", $company->offline_email_last_run).PHP_EOL;
                echo "interval=".$company->offline_email_inteval.PHP_EOL;
                echo "value=".(time()-$company->offline_email_last_run)/60;

                $this->debuglog("Company name:".$company->company_name.",last run = ".date("Y-m-d H:i:s", $company->offline_email_last_run).",value=(time()-$company->offline_email_last_run)/60".PHP_EOL);
                */

                if ($company->offline_email_flag && ($company->offline_email_last_run == 0) || ((time() - $company->offline_email_last_run) / 60 > $company->offline_email_inteval)) {
                    if ($company->offline_email_last_run == 0) {
                        $company->offline_email_last_run = strtotime("-$company->offline_email_inteval minute");
                    }

                    $dst_on = 0;
                    if ($now >= $company->dst_start && $now < $company->dst_end) {
                        $dst_on = 1;
                    }
                    $players = $this->device->check_offline_email_players($company->id, date("Y-m-d H:i:s", $company->offline_email_last_run), date("Y-m-d H:i:s"), $dst_on);



                    if ($players) {
                        $this->cur_lang = "germany";
                        //$this->cur_lang = "english";
                        $this->config->set_item('language', $this->cur_lang);


                        $this->load->library('mailer');
                        $this->lang->load('common');
                        $this->lang->load('player');
                        $this->lang->load('email');

                        $this->load->helper('language');
                        $this->load->helper('serial');

                        $data = array();
                        $data['company_name'] = $company->company_name;
                        var_dump($players);


                        $data['players'] = $players;
                        $content = $this->load->view('email/offline', $data, true);

                        $email_ary1 = explode(',', $company->useremail1);
                        var_dump($email_ary1);
                        echo $content . PHP_EOL;
                        if (!empty($email_ary1)) {
                            $ret = $this->mailer->sendmail($email_ary1, '', $this->lang->line('email.player.offline.title'), $content);
                            echo "sendemail ret = " . $ret . PHP_EOL;
                        }
                    }

                    $this->membership->update_company(array('offline_email_last_run' => time()), $company->id);
                } elseif ($company->offline_email_flag2 && ((time() - $company->offline_email_last_run2) / 60 > $company->offline_email_inteval2)) {
                    if ($company->offline_email_last_run2 == 0) {
                        $company->offline_email_last_run2 = strtotime("-$company->offline_email_inteval2 minute");
                    }

                    $dst_on = 0;
                    if ($now >= $company->dst_start && $now < $company->dst_end) {
                        $dst_on = 1;
                    }
                    $players = $this->device->check_offline_email_players($company->id, date("Y-m-d H:i:s", $company->offline_email_last_run2), date("Y-m-d H:i:s"), $dst_on);


                    if ($players) {
                        $this->cur_lang = "germany";
                        //$this->cur_lang = "english";
                        $this->config->set_item('language', $this->cur_lang);


                        $this->load->library('mailer');
                        $this->lang->load('common');
                        $this->lang->load('player');
                        $this->lang->load('email');

                        $this->load->helper('language');
                        $this->load->helper('serial');

                        $data = array();
                        $data['company_name'] = $company->company_name;


                        $data['players'] = $players;
                        $content = $this->load->view('email/offline', $data, true);

                        $email_ary2 = explode(',', $company->useremail2);

                        if (!empty($email_ary2)) {
                            $this->mailer->sendmail($email_ary2, '', $this->lang->line('email.player.offline.title'), $content);
                        }
                    }

                    $this->membership->update_company(array('offline_email_last_run2' => time()), $company->id);
                }
            }
        }
    }

    /********************************UAM XML NEWS**************************************************/

    private function image_multiline_text($image, $size, $xpos, $ypos, $color, $font, $text, $max_width, $next_line = '')
    {
        $split = explode(" ", $text);


        $string = "";
        $new_string = "";
        $box = imagettfbbox($size, 0, $font, $text[0]);
        $ypos += $box[3] - $box[5];

        $box = imagettfbbox($size, 0, $font, $text);
        $text_height = $box[3] - $box[5];
        $line_height = $text_height;


        for ($i = 0; $i < count($split); $i++) {
            $new_string .= $split[$i] . " ";
            //chrome_log($new_string);

            // check size of string
            $tbbox = imagettfbbox($size, 0, $font, $new_string);
            if (($tbbox[4]) <= $max_width) {
                $string = $new_string;
                //chrome_log($string);
            } else {
                if ($i > 0) {
                    $i--;
                } else {
                    $string = $new_string;
                }
                $new_string = "";

                $tb = imagettftext($image, $size, 0, $xpos, $ypos, $color, $font, $string);
                /*
                 $height =  $tb[3]-$tb[5];
                 //chrome_log("height=".$height);

                 if($height < $text_height){
                 $ypos += $height;
                 }
                 */
                $string = "";

                $ypos = $tb[3] + $line_height; // change this to adjust line-height.
            }
        }
        imagettftext($image, $size, 0, $xpos, $ypos, $color, $font, $string); // "draws" the rest of the string
        return $ypos;
    }


    private function generate_bg($outpath, $title, $desc, $isPortrait = true)
    {
        //90pt = 71 ponds

        $font_file_title = 'fonts/TheSans-8ExtraBold.ttf';
        $font_file_desc = 'fonts/TheSans-6SemiBold.ttf';

        if ($isPortrait) {
            $bg_path = './images/news/TS-STROEER-9-16-HD.jpg';

            $xstart = 50;
            $ystart = 900;
            $fontsize_title = 60; //64;
            $fontsize_desc = 37;
        } else {
            $bg_path = './images/news/TS-STROEER-16-9-HD.jpg';
            $xstart = 840;
            $ystart = 318;

            $fontsize_title = 65; //71;
            $fontsize_desc = 37; //47;
        }
        $max_width = 980;

        $img = imagecreatefromjpeg($bg_path);

        $font_color = imagecolorallocate($img, 0xFF, 0xFF, 0xFF);

        $title = $title;
        $desc = $desc;

        $ypos = $this->image_multiline_text($img, $fontsize_title, $xstart, $ystart, $font_color, $font_file_title, $title, $max_width);
        $ypos += 64;
        $this->image_multiline_text($img, $fontsize_desc, $xstart, $ypos, $font_color, $font_file_desc, $desc, $max_width);

        imagejpeg($img, $outpath);
    }


    private function parse_news_xml()
    {
        set_time_limit(600);

        $client = new GuzzleHttp\Client();

        $cached_path = $this->config->item('cached_temp_path');
        $xmlpath = $cached_path . "news.xml";
        $newspath = './news';

        if (!file_exists($cached_path)) {
            mkdir($cached_path, 0744, true);
        }

        if (!file_exists($newspath)) {
            mkdir($newspath, 0744, true);
        }


        $last_fetched = 0;


        $res = $client->get(
            'http://tagesschau.de/templates/pages/export/iscreen.xml'
            //	['headers' => ['If-Modified-Since' => $last_fetched]]
        );


        $channels = array();
        if ($res->getStatusCode() != 200) {
            return;
        }
        if ($res->hasHeader('Date')) {
            $last_fetched = $res->getHeader('Date')[0];
        }

        file_put_contents($xmlpath, $res->getBody()->getContents());


        $this->load->model('xmlnews');




        if (!file_exists($xmlpath)) {
            chrome_log("The new feed does not exist:" . $xmlpath);
            return;
        }
        $xml = simplexml_load_file($xmlpath) or die("Unable to open file");

        foreach ($xml->channel->item as $newsitem) {
            $news = array();
            $news['title'] = (string)$newsitem->title;
            $news['description'] = (string)$newsitem->description;

            foreach ($newsitem->enclosure->attributes() as $k => $v) {
                if ($k == "type") {
                    if ($v == "video/mp4") {
                        $news['type'] = 1;
                    } else {
                        $news['type'] = 0;
                    }
                } elseif ($k == "url") {
                    $news['url'] = (string)$v;
                }
            }
            $channels[] = $news;
        }
        //chrome_log($channels);



        $index = 1;
        $ffmpegPath = $this->config->item('ffmpeg');

        foreach ($channels as $news) {
            $url = $news['url'];
            $pathinfo = pathinfo($url);
            if (is_array($pathinfo)) {
                $downloadname = $cached_path . $pathinfo['basename'];
            } else {
                break;
            }


            $res = $client->request('GET', $url, ['sink' => $downloadname]);

            //chrome_log($res->getStatusCode());


            if ($res->getStatusCode() != 200) {
            } else {
                //prepare bg files with title&desc
                $bgpath = $cached_path . 'news_bg.jpg'; //'/tmp/news_bg.jpg';

                $this->generate_bg($bgpath, $news['title'], $news['description']);

                //convert fullscreen vdieo files
                //landsacpe
                // $scale = "788:443";
                // $overlay="0:318";
                //portraint
                $scale = "985:550";
                $overlay = "50:294";

                $itemname = $cached_path . "news" . $index . ".tmp.mp4";

                if ($news['type'] != 1) {
                    $itemname = $newspath . "/news" . $index . ".mp4";
                    $pic_tmp = $cached_path . "pic_tmp.mp4";
                    $command = $ffmpegPath . ' -threads 8 -loop 1 -y -i ' . $downloadname . '  -c:v libx264 -t 00:00:10.000 -an -pix_fmt yuv420p ' . $pic_tmp;
                    //$command=$ffmpegPath.' -loop 1 -y -i '.$bgpath.' -i '.$downloadname.' -filter_complex '."[1:v]scale='$scale'[fg];[0:v][fg]overlay='$overlay':shortest=1 ".$itemname.' 2>&1';
                    exec($command, $output, $return);

                    @unlink($$downloadname);
                    $downloadname = $pic_tmp;
                }

                $command = $ffmpegPath . ' -threads 8 -loop 1 -y -i ' . $bgpath . ' -i  ' . $downloadname . ' -filter_complex "' . '[1:v]scale=' . $scale . '[fg];[0:v][fg]overlay=' . $overlay . ':shortest=1" -t 00:00:10.000 -an ' . $itemname . ' 2>&1';


                exec($command, $output, $return);

                if ($return == 0) {
                    $destPath = $newspath . "/news" . $index . ".mp4";
                    rename($itemname, $destPath);
                    @unlink($itemname);
                }
                @unlink($downloadname);
            }
            $index++;
        }
    }

    public function update_news()
    {
        if ($this->config->item('xml_news_support')) {
            $this->load->helper('chrome_logger');

            $this->load->model('xmlnews');

            $news = $this->xmlnews->get_news_settings();

            if (!$news) {
                return;
            }

            if ($news->last_pub_time == 0 || (time() - $news->last_pub_time) / 3600 > $news->check_interval) {
                $this->parse_news_xml();
                //update xml news
                $data = array();

                $data['last_pub_time'] = time();

                $this->xmlnews->update_news_settings($data);
                $this->load->model('program');
                $this->program->update_campaigns_with_reload();
            }
        }
    }
    public function parse_weather()
    {
        if (!$this->config->item('has_weather')) {
            return;
        }

        $this->lang->load('weather');
        $apiformat = "https://api.openweathermap.org/data/2.5/forecast?zip=%s&units=%s&appid=%s";

        $this->load->model('membership');
        $this->load->model('device');
        $this->load->model('weathers');
        $this->load->model('material');
        $this->load->model('program');
        $this->load->helper('file');
        $fvideopath = '/home/icat/public_html/news';

        if (!file_exists($fvideopath)) {
            mkdir($fvideopath, 0744, true);
        }

        $ffmpegPath = $this->config->item('ffmpeg');

        $bg_file = "./assets/weathers/bg_default.mp4";
        $movie_zone = array('flag' => false, 'x' => 0, 'y' => 0, 'w' => 0, 'h' => 0, 'path' => "./assets/weathers/defaultvideo.mp4");
        $day1_zone = array('flag' => false, 'x' => 0, 'y' => 0, 'w' => 0, 'h' => 0);
        $day2_zone = array('flag' => false, 'x' => 0, 'y' => 0, 'w' => 0, 'h' => 0);
        $day3_zone = array('flag' => false, 'x' => 0, 'y' => 0, 'w' => 0, 'h' => 0);

        $today = date("Y-m-d");
        $date1 = date("Y-m-d", strtotime($today . " + 1 day"));
        $date2 = date("Y-m-d", strtotime($today . " + 2 day"));
        $date3 = date("Y-m-d", strtotime($today . " + 3 day"));

        $companys = $this->membership->get_all_company_list();

        foreach ($companys as $company) {
            $cid = $company->id;
            $active_template = $this->weathers->get_active_template($cid);


            if ($active_template == false) {
                //log_message('debug',"no active template on company:".$company->name);
                echo "no active template on company:" . $company->name . PHP_EOL;
                continue;
            }

            $zipcodes = $this->device->get_all_zipcode($cid);


            if ($zipcodes == false) {
                continue;
            }

            $areas = $this->weathers->get_template_area($active_template->id);

            if ($areas == false) {
                log_message('debug', "no template areas!");
                continue;
            }

            foreach ($areas as $area) {

                //Movie
                if ((int)$area->area_type == 8) {
                    $movie_zone['flag'] = true;
                    $movie_zone['x'] = $area->x * 2;
                    $movie_zone['y'] = $area->y * 2;
                    $movie_zone['w'] = $area->w * 2;
                    $movie_zone['h'] = $area->h * 2;
                    $wel_video = $this->material->get_media($area->mid);
                    if ($wel_video) {
                        $movie_zone['path'] = $wel_video->full_path;
                    }
                } elseif ((int)$area->area_type == 9) {
                    $bg = $this->material->get_media($area->mid);
                    if ($bg) {
                        $out_file = "./assets/weathers/bg_tmp.mp4";
                        $input = $bg->full_path;

                        $command = $ffmpegPath . " -threads 4 -loop 1 -y -i " . $input . " -c:v libx264 -t 00:00:10.000 -s 1080*1920 -pix_fmt yuv420p -an " . $out_file;
                        @exec($command, $output, $return);

                        if ($return == 0) {
                            $bg_file = $out_file;
                        } else {
                            log_message("error", "failed convert bg");
                        }
                    }
                } elseif ((int)$area->area_type == $this->config->item('area_type_image')) {
                    $day1_zone['flag'] = true;
                    $day1_zone['x'] = $area->x * 2;
                    $day1_zone['y'] = $area->y * 2;
                    $day1_zone['w'] = $area->w * 2;
                    $day1_zone['h'] = $area->h * 2;
                } elseif ((int)$area->area_type == $this->config->item('area_type_image2')) {
                    $day2_zone['flag'] = true;
                    $day2_zone['x'] = $area->x * 2;
                    $day2_zone['y'] = $area->y * 2;
                    $day2_zone['w'] = $area->w * 2;
                    $day2_zone['h'] = $area->h * 2;
                } elseif ((int)$area->area_type == $this->config->item('area_type_image3')) {
                    $day3_zone['flag'] = true;
                    $day3_zone['x'] = $area->x * 2;
                    $day3_zone['y'] = $area->y * 2;
                    $day3_zone['w'] = $area->w * 2;
                    $day3_zone['h'] = $area->h * 2;
                }
            }

            //$appid = "57539b2d549a0536e17044d73c09109d";
            $appid = "df944703b285064a3b8193c6c7ff3d62"; //key for dig


            foreach ($zipcodes as $zipcode) {
                $player_zcode = $zipcode->zipcode;

                $zip = $player_zcode . ",DE";

                $unit = "metric";
                $url = sprintf($apiformat, $zip, $unit, $appid);

                $opts  = array('http' => array('method' => "GET", 'timeout' => 30));
                $context  = stream_context_create($opts);

                $jsonstr = @file_get_contents($url, false, $context);


                $day1 = array('max' => -100, 'condition' => "N/A", 'min' => -100);
                $day2 = array('max' => -100, 'condition' => "N/A", 'min' => -100);
                $day3 = array('max' => -100, 'condition' => "N/A", 'min' => -100);

                if ($jsonstr) {
                    $winfo = json_decode($jsonstr);

                    if ($winfo) {
                        for ($index = 0; $index < count($winfo->list); $index++) {
                            $item = $winfo->list[$index];
                            if (strstr($item->dt_txt, $date1)) {
                                if ($item->main->temp_max > $day1['max']) {
                                    $day1['max'] = floor($item->main->temp_max) . '°C';
                                    $day1['min'] = $item->main->temp_min . '°C';
                                    $day1['condition'] = $item->weather[0]->main;
                                    $day1['icon'] = $item->weather[0]->icon;
                                    $day1['id'] = $item->weather[0]->id;
                                }
                            } elseif (strstr($item->dt_txt, $date2)) {
                                if ($item->main->temp_max > $day2['max']) {
                                    $day2['max'] = floor($item->main->temp_max) . '°C';
                                    $day2['min'] = $item->main->temp_min . '°C';
                                    $day2['condition'] = $item->weather[0]->main;
                                    $day2['icon'] = $item->weather[0]->icon;
                                    $day2['id'] = $item->weather[0]->id;
                                }
                            } elseif (strstr($item->dt_txt, $date3)) {
                                if ($item->main->temp_max > $day3['max']) {
                                    $day3['max'] = floor($item->main->temp_max) . '°C';
                                    $day3['min'] = $item->main->temp_min . '°C';
                                    $day3['condition'] = $item->weather[0]->main;
                                    $day3['icon'] = $item->weather[0]->icon;
                                    $day3['id'] = $item->weather[0]->id;
                                }
                            }
                        }

                        if ($day1['id'] == 600) {
                            $day1_icon = './assets/weathers/icons/ID600.png';
                        } elseif ($day1['id'] == 616) {
                            $day1_icon = './assets/weathers/icons/ID616.png';
                        } else {
                            $day1_icon = './assets/weathers/icons/' . $day1['icon'] . '.png';
                        }
                        if ($day2['id'] == 600) {
                            $day2_icon = './assets/weathers/icons/ID600.png';
                        } elseif ($day1['id'] == 616) {
                            $day2_icon = './assets/weathers/icons/ID616.png';
                        } else {
                            $day2_icon = './assets/weathers/icons/' . $day2['icon'] . '.png';
                        }
                        if ($day3['id'] == 600) {
                            $day3_icon = './assets/weathers/icons/ID600.png';
                        } elseif ($day3['id'] == 616) {
                            $day3_icon = './assets/weathers/icons/ID616.png';
                        } else {
                            $day3_icon = './assets/weathers/icons/' . $day3['icon'] . '.png';
                        }
                        //$output_path = "./resources/$cid/$player_zcode.mp4";
                        $video_name = $player_zcode . "_" . $company->name . ".mp4";
                        $output_path = $fvideopath . "/" . $video_name;

                        $fontfile = "./fonts/Oswald-Regular.ttf";
                        $fontsz_date = 50;
                        $fontsz_degree = 60;
                        $fontcolor = "black";
                        $showdate1 = $this->get_weekday_str(date("w", strtotime($today . " + 1 day")));
                        $showdate2 = $this->get_weekday_str(date("w", strtotime($today . " + 2 day")));
                        $showdate3 = $this->get_weekday_str(date("w", strtotime($today . " + 3 day")));

                        $iconw = 220;

                        $tbbox = imagettfbbox($fontsz_degree, 0, $fontfile, "'" . $day1['max'] . "'");
                        $degree1_w = abs($tbbox[2] - $tbbox[0]);
                        $degree_h = $tbbox[1] - $tbbox[7];
                        $tbbox = imagettfbbox($fontsz_degree, 0, $fontfile, $day2['max']);
                        $degree2_w = abs($tbbox[2] - $tbbox[0]);
                        $tbbox = imagettfbbox($fontsz_degree, 0, $fontfile, $day3['max']);
                        $degree3_w = abs($tbbox[2] - $tbbox[0]);

                        $day1_offset = 2.5;
                        $day2_offset = 2.8;
                        $day3_offset = 3.0;


                        $command = $ffmpegPath . " -threads 4 -y -i " . $bg_file . " -i " . $movie_zone['path'] . " -itsoffset $day1_offset -i " . $day1_icon . " -itsoffset $day2_offset -i " . $day2_icon . " -itsoffset $day3_offset -i " . $day3_icon . ' -filter_complex "' .
                            "[2:v]scale=w=" . $iconw . ":h=" . $iconw . "[icon1];[3:v]scale=w=" . $iconw . ":h=" . $iconw . "[icon2];[4:v]scale=w=" . $iconw . ":h=" . $iconw . "[icon3];[1:v]scale=" . $movie_zone['w'] . ':' . $movie_zone['h'] . "[mov1],[0:v][mov1]overlay=" . $movie_zone['x'] . ":" . $movie_zone['y'] . "[bkg1];[bkg1][icon1]overlay=" . ($day1_zone['x'] + $day1_zone['w'] / 2 - $iconw / 2) . ":" . ($day1_zone['y'] + $day1_zone['h'] / 2 - $iconw / 2) . ",drawtext=fontfile=$fontfile:text=" . $showdate1 . ":y=" . ($day1_zone['y'] + 20) . ":x=" . ($day1_zone['x'] + 10) . ":fontsize=" . $fontsz_date . ":fontcolor=black:enable=gte(t\,$day1_offset),drawtext=fontfile=$fontfile:text=" . $day1['max'] . ":y=" . ($day1_zone['y'] + $day1_zone['h'] - $degree_h / 2 - 40) . ":x=" . ($day1_zone['x'] + $day1_zone['w'] - $degree1_w) . ":fontsize=" . $fontsz_degree . ":fontcolor=black:enable=gte(t\,$day1_offset)[day1];[day1][icon2]overlay=" . ($day2_zone['x'] + $day2_zone['w'] / 2 - $iconw / 2) . ":" . ($day2_zone['y'] + $day2_zone['h'] / 2 - 110) . ",drawtext=fontfile=$fontfile:text=" . $showdate2 . ":y=" . ($day2_zone['y'] + 20) . ":x=" . ($day2_zone['x'] + 10) . ":fontsize=" . $fontsz_date . ":fontcolor=black:enable=gte(t\,$day2_offset),drawtext=fontfile=$fontfile:text=" . $day2['max'] . ":y=" . ($day2_zone['y'] + $day2_zone['h'] - $degree_h / 2 - 40) . ":x=" . ($day2_zone['x'] + $day2_zone['w'] - $degree2_w) . ":fontsize=" . $fontsz_degree . ":fontcolor=black:enable=gte(t\,$day2_offset)[day2];[day2][icon3]overlay=" . ($day3_zone['x'] + $day3_zone['w'] / 2 - $iconw / 2) . ":" . ($day3_zone['y'] + $day3_zone['h'] / 2 - 110) . ",drawtext=fontfile=$fontfile:text=" . $showdate3 . ":y=" . ($day3_zone['y'] + 20) . ":x=" . ($day3_zone['x'] + 10) . ":fontsize=" . $fontsz_date . ":fontcolor=black:enable=gte(t\,$day3_offset),drawtext=fontfile=" . $fontfile . ":text=" . $day3['max'] . ":y=" . ($day3_zone['y'] + $day3_zone['h'] - $degree_h / 2 - 40) . ":x=" . ($day3_zone['x'] + $day3_zone['w'] - $degree3_w) . ":fontsize=" . $fontsz_degree . ":fontcolor=black:enable=gte(t\,$day3_offset)";




                        $commandextr = '';

                        $command = $command . $commandextr . '" -t 00:00:10.000 -an "' . $output_path . '"';

                        @exec($command, $output, $return);


                        if ($return == 0) {
                            $exist_media = $this->material->get_media_by_name($cid, $video_name);
                            if ($exist_media) {
                                $plsary = $this->material->get_pb_id($exist_media->id);

                                if ($plsary) {
                                    foreach ($plsary as $pl) {
                                        $updates = array('update_time' => date('Y-m-d H:i:s'));
                                        $this->program->update_playlist($updates, $pl->id);
                                    }
                                }
                            }
                        } else {
                            log_message("debug", "failed convert weather:" . $command);
                            continue;
                        }
                    }
                }
            }
        }
    }
    private function get_weekday_str($wd)
    {
        $showdate = "N/A";
        switch ($wd) {
            case 0:
                $showdate = $this->lang->line('sun');
                break;
            case 1:
                $showdate = $this->lang->line('mon');
                break;
            case 2:
                $showdate = $this->lang->line('tue');
                break;
            case 3:
                $showdate = $this->lang->line('wed');
                break;
            case 4:
                $showdate = $this->lang->line('thu');
                break;
            case 5:
                $showdate = $this->lang->line('fri');
                break;
            case 6:
                $showdate = $this->lang->line('sat');
                break;
            default:
                break;
        }
        return $showdate;
    }

    public function dialy_check()
    {
        $this->load->model('program');
        $this->program->update_reserved_campaigns();
    }


    private function debuglog($log)
    {
        if (!is_dir('logs')) {
            mkdir('logs');
        }

        $h = fopen('logs/cron_debuglog.' . date('Y-m-d') . '.log', 'a+');
        if ($h) {
            fwrite($h, $log);
            fclose($h);
        }
    }
    public function update_amcs()
    {
        $this->load->model('device');
        $this->device->update_macs();
    }

    public function update_main_thumb()
    {
        $this->load->model('material');
        $this->load->helper('media');
        $media = $this->material->get_media_list(0, 0, -1);

        foreach ($media['data'] as $medium) {
            $destPath = $medium->full_path;

            if (file_exists($destPath)) {
                $thumbPaths = generate_thumbnails($destPath);
                if ($thumbPaths) {
                    $data = array();
                    $media['preview_status'] = 2;
                    if (isset($thumbPaths['tiny'])) {
                        $data['tiny_url'] = $thumbPaths['tiny'];
                    }
                    if (isset($thumbPaths['main'])) {
                        $data['main_url'] = $thumbPaths['main'];
                    }

                    $this->material->update_medium($data, $medium->id);
                }
            }
        }
    }


    public function scheduledRs485Commands()
    {
        $this->load->model('peripheral');
        $this->load->model('device');

        $commands = $this->peripheral->get_scheduled_commands();
        $now = date("H:i");
        foreach ($commands as $command) {
            if ($command->daily_at == $now) {
                $players = $this->peripheral->get_peripheral_player_ids($command->peripheral_id);
                if (empty($players)) {
                    continue;
                }
                $settings = $this->peripheral->get_command_and_settings($command->id);
                $settings->commandToApk = 0x18;

                $this->device->send_command_new($players, $settings);
            }
        }
    }

    public function merge_playback_detail($start_date = "2024-10-01", $months = null)
    {
        $this->load->model('feedback');
        $this->feedback->merge_playback_detail($start_date, $months);
        echo "cacluating...Done!" . "<br>";
    }

    public function getLocalTime()
    {
        $time = date("Y-m-d H:i:s", time());
        echo "time=" . $time . PHP_EOL;
        $serverTime = "2023-11-07 04:30:38";
        $localTimeZone = "UP2";
        $this->load->helper('date');
        $localTime = server_to_local($time, $localTimeZone);
        echo "localTime=" . $localTime . PHP_EOL;
    }

    //Connect to ftp server and check file products.json is modified or not
    public function check_products_json()
    {
        if (!$this->config->item('with_register_feature')) {
            return;
        }
        $this->load->model('store');
        $this->load->model('product');
        $redis = new Redis();
        $redis->pconnect('127.0.0.1', 6379);

        $dest_path = $this->config->item('register_json_dest_path');

        $store_file = $dest_path . "stores.json";

        $last_store_modified_time = $redis->get('store_last_modified_time');


        $last_modified_time = filemtime($store_file);
        echo date("Y-m-d H:i:s", $last_store_modified_time) . "<br>";
        echo date("Y-m-d H:i:s", $last_modified_time) . "<br>";
        if (!$last_store_modified_time || ($last_store_modified_time && $last_store_modified_time < $last_modified_time)) {
            $redis->set('store_last_modified_time', $last_modified_time);
            $items = Items::fromFile($store_file);
            $store_ids = array();
            foreach ($items as $item) {
                $store_ids[] = $item->id;
                $store_array = array('store_id' => $item->id, 'name' => $item->name);
                $store = $this->store->get_by_store_id($item->id);
                if ($store) {
                    $this->store->update($store->id, $store_array);
                } else {
                    $this->store->insert($store_array);
                }
            }
            if ($store_ids) {
                $this->store->delete_not_in_store_ids($store_ids);
            }
        }
        $stores = $this->store->get_all();

        $leading_char = "TP";
        $product_ids = array();
        foreach ($stores['data'] as $store) {

            $products_file =  $dest_path . $leading_char . $store->store_id . ".json";
            echo $products_file . "<br>";
            if (file_exists($products_file)) {
                //Get last modified time of products.json
                $last_modified_time = filemtime($products_file);
                echo date("Y-m-d H:i:s", $last_modified_time) . "<br>";
                echo "store: $store->name last update time:" . $store->json_last_update_time . "<br>";
                echo strtotime($store->json_last_update_time) . "<br>";
                echo $last_modified_time . "<br>";

                if ($store->json_last_update_time && strtotime($store->json_last_update_time) >= $last_modified_time) {
                    echo "$products_file is not modified<br>";
                    continue;
                } else {
                    $this->store->update($store->id, array('json_last_update_time' => date("Y-m-d H:i:s", $last_modified_time)));
                    echo "Update store's last update time" . $store->json_last_update_time . "<br>";
                    echo $this->db->last_query() . "<br>";
                }


                $items = Items::fromFile($products_file, ['pointer' => '/-/products']);

                foreach ($items as $item) {

                    $product_ids[] = $item->id;
                    $product_array = array(
                        'store_id' => $store->id, 'product_id' => $item->id, 'ean_code' => $item->EAN, 'plu_code' => $item->PLU,
                        'name' => $item->name, 'price' => $item->price, 'description' => $item->description, 'updated_at' => $item->updated_at
                    );
                    $filter_array = array('product_id' => $item->id);
                    $product = $this->product->get_by_filter($filter_array);
                    if ($product) {
                        $product_id = $product->id;
                        if ($product->updated_at < $item->updated_at) {
                            $this->product->update($product->id, $product_array);
                        }
                    } else {
                        $product_id = $this->product->insert($product_array);
                    }
                    if ($product_id) {
                        $this->product->delete_discounts($product_id);
                        $discounts = array();
                        if (isset($item->discounts)) {
                            foreach ($item->discounts as $discount) {
                                $discounts[] = array('product_id' => $product_id, 'price' => $discount->price, 'start_time' => $discount->start_time, 'end_time' => $discount->end_time);
                            }
                            if (!empty($discounts)) {
                                $this->product->insert_discounts_batch($discounts);
                            }
                        }
                    }
                }
            } else {
                echo "File not found: " . $products_file . "<br>";
            }
        }
        if (!empty($product_ids)) {
            $this->product->delete_not_in_product_ids($product_ids);
        }
    }
}
