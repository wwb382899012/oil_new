$(function(){
  var isWebkitBrowser = /webkit/i.test(navigator.userAgent);
  // var isWebkitBrowser = false;
  var notSupportDom = document.createElement('div');
  if(!isWebkitBrowser){
    $(notSupportDom)
      .addClass('page-not-support')
      .append('<div class="page-not-support-wrapper">' +
        '<p>当前浏览器不兼容，请使用谷歌浏览器或者其它浏览器的极速模式!</p>' +
        '<div>'+
        '<img src="/img/not-support/icon-chrome.png" />' +
        '<img src="/img/not-support/icon-360.png" />' +
        '<img src="/img/not-support/icon-safari.png" />' +
        '</div>'+
        '</div>');
    $('body').append(notSupportDom);
  }
});


String.prototype.replaceAll = function (s1, s2) {
	return this.replace(new RegExp(s1, "gm"), s2);
}
String.prototype.trim=function(){
	return this.replace(/(^\s*)|(\s*$)/g, "");
}
String.prototype.ltrim=function(){
	return this.replace(/(^\s*)/g,"");
}
String.prototype.rtrim=function(){
	return this.replace(/(\s*$)/g,"");
}
/*Array.prototype.clone=function()
{
    return JSON.parse(JSON.stringify(this));
}*/

/**
 * 保留小数
 * @param n
 * @returns {number}
 */
Number.prototype.round=function (n) {
    if(n==null)
        n=0;
    var t=Math.pow(10,n);
    return Math.round(this*t)/t;
}
/*Object.prototype.round=function (n) {
    if(n==null)
        n=0;
    var t=Math.pow(10,n);
    return Math.round(parseFloat(this)*t)/t;
}*/

/**
 对Date的扩展，将 Date 转化为指定格式的String * 月(M)、日(d)、12小时(h)、24小时(H)、分(m)、秒(s)、周(E)、季度(q)
 可以用 1-2 个占位符 * 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字) * eg: * (new
 Date()).format("yyyy-MM-dd hh:mm:ss.S")==> 2006-07-02 08:09:04.423
 * (new Date()).format("yyyy-MM-dd E HH:mm:ss") ==> 2009-03-10 二 20:09:04
 * (new Date()).format("yyyy-MM-dd EE hh:mm:ss") ==> 2009-03-10 周二 08:09:04
 * (new Date()).format("yyyy-MM-dd EEE hh:mm:ss") ==> 2009-03-10 星期二 08:09:04
 * (new Date()).format("yyyy-M-d h:m:s.S") ==> 2006-7-2 8:9:4.18
 */
Date.prototype.format = function (fmt)
{
    if(fmt==undefined || fmt==null)
        fmt="yyyy-MM-dd";
    var o = {
        "M+": this.getMonth() + 1, //月份
        "d+": this.getDate(), //日
        "h+": this.getHours() % 12 == 0 ? 12 : this.getHours() % 12, //小时
        "H+": this.getHours(), //小时
        "m+": this.getMinutes(), //分
        "s+": this.getSeconds(), //秒
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
        "S": this.getMilliseconds() //毫秒
    };
    var week = {
        "0": "日",//"/u65e5",
        "1": "一",//"/u4e00",
        "2": "二",//"/u4e8c",
        "3": "三",//"/u4e09",
        "4": "四",//"/u56db",
        "5": "五",//"/u4e94",
        "6": "六",//"/u516d"
    };
    if (/(y+)/.test(fmt))
    {
        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    }
    if (/(E+)/.test(fmt))
    {
        //fmt = fmt.replace(RegExp.$1, ((RegExp.$1.length > 1) ? (RegExp.$1.length > 2 ? "/u661f/u671f" : "/u5468") : "") + week[this.getDay() + ""]);
        fmt = fmt.replace(RegExp.$1, ((RegExp.$1.length > 1) ? (RegExp.$1.length > 2 ? "星期" : "周") : "") + week[this.getDay() + ""]);
    }
    for (var k in o)
    {
        if (new RegExp("(" + k + ")").test(fmt))
        {
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        }
    }
    return fmt;
}
var inc=new Object();

