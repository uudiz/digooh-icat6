<?php

if (!function_exists('str_date_to_num')) {
	/**
	 * 通过输入的时间格式，如：2011-12-13 输出为20111212
	 * 
	 * @param object $date
	 * @param object $spliter [optional]
	 * @return 
	 */
	function str_date_to_num($date, $spliter = '[-]')
	{
		if (empty($date)) {
			return FALSE;
		}
		$CI = &get_instance();
		$CI->lang->load('my_date');
		$fmt = $CI->lang->line('fmt');
		list($$fmt[0], $$fmt[1], $$fmt[2]) = preg_split($spliter, $date);
		return intval($y . $m . $d);
	}
}

if (!function_exists('server_to_local')) {

	/**
	 * 将服务器时间转换为本地显示时间
	 * 
	 * @param string $server_date_time 服务器的日期信息 yyyy-MM-dd H:i:s
	 * @param string $local_time_zone
	 * @return 
	 */
	function server_to_local($server_date_time, $local_time_zone, $dst = FALSE)
	{
		if ($server_date_time && $local_time_zone != null) {
			if ($local_time_zone) {
				if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/", $server_date_time, $matches)) {
					$time = mktime(intval($matches[4]), intval($matches[5]), intval($matches[6]), intval($matches[2]), intval($matches[3]), intval($matches[1]));
					$gmt_time = mktime(gmdate("H", $time), gmdate("i", $time), gmdate("s", $time), gmdate("m", $time), gmdate("d", $time), gmdate("Y", $time));
					$local_time = gmt_to_local($gmt_time, $local_time_zone, $dst);
					return date('Y-m-d H:i:s', $local_time);
				}
			} else {
				return $server_date_time;
			}
		}
		return FALSE;
	}
}

if (!function_exists('now_to_local_date')) {
	function now_to_local_date($local_time_zone, $dst)
	{
		$time = time();
		$gmt_time = mktime(gmdate("H", $time), gmdate("i", $time), gmdate("s", $time), gmdate("m", $time), gmdate("d", $time), gmdate("Y", $time));
		if ($local_time_zone) {
			$local_time = gmt_to_local($gmt_time, $local_time_zone, $dst);
		} else {
			$local_time = $gmt_time;
		}
		return date('Y-m-d', $local_time);
	}
}


if (!function_exists('gmt_to_server')) {
	/**
	 * 
	 * @param object $timestamp
	 * @return 
	 */
	function gmt_to_server($timestamp)
	{
		$CI = &get_instance();
		return gmt_to_local($timestamp, $CI->config->item('server.timezone'), $CI->config->item('server.dst'));
	}
}

if (!function_exists('server_to_gmttimestamp')) {

	/**
	 * 将服务器时间转换为GTM时间的时间戳
	 *
	 * @param object $server_date_time 服务器的日期信息 yyyy-MM-dd H:i:s
	 * @param object $local_time_zone
	 * @param object $dst
	 * @return
	 */
	function server_to_gmttimestamp($server_date_time)
	{
		if ($server_date_time) {
			$time = strtotime($server_date_time);
			$gmt_time = gmmktime(gmdate("H", $time), gmdate("i", $time), gmdate("s", $time), gmdate("m", $time), gmdate("d", $time), gmdate("Y", $time));
			return $gmt_time;
		}

		return FALSE;
	}
}

if (!function_exists('server_to_local_by_zonenum')) {

	/**
	 * 将服务器时间转换为本地显示时间
	 *
	 * @param object $server_date_time 服务器的日期信息 yyyy-MM-dd H:i:s
	 * @param object $local_time_zone
	 * @param object $dst
	 * @return
	 */
	function server_to_local_by_zonenum($server_date_time, $local_time_zone, $dst = FALSE)
	{
		if ($server_date_time) {
			if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/", $server_date_time, $matches)) {
				$time = mktime(intval($matches[4]), intval($matches[5]), intval($matches[6]), intval($matches[2]), intval($matches[3]), intval($matches[1]));
				$gmt_time = mktime(gmdate("H", $time), gmdate("i", $time), gmdate("s", $time), gmdate("m", $time), gmdate("d", $time), gmdate("Y", $time));
				$local_time = $gmt_time + $local_time_zone * 3600;
				if ($dst) $local_time += 3600;
				return date('Y-m-d H:i:s', $local_time);
			}
		}
		return FALSE;
	}
}
