<template id='component-template-multi-file-upload'>
    <span data-bind="foreach:attachTypes">
        <!-- ko component: {
            name: "multi-file-upload-item",
            params: {
                        controller:$parent.controller,
                        fileConfig:$data,
                        files:$parent.attachs,
                        fileParams:$parent.fileParams,
                        id:id
            }
        } -->
        <!-- /ko -->
    </span>
</template>
<template id='component-template-multi-file-upload-item'>
    <div class="form-group">
        <label class="col-sm-2 control-label">
            <span class='glyphicon ' data-bind="css:{'glyphicon-ok text-green':fileUploadStatus,' glyphicon-remove text-red':!fileUploadStatus()}"></span>&emsp;
            <span data-bind="html:model.name"></span>
            <span data-bind="visible:model.required"><span class="text-red fa fa-asterisk"></span></span>
        </label>
        <div class="col-sm-10">
            <!-- ko component: {
                        name: "file-upload",
                        params: {
                            status:fileUploadStatus,
                            controller:controller,
                            fileConfig:fileConfig,
                            fileParams:fileParams,
                            files:files[id]
                        }
                } -->
            <!-- /ko -->
        </div>
    </div>
</template>
<?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/upload.php"; ?>

<script>
	ko.components.register('multi-file-upload', {
		template: {element: 'component-template-multi-file-upload'},
		viewModel: multiUploadComponent
	});
	ko.components.register('multi-file-upload-item', {
		template: {element: 'component-template-multi-file-upload-item'},
		viewModel: fileUploadItemComponent
	});
	function multiUploadComponent(params) {
		var self = this;
		self.attachTypes = ko.observableArray(inc.objectToArray(params.attachTypes));
		self.controller = params.controller;
		self.attachs = params.attachs;
		self.fileParams = params.fileParams;
	}

	function fileUploadItemComponent(params) {
		var self = this;
		self.controller = params.controller;
		self.fileParams = params.fileParams;
		self.files = params.files;
		self.fileConfig = params.fileConfig;
		self.id = params.id;
		self.model = new fileConfig(params.fileConfig);
		self.fileUploadStatus = ko.observable();
	}
	function fileConfig(option) {
		var defaults = {
			fileType: '',
			id: 0,
			maxSize: 30,
			multi: 0,
			name: '',
			required: false,
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.fileType = ko.observable(o.fileType);
		self.id = ko.observable(o.id);
		self.maxSize = ko.observable(o.maxSize);
		self.multi = ko.observable(o.multi);
		self.name = ko.observable(o.name);
		self.required = ko.observable(o.required);
	}
</script>