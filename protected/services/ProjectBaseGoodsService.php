<?php

/**
 * Desc: 项目发起交易明细服务
 * User: susiehuang
 * Date: 2017/8/30 0031
 * Time: 11:05
 */
class ProjectBaseGoodsService {
    /**
     * @desc 获取项目交易明细
     * @param bigint $project_id
     * @return array
     */
    public static function getProjectTransactions($project_id) {
        $result = array();
        if (Utility::checkQueryId($project_id)) {
            $sql = 'select a.*, b.up_partner_id, b.down_partner_id,a.unit_convert_rate, d.name as goods_name from t_project_base_goods a
                    left join t_project_base b on a.base_id = b.base_id 
                    left join t_project c on a.project_id = c.project_id 
                    left join t_goods d on d.goods_id = a.goods_id 
                    where a.project_id = ' . $project_id . ' order by a.relative_id';
            $res = Utility::query($sql);
            if (Utility::isNotEmpty($res)) {
                foreach ($res as $row) {
                    $result[$row['relative_id']]['goods_id'] = $row['goods_id'];
                    $result[$row['relative_id']]['goods_name'] = $row['goods_name'];
                    $result[$row['relative_id']]['goods_describe'] = $row['goods_describe'];
                    $result[$row['relative_id']]['quantity'] = $row['quantity'];
                    $result[$row['relative_id']]['unit_convert_rate'] = $row['unit_convert_rate'];
                    $result[$row['relative_id']]['unit'] = $row['unit'];
                    if ($row['type'] == ConstantMap::BUY_TYPE) {
                        $result[$row['relative_id']]['purchase_detail_id'] = $row['detail_id'];
                        $result[$row['relative_id']]['purchase_price'] = $row['price'];
                        $result[$row['relative_id']]['purchase_currency'] = $row['currency'];
                        $result[$row['relative_id']]['purchase_amount'] = $row['amount'];
                        $result[$row['relative_id']]['purchase_amount_cny'] = $row['amount_cny'];
                    } elseif ($row['type'] == ConstantMap::SALE_TYPE) {
                        $result[$row['relative_id']]['sale_detail_id'] = $row['detail_id'];
                        $result[$row['relative_id']]['sale_price'] = $row['price'];
                        $result[$row['relative_id']]['sell_currency'] = $row['currency'];
                        $result[$row['relative_id']]['sale_amount'] = $row['amount'];
                        $result[$row['relative_id']]['sale_amount_cny'] = $row['amount_cny'];
                    } else {
                        $result[$row['relative_id']]['purchase_detail_id'] = 0;
                        $result[$row['relative_id']]['sale_detail_id'] = 0;
                        $result[$row['relative_id']]['sale_price'] = 0;
                        $result[$row['relative_id']]['sell_currency'] = 0;
                        $result[$row['relative_id']]['sale_amount'] = 0;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @desc 检查交易明细参数是否合法
     * @param array $transactions
     * @return bool|string
     */
    public static function checkParamsValid($transactions) {
        if (Utility::isNotEmpty($transactions)) {
            $invalid = false;
            foreach ($transactions as $key => $row) {
                $requiredParams = array('goods_id', 'quantity', 'unit');
                if (!empty($row['up_partner_id'])) {
                    array_push($requiredParams, 'purchase_price');
                    array_push($requiredParams, 'purchase_amount');
                }
                if (!empty($row['down_partner_id'])) {
                    array_push($requiredParams, 'sale_price');
                    array_push($requiredParams, 'sale_amount');
                }
                //必填参数校验
                if (!Utility::checkRequiredParamsNoFilterInject($row, $requiredParams)) {
                    $invalid = true;
                    break;
                }

                if (!empty($row['up_partner_id'])) {
                    if (bccomp(($row['purchase_price'] * $row['quantity']), $row['purchase_amount']) == 1) {
                        return BusinessError::outputError(OilError::$TRANSACTION_PURCHASE_AMOUNT_ERROR);
                    }
                }

                if (!empty($row['down_partner_id'])) {
                    if (bccomp(($row['sale_price'] * $row['quantity']), $row['sale_amount']) == 1) {
                        return BusinessError::outputError(OilError::$TRANSACTION_SALE_AMOUNT_ERROR);
                    }
                }
            }

            if ($invalid) {
                return BusinessError::outputError(OilError::$TRANSACTION_REQUIRED_PARAMS_CHECK_ERROR);
            }
        } else {
            return BusinessError::outputError(OilError::$PROJECT_LAUNCH_NOT_TRANSACTION);
        }

        return true;
    }

    /**
     * @desc 格式化交易明细参数，将一条交易明细拆分成采购和销售两条明细，并统一push到数组中
     * @param array $transactions
     * @return array
     */
    public static function formatGoodsTransaction($transactions) {
        $res = array();
        if (Utility::isNotEmpty($transactions)) {
            foreach ($transactions as $key => $row) {
                if(!empty($row['up_partner_id'])) {
                    $buyData['detail_id'] = $row['detail_id'];
                    $buyData['type'] = ConstantMap::BUY_TYPE;
                    $buyData['goods_id'] = $row['goods_id'];
                    $buyData['goods_describe'] = $row['goods_describe'];
                    $buyData['price'] = $row['purchase_price'];
                    $buyData['quantity'] = $row['quantity'];
                    $buyData['unit'] = $row['unit'];
                    $buyData['amount_cny'] = $row['purchase_amount'];
                    $buyData['amount'] = $row['purchase_amount'];
                    $buyData['currency'] = $row['purchase_currency'];
                    $buyData['relative_id'] = $key + 1;
                    $buyData['unit_convert_rate'] = $row['unit_convert_rate'];
                    array_push($res, $buyData);
                }

                if(!empty($row['down_partner_id'])) {
                    $saleData['detail_id'] = $row['detail_id'];
                    $saleData['type'] = ConstantMap::SALE_TYPE;
                    $saleData['goods_id'] = $row['goods_id'];
                    $saleData['goods_describe'] = $row['goods_describe'];
                    $saleData['price'] = $row['sale_price'];
                    $saleData['quantity'] = $row['quantity'];
                    $saleData['unit'] = $row['unit'];
                    $saleData['amount_cny'] = $row['sale_amount'];
                    $saleData['amount'] = $row['sale_amount'];
                    $saleData['currency'] = $row['sell_currency'];
                    $saleData['relative_id'] = $key + 1;
                    $saleData['unit_convert_rate'] = $row['unit_convert_rate'];
                    array_push($res, $saleData);
                }
            }
        }

        return $res;
    }

    /**
     * @desc 根据类型获取项目交易明细
     * @param bigint $project_id
     * @return array
     */
    public static function getProjectTransByType($project_id, $type) {
        $result = array();
        if (Utility::checkQueryId($project_id) && Utility::checkQueryId($type)) {
            $sql = 'select a.*, c.status as project_status,d.name as goods_name from t_project_base_goods a 
                    left join t_project_base b on a.base_id = b.base_id 
                    left join t_project c on a.project_id = c.project_id 
                    left join t_goods d on d.goods_id = a.goods_id 
                    where a.project_id = ' . $project_id . ' and a.type=' . $type . ' order by a.relative_id';
            $res = Utility::query($sql);
            if (Utility::isNotEmpty($res)) {
                foreach ($res as $row) {
                    $result[$row['relative_id']]['goods_id'] = $row['goods_id'];
                    $result[$row['relative_id']]['goods_name'] = $row['goods_name'];
                    $result[$row['relative_id']]['goods_describe'] = $row['goods_describe'];
                    $result[$row['relative_id']]['quantity'] = $row['quantity'];
                    $result[$row['relative_id']]['unit'] = $row['unit'];
                    $result[$row['relative_id']]['detail_id'] = $row['detail_id'];
                    $result[$row['relative_id']]['price'] = $row['price'];
                    $result[$row['relative_id']]['currency'] = $row['currency'];
                    $result[$row['relative_id']]['amount'] = $row['amount'];
                    $result[$row['relative_id']]['type'] = $type;
                    $result[$row['relative_id']]['unit_convert_rate'] = $row['unit_convert_rate'];
                }
            }
        }

        return $result;
    }


    /**
     * @desc 保存商品交易明细
     * @param array $transactions
     * @param int $project_id
     * @param int $base_id
     * @return array|int
     */
    public static function saveGoodsTransactions($transactions, $project_id, $base_id) {
        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' pass transaction params are:' . json_encode($transactions) . ' project_id is:' . $project_id . ', base_id is:' . $base_id);
        if (Utility::isEmpty($transactions) || empty($project_id) || empty($base_id)) {
            BusinessException::throw_exception(OilError::$PARAMS_PASS_ERROR);
        }
        $transactions = ProjectBaseGoodsService::formatGoodsTransaction($transactions);
        Mod::log(__CLASS__ . '::' . __FUNCTION__ . 'in line ' . __LINE__ . ' after formatting transaction params are:' . json_encode($transactions));

        $sql = "select * from t_project_base_goods where project_id=" . $project_id . " and base_id = " . $base_id;
        $data = Utility::query($sql);
        $p = array();
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $v) {
                $p[$v["detail_id"]] = $v["detail_id"];
            }
        }

        $sqls   = array();
        $values = array();
        $idArr  = array();
        $idStr  = "";

        foreach ($transactions as $row) {
            if (array_key_exists($row["detail_id"], $p)) {
                $sqls[] = "update t_project_base_goods set 
                           type = " . $row["type"] . ",
                           goods_id = '" . $row["goods_id"] . "',
                           goods_describe = '" . $row["goods_describe"] . "',
                           price = " . $row["price"] . ",
                           quantity = " . $row["quantity"] . ",
                           unit = " . $row["unit"] . ",
                           amount_cny = " . $row["amount_cny"] . ",
                           amount = " . $row["amount"] . ",
                           currency = " . $row["currency"] . ",
                           relative_id = " . $row["relative_id"] . ",
                           unit_convert_rate = " . $row["unit_convert_rate"] . ",
                           update_user_id = " . Utility::getNowUserId() . ",
                           update_time = now()
                           where detail_id = " . $row["detail_id"];
                unset($p[$row["detail_id"]]);
            } else {
                $values[] = "(" . $base_id . "," . $project_id . "," . $row["type"] . "," . $row["goods_id"] . ",'" . $row["goods_describe"] . "'," . $row["price"] . "," . $row["quantity"] . "," . $row["unit"] . "," . $row["amount_cny"] . "," . $row["amount"] . "," . $row["currency"] . "," . $row["relative_id"] . ",". $row['unit_convert_rate'] . ',' . ConstantMap::STATUS_NEW . ",now()," . Utility::getNowUserId() . ",now()," . Utility::getNowUserId() . ",now())";
            }

            $idArr[] = $row['goods_id'];
        }
        $sql = "";
        if (count($sqls) > 0) {
            $sql .= implode(";", $sqls) . ";";
        }

        if (count($values) > 0) {
            $sql .= "insert into t_project_base_goods(base_id,project_id,type,goods_id,goods_describe,price,quantity,unit,amount_cny,amount,currency,relative_id,unit_convert_rate,status,status_time,create_user_id,create_time,update_user_id,update_time) values " . implode(",", $values) . ";";
        }

        if (count($p) > 0) {
            $sql .= "delete from t_project_base_goods where detail_id in(" . implode(",", $p) . ");";
        }

        if(count($idArr)>0)
            $idStr = implode(",", $idArr);

        $sql .= "update t_project_base p set p.goods_name = (select GROUP_CONCAT(name SEPARATOR '|') goods_name from t_goods where goods_id in (".$idStr.")) where p.project_id=".$project_id.";";

        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' need to execute sqls are:' . json_encode($sql));
        if (!empty($sql)) {
            Utility::execute($sql);
        }
    }


    public static function getProjectPaymentByType($project_id, $type) {
        $result = array();
        if (Utility::checkQueryId($project_id) && Utility::checkQueryId($type)) {
            $sql = 'select pp.* from t_payment_plan pp 
                    left join t_project p on pp.project_id = p.project_id 
                    where pp.project_id = ' . $project_id . ' and pp.type=' . $type . ' order by pp.plan_id';
            $res = Utility::query($sql);
            if (Utility::isNotEmpty($res)) {
                foreach ($res as $row) {
                    // 目前展示使用,全部先选上
                    $result[$row['plan_id']] = $row;
                }
            }
        }

        return $result;
    }


    public static function getGoodsInfos($goods) {
        $array = array();
        foreach ($goods as $good) {
            $array[] = $good->attributes;
        }
        return $array;
    }
}
