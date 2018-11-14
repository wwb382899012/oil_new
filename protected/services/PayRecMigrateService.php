<?php
/**
 * Created by PhpStorm.
 * User: shengwu
 * Date: 2017/12/6
 * Time: 10:20
 */
class PayRecMigrateService {
    public static $SUCCESS_CNT = 0; // 迁移成功数量
    public static $TOTAL = 0; // 总共筛选数量
    public static $COMPANY_MAP = array(
        '中油海化' => 'HH',
        '海化' => 'HH',
        '亚太' => 'YT',
        '泰丰' => 'TF',
        '坤源' => 'KY',
        '上海启益' => 'QY',
        '启益' => 'QY',
        '兴源' => 'XY',
    );

    protected static $COMMON_DATA = array(
        'create_user_id' => -1,
        'remark' => '系统导入'
    );

    public static function initCommon($data) {
        $now = Utility::getDateTime();
        self::$COMMON_DATA['create_time'] = $now;
        self::$COMMON_DATA['update_time'] = $now;
        self::$COMMON_DATA['update_user_id'] = $data['id'] * -1;
    }

    protected static function convertMoney($money) {
        return $money * 100;
    }

    public static function run() {
        set_time_limit(1 * 60 * 60);
        $startId = 0;
        $baseSql = "select a.*, a.flow_out * 100 as flow_out, a.flow_in*100 as flow_in,b.account_id,c.subject_id from t_acc_cash_flow a";
        $baseSql .= ' left join t_company_acc b on a.acc_bank_id = b.id';
        $baseSql .= ' left join t_acc_statement_use c on c.use = a.use';
        $sql = $baseSql . " group by a.id order by id asc";
        $data = Utility::query($sql, Utility::DB_HISTORY);
        if (empty($data) && is_array($data)) {
            return false;
        }

        if ($data === false) {
            Mod::log(__METHOD__ . "\t迁移收付款中数据查询出错,sql:".$sql, CLogger::LEVEL_ERROR, 'oil.import.log');
            return false;
        }

        foreach ($data as $item) {
            self::$TOTAL++;
            if (empty($item['purchase_contract_id']) && empty($item['sales_contract_id'])) {
                Mod::log(__METHOD__ . "\t没有任何合同,data:".json_encode($item), CLogger::LEVEL_ERROR, 'oil.import.log');
                continue;
            }

            $item['contract_id'] = !empty($item['purchase_contract_id']) ? $item['purchase_contract_id']: $item['sales_contract_id'];
            if ($item['flow_in'] > 0) { // 收款
                $res = self::migrateReceive($item);
            } else if ($item['flow_out'] > 0) { // 付款
                $res = self::migratePay($item);
            } else {
                Mod::log(__METHOD__ . "\t数据有问题,data:".json_encode($item), CLogger::LEVEL_ERROR, 'oil.import.log');
            }

            Mod::log(__METHOD__ . "\t迁移数据, ID:" . $item['id'] . " res:".json_encode($res), CLogger::LEVEL_INFO);

            if ($res !== false) {
                self::$SUCCESS_CNT++;
            }
        }

        $info = sprintf("%s\t收付款数据迁移完毕,总共:%s条, 成功:%s条", __METHOD__, self::$TOTAL, self::$SUCCESS_CNT);
        Mod::log($info, CLogger::LEVEL_INFO);
    }

