
            <div class="form-group">
                <label for="buy_sell_type" class="col-sm-2 control-label">调货处理单编号</label>
                <div class="col-sm-10">
                    <p class="form-control-static">
                    <?php echo $returnOrder->cross_code;?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label for="buy_sell_type" class="col-sm-2 control-label">销售合同编号</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                    <?php echo $returnOrder->contract->contract_code;?>
                    </p>
                </div>
                <label for="buy_sell_type" class="col-sm-2 control-label">下游合作方</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                    <?php echo $returnOrder->contract->partner->name;?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label for="buy_sell_type" class="col-sm-2 control-label">项目编号</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                    <?php echo $returnOrder->project->project_code;?>
                    </p>
                </div>
                <label for="buy_sell_type" class="col-sm-2 control-label">品名</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                    <?php echo $returnOrder->goods->name;?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label for="buy_sell_type" class="col-sm-2 control-label">调货原因</label>
                <div class="col-sm-10">
                    <p class="form-control-static">
                    <?php echo $returnOrder->remark;?>
                    </p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">调货明细</label>
                <div class="col-sm-10">
                    <table class="table table-condensed table-hover table-bordered table-layout">
                        <thead>
                            <tr>
                                <!-- <th style="width: 180px; text-align: center;">调货单编码</th> -->
                                <th style="width: 160px; text-align: center;">采购合同编号</th>
                                <!-- <th style="width: 180px; text-align: center;">上游合作方</th> -->
                                <th style="width: 180px; text-align: center;">入库单编号</th>
                                <th style="text-align: center;">仓库</th>
                                <th style="width: 120px; text-align: center;">预计调货数量</th>
                                <th style="width: 120px; text-align: center;">实际出库数量</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $sum = 0;
                            $sum_out = 0;
                            $unit = 0;
                            foreach ($relationOrder->crossDetail as $key => $value) {
                                $unit = $value->stock->unit;
                                $sum += $value['quantity'];
                                $sum_out += $value['quantity_out'];
                                ?>
                            <tr>
                                <td style="text-align: left;"><?php echo $value->contract->contract_code ?></td>
                                <td style="text-align: left;"><?php echo $value->stock->stockIn->code ?></td>
                                <td style="text-align: left;" title="<?php echo $value->stock->store->name ?>"><?php echo $value->stock->store->name ?></td>
                                <td style="text-align: right;"><?php echo number_format($value['quantity'], 2).$this->map['goods_unit'][$value->stock->unit]['name'] ?></td>
                                <td style="text-align: right;"><?php echo number_format($value['quantity_out'], 2).$this->map['goods_unit'][$value->stock->unit]['name'] ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="text-align: center;">合计</td>
                                <!-- <td >&nbsp;</td> -->
                                <!-- <td >&nbsp;</td> -->
                                <td >&nbsp;</td>
                                <td >&nbsp;</td>
                                <td style="text-align: right;"><?php echo number_format($sum, 2).$this->map['goods_unit'][$unit]['name'] ?></td>
                                <td style="text-align: right;"><?php echo number_format($sum_out, 2).$this->map['goods_unit'][$unit]['name'] ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="form-group">
                <label for="buy_sell_type" class="col-sm-2 control-label">调货处理方式</label>
                <div class="col-sm-10">
                    <p class="form-control-static">
                        <?php echo $this->map['cross_done_desc'][$returnOrder['type']]?>
                    </p>
                </div>
            </div>
            <?php if($returnOrder['type'] == ConstantMap::DISTRIBUTED_RETURN):?>
            <div class="form-group">
                <label for="buy_sell_type" class="col-sm-2 control-label">本次还货明细</label>
                <div class="col-sm-10">
                    <table class="table table-condensed table-hover table-bordered table-layout">
                        <thead>
                            <tr>
                                <!-- <th style="width: 180px; text-align: center;">调货单编码</th> -->
                                <th style="width: 200px; text-align: center;">还货采购合同编号</th>
                                <!-- <th style="width: 180px; text-align: center;">上游合作方</th> -->
                                <th style="width: 200px; text-align: center;">入库单编号</th>
                                <th style="text-align: center;">品名</th>
                                <th style="width: 150px; text-align: center;">可用库存数量</th>
                                <th style="width: 150px; text-align: center;">还货数量</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $sum = 0;
                            $sum_out = 0;
                            $unit = 0;
                            foreach ($returnOrder->crossDetail as $key => $value) {
                                $unit = $value->stock->unit;
                                $sum += $value['quantity'];
                                $sum_out += $value['quantity_out'];
                                ?>
                            <tr>
                                <td style="text-align: left;"><?php echo $value->contract->contract_code ?></td>
                                <td style="text-align: left;"><?php echo $value->stock->stockIn->code ?></td>
                                <td style="text-align: left;" title="<?php echo $value->stock->store->name ?>"><?php echo $value->stock->store->name ?></td>
                                <td style="text-align: right;"><?php echo number_format($value['quantity'], 4).$this->map['goods_unit'][$value->stock->unit]['name'] ?></td>
                                <td style="text-align: right;"><?php echo number_format($value['quantity_out'], 4).$this->map['goods_unit'][$value->stock->unit]['name'] ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="text-align: center;">合计</td>
                                <!-- <td >&nbsp;</td> -->
                                <!-- <td >&nbsp;</td> -->
                                <td >&nbsp;</td>
                                <td >&nbsp;</td>
                                <td style="text-align: right;"><?php echo number_format($sum, 2).$this->map['goods_unit'][$unit]['name'] ?></td>
                                <td style="text-align: right;"><?php echo number_format($sum_out, 2).$this->map['goods_unit'][$unit]['name'] ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php else:?>
            <div class="form-group">
                <label for="buy_sell_type" class="col-sm-2 control-label">采购合同数量</label>
                <div class="col-sm-10">
                    <p class="form-control-static">
                        <?php echo $returnOrder->quantity . $this->map['goods_unit'][$returnOrder->crossDetail->stockOutDetail->stock->unit]['name'];?>
                    </p>
                </div>
            </div>
            <?php endif;?>
            <div class="form-group">
                <label for="buy_sell_type" class="col-sm-2 control-label">备注</label>
                <div class="col-sm-10">
                    <p class="form-control-static">
                        <?php echo $returnOrder->remark?>
                    </p>
                </div>
            </div>