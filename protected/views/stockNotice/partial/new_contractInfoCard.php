<div class="z-card">
    <h3 class="z-card-header">
        <b>
            <a href="/contract/detail/?id=<?php echo $contract['contract_id'] ?>&t=1" target="_blank">采购合同</a> &nbsp;&nbsp;&nbsp;&nbsp;
            <a href="/contract/detail?id=<?php echo $contract['contract_id']; ?>&t=1" title="<?php echo $contract['contract_code'] ?>" target="_blank" class="text-link">
                <span>NO.<span><?php echo $contract['contract_code'] ?></span></span>
            </a>
        </b>
    </h3>
    <div class="z-card-body">
        <?php if(Utility::isNotEmpty($transactions)): ?>
            <div class="flex-grid form-group">
                <table class="table table-custom">
                    <thead>
                    <tr>
                        <th>品名</th>
                        <th>合同数量</th>
                        <th>总入库通知单数量</th>
                        <th>总入库数量</th>
                        <th>总结算数量</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($transactions as $v): ?>
                        <tr>
                            <td><?php echo !empty($v['goods_name']) ? $v['goods_name'] : (!empty($v['goods']['name']) ? $v['goods']['name'] : '') ?></td>
                            <td>
                                <?php echo Utility::numberFormatToDecimal($v["quantity"], 4) ?>
                                <?php echo $this->map["goods_unit"][$v["unit"]]['name'] ?>
                                ± <?php echo $v["more_or_less_rate"] * 100 .'%' ?>
                            </td>
                            <td>
                                <?php $total = ContractGoodsService::getStockInBatchGoodsQuantity($contract['contract_id'], $v['goods_id'], $this->map["goods_unit"][$v["unit"]]['id']);

                                echo $total['quantity'], $this->map["goods_unit"][$v["unit"]]['name'];
                                if(!empty($v['sub']["quantity"]) && $v['sub']["unit"] != $v["unit"]){
                                    echo '/'.Utility::numberFormatToDecimal($total['quantity_sub'], 4).$this->map["goods_unit"][$v['sub']["unit"]]['name'];
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $total = ContractGoodsService::getStockInGoodsQuantityNew($contract['contract_id'], $v['goods_id'], $this->map["goods_unit"][$v["unit"]]['id']);
                                echo $total['quantity'], $this->map["goods_unit"][$v["unit"]]['name'];
                                if(!empty($v['sub']["quantity"]) && $v['sub']["unit"] != $v["unit"]){
                                    echo '/'.Utility::numberFormatToDecimal($total['quantity_sub'], 4).$this->map["goods_unit"][$v['sub']["unit"]]['name'];
                                }
                                ?>
                            </td>
                            <td>
                                <?php $total = ContractGoodsService::getStockBatchSettlementGoodsQuantity($contract['contract_id'], $v['goods_id'], $this->map["goods_unit"][$v["unit"]]['id']);
                                echo $total['quantity'], $this->map["goods_unit"][$v["unit"]]['name'];
                                if(!empty($v['sub']["quantity"]) && $v['sub']["unit"] != $v["unit"]){
                                    echo '/'.Utility::numberFormatToDecimal($total['quantity_sub'], 4).$this->map["goods_unit"][$v['sub']["unit"]]['name'];
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>