//去除对象或对象数组中值为function的属性
inc.toJSON=function(obj,replacer, space){
	 var cache = [];
     var json = JSON.stringify(obj, function(key, value) {
         if (typeof value === 'object' && value !== null) {
             if (cache.indexOf(value) !== -1) {
                 // circular reference found, discard key
                 return;
             }
             // store value in our collection
             cache.push(value);
         }
         return replacer ? replacer(key, value) : value;
     }, space);
     cache = null;
     return json;
}

//格式化金额显示
inc.formatMoney=function(s, n) {
	n = n > 0 && n <= 20 ? n : 2;
	s = parseFloat((s + "").replace(/[^\d\.-]/g, "")).toFixed(n) + "";
	var l = s.split(".")[0].split("").reverse(), r = s.split(".")[1];
	t = "";
	for (i = 0; i < l.length; i++) {
		t += l[i] + ((i + 1) % 3 == 0 && (i + 1) != l.length ? "," : "");
	}
	return t.split("").reverse().join("") + "." + r;
}

//显示自动隐藏的提示，默认3秒后隐藏，第二个参数可以选填该值
inc.showNotice =function(msg) {
	$(function () {
		if ($("div#showSuccessInfo").length == 0) {
			var htmlobj = "<div id=\"showSuccessInfo\"  style=\"z-index:9999999;background-color: #BC3521;padding: 5px;position: fixed!important;position: absolute;color: #FFFFFF;\"></div>";
			$(document.body).append(htmlobj);
		}

		$("div#showSuccessInfo").html(msg).show().css("top", document.documentElement.scrollTop + 200).css("left", $(window).width() / 2 - 30);
		var t = 3000;
		if (arguments[1])
			t = parseInt(arguments[1]) * 1000;
		setTimeout(inc.closeNotice, t);

	});
}

inc.closeNotice=function() {
	$("div#showSuccessInfo").fadeOut(2000);
}

inc.checkFileType=function(fileName, permitType)
{
	var l = fileName.length;
	var t = fileName.substring(fileName.lastIndexOf('.') + 1, l);
	if (permitType.indexOf("|" + t + "|") < 0) {
		return false;
	}
	else
		return true;
}

inc.closeMainAbsoluteContainer=function(){
	var obj=$(".main-absolute-container");
	obj.parent().removeClass("open");
	obj.hide();
}

inc.bindCKEditorPasteImage=function(editor){
	editor.on("instanceReady",function(){
		this.document.on("paste", function (event) {
			if(typeof FileReader == 'undefined'){
				return;
			}
			var items = (event.data.$.clipboardData  || event.data.$.originalEvent.clipboardData).items;
			var blob;
			for (var i = 0; i < items.length; i++) {
				if (items[i].type.indexOf("image") === 0) {
					blob = items[i].getAsFile();
					break;
				}
			}
			if (blob !== null &&  blob != undefined) {
				var a = new FileReader();
				var dd;
				a.onload = function () {
					dd = this.result.substr(this.result.indexOf(",") + 1);
					$.post("/site/pasteUpload/",{upload:dd}, function (data) {
						var json=eval("("+data+")");
						if (json.state == 0) {
							editor.insertHtml("<img src='"+json.data+"' />");
						}
						else {
							alert(json.data);
						}
					});
				};
				a.readAsDataURL(blob);
			}
		});
	});
}

//打出弹出窗口
inc.openWindow=function (obj) {
	var o = $.extend({
		link: "#",
		height: 500,
		width: 960
	}, obj || {});
	window.open(o.link, "newWindow"+Math.random(), "height=" + o.height + ",width=" + o.width + ",toolbar=no,menubar=no,scrollbars=yes, resizable=no,location=no, status=no");
}

/**
 * 模拟a的超链接新窗口打开页面
 * @param url
 */
inc.a= function (url) {
	var obj=document.createElement("a");
	obj.href=url;
	obj.target="_blank";
	$("body").append(obj);
	obj.click();
    $(obj).remove();
}

