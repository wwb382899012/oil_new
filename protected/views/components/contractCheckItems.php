<?php include("koForm.php"); ?>
<template id='component-template-contract-check-items' >
    <!--ko foreach:items -->
    <div class="form-group">
        <div class="col-sm-12">
            <h4>
                <span class="text-red fa fa-asterisk" data-bind="visible:required"></span>
                <span data-bind="text:$index()+1"></span>、
                <span data-bind="text:name"></span>
            </h4>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
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
    ko.components.register('contract-check-items', {
        template: { element: 'component-template-contract-check-items' },
        viewModel:contractCheckItemsComponent
    });

    /**
     * 组件主ViewMode
     * @param params {items:observableArray}
     */
    function contractCheckItemsComponent(params)
    {
        var self=this;
        self.items=params.items;//ko.observableArray();

        ko.utils.arrayForEach(self.items(), function(item,i) {
            switch (item.type)
            {
                case "koSelectButtonsWithRemark":
                    self.items()[i]=new SelectButtonsWithRemarkItemModel(item);
                    break;
                case "koSelectButtons":
                    self.items()[i]=new SelectButtonsItemModel(item);
                    break;
                case "koSelect":
                    self.items()[i]=new SelectItemModel(item);
                    //self.items.push(new SelectItemModel(item));
                    break;
                default:
                    self.items()[i]=new InputItemModel(item);
                    //self.items.push(new InputItemModel(item));
                    break;
            }

        });

        
    }

</script>