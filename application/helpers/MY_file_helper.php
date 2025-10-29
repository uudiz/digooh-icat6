<?php

/**
 * 保存文件
 * 
 * @param object $destFile
 * @param object $content
 * @return 
 */
function saveFile($destFile, $content)
{

	$handle = fopen($destFile, 'w');
	if ($handle) {
		fwrite($handle, $content);
		fclose($handle);
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
 * 保存FTP文件,支持JPEG和JPG
 * 
 * @param string $ftpUrl
 * @param string $savePath
 * 
 */
function downloadRemoteFile($remoteUrl, $savePath, $force = FALSE)
{
	$ext = getFileType($remoteUrl);

	if ($ext != 'jpeg' && $ext != 'jpg') {
		return FALSE;
	}

	if (!file_exists($savePath) || !is_dir($savePath)) {
		mkdir($savePath, 0777, TRUE);
	}

	if (!is_dir($savePath)) {
		return FALSE;
	}

	$md5 = md5($remoteUrl);
	$newFile = $savePath . $md5 . '.' . $ext;
	if (file_exists($newFile) && $force === FALSE) {
		return $newFile;
	}

	$img = @file_get_contents($remoteUrl);
	if ($img === FALSE) {
		return FALSE;
	}


	$h = fopen($newFile, 'w');
	if ($h) {
		fwrite($h, $img);
		fclose($h);

		return $newFile;
	} else {
		return FALSE;
	}
}

/**
 * 下载文件
 * 
 * @param object $filePath
 * @return 
 */
function downloadFile($filePath, $aliasName = FALSE)
{
	if (file_exists($filePath)) {
		$ftime = filemtime($filePath);
		@header("Cache-control: public");
		@header("Pragma: public");
		@header("Last-Modified: " . gmdate("D, d M Y H:i:s", $ftime) . " GMT");

		//支持If-Modified-Since
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $ftime)) {
			header('HTTP/1.1 304 Not Modified');
			header('Connection: close');
			return FALSE;
		}

		$fsize = filesize($filePath);
		$start = 0;
		$end = 0;
		if (isset($_SERVER['HTTP_RANGE']) && ($_SERVER['HTTP_RANGE'] != "")) {
			//bytes=0-xx 
			$range = str_replace('=', '-', $_SERVER['HTTP_RANGE']);
			$range = explode('-', $range);
			$start = trim($range[1]);
			if (trim($range[2]) == '') {
				$end = $fsize - 1;
			} else {
				$end = intval(trim($range[2]));
			}
		} else {
			$start = 0;
		}
		if ($aliasName) {
			$fileName = $aliasName;
		} else {
			$fileName = getFileName($filePath);
		}

		header("Content-type: " . getContentType(getFileType($fileName)));
		header("Accept-Ranges: bytes");
		header("Content-Disposition: attachment; filename=" . urlencode($fileName));
		$handler = fopen($filePath, 'r');
		if (isset($_SERVER['HTTP_RANGE']) && ($_SERVER['HTTP_RANGE'] != "")) {
			fseek($handler, $start);
			header("HTTP/1.1 206 Partial Content");

			if ($end >= $start) {
				header("Content-Ranges: bytes " . $start . "-" . $end . "/" . $fsize);
				header("Content-Length: " . (($end - $start) + 1));
			} else {
				header("Content-Ranges: bytes " . $start . "-" . ($fsize - 1) . "/" . $fsize);
				header("Content-Length: " . ($fsize - $start));
			}
			//header("Connection: close"."\n\n"); 

		} else {
			header("Content-Ranges: bytes " . $start . "-" . ($fsize - 1) . "/" . $fsize);
			header("Content-Length: $fsize");
		}
		//range
		if ($end > $start) {
			$out = fread($handler, ($end - $start + 1));
			echo $out;
			flush();
		} else {
			while (!feof($handler)) {
				$out = fread($handler, 4096);
				echo $out;
				flush();
			}
		}
		@fclose($handler);

		return TRUE;
	}
	return FALSE;
}
/**
 * NP200下载文件
 * 
 * @param string $filePath
 * @return 
 */
