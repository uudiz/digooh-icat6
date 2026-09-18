<?php

use Swoole\Database\MysqliConfig;
use Swoole\Database\MysqliPool;

/** @var int $server_port Defined in swoole_config.php */
/** @var array $db_server Defined in swoole_config.php */

require_once('utils.php');
require_once('swoole_config.php');

mysqli_report(MYSQLI_REPORT_OFF); // Disable exceptions for MySQLi in PHP 8.1+ to keep legacy boolean return behavior

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

class IcatServer extends Swoole\Server
{
    public array $fdlist = [];
    public ?MysqliPool $dbPool = null;
}

$server = new IcatServer('0.0.0.0', $server_port, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

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
    if (strlen($data) < 2) return;
    $crcstr = unpack('C2', substr($data, -2));
    if ($crcstr === false) return;
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
            $value = isset($recv['value']) ? $recv['value'] : 0;
            //echo "Control command: type=" . $recv['type'] . ", value=" . $value . "\n";
            // echo "original value: " . $recv['value'] . "\n";
            $data = pack('Ca4Ca10CC', 0x00, '1234', 10, $recv['sn'] ?? '', $recv['type'] ?? 0, $value);
            break;
        case 0x4:
            $data = pack('Ca4Ca10', 0x00, '1234', 10, $recv['sn'] ?? '');
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
    // 关闭时清理 DB 中的 socket_fd：避免其他模块（如 UDP 控制/刷新命令）通过失效 fd 发送
    // WHERE socket_fd=? 保证只有当前连接仍然匹配时才清零，防止误伤重连后的新连接
    $mysqli = null;
    try {
        $mysqli = $server->dbPool->get();
        safeQuery($mysqli, "UPDATE cat_player SET socket_fd=0 WHERE socket_fd=" . intval($fd), 'close.clear_fd');
    } catch (\Throwable $e) {
        echo "[" . date("Y-m-d H:i:s") . "] [close] DB error: " . $e->getMessage() . "\n";
    } finally {
        if ($mysqli !== null) {
            try {
                $server->dbPool->put($mysqli);
            } catch (\Throwable $e) {
                // ignore pool put failure
            }
        }
    }
});


$server->start();


