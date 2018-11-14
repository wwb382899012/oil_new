<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/8 10:08
 * Describe：
 */
class PartnerService
{
    /**
     * 获取可选的所有合作方
     * @return array
     */
    public static function getPartners()
    {
        $sql="select partner_id,name,code,bank_name from t_partner where status=".Partner::STATUS_PASS." order by partner_id desc";
        return Utility::query($sql);
    }

    /**
     * 获取可选的上游合作方
     * @return array
     */
    public static function getUpPartners()
    {
        $sql="select partner_id,name,code from t_partner where status=".Partner::STATUS_PASS." and FIND_IN_SET(1, type) order by partner_id desc";
        return Utility::query($sql);
    }

    /**
     * 获取可选的下游合作方
     * @return array
     */
    public static function getDownPartners()
    {
        $sql="select partner_id,name,code from t_partner where status=".Partner::STATUS_PASS." and FIND_IN_SET(2, type) order by partner_id desc";
        return Utility::query($sql);
    }

    /**
     * 获取可选的代理商
     * @return array
     */
    public static function getAgentPartners()
    {
        $sql="select partner_id,name,code from t_partner where status=".Partner::STATUS_PASS." and FIND_IN_SET(3, type) order by partner_id desc";
        return Utility::query($sql);
    }


    public static function updateApplyStatus($partnerId,$status,$oldStatus=null)
    {
        $obj=PartnerApply::model()->findByPk($partnerId);
        if(empty($obj->partner_id))
            return "当前合作方不存在！";
        if($oldStatus!==null && $obj->status!=$oldStatus)
        {
            return "当前合作方原状态与条件状态不一致！";
        }
        if($obj->status!=$status)
        {
            //$obj->old_status=$obj->status;
            $obj->status=$status;
            $obj->status_time= date("Y-m-d H:i:s");
            $obj->update_user_id=Utility::getNowUserId();
            $obj->update_time=date("Y-m-d H:i:s");
            $res=$obj->save(true,array("status","status_time","update_user_id","update_time"));
            if($res===true)
                return 1;
            else
                return $res;
        }
        else
            return 1;
    }

    /**
     * 根据企业关键字全名获取企业相关信息
     * 返回的结果分为以下几种：
     * 1：如果库中有，且数据为多条，返回多为数组
     * 2：如果库中有，且只有1条数据：
     *      2.1 如果数据在更新期内，直接返还只有一条记录的二维数组
     *      2.1 如果数据超过更新期（每60天更新合作方企业资料库），则调用getDetailPartners 方法，返回只有一条记录的二维数组
     * 3：如果库中没有，则调用getDetailPartners 方法
     *      3.1 如果有返回信息，则直接返回只有一条记录的二维数组
     *      3.2 如果没有，则调用getSimplePartners 方法，然后循环调用getDetailPartners 方法，返回多维数组
     * 4：如果以上都没有数据，则返回空数组
     *
     * @param $context
     * @return array
     */
    public static function getPartnersInfo($context)
    {
        if(empty($context))
            return array();
        $name   = trim($context);
        $apiKey = 'ae7b7645d0784635b734d39758ca168b';

        $result = array();
        $data   = array();

        $partners = PartnerInfo::model()->findAllToArray("name like '%".$name."%'");
        if(count($partners)>1){
            foreach ($partners as $key => $value) {
                unset($value['extra_info']);
                $data[] = $value;
            }
        }else if(count($partners)==1){
            $isUpdate = self::calcDays($partners[0]['update_time']);
            if($isUpdate){
                $result = self::getDetailPartners($partners[0]['name'],$apiKey);
                $data[0] = $result;
            }else{
                unset($partners[0]['extra_info']);
                $data = $partners;
            }
            /*if($partners[0]['is_full']==1){
                $isUpdate = self::calcDays($partners['create_time']);
                if($isUpdate){
                    $result = self::getDetailPartners($partners[0]['name'],$apiKey);
                    $data[] = $result;
                }else{
                    unset($partners[0]['extra_info']);
                    $data = $partners;
                }
                return $data;
            }else{
                $result = self::getDetailPartners($partners[0]['name'],$apiKey);
                unset($result['extra_info']);
                $data[] = $result;
                return $data;
            }*/
        }else{
            $result = self::getDetailPartners($name,$apiKey);
            if(is_array($result) && !empty($result)){
                $data[0] = $result;
            }else{
                $result = self::getSimplePartners($name,$apiKey);
                foreach ($result as $key => $value) {
                    $data[] = self::getDetailPartners(trim($value['Name']),$apiKey);
                }
            }
        }
        return $data;
    }