function downloadNP200File($filePath, $aliasName = FALSE, $headers = FALSE)
{
	if (file_exists($filePath)) {
		$ftime = filemtime($filePath);
		@header("Cache-control: public");
		@header("Pragma: public");
		@header("Last-Modified: " . gmdate("D, d M Y H:i:s", $ftime) . " GMT");

		//支持If-Modified-Since
		/*
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $ftime)) {
			header('HTTP/1.1 304 Not Modified');
    		header('Connection: close');
			return FALSE;
		}*/

		$fsize = filesize($filePath);
		$start = 0;
		$end = 0;
		$has_range = false;
		if (isset($_SERVER['HTTP_RANGE']) && ($_SERVER['HTTP_RANGE'] != "")) {
			//bytes=0-xx 
			$range = str_replace('=', '-', $_SERVER['HTTP_RANGE']);
			$range = explode('-', $range);
			$start = trim($range[1]);
			if (trim($range[2]) == '') {
				$end = $fsize - 1;
			} else {
				$end = intval(trim($range[2]));
			}
		} else {
			$start = 0;
		}
		if ($aliasName) {
			$fileName = $aliasName;
		} else {
			$fileName = getFileName($filePath);
		}

		header("Content-type: " . getContentType(getFileType($fileName)));
		header("Accept-Ranges: bytes");
		header("Content-Disposition: attachment; filename=" . urlencode($fileName));
		$handler = fopen($filePath, 'r');

		if (isset($_SERVER['HTTP_RANGE']) && ($_SERVER['HTTP_RANGE'] != "")) {
			fseek($handler, $start);
			header("HTTP/1.1 206 Partial Content");
			$has_range = true;
			if ($end >= $start) {
				header("Content-Ranges: bytes " . $start . "-" . $end . "/" . $fsize);
				header("Content-Length: " . (($end - $start) + 1));
			} else {
				header("Content-Ranges: bytes " . $start . "-" . ($fsize - 1) . "/" . $fsize);
				header("Content-Length: " . ($fsize - $start));
			}
			//header("Connection: close"."\n\n"); 

		} else {
			header("Content-Ranges: bytes " . $start . "-" . ($fsize - 1) . "/" . $fsize);
			header("Content-Length: $fsize");
		}

		if ($headers && is_array($headers)) {
			foreach ($headers as $h) {
				header($h);
			}
		}

		//range
		if ($has_range && $end >= $start) {
			$out = fread($handler, ($end - $start + 1));
			echo $out;
			flush();
		} else {
			while (!feof($handler)) {
				$out = fread($handler, 4096);
				echo $out;
				flush();
			}
		}
		@fclose($handler);

		return TRUE;
	}
	return FALSE;
}
/**
 * 下载指定内容，支持If-Modified-Since方式下载
 * 
 * @param string $content
 * @param object $txt
 * @param object $mtime [optional]
 * @param string $fileName [optional]
 * @return 
 */
function downloadContent($content, $fileName = FALSE, $headers = FALSE)
{
	@header("Cache-control: public");
	@header("Pragma: public");

	if ($fileName) {
		header("Content-type: " . getContentType(getFileType($fileName)));
		header("Accept-Ranges: bytes");
		header("Content-Disposition: attachment; filename=" . urlencode($fileName));
	} else {
		header("Content-type: " . getContentType($type));
	}

	//if($mtime == 0){
	$mtime = time();
	//}

	@header("Last-Modified: " . gmdate("D, d M Y H:i:s", $mtime) . " GMT");
	/*
	//支持If-Modified-Since
	if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $mtime)) {
		header('HTTP/1.1 304 Not Modified');
		header('Connection: close');
		return TRUE;
	}
	*/
	$length = strlen($content);
	header("Content-Length: " . $length);
	if ($headers && is_array($headers)) {
		foreach ($headers as $h) {
			header($h);
		}
	}

	echo $content;
	flush();

	return TRUE;
}


function getFileName($filePath)
{
	$array =  explode('/', $filePath);

	return $array[count($array) - 1];
}

function getFileType($fileName)
{
	$array =  explode('.', $fileName);
	return strtolower($array[count($array) - 1]);
}

function getContentType($type)
{
	$ret = 'application/octet-stream';
	switch (strtolower($type)) {
		case 'pls':
		case 'sch':
		case 'xml':
		case 'cfg':
			$ret = 'text/xml';
			break;
		case 'asf':
			$ret = 'video/x-ms-asf';
			break;
		case 'avi':
			$ret = 'video/x-msvideo';
			break;
		case 'JPG':
		case 'JPEG';
			$ret = 'image/jpeg';
			break;
		case 'mp3':
			$ret = 'audio/x-mpeg';
			break;
		case 'wav':
			$ret = 'audio/x-wav';
			break;
		case 'mpg':
		case 'mpeg':
			$ret = 'video/mpeg';
			break;
		case 'txt':
			$ret = 'text/plain';
			break;
	}

	return $ret;
}

/**
 * 获取rom文件中的版本信息
 * 
 * @param object $file
 * @param object $offset [optional]
 * @param object $len [optional]
 * @return 
 */
function get_rom_version($file, $offset = 0x10, $len = 16)
{
	if (!file_exists($file) || filesize($file) < ($offset + $len)) {
		return FALSE;
	}

	$version = FALSE;

	$handle = fopen($file, 'r');
	if ($handle) {
		fseek($handle, $offset);
		$version = fread($handle, $len);

		if ($version) {
			$version = trim($version);
		}
		fclose($handle);
	}


	return $version;
}

/**
 * 获取升级软件的版本信息
 * 
 * @param string $file
 * @return 
 */
