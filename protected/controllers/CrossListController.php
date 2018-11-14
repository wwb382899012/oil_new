<?php

/**
 * Created by vector.
 * DateTime: 2017/10/20 15:58
 * Describe：调货处理
 */
class CrossListController  extends AttachmentController
{

    public function pageInit()
    {
        $this->rightCode="crossList";
        $this->filterActions="";
    }


    public function actionIndex()
    {
        $attr = $_GET[search];

        $user = SystemUser::getUser(Utility::getNowUserId());

        $fields  = "o.cross_id,o.cross_code,o.status,
                    c.contract_id as sell_id,c.contract_code as sell_code,
                    pa.partner_id,pa.name as partner_name,g.name as goods_name,
                    g.goods_id,co.corporation_id,co.name as corporation_name,cf.code_out";

        $sql1   = "select ".$fields."
            from t_cross_order o
            left join t_contract c on o.contract_id=c.contract_id
            left join t_partner pa on c.partner_id=pa.partner_id
            left join t_goods g on o.goods_id=g.goods_id
            left join t_corporation co on c.corporation_id=co.corporation_id 
            left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1 
            ". $this->getWhereSql($attr);

        $sql1  .= " and o.type=".ConstantMap::ORDER_CROSS_TYPE." 
                        and o.status>=".CrossOrder::STATUS_PASS." 
                        and c.corporation_id in (".$user['corp_ids'].") 
                        group by o.cross_id order by o.cross_id desc";
        $sql = 'select {col} from (' . $sql1 . ') as gs where 1=1 {limit}';
        
        $data=$this->queryTablesByPage($sql,'*');

        $data["search"]=$attr;
        $this->render("index", $data);
    }

    //判断是否能释放
    public function checkIsCanSubmit($status)
    {
        if($status == CrossOrder::STATUS_PASS)
        {
            return true;
        }
        else
            return false;
    }


    public function actionDetail()
    {
        $id  = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("参数有误！", $this->mainUrl);

        $crossDetail = CrossOrderService::getOrderDetailById($id);
        if(Utility::isEmpty($crossDetail)){
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }

        $sql = "select i.detail_id,c.contract_code,c.contract_id,g.goods_id,g.name as goods_name,i.more_or_less_rate,i.quantity,i.unit from t_contract c 
                left join t_contract_goods i on c.contract_id=i.contract_id
                left join t_goods g on i.goods_id=g.goods_id
                where c.contract_id=".$crossDetail[0]['contract_id']."";
        $data = Utility::query($sql);

        $contract       = array();
        $transactions   = array();
        if(Utility::isNotEmpty($data)){
            $contract['contract_id'] = $data[0]['contract_id'];
            $contract['contract_code'] = $data[0]['contract_code'];
            
            $transactions = $data;
            foreach ($data as $key => $value) {
                if($value['goods_id']==$crossDetail[0]['goods_id']){
                    $contract['goods_id']   = $value['goods_id'];
                    $contract['goods_name'] = $value['goods_name'];
                    $contract['detail_id']  = $value['detail_id'];
                }
            }
        }else{
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }

        $this->pageTitle="查看详情";
        $this->render("detail",array(
            "data" => $crossDetail,
            "contract"=>$contract,
            "transactions"=>$transactions
            )
        );
    }

    public function actionSubmit()
    {
        $id = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("参数错误！", $this->mainUrl);
        }

        $order = CrossOrder::model()->with("crossDetail")->findByPk($id);
        if(!$this->checkIsCanSubmit($order->status))
            $this->returnError("当前状态下不可提交借货信息！");

        $nowUserId = Utility::getNowUserId();
        $nowTime   = new CDbExpression("now()");

        $trans = Utility::beginTransaction();
        try{
            $oldStatus = $order->status;
            $order->status = CrossOrder::STATUS_DONE;
            $order->status_time      = $nowTime;
            $order->update_time      = $nowTime;
            $order->update_user_id   = $nowUserId;
            $order->save();

            $crossDetail = $order->crossDetail;
            if(Utility::isNotEmpty($crossDetail)){
                foreach ($crossDetail as $detail) {
                    $r = StockService::unFreeze($detail->stock_id, $detail->quantity_balance);
                    if(!$r){
                        throw new Exception("库存ID为".$detail->stock_id.",解冻库存失败！");
                    }

                    $rows= CrossDetail::model()->updateByPk($detail->detail_id
                        ,array(
                             "quantity_balance"=>new CDbExpression("0"),
                             "quantity"=>new CDbExpression("quantity-".$detail->quantity_balance),
                             "update_time"=>new CDbExpression("now()"),
                             "remark"=>json_encode(array('origin'=>$detail->quantity, "actual"=>($detail->quantity - $detail->quantity_balance)))
                             // "remark"=>"原始借货数量为：".$detail->quantity.";实际借货数量为：".($detail->quantity - $detail->quantity_balance)
                         )
                        ,"quantity-quantity_balance>=0"
                    );
                    if($rows!=1)
                        throw new Exception("借还货ID为:".$detail->detail_id.",更新库存数量:".$detail->quantity_balance."失败！");
                }
            }

            $trans->commit();

            Utility::addActionLog(json_encode(array('oldStatus'=>$oldStatus)), "提交调货单借货完成", "CrossOrder", $order->cross_id);
            $this->returnSuccess();
            
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$CROSS_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }
        
    }
}