<?php

/**
 * Desc:
 * User: susiehuang
 * Date: 2017/5/5 0005
 * Time: 16:22
 */
class ReceiveSerive {
    const RECEIVE_TYPE_BOND = 1; //保证金

    /**
     * @desc 获取上游收款确认头部信息
     * @param $projectId
     * @return array
     */
    /*public function getReceiveHead($projectId) {
        if ($this->type == 1) {
            $sql = "select {col} from t_project a
                left join t_partner p on a.up_partner_id=p.partner_id
                left join t_corporation c on a.corporation_id=c.corporation_id
                left join t_invoice i on a.project_id=i.project_id
                left join t_settlement su on a.project_id=su.project_id and su.type=1
                left join t_settlement sd on a.project_id=sd.project_id and sd.type=2
                left join t_project_pay_plan pp on pp.project_id=a.project_id
                left join t_pay_application pa on pa.plan_id=pp.plan_id and pa.status>0
                left join t_payment pm on pm.apply_id=pa.apply_id
                where a.project_id=" . $projectId . " {limit}";

            $fields = "a.project_id,a.project_name,p.partner_id,p.name as partner_name,c.corporation_id,c.name as corporation_name,
                sum(pp.amount) as total_amount, sum(pa.amount) as pay_amount, sum(pm.amount) as actual_amount,
                case when a.status>=" . Project::STATUS_SETTLE_CONFIRM . " then '已结算'
                else '未结算'
                end as p_status,
                case when i.status=1 then '是'
                else '否'
                end as invoice_status,
                case when su.status=" . Settlement::STATUS_DONE . " then '已结清'
                else '未结清'
                end as up_settle_status,
                case when sd.status=" . Settlement::STATUS_DONE . " then '是'
                else '否'
                end as down_settle_status";
        } else {
            if ($this->type == 2) {
                $sql = "select {col} from t_project a
                left join t_partner p on a.up_partner_id=p.partner_id
                left join t_corporation c on a.corporation_id=c.corporation_id
                left join t_invoice i on a.project_id=i.project_id
                left join t_settlement su on a.project_id=su.project_id and su.type=1
                left join t_settlement sd on a.project_id=sd.project_id and sd.type=2
                left join t_return_plan rp on rp.project_id=a.project_id
                where a.project_id=" . $projectId . " {limit}";

                $fields = "a.project_id,a.project_name,p.partner_id,p.name as partner_name,c.corporation_id,c.name as corporation_name,
                sum(rp.amount) as total_amount,
                case when a.status>=" . Project::STATUS_SETTLE_CONFIRM . " then '已结算'
                else '未结算'
                end as p_status,
                case when i.status=1 then '是'
                else '否'
                end as invoice_status,
                case when su.status=" . Settlement::STATUS_DONE . " then '已结清'
                else '未结清'
                end as up_settle_status,
                case when sd.status=" . Settlement::STATUS_DONE . " then '是'
                else '否'
                end as down_settle_status";
            }
        }
        $data = $this->queryTablesByPage($sql, $fields);

        if (Utility::isNotEmpty($data['data']['rows'])) {
            if ($this->type == 1) {
                $receive_amount = UpReceive::getReceiveAmount($projectId);
                foreach ($data['data']['rows'] as $key => $row) {
                    $data['data']['rows'][$key]['paid_amount'] = $row['actual_amount'] - $receive_amount;
                    $data['data']['rows'][$key]['unpaid_amount'] = $row['total_amount'] - $row['pay_amount'] + $receive_amount;
                }
            } else {
                if ($this->type == 2) {
                    //$total_return_amount = DownReceive::getReceiveAmount($projectId);
                    $receive_amount = DownReceive::getReceiveAmount($projectId);
                    foreach ($data['data']['rows'] as $key => $row) {
                        $data['data']['rows'][$key]['paid_amount'] = $receive_amount;
                        $data['data']['rows'][$key]['unpaid_amount'] = $row['total_amount'] - $receive_amount;
                    }
                }
            }
        }

        return $data;
    }*/

