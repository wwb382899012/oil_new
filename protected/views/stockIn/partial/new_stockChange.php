<div class="z-card">
    <div class="content-wrap-title">
        <h3 class="z-card-header">
            <b>入库单库存变化详情</b>
            <p class="content-wrap-expand pull-right box-tools"><span>收起</span><i class="icon icon-shouqizhankai"></i></p>
        </h3>
    </div>
    <div>
        <?php if(1 == count($stockDetail)):?>
            <div>
                <?php if(Utility::isNotEmpty($stockDetail)): ?>
                    <?php foreach($stockDetail as $key => $row): ?>
                        <div class="tab-pane <?php echo ($key == 0) ? "active" : "" ?>"
                             id="goods_<?php echo $row['goods_id']; ?>">
                            <?php if(Utility::isNotEmpty($row['stock_detail'])): ?>
                                <table class="table table-custom">
                                    <thead>
                                    <tr>
                                        <th>品名</th>
                                        <th>日期</th>
                                        <th>单据编号</th>
                                        <th>出入库类型</th>
                                        <th>出入库方式</th>
                                        <th>数量</th>
                                        <th>库存数量</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($row['stock_detail'] as $v) : ?>
                                        <tr>
                                            <td><?php echo $row['goods_name']; ?></td>
                                            <td><?php echo $v['op_date']; ?></td>
                                            <td>
                                                <?php
                                                if($v['type'] == StockLog::TYPE_IN){
                                                    $code = '<a href="#">'.$v['code'].'</a>';
                                                }elseif($v['type'] == StockLog::TYPE_OUT){
                                                    $code = '<a target="_blank" href="/stockOutList/view?id='.$v['id'].'&t=1">'.$v['code'].'</a>';
                                                }
                                                if($v['method'] == StockLog::METHOD_STOCK_CHECK){
                                                    $code = '<a target="_blank" href="/stockInventory/detail?corp_id='.$v['corporation_id'].'&store_id='.$v['store_id'].'&goods_id='.$v['goods_id'].'&unit='.$v['unit'].'&t=1">'.$v['id'].'</a>';
                                                }
                                                if(!empty($v['cross_code'])){
                                                    $code .= '(<span class="text-red">'.$v['cross_code'].'</span>)';
                                                }
                                                echo $code;
                                                ?>
                                            </td>
                                            <td><?php echo $v['type_desc']; ?></td>
                                            <td><?php echo $v['method_desc']; ?></td>
                                            <td>
                                                <?php
                                                $quantityDesc = Utility::numberFormatToDecimal($v['quantity'], 4).Map::$v['goods_unit'][$v['unit']]['name'];
                                                echo $v['quantity'] > 0 ? '+'.$quantityDesc : $quantityDesc;
                                                ?>
                                            </td>
                                            <td><?php echo Utility::numberFormatToDecimal($v['quantity_balance'], 4).Map::$v['goods_unit'][$v['unit']]['name']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php else:?>
            <ul class="nav nav-tabs">
                <?php if(Utility::isNotEmpty($stockDetail)): ?>
                    <?php foreach($stockDetail as $key => $row) : ?>
                        <li class="<?php echo ($key == 0) ? "active" : "" ?>">
                            <a href="#goods_<?php echo $row['goods_id']; ?>"
                               data-toggle="tab"><?php echo $row['goods_name']; ?></a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <div class="tab-content">
                <?php if(Utility::isNotEmpty($stockDetail)): ?>
                    <?php foreach($stockDetail as $key => $row): ?>
                        <div class="tab-pane <?php echo ($key == 0) ? "active" : "" ?>"
                             id="goods_<?php echo $row['goods_id']; ?>">
                            <?php if(Utility::isNotEmpty($row['stock_detail'])): ?>
                                <table class="table table-custom">
                                    <thead>
                                    <tr>
                                        <th>日期</th>
                                        <th>单据编号</th>
                                        <th>出入库类型</th>
                                        <th>出入库方式</th>
                                        <th>数量</th>
                                        <th>库存数量</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($row['stock_detail'] as $v) : ?>
                                        <tr>
                                            <td><?php echo $v['op_date']; ?></td>
                                            <td>
                                                <?php
                                                if($v['type'] == StockLog::TYPE_IN){
                                                    $code = '<a href="#">'.$v['code'].'</a>';
                                                }elseif($v['type'] == StockLog::TYPE_OUT){
                                                    $code = '<a target="_blank" href="/stockOutList/view?id='.$v['id'].'&t=1">'.$v['code'].'</a>';
                                                }
                                                if($v['method'] == StockLog::METHOD_STOCK_CHECK){
                                                    $code = '<a target="_blank" href="/stockInventory/detail?corp_id='.$v['corporation_id'].'&store_id='.$v['store_id'].'&goods_id='.$v['goods_id'].'&unit='.$v['unit'].'&t=1">'.$v['id'].'</a>';
                                                }
                                                if(!empty($v['cross_code'])){
                                                    $code .= '(<span class="text-red">'.$v['cross_code'].'</span>)';
                                                }
                                                echo $code;
                                                ?>
                                            </td>
                                            <td><?php echo $v['type_desc']; ?></td>
                                            <td><?php echo $v['method_desc']; ?></td>
                                            <td>
                                                <?php
                                                $quantityDesc = Utility::numberFormatToDecimal($v['quantity'], 4).Map::$v['goods_unit'][$v['unit']]['name'];
                                                echo $v['quantity'] > 0 ? '+'.$quantityDesc : $quantityDesc;
                                                ?>
                                            </td>
                                            <td><?php echo Utility::numberFormatToDecimal($v['quantity_balance'], 4).Map::$v['goods_unit'][$v['unit']]['name']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif;?>

    </div>
</div>