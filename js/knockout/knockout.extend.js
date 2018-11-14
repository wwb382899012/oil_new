ko.bindingHandlers.stopBinding = {
    init: function() {
        return { controlsDescendantBindings: true };
    }
};
ko.virtualElements.allowedBindings.stopBinding = true;
ko.bindingHandlers.money = {
	init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
	{
		var valueUnwrapped = ko.unwrap(valueAccessor());
        var obj=$(element);
        obj.val(valueUnwrapped/100);

        obj.bind('keyup',function(event){
            if(event.keyCode<37 || event.keyCode>40) {
                this.value = this.value.replace(/[^0-9.-]/g, '');
                var arr = this.value.split('.');
                if (arr[0] && arr[0].length > 15)
                    this.value = this.value.substring(0, 15);
                if (arr[1] && arr[1].length > 2)
                    this.value = this.value.substring(0, this.value.length - (arr[1].length - 2));
            }
        }).bind("blur", function () {
            this.value=this.value.replace(/[^0-9.-]/g, '');
            this.value=formatNumber(this.value);
        }).bind("focus", function () {
            this.value=this.value.replace(/[^0-9.-]/g, '');
        }).css("ime-mode", "disabled");

        obj.change(function ()
		{
			var value = valueAccessor();
			var v=obj.val();
            if(v!=null && !isNaN(v))
			    value(Math.round(parseFloat(v.replace(/[^0-9.-]/g, ''))*100));
            else
                value(0);
		});

        function formatNumber(v)
        {
            if(v!=null && !isNaN(v)) {
                var old_v = v;
                v = (Math.round(Math.abs(parseFloat(v)*100))/100).toString();
                var s = v.replace(/[^0-9.-]/g, '');
                s= s.replace(/^(\d*)$/,"$1.");
                s = s.replace(/^0/g, '');
                s = (s + "00").replace(/(\d*\.\d\d)\d*/, "$1");
                s = s.replace(".", ",");
                var re = /(\d)(\d{3},)/;
                while (re.test(s))
                    s = s.replace(re, "$1,$2");
                s = s.replace(/,(\d\d)$/, ".$1");
                s = s.replace(/^\./, "0.");
                if(old_v < 0)
                    s = '-' + s;
                return s;
            }
            return v;
        }

	},
	update: function (element, valueAccessor, allBindings, viewModel, bindingContext)
	{
		var valueUnwrapped = ko.unwrap(valueAccessor());
		$(element).val(valueUnwrapped/100).triggerHandler("blur");
	}
};
ko.bindingHandlers.moneyText={
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());
        var unit=100;
        ko.bindingHandlers.text.init(element, function() { return formatNumber(valueUnwrapped/unit);});

        function formatNumber(v)
        {
            if(v!=null && !isNaN(v)) {
                var old_v = v;
                v = Math.abs(parseFloat(v)).toString();
                var s = v.replace(/[^0-9.-]/g, '');
                s= s.replace(/^(\d*)$/,"$1.");
                s = s.replace(/^0/g, '');
                s = (s + "00").replace(/(\d*\.\d\d)\d*/, "$1");
                s = s.replace(".", ",");
                var re = /(\d)(\d{3},)/;
                while (re.test(s))
                    s = s.replace(re, "$1,$2");
                s = s.replace(/,(\d\d)$/, ".$1");
                s = s.replace(/^\./, "0.");
                if(old_v < 0)
                    s = '-' + s;
                return s;
            }
            return "";
        }
    },
    update: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var unit=100;
        //bindingContext.extend(valueAccessor);
        var valueUnwrapped = ko.unwrap(valueAccessor());
        ko.bindingHandlers.text.update(element, function() { return formatNumber(valueUnwrapped/unit);});

        function formatNumber(v)
        {
            if(v!=null && !isNaN(v)) {
                var old_v = v;
                v = Math.abs(parseFloat(v)).toString();
                var s = v.replace(/[^0-9.-]/g, '');
                s= s.replace(/^(\d*)$/,"$1.");
                s = s.replace(/^0/g, '');
                s = (s + "00").replace(/(\d*\.\d\d)\d*/, "$1");
                s = s.replace(".", ",");
                var re = /(\d)(\d{3},)/;
                while (re.test(s))
                    s = s.replace(re, "$1,$2");
                s = s.replace(/,(\d\d)$/, ".$1");
                s = s.replace(/^\./, "0.");
                if(old_v < 0)
                    s = '-' + s;
                return s;
            }
            return "";
        }
    }
}
ko.bindingHandlers.moneyChineseText={
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());
        var unit=100;
        ko.bindingHandlers.text.init(element, function() { return inc.moneyToChinese(valueUnwrapped/unit);});
    },
    update: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var unit=100;
        //bindingContext.extend(valueAccessor);
        var valueUnwrapped = ko.unwrap(valueAccessor());
        ko.bindingHandlers.text.update(element, function() { return inc.moneyToChinese(valueUnwrapped/unit);});
    }
}
ko.bindingHandlers.moneyWanText={
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());
        var unit=10000*100;
        ko.bindingHandlers.text.init(element, function() { return valueUnwrapped/unit;});
    },
    update: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var unit=10000*100;
        //bindingContext.extend(valueAccessor);
        var valueUnwrapped = ko.unwrap(valueAccessor());
        ko.bindingHandlers.text.update(element, function() { return valueUnwrapped/unit;});
    }
}
ko.bindingHandlers.moneyWanChineseText={
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());
        var unit=10000*100;
        ko.bindingHandlers.text.init(element, function() { return inc.moneyToChinese(valueUnwrapped/unit);});
    },
    update: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var unit=10000*100;
        //bindingContext.extend(valueAccessor);
        var valueUnwrapped = ko.unwrap(valueAccessor());
        ko.bindingHandlers.text.update(element, function() { return inc.moneyToChinese(valueUnwrapped/unit);});
    }
}
ko.bindingHandlers.moneyWan = {
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());
        var unit=10000*100;
        $(element).val(valueUnwrapped/unit);
        $(element).change(function ()
        {
            var value = valueAccessor();
            value($(element).val()*unit);
        });

    },
    update: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var unit=10000*100;
        var valueUnwrapped = ko.unwrap(valueAccessor());
        $(element).val(valueUnwrapped/unit);
    }
};

