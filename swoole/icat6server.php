<?php
require_once('utils.php');
require_once('swoole_config.php');

$serv = new Icat6SocketServer();
$serv->run('0.0.0.0', 4702);


class Icat6SocketServer
{
    protected $serv; //swoole server
    //protected $memcach_conn=null;

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
    const MAX_PACKAGE_LEN = 8000000; //max data accept

    public function run($host, $port)
    {
        register_shutdown_function(array(
            $this,
            'errorHandler'
        ));
        $this->serv = new \Swoole\Server($host, $port);
        $this->serv->addlistener("127.0.0.1", 4702, SWOOLE_SOCK_UDP); // UDP
        $this->serv->set(array(
            'daemonize' => false,
            'max_request' => 0,                //reload worker by run xx times
            'dispatch_mode' => 3,              //who come first who is
            'worker_num' => 8,                 //how much worker will start,base on your cpu nums
            'reactor_num' => 8,                // depend cpu how much cpu you have
            'backlog' => 128,                  //accept queue
            'open_cpu_affinity' => 1,          //get cpu more time
            'open_tcp_nodelay' => 1,           // for small packet to open
            'tcp_defer_accept' => 5,           //client will accept when not have data
            'max_conn' => 4096,
            'task_worker_num' => 10,           //MYSQL connection pool
            'task_ipc_mode' => 3,              //use queue with "who come first who is"
            //'message_queue_key' => 0x72000100,
            'open_length_check' => true,
            'package_max_length' => 512,
            'package_length_type' => 'C',      //see php pack()
            'package_length_offset' => 3,
            'package_body_offset' => 6,
            'heartbeat_check_interval' => 1800, //设置心跳检测间隔
            'heartbeat_idle_time' => 3600, //5分钟无数据断开
            //'heartbeat_check_interval' => 60, //设置心跳检测间隔
            'log_file' => '/tmp/swooleicat6.' . date('Y-m-d') . '.log',
        ));

        $this->serv->on('start', array($this, 'onStart'));
        $this->serv->on('connect', array($this, 'onConnect'));
        $this->serv->on('receive', array($this, 'onReceive'));
        $this->serv->on('close', array($this, 'onClose'));
        $this->serv->on('task', array($this, 'onTask'));
        $this->serv->on('finish', array($this, 'onFinish'));
        //UDP Control command from ICAT 
        $this->serv->on('packet', function ($serv, $data, $clientInfo) {
            $recv = json_decode($data, true);

            if (!$recv || !isset($recv['command'])) {
                return;
            }
            $command = $recv['command'];

            if ($command !== 3 && $command != 4) {
                return;
            }
            echo "control command=$command\n";
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


        $this->serv->start();
    }

    public function onStart($serv)
    {
        echo "[" . date("Y-m-d H:i:s") . "]  MasterPid={$serv->master_pid}|Manager_pid={$serv->manager_pid}\n";
        echo "[" . date("Y-m-d H:i:s") . "]  Server: start.Swoole version is [" . SWOOLE_VERSION . "]\n";
    }

    public function onConnect($server, $fd, $from_id)
    {
        //echo "[" . date("Y-m-d H:i:s") . "]  Client:Connect from {$fd}\n";
    }

    public function onReceive($serv, $fd, $from_id, $data)
    {
        //CRC16校验
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

        $datastream = blowfish_dec(substr($data, 4, -2));
        //echo "command=".$package['comm']."\n";

        switch ($package['comm']) {
            case self::CLOGIN:
            case self::CLOGINNEW:
                $this->onLogin($serv, $fd, $from_id, $datastream, $package['lenth']);
                break;
            case self::CHEARTBEAT:
                //$this->onHeartBeat($serv, $fd, $from_id, $datastream);
                $this->onHeartBeat($serv, $fd, $from_id, $datastream, $package['lenth']);
                break;
            case self::SERVERCONTRL:
                //$this->onControl($serv, $fd, $from_id, $datastream);
                $this->onControl($serv, $fd, $from_id, $datastream);
                break;
            case self::CINSTANTSCH:
                break;
            case self::ConsumerCode:
                $this->onConsumerCode($serv, $fd, $from_id, $datastream);
                // no break
            case self::SActivate: //标准终端激活
            case self::IActivate: //互动终端激活
                $this->onActivate($serv, $fd, $from_id, $datastream, $package['comm'], $package['lenth'] + 3);
                break;
            default:
                break;
        }
    }


    public function onTask($serv, $fd, $from_id, $sql)
    {
        global $db_server;
        static $conn = null;
        if ($conn == null) {
            $conn = mysqli_connect($db_server['host'], $db_server['user'], $db_server['password'], $db_server['name'], $db_server['port']);
            if (!$conn) {
                $conn = null;
                $serv->finish('ERR:' . mysqli_connect_error());
                var_dump(mysqli_connect_error());
                echo "[" . date("Y-m-d H:i:s") . "]  [ERROR] Unable to connect to db!\n";
                return;
            }
        }
        for ($i = 0; $i < 2; $i++) {
            $result = $conn->query($sql);
            if ($result === false) {
                $errno = mysqli_errno($conn);
                if ($errno == 2006 || $errno == 2013) {
                    mysqli_close($conn);
                    $conn = mysqli_connect($db_server['host'], $db_server['user'], $db_server['password'], $db_server['name'], $db_server['port']);

                    if ($conn) {
                        continue;
                    }
                    return;
                }
            }
            break;
        }

        //	print_r($sql);
        //	print_r($result);
        mysqli_set_charset($conn, "utf8");
        if (!$result) {
            $serv->finish('ERR:' . mysqli_error($conn));
            return;
        }

        if (is_bool($result)) {
            $serv->finish($result);
            return;
        }

        if ($result->num_rows == 0) {
            $serv->finish(false);
            return;
        }

        $data = $result->fetch_all(MYSQLI_ASSOC);

        $serv->finish('OK:' . serialize($data));
    }

    public function onFinish($serv, $task_id, $data)
    {
    }

    public function onClose($serv, $fd)
    {
        //echo "[" . date("Y-m-d H:i:s") . "]  Client:DisConnect from {$fd}\n";
        //	$serv->taskwait("update cat_player set status = 1,socket_fd=0 where socket_fd = '".$fd."'");
    }

    public function errorHandler()
    {
    }



    //When clients logging in
    public function onLogin($serv, $fd, $from_id, $data, $input)
    {
        //echo "[".date("Y-m-d H:i:s")."]  [Logging] Got login msg! fd=".$fd." \n";

        $result = array();
        $respval = 0;
        $loginpara_tmp1 = unpack('Cstype/a4netid/Csnlen/a10sn/Cmodel/a17mac/Cverlen', $data);
        $loginpara_tmp2 = unpack('Cstype/a4netid/Csnlen/a10sn/Cmodel/a17mac/Cverlen/a' . $loginpara_tmp1['verlen'] . 'ver/Csense/Cfirmverlen', $data);
        $loginpara = unpack('Cstype/a4netid/Csnlen/a10sn/Cmodel/a17mac/Cverlen/a' . $loginpara_tmp2['verlen'] . 'ver/Csense/Cfirmverlen/a' . $loginpara_tmp2['firmverlen'] . 'firmver/a8gmt/Cvol/a8disckspace/a8availableSpace/a4mpegcore', $data);
        if (!$loginpara) {
            return;
        }

        $sql = "SELECT id,name,upgrade_version FROM cat_player WHERE SN = '" . $loginpara['sn'] . "';";

        $result = $serv->taskwait($sql, 1);
        //查找数据库中改Player是否存在

        if ($result) {
            //保存对应的 sn 和 fd  到memcache
            list($status, $db_res) = explode(':', $result, 2);
            $db_res = unserialize($db_res);

            if ($status == 'OK') {
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
                    . date('Y-m-d H:i:s') . "', socket_fd=" . $fd . " WHERE SN = '" . $loginpara['sn'] . "';";
                $serv->task($sql);
                if ($db_res[0]['upgrade_version'] . 'A' == $loginpara['ver']) {
                    //执行成功后，删除当前升级包
                    $serv->task("update cat_player set upgrade_version = NULL where sn = '" . $loginpara['sn'] . "'");
                }
                $respval = 0;
                $sql = "INSERT INTO `cat_player_log`(`player_id` ,`event_type` ,`detail`, `add_time`)VALUES (" . $db_res[0]['id'] . ", 1, 'Player[" . $db_res[0]['name'] . "] login successfully, mac address[" . $loginpara['mac'] . "], software version[" . $loginpara['ver'] . "]', '" . date('Y-m-d H:i:s') . "');";
                $serv->task($sql);
            }
        } else {
            if (strlen($loginpara['sn']) == 10 && $loginpara['sn'] != '0010010013') {
                $ccode = substr($loginpara['sn'], 0, 3);
                $sql = "select id from cat_company where code=" . $ccode;
                $result = $serv->taskwait($sql);


                if ($result) {
                    list($status, $db_res) = explode(':', $result, 2);

                    if ($status == 'OK') {
                        $db_res = unserialize($db_res);
                        $companyid = $db_res[0]['id'];

                        $sql = "insert into cat_player(sn,company_id) values(" . $loginpara['sn'] . "," . $companyid . ")";
                    }
                } else {
                    $sql = "insert into cat_player(sn) values(" . $loginpara['sn'] . ")";
                }
                $serv->task($sql);

                //   				$this->my_memcache_set($loginpara['sn'],$fd);

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
            $mac_result = $serv->taskwait($mac_sql);
            if ($mac_result) {
                list($status, $db_res) = explode(':', $mac_result, 2);
                if ($status == 'OK') {
                    $db_res = unserialize($db_res);
                    $respval = 2;
                    $loginpara['sn'] = $db_res[0]['sn'];
                }
            }
        }
        //echo 'reg  respval: '.$respval.', sn: '.$loginpara['sn']."\n";



        $data = pack('Ca4Ca10C', 0x00, $loginpara['netid'], $loginpara['snlen'], $loginpara['sn'], $respval);
        $encdata = blowfish_enc($data); //blowfish 加密DATA数据
        $length = strlen($encdata);
        $header = pack('CCCC', 0xec, 0xeb, 0x05, $length);
        $msg = $header . $encdata;
        $crc = crc16($msg);  //CRC校验
        $loginmsg = $msg . pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff));  //拼装数据包

        $output = $length + 3;
        saveSwooleAccess($loginpara['sn'], $input, $output);
        sendMsg($serv, $fd, $loginmsg);
    }

    /**
     * 心跳包的处理
     * 分析并记录相关参数到数据库中
     * 发送响应给板子
     */
    public function onHeartBeat($serv, $fd, $from_id, $data, $length)
    {
        //echo "[".date("Y-m-d H:i:s")."]  [onHeartBeat] fd=".$fd." \n";

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
        $header = pack('CCCC', 0xec, 0xeb, self::CHEARTBEAT, $length);
        $msg = $header . $encdata;
        $crc = crc16($msg);   //CRC校验
        $loginmsg = $msg . pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff));  //拼装数据包

