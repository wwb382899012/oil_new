<?php

/**
 * @author  vector
 * @date    2018-06-11
 * @desc    收付款占用利息服务类
 */
class InterestReportService
{
	/**
	 * [addInterestInfo 添加利息信息]
	 * @param
	 */
	public static function addInterestInfo()
	{
		/*$sql = "insert into t_payment_interest(contract_id,contract_code,project_id,project_code,
				corporation_id,corporation_name,user_id,user_name,contract_type,check_pass_time,amount_sign,create_time)
				select * from (select c.contract_id,c.contract_code,p.project_id,p.project_code,
				co.corporation_id,co.name as corporation_name,u.user_id,u.name as user_name,c.type,d.update_time,c.amount_cny,now() as create_time
				from t_contract c 
				left join t_project p on c.project_id=p.project_id
				left join t_corporation co on c.corporation_id=co.corporation_id
				left join t_system_user u on p.manager_user_id=u.user_id
				left join t_check_detail d on c.contract_id=d.obj_id
				where d.business_id=".FlowService::BUSINESS_BUSINESS_CHECK." and d.check_status=1) as temp
				where not EXISTS (select a.contract_id from t_payment_interest a where a.contract_id=temp.contract_id)";// c.corporation_id in (10, 12) and */
		$typeArr     = array_merge(ConstantMap::$channel_buy_project_type, ConstantMap::$warehouse_receive_project_type);
		$projectType = implode(',', $typeArr);
		$sql = "insert into t_payment_interest(contract_id,contract_code,project_id,project_code,
				corporation_id,corporation_name,user_id,user_name,contract_type,amount_sign,create_time,check_pass_time)
				select * from (select c.contract_id,c.contract_code,p.project_id,p.project_code,
				co.corporation_id,co.name as corporation_name,u.user_id,u.name as user_name,c.type,c.amount_cny,now() as create_time,
				(select d.update_time from t_check_detail d where d.business_id=".FlowService::BUSINESS_BUSINESS_CHECK." and d.check_status=1 and 
				case when c.is_main=".ConstantMap::CONTRACT_MAIN." and c.type=".ConstantMap::SALE_TYPE." and p.type in(".$projectType.") then c.contract_id-1=d.obj_id else c.contract_id=obj_id end ) as update_time
				from t_contract c 
				left join t_project p on c.project_id=p.project_id
				left join t_corporation co on c.corporation_id=co.corporation_id
				left join t_system_user u on p.manager_user_id=u.user_id
				where c.status>=".Contract::STATUS_BUSINESS_CHECKED.") as temp
				where not EXISTS (select a.contract_id from t_payment_interest a where a.contract_id=temp.contract_id)";

