<?php

/**
 * Created by vector.
 * DateTime: 2017/08/31 15:58
 * Describe：采购锁价
 */
class BuyLockController  extends AttachmentController
{

    public function pageInit()
    {
        $this->rightCode="buyLock";
        $this->filterActions="";
//        $this->newUIPrefix = 'new_';
    }


    public function actionIndex()
    {
//        $attr = $_GET[search];
        $attr = $this->getSearch();

        $user = SystemUser::getUser(Utility::getNowUserId());

        $fields  = "c.contract_id,c.contract_code,c.currency,pa.partner_id,pa.name as partner_name,g.name as goods_name,t.lock_type,t.detail_id,
                    t.price,t.quantity,t.unit,t.more_or_less_rate,t.goods_id,cf.code_out";

        $sql1 = "select ".$fields."
            from t_contract c 
            left join t_contract_goods t on c.contract_id=t.contract_id
            left join t_contract_file f on c.contract_id=f.contract_id
            left join t_project p on c.project_id=p.project_id
            left join t_system_user u on p.manager_user_id=u.user_id
            left join t_partner pa on c.partner_id=pa.partner_id
            left join t_goods g on t.goods_id=g.goods_id 
            left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1 
            ". $this->getWhereSql($attr);
            //and c.status>=".Contract::STATUS_BUSINESS_CHECKED."
            //and f.is_main=1 and f.type=11 and f.status=".ContractFile::STATUS_CHECKING."
        $sql1    .= " and c.type=1 and c.price_type=2 and t.type=1 and c.status>=".Contract::STATUS_BUSINESS_CHECKED." and p.corporation_id in (".$user['corp_ids'].") 
        group by c.contract_id,t.goods_id order by p.project_id desc,c.contract_id desc,t.detail_id desc";

        $sql = 'select {col} from (' . $sql1 . ') as gs where 1=1 {limit}';
        $data=$this->queryTablesByPage($sql,'*');
        $map = Map::$v;
        // print_r($data);die;
        if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $plus = "";
                $data['data']['rows'][$key]['price'] = $map['currency'][$row['currency']]['ico'].' '.number_format($row['price']/100, 2);
                if($row['more_or_less_rate']>0){
                    $more_or_less_rate = $row['more_or_less_rate']*100;
                    $plus = "±".$more_or_less_rate."%";
                }
                $data['data']['rows'][$key]['contract_quantity'] = number_format($row['quantity'], 2).$map['goods_unit'][$row['unit']]['name'].$plus;
                if(!empty($row['lock_type'])){                
                    $lockArr = LockPrice::model()->findAllToArray("contract_id=".$row['contract_id']." and goods_id=".$row['goods_id']);
                    $lock_quantity = 0;
                    if(count($lockArr)>0){
                        if($row['lock_type']==ConstantMap::LOCK_PUT_ORDER){
                            foreach ($lockArr as $k => $val) {
                                $lock_quantity += $val['lock_quantity'];
                            }
                        }else{
                            $lock_quantity = $lockArr[0]['lock_quantity'];
                        }

                        if(!empty($lock_quantity))
                            $data['data']['rows'][$key]['lock_quantity'] = number_format($lock_quantity, 2).$map['goods_unit'][$lockArr[0]['unit']]['name'];
                    }
                    
                    $data['data']['rows'][$key]['lock_type'] = $map['lock_type'][$row['lock_type']];
                }
            }
        }
        // print_r($data);die;

        $data["search"]=$attr;
        $this->render("index", $data);
    }

    public function actionConfirm()
    {
        $params = $_POST["obj"];

        if(empty($params['lock_type']) || empty($params['detail_id']))
            $this->returnError("参数有误！");
        $contractGoods = ContractGoods::model()->with('goods')->findByPk($params['detail_id']);
        if(empty($contractGoods->detail_id))
            $this->returnError("当前品名的交易明细不存在！");
        if(!empty($contractGoods->lock_type))
            $this->returnError("锁价维度已经确定，不可更改！");

        $contract = Contract::model()->findByPk($contractGoods->contract_id);

        if($params['lock_type']==ConstantMap::LOCK_PUT_ORDER){
            $noticeArr = StockNoticeDetail::model()->findAllToArray(array("condition"=>"goods_id=".$contractGoods->goods_id." and contract_id=".$contractGoods->contract_id,"order"=>"batch_id asc"));
            if(Utility::isEmpty($noticeArr))
                $this->returnError("合同编号：".$contract->contract_code."<br/>品名：".$contractGoods->goods->name."<br/>没有可锁价的入库通知单，请先添加入库通知单！");
        }
        

        $contractGoods->lock_type = $params['lock_type'];

        $lock_id = 0;

        $trans = Utility::beginTransaction();
        try{
            $contractGoods->save();

            $nowTime    = new CDbExpression("now()");
            $nowUserId  = Utility::getNowUserId();
            //保存锁价信息
            if($params['lock_type']==ConstantMap::LOCK_TYPE_CONTRACT){ //按采购合同锁价
                $obj = new LockPrice();
                $obj->detail_id     = $contractGoods->detail_id;
                $obj->goods_id      = $contractGoods->goods_id;
                $obj->contract_id   = $contractGoods->contract_id;
                $obj->quantity      = '-1';
                $obj->lock_quantity = 0;
                $obj->balance_quantity = 0;
                $obj->unit = $contractGoods->unit;
                $obj->status = 1;
                $obj->status_time   = $nowTime;
                $obj->create_user_id= $nowUserId;
                $obj->create_time   = $nowTime;
                $obj->update_time   = $nowTime;
                $obj->update_user_id= $nowUserId;
                $obj->save();

                $lock_id = $obj->lock_id;
                
            }else{ //按入库通知单锁价
                $sqls = BuyLockService::insertLockStockNotice($noticeArr, $contractGoods, $userId);
                Utility::execute($sqls);
            }

            //保存计价标的
            $target = new ContractGoodsTarget();
            $target->project_id     = $contractGoods->project_id;
            $target->contract_id    = $contractGoods->contract_id;
            $target->goods_id       = $contractGoods->goods_id;
            $target->lock_id        = $lock_id;
            $target->name           = $contractGoods->refer_target;
            $target->status = 1;
            $target->status_time    = $nowTime;
            $target->create_user_id = $nowUserId;
            $target->create_time    = $nowTime;
            $target->update_time    = $nowTime;
            $target->update_user_id = $nowUserId;
            $target->save();    

            $trans->commit();

            $this->returnSuccess();
        }catch(Exception $e){
            try{ $trans->rollback(); }catch(Exception $ee){}

            $this->returnError("操作失败！".$e->getMessage());
        }
    }


    //锁价操作
    public function actionLock()
    {
        $id = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("参数错误！", "/buyLock/");
        }

        $contractGoods = ContractGoods::model()->with('contract', 'goods')->findByPk($id);
        if(empty($contractGoods->detail_id) || empty($contractGoods->contract->contract_id) || empty($contractGoods->goods->goods_id))
        {
            $this->renderError("当前信息不存在！", "/buyLock/");
        }

        $contract = $contractGoods->contract;
        $goods = $contractGoods->goods;

        
        $lock = LockPrice::model()->findAllToArray(array("condition"=>"contract_id=".$contract->contract_id." and goods_id=".$goods->goods_id, "order"=>"lock_id asc"));

        $lock_quantity = 0;
        $lockBatch = array();
        if($contractGoods->lock_type==ConstantMap::LOCK_PUT_ORDER){
            foreach ($lock as $v) {
                $lock_quantity += $v['lock_quantity'];
                $lockBatch[$v['lock_id']]['batch_id'] = $v['batch_id'];
            }
        }else{
            $lock_quantity = $lock[0]['lock_quantity'];
        }

        $map = Map::$v;

        $data['detail_id'] = $contractGoods->detail_id;
        $data['goods_id'] = $contractGoods->goods_id;
        $data['project_id'] = $contractGoods->project_id;
        $data['contract_id'] = $contract->contract_id;
        $data['contract_code'] = $contract->contract_code;
        $data['lock_id'] = $lock[0]['lock_id'];
        $data['goods_name'] = $goods->name;
        $data['contract_currency'] = $map['currency'][$contract->currency]['ico'];
        $data['price'] = $data['contract_currency'].' '.number_format($contractGoods->price/100, 2);
        $plus = "";
        if($contractGoods->more_or_less_rate>0){
            $more_or_less_rate = $contractGoods->more_or_less_rate*100;
            $plus = "±".$more_or_less_rate."%";
        }
        $data['contract_quantity'] = number_format($contractGoods->quantity, 2).$map['goods_unit'][$contractGoods->unit]['name'].$plus;
        $data['lock_quantity'] = $lock_quantity>0 ? number_format($lock_quantity, 2).$map['goods_unit'][$lock[0]['unit']]['name'] : '-';
        $data['price_type'] = $map['price_type'][$contract->price_type];
        $data['formula'] = $contract->formula;
        $data['lock_type_name'] = $map['lock_type'][$contractGoods->lock_type];
        $data['lock_type'] = $contractGoods->lock_type;
        $data['type'] = ConstantMap::LOCK_PRICE;
        $targetDetail = BuyLockService::getAllTarget($data['contract_id'], $data['goods_id']);
        $data['target_id'] = $targetDetail[count($targetDetail)-1]['target_id'];
        $data['unit_price'] = $contractGoods->unit_price;
        // $data['unit_store'] = $contractGoods->unit_store;

        $lockArr = array();
        $rollArr = array();
        $targetArr = array();
        $codeArr = array();

        $noticeArr = array();
        if($data['lock_type']==ConstantMap::LOCK_PUT_ORDER){
            // $res = Utility::query("select a.detail_id,b.lock_id,a.batch_id from t_stock_in_batch_detail a left join t_lock_price b on a.batch_id=b.batch_id where a.contract_id=".$data['contract_id']." and a.goods_id=".$data['goods_id']);
            $stockNotice = StockNoticeDetail::model()->with('batch','sub','lock')->findAll("t.contract_id=".$data['contract_id']." and t.goods_id=".$data['goods_id']." and batch.status=".StockNotice::STATUS_SUBMIT);
            foreach ($stockNotice as $key => $notice) {
                $noticeArr[$notice->batch_id]['code'] = $notice->batch->code;
                $noticeArr[$notice->batch_id]['quantity'] += $notice->quantity;
                $noticeArr[$notice->batch_id]['unit'] = $notice->unit;
                $noticeArr[$notice->batch_id]['sub_quantity'] += $notice->sub->quantity;
                $noticeArr[$notice->batch_id]['sub_unit'] = $notice->sub->unit;
                $noticeArr[$notice->batch_id]['lock_quantity'] = $notice->lock->lock_quantity;
            }
        }


        $lockDetail = BuyLockService::getLockDetail($data['contract_id'], $data['goods_id']);
        if(Utility::isNotEmpty($lockDetail)){
            $data['total_price']   = $lockDetail['total_price'];
            $data['total_quantity']= $lockDetail['total_quantity'];
            $data['total_amount']  = $lockDetail['total_amount'];
            unset($lockDetail['total_price']);
            unset($lockDetail['total_quantity']);
            unset($lockDetail['total_amount']);
            $len     = count($lockDetail);
            $lockArr = $lockDetail[$len-1];
            $data['order_index'] = $lockArr['order_index'];

            //获取相同计价标的下的已锁价数量
            foreach ($lockDetail as $key => $value) {
                $targetArr[$value['target_id']]['lock_quantity'] += $value['quantity'];
                $targetArr[$value['target_id']]['month_spread']  = $value['month_spread'];
                $targetArr[$value['target_id']]['rollover_fee']  = $value['rollover_fee']; 
                if($data['lock_type']==ConstantMap::LOCK_PUT_ORDER && count($noticeArr)>0 && count(lockBatch)>0){
                    $codeArr[$value['target_id']]['order_code']  = $noticeArr[$lockBatch[$value['lock_id']]['batch_id']]['code'];
                } 
            }
        }
        $rollDetail = BuyLockService::getRolloverDetail($data['contract_id'], $data['goods_id']);
        if(Utility::isNotEmpty($rollDetail)){
            $len     = count($rollDetail);
            $rollArr = $rollDetail[$len-1];

            //获取相同计价标的下转月数量
            foreach ($rollDetail as $key => $value) {
                $targetArr[$value['target_id']]['roll_quantity'] = $value['quantity'];
                $targetArr[$value['target_id']]['roll_quantity_format'] = number_format($value['quantity'], 2);
                if(!empty($targetArr[$value['target_id']]['lock_quantity'])){
                    $lock_quantity = $targetArr[$value['target_id']]['lock_quantity'];
                    $targetArr[$value['target_id']]['balance_quantity'] = $value['quantity'] - $lock_quantity;
                    $targetArr[$value['target_id']]['balance_quantity_format'] = number_format($targetArr[$value['target_id']]['balance_quantity'], 2);
                    $targetArr[$value['target_id']]['lock_quantity_format'] = number_format($lock_quantity, 2);
                }else{
                    $targetArr[$value['target_id']]['balance_quantity'] = $value['quantity'];
                    $targetArr[$value['target_id']]['balance_quantity_format'] = number_format($value['quantity'], 2);
                }
                $targetArr[$value['target_id']]['month_spread']  = $value['month_spread'];
                $targetArr[$value['target_id']]['rollover_fee']  = $value['rollover_fee'];
                if($data['lock_type']==ConstantMap::LOCK_PUT_ORDER && count($noticeArr)>0 && count(lockBatch)>0){
                    $codeArr[$value['target_id']]['order_code']  = $noticeArr[$lockBatch[$value['lock_id']]['batch_id']]['code'];
                } 
            }
        }
        
        if(!empty($rollArr)){
            $data['currency'] = $rollArr['currency'];
            $data['unit'] = $rollArr['unit'];
            $data['month_spread'] = $rollArr['month_spread'];
            $data['rollover_fee'] = $rollArr['rollover_fee'];
            $data['premium'] = !empty($lockArr['premium']) ? $lockArr['premium'] : 0;
            $data['amount'] = $data['premium'] + $data['month_spread'] + $data['rollover_fee'];
        }else if(empty($rollArr) && !empty($lockArr)){
            $data['currency'] = $lockArr['currency'];
            $data['unit'] = $lockArr['unit'];
            $data['premium'] = $lockArr['premium'];
            $data['month_spread'] = $lockArr['month_spread'];
            $data['rollover_fee'] = $lockArr['rollover_fee'];
            $data['amount'] = $data['premium'] + $data['month_spread'] + $data['rollover_fee'];
        }else{
            $data['currency'] = $contract->currency;
            $data['unit'] = $lock[0]['unit'];
        }

        $data['unit_name'] = $map['goods_unit'][$data['unit']]['name'];

        // print_r($targetArr);die;
        
        $this->pageTitle="操作锁价";
        $this->render("lock",array(
            'data' => $data, 
            'lockDetail'=> $lockDetail,
            'rollDetail'=> $rollDetail,
            'targetDetail'=>$targetDetail,
            'targetArr'=>$targetArr,
            'noticeArr'=>$noticeArr,
            'codeArr'=>$codeArr
            )
        );
    }

    public function actionRollover()
    {
        $id = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("参数错误！", "/buyLock/");
        }

        $contractGoods = ContractGoods::model()->with('contract', 'goods')->findByPk($id);
        // $lock = LockPrice::model()->with('contract', 'goods', 'contractGoods')->findByPk($id);
        if(empty($contractGoods->detail_id) || empty($contractGoods->contract->contract_id) || empty($contractGoods->goods->goods_id))
        {
            $this->renderError("当前信息不存在！", "/buyLock/");
        }

        $contract = $contractGoods->contract;
        $goods = $contractGoods->goods;

        
        $lock = LockPrice::model()->findAllToArray(array("condition"=>"contract_id=".$contract->contract_id." and goods_id=".$goods->goods_id, "order"=>"lock_id asc"));

        $lock_quantity = 0;
        if($contractGoods->lock_type==ConstantMap::LOCK_PUT_ORDER){
            foreach ($lock as $v) {
                $lock_quantity += $v['lock_quantity'];
            }
        }else{
            $lock_quantity = $lock[0]['lock_quantity'];
        }

        $map = Map::$v;

        $data['detail_id'] = $contractGoods->detail_id;
        $data['goods_id'] = $contractGoods->goods_id;
        $data['project_id'] = $contractGoods->project_id;
        $data['contract_id'] = $contract->contract_id;
        $data['contract_code'] = $contract->contract_code;
        $data['lock_id'] = $lock[0]['lock_id'];
        $data['goods_name'] = $goods->name;
        $data['contract_currency'] = $map['currency'][$contract->currency]['ico'];
        $data['price'] = $data['contract_currency'].' '.number_format($contractGoods->price/100, 2);
        $plus = "";
        if($contractGoods->more_or_less_rate>0){
            $more_or_less_rate = $contractGoods->more_or_less_rate*100;
            $plus = "±".$more_or_less_rate."%";
        }
        $data['contract_quantity'] = number_format($contractGoods->quantity, 2).$map['goods_unit'][$contractGoods->unit]['name'].$plus;
        $data['lock_quantity'] = $lock_quantity>0 ? number_format($lock_quantity, 2).$map['goods_unit'][$lock[0]['unit']]['name'] : '-';
        $data['price_type'] = $map['price_type'][$contract->price_type];
        $data['formula'] = $contract->formula;
        $data['lock_type_name'] = $map['lock_type'][$contractGoods->lock_type];
        $data['lock_type'] = $contractGoods->lock_type;
        $data['type'] = ConstantMap::ROLLOVER_MONTH;
        $targetDetail = BuyLockService::getAllTarget($data['contract_id'], $data['goods_id']);
        $data['old_target_id'] = $targetDetail[count($targetDetail)-1]['target_id'];
        $data['old_target_name'] = $targetDetail[count($targetDetail)-1]['name'];
        $data['unit_price'] = $contractGoods->unit_price;
        // $data['unit_store'] = $contractGoods->unit_store;

        $lockArr = array();
        $rollArr = array();
        $targetArr = array();


        $lockDetail = BuyLockService::getLockDetail($data['contract_id'], $data['goods_id']);
        if(Utility::isNotEmpty($lockDetail)){
            unset($lockDetail['total_price']);
            unset($lockDetail['total_quantity']);
            unset($lockDetail['total_amount']);
            $len     = count($lockDetail);
            $lockArr = $lockDetail[$len-1];

            //获取相同计价标的下的已锁价数量
            foreach ($lockDetail as $key => $value) {
                $targetArr[$value['target_id']]['lock_quantity'] += $value['quantity'];
            }
        }
        $rollDetail = BuyLockService::getRolloverDetail($data['contract_id'], $data['goods_id']);
        if(Utility::isNotEmpty($rollDetail)){
            $len     = count($rollDetail);
            $rollArr = $rollDetail[$len-1];
        }
        
        if(!empty($rollArr)){
            $data['currency'] = $rollArr['currency'];
            $data['unit'] = $rollArr['unit'];
            $data['order_index'] = $rollArr['order_index'];
        }else if(empty($rollArr) && !empty($lockArr)){
            $data['currency'] = $lockArr['currency'];
            $data['unit'] = $lockArr['unit'];
        }else{
            $data['currency'] = $contract->currency;
            $data['unit'] = $lock[0]['unit'];
        }

        $noticeArr = array();
        if($contractGoods->lock_type==ConstantMap::LOCK_PUT_ORDER){
            // $res = Utility::query("select a.detail_id,b.lock_id,a.batch_id from t_stock_in_batch_detail a left join t_lock_price b on a.batch_id=b.batch_id where a.contract_id=".$data['contract_id']." and a.goods_id=".$data['goods_id']);
            $stockNotice = StockNoticeDetail::model()->with('batch','sub','lock')->findAll("t.contract_id=".$data['contract_id']." and t.goods_id=".$data['goods_id']." and batch.status=10");
            // print_r($stockNotice[0]);die;
            foreach ($stockNotice as $key => $notice) {
                $noticeArr[$notice->batch_id]['code'] = $notice->batch->code;
                $noticeArr[$notice->batch_id]['quantity'] += $notice->quantity;
                $noticeArr[$notice->batch_id]['unit'] = $notice->unit;
                $noticeArr[$notice->batch_id]['sub_quantity'] += $notice->sub->quantity;
                $noticeArr[$notice->batch_id]['sub_unit'] = $notice->sub->unit;
                $noticeArr[$notice->batch_id]['lock_quantity'] = $notice->lock->lock_quantity;
            }
        }
        
        
        // print_r($rollDetail);die;
        $this->pageTitle="操作转月";
        $this->render("rollover",array(
            'data' => $data, 
            'lockDetail'=> $lockDetail,
            'rollDetail'=> $rollDetail,
            'targetArr'=>$targetArr,
            'noticeArr'=>$noticeArr
            )
        );
    }

    public function actionDetail()
    {
        $id = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("参数错误！", "/buyLock/");
        }

        $contractGoods = ContractGoods::model()->with('contract', 'goods')->findByPk($id);
        // $lock = LockPrice::model()->with('contract', 'goods', 'contractGoods')->findByPk($id);
        if(empty($contractGoods->detail_id) || empty($contractGoods->contract->contract_id) || empty($contractGoods->goods->goods_id))
        {
            $this->renderError("当前信息不存在！", "/buyLock/");
        }

        $contract = $contractGoods->contract;
        $goods = $contractGoods->goods;

        
        $lock = LockPrice::model()->findAllToArray(array("condition"=>"contract_id=".$contract->contract_id." and goods_id=".$goods->goods_id, "order"=>"lock_id asc"));
        if($contractGoods->lock_type==ConstantMap::LOCK_PUT_ORDER){
            foreach ($lock as $v) {
                $lock_quantity += $v['lock_quantity'];
            }
        }else{
            $lock_quantity = $lock[0]['lock_quantity'];
        }

        $map = Map::$v;

        $data['detail_id'] = $contractGoods->detail_id;
        $data['goods_id'] = $contractGoods->goods_id;
        $data['project_id'] = $contractGoods->project_id;
        $data['contract_id'] = $contract->contract_id;
        $data['contract_code'] = $contract->contract_code;
        $data['lock_id'] = $lock[0]['lock_id'];
        $data['goods_name'] = $goods->name;
        $data['contract_currency'] = $map['currency'][$contract->currency]['ico'];
        $data['price'] = $data['contract_currency'].' '.number_format($contractGoods->price/100, 2);
        $plus = "";
        if($contractGoods->more_or_less_rate>0){
            $more_or_less_rate = $contractGoods->more_or_less_rate*100;
            $plus = "+".$more_or_less_rate."%";
        }
        $data['contract_quantity'] = number_format($contractGoods->quantity, 2).$map['goods_unit'][$contractGoods->unit]['name'].$plus;
        $data['lock_quantity'] = $lock_quantity>0 ? number_format($lock_quantity, 2).$map['goods_unit'][$lock[0]['unit']]['name'] : '-';
        $data['price_type'] = $map['price_type'][$contract->price_type];
        $data['formula'] = $contract->formula;
        $data['lock_type_name'] = $map['lock_type'][$contractGoods->lock_type];
        $data['lock_type'] = $contractGoods->lock_type;
        $data['type'] = ConstantMap::LOCK_PRICE;

        $lockArr = array();
        $rollArr = array();
        $targetArr = array();


        $lockDetail = BuyLockService::getLockDetail($data['contract_id'], $data['goods_id']);
        if(Utility::isNotEmpty($lockDetail)){
            $data['total_price']   = $lockDetail['total_price'];
            $data['total_quantity']= $lockDetail['total_quantity'];
            $data['total_amount']  = $lockDetail['total_amount'];
            unset($lockDetail['total_price']);
            unset($lockDetail['total_quantity']);
            unset($lockDetail['total_amount']);

            //获取相同计价标的下的已锁价数量
            foreach ($lockDetail as $key => $value) {
                $targetArr[$value['target_id']]['lock_quantity'] += $value['quantity'];
            }
        }
        $rollDetail = BuyLockService::getRolloverDetail($data['contract_id'], $data['goods_id']);
        // print_r($data);die;

        $this->pageTitle="查看详情";
        $this->render("detail",array(
            'data' => $data, 
            'lockDetail'=> $lockDetail,
            'rollDetail'=> $rollDetail,
            'targetArr'=>$targetArr,
            )
        );
    }

    public function actionSave()
    {
        $params = Mod::app()->request->getParam('data');
        $paramsCheckRes = BuyLockService::checkParamsValid($params, $params['type'], $params['lock_type']);
        if ($paramsCheckRes !== true) {
            $this->returnError($paramsCheckRes);
        }

        if($params['type']==ConstantMap::ROLLOVER_MONTH){
            $target = ContractGoodsTarget::model()->find("contract_id=".$params['contract_id']." and goods_id=".$params['goods_id']." and name='".$params['target_name']."'");
            if(!empty($target->target_id)){
                $this->returnError("当前计价标的已经存在，不能重复添加！");
            }
        }

        // print_r($params);die;
        $detail_id = $params['detail_id'];
        unset($params['detail_id']);
        $order_index = $params['order_index']+1;

        if($params['type']==ConstantMap::LOCK_PRICE){
            
            $obj = new LockPriceDetail();
            if($params['lock_type']==ConstantMap::LOCK_TYPE_CONTRACT){
                $obj->lock_code = $params['contract_code']."_SJ".$order_index;
            }else{
                $lock = LockPrice::model()->find("batch_id=".$params['batch_id']);
                $params['lock_id'] = $lock->lock_id;
                $obj->lock_code = $params['order_code']."_SJ".$order_index;
            }
            $obj->status = LockPriceDetail::STATUS_SAVED;
            $logData = array('remark' => '添加锁价', 'model_name' => 'LockPriceDetail');
        }else{
            $obj = new ContractGoodsRollover();
            if($params['lock_type']==ConstantMap::LOCK_TYPE_CONTRACT){
                $obj->rollover_code = $params['contract_code']."_ZY".$order_index;
            }else{
                $lock = LockPrice::model()->find("batch_id=".$params['batch_id']);
                $params['lock_id'] = $lock->lock_id;
                $obj->rollover_code = $params['order_code']."_ZY".$order_index;
            }
            $obj->status = ContractGoodsRollover::STATUS_SAVED;
            $logData = array('remark' => '添加转月', 'model_name' => 'ContractGoodsRollover');
        }
        // print_r($params);die;

        $obj->setAttributes($params, false);

        $nowUserId  = Utility::getNowUserId();
        $nowTime    = new CDbExpression("now()");
        
        $obj->order_index       = $order_index;
        $obj->status_time       = $nowTime;
        $obj->create_time       = $nowTime;
        $obj->create_user_id    = $nowUserId;
        $obj->update_time       = $nowTime;
        $obj->update_user_id    = $nowUserId;

        $trans = Utility::beginTransaction();
        try {
            
            $goods  = Goods::model()->findByPk($params['goods_id']);
            $r      = ContractGoodsService::checkGoodsPriceUnit($params["contract_id"],$params["goods_id"],$params['unit']);
            if(!$r)
                $this->returnError("商品'".$goods->name."'的数量单位与现有数据不一致，重新检查后重新填写");
            
            if($params['type']==ConstantMap::LOCK_PRICE){
                BuyLockService::lockPrice($params['lock_id'], $params['quantity'], $order_index, $params['unit']);
            }else{
                //保存计价标的
                $target = new ContractGoodsTarget();
                $target->project_id     = $params['project_id'];
                $target->contract_id    = $params['contract_id'];
                $target->goods_id       = $params['goods_id'];
                $target->lock_id        = $params['lock_id'];
                $target->name           = trim($params['target_name']);
                $target->status = 1;
                $target->status_time    = $nowTime;
                $target->create_user_id = $nowUserId;
                $target->create_time    = $nowTime;
                $target->update_time    = $nowTime;
                $target->update_user_id = $nowUserId;
                $target->save();

                $obj->target_id = $target->target_id;
            }


            $obj->save();

            $trans->commit();
            $logData['content'] = null;
            $logData['object_id'] = $params['type']==ConstantMap::LOCK_PRICE ? $obj->detail_id : $obj->rollover_id;
            Utility::addActionLog($logData);
            $this->returnSuccess($detail_id);
            
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$LOCK_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }
        
    }
}