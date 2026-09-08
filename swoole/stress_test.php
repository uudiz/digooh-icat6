<?php

/**
 * iCAT6 DOOH Digital Signage CMS - Stress Test Tool
 *
 * This tool simulates massive players connecting to the Swoole Server.
 * It reads player SNs from the MariaDB/MySQL database for a specified company_id,
 * and uses Swoole's coroutines to handle concurrent player connections,
 * sending Login and periodic Heartbeat packages, and monitoring responses.
 */

// Ensure we are running from CLI
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

// Check if Swoole extension is loaded
if (!extension_loaded('swoole')) {
    die("Error: The 'swoole' extension is required to run this script. Please install/enable it.\n");
}

// Load Swoole client requirements and config
$configPath = __DIR__ . '/swoole_config.php';
if (!file_exists($configPath)) {
    $configPath = __DIR__ . '/swoole_config.sample.php';
}

if (!file_exists($configPath)) {
    die("Error: swoole_config.php or swoole_config.sample.php not found.\n");
}

require_once $configPath;
require_once __DIR__ . '/utils.php';

// Helper to print help/usage instructions
function printUsage()
{
    echo <<<USAGE
iCAT6 Swoole Server Stress Test Client

Usage: php stress_test.php [options]

Options:
  -c <company_id>    The Company ID to fetch players from (default: 25)
  -i <interval>      The Heartbeat interval in seconds (default: 30)
  -h <host>          Swoole server IP/Host (defaults to \$server_port config host / 127.0.0.1)
  -p <port>          Swoole server Port (defaults to \$server_port config / 4705)
  -n <limit>         Limit the number of simulated players (default: 0, which means no limit)
  -r <rate_limit>    Spawn rate: max connections to instantiate per second (default: 50)
  -v                 Enable verbose logging for each individual packet (warning: generates a lot of output)
  --help             Show this help screen

Example:
  php stress_test.php -c 25 -i 15 -n 100 -r 20
USAGE;
    echo "\n";
}

// Parse input options
$opts = getopt("c:i:h:p:n:r:v", ["help"]);
if (is_array($opts) && isset($opts['help'])) {
    printUsage();
    exit(0);
}

$companyId  = isset($opts['c']) ? intval($opts['c']) : 25;
$interval   = isset($opts['i']) ? intval($opts['i']) : 30;
$serverHost = isset($opts['h']) ? $opts['h'] : '127.0.0.1';
$serverPort = isset($opts['p']) ? intval($opts['p']) : (isset($server_port) ? $server_port : 4705);
$limit      = isset($opts['n']) ? intval($opts['n']) : 0;
$rateLimit  = isset($opts['r']) ? intval($opts['r']) : 50;
$verbose    = isset($opts['v']);

// Try to use DB details from swoole_config
$dbHost = isset($db_server['host']) ? $db_server['host'] : '127.0.0.1';
$dbPort = isset($db_server['port']) ? $db_server['port'] : 3306;
$dbUser = isset($db_server['user']) ? $db_server['user'] : 'icat';
$dbPass = isset($db_server['password']) ? $db_server['password'] : 'icat';
$dbName = isset($db_server['name']) ? $db_server['name'] : 'digtest';

echo "============================================================\n";
echo "iCAT6 STRESS TEST TOOL - INITIALIZING\n";
echo "============================================================\n";
echo "Connecting to Database: {$dbHost}:{$dbPort} ({$dbName}) ... ";

// Connect to DB to load player SNs
$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($mysqli->connect_error) {
    echo "FAILED!\n";
    die("Database Connection Error: " . $mysqli->connect_error . "\n");
}
echo "SUCCESS!\n";

// Query players
$sql = "SELECT sn, name FROM cat_player WHERE company_id = " . intval($companyId) . " AND status != 0";
if ($limit > 0) {
    $sql .= " LIMIT " . intval($limit);
}

$res = $mysqli->query($sql);
if (!$res) {
    die("Database Query Failed: " . $mysqli->error . "\n");
}

$players = [];
while ($row = $res->fetch_assoc()) {
    // Normalise SN to 10 chars
    $sn = trim($row['sn']);
    if (strlen($sn) > 10) {
        $sn = substr($sn, 0, 10);
    } else {
        $sn = str_pad($sn, 10, '0', STR_PAD_RIGHT);
    }
    $players[] = [
        'sn' => $sn,
        'name' => $row['name']
    ];
}
$mysqli->close();

$playerCount = count($players);
if ($playerCount == 0) {
    echo "No active players found for company ID: $companyId.\n";
    echo "Please insert test players or specify another company ID with -c option.\n";
    exit(0);
}