    public static function migratePay($data) {
        $isInDbTrans = Utility::isInDbTrans();
        if (!$isInDbTrans) {
            $db = Mod::app()->db;
            $trans = $db->beginTransaction();
        }
        try {
            $contract = self::findContract($data['contract_id']);
            if (empty($contract)) {
                throw new Exception("合同未查询到");
            }

            self::initCommon($data);
            $corpId = self::findCorpId($data['company_name']);
            $projectId = $contract['project_id'];
            $account = self::findAccount($data['account_id']);

            $appData = array(
                'corporation_id' => $corpId,
                'project_id' => $projectId,
                'contract_id' => $contract['contract_id'],
                'type' => PayApplication::TYPE_CONTRACT,
                'category' => PayApplication::CATEGORY_NORMAL,
                'subject_id' => $data['subject_id'],
                'pay_date' => $data['flow_date'],
                'payee' => $data['comp_acc_name'],
                'bank' => !empty($account) ? $account['bank_name'] : null,
                'account_name' => !empty($account) ? $account['account_name'] : null,
                'account' => !empty($account) ? $account['account_no'] : null,
                'amount' => $data['flow_out'],
                'amount_cny' => $data['flow_out'],
                'currency' => 1, //人民币
                'exchange_rate' => 1.000000,
                'is_invoice' => 0,
                'amount_paid' => $data['flow_out'],
                'amount_paid_cny' => $data['flow_out'],
                'amount_balance' => 0.00,
                'amount_claim' => $data['flow_out'],
                'amount_factoring' => 0.00,
                'is_factoring' => 0,
                'status_time' => $data['flow_date'],
                'status' => PayApplication::STATUS_DONE,
                'sub_contract_code' => trim($data['contract_id']),
                'remark' => '系统导入 ' . $data['use']
            );
            $app = PayApplication::model()->find('update_user_id='.($data['id'] * -1));
            if(empty($app)) {
                $app = new PayApplication();
            }

            $app->setId();
            $app->setAttributes(self::$COMMON_DATA);
            $app->setAttributes($appData);

            $res = $app->save();
            if ($res === false) {
                throw new Exception("插入PayApplication失败\tinsertData:".json_encode($appData));
            }

            $payData = array(
                'payment_id' => IDService::getPayConfirmId(),
                'apply_id' => $app->apply_id,
                'pay_date' => $data['flow_date'],
                'payment_no' => null,
                'amount' => $data['flow_out'],
                'currency' => 1,
                'amount_cny' => $data['flow_out'],
                'exchange_rate' => 1.000000,
                'account_id' => $data['account_id'],
                'status' => Payment::STATUS_SUBMITED,
            );

//            while (1) {
//                $exists = Payment::model()->find("payment_id=".$payData['payment_id']);
//                if (empty($exists->payment_id)) {
//                    break;
//                }
//                $payData['payment_id'] = IDService::getPayConfirmId();
//            }
            $pay = Payment::model()->find('apply_id='.$app->apply_id);
            if(empty($pay)) {
                $pay = new Payment();
            }
            $pay->setAttributes(self::$COMMON_DATA);
            $pay->setAttributes($payData);
            $res = $pay->save();
            if ($res === false) {
                throw new Exception("插入Payment失败\tinsertData:".json_encode($payData));
            }


            if (!$isInDbTrans) {
                $trans->commit();
            }
            return true;
        } catch (Exception $e) {
            $type = empty($data['purchase_contract_id']) ? "销售" : "采购";
            $amount = $data['flow_out'] / 100;
            Mod::log(__METHOD__ . "\t付款迁移失败,记录ID:{$data['id']},项目编号:{$data['item_id']},{$type}合同编号:{$data['contract_id']},金额:{$amount},错误信息:{$e->getMessage()}\tdata:" . json_encode($data), CLogger::LEVEL_ERROR, 'oil.import.log');
            if (!$isInDbTrans) {
                try {
                    $trans->rollback();
                } catch (Exception $ee) {
                    throw new Exception($ee->getMessage());
                    return false;
                }
            } else {
                throw new Exception($e->getMessage());
                return false;
            }
            return false;
        }
    }