    /**
     * @desc 获取上游收款确认头部信息
     * @param $projectId
     * @return array
     */
    public function getReceiveHead($projectId) {
        $sql = "select {col} from t_project a
                left join t_partner pu on a.up_partner_id=pu.partner_id
                left join t_partner pd on a.down_partner_id=pd.partner_id
                left join t_corporation c on a.corporation_id=c.corporation_id
                left join t_invoice i on a.project_id=i.project_id
                left join t_settlement s on a.project_id=s.project_id and s.type=2
                where a.project_id=" . $projectId . " order by a.project_id {limit}";
        $fields = "a.project_id, a.project_name, a.status,
				pu.partner_id as up_partner_id, pu.name as up_partner_name,
				pd.partner_id as down_partner_id, pd.name as down_partner_name,
                c.corporation_id, c.name as corporation_name,
                case when i.status=1 then '已收到'
                else '未收到'
                end as invoice_status,
                case when s.status=" . Settlement::STATUS_DONE . " then '是'
                else '否'
                end as settle_status";
        $data = $this->queryTablesByPage($sql, $fields);

        return $data;
    }

    /**
     * @desc 获取上游收款流水
     * @param $projectId 项目id
     * @param $planId 计划id
     * @return array
     */
    public function getUpReceiveWater($projectId, $planId) {
        $sql = "select {col} from t_up_confirm uc
                left join t_corporation c on uc.corporation_id=c.corporation_id
                left join t_pay_attachment pa on pa.relation_id=uc.receive_id and pa.status=1 
                left join t_account a on uc.account_id=a.account_id 
                left join t_project_pay_plan pp on pp.plan_id=uc.plan_id 
                where uc.project_id=" . $projectId . " and uc.plan_id=" . $planId . " and uc.status>0  order by uc.create_time {limit}";
        $fields = "uc.receive_id, uc.receive_date, uc.corporation_id, uc.amount, uc.remark, c.name as corporation_name, uc.account_id, a.bank_name, pa.id, pa.type, pp.period";
        $data = $this->queryTablesByPage($sql, $fields);

        return $data;
    }

