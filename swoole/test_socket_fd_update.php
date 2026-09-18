<?php
/**
 * 测试 socket_fd 在客户端断线重连后是否正确更新
 *
 * 使用方法:
 *   php swoole/test_socket_fd_update.php [--host=127.0.0.1] [--port=4702] [--sn=1234567890] [--wait=5]
 *
 * 前置条件:
 *   1. swoole server (icat6serverphp85.php) 正在运行
 *   2. 数据库中 cat_player 表有对应 SN 的记录（脚本会自动检查并创建测试记录）
 */

require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/swoole_config.php';

// ========== 参数解析 ==========
$options = getopt('', ['host:', 'port:', 'sn:', 'wait:', 'db-host:', 'db-port:', 'db-user:', 'db-pass:', 'db-name:']);
$serverHost = $options['host'] ?? '127.0.0.1';
$serverPort = (int)($options['port'] ?? $server_port ?? 4702);
$testSN     = $options['sn'] ?? '1234567890';
$waitSec    = (int)($options['wait'] ?? 5);  // 断线后等待秒数

$dbHost = $options['db-host'] ?? $db_server['host'];
$dbPort = (int)($options['db-port'] ?? $db_server['port']);
$dbUser = $options['db-user'] ?? $db_server['user'];
$dbPass = $options['db-pass'] ?? $db_server['password'];
$dbName = $options['db-name'] ?? $db_server['name'];

// 记录测试开始时服务器日志文件的大小（用于后续判定本次测试是否引发新异常）
$logFileForBaseline = '/tmp/swoole.' . date('Y-m-d') . '.log';
$logBaselineSize = file_exists($logFileForBaseline) ? filesize($logFileForBaseline) : 0;

// ========== 颜色输出 ==========
function green($s) { return "\033[32m$s\033[0m"; }
function red($s)   { return "\033[31m$s\033[0m"; }
function yellow($s){ return "\033[33m$s\033[0m"; }
function info($s)  { echo "[" . date('H:i:s') . "] $s\n"; }
function pass($s)  { echo green("[PASS] $s") . "\n"; }
function fail($s)  { echo red("[FAIL] $s") . "\n"; }
function warn($s)  { echo yellow("[WARN] $s") . "\n"; }

// ========== 数据库连接 ==========
function getDbConnection($host, $port, $user, $pass, $name) {
    $mysqli = new mysqli($host, $user, $pass, $name, $port);
    if ($mysqli->connect_error) {
        die(red("数据库连接失败: " . $mysqli->connect_error) . "\n");
    }
    $mysqli->set_charset('utf8');
    return $mysqli;
}

// ========== 构建登录数据包 ==========
function buildLoginPacket($sn, $mac = 'AA:BB:CC:DD:EE:FF') {
    $stype    = 1;
    $netid    = 'NET1';
    $snlen    = 10;
    $model    = 2;
    $verlen   = 8;
    $ver      = '4.0.3.01';  // 8 bytes version string
    $sense    = 0;
    $firmverlen = 4;
    $firmver  = 'F1.0';
    $gmt      = str_pad('8', 8, '0');  // 8 bytes GMT
    $vol      = 50;
    $disckspace     = str_pad('500', 8, '0');   // 8 bytes
    $availableSpace = str_pad('200', 8, '0');   // 8 bytes
    $mpegcore       = '5166';                    // 4 bytes

    // payload = ver + sense + firmverlen + firmver + gmt + vol + disckspace + availableSpace + mpegcore
    $payload = $ver . chr($sense) . chr($firmverlen) . $firmver . $gmt . chr($vol) . $disckspace . $availableSpace . $mpegcore;

    // 固定头: Cstype/a4netid/Csnlen/a10sn/Cmodel/a17mac/Cverlen/a*payload
    $snPadded = str_pad($sn, 10, "\0");
    $macPadded = str_pad($mac, 17, "\0");

    $data = pack('C', $stype) . $netid . pack('C', $snlen) . $snPadded . pack('C', $model) . $macPadded . pack('C', $verlen) . $payload;

    return $data;
}