		Utility::executeSql($sql);
	}

	/**
	 * [addDayInterest 添加每日计息记录]
	 * @param
	 */
	public static function addDayInterest($projectId="")
	{
		$sql     = "select * from t_payment_interest "; //where contract_type=".ConstantMap::BUY_TYPE." and (stop_date is null or (stop_date>=date_format(now(), '%Y-%m-%d') and status<".PaymentInterest::STATUS_PASS."))
		if(!empty($projectId))
			$sql .= " where project_id=".$projectId;
		$infoArr = Utility::query($sql);

		$obj = new InterestReportService();
		
		$inArr = array();
		$amountInfo = array();
		$changeArr  = array();
		$detailArr  = array();

		$prefixSql   = "INSERT INTO t_payment_interest_change(contract_id,amount_goods,amount_actual,days,interest,create_time,update_time) VALUES ";
		$suffixSql   = " ON DUPLICATE KEY UPDATE amount_goods = VALUES(amount_goods),amount_actual = VALUES(amount_actual),days = VALUES(days),interest = VALUES(interest),create_time = IFNULL(create_time,VALUES(create_time)),update_time = VALUES(update_time);";
		$truncateSql = "TRUNCATE TABLE t_payment_interest_detail;"; 
		$detailSql 	 = "INSERT INTO t_payment_interest_detail(contract_id,interest_date,amount_goods,amount_actual,amount_day,interest_day,create_time,update_time) VALUES ";
		//获取详细及总金额
		if(!empty($infoArr)){
			if(!empty($projectId)){
				foreach ($infoArr as $v) {
					$inArr[] = $v['contract_id'];
				}
				$truncateSql = "DELETE FROM t_payment_interest_detail WHERE contract_id in (".implode(',', $inArr).");";
			}
			
			foreach ($infoArr as $info) {
				$nowtime  = strtotime(date("Y-m-d"));
				$stoptime = !empty($info['stop_date']) ? strtotime($info['stop_date']) : 0;
				if($info['status']==PaymentInterest::STATUS_PASS && !empty($stoptime) && $stoptime<$nowtime)
					PaymentInterest::model()->updateByPk($info['id'], array('status'=>PaymentInterest::STATUS_DONE, 'update_time'=>new CDbExpression('now()')));

				$payArr     = $obj->getActualPayInfo($info['contract_id']);
				$multiArr 	= $obj->getMultiContractActualPayInfo($info['contract_id']);
				$claimArr   = $obj->getClaimInfo($info['contract_id']);
				$receiveArr = $obj->getReceiveInfo($info['contract_id']);
				
				$combineArr = array_merge($payArr,$multiArr,$claimArr);
				$combineArr = $obj->getDateUniqueInfo($combineArr);
				$receiveArr = $obj->getDateUniqueInfo($receiveArr);


				$goodsArr = array();
				$goodsInfo= array();
				if($info['contract_type'] == ConstantMap::BUY_TYPE){
					$goodsInfo = $obj->getContractOfInQuantity($info['contract_id']);
				}else{
					$goodsInfo = $obj->getContractOfOutQuantity($info['contract_id']);
				}

				$goodsArr   = $obj->getDateUniqueInfo($goodsInfo);
				
				$amountInfo = $obj->getAmountInfo($combineArr, $receiveArr, $goodsArr, $info['contract_type']);

				$stoptime      = 0;
				$totalDay      = 0;
				$totalInterest = 0;

				if(!empty($amountInfo['date_detail'])){
					if(!empty($info['stop_date'])){
						$stoptime = strtotime($info['stop_date']);
						$nowtime  = $stoptime>=$nowtime ? $nowtime : $stoptime;
					}

					$dateKeys = array_keys($amountInfo['date_detail']); 
					$begintime= strtotime($dateKeys[0]);

					if(!empty($stoptime) && $stoptime < $begintime){
						unset($amountInfo['date_detail']);
						$amountInfo['date_detail'][$info['stop_date']]['interest_day']      = 0;
						$amountInfo['date_detail'][$info['stop_date']]['amount_day']        = 0;
						$amountInfo['date_detail'][$info['stop_date']]['amount_cumulative'] = 0;
						$amountInfo['date_detail'][$info['stop_date']]['goods_amount']      = 0;
					}else{
						// $nowtime =  (empty($stoptime) || $stoptime>=$nowtime) ? $nowtime : $stoptime;
						$totalDay= ($nowtime - $begintime)/ 86400 + 1;
						foreach ($amountInfo['date_detail'] as $date=>$detail) {
							$d = strtotime($date);
							if($d <= $nowtime){
								$totalInterest += $detail['interest_day'];
							}else{
								unset($amountInfo['date_detail'][$date]);
							}
						}
					}

					foreach ($amountInfo['date_detail'] as $date=>$v) {
						$v['goods_amount'] = !empty($v['goods_amount']) ? $v['goods_amount'] : 0;
						$detailArr[] = "(".$info['contract_id'].",\"".$date."\",".$v['goods_amount'].",".$v['amount_cumulative'].",".$v['amount_day'].",".$v['interest_day'].",now(),now())";
					}
				}

				$amountInfo['total_amount']['days']      = $totalDay;
				$amountInfo['total_amount']['interest']  = $totalInterest;

				if(!empty($amountInfo['total_amount'])){
					$totalAmount = $amountInfo['total_amount'];
					$changeArr[] = "(".$info['contract_id'].",".$totalAmount['goods_amount'].",".$totalAmount['actual_amount'].",".$totalAmount['days'].",".$totalAmount['interest'].",now(),now())";
				}
			}
		}

		$sqlArr = array();
		if(!empty($changeArr)){
			$changeArr = array_chunk($changeArr, 1000);
			foreach ($changeArr as $v) {
				$sqlArr[] = $prefixSql.implode(",", $v).$suffixSql;
				// Utility::execute($sql);
			}
		}

		if(!empty($detailArr)){
			$detailArr = array_chunk($detailArr, 1000);
			foreach ($detailArr as $k=>$v) {
				$prefix = $k==0 ? $truncateSql : "";
				$sqlArr[] = $prefix.$detailSql.implode(",", $v).";";
				// Utility::execute($sql);
			}
		}



		if(!empty($sqlArr))
			Utility::executeSql($sqlArr);
	}

	/**
	 * [getMultiContractActualPayInfo 获取多合同下付款实付金额]
	 * @param
	 * @param  [bigint] $contractId [合同id]
	 * @return [array]
	 */
	public static function getMultiContractActualPayInfo($contractId)
	{
		$infoArr = array();
		if(empty($contractId))
	 		return $infoArr;

	 	$idArr = array();
	 	$sql = "select a.apply_id,a.subject_id,a.amount as apply_amount,a.amount_paid,d.detail_id,d.amount as detail_amount,
	 			p.amount,p.amount_cny,p.pay_date as interest_date,p.exchange_rate
	 			from t_pay_application_detail d 
	 			left join t_pay_application a on a.apply_id=d.apply_id
	 			left join t_payment p on a.apply_id=p.apply_id
	 			where p.status=".Payment::STATUS_SUBMITED." and d.contract_id=".$contractId." and a.type=".PayApplication::TYPE_MULTI_CONTRACT." and a.category=".PayApplication::CATEGORY_NORMAL." order by p.pay_date asc";

	 	$data = Utility::query($sql);
	 	if(Utility::isNotEmpty($data)){
	 		foreach ($data as $key => $val) {
	 			if($val['detail_amount'] < $val['apply_amount']){
	 				if(in_array($val['apply_id'], $idArr))
	 					continue;
	 				$idArr[] = $val['apply_id'];

	 				$sql = "select sum(amount) as total_amount from t_pay_application_detail where apply_id=".$val['apply_id']." and detail_id<".$val['detail_id'];
	 				$detailArr = Utility::query($sql);
	 				if(Utility::isNotEmpty($detailArr)){
						$total_amount  = 0;
						$last_amount   = 0;
						$detail_amount = $detailArr[0]['total_amount'];
						$tArr          = array();
	 					if($detail_amount < $val['amount_paid']){
	 						$sql 	= "select amount,amount_cny,pay_date as interest_date,exchange_rate from t_payment where apply_id=".$val['apply_id']." and status=".Payment::STATUS_SUBMITED." order by pay_date asc ";
	 						$pArr 	= Utility::query($sql);
	 						if(Utility::isNotEmpty($pArr)){
	 							foreach ($pArr as $k=>$p) {
									$total_amount += $p['amount'];
									$diff_amount   = $total_amount - $detail_amount;
	 								if($diff_amount > 0 && $diff_amount <= $val['detail_amount']){
										$tArr[$k]               = $p;
										$tArr[$k]['amount_cny'] = round(($diff_amount - $last_amount) * $p['exchange_rate']);
										$tArr[$k]['subject_id'] = $val['subject_id'];
										$infoArr[]              = $tArr[$k];
	 								}else if($diff_amount > $val['detail_amount'] && $last_amount < $val['detail_amount']){
										$tArr[$k]               = $p;
										$tArr[$k]['amount_cny'] = round(($val['detail_amount'] - $last_amount) * $p['exchange_rate']);
										$tArr[$k]['subject_id'] = $val['subject_id'];
										$infoArr[]              = $tArr[$k];
	 								}else if($last_amount >= $val['detail_amount']){
	 									break;
	 								}

									if($diff_amount > 0)
	 									$last_amount = $total_amount - $detail_amount;
	 							}
	 						}
	 					}
	 				}
	 			}else{
	 				$tArr[$key]['subject_id']    = $val['subject_id'];
					$tArr[$key]['amount']        = $val['amount'];
					$tArr[$key]['amount_cny']    = $val['amount_cny'];
					$tArr[$key]['interest_day']  = $val['interest_day'];
					$tArr[$key]['exchange_rate'] = $val['exchange_rate'];
					$infoArr[]                   = $tArr[$key];
	 			}
	 		}
	 	}

	 	return $infoArr;
	}

	/**
	 * [getActualPayInfo 非多合同下付款实付信息]
	 * @param
	 * @param  [biginy] $contractId [合同id]
	 * @param  [int] $category   [合同类型]
	 * @return [array]
	 */
	public static function getActualPayInfo($contractId, $category=PayApplication::CATEGORY_NORMAL)
	{
		$data = array();
		if(empty($contractId))
	 		return $data;

	 	$sql  = "select a.subject_id,p.amount,p.amount_cny,p.pay_date as interest_date,p.exchange_rate from t_payment p 
	 			left join t_pay_application a on a.apply_id=p.apply_id
	 			where a.contract_id=".$contractId." and p.status=".Payment::STATUS_SUBMITED.
	 			// " and p.pay_date<=date_format(now(),'%Y-%m-%d')".
	 			" and a.category=".$category." and a.type!=".PayApplication::TYPE_MULTI_CONTRACT." order by p.pay_date asc";

		$data = Utility::query($sql);

		return $data;
	}

	/**
	 * [getClaimAmount 获取后补认领信息]
	 * @param
	 * @param  [bigint] $contractId [合同id]
	 * @return [int]
	 */
	public static function getClaimInfo($contractId)
	{
		$infoArr = array();
		if(empty($contractId))
			return $infoArr;

		$sql  = "select apply_id,subject_id,amount as claim_amount from t_pay_claim where status>=".PayClaim::STATUS_SUBMITED." and contract_id=".$contractId;
		$data = Utility::query($sql);
		$claimArr = array();
		if(!empty($data)){
			foreach ($data as $v) {
				$claimArr[$v['apply_id']]['claim_amount'] += $v['claim_amount'];
				$claimArr[$v['apply_id']]['subject_id']    = $v['subject_id'];
			}
		}

		$payArr = array();
		if(!empty($claimArr)){
			foreach ($claimArr as $applyId=>$claim) {
				$claim_amount = $claim['claim_amount'];
				$subject_id   = $claim['subject_id'];
				$sql  = "select amount,amount_cny,pay_date as interest_date,exchange_rate from t_payment where status=".Payment::STATUS_SUBMITED.
						" and apply_id=".$applyId." order by pay_date asc"; // and pay_date<=date_format(now(),'%Y-%m-%d') 
				$data = Utility::query($sql);
				if(!empty($data)){
					foreach ($data as $k=>$v) {
						if($claim_amount>=$v['amount']){
							$payArr[$k] = $v;
						}else{
							$payArr[$k] = $v;
							$payArr[$k]['amount_cny'] = round($claim_amount * $v['exchange_rate']) ;
						}
						$payArr[$k]['subject_id'] = $subject_id;
						$amount       = $claim_amount - $v['amount'];
						$claim_amount = $amount>0 ? $amount : 0;
						$infoArr[]    = $payArr[$k];
					}
				}
						
			}
		}

		return $infoArr;

	} 


	/**
	 * [getReceiveInfo 获取银行流水认领信息]
	 * @param
	 * @param  [bigint] $contractId [合同id]
	 * @return [array]
	 */
	private function getReceiveInfo($contractId)
	{
		$data = array();
		if(empty($contractId))
			return $data;

		$sql = 	"select amount,amount_cny,receive_date as interest_date,exchange_rate from t_receive_confirm where status>=".ReceiveConfirm::STATUS_SUBMITED.
				" and contract_id=".$contractId." order by receive_date asc"; // and receive_date<=date_format(now(),'%Y-%m-%d') 
		$data = Utility::query($sql);

		return $data;
	}

	/**
	 * [getDateUniqueInfo 获取日期唯一的金额信息]
	 * @param
	 * @param  [array] $infoArr [信息数组]
	 * @return [array]
	 */
	private function getDateUniqueInfo($infoArr)
	{
		$amountArr = array();
		if(empty($infoArr))
			return $amountArr;

		foreach ($infoArr as $info) {
			// $amountArr[$info['interest_date']]['amount']     += $info['amount'];
			$amountArr[$info['interest_date']]['amount_cny']   += $info['amount_cny'];
			$amountArr[$info['interest_date']]['interest_date'] = $info['interest_date'];
			// $amountArr[$info['interest_date']]['exchange_rate'] = $info['exchange_rate'];
		}

		return $amountArr;
	}

	/**
	 * [getAmountInfo 获取从远到近每日实付金额数组]
	 * @param
	 * @param  [array] $payArr     [实际付款数组]
	 * @param  [array] $receiveArr [实际收款数组]
	 * @param  [array] $goodsArr   [货值数组]
	 * @param  [int]   $type       [合同类型]
	 * @return [array]
	 */
	private function getAmountInfo($payArr, $receiveArr, $goodsArr, $type=ConstantMap::BUY_TYPE)
	{
		$amountInfo   = array();
		$actualAmount = 0;
		$goodsAmount  = 0;
		if(empty($payArr) && empty($receiveArr)){
			$amountInfo['date_detail'] = array();
			if(!empty($goodsArr)){
				foreach ($goodsArr as $goods) {
					$goodsAmount += $goods['amount_cny'];
				}
			}
			$amountInfo['total_amount']['goods_amount']  = $goodsAmount;
			$amountInfo['total_amount']['actual_amount'] = $actualAmount;
			return $amountInfo;
		}


		$amountArr  = array();
		if($type == ConstantMap::BUY_TYPE){
			$pay_type     = 1;
			$receive_type = -1;
		}else{
			$pay_type     = -1;
			$receive_type = 1;
		}

		if(!empty($receiveArr)){
			foreach ($receiveArr as $date=>$receive) {
				// $amountArr[$date]['interest_date'] = $date;
				if(!empty($payArr[$date])){
					$amountArr[$date]['amount_day']   = $pay_type * $payArr[$date]['amount_cny'] + $receive_type * $receive['amount_cny'];
					unset($payArr[$date]);
				}else{
					$amountArr[$date]['amount_day']   = $receive_type * $receive['amount_cny'];
				}
			}
		}

		if(!empty($payArr)){
			foreach ($payArr as $date=>$pay) {
				$amountArr[$date]['amount_day']   = $pay_type * $pay['amount_cny'];
			}
		}

		
		if(!empty($amountArr)){
			ksort($amountArr);

			foreach ($amountArr as $date=>$info) {
				$actualAmount += $info['amount_day'];
				$amountArr[$date]['amount_cumulative'] = $actualAmount;
				$amountArr[$date]['interest_day']      = round(($actualAmount * 12.6) / 36500);
			}

			$obj = new InterestReportService();

			$amountKeys = array_keys($amountArr);
			$startDate  = $amountKeys[0];
			$endDate    = $amountKeys[count($amountKeys)-1];
			$dateKeys 	= $obj->getContinueDate($startDate, $endDate);

			// $begintime	= strtotime($startDate);
			if(!empty($dateKeys)){
				// $endtime = strtotime($dateKeys[count($dateKeys)-1]); 
				foreach ($dateKeys as $date) {
					if(!array_key_exists($date, $amountArr)){
						$d = strtotime($date);
						if($d > strtotime($endDate)){
							$amountArr[$date] = $amountArr[$endDate];
						}else{
							foreach ($amountKeys as $k=>$v) {
								$s1 = strtotime($v);
								$s2 = strtotime($amountKeys[$k+1]);
								if($d>$s1 && $d<$s2){
									$amountArr[$date] = $amountArr[$v];
								}
							}
						}
						$amountArr[$date]['amount_day'] = 0;
					}
				}
			}

			ksort($amountArr);

			if(!empty($goodsArr)){
				ksort($goodsArr);
				foreach ($goodsArr as $date=>$goods) {
					$goodsAmount += $goods['amount_cny'];
					if(array_key_exists($date, $amountArr)){
						$amountArr[$date]['goods_amount'] = $goodsAmount;
					}
				}

				//从付款开始到截止日期的每天累计货值
				foreach ($amountArr as $date=>$amount) {
					if($date != $amountKeys[0] && empty($amountArr[$date]['goods_amount'])){
						$destime = strtotime($date) - 86400;
						$destdate= date('Y-m-d', $destime); 
						$amountArr[$date]['goods_amount'] = $amountArr[$destdate]['goods_amount'];
					}
				}
			}
		}

		$amountInfo['date_detail'] = $amountArr;
		$amountInfo['total_amount']['goods_amount']  = $goodsAmount;
		$amountInfo['total_amount']['actual_amount'] = $actualAmount;

		return $amountInfo;
	}


	/**
	 * [getContinueDate 获取连续的日期数组]
	 * @param
	 * @param  [date] $startDate [开始日期]
	 * @param  [date] $endDate   [结束日期]
	 * @return [array]
	 */
	private function getContinueDate($startDate, $endDate)
	{
		$dateArr = array();
		if(empty($startDate) || empty($endDate))
			return $dateArr;

		$begintime = strtotime($startDate);
		$endtime   = strtotime($endDate);
		$nowtime   = strtotime(date("Y-m-d"));
		$endtime   = $endtime>$nowtime ? $endtime : $nowtime; 
		if($begintime < $endtime){
			for ($start = $begintime; $start <= $endtime; $start += 86400) {
    			$dateArr[] = date("Y-m-d", $start);
			}
		}

		return $dateArr;
	}

	/**
	 * [getContractOfInQuantity 获取合同下所有的入库数量]
	 * @param
	 * @param  [bigint] $contractId [合同id]
	 * @return [array]
	 */
	private function getContractOfInQuantity($contractId)
	{
		$inArr = array();
		if(empty($contractId))
			return $inArr;
		$sql = "select i.stock_in_id,c.contract_id,c.exchange_rate,c.currency,
				g.goods_id,g.price,g.unit as contract_unit,g.unit_convert_rate,
				i.entry_date as interest_date,d.quantity ,d.unit,s.quantity as quantity_sub,
				s.unit as unit_sub
				from t_stock_in_detail d
				left join t_stock_in_detail_sub s on d.stock_id=s.stock_id
				left join t_stock_in i on i.stock_in_id=d.stock_in_id
				left join t_contract c on c.contract_id=i.contract_id 
				left join t_contract_goods g on c.contract_id=g.contract_id and d.goods_id=g.goods_id
				where c.contract_id=".$contractId." and i.status>=".StockIn::STATUS_PASS;
		$stockArr = Utility::query($sql);
		if(!empty($stockArr)){
			foreach ($stockArr as $key=>$stock) {
				$inArr[$key] = $stock;
				$inArr[$key]['price_cny'] = round($stock['price'] * $stock['exchange_rate']);
				if($stock['contract_unit'] == $stock['unit'])
					$quantity = $stock['quantity'];
				else
					$quantity = $stock['quantity_sub'];
				$inArr[$key]['amount_cny'] = round($inArr[$key]['price_cny'] * $quantity);
			}
		}

		return $inArr;
	}

	/**
	 * [getContractOfOutQuantity 获取合同下所有的出库数量]
	 * @param
	 * @param  [bigint] $contractId [合同id]
	 * @return [array]
	 */
	private function getContractOfOutQuantity($contractId)
	{
		$outArr = array();
		if(empty($contractId))
			return $outArr;

		$sql = "select d.out_id,c.contract_id,c.currency,c.exchange_rate,
				g.price,g.unit,g.goods_id,o.out_date as interest_date,d.quantity
				from t_stock_out_detail d 
				left join t_stock_out_order o on d.out_order_id=o.out_order_id
				left join t_contract c on c.contract_id=d.contract_id
				left join t_contract_goods g on c.contract_id=g.contract_id and g.goods_id=d.goods_id
				where c.contract_id=".$contractId." and (o.status=".StockOutOrder::STATUS_SUBMITED." or o.status=".StockOutOrder::STATUS_SETTLED.")";
		$stockArr = Utility::query($sql);
		if(!empty($stockArr)){
			foreach ($stockArr as $key=>$stock) {
				$outArr[$key] = $stock;
				$outArr[$key]['price_cny']  = round($stock['price'] * $stock['exchange_rate']);
				$outArr[$key]['amount_cny'] = round($outArr[$key]['price_cny'] * $stock['quantity']);
			}
		}

		return $outArr;
	}

	/**
	 * [addDayInterestByContractId 按照合同生成利息明细]
	 * @param
	 * @param [bigint] $contractId [合同id]
	 */
	public static function addDayInterestByContractId($contractId)
	{
		$sql     = "select * from t_payment_interest WHERE contract_id=".$contractId; 
		$infoArr = Utility::query($sql);

		$obj = new InterestReportService();
		

		$amountInfo = array();
		$changeArr  = array();
		$detailArr  = array();

		$prefixSql   = "INSERT INTO t_payment_interest_change(contract_id,amount_goods,amount_actual,days,interest,create_time,update_time) VALUES ";
		$suffixSql   = " ON DUPLICATE KEY UPDATE amount_goods = VALUES(amount_goods),amount_actual = VALUES(amount_actual),days = VALUES(days),interest = VALUES(interest),create_time = IFNULL(create_time,VALUES(create_time)),update_time = VALUES(update_time);";
		$truncateSql = "DELETE FROM t_payment_interest_detail WHERE contract_id=".$contractId.";"; 
		$detailSql 	 = "INSERT INTO t_payment_interest_detail(contract_id,interest_date,amount_goods,amount_actual,amount_day,interest_day,create_time,update_time) VALUES ";
		//获取详细及总金额
		if(!empty($infoArr)){
			foreach ($infoArr as $info) {
				$nowtime  = strtotime(date("Y-m-d"));
				$stoptime = !empty($info['stop_date']) ? strtotime($info['stop_date']) : 0;
				if($info['status']==PaymentInterest::STATUS_PASS && !empty($stoptime) && $stoptime<$nowtime)
					PaymentInterest::model()->updateByPk($info['id'], array('status'=>PaymentInterest::STATUS_DONE, 'update_time'=>new CDbExpression('now()')));

				$payArr     = $obj->getActualPayInfo($info['contract_id']);
				$multiArr 	= $obj->getMultiContractActualPayInfo($info['contract_id']);
				$claimArr   = $obj->getClaimInfo($info['contract_id']);
				$receiveArr = $obj->getReceiveInfo($info['contract_id']);
				
				$combineArr = array_merge($payArr,$multiArr,$claimArr);
				$combineArr = $obj->getDateUniqueInfo($combineArr);
				$receiveArr = $obj->getDateUniqueInfo($receiveArr);


				$goodsArr = array();
				$goodsInfo= array();
				if($info['contract_type'] == ConstantMap::BUY_TYPE){
					$goodsInfo = $obj->getContractOfInQuantity($info['contract_id']);
				}else{
					$goodsInfo = $obj->getContractOfOutQuantity($info['contract_id']);
				}

				$goodsArr   = $obj->getDateUniqueInfo($goodsInfo);
				
				$amountInfo = $obj->getAmountInfo($combineArr, $receiveArr, $goodsArr, $info['contract_type']);

				$stoptime      = 0;
				$totalDay      = 0;
				$totalInterest = 0;

				if(!empty($amountInfo['date_detail'])){
					if(!empty($info['stop_date'])){
						$stoptime = strtotime($info['stop_date']);
						$nowtime  = $stoptime>=$nowtime ? $nowtime : $stoptime;
					}

					$dateKeys = array_keys($amountInfo['date_detail']); 
					$begintime= strtotime($dateKeys[0]);

					if(!empty($stoptime) && $stoptime < $begintime){
						unset($amountInfo['date_detail']);
						$amountInfo['date_detail'][$info['stop_date']]['interest_day']      = 0;
						$amountInfo['date_detail'][$info['stop_date']]['amount_day']        = 0;
						$amountInfo['date_detail'][$info['stop_date']]['amount_cumulative'] = 0;
						$amountInfo['date_detail'][$info['stop_date']]['goods_amount']      = 0;
					}else{
						// $nowtime =  (empty($stoptime) || $stoptime>=$nowtime) ? $nowtime : $stoptime;
						$totalDay= ($nowtime - $begintime)/ 86400 + 1;
						foreach ($amountInfo['date_detail'] as $date=>$detail) {
							$d = strtotime($date);
							if($d <= $nowtime){
								$totalInterest += $detail['interest_day'];
							}else{
								unset($amountInfo['date_detail'][$date]);
							}
						}
					}

					foreach ($amountInfo['date_detail'] as $date=>$v) {
						$v['goods_amount'] = !empty($v['goods_amount']) ? $v['goods_amount'] : 0;
						$detailArr[] = "(".$info['contract_id'].",\"".$date."\",".$v['goods_amount'].",".$v['amount_cumulative'].",".$v['amount_day'].",".$v['interest_day'].",now(),now())";
					}
				}

				$amountInfo['total_amount']['days']      = $totalDay;
				$amountInfo['total_amount']['interest']  = $totalInterest;

				if(!empty($amountInfo['total_amount'])){
					$totalAmount = $amountInfo['total_amount'];
					$changeArr[] = "(".$info['contract_id'].",".$totalAmount['goods_amount'].",".$totalAmount['actual_amount'].",".$totalAmount['days'].",".$totalAmount['interest'].",now(),now())";
				}
			}
		}

		$sqlArr = array();
		if(!empty($changeArr)){
			$changeArr = array_chunk($changeArr, 1000);
			foreach ($changeArr as $v) {
				$sqlArr[] = $prefixSql.implode(",", $v).$suffixSql;
				// Utility::execute($sql);
			}
		}

		if(!empty($detailArr)){
			$detailArr = array_chunk($detailArr, 1000);
			foreach ($detailArr as $k=>$v) {
				$prefix = $k==0 ? $truncateSql : "";
				$sqlArr[] = $prefix.$detailSql.implode(",", $v).";";
				// Utility::execute($sql);
			}
		}



		if(!empty($sqlArr))
			Utility::execute($sqlArr);
	}

}