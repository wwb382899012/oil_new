<style type="text/css">
    .ztree li span.button.switch.level0 {visibility:hidden; width:1px;}
    .ztree li ul.level0 {padding:0; background:none;}
</style>
<div class="box box-success" id="main-container">
    <div class="box-header with-border">
        <h3 class="box-title"><span data-bind="visible:id()==0">添加</span><span data-bind="visible:id()!=0">修改</span>系统模块</h3>
    </div>
    <div class="box-body">
        <form id="mainForm" role="form" class="form-horizontal">
            <input type="hidden"  id="moduleId" name="obj[id]" data-bind="value:id">
            <div class="form-group">
                <label class="col-sm-2 control-label">模块名称</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="模块名称" id="name" name="obj[name]" data-bind="value:name">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">图标</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="图标" id="icon" name="obj[icon]" data-bind="value:icon">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">所属系统</label>
                <div class="col-sm-10">
                    <select class="form-control" id="systemId" name="obj[system_id]" data-bind="value:system_id">
                        <?php foreach($this->map["system_id"] as $k=>$v)
                        {
                            echo "<option value='".$k."'>".$v."</option>";
                        }?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">上级模块</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="上级模块" id="parentName" name="obj[parentName]" data-bind="value:parentName,attr:{readonly:true}" onclick="selectTreeObj.showTree(this)">
                    <input type="hidden"  id="parent_id" name="obj[parent_id]" data-bind="value:parent_id">
                    <div id="treeSelecctContent" class="treeSelecctContent" style="display: none; position: absolute; width: 500px; z-index: 999999;">
                        <ul id="treeSelecctTree" class="ztree ztreeSelect" style="margin-top: 0; height: 400px;"></ul>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">权限码</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="权限码" id="code" name="obj[code]" data-bind="value:code">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">模块操作</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="模块操作" id="actions" name="obj[actions]" data-bind="value:actions">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">页面地址</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="页面地址" id="page_url" name="obj[page_url]" data-bind="value:page_url">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">排序码</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" placeholder="排序码" id="order_index" name="obj[order_index]" data-bind="value:order_index">
                </div>
                <label class="col-sm-2 control-label">是否外部链接</label>
                <div class="col-sm-4">
                    <select class="form-control" id="is_external" name="obj[is_external]" data-bind="value:is_external">
                        <?php foreach($this->map["module_is_external"] as $k=>$v)
                        {
                            echo "<option value='".$k."'>".$v."</option>";
                        }?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">是否启用</label>
                <div class="col-sm-4">
                    <select class="form-control" id="status" name="obj[status]" data-bind="value:status">
                        <?php foreach($this->map["module_status"] as $k=>$v)
                        {
                            echo "<option value='".$k."'>".$v."</option>";
                        }?>
                    </select>
                </div>

                <label class="col-sm-2 control-label">是否公开</label>
                <div class="col-sm-4">
                    <select class="form-control" id="is_public" name="obj[is_public]" data-bind="value:is_public">
                        <?php foreach($this->map["module_is_public"] as $k=>$v)
                        {
                            echo "<option value='".$k."'>".$v."</option>";
                        }?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">是否菜单</label>
                <div class="col-sm-2">
                    <select class="form-control" id="is_menu" name="obj[is_menu]" data-bind="value:is_menu">
                        <?php foreach($this->map["module_is_menu"] as $k=>$v)
                        {
                            echo "<option value='".$k."'>".$v."</option>";
                        }?>
                    </select>
                </div>
                <label class="col-sm-2 control-label">备注</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" placeholder="备注" id="remark" name="obj[remark]" data-bind="value:remark">
                </div>

            </div>
        </form>
    </div>
    <div class="box-footer text-right">
        <button type="button" class="btn btn-primary" data-bind="click:save">保存</button>
        <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
    </div>
</div>
<script type="text/javascript" src="/zTree/js/jquery.ztree.all-3.5.min.js"></script>
<script type="text/javascript" src="/zTree/js/jquery.ztree.exhide-3.5.min.js"></script>
<link href="/zTree/css/zTreeStyle.css" rel="stylesheet" type="text/css" />

