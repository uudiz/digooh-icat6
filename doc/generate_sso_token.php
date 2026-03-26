<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "This helper can only run from CLI.\n";
    exit(1);
}


$options = array(
    'uid' =>  215,
    'path' =>  'player',
    'issuer' =>  'www.example.com',
    'secret' => 'd90b2e90fc239dceef85fd73b3a029690e6e2324379cf83a9bc99b6c67a18a32',
    'ttl' =>  120,
    'base-url' => 'https://cms-stage.digooh.com',
);


$uid = isset($options['uid']) ? (int) $options['uid'] : 215;
$path = isset($options['path']) ? (string) $options['path'] : 'player';
$issuer = isset($options['issuer']) ? trim((string) $options['issuer']) : 'www.example.com';
$secret = isset($options['secret']) ? (string) $options['secret'] : 'd90b2e90fc239dceef85fd73b3a029690e6e2324379cf83a9bc99b6c67a18a32';
$ttl = isset($options['ttl']) ? (int) $options['ttl'] : 120;
$baseUrl = isset($options['base-url']) ? trim((string) $options['base-url']) : 'http://localhost:3061';

$iat = time();
$exp = $iat + $ttl;
$jti = bin2hex(random_bytes(16));

$header = array(
    'alg' => 'HS256',
    'typ' => 'JWT',
);

$payload = array(
    'iss' => $issuer,
    'uid' => $uid,
    'iat' => $iat,
    'exp' => $exp,
    'jti' => $jti,
    'path' => $path,
);

$token = buildJwtHs256($header, $payload, $secret);
$redirectUrl = rtrim($baseUrl, '/') . '/login/sso?token=' . rawurlencode($token) . '&path=' . rawurlencode($path);

echo "SSO token generated successfully.\n\n";
echo "uid: " . $uid . "\n";
echo "path: " . $path . "\n";
echo "iss: " . $issuer . "\n";
echo "iat: " . $iat . "\n";
echo "exp: " . $exp . "\n";
echo "jti: " . $jti . "\n\n";

echo "JWT:\n";
echo $token . "\n\n";

echo "Redirect URL:\n";
echo $redirectUrl . "\n";

if ($secret === 'replace-with-strong-shared-secret') {
    echo "\nWarning: You are using the default test secret. Replace it with your real shared secret.\n";
}

function buildJwtHs256(array $header, array $payload, string $secret): string
{
    $headerEncoded = base64urlEncode(json_encode($header));
    $payloadEncoded = base64urlEncode(json_encode($payload));
    $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $secret, true);
    $signatureEncoded = base64urlEncode($signature);

    return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
}

function base64urlEncode(string $value): string
{
    return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
}
