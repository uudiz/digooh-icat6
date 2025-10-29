var template = {

    /*默认页面*/
    index: {
        swfu: null,
        sessionId: null,
        init: function(){
            $(".tab-01 a").click(function(event){
                event.preventDefault();
                var cur = $(this).addClass("on");
                cur.siblings("a").removeClass("on");
                var type = cur.attr('type');
                var curType = $('#templateContent').attr('type');
                if (type != curType) {
                    template.index.refresh(type);
                }
                /*num = $(".tab-01 a").index($(this));
                $(".tab-01-in").eq(num).show().siblings(".tab-01-in").hide();*/
            });
            var type = $('.tab-01 a.on').attr('type');
            $('#templateContent').attr('type', type);
            $.get('/template/lists?type=' + type + '&t=' + new Date().getTime(), function(data){
                $('#templateContent').html(data);
                /*reinit this box~*/
                tb_init('a.thickbox');/*pass where to apply thickbox*/
            });
            
        },
        refresh: function(type){
        
            showLoading();
            type = type | $('.tab-01 a.on').attr('type');
            $('#templateContent').attr('type', type);
            $.get('/template/lists?type=' + type + '&t=' + new Date().getTime(), function(data){
                $('#templateContent').html(data);
                /*reinit this box~*/
                tb_init('a.thickbox');/*pass where to apply thickbox*/
                hideLoading();
            });
        },
        page: function(type, curpage){
            showLoading();
            type = type | $('.tab-01 a.on').attr('type');
			//curpage = curpage | 1;
			curpage = curpage;
            $('#templateContent').attr('type', type);
            $.get('/template/lists/' + curpage + '?type=' + type + '&t=' + new Date().getTime(), function(data){
                $('#templateContent').html(data);
                /*reinit this box~*/
                tb_init('a.thickbox');/*pass where to apply thickbox*/
                hideLoading();
            });
        },
        add: function(type){
            $.get('/template/add?type=' + type + "&t=" + new Date().getTime(), function(data){
                $('#tabs').after(data);
                $('.dialog').dialog({
                    autoOpen: true,
                    modal: true,
                    width: 500,
                    buttons: [{
                        text: 'Save',
                        click: function(event){
                            template.index.saveCreate();
                        }
                    }, {
                        text: 'Cancel',
                        click: function(event){
                            template.index.closeDialog();
                        }
                    }],
                    close: function(event, ui){
                        template.index.closeDialog();
                    }
                });
            });
        },
        closeDialog: function(){
            var d = $('.dialog');
            d.dialog('destory');
            d.parent().remove();
            d.remove();
        },
        doSave: function(){
            var id = $('#id').val();
            if (id == undefined || id == '') {
                id = 0;
            }
            var name = $('#name').val();
			if (name.indexOf("&") >= 0 || name.indexOf("<") >= 0 || name.indexOf(">") >= 0 || name.indexOf("'") >= 0 || name.indexOf("\\") >= 0 || name.indexOf("%") >= 0) {
			showFormMsg("Special symbols (& < > ' \\ %) are not allowed in the template name.", 'error');
            return false;
			}
            var descr = $('#descr').val();
            var postData = {
                name: name,
                descr: descr,
                id: id,
                template_type: $('#template_type').val()
            };
            if (id == 0) {
                var screen = $('#screen').val();
                var wh = screen.split('X');
                var type = $('#type').val();
                postData.type = type;
                postData.width = wh[0];
                postData.height = wh[1];
            }
            
            $.post('/template/do_save', postData, function(data){
                if (data.code != 0) {
                    //$('.validateTips').html(json.msg);
                    showFormMsg(data.msg, 'error')
                }
                else {
                    /*template.index.closeDialog();
                     var tab = $('#tabs');
                     var index = tab.tabs( "option", "selected" );
                     tab.tabs( "load" , index );*/
                    showFormMsg(data.msg, 'success');
                    if (id > 0) {
                        setTimeout(function(){
                            //关闭当前窗口
                            tb_remove();
                            template.index.refresh(type);
                        }, 200);
                    }
                    else {
						tb_remove();
                        setTimeout(function(){
                            window.location.href = "/template/edit_screen?id=" + data.id;
                        }, 200);
                    }
                }
                
            }, 'json')
        },
        remove: function(id, msg, force, type, curpage){
            if (force == undefined || !force) {
                if (confirm(msg)) {
                    var req = {
                        id: id
                    }
                    $.post('/template/do_delete', req, function(data){
                        if (data.code == 0) {
                            showMsg(data.msg, 'success');
                            template.index.page(type, curpage);
                            setTimeout(hideMsg, 1000);
                            
                        }
                        else 
                            if (data.code == 2) {
                                if (confirm(data.msg)) {
                                    template.index.remove(id, msg, true);
                                }
                            }
                            else {
                                showMsg(data.msg, 'error');
                            }
                    }, 'json');
                }
            }
            else 
                if (force) {
                    var req = {
                        id: id,
                        force: true
                    }
                    $.post('/template/do_delete', req, function(data){
                        if (data.code == 0) {
                            showMsg(data.msg, 'success');
                            template.index.page(type, curpage);
                            setTimeout(hideMsg, 1000);
                        }
                        else {
                            showMsg(data.msg, 'error');
                        }
                    }, 'json');
                }
        },
        /**
		 * 1.丢弃小数部分,保留整数部分       parseInt(5/2)
		 * 2.向上取整,有小数就整数部分加1   Math.ceil(5/2)
		 * 3,四舍五入. Math.round(5/2)
		 * 4,向下取整  Math.floor(5/2) 
		 */
        changeX: function(t_width, t_height) {
        	var id = $('#areaChange').val();
        	var areaX = parseInt($('#areaX').val());
        	var obj = $('#'+id);
			var x = obj.position().left;
            var w = obj.innerWidth();
            if(isNaN(areaX)) {
            	$('#areaX').attr('value', 2*x);
            }else {
            	if(areaX % 2 != 0) {
	            	areaX--;
	            }
	            var left = areaX/2;
	            if(left + w > t_width) {
	            	left = t_width - w;
	            }
	            $('#'+id).css('left', left);
				$('#areaX').attr('value', 2*left);
            }
        },
        changeY: function(t_width, t_height) {
        	var id = $('#areaChange').val();
        	var areaY = parseInt($('#areaY').val());
        	var obj = $('#'+id);
            var y = obj.position().top;
            var h = obj.innerHeight();
            if(isNaN(areaY)) {
            	$('#areaY').attr('value', 2*y);
            }else {
            	if(areaY % 2 != 0) {
	            	areaY--;
	            }
	            var top = areaY/2;
	            if(top + h > t_height) {
	            	top = t_height - h;
	            }
	            $('#'+id).css('top', top);
				$('#areaY').attr('value', 2*top);
            }
        },
        changeW: function(t_width, t_height, type) {
        	var id = $('#areaChange').val();
        	var areaWidth = $('#areaWidth').val();
        	var areaWidthP = $('#areaWidthPercent').val();
        	var obj = $('#'+id);
        	var x = obj.position().left;
            var w = obj.innerWidth();
            if(type == 2) {
            	areaWidth = 2 * t_width * areaWidthP * 0.01;
            }
            if(isNaN(areaWidth)) {
            	$('#areaWidth').attr('value', 2 * w);
            }else {
            	var wmod = areaWidth % 4;
            	if(wmod != 0) {
            		areaWidth -= wmod;
            	}
            	var width = areaWidth / 2;
            	if(width + x > t_width) {
	            	width = t_width - x;
	            }
	            //alert(areaWidth+', '+template.screen.minVideoRealWidth);
	            if(id == 'area_movie' && (areaWidth < template.screen.minVideoRealWidth)) {
	            	width = template.screen.minVideoRealWidth / 2;
	            }
	            if(id.indexOf('area_image') != -1 && (areaWidth < template.screen.minImageRealWidth)) {
	            	width = template.screen.minImageRealWidth / 2;
	            }
	            //np100才会有此判断
	            if(id == 'area_date' || id == 'area_weather' || id == 'area_time' || id == 'area_logo') {
	            	width = w;
	            }
	            /*
				if(template.screen.template_type != 1) {
					if(id == 'area_logo') {
		            	width = w;
		            }
		            // 256/512
		            if(id == 'area_date' || id == 'area_weather') {
		            	if(width >= 256) {
		            		width = 256;
		            	}else {
		            		width = 128;
		            	}
		            }
				}*/
	            
	            $('#'+id).css('width', width);
				$('#areaWidth').attr('value', 2 * width);
				$('#areaWidthPercent').attr('value', Math.round(width / t_width * 10000) / 100.00);
				//$('#areaWidthPercent').attr('value', width / t_width * 10000/100.00);
            } 
        },
        changeH: function(t_width, t_height, type) {
        	var id = $('#areaChange').val();
        	var areaHeight = $('#areaHeight').val();
        	var areaHeightP = $('#areaHeightPercent').val();
        	var obj = $('#'+id);
        	var y = obj.position().top;
            var h = obj.innerHeight();
            
            if(type == 2) {
            	areaHeight = 2 * t_height * areaHeightP * 0.01;
            }
            if(isNaN(areaHeight)) {
            	$('#areaHeight').attr('value', 2 * h);
            }else {
            	var hmod = areaHeight % 2;
            	if(hmod != 0) {
            		areaHeight -= hmod;
            	}
            	var height = areaHeight / 2;
            	if(height + y > t_height) {
	            	height = t_height - y;
	            }
	            if(id == 'area_movie' && (areaHeight < template.screen.minVideoRealHeight)) {
	            	height = template.screen.minVideoRealHeight / 2;
	            }
	            if(id.indexOf('area_image') != -1 && (areaHeight < template.screen.minImageRealHeight)) {
	            	height = template.screen.minImageRealHeight / 2;
	            }
	            if(id == 'area_date' || id == 'area_weather' || id == 'area_time' || id == 'area_logo') {
	            	height = h;
	            }
	            //np100才会有这个判断
	            /*
	            if(template.screen.template_type != 1) {
					if(id == 'area_date' || id == 'area_weather') {
		            	if(height >= 256) {
		            		height = 256;
		            	}else {
		            		if(height < 256 && height >=128) {
		            			height = 128;
		            		}else {
		            			height = 64;
		            		}
		            	}
		            }
		            if(id == 'area_logo') {
		            	height = h;
		            }	            
	            }*/
	            $('#'+id).css('height', height);
	            $('#'+id+' dd').css('height', height - 20);
				$('#areaHeight').attr('value', 2 * height);
				$('#areaHeightPercent').attr('value', Math.round(height / t_height * 10000) / 100.00);
            } 
        },
        changeWP: function() {},
        changeHP: function() {}
    },
    //屏幕页面
    screen: {
		curObj: null,
        id: 0,//当前模板的ID，唯一表示数据
        template_type: 0, //template类型，默认0表示np100, 1表示np200
        tabIndex: 1,
        bg: null,
        movie: null,
        image: new Array(),
        text: null,
        date: null,
        time: null,
        weather: null,
        logo: null,
        mask: null,
        zIndex: 10,
        width: 800,
        height: 600,
        realWidth: 1280,//实际宽度
        realHeight: 720,//实际高度
        //minVideoRealWidth: 640,//视频最小宽度
		minVideoRealWidth: 480,//视频最小宽度
        minVideoRealHeight: 480,//视频最小高度
        defaultTextHeight:0,
		defaultTextRealHeight:60,
        minTextHeight : 0,
        minTextRealHeight: 44,
		minTextWidth : 0,
		minTextRealWidth : 60,
        minVideoWidth: 0,
        minVideoHeight: 0,
        minImageRealWidth: 100, //图片最小宽度
        minImageRealHeight: 100,//图片最小高度
        minImageWidth: 0,
        minImageHeight: 0,
        minDateWidth: 0,
        minDateHeight: 0,
        maxDateWidth: 0,
        maxDateHeight: 0,
        minDateRealWidth: 256, //日期最小宽度
        minDateRealHeight: 128,//日期最小高度
        maxDateRealWidth: 512, //日期最大宽度
        maxDateRealHeight: 512,//日期最大高度
        minWeatherWidth: 0,
        minWeatherHeight: 0,
        maxWeatherWidth: 0,
        maxWeatherHeight: 0,
        minWeatherRealWidth: 256, //天气最小宽度
        minWeatherRealHeight: 128,//天气最小高度
        maxWeatherRealWidth: 512, //天气最大宽度
        maxWeatherRealHeight: 512,//天气最大高度
        logoSize : [],
        logoRealSize : [64,128,256],
        DateWeatherSize : [], //天气和日期的Size数组
		DateWeatherRealSize : [128,256,512],//天气和日期的Size数组
        gridX: 1,
        gridY: 1,
        warnSpace: '',
        warnOverlap: '',//已经存在被覆盖区域
        warnLogo: '',
		warnVideo:'', /*不存在Video区提示*/
        enableDeleteButton: true, //是否允许删除按钮
        deletes: new Array(),//已经删除的区域ID记录
        RIGHT: 1, //Resize方向向右
        DOWN: 2,//Resize方向向下
        LEFT: 3,// Resize方向向左
        UP: 4, //Resize方向向上
        RIGHT_DOWN: 5, //Resize方向右下方
        DRAGE_LEFT: 6, //左拖动
        DRAGE_UP: 7,//上拖动
        DRAGE_RIGHT: 8,//右拖动
        DRAGE_DOWN: 9, //下拖动
        DRAGE_LEFT_UP: 10,//左上方拖动
        DRAGE_RIGHT_UP: 11,
        DRAGE_RIGHT_DOWN: 12,
        DRAGE_LEFT_DOWN: 13,
        readonly: false,//是否只读
        debug: false && $.browser.mozilla,
        goTemplate: function(type){
            tb_remove();
            window.location.href = '/template/index?type=' + type + '&t=' + new Date().getTime();
        },
        init: function(){
			//init logo size
			for(var i =0; i < template.screen.logoRealSize.length; i++) {
				template.screen.logoSize[i] = template.screen.width * template.screen.logoRealSize[i] / template.screen.realWidth;
			}
//init date、weather size
for(var i =0; i < template.screen.DateWeatherRealSize.length; i++) {
	template.screen.DateWeatherSize[i] = template.screen.width * template.screen.DateWeatherRealSize[i] / template.screen.realWidth;
}
			
        	//init text min
			template.screen.defaultTextHeight=template.screen.height * template.screen.defaultTextRealHeight / template.screen.realHeight;
			template.screen.minTextWidth=template.screen.width * template.screen.minTextRealWidth / template.screen.realWidth;
			template.screen.minTextHeight=template.screen.height * template.screen.minTextRealHeight / template.screen.realHeight;
			
            // init image min width & height
            template.screen.minImageWidth = template.screen.width > template.screen.height ? (template.screen.width * template.screen.minImageRealWidth / template.screen.realWidth) : (template.screen.height * template.screen.minImageRealHeight / template.screen.realHeight);
            template.screen.minImageHeight = template.screen.width > template.screen.height ? (template.screen.height * template.screen.minImageRealHeight / template.screen.realHeight) : (template.screen.width * template.screen.minImageRealWidth / template.screen.realWidth);
            
			
            // init video min width & height
            template.screen.minVideoWidth = template.screen.width > template.screen.height ? (template.screen.width * template.screen.minVideoRealWidth / template.screen.realWidth) : (template.screen.height * template.screen.minVideoRealHeight / template.screen.realHeight);
            template.screen.minVideoHeight = template.screen.width > template.screen.height ? (template.screen.height * template.screen.minVideoRealHeight / template.screen.realHeight) : (template.screen.width * template.screen.minVideoRealWidth / template.screen.realWidth);
            
            if(!template.screen.template_type) {
            	template.screen.gridX = template.screen.width > template.screen.height ? (4 * template.screen.width) / template.screen.realWidth : (2 * template.screen.height) / template.screen.realHeight;
            	template.screen.gridY = template.screen.width > template.screen.height ? (2 * template.screen.height) / template.screen.realHeight : (4 * template.screen.width) / template.screen.realWidth;
            }else {
            	template.screen.gridX = 1;
            	template.screen.gridY = 1;
            }
            //template.screen.gridX = template.screen.width > template.screen.height ? (4 * template.screen.width) / template.screen.realWidth : (2 * template.screen.height) / template.screen.realHeight;
            //template.screen.gridY = template.screen.width > template.screen.height ? (2 * template.screen.height) / template.screen.realHeight : (4 * template.screen.width) / template.screen.realWidth;
            
            // init weather width& height
            if(template.screen.template_type) {
            	//template.screen.minWeatherRealWidth = 500;
            	//template.screen.minWeatherRealHeight = 180;
            	template.screen.maxWeatherRealWidth = 1920;
            	template.screen.maxWeatherRealHeight = 1080;
            	template.screen.minDateRealWidth = 200;
            	template.screen.minDateRealHeight = 100;
            	template.screen.maxDateRealWidth = 1920;
            	template.screen.maxDateRealHeight = 1080;
            }
            
            // init date width& height
            template.screen.minDateWidth = true ? (template.screen.width * template.screen.minDateRealWidth / template.screen.realWidth) : (template.screen.height * template.screen.minDateRealHeight / template.screen.realHeight);
            template.screen.minDateHeight = true ? (template.screen.height * template.screen.minDateRealHeight / template.screen.realHeight) : (template.screen.width * template.screen.minDateRealWidth / template.screen.realWidth);
            template.screen.maxDateWidth = true ? (template.screen.width * template.screen.maxDateRealWidth / template.screen.realWidth) : (template.screen.height * template.screen.maxDateRealHeight / template.screen.realHeight);
            template.screen.maxDateHeight = true ? (template.screen.height * template.screen.maxDateRealHeight / template.screen.realHeight) : (template.screen.width * template.screen.maxDateRealWidth / template.screen.realWidth);
            
            template.screen.minWeatherWidth = true ? (template.screen.width * template.screen.minWeatherRealWidth / template.screen.realWidth) : (template.screen.height * template.screen.minWeatherRealHeight / template.screen.realHeight);
            template.screen.minWeatherHeight = true ? (template.screen.height * template.screen.minWeatherRealHeight / template.screen.realHeight) : (template.screen.width * template.screen.minWeatherRealWidth / template.screen.realWidth);
            template.screen.maxWeatherWidth = true ? (template.screen.width * template.screen.maxWeatherRealWidth / template.screen.realWidth) : (template.screen.height * template.screen.maxWeatherRealHeight / template.screen.realHeight);
            template.screen.maxWeatherHeight = true ? (template.screen.height * template.screen.maxWeatherRealHeight / template.screen.realHeight) : (template.screen.width * template.screen.maxWeatherRealWidth / template.screen.realWidth);
            
            if (template.screen.debug) {
                console.info("init minVideoWidth" + template.screen.minVideoWidth + ", minVideoHeight:" + template.screen.minVideoHeight);
                console.info("init minImageWidth" + template.screen.minImageWidth + ", minImageHeight:" + template.screen.minImageHeight);
                console.info("init gridX" + template.screen.gridX + ", gridY:" + template.screen.gridY);
            }
            //初始化屏幕的高度和宽度
            //$('.screen').css('width',template.screen.width).css('height', template.screen.height);
			if(template.screen.readonly){
				return;
			}
            $('#bg').click(function(event){
                template.screen.addBg();
            });
            //this.enable('bg');
            
            $('#movie').click(function(event){
                template.screen.addMovie();
            });
            //$('#movie').button('enable');
            //this.enable('movie');
            
            $('#image1').click(function(event){
                template.screen.addImage(1);
            });
			$('#image2').click(function(event){
                template.screen.addImage(2);
            });
			$('#image3').click(function(event){
                template.screen.addImage(3);
            });
            $('#image4').click(function(event){
                template.screen.addImage(4);
            });
            //$('#image').button('enable');
            //this.enable('image');
            
            $('#text').click(function(event){
                template.screen.addText();
            });
            $('#staticText').click(function(event){
                template.screen.addStaticText();
            });
            //$('#text').button('enable');
            //this.enable('text');
            
            $('#date').click(function(event){
                template.screen.addDate();
            });
            //$('#date').button('enable');
            //this.enable('date');
            
            $('#time').click(function(event){
                template.screen.addTime();
            });
            //$('#time').button('enable');
            //this.enable('time');
            
            $('#logo').click(function(event){
                template.screen.addLogo();
                
            });
            $('#weather').click(function(event){
                template.screen.addWeather();
            });
            $('#webpage').click(function(event){
                template.screen.addWebpage();
            });
            $('#mask').click(function(event){
                template.screen.addMask();
            });
            //$('#logo').button('enable');
            //this.enable('logo');
            
            $('#save').click(function(event){
                template.screen.save();
            });
            
            /*$('.operate img').click(function(event){
             var curObj = $(this);
             var id = curObj.attr('id');
             if(id == undefined || id == null){
             return;
             }
             
             var nid = 'area' + id.substr(2);
             $('#' + nid).css('z-index', ++template.screen.zIndex);
             });*/
        },
        //初始化背景
        initBg: function(areaId, mediaId, img){
            if (img.length > 0) {
                template.screen.showBg(img);
            }
            template.screen.bg = {
                mediaId: mediaId,
                areaId: areaId
            };
        },
        //初始化视频
        initMovie: function(areaId, name, x, y, w, h, zIndex){
            var id = 'area_movie';
            template.screen.showMovie(id, name, x, y, w, h, zIndex);
            template.screen.movie = {
                id: id,
                areaId: areaId,
                name: name
            };
        },
        //初始化照片
        initImage: function(areaId, name, x, y, w, h, zIndex, index){
            //var index = (template.screen.image.length == 0) ? 1 : (template.screen.image[template.screen.image.length - 1].index + 1)
            var id = 'area_image_' + index;
            template.screen.showImage(id, name, x, y, w, h, zIndex);
            template.screen.image.push({
                id: id,
                areaId: areaId,
                index: index,
                name: name
            });
            /*if (template.screen.image.length > 2) {
                //$('#image').button('disable');
                template.screen.disable('image');
            }*/
			template.screen.disable('image' + index);
        },//初始化文字区域
        initText: function(areaId, name, x, y, w, h, zIndex){
            var id = 'area_text';
            template.screen.showText(id, name, x, y, w, h, zIndex);
            template.screen.text = {
                id: id,
                areaId: areaId,
                name: name
            };
        },
        initStaticText: function(areaId, name, x, y, w, h, zIndex){
            var id = 'area_staticText';
            template.screen.showStaticText(id, name, x, y, w, h, zIndex);
            template.screen.staticText = {
                id: id,
                areaId: areaId,
                name: name
            };
        },
        //初始化日期
        initDate: function(areaId, name, x, y, w, h, zIndex){
            var id = 'area_date';
            template.screen.showDate(id, name, x, y, w, h, zIndex);
            template.screen.date = {
                id: id,
                areaId: areaId,
                name: name
            };
        },//初始化时间
        initTime: function(areaId, name, x, y, w, h, zIndex){
            var id = 'area_time';
            template.screen.showTime(id, name, x, y, w, h, zIndex);
            template.screen.time = {
                id: id,
                areaId: areaId,
                name: name
            };
        },//初始化图标
        initLogo: function(areaId, name, x, y, w, h, mediaId, img, zIndex){
            var id = 'area_logo';
            template.screen.showLogo(id, name, x, y, w, h, img, zIndex);
            template.screen.logo = {
                id: id,
                areaId: areaId,
                mediaId: mediaId,
                name: name
            };
        },
        initWeather: function(areaId, name, x, y, w, h, zIndex){
            var id = 'area_weather';
            template.screen.showWeather(id, name, x, y, w, h);
            template.screen.weather = {
                id: id,
                areaId: areaId,
                name: name
            };
        },
        initWebpage: function(areaId, name, x, y, w, h, zIndex){
            var id = 'area_webpage';
            template.screen.showWebpage(id, name, x, y, w, h);
            template.screen.webpage = {
                id: id,
                areaId: areaId,
                name: name
            };
        },
        initMask: function(areaId, name, x, y, w, h, zIndex){
            var id = 'area_mask';
            template.screen.showMask(id, name, x, y, w, h, zIndex);
            template.screen.mask = {
                id: id,
                areaId: areaId,
                name: name
            };
        },
        enable: function(id){
            var cur = $('#' + id);
            cur.parent().removeClass('disable');
            cur.unbind('click').bind('click', function(event){
                switch (id) {
                    case 'movie':
                        template.screen.addMovie();
                        break;
                    case 'image1':
                        template.screen.addImage(1);
                        break;
					case 'image2':
                        template.screen.addImage(2);
                        break;
					case 'image3':
                        template.screen.addImage(3);
                        break;
                    case 'image4':
                        template.screen.addImage(4);
                        break;
                    case 'text':
                        template.screen.addText();
                        break;
                    case 'staticText':
                        template.screen.addStaticText();
                        break;
                    case 'date':
                        template.screen.addDate();
                        break;
                    case 'time':
                        template.screen.addTime();
                        break;
                    case 'weather':
                        template.screen.addWeather();
                        break;
                    case 'logo':
                        template.screen.addLogo();
                        break;
                    case 'webpage':
                        template.screen.addWebpage();
                        break;
                    case 'mask':
                        template.screen.addMask();
                        break;
                }
            });
        },
        disable: function(id){
            var cur = $('#' + id);
            cur.unbind('click');
            cur.parent().addClass('disable');
        },
		isContains : function(p, c){
			/*parent contain c*/
			var range1 = template.screen.getRealRange(p);
			var range2 = template.screen.getRealRange(c);
            var x1 = range1.x;
            var y1 = range1.y;
            var x2 = x1 + range1.w;
            var y2 = y1 + range1.h;
            
            var xx1 = range2.x;
            var yy1 = range2.y;
            var xx2 = xx1 + range2.w;
            var yy2 = yy1 + range2.h;
			
			return (x1 <= xx1 && y1 <= yy1 && x2 >= xx2 && y2 >= yy2);
		},
        isIntersect: function(curObj, targetObj, direct){
            if (curObj == undefined || targetObj == undefined /*|| direct == undefined || direct <= 0*/) {
                return false;
            }
            
            var id = curObj.attr('id');
            var tid = targetObj.attr('id');
            
            /*同一个对象不比较*/
            if (id == tid) {
                return false;
            }
			
			var range1 = template.screen.getRealRange(curObj);
			var range2 = template.screen.getRealRange(targetObj);
            var x1 = range1.x;
            var y1 = range1.y;
            var x2 = x1 + range1.w;
            var y2 = y1 + range1.h;
            
            var xx1 = range2.x;
            var yy1 = range2.y;
            var xx2 = xx1 + range2.w;
            var yy2 = yy1 + range2.h;
            
            if (template.screen.debug) {
                console.info("isIntersect curObj:" + curObj.text() + ", targetObj:" + targetObj.text() + ", direct:" + direct + ", point(left, top): " + (x1 + "," + y1) + ", point(right, bottom):" + (x2 + "," + y2) + ", target point(left, top):" + (xx1 + "," + yy1) + ", target point(right, bottom):" + (xx2 + "," + yy2));
            }
            
            var result = false;
            switch (direct) {
                case template.screen.RIGHT:
                    //上方修正
                    result = (xx1 >= x1) &&
                    (x2 > xx1 && ((y1 >= yy1 && y1 < yy2) || (y2 > yy1 && y2 <= yy2) || (y1 < yy1 && y2 > yy2) || (y1 == yy1 && y2 == yy2)));
                    break;
                case template.screen.LEFT:
                    result = (x2 > xx1) &&
                    (x1 < xx2 && ((y1 >= yy1 && y1 < yy2) || (y2 > yy1 && y2 <= yy2) || (y1 < yy1 && y2 > yy2) || (y1 == yy1 && y2 == yy2)));
                    break;
                case template.screen.UP:
                    result = (y2 > yy1) &&
                    (yy2 > y1 && ((x1 >= xx1 && x1 < xx2) || (x2 > xx1 && x2 <= xx2) || (x1 < xx1 && x2 > xx2) || (x1 == xx1 && x2 == xx2)));
                    break;
                case template.screen.DOWN:
                    result = (yy1 >= y1) &&
                    (y2 > yy1 && ((x1 >= xx1 && x1 < xx2) || (x2 > xx1 && x2 <= xx2) || (x1 < xx1 && x2 > xx2) || (x1 == xx1 && x2 == xx2)));
                    break;
                case template.screen.RIGHT_DOWN:
                    result = ((xx1 >= x1) &&
                    (x2 > xx1 && ((y1 >= yy1 && y1 < yy2) || (y2 > yy1 && y2 < yy2) || (y1 < yy1 && y2 > yy2) || (y1 == yy1 && y2 == yy2)))) ||
                    ((yy1 >= y1) &&
                    (y2 > yy1 && ((x1 >= xx1 && x1 < xx2) || (x2 > xx1 && x2 <= xx2) || (x1 < xx1 && x2 > xx2) || (x1 == xx1 && x2 == xx2))));
                    break;
                //all dragable
                case template.screen.DRAGE_RIGHT:
                case template.screen.DRAGE_DOWN:
                case template.screen.DRAGE_RIGHT_DOWN:
                case template.screen.DRAGE_LEFT:
                case template.screen.DRAGE_UP:
                case template.screen.DRAGE_LEFT_UP:
                case template.screen.DRAGE_RIGHT_UP:
                case template.screen.DRAGE_LEFT_DOWN:
				default:
				/*
				 * 回字
				 xx1,yy1
				 |----------------------|
				 |	|-------|			|
				 |	|x1,y1	|			|
				 |	|		|			|
				 |	|_______|x2,y2		|
				 |						|
				 |						|
				 |						|
				 |______________________| xx2,yy2
				*中
					    xx1,yy1
						|-------|
				 x1,y1	|		|
				 |------|-------|-----------|
				 |		|		|			|
				 |		|		|			|
				 |______|_______|___________| x2,y2
				 		|		|
				 		|_______|xx2, yy2
				*/
					
					result =
							/*顶点相交*/
							(x1 >= xx1 && x1 < xx2 && y1 >= yy1 && y1 < yy2)/*Left,Top*/
							||
							(x2 > xx1 && x2 <= xx2 && y1 >= yy1 && y1 < yy2) /*Right, Top*/
							||
							(x1 >= xx1 && x1 < xx2 && y2 > yy1 && y2 <= yy2)/*Left, Bottom*/
							||
							(x2 > xx1 && y2 > yy1 && x2 <= xx2 && y2 <= yy2)/*Right, Bottom*/
							||
							 (x1 < xx1 && y1 < yy1 && x2 > xx2 && y2 > yy2) /*外回*/
							||
							 (x1 >= xx1 && y1 >= yy1 && x2 <= xx2 && y2 <= yy2)/*内回和等*/
							||
							((y1 <= yy1 && y2 >= yy2)/*Top Must be outer*/ && ((x1 < xx1 && x2 > xx1 && x2 <= xx2)/*Left*/ || (x1 >= xx1 && x2 <= xx2)/*Middle*/ || (x2 > xx2 && x1 >= xx1 && x1 < xx2)/*Right*/)) /*中*/
							||
							((x1 <= xx1 && x2 >= xx2)/*Left or Right be outer*/ && ((y1 <= yy1 && y2 > yy1 && y2 <= yy2)/*Top*/ || (y1 > yy1 && y2 < yy2)/*Middle*/ || (y2 > yy2 && y1 >= yy1 && y1 < yy2)/*Bottom*/)) /*竖中*/
							
					;
                    break;
            }
            if (template.screen.debug) {
                console.info("isIntersect curObj:" + curObj.text() + ", targetObj:" + targetObj.text() + ", direct:" + direct + ", result: " + result);
            }
            return result;
        },
        getCrossPoint: function(targetObjs, direct){
            //获取某个方向上最合适的交点
            if (targetObjs == undefined || targetObjs.length < 2 || direct == undefined || direct <= 0) {
                return false;
            }
            var result = null;
            switch (direct) {
                case template.screen.RIGHT_DOWN:
                    var xArray = new Array();
                    var yArray = new Array();
                    for (var i = 0; i < targetObjs.length; i++) {
                        xArray.push(targetObjs[i].position().left);
                        yArray.push(targetObjs[i].position().top);
                    }
                    xArray = xArray.sort(function(a, b){
                        return a > b ? 1 : -1
                    });
                    yArray = yArray.sort(function(a, b){
                        return a > b ? 1 : -1
                    });
                    if (template.screen.debug) {
                        console.info(xArray);
                        console.info(yArray);
                        console.info('getCrossPoint,x:' + xArray[1] + ', y:' + yArray[1]);
                    }
                    
                    result = {
                        'x': xArray[1],
                        'y': yArray[1]
                    };
                    break;
            }
            if (result == null) {
                return false;
            }
            return result;
        },
        getIntersectObj: function(curObj, direct){ //获取在某个方向上与当前对象存在相交的对象列表
        	if(template.screen.template_type) {
        		if (curObj == undefined || direct == undefined || direct <= 0) {
	                return null;
	            }
	            
	            var id = curObj.attr('id');
	            var result = new Array();
	            var parentObj = curObj.parent();
	            if (parentObj == null) {
	                return null;
	            }
	            parentObj.children().each(function(){
	                var kid = $(this);
	                var cid = kid.attr('id');
	                if((id.indexOf('image') != -1 || id.indexOf('movie') != -1 || id.indexOf('webpage') != -1) && (cid.indexOf('image') != -1 || cid.indexOf('movie') != -1 || cid.indexOf('webpage') != -1)) {
	                	switch (direct) {
		                    case template.screen.RIGHT_DOWN:
		                        if (template.screen.isIntersect(curObj, kid, template.screen.RIGHT)) {
		                            result.push({
		                                'direct': template.screen.RIGHT,
		                                'obj': kid
		                            });
		                        }
		                        else 
		                            if (template.screen.isIntersect(curObj, kid, template.screen.DOWN)) {
		                                result.push({
		                                    'direct': template.screen.DOWN,
		                                    'obj': kid
		                                });
		                            }
		                        break;
		                    default:
		                        if (template.screen.isIntersect(curObj, kid, direct)) {
		                            result.push({
		                                'direct': direct,
		                                'obj': kid
		                            });
		                        }
		                        break;
		                }
	                }else {
	                	if((id.indexOf('date') != -1 || id.indexOf('weather') != -1 || id.indexOf('time') != -1 || id.indexOf('logo') != -1) && (cid.indexOf('date') != -1 || cid.indexOf('weather') != -1 || cid.indexOf('time') != -1 || cid.indexOf('logo') != -1)) {
		                	switch (direct) {
			                    case template.screen.RIGHT_DOWN:
			                        if (template.screen.isIntersect(curObj, kid, template.screen.RIGHT)) {
			                            result.push({
			                                'direct': template.screen.RIGHT,
			                                'obj': kid
			                            });
			                        }
			                        else 
			                            if (template.screen.isIntersect(curObj, kid, template.screen.DOWN)) {
			                                result.push({
			                                    'direct': template.screen.DOWN,
			                                    'obj': kid
			                                });
			                            }
			                        break;
			                    default:
			                        if (template.screen.isIntersect(curObj, kid, direct)) {
			                            result.push({
			                                'direct': direct,
			                                'obj': kid
			                            });
			                        }
			                        break;
			                }
		                }else {
		                	return null;
		                }
	                }
	            });
        	}else {
        		if (curObj == undefined || direct == undefined || direct <= 0) {
	                return null;
	            }
	            
	            var id = curObj.attr('id');
	            if (id == undefined || (id.indexOf('image') == -1 && id.indexOf('movie') == -1 && id.indexOf('date') == -1 && id.indexOf('weather') == -1)) {
	                return null;
	            }
	            
	            var result = new Array();
	            var parentObj = curObj.parent();
	            if (parentObj == null) {
	                return null;
	            }
	            parentObj.children().each(function(){
	                var kid = $(this);
	                var cid = kid.attr('id');
	                if (cid == undefined || (cid.indexOf('image') == -1 && cid.indexOf('movie') == -1 && cid.indexOf('date') == -1 && cid.indexOf('weather') == -1)) {
	                    return null;
	                }
	                switch (direct) {
	                    case template.screen.RIGHT_DOWN:
	                        if (template.screen.isIntersect(curObj, kid, template.screen.RIGHT)) {
	                            result.push({
	                                'direct': template.screen.RIGHT,
	                                'obj': kid
	                            });
	                        }
	                        else 
	                            if (template.screen.isIntersect(curObj, kid, template.screen.DOWN)) {
	                                result.push({
	                                    'direct': template.screen.DOWN,
	                                    'obj': kid
	                                });
	                            }
	                        break;
	                    default:
	                        if (template.screen.isIntersect(curObj, kid, direct)) {
	                            result.push({
	                                'direct': direct,
	                                'obj': kid
	                            });
	                        }
	                        break;
	                }
	            });
        	}
            if (template.screen.debug) {
                console.info('getIntersectObj ' + curObj.text() + ", direct:" + direct + ", intersect.length:" + result.length);
            }
            return result;
        },//停靠在目标对象，目前对象是在当前对象的那个方向
        dockArea: function(curObj, targetObjs, direct){
            if (curObj == undefined || targetObjs == undefined || targetObjs.length == 0 || direct == undefined) {
                return false;
            }
            
            var id = curObj.attr('id');
            if (id == undefined) {
                return false;
            }
            
            var minWidth = 1;
            var minHeight = 1;
            if (id.indexOf('image') != -1) {
                minWidth = template.screen.minImageWidth;
                minHeight = template.screen.minImageHeight;
            }
            else 
                if (id.indexOf('movie') != -1) {
                    minWidth = template.screen.minVideoWidth;
                    minHeight = template.screen.minVideoHeight;
                }
            if (template.screen.debug) {
                console.info("dockArea, id:" + id + ", minWidth:" + minWidth + ", minHeight:" + minHeight);
            }
            var result = false;
            var enlargeX = template.screen.width > template.screen.height ? 10 : 5; //横向差为10倍
            var enlargeY = template.screen.width > template.screen.height ? 5 : 10; //纵向差为5倍
            var curX = curObj.position().left;
            var curY = curObj.position().top;
            var curW = curObj.outerWidth();
            var curH = curObj.outerHeight();
            
            switch (direct) {
                case template.screen.RIGHT:
                    var minX = targetObjs[0].obj.position().left;
                    for (var i = 1; i < targetObjs.length; i++) {
                        if (targetObjs[i].obj.position().left < minX) {
                            minX = targetObjs[i].obj.position().left;
                        }
                    }
                    var width = minX - curObj.position().left;
                    if (width >= minWidth) {
                        curObj.css('width', width);
                        result = true;
                    }
                    break;
                case template.screen.LEFT:
					/*bug: only dock right side*/
                    var maxX = targetObjs[0].obj.position().left + targetObjs[0].obj.outerWidth(true);
                    for (var i = 1; i < targetObjs.length; i++) {
                        if (targetObjs[i].obj.position().left + targetObjs[i].obj.outerWidth(true) > maxX) {
                            maxX = targetObjs[i].obj.position().left + targetObjs[i].obj.outerWidth(true);
                        }
                    }
                    curObj.css('left', maxX);
                    curObj.css('width', curW - (maxX - curX));
                    result = true;
                    break;
                case template.screen.UP:
					/*bug: only dock down side*/
                    var maxY = targetObjs[0].obj.position().top + targetObjs[0].obj.outerHeight(true);
                    for (var i = 1; i < targetObjs.length; i++) {
                        if (targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight(true) > maxY) {
                            maxY = targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight(true);
                        }
                    }
                    curObj.css('top', maxY);
                    curObj.css('height', curH - (maxY - curY));
                    result = true;
                    break;
                case template.screen.DOWN:
                    var minY = targetObjs[0].obj.position().top;
                    for (var i = 1; i < targetObjs.length; i++) {
                        if (targetObjs[i].obj.position().top < minY) {
                            minY = targetObjs[i].obj.position().top;
                        }
                    }
                    var height = minY - curObj.position().top;
                    if (height >= minHeight) {
                        curObj.css('height', height);
                        result = true;
                    }
                    break;
                case template.screen.RIGHT_DOWN:
                    var minX = 0;
                    var minY = 0;
                    var width = 0;
                    var height = 0;
                    var minX = targetObjs[0].obj.position().left;
                    var minY = targetObjs[0].obj.position().top;
                    for (var i = 0; i < targetObjs.length; i++) {
                    
                        if (targetObjs[i].direct == template.screen.RIGHT) {
                            if (targetObjs[i].obj.position().left < minX) {
                                minX = targetObjs[i].obj.position().left;
                            }
                            if (template.screen.debug) {
                                console.info("dockArea[" + curObj.text() + "] RIGHT_DOWN, intersect RIGHT [" + targetObjs[i].obj.text() + "], minX:" + minX);
                            }
                        }
                        else 
                            if (targetObjs[i].direct == template.screen.DOWN) {
                                if (targetObjs[i].obj.position().top < minY) {
                                    minY = targetObjs[i].obj.position().top;
                                }
                                if (template.screen.debug) {
                                    console.info("dockArea[" + curObj.text() + "] RIGHT_DOWN, intersect DOWN [" + targetObjs[i].obj.text() + "], minY:" + minY);
                                }
                            }
                    }
                    width = minX - curObj.position().left;
                    height = minY - curObj.position().top;
                    
                    if (template.screen.debug) {
                        console.info("dockArea RIGHT_DOWN, width:" + width + ", height:" + height + ", minX:" + minX + ", minY:" + minY);
                    }
                    if (width >= minWidth) {
                        curObj.css('width', width);
                        result = true;
                    }
                    if (height >= minHeight) {
                        curObj.css('height', height);
                        result = true;
                    }
                    
                    break;
                case template.screen.DRAGE_LEFT:
                    var maxX = targetObjs[0].obj.position().left + targetObjs[0].obj.outerWidth();
                    for (var i = 1; i < targetObjs.length; i++) {
                        if (targetObjs[i].obj.position().left + targetObjs[i].obj.outerWidth() > maxX) {
                            maxX = targetObjs[i].obj.position().left + targetObjs[i].obj.outerWidth();
                        }
                    }
                    
                    curObj.css('left', maxX);
                    result = true;
                    break;
                case template.screen.DRAGE_UP:
                    var maxY = targetObjs[0].obj.position().top + targetObjs[0].obj.outerHeight();
                    for (var i = 1; i < targetObjs.length; i++) {
                        if (targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight() > maxY) {
                            maxY = targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight();
                        }
                    }
                    curObj.css('top', maxY);
                    result = true;
                    break;
                case template.screen.DRAGE_RIGHT:
                    var width = curObj.outerWidth();
                    var minX = targetObjs[0].obj.position().left;
                    for (var i = 1; i < targetObjs.length; i++) {
                        if (targetObjs[i].obj.position().left < minX) {
                            minX = targetObjs[i].obj.position().left;
                        }
                    }
                    minX = minX - width;
                    if (minX > 0) {
                        curObj.css('left', minX);
                        result = true;
                    }
                    break;
                case template.screen.DRAGE_DOWN:
                    var height = curObj.outerHeight();
                    var minY = targetObjs[0].obj.position().top;
                    for (var i = 1; i < targetObjs.length; i++) {
                        if (targetObjs[i].obj.position().top < minY) {
                            minY = targetObjs[i].obj.position().top;
                        }
                    }
                    minY = minY - height;
                    if (minY > 0) {
                        curObj.css('top', minY);
                        result = true;
                    }
                    break;
                case template.screen.DRAGE_LEFT_UP:
                    var width = curObj.outerWidth();
                    var height = curObj.outerHeight();
                    var left = curObj.position().left;
                    var top = curObj.position().top;
                    var targetX = 0;
                    var targetY = 0;
                    for (var i = 0; i < targetObjs.length; i++) {
                        targetX = targetObjs[i].obj.position().left + targetObjs[i].obj.outerWidth();
                        if (left >= targetObjs[i].obj.position().left &&
                        left < targetX &&
                        (targetX - left) * enlargeX < width &&
                        targetX + width <= template.screen.width) {
                            curObj.css('left', targetX);
                            result = true;
                        }
                        
                        targetY = targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight();
                        if (top >= targetObjs[i].obj.position().top &&
                        top < targetY &&
                        (targetY - top) * enlargeY < height &&
                        targetY + height <= template.screen.height) {
                            curObj.css('top', targetY);
                            result = true;
                        }
                        
                    }
                    break;
                case template.screen.DRAGE_RIGHT_UP:
                    var width = curObj.outerWidth();
                    var height = curObj.outerHeight(true);
                    var left = curObj.position().left + width;
                    var top = curObj.position().top;
                    var targetX = 0;
                    var targetY = 0;
                    for (var i = 0; i < targetObjs.length; i++) {
                        targetX = targetObjs[i].obj.position().left + targetObjs[i].obj.outerWidth();
                        var diffLeft = targetObjs[i].obj.position().left - width;
                        if (left > targetObjs[i].obj.position().left &&
                        left <= targetX &&
                        (left - targetObjs[i].obj.position().left) * enlargeX < width &&
                        diffLeft >= -0.01) {
                            //解决精度问题
                            if (diffLeft < 0) {
                                diffLeft = 0;
                            }
                            curObj.css('left', diffLeft);
                            result = true;
                        }
                        targetY = targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight();
                        if (top >= targetObjs[i].obj.position().top &&
                        top < targetY &&
                        (targetY - top) * enlargeY < height &&
                        targetY + height <= template.screen.height) {
                            curObj.css('top', targetY);
                            result = true;
                        }
                    }
                    break;
                case template.screen.DRAGE_RIGHT_DOWN:
                    var width = curObj.outerWidth();
                    var height = curObj.outerHeight();
                    var left = curObj.position().left + width;
                    var top = curObj.position().top + height;
                    var targetX = 0;
                    var targetY = 0;
                    for (var i = 0; i < targetObjs.length; i++) {
                        targetX = targetObjs[i].obj.position().left + targetObjs[i].obj.outerWidth();
                        var diffLeft = targetObjs[i].obj.position().left - width;
                        if (left > targetObjs[i].obj.position().left &&
                        left <= targetX &&
                        (left - targetObjs[i].obj.position().left) * enlargeX < width &&
                        diffLeft >= -0.001) {
                            if (diffLeft < 0) {
                                diffLeft = 0;
                            }
                            curObj.css('left', diffLeft);
                            result = true;
                        }
                        targetY = targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight();
                        var diffTop = targetObjs[i].obj.position().top - height;
                        if (top > targetObjs[i].obj.position().top &&
                        top <= targetY &&
                        (top - targetY) * enlargeY < height &&
                        diffTop >= -0.001) {
                            if (diffTop < 0) {
                                diffTop = 0;
                            }
                            curObj.css('top', diffTop);
                            result = true;
                        }
                    }
                    break;
                case template.screen.DRAGE_LEFT_DOWN:
                    var width = curObj.outerWidth();
                    var height = curObj.outerHeight();
                    var left = curObj.position().left;
                    var top = curObj.position().top + height;
                    var targetX = 0;
                    var targetY = 0;
                    for (var i = 0; i < targetObjs.length; i++) {
                        targetX = targetObjs[i].obj.position().left + targetObjs[i].obj.outerWidth();
                        if (left >= targetObjs[i].obj.position().left &&
                        left < targetX &&
                        (targetX - left) * enlargeX < width &&
                        targetX + width <= template.screen.width) {
                            curObj.css('left', targetX);
                            result = true;
                        }
                        
                        targetY = targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight();
                        var diffTop = targetObjs[i].obj.position().top - height;
                        if (top > targetObjs[i].obj.position().top &&
                        top <= targetY &&
                        (top - targetObjs[i].obj.position().top) * enlargeY < height &&
                        diffTop >= -0.001) {
                            if (diffTop < 0) {
                                diffTop = 0;
                            }
                            curObj.css('top', diffTop);
                            result = true;
                        }
                    }
                    break;
            }
			
			var x = curObj.position().left;
            var y = curObj.position().top;
            var w = curObj.outerWidth();
            var h = curObj.outerHeight();
			if(((x + w) > template.screen.width ) || (y + h) > template.screen.height){
				result = false;
				curObj.css('left', curX);
				curObj.css('top', curY);
				curObj.css('width', curW);
				curObj.css('height', curH);
			}
            if (template.screen.debug) {
                console.info("dockArea:" + curObj.text() + ", result:" + result + ", direct:" + direct);
            }
            return result;
            
        },
        adjustArea: function(curObj){
            if (curObj == undefined) {
                return;
            }
            
            //微调区域，控制边界，并保证高度和宽度是2的倍数
            var left = curObj.position().left;
            var top = curObj.position().top;
            if (left < 0) {
                curObj.css('left', 0);
            }
            
            if (top < 0) {
                curObj.css('top', 0);
            }
            
            var nWidth = curObj.innerWidth();
            var nHeight = curObj.innerHeight();
            if (left + nWidth > template.screen.width) {
                nWidth = template.screen.width - left;
                curObj.css('width', nWidth);
            }
            if (top + nHeight > template.screen.height) {
                nHeight = template.screen.height - top;
                curObj.css('height', nHeight);
            }
            
            
            var width = Math.round(template.screen.realWidth * nWidth / template.screen.width);
            var height = Math.round(template.screen.realHeight * nHeight / template.screen.height);
            /*
            if(template.screen.template_type) {
            	var width = template.screen.realWidth * nWidth / template.screen.width;
            	var height = template.screen.realHeight * nHeight / template.screen.height;
            }else {
            	var width = Math.round(template.screen.realWidth * nWidth / template.screen.width);
            	var height = Math.round(template.screen.realHeight * nHeight / template.screen.height);
            }*/
            
            if (template.screen.debug) {
                console.info("adjustArea width:" + width + ", height:" + height);
            }
			
			//  test
			if(!template.screen.template_type) {
				var wmod = width % 4;
	            if (wmod != 0) {
	                //adjust width
	                width -= wmod;
	                
	                nWidth = width * template.screen.width / template.screen.realWidth;
	                curObj.css('width', nWidth);
	                if (template.screen.debug) {
	                    console.info("adjustArea realWidth:" + width + ", show width:" + nWidth);
	                }
	            }
	            
	            if (height % 2 != 0) {
	                //addjust height
	                height--;
	                nHeight = height * template.screen.height / template.screen.realHeight;
	                curObj.css('height', nHeight);
	                if (template.screen.debug) {
	                    console.info("adjustArea realHeight:" + height + ", show height:" + nHeight);
	                }
	            }
			}	
			
			/* test
            var wmod = width % 4;
            if (wmod != 0) {
                //adjust width
                width -= wmod;
                
                nWidth = width * template.screen.width / template.screen.realWidth;
                curObj.css('width', nWidth);
                if (template.screen.debug) {
                    console.info("adjustArea realWidth:" + width + ", show width:" + nWidth);
                }
            }
            
            if (height % 2 != 0) {
                //addjust height
                height--;
                nHeight = height * template.screen.height / template.screen.realHeight;
                curObj.css('height', nHeight);
                if (template.screen.debug) {
                    console.info("adjustArea realHeight:" + height + ", show height:" + nHeight);
                }
            }*/
        },
        checkCurrentRange: function(curObj){//当前对象存在于其他对象的冲突，则返回true否则返回false
            //check only image and movie
            var id = curObj.attr('id');
            if (id == undefined || (id.indexOf('image') == -1 && id.indexOf('movie') == -1 && id.indexOf('date') == -1 && id.indexOf('weather') == -1 && id.indexOf('webpage') == -1 && id.indexOf('interaction') == -1 && id.indexOf('staticText') == -1 && id.indexOf('time') == -1)) {
                return;
            }
            //	判断当前区域是否可以重叠
            var x1 = curObj.position().left;
            var y1 = curObj.position().top;
            var x2 = x1 + curObj.outerWidth(true);
            var y2 = y1 + curObj.outerHeight(true);
            
            //check before
            var prev = curObj.prev();
            while (prev.length > 0) {
                var pid = prev.attr('id');
                
                //只检查照片区域和视频区域重叠问题
                if (pid.indexOf('image') > 0 || pid.indexOf('movie') > 0 || pid.indexOf('webpage') > 0 || pid.indexOf('interaction') > 0 || pid.indexOf('staticText') > 0 || pid.indexOf('date') > 0 || pid.indexOf('time') > 0) {
                    var xx1 = prev.position().left;
                    var yy1 = prev.position().top;
                    var xx2 = xx1 + prev.outerWidth(true);
                    var yy2 = yy1 + prev.outerHeight(true);
                    
                    if ((x1 >= xx1 && x1 < xx2) && (y1 >= yy1 && y1 < yy2)) {
                        //cur相对prev的位置 top left
                        return true;
                    }
                    else 
                        if ((x2 > xx1 && x2 <= xx2) && (y1 >= yy1 && y1 < yy2)) {
                            //top right
                            return true;
                        }
                        else 
                            if ((x1 >= xx1 && x1 < xx2) && (y2 > yy1 && y2 <= yy2)) {
                                //bottom left
                                return true;
                            }
                            else 
                                if ((x2 > xx1 && x2 <= xx2) && (y2 > yy1 && y2 <= yy2)) {
                                    //bottom right
                                    return true;
                                }
                                else 
                                    if (xx1 == x1 && xx2 == x2 && yy1 == y1 && yy2 == y2) {
                                        //just same
                                        return true;
                                    }
                                    else 
                                        if ((x1 < xx1 && x2 > xx2 && y1 < yy1 && y2 > yy2) || (xx1 < x1 && xx2 > x2 && yy1 < y1 && yy2 > y2)) {
                                            //all outer
                                            return true;
                                        }
                                        else 
                                            if (((x1 < xx1 && x2 > xx2) && ((y1 >= yy1 && y1 < yy2) || (y2 > yy1 && y2 <= yy2))) ||
                                            ((xx1 < x1 && xx2 > x2) && ((yy1 >= y1 && yy1 < y2) || (yy2 > y1 && yy2 <= y2)))) {
                                                //x outer
                                                return true;
                                            }
                                            else 
                                                if ((y1 < yy1 && y2 > yy2) && ((x1 >= xx1 && x1 < xx2) || (x2 > xx1 && x2 <= xx2)) ||
                                                (yy1 < y1 && yy2 > y2) && ((xx1 >= x1 && xx1 < x2) || (xx2 > x1 && xx2 <= x2))) {
                                                    //y outer
                                                    return true;
                                                }
                }
                prev = prev.prev();
            }
            //check after
            var next = curObj.next();
            while (next[0] != undefined) {
                var pid = next.attr('id');
                //只检查照片区域和视频区域重叠问题
                if (pid.indexOf('image') > 0 || pid.indexOf('movie') > 0 || pid.indexOf('webpage') > 0 || pid.indexOf('interaction') > 0 || pid.indexOf('staticText') > 0 || pid.indexOf('time') > 0 || pid.indexOf('date') > 0) {
                    var xx1 = next.position().left;
                    var yy1 = next.position().top;
                    var xx2 = xx1 + next.outerWidth(true);
                    var yy2 = yy1 + next.outerHeight(true);
                    
                    if ((x1 >= xx1 && x1 < xx2) && (y1 >= yy1 && y1 < yy2)) {
                        //top left
                        return true;
                    }
                    else 
                        if ((x2 > xx1 && x2 <= xx2) && (y1 >= yy1 && y1 < yy2)) {
                            //top right
                            return true;
                        }
                        else 
                            if ((x1 >= xx1 && x1 < xx2) && (y2 > yy1 && y2 <= yy2)) {
                                //bottom left
                                return true;
                            }
                            else 
                                if ((x2 > xx1 && x2 <= xx2) && (y2 > yy1 && y2 <= yy2)) {
                                    //bottom right
                                    return true;
                                }
                                else 
                                    if (xx1 == x1 && xx2 == x2 && yy1 == y1 && yy2 == y2) {
                                        //just same
                                        return true;
                                    }
                                    else 
                                        if ((x1 < xx1 && x2 > xx2 && y1 < yy1 && y2 > yy2) || (xx1 < x1 && xx2 > x2 && yy1 < y1 && yy2 > y2)) {
                                            //all outer
                                            return true;
                                        }
                                        else 
                                            if (((x1 <= xx1 && x2 >= xx2) && ((y1 >= yy1 && y1 < yy2) || (y2 > yy1 && y2 <= yy2))) ||
                                            ((xx1 <= x1 && xx2 >= x2) && ((yy1 >= y1 && yy1 < y2) || (yy2 > y1 && yy2 <= y2)))) {
                                                //x outer
                                                return true;
                                            }
                                            else 
                                                if ((y1 <= yy1 && y2 >= yy2) && ((x1 >= xx1 && x1 < xx2) || (x2 > xx1 && x2 <= xx2)) ||
                                                (yy1 <= y1 && yy2 >= y2) && ((xx1 >= x1 && xx1 < x2) || (xx2 > x1 && xx2 <= x2))) {
                                                    //y outer
                                                    return true;
                                                }
                }
                next = next.next();
            }
            
            return false;
        },
        changePosition: function(id, x, y, w, h, position){
            var obj = $('#' + id);
            if (position != undefined && position != '') {
                obj.css('position', position);
            }
            
            if (x >= 0) {
                obj.css('left', x);
            }
            if (y >= 0) {
                obj.css('top', y);
            }
            if (w > 0) {
                obj.css('width', w);
                
            }
            if (h > 0) {
                obj.css('height', h);
                //obj.children('dd').css('height', (h - 24)); //更新内容区域的高度
				template.screen.updateAreaBody(obj);			
            }
            
            
        },
        //return -1:no space 0: OK 1: min Ok
        calculateRange: function(w, h, minW, minH){
            var dls = $('#screen').children('dl');
            var areas = new Array();
            if (dls.length > 0) {
                for (var i = 0; i < dls.length; i++) {
                    if (dls[i].id.indexOf('movie') >= 0 || dls[i].id.indexOf('image') > 0 || dls[i].id.indexOf('webpage') >= 0 || dls[i].id.indexOf('interaction') > 0 || dls[i].id.indexOf('staticText') > 0 || dls[i].id.indexOf('time') > 0 || dls[i].id.indexOf('date') > 0) {
                        var a = $('#' + dls[i].id);
                        areas.push({
                            x: a.position().left,
                            y: a.position().top,
                            w: a.outerWidth(true),
                            h: a.outerHeight(true)
                        });
                    }
                }
            }
        },
        addBg: function(){
            template.screen.openImageLibrary('bg');
        },//显示背景区域
        showBg: function(bgImg){
            /*$('#screen').css('background-image', 'url(' + bgImg + ')')
             .css('background-repeat','no-repeat')
             .css('background-position','right bottom');*/
            $('#screenbg').attr('src', bgImg).attr('width', template.screen.width).attr('height', template.screen.height);
            tb_remove();
        },
        addMovie: function(){
            var id = 'area_movie';
            //default
            var w = template.screen.minVideoWidth;
            var h = template.screen.minVideoHeight;
            //			template.screen.calculateRange();
            //添加设置默认层
            //template.screen.showMovie(id, 'Movie/Photo', -1, -1, w, h, template.screen.zIndex);
            template.screen.showMovie(id, 'Movie/Photo', -1, -1, w, h, 10);
            template.screen.movie = {
                id: id
            };
        },//显示视频区域
        showMovie: function(id, title, x, y, w, h, zIndex){
            var minW = template.screen.minVideoWidth;
            var minH = template.screen.minVideoHeight;
            template.screen.createArea(id, 'movie', title, minW, template.screen.width, minH, template.screen.height, function(event, ui){
                /*$('#'+id +" dt").dblclick(function(e){
                 var cur = $(e.target);
                 var parent = cur.parent();
                 var pp = parent.parent();
                 parent.css('top', 0);
                 parent.css('left', 0);
                 
                 if ((pp.innerWidth() - parent.outerWidth(true) < 5) && (pp.innerHeight() - parent.outerHeight(true) < 5)) {
                 //set half screen
                 parent.css('width', pp.innerWidth()/2);
                 parent.css('height', pp.innerHeight()/2);
                 }
                 else {
                 //set full screen
                 parent.css('width', pp.innerWidth());
                 parent.css('height', pp.innerHeight());
                 }
                 parent.children('dd').css('height', (parent.innerHeight() - cur.outerHeight(true)));
                 });
                 */
                //$("#movie").button("disable");
                template.screen.disable('movie');
            }, zIndex);
            //只有当未设置初始值才默认规则
            if (w == -1 || h == -1) {
                //设置视频区域的位置
                if (template.screen.image.length == 0) {
                    //如果为设置照片，则默认宽度为screen宽度，高度为屏幕高度的2/3
                    template.screen.changePosition(id, 0, 0, template.screen.width, (template.screen.height * 2) / 3, 'absolute');
                }
                else {
                    //获取最后一个照片的信息，在最后一个照片的下方显示，宽度为全屏
                    var lastImg = $('#' + template.screen.image[template.screen.image.length - 1].id);
                    var top = lastImg.position().top + lastImg.outerHeight(true);
                    var parent = lastImg.parent();
                    if (top >= parent.innerHeight()) {
                        top = 0;
                    }
                    template.screen.changePosition(id, 0, top, template.screen.width, template.screen.height - top, 'absolute');
                }
            }
            else {
                template.screen.changePosition(id, x, y, w, h, 'absolute');
            }
            //判断当前对象是否存在其他覆盖区域
            /*if(template.screen.checkCurrentRange($('#' + id))){
             showMsg(template.screen.warnOverlap,'warn');
             }else{
             hideMsg();
             }*/
        },
        addImage: function(index){
            /*if (template.screen.image.length > 2) {
                //$('#image').button('disable');
                template.screen.disable('image' + index);
                return;
            }*/
            
            /*var index = template.screen.image.length == 0 ? 1 : (template.screen.image[template.screen.image.length - 1].index + 1);*/
            var id = 'area_image_' + index;
            //default
            var w = (120 * template.screen.width) / template.screen.realWidth;
            var h = (120 * template.screen.height) / template.screen.realHeight;
            if (template.screen.width < template.screen.height) {
                t = w;
                w = h;
                h = t;
            }
            template.screen.showImage(id, 'Image' + index, -1, -1, w, h, 20+index);
            template.screen.image.push({
                id: id,
                index: index
            });
            
            /*if (template.screen.image.length > 2) {
                //$('#image').button('disable');
                template.screen.disable('image');
                return;
            }*/
			template.screen.disable('image' + index);
            
        },
        showImage: function(id, title, x, y, w, h, zIndex){
            var minW = template.screen.minImageWidth;
            var minH = template.screen.minImageHeight;
            template.screen.createArea(id, 'image', title, minW, template.screen.width, minH, template.screen.height, function(event, ui){
                if (template.screen.image.length > 3) {
                    //$('#image').button('disable');
                    template.screen.disable('image');
                }
                
                //如果设置了xywh则无需默认
                if (w != -1 && h != -1) {
                    return;
                }
                
                //设置照片显示策略
                var cur = $('#' + id);
                var prev = cur.prev();
                var pid = prev.attr('id');
                var container = cur.parent();
                
                if (pid == undefined) {
                    //just default
                    template.screen.changePosition(id, 0, 0, template.screen.width, template.screen.height / 3);
                    return;
                }
                
                if (pid.indexOf('movie') > -1) {
                    //prev movie
                    template.screen.changePosition(id, 0, prev.outerHeight(true) + 1, container.innerWidth() / 3, container.innerHeight() - prev.outerHeight(true) - 1, 'absolute');
                    
                }
                else 
                    if (pid.indexOf('image') > -1) {
                        //image 如果前一元素为照片，则判断是否可以平铺到右侧，否则判断下侧，否则默认位置弹出，且提示
                        if (prev.position().left + prev.outerWidth(true) + prev.innerWidth() <= container.innerWidth()) {
                            template.screen.changePosition(id, prev.position().left + prev.outerWidth(true), prev.position().top, prev.outerWidth(true), prev.outerHeight(true), 'absolute');
                        }
                        else 
                            if (prev.position().top + prev.outerHeight(true) + prev.innerHeight() <= container.innerHeight()) {
                                template.screen.changePosition(id, prev.position().left, prev.position().top + prev.outerHeight(true), prev.outerWidth(true), prev.outerHeight(), 'absolute');
                            }
                    }
            }, zIndex);
            //设置照片域的位置
            template.screen.changePosition(id, x, y, w, h, 'absolute');
            
            //判断当前对象是否存在其他覆盖区域
            /*if(template.screen.checkCurrentRange($('#' + id))){
             showMsg(template.screen.warnOverlap,'warn');
             }else{
             hideMsg();
             }*/
        },
        addText: function(){
            var id = 'area_text';
            template.screen.showText(id, 'Text', -1, -1, -1, template.screen.defaultTextHeight);
            template.screen.text = {
                id: id
            };
        },
        showText: function(id, title, x, y, w, h, zIndex){
            if (template.screen.text != null) {
                showMsg('Text alread exist', 'warn');
                return;
            }
            if(template.screen.template_type) {
            	zIndex = 101;
            }else {
            	zIndex = 99;
            }
            template.screen.createArea(id, 'text', title, 120, template.screen.width, template.screen.minTextHeight, template.screen.height, function(event, ui){
                //$("#text" ).button("disable");
                template.screen.disable('text');
                
                //如果设置了xywh则无需默认
                if (w != -1 && h != -1) {
                    return;
                }
                
                //set position
                /*if(template.screen.movie != null){
                 //设置在视频区域的内部下面
                 var m = $('#' + template.screen.movie.id);
                 template.screen.changePosition(id, m.position().left, m.position().top + m.outerHeight(true) - 50, m.outerWidth(true), h,'absolute');
                 }*/
                var textArea = $('#' + id);
                template.screen.changePosition(id, 0, template.screen.height - h, template.screen.width, h, 'absolute');
            }, zIndex);
            //设置照片域的位置
            template.screen.changePosition(id, x, y, w, h, 'absolute');
        },
        addStaticText: function(){
            var id = 'area_staticText';
             template.screen.showStaticText(id, 'Bulletin Board', 0, template.screen.height-100, template.screen.width/2, 100);
            template.screen.staticText = {
                id: id
            };
        },
        showStaticText: function(id, title, x, y, w, h, zIndex){
            if (template.screen.staticText != null) {
                showMsg('Text alread exist', 'warn');
                return;
            }
            template.screen.createArea(id, 'staticText', title, 120, template.screen.width, template.screen.minTextHeight, template.screen.height, function(event, ui){
                template.screen.disable('staticText');
                if (w != -1 && h != -1) {
                    return;
                }
                var textArea = $('#' + id);
                template.screen.changePosition(id, x, y, w, h, 'absolute');
            }, zIndex);
            //设置照片域的位置
			$('#'+id).css('z-index', 9);
            template.screen.changePosition(id, x, y, w, h, 'absolute');
        },
        addDate: function(){
            //template.screen.openTimeDialog('date');
			var id = 'area_date';
            template.screen.showDate(id, 'Date', -1, -1, -1, -1);
            template.screen.date = {
                id: id
            };
        },
        //显示日期区域
        showDate: function(id, title, x, y, w, h, zIndex){
            template.screen.createArea(id, 'date', title, template.screen.minDateWidth, template.screen.maxDateWidth, template.screen.minDateHeight, template.screen.maxDateHeight, function(event, ui){
            
                //$("#date" ).button("disable");
                template.screen.disable('date');
                
                //如果设置了xywh则无需默认
                if (w != -1 && h != -1) {
                    return;
                }
                
                w = template.screen.minDateWidth;
                h = template.screen.minDateHeight;
                x = template.screen.width - w;
                y = 0;
                /*
                var date = $('#area_time');
                var pos = date.position();
                if (pos != null) {
                    y = pos.top + date.outerHeight(true);
                }*/
                //设置照片域的位置
                template.screen.changePosition(id, x, y, w, h, 'absolute');
            }, zIndex);
            //设置照片域的位置
			$('#'+id).css('z-index', 100);
            template.screen.changePosition(id, x, y, w, h, 'absolute');
        },
        addTime: function(){
            //template.screen.openTimeDialog('time');
            var id = 'area_time';
            template.screen.showTime(id, 'Time', -1, -1, -1, -1);
            template.screen.time = {
                id: id
            };
        },
        showTime: function(id, title, x, y, w, h, zIndex){
            template.screen.createArea(id, 'time', title, 50, template.screen.width, 50, 100, function(event, ui){
                //$("#time" ).button("disable");
                template.screen.disable('time');
                
                //如果设置了xywh则无需默认
                if (w != -1 && h != -1) {
                    return;
                }
                
                w = 100;
                h = 50;
                x = template.screen.width - w;
                y = 0;
                //设置照片域的位置
                template.screen.changePosition(id, x, y, w, h, 'absolute');
            }, zIndex);
            $('#'+id).css('z-index', 99);
            template.screen.changePosition(id, x, y, w, h, 'absolute');
            //var cur = $('#'+ id);
            //var prev = cur.prev();
        
        },
        addWeather: function(){
            /*template.screen.openTimeDialog('weather');*/
			var id = 'area_weather';
            template.screen.showWeather(id, 'Weather', -1, -1, -1, -1);
            template.screen.weather = {
                id: id
            };
        },
        showWeather: function(id, title, x, y, w, h, zIndex){
            template.screen.createArea(id, 'weather', title, template.screen.minWeatherWidth, template.screen.maxWeatherWidth, template.screen.minWeatherHeight, template.screen.maxWeatherHeight, function(event, ui){
                //$("#time" ).button("disable");
                template.screen.disable('weather');
                
                //如果设置了xywh则无需默认
                if (w != -1 && h != -1) {
                    return;
                }
                
                w = template.screen.minWeatherWidth;
                h = template.screen.minWeatherHeight;
                x = template.screen.width - w;
                y = 0;
                /*var date = $('#area_time');
                var pos = date.position();
                if (pos != null) {
                    y = pos.top + date.outerHeight(true);
                }*/
                //设置照片域的位置
                template.screen.changePosition(id, x, y, w, h, 'absolute');
            }, zIndex);
			//固定zindex
            $('#'+id).css('z-index', 101);
            template.screen.changePosition(id, x, y, w, h, 'absolute');
        },
        addWebpage: function(){
            //template.screen.openImageLibrary('logo');
            var id = 'area_webpage';
            var title = 'Webpage';
            template.screen.showWebpage(id, title, -1, -1, -1, -1);
            template.screen.webpage = {
                id: id
            };
        },
        showWebpage: function(id, title, x, y, w, h, zIndex){
            //var id = 'area_logo';
			zIndex=40;
			var minW = template.screen.minImageWidth;
            var minH = template.screen.minImageHeight;
            template.screen.createArea(id, 'webpage', title, minW, template.screen.width, minH, template.screen.height, function(){
                template.screen.disable('webpage');
                //$('#' + id+' > dd').css('background-color','#ff0000');
                //$('#' + id + ' > dd').html('<img src="/images/icons/web.jpg" alt="webpage" width="100%" height="100%" />');
				
                //如果设置了xywh则无需默认
                if (w == -1 || h == -1) {
                    //设置照片域的位置
                    template.screen.changePosition(id, 30, 30, 150, 150, 'absolute');
                }
                
                
            }, zIndex);
            //设置照片域的位置
            template.screen.changePosition(id, x, y, w, h,'absolute');
            tb_remove();
        },
        addMask: function(){
            //template.screen.openImageLibrary('logo');
            var id = 'area_mask';
            var title = 'Mask';
            template.screen.showMask(id, title, -1, -1, -1, -1);
            template.screen.mask = {
                id: id
            };
        },
        showMask: function(id, title, x, y, w, h, zIndex){
            //var id = 'area_logo';
			zIndex=40;
			var minW = template.screen.minImageWidth;
            var minH = template.screen.minImageHeight;
            template.screen.createArea(id, 'mask', title, minW, template.screen.width, minH, template.screen.height, function(){
                template.screen.disable('mask');
                $('#' + id+' > dd').css('background-color','#ffcc00');
				$('#' + id+' > dd').css('opacity', 0.6);
				//$('#' + id + ' > dd').html('<img src="/images/icons/in.jpg" alt="mask" width="100%" height="100%" />');
                //如果设置了xywh则无需默认
                if (w == -1 || h == -1) {
                    //设置照片域的位置
                    template.screen.changePosition(id, 30, 30, 150, 150, 'absolute');
                }
                
            }, zIndex);
            //设置照片域的位置
            template.screen.changePosition(id, x, y, w, h,'absolute');
            tb_remove();
            
        },
        addLogo: function(){
            //template.screen.openImageLibrary('logo');
            var id = 'area_logo';
            var title = 'Logo';
            template.screen.showLogo(id, title, -1, -1, -1, -1);
            template.screen.logo = {
                id: id
            };
        },
        showLogo: function(id, title, x, y, w, h, zIndex){
            //var id = 'area_logo';
			
			if(template.screen.template_type) {
				zIndex=98;
			}else {
				zIndex=200;
			}
			var min = 64;
			var max = 64;
            template.screen.createArea(id, 'logo', title, min, max, min, max, function(){
                template.screen.disable('logo');
                $('#' + id+' > dd').css('background-color','#7EC0EE');
				
                //如果设置了xywh则无需默认
                if (w == -1 || h == -1) {
                    //设置照片域的位置
                    template.screen.changePosition(id, 0, 0, max, max, 'absolute');
                }
                
                
            }, zIndex);
            //设置照片域的位置
            template.screen.changePosition(id, x, y, w, h,'absolute');
            tb_remove();
            
        },
		isResizeLimited : function(obj){
			var id = obj.attr('id');
			var orignLeft = obj.position().left;
			var orignTop = obj.position().top;
			var orignWidth = obj.innerWidth();
			var orignHeight = obj.innerHeight();
			if(orignTop >= template.screen.height || orignLeft >= template.screen.width){
				return true;
			}
			
			if(orignWidth > template.screen.width || orignHeight > template.screen.height){
				return true;
			}
			
			if((orignLeft + orignWidth) > template.screen.width || (orignTop + orignHeight) > template.screen.height){
				return true;
			}
			
			/*日期和天气*/
			if(id.indexOf('date') != -1){
				if((orignWidth > template.screen.maxDateWidth || orignHeight > template.screen.maxDateHeight) || (orignWidth < template.screen.minDateWidth || orignHeight < template.screen.minDateHeight)){
					return true;
				}
			}else if (id.indexOf('weather') != -1) {
				if((orignWidth > template.screen.maxWeatherWidth || orignHeight > template.screen.maxWeatherHeight) || (orignWidth < template.screen.minWeatherWidth || orignHeight < template.screen.minWeatherHeight)){
					return true;
				}
			}
			else 
				if (id.indexOf('text') != -1) {
					/*文本区域大小限制*/
					if (orignHeight > template.screen.maxTextHeight || orignHeight < template.screen.minTextHeight) {
						return true;
					}
				}
			
			return false;
		},
		adjustResize : function(obj, dir){
			var orignLeft = obj.position().left;
			var orignTop = obj.position().top;
			var orignWidth = obj.innerWidth();
			var orignHeight = obj.innerHeight();
			
			var changed = true;
			if(template.screen.template_type) {
				switch(dir){
					case template.screen.LEFT:
						if(orignLeft > 0){
							if(obj.attr('id') == 'area_logo') {
								obj.css('left',orignLeft);
								obj.css('width',orignWidth);
							}else {
								obj.css('left',orignLeft-template.screen.gridX);
								obj.css('width', orignWidth + template.screen.gridX);
							}
						}else{
							changed = false;
						}
					break;
					case template.screen.UP:
						if(orignTop > 0){
							if(obj.attr('id') == 'area_logo') {
								obj.css('top',orignTop);
								obj.css('height',orignHeight);
							}else {
								obj.css('top',orignTop-template.screen.gridY);
								obj.css('height',orignHeight+template.screen.gridY);
							}
						}else{
							changed = false;
						}
					break;
					case template.screen.RIGHT:
						if((orignLeft+orignWidth) < template.screen.width){
							if(obj.attr('id') == 'area_logo') {
								obj.css('width',orignWidth);
							}else {
								obj.css('width', orignWidth + template.screen.gridX);
							}
						}else{
							changed = false;
						}
					break;
					case template.screen.DOWN:
						if((orignTop+orignHeight) < template.screen.height){
							if(obj.attr('id') == 'area_logo') {
								obj.css('height',orignHeight);
							}else {
								obj.css('height',orignHeight+template.screen.gridY);
							}
						}else{
							changed = false;
						}
					break;
				}
			}else {
				switch(dir){
					case template.screen.LEFT:
					if(orignLeft > 0){
						//obj.css('left',orignLeft-template.screen.gridX);
						//obj.css('width',orignWidth+template.screen.gridX);
						if(obj.attr('id') == 'area_weather' || obj.attr('id') == 'area_date') {
							if(orignWidth >= 256) {
								changed = false;
							}else {
								if(orignLeft <= orignWidth) {
									obj.css('left', 0);
									obj.css('width', 2 * orignWidth);
								}else {
									obj.css('left', orignLeft - orignWidth);
									obj.css('width', 2*orignWidth);
								}
							}
						}else {
							if(obj.attr('id') == 'area_logo') {
								obj.css('left',orignLeft);
								obj.css('width',orignWidth);
							}else {
								obj.css('left',orignLeft-template.screen.gridX);
								obj.css('width', orignWidth + template.screen.gridX);
							}
						}
					}else{
						changed = false;
					}
					break;
					case template.screen.UP:
					if(orignTop > 0){
						//obj.css('top',orignTop-template.screen.gridY);
						//obj.css('height',orignHeight+template.screen.gridY);
						if(obj.attr('id') == 'area_weather' || obj.attr('id') == 'area_date') {
							if(orignHeight >= 256) {
								changed = false;
							}else {
								if(orignTop == 0) {
									changed = false;
								} else {
									if(orignTop > orignHeight) {
										obj.css('top', orignTop - orignHeight);
										obj.css('height', 2 *　orignHeight);
									}else {
										obj.css('top', 0);
										obj.css('height', 2 *　orignHeight);
									}
								}
							}
						}else {
							if(obj.attr('id') == 'area_logo') {
								obj.css('top',orignTop);
								obj.css('height',orignHeight);
							}else {
								obj.css('top',orignTop-template.screen.gridY);
								obj.css('height',orignHeight+template.screen.gridY);
							}
						}
					}else{
						changed = false;
					}
					break;
					case template.screen.RIGHT:
					if((orignLeft+orignWidth) < template.screen.width){
						//obj.css('width',orignWidth+template.screen.gridX);
						if(obj.attr('id') == 'area_weather' || obj.attr('id') == 'area_date') {
							if(orignWidth >= 256) {
								changed = false;
							}else {
								if((orignLeft + 2*orignWidth) > template.screen.width){
									if(orignLeft + orignWidth == template.screen.width) {
										changed = false;
									}else {
										obj.css('left', template.screen.width - 2 *　orignWidth);
										obj.css('width', 2 *　orignWidth);
									}
								}else {
									obj.css('width', 2 *　orignWidth);
								}
							}
						}else {
							if(obj.attr('id') == 'area_logo') {
								obj.css('width',orignWidth);
							}else {
								obj.css('width', orignWidth + template.screen.gridX);
							}
						}
					}else{
						changed = false;
					}
					break;
					case template.screen.DOWN:
					if((orignTop+orignHeight) < template.screen.height){
						//obj.css('height',orignHeight+template.screen.gridY);
						if(obj.attr('id') == 'area_weather' || obj.attr('id') == 'area_date') {
							if(orignHeight >= 256) {
								changed = false;
							}else {
								if(template.screen.height > (2*orignHeight +orignTop)) {
									obj.css('height', 2 * orignHeight);
									console.info('-----orignHeight:　'+ orignHeight);
								}else {
									if(template.screen.height-orignTop-orignHeight==0) {
										changed = false;
									} else {
										if(template.screen.height-orignTop-orignHeight <= orignHeight ) {
										console.info('----2');
											obj.css('top', template.screen.height - 2*orignHeight);
											obj.css('height', 2 * orignHeight);
										} else {
											changed = false;
										}
									}
								}
							}		
						}else {
							
							if(obj.attr('id') == 'area_logo') {
								obj.css('height',orignHeight);
							}else {
								obj.css('height',orignHeight+template.screen.gridY);
							}
						}
					}else{
						changed = false;
					}
					break;
				}
			}
			/*
			switch(dir){
				case template.screen.LEFT:
				if(orignLeft > 0){
					//obj.css('left',orignLeft-template.screen.gridX);
					//obj.css('width',orignWidth+template.screen.gridX);
					if(obj.attr('id') == 'area_weather' || obj.attr('id') == 'area_date') {
						if(orignWidth >= 256) {
							changed = false;
						}else {
							if(orignLeft <= orignWidth) {
								obj.css('left', 0);
								obj.css('width', 2 * orignWidth);
							}else {
								obj.css('left', orignLeft - orignWidth);
								obj.css('width', 2*orignWidth);
							}
						}
					}else {
						if(obj.attr('id') == 'area_logo') {
							obj.css('left',orignLeft);
							obj.css('width',orignWidth);
						}else {
							obj.css('left',orignLeft-template.screen.gridX);
							obj.css('width', orignWidth + template.screen.gridX);
						}
					}
				}else{
					changed = false;
				}
				break;
				case template.screen.UP:
				if(orignTop > 0){
					//obj.css('top',orignTop-template.screen.gridY);
					//obj.css('height',orignHeight+template.screen.gridY);
					if(obj.attr('id') == 'area_weather' || obj.attr('id') == 'area_date') {
						if(orignHeight >= 256) {
							changed = false;
						}else {
							if(orignTop == 0) {
								changed = false;
							} else {
								if(orignTop > orignHeight) {
									obj.css('top', orignTop - orignHeight);
									obj.css('height', 2 *　orignHeight);
								}else {
									obj.css('top', 0);
									obj.css('height', 2 *　orignHeight);
								}
							}
						}
					}else {
						if(obj.attr('id') == 'area_logo') {
							obj.css('top',orignTop);
							obj.css('height',orignHeight);
						}else {
							obj.css('top',orignTop-template.screen.gridY);
							obj.css('height',orignHeight+template.screen.gridY);
						}
					}
				}else{
					changed = false;
				}
				break;
				case template.screen.RIGHT:
				if((orignLeft+orignWidth) < template.screen.width){
					//obj.css('width',orignWidth+template.screen.gridX);
					if(obj.attr('id') == 'area_weather' || obj.attr('id') == 'area_date') {
						if(orignWidth >= 256) {
							changed = false;
						}else {
							if((orignLeft + 2*orignWidth) > template.screen.width){
								if(orignLeft + orignWidth == template.screen.width) {
									changed = false;
								}else {
									obj.css('left', template.screen.width - 2 *　orignWidth);
									obj.css('width', 2 *　orignWidth);
								}
							}else {
								obj.css('width', 2 *　orignWidth);
							}
						}
					}else {
						if(obj.attr('id') == 'area_logo') {
							obj.css('width',orignWidth);
						}else {
							obj.css('width', orignWidth + template.screen.gridX);
						}
					}
				}else{
					changed = false;
				}
				break;
				case template.screen.DOWN:
				if((orignTop+orignHeight) < template.screen.height){
					//obj.css('height',orignHeight+template.screen.gridY);
					if(obj.attr('id') == 'area_weather' || obj.attr('id') == 'area_date') {
						if(orignHeight >= 256) {
							changed = false;
						}else {
							if(template.screen.height > (2*orignHeight +orignTop)) {
								obj.css('height', 2 * orignHeight);
								console.info('-----orignHeight:　'+ orignHeight);
							}else {
								if(template.screen.height-orignTop-orignHeight==0) {
									changed = false;
								} else {
									if(template.screen.height-orignTop-orignHeight <= orignHeight ) {
									console.info('----2');
										obj.css('top', template.screen.height - 2*orignHeight);
										obj.css('height', 2 * orignHeight);
									} else {
										changed = false;
									}
								}
							}
						}		
					}else {
						
						if(obj.attr('id') == 'area_logo') {
							obj.css('height',orignHeight);
						}else {
							obj.css('height',orignHeight+template.screen.gridY);
						}
					}
				}else{
					changed = false;
				}
				break;
			}*/
			
			if(changed){
				if(obj.attr('id') == 'area_logo'){
					for(var i = template.screen.logoSize.length-1; i >= 0; i--){
						var size = template.screen.logoSize[i];
						if(orignWidth >= size || orignHeight >= size){
							if (dir == template.screen.LEFT || dir == template.screen.RIGHT) {
								obj.css('width', size);
							}
							else {
								obj.css('height', size);
							}
							break;
						}
					}
					template.screen.updateAreaBody(obj);
					template.screen.showAreaInfo(obj);
					return;
				}
				if(!template.screen.template_type) {//如果是NP100需要此判断
					//date、weather size 2013-12-19
					if(obj.attr('id') == 'area_date' || obj.attr('id') == 'area_weather'){				
						for(var i = 2; i < template.screen.DateWeatherSize.length; i++){
							var width = template.screen.DateWeatherSize[i];
							if(orignWidth >= width){
								if (dir == template.screen.LEFT || dir == template.screen.RIGHT) {
									obj.css('width', width);
								}
								break;
							}
						}
						
						for(var i = 2; i < template.screen.DateWeatherSize.length; i++){
							var height = template.screen.DateWeatherSize[i];
							if(orignHeight >= height){
								if (dir == template.screen.UP || dir == template.screen.DOWN) {
									obj.css('height', height);
								}
								break;
							}
						}
						template.screen.updateAreaBody(obj);
						template.screen.showAreaInfo(obj);
						return;
					}
				}
				
				if (template.screen.isResizeLimited(obj)) {
					obj.css('top', orignTop);
					obj.css('left', orignLeft);
					obj.css('width', orignWidth);
					obj.css('height', orignHeight);
					
				}else {
					var interObjs = template.screen.getIntersectObj(obj, dir);
					if (interObjs != null && interObjs.length > 0) {
						var result = template.screen.dockArea(obj, interObjs, dir);
						if (!result) {
							obj.css('top', orignTop);
							obj.css('left', orignLeft);
							obj.css('width', orignWidth);
							obj.css('height', orignHeight);
						}
					}
				}	
				//obj.children('dd').css('height', (obj.innerHeight() - obj.children('dt').outerHeight(true)));
				template.screen.updateAreaBody(obj);
                //update area info
                template.screen.showAreaInfo(obj);
			}
		},
		adjustMove : function(obj, dir){
			var orignLeft = obj.position().left;
			var orignTop = obj.position().top;
			var orignWidth = obj.innerWidth();
			var orignHeight = obj.innerHeight();
			var changed = true;
			switch(dir){
				case template.screen.LEFT:
				if(orignLeft >= template.screen.gridX){
					obj.css('left',orignLeft-template.screen.gridX);
				}else if(orignLeft > 0){
					obj.css('left', 0);
				}else{
					changed = false;
				}
				break;
				case template.screen.UP:
				if(orignTop >= template.screen.gridY){
					obj.css('top',orignTop-template.screen.gridY);
				}else if(orignTop > 0){
					obj.css('top', 0 );
				}else{
					changed = false;
				}
				break;
				case template.screen.RIGHT:
				if((orignLeft+orignWidth + template.screen.gridX) <= template.screen.width){
					obj.css('left',orignLeft+template.screen.gridX);
				}else if(orignLeft+orignWidth < template.screen.width){
					obj.css('left', template.screen.width - orignWidth);
				}else{
					changed = false;
				}
				break;
				case template.screen.DOWN:
				if((orignTop+orignHeight + template.screen.gridY) <= template.screen.height){
					obj.css('top',orignTop+template.screen.gridY);
				}else if(orignTop+orignHeight < template.screen.height){
					obj.css('top',template.screen.height-orignHeight);
				}else{
					changed = false;
				}
				break;
			}
			
			if(changed){
				if(template.screen.template_type) {
					var interObjs = template.screen.getIntersectObj(obj, dir);
	                if (interObjs != null && interObjs.length > 0) {
	                    var result = template.screen.dockArea(obj, interObjs, dir);
	                    if (!result) {
	                        obj.css('top', orignTop);
							obj.css('left', orignLeft);
	                    }
	                }
				}else {
					var interObjs = template.screen.getIntersectObj(obj, dir);
		            if (interObjs != null && interObjs.length > 0) {
		            	var result = template.screen.dockArea(obj, interObjs, dir);	         	
		                if (template.screen.OverlappingOne()) {
		                	switch(dir){
		                		case template.screen.LEFT:
			                		obj.css('left', orignLeft - template.screen.gridX);
			                		obj.css('width', orignWidth);
			                		obj.css('height', orignHeight);
			                		break;
			                	case template.screen.UP:
			                		obj.css('top', orignTop - template.screen.gridY);
			                		obj.css('width', orignWidth);
			                		obj.css('height', orignHeight);
			                		break;
			                	case template.screen.RIGHT:
			                		obj.css('left', orignLeft + template.screen.gridX);
			                		obj.css('width', orignWidth);
			                		obj.css('height', orignHeight);
			                		break;
			                	case template.screen.DOWN:
			                		obj.css('top', orignTop + template.screen.gridY);
			                		obj.css('width', orignWidth);
			                		obj.css('height', orignHeight);
			                		break;
		                	}
		            	}
		           	}
				}
				
				var width = obj.innerWidth();
				if(width != orignWidth){
					obj.css('left', orignLeft);
					obj.css('width', orignWidth);
				}
				var height = obj.innerHeight();
				if(height != orignHeight){
					obj.css('top', orignTop);
					obj.css('height', orignHeight);
				}
				//obj.children('dd').css('height', (obj.innerHeight() - obj.children('dt').outerHeight(true)));
                //update area info
                template.screen.showAreaInfo(obj);
			}
		},
		updateAreaBody : function(cur){
			/*-1 内部高度*/
			var height = (cur.innerHeight() - cur.children('dt').outerHeight(true));
			if(height < 0){
				height=0;
			}
			cur.children('dd').css('height', height);
		},
        createArea: function(id, type, title, minWidth, maxWidth, minHeight, maxHeight, callbackCreate, zIndex){
            zIndex = zIndex || template.screen.zIndex;
            if ("logo" != type && zIndex > template.screen.zIndex) {
                template.screen.zIndex = zIndex;
            }
            
            $('#screen').append(template.screen._template(id, type, title));
            var area = $("#" + id);
			area.attr('tabindex', template.screen.tabIndex);
			template.screen.tabIndex++;
            area.click(function(event){
                var cur = $(this);
				if (template.screen.readonly) {
					template.screen.showAreaInfo(cur);
                	return;
            	}
				if(template.screen.curObj != null){
					if(template.screen.curObj.attr('id') == cur.attr('id')){
						cur.focus();
						return;
					}
					template.screen.curObj.unbind("keydown");
					template.screen.curObj.children('dt').removeClass('selected');
				}
				//cur.attr("tabindex",cur.css('z-index'));
				cur.focus();
				template.screen.curObj=cur;
				template.screen.curObj.children('dt').addClass('selected');
				template.screen.curObj.keydown(function(event){
					//alert($(this));
					//j 74
					//k 75
					//l 76
					//m 77
					//left 37
					//up 38
					//down 40
					//right 39
					var dealed = false;
					var keyCode = event.keyCode;
					if(keyCode == 27){
						if(template.screen.curObj != null){
							template.screen.curObj.unbind("keydown");
							template.screen.curObj.children('dt').removeClass('selected');
							template.screen.curObj=null;
						}
					}
					if (event.ctrlKey) {
						switch(keyCode){
							case 74://j
							case 37://left
							template.screen.adjustResize($(this), template.screen.LEFT);
							dealed=true;	
							break;
							case 75://k
							case 38://up
							template.screen.adjustResize($(this), template.screen.UP);
							dealed=true;
							break;
							case 76://l
							case 39://right
							template.screen.adjustResize($(this), template.screen.RIGHT);
							dealed=true;
							break;
							case 77://m
							case 40://down
							template.screen.adjustResize($(this), template.screen.DOWN);
							dealed=true;
							break;
						}
					}else{
						switch(keyCode){
							case 74://j
							case 37://left
								template.screen.adjustMove($(this), template.screen.LEFT);
								dealed=true;	
							break;
							case 75://k
							case 38://up
								template.screen.adjustMove($(this), template.screen.UP);
								dealed=true;
							break;
							case 76://l
							case 39://right
								template.screen.adjustMove($(this), template.screen.RIGHT);
								dealed=true;
							break;
							case 77://m
							case 40://down
								template.screen.adjustMove($(this), template.screen.DOWN);
								dealed=true;
							break;
						}
					}
					if(dealed){
						event.preventDefault();
					}
				});
                template.screen.showAreaInfo(cur);
                if (cur.hasClass('movie') || cur.hasClass('image') || cur.hasClass('webpage') || cur.hasClass('interaction')) {
                    return;
                }
                /*if (template.screen.zIndex > cur.zIndex()) {
                    cur.css('z-index', ++template.screen.zIndex);
                }*/
            });
            area.css('z-index', zIndex);
			if (callbackCreate != null) {
				callbackCreate();
			}
            if (template.screen.readonly) {
                return;
            }
            
            $("#" + id + " .close").click(function(event){
                event.preventDefault();
                //var id = $(this).parents('.common-style').attr('id');
                var tmp = id.split('_');
				var idx  = 0;
                if (tmp.length == 3 && 'image' == tmp[1]) {
                    //删除照片区域
                    template.screen.removeImageArea(tmp[2]);
					idx=tmp[2];
                }
                else {
                    template.screen.removeArea(tmp[1]);
                }
				var tid = tmp[1] + (idx != 0 ? idx : '');
                template.screen.enable(tid);
                $("#" + id).resizable("destroy").remove();
                //关闭show title
                $('.tooltip').hide();
            });
            
            area.resizable({
                maxHeight: maxHeight,
                maxWidth: maxWidth,
                minHeight: minHeight,
                minWidth: minWidth,
                ghost: false,
                delay: 150,
                helper: "ui-resizable-helper",
                distance: 5,
                handles: "e, s, w, se, n",
                grid: [template.screen.gridX, template.screen.gridY],
				start : function(event, ui){
					/**修改防止成绝对坐标**/
					ui.element.attr('top', ui.element.css('top'));
					ui.element.attr('left', ui.element.css('left'));
					//ui.originalPosition.top=ui.element.css('top');
					//ui.originalPosition.left=ui.element.css('left');
				},
                resize: function(event, ui){
                    var cur = ui.helper;
                    template.screen.showAreaInfo(cur, true);
                },
                stop: function(event, ui){
					
					var originTop = ui.originalPosition.top;
					var orignLeft = ui.originalPosition.left;
					var orignWidth = ui.originalSize.width;
                    var orignHeight = ui.originalSize.height;
					var top = ui.position.top;
					var left = ui.position.left;
					var width = ui.size.width;
                    var height = ui.size.height;
					var cur = ui.originalElement;
					/*check logo if enable*/
					if(cur.attr('id') == 'area_logo'){
						var width = cur.innerWidth();
						var height = cur.innerHeight();
						if(orignWidth != width && orignHeight != height){
							/*angle*/
							for(var i = template.screen.logoSize.length-1; i >= 0; i--){
								var size = template.screen.logoSize[i];
								if(width >= size){
									cur.css('width', size);
									break;
								}
							}
							
							for(var i = template.screen.logoSize.length-1; i >= 0; i--){
								var size = template.screen.logoSize[i];
								if(height >= size){
									cur.css('height', size);
									break;
								}
							}
							
						}else if(orignWidth != width){
							/*H*/
							for(var i = template.screen.logoSize.length-1; i >= 0; i--){
								var size = template.screen.logoSize[i];
								if(width >= size){
									cur.css('width', size);
									break;
								}
							}
							
						}else if(orignHeight != height){
							/*V*/
							for(var i = template.screen.logoSize.length-1; i >= 0; i--){
								var size = template.screen.logoSize[i];
								if(height >= size){
									cur.css('height', size);
									break;
								}
							}
						}
						
						
						template.screen.updateAreaBody(cur);
						template.screen.showAreaInfo(cur);
						return;
					}
					
					if(!template.screen.template_type) {
						if(cur.attr('id') == 'area_date' || cur.attr('id') == 'area_weather'){
							var width = cur.innerWidth();
							var height = cur.innerHeight();
							if(orignWidth != width && orignHeight != height){
								/*angle*/
								for(var i = template.screen.DateWeatherSize.length-1; i >= 0; i--){
									var size = template.screen.DateWeatherSize[i];
									if(width >= size){
										if((cur.position().left + size) > template.screen.width) {
											cur.css('left', template.screen.width - size);
										}
										if(cur.position().left < 0) {
											cur.css('left', 0);
										}
										if(cur.position().top < 0) {
											cur.css('top', 0);
										}
										cur.css('width', size);
										break;
									}
								}
								
								for(var i = template.screen.DateWeatherSize.length-1; i >= 0; i--){
									var size = template.screen.DateWeatherSize[i];
									if(height >= size){
										if((cur.position().top + size) > template.screen.height) {
											cur.css('top', template.screen.height - size);
										}
										cur.css('height', size);
										break;
									}
								}
								
							}else if(orignWidth != width){
								/*H*/
								for(var i = template.screen.DateWeatherSize.length-1; i >= 0; i--){
									var size = template.screen.DateWeatherSize[i];
									if(width >= size){
										if((cur.position().left + size) > template.screen.width) {
											cur.css('left', template.screen.width - size);
										}
										if(cur.position().left < 0) {
											cur.css('left', 0);
										}
										if(cur.position().top < 0) {
											cur.css('top', 0);
										}
										cur.css('width', size);
										break;
									}
								}
								
							}else if(orignHeight != height){
								/*V*/
								for(var i = template.screen.DateWeatherSize.length-1; i >= 0; i--){
									var size = template.screen.DateWeatherSize[i];
									if(height >= size){
										if((cur.position().top + size) > template.screen.height) {
											cur.css('top', template.screen.height - size);
										}
										if(cur.position().top < 0) {
											cur.css('top', 0);
										}
										cur.css('height', size);
										break;
									}
								}
							}
							template.screen.updateAreaBody(cur);
							template.screen.showAreaInfo(cur);
							return;
						}
					}
                    
                    if (template.screen.debug) {
                        console.info(ui);
                        console.info(event);
                        console.info($(this).innerWidth());
                        console.info('left:' + cur.css('width') + ', cur.left:' + cur.position().left + ", orignWidth:" + orignWidth + ", width:" + width + ",cur.width:" + cur.innerWidth() + ", orignHeight:" + orignHeight + ", height:" + height);
                    }
                    var dir = 0;//unkowned
                    if (orignLeft > ui.position.left) {
                        //to the border of left
                        if (cur.position().left < 0) {
                            if (template.screen.debug) {
                                console.info('width:' + width + ', left:' + cur.position().left + ', origWidth:' + orignWidth + ', wishWidth:' + (orignWidth + ui.originalPosition.left));
                            }
                            cur.css('width', width + cur.position().left);
                            cur.css('left', 0);
							template.screen.updateAreaBody(cur);
                            template.screen.showAreaInfo(cur);
                            return;
                        }
                        if (template.screen.debug) {
                            console.info('left:' + ui.position.left + ', cur.left:' + cur.position().left);
                        }
                        dir = template.screen.LEFT;
                    }
                    else 
                        if (width > orignWidth && height > orignHeight) {
                            dir = template.screen.RIGHT_DOWN;
                        }
                        else 
                            if (width > orignWidth) {
                                dir = template.screen.RIGHT;
                            }
                            else 
                                if (ui.originalPosition.top > ui.position.top) {
                                    dir = template.screen.UP;
                                }
                                else 
                                    if (height > orignHeight) {
                                        dir = template.screen.DOWN;
                                    }
                    var changed = true;
                    //check only enlarge
                    if (dir > 0) {
                        if (dir == template.screen.RIGHT_DOWN) {
                            cur.css('height', orignHeight);
                            dir = template.screen.RIGHT;
                            var interObjs = template.screen.getIntersectObj(cur, dir);
                            if (interObjs != null && interObjs.length > 0) {
                                var result = template.screen.dockArea(cur, interObjs, dir);
                                if (!result) {
                                    cur.css('width', orignWidth);
                                }
                                else {
                                    hideMsg();
                                }
                            }
                            dir = template.screen.DOWN;
                            cur.css('height', height);
                            interObjs = template.screen.getIntersectObj(cur, dir);
                            if (interObjs != null && interObjs.length > 0) {
                                var result = template.screen.dockArea(cur, interObjs, dir);
                                if (!result) {
									cur.css('top', originTop);
                                    cur.css('height', orignHeight);
                                }
                                else {
                                    hideMsg();
                                }
                            }
                        }
                        else {
                            var interObjs = template.screen.getIntersectObj(cur, dir);
                            if (interObjs != null && interObjs.length > 0) {
                                //TODO change to nearby
                                var result = template.screen.dockArea(cur, interObjs, dir);
                                if (!result) {
                                	cur.css('top', originTop);
									cur.css('left', orignLeft);
                                    cur.css('width', orignWidth);
                                    cur.css('height', orignHeight);
                                    changed = false;
                                    //showMsg(template.screen.warnOverlap, 'warn');
                                }
                                else {
                                    hideMsg();
                                }
                            }else {
                            	// 鼠标拉大 向左靠边 2013-12-20
                            	if(dir == template.screen.LEFT) {							
									if(width == template.screen.width) {
										cur.css('left', 0);	
										cur.css('width', template.screen.width);
										changed = false;
									}				 
								}
								if(dir == template.screen.UP) {							
									if(height == template.screen.height) {
										cur.css('top', 0);	
										cur.css('height', template.screen.height);
										changed = false;
									}				 
								}
                            }
                        }
                    }
					
                    if (changed) {
                        template.screen.adjustArea(cur);
                    }
                    
                    if (template.screen.debug) {
                        console.info("adJust" + changed + ", cur.innerHeight:" + cur.innerHeight() + ", dt.height:" + cur.children('dt').outerHeight(true));
                    }
                    //cur.children('dd').css('height', (cur.innerHeight() - cur.children('dt').outerHeight(true)));
					template.screen.updateAreaBody(cur);
                    //update area info
                    template.screen.showAreaInfo(cur);
                    return;
                }
            }).draggable({
                containment: "parent",
                scroll: true,
                distance: 5,
                delay: 300,
				grid: [template.screen.gridX, template.screen.gridY],
                drag: function(event, ui){
                    var cur = $(ui.helper);
                    template.screen.showAreaInfo(cur);
                },
                stoped: function(event, ui){
					//暂时废弃该功能
                    var cur = $(ui.helper);
                    var orignLeft = ui.originalPosition.left;
                    var orignTop = ui.originalPosition.top;
                    var left = ui.position.left;
                    var top = ui.position.top;
                    var dir = 0;//unkowned
                    if (template.screen.debug) {
                        console.info("draggable stop:" + cur.text() + ", orignLeft:" + orignLeft + ", orignTop:" + orignTop + ", left:" + left + ", top:" + top + ", dir:" + dir);
                    }
                    var ac = -0.2;//精度
                    if (template.screen.debug) {
                        console.info("acL:" + Math.abs(orignLeft - left) + ", acT:" + Math.abs(orignTop - top));
                    }
                    if ((orignLeft - left > ac) && (orignTop - top) > ac) {
                        dir = template.screen.DRAGE_LEFT_UP;
                    }
                    else 
                        if ((left - orignLeft) > ac && (orignTop - top) > ac) {
                            dir = template.screen.DRAGE_RIGHT_UP;
                        }
                        else 
                            if ((left - orignLeft) > ac && (top - orignTop) > ac) {
                                dir = template.screen.DRAGE_RIGHT_DOWN;
                            }
                            else 
                                if ((orignLeft - left) > ac && (top - orignTop) > ac) {
                                    dir = template.screen.DRAGE_LEFT_DOWN;
                                }
                                else 
                                    if ((orignLeft - left) > ac) {
                                        dir = template.screen.DRAGE_LEFT;
                                    }
                                    else 
                                        if ((orignTop - top) > ac) {
                                            dir = template.screen.DRAGE_UP;
                                        }
                                        else 
                                            if ((left - orignLeft) > ac) {
                                                dir = template.screen.DRAGE_RIGHT;
                                            }
                                            else 
                                                if ((top - orignTop) > ac) {
                                                    dir = template.screen.DRAGE_DOWN;
                                                }
                    
                    if (template.screen.debug) {
                        console.info("draggable " + cur.text() + ", orignLeft:" + orignLeft + ", orignTop:" + orignTop + ", left:" + left + ", top:" + top + ", dir:" + dir);
                    }
                    changed = false;
                    if (dir > 0) {
                        var interObjs = template.screen.getIntersectObj(cur, dir);
                        if (interObjs != null && interObjs.length > 0) {
                            //TODO change to nearby
                            cur.css('left', orignLeft);
                            cur.css('top', orignTop);
                        }
                    }
                    
                    template.screen.showAreaInfo(cur);
                }
            });
			
            template.screen.zIndex++;
        },
        showAreaInfo: function(cur, resizing){
            if (resizing == undefined) {
                resizing = false;
            }
            var info = $('#areaInfo');
            var stop = $(document).scrollTop();
            if (stop > 0) {
                info.css('top', stop);
            }
            info.show();
            $('#areaTitle').text(cur.text());
            
            if (template.screen.debug) {
                console.info("showAreaInfo  cur:" + cur +", width: " + cur.width() + ", height: " + cur.height()+ ", innerWidth: " + cur.innerWidth() + ", innerHeight: " + cur.innerHeight());
            }
			var range = template.screen.getRealRange(cur);
            var xp = $('#areaX');
            xp.val(range.x);
            var yp = $('#areaY');
            yp.val(range.y);
            var wp = $('#areaWidth');
            wp.val(range.w);
            var hp = $('#areaHeight');
            hp.val(range.h);
            var wpp = $('#areaWidthPercent');
            wpp.val(range.wp);
            var hpp = $('#areaHeightPercent');
            hpp.val(range.hp);
            $('#areaChange').val(cur.attr('id'));
            if (resizing) {
                xp.addClass('blue');
                yp.addClass('blue');
                wp.addClass('blue');
                hp.addClass('blue');
                wpp.addClass('blue');
                hpp.addClass('blue');
            }
            else {
                xp.removeClass('blue');
                yp.removeClass('blue');
                wp.removeClass('blue');
                hp.removeClass('blue');
                wpp.removeClass('blue');
                hpp.removeClass('blue');
            }
        },
		getRealRange : function(cur){
			//var curWidth = Math.round(cur.innerWidth());
            //var curHeight = Math.round(cur.innerHeight());
            if(template.screen.template_type) {
            	var curWidth = cur.innerWidth();
            	var curHeight = cur.innerHeight();
            }else {
            	var curWidth = Math.round(cur.innerWidth());
            	var curHeight = Math.round(cur.innerHeight());
            }
            var widthPercent = Math.round(curWidth / template.screen.width * 10000) / 100.00;
            if (widthPercent > 100) {
                widthPercent = 100;
            }
            var heightPercent = Math.round(curHeight / template.screen.height * 10000) / 100.00;
            if (heightPercent > 100) {
                heightPercent = 100;
            }
            
            var width = template.screen.realWidth * curWidth / template.screen.width;
            var height = template.screen.realHeight * curHeight / template.screen.height;
            //adjust show
            
            /*if (width % 2 != 0) {
                width--;
            }
            if (height % 2 != 0) {
                height--;
            }*/
			//width = Math.round(width);
			//height = Math.round(height);
			if(template.screen.template_type) {
				width = width;
				height = height;
			}else {
				if (width % 2 != 0) {
	                width--;
	            }
	            if (height % 2 != 0) {
	                height--;
	            }
				width = Math.round(width);
				height = Math.round(height);
			}
			var x = Math.round((cur.position().left * template.screen.realWidth) / template.screen.width);
			var y = Math.round(cur.position().top * template.screen.realHeight / template.screen.height);
			
			return {
				x: x,
				y: y,
				w : width,
				h : height,
				wp: widthPercent,
				hp: heightPercent
			}
		},
        hideAreaInfo: function(){
            $('#areaInfo').hide();
        },
        removeArea: function(type){
            eval('var area =template.screen.' + type + '; if(area != null && area.areaId != undefined){template.screen.deletes.push({areaId:area.areaId,type:"' + type + '"});};template.screen.' + type + '=null;')
            template.screen.hideAreaInfo();
            
        },
        removeImageArea: function(index){
            var image = template.screen.image;
            for (var i = 0; i < image.length; i++) {
                if (image[i].index == index) {
                    if (image[i].areaId != undefined) {
                        template.screen.deletes.push({
                            areaId: image[i].areaId,
                            type: 'image'
                        });
                    }
                    image.splice(i, 1);
                    break;
                }
            }
            template.screen.hideAreaInfo();
        },
        _template: function(id, type, name){
            /*return '<div id="'+id+'" class="ui-widget-content draggable bg-'+type+' '+type+'">'
             + '<div class="title ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">'
             + (this.enableDeleteButton ?  '<a href="javascript:void();" class="ui-dialog-titlebar-close ui-corner-all" role="button" onclick="template.screen.destory(event,this);"><span class="ui-icon ui-icon-closethick">close</span></a>' : '')
             + '<span class="ui-widget-header">'+name+'</span>'
             + '<span class="xy" ></span>'
             + '</div>'
             +'</div>';*/
            //onclick="template.screen.destory(this,event);"
            if (template.screen.readonly) {
                return '<dl id="' + id + '" style="width: 400px;" class="' + type + ' common-style"><dt>' + name + '</dt><dd></dd></dl>';
            }
            else {
                return '<dl id="' + id + '" style="width: 400px;" class="' + type + ' common-style"><dt>' + name + '<div class="icon"><img class="close" title="Close" src="/images/icons/cross2.png" ></div></dt><dd></dd></dl>';
            }
        },
        destory: function(closeItem, event){
            event.preventDefault();
            var id = $(closeItem).parents('.common-style').attr('id');
            var tmp = id.split('_');
            if (tmp.length == 3 && 'image' == tmp[1]) {
                //删除照片区域
                template.screen.removeImageArea(tmp[2]);
            }
            else {
                template.screen.removeArea(tmp[1]);
            }
            //$('#'+tmp[1]).button('enable');
            this.enable(tmp[1]);
            $("#" + id).resizable("destroy");
            $("#" + id).remove();
            //关闭show title
            $('.tooltip').hide();
            
        },
        imagePage: function(curpage, type){
            var folderId = $('#folderId').val();
            if (folderId == '') {
                folderId = -1;
            }
            $.get('/template/images/' + curpage + '?type=' + type + '&folder_id=' + folderId + '&t=' + new Date().getTime(), function(data){
                $('#imageContent').html(data);
            });
        },
        imageFilter: function(type){
            $('#folderId').val($('#filterFolder').val());
            template.screen.imagePage(1, type);
        },
        setImage: function(mid, type){
            if (type == 'bg') {
                var bigsrc = $('#img_' + mid).attr('bigsrc');
				if (template.screen.bg != null) {
					template.screen.bg.mediaId=mid;
				}
				else {
					template.screen.bg = {
						mediaId: mid
					};
				}
                template.screen.showBg(bigsrc);
            }
            else 
                if (type == 'logo') {
                    var id = 'area_logo';
                    var title = 'Logo';
                    var src = $('#img_' + mid).attr('src');
                    template.screen.showLogo(id, title, -1, -1, -1, -1, src);
                    template.screen.logo = {
                        mediaId: mid,
                        id: id
                    };
                }
        },
        openImageLibrary: function(type){
        
            var t = 'Media Library';
            var a = '/template/images/?type=' + type + '&width=900&height=450&t=' + new Date().getTime();
            var g = '';
            tb_show(t, a, g);
            
            if (true) {
                return;
            }
            //old code
            //显示加载信息
            showLoading();
            
            $.get('/template/images?type=' + type + '&t=' + new Date().getTime(), function(data){
                hideLoading();
                $('.screen').after(data);
                $('#dialog').dialog({
                    modal: true,
                    width: 800,
                    height: 600,
                    create: function(event, ui){
                        $('.picture img').click(function(e){
                            if (type == 'bg') {
                                template.screen.bg = {
                                    mediaId: e.target.id
                                };
                                template.screen.showBg(e.target.style.backgroundImage.replace('tiny', 'main'));
                            }
                            else 
                                if (type == 'logo') {
                                
                                    var id = 'area_logo';
                                    var title = 'Logo';
                                    template.screen.showLogo(id, title, -1, -1, -1, -1, e.target.style.backgroundImage);
                                    template.screen.logo = {
                                        mediaId: e.target.id,
                                        id: id
                                    };
                                }
                            //删除当前节点
                            var d = $('#dialog');
                            d.parent().remove();
                            d.remove();
                        });
                    },
                    close: function(event, ui){
                        event.preventDefault();
                        var d = $('#dialog');
                        d.parent().remove();
                        d.remove();
                        return false;
                    }
                }).show('slow');
            });
        },
        openTimeDialog: function(type){
        
            var t = '';
            var a = '/template/' + type + 's?type=' + type + '&t=' + new Date().getTime();
            var g = '';
            tb_show(t, a, g);
            return;
            //显示加载信息
            //showLoading();
            $.get('/template/' + type + 's?type=' + type + '&t=' + new Date().getTime(), function(data){
                hideLoading();
                $('#screen').after(data);
                $('#dialog').dialog({
                    modal: true,
                    width: 320,
                    height: 300,
                    buttons: [{
                        text: 'OK',
                        click: function(event, ui){
                            var format = $('#format').val();
                            var family = $('#family').val();
                            var fontSize = $('#fontSize').val();
                            
                            var color = $('#color').val();
                            var bold = $('#bold').is(':checked') ? 1 : 0;
                            
                            var value = {
                                format: format,
                                family: family,
                                fontSize: fontSize,
                                color: color,
                                bold: bold
                            };
                            var up = firstToUpperCase(type);
                            var id = 'area_' + type;
                            var title = up;
                            eval('template.screen.' + type + '=value;template.screen.show' + up + '("' + id + '","' + title + '", -1, -1, -1, -1);template.screen.' + type + '.id="' + id + '";');
                            var d = $('#dialog');
                            d.parent().remove();
                            d.remove();
                            return false;
                        }
                    }],
                    close: function(event, ui){
                        event.preventDefault();
                        var d = $('#dialog');
                        d.parent().remove();
                        d.remove();
                        return false;
                    }
                }).show('slow');
            });
        },
        setDateSetting: function(type){
            var format = $('#format').val();
            var family = $('#family').val();
            var fontSize = $('#fontSize').val();
            
            var color = $('#color').val();
            var bold = $('#bold').is(':checked') ? 1 : 0;
            
            var value = {
                format: format,
                family: family,
                fontSize: fontSize,
                color: color,
                bold: bold
            };
            //var type = 'date';
            var up = firstToUpperCase(type);
            var id = 'area_' + type;
            var title = up;
            eval('template.screen.' + type + '=value;template.screen.show' + up + '("' + id + '","' + title + '", -1, -1, -1, -1);template.screen.' + type + '.id="' + id + '";');
            tb_remove();
        },
        changeDateFormat: function(obj){
            $.get('/template/get_date_format/' + $(obj).val() + '?t=' + new Date().getTime(), function(data){
                $('#datePreview').html(data);
            });
        },
        changeTimeFormat: function(obj){
            $.get('/template/get_time_format/' + $(obj).val() + '?t=' + new Date().getTime(), function(data){
                $('#datePreview').html(data);
            });
        },
        setWeatherSetting: function(){
            var format = $('#format').val();
            var family = $('#family').val();
            var fontSize = $('#fontSize').val();
            var color = $('#color').val();
            
            var value = {
                format: format,
                family: family,
                fontSize: fontSize,
                color: color
            };
            var type = 'weather';
            var up = firstToUpperCase(type);
            var id = 'area_' + type;
            var title = up;
            eval('template.screen.' + type + '=value;template.screen.show' + up + '("' + id + '","' + title + '", -1, -1, -1, -1);template.screen.' + type + '.id="' + id + '";');
            tb_remove();
        },
        changeWeatherFormat: function(obj){
            var preview = $('#datePreview');
            
            switch (parseInt(obj.value)) {
                case 1:
                    preview.children('div').css('display', 'block');
                    preview.children('img').css('display', 'none');
                    break;
                case 2:
                    preview.children('div').css('display', 'none');
                    preview.children('img').css('display', 'block');
                    break;
                case 3:
                    preview.children('div').css('display', 'block');
                    preview.children('img').css('display', 'block');
                    break;
            }
        },
        changeDateFamily: function(obj){
            $('#datePreview').css('font-family', $(obj).val());
        },
        changeDateFontSize: function(obj){
            $('#datePreview').css('font-size', $(obj).val());
        },
        changeDateFontBold: function(obj){
            if (obj.checked) {
                $('#datePreview').css('font-weight', 'bold');
            }
            else {
                $('#datePreview').css('font-weight', '');
            }
        },
        changeFontColor: function(color){
            $('#datePreview').css('color', color);
        },
        initColorSelector: function(selector){
            $('#' + selector).ColorPicker({
                color: $('#color').val(),
                onShow: function(colpkr){
                    $(colpkr).fadeIn(500);
                    return false;
                },
                onHide: function(colpkr){
                    $(colpkr).fadeOut(500);
                    return false;
                },
                onSubmit: function(hsb, hex, rgb, el){
                    $('#' + selector + ' div').css('backgroundColor', '#' + hex);
                    $('#color').val('#' + hex);
                    template.screen.changeFontColor('#' + hex);
                    $(el).ColorPickerHide();
                }
            });
        },//是否有重叠区域，并高亮边框提示
        isMovieOverlapping: function(){
            var result = false;
            if (template.screen.movie != null && template.screen.image.length > 0) {
                var parent = $('#' + template.screen.movie.id);
                x = parent.position().left;
                y = parent.position().top;
                w = parent.innerWidth();
                h = parent.innerHeight();
                
                
                //console.log('x:' + x +',y:' + y + ',w:' + w +',h:' + h);
                for (var i = 0; i < template.screen.image.length; i++) {
                    cur = $('#' + template.screen.image[i].id);
                    x1 = cur.position().left;
                    y1 = cur.position().top;
                    //w1 = cur.outerWidth(true);
                    //h1 = cur.outerHeight(true);
                    w1 = cur.innerWidth();
                    h1 = cur.innerHeight();
                    
                    //all equal
                    if (x1 == x && y1 == y && w1 == w && h1 == h) {
                        result = true;
                    }
                    else 
                        if ((x1 >= x) && (x1 < (x + w)) && (y1 >= y) && (y1 < (y + h))) { //corn 1
                            result = true;
                        }
                        else 
                            if (((x1 + w1) > x && (x1 + w1) <= (x + w) && (y1 >= y) && y1 < (y + h))) { //corn 2
                                result = true;
                            }
                            else 
                                if (((x1 > x) && (x1 < (x + w)) && ((y1 + h1) >= y) && (y1 + h1) < (y + h1))) { //corn 3
                                    result = true;
                                }
                                else 
                                    if ((((x1 + w1) > x) && ((x1 + w1) < (x + w)) && ((y1 + h1) >= y) && (y1 + h1) < (y + h))) {//corn 4
                                        result = true;
                                    }
                    
                    if (result) {
                        cur.css('borderWidth', '1px').css('borderStyle', 'solid').css('borderColor', 'red');
                        break;
                    }
                    else {
                        cur.css('borderWidth', '').css('borderStyle', '').css('borderColor', '');
                    }
                }
            }
            return result;
        },
        isImageOverlapping: function(){
            var result = false;
            if (template.screen.image.length > 1) {
                for (var i = 0; i < template.screen.image.length; i++) {
                    var parent = $('#' + template.screen.image[i].id);
                    x = parent.position().left;
                    y = parent.position().top;
                    w = parent.innerWidth();
                    h = parent.innerHeight();
                    for (var j = i + 1; j < template.screen.image.length; j++) {
                        cur = $('#' + template.screen.image[j].id);
                        x1 = cur.position().left;
                        y1 = cur.position().top;
                        //w1 = cur.outerWidth(true);
                        //h1 = cur.outerHeight(true);
                        w1 = cur.innerWidth();
                        h1 = cur.innerHeight();
                        
                        //console.log("x:" +x +", x1:" + x1 +", y:" + y +", y1:"+ y1+", w:" + w+", w1:" + w1 +", h:" + h+", h1:" + h1);
                        //all equal
                        if (x1 == x && y1 == y && w1 == w && h1 == h) {//完全重叠
                            result = true;
                        }
                        else 
                            if ((x1 >= x) && (x1 < (x + w)) && (y1 >= y) && (y1 < (y + h))) { //corn 1
                                result = true;
                            }
                            else 
                                if (((x1 + w1) > x && (x1 + w1) <= (x + w) && (y1 >= y) && y1 < (y + h))) { //corn 2
                                    result = true;
                                }
                                else 
                                    if (((x1 > x) && (x1 < (x + w)) && ((y1 + h1) >= y) && (y1 + h1) < (y + h1))) { //corn 3
                                        result = true;
                                    }
                                    else 
                                        if ((((x1 + w1) > x) && ((x1 + w1) < (x + w)) && ((y1 + h1) >= y) && (y1 + h1) < (y + h))) {//corn 4
                                            result = true;
                                        }
                        
                        if (result) {
                            cur.css('borderWidth', '1px').css('borderStyle', '').css('borderColor', 'red');
                            break;
                        }
                        else {
                            cur.css('borderWidth', '').css('borderStyle', '').css('borderColor', '');
                        }
                    }
                    if (result) {
                        break;
                    }
                }
            }
            
            return result;
        },
		/*Logo is correct for pos*/
		isLogoCorrect : function(){
			var result = false;
			if(template.screen.logo != null){
				var cur = $('#area_logo');
				var areas = $('#screen dl');
				if(areas.length <= 1){
					result = false;
					return result;
				}
				
				for (var i = 0; i < areas.length; i++) {
					if (areas[i].id == 'area_movie' || areas[i].id.indexOf('area_image') != -1 || areas[i].id == 'area_webpage' || areas[i].id.indexOf('area_interaction') != -1) {
						var next = $(areas[i]);
						if (template.screen.isContains(next, cur)) {
							result = true;
							break;
						}
					}
				}
				
			}else{
				result = true;
			}
			
			return result;
		},
        isOverlapping: function(){
        	
			
        },
        OverlappingOne: function(){
        	//return template.screen.
            //return template.screen.isMovieOverlapping() || template.screen.isImageOverlapping() || template.screen.isDateOverlapping();
			var areas = $('#screen dl');
			//one or less area ignore
			if(areas.length <= 1){
				return false;
			}
			var result = false;
			for(var i =0 ;i < areas.length; i++){
				if(areas[i].id == 'area_text' || areas[i].id == 'area_logo'){
					continue;
				}
				
				var cur = $(areas[i]);
				for(var j = (i+1); j < areas.length; j++){
					if(areas[j].id == 'area_text' || areas[j].id == 'area_logo' || areas[j].id == areas[i].id){
						continue;
					}
					/*if((areas[i].id=='area_logo'&& (areas[j].id == 'area_movie' || areas[j].id.indexOf('area_image') != -1)) || (areas[j].id == 'area_logo' && ( (areas[i].id == 'area_movie' || areas[i].id.indexOf('area_image') != -1)))){
						continue;
					}*/
					var next = $(areas[j]);
					if(template.screen.isIntersect(cur, next)){
						next.css('borderWidth', '1px').css('borderStyle', '').css('borderColor', 'red');
						result = true;
						break;
					}else{
						next.css('borderWidth', '').css('borderStyle', '').css('borderColor', '');
					}
				}
				if(result){
					break;
				}
			}
			return result;
        },
        createData: function(){
            var data = new Object();
            var x, y, w, h, zindex, areaId, name;
            //设置区域宽度信息
            if (template.screen.movie != null) {
                var parent = $('#' + template.screen.movie.id);
                x = parent.position().left;
                y = parent.position().top;
                w = parent.innerWidth();
                h = parent.innerHeight();
               // zindex = parent.zIndex();
                zindex = document.getElementById(template.screen.movie.id).style.zIndex;
                //zindex = parent.style.zIndex;
               
                areaId = template.screen.movie.areaId == undefined ? 0 : template.screen.movie.areaId;
                name = template.screen.movie.name == undefined ? 0 : template.screen.movie.name;
                
                data.movie = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"area_id":' + areaId + ',"name":"' + name + '","zindex":' + zindex + '}';
            }
            
            //设置照片的相关属性
            if (template.screen.image.length > 0) {
                data.image = [];
                for (var i = 0; i < template.screen.image.length; i++) {
                    var parent = $('#' + template.screen.image[i].id);
                    x = parent.position().left;
                    y = parent.position().top;
                    w = parent.innerWidth();
                    h = parent.innerHeight();
                    //zindex = parent.zIndex();
                    zindex = document.getElementById(template.screen.image[i].id).style.zIndex;
                    areaId = template.screen.image[i].areaId == undefined ? 0 : template.screen.image[i].areaId;
                    //name = template.screen.image[i].name == undefined ? 0 : template.screen.image[i].name;
					var num = i + 1; //照片区域的数量
                    name = 'Image'+num;
                    
                    //template.screen.image[i].json='{"x":'+x+',"y":'+y+',"w":'+w+',"h":'+h+'}';
                    data.image[i] = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"area_id":' + areaId + ',"name":"' + name + '","zindex":' + zindex + '}';
                }
            }
            
            //设置文本区域
            if (template.screen.text != null) {
                var parent = $('#' + template.screen.text.id);
                x = parent.position().left;
                y = parent.position().top;
                w = parent.innerWidth();
                h = parent.innerHeight();
               // zindex = parent.zIndex();
                zindex = document.getElementById(template.screen.text.id).style.zIndex;
 
                areaId = template.screen.text.areaId == undefined ? 0 : template.screen.text.areaId;
                name = template.screen.text.name == undefined ? 0 : template.screen.text.name;
                
                //template.screen.text.json='{"x":'+x+',"y":'+y+',"w":'+w+',"h":'+h+'}';
                data.text = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"area_id":' + areaId + ',"name":"' + name + '","zindex":' + zindex + '}';
            }
            
            //设置静态文本区域
            if (template.screen.staticText != null) {
                var parent = $('#' + template.screen.staticText.id);
                x = parent.position().left;
                y = parent.position().top;
                w = parent.innerWidth();
                h = parent.innerHeight();
                //zindex = parent.zIndex();
                zindex = document.getElementById(template.screen.staticText.id).style.zIndex;
                areaId = template.screen.staticText.areaId == undefined ? 0 : template.screen.staticText.areaId;
                name = template.screen.staticText.name == undefined ? 0 : template.screen.staticText.name;
                
                //template.screen.text.json='{"x":'+x+',"y":'+y+',"w":'+w+',"h":'+h+'}';
                data.staticText = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"area_id":' + areaId + ',"name":"' + name + '","zindex":' + zindex + '}';
            }
            
            //日期区域
            if (template.screen.date != null) {
                var parent = $('#' + template.screen.date.id);
                x = parent.position().left;
                y = parent.position().top;
                w = parent.innerWidth();
                h = parent.innerHeight();
               // zindex = parent.zIndex();
                zindex = document.getElementById(template.screen.date.id).style.zIndex;
                areaId = template.screen.date.areaId == undefined ? 0 : template.screen.date.areaId;
                name = template.screen.date.name == undefined ? 0 : template.screen.date.name;
                
                
                setting = '{}';
                if (template.screen.date.format != undefined) {
                    setting = '{"format":"' + template.screen.date.format + '","family":"' + template.screen.date.family + '","color":"' + template.screen.date.color + '","bold":' + template.screen.date.bold + ',"font_size":"' + template.screen.date.fontSize + '"}';
                }
                data.date = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"area_id":' + areaId + ',"name":"' + name + '","zindex":' + zindex + ',"setting":' + setting + '}';
            }
            
            //时间区域
            if (template.screen.time != null) {
                var parent = $('#' + template.screen.time.id);
                x = parent.position().left;
                y = parent.position().top;
                w = parent.innerWidth();
                h = parent.innerHeight();
                //zindex = parent.zIndex();
                zindex = document.getElementById(template.screen.time.id).style.zIndex;
                areaId = template.screen.time.areaId == undefined ? 0 : template.screen.time.areaId;
                name = template.screen.time.name == undefined ? 0 : template.screen.time.name;
                
                setting = '{}';
                if (template.screen.time.format != undefined) {
                    setting = '{"format":"' + template.screen.time.format + '","family":"' + template.screen.time.family + '","color":"' + template.screen.time.color + '","bold":' + template.screen.time.bold + ',"font_size":"' + template.screen.time.fontSize + '"}'
                }
                data.time = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"area_id":' + areaId + ',"name":"' + name + '","zindex":' + zindex + ',"setting":' + setting + '}';
            }
            //Weather
            if (template.screen.weather != null) {
                var parent = $('#' + template.screen.weather.id);
                x = parent.position().left;
                y = parent.position().top;
                w = parent.innerWidth();
                h = parent.innerHeight();
               // zindex = parent.zIndex();
                zindex = document.getElementById(template.screen.weather.id).style.zIndex;
                areaId = template.screen.weather.areaId == undefined ? 0 : template.screen.weather.areaId;
                name = template.screen.weather.name == undefined ? 0 : template.screen.weather.name;
                
                setting = '{}';
                if (template.screen.weather.format != undefined) {
                    setting = '{"format":"' + template.screen.weather.format + '","family":"' + template.screen.weather.family + '","color":"' + template.screen.weather.color + '","font_size":"' + template.screen.weather.fontSize + '"}'
                }
                data.weather = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"area_id":' + areaId + ',"name":"' + name + '","zindex":' + zindex + ',"setting":' + setting + '}';
            }
            
            //Webpage
            if (template.screen.webpage != null) {
                var parent = $('#' + template.screen.webpage.id);
                x = parent.position().left;
                y = parent.position().top;
                w = parent.innerWidth();
                h = parent.innerHeight();
               // zindex = parent.zIndex();
                zindex = document.getElementById(template.screen.webpage.id).style.zIndex;
                areaId = template.screen.webpage.areaId == undefined ? 0 : template.screen.webpage.areaId;
                name = template.screen.webpage.name == undefined ? 0 : template.screen.webpage.name;
                data.webpage = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"area_id":' + areaId + ',"name":"' + name + '","zindex":' + zindex + '}';
            }
            //mask
            if (template.screen.mask != null) {
                var parent = $('#' + template.screen.mask.id);
                x = parent.position().left;
                y = parent.position().top;
                w = parent.innerWidth();
                h = parent.innerHeight();
                //zindex = parent.zIndex();
                zindex = document.getElementById(template.screen.mask.id).style.zIndex;
                areaId = template.screen.mask.areaId == undefined ? 0 : template.screen.mask.areaId;
                name = template.screen.mask.name == undefined ? 0 : template.screen.mask.name;
                data.mask = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"area_id":' + areaId + ',"name":"' + name + '","zindex":' + zindex + '}';
            }
            //Logo区域
            if (template.screen.logo != null) {
                var parent = $('#' + template.screen.logo.id);
                x = parent.position().left;
                y = parent.position().top;
                w = parent.innerWidth();
                h = parent.innerHeight();
               // zindex = parent.zIndex();
                zindex = document.getElementById(template.screen.logo.id).style.zIndex;
                mediaId = template.screen.logo.mediaId== undefined ? 0 : template.screen.logo.mediaId;
                areaId = template.screen.logo.areaId == undefined ? 0 : template.screen.logo.areaId;
                name = template.screen.logo.name == undefined ? '' : template.screen.logo.name;
                
                data.logo = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"area_id":' + areaId + ',"name":"' + name + '","zindex":' + zindex + ',"media_id":' + mediaId + '}';
            }
            
            //BG区域
            if (template.screen.bg != null) {
                mediaId = template.screen.bg.mediaId;
                areaId = template.screen.bg.areaId == undefined ? 0 : template.screen.bg.areaId;
                data.bg = '{"x":0,"y":0,"w":' + template.screen.width + ',"h":' + template.screen.height + ',"area_id":' + areaId + ',"media_id":' + mediaId + '}';
            }
            
            //screen
            data.screen = '{"w":' + template.screen.width + ',"h":' + template.screen.height + ', "template_type":'+template.screen.template_type+'}';
            data.id = template.screen.id;
            
            //组织已经删除的ID
            if (template.screen.deletes.length > 0) {
                deletes = '{';
                for (var i = 0; i < template.screen.deletes.length; i++) {
                    deletes += '"' + i + '":{"id":' + template.screen.deletes[i].areaId + ',"type":"' + template.screen.deletes[i].type + '"}';
                    if (i < template.screen.deletes.length - 1) {
                        deletes += ',';
                    }
                }
                deletes += '}';
                data.deletes = deletes;
            }
            
            return data;
        },
        save: function(){
        	if(template.screen.template_type != 1) {
        		if(template.screen.movie == null){
					alert(template.screen.warnVideo);
					return;
				}
        	}
        	/*
        	if(template.screen.template_type == 1) {
	        	if(template.screen.staticText != null && template.screen.bg == null){
					alert("Bg zone is required for playback");
					return;
				}
			}*/
        	/*
			if(template.screen.movie == null){
				alert(template.screen.warnVideo);
				return;
			}*/
            /*if(!template.screen.isLogoCorrect()){
				alert(template.screen.warnLogo);
			}else */if (template.screen.isOverlapping()) {
                alert(template.screen.warnOverlap);
            }
            else {
                var saveButton = $('#save');
                saveButton.unbind('click');
                saveButton.removeClass('btn-01');
                saveButton.addClass('btn-02');
                restScrollPosition();
                $.post('/template/save_screen?t=' + new Date().getTime(), template.screen.createData(), function(data){
                    if (data.code == 0) {
                        showMsg(data.msg, 'success');
                        //update local value
                        if (data.bg != undefined) {
                            template.screen.bg.areaId = data.bg.area_id;
                        }
                        if (data.movie != undefined) {
                            template.screen.movie.areaId = data.movie.area_id;
                        }
                        if (data.image != undefined) {
                            for (var i = 0; i < data.image.length; i++) {
                                template.screen.image[i].areaId = data.image[i].area_id;
                            }
                            
                        }
                        
                        if (data.text != undefined) {
                            template.screen.text.areaId = data.text.area_id;
                        }
                        
                        if (data.staticText != undefined) {
                            template.screen.staticText.areaId = data.staticText.area_id;
                        }
                        
                        if (data.date != undefined) {
                            template.screen.date.areaId = data.date.area_id;
                        }
                        
                        if (data.time != undefined) {
                            template.screen.time.areaId = data.time.area_id;
                        }
                        
                        if (data.weather != undefined) {
                            template.screen.weather.areaId = data.weather.area_id;
                        }
                        
                        if (data.interaction != undefined) {
                            template.screen.interaction.areaId = data.interaction.area_id;
                        }
                        
                        if (data.webpage != undefined) {
                            template.screen.webpage.areaId = data.webpage.area_id;
                        }
                        
                        if (data.logo != undefined) {
                            template.screen.logo.areaId = data.logo.area_id;
                        }
                    }
                    else {
                        showMsg(data.msg, 'error');
                    }
                    saveButton.removeClass('btn-02');
                    saveButton.addClass('btn-01');
                    saveButton.bind('click', function(){
                        template.screen.save();
                    });
                    window.location.href="/template/index";
                }, 'json').error(function(){
                    showMsg(requestFail, 'error');
                    saveButton.removeClass('btn-02');
                    saveButton.addClass('btn-01');
                    saveButton.bind('click', function(){
                        template.screen.save();
                    });
                });

            }
            
            return false;
        },
        
    }
}