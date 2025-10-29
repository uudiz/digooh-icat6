<?php
class Index extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('index');
        $this->lang->load('tag');
        $this->lang->load('criteria');
        $this->lang->load('user');
        $this->lang->load('onoff');
    }


    public function index()
    {
        $this->load->model('membership');

        $data = $this->get_data();

        $this->load->model('device');
        $cid = $this->get_cid();
        $pid = $this->get_parent_company_id();
        $auth = $this->get_auth();

        $cris = $this->get_criteria($cid, $pid);

        if ($auth > 1) {
            if ($cris) {
                $data['criteria_cnt'] = count($cris['criteria']);
            }
        }



        $filter_array = [];
        if (isset($cris['filter_array'])) {
            $filter_array = array_merge($filter_array, $cris['filter_array']);
        }

        $tags = $this->device->get_tag_list($cid);
        $data['tags_cnt'] = $tags['total'];

        $players = $this->device->get_player_list($pid ?: $cid, $filter_array);
        $data['players_cnt'] = $players['total'];
        $online_cnt = 0;
        if ($players['total']) {
            foreach ($players['data'] as $player) {
                if ($player->status > 1) {
                    $online_cnt++;
                }
            }
        }
        $data['online_cnt'] = $online_cnt;

        if ($this->get_auth() < 10) {
            $folders = $this->get_folders();
            $filter_array = [];
            if (isset($folders['folder_id'])) {
                $filter_array['folder_id'] = $folders['folder_id'];
            }
            // $data['folders_cnt'] = count(json_decode($folders['folders']));
            $cid = $folders['cid'];

            $this->load->model('material');
            $media = $this->material->get_media_list($cid, 0, -1, "name", 'desc', $filter_array);
            $data['media_cnt'] = $media['total'];

            $this->load->model('program');
            $cams_cnt = 0;
            if ($this->get_auth() < 2) {
                if ($this->config->item("new_campaign_user") && $auth == 1) {
                    $campaigns = $this->program->get_campaigns_count($this->get_cid(), $this->get_uid());
                    $cams_cnt = $campaigns['total'];
                } else {
                    //$user = $this->membership->get_user($this->get_uid());
                    $user_campaigns =  $this->membership->get_user_campaigns($this->get_uid());
                    if (!$user_campaigns) {
                        $cams_cnt = 0;
                    } else {
                        $cams_cnt = count($user_campaigns);
                    }
                }
            } else {
                $this->load->model('program');
                $campaigns = $this->program->get_campaigns_count($this->get_cid());
                $cams_cnt = $campaigns['total'];
            }

            $data['campaigns_cnt'] = $cams_cnt;

            //$this->load->model('feedback');
            //$data['playback_cnt'] = $this->feedback->get_playback_count($cid);
        }


        $data['companies_cnt'] = $this->membership->get_company_count();
        $data['users_cnt'] = $this->membership->get_users_count($cid);


        $data['body_file'] = "bootstrap/index";
        $this->load->view("bootstrap/layout/basiclayout", $data);
    }

    public function weather()
    {
        $this->load->helper('weather');
        print_r(get_weather($this->input->get('city')));
    }

    public function server()
    {
        echo time() . '<br/>';
        echo date('Y-m-d H:i:s');
        print_r($_SERVER);
    }


    public function main()
    {
        $cid = $this->get_cid();
        $data = $this->get_data();
        $this->load->model('device');
        $this->load->model('material');
        $this->load->model('program');
        $this->load->model('feedback');


        $tags = $this->device->get_tag_list($cid);
        $tag_count = $tags['total'];



        $criterias = $this->device->get_criteria_list($cid);
        $criteria_count = $criterias['total'];

        $player_count = 0;
        $media_count = 0;
        $rss_count = 0;
        $template_count = 0;
        $playlist_count = 0;
        $schedule_count = 0;
        $playback_count = 0;


        $player_count = $this->device->get_group_player_count($cid);

        if ($this->get_parent_company_id()) {
            $medias = $this->get_folders_and_media(-1);
            $media_count = $medias['total'];
        } else {
            $media_count = $this->material->get_company_media_count($cid);
        }

        $rss_count = $this->material->get_company_rss_count($cid);
        $template_count = $this->program->get_company_template_count($cid);
        $playlist_count = $this->program->get_company_playlist_count($cid);

        $playback_count = $this->feedback->get_playback_count($cid);

        $data = $this->get_data();

        $data['player_count'] = $player_count;
        $data['media_count'] = $media_count;
        $data['rss_count'] = $rss_count;
        $data['template_count'] = $template_count;
        $data['playlist_count'] = $playlist_count;
        $data['playback_count'] = $playback_count;
        $data['tag_count'] = $tag_count;
        $data['criteria_count'] = $criteria_count;

        $this->load->view("main", $data);
    }

    public function info()
    {
        phpinfo();
        exit;
        $string = 'Israel&#039;s';
        $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
        $string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);

        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
        $trans_tbl = array_flip($trans_tbl);
        echo strtr($string, $trans_tbl);

        //echo html_entity_decode('Israel&#039;s');
    }

    public function software()
    {
        $path = "F:\\work\\rewrite.log";
        echo filesize($path);
        $this->load->helper("file");
        print_r(get_img_info($path));
    }

    public function match()
    {
        $rawname = 'DSCN0002(1)';
        $x = 'DSCN0002';
        if (preg_match("/$x\(([0-9]+)\)$/", $rawname)) {
            //echo $matches[1];
            echo "xxxxx";
        } else {
            echo 'f';
        }
    }


    public function time()
    {
        $date = '2013-03-14 22:14:39';
        if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/", $date, $matches)) {
            $time = mktime(intval($matches[4]), intval($matches[5]), intval($matches[6]), intval($matches[2]), intval($matches[3]), intval($matches[1]));
            //echo $time.'  '.time().' ';
            //echo date('Y-m-d H:i:s', $time);

            $now = time();
            $system_time = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));;
            echo ($now - $system_time) / 3600;
            echo 'GMT:' . $system_time;
            echo date('Y-m-d H:i:s', $system_time) . ' ' . date('Y-m-d H:i:s', time());
            echo "dst:" . date('I');
            echo '--> server_timezone:' . $this->config->item('server.timezone');

            die;
        }
        $time1 = time();
        echo date('Y-m-d H:i:s', time());
        echo "--->";
        ini_set('date.timezone', 'UTC');
        $time2 = time();
        echo date('Y-m-d H:i:s', time());
        echo "---->";
        echo $time1 - $time2;
        echo "-->";
    }

    public function time2()
    {
        echo date('Y-m-d H:i:s');
    }

    public function update_language()
    {
        $this->load->model('membership');
        $language = $this->input->post('lang');
        $this->session->set_userdata(array('language' => $language));
        $this->membership->update_user(array('language' => $language), $this->get_uid());
        echo json_encode(array('language' => $language));
    }
}