// ========== 构建心跳数据包 ==========
function buildHeartbeatPacket($sn) {
    $stype    = 1;
    $netid    = 'NET1';
    $snlen    = 10;
    $status   = 2;  // Playing
    $voltage  = str_pad('220', 8, '0', STR_PAD_LEFT);   // 8 bytes, voltage是varchar
    $elec     = str_pad('1', 8, '0', STR_PAD_LEFT);     // 8 bytes, electric是varchar
    $fan      = 0;
    $discspace= str_pad('500', 8, '0', STR_PAD_LEFT);   // 8 bytes, disk_free是int，用'0'填充
    $wet      = 50;
    $temp     = str_pad('25', 8, '0', STR_PAD_LEFT);    // 8 bytes, temperature是tinyint
    $downnum  = 0;
    $offstate = 0;

    $plsName = 'TestPL';
    $plen    = strlen($plsName);

    $pmodel    = 2;
    $wetnew    = str_pad('25000', 8, '0');
    $tempnew   = str_pad('30000', 8, '0');
    $brightnew = 80;
    $volume    = 50;

    $payload = $plsName . chr($pmodel) . $wetnew . $tempnew . chr($brightnew) . chr($volume);

    $snPadded = str_pad($sn, 10, "\0");

    $data = pack('C', $stype) . $netid . pack('C', $snlen) . $snPadded
          . pack('C', $status) . $voltage . $elec . pack('C', $fan)
          . $discspace . pack('C', $wet) . $temp . pack('C', $downnum)
          . pack('C', $offstate) . pack('C', $plen) . $payload;

    return $data;
}

// ========== 封装并发送数据包 ==========
function wrapAndSend($client, $data, $command) {
    $encdata = blowfish_enc($data);
    $length  = strlen($encdata);
    $header  = pack('CCCC', 0xec, 0xeb, $command, $length);
    $msg     = $header . $encdata;
    $crc     = crc16($msg);
    $msg    .= pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff));

    return $client->send($msg);
}

// ========== 接收并解析响应 ==========
function recvAndParse($client, $timeout = 3) {
    $raw = @$client->recv($timeout);
    if ($raw === false || $raw === '') {
        return null;
    }
    if (strlen($raw) > 6) {
        $rdata = substr($raw, 4, -2);
        $plain = blowfish_dec($rdata);
        if ($plain === false || strlen($plain) < 17) {
            return null;
        }
        return unpack('Cstype/a4netid/Csnlen/a10sn/Crespval', $plain);
    }
    return null;
}

// ========== 查询数据库中的 socket_fd ==========
function getSocketFd($db, $sn) {
    $stmt = $db->prepare("SELECT socket_fd, last_connect, status FROM cat_player WHERE sn = ?");
    $stmt->bind_param('s', $sn);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $stmt->close();
        return $row;
    }
    $stmt->close();
    return null;
}

// ========== 确保测试 player 存在 ==========
function ensureTestPlayer($db, $sn) {
    $stmt = $db->prepare("SELECT id FROM cat_player WHERE sn = ?");
    $stmt->bind_param('s', $sn);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmt->close();
        info("测试 Player (SN=$sn) 已存在于数据库");
        return true;
    }
    $stmt->close();

    // 插入测试记录
    warn("测试 Player (SN=$sn) 不存在，正在创建...");
    $stmt = $db->prepare("INSERT INTO cat_player (sn, name, company_id, status, socket_fd) VALUES (?, 'TestPlayer_FD', 0, 1, 0)");
    $stmt->bind_param('s', $sn);
    $ok = $stmt->execute();
    $stmt->close();
    if (!$ok) {
        fail("无法创建测试 Player: " . $db->error);
        return false;
    }
    pass("测试 Player 创建成功");
    return true;
}

// ========== 构建敌意心跳包（故意往 int 列写非数字，验证拆分后 socket_fd 仍能更新） ==========
function buildHeartbeatPacketHostile($sn) {
    // 旧代码会把 'BAD\0' 写入 disk_free(int) 导致 MySQL "Data truncated" 异常，worker 直接崩溃
    $stype    = 1;
    $netid    = 'NET1';
    $snlen    = 10;
    $status   = 2;
    $voltage  = 'BADVOL' . "\0\0";
    $elec     = 'BADELE' . "\0\0";
    $fan      = 0;
    $discspace= 'BAD' . "XXX\0\0\0";   // int 列写入非数字
    $wet      = 50;
    $temp     = 'AB';
    $downnum  = 0;
    $offstate = 0;

    $plsName = 'HPL';
    $plen    = strlen($plsName);

    $payload = $plsName . chr(2) . str_pad('25000', 8, '0') . str_pad('30000', 8, '0') . chr(80) . chr(50);

    $snPadded = str_pad($sn, 10, "\0");

    $data = pack('C', $stype) . $netid . pack('C', $snlen) . $snPadded
          . pack('C', $status) . $voltage . $elec . pack('C', $fan)
          . $discspace . pack('C', $wet) . pack('C', 25) . pack('C', $downnum)
          . pack('C', $offstate) . pack('C', $plen) . $payload;

    return $data;
}