/**
 * 主查询的验证
 *
 * @param e	当前按钮事件对象（this）
 */
inc.mainSearch=function(e){
	var f=$(e).parent("form");
	var p=f.find("#phone");
	if(p.val()=="")
	{
		p.addClass("error");
		alert("请输入手机号或用户Id！");
		return;
	}
	var reg = new RegExp("^[0-9]+$");
	if(!reg.test(p.val().trim()))
	{
		p.addClass("error");
		alert("只能输入手机号或用户Id！");
		return;
	}
	f.submit();
}


inc.setCookie = function (name, value, expireDays)
{
    var exDate = new Date()
    if(expireDays==undefined)
        expireDays=30;
    exDate.setDate(exDate.getDate() + expireDays)
    document.cookie = name + "=" + escape(value) +
        ((expireDays == null) ? "" : ";expires=" + exDate.toGMTString())
}

inc.getCookie = function (name)
{
    if (document.cookie.length > 0)
    {
        c_start = document.cookie.indexOf(name + "=")
        if (c_start != -1)
        {
            c_start = c_start + name.length + 1
            c_end = document.cookie.indexOf(";", c_start)
            if (c_end == -1) c_end = document.cookie.length
            return unescape(document.cookie.substring(c_start, c_end))
        }
    }
    return ""
}

/*************************************************************
 * 页面中弹出框相关的js
 */
function alertModel(text,href){

    inc.setModal({
        icon:"info",
        title:text,
        ok:false,
        closeBtnText:"关闭",
        onClose:function () {
            if($.isFunction(href))
                href();
            else
            {
                if(href!=null)
                    location.href=href;
            }
        }
    });
}
function alertConfirmModel(text,func){

    inc.confirm({
        title:text,
        onOK:function(e){
            func(e);
        }
    });
}
function showFlowItem(id,flowId){
	openWindow({
		link:"/flow/flowItems/?t=1&search[finst.inst_id]="+id+"&search[finst.flow_id]="+flowId
	});
}
/*--------------------------------------------------------*/



inc.showChart=function(option)
{
    var defaults = {
        container:"#chartContainer",
        chartType:"spline",
        title:"",
        subTitle:"",
        yTitle:"",
        x:[],
        dataFormatter:function() {
            return this.y;
        },
        yAxisFormatter:function() {
            return this.value;
        },
        tooltipFormatter:function() {
            var s="<b>"+this.x + "</b>   " +this.series.name+": "+ this.y ;
            return s;
        },
        series:[]
    };
    var o = $.extend(defaults, option);
    $(o.container).highcharts({
        chart: {
            type: o.chartType
        },
        title: {
            text: o.title
        },
        subtitle: {
            text: o.subTitle
        },
        xAxis: {
            categories: o.x
        },
        yAxis: {
            title: {
                text: o.yTitle
            },
            labels: {
                formatter: o.yAxisFormatter
            }
        },
        tooltip: {
            crosshairs: true,
            //shared: true, //如果该选项打开，下面的formatter函数中是this.points对象，即多个点，而为false时，则为this.point,即单个点
            formatter:o.tooltipFormatter
        },
        plotOptions: {
            series: {
                dataLabels: {
                    enabled: true,
                    formatter:o.dataFormatter
                },
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            if(typeof (this.url) != undefined && this.url)
                            {
                                inc.a(this.url);
                            }
                        }
                    }
                }
            }
        },
        series: o.series
        /*series: [
         {
         name: '事件数',
         data: [{
                y:10,url:"#"  //如果数据源中包含url，点击点时会自动触发新打开页面跳转
            }]
         }
         ]*/
    });

}
inc.dateAdd= function (date,strInterval, Number) {  //参数分别为日期对象，增加的类型，增加的数量
    var dtTmp = date;
    switch (strInterval) {
        case 'second':
        case 's' :
            return new Date(Date.parse(dtTmp) + (1000 * Number));
        case 'minute':
        case 'n' :
            return new Date(Date.parse(dtTmp) + (60000 * Number));
        case 'hour':
        case 'h' :
            return new Date(Date.parse(dtTmp) + (3600000 * Number));
        case 'day':
        case 'd' :
            return new Date(Date.parse(dtTmp) + (86400000 * Number));
        case 'week':
        case 'w' :
            return new Date(Date.parse(dtTmp) + ((86400000 * 7) * Number));
        case 'month':
        case 'm' :
            var nextM=new Date(dtTmp.getFullYear(), (dtTmp.getMonth()) + Number+1, 0);
            var max=nextM.getDate(),d=dtTmp.getDate();
            d=d>max? max:d;
            return new Date(dtTmp.getFullYear(), (dtTmp.getMonth()) + Number, d, dtTmp.getHours(), dtTmp.getMinutes(), dtTmp.getSeconds());
        case 'year':
        case 'y' :
            return new Date((dtTmp.getFullYear() + Number), dtTmp.getMonth(), dtTmp.getDate(), dtTmp.getHours(), dtTmp.getMinutes(), dtTmp.getSeconds());
    }
}
/**
 * 清除为null的属性
 * @param params
 * @returns {*}
 */
