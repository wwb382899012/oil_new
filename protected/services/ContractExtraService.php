<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/8 10:46
 * Describe：
 */
class ContractExtraService {
    /*public static function formatExtraParams($params, $type, $category) {
        $res = array();
        if (Utility::isNotEmpty($params) && !empty($type) && !empty($category)) {
            $params = $params[$type][$category]['extraItems'];
            foreach ($params as $key => $row) {
                $res[$row['key']] = $row['value'];
                if ($row['type'] == 'koMultipleSelect' && is_array($row['value'])) {
                    $res[$row['key']] = implode(',', $row['value']);
                }
            }
        }

        return $res;
    }*/

    /**
     * @desc 参数校验
     * @param array $params
     * @param int $category
     * @param int $type
     * @return bool|string
     */
    public static function checkParamsValid($params, $type, $category) {
        if (Utility::isNotEmpty($params) && Utility::checkQueryId($type) && Utility::checkQueryId($category)) {
            $paramsMap = Map::$v['contract_config'][$type][$category]['extra'];
            if (Utility::isNotEmpty($paramsMap)) {
                //必填参数校验
                foreach ($paramsMap as $row) {
                    /*if ($row['required'] && empty($params[$row['key']])) {
                        return BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR);
                    }*/
                    foreach ($params as $k => $v) {
                        if($row['key'] == $v['key'] && $row['required'] && empty($v['value'])) {
                            return BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR);
                        }
                    }
                }
            }

            return true;
        }

        return BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR);
    }

    /**
     * @desc 格式化前端展示配置value值
     * @param array $items
     * @param int $type
     * @param int $category
     * @return array
     */
    public static function reverseExtraData($items, $type, $category) {
        $config = Map::$v['contract_config'];
        if (!empty($items)) {
            $extra = $config[$type][$category]['extra'];
            foreach ($extra as $key => $row) {
                foreach ($items as $k => $v) {
                    if($v['key'] == $row['key']) {
                        $config[$type][$category]['extra'][$key]['name'] = $v['name'];
                        $config[$type][$category]['extra'][$key]['value'] = $v['value'];
                        if ($row['type'] == 'koMultipleSelect' && is_string($v['value'])) {
                            $config[$type][$category]['extra'][$key]['value'] = explode(',', $v['value']);
                        }
                    }
                }
            }
            /*foreach ($extra as $key => $row) {
                $config[$type][$category]['extra'][$key]['value'] = $items[$row['key']];
                if ($row['type'] == 'koMultipleSelect' && is_string($items[$row['key']])) {
                    $config[$type][$category]['extra'][$key]['value'] = explode(',', $items[$row['key']]);
                }
            }*/
        }

        return $config;
    }

    /**
     * @desc 获取合同配置
     * @param int $project_type
     * @param int $contract_type
     * @param int $buy_sell_type
     * @return array
     */
    public static function getContractConfig($project_type, $contract_type, $buy_sell_type=0) {
        $config     = Map::$v['contract_config'];
        $asConfig   = array();
        if (!empty($project_type)) {
            if((in_array($project_type, ConstantMap::$buy_select_contract_type) &&
                $contract_type==ConstantMap::BUY_TYPE) ||
                ($project_type==ConstantMap::PROJECT_TYPE_SELF_IMPORT &&
                $buy_sell_type==ConstantMap::FIRST_BUY_LAST_SALE)){
                unset($config[ConstantMap::BUY_TYPE][ConstantMap::BUY_SALE_CONTRACT_TYPE_INTERNAL]);
                $asConfig[ConstantMap::BUY_TYPE] = $config[ConstantMap::BUY_TYPE];
            }else if((in_array($project_type, ConstantMap::$buy_static_contract_type) &&
                $contract_type==ConstantMap::BUY_TYPE) ||
                ($project_type==ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE &&
                $buy_sell_type==ConstantMap::FIRST_BUY_LAST_SALE)){
                $asConfig[ConstantMap::BUY_TYPE][ConstantMap::BUY_SALE_CONTRACT_TYPE_INTERNAL] = $config[ConstantMap::BUY_TYPE][ConstantMap::BUY_SALE_CONTRACT_TYPE_INTERNAL];
            }else if((in_array($project_type, array_merge(ConstantMap::$buy_select_contract_type, ConstantMap::$buy_static_contract_type)) &&
                $contract_type==ConstantMap::SALE_TYPE) ||
                (($project_type==ConstantMap::PROJECT_TYPE_SELF_IMPORT ||
                $project_type==ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE ) &&
                $buy_sell_type==ConstantMap::FIRST_SALE_LAST_BUY)){
                $asConfig[ConstantMap::SALE_TYPE][ConstantMap::SELL_SALE_CONTRACT_TYPE_INTERNAL] = $config[ConstantMap::SALE_TYPE][ConstantMap::SELL_SALE_CONTRACT_TYPE_INTERNAL];
            }
        }

        return $asConfig;
    }
}