        $output = $length + 3;

        sendMsg($serv, $fd, $loginmsg);


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

        if ($status != 1011 && $status != 1012 && $status != 1013 && $status != 1014 && $status != 1016) {
            $sql_player = "select id, name, status from cat_player where sn = '" . $loginpara['sn'] . "'";
            $result_player = $serv->taskwait($sql_player, 1);
            if (!$result_player) {
                return;
            }
            list($db_ret, $db_res) = explode(':', $result_player, 2);

            if ($db_ret == 'OK') {
                $db_res = unserialize($db_res);
                $pls = ' ';
                //状态改变了

                if ($status != $db_res[0]['status'] || ($status == 2 || $status == 6/*&&$plsName!='null'&&$plsName != 'NULL'*/)) {
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

                    $p_str = "Player[" . $db_res[0]['name'] . "] Status: " . $status_str . $pls;
                    $utf8_str = mb_convert_encoding($p_str, "UTF-8");
                    $sql_log = "INSERT INTO `cat_player_log`(`player_id` ,`event_type` ,`detail`, `add_time`)VALUES (" . $db_res[0]['id'] . ", 2, '" . $utf8_str . "', '" . date('Y-m-d H:i:s') . "')";
                    $serv->task($sql_log);
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

        $serv->task($sql);
    }

    /**
     * 终端远程操作处理
     */
    public function onControl($serv, $fd, $from_id, $datastram)
    {
        $param1 = unpack('Cstype/a4netid/Csnlen/Cfdlen', $datastram);

        $control_param = unpack('Cstype/a4netid/Csnlen/Cfdlen/a' . $param1['snlen'] . 'sn/a' . $param1['fdlen'] . 'fd/Ctype/Cvalue/Ccommand', $datastram);

        $player_sn = $control_param['sn'];
        $player_fd = $control_param['fd'];
        $command = $control_param['command'] ?: 0x3;
        //echo "[" . date("Y-m-d H:i:s") . "]  [onControl] sn=" . $player_sn .  ",command=" . $command . ",type=" . $control_param['type'] . ",value=" . $control_param['value'] . " \n";

        if ($player_fd > 0) {
            if ($command == 0x3) {
                $data = pack('Ca4Ca10CC', 0x00, $control_param['netid'], 10, $player_sn, $control_param['type'], $control_param['value']);
            } else {
                $data = pack('Ca4Ca10', 0x00, $control_param['netid'], 10, $player_sn);
            }


            $encdata = blowfish_enc($data);  //blowfish 加密DATA数据
            $length = strlen($encdata);

            if ($command == 0x3) {
                $header = pack('CCCC', 0xec, 0xeb, self::CCONTROL, $length);
            } else {
                $header = pack('CCCC', 0xec, 0xeb, 0x04, $length);
            }
            $msg = $header . $encdata;
            $crc = crc16($msg); //CRC校验
            $controlMsg = $msg . pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff)); //拼装数据包
            //$serv->send($fd, $controlMsg);
            sendMsg($serv, $player_fd, $controlMsg);
        }
    }



