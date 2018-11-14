<?php

/**
 * Desc: 子合同交易代理费服务
 * User: susiehuang
 * Date: 2017/8/30 0031
 * Time: 11:05
 */
class ContractAgentDetailService {
    /**
     * @desc 检查交易代理费参数是否合法
     * @param array $agentDetails
     * @return bool|string
     */
    public static function checkParamsValid($agentDetails) {
        if (Utility::isNotEmpty($agentDetails)) {
            foreach ($agentDetails as $key => $row) {
                $requiredParams = array('type', 'unit');
                /*if ($row['type'] == ConstantMap::AGENT_FEE_CALCULATE_BY_AMOUNT) {
                    array_push($requiredParams, 'price');
                } elseif ($row['type'] == ConstantMap::AGENT_FEE_CALCULATE_BY_PRICE) {
                    array_push($requiredParams, 'fee_rate');
                }*/
                //必填参数校验
                if (!Utility::checkRequiredParamsNoFilterInject($row, $requiredParams)) {
                    return BusinessError::outputError(OilError::$AGENT_FEE_REQUIRED_PARAMS_CHECK_ERROR);
                }

                if ($row['type'] == ConstantMap::AGENT_FEE_CALCULATE_BY_AMOUNT) {
                    //array_push($requiredParams, 'price');
                    if (!array_key_exists('price', $row)) {
                        return BusinessError::outputError(OilError::$AGENT_FEE_PRICE_ERROR);
                    }
                } elseif ($row['type'] == ConstantMap::AGENT_FEE_CALCULATE_BY_PRICE) {
                    //array_push($requiredParams, 'fee_rate');
                    if (!array_key_exists('fee_rate', $row)) {
                        return BusinessError::outputError(OilError::$AGENT_FEE_PRICE_ERROR);
                    }
                }
            }
        } else {
            return BusinessError::outputError(OilError::$CONTRACT_ADD_NOT_AGENT_FEE);
        }

        return true;
    }

    public static function reverseAgentDetails($agentDetails) {
        $res = array();
        if (Utility::isNotEmpty($agentDetails)) {
            foreach ($agentDetails as $key => $row) {
                if (is_array($row->attributes)) {
                    $detail = array();
                    foreach ($row->attributes as $attrKey => $attrVal) {
                        // $detail['agent_' . $attrKey] = $attrVal; 
                        $detail[$attrKey] = $attrVal;
                    }
                    $detail['goods_name'] = !empty($row->goods) ? $row->goods->name : '';
                    $res[$key] = $detail;
                }
            }
        }

        return $res;
    }
}
