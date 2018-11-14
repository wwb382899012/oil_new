<div id="chartsContainer"></div>
<script src="/plugins/highcharts/highcharts.js"></script>
<script>
    $(function () {
        showCharts(<?php echo json_encode($data) ?>);
    });

    function showCharts(option) {
        var defaults={
            container:"#chartsContainer",
            chartType:"spline",
            title:"",
            subTitle:"",
            yTitle:"金额",
            dataFormatter:function() {
                return Highcharts.numberFormat(this.y/10000,2)+" 万元";
            },
            yAxisFormatter:function() {
                return Highcharts.numberFormat(this.value/10000,2)+" 万元";
            },
            tooltipFormatter:function() {
                var s="<b>"+ this.x + "</b>   " +this.series.name+": "+ Highcharts.numberFormat(this.y,2)+" 元" ;
                return s;
            }
        };
        var o=$.extend(defaults,option);
        inc.showChart(o);
    }
</script>