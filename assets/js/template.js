var template = {
  /*默认页面*/
  index: {
    /**
     * 1.丢弃小数部分,保留整数部分       parseInt(5/2)
     * 2.向上取整,有小数就整数部分加1   Math.ceil(5/2)
     * 3,四舍五入. Math.round(5/2)
     * 4,向下取整  Math.floor(5/2)
     */
    changeX: function () {
      t_width = template.screen.width;
      var id = $("#areaChange").val();
      var areaX = parseInt($("#areaX").val());
      var obj = $("#" + id);
      var x = obj.position().left;
      var w = obj.innerWidth();
      if (isNaN(areaX)) {
        $("#areaX").val(2 * x);
      } else {
        if (areaX % 2 != 0) {
          areaX--;
        }
        var left = areaX / template.screen.radio;
        if (left + w > t_width) {
          left = t_width - w;
        }
        $("#" + id).css("left", left);
        $("#areaX").val(template.screen.radio * left);
      }
    },
    changeY: function () {
      var t_height = template.screen.height;
      var id = $("#areaChange").val();
      var areaY = parseInt($("#areaY").val());
      var obj = $("#" + id);
      var y = obj.position().top;
      var h = obj.innerHeight();
      if (isNaN(areaY)) {
        $("#areaY").val(2 * y);
      } else {
        if (areaY % 2 != 0) {
          areaY--;
        }
        var top = areaY / template.screen.radio;
        if (top + h > t_height) {
          top = t_height - h;
        }
        $("#" + id).css("top", top);
        $("#areaY").val(template.screen.radio * top);
      }
    },
    changeW: function (type) {
      t_width = template.screen.width;
      var id = $("#areaChange").val();
      var areaWidth = $("#areaWidth").val();
      var areaWidthP = $("#areaWidthPercent").val();
      var obj = $("#" + id);
      var x = obj.position().left;
      var w = obj.innerWidth();
      if (type == 2) {
        areaWidth = template.screen.radio * t_width * areaWidthP * 0.01;
      }
      if (isNaN(areaWidth)) {
        $("#areaWidth").val(2 * w);
      } else {
        var wmod = areaWidth % 4;
        if (wmod != 0) {
          areaWidth -= wmod;
        }
        var width = areaWidth / template.screen.radio;
        if (width + x > t_width) {
          width = t_width - x;
        }

        $("#" + id).css("width", width);
        $("#areaWidth").val(template.screen.radio * width); //attr('value', 2 * width);
        $("#areaWidthPercent").val(
          Math.round((width / t_width) * 10000) / 100.0
        );
      }
    },
    changeH: function (type) {
      var t_height = template.screen.height;
      var id = $("#areaChange").val();
      var areaHeight = $("#areaHeight").val();
      var areaHeightP = $("#areaHeightPercent").val();
      var obj = $("#" + id);
      var y = obj.position().top;
      var h = obj.innerHeight();

      if (type == 2) {
        areaHeight = template.screen.radio * t_height * areaHeightP * 0.01;
      }
      if (isNaN(areaHeight)) {
        $("#areaHeight").val(2 * h);
      } else {
        var hmod = areaHeight % 2;
        if (hmod != 0) {
          areaHeight -= hmod;
        }
        var height = areaHeight / template.screen.radio;
        if (height + y > t_height) {
          height = t_height - y;
        }

        $("#" + id).css("height", height);
        $("#" + id + " dd").css("height", height - 20);
        $("#areaHeight").val(template.screen.radio * height);
        $("#areaHeightPercent").val(
          Math.round((height / t_height) * 10000) / 100.0
        );
      }
    },
    changeWP: function () {},
    changeHP: function () {},
  },
  //屏幕页面
  screen: {
    curObj: null,
    radio: 2,
    template_type: 1, //template类型，默认0表示np100, 1表示np200
    tabIndex: 1,

    zIndex: 10,
    width: 800,
    height: 600,
    realWidth: 1280, //实际宽度
    realHeight: 720, //实际高度
    minVideoRealWidth: 48, //视频最小宽度
    minVideoRealHeight: 48, //视频最小高度
    defaultTextHeight: 0,
    defaultTextRealHeight: 60,
    minTextRealHeight: 44,

    minImageRealWidth: 100, //图片最小宽度
    minImageRealHeight: 100, //图片最小高度

    minDateRealWidth: 256, //日期最小宽度
    minDateRealHeight: 128, //日期最小高度
    maxDateRealWidth: 512, //日期最大宽度
    maxDateRealHeight: 512, //日期最大高度

    minWeatherRealWidth: 256, //天气最小宽度
    minWeatherRealHeight: 128, //天气最小高度
    maxWeatherRealWidth: 512, //天气最大宽度
    maxWeatherRealHeight: 512, //天气最大高度
    logoSize: [],
    logoRealSize: [64, 128, 256],
    DateWeatherSize: [], //天气和日期的Size数组
    DateWeatherRealSize: [128, 256, 512], //天气和日期的Size数组
    gridX: 1,
    gridY: 1,

    deletes: new Array(), //已经删除的区域ID记录
    RIGHT: 1, //Resize方向向右
    DOWN: 2, //Resize方向向下
    LEFT: 3, // Resize方向向左
    UP: 4, //Resize方向向上
    RIGHT_DOWN: 5, //Resize方向右下方
    DRAGE_LEFT: 6, //左拖动
    DRAGE_UP: 7, //上拖动
    DRAGE_RIGHT: 8, //右拖动
    DRAGE_DOWN: 9, //下拖动
    DRAGE_LEFT_UP: 10, //左上方拖动
    DRAGE_RIGHT_UP: 11,
    DRAGE_RIGHT_DOWN: 12,
    DRAGE_LEFT_DOWN: 13,
    readonly: false, //是否只读
    debug: false && $.browser.mozilla,
    areas: new Array(),
    id_index: 1,
    id_indexes: new Array(),

    init: function () {
      this.curObj = null;
      this.areas = new Array();

      template.screen.deletes = new Array();
      template.screen.tabIndex = 0;
      //init logo size
      for (var i = 0; i < template.screen.logoRealSize.length; i++) {
        template.screen.logoSize[i] =
          (template.screen.width * template.screen.logoRealSize[i]) /
          template.screen.realWidth;
      }
      //init date、weather size
      for (var i = 0; i < template.screen.DateWeatherRealSize.length; i++) {
        template.screen.DateWeatherSize[i] =
          (template.screen.width * template.screen.DateWeatherRealSize[i]) /
          template.screen.realWidth;
      }

      if (!template.screen.template_type) {
        template.screen.gridX =
          template.screen.width > template.screen.height
            ? (4 * template.screen.width) / template.screen.realWidth
            : (2 * template.screen.height) / template.screen.realHeight;
        template.screen.gridY =
          template.screen.width > template.screen.height
            ? (2 * template.screen.height) / template.screen.realHeight
            : (4 * template.screen.width) / template.screen.realWidth;
      } else {
        template.screen.gridX = 1;
        template.screen.gridY = 1;
      }
      //template.screen.gridX = template.screen.width > template.screen.height ? (4 * template.screen.width) / template.screen.realWidth : (2 * template.screen.height) / template.screen.realHeight;
      //template.screen.gridY = template.screen.width > template.screen.height ? (2 * template.screen.height) / template.screen.realHeight : (4 * template.screen.width) / template.screen.realWidth;

      //初始化屏幕的高度和宽度
      //$('.screen').css('width',template.screen.width).css('height', template.screen.height);
      if (template.screen.readonly) {
        return;
      }

      $(".btn-icon").each(function () {
        $(this).removeClass("disabled");
      });

      $("#save")
        .off("click")
        .on("click", function (event) {
          template.screen.save();
        });
    },

    initArea: function (area, settings) {
      var id = "area_" + area.area_name;

      if (area.area_type == "30") {
        var tmp = area.area_name.split("-");
        this.id_indexes.push(Number(tmp[1]));
      }
      template.screen.showArea(id, area);
      var newArea = {
        id: id,
        areaId: area.id,
        name: area.name,
        type: area.area_type,
      };

      if (area.mediaId) {
        newArea.mediaId = area.mediaId;
      }

      if (area.settings) {
        newArea.settings = area.settings;
      }
      template.screen.areas.push(newArea);
    },

    isIntersect: function (curObj, targetObj, direct) {
      if (
        curObj == undefined ||
        targetObj == undefined /*|| direct == undefined || direct <= 0*/
      ) {
        return false;
      }

      var id = curObj.attr("id");
      var tid = targetObj.attr("id");

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
        console.info(
          "isIntersect curObj:" +
            curObj.text() +
            ", targetObj:" +
            targetObj.text() +
            ", direct:" +
            direct +
            ", point(left, top): " +
            (x1 + "," + y1) +
            ", point(right, bottom):" +
            (x2 + "," + y2) +
            ", target point(left, top):" +
            (xx1 + "," + yy1) +
            ", target point(right, bottom):" +
            (xx2 + "," + yy2)
        );
      }

      var result = false;
      switch (direct) {
        case template.screen.RIGHT:
          //上方修正
          result =
            xx1 >= x1 &&
            x2 > xx1 &&
            ((y1 >= yy1 && y1 < yy2) ||
              (y2 > yy1 && y2 <= yy2) ||
              (y1 < yy1 && y2 > yy2) ||
              (y1 == yy1 && y2 == yy2));
          break;
        case template.screen.LEFT:
          result =
            x2 > xx1 &&
            x1 < xx2 &&
            ((y1 >= yy1 && y1 < yy2) ||
              (y2 > yy1 && y2 <= yy2) ||
              (y1 < yy1 && y2 > yy2) ||
              (y1 == yy1 && y2 == yy2));
          break;
        case template.screen.UP:
          result =
            y2 > yy1 &&
            yy2 > y1 &&
            ((x1 >= xx1 && x1 < xx2) ||
              (x2 > xx1 && x2 <= xx2) ||
              (x1 < xx1 && x2 > xx2) ||
              (x1 == xx1 && x2 == xx2));
          break;
        case template.screen.DOWN:
          result =
            yy1 >= y1 &&
            y2 > yy1 &&
            ((x1 >= xx1 && x1 < xx2) ||
              (x2 > xx1 && x2 <= xx2) ||
              (x1 < xx1 && x2 > xx2) ||
              (x1 == xx1 && x2 == xx2));
          break;
        case template.screen.RIGHT_DOWN:
          result =
            (xx1 >= x1 &&
              x2 > xx1 &&
              ((y1 >= yy1 && y1 < yy2) ||
                (y2 > yy1 && y2 < yy2) ||
                (y1 < yy1 && y2 > yy2) ||
                (y1 == yy1 && y2 == yy2))) ||
            (yy1 >= y1 &&
              y2 > yy1 &&
              ((x1 >= xx1 && x1 < xx2) ||
                (x2 > xx1 && x2 <= xx2) ||
                (x1 < xx1 && x2 > xx2) ||
                (x1 == xx1 && x2 == xx2)));
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
            (x1 >= xx1 && x1 < xx2 && y1 >= yy1 && y1 < yy2) /*Left,Top*/ ||
            (x2 > xx1 && x2 <= xx2 && y1 >= yy1 && y1 < yy2) /*Right, Top*/ ||
            (x1 >= xx1 && x1 < xx2 && y2 > yy1 && y2 <= yy2) /*Left, Bottom*/ ||
            (x2 > xx1 &&
              y2 > yy1 &&
              x2 <= xx2 &&
              y2 <= yy2) /*Right, Bottom*/ ||
            (x1 < xx1 && y1 < yy1 && x2 > xx2 && y2 > yy2) /*外回*/ ||
            (x1 >= xx1 && y1 >= yy1 && x2 <= xx2 && y2 <= yy2) /*内回和等*/ ||
            (y1 <= yy1 &&
              y2 >= yy2 /*Top Must be outer*/ &&
              ((x1 < xx1 && x2 > xx1 && x2 <= xx2) /*Left*/ ||
                (x1 >= xx1 && x2 <= xx2) /*Middle*/ ||
                (x2 > xx2 && x1 >= xx1 && x1 < xx2))) /*Right*/ /*中*/ ||
            (x1 <= xx1 &&
              x2 >= xx2 /*Left or Right be outer*/ &&
              ((y1 <= yy1 && y2 > yy1 && y2 <= yy2) /*Top*/ ||
                (y1 > yy1 && y2 < yy2) /*Middle*/ ||
                (y2 > yy2 && y1 >= yy1 && y1 < yy2))) /*Bottom*/ /*竖中*/;
          break;
      }
      if (template.screen.debug) {
        console.info(
          "isIntersect curObj:" +
            curObj.text() +
            ", targetObj:" +
            targetObj.text() +
            ", direct:" +
            direct +
            ", result: " +
            result
        );
      }
      return result;
    },
    getCrossPoint: function (targetObjs, direct) {
      //获取某个方向上最合适的交点
      if (
        targetObjs == undefined ||
        targetObjs.length < 2 ||
        direct == undefined ||
        direct <= 0
      ) {
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
          xArray = xArray.sort(function (a, b) {
            return a > b ? 1 : -1;
          });
          yArray = yArray.sort(function (a, b) {
            return a > b ? 1 : -1;
          });
          if (template.screen.debug) {
            console.info(xArray);
            console.info(yArray);
            console.info("getCrossPoint,x:" + xArray[1] + ", y:" + yArray[1]);
          }

          result = {
            x: xArray[1],
            y: yArray[1],
          };
          break;
      }
      if (result == null) {
        return false;
      }
      return result;
    },
    getIntersectObj: function (curObj, direct) {
      //获取在某个方向上与当前对象存在相交的对象列表
      if (template.screen.template_type) {
        if (curObj == undefined || direct == undefined || direct <= 0) {
          return null;
        }

        var id = curObj.attr("id");
        var result = new Array();
        var parentObj = curObj.parent();
        if (parentObj == null) {
          return null;
        }
        if (!parentObj.children().length) {
          return null;
        }

        var areas = parentObj.children("dl");
        areas.each(function () {
          var kid = $(this);
          var cid = kid.attr("id");

          if (id == kid) {
            return true;
          }

          if (
            (id == "area_movie" ||
              id.indexOf("area_pic") != -1 ||
              id == "area_website") &&
            (cid == "area_movie" ||
              cid.indexOf("area_pic") != -1 ||
              cid == "area_website")
          ) {
            switch (direct) {
              case template.screen.RIGHT_DOWN:
                if (
                  template.screen.isIntersect(
                    curObj,
                    kid,
                    template.screen.RIGHT
                  )
                ) {
                  result.push({
                    direct: template.screen.RIGHT,
                    obj: kid,
                  });
                } else if (
                  template.screen.isIntersect(curObj, kid, template.screen.DOWN)
                ) {
                  result.push({
                    direct: template.screen.DOWN,
                    obj: kid,
                  });
                }
                break;
              default:
                if (template.screen.isIntersect(curObj, kid, direct)) {
                  result.push({
                    direct: direct,
                    obj: kid,
                  });
                }
                break;
            }
          } else {
            if (
              (id == "area_text" || id == "area_date" || id == "area_time") &&
              (cid == "area_text" || cid == "area_date" || cid == "area_time")
            ) {
              switch (direct) {
                case template.screen.RIGHT_DOWN:
                  if (
                    template.screen.isIntersect(
                      curObj,
                      kid,
                      template.screen.RIGHT
                    )
                  ) {
                    result.push({
                      direct: template.screen.RIGHT,
                      obj: kid,
                    });
                  } else if (
                    template.screen.isIntersect(
                      curObj,
                      kid,
                      template.screen.DOWN
                    )
                  ) {
                    result.push({
                      direct: template.screen.DOWN,
                      obj: kid,
                    });
                  }
                  break;
                default:
                  if (template.screen.isIntersect(curObj, kid, direct)) {
                    result.push({
                      direct: direct,
                      obj: kid,
                    });
                  }
                  break;
              }
            } else {
              return null;
            }
          }
        });
      }
      if (template.screen.debug) {
        console.info(
          "getIntersectObj " +
            curObj.text() +
            ", direct:" +
            direct +
            ", intersect.length:" +
            result.length
        );
      }
      return result;
    }, //停靠在目标对象，目前对象是在当前对象的那个方向
    dockArea: function (curObj, targetObjs, direct) {
      if (
        curObj == undefined ||
        targetObjs == undefined ||
        targetObjs.length == 0 ||
        direct == undefined
      ) {
        return false;
      }

      var id = curObj.attr("id");
      if (id == undefined) {
        return false;
      }

      var minWidth = 1;
      var minHeight = 1;

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
            curObj.css("width", width);
            result = true;
          }
          break;
        case template.screen.LEFT:
          /*bug: only dock right side*/
          var maxX =
            targetObjs[0].obj.position().left +
            targetObjs[0].obj.outerWidth(true);
          for (var i = 1; i < targetObjs.length; i++) {
            if (
              targetObjs[i].obj.position().left +
                targetObjs[i].obj.outerWidth(true) >
              maxX
            ) {
              maxX =
                targetObjs[i].obj.position().left +
                targetObjs[i].obj.outerWidth(true);
            }
          }
          curObj.css("left", maxX);
          curObj.css("width", curW - (maxX - curX));
          result = true;
          break;
        case template.screen.UP:
          /*bug: only dock down side*/
          var maxY =
            targetObjs[0].obj.position().top +
            targetObjs[0].obj.outerHeight(true);
          for (var i = 1; i < targetObjs.length; i++) {
            if (
              targetObjs[i].obj.position().top +
                targetObjs[i].obj.outerHeight(true) >
              maxY
            ) {
              maxY =
                targetObjs[i].obj.position().top +
                targetObjs[i].obj.outerHeight(true);
            }
          }
          curObj.css("top", maxY);
          curObj.css("height", curH - (maxY - curY));
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
            curObj.css("height", height);
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
                console.info(
                  "dockArea[" +
                    curObj.text() +
                    "] RIGHT_DOWN, intersect RIGHT [" +
                    targetObjs[i].obj.text() +
                    "], minX:" +
                    minX
                );
              }
            } else if (targetObjs[i].direct == template.screen.DOWN) {
              if (targetObjs[i].obj.position().top < minY) {
                minY = targetObjs[i].obj.position().top;
              }
              if (template.screen.debug) {
                console.info(
                  "dockArea[" +
                    curObj.text() +
                    "] RIGHT_DOWN, intersect DOWN [" +
                    targetObjs[i].obj.text() +
                    "], minY:" +
                    minY
                );
              }
            }
          }
          width = minX - curObj.position().left;
          height = minY - curObj.position().top;

          if (template.screen.debug) {
            console.info(
              "dockArea RIGHT_DOWN, width:" +
                width +
                ", height:" +
                height +
                ", minX:" +
                minX +
                ", minY:" +
                minY
            );
          }
          if (width >= minWidth) {
            curObj.css("width", width);
            result = true;
          }
          if (height >= minHeight) {
            curObj.css("height", height);
            result = true;
          }

          break;
        case template.screen.DRAGE_LEFT:
          var maxX =
            targetObjs[0].obj.position().left + targetObjs[0].obj.outerWidth();
          for (var i = 1; i < targetObjs.length; i++) {
            if (
              targetObjs[i].obj.position().left +
                targetObjs[i].obj.outerWidth() >
              maxX
            ) {
              maxX =
                targetObjs[i].obj.position().left +
                targetObjs[i].obj.outerWidth();
            }
          }

          curObj.css("left", maxX);
          result = true;
          break;
        case template.screen.DRAGE_UP:
          var maxY =
            targetObjs[0].obj.position().top + targetObjs[0].obj.outerHeight();
          for (var i = 1; i < targetObjs.length; i++) {
            if (
              targetObjs[i].obj.position().top +
                targetObjs[i].obj.outerHeight() >
              maxY
            ) {
              maxY =
                targetObjs[i].obj.position().top +
                targetObjs[i].obj.outerHeight();
            }
          }
          curObj.css("top", maxY);
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
            curObj.css("left", minX);
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
            curObj.css("top", minY);
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
            targetX =
              targetObjs[i].obj.position().left +
              targetObjs[i].obj.outerWidth();
            if (
              left >= targetObjs[i].obj.position().left &&
              left < targetX &&
              (targetX - left) * enlargeX < width &&
              targetX + width <= template.screen.width
            ) {
              curObj.css("left", targetX);
              result = true;
            }

            targetY =
              targetObjs[i].obj.position().top +
              targetObjs[i].obj.outerHeight();
            if (
              top >= targetObjs[i].obj.position().top &&
              top < targetY &&
              (targetY - top) * enlargeY < height &&
              targetY + height <= template.screen.height
            ) {
              curObj.css("top", targetY);
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
            targetX =
              targetObjs[i].obj.position().left +
              targetObjs[i].obj.outerWidth();
            var diffLeft = targetObjs[i].obj.position().left - width;
            if (
              left > targetObjs[i].obj.position().left &&
              left <= targetX &&
              (left - targetObjs[i].obj.position().left) * enlargeX < width &&
              diffLeft >= -0.01
            ) {
              //解决精度问题
              if (diffLeft < 0) {
                diffLeft = 0;
              }
              curObj.css("left", diffLeft);
              result = true;
            }
            targetY =
              targetObjs[i].obj.position().top +
              targetObjs[i].obj.outerHeight();
            if (
              top >= targetObjs[i].obj.position().top &&
              top < targetY &&
              (targetY - top) * enlargeY < height &&
              targetY + height <= template.screen.height
            ) {
              curObj.css("top", targetY);
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
            targetX =
              targetObjs[i].obj.position().left +
              targetObjs[i].obj.outerWidth();
            var diffLeft = targetObjs[i].obj.position().left - width;
            if (
              left > targetObjs[i].obj.position().left &&
              left <= targetX &&
              (left - targetObjs[i].obj.position().left) * enlargeX < width &&
              diffLeft >= -0.001
            ) {
              if (diffLeft < 0) {
                diffLeft = 0;
              }
              curObj.css("left", diffLeft);
              result = true;
            }
            targetY =
              targetObjs[i].obj.position().top +
              targetObjs[i].obj.outerHeight();
            var diffTop = targetObjs[i].obj.position().top - height;
            if (
              top > targetObjs[i].obj.position().top &&
              top <= targetY &&
              (top - targetY) * enlargeY < height &&
              diffTop >= -0.001
            ) {
              if (diffTop < 0) {
                diffTop = 0;
              }
              curObj.css("top", diffTop);
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
            targetX =
              targetObjs[i].obj.position().left +
              targetObjs[i].obj.outerWidth();
            if (
              left >= targetObjs[i].obj.position().left &&
              left < targetX &&
              (targetX - left) * enlargeX < width &&
              targetX + width <= template.screen.width
            ) {
              curObj.css("left", targetX);
              result = true;
            }

            targetY =
              targetObjs[i].obj.position().top +
              targetObjs[i].obj.outerHeight();
            var diffTop = targetObjs[i].obj.position().top - height;
            if (
              top > targetObjs[i].obj.position().top &&
              top <= targetY &&
              (top - targetObjs[i].obj.position().top) * enlargeY < height &&
              diffTop >= -0.001
            ) {
              if (diffTop < 0) {
                diffTop = 0;
              }
              curObj.css("top", diffTop);
              result = true;
            }
          }
          break;
      }

      var x = curObj.position().left;
      var y = curObj.position().top;
      var w = curObj.outerWidth();
      var h = curObj.outerHeight();
      if (x + w > template.screen.width || y + h > template.screen.height) {
        result = false;
        curObj.css("left", curX);
        curObj.css("top", curY);
        curObj.css("width", curW);
        curObj.css("height", curH);
      }
      if (template.screen.debug) {
        console.info(
          "dockArea:" +
            curObj.text() +
            ", result:" +
            result +
            ", direct:" +
            direct
        );
      }
      return result;
    },
    adjustArea: function (curObj) {
      if (curObj == undefined) {
        return;
      }

      //微调区域，控制边界，并保证高度和宽度是2的倍数
      var left = curObj.position().left;
      var top = curObj.position().top;
      if (left < 0) {
        curObj.css("left", 0);
      }

      if (top < 0) {
        curObj.css("top", 0);
      }

      var nWidth = curObj.innerWidth();
      var nHeight = curObj.innerHeight();
      if (left + nWidth > template.screen.width) {
        nWidth = template.screen.width - left;
        curObj.css("width", nWidth);
      }
      if (top + nHeight > template.screen.height) {
        nHeight = template.screen.height - top;
        curObj.css("height", nHeight);
      }

      var width = Math.round(
        (template.screen.realWidth * nWidth) / template.screen.width
      );
      var height = Math.round(
        (template.screen.realHeight * nHeight) / template.screen.height
      );

      if (template.screen.debug) {
        console.info("adjustArea width:" + width + ", height:" + height);
      }

      //  test
      if (!template.screen.template_type) {
        var wmod = width % 4;
        if (wmod != 0) {
          //adjust width
          width -= wmod;

          nWidth = (width * template.screen.width) / template.screen.realWidth;
          curObj.css("width", nWidth);
          if (template.screen.debug) {
            console.info(
              "adjustArea realWidth:" + width + ", show width:" + nWidth
            );
          }
        }

        if (height % 2 != 0) {
          //addjust height
          height--;
          nHeight =
            (height * template.screen.height) / template.screen.realHeight;
          curObj.css("height", nHeight);
          if (template.screen.debug) {
            console.info(
              "adjustArea realHeight:" + height + ", show height:" + nHeight
            );
          }
        }
      }
    },

    changePosition: function (id, x, y, w, h, position) {
      var obj = $("#" + id);
      if (position != undefined && position != "") {
        obj.css("position", position);
      }

      x = Number(x);
      y = Number(y);
      w = Number(w);
      h = Number(h);

      if (x >= 0) {
        obj.css("left", x);
      }
      if (y >= 0) {
        obj.css("top", y);
      }
      if (w > 0) {
        obj.css("width", w);
      }
      if (h > 0) {
        obj.css("height", h);
        template.screen.updateAreaBody(obj);
      }
      template.screen.showAreaInfo($("#" + id));
    },

    addArea: function (area) {
      var id = "area_" + area.areaName;

      if (area.area_type == "30") {
        var idIndex = this.getAndSetIdIndex();

        id += "-" + idIndex;
        area.name += idIndex;
      }

      var newArea = {
        id: id,
        name: area.name,
        type: area.area_type,
      };

      if (
        id == "area_text" ||
        id == "area_date" ||
        id == "area_time" ||
        id == "area_weather"
      ) {
        var newSetting = {
          font_size: 40,
          bg_color: "#000000",
          color: "#FFFFFF",
          transparent: 0,
          style: 1,
        };

        if (id == "area_text") {
          newSetting.style = 2;
          newSetting.direction = 1;
        }

        newArea.settings = newSetting;
      }
      if (id.startsWith("area_id")) {
        var newSetting = {
          font_size: 80,
          bg_color: "#000000",
          color: "#FFFFFF",
          transparent: 100,
        };

        newArea.settings = newSetting;
      }
      template.screen.areas.push(newArea);
      template.screen.showArea(id, area);
    },

    showArea: function (id, area) {
      w = area.w ? Number(area.w) : 64;
      h = area.h ? Number(area.h) : 64;
      x = area.x ? Number(area.x) : 0;
      y = area.y ? Number(area.y) : 0;

      var title = area.name;

      var maxW = template.screen.width;
      var maxH = template.screen.height;
      var minW = 48;
      var minH = 48;
      if (id == "area_date") {
        minW = this.minDateRealWidth / this.radio;
        minH = this.minDateRealHeight / this.radio;
      }

      this.createArea(
        id,
        title,
        area.area_type,
        minW,
        maxW,
        minH,
        maxH,
        null,
        area.zindex
      );
      template.screen.changePosition(id, x, y, w, h, "absolute");
    },

    isResizeLimited: function (obj) {
      var id = obj.attr("id");
      var orignLeft = obj.position().left;
      var orignTop = obj.position().top;
      var orignWidth = obj.innerWidth();
      var orignHeight = obj.innerHeight();
      if (
        orignTop >= template.screen.height ||
        orignLeft >= template.screen.width
      ) {
        return true;
      }

      if (
        orignWidth > template.screen.width ||
        orignHeight > template.screen.height
      ) {
        return true;
      }

      if (
        orignLeft + orignWidth > template.screen.width ||
        orignTop + orignHeight > template.screen.height
      ) {
        return true;
      }

      return false;
    },
    adjustResize: function (obj, dir) {
      var orignLeft = obj.position().left;
      var orignTop = obj.position().top;
      var orignWidth = obj.innerWidth();
      var orignHeight = obj.innerHeight();

      var changed = true;
      if (template.screen.template_type) {
        switch (dir) {
          case template.screen.LEFT:
            if (orignLeft > 0) {
              if (obj.attr("id") == "area_logo") {
                obj.css("left", orignLeft);
                obj.css("width", orignWidth);
              } else {
                obj.css("left", orignLeft - template.screen.gridX);
                obj.css("width", orignWidth + template.screen.gridX);
              }
            } else {
              changed = false;
            }
            break;
          case template.screen.UP:
            if (orignTop > 0) {
              if (obj.attr("id") == "area_logo") {
                obj.css("top", orignTop);
                obj.css("height", orignHeight);
              } else {
                obj.css("top", orignTop - template.screen.gridY);
                obj.css("height", orignHeight + template.screen.gridY);
              }
            } else {
              changed = false;
            }
            break;
          case template.screen.RIGHT:
            if (orignLeft + orignWidth < template.screen.width) {
              if (obj.attr("id") == "area_logo") {
                obj.css("width", orignWidth);
              } else {
                obj.css("width", orignWidth + template.screen.gridX);
              }
            } else {
              changed = false;
            }
            break;
          case template.screen.DOWN:
            if (orignTop + orignHeight < template.screen.height) {
              if (obj.attr("id") == "area_logo") {
                obj.css("height", orignHeight);
              } else {
                obj.css("height", orignHeight + template.screen.gridY);
              }
            } else {
              changed = false;
            }
            break;
        }
      } else {
        switch (dir) {
          case template.screen.LEFT:
            if (orignLeft > 0) {
              //obj.css('left',orignLeft-template.screen.gridX);
              //obj.css('width',orignWidth+template.screen.gridX);
              if (
                obj.attr("id") == "area_weather" ||
                obj.attr("id") == "area_date"
              ) {
                if (orignWidth >= 256) {
                  changed = false;
                } else {
                  if (orignLeft <= orignWidth) {
                    obj.css("left", 0);
                    obj.css("width", 2 * orignWidth);
                  } else {
                    obj.css("left", orignLeft - orignWidth);
                    obj.css("width", 2 * orignWidth);
                  }
                }
              } else {
                if (obj.attr("id") == "area_logo") {
                  obj.css("left", orignLeft);
                  obj.css("width", orignWidth);
                } else {
                  obj.css("left", orignLeft - template.screen.gridX);
                  obj.css("width", orignWidth + template.screen.gridX);
                }
              }
            } else {
              changed = false;
            }
            break;
          case template.screen.UP:
            if (orignTop > 0) {
              //obj.css('top',orignTop-template.screen.gridY);
              //obj.css('height',orignHeight+template.screen.gridY);
              if (
                obj.attr("id") == "area_weather" ||
                obj.attr("id") == "area_date"
              ) {
                if (orignHeight >= 256) {
                  changed = false;
                } else {
                  if (orignTop == 0) {
                    changed = false;
                  } else {
                    if (orignTop > orignHeight) {
                      obj.css("top", orignTop - orignHeight);
                      obj.css("height", 2 * orignHeight);
                    } else {
                      obj.css("top", 0);
                      obj.css("height", 2 * orignHeight);
                    }
                  }
                }
              } else {
                if (obj.attr("id") == "area_logo") {
                  obj.css("top", orignTop);
                  obj.css("height", orignHeight);
                } else {
                  obj.css("top", orignTop - template.screen.gridY);
                  obj.css("height", orignHeight + template.screen.gridY);
                }
              }
            } else {
              changed = false;
            }
            break;
          case template.screen.RIGHT:
            if (orignLeft + orignWidth < template.screen.width) {
              //obj.css('width',orignWidth+template.screen.gridX);
              if (
                obj.attr("id") == "area_weather" ||
                obj.attr("id") == "area_date"
              ) {
                if (orignWidth >= 256) {
                  changed = false;
                } else {
                  if (orignLeft + 2 * orignWidth > template.screen.width) {
                    if (orignLeft + orignWidth == template.screen.width) {
                      changed = false;
                    } else {
                      obj.css("left", template.screen.width - 2 * orignWidth);
                      obj.css("width", 2 * orignWidth);
                    }
                  } else {
                    obj.css("width", 2 * orignWidth);
                  }
                }
              } else {
                if (obj.attr("id") == "area_logo") {
                  obj.css("width", orignWidth);
                } else {
                  obj.css("width", orignWidth + template.screen.gridX);
                }
              }
            } else {
              changed = false;
            }
            break;
          case template.screen.DOWN:
            if (orignTop + orignHeight < template.screen.height) {
              //obj.css('height',orignHeight+template.screen.gridY);
              if (
                obj.attr("id") == "area_weather" ||
                obj.attr("id") == "area_date"
              ) {
                if (orignHeight >= 256) {
                  changed = false;
                } else {
                  if (template.screen.height > 2 * orignHeight + orignTop) {
                    obj.css("height", 2 * orignHeight);
                    console.info("-----orignHeight:　" + orignHeight);
                  } else {
                    if (template.screen.height - orignTop - orignHeight == 0) {
                      changed = false;
                    } else {
                      if (
                        template.screen.height - orignTop - orignHeight <=
                        orignHeight
                      ) {
                        console.info("----2");
                        obj.css(
                          "top",
                          template.screen.height - 2 * orignHeight
                        );
                        obj.css("height", 2 * orignHeight);
                      } else {
                        changed = false;
                      }
                    }
                  }
                }
              } else {
                if (obj.attr("id") == "area_logo") {
                  obj.css("height", orignHeight);
                } else {
                  obj.css("height", orignHeight + template.screen.gridY);
                }
              }
            } else {
              changed = false;
            }
            break;
        }
      }

      if (changed) {
        if (obj.attr("id") == "area_logo") {
          for (var i = template.screen.logoSize.length - 1; i >= 0; i--) {
            var size = template.screen.logoSize[i];
            if (orignWidth >= size || orignHeight >= size) {
              if (dir == template.screen.LEFT || dir == template.screen.RIGHT) {
                obj.css("width", size);
              } else {
                obj.css("height", size);
              }
              break;
            }
          }
          template.screen.updateAreaBody(obj);
          template.screen.showAreaInfo(obj);
          return;
        }

        if (template.screen.isResizeLimited(obj)) {
          obj.css("top", orignTop);
          obj.css("left", orignLeft);
          obj.css("width", orignWidth);
          obj.css("height", orignHeight);
        } else {
          var interObjs = template.screen.getIntersectObj(obj, dir);
          if (interObjs != null && interObjs.length > 0) {
            var result = template.screen.dockArea(obj, interObjs, dir);
            if (!result) {
              obj.css("top", orignTop);
              obj.css("left", orignLeft);
              obj.css("width", orignWidth);
              obj.css("height", orignHeight);
            }
          }
        }
        //obj.children('dd').css('height', (obj.innerHeight() - obj.children('dt').outerHeight(true)));
        template.screen.updateAreaBody(obj);
        //update area info
        template.screen.showAreaInfo(obj);
      }
    },
    adjustMove: function (obj, dir) {
      var orignLeft = obj.position().left;
      var orignTop = obj.position().top;
      var orignWidth = obj.innerWidth();
      var orignHeight = obj.innerHeight();
      var changed = true;
      switch (dir) {
        case template.screen.LEFT:
          if (orignLeft >= template.screen.gridX) {
            obj.css("left", orignLeft - template.screen.gridX);
          } else if (orignLeft > 0) {
            obj.css("left", 0);
          } else {
            changed = false;
          }
          break;
        case template.screen.UP:
          if (orignTop >= template.screen.gridY) {
            obj.css("top", orignTop - template.screen.gridY);
          } else if (orignTop > 0) {
            obj.css("top", 0);
          } else {
            changed = false;
          }
          break;
        case template.screen.RIGHT:
          if (
            orignLeft + orignWidth + template.screen.gridX <=
            template.screen.width
          ) {
            obj.css("left", orignLeft + template.screen.gridX);
          } else if (orignLeft + orignWidth < template.screen.width) {
            obj.css("left", template.screen.width - orignWidth);
          } else {
            changed = false;
          }
          break;
        case template.screen.DOWN:
          if (
            orignTop + orignHeight + template.screen.gridY <=
            template.screen.height
          ) {
            obj.css("top", orignTop + template.screen.gridY);
          } else if (orignTop + orignHeight < template.screen.height) {
            obj.css("top", template.screen.height - orignHeight);
          } else {
            changed = false;
          }
          break;
      }

      if (changed) {
        if (template.screen.template_type) {
          var interObjs = template.screen.getIntersectObj(obj, dir);
          if (interObjs != null && interObjs.length > 0) {
            var result = template.screen.dockArea(obj, interObjs, dir);
            if (!result) {
              obj.css("top", orignTop);
              obj.css("left", orignLeft);
            }
          }
        } else {
          var interObjs = template.screen.getIntersectObj(obj, dir);
          if (interObjs != null && interObjs.length > 0) {
            var result = template.screen.dockArea(obj, interObjs, dir);
            if (template.screen.OverlappingOne()) {
              switch (dir) {
                case template.screen.LEFT:
                  obj.css("left", orignLeft - template.screen.gridX);
                  obj.css("width", orignWidth);
                  obj.css("height", orignHeight);
                  break;
                case template.screen.UP:
                  obj.css("top", orignTop - template.screen.gridY);
                  obj.css("width", orignWidth);
                  obj.css("height", orignHeight);
                  break;
                case template.screen.RIGHT:
                  obj.css("left", orignLeft + template.screen.gridX);
                  obj.css("width", orignWidth);
                  obj.css("height", orignHeight);
                  break;
                case template.screen.DOWN:
                  obj.css("top", orignTop + template.screen.gridY);
                  obj.css("width", orignWidth);
                  obj.css("height", orignHeight);
                  break;
              }
            }
          }
        }

        var width = obj.innerWidth();
        if (width != orignWidth) {
          obj.css("left", orignLeft);
          obj.css("width", orignWidth);
        }
        var height = obj.innerHeight();
        if (height != orignHeight) {
          obj.css("top", orignTop);
          obj.css("height", orignHeight);
        }
        //obj.children('dd').css('height', (obj.innerHeight() - obj.children('dt').outerHeight(true)));
        //update area info
        template.screen.showAreaInfo(obj);
      }
    },
    updateAreaBody: function (cur) {
      /*-1 内部高度*/
      var height = cur.innerHeight() - cur.children("dt").outerHeight(true);
      if (height < 0) {
        height = 0;
      }
      cur.children("dd").css("height", height);
    },
    createArea: function (
      id,
      title,
      type,
      minWidth,
      maxWidth,
      minHeight,
      maxHeight,
      callbackCreate,
      zIndex
    ) {
      zIndex = zIndex || template.screen.zIndex;

      $("#screen").append(template.screen._template(id, title, type));
      var area = $("#" + id);
      area.attr("tabindex", template.screen.tabIndex);
      template.screen.tabIndex++;

      area.on("click", function (event) {
        var cur = $(this);
        if (template.screen.readonly) {
          template.screen.showAreaInfo(cur);
          return;
        }
        if (template.screen.curObj != null) {
          if (template.screen.curObj.attr("id") == cur.attr("id")) {
            cur.focus();
            return;
          }
          //template.screen.curObj.unbind("keydown");
          template.screen.curObj.children("dt").removeClass("selected");
        }

        cur.focus();
        template.screen.curObj = cur;
        template.screen.curObj.children("dt").addClass("selected");
        template.screen.curObj.keydown(function (event) {
          var dealed = false;
          var keyCode = event.keyCode;
          if (keyCode == 27) {
            if (template.screen.curObj != null) {
              //	template.screen.curObj.unbind("keydown");
              template.screen.curObj.children("dt").removeClass("selected");
              template.screen.curObj = null;
            }
          }
          if (event.ctrlKey) {
            switch (keyCode) {
              case 74: //j
              case 37: //left
                template.screen.adjustResize($(this), template.screen.LEFT);
                dealed = true;
                break;
              case 75: //k
              case 38: //up
                template.screen.adjustResize($(this), template.screen.UP);
                dealed = true;
                break;
              case 76: //l
              case 39: //right
                template.screen.adjustResize($(this), template.screen.RIGHT);
                dealed = true;
                break;
              case 77: //m
              case 40: //down
                template.screen.adjustResize($(this), template.screen.DOWN);
                dealed = true;
                break;
            }
          } else {
            switch (keyCode) {
              case 74: //j
              case 37: //left
                template.screen.adjustMove($(this), template.screen.LEFT);
                dealed = true;
                break;
              case 75: //k
              case 38: //up
                template.screen.adjustMove($(this), template.screen.UP);
                dealed = true;
                break;
              case 76: //l
              case 39: //right
                template.screen.adjustMove($(this), template.screen.RIGHT);
                dealed = true;
                break;
              case 77: //m
              case 40: //down
                template.screen.adjustMove($(this), template.screen.DOWN);
                dealed = true;
                break;
            }
          }
          if (dealed) {
            event.preventDefault();
          }
        });
        template.screen.showAreaInfo(cur);
        if (
          cur.hasClass("movie") ||
          cur.hasClass("image") ||
          cur.hasClass("webpage") ||
          cur.hasClass("interaction")
        ) {
          return;
        }
      });
      area.css("z-index", zIndex);
      this.enableToolbar(id, false);
      area.click();
      if (template.screen.readonly) {
        return;
      }

      $("#" + id + " .close").on("click", function (event) {
        event.preventDefault();

        template.screen.enableToolbar(id, true);
        $("#" + id).remove();
        //关闭show title
        $(".tooltip").hide();
      });

      area.resizable({
        maxHeight: maxHeight,
        maxWidth: maxWidth,
        minHeight: minHeight,
        minWidth: minWidth,
        ghost: false,
        delay: 150,
        // helper: "ui-resizable-helper",
        distance: 5,
        handles: "e, s, w, se, n",
        // grid: [template.screen.gridX, template.screen.gridY],
        start: function (event, ui) {
          //修改防止成绝对坐标
          //ui.element.attr('top', ui.element.css('top'));
          //ui.element.attr('left', ui.element.css('left'));
          //ui.originalPosition.top=ui.element.css('top');
          //ui.originalPosition.left=ui.element.css('left');
        },
        resize: function (event, ui) {
          var cur = ui.helper;
          template.screen.showAreaInfo(cur, true);
        },
        stop: function (event, ui) {
          var originTop = ui.originalPosition.top;
          var orignLeft = ui.originalPosition.left;
          var orignWidth = ui.originalSize.width;
          var orignHeight = ui.originalSize.height;

          var top = ui.position.top;
          var left = ui.position.left;
          var width = ui.size.width;
          var height = ui.size.height;
          var cur = ui.originalElement;
          //check logo if enable
          if (cur.attr("id") == "area_logo") {
            var width = cur.innerWidth();
            var height = cur.innerHeight();
            if (orignWidth != width && orignHeight != height) {
              //angle
              for (var i = template.screen.logoSize.length - 1; i >= 0; i--) {
                var size = template.screen.logoSize[i];
                if (width >= size) {
                  cur.css("width", size);
                  break;
                }
              }

              for (var i = template.screen.logoSize.length - 1; i >= 0; i--) {
                var size = template.screen.logoSize[i];
                if (height >= size) {
                  cur.css("height", size);
                  break;
                }
              }
            } else if (orignWidth != width) {
              //H
              for (var i = template.screen.logoSize.length - 1; i >= 0; i--) {
                var size = template.screen.logoSize[i];
                if (width >= size) {
                  cur.css("width", size);
                  break;
                }
              }
            } else if (orignHeight != height) {
              //V
              for (var i = template.screen.logoSize.length - 1; i >= 0; i--) {
                var size = template.screen.logoSize[i];
                if (height >= size) {
                  cur.css("height", size);
                  break;
                }
              }
            }

            template.screen.updateAreaBody(cur);
            template.screen.showAreaInfo(cur);
            return;
          }

          if (!template.screen.template_type) {
            if (
              cur.attr("id") == "area_date" ||
              cur.attr("id") == "area_weather"
            ) {
              var width = cur.innerWidth();
              var height = cur.innerHeight();
              if (orignWidth != width && orignHeight != height) {
                //angle
                for (
                  var i = template.screen.DateWeatherSize.length - 1;
                  i >= 0;
                  i--
                ) {
                  var size = template.screen.DateWeatherSize[i];
                  if (width >= size) {
                    if (cur.position().left + size > template.screen.width) {
                      cur.css("left", template.screen.width - size);
                    }
                    if (cur.position().left < 0) {
                      cur.css("left", 0);
                    }
                    if (cur.position().top < 0) {
                      cur.css("top", 0);
                    }
                    cur.css("width", size);
                    break;
                  }
                }

                for (
                  var i = template.screen.DateWeatherSize.length - 1;
                  i >= 0;
                  i--
                ) {
                  var size = template.screen.DateWeatherSize[i];
                  if (height >= size) {
                    if (cur.position().top + size > template.screen.height) {
                      cur.css("top", template.screen.height - size);
                    }
                    cur.css("height", size);
                    break;
                  }
                }
              } else if (orignWidth != width) {
                //H
                for (
                  var i = template.screen.DateWeatherSize.length - 1;
                  i >= 0;
                  i--
                ) {
                  var size = template.screen.DateWeatherSize[i];
                  if (width >= size) {
                    if (cur.position().left + size > template.screen.width) {
                      cur.css("left", template.screen.width - size);
                    }
                    if (cur.position().left < 0) {
                      cur.css("left", 0);
                    }
                    if (cur.position().top < 0) {
                      cur.css("top", 0);
                    }
                    cur.css("width", size);
                    break;
                  }
                }
              } else if (orignHeight != height) {
                //V
                for (
                  var i = template.screen.DateWeatherSize.length - 1;
                  i >= 0;
                  i--
                ) {
                  var size = template.screen.DateWeatherSize[i];
                  if (height >= size) {
                    if (cur.position().top + size > template.screen.height) {
                      cur.css("top", template.screen.height - size);
                    }
                    if (cur.position().top < 0) {
                      cur.css("top", 0);
                    }
                    cur.css("height", size);
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
            console.info(
              "left:" +
                cur.css("width") +
                ", cur.left:" +
                cur.position().left +
                ", orignWidth:" +
                orignWidth +
                ", width:" +
                width +
                ",cur.width:" +
                cur.innerWidth() +
                ", orignHeight:" +
                orignHeight +
                ", height:" +
                height
            );
          }
          var dir = 0; //unkowned
          if (orignLeft > ui.position.left) {
            //to the border of left
            if (cur.position().left < 0) {
              if (template.screen.debug) {
                console.info(
                  "width:" +
                    width +
                    ", left:" +
                    cur.position().left +
                    ", origWidth:" +
                    orignWidth +
                    ", wishWidth:" +
                    (orignWidth + ui.originalPosition.left)
                );
              }
              cur.css("width", width + cur.position().left);
              cur.css("left", 0);
              template.screen.updateAreaBody(cur);
              template.screen.showAreaInfo(cur);
              return;
            }
            if (template.screen.debug) {
              console.info(
                "left:" + ui.position.left + ", cur.left:" + cur.position().left
              );
            }
            dir = template.screen.LEFT;
          } else if (width > orignWidth && height > orignHeight) {
            dir = template.screen.RIGHT_DOWN;
          } else if (width > orignWidth) {
            dir = template.screen.RIGHT;
          } else if (ui.originalPosition.top > ui.position.top) {
            dir = template.screen.UP;
          } else if (height > orignHeight) {
            dir = template.screen.DOWN;
          }
          var changed = true;
          //check only enlarge
          if (dir > 0) {
            if (dir == template.screen.RIGHT_DOWN) {
              cur.css("height", orignHeight);
              dir = template.screen.RIGHT;
              var interObjs = template.screen.getIntersectObj(cur, dir);
              if (interObjs != null && interObjs.length > 0) {
                var result = template.screen.dockArea(cur, interObjs, dir);
                if (!result) {
                  cur.css("width", orignWidth);
                } else {
                  hideMsg();
                }
              }
              dir = template.screen.DOWN;
              cur.css("height", height);
              interObjs = template.screen.getIntersectObj(cur, dir);
              if (interObjs != null && interObjs.length > 0) {
                var result = template.screen.dockArea(cur, interObjs, dir);
                if (!result) {
                  cur.css("top", originTop);
                  cur.css("height", orignHeight);
                } else {
                  hideMsg();
                }
              }
            } else {
              var interObjs = template.screen.getIntersectObj(cur, dir);
              if (interObjs != null && interObjs.length > 0) {
                //TODO change to nearby
                var result = template.screen.dockArea(cur, interObjs, dir);
                if (!result) {
                  cur.css("top", originTop);
                  cur.css("left", orignLeft);
                  cur.css("width", orignWidth);
                  cur.css("height", orignHeight);
                  changed = false;
                  //showMsg(template.screen.warnOverlap, 'warn');
                } else {
                  hideMsg();
                }
              } else {
                // 鼠标拉大 向左靠边 2013-12-20
                if (dir == template.screen.LEFT) {
                  if (width == template.screen.width) {
                    cur.css("left", 0);
                    cur.css("width", template.screen.width);
                    changed = false;
                  }
                }
                if (dir == template.screen.UP) {
                  if (height == template.screen.height) {
                    cur.css("top", 0);
                    cur.css("height", template.screen.height);
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
            console.info(
              "adJust" +
                changed +
                ", cur.innerHeight:" +
                cur.innerHeight() +
                ", dt.height:" +
                cur.children("dt").outerHeight(true)
            );
          }
          //cur.children('dd').css('height', (cur.innerHeight() - cur.children('dt').outerHeight(true)));
          template.screen.updateAreaBody(cur);
          //update area info
          template.screen.showAreaInfo(cur);
          return;
        },
      });
      area.draggable({
        containment: "parent",
        scroll: true,
        distance: 5,
        delay: 300,
        grid: [template.screen.gridX, template.screen.gridY],
        drag: function (event, ui) {
          var cur = $(ui.helper);
          template.screen.showAreaInfo(cur);
        },
        stoped: function (event, ui) {
          //暂时废弃该功能
          var cur = $(ui.helper);
          var orignLeft = ui.originalPosition.left;
          var orignTop = ui.originalPosition.top;
          var left = ui.position.left;
          var top = ui.position.top;
          var dir = 0;
          if (template.screen.debug) {
            console.info(
              "draggable stop:" +
                cur.text() +
                ", orignLeft:" +
                orignLeft +
                ", orignTop:" +
                orignTop +
                ", left:" +
                left +
                ", top:" +
                top +
                ", dir:" +
                dir
            );
          }
          var ac = -0.2; //精度
          if (template.screen.debug) {
            console.info(
              "acL:" +
                Math.abs(orignLeft - left) +
                ", acT:" +
                Math.abs(orignTop - top)
            );
          }
          if (orignLeft - left > ac && orignTop - top > ac) {
            dir = template.screen.DRAGE_LEFT_UP;
          } else if (left - orignLeft > ac && orignTop - top > ac) {
            dir = template.screen.DRAGE_RIGHT_UP;
          } else if (left - orignLeft > ac && top - orignTop > ac) {
            dir = template.screen.DRAGE_RIGHT_DOWN;
          } else if (orignLeft - left > ac && top - orignTop > ac) {
            dir = template.screen.DRAGE_LEFT_DOWN;
          } else if (orignLeft - left > ac) {
            dir = template.screen.DRAGE_LEFT;
          } else if (orignTop - top > ac) {
            dir = template.screen.DRAGE_UP;
          } else if (left - orignLeft > ac) {
            dir = template.screen.DRAGE_RIGHT;
          } else if (top - orignTop > ac) {
            dir = template.screen.DRAGE_DOWN;
          }

          if (template.screen.debug) {
            console.info(
              "draggable " +
                cur.text() +
                ", orignLeft:" +
                orignLeft +
                ", orignTop:" +
                orignTop +
                ", left:" +
                left +
                ", top:" +
                top +
                ", dir:" +
                dir
            );
          }
          changed = false;
          if (dir > 0) {
            var interObjs = template.screen.getIntersectObj(cur, dir);
            if (interObjs != null && interObjs.length > 0) {
              //TODO change to nearby
              cur.css("left", orignLeft);
              cur.css("top", orignTop);
            }
          }

          template.screen.showAreaInfo(cur);
        },
      });

      template.screen.zIndex++;
    },
    showAreaInfo: function (cur, resizing) {
      if (resizing == undefined) {
        resizing = false;
      }
      var info = $("#areaInfo");
      var stop = $(document).scrollTop();
      if (stop > 0) {
        info.css("top", stop);
      }
      info.show();
      $("#areaTitle").text(cur.text());

      var range = template.screen.getRealRange(cur);

      var xp = $("#areaX");
      xp.val(range.x);
      var yp = $("#areaY");
      yp.val(range.y);
      var wp = $("#areaWidth");
      wp.val(range.w);
      var hp = $("#areaHeight");
      hp.val(range.h);
      var wpp = $("#areaWidthPercent");
      wpp.val(range.wp);
      var hpp = $("#areaHeightPercent");
      hpp.val(range.hp);
      var id = cur.attr("id");
      $("#areaChange").val(id);

      if (
        id == "area_date" ||
        id == "area_time" ||
        id == "area_weather" ||
        id == "area_text"
      ) {
        var options = get_select_options(id);
        $("#areaFormat").empty().append(options);
        $("#area_settings").show();
        this.showAreaSettings();
        $("#extra_settings").show();
        if (id == "area_text") {
          $(".txt-setting").show();
        } else {
          $(".txt-setting").hide();
        }
      } else if (id.startsWith("area_id")) {
        $("#extra_settings").hide();
        $("#area_settings").show();
        this.showAreaSettings();
      } else {
        $("#area_settings").hide();
      }
    },
    getRealRange: function (cur) {
      //const el = document.getElementById("documentLabel");
      var curWidth = Math.round(cur.innerWidth());
      var curHeight = Math.round(cur.innerHeight());

      var widthPercent =
        Math.round((curWidth / template.screen.width) * 10000) / 100.0;
      if (widthPercent > 100) {
        widthPercent = 100;
      }
      var heightPercent =
        Math.round((curHeight / template.screen.height) * 10000) / 100.0;
      if (heightPercent > 100) {
        heightPercent = 100;
      }

      var width =
        (template.screen.realWidth * curWidth) / template.screen.width;
      var height =
        (template.screen.realHeight * curHeight) / template.screen.height;
      //adjust show

      if (template.screen.template_type) {
        width = width;
        height = height;
      } else {
        if (width % 2 != 0) {
          width--;
        }
        if (height % 2 != 0) {
          height--;
        }
        width = Math.round(width);
        height = Math.round(height);
      }
      var x = Math.round(
        (cur.position().left * template.screen.realWidth) /
          template.screen.width
      );
      var y = Math.round(
        (cur.position().top * template.screen.realHeight) /
          template.screen.height
      );

      return {
        x: x,
        y: y,
        w: width,
        h: height,
        wp: widthPercent,
        hp: heightPercent,
      };
    },
    hideAreaInfo: function () {
      $("#areaInfo").hide();
    },
    removeArea: function (id) {
      for (var i = 0; i < this.areas.length; i++) {
        if (this.areas[i].id == id) {
          if (this.areas[i].areaId) {
            this.deletes.push(template.screen.areas[i].areaId);
          }

          if (this.areas[i].type == 30) {
            var tmp = id.split("-");

            this.id_indexes = this.id_indexes.filter(function (item) {
              return item != tmp[1];
            });
          }

          this.areas.splice(i, 1);
          break;
        }
      }
      template.screen.hideAreaInfo();
    },

    _template: function (id, name, type) {
      if (template.screen.readonly) {
        return `<dl id=${id}  style="width: 400px;" class="common-style" area-type="${type}">
                    <dt>
                    ${name}
                    </dt>
                    <dd></dd>
                    </dl>`;
      } else {
        return `<dl id=${id}  style="width: 400px;" class="common-style" area-type="${type}">
                    <dt>
                    ${name}
                    <div class="icon"><img class="close" title="Close" src="/images/icons/cross2.png" >
                    </dt>
                    <dd></dd>
                    </dl>`;
      }
    },

    imagePage: function (curpage, type) {
      var folderId = $("#folderId").val();
      if (folderId == "") {
        folderId = -1;
      }
      $.get(
        "/template/images/" +
          curpage +
          "?type=" +
          type +
          "&refresh=1" +
          "&folder_id=" +
          folderId +
          "&t=" +
          new Date().getTime(),
        function (data) {
          $("#imageContent").html(data);
        }
      );
    },

    isOverlapping: function () {},
    OverlappingOne: function () {
      var areas = $("#screen dl");
      //one or less area ignore
      if (areas.length <= 1) {
        return false;
      }
      var result = false;
      for (var i = 0; i < areas.length; i++) {
        if (areas[i].id == "area_5" || areas[i].id == "area_logo") {
          continue;
        }

        var cur = $(areas[i]);
        for (var j = i + 1; j < areas.length; j++) {
          if (
            areas[j].id == "area_logo" ||
            areas[j].id == "area_5" ||
            areas[j].id == areas[i].id
          ) {
            continue;
          }

          var next = $(areas[j]);
          if (template.screen.isIntersect(cur, next)) {
            next
              .css("borderWidth", "1px")
              .css("borderStyle", "")
              .css("borderColor", "red");
            result = true;
            break;
          } else {
            next
              .css("borderWidth", "")
              .css("borderStyle", "")
              .css("borderColor", "");
          }
        }
        if (result) {
          break;
        }
      }
      return result;
    },

    enableToolbar: function (id, show) {
      var tmp = id.split("_");

      var type = $(`#${id}`).attr("area-type");

      var tid = tmp[1];
      if (show) {
        template.screen.removeArea(id);
      }
      if (!id.startsWith("area_id")) {
        $(`#btn-${tid}`).addClass("disabled");
      }
    },

    createData: function () {
      if (this.areas) {
        var data = this.areas.map((area) => {
          var parent = $("#" + area.id);

          if (!parent) {
            return null;
          }
          var res = {
            x: parent.position().left,
            y: parent.position().top,
            w: parent.innerWidth(),
            h: parent.innerHeight(),
            name: area.name,
            type: area.type,
            zindex: parent.css("z-index"),
            areaId: area.areaId == undefined ? 0 : area.areaId,
            areaName: area.id.split("_")[1],
          };
          if (area.mediaId !== undefined) {
            res.mediaId = area.mediaId;
          }
          if (area.settings) {
            res.settings = area.settings;
          }
          return res;
        });
        if (this.bg != null) {
          mediaId = this.bg.mediaId;
          areaId = this.bg.areaId == undefined ? 0 : this.bg.areaId;
          var bg = {
            x: 0,
            y: 0,
            w: this.width,
            h: this.height,
            areaId: areaId,
            type: this.bg.type,
            name: "BG",
            areaName: "BG",
            zindex: 0,
            mediaId: mediaId,
          };
          data.push(bg);
        }
      }
      return data;
    },
    getCurAreaIndex: function () {
      if (!this.curObj) {
        return -1;
      }
      //var type = this.curObj.attr("area-type");
      var id = this.curObj.attr("id");
      for (var i = 0; i < this.areas.length; i++) {
        //if (this.areas[i].type == type) {
        if (this.areas[i].id == id) {
          return i;
        }
      }
      return -1;
    },
    showAreaSettings() {
      var curIndex = this.getCurAreaIndex();
      if (curIndex == -1) {
        return;
      }
      var settings = this.areas[curIndex].settings
        ? this.areas[curIndex].settings
        : null;

      if (settings) {
        $("#areaFontSize").val(settings.font_size);
        $("#areaBgColor").val(settings.bg_color);
        $("#areaFontColor").val(settings.color);
        $("#areaTrans").val(settings.transparent);
        $("#areaFormat").val(settings.style);

        if (settings.direction) {
          $("#direction").val(settings.direction);
        }
      }
    },
    setAreasSettings() {
      var newSetting = {
        font_size: $("#areaFontSize").val(),
        bg_color: $("#areaBgColor").val(),
        color: $("#areaFontColor").val(),
        transparent: $("#areaTrans").val(),
        style: $("#areaFormat").val(),
      };

      if ($("#direction").is(":visible")) {
        newSetting.direction = $("#direction").val();
      }
      var curIndex = this.getCurAreaIndex();
      if (curIndex == -1) {
        return;
      }
      this.areas[curIndex].settings = newSetting;
    },
    getAndSetIdIndex() {
      for (var i = 1; ; i <= 100) {
        if (!this.id_indexes.includes(i)) {
          this.id_indexes.push(i);
          return i;
        }
        i++;
      }
    },
  },
};
