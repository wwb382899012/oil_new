<?php
/**
 * Describe：调货单审核
 */
class Check11Controller extends CheckController
{

    public function pageInit()
    {
        parent::pageInit();
        $this->filterActions="";
        $this->businessId=11;
        $this->rightCode = "check11_";
        $this->checkButtonStatus["reject"]=0;
    }

    public function initRightCode()
    {
        $attr= $_REQUEST["search"];
        $checkStatus=$attr["checkStatus"];
        $this->treeCode="check11_".$checkStatus;

    }

    protected function getExtraCheckItems()
    {
        $items=json_decode($_POST["items"],true);
        return $items;
    }


    public function actionIndex()
    {
        $attr = $_REQUEST[search];
        
        if(!empty($attr["checkStatus"]))
        {
            $checkStatus=$attr["checkStatus"];
            unset($attr["checkStatus"]);
        }
        $user = SystemUser::getUser($this->nowUserId);

        $sql="
                 select {col} from t_check_detail a
                 left join t_cross_order o on a.obj_id=o.cross_id
                 left join t_goods g on o.goods_id=g.goods_id
                 left join t_contract c on o.contract_id=c.contract_id
                 left join t_partner pa on c.partner_id=pa.partner_id
                 left join t_project p on o.project_id=p.project_id
                 left join t_system_user s on c.manager_user_id=s.user_id
                 left join t_check_item i on i.check_id=a.check_id and i.node_id>0 
                 left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1 
                ".$this->getWhereSql($attr)." and a.business_id=".$this->businessId."
                and (a.role_id=".$this->nowUserRoleId." or a.check_user_id=".$this->nowUserId.")";

        $fields="a.detail_id,o.cross_code,c.contract_id,c.contract_code,g.goods_id,g.name as goods_name,
                pa.partner_id,pa.name as partner_name,p.project_id,p.project_code,cf.code_out";

        switch($checkStatus)
        {
            case 2:
                $sql .= " and a.status=1 and a.check_status=1 ";
                $fields.=",0 isCanCheck, 2 as checkStatus ";
                break;
            case 3:
                $sql .= " and a.status=1 and a.check_status=0";
                $fields.=",0 isCanCheck, 3 as checkStatus ";
                break;
            case 4:
                $sql .= " and a.status=1 and a.check_status=-1";
                $fields.=",0 isCanCheck, 4 as checkStatus ";
                break;
            default:
                $sql .= " and a.status=0";
                $fields.=",1 isCanCheck, 1 as checkStatus ";
                break;
        }

        $sql .= " and c.corporation_id in (".$user['corp_ids'].")  order by a.detail_id desc";
        $data = $this->queryTablesByPage($sql,$fields);

        $attr["checkStatus"]=$checkStatus;
        $data["search"]=$attr;
        $data["b"]=$this->businessId;
        $this->render('index',$data);
    }

    public function actionCheck()
    {
        $id    = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("参数有误！", "/check11/");
        $checkDetail = CheckDetail::model()->findByPk($id);
        if(empty($checkDetail->detail_id))
            $this->renderError("当前信息不存在！", "/check11/");

        $crossOrder = CrossOrder::model()->with('contract', 'goods', 'project', 'contractGoods')->findByPk($checkDetail->obj_id);
        $contract   = $crossOrder->contract;
        $goods      = $crossOrder->goods;
        $project    = $crossOrder->project;
        $contractGoods = $crossOrder->contractGoods;

        $partner = Partner::model()->findByPk($contract->partner_id);
        $corporation = Corporation::model()->findByPk($contract->corporation_id);

        $map    = Map::$v;
        $contract_unit   = $map['goods_unit'][$contractGoods->unit]['name'];
        $store_unit      = $map['goods_unit'][$contractGoods->unit_store]['name'];
        $data['detail_id']      = $id;
        $data['contract_id']    = $contract->contract_id;
        $data['contract_code']  = $contract->contract_code;
        $data['goods_id']       = $goods->goods_id;
        $data['goods_name']     = $goods->name;
        $data['project_id']     = $project->project_id;
        $data['project_code']   = $project->project_code;
        $data['partner_id']     = $partner->partner_id;
        $data['partner_name']   = $partner->name;
        $data['corporation_id']     = $corporation->corporation_id;
        $data['corporation_name']   = $corporation->name;

        $plus = "";
        if($contractGoods->more_or_less_rate>0){
            $more_or_less_rate = $contractGoods->more_or_less_rate*100;
            $plus = "+".$more_or_less_rate."%";
        }
        $data['contract_quantity'] = number_format($contractGoods->quantity, 2).$contract_unit.$plus;
        $delivery_quantity = CrossOrderService::getAllocateTotal($data['contract_id'], $data['goods_id']);
        $data['delivery_quantity'] = !empty($delivery_quantity) ? number_format($delivery_quantity,2).$store_unit : '-';

        $out_quantity = CrossOrderService::getOutTotal($data['contract_id'], $data['goods_id']);
        $data['out_quantity'] = !empty($out_quantity) ? number_format($out_quantity,2).$store_unit : '-';

        $order = CrossOrderService::getAllOrderDetail($data['contract_id'], $data['goods_id']);
        // print_r($order);die;
        if(Utility::isEmpty($order)){
            $this->renderError("当前调货信息不存在！", "/cross/");
        }

        $goodsItems = array();
        $crossDetail= array();
        $total_quantity = 0;
        $total_quantity_out = 0;
        foreach ($order as $key => $value) {
            if($value['cross_id']==$checkDetail->obj_id)
                $goodsItems[] = $value;
            $total_quantity += $value['quantity']; 
            $total_quantity_out += $value['quantity_out']; 
            $crossDetail['detail'][$key] = $value;
        }
        if(!empty($crossDetail)){
            $crossDetail['total_quantity']      = $total_quantity;
            $crossDetail['total_quantity_out']  = $total_quantity_out;
            $crossDetail['unit']                = $contractGoods->unit_store;
        }
        // print_r($goodsItems);die;

        $nowDetail = array();
        foreach ($goodsItems as $key => $value) {
            $total_cross_quantity += $value['quantity'];
            $total_balance_quantity += $value['quantity_balance'];
            $nowDetail['detail'][] = $value;
        }
        $nowDetail['total_quantity'] = $total_cross_quantity;
        $nowDetail['total_balance_quantity'] = $total_balance_quantity;
        $nowDetail['unit'] = $goodsItems[0]['unit'];

        $data['cross_id']   = $goodsItems[0]['cross_id'];
        $data['cross_date'] = $goodsItems[0]['cross_date'];
        $data['reason']     = $goodsItems[0]['remark'];
        $data['check_id']   = $checkDetail->check_id;
        
        // print_r($data);die;
        $this->pageTitle="调货单审核";
        $this->render('check',array(
            'data'=>$data,
            'crossDetail'=> $crossDetail,
            'nowDetail'=>$nowDetail,
            'max_cross_id'=>$crossOrder->cross_id
            )
        );
    }


