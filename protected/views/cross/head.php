<div class="form-group">
    <label for="type" class="col-sm-2 control-label">销售合同编号</label>
    <div class="col-sm-4">
        <p class="form-control-static"><a target="_blank" href="/businessConfirm/detail?t=1&id=<?php  echo $data['contract_id']?>"><?php echo $data['contract_code'] ?></a></p>
    </div>
    <label for="type" class="col-sm-2 control-label">下游合作方</label>
    <div class="col-sm-4">
        <p class="form-control-static"><a target="_blank" href="/partner/detail?t=1&id=<?php  echo $data['partner_id']?>"><?php echo $data['partner_name'] ?></a></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">项目编号</label>
    <div class="col-sm-4">
        <p class="form-control-static"><a target="_blank" href="/project/detail?t=1&id=<?php  echo $data['project_id']?>"><?php echo $data['project_code'] ?></a></p>
    </div>
    <label for="type" class="col-sm-2 control-label">交易主体</label>
    <div class="col-sm-4">
        <p class="form-control-static"><a target="_blank" href="/corporation/detail?t=1&id=<?php  echo $data['corporation_id']?>"><?php echo $data['corporation_name'] ?></a></p>
    </div>
    
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">品名</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $data['goods_name'] ?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">已发货数量</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $data["delivery_quantity"] ?></p>
    </div>
    
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">合同数量</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $data['contract_quantity'] ?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">已出库数量</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $data["out_quantity"] ?></p>
    </div>
</div>