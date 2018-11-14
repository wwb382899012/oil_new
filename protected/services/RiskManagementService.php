<?php 
/**
*	业务审核
*/
class RiskManagementService {
	static public function riskManagementStart($contract_id) {
		$contract = Contract::model()->findByPk($contract_id);
		if(!empty($contract) && $contract->is_main) {
			// $contracts = Contract::model()->find(array(
			// 	'condition'=>'project_id=:project_id and is_main = 1 and contract_id <> :contract_id', 
			// 	'params'=>array(
			// 		'project_id'=>$contract->project_id, 
			// 		'contract_id'=>$contract->contract_id)
			// 	)
			// );
			// array_push($contracts, $contract);
			Contract::model()->updateAll(array(
				'status'=>Contract::STATUS_SUBMIT,
				'update_time'=>new CDbExpression('now()'),
				'update_user_id'=>Utility::getNowUserId()
			), "project_id=:project_id and is_main=1", array(
				'project_id'=>$contract->project_id
			));
		} else if(!empty($contract) && !$contract->is_main) {
			$contract->status = Contract::STATUS_SUBMIT;
			$contract->update_time = new CDbExpression('now()');
			$contract->update_user_id = Utility::getNowUserId();
			$contract->save();
		}
	}

	static public function doneRiskManagement($contract_id) {
		$contract = Contract::model()->findByPk($contract_id);
		if(!empty($contract) && $contract->is_main) {
			// $contracts = Contract::model()->find(array(
			// 	'condition'=>'project_id=:project_id and is_main = 1 and contract_id <> :contract_id', 
			// 	'params'=>array(
			// 		'project_id'=>$contract->project_id, 
			// 		'contract_id'=>$contract->contract_id)
			// 	)
			// );
			// array_push($contracts, $contract);
			Contract::model()->updateAll(array(
				'status'=>Contract::STATUS_RISK_CHECKED,
				'update_time'=>new CDbExpression('now()'),
				'update_user_id'=>Utility::getNowUserId()
			), "project_id=:project_id and is_main=1", array(
				'project_id'=>$contract->project_id
			));
		} else if(!empty($contract) && !$contract->is_main) {
			$contract->status = Contract::STATUS_RISK_CHECKED;
			$contract->update_time = new CDbExpression('now()');
			$contract->update_user_id = Utility::getNowUserId();
			$contract->save();
		}
	}

	static public function rollbackRiskManagement($contract_id) {
		$contract = Contract::model()->findByPk($contract_id);
		if(!empty($contract) && $contract->is_main) {
			// $contracts = Contract::model()->find(array(
			// 	'condition'=>'project_id=:project_id and is_main = 1 and contract_id <> :contract_id', 
			// 	'params'=>array(
			// 		'project_id'=>$contract->project_id, 
			// 		'contract_id'=>$contract->contract_id)
			// 	)
			// );
			// array_push($contracts, $contract);
			Contract::model()->updateAll(array(
				'status'=>Contract::STATUS_BACK,
				'update_time'=>new CDbExpression('now()'),
				'update_user_id'=>Utility::getNowUserId()
			), "project_id=:project_id and is_main=1", array(
				'project_id'=>$contract->project_id
			));
		} else if(!empty($contract) && !$contract->is_main) {
			$contract->status = Contract::STATUS_BACK;
			$contract->update_time = new CDbExpression('now()');
			$contract->update_user_id = Utility::getNowUserId();
			$contract->save();
		}
	}

	public static function getContractCheckItem($contract_id, $businessId) {
		$checkItem = CheckItem::model()->find(
			array(
				"condition"=>"business_id=:business_id and obj_id=:obj_id", 
				"params"=>array("obj_id"=>$contract_id, "business_id"=>$businessId)));
		return $checkItem->check_id;
	}

	public static function getProjectCheckItem($project_id, $businessId) {
        $sql = "select check_id from t_check_item ci where ci.obj_id in (select contract_id from t_contract where t_contract.project_id = '{$project_id}' and is_main=1)
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