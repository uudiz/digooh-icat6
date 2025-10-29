<?
class Yuv extends CI_Controller{
	
	public function index(){
		if(jpeg2yuv("/home/miatek/np100/yuv_test.jpg","/home/miatek/np100/images/logos/yuv_test.jpg.yuv",1080,716)){
			echo "Yes";
		}else{
			echo "No";
		}
	}
}
?>