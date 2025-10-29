function getRect(cur) {
  return {
    id: cur.attr("id"),
    top: Math.round(cur.position().left),
    left: Math.round(cur.position().left),
    width: Math.round(cur.width()),
    height: Math.round(cur.height()),
  };
}
var template = {
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

    template.deletes = new Array();
    template.tabIndex = 0;
    //init logo size
    for (var i = 0; i < template.logoRealSize.length; i++) {
      template.logoSize[i] =
        (template.width * template.logoRealSize[i]) / template.realWidth;
    }
    //init date、weather size
    for (var i = 0; i < template.DateWeatherRealSize.length; i++) {
      template.DateWeatherSize[i] =
        (template.width * template.DateWeatherRealSize[i]) / template.realWidth;
    }

    if (!template.template_type) {
      template.gridX =
        template.width > template.height
          ? (4 * template.width) / template.realWidth
          : (2 * template.height) / template.realHeight;
      template.gridY =
        template.width > template.height
          ? (2 * template.height) / template.realHeight
          : (4 * template.width) / template.realWidth;
    } else {
      template.gridX = 1;
      template.gridY = 1;
    }
    //template.gridX = template.width > template.height ? (4 * template.width) / template.realWidth : (2 * template.height) / template.realHeight;
    //template.gridY = template.width > template.height ? (2 * template.height) / template.realHeight : (4 * template.width) / template.realWidth;

    //初始化屏幕的高度和宽度
    //$('.screen').css('width',template.width).css('height', template.height);
    if (template.readonly) {
      return;
    }

    $(".btn-icon").each(function () {
      $(this).removeClass("disabled");
    });

    $("#save")
      .off("click")
      .on("click", function (event) {
        template.save();
      });
  },

  initArea: function (area, settings) {
    var id = "area_" + area.area_name;

    if (area.area_type == "30") {
      var tmp = area.area_name.split("-");
      this.id_indexes.push(Number(tmp[1]));
    }

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
    template.areas.push(newArea);
    template.showArea(id, area);
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

    var range1 = template.getRealRange(curObj);
    var range2 = template.getRealRange(targetObj);
    var x1 = range1.x;
    var y1 = range1.y;
    var x2 = x1 + range1.w;
    var y2 = y1 + range1.h;

    var xx1 = range2.x;
    var yy1 = range2.y;
    var xx2 = xx1 + range2.w;
    var yy2 = yy1 + range2.h;

    if (template.debug) {
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
      case template.RIGHT:
        //上方修正
        result =
          xx1 >= x1 &&
          x2 > xx1 &&
          ((y1 >= yy1 && y1 < yy2) ||
            (y2 > yy1 && y2 <= yy2) ||
            (y1 < yy1 && y2 > yy2) ||
            (y1 == yy1 && y2 == yy2));
        break;
      case template.LEFT:
        result =
          x2 > xx1 &&
          x1 < xx2 &&
          ((y1 >= yy1 && y1 < yy2) ||
            (y2 > yy1 && y2 <= yy2) ||
            (y1 < yy1 && y2 > yy2) ||
            (y1 == yy1 && y2 == yy2));
        break;
      case template.UP:
        result =
          y2 > yy1 &&
          yy2 > y1 &&
          ((x1 >= xx1 && x1 < xx2) ||
            (x2 > xx1 && x2 <= xx2) ||
            (x1 < xx1 && x2 > xx2) ||
            (x1 == xx1 && x2 == xx2));
        break;
      case template.DOWN:
        result =
          yy1 >= y1 &&
          y2 > yy1 &&
          ((x1 >= xx1 && x1 < xx2) ||
            (x2 > xx1 && x2 <= xx2) ||
            (x1 < xx1 && x2 > xx2) ||
            (x1 == xx1 && x2 == xx2));
        break;
      case template.RIGHT_DOWN:
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
      case template.DRAGE_RIGHT:
      case template.DRAGE_DOWN:
      case template.DRAGE_RIGHT_DOWN:
      case template.DRAGE_LEFT:
      case template.DRAGE_UP:
      case template.DRAGE_LEFT_UP:
      case template.DRAGE_RIGHT_UP:
      case template.DRAGE_LEFT_DOWN:
      default:
        result =
          /*顶点相交*/
          (x1 >= xx1 && x1 < xx2 && y1 >= yy1 && y1 < yy2) /*Left,Top*/ ||
          (x2 > xx1 && x2 <= xx2 && y1 >= yy1 && y1 < yy2) /*Right, Top*/ ||
          (x1 >= xx1 && x1 < xx2 && y2 > yy1 && y2 <= yy2) /*Left, Bottom*/ ||
          (x2 > xx1 && y2 > yy1 && x2 <= xx2 && y2 <= yy2) /*Right, Bottom*/ ||
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
    if (template.debug) {
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
      case template.RIGHT_DOWN:
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
        if (template.debug) {
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
    if (template.template_type) {
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
            case template.RIGHT_DOWN:
              if (template.isIntersect(curObj, kid, template.RIGHT)) {
                result.push({
                  direct: template.RIGHT,
                  obj: kid,
                });
              } else if (template.isIntersect(curObj, kid, template.DOWN)) {
                result.push({
                  direct: template.DOWN,
                  obj: kid,
                });
              }
              break;
            default:
              if (template.isIntersect(curObj, kid, direct)) {
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
              case template.RIGHT_DOWN:
                if (template.isIntersect(curObj, kid, template.RIGHT)) {
                  result.push({
                    direct: template.RIGHT,
                    obj: kid,
                  });
                } else if (template.isIntersect(curObj, kid, template.DOWN)) {
                  result.push({
                    direct: template.DOWN,
                    obj: kid,
                  });
                }
                break;
              default:
                if (template.isIntersect(curObj, kid, direct)) {
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
    if (template.debug) {
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
    var enlargeX = template.width > template.height ? 10 : 5; //横向差为10倍
    var enlargeY = template.width > template.height ? 5 : 10; //纵向差为5倍
    var curX = curObj.position().left;
    var curY = curObj.position().top;
    var curW = curObj.outerWidth();
    var curH = curObj.outerHeight();

    switch (direct) {
      case template.RIGHT:
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
      case template.LEFT:
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
      case template.UP:
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
      case template.DOWN:
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
      case template.RIGHT_DOWN:
        var minX = 0;
        var minY = 0;
        var width = 0;
        var height = 0;
        var minX = targetObjs[0].obj.position().left;
        var minY = targetObjs[0].obj.position().top;
        for (var i = 0; i < targetObjs.length; i++) {
          if (targetObjs[i].direct == template.RIGHT) {
            if (targetObjs[i].obj.position().left < minX) {
              minX = targetObjs[i].obj.position().left;
            }
            if (template.debug) {
              console.info(
                "dockArea[" +
                  curObj.text() +
                  "] RIGHT_DOWN, intersect RIGHT [" +
                  targetObjs[i].obj.text() +
                  "], minX:" +
                  minX
              );
            }
          } else if (targetObjs[i].direct == template.DOWN) {
            if (targetObjs[i].obj.position().top < minY) {
              minY = targetObjs[i].obj.position().top;
            }
            if (template.debug) {
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

        if (template.debug) {
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
      case template.DRAGE_LEFT:
        var maxX =
          targetObjs[0].obj.position().left + targetObjs[0].obj.outerWidth();
        for (var i = 1; i < targetObjs.length; i++) {
          if (
            targetObjs[i].obj.position().left + targetObjs[i].obj.outerWidth() >
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
      case template.DRAGE_UP:
        var maxY =
          targetObjs[0].obj.position().top + targetObjs[0].obj.outerHeight();
        for (var i = 1; i < targetObjs.length; i++) {
          if (
            targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight() >
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
      case template.DRAGE_RIGHT:
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
      case template.DRAGE_DOWN:
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
      case template.DRAGE_LEFT_UP:
        var width = curObj.outerWidth();
        var height = curObj.outerHeight();
        var left = curObj.position().left;
        var top = curObj.position().top;
        var targetX = 0;
        var targetY = 0;
        for (var i = 0; i < targetObjs.length; i++) {
          targetX =
            targetObjs[i].obj.position().left + targetObjs[i].obj.outerWidth();
          if (
            left >= targetObjs[i].obj.position().left &&
            left < targetX &&
            (targetX - left) * enlargeX < width &&
            targetX + width <= template.width
          ) {
            curObj.css("left", targetX);
            result = true;
          }

          targetY =
            targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight();
          if (
            top >= targetObjs[i].obj.position().top &&
            top < targetY &&
            (targetY - top) * enlargeY < height &&
            targetY + height <= template.height
          ) {
            curObj.css("top", targetY);
            result = true;
          }
        }
        break;
      case template.DRAGE_RIGHT_UP:
        var width = curObj.outerWidth();
        var height = curObj.outerHeight(true);
        var left = curObj.position().left + width;
        var top = curObj.position().top;
        var targetX = 0;
        var targetY = 0;
        for (var i = 0; i < targetObjs.length; i++) {
          targetX =
            targetObjs[i].obj.position().left + targetObjs[i].obj.outerWidth();
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
            targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight();
          if (
            top >= targetObjs[i].obj.position().top &&
            top < targetY &&
            (targetY - top) * enlargeY < height &&
            targetY + height <= template.height
          ) {
            curObj.css("top", targetY);
            result = true;
          }
        }
        break;
      case template.DRAGE_RIGHT_DOWN:
        var width = curObj.outerWidth();
        var height = curObj.outerHeight();
        var left = curObj.position().left + width;
        var top = curObj.position().top + height;
        var targetX = 0;
        var targetY = 0;
        for (var i = 0; i < targetObjs.length; i++) {
          targetX =
            targetObjs[i].obj.position().left + targetObjs[i].obj.outerWidth();
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
            targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight();
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
      case template.DRAGE_LEFT_DOWN:
        var width = curObj.outerWidth();
        var height = curObj.outerHeight();
        var left = curObj.position().left;
        var top = curObj.position().top + height;
        var targetX = 0;
        var targetY = 0;
        for (var i = 0; i < targetObjs.length; i++) {
          targetX =
            targetObjs[i].obj.position().left + targetObjs[i].obj.outerWidth();
          if (
            left >= targetObjs[i].obj.position().left &&
            left < targetX &&
            (targetX - left) * enlargeX < width &&
            targetX + width <= template.width
          ) {
            curObj.css("left", targetX);
            result = true;
          }

          targetY =
            targetObjs[i].obj.position().top + targetObjs[i].obj.outerHeight();
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
    if (x + w > template.width || y + h > template.height) {
      result = false;
      curObj.css("left", curX);
      curObj.css("top", curY);
      curObj.css("width", curW);
      curObj.css("height", curH);
    }
    if (template.debug) {
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
    if (left + nWidth > template.width) {
      nWidth = template.width - left;
      curObj.css("width", nWidth);
    }
    if (top + nHeight > template.height) {
      nHeight = template.height - top;
      curObj.css("height", nHeight);
    }

    var width = Math.round((template.realWidth * nWidth) / template.width);
    var height = Math.round((template.realHeight * nHeight) / template.height);

    if (template.debug) {
      console.info("adjustArea width:" + width + ", height:" + height);
    }

    //  test
    if (!template.template_type) {
      var wmod = width % 4;
      if (wmod != 0) {
        //adjust width
        width -= wmod;

        nWidth = (width * template.width) / template.realWidth;
        curObj.css("width", nWidth);
        if (template.debug) {
          console.info(
            "adjustArea realWidth:" + width + ", show width:" + nWidth
          );
        }
      }

      if (height % 2 != 0) {
        //addjust height
        height--;
        nHeight = (height * template.height) / template.realHeight;
        curObj.css("height", nHeight);
        if (template.debug) {
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
    }
    template.showAreaInfo($("#" + id));
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
        font_family: "Arial",
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
        font_family: "Roboto",
        font_size: 80,
        bg_color: "#000000",
        color: "#FFFFFF",
        transparent: 100,
        style: 0,
      };

      newArea.settings = newSetting;
    }
    template.areas.push(newArea);
    template.showArea(id, area);
  },

  showArea: function (id, area) {
    w = area.w ? Number(area.w) : 64;
    h = area.h ? Number(area.h) : 64;
    x = area.x ? Number(area.x) : 0;
    y = area.y ? Number(area.y) : 0;

    var title = area.name;

    var maxW = template.width;
    var maxH = template.height;
    var minW = 48;
    var minH = 48;
    if (id == "area_date") {
      minW = this.minDateRealWidth / this.radio;
      minH = this.minDateRealHeight / this.radio;
    } else if (id.startsWith("area_id")) {
      minW = 16 / this.radio;
      minH = 16 / this.radio;
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
    template.changePosition(id, x, y, w, h, "absolute");
  },

  isResizeLimited: function (obj) {
    var id = obj.attr("id");
    var orignLeft = obj.position().left;
    var orignTop = obj.position().top;
    var orignWidth = obj.innerWidth();
    var orignHeight = obj.innerHeight();
    if (orignTop >= template.height || orignLeft >= template.width) {
      return true;
    }

    if (orignWidth > template.width || orignHeight > template.height) {
      return true;
    }

    if (
      orignLeft + orignWidth > template.width ||
      orignTop + orignHeight > template.height
    ) {
      return true;
    }

    return false;
  },
  adjustResize: function (obj, dir) {
    var orignLeft = Math.round(obj.position().left);
    var orignTop = Math.round(obj.position().top);
    var orignWidth = Math.round(obj.innerWidth());
    var orignHeight = Math.round(obj.innerHeight());
    var is_overlapped = false;
    var overlaps = template.isOverlapped(getRect(obj));
    if (overlaps && overlaps.length) {
      is_overlapped = true;
    }
    var changed = true;

    switch (dir) {
      case template.LEFT:
        if (orignLeft > 0) {
          if (obj.attr("id") == "area_logo") {
            obj.css("left", orignLeft);
            obj.css("width", orignWidth);
          } else {
            obj.css("left", orignLeft - template.gridX);
            obj.css("width", orignWidth + template.gridX);
          }
        } else {
          changed = false;
        }
        break;
      case template.UP:
        if (orignTop > 0) {
          if (obj.attr("id") == "area_logo") {
            obj.css("top", orignTop);
            obj.css("height", orignHeight);
          } else {
            obj.css("top", orignTop - template.gridY);
            obj.css("height", orignHeight + template.gridY);
          }
        } else {
          changed = false;
        }
        break;
      case template.RIGHT:
        if (orignLeft + orignWidth < template.width) {
          if (obj.attr("id") == "area_logo") {
            obj.css("width", orignWidth);
          } else {
            obj.css("width", orignWidth + template.gridX);
          }
        } else {
          changed = false;
        }
        break;
      case template.DOWN:
        if (orignTop + orignHeight < template.height) {
          if (obj.attr("id") == "area_logo") {
            obj.css("height", orignHeight);
          } else {
            obj.css("height", orignHeight + template.gridY);
          }
        } else {
          changed = false;
        }
        break;
    }

    if (changed && !is_overlapped) {
      var originRect = getRect(obj);

      var overlaps = template.isOverlapped(originRect);
      if (overlaps && overlaps.length) {
        template.adjustPosition(originRect, obj, overlaps, dir);
        template.showAreaInfo(obj);
      }
    }
  },
  adjustMove: function (obj, dir) {
    var orignLeft = obj.position().left;
    var orignTop = obj.position().top;
    var orignWidth = obj.width();
    var orignHeight = obj.height();
    var changed = true;
    var is_overlapped = false;
    var overlaps = template.isOverlapped(getRect(obj));
    if (overlaps && overlaps.length) {
      is_overlapped = true;
    }

    switch (dir) {
      case template.LEFT:
        if (orignLeft >= template.gridX) {
          obj.css("left", Math.round(orignLeft - template.gridX));
        } else if (orignLeft > 0) {
          obj.css("left", 0);
        } else {
          changed = false;
        }
        break;
      case template.UP:
        if (orignTop >= template.gridY) {
          obj.css("top", Math.round(orignTop - template.gridY));
        } else if (orignTop > 0) {
          obj.css("top", 0);
        } else {
          changed = false;
        }
        break;
      case template.RIGHT:
        if (orignLeft + orignWidth + template.gridX <= template.width) {
          obj.css("left", Math.round(orignLeft + template.gridX));
        }
        {
          changed = false;
        }
        break;
      case template.DOWN:
        if (orignTop + orignHeight + template.gridY <= template.height) {
          obj.css("top", orignTop + template.gridY);
        } else if (orignTop + orignHeight < template.height) {
          obj.css("top", template.height - orignHeight);
        } else {
          changed = false;
        }
        break;
    }

    if (changed && !is_overlapped) {
      var overlaps = template.isOverlapped(getRect(obj));

      if (overlaps && overlaps.length) {
        obj.css("top", orignTop);
        obj.css("left", orignLeft);
        obj.css("width", orignWidth);
        obj.css("height", orignHeight);
      }
    }
    template.showAreaInfo(obj);
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
    zIndex = zIndex || template.zIndex;

    $("#screen").append(template._template(id, title, type));
    var area = $("#" + id);
    area.attr("tabindex", template.tabIndex);
    template.tabIndex++;

    area.off("click").on("click", function (event) {
      var cur = $(this);
      cur.focus();
      template.curObj = cur;
      if (template.readonly) {
        template.showAreaInfo(cur);
        return;
      }
      if (template.curObj != null) {
        if (template.curObj.attr("id") == cur.attr("id")) {
          cur.focus();
          template.showAreaInfo(cur);
          //return;
        }
      }
      //template.curObj.addClass("selected");
      template.curObj.off("keydown").on("keydown", function (event) {
        var dealed = false;
        var keyCode = event.keyCode;
        if (keyCode == 27) {
          if (template.curObj != null) {
            //	template.curObj.unbind("keydown");
            //template.curObj.children("h3").removeClass("selected");
            template.curObj = null;
          }
        }
        if (event.ctrlKey) {
          switch (keyCode) {
            case 74: //j
            case 37: //left
              template.adjustResize($(this), template.LEFT);
              dealed = true;
              break;
            case 75: //k
            case 38: //up
              template.adjustResize($(this), template.UP);
              dealed = true;
              break;
            case 76: //l
            case 39: //right
              template.adjustResize($(this), template.RIGHT);
              dealed = true;
              break;
            case 77: //m
            case 40: //down
              template.adjustResize($(this), template.DOWN);
              dealed = true;
              break;
          }
        } else {
          switch (keyCode) {
            case 74: //j
            case 37: //left
              template.adjustMove($(this), template.LEFT);
              dealed = true;
              break;
            case 75: //k
            case 38: //up
              template.adjustMove($(this), template.UP);
              dealed = true;
              break;
            case 76: //l
            case 39: //right
              template.adjustMove($(this), template.RIGHT);
              dealed = true;
              break;
            case 77: //m
            case 40: //down
              template.adjustMove($(this), template.DOWN);
              dealed = true;
              break;
          }
        }
        if (dealed) {
          event.preventDefault();
        }
      });
      template.showAreaInfo(cur);
    });
    area.css("z-index", zIndex);
    this.enableToolbar(id, false);

    if (template.readonly) {
      return;
    }
    area.click();

    $("#" + id + " .close").on("click", function (event) {
      event.preventDefault();
      template.enableToolbar(id, true);
      $("#" + id).remove();
      //关闭show title
      $(".tooltip").hide();
    });

    area.resizable({
      containment: "#screen",
      maxHeight: maxHeight,
      maxWidth: maxWidth,
      minHeight: minHeight,
      minWidth: minWidth,
      autoHide: true,
      handles: "e, s, w, se, n",

      resize: function (event, ui) {
        var cur = ui.element;
        template.showAreaInfo(cur, true);
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
        var cur = ui.element;
        var curRect = {
          id: cur.attr("id"),
          top: top,
          left: left,
          width: width,
          height: height,
        };
        var originRect = {
          id: cur.attr("id"),
          top: originTop,
          left: orignLeft,
          width: orignWidth,
          height: orignHeight,
        };

        template.showAreaInfo(cur);

        var overlaps = template.isOverlapped(originRect);

        if (overlaps != false && overlaps.length) {
          return;
        }

        var dir = 0;
        if (ui.position.left < ui.originalPosition.left) {
          direction += "left";
          dir = template.LEFT;
        } else if (width > orignWidth && height > orignHeight) {
          dir = template.RIGHT_DOWN;
        } else if (width > orignWidth) {
          dir = template.RIGHT;
        } else if (ui.originalPosition.top > ui.position.top) {
          dir = template.UP;
        } else if (height > orignHeight) {
          dir = template.DOWN;
        }

        if (dir === 0) {
          return;
        }
        var cur = ui.element;
        var cur_id = cur.attr("id");
        if (
          cur_id == "area_date" ||
          cur_id == "area_time" ||
          cur_id == "area_mask"
        ) {
          return;
        }

        overlaps = template.isOverlapped(curRect);
        template.adjustPosition(originRect, cur, overlaps, dir);
        template.showAreaInfo(cur);
      },
    });

    area.draggable({
      containment: "parent",
      scroll: true,
      //distance: 5,
      delay: 300,
      grid: [1, 1],
      drag: function (event, ui) {
        var cur = $(ui.helper);
        template.showAreaInfo(cur);
      },
      stop: function (event, ui) {
        var cur = $(ui.helper);
        var top = ui.position.top;
        var left = ui.position.left;
        var width = cur.width();
        var height = cur.height();

        cur.css("top", Math.round(top));
        cur.css("left", Math.round(left));
        var curRect = {
          id: cur.attr("id"),
          top: ui.position.top,
          left: ui.position.left,
          width: cur.width(),
          height: cur.height(),
        };

        template.showAreaInfo(cur);
        var overlaps = template.isOverlapped(curRect);
        if (overlaps && overlaps.length) {
          console.log("there are Overlapped zones");
        }
      },
    });

    template.zIndex++;
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

    var range = template.getRealRange(cur);

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
      this.showAreaSettings(id);
      $("#extra_settings").show();
      $("#areaFontFamily").hide();
      $('.status-setting').hide();
      if (id == "area_text") {
        $(".txt-setting").show();
      } else {
        $(".txt-setting").hide();
      }
    } else if (id.startsWith("area_id")) {
      $("#extra_settings").hide();
      $("#area_settings").show();
      $("#areaFontFamily").show();
      $('.status-setting').show();
      this.showAreaSettings(id);
    } else {
      $('.status-setting').hide();
      $("#area_settings").hide();
    }
  },
  getRealRange: function (cur) {
    //const el = document.getElementById("documentLabel");
    var curWidth = Math.round(cur.innerWidth());
    var curHeight = Math.round(cur.innerHeight());

    var widthPercent = Math.round((curWidth / template.width) * 10000) / 100.0;
    if (widthPercent > 100) {
      widthPercent = 100;
    }
    var heightPercent =
      Math.round((curHeight / template.height) * 10000) / 100.0;
    if (heightPercent > 100) {
      heightPercent = 100;
    }

    var width = (template.realWidth * curWidth) / template.width;
    var height = (template.realHeight * curHeight) / template.height;
    //adjust show

    if (template.template_type) {
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
      (cur.position().left * template.realWidth) / template.width
    );
    var y = Math.round(
      (cur.position().top * template.realHeight) / template.height
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
          this.deletes.push(template.areas[i].areaId);
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
    template.hideAreaInfo();
  },

  _template: function (id, name, type) {
    if (template.readonly) {
      return `<div id=${id}  style="width: 400px;" class="common-style template-zone" area-type="${type}">
                      <h4 class="zone-header">${name}</h4>         
                                  </div>`;
    } else {
      return `<div id=${id}  style="width: 400px;" class="common-style template-zone" area-type="${type}">
          <h4 class="zone-header">${name}</h4>
          <span class="close"><img  title="Close" src="/assets/icons/cross2.png" ></span>
                      </div>`;
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
        if (template.isIntersect(cur, next)) {
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

    var tid = tmp[1];
    if (show) {
      template.removeArea(id);
      if (!id.startsWith("area_id")) {
        $(`#btn-${tid}`).removeClass("disabled");
      }
    } else {
      if (!id.startsWith("area_id")) {
        $(`#btn-${tid}`).addClass("disabled");
      }
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
      if (this.areas[i].id == id) {
        return i;
      }
    }
    return -1;
  },
  showAreaSettings(id) {
    var settings = null;
    for (var i = 0; i < this.areas.length; i++) {
      if (this.areas[i].id == id) {
        settings = this.areas[i].settings;
        break;
      }
    }

    if (settings) {
      $("#areaFontFamily").val(settings.font_family);
      var fontSlider = document.getElementById("rangeFontsize");
      fontSlider.noUiSlider.set(settings.font_size);
      // $("#areaFontSize").val(settings.font_size);
      $("#areaBgColor").val(settings.bg_color);
      $("#areaFontColor").val(settings.color);
      $("#areaTrans").val(settings.transparent);
      $("#areaFormat").val(settings.style);

      if (settings.charger_setting_id) {
        $("#charger_setting_id").val(settings.charger_setting_id ? settings.charger_setting_id : 0);
        $("#charger_setting_id").trigger("change");
      }

      if (settings.style) {
        $("#charger_setting_type").val(settings.style).trigger("change");
      }

      if (settings.direction) {
        $("#direction").val(settings.direction);
      }
    }
  },
  setAreasSettings() {
    var fontSlider = document.getElementById("rangeFontsize");
    var newSetting = {
      font_family: $("#areaFontFamily").val(),
      font_size: fontSlider.noUiSlider.get(),
      bg_color: $("#areaBgColor").val(),
      color: $("#areaFontColor").val(),
      transparent: $("#areaTrans").val(),
      style: $("#areaFormat").val(),
      
    };

    if ($("#charger_setting_id").is(":visible")) {
      newSetting['charger_setting_id'] =  $("#charger_setting_id").val() ? $("#charger_setting_id").val() : null;
    };
    if ($("#charger_setting_type").is(":visible")) {
      newSetting['style'] = $("#charger_setting_type").val() ? $("#charger_setting_type").val() : 1;
    }

    if ($("#direction").is(":visible")) {
      newSetting.direction = $("#direction").val();
    }
    var curIndex = this.getCurAreaIndex();
    if (curIndex == -1) {
      return;
    }
    this.areas[curIndex].settings = newSetting;
  },
  getAndSetIdIndex: function () {
    for (var i = 1; ; i <= 100) {
      if (!this.id_indexes.includes(i)) {
        this.id_indexes.push(i);
        return i;
      }
      i++;
    }
  },
  isOverlapped: function (curDiv) {
    var overlaps = [];

    if (
      curDiv.id == "area_date" ||
      curDiv.id == "area_time" ||
      curDiv.id == "area_mask" ||
      curDiv.id == "area_text"
    ) {
      return false;
    }

    $(".template-zone").each(function () {
      var targetId = $(this).attr("id");
      overlap = false;
      if (curDiv.id == targetId) {
        return;
      }
      if (
        targetId == "area_date" ||
        targetId == "area_time" ||
        targetId == "area_mask" ||
        targetId == "area_text"
      ) {
        return false;
      }

      var otherLeft = Math.round($(this).position().left);
      var otherTop = Math.round($(this).position().top);
      var otherWidth = Math.round($(this).width());
      var otherHeight = Math.round($(this).height());

      if (
        curDiv.left < otherLeft + otherWidth &&
        curDiv.left + curDiv.width > otherLeft &&
        curDiv.top < otherTop + otherHeight &&
        curDiv.top + curDiv.height > otherTop
      ) {
        overlap = true;
      }

      if (overlap) {
        overlaps.push($(this));
      }
    });
    if (overlaps.length) {
      return overlaps;
    }
    return false;
  },
  adjustPosition: function (originRect, cur, overlaps, dir) {
    if (overlaps && overlaps.length) {
      var originTop = originRect.top;
      var originLeft = originRect.left;
      var originWidth = originRect.width;
      var originHeight = originRect.height;

      if (dir === template.RIGHT) {
        var targetX = Infinity;
        overlaps.forEach((element) => {
          if (element.position().left < targetX) {
            targetX = Math.round(element.position().left);
          }
        });
        cur.css("width", targetX - cur.position().left);
      }

      if (dir === template.LEFT) {
        var targetX = 0;
        overlaps.forEach((element) => {
          if (element.position().left + element.width() > targetX) {
            targetX = element.position().left + element.width();
          }
        });
        cur.css("left", targetX);

        cur.css("width", originWidth + originLeft - targetX);
      }
      if (dir === template.DOWN) {
        var targetY = Infinity;
        overlaps.forEach((element) => {
          if (
            element.position().top < targetY &&
            element.position().top > cur.position().top
          ) {
            targetY = Math.round(element.position().top);
          }
        });

        if (targetY !== Infinity) {
          cur.css("height", targetY - originTop);
        }
      }
      if (dir === template.UP) {
        var targetY = cur.position().top;
        overlaps.forEach((element) => {
          if (element.position().top + element.height() > targetY) {
            targetY = Math.round(element.position().top + element.height());
          }
        });

        if (originTop + originHeight - targetY > originHeight) {
          cur.css("height", originTop + originHeight - targetY);
        }

        cur.css("top", targetY);
        //cur.css("height", originTop + orignHeight - targetY);
      }
      if (dir === template.RIGHT_DOWN) {
        var newWidth = Infinity;
        var newHeight = Infinity;
        if (overlaps && overlaps.length) {
          overlaps.forEach((element) => {
            if (
              element.position().left > originLeft &&
              element.position().left - originLeft < newWidth &&
              element.position().left - originLeft > originWidth
            ) {
              newWidth = Math.round(element.position().left - originLeft);
            }
            if (
              element.position().top > originRect.top &&
              element.position().top - originTop < newHeight &&
              element.position().top - originTop > originHeight
            ) {
              newHeight = Math.round(element.position().top - originTop);
            }
          });
          if (newWidth != Infinity && newWidth != 0) {
            cur.css("width", newWidth);
          }
          if (newHeight != Infinity) {
            cur.css("height", newHeight);
          }
        }
      }
    }
  },

  changeX: function () {
    t_width = template.width;
    var id = $("#areaChange").val();
    var areaX = parseInt($("#areaX").val());
    if (areaX < 0) {
      areaX = 0;
    }
    var obj = $("#" + id);
    var x = obj.position().left;
    var w = obj.innerWidth();
    if (isNaN(areaX)) {
      $("#areaX").val(2 * x);
    } else {
      if (areaX % 2 != 0) {
        areaX--;
      }
      var left = areaX / template.radio;
      if (left + w > t_width) {
        left = t_width - w;
      }
      $("#" + id).css("left", left);
      $("#areaX").val(template.radio * left);
    }
  },
  changeY: function () {
    var t_height = template.height;
    var id = $("#areaChange").val();
    var areaY = parseInt($("#areaY").val());
    var obj = $("#" + id);
    var y = obj.position().top;
    var h = obj.innerHeight();
    if (areaY < 0) {
      areaY = 0;
    }
    if (isNaN(areaY)) {
      $("#areaY").val(2 * y);
    } else {
      if (areaY % 2 != 0) {
        areaY--;
      }
      var top = areaY / template.radio;
      if (top + h > t_height) {
        top = t_height - h;
      }
      $("#" + id).css("top", top);
      $("#areaY").val(template.radio * top);
    }
  },
  changeW: function (type) {
    t_width = template.width;
    var id = $("#areaChange").val();
    var areaWidth = $("#areaWidth").val();
    var areaWidthP = $("#areaWidthPercent").val();
    var obj = $("#" + id);
    var x = obj.position().left;
    var w = obj.innerWidth();

    if (type == 2) {
      areaWidth = Math.round(template.radio * t_width * areaWidthP * 0.01);
    }
    if (isNaN(areaWidth)) {
      $("#areaWidth").val(2 * w);
    } else {
      /* var wmod = areaWidth % 4;
      console.log("wmod:", wmod);
      if (wmod != 0) {
        areaWidth -= wmod;
      }
      */
      var width = areaWidth / template.radio;
      if (width + x > t_width) {
        width = t_width - x;
      }
      
      if (id.startsWith("area_id")) {
        if (width < 16 / this.radio) {
          width = 16 / this.radio;
        }
      }
      else {
        if (width < 100 / this.radio) {
          width = 100 / this.radio;
        }
      }

      
      $("#" + id).css("width", width);
      $("#areaWidth").val(Math.round(template.radio * width)); //attr('value', 2 * width);
      $("#areaWidthPercent").val(Math.round((width / t_width) * 10000) / 100.0);
    }
  },
  changeH: function (type) {
    var t_height = template.height;
    var id = $("#areaChange").val();
    var areaHeight = $("#areaHeight").val();
    var areaHeightP = $("#areaHeightPercent").val();
    var obj = $("#" + id);
    var y = obj.position().top;
    var h = obj.innerHeight();

    if (type == 2) {
      areaHeight = Math.round(template.radio * t_height * areaHeightP * 0.01);
    }
    if (isNaN(areaHeight)) {
      $("#areaHeight").val(2 * h);
    } else {
      /*
      var hmod = areaHeight % 2;
      if (hmod != 0) {
        areaHeight -= hmod;
      }
      */
      var height = areaHeight / template.radio;
      if (height + y > t_height) {
        height = t_height - y;
      }

      //Min height is 100pixel
      if (id.startsWith("area_id")) {
        if (height < 16 / this.radio) {
          height = 16 / this.radio;
        }
      }
      else {
        if (height < 100 / this.radio) {
          height = 100 / this.radio;
        }
      }
      $("#" + id).css("height", height);
      $("#" + id + " dd").css("height", height - 20);
      $("#areaHeight").val(Math.round(template.radio * height));
      $("#areaHeightPercent").val(
        Math.round((height / t_height) * 10000) / 100.0
      );
    }
  },
};
