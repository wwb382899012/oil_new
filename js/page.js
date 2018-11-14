var page=new Object();

page.setSelectedMenus=function(ids)
{
    var arr=ids.split(",");
    for(var i=0;i<arr.length;i++)
    {
        if(i!=null && i>0)
        {
            $("#menu_item_"+arr[i]).addClass("active");
        }
    }
}

page.initTableHeight=function () {
    var d=$(".content-list > .panel");
    if(d.length>0)
    {
        var s=d.parent();
        if(s[0].offsetWidth < s[0].scrollWidth) {
            var h = $(window).height() - 40;
            if (d.height() > h)
                d.parent().height(h);
        }
        else
        {
            d.parent().height("auto");
        }
    }
}
page.initTableAndPagination = function(options) {
  var totalPages = options.totalPages;
  var currentPage = options.currentPage;
  var visiblePages = options.visiblePages;
  var callback = options.onPageChange;
  var columns=1;
  if(options.hasOwnProperty("columns") && options.columns!=="")
      columns=options.columns;
  var table=$('.dataTable').DataTable( {
    //scrollY:'auto',
    scrollCollapse: true,
    scrollX: true,
    info:false,
      "autoWidth": true,
    order: [],
    //ordering:false,
    fixedHeader:false,
    fixedColumns: {
      leftColumns: 0,
      rightColumns: columns
    },
    "language": {
      "emptyTable": "<div style='padding-left: 150px; font-weight: bold;'>暂时没有数据</div>"
    },
    searching:false,
    paging: false
  } );

  $(window).resize(function () {
      setTimeout(function () {
          //console.log("table adjust");
          table.columns.adjust().draw();
          //table.fixedColumns().relayout();
      },100);
  });
    page.initPagination("#pagination",options);
}
page.autoWidth = function (options, width) {
    // datatables 固定列统一宽度
    options = options || {};
    var rightColumns = options.rightColumns || 1;
    for (var i = 0 ; i < rightColumns ; i++) {
        var index = i + 1;
        $('.data-table').find('thead tr th:nth-last-child(' + index + ')').css('width', width || 'auto');
    }
}
page.autoscroll = function() {
    setTimeout(function () {
        var elem = $('[autoscroll]')
        var parent = elem.parent()
        if (parent.outerWidth() < elem.outerWidth()) {
            parent.css('overflow-x', 'scroll');
        }
    })
}
page.initDatatables = function(selector, options) {
    selector= selector || "table.data-table";
    var rightColumns=1;
    if(options && options.hasOwnProperty("columns") && options.columns!=="")
    {
        rightColumns=options.columns;
        delete options.columns
    }

    $(function(){

        var table=$(selector);
        if(table.length<=0)
            return;

        if(table.data("config"))
        {
            options=$.extend(options, eval("("+table.data("config")+")"));
        }
        /*if(!options && table.data("config"))
            options=eval("("+table.data("config")+")");*/

        var defaults=( {
            //scrollY:'auto',
            scrollCollapse: true,
            scrollX: true,
            info:false,
            "autoWidth": true,
            order: [],
            ordering:false,
            fixedHeader:false,
            fixedColumns: {
                leftColumns: 0,
                rightColumns: rightColumns
            },
            "language": {
                "emptyTable": "<div style='padding-left: 150px; font-weight: bold;'>暂时没有数据</div>"
            },
            searching:false,
            paging: false
        } );
        var o = $.extend(defaults, options);
		table=table.DataTable(o);

		$(window).resize(function () {
			setTimeout(function () {
				//console.log("table adjust");
				table.columns.adjust().draw();
				//table.fixedColumns().relayout();
			},100);
		});

		$(".data-table").find("a").addClass("text-link");
		$(".data-table").find("td:not(:last-child):not('.no-ellipsis')").addClass("ellipsis");
		page.dataTableHover();
    });
}

page.initPagination=function(selecter,options)
{
	selecter= selecter || "#pagination";
    $(selecter).jqPaginator({
        totalPages: options.totalPages || 0,
        visiblePages: options.visiblePages || 6,
        currentPage: options.currentPage || 1,
        prev: "<button type=\"button\" class=\"btn-prev\"><i class=\"el-icon el-icon-arrow-left\"></i></button>",
        next: "<button type=\"button\" class=\"btn-next\"><i class=\"el-icon el-icon-arrow-right\"></i></button>",
        page: "<li class=\"number\">{{page}}</li>",
        wrapper: "<ul class=\"el-pager\"></ul>",
        onPageChange: options.onPageChange
    });
}

page.dataTableHover = function() {
    $(document).on({
        mouseenter: function () {
            trIndex = $(this).index()+1;
            $("table.dataTable").each(function(index) {
                $(this).find("tr:eq("+trIndex+")").addClass("hover")
            });
        },
        mouseleave: function () {
            trIndex = $(this).index()+1;
            $("table.dataTable").each(function(index) {
                $(this).find("tr:eq("+trIndex+")").removeClass("hover")
            });
        }
    }, ".dataTables_wrapper tr");
}