ko.bindingHandlers.percent = {
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());
        $(element).val(valueUnwrapped*100);
        $(element).change(function ()
        {
            var value = valueAccessor();
            value($(element).val()/100);
        });
    },
    update: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());
        var m=Math.pow(10, 6);
        $(element).val(parseInt(valueUnwrapped*m)/(m/100));
    }
};
ko.bindingHandlers.percentText={
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());
        ko.bindingHandlers.text.init(element, function() { return (valueUnwrapped*100).toString()+"%";});
    },
    update: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());
        ko.bindingHandlers.text.update(element, function() { return (valueUnwrapped*100).toString()+"%";});
    }
};
ko.bindingHandlers.numberFixed = {
	init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
	{
		var valueUnwrapped = ko.unwrap(valueAccessor());
		$(element).val(parseFloat(valueUnwrapped).toFixed(4));
		$(element).change(function ()
		{
			var value = valueAccessor();
			value(parseFloat($(element).val()).toFixed(4));
		});
	},
	update: function (element, valueAccessor, allBindings, viewModel, bindingContext)
	{
		var valueUnwrapped = ko.unwrap(valueAccessor());
		$(element).val(parseFloat(valueUnwrapped).toFixed(4));
	}
};
ko.bindingHandlers.numberFixedText={
	init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
	{
		var valueUnwrapped = ko.unwrap(valueAccessor());
		ko.bindingHandlers.text.init(element, function() { return inc.toDecimal(valueUnwrapped, 4)});
	},
	update: function (element, valueAccessor, allBindings, viewModel, bindingContext)
	{
		var valueUnwrapped = ko.unwrap(valueAccessor());
		ko.bindingHandlers.text.update(element, function() { return inc.toDecimal(valueUnwrapped, 4)});
	}
};

ko.bindingHandlers.date = {
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());
        $(element).datetimepicker({format: 'yyyy-mm-dd',minView: 'month'});
        $(element).val(valueUnwrapped);
        $(element).change(function ()
        {
            var value = valueAccessor();
            //console.log($(element).val());
            value($(element).val());
        });

    },
    update: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        bindingContext.extend(valueAccessor);
        var valueUnwrapped = ko.unwrap(valueAccessor());
        $(element).val(valueUnwrapped);
    }
};

