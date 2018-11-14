<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/7 15:51
 * Describe：企业类型表
 */
class Ownership extends BaseActiveRecord {

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 't_ownership';
	}

	/**
	 * @desc 获取企业所有制枚举数组
	 * @return array
	 */
	public static function getOwnerships() {
		$sql = "select id,name from t_ownership where status=1 order by id asc";
		$data = Utility::query($sql);

		return $data;
	}

	public static function getOwnershipNameById($id) {
	    if(empty($id))
	        return "";
		$sql = "select id,name from t_ownership where id=".$id." and status=1 order by id asc";
		$data = Utility::query($sql);

		return $data[0]['name'];
	}
}