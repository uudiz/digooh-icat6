<?php 
if (!function_exists("get_weather")) {

    function get_weather($city_code, $format = 'f') {
    	if(empty($city_code)){
    		return FALSE;
    	}
		
        return get_yahoo_weather($city_code, $format);
    }
    
    /**
     * http://xml.weather.yahoo.com/forecastrss?p=94043&u=c
     * http://weather.yahooapis.com/forecastrss?p=94043&u=c
     * http://weather.yahooapis.com/forecastrss?w=2151330&u=c
     * u=c
     * u=f
     * @param object $city_code
     * @param object $format [optional]
     * @return
     */
    function get_yahoo_weather($city_code, $format = 'f') {
        $url = "http://weather.yahooapis.com/forecastrss?w=$city_code&u=$format";
        if ($f = @fopen($url, 'r')) {
            $rss = '';
            while (!feof($f)) {
                $rss .= fgets($f, 4096);
            }
            fclose($f);
			
			//$code_array = array("32"=>"sunny.png", "rain.png", "16"=>"snow.png", "26"=>"cloudy.png", "20"=>"fog.png","13"=>"flurries.png","21"=>"haze.png","mist.png", "18"=>"sleet.png", "22"=>"smoke.png", "storm.png","thunderstorm.png","chance_of_storm.png","chance_of_rain.png","chance_of_snow.png","chance_of_tstorm.png","mostly_sunny.png","27"=>"mostly_cloudy.png","28"=>"mostly_cloudy.png","29"=>"partly_cloudy.png","30"=>"partly_cloudy.png","sleet1.png","cn_cloudy.png","cn_fog.png","dust.png","icy.png");
			$code_array = array("0"=>"0.Tornado",
								"1"=>"1.Tropical_Storm",
								"2"=>"2.Hurricane",
								"3"=>"3.Severe_T-Storms",
								"4"=>"4.Thunderstorms",
								"5"=>"5.Rain_Snow",
								"6"=>"6.Rain_Sleet",
								"7"=>"7.Snow_Sleet",
								"8"=>"8.Freezing_Drizzle",
								"9"=>"9.Drizzle",
								"10"=>"10.Freezing_Rain",
								"11"=>"11.Showers",
								"12"=>"12.Showers",
								"13"=>"13.Flurries",
								"14"=>"14.Light_Snow_Showers",
								"15"=>"15.Blowing_Snow",
								"16"=>"16.Snow",
								"17"=>"17.Hail",
								"18"=>"18.Sleet",
								"19"=>"19.Dust",
								"20"=>"20.Foggy",
								"21"=>"21.Haze",
								"22"=>"22.Smoke",
								"23"=>"23.Blustery",
								"24"=>"24.Windy",
								"25"=>"25.Cold",
								"26"=>"26.Cloudy",
								"27"=>"27.Mostly_Cloudy",
								"28"=>"28.Mostly_Cloudy",
								"29"=>"29.Partly_Cloudy",
								"30"=>"30.Partly_Cloudy",
								"31"=>"31.Clear",
								"32"=>"32.Sunny",
								"33"=>"33.Fair",
								"34"=>"34.Fair",
								"35"=>"35.Rain_Hail",
								"36"=>"36.Hot",
								"37"=>"37.Thunderstorms",
								"38"=>"38.Scattered_T-Storms",
								"39"=>"39.Scattered_T-Storms",
								"40"=>"40.Scattered_Shower",
								"41"=>"41.Heavy_Snow",
								"42"=>"42.Scattered_Snow_Showers",
								"43"=>"43.Heavy_Snow",
								"44"=>"44.Partly_Cloudy",
								"45"=>"45.T-Showers",
								"46"=>"46.Snow_Showers",
								"47"=>"47.Isolated_T-Showers");
            $result = array();
			$unit = '°F';
	        if(strtolower($format) == 'c') {
	        	$unit = '℃';
	        }
            $dom = new DOMDocument();
            if(@$dom->loadXML($rss)){
            	$items = $dom->getElementsByTagName('location');
				if($items->length > 0){
					$result['city']=$items->item(0)->getAttribute('city');
					if(strpos($result['city'], ' ')){
						$result['city'] = str_replace(' ','_', $result['city']);
					}
				}else{
					return FALSE;
				}
				
				$items = $dom->getElementsByTagName('condition');
				if($items->length > 0){
					$item = $items->item(0);
					$result['icon']=@$code_array[$item->getAttribute('code')];
				}
				
				if(empty($result['icon'])){
					$result['icon']='partly_cloudy.png';
				}
					
				$items = $dom->getElementsByTagName('forecast');
				if($items->length > 0){
					$item = $items->item(0);
					$result['low']= $item->getAttribute('low').$unit;
					$result['high']= $item->getAttribute('high').$unit;
				}else{
					return FALSE;
				}
            }
			
			return $result;
        }
        
        return FALSE;
    }
    
    function getArray($node) {
        $array = false;
        
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $array[$attr->nodeName] = $attr->nodeValue;
            }
        }
        
        if ($node->hasChildNodes()) {
            if ($node->childNodes->length == 1) {
                $array[$node->firstChild->nodeName] = getArray($node->firstChild);
            } else {
                foreach ($node->childNodes as $childNode) {
                    if ($childNode->nodeType != XML_TEXT_NODE) {
                        $array[$childNode->nodeName][] = getArray($childNode);
                    }
                }
            }
        } else {
            return $node->nodeValue;
        }
        return $array;
    }
    
    /**
     * http://www.google.com.hk/ig/api?hl=en-us&weather=94043
     *
     * weather:
     * Where location can be either a zip code (weather=24060); city name, state (weather=woodland,PA); city name, country (weather=london,england); or possibly others. Try it out and see what response you get back to test your location.
     *
     * @param object $city_code
     * @param object $country [optional]
     * @return
     */
    function get_google_weather($city_code, $country = 'us') {
        $country_array = array('us'=>'en-us', 'cn'=>'zh-cn');
        $hl = 'en-us';
        
        if (array_key_exists($country, $country_array)) {
            $hl = $country_array[$country];
        }
        $url = "http://www.google.com.hk/ig/api?hl=$hl&weather=$city_code";
        $xml = @simplexml_load_file($url);
        //print_r($xml);
        if ($xml === FALSE) {
            return FALSE;
        }
        $weather = $xml->weather;
        if (!isset($weather->forecast_information)) {
            return FALSE;
        }
        $city = $weather->forecast_information->city->attributes();
        $unitSystem = (string) $weather->forecast_information->unit_system->attributes()->data;
        
        $unit = ' F';
        switch (strtolower($unitSystem)) {
            case 'us':
                $unit = ' F';
                break;
            case 'si':
                $unit = '℃';
                break;
        }
        
        $forecast = $weather->forecast_conditions;
        $low = $forecast->low->attributes();
        $high = $forecast->high->attributes();
        $condition = $forecast->condition->attributes();
        $icon = $forecast->icon->attributes();
        $icon = str_replace('/ig/images/weather/', '', $icon->data);
        
        $result = array('city'=>(string) $city->data, 'low'=>(string) $low->data.$unit, 'high'=>(string) $high->data.$unit, 'icon'=>$icon, 'condition'=>(string) $condition->data);
        return $result;
    }
    
	function FtoC($temp) {
		return (int)(($temp - 32) / (9 / 5));
	}
	
	function get_yahoo_weather_3days($city_code, $format = 'f') {
       //$url = "http://xml.weather.yahoo.com/forecastrss?w=$city_code&u=$format";
       
       $url = "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20weather.forecast%20where%20woeid%20=%20$city_code&format=xml";
        if ($f = @fopen($url, 'r')) {
            $rss = '';
            while (!feof($f)) {
                $rss .= fgets($f, 4096);
            }
            fclose($f);
		
			
			$code_array = array("32"=>"sunny.png", 
			  						/*"rain.png",*/ 
			                      "16"=>"snow.png", 
			                      "26"=>"cloudy.png", 
			                      "20"=>"fog.png",
			                      "13"=>"flurries.png",
			                      "21"=>"haze.png",
			                      "20"=>"mist.png", 
			                      "18"=>"sleet.png", 
			                      "22"=>"smoke.png", 
			                      /*"storm.png",*/
			                      "4"=>"thunderstorm.png", //雷电交加的暴风雨; 大雷雨
			                      /*"chance_of_storm.png",
			                      "chance_of_rain.png",
			                      "chance_of_snow.png",
			                      "chance_of_tstorm.png",*/
			                      "33"=>"mostly_sunny.png",
			                      "34"=>"mostly_sunny.png",
			                      "27"=>"mostly_cloudy.png",
			                      "28"=>"mostly_cloudy.png",
			                      "29"=>"partly_cloudy.png",
			                      "30"=>"partly_cloudy.png",
			                      /*"sleet1.png",
			                      "cn_cloudy.png",
			                      "cn_fog.png",*/
			                      "19"=>"dust.png",
			                      "25"=>"icy.png");

			/*
			$code_array = array("0"=>"0.Tornado", //龙卷风，陆龙卷; 大雷雨
								"1"=>"1.Tropical_Storm",  //热带风暴
								"2"=>"2.Hurricane",  //飓风
								"3"=>"3.Severe_T-Storms", //猛烈的热带风暴
								"4"=>"4.Thunderstorms", //雷暴
								"5"=>"5.Rain_Snow",    //雨雪
								"6"=>"6.Rain_Sleet",   //雨夹雪
								"7"=>"7.Snow_Sleet",   //雪雨夹雪
								"8"=>"8.Freezing_Drizzle", //冻毛毛雨
								"9"=>"9.Drizzle",   //细雨
								"10"=>"10.Freezing_Rain", //冻雨
								"11"=>"11.Showers",    //降雨
								"12"=>"12.Showers",    //降雨
								"13"=>"13.Flurries",   //小雪
								"14"=>"14.Light_Snow_Showers",//小阵雪
								"15"=>"15.Blowing_Snow",//吹雪
								"16"=>"16.Snow", //雪
								"17"=>"17.Hail", //冰雹
								"18"=>"18.Sleet", //雨夹雪
								"19"=>"19.Dust",  //灰尘
								"20"=>"20.Foggy", //有雾的
								"21"=>"21.Haze",  //阴霾
								"22"=>"22.Smoke", //烟
								"23"=>"23.Blustery", //大风
								"24"=>"24.Windy",  //有风的
								"25"=>"25.Cold",  //冷
								"26"=>"26.Cloudy", //阴天
								"27"=>"27.Mostly_Cloudy",//多云
								"28"=>"28.Mostly_Cloudy",//多云
								"29"=>"29.Partly_Cloudy",//多云
								"30"=>"30.Partly_Cloudy",//多云
								"31"=>"31.Clear", //晴朗
								"32"=>"32.Sunny", //阳光明媚
								"33"=>"33.Fair", //晴朗
								"34"=>"34.Fair",
								"35"=>"35.Rain_Hail", //雨雹
								"36"=>"36.Hot",
								"37"=>"37.Thunderstorms", //雷暴
								"38"=>"38.Scattered_T-Storms", //零星雷雨
								"39"=>"39.Scattered_T-Storms",
								"40"=>"40.Scattered_Shower", //零星阵雨
								"41"=>"41.Heavy_Snow", //大雪
								"42"=>"42.Scattered_Snow_Showers", //零星阵雨雪
								"43"=>"43.Heavy_Snow", //大雪
								"44"=>"44.Partly_Cloudy", //多云
								"45"=>"45.T-Showers",  //雷阵雨
								"46"=>"46.Snow_Showers",  //阵雪
								"47"=>"47.Isolated_T-Showers"); //局部阵雪
			*/
            $result = array();
            $city = array();
            $city_data = array();
			$unit = '°F';
	        if(strtolower($format) == 'c') {
	        	$unit = '℃';
	        }
            $dom = new DOMDocument();
            if(@$dom->loadXML($rss)){
            	$items = $dom->getElementsByTagName('location');
				if($items->length > 0){
					$city['city']=$items->item(0)->getAttribute('city');
					if(strpos($city['city'], ' ')){
						$city['city'] = str_replace(' ','_', $city['city']);
					}
				}else{
					return FALSE;
				}
				$items = $dom->getElementsByTagName('pubDate');
				
				if($items->length > 0){
					
					$city['pubDate']=$items->item(0)->nodeValue;
				}
				
				$items = $dom->getElementsByTagName('condition');
				$gmt = time();
				for($i = 0; $i < 3; $i++) {
					if($items->length > 0){
						$item = $items->item($i);
						$city_data[$i]['icon']=@$code_array[$item->getAttribute('code')];
						$city_data[$i]['iconNum']=$item->getAttribute('code');
					}
					
					if(empty($city_data[$i]['icon'])){
						$city_data[$i]['icon']='partly_cloudy.png';
						$city_data[$i]['iconNum']=30;
					}
						
					$items = $dom->getElementsByTagName('forecast');
					if($items->length > 0){
						$item = $items->item($i);
						if(strtolower($format) == 'c'){
							$city_data[$i]['low']= FtoC($item->getAttribute('low')).$unit;
							 $city_data[$i]['high']=FtoC($item->getAttribute('high')).$unit;
						}
						else	{
							$city_data[$i]['low']= $item->getAttribute('low').$unit;
							$city_data[$i]['high']= $item->getAttribute('high').$unit;
						}
						$city_data[$i]['date']= date('Y-m-d', $gmt+$i*3600*24);
					}else{
						return FALSE;
					}
				}
            }
			$result = array('city'=>$city, 'data'=>$city_data);
			
			return $result;
        }
        
        return FALSE;
    }
    function get_weather_lang($num, $id) {
    	$title = '';
    	switch($num) {
    		case 1: //西班牙语
    			$lang = array(
							"0"=>"Tornado",
							"1"=>"Tormenta Tropical",
							"2"=>"Huracán",
							"3"=>"Tormentas Severas",
							"4"=>"Tormentas Eléctricas",
							"5"=>"Lluvia Y Nieve",
							"6"=>"Lluvia Y Aguanieve",
							"7"=>"Nieve Y Aguanieve",
							"8"=>"Llovizna Helada",
							"9"=>"Llovizna",
							"10"=>"Lluvia Helada",
							"11"=>"Aguaceros",
							"12"=>"Aguaceros",
							"13"=>"Nevisca",
							"14"=>"Nevadas Ligeras",
							"15"=>"Nieve Y Viento",
							"16"=>"Nieve",
							"17"=>"Granizo",
							"18"=>"Aguanieve",
							"19"=>"Polvo",
							"20"=>"Neblina",
							"21"=>"Bruma",
							"22"=>"Humo",
							"23"=>"Borrascoso",
							"24"=>"Viento",
							"25"=>"Frio",
							"26"=>"Nublado",
							"27"=>"Nublado (noche)",
							"28"=>"Nublado (día)",
							"29"=>"Parcialmente Nublado (noche)",
							"30"=>"Parcialmente Nublado (día)",
							"31"=>"Despejado (noche)",
							"32"=>"Soleado",
							"33"=>"Templado (noche)",
							"34"=>"Templado (día)",
							"35"=>"Lluvia Y Granizo",
							"36"=>"Calor",
							"37"=>"Tormentas Eléctricas Aisladas",
							"38"=>"Tormentas Eléctricas Dispersas",
							"39"=>"Tormentas Eléctricas Dispersas",
							"40"=>"Lluvias Dispersas",
							"41"=>"Nevadas Fuertes",
							"42"=>"Nevadas Dispersas",
							"43"=>"Nevadas Fuertes",
							"44"=>"Parcialmente Nublado",
							"45"=>"Tormentas Eléctricas",
							"46"=>"Nevadas",
							"47"=>"Tormentas Eléctricas Aisladas",
							"3200"=>"no disponible"
						);
				$title = $lang[$id];
    			break;
    			case 2: //其他
    			break;
    	} 
    	return $title;
    }
}
