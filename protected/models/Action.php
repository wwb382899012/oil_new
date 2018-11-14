<?php

/**
 * Created by youyi000.
 * DateTime: 2016/8/15 16:56
 * Describe：
 */
class Action extends BaseActiveRecord
{

    const ACTION_1=1;   //合作方准入审核
    const ACTION_2=2;   //风控合作方初审
    const ACTION_3=3;   //现场风控
    const ACTION_4=4;   //风控会议评审
    const ACTION_5=5;
    const ACTION_6=6;
    const ACTION_7=7;
    const ACTION_8=8;
    const ACTION_9=9;    //项目发起
    const ACTION_10=10;  //商务确认
    const ACTION_11=11;
    const ACTION_12=12;
    const ACTION_13=13;
    const ACTION_14=14;
    const ACTION_15=15;
    const ACTION_16=16;
    const ACTION_17=17;
    const ACTION_18=18;
    const ACTION_19=19;
    const ACTION_20=20;
    const ACTION_21=21;
    const ACTION_22=22;
    const ACTION_23=23;
    const ACTION_24=24;
    const ACTION_PAY_APPLICATION_BACK=25;//付款申请退回待修改
    const ACTION_26=26;
    const ACTION_27=27;
    const ACTION_28=28;
    const ACTION_29=29;
    const ACTION_30=30;
    const ACTION_31=31; //合同审核退回待修改
    const ACTION_32=32; //入库单审核驳回待修改
    const ACTION_33=33; //发货单审核退回待修改
    //const ACTION_34=34; //保理申请审核退回待修改
    const ACTION_35=35; //保理申请驳回待修改
    const ACTION_36=36;
    const ACTION_37=37;
    const ACTION_STOCK_INVENTORY_CHECK = 38; //库存盘点审核
    const ACTION_STOCK_INVENTORY_BACK = 39; //库存盘点审核驳回待修改
    const ACTION_CROSS_CHECK_BACK=40;//调货单审核退回事件
    const ACTION_CROSS_RETURN_CHECK_BACK=41;//调货处理（还货）审核退回事件
    const ACTION_PROJECT_BACK=42;//商务项目驳回
    const ACTION_STOCK_BATCH_SETTLE_BACK=43;//入库通知单结算审核退回待修改
    const ACTION_44=44;//风控初审驳回
    const ACTION_45=45; // 入库通知单结算审核
    const ACTION_FACTOR_AMOUNT_CONFIRM=46; //保理对接款确认
    const ACTION_47=47;//发货单结算审核
    const ACTION_48=48;//发货单结算驳回
    const ACTION_ACTUAL_PAY=49; //付款实付
    const ACTION_FACTOR_APPLY=50; //保理申请
    const ACTION_STOP_BACK=51; //付款止付驳回
    const ACTION_STOP_CHECK=52; //付款止付审核
    const ACTION_STOCK_OUT_WAIT_CHECK = 53; //出库单审核
    const ACTION_STOCK_OUT_CHECK_BACK = 54; //出库单审核退回待修改
    const ACTION_STOCK_CONTRACT_SETTLEMENT_CHECK = 55; //采购合同结算审核
    const ACTION_STOCK_CONTRACT_SETTLEMENT_BACK = 56; //采购合同结算审核驳回待修改
    const ACTION_DELIVERY_CONTRACT_SETTLEMENT_CHECK = 57; //销售合同结算审核
    const ACTION_DELIVERY_CONTRACT_SETTLEMENT_BACK = 58; //销售合同结算审核驳回待修改
    const ACTION_CONTRACT_SPLIT_CHECK = 59; //合同平移审核
    const ACTION_CONTRACT_SPLIT_BACK = 60; //合同平移审核驳回待修改
    const ACTION_STOCK_IN_SPLIT_CHECK = 61; //入库平移审核
    const ACTION_STOCK_IN_SPLIT_CHECK_BACK = 62; //入库平移审核驳回待修改
    const ACTION_CONTRACT_TERMINATE_CHECK=63;//合同终止审核
    const ACTION_CONTRACT_TERMINATE_BACK=64;//合同终止审核驳回待修改
    const ACTION_CONTRACT_SPLIT_BUSINESS_CONFIRM = 65;  //合同平移发起的商务确认
    const ACTION_STOCK_OUT_SPLIT_CHECK = 66; //出库平移审核
    const ACTION_STOCK_OUT_SPLIT_CHECK_BACK = 67; //出库平移审核驳回待修改

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_action';
    }
    public function relations()
    {
        return array(
            "tasks" => array(self::HAS_MANY, "Task", "action_id"),

        );
    }

}