ko.bindingHandlers.readonly = {
    init: function(element, valueAccessor) {
        //console.log(element.tagName);
        if((element.tagName.toLowerCase() == "input" && $(element).attr("type") == "text")
            || element.tagName.toLowerCase() == "textarea")
        {
            if(valueAccessor())
                element.setAttribute("readOnly","true");
            else
                element.removeAttribute("readOnly");
            //element.readOnly=valueAccessor()?true:false;
        }
    },
    update: function(element, valueAccessor) {
        if((element.tagName.toLowerCase() == "input" && $(element).attr("type") == "text")
            || element.tagName.toLowerCase() == "textarea")
        {
            if(valueAccessor())
                element.setAttribute("readOnly","true");
            else
                element.removeAttribute("readOnly");
            //element.readOnly=valueAccessor()?true:false;
        }
    }
};


ko.bindingHandlers.hidden = {
    update: function(element, valueAccessor) {
        var value = ko.utils.unwrapObservable(valueAccessor());
        ko.bindingHandlers.visible.update(element, function() { return!value; });
    }
};

ko.bindingHandlers.selectpicker = {
    after: ['options','selectPickerOptions'],
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());

        var v=valueUnwrapped;
        var enable=true;
        var jo = $(element);
        if (valueUnwrapped && typeof (valueUnwrapped) == "object" && valueUnwrapped.value)
        {
            v=ko.unwrap(valueUnwrapped.value);
            if(valueUnwrapped.hasOwnProperty("enable"))
            {
                enable= ko.unwrap(valueUnwrapped.enable);
            }
        }
        else
        {
            if(allBindings['has']('enable'))
            {
                if(!allBindings.get('enable'))
                    enable=false;
            }
        }
        if(!enable)
            jo.attr("disabled","disabled");
        /*if(!enable)
            jo.data("enable",enable);*/
        // liveSearch: true,
        var option = { multipleSeparator: ',', width:  "100%", size: 8 };
        if (jo.attr("multiple") != null)
            option = {  multipleSeparator: ',', width: "100%", actionsBox: true, deselectAllText: '取消全选', selectAllText: '全选', size: 8 };
        option = allBindings.get('selectPickerOption') || option;

        //ko.bindingHandlers.select.init(element, valueUnwrapped.value);
        jo.selectpicker(option);
        if (v != null && v != "")
        {
            jo.selectpicker('val', v.toString().split(','));
        }

        jo.change(function ()
        {
            var value = valueAccessor();
            if (value && typeof (value) == "object" && value.value)
                value.value($(element).val());
            else
                value($(element).val());
        });

    },
    update: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());

        var v=valueUnwrapped;

        var enable=true;
        if (valueUnwrapped && typeof (valueUnwrapped) == "object" && valueUnwrapped.value)
        {
            v=ko.unwrap(valueUnwrapped.value);
            enable= ko.unwrap(valueUnwrapped.enable);
        }
        else
        {
            if(allBindings['has']('enable'))
            {
                if(!ko.unwrap(allBindings.get('enable')))
                    enable=false;
            }
        }
        //console.log(ko.unwrap(allBindings.get('enable')));

        var jo=$(element);

        if(v==null || v=="")
            v=[];

        if (typeof (v) != "object")
            v=v.toString().split(',');

        if(jo.data("value")!=v)
        {
            jo.selectpicker('val', v);
            jo.data("value",v);
        }

        if(jo.data("enable")!=enable)
        {
            jo.data("enable",enable);
            if(enable)
                jo.removeAttr("disabled");
            else
                jo.attr("disabled","disabled");
            jo.selectpicker('refresh');
        }
    }
};
ko.bindingHandlers.selectPickerOptions = {
    init: function (element)
    {
        ko.bindingHandlers['options'].init(element);
    },
    update: function(element, valueAccessor, allBindings) {

        ko.bindingHandlers['options'].update(element, valueAccessor, allBindings);
        if($(element).prev(".bootstrap-select").length>0 || $(element).next(".bootstrap-select").length>0)
            $(element).selectpicker('refresh');
    }
};

ko.bindingHandlers.slideToggle= {
    update: function(element, valueAccessor) {
        var v=ko.utils.unwrapObservable(valueAccessor());  //unwrap to get subscription
        if(v)
            $(element).slideDown(2000);
        else
            $(element).slideUp(2000);
    }
};

