<?php

use Swoole\Database\MysqliConfig;
use Swoole\Database\MysqliPool;

require_once('utils.php');
require_once('swoole_config.php');



const CLOGIN = 0x01;
const CHEARTBEAT = 0x02;
const CCONTROL = 0x03;
const CINSTANTSCH = 0x04;
const CLOGINNEW = 0x05;
const SActivate = 0x06;
const ConsumerCode = 0x07;
const IActivate = 0x08;
const SERVERCONTRL = 0x0f;
const REFRESHPL = 0x0e;

$server = new Swoole\Server('0.0.0.0', $server_port, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

$server->addlistener("0.0.0.0", $server_port, SWOOLE_SOCK_UDP); // UDP

$server->fdlist = [];
$server->dbPool = new MysqliPool((new MysqliConfig)
        ->withHost($db_server['host'])
        ->withPort($db_server['port'])
        ->withDbName($db_server['name'])
        ->withCharset('utf8')
        ->withUsername($db_server['user'])
        ->withPassword($db_server['password'])
);

$server->set([
    'daemonize' => false,
    'worker_num' => swoole_cpu_num() * 2,                 //how much worker will start,base on your cpu nums
    'reactor_num' => swoole_cpu_num() * 2,                // depend cpu how much cpu you have
    'open_cpu_affinity' => 1,          //get cpu more time
    'open_tcp_nodelay' => 1,           // for small packet to open
    //'tcp_defer_accept' => 5,
    'tcp_fastopen' => true,
    'max_conn' => 4096,
    'open_length_check' => true,
    'package_max_length' => 512,
    'package_length_type' => 'C',      //see php pack()
    'package_length_offset' => 3,
    'package_body_offset' => 6,
    'heartbeat_check_interval' => 1800, //设置心跳检测间隔
    'heartbeat_idle_time' => 3600, //5分钟无数据断开
    //'log_level' => SWOOLE_LOG_ERROR,
    'log_date_format' => '%Y-%m-%d %H:%M:%S',
    'enable_coroutine' => true,
    'hook_flags' => SWOOLE_HOOK_ALL,
    'log_file' => '/tmp/swoole.' . date('Y-m-d') . '.log',
    'stats_file' => '/tmp/stats.log',
]);


$server->on('start', function ($serv) {
    echo "Swoole server is started at " . date("Y-m-d H:i:s") . PHP_EOL;
});

$server->on('connect', function ($server, $fd) {
    //echo "Client:Connect.\n";
});

$server->on('receive', function ($serv, $fd, $reactor_id, $data) {
    $crcstr = unpack('C2', substr($data, -2));
    $crc1 = (($crcstr[1] << 8) & 0xff00) | $crcstr[2];
    $crc2 = crc16(substr($data, 0, -2));

    if ($crc1 != $crc2) {
        echo "[" . date("Y-m-d H:i:s") . "]  [Receive] Crc check fail!\n";
        return;
    }

    $package = unpack('Cstart1/Cstart2/Ccomm/Clenth', $data);
    if (empty($package)) {
        echo "[" . date("Y-m-d H:i:s") . "]  [Receive] Failed to unpack package!\n";
        return;
    }
    if ($package['start1'] != 0xec && $package['start1'] != 0xeb) {
        echo "[" . date("Y-m-d H:i:s") . "]  [Receive] Illegal package!\n";
        return;
    }

    if (!isset($serv->fdlist[$fd])) {
        $serv->fdlist[$fd] = $fd;
    }

    $datastream = blowfish_dec(substr($data, 4, -2));

    switch ($package['comm']) {
        case CLOGIN:
        case CLOGINNEW:
            onLogin($serv, $fd, $datastream, $package['lenth']);
            break;
        case CHEARTBEAT:
            //$this->onHeartBeat($serv, $fd, $from_id, $datastream);
            onHeartBeat($serv, $fd, $datastream, $package['lenth']);
            break;
        case SERVERCONTRL:
            onControl($serv, $datastream);
            break;
        case CINSTANTSCH:
            break;
        case ConsumerCode:
            break;
        case SActivate: //标准终端激活
        case IActivate: //互动终端激活
            onActivate($serv, $fd,  $datastream, $package['comm']);
            break;
        default:
            break;
    }
});

//UDP Control command from ICAT 
$server->on('packet', function ($serv, $data, $clientInfo) {
    $recv = json_decode($data, true);

    if (!$recv || !isset($recv['command'])) {
        return;
    }
    $command = $recv['command'];
    //echo "[" . date("Y-m-d H:i:s") . "]  [onPacket] command=" . $command . "\n";
    switch ($command) {
        case 0x3:
            $data = pack('Ca4Ca10CC', 0x00, '1234', 10, $recv['sn'], $recv['type'], $recv['value']);
            break;
        case 0x4:
            $data = pack('Ca4Ca10', 0x00, '1234', 10, $recv['sn']);
            break;
    }

    $encdata = blowfish_enc($data);  //blowfish 加密DATA数据
    $length = strlen($encdata);

    $header = pack('CCCC', 0xec, 0xeb, $command, $length);

    $msg = $header . $encdata;
    $crc = crc16($msg); //CRC校验
    $controlMsg = $msg . pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff)); //拼装数据包

    $ret = $serv->send($recv['fd'], $controlMsg);
    if ($ret === false) {
        echo "[" . date("Y-m-d H:i:s") . "]  [onControl] send failed! command=$command\n";
    }
});
$server->on('close', function ($server, $fd) {
    unset($server->fdlist[$fd]);
});


