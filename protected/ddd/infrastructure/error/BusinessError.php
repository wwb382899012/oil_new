<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 18:02
 * Describe：
 *  业务错误码定义，8位数字，前三位区分不同大模块，第四五两位区分小模块，最后三位不同的错误信息
 *      100 - 系统通用
 *              - 00 系统
 *              - 01 参数校验
 *              - 02 表单校验
 *              - 03 操作
 *              - 04 审核
 *      101 - 项目合同相关
 *              - 00 项目
 *              - 01 合同
 *              - 02 锁价
 *      102 - 出入库相关
 *              - 01 入库
 *              - 02 出库
 *              - 03 借还货
 *              - 04 库存相关
 *      103 - 收付款相关
 *      104 - 融资模块相关
 *      105 - 风控准入相关
 *      106 - 发票相关
 *      107 - 文件上传
 *      108 - 合同结算
 *      109 - 合同拆分
 *
 */

namespace ddd\infrastructure\error;


class BusinessError
{
    /**
     * 数据模型不存在
     */
    const MODEL_NOT_EXISTS=1001;

    const MODEL_SAVE_FALSE=1002;

    const MODEL_DELETE_FALSE=1003;

    /**
     * 实体对象不存在
     */
    const ENTITY_NOT_EXISTS=2001;


    const Argument_Required=array("10000001","{name}不得为空");
    const Argument_Invalid=array("10000002","{name}不得为空");
    const Form_Item_Required=array("10002001","{name}不得为空");
    const Readonly_Property=array("10002002","{name}为只读");
    const Operate_Error = array("10003001","操作失败，失败原因：{reason}");

    const Check_Detail_Not_Exist = array("10004001","审核信息不存在！审核信息ID:{detail_id}");
    const Check_Obj_Empty = array("10004002","审核对象为空");
    const Check_Obj_Not_Exist = array("10004003","审核对象不存在");
    const Obj_Check_Detail_Not_Exist = array("10004004","对象:{obj_id}审核信息不存在");

    #region 101

    const Project_Goods_Is_Exists=array("10100102","项目交易商品已经包含了商品{goods_id}");

    const Contract_Goods_Is_Exists=array("10101301","合同{contract_code}已经包含了商品{goods_id}");
    const Contract_Cannot_Submit=array("10101302","当前状态的合同不能进行提交操作！");
    const Contract_Cannot_Risk_Back=array("10101303","当前合同进行风控驳回！");
    const Contract_Not_Exists=array("10101304","合同不存在，合同ID：{contract_id}");



    #endregion

    #region 102

    const Contract_Cannot_Lading=array("10201001","合同{contract_code}当前还不能发起提单");
    const Lading_Goods_Is_Exists=array("10201002","提单已经包含了商品{goods_id}");
    const Lading_Goods_Sub_Unit_Difference=array("10201003","提单商品{goods_id}的第二单位异常");
    const Lading_Bill_Cannot_Settle=array("10201004","入库通知单{batch_id}当前还不能发起结算");
    const Contract_Cannot_Create_DeliveryOrder=array("10201005","合同{contract_code}当前还不能创建发货单");
    const Delivery_Order_Goods_Is_Exists=array("10201006","发货单已经包含了商品{goods_id}");
    // const Delivery_Order_Cannot_Settle=array("10201005","发货单{order_id}当前还不能发起结算");
    const Stock_Notice_Not_Allow_Trash=array("10201007","入库通知单不允许作废");
    const Stock_Notice_Not_Allow_Edit=array("10201008","入库通知单不允许编辑");
    const Stock_Notice_Not_Allow_Submit=array("10201009","入库通知单不允许提交");

    const Stock_In_Goods_Is_Exists=array("10201010","入库单已经包含了商品{goods_id}");

    const Delivery_Order_Not_Allow_Edit = array("10201030","当前发货单不允许编辑");
    const Delivery_Order_Not_Allow_Trash = array("10201031","当前发货单不允许作废");
    const Delivery_Order_Not_Allow_Submit = array("10201032","当前发货单不允许提交");
    const Delivery_Order_Not_Allow_Revocation = array("10201033","当前发货单不允许撤回");
    const Delivery_Order_Not_Allow_Reject = array("10201034","当前发货单不允许驳回");
    const Delivery_Order_Not_Allow_Approve = array("10201035","当前发货单不允许审核");

    const Stock_Out_Not_Allow_Edit = array("10201050","当前出库单不允许编辑");
    const Stock_Out_Not_Allow_Trash = array("10201031","当前出库单不允许作废");
    const Stock_Out_Not_Allow_Submit = array("10201032","当前出库单不允许提交");
    const Stock_Out_Not_Allow_Revocation = array("10201033","当前出库单不允许撤回");
    const Stock_Out_Not_Allow_Reject = array("10201034","当前出库单不允许驳回");
    const Stock_Out_Not_Allow_Approve = array("10201035","当前出库单不允许审核");

