<?php

/**
 * Desc: 石油系统错误码
 * User: susiehuang
 * Date: 2017/5/8 0008
 * Time: 17:54
 */
class OilError {
    //100001001 非业务错误码
    public static $NOT_FIND = array(100011001, '${key} not find ${msg}');
    public static $NOT_IN_VALUE = array(100011002, '${key} not in values');
    public static $NOT_INT_ERR = array(100011003, '${str}:输入非法，请输入整数');
    public static $FEN_TO_YUAN_ERR = array(100011004, '${fen} not fen');
    public static $NOT_STANDARD_YUAN = array(100011005, '${yuan}不是正规的元单位');
    public static $YUAN_TO_FEN_ERR = array(100011006, '${yuan} to fen larger than ${num}');
    public static $UNSUPPORT_COMPUTER = array(100011007, '不支持该机器');
    public static $REQUIRED_PARAMS_CHECK_ERROR = array(100011008, '*号标注字段不得为空！');
    public static $PARAMS_PASS_ERROR = array(100011009, '参数传入错误，请检查！');
    public static $OPERATE_FAILED = array(100011010, '操作失败:${reason}！');
    public static $APPROVAL_INFO_NOT_EXIST = array(100011011, '审批信息不存在，审批ID:${check_id}！');
    public static $GOODS_UNIT_CHANGED = array(100011012, '商品${goods_id}的计价单位与已现有数据不一致，重新检查后重新填写！');
    public static $CORPORATION_NOT_SELECTED = array(100011013, '请选择交易主体！');
    public static $PARTNER_NOT_SELECTED = array(100011014, '请选择合作方！');
    public static $CHECK_DETAIL_NOT_EXIST = array(100011015, '审批明细不存在，明细ID:${detail_id}！');
    public static $TRANSACTION_DETAIL_GOODS_NAME_REPEAT = array(100011016, '交易明细品名不得重复！');
    public static $SYSTEM_BUSY = array(100011017, '系统繁忙，稍后重试！');

    //100002001 项目发起相关错误码
    public static $PROJECT_LAUNCH_NOT_TRANSACTION = array(100002001, '请填写交易明细');
    public static $PROJECT_NOT_ALLOW_EDIT = array(100002002, '当前状态的项目不允许修改');
    public static $PROJECT_SAVE_ADD_ERROR = array(100002003, '项目信息保存失败:${reason}');
    public static $TRANSACTION_REQUIRED_PARAMS_CHECK_ERROR = array(100002004, '交易明细中*号标注字段不得为空！');
    public static $TRANSACTION_PURCHASE_AMOUNT_ERROR = array(100002005, '交易明细采购总价有误！');
    public static $TRANSACTION_SALE_AMOUNT_ERROR = array(100002006, '交易明细销售总价有误！');
    public static $PROJECT_NOT_EXIST = array(100002007, '项目不存在，项目ID:${project_id}！');
    public static $PROJECT_NOT_ALLOW_DELETE = array(100002008, '当前状态的项目不允许删除');
    public static $PROJECT_CODE_GENERATE_ERROR = array(100002009, '项目编码生成失败！');
    public static $PROJECT_CODE_SERIAL_GENERATE_ERROR = array(100002010, '项目编码序号生成失败！');
    public static $PROJECT_TYPE_NO_CODE = array(100002011, '项目类型:${type}编码不存在！');
    public static $PROJECT_NOT_EXIST_BY_CODE = array(100002012, '项目不存在，项目编号:${project_code}！');
    public static $TRANSACTION_TOTAL_QUANTITY_EMPTY = array(100002013, '对不起，至少有一条交易明细入库单数量不能为空');
    public static $PARTNER_NOT_ALLOW_REPEAT = array(100002014, '上下游合作方不能重复');


    //100003001 业务负责人相关
    public static $BUSINESS_MANAGER_NO_CODE = array(100003001, '请添加业务负责人:${name} 的编码！');
    public static $BUSINESS_MANAGER_NOT_EXIST = array(100003002, '业务负责人:${manager_id}不存在');

    //100004001 业务主体相关
    public static $CORPORATION_NO_CODE = array(100004001, '请添加公司主体:${corporation_id}的企业编码！');
    public static $CORPORATION_NOT_EXIST = array(100004002, '公司主体:${corporation_id}不存在！');