page.checkFieldHasValue = function(selector) {
  var $scopeElem = $(selector + ' ' + '.is-hidden');
  var $inputGroup = $scopeElem.find('input');
  var $selectGroup = $scopeElem.find('select');
  var checkInput = $inputGroup.is(function() {
      if (this.type === 'hidden') {
          return false;
      }
      return !!$(this).val()
  })
  var checkSelect = $selectGroup.is(function() {
    return !!$(this).val()
  })
    return checkSelect || checkInput
}
page.setMainRoleNewUI=function (id)
{
    var formData = "id="+id;
    $.ajax({
        type: 'POST',
        url: '/site/setRole',
        data: formData,
        dataType: "json",
        success: function (json) {
            if (json.state == 0) {
				inc.vueMessage({
				    //onClose: function () {location.reload()},
                    message: "操作成功",
                    duration: 500
                });
                location.reload();
				// inc.showNotice("操作成功");
            }
            else {
                inc.vueAlert(json.data);
            }
        },
        error: function (data) {
            inc.vueAlert("操作失败：" + data.responseText);
        }
    });
}
page.setMainRole=function (id)
{
    var formData = "id="+id;
    $.ajax({
        type: 'POST',
        url: '/site/setRole',
        data: formData,
        dataType: "json",
        success: function (json) {
            if (json.state == 0) {
                inc.showNotice("操作成功");
                location.reload();
            }
            else {
                inc.alert(json.data);
            }
        },
        error: function (data) {
            inc.alert("操作失败：" + data.responseText);
        }
    });
}
//use in attachmentsEditMulti.php

function AttachModel(domId,baseId,controller)
{
	var self=this;
	$("#"+domId).find(".file-upload").each(function ()
	{
		var self=$(this);
		var btnText=self.prev();
		var fileNameTd=self.parent().parent().prev("td.file-name");
		var maxSize=parseInt(fileNameTd.data("maxsize"))*1024*1024;
		var permitFileType=fileNameTd.data("filetype");
		self.fileupload({
			url: '/'+controller+'/saveFile/',
			dataType: 'json',
			autoUpload: true,
			add: function (e, data) {
				if (!inc.checkFileType(data.files[0].name, permitFileType)) {
					layer.alert("只能上传指定类型的文件："+permitFileType, {icon: 5});
					return;
				}
				if(data.files[0].size>maxSize)
				{
					layer.alert("文件大小超出最大限制："+fileNameTd.data("maxsize")+"M", {icon: 5});
					return;
				}
				btnText.html("正在上传文件。。。");
				data.formData={id:baseId,type:fileNameTd.data("type")};
				data.submit();
			},
			done: function (e, data) {
				if (data.result.state == 0) {
					fileNameTd.prev().prev().html('<span class="glyphicon glyphicon-ok text-green"></span>');
					var fDom=$("<p id='i_"+data.result.data+"'  class='form-control-static file'></p>")
						.append($("<a target='_blank' class='btn btn-primary btn-xs'></a>")
							.attr("href","/"+controller+"/getFile/?id="+data.result.data+"&fileName="+data.result.extra.name)
							.html(data.result.extra.name))
						.append('&emsp;&emsp;')
						.append($('<a class="btn btn-danger btn-xs del-btn" onclick="page.delAttachment('+data.result.data+',\''+controller+'\')">删除</a>'));

					if(fileNameTd.data("multi")==1)
					{
						fileNameTd.find(".file-list").append(fDom);
						btnText.html("继续上传");
					}
					else
					{
						fileNameTd.find(".file-list").html(fDom);
						btnText.html("重新上传");
					}
				}
				else {
					layer.alert(data.result.data, {icon: 5});
					btnText.html("上传");
				}
			},
			fail:function(){
				layer.alert("上传出错，请稍后重试！", {icon: 5});
				btnText.html("上传");
			}
		});
	});

	self.del=function (id) {
		page.delAttachment(id,controller);
	}
}

