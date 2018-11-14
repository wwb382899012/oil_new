<?php

/**
 * Created by youyi000.
 * DateTime: 2016/6/24 15:57
 * Describe：
 */
class Attachment
{
    //const C_CUSTOMER="customer";
    //const C_CONTACT="contact";
    const C_SUPPLIER="supplier";
    const C_PROJECT="project";
    const C_BUDGET="budget";
    const C_CONTRACT="contract";
    //const C_RESOLUTION="resolution";
    const C_CONTRACTFILE="contractFile";
    const C_PAYMENT="payment";
    const C_PAYSTOP="payStop";
    //const C_UPREMIND="upRemind";
    //const C_UPRECEIVE="upReceive";
    //const C_DOWNREMIND="downRemind";
    //const C_DOWNRECEIVE="downReceive";
    //const C_INVOICE="invoice";
    //const C_EXPRESS="express";
    //const C_DOWNCONFIRM="downConfirm";
    //const C_FEEDBACK="feedback";
    //const C_REVCONFIRM="revConfirm";
    //const C_PAYREQUEST="payRequest";
    //const C_INVOICEUP="invoiceUp";
    //const C_INVOICEREMIND="invoiceRemind";

    const C_PAY_APPLICATION="payApplication";//付款申请

    //const C_CONFERENCE="conference";

    const C_TRASH_PROJECT="trashProject";

    const C_PARTNER="partner";
    const C_PARTNER_A="partnerA";
    const C_PARTNER_B="partnerB";
    const C_PARTNER_CHECK="partnerCheck";//审核
    const C_PARTNER_RISK="partnerRisk";//现场风控
    const C_PARTNER_REVIEW="partnerReview";//评审
    const C_PARTNER_REVIEW_EXTRA="partnerReviewExtra";//评审补充材料
    const C_PARTNER_APPLY="partnerApply";
    const C_USER_EXTRA = "userExtra"; //用户额外信息
    const C_USER_CREDIT = "userCredit"; //用户个人额度信息

    const C_STOCK_NOTICE = 'stockNotice'; //入库通知单
    const C_STOCK_IN = 'stockIn'; //入库单
    const C_STOCK_BATCH_SETTLEMENT = 'stockBatchSettlement'; // 入库单结算
    const C_FACTORING = 'factor'; // 保理
    const C_BANK_FLOW_IMPORT = 'bankFlowImport'; //银行收款流水单
    const C_RECEIVE_CONFIRM_IMPORT = 'receiveConfrim'; //银行收款流水单

    const C_INVOICE_APPLY = "invoiceApply"; //(进项票:1,销项票:11)发票申请附件

    const C_STOCK_INVENTORY = 'stockInventory'; //库存盘点
    const C_STOCK_OUT = 'stockOut'; //仓库出库单
    const C_STOCK_DELIVERY='stockDelivery'; //发货单、出库单
    const C_DELIVERY_ORDER_SETTLEMENT="deliveryOrderSettlement";//发货单结算

    const C_CONTRACT_SETTLEMENT = 'contractSettlement'; // 合同结算
    const C_CONTRACT_SPLIT = 'contractSplit'; //合同拆分
    const C_STOCK_SPLIT = 'stockSplit'; //出入库拆分
    const C_CONTRACT_TERMINATE = 'contractTerminate'; //合同拆分
    /**
     * 当前配置信息
     *  array("key"=>"customer","filePath"=>"/upload/customer/","mapName"=>"customer_attachment_type","tableName"=>"t_customer_attachment","baseFieldName"=>"customer_id",)
     * @var
     */
    public $config;

    public $type=0;

