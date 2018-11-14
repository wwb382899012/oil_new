
<template id='component-template-quota-items' >
    <table class="table table-hover table-hover-custom">
        <thead>
            <tr>
                <th style="width:200px;">占用对象</th>
                <th style="width:200px;">占用额度<span class="text-red fa fa-asterisk"></span></th>
                <th style="width:200px;">备注</th>
                <th style="width:100px;">操作</th>
            </tr>
        </thead>
        <tbody data-bind="foreach:quotas">
            <tr>
                <td>
                    <select class="form-control" data-bind="options:managers,optionsText:'name',value: user_id, optionsValue:'user_id'">
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <input class="form-control" type="number" data-bind="moneyWan:quota">
                        <span class="input-group-addon">万元</span>
                    </div>
                </td>
                <td>
                    <input class="form-control" data-bind="value:remark">
                </td>
                <td>
                    <button class="btn delete-btn-custom btn-xs" data-bind="click:function() {
                        $parent.del($index())
                    }">删除</button>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">
                    <a class="" data-bind="click:add">+ 新增</a>
                </td>
            </tr>
        </tfoot>
    </table>
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
        self.user_id = ko.observable(o.user_id);
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