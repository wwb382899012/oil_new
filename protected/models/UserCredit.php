<?php

/**
 * Created by youyi000.
 * DateTime: 2017/4/12 14:51
 * Describe：
 */
class UserCredit extends BaseActiveRecord
{

    const STATUS_SUBMIT=2;//已申请待确认
    const STATUS_REJECT=-1;//已拒绝
    const STATUS_CONFIRM=6;//已确认

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_user_credit";
    }

    public function beforeSave()
    {
        if ($this->isNewRecord)
        {
            if (empty($this->create_time))
                $this->create_time = new CDbExpression("now()");
            if (empty($this->create_user_id))
                $this->create_user_id= Utility::getNowUserId();
        }
        if ($this->update_time == $this->getOldAttribute("update_time"))
        {
            $this->update_time = new CDbExpression("now()");
            $this->update_user_id = Utility::getNowUserId();
        }
        return parent::beforeSave(); // TODO: Change the autogenerated stub
    }

	public static function formatUserCreditDetailInfo($rows) {
		if (is_array($rows) && count($rows) > 0) {
			foreach ($rows as $key => $row) {
				$rows[$key]['up_quantity'] = 0;
				$rows[$key]['up_amount'] = 0;
				$rows[$key]['down_quantity'] = 0;
				$rows[$key]['down_amount'] = 0;

				$sql = "select quantity as up_quantity,amount as up_amount from t_project_detail where type=1 and project_id=" . $row['project_id'];
				$resUp = Utility::query($sql);
				if (!Utility::isEmpty($resUp)) {
					$rows[$key]['up_quantity'] = $resUp[0]['up_quantity'];
					$rows[$key]['up_amount'] = number_format($resUp[0]['up_amount'], 2);
				}

				$sql = "select quantity as down_quantity,amount as down_amount from t_project_detail where type=2 and project_id=" . $row['project_id'];
				$resDown = Utility::query($sql);
				if (!Utility::isEmpty($resDown)) {
					$rows[$key]['down_quantity'] = $resDown[0]['down_quantity'];
					$rows[$key]['down_amount'] = number_format($resDown[0]['down_amount'], 2);
				}
			}
			return $rows;
		}
	}
}