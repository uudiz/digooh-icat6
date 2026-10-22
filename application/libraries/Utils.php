<?php
class Utils {
	function crc16($string) {
		$crc = 0xFFFF;
		for ($x = 0; $x < strlen($string); $x++) {
			$crc = $crc ^ ord($string[$x]);
			for ($y = 0; $y < 8; $y++) {
				if (($crc & 0x0001) == 0x0001) {
					$crc = (($crc >> 1) ^ 0xA001);
				} else {
					$crc = $crc >> 1;
				}
			}
		}
		//¸ßµÍ×Ö½Ú½»»»
		$high = ($crc & 0xFF00) >> 8;
		$low = $crc & 0xFF;
		$crc = 0xFFFF;
		$crc = (($low << 8) & 0xFF00) | $high;
		return $crc;
	}

	function make_openssl_blowfish_key($key)
	{
	    if("$key" === '')
	        return $key;

	    $len = (16+2) * 4;
	    while(strlen($key) < $len) {
	        $key .= $key;
	    }
	    $key = substr($key, 0, $len);
	    return $key;
	}

	function blowfish_enc($clearData) {
		
		$key = "4tGhK12p";
		$blockSize = 8;
	   	$len = strlen($clearData);
	    $paddingLen = intval(($len + $blockSize - 1) / $blockSize) * $blockSize - $len;
	    $padding = str_repeat("\0", $paddingLen);
	    $data = $clearData . $padding;
	   	 $key = $this->make_openssl_blowfish_key($key);

	   	$encrypted = openssl_encrypt($data, 'BF-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
	   	return $encrypted;
	}



	function blowfish_dec($encryptedData) {
		$key = "4tGhK12p";
		$key = $this->make_openssl_blowfish_key($key);

		return openssl_decrypt($encryptedData, 'BF-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);

		}
	}