ko.bindingHandlers.bankInput = {
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());

        $(element).val(valueUnwrapped);
        $(element).bankInput({min: 1, max: 50, deimiter: ' '});
        $(element).on("change",function ()
        {
            updateValue();
        }).on("blur",function ()
        {
            updateValue();
        });

        function updateValue()
        {
            var value = valueAccessor();
            value($(element).val().replaceAll(' ',''));
        }

    },
    update: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());
        if ($(element).val() != valueUnwrapped)
            $(element).val(valueUnwrapped);
    }
};


ko.bindingHandlers.htmlEditable = {
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());
        element.contentEditable =true;
        $(element).html(valueUnwrapped);
        $(element).on("input.htmlEditable",function(){
            if ($(element).html() != ko.unwrap(valueAccessor()))
            {
                var value = valueAccessor();
                value($(element).html());
            }

        });
        $(element).on("paste.htmlEditable", function (e)
        {
            e.preventDefault();
            var pastedText = "";
            if (window.clipboardData && window.clipboardData.getData)
            { // IE
                pastedText = window.clipboardData.getData('Text');
            } else
            {
                pastedText = e.originalEvent.clipboardData.getData('Text');//e.clipboardData.getData('text/plain');
            }
            pastedText = pastedText.replace(/<\/?[^>]*>/g, '');
            pastedText = pastedText.replace(/[ | ]*\n/g, '');
            pastedText = pastedText.replace("<br>", '');
            document.execCommand("insertText", false, pastedText);
        });
        //handle disposal (if KO removes by the template binding)
        ko.utils.domNodeDisposal.addDisposeCallback(element, function ()
        {
            $(element).off("input.htmlEditable");
            $(element).off("paste.htmlEditable");
        });
    },
    update: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var valueUnwrapped = ko.unwrap(valueAccessor());
        if ($(element).html() != valueUnwrapped)
            $(element).html(valueUnwrapped);

    }
};


/**
 * 文件上传的绑定
 *  绑定方式：data-bind="fileUpload:true,url:postUrl,add:addFunction,done:doneFunction"
 * @type {{init: ko.bindingHandlers.fileUpload.init}}
 */
ko.bindingHandlers.fileUpload = {
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
		element.addEventListener('dragenter',function(e){
            $(element).parent().parent().css({'background-color':'#aaa','color':'#fff','border-color':'#dcdcdc'})
        })
		element.addEventListener('dragleave',function(e){
			$(element).parent().parent().css({'background-color':'','color':'','border-color':''})
        },true)
		element.addEventListener('drop',function(e){
			$(element).parent().parent().css({'background-color':'','color':'','border-color':''})
        },true)
        var addFunc=allBindings.get('add');
        var doneFunc=allBindings.get('done');
        var failFunc=allBindings.get('fail');
        var progressFunc=allBindings.get('progress');
        var config={
            dataType: "json",
            autoUpload: true,
            dropZone:$(element).parent().parent(),
            progressall: function (e, data) {
                if(progressFunc)
                    progressFunc(e,data);
            },
            add: function (e, data) {
                if(addFunc)
                    addFunc(e,data);
            },
            done: function (e, data) {
                if(doneFunc)
                    doneFunc(e,data);
            },
            fail:function (e) {
                if(failFunc)
                    failFunc(e);
            }
        };
        config["url"] = ko.unwrap(allBindings.get('url'));
        $(element).fileupload(config);
    }
};


ko.bindingHandlers.typeahead = {
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        var source=allBindings.get('source');
        var elem = $(element);
        var value = valueAccessor();

        elem.typeahead({
            showHintOnFocus:true,
            source: function(query, process) {
                if(source)
                {
                    if($.isArray(source) || ko.isObservable(source))
                        return process(ko.unwrap(source));
                    else
                        source(query, process);
                }
                else
                    return [];
            },
            onselect: function(val) { value(val); }
        });

        ko.bindingHandlers['value'].init (element, valueAccessor, allBindings, viewModel, bindingContext);
    },
    update: function (element, valueAccessor, allBindings, viewModel, bindingContext)
    {
        ko.bindingHandlers['value'].update(element, valueAccessor, allBindings, viewModel, bindingContext);
    }
};


/**
 *
 * 通过js对象更新viewModel中的值
 * @param viewModel
 * @param data
 */
ko.setValue=function(viewModel,data)
{
    var properties = Object.keys(viewModel);

    for(var i=0;i<properties.length;i++)
    {
        if(data.hasOwnProperty(properties[i]))
            viewModel[properties[i]](data[properties[i]]);
    }
}