function onHeartBeat($serv, $fd, $data, $length)
{
    $respval = 0;

    // 1. 解析固定长度包头 (前47字节)，将剩余不定长数据放入 payload 中
    $fixed_format = 'Cstype/a4netid/Csnlen/a10sn/Cstatus/a8voltage/a8elec/Cfan/a8discspace/Cwet/Ctemp/Cdownnum/Coffstate/Cplen/a*payload';
    $loginpara = unpack($fixed_format, $data);

    if (empty($loginpara) || !isset($loginpara['payload'])) {
        echo "[" . date("Y-m-d H:i:s") . "] [Heartbeat] Empty package or unpack failed!\n";
        return;
    }

    $payload = $loginpara['payload'];
    $plen = $loginpara['plen'];
    $offset = 0;

    // 2. 按顺序利用偏移量 (offset) 游标依次读取变长和可选字段
    $loginpara['plsName'] = substr($payload, $offset, $plen);
    $offset += $plen;

    // 3. 读取终端类型 Pmodel (1 byte)
    if (strlen($payload) >= $offset + 1) {
        $loginpara['pmodel'] = ord($payload[$offset]);
        $offset += 1;
    } else {
        $loginpara['pmodel'] = 2; // fallback default
    }

    // 4. 读取扩展传感器数据 (17 bytes: wetnew 8, tempnew 8, brightnew 1)
    if (strlen($payload) >= $offset + 17) {
        $loginpara['wetnew'] = substr($payload, $offset, 8);
        $offset += 8;
        $loginpara['tempnew'] = substr($payload, $offset, 8);
        $offset += 8;
        $loginpara['brightnew'] = ord($payload[$offset]);
        $offset += 1;
    }

    // 5. 判断是否存在额外的音量字段 (1 byte)
    if (strlen($payload) >= $offset + 1) {
        $loginpara['vol'] = ord($payload[$offset]);
        $offset += 1;
    }

    unset($loginpara['payload']); // 清理缓冲区游标

    $respval = 0; //success basically unless we have other logic
    $data = pack('Ca4Ca10C', 0x00, $loginpara['netid'], $loginpara['snlen'], $loginpara['sn'], $respval);
    $encdata = blowfish_enc($data);  //blowfish 加密DATA数据
    $length = strlen($encdata);
    $header = pack('CCCC', 0xec, 0xeb, CHEARTBEAT, $length);
    $msg = $header . $encdata;
    $crc = crc16($msg);   //CRC校验
    $loginmsg = $msg . pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff));  //拼装数据包

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

    $mysqli = null;
    try {
        $mysqli = $serv->dbPool->get();
        $snEsc = sqlStr($mysqli, $loginpara['sn']);

        // 1) 关键更新：socket_fd + last_connect 拆到独立小 SQL，几乎不可能失败
        //    即使后面的传感器/状态 SQL 出错，重连后的 fd 也已落库，不会拖垮控制链路
        $sqlCritical = "UPDATE cat_player SET socket_fd=" . intval($fd)
            . ", last_connect='" . date('Y-m-d H:i:s') . "'"
            . " WHERE SN='" . $snEsc . "'";
        safeQuery($mysqli, $sqlCritical, 'heartbeat.critical');

        // 2) 状态日志（非关键）
        if ($status != 1011 && $status != 1012 && $status != 1013 && $status != 1014 && $status != 1016) {
            $sql_player = "SELECT id, name, status FROM cat_player WHERE sn='" . $snEsc . "'";
            $result = safeQuery($mysqli, $sql_player, 'heartbeat.select_player');

            if ($result && $result->num_rows) {
                $player = $result->fetch_object();
                $pls = ' ';

                if ($status != $player->status || ($status == 2 || $status == 6)) {
                    if ($plsName == 'NULL' || $plsName == 'null') {
                        $plsName = '';
                    }
                    if ($plsName != '' && ($status == 2 || $status == 6)) {
                        $pls = ' playlist [' . $plsName . ']';
                    }

                    $status_str = heartbeatStatusStr($status);
                    $p_str = "Player[" . $player->name . "] Status: " . $status_str . $pls;
                    $utf8_str = sqlStr($mysqli, mb_convert_encoding($p_str, "UTF-8"));
                    $sql_log = "INSERT INTO `cat_player_log`(`player_id`,`event_type`,`detail`,`add_time`) VALUES ("
                        . intval($player->id) . ", 2, '" . $utf8_str . "', '" . date('Y-m-d H:i:s') . "')";
                    safeQuery($mysqli, $sql_log, 'heartbeat.log_insert');
                }
            }
        }

        // 3) 非关键更新：传感器 / 状态字段（数组拼接，int 列走 sqlInt，varchar 列走 sqlStr）
        $updates = [];
        $updates[] = "model=" . intval($pmodel);
        $updates[] = "status=" . intval($status);
        $updates[] = "voltage='" . sqlStr($mysqli, $voltage) . "'";
        $updates[] = "electric='" . sqlStr($mysqli, $elec) . "'";
        $updates[] = "fan=" . intval($fan);
        $updates[] = "disk_free=" . sqlInt($discspace);
        $updates[] = "humidity=" . intval($wet);
        $updates[] = "temperature=" . intval($temp);
        $updates[] = "downloaddnum=" . intval($downnum);
        $updates[] = "offstate=" . intval($offstate);

        if (isset($loginpara['wetnew'])) {
            $tempwet = sqlInt($loginpara['wetnew']);
            if ($tempwet != 0) {
                $updates[] = "dampness='" . round($tempwet / 1000, 2) . "'";
            }
        }
        if (isset($loginpara['tempnew'])) {
            $tempint = sqlInt($loginpara['tempnew']);
            if ($tempint != 0) {
                $updates[] = "temp='" . round($tempint / 1000, 2) . "'";
            }
        }
        if (isset($loginpara['brightnew'])) {
            $updates[] = "brightness=" . sqlInt($loginpara['brightnew']);
        }
        if (isset($loginpara['vol'])) {
            $volume = intval($loginpara['vol']);
            if ($volume >= 0 && $volume <= 100) {
                $updates[] = "volume=" . $volume;
            }
        }

        $sqlInfo = "UPDATE cat_player SET " . implode(', ', $updates) . " WHERE SN='" . $snEsc . "'";
        safeQuery($mysqli, $sqlInfo, 'heartbeat.info');
    } catch (\Throwable $e) {
        echo "[" . date("Y-m-d H:i:s") . "] [Heartbeat] unexpected DB error: " . $e->getMessage() . "\n";
    } finally {
        if ($mysqli !== null) {
            try {
                $serv->dbPool->put($mysqli);
            } catch (\Throwable $e) {
                // ignore pool put failure
            }
        }
    }
}