    /**
     * 根据企业关键字全名精确查找
     * @param $context
     * @return array
     */

    public static function getDetailPartners($context,$apiKey)
    {
        if(empty($context))
            return array();

        $url    = "http://i.yjapi.com/ECI/GetDetailsByName?key=$apiKey&keyword=$context";

        $curl = Mod::app()->curl;
        try{
            $res = $curl->get($url);
            // self::getChaBalance($apiKey);
            Mod::log("Alarm Log, params is ".$context.", and result is ".$res);
            $result = json_decode($res, true);
            if(!is_array($result['Result']) || empty($result['Result'])){
                return array();
            }
            self::setQichachaCallNum();
            if(!empty($result['Result']['EconKind'])){
                $owner = Ownership::model()->find("name='".trim($result['Result']['EconKind'])."' and status=1");
                if(empty($owner->id)){
                    $owner = new Ownership();
                    $owner->name   = trim($result['Result']['EconKind']);
                    $owner->status = 1;
                    $owner->create_user_id = 0;
                    $owner->create_time = date('Y-m-d H:i:s');
                }
                $owner->update_user_id = 0;
                $owner->update_time = date('Y-m-d H:i:s');
                $owner->save();
            }

            $run_status = self::getRunStatus(trim($result['Result']['Status']));

            if(!empty($result['Result']['Name'])){
                 $partner = PartnerInfo::model()->find("name='".$context."'");
                 if(empty($partner->id)){
                    $partner = new PartnerInfo();
                    $partner->name        = trim($result['Result']['Name']);
                    $partner->create_user_id = 0;
                    $partner->create_time = date('Y-m-d H:i:s');
                 }
                 $partner->code         = $result['Result']['KeyNo'];
                 // $partner->credit_code  = empty($result['Result']['CreditCode']) ? $result['Result']['No'] : $result['Result']['CreditCode'];
                 $partner->credit_code  = $result['Result']['CreditCode'];
                 $partner->registration_code        = $result['Result']['No'];
                 $partner->corporate    = $result['Result']['OperName'];
                 $partner->start_date   = date_format(date_create($result['Result']['StartDate']),'Y-m-d');
                 $partner->address      = $result['Result']['Address'];
                 $partner->registration_authority   = $result['Result']['BelongOrg'];
                 $partner->registered_capital       = $result['Result']['RegistCapi'];
                 $partner->business_scope           = $result['Result']['Scope'];
                 $partner->runs_state   = $run_status;
                 $partner->ownership    = !empty($result['Result']['EconKind']) ? $owner->id : 0;
                 $partner->extra_info   = json_encode($result['Result']);
                 // $partner->is_full      = 1;
                 $partner->update_user_id   = 0;
                 $partner->update_time      = date('Y-m-d H:i:s');
                 $partner->save();

                 $data = $partner->attributes;
                 unset($data['extra_info']);
                 return $data;
            }else{
                return array();
            }
        }
        catch(Exception $e)
        {
            Mod::log("Alarm error, params is ".$context.", and error message is ".$e->getMessage(),"error");
            return array("code"=>-1,"msg"=>$e->getMessage());
        }
    }

