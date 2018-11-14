<?php

/**
 * Desc: 用户附加信息
 * User: susieh
 * Date: 2017/4/13 11:31
 * Time: 11:31
 */
class UserExtra extends BaseCacheActiveRecord
{
    /**
     * 获取缓存key
     * @param string $key
     * @return string
     */
    public static function getCacheKey($key="")
    {
        return static::$cacheKeyPrefix.__CLASS__.$key;
    }

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 't_user_extra';
	}

	public function relations()
    {
        return array(
            "credit"=>array(self::HAS_ONE , "UserCreditDetail", "user_id"),
        );
    }

    /**
	 * @desc 保存，同时修改system_user表中信息
	 * @return bool|string
	 * @throws Exception
	 */
	public function save($runValidation=true,$attributes=null) {
		$isInDbTrans = Utility::isInDbTrans();
		if (!$isInDbTrans) {
			$db = Mod::app()->db;
			$trans = $db->beginTransaction();
		}

		try {
			parent::save();
			//更新system_user表信息
			$this->updateSystemUserInfo();
			if (!$isInDbTrans) {
				$trans->commit();
			}

			return 1;
		} catch (Exception $e) {
			if (!$isInDbTrans) {
				try {
					$trans->rollback();
				} catch (Exception $ee) {
				}

				return $e->getMessage();
			} else {
				throw $e;
			}
		}
	}

	/**
	 * @desc 更新system_user表信息
	 * @return array|int
	 */
	public function updateSystemUserInfo() {
		$name = $this->name;
		$phone = $this->phone;
		$status = $this->status;
		$remark = $this->remark;
		$update_time = date("Y-m-d H:i:s");
		$update_user_id = Utility::getNowUserId();

		$sql = "update t_system_user set name='" . $name . "', phone='" . $phone . "', status=" . $status . ", remark='" . $remark . "', update_user_id=" . $update_user_id . ", update_time='" . $update_time . "' where user_id=" . $this->user_id;
		Utility::executeSql($sql);
	}

	/**
	 * @desc 获取业务负责人编码
	 * @param int $manager_id
	 * @return object
	 */
    public static function getManagerInfo($manager_id) {
	    if(Utility::checkQueryId($manager_id)){
            $obj = UserExtra::model()->findByPk($manager_id, 'status = :status', array('status' => ConstantMap::STATUS_VALID));
            return $obj;
        }
        return null;
	}

    /**
     * 获取用户基本信息
     * @param $userId
     * @return array|mixed
     */
    public static function getUserExtraInfo($userId)
    {
        $res=self::getCache($userId);
        if(!empty($res))
            return json_decode($res,true);

        $model=self::model()->findByPk($userId);
        if(!empty($model)) {
            self::setCache($userId,json_encode($model->attributes));
            return $model->attributes;
        }
        return array();
    }

    /**
     * 获取用户编码
     * @param $userId
     * @return mixed|string
     */
    public static function getUserCode($userId)
    {
        $data=self::getUserExtraInfo($userId);
        if(!empty($data))
            return trim($data["code"]);
        else
            return "";
    }

}