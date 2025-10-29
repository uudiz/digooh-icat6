<?php 
/*
 * Created on 2011-12-12
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
if (!function_exists('generate_company_code')) {

    /**
     * 生成公司码
     */
    function generate_company_code($companyName) {
        $tmp = $companyName.time();
        
        $len = strlen($tmp);
        $code = 0;
        for ($i = 0; $i < 3; $i++) {
            $code = $code + ord($tmp [rand(0, $len - 1)] );
        }
        if ($code != 999) {
            return $code;
        } else {
            return generate_company_code($tmp);
        }
    }
}

if (!function_exists('encode_player_code')) {
    /**
     * 格式化显示序列号
     *
     * @param object $code
     * @return
     */
    function encode_player_code($code) {
        $retCode = substr($code, 0, 3).'-'.substr($code, 3, 3).'-'.substr($code, 6, 4);
        
        return $retCode;
    }
}

if (!function_exists('decode_player_code')) {
    /**
     * 将显示的序号处理成存储的格式
     * @param object $code
     * @return
     */
    function decode_player_code($code) {
        $temp = explode('-', $code);
        $retCode = "";
        for ($i = 0; $i < count($temp); $i++)
            $retCode .= $temp[$i];
            
        if ($retCode != '')
            return $retCode;
    }
}

if (!function_exists('generate_player_code')) {

    /**
     * 通过公司码计算生成客户终端码
     *
     * @param object $company_code
     * @return
     */
    function generate_player_code($company_code, $format = TRUE) {
    	if($company_code < 9){
    		$company_code = "00$company_code";
    	}elseif($company_code < 99){
    		$company_code = "0$company_code";
    	}
		
    	//if($company_code != 3){
    		//return FALSE;
    	//}
		
		$rand1 = get_random(3);
		$rand2 = get_random(3);
		$checksum = get_checksum($company_code, $rand1, $rand2);
		
		if($format){
			return $company_code.'-'.$rand1.'-'.$rand2.$checksum;
		}else{
			return $company_code.$rand1.$rand2.$checksum; 
		}
    }
}

if (!function_exists('get_random')) {
	/**
	 * 获取随即的指定长度的数字字符
	 * 
	 * @param object $length
	 * @return 
	 */
    function get_random($length) {
        $char = 'ABCDEFGHIJKLMNOPQRSTUVWZYZ';
        $char .= strtolower($char);
        $len = strlen($char);
        $code = 0;
		
        for ($i = 0; $i < $length; $i++) {
            $code = $code + ord($char[rand(0, $len - 1)] );
        }

		$code_len = strlen($code);
		if($code_len > $length){
			$code = substr($code, 0, $len);
			
		}else if($code_len < $length){
			for($i =0; $i < $length-$code_len; $i++){
				$code .='0';
			}
		}
        
        return $code;
    }
}

if(!function_exists('get_checksum')){
	/**
	 * 获取校验码
	 * 
	 * @param object $company_code
	 * @param object $rand1
	 * @param object $rand2
	 * @return 
	 */
	function get_checksum($company_code,$rand1,$rand2)
	{
	 	$code = intval($company_code)+intval($rand1)+intval($rand2);
		
	 	return $code%9;
	}
}

if(!function_exists('format_sn')){
	/**
	 * 格式化输出sn
	 * @param object $sn
	 * @return 
	 */
	function format_sn($sn){
		$result = '';
		$result .= substr($sn, 0, 3);
		$result .='-';
		$result .= substr($sn, 3, 3);
		$result .='-';
		$result .= substr($sn, 6);
		return $result;
	}
}

