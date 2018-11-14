<div class="form-group">
    <label for="type" class="col-sm-2 control-label">调货单编号</label>
    <div class="col-sm-4">
        <p class="form-control-static"><a target="_blank" href="/cross/detail?t=1&id=<?php  echo $data['detail_id']?>"><?php echo $data['relation_cross_code'] ?></a></p>
    </div>
    <label for="type" class="col-sm-2 control-label">被借采购合同编号</label>
    <div class="col-sm-4">
        <p class="form-control-static"><a target="_blank" href="/businessConfirm/detail?t=1&id=<?php  echo $data['buy_id']?>"><?php echo $data['buy_code'] ?></a></p>
    </div>
</div>
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
    <label for="type" class="col-sm-2 control-label">品名</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $data['goods_name'] ?></p>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">调货原因</label>
    <div class="col-sm-10">
        <p class="form-control-static"><?php echo $data['reason'] ?></p>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">调货日期</label>
    <div class="col-sm-10">
        <p class="form-control-static"><?php echo $data['cross_date'] ?></p>
    </div>
</div>