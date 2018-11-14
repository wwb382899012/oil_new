
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo $contract['type']==1?'采购':'销售';?>合同详情</h3>
        <div class="pull-right box-tools">
            <?php if(!$this->isExternal && !$hideBackBtn){ ?>
            <button type="button" class="btn btn-default btn-sm" onclick="back()">
                返回
            </button>
            <?php } ?>
        </div>
    </div>
    <div class="box-body form-horizontal">
        <div class="form-group">
            <div class="col-sm-2 control-label">
                <?php echo $contract['type']==1?'采购':'销售';?>合同编号
            </div>
            <div class="col-sm-4">
                <p class="form-control-static"><a href="/contract/detail?id=<?php echo $contract['contract_id'];?>&t=1" target="blank"><?php echo $contract['contract_code'];?></a></p>
            </div>
            <div class="col-sm-2 control-label">
                项目编号
            </div>
            <div class="col-sm-4">
                <p class="form-control-static"><a href="/project/detail?id=<?php echo $contract['project_id'];?>&t=1" target="blank"><?php echo $contract['project_code'];?></a></p>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-2 control-label">
                <?php echo $contract['type']==1?'采购':'销售';?>合同类型
            </div>
            <div class="col-sm-4">
                <p class="form-control-static">
                <?php 
                if($contract['type']==1)
                    echo Map::$v['contract_category_buy_type'][$contract['category']];
                else 
                    echo Map::$v['contract_category_sell_type'][$contract['category']];
                if(!empty($contract['agent_type'])&&$contract['type']==1){
                echo '（'.Map::$v['buy_agent_type'][$contract['agent_type']].'）';
                }
                ?>
                </p>
            </div>
            <div class="col-sm-2 control-label">
                交易主体
            </div>
            <div class="col-sm-4">
                <p class="form-control-static"><a href="/corporation/detail?id=<?php echo $contract['corporation_id'];?>&t=1" target="blank"><?php echo $contract['corporation_name'];?></a></p>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-2 control-label">
                <?php echo $contract['type']==1?'上游':'下游';?>合作方
            </div>
            <div class="col-sm-4">
                <p class="form-control-static"><a href="/partner/detail?id=<?php echo $contract['partner_id'];?>&t=1" target="blank"><?php echo $contract['partner_name'];?></a></p>
            </div>
           <?php if($contract['type']==1&&!empty($contract['agent_name'])):?>
            <div class="col-sm-2 control-label">
            采购代理商
            </div>
            <div class="col-sm-4">
                <p class="form-control-static"><a href="/partner/detail?id=<?php echo $contract['agent_id'];?>&t=1" target="blank"><?php echo $contract['agent_name'];?></a></p>
            </div>
            <?php endif;?>
        </div>
        <div class="form-group">
            <div class="col-sm-2 control-label">
                 <?php echo $contract['type']==1?'采购':'销售';?>币种
            </div>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo Map::$v['contract_settlement_currency'][$contract['currency']]['name'];?></p>
            </div>
            <div class="col-sm-2 control-label">
                 <?php echo $contract['type']==1?'采购':'销售';?>价格方式
            </div>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo Map::$v['price_type'][$contract['price_type']];?></p>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-2 control-label">
                 <?php echo $contract['type']==1?'采购':'销售';?>计价公式
            </div>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $contract['formula'];?></p>
            </div>
            <div class="col-sm-2 control-label">
                 <?php echo $contract['type']==1?'采购':'销售';?>合同负责人
            </div>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $contract['manager_user_name'];?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped table-bordered">
                    <tr>
                        <td>品名</td>
                        <td>计价标的</td>
                        <td>采购溢短装比例</td>
                        <td>数量</td>
                        <td>单位</td>
                        <td><?php echo $contract['type']==1?'采购':'销售';?>单价</td>
                        <td width="30%"><?php echo $contract['type']==1?'采购':'销售';?>总价</td>
                    </tr>
                    <?php if(!empty($contract['items'])):?>
                      <?php foreach ($contract['items'] as $k=>$v):?>
                        <tr>
                            <td><?php echo $v['goods_name'];?></td>
                            <td><?php echo $v['refer_target'];?></td>
                            <td><?php echo $v['more_or_less_rate']*100;?>%</td>
                            <td><?php echo $v['quantity']['quantity'];?></td>
                            <td><?php  echo Map::$v['goods_unit'][$v['quantity']['unit']]['name'];?></td>
                            <td><?php echo Map::$v['contract_settlement_currency'][$contract['currency']]['sign'].number_format($v['price']/100, 2);?></td>
                            <td width="30%"><?php echo Map::$v['contract_settlement_currency'][$contract['currency']]['sign'].number_format($v['amount']/100, 2);?></td>
                        </tr>
                        <?php endforeach;?>
                  <?php endif;?>
                </table>
            </div>
        </div>
    </div>
    </div>

    <script>
        function back() {
            /*if( document.referrer === '')
                location.href=/<?php echo $this->getId(); ?>/;
            else
                history.back();*/
            location.href=/<?php echo $this->getId(); ?>/;
        }
    </script>