inc.clearNullProperty=function(params)
{
    for ( var p in params ){
        if(params[p]==null || params[p]==undefined)
            delete  params[p];
    }
    return params;
}

inc.loadingIco="<i class=\"fa fa-spinner fa-pulse fa-fw\" ></i>";

/**
 *  通过ko的view对象获取提交的数据
 * @param view
 * @param filterParams
 * @returns {*}
 */
inc.getPostData=function(view,filterParams)
{
    var data=ko.toJS(view);
    return inc.clearKOJSData(data,filterParams);
}

/**
 * 清除ko.toJS后的对象，去除function和指定属性值的
 * @param obj
 * @param params
 * @returns {*}
 */
inc.clearKOJSData=function(obj,params)
{
    var defaults = ["errors","save","saveBtnText","buttonText","actionState","tempSaveBtnText"];
    if(params)
        defaults= defaults.concat(params);
    for (var i=0;i<defaults.length;i++)
    {
        delete obj[defaults[i]];
    }
    for ( var p in obj )
    {
        if(typeof obj[p] === "function")
            delete  obj[p];
    }

    return obj;
}
/**
 * js对象转数组
 * @param obj
 */
inc.objectToArray=function (obj) {
    return Object.keys(obj).map(function(key){ return obj[key] });
}
/**
 * map文件对象js转数组
 * @param obj {"1":"启用","2":"不启用"}
 * @returns {Array} [{"id":1,"name":"启用"},{"id":2,"name":"不启用"}]
 */
inc.mapObjectToArray=function (obj) {
    return Object.keys(obj).map(function(key){ return {"id":key,"name":obj[key]} });
}

/**
 * days360计算日期相差天数
 * @param endDate 2017-11-11
 * @param startDate 2017-10-11
 */
inc.diffDays = function (start, end) {
	var diff = {years: 0, months: 0, days: 0};
	var diffDays = 0;
	var sDate = new Date(start);
	var eDate = new Date(end);
	var timeDiff = eDate - sDate;

	if (timeDiff > 0) {
		var years = eDate.getFullYear() - sDate.getFullYear();
		var months = eDate.getMonth() - sDate.getMonth();
		var days = eDate.getDate() - sDate.getDate();

		if (months < 0) {
			years--;
			months += 12;
		}

		if (days < 0) {
			months = Math.max(0, diff.months - 1);
			days += 30;
		}

		diffDays = (years * 12 + months) * 30 + days;
	}

	return diffDays;
};

