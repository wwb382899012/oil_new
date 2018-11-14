<section class="content">
    <?php
    $checkLog = FlowService::getCheckLog($stockIn['stock_in_id'], "7");
    include 'partial/stockInInfo.php';
    if (Utility::isNotEmpty($stockDetail)) {
        $flag = 0;
        foreach ($stockDetail as $key => $row) {
            if (Utility::isNotEmpty($row['stock_detail'])) {
                $flag = 1;
                break;
            }
        }
        if ($flag) {
            include 'partial/stockChange.php';
        }
    }
    ?>
</section>

<script>
    function back() {
        <?php
        if (!empty($_GET["url"])) {
            echo 'location.href="' . $this->getBackPageUrl() . '";';
        } else {
            echo "history.back();";
        }
        ?>
    }
</script>