// ========== 主测试流程 ==========
echo "\n";
echo "============================================================\n";
echo "  Socket FD 重连更新测试\n";
echo "============================================================\n";
info("服务器: $serverHost:$serverPort");
info("测试 SN: $testSN");
info("断线等待: {$waitSec}s");
echo "\n";

// 连接数据库
$db = getDbConnection($dbHost, $dbPort, $dbUser, $dbPass, $dbName);
info("数据库连接成功");

// 确保测试 player 存在
if (!ensureTestPlayer($db, $testSN)) {
    exit(1);
}

// 重置 socket_fd 为 0
$db->query("UPDATE cat_player SET socket_fd = 0 WHERE sn = '$testSN'");
info("已重置 socket_fd = 0");
echo "\n";

// ========== 第一次连接 & 登录 ==========
info("--- 第一次连接 ---");
$client1 = new Swoole\Client(SWOOLE_SOCK_TCP);
if (!$client1->connect($serverHost, $serverPort, 5)) {
    fail("第一次连接失败: errCode={$client1->errCode}");
    exit(1);
}
pass("TCP 连接建立成功");

// 发送登录包 (CLOGINNEW = 0x05)
$loginData = buildLoginPacket($testSN);
if (!wrapAndSend($client1, $loginData, 0x05)) {
    fail("登录包发送失败");
    exit(1);
}
info("已发送 LOGIN 命令 (0x05)");

// 等待响应
$resp1 = recvAndParse($client1);
if ($resp1) {
    $respSN = trim($resp1['sn'] ?? '');
    $respVal = $resp1['respval'] ?? -1;
    info("收到登录响应: sn=$respSN, respval=$respVal");
    if ($respVal == 0) {
        pass("登录成功 (respval=0)");
    } else {
        warn("登录返回 respval=$respVal (非0，可能 SN 未注册)");
    }
} else {
    warn("未收到登录响应（超时或数据格式不匹配）");
}

// 等待一下确保 DB 写入完成
usleep(500000); // 500ms

// 查询 socket_fd
$playerInfo1 = getSocketFd($db, $testSN);
if ($playerInfo1) {
    $fd1 = $playerInfo1['socket_fd'];
    info("第一次登录后 DB socket_fd = $fd1, last_connect = {$playerInfo1['last_connect']}");
    if ($fd1 > 0) {
        pass("socket_fd 已写入 ($fd1)");
    } else {
        fail("socket_fd 未更新！仍为 $fd1");
    }
} else {
    fail("无法查询到 Player 记录");
    exit(1);
}

echo "\n";

// ========== 发送一个心跳验证 fd 一致 ==========
info("发送心跳验证...");
$hbData = buildHeartbeatPacket($testSN);
wrapAndSend($client1, $hbData, 0x02);
$hbResp = recvAndParse($client1);
if ($hbResp) {
    info("心跳响应正常");
}
usleep(500000);

$playerInfoHb = getSocketFd($db, $testSN);
$fdHb = $playerInfoHb['socket_fd'] ?? 0;
if ($fdHb == $fd1) {
    pass("心跳后 socket_fd 保持一致 ($fdHb)");
} else {
    warn("心跳后 socket_fd 变为 $fdHb (之前是 $fd1)");
}

echo "\n";

// ========== 断开连接 ==========
info("--- 断开连接 ---");
$client1->close();
pass("客户端已断开");

// 等待服务器检测到断开
sleep(1);
$playerInfoAfterClose = getSocketFd($db, $testSN);
$fdAfterClose = $playerInfoAfterClose['socket_fd'] ?? 0;
info("断开后 DB socket_fd = $fdAfterClose");
if ($fdAfterClose == $fd1) {
    warn("断开后 socket_fd 未清零（服务器 close 事件不清理 DB，这是已知行为）");
} else {
    info("断开后 socket_fd 已变更");
}

echo "\n";

// ========== 等待一段时间后重连 ==========
info("--- 等待 {$waitSec}s 模拟断线间隔 ---");
sleep($waitSec);

info("--- 第二次连接（重连） ---");
$client2 = new Swoole\Client(SWOOLE_SOCK_TCP);
if (!$client2->connect($serverHost, $serverPort, 5)) {
    fail("第二次连接失败: errCode={$client2->errCode}");
    exit(1);
}
pass("TCP 重连建立成功");

