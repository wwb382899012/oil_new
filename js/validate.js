function validateUrl(url){
	 if (url.length==0){
		return true;
	 }
	 var strRegex = "^((https|http|ftp|rtsp|mms)://)?[a-z0-9A-Z]*(\.)?[a-z0-9A-Z][a-z0-9A-Z]{0,61}?[a-z0-9A-Z]\.com|net|cn|cc (:s[0-9]{1-4})?/$";
	 var re = new RegExp(strRegex);
	 return re.test(url);
}

function validateRequired(text){
	return text?true:false;
}

function validateMaxLength(text,length){
	return text.length>length?false:true;
}

function  validateMinLength(text,length){
        return text.length<length?false:true;
}

function validateNumber(text){
	var strRegex = "^([1-9][0-9]*|[0-9]{1})[\.]{0,1}[0-9]*$";
	var re = new RegExp(strRegex);
	return re.test(text);
}

function validateInteger(text){
	var strRegex = "^[0-9]*$";
	var re = new RegExp(strRegex);
	return re.test(text);
}

function validatePhone(text){
	var strRegex = "^1[3|4|5|8][0-9]{9}$";
	var re = new RegExp(strRegex);
	return re.test(text);
}

var passwordRule = {
	maxLength:16,
	minLength:8,
}



var messages = {
	maxLength:"最大长度不能超过{1}",
	url:"URL格式验证错误",
	required:"内容不能为空",
	minLength:"最小长度不能低于{1}",
	number:'输入必须为数字',
	phone:'手机号格式验证错误',
	integer:'输入必须为整数',
};


function setError(val,text){
	$("#"+(val.attr("id")+"Error")).text( text);
	val.parent().parent().removeClass("has-success").addClass("has-error");
}

function setSucc(val){
	$("#"+(val.attr("id")+"Error")).text("");
        val.parent().parent().removeClass("has-error").addClass("has-success");
}


function validateInput($input){
	var rules = $input.attr("rule");
	var length = $input.attr("length");
	var text = $input.val();
	var res = true;
	if (rules&&rules.length>0){
		var ruleArray = rules.split(" ");
		for(var i=0;i<ruleArray.length;i++){
			switch(ruleArray[i]){
				case "url":
					if (!validateUrl(text)){
						setError($input,messages.url);
						return false;
					}
					break;
				case "maxLength":
					if (!validateMaxLength(text,length)){
						setError($input,messages.maxLength.replace("{1}",length));
						return false;
					}
					break;
				case "required":
					if (!validateRequired(text)){
						setError($input,messages.required);
						return false;
					}
					break;                                                                                                                
				case "password":
					if (!validateMaxLength(text,passwordRule.maxLength)){
						setError($input,messages.maxLength.replace("{1}",passwordRule.maxLength));
						return false;
					}
					if (!validateMinLength(text,passwordRule.minLength)){
						setError($input,messages.minLength.replace("{1}",passwordRule.minLength));
						return false;
					}
					break;
				case "minLength":
					if (!validateMinLength(text,length)){
						setError($input,messages.minLength.replace("{1}",length));
						return false;
					}
				case "number":
					if (!validateNumber(text)){
						setError($input,messages.number);
						return false;
					}
					break;
				case "phone":
					if (!validatePhone(text)){
						setError($input,messages.phone);
						return false;
					}
					break;
				case "integer":
					if (!validateInteger(text)){
						setError($input,messages.integer);
						return false;
					}
					break;
				default:                                                                                                                      
					break;                                                                                                                
			}        
		}           	
	}
	setSucc($input);	
	return true;
}

function validateForm(formId){
	var res = true;
	$("#"+formId).find(".form-control").each(function(){
		if (!validateInput($(this))){
			res=false;
		}
	});
	return res;
}

