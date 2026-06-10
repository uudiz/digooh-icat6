<?php
require_once 'swoole/utils.php';

function buildHeartbeat() {
    $payload = "PlaylistA"; // 9 bytes
    $payload .= chr(2); // pmodel
    $payload .= str_pad("50", 8, "0"); // wetnew
    $payload .= str_pad("30", 8, "0"); // tempnew
    $payload .= chr(100); // brightnew
    $payload .= chr(50); // vol
    
    return pack('Ca4Ca10Ca8a8Ca8CCCCC',
        1, "NET1", 10, "1234567890", 2, "220V", "1A", 1, "100G", 50, 30, 0, 0, 9
    ) . $payload;
}

$cdata = buildHeartbeat();
$encdata = blowfish_enc($cdata);
$length = strlen($encdata);
$header = pack('CCCC', 0xec, 0xeb, 0x02, $length); // 0x02 = CHEARTBEAT
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
