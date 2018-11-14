<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            <b><a href="/contract/detail/?id=<?php echo $contract['contract_id'] ?>&t=1" target="_blank">采购合同</a> &nbsp;&nbsp;&nbsp;&nbsp;
                <a href="/contract/detail?id=<?php echo $contract['contract_id'];?>" target="_blank">
                    <span style="font-size: 16px;">NO.<span><?php echo $contract['contract_code'] ?></span></span>
                </a>
            </b>
        </h3>
        <div class="pull-right box-tools">
            <?php if (!$this->isExternal) { ?>
                <button type="button" class="btn btn-default btn-sm" onclick="back()">返回</button>
            <?php } ?>
        </div>
    </div>
    <div class="box-body form-horizontal">
        <?php
        if (Utility::isNotEmpty($transactions)) { ?>
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                <tr>
                    <th style="width:120px;text-align:center">品名</th>
                    <th style="width:80px;text-align:center">合同数量</th>
                    <th style="width:80px;text-align:center">总入库通知单数量</th>
                    <th style="width:80px;text-align:center">总入库数量</th>
                    <th style="width:80px;text-align:center">总结算数量</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($transactions as $v) { ?>
                    <tr>
                        <td style="text-align:center"><?php echo !empty($v['goods_name']) ? $v['goods_name'] : (!empty($v['goods']['name']) ? $v['goods']['name'] : '') ?></td>
                        <td style="text-align:center">
                            <?php echo Utility::numberFormatToDecimal($v["quantity"], 4) ?>
                            <?php echo $this->map["goods_unit"][$v["unit"]]['name'] ?>
                            ± <?php echo $v["more_or_less_rate"] * 100 . '%' ?>
                        </td>
                        <td style="text-align:center">
                            <?php $total = ContractGoodsService::getStockInBatchGoodsQuantity($contract['contract_id'], $v['goods_id'], $this->map["goods_unit"][$v["unit"]]['id']);

                            echo $total['quantity'], $this->map["goods_unit"][$v["unit"]]['name'];
                            if(!empty($v['sub']["quantity"]) && $v['sub']["unit"] != $v["unit"]) {
                                echo '/' . Utility::numberFormatToDecimal($total['quantity_sub'], 4) . $this->map["goods_unit"][$v['sub']["unit"]]['name'];
                            }
                            ?>
                        </td>
                        <td style="text-align:center">
                            <?php 
                            $total = ContractGoodsService::getStockInGoodsQuantity($contract['contract_id'], $v['goods_id'], $this->map["goods_unit"][$v["unit"]]['id']);
                            // $total = ContractGoodsService::getStockInBatchGoodsQuantity($contract['contract_id'], $v['goods_id'], $this->map["goods_unit"][$v["unit"]]['id']);

                            echo $total['quantity'], $this->map["goods_unit"][$v["unit"]]['name'];
                            if(!empty($v['sub']["quantity"]) && $v['sub']["unit"] != $v["unit"]) {
                                echo '/' . Utility::numberFormatToDecimal($total['quantity_sub'], 4) . $this->map["goods_unit"][$v['sub']["unit"]]['name'];
                            }
                            ?>
                        </td>
                        <td style="text-align:center">
                            <?php $total = ContractGoodsService::getStockBatchSettlementGoodsQuantity($contract['contract_id'], $v['goods_id'], $this->map["goods_unit"][$v["unit"]]['id']);

                            echo $total['quantity'], $this->map["goods_unit"][$v["unit"]]['name'];
                            if(!empty($v['sub']["quantity"]) && $v['sub']["unit"] != $v["unit"]) {
                                echo '/' . Utility::numberFormatToDecimal($total['quantity_sub'], 4) . $this->map["goods_unit"][$v['sub']["unit"]]['name'];
                            }
                            ?>
                        </td>

                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
    function back() {
        location.href = "/<?php echo $this->getId() ?>";
    }

</script>