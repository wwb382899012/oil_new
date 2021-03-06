<?php

/**
 * Created by youyi000.
 * DateTime: 2016/6/27 16:36
 * Describe：
 */
class Project extends BaseActiveRecord {

    const STATUS_STOP = - 9;//项目终止

    const STATUS_REJECT = - 1;//审核拒绝

    const STATUS_NEW = 0;//新增加
    const STATUS_BACK = 1;//项目撤回

    const STATUS_SUBMIT = 10;//提交到商务确认中

    const STATUS_CONTRACT_BACK = 23;//合同初审驳回
    const STATUS_CONTRACT_REJECT = 24;//合同初审拒绝
    const STATUS_CONTRACT_CHECKING = 25;//合同初审中
    // const STATUS_BUSINESS_CONTRACT_CHECKING=25;//商务合同初审中
    // const STATUS_LAW_FIN_CONTRACT_CHECKING=26;//法务财务合同初审中

    const STATUS_PAY_CONDITION = 30;//最终合同，trashed
    const STATUS_UP_DOWN_CONTRACT_STAMP = 35;//上下游合同签章

    const STATUS_STAMP_BACK = 43;//签章合同审核驳回
    const STATUS_STAMP_REJECT = 44;//签章合同审核拒绝
    const STATUS_STAMP_CHECKING = 45;//签章合同审核中
    // const STATUS_BUSINESS_STAMP_CHECKING=45;//商务签章合同审核中
    // const STATUS_FINANCE_STAMP_CHECKING=46;//财务签章合同审核中

    const STATUS_OUR_CONTRACT_UPLOAD = 50;//我方盖章合同上传中

    const STATUS_CONTRACT_FILE = 55;//双签纸质合同上传中

    const STATUS_PAY_CONFIRM = 60;//下游保证金确认
    const STATUS_PAY_REQUEST = 61;//上游预付款处理中

    const STATUS_UP_PREPAY_CONFIRM = 70;//上游预付款已确认


    //const STATUS_DETAIL=20;//初审完成商务完善信息中

    //const STATUS_PENDING_RISK=22; //风控已排期

    //const STATUS_IRR_CHECKING=25;//IRR审核中

    //const STATUS_CONTRACT=30;//合同制作中
    //const STATUS_CONTRACT_BACK=31; //合同审核驳回

    //const STATUS_CONTRACT_REJECT=32; //合同审核拒绝

    //const STATUS_CONTRACT_CHECKING=35; //合同审核中

    //const STATUS_CONTRACT_FILE=41;//合同归档中

    /*const STATUS_CONTRACT_FILE_BACK=42; //合同归档审核驳回

    const STATUS_CONTRACT_FILE_REJECT=43; //合同归档审核拒绝

    const STATUS_CONTRACT_FILE_CHECKING=45;//合同归档审核中

    const STATUS_CONTRACT_DONE=49;//合同归档审核完成*/

    //const STATUS_PAY_CONFIRM=50;//付款确认
    /*const STATUS_UP_PAY=50;//上游付款
    const STATUS_IMPREST_DONE=51;//上游预付款完成*/

    const STATUS_SETTLING = 80;//结算中
    const STATUS_SETTLE_REJECTED = 81;//结算审核退回
    const STATUS_SETTLE_CHECKING = 83;//结算审核中
    const STATUS_SETTLE_CONFIRM = 85;//结算审核完成
    // const STATUS_SETTLE_FILE=86;//结算单归档
    // const STATUS_SETTLE_FILE_REJECTED=87;//结算单归档审核拒绝
    // const STATUS_SETTLE_FILE_CHECKING=88;//结算单归档审核中
    // const STATUS_SETTLE_FILE_DONE=89;//结算单归档审核完成

    /*const STATUS_UP_SETTLE=52;//上游结算
    const STATUS_UP_SETTLE_CHECKING=53;//上游结算审核中
    const STATUS_UP_SETTLE_DONE=54;//上游结算审核完成
    const STATUS_UP_SETTLE_FILE=55;//上游结算单归档
    const STATUS_UP_SETTLE_FILE_CHECKING=56;//上游结算单归档审核中
    const STATUS_UP_SETTLE_FILE_DONE=57;//上游结算单归档审核完成

    const STATUS_DOWN_SETTLE=61;//下游结算
    const STATUS_DOWN_SETTLE_CHECKING=62;//下游结算审核中
    const STATUS_DOWN_SETTLE_DONE=63;//下游结算审核完成
    const STATUS_DOWN_SETTLE_FILE=64;//下游结算单归档
    const STATUS_DOWN_SETTLE_FILE_CHECKING=65;//下游结算单归档审核中
    const STATUS_DOWN_SETTLE_FILE_DONE=66;//下游结算单归档审核完成*/


