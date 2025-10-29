<?
class Phpinfo extends CI_Controller{
	
	public function index(){		echo ini_get('upload_max_filesize').'<br/>';
		phpinfo();
	}
}
?>