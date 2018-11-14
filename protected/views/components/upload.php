<template id='component-template-file-upload' >
	<div class="btn btn-success" style="float:left;height: 34px;">
		<span class="fileinput-button">
	        <span class="btn-text" data-bind="html:btnText">选择上传文件</span>
	        <input type="file" data-bind="fileUpload:true,url:postUrl,add:addFunction,done:doneFunction,progress:progressFunction"/>
	    </span>
	</div>
	<div class="progress" style="float:left;margin-bottom: 0;margin-top: 12PX;display: none;">
	    <div class="bar" style="width: 0%;"></div>
	</div>
    <div class="clearfix"></div>
    <ul class="list-unstyled " data-bind="foreach:files">
        <li style="padding-top: 7px;">
            <span class="glyphicon glyphicon-ok text-green" data-bind="visible:isDone"></span>
            <a target="_blank" data-bind="attr: { href: url, title: fileName },html:fileName"></a>
            <a class="btn btn-danger btn-xs del-btn" data-bind="click:$parent.del">删除</a>
        </li>
    </ul>
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

    /**
     * 文件上传组件
     * @param params
     *          {
     *              status:settlementFileStatus,
     *              uploadFiles:settleFiles,
     *              controller:"保存文件的Controller名",
     *              fileConfig:{上传文件的配置信息},
     *              files:初始化的文件,
     *              baseId:关联信息id
     *          }
     * @constructor
     */
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
        self.files.subscribe(function (v) {
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
        self.maxSize=params.fileConfig.maxSize*1024*1024;//允许上传的文件大小最大值，单位M
        if(params.fileConfig.multi)
            self.isMulti(true);

        //文件上传额外参数
        self.params={};
        if(params.baseId)
            self.params["id"]=ko.unwrap(params.baseId);
        /*self.params={
            id:ko.unwrap(params.baseId),
            type:params.fileConfig.id
        };*/
		self.params=$.extend(self.params,params.fileParams, {type:params.fileConfig.id});

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
            var files=ko.unwrap(params.files);
            if(files==null)
                return;

            for(var i=0;i<files.length;i++)
            {
                var fileItem=new FileItem({
                    'id':files[i]["id"],
                    'isDone':1,
                    'fileName':files[i]["name"],
                    'url':self.getReadFileUrl(files[i]["id"], files[i]["name"])
                });
                self.files.push(fileItem);
            }
        }

        self.setBtnText();

        //是否正在上传中
        self.isUploading=false;

        self.addFunction=function(e,data)
        {
            if(self.isUploading)
            {
                alert("文件正在上传中，请稍后再操作！");
                return;
            }

        	var el = $(e.target).parent().parent().next();
			el.hide();
            if (!inc.checkFileType(data.files[0].name, ko.unwrap(self.fileType))) {
                alert("只能上传指定类型的文件：" + ko.unwrap(self.fileType));
                return;
            }
            if (data.files[0].size > ko.unwrap(self.maxSize)) {
                alert("文件大小超过最大限制：" +ko.unwrap(self.maxSize) + "K");
                return;
            }
            self.isUploading=true;
            self.btnText("正在上传文件。。。");
            e.target.disabled = true;
            data.formData = self.params;
            data.submit();
        }
		
		self.progressFunction = function (e, data) {
			var el = $(e.target).parent().parent().next();
			el.show();
	        var progress = parseInt(data.loaded / data.total * 100, 10);
	        el.find('.bar').css(
	            'width',
	            progress + '%'
	        );
	    },
		
        self.doneFunction=function(e,data){
            self.isUploading=false;
        	var el = $(e.target).parent().parent().next();
			el.hide();
			e.target.disabled = false;
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

        self.failFunction=function () {
            self.isUploading=false;
        	e.target.disabled = false;
            alert("上传出错，请稍后重试！");
            self.setBtnText();
        }
    }
</script>