    /**
     * 附件的配置信息
     * @var array
     */
    public $configs=array(
        "project"=>array("key"=>"project","filePath"=>"/data/oil_new/upload/project/","mapName"=>"project_launch_attachment_type","tableName"=>"t_project_attachment","baseFieldName"=>"project_id",),
        "budget"=>array("key"=>"budget","filePath"=>"/data/oil_new/upload/budget/","mapName"=>"project_budget_attachment_type","tableName"=>"t_project_attachment","baseFieldName"=>"project_id",),

        "contract"=>array("key"=>"contract","filePath"=>"/data/oil/upload/contract/","mapName"=>"all_contract_attachment_type","tableName"=>"t_contract","baseFieldName"=>"project_id","idFieldName"=>"contract_id",),
        //"resolution"=>array("key"=>"resolution","filePath"=>"/data/oil/upload/resolution/","mapName"=>"resolution_attachment_type","tableName"=>"t_project_attachment","baseFieldName"=>"project_id",),
        "contractFile"=>array("key"=>"contractFile","filePath"=>"/data/oil/upload/contractFile/","mapName"=>"contract_file_attachment_type","tableName"=>"t_contract_file","baseFieldName"=>"contract_id","idFieldName"=>"file_id"),


        //合作方相关的
        "partner"=>array("key"=>"partner","filePath"=>"/data/oil/upload/partner/","mapName"=>"partner_attachment_type","tableName"=>"t_partner_apply_attachment","baseFieldName"=>"partner_id",),

        "partnerA"=>array("key"=>"partnerA","filePath"=>"/data/oil/upload/partner/","mapName"=>"partner_a_attachment_type","tableName"=>"t_partner_apply_attachment","baseFieldName"=>"partner_id",),
        "partnerB"=>array("key"=>"partnerB","filePath"=>"/data/oil/upload/partner/","mapName"=>"partner_b_attachment_type","tableName"=>"t_partner_apply_attachment","baseFieldName"=>"partner_id",),

        "trashProject"=>array("key"=>"trashProject","filePath"=>"/data/oil/upload/project/","mapName"=>"project_trash_attachment_type","tableName"=>"t_project_attachment","baseFieldName"=>"project_id",),

        "partnerCheck"=>array("key"=>"partnerCheck","filePath"=>"/data/oil/upload/partner/check/","mapName"=>"partner_check_attachment_type","tableName"=>"t_check_attachment","baseFieldName"=>"detail_id",),
        "partnerRisk"=>array("key"=>"partnerRisk","filePath"=>"/data/oil/upload/partner/risk/","mapName"=>"partner_risk_attachment_type","tableName"=>"t_partner_risk_attachment","baseFieldName"=>"base_id",),
        "partnerReview"=>array("key"=>"partnerRisk","filePath"=>"/data/oil/upload/partner/risk/","mapName"=>"partner_review_attachment_type","tableName"=>"t_partner_review_attachment","baseFieldName"=>"base_id",),
        "partnerReviewExtra"=>array("key"=>"partnerRisk","filePath"=>"/data/oil/upload/partner/risk/","mapName"=>"partner_review_extra_attachment_type","tableName"=>"t_partner_review_attachment","baseFieldName"=>"base_id",),
        "partnerApply"=>array("key"=>"partnerApply","filePath"=>"/data/oil/upload/partner/apply","mapName"=>"partner_apply_attachment_type","tableName"=>"t_partner_apply_attachment","baseFieldName"=>"partner_id",),
        "userExtra"=>array("key"=>"userExtra","filePath"=>"/data/oil/upload/user/extra","mapName"=>"user_extra_attachment_type","tableName"=>"t_user_attachment","baseFieldName"=>"base_id",),
        "userCredit"=>array("key"=>"userCredit","filePath"=>"/data/oil/upload/user/credit","mapName"=>"user_credit_attachment_type","tableName"=>"t_user_attachment","baseFieldName"=>"base_id",),

        "stockNotice"=>array("key"=>"stockNotice","filePath"=>"/data/oil/upload/stock/stockNotice","mapName"=>"stock_notice_attachment_type","tableName"=>"t_stock_in_batch_attachment","baseFieldName"=>"base_id",),
        "stockOut"=>array("key"=>"stockOut","filePath"=>"/data/oil/upload/stock/stockOut","mapName"=>"stock_delivery_attachment","tableName"=>"t_delivery_attachment","baseFieldName"=>"base_id",),
        "stockIn"=>array("key"=>"stockIn","filePath"=>"/data/oil/upload/stock/stockIn","mapName"=>"stock_in_attachment_type","tableName"=>"t_stock_in_attachment","baseFieldName"=>"base_id",),
        "stockBatchSettlement"=>array("key"=>"stockBatchSettlement","filePath"=>"/data/oil/upload/stock/stockBatchSettlement","mapName"=>"stock_batch_settlement_type","tableName"=>"t_stock_batch_settlement_attachment","baseFieldName"=>"base_id",),
        "factor"=>array("key"=>"factor","filePath"=>"/data/oil/upload/factor","mapName"=>"factor_attachment_type","tableName"=>"t_factoring_attachment","baseFieldName"=>"base_id",),

        "payApplication"=>array("key"=>"payApplication","filePath"=>"/data/oil/upload/pay/application","mapName"=>"pay_application_attachment_type","tableName"=>"t_pay_attachment","baseFieldName"=>"base_id",),


        // "bankFlowImport"=>array("key"=>"bankFlow","filePath"=>"runtime/upload/stock/bankFlow","mapName"=>"bank_flow_file_type","tableName"=>"t_bank_flow_file_temp_attachment","baseFieldName"=>"base_id",),
        "bankFlowImport"=>array("key"=>"bankFlow","filePath"=>"/data/oil/upload/stock/bankFlow","mapName"=>"bank_flow_file_type","tableName"=>"t_bank_flow_file_temp_attachment","baseFieldName"=>"base_id",),
        "receiveConfrim"=>array("key"=>"bankFlow","filePath"=>"/data/oil/upload/stock/receiveConfrim","mapName"=>"receive_confirm_file_type","tableName"=>"t_receive_confirm_file_temp_attachment","baseFieldName"=>"base_id",),

        "invoiceApply"=>array("key"=>"invoiceApply","filePath"=>"/data/oil/upload/invoice/apply","mapName"=>"invoice_application_attachment_type","tableName"=>"t_invoice_attachment","baseFieldName"=>"base_id",),
        "payment"=>array("key"=>"payment","filePath"=>"/data/oil/upload/payment/","mapName"=>"payment_attachment_type","tableName"=>"t_pay_attachment","baseFieldName"=>"base_id",),
        "payStop"=>array("key"=>"payStop","filePath"=>"/data/oil/upload/payStop/","mapName"=>"pay_stop_attachment_type","tableName"=>"t_pay_attachment","baseFieldName"=>"base_id",),
        "stockInventory"=>array("key"=>"stockInventory","filePath"=>"/data/oil/upload/stock/inventory/","mapName"=>"stock_inventory_attachment","tableName"=>"t_stock_inventory_attachment","baseFieldName"=>"base_id",),
        "stockDelivery"=>array("key"=>"stockDelivery","filePath"=>"/data/oil/upload/stock/delivery/","mapName"=>"stock_delivery_attachment","tableName"=>"t_delivery_attachment","baseFieldName"=>"base_id",),
        "deliveryOrderSettlement"=>array("key"=>"deliveryOrderSettlement","filePath"=>"/data/oil/upload/stock/delivery/","mapName"=>"delivery_settlement_attachment","tableName"=>"t_delivery_settlement_attachment","baseFieldName"=>"base_id",),
        
        "contractSettlement"=>array("key"=>"contractGoodsSettlement","filePath"=>"/data/oil/upload/contractSettlement/","mapName"=>"contract_settlement_attachment","tableName"=>"t_contract_settlement_attachment","baseFieldName"=>"base_id",),
//        'contractOtherSettlement'=>array("key"=>"contractOtherSettlement","filePath"=>"/data/oil/upload/contract_settlement/","mapName"=>"contract_settlement_other_attachment","tableName"=>"t_contract_settlement_other_attachment","baseFieldName"=>"base_id",),

        "contractSplit" => array("key"=>"contractSplit","filePath"=>"/data/oil/upload/contractSplit/","mapName"=>"contract_split_attachment","tableName"=>"t_contract_split_attachment","baseFieldName"=>"base_id"),
        "stockSplit" => array("key"=>"stockSplit","filePath"=>"/data/oil/upload/stockSplit/","mapName"=>"stock_split_attachment","tableName"=>"t_stock_split_attachment","baseFieldName"=>"base_id"),
        "contractTerminate" => array("key"=>"contractTerminate","filePath"=>"/data/oil/upload/contractTerminate/","mapName"=>"contract_terminate_attachment","tableName"=>"t_contract_terminate_attachment","baseFieldName"=>"base_id"),
    );