inc.moneyToChinese=function (money){
    var cnNums = new Array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖"); //汉字的数字
    var cnIntRadice = new Array("","拾","佰","仟"); //基本单位
    var cnIntUnits = new Array("","万","亿","兆"); //对应整数部分扩展单位
    var cnDecUnits = new Array("角","分","毫","厘"); //对应小数部分单位
    //var cnInteger = "整"; //整数金额时后面跟的字符
    var cnIntLast = "元"; //整型完以后的单位
    var maxNum = 999999999999999.9999; //最大处理的数字

    var IntegerNum; //金额整数部分
    var DecimalNum; //金额小数部分
    var ChineseStr=""; //输出的中文金额字符串
    var parts; //分离金额后用的数组，预定义
    if( money == "" ){
        return "";
    }
    money = parseFloat(money);
    if( money >= maxNum ){
        alert('超出最大处理数字');
        return "";
    }
    if( money == 0 ){
        //ChineseStr = cnNums[0]+cnIntLast+cnInteger;
        ChineseStr = cnNums[0]+cnIntLast
        //document.getElementById("show").value=ChineseStr;
        return ChineseStr;
    }
    money = money.toString(); //转换为字符串
    if( money.indexOf(".") == -1 ){
        IntegerNum = money;
        DecimalNum = '';
    }else{
        parts = money.split(".");
        IntegerNum = parts[0];
        DecimalNum = parts[1].substr(0,4);
    }
    if( parseInt(IntegerNum,10) > 0 ){//获取整型部分转换
        zeroCount = 0;
        IntLen = IntegerNum.length;
        for( i=0;i<IntLen;i++ ){
            n = IntegerNum.substr(i,1);
            p = IntLen - i - 1;
            q = p / 4;
            m = p % 4;
            if( n == "0" ){
                zeroCount++;
            }else{
                if( zeroCount > 0 ){
                    ChineseStr += cnNums[0];
                }
                zeroCount = 0; //归零
                ChineseStr += cnNums[parseInt(n)]+cnIntRadice[m];
            }
            if( m==0 && zeroCount<4 ){
                ChineseStr += cnIntUnits[q];
            }
        }
        ChineseStr += cnIntLast;
        //整型部分处理完毕
    }
    if( DecimalNum!= '' ){//小数部分
        decLen = DecimalNum.length;
        for( i=0; i<decLen; i++ ){
            n = DecimalNum.substr(i,1);
            if( n != '0' ){
                ChineseStr += cnNums[Number(n)]+cnDecUnits[i];
            }
        }
    }
    if( ChineseStr == '' ){
        //ChineseStr += cnNums[0]+cnIntLast+cnInteger;
        ChineseStr += cnNums[0]+cnIntLast;
    }/* else if( DecimalNum == '' ){
                ChineseStr += cnInteger;
                ChineseStr += cnInteger;
            } */
    return ChineseStr;
}

inc.alert=function(msg)
{
    var defaults ={
        id:"",
        icon:"info",
        title:msg,
        content:"",
        ok:false,
        okBtnText:"OK",
        closeBtnText:"关闭",
        close:true,
        onClose:null,
        onOK:null
    };
    inc.setModal(defaults);
}
inc.confirm=function(option)
{
    var defaults ={
        id:"",
        icon:"question",
        title:"",
        content:"",
        ok:true,
        okBtnText:"确认",
        closeBtnText:"取消",
        close:true,
        onClose:null,
        onOK:null
    };
    var o = $.extend(defaults, option);
    inc.setModal(o);
}
inc.warning=function(msg)
{
    var defaults ={
        icon:"warning",
        title:msg,
        ok:false,
        closeBtnText:"关闭",
        close:true,
        onClose:null,
        onOK:null
    };
    inc.setModal(defaults);
}

