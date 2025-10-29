<?php
function crc16($string)
{
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
	//高低字节交换
	$high = ($crc & 0xFF00) >> 8;
	$low = $crc & 0xFF;

	$crc = 0xFFFF;

	$crc = (($low << 8) & 0xFF00) | $high;

	return $crc;
}

function make_openssl_blowfish_key($key)
{
	if ($key === '')
		return $key;

	$len = (16 + 2) * 4;
	while (strlen($key) < $len) {
		$key .= $key;
	}
	$key = substr($key, 0, $len);
	return $key;
}

function blowfish_enc($clearData)
{

	$key = "4tGhK12p";
	$blockSize = 8;
	$len = strlen($clearData);
	$paddingLen = intval(($len + $blockSize - 1) / $blockSize) * $blockSize - $len;
	$padding = str_repeat("\0", $paddingLen);
	$data = $clearData . $padding;
	$key = make_openssl_blowfish_key($key);

	$encrypted = openssl_encrypt($data, 'BF-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
	return $encrypted;

	/*
   	
	$cipher = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_ECB, '');
	$key = "4tGhK12p";
	$iv = '00000000';
	if (mcrypt_generic_init($cipher, $key, $iv) != -1) {
		// PHP pads with NULL bytes if $cleartext is not a multiple of the block size..
		$cipherText = mcrypt_generic($cipher, $clearData);
		mcrypt_generic_deinit($cipher);

		mcrypt_module_close($cipher);

		echo "mctype enc:".bin2hex($cipherText).PHP_EOL;
		
		$blockSize = 8;
   		$len = strlen($clearData);
    	$paddingLen = intval(($len + $blockSize - 1) / $blockSize) * $blockSize - $len;
    	$padding = str_repeat("\0", $paddingLen);
    	$data = $clearData . $padding;
   	 	$key = make_openssl_blowfish_key($key);

   	 	$encrypted = openssl_encrypt($data, 'BF-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
		echo "openssl enc:".bin2hex($cipherText).PHP_EOL;

		return $cipherText;

	}
	return null;
*/
}



function blowfish_dec($encryptedData)
{
	$key = "4tGhK12p";
	$key = make_openssl_blowfish_key($key);

	return openssl_decrypt($encryptedData, 'BF-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);

	/*
	$cipher = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_ECB, '');
	$key = "4tGhK12p";
	$iv = '00000000';
	if (mcrypt_generic_init($cipher, $key, $iv) != -1) {
		$decyptedText = mdecrypt_generic($cipher, $encryptedData);
		mcrypt_generic_deinit($cipher);
		mcrypt_module_close($cipher);

		echo "mcrypt dec:".$decyptedText.PHP_EOL;
		 $key = make_openssl_blowfish_key($key);

		$str = openssl_decrypt($encryptedData, 'BF-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
		echo "openssl dec:".$str.PHP_EOL;
		return $decyptedText;
	}
	*/
}
