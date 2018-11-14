
ko.components.register('koSelectButtons', {
    template: { element: 'component-template-ko-select-buttons' },
    viewModel:itemComponent
});
ko.components.register('koTextArea', {
    template: { element: 'component-template-ko-text-area' },
    viewModel:itemComponent
});
ko.components.register('koInput', {
    template: { element: 'component-template-ko-input' },
    viewModel:itemComponent
});
ko.components.register('koSelect', {
    template: { element: 'component-template-ko-select' },
    viewModel:itemComponent
});
ko.components.register('koMultipleSelect', {
    template: { element: 'component-template-ko-multiple-select' },
    viewModel:itemComponent
});
ko.components.register('koSelectButtonsWithRemark', {
    template: { element: 'component-template-ko-select-buttons-input' },
    viewModel:itemComponent
});

/**
 * 单项的ViewModel
 * @param params
 */
function itemComponent(params)
{
    var self=this;
    self.model=params.item;
}

/**
 * 单选按钮组
 * @param data
 * @constructor
 */
function SelectButtonsItemModel(data)
{
    var defaults={
        value:-1
    };
    var o=$.extend(defaults,{});
    var self=this;
    ko.mapping.fromJS(data, {'ignore': ["min_value"]}, this);
    if(!self.value)
        self.value=ko.observable(o.value);

    self.minValue=0;
    if(data.hasOwnProperty("min_value"))
        self.minValue=data.min_value;

    if(self.required())
        self.value.extend({custom:{
            params: function (v) {
                return self.value()>=self.minValue;
            },
            message: "请选择"
        }});

    self.select=function (item) {
        self.value(item.value());
    }
    ko.utils.arrayForEach(self.options(), function(item) {
        item.isSelected=ko.computed(function () {
            return self.value()===item.value();
        });
        item.selectedCss=ko.computed(function () {
            if(this.isSelected())
            {
                if(this.css)
                    return ko.unwrap(this.css);
                else
                    return "btn-success";
            }
            else
                return "btn-default";
        },item);
    });
    self.value.subscribe(function (v) {
        data.value=v;
    });

    self.isValid=ko.computed(function () {
        return (!self.value.isValid) || self.value.isValid();
        //return (!self.required() || self.value()>-1);
    });
}

/**
 * 文本框
 * @param data
 * @constructor
 */
function InputItemModel(data)
{
    var defaults={
        value:""
    };
    var o=$.extend(defaults,{});
    var self=this;
    ko.mapping.fromJS(data, {}, this);

    if(!self.value)
        self.value=ko.observable(o.value);
    if(self.required())
        self.value.extend({required:true});

    self.value.subscribe(function (v) {
        data.value=v;
    });

    self.isValid=ko.computed(function () {
        return (!self.value.isValid) || self.value.isValid();
        //return (!self.required() || $.trim(self.value())!="");
    });
}

/**
 * 下拉单选
 * @param data
 * @constructor
 */
function SelectItemModel(data)
{
    var defaults={
        value:-1
    };
    var o=$.extend(defaults,{});
    var self=this;
    ko.mapping.fromJS(data, {
        'ignore': ["items","min_value","options_caption"]
    }, this);
    self.minValue=0;
    if(data.hasOwnProperty("min_value"))
        self.minValue=data.min_value;

    self.optionsCaption="请选择";
    if(data.hasOwnProperty("options_caption"))
        self.optionsCaption=data.options_caption;

    self.selectOptions=ko.computed(function () {
        if(data.items)
            return inc.objectToArray(data.items);
        else
            return [];
    });

    if(!self.value)
        self.value=ko.observable(o.value);
    if(self.required())
        self.value.extend({custom:{
            params: function (v) {
                return null!=ko.utils.arrayFirst(self.selectOptions(),function(item){return item.id == v; });
            },
            message: self.optionsCaption
        }});
    self.isValid=ko.computed(function () {
        return (!self.value.isValid) || self.value.isValid();
        //return (!self.required() || (self.value()!=null && self.value()>-1));
    });



    self.value.subscribe(function (v) {
        data.value=v;
    });
}


/**
 * 下拉多选
 * @param data
 * @constructor
 */