$server->start();


function onHeartBeat($serv, $fd, $data, $length)
{
    //echo "[" . date("Y-m-d H:i:s") . "]  [Heartbeat] Got Heart beat! fd=" . $fd . " \n";
    $respval = 0;
    $loginpara_tmp = unpack('Cstype/a4netid/Csnlen/a10sn/Cstatus/a8voltage/a8elec/Cfan/a8discspace/Cwet/Ctemp/Cdownnum/Coffstate/Cplen', $data);

    if ($length <= 64) {
        $loginpara = unpack('Cstype/a4netid/Csnlen/a10sn/Cstatus/a8voltage/a8elec/Cfan/a8discspace/Cwet/Ctemp/Cdownnum/Coffstate/Cplen/a' . $loginpara_tmp['plen'] . 'plsName/Cpmodel', $data);
    } else {
        $loginpara = unpack('Cstype/a4netid/Csnlen/a10sn/Cstatus/a8voltage/a8elec/Cfan/a8discspace/Cwet/Ctemp/Cdownnum/Coffstate/Cplen/a' . $loginpara_tmp['plen'] . 'plsName/Cpmodel/a8wetnew/a8tempnew/Cbrightnew', $data);
        if (!$loginpara) {
            $loginpara = unpack('Cstype/a4netid/Csnlen/a10sn/Cstatus/a8voltage/a8elec/Cfan/a8discspace/Cwet/Ctemp/Cdownnum/Coffstate/Cplen/a' . $loginpara_tmp['plen'] . 'plsName/Cpmodel', $data);
        }
    }

    //$this->my_memcache_set($loginpara['sn'], $fd);
    if (empty($loginpara)) {
        $respval = 1; //fail
        echo "[" . date("Y-m-d H:i:s") . "] [Heartbeat] Empty package!\n";
    }
    $data = pack('Ca4Ca10C', 0x00, $loginpara['netid'], $loginpara['snlen'], $loginpara['sn'], $respval);
    $encdata = blowfish_enc($data);  //blowfish 加密DATA数据
    $length = strlen($encdata);
    $header = pack('CCCC', 0xec, 0xeb, CHEARTBEAT, $length);
    $msg = $header . $encdata;
    $crc = crc16($msg);   //CRC校验
    $loginmsg = $msg . pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff));  //拼装数据包

    $output = $length + 3;

    $serv->send($fd, $loginmsg);
    //sendMsg($serv, $fd, $loginmsg);

    if ($respval == 1) {
        return;
    }


    $status = $loginpara['status'];       //状态
    $voltage = $loginpara['voltage'];     //电压
    $elec = $loginpara['elec'];           //电流
    $fan = $loginpara['fan'];             //风扇状态
    $discspace = $loginpara['discspace']; //磁盘空间
    $wet = $loginpara['wet'];             //湿度
    $temp = $loginpara['temp'];           //温度
    $downnum = $loginpara['downnum'];     //完整度
    $offstate = $loginpara['offstate'];   //断电状态
    $plsName = $loginpara['plsName'];     //播放列表名称
    $pmodel = $loginpara['pmodel']; //终端类型 1:HDMI_50Hz 2:HDMI_60Hz


    if ($pmodel > 2 || $pmodel < 0) {
        $pmodel = 2;
    }

    //echo "[".date("Y-m-d H:i:s")."] [Heartbeat] Got Heart beat!  fd=".$fd.",SN=".$loginpara['sn']." ,status=".$status." ,pls=".$plsName." \n";

    switch ($status) {
        case 11:
            $status = 1011;  //一天未登录
            break;
        case 12:
            $status = 1012;  //多天未登录
            break;
        case 13:
            //$status = 1013;  //忽略状态只记录完整度
            $status = 127;
            break;
        case 14:
            $status = 127;
            break;
        case 16:
            $status = 1016;  //默认
            break;
    }

    //终端状态写入cat_player_log

    $mysqli = $serv->dbPool->get();
    if ($status != 1011 && $status != 1012 && $status != 1013 && $status != 1014 && $status != 1016) {
        $sql_player = "select id, name, status from cat_player where sn = '" . $loginpara['sn'] . "'";


        $result = $mysqli->query($sql_player);

        if ($result && $result->num_rows) {

            $player = $result->fetch_object();
            $pls = ' ';

            if ($status != $player->status || ($status == 2 || $status == 6/*&&$plsName!='null'&&$plsName != 'NULL'*/)) {
                if ($plsName == 'NULL' || $plsName == 'null') {
                    $plsName = '';
                }
                if ($plsName != '' && ($status == 2 || $status == 6)) {
                    $pls = ' playlist [' . $plsName . ']';
                }

                $status_str = "unkonwn";
                switch ($status) {
                    case 1:
                        $status_str = "Offline";
                        break;
                    case 2:
                        $status_str = "Playing";
                        break;
                    case 3:
                        $status_str = "Downloading";
                        break;
                    case 4:
                        $status_str = "Stop";
                        break;
                    case 5:
                        $status_str = "Online";
                        break;
                    case 6:
                        $status_str = "Playing";
                        break;
                    case 7:
                        $status_str = "Exception";
                        break;
                    case 9:
                        $status_str = "Login";
                        break;
                    case 10:
                        $status_str = "Sign out";
                        break;
                    case 20:
                        $status_str = " HDMI-Input starts...";
                        break;
                    case 21:
                        $status_str = " HDMI-Input end";
                        break;
                    case 127:
                        $status_str = "Idle(Sleep Mode)";
                        break;
                }

                $p_str = "Player[" . $player->name . "] Status: " . $status_str . $pls;
                $utf8_str = mb_convert_encoding($p_str, "UTF-8");
                $sql_log = "INSERT INTO `cat_player_log`(`player_id` ,`event_type` ,`detail`, `add_time`)VALUES (" . $player->id . ", 2, '" . $utf8_str . "', '" . date('Y-m-d H:i:s') . "')";
                $mysqli->query($sql_log);
            }
        }
    }
    //更新Player状态

    $sql = "UPDATE cat_player 
            SET model=" . $pmodel . ", status=" . $status . ", voltage='" . $voltage . "', electric='" . $elec . "', fan=" . $fan . ", disk_free='" . $discspace .
        "', humidity=" . $wet . ", temperature='" . $temp . "', downloaddnum='" . $downnum . "', offstate='" . $offstate .
        "', last_connect='" . date('Y-m-d H:i:s') . "',socket_fd='" . $fd;

    if (isset($loginpara['wetnew'])) {
        $tempwet = intval($loginpara['wetnew']);
        if ($tempwet != 0) {
            //$realvalue = -6 + 125 * ($tempwet / 65536);
            $realvalue = $tempwet / 1000;
            $sql .= "', dampness='" .  round($realvalue, 2);
        }
    }
    if (isset($loginpara['tempnew'])) {
        $tempint = intval($loginpara['tempnew']);
        if ($tempint != 0) {
            $realvalue = $tempint / 1000;
            $sql .= "', temp='" . round($realvalue, 2);
        }
    }
    if (isset($loginpara['brightnew'])) {
        $sql .= "', brightness='" .  $loginpara['brightnew'];
    }


    $sql .= "' WHERE SN = '" . $loginpara['sn'] . "';";

    $mysqli->query($sql);
    $serv->dbPool->put($mysqli);
}