    public function actionDetail()
    {
        $id    = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("参数有误！", "/check11/");
        $checkDetail = CheckDetail::model()->findByPk($id);
        if(empty($checkDetail->detail_id))
            $this->renderError("当前信息不存在！", "/check11/");

        $crossOrder = CrossOrder::model()->with('contract', 'goods', 'project', 'contractGoods')->findByPk($checkDetail->obj_id);
        $contract   = $crossOrder->contract;
        $goods      = $crossOrder->goods;
        $project    = $crossOrder->project;
        $contractGoods = $crossOrder->contractGoods;

        $partner = Partner::model()->findByPk($contract->partner_id);
        $corporation = Corporation::model()->findByPk($contract->corporation_id);

        $map    = Map::$v;
        $contract_unit   = $map['goods_unit'][$contractGoods->unit]['name'];
        $store_unit      = $map['goods_unit'][$contractGoods->unit_store]['name'];
        $data['detail_id']      = $id;
        $data['contract_id']    = $contract->contract_id;
        $data['contract_code']  = $contract->contract_code;
        $data['goods_id']       = $goods->goods_id;
        $data['goods_name']     = $goods->name;
        $data['project_id']     = $project->project_id;
        $data['project_code']   = $project->project_code;
        $data['partner_id']     = $partner->partner_id;
        $data['partner_name']   = $partner->name;
        $data['corporation_id']     = $corporation->corporation_id;
        $data['corporation_name']   = $corporation->name;

        $plus = "";
        if($contractGoods->more_or_less_rate>0){
            $more_or_less_rate = $contractGoods->more_or_less_rate*100;
            $plus = "+".$more_or_less_rate."%";
        }
        $data['contract_quantity'] = number_format($contractGoods->quantity, 2).$contract_unit.$plus;
        $delivery_quantity = CrossOrderService::getAllocateTotal($data['contract_id'], $data['goods_id']);
        $data['delivery_quantity'] = !empty($delivery_quantity) ? number_format($delivery_quantity,2).$store_unit : '-';

        $out_quantity = CrossOrderService::getOutTotal($data['contract_id'], $data['goods_id']);
        $data['out_quantity'] = !empty($out_quantity) ? number_format($out_quantity,2).$store_unit : '-';

        $order = CrossOrderService::getAllOrderDetail($data['contract_id'], $data['goods_id']);
        // print_r($order);die;
        if(Utility::isEmpty($order)){
            $this->renderError("当前调货信息不存在！", "/cross/");
        }

        $goodsItems = array();
        $crossDetail= array();
        $total_quantity = 0;
        $total_quantity_out = 0;
        foreach ($order as $key => $value) {
            if($value['cross_id']==$checkDetail->obj_id)
                $goodsItems[] = $value;
            $total_quantity += $value['quantity']; 
            $total_quantity_out += $value['quantity_out']; 
            $crossDetail['detail'][$key] = $value;
        }
        if(!empty($crossDetail)){
            $crossDetail['total_quantity']      = $total_quantity;
            $crossDetail['total_quantity_out']  = $total_quantity_out;
            $crossDetail['unit']                = $contractGoods->unit_store;
        }
        // print_r($goodsItems);die;

        $nowDetail = array();
        foreach ($goodsItems as $key => $value) {
            $total_cross_quantity += $value['quantity'];
            $total_balance_quantity += $value['quantity_balance'];
            $nowDetail['detail'][] = $value;
        }
        $nowDetail['total_quantity'] = $total_cross_quantity;
        $nowDetail['total_balance_quantity'] = $total_balance_quantity;
        $nowDetail['unit'] = $goodsItems[0]['unit'];

        $data['cross_id']   = $goodsItems[0]['cross_id'];
        $data['cross_date'] = $goodsItems[0]['cross_date'];
        $data['reason']     = $goodsItems[0]['remark'];
        $log = CheckLog::model()->find("detail_id=".$checkDetail->detail_id);
        $data['remark']     = $log->remark;
        $data['check_id']   = $checkDetail->check_id;
        
        // print_r($data);die;
        $this->pageTitle="调货单详情";
        $this->render('detail',array(
            'data'=>$data,
            'crossDetail'=> $crossDetail,
            'nowDetail'=>$nowDetail
            )
        );
    }
}