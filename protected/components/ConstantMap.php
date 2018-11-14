<?php

/**
 * Desc: 系统自定义常量
 * User: susiehuang
 * Date: 2017/7/26 0026
 * Time: 17:22
 */
class ConstantMap {
    //项目编码开头字母
    const PROJECT_CODE_START_STR = 'Z';

    //是否生效
    const STATUS_VALID = 1; //有效
    const STATUS_INVALID = 0; //无效

    //接口返回是否正确
    const VALID = 0;
    const INVALID = - 1;
    const SPECIAL_INVALID = - 2;

    //新添加状态
    const STATUS_NEW = 0; //新添加

    //管理员角色id
    const ADMIN_ROLE_ID = 1;
    const BUSINESS_ROLE_ID = 4; //商务角色id
    const DEPARTMENT_LEADER_ROLE_ID = 16;//部门负责人
    const RISK_MANAGE_ROLE_ID = 6;//风控角色ID
    const RISK_MANAGE_LEADER_ROLE_ID = 17;//风控负责人
    const LAW_LEADER_ROLE_ID = 18;//法务负责人
    const BOARD_LEADER_ROLE_ID = 19;//板块负责人
    const ACCOUNTING_ROLE_ID = 20;//财务会计
    const BOARD_FINANCE_LEADER_ROLE_ID = 12;//板块财务负责人
    const FINANCE_LEADER_ROLE_ID = 9;//财务负责人
    const CASHIER_ROLE_ID = 8;//出纳

    //项目类型
    const PROJECT_TYPE_SELF_IMPORT = 1; //进口自营
    const PROJECT_TYPE_IMPORT_BUY = 2; //进口代采
    const PROJECT_TYPE_IMPORT_CHANNEL = 3; //进口渠道
    const PROJECT_TYPE_SELF_INTERNAL_TRADE = 4; //内贸自营
    const PROJECT_TYPE_INTERNAL_TRADE_BUY = 5; //内贸代采
    const PROJECT_TYPE_INTERNAL_TRADE_CHANNEL = 6; //内贸渠道
    const PROJECT_TYPE_WAREHOUSE_RECEIPT = 7; //仓单质押


    const SELF_IMPORT_FIRST_SALE_LAST_BUY = 11; //进口自营-先销后采
    const SELF_IMPORT_FIRST_BUY_LAST_SALE = 12; //进口自营-先采后销
    const SELF_INTERNAL_TRADE_FIRST_SALE_LAST_BUY = 41; //内贸自营-先销后采
    const SELF_INTERNAL_TRADE_FIRST_BUY_LAST_SALE = 42; //内贸自营-先采后销

    //购销顺序
    const FIRST_SALE_LAST_BUY = 1; //先销后采
    const FIRST_BUY_LAST_SALE = 2; //先采后销

    //采销类型
    const BUY_TYPE = 1; //采
    const SALE_TYPE = 2; //销

    //附件类型
    const PROJECT_LAUNCH_DECISION_ATTACH_TYPE = 1; //项目发起立项会决议附件类型
    const PROJECT_BUDGET_ATTACH_TYPE = 21; //项目发起立项会决议附件类型

    const STOCK_NOTICE_ATTACH_TYPE = 1; //入库通知单附件类型
    const STOCK_IN_ATTACH_TYPE = 1; //入库单附件类型
    const STOCK_BATCH_ATTACH_TYPE = 1; //入库通知单结算附件类型
    const STOCK_INVENTORY_ATTACH_TYPE = 1; //库存盘点附件类型
    
    
    const STOCK_DELIVERY_ATTACH_TYPE = 1; //发货单附件类型
    const STOCK_OUT_ATTACH_TYPE = 2; //仓库出库单附件类型
    
    const PROJECT_CODE_SERIAL_PREFIX_KEY = 'project_code_serial_'; //项目编码自增部分redis key

    //操作类型
    const OPERATE_ADD = 1;
    const OPERATE_EDIT = 2;

    //是否主合同
    const CONTRACT_MAIN = 1; //主合同
    const CONTRACT_NOT_MAIN = 0; //非主合同

    //合同类型
    const CONTRACT_CATEGORY_SUB_BUY = 1; //子采购
    const CONTRACT_CATEGORY_SUB_SALE = 2; //子销售

    //模块名
    const MODUAL_PROJECT_ID = 'project';
    const MODUAL_SUB_CONTRACT_ID = 'subContract';

    //币种
    const CURRENCY_RMB = 1;
    const CURRENCY_DOLLAR = 2;

    //收支类型
    const RECEIVE_TYPE = 1; //收
    const PAY_TYPE = 2; //付

    //代理手续费计费方式
    const AGENT_FEE_CALCULATE_BY_AMOUNT = 1; //从量
    const AGENT_FEE_CALCULATE_BY_PRICE = 2; //从价

    //采购合同类型
    const BUY_SALE_CONTRACT_TYPE_DIRECT_IMPORT = 1; //直接进口合同
    const BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT = 2; //代理进口合同
    const BUY_SALE_CONTRACT_TYPE_INTERNAL = 3; //国内采购合同
    const BUY_SALE_CONTRACT_TYPE_AUTO = 5; //自动生成采购合同

    const SELL_SALE_CONTRACT_TYPE_INTERNAL = 4; //国内销售合同
    const SELL_SALE_CONTRACT_TYPE_AUTO = 6; //自动生成销售合同