    //100005001 项目合同相关
    public static $TRANSACTION_CURRENCY_AMOUNT_ERROR = array(100005001, '交易明细总价有误！');
    public static $TRANSACTION_AMOUNT_ERROR = array(100005002, '交易明细人民币总价有误！');
    public static $AGENT_FEE_REQUIRED_PARAMS_CHECK_ERROR = array(100005003, '代理手续费中*号标注字段不得为空！');
    public static $CONTRACT_ADD_NOT_AGENT_FEE = array(100005004, '请填写代理手续费!');
    public static $PROJECT_NOT_ALLOW_ADD_SUB_CONTRACT = array(100005005, '当前状态的项目不允许添加子合同！');
    public static $PAYMENT_PLAN_PARAMS_CHECK_ERROR = array(100005006, '请填写${label}中所有字段！');
    public static $TOTAL_PAYMENT_AMOUNT_GR_BUY_AMOUNT = array(100005007, '总付款金额超过人民币采购总价！');
    public static $PROJECT_SUB_CONTRACT_NOT_EXIST = array(100005008, '子合同信息不存在，子合同ID:${contract_id}！');
    public static $SUB_CONTRACT_NOT_ALLOW_EDIT = array(100005009, '当前状态的子合同不允许修改！');
    public static $CONTRACT_NOT_OPERATION = array(100005010, '当前合同信息不可操作！');
    public static $PROJECT_CONTRACT_NOT_EXIST = array(100005011, '合同信息不存在，合同ID:${contract_id}！');
    public static $CONTRACT_TYPE_NO_CODE = array(100005012, '合同类型:${type}编码不存在！');
    public static $CONTRACT_CODE_SERIAL_GENERATE_ERROR = array(100005013, '合同编码序号生成失败！');
    public static $CONTRACT_CODE_GENERATE_ERROR = array(100005014, '合同编码生成失败！失败原因:${reason}');
    public static $CONTRACT_FILE_ATTACH_NOT_UPLOAD = array(100005015, '请上传附件！');
    public static $CONTRACT_FILE_VIEW_DETAIL_NOT_ALLOW = array(100005016, '请先上传附件后才能查看合同上传详情！');
    public static $PROJECT_NOT_HAVE_CONTRACT = array(100005017, '当前项目没有合同信息，请添加项目合同！');
    public static $CONTRACT_FILE_NOT_UPLOAD = array(100005018, '合同尚未上传，请先上传合同！');
    public static $CONTRACT_FILE_UPLOAD_NOT_ALLOW = array(100005019, '当前状态无法进行${module_name}上传操作！');
    public static $CONTRACT_FILE_UPLOAD_NOT_FINISH = array(100005020, '${pre_module_name}上传尚未完成，请先完成${pre_module_name}上传功能！');
    public static $SIGN_UPLOAD_SUBMIT_STATUS_NOT_ALLOW = array(100005021, '当前状态不允许提交！');
    public static $PURCHASE_TRANSACTION_NOT_ALLOW_NULL = array(100005022, '请填写采购交易明细');
    public static $SALE_TRANSACTION_NOT_ALLOW_NULL = array(100005023, '请填写销售交易明细');
    public static $CONTRACT_GOODS_NOT_EXIST = array(100005024, '合同交易明细不存在，明细ID:${detail_id}');
    public static $AGENT_FEE_PRICE_ERROR = array(100005025, '请填写代理手续费中-计费单价字段！');
    public static $AGENT_FEE_FEE_RATE_ERROR = array(100005026, '请填写代理手续费中-代理手续费字段！');
    public static $UPDATE_RELATION_CONTRACT_ID_ERROR = array(100005027, '更新关联合同id失败！');
	public static $CONTRACT_SAVE_ADD_ERROR = array(100005028, '合同信息保存失败:${reason}');

    public static $CONTRACT_CANNOT_LADING = array(100005201, '当前合同不允许创建提单（入库通知单）');
    public static $CONTRACT_CANNOT_SUBMIT = array(100005202, '当前状态的合同不允许提交');


    public static $CONTRACT_TERMINATE_NOT_ALLOW_EDIT=array(100005301,'当前状态的合同终止不允许修改');
    public static $CONTRACT_TERMINATE_NOT_ALLOW_SUBMIT=array(100005302,'当前状态的合同终止不允许提交');

    public static $STOREHOUSE_PARAMS_ERROR = array(100006020, '参数错误');
    public static $STOREHOUSE_NAME_ERROR = array(100006021, '仓库名重复');
    public static $STOREHOUSE_EDITABLE_ERROR = array(100006022, '当前状态不允许修改');
    public static $STOREHOUSE_SAVE_ERROR = array(100006023, '保存失败！');