<script>
    var view;
    var selectTreeObj;

    $(function(){

        view=new ModuleViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view,$("#main-container")[0]);

        selectTreeObj=new SelectTree();
        selectTreeObj.init();

    });
    function ModuleViewModel(option) {
        var defaults = {
            id: 0,
            system_id: 0,
            name: "",
            code:"",
            icon:"",
            actions:"列表|index,详情|detail,添加|add,修改|edit,保存|save,删除|del",
            page_url: "",
            parent_id:0,
            status:1,
            order_index:0,
            is_public: 0,
            is_external:0,
            is_menu:1,
            remark:""
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.actionState=0;
        self.id = ko.observable(o.id);
        self.name = ko.observable(o.name).extend({required: true, maxLength: 200});
        self.icon = ko.observable(o.icon).extend({maxLength: 250});
        self.actions = ko.observable(o.actions);
        self.page_url = ko.observable(o.page_url).extend({maxLength: 250});
        self.is_public = ko.observable(o.is_public);
        self.is_external = ko.observable(o.is_external);
        self.is_menu = ko.observable(o.is_menu);
        self.status = ko.observable(o.status);
        
        self.order_index = ko.observable(o.order_index).extend({number:true});
        self.remark = ko.observable(o.remark).extend({maxLength: 500});
        self.system_id = ko.observable(o.system_id);

        self.parent_id = ko.observable(o.parent_id);
        self.parentName = ko.observable();
        self.code = ko.observable(o.code).extend({required: true, maxLength: 200});
        /*self.code = ko.observable(o.code).extend({maxLength: 200,custom: {
            params: function (v) {
                if (self.page_url()=="" ||
                    (self.page_url()!="" && self.is_public()==1) ||
                    (self.page_url()!="" && self.is_public()==0 && v!="")
                ) {
                    return true;
                }
                else
                    return false;
            },
            message: "请填写权限码！"
        }
        });*/

        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.save = function () {
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }
            var formData = $("#mainForm").serialize();
            if(self.actionState==1)
                return;
            self.actionState=1;
            $.ajax({
                type: 'POST',
                url: '/module/save',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        self.id(json.data);
                        inc.showNotice("保存成功");
                        self.back();
                    }
                    else {
                        alertModel(json.data);
                    }
                    self.actionState=0;
                },
                error: function (data) {
                    self.actionState=0;
                    alertModel("保存失败！" + data.responseText);
                }
            });
        }

        self.back = function () {
            location.href = "/module/";
        }

    }

    /************************************************************************************
     edit select tree code start
     *************************************************************************************/

    function SelectTree(){
        var self=this;
        self.tree;
        self.treeContainer=$("#treeSelecctContent");

        self.zNodes =[
            { id:0, pId:0, name:"根模块", open:true},
        ];

        //选中时触发的函数
        self.onSelectTreeClick= function (e, treeId, treeNode) {
            view.parentName(treeNode.name);
            view.parent_id(treeNode.id);
        }

        //设置选定值
        self.setSelectedNode=function () {
            var node = self.tree.getNodeByParam("id", view.parent_id(), null);
            //console.log(node);
            if(node!=null)
            {
                self.tree.selectNode(node);
                view.parentName(node.name);
            }
            else
            {
                view.parentName("根模块");
            }
        }

        self.showTree=function(e) {
            self.treeContainer.slideDown("fast");
            $("body").bind("mousedown", self.onMouseDown);
        }

        self.hideTree=function () {
            self.treeContainer.fadeOut("fast");
            $("body").unbind("mousedown", self.onMouseDown);
        }

        self.onMouseDown=function (event) {
            if (!(event.target.id == "selectedText" || event.target.id == "treeSelecctContent" || $(event.target).parents("#treeSelecctContent").length > 0)) {
                self.hideTree();
            }
        }

        self.setting = {
            view: {
                selectedMulti: false
            },
            data: {
                key: {
                    name: "name"
                },
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id"
                }
            },
            callback: {
                onClick: self.onSelectTreeClick
            }
        };

        self.init=function(){
            $.ajax({
                type: 'POST',
                url: '/module/getSelect',
                data: "id="+view.id(),
                dataType: "json",
                success: function (data) {
                    data.splice(0, 0, { id:0, parent_id:0, name:"根模块", open:true});
                    self.tree=$.fn.zTree.init($("#treeSelecctTree"), self.setting,data);
                    self.setSelectedNode();
                }
            });

        }
    }

    /*----------------------------------------------------------------------------------
     edit select tree code end
     ----------------------------------------------------------------------------------*/


</script>