/**
 * 将心跳 status 代码映射为可读字符串（抽离自 onHeartBeat，降低主函数复杂度）
 */
function heartbeatStatusStr(int $status): string
{
    switch ($status) {
        case 1:  return "Offline";
        case 2:
        case 6:  return "Playing";
        case 3:  return "Downloading";
        case 4:  return "Stop";
        case 5:  return "Online";
        case 7:  return "Exception";
        case 8:  return "Upgrade in progress";
        case 9:  return "Login";
        case 10: return "Sign out";
        case 20: return " HDMI-Input starts...";
        case 21: return " HDMI-Input end";
        case 127: return "Idle(Sleep Mode)";
        default: return "Unknown(code:" . $status . ")";
    }
}

function onLogin($serv, $fd, $data, $input)
{
    //echo "[" . date("Y-m-d H:i:s") . "]  [Logging] Got login msg! fd=" . $fd . " \n";
    $result = array();
    $respval = 0;

    // 1. 提取头部固定段 (前33字节) 和剩余部分作为 payload
    $fixed_format = 'Cstype/a4netid/Csnlen/a10sn/Cmodel/a17mac/Cverlen/a*payload';
    $loginpara = unpack($fixed_format, $data);

    if (empty($loginpara) || !isset($loginpara['payload'])) {
        return;
    }

    $payload = $loginpara['payload'];
    $offset = 0;

    // 2. 依次利用游标解析变长字段
    $verlen = $loginpara['verlen'] ?? 0;
    $loginpara['ver'] = substr($payload, $offset, $verlen);
    $offset += $verlen;

    // 接下来是固定的 2 字节: sense(1), firmverlen(1)
    if (strlen($payload) >= $offset + 2) {
        $loginpara['sense'] = ord($payload[$offset]);
        $offset += 1;

        $loginpara['firmverlen'] = ord($payload[$offset]);
        $offset += 1;
    } else {
        return;
    }

    // 根据 firmverlen 提取变长 firmver
    $firmverlen = $loginpara['firmverlen'];
    $loginpara['firmver'] = substr($payload, $offset, $firmverlen);
    $offset += $firmverlen;

    // 接下来是基础定长的后续字段 (29 bytes)
    // a8gmt / Cvol / a8disckspace / a8availableSpace / a4mpegcore
    if (strlen($payload) >= $offset + 29) {
        $loginpara['gmt'] = substr($payload, $offset, 8);
        $offset += 8;

        $loginpara['vol'] = ord($payload[$offset]);
        $offset += 1;

        $loginpara['disckspace'] = substr($payload, $offset, 8);
        $offset += 8;

        $loginpara['availableSpace'] = substr($payload, $offset, 8);
        $offset += 8;

        $loginpara['mpegcore'] = substr($payload, $offset, 4);
        $offset += 4;
    }

    // 额外参数解析
    $extraPara = null;
    if ($input > 96 && strlen($payload) > $offset) {
        // C/a4/C/a10/C/a17/C/a...ver/C/C/a...firmver/a8/C/a8/a8/a4 早已在此前被匹配
        // 当前 $offset 处直接对接: cangel(1)/c(1)/a9resolution(9)/CexLen(1)/A*extra
        if (strlen($payload) >= $offset + 12) {
            $extraPara = [];
            // cangel -> 有符号字符（此处取 ord 并处理符号位，或直接用 unpack 局部解）
            $tmpExtras = unpack('cangel/creserved/a9resolution/CexLen/A*extra', substr($payload, $offset));
            if (!empty($tmpExtras)) {
                $extraPara = $tmpExtras;
            }
        }
    }

    unset($loginpara['payload']);

    if ($loginpara) {
        $mysqli = null;
        try {
            $mysqli = $serv->dbPool->get();
            $snRaw = (string) ($loginpara['sn'] ?? '');
            $snEsc = sqlStr($mysqli, $snRaw);

            $sql = "SELECT id,name,upgrade_version FROM cat_player WHERE SN = '" . $snEsc . "';";
            $result = safeQuery($mysqli, $sql, 'login.select_player');

            if ($result && $result->num_rows) {

                $player = $result->fetch_object();

                if (isset($loginpara['gmt'])) {
                    $gmt = preg_replace('/^0+/', '', $loginpara['gmt'] ?? '');
                } else {
                    $gmt = 1;
                }
                $availableSpace = preg_replace('/^0+/', '', $loginpara['availableSpace'] ?? '');
                $disckspace = preg_replace('/^0+/', '', $loginpara['disckspace'] ?? '');
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

                // 1) 关键更新：socket_fd + status + last_connect 拆到独立小 SQL
                //    避免其它字段（如 disk_free/mac/version）写时失败时连带 socket_fd 丢失
                $sqlCritical = "UPDATE cat_player SET socket_fd=" . intval($fd)
                    . ", status=5, last_connect='" . date('Y-m-d H:i:s') . "'"
                    . " WHERE SN='" . $snEsc . "'";
                safeQuery($mysqli, $sqlCritical, 'login.critical');

                // 2) 非关键更新：设备属性字段（数组拼接，int 列用 sqlInt，varchar 列用 sqlStr）
                $updates = [];
                $updates[] = "reboot_flag=0";
                $updates[] = "batch_reg_status=0";
                $updates[] = "mpeg_core=" . intval($mpegCore);
                $updates[] = "mac='" . sqlStr($mysqli, $loginpara['mac'] ?? '') . "'";
                $updates[] = "version_length=" . sqlInt($loginpara['verlen'] ?? 0);
                $updates[] = "version='" . sqlStr($mysqli, $loginpara['ver'] ?? '') . "'";
                $updates[] = "firmver_length=" . sqlInt($loginpara['firmverlen'] ?? 0);
                $updates[] = "firmver='" . sqlStr($mysqli, $loginpara['firmver'] ?? '') . "'";
                $updates[] = "time_zone=" . floatval($gmt);
                $updates[] = "space='" . sqlStr($mysqli, $space) . "'";
                $updates[] = "disk_free=" . sqlInt($availableSpace);
                $updates[] = "disk_total=" . sqlInt($disckspace);
                $updates[] = "storage=" . sqlInt($loginpara['vol'] ?? 0);

                $simICCID = '';
                $extraAssigns = [];
                if ($extraPara) {
                    $updates[] = "resolution='" . sqlStr($mysqli, $extraPara['resolution'] ?? '') . "'";
                    $updates[] = "angel=" . sqlInt($extraPara['angel'] ?? 0);
                    if (isset($extraPara['extra'])) {
                        if (!empty($extraPara['exLen'])) {
                            $extraPara['extra'] = substr($extraPara['extra'], 0, $extraPara['exLen']);
                        }
                        $exPara = json_decode($extraPara['extra'], true);
                        if (is_array($exPara)) {
                            foreach ($exPara as $key => $value) {
                                if ($key == 'simICCID') {
                                    $simICCID = preg_replace('/\D/', '', (string) ($value ?? ''));
                                    continue;
                                }
                                // 防御：只允许合法列名，避免 JSON 字段名直接拼接到 SQL 造成注入
                                if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', (string) $key)) {
                                    $extraAssigns[] = "`$key`='" . sqlStr($mysqli, $value) . "'";
                                }
                            }
                        }
                    }
                }

                $sqlInfo = "UPDATE cat_player SET "
                    . implode(', ', array_merge($updates, $extraAssigns))
                    . " WHERE SN='" . $snEsc . "'";
                safeQuery($mysqli, $sqlInfo, 'login.info');

                if ($simICCID !== '') {
                    safeQuery($mysqli,
                        "UPDATE cat_player_extra SET simno='" . sqlStr($mysqli, $simICCID) . "' WHERE player_id=" . intval($player->id),
                        'login.sim');
                }

                if (($player->upgrade_version . 'A') === (string) ($loginpara['ver'] ?? '')) {
                    // 升级完成后清空 upgrade_version
                    safeQuery($mysqli, "UPDATE cat_player SET upgrade_version=NULL WHERE SN='" . $snEsc . "'", 'login.upgrade_clear');
                }
                $respval = 0;

                $logDetail = sqlStr($mysqli,
                    "Player[" . $player->name . "] login successfully, mac address[" . ($loginpara['mac'] ?? '')
                    . "], software version[" . ($loginpara['ver'] ?? '') . "]");
                safeQuery($mysqli,
                    "INSERT INTO `cat_player_log`(`player_id`,`event_type`,`detail`,`add_time`) VALUES ("
                    . intval($player->id) . ", 1, '" . $logDetail . "', '" . date('Y-m-d H:i:s') . "')",
                    'login.log_insert');
            } else {
                if (strlen($snRaw) == 10 && $snRaw != '0010010013') {
                    $ccode = sqlInt(substr($snRaw, 0, 3));
                    $sql = "SELECT id FROM cat_company WHERE code=" . $ccode;
                    $result = safeQuery($mysqli, $sql, 'login.select_company');

                    $companyid = 0;
                    if ($result && $result->num_rows) {
                        $company = $result->fetch_object();
                        $companyid = intval($company->id);
                    }
                    safeQuery($mysqli,
                        "INSERT INTO cat_player(sn,company_id) VALUES('" . $snEsc . "'," . $companyid . ")",
                        'login.auto_register');
                    $respval = 0;
                } else {
                    $respval = 1;
                    echo "[" . date("Y-m-d H:i:s") . "] [logging] sn:" . $snRaw . "  login error!\n";
                }
            }
            //2016-02-25 查找mac是否存在,存在的话，更新返回包中的sn;
            if ($snRaw === '0010010013' && ($loginpara['ver'] ?? '') >= '4.0.3.0310A') {

                $temp_mac1 = sqlStr($mysqli, str_replace(":", "-", $loginpara['mac'] ?? ''));
                $temp_mac2 = sqlStr($mysqli, $loginpara['mac'] ?? '');
                $mac_sql = "SELECT sn FROM cat_player WHERE sn!='0010010013' AND batch_registration=1 AND (mac='" . $temp_mac1 . "' OR mac='" . $temp_mac2 . "') LIMIT 0,1;";
                $result = safeQuery($mysqli, $mac_sql, 'login.batch_match_mac');
                if ($result && $result->num_rows) {
                    $rec = $result->fetch_object();

                    $respval = 2;
                    $loginpara['sn'] = $rec->sn;
                }
            }
        } catch (\Throwable $e) {
            echo "[" . date("Y-m-d H:i:s") . "] [Login] unexpected DB error: " . $e->getMessage() . "\n";
        } finally {
            if ($mysqli !== null) {
                try {
                    $serv->dbPool->put($mysqli);
                } catch (\Throwable $e) {
                    // ignore pool put failure
                }
            }
        }
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
    if (empty($param1)) return;

    $control_param = unpack('Cstype/a4netid/Csnlen/Cfdlen/a' . $param1['snlen'] . 'sn/a' . $param1['fdlen'] . 'fd/Ctype/Cvalue/Ccommand', $datastram);
    if (empty($control_param)) return;

    $player_sn = $control_param['sn'];
    $player_fd = $control_param['fd'];
    $command = $control_param['command'] ?? 0x3;


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
    if (empty($param1)) return;
    $activatePara = unpack('Cstype/a4netid/Csnlen/a10sn/Ctype/Cidlen/a' . $param1['idlen'] . 'id/Cmodel/a17mac/CipLength', $data);
    if (empty($activatePara)) return;

    $defaultFixedValue = "Sj9TiH4u";
    $rand_arr = array();  //4字节随机数
    $rand_arr[0] = rand(0, 255);
    $rand_arr[1] = rand(0, 255);
    $rand_arr[2] = rand(0, 255);
    $rand_arr[3] = rand(0, 255);
    $rand_str = toStr($rand_arr);  //随机数转换成string字符串

    $type = 0x2;
    $days = 0;
    $time_arr = array_fill(0, 8, 0); // Default to clean 0s to prevent Undefined Variable notice

    //NP201
    $mysqli = $serv->dbPool->get();
    $activateModel = $activatePara['model'] ?? 0;
    if ($activateModel == 0x9) {
        $activateMac = $activatePara['mac'] ?? '';
        $sqlstr = 'SELECT * FROM cat_player_activation where mac="' . $activateMac . '"';
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
    $eSign_arr = getBytes($eSign);

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
 * @param array $bytes 字节数组
 * @return string 一个String类型的数据
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
 * @param string $string 需要转换的字符串
 * @return array 目标byte数组
 */
function getBytes($string)
{
    $bytes = array();
    for ($i = 0; $i < strlen($string); $i++) {
        $bytes[] = ord($string[$i]);
    }
    return $bytes;
}

/**
 * 安全执行 SQL：捕获 MySQL/Mysqli 异常，防止 worker 因单条 SQL 失败而崩溃
 * @param mixed  $mysqli  Swoole\Database\MysqliProxy
 * @param string $sql     待执行的 SQL
 * @param string $context 日志上下文标签
 * @return mysqli_result|bool 成功返回结果或 true；失败返回 false
 */
function safeQuery($mysqli, string $sql, string $context = '')
{
    try {
        return $mysqli->query($sql);
    } catch (\Throwable $e) {
        echo "[" . date("Y-m-d H:i:s") . "] [DB_ERROR] ctx=$context err=" . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * 从 packet 原始字节串中安全提取整数：避免向 int/tinyint 列写入含非数字字符时触发
 * 严格模式下的 "Data truncated for column" 异常（这个异常以前会拖垮整个 UPDATE，
 * 导致 socket_fd 一并丢失）。
 */
function sqlInt($value, int $default = 0): int
{
    $str = (string) $value;
    $num = preg_replace('/[^\d\-]/', '', $str);
    if ($num === '' || $num === '-' || $num === null) {
        return $default;
    }
    return intval($num);
}

/**
 * 清洗 + 转义 SQL 字符串值：先去掉 null 字节等控制字符（会直接导致 MySQL 协议错误），
 * 再走 mysqli->real_escape_string 防止 SQL 注入。
 */
function sqlStr($mysqli, $value): string
{
    $str = (string) $value;
    // 保留可见 ASCII + UTF-8 高位字节；剥离 null / 控制字符
    $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $str);
    if ($cleaned === null) {
        // preg_replace 对非法 UTF-8 返回 null：fallback 到纯 ASCII 过滤
        $cleaned = preg_replace('/[^\x20-\x7E]/', '', $str);
    }
    try {
        return @$mysqli->real_escape_string($cleaned);
    } catch (\Throwable $e) {
        return addslashes($cleaned);
    }
}
