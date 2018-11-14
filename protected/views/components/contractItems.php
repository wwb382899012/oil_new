<?php include("koForm.php"); ?>
<template id='component-template-contract-items' >
    <!--ko foreach:items -->
    <!-- <div class="form-group">
        <label class="control-label" data-bind="css:$parent.labelWidth"><span data-bind='html:name'></span>
            <span class="text-red fa fa-asterisk" data-bind="visible:required"></span>
        </label> -->
    <div class="form-group form-group-custom form-group">
        <label for="type" class="col-lg-3 col-xl-2 control-label control-label-custom" data-bind="css:$parent.labelWidth">
            <span class="label-custom__span-red" data-bind="visible:required">*</span>
            <span data-bind='html:name'></span>
        </label>
        <div data-bind="css:$parent.controlWidth" class="col-lg-9 col-xl-10">
            <!-- ko component: {
                name: type,
                params: {
                            item: $data
                        }
            } -->
            <!-- /ko -->
        </div>
    </div>
    <!-- /ko -->
</template>
<script>
    ko.components.register('contract-items', {
        template: { element: 'component-template-contract-items' },
        viewModel:contractItemsComponent
    });

    /**
     * 组件主ViewMode
     * @param params {type:observable,category:observable,config:{}}
     */
    function contractItemsComponent(params)
    {
        var self=this;
        //self.config=params.config;
        self.type=params.type;
        self.category=params.category;
        self.labelWidth='';
        self.controlWidth='';
        self.type.subscribeChanged(function (newVal, oldVal) {
            params.config[oldVal][self.category()]["extraItems"]=null;
        });
        self.category.subscribeChanged(function (newVal, oldVal) {
            params.config[self.type()][oldVal]["extraItems"]=null;
        });

        self.items = ko.computed(function () {
            if (self.type() > 0 && self.category() > 0)
            {

                if(!params.config[self.type()][self.category()]["extraItems"])
                {
                    var extra = params.config[self.type()][self.category()].extra;
                    var extraItems=inc.objectToArray(extra);
                    var items=[];
                    ko.utils.arrayForEach(extraItems, function(item) {
                        switch (item.type)
                        {
                            case "koSelectButtons":
                                items.push(new SelectButtonsItemModel(item));
                                break;
                            case "koSelect":
                                items.push(new SelectItemModel(item));
                                break;
                            case "koMultipleSelect":
                                items.push(new SelectMultipleItemModel(item));
                                break;
                            default:
                                items.push(new InputItemModel(item));
                                break;
                        }
                    });
                    params.config[self.type()][self.category()]["extraItems"]=items;
                }
                return params.config[self.type()][self.category()]["extraItems"];
            }
            else
            {
                return [];
            }
        }, self);
    }

</script>