echo "Found total players in DB: " . $playerCount . "\n";
echo "Heartbeat sending interval: " . $interval . "s\n";
echo "Target Swoole server: " . $serverHost . ":" . $serverPort . "\n";
echo "Spawning Connections Rate limit: " . $rateLimit . "/sec\n";
echo "Verbose Mode: " . ($verbose ? "ENABLED" : "DISABLED") . "\n";
echo "============================================================\n";
echo "Starting stress simulation in 2 seconds... Press Ctrl+C to terminate.\n";
sleep(2);

// Real-time tracking metrics
class StressMetrics
{
    public static $total = 0;
    public static $connecting = 0;
    public static $running = 0;
    public static $failed = 0;
    public static $disconnects = 0;
    public static $heartbeatsSent = 0;
    public static $heartbeatsReceived = 0;
    public static $loginsSent = 0;
    public static $loginsReceived = 0;
    public static $errors = [];
}
StressMetrics::$total = $playerCount;

// Function to safely append errors
function addError($msg)
{
    $msg = "[" . date('H:i:s') . "] " . $msg;
    if (count(StressMetrics::$errors) >= 15) {
        array_shift(StressMetrics::$errors);
    }
    StressMetrics::$errors[] = $msg;
}

// Function to compile LOGIN package
function buildLoginPacket($sn)
{
    // Cstype/a4netid/Csnlen/a10sn/Csdlen/a15sd/Cxmdlen/a15xmd/Ccmdlen/a15cmd/CSimlen/a15Sim/Cwifimaclen/a15wifimac/Cverlen/a*payload
    $payload = "VER8.5" . chr(0) . chr(4) . "FIRM" . json_encode(["stress" => true]);
    $cdata = pack(
        'Ca4Ca10Ca15Ca15Ca15Ca15Ca15C',
        1,
        "NET1",
        10,
        $sn,
        4,
        "SD12",
        4,
        "XMD1",
        4,
        "CMD1",
        4,
        "SIM1",
        4,
        "WIFI",
        6 // verlen (VER8.5 is 6 chars)
    ) . $payload;

    $encdata = blowfish_enc($cdata);
    $length = strlen($encdata);
    $header = pack('CCCC', 0xec, 0xeb, 0x01, $length); // 0x01 = CLOGIN
    $msg = $header . $encdata;
    $crc = crc16($msg);
    $msg .= pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff));
    return $msg;
}

// Function to compile HEARTBEAT package
function buildHeartbeatPacket($sn)
{
    // payload parameters
    $plsName = "PlaylistS"; // 9 bytes
    $payload = $plsName;
    $payload .= chr(2); // pmodel
    $payload .= str_pad("50", 8, "0"); // wetnew
    $payload .= str_pad("30", 8, "0"); // tempnew
    $payload .= chr(100); // brightnew
    $payload .= chr(50); // vol

    // Fixed part parameters (Cstype/a4netid/Csnlen/a10sn/Cstatus/a8voltage/a8elec/Cfan/a8discspace/Cwet/Ctemp/Cdownnum/Coffstate/Cplen)
    $cdata = pack(
        'Ca4Ca10Ca8a8Ca8CCCCC',
        1,
        "NET1",
        10,
        $sn,
        2,
        "220V",
        "1A",
        1,
        "100G",
        50,
        30,
        0,
        0,
        strlen($plsName)
    ) . $payload;

    $encdata = blowfish_enc($cdata);
    $length = strlen($encdata);
    $header = pack('CCCC', 0xec, 0xeb, 0x02, $length); // 0x02 = CHEARTBEAT
    $msg = $header . $encdata;
    $crc = crc16($msg);
    $msg .= pack('C2', (($crc & 0xff00) >> 8), ($crc & 0xff));
    return $msg;
}