    /*const STATUS_PAY_REQUEST=51;//付款申请

    const STATUS_PAY_BACK=52;//付款审核驳回

    const STATUS_PAY_CHECKING=54;//付款审核中

    const STATUS_PAY_DONE=56;//付款完成*/

    //const STATUS_SETTLING=60;//结算中

    //const STATUS_SETTLEMENT_CHECKING=63;//结算审核中

    /*const STATUS_SETTLE_DONE=70;//项目结清

    const STATUS_UP_INVOICE=71;//上游开票中

    const STATUS_DOWN_INVOICE=72;//下游开票中
    const STATUS_INVOICE_CHECKING=73;//下游开票审核中
    const STATUS_INVOICE_DONE=74;//下游开票审核通过
    const STATUS_TAX_FEEDBACK=75;//税票条件反馈中
    const STATUS_TAX_CHECKING=76;//税票条件审核中
    const STATUS_TAX_DONE=77;//税票条件审核通过

    const STATUS_INVOICE_OPENED=80;//发票已开具
    const STATUS_INVOICE_EXPRESS=81;//发票已发快递
    const STATUS_INVOICE_CONFIRM=82;//下游确认收票*/

    const STATUS_DONE = 99;//项目完成

    public $oldStatus = 0;

    public $oldStatusTime;


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_project';
    }

    public function relations()
    {
        return array(
            "base" => array(self::HAS_ONE, "ProjectBase", "project_id"),//项目发起信息
            "contracts" => array(self::HAS_MANY, "Contract", "project_id"),//项目合同信息
            "attachments" => array(self::HAS_MANY, "ProjectAttachment", "project_id"),
            "corporation" => array(self::BELONGS_TO, "Corporation", "corporation_id"),
            "manager"=>array(self::BELONGS_TO, "SystemUser",array('manager_user_id'=>'user_id')), // 负责人
            "creator"=>array(self::BELONGS_TO, "SystemUser",array('create_user_id'=>'user_id')), // 创建人
            "storehouse"=>array(self::HAS_ONE, 'Storehouse', array('store_id'=>'storehouse_id')), // 创建人

        );
    }

    protected function beforeDelete()
    {
        $res= parent::beforeDelete(); // TODO: Change the autogenerated stub

        if(!$res)
            return false;

        $res=$this->base->delete();
        if(!$res)
            return false;
        foreach ($this->contracts as $model)
        {
            $res=$model->delete();
            if(!$res)
                return false;
        }

        $attachments=$this->attachments;//触发加载数据

        return true;
    }

    protected function afterDelete()
    {
        parent::afterDelete(); // TODO: Change the autogenerated stub

        foreach ($this->attachments as $model)
        {
            $model->delete();//附件删除失败不影响整体的删除
        }

    }

    /**
     * 根据项目编码查找项目
     * @param $projectCode
     * @return CActiveRecord
     */
    public function findByCode($projectCode)
    {
        return $this->find("project_code='".$projectCode."'");
    }


    protected function afterFind()
    {
        parent::afterFind(); // TODO: Change the autogenerated stub
        $this->oldStatus = $this->status;
        $this->oldStatusTime = $this->status_time;
    }

    protected function beforeSave()
    {
        $this->oldStatus = $this->getOldAttribute("status");
        $this->oldStatusTime = $this->getOldAttribute("status_time");

        if (!$this->isNewRecord)
        {
            if ($this->status != $this->oldStatus)
            {
                $timeSpan = strtotime("now") - strtotime($this->oldStatusTime);
                $this->addStatusLog($this->oldStatus, $timeSpan);
            }
        }

        return parent::beforeSave(); // TODO: Change the autogenerated stub
    }

    /**
     * 添加项目状态变更记录
     * @param $oldStatus
     * @param $timeSpan
     */
    public function addStatusLog($oldStatus, $timeSpan)
    {
        $sql = "insert into t_project_log(project_id,field_name,old_value,new_value,timespan,remark,create_user_id,create_time)
              values (" . $this->project_id . ",'status'," . $oldStatus . "," . $this->status . "," . $timeSpan . ",'','" . $this->update_user_id . "',now());";
        Utility::executeSql($sql);
    }




    /**
     * 获取附件信息
     * @param $id
     * @param $type
     * @return array
     */
    public static function getAttachment($id, $type = '') {
        if (empty($id)) {
            return array();
        }
        if (!empty($type)) {
            $type = ' and type=' . $type;
        }

        $sql = "select * from t_project_attachment where project_id=" . $id . " and status=1" . $type . " order by type asc";
        $data = Utility::query($sql);
        $attachments = array();
        foreach ($data as $v) {
            //$attachments[$v["type"]] = $v;
            $attachments[$v["type"]][] = $v;
        }

        return $attachments;
    }

    /**
     * 获取所有补充材料附件信息
     * @param $id
     * @return array
     */
    public static function getSupplementAttachment($id) {
        if (empty($id)) {
            return array();
        }
        $sql = "select * from t_project_attachment where type=61 and project_id=" . $id . " and status>=0 order by id asc";
        $data = Utility::query($sql);
        $attachments = array();
        foreach ($data as $v) {
            $attachments[$v["id"]] = $v;
        }

        return $attachments;
    }

    /**
     * 保存放款计划
     * @param $payments
     */
    public function generatePayments($payments) {
        if (Utility::isEmpty($payments)) {
            return;
        }

        $sql = "select * from t_project_pay_plan where project_id=" . $this->project_id . " order by pay_date asc";
        $data = Utility::query($sql);
        $p = array();
        foreach ($data as $v) {
            $p[$v["plan_id"]] = $v["plan_id"];
        }
        $sqls = array();
        $values = array();
        foreach ($payments as $v) {
            //$remark=array("conditions"=>$v["conditions"],"other"=>$v["other"]);
            $remark = '{"conditions":' . json_encode($v["conditions"]) . ',"other":"' . $v["other"] . '"}';
            if ($v["amount"] < 0) {
                $type = 1;
            } else {
                $type = 0;
            }
            if (array_key_exists($v["id"], $p)) {
                if ($v["status"] == 0) {
                    $sqls[] = "update t_project_pay_plan set 
                        pay_date='" . $v["pay_date"] . "',rate=" . $v["rate"] . ",
                        amount=" . $v["amount"] . ",pay_type=" . $v["pay_type"] . ",
                        period=" . $v["period"] . ",pay_days=" . $v["pay_days"] . ",
                        remark='" . $remark . "',type=" . $type . ",
                        update_time=now()
                        where plan_id=" . $v["id"] . "";
                }
                unset($p[$v["id"]]);
            } else {
                if ($v["status"] == 0) {
                    $values[] = "(" . $this->project_id . ",'" . $v["pay_date"] . "'," . $v["pay_type"] . "," . $type . "," . $v["pay_days"] . "," . $v["period"] . "," . $v["rate"] . "," . $v["amount"] . ",now(),now(),'" . $remark . "')";
                }
            }
        }
        //var_dump($sqls);
        $sql = "";
        if (count($sqls) > 0) {
            $sql .= implode(";", $sqls) . ";";
        }
        if (count($values) > 0) {
            $sql .= "insert into t_project_pay_plan(project_id,pay_date,pay_type,type,pay_days,period,rate,amount,create_time,update_time,remark) values " . implode(",", $values) . ";";
        }
        if (count($p) > 0) {
            $sql .= "delete from t_project_pay_plan where plan_id in(" . implode(",", $p) . ");";
        }
        if (!empty($sql)) {
            Utility::execute($sql);
        }
    }

    /**
     * 生成下游收款计划，当$type=0时，表示只处理返还计划
     * @param $plans
     * @param $type
     */
    public function generateReturnPlans($plans, $type = - 1) {
        if (Utility::isEmpty($plans)) {
            return;
        }

        $sql = "select * from t_return_plan where project_id=" . $this->project_id . "";
        if ($type > - 1) {
            $sql .= " and type=" . $type . "";
        }
        $sql .= " order by return_date asc";
        $data = Utility::query($sql);
        $p = array();
        $t = array();
        foreach ($data as $v) {
            $p[$v["id"]] = $v["id"];
            $t[$v["type"]] = $v["id"];
        }
        $sqls = array();
        $values = array();
        foreach ($plans as $v) {
            if ($v["type"] > 0) {
                if (array_key_exists($v["type"], $t)) {
                    if ($v["status"] == 0) {
                        $sqls[] = "update t_return_plan set 
                        return_date='" . $v["return_date"] . "',rate=" . $v["rate"] . ",
                        amount=" . $v["amount"] . ",receive_type=" . $v["receive_type"] . ",
                        period=0,receive_days=" . $v["receive_days"] . ",
                        update_time=now()
                        where id=" . $t[$v["type"]] . "";
                    }
                    unset($p[$t[$v["type"]]]);
                } else {
                    if ($v["status"] == 0) {
                        $values[] = "(" . $this->project_id . "," . $v["type"] . ",0,'" . $v["return_date"] . "'," . $v["rate"] . "," . $v["amount"] . ",now(),now())";
                    }
                }
            } else {
                if (array_key_exists($v["id"], $p)) {
                    if ($v["status"] == 0) {
                        $sqls[] = "update t_return_plan set 
                        return_date='" . $v["return_date"] . "',rate=" . $v["rate"] . ",
                        amount=" . $v["amount"] . ",receive_type=" . $v["receive_type"] . ",
                        period=" . $v["period"] . ",receive_days=" . $v["receive_days"] . ",
                        update_time=now()
                        where id=" . $v["id"] . "";
                    }
                    unset($p[$v["id"]]);
                } else {
                    if ($v["status"] == 0) {
                        $values[] = "(" . $this->project_id . "," . $v["period"] . ",'" . $v["return_date"] . "'," . $v["receive_type"] . "," . $v["receive_days"] . "," . $v["rate"] . "," . $v["amount"] . ",now(),now())";
                    }
                }
            }

        }

        $sql = "";
        if (count($sqls) > 0) {
            $sql .= implode(";", $sqls) . ";";
        }
        if (count($values) > 0) {
            $sql .= "insert into t_return_plan(project_id,period,return_date,receive_type,receive_days,rate,amount,create_time,update_time) values " . implode(",", $values) . ";";
        }
        if (count($p) > 0) {
            $sql .= "delete from t_return_plan where id in(" . implode(",", $p) . ");";
        }

        if (!empty($sql)) {
            Utility::execute($sql);
        }
    }


    /**
     * 计算实际价差及IRR
     */
    public function computeActualIRR($upSettle, $downSettle, $payments, $plans) {
        /*$sql="select
                  a.*,
                  u.settle_id as up_settle_id,u.price as up_price,u.quantity as up_quantity,u.amount as up_amount,
                  d.settle_id as down_settle_id,d.price as down_price,d.quantity as down_quantity,d.amount as down_amount
                from t_project a
                  left join t_settlement u on a.project_id=u.project_id and u.type=1
                  left join t_settlement d on a.project_id=d.project_id and d.type=2
                where a.project_id=".$this->project_id."";
        $data=Utility::query($sql);
        if(Utility::isEmpty($data))
            return;
        $finance=$data[0];*/
        /*if(empty($finance["up_settle_id"]) || empty($finance["down_settle_id"]))
        {
            $detail=ProjectService::getDetail($this->project_id);
            $detail=$detail[0];
            if(empty($finance["up_settle_id"]))
            {
                $finance["up_price"]=$detail["up_price"];
                $finance["up_quantity"]=$detail["up_quantity"];
                $finance["up_amount"]=$detail["up_amount"];
            }
            if(empty($finance["down_settle_id"]))
            {
                $finance["down_price"]=$detail["down_price"];
                $finance["down_quantity"]=$detail["down_quantity"];
                $finance["down_amount"]=$detail["down_amount"];
            }
        }*/

        /*self.price_margin(Math.round((parseFloat(self.down_price())/parseFloat(self.up_price())-1.0)*10000)/10000);
        self.price_margin_tax(Math.round((self.price_margin()-[self.price_margin()/1.17*0.17*1.12+(self.price_margin()+2)*0.0003])*10000)/10000);
        if(self.planItems().length>0)
            self.month_margin_tax(Math.round((self.price_margin_tax()/self.planItems()[self.planItems().length-1].receive_days()*30)*10000)/10000);

*/
        if (count($plans) > 0) {
            foreach ($plans as $key => $value) {
                $gap[$key] = $value['receive_days'];
            }
            $maxIndex = array_search(max($gap), $gap);
        }
        $price_margin = $downSettle["price"] / $upSettle["price"] - 1;
        if ($upSettle["price"] != 0 && count($plans) > 0 && $plans[$maxIndex]['receive_days'] > 0) {
            $this->price_margin_actual = round(($price_margin) / $plans[$maxIndex]['receive_days'] * 30, 4);
        } else {
            $this->price_margin_actual = 0;
        }

        $this->price_margin_tax_actual = round($price_margin - ($price_margin / 1.17 * 0.17 * 1.12 + ($price_margin + 2) * 0.0003), 4);

        //print_r($this->price_margin_tax_actual);die;
        if (count($plans) > 0 && $plans[$maxIndex]['receive_days'] > 0) {
            $this->month_margin_tax_actual = round($this->price_margin_tax_actual / $plans[$maxIndex]['receive_days'] * 30, 4);
        } else {
            $this->month_margin_tax_actual = 0;
        }
        /*if($this->period>0)
            $this->irr_actual=round($this->price_margin_actual/$this->period*365,4);
        else
            $this->irr_actual==0;*/
        if (count($payments) > 0 && count($plans > 0)) {
            $this->irr_actual = ProjectService::computeXIRR($payments, $plans);
        }
    }

    public function bakPayments() {
        $payments = ProjectService::getUpPayments($this->project_id, 1);
        if (Utility::isNotEmpty($payments)) {
            return;
        }

        $payments = ProjectService::getUpPayments($this->project_id);
        $values = array();

        foreach ($payments as $k => $v) {
            unset($payments[$k]["plan_id"]);
            $payments[$k]["create_time"] = date("Y-m-d H:i:s");
            $payments[$k]["update_time"] = date("Y-m-d H:i:s");
            $payments[$k]["create_user_id"] = $this->update_user_id;
            $payments[$k]["update_user_id"] = $this->update_user_id;
            $values[] = "('" . implode("','", $payments[$k]) . "')";
        }

        if (count($values) < 1) {
            return;
        }

        $sql = "insert into t_project_pay_plan_bak(" . implode(",", array_keys($payments[0])) . ") values ";
        $sql .= implode(",", $values);
        Utility::execute($sql);

    }

    public function bakReturnPlans() {
        $plans = ProjectService::getDownReturnPlans($this->project_id, - 1, 1);

        if (Utility::isNotEmpty($plans)) {
            return;
        }

        $plans = ProjectService::getDownReturnPlans($this->project_id);
        $values = array();
        foreach ($plans as $k => $v) {
            unset($plans[$k]["id"]);
            $plans[$k]["create_time"] = date("Y-m-d H:i:s");
            $plans[$k]["update_time"] = date("Y-m-d H:i:s");
            $plans[$k]["create_user_id"] = $this->update_user_id;
            $plans[$k]["update_user_id"] = $this->update_user_id;
            $values[] = "('" . implode("','", $plans[$k]) . "')";
        }
        if (count($values) < 1) {
            return;
        }

        $sql = "insert into t_return_plan_bak(" . implode(",", array_keys($plans[0])) . ") values ";
        $sql .= implode(",", $values);
        Utility::execute($sql);

    }

    /**
     * 通过项目获取所有的审核记录
     */
    public function getCheckLogByProject($projectId) {
        if (empty($projectId)) {
            return array();
        }
        $checklogIds = array($projectId);
        //合同审核信息
        //上下游签章合同审核
        $contracts = Contract::getByProject($projectId);
        foreach ($contracts as $v) {
            array_push($checklogIds, $v['contract_id']);
        }
        //结算审核==项目id
        //下游发票审核
        $invoices = Invoice::getByProject($projectId);
        foreach ($invoices as $v) {
            array_push($checklogIds, $v['invoice_id']);
        }
        //上游付款审核
        $payRequests = PayRequest::getByProject($projectId);
        foreach ($payRequests as $v) {
            array_push($checklogIds, $v['apply_id']);
        }

        return $checklogIds;
    }
}