function onLogin($serv, $fd, $data, $input)
{
    //echo "[" . date("Y-m-d H:i:s") . "]  [Logging] Got login msg! fd=" . $fd . " \n";
    $result = array();
    $respval = 0;
    $loginpara_tmp1 = unpack('Cstype/a4netid/Csnlen/a10sn/Cmodel/a17mac/Cverlen', $data);
    $loginpara_tmp2 = unpack('Cstype/a4netid/Csnlen/a10sn/Cmodel/a17mac/Cverlen/a' . $loginpara_tmp1['verlen'] . 'ver/Csense/Cfirmverlen', $data);
    $loginpara = unpack('Cstype/a4netid/Csnlen/a10sn/Cmodel/a17mac/Cverlen/a' . $loginpara_tmp2['verlen'] . 'ver/Csense/Cfirmverlen/a' . $loginpara_tmp2['firmverlen'] . 'firmver/a8gmt/Cvol/a8disckspace/a8availableSpace/a4mpegcore', $data);
    if ($loginpara) {


        $extraPara = null;
        if ($input > 96) {
            $extraPara = unpack('C/a4/C/a10/C/a17/C/a' . $loginpara_tmp2['verlen'] . '/C/C/a' . $loginpara_tmp2['firmverlen'] . '/a8/C/a8/a8/a4/cangel/c/a9resolution/CexLen/A*extra', $data);
        }
        $mysqli = $serv->dbPool->get();
        $sql = "SELECT id,name,upgrade_version FROM cat_player WHERE SN = '" . $loginpara['sn'] . "';";

        $result = $mysqli->query($sql);

        if ($result && $result->num_rows) {

            $player = $result->fetch_object();

            if (isset($loginpara['gmt'])) {
                $gmt = preg_replace('/^0+/', '', $loginpara['gmt']);
            } else {
                $gmt = 1;
            }
            $availableSpace = preg_replace('/^0+/', '', $loginpara['availableSpace']);
            $disckspace = preg_replace('/^0+/', '', $loginpara['disckspace']);
            $space = $availableSpace . ',' . $disckspace;
            $mpegCore = 1;
            if (isset($loginpara['mpegcore'])) {
                if ($loginpara['mpegcore'] == '5166') {
                    $mpegCore = 2;
                } elseif ($loginpara['mpegcore'] == '5186') {
                    $mpegCore = 3;
                } else if ($loginpara['mpegcore'] == '3568') {
                    $mpegCore = 4;
                }
            }


            $sql = "UPDATE cat_player SET status=5, reboot_flag=0, batch_reg_status=0, mpeg_core='"
                . $mpegCore . "', mac='" . $loginpara['mac'] . "', version_length=" . $loginpara['verlen']
                . ", version='" . $loginpara['ver'] . "',firmver_length=" . $loginpara['firmverlen']
                . ", firmver='" . $loginpara['firmver'] . "', time_zone=" . $gmt . ", space='" . $space
                . "', disk_free='" . $availableSpace . "', disk_total='" . $disckspace . "', storage=" . $loginpara['vol'] . ",last_connect='"
                . date('Y-m-d H:i:s') . "', socket_fd=" . $fd;

            if ($extraPara) {
                $sql .= ", resolution='" . $extraPara['resolution'] . "',angel=" . $extraPara['angel'];
                if (isset($extraPara['extra'])) {
                    if ($extraPara['exLen']) {
                        $extraPara['extra'] = substr($extraPara['extra'], 0, $extraPara['exLen']);
                    }
                    $exPara = json_decode($extraPara['extra'], true);
                    if ($exPara) {
                        foreach ($exPara as $key => $value) {
                            $sql .= ", " . $key . "=" . '"' . $value . '"';
                        }
                    }
                }
            }

            $sql .= " WHERE SN = '" . $loginpara['sn'] . "';";

            // echo $sql . PHP_EOL;
            $mysqli->query($sql);



            if ($player->upgrade_version . 'A' == $loginpara['ver']) {
                //执行成功后，删除当前升级包
                $mysqli->query("update cat_player set upgrade_version = NULL where sn = '" . $loginpara['sn'] . "'");
            }
            $respval = 0;
            $sql = "INSERT INTO `cat_player_log`(`player_id` ,`event_type` ,`detail`, `add_time`)VALUES (" . $player->id . ", 1, 'Player[" .  $player->name . "] login successfully, mac address[" . $loginpara['mac'] . "], software version[" . $loginpara['ver'] . "]', '" . date('Y-m-d H:i:s') . "');";
            $mysqli->query($sql);
        } else {
            if (strlen($loginpara['sn']) == 10 && $loginpara['sn'] != '0010010013') {
                $ccode = substr($loginpara['sn'], 0, 3);
                $sql = "select id from cat_company where code=" . $ccode;
                $result = $mysqli->query($sql);


                if ($result && $result->num_rows) {
                    $company = $result->fetch_object();
                    $companyid = $company->id;

                    $sql = "insert into cat_player(sn,company_id) values(" . $loginpara['sn'] . "," . $companyid . ")";
                } else {
                    $sql = "insert into cat_player(sn,company_id) values(" . $loginpara['sn'] . ",0)";
                }
                $mysqli->query($sql);
                $respval = 0;
            } else {
                $respval = 1;
                echo "[" . date("Y-m-d H:i:s") . "] [logging] sn:" . $loginpara['sn'] . "  login error!\n";
            }
        }
        //2016-02-25 查找mac是否存在,存在的话，更新返回包中的sn;
        if ($loginpara['sn'] == '0010010013' && $loginpara['ver'] >= '4.0.3.0310A') {

            $temp_mac1 = str_replace(":", "-", $loginpara['mac']);
            $temp_mac2 = $loginpara['mac'];
            $mac_sql = "SELECT sn FROM cat_player WHERE sn!='0010010013' and batch_registration=1 and (mac='" . $temp_mac1 . "' or mac='" . $temp_mac2 . "') limit 0,1;";
            $mysqli->query($sql);
            $result = $mysqli->query($sql);
            if ($result && $result->num_rows) {
                $rec = $result->fetch_object();;

                $respval = 2;
                $loginpara['sn'] = $rec->sn;
            }
        }

        //echo 'reg  respval: '.$respval.', sn: '.$loginpara['sn']."\n";
        $serv->dbPool->put($mysqli);
    }
    $data = pack('Ca4Ca10C', 0x00, $loginpara['netid'], $loginpara['snlen'], $loginpara['sn'], $respval);
    $encdata = blowfish_enc($data); //blowfish 加密DATA数据
    $length = strlen($encdata);
    $header = pack('CCCC', 0xec, 0xeb, 0x05, $length);
    $msg = $header . $encdata;
    $crc = crc16($msg);  //CRC校验
    $loginmsg = $msg . pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff));  //拼装数据包

    $serv->send($fd, $loginmsg);
    //endMsg($serv, $fd, $loginmsg);
}

