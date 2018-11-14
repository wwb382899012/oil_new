<?php

use ddd\Split\Domain\Model\SplitEnum;

/**
 * Desc: 入库通知单
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class StockNoticeService {
    private static $stockNoticeCodeKey = 'stock.notice.code';

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

        $sql = "select * from t_stock_in_batch_attachment where base_id=" . $id . " and status=1" . $type . " order by type asc";
        $data = Utility::query($sql);
        $attachments = array();
        foreach ($data as $v) {
            $attachments[$v["type"]][] = $v;
        }

        return $attachments;
    }

    /**
     * @desc 根据合同交易明细格式化入库通知单明细
     * @param array $contractGoods
     * @param int $batch_id
     * @return array
     */
    public static function formatStockNoticeGoods($contractGoods, $batch_id = 0) {
        $res = array();
        if (Utility::isNotEmpty($contractGoods)) {
            foreach ($contractGoods as $key => $row) {
                $res[$key]['detail_id'] = 0;// !empty($row['detail_id']) ? $row['detail_id'] : 0;
                $res[$key]['contract_id'] = $row['contract_id'];// !empty($row['contract_id']) ? $row['contract_id'] : 0;
                $res[$key]['project_id'] = $row['project_id']; //!empty($row['project_id']) ? $row['project_id'] : 0;
                $res[$key]['batch_id'] = $batch_id;
                $res[$key]['goods_id'] = $row['goods_id'];// !empty($row['goods_id']) ? $row['goods_id'] : 0;
                //                $res[$key]['goods_name'] = !empty($row['goods_name']) ? $row['goods_name'] : '';
                $res[$key]['goods_name'] = $row->goods->name;
                //$res[$key]['goods_describe'] = !empty($row['goods_describe']) ? $row['goods_describe'] : '';
                $res[$key]['quantity'] = $row['quantity'];// !empty($row['detail_id']) ? (!empty($row['quantity']) ? $row['quantity'] : 0) : 0;
                $res[$key]['quantity_sub'] = $row['quantity'];// !empty($row->sub->quantity) ? $row->sub->quantity :0;// (!empty($row['detail_id']) ? (!empty($row['quantity_sub']) ? $row['quantity_sub'] : 0) : 0);
                $res[$key]['unit'] = $row['unit'];//!empty($row['unit']) ? $row['unit'] : 0;
                //                $res[$key]['unit_sub'] = !empty($row['unit_sub']) ? $row['unit_sub'] : (!empty($row['unit']) ? $row['unit'] : 0);
                $res[$key]['unit_sub'] = !empty($row['unit_store']) ? $row['unit_store'] : (!empty($row['unit']) ? $row['unit'] : 0);//!empty($row->sub->unit) ? $row->sub->unit : (!empty($row['unit_store']) ? $row['unit_store'] :0);
                $res[$key]['store_id'] = !empty($row['store_id']) ? $row['store_id'] : 0;
                $res[$key]['unit_rate'] = !empty($row['unit_rate']) ? $row['unit_rate'] : 1;
                $res[$key]['remark'] = !empty($row['stock_id']) && !empty($row['remark']) ? $row['remark'] : '';
            }
        }

        return $res;
    }

    /**
     * @desc 生成入库通知单编码
     * @param int $contract_id
     * @return string
     */
    public static function generateStockNoticeCode($contract_id) {
        $code = '';
        if (Utility::checkQueryId($contract_id)) {
            $contractModel = Contract::model()->findByPk($contract_id);
            if (!empty($contractModel->contract_id) && !empty($contractModel->contract_code)) {
                $code .= $contractModel->contract_code . '-' . IDService::getSerialNum(self::$stockNoticeCodeKey . '.' . $contract_id);
            }
        }

        return $code;
    }

    /**
     * 获取某一合同可编辑的入库商品明细信息
     * @param $contractId
     * @param int $batchId
     * @return array
     */
    public static function getEditDetails($contractId, $batchId = 0) {
        $sql = "select 
                a.contract_id,a.goods_id,a.unit,a.unit_store,
                g.name as goods_name,
                ifNull(b.detail_id,0),
                ifNull(b.batch_id,0),ifNull(b.store_id,0),ifNull(b.quantity,a.quantity),ifNull(b.unit_rate,1),
                ifNull(c.quantity,a.quantity) as quantity_sub,
                ifNull(c.unit,a.unit) as unit_sub
              from t_contract_goods a 
              left join t_goods g on a.goods_id=g.goods_id
              left join t_stock_in_batch_detail b 
                on a.goods_id=b.goods_id and a.contract_id=b.contract_id and b.batch_id=" . $batchId . "
              left join t_stock_in_batch_detail_sub c on b.detail_id=c.detail_id
              where a.contract_id=" . $contractId . " order by a.detail_id asc";

        return Utility::query($sql);
    }

    /**
     * @desc 更新入库通知单实际入库数量
     * @param int $batch_id
     * @return int
     */
    public static function updateStockNoticeQuantityActual($batch_id) {
        if (Utility::checkQueryId($batch_id)) {
            $stockNoticeModel = StockNotice::model()->with('details')->findByPk($batch_id);
            if (empty($stockNoticeModel->batch_id)) {
                return BusinessError::outputError(OilError::$STOCK_BATCH_NOT_EXIST, array('batch_id' => $batch_id));
            }

            if (Utility::isNotEmpty($stockNoticeModel->details)) {
                foreach ($stockNoticeModel->details as $key => $row) {
                    $total_quantity_actual = 0;
                    $total_quantity_sub_actual = 0;
                    $stockInDetails = StockInDetail::model()->with('stockIn', 'sub')->findAll('t.detail_id = :detailId and stockIn.status >= :status', array('detailId' => $row->detail_id, 'status' => StockIn::STATUS_PASS));
                    if (Utility::isNotEmpty($stockInDetails)) {
                        foreach ($stockInDetails as $k => $v) {
                            if($v->unit==$row->unit)
                                $total_quantity_actual += $v->quantity_actual;
                            else
                                $total_quantity_sub_actual += $v->quantity_actual;
                            
                            if($v->sub->unit==$row->unit)
                                $total_quantity_actual += $v->sub->quantity_actual;
                            else 
                                $total_quantity_sub_actual += $v->sub->quantity_actual;
                        }
                        $stockNoticeDetail = StockNoticeDetail::model()->findByPk($row->detail_id);
                        $stockNoticeDetail->quantity_actual = $total_quantity_actual;
                        $stockNoticeDetail->quantity_actual_sub = $total_quantity_sub_actual;
                        $stockNoticeDetail->update_time = new CDbExpression("now()");
                        $stockNoticeDetail->save();
                    }
                }
            }
        }
    }

    /**
     * @desc 获取入库通知单总数量
     * @param bigint $batchId
     * @param int $goodsId
     * @param int $unit
     * @return array
     */
    public static function getTotalStockNoticeQuantity($batchId, $goodsId, $unit) {
        $data = array('quantity' => 0, 'quantity_sub' => 0);
        $sqlSub = 'select ifnull(detail_id,0) from t_stock_in_batch_detail where batch_id=' . $batchId . ' and goods_id=' . $goodsId . ' and unit=' . $unit;
        $sql = 'select ifnull(quantity,0) as total_quantity from t_stock_in_batch_detail where detail_id in(' . $sqlSub . ')';
        $res = Utility::query($sql);
        if (Utility::isNotEmpty($res)) {
            $data['quantity'] = $res[0]['total_quantity'];
        }
        $sql1 = 'select ifnull(quantity,0) as total_quantity from t_stock_in_batch_detail_sub where detail_id in(' . $sqlSub . ')';
        $res1 = Utility::query($sql1);
        if (Utility::isNotEmpty($res1)) {
            $data['quantity_sub'] = $res1[0]['total_quantity'];
        }
        return $data;
    }

    /**
     * @desc 获取入库总数量
     * @param bigint $batchId
     * @param int $goodsId
     * @param int $unit
     * @return array
     */
    public static function getTotalStockInQuantity($batchId, $goodsId, $unit) {
        $data = array('quantity' => 0, 'quantity_sub' => 0);
        
        $sqlSub = 'select ifnull(stock_id, 0) from t_stock_in_detail a 
                left join t_stock_in b on b.stock_in_id=a.stock_in_id 
                left join t_stock_in_batch c on c.batch_id=b.batch_id 
                where c.batch_id=' . $batchId . ' and c.status>=' . StockNotice::STATUS_SUBMIT . ' and b.status>=' . StockIn::STATUS_PASS . ' and a.goods_id=' . $goodsId;
        $sql = 'select ifnull(sum(quantity),0) as total_quantity,unit from t_stock_in_detail where stock_id in(' . $sqlSub . ') group by unit';

        $res = Utility::query($sql);
        if (Utility::isNotEmpty($res)) {
            foreach ($res as $k => $v) {
                if($v['unit'] == $unit)
                    $data['quantity']       += $v['total_quantity'];
                else
                    $data['quantity_sub']   += $v['total_quantity'];
            }
            
        }
        $sql1 = 'select ifnull(sum(quantity),0) as total_quantity,unit from t_stock_in_detail_sub where stock_id in(' . $sqlSub . ') group by unit';
        $res1 = Utility::query($sql1);
        if (Utility::isNotEmpty($res1)) {
            foreach ($res1 as $k => $v) {
                if($v['unit'] == $unit)
                    $data['quantity']       += $v['total_quantity'];
                else
                    $data['quantity_sub']   += $v['total_quantity'];
            }
            
        }

        return $data;
    }
    /*public static function getTotalStockInQuantity($batchId, $goodsId, $unit) {
        $data = array('quantity' => 0, 'quantity_sub' => 0);
        $sqlSub = 'select ifnull(stock_id, 0) from t_stock_in_detail a 
                left join t_stock_in b on b.stock_in_id=a.stock_in_id 
                left join t_stock_in_batch c on c.batch_id=b.batch_id 
                where c.batch_id=' . $batchId . ' and c.status>=' . StockNotice::STATUS_SUBMIT . ' and b.status>=' . StockIn::STATUS_PASS . ' and a.goods_id=' . $goodsId . ' and a.unit=' . $unit;
        $sql = 'select ifnull(sum(quantity),0) as total_quantity from t_stock_in_detail where stock_id in(' . $sqlSub . ')';

        $res = Utility::query($sql);
        if (Utility::isNotEmpty($res)) {
            $data['quantity'] = $res[0]['total_quantity'];
        }
        $sql1 = 'select ifnull(sum(quantity),0) as total_quantity from t_stock_in_detail_sub where stock_id in(' . $sqlSub . ')';
        $res1 = Utility::query($sql1);
        if (Utility::isNotEmpty($res1)) {
            $data['quantity_sub'] = $res1[0]['total_quantity'];
        }

        return $data;
    }*/

    /*public static function getTotalStockInQuantity($batchId, $goodsId, $unit) {
        $data = array('quantity' => 0, 'quantity_sub' => 0);
        $sqlSub = 'select ifnull(detail_id,0) from t_stock_in_batch_detail where batch_id=' . $batchId . ' and goods_id=' . $goodsId . ' and unit=' . $unit;
        $sql = 'select ifnull(quantity_actual,0) as total_quantity from t_stock_in_batch_detail where detail_id in(' . $sqlSub . ')';
        $res = Utility::query($sql);
        if (Utility::isNotEmpty($res)) {
            $data['quantity'] = $res[0]['total_quantity'];
        }
        $sql1 = 'select ifnull(quantity_actual,0) as total_quantity from t_stock_in_batch_detail_sub where detail_id in(' . $sqlSub . ')';
        $res1 = Utility::query($sql1);
        if (Utility::isNotEmpty($res1)) {
            $data['quantity_sub'] = $res1[0]['total_quantity'];
        }
        return $data;
    }*/

    /**
     * @desc 获取总结算数量
     * @param $batchId
     * @param $goodsId
     * @param $unit
     * @return array
     */
    public static function getTotalSettlementQuantity($batchId, $goodsId, $unit) {
        $data = array('quantity' => 0, 'quantity_sub' => 0);
        /*
		$sqlSub = 'select ifnull(a.settle_id, 0) from t_stock_batch_settlement a 
                LEFT JOIN t_check_detail b ON b.detail_id = a.detail_id
                where a.batch_id=' . $batchId . ' and b.status=1 and b.check_status=1 and a.goods_id=' . $goodsId . ' and a.unit=' . $unit;
		*/
		$sqlSub = 'select ifnull(a.item_id, 0) from t_stock_batch_settlement a 
                LEFT JOIN t_stock_in_batch b ON b.batch_id = a.batch_id
                where a.batch_id=' . $batchId . ' and b.status='.StockNotice::STATUS_SETTLED.' and a.goods_id=' . $goodsId . ' and a.unit=' . $unit;
				
        $sql = 'select ifnull(sum(quantity),0) as total_quantity from t_stock_batch_settlement where item_id in(' . $sqlSub . ')';

        $res = Utility::query($sql);
        if (Utility::isNotEmpty($res)) {
            $data['quantity'] = $res[0]['total_quantity'];
        }

        $sql1 = 'select ifnull(sum(quantity),0) as total_quantity from t_stock_batch_settlement_sub where item_id in(' . $sqlSub . ')';
        $res1 = Utility::query($sql1);
        if (Utility::isNotEmpty($res1)) {
            $data['quantity_sub'] = $res1[0]['total_quantity'];
        }

        return $data;
    }

    /**
     * 判断当前发入库通知单下是否可以添加入库单
     * @param $status
     * @param $isVirtual 是否平移生成,虚拟单
     * @return bool
     */
    public static function isCanAddStockIn($status, $isVirtual){
        if(($status >= StockNotice::STATUS_SUBMIT && $status<StockNotice::STATUS_SETTLE_SUBMIT) && SplitEnum::IS_REALITY == $isVirtual){
            return true;
        }

        return false;
    }
}