    // 风控审核
    public static $CONTRACT_NOT_ALLOW_RISKMANAGEMENT_CHECK = array(100007001, '当前状态的合同不在风控审核当中');

    //100008001 入库管理

    public static $STOCK_BATCH_NOT_ALLOW_EDIT = array(100008001, '当前状态的入库通知单不允许修改');
    public static $STOCK_BATCH_NOT_EXIST = array(100008002, '入库通知单:${batch_id}不存在！');
    public static $STOCK_BATCH_NOT_ALLOW_ADD = array(100008003, '当前合同不允许添加入库通知单，合同ID:${contract_id}');
    public static $STOCK_IN_NOT_ALLOW_ADD = array(100008004, '当前入库通知单不允许添加入库单，入库通知单ID:${batch_id}');
    public static $STOCK_BATCH_GOODS_REPEAT = array(100008005, '入库通知单明细中品名不能重复，请重新选择！');
    public static $STOCK_IN_NOT_ALLOW_EDIT = array(100008006, '当前状态的入库单不允许修改');
    public static $STOCK_IN_NOT_EXIST = array(100008007, '入库单:${stock_in_id}不存在！');
    public static $STOCK_BATCH_NOT_ALLOW_SUBMIT = array(100008008, '当前状态的入库通知单不允许提交！');
    public static $STOCK_IN_NOT_ALLOW_SUBMIT = array(100008009, '当前状态的入库单不允许提交！');
    public static $STOCK_BATCH_NOT_HAVE_DETAIL = array(100008010, '请填写入库通知单明细！');
    public static $STOCK_IN_NOT_HAVE_DETAIL = array(100008011, '请填写入库明细！');
    public static $STOCK_BATCH_SETTLE_NOT_ALLOW_SUBMIT = array(100008012, '当前状态的入库通知单结算不允许提交！');
    public static $STOCK_BATCH_SETTLE_NOT_ALLOW = array(100008013, '该入库通知单存在未审核通过的入库单,不能执行结算操作！');
    public static $STOCK_BATCH_DETAIL_NOT_EXIST = array(100008014, '入库通知单明细不存在，明细ID:${detail_id}');
    public static $STOCK_BATCH_SETTLE_PARAMS_ERROR  = array(100008015, '结算明细中*号标注字段不得为空');
    public static $STOCK_BATCH_SETTLE_NOT_ALLOW_EMPTY  = array(100008016, '请填写结算明细');
    public static $STOCK_IN_NOT_ALLOW_INVALID = array(100008017, '当前状态的入库通知单不允许作废');
    public static $STOCK_IN_NOT_ALLOW_REVOCATION = array(100008018, '当前状态的入库通知单不允许撤回');
    public static $STOCK_IN_SETTLE_STATUS_UPDATE_ERROR  = array(100008019, '入库单结算状态更新失败！');
    public static $STOCK_BATCH_SETTLE_NOT_ALLOW_EDIT = array(100008020, '当前状态的入库通知单结算已提交，不能执行修改操作！');
    public static $BUY_CONTRACT_SETTLE_NOT_ALLOW_EDIT = array(100008021, '当前状态的采购合同结算已提交，不能执行修改操作！');

