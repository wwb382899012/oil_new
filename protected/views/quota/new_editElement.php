
<template id='component-template-quota-items' >
    <ul class="table-com">
        <li>
            <span style="width:0;flex: 0 0 300px;">占用对象</span>
            <span>占用额度<span class="must-logo">*</span></span>
            <span>备注</span>
            <span style="flex: 0 0 100px;">操作</span>
        </li>
        <!-- ko foreach:quotas -->
        <li>
            <div style="width:0;flex: 0 0 300px;overflow:unset;">
                <select class="show-menu-arrow form-control" 
                data-live-search="true"
                data-bind="
                selectPickerOptions:managers,
                optionsText:'name',
                selectpicker: user_id, optionsValue:'user_id',
                optionsCaption:'请选择',
                valueAllowUnset: true">
                </select>
            </div>
            <div class="input-with-logo-right">
                <span>万元</span>
                <input type="number" data-bind="moneyWan:quota">
            </div>
            <p>
                <input class="form-control" style="width:100%;" type="text" data-bind="value:remark">
            </p>
            <!-- <button class="delete-btn-custom btn-xs" data-bind="click:function() {
                        $parent.del($index())
                    }">删除</button> -->
            <p style="flex: 0 0 100px;">
                <a href="javascript:void(0)" class="z-btn-action" data-bind="click:function() {
                    $parent.del($index())
                }">删除</a>
            </p>
        </li>
        <!-- /ko -->
        <li>
            <p style="width:0;flex: 0 0 300px;"></p>
            <p></p>
            <p></p>
            <p style="flex: 0 0 100px;">
                <a href="javascript: void 0" style="color: #fff;background-color: #FF6E34;" class="o-btn o-btn-primary action" data-bind="click:add">新增</a>
            </p>
        </li>
    </ul>
</template>
<script type="text/javascript">
    ko.components.register('quota-items', {
        template: { element: 'component-template-quota-items' },
        viewModel:quotaItemsModel
    });

    function quotaItemsModel(params) {
        var defaults = {
            quotas:[],
            managers:[],
        }
        var o = $.extend(defaults, params);
        var self = this;
        self.quotas = o.quotas;
        self.managers = o.managers;
        self.del = function(ind) {
            self.quotas.splice(ind, 1);
        }
        self.add = function() {
            var managers = self.managers;
            var companies = self.companies;
            self.quotas.push(new QuotaModel({managers:managers, companies:companies}));
        }
        self.isValid=function () {
            return self.errors().length===0;
        }
    }

    function QuotaModel(option) {
        var defaults = {
            user_id : '',
            quota:'',
            remark:'',
            managers:[]
        }
        var o = $.extend(defaults, option);
        var self = this;
        self.user_id = ko.observable(o.user_id).extend({custom:{
            params:function (v) {
                if(v==null || v=="")
                    return false;
                return true;
            },
                message:"请选择占用对象"
            }});
        self.quota = ko.observable(o.quota).extend({required:true});
        self.remark = ko.observable(o.remark);
        self.managers = ko.observableArray(o.managers);
        self.getValue = function() {
            return {
                user_id : self.user_id(),
                quota : self.quota(),
                remark : self.remark()
            }
        }
    }
</script>