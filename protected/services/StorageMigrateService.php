<?php
/**
 * Created by PhpStorm.
 * User: shengwu
 * Date: 2017/12/8
 * Time: 11:07
 */
class StorageMigrateService {
    protected static $COMMON_DATA = array(
        'create_user_id' => -1,
        'update_user_id' => -1,
        'remark' => '系统导入',
    );

    public static function importStockIn() {
        $fields = 'a.*,(b.settle_price * a.settle_quantity * 100) as settle_amount,b.settle_price*100 as settle_price,b.delivery_date,b.prd_id';
        $fields .= ',c.store_id,d.goods_id';
        $baseSql = "select $fields from t_storage_flow_in a
                left JOIN t_contract_purchase b on a.contract_id = b.contract_id
                left join t_storehouse c on a.storehouse_id = c.sid
                left join t_prd as d  on d.prd_id = b.prd_id
                where a.storehouse_id > 0 and a.flowin_quantity > 0";
        $sql = $baseSql . ' group by a.id order by a.id asc';
        $resArr = Utility::query($sql, Utility::DB_HISTORY);
        if (is_array($resArr) && count($resArr) == 0)
            return true;

        if ($resArr === false)
            Mod::log(__METHOD__ . "数据库查询失败");

        $failedIds = array();
        foreach ($resArr as $item) {
            $res = self::processStockIn($item);
            if ($res === false) {
                Mod::log(__METHOD__ . "数据导入失败,data:".json_encode($item), CLogger::LEVEL_ERROR, 'oil.import.log');
                $failedIds[] = $item['id'];
            }
        }

        Mod::log(__METHOD__ . "入库相关数据导入结果:total:".count($res)." failedNum:".$failedIds." failedIds:".json_encode($failedIds));
    }

    public static function rollback() {
        try {
            $sql = 'delete a from  `t_stock_log` as a , t_stock_in_detail as b where a.stock_id = b.stock_id and b.create_user_id = -1;';
            $sql .= 'delete from t_stock_in_batch where create_user_id=-1;';
            $sql .= 'delete from t_stock_in_batch_detail where create_user_id=-1;';
            $sql .= 'delete from t_stock_in where create_user_id=-1;';
            $sql .= 'delete from t_stock where create_user_id=-1;';
            $sql .= 'delete from t_stock_in_detail where create_user_id=-1;';
            $sql .= 'delete from t_stock_batch_settlement where create_user_id=-1;';
            $res = Utility::executeSql($sql);
            return $res;
        } catch (Exception $e) {
            Mod::log("回滚失败:" . $e->getMessage());
        }
    }

    protected static function getStockBatchStatus($settleCnt, $settlePrice) {
        if ($settleCnt > 0 && $settlePrice > 0)
            return StockNotice::STATUS_SETTLED;

        return StockNotice::STATUS_SUBMIT;
    }

    protected static function initCommonData($contract) {
        $now = Utility::getDateTime();
        self::$COMMON_DATA['create_time'] = $now;
        self::$COMMON_DATA['update_time'] = $now;
        self::$COMMON_DATA['contract_id'] = $contract['contract_id'];
        self::$COMMON_DATA['project_id'] = $contract['project_id'];
    }

    protected static function convertMoney($money) {
        return $money * 100;
    }