    //100009001 出库管理
    public static $DELIVERY_ORDER_DETAIL_NOT_ALLOW_NULL = array(100009001, '请填写发货明细');
    public static $DELIVERY_ORDER_DETAIL_PARAMS_ERROR = array(100009002, '发货明细中*号标注字段不得为空！');
    public static $DELIVERY_ORDER_NOT_ALLOW_EDIT = array(100009003, '当前状态的发货单不允许修改');
    public static $DELIVERY_ORDER_NOT_EXIST = array(100009004, '发货单:${order_id}不存在！');
    public static $DELIVERY_ORDER_NOT_ALLOW_SUBMIT = array(100009005, '当前状态的发货单不允许提交！');
    public static $STOCK_FREEZE_ERROR = array(100009006, '库存冻结失败！库存ID:${stock_id},冻结库存量${quantity}');
    public static $STOCK_UNFREEZE_ERROR = array(100009007, '库存解冻失败！库存ID:${stock_id},解冻库存量${quantity}');
    public static $DELIVERY_QUANTITY_NOT_EQUAL_DISTRIBUTED_QUANTITY = array(100009008, '发货数量：${delivery_quantity}与配货明细总数量：${distributed_quantity}不一致，请检查！');
    public static $DISTRIBUTED_DETAIL_PARAMS_ERROR = array(100009009, '配货信息中*号标注字段不得为空！');
    public static $DISTRIBUTED_QUANTITY_GT_STOCK_QUANTITY_BALANCE = array(100009010, '入库单${code}(${cross_code})的配货数量：${distributed_quantity} 大于可用库存数量${quantity_balance}，请检查！');
    public static $DELIVERY_QUANTITY_GT_STOCK__IN_QUANTITY = array(100009011, '${goods_name}的本次发货总数量：${delivery_quantity} 大于其入库单数量：${stock_in_quantity}，请检查！');
    public static $CORSS_STOCK_FREEZE_ERROR = array(100009012, '借还货库存冻结失败！借还货库存ID:${cross_detail_id},冻结库存量${quantity}');
    public static $CORSS_STOCK_UNFREEZE_ERROR = array(100009013, '借还货库存解冻失败！借还货库存ID:${cross_detail_id},解冻库存量${quantity}');
    public static $DESTRIBUTE_GOODS_NOT_EXIST = array(100009014, '配货明细不能为空');
    public static $DESTRIBUTE_GOODS_NOT_DIFF_CONTRACT_CODE = array(100009015, '配货明细不能来自不同的销售合同');
    public static $DELIVERY_ORDER_SETTLE_NOT_ALLOW_SUBMIT = array(100009016, '当前状态的发货单结算不允许提交！');
    public static $DELIVERY_ORDER_SETTLE_NOT_ALLOW_EDIT = array(100009017, '当前状态的发货单结算已提交，不能执行修改操作！');
    public static $SELL_CONTRACT_SETTLE_NOT_ALLOW_EDIT = array(100009018, '当前状态的销售合同结算已提交，不能执行修改操作！');


    public static $STOCK_OUT_NOT_EXIST  = array(100009016, '出库单:${out_order_id}不存在！');
    public static $STOCK_OUT_NOT_ALLOW_INVALID = array(100009017, '当前状态的出库单不允许作废');
    public static $STOCK_OUT_NOT_ALLOW_REVOCATION = array(100009018, '当前状态的出库单不允许撤销');
    public static $STOCK_OUT_SETTLE_STATUS_UPDATE_ERROR  = array(100009019, '出库单结算状态更新失败！');
    public static $STOCK_OUT_BATCH_SETTLE_NOT_ALLOW = array(100009020, '该发货单存在未审核通过的出库单,不能执行结算操作！');
    public static $DELIVERY_ORDER_NOT_ALLOW_ADD = array(100009021, '当前选择的销售合同不能添加经仓发货单！');
    public static $DELIVERY_ORDER_GOODS_NOT_EXIST = array(100009022, '当前选择的销售合同不存在商品明细！');
    public static $OUT_QUANTITY_GT_ALLOW_PERCENTAGE = array(100009023, '【${goods_name}】的出库总数量不能超过配货数量10%，剩余可配:${quantity} + ${overflow}(可配+溢出),请检查！');
    public static $OUT_QUANTITY_NOT_ALLOW = array(100009024, '【${goods_name}】无剩余可配出库数量,请检查！');
    public static $OUT_STOCK_FREEZE_ERROR = array(100009025, '本次出库数量${quantity}已超过可用库存数量${residue_quantity}');
    public static $STOCK_OUT_ORDER_NOT_ALLOW_ADD = array(100009026, '当前状态的发货单不允许添加出库单！');
    public static $DELIVERY_DETAIL_NOT_EXIST = array(100009027, '本次发货明细不能为空！');
    public static $STOCK_OUT_DETAIL_NOT_EXIST = array(100009028, '本次出库明细不能为空！');

    //锁价
    public static $LOCK_SAVE_ADD_ERROR = array(100000301, '锁价信息保存失败:${reason}');

    //调货
    public static $CROSS_SAVE_ADD_ERROR = array(100000302, '调货信息保存失败:${reason}');

    //发票申请
    public static $INVOICE_APPLY_SAVE_ADD_ERROR = array(100000401, '发票申请信息保存失败:${reason}');

    //付款实付
    public static $PAY_CONFIRM_SAVE_ADD_ERROR = array(100000501, '付款实付信息保存失败:${reason}');

    //付款止付
    public static $PAY_STOP_SAVE_ADD_ERROR = array(100000511, '付款止付信息保存失败:${reason}');
    public static $PAY_STOP_NOT_ALLOW_EDIT = array(100000512, '当前状态下不可操作止付信息！');

