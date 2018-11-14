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
    <select class="selectpicker show-menu-arrow form-control"
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
    <select class="selectpicker show-menu-arrow form-control" multiple="true"
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
    <div class="o-row">
        <div class=" o-col-sm-6">
            <div class="flex-grid children-gap--fixed first-line-align">
                <!--ko foreach:options -->
                <label class="o-control o-control--radio inline-flex">
                    <input type="radio" style="width: auto" data-bind='{
                        event:{change: $parent.select},
                        attr: { name: "radio"+ $parent.id()}
                    }'>
                    <span data-bind="text:text" style="margin-left: 10px;"></span>
                    <div class="o-control__indicator"></div>
                </label>
                <!-- /ko -->
            </div>
            <span class="validationMessage" data-bind="validationMessage: value"></span>
        </div>
        <div class="o-col-sm-6 ">
            <div class="grid1">
                <label class="cell-title grid-item1">
                    备注:
                </label>
                <input type="text" class="form-control grid-item2" placeholder="请填写备注" data-bind="value:remark">
            </div>

        </div>
    </div>
    <!-- /ko -->
</template>

<script src="/js/pages/koForm.js" type="text/javascript"></script>