<div class="box box-success" id="main-container">
    <div class="box-header with-border">
        <h3 class="box-title">用户详情</h3>
        <div class="pull-right box-tools">
            <button type="button"  class="btn btn-default btn-sm" onclick="edit()">修改</button>
        </div>
    </div>
    <div class="box-body">
        <form id="mainForm" role="form" class="form-horizontal">
            <input type="hidden"  id="userId" name="obj[userId]" data-bind="value:userId">
            <div class="form-group">
                <label class="col-sm-2 control-label">用户名</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $data["user_name"] ?></p>
                </div>
                <label class="col-sm-2 control-label">主角色</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $data["role_name"] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">企业微信Id</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $data["identity"] ?></p>
                </div>
                <label class="col-sm-2 control-label">状态</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $this->map["user_status"][$data["status"]] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">姓名</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $data["name"] ?></p>
                </div>
                <label class="col-sm-2 control-label">手机</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $data["phone"] ?></p>
                </div>

            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Email</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $data["email"] ?></p>
                </div>
                <label class="col-sm-2 control-label">微信号</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $data["weixin"] ?></p>
                </div>

            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">所属角色</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><?php echo UserService::getRoleNames($data["role_ids"]) ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">管理主体公司</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><?php echo UserService::getCorpNames($data["corp_ids"],"<br/>") ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">登陆时间</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $data["login_time"] ?></p>
                </div>
                <label class="col-sm-2 control-label">登陆次数</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $data["login_count"] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">说明</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><?php echo $data["remark"] ?></p>
                </div>

            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <div id="tree-container" class="tree-container">
                        <ul id="showTree" class="ztree ztreeSelect" style="margin-top: 0; "></ul>
                    </div>
                </div>
            </div>

        </form>
    </div>
    <div class="box-footer text-right">
        <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
    </div>
</div>
<script type="text/javascript" src="/zTree/js/jquery.ztree.all-3.5.min.js"></script>
<script type="text/javascript" src="/zTree/js/jquery.ztree.exhide-3.5.min.js"></script>
<link href="/zTree/css/zTreeStyle.css" rel="stylesheet" type="text/css" />

<script>
    var view;
    var treeObj;

    $(function(){

        view=new UserViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view,$("#main-container")[0]);

        treeObj=new ShowTree();
        treeObj.init();

    });
    function UserViewModel(option) {
        var self = this;
        self.userId=ko.observable(<?php echo $data["user_id"] ?>);
        self.actionState=0;

        self.back = function () {
            //location.href = "/user/";
//            history.back();
	        <?php
	        if (!empty($_GET["url"])) {
		        echo 'location.href="' . $this->getBackPageUrl() . '";';
	        } else {
		        echo "history.back();";
	        }
	        ?>
        }

    }

    function ShowTree(){
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
                        str += "<div class='item-container'><input type='checkbox' class='action' disabled value='" + arr[1] + "' id='a_" + arr[1] + "' "+c+"  /> " + arr[0] + "</div>";
                    }
                }
                str = "<div id='actions_" + node.id + "' class='tree-node-diy-container'>" + str + "</div>";
                aObj.after(str);
            }
        }

        //验证权限
        self.checkActionSelected=function(id,action){
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



        self.setting = {
            view: {
                addDiyDom: self.addDiyDom
            },
            check: {
                enable: false
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
            }
        };


        self.init=function(){
            self.tree=$.fn.zTree.init($("#showTree"), self.setting,<?php echo json_encode($modules) ?>);
            self.tree.expandAll(true);
        }
    }

    function edit()
    {
        location.href="/user/edit/?t=<?php echo $this->isExternal ?>&id=<?php echo $data["user_id"] ?>";
    }

</script>