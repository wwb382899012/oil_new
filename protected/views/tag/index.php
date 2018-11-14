<script type="text/javascript" src="/zTree/js/jquery.ztree.all-3.5.min.js"></script>
<script type="text/javascript" src="/zTree/js/jquery.ztree.exhide-3.5.min.js"></script>
<link href="/zTree/css/zTreeStyle.css" rel="stylesheet" type="text/css" />
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">
            <form class="form-inline">
                <button class="btn btn-success" data-toggle="tooltip" title="添加根标签" data-original-title="添加根标签" data-bind="click:add"><i class="fa fa-plus"></i>添加根标签</button>
                <div class="input-group">
                    <input type="text" style="width: 300px;" class="form-control" placeholder="查询标签" id="keyWord" data-bind="textInput:keyWord"><span class="validationMessage" style="display: none;"></span>
                          <span class="input-group-btn">
                            <button class="btn btn-default" type="button" data-bind="click:search">查询</button>
                          </span>
                </div>

            </form>
        </h3>
    </div>
    <div class="box-body">
        <ul id="tree" class="ztree" style="margin-top: 0;"></ul>
    </div>
</div>

<div class="modal fade" id="type-edit-from">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">添加标签</h4>
            </div>
            <div class="modal-body">
                <form id="type-form" role="form">
                    <div class="form-group">
                        <label for="org_name">标签名称</label>
                        <input type="text" class="form-control" id="name" name="obj[name]" placeholder="标签名称" data-bind="value:type.name">
                        <input type="hidden"  id="typeId" name="obj[id]" data-bind="value:type.id">
                        <input type="hidden"  id="parentId" name="obj[parentId]" data-bind="value:type.parentId">
                    </div>

                    <div class="form-group">
                        <label for="org_phone">排序码</label>
                        <input type="text" class="form-control" id="orderIndex" name="obj[orderIndex]" placeholder="排序码" data-bind="value:type.orderIndex" style="width: 80px;">

                    </div>
                    <div class="form-group">
                        <label for="org_name">说明</label>
                        <input type="text" class="form-control" id="remark" name="obj[remark]" placeholder="说明" data-bind="value:type.remark">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" data-bind="click:save">保存</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script type="text/javascript">

    var view;
    var treeObj;

    var treeData = <?php echo json_encode($data) ?>;

    //initialize list page view model
    function initListPageViewModel() {
        view = new ViewModel();
        ko.applyBindings(view,$("#main-container")[0]);
        //treeObj = $.fn.zTree.init($("#tree"), setting, treeData);
        treeObj=new mainTree();
        treeObj.init();
    }

    $(function () {
        initListPageViewModel();
    });

    /************************************************************************************
     tree code start
     *************************************************************************************/

    function mainTree()
    {
        var self=this;
        self.tree;
        //self.treeContainer=$("#tree");



        self.filter=function(treeId, parentNode, childNodes) {
            if (!childNodes) return null;
            for (var i = 0, l = childNodes.length; i < l; i++) {
                childNodes[i].name = childNodes[i].name.replace(/\.n/g, '.');
            }
            return childNodes;
        }

        self.zTreeOnClick=function(event, treeId, treeNode) {
            view.detail(treeNode.id);
        };

        self.addHoverDom=function (treeId, treeNode) {

            var sObj = $("#" + treeNode.tId + "_span");

            if (treeNode.editNameFlag || $("#edit_" + treeNode.tId).length > 0) return;

            var addStr = "<span id='span_" + treeNode.tId + "'>&nbsp;&nbsp;"
                + "&nbsp;&nbsp;<span class='font_blue' id='edit_" + treeNode.tId + "' title='修改' onfocus='this.blur();'>修改</span>"
                + "&nbsp;&nbsp;<span class='font_blue' id='add_" + treeNode.tId + "' title='添加子标签' onfocus='this.blur();'>添加子标签</span>";
            //addStr += "&nbsp;&nbsp;<span class='font_blue' id='showList_" + treeNode.tId + "' title='查看问题' onfocus='this.blur();'>查看问题</span>";
            if(!treeNode.isParent) {
                addStr += "&nbsp;&nbsp;<span class='font_blue' id='delete_" + treeNode.tId + "' title='删除' onfocus='this.blur();'>删除</span>";
            }else
                addStr += "&nbsp;&nbsp;<span class='font_blue' id='refresh_" + treeNode.tId + "' title='重新加载子项' onfocus='this.blur();'>刷新</span>";
            addStr += "</span>";
            sObj.after(addStr);

            //绑定事件
            var btn = $("#edit_" + treeNode.tId);
            if (btn) btn.bind("click", function () {
                update(treeNode);
                return false;
            });
            /*btn = $("#showList_" + treeNode.tId);
             if (btn) btn.bind("click", function () {

             openWindow({link:"/faq/list?search[type]="+treeNode.id});
             return false;
             });*/
            if(!treeNode.isParent) {
                btn = $("#delete_" + treeNode.tId);
                if (btn) btn.bind("click", function () {

                    deleteObject(treeNode);
                    return false;
                });
            }
            else
            {
                btn = $("#refresh_" + treeNode.tId);
                if (btn) btn.bind("click", function () {
                    self.tree.reAsyncChildNodes(treeNode, "refresh", false);
                    return false;
                });
            }

            btn = $("#add_" + treeNode.tId);
            if (btn) btn.bind("click", function () {

                add(treeNode.id);
                return false;
            });


        };

        self.removeHoverDom=function (treeId, treeNode) {
            $("#add_" + treeNode.tId).unbind().remove();
            $("#edit_" + treeNode.tId).unbind().remove();
            $("#delete_" + treeNode.tId).unbind().remove();
            $("#refresh_" + treeNode.tId).unbind().remove();
            $("#showList_" + treeNode.tId).unbind().remove();
            $("#span_" + treeNode.tId).unbind().remove();
        };

        self.setting = {
            view: {
                addHoverDom: self.addHoverDom,
                removeHoverDom: self.removeHoverDom,
                dblClickExpand: false
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
                onClick: self.zTreeOnClick
            }
            ,
            async: {
                enable: true,
                url: "/tag/getAllChildren",
                autoParam: ["id"],
                dataFilter: self.filter
            }
        };

        self.init=function(){
            self.tree=$.fn.zTree.init($("#tree"), self.setting);
        }
    }

    function add(pId) {
        view.add(pId);
    }

    function update(node) {
        view.update(node);
    }


    function deleteObject(obj) {
        if (confirm('您的这次操作将是不可逆的，您确定要删除这条信息吗？'))
            view.del(obj);
    }



    /*----------------------------------------------------------------------------------
     tree code end
     ----------------------------------------------------------------------------------*/


    //标签模型
    function TagModel(option) {
        var defaults = {
            id: 0,
            name: "",
            order_index: 0,
            parent_id: 0,
            remark: ""
        };
        var o = $.extend(defaults, option);

        var self = this;
        self.id = ko.observable(o.id);
        self.parentId = ko.observable(o.parent_id);
        self.name = ko.observable(o.name).extend({ required: true, maxLength: 100 });
        self.orderIndex = ko.observable(o.order_index);
        self.remark = ko.observable(o.remark);

        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.clear = function () {
            self.id(0);
            self.parentId(0);
            self.name("");
            self.remark("");
            self.orderIndex(0);
        }

        self.set = function (obj) {
            self.id(obj.id);
            self.parentId(obj.parent_id);
            self.orderIndex(obj.order_index);
            self.name(obj.name);
            self.remark(obj.remark);
        }

    }

    //view model
    function ViewModel(option) {
        var self=this;

        self.data = ko.observableArray([]);

        self.name = ko.observable();
        self.keyWord = ko.observable();

        self.actionTitle = ko.observable("添加标签");
        self.updateVisible = ko.observable(false);
        self.detailVisible = ko.observable(false);

        self.type = new TagModel();

        self.add = function (pId) {
            self.type.id(0);
            if (pId == undefined || pId == null)
                pId = 0;
            self.type.parentId(pId);
            if(pId>0)
            {
                var node = treeObj.tree.getNodeByParam("id", pId, null);
                if(node!=null) {
                    node.isParent=true;
                }
            }

            self.type.name("");
            self.type.name.isModified(false);
            self.type.orderIndex(0);

            self.actionTitle("添加标签");

            self.openDialog();
        }

        self.update = function (obj) {
            self.type.set(obj);
            self.actionTitle("修改标签");
            self.openDialog();
        }
        self.saveText=ko.observable("保存");
        self.saveState=ko.observable(0);
        self.save = function () {
            if (self.type.isValid()) {

                if(self.saveState()==1)
                {
                    //alertModel("正在提交，请不要重复提交！");
                    return;
                }
                self.saveState(1);
                self.saveText("请在提交，请稍后...");
                var formData = $("#type-form").serialize();

                $.ajax({
                    type: 'POST',
                    url: '/tag/save',
                    data: formData,
                    dataType: "json",
                    success: function (json) {
                        if (json.state == 0) {
                            //self.updateVisible(false);
                            var id =  json.data;
                            self.closeDialog();
                            //如果修改节点，则更新当前节点信息即可，否则更新子节点信息。
                            if (self.type.id() > 0) {
                                if (self.type.id() == id) {//更新原来的节点
                                    var node = treeObj.tree.getNodeByParam("id", self.type.id(), null);
                                    if(node!=null) {
                                        if (node.parent_id == self.type.parentId())//如果当前节点的父节点没有变化，只更新节点本身信息即可
                                        {
                                            //更新左侧的选择树
                                            node.name = self.type.name();
                                            node.order_index = self.type.orderIndex();
                                            treeObj.tree.updateNode(node);
                                        }
                                        else {
                                            treeObj.tree.reAsyncChildNodes(null, "refresh", false);
                                        }
                                    }
                                    else
                                    {
                                        treeObj.tree.reAsyncChildNodes(null, "refresh", false);
                                    }

                                }
                                else {
                                    treeObj.tree.reAsyncChildNodes(null, "refresh", false);
                                }
                            }
                            else {
                                self.type.id(id);
                                self.refreshTree(self.type.parentId());
                            }
                        }
                        else {
                            alert(json.data);
                        }
                        self.saveState(0);
                        self.saveText("保存");
                    },
                    error:function (data) {
                        alertModel("保存失败！"+data.responseText);
                        self.saveState(0);
                    }
                });

            }
        }

        self.del = function (obj) {
            var data = "id=" + obj.id;
            $.ajax({
                type: 'POST',
                url: '/tag/del',
                dataType: "json",
                data: data,
                success: function (json) {
                    if (json.state == 0) {
                        treeObj.tree.removeNode(obj);
                    }
                    else {
                        alertModel(json.data);
                    }
                },
                error: function (data) {
                    alertModel("操作失败！"+data.responseText);
                }
            });
        }

        self.cancel = function () {
            self.updateVisible(false);
            self.detailVisible(false);
        }

        //查看
        self.detail = function (id) {
            $("#detail-container").load("/org/detail?id="+id+"&k="+Math.round());
        }

        //格式化ajax获取的数据
        self.formatData = function (data) {
            self.data.removeAll();
            for (var i = 0; i < data.length; i++) {
                self.data.push(new TagModel(data[i]));
            }
        }



        //刷新指定id节点的所有子类型
        self.refreshTree=function(id){
            var node = treeObj.tree.getNodeByParam("id", id, null);
            treeObj.tree.reAsyncChildNodes(node, "refresh", false);
        }

        self.openDialog=function(){
            $("#type-edit-from").modal({backdrop:'static'});
        }
        self.closeDialog=function(){
            $("#type-edit-from").modal("hide");
        }


        self.search=function(){
            if(self.keyWord()!=undefined && self.keyWord()!="")
            {
                var node = treeObj.tree.getNodeByParam("name", self.keyWord(), null);
                if(node!=null) {
                    treeObj.tree.selectNode(node);
                }
                else
                {
                    alert("不存在当前名称名称的标签！");
                }
            }
        }
    }



</script>