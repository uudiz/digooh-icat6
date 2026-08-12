<?php

/**
 * iCAT6 DOOH Digital Signage CMS - Player Scaffold Tool
 *
 * This script allows creating or finding a Company by name,
 * and generating a custom number of simulated players under that company.
 */

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

// Load DB config from swoole_config.php or swoole_config.sample.php
$configPath = __DIR__ . '/swoole_config.php';
if (!file_exists($configPath)) {
    $configPath = __DIR__ . '/swoole_config.sample.php';
}

if (!file_exists($configPath)) {
    die("Error: swoole_config.php or swoole_config.sample.php not found.\n");
}

require_once $configPath;

function printUsage()
{
    echo <<<USAGE
iCAT6 Player Generator Tool

Usage: php generate_players.php [options]

Required Options:
  -c <company_name>  The Name of the Company to search for or create.
  -n <count>         The number of players to generate under this company.

Options:
  --help             Show this help screen

Example:
  php generate_players.php -c "Test Corporation" -n 2000
USAGE;
    echo "\n";
}

$opts = getopt("c:n:", ["help"]);
if (is_array($opts) && isset($opts['help'])) {
    printUsage();
    exit(0);
}

$companyName = isset($opts['c']) ? trim($opts['c']) : '';
$playerCount = isset($opts['n']) ? intval($opts['n']) : 0;

if (empty($companyName) || $playerCount <= 0) {
    echo "Error: Both -c (company name) and -n (number of players to generate) are required.\n\n";
    printUsage();
    exit(1);
}

// Connect to Database
$dbHost = isset($db_server['host']) ? $db_server['host'] : '127.0.0.1';
$dbPort = isset($db_server['port']) ? $db_server['port'] : 3306;
$dbUser = isset($db_server['user']) ? $db_server['user'] : 'icat';
$dbPass = isset($db_server['password']) ? $db_server['password'] : 'icat';
$dbName = isset($db_server['name']) ? $db_server['name'] : 'digtest';

echo "Connecting to Database: {$dbHost}:{$dbPort} ({$dbName}) ... ";
$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($mysqli->connect_error) {
    echo "FAILED!\n";
    die("Database Connection Error: " . $mysqli->connect_error . "\n");
}
echo "SUCCESS!\n";

// 1. Check if Company exists
$escCompanyName = $mysqli->real_escape_string($companyName);
$query = "SELECT id, code FROM cat_company WHERE name = '$escCompanyName' AND flag = 0 LIMIT 1";
$res = $mysqli->query($query);

$companyId = null;
$companyCode = null;
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $companyId = intval($row['id']);
    $companyCode = isset($row['code']) ? trim($row['code']) : '';
    if (empty($companyCode) || $companyCode == '0') {
        $companyCode = generate_company_code($companyName, $mysqli);
        $mysqli->query("UPDATE cat_company SET code = '" . $mysqli->real_escape_string($companyCode) . "' WHERE id = $companyId");
    }
    echo "Found existing company '{$companyName}' with ID: $companyId, Code: $companyCode\n";
} else {
    echo "Company '{$companyName}' does not exist. Creating it now... ";

    $companyCode = generate_company_code($companyName, $mysqli);
    // Insert new company with basic defaults compatible with schema
    $insertQuery = "INSERT INTO cat_company 
        (name, descr, total_disk, time_zone, start_date, max_user, flag, storage_usage, theme_color, code) 
        VALUES 
        ('$escCompanyName', 'Auto-generated for stress testing', 10000000000, 'UM8', CURDATE(), 50, 0, 0, '#951b80', '" . $mysqli->real_escape_string($companyCode) . "')";

    if ($mysqli->query($insertQuery)) {
        $companyId = $mysqli->insert_id;
        echo "SUCCESS! Created Company '{$companyName}' with ID: $companyId, Code: $companyCode\n";
    } else {
        die("FAILED to create Company: " . $mysqli->error . "\n");
    }
}

// 2. Determine player name suffix sequence and starting sequence for players of this company
$checkQuery = "SELECT name FROM cat_player WHERE company_id = $companyId AND name LIKE 'Testplayer_{$companyId}_%'";
$res = $mysqli->query($checkQuery);

$startSeq = 1;
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $pName = $row['name'];
        $parts = explode('_', $pName);
        if (count($parts) === 3) {
            $seq = intval($parts[2]);
            if ($seq >= $startSeq) {
                $startSeq = $seq + 1;
            }
        }
    }
}