/**
 * 判断属性是否是ko.observable对象
 * @param instance
 * @returns {boolean}
 */
/*ko.isObservable = function(instance) {
    if(instance == null || instance == undefined || instance.__ko_proto__ == undefined) return false;
    if(instance.__ko_proto__ == ko.observable) return true;
    return ko.isObservable(instance.__ko_proto__);
}*/
/**
 * 判断ko.observable对象及js数据的属性，如果一致且都存在，则赋值f
 * @param koObject
 * @param data
 */
ko.setObservablesValue = function(koObject,data) {
    for (prop in koObject) {
        if(ko.isObservable(koObject[prop]) && data.hasOwnProperty(prop))
        {
            koObject[prop](data[prop]);
            if (ko.validation.utils.isValidatable(koObject[prop]))
                koObject[prop].isModified(false);
        }
    }
}

/***************************************************************************
 扩展验证代码
 ***************************************************************************/

ko.validation.makeBindingHandlerValidatable("money");
ko.validation.makeBindingHandlerValidatable("moneyWan");
ko.validation.makeBindingHandlerValidatable("percent");
ko.validation.makeBindingHandlerValidatable("date");
ko.validation.makeBindingHandlerValidatable("selectpicker", function (element) {return  $("element").next(".bootstrap-select"); });
ko.validation.makeBindingHandlerValidatable("bankInput");
ko.validation.makeBindingHandlerValidatable("htmlEditable");
ko.validation.makeBindingHandlerValidatable("numberFixed");
//ko.validation.makeBindingHandlerValidatable("typeahead");
// ko.validation.makeBindingHandlerValidatable("percentText");

ko.validation.init({
    //insertMessages: true,
    //decorateElement: true,
    grouping: {
        deep: true
    },
    decorateInputElement:true //添加验证错误的输入框的样式

});

ko.validation.localize({
    required: '不得为空',
    min: '输入值必须大于等于 {0}',
    max: '输入值必须小于等于 {0}',
    minLength: '至少输入 {0} 个字符',
    maxLength: '输入的字符数不能超过 {0} 个',
    pattern: '请检查此值',
    step: '每次步进值是 {0}！',
    email: '请输入一个正确的email',
    date: '请输入一个正确的日期！',
    dateISO: '请输入一个正确的日期！',
    number: '请输入一个数字',
    digit: '请输入一个数字',
    phoneUS: '请输入一个合法的手机号(US)！',
    equal: '输入值必须一样！',
    notEqual: '请选择另一个值！',
    unique: '此值应该是唯一的！'
});


//判断输入的数字是否是正数
ko.validation.rules['positiveNumber'] = {
    validator: function (val) {
        if(parseFloat(val)==val && val > 0 && parseFloat(val)<Math.pow(10,17))
            return true;
    },
    message: "请输入一个大于0的数字且小于10^15的数字"
};

ko.validation.rules['positiveInt'] = {
    validator: function (val) {
        if(val==null || isNaN(val))
            return false;
        if(val.legnth>20)
            return false;
        return /^\+?[1-9][0-9]*$/.test(val);
    },
    message: "请输入一个大于0的整数"
};
ko.validation.rules['int'] = {
    validator: function (val) {
        return /^-?\d+$/.test(val);
    },
    message: "请输入一个整数"
};
ko.validation.rules['intN0'] = {
    validator: function (val) {
        return /^\d+$/.test(val);
    },
    message: "请输入一个非负数"
};
ko.validation.rules['notNegative'] = {
    validator: function (val) {
        return /^\d+(\.\d+)?$/.test(val);
    },
    message: "请输入一个非负数"
};
ko.validation.rules['select'] = {
    validator: function (val) {
        if(parseInt(val)==val && val > 0)
            return true;
    },
    message: "请选择正确的项"
};

ko.validation.rules['isNotNull'] = {
    validator: function (val) {
        if(val != '' && val != null)
            return true;
    },
    message: "不得为空"
};

//判断输入的数字是否是正数
ko.validation.rules['isNullVal'] = {
    validator: function (val) {
        if(val > 0)
            return true;
    },
    message: "不得为空"
};

