<template id='component-template-file-upload' >

<div data-bind="visible:!status()">
	<div class="btn btn-upload-custom fileinput-button" style="float: left;display: flex;align-items: center;">
		<span class="btn-text">
		    <img class="img img-upload"   src="/img/upload/upload-cloud.png"> 
		    <span class="btn-text" data-bind="html:btnText">选择上传文件</span>
		    <input id="file-input-1" type="file" data-bind="fileUpload:true,url:postUrl,add:addFunction,done:doneFunction,progress:progressFunction"/>
		</span>
	</div>
	<div class="progress" style="float:left;margin-bottom: 0;margin-top: 12PX;display: none;">
	    <div class="bar" style="width: 0%;"></div>
	</div>
	<span style="float: left;margin-top: 12PX;;" class="btn-text-desc">&nbsp;&nbsp;&nbsp;&nbsp;只能上传<span data-bind="html:fileTypeDesc"></span>格式文件，文件不能超过<span data-bind="html:maxSize"></span>M</span>
</div>
<div style="display: block;overflow: hidden;" data-bind="visible:status()">
	<div class="pull-left">
		<ul class="list-unstyled list-unstyled-custom" data-bind="foreach:files">
	        <li class="list-unstyled__upload-list" style="display: flex;align-items: center;">
	        	<div style="float：left;display: inline-block;width: 254px;word-break: break-all;margin-bottom: 0;">
	              	<span class="glyphicon glyphicon-ok text-green" data-bind="visible:isDone"></span>
	              	<a class="text-name-custom" target="_blank" data-bind="attr: { href: url, title: fileName },html:fileName" ></a>
	            </div>
	        	<!--<div class="progress" id="progress2" style="float：left;margin-bottom: 0;margin-top: 12PX;">
				    <div class="bar" style="width: 0%;"></div>
				</div>-->
				<a style="float：left;" class="btn btn-xs del-btn file-btn-custom del-btn-custom" data-bind="click:$parent.del">
		            <img class="img " src="/img/upload/delete-btn.png"> 删除
	          	</a>
	          	<a style="float：left;" target="_blank" class="btn btn-xs del-btn file-btn-custom download-btn-custom" data-bind="attr: { href: url, title: fileName }" >
		            <img class="img " src="/img/upload/download-btn.png"> 下载
	          	</a>
	        </li>
	    </ul>
	    <div style="display: none;" class="progress-box">
			<div class="btn btn-upload-custom fileinput-button" style="float: left;display: flex;align-items: center;">
				<span class="btn-text">
				    <img class="img img-upload"   src="/img/upload/upload-cloud.png"> 
				    <span class="btn-text" data-bind="html:btnText">选择上传文件</span>
				    <input type="file" data-bind="fileUpload:true,url:postUrl,add:addFunction,done:doneFunction,progress:progressFunction"/>
				</span>
			</div>
			<div class="progress" style="float:left;margin-bottom: 0;margin-top: 12PX;">
			    <div class="bar" style="width: 0%;"></div>
			</div>
			<span style="float: left;margin-top: 12PX;;" class="btn-text-desc">&nbsp;&nbsp;&nbsp;&nbsp;只能上传<span data-bind="html:fileTypeDesc"></span>格式文件，文件不能超过<span data-bind="html:maxSize"></span>M</span>
		</div>
	</div>
    <ul class="pull-left list-unstyled list-unstyled-custom">
        <li class="list-unstyled__upload-list">
	        <span style="padding: 0 12px" class="btn reupload-file-btn-custom fileinput-button">
	            <span class="btn-text">
	                <img class="img" src="/img/upload/upload-again-btn.png"> 
	                <span class="btn-text" >继续上传</span>
	                <input id="file-input-2" type="file" data-bind="fileUpload:true,url:postUrl,add:addFunction,done:doneFunction,progress:progressFunction"/>
	            </span>
	        </span>
        </li>
    </ul>
</div>
</template>
<style>
	.progress{
		margin-left: 10px;
		width: 100PX;
		height: 18px;
		border: 1px solid #ccc;
		border-radius: 10px;
		overflow: hidden;
	}
	.bar {
	    height: 18px;
	    background: green;
	}
</style>
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
        });

        self.controller=params.controller;

        self.postUrl="/"+self.controller+"/saveFile/";//文件提交地址

        self.fileType=params.fileConfig.fileType;//允许上传的文件类型
        self.fileTypeDesc = params.fileConfig.fileTypeDesc || self.fileType;
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

        self.btnText=ko.observable("选择上传文件");

        self.checkFileType=function (fileName) {
            if (!inc.checkFileType(fileName, ko.unwrap(self.fileType))) {
                alert("只能上传指定类型的文件：" + ko.unwrap(self.fileType));
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
                        inc.showNotice("操作成功");
                       self.files.remove(file);
                    }
                    else {
                        alert(json.data);
                    }
                },
                error: function (data) {
                    alert("操作失败：" + data.responseText);
                }
            });

        }

        self.setBtnText=function () {
            if(self.isMulti() && self.files().length>0)
                self.btnText("继续上传");
            else if(!self.isMulti() && self.files().length>0)
                self.btnText("重新上传");
            else
                self.btnText("选择上传文件");
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
                alert("文件正在上传中，请稍后再操作！");
                return;
            }
            if (!inc.checkFileType(data.files[0].name, ko.unwrap(self.fileType))) {
                alert("只能上传指定类型的文件：" + ko.unwrap(self.fileType));
                return;
            }
            if (data.files[0].size > ko.unwrap(self.maxSize*1024*1024)) {
                alert("文件大小超过最大限制：" +ko.unwrap(self.maxSize) + "M");
                return;
            }
            self.btnText("正在上传文件。。。");
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
                alert(data.result.data);
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
            alert("上传出错，请稍后重试！");
            self.setBtnText();
        }
    }
</script>