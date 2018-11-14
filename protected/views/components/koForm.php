<template id='component-template-ko-select-buttons' >
    <!--ko with:model -->
    <!--ko foreach:options -->
    <a class="btn btn-sm " data-bind='{
			text:text,
			css:selectedCss,
			click:$parent.select,
		}'></a>
    <!-- /ko -->
    <span class="validationMessage" data-bind="validationMessage: value"></span>
    <!-- /ko -->
</template>
<template id='component-template-ko-text-area' >
    <!--ko with:model -->
    <textarea class="form-control" data-bind='value:value'></textarea>
    <!-- /ko -->
</template>
<template id='component-template-ko-input' >
    <!--ko with:model -->
    <input type="text" class="form-control"  placeholder="请填写内容" data-bind="value:value">
    <!-- /ko -->
</template>
<template id='component-template-ko-select' >
    <!--ko with:model -->
    <select class="form-control input-sm"
            data-bind="
                    optionsCaption: optionsCaption,
                    optionsText: 'name',
                    optionsValue: 'id',
                    options:selectOptions,
                    valueAllowUnset: true,
                    selectpicker:value">
    </select>
    <!-- /ko -->
</template>
<template id='component-template-ko-multiple-select' >
    <!--ko with:model -->
    <select class="form-control input-sm" multiple="true"
            data-bind="
                    optionsCaption: optionsCaption,
                    optionsText: 'name',
                    optionsValue: 'id',
                    options:selectOptions,
                    valueAllowUnset: true,
                    selectpicker:value">
    </select>
    <!-- /ko -->
</template>
<template id='component-template-ko-select-buttons-input' >
    <!--ko with:model -->
    <div class="form-inline">
        <!--ko foreach:options -->
        <a class="btn btn-sm " data-bind='{
			text:text,
			css:selectedCss,
			click:$parent.select,
		}'></a>
        <!-- /ko -->
        &nbsp;<input type="text" class="form-control" style="width:90%" placeholder="请填写备注" data-bind="value:remark">
        <span class="validationMessage" data-bind="validationMessage: value"></span>
    </div>
    <!-- /ko -->
</template>

<script src="/js/pages/koForm.js" type="text/javascript"></script>