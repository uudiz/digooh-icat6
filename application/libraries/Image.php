<?php

class Image{
	var $CI;
	var $im = FALSE;
	var $width;
	var $height;
	var $title_color = '#000000';//标题颜色
	var $title_area_color = '#F9F9F9';//标题区域颜色
	var $title_height = 15;
	var $title_font_file = '';
	
	/**
	 * Constructor
	 *
	 * Simply determines whether the mcrypt library exists.
	 *
	 */
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->set_title_font('Times New Roman Bold');
		log_message('debug', "MY_Image Class Initialized");
	}
	
	/**
	 * 创建一个空白黑色图片
	 * 
	 * @param object $width
	 * @param object $height
	 * @param object $bg_image_file [optional]背景图路径
	 * @return 
	 */
	public function create($width, $height, $bg_image_file = FALSE){
		if($bg_image_file){
			$bg_image_file = $this->get_realpath($bg_image_file);
			$bg = @imagecreatefromjpeg($bg_image_file);
			if($bg){
				$x = imagesx($bg);
				$y = imagesy($bg);
				if($x == $width && $y = $height){
					$this->im = $bg;
				}else{
					//resize
					$this->im = @imagecreatetruecolor($width, $height);
					// Resize
					@imagecopyresized($this->im, $bg, 0, 0, 0, 0, $width, $height, $x, $y);
					@imagedestroy($bg);
				}
			}
		}
		if($this->im == FALSE){
			$this->im = @imagecreatetruecolor($width, $height);
		}
		
		$this->width = $width;
		$this->height = $height;
		
		if($this->im == FALSE){
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 * 设置当前照片背景颜色
	 * @param object $color
	 * @return 
	 */
	public function set_background_color($color){
		if($this->im){
			return @imagefilledrectangle($this->im, 0, 0, $this->width, $this->height,$this->get_color($color));
		}
		
		return FALSE;
	}
	
	
	/**
	 * 设置背景颜色
	 * 
	 * @param $color #FFFFFF
	 * @return 
	 */
	public function get_color($color){
		$red   = 0xFF;
		$green = 0xFF;
		$blue  = 0xFF;
		if(substr($color, 0, 1) == '#'){
			$red   = intval('0x'.substr($color, 1, 2), 16);
			$green = intval('0x'.substr($color, 3, 2), 16);
			$blue  = intval('0x'.substr($color, 5, 2), 16);
		}
		
		if($this->im){
			$c = imagecolorallocate($this->im, $red, $green, $blue);
			return $c;
		}
		
		return FALSE;
	}
	/**
	 * 添加一个照片区域
	 * 
	 * @param int    $x
	 * @param int    $y
	 * @param int    $width
	 * @param int    $height
	 * @param object $title
	 * @param object $bgcolor
	 * @param object $border_color
	 * @param object $bg_image
	 * @return 
	 */
	public function add_area($x, $y, $width, $height, $title = '', $bgcolor = '#000000', $border_color = '#CCCCCC', $bg_image=FALSE){
		
		if($this->im){
			$result =  @imagefilledrectangle($this->im, $x, $y, $x + $width, $y+$height,$this->get_color($bgcolor));
			if($bg_image){
				$bg_image_file = $this->get_realpath($bg_image);
				$bg = @imagecreatefromjpeg($bg_image_file);
				if($bg){
					$size_x = imagesx($bg);
					$size_y = imagesy($bg);
					if($size_x != $width && $size_y != $height){
						//resize
		                $thumb = @imagecreatetruecolor($width, $height);
		                //resize
		                @imagecopyresized($thumb, $bg, 0, 0, 0, 0, $width, $height, $size_x, $size_y);
						imagecopy($this->im, $thumb, $x, $y, 0, 0, $width, $height);
		                @imagedestroy($thumb);
	                	
					}else{
						imagecopy($this->im, $bg, $x, $y, 0, 0, $width, $height);
					}
					
					
				}
				@imagedestroy($bg);
			}
			if(!empty($title)){
				//set title #F9F9F9
				@imagefilledrectangle($this->im, $x, $y, $x+$width, $y + $this->title_height, $this->get_color($this->title_area_color));
				//@imagestring($this->im, 2, $x + 10, $y + 10, $title, $this->get_color($this->title_color));
				@imagettftext($this->im, 10, 0, $x + 4, $y + 12, $this->get_color($this->title_color), $this->title_font_file,  $title);
				if($bgcolor != $border_color){
					//set border color
					$color = $this->get_color($border_color);
					@imageline($this->im, $x, $y, $x, $y + $height, $color);
					@imageline($this->im, $x, $y, $x + $width, $y, $color);
					
					@imageline($this->im, $x, $y+$height, $x + $width, $y + $height, $color);
					@imageline($this->im, $x + $width, $y, $x + $width, $y + $height, $color);
				}
			}
			
			
			return $result;
		}
		
		return FALSE;
	}
	
	/**
	 * 保存一个文件，输出到指定的文件内
	 * 
	 * @param object $file_path 当前文件的绝对路径
	 * @param object $filename
	 * @return 
	 */
	public function save($file_path, $filename){
		if($this->im){
			
			$file_path = $this->get_realpath($file_path);
			
			if(!file_exists($file_path)){
				mkdir($file_path, 0777, TRUE);
			}
			if(file_exists($file_path)){
				$result = imagejpeg($this->im, $file_path.'/'.$filename, 100);
				imagedestroy($this->im);
				return $result;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * 设置字体文件信息
	 * 
	 * @param object $font [simfang]
	 * @return 
	 */
	public function set_title_font($font){
		$font_file = $this->get_realpath('./fonts/'.$font.'.ttf');
		if(file_exists($font_file)){
			$this->title_font_file = $font_file;
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * 获取真实路径
	 * 
	 * @param object $path
	 * @return 
	 */
	private function get_realpath($path){
		if (function_exists('realpath') AND @realpath($path) !== FALSE)
		{
			$path = str_replace("\\", "/", realpath($path));
		}
		
		return $path;
	}
}