    //收付款类别
    const RECEIVE_PAY_TYPE_OTHER = 5; //其他

    //价格方式
    const PRICE_TYPE_STATIC = 1;  //死价
    const PRICE_TYPE_TEMPORARY = 2;    //活价（价格为暂估价）

    //合同附件类型
    const FINAL_CONTRACT_FILE = 1; //最终合同文本
    const ELECTRON_SIGN_CONTRACT_FILE = 11; //电子双签合同
    const PAPER_SIGN_CONTRACT_FILE = 21; //纸质双签合同

    //合同文本类型
    const FINAL_CONTRACT_MODULE = 1; //最终合同
    const ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE = 11; //电子双签
    const PAPER_DOUBLE_SIGN_CONTRACT_MODULE = 21; //纸质双签

    //public static $Channel_Business_Project_Type=array();

    //渠道、代采
    public static $channel_buy_project_type = array(ConstantMap::PROJECT_TYPE_IMPORT_BUY, ConstantMap::PROJECT_TYPE_IMPORT_CHANNEL, ConstantMap::PROJECT_TYPE_INTERNAL_TRADE_BUY, ConstantMap::PROJECT_TYPE_INTERNAL_TRADE_CHANNEL);
    //仓单质押
    public static $warehouse_receive_project_type = array(ConstantMap::PROJECT_TYPE_WAREHOUSE_RECEIPT);
    //自营项目类型
    public static $self_support_project_type = array(ConstantMap::PROJECT_TYPE_SELF_IMPORT, ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE); //自营

    //进口渠道、进口代采
    public static $import_buy_project_type = array(ConstantMap::PROJECT_TYPE_IMPORT_BUY, ConstantMap::PROJECT_TYPE_IMPORT_CHANNEL);

    //采购合同类别
    public static $buy_select_contract_type = array(ConstantMap::PROJECT_TYPE_IMPORT_BUY, ConstantMap::PROJECT_TYPE_IMPORT_CHANNEL);
    public static $buy_static_contract_type = array(ConstantMap::PROJECT_TYPE_INTERNAL_TRADE_BUY, ConstantMap::PROJECT_TYPE_INTERNAL_TRADE_CHANNEL, ConstantMap::PROJECT_TYPE_WAREHOUSE_RECEIPT);

    //上传合同类型
    public static $upload_contract_type = array(ConstantMap::BUY_SALE_CONTRACT_TYPE_DIRECT_IMPORT, ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT, ConstantMap::BUY_SALE_CONTRACT_TYPE_INTERNAL);

    //锁价与转月
    const LOCK_PRICE = 1;
    const ROLLOVER_MONTH = 2;

    //锁价维度：合同和入库通知单
    const LOCK_TYPE_CONTRACT = 1;
    const LOCK_PUT_ORDER = 2;

    //入库通知单发货方式
    const STOCK_NOTICE_TYPE_BY_WAREHOUSE = 1; //经仓
    const STOCK_NOTICE_TYPE_DIRECT_TRANSFER = 2; // 直调

    //借调货类型
    const ORDER_CROSS_TYPE  = 1; //借调货
    const ORDER_BUY_TYPE    = 2; //生成采购合同
    const ORDER_BACK_TYPE   = 3; //还货

    //配货类型
    const DISTRIBUTED_NORMAL = 1; //正常发货（本项目）
    const DISTRIBUTED_LOAN = 2; //借货
    const DISTRIBUTED_RETURN = 3; //还货

    const STOCK_INVENTORY = 4; //库存盘点


    //发票相关常量
    const INPUT_INVOICE_TYPE    = 1;  //进项票
    const OUTPUT_INVOICE_TYPE   = 2; //销项票

    const PAYMENT_GOODS_TYPE    = 1; //货款
    const PAYMENT_NOT_GOODS_TYPE= 2; //非货款

    const INVOICE_RATE_NORMAL_TYPE = 1; //常规增值税专用票
    const INVOICE_RATE_CUSTOMS_TYPE = 2; //海关增值税专用票

    //保理业务id
    const FACTOR_BUSINESS_ID = 14;
    
    // 流水认领
    const RECEIVE_CONFIRM_ATTACH_TYPE = 1; //入库通知单附件类型

    //库存盘点类型
    const STOCK_INVENTORY_PROFIT = 1; //盘赢
    const STOCK_INVENTORY_LOSS = 2; //盘亏

    //出入库方式
    const STOCK_TYPE_OUT = 1; //出库
    const STOCK_TYPE_IN = 2; //入库

    //发票申请税率类型
    const INVOICE_RATE_TYPE = 4; //其他

    //税款保证金付款科目id
    const TAX_DEPOSIT_SUBJECT_ID = 6;
    const GOODS_FEE_SUBJECT_ID='1,6,7,8';

    //代理模式
    const AGENT_TYPE_BUY_SALE = 1;//购销代理模式
    const AGENT_TYPE_PURE = 2; //纯代理模式

    //合同截止交货日期和交票期限
    const CONTRACT_DEFAULT_DELIVERY_TERM = 90;
    const CONTRACT_DEFAULT_DAYS = 7;

    //合同商品单位换算比，默认被换算单位
    const CONTRACT_GOODS_UNIT_CONVERT = '吨';
    const CONTRACT_GOODS_UNIT_CONVERT_VALUE = 2;

    //报表统计默认单位：吨
    const UNIT_TON=2;

}