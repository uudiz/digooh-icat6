var curLang = 'en';
var requestFail='Request Fail, Please try again!';

function toJsonObj(src){
	
	try {
		if (src.length > 0) {
			return eval("(" + src + ")");
		}
	}catch(ex){}
	
	return null;
}

/**
 * 首字母大写
 * @param {Object} str
 */
function firstToUpperCase(str){
	if(str != null){
		len = str.length;
		if(len > 0){
			return str.substr(0,1).toUpperCase()+str.substr(1,len);
		}
	}else{
		return null;
	}
}
/**
 * 显示加载信息
 */
function showOverlayLoading(){
	window.top.window.showLoading();
	
}

/**
 * 隐藏加载信息
 */
function hideOverlayLoading(){
	window.top.window.hideLoading();

}

/**
 * 显示加载信息
 */
function showLoading(){

	var loading = $('#loadingLayer');
	if(loading != null){
		loading.show();
	}
	
}

/**
 * 隐藏加载信息
 */
function hideLoading(){
	var loading = $('#loadingLayer');
	if(loading != null){
		loading.hide();
	}
	
}

/**
 * 显示错误消息
 * @param {Object} msg
 */
function showMsg(msg, level){

	var statusMsg = new Object();
	statusMsg.type=level;
	statusMsg.message = msg;
	localStorage.setItem("Status",JSON.stringify(statusMsg));
}

/**
 * 隐藏错误消息
 */
function hideMsg(){
	var msgPanel = $('#msgContent');
	msgPanel.html('');
	msgPanel.parent().attr('class','');
	$('#msgLayer').hide();
}

/**
 * 显示表单错误信息
 * 
 * @param {Object} msg
 * @param {Object} level
 */


function showFormMsg(message, type) {
  var alertPlaceholder = document.getElementById('validateTips')
  var wrapper = document.createElement('div')
  wrapper.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'

  alertPlaceholder.append(wrapper)
}

function tb_init(str){

}
function tb_remove() {

}


