<!---->
<template id='component-template-audit-items' >
<!--ko foreach:{data:configs} -->
    <div class="form-group" data-bind='css:$data.invalide'>
        <label for="type" class="col-sm-2 control-label" data-bind='html:$data.name'></label>
        <div data-bind='component:{
        name:$data.type, 
        params:{
        	config:$data, 
        	}
        }'></div>
        
    </div>
<!-- /ko -->
</template>

<template id='component-template-ko-select-buttons' >
    <div class="col-sm-10">
		<!--ko foreach:{data:$component.options} -->
		<a class="btn btn-sm " data-bind='{
			text:$data.text, 
			css:{
				"btn-success":$data.selected, 
				"btn-default":$data.notSelected,
				},
			click:click,
		}'></a>
		<!-- /ko -->
    </div>
</template>

<template id='component-template-ko-text-area' >
    <div class="col-sm-10">
    	<textarea class="form-control" data-bind='{
	    	value:config.value,
	    	event: {
	    		click:update
	    	}
    	}'></textarea>
    </div>
</template>



<script type="text/javascript">
    ko.components.register('audit-items', {
        template: { element: 'component-template-audit-items' },
        viewModel:auditItems
    });
    function auditItems(params)
    {
    	var self = this;
    	var defaultValue = {};
    	for(var ind = params.configs().length - 1; ind >=0; ind --) {
    		var config = params.configs()[ind];
    		params.configs()[ind]['invalide'] = ko.observable('');
    		if(params.configs()[ind].required) {
    			params.configs()[ind].text = params.configs()[ind].text + '<span data-bind="" class="text-red">*</span>';
    		}
    		if(typeof params.values[config.key] == 'undefined') {
    			defaultValue[config.key] = '';
    			params.configs()[ind]['value'] = '';
    		} else {
    			defaultValue[config.key] = params.values[config.key];
    			params.configs()[ind]['value'] = params.values[config.key];
    		}
    	}
    	self.configs = params.configs;

    	self.configs.isValid = function() {
    		var valid = true;
            for (var i = 0; i <= self.configs().length - 1; i++) {
                if(self.configs()[i].required && (self.configs()[i].value=='' || typeof self.configs()[i].value=='undefined')) {
                    self.configs()[i].invalide('has-error');
                    valid = false;
                }
            }
            return valid;
    	}

    	self.configs.value = function() {
    		var defaultValue = {};
	    	for(var ind = 0; ind <= self.configs().length - 1; ind ++) {
	    		var config = self.configs()[ind];
	    		defaultValue[config.key] = config['value'];
	    	}
            return defaultValue;
    	}
    }

    function koDefaultComponentInput(params)
    {
    	var self = this;
    	self.config = params.config;
    	self.update = function() {
    		self.config['invalide']('');
    	}
    }

    function koSelectButtons(params) {
    	var self = this;
    	self.config = params.config;
    	var options = params.config.options;
    	self.options = ko.observableArray();
    	self.setOptions = function () {
	    	for (var i = 0; i <= options.length - 1; i++) {
	    		self.options.push(new koSelectButtonsOption(options[i], self));
	    	}
    	}
    	self.resetOptions = function () {
    		self.config['invalide']('');
			ko.utils.arrayForEach(self.options(), function(el, index) {
			    el.updateParent(self);
			});
    	}
    	self.setOptions();
    }

    function koSelectButtonsOption(option, parent) {
    	var self = this;
    	self.parent = parent;
    	self.value = ko.observable(option['value']);
    	self.text = ko.observable(option['text']);
    	self.selected = ko.observable(parent.config.value == self.value());
    	self.notSelected = ko.observable(parent.config.value != self.value());
	    self.updateParent = function(parent) {
    		self.parent = parent;
	    	self.selected(parent.config.value == self.value());
	    	self.notSelected(parent.config.value != self.value());
	    }
		self.click = function() {
			self.parent.config.value = self.value();
			self.parent.resetOptions();
		}
    }

    ko.components.register('koSelectButtons', {
        template: { element: 'component-template-ko-select-buttons' },
        viewModel:koSelectButtons
    });

    ko.components.register('koTextArea', {
        template: { element: 'component-template-ko-text-area' },
        viewModel:koDefaultComponentInput
    });
</script>