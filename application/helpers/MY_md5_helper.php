<?php

if(!function_exists("mia_md5_file")){
	
	/**
	 * MD5 文件
	 * 
	 * @param object $file
	 * @return 
	 */
	function mia_md5_file($file){
		if(file_exists($file)){
			$fsize = filesize($file);
			$fss = pack('I', $fsize);
			$n = 0;
			if($fsize <= 2097152){
				$n = 32;
			}else if($fsize <= 838860){
				$n = 64;
			}else {
				$n = $fsize / 4194304;
			}
			
		}
		
		return FALSE;
	}
}
