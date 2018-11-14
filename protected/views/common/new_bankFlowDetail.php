<li>
    <label>银行流水编号：</label>
    <p><?php echo $bankFlow->code ?></p>
</li>
<li>
    <label>交易主体：</label>
    <p>
        <a href="/corporation/detail/?id=<?php echo $bankFlow->corporation->corporation_id ?>&t=1" target="_blank"><?php echo $bankFlow->corporation->name ?></a>
    </p>
</li>
<li>
    <label>收款银行：</label>
    <p class="form-control-static"><?php echo $bankFlow->bank_name ?></p>
</li>
<li>
    <label>银行账户名：</label>
    <p class="form-control-static"><?php echo $bankFlow->account_name ?></p>
</li>
<li>
    <label>付款公司：</label>
    <p class="form-control-static"><?php echo $bankFlow->pay_partner ?></p>
</li>
<li>
    <label>收款时间：</label>
    <p class="form-control-static"><?php echo $bankFlow->receive_date ?></p>
</li>
<li>
    <label>付款银行：</label>
    <p class="form-control-static"><?php echo $bankFlow->pay_bank ?></p>
</li>
<li>
    <label>收款金额：</label>
    <p class="form-control-static"><?php echo number_format($bankFlow->amount / 100, 2) ?></p>
</li>
<li>
    <label>币种：</label>
    <p class="form-control-static"><?php echo $this->map['currency'][$bankFlow->currency]['name'] ?></p>
</li>
<li>
    <label>汇率：</label>
    <p class="form-control-static"><?php echo $bankFlow->exchange_rate ?></p>
</li>

