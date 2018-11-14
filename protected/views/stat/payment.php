<div id="pay_line"></div>
<script src="/plugins/highcharts/highcharts.js"></script>
<script>
    $(function () {
        inc.showChart({
            container:"#pay_line",
            chartType:"spline",
            title:"近七天付款信息",
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
            },
            x:<?php echo json_encode($payData["x"]) ?>,
            series:<?php echo json_encode($payData["series"]) ?>
        });
    });
    </script>