    /**
     * 附件类型信息
     *  array("id"=>"1","name"=>"营业执照","maxSize"=>40,"fileType"=>"|jpg|png|jpeg|bmp|")
     * @var
     */
    public $typeInfo;

    /**
     * 当前类型的附件配置信息
     * @var
     */
    public $typeConfig;
    public $map;

    public $file=array("id"=>0,"name"=>"","fileUrl"=>"","filePath"=>"","status"=>0);


    function __construct($key) {
        $this->config=$this->configs[$key];
        $this->map= Map::$v;
        $this->typeConfig= $this->map[$this->config["mapName"]];
        $this->init();
    }

    public function init()
    {

    }

    /**
     * 保存上传文件主方法
     * @param $baseId
     * @param $type
     * @param $file
     * @param int $userId
     * @param null $extras
     * @param int $isWordToPdf 是否自动转PDF
     * @return int|string
     */
    public function saveFile($baseId,$type,$file,$userId=0,$extras=null,$isWordToPdf=0)
    {
        //$file=$_FILES["files"];

        $this->type=$type;

        if (empty($file))
        {
            return "文件不能为空！";
        }

        $this->typeInfo=$this->getTypeInfo();
        //print_r($this->typeInfo);die;

        $fileName = $file["name"][0];
        //echo $fileName;
        //print_r($fileName);die;
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $originalName = $fileName;//pathinfo($fileName, PATHINFO_BASENAME);
        //print_r($fielname);die;

        $res=$this->checkFileIsPermit($file["size"][0],$extension);
        if($res!=1)
            return $res;

        $filePath=$this->getFilePath($baseId);
        //$name=$this->typeInfo["name"]."_".Utility::getRandomKey() . ".".$extension;
        //$name=$fName."_".Utility::getRandomKey() . ".".$extension;
        $name=$baseId."_".$type."_".time()."_".Utility::getRandomKey() . ".".$extension;
        $filePath = $filePath.$name;
        try
        {
            //move_uploaded_file($file["tmp_name"][0],iconv("UTF-8","GB2312",ROOT_DIR.$filePath));
            move_uploaded_file($file["tmp_name"][0],$filePath);
            if($isWordToPdf && ($extension=="doc" || $extension=="docx"))
            {
                //AMQPService::publishFileWordToPDF($filePath);
                Utility::wordToPdf($filePath);
            }
        }
        catch (Exception $e)
        {
            Mod::log('文件上传失败,message:'.$e->getMessage(),'error');
            return $e->getMessage();
        }

        $this->file["name"]=$originalName;
        $this->file["fileUrl"]=$filePath;
        $this->file["filePath"]=$filePath;

        //保存信息
        $res=$this->saveAttachmentLog($baseId,$userId,$extras,$this->typeInfo["multi"]);


        return $res;
    }