inc.error=function(msg)
{
    var defaults ={
        icon:"error",
        title:msg,
        ok:false,
        closeBtnText:"关闭",
        close:true,
        onClose:null,
        onOK:null
    };
    inc.setModal(defaults);
}
inc.setModal=function(option)
{
    var defaults ={
        id:"",
        icon:"",
        title:"",
        content:"",
        ok:true,
        okBtnText:"OK",
        closeBtnText:"关闭",
        close:true,
        onClose:null,
        onOK:null
    };
    var o = $.extend(defaults, option);

    var template="<div class=\"modal fade\" tabindex=\"-1\" role=\"dialog\" >\n" +
        "    <div class=\"modal-dialog\">\n" +
        "        <div class=\"modal-content\" >\n" +
        "            <div class=\"modal-header\">\n" +
        "                <button type=\"button\" class=\"close\" name=\"closeModel\" data-dismiss=\"modal\"><span aria-hidden=\"true\">&times;</span><span class=\"sr-only\">Close</span></button>\n" +
        "                <h4 class=\"modal-title\">Modal title</h4>" +
        "            </div>" +
        "            <div class=\"modal-body\">\n" +
        "            </div>"+
        "            <div class=\"modal-footer\">\n" +
        "            </div>\n" +
        "        </div>\n" +
        "    </div>\n" +
        "</div>";
    var modal=$(template);
    if(o.id)
    {
        modal.attr("id",o.id);
    }

    var icons={
        "question":"<span class=\"glyphicon glyphicon-question-sign text-danger\" aria-hidden=\"true\"></span>",
        "ok":"<span class=\"glyphicon glyphicon-ok-sign text-green\" aria-hidden=\"true\"></span>",
        "error":"<span class=\"glyphicon glyphicon-remove-sign text-danger\" aria-hidden=\"true\"></span>",
        "info":"<span class=\"glyphicon glyphicon-info-sign text-info\" aria-hidden=\"true\"></span>",
        "warning":"<span class=\"glyphicon glyphicon-info-sign text-warning\" aria-hidden=\"true\"></span>"
    };
    var buttons={
        "ok":"<button type=\"button\" id=\"modalOkButton\" name=\"modalOkButton\" class=\"btn btn-primary\" data-dismiss=\"modal\">OK</button>",
        "close":"<button type=\"button\" id=\"modalCloseButton\" name=\"closeModel\" class=\"btn btn-default\" data-dismiss=\"modal\">关闭</button>"
    };

    var titleDom=modal.find(".modal-title");
    if(o.title)
    {
        titleDom.html("&nbsp;"+o.title);
    }

    if(o.icon && icons[o.icon])
        titleDom.prepend($(icons[o.icon]));
    if(titleDom.html()!="")
        titleDom.show();
    else
        titleDom.hide();

    var bodyDom=modal.find(".modal-body");
    if(o.content)
    {
        bodyDom.html(o.content);
        bodyDom.show();
    }
    else
        bodyDom.hide();

    var footDom=modal.find(".modal-footer");
    if(o.close)
    {
        var closeBtn=$(buttons["close"]).text(o.closeBtnText);
        /*if($.isFunction(o.onClose))
        {
            closeBtn.on("click",function () {
                o.onClose();
            });
        }*/
        footDom.append(closeBtn);
    }

    if(o.ok)
    {
        var okBtn=$(buttons["ok"]).text(o.okBtnText);
        if($.isFunction(o.onOK))
        {
            okBtn.on("click",function (e) {
                o.onOK(e);
            });
        }

        footDom.append(okBtn);
    }

    modal.on('hidden.bs.modal', function (e) {
        if($.isFunction(o.onClose))
        {
            o.onClose(e);
        }
        modal.remove();
    });

    $(document.body).append(modal);
    modal.modal({
        backdrop:true,
        keyboard:false,
        show:true
    });
    return modal;
}
/**
 * 浮点数格式化
 * @param number
 * @param n 位数
 */
inc.formatNumberToFixed = function (number, n) {
	n = n || 2;
	return parseFloat(number).toFixed(n);
};

inc.getNowDate=function(){
    return new Date().format();
}
/**
 * 获取指定日期的字符串形式，参数为前后的天数，默认为0
 * @returns {string}
 */
inc.getDateString=function()
{
    var n=0;
    if(arguments[0])
    {
        n=arguments[0];
    }
    var d = new Date();
    d.setTime((new Date()).getTime()+n*24*60*60*1000)
    return d.toDateString();
}

/**
 * 检查是否是金额
 */