    //发票开票
    public static $INVOICE_SAVE_ADD_ERROR = array(100000601, '销项票开票信息保存失败:${reason}');

    //100010001 保理管理
    public static $FACTOR_NOT_EXIST = array(100010001, '保理单:${factor_id}不存在！');
    public static $FACTOR_NOT_ALLOW_EDIT = array(100010002, '当前状态的保理对接单不允许修改');
    public static $FACTOR_DETAIL_NOT_ALLOW_SUBMIT = array(100010003, '当前状态的保理对接单不允许提交！');
    public static $PAY_APPLICATION_NOT_EXIST = array(100010004, '付款申请不存在，付款申请ID:${apply_id}！');
    public static $FACTOR_AMOUNT_GT_BALANCE_AMOUNT = array(100010005, '保理对接本金:${amount}不能大于剩余可对接金额:${balance_amount}！');
    public static $FACTOR_NOT_ALLOW_DOCONFIRM = array(100010006, '当前状态的保理单不允许确认提交！');
    public static $FACTOR_NOT_ALLOW_BOARD_FINANCE_LEADER_APPROVAL = array(100010007, '当前状态的保理单不允许板块财务负责人审批！');
    public static $FACTOR_NOT_ALLOW_FINANCE_LEADER_APPROVAL = array(100010008, '当前状态的保理单不允许财务负责人审批！');
    public static $FACTOR_NOT_ALLOW_CASHIER_APPROVAL = array(100010009, '当前状态的保理单不允许出纳审批！');
    public static $FACTOR_APPROVAL_STATUS_GET_ERROR = array(100010010, '保理信息审批流转状态获取失败！');
    public static $RETURN_DATE_LT_LAST_RETURN_DATE = array(100010011, '本次保理回款日期:${return_date},不能小于最后一次保理回款日期:${last_return_date}！');
    public static $AMOUNT_CAPITAL_INTEREST_NOT_MATCH = array(100010012, '您填写的回款本息与利息本金之和不符！');
    public static $FACTOR_NOT_ALLOW_RETURN = array(100010013, '当前状态的保理单不允许回款！');
    public static $FACTOR_RETURN_NOT_EXIST = array(100010014, '保理回款信息不存在！回款ID:${id}');
    public static $FACTOR_RETURN_NOT_ALLOW_SUBMIT = array(100010015, '当前状态的保理回款信息不允许提交！');
    public static $FACTOR_RETURN_NOT_ALLOW_ADD = array(100010016, '该保理信息存着未提交保理回款信息，请到明细中提交后再回款！');
    public static $FACTOR_NOT_ACTUAL_PAY = array(100010017, '尚未实付，无法进行该操作！');
    public static $FACTOR_RETURN_NOT_ALLOW_EDIT = array(100010018, '当前状态的保理回款信息不允许修改！');
    public static $FACTOR_ACTUAL_INTEREST_GR_AMOUNT = array(100010019, '回款本息不能小于应还利息！');
    public static $FACTOR_NOT_ALLOW_ADD = array(100010020, '该保理单不允许对接申请');
    public static $FACTOR_DETAIL_NOT_EXIST = array(100010021, '保理对接单:${detail_id}不存在！');
    public static $FACTOR_CODE_GENERATE_ERROR = array(100010022, '保理对接编号生成失败！');
    public static $FACTOR_NOT_ALLOW_DELETE = array(100010023, '当前状态的保理单不允许删除！');
    public static $MONEY_PAY_ORDER_SAVE_FAILED = array(100010024, '保存资金系统付款请求信息失败！');
    public static $MONEY_PAY_ORDER_NOT_ALLOW = array(100010025, '当前状态的付款申请单不能进行自动实付操作');
    public static $MONEY_PAY_ORDER_ERROR = array(100010026, '调用资金系统实付命令结果异常!');