    /**
     * 根据企业关键字模糊查找
     * @param $context
     * @return array
     */
    public static function getSimplePartners($context,$apiKey)
    {
        if(empty($context))
            return array();

        $url    = "http://i.yjapi.com/ECISimple/Search?key=$apiKey&keyword=$context";

        $curl = Mod::app()->curl;
        try{
            $res = $curl->get($url);
            // Mod::log("Alarm Log, params is ".$context.", and result is ".$res);
            $result = json_decode($res, true);
            if(!is_array($result['Result']) || empty($result['Result'])){
                return array();
            }

            self::setQichachaCallNum();
            /*
            $info   = PartnerInfo::model()->findAllToArray("name like '%".$context."%'");
            $p      = array();
            if(Utility::isNotEmpty($info)){
                foreach ($info as $v)
                {
                    $p[$v["code"]]=$v["code"];
                }
            }

            $sqls   = array();
            $values = array();
            $data   = array();
            foreach ($result['Result'] as $key => $value) {
                $run_status = self::getRunStatus(trim($value['Status']));
                $data[$key]['name']         = trim($value['Name']);
                $data[$key]['credit_code']  = $value['No'];
                $data[$key]['corporate']    = $value['OperName'];
                $data[$key]['start_date']   = date_format(date_create($value['StartDate']),'Y-m-d');
                $data[$key]['runs_state']   = $run_status;
                if(array_key_exists($value["KeyNo"],$p))
                {
                    $sqls[] = "update t_partner_info set
                        credit_code='" . $data[$key]['credit_code'] . "',corporate='" . $data[$key]['corporate'] . "',
                        start_date='" . $data[$key]['start_date'] . "',runs_state=" . $run_status . ",
                        update_time=now()
                        where code='" . $value['KeyNo'] . "'";
                    unset($p[$value["KeyNo"]]);
                }
                else
                {
                    $values[]="('".$value['KeyNo']."','".$data[$key]['name']."','".$data[$key]['credit_code']."','".$data[$key]['corporate']."','".$data[$key]['start_date']."',".$run_status.",0,0,now(),0,now())";
                }
            }
            $sql = "";
            if(count($sqls)>0)
            {
                $sql .= implode(";", $sqls).";";
            }
            if(count($values)>0){
                $sql .= "insert into t_partner_info(code,name,credit_code,corporate,start_date,runs_state,is_full,create_user_id,create_time,update_user_id,update_time) values ".implode(",", $values).";";
            }
            if(!empty($sql))
            {
                Utility::execute($sql);
            }*/
            return $result['Result'];
        }
        catch(Exception $e)
        {
            Mod::log("Alarm error, params is ".$context.", and error message is ".$e->getMessage(),"error");
            return array("code"=>-1,"msg"=>$e->getMessage());
        }
    }

    /**
     * 根据runStatus的value获取key
     * @param $value
     * @return int
     */
    public static function getRunStatus($status)
    {
        $map    = Map::$v;
        $runArr = $map["runs_state"];
        $run_status = 1;
        if(!empty($status)){
            foreach ($runArr as $key => $value) {
                if($value==$status){
                    $run_status = $key;
                    break;
                }
            }
        }
        return $run_status;
    }