function AttachModelNewUI(domId,baseId,controller)
{
	var self=this;
	$("#"+domId).find(".file-upload").each(function ()
	{
		var self=$(this);
		var btnText=self.prev();
		var fileNameTd=self.parent().parent().prev("td.file-name");
		var maxSize=parseInt(fileNameTd.data("maxsize"))*1024*1024;
		var permitFileType=fileNameTd.data("filetype");
		self.fileupload({
			url: '/'+controller+'/saveFile/',
			dataType: 'json',
			autoUpload: true,
			add: function (e, data) {
				if (!inc.checkFileType(data.files[0].name, permitFileType)) {
					inc.vueAlert("只能上传指定类型的文件："+permitFileType);
					return;
				}
				if(data.files[0].size>maxSize)
				{
					inc.vueAlert("文件大小超出最大限制："+fileNameTd.data("maxsize")+"M");
					return;
				}
				btnText.html("正在上传文件。。。");
				data.formData={id:baseId,type:fileNameTd.data("type")};
				data.submit();
			},
			done: function (e, data) {
				if (data.result.state == 0) {
					fileNameTd.prev().prev().html('<span class="glyphicon glyphicon-ok text-green"></span>');
					var fDom=$("<p id='i_"+data.result.data+"'  class='form-control-static file'></p>")
						.append($("<a target='_blank' class='btn btn-primary btn-xs'></a>")
							.attr("href","/"+controller+"/getFile/?id="+data.result.data+"&fileName="+data.result.extra.name)
							.html(data.result.extra.name))
						.append('&emsp;&emsp;')
						.append($('<a class="btn btn-danger btn-xs del-btn" onclick="page.delAttachment('+data.result.data+',\''+controller+'\')">删除</a>'));

					if(fileNameTd.data("multi")==1)
					{
						fileNameTd.find(".file-list").append(fDom);
						btnText.html("继续上传");
					}
					else
					{
						fileNameTd.find(".file-list").html(fDom);
						btnText.html("重新上传");
					}
				}
				else {
					inc.vueAlert(data.result.data);
					btnText.html("上传");
				}
			},
			fail:function(){
				inc.vueAlert("上传出错，请稍后重试！");
				btnText.html("上传");
			}
		});
	});

	self.del=function (id) {
		page.delAttachmentNewUI(id,controller);
	}
}

page.delAttachment=function (id,controller)
{
	layer.confirm("您确定删除当前已经上传的文件吗，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
		var formData = "id="+id;

		$.ajax({
			type: 'POST',
			url: '/'+controller+'/delFile',
			data: formData,
			dataType: "json",
			success: function (json) {
				if (json.state == 0) {
					inc.showNotice("操作成功");
					var fileNameTd=$("#i_"+id).parents("td.file-name");
					var btnText=fileNameTd.parent().find(".btn-text");
					$("#i_"+id).remove();
					if(fileNameTd.data("multi")!=1 || fileNameTd.find(".file-list").children("p").length<1)
					{
						fileNameTd.prev().prev().html('<span class="glyphicon glyphicon glyphicon-remove text-red"></span>');
						btnText.html("上传");
					}
				}
				else {
					layer.alert(json.data, {icon: 5});
				}
			},
			error: function (data) {
				layer.alert("操作失败：" + data.responseText, {icon: 5});
			}
		});

		layer.close(index);
	});

}

page.delAttachmentNewUI=function (id,controller)
{
	inc.vueConfirm({
		content: "您确定删除当前已经上传的文件吗，该操作不可逆？", onConfirm: function() {
			var formData = "id=" + id;

			$.ajax({
				type: 'POST',
				url: '/' + controller + '/delFile',
				data: formData,
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						inc.showNotice("操作成功");
						var fileNameTd = $("#i_" + id).parents("td.file-name");
						var btnText = fileNameTd.parent().find(".btn-text");
						$("#i_" + id).remove();
						if (fileNameTd.data("multi") != 1 || fileNameTd.find(".file-list").children("p").length < 1) {
							fileNameTd.prev().prev().html('<span class="glyphicon glyphicon glyphicon-remove text-red"></span>');
							btnText.html("上传");
						}
					}
					else {
						inc.vueAlert(json.data);
					}
				},
				error: function (data) {
					inc.vueAlert("操作失败：" + data.responseText);
				}
			});
		}
	});

}

page.showProject=function()
{
    var project_code=$("#top_search_key").val();
    if(project_code==null || project_code=="")
        return;
    $.ajax({
        type: 'GET',
        url: '/project/getIdByCode',
        data: {project_code: project_code},
        dataType: "json",
        success: function (json) {
            if (json.state == 0) {
                //console.log(json);
                inc.a("/project/detail/?t=1&id="+json.data);
            }
            else {
                inc.alert(json.data);
            }
        },
        error: function (data) {
            inc.alert("操作失败！" + data.responseText);
        }
    });
}
page.showProjectNewUI=function()
{
	var project_code=$("#top_search_key").val();
	if(project_code==null || project_code=="")
		return;
	$.ajax({
		type: 'GET',
		url: '/project/getIdByCode',
		data: {project_code: project_code},
		dataType: "json",
		success: function (json) {
			if (json.state == 0) {
			    console.log(json);
				inc.a("/project/detail/?t=1&id="+json.data);
			}
			else {
				inc.vueAlert(json.data);
			}
		},
		error: function (data) {
			inc.vueAlert("操作失败！" + data.responseText);
		}
	});
}
page.keyDownSearchNewUI = function (e) {
    var event = e || window.event;
    var code = event.keyCode || event.which || event.charCode;
    if (code == 13) {
        page.showProjectNewUI();
    }
}
page.keyDownSearch = function (e) {
	var event = e || window.event;
	var code = event.keyCode || event.which || event.charCode;
	if (code == 13) {
		page.showProject();
	}
}

