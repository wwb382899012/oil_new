<?php include("new_koForm.php"); ?>
<template id='component-template-check-items' >
    <!--ko foreach:items -->
        <div class="form-group">
            <p class="form-cell-title">
                <span data-bind='html:name, css: {"must-fill": required}'></span>
            </p>
            <div>
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
