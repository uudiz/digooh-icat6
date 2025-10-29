<?php
class MY_FTP extends CI_FTP
{

	var $systype = FALSE;

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * 
	 * @param object $config [optional]
	 * @return 
	 */
	function connect($config = array())
	{
		if (parent::connect()) {
			$this->systype = @ftp_systype($this->conn_id);
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * 获取当前目录位置信息
	 * 
	 * @return 
	 */
	public function pwd()
	{
		if ($this->_is_conn()) {
			return @ftp_pwd($this->conn_id);
		}

		return FALSE;
	}

	/**
	 * 切换目录
	 * 
	 * @param object $dir
	 * @return 
	 */
	public function chdir($dir)
	{
		if ($this->_is_conn()) {
			return @ftp_chdir($this->conn_id, $dir);
		}

		return FALSE;
	}

	/**
	 * 显示当前目录下的媒体文件
	 * 
	 * @param object $path [optional]
	 * @param object $ext [optional]
	 * @return 
	 */
	public function list_media_files($path = '.', $ext = '*')
	{
		$buff = @ftp_rawlist($this->conn_id, $path);
		if (empty($buff) || count($buff) == 0) {
			return FALSE;
		}
		$result = array();
		$ext_array = FALSE;
		if ('*' != $ext) {
			$ext_array = preg_split('/[;]/', strtolower($ext));
		}

		foreach ($buff as $line) {
			$temp = $this->get_line_buffer($line);
			$file = FALSE;

			switch ($temp[0][0]) {
				case 'd':
					$file = new StdClass;
					$file->dir = TRUE;
					$file->name = $this->get_file_name($temp);
					//过滤上级目录
					if ($file->name == '.' || $file->name == '..') {
						break;
					}
					$result[] = $file;
					break;
				case '-':
					$file = new StdClass;
					$file->dir = FALSE;
					$file->name = $this->get_file_name($temp);
					$file->size = $this->get_file_size($temp);
					if ($ext_array) {
						$fext = $this->get_ext($file->name);
						if ($fext) {
							if (in_array(strtolower('*' . $fext), $ext_array)) {
								//只有满足条件的可以放到数组中
								$result[] = $file;
							}
						}
					} else {
						$result[] = $file;
					}
					break;
				case 0:
				case 1:
				case 2:
				case 3:
				case 4:
				case 5:
				case 6:
				case 7:
				case 8:
				case 9:
					//DOS
					if (@in_array('<DIR>', $temp)) {
						$file->dir = TRUE;
						$file->name = $this->get_dos_file_name($temp);
						$result[] = $file;
						break;
					} else {
						$file->dir = FALSE;
						$file->name = $this->get_dos_file_name($temp);
						$file->size = $this->get_file_size($temp);
						if ($ext_array) {
							$fext = $this->get_ext($file->name);
							if ($fext) {
								if (in_array(strtolower($fext), $ext_array)) {
									//只有满足条件的可以放到数组中
									$result[] = $file;
								}
							}
						} else {
							$result[] = $file;
						}
					}
					break;
			}
			unset($file);
		}
		return $result;
	}

	/**
	 * 获取解析的行信息
	 * 
	 * @param object $line
	 * @return 
	 */
	private function get_line_buffer($line)
	{
		return preg_split("/[\s]+/", $line, 9);
		/*
		$tempNew = explode(' ', $line);
		$temp = array();
		foreach($tempNew as $value){
				if(trim($value) != ''){
					$temp[] = $value;
			}
		}
		
		return $temp;*/
	}


	/**
	 * 获取Uninx的文件名称
	 * 
	 * @param object $line_buffer
	 * @return 
	 */
	private function get_unix_file_name($line_buffer)
	{
		$len = count($line_buffer);

		$file_name = FALSE;
		if ($len > 9) {
			//file name has blank
			for ($i = 8; $i < $len; $i++) {
				$file_name .= $line_buffer[$i];
			}
		} else if ($len == 9) {
			$file_name = $line_buffer[$len - 1];
		}

		return $file_name;
	}

	/**
	 * -------r--           0     7226     7226 Mar 21 14:27 Upcoming Events.htm
	 * drwxr-xr-x               folder        3 Mar 22 10:06 Upcoming Events_files
	 * 
	 * @param object $line_buffer
	 * @return 
	 */
	private function get_mac_file_name($line_buffer)
	{
		$file_name = FALSE;
		$len = count($line_buffer);
		$i = $line_buffer[0][0] == 'd' ? 6 : 7;
		for (; $i < $len; $i++) {
			$file_name .= $line_buffer[$i];
		}

		return $file_name;
	}

	/**
	 * 
	 * 2012-01-16  19:22    <DIR>          My Documents
	 * 2009-12-15  22:06                72 Tnetwork.bat
	 * 
	 * @param object $line_buffer
	 * @return 
	 */
	private function get_dos_file_name($line_buffer)
	{
		$file_name = FALSE;
		$len = count($line_buffer);
		for ($i = 3; $i < $len; $i++) {
			$file_name .= $line_buffer[$i];
		}

		return $file_name;
	}

	/**
	 * d [R----F--] supervisor            512       Jan 16 18:53    login
	 * - [R----F--] rhesus             214059       Oct 20 15:27    cx.exe
	 * 
	 * @param object $line_buffer
	 * @return 
	 */
	private function get_netware_file_name($line_buffer)
	{
		$file_name = FALSE;
		$len = count($line_buffer);
		for ($i = 7; $i < $len; $i++) {
			$file_name .= $line_buffer[$i];
		}

		return $file_name;
	}

	/**
	 * unix 
	 * -rw-r--r--    1 icatsign   icatsign        13205 Aug 23 20:13 RSSParser.php
	 * NetWare
	 * d [R----F--] supervisor            512       Jan 16 18:53    login
	 * - [R----F--] rhesus             214059       Oct 20 15:27    cx.exe
	 * 
	 * windows MSDOS
	 * ----------   1 owner    group         1803128 Jul 10 10:18 ls-lR.Z
	 * -rwxrwxrwx   1 noone    nogroup      322 Aug 19  1996 message.ftp
	 * 
	 * 
	 * 
	 * MACOS NetPresenz
	 * 
	 * -------r--           0     7226     7226 Mar 21 14:27 Upcoming Events.htm
	 * drwxr-xr-x               folder        3 Mar 22 10:06 Upcoming Events_files
	 * 
	 * @param object $line_buffer
	 * @return 
	 */
	private function get_file_name($line_buffer)
	{
		$name = FALSE;
		switch ($this->systype) {
			case 'UNIX':
				$name = $this->get_unix_file_name($line_buffer);
				break;
			case 'MACOS':
				$name = $this->get_mac_file_name($line_buffer);
				break;
			case 'NetWare':
				$name = $this->get_netware_file_name($line_buffer);
				break;
			default:
				$name = $this->get_unix_file_name($line_buffer);
				break;
		}

		return $name;
	}

	private function get_file_size($line_buffer)
	{
		$size = 0;
		switch ($this->systype) {
			case 'UNIX':
				$size = $line_buffer[4];
				break;
			case 'MACOS':
				$size = $line_buffer[3];
				break;
			case 'NetWare':
				$size = $line_buffer[3];
				break;
			default:
				$size = $line_buffer[4];
				break;
		}

		return $size;
	}

	/**
	 * 输入:test.txt
	 * 返回.txt
	 * 
	 * 输入:text.txt.bin
	 * 返回 .bin
	 * 
	 * @param object $file_name
	 * @return 
	 */
	private function get_ext($file_name)
	{
		//$array = split('[.]', $file_name);
		$array = preg_split('/[.]/', $file_name);
		$len = count($array);
		if ($len) {
			return '.' . $array[$len - 1];
		} else {
			return FALSE;
		}
	}
}
