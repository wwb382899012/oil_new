ko.components.register('check-items', {
    template: { element: 'component-template-check-items' },
    viewModel:checkItemsComponent
});

/**
 * 组件主ViewMode
 * @param params
 */
function checkItemsComponent(params)
{
    var self=this;
    self.items=params.items;//ko.observableArray();
    var oItems=params.items();


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
                break;
            default:
                self.items()[i]=new InputItemModel(item);
                break;
        }

    });

    self.getOptionText=function(options,value)
    {
        var s="";
        ko.utils.arrayForEach(options,function (op) {
            if(value==op.value())
            {
                s=op.text();
                return s;
            }
        });
        return s;
    }

    self.items.getValues=function()
    {
        var extraValues={};
        ko.utils.arrayForEach(self.items(),function (item) {
            switch (item.type())
            {
                case "koSelectButtonsWithRemark":
                    var remark="";
                    if(remark!=undefined && remark!=null && remark!="")
                        remark=" 备注："+remark;
                    extraValues[item.key()] = {
                        name:item.name(),
                        displayValue:self.getOptionText(item.options(),item.value())+remark,
                        value:item.value(),
                        remark:item.remark()
                    };
                    break;
                case "koSelectButtons":
                case "koSelect":
                    extraValues[item.key()] = {
                        name:item.name(),
                        displayValue:self.getOptionText(item.options(),item.value()),
                        value:item.value()
                    };
                    break;
                default:
                    extraValues[item.key()] = {
                        name:item.name(),
                        displayValue:item.value(),
                        value:item.value()
                    };
                    break;
            }
        });
        return extraValues;
    }
}
