<template id='component-template-multi-file-upload'>
	<div data-bind="foreach:attachTypes">
    <!-- ko component: {
        name: "multi-file-upload-item",
        params: {
            controller:$parent.controller,
            fileConfig:$data,
            files:$parent.attachs,
            fileParams:$parent.fileParams,
            index:ko.unwrap($index),
        }
    } -->
    <!-- /ko -->	
	</div>
</template>
<template id='component-template-multi-file-upload-item'>
<div class="box-body form-group form-group-custom  form-group-custom-upload">
	<div class="form-group">
	    <label class="col-sm-2 control-label">

	      <span class="glyphicon glyphicon-remove text-red" data-bind="css:{'glyphicon-ok text-green':fileUploadStatus,' glyphicon-remove text-red':!fileUploadStatus()}"></span>&emsp;
	      <span class="upload-star-box">
          <i class="required__span-red" data-bind="visible:model.required">*</i>
  		</span>
	      <span data-bind="html:model.name" class="upload-title upload-title-custom"></span>
	      <span data-bind="visible:model.required" style="display: none;">
	        <span class="text-red fa fa-asterisk"></span>
	      </span>
	    </label>
	    <div class="col-sm-10">
	      <!-- ko component: {
	                name: "file-upload",
	                params: {
	                    status:fileUploadStatus,
	                    controller:controller,
	                    fileConfig:fileConfig,
	                    fileParams:fileParams,
	                    files:files[index+1]
	                }
	        } -->
	    <!-- /ko -->
	    </div>
	</div>
</div>
</template>
<?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/uploadNew.php"; ?>

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
		self.index = params.index;
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