<div class="form-group">
    <label for="type" class="col-sm-2 control-label">采购合同编号</label>
    <div class="col-sm-4">
        <p class="form-control-static"><a target="_blank" href="/businessConfirm/detail?t=1&id=<?php  echo $data['contract_id']?>"><?php echo $data['contract_code'] ?></a></p>
    </div>
    <label for="type" class="col-sm-2 control-label">锁价维度</label>
    <div class="col-sm-4">
        <p class="form-control-static text-red"><?php echo $data["lock_type_name"] ?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">品名</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $data['goods_name'] ?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">单价</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $data["price"] ?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">合同数量</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $data['contract_quantity'] ?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">已锁数量</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $data["lock_quantity"] ?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">价格方式</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $data['price_type'] ?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">采购币种</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $data["contract_currency"] ?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">计价公式</label>
    <div class="col-sm-10">
        <p class="form-control-static"><?php echo $data['formula'] ?></p>
    </div>
</div>