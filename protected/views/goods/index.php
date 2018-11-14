<script type="text/javascript" src="/zTree/js/jquery.ztree.all-3.5.min.js"></script>
<script type="text/javascript" src="/zTree/js/jquery.ztree.exhide-3.5.min.js"></script>
<script type="text/javascript" src="/zTree/js/jquery.ztree.exedit-3.5.js"></script>
<link href="/zTree/css/zTreeStyle.css" rel="stylesheet" type="text/css" />

<div class="box box-primary">
    <div class="box-body">
        <form class="search-form" onSubmit="filterTree();return false;">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="input-group">
                            <div class="input-group-addon">商品名称</div>
                            <input type="text" class="form-control input-sm" id="searchName" placeholder="商品名称" value="" onkeydown="search(event)">
                        </div>
                    </div>
                </div>
            </div>
            &emsp;
            <input type="button" value="&nbsp;查询&nbsp;" class="btn btn-success btn-sm" onclick="filterTree(this);">
            <input type="button" value="&nbsp;添加&nbsp;" id="addButton" class="btn btn-success btn-sm" onclick="addNode(0);">
        </form>
    </div>
</div>
<section class="content-list" style="height: auto;">
    <div class="panel panel-default" style="min-width:1050px;">
        <div class="box-body">
            <ul id="treeDemo" class="ztree"></ul>
        </div>
    </div>
</section>
<div class="modal fade" id="type-edit-from">
</div>
<script type="text/javascript" src="/js/template.js"></script>

<script type="text/html" id="hoverBtn">
    <div class="" style="display:inline" id="myBtnList_{{id}}">
        <a class="level0" style="padding-left:12px;" href="javascript:void(0);" onclick="viewDialog({{id}}, {{(parentId)?parentId:0}});">
            <span>查看</span>
        </a>
        <a class="level0" style="padding-left:12px;" href="javascript:void(0);" onclick="showDialog({{id}}, {{(parentId)?parentId:0}});">
            <span>编辑</span>
        </a>
        <a class="level0" style="padding-left:12px;" href="javascript:void(0);" onclick="addNode('{{id}}');">
            <span>添加子项</span>
        </a>
        {{if enable}}
        <a class="level0" style="padding-left:12px;" href="javascript:void(0);" onclick="disableItem('{{id}}', '{{tId}}', false);">
            <span>停用</span>
        </a>
        {{/if}}
        {{if !enable}}
        <a class="level0" style="padding-left:12px;" href="javascript:void(0);" onclick="disableItem('{{id}}', '{{tId}}', true);">
            <span>启用</span>
        </a>
        {{/if}}
        <a class="level0" style="padding-left:12px;" href="javascript:void(0);" onclick="deleteItem('{{id}}', '{{tId}}', true);">
            <span>删除</span>
        </a>
    </div>
</script>

<script type="text/html" id="dialogForm">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">{{title}}</h4>
            </div>
            <div class="modal-body">
                <form id="goods-form" role="form">
                    <div class="form-group">
                        <label for="">商品名称</label>
                        <input type="text" class="form-control" attrName="name" name="obj[name]" placeholder="类别名称" value="{{obj.name}}">
                        <input type="hidden" attrName="goods_id"  name="obj[goods_id]" value="{{obj.goods_id}}">
                        <input type="hidden" attrName="parent_id"  name="obj[parent_id]" value="{{obj.parentId}}">
                    </div>
                    
                    <div class="form-group">

                        <label class="">单位</label>
                        <select class="form-control" attrName="unit" name="obj[unit]" >
                            {{each goods_unit as unit}}
                                {{if obj.unit == unit.id}}
                                <option value='{{unit.id}}' selected="selected">{{unit.name}}</option>
                                {{/if}}
                                {{if obj.unit != unit.id}}
                                <option value='{{unit.id}}'>{{unit.name}}</option>
                                {{/if}}
                            {{/each}}
                        </select>
                    </div>
                    <div class="form-group">

                        <label class="">状态</label>
                        <select class="form-control" attrName="status" name="obj[status]">
                            {{each goods_status as unit ind }}
                                {{if obj.status == ind}}
                                <option value='{{ind}}' selected="selected">{{unit}}</option>
                                {{/if}}
                                {{if obj.status != ind}}
                                <option value='{{ind}}'>{{unit}}</option>
                                {{/if}}
                            {{/each}}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">说明</label>
                        <input type="text" class="form-control" attrName="remark" name="obj[remark]" placeholder="说明" value="{{obj.remark}}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" onclick="saveNode();">保存</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</script>

