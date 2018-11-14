<div class="box box-success" id="main-container">
    <div class="box-header with-border">
        <h3 class="box-title">设置角色权限</h3>
    </div>
    <div class="box-body">
        <form id="mainForm" role="form" class="form-horizontal">
            <input type="hidden"  id="userId" name="obj[userId]" data-bind="value:userId">
            <div class="form-group">
                <label class="col-sm-2 control-label">角色</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><?php echo $data["role_name"] ?></p>
                </div>
            </div>

            <div class="form-group">

                <div class="col-sm-12">

                    <div id="tree-container" class="tree-container">
                        <ul id="treeSelecctTree" class="ztree ztreeSelect" style="margin-top: 0; "></ul>
                    </div>
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

        view=new RightViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view,$("#main-container")[0]);

        selectTreeObj=new SelectTree();
        selectTreeObj.init();

    });
    function RightViewModel(option) {
        var self = this;
        self.roleId=ko.observable(<?php echo $data["role_id"] ?>);
        self.actionState=0;
        self.save = function () {

            var v=selectTreeObj.getSelectedValue();
            //console.log(v);
            //return;
            var formData ="id="+self.roleId()+"&data="+v;
            if(self.actionState==1)
                return;
            self.actionState=1;
            $.ajax({
                type: 'POST',
                url: '/role/saveRight',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
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
            history.back();
        }

    }



    /************************************************************************************
     edit select tree code start
     *************************************************************************************/

    function SelectTree(){
        var self=this;
        self.tree;
        self.treeContainer=$("#treeSelecctContent");
        self.selectedItemObject=<?php echo json_encode($selectedObject); ?>;

        //自定义节点
        self.addDiyDom= function (treeId, node) {
            var aObj = $("#" + node.tId + "_a");
            if ($("#actions_"+node.id).length>0) return;
            if(node.actions) {
                var actions = node.actions.split(",");
                var str = "";
                var allChecked="checked";
                for (var i = 0; i < actions.length; i++)
                {
                    if (actions[i] != null && actions[i].length > 0)
                    {
                        var arr=actions[i].split("|");
                        var checked=self.checkActionSelected(node.id,arr[1])
                        var c="";
                        if(checked)
                            c="checked";
                        else
                            allChecked="";
                        str += "<div class='item-container'><input type='checkbox' class='action' value='" + arr[1] + "' id='a_" + arr[1] + "' "+c+"  onclick='checkSelected(this)' /> " + arr[0] + "</div>";
                    }
                }
                str = "<div id='actions_" + node.id + "' class='tree-node-diy-container'><div class='item-container'><input type='checkbox' class='all-action'  "+allChecked+" onclick='selectAll(this)'/>选中所有</div>" + str + "</div>";
                aObj.after(str);
            }
        }

        //验证权限
        self.checkActionSelected=function(id,action){
            //{"1":[""],"2":["index","add","edit"],"3":["index","add"]}
            if(!self.selectedItemObject[id])
                return false;
            var n=self.selectedItemObject[id].length;
            if(n)
            {
                for(var i=0;i<n;i++)
                {
                    if(self.selectedItemObject[id][i].toLowerCase()==action.toLowerCase())
                        return true;
                }
            }
            return false;
        }

        //设置选定值
        self.setSelectedNode=function () {

            var nodes = self.tree.getNodesByFilter(self.filter);
            for(var i=0;i<nodes.length;i++)
                self.tree.checkNode(nodes[i]);
        }

        self.filter=function (node) {
            return self.selectedItemObject[node.id]!=null
        }

        self.setting = {
            view: {
                addDiyDom: self.addDiyDom
            },
            check: {
                enable: true
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

            }
        };

        self.getSelectedValue=function()
        {
            var nodes = self.tree.getCheckedNodes(true);
            var ids="0";
            var actionObject={};
            for(var i=0;i<nodes.length;i++)
            {
                ids+=","+nodes[i].id;
                var s="";
                $("#actions_"+nodes[i].id).find("input.action:checked").each(function(){
                    s+=$(this).val()+",";
                });
                actionObject[nodes[i].id]=s;
            }
            return JSON.stringify(actionObject);
        }

        self.init=function(){
            $.ajax({
                type: 'POST',
                url: '/module/getActive',
                dataType: "json",
                success: function (data) {
                    self.tree=$.fn.zTree.init($("#treeSelecctTree"), self.setting,data);
                    self.tree.expandAll(true);
                    self.setSelectedNode();
                }
            });
        }
    }

    /*----------------------------------------------------------------------------------
     edit select tree code end
     ----------------------------------------------------------------------------------*/

function selectAll(e){
    //alert(e.checked);
    $(e).parent().parent().find("input.action[type=checkbox]").each(function () {
        $(this)[0].checked= e.checked;
    });
}
    function checkSelected(e){
        var c=true;
        $(e).parent().parent().find("input.action[type=checkbox]").each(function () {
            if(!$(this)[0].checked)
                c=false;
        });
        $(e).parent().parent().find("input.all-action[type=checkbox]")[0].checked=c;
    }
</script>