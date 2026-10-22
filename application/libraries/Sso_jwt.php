<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sso_jwt
{
    /**
     * Verify HS256 JWT and required claims for SSO.
     *
     * @param string $token
     * @param array $issuers
     * @param string $secret
     * @param int $clockSkew
     * @param int $maxTtl
     * @return array
     */
    public function verify($token, $issuers, $secret, $clockSkew = 0, $maxTtl = 120)
    {
        $token = trim((string) $token);
        if ($token === '') {
            return array('ok' => false, 'error' => 'empty token');
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return array('ok' => false, 'error' => 'invalid token format');
        }

        list($headerB64, $payloadB64, $signatureB64) = $parts;
        $headerJson = $this->base64url_decode($headerB64);
        $payloadJson = $this->base64url_decode($payloadB64);
        $signature = $this->base64url_decode($signatureB64);
        if ($headerJson === false || $payloadJson === false || $signature === false) {
            return array('ok' => false, 'error' => 'invalid token encoding');
        }

        $header = json_decode($headerJson, true);
        $payload = json_decode($payloadJson, true);
        if (!is_array($header) || !is_array($payload)) {
            return array('ok' => false, 'error' => 'invalid token json');
        }

        if (!isset($header['alg']) || strtoupper((string) $header['alg']) !== 'HS256') {
            return array('ok' => false, 'error' => 'unsupported algorithm');
        }

        $signedData = $headerB64 . '.' . $payloadB64;
        $expectedSig = hash_hmac('sha256', $signedData, (string) $secret, true);
        if (!hash_equals($expectedSig, $signature)) {
            return array('ok' => false, 'error' => 'invalid signature');
        }

        $issuer = isset($payload['iss']) ? trim((string) $payload['iss']) : '';
        $uid = isset($payload['uid']) ? (int) $payload['uid'] : 0;
        $jti = isset($payload['jti']) ? trim((string) $payload['jti']) : '';
        $exp = isset($payload['exp']) ? (int) $payload['exp'] : 0;
        $iat = isset($payload['iat']) ? (int) $payload['iat'] : 0;
        $nbf = isset($payload['nbf']) ? (int) $payload['nbf'] : 0;

        if ($issuer === '' || $uid <= 0 || $jti === '' || $exp <= 0 || $iat <= 0) {
            return array('ok' => false, 'error' => 'missing required claims');
        }

        if (!in_array($issuer, (array) $issuers, true)) {
            return array('ok' => false, 'error' => 'issuer not allowed');
        }

        $clockSkew = max(0, (int) $clockSkew);
        $now = time();
        if ($iat > ($now + $clockSkew)) {
            return array('ok' => false, 'error' => 'token issued in the future');
        }
        if ($exp < ($now - $clockSkew)) {
            return array('ok' => false, 'error' => 'token expired');
        }
        if ($nbf > 0 && $nbf > ($now + $clockSkew)) {
            return array('ok' => false, 'error' => 'token not active yet');
        }

        $maxTtl = (int) $maxTtl;
        if ($maxTtl > 0 && ($exp - $iat) > ($maxTtl + $clockSkew)) {
            return array('ok' => false, 'error' => 'token ttl too long');
        }

        $payload['uid'] = $uid;

        return array('ok' => true, 'payload' => $payload);
    }

    private function base64url_decode($value)
    {
        $value = strtr((string) $value, '-_', '+/');
        $padding = strlen($value) % 4;
        if ($padding > 0) {
            $value .= str_repeat('=', 4 - $padding);
        }

        return base64_decode($value, true);
    }
}