function SelectMultipleItemModel(data)
{
	data.value = $.isArray(data.value) ? data.value.join(',') : data.value;
	var defaults={
        value:""
    };
    var o=$.extend(defaults,{});
    var self=this;
    ko.mapping.fromJS(data, {
        'ignore': ["items","options_caption"]
    }, this);

    self.optionsCaption="请选择";
    if(data.hasOwnProperty("options_caption"))
        self.optionsCaption=data.options_caption;

    if(!self.value)
        self.value=ko.observable(o.value);
    if(self.required())
        self.value.extend({custom:{
            params: function (v) {
                return $.isArray(v) || (v != null &&  v != '');
            },
            message: self.optionsCaption
        }});
    self.isValid=ko.computed(function () {
        return (!self.value.isValid) || self.value.isValid();
        //return (!self.required() || self.value()>-1);
    });

    self.selectOptions=ko.computed(function () {
        if(data.items)
            return inc.objectToArray(data.items);
        else
            return [];
    });

    self.value.subscribe(function (v) {
        if($.isArray(v) && v.length > 0) {
			ko.utils.arrayRemoveItem(v, "");
		}
        data.value=v;
    });
}

/**
 * 单选按钮组加备注
 * @param data
 * @constructor
 */
function SelectButtonsWithRemarkItemModel(data)
{
    var defaults={
        value:-1
    };
    var o=$.extend(defaults,{});
    var self=this;
    ko.mapping.fromJS(data, {'ignore': ["min_value"]}, this);
    if(!self.value)
        self.value=ko.observable(o.value);

    if(!self.remark)
        self.remark=ko.observable(o.remark);

    self.minValue=0;
    if(data.hasOwnProperty("min_value"))
        self.minValue=data.min_value;

    if(self.required())
        self.value.extend({custom:{
            params: function (v) {
                return null!=ko.utils.arrayFirst(ko.unwrap(self.options),function(item){return ko.unwrap(item.value) == v; });
                // return self.value()>=self.minValue;
            },
            message: "请选择"
        }});


    if(!!self.remark_required)
    {
        self.remark.extend({custom:{
            params: function (v) {
                if(v!=null && v!="")
                    return true;
                return null==ko.utils.arrayFirst(ko.unwrap(self.remark_required),function(item){return ko.unwrap(item) == self.value(); });
            },
            message: "请填写备注"
        }});
    }
    self.select=function (item) {
        self.value(item.value());
    }
    ko.utils.arrayForEach(self.options(), function(item) {
        item.isSelected=ko.computed(function () {
            return self.value()===item.value();
        });

        item.selectedCss=ko.computed(function () {
            if(this.isSelected())
            {
                if(this.css)
                    return ko.unwrap(this.css);
                else
                    return "btn-success";
            }
            else
                return "btn-default";
        },item);
    });
    self.value.subscribe(function (v) {
        data.value=v;
    });

    self.isValid=ko.computed(function () {
        return (!self.value.isValid) || self.value.isValid();
        //return (!self.required() || self.value()>-1);
    });
}

var koForm=new Object();
koForm.getValues = function(data) {
	var extraValues=[];
	ko.utils.arrayForEach(data,function (obj) {
		switch (ko.unwrap(obj.type)) {
			case "koSelectButtonsWithRemark":
				var remark="";
				if(remark!=undefined && remark!=null && remark!="")
					remark=" 备注："+remark;
				extraValues.push({
					key:ko.unwrap(obj.key),
					name:ko.unwrap(obj.name),
					display_value:koForm.getOptionText(ko.unwrap(obj.options),ko.unwrap(obj.value))+remark,
					value:ko.unwrap(obj.value),
					remark:ko.unwrap(obj.remark)
				});
				break;
			case "koSelectButtons":
            case "koMultipleSelect":
			case "koSelect":
				extraValues.push({
					key:ko.unwrap(obj.key),
					name:ko.unwrap(obj.name),
					display_value:koForm.getOptionText(ko.unwrap(obj.selectOptions),ko.unwrap(obj.value)),
					value:ko.unwrap(obj.value)
				});
				break;
			default:
				extraValues.push({
					key:ko.unwrap(obj.key),
					name:ko.unwrap(obj.name),
					display_value:ko.unwrap(obj.value),
					value:ko.unwrap(obj.value)
				});
				break;
		}
	});
	return extraValues;
}

koForm.getOptionText=function(options,value) {
    var vals = (typeof value == 'string' && value != '' && value != 0) ? value.split(',') : ($.isArray(value) ? value : []);
	var s="";
	if($.isArray(vals) && vals.length > 0) {
	    ko.utils.arrayForEach(vals, function (val) {
	        if(s != '') {
	            s += ',';
            }
            s += options[val-1].name;
		})
    }
	return s;
}