    /**
     * 计算合作方资料库创建时间和当前日期相差的天数
     */
    public static function calcDays($update_time)
    {
        $updateDays = Mod::app()->params["updatePartnerInfoDays"];
        $datetime1  = new DateTime(date_format(date_create($update_time),'Y-m-d'));
        $datetime2  = new DateTime(date('Y-m-d'));
        $interval   = $datetime1->diff($datetime2);
        $diffDays   = $interval->format('%a');
        if($diffDays > $updateDays){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 当企查查接口查询剩余次数小于100次时，发送告警邮件
     */
    public static function getChaBalance($apiKey)
    {
        if(empty($apiKey))
            return null;
        $url    = "http://i.yjapi.com/ECI/Balance?key=$apiKey";

        $curl = Mod::app()->curl;
        try{
            $res = $curl->get($url);
//            Mod::log("Alarm Log, params is ".$context.", and result is ".$res);
            $result = json_decode($res, true);
            if(is_array($result['Result']) && !empty($result['Result'])){
                $balance = Mod::app()->params["alarmChaChaBalance"];
                if($result['Result']['Balance'] <= $balance && $result['Result']['Balance']!=-7 && $result['Result']['Balance']!=-60){
                    //$map     = Map::$v;
                    $toArray = Map::$v["qichacha_interface_balance_email_alarm_user"];
                    $content = '大家好！<br/>&emsp;&emsp;企查查企业查询接口剩余查询次数&nbsp;<span style="color:#FF0000;font-weight:bold;">'.$result['Result']['Balance'].'</span>&nbsp;次，请及时充值！';
                    $subject = '企查查接口剩余查询次数告警';
                    $fromArray =  array('from'=>'system@jyblife.com', 'from_name'=>'石油系统');
                    Mod::app()->mail->send($toArray, $subject, $content,'',$fromArray);
                    // AMQPService::publishEmail($project->create_user_id,'企查查接口剩余查询次数告警','大家好！<br/>&emsp;&emsp;企查查企业查询接口剩余查询次数&nbsp;<span style="color:#FF0000;font-weight:bold;">'.$result['Result']['Balance'].'</span>&nbsp;次，请及时充值！');
                }
            }
        }
        catch(Exception $e)
        {
//            Mod::log("Alarm error, params is ".$context.", and error message is ".$e->getMessage(),"error");
            return array("code"=>-1,"msg"=>$e->getMessage());
        }
    }

    /**
     * 获取企查查接口调用次数
     */
    public static function getQichachaCallNum()
    {
        $redis = Mod::app()->redis;
        $keyName="oil_qichacha_call_num";
        if($redis->EXISTS($keyName))
            return $redis->get($keyName);
        else 
            return 0;
    }

    /**
     * 设置企查查接口调用次数
     */
    public static function setQichachaCallNum()
    {
        $redis = Mod::app()->redis;
        $keyName="oil_qichacha_call_num";
        
        $redis->incr($keyName);
    }


    public static function getCheckAttachments($detailId)
    {
        if(!Utility::checkQueryId($detailId))
            return array();
        $sql="select * from t_check_attachment where detail_id=".$detailId." and type>30000 and type<=30010 and status=1  order by type asc";
        $data=Utility::query($sql);
        $attachments=array();

        foreach($data as $v)
        {
            $attachments[$v["type"]][]=$v;
        }
        return $attachments;
    }

    public static function getCheckComputeAttachments($detailId)
    {
        if(!Utility::checkQueryId($detailId))
            return array();
        $sql="select * from t_check_attachment where detail_id=".$detailId." and type=30010 and status=1  order by type asc";
        $data=Utility::query($sql);
        $attachments=array();

        foreach($data as $v)
        {
            $attachments[$v["type"]][]=$v;
        }
        return $attachments;
    }


    /**
     * 更新合作方申请表状态
     * @param $partnerId
     * @param $status
     * @return int|string
     */
    public static function updateApplyPartnerStatus($partnerId,$status,$amount='')
    {
        $obj=PartnerApply::model()->findByPk($partnerId);
        if(empty($obj->partner_id))
            return "当前合作方不存在！";

        if($status!=$obj->status || (!empty($amount) && $amount>0)){
            if(!empty($amount) && $amount>0)
                $obj->credit_amount = $amount;
            $obj->status        = $status;
            $obj->status_time   = date("Y-m-d H:i:s");
            $obj->update_user_id= Utility::getNowUserId();
            $obj->update_time   = date("Y-m-d H:i:s");
            $res=$obj->save();
            if($res===true)
                return 1;
            else
                return $res;
        }
        return 1;
    }

    /**
     * 更新合作方信息
     * @param $partnerId
     * @param $status
     * @return int|string
     */
    public static function updatePartnerInfo($partnerId)
    {
        $partner = PartnerApply::model()->findByPk($partnerId);
        Mod::log($partner->attributes);
        if(empty($partner->partner_id))
            return "当前合作方不存在！";
        $obj = Partner::model()->findByPk($partnerId);
        if(empty($obj->partner_id)){
            $obj = new Partner();
            $obj->create_user_id        = $partner->create_user_id;
            $obj->create_time           = $partner->create_time;
        }

        $obj->setAttributes($partner->attributes,false);
        $res=$obj->save();
        if($res===true){
            $obj2 = PartnerCredit::model()->findByPk($partnerId);
            if(empty($obj2->partner_id)){
                $obj2 = new PartnerCredit();
                $obj2->create_user_id   = $partner->create_user_id;
                $obj2->create_time      = $partner->create_time;
            }
            $obj2->status               = 1;
            $obj2->partner_id           = $partnerId;
            $obj2->credit_amount        = $partner->credit_amount;
            $obj2->status_time          = $partner->status_time;
            $obj2->update_user_id       = $partner->update_user_id;
            $obj2->update_time          = $partner->update_time;
            $obj2->save();

            $attachments=PartnerAttachment::model()->find("partner_id=".$partnerId." and status>0");
            $sql = "";
            if(!empty($attachments->id)){
                $sql .= "update t_partner_attachment set status=0 where partner_id=".$partnerId.";";
            }

            $sql .= "insert into t_partner_attachment(partner_id,type,name,file_path,file_url,status,remark,create_user_id,create_time,update_user_id,update_time) select partner_id,type,name,file_path,file_url,status,remark,create_user_id,create_time,update_user_id,update_time from t_partner_apply_attachment where partner_id=".$partnerId." and status>0;";
            Utility::execute($sql);

            // Mod::log($obj->attributes);
            //更新合作方额度
            $partnerEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\IPartnerRepository::class)->findByPk($partnerId);
            if(empty($partnerEntity)) {
                $partnerEntity = new \ddd\domain\entity\Partner();
                $partnerEntity->partner_id = $partnerId;
                $partnerEntity->credit_amount = $partner->credit_amount;
            }

            $res = \ddd\application\PartnerService::service()->passPartner($partnerId, $partnerEntity);
            if ($res !== true) {
                throw new Exception($res);
            }
            self::addPartnerLog('更新合作方表数据',$obj);
            return 1;
        }else
            return $res;
    }


	/**
	 * @desc 添加合作方相关操作日志
	 * @param $logRemark | string 日志备注信息
	 * @param $object | object 操作对象
	 */
	public static function addPartnerLog($logRemark, $object) {
		$create_user_id = isset($object->update_user_id) ? $object->update_user_id : $object->create_user_id;
		$create_time = isset($object->update_time) ? $object->update_time : $object->create_time;
		$content = count($object->getUpdateLog()) > 0 ? Utility::json_encode($object->getUpdateLog()) : '';

		$sql = "insert into t_partner_log(object_id,table_name,content,remark,create_user_id,create_time)
              values (" . $object->partner_id . ",'" . $object->tableName() . "','" . $content . "','" . $logRemark . "'," . $create_user_id . ",'" . $create_time . "')";
		Utility::executeSql($sql);
	}


    /**
     * @desc 根据企查查唯一标识KeyNo，跳转企业详情页
     * @param $name | string 企业全名
     * @param return keyNO
     */
    public static function getKeyNo($name){
        $keyNo = '';
        if(empty($name))
            return $keyNo;

        $info = PartnerInfo::model()->find("name='".$name."'");
        if(!empty($info->id)){
            $keyNo = $info->code;
        }
        return $keyNo;

    }

    /**
     * 对多维数组按指定列排序
     */
    public static function array_msort($array, $cols)
    {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\''.$col.'\'],'.$order.',';
        }
        $eval = substr($eval,0,-1).');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k,1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;
    
    } 

