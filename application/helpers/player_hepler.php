<?php

/**
 * 向客户端发送命名
 * 
 * @param object $ip
 * @param object $cmd
 * @param object $value [optional]
 * @return 
 */
function sendCommand($ip, $cmd, $value=''){
	if(empty($cmd)){
		return FALSE;
	}
	
	$fp = @fsockopen($ip, 80, $errno, $errstr, 30);
	if(!$fp){
		return FALSE;
	}
	
	$query = sprintf('oper=%s&value=%s', $cmd, $value);
	fputs($fp, "GET "  . $query  . " HTTP/1.1\r\n"); 
	fputs($fp, "HOST: " . $host . "\r\n"); 
	fputs($fp, "User-Agent: http://www.mia.com/cmd\r\n");
	fputs($fp, "Connection: close\r\n\r\n"); 
	stream_set_timeout($fp, 5);
	$content = ""; 
	while (!feof($fp)) { 
		$content .= fgets ($fp, 1024); 
	} 
	fclose ($fp);
	
	if(preg_match("/^HTTP\/\d.\d 200 OK/is",$content)){
		return TRUE;
	}else{
		return FALSE;
	}
}