echo "Generating {$playerCount} players. Starting Sequence = {$startSeq}...\n";

// Use transactions to make inserts extremely fast (important for generating 2000 players!)
$mysqli->begin_transaction();

$successfulInserts = 0;
$generatedSNs = [];
try {
    for ($i = 0; $i < $playerCount; $i++) {
        $sequence = $startSeq + $i;

        // Ensure SN is generated according to rules and completely unique
        do {
            $sn = generate_player_code($companyCode, false);
        } while (isset($generatedSNs[$sn]) || check_sn_exist_db($sn, $mysqli));
        $generatedSNs[$sn] = true;

        // Player Name: Testplayer_<companyId>_<sequence> 
        $pName = "Testplayer" . "_" . $companyId . "_" . $sequence;

        // Generate pseudo-random unique MAC address
        $mac = sprintf("00:16:3e:%02x:%02x:%02x", rand(0, 255), rand(0, 255), rand(0, 255));

        // Status 1: Offline (default, the script updates it anyway)
        $insertPlayer = "INSERT INTO cat_player 
            (sn, name, descr, company_id, mac, status, player_type) 
            VALUES 
            ('$sn', '$pName', 'Stress simulation player', $companyId, '$mac', 1, 1)";

        if ($mysqli->query($insertPlayer)) {
            $successfulInserts++;
        } else {
            throw new Exception("MySQL Error inserting Player '{$pName}': " . $mysqli->error);
        }
    }

    $mysqli->commit();
    echo "SUCCESS: Successfully generated and committed {$successfulInserts} players in database!\n";
    echo "============================================================\n";
    echo "You can now run stress_test.php with company ID '{$companyId}':\n";
    echo "  php stress_test.php -c {$companyId}\n";
    echo "============================================================\n";
} catch (Exception $e) {
    $mysqli->rollback();
    echo "An error occurred. Database updates rolled back.\n";
    echo "Error message: " . $e->getMessage() . "\n";
}

$mysqli->close();


// =========================================================================
// S/N Code Generation Helper Functions (Direct port from serial_helper.php)
// =========================================================================

function get_checksum($company_code, $rand1, $rand2)
{
    $code = intval($company_code) + intval($rand1) + intval($rand2);
    return $code % 9;
}

function get_random($length)
{
    $char = 'ABCDEFGHIJKLMNOPQRSTUVWZYZ';
    $char .= strtolower($char);
    $len = strlen($char);
    $code = 0;
    for ($i = 0; $i < $length; $i++) {
        $code = $code + ord($char[rand(0, $len - 1)]);
    }
    $code_len = strlen($code);
    if ($code_len > $length) {
        $code = substr($code, 0, $length);
    } else if ($code_len < $length) {
        for ($i = 0; $i < $length - $code_len; $i++) {
            $code .= '0';
        }
    }
    return $code;
}

function generate_player_code($company_code, $format = TRUE)
{
    if ($company_code < 9) {
        $company_code = "00$company_code";
    } elseif ($company_code < 99) {
        $company_code = "0$company_code";
    }
    $rand1 = get_random(3);
    $rand2 = get_random(3);
    $checksum = get_checksum($company_code, $rand1, $rand2);
    if ($format) {
        return $company_code . '-' . $rand1 . '-' . $rand2 . $checksum;
    } else {
        return $company_code . $rand1 . $rand2 . $checksum;
    }
}

function generate_company_code($companyName, $mysqli)
{
    $tmp = $companyName . time();
    $len = strlen($tmp);
    $code = 0;
    for ($i = 0; $i < 3; $i++) {
        $code = $code + ord($tmp[rand(0, $len - 1)]);
    }
    if ($code == 999) {
        return generate_company_code($tmp, $mysqli);
    }
    $escCode = $mysqli->real_escape_string($code);
    $checkRes = $mysqli->query("SELECT id FROM cat_company WHERE code = '$escCode' LIMIT 1");
    if ($checkRes && $checkRes->num_rows > 0) {
        return generate_company_code($tmp, $mysqli);
    }
    return $code;
}

function check_sn_exist_db($sn, $mysqli)
{
    $escSn = $mysqli->real_escape_string($sn);
    $res = $mysqli->query("SELECT id FROM cat_player WHERE sn = '$escSn' LIMIT 1");
    return ($res && $res->num_rows > 0);
}
