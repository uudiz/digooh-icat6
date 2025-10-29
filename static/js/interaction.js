var interaction = {
	index: {
        swfu: null,
        sessionId: null,
    },
	doSave : function(){
		var name = $('#name').val();
		var screen = $('#screen').val();
		var descr = $('#descr').val();
		var id = $('#id').val();
		if(id == undefined) {
			id = 0;
		}
		
		$.post('/interaction/doSave', {
			name : name,
			screen : screen,
			descr  : descr,
			id: id
		}, function(data){
			if(data.code == 0){
				showFormMsg(data.msg,'success');
				tb_remove();
				setTimeout(function(){
					//interaction.refresh();
					window.location.href='interaction/create_interaction_date?id='+data.id;
				}, 800);
			}else{
				showFormMsg(data.msg, 'error');
			}
		},'json');
	},
	doEdit : function(){
		var name = $('#name').val();
		var screen = $('#screen').val();
		var descr = $('#descr').val();
		var id = $('#id').val();
		if(id == undefined) {
			id = 0;
		}
		
		$.post('/interaction/doSave', {
			name : name,
			screen : screen,
			descr  : descr,
			id: id
		}, function(data){
			if(data.code == 0){
				showFormMsg(data.msg,'success');
				tb_remove();
				setTimeout(function(){
					interaction.refresh();
				}, 800);
			}else{
				showFormMsg(data.msg, 'error');
			}
		},'json');
	},
	refresh : function(){
		showLoading();
		$.get('/interaction/refresh?t='+new Date().getTime(), function(data){
			$('.wrap').html(data);
			hideLoading();
			tb_init('a.thickbox, area.thickbox, input.thickbox');
		});
	},
	page : function(curpage, orderItem, order){
		showLoading();
		window.location.href='/interaction/index/'+curpage+"/"+orderItem+"/"+order;
	},
	remove: function(id, msg) {
		if(confirm(msg)){
			var req = {
					  id:id
					  }
			$.post('/interaction/do_delete',req, function(data){
				if(data.code == 0){
					showMsg(data.msg,'success');
					interaction.refresh();
					setTimeout(hideMsg, 1000);
				}else{
					showMsg(data.msg, 'error');
				}
			},'json');
		}
	},
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
	            
	     	$('#'+id).css('width', width);
			$('#areaWidth').attr('value', 2 * width);
			$('#areaWidthPercent').attr('value', Math.round(width / t_width * 10000) / 100.00);
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
	       	$('#'+id).css('height', height);
	      	$('#'+id+' dd').css('height', height - 20);
			$('#areaHeight').attr('value', 2 * height);
			$('#areaHeightPercent').attr('value', Math.round(height / t_height * 10000) / 100.00);
		} 
    },
	//屏幕页面
    screen: {
		curObj: null,
        id: 0,//当前模板的ID，唯一表示数据
        tabIndex: 1,
        bgimg: new Array(),
        movie: new Array(),
        image: new Array(),
        text: new Array(),
        staticText: new Array(),
        date: new Array(),
        time: new Array(),
        weather: new Array(),
        btn: new Array(),
        webpage: new Array(),
        btnPage: null,
        btnGroup: null,
        logo: null,
        zTree: null, 
        rMenu: null, 
        newMenu:　null,
        treeNodeCount: 2, //树形结构节点唯一编号
        screenID: 2,//当前点击的screen Id
        imageNum: 0,
        movieNum: 0,
        textNum: 0,
        StextNum: 0,
        dateNum: 0,
        weatherNum: 0,
        webpageNum: 0,
        btnNum: 0,
        btnPageNum: 0,
        btnGroupNum: 0,
        timeNum: 0,
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
        enableDeleteButton: true, //是否允许删除Button
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
        //debug: true,
        goTemplate: function(type){
            tb_remove();
            window.location.href = '/interaction/index?type=' + type + '&t=' + new Date().getTime();
        },
        init: function(){
			//init logo size
			for(var i =0; i < interaction.screen.logoRealSize.length; i++) {
				interaction.screen.logoSize[i] = interaction.screen.width * interaction.screen.logoRealSize[i] / interaction.screen.realWidth;
			}
			//init date、weather size
			for(var i =0; i < interaction.screen.DateWeatherRealSize.length; i++) {
				interaction.screen.DateWeatherSize[i] = interaction.screen.width * interaction.screen.DateWeatherRealSize[i] / interaction.screen.realWidth;
			}
			
        	//init text min
			interaction.screen.defaultTextHeight=interaction.screen.height * interaction.screen.defaultTextRealHeight / interaction.screen.realHeight;
			interaction.screen.minTextWidth=interaction.screen.width * interaction.screen.minTextRealWidth / interaction.screen.realWidth;
			interaction.screen.minTextHeight=interaction.screen.height * interaction.screen.minTextRealHeight / interaction.screen.realHeight;
			
            // init image min width & height
            interaction.screen.minImageWidth = interaction.screen.width > interaction.screen.height ? (interaction.screen.width * interaction.screen.minImageRealWidth / interaction.screen.realWidth) : (interaction.screen.height * interaction.screen.minImageRealHeight / interaction.screen.realHeight);
            interaction.screen.minImageHeight = interaction.screen.width > interaction.screen.height ? (interaction.screen.height * interaction.screen.minImageRealHeight / interaction.screen.realHeight) : (interaction.screen.width * interaction.screen.minImageRealWidth / interaction.screen.realWidth);
            
			
            // init video min width & height
            interaction.screen.minVideoWidth = interaction.screen.width > interaction.screen.height ? (interaction.screen.width * interaction.screen.minVideoRealWidth / interaction.screen.realWidth) : (interaction.screen.height * interaction.screen.minVideoRealHeight / interaction.screen.realHeight);
            interaction.screen.minVideoHeight = interaction.screen.width > interaction.screen.height ? (interaction.screen.height * interaction.screen.minVideoRealHeight / interaction.screen.realHeight) : (interaction.screen.width * interaction.screen.minVideoRealWidth / interaction.screen.realWidth);
            
            interaction.screen.gridX = interaction.screen.width > interaction.screen.height ? (4 * interaction.screen.width) / interaction.screen.realWidth : (2 * interaction.screen.height) / interaction.screen.realHeight;
            interaction.screen.gridY = interaction.screen.width > interaction.screen.height ? (2 * interaction.screen.height) / interaction.screen.realHeight : (4 * interaction.screen.width) / interaction.screen.realWidth;
            
            // init date width& height
            interaction.screen.minDateWidth = true ? (interaction.screen.width * interaction.screen.minDateRealWidth / interaction.screen.realWidth) : (interaction.screen.height * interaction.screen.minDateRealHeight / interaction.screen.realHeight);
            interaction.screen.minDateHeight = true ? (interaction.screen.height * interaction.screen.minDateRealHeight / interaction.screen.realHeight) : (interaction.screen.width * interaction.screen.minDateRealWidth / interaction.screen.realWidth);
            interaction.screen.maxDateWidth = true ? (interaction.screen.width * interaction.screen.maxDateRealWidth / interaction.screen.realWidth) : (interaction.screen.height * interaction.screen.maxDateRealHeight / interaction.screen.realHeight);
            interaction.screen.maxDateHeight = true ? (interaction.screen.height * interaction.screen.maxDateRealHeight / interaction.screen.realHeight) : (interaction.screen.width * interaction.screen.maxDateRealWidth / interaction.screen.realWidth);
            
            // init date width& height
            interaction.screen.minWeatherWidth = true ? (interaction.screen.width * interaction.screen.minWeatherRealWidth / interaction.screen.realWidth) : (interaction.screen.height * interaction.screen.minWeatherRealHeight / interaction.screen.realHeight);
            interaction.screen.minWeatherHeight = true ? (interaction.screen.height * interaction.screen.minWeatherRealHeight / interaction.screen.realHeight) : (interaction.screen.width * interaction.screen.minWeatherRealWidth / interaction.screen.realWidth);
            interaction.screen.maxWeatherWidth = true ? (interaction.screen.width * interaction.screen.maxWeatherRealWidth / interaction.screen.realWidth) : (interaction.screen.height * interaction.screen.maxWeatherRealHeight / interaction.screen.realHeight);
            interaction.screen.maxWeatherHeight = true ? (interaction.screen.height * interaction.screen.maxWeatherRealHeight / interaction.screen.realHeight) : (interaction.screen.width * interaction.screen.maxWeatherRealWidth / interaction.screen.realWidth);
            
            if (interaction.screen.debug) {
                console.info("init minVideoWidth" + interaction.screen.minVideoWidth + ", minVideoHeight:" + interaction.screen.minVideoHeight);
                console.info("init minImageWidth" + interaction.screen.minImageWidth + ", minImageHeight:" + interaction.screen.minImageHeight);
                console.info("init gridX" + interaction.screen.gridX + ", gridY:" + interaction.screen.gridY);
            }
            //初始化屏幕的高度和宽度
			if(interaction.screen.readonly){
				return;
			}
            $('#bg').click(function(event){
                interaction.screen.addBg();
            });
            
            $('#movie').click(function(event){
                interaction.screen.addMovie();
            });
            
            $('#image1').click(function(event){
                interaction.screen.addImage(1);
            });
            
			$('#image2').click(function(event){
                interaction.screen.addImage(2);
            });
            
			$('#image3').click(function(event){
                interaction.screen.addImage(3);
            });
            
            $('#image4').click(function(event){
                interaction.screen.addImage(4);
            });
            
            $('#text').click(function(event){
                interaction.screen.addText();
            });
            
            $('#staticText').click(function(event){
                interaction.screen.addStaticText();
            });
            
            $('#date').click(function(event){
                interaction.screen.addDate();
            });
            
            $('#time').click(function(event){
                interaction.screen.addTime();
            });
            
            $('#logo').click(function(event){
                interaction.screen.addLogo();
            });
            
            $('#weather').click(function(event){
                interaction.screen.addWeather();
            });
            
            $('#webpage').click(function(event){
                interaction.screen.addWebpage();
            });
            
            $('#bton').click(function(event){
                interaction.screen.addBtn();
            });
            
            $('#btnGroup').click(function(event){
                interaction.screen.addTreeBtnGroup();
            });
            
            $('#save').click(function(event){
                interaction.screen.save();
            });
        },
        initTree: function(znodes, screenID, treeNodeCount) {
        	var setting = {
				view: {
					dblClickExpand: false,
				},
				data: {
					simpleData: {
						enable: true
					}
				},
				check: {
					enable: false
				},
				callback: {
					onRightClick: interaction.screen.OnRightClick,
					onClick: interaction.screen.onClick
				}
			};
			interaction.screen.screenID = screenID;
			interaction.screen.treeNodeCount = treeNodeCount;
			var zNodes = znodes;
			$.fn.zTree.init($("#tree"), setting, zNodes);
			interaction.screen.zTree = $.fn.zTree.getZTreeObj("tree");
			interaction.screen.rMenu = $("#rMenu");
			interaction.screen.newMenu = $("#newMenu");
			$('#centerframe').append("<div style='width: "+interaction.screen.width+"px; height: "+interaction.screen.height+"px; background-color: rgb(255, 255, 255);' class='gray-area' id='screen2'><img id='screenbg2' style='position: absolute; top: 0px; left:0px; z-index:1;' width='0' height='0' /></div>");
        	$('#centerframe div').css('display', 'none');
        	$('#screen2').css('display', 'block');
			var div_id = 3;
        	if(treeNodeCount > 2) {
        		do{
        			$('#centerframe').append("<div style='width: "+interaction.screen.width+"px; height: "+interaction.screen.height+"px; background-color: rgb(255, 255, 255);display: none;' class='gray-area' id='screen" + div_id + "'><img id='screenbg"+div_id+"' style='position: absolute; top: 0px; left:0px; z-index:1;' width='0' height='0' /></div>");
        			div_id++;
        		}while(div_id < treeNodeCount);
        	}

        	$('#tree_1_a').addClass("curSelectedNode");
        	$('#tree_1_a').click();
        },
        OnRightClick: function(event, treeId, treeNode) {
        	if(treeNode.iconSkin != 'mainPage' && treeNode.iconSkin != 'page' && treeNode.iconSkin !='touch') {
        		interaction.screen.onClick(event, treeId, treeNode);
        	}
        	if(treeNode.iconSkin == 'mainPage' || treeNode.iconSkin == 'page') {
        		$('#centerframe div').css('display', 'none');
        		$('div.icon').css('display', 'block');
        		$('div.ui-resizable-handle').css('display', 'block');
        		interaction.screen.screenID = treeNode.id;
        		$('#screen'+treeNode.id).css('display', 'block');
        	}
        	$('a').removeClass('curSelectedNode');
        	if (!treeNode && event.target.tagName.toLowerCase() != "button" && $(event.target).parents("a").length == 0) {
				interaction.screen.zTree.cancelSelectedNode();
				interaction.screen.showRMenu("root", event.clientX, event.clientY, treeNode);
			} else if (treeNode && !treeNode.noR) {
				interaction.screen.zTree.selectNode(treeNode);
				interaction.screen.showRMenu("node", event.clientX, event.clientY, treeNode);
			} else if(treeNode.noR) {
				interaction.screen.zTree.selectNode(treeNode);
				//interaction.screen.showRMenu2("node", event.clientX, event.clientY, treeNode);
			}
        },
        onClick: function(event, treeId, treeNode) {
        	if(treeNode.iconSkin == 'mainPage' || treeNode.iconSkin == 'page' || treeNode.iconSkin == 'touch' || treeNode.iconSkin == 'folder') {
        		interaction.screen.screenID = treeNode.id;
        	}else {
        		interaction.screen.screenID = treeNode.getParentNode().id;
        	}
        	$('#centerframe div').css('display', 'none');
        	$('div.icon').css('display', 'block');
        	$('div.ui-resizable-handle').css('display', 'block');

        	switch(treeNode.iconSkin) {
        		case 'touch':
        			$('#div_touch').css('display', 'block');
        			$('#screen2').css('display', 'block');
        			$('#div_btn').css('display', 'none');
        			interaction.screen.disable('bg');
        			interaction.screen.disable('movie');
		        	interaction.screen.disable('image1');
		        	interaction.screen.disable('image2');
		        	interaction.screen.disable('image3');
		        	interaction.screen.disable('image4');
		        	interaction.screen.disable('time');
		        	interaction.screen.disable('date');
		        	interaction.screen.disable('text');
		        	interaction.screen.disable('staticText');
		        	interaction.screen.disable('webpage');
		        	interaction.screen.disable('weather');
		        	interaction.screen.disable('bton');
		        	interaction.screen.disable('btnGroup');
					$('.icon-list li').addClass('disable');
					break;
        		case 'folder':
        			$('#div_touch').css('display', 'none');
        			$('#screen2').css('display', 'block');
        			$('#div_btn').css('display', 'none');
        			interaction.screen.disable('bg');
        			interaction.screen.disable('movie');
		        	interaction.screen.disable('image1');
		        	interaction.screen.disable('image2');
		        	interaction.screen.disable('image3');
		        	interaction.screen.disable('image4');
		        	interaction.screen.disable('time');
		        	interaction.screen.disable('date');
		        	interaction.screen.disable('text');
		        	interaction.screen.disable('staticText');
		        	interaction.screen.disable('webpage');
		        	interaction.screen.disable('weather');
		        	interaction.screen.disable('bton');
		        	interaction.screen.disable('btnGroup');
					$('.icon-list li').addClass('disable');
        			break;
        		case 'mainPage':
        			$('#div_touch').css('display', 'none');
        			$('#screen'+treeNode.id).css('display', 'block');
        			$('#div_btn').css('display', 'none');
        			interaction.screen.treedisable(treeNode.children);
        			break;
        		case 'page':
        			$('#div_touch').css('display', 'none');
        			$('#screen'+treeNode.id).css('display', 'block');
        			$('#div_div').css('display', 'none');
        			interaction.screen.treedisable(treeNode.children);
        			break;
        		case 'btnGroup':
        		case 'btnPage':
        		case 'movie':
        		case 'image':
        		case 'text':
        		case 'stext':
        		case 'date':
        		case 'time':
        		case 'weather':
        		case 'webtab':
        			$('#div_touch').css('display', 'none');
        			$('#screen'+treeNode.getParentNode().id).css('display', 'block');
        			$('#div_page').css('display', 'none');
        			$('#div_btn').css('display', 'none');
        			var obj = $.fn.zTree.getZTreeObj("tree");
        			var node = obj.getNodeByTId('tree_'+treeNode.getParentNode().id);
        			interaction.screen.treedisable(node.children);
        			break;
        		case 'btn':
        			$('#div_touch').css('display', 'none');
        			$('#screen'+treeNode.getParentNode().id).css('display', 'block');
        			$('#div_page').css('display', 'none');
        			$('#div_btn').css('display', 'block');
        			var obj = $.fn.zTree.getZTreeObj("tree");
        			var node = obj.getNodeByTId('tree_'+treeNode.getParentNode().id);
        			interaction.screen.treedisable(node.children);
        			break;
        		default:
        		break;
        	}
        },
        add_click: function(treeId, treeNode) {
        	if(treeNode.iconSkin == 'mainPage' || treeNode.iconSkin == 'page' || treeNode.iconSkin == 'touch' || treeNode.iconSkin == 'folder') {
        		interaction.screen.screenID = treeNode.id;
        	}else {
        		interaction.screen.screenID = treeNode.getParentNode().id;
        	}
        	$('#centerframe div').css('display', 'none');
        	$('div.icon').css('display', 'block');
        	$('div.ui-resizable-handle').css('display', 'block');
        	$('a').removeClass('curSelectedNode');
        	$('#tree_'+treeNode.id+'_a').addClass('curSelectedNode');
        	switch(treeNode.iconSkin) {
        		case 'touch':
        		case 'folder':
        			interaction.screen.disable('bg');
        			interaction.screen.disable('movie');
		        	interaction.screen.disable('image1');
		        	interaction.screen.disable('image2');
		        	interaction.screen.disable('image3');
		        	interaction.screen.disable('image4');
		        	interaction.screen.disable('time');
		        	interaction.screen.disable('date');
		        	interaction.screen.disable('text');
		        	interaction.screen.disable('staticText');
		        	interaction.screen.disable('webpage');
		        	interaction.screen.disable('weather');
		        	interaction.screen.disable('bton');
		        	interaction.screen.disable('btnGroup');
					$('.icon-list li').addClass('disable');
        			break;
        		case 'mainPage':
        			$('#screen'+treeNode.id).css('display', 'block');
        			interaction.screen.treedisable(treeNode.children);
        			break;
        		case 'page':
        			$('#screen'+treeNode.id).css('display', 'block');
        			interaction.screen.treedisable(treeNode.children);
        			break;
        		case 'btnGroup':
        		case 'btnPage':
        		case 'btn':
        		case 'movie':
        		case 'image':
        		case 'text':
        		case 'stext':
        		case 'date':
        		case 'time':
        		case 'weather':
        		case 'webtab':
        			$('#screen'+treeNode.getParentNode().id).css('display', 'block');
        			var obj = $.fn.zTree.getZTreeObj("tree");
        			var node = obj.getNodeByTId('tree_'+treeNode.getParentNode().id);
        			interaction.screen.treedisable(node.children);
        			break;
        		default:
        		break;
        	}
        },
        treedisable: function(children) {
        	var image_num = 0;
        	$('.icon-list li').removeClass('disable');
        	interaction.screen.enable('movie');
        	interaction.screen.enable('image1');
        	interaction.screen.enable('image2');
        	interaction.screen.enable('image3');
        	interaction.screen.enable('image4');
        	interaction.screen.enable('time');
        	interaction.screen.enable('date');
        	interaction.screen.enable('text');
        	interaction.screen.enable('staticText');
        	interaction.screen.enable('webpage');
        	interaction.screen.enable('weather');
        	interaction.screen.enable('bton');
        	interaction.screen.enable('btnGroup');
        	interaction.screen.enable('bg');
        	$('#screen'+interaction.screen.screenID+' dl').each(function() {
    			var _id = $(this).attr("id");
    			if(_id.indexOf("area_movie") >= 0) {
    				interaction.screen.disable('movie');
    			}
    			if(_id.indexOf("area_date") >= 0) {
    				interaction.screen.disable('date');
    			}
    			if(_id.indexOf("area_time") >= 0) {
    				interaction.screen.disable('time');
    			}
    			if(_id.indexOf("area_weather") >= 0) {
    				interaction.screen.disable('weather');
    			}
    			if(_id.indexOf("area_webpage") >= 0) {
    				interaction.screen.disable('webpage');
    			}
    			if(_id.indexOf("area_text") >= 0) {
    				interaction.screen.disable('text');
    			}
    			if(_id.indexOf("area_staticText") >= 0) {
    				interaction.screen.disable('staticText');
    			}
    			if(_id.indexOf("area_image") >= 0) {
    				var tmp = _id.split('_');
    				image_num = tmp[3];
    				interaction.screen.disable('image'+image_num);
    			}
			});
        },
        showRMenu: function(type, x, y, treeNode) {
        	var myobj = eval(treeNode.children);
			
			if(treeNode.id==2) {
				$("#m_del").hide();
				$("#m_copy").hide();
			}else {
				$("#m_del").show();
				$("#m_copy").show();
			}

			x = x + 20;
			interaction.screen.rMenu.css({"top":y+"px", "left":x+"px", "visibility":"visible"});

			$("body").bind("mousedown", interaction.screen.onBodyMouseDown);
        },
        showRMenu2: function(type, x, y, treeNode) {
        	if(treeNode.open == true) {
				$("#m_expand").show();
				$("#m_collapse").hide();
			}else {
				$("#m_expand").hide();
				$("#m_collapse").show();
			}
			$("#m_folder").hide();
			$("#m_page").show();
			$("#m_group").hide();
			$("#m_btnPage").hide();
			$("#m_btn").hide();
			$("#m_movie").hide();
			$("#m_image").hide();
			$("#m_text").hide();
			$("#m_sText").hide();
			$("#m_date").hide();
			$("#m_time").hide();
			$("#m_weather").hide();
			$("#m_webPage").hide();
			$("#m_del").hide();
			$("#m_copy").hide();
			x = x + 20;
			interaction.screen.rMenu.css({"top":y+"px", "left":x+"px", "visibility":"visible"});

			$("body").bind("mousedown", interaction.screen.onBodyMouseDown);
        },
        //判断 模块区域中  指定模块的数量
        judgeNum: function(obj, name) {
        	var myobj = eval(obj);
        	var num = 0;
        	for(var i=0; i<myobj.length; i++) {
        		if(obj[i].iconSkin == name) {
        			num++;
        		}
        	}
        	return num;
        },
        //判断 模块区域中  各类模块的数量
        judgeAllNum: function(obj) {
        	interaction.screen.movieNum = 0;
        	interaction.screen.imageNum = 0;
        	var num = 0;
        	for(var i=0; i<obj.length; i++) {
        		if(obj[i].iconSkin == 'movie') {
        			interaction.screen.movieNum++;
        		}
        		if(obj[i].iconSkin == 'image') {
        			interaction.screen.imageNum++;
        		}
        		if(obj[i].iconSkin == 'text') {
        			interaction.screen.textNum++;
        		}
        		if(obj[i].iconSkin == 'stext') {
        			interaction.screen.StextNum++;
        		}
        		if(obj[i].iconSkin == 'webtab') {
        			interaction.screen.webpageNum++;
        		}
        		if(obj[i].iconSkin == 'date') {
        			interaction.screen.dateNum++;
        		}
        		if(obj[i].iconSkin == 'weather') {
        			interaction.screen.weatherNum++;
        		}
        		if(obj[i].iconSkin == 'btnPage') {
        			interaction.screen.btnPageNum++;
        		}
        		if(obj[i].iconSkin == 'time') {
        			interaction.screen.timeNum++;
        		}
        		if(obj[i].iconSkin == 'btn') {
        			interaction.screen.btnNum++;
        		}
        		if(obj[i].iconSkin == 'btnGroup') {
        			interaction.screen.btnGroupNum++;
        		}
        	}
        },
        //添加目录
        addTreeFolder: function() {
        	interaction.screen.hideRMenu();
			var newNode = { name:"Folder" + (interaction.screen.treeNodeCount++), checked:true, iconSkin: "folder", id: (interaction.screen.treeNodeCount-1)};
			var newNode2 = { name:"Page" + (interaction.screen.treeNodeCount++), checked:true, iconSkin: "page", id: (interaction.screen.treeNodeCount-1)};
			interaction.screen.screenID = interaction.screen.treeNodeCount-1;
			if (interaction.screen.zTree.getSelectedNodes()[0]) {
				newNode.checked = interaction.screen.zTree.getSelectedNodes()[0].checked;
				var testNode = interaction.screen.zTree.addNodes(interaction.screen.zTree.getSelectedNodes()[0], newNode); //新添加的节点object
				var test_node = interaction.screen.zTree.getNodeByTId(testNode[0].tId);
				interaction.screen.zTree.addNodes(test_node, newNode2);
				$('#centerframe').append("<div style='width: "+interaction.screen.width+"px; height: "+interaction.screen.height+"px; background-color: rgb(255, 255, 255);' class='gray-area' id='screen" + (interaction.screen.treeNodeCount-1) + "'><img id='screenbg"+interaction.screen.screenID+"' style='position: absolute; top: 0px; left:0px; z-index:1;' width='0' height='0' /></div>");
				$('#centerframe div').css('display', 'none');
        		$('#screen' + (interaction.screen.treeNodeCount-1)).css('display', 'block');
        		interaction.screen.screenID = interaction.screen.treeNodeCount-1;
				$('a').removeClass('curSelectedNode');
			}
        },
        //添加页
        addTreePage: function() {
        	interaction.screen.hideRMenu();
			var newNode = { name:"Page" + (interaction.screen.treeNodeCount-1), iconSkin: "page", id: interaction.screen.treeNodeCount};
			if (interaction.screen.zTree.getSelectedNodes()[0]) {
				newNode.checked = interaction.screen.zTree.getSelectedNodes()[0].checked;
				interaction.screen.zTree.addNodes($.fn.zTree.getZTreeObj("tree").getNodeByTId('tree_1'), newNode);
				//添加 Page 模板页面
				$('#centerframe div').css('display', 'none');
        		$('#screen' + interaction.screen.treeNodeCount).css('display', 'block');
        		interaction.screen.screenID = interaction.screen.treeNodeCount;
        		$('#centerframe').append("<div style='width: "+interaction.screen.width+"px; height: "+interaction.screen.height+"px; background-color: rgb(255, 255, 255);' class='gray-area' id='screen" + interaction.screen.treeNodeCount + "'><img id='screenbg"+interaction.screen.screenID+"' style='position: absolute; top: 0px; left:0px; z-index:1;' width='0' height='0' /></div>");
        		$('a').removeClass('curSelectedNode');
        		$("#tree_"+newNode.id+'_a').addClass("curSelectedNode");
        		$("#tree_"+newNode.id+'_a').click();
        		interaction.screen.treeNodeCount++
			}
        },
        //添加Button组
        addTreeBtnGroup: function() {
        	interaction.screen.hideRMenu();
			var newNode = { name:"ButtonGroup" + (interaction.screen.treeNodeCount++), checked:true, iconSkin: "btnGroup", id: (interaction.screen.treeNodeCount-1)};
			var newNode2 = { name:"ButtonPage" + (interaction.screen.treeNodeCount++), checked:true, iconSkin: "btnPage", id: (interaction.screen.treeNodeCount-1)};
			var newNode3 = [{name:"Button1", checked:true, iconSkin: "btn"},{name:"Button2", checked:true, iconSkin: "btn"},{name:"Button3", checked:true, iconSkin: "btn"},{name:"Button4", checked:true, iconSkin: "btn"},{name:"Button5", checked:true, iconSkin: "btn"},{name:"Button6", checked:true, iconSkin: "btn"}];
			if (interaction.screen.zTree.getSelectedNodes()[0]) {
				newNode.checked = interaction.screen.zTree.getSelectedNodes()[0].checked;
				var pageNode = interaction.screen.zTree.addNodes(interaction.screen.zTree.getSelectedNodes()[0], newNode); //新添加的节点object
				var btnPage = interaction.screen.zTree.getNodeByTId(pageNode[0].tId);
				var btnNode = interaction.screen.zTree.addNodes(btnPage, newNode2);
				var btn = interaction.screen.zTree.getNodeByTId(btnNode[0].tId);
				interaction.screen.zTree.addNodes(btn, newNode3);
				interaction.screen.addBtnGroup();
			}
        },
        //添加Button页
        addTreeBtnPage: function() { 
        	interaction.screen.hideRMenu();
			var newNode = { name:"ButtonPage" + (interaction.screen.treeNodeCount++), checked:true, iconSkin: "btnPage", id: (interaction.screen.treeNodeCount-1)};
			var newNode2 = [{name:"Button1", checked:true, iconSkin: "btn"},{name:"Button2", checked:true, iconSkin: "btn"},{name:"Button3", checked:true, iconSkin: "btn"},{name:"Button4", checked:true, iconSkin: "btn"},{name:"Button5", checked:true, iconSkin: "btn"},{name:"Button6", checked:true, iconSkin: "btn"}];
			if (interaction.screen.zTree.getSelectedNodes()[0]) {
				newNode.checked = interaction.screen.zTree.getSelectedNodes()[0].checked;
				var testNode = interaction.screen.zTree.addNodes(interaction.screen.zTree.getSelectedNodes()[0], newNode); //新添加的节点object
				var test_node = interaction.screen.zTree.getNodeByTId(testNode[0].tId);
				interaction.screen.zTree.addNodes(test_node, newNode2);
			}
        },
        //添加Button
        addTreeBtn: function() {
        	interaction.screen.hideRMenu();
        	interaction.screen.treeNodeCount++;
			var newNode = { name:"Button", checked:true, iconSkin: "btn", id: (interaction.screen.treeNodeCount-1)};
			if (interaction.screen.zTree.getSelectedNodes()[0]) {
				newNode.checked = interaction.screen.zTree.getSelectedNodes()[0].checked;
				//interaction.screen.zTree.addNodes($.fn.zTree.getZTreeObj("tree").getNodeByTId('tree_'+interaction.screen.screenID), newNode);
				//判断该区域  视频区 的数量
				$('#tree li a').removeClass('curSelectedNode');
				$('#tree_'+newNode.id+'_a').addClass('curSelectedNode');
				
				var childrens = $.fn.zTree.getZTreeObj("tree").getNodeByTId('tree_'+interaction.screen.screenID).children;
				var btn_num = interaction.screen.judgeNum(childrens, 'btn');
				interaction.screen.addBtn(btn_num);
			}
        },
        //添加视频
        addTreeMovie: function() {
        	interaction.screen.hideRMenu();
			if (interaction.screen.zTree.getSelectedNodes()[0]) {
				if(interaction.screen.zTree.getSelectedNodes()[0].children == undefined || interaction.screen.judgeNum(interaction.screen.zTree.getSelectedNodes()[0].children, 'movie')<1) {
					interaction.screen.treeNodeCount++;
					var newNode = { name:"Movie", checked:true, iconSkin: "movie", id: (interaction.screen.treeNodeCount-1)};
					newNode.checked = interaction.screen.zTree.getSelectedNodes()[0].checked;
					interaction.screen.zTree.addNodes($.fn.zTree.getZTreeObj("tree").getNodeByTId('tree_'+interaction.screen.screenID), newNode);
					//判断该区域  视频区 的数量
					var childrens = interaction.screen.zTree.getSelectedNodes()[0].children;
					$('#tree li a').removeClass('curSelectedNode');
					$('#tree_'+newNode.id+'_a').addClass('curSelectedNode');
					interaction.screen.addMovie(newNode.id);
				}
			}
        },
        //添加图片
        addTreeImage: function() {
        	interaction.screen.hideRMenu();
			if (interaction.screen.zTree.getSelectedNodes()[0]) {
				if(interaction.screen.zTree.getSelectedNodes()[0].children == undefined || interaction.screen.judgeNum(interaction.screen.zTree.getSelectedNodes()[0].children, 'image')<4) {
					interaction.screen.treeNodeCount++;
					var newNode = { name:"Image", checked:true, iconSkin: "image", id: (interaction.screen.treeNodeCount-1)};
					newNode.checked = interaction.screen.zTree.getSelectedNodes()[0].checked;
					interaction.screen.zTree.addNodes($.fn.zTree.getZTreeObj("tree").getNodeByTId('tree_'+interaction.screen.screenID), newNode);
					$('#tree li a').removeClass('curSelectedNode');
					$('#tree_'+newNode.id+'_a').addClass('curSelectedNode');
					//判断该区域  视频区 的数量
					var childrens = $.fn.zTree.getZTreeObj("tree").getNodeByTId('tree_'+interaction.screen.screenID).children;
					var img_num = interaction.screen.judgeNum(childrens, 'image');
					interaction.screen.addImage(newNode.id, img_num);
				}
			}
        },
        //添加静态文字
        addTreeStext: function() {
        	interaction.screen.hideRMenu();
			if (interaction.screen.zTree.getSelectedNodes()[0]) {
				if(interaction.screen.zTree.getSelectedNodes()[0].children == undefined || interaction.screen.judgeNum(interaction.screen.zTree.getSelectedNodes()[0].children, 'stext')<1) {
					interaction.screen.treeNodeCount++;
					var newNode = { name:"StaticText", checked:true, iconSkin: "stext", id: (interaction.screen.treeNodeCount-1)};
					newNode.checked = interaction.screen.zTree.getSelectedNodes()[0].checked;
					interaction.screen.zTree.addNodes($.fn.zTree.getZTreeObj("tree").getNodeByTId('tree_'+interaction.screen.screenID), newNode);
					$('#tree li a').removeClass('curSelectedNode');
					$('#tree_'+newNode.id+'_a').addClass('curSelectedNode');
					interaction.screen.addStaticText(newNode.id);
				}
			}
        },
        //添加文字
        addTreeText: function() {
        	interaction.screen.hideRMenu();
			if (interaction.screen.zTree.getSelectedNodes()[0]) {
				if(interaction.screen.zTree.getSelectedNodes()[0].children == undefined || interaction.screen.judgeNum(interaction.screen.zTree.getSelectedNodes()[0].children, 'text')<1) {
					interaction.screen.treeNodeCount++;
					var newNode = { name:"Text", checked:true, iconSkin: "text", id: (interaction.screen.treeNodeCount-1)};
					newNode.checked = interaction.screen.zTree.getSelectedNodes()[0].checked;
					interaction.screen.zTree.addNodes($.fn.zTree.getZTreeObj("tree").getNodeByTId('tree_'+interaction.screen.screenID), newNode);
					$('#tree li a').removeClass('curSelectedNode');
					$('#tree_'+newNode.id+'_a').addClass('curSelectedNode');
					interaction.screen.addText(newNode.id);
				}
			}
        },
        //添加日期
        addTreeDate: function() {
        	interaction.screen.hideRMenu();
			if (interaction.screen.zTree.getSelectedNodes()[0]) {
				if(interaction.screen.zTree.getSelectedNodes()[0].children == undefined || interaction.screen.judgeNum(interaction.screen.zTree.getSelectedNodes()[0].children, 'date')<1) {
					interaction.screen.treeNodeCount++;
					var newNode = { name:"Date", checked:true, iconSkin: "date", id: (interaction.screen.treeNodeCount-1)};
					newNode.checked = interaction.screen.zTree.getSelectedNodes()[0].checked;
					interaction.screen.zTree.addNodes($.fn.zTree.getZTreeObj("tree").getNodeByTId('tree_'+interaction.screen.screenID), newNode);
					$('#tree li a').removeClass('curSelectedNode');
					$('#tree_'+newNode.id+'_a').addClass('curSelectedNode');
					interaction.screen.addDate(newNode.id);
				}
			}
        },
        //添加时间
        addTreeTime: function() {
        	interaction.screen.hideRMenu();
			if (interaction.screen.zTree.getSelectedNodes()[0]) {
				if(interaction.screen.zTree.getSelectedNodes()[0].children == undefined || interaction.screen.judgeNum(interaction.screen.zTree.getSelectedNodes()[0].children, 'time')<1) {
					interaction.screen.treeNodeCount++;
					var newNode = { name:"Time", checked:true, iconSkin: "time", id: (interaction.screen.treeNodeCount-1)};
					newNode.checked = interaction.screen.zTree.getSelectedNodes()[0].checked;
					interaction.screen.zTree.addNodes($.fn.zTree.getZTreeObj("tree").getNodeByTId('tree_'+interaction.screen.screenID), newNode);
					$('#tree li a').removeClass('curSelectedNode');
					$('#tree_'+newNode.id+'_a').addClass('curSelectedNode');
					interaction.screen.addTime(newNode.id);
				}
			}
        },
        //添加天气
        addTreeWeather: function() {
        	interaction.screen.hideRMenu();
			if (interaction.screen.zTree.getSelectedNodes()[0]) {
				if(interaction.screen.zTree.getSelectedNodes()[0].children == undefined || interaction.screen.judgeNum(interaction.screen.zTree.getSelectedNodes()[0].children, 'weather')<1) {
					interaction.screen.treeNodeCount++;
					var newNode = { name:"Weather", checked:true, iconSkin: "weather", id: (interaction.screen.treeNodeCount-1)};
					newNode.checked = interaction.screen.zTree.getSelectedNodes()[0].checked;
					interaction.screen.zTree.addNodes($.fn.zTree.getZTreeObj("tree").getNodeByTId('tree_'+interaction.screen.screenID), newNode);
					$('#tree li a').removeClass('curSelectedNode');
					$('#tree_'+newNode.id+'_a').addClass('curSelectedNode');
					interaction.screen.addWeather(newNode.id);
				}
			}
        },
        //添加网页
        addTreeWebtab: function() {
        	interaction.screen.hideRMenu();
			if (interaction.screen.zTree.getSelectedNodes()[0]) {
				if(interaction.screen.zTree.getSelectedNodes()[0].children == undefined || interaction.screen.judgeNum(interaction.screen.zTree.getSelectedNodes()[0].children, 'webtab')<1) {
					interaction.screen.treeNodeCount++;
					var newNode = { name:"Webpage", checked:true, iconSkin: "webtab", id: (interaction.screen.treeNodeCount-1)};
					newNode.checked = interaction.screen.zTree.getSelectedNodes()[0].checked;
					interaction.screen.zTree.addNodes($.fn.zTree.getZTreeObj("tree").getNodeByTId('tree_'+interaction.screen.screenID), newNode);
					$('#tree li a').removeClass('curSelectedNode');
					$('#tree_'+newNode.id+'_a').addClass('curSelectedNode');
					interaction.screen.addWebpage(newNode.id);
				}
			}
        },
        //删除节点
        removeTreeNode: function(id) {
        	interaction.screen.hideRMenu();
			var nodes = interaction.screen.zTree.getSelectedNodes();
			if (nodes && nodes.length>0) {
				var msg = "Are you sure to remove this page?";
				if (confirm(msg)==true){
					interaction.screen.zTree.removeNode(nodes[0]);
					interaction.screen.treeNodeCount--;
					$('#screen' + nodes[0].id + ' dl').each(function() {
						var _id = $(this).attr("id");
		    			if(_id.indexOf("area_movie") >= 0) {
		    				interaction.screen.removeMovieArea(nodes[0].id);
		    			}
		    			if(_id.indexOf("area_date") >= 0) {
		    				interaction.screen.removeDateArea(nodes[0].id);
		    			}
		    			if(_id.indexOf("area_time") >= 0) {
		    				interaction.screen.removeTimeArea(nodes[0].id);
		    			}
		    			if(_id.indexOf("area_weather") >= 0) {
		    				interaction.screen.removeWeatherArea(nodes[0].id);
		    			}
		    			if(_id.indexOf("area_webpage") >= 0) {
		    				interaction.screen.removeWebpageArea(nodes[0].id);
		    			}
		    			if(_id.indexOf("area_text") >= 0) {
		    				interaction.screen.removeTextArea(nodes[0].id);
		    			}
		    			if(_id.indexOf("area_staticText") >= 0) {
		    				interaction.screen.removeSTextArea(nodes[0].id);
		    			}
		    			if(_id.indexOf("area_image") >= 0) {
		    				var tmp = _id.split('_');
		    				interaction.screen.removeImageArea(nodes[0].id, tmp[3]);
		    			}
		    			if(_id.indexOf("area_btn") >= 0) {
		    				var tmp = _id.split('_');
		    				interaction.screen.removeBtnArea(nodes[0].id, tmp[3]);
		    			}
					});
					//删除表中的数据
					interaction.screen.zTree.selectNode(nodes[0].getParentNode());
					$("#screen"+nodes[0].id).remove();
					$('.tooltip').hide();
					$('#tree_2_a').click();
				}
			}
        },
		//复制节点
		copyTreeNode: function(pid) {
			var nodes = interaction.screen.zTree.getSelectedNodes();
			var old_node_id = nodes[0].id;
			
			var str_movie = 'area_movie_' + nodes[0].id;
			var to_movie = 'area_movie_' + interaction.screen.treeNodeCount;
			var str_time = 'area_time_' + nodes[0].id;
			var to_time = 'area_time_' + interaction.screen.treeNodeCount;
			var str_weather = 'area_weather_' + nodes[0].id;
			var to_weather = 'area_weather_' + interaction.screen.treeNodeCount;
			var str_date = 'area_date_' + nodes[0].id;
			var to_date = 'area_date_' + interaction.screen.treeNodeCount;
			var str_webpage = 'area_webpage_' + nodes[0].id;
			var to_webpage = 'area_webpage_' + interaction.screen.treeNodeCount;
			var str_text = 'area_text_' + nodes[0].id;
			var to_text = 'area_text_' + interaction.screen.treeNodeCount;
			var str_staticText = 'area_staticText_' + nodes[0].id;
			var to_staticText = 'area_staticText_' + interaction.screen.treeNodeCount;
			var str1 = '_' + nodes[0].id + '_'; //area_image_1_1 或area_btn_1_1 替换这两类id
			var str2 = '_' + interaction.screen.treeNodeCount + '_';
			
			interaction.screen.hideRMenu();
			var newNode = { name:"Page" + (interaction.screen.treeNodeCount-1), iconSkin: "page", id: interaction.screen.treeNodeCount};
			if (interaction.screen.zTree.getSelectedNodes()[0]) {
				newNode.checked = interaction.screen.zTree.getSelectedNodes()[0].checked;
				interaction.screen.zTree.addNodes($.fn.zTree.getZTreeObj("tree").getNodeByTId('tree_1'), newNode);
				//添加 Page 模板页面
				$('#centerframe div').css('display', 'none');
        		$('#screen' + interaction.screen.treeNodeCount).css('display', 'block');
        		interaction.screen.screenID = interaction.screen.treeNodeCount;
        		$('#centerframe').append("<div style='width: "+interaction.screen.width+"px; height: "+interaction.screen.height+"px; background-color: rgb(255, 255, 255);' class='gray-area' id='screen" + interaction.screen.treeNodeCount + "'><img id='screenbg"+interaction.screen.treeNodeCount+"' style='position: absolute; top: 0px; left:0px; z-index:1;' width='0' height='0' /></div>");
        		$('a').removeClass('curSelectedNode');
        		$("#tree_"+newNode.id+'_a').addClass("curSelectedNode");
        		$("#tree_"+newNode.id+'_a').click();
        		interaction.screen.treeNodeCount++
			}
			
			var node_id = interaction.screen.treeNodeCount - 1;
			var num = 1;
			var bnum = 1;
			$('#screen' + old_node_id + ' dl').each(function() {
				var x,y,w,h;
				var _id = $(this).attr("id");
		    	if(_id.indexOf("area_movie") >= 0) {
					var id = _id.replace(str_movie, to_movie);
					var parent = $('#' + _id);
					h = parent.innerHeight();
					w = parent.innerWidth();
					x = parent.css('left');
					x = x.substring(0, x.length-2);
                    x =  parseInt(x);
                    y = parent.css('top');
					y = y.substring(0, y.length-2);
                    y = parseInt(y);
					interaction.screen.showMovie(id, node_id ,'Movie/Photo', x, y, w, h, 10);
					interaction.screen.movie.push({
						screenID: node_id,
						id: id
					});
		   		}
		    	if(_id.indexOf("area_date") >= 0) {
					var id = _id.replace(str_date, to_date);
					var parent = $('#' + _id);
					h = parent.innerHeight();
					w = parent.innerWidth();
					x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    x = parseInt(x);
                    y = parent.css('top');
					y = y.substring(0, y.length-2);
                    y = parseInt(y);
					interaction.screen.showDate(id, node_id ,'Date', x, y, w, h, 23);
		    		interaction.screen.date.push({
						screenID: node_id,
						id: id
					});
		   		}
	   			if(_id.indexOf("area_time") >= 0) {
					var id = _id.replace(str_time, to_time);
					var parent = $('#' + _id);
					h = parent.innerHeight();
					w = parent.innerWidth();
					x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    x = parseInt(x);
                    y = parent.css('top');
					y = y.substring(0, y.length-2);
                    y = parseInt(y);
					interaction.screen.showTime(id, node_id ,'Time', x, y, w, h, 22);
					interaction.screen.time.push({
						screenID: node_id,
						id: id
					});
		    	}
		    	if(_id.indexOf("area_weather") >= 0) {
					var id = _id.replace(str_weather, to_weather);
					var parent = $('#' + _id);
					h = parent.innerHeight();
					w = parent.innerWidth();
					x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    x =  parseInt(x);
                    y = parent.css('top');
					y = y.substring(0, y.length-2);
                    y = parseInt(y);
					interaction.screen.showWeather(id, node_id ,'Weather', x, y, w, h, 21);
		    		interaction.screen.weather.push({
						screenID: node_id,
						id: id
					});
		    	}
		    	if(_id.indexOf("area_webpage") >= 0) {
					var id = _id.replace(str_webpage, to_webpage);
					var parent = $('#' + _id);
					h = parent.innerHeight();
					w = parent.innerWidth();
					x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    x =  parseInt(x);
                    y = parent.css('top');
					y = y.substring(0, y.length-2);
                    y = parseInt(y);
					interaction.screen.showWebpage(id, node_id ,'Webpage', x, y, w, h, 20);
		    		interaction.screen.webpage.push({
						screenID: node_id,
						id: id
					});
		    	}
		    	if(_id.indexOf("area_text") >= 0) {
					var id = _id.replace(str_text, to_text);
					var parent = $('#' + _id);
					h = parent.innerHeight();
					w = parent.innerWidth();
					x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    x =  parseInt(x);
                    y = parent.css('top');
					y = y.substring(0, y.length-2);
                    y = parseInt(y);
					interaction.screen.showText(id, node_id ,'Text', x, y, w, h, 44);
		    		interaction.screen.text.push({
						screenID: node_id,
						id: id
					});
		    	}
		    	if(_id.indexOf("area_staticText") >= 0) {
					var id = _id.replace(str_staticText, to_staticText);
					var parent = $('#' + _id);
					h = parent.innerHeight();
					w = parent.innerWidth();
					x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    x =  parseInt(x);
                    y = parent.css('top');
					y = y.substring(0, y.length-2);
                    y = parseInt(y);
					interaction.screen.showStaticText(id, node_id ,'Bulletin Board', x, y, w, h, 8);
		    		interaction.screen.staticText.push({
						screenID: node_id,
						id: id
					});
		    	}
		    	if(_id.indexOf("area_image") >= 0) {
					var id = _id.replace(str1, str2);
					var parent = $('#' + _id);
					h = parent.innerHeight();
					w = parent.innerWidth();
					x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    x =  parseInt(x);
                    y = parent.css('top');
					y = y.substring(0, y.length-2);
                    y = parseInt(y);
					interaction.screen.showImage(id, node_id ,'Image'+num, num, x, y, w, h, 10 + num);
		    		interaction.screen.image.push({
						screenID: node_id,
						id: id
					});
					num++;
		   		}
		    	if(_id.indexOf("area_btn") >= 0) {
					var id = _id.replace(str1, str2);
					var parent = $('#' + _id);
					h = parent.innerHeight();
					w = parent.innerWidth();
					x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    x =  parseInt(x);
                    y = parent.css('top');
					y = y.substring(0, y.length-2);
                    y = parseInt(y);
					interaction.screen.showBtn(id, node_id ,'Button'+bnum, bnum, x, y, w, h, 45 + bnum);
		    		interaction.screen.btn.push({
						screenID: node_id,
						id: id
					});
					bnum++;
		    	}
			});
			$('#screen' + old_node_id + ' img').each(function() {
				var _id = $(this).attr("id");
				if(_id.indexOf("screenbg") >= 0) {
					var mid = $('#'+_id).attr("mid");
					var src = $('#'+_id).attr("src");
					interaction.screen.showBg(src, mid, node_id);
		    		interaction.screen.bgimg.push({
						screenID: node_id,
						mediaId: mid
					});
		    	}
			});
		},
        //更新节点名称
        renameTreeNode: function() {
        	interaction.screen.hideRMenu();
			var zTree = $.fn.zTree.getZTreeObj("tree"),
			nodes = zTree.getSelectedNodes(),
			treeNode = nodes[0];
			zTree.editName(treeNode);
        },
        //树形结构展开
        collapseSonTreeNode: function() {
        	interaction.screen.hideRMenu();
			nodes = interaction.screen.zTree.getSelectedNodes();
			for (var i=0, l=nodes.length; i<l; i++) {
				interaction.screen.zTree.expandNode(nodes[i], true, true, null);
			}
        },
        //树形结构收起
        expandSonTreeNode: function() {
        	interaction.screen.hideRMenu();
			nodes = interaction.screen.zTree.getSelectedNodes();
			for (var i=0, l=nodes.length; i<l; i++) {
				interaction.screen.zTree.expandNode(nodes[i], false, true, null);
			}
        },
        //清空所有子节点
        clearChildren: function() {
        	interaction.screen.hideRMenu();
			nodes = interaction.screen.zTree.getSelectedNodes(),
			treeNode = nodes[0];
			interaction.screen.zTree.removeChildNodes(treeNode);
        },
        hideRMenu: function() {
        	interaction.screen.rMenu.css({"visibility": "hidden"});
			$("body").unbind("mousedown", interaction.screen.onBodyMouseDown);
        },
        onBodyMouseDown: function(event) {
        	if (!(event.target.id == "rMenu" || $(event.target).parents("#rMenu").length>0)) {
				interaction.screen.rMenu.css({"visibility" : "hidden"});
			}
        },
        //初始化背景
        initBg: function(areaId, mediaId, img, screenID){
            if (img.length > 0) {
                interaction.screen.showBg(img, mediaId, screenID);
            }
           
            interaction.screen.bgimg.push({
                mediaId: mediaId,
                areaId: areaId,
                screenID: screenID
            });
        },
        //初始化视频
        initMovie: function(areaId, name, x, y, w, h, zIndex, screenID){
        	//alert(areaId);
            //var id = 'area_movie'+interaction.screen.screenID;
            var id = 'area_movie_' + screenID;
            interaction.screen.showMovie(id, screenID, name, x, y, w, h, zIndex);
            interaction.screen.movie.push({
            	screenID: screenID,
                id: id,
                areaId: areaId,
                name: name,
                x: x,
                y: y,
                w: w,
                h: h,
                zIndex: zIndex
            });
        },
        //初始化照片
        initImage: function(areaId, name, x, y, w, h, zIndex, num, screenID){
            //var index = (interaction.screen.image.length == 0) ? 1 : (interaction.screen.image[interaction.screen.image.length - 1].index + 1)
            var id = 'area_image_' + screenID + '_' + num;
            interaction.screen.showImage(id, screenID, name, num, x, y, w, h, zIndex);
            interaction.screen.image.push({
            	screenID: screenID,
                id: id,
                areaId: areaId,
                name: name,
                x: x,
                y: y,
                w: w,
                h: h,
                zIndex: zIndex
            });
			interaction.screen.disable('image' + num);
        },
        //初始化文字区域
        initText: function(areaId, name, x, y, w, h, zIndex, screenID){
            var id = 'area_text_' + screenID;
            interaction.screen.showText(id, screenID, name, x, y, w, h, zIndex);
            interaction.screen.text.push({
            	screenID: screenID,
                id: id,
                areaId: areaId,
                name: name,
                x: x,
                y: y,
                w: w,
                h: h,
                zIndex: zIndex
            });
        },
        //初始化静态文字
        initStaticText: function(areaId, name, x, y, w, h, zIndex, screenID){
            var id = 'area_staticText_' + screenID;
            interaction.screen.showStaticText(id, screenID, name, x, y, w, h, zIndex);
            interaction.screen.staticText.push({
            	screenID: screenID,
                id: id,
                areaId: areaId,
                name: name,
                x: x,
                y: y,
                w: w,
                h: h,
                zIndex: zIndex
            });
        },
        //初始化日期
        initDate: function(areaId, name, x, y, w, h, zIndex, screenID){
            var id = 'area_date_' + screenID;
            interaction.screen.showDate(id, screenID, name, x, y, w, h, zIndex);
            interaction.screen.date.push({
            	screenID: screenID,
                id: id,
                areaId: areaId,
                name: name,
                x: x,
                y: y,
                w: w,
                h: h,
                zIndex: zIndex
            });
        },
        //初始化时间
        initTime: function(areaId, name, x, y, w, h, zIndex, screenID){
            var id = 'area_time_' + screenID;
            interaction.screen.showTime(id, screenID, name, x, y, w, h, zIndex);
            interaction.screen.time.push({
            	screenID: screenID,
                id: id,
                areaId: areaId,
                name: name,
                x: x,
                y: y,
                w: w,
                h: h,
                zIndex: zIndex
            });
        },
        //初始化图标
        initLogo: function(areaId, name, x, y, w, h, mediaId, img, zIndex, screenID){
            var id = 'area_logo_' + screenID;
            interaction.screen.showLogo(id, screenID, name, x, y, w, h, img, zIndex);
            interaction.screen.logo = {
            	screenID: screenID,
                id: id,
                areaId: areaId,
                mediaId: mediaId,
                name: name,
                x: x,
                y: y,
                w: w,
                h: h,
                zIndex: zIndex
            };
        },
        initWeather: function(areaId, name, x, y, w, h, zIndex, screenID){
            var id = 'area_weather_' + screenID;
            interaction.screen.showWeather(id, screenID, name, x, y, w, h);
            interaction.screen.weather.push({
            	screenID: screenID,
                id: id,
                areaId: areaId,
                name: name,
                x: x,
                y: y,
                w: w,
                h: h,
                zIndex: zIndex
            });
        },
        initWebpage: function(areaId, name, x, y, w, h, zIndex, screenID){
            var id = 'area_webpage_' + screenID;
            interaction.screen.showWebpage(id, screenID, name, x, y, w, h);
            interaction.screen.webpage.push({
            	screenID: screenID,
                id: id,
                areaId: areaId,
                name: name,
                x: x,
                y: y,
                w: w,
                h: h,
                zIndex: zIndex
            });
        },
        initBtn: function(areaId, name, x, y, w, h, zIndex, num, screenID) {
        	var id = 'area_btn_' + screenID + '_' + num;
            interaction.screen.showBtn(id, screenID, name, num, x, y, w, h, zIndex);
            interaction.screen.btn.push({
            	screenID: screenID,
                id: id,
                areaId: areaId,
                name: name,
                x: x,
                y: y,
                w: w,
                h: h,
                zIndex: zIndex
            });
			interaction.screen.disable('Button' + num);
        },
        //初始化互动应用
        /*
        initInteraction: function(areaId, name, x, y, w, h, zIndex){
            var id = 'area_interaction';
            interaction.screen.showInteraction(id, name, x, y, w, h, zIndex);
            interaction.screen.interaction = {
                id: id,
                areaId: areaId,
                name: name
            };
        },*/
        enable: function(id){
            var cur = $('#' + id);
            cur.parent().removeClass('disable');
            cur.unbind('click').bind('click', function(event){
                switch (id) {
                    case 'movie':
                        interaction.screen.addMovie();
                        break;
                    case 'image1':
                        interaction.screen.addImage(1);
                        break;
					case 'image2':
                        interaction.screen.addImage(2);
                        break;
					case 'image3':
                        interaction.screen.addImage(3);
                        break;
                    case 'image4':
                        interaction.screen.addImage(4);
                        break;
                    case 'text':
                        interaction.screen.addText();
                        break;
                    case 'staticText':
                        interaction.screen.addStaticText();
                        break;
                    case 'date':
                        interaction.screen.addDate();
                        break;
                    case 'time':
                        interaction.screen.addTime();
                        break;
                    case 'weather':
                        interaction.screen.addWeather();
                        break;
                    case 'logo':
                        interaction.screen.addLogo();
                        break;
                    case 'webpage':
                        interaction.screen.addWebpage();
                        break;
                    case 'bg':
                        interaction.screen.addBg();
                        break;
                    case 'bton':
                        interaction.screen.addBtn();
                        break;
                    case 'btnGroup':
                        interaction.screen.addTreeBtnGroup();
                        break;
                }
            });
        },
        disable: function(id){
            var cur = $('#' + id);
            cur.unbind('click');
            cur.parent().addClass('disable');
        },
        testDisable: function(id, iconSkin){
        	var cur = $('#'+id);
        	
        },
		isContains : function(p, c){
			/*parent contain c*/
			var range1 = interaction.screen.getRealRange(p);
			var range2 = interaction.screen.getRealRange(c);
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
			
			var range1 = interaction.screen.getRealRange(curObj);
			var range2 = interaction.screen.getRealRange(targetObj);
            var x1 = range1.x;
            var y1 = range1.y;
            var x2 = x1 + range1.w;
            var y2 = y1 + range1.h;
            
            var xx1 = range2.x;
            var yy1 = range2.y;
            var xx2 = xx1 + range2.w;
            var yy2 = yy1 + range2.h;
            
            if (interaction.screen.debug) {
                console.info("isIntersect curObj:" + curObj.text() + ", targetObj:" + targetObj.text() + ", direct:" + direct + ", point(left, top): " + (x1 + "," + y1) + ", point(right, bottom):" + (x2 + "," + y2) + ", target point(left, top):" + (xx1 + "," + yy1) + ", target point(right, bottom):" + (xx2 + "," + yy2));
            }
            
            var result = false;
            switch (direct) {
                case interaction.screen.RIGHT:
                    //上方修正
                    result = (xx1 >= x1) &&
                    (x2 > xx1 && ((y1 >= yy1 && y1 < yy2) || (y2 > yy1 && y2 <= yy2) || (y1 < yy1 && y2 > yy2) || (y1 == yy1 && y2 == yy2)));
                    break;
                case interaction.screen.LEFT:
                    result = (x2 > xx1) &&
                    (x1 < xx2 && ((y1 >= yy1 && y1 < yy2) || (y2 > yy1 && y2 <= yy2) || (y1 < yy1 && y2 > yy2) || (y1 == yy1 && y2 == yy2)));
                    break;
                case interaction.screen.UP:
                    result = (y2 > yy1) &&
                    (yy2 > y1 && ((x1 >= xx1 && x1 < xx2) || (x2 > xx1 && x2 <= xx2) || (x1 < xx1 && x2 > xx2) || (x1 == xx1 && x2 == xx2)));
                    break;
                case interaction.screen.DOWN:
                    result = (yy1 >= y1) &&
                    (y2 > yy1 && ((x1 >= xx1 && x1 < xx2) || (x2 > xx1 && x2 <= xx2) || (x1 < xx1 && x2 > xx2) || (x1 == xx1 && x2 == xx2)));
                    break;
                case interaction.screen.RIGHT_DOWN:
                    result = ((xx1 >= x1) &&
                    (x2 > xx1 && ((y1 >= yy1 && y1 < yy2) || (y2 > yy1 && y2 < yy2) || (y1 < yy1 && y2 > yy2) || (y1 == yy1 && y2 == yy2)))) ||
                    ((yy1 >= y1) &&
                    (y2 > yy1 && ((x1 >= xx1 && x1 < xx2) || (x2 > xx1 && x2 <= xx2) || (x1 < xx1 && x2 > xx2) || (x1 == xx1 && x2 == xx2))));
                    break;
                //all dragable
                case interaction.screen.DRAGE_RIGHT:
                case interaction.screen.DRAGE_DOWN:
                case interaction.screen.DRAGE_RIGHT_DOWN:
                case interaction.screen.DRAGE_LEFT:
                case interaction.screen.DRAGE_UP:
                case interaction.screen.DRAGE_LEFT_UP:
                case interaction.screen.DRAGE_RIGHT_UP:
                case interaction.screen.DRAGE_LEFT_DOWN:
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
            if (interaction.screen.debug) {
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
                case interaction.screen.RIGHT_DOWN:
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
                    if (interaction.screen.debug) {
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

                //if (cid == undefined || (cid.indexOf('image') == -1 && cid.indexOf('movie') == -1 && cid.indexOf('webpage') == -1)) {
                if(((id.indexOf('time') != -1 || id.indexOf('date') != -1 || id.indexOf('weather') != -1) && (cid.indexOf('time') != -1 || cid.indexOf('date') != -1 || cid.indexOf('weather') != -1)) || ((id.indexOf('image') != -1 || id.indexOf('movie') != -1 || id.indexOf('webpage') != -1) && (cid.indexOf('image') != -1 || cid.indexOf('movie') != -1 || cid.indexOf('webpage') != -1)) || (id.indexOf('btn') != -1 && cid.indexOf('btn') != -1)) {
                	switch (direct) {
		     			case interaction.screen.RIGHT_DOWN:
		              		if(interaction.screen.isIntersect(curObj, kid, interaction.screen.RIGHT)) {
		                    	result.push({
		                    		'direct': interaction.screen.RIGHT,
		                      		'obj': kid
		                    	});
		            		}else{
		            			if(interaction.screen.isIntersect(curObj, kid, interaction.screen.DOWN)) {
		                      		 result.push({
		                          		'direct': interaction.screen.DOWN,
		                           		'obj': kid
		                    		});
		                    	}
		            		}     
		                	break;
		    			default:
		           			if (interaction.screen.isIntersect(curObj, kid, direct)) {
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
                
            });
 
            if (interaction.screen.debug) {
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
                minWidth = interaction.screen.minImageWidth;
                minHeight = interaction.screen.minImageHeight;
            }
            else 
                if (id.indexOf('movie') != -1) {
                    minWidth = interaction.screen.minVideoWidth;
                    minHeight = interaction.screen.minVideoHeight;
                }
            if (interaction.screen.debug) {
                console.info("dockArea, id:" + id + ", minWidth:" + minWidth + ", minHeight:" + minHeight);
            }
            var result = false;
            var enlargeX = interaction.screen.width > interaction.screen.height ? 10 : 5; //横向差为10倍
            var enlargeY = interaction.screen.width > interaction.screen.height ? 5 : 10; //纵向差为5倍
            var curX = curObj.position().left;
            var curY = curObj.position().top;
            var curW = curObj.outerWidth();
            var curH = curObj.outerHeight();
            
            switch (direct) {
                case interaction.screen.RIGHT:
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
                case interaction.screen.LEFT:
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
                case interaction.screen.UP:
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
                case interaction.screen.DOWN:
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
                case interaction.screen.RIGHT_DOWN:
                    var minX = 0;
                    var minY = 0;
                    var width = 0;
                    var height = 0;
                    var minX = targetObjs[0].obj.position().left;
                    var minY = targetObjs[0].obj.position().top;
                    for (var i = 0; i < targetObjs.length; i++) {
                    
                        if (targetObjs[i].direct == interaction.screen.RIGHT) {
                            if (targetObjs[i].obj.position().left < minX) {
                                minX = targetObjs[i].obj.position().left;
                            }
                            if (interaction.screen.debug) {
                                console.info("dockArea[" + curObj.text() + "] RIGHT_DOWN, intersect RIGHT [" + targetObjs[i].obj.text() + "], minX:" + minX);
                            }
                        }
                        else 
                            if (targetObjs[i].direct == interaction.screen.DOWN) {
                                if (targetObjs[i].obj.position().top < minY) {
                                    minY = targetObjs[i].obj.position().top;
                                }
                                if (interaction.screen.debug) {
                                    console.info("dockArea[" + curObj.text() + "] RIGHT_DOWN, intersect DOWN [" + targetObjs[i].obj.text() + "], minY:" + minY);
                                }
                            }
                    }
                    width = minX - curObj.position().left;
                    height = minY - curObj.position().top;
                    
                    if (interaction.screen.debug) {
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
                case interaction.screen.DRAGE_LEFT:
                    var maxX = targetObjs[0].obj.position().left + targetObjs[0].obj.outerWidth();
                    for (var i = 1; i < targetObjs.length; i++) {
                        if (targetObjs[i].obj.position().left + targetObjs[i].obj.outerWidth() > maxX) {
                            maxX = targetObjs[i].obj.position().left + targetObjs[i].obj.outerWidth();
                        }
                    }
                    
                    curObj.css('left', maxX);
                    result = true;
                    break;
                case interaction.screen.DRAGE_UP:
                    var maxY = targetObjs[0].obj.position().top + targetObjs[0].obj.outerHeight();
                    for (var i = 1; i < targetObjs.length; i++) {
                        if (targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight() > maxY) {
                            maxY = targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight();
                        }
                    }
                    curObj.css('top', maxY);
                    result = true;
                    break;
                case interaction.screen.DRAGE_RIGHT:
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
                case interaction.screen.DRAGE_DOWN:
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
                case interaction.screen.DRAGE_LEFT_UP:
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
                        targetX + width <= interaction.screen.width) {
                            curObj.css('left', targetX);
                            result = true;
                        }
                        
                        targetY = targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight();
                        if (top >= targetObjs[i].obj.position().top &&
                        top < targetY &&
                        (targetY - top) * enlargeY < height &&
                        targetY + height <= interaction.screen.height) {
                            curObj.css('top', targetY);
                            result = true;
                        }
                        
                    }
                    break;
                case interaction.screen.DRAGE_RIGHT_UP:
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
                        targetY + height <= interaction.screen.height) {
                            curObj.css('top', targetY);
                            result = true;
                        }
                    }
                    break;
                case interaction.screen.DRAGE_RIGHT_DOWN:
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
                case interaction.screen.DRAGE_LEFT_DOWN:
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
                        targetX + width <= interaction.screen.width) {
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
			if(((x + w) > interaction.screen.width ) || (y + h) > interaction.screen.height){
				result = false;
				curObj.css('left', curX);
				curObj.css('top', curY);
				curObj.css('width', curW);
				curObj.css('height', curH);
			}
            if (interaction.screen.debug) {
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
            if (left + nWidth > interaction.screen.width) {
                nWidth = interaction.screen.width - left;
                curObj.css('width', nWidth);
            }
            if (top + nHeight > interaction.screen.height) {
                nHeight = interaction.screen.height - top;
                curObj.css('height', nHeight);
            }
            
            
            var width = Math.round(interaction.screen.realWidth * nWidth / interaction.screen.width);
            var height = Math.round(interaction.screen.realHeight * nHeight / interaction.screen.height);
            
            if (interaction.screen.debug) {
                console.info("adjustArea width:" + width + ", height:" + height);
            }
            var wmod = width % 4;
            if (wmod != 0) {
                //adjust width
                width -= wmod;
                
                nWidth = width * interaction.screen.width / interaction.screen.realWidth;
                curObj.css('width', nWidth);
                if (interaction.screen.debug) {
                    console.info("adjustArea realWidth:" + width + ", show width:" + nWidth);
                }
            }
            
            if (height % 2 != 0) {
                //addjust height
                height--;
                nHeight = height * interaction.screen.height / interaction.screen.realHeight;
                curObj.css('height', nHeight);
                if (interaction.screen.debug) {
                    console.info("adjustArea realHeight:" + height + ", show height:" + nHeight);
                }
            }
        },
        checkCurrentRange: function(curObj){//当前对象存在于其他对象的冲突，则返回true否则返回false
            //check only image and movie
            var id = curObj.attr('id');
            if (id == undefined || (id.indexOf('image') == -1 && id.indexOf('movie') == -1 && id.indexOf('date') == -1 && id.indexOf('weather') == -1 && id.indexOf('webpage') == -1 && id.indexOf('interaction') == -1)) {
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
                if (pid.indexOf('image') > 0 || pid.indexOf('movie') > 0 || pid.indexOf('webpage') > 0 || pid.indexOf('interaction') > 0) {
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
                if (pid.indexOf('image') > 0 || pid.indexOf('movie') > 0 || pid.indexOf('webpage') > 0 || pid.indexOf('interaction') > 0) {
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
				interaction.screen.updateAreaBody(obj);			
            }
        },
        //return -1:no space 0: OK 1: min Ok
        calculateRange: function(w, h, minW, minH){
            var dls = $('#screen').children('dl');
            var areas = new Array();
            if (dls.length > 0) {
                for (var i = 0; i < dls.length; i++) {
                    if (dls[i].id.indexOf('movie') >= 0 || dls[i].id.indexOf('image') > 0 || dls[i].id.indexOf('webpage') >= 0 || dls[i].id.indexOf('interaction') > 0) {
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
            interaction.screen.openImageLibrary('bg', interaction.screen.screenID);
        },
        //显示背景区域
        showBg: function(bgImg, mid, screenID){
            $('#screenbg' + screenID).attr('src', bgImg).attr('width', interaction.screen.width).attr('height', interaction.screen.height).attr('mid', mid).attr('screenID', screenID).addClass('mxBg');
            tb_remove();
        },
        addMovie: function(){
            var id = 'area_movie_' + interaction.screen.screenID;
            var w = interaction.screen.minVideoWidth;
            var h = interaction.screen.minVideoHeight;
            var screenID = interaction.screen.screenID;
            
            //添加设置默认层
            interaction.screen.showMovie(id, screenID ,'Movie/Photo', -1, -1, w, h, 10);
            interaction.screen.movie.push({
            	screenID: screenID,
                id: id
            });
        },//显示视频区域
        showMovie: function(id, screenID, title, x, y, w, h, zIndex){
        	var pageId = interaction.screen.screenID; //模块属于哪个
            var minW = interaction.screen.minVideoWidth;
            var minH = interaction.screen.minVideoHeight;
            //var screenID = interaction.screen.screenID;
            interaction.screen.createArea(id, screenID ,'movie', title, minW, interaction.screen.width, minH, interaction.screen.height, function(event, ui){
                interaction.screen.disable('movie');
                //$('#' + id + ' > dd').html('<img src="/images/icons/sp.png" alt="movie" width="100%" height="100%" />');  //test 先隐藏
            }, zIndex);
            //只有当未设置初始值才默认规则
            if (w == -1 || h == -1) {
                //设置视频区域的位置
                if (interaction.screen.image.length == 0) {
                    //如果为设置照片，则默认宽度为screen宽度，高度为屏幕高度的2/3
                    interaction.screen.changePosition(id, 0, 0, interaction.screen.width, (interaction.screen.height * 2) / 3, 'absolute');
                }
                else {
                    //获取最后一个照片的信息，在最后一个照片的下方显示，宽度为全屏
                    var lastImg = $('#' + interaction.screen.image[interaction.screen.image.length - 1].id);
                    var top = lastImg.position().top + lastImg.outerHeight(true);
                    var parent = lastImg.parent();
                    if (top >= parent.innerHeight()) {
                        top = 0;
                    }
                    interaction.screen.changePosition(id, 0, top, interaction.screen.width, interaction.screen.height - top, 'absolute');
                }
            }else {
                interaction.screen.changePosition(id, x, y, w, h, 'absolute');
            }
        },
        addImage: function(num){
            var screenID = interaction.screen.screenID;
            var img_num = num;
            var id = 'area_image_' + screenID + '_' + num;
            //default
            var w = (200 * interaction.screen.width) / interaction.screen.realWidth;
            var h = (200 * interaction.screen.height) / interaction.screen.realHeight;
            if (interaction.screen.width < interaction.screen.height) {
                t = w;
                w = h;
                h = t;
            }
            interaction.screen.showImage(id, screenID, 'Image'+num, img_num, -1, -1, w, h, 10 + num);
            interaction.screen.image.push({
            	screenID: screenID,
                id: id
            });
			//interaction.screen.nav_reset();
        },
        showImage: function(id, screenID, title, num, x, y, w, h, zIndex){
            var minW = interaction.screen.minImageWidth;
            var minH = interaction.screen.minImageHeight;
            //var screenID = interaction.screen.screenID;
            //interaction.screen.createArea(id, 'image', title, minW, interaction.screen.width, minH, interaction.screen.height, function(event, ui){
            interaction.screen.createArea(id, screenID, 'image'+num, title, minW, interaction.screen.width, minH, interaction.screen.height, function(event, ui){
            	//$('#' + id + ' > dd').html('<img src="/images/icons/tp.png" alt="image" width="100%" height="100%" />'); //test 先隐藏
                interaction.screen.disable('image'+num);

                //如果设置了xywh则无需默认
                if (w != -1 && h != -1) {
                    return;
                }

                //设置照片显示策略
                var cur = $('#' + id);
                var prev = cur.prev();
                var pid = prev.attr('id');
               	//alert('pid: '+pid);
                var container = cur.parent();
                
                if (pid == undefined) {
                    //just default
                    interaction.screen.changePosition(id, 0, 0, interaction.screen.width, interaction.screen.height / 3);
                    return;
                }
                
                if (pid.indexOf('movie') > -1) {
                    //prev movie
                    interaction.screen.changePosition(id, 0, prev.outerHeight(true) + 1, container.innerWidth() / 3, container.innerHeight() - prev.outerHeight(true) - 1, 'absolute');
                }
                else
                    if (pid.indexOf('image') > -1) {
                        //image 如果前一元素为照片，则判断是否可以平铺到右侧，否则判断下侧，否则默认位置弹出，且提示
                        if (prev.position().left + prev.outerWidth(true) + prev.innerWidth() <= container.innerWidth()) {
                            interaction.screen.changePosition(id, prev.position().left + prev.outerWidth(true), prev.position().top, prev.outerWidth(true), prev.outerHeight(true), 'absolute');
                        }
                        else 
                            if (prev.position().top + prev.outerHeight(true) + prev.innerHeight() <= container.innerHeight()) {
                                interaction.screen.changePosition(id, prev.position().left, prev.position().top + prev.outerHeight(true), prev.outerWidth(true), prev.outerHeight(), 'absolute');
                            }
                    }
            }, zIndex);
            //设置照片域的位置
            interaction.screen.changePosition(id, x, y, w, h, 'absolute');
        },
        addText: function(){
        	var screenID = interaction.screen.screenID;
            var id = 'area_text_' + screenID;
            interaction.screen.showText(id, screenID, 'Text', -1, -1, -1, interaction.screen.defaultTextHeight);
            interaction.screen.text.push({
            	screenID: screenID,
            	id: id
            });
        },
        showText: function(id, screenID, title, x, y, w, h, zIndex){
            interaction.screen.createArea(id, screenID, 'text', title, 120, interaction.screen.width, interaction.screen.minTextHeight, interaction.screen.height, function(event, ui){
                interaction.screen.disable('text');
                //如果设置了xywh则无需默认
                if (w != -1 && h != -1) {
                    return;
                }
                var textArea = $('#' + id);
                interaction.screen.changePosition(id, 0, interaction.screen.height - h, interaction.screen.width, h, 'absolute');
            }, zIndex);
            //设置照片域的位置
			$('#'+id).css('z-index', 44);
            interaction.screen.changePosition(id, x, y, w, h, 'absolute');
        },
        addStaticText: function(){
        	var screenID = interaction.screen.screenID;
            var id = 'area_staticText_' + screenID;
            interaction.screen.showStaticText(id, screenID, 'Bulletin Board', 0, interaction.screen.height-100, interaction.screen.width/2, 100);
            interaction.screen.staticText.push({
            	screenID: screenID,
                id: id
            });
        },
        showStaticText: function(id, screenID, title, x, y, w, h, zIndex){
        	//var screenID = interaction.screen.screenID;
            interaction.screen.createArea(id, screenID, 'staticText', title, 120, interaction.screen.width, interaction.screen.minTextHeight, interaction.screen.height, function(event, ui){
                interaction.screen.disable('staticText');
                if (w != -1 && h != -1) {
                    return;
                }
                var textArea = $('#' + id);
                interaction.screen.changePosition(id, 150, 150, 200, 50, 'absolute');
            }, zIndex);
            //设置照片域的位置
			$('#'+id).css('z-index', 8);
            interaction.screen.changePosition(id, x, y, w, h, 'absolute');
        },
        addDate: function(){
        	var screenID = interaction.screen.screenID;
			var id = 'area_date_'+ screenID;
            interaction.screen.showDate(id, screenID, 'Date', -1, -1, -1, -1);
            interaction.screen.date.push({
            	screenID: screenID,
                id: id
            });
        },
        //显示日期区域
        showDate: function(id, screenID, title, x, y, w, h, zIndex){
        	//var screenID = interaction.screen.screenID;
            interaction.screen.createArea(id, screenID, 'date', title, interaction.screen.minDateWidth, interaction.screen.maxDateWidth, interaction.screen.minDateHeight, interaction.screen.maxDateHeight, function(event, ui){
                //$("#date" ).button("disable");
                interaction.screen.disable('date');
                //如果设置了xywh则无需默认
                if (w != -1 && h != -1) {
                    return;
                }
                
                w = interaction.screen.minDateWidth;
                h = interaction.screen.minDateHeight;
                x = interaction.screen.width - w;
                y = 0;
                var date = $('#area_time');
                var pos = date.position();
                if (pos != null) {
                    y = pos.top + date.outerHeight(true);
                }
                //设置照片域的位置
                interaction.screen.changePosition(id, x, y, w, h, 'absolute');
            }, zIndex);
            //设置照片域的位置
			$('#'+id).css('z-index', 23);
            interaction.screen.changePosition(id, x, y, w, h, 'absolute');
        },
        addTime: function(){
        	var screenID = interaction.screen.screenID;
            var id = 'area_time_' + screenID;
            interaction.screen.showTime(id, screenID, 'Time', -1, -1, -1, -1);
            interaction.screen.time.push({
            	screenID: screenID,
                id: id
            });
        },
        showTime: function(id, screenID, title, x, y, w, h, zIndex){
        	//var screenID = interaction.screen.screenID;
            interaction.screen.createArea(id, screenID, 'time', title, 50, interaction.screen.width, 50, 100, function(event, ui){
                //$("#time" ).button("disable");
                interaction.screen.disable('time');
                
                //如果设置了xywh则无需默认
                if (w != -1 && h != -1) {
                    return;
                }
                
                w = 100;
                h = 50;
                x = interaction.screen.width - w;
                y = 0;
                //设置照片域的位置
                interaction.screen.changePosition(id, x, y, w, h, 'absolute');
            }, zIndex);
            $('#'+id).css('z-index', 22);
            interaction.screen.changePosition(id, x, y, w, h, 'absolute');
        },
        addWeather: function(){
        	var screenID = interaction.screen.screenID;
			var id = 'area_weather_' + screenID;
            interaction.screen.showWeather(id, screenID, 'Weather', -1, -1, -1, -1);
            interaction.screen.weather.push({
            	screenID: screenID,
                id: id
            });
        },
        showWeather: function(id, screenID, title, x, y, w, h, zIndex){
        	//var screenID = interaction.screen.screenID;
            interaction.screen.createArea(id, screenID, 'weather', title, interaction.screen.minWeatherWidth, interaction.screen.maxWeatherWidth, interaction.screen.minWeatherHeight, interaction.screen.maxWeatherHeight, function(event, ui){
                //$("#time" ).button("disable");
                interaction.screen.disable('weather');
                //如果设置了xywh则无需默认
                if (w != -1 && h != -1) {
                    return;
                }
                
                w = interaction.screen.minWeatherWidth;
                h = interaction.screen.minWeatherHeight;
                x = interaction.screen.width - w;
                y = 0;
                var date = $('#area_time');
                var pos = date.position();
                if (pos != null) {
                    y = pos.top + date.outerHeight(true);
                }
                //设置照片域的位置
                interaction.screen.changePosition(id, x, y, w, h, 'absolute');
            }, zIndex);
			//固定zindex
            $('#'+id).css('z-index', 21);
            interaction.screen.changePosition(id, x, y, w, h, 'absolute');
        },
        addWebpage: function(index){
            var screenID = interaction.screen.screenID;
            var id = 'area_webpage_' + screenID;
            var title = 'Webpage';
            interaction.screen.showWebpage(id, screenID, title, -1, -1, -1, -1);
            interaction.screen.webpage.push({
             	screenID: screenID,
                id: id
            });
        },
        showWebpage: function(id, screenID, title, x, y, w, h, zIndex){
			zIndex = 20;
			var minW = interaction.screen.minImageWidth;
            var minH = interaction.screen.minImageHeight;
            //var screenID = interaction.screen.screenID;
            interaction.screen.createArea(id, screenID, 'webpage', title, minW, interaction.screen.width, minH, interaction.screen.height, function(){
                interaction.screen.disable('webpage');
                //$('#' + id+' > dd').css('background-color','#ff0000');
                //$('#' + id + ' > dd').html('<img src="/images/icons/web.jpg" alt="webpage" width="100%" height="100%" />');
				
                //如果设置了xywh则无需默认
                if (w == -1 || h == -1) {
                    //设置照片域的位置
                    interaction.screen.changePosition(id, 30, 30, 150, 150, 'absolute');
                }
            }, zIndex);
            //设置照片域的位置
            interaction.screen.changePosition(id, x, y, w, h,'absolute');
            tb_remove();
        },
        addLogo: function(){
            //interaction.screen.openImageLibrary('logo');
            var id = 'area_logo';
            var title = 'Logo';
            interaction.screen.showLogo(id, title, -1, -1, -1, -1);
            interaction.screen.logo = {
                id: id
            };
        },
        showLogo: function(id, title, x, y, w, h, zIndex){
            //var id = 'area_logo';
			zIndex=200;
			var min = interaction.screen.logoSize[0];
			var max = interaction.screen.logoSize[interaction.screen.logoSize.length - 1];
			var screenID = interaction.screen.screenID;
            interaction.screen.createArea(id, screenID, 'logo', title, min, max, min, max, function(){
                interaction.screen.disable('logo');
                $('#' + id+' > dd').css('background-color','#ff0000');
				
                //如果设置了xywh则无需默认
                if (w == -1 || h == -1) {
                    //设置照片域的位置
                    interaction.screen.changePosition(id, 0, 0, max, max, 'absolute');
                }
            }, zIndex);
            //设置照片域的位置
            interaction.screen.changePosition(id, x, y, w, h,'absolute');
            tb_remove();
            
        },
        addBtn: function(){
        	var screenID = interaction.screen.screenID;
        	var num = 1;
        	$('#screen' + screenID + ' dl').each(function() {
    			var _id = $(this).attr("id");
    			if(_id.indexOf("area_btn") >= 0) {
    				num++;
    			}
			});
            var id = 'area_btn_' + screenID + '_' + num;
            interaction.screen.showBtn(id, screenID, 'Touch'+num, num, -1, -1, -1, -1);
            interaction.screen.btn.push({
            	screenID: screenID,
                id: id
            });
        },
        showBtn: function(id, screenID, title, num, x, y, w, h, zIndex){
            interaction.screen.createArea(id, screenID, 'bton', title, 24, interaction.screen.width, 24, interaction.screen.height, function(event, ui){
                //如果设置了xywh则无需默认
                if (w != -1 && h != -1) {
                    return;
                }
                
                w = 100;
                h = 100;
                x = interaction.screen.width - w;
                y = 0;
                //设置照片域的位置
                interaction.screen.changePosition(id, x, y, w, h, 'absolute');
            }, zIndex);
            $('#'+id).css('z-index', 45);
            $('#'+id).css('opacity', 0.7);
            interaction.screen.changePosition(id, x, y, w, h, 'absolute');
        },
        addBtnGroup: function(){
            interaction.screen.openBtnGroupDialog('btnGroup');
            /*var id = 'area_btn_' + interaction.screen.screenID;
            interaction.screen.showBtnGroup(id, 'Button Group', -1, -1, -1, -1);
            interaction.screen.btnGroup = {
                id: id
            };*/
        },
        showBtnGroup: function(id, title, x, y, w, h, zIndex){
        	//设置照片域的位置
            w = parseInt(w/2);
            h = parseInt(h/2);
            if(w >= 960) {
            	w = 960;
            }
            if(h >= 540) {
            	h = 540;
            }
            interaction.screen.createArea(id, 'btnGroup', title, 50, interaction.screen.width, 50, interaction.screen.height, function(event, ui){
                //interaction.screen.disable('btnGroup');
                //$('#' + id + ' > dd').html('<img src="/images/icons/ap.png" alt="movie" width="100%" height="100%" />');
                //如果设置了xywh则无需默认
                if (w != -1 && h != -1) {
                    return;
                }
                interaction.screen.changePosition(id, x, y, w, h, 'absolute');
            }, zIndex);
            $('#'+id).css('z-index', 102);
            interaction.screen.changePosition(id, x, y, w, h, 'absolute');
            $('#'+id).append('<dl tabindex="3" id="area_btn_1_3" style="width: 100px; z-index: 102; position: absolute; left: 0px; top: 0px; height: 100px;" class="bton common-style ui-resizable ui-draggable"><dt>Button1<div class="icon"><img class="close" title="Close" src="/images/icons/cross2.png"></div></dt><dd style="height: 78px;"></dd><div class="ui-resizable-handle ui-resizable-e"></div><div class="ui-resizable-handle ui-resizable-s"></div><div class="ui-resizable-handle ui-resizable-w"></div><div style="z-index: 1001;" class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se"></div><div class="ui-resizable-handle ui-resizable-n"></div></dl>');
        	$('#'+id).append('<dl tabindex="3" id="area_btn_2_3" style="width: 100px; z-index: 102; position: absolute; left: 110px; top: 0px; height: 100px;" class="bton common-style ui-resizable ui-draggable"><dt>Button1<div class="icon"><img class="close" title="Close" src="/images/icons/cross2.png"></div></dt><dd style="height: 78px;"></dd><div class="ui-resizable-handle ui-resizable-e"></div><div class="ui-resizable-handle ui-resizable-s"></div><div class="ui-resizable-handle ui-resizable-w"></div><div style="z-index: 1001;" class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se"></div><div class="ui-resizable-handle ui-resizable-n"></div></dl>');
        
        },
        nav_reset: function() {
        	var obj = $.fn.zTree.getZTreeObj("tree");
        	var node = obj.getNodeByTId('tree_'+interaction.screen.screenID);
			interaction.screen.add_click(interaction.screen.screenID, node);
        },
		isResizeLimited : function(obj){
			var id = obj.attr('id');
			var orignLeft = obj.position().left;
			var orignTop = obj.position().top;
			var orignWidth = obj.innerWidth();
			var orignHeight = obj.innerHeight();
			if(orignTop >= interaction.screen.height || orignLeft >= interaction.screen.width){
				return true;
			}
			
			if(orignWidth > interaction.screen.width || orignHeight > interaction.screen.height){
				return true;
			}
			
			if((orignLeft + orignWidth) > interaction.screen.width || (orignTop + orignHeight) > interaction.screen.height){
				return true;
			}
			
			/*日期和天气*/
			if(id.indexOf('date') != -1){
				if((orignWidth > interaction.screen.maxDateWidth || orignHeight > interaction.screen.maxDateHeight) || (orignWidth < interaction.screen.minDateWidth || orignHeight < interaction.screen.minDateHeight)){
					return true;
				}
			}else if (id.indexOf('weather') != -1) {
				if((orignWidth > interaction.screen.maxWeatherWidth || orignHeight > interaction.screen.maxWeatherHeight) || (orignWidth < interaction.screen.minWeatherWidth || orignHeight < interaction.screen.minWeatherHeight)){
					return true;
				}
			}
			else 
				if (id.indexOf('text') != -1) {
					/*文本区域大小限制*/
					if (orignHeight > interaction.screen.maxTextHeight || orignHeight < interaction.screen.minTextHeight) {
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
			switch(dir){
				case interaction.screen.LEFT:
				if(orignLeft > 0){
					//obj.css('left',orignLeft-interaction.screen.gridX);
					//obj.css('width',orignWidth+interaction.screen.gridX);
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
							obj.css('left',orignLeft-interaction.screen.gridX);
							obj.css('width',orignWidth+interaction.screen.gridX);
						}
					}
				}else{
					changed = false;
				}
				break;
				case interaction.screen.UP:
				if(orignTop > 0){
					//obj.css('top',orignTop-interaction.screen.gridY);
					//obj.css('height',orignHeight+interaction.screen.gridY);
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
							obj.css('top',orignTop-interaction.screen.gridY);
							obj.css('height',orignHeight+interaction.screen.gridY);
						}
					}
				}else{
					changed = false;
				}
				break;
				case interaction.screen.RIGHT:
				if((orignLeft+orignWidth) < interaction.screen.width){
					//obj.css('width',orignWidth+interaction.screen.gridX);
					if(obj.attr('id') == 'area_weather' || obj.attr('id') == 'area_date') {
						if(orignWidth >= 256) {
							changed = false;
						}else {
							if((orignLeft + 2*orignWidth) > interaction.screen.width){
								if(orignLeft + orignWidth == interaction.screen.width) {
									changed = false;
								}else {
									obj.css('left', interaction.screen.width - 2 *　orignWidth);
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
							obj.css('width',orignWidth+interaction.screen.gridX);
						}
					}
				}else{
					changed = false;
				}
				break;
				case interaction.screen.DOWN:
				if((orignTop+orignHeight) < interaction.screen.height){
					//obj.css('height',orignHeight+interaction.screen.gridY);
					if(obj.attr('id') == 'area_weather' || obj.attr('id') == 'area_date') {
						if(orignHeight >= 256) {
							changed = false;
						}else {
							if(interaction.screen.height > (2*orignHeight +orignTop)) {
								obj.css('height', 2 * orignHeight);
								//console.info('-----orignHeight:　'+ orignHeight);
							}else {
								if(interaction.screen.height-orignTop-orignHeight==0) {
									changed = false;
								} else {
									if(interaction.screen.height-orignTop-orignHeight <= orignHeight ) {
									//console.info('----2');
										obj.css('top', interaction.screen.height - 2*orignHeight);
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
							obj.css('height',orignHeight+interaction.screen.gridY);
						}
					}
				}else{
					changed = false;
				}
				break;
			}
			
			if(changed){
				if(obj.attr('id') == 'area_logo'){
					for(var i = interaction.screen.logoSize.length-1; i >= 0; i--){
						var size = interaction.screen.logoSize[i];
						if(orignWidth >= size || orignHeight >= size){
							if (dir == interaction.screen.LEFT || dir == interaction.screen.RIGHT) {
								obj.css('width', size);
							}
							else {
								obj.css('height', size);
							}
							break;
						}
					}
					interaction.screen.updateAreaBody(obj);
					interaction.screen.showAreaInfo(obj);
					return;
				}
				//date、weather size 2013-12-19
				if(obj.attr('id') == 'area_date' || obj.attr('id') == 'area_weather'){			
					for(var i = 2; i < interaction.screen.DateWeatherSize.length; i++){
						var width = interaction.screen.DateWeatherSize[i];
						if(orignWidth >= width){
							if (dir == interaction.screen.LEFT || dir == interaction.screen.RIGHT) {
								obj.css('width', width);
							}
							break;
						}
					}
					
					for(var i = 2; i < interaction.screen.DateWeatherSize.length; i++){
						var height = interaction.screen.DateWeatherSize[i];
						if(orignHeight >= height){
							if (dir == interaction.screen.UP || dir == interaction.screen.DOWN) {
								obj.css('height', height);
							}
							break;
						}
					}
					interaction.screen.updateAreaBody(obj);
					interaction.screen.showAreaInfo(obj);
					return;
				}
			
				if (interaction.screen.isResizeLimited(obj)) {
					obj.css('top', orignTop);
					obj.css('left', orignLeft);
					obj.css('width', orignWidth);
					obj.css('height', orignHeight);
					
				}
				else {
					var interObjs = interaction.screen.getIntersectObj(obj, dir);
					if (interObjs != null && interObjs.length > 0) {
						var result = interaction.screen.dockArea(obj, interObjs, dir);
						if (!result) {
							obj.css('top', orignTop);
							obj.css('left', orignLeft);
							obj.css('width', orignWidth);
							obj.css('height', orignHeight);
						}
					}
				}	
				//obj.children('dd').css('height', (obj.innerHeight() - obj.children('dt').outerHeight(true)));
				interaction.screen.updateAreaBody(obj);
                //update area info
                interaction.screen.showAreaInfo(obj);
			}
		},
		adjustMove : function(obj, dir){
			var orignLeft = obj.position().left;
			var orignTop = obj.position().top;
			var orignWidth = obj.innerWidth();
			var orignHeight = obj.innerHeight();
			var changed = true;
			switch(dir){
				case interaction.screen.LEFT:
				if(orignLeft >= interaction.screen.gridX){
					obj.css('left',orignLeft-interaction.screen.gridX);
				}else if(orignLeft > 0){
					obj.css('left', 0);
				}else{
					changed = false;
				}
				break;
				case interaction.screen.UP:
				if(orignTop >= interaction.screen.gridY){
					obj.css('top',orignTop-interaction.screen.gridY);
				}else if(orignTop > 0){
					obj.css('top', 0 );
				}else{
					changed = false;
				}
				break;
				case interaction.screen.RIGHT:
				if((orignLeft+orignWidth + interaction.screen.gridX) <= interaction.screen.width){
					obj.css('left',orignLeft+interaction.screen.gridX);
				}else if(orignLeft+orignWidth < interaction.screen.width){
					obj.css('left', interaction.screen.width - orignWidth);
				}else{
					changed = false;
				}
				break;
				case interaction.screen.DOWN:
				if((orignTop+orignHeight + interaction.screen.gridY) <= interaction.screen.height){
					obj.css('top',orignTop+interaction.screen.gridY);
				}else if(orignTop+orignHeight < interaction.screen.height){
					obj.css('top',interaction.screen.height-orignHeight);
				}else{
					changed = false;
				}
				break;
			}
			
			if(changed){
                var interObjs = interaction.screen.getIntersectObj(obj, dir);
	            if (interObjs != null && interObjs.length > 0) {
	            	var result = interaction.screen.dockArea(obj, interObjs, dir);	         	
	                if (interaction.screen.OverlappingOne()) {
	                	switch(dir){
	                		case interaction.screen.LEFT:
		                		obj.css('left', orignLeft - interaction.screen.gridX);
		                		obj.css('width', orignWidth);
		                		obj.css('height', orignHeight);
		                		break;
		                	case interaction.screen.UP:
		                		obj.css('top', orignTop - interaction.screen.gridY);
		                		obj.css('width', orignWidth);
		                		obj.css('height', orignHeight);
		                		break;
		                	case interaction.screen.RIGHT:
		                		obj.css('left', orignLeft + interaction.screen.gridX);
		                		obj.css('width', orignWidth);
		                		obj.css('height', orignHeight);
		                		break;
		                	case interaction.screen.DOWN:
		                		obj.css('top', orignTop + interaction.screen.gridY);
		                		obj.css('width', orignWidth);
		                		obj.css('height', orignHeight);
		                		break;
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
                interaction.screen.showAreaInfo(obj);
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
        createArea: function(id, screenID, type, title, minWidth, maxWidth, minHeight, maxHeight, callbackCreate, zIndex){
            zIndex = zIndex || interaction.screen.zIndex;
            if ("logo" != type && zIndex > interaction.screen.zIndex) {
                interaction.screen.zIndex = zIndex;
            }
            //$('#screen'+interaction.screen.screenID).append(interaction.screen._template(id, type, title));
            $('#screen'+screenID).append(interaction.screen._template(id, type, title));

            var area = $("#" + id);
			area.attr('tabindex', interaction.screen.tabIndex);
			interaction.screen.tabIndex++;
            area.click(function(event){
				
                var cur = $(this);
				if (interaction.screen.readonly) {
					interaction.screen.showAreaInfo(cur);
                	return;
            	}
				if(interaction.screen.curObj != null){
					if(interaction.screen.curObj.attr('id') == cur.attr('id')){
						cur.focus();
						return;
					}
					interaction.screen.curObj.unbind("keydown");
					interaction.screen.curObj.children('dt').removeClass('selected');
				}
				//cur.attr("tabindex",cur.css('z-index'));
				cur.focus();
				interaction.screen.curObj=cur;
				interaction.screen.curObj.children('dt').addClass('selected');
				interaction.screen.curObj.keydown(function(event){
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
						if(interaction.screen.curObj != null){
							interaction.screen.curObj.unbind("keydown");
							interaction.screen.curObj.children('dt').removeClass('selected');
							interaction.screen.curObj=null;
						}
					}
					if (event.ctrlKey) {
						switch(keyCode){
							case 74://j
							case 37://left
							interaction.screen.adjustResize($(this), interaction.screen.LEFT);
							dealed=true;	
							break;
							case 75://k
							case 38://up
							interaction.screen.adjustResize($(this), interaction.screen.UP);
							dealed=true;
							break;
							case 76://l
							case 39://right
							interaction.screen.adjustResize($(this), interaction.screen.RIGHT);
							dealed=true;
							break;
							case 77://m
							case 40://down
							interaction.screen.adjustResize($(this), interaction.screen.DOWN);
							dealed=true;
							break;
						}
					}else{
						switch(keyCode){
							case 74://j
							case 37://left
								interaction.screen.adjustMove($(this), interaction.screen.LEFT);
								dealed=true;	
							break;
							case 75://k
							case 38://up
								interaction.screen.adjustMove($(this), interaction.screen.UP);
								dealed=true;
							break;
							case 76://l
							case 39://right
								interaction.screen.adjustMove($(this), interaction.screen.RIGHT);
								dealed=true;
							break;
							case 77://m
							case 40://down
								interaction.screen.adjustMove($(this), interaction.screen.DOWN);
								dealed=true;
							break;
						}
					}
					if(dealed){
						event.preventDefault();
					}
				});
                interaction.screen.showAreaInfo(cur);
                if (cur.hasClass('movie') || cur.hasClass('image') || cur.hasClass('webpage') || cur.hasClass('interaction')) {
                    return;
                }
                /*if (interaction.screen.zIndex > cur.zIndex()) {
                    cur.css('z-index', ++interaction.screen.zIndex);
                }*/
            });
            area.css('z-index', zIndex);
			if (callbackCreate != null) {
				callbackCreate();
			}
            if (interaction.screen.readonly) {
                return;
            }
            
            $("#" + id + " .close").click(function(event){
                event.preventDefault();
                var tmp = id.split('_');
				var idx  = 0;
                if (tmp.length == 4 && 'image' == tmp[1]) { //删除照片区域       area_image_2_1
                    interaction.screen.removeImageArea(tmp[2], tmp[3]);
					idx=tmp[3];
                }else {
                    if (tmp.length == 3 && 'bton' == tmp[1]) { 
                    	interaction.screen.removeBtonArea(tmp[2], tmp[3]);
						idx=tmp[1];
                	}else {
                		if (tmp.length == 3 && 'movie' == tmp[1]) { //area_movie_2
                    		interaction.screen.removeMovieArea(tmp[2]);
                		}else {
                			if (tmp.length == 3 && 'webpage' == tmp[1]) {
                    			interaction.screen.removeWebpageArea(tmp[2]);
                			}else {
                				if (tmp.length == 3 && 'date' == tmp[1]) {
	                    			interaction.screen.removeDateArea(tmp[2]);
	                			}else {
	                				if (tmp.length == 3 && 'time' == tmp[1]) {
		                    			interaction.screen.removeTimeArea(tmp[2]);
		                			}else {
		                				if (tmp.length == 3 && 'weather' == tmp[1]) {
			                    			interaction.screen.removeWeatherArea(tmp[2]);
			                			}else {
			                				if (tmp.length == 3 && 'text' == tmp[1]) {
				                    			interaction.screen.removeTextArea(tmp[2]);
				                			}else {
				                				if (tmp.length == 3 && 'staticText' == tmp[1]) {
					                    			interaction.screen.removeSTextArea(tmp[2]);
					                			}else {
					                				if (tmp.length == 4 && 'btn' == tmp[1]) { //area_btn_2_1
									                    interaction.screen.removeBtnArea(tmp[2], tmp[3]);
														idx=tmp[3];
									                }else {
									                	interaction.screen.removeArea(tmp[1]);
									                }
					                			}
				                			}
			                			}
		                			}
	                			}
                			}
                		}
                    }
                }
				var tid = tmp[1] + (idx != 0 ? idx : '');
                interaction.screen.enable(tid);
                $("#" + id).resizable("destroy").remove();
                //关闭show title
                $('.tooltip').hide();
                /*
                var delete_img = 1;
                var delete_btn = 1;
                var delete_movie = 1;
                $('#tree_'+interaction.screen.screenID+' ul li').each(function() {
    				var _id = $(this).attr("id"); // 遍历获取符合该样式选择器的所有元素的ID
    				var treeObj = $.fn.zTree.getZTreeObj("tree");
					var node = treeObj.getNodeByTId(_id);
    				if(tid.indexOf('webpage') >=0 && node.iconSkin == 'webtab' || tid.indexOf('date') >=0 && node.iconSkin == 'date' || tid.indexOf('time') >=0 && node.iconSkin == 'time' || tid.indexOf('weather') >=0 && node.iconSkin == 'weather' || tid.indexOf('text') >=0 && node.iconSkin == 'text' || tid.indexOf('staticText') >=0 && node.iconSkin == 'stext') {
    					treeObj.removeNode(node);
    				}
    				if(tid.indexOf('movie') >=0 && node.iconSkin == 'movie' && delete_movie == 1) {
    					treeObj.removeNode(node);
    					delete_movie++;
    				}
    				if(tid.indexOf('image') >=0 && node.iconSkin == 'image' && delete_img == 1) {
    					treeObj.removeNode(node);
    					delete_img++;
    				}
    				if(tid.indexOf('btn') >=0 && node.iconSkin == 'btn' && delete_btn == 1) {
    					treeObj.removeNode(node);
    					delete_btn++;
    				}
				});
				*/
				//interaction.screen.nav_reset();
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
                grid: [interaction.screen.gridX, interaction.screen.gridY],
				start : function(event, ui){
					/**修改防止成绝对坐标**/
					ui.element.attr('top', ui.element.css('top'));
					ui.element.attr('left', ui.element.css('left'));
					//ui.originalPosition.top=ui.element.css('top');
					//ui.originalPosition.left=ui.element.css('left');
				},
                resize: function(event, ui){
                    var cur = ui.helper;
                    interaction.screen.showAreaInfo(cur, true);
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
							for(var i = interaction.screen.logoSize.length-1; i >= 0; i--){
								var size = interaction.screen.logoSize[i];
								if(width >= size){
									cur.css('width', size);
									break;
								}
							}
							
							for(var i = interaction.screen.logoSize.length-1; i >= 0; i--){
								var size = interaction.screen.logoSize[i];
								if(height >= size){
									cur.css('height', size);
									break;
								}
							}
							
						}else if(orignWidth != width){
							/*H*/
							for(var i = interaction.screen.logoSize.length-1; i >= 0; i--){
								var size = interaction.screen.logoSize[i];
								if(width >= size){
									cur.css('width', size);
									break;
								}
							}
							
						}else if(orignHeight != height){
							/*V*/
							for(var i = interaction.screen.logoSize.length-1; i >= 0; i--){
								var size = interaction.screen.logoSize[i];
								if(height >= size){
									cur.css('height', size);
									break;
								}
							}
						}
						
						
						interaction.screen.updateAreaBody(cur);
						interaction.screen.showAreaInfo(cur);
						return;
					}
					
					if(cur.attr('id') == 'area_date' || cur.attr('id') == 'area_weather'){
						var width = cur.innerWidth();
						var height = cur.innerHeight();
						if(orignWidth != width && orignHeight != height){
							/*angle*/
							for(var i = interaction.screen.DateWeatherSize.length-1; i >= 0; i--){
								var size = interaction.screen.DateWeatherSize[i];
								if(width >= size){
									if((cur.position().left + size) > interaction.screen.width) {
										cur.css('left', interaction.screen.width - size);
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
							
							for(var i = interaction.screen.DateWeatherSize.length-1; i >= 0; i--){
								var size = interaction.screen.DateWeatherSize[i];
								if(height >= size){
									if((cur.position().top + size) > interaction.screen.height) {
										cur.css('top', interaction.screen.height - size);
									}
									cur.css('height', size);
									break;
								}
							}
							
						}else if(orignWidth != width){
							/*H*/
							for(var i = interaction.screen.DateWeatherSize.length-1; i >= 0; i--){
								var size = interaction.screen.DateWeatherSize[i];
								if(width >= size){
									if((cur.position().left + size) > interaction.screen.width) {
										cur.css('left', interaction.screen.width - size);
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
							for(var i = interaction.screen.DateWeatherSize.length-1; i >= 0; i--){
								var size = interaction.screen.DateWeatherSize[i];
								if(height >= size){
									if((cur.position().top + size) > interaction.screen.height) {
										cur.css('top', interaction.screen.height - size);
									}
									if(cur.position().top < 0) {
										cur.css('top', 0);
									}
									cur.css('height', size);
									break;
								}
							}
						}
						
						
						interaction.screen.updateAreaBody(cur);
						interaction.screen.showAreaInfo(cur);
						return;
					}
                    if (interaction.screen.debug) {
                        console.info(ui);
                        console.info(event);
                        console.info($(this).innerWidth());
                        console.info('left:' + cur.css('width') + ', cur.left:' + cur.position().left + ", orignWidth:" + orignWidth + ", width:" + width + ",cur.width:" + cur.innerWidth() + ", orignHeight:" + orignHeight + ", height:" + height);
                    }
                    var dir = 0;//unkowned

                    if (orignLeft > ui.position.left) {
                        //to the border of left
                        if (cur.position().left < 0) {
                            if (interaction.screen.debug) {
                                console.info('width:' + width + ', left:' + cur.position().left + ', origWidth:' + orignWidth + ', wishWidth:' + (orignWidth + ui.originalPosition.left));
                            }
                            cur.css('width', width + cur.position().left);
                            cur.css('left', 0);
							interaction.screen.updateAreaBody(cur);
                            interaction.screen.showAreaInfo(cur);
                            return;
                        }
                        if (interaction.screen.debug) {
                            console.info('left:' + ui.position.left + ', cur.left:' + cur.position().left);
                        }
                        dir = interaction.screen.LEFT;
                    }else {
                    	if (width > orignWidth && height > orignHeight) {
                            dir = interaction.screen.RIGHT_DOWN;
                        }else {
                        	if (width > orignWidth) {
                                dir = interaction.screen.RIGHT;
                            }else {
                            	 if (ui.originalPosition.top > ui.position.top) {
                                    dir = interaction.screen.UP;
                                }
                                else {
                                	if (height > orignHeight) {
                                        dir = interaction.screen.DOWN;
                                    }
                                }
                                    
                            }
                        }
                    }
                    var changed = true;
                    //check only enlarge
                    if (dir > 0) {
                        if (dir == interaction.screen.RIGHT_DOWN) {
                            cur.css('height', orignHeight);
                            dir = interaction.screen.RIGHT;
                            var interObjs = interaction.screen.getIntersectObj(cur, dir);
                            if (interObjs != null && interObjs.length > 0) {
                                var result = interaction.screen.dockArea(cur, interObjs, dir);
                                if (!result) {
                                    cur.css('width', orignWidth);
                                }
                                else {
                                    hideMsg();
                                }
                            }
                            dir = interaction.screen.DOWN;
                            cur.css('height', height);
                            interObjs = interaction.screen.getIntersectObj(cur, dir);
                            if (interObjs != null && interObjs.length > 0) {
                                var result = interaction.screen.dockArea(cur, interObjs, dir);
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
                            var interObjs = interaction.screen.getIntersectObj(cur, dir);
                            if (interObjs != null && interObjs.length > 0) {
                                //TODO change to nearby
                                var result = interaction.screen.dockArea(cur, interObjs, dir);
                                if (!result) {
                                	cur.css('top', originTop);
									cur.css('left', orignLeft);
                                    cur.css('width', orignWidth);
                                    cur.css('height', orignHeight);
                                    changed = false;
                                    //showMsg(interaction.screen.warnOverlap, 'warn');
                                }
                                else {
                                    hideMsg();
                                }
                            }else {
                            	// 鼠标拉大 向左靠边 2013-12-20
                            	if(dir == interaction.screen.LEFT) {							
									if(width == interaction.screen.width) {
										cur.css('left', 0);	
										cur.css('width', interaction.screen.width);
										changed = false;
									}				 
								}
								if(dir == interaction.screen.UP) {							
									if(height == interaction.screen.height) {
										cur.css('top', 0);	
										cur.css('height', interaction.screen.height);
										changed = false;
									}				 
								}
                            }
                        }
                    }
					
                    if (changed) {
                        interaction.screen.adjustArea(cur);
                    }
                    
                    if (interaction.screen.debug) {
                        console.info("adJust" + changed + ", cur.innerHeight:" + cur.innerHeight() + ", dt.height:" + cur.children('dt').outerHeight(true));
                    }
                    //cur.children('dd').css('height', (cur.innerHeight() - cur.children('dt').outerHeight(true)));
					interaction.screen.updateAreaBody(cur);
                    //update area info
                    interaction.screen.showAreaInfo(cur);
                    return;
                }
            }).draggable({
                containment: "parent",
                scroll: true,
                distance: 5,
                delay: 300,
				grid: [interaction.screen.gridX, interaction.screen.gridY],
                drag: function(event, ui){
                    var cur = $(ui.helper);
                    interaction.screen.showAreaInfo(cur);
                },
                stoped: function(event, ui){
					//暂时废弃该功能
                    var cur = $(ui.helper);
                    var orignLeft = ui.originalPosition.left;
                    var orignTop = ui.originalPosition.top;
                    var left = ui.position.left;
                    var top = ui.position.top;
                    var dir = 0;//unkowned
                    if (interaction.screen.debug) {
                        console.info("draggable stop:" + cur.text() + ", orignLeft:" + orignLeft + ", orignTop:" + orignTop + ", left:" + left + ", top:" + top + ", dir:" + dir);
                    }
                    var ac = -0.2;//精度
                    if (interaction.screen.debug) {
                        console.info("acL:" + Math.abs(orignLeft - left) + ", acT:" + Math.abs(orignTop - top));
                    }
                    if ((orignLeft - left > ac) && (orignTop - top) > ac) {
                        dir = interaction.screen.DRAGE_LEFT_UP;
                    }
                    else 
                        if ((left - orignLeft) > ac && (orignTop - top) > ac) {
                            dir = interaction.screen.DRAGE_RIGHT_UP;
                        }
                        else 
                            if ((left - orignLeft) > ac && (top - orignTop) > ac) {
                                dir = interaction.screen.DRAGE_RIGHT_DOWN;
                            }
                            else 
                                if ((orignLeft - left) > ac && (top - orignTop) > ac) {
                                    dir = interaction.screen.DRAGE_LEFT_DOWN;
                                }
                                else 
                                    if ((orignLeft - left) > ac) {
                                        dir = interaction.screen.DRAGE_LEFT;
                                    }
                                    else 
                                        if ((orignTop - top) > ac) {
                                            dir = interaction.screen.DRAGE_UP;
                                        }
                                        else 
                                            if ((left - orignLeft) > ac) {
                                                dir = interaction.screen.DRAGE_RIGHT;
                                            }
                                            else 
                                                if ((top - orignTop) > ac) {
                                                    dir = interaction.screen.DRAGE_DOWN;
                                                }
                    
                    if (interaction.screen.debug) {
                        console.info("draggable " + cur.text() + ", orignLeft:" + orignLeft + ", orignTop:" + orignTop + ", left:" + left + ", top:" + top + ", dir:" + dir);
                    }
                    changed = false;
                    if (dir > 0) {
                        var interObjs = interaction.screen.getIntersectObj(cur, dir);
                        if (interObjs != null && interObjs.length > 0) {
                            //TODO change to nearby
                            cur.css('left', orignLeft);
                            cur.css('top', orignTop);
                        }
                    }
                    
                    interaction.screen.showAreaInfo(cur);
                }
            });
            interaction.screen.zIndex++;
            /*
			$('.movie').draggable({
                containment: "parent",
                scroll: true,
                distance: 5,
                delay: 300,
				grid: [interaction.screen.gridX, interaction.screen.gridY],
                drag: function(event, ui){
                    var cur = $(ui.helper);
                    interaction.screen.showAreaInfo(cur);
                },
                stoped: function(event, ui){
					//暂时废弃该功能
					alert(event);
                    var cur = $(ui.helper);
                    var orignLeft = ui.originalPosition.left;
                    var orignTop = ui.originalPosition.top;
                    var left = ui.position.left;
                    var top = ui.position.top;
                    var dir = 0;//unkowned
                    if (interaction.screen.debug) {
                        console.info("draggable stop:" + cur.text() + ", orignLeft:" + orignLeft + ", orignTop:" + orignTop + ", left:" + left + ", top:" + top + ", dir:" + dir);
                    }
                    var ac = -0.2;//精度
                    if (interaction.screen.debug) {
                        console.info("acL:" + Math.abs(orignLeft - left) + ", acT:" + Math.abs(orignTop - top));
                    }
                    if ((orignLeft - left > ac) && (orignTop - top) > ac) {
                        dir = interaction.screen.DRAGE_LEFT_UP;
                    }
                    else 
                        if ((left - orignLeft) > ac && (orignTop - top) > ac) {
                            dir = interaction.screen.DRAGE_RIGHT_UP;
                        }
                        else 
                            if ((left - orignLeft) > ac && (top - orignTop) > ac) {
                                dir = interaction.screen.DRAGE_RIGHT_DOWN;
                            }
                            else 
                                if ((orignLeft - left) > ac && (top - orignTop) > ac) {
                                    dir = interaction.screen.DRAGE_LEFT_DOWN;
                                }
                                else 
                                    if ((orignLeft - left) > ac) {
                                        dir = interaction.screen.DRAGE_LEFT;
                                    }
                                    else 
                                        if ((orignTop - top) > ac) {
                                            dir = interaction.screen.DRAGE_UP;
                                        }
                                        else 
                                            if ((left - orignLeft) > ac) {
                                                dir = interaction.screen.DRAGE_RIGHT;
                                            }
                                            else 
                                                if ((top - orignTop) > ac) {
                                                    dir = interaction.screen.DRAGE_DOWN;
                                                }
                    
                    if (interaction.screen.debug) {
                        console.info("draggable " + cur.text() + ", orignLeft:" + orignLeft + ", orignTop:" + orignTop + ", left:" + left + ", top:" + top + ", dir:" + dir);
                    }
                    changed = false;
                    if (dir > 0) {
                        var interObjs = interaction.screen.getIntersectObj(cur, dir);
                        if (interObjs != null && interObjs.length > 0) {
                            //TODO change to nearby
                            cur.css('left', orignLeft);
                            cur.css('top', orignTop);
                        }
                    }
                    
                    interaction.screen.showAreaInfo(cur);
                }
            });
            /*
			interaction.screen.draggable_l('.movie', 'parent');
			interaction.screen.draggable_l('.Image1', 'parent');
			interaction.screen.draggable_l('.Image2', 'parent');
			interaction.screen.draggable_l('.Image3', 'parent');
			interaction.screen.draggable_l('.Image4', 'parent');
			interaction.screen.draggable_l('.webpage', 'parent');
			interaction.screen.draggable_l('.date', 'parent');
			interaction.screen.draggable_l('.time', 'parent');
			interaction.screen.draggable_l('.weather', 'parent');
			interaction.screen.draggable_l('.text', 'parent');
			interaction.screen.draggable_l('.staticText', 'parent');
			//interaction.screen.draggable_l('.btnGroup', 'parent');
			interaction.screen.draggable_l('.bton', 'parent');
			*/
            
        },
       
        //Test
        draggable_l: function(type, containment) {
            $(type).draggable({
                containment: containment,
                scroll: true,
                distance: 5,
                delay: 300,
				grid: [interaction.screen.gridX, interaction.screen.gridY],
                drag: function(event, ui){
                    var cur = $(ui.helper);
                    interaction.screen.showAreaInfo(cur);
                },
                stoped: function(event, ui){
					//暂时废弃该功能
					//alert(event);
                    var cur = $(ui.helper);
                    var orignLeft = ui.originalPosition.left;
                    var orignTop = ui.originalPosition.top;
                    var left = ui.position.left;
                    var top = ui.position.top;
                    var dir = 0;//unkowned
                    if (interaction.screen.debug) {
                        console.info("draggable stop:" + cur.text() + ", orignLeft:" + orignLeft + ", orignTop:" + orignTop + ", left:" + left + ", top:" + top + ", dir:" + dir);
                    }
                    var ac = -0.2;//精度
                    if (interaction.screen.debug) {
                        console.info("acL:" + Math.abs(orignLeft - left) + ", acT:" + Math.abs(orignTop - top));
                    }
                    if ((orignLeft - left > ac) && (orignTop - top) > ac) {
                        dir = interaction.screen.DRAGE_LEFT_UP;
                    }
                    else 
                        if ((left - orignLeft) > ac && (orignTop - top) > ac) {
                            dir = interaction.screen.DRAGE_RIGHT_UP;
                        }
                        else 
                            if ((left - orignLeft) > ac && (top - orignTop) > ac) {
                                dir = interaction.screen.DRAGE_RIGHT_DOWN;
                            }
                            else 
                                if ((orignLeft - left) > ac && (top - orignTop) > ac) {
                                    dir = interaction.screen.DRAGE_LEFT_DOWN;
                                }
                                else 
                                    if ((orignLeft - left) > ac) {
                                        dir = interaction.screen.DRAGE_LEFT;
                                    }
                                    else 
                                        if ((orignTop - top) > ac) {
                                            dir = interaction.screen.DRAGE_UP;
                                        }
                                        else 
                                            if ((left - orignLeft) > ac) {
                                                dir = interaction.screen.DRAGE_RIGHT;
                                            }
                                            else 
                                                if ((top - orignTop) > ac) {
                                                    dir = interaction.screen.DRAGE_DOWN;
                                                }
                    
                    if (interaction.screen.debug) {
                        console.info("draggable " + cur.text() + ", orignLeft:" + orignLeft + ", orignTop:" + orignTop + ", left:" + left + ", top:" + top + ", dir:" + dir);
                    }
                    changed = false;
                    if (dir > 0) {
                        var interObjs = interaction.screen.getIntersectObj(cur, dir);
                        if (interObjs != null && interObjs.length > 0) {
                            //TODO change to nearby
                            cur.css('left', orignLeft);
                            cur.css('top', orignTop);
                        }
                    }
                    
                    interaction.screen.showAreaInfo(cur);
                }
            });	
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
            
            if (interaction.screen.debug) {
                console.info("showAreaInfo  cur:" + cur +", width: " + cur.width() + ", height: " + cur.height()+ ", innerWidth: " + cur.innerWidth() + ", innerHeight: " + cur.innerHeight());
            }
			var range = interaction.screen.getRealRange(cur);
			/*
            var xp = $('#areaX');
            xp.text(range.x + 'px');
            var yp = $('#areaY');
            yp.text(range.y + 'px');
            var wp = $('#areaWidth');
            wp.text(range.w + 'px');
            var hp = $('#areaHeight');
            hp.text(range.h + 'px');
            var wpp = $('#areaWidthPercent');
            wpp.text(range.wp + '%');
            var hpp = $('#areaHeightPercent');
            hpp.text(range.hp + '%');
            */
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
			var curWidth = Math.round(cur.innerWidth());
            var curHeight = Math.round(cur.innerHeight());
            var widthPercent = Math.round(curWidth / interaction.screen.width * 10000) / 100.00;
            if (widthPercent > 100) {
                widthPercent = 100;
            }
            var heightPercent = Math.round(curHeight / interaction.screen.height * 10000) / 100.00;
            if (heightPercent > 100) {
                heightPercent = 100;
            }
            
            var width = interaction.screen.realWidth * curWidth / interaction.screen.width;
            var height = interaction.screen.realHeight * curHeight / interaction.screen.height;
            //adjust show
            if (width % 2 != 0) {
                width--;
            }
            if (height % 2 != 0) {
                height--;
            }
			width = Math.round(width);
			height = Math.round(height);
			var x = Math.round((cur.position().left * interaction.screen.realWidth) / interaction.screen.width);
			var y = Math.round(cur.position().top * interaction.screen.realHeight / interaction.screen.height);
			
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
            //$('#areaInfo').hide();
            var xp = $('#areaX');
            xp.text(0 + 'px');
            var yp = $('#areaY');
            yp.text(0 + 'px');
            var wp = $('#areaWidth');
            wp.text(0 + 'px');
            var hp = $('#areaHeight');
            hp.text(0 + 'px');
            var wpp = $('#areaWidthPercent');
            wpp.text(0 + '%');
            var hpp = $('#areaHeightPercent');
            hpp.text(0 + '%');
        },
        removeArea: function(type){
            eval('var area =interaction.screen.' + type + '; if(area != null && area.areaId != undefined){interaction.screen.deletes.push({areaId:area.areaId,type:"' + type + '"});};interaction.screen.' + type + '=null;')
            interaction.screen.hideAreaInfo();
            
        },
        removeImageArea: function(index, num){
            var image = interaction.screen.image;
            var id = 'area_image_' + index + '_' + num;
            for (var i = 0; i < image.length; i++) {
                if (image[i].id == id) {
                    if (image[i].areaId != undefined) {
                        interaction.screen.deletes.push({
                            areaId: image[i].areaId,
                            type: 'image'
                        });
                    }
                    image.splice(i, 1);
                    break;
                }
            }
            interaction.screen.hideAreaInfo();
        },
        removeBtnArea: function(index, num){
            var btn = interaction.screen.btn;
            var id = 'area_btn_' + index + '_' + num;
            for (var i = 0; i < btn.length; i++) {
                if (btn[i].id == id) {
                    if (btn[i].areaId != undefined) {
                        interaction.screen.deletes.push({
                            areaId: btn[i].areaId,
                            type: 'btn'
                        });
                    }
                    btn.splice(i, 1);
                    break;
                }
            }
            interaction.screen.hideAreaInfo();
        },
        removeMovieArea: function(index){
            var movie = interaction.screen.movie;
            var id = 'area_movie_' + index;
            for (var i = 0; i < movie.length; i++) {
                if (movie[i].id == id) {
                    if (movie[i].areaId != undefined) {
                        interaction.screen.deletes.push({
                            areaId: movie[i].areaId,
                            type: 'movie'
                        });
                    }
                    movie.splice(i, 1);
                    break;
                }
            }
            interaction.screen.hideAreaInfo();
        },
        removeWebpageArea: function(index){
            var webpage = interaction.screen.webpage;
            var id = 'area_webpage_' + index;
            for (var i = 0; i < webpage.length; i++) {
                if (webpage[i].id == id) {
                    if (webpage[i].areaId != undefined) {
                        interaction.screen.deletes.push({
                            areaId: webpage[i].areaId,
                            type: 'webpage'
                        });
                    }
                    webpage.splice(i, 1);
                    break;
                }
            }
            interaction.screen.hideAreaInfo();
        },
        removeDateArea: function(index){
            var date = interaction.screen.date;
            var id = 'area_date_' + index;
            for (var i = 0; i < date.length; i++) {
                if (date[i].id == id) {
                    if (date[i].areaId != undefined) {
                        interaction.screen.deletes.push({
                            areaId: date[i].areaId,
                            type: 'date'
                        });
                    }
                    date.splice(i, 1);
                    break;
                }
            }
            interaction.screen.hideAreaInfo();
        },
        removeTimeArea: function(index){
            var time = interaction.screen.time;
            var id = 'area_time_' + index;
            for (var i = 0; i < time.length; i++) {
                if (time[i].id == id) {
                    if (time[i].areaId != undefined) {
                        interaction.screen.deletes.push({
                            areaId: time[i].areaId,
                            type: 'time'
                        });
                    }
                    time.splice(i, 1);
                    break;
                }
            }
            interaction.screen.hideAreaInfo();
        },
        removeWeatherArea: function(index){
            var weather = interaction.screen.weather;
            var id = 'area_weather_' + index;
            for (var i = 0; i < weather.length; i++) {
                if (weather[i].id == id) {
                    if (weather[i].areaId != undefined) {
                        interaction.screen.deletes.push({
                            areaId: weather[i].areaId,
                            type: 'weather'
                        });
                    }
                    weather.splice(i, 1);
                    break;
                }
            }
            interaction.screen.hideAreaInfo();
        },
        removeTextArea: function(index){
            var text = interaction.screen.text;
            var id = 'area_text_' + index;
            for (var i = 0; i < text.length; i++) {
                if (text[i].id == id) {
                    if (text[i].areaId != undefined) {
                        interaction.screen.deletes.push({
                            areaId: text[i].areaId,
                            type: 'text'
                        });
                    }
                    text.splice(i, 1);
                    break;
                }
            }
            interaction.screen.hideAreaInfo();
        },
        removeSTextArea: function(index){
            var stext = interaction.screen.staticText;
            var id = 'area_staticText_' + index;
            for (var i = 0; i < stext.length; i++) {
                if (stext[i].id == id) {
                    if (stext[i].areaId != undefined) {
                        interaction.screen.deletes.push({
                            areaId: stext[i].areaId,
                            type: 'staticText'
                        });
                    }
                    stext.splice(i, 1);
                    break;
                }
            }
            interaction.screen.hideAreaInfo();
        },
        _template: function(id, type, name){
            /*return '<div id="'+id+'" class="ui-widget-content draggable bg-'+type+' '+type+'">'
             + '<div class="title ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">'
             + (this.enableDeleteButton ?  '<a href="javascript:void();" class="ui-dialog-titlebar-close ui-corner-all" role="button" onclick="interaction.screen.destory(event,this);"><span class="ui-icon ui-icon-closethick">close</span></a>' : '')
             + '<span class="ui-widget-header">'+name+'</span>'
             + '<span class="xy" ></span>'
             + '</div>'
             +'</div>';*/
            //onclick="interaction.screen.destory(this,event);"
            if (interaction.screen.readonly) {
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
            if (tmp.length == 4 && 'image' == tmp[1]) {
                //删除照片区域
                interaction.screen.removeImageArea(tmp[2]);
            }
            else {
                interaction.screen.removeArea(tmp[1]);
            }
            //$('#'+tmp[1]).button('enable');
            this.enable(tmp[1]);
            $("#" + id).resizable("destroy");
            $("#" + id).remove();
            //关闭show title
            $('.tooltip').hide();
            
        },
        imagePage: function(curpage, type, screenID){
            var folderId = $('#folderId').val();
            if (folderId == '') {
                folderId = -1;
            }
            $.get('/interaction/images/' + curpage + '?type=' + type + '&screenID=' + screenID + '&folder_id=' + folderId + '&t=' + new Date().getTime(), function(data){
                $('#imageContent').html(data);
            });
        },
        imageFilter: function(type){
            $('#folderId').val($('#filterFolder').val());
            interaction.screen.imagePage(1, type);
        },
        setImage: function(mid, type, screenID){
            if (type == 'bg') {
                var bigsrc = $('#img_' + mid).attr('bigsrc');
                interaction.screen.showBg(bigsrc, mid, screenID);
                interaction.screen.bgimg.push({
		  			screenID: screenID,
		 			mediaId: mid
		       	});
            }else {
            	if (type == 'logo') {
                    var id = 'area_logo';
                    var title = 'Logo';
                    var src = $('#img_' + mid).attr('src');
                    interaction.screen.showLogo(id, title, -1, -1, -1, -1, src);
                    interaction.screen.logo = {
                        mediaId: mid,
                        id: id
                    };
                }
            }
        },
        openImageLibrary: function(type, screenID){
            var t = 'Media Library';
            var a = '/interaction/images/?type=' + type + '&screenID='+ screenID + '&width=900&height=450&t=' + new Date().getTime();
            var g = '';
            tb_show(t, a, g);
            
            if (true) {
                return;
            }
            //old code
            //显示加载信息
            showLoading();
            /*
            $.get('/interaction/images?type=' + type + '&t=' + new Date().getTime(), function(data){
                hideLoading();
                $('.screen').after(data);
                $('#dialog').dialog({
                    modal: true,
                    width: 800,
                    height: 600,
                    create: function(event, ui){
                        $('.picture img').click(function(e){
                            if (type == 'bg') {
								var id = 'area_bg';
								alert(id);
                                interaction.screen.showBg(e.target.style.backgroundImage.replace('tiny', 'main'));
                                interaction.screen.bg.push({
                                    folderID: node.id,
                                    screenID: screenID,
                                    mediaId: e.target.id,
                                    id: id
                                });
                                
                            }
                            else 
                                if (type == 'logo') {
                                    var id = 'area_logo';
                                    var title = 'Logo';
                                    interaction.screen.showLogo(id, title, -1, -1, -1, -1, e.target.style.backgroundImage);
                                    interaction.screen.logo = {
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
            */
        },
        openTimeDialog: function(type){
            var t = '';
            var a = '/interaction/' + type + 's?type=' + type + '&t=' + new Date().getTime();
            var g = '';
            tb_show(t, a, g);
            return;
            //显示加载信息
            //showLoading();
            $.get('/interaction/' + type + 's?type=' + type + '&t=' + new Date().getTime(), function(data){
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
                            eval('interaction.screen.' + type + '=value;interaction.screen.show' + up + '("' + id + '","' + title + '", -1, -1, -1, -1);interaction.screen.' + type + '.id="' + id + '";');
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
        openBtnGroupDialog: function(type) {
        	var t = '';
            var a = '/interaction/' + type + 's?type=' + type + '&t=' + new Date().getTime();
            var g = '';
            tb_show(t, a, g);
           	return;
        },
        setBtnGroupSetting: function(type){
            var areaWidth = $('#area_width').val();
            var areaHeight = $('#area_height').val();
            var col = $('#col').val();
            var row = $('#row').val();
            var left = $('#left').val();
            var top = $('#top').val();
            var width = $('#width').val();
            var height = $('#height').val();
            var spaceBetween = $('#space_between').val();
            var lineSpace = $('#line_space').val();
            /*
            var value = {
                format: format,
                family: family,
                fontSize: fontSize,
                color: color,
                bold: bold
            };
            */
            //var type = 'date';
            var up = firstToUpperCase(type);
            var id = 'area_' + type + '_' + interaction.screen.screenID;
            var title = up;
            var btnId = 'area_btn_' + interaction.screen.screenID;
            //eval('interaction.screen.show' + up + '("' + id + '","' + title + '", -1, -1, -1, -1);interaction.screen.' + type + '.id="' + id + '";');
            //eval('interaction.screen.show' + up + '("' + id + '","' + title + '", -1, -1, -1, -1);');
            tb_remove();
            eval('interaction.screen.show' + up + '("' + id + '","' + title + '", 0, 0, "'+areaWidth+'", "'+areaHeight+'");');
            //eval('interaction.screen.showBtn' + '("' + btnId + '","' + title + '", 0, 0, "' + width + '", "' + height + '");');
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
            eval('interaction.screen.' + type + '=value;interaction.screen.show' + up + '("' + id + '","' + title + '", -1, -1, -1, -1);interaction.screen.' + type + '.id="' + id + '";');
            tb_remove();
        },
        changeDateFormat: function(obj){
            $.get('/interaction/get_date_format/' + $(obj).val() + '?t=' + new Date().getTime(), function(data){
                $('#datePreview').html(data);
            });
        },
        changeTimeFormat: function(obj){
            $.get('/interaction/get_time_format/' + $(obj).val() + '?t=' + new Date().getTime(), function(data){
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
            eval('interaction.screen.' + type + '=value;interaction.screen.show' + up + '("' + id + '","' + title + '", -1, -1, -1, -1);interaction.screen.' + type + '.id="' + id + '";');
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
                    interaction.screen.changeFontColor('#' + hex);
                    $(el).ColorPickerHide();
                }
            });
        },//是否有重叠区域，并高亮边框提示
        isMovieOverlapping: function(){
            var result = false;
            if (interaction.screen.movie != null && interaction.screen.image.length > 0) {
                var parent = $('#' + interaction.screen.movie.id);
                x = parent.position().left;
                y = parent.position().top;
                w = parent.innerWidth();
                h = parent.innerHeight();
                
                
                //console.log('x:' + x +',y:' + y + ',w:' + w +',h:' + h);
                for (var i = 0; i < interaction.screen.image.length; i++) {
                    cur = $('#' + interaction.screen.image[i].id);
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
            if (interaction.screen.image.length > 1) {
                for (var i = 0; i < interaction.screen.image.length; i++) {
                    var parent = $('#' + interaction.screen.image[i].id);
                    x = parent.position().left;
                    y = parent.position().top;
                    w = parent.innerWidth();
                    h = parent.innerHeight();
                    for (var j = i + 1; j < interaction.screen.image.length; j++) {
                        cur = $('#' + interaction.screen.image[j].id);
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
                            cur.css('borderWidth', '1px').css('borderStyle', 'solid').css('borderColor', 'red');
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
			if(interaction.screen.logo != null){
				var cur = $('#area_logo');
				var areas = $('#screen dl');
				if(areas.length <= 1){
					result = false;
					return result;
				}
				
				for (var i = 0; i < areas.length; i++) {
					if (areas[i].id == 'area_movie' || areas[i].id.indexOf('area_image') != -1 || areas[i].id == 'area_webpage' || areas[i].id.indexOf('area_interaction') != -1) {
						var next = $(areas[i]);
						if (interaction.screen.isContains(next, cur)) {
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
            var result = false;
            var divs = $('#centerframe div');

            for(var x=0; x < divs.length; x++) {
            	var screen = $(divs[x]);
            	var id = screen.attr('id');
            	var t_id = id.substr(6, id.length);
            	if(id.indexOf('screen') != -1) {
            		var areas = $('#' + id + ' dl');
            		if(areas.length > 0) {
            			for(var i = 0; i < areas.length; i++) {
            				var cur = $(areas[i]);
            				for(var j = (i+1); j < areas.length; j++) {
            					if(areas[i].id != areas[j].id && ((areas[i].id.indexOf('image_'+t_id) != -1 || areas[i].id.indexOf('movie_'+t_id) != -1 || areas[i].id.indexOf('webpage_'+t_id) != -1) && (areas[j].id.indexOf('image_'+t_id) != -1 || areas[j].id.indexOf('movie_'+t_id) != -1 || areas[j].id.indexOf('webpage_'+t_id) != -1) || (areas[i].id.indexOf('time_'+t_id) != -1 || areas[i].id.indexOf('weather_'+t_id) != -1 || areas[i].id.indexOf('date_'+t_id) != -1) && (areas[j].id.indexOf('time_'+t_id) != -1 || areas[j].id.indexOf('weather_'+t_id) != -1 || areas[j].id.indexOf('date_'+t_id) != -1))) {
									var next = $(areas[j]);
									if(interaction.screen.isIntersect(cur, next)){
										next.css('borderWidth', '1px').css('borderStyle', 'solid').css('borderColor', 'red');
										result = true;
										break;
									}else{
										next.css('borderWidth', '').css('borderStyle', '').css('borderColor', '');
									}
								}else {
									if(areas[i].id != areas[j].id && areas[i].id.indexOf('btn_'+t_id) != -1 && areas[j].id.indexOf('btn_'+t_id) != -1) {
										var next = $(areas[j]);
										if(interaction.screen.isIntersect(cur, next)){
											next.css('borderWidth', '1px').css('borderStyle', 'solid').css('borderColor', 'red');
											result = true;
											break;
										}else{
											next.css('borderWidth', '').css('borderStyle', '').css('borderColor', '');
										}
									}else {
										continue;
									}
									//continue;
								}
            				}
            				if(result){
								break;
							}
            			}
            		}
            	}            	
            }
            
            /*
			var areas = $('#screen dl');
			//one or less area ignore
			if(areas.length <= 1){
				return false;
			}
			var result = false;
			for(var i =0 ;i < areas.length; i++){
				if(areas[i].id == 'area_logo' || areas[i].id == 'area_text' || areas[i].id == 'area_staticText' || areas[i].id == 'area_mask'){
					continue;
				}
				
				var cur = $(areas[i]);
				for(var j = (i+1); j < areas.length; j++){
					// test
					if(template.screen.template_type) {
						if(areas[j].id == 'area_staticText' || areas[j].id == 'area_text' || areas[j].id == 'area_mask' || (areas[i].id=='area_logo'&& (areas[j].id == 'area_movie' || areas[j].id.indexOf('area_image') != -1 || areas[j].id == 'area_weather' || areas[j].id == 'area_webpage')) || (areas[j].id == 'area_logo' && ((areas[i].id == 'area_movie' || areas[i].id.indexOf('area_image') != -1 || areas[i].id == 'area_weather' || areas[i].id == 'area_webpage'))) || (areas[i].id=='area_weather'&& (areas[j].id == 'area_movie' || areas[j].id.indexOf('area_image') != -1 || areas[j].id == 'area_webpage' || areas[j].id == 'area_staticText')) || (areas[j].id == 'area_weather' && ((areas[i].id == 'area_movie' || areas[i].id.indexOf('area_image') != -1 || areas[i].id == 'area_webpage' || areas[i].id == 'area_staticText'))) || (areas[i].id=='area_date'&& (areas[j].id == 'area_movie' || areas[j].id.indexOf('area_image') != -1 || areas[j].id == 'area_webpage' || areas[j].id == 'area_staticText')) || (areas[j].id == 'area_date' && ((areas[i].id == 'area_movie' || areas[i].id.indexOf('area_image') != -1 || areas[i].id == 'area_webpage' || areas[i].id == 'area_staticText'))) || (areas[i].id=='area_time'&& (areas[j].id == 'area_movie' || areas[j].id.indexOf('area_image') != -1 || areas[j].id == 'area_webpage' || areas[j].id == 'area_staticText')) || (areas[j].id == 'area_time' && ((areas[i].id == 'area_movie' || areas[i].id.indexOf('area_image') != -1 || areas[i].id == 'area_webpage' || areas[i].id == 'area_staticText')))){
							continue;
						}
					}else {
						if(areas[j].id == 'area_text' || areas[j].id == 'area_logo' || areas[j].id == 'area_mask' || areas[j].id == areas[i].id){
							continue;
						}
					}
					var next = $(areas[j]);
					if(template.screen.isIntersect(cur, next)){
						//next.css('borderWidth', '0px').css('borderStyle', 'solid').css('borderColor', 'red');
						$('#'+areas[j].id+' dd').css('background-color', 'red');
						result = true;
						break;
					}else{
						next.css('borderWidth', '').css('borderStyle', '').css('borderColor', '');
					}
				}
				if(result){
					break;
				}
			}*/
            
            return result;
        },
        OverlappingOne: function(){
			var areas = $('#screen dl');
			//one or less area ignore
			if(areas.length <= 1){
				return false;
			}
			var result = false;
			for(var i =0 ;i < areas.length; i++){
				if(areas[i].id == 'area_text' || areas[i].id == 'area_staticText' || areas[i].id == 'area_logo'){
					continue;
				}
				
				var cur = $(areas[i]);
				for(var j = (i+1); j < areas.length; j++){
					if(areas[j].id == 'area_text' || areas[j].id == 'area_staticText' || areas[j].id == 'area_logo' || areas[j].id == areas[i].id){
						continue;
					}

					var next = $(areas[j]);
					if(interaction.screen.isIntersect(cur, next)){
						next.css('borderWidth', '1px').css('borderStyle', 'solid').css('borderColor', 'red');
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
            var x, y, w, h, zindex, areaId, name, lastCount, screenID, folderID, treeID, id, str_id, projectName, treeName, index;
			var treeObj = $.fn.zTree.getZTreeObj("tree");
  			var nodes = treeObj.transformToArray(treeObj.getNodes());
  			/*
  			if($('#touchName').val() == '') {
  				projectName = 'Project' + new Date().getTime();
  			}else {
  				projectName = $('#touchName').val();
  			}*/
  			projectName = $('#touchName').val();
  			
           	var treejson = '{ id:1, pId:0, name:"'+projectName+'", open:true, iconSkin:"touch", noR:true},{ id:2, pId:1, name:"'+nodes[1].name+'", checked:true, open:true, iconSkin:"mainPage"},';
           	var treejsonpls = '{ id:1, pId:0, name:"'+projectName+'", open:true, iconSkin:"touch", noR:true},{ id:2, pId:1, name:"'+nodes[1].name+'", checked:true, open:true, iconSkin:"mainPage"},';
           	var p_id = 2;
           	var f_id = 1;
           	var p_change_id = '';
           	var change_id = '';
           	var change_area_id = '';
          	for(var i=2; i < nodes.length; i++){
          		if(nodes[i].iconSkin == 'page') {
          			//treejson = treejson + '{id:'+ (i+1) +', pId:1'+', name:"'+nodes[i].name+'", checked:true, open:true, iconSkin:"'+nodes[i].iconSkin+'"},';
          			treejson = treejson + '{id:'+ (i+1) +', pId:1'+', name:"Page'+i+'", checked:true, open:true, iconSkin:"'+nodes[i].iconSkin+'"},';
          			//treejsonpls = treejsonpls + '{id:'+ (i+1) +', pId:1'+', name:"'+nodes[i].name+'", checked:true, open:true, iconSkin:"'+nodes[i].iconSkin+'"},';
          			treejsonpls = treejsonpls + '{id:'+ (i+1) +', pId:1'+', name:"Page'+i+'", checked:true, open:true, iconSkin:"'+nodes[i].iconSkin+'"},';
          			p_id = i+1;
          			if(f_id != nodes[i].id) {
	          			p_change_id = p_change_id + f_id + '_' + nodes[i].pId + ',';
	          		}
          		}else {
          			//treejson = treejson + '{id:'+ (i+1) +', pId:'+p_id+', name:"'+nodes[i].name+'", checked:true, open:true, iconSkin:"'+nodes[i].iconSkin+'"},';
          			treejson = treejson + '{id:'+ (i+1) +', pId:'+p_id+', name:"Page'+2+'", checked:true, open:true, iconSkin:"'+nodes[i].iconSkin+'"},';
          			change_id = change_id + p_id + '_' + nodes[i].pId + ',';
          			change_area_id = change_area_id + (i+1) + '_' + nodes[i].id + ',';
          		}
          		lastCount = i+1;
         	}
			p_change_id = p_change_id.substring(0, p_change_id.length-1);
			change_id = change_id.substring(0, change_id.length-1);
			change_area_id = change_area_id.substring(0, change_area_id.length-1);
			
            if (interaction.screen.movie.length > 0) {
                data.movie = [];
                for (var i = 0; i < interaction.screen.movie.length; i++) {
                    var parent = $('#' + interaction.screen.movie[i].id);
                    x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    y = parent.css('top');
                    y = y.substring(0, y.length-2);
                    w = parent.innerWidth();
                    h = parent.innerHeight();
                    zindex = parent.zIndex();
                    areaId = interaction.screen.movie[i].areaId == undefined ? 0 : interaction.screen.movie[i].areaId;
                    screenID = interaction.screen.movie[i].screenID;
                    name = 'Movie';
                    data.movie[i] = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"areaId":' + areaId + ',"name":"' + name + '","zindex":' + zindex + ',"screenID":' + screenID + '}';
                }
            }
            
            //设置照片的相关属性
            if (interaction.screen.image.length > 0) {
                data.image = [];
                for (var i = 0; i < interaction.screen.image.length; i++) {
                    var parent = $('#' + interaction.screen.image[i].id);
                    x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    y = parent.css('top');
                    y = y.substring(0, y.length-2);
                    w = parent.innerWidth();
                    h = parent.innerHeight();
                    zindex = parent.zIndex();
                    areaId = interaction.screen.image[i].areaId == undefined ? 0 : interaction.screen.image[i].areaId;
                    screenID = interaction.screen.image[i].screenID;
                    id = interaction.screen.image[i].id;
                    var tmp = id.split('_');
					var num = tmp[3];
                    name = 'Image'+num;
                    data.image[i] = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"areaId":' + areaId + ',"name":"' + name + '","zindex":' + zindex + ',"screenID":' + screenID + ',"num":' + num + '}';
                }
            }

            //设置文本区域
            if (interaction.screen.text.length > 0) {
				data.text = [];
                for (var i = 0; i < interaction.screen.text.length; i++) {
                    var parent = $('#' + interaction.screen.text[i].id);
                    x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    y = parent.css('top');
                    y = y.substring(0, y.length-2);
                    w = parent.innerWidth();
                    h = parent.innerHeight();
                    zindex = parent.zIndex();
                    areaId = interaction.screen.text[i].areaId == undefined ? 0 : interaction.screen.text[i].areaId;
                    screenID = interaction.screen.text[i].screenID;
                    name = 'Text';
                    data.text[i] = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"areaId":' + areaId + ',"name":"' + name + '","zindex":' + zindex + ',"screenID":' + screenID + '}';
                }
            }
            
            //设置静态文本区域
            if (interaction.screen.staticText.length > 0) {
				data.staticText = [];
                for (var i = 0; i < interaction.screen.staticText.length; i++) {
                    var parent = $('#' + interaction.screen.staticText[i].id);
                    x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    y = parent.css('top');
                    y = y.substring(0, y.length-2);
                    w = parent.innerWidth();
                    h = parent.innerHeight();
                    zindex = parent.zIndex();
                    areaId = interaction.screen.staticText[i].areaId == undefined ? 0 : interaction.screen.staticText[i].areaId;
                    screenID = interaction.screen.staticText[i].screenID;
                    name = "Bulletin Board";
                    data.staticText[i] = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"areaId":' + areaId + ',"name":"' + name + '","zindex":' + zindex + ',"screenID":' + screenID + '}';
                }
            }
            
            //日期区域
            if (interaction.screen.date.length > 0) {
            	data.date = [];
            	for(var i = 0; i < interaction.screen.date.length > 0; i++) {
            		var parent = $('#' + interaction.screen.date[i].id);
            	 	x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    y = parent.css('top');
                    y = y.substring(0, y.length-2);
            		w = parent.innerWidth();
            		h = parent.innerHeight();
            		zindex = parent.zIndex();
            		areaId = interaction.screen.date[i].areaId == undefined ? 0 : interaction.screen.date[i].areaId;
            		screenID = interaction.screen.date[i].screenID;
            		name = "Date";
            		data.date[i] = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"areaId":' + areaId + ',"name":"' + name + '","zindex":' + zindex +',"screenID":' + screenID + '}';
            	}
            }
            
            //时间区域
            if (interaction.screen.time.length > 0) {
            	data.time = [];
            	for(var i=0; i < interaction.screen.time.length > 0 ; i++) {
            		var parent = $('#' + interaction.screen.time[i].id);
            	 	x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    y = parent.css('top');
                    y = y.substring(0, y.length-2);
            		w = parent.innerWidth();
            		h = parent.innerHeight();
            		zindex = parent.zIndex();
            		areaId = interaction.screen.time[i].areaId == undefined ? 0 : interaction.screen.time[i].areaId;
            		screenID = interaction.screen.time[i].screenID;
            		name = "Time";
            		data.time[i] = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"areaId":' + areaId + ',"name":"' + name + '","zindex":' + zindex + ',"screenID":' + screenID + '}';
            	}
            }

            //Weather
             if (interaction.screen.weather.length > 0) {
                data.weather = [];
                for(var i=0; i < interaction.screen.weather.length > 0; i++) {
                	var parent = $('#' + interaction.screen.weather[i].id);
	                x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    y = parent.css('top');
                    y = y.substring(0, y.length-2);
	                w = parent.innerWidth();
	                h = parent.innerHeight();
	                zindex = parent.zIndex();
	                areaId = interaction.screen.weather[i].areaId == undefined ? 0 : interaction.screen.weather[i].areaId;
	                screenID = interaction.screen.weather[i].screenID;
	                name = 'Weather';
	                data.weather[i] = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"areaId":' + areaId + ',"name":"' + name + '","zindex":' + zindex + ',"screenID":' + screenID + '}';
	        	}
            }
            
            //Webpage
            if (interaction.screen.webpage != null) {
            	data.webpage = [];
            	for(var i=0; i < interaction.screen.webpage.length > 0; i++) {
                	var parent = $('#' + interaction.screen.webpage[i].id);
	               	x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    y = parent.css('top');
                    y = y.substring(0, y.length-2);
	                w = parent.innerWidth();
	                h = parent.innerHeight();
	                zindex = parent.zIndex();
	                areaId = interaction.screen.webpage[i].areaId == undefined ? 0 : interaction.screen.webpage[i].areaId;
	                screenID = interaction.screen.webpage[i].screenID;
	                name = 'Webpage';
	                data.webpage[i] = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"areaId":' + areaId + ',"name":"' + name + '","zindex":' + zindex + ',"screenID":' + screenID + '}';
	        	}
            }
            
            //设置照片的相关属性
            if (interaction.screen.btn.length > 0) {
                data.btn = [];
                for (var i = 0; i < interaction.screen.btn.length; i++) {
                    var parent = $('#' + interaction.screen.btn[i].id);
                    x = parent.css('left');
                    x = x.substring(0, x.length-2);
                    y = parent.css('top');
                    y = y.substring(0, y.length-2);
                    w = parent.innerWidth();
                    h = parent.innerHeight();
                    zindex = parent.zIndex();
                    areaId = interaction.screen.btn[i].areaId == undefined ? 0 : interaction.screen.btn[i].areaId;
                    screenID = interaction.screen.btn[i].screenID;
					id = interaction.screen.btn[i].id;
                    var tmp = id.split('_');
					//var num = i + 1;
					var num = tmp[3];
                    name = 'Touch'+num;
                    data.btn[i] = '{"x":' + x + ',"y":' + y + ',"w":' + w + ',"h":' + h + ',"areaId":' + areaId + ',"name":"' + name + '","zindex":' + zindex + ',"screenID":' + screenID + ',"num":' + num +'}';
                }
            }
           
            //BG区域
            if(interaction.screen.bgimg.length > 0) {
            	data.bgimg = [];
            	/*
            	for(var i = 0; i < interaction.screen.bgimg.length; i++) {
            		for(var j = i+1; j < interaction.screen.bgimg.length; j++) {
            			if(interaction.screen.bgimg[i].screenID == interaction.screen.bgimg[j].screenID) {
            				interaction.screen.bgimg.splice(i, j);
            			}
            		}
            	}*/
            	for(var i = 0; i < interaction.screen.bgimg.length; i++) {
            		mediaId = interaction.screen.bgimg[i].mediaId;
                	areaId = interaction.screen.bgimg[i].areaId == undefined ? 0 : interaction.screen.bgimg[i].areaId;
                	screenID = interaction.screen.bgimg[i].screenID;
                	data.bgimg[i] = '{"x":0,"y":0,"w":' + interaction.screen.width + ',"h":' + interaction.screen.height + ',"areaId":' + areaId + ',"media_id":' + mediaId + ',"screenID":' + screenID + '}';
            	}
            }
            
            //screen
            data.screen = '{"w":' + interaction.screen.width + ',"h":' + interaction.screen.height + '}';
            data.id = interaction.screen.id;
            //组织已经删除的ID
            if (interaction.screen.deletes.length > 0) {
                deletes = '{';
                for (var i = 0; i < interaction.screen.deletes.length; i++) {
                    deletes += '"' + i + '":{"id":' + interaction.screen.deletes[i].areaId + ',"type":"' + interaction.screen.deletes[i].type + '"}';
                    if (i < interaction.screen.deletes.length - 1) {
                        deletes += ',';
                    }
                }
                deletes += '}';
                data.deletes = deletes;
            }

            data.treejson = treejson;
            data.treejsonpls = treejsonpls;
            data.touchName = $('#touchName').val();
            data.period = $('#period').val();
            data.action = $('#action').val();
            data.changeid = change_id;
            data.pchangeid = p_change_id;
            data.changeAreaId = change_area_id;
            data.lastCount = lastCount;
            return data;
        },
        save: function(){
			/*if(interaction.screen.movie == null){
				alert(interaction.screen.warnVideo);
				return;
			}*/
            /*if(!interaction.screen.isLogoCorrect()){
				alert(interaction.screen.warnLogo);
			}else *//*if (interaction.screen.isOverlapping()) {
                alert(interaction.screen.warnOverlap);
            }else {*/
                var saveButton = $('#save');
                saveButton.unbind('click');
                saveButton.removeClass('btn-01');
                saveButton.addClass('btn-02');
                restScrollPosition();

                $.post('/interaction/save_screen?t=' + new Date().getTime(), interaction.screen.createData(), function(data){
                	//alert(data);
                //});
                    if (data.code == 0) {
                        showMsg(data.msg, 'success');
                        //update local value
                        if (data.bgimg != undefined) {
                           for (var i = 0; i < data.bgimg.length; i++) {
                            	interaction.screen.bgimg[i].areaId = data.bgimg[i].areaId;
                            }
                        }
                        if (data.movie != undefined) {
                            for (var i = 0; i < data.movie.length; i++) {
                                interaction.screen.movie[i].areaId = data.movie[i].areaId;
                            }
                        }
                        if (data.image != undefined) {
                            for (var i = 0; i < data.image.length; i++) {
                                interaction.screen.image[i].areaId = data.image[i].areaId;
                            }
                        }
                        
                        if (data.text != undefined) {
                            for (var i = 0; i < data.text.length; i++) {
                                interaction.screen.text[i].areaId = data.text[i].areaId;
                            }
                        }
                        
                        if (data.staticText != undefined) {
                            for (var i = 0; i < data.staticText.length; i++) {
                                interaction.screen.staticText[i].areaId = data.staticText[i].areaId;
                            }
                        }
                        
                        if (data.date != undefined) {
                            for (var i = 0; i < data.date.length; i++) {
                                interaction.screen.date[i].areaId = data.date[i].areaId;
                            }
                        }
                        
                        if (data.time != undefined) {
                            for (var i = 0; i < data.time.length; i++) {
                                interaction.screen.time[i].areaId = data.time[i].areaId;
                            }
                        }
                        
                        if (data.weather != undefined) {
                            for (var i = 0; i < data.weather.length; i++) {
                                interaction.screen.weather[i].areaId = data.weather[i].areaId;
                            }
                        }   
                        
                        if (data.webpage != undefined) {
                            for (var i = 0; i < data.webpage.length; i++) {
                                interaction.screen.webpage[i].areaId = data.webpage[i].areaId;
                            }
                        }
                        
                        if(data.btn != undefined) {
                        	for(var i = 0; i < data.btn.length; i++) {
                        		interaction.screen.btn[i].areaId = data.btn[i].areaId;
                        	}
                        }
                        
                        if (data.logo != undefined) {
                            interaction.screen.logo.areaId = data.logo.area_id;
                        }
                    }else {
                        showMsg(data.msg, 'error');
                    }
					/*
                    saveButton.removeClass('btn-02');
                    saveButton.addClass('btn-01');
                    saveButton.bind('click', function(){
                        interaction.screen.save();
                    });
					*/
                    window.location.href='/interaction/index';
                }, 'json');
            //}
            
            return false;
        }
    }
}
