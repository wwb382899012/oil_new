<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">商品名称</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" placeholder="商品名称" id="goods" name="data[goods]" data-bind="value:goods,attr:{readonly:true}" onclick="selectTreeObj.showTree(this)">
                        <input type="hidden"  id="goods_id" name="data[goods_id]" data-bind="value:goods_id">
                        <div id="treeSelecctContent" class="treeSelecctContent" style="display: none; position: absolute; width: 500px; z-index: 999999;">
                            <ul id="treeSelecctTree" class="ztree ztreeSelect" style="margin-top: 0; height: 400px; overflow: auto"></ul>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">价格</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="price" name= "data[price]" placeholder="价格" data-bind="value:price">
                    </div>
                    <label class="col-sm-2 control-label">币种</label>
                    <div class="col-sm-2">
                        <select class="form-control" title="请选择币种" id="currency" name="data[currency]" data-bind="value: currency,valueAllowUnset: true">
                            <?php foreach ($this->map["currency_type"] as $k => $v) {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            } ?>
                        </select>
                    </div>

                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">价格日期</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="date" name= "data[price_date]" placeholder="价格日期" data-bind="date:price_date">
                    </div>
                    <label class="col-sm-2 control-label">单位</label>
                    <div class="col-sm-3">
                        <select class="form-control" id="unit" name="data[unit]" data-bind="value:unit">
                            <?php foreach($this->map["goods_unit"] as $k=>$v)
                            {
                                echo "<option value='".$v['id']."'>".$v['name']."</option>";
                            }?>
                        </select>
                    </div>

                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">来源</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="source" name= "data[source]" placeholder="来源" data-bind="value:source">
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-6">
                        <textarea class="form-control" id="remark" name= "data[remark]" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save">保存</button>
                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='data[price_id]' data-bind="value:price_id" />
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<script type="text/javascript" src="/zTree/js/jquery.ztree.all-3.5.min.js"></script>
<script type="text/javascript" src="/zTree/js/jquery.ztree.exhide-3.5.min.js"></script>
<link href="/zTree/css/zTreeStyle.css" rel="stylesheet" type="text/css" />
<script>
    var view;
    var selectTreeObj;
    $(function () {
        view=new ViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view);
        selectTreeObj=new SelectTree();
        selectTreeObj.init();
    });

    function ViewModel(option){
        var defaults={
            price_id:0,
            goods_id:0,
            source:"",
            price:0.0,
            goods:"",
            price_date:inc.getNowDate(),
            unit:1,
            currency:1,
            remark:""
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.goods_id=ko.observable(o.goods_id).extend({
            custom:{
                params: function (v) {
                    return parseInt(v)==v && v>0;
                },
                message: "请选择商品"
            }
        });
        self.price=ko.observable(o.price).extend({required:true,number:true});
        self.price_id=ko.observable(o.price_id);
        self.currency=ko.observable(o.currency);
        self.source=ko.observable(o.source);
        self.goods=ko.observable(o.goods).extend({required:true});
        self.unit=ko.observable(o.unit);
        self.price_date=ko.observable(o.price_date);
        self.remark=ko.observable(o.remark);
        self.errors=ko.validation.group(self);
        self.isValid=function () {
            return self.errors().length===0;
        }

        self.buttonText=ko.observable("保存");
        self.save=function () {
            if(!self.isValid()){
                self.errors.showAllMessages();
                return;
            }

            self.buttonText("保存中 "+inc.loadingIco);
            var formData=$("#mainForm").serialize();
            $.ajax({
                type:"POST",
                url:"/price/save",
                data:formData,
                dataType:"json",
                success:function (json) {
                    self.buttonText("保存");
                    if(json.state==0){
                        if(document.referrer){
                            location.href=document.referrer;
                        }else{
                            location.href="/price/";
                        }
                    }else{
                        alert(json.data);
                    }
                },
                error:function (data) {
                    self.buttonText("保存");
                    alert("保存失败："+data.responseText);
                }
            });
        }

        self.back=function () {
            history.back();
        }
    }


    function SelectTree(){
        var self=this;
        self.tree;
        self.treeContainer=$("#treeSelecctContent");

        self.zNodes =[

        ];

        //选中时触发的函数
        self.onSelectTreeClick= function (e, treeId, treeNode) {
            view.goods(treeNode.name);
            view.goods_id(treeNode.goods_id);
        }

        //设置选定值
        self.setSelectedNode=function () {
            var node = self.tree.getNodeByParam("goods_id", view.goods_id(), null);
            if(node!=null)
            {
                self.tree.selectNode(node);
                view.goods(node.name);
            }
            else
            {
                view.goods("请选择商品");
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
                    idKey: "goods_id",
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
                url: '/goods/getSelect',
                //data: "id="+view.id(),
                dataType: "json",
                success: function (data) {
                    //data.splice(0, 0, { id:0, parent_id:0, name:"请选择商品", open:true});
                    self.tree=$.fn.zTree.init($("#treeSelecctTree"), self.setting,data);
                    self.setSelectedNode();
                }
            });

        }
    }

</script>