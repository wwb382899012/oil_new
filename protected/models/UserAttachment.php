<?php

/**
 * Desc: 用户附件信息
 * User: susieh
 * Date: 2017/4/13 11:31
 * Time: 11:31
 */
class UserAttachment extends BaseActiveRecord {
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 't_user_attachment';
	}
}