// Run Swoole coroutine context
Swoole\Coroutine\run(function () use ($players, $serverHost, $serverPort, $interval, $rateLimit, $verbose) {

    // Create UI Monitoring Scheduler Coroutine (only if not verbose)
    if (!$verbose) {
        Swoole\Coroutine::create(function () use ($interval, $serverHost, $serverPort) {
            $startTime = time();
            while (true) {
                // Clear console
                echo "\033[2J\033[H";
                $elapsed = time() - $startTime;
                $activePct = StressMetrics::$total > 0 ? round((StressMetrics::$running / StressMetrics::$total) * 100, 1) : 0;

                // Progress Bar representation
                $barWidth = 40;
                $filledCount = round(($activePct / 100) * $barWidth);
                $bar = str_repeat("=", $filledCount) . str_repeat(".", $barWidth - $filledCount);

                echo "============================================================\n";
                echo "             iCAT6 DIGITAL SIGNAGE STRESS TESTER             \n";
                echo "============================================================\n";
                echo "Time Elapsed: " . sprintf("%02d:%02d:%02d", $elapsed / 3600, ($elapsed % 3600) / 60, $elapsed % 60) . " | Test Interval: {$interval}s\n";
                echo "Target Server: {$serverHost}:{$serverPort}\n";
                echo "------------------------------------------------------------\n";
                echo "Players Pool      Total in Stress: " . StressMetrics::$total . "\n";
                echo "Phase Status      Spawning: " . StressMetrics::$connecting . " | Online: " . StressMetrics::$running . " | Failed: " . StressMetrics::$failed . "\n";
                echo "Throughput (Pkt)  Logins Sent/Rcvd:  " . StressMetrics::$loginsSent . " / " . StressMetrics::$loginsReceived . "\n";
                echo "                  Heartbeats S/R:    " . StressMetrics::$heartbeatsSent . " / " . StressMetrics::$heartbeatsReceived . "\n";
                echo "Network Events    Lost/Disconnected: " . StressMetrics::$disconnects . "\n";
                echo "Progress HUD      [{$bar}] {$activePct}%\n";
                echo "------------------------------------------------------------\n";
                echo "Last Event Logs:\n";
                if (empty(StressMetrics::$errors)) {
                    echo "  No error reports. System is running cleanly.\n";
                } else {
                    foreach (StressMetrics::$errors as $err) {
                        echo "  $err\n";
                    }
                }
                echo "============================================================\n";

                Swoole\Coroutine::sleep(1);
            }
        });
    }

    // Spawn player coroutines staged by rate limits
    $spawnDelay = 1.0 / $rateLimit; // delay between each client spawn in seconds

    foreach ($players as $index => $playerData) {
        $sn = $playerData['sn'];
        $name = $playerData['name'];

        StressMetrics::$connecting++;

        Swoole\Coroutine::create(function () use ($sn, $name, $serverHost, $serverPort, $interval, $verbose) {
            $client = null;
            $online = false;

            while (true) {
                try {
                    $client = new Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);

                    // Connection timeout of 5s
                    if (!$client->connect($serverHost, $serverPort, 5.0)) {
                        throw new Exception("TCP Connection Timeout/Failure");
                    }

                    // Mark as connected/online (bypassing login handshake, going straight to heartbeat)
                    if (!$online) {
                        StressMetrics::$connecting--;
                        StressMetrics::$running++;
                        $online = true;
                    }

                    // Send initial heartbeat immediately upon connection
                    $hbMsg = buildHeartbeatPacket($sn);
                    StressMetrics::$heartbeatsSent++;
                    if ($verbose) {
                        echo "[" . date("H:i:s") . "] [{$sn}] Sending Initial Heartbeat...\n";
                    }

                    if (!$client->send($hbMsg)) {
                        throw new Exception("Send failed during Initial Heartbeat");
                    }

                    $reply = $client->recv(5.0);
                    if (!$reply) {
                        throw new Exception("No response received for Initial Heartbeat");
                    }

                    StressMetrics::$heartbeatsReceived++;
                    if ($verbose) {
                        echo "[" . date("H:i:s") . "] [{$sn}] Initial Heartbeat ack received\n";
                    }

                    // Sustained heartbeat loop
                    while ($online) {
                        // Sleep for the heartbeat interval
                        Swoole\Coroutine::sleep($interval);

                        $hbMsg = buildHeartbeatPacket($sn);
                        StressMetrics::$heartbeatsSent++;
                        if ($verbose) {
                            echo "[" . date("H:i:s") . "] [{$sn}] Sending Heartbeat...\n";
                        }

                        if (!$client->send($hbMsg)) {
                            throw new Exception("Send failed during Heartbeat");
                        }

                        $reply = $client->recv(5.0);
                        if (!$reply) {
                            throw new Exception("No response received for Heartbeat");
                        }

                        StressMetrics::$heartbeatsReceived++;
                        if ($verbose) {
                            echo "[" . date("H:i:s") . "] [{$sn}] Heartbeat ack received\n";
                        }
                    }
                } catch (\Throwable $e) {
                    if ($online) {
                        StressMetrics::$running--;
                        StressMetrics::$disconnects++;
                        $online = false;
                    } else {
                        StressMetrics::$connecting--;
                        StressMetrics::$failed++;
                    }

                    $errMsg = "Player {$sn} ({$name}) error: " . $e->getMessage();
                    addError($errMsg);
                    if ($verbose) {
                        echo "[ERROR] $errMsg\n";
                    }

                    if (isset($client) && $client instanceof Swoole\Coroutine\Client) {
                        @$client->close();
                    }

                    // Restagger reconnecting
                    Swoole\Coroutine::sleep(rand(5, 10));
                    StressMetrics::$connecting++;
                }
            }
        });

        // Wait before spawning the next to throttle setup surge
        Swoole\Coroutine::sleep($spawnDelay);
    }
});
