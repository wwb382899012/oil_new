<template id='component-template-file-upload' >
    <span class="btn btn-success fileinput-button">
        <span class="btn-text" data-bind="html:btnText">选择上传文件</span>
        <input type="file" data-bind="fileUpload:true,url:postUrl,add:addFunction,done:doneFunction"/>
    </span>
    <ul class="list-unstyled" data-bind="foreach:files">
        <li style="padding-top: 7px;">
            <span class="glyphicon glyphicon-ok text-green" data-bind="visible:isDone"></span>
            <p data-bind="text:fileName"></p>
        </li>
    </ul>
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
        self.maxSize=params.fileConfig.maxSize*1024*1024;//允许上传的文件大小最大值，单位M
        if(params.fileConfig.multi)
            self.isMulti(true);

        //文件上传额外参数
        self.params={
            id:ko.unwrap(params.baseId),
            type:params.fileConfig.id
        };

        self.getReadFileUrl=function (id) {
            return '/'+self.controller+'/getFile/?id='+id;
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
                    'url':self.getReadFileUrl(params.files[i]["id"])
                });
                self.files.push(fileItem);
            }
        }

        self.setBtnText();

        self.addFunction=function(e,data){
            if (!inc.checkFileType(data.files[0].name, ko.unwrap(self.fileType))) {
                alert("只能上传指定类型的文件：" + ko.unwrap(self.fileType));
                return;
            }
            if (data.files[0].size > ko.unwrap(self.maxSize)) {
                alert("文件大小超过最大限制：" +ko.unwrap(self.maxSize) + "K");
                return;
            }
            self.btnText("正在上传文件。。。");
            data.formData = self.params;
            data.submit();
        }

        self.doneFunction=function(e,data){
            if (data.result.state == 0) {
                var fileItem=new FileItem({
                    'id':data.result.data,
                    'isDone':1,
                    'fileName':data.result.extra.name,
                    'url':self.getReadFileUrl(data.result.data)
                });
                self.addFile(fileItem);

            } else {
                alert(data.result.data);
            }
            self.setBtnText();
        }

        self.failFunction=function () {
            alert("上传出错，请稍后重试！");
            self.setBtnText();
        }
    }
</script>