<script type="text/html" id="dialogView">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">{{title}}</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="form-group row">
                        <label class="col-sm-2 control-label">商品名称</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{obj.name}}</p>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2 control-label">单位</label>
                        <div class="col-sm-10">
                        {{each goods_unit as unit}}
                            {{if obj.unit == unit.id}}
                            <p class="form-control-static">{{unit.name}}</p>
                            {{/if}}
                        {{/each}}
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 control-label">状态</label>
                        <div class="col-sm-10">
                        {{each goods_status as unit ind }}
                            {{if obj.status == ind}}
                            <p class="form-control-static">{{unit}}</p>
                            {{/if}}
                        {{/each}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 control-label">说明</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{obj.remark}}</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 control-label">更新时间</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{obj.update_time}}</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 control-label">更新人</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{obj.updater_name}}</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 control-label">创建时间</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{obj.create_time}}</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 control-label">创建人</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{obj.creater_name}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</script>
<script type="text/javascript">
    
    window.zTreeObj;
    window.zTreeObjChanging = false;
    window.zTreeData = <?php echo json_encode($tree)?>;
    window.goods_unit = <?php echo json_encode(array_values($this->map['goods_unit']))?>;
    window.goods_status = <?php echo json_encode($this->map['goods_status'])?>;
    window.hiddenNodes = []; // 隐藏节点
    window.currentUser = '<?php 
        $user = Utility::getNowUser();
        echo $user['name'];?>';
    $(document).ready(function(){
        var setting = {
            callback:{
                // onNodeCreated:function(event, treeId, treeNode) {
                //     if (treeNode.enable==false) {
                //     }
                // },
                // onDrag: function(event, treeId, treeNodes){
                //     return true;
                // },
                // onDrop:function(event, treeId, treeNodes, targetNode, moveType) {
                //     // 防止两次进入这段代码,已经禁止了多选
                // },
                // onExpand: function(){
                //     return true;
                // },
                // beforeDrop:beforeDrop,
            },
            edit:{
                drag:{
                    isMove:true,
                    isCopy:false,
                    isCopy: true,
                    isMove: true,
                    prev: true,
                    next: true,
                    inner: true
                },
                // 暂时不用拖动等
                enable:false,
                showRenameBtn:false,
                showRemoveBtn:false
            },
            view: {
                addHoverDom: showChildNodeInfo,
                removeHoverDom:function (treeId, treeNode) {
                    $("#myBtnList_"+treeNode.goods_id).unbind().remove();
                },
                autoCancelSelected:true,
                selectedMulti:false,
                fontCss:function (treeId, treeNode) {
                    return treeNode.status == 0 ? {color:"gray"} : {color:"black"};
                },
            },
            data:{
                keep:{
                    parent : false
                },
                simpleData: {
                    enable: true
                },
            }
        };
        window.zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, window.zTreeData);
    });

    function addNode(parentNodeId) {
        showDialog(null, parentNodeId);
        return;
    }

    function showDialog(id, parentId) {
        var node = {};
        if(id != null) {
            node = window.zTreeObj.getNodeByParam("goods_id", id);
        } else {
            node = {parentId:'', id:'', name:''};
        }
        if(parentId != 0 && parentId != null) {
            parentNode = window.zTreeObj.getNodeByParam("goods_id", parentId);
            node.unit = (node.unit) ? node.unit : parentNode.unit;
        }
        node.parentId = parentId;
        var title = (id==null)?'添加':'编辑';
        var innerHtml = template('dialogForm', {obj:node, title:title+'商品', goods_unit:window.goods_unit, goods_status:window.goods_status});
        $("#type-edit-from").html(innerHtml);
        $("#type-edit-from").modal("show");
    }

    function viewDialog(id) {
        var node = {};
        node = window.zTreeObj.getNodeByParam("goods_id", id);
        var innerHtml = template('dialogView', {obj:node, title:'查看商品', goods_unit:window.goods_unit, goods_status:window.goods_status});
        $("#type-edit-from").html(innerHtml);
        $("#type-edit-from").modal("show");
    }

    function disableItem(id, nodeId, enable) {
        var node = window.zTreeObj.getNodeByParam("goods_id", id);
        var name = node.name;
        var confirmString = enable? "启用":"停用";
        layer.confirm("是否确定"+confirmString+"该商品？", {icon: 3, title: '提示'}, function(){
            $.ajax({
                data:{
                    goods_id:node.goods_id,
                    name:node.name,
                    status:enable?1:0, 
                },
                url:'/goods/ajaxSave/',
                method:'post',
                dataType:'json',
                success:function(data) {
                    if(data.state == 0) {
                        layer.msg("更新成功", {icon: 6, time:1000},function() {
                            node.status = enable?1:0;
                            window.zTreeObj.updateNode(node);
                            disableChildNode(node, enable);
                        });
                    } else {
                        layer.alert(data.data);
                    }
                },
                error:function(data) {
                    layer.alert('更新失败,请重试');
                }
            });
        });
    }

    function disableChildNode(node, enable) {
        node.enable = enable;
        if(node.children.length>0) {
            for (var i = node.children.length - 1; i >= 0; i--) {
                var child = window.zTreeObj.getNodeByParam("goods_id", node.children[i].goods_id);
                disableChildNode(child, enable);
            }
        }
        window.zTreeObj.updateNode(node);
        setTimeout(function() {
            $("#myBtnList_"+node.goods_id).unbind().remove();
        }, 10);
    }

    function deleteItem(id, nodeId, enable) {
        var node = window.zTreeObj.getNodeByParam("goods_id", id);
        layer.confirm("您确定要删除该条记录，此操作不可逆？", {icon: 3, title: '提示'}, function(){
            var formData="id="+id;
            $.ajax({
                type:"POST",
                url:"/goods/ajaxDel",
                data:formData,
                dataType:"json",
                success:function (json) {
                    if(json.state==0){
                        layer.msg("删除成功！", {icon: 6, time:1000},function() {
                            // 去掉事件生成的内容,在继续删除node
                            $("#myBtnList_"+node.goods_id).remove();
                            window.zTreeObj.removeNode(node);
                        });
                    }else{
                        layer.alert(json.data);
                    }
                },
                error:function (data) {
                    layer.alert("删除失败："+data.responseText);
                }
            });
        });
    }

    function showChildNodeInfo (treeId, treeNode) {
        if(treeNode.tId) {
            var aObj = $("#" + treeNode.tId + "_a");
            if ($("#myBtnList_"+treeNode.goods_id).length>0) 
                return;
            var id = treeNode.goods_id, tId = treeNode.tId;
            var editStr = template("hoverBtn", {id:id, tId:treeNode.tId, enable:parseInt(treeNode.status) == 1, parentId:treeNode.pId});
            aObj.append(editStr);
        }
    }

    function saveNode() {
        var data = {};
        var inputs = $("#goods-form [name^=obj]");
        for(var ind = inputs.length - 1; ind >=0 ; ind --) {
            data[$(inputs[ind]).attr("attrName")] = $(inputs[ind]).val();
        }
        if(data['name'] == '') {
            layer.alert('请填写分类名称');
            return false;
        }
        var isUpdate = data.goods_id == '' ? true : false;

        $.ajax({
            data:data,
            method:'post',
            url:'/goods/ajaxSave',
            dataType:'json',
            success:function(ret) {
                if(ret.state == 0) {
                    layer.msg("保存成功", {icon: 6, time:1000},function() {
                        $("#type-edit-from").modal("hide");
                        if(!isUpdate) {
                            var node = window.zTreeObj.getNodeByParam("goods_id", data.goods_id);
                            node.name = data.name;
                            node.unit = data.unit;
                            node.remark = data.remark;
                            node.status = data.status;
                            node.update_time = new Date().format('yyyy-M-d H:m:s');
                            node.updater_name = window.currentUser;
                            window.zTreeObj.updateNode(node);
                        } else {
                            var newNode = {goods_id:ret.data, name:data.name, children:[], status:data.status, remark:data.remark};
                            if(data.parent_id != null && data.parent_id != 0){
                                var parentNode = window.zTreeObj.getNodeByParam("goods_id", data.parent_id);
                                parentNode.isParent = true;
                                newNode.parent_id = data.parent_id;
                                newNode.unit = data.unit;
                                newNode.update_time = new Date().format('yyyy-M-d H:m:s');
                                newNode.create_time = new Date().format('yyyy-M-d H:m:s');
                                newNode.updater_name = window.currentUser;
                                newNode.creater_name = window.currentUser;
                                window.zTreeObj.addNodes(parentNode, newNode);
                            }
                            else {
                                newNode.parent_id = 0;
                                window.zTreeObj.addNodes(null, [newNode]);
                            }
                        }
                    });
                } else {
                    layer.alert(ret.data);
                }
            },
            error:function(ret) {
                layer.alert('更新失败,请重试');
            }
        });
    }

    function filterTree(obj) {      
        if(window.hiddenNodes.length>0)
            zTreeObj.showNodes(window.hiddenNodes);
        window.hiddenNodes = window.zTreeObj.getNodesByFilter(filter);
        window.zTreeObj.hideNodes(window.hiddenNodes);
    }

    function filter(node) {
        var _keywords = $("#searchName").val();
        if(node == null) {
            return true;
        }
        if(!node.isParent&&node.name.indexOf(_keywords)!=-1) return false;
        if(node.isParent) {
            var children = window.zTreeObj.getNodesByFilter(filter, false, node);
            return children.length >= node.children.length;
        }
        return true;  
    }

    function search(event) {
        if(event.keyCode==13) {
            filterTree();
        }
        event.stopPropagation();
        return false;
    }
</script>