    public function onConsumerCode($serv, $fd, $from_id, $data)
    {
        echo 'ConsumerCode command\n';
    }

    /**
     * 终端激活处理
     */
    public function onActivate($serv, $fd, $from_id, $data, $commn, $input)
    {
        echo "[" . date("Y-m-d H:i:s") . "] [Activate] Get Activate command!\n";
        $param1 = unpack('Cstype/a4netid/Csnlen/a10sn/Ctype/Cidlen', $data);
        echo "idlenght=" . $param1['idlen'] . "\n";
        $activatePara = unpack('Cstype/a4netid/Csnlen/a10sn/Ctype/Cidlen/a' . $param1['idlen'] . 'id/Cmodel/a17mac/CipLength', $data);

        echo "sn=" . $activatePara['sn'] . "\n";
        $defaultFixedValue = "Sj9TiH4u";
        $rand_arr = array();  //4字节随机数
        $rand_arr[0] = rand(0, 255);
        $rand_arr[1] = rand(0, 255);
        $rand_arr[2] = rand(0, 255);
        $rand_arr[3] = rand(0, 255);
        $rand_str = $this->toStr($rand_arr);  //随机数转换成string字符串

        $type = 0x2;
        $days = 0;
        //NP201

        if ($activatePara['model'] == 0x9) {
            $sqlstr = 'SELECT * FROM cat_player_activation where mac="' . $activatePara['mac'] . '"';
            echo $sqlstr;
            $result = $serv->taskwait($sqlstr, 1);
            list($db_ret, $db_res) = explode(':', $result, 2);


            if ($db_ret == 'OK') {
                $db_res = unserialize($db_res);

                if ($db_res[0]['is_active'] == 0) {
                    $type = 0; //激活失败
                } else {
                    $expire_date = $db_res[0]['expire_at'];
                    echo "got expire_date from db=" . $expire_date . "\n";
                    $expire_time = strtotime($expire_date);
                    $now = strtotime(date("Y-m-d", time()));
                    echo "now=" . $now . "\n";
                    echo "expire_time=" . $expire_time . "\n";
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
                $serv->taskwait($sqlstr, 1);
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
        $eSign_arr = $this->getBytes($eSign); //转换一个String字符串为byte数组

        $data = pack('Ca4Ca10CC4C16C8', 0x00, $activatePara['netid'], 0x08, $activatePara['sn'], $type, $rand_arr[0], $rand_arr[1], $rand_arr[2], $rand_arr[3], $eSign_arr[0], $eSign_arr[1], $eSign_arr[2], $eSign_arr[3], $eSign_arr[4], $eSign_arr[5], $eSign_arr[6], $eSign_arr[7], $eSign_arr[8], $eSign_arr[9], $eSign_arr[10], $eSign_arr[11], $eSign_arr[12], $eSign_arr[13], $eSign_arr[14], $eSign_arr[15], $time_arr[7], $time_arr[6], $time_arr[5], $time_arr[4], $time_arr[3], $time_arr[2], $time_arr[1], $time_arr[0]);
        $encdata = blowfish_enc($data);  //blowfish 加密DATA数据
        $length = strlen($encdata);
        $header = pack('CCCC', 0xec, 0xeb, $commn, $length);
        $msg = $header . $encdata;
        $crc = crc16($msg);  //CRC校验
        $activateMsg = $msg . pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff)); //拼装数据包
        //$serv->send($fd, $activateMsg);
        $output = $length + 3;
        saveSwooleAccess($activatePara['sn'], $input, $output);
        sendMsg($serv, $fd, $activateMsg);
    }

    /**
     * 将字节数组转化为String类型的数据
     * @param $bytes 字节数组
     * @param $str 目标字符串
     * @return 一个String类型的数据
     */
    public function toStr($bytes)
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
    public function getBytes($string)
    {
        $bytes = array();
        for ($i = 0; $i < strlen($string); $i++) {
            $bytes[] = ord($string[$i]);
        }
        return $bytes;
    }

    /**
     * 客户机心跳是否超时（5分钟）
     * @param $sn  终端编号
     */
    /*
    function timeout($serv, $sn) {
        $sql_player = "select id, name, status from cat_player where sn = '".$sn."'";
        $result_p = $this->my_query($serv, $sql_player);
        if($result_p){
            $sql_log = "select * from cat_player_log where player_id=".$result_p[0]." and event_type=2 order by id desc limit 1";
            $log = $serv->taskwait($sql_log);
            if($log){
                if($log[2]){
                    if(preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/",$log[2], $matches)){
                        $time=mktime(intval($matches[4]), intval($matches[5]),intval($matches[6]), intval($matches[2]), intval($matches[3]),intval($matches[1]));
                        if((time()- $time) >= (5 * 60)-10 || (time()- $time) < 0){
                            return TRUE;
                        }
                    }
                }
            }else {
                return FALSE;
            }
        }
        return FALSE;
    }


    function my_memcache_set($key,$value){

            $ret = @memcache_get($this->memcach_conn,$key);  //从memcache中获取sn对应的fd

            if($ret){
                return @memcache_replace($this->memcach_conn,$key,$value);
            }else{
                return @memcache_set($this->memcach_conn,$key,$value);
            }

    }

    function my_memcache_get($key){
        //$key = "icat6".$key;
            $ret = @memcache_get($this->memcach_conn,$key);  //从memcache中获取sn对应的fd
            //echo sprintf("my_memcache_get:key=%s,ret=%d",$key,$ret);
            //$mcache->close();
            return $ret;

    }
    */
}

/**
 * 发送数据包，如果失败重试5次
 * 5次后还是失败写入日志
 **/
function sendMsg($serv, $fd, $msg)
{
    $send_status = false;
    $send_num = 0;
    do {
        $send_status = $serv->send($fd, $msg);

        $send_num++;
        if ($send_status != true) {
            $last_error = $serv->getLastError();
            if ($last_error >= 1001 && $last_error <= 1202) {
                echo "error num on send=." . $last_error . "\n";
                $send_num = 5;
                break;
            }
        }
    } while ($send_num < 5 && $send_status == false);

    if ($send_num >= 5) {
        $log = date('Y-m-d H:i:s') . "| send failed 5 times!\n";
        echo $log;
        return false;
    }
    return true;
}


/**
 * 统计swoole Input Output
 */
function saveSwooleAccess($sn, $input, $output)
{
    return;
    /*
    $h = fopen('/var/log/swoole/'.date('Ymd').'_swoole_log', 'a+');
    if($h){
        $log = $sn.' '.$input.' '.$output."\n";
        //$log = date('Y-m-d H:i:s').' '.$sn.' '.$input.' '.$output."\n";
        fwrite($h, $log);
        fclose($h);
    }
    */
}
