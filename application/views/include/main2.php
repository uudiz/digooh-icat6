<?php
/*
 * Created on 2011-12-11
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
$this->load->view("include/header2");
if(isset($body_file)){
	$this->load->view($body_file);
}
$this->load->view("include/footer2");