
<div class="z-card">
    <h3 class="z-card-header"><?php echo $contract['type']==1?'采购':'销售';?>合同详情</h3>
    <div class="z-card-body">
        <div class="busi-detail">
            <div class="flex-grid form-group">
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed"><?php echo $contract['type']==1?'采购':'销售';?>合同编号:</span>
                    <span class="form-control-static line-h--text">
                        <a class="text-link" href="/contract/detail?id=<?php echo $contract['contract_id'];?>&t=1" target="blank"><?php echo $contract['contract_code'];?></a>
                    </span>
                </label>
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed">项目编号:</span>
                    <span class="form-control-static line-h--text">
                        <a class="text-link" href="/project/detail?id=<?php echo $contract['project_id'];?>&t=1" target="blank"><?php echo $contract['project_code'];?></a>
                    </span>
                </label>
            </div>
            <div class="flex-grid form-group">
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed"><?php echo $contract['type']==1?'采购':'销售';?>合同类型:</span>
                    <span class="form-control-static line-h--text">
                        <?php 
                        if($contract['type']==1)
                            echo Map::$v['contract_category_buy_type'][$contract['category']];
                        else 
                            echo Map::$v['contract_category_sell_type'][$contract['category']];
                        if(!empty($contract['agent_type'])&&$contract['type']==1){
                            echo '（'.Map::$v['buy_agent_type'][$contract['agent_type']].'）';
                        }
                        ?>
                    </span>
                </label>
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed">交易主体:</span>
                    <span class="form-control-static line-h--text">
                        <a class="text-link" href="/corporation/detail?id=<?php echo $contract['corporation_id'];?>&t=1" target="blank"><?php echo $contract['corporation_name'];?></a>
                    </span>
                </label>
            </div>
            <div class="flex-grid form-group">
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed"><?php echo $contract['type']==1?'上游':'下游';?>合作方:</span>
                    <span class="form-control-static line-h--text">
                        <a class="text-link" href="/partner/detail?id=<?php echo $contract['partner_id'];?>&t=1" target="blank"><?php echo $contract['partner_name'];?></a>
                    </span>
                </label>
                <?php if($contract['type']==1&&!empty($contract['agent_name'])):?>
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed">采购代理商:</span>
                    <span class="form-control-static line-h--text">
                        <a class="text-link" href="/partner/detail?id=<?php echo $contract['agent_id'];?>&t=1" target="blank"><?php echo $contract['agent_name'];?></a>
                    </span>
                </label>
                <?php endif;?>
            </div>
            <div class="flex-grid form-group">
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed"><?php echo $contract['type']==1?'采购':'销售';?>币种:</span>
                    <span class="form-control-static line-h--text">
                        <?php echo Map::$v['contract_settlement_currency'][$contract['currency']]['name'];?>
                    </span>
                </label>
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed"><?php echo $contract['type']==1?'采购':'销售';?>价格方式:</span>
                    <span class="form-control-static line-h--text">
                        <?php echo Map::$v['price_type'][$contract['price_type']];?>
                    </span>
                </label>
            </div>
            <div class="flex-grid form-group">
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed"><?php echo $contract['type']==1?'采购':'销售';?>计价公式:</span>
                    <span class="form-control-static line-h--text">
                        <?php echo $contract['formula'];?>
                    </span>
                </label>
                <label class="col col-count-2 field flex-grid align-start">
                    <span class="line-h--text w-fixed"><?php echo $contract['type']==1?'采购':'销售';?>合同负责人:</span>
                    <span class="form-control-static line-h--text">
                        <?php echo $contract['manager_user_name'];?>
                    </span>
                </label>
            </div>
            <div class="form-group">
                <table class="table">
                    <thead>
                        <tr>
                            <th>品名</th>
                            <th>计价标的</th>
                            <th>采购溢短装比例</th>
                            <th>数量</th>
                            <th>单位</th>
                            <th><?php echo $contract['type']==1?'采购':'销售';?>单价</th>
                            <th width="30%"><?php echo $contract['type']==1?'采购':'销售';?>总价</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($contract['items'])):?>
                            <?php foreach ($contract['items'] as $k=>$v):?>
                            <tr>
                                <td><?php echo $v['goods_name'];?></td>
                                <td><?php echo $v['refer_target'];?></td>
                                <td><?php echo $v['more_or_less_rate']*100;?>%</td>
                                <td><?php echo $v['quantity']['quantity'];?></td>
                                <td><?php  echo Map::$v['goods_unit'][$v['quantity']['unit']]['name'];?></td>
                                <td><?php echo Map::$v['contract_settlement_currency'][$contract['currency']]['sign'].number_format($v['price']/100, 2);?></td>
                                <td><?php echo Map::$v['contract_settlement_currency'][$contract['currency']]['sign'].number_format($v['amount']/100, 2);?></td>
                            </tr>
                            <?php endforeach;?>
                        <?php endif;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>