    const Stock_In_Not_Allow_Edit = array("10201060","当前入库单不允许编辑");
    const Stock_In_Not_Allow_Trash = array("10201031","当前入库单不允许作废");
    const Stock_In_Not_Allow_Submit = array("10201032","当前入库单不允许提交");
    const Stock_In_Not_Allow_Revocation = array("10201033","当前入库单不允许撤回");
    const Stock_In_Not_Allow_Reject = array("10201034","当前入库单不允许驳回");
    const Stock_In_Not_Allow_Approve = array("10201035","当前入库单不允许审核");


    //04 库存相关
    const Stock_Quantity_Balance_Not_Enough=array("10204001","当前可用库存：{balance},需处理库存：{quantity},可用库存不足");
    const Stock_Frozen_Quantity_Not_Enough=array("10204002","当前已冻结库存：{frozen},需解冻库存：{quantity},可解冻库存不足");
    const Stock_Quantity_Out_Not_Enough=array("10204003","当前已出库量：{out},需处理量：{quantity},数量不足");
    #endregion


    #region 108

    const Buy_Contract_Cannot_Settle=array("10801001","当前状态不能发起采购合同{contract_code}结算");
    const Lading_Cannot_Settle=array("10801002","当前状态不能发起入库通知单{code}结算");
    const Sale_Contract_Cannot_Settle=array("10801003","当前状态不能发起销售合同{contract_code}结算");
    const Delivery_Order_Cannot_Settle=array("10801004","当前状态不能发起发货单{code}结算");
    const Other_Expense_Settlement_Subject_Is_Exists=array("10801005","非货款结算明细已经包含了科目{subject_id}");
    const Tax_Subject_Is_Exists=array("10801006","税收明细已经包含了税收名目{tax_id}");
    const Other_Expense_Subject_Is_Exists=array("10801007","其他费用明细已经包含了科目{subject_id}");
    const Now_Settle_Mode_Is_Buy_Contract_Settle=array("10801008","当前结算方式是按采购合同{contract_code}合并结算");
    const Now_Settle_Mode_Is_Sale_Contract_Settle=array("10801009","当前结算方式是按销售合同{contract_code}合并结算");
    #endregion

    #region 109

    const Contract_Cannot_Split_Apply=array("10901001","当前合同{contract_code}不能发起合同平移申请");
    const Contract_Split_Goods_Is_Exists=array("10901002","合同平移已经包含了商品{goods_id}");
    const Stock_Bill_Cannot_Split=array("10901003","当前单据{bill_code}不能进行平移");
    const Stock_Split_Goods_Is_Exists=array("10901004","出入库平移已经包含了商品{goods_id}");
    const Stock_Split_Is_Exists=array("10901005","合同平移已经包含了该出入库平移{bill_code}");
    const Contract_Split_Apply_Cannot_Edit=array("10901006","当前状态的合同平移申请不能进行编辑操作");
    const Contract_Split_Apply_Cannot_Submit=array("10901007","当前状态的合同平移申请不能进行提交操作");
    const Contract_split_Apply_Flow_Error = array("10901008", "合同平移申请审批流生成失败");
    const Contract_split_Apply_Not_Exists = array("10901009", "合同平移申请:{apply_id}不存在");
    const Contract_split_Apply_Not_Allow_Submit = array("10901010", "当前状态的合同平移申请不允许提交");
    const Contract_split_Apply_Not_Allow_Check = array("10901011", "当前状态的合同平移申请不允许审核");
    const Contract_split_Apply_Not_Allow_Check_Back = array("10901012", "当前状态的合同平移申请不允许驳回");
    const Contract_split_Apply_Not_Allow_Trash = array("10901013", "当前状态的合同平移申请不允许废弃");



    const Contract_Cannot_Terminate_Stock_In=array("10303001","该合同下存在未审核通过的入库单，不能进行终止操作");
    const Contract_Cannot_Terminate_Stock_Out=array("10303002","该合同下存在未审核通过的出库单，不能进行终止操作");
    const Contract_Cannot_Terminate=array("10303003","当前合同不能进行终止操作");
    const Contract_Terminate_Cannot_Edit=array("10303004","当前合同终止不允许编辑");
    const Contract_Terminate_Cannot_Submit=array("10303005","当前合同终止不允许提交");

    #endregion


    const Stock_Split_Not_Allow_Edit = array("20201030","当前出入库拆分不允许编辑");
    const Stock_Split_Not_Allow_Submit = array("20201032","当前出入库拆分不允许提交");
    const Stock_Split_Not_Allow_Reject = array("20201033","当前出入库拆分不允许驳回");
    const Stock_Split_Not_Allow_Approve = array("20201034","当前出入库拆分不允许审核");

    #region 110
    const Settle_Goods_Is_Exists=array("10101301","发货单{order_id}已经包含了商品{goods_id}");
    #endregion

    #region 111
    const Delivery_Settlement_Goods_Is_Exists=array("10111301","发货单结算{order_id}已经包含了商品{goods_id}");
    #endregion
}