    /**
     * @desc 获取上游的付款计划详情
     * @param $projectId
     * @return array
     */
    public static function getUpPayPlan($projectId) {
        $sql = "select a.plan_id, a.project_id, a.pay_days, a.pay_date, a.pay_type, a.type, a.period, a.rate, a.amount, a.status,
               sum(b.amount) as pay_amount, sum(c.amount) as actual_amount, sum(uc.amount) as receive_amount 
               from t_project_pay_plan a
               left join t_pay_application b on a.plan_id=b.plan_id and b.status>0
               left join t_payment c on b.apply_id=c.apply_id 
               left join t_up_confirm uc on uc.plan_id=a.plan_id and uc.status>0
               where a.project_id=" . $projectId . " group by a.plan_id order by a.period asc";
        $data = Utility::query($sql);
        $payments = array();
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $key => $value) {
                $value['paying_amount'] = $value['pay_amount'] - $value['actual_amount'];
                $value['actual_amount'] -= $value['receive_amount'];
                $value['balance'] = $value['amount'] - $value['pay_amount'] + $value['receive_amount'];
                $payments['plan'][] = $value;
                $payments['total']['total_amount'] += $value['amount'];
                $payments['total']['total_balance'] += $value['balance'];
                $payments['total']['total_actual_amount'] += $value['actual_amount'];
                $payments['total']['total_paying_amount'] += $value['paying_amount'];
            }
        }

        return $payments;
    }

    /**
     * @desc 获取下游的收款计划详情
     * @param $projectId
     * @return array
     */
    public static function getDownPayPlan($projectId) {
        $sql = "select a.id,a.project_id,a.receive_days,a.return_date,a.receive_type,a.type,a.period,
               a.rate,a.amount,a.status,IFNULL(sum(b.amount), 0) as actual_amount
               from t_return_plan a
               left join t_down_confirm b on a.id=b.plan_id and b.status>0  
               where a.project_id=" . $projectId . " group by a.id order by a.period asc";
        $data = Utility::query($sql);
        $payments = array();
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $key => $value) {
                $value['paying_amount'] = 0;
                $value['balance'] = $value['amount'] - $value['actual_amount'];
                $payments['plan'][] = $value;
                $payments['total']['total_amount'] += $value['amount'];
                $payments['total']['total_balance'] += $value['balance'];
                $payments['total']['total_actual_amount'] += $value['actual_amount'];
                $payments['total']['total_paying_amount'] = 0;
            }
        }

        return $payments;
    }

    /**
 * @desc 获取下游收款流水
 * @param $projectId 项目id
 * @param $planId 计划id
 * @return array
 */
    public function getDownReceiveWater($projectId, $planId) {
        $sql = "select receive_type from t_return_plan where id=" . $planId;
        $res = Utility::query($sql);
        if (Utility::isNotEmpty($res)) {
            if ($res[0]['receive_type'] == self::RECEIVE_TYPE_BOND) {
                $attach_table_join = 't_rent_attachment ra on ra.relation_id=dc.plan_id and ra.project_id=dc.project_id and ra.status=1 and ra.type=1 ';
            } else {
                $attach_table_join = 't_remind_attachment ra on ra.relation_id=dc.receive_id and ra.project_id=dc.project_id and ra.status=1 and ra.type=51 ';
            }
            /*$sql = "select {col} from t_down_confirm dc
                left join t_corporation c on dc.corporation_id=c.corporation_id
                left join t_remind_attachment ra on ra.relation_id=dc.receive_id and ra.status=1
                left join t_account a on dc.account_id=a.account_id
                left join t_return_plan rp on rp.id=dc.plan_id
                where dc.project_id=" . $projectId . " and dc.plan_id=" . $planId . " and dc.status>0  order by dc.create_time {limit}";*/
            $sql = "select {col} from t_down_confirm dc
                left join t_corporation c on dc.corporation_id=c.corporation_id
                left join " . $attach_table_join . " 
                left join t_account a on dc.account_id=a.account_id 
                left join t_return_plan rp on rp.id=dc.plan_id
                where dc.project_id=" . $projectId . " and dc.plan_id=" . $planId . " and dc.status>0  order by dc.create_time {limit}";
            $fields = "dc.receive_id, dc.receive_date, dc.corporation_id, dc.amount, dc.remark, c.name as corporation_name, dc.account_id, a.bank_name, ra.id, ra.type, rp.period, rp.receive_type";
            $data = $this->queryTablesByPage($sql, $fields);

            return $data;
        }

        return array();
    }

    /**
     * @desc 获取下游全部的收款流水
     * @param $projectId 项目id
     * @return array
     */
    public function getDownReceiveWaterByProject($projectId) {
        $result = array();
        $sql = "select receive_type,`id` as plan_id from t_return_plan where project_id=" . $projectId;
        $res = Utility::query($sql);

        if (Utility::isNotEmpty($res)) {
            foreach ($res as $v){
                if ($v['receive_type'] == self::RECEIVE_TYPE_BOND) {
                    $attach_table_join = 't_rent_attachment ra on ra.relation_id=dc.plan_id and ra.project_id=dc.project_id and ra.status=1 and ra.type=1 ';
                } else {
                    $attach_table_join = 't_remind_attachment ra on ra.relation_id=dc.receive_id and ra.project_id=dc.project_id and ra.status=1 and ra.type=51 ';
                }
                $fields = "dc.receive_id, dc.receive_date, dc.corporation_id, dc.amount, dc.remark, c.name as corporation_name, dc.account_id, a.bank_name, ra.id, ra.type, rp.period, rp.receive_type";
                $sql = "select ".$fields." from t_down_confirm dc
                left join t_corporation c on dc.corporation_id=c.corporation_id
                left join " . $attach_table_join . " 
                left join t_account a on dc.account_id=a.account_id 
                left join t_return_plan rp on rp.id=dc.plan_id
                where dc.project_id=" . $projectId . " and dc.plan_id=" . $v['plan_id'] . " and dc.status>0  order by dc.create_time ";
                $data = Utility::query($sql);
                foreach ($data as $item){
                    $result[] = $item;
                }
            }
        }

        return $result;
    }

    /**
     * @desc 获取上游所有的实收流水
     * @param $projectId 项目id
     * @return array
     */
    public function getUpReceiveWaterByProject($projectId) {
        $sql = "select {col} from t_up_confirm uc
                left join t_corporation c on uc.corporation_id=c.corporation_id
                left join t_pay_attachment pa on pa.relation_id=uc.receive_id and pa.status=1 
                left join t_account a on uc.account_id=a.account_id 
                left join t_project_pay_plan pp on pp.plan_id=uc.plan_id 
                where uc.project_id=" . $projectId ." and uc.status>0 and pp.type>0 order by uc.create_time {limit}";
        $fields = "uc.receive_id, uc.receive_date, uc.corporation_id, uc.amount, uc.remark, c.name as corporation_name, uc.account_id, a.bank_name, pa.id, pa.type, pp.period";
        $data = $this->queryTablesByPage($sql, $fields);
        return $data;
    }
}