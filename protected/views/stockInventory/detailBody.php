<?php
$goodsDetail = $stockInventory->stockInventoryGoodsDetail;
$stockInventoryGoodsDetail = $goodsDetail[0];
$unit = Map::$v['goods_unit'][$stockInventoryGoodsDetail['unit']]['name'];
?>
<h4 class="section-title">基本信息</h4>
<div class="form-group">
    <label class="col-sm-2 control-label">交易主体</label>
    <div class="col-sm-10">
        <p class="form-control-static">
            <a target="_blank" href="/corporation/detail/?id=<?php echo $stockInventory->corporation_id ?>&t=1"><?php echo Corporation::getCorporationName($stockInventory->corporation_id) ?></a>
        </p>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">仓库</label>
    <div class="col-sm-10">
        <p class="form-control-static">
            <a target="_blank" href="/storehouse/detail/?store_id=<?php echo $stockInventory->store_id ?>&t=1"><?php echo StorehouseService::getStoreName($stockInventory->store_id) ?></a>
        </p>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">品名</label>
    <div class="col-sm-10">
        <p class="form-control-static"><?php echo GoodsService::getSpecialGoodsNames($stockInventoryGoodsDetail->goods_id) ?></p>
    </div>
</div>

<h4 class="section-title">本次盘点信息</h4>
<div class="form-group">
    <label for="inventory_date" class="col-sm-2 control-label">库存盘点编号</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $stockInventory->inventory_id ?></p>
    </div>
</div>
<div class="form-group">
    <label for="inventory_date" class="col-sm-2 control-label">盘点日期</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $stockInventory->inventory_date ?></p>
    </div>
</div>
<div class="form-group">
    <?php
    $attachments = StockInventoryService::getAttachments($stockInventory->inventory_id);
    ?>
    <label class="col-sm-2 control-label"><?php echo $this->map["stock_inventory_attachment"][ConstantMap::STOCK_INVENTORY_ATTACH_TYPE]['name'] ?></label>
    <div class="col-sm-10">
        <?php if (!empty($attachments[ConstantMap::STOCK_INVENTORY_ATTACH_TYPE])) {
            foreach ($attachments[ConstantMap::STOCK_INVENTORY_ATTACH_TYPE] as $row) {
                if (!empty($row['file_url'])) {
                    ?>
                    <p class="form-control-static">
                        <a href='/stockInventory/getFile/?id=<?php echo $row['id'] ?>&fileName=<?php echo $row['name'] ?>' target='_blank' class='btn btn-primary btn-xs'>点击查看</a>
                    </p>
                    <?php
                } else {
                    echo '无';
                }
            }
        } else {
            echo '<p class="form-control-static">无</p>';
        }
        ?>
    </div>
</div>
<div class="form-group">
    <label for="quantity_active" class="col-sm-2 control-label">盘点前可用库存/<?php echo $unit ?></label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo Utility::numberFormatToDecimal($stockInventoryGoodsDetail->quantity_active, 4) ?></p>
    </div>
    <label for="quantity_frozen" class="col-sm-2 control-label">冻结库存/<?php echo $unit ?></label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo Utility::numberFormatToDecimal($stockInventoryGoodsDetail->quantity_frozen, 4) ?></p>
    </div>
</div>
<div class="form-group">
    <label for="quantity_before" class="col-sm-2 control-label">盘点前库存/<?php echo $unit ?></label>
    <div class="col-sm-4">
        <p class="form-control-static text-red"><?php echo Utility::numberFormatToDecimal($stockInventoryGoodsDetail->quantity_before, 4) ?></p>
    </div>
</div>
<div class="form-group">
    <label for="quantity" class="col-sm-2 control-label">盘点后库存/<?php echo $unit ?></label>
    <div class="col-sm-4">
        <p class="form-control-static text-red"><?php echo Utility::numberFormatToDecimal($stockInventoryGoodsDetail->quantity, 4) ?></p>
    </div>
    <label for="quantity_diff" class="col-sm-2 control-label">库存损耗/<?php echo $unit ?></label>
    <div class="col-sm-4">
        <p class="form-control-static text-red"><?php echo Utility::numberFormatToDecimal($stockInventoryGoodsDetail->quantity_diff, 4) ?></p>
    </div>
</div>
<?php
if ($stockInventoryGoodsDetail->quantity_diff != 0 && Utility::isNotEmpty($stockInventory->stockInventoryDetail)) { ?>
    <div class="form-group">
        <label class="col-sm-2 control-label">损耗分摊</label>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-1 col-sm-11">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                <tr>
                    <th style="width:140px; text-align: center;">入库单编号</th>
                    <th style="width:100px; text-align: right;">盘点前库存/<?php echo $unit ?></th>
                    <th style="width:100px; text-align: right;">冻结库存/<?php echo $unit ?></th>
                    <th style="width:100px; text-align: right;">盘点前可用库存/<?php echo $unit ?></th>
                    <th style="width:100px; text-align: right;">库存损耗/<?php echo $unit ?></th>
                    <th style="width:100px; text-align: right;">盘点后可用库存/<?php echo $unit ?></th>
                    <th style="width:100px; text-align: right;">盘点后库存/<?php echo $unit ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($stockInventory->stockInventoryDetail as $val) { ?>
                    <tr>
                        <td style="text-align: center;">
                            <a target="_blank" title="<?php echo $val->stock_in_id ?>" href="/stockInList/view/?id=<?php echo $val->stock_in_id ?>&t=1"><?php echo $val->stockIn->code ?></a>
                        </td>
                        <td style="text-align: right;"><?php echo Utility::numberFormatToDecimal($val->quantity_before, 4) ?></td>
                        <td style="text-align: right;"><?php echo Utility::numberFormatToDecimal($val->quantity_frozen, 4) ?></td>
                        <td style="text-align: right;"><?php echo Utility::numberFormatToDecimal($val->quantity_active, 4) ?></td>
                        <td style="text-align: right;" class="text-red"><?php echo Utility::numberFormatToDecimal($val->quantity_diff, 4) ?></td>
                        <td style="text-align: right;"><?php echo Utility::numberFormatToDecimal(($val->quantity_active - $val->quantity_diff), 4) ?></td>
                        <td style="text-align: right;" class="text-red"><?php echo Utility::numberFormatToDecimal($val->quantity, 4) ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>
<div class="form-group">
    <label class="col-sm-2 control-label">备注</label>
    <div class="col-sm-10">
        <p class="form-control-static text-red"><?php echo $stockInventory->remark ?></p>
    </div>
</div>