//验证实数整数位的个数
ko.validation.rules['intLength'] = {
    validator: function (val, option) {
        if (val == null || val == undefined || val == "")
            return false;
        var reg = new RegExp("^[1-9]{1}[0-9]{0," + (option - 1).toString() + "}$|^[1-9]{1}[0-9]{0," + (option - 1).toString() + "}[.]([0-9]*)?$");
        return reg.test(val.toString());
    },
    message: "只能是至多含有 {0} 位整数的非负数字"
};
//验证实数小数位的个数
ko.validation.rules['decimalLength'] = {
    validator: function (val, option) {
        var reg = new RegExp("^([0-9])+(.[0-9]{0," + option + "})?$");
        return (val == null || val == undefined || val == "" || reg.test(val.toString()));
    },
    message: "只能是至多含有 {0} 位小数的非负数字"
};
//自定义验证，传入的参数为一个返回值为true或false的函数，默认提示是“错误”
ko.validation.rules['custom'] = {
    validator: function (val, func) {
        return func(val);
    },
    message: "错误！"
};
ko.validation.rules['idCard'] = {
    validator: function (val) {
        if(val==null || val=="")
            return true;
        var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
        ///^[0-9]{17}[0-9xX]{1}$/
        return reg.test(val);
    },
    message: "不是正确的身份证号码"
};
ko.validation.rules['phone'] = {
    validator: function (val) {
        if(arguments[1])
        {
            if(typeof (arguments[1]) === "function")
            {
                if(arguments[1]())
                    return true;
            }
        }

        if(val==null || val=="")
            return true;

        //var reg = /^(1[0-9]{10})$/;
        var reg = /^(1[35847]\d{9})$/;
        return reg.test(val);
    },
    message: "不是正确的手机号码"
};

//电话号码校验：支持手机号码，3-4位区号，7-8位直播号码，1－4位分机号
ko.validation.rules['telephone'] = {
	validator: function (val) {
		if(arguments[1])
		{
			if(typeof (arguments[1]) === "function")
			{
				if(arguments[1]())
					return true;
			}
		}

		if(val==null || val=="")
			return true;

		var reg = /((\d{11})|^((\d{7,8})|(\d{4}|\d{3})-(\d{7,8})|(\d{4}|\d{3})-(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1})|(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1}))$)/;
		return reg.test(val);
	},
	message: "不是正确的电话号码"
};

ko.validation.rules['money'] = {
    validator: function (val) {
        if(val==null)
            return false;
        if(parseFloat(val)==0)
            return true;
        if(parseFloat(val)>=Math.pow(10,17))
            return false;

        var reg = /^\d+(\.{0,1}\d+){0,1}$/;
        return reg.test(val);
    },
    message: "请输入一个大于0的数字且小于10^15的数字"
};

ko.validation.rules['moneyNZ'] = {
    validator: function (val) {
        if(val==null || isNaN(val))
            return false;
        if(val.legnth>20)
            return false;
        return /^\+?[1-9][0-9]*$/.test(val);
    },
    message: "请输入一个大于0的数字"
};

ko.validation.registerExtenders();


ko.subscribable.fn.subscribeChanged = function (callback) {
    var oldValue;
    this.subscribe(function (_oldValue) {
        oldValue = _oldValue;
    }, this, 'beforeChange');

    this.subscribe(function (newValue) {
        callback(newValue, oldValue);
    });
};

ko.extenders.numeric = function(target, precision) {
    //create a writable computed observable to intercept writes to our observable
    var result = ko.pureComputed({
        read: function(){
            var v=target();
            if(!isNaN(v))
                return v;
            else
                return 0;
        },  //always return the original observables value
        write: function(newValue) {//parseFloat(newValue.replace(/[^\0-9\.]/g,''))
            var current = target(),
            roundingMultiplier = Math.pow(10, precision),
            newValueAsNum = isNaN(newValue) ? 0 : +newValue,
            valueToWrite = Math.round(newValueAsNum * roundingMultiplier) / roundingMultiplier;

            //only write if it changed
            if (valueToWrite !== current) {
                target(valueToWrite);
            } else {
                //if the rounded value is the same, but a different value was written, force a notification for the current field
                if (newValue !== current) {
                    target.notifySubscribers(valueToWrite);
                }
            }
        }
    }).extend({ notify: 'always' });

    //initialize with current value to make sure it is rounded appropriately
    result(target());

    //return the new computed observable
    return result;
};


ko.isObservableArray=function (instance) {
    return ko.isObservable(instance) && instance["removeAll"]!=null;
}

