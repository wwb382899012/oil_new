
            <div class="form-group">
                <label class="col-sm-2 control-label">银行流水编号</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $bankFlow->code ?></p>
                </div>
                <label class="col-sm-2 control-label">交易主体</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><a href="/corporation/detail/?id=<?php echo $bankFlow->corporation->corporation_id ?>"><?php echo $bankFlow->corporation->name ?></a></p>
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-2 control-label">收款银行</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><?php echo $bankFlow->bank_name ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">银行账户名</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><?php echo $bankFlow->account_name ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">付款公司</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $bankFlow->pay_partner ?></p>
                </div>
                <label class="col-sm-2 control-label">收款时间</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $bankFlow->receive_date ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">付款银行</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $bankFlow->pay_bank ?></p>
                </div>
                <label class="col-sm-2 control-label">收款金额</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo number_format($bankFlow->amount/100, 2) ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">币种</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $this->map['currency'][$bankFlow->currency]['name'] ?></p>
                </div>
                <label class="col-sm-2 control-label">汇率</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $bankFlow->exchange_rate ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">备注</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><?php echo $bankFlow->remark ?></p>
                </div>
            </div>
