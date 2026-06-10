<?php
require_once 'swoole/utils.php';

function buildLogin() {
    $payload = "";
    // Firmware len and firmware (for testing we just make them up)
    // sense, ver, then extra data
    
    // pack format inside onLogin:
    // Cstype/a4netid/Csnlen/a10sn/Csdlen/a15sd/Cxmdlen/a15xmd/Ccmdlen/a15cmd/CSimlen/a15Sim/Cwifimaclen/a15wifimac/Cverlen/a*payload
    return pack('Ca4Ca10Ca15Ca15Ca15Ca15Ca15C',
        1, "NET1", 10, "1234567890", 
        4, "SD12", 4, "XMD1", 4, "CMD1", 4, "SIM1", 4, "WIFI", 
        6 // verlen
    ) . "VER123" . chr(0) . chr(4) . "FIRM" . "{ \"some\": \"json\" }";
}

$cdata = buildLogin();
$encdata = blowfish_enc($cdata);
$length = strlen($encdata);
$header = pack('CCCC', 0xec, 0xeb, 0x01, $length); // 0x01 = CLOGIN
$msg = $header . $encdata;
$crc = crc16($msg);
$msg .= pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff));

$client = new Swoole\Client(SWOOLE_SOCK_TCP);
if (!$client->connect('127.0.0.1', 4705, -1)) {
    exit("connect failed. Error: {$client->errCode}\n");
}
$client->send($msg);
$raw_resp = $client->recv();
if ($raw_resp) {
    if (strlen($raw_resp) > 4) {
        $rdata = substr($raw_resp, 4, -2);
        $plain = blowfish_dec($rdata);
        var_dump(unpack('Cstype/a4netid/Csnlen/a10sn/Crespval', $plain));
    }
}
$client->close();