function onControl($serv, $datastram)
{
    $param1 = unpack('Cstype/a4netid/Csnlen/Cfdlen', $datastram);

    $control_param = unpack('Cstype/a4netid/Csnlen/Cfdlen/a' . $param1['snlen'] . 'sn/a' . $param1['fdlen'] . 'fd/Ctype/Cvalue/Ccommand', $datastram);

    $player_sn = $control_param['sn'];
    $player_fd = $control_param['fd'];
    $command = $control_param['command'] ?: 0x3;


    // echo "[" . date("Y-m-d H:i:s") . "]  [onControl] sn=" . $player_sn .  ",command=" . $command . ",type=" . $control_param['type'] . ",value=" . $control_param['value'] . " \n";
    //var_dump($control_param);

    if ($player_fd > 0) {
        if ($command == 0x3) {
            $data = pack('Ca4Ca10CC', 0x00, $control_param['netid'], 10, $player_sn, $control_param['type'], $control_param['value']);
        } else {
            $data = pack('Ca4Ca10', 0x00, $control_param['netid'], 10, $player_sn);
        }


        $encdata = blowfish_enc($data);  //blowfish 加密DATA数据
        $length = strlen($encdata);

        if ($command == 0x3) {
            $header = pack('CCCC', 0xec, 0xeb, CCONTROL, $length);
        } else {
            $header = pack('CCCC', 0xec, 0xeb, 0x04, $length);
        }
        $msg = $header . $encdata;
        $crc = crc16($msg); //CRC校验
        $controlMsg = $msg . pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff)); //拼装数据包
        //$serv->send($fd, $controlMsg);

        $ret = $serv->send($player_fd, $controlMsg);
        if ($ret === false) {
            echo "[" . date("Y-m-d H:i:s") . "]  [onControl] send failed! command=$command\n";
        }
        // sendMsg($serv, $player_fd, $controlMsg);
    }
}
function onConsumerCode($serv, $fd, $from_id, $data)
{
    echo 'ConsumerCode command\n';
}

