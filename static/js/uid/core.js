var uid=new Object();
var config={
	path:{
		js:"/static/js/uid/"
	}
}
function loadModule(){
	
	var maps=$.map(config.modules,function(module){return config.path.js+module+".js"});
	
	$.chainclude(maps,function(){
				init();
				onResize();
			});
	// $.chainclude(maps,function(){
				// init();
				// onResize();
			// });
}

function init(){
	console.log("init");
	$.each(config.modules,function(i,module){
		eval("uid."+module+".init();");
		
	})

}
function onResize(){
	var pResizeTimer = null;
	$(window).bind('resize', function() {
	    if(pResizeTimer) clearTimeout(pResizeTimer);
	    pResizeTimer= setTimeout(function(){
				eval("uid."+config.modules[0]+".resize()");
			
		}, 10)
	 })
}

function resizeChildren(childrens){
		 $(childrens).each(function(i,children){
			var module=$(children).attr("class");
			var at=$.inArray(module,window.config.modules);
			if(at!=-1){
			
			eval("uid."+module+".resize(children);");}
		 })
}