    /**
     * 获取文件存储相对于根目录的路径
     * @param $baseId
     * @return string
     */
    public function getFilePath($baseId)
    {
        $n=100;
        $filePath=$this->config["filePath"];
        $k=intval($baseId/$n);
        $filePath.=($k*$n+1)."-".($k*$n+$n)."/".$baseId."/";
        Utility::checkDirectory($filePath);
        return $filePath;
    }

    /**
     * 获取文件类别相关的信息，一般在子类中重写
     * @return array
     */
    public function getTypeInfo()
    {
        return $this->map[$this->config["mapName"]][$this->type];
    }

    /**
     * 判断文件是否允许上传
     * @param $size
     * @param $fileExtension
     * @return int|string
     */
    public function checkFileIsPermit($size,$fileExtension)
    {
        //$typeInfo=$this->getTypeInfo();
        if($size>$this->typeInfo["maxSize"]*1024*1024)
        {
            return "文件大小超出最大限制，最大为".$this->typeInfo["maxSize"]."M";
        }

        if(!(strpos("#".$this->typeInfo["fileType"],$fileExtension)>0))
        {
            return "文件类型不是允许的上传类型，允许的文件类型为：".$this->typeInfo["fileType"];
        }

        return 1;

    }

    /**
     * 保存附件信息
     * @param $baseId
     * @param int $userId
     * @param null $extras
     * @return int|string
     */
    protected function saveAttachmentLog($baseId,$userId=0,$extras=null)
    {
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            $sqls=array();

            $fields="";
            $value="";
            $query="";
            if(is_array($extras) && count($extras)>0)
            {
                foreach ($extras as $k=>$v)
                {
                    $fields.=",".$k;
                    $value .=",'".$v."'";
                    $query .= " and ".$k."=".$v;
                }
            }


            if($this->typeInfo["multi"]!=1)
            {
                $sql = "update " . $this->config["tableName"] . " set status=0,update_time=now(),update_user_id='" . $userId . "' where type=" . $this->type . " and " . $this->config["baseFieldName"] . "=" . $baseId . " and status>=1" . $query;
                Utility::executeSql($sql);
            }

            $sql="insert into ".$this->config["tableName"]."(".$this->config["baseFieldName"].",type,name,file_path,file_url,status,create_time,create_user_id,update_time,update_user_id".$fields.")
                values(".$baseId.",".$this->type.",'".$this->file["name"]."',:filePath,:fileUrl,1,now(),'".$userId."',now(),'".$userId."'".$value.")";
            Utility::executeSql($sql,Utility::DB,array("filePath"=>$this->file["filePath"],"fileUrl"=>$this->file["fileUrl"]));

            $idName=$this->getIdFiledName();

            $sql="select ".$idName." from ".$this->config["tableName"]." where type=".$this->type." and ".$this->config["baseFieldName"]."=".$baseId." and status=1".$query." order by ".$idName." desc limit 1";
            $data=Utility::query($sql);

            $trans->commit();
            $this->file["id"]=$data[0][$idName];
            return 1;
        } catch (Exception $e) {
            try { $trans->rollback(); }catch(Exception $ee){}
            return $e->getMessage();
        }
    }

    protected function getIdFiledName()
    {
        $idName=empty($this->config["idFieldName"])?"id":$this->config["idFieldName"];
        return $idName;
    }

    /**
     * 获取关联id字段名
     * @return string
     */
    protected function getBaseIdFiledName()
    {
        $baseFieldName=empty($this->config["baseFieldName"])?"base_id":$this->config["baseFieldName"];
        return $baseFieldName;
    }

    /**
     * 获取文件的读取路径
     * @param $id
     * @return null
     */
    public function getFileReadPath($id)
    {
        $idName=$this->getIdFiledName();
        $sql="select * from ".$this->config["tableName"]." where ".$idName."=".$id."";
        $data=Utility::query($sql);
        //Mod::log("hz_log attachment data:".json_encode($data));
        if(Utility::isNotEmpty($data))
            return $data[0]["file_path"];
        else
            return null;
    }

    /**
     * 判断指定类别的文件是否上传
     * @param $type
     * @return null
     */
    public function checkIsExistWithType($type)
    {
        $sql="select * from ".$this->config["tableName"]." where type=".$type." and status=1";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
            return true;
        else
            return false;
    }

    /**
     * 删除文件，同时更新文件记录的状态为删除状态
     * @param $id
     * @return bool
     */
    public function deleteFile($id)
    {
        $idName=$this->getIdFiledName();
        $sql="select * from ".$this->config["tableName"]." where ".$idName."=".$id."";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
        {
            if(!key_exists($data[0]["type"],$this->typeConfig))
            {
                Mod::log("非法删除表".$this->config["tableName"]."中".$id."的文件","error");
                return false;
            }
            $res = @unlink ($data[0]["file_path"]);
            if($res)
            {
                $sql="update ".$this->config["tableName"]." set status=-1 where ".$idName."=".$id."";
                $res=Utility::execute($sql);
                if($res!=-1)
                {
                    return true;
                }
                else
                {
                    Mod::log("更新表".$this->config["tableName"]."的文件".$id."的状态出错","error");
                    return false;
                }

            }
            else
            {
                Mod::log("删除表".$this->config["tableName"]."中标识为".$id."的文件出错","error");
                return false;
            }
        }
        return true;
    }


    /**
     * 获取指定baseId的所有正常的附件信息
     * @param $baseId
     * @param null $type
     * @return array
     */
    public function getAttachments($baseId,$type=null)
    {
        if(empty($baseId))
            return array();

        if(empty($this->config))
            return array();

        $condition="";
        if(!empty($type))
            $condition=" and type=".$type;


        $sql="select * from ".$this->config["tableName"]." where ".$this->config["baseFieldName"]."=".$baseId." ".$condition." and status=1 ";
        $data=Utility::query($sql);
        $attachments = array();
        foreach ($data as $v) {
            $attachments[$v["type"]][] = $v;
        }

        return $attachments;
    }

}