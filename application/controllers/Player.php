<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class Player extends MY_Controller
{
    private $filter_array = false;
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('common');
        $this->lang->load('player');
        $this->lang->load('group');
        $this->lang->load('criteria');
        $this->lang->load('tag');
        $this->lang->load('template');
        $this->lang->load('warn');
        $this->lang->load('software');
        //$this->lang->load('region');
        $this->load->helper('serial');
        $this->filter_array = false;
    }



    /**
     * 获取客户机列表页面
     *
     * @return
     */
    public function index()
    {
        $this->addJs("/assets/js/player.js", false);
        $data = $this->get_data();
        $pid = $this->get_parent_company_id();


        $cris = $this->get_criteria($this->get_cid(), $pid);
        $data['criteria'] = $cris['criteria'];
        if (isset($cris['filter_array'])) {
            $this->filter_array =  $cris['filter_array'];
        }

        $data['body_file'] = 'bootstrap/players/index';
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    public function getTableData()
    {
        $filter = $this->input->post('search');

        $status = $this->input->post('status');

        $tagid  = $this->input->post('tag_id');
        $criteria = $this->input->post('criteria');
        $and_criteria = $this->input->post('bind_criteria');

        $ex_criteria = $this->input->post('ex_criteria');
        $offset = $this->input->post('offset');
        $limit = $this->input->post('limit');
        $order_item = $this->input->post('sort');
        $order = $this->input->post('order');
        $filter_array = array();
        $mpeg_core = $this->input->post('mpeg_core');
        if ($filter != null) {
            $filter_array['filter_type'] = 'fourfields';
            if (preg_match("/(^[0-9]{3}\-[0-9]{3}\-[0-9]{4}$)/", $filter)) {
                $filter = trim(str_replace('-', '', $filter));
            }

            $filter_array['filter'] = $filter;
        }
        if ($status && $status >= 0) {
            $filter_array['status'] = $status;
        }

        if ($criteria && $criteria != -1) {
            $filter_array['criteria'] = $criteria;
        }

        if (!empty($and_criteria)) {
            $filter_array['and_criteria'] = $and_criteria;
        }

        if (!empty($ex_criteria)) {
            $filter_array['ex_criteria'] = $ex_criteria;
        }

        $online = $this->input->post('online');
        if ($online && $online == 1) {
            $filter_array['online'] = 1;
        }

        if ($this->config->item('has_sensor')) {
            $above_range = $this->input->post('healthy_status');
            if ($above_range && $above_range == 1) {
                $filter_array['healthy_status'] = 0;
            }
        }

        if ($tagid) {
            $filter_array['tag'] = $tagid;
        }
        if ($mpeg_core) {
            $filter_array['mpeg_core'] = $mpeg_core;
        }
        if ($this->filter_array) {
            $filter_array = array_merge($filter_array, $this->filter_array);
        }

        $this->load->model('device');
        $this->load->model('program');
        $this->load->model('strategy');

        $nocriassigned = false;

        $data  = $this->get_data();
        $pid = $this->get_parent_company_id();
        if ($pid > 0) {
            $cid = $pid;
        } else {
            $cid = $this->get_cid();
        }


        $cris = $this->get_criteria($this->get_cid(), $pid);
        $data['criteria'] = $cris['criteria'];
        if (isset($cris['filter_array'])) {
            $filter_array = array_merge($filter_array, $cris['filter_array']);
        }

        $data['tags'] = $this->device->get_tag_list($cid)['data'];


        $this->load->helper('chrome_logger');

        if ($nocriassigned) {
            $data['total'] = 0;
        } else {
            $data['filter_array'] = $filter_array;
            $players = $this->device->get_player_list($cid, $filter_array, true, $offset, $limit, $order_item, $order);

            if (isset($players) && count($players)) {
                if ($players['data']) {
                    foreach ($players['data'] as $p) {
                        $timezone = $this->get_timezone($p->id);
                        $p->last_connect = $this->get_local_time($p->last_connect, $timezone);

                        // $p->last_update = $this->get_local_time($p->last_update, $timezone);
                        $p->screenshotDate = $this->get_local_time($p->screenshotDate, $timezone);


                        if ($p->space) {
                            $space_arr = explode(',', $p->space);
                            $total_space = $space_arr[1]; //300  10%=30
                            $free_space = $p->disk_total; //20
                            if (0.1 * $total_space >= $free_space) {
                                $p->player_flag = 1;
                            } else {
                                $p->player_flag = 0;
                            }
                        } else {
                            $p->player_flag = 0;
                        }
                        $status = $p->status > 10 ? 12 : $p->status;
                        $p->status_toolbar = $this->lang->line("status." . $status);

                        if ($this->config->item('has_sensor')) {
                            if ($p->threshold_id) {
                                $this->load->model('threshold');
                                $p->thresholds = $this->threshold->get_item($p->threshold_id);
                            }
                        }
                    }
                }

                $data['total'] = $players['total'];
                $data['data']  = $players['data'];
                //$data['gid'] = $gid;
            } else {
                $data['total'] = 0;
            }
        }
        $data['modelArr'] = $this->lang->line('HW.model');

        $data['pid'] = $pid;
        $data['auth'] = $this->get_auth();
        $data['total'] = $players['total'];
        $data['rows']  = $players['data'];
        $data['cid']   = $this->get_cid();

        echo json_encode($data);
    }





    public function add_player_row()
    {
        $cid = $this->input->get('cid');
        $this->load->model('device');
        $this->load->model('membership');
        $company = $this->membership->get_company($cid);
        $sn = $this->device->get_player_new_code($cid);
        $data = $this->get_data();
        $data['cid'] = $cid;
        $data['sn'] = $sn;
        $data['mac'] = $this->input->get('mac');
        $data['cname'] = $company->name;
        $data['code'] = $this->input->get('code');
        $data['descr'] = $this->input->get('descr');
        $this->load->view('org/player/anew_row', $data);
    }

    //保存批量添加player
    public function do_save_player()
    {
        $sns = $this->input->post('sns');
        $macs = $this->input->post('macs');
        $cids = $this->input->post('cids');
        $codes = $this->input->post('codes');
        $descrs = $this->input->post('descrs');
        $this->load->model('device');
        $this->load->model('membership');
        if (empty($sns)) {
            echo 'empty';
        } else {
            for ($i = 0; $i < count($sns); $i++) {
                $sn = $sns[$i];
                $cid = $cids[$i];
                $mac = $macs[$i];
                if (empty($codes)) {
                    $city_code = '';
                } else {
                    $city_code = $codes[$i];
                }
                if (empty($descrs)) {
                    $descr = '';
                } else {
                    $descr = $descrs[$i];
                }
                /*
                //查询所在公司下是否有Temp组, 没有的话，添加
                $group = $this->device->get_group_byname(0, $cid, 'Temp');
                if($group) {
                    $group_id = $group->id;
                }else {
                    //添加Home组，再添加player
                    $newGroup = array(
                        'name'=>'Temp',
                        'descr'=>'Auto-registered player',
                        'type'=>1,
                        'company_id'=>$cid
                    );

                    $new_g = $this->device->add_group($newGroup, 0);
                    $group_id = $new_g;
                }
                */
                //$name = substr($mac, 9);
                //$name = str_replace('-', '', $mac);
                $mac = '0C-63-FC-' . substr($mac, 0, 2) . '-' . substr($mac, 2, 2) . '-' . substr($mac, 4);
                $name = $sn;
                $player = array(
                    'name' => $name,
                    'sn' => $sn,
                    'mac' => $mac,
                    'player_type' => 1,
                    'company_id' => $cid,
                    'city_code' => $city_code,
                    'descr' => $descr,
                    'batch_registration' => 1,
                    'batch_reg_status' => 1
                );
                $id = $this->device->add_player($player, 0);
                echo $id;
            }
        }
    }

    /**
     * 批量注册客户机
     *
     * @return
     */
    public function anew_add()
    {
        $this->addJs("player.js");
        $cid = $this->get_cid();
        $auth = $this->get_auth();
        $gids = 0;
        $this->load->model('device');
        $this->load->model('membership');
        $this->load->model('strategy');
        $companys = $this->membership->get_all_company_list();

        $data = $this->get_data();
        $data['companys'] = $companys;

        $data['body_file'] = 'org/player/anew_add_player';
        //$this->load->view('org/player/anew_add_player', $data);
        $this->load->view('include/main2', $data);
    }

    public function edit()
    {
        $this->addJs("/assets/js/form.js", false);
        $data = $this->get_data();
        $cid = $this->get_cid();
        $id = $this->input->get('id');

        $privilege =  $this->session->userdata("privilege");

        if ($this->get_auth() <= 2 || $this->is_partner()) {
            if (!$id && isset($privilege->can_create_player) && $privilege->can_create_player) {
            } else {
                $data['body_file'] = 'bootstrap/401';
                $this->load->view('bootstrap/layout/basiclayout', $data);
                return;
            }
        }


        $type = $this->input->get('type');
        $name = $this->input->get('name');
        $this->load->model('device');
        $this->load->model('strategy');
        $criteria = $this->device->get_criteria_list($cid);
        $tags = $this->device->get_tag_list($cid);
        $configs = $this->strategy->get_config_list($cid);
        $timers = $this->strategy->get_all_timer_list($cid);

        $data['title'] = $this->lang->line('create.player');
        if ($this->config->item('has_sensor')) {
            $this->load->model('threshold');
            $sensors = $this->threshold->get_list($cid);
            $data['sensors'] = $sensors['data'];
        }

        if ($id) {
            $player = $this->device->get_player($id);
            $extra = $this->device->get_player_extra($id);


            $data['id'] = $id;
            $data['player'] = $player;
            $data['name'] = $name;

            $data['extra'] = $extra;
            $data['title'] = $this->lang->line('edit.player');

            $curcris = $this->device->get_criteria_by_player($id);

            if ($curcris) {
                $data['cristr'] = implode(',', $curcris);
            }

            $curtags = $this->device->get_tags_by_player($id);

            if ($curtags) {
                foreach ($curtags as $t) {
                    $tagarray[] = $t['id'];
                }
                $data['tagstr'] = implode(',', $tagarray);
            }

            if ($this->config->item("player_pics")) {
                $pics = $this->device->get_player_pics($id);
                if ($pics) {
                    $data['pics'] = $this->device->get_player_pics($id);
                }
            }

            if ($this->config->item('ssp_feature') && $data['ssp_feature']) {
                $amc = $this->device->get_player_amc($player);
                $data['amc'] = $amc;
                $data['sspcristr'] =  $this->device->get_sspcriteria_ids_by_player($id);
                $data['ssptagstr'] =  $this->device->get_ssptags_ids_by_player($id);
                $data['ssptypes'] = $this->device->get_ssp_code_types();
            }
        }

        if ($this->config->item('ssp_feature') && $data['ssp_feature']) {


            $sspcris = $this->device->get_sspcriteria_list($cid);

            if ($sspcris['total']) {



                $groupedData = array();
                foreach ($sspcris['data'] as $item) {
                    $type = $item->type;
                    if (!isset($groupedData[$type])) {
                        $groupedData[$type] = array();
                    }
                    $groupedData[$type][] = $item;
                }
                $data['groupedSspCategories'] = $groupedData;
            }
            $ssptags = $this->device->get_ssptag_list($cid);
            if ($ssptags['total']) {
                $data['ssptags'] = $ssptags['data'];
            }
        }
        $data['criteria'] = $criteria['data'];
        $data['tags'] = $tags['data'];
        $data['configs'] = $configs['data'];
        $data['timers'] = $timers;

        $data['body_file'] = 'bootstrap/players/form';
        $this->load->view('bootstrap/layout/basiclayout', $data);
    }

    /*
     * 更新客户机所在组
     *
     */
    public function update_playerGroup()
    {
        $id = $this->input->post('id');
        $gid = $this->input->post('gid');
        $data = array('group_id' => $gid);
        $this->load->model('device');
        $this->device->update_player($data, $id);
    }

    /**
     * 保存客户机
     * @return
     */
    public function do_save()
    {
        $this->load->model('program');
        $result = array();
        $criteria = $this->input->post('criteria_select');
        $tags = $this->input->post('tags_select');
        $screensel = $this->input->post('screensel');
        $needPulish = false;
        $addingNew = false;
        $id = $this->input->post('id');
        $this->load->model('device');

        if ($screensel === null) {
            $screensel = 0;
        }


        $cid = $this->get_cid();
        if ($cid <= 0) {
            $result = array('code' => 1, 'msg' => $this->lang->line('warn.system.user'));
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', $this->lang->line('player'), 'trim|required');

            if ($this->config->item('digooh_player_form_validation')) {
                $this->form_validation->set_rules('setupdate', $this->lang->line('setup_date'), 'trim|required');
                $this->form_validation->set_rules('customsn1', $this->lang->line('custom_sn1'), 'trim|required');
                $this->form_validation->set_rules('customsn2', $this->lang->line('custom_sn2'), 'trim|required');
                $this->form_validation->set_rules('pps', $this->lang->line('pps'), 'trim|required');
            }

            if ($this->form_validation->run() == false) {
                $result = array('code' => 1, 'msg' => validation_errors());
            } elseif ($criteria == null) {
                $result = array('code' => 1, 'msg' => "Minimum one criteria is required!");
            } else {

                $flag = $this->device->get_player_by_name($id, $cid, $this->input->post('name')); //判断客户机是否有重名
                if ($id >= 0 && $flag) {
                    $result = array('code' => 1, 'msg' => sprintf($this->lang->line('player.name.exsit'), $this->input->post('name')));
                } else {
                    $player_type = 1;
                    $barcode = $this->input->post('barcode');
                    $conname = $this->input->post('conname');
                    $conphone = $this->input->post('conphone');
                    $conemail = $this->input->post('conemail');
                    $conaddr = $this->input->post('conaddr');
                    $conzipcode = $this->input->post('zipcode');
                    $simno = $this->input->post('simno');
                    $contown = $this->input->post('contown');
                    $street_num = $this->input->post('street_num');
                    $house_num = $this->input->post('house_num');


                    $simvolume = $this->input->post('simvolume');
                    $itemnum = $this->input->post('itemnum');
                    $screensize = $this->input->post('screensize');
                    $modelname = $this->input->post('modelname');
                    $sided = $this->input->post('sided');
                    $partnerid = $this->input->post('partnerid');
                    $locationid = $this->input->post('locationid');
                    $geox = $this->input->post('geox');
                    $geoy = $this->input->post('geoy');
                    $setupdate = $this->input->post('setupdate');
                    $viewdirection = $this->input->post('viewdirection');
                    $pps = $this->input->post('pps');
                    $visitors = $this->input->post('visitors');
                    $displaynum = $this->input->post('displaynum');
                    $state =  $this->input->post('state');
                    $country =  $this->input->post('country');
                    $customsn1 =  $this->input->post('customsn1');
                    $customsn2 =  $this->input->post('customsn2');


                    $extradata['barcode'] = $barcode ? $barcode : "";
                    $extradata['conname'] = $conname ? $conname : "";
                    $extradata['conphone'] = $conphone ? $conphone : "";
                    $extradata['conemail'] = $conemail ? $conemail : "";
                    $extradata['conaddr'] = $conaddr ? $conaddr : "";
                    $extradata['conzipcode'] = $conzipcode ? $conzipcode : "";
                    $extradata['simno'] = $simno ? $simno : "";
                    $extradata['contown'] = $contown ? $contown : "";

                    $extradata['simvolume'] = $simvolume ? $simvolume : "";
                    $extradata['itemnum'] = $itemnum ? $itemnum : "";
                    $extradata['screensize'] = $screensize ? $screensize : "";
                    $extradata['modelname'] = $modelname ? $modelname : "";
                    $extradata['sided'] =     $sided;
                    $extradata['partnerid'] = $partnerid ? $partnerid : "";
                    $extradata['locationid'] = $locationid ? $locationid : "";
                    $extradata['geox'] = $geox ? $geox : "";
                    $extradata['geoy'] = $geoy ? $geoy : "";
                    $extradata['setupdate'] = $setupdate;
                    $extradata['viewdirection'] = $viewdirection ? $viewdirection : "";
                    $extradata['pps'] = $pps ? $pps : "";
                    $extradata['visitors'] = $visitors ? $visitors : "";
                    $extradata['displaynum'] = $displaynum ? $displaynum : "";
                    $extradata['state'] = $state ? $state : "";
                    $extradata['country'] = $country ? $country : "";
                    $extradata['custom_sn1'] = $customsn1 ? $customsn1 : "";
                    $extradata['custom_sn2'] = $customsn2 ? $customsn2 : "";

                    $extradata['street_num'] = $street_num;
                    $extradata['last_maintenance'] = $this->input->post('last_maintenance');


                    $pos_tags = $this->input->post('pos_tags');
                    if ($pos_tags) {
                        $extradata['pos_tags'] = $pos_tags;
                    }
                    $ssp_exclude = $this->input->post('ssp_exclude');
                    if ($ssp_exclude) {
                        $extradata['ssp_exclude'] = $ssp_exclude;
                    }

                    $ssp_additional = $this->input->post('ssp_additional');
                    if ($ssp_additional) {
                        $extradata['ssp_additional'] = $ssp_additional;
                    }

                    $ssp_dsp_alias = $this->input->post('ssp_dsp_alias');
                    if ($ssp_dsp_alias) {
                        $extradata['ssp_dsp_alias'] = $ssp_dsp_alias;
                    }

                    $ssp_dsp_ref = $this->input->post('ssp_dsp_ref');
                    if ($ssp_dsp_ref) {
                        $extradata['ssp_dsp_ref'] = $ssp_dsp_ref;
                    }
                    $timer_id = $this->input->post('timer_config_id');
                    $timer_id = $timer_id > '0' ? $timer_id : null;

                    $data = array(
                        'name' => $this->input->post('name'),
                        'city_code' => $this->input->post('city_code'),
                        'descr' => $this->input->post('descr'),
                        'timer_config_id' => $timer_id,
                        'player_type' => $player_type,
                        'screen_oritation' => $screensel,
                        'video_playback' => $this->input->post('video_playback'),
                        //'mac' => $this->input->post('mac')

                    );

                    if ($this->config->item('has_sensor')) {

                        $data['threshold_id'] = $this->input->post('threshold_id') ?: null;
                    }

                    $data['details'] = $this->input->post('details');



                    if ($id) {
                        $player = $this->device->get_player($id);


                        if ($timer_id != $player->timer_config_id) {
                            $data['timer_config_flag'] = 1;
                            $data['timer_update'] =  date('Y-m-d H:i:s');
                            $needPulish = true;
                        }

                        $curcris = $this->device->get_criteria_by_player($id);

                        if ($curcris && $criteria) {
                            $get_cirarray = explode(",", $criteria);
                            sort($curcris);
                            sort($get_cirarray);
                            if ($curcris != $get_cirarray) {
                                $needPulish = true;
                            }
                        } elseif ((!$curcris && $criteria) || (!$criteria && $curcris)) {
                            $needPulish = true;
                        }

                        $curTags = $this->device->get_tags_by_player($id);

                        if ($curTags && $tags) {
                            $cur_tagarray = array_column($curTags, 'id');
                            $get_tagarray = explode(",", $tags);
                            sort($cur_tagarray);
                            sort($get_tagarray);
                            if ($cur_tagarray != $get_tagarray) {
                                $needPulish = true;
                            }
                        } elseif ((!$curTags && $tags) || (!$tags && $curTags)) {
                            $needPulish = true;
                        }

                        //$data['add_time'] = date('Y-m-d H:i:s');

                        $this->device->update_player_wCriteria($data, $id, $criteria, $tags);

                        //update extra info

                        $this->device->update_player_extra($extradata, $id);

                        $data['id'] = $id;
                        $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                        // $result = array_merge($data, $result);
                    } else {
                        $sn = $this->device->get_player_new_code($cid);
                        if ($sn === false) {
                            $result = array('code' => 1, 'msg' => $this->lang->line('error.player.sn'));
                        } else {
                            $data['company_id'] = $cid;
                            $data['sn'] = $sn;
                            $data['timer_config_flag'] = 1;
                            $data['add_time'] = date('Y-m-d H:i:s');
                            $id = $this->device->add_player($data, $this->get_uid(), $criteria, $tags);
                            //add extra info
                            $extradata['player_id'] = $id;
                            $this->device->add_player_extra($extradata);

                            $needPulish = true;
                            $addingNew = true;

                            if ($id !== false) {
                                $data['id'] = $id;

                                $result = array('code' => 0, 'msg' => $this->lang->line('save.success'));
                                //$result = array_merge($data, $result);
                            } else {
                                $result = array('code' => 1, 'msg' => sprintf($this->lang->line('save.fail'), $this->lang->line('player')));
                            }
                        }
                    }
                }
            }
        }
        //FIXME

        if ($this->config->item('ssp_feature') && $this->is_sspEnabled() && $id) {
            $ssp_tags = $this->input->post("ssptags_select");



            $ssp_categories =  $this->input->post("ssp_categories");
            $this->device->sync_player_sspcriteria($id, $ssp_categories);
            /*
            $ssp_catogeries = array();

            
            $dmis =  $this->input->post("dmi_select");
            if ($dmis) {
                $ssp_catogeries = array_merge($ssp_catogeries, $dmis);
            }
            $dapps =  $this->input->post("dpaa_select");
            if ($dapps) {
                $ssp_catogeries = array_merge($ssp_catogeries, $dapps);
            }

            $ilbs =  $this->input->post("ilb_select");
            if ($ilbs) {
                $ssp_catogeries = array_merge($ssp_catogeries, $ilbs);
            }

            $openoohs =  $this->input->post("openoohs_select");
            if ($openoohs) {
                $ssp_catogeries = array_merge($ssp_catogeries, $openoohs);
            }
            

           
*/

            $mon = $this->input->post('mon');
            $tue = $this->input->post('tue');
            $wed = $this->input->post('wed');
            $thu = $this->input->post('thu');
            $fri = $this->input->post('fri');
            $sat = $this->input->post('sat');
            $sun = $this->input->post('sun');


            $mon = $mon ? $mon : "0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0";
            $tue = $tue ? $tue : "0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0";
            $wed = $wed ? $wed : "0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0";
            $thu = $thu ? $thu : "0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0";
            $fri = $fri ? $fri : "0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0";
            $sat = $sat ? $sat : "0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0";
            $sun = $sun ? $sun : "0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0";


            $amc = array(
                'mon' => $mon,
                'tue' => $tue,
                'wed' => $wed,
                'thu' => $thu,
                'fri' => $fri,
                'sat' => $sat,
                'sun' => $sun,
            );
            $this->device->update_player_amc($id, $amc);
        }


        if ($id && $needPulish) {

            /*
            if (!$addingNew) {
                $campaigns = $this->program->get_published_campaign_by_player($id, -1, 9);
                //$this->program->detach_campaign_player_byPlayerId($id);
                if ($campaigns) {
                    $needPulish = true;
                } else {
                    $needPulish = false;
                }
            }
            */


            if ($needPulish || $addingNew) {

                $result['id'] = $id;
                $result['method'] = $addingNew ? 'add' : 'edit';
                $result['needPublish'] = 1;
                $result['repubmsg'] = $this->lang->line('player.refresh.msg');
            }
        }
        echo json_encode($result);
    }


    /**
     * 执行删除客户机
     *
     * @return
     */
    public function do_delete()
    {
        $id = $this->input->post("id");
        $code = 0;
        $msg = '';
        $result = array();
        $result['code'] = 0;
        $result['msg'] = $this->lang->line('delete.success');
        $this->load->model('program');
        $campaigns = $this->program->get_published_campaign_by_player($id, -1, 10);
        if ($campaigns) {
            $result['needPublish'] = 1;
            $result['repubmsg'] = $this->lang->line('need.refresh.campaign');
        }
        if ($id) {
            $this->load->model('device');
            if ($this->config->item("player_pics")) {
                $pics = $this->device->get_player_pics($id);
                if ($pics) {
                    $path = "./resources/playerPic/$id";

                    foreach ($pics as $pic) {
                        $picpath = $path . "/" . $pic->name;

                        if (file_exists($path)) {
                            @unlink($picpath);
                        }
                    }
                    $this->device->delete_player_pics($id);
                    @rmdir($path);
                }
            }

            if ($this->device->delete_player($id, $this->get_uid()) === false) {
                $result['code'] = 1;
                $result['msg'] = $this->lang->item('error.player.delete');
            }

            $this->program->detach_campaign_player_byPlayerId($id);
        } else {
            $result['code'] = 1;
            $result['msg'] = $this->lang->item('error.player.delete');
        }
        echo json_encode($result);
    }


    public function getLogData()
    {
        $this->load->model('device');
        $player_id = $this->input->post('player_id');
        $limit = $this->input->post('limit');
        $offset = $this->input->post('offset');
        $logs = $this->device->get_player_logList($player_id, $offset, $limit);

        $timezone = $this->get_timezone($player_id);
        if ($logs['total'] > 0) {
            foreach ($logs['data'] as $item) {
                $item->add_time = $this->get_local_time($item->add_time, $timezone);
            }
        }
        $result['total'] = $logs['total'];
        $result['rows'] = $logs['data'];
        echo json_encode($result);
    }
    /**
     * 显示详细信息
     *
     * @return
     */
    public function detail()
    {
        $id = $this->input->get("id");
        $this->load->model('device');
        $player = $this->device->get_player($id);
        if ($player) {
            $player->model = $this->config->item('player.model.' . $player->model);
            $player->total_off_times = $this->device->get_player_power_exption_count($id);
            $time = time();
            $date = [];
            for ($i = 0; $i <= 6; $i++) {
                $day = date('Y-m-d', strtotime('-' . $i . ' days', $time));
                $date[$i] = $this->device->get_player_power_exption_count($id, $day);
            }
            $player->recentDayOffTimes = $date;

            $storage_info  = 'No DISC';
            switch ($player->storage) {
                case 0:
                    $storage_info = 'No DISC';
                    break;
                case 1:
                    $storage_info = 'Internal DISC';
                    break;
                case 2:
                    if (!$player->player_type) {
                        $storage_info = 'SD';
                    } else {
                        $storage_info = 'TF Card';
                    }
                    break;
                case 3:
                    $storage_info = 'USB';
                    break;
                default:
                    $storage_info = 'N/A';
                    break;
            }
            if ($player->storage) {
                if (isset($player->space)) {
                    $info = explode(',', $player->space);
                    if (count($info) == 2) {
                        $disk_free = $info[0];
                        $disk_total = $info[1];
                        $storage_info = $storage_info . ": " . round($disk_free, 0) . '/' . round($disk_total, 0) . "MB (" . (round($disk_free / $disk_total, 2) * 100) . "% Free)";
                    }
                } else {
                    $storage_info = $storage_info . ": " . $player->disk_free . '/' . $player->disk_total . "MB (" . (round($player->disk_free / $player->disk_total, 2) * 100) . "% Free)";
                }
            }

            $player->storage_info = $storage_info;
        }
        $data = $this->get_data();
        $data['player'] = $player;
        $data['id'] = $id;
        $data['daily_restart'] = $player->daily_restart;


        /*
        $logs = $this->device->get_player_log_list($id, date('Y-m-d H:i:s', time() - (7 * 24 * 3600)));

        $timezone = $this->get_timezone($id);
        if ($logs) {
            $i = 0;
            foreach ($logs as $log) {
                $log->add_time = $this->get_local_time($log->add_time, $timezone);
            }
        }
        

        $data['logs'] = $logs;
        */
        $this->load->view('bootstrap/players/detail', $data);
    }
    private function get_timezone($id)
    {
        $this->load->model('device');
        $timezone = $this->device->get_player_timezone($id);
        if ($timezone < 0 && $timezone >= -12) {
            if (ceil($timezone) == $timezone) {
                $timezone = 'UM' . abs($timezone);
            } else {
                //半时区
                $timezone = 'UM' . intval(abs($timezone)) . '5';
            }
        } elseif ($timezone > 0 && $timezone <= 13) {
            if (ceil($timezone) == $timezone) {
                $timezone = 'UP' . $timezone;
            } else {
                //半时区
                $timezone = 'UP' . intval(abs($timezone)) . '5';
            }
        } else {
            $timezone = 'UTC';
        }
        return $timezone;
    }
    public function onlines($curpage = 1, $order_item = 'id', $order = 'desc')
    {
        $this->load->model('device');
        $type = $this->input->get('ptype');
        $gids = 0;
        $cid = $this->get_cid();
        $auth = $this->get_auth();
        $this->load->model('device');
        if ($auth < $this->config->item('auth_admin')) {
            $gids = $this->device->get_group_ids($this->get_uid());
        }
        $players = $this->device->get_all_online_player_list($cid, $gids, $order_item, $order, $type);
        $data = $this->get_data();
        $data['players'] = $players;
        $data['pid'] = rand(0, 100);
        $data['id'] = $this->input->get('id');
        $data['curpage'] = $curpage;
        $data['order_item'] = $order_item;
        $data['order'] = $order;
        $this->load->view('org/player/player_online_panel', $data);
    }

    public function configxml_onlines($curpage = 1, $order_item = 'id', $order = 'desc')
    {
        $this->load->model('device');
        $type = $this->input->get('ptype');
        $gids = 0;
        $cid = $this->get_cid();
        $auth = $this->get_auth();
        $this->load->model('device');
        if ($auth < $this->config->item('auth_admin')) {
            $gids = $this->device->get_group_ids($this->get_uid());
        }
        $players = $this->device->get_all_online_player_list($cid, $gids, $order_item, $order, $type);
        $data = $this->get_data();
        $data['players'] = $players;
        $data['pid'] = rand(0, 100);
        $data['id'] = $this->input->get('id');
        $data['curpage'] = $curpage;
        $data['order_item'] = $order_item;
        $data['order'] = $order;
        $data['ptype'] = $type;
        $this->load->view('org/player/player_configxml_online_panel', $data);
    }

    public function software_onlines($curpage = 1, $order_item = 'id', $order = 'desc')
    {
        $this->load->model('device');
        $type = $this->input->get('ptype');
        $core = $this->input->get('core'); //5166 或 5161
        $gids = 0;
        $cid = $this->get_cid();
        $auth = $this->get_auth();
        $this->load->model('device');
        if ($auth < $this->config->item('auth_admin')) {
            $gids = $this->device->get_group_ids($this->get_uid());
        }
        $players = $this->device->get_all_online_player_list($cid, $gids, $order_item, $order, $type, $core);
        $data = $this->get_data();
        $data['players'] = $players;
        $data['pid'] = rand(0, 100);
        $data['id'] = $this->input->get('id');
        $data['modelArr'] = $this->lang->line('HW.model');
        $data['curpage'] = $curpage;
        $data['order_item'] = $order_item;
        $data['order'] = $order;
        $data['ptype'] = $type;
        $data['core'] = $core;
        $this->load->view('org/player/software_player_online', $data);
    }

    /**
     * 更新软件版本
     *
     * @return
     */
    public function do_upgrade_version()
    {
        $version = $this->input->post('version');
        $ids = $this->input->post('ids');
        $code = 0;
        $msg = '';
        if ($ids === false || empty($ids)) {
            $code = 1;
            $msg = $this->lang->line('error.update.version.args.player');
        } elseif ($version) {
            $this->load->model('device');

            if ($this->device->update_upgrade_version($ids, $version)) {
                $msg = $this->lang->line('update.version.success');
            } else {
                $code = 1;
                $msg = $this->lang->line('error.update.version');
            }
        } else {
            $code = 1;
            $msg = $this->lang->line('param.error');
        }

        echo json_encode(array('code' => $code, 'msg' => $msg));
    }

    public function do_upgrade_firmware()
    {
        $version = $this->input->post('version');
        $ids = $this->input->post('ids');
        $code = 0;
        $msg = '';
        if ($ids === false || empty($ids)) {
            $code = 1;
            $msg = $this->lang->line('error.update.version.args.player');
        } elseif ($version) {
            $this->load->model('device');

            if ($this->device->update_upgrade_firmware_version($ids, $version)) {
                $msg = $this->lang->line('update.version.success');
            } else {
                $code = 1;
                $msg = $this->lang->line('error.update.version');
            }
        } else {
            $code = 1;
            $msg = $this->lang->line('param.error');
        }

        echo json_encode(array('code' => $code, 'msg' => $msg));
    }


    //error log list
    public function logList()
    {
        // open this directory
        if (!($myDirectory = @opendir($this->config->item("cached_errorlog_path")))) {
            die("No Current Files");
        }

        // get each entry
        while ($entryName = readdir($myDirectory)) {
            $dirArray[] = $entryName;
        }

        // close directory
        closedir($myDirectory);

        //	count elements in array
        $indexCount    = count($dirArray);

        // sort 'em
        sort($dirArray);
        $data['array'] = $dirArray;
        $this->load->view('bootstrap/players/log_list', $data);
    }

    /**
     *liu 2013-10-10 终端重启标志设置，默认reboot_flag为0,1表示需要重启
     */
    public function reboot()
    {
        $id = $this->input->post("id");
        $this->load->model("device");
        $code = 0;
        $msg = '';
        if ($id === false || empty($id)) {
            $code = 1;
            $msg = $this->lang->line('error.reboot.args.player');
        } else {
            $this->load->model('device');
            if ($this->device->update_flag($id, 'reboot_flag')) {
                $msg = $this->lang->line('reboot.player.success');
            } else {
                $code = 1;
                $msg = $this->lang->line('error.reboot.player');
            }
        }

        echo json_encode(array('code' => $code, 'msg' => $msg));
    }

    /**
     *liu 2013-10-10  终端格式化标志设置，默认format_flag为0,1表示需要格式化
     */
    public function format()
    {
        $id = $this->input->post("id");
        $this->load->model("device");
        $code = 0;
        $msg = '';
        if ($id === false || empty($id)) {
            $code = 1;
            $msg = $this->lang->line('error.reboot.args.player');
        } else {
            $this->load->model('device');
            if ($this->device->update_flag($id, 'format_flag')) {
                $msg = $this->lang->line('reboot.player.success');
            } else {
                $code = 1;
                $msg = $this->lang->line('error.format.player');
            }
        }
        echo json_encode(array('code' => $code, 'msg' => $msg));
    }

    public function preview_player()
    {
        //取消时间限制
        set_time_limit(0);
        $id = $this->input->get("id");
        $pls_id = $this->input->get("pls_id");
        $temp_id = $this->input->get("temp");
        if (isset($pls_id)) {
            $city_code = '';
            $player_city_code = '';
            $w_city = '';
            $w_low = '';
            $w_high = '';
            $w_icon = '';
            $w_text = '';
            $w_data = array();
            $weather_format = 'f';
            $this->load->helper('media');
            $this->load->model('program');
            $this->load->model('device');
            $this->load->helper('weather');
            $this->load->model('membership');
            $player = $this->device->get_player($id);
            $player_city_code = $player->city_code;

            $template = $this->program->get_template($temp_id);
            $area_list = $this->program->get_area_list($temp_id);
            if ($template) {
                $te_width = $template->w;
                $te_height = $template->h;
            } else {
                $te_width = $this->config->item('screen_width');
                $te_height =  $this->config->item('screen_height');
            }
            //取出当前playlist下template的各个area的媒体文件
            if ($area_list) {
                foreach ($area_list as $area) {
                    if ($area->area_type == $this->config->item('area_type_bg')) {
                        $bgs = $this->program->get_media_url($area->id, $pls_id);
                        if ($bgs) {
                            foreach ($bgs as $media) {
                                $area->main_url = $media->main_url;
                            }
                        } else {
                            $area->main_url = '';
                        }
                    } else {
                        if ($area->area_type == $this->config->item('area_type_logo')) {
                            $bgs = $this->program->get_media_url($area->id, $pls_id);
                            if ($bgs) {
                                foreach ($bgs as $media) {
                                    $area->tiny_url = $media->tiny_url;
                                }
                            } else {
                                $area->tiny_url = '';
                            }
                        } else {
                            if ($area->area_type == $this->config->item('area_type_image')) {
                                $bgs = $this->program->get_media_url($area->id, $pls_id);
                                if ($bgs) {
                                    $urls = array();
                                    $duration = array();
                                    $transmode = array();
                                    foreach ($bgs as $media) {
                                        //$urls[] = $media->main_url;
                                        $urls[] = substr($media->preview_url, 1, strlen($media->preview_url));
                                        $duration[] = substr($media->duration, 3, 2);
                                        $transmode[] = $media->transmode;
                                        $area->main_url = $urls;
                                        $area->duration = $duration;
                                        $area->transmode = $transmode;
                                    }
                                } else {
                                    $area->main_url = '';
                                }
                            } else {
                                if ($area->area_type == $this->config->item('area_type_movie')) {
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
                                            if ($media->media_type == $this->config->item("media_type_image")) {
                                                if ($media->source == $this->config->item("media_source_local")) {
                                                    $urls[] = $urls[] = substr($media->preview_url, 1, strlen($media->preview_url));
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
                                } else {
                                    if ($area->area_type == $this->config->item('area_type_text')) {
                                        $setting = $this->program->get_playlist_area_text_setting($pls_id, $area->id);
                                        if ($setting === false) {
                                            $setting = $this->get_default_text_setting($pls_id, $area->id);
                                            $id = $this->program->add_area_text_setting($setting, $this->get_uid());
                                            $setting = $this->program->get_area_text_setting($id);
                                        }
                                        $area->setting = $setting;
                                    } else {
                                        if ($area->area_type == $this->config->item('area_type_date')) {
                                            $setting = $this->program->get_area_time_setting($area->id, $pls_id);
                                            if ($setting) {
                                                $area->value = date($this->config->item('area_date_format_' . $setting->format));
                                            }
                                            $area->setting = $setting;
                                        } else {
                                            if ($area->area_type == $this->config->item('area_type_weather')) {
                                                $setting = $this->program->get_area_weather_setting($area->id, $pls_id);
                                                $area->setting = $setting;
                                                $company = $this->membership->get_company($template->company_id); //获取雅虎天气城市码
                                                $city_code = $company->city_code;
                                                if ($player_city_code) {
                                                    $city_code = $player_city_code;
                                                } else {
                                                    $city_code = '12712632';
                                                }
                                                $weather_format = $company->weather_format;
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
                                            } else {
                                                if ($area->area_type == $this->config->item('area_type_time')) {
                                                    $setting = $this->program->get_area_time_setting($area->id, $pls_id);
                                                    $area->setting = $setting;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $data = $this->get_data();
            $data['pls_id'] = $pls_id;
            $data['area_list'] = $area_list;
            $data['template'] = $template;
            $data['w_city'] = $w_city;
            $data['w_low'] = $w_low;
            $data['w_high'] = $w_high;
            $data['w_icon'] = $w_icon;
            $data['w_text'] = $w_text;
            $data['width'] = $te_width;
            $data['height'] = $te_height;
            $data['player_type'] = $player->player_type;
        } else {
            $data = $this->get_data();
            $data['pls_id'] = 0;
        }
        $this->load->view('org/player/preview_player', $data);
    }

    /**
     *
     *
     * @return
     */
    public function download()
    {
        $g_id = $this->input->get('group_id');
        $city_code = $this->input->get('city_code');

        $return_code = '';
        $return_url = '';

        $this->load->model('program');
        $g_flag = $this->program->get_sch_gid($g_id); //判断该组有没有SCH

        $data = array();
        if (!$g_flag) {
            $return_code = 1;
            $return_url = '';
            $data['return_msg'] = 'Can not find schedule for this player!';
        } else {
            $this->sep = "\n";
            $sch_name = 'PlayList.SCH';   //压缩包文件名
            $all_pls_id = '';            //所有pls列表ID

            //根据 group_id 生成SCH、PLS、Weather.xml
            $this->load->helper('week');
            $this->load->helper('file');
            $this->load->model('program');
            $schedules = $this->program->get_publish_schedule_by_group($g_id);
            if ($schedules) {
                foreach ($schedules as $sch) {
                    $sch->playlists = $this->program->get_playlist_by_schedule($sch->id);
                }
            }

            // 生成 Playlist.SCH
            $sch = '<?xml version="1.0" encoding="UTF-8"?>' . $this->sep;
            $sch .= '<Schedule>' . $this->sep;
            foreach ($schedules as $schedule) {
                if ($schedule->playlists) {
                    foreach ($schedule->playlists as $pl) {
                        $sch .= sprintf('<Programme name="%d.PLS" id="%d" fid="%d" type="0">', $pl->id, $schedule->id, $pl->id) . $this->sep;
                        $sch .= sprintf('<PublishTime>%s</PublishTime>', $schedule->publish_time) . $this->sep;
                        $sch .= sprintf('<action>%d</action>', $schedule->action) . $this->sep;
                        $sch .= sprintf('<URL>resources/publish/%d/%d.PLS</URL>', $schedule->company_id, $pl->id) . $this->sep;

                        if (true /*$pl->date_flag || $pl->time_flag*/) {
                            $sch .= '<Dates>' . $this->sep;
                            $sch .= '<Date>' . $this->sep;
                            if (true/*$pl->date_flag*/) {
                                $sch .= sprintf('<StartDate>%s</StartDate>', $schedule->start_date) . $this->sep;
                                $sch .= sprintf('<EndDate>%s</EndDate>', $schedule->end_date) . $this->sep;
                            }

                            if (true /*$pl->time_flag*/) {
                                if ($schedule->allDayFlag) {
                                    $sch .= sprintf('<StartTime>%s</StartTime>', '') . $this->sep;
                                    $sch .= sprintf('<EndTime>%s</EndTime>', '') . $this->sep;
                                } else {
                                    $sch .= sprintf('<StartTime>%s</StartTime>', $schedule->start_time) . $this->sep;
                                    $sch .= sprintf('<EndTime>%s</EndTime>', $schedule->end_time) . $this->sep;
                                }
                            }
                            $sch .= '</Date>' . $this->sep;
                            $sch .= '</Dates>' . $this->sep;
                        }

                        if (true /*$pl->week_flag*/) {
                            $sch .= '<Weeks>' . $this->sep;
                            $weeks = '';
                            if (is_week($schedule->week, 1)) {
                                $weeks .= '1,';
                            }
                            if (is_week($schedule->week, 2)) {
                                $weeks .= '2,';
                            }
                            if (is_week($schedule->week, 3)) {
                                $weeks .= '3,';
                            }
                            if (is_week($schedule->week, 4)) {
                                $weeks .= '4,';
                            }
                            if (is_week($schedule->week, 5)) {
                                $weeks .= '5,';
                            }
                            if (is_week($schedule->week, 6)) {
                                $weeks .= '6,';
                            }
                            if (is_week($schedule->week, 0)) {
                                $weeks .= '7,';
                            }
                            if (strlen($weeks) > 0) {
                                $sch .= substr($weeks, 0, -1);
                            }
                            $sch .= '</Weeks>' . $this->sep;
                        }
                        $sch .= sprintf('<Size>%d</Size>', $pl->file_size) . $this->sep;
                        $sch .= sprintf('<Signature>%s</Signature>', $pl->signature) . $this->sep;
                        $sch .= '</Programme>' . $this->sep;
                    }
                }
            }
            $sch .= '</Schedule>' . $this->sep;
            saveFile($this->config->item('cached_temp_path') . $g_id . '.SCH', $sch);  //保存到cached文件夹下，

            //判断sch文件是否改变
            $one_sch = $this->config->item('cached_temp_path') . $g_id . '.SCH';
            $two_sch = $this->config->item('resources') . $this->get_cid() . '/' . $g_id . '/MEDIA/PlayList.SCH';
            //if(file_exists($one_sch) && file_exists($two_sch) && md5_file($one_sch) == md5_file($two_sch)) {
            if (false) {
                $return_code = 0;
                $return_url = './resources/' . $this->get_cid() . '/' . $g_id . '/Playlist.SCH.tar.gz';
            } else {
                //创建文件夹
                $tar_path = $this->config->item('resources') . $this->get_cid() . '/' . $g_id . '/MEDIA/';
                $tar_path_audio = $tar_path . 'AUDIO/';
                $tar_path_bg = $tar_path . 'BG/';
                $tar_path_image = $tar_path . 'IMAGE/';
                $tar_path_logo = $tar_path . 'LOGO/';
                $tar_path_video = $tar_path . 'VIDEO/';
                $tar_path_yuv = $tar_path . 'YUV/';
                if (!file_exists($tar_path)) {
                    mkdir($tar_path, 0777, true);
                    mkdir($tar_path_audio, 0777, true);
                    mkdir($tar_path_bg, 0777, true);
                    mkdir($tar_path_image, 0777, true);
                    mkdir($tar_path_logo, 0777, true);
                    mkdir($tar_path_video, 0777, true);
                    mkdir($tar_path_yuv, 0777, true);
                } else {
                    if (file_exists($tar_path . 'Weather.xml')) {
                        unlink($tar_path . 'Weather.xml');
                    }
                    $this->delete_m($tar_path_audio);
                    $this->delete_m($tar_path_bg);
                    $this->delete_m($tar_path_image);
                    $this->delete_m($tar_path_logo);
                    $this->delete_m($tar_path_video);
                    $this->delete_m($tar_path_yuv);
                }

                // 生成 Playlist.SCH
                $sch = '<?xml version="1.0" encoding="UTF-8"?>' . $this->sep;
                $sch .= '<Schedule>' . $this->sep;
                foreach ($schedules as $schedule) {
                    if ($schedule->playlists) {
                        foreach ($schedule->playlists as $pl) {
                            $sch .= sprintf('<Programme name="%d.PLS" id="%d" fid="%d" type="0">', $pl->id, $schedule->id, $pl->id) . $this->sep;
                            $sch .= sprintf('<PublishTime>%s</PublishTime>', $schedule->publish_time) . $this->sep;
                            $sch .= sprintf('<action>%d</action>', $schedule->action) . $this->sep;
                            $sch .= sprintf('<URL>resources/publish/%d/%d.PLS</URL>', $schedule->company_id, $pl->id) . $this->sep;

                            if (true /*$pl->date_flag || $pl->time_flag*/) {
                                $sch .= '<Dates>' . $this->sep;
                                $sch .= '<Date>' . $this->sep;
                                if (true/*$pl->date_flag*/) {
                                    $sch .= sprintf('<StartDate>%s</StartDate>', $schedule->start_date) . $this->sep;
                                    $sch .= sprintf('<EndDate>%s</EndDate>', $schedule->end_date) . $this->sep;
                                }

                                if (true /*$pl->time_flag*/) {
                                    if ($schedule->allDayFlag) {
                                        $sch .= sprintf('<StartTime>%s</StartTime>', '') . $this->sep;
                                        $sch .= sprintf('<EndTime>%s</EndTime>', '') . $this->sep;
                                    } else {
                                        $sch .= sprintf('<StartTime>%s</StartTime>', $schedule->start_time) . $this->sep;
                                        $sch .= sprintf('<EndTime>%s</EndTime>', $schedule->end_time) . $this->sep;
                                    }
                                }
                                $sch .= '</Date>' . $this->sep;
                                $sch .= '</Dates>' . $this->sep;
                            }

                            if (true /*$pl->week_flag*/) {
                                $sch .= '<Weeks>' . $this->sep;
                                $weeks = '';
                                if (is_week($schedule->week, 1)) {
                                    $weeks .= '1,';
                                }
                                if (is_week($schedule->week, 2)) {
                                    $weeks .= '2,';
                                }
                                if (is_week($schedule->week, 3)) {
                                    $weeks .= '3,';
                                }
                                if (is_week($schedule->week, 4)) {
                                    $weeks .= '4,';
                                }
                                if (is_week($schedule->week, 5)) {
                                    $weeks .= '5,';
                                }
                                if (is_week($schedule->week, 6)) {
                                    $weeks .= '6,';
                                }
                                if (is_week($schedule->week, 0)) {
                                    $weeks .= '7,';
                                }
                                if (strlen($weeks) > 0) {
                                    $sch .= substr($weeks, 0, -1);
                                }
                                $sch .= '</Weeks>' . $this->sep;
                            }
                            $sch .= sprintf('<Size>%d</Size>', $pl->file_size) . $this->sep;
                            $sch .= sprintf('<Signature>%s</Signature>', $pl->signature) . $this->sep;
                            $sch .= '</Programme>' . $this->sep;
                        }
                    }
                }
                $sch .= '</Schedule>' . $this->sep;
                $cachedFile =  $tar_path . 'PlayList.SCH';
                saveFile($cachedFile, $sch);
                saveFile($this->config->item('cached_temp_path') . $g_id . '.SCH', $sch);  //保存到cached文件夹下，

                //获取所有列表 PLS
                foreach ($schedules as $schedule) {
                    if ($schedule->playlists) {
                        foreach ($schedule->playlists as $pl) {
                            $all_pls_id = $all_pls_id . $pl->id . ',';
                            $playlist = $this->program->get_playlist($pl->id);
                            $publish_path = $this->config->item('playlist_publish_path');
                            $publish_path .= $playlist->company_id . '/' . $pl->id . '.PLS';
                            copy($publish_path, $tar_path . $pl->id . '.PLS');
                        }
                    }
                }

                //获取天气  Weather.xml
                $this->load->model('membership');
                $this->load->helper('weather');
                $company = $this->membership->get_company($this->get_cid());
                $weather = get_weather($city_code, $company->weather_format);
                if ($weather) {
                    $xml = '<?xml version="1.0" encoding="utf-8"?>' . $this->sep;
                    $xml .= sprintf("<Weather>\n<City>%s</City>\n<Content>%s~%s</Content>\n<Pic>%s</Pic>\n</Weather>", $weather['city'], $weather['low'], $weather['high'], $weather['icon']);
                    saveFile($tar_path . 'Weather.xml', $xml);
                }

                //获取所有下载文件的路径       cat_playlist_area_media | cat_template_area | cat_media
                $all_pls_id = substr($all_pls_id, 0, strlen($all_pls_id) - 1);
                $this->load->model('device');
                $array_m = $this->device->get_pls_media($all_pls_id);
                $media_name = '';
                foreach ($array_m as $array) {
                    if ($array->source == 0) {
                        $ext = strtolower(substr($array->name, -4));
                        if ($ext == 'mpeg' || $ext == 'divx' || $ext == 'jpeg') {
                            $media_name = substr($array->name, 0, strlen($array->name) - 5);
                            $media_name = iconv('UTF-8', 'GBK//TRANSLIT//IGNORE', $media_name);
                        } else {
                            $ext = strtolower(substr($array->name, -3));
                            if ($ext == 'mp4' || $ext == 'mpg' || $ext == 'flv' || $ext == 'mov' || $ext == 'avi' || $ext == 'wmv' || $ext == 'jpg' || $ext == 'png' || $ext == 'bmp') {
                                $media_name = substr($array->name, 0, strlen($array->name) - 4);
                                $media_name = iconv('UTF-8', 'GBK//TRANSLIT//IGNORE', $media_name);
                            }
                        }
                        if ($array->area_type == 9) {  //BG
                            if ($array->img_fitORfill) { //fit
                                copy($array->publish_url, $tar_path_bg . '[F]' . $media_name . '.yuv');
                            } else {
                                copy($array->publish_url, $tar_path_bg . '[W]' . $media_name . '.yuv');
                            }
                        }
                        if ($array->area_type == 8) {  //Logo
                            if ($array->img_fitORfill) { //fit
                                copy($array->publish_url, $tar_path_logo . '[F]' . $media_name . '[' . $array->pid . ']' . '.bmp');
                            } else {
                                copy($array->publish_url, $tar_path_logo . '[W]' . $media_name . '[' . $array->pid . ']' . '.bmp');
                            }
                            //copy($array->publish_url, $tar_path_logo.$array->img_fitORfill.'_'.$array->area_type.'_'.$media_name.'['.$array->pid.']'.'.bmp');
                        }
                        if ($array->area_type == 0) {
                            if ($array->media_type == 1 && $array->source == 0) { //Image
                                if (strtolower(substr($array->name, -3)) == 'jpg') {
                                    if ($array->w < $array->h) {
                                        if ($array->img_fitORfill) { //fit
                                            copy($array->publish_url, $tar_path_image . '[F]' . $media_name . '[' . $array->pid . ']' . '.jpg');
                                        } else {
                                            copy($array->publish_url, $tar_path_image . '[W]' . $media_name . '[' . $array->pid . ']' . '.jpg');
                                        }
                                        //copy($array->publish_url, $tar_path_image.$array->img_fitORfill.'_'.$array->area_type.'_'.$media_name.'['.$array->pid.']'.'.jpg');
                                    } else {
                                        if ($array->img_fitORfill) { //fit
                                            copy($array->publish_url, $tar_path_image . '[F]' . $media_name . '.jpg');
                                        } else {
                                            copy($array->publish_url, $tar_path_image . '[W]' . $media_name . '.jpg');
                                        }
                                        //copy($array->publish_url, $tar_path_image.$array->img_fitORfill.'_'.$array->area_type.'_'.$media_name.'.jpg');
                                    }
                                }
                                if (strtolower(substr($array->name, -3)) == 'png') {
                                    if ($array->w < $array->h) {
                                        if ($array->img_fitORfill) { //fit
                                            copy($array->publish_url, $tar_path_image . '[F]' . $media_name . '[' . $array->pid . ']' . '.png');
                                        } else {
                                            copy($array->publish_url, $tar_path_image . '[W]' . $media_name . '[' . $array->pid . ']' . '.png');
                                        }
                                        //copy($array->publish_url, $tar_path_image.$array->img_fitORfill.'_'.$array->area_type.'_'.$media_name.'['.$array->pid.']'.'.jpg');
                                    } else {
                                        if ($array->img_fitORfill) { //fit
                                            copy($array->publish_url, $tar_path_image . '[F]' . $media_name . '.png');
                                        } else {
                                            copy($array->publish_url, $tar_path_image . '[W]' . $media_name . '.png');
                                        }
                                        //copy($array->publish_url, $tar_path_image.$array->img_fitORfill.'_'.$array->area_type.'_'.$media_name.'.jpg');
                                    }
                                }
                                if (strtolower(substr($array->name, -3)) == 'bmp') {
                                    if ($array->w < $array->h) {
                                        if ($array->img_fitORfill) { //fit
                                            copy($array->publish_url, $tar_path_image . '[F]' . $media_name . '[' . $array->pid . ']' . '.bmp');
                                        } else {
                                            copy($array->publish_url, $tar_path_image . '[W]' . $media_name . '[' . $array->pid . ']' . '.bmp');
                                        }
                                        //copy($array->publish_url, $tar_path_image.$array->img_fitORfill.'_'.$array->area_type.'_'.$media_name.'['.$array->pid.']'.'.jpg');
                                    } else {
                                        if ($array->img_fitORfill) { //fit
                                            copy($array->publish_url, $tar_path_image . '[F]' . $media_name . '.bmp');
                                        } else {
                                            copy($array->publish_url, $tar_path_image . '[W]' . $media_name . '.bmp');
                                        }
                                        //copy($array->publish_url, $tar_path_image.$array->img_fitORfill.'_'.$array->area_type.'_'.$media_name.'.jpg');
                                    }
                                }
                                if (strtolower(substr($array->name, -4)) == 'jpeg') {
                                    if ($array->w < $array->h) {
                                        if ($array->img_fitORfill) { //fit
                                            copy($array->publish_url, $tar_path_image . '[F]' . $media_name . '[' . $array->pid . ']' . '.jpeg');
                                        } else {
                                            copy($array->publish_url, $tar_path_image . '[W]' . $media_name . '[' . $array->pid . ']' . '.jpeg');
                                        }
                                        //copy($array->publish_url, $tar_path_image.$array->img_fitORfill.'_'.$array->area_type.'_'.$media_name.'['.$array->pid.']'.'.jpeg');
                                    } else {
                                        if ($array->img_fitORfill) { //fit
                                            copy($array->publish_url, $tar_path_image . '[F]' . $media_name . '.jpeg');
                                        } else {
                                            copy($array->publish_url, $tar_path_image . '[W]' . $media_name . '.jpeg');
                                        }
                                        //copy($array->publish_url, $tar_path_image.$array->img_fitORfill.'_'.$array->area_type.'_'.$media_name.'.jpeg');
                                    }
                                }
                            }
                            if ($array->media_type == 2 && $array->source == 0) { //Video
                                if ($array->w < $array->h) {
                                    $media_name = substr($array->name, 0, strlen($array->name) - 4);
                                    $media_name = iconv('UTF-8', 'GBK//TRANSLIT//IGNORE', $media_name);
                                    copy($array->publish_url, $tar_path_video . $media_name . '[P].mkv');
                                } else {
                                    $ext = strtolower(substr($array->name, -4));
                                    if ($ext == 'mpeg' || $ext == 'divx') {
                                        $media_name = substr($array->name, 0, strlen($array->name) - 5);
                                        $media_name = iconv('UTF-8', 'GBK//TRANSLIT//IGNORE', $media_name);
                                        copy($array->publish_url, $tar_path_video . $media_name . '.' . $ext);
                                    } else {
                                        $ext = strtolower(substr($array->name, -3));
                                        if ($ext == 'mpg' || $ext == 'avi') {
                                            $media_name = substr($array->name, 0, strlen($array->name) - 4);
                                            $media_name = iconv('UTF-8', 'GBK//TRANSLIT//IGNORE', $media_name);
                                            copy($array->publish_url, $tar_path_video . $media_name . '.' . $ext);
                                        }
                                        if ($ext == 'mp4' || $ext == 'flv' || $ext == 'mov' || $ext == 'wmv' || $ext == 'mkv') {
                                            $media_name = substr($array->name, 0, strlen($array->name) - 4);
                                            $media_name = iconv('UTF-8', 'GBK//TRANSLIT//IGNORE', $media_name);
                                            copy($array->publish_url, $tar_path_video . $media_name . '.mkv');
                                        }
                                    }
                                }
                            }
                        }
                        if ($array->area_type == 1) { //Yuv
                            if ($array->img_fitORfill) { //fit
                                copy($array->publish_url, $tar_path_yuv . '[F]' . $media_name . '[' . $array->pid . ']' . '.yuv');
                            } else {
                                copy($array->publish_url, $tar_path_yuv . '[W]' . $media_name . '[' . $array->pid . ']' . '.yuv');
                            }
                            //copy($array->publish_url, $tar_path_yuv.$array->img_fitORfill.'_'.$array->area_type.'_'.$media_name.'['.$array->pid.']'.'.yuv');
                        }
                    }
                }
                //tar -zcvf /home/np100g/public_html/np100/resources/196/sch_test.tar.gz -C /home/np100g/public_html/np100/resources/196 sch_test
                //$command = 'tar -zcvf /home/miatek/np100/resources/'.$this->get_cid().'/'.$g_id.'/Playlist.SCH.tar.gz -C /home/miatek/np100/resources/'.$this->get_cid().'/'.$g_id.' MEDIA';
                $command = 'tar -zcvf ' . $this->config->item('gz_path') . 'resources/' . $this->get_cid() . '/' . $g_id . '/Playlist.SCH.tar.gz -C ' . $this->config->item('gz_path') . 'resources/' . $this->get_cid() . '/' . $g_id . ' MEDIA';
                @exec($command, $output, $return);
                $return_code = $return;
                $return_url = './resources/' . $this->get_cid() . '/' . $g_id . '/Playlist.SCH.tar.gz';
            }
            $data['return_msg'] = '';
        }


        $data['return_code'] = $return_code;
        $data['return_url'] = $return_url;

        $this->load->view('org/player/download', $data);
    }

    /**
     * android pls打包
     */
    public function download_android()
    {
        $g_id = $this->input->get('group_id');
        $city_code = $this->input->get('city_code');

        $return_code = '';
        $return_url = '';

        $this->load->model('program');
        $g_flag = $this->program->get_sch_gid($g_id); //判断该组有没有SCH

        $data = array();
        if (!$g_flag) {
            $return_code = 1;
            $return_url = '';
            $data['return_msg'] = 'Can not find schedule for this player!';
        } else {
            $this->sep = "\n";
            $sch_name = 'Playlist.SCH';   //压缩包文件名
            $all_pls_id = '';            //所有pls列表ID

            //根据 group_id 生成SCH、PLS、Weather.xml
            $this->load->helper('week');
            $this->load->helper('file');
            $this->load->model('program');
            $schedules = $this->program->get_publish_schedule_by_group($g_id);
            if ($schedules) {
                foreach ($schedules as $sch) {
                    $sch->playlists = $this->program->get_playlist_by_schedule($sch->id);
                }
            }

            if (false) {
                $return_code = 0;
                $return_url = './resources/' . $this->get_cid() . '/' . $g_id . '/Playlist.SCH.tar.gz';
            } else {
                //创建文件夹
                $tar_path = $this->config->item('resources') . $this->get_cid() . '/' . $g_id . '/MEDIA/';
                $tar_path_audio = $tar_path . 'AUDIO/';
                $tar_path_bg = $tar_path . 'BG/';
                $tar_path_image = $tar_path . 'IMAGE/';
                $tar_path_logo = $tar_path . 'LOGO/';
                $tar_path_video = $tar_path . 'VIDEO/';
                $tar_path_mask = $tar_path . 'MASK/';

                //如果目录已存在，删除之
                $this->del_folder($tar_path);


                if (!file_exists($tar_path)) {
                    mkdir($tar_path, 0777, true);
                    mkdir($tar_path_audio, 0777, true);
                    mkdir($tar_path_bg, 0777, true);
                    mkdir($tar_path_image, 0777, true);
                    mkdir($tar_path_logo, 0777, true);
                    mkdir($tar_path_video, 0777, true);
                    mkdir($tar_path_mask, 0777, true);
                } else {
                    if (file_exists($tar_path . 'Weather.xml')) {
                        unlink($tar_path . 'Weather.xml');
                    }
                    $this->delete_m($tar_path_audio);
                    $this->delete_m($tar_path_bg);
                    $this->delete_m($tar_path_image);
                    $this->delete_m($tar_path_logo);
                    $this->delete_m($tar_path_video);
                    $this->delete_m($tar_path_mask);
                }


                $sch = '<?xml version="1.0" encoding="UTF-8"?>' . $this->sep;
                $sch .= '<Schedule>' . $this->sep;
                foreach ($schedules as $schedule) {
                    if ($schedule->playlists) {
                        foreach ($schedule->playlists as $pl) {

                            //判断横屏还是竖屏
                            $this->load->model('program');
                            $template = $this->program->get_template($pl->template_id);
                            if ($template->w > $template->h) {
                                $sense = 1;
                            } else {
                                $sense = 2;
                            }
                            $fid = $this->program->add_cat_fid();

                            $sch .= sprintf('<Programme name="%d.PLS" id="%d" fid="%d%d" type="0" sense="%d" model="3">', $pl->id, $pl->id, $pl->id, $fid, $sense) . $this->sep;
                            $sch .= sprintf('<PublishTime>%s</PublishTime>', $schedule->publish_time) . $this->sep;
                            $sch .= sprintf('<action>%d</action>', $schedule->action) . $this->sep;
                            $sch .= sprintf('<URL>resources/publish/%d/%d.PLS</URL>', $schedule->company_id, $pl->id) . $this->sep;
                            $sch .= '<Dates>' . $this->sep;
                            $sch .= '<Date>' . $this->sep;
                            $sch .= sprintf('<StartDate>%s</StartDate>', $schedule->start_date) . $this->sep;
                            $sch .= sprintf('<EndDate>%s</EndDate>', $schedule->end_date) . $this->sep;
                            if ($schedule->allDayFlag) {
                                $sch .= sprintf('<StartTime>%s</StartTime>', '') . $this->sep;
                                $sch .= sprintf('<EndTime>%s</EndTime>', '') . $this->sep;
                            } else {
                                $sch .= sprintf('<StartTime>%s</StartTime>', $schedule->start_time) . $this->sep;
                                $sch .= sprintf('<EndTime>%s</EndTime>', $schedule->end_time) . $this->sep;
                            }
                            $sch .= '</Date>' . $this->sep;
                            $sch .= '</Dates>' . $this->sep;
                            $sch .= '<Weeks>' . $this->sep;
                            $weeks = '';
                            if (is_week($schedule->week, 1)) {
                                $weeks .= '1,';
                            }
                            if (is_week($schedule->week, 2)) {
                                $weeks .= '2,';
                            }
                            if (is_week($schedule->week, 3)) {
                                $weeks .= '3,';
                            }
                            if (is_week($schedule->week, 4)) {
                                $weeks .= '4,';
                            }
                            if (is_week($schedule->week, 5)) {
                                $weeks .= '5,';
                            }
                            if (is_week($schedule->week, 6)) {
                                $weeks .= '6,';
                            }
                            if (is_week($schedule->week, 0)) {
                                $weeks .= '7,';
                            }
                            if (strlen($weeks) > 0) {
                                $sch .= substr($weeks, 0, -1);
                            }
                            $sch .= '</Weeks>' . $this->sep;
                            $sch .= sprintf('<Size>%d</Size>', $pl->file_size) . $this->sep;
                            $sch .= sprintf('<Signature>%s</Signature>', $pl->signature) . $this->sep;
                            $sch .= '</Programme>' . $this->sep;
                        }
                    }
                }
                $sch1 = $sch . '</Schedule>';
                $sch_md5 = md5($sch1);
                $sch = $sch . '</Schedule><!--' . $sch_md5 . '-->' . $this->sep;
                saveFile($tar_path . 'Playlist.SCH', $sch);

                //获取所有列表 PLS
                foreach ($schedules as $schedule) {
                    if ($schedule->playlists) {
                        foreach ($schedule->playlists as $pl) {
                            $all_pls_id = $all_pls_id . $pl->id . ',';
                            $playlist = $this->program->get_playlist($pl->id);
                            $publish_path = $this->config->item('playlist_publish_path');
                            $publish_path .= $playlist->company_id . '/' . $pl->id . '.PLS';
                            copy($publish_path, $tar_path . $pl->signature . '.PLS');
                        }
                    }
                }

                //获取天气  Weather.xml
                $this->load->model('membership');
                $this->load->helper('weather');
                $company = $this->membership->get_company($this->get_cid());
                //$weather = get_weather($city_code, $company->weather_format);
                $weather = get_yahoo_weather_3days($city_code, $company->weather_format);
                if ($weather) {
                    $city = $weather['city'];
                    $data = $weather['data'];
                    $xml = '<?xml version="1.0" encoding="utf-8"?>' . $this->sep;
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
                    saveFile($tar_path . 'Weather.xml', $xml);
                }

                //获取所有下载文件的路径       cat_playlist_area_media | cat_template_area | cat_media
                $all_pls_id = substr($all_pls_id, 0, strlen($all_pls_id) - 1);

                $this->load->model('device');
                $array_m = $this->device->get_pls_media($all_pls_id);

                foreach ($array_m as $array) {
                    $ext = substr($array->name, strripos($array->name, '.') + 1);

                    if ($array->source == 0) {
                        if (file_exists($array->publish_url)) {
                            if ($array->area_type == 9) {  //BG
                                copy($array->publish_url, $tar_path_bg . md5_file($this->config->item('gz_path') . substr($array->publish_url, 1)) . '.' . $ext);
                            }
                            if ($array->area_type == 8) {  //Logo
                                copy($array->publish_url, $tar_path_logo . $array->signature . '.' . $ext);
                            }
                            if ($array->media_type == 1 && ($array->area_type == 0 || $array->area_type == 1)) { //Image
                                copy($array->publish_url, $tar_path_image . $array->signature . '.' . $ext);
                            }
                            if ($array->media_type == 2) { //Video
                                copy($array->publish_url, $tar_path_video . $array->signature . '.' . $ext);
                            }
                            if ($array->area_type == 28) { //Mask
                                copy($array->publish_url, $tar_path_mask . $array->signature . '.' . $ext);
                            }
                        }
                    }
                }
                $return_url = './resources/' . $this->get_cid() . '/' . $g_id . '/Playlist.SCH.tar.gz';
                if (file_exists($return_url)) {
                    @exec('rm -f ' . $return_url);
                }

                $command = 'tar -zcvf ' . $this->config->item('gz_path') . 'resources/' . $this->get_cid() . '/' . $g_id . '/Playlist.SCH.tar.gz -C ' . $this->config->item('gz_path') . 'resources/' . $this->get_cid() . '/' . $g_id . ' MEDIA';
                @exec($command, $output, $return);

                $this->del_folder('./' . $tar_path);

                $return_code = $return;
                //$return_url = './resources/'.$this->get_cid().'/'.$g_id.'/Playlist.SCH.tar.gz';
            }
            $data['return_msg'] = '';
        }
        $data['return_code'] = $return_code;
        $data['return_url'] = $return_url;


        $this->load->view('org/player/download', $data);
    }

    public function del_folder($dir)
    {
        if (file_exists($dir)) {
            $command = 'rm -rf ' . $dir;
            @exec($command);
        }
    }

    //删除文件夹中的文件
    public function delete_m($dir)
    {
        if (file_exists($dir)) {
            $dh = opendir($dir);
            while ($file = readdir($dh)) {
                if ($file != "." && $file != "..") {
                    $fullpath = $dir . "/" . $file;
                    if (!is_dir($fullpath)) {
                        unlink($fullpath);
                    } else {
                        deldir($fullpath);
                    }
                }
            }
            closedir($dh);
        }
    }

    public function android_control()
    {
        $ids = $this->input->post('ids');
        $type = $this->input->post('type');
        $value = $this->input->post('value');

        $this->load->model('device');
        $this->device->send_command($ids, $type, $value);
    }

    public function screenshotView()
    {
        $cid = $this->input->get('cid');
        $id = $this->input->get('id');
        $sn = $this->input->get('sn');
        $time = $this->input->get('time');
        $path = './resources/preview/' . $cid . '/' . $sn . '.png';
        $data = array();
        $data['cid'] = $cid;
        $data['id'] = $id;
        $data['sn'] = $sn;
        $data['path'] = $path;
        $data['time'] = $time;

        $this->load->view('org/player/screenshot_view', $data);
    }

    public function do_delete_screenshot()
    {
        $id = $this->input->post('id');
        $this->load->model('device');
        $data = array('screenshot' => '');

        $result = $this->device->update_player($data, $id);

        $data['msg'] = $this->lang->line('delete.success');
        $data['code'] = 0;

        echo json_encode($data);
    }
    public function calendar_view()
    {
        $id = $this->input->get('id');
        $data = array();
        $data['id'] = $id;

        $this->load->view('org/player/calendar_list', $data);
    }


    public function prepare_events()
    {

        $cid = $this->get_cid();
        $player_id = $this->input->get('id');
        $start = $this->input->get('start');
        $end = $this->input->get('end');

        $this->load->model('program');
        $this->load->model('membership');
        $company = $this->membership->get_company($this->get_cid());

        $begin = new DateTime($start);
        $end = new DateTime($end);
        $end = $end->modify('+1 day');
        $interval = DateInterval::createFromDateString('1 day');
        $date_range = new DatePeriod($begin, $interval, $end);

        $data = array();

        $this->load->model('device');


        if ($this->config->item('with_template')) {
            $cams = $this->program->get_published_playlist_by_player($player_id);
            if ($cams) {
                //for ($checkday = $startStamp; $checkday <= $endStamp;) {
                foreach ($date_range as $checkday) {

                    $week_day = $checkday->format("w");
                    if ($week_day == 0) {
                        $week_day = 6;
                    } else {
                        $week_day = $week_day - 1;
                    }

                    $today = $checkday->format("Y-m-d");


                    $today_campaigns = array_filter($cams, function ($value) use ($today) {
                        if ($today >= $value->start_date && $today <= $value->end_date) {
                            return true;
                        }
                        return false;
                    });


                    if ($today_campaigns) {

                        foreach ($today_campaigns as $campaign) {
                            //$campaign->week is a byte value, each bit represent a day of week, 1 means on, 0 means off,check $week_day is on or off


                            if (!($campaign->week & (1 << $week_day))) {
                                continue;
                            }


                            if ($campaign->time_flag) {
                                $item = array('start' => $today, 'end' => $today);
                                $item['allDay'] = 1;
                            } else {
                                $start_time = sprintf("%02d:%02d", $campaign->start_timeH, $campaign->start_timeM);
                                $end_time = sprintf("%02d:%02d", $campaign->end_timeH, $campaign->end_timeM);
                                if (($campaign->end_timeH < $campaign->start_timeH) || ($campaign->end_timeH == $campaign->start_timeH && $campaign->end_timeM < $campaign->start_timeM)) {
                                    $end_time = sprintf("%02d:%02d", $campaign->end_timeH, $campaign->end_timeM);
                                    $end_day = date("Y-m-d", strtotime($today . "+1 day"));
                                } else {
                                    $end_day = $today;
                                }
                                if ($campaign->end_timeH == 24 && $campaign->end_timeM == 0) {
                                    $end_time = sprintf("%02d:%02d", 0, 0);
                                    $end_day = date("Y-m-d", strtotime($today . "+1 day"));
                                }
                                $item = array('start' => $today . sprintf("T%s:00Z", $start_time), 'end' => $end_day . sprintf("T%s:00Z", $end_time));
                            }

                            $item['id'] = $campaign->id;
                            $item['title'] = $campaign->name;

                            $data[] = $item;
                            //$item = null;
                        }
                    }
                }
            }
        } else {
            $player = $this->device->get_player($player_id, true);

            foreach ($date_range as $checkday) {
                $today = $checkday->format("Y-m-d");
                $timeslots = $this->program->do_get_today_timeslots($player, $today);

                if ($timeslots) {
                    foreach ($timeslots as $slot) {

                        if (count($slot->campaigns)) {
                            $item = array('start' => $today  . ' ' . sprintf("%02d:%02d:00", $slot->startH, $slot->startM), 'end' => $today  . ' ' . sprintf("%02d:%02d:00", $slot->stopH, $slot->stopM));
                            if ($company->color_setting == 1) {
                                $item['textColor'] = "#000000";
                            }

                            foreach ($slot->campaigns as $campaign) {

                                $item['id'] = $campaign['compaign_id'];
                                $item['title'] = $campaign['name'];
                                if ($campaign['priority'] == 0) {
                                    if ($company->color_setting == 0) {
                                        $item['color'] = '#FF8247';
                                    } else {
                                        $item['color'] = '#CD9B9B';
                                    }
                                } elseif ($campaign['priority'] == 1) {
                                    if ($company->color_setting == 0) {
                                        $item['color'] = '#1874CD';
                                    } else {
                                        $item['color'] = '#EEB4B4';
                                    }
                                } elseif ($campaign['priority'] == 2) {
                                    if ($company->color_setting == 0) {
                                        $item['color'] = '#1C86EE';
                                    } else {
                                        $item['color'] = '#FFC1C1';
                                    }
                                }


                                $data[] = $item;
                                //$item = null;
                            }
                        }
                    }
                }
            }
        }
        echo json_encode($data);
    }


    public function get_reports()
    {
        $this->load->model('device');


        $playerid = $this->input->get("id");
        $getday = $this->input->get("day");
        $player = $this->device->get_player($playerid, true);


        $this->load->model('program');
        $this->load->model('membership');
        $this->load->helper('date');


        if ($getday) {
            $today = $getday;
        } else {
            $dst = $this->membership->is_dst_on($player->company_id);
            $today = now_to_local_date($player->time_zone, $dst);
        }

        $timeslots = $this->program->do_get_today_timeslots($player, $today);


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $count_num = 0;

        $exline = 1;
        $campaignstart = 1;
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(60);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(60);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $fillary = array(
            'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'E0EEE0'
            )
        );
        $dedi_fillary = array(
            'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'FFDAB9'
            )
        );


        if ($timeslots) {
            foreach ($timeslots as $slot) {
                $medias = $this->program->get_sorted_timeslot_medias($slot, $today);

                $now_hour = strtotime(date("Y-m-d", time()) . sprintf(" %02d:%02d:00", $slot->startH, $slot->startM));
                foreach ($medias as $media) {
                    if ($media->date_flag && ($today < $media->start_date || $today > $media->end_date)) {
                        continue;
                    }

                    $spreadsheet->getActiveSheet()->setCellValue('A' . $exline, date("H:i:s", $now_hour))
                        ->setCellValue('B' . $exline, $media->name)
                        ->setCellValue('C' . $exline, $media->playlist_name)
                        ->setCellValue('D' . $exline, floor($media->play_time));
                    /*
                    if($slot->campaigns&&isset($slot->campaigns[0])&&$slot->campaigns[0]['priority']==0){
                        $spreadsheet->getActiveSheet()->getStyle(sprintf("A%d:D%d",$campaignstart,$exline-1))->getFill()->applyFromArray($dedi_fillary);
                    }
                    else
                    */
                    if ($count_num % 2 == 1) {
                        $spreadsheet->getActiveSheet()->getStyle(sprintf("A%d:D%d", $campaignstart, $exline - 1))->getFill()->applyFromArray($fillary);
                    }
                    $now_hour += $media->play_time;
                    $exline++;
                }


                $campaignstart = $exline;
                $count_num++;
            }
        } else {
        }
        unset($timeslots);
        $writer = new Xlsx($spreadsheet);
        unset($spreadsheet);

        header("Pragma: public");
        header("Expires: 0");
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Headers:content-type');
        header('Access-Control-Allow-Credentials:true');
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header("Content-Disposition: attachment;filename=PlaySchedule(" . $player->name . ")_" . $today . ".xlsx");
        header("Content-Transfer-Encoding:binary");
        $writer->save('php://output');
        exit();
    }

    public function schedule_date()
    {
        $id = $this->input->get('id');
        $data = array();
        $data['id'] = $id;

        $this->load->view('org/player/export_schedule', $data);
    }



    public function upload_photo()
    {
        $preview = $config = $errors = [];
        $index  = 0;
        $pid = $this->input->post('pid');
        $targetDir = './resources/playerPic/' . $pid;

        if (!file_exists($targetDir)) {
            if (!@mkdir($targetDir, 0755, true)) {
                $ret =  [
                    'error' => 'Failed to creat directory'
                ];
                echo json_encode($ret);
                return;
            }
        }
        $fileBlob = 'fileBlob';                      // the parameter name that stores the file blob
        if (isset($_FILES[$fileBlob])) {
            $file = $_FILES[$fileBlob]['tmp_name'];  // the path for the uploaded file chunk 

            $fileName = $_POST['fileName'];          // you receive the file name as a separate post data
            $fileSize = $_POST['fileSize'];          // you receive the file size as a separate post data
            $realName = $_POST['fileId'];              // you receive the file identifier as a separate post data

            $index =  $_POST['chunkIndex'];          // the current file chunk index

            $totalChunks = $_POST['chunkCount'];     // the total number of chunks for this file
            $targetFile = $targetDir . '/' . $fileName;  // your target file path
            if ($totalChunks > 1) {                  // create chunk files only if chunks are greater than 1
                $targetFile .= '_' . str_pad($index, 4, '0', STR_PAD_LEFT);
            }

            if (move_uploaded_file($file, $targetFile)) {
                // get list of all chunks uploaded so far to server
                $chunks = $totalChunks == 1 ? glob("{$targetDir}/{$fileName}*") : glob("{$targetDir}/{$fileName}_*");
                // check uploaded chunks so far (do not combine files if only one chunk received)
                $allChunksUploaded = $totalChunks >= 1 && count($chunks) == $totalChunks;

                if ($allChunksUploaded) {           // all chunks were uploaded

                    $outFile = $targetDir . '/' . $realName;
                    // combines all file chunks to one file
                    $this->combineChunks($chunks, $outFile);
                    $data['player_id'] = $pid;
                    $data['name'] = $realName;
                    $data['ori_name'] = $fileName;
                    $this->load->model('device');
                    $fileId = $this->device->add_player_pic($data);

                    if ($fileId) {
                        // if you wish to generate a thumbnail image for the file
                        $targetUrl = ''; //getThumbnailUrl($path, $fileName);
                        $preview[] = '/resources/playerPic/' . $pid . '/' . $realName;
                        $config[] =        [
                            'type' => 'image',      // check previewTypes (set it to 'other' if you want no content preview)
                            'caption' => $fileName, // caption
                            'key' => $fileId,       // keys for deleting/reorganizing preview
                            'fileId' => $fileId,    // file identifier
                            'size' => $fileSize,    // file size
                        ];
                    } else {
                        $errors[] = 'Error updating database';
                    }
                }
            } else {
                $ret =  [
                    'error' => 'Error uploading chunk ' . $_POST['chunkIndex']
                ];
                $errors[] = 'Error uploading chunk ' . $_POST['chunkIndex'];
            }
        }

        if (!empty($errors)) {
            $out = ['errors' => $errors];
        } else {
            $out = [
                'chunkIndex' => $index,
                'initialPreview' => $preview,
                'initialPreviewConfig' => $config,
                'append' => true,
            ];
        }

        echo json_encode($out);
    }
    public function get_player_pictures()
    {
        $id = $this->input->get('id');
        $initialPreview = [];
        $initialPreviewConfig = [];
        $this->load->model('device');
        $pics = $this->device->get_player_pics($id);
        if ($pics) {
            $path = '/resources/playerPic/';
            foreach ($pics as $pic) {
                $url =  $path . $pic->player_id . '/' . $pic->name;
                $initialPreview[] =  $url;
                $config = array('key' => $pic->id, 'caption' => $pic->ori_name);
                $initialPreviewConfig[] = (object)$config;
            }
        }
        $data['initialPreview'] = $initialPreview;
        $data['initialPreviewConfig'] = $initialPreviewConfig;
        echo json_encode($data);
    }

    public function delete_picture()
    {
        $this->load->model('device');
        $id = $this->input->post('key');

        $pic = $this->device->get_player_pic_byId($id);

        if ($pic) {
            if ($this->device->delete_player_pic($id)) {
                $path = './resources/playerPic/' . $pic->player_id . '/' . $pic->name;
                if (file_exists($path)) {
                    unlink($path);
                }
                echo json_encode([]);
                return;
            }
        }
        $ret =  [
            'error' => 'Failed in deleting'
        ];
        echo json_encode($ret);
    }


    public function export_player()
    {
        $this->load->model('membership');
        $this->load->model('device');
        $filter = $this->input->get('filter');

        $online = $this->input->get('online');

        $tagid  = $this->input->get('tag_id');
        $criterion_id = $this->input->get('criterion_id');

        $filter_array = array();

        if ($filter != null) {
            $filter_array['filter'] = $filter;
        }
        if ($online) {
            $filter_array['online'] = $online;
        }

        if ($criterion_id) {
            $filter_array['criteria'] = $criterion_id;
        }

        if ($tagid) {
            $filter_array['tag'] = $tagid;
        }



        $cid = $this->get_cid();
        $result = $this->device->get_player_list($cid, $filter_array, 2)['data'];



        $company = $this->membership->get_company($cid);
        if ($this->config->item('ssp_feature') && $company->sspfeature) {
            foreach ($result as $player) {
                $player->sspcriteria = $this->device->get_sspcriteria_ids_by_player($player->id, 1);
                $player->ssptags = $this->device->get_ssptags_ids_by_player($player->id, 1);
            }
        }



        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->getDefaultColumnDimension()->setWidth(15);

        $worksheet->getColumnDimension('A')->setWidth(20);
        $worksheet->getStyle('A')->getNumberFormat()->setFormatCode('#');

        $worksheet->getColumnDimension('B')->setWidth(20);
        $worksheet->getStyle('B')->getNumberFormat()->setFormatCode('#');



        $worksheet->getColumnDimension('C')->setWidth(40);
        $worksheet->getStyle('C')->getAlignment()->setWrapText(true);

        $worksheet->getColumnDimension('E')->setWidth(40);
        $worksheet->getStyle('E')->getAlignment()->setWrapText(true);

        $worksheet->getColumnDimension('L')->setWidth(40);
        $worksheet->getStyle('L')->getAlignment()->setWrapText(true);

        $worksheet->getColumnDimension('AE')->setWidth(40);
        $worksheet->getStyle('AE')->getAlignment()->setWrapText(true);


        $col_index = 1;
        if ($this->config->item("with_template")) {
            $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('store.id'));
            $worksheet->setCellValueByColumnAndRow($col_index++, 1,  $this->lang->line('store.display_id'));
        } else {
            $worksheet->setCellValueByColumnAndRow($col_index++, 1, (isset($company->cust_player_field1) && $company->cust_player_field1) ? $company->cust_player_field1 : $this->lang->line('custom_sn1'));
            $worksheet->setCellValueByColumnAndRow($col_index++, 1, (isset($company->cust_player_field2) && $company->cust_player_field2) ? $company->cust_player_field2 : $this->lang->line('custom_sn2'));
        }
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('player.name'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('sn'));


        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('criteria'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('timer.settings'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('screen.type'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('desc'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('player_conname'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('player_conphone'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('player_conemail'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('player_conaddr'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('street_num'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('player_connzipcode'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('player_contown'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('player_barcode'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('displaynum'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('player_simno'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('sim_volume'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('item_num'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('model_name'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('screen_size'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('side'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('partner_id'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('location_id'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, 'Lat.');
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, 'Lng.');
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('setup_date'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('view_direction'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('pps'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('visitors'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('tag'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('ssp.criteria'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('ssp.tags'));
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, "firmware verison");
        $worksheet->setCellValueByColumnAndRow($col_index++, 1, $this->lang->line('last.maintenance'));

        if ($result) {
            $exline = 2;
            foreach ($result as $row) {
                $worksheet->setCellValue('A' . $exline, $row->custom_sn1)
                    ->setCellValue('B' . $exline, $row->custom_sn2)
                    ->setCellValue('C' . $exline, $row->name)
                    ->setCellValue('D' . $exline, $row->sn)
                    ->setCellValue('E' . $exline, $row->criteria_name)
                    ->setCellValue('F' . $exline, $row->timecfg)
                    ->setCellValue('G' . $exline, $row->screen_oritation == 0 ? '1080X1920' : '1920X1080')
                    ->setCellValue('H' . $exline, $row->descr)
                    ->setCellValue('I' . $exline, $row->conname)
                    ->setCellValue('J' . $exline, $row->conphone)
                    ->setCellValue('K' . $exline, $row->conemail)
                    ->setCellValue('L' . $exline, $row->conaddr)
                    ->setCellValue('M' . $exline, $row->street_num)
                    ->setCellValue('N' . $exline, $row->conzipcode)
                    ->setCellValue('O' . $exline, $row->contown)
                    ->setCellValue('P' . $exline, $row->barcode)
                    ->setCellValue('Q' . $exline, $row->displaynum)
                    ->setCellValue('R' . $exline, $row->simno)
                    ->setCellValue('S' . $exline, $row->simvolume)
                    ->setCellValue('T' . $exline, $row->itemnum)
                    ->setCellValue('U' . $exline, $row->modelname)
                    ->setCellValue('V' . $exline, $row->screensize)
                    ->setCellValue('W' . $exline, $row->sided == 0 ? 'Single' : 'Double')
                    ->setCellValue('X' . $exline, $row->partnerid)
                    ->setCellValue('Y' . $exline, $row->locationid)
                    ->setCellValue('Z' . $exline, $row->geox)
                    ->setCellValue('AA' . $exline, $row->geoy)
                    ->setCellValue('AB' . $exline, ($row->setupdate && $row->setupdate != '0000-00-00') ? $row->setupdate : '')
                    ->setCellValue('AC' . $exline, $row->viewdirection)
                    ->setCellValue('AD' . $exline, $row->pps)
                    ->setCellValue('AE' . $exline, $row->visitors)
                    ->setCellValue('AF' . $exline, $row->tags)
                    ->setCellValue('AG' . $exline, isset($row->sspcriteria) ? $row->sspcriteria : '')
                    ->setCellValue('AH' . $exline, isset($row->ssptags) ? $row->ssptags : '')
                    ->setCellValue('AI' . $exline, $row->firmver)
                    ->setCellValue('AJ' . $exline, $row->last_maintenance ?: "");



                $exline++;
            }
        }
        $writer = new Xlsx($spreadsheet);
        unset($spreadsheet);

        header("Pragma: public");
        header("Expires: 0");
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Headers:content-type');
        header('Access-Control-Allow-Credentials:true');
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header("Content-Disposition: attachment;filename=PlayerList($company->name)_" . date("Y-m-d") . ".xlsx");
        header("Content-Transfer-Encoding:binary");
        $writer->save('php://output');
        exit();
    }


    public function player_map($target)
    {
        $this->lang->load('campaign');
        $data = $this->get_data();
        $data['target'] = $target;
        $pid = $this->get_parent_company_id();
        if ($pid > 0) {
            $cid = $pid;
        } else {
            $cid = $this->get_cid();
        }
        $cris = $this->get_criteria($this->get_cid(), $pid);

        $data['criteria'] = $cris['criteria'];
        $data['tags'] = $this->device->get_tag_list($cid)['data'];

        $this->load->view('bootstrap/players/player_map', $data);
    }

    public function players_in_place()
    {
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $addr = $this->input->post('search');
        $radius = $this->input->post('radius');
        $city = $this->input->post('city');
        $zipcode = $this->input->post('zipcode');
        $criteria = $this->input->post('criteria');
        $and_criteria = $this->input->post('bind_criteria');
        $and_criteria_or = $this->input->post('bind_criteria_or');

        $ex_criteria = $this->input->post('ex_criteria');
        $tags = $this->input->post('tags');
        $sdawid = $this->input->post('sdaw');
        $minpps = $this->input->post('pps');


        $this->load->model("device");
        $pid = $this->get_parent_company_id();

        $cris = $this->get_criteria($this->get_cid(), $pid);
        $filter_array = array();
        $data['criteria'] = $cris['criteria'];
        if (isset($cris['filter_array'])) {
            $filter_array = array_merge($filter_array, $cris['filter_array']);
        }

        if (!empty($addr)) {
            $filter_array['filter_type'] = 'fourfields';
            $filter_array['filter'] = $addr;
        }

        if (!empty($criteria)) {
            $filter_array['criteria'] = $criteria;
        }
        if (!empty($and_criteria)) {
            $filter_array['and_criteria'] = $and_criteria;
        }

        if (!empty($and_criteria_or)) {
            $filter_array['and_criteria_or'] = $and_criteria_or;
        }


        if (!empty($ex_criteria)) {
            $filter_array['ex_criteria'] = $ex_criteria;
        }
        if (!empty($tags)) {
            $filter_array['tag'] = $tags;
        }

        if (!empty($sdawid)) {
            $filter_array['sdawids'] = explode(PHP_EOL, $sdawid);
        }
        if (!empty($minpps)) {
            $filter_array['minpps'] = $minpps;
        }

        $main_campaign_id = $this->input->post('main_campaign_id');
        if ($main_campaign_id && $main_campaign_id > 0) {
            $filter_array['main_campaign_id'] = $main_campaign_id;
        }

        $show_all = $this->input->post('show_all');

        if (!$show_all) {
            $filter_array['setupdate'] = date("Y-m-d");
        }

        $pid = $this->get_parent_company_id();
        if ($pid > 0) {
            $cid = $pid;
        } else {
            $cid = $this->get_cid();
        }
        $company_id = $this->input->post('company_id');
        if ($company_id) {
            $company = $this->membership->get_company($company_id);

            if ($company->pId > 0) {
                $cris = $this->get_criteria($company_id, $company->pId);
                if (isset($cris['filter_array'])) {
                    $filter_array = array_merge($filter_array, $cris['filter_array']);
                }
                $cid = $company->pId;
            } else {
                $cid = $company_id;
            }
        }


        $players  = $this->device->get_player_list($cid, $filter_array, true);

        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $radius = $this->input->post('radius');



        $result_players = $players['data'];


        if ($players['total'] > 0 && $lat && $lng && $radius) {

            foreach ($result_players as $player) {
                if (is_numeric($player->geox) && is_numeric($player->geoy)) {
                    $player->distance = $this->device->calDistance($player->geox, $player->geoy, $lat, $lng);
                }
            }

            $radius = $radius * 1000;

            $result_players = array_filter($result_players, function ($value) use ($radius) {
                if (isset($value->distance) && $value->distance <= $radius) {
                    return true;
                }
                return false;
            });
            $result_players = array_merge($result_players);
        }
        $data['rows'] = $result_players;
        //$data['target'] = $this->input->post('target');
        echo json_encode($data);
        //$this->load->view('org/player/selected_player_list', $data);
    }


    /**
     * Import players
     * Player name is made so:Stadt (= City) + Adress + optionally: number of player with same name and adress in brackets, e.g. (1)
     *
     * @return void
     */
    public function import_players()
    {
        $config['upload_path'] = '/tmp';
        $config['allowed_types'] = 'xls|xlsx';
        $config['max_size'] = 10485760; //10MB
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        $this->load->model("device");
        $cid = $this->get_cid();

        if (!$this->upload->do_upload('file')) {
            $result = array('code' => 1, 'msg' => $this->upload->display_errors());
        } else {
            $data = $this->upload->data();
            /**  Identify the type of $inputFileName  **/
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($data['full_path']);
            /**  Create a new Reader of the type that has been identified  **/
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            /**  Advise the Reader that we only want to load cell data  **/
            $reader->setReadDataOnly(true);

            /**  Load $inputFileName to a Spreadsheet Object  **/

            $spreadsheet = $reader->load($data['full_path']);
            $worksheet = $spreadsheet->getActiveSheet();
            // Get the highest row and column numbers referenced in the worksheet
            $highestRow = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'


            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // e.g. 5


            if ($highestColumnIndex <= 16) {
                $result = array('code' => 1, 'msg' => 'Invalid file format!');
                echo json_encode($result);
                return;
            }

            //from second line
            $succeed_cnt = 0;
            $skiped_cnt = 0;
            $failed_cnt = 0;
            $required_field_cnt = 0;


            for ($row = 2; $row <= $highestRow; $row++) {
                $player = array();
                $extra = array();
                $sdawid = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                $qid = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                $city = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                $address = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                $pps = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                $timer_name =  $worksheet->getCellByColumnAndRow(20, $row)->getValue();
                $setupdate = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                $criteria = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                $criids = false;


                $player['name'] =  $city . " " . $address;

                $timer = false;
                if ($timer_name) {
                    $this->load->model('strategy');

                    if (is_numeric($timer_name)) {
                        $timer_id = $timer_name;
                        $timer = $this->strategy->get_timer($timer_id);
                        if (!$timer) {
                            $timer = $this->strategy->get_timer_by_name(0, $cid, $timer_name);
                        }
                    } else {
                        $timer = $this->strategy->get_timer_by_name(0, $cid, $timer_name);
                    }
                    if ($timer) {
                        $player['timer_config_id'] = $timer->id;
                    }
                }

                if ($criteria) {
                    $criids = $this->device->get_criteria_id_byName($cid, $criteria);
                }


                if ($this->config->item('digooh_player_form_validation')) {
                    if (!$sdawid || !$qid || !$pps || !$timer_name || !$setupdate || !$player['name'] || !$timer || !$criids) {
                        $skiped_cnt++;
                        $required_field_cnt++;
                        continue;
                    }
                } else {
                    if (!$qid || !$player['name']) {
                        $required_field_cnt++;
                        $skiped_cnt++;
                        continue;
                    }
                }



                $extra = array(
                    "custom_sn1" => $sdawid,
                    'custom_sn2' => $qid,
                    'contown' => $city,
                    'conaddr' => $address,
                    'conzipcode' => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
                    'state' => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
                    'geox' => $worksheet->getCellByColumnAndRow(7, $row)->getValue(), //LAT
                    'geoy' => $worksheet->getCellByColumnAndRow(8, $row)->getValue(), //LNG
                    'pps' => $worksheet->getCellByColumnAndRow(9, $row)->getValue(),
                    'barcode' => $worksheet->getCellByColumnAndRow(10, $row)->getValue(),
                    /*
                        11: descripiont
                        12: createria
                        16: Setup Date
                        21: 
                    */
                    'modelname' => $worksheet->getCellByColumnAndRow(13, $row)->getValue(),
                    'viewdirection' => $worksheet->getCellByColumnAndRow(14, $row)->getValue(),
                    'locationid' => $worksheet->getCellByColumnAndRow(15, $row)->getValue(),
                    //'barcode' => $worksheet->getCellByColumnAndRow(17, $row)->getValue(),
                    'displaynum' => $worksheet->getCellByColumnAndRow(17, $row)->getValue(),
                    'visitors' => $worksheet->getCellByColumnAndRow(18, $row)->getValue(),
                    'street_num' => $worksheet->getCellByColumnAndRow(19, $row)->getValue(),
                );

                if (is_numeric($setupdate)) {
                    $extra['setupdate'] = date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($setupdate));
                } else {
                    $extra['setupdate'] = $setupdate;
                }


                if ($this->device->check_player_exist_for_importing($extra['custom_sn1'])) {
                    $skiped_cnt++;

                    continue;
                }

                $num = $this->device->get_last_append_num_of_player_name($cid, $player['name']);

                if ($num !== false) {
                    $player['name'] = $player['name'] . "(" . ($num + 1) . ")";
                }


                $player['descr'] = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                $player['company_id'] = $cid;

                $player['sn'] = $this->device->get_player_new_code($cid);
                $player['add_time'] = date('Y-m-d H:i:s');



                $add_id = $this->device->add_player($player, $this->get_uid(), $criids);

                if (!$add_id) {
                    $player['sn'] =  $this->device->get_player_new_code($cid);
                    $add_id = $this->device->add_player($player, $this->get_uid(), $criids);
                }

                if ($add_id) {
                    $extra['player_id'] = $add_id;

                    $add_id = $this->device->add_player_extra($extra);
                    $succeed_cnt++;
                } else {
                    $failed_cnt++;
                }
            }

            $msg = "New addition has $succeed_cnt players, and skip $skiped_cnt players.";
            $failed_msg = ($failed_cnt) ? "<br/>Failed: $failed_cnt players" : "";

            $result = array('code' => 0, 'msg' => $msg . $failed_msg);
        }
        echo json_encode($result);
    }

    public function update_amc_while_changing_timer($timer_id)
    {
        $this->load->model('device');
        $amc = $this->device->get_amc_from_timer($timer_id);
        echo json_encode($amc);
    }

    public function getPeripheriesData()
    {
        $this->load->model('peripheral');
        $player_id = $this->input->post('player_id');
        $limit = $this->input->post('limit');
        $offset = $this->input->post('offset');
        $data = $this->peripheral->get_player_peripherals($player_id, $offset, $limit);


        if ($data['total'] > 0) {
            foreach ($data['data'] as $p) {

                $commands = $this->peripheral->get_peripheral_command_list($p->id);
                $p->commands = $commands['data'];
            }
        }

        $result['total'] = $data['total'];
        $result['rows'] = $data['data'];
        echo json_encode($result);
    }


    public function rs485_control()
    {
        $players = $this->input->post('players');
        $command_id = $this->input->post('command');

        $this->load->model('device');

        $this->load->model('peripheral');
        $settings = $this->peripheral->get_command_and_settings($command_id);
        $settings->commandToApk = 0x18;


        $this->device->send_command_new($players, $settings);
    }

    public function refresh_player()
    {
        $this->load->model('program');
        $id = $this->input->get('id');
        $method = $this->input->get('method');
        $cid = $this->get_cid();

        if ($this->config->item('with_partners')) {
            if (!$this->get_parent_company_id()) {
                $this->load->model('membership');
                $partners = $this->membership->get_all_partners($cid);
                if ($partners) {
                    array_push($partners, $cid);
                    $cid = $partners;
                }
            }
        }
        if ($method == 'edit') {
            //Delete planed record and leaset free for the player
            $this->program->delete_planed_record(false, date('Y-m-d H:i:s'), $id);
            $this->program->delete_player_least_free($id);
        }
        $ret = $this->program->refresh_campaigns_by_player($cid, $id);

        $msg = $this->lang->line('player.refresh.result');
        if (count($ret['refreshed_list']) > 0) {
            $msg .= implode("<br/>", $ret['refreshed_list']);
        } else {
            $msg .= " ";
        }
        if (count($ret['failed_list']) > 0) {
            $msg .= '<font color="red">';
            $msg .= "<br/>Failed camapigns: <br/>" . implode("<br/>", $ret['failed_list']);
            $msg .= "</font>";
        }
        echo json_encode(array('code' => "success", 'msg' => $msg));
    }
}