    public static function migrateReceive($data) {
        $isInDbTrans = Utility::isInDbTrans();
        if (!$isInDbTrans) {
            $db = Mod::app()->db;
            $trans = $db->beginTransaction();
        }
        try {
            $contract = self::findContract($data['contract_id']);
            if (empty($contract)) {
                throw new Exception("合同未查询到");
            }
            $corpId = self::findCorpId($data['company_name']);
            $projectId = $contract['project_id'];
            $account = self::findAccount($data['account_id']);
            $flowData = array(
                'flow_id' => IDService::getBankFlowId(),
                'code' => null,
                'corporation_id' => $corpId,
                'bank_name' => !empty($account) ? $account['bank_name'] : null,
                'account' => !empty($account) ? $account['account_no'] : null,
                'account_name' => !empty($account) ? $account['account_name'] : null,
                'subject' => $data['subject_id'],
                'pay_partner' => $contract['partner_name'],
                'pay_bank' => $contract['bank_name'],
                'amount' => $data['flow_in'],
                'receive_date' => $data['flow_date'],
                'pay_type' => 0,
                'currency' => 1,
                'exchange_rate' => 1.000000,
                'status_time' => $data['flow_date'],
                'status' => BankFlow::STATUS_DONE,
                'amount_claim' => $data['flow_in']
            );
            $flowObj = BankFlow::model()->find('update_user_id='.($data['id']*-1));
            if(empty($flowObj)) {
                $flowObj = new BankFlow();
            }
            $flowObj->setAttributes(array_merge(self::$COMMON_DATA,$flowData));
            $res = $flowObj->save();
            if ($res === false) {
                throw new Exception("插入流水表失败\tinsertData:".json_encode($flowData));
            }

            $recData = array(
                'receive_id' => IDService::getReceiveConfirmId(),
                'flow_id' => $flowData['flow_id'],
                'project_id' => $projectId,
                'contract_id' => $contract['contract_id'],
                'sub_contract_type' => null,
                'sub_contract_code' => trim($data['contract_id']),
                'receive_date' => $data['flow_date'],
                'amount' => $data['flow_in'],
                'account_id' => $data['account_id'],
                'pay_type' => 0,
                'currency' => 1, //人民币
                'exchange_rate' => 1.000000,
                'status' => ReceiveConfirm::STATUS_SUBMITED,
                'subject' => $data['subject_id'],
                'remark' => '系统导入 ' . $data['use']
            );
            $rec = ReceiveConfirm::model()->find('flow_id='. $flowData['flow_id']);
            if(empty($rec)) {
                $rec = new ReceiveConfirm();
            }
            $rec->setAttributes(array_merge(self::$COMMON_DATA,$recData));
            $res = $rec->save();
            if ($res === false) {
                throw new Exception("插入receive_confirm失败\tinsertData:".json_encode($recData));
            }

            $detail = array(
                'receive_id' => $rec->receive_id,
                'project_id' => $projectId,
                'contract_id' => $contract['contract_id'],
                'plan_id' => null,
                'amount' => $data['flow_in'],
                'status_time' => $data['flow_date'],
                'status' => ReceiveDetail::STATUS_DONE,
            );
            $detailObj = ReceiveDetail::model()->find('receive_id='.$rec->receive_id);
            if(empty($detailObj)) {
                $detailObj = new ReceiveDetail();
            }
            $detailObj->setAttributes(array_merge(self::$COMMON_DATA,$detail));
            $res = $detailObj->save();
            if ($res === false) {
                throw new Exception("插入receive_detail失败\tinsertData:".json_encode($detail));
            }

            if (!$isInDbTrans) {
                $trans->commit();
            }
            return true;
        } catch (Exception $e) {
            $type = empty($data['purchase_contract_id']) ? "销售" : "采购";
            $amount = $data['flow_in'] / 100;
            Mod::log(__METHOD__ . "\t收款迁移失败,记录ID:{$data['id']},项目编号:{$data['item_id']},{$type}合同编号:{$data['contract_id']},金额:{$amount},错误信息:{$e->getMessage()}\tdata:" . json_encode($data), CLogger::LEVEL_ERROR, 'oil.import.log');
            if (!$isInDbTrans) {
                try {
                    $trans->rollback();
                } catch (Exception $ee) {
                    throw new Exception($ee->getMessage());
                    return false;
                }
            } else {
                throw new Exception($e->getMessage());
                return false;
            }
            return false;
        }
    }

    public static function rollback() {
        try {
            $sql = 'delete from t_pay_application where create_user_id = -1;';
            $sql .= 'delete from t_payment where create_user_id = -1;';
            $sql .= 'delete from t_bank_flow where create_user_id = -1;';
            $sql .= 'delete from t_receive_confirm where create_user_id = -1;';
            $sql .= 'delete from t_receive_detail where create_user_id = -1;';
            $res = Utility::executeSql($sql);
            return $res;
        } catch (Exception $e) {
            Mod::log("回滚失败:" . $e->getMessage());
        }
    }

    protected static function findCorpId($companyName) {
        if (!trim($companyName)) {
            throw new Exception("主体名称不能为空");
        }
        $corp = Corporation::model()->find('code="' . self::$COMPANY_MAP[$companyName] . '"');
        if (!$corp->corporation_id) {
            Mod::log(__METHOD__ . "\t主体未查询到,companyName:" . $companyName, CLogger::LEVEL_ERROR, 'oil.import.log');
            throw new Exception("corporation未查询到, companyName:".$companyName);
        }

        return $corp->corporation_id;
    }

    protected static function findProjectId($code) {
        $code = trim($code);
        if (empty($code)) {
            throw new Exception("项目编码不能为空");
            return null;
        }

        $project = Project::model()->find("project_code=$code");
        if (empty($project->project_id)) {
            Mod::log(__METHOD__ . "\t项目未查询到,code:".$code, CLogger::LEVEL_ERROR, 'oil.import.log');
            throw new Exception("项目信息未查询到");
            return null;
        }

        return $project->project_id;
    }

    protected static function findContract($code) {
        if (!trim($code))
            throw new Exception("该条数据对应的合同编码为空");

        $sql = 'select c.contract_id,c.corporation_id,p.name as partner_name,p.bank_name,c.project_id from t_contract c 
              left join t_partner p on c.partner_id = p.partner_id 
              where c.contract_code="'.$code.'"';
        $contract = Utility::query($sql, Utility::DB);
        if (empty($contract))
            throw new Exception("未能查询到对应的合同");

        return $contract[0];
    }

    protected static function findAccount($accountId) {
        if (empty($accountId)) {
            throw new Exception("账户ID不能空");
        }
        $account = Account::model()->find("account_id=" . $accountId);
        if (!$account->account_id) {
            throw new Exception("账户ID:{$accountId}未查询到对应账户");
            return null;
        }

        return $account;
    }
}