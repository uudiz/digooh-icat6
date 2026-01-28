<?php

if (!function_exists('is_week')) {
	/**
	 * 判断是否是指定的星期X，从星期天开始~星期六
	 * @param object $week
	 * @param object $idx [optional]
	 * @return 
	 */
	function is_week($week, $idx = FALSE)
	{
		if ($idx === FALSE || $idx < 0 || $idx > 6) {
			return FALSE;
		}

		$array = array(0x01, 0x02, 0x04, 0x08, 0x10, 0x20, 0x40, 0x80);
		if (is_numeric($week)) {
			return $week & $array[$idx];
		}

		return FALSE;
	}
}

if (!function_exists('isDayEnabled')) {
	function isDayEnabled($weekdayBitmap, $dayIndex)
	{
		// Special case: 127 means all days are enabled
		if ($weekdayBitmap == 127) {
			return true;
		}

		// Map PHP's date('w') to our bitmap positions
		// Sunday (0) should check bit 6
		// Monday (1) should check bit 0
		// Tuesday (2) should check bit 1, etc.
		$bitPosition = ($dayIndex == 0) ? 6 : $dayIndex - 1;

		// Check if the bit for the specified day is set
		return ($weekdayBitmap & (1 << $bitPosition)) != 0;
	}
}
if (!function_exists('parseWeeks')) {
	//weekdays is a byte value, each bit represent a day of week, 1 means on, 0 means off,return a string like "1,2,3,4,5,6,7"
	function parseWeeks($weekdays)
	{
		$weeks = array();
		for ($i = 0; $i < 7; $i++) {
			if ($weekdays & (1 << $i)) {
				$weeks[] = $i + 1;
			}
		}
		return implode(',', $weeks);
	}
}