// 发送登录包
$loginData2 = buildLoginPacket($testSN);
if (!wrapAndSend($client2, $loginData2, 0x05)) {
    fail("第二次登录包发送失败");
    exit(1);
}
info("已发送 LOGIN 命令 (0x05)");

// 等待响应
$resp2 = recvAndParse($client2);
if ($resp2) {
    $respVal2 = $resp2['respval'] ?? -1;
    info("收到登录响应: respval=$respVal2");
    if ($respVal2 == 0) {
        pass("第二次登录成功");
    } else {
        warn("第二次登录返回 respval=$respVal2");
    }
} else {
    warn("未收到第二次登录响应");
}

// 等待 DB 写入
usleep(500000);

// 查询新的 socket_fd
$playerInfo2 = getSocketFd($db, $testSN);
if ($playerInfo2) {
    $fd2 = $playerInfo2['socket_fd'];
    info("第二次登录后 DB socket_fd = $fd2, last_connect = {$playerInfo2['last_connect']}");
} else {
    fail("无法查询到 Player 记录");
    exit(1);
}

echo "\n";

// ========== 结果对比 ==========
echo "============================================================\n";
echo "  测试结果汇总\n";
echo "============================================================\n";
echo "  第一次连接 socket_fd: $fd1\n";
echo "  第二次连接 socket_fd: $fd2\n";
echo "  断线后 DB socket_fd:  $fdAfterClose\n";
echo "------------------------------------------------------------\n";

$testPassed = true;

// 检查 1: 第一次登录后 socket_fd 应 > 0
if ($fd1 > 0) {
    pass("检查1: 首次登录 socket_fd 正确写入 ($fd1)");
} else {
    fail("检查1: 首次登录 socket_fd 未写入");
    $testPassed = false;
}

// 检查 2: 重连后 socket_fd 应该更新（新 fd != 旧 fd，或者至少 > 0）
if ($fd2 > 0 && $fd2 != $fd1) {
    pass("检查2: 重连后 socket_fd 已更新 ($fd1 -> $fd2)");
} elseif ($fd2 > 0 && $fd2 == $fd1) {
    // fd 可能被操作系统复用了，这也是正常的
    warn("检查2: 重连后 socket_fd 相同 ($fd2 == $fd1)，可能是 fd 被系统复用");
    info("       这不一定代表 bug，需要进一步通过 last_connect 时间确认");
    // 检查 last_connect 是否更新了
    if ($playerInfo2['last_connect'] != $playerInfo1['last_connect']) {
        pass("       last_connect 时间已更新，说明登录逻辑执行了");
    } else {
        fail("       last_connect 时间未更新！说明重连登录未执行");
        $testPassed = false;
    }
} else {
    fail("检查2: 重连后 socket_fd 未更新！(fd2=$fd2)");
    $testPassed = false;
}

// 检查 3: 断线后旧 fd 是否仍然在 DB（设计问题检测）
if ($fdAfterClose == $fd1 && $fd1 > 0) {
    warn("检查3: close 事件未清理 DB 中的 socket_fd（潜在设计问题）");
    info("       影响: 断线期间其他模块通过 DB 中 socket_fd 发送命令会失败");
}

echo "\n";

// ========== 额外测试: 心跳也能更新 socket_fd ==========
info("--- 额外测试: 验证心跳也能更新 socket_fd ---");
$hbData2 = buildHeartbeatPacket($testSN);
wrapAndSend($client2, $hbData2, 0x02);
$hbResp2 = recvAndParse($client2);
usleep(500000);

$playerInfo3 = getSocketFd($db, $testSN);
$fd3 = $playerInfo3['socket_fd'] ?? 0;
if ($fd3 == $fd2) {
    pass("心跳后 socket_fd 保持正确 ($fd3)");
} elseif ($fd3 > 0) {
    warn("心跳后 socket_fd 变为 $fd3 (之前是 $fd2)");
} else {
    fail("心跳后 socket_fd 变为 0！");
    $testPassed = false;
}

$client2->close();
sleep(1);

echo "\n";