/**
 * 使用dataTables的js插件
 * @param selector
 * @param options
 */
page.initDataTables=function(selector,options)
{
    if(!selector)
    {
        selector="table.data-table";
    }

    $(function(){

        var table=$(selector);
        if(table.length<=0)
            return;

        var ths=table.find("thead>tr>th");
        var trs=table.find("tbody>tr");
        if(trs.length===1)
        {
            var td=trs.find("td");
            if(td.length===1)
            {
                for(var i=1;i<ths.length;i++)
                    trs.append('<td class="hidden"></td>');
            }
        }

        if(!options && table.data("config"))
            options=eval("("+table.data("config")+")");

        var defaults={
            //scrollY:'auto',
            scrollCollapse: true,
            scrollX: true,
            info:false,
            order: [],
            //ordering:false,
            fixedHeader:true,
            /*fixedColumns: {
             leftColumns: 1
             },*/
            "language": {
                "emptyTable": "<div style='padding-left: 150px; font-weight: bold;'>暂时没有数据</div>"
            },
            searching:false,
            paging: false
        };

        var s=table.parent();
        if(table[0].offsetWidth+30*ths.length > s[0].offsetWidth)
        {
            defaults["scrollY"]=$(window).height() - 180;
        }
        var o = $.extend(defaults, options);
        table.DataTable(o);
    });
}

page.showMyTasks = function (is_first_load) {
	var is_active;
	if($('#menu_item_tasks').length > 0 && $('#menu_item_tasks').hasClass('menu-open')) {
		is_active = 1;
	} else {
		is_active = 0;
	}
	is_active = is_first_load || is_active;
	$.ajax({
		type: 'GET',
		url: '/site/getMyTasks?is_active='+is_active,
		dataType: "json",
		success: function (json) {
			if (json.state == 0) {
				$('#sidebar-menu-my-tasks').html(json.data);
			}
			setTimeout(page.showMyTasks,20000)
		}
	});
};

page.autoTaskErrors=0;
page.showMyTasksWithNewUI = function () {
	$.ajax({
		type: 'GET',
		url: '/site/getMyTasksWithNewUI',
		dataType: "json",
		success: function (json) {
			if (json.state == 0) {
				$('#sidebar-menu-my-tasks-newUI').html(json.data);
                setTimeout(page.showMyTasksWithNewUI,20000);
			}
			else
            {
                page.autoTaskErrors++;
                if(page.autoTaskErrors<=5)
                    setTimeout(page.showMyTasksWithNewUI,20000)
            }
		},
        error:function()
        {
            page.autoTaskErrors++;
            if(page.autoTaskErrors<=5)
                setTimeout(page.showMyTasksWithNewUI,20000)
        }
	});
}

/**
 * 通用导出
 * @param url
 */
page.export=function (url,exportAction) {
    var formData= $(".main-content form.search-form").serialize();
    location.href="/"+url+"/"+exportAction+"?"+formData;
};

/**
 * 通用重置
 */
page.doReset = function () {
	for (var i = $("form.search-form input").length - 1; i >= 0; i--) {
		$($("form.search-form input[type=text]")[i]).val('');
	}
	for (var i = $("form.search-form select").length - 1; i >= 0; i--) {
		$($("form.search-form select")[i]).val('');
        $("form.search-form select").eq(i).selectpicker('refresh')
	}
};

/**
 * 搜索切换
 */
page.toggleFields = function () {
	$('.condition-fields .is-hidden').toggle()
	var toggleText = $('#toggle-fields .toggle-text')
	if (toggleText.hasClass('in-fold')) {
		toggleText.removeClass('in-fold')
		toggleText.text('收起搜索')
	} else {
		toggleText.addClass('in-fold')
		toggleText.text('展开搜索')
	}
};

// 合同上传元素定位
page.positionInTable = function () {
    var page = this
    setTimeout(function () {
        var theadHeight = $('.table-in-table th').height();
        var targets = $('.in-table-wrapper')
        targets.each(function() {
            var offsetTop = this.offsetTop
            $(this).children('.flex-grid').css('top', offsetTop + theadHeight - 30)
        })
        if (!page.observer) {
            var positionInTable = page.positionInTable
            var observer = page.observer = new MutationObserver(function () {
                positionInTable.call(page)
            })
            targets.each(function () {
                observer.observe(this, { subtree: true, childList: true })
            })
        }
    })
}