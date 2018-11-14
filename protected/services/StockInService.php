<?php

use ddd\Split\Domain\Model\SplitEnum;

/**
 * Desc: 入库单服务
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class StockInService {
    private static $stockInCodeKey = 'stock.in.code';

    public static function getInstance(){
        return new self();
    }

    /**
     * @desc 获取附件信息
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

        $sql = "select * from t_stock_in_attachment where base_id=" . $id . " and status=1" . $type . " order by type asc";
        $data = Utility::query($sql);
        $attachments = array();
        foreach ($data as $v) {
            $attachments[$v["type"]][] = $v;
        }

        return $attachments;
    }

    /**
     * @desc 根据入库通知单交易明细格式化入库单明细
     * @param array $contractGoods
     * @param int $stock_in_id
     * @param array $stockInGoods
     * @return array
     */
    public static function formatStockInGoods($contractGoods, $stock_in_id = 0, $stockInGoods = array()) {
        $res = array();
        if (Utility::isNotEmpty($contractGoods)) {
            foreach ($contractGoods as $key => $row) {
                $res[$key]['stock_id'] = !empty($row['stock_id']) ? $row['stock_id'] : 0;
                $res[$key]['contract_id'] = $row['contract_id'];
                $res[$key]['project_id'] = $row['project_id'];
                $res[$key]['detail_id'] = $row['detail_id'];
                $res[$key]['stock_in_id'] = $stock_in_id;
                $res[$key]['goods_id'] = $row['goods_id'];
                $res[$key]['batch_id'] = !empty($row['batch_id']) ? $row['batch_id'] : 0;
                $res[$key]['goods_name'] = $row->goods->name;
                $res[$key]['goods_describe'] = $row['goods_describe'];
                $res[$key]['quantity'] = 0;
                $res[$key]['quantity_sub'] = 0;
                $res[$key]['unit'] = !empty($row['unit']) ? $row['unit'] : 0;
                $res[$key]['stock_unit'] = !empty($row['unit']) ? $row['unit'] : 0;
                $res[$key]['unit_sub'] = !empty($row->sub->unit) ? $row->sub->unit : (!empty($row['unit']) ? $row['unit'] : 0);
                $res[$key]['store_id'] = !empty($row['store_id']) ? $row['store_id'] : 0;
                $res[$key]['unit_rate'] = !empty($row['unit_rate']) ? $row['unit_rate'] : 1;
                $res[$key]['remark'] = '';
                if (Utility::isNotEmpty($stockInGoods)) {
                    foreach ($stockInGoods as $k => $v) {
                        if ($v['detail_id'] == $row['detail_id']) {
                            $res[$key]['quantity'] = $v['quantity'];
                            $res[$key]['quantity_sub'] = $v['quantity_sub'];
                            $res[$key]['unit'] = $v['unit'];
                            $res[$key]['stock_unit'] = $v['unit'];
                            $res[$key]['unit_sub'] = !empty($v->sub->unit) ? $v->sub->unit : (!empty($v['unit']) ? $v['unit'] : 0);
                            $res[$key]['store_id'] = $v['store_id'];
                            $res[$key]['unit_rate'] = $v['unit_rate'];
                            $res[$key]['remark'] = $v['remark'];
                        }
                    }
                }
                $res[$key]['unit_desc'] = !empty($res[$key]['unit']) ? Map::$v['goods_unit'][$res[$key]['unit']]['name'] : '';
                $res[$key]['unit_sub_desc'] = !empty($res[$key]['unit_sub']) ? Map::$v['goods_unit'][$res[$key]['unit_sub']]['name'] : '';
                $res[$key]['units'] = array();
                if (!empty($res[$key]['unit'])) {
                    array_push($res[$key]['units'], array('id' => $res[$key]['unit'], 'name' => Map::$v['goods_unit'][$res[$key]['unit']]['name']));
                }
                if (!empty($res[$key]['unit_sub'])) {
                    if (empty($res[$key]['unit']) || $res[$key]['unit_sub'] != $res[$key]['unit']) {
                        array_push($res[$key]['units'], array('id' => $res[$key]['unit_sub'], 'name' => Map::$v['goods_unit'][$res[$key]['unit_sub']]['name']));
                    }
                }
            }
        }

        return $res;
    }

    /**
     * @desc 生成入库单编码
     * @param int $batch_id
     * @return string
     */
    public static function generateStockInCode($batch_id) {
        $code = '';
        if (Utility::checkQueryId($batch_id)) {
            $stockNoticeModel = StockNotice::model()->findByPk($batch_id);
            if (!empty($stockNoticeModel->batch_id) && !empty($stockNoticeModel->code)) {
                $code .= $stockNoticeModel->code . '-' . IDService::getSerialNum(self::$stockInCodeKey . '.' . $batch_id);
            }
        }

        return $code;
    }

    /**
     * @desc 获取入库单库存变化明细
     * @param int $stock_in_id
     * @return array
     */
    public static function getStockChangeForStockIn($stock_in_id) {
        $res = array();
        if (Utility::checkQueryId($stock_in_id)) {
            $sql = 'select a.stock_id, a.goods_id, b.name as goods_name from t_stock a 
                    left join t_goods b on b.goods_id = a.goods_id where a.stock_in_id = ' . $stock_in_id;
            $res = Utility::query($sql);
            if (Utility::isNotEmpty($res)) {
                foreach ($res as $key => $row) {
                    $sql = 'select log_id, relation_id, type, method, quantity, quantity_balance, unit, goods_id, 
                            case when type = 1 then "出库" when type = 2 then "入库" else "" end as type_desc, 
                            case when method = 1 then "合同销售出库" when method = 2 then "借货" 
                            when method = 3 then "还货" when method = 4 then "库存盘点" else "" end as method_desc 
                            from t_stock_log where stock_id = ' . $row['stock_id'] . ' and goods_id = ' . $row['goods_id'];
                    $data = Utility::query($sql);
                    if (Utility::isNotEmpty($data)) {
                        foreach ($data as $k => $v) {
                            if ($v['type'] == StockLog::TYPE_IN) {
                                if ($v['method'] == 0) {
                                    $v['method_desc'] = '入库';
                                }
                                $sql = 'select b.entry_date as op_date, a.stock_in_id as id, b.code, "" as cross_code, 
                                        0 as inventory_type, 0 as corporation_id, 0 as store_id 
                                        from t_stock_in_detail a left join t_stock_in b on b.stock_in_id = a.stock_in_id 
                                        where a.stock_id = ' . $v['relation_id'];
                            } else {
                                $v['quantity'] = 0 - $v['quantity'];
                                $sql = 'select b.out_date as op_date, a.out_order_id as id, b.code, ifnull(d.cross_code, "") as cross_code, 
                                        0 as inventory_type, 0 as corporation_id, 0 as store_id 
                                        from t_stock_out_detail a 
                                        left join t_stock_out_order b on b.out_order_id = a.out_order_id 
                                        left join t_cross_detail c on c.detail_id = a.cross_detail_id 
                                        left join t_cross_order d on d.cross_id = c.cross_id where a.out_id = ' . $v['relation_id'];
                            }
                            if ($v['method'] == StockLog::METHOD_STOCK_CHECK) {
                                $sql = 'select b.inventory_date as op_date, a.inventory_id as id, "" as code, "" as cross_code, 
                                        a.type as inventory_type, a.corporation_id, a.store_id 
                                        from t_stock_inventory_detail a 
                                        left join t_stock_inventory b on b.inventory_id = a.inventory_id where a.detail_id = ' . $v['relation_id'];
                            }
                            $result = Utility::query($sql);
                            if (Utility::isNotEmpty($result)) {
                                $data[$k] = array_merge($v, $result[0]);
                            }
                        }
                    }
                    $res[$key]['stock_detail'] = $data;
                }
            }
        }

        return $res;
    }

    public function getCanInvalidStockInBill($id){
        return StockIn::model()->findByPk($id,'status IN('.implode(',',
                array(StockIn::STATUS_NEW ,StockIn::STATUS_BACK ,StockIn::STATUS_REVOCATION )).')');
    }

    public function getCanRevocationStockInBill($id){
        return StockIn::model()->findByPk($id,'status=:status',
            array(':status'=>StockIn::STATUS_SUBMIT));
    }

    /**
     * 撤销
     * @param $model
     * @return int
     */
    public static function revocationStockInBill(StockIn $model){
        $trans = Utility::beginTransaction();
        try{
            $result = StockIn::model()->updateByPk($model->stock_in_id,array(
                'status'=>  StockIn::STATUS_REVOCATION
            ),'status=:status',array(':status'=> StockIn::STATUS_SUBMIT));
            if(!$result){
                throw new Exception("撤销出货单失败");
            }

            //撤销审核任务
            $result = FlowService::revocationFlow(FlowService::BUSINESS_STOCK_IN_CHECK,$model->stock_in_id);
            if(1 !== $result){
                throw new Exception($result);
            }
            $trans->commit();

            Utility::addActionLog(json_encode(array('oldStatus' => $model->status)), "撤销入库单审核", "StockIn", $model->stock_in_id);
            return true;
        }catch(Exception $e){
            try{
                $trans->rollback();
            }catch(Exception $ee){
            }
        }

        return false;
    }

    /**
     * 作废
     * @param $model
     * @return int
     */
    public static function invalidStockInBill(StockIn $model,$remark = ''){
        $updateData =array('status'=>StockIn::STATUS_INVALIDITY);
        if(!empty($remark)){
            $updateData['remark'] = $model->remark .'；作废理由：'.$remark;
        }

        $result = StockIn::model()->updateByPk($model->stock_in_id,$updateData,'status IN('.implode(',',array(StockIn::STATUS_NEW , StockIn::STATUS_BACK , StockIn::STATUS_REVOCATION)) .')');

        Utility::addActionLog(json_encode(array('oldStatus' => $model->status)), "作废入库单", "StockIn", $model->stock_in_id);
        return $result;
    }

    /**
     * 判断是否可以修改
     * @param $status
     * @return bool
     */
    public static function isCanEdit($status) {
        return in_array($status,array(
            StockIn::STATUS_NEW,
            StockIn::STATUS_BACK,
            StockIn::STATUS_REVOCATION
        ));
    }

    /**
     * 是否能撤销入库单审批
     * @param $status
     * @return bool
     */
    public static function isCanRevocation($status){
        //提交审批之后才可以撤销
        return (StockIn::STATUS_SUBMIT == $status);
    }

    /**
     * 是否能作废入库单
     * @param $status
     * @return bool
     */
    public static function isCanInvalid($status){
        //驳回、撤销之后才可以作废
        return (StockIn::STATUS_NEW == $status || StockIn::STATUS_BACK == $status || StockIn::STATUS_REVOCATION == $status);
    }

    /**
     * 是否显示状态信息
     * @param $status
     * @return bool
     */
    public static function isCanShowStatus($status){
        return in_array($status,array(
            StockIn::STATUS_INVALIDITY, //作废
            //StockIn::STATUS_NEW, //保存
            StockIn::STATUS_REVOCATION //撤回
        ));
    }

    /**
     * 是否作废了
     * @param $status
     * @return bool
     */
    public static function isInvalid($status){
        return StockIn::STATUS_INVALIDITY == $status;
    }

    /**
     * 是否可以显示审核状态信息
     * @param $status
     * @return bool
     */
    public static function isCanShowAuditStatus($status){
        return in_array($status,array(
            StockIn::STATUS_SUBMIT,
            StockIn::STATUS_BACK,
            StockIn::STATUS_PASS,
            StockIn::STATUS_SETTLED
        ));
    }

    /**
     * 是否显示审核状态备注
     * @param $status
     * @return bool
     */
    public static function isShowAuditRemark($status){
        return in_array($status,array(
            StockIn::STATUS_BACK,
            StockIn::STATUS_PASS,
            StockIn::STATUS_SETTLED
        ));
    }

    /**
     * 是否拆分生成
     * @param $isSplit
     * @return bool
     */
    public static function isVirtualBill($isSplit){
        return SplitEnum::IS_VIRTUAL == $isSplit;
    }

    /**
     * 是否可以结算
     * @param $status
     * @return bool
     */
    public static function isCanSettlement($status){
        return in_array($status,array(
            StockIn::STATUS_INVALIDITY,
            StockIn::STATUS_PASS,
        ));
    }

    /**
     * @desc 根据入库通知单id更新入库单结算状态
     * @param int $batchId
     * @return bool
     */
    public static function updateSettledStatusByBatchId($batchId) {
        if(Utility::checkQueryId($batchId) && $batchId > 0) {
            $stockIns = StockIn::model()->findAll('batch_id=:batchId and status=:status', array("batchId" => $batchId, 'status'=>StockIn::STATUS_PASS));
            if (Utility::isNotEmpty($stockIns)) {
                foreach ($stockIns as $stockIn) {
                    if($stockIn->notice->status == StockNotice::STATUS_SETTLED) {
                        $rows = StockIn::model()->updateByPk($stockIn->stock_in_id,
                            array(
                                'status' => StockIn::STATUS_SETTLED,
                                'update_time' => new CDbExpression('now()'),
                                'update_user_id' => Utility::getNowUserId()
                            )
                        );
                        if($rows != 1) {
                            return false;
                        }
                    }
                }
            }
            return true;
        }
    }
}