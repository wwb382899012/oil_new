<template id='component-template-file-upload'>
    <div class="upload-new-com">
        <div data-bind="visible:!status()">
            <div class="z-btn-action fileinput-button fileinput-button-first">
                <span class="btn-text">
                    <span data-bind="html:btnText">上传文件</span>
                    <input id="file-input-1" type="file" data-bind="fileUpload:true,url:postUrl,add:addFunction,done:doneFunction,progress:progressFunction"/>
                </span>
            </div>
            <div class="progress">
                <div class="bar"></div>
            </div>
            <span class="file-rules-text btn-text-desc">只能上传<span data-bind="html:fileTypeDesc"></span>格式文件，文件不能超过<span data-bind="html:maxSize"></span>M</span>
        </div>
        <div class="clearfix"></div>
        <ul class="list-unstyled " data-bind="foreach:files">
            <li>
                <div class="file-wrap">
                    <a class="text-link" target="_blank" data-bind="attr: { href: url, title: fileName },html:fileName" ></a>
                </div>
                <a href="javascript:void(0)" class="z-btn-action del-btn" data-bind="click:$parent.del">
                    <i class="icon icon-shanchu1"></i> 删除
                </a>
                <a href="javascript:void(0)" target="_blank" class="z-btn-action upload-btn" data-bind="attr: { href: url, title: fileName }" >
                    <i class="icon icon-xiazai"></i> 下载
                </a>
                <!-- ko if: $index()==0 -->
                <span class="fileinput-button upload-continu-wrap">
                    <a href="javascript:void(0)" class="z-btn-action upload-continu-btn">
                        <i class="icon icon-xinzeng"></i>
                        <span class="btn-text" data-bind="html:$parent.btnText">继续上传</span>
                        <input   type="file" data-bind="fileUpload:true,url:$parent.postUrl,add:$parent.addFunction,done:$parent.doneFunction,progress:$parent.progressFunction"/>
                    </a>
                </span>
                <!-- /ko -->
            </li>
        </ul>
    </div>