/**
 * 终端激活处理
 */
function onActivate($serv, $fd, $data, $commn)
{
    echo "[" . date("Y-m-d H:i:s") . "] [Activate] Get Activate command!\n";
    $param1 = unpack('Cstype/a4netid/Csnlen/a10sn/Ctype/Cidlen', $data);
    $activatePara = unpack('Cstype/a4netid/Csnlen/a10sn/Ctype/Cidlen/a' . $param1['idlen'] . 'id/Cmodel/a17mac/CipLength', $data);

    $defaultFixedValue = "Sj9TiH4u";
    $rand_arr = array();  //4字节随机数
    $rand_arr[0] = rand(0, 255);
    $rand_arr[1] = rand(0, 255);
    $rand_arr[2] = rand(0, 255);
    $rand_arr[3] = rand(0, 255);
    $rand_str = toStr($rand_arr);  //随机数转换成string字符串

    $type = 0x2;
    $days = 0;
    //NP201
    $mysqli = $serv->dbPool->get();
    if ($activatePara['model'] == 0x9) {
        $sqlstr = 'SELECT * FROM cat_player_activation where mac="' . $activatePara['mac'] . '"';
        $result = $mysqli->query($sqlstr);

        if ($result && $result->num_rows) {

            $activation = $result->fetch_object();


            if ($activation->is_active == 0) {
                $type = 0; //激活失败
            } else {
                $expire_date = $activation->expire_at;
                $expire_time = strtotime($expire_date);
                $now = strtotime(date("Y-m-d", time()));
                if ($now >= $expire_time) {
                    $type = 0;
                } else {
                    $type = 2;
                    $days = intval(($expire_time - $now) / 86400);
                }
            }
        } else {
            //如果没有记录,入库并激活15天
            $type = 0x1;
            $expire_date = strtotime("+15 days");
            $datestr = date("Y-m-d", $expire_date);
            $sqlstr = "INSERT INTO  cat_player_activation" .
                " (mac,is_active,expire_at)" .
                ' VALUES ("' . $activatePara['mac'] . '"' . ",1,'$datestr')";
            $mysqli->query($sqlstr);
            $days = 15;
        }
        //计算当前时间和过期时间差
        $now = strtotime("Y-m-d");
        if ($days >= 0) {
            echo "days=" . $days . "\n";
            $time_arr = array();
            $time_arr[0] = $days & 0xff;
            $time_arr[1] = $days >> 8 & 0xff;
            $time_arr[2] = $days >> 16 & 0xff;
            $time_arr[3] = $days >> 24 & 0xff;
            $time_arr[4] = $days >> 32 & 0xff;
            $time_arr[5] = $days >> 40 & 0xff;
            $time_arr[6] = $days >> 48 & 0xff;
            $time_arr[7] = $days >> 56 & 0xff;
        }
    } else {
        $time = 10 * 365 * 24 * 60;  //默认授权期限10年
        $time_arr = array();
        $time_arr[0] = $time & 0xff;
        $time_arr[1] = $time >> 8 & 0xff;
        $time_arr[2] = $time >> 16 & 0xff;
        $time_arr[3] = $time >> 24 & 0xff;
        $time_arr[4] = $time >> 32 & 0xff;
        $time_arr[5] = $time >> 40 & 0xff;
        $time_arr[6] = $time >> 48 & 0xff;
        $time_arr[7] = $time >> 56 & 0xff;
    }
    //计算16位MD5   hid、mac、固定值(Sj9TiH4u)、随机数
    $eSign = md5($activatePara['sn'] . $activatePara['mac'] . $defaultFixedValue . $rand_str, true);
    $eSign_arr = array();
    $eSign_arr = getBytes($eSign); //转换一个String字符串为byte数组

    $data = pack('Ca4Ca10CC4C16C8', 0x00, $activatePara['netid'], 0x08, $activatePara['sn'], $type, $rand_arr[0], $rand_arr[1], $rand_arr[2], $rand_arr[3], $eSign_arr[0], $eSign_arr[1], $eSign_arr[2], $eSign_arr[3], $eSign_arr[4], $eSign_arr[5], $eSign_arr[6], $eSign_arr[7], $eSign_arr[8], $eSign_arr[9], $eSign_arr[10], $eSign_arr[11], $eSign_arr[12], $eSign_arr[13], $eSign_arr[14], $eSign_arr[15], $time_arr[7], $time_arr[6], $time_arr[5], $time_arr[4], $time_arr[3], $time_arr[2], $time_arr[1], $time_arr[0]);
    $encdata = blowfish_enc($data);  //blowfish 加密DATA数据
    $length = strlen($encdata);
    $header = pack('CCCC', 0xec, 0xeb, $commn, $length);
    $msg = $header . $encdata;
    $crc = crc16($msg);  //CRC校验
    $activateMsg = $msg . pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff)); //拼装数据包
    $serv->send($fd, $activateMsg);
    //sendMsg($serv, $fd, $activateMsg);
}

/**
 * 将字节数组转化为String类型的数据
 * @param $bytes 字节数组
 * @param $str 目标字符串
 * @return 一个String类型的数据
 */
function toStr($bytes)
{
    $str = '';
    foreach ($bytes as $ch) {
        $str .= chr($ch);
    }
    return $str;
}

/**
 * 转换一个String字符串为byte数组
 * @param $str 需要转换的字符串
 * @param $bytes 目标byte数组
 */
function getBytes($string)
{
    $bytes = array();
    for ($i = 0; $i < strlen($string); $i++) {
        $bytes[] = ord($string[$i]);
    }
    return $bytes;
}
