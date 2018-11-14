<?php

/**
 * Created by vector.
 * DateTime: 2018/6/27 16:23
 * Describe：区块链接口服务
 */
class BlockChainService
{
    /**
     * 调用接口命令
     * @param $params
     * @return mixed
     */
    public static function cmd($params)
    {
        $url = Mod::app()->params->block_interface_url;
        return Utility::cmd($params, $url);
    }

    /**
     * [getContractInfo 获取合同信息]
     * @param
     * @param  [bigint] $contractId [合同id]
     * @return [array]
     */
    public static function getContractInfo($contractId)
    {
        $infoArr = array();
        if(empty($contractId))
            return $infoArr;
        $contract = Contract::model()->with('goods','extra','payments')->findByPk($contractId);
        if(empty($contract->contract_id))
            return $infoArr;

        $infoArr['id']            = $contract->contract_id;
        $infoArr['relationId']    = $contract->relation_contract_id;
        $infoArr['code']          = !empty($contract->contract_code) ? $contract->contract_code : '';
        $infoArr['codeOut']       = !empty($contract->code_out) ? $contract->code_out : '';
        $infoArr['projectId']     = $contract->project_id;
        $infoArr['corporationId'] = $contract->corporation_id;
        $infoArr['type']          = $contract->type;
        $infoArr['category']      = $contract->category;
        $infoArr['partnerId']     = $contract->partner_id;
        $infoArr['contractDate']  = !empty($contract->contract_date) ? $contract->contract_date : '';
        $infoArr['payMethod']     = !empty($contract->pay_method) ? $contract->pay_method : '';
        $infoArr['payRemark']     = !empty($contract->pay_remark) ? $contract->pay_remark : '';
        $infoArr['deliveryTerm']  = !empty($contract->delivery_term) ? $contract->delivery_term : '';
        $infoArr['days']          = $contract->days;
        $infoArr['currency']      = $contract->currency;
        $infoArr['exchangeRate']  = $contract->exchange_rate;
        $infoArr['amount']        = $contract->amount;
        $infoArr['amountCny']     = $contract->amount_cny;
        $infoArr['priceType']     = $contract->price_type;
        $infoArr['formula']       = $contract->formula;
        $infoArr['managerUserId'] = $contract->manager_user_id;
        $infoArr['contractItems'] = $contract->extra->content;

        $contractGoods = $contract->modelsToArray($contract->goods);
        $payments      = $contract->modelsToArray($contract->payments);
        $infoArr['goodsItems']    = json_encode($contractGoods);
        $infoArr['paymentPlans']  = json_encode($payments);

        
        return $infoArr;
    }

    /**
     * 区块链返回数据结构
     * {
            "code":"0",
            "success":true,
            "msg":"操作成功",
            "data":{
                "blockHash":"0x38233sdfasfas423",
                "txHash":"0x131231fjdasdaf"
            }
        }
     * 合同信息上链
     * @param $array
     *
     * @return bool
     */
    public static function contractBlock($contractId)
    {
        $params = self::getContractInfo($contractId);
        if(!empty($params)){
           $result = self::cmd($params);
           if($result['code']==0){
            Contract::model()->updateByPk($contractId, array('block_hash'=>$result['data']['blockHash'],'tx_hash'=>$result['data']['txHash']));
           }
        }

        return true;
    }

    
}