function get_img_info($file)
{
	if (file_exists($file) && filesize($file) > 1024) {
		$offset = filesize($file) - 1024;
		$h = fopen($file, 'r');
		if ($h) {
			$id = "DELT/SW-110/USER";
			fseek($h, $offset);
			$identifier = fread($h, 16);
			if ($identifier) {
				$identifier = trim($identifier);
				if ($identifier != $id) {
					return FALSE;
				}
			}
			$version = fread($h, 16);
			if ($version) {
				$version = trim($version);
			}
			$mpeg_core = fread($h, 32);
			if ($mpeg_core) {
				$mpeg_core = trim($mpeg_core);
				$core_type = 0;
				if (strstr($mpeg_core, '5166')) {
					$core_type = 2; //5166固件类型的软件
				} else if (strstr($mpeg_core, '5186')) {
					$core_type = 3; //5188固件类型的软件,NP300
				} else if (strstr($mpeg_core, '5161')) {
					$core_type = 1;
				} else if (strstr($mpeg_core, '3568')) {
					$core_type = 4; //3568
				} else {
					return false;
				}
				$mpeg_core = $core_type;
			}
			$copyright = fread($h, 32);
			if ($copyright) {
				$copyright = trim($copyright);
			}
			$publish_time = fread($h, 32);
			if ($publish_time) {
				$str = trim($publish_time);
				$pos = strpos($str, ']');
				if ($pos !== false) {
					$publish_time = substr($str, 0, $pos + 1);
					$dt = DateTime::createFromFormat('Y/m/d[H:i:s]', $publish_time);
					$publish_time = $dt->format('Y-m-d H:i:s');
				}
			}
			fclose($h);

			return array('identifier' => $identifier, 'version' => $version, 'mpeg_core' => $mpeg_core, 'copyright' => $copyright, 'publish_time' => $publish_time);
		}
	}

	return FALSE;
}

function np_download($filename = '', $data = NULL, $aliasName = NULL, $headers = NULL)
{
	if ($filename === '' or $data === '') {
		return false;
	} elseif ($data === NULL) {

		if (!@is_file($filename) or ($filesize = @filesize($filename)) === FALSE) {
			header("HTTP/1.1 404 Partial Content");
		}

		$ftime = filemtime($filename);
		$filepath = $filename;
		$filename = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $filename));
		$filename = end($filename);
	} else {
		$filesize = strlen($data);
		$ftime = time();
		if ($aliasName) {
			$filename = $aliasName;
		}
	}

	// Set the default MIME type to send
	$mime = 'application/xml';

	$x = explode('.', $filename);
	$extension = end($x);
	$start = 0;
	$end = $filesize - 1;


	// Load the mime types
	$mimes = &get_mimes();

	// Only change the default MIME if we can find one
	if (isset($mimes[$extension])) {
		$mime = is_array($mimes[$extension]) ? $mimes[$extension][0] : $mimes[$extension];
	}


	/* It was reported that browsers on Android 2.1 (and possibly older as well)
		 * need to have the filename extension upper-cased in order to be able to
		 * download it.
		 *
		 * Reference: http://digiblog.de/2011/04/19/android-and-the-download-file-headers/
		 */
	if (count($x) !== 1 && isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/Android\s(1|2\.[01])/', $_SERVER['HTTP_USER_AGENT'])) {
		$x[count($x) - 1] = strtoupper($extension);
		$filename = implode('.', $x);
	}

	if ($data === NULL && ($fp = @fopen($filepath, 'rb')) === FALSE) {
		return false;
	}

	if (isset($_SERVER['HTTP_RANGE']) && ($_SERVER['HTTP_RANGE'] != "")) {
		$range = str_replace('=', '-', $_SERVER['HTTP_RANGE']);
		$range = explode('-', $range);
		$start = trim($range[1]);
		if (trim($range[2]) == '') {
			$end = $filesize - 1;
		} else {
			$end = intval(trim($range[2]));
		}

		if ($data === NULL && isset($fp)) {
			fseek($fp, $start);
		}


		if ($start >= 0 && $end >= $start && $end < $filesize) {
			header("HTTP/1.1 206 Partial Content");
			header("Content-Ranges: bytes " . $start . "-" . $end . "/" . $filesize);
			//header("Content-Length: " . (($end - $start) + 1));
			$filesize = $end - $start + 1;
		} else {
			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			header('Content-Range: bytes */' . $filesize); // Required in 416.
			fclose($fp);
			return false;
		}
	}

	// Clean output buffer
	if (ob_get_level() !== 0 && @ob_end_clean() === FALSE) {
		@ob_clean();
	}

	// Generate the server headers
	header('Content-Type: ' . $mime);

	header("Last-Modified: " . gmdate("D, d M Y H:i:s", $ftime) . " GMT");
	header('Content-Disposition: attachment; filename="' . ($aliasName ? $aliasName : $filename) . '"');
	header('Expires: 0');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: ' . $filesize);
	header('Cache-Control: private, no-transform, no-store, must-revalidate');

	if ($headers && is_array($headers)) {
		foreach ($headers as $h) {
			header($h);
		}
	}
	// If we have raw data - just dump it

	if ($data !== NULL) {
		$str = substr($data, $start, $end - $start + 1);
		echo $str;
		if ($start == 0 && $end == 0) {
			return false;
		}
		return true;
	}

	// Flush 1MB chunks of data
	while (!feof($fp) && ($pos = ftell($fp)) <= $end && ($data = fread($fp, min(1048576, $end - $pos + 1))) !== FALSE) {
		echo $data;
	}

	fclose($fp);
	return true;
}
