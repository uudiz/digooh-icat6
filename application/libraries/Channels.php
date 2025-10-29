<?php
class Channels{
	public $last_modified;
	public $news_list;
	
	public function __construct(){
		$this->last_modified = '';
		$this->news_list = array();
	}
	
	public function addNews($news){
		array_push($this->$news, $news);
	}
}