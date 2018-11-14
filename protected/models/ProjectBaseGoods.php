<?php

/**
 * Desc: 项目发起商品交易明细
 * User: susiehuang
 * Date: 2017/8/29 0031
 * Time: 10:05
 */
class ProjectBaseGoods extends BaseActiveRecord {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return "t_project_base_goods";
    }

    /**
     * @desc 项目发起商品交易明细，删除成功返回1，否则返回错误信息或0
     * @param int $detail_id
     * @return bool|string
     */
    public static function del($detail_id) {
        if (!Utility::checkQueryId($detail_id) || !Utility::isIntString($detail_id)) {
            return BusinessError::outputError(OilError::$PARAMS_PASS_ERROR);
        }

        $sql = "delete from t_project_base_goods a 
                left join t_project b on b.project_id = a.project_id 
                left join t_project_base c on c.base_id = a.base_id
                where a.detail_id=" . $detail_id . " and b.status < " . Project::STATUS_SUBMIT . ";";
        $res = Utility::execute($sql);
        if ($res == 1) {
            return true;
        } else {
            return BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $res));
        }
    }
}