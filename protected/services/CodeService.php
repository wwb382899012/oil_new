<?php

/**
 * Created by youyi000.
 * DateTime: 2017/9/5 15:51
 * Describe：
 */
class CodeService {

    /**
     * 获取项目编码
     * @param $corporationId
     * @param $managerId
     * @param $type
     * @return array
     */
    public static function getProjectCode($corporationId, $managerId, $type) {
        $res = array('code' => ConstantMap::VALID, 'project_code' => '', 'msg' => '');
        if (!Utility::checkQueryId($corporationId) || !Utility::checkQueryId($managerId) || !Utility::checkQueryId($type)) {
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$PARAMS_PASS_ERROR);

            return $res;
        }

        $code = ConstantMap::PROJECT_CODE_START_STR;
        $str = Corporation::getCorporationCode($corporationId);
        if (empty($str)) {
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$CORPORATION_NO_CODE, array('corporation_id' => $corporationId));

            return $res;
        }
        $code .= $str;

        $str = UserExtra::getUserCode($managerId);
        if (empty($str)) {
            $userInfo = SystemUser::getUser($managerId);
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$BUSINESS_MANAGER_NO_CODE, array('name' => $userInfo['name']));

            return $res;
        }
        $code .= $str;

        $str = Map::$v['project_business_type'][$type]['code'];
        if (empty($str)) {
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$PROJECT_TYPE_NO_CODE, array('type' => $type));

            return $res;
        }
        $code .= $str;

        $codeId = IDService::getProjectCodeId();
        if (empty($codeId)) {
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$PROJECT_CODE_SERIAL_GENERATE_ERROR);

            return $res;
        }
        $code .= $codeId;

        $res['project_code'] = $code;

        return $res;
    }

    /**
     * 获取合同编码
     * @param $corporationId
     * @param $managerId
     * @param $projectType
     * @param $category
     * @return array
     */
    public static function getContractCode($corporationId, $managerId, $projectType, $category) {
        $res = array('code' => ConstantMap::VALID, 'contract_code' => '', 'msg' => '');
        if (!Utility::checkQueryId($corporationId) || !Utility::checkQueryId($managerId) || !Utility::checkQueryId($projectType) || !Utility::checkQueryId($category)) {
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$PARAMS_PASS_ERROR);

            return $res;
        }

        $code = '';
        $str = Corporation::getCorporationCode($corporationId);
        if (empty($str)) {
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$CORPORATION_NO_CODE, array('corporation_id' => $corporationId));

            return $res;
        }
        $code .= $str;

        $str = UserExtra::getUserCode($managerId);
        if (empty($str)) {
            $user = SystemUser::getUser($managerId);
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$BUSINESS_MANAGER_NO_CODE, array('name' => $user['name']));

            return $res;
        }
        $code .= $str;

        $str = Map::$v['project_business_type'][$projectType]['code'];
        if (empty($str)) {
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$PROJECT_TYPE_NO_CODE, array('type' => $projectType));

            return $res;
        }
        $code .= $str;

        $code .= date('ymd');

        $type = 1;
        if ($category == ConstantMap::SELL_SALE_CONTRACT_TYPE_INTERNAL) {
            $type = 2;
        }
        $str = Map::$v['contract_config'][$type][$category]['code'];
        if (empty($str)) {
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$CONTRACT_TYPE_NO_CODE, array('type' => $category));

            return $res;
        }
        $code .= $str;

        $codeId = IDService::getContractCodeId();
        if (empty($codeId)) {
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$CONTRACT_CODE_SERIAL_GENERATE_ERROR);

            return $res;
        }
        $code .= $codeId;

        $res['contract_code'] = $code;

        return $res;
    }

    /**
     * 获取发货单编码
     * @param $corporationId
     * @param $date
     * @return array
     */
    public static function getDeliveryOrderCode($corporationId, $date = '') {
        $res = array('code' => ConstantMap::VALID, 'data' => '', 'msg' => '');
        if (!Utility::checkQueryId($corporationId)) {
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$PARAMS_PASS_ERROR);

            return $res;
        }

        $code = '';
        $date = empty($date) ? date('ymd') : date('ymd', strtotime($date));
        $str = Corporation::getCorporationCode($corporationId);
        if (empty($str)) {
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$CORPORATION_NO_CODE, array('corporation_id' => $corporationId));

            return $res;
        }

        $code .= $str . $date . DeliveryOrder::DELIVERY_ORDER_CODE_FIXED_STR . IDService::getSerialNum(DeliveryOrder::$deliveryOrderCodeKey);

        $res['data'] = $code;

        return $res;
    }

    /**
     * @desc 生成保理对接编号
     * @param int $corpId
     * @param string $date
     * @return string
     */
    public static function getFactoringCode($corpId, $date='') {
        $res = array('code' => ConstantMap::VALID, 'data' => '', 'msg' => '');
        if (!Utility::checkQueryId($corpId)) {
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$PARAMS_PASS_ERROR);

            return $res;
        }

        $code = '';
        $str = Corporation::getCorporationCode($corpId);
        if (empty($str)) {
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$CORPORATION_NO_CODE, array('corporation_id' => $corpId));

            return $res;
        }

        $code .= $str . IDService::getFactoringCodeSerial($date, $str);

        $res['data'] = $code;

        return $res;
    }

    /**
     * @desc 生成资金对接编号
     * @param string $month
     * @return string
     */
    public static function getFactoringFundCode($month = '') {
        $factorCodeSystemId =  Mod::app()->params['factor_code_server_id'];
        return 'BS' . $factorCodeSystemId . IDService::getFactoringFundCodeSerial($month);
    }
}