    /**
     * 获取首字母
     */
    public static function getFirstWord($str) 
    { 
        $str= iconv("UTF-8","gb2312", $str);//如果程序是gbk的，此行就要注释掉 
        if (preg_match("/^[\x7f-\xff]/", $str)) 
        { 
            $fchar=ord($str{0}); 
            if($fchar>=ord("A") and $fchar<=ord("z") )return strtoupper($str{0}); 
            $a = $str; 
            $val=ord($a{0})*256+ord($a{1})-65536; 
            if($val>=-20319 and $val<=-20284)return "A"; 
            if($val>=-20283 and $val<=-19776)return "B"; 
            if($val>=-19775 and $val<=-19219)return "C"; 
            if($val>=-19218 and $val<=-18711)return "D"; 
            if($val>=-18710 and $val<=-18527)return "E"; 
            if($val>=-18526 and $val<=-18240)return "F"; 
            if($val>=-18239 and $val<=-17923)return "G"; 
            if($val>=-17922 and $val<=-17418)return "H"; 
            if($val>=-17417 and $val<=-16475)return "J"; 
            if($val>=-16474 and $val<=-16213)return "K"; 
            if($val>=-16212 and $val<=-15641)return "L"; 
            if($val>=-15640 and $val<=-15166)return "M"; 
            if($val>=-15165 and $val<=-14923)return "N"; 
            if($val>=-14922 and $val<=-14915)return "O"; 
            if($val>=-14914 and $val<=-14631)return "P"; 
            if($val>=-14630 and $val<=-14150)return "Q"; 
            if($val>=-14149 and $val<=-14091)return "R"; 
            if($val>=-14090 and $val<=-13319)return "S"; 
            if($val>=-13318 and $val<=-12839)return "T"; 
            if($val>=-12838 and $val<=-12557)return "W"; 
            if($val>=-12556 and $val<=-11848)return "X"; 
            if($val>=-11847 and $val<=-11056)return "Y"; 
            if($val>=-11055 and $val<=-10247)return "Z"; 
        }  
        else 
        { 
            return false; 
        } 
    }


    /**
     * 获取按额度的附件分类
     * @param $partnerType
     * @param $level
     * @param $amount
     * @return int|string
     */
    public static function getAttachmentAmountType($partnerType,$level,$amount)
    {
        $configs=PartnerConfig::$partnerAttachmentAmountConfig[$partnerType][$level];
        if(is_array($configs))
        {
            foreach ($configs as $k=>$v)
            {
                if($amount>$v[0] && $amount<=$v[1])
                    return $k;
            }
        }
        return "-1";
    }