</template>
<script>
    ko.components.register('file-upload', {
        template: { element: 'component-template-file-upload' },
        viewModel:uploadComponent
    });
    function FileItem(option)
    {
        var defaults={
            id:0,
            isDone:0,
            fileName:"",
            url:""
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.id=ko.observable(o.id);
        self.fileName=ko.observable(o.fileName);
        self.isDone=ko.observable(o.isDone);
        self.url=ko.observable(o.url);

    }
    function uploadComponent(params)
    {
        var self=this;

        self.isMulti=ko.observable(false);//是否是多文件上传
        if(params.uploadFiles!=null && ko.isObservable(params.uploadFiles))
        {
           self.targetFiles=params.uploadFiles;
           if(!params.files)
               params.files=inc.arrayClone(params.uploadFiles());
        }
        else
            self.targetFiles=ko.observableArray();

        self.files=ko.observableArray();
        if(params.status)
            self.status=params.status;
        else
            self.status=ko.observable();
        self.files.subscribe(function () {
            if(self.files().length>0)
                self.status(1);
            else
                self.status(0);
            
            self.syncFiles();
        });

        self.syncFiles=function () {
            if(ko.isObservableArray(self.targetFiles))
            {
                self.targetFiles.removeAll();
                ko.utils.arrayForEach(self.files(),function (f) {
                    self.targetFiles.push(f);
                });
            }
            else
            {
                self.targetFiles(inc.arrayClone(self.files()));
            }
        }

        self.controller=params.controller;

        self.postUrl="/"+self.controller+"/saveFile/";//文件提交地址

        self.fileType=params.fileConfig.fileType;//允许上传的文件类型
        self.fileTypeDesc = params.fileConfig.fileTypeDesc || self.fileType;
        self.fileTypeDesc = self.fileTypeDesc.replaceAll(new RegExp(/\|/g),"、");
        self.fileTypeDesc = self.fileTypeDesc.substring(0,self.fileTypeDesc.length-1);
        if(self.fileTypeDesc.indexOf("、")==0)
            self.fileTypeDesc = self.fileTypeDesc.substring(1);
        self.maxSize=params.fileConfig.maxSize;//允许上传的文件大小最大值，单位M
        if(params.fileConfig.multi)
            self.isMulti(true);

        //文件上传额外参数
        /*self.params={
            id:ko.unwrap(params.baseId),
            type:params.fileConfig.id
        };*/
        self.params=$.extend({},params.fileParams, {type:params.fileConfig.id});

        self.getReadFileUrl=function (id, fileName) {
            return '/'+self.controller+'/getFile/?id='+id+'&fileName='+fileName;
        }

        self.btnText=ko.observable("上传文件");

        self.checkFileType=function (fileName) {
            if (!inc.checkFileType(fileName, ko.unwrap(self.fileType))) {
                // alert("只能上传指定类型的文件：" + ko.unwrap(self.fileType));
                // inc.vueAlert('只能上传指定类型的文件：' + ko.unwrap(self.fileTypeDesc));
                inc.vueAlert('只能上传指定类型的文件：' + ko.unwrap(self.fileTypeDesc) + '格式文件，文件不能超过' + ko.unwrap(self.maxSize) + 'M');
                return false;
            }
        }
        self.addFile=function (file) {
            if(!self.isMulti())
                self.files.removeAll();
            self.files.push(file);
        }
        
        self.del=function (file) {
            var formData = "id="+file.id();

            $.ajax({
                type: 'POST',
                url: '/'+self.controller+'/delFile',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        inc.vueMessage("操作成功");
                       self.files.remove(file);
                    }
                    else {
                        // alert(json.data);
                        inc.vueAlert(json.data);
                    }
                },
                error: function (data) {
                    // alert("操作失败：" + data.responseText);
                    inc.vueAlert('操作失败：' + data.responseText);
                }
            });

        }

        self.setBtnText=function () {
            if(self.isMulti() && self.files().length>0)
                self.btnText("继续上传");
            else if(!self.isMulti() && self.files().length>0)
                self.btnText("重新上传");
            else
                self.btnText("上传文件");
        }


        if(params.files)
        {
            for(var i=0;i<params.files.length;i++)
            {
                var fileItem=new FileItem({
                    'id':params.files[i]["id"],
                    'isDone':1,
                    'fileName':params.files[i]["name"],
                    'url':self.getReadFileUrl(params.files[i]["id"], params.files[i]["name"])
                });
                self.files.push(fileItem);
            }
        }

        self.setBtnText();

        //是否正在上传中
        self.isUploading=false;

        self.addFunction=function(e,data,element)
        {
            if(self.isUploading)
            {
                // alert("文件正在上传中，请稍后再操作！");
				inc.vueAlert('文件正在上传中，请稍后再操作！');
                return;
            }
            if (!inc.checkFileType(data.files[0].name, ko.unwrap(self.fileType))) {
                // alert("只能上传指定类型的文件：" + ko.unwrap(self.fileType));
                // inc.vueAlert('只能上传指定类型的文件：' + ko.unwrap(self.fileTypeDesc));
                inc.vueAlert('只能上传指定类型的文件：' + ko.unwrap(self.fileTypeDesc) + '格式文件，文件不能超过' + ko.unwrap(self.maxSize) + 'M');
                return;
            }
            if (data.files[0].size > ko.unwrap(self.maxSize*1024*1024)) {
                // alert("文件大小超过最大限制：" +ko.unwrap(self.maxSize) + "M");
				inc.vueAlert("文件大小超过最大限制：" +ko.unwrap(self.maxSize) + "M");
                return;
            }
            self.btnText("上传中");
            self.isUploading=true;
            e.target.disabled = true;
            data.formData = self.params;
            data.submit();
        }

        self.doneFunction=function(e,data,element){
        	e.target.disabled = false;
            self.isUploading=false;
			var el;
			if(e.target.id == "file-input-1"){
				el = $(e.target).parent().parent().next();
			}else{
				el = $(e.target).closest('ul').prev().find('.progress-box');
			}
			el.hide();
            if (data.result.state == 0) {
                var fileItem=new FileItem({
                    'id':data.result.data,
                    'isDone':1,
                    'fileName':data.result.extra.name,
                    'url':self.getReadFileUrl(data.result.data, data.result.extra.name)
                });
                self.addFile(fileItem);
            } else {
                // alert(data.result.data);
				inc.vueAlert(data.result.data);
            }
            self.setBtnText();
        }
		
		self.progressFunction = function (e, data) {
			var el;
			if(e.target.id == "file-input-1"){
				el = $(e.target).parent().parent().next();
			}else{
				el = $(e.target).closest('ul').prev().find('.progress-box');
			}
			el.show();
	        var progress = parseInt(data.loaded / data.total * 100, 10);
	        el.find('.bar').css(
	            'width',
	            progress + '%'
	        );
	    },
		
        self.failFunction=function (e) {
        	e.target.disabled = false;
            self.isUploading=false;
			var el;
			if(e.target.id == "file-input-1"){
				el = $(e.target).parent().parent().next();
			}else{
				el = $(e.target).closest('ul').prev().find('.progress-box');
			}
			el.hide();
            // alert("上传出错，请稍后重试！");
			inc.vueAlert('上传出错，请稍后重试！');
            self.setBtnText();
        }
    }
</script>