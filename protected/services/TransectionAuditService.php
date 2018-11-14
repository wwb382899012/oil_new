<?php 
/**
*	业务审核
*/
class TransectionAuditService {
	static public function beginTransectionAudit($contract_id) {
		$contract = Contract::model()->findByPk($contract_id);
		if(!empty($contract) && $contract->is_main) {
			Contract::model()->updateAll(array(
				'status'=>Contract::STATUS_CREDIT_CONFIRMED,
				'update_time'=>new CDbExpression('now()'),
				'update_user_id'=>Utility::getNowUserId()
			), "project_id=:project_id and is_main=1", array(
				'project_id'=>$contract->project_id
			));
		} else if(!empty($contract) && !$contract->is_main) {
			$contract->status = Contract::STATUS_CREDIT_CONFIRMED;
			$contract->update_time = new CDbExpression('now()');
			$contract->update_user_id = Utility::getNowUserId();
			$contract->save();
		}
	}

	static public function doneTransectionAduit($contract_id) {
		$contract = Contract::model()->findByPk($contract_id);
		if(!empty($contract) && $contract->is_main) {
			$contracts = Contract::model()->findAll(array(
				'condition'=>'project_id=:project_id and is_main = 1', 
				'params'=>array(
					'project_id'=>$contract->project_id)
				)
			);
			foreach($contracts as $contract) {
				$contract->status = Contract::STATUS_BUSINESS_CHECKED;
				$contract->update_time = new CDbExpression('now()');
				$contract->update_user_id = Utility::getNowUserId();
				$contract->save();
				ContractService::generateContractCode($contract->contract_id);
			}
		} else if(!empty($contract) && !$contract->is_main) {
			$contract->status = Contract::STATUS_BUSINESS_CHECKED;
			$contract->update_time = new CDbExpression('now()');
			$contract->update_user_id = Utility::getNowUserId();
			$contract->save();
			ContractService::generateContractCode($contract->contract_id);
		}
	}

	static public function rollbackTransectionAduit($contract_id) {
		$contract = Contract::model()->findByPk($contract_id);
		if(!empty($contract) && $contract->is_main) {
			Contract::model()->updateAll(array(
				'status'=>Contract::STATUS_BACK,
				'update_time'=>new CDbExpression('now()'),
				'update_user_id'=>Utility::getNowUserId()
			), "project_id=:project_id and is_main=1", array(
				'project_id'=>$contract->project_id
			));
			// 把所有的额度数据废弃
			ProjectCreditDetail::model()->updateAll(array(
				'status'=>ProjectCreditDetail::STATUS_DELETE,
				'update_time'=>new CDbExpression('now()'),
				'update_user_id'=>Utility::getNowUserId()
			), "project_id=:project_id", array(
				'project_id'=>$contract->project_id
			));
		} else if(!empty($contract) && !$contract->is_main) {
			$contract->status = Contract::STATUS_BACK;
			$contract->update_time = new CDbExpression('now()');
			$contract->update_user_id = Utility::getNowUserId();
			$contract->save();
			ProjectCreditDetail::model()->updateAll(array(
				'status'=>ProjectCreditDetail::STATUS_DELETE,
				'update_time'=>new CDbExpression('now()'),
				'update_user_id'=>Utility::getNowUserId()
			), "contract_id=:contract_id", array(
				'contract_id'=>$contract->contract_id
			));
		}
	}

	public static function getContractCheckItem($contract_id, $businessId) {
		$checkItem = CheckItem::model()->find(
			array(
				"condition"=>"business_id=:business_id and obj_id=:obj_id", 
				"params"=>array("obj_id"=>$contract_id, "business_id"=>$business_id)));
		return $checkItem->check_id;
	}

	public static function getProjectCheckItem($project_id, $businessId) {
        $sql = "select check_id from t_check_item ci where ci.obj_id in (select contract_id from t_contract where t_contract.project_id = '{$project_id}' and is_main = 1)
        ";
        $data = Utility::query($sql);
        if(!empty($data[0]) && isset($data[0]['check_id'])) {
        	return $data[0]['check_id'];
        }
        return null;
	}

	public static function getProjectCheckItemObj($project_id, $businessId) {
        $sql = "select obj_id from t_check_item ci where ci.obj_id in (select contract_id from t_contract where t_contract.project_id = '{$project_id}')
        ";
        $data = Utility::query($sql);
        if(!empty($data[0]) && isset($data[0]['obj_id'])) {
        	return $data[0]['obj_id'];
        }
        return null;

	}
}
?>