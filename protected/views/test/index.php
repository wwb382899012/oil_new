<?php
//Goods::getActiveTreeTable("　");
$s=array(
    'key'=>'test',
    'name'=>'货物质量标准是否一致',
    'type'=>'koSelectButtonsWithRemark',
    'required'=>true,
    'options'=>array(
        array('text'=>'是', 'value'=>'1'),
        array('text'=>'否', 'value'=>'0'),
    ),
    'remark_required'=>array("0"),
);

$items=array(
    array(
        'key'=>'isThirdParty',
        'text'=>'是否第三方库',
        'type'=>'koSelectButtons',
        'required'=>true,
        'options'=>array(
            array('text'=>'是', 'value'=>'1'),
            array('text'=>'否', 'value'=>'0'),
        )
    ),
    array(
        'key'=>'test',
        'name'=>'货物质量标准是否一致',
        'type'=>'koSelectButtonsWithRemark',
        'required'=>true,
        'options'=>array(
            array('text'=>'是', 'value'=>'1'),
            array('text'=>'否', 'value'=>'0'),
        ),
        'remark_required'=>array("0"),
    ),
    array(
        'key'=>'isLargeStorehouse',
        'text'=>'库容是否大于十万方',
        'type'=>'koSelectButtons',
        'required'=>true,
        //'value'=>-1,
        'options'=>array(
            array('text'=>'是', 'value'=>'1'),array('text'=>'否', 'value'=>'0'),
        )
    ),
    array(
        'key'=>'isTrade',
        'text'=>'仓库是否参与贸易',
        'type'=>'koSelectButtons',
        'required'=>true,
        'options'=>array(
            array('text'=>'是', 'value'=>'1'),array('text'=>'否', 'value'=>'0'),
        )
    ),
    array(
        'key'=>'hasErp',
        'text'=>'ERP操作系统',
        'type'=>'koSelectButtons',
        'required'=>true,
        'options'=>array(
            array('text'=>'是', 'value'=>'1'),array('text'=>'否', 'value'=>'0'),
        )
    ),
    array(
        'key'=>'erpCheckable',
        'text'=>'ERP系统查看货权',
        'type'=>'koSelectButtons',
        'required'=>true,
        'options'=>array(
            array('text'=>'可以', 'value'=>'1'),array('text'=>'不可以', 'value'=>'0'),
        )
    ),
    array(
        'key'=>'checkPort',
        'text'=>'开放ERP系统查看监管端口',
        'type'=>'koSelectButtons',
        'required'=>true,
        'options'=>array(
            array('text'=>'可以', 'value'=>'1'),array('text'=>'不可以', 'value'=>'0'),
        )
    ),
    array(
        'key'=>'isReliable',
        'text'=>'失信与重大诉讼',
        'type'=>'koTextArea',
        'required'=>true, ),
    array(
        'key'=>'others',
        'text'=>'其他说明',
        'type'=>'koTextArea',
        'required'=>false, ),

    array(
        'key'=>'isLargeStorehouse',
        'text'=>'库容是否大于十万方',
        'type'=>'koSelect',
        'options_caption'=>"请选择库容",
        'required'=>true,
        'items'=>array(
            "1"=>array("id" => 1, "name" => "履约保证金"),
            "2"=>array("id" => 2, "name" => "预付款"),
            "3"=>array("id" => 3, "name" => "货款"),
            "4"=>array("id" => 4, "name" => "进口关税增值税保证金"),
            "5"=>array("id" => 5, "name" => "其他","type"=>"input"),
        )
    ),
);

$items=$this->map["storehouse_checkitems_config"];
include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/checkItems.php";

?>
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">

                <!-- ko component: {
                              name: "check-items",
                              params: {
                                          items: items

                                          }
                          } -->
                <!-- /ko -->
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">银行账号</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="account_no" name= "obj[account_no]" placeholder="银行账号" data-bind="typeahead:name,source:getData">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">银行账号</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="account_no1" name= "obj[account_no]" placeholder="银行账号">
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <span data-bind="text:itemsIsValid"></span>
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:show">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

</section>
<script>

    var t=<?php echo json_encode($this->map["agent_fee_pay_type"]) ?>;
    /*console.log(t);
    console.log(inc.mapObjectToArray(t));

    var t1={
        "id":1,
        "name":"OK"
    };

    var t2=function () {
        this.id=function () {
            console.log(123);
        }
        
        this.test=function () {
            
        }
    }
    var tt=$.extend(t1,t2);
    console.log(tt);
    console.log(tt.id);
*/

    var view;
    $(function () {
        view=new ViewModel();
        ko.applyBindings(view);
        $("#account_no1").on("keyup",function(event)
        {
            console.log(event.keyCode);
        })
        //console.log(window.event.keyCode);
        console.log(inc.dateAdd(new Date("2016-1-30"),"m",1));
    });

    function ViewModel()
    {
        var self=this;
        self.items=ko.observableArray(<?php echo json_encode($items)?>);

        self.options=["adfasdfs","2333","ccccc"];
        self.typeOptions=ko.observableArray();

        self.id=ko.observable(0);
        self.name=ko.observable("test").extend({
            custom:{
                params: function (v) {
                   return self.id()>0
                },
                message: "请填写正确的合同号"
            }
        });
        self.name.subscribe(function(v){
            var i=ko.utils.arrayFirst(self.typeOptions(),function (item) {
               return (item.name==v);
            });
            if(i)
                self.id(i.id);
            else
                self.id(0);
        });
        
        self.getData=function (query,process) {
            var url="/payClaim/ajaxContract?corporation_id=1";
            //console.log(process);
            $.ajax({
                url:url,
                data:{
                    search:query,
                },
                method:'post',
                dataType:'json',
                async:false,
                success:function(data) {
                    if(data.state==0){
                        self.typeOptions.removeAll();
                        var options = [], retData = data.data;
                        for (var i = 0; i <=retData.length - 1; i++) {
                            var thisData = {
                                'id':retData[i].contract_id,
                                'name':retData[i].contract_code,
                                'type':retData[i].type,
                            };
                            self.typeOptions.push(thisData);
                        }
                        if($.isFunction(process)) {
                            process(self.typeOptions());
                        }
                    }
                },
                error:function(res) {

                }
            });
        }
        self.itemsIsValid=ko.observable(false);
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.show=function () {
            console.log(self.items.getValues());
            console.log(ko.toJS(self));
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }

        }



    }


    function addMoth(d,m){
        //var ds=d.split('-'),_d=ds[2]-0;
        var date=new Date(d);
        var ds=date.getDate();
        var _d=ds;
        console.log(ds);
        console.log(date);
        var nextM=new Date(date.getFullYear(),date.getMonth()+m+1, 0 );
        var max=nextM.getDate();
        console.log(nextM);
        console.log(max);
        console.log(date.getMonth()+m);
        d=new Date( date.getFullYear(),date.getMonth()+m,_d>max? max:_d );
        return d.toLocaleDateString().match(/\d+/g).join('-')
    }
    /*console.log(  addMoth('2017-11-30 ',1) )
    console.log(  addMoth('2017-11-30 ',2) )
    console.log(  addMoth('2017-11-30 ',3) )*/

</script>