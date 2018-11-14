<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/9 16:07
 * Describe：
 *  库存相关操作
 */

class StockService
{


    /**
     * 出库存
     * @param $stockId
     * @param $quantity
     * @return bool
     */
    public static function out($stockId,$quantity)
    {
        $rows=Stock::model()->updateByPk($stockId
            ,array(
                "quantity_balance"=>new CDbExpression("quantity_balance-".$quantity),
                "quantity_out"=>new CDbExpression("quantity_out+".$quantity),
                "update_time"=>new CDbExpression("now()")
                )
            ,"quantity-quantity_out-quantity_frozen>=".$quantity
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }

    /**
     * 退款库
     * @param $stockId
     * @param $quantity
     * @return bool
     */
    public static function refund($stockId,$quantity)
    {
        $rows=Stock::model()->updateByPk($stockId,
            array(
                    "quantity_balance"=>new CDbExpression("quantity_balance+".$quantity),
                     "quantity_out"=>new CDbExpression("quantity_out-".$quantity),
                     "update_time"=>new CDbExpression("now()")
                 )
            ,"quantity_out>=".$quantity
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }

    /**
     * 冻结库存
     * @param $stockId
     * @param $quantity
     * @return bool
     */
    public static function freeze($stockId,$quantity)
    {
        $rows=Stock::model()->updateByPk($stockId
            ,array(
                "quantity_balance"=>new CDbExpression("quantity_balance-".$quantity),
                "quantity_frozen"=>new CDbExpression("quantity_frozen+".$quantity),
                "update_time"=>new CDbExpression("now()")
            )
            ,"quantity-quantity_out-quantity_frozen>=".$quantity
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }

    /**
     * 解冻库存
     * @param $stockId
     * @param $quantity
     * @return bool
     */
    public static function unFreeze($stockId,$quantity)
    {
        $rows=Stock::model()->updateByPk($stockId
            ,array(
                "quantity_balance"=>new CDbExpression("quantity_balance+".$quantity),
                "quantity_frozen"=>new CDbExpression("quantity_frozen-".$quantity),
                "update_time"=>new CDbExpression("now()")
            )
            ,"quantity_frozen>=".$quantity
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }


    /**
     * 增加库存，此方法只在实际有库存增加时才调用，比如盘点时有盘赢，如果退库，调用refund！！！
     * @param $stockId
     * @param $quantity
     * @return bool
     */
    public static function add($stockId,$quantity)
    {
        $rows=Stock::model()->updateByPk(
            $stockId,
            array(
                   "quantity"=>new CDbExpression("quantity+".$quantity),
                   "quantity_balance"=>new CDbExpression("quantity_balance+".$quantity),
                   "update_time"=>new CDbExpression("now()")
            )
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }

    /**
     * 减小库存，此方法只在实际有库存减小时才调用，比如盘点时有盘亏等场景；如果出库，调用out！！！
     * @param $stockId
     * @param $quantity
     * @return bool
     */
    public static function reduce($stockId,$quantity)
    {
        $rows=Stock::model()->updateByPk($stockId
            ,array(
                                 "quantity_balance"=>new CDbExpression("quantity_balance-".$quantity),
                                 "quantity"=>new CDbExpression("quantity-".$quantity),
                                 "update_time"=>new CDbExpression("now()")
                                         )
            ,"quantity_balance >= ".$quantity." and quantity>=".$quantity
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }

    /**
     * @desc 入库单审批通过添加库存信息
     * @param int $stock_in_id
     * @throws
     */
    public static function addStockAfterStockInPass($stock_in_id) {
        if (Utility::checkQueryId($stock_in_id)) {
            $data = StockIn::model()->with('details', 'details.sub')->findByPk($stock_in_id);
            if (empty($data->stock_in_id)) {
                BusinessException::throw_exception(OilError::$STOCK_IN_NOT_EXIST, array('stock_in_id' => $stock_in_id));
            }

            if (Utility::isNotEmpty($data->details)) {
                foreach ($data->details as $key => $row) {
                    $stockModel = Stock::model()->findByPk($row['stock_id']);
                    if (empty($stockModel->stock_id)) {
                        $stockModel = new Stock();
                    }
                    $params = $row->getAttributes(true, array('status_time', 'status', 'remark', 'create_user_id', 'create_time', 'update_user_id', 'update_time'));
                    $params['quantity_sub'] = $row->sub->quantity;
                    $params['unit_sub'] = $row->sub->unit;
                    $params['quantity_balance'] = $row->quantity;

                    $isInDbTrans = Utility::isInDbTrans();
                    if (!$isInDbTrans) {
                        $db = Mod::app()->db;
                        $trans = $db->beginTransaction();
                    }

                    try {
                        $stockModel->setAttributes($params, false);
                        $stockModel->save();

                        //添加入库明细日志
                        $stockLogParams = $stockModel->getAttributes(true, Utility::getCommonIgnoreAttributes());
                        $stockLogParams['type'] = ConstantMap::STOCK_TYPE_IN;
                        $stockLogParams['to_contract_id'] = $stockModel->contract_id;
                        $stockLogParams['relation_id'] = $stockModel->stock_id;
                        StockLog::addStockLog($stockLogParams);

                        if (!$isInDbTrans) {
                            $trans->commit();
                        }

                    } catch (Exception $e) {
                        if (!$isInDbTrans) {
                            try {
                                $trans->rollback();
                            } catch (Exception $ee) {
                                throw new Exception($ee->getMessage());
                            }
                        } else {
                            throw new Exception($e->getMessage());
                        }
                    }
                }
            }
        }
    }

    /**
     * @desc 获取商品库存明细
     * @param array $params
     * @return array
     */
    public static function getStockDetail($params) {
        $data = array();
        if (Utility::checkQueryId($params['corporationId']) && Utility::checkQueryId($params['goodsId']) && Utility::checkQueryId($params['unit'])) {

            $sql = 'select b.corporation_id,a.goods_id,a.unit,a.store_id,c.name as store_name,sum(a.quantity_balance) AS quantity_active,sum(a.quantity_frozen) AS quantity_frozen,sum(a.quantity_balance)+sum(a.quantity_frozen) as quantity 
                    from t_stock a 
                    left join t_contract b on a.contract_id = b.contract_id 
                    left join t_storehouse c on c.store_id = a.store_id
                    where b.corporation_id = ' . $params['corporationId'] . ' and a.goods_id = ' . $params['goodsId'] . ' and a.unit = ' . $params['unit'] . ' 
                    group by a.store_id';

            $data = Utility::query($sql);
        }

        return $data;
    }
}