inc.isMoney = function (money) {
	if (parseFloat(money).toString() == 'NaN' || parseFloat(money) < 0) {
		return false;
	}
	return true;
}

/**
 * 检查变量是否为空
 */
inc.isEmpty = function (val) {
	return val == '' || val == null || val == 0 || val == undefined;
};

/**
 * 浮点数四舍五入
 * @param val
 * @param scale 小数位数
 */
inc.toDecimal = function (val, scale) {
	scale = scale || 2;
	if (isNaN(parseFloat(val))) {
		return 0;
	}

	return Math.round(val*Math.pow(10, scale))/Math.pow(10, scale);
}

/**
 * Get a prestored setting
 *
 * @param String name Name of of the setting
 * @returns String The value of the setting | null
 */
 inc.get=function(name) {
    if (typeof (Storage) !== 'undefined') {
        return localStorage.getItem(name)
    } else {
        window.alert('Please use a modern browser to properly view this template!')
    }
}

/**
 * Store a new settings in the browser
 *
 * @param String name Name of the setting
 * @param String val Value of the setting
 * @returns void
 */
 inc.store=function(name, val) {
    if (typeof (Storage) !== 'undefined') {
        localStorage.setItem(name, val)
    } else {
        window.alert('Please use a modern browser to properly view this template!')
    }
}

/**
 * 阻止enter时浏览器默认行为发生
 * @param Object event
 */
inc.stopEnterDefault = function (event) {
	event = window.event || event;
	if(event.keyCode == 13) {
		if(event && event.preventDefault) {//非IE浏览器
			event.preventDefault();
		} else {//IE
			window.event.returnValue = false;
		}
		return false;
	}
}
/**
 * 数组深度复制
 * @param arr
 * @returns {any}
 */
inc.arrayClone=function(arr)
{
    return JSON.parse(JSON.stringify(arr));
}

/**
 * elementui消息提示
 * @param options
 */
inc.vueMessage = function (options) {
	if(typeof(options) === 'string') {
		options = {message: options};
	}
	var defaults = {
		message:  '',
		type: 'success',
		duration: 3000,
		showClose: false,
		onClose: null
	};
	var o = $.extend(defaults, options);
	vue.$message({
        dangerouslyUseHTMLString: true,
		type: o.type,
		message: o.message,
		duration: o.duration,
		showClose: o.showClose,
		onClose: () => {
			if($.isFunction(o.onClose)) {
				o.onClose();
			}
		}
	});
};

/**
 * elementui弹框提示
 * @param options
 */
inc.vueAlert = function(options) {
	if(typeof(options) === 'string') {
		options = {content: options};
	}
	var defaults = {
		title:  '提示',
		content: '',
		confirmButtonText: '确定',
		onConfirm: null,
		onClose: null,
        type: 'warning'
	};
	var o = $.extend(defaults, options);
	// console.log(o)
	vue.$alert(o.content, o.title, {
        dangerouslyUseHTMLString: true,
		confirmButtonText: o.confirmButtonText,
        type: o.type,
		callback: action => {
			if(action === 'confirm') {
				if($.isFunction(o.onConfirm)) {
					o.onConfirm();
				}
			} else {
				if($.isFunction(o.onClose)) {
					o.onClose();
				}
			}
		}
	});
};

/**
 * elementui确认消息弹框
 * @param options
 */
inc.vueConfirm = function (options) {
	var defaults = {
		title: '提示',
		content: '',
		confirmButtonText: '确定',
		cancelButtonText: '取消',
		type: 'warning',
		onConfirm: null,
		onCancel: null
	};
	var o = $.extend(defaults, options);
	vue.$confirm(o.content, o.title, {
		confirmButtonText: o.confirmButtonText,
		cancelButtonText: o.cancelButtonText,
		type: o.type,
        dangerouslyUseHTMLString: true,
		callback: (action, instance) => {
			if (action === 'confirm') {
				if($.isFunction(o.onConfirm)) {
					o.onConfirm();
				}
			} else {
				if($.isFunction(o.onCancel)) {
					o.onCancel();
				}
			}
		}
	});
};