    // 付款100011000
    public static $PAY_APPLICATION_SAVE_ADD_ERROR = array(100011001, '付款申请保存失败：${reason}');
    public static $PAY_CLAIM_AMOUNT_OVERFLOW = array(100011002, '认领金额不能大于待认领金额');
    public static $PAY_CLAIM_NOT_ALLOW_ADD = array(100011003, '付款申请尚未实付，无法进行认领操作');
    public static $PAY_APPLICATION_NOT_ALLOW_CLAIM = array(100011004, '当前状态的付款申请不允许认领！付款申请编号：${apply_id}');
    public static $PAY_CLAIM_NOT_ALLOW_SUBMIT = array(100011005, '当前状态的付款认领信息不能提交！认领ID：${claim_id}');
    public static $PAYMENT_PLAN_NOT_EXIST = array(100011006, '付款计划不存在！付款计划ID：${plan_id}');
    public static $PAYMENT_PLAN_CLAIM_OVERFLOW = array(100011007, '期数${period}认领金额不能超过待认领金额');
    public static $PAY_CLAIM_NOT_EXIST = array(100011008, '付款认领信息不存在！认领ID:${claim_id}');
    public static $PAY_CONFIRM_NOT_EXIST = array(100011009, '付款实付信息不存在！实付ID:${payment_id}');

    //100012001
    public static $STOCK_INVENTORY_DETAIL_EMPTY = array(100012001, '请填写损耗分摊明细！');
    public static $STOCK_INVENTORY_DETAIL_PARAMS_PASS_ERROR = array(100012002, '损耗分摊明细中参数传入错误，请检查！');
    public static $STOCK_INVENTORY_DETAIL_QUANTITY_DIFF_NOT_MATCH = array(100012003, '损耗分摊库存损耗总数与库存损耗不符，请检查！');
    public static $STOCK_INVENTORY_NOT_ALLOW_EDIT = array(100012004, '当前状态的库存盘点信息不允许修改');
    public static $STOCK_INVENTORY_NOT_ALLOW_ADD = array(100012005, '存在尚未审核的库存盘点信息，不允许进行库存盘点');
    public static $STOCK_INVENTORY_NOT_ALLOW_SUBMIT = array(100012006, '当前状态的库存盘点信息不允许提交');
    public static $STOCK_INVENTORY_NOT_EXIST = array(100012007, '库存盘点信息:${inventory_id}不存在！');
    public static $STOCK_INVENTORY_GOODS_DETAIL_NOT_EXIST = array(100012007, '库存盘点明细信息:${goods_detail_id}不存在！');
    public static $STOCK_REDUCE_ERROR = array(100012008, '库存减小失败！库存id:${stock_id},数量:${quantity}');
    public static $STOCK_ADD_ERROR = array(100012009, '库存增加失败！库存id:${stock_id},数量:${quantity}');
    public static $STOCK_BALANCE_NOT_ENOUGH = array(100012010, '入库单${stock_in_code}的库存损耗不能大于可用库存！库存id:${stock_id},库存损耗:${quantity},可用库存:{quantity_balance}');
    public static $STOCK_NOT_EXIST = array(100012011, '库存单号不存在！库存id:${stock_id}');
    public static $PENDING_PAY_STOP = array(100012012, '止付进行中, 不能进行实付');

    //100013001
    public static $BANK_FLOW_NOT_EXIST = array(100013001, '银行流水不存在，银行流水ID:${flow_id}');
    public static $BANK_FLOW_NOT_ALLOW_SUBMIT = array(100013002, '当前状态的银行流水不允许提交，银行流水ID:${flow_id}');
    public static $BANK_FLOW_NOT_ALLOW_CLAIM = array(100013003, '当前状态的银行流水无法认领，银行流水ID:${flow_id}');
    public static $RECEIVE_CONFIRM_NOT_EXIST = array(100013004, '银行流水认领不存在，认领ID:${receive_id}');

    //停息
    public static $PAYMENT_INTEREST_NOT_EXIST = array(100014001, '合同利息占用信息不存在，合同ID:${contract_code}');
    public static $STOP_INTEREST_ERROR        = array(100014002, '停息保存失败!');
    public static $STOP_DATE_IS_NOT_NULL      = array(100014003, '停息日期不能为空!');
    public static $STOP_REASON_IS_NOT_NULL    = array(100014004, '停息理由不能为空!');

    //结算
    public static $DELIVERY_ORDER_SETTLEMENT_NOT_EXIST = array(100015001, '发货单:${order_id}结算信息不存在！');
    public static $SELL_CONTRACT_SETTLEMENT_NOT_EXIST = array(100015002, '合同:${contract_id}结算信息不存在！');
    public static $LADING_BILL_SETTLEMENT_NOT_EXIST = array(100015003, '入库通知单:${batch_id}结算信息不存在！');
    public static $BUY_CONTRACT_SETTLEMENT_NOT_EXIST = array(100015003, '合同:${contract_id}结算信息不存在！');
}