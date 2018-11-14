<?php include("koForm.php"); ?>
<template id='component-template-check-items' >
    <!--ko foreach:items -->
    <div class="form-group">
        <label class="col-sm-2 control-label">
            <span data-bind='html:name'></span>
            <span class="text-red fa fa-asterisk" data-bind="visible:required"></span>
        </label>
        <div class="col-sm-10">
            <!-- ko component: {
                name: type,
                params: {
                            item: $data
                        }
            } -->
            <!-- /ko -->
        </div>
    </div>
    <!-- /ko -->
</template>
<script src="/js/pages/checkItems.js" type="text/javascript"></script>