    protected static function processStockIn($data) {
        set_time_limit(1 * 60 * 60);
        try {
            $isInDbTrans = Utility::isInDbTrans();
            if (!$isInDbTrans) {
                $db = Mod::app()->db;
                $trans = $db->beginTransaction();
            }

            $contract = Contract::model()->find("contract_code='" . $data['contract_id'] . "'");
            if (!$contract->contract_id) {
                throw new Exception("合同信息未查询到");
                Mod::log(__METHOD__ . "\t合同信息未查询到\tdata:" . json_encode($data), 'error', 'oil.import.log');
                return false;
            }

            if (empty($data['goods_id'])) {
                throw new Exception("商品ID不能为空");
                Mod::log(__METHOD__ . "\t商品ID不能为空\tdata:" . json_encode($data), 'error', 'oil.import.log');
                return false;
            }

            $contract = $contract->attributes;
            self::initCommonData($contract);

            // 入库通知单
            $batchData = array(
                'batch_id' => IDService::getStockBatchId(),
                'code' => StockNoticeService::generateStockNoticeCode($contract['contract_id']),
                'type' => 1, // 1经仓 2直调
                'batch_date' => $data['put_date'],
                'order_index' => 0,
                'currency' => 1,
                'exchange_rate' => 1.000000,
                'status_time' => date('Y-m-d H:i:s', strtotime($data['put_date'])),
                'status' => self::getStockBatchStatus($data['settle_quantity'], $data['settle_price']), //结算数量 + 结算价格 > 0 => 审核通过
            );

            $batchObj = StockNotice::model()->find('update_user_id='.($data['id']*-1));
            if (empty($batchObj)) {
                $batchObj = new StockNotice();
            }
            $batchObj->setAttributes($batchData, false);
            $batchObj->setAttributes(self::$COMMON_DATA, false);
            $batchObj->setAttribute('update_user_id', ($data['id']*-1));
            $res = $batchObj->save();

            if ($res === false)
                throw new Exception("插入入库通知单失败\tinsertData:".json_encode($batchData));

            // 入库信息
            $stockInData = array(
                'stock_in_id' => IDService::getStockInId(),
                'batch_id' => $batchData['batch_id'],
                'code' => StockInService::generateStockInCode($batchData['batch_id']),
                'store_id' => $data['store_id'],
                'type' => 1,
                'entry_date' => $data['put_date'],
                'order_index' => 0,
                'currency' => 1, //人民币
                'exchange_rate' => 1.000000,
                'status_time' => Utility::getDateTime($data['put_date']),
                'status' => StockIn::STATUS_PASS,
            );
            $stockInObj = StockIn::model()->find('batch_id='.$batchData['batch_id']);
            if(empty($stockInObj)) {
                $stockInObj = new StockIn();
            }
            $stockInObj->setAttributes($stockInData, false);
            $stockInObj->setAttributes(self::$COMMON_DATA, false);
            $stockInObj->setAttribute('update_user_id', ($data['id']*-1));
            $res = $stockInObj->save();
            if ($res === false) {
                throw new Exception("保存入库信息失败\tinsertData:".json_encode($stockInData));
            }

            // 入库通知单商品明细
            $batchDetailData = array(
                'batch_id' => $batchData['batch_id'],
                'store_id' => $data['store_id'],
                'goods_id' => $data['goods_id'],
                'goods_describe' => '',
                'price' => $data['settle_price'],
                'quantity' => $data['flowin_quantity'],
                'quantity_actual' => $data['flowin_quantity'],
                'unit' => 2,
                'unit_rate' => 1.0000,
                'currency' => 1,
                'amount_settle' => $data['settle_amount'],
                'quantity_settle' => $data['settle_quantity'],
                'status_time' => Utility::getDateTime($data['put_date']),
                'status' => 0,
            );

            $batchDetailObj = StockNoticeDetail::model()->find('batch_id='.$batchData['batch_id'].' and goods_id='.$data['goods_id']);
            if(empty($batchDetailObj)) {
                $batchDetailObj = new StockNoticeDetail();
            }
            $batchDetailObj->setAttributes(self::$COMMON_DATA, false);
            $batchDetailObj->setAttributes($batchDetailData, false);
            $batchDetailObj->setAttribute('update_user_id', ($data['id']*-1));
            $detailRes = $batchDetailObj->save();
            if ($detailRes === false)
                throw new Exception("保存入库通知单商品明细失败\tinsertData:".json_encode($batchDetailData));


            // 保存入库通知结算信息
            if ($batchData['status'] == StockNotice::STATUS_SETTLED) {
                self::importSettlement($data, $contract, $batchData['batch_id'], $batchDetailObj->detail_id);
            }

            // 合同商品入库明细
            $stockDetailData = array(
                'store_id' => $data['store_id'],
                'stock_in_id' => $stockInData['stock_in_id'],
                'detail_id' => $batchDetailObj->detail_id,
                'goods_id' => $data['goods_id'],
                'goods_describe' => '',
                'price' => $data['settle_price'],
                'quantity' => $data['flowin_quantity'],
                'quantity_actual' => $data['flowin_quantity'],
                'unit' => 2,
                'unit_rate' => 1.0000,
                'currency' => 1,
                'amount_settle' => $data['amount_settle'],
                'quantity_settle' => $data['settle_quantity'],
                'status_time' => Utility::getDateTime($data['put_date']),
                'status' => 0,
            );
            $stockDetailObj = StockInDetail::model()->find('stock_in_id='.$stockInData['stock_in_id'].' and goods_id='.$data['goods_id'].' and detail_id='.$batchDetailObj->detail_id);
            if(empty($stockDetailObj)) {
                $stockDetailObj = new StockInDetail();
            }
            $stockDetailObj->setAttributes(self::$COMMON_DATA, false);
            $stockDetailObj->setAttributes($stockDetailData);
            $stockDetailObj->setAttribute('update_user_id', ($data['id']*-1));
            $res = $stockDetailObj->save();
            if ($res === false)
                throw new Exception("保存合同入库商品明细失败\tinsertData:".json_encode($stockDetailData));

            $stockId = $stockDetailObj->stock_id;
            $stockData = array(
                'stock_id' => $stockId,
                'store_id' => $data['store_id'],
                'stock_in_id' => $stockInData['stock_in_id'],
                'detail_id' => $batchDetailObj->detail_id,
                'goods_id' => $data['goods_id'],
                'goods_describe' => '',
                'quantity' => $data['flowin_quantity'],
                'quantity_balance' => $data['flowin_quantity'],
                'quantity_frozen' => 0,
                'quantity_out' => 0,
                'unit' => 2,
                'unit_rate' => 1.0000,
                'status_time' => null,
                'status' => 0,
            );

            $stockObj = Stock::model()->findByPk('stock_id='.$stockId);
            if(empty($stockObj)) {
                $stockObj = new Stock();
            }
            $stockObj->setAttributes(self::$COMMON_DATA, false);
            $stockObj->setAttributes($stockData, false);
            $stockObj->setAttribute('update_user_id', ($data['id']*-1));
            $stockRes = $stockObj->save();

            if ($stockRes === false)
                throw new Exception("保存合同商品库存信息失败\tinsertData:".json_encode($stockData));

            // 合同商品库存出入库明细
            $stockLogParams = $stockObj->getAttributes(false);
            $stockLogParams['stock_id'] = $stockId;
            $stockLogParams['relation_id'] = $stockId;
            $stockLogParams['to_contract_id'] = $contract['contract_id'];
            $stockLogParams['type'] = ConstantMap::STOCK_TYPE_IN;
            StockLog::addStockLog($stockLogParams);

            if (!$isInDbTrans) {
                $trans->commit();
            }
            return true;
        } catch (Exception $e) {
            Mod::log(__METHOD__ . "\t入库迁移失败,记录ID:{$data['id']},合同编号:{$data['contract_id']},错误信息:{$e->getMessage()}\tdata:" . json_encode($data), CLogger::LEVEL_ERROR, 'oil.import.log');
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

    protected static function getSettleAmount($price, $quantity) {
        $amount = $price * $quantity;
        return self::convertMoney($amount);
    }

    protected static function importSettlement($data, $contract, $batchId, $detailId) {
        $data = array(
            'batch_id' => $batchId,
            'detail_id' => $detailId,
            'settle_date' => $data['put_date'],
            'goods_id' => $data['goods_id'],
            'price' => $data['settle_price'],
            'unit' => 2, // 2 =》 吨
            'quantity' => $data['flowin_quantity'],
            'quantity_loss' => $data['flowin_quantity'] - $data['settle_quantity'],
            'amount_cny' => $data['settle_amount'],
            'unit_rate' => 1.0000,
            'amount' => $data['settle_amount'],
            'currency' => 1,
            'unit_settle' => 2, // 结算单位
            'status_time' => $data['put_date'],
            'status' => StockBatchSettlement::STATUS_PASS
        );

        $settlementObj = StockBatchSettlement::model()->find('batch_id='.$batchId.' and detail_id='.$detailId.' and goods_id='.$data['goods_id']);
        if(empty($settlementObj)) {
            $settlementObj = new StockBatchSettlement();
        }
        $settlementObj->setAttributes(self::$COMMON_DATA, false);
        $settlementObj->setAttributes($data, false);
        $settlementObj->setAttribute('update_user_id', ($data['id']*-1));
        $res = $settlementObj->save();
        if ($res === false)
            throw new Exception("保存入库通知单结算信息失败");

        return $res;
    }
}