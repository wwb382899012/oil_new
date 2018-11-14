<?php

/**
 * Desc: 保理对接明细管理
 * User: susiehuang
 * Date: 2017/12/19 0009
 * Time: 10:03
 */
class FactoringDetailService {
    const FACTOR_DETAIL_CONTRACT_CODE_LOCK = 'oil_factor_detail_contract_code_lock';

    /**
     * @desc 获取指定条件的保理对接金额
     * @param int $factorId
     * @param string $condition
     * @return float
     */
    public static function getFactorAmountById($factorId, $condition = '') {
        if (Utility::checkQueryId($factorId)) {
            $sql = 'select ifnull(sum(amount), 0) as total_amount from t_factoring_detail where factor_id = ' . $factorId . $condition;
            $data = Utility::query($sql);
            if (Utility::isNotEmpty($data)) {
                return $data[0]['total_amount'];
            }
        }

        return 0;
    }


    /**
     * @desc 是否可添加保理对接申请
     * @param int $factorId
     * @return bool
     */
    public static function checkIsCanAdd($factorId) {
        if (Utility::checkQueryId($factorId)) {
            $factor = Factor::model()->findByPk($factorId);
            if (!empty($factor)) {
                $checkingAmount = FactoringDetailService::getFactorAmountById($factor->factor_id, ' and status = ' . FactorDetail::STATUS_SUBMIT);
                $buttedAmount = FactoringDetailService::getFactorAmountById($factor->factor_id, ' and status >= ' . FactorDetail::STATUS_PASS);

                return bccomp($factor->amount, $checkingAmount + $buttedAmount, 2) === 1;
            }
        }

        return false;
    }

    /**
     * @desc 获取附件信息
     * @param $id
     * @param $factorId
     * @return array
     */
    public static function getAttachments($id, $factorId) {
        $attachments = array();
        if (empty($id) || empty($factorId)) {
            return array();
        }
        if (!empty($type)) {
            $type = ' and type=' . $type;
        }

        $sql = 'select * from t_factoring_attachment where base_id=' . $factorId . ' and status=1 and type in(1,2) ' . $type . ' order by type asc';
        $data = Utility::query($sql);

        $sql1 = 'select * from t_factoring_attachment where base_id=' . $factorId . ' and detail_id=' . $id . ' and status=1 and type>2 ' . $type . ' order by type asc';
        $data1 = Utility::query($sql1);

        $data = array_merge_recursive($data, $data1);
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $v) {
                $attachments[$v["type"]][] = $v;
            }
        }

        return $attachments;
    }

    /**
     * @desc 必填附件校验
     * @param int $detailId
     * @param int $factorId
     * @return bool|string
     */
    public static function checkRequiredAttachmens($detailId, $factorId) {
        $attachMap = Map::$v['factor_attachment_type'];
        if (Utility::isNotEmpty($attachMap)) {
            foreach ($attachMap as $key => $row) {
                if (!empty($row['required'])) {
                    $query = '';
                    if ($row['id'] > 2) {
                        $query .= ' and detail_id=' . $detailId;
                    }
                    $sql = 'select * from t_factoring_attachment where base_id = ' . $factorId . $query . ' and type=' . $row['id'] . ' and status = 1';
                    $res = Utility::query($sql);
                    if (Utility::isEmpty($res)) {
                        return "*标注附件必传，请上传" . $row['name'];
                    }
                }
            }

            return true;
        }
    }

    /**
     * @desc 获取保理对接流水号
     * @param int $factorId
     * @param int $len 序列号位数
     * @return string
     */
    public static function generateFactorDetailContractCode($factorId, $len = 2) {
        if (Utility::checkQueryId($factorId)) {
            $factor = Factor::model()->findByPk($factorId);
            if (!empty($factor)) {
                $sql = 'select count(1) as total from t_factoring_detail where factor_id = ' . $factorId;
                $res = Utility::query($sql);
                $total = 0;
                if (Utility::isNotEmpty($res)) {
                    $total = $res[0]['total'];
                }
                $serial = $total + 1;
                /*if ($serial < 10) {
                    $serial = '00' . strval($serial);
                } elseif ($serial < 100) {
                    $serial = '0' . strval($serial);
                }*/

                $serial = "000000000000000000" . $serial;

                $serial = substr($serial, strlen($serial) - $len);

                return $factor->contract_code . $serial;
            }
        } else {
            return '';
        }
    }

    /**
     * @desc 获取资金对接流水号
     * @param int $factorId
     * @param int $len 序列号位数
     * @param object $factor 保理对象
     * @return string
     */
    public static function generateFactorDetailContractCodeFund($factorId, $len = 2, $factor = null) {
        if (Utility::checkQueryId($factorId)) {
            if (empty($factor)) {
                $factor = Factor::model()->findByPk($factorId);
            }
            $serial = IDService::getSerialNum('oil.factor.detail.fund.water.code');
            $serial = "000000000000000000" . $serial;
            $serial = substr($serial, strlen($serial) - $len);

            return $factor->contract_code_fund . $serial;
        }

        return '';
    }
}