// ========== 场景2: 重连后只发心跳不发登录 ==========
info("--- 场景2: 重连后只发心跳(不发LOGIN) ---");
$client3 = new Swoole\Client(SWOOLE_SOCK_TCP);
if (!$client3->connect($serverHost, $serverPort, 5)) {
    fail("场景2 连接失败");
} else {
    pass("场景2 TCP 连接成功");
    // 只发心跳，不发登录
    $hbData3 = buildHeartbeatPacket($testSN);
    wrapAndSend($client3, $hbData3, 0x02);
    recvAndParse($client3);
    usleep(500000);

    $playerInfo4 = getSocketFd($db, $testSN);
    $fd4 = $playerInfo4['socket_fd'] ?? 0;
    info("场景2 心跳后 DB socket_fd = $fd4 (之前是 $fd2)");

    if ($fd4 > 0 && $fd4 != $fd2) {
        pass("场景2: 仅心跳也能更新 socket_fd ($fd2 -> $fd4)");
    } elseif ($fd4 == $fd2) {
        // fd 可能被复用
        if ($playerInfo4['last_connect'] != $playerInfo2['last_connect']) {
            warn("场景2: fd 相同但 last_connect 已更新，说明心跳执行了 (fd可能被系统复用)");
        } else {
            fail("场景2: 仅发心跳时 socket_fd 未更新！");
            $testPassed = false;
        }
    } else {
        fail("场景2: socket_fd 变为 $fd4，异常！");
        $testPassed = false;
    }
    $client3->close();
}

echo "\n";

// ========== 场景4: 敌意数据下 socket_fd 仍能写入（验证拆分修复的有效性） ==========
info("--- 场景4: 发送会导致 SQL 异常的敌意数据 ---");
$db->query("UPDATE cat_player SET socket_fd=0 WHERE sn='$testSN'");

$client4 = new Swoole\Client(SWOOLE_SOCK_TCP);
if (!$client4->connect($serverHost, $serverPort, 5)) {
    fail("场景4 连接失败");
} else {
    $hostile = buildHeartbeatPacketHostile($testSN);
    wrapAndSend($client4, $hostile, 0x02);
    recvAndParse($client4, 2);
    usleep(800000);

    $pInfo4 = getSocketFd($db, $testSN);
    $fd4b = $pInfo4['socket_fd'] ?? 0;
    info("场景4: 敌意心跳后 DB socket_fd = $fd4b (预期 > 0)");

    if ($fd4b > 0) {
        pass("场景4: 即使传感器字段包含非法数据，socket_fd 仍能正确写入 (关键修复验证成功)");
    } else {
        fail("场景4: 敌意数据导致 socket_fd 丢失！修复无效");
        $testPassed = false;
    }
    $client4->close();
}
sleep(1);

// 场景3 日志检查（放到最后，以涵盖所有已发送包）
info("--- 场景3: 检查本次测试期间是否有 worker 异常 ---");
$logFile = '/tmp/swoole.' . date('Y-m-d') . '.log';
if (file_exists($logFile)) {
    $currentSize = filesize($logFile);
    if ($currentSize > $logBaselineSize) {
        // 只读取本次测试后新增的部分
        $fp = fopen($logFile, 'rb');
        fseek($fp, $logBaselineSize);
        $newContent = stream_get_contents($fp);
        fclose($fp);

        $newAbnormal = preg_match_all('/abnormal exit/', $newContent);
        $newTruncation = preg_match_all('/Data truncated/', $newContent);
        $newMysqli = preg_match_all('/MysqliException/', $newContent);

        if ($newAbnormal > 0) {
            fail("场景3: 本次测试引发 $newAbnormal 次 worker 异常退出，修复未完全生效");
            info("       数据截断: $newTruncation 次, Mysqli 异常: $newMysqli 次");
            echo "\n[新增日志片段开始]\n" . substr($newContent, 0, 2000) . "\n[新增日志片段结束]\n";
            $testPassed = false;
        } else {
            pass("场景3: 本次测试期间无 worker 异常退出，拆分后的 socket_fd 更新不受其他字段错误影响");
        }
    } else {
        pass("场景3: 本次测试未向日志写入新内容，无 worker 异常");
    }
} else {
    info("场景3: 日志文件不存在 ($logFile)");
}

echo "\n";
echo "============================================================\n";
if ($testPassed) {
    echo green("  总体结果: 所有关键检查通过\n");
} else {
    echo red("  总体结果: 存在失败项，socket_fd 更新可能有问题\n");
}
echo "============================================================\n\n";

// ========== 清理 ==========
info("清理: 重置测试 Player socket_fd = 0");
$db->query("UPDATE cat_player SET socket_fd = 0 WHERE sn = '$testSN'");
$db->close();

exit($testPassed ? 0 : 1);