    /**
     * 根据供应商类别返回风控准入的风险类别
     * @param $partnerType
     * @return int
     */
    public static function getPartnerRiskType($partnerType)
    {
        $types = explode(',', $partnerType);
        if(in_array(Partner::TYPE_DOWN, $types))
        {
            return Partner::RISK_TYPE_DOWN;
        }
        return Partner::RISK_TYPE_UP;
    }

    /**
     * @desc 获取合作方相关额度数据
     * @param int $partnerId
     * @return array [
     *      'stock_in_amount' => 1234               #入库金额
     *      'stock_out_amount' => 1234              #出库金额
     *      'stock_in_settle_amount' => 1234        #入库结算货款金额，为0说明未结算
     *      'stock_out_settle_amount' => 1234       #出库结算货款金额，为0说明未结算
     *      'paid_amount' => 1234                   #已付金额
     *      'received_amount' => 1234               #已收金额
     *      'input_invoice_amount' => 1234          #进项票金额
     *      'output_invoice_amount' => 1234         #销项票金额
     * ]
     */
    public static function getPartnerAmountDetail($partnerId)
    {
        $res = array('stock_in_amount' => 0, 'stock_out_amount' => 0, 'stock_in_settle_amount' => 0, 'stock_out_settle_amount' => 0, 'goods_in_unsettled_amount' => 0, 'goods_out_unsettled_amount' => 0, 'in_settle_diff_amount' => 0, 'out_settle_diff_amount' => 0, 'paid_amount' => 0, 'received_amount' => 0, 'input_invoice_amount' => 0, 'output_invoice_amount' => 0);
        if (Utility::checkQueryId($partnerId)) {
            $partner = Partner::model()->findByPk($partnerId);
            if(!empty($partner)) {
                //合作方下所有合同
                $contracts = Contract::model()->findAll('partner_id=:partnerId and status>=:status1 and status<>:status2', array('partnerId' => $partner->partner_id, 'status1' => Contract::STATUS_SUBMIT, 'status2' => Contract::STATUS_BUSINESS_REJECT));
                if (Utility::isNotEmpty($contracts))
                {
                    foreach ($contracts as $contract)
                    {
                        //收付款有可能存在退款，所以不区分采销类型
                        $res['paid_amount'] += ContractService::getContractGoodsActualPaidAmount($contract->contract_id); //合同实付金额
                        $res['received_amount'] += ReceiveConfirmService::getReceivedGoodsAmountByContractId($contract->contract_id); //合同实收金额
                        if ($contract->type == ConstantMap::BUY_TYPE)
                        { //采购合同
                            //合同入库单金额
                            $res['stock_in_amount'] += ContractService::getContractStockInAmount($contract->contract_id);

                            //合同入库结算金额
                            $res['stock_in_settle_amount'] += ContractService::getContractGoodsSettlementAmount($contract->contract_id, $contract);

                            //合同入库未结算金额
                            $res['goods_in_unsettled_amount'] += ContractService::getTradeGoodsInUnsettledAmount($contract->contract_id, $contract);

                            //合同入库结算差额
                            $res['in_settle_diff_amount'] += ContractService::getTradeGoodsInSettleDiffAmount($contract->contract_id, $contract);

                            //合同进项票金额
                            $res['input_invoice_amount'] += ContractService::getContractInputInvoiceAmount($contract->contract_id);
                        } else
                        { //销售合同
                            //合同出库单金额
                            $res['stock_out_amount'] += ContractService::getContractStockOutAmount($contract->contract_id);

                            //合同出库结算金额
                            $res['stock_out_settle_amount'] += ContractService::getContractGoodsSettlementAmount($contract->contract_id, $contract);

                            //合同出库未结算金额
                            $res['goods_out_unsettled_amount'] += ContractService::getTradeGoodsOutUnsettledAmount($contract->contract_id, $contract);

                            //合同出库结算差额
                            $res['out_settle_diff_amount'] += ContractService::getTradeGoodsOutSettleDiffAmount($contract->contract_id, $contract);

                            //合同销项票金额
                            $res['output_invoice_amount'] += ContractService::getContractOutputInvoiceAmount($contract->contract_id);
                        }
                    }
                }
            }
        }
        return $res;
    }
}
