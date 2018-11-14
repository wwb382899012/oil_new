<?php
return array(

    /**
     * 系统
     */
    "system_id"=>array("1"=>"石油项目管理系统"),

    /**
     * 系统模块状态
     */
    "module_status"=>array("0"=>"未启用","1"=>"已启用"),
    /**
     * 系统模块是否公开，即不需要判断权限
     */
    "module_is_public"=>array("0"=>"不公开","1"=>"公开"),
    /**
     * 系统模块的链接是否外部的，即直接新窗口打开
     */
    "module_is_external"=>array("0"=>"内部","1"=>"外部"),
    "module_is_menu"=>array("0"=>"不是菜单","1"=>"是菜单"),

    /**
     * 系统用户状态
     */
    "user_status"=>array("0"=>"未启用","1"=>"启用"),

    /**
     * 系统角色状态
     */
    "role_status"=>array("0"=>"未启用","1"=>"启用"),

    "partner_status"=>array(
            "0"=>"已保存",
            "9"=>"风控初审驳回",
            "10"=>"风控初审中",
//            "15"=>"现场风控驳回",
            "25"=>"现场风控中",
            "30"=>"会议评审中",
            "40"=>"补充资料需再评审",
            "45"=>"补充资料无需再评",
            "-1"=>"评审否决",
            "99"=>"评审通过"
            ),

    "partner_status_log"=>array(
	    "0"=>"已保存",
	    "9"=>"风控初审驳回",
	    "10"=>"风控初审中",
	    "15"=>"现场风控驳回",
	    "25"=>"现场风控中",
	    "30"=>"会议评审中",
	    "40"=>"补充资料需再评审",
	    "45"=>"补充资料无需再评",
	    "-1"=>"评审否决",
	    "99"=>"评审通过"
    ),
    // "partner_level_type"=>array("1"=>"A类","2"=>"B类","3"=>"C类","4"=>"D类"),

    // "partner_status"=>array("0"=>"待确认","1"=>"正常"),

    /**
     * 账户状态
     */
    "account_status"=>array("0"=>"失效","1"=>"正常"),

    "sex"=>array("0"=>"女","1"=>"男"),

    /**
     * 供应商所有制
     */
    "supplier_ownership"=>array("1"=>"公立","2"=>"私立"),

    /**
     * 合作方企业所有制
     */
    "ownership"=>array("1"=>"国有","2"=>"民营"),

	/**
	 * 交易品种 石油/沥青/
     */
	 "goods_type"=>array("1"=>"0#普通柴油","2"=>"92#车用汽油","3"=>"燃料油",
                         "4"=>"MTBE","5"=>"纯苯","6"=>"芳烃","7"=>"混合芳烃",
                         "8"=>"沥青料","9"=>"石脑油","10"=>"石油焦","11"=>"碳九馏分",
                         "12"=>"调和油","13"=>"乙烯焦油","14"=>"异辛烷","15"=>"原料油",
                         "16"=>"工业用裂解碳九","17"=>"1#混合三甲苯","18"=>"稳定轻烃",
                         "19"=>"戊烷发泡剂","20"=>"0#车用柴油（V）","21"=>"95#车用汽油（V）",),

    /**
     * 是否在经营范围内
     */
    "is_in_scope"=>array("0"=>"不在经营范围内","1"=>"在经营范围内"),

     /**
     * 交货方式
     */
     "delivery_method"=>array("1"=>"买方自提","2"=>"卖方送货"),




    "corporation_status"=>array("0"=>"待确认","1"=>"正常"),

    //付款形式
    "pay_time"=>array("1"=>"预付","2"=>"后付"),
    //付款形式
    "receive_time"=>array("1"=>"保证金","2"=>"后收"),

    "project_status"=>array(
        "-9"=>"项目终止",
        "-1"=>"审核拒绝",
        "0"=>"未提交",
        "1"=>"项目撤回",
        //"10"=>"业务初审中",
        "10"=>"商务确认中",

        "23"=>"合同初审驳回",
        "24"=>"合同初审拒绝",
        "25"=>"合同初审中",
        //"25"=>"商务合同初审中",
        //"26"=>"财务法务合同初审中",

        "30"=>"最终合同",
        "35"=>"上下游合同签章",

        "43"=>"签章合同审核驳回",
        "44"=>"签章合同审核拒绝",
        "45"=>"签章合同审核中",
        //"46"=>"财务签章合同审核中",

        //"20"=>"初审完成",

        //"25"=>"IRR复核中",

        //"30"=>"合同初审",
        //"31"=>"合同审核驳回",
        //"32"=>"合同审核拒绝",
        //"35"=>"合同审核中",

        "50"=>"我方合同上传",

        "55"=>"双签合同上传",

        "60"=>"保证金确认中",

        "61"=>"预付款处理中",

        "70"=>"预付款已完成",

        //"41"=>"合同归档审核中",
        //"42"=>"合同归档审核驳回",
        //"43"=>"合同归档审核拒绝",
        //"45"=>"合同归档审核中",

        //"49"=>"合同完成",

        //"50"=>"上游付款",
        // "51"=>"预付款完成",
        // "52"=>"上游结算",
        // "53"=>"上游结算审核中",
        // "54"=>"上游结算完成",
        // "55"=>"上游结算单归档",
        // "56"=>"上游结算单归档审核中",
        // "57"=>"上游结算单归档审核完成",

        "80"=>"结算中",
        "81"=>"结算审核退回",
        "83"=>"结算审核中",
        "85"=>"结算审核完成",

        /*"86"=>"结算单归档中",
        "87"=>"结算单归档审核拒绝",
        "88"=>"结算单归档审核中",
        "89"=>"结算单归档审核完成",*/

        // "50"=>"付款确认",
        // "51"=>"付款申请",
        // "52"=>"付款审核拒绝",
        // "54"=>"付款审核中",
        // "56"=>"付款完成",

        //"60"=>"结算中",
        "90"=>"项目结清",
        "71"=>"上游开票中",
        "72"=>"下游开票中",
        "73"=>"下游开票审核中",
        "74"=>"下游开票审核通过",
        "75"=>"税票条件反馈中",
        "76"=>"税票条件审核中",
        "77"=>"税票条件审核通过",
        "80"=>"发票已开具",
        "81"=>"发票已发快递",
        "82"=>"下游确认收票",

        "99"=>"项目完成",
    ),

    /**
     * 项目所有相关的附件类别
     */
    "project_attachment_all_type"=>array(
        "1"=>array("id"=>"1","name"=>"上游采购合同","maxSize"=>30,"fileType"=>"|docx|pdf|"),
        "2"=>array("id"=>"2","name"=>"下游销售合同","maxSize"=>30,"fileType"=>"|docx|pdf|"),
        "3"=>array("id"=>"3","name"=>"担保合同","maxSize"=>30,"fileType"=>"|docx|pdf|"),
        "4"=>array("id"=>"4","name"=>"下游收货确认书","maxSize"=>30,"fileType"=>"|docx|pdf|"),
        "5"=>array("id"=>"5","name"=>"履约保函","maxSize"=>30,"fileType"=>"|docx|pdf|"),
        "6"=>array("id"=>"6","name"=>"货权转移证明","maxSize"=>30,"fileType"=>"|docx|pdf|"),
        "7"=>array("id"=>"7","name"=>"其他合同1","maxSize"=>30,"fileType"=>"|docx|pdf|"),
        "8"=>array("id"=>"8","name"=>"其他合同2","maxSize"=>30,"fileType"=>"|docx|pdf|"),

        "51"=>array("id"=>"51","name"=>"会议附件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "61"=>array("id"=>"61","name"=>"补充材料","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),

        "201"=>array("id"=>"201","name"=>"上游结算单申请件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "202"=>array("id"=>"202","name"=>"上游结算单确认（签字盖章）件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "203"=>array("id"=>"203","name"=>"下游结算单申请件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "204"=>array("id"=>"204","name"=>"下游结算单确认（签字盖章）件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),


    /**
     * 项目发起时的附件信息，对应项目附件表
     */
    "project_new_attachment_type"=>array(
        "1"=>array("id"=>"1","name"=>"上游合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "2"=>array("id"=>"2","name"=>"下游合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "3"=>array("id"=>"3","name"=>"其他资料","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 项目附件信息，对应项目附件表
     */
    "project_attachment_type"=>array(
        "1"=>array("id"=>"1","name"=>"上游合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "2"=>array("id"=>"2","name"=>"下游合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "3"=>array("id"=>"3","name"=>"其他资料","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "10"=>array("id"=>"10","name"=>"下游收货确认","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 项目终止相关附件
     */
    "project_trash_attachment_type"=>array(
        "901"=>array("id"=>"901","name"=>"终止协议","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "902"=>array("id"=>"902","name"=>"其他附件","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 结算单发起时的附件类别，对应项目附件表
     */
    "settle_attachment_type"=>array(
        "201"=>array("id"=>"201","name"=>"结算单申请件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        //"203"=>array("id"=>"203","name"=>"下游结算单申请件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 结算单归档时的附件类别，对应项目附件表
     */
    "settle_file_attachment_type"=>array(
        "202"=>array("id"=>"202","name"=>"上游结算单确认（签字盖章）件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "204"=>array("id"=>"204","name"=>"下游结算单确认（签字盖章）件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 结算单附件类别，对应项目附件表，结算相关的附件type在200~300之内
     */
    "settlement_attachment_type"=>array(
        "201"=>array("id"=>"201","name"=>"上游结算单申请件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "202"=>array("id"=>"202","name"=>"上游结算单确认（签字盖章）件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "203"=>array("id"=>"203","name"=>"下游结算单申请件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "204"=>array("id"=>"204","name"=>"下游结算单确认（签字盖章）件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 上游付款相关的附件类
     */
    "up_attachment_type"=>array(
        "1"=>array("id"=>"1","name"=>"上游发票","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    "project_check1_status"=>array("1"=>"待审核","2"=>"审核通过","3"=>"审核拒绝","4"=>"审核驳回"),

    "contract_check_status"=>array("1"=>"待审核","2"=>"审核通过","3"=>"审核驳回",),

    "settle_check_status"=>array("1"=>"待审核","2"=>"审核通过","4"=>"审核驳回",),
    /**
     * 企业准入风控初审状态
     */
    "partner_check_status"=>array("1"=>"待审核","2"=>"审核通过","3"=>"审核拒绝","4"=>"审核驳回","5"=>"需现场风控","6"=>"需评审",),

    "pay_check_status"=>array("1"=>"待审核","2"=>"审核通过","4"=>"审核驳回",),

    "invoice_check_status"=>array("1"=>"待审核","2"=>"审核通过","4"=>"审核驳回",),

    "invoice_condition_check_status"=>array("1"=>"待审核","2"=>"审核通过","3"=>"审核拒绝",),

    "check_status"=>array("-1"=>"驳回","0"=>"拒绝","1"=>"通过"),

    "settle_status"=>array("-1"=>"待处理","0"=>"未结算","1"=>"结算驳回","2"=>"已结算"),

    "settlement_status"=>array("-1"=>"未发起","0"=>"草稿","1"=>"待审核","2"=>"审核拒绝","5"=>"已确认","6"=>"签字归档","8"=>"归档审核中","9"=>"归档完成","99"=>"交易结算完成"),

    /**
    *合同状态
    */
    "contract_status"=>array("-2"=>"待处理","-1"=>"审核驳回","0"=>"未提交","1"=>"已提交","6"=>"审核通过","7"=>"已打印"),

    /**
     * 所有合同附件类别
     */
    "all_contract_attachment_type"=>array(
        "1"=>array("id"=>"1","name"=>"上游采购合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "2"=>array("id"=>"2","name"=>"下游销售合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "3"=>array("id"=>"3","name"=>"担保合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "4"=>array("id"=>"4","name"=>"下游收货确认书","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "5"=>array("id"=>"5","name"=>"履约保函","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "6"=>array("id"=>"6","name"=>"货权转移证明","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "7"=>array("id"=>"7","name"=>"其他合同1","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "8"=>array("id"=>"8","name"=>"其他合同2","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),

        "101"=>array("id"=>"101","name"=>"上游采购合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "102"=>array("id"=>"102","name"=>"下游销售合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "103"=>array("id"=>"103","name"=>"担保合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "104"=>array("id"=>"104","name"=>"下游收货确认书","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "105"=>array("id"=>"105","name"=>"履约保函","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "106"=>array("id"=>"106","name"=>"货权转移证明","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "107"=>array("id"=>"107","name"=>"其他合同1","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "108"=>array("id"=>"108","name"=>"其他合同2","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),

        "201"=>array("id"=>"201","name"=>"上游采购合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "202"=>array("id"=>"202","name"=>"下游销售合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "203"=>array("id"=>"203","name"=>"担保合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "204"=>array("id"=>"204","name"=>"下游收货确认书","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "205"=>array("id"=>"205","name"=>"履约保函","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "206"=>array("id"=>"206","name"=>"货权转移证明","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "207"=>array("id"=>"207","name"=>"其他合同1","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "208"=>array("id"=>"208","name"=>"其他合同2","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),

        "301"=>array("id"=>"301","name"=>"上游采购合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "302"=>array("id"=>"302","name"=>"下游销售合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "303"=>array("id"=>"303","name"=>"担保合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "304"=>array("id"=>"304","name"=>"下游收货确认书","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "305"=>array("id"=>"305","name"=>"履约保函","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "306"=>array("id"=>"306","name"=>"货权转移证明","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "307"=>array("id"=>"307","name"=>"其他合同1","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "308"=>array("id"=>"308","name"=>"其他合同2","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "309"=>array("id"=>"309","name"=>"其他合同1","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "310"=>array("id"=>"310","name"=>"其他合同2","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),

        "401"=>array("id"=>"401","name"=>"上游采购合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "402"=>array("id"=>"402","name"=>"下游销售合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "403"=>array("id"=>"403","name"=>"担保合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "404"=>array("id"=>"404","name"=>"下游收货确认书","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "405"=>array("id"=>"405","name"=>"履约保函","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "406"=>array("id"=>"406","name"=>"货权转移证明","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "407"=>array("id"=>"407","name"=>"其他合同1","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "408"=>array("id"=>"408","name"=>"其他合同2","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 合同附件类别
     */
    "contract_new_attachment_type"=>array(
        "1"=>array("id"=>"1","name"=>"上游合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "2"=>array("id"=>"2","name"=>"下游合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
    ),

    /**
     * 项目合同附件类别
     */
    "contract_attachment_type"=>array(
        "1"=>array("id"=>"1","name"=>"上游采购合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "2"=>array("id"=>"2","name"=>"下游销售合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "3"=>array("id"=>"3","name"=>"担保合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "4"=>array("id"=>"4","name"=>"下游收货确认书","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "5"=>array("id"=>"5","name"=>"履约保函","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "6"=>array("id"=>"6","name"=>"货权转移证明","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "7"=>array("id"=>"7","name"=>"其他合同1","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "8"=>array("id"=>"8","name"=>"其他合同2","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
    ),

    /**
     * 项目合同附件类别
     */
    "project_contract_attachment_type"=>array(
        "1"=>array("id"=>"1","name"=>"上游采购合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "2"=>array("id"=>"2","name"=>"下游销售合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "3"=>array("id"=>"3","name"=>"担保合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "4"=>array("id"=>"4","name"=>"下游收货确认书","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "5"=>array("id"=>"5","name"=>"履约保函","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "6"=>array("id"=>"6","name"=>"货权转移证明","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "7"=>array("id"=>"7","name"=>"其他合同1","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "8"=>array("id"=>"8","name"=>"其他合同2","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
    ),

    /**
     * 商务合同附件类别
     */
    "business_contract_attachment_type"=>array(
        "101"=>array("id"=>"101","name"=>"上游采购合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "102"=>array("id"=>"102","name"=>"下游销售合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "103"=>array("id"=>"103","name"=>"担保合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "104"=>array("id"=>"104","name"=>"下游收货确认书","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "105"=>array("id"=>"105","name"=>"履约保函","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "106"=>array("id"=>"106","name"=>"货权转移证明","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "107"=>array("id"=>"107","name"=>"其他合同1","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "108"=>array("id"=>"108","name"=>"其他合同2","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
    ),

    /**
     * 签章合同附件类别
     */
    "stamp_contract_attachment_type"=>array(
        "201"=>array("id"=>"201","name"=>"上游采购合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "202"=>array("id"=>"202","name"=>"下游销售合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "203"=>array("id"=>"203","name"=>"担保合同","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "204"=>array("id"=>"204","name"=>"下游收货确认书","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "205"=>array("id"=>"205","name"=>"履约保函","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "206"=>array("id"=>"206","name"=>"货权转移证明","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "207"=>array("id"=>"207","name"=>"其他合同1","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
        "208"=>array("id"=>"208","name"=>"其他合同2","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
    ),

    /**
     * 我方签章合同附件类别
     */
    "mine_contract_attachment_type"=>array(
        "301"=>array("id"=>"301","name"=>"上游采购合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "302"=>array("id"=>"302","name"=>"下游销售合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "303"=>array("id"=>"303","name"=>"担保合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "304"=>array("id"=>"304","name"=>"下游收货确认书","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "305"=>array("id"=>"305","name"=>"履约保函","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "306"=>array("id"=>"306","name"=>"货权转移证明","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "307"=>array("id"=>"307","name"=>"其他合同1","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "308"=>array("id"=>"308","name"=>"其他合同2","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 双签纸质合同附件类别
     */
    "paper_contract_attachment_type"=>array(
        "401"=>array("id"=>"401","name"=>"上游采购合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "402"=>array("id"=>"402","name"=>"下游销售合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "403"=>array("id"=>"403","name"=>"担保合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "404"=>array("id"=>"404","name"=>"下游收货确认书","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "405"=>array("id"=>"405","name"=>"履约保函","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "406"=>array("id"=>"406","name"=>"货权转移证明","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "407"=>array("id"=>"407","name"=>"其他合同1","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "408"=>array("id"=>"408","name"=>"其他合同2","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),
    /**
     * 项目讨论会议附件
     */
    "project_conference_attachment_type"=>array(
        "51"=>array("id"=>"51","name"=>"会议附件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 项目讨论补充附件
     */
    "project_supplement_attachment_type"=>array(
        "61"=>array("id"=>"61","name"=>"补充材料","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
    *归档合同状态
    */
    "contract_file_status"=>array("-2"=>"待处理","-1"=>"未上传","1"=>"已上传未提交","2"=>"已提交"),

    /**
     * 合同归档附件类别
     */
    /*"contract_file_attachment_type"=>array(
        "101"=>array("id"=>"101","name"=>"上游合同（归档）","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "102"=>array("id"=>"102","name"=>"下游合同（归档）","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "103"=>array("id"=>"103","name"=>"下游收货确认书（归档）","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "104"=>array("id"=>"104","name"=>"担保书（归档）","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "105"=>array("id"=>"105","name"=>"其他合同1（归档）","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "106"=>array("id"=>"106","name"=>"其他合同2（归档）","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "107"=>array("id"=>"107","name"=>"其他合同3（归档）","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),*/
    "contract_file_attachment_type"=>array(
        "401"=>array("id"=>"401","name"=>"上游采购合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "402"=>array("id"=>"402","name"=>"下游销售合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "403"=>array("id"=>"403","name"=>"担保合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "404"=>array("id"=>"404","name"=>"下游收货确认书","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "405"=>array("id"=>"405","name"=>"履约保函","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "406"=>array("id"=>"406","name"=>"货权转移证明","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "407"=>array("id"=>"407","name"=>"其他合同1","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "408"=>array("id"=>"408","name"=>"其他合同2","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),


    /**
     * 付款申请状态
     */
    "pay_request_status"=>array("-1"=>"已驳回","1"=>"审核中","2"=>"审核通过"),
    //"pay_request_status"=>array("-2"=>"待处理","-1"=>"申请被拒绝","0"=>"未提交","1"=>"已提交"),

    /**
     * 付款计划状态
     */
    "pay_plan_status"=>array("-1"=>"待处理","0"=>"待付款","1"=>"有余款","2"=>"未实付","3"=>"已付款"),

    /**
     * 回款计划状态
     */
    "return_plan_status"=>array("-1"=>"待处理","0"=>"待收款","1"=>"有余款","2"=>"已收款"),

    /**
     * 是否有发票
     */
    "pay_request_invoice"=>array("0"=>"无","1"=>"有"),

    /**
     * 下游保证金确认附件类型
     */
    "rev_confirm_attachment_type"=>array(
        "1"=>array("id"=>"1","name"=>"收款凭证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 放款申请文件类别
     */
     "pay_request_attachment_type"=>array(
        //"1"=>array("id"=>"1","name"=>"确认单","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "1"=>array("id"=>"1","name"=>"上传发票","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 上传凭证相关的附件
     */
     "payment_attachment_type"=>array(
        "21"=>array("id"=>"21","name"=>"放款凭证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 支付计划状态
     */
    "pay_status"=>array("0"=>"未放款","1"=>"已放款"),

    /**
     * 财务放款状态
     */
    "accountant_pay_status"=>array("0"=>"未实付","1"=>"已实付"),

    /**
     * 还款方式
     */
    "pay_type"=>array("1"=>"等额本息","2"=>"其他"),

    /**
     * 还款周期
     */
    "pay_period"=>array("1"=>"月","2"=>"季度","3"=>"半年","4"=>"年"),

    /**
     * 流动比率区间
     */
    "ratio_flow"=>array("1"=>"130%（含）以上","2"=>"110%（含）- 130%","3"=>"90% （含）- 110%","4"=>"70%（含）- 90%","5"=>"60（含）- 70%"),

    /**
     * 资产负债率区间
     */
    "ratio_asset_liability"=>array("1"=>"30%（含）以下","2"=>"30% - 40%（含）","3"=>"40% - 50%（含）","4"=>"50% - 60%（含）","5"=>"60% - 70%（含）","6"=>"70%以上"),

    /**
     * 速动比率区间
     */
    "rate_turnover"=>array("1"=>"130%（含）以上","2"=>"110%（含）- 130%","3"=>"90% （含）- 110%","4"=>"70%（含）- 90%","5"=>"60（含）- 70%"),
    //array("1"=>"1500%（含）以上","2"=>"1200%（含）- 1500%","3"=>"1000%（含）- 1200%","4"=>"800%（含）- 1000%","5"=>"600%（含）- 800%"),

    /**
     * 租金涵盖比区间
     */
    "rate_rent"=>array("1"=>"40倍（含）以上","2"=>"30倍（含）- 40倍","3"=>"20倍（含）- 30倍","4"=>"15倍（含）- 20倍","5"=>"10倍（含）- 15倍"),

    /**
     * 项目租金涵盖比区间
     */
    "rate_project_rent"=>array("1"=>"1.3倍（含）以上","2"=>"1.1倍（含）- 1.3倍","3"=>"0.9倍（含）- 1.1倍","4"=>"0.7倍（含）- 0.9倍","5"=>"0.5倍（含）- 0.7倍"),

    /**
     * 审核评论对应type
     */
    "check_note_type"=>array("1"=>"承租企业批注","2"=>"供应商批注","3"=>"租赁物清单批注","4"=>"交易方案批注","5"=>"尽调报告批注"),


    /**
     * 流程节点对应actionID
     */
    "node_id_map_action_id"=>array("1"=>"1","2"=>"4","3"=>"6","4"=>"7","5"=>"9","6"=>"11","7"=>"15","8"=>"18","9"=>"20","10"=>"17","11"=>"21","12"=>"23","13"=>"24",),

    "project_progress"=>array(
        "1"=>"项目发起",
        "2"=>"商务确认",
        "3"=>"合同初审",
        "4"=>"项目合同",
        "5"=>"项目结算",
        "6"=>"项目收放款",
        // "100"=>"拒绝项目",
    ),

    /**
     * 放款凭证附件类型
     */
    /*"accountant_pay_attachment_type"=>array(
        "101"=>array("id"=>"101","name"=>"放款凭证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),*/

    /**
    * 分期金额:0、保证金:1、服务费:2、保险费:3
    */
    "finance_amount_type"=>array("1"=>"deposit_amount","2"=>"fee_amount","3"=>"insurance_amount"),

    /**
     * 付款确认状态
     */
    "pay_confirm_status"=>array("0"=>"未确认","2"=>"已确认"),

    /**
     * 还款催收状态
     */
    "remind_status"=>array("1"=>"待收款","2"=>"已收款"),

    /**
     * 还款催收附件类型
     */
    "remind_attachment_type"=>array(
        "1"=>array("id"=>"1","name"=>"通话凭证","maxSize"=>40,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|mp3|"),//下游收款催收附件
        "101"=>array("id"=>"101","name"=>"通话凭证","maxSize"=>40,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|mp3|"),//上游还款催收附件
    ),

    /**
     * 收款确认状态
     */
    "receive_confirm_status"=>array("-1"=>"待处理","0"=>"未确认","1"=>"有余款","2"=>"已确认"),

    /**
     * 收款确认附件类型
     */
    "receive_attachment_type"=>array(
        "51"=>array("id"=>"51","name"=>"收款凭证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),//下游收款确认附件
        "121"=>array("id"=>"121","name"=>"收款凭证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),//上游收款确认附件
    ),

     /**
     * 转账方式
     */
    "transfer_type"=>array("1"=>"银行转账","2"=>"承兑汇票","3"=>"支票"),


    /**
     * 上下游项目结清状态
     */
    "settle_done_status"=>array("1"=>"未结清","2"=>"已结清"),

    /**
     * 发票申请状态  array("-1"=>"待处理","0"=>"待付款","1"=>"有余款","2"=>"审核通过","3"=>"已付款"),
     */
    "request_invoice_status"=>array("-1"=>"待处理","0"=>"未申请","1"=>"有余款","2"=>"已申请"),
    /**
     * 发票状态
     */
    "invoice_request_status"=>array("-1"=>"已驳回","1"=>"财务主管审核中","2"=>"反馈结果中","3"=>"出纳审核中","4"=>"审核通过"),

    /**
    *   开票类型
    */
    "invoice_type"=>array("1"=>"增值税普通发票","2"=>"增值税专用发票"),

    /**
    *   开票对象类型
    */
    "invoice_object_type"=>array("1"=>"上游企业","2"=>"下游企业"),

    /**
     * 发票开具状态
     */
    "invoice_status"=>array("0"=>"未开票","1"=>"已开票"),

    /**
     * 发票凭证附件类型
     */
    "invoice_attachment_type"=>array(
        "51"=>array("id"=>"51","name"=>"发票凭证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 发票快递发出状态
     */
    "express_status"=>array("0"=>"未发快递","1"=>"已发快递"),

    /**
     * 下游发票收到状态
     */
    "invoice_receive_status"=>array("0"=>"未收到","1"=>"已收到"),

    /**
     * 快递单附件类型
     */
    "express_attachment_type"=>array(
        "101"=>array("id"=>"101","name"=>"快递单据","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 下游确认收到发票回执附件类型
     */
    "down_confirm_attachment_type"=>array(
        "201"=>array("id"=>"201","name"=>"发票回执","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 税票条件反馈状态
     */
    "invoice_feedback_status"=>array("0"=>"未反馈","1"=>"已反馈"),

    /**
     * 反馈结果附件类型
     */
    "feedback_attachment_type"=>array(
        "121"=>array("id"=>"121","name"=>"反馈附件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),


    /**
     * 合作方类别
     */
    "partner_type"=>array("0"=>"上下游","1"=>"上游","2"=>"下游"),


    /*
     * 发票催收通话凭证附件类型
     */
    "invoice_remind_attachment_type"=>array(
        "1"=>array("id"=>"1","name"=>"通话凭证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /*
     * 上游发票凭证附件类型
     */
    "up_invoice_attachment_type"=>array(
        "1"=>array("id"=>"1","name"=>"发票凭证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 下游收货确认书附件类型
     */
    /*"down_receive_confirm_type"=>array(
        "301"=>array("id"=>"301","name"=>"下游收货确认书","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),*/

    /*
     * 交易类型
     */
    "trade_type"=>array("1"=>"渠道","2"=>"自营","3"=>"仓单质押"),

    /**
    * 商务确认对应字段
    */
    "business_confirm_map_name"=>array(
        "up_partner_id"=>"up_partner_id",//上游合作方
        "down_partner_id"=>"down_partner_id",//下游合作方
        "manager_name"=>"manager_name",//项目经理
        "corporation"=>"corporation",//交易主体
        "goods_type"=>"goods_type",//交易品种
        "trade_type"=>"trade_type",//交易类型
        "delivery_method"=>"delivery_method",//交货方式
        "conclusion"=>"conclusion",//项目讨论会结论
        "trading_scheme"=>"trading_scheme",//交易方案

    ),

    /**
    *合同初审--上下游合同审核条目
    */

    "contract_up_down_check_type"=>array(
        "1"=>array("display_name"=>"合同编号、签订日期、签订地点审核？","title"=>"合同签订","type"=>1),
        "2"=>array("display_name"=>"月息≥ 1.35%/月？","title"=>"月息审核","type"=>2),
        "3"=>array("display_name"=>"上游交提货方式（送货/自提）与下游交提货方式（送货/自提）是否一致？","title"=>"提货方式","type"=>3),
        "4"=>array("display_name"=>"上游交货地点与下游交货地点是否一致？","title"=>"交货地点","type"=>4),
        "5"=>array("display_name"=>"交提货时间是否一致？","title"=>"提货时间","type"=>5),
        "6"=>array("display_name"=>"货物质量标准是否一致？","title"=>"货物质量","type"=>6),
        "7"=>array("display_name"=>"风险转移条款是否背对背？","title"=>"风险转移","type"=>7),
        "8"=>array("display_name"=>"结算方式是否符合业务情况？","title"=>"结算方式","type"=>8),
        "9"=>array("display_name"=>"付款时间点是否明确？","title"=>"付款时间","type"=>9),
        "10"=>array("display_name"=>"上游开票时间点是否明确？","title"=>"开票时间","type"=>10),
        "11"=>array("display_name"=>"上游付款条件是否符合交易方案与风控原则？","title"=>"上游付款条件","type"=>11),
        "12"=>array("display_name"=>"下游收款条件是否符合交易方案与风控原则？","title"=>"下游付款条件","type"=>12),
        "13"=>array("display_name"=>"违约责任条款是否明确（违约后承担违约金、诉讼费、律师费、评估费以及鉴定费的要求）？","title"=>"违约责任","type"=>13),
        "14"=>array("display_name"=>"上游违约条款是否明确(特别约定；逾期供货；交货条款；货品质量；逾期开票；逾期退款)？","title"=>"上游违约条款","type"=>14),
        "15"=>array("display_name"=>"下游违约条款是否明确（逾期提货；逾期付款；货品质量）？","title"=>"上游违约条款","type"=>15),
        "16"=>array("display_name"=>"争议解决：诉讼管辖是否在原告所在地？","title"=>"争议解决","type"=>16),
        "17"=>array("display_name"=>"合同有效性；原件、扫描件、传真件有效？","title"=>"合同有效性","type"=>17),
    ),

    /**
    *合同初审--下游收货确认书
    */
    "contract_confirmation_check_type"=>array(
        "51"=>array("display_name"=>"签订日期及货物所属合同对应下游合同并与上游合同中要求一致？","title"=>"日期货物等一致","type"=>51),
        "52"=>array("display_name"=>"产品名称，销售单价，运输方式，收货数量，交货地点是否一致？","title"=>"产品单价等一致","type"=>52),
    ),

    /**
    *合同初审--担保合同审核条目
    */
    "contract_warranty_check_type"=>array(
        "101"=>array("display_name"=>"被担保合同的合同编号、合同名称、签订日期、产品名称、合同金额及收款日期与下游合同一致","title"=>"合同一致性","type"=>101),
    ),

    /**
    *合同初审--履约保函
    */
    "contract_guarantee_check_type"=>array(
        "151"=>array("display_name"=>"银行保函单列明的合同名称、合同编号、签订日期、产品名称、合同金额及还款日期与下游合同一致？","title"=>"日期金额等一致","type"=>151),
    ),

    /**
    *合同初审--货权转移证明
    */
    "contract_transfer_check_type"=>array(
        "201"=>array("display_name"=>"转让方、数量、品名、仓库名称与上游合同保持一致？","title"=>"数量品名一致","type"=>201),
        "202"=>array("display_name"=>"转让方及仓库公司均需加盖公章？","title"=>"加盖公章","type"=>201),
    ),

    /**
     * 下游收货确认书类型
     */
    "confirmation_type"=>array("0"=>"否","1"=>"是"),

    /**
     * 下游保证金类型
     */
    "first_pay_type"=>array("0"=>"否","1"=>"是"),

    /**
     * 合同双签类型
     */
    "contract_stamp_type"=>array("0"=>"否","1"=>"是"),

    /**
     * 履约保函类型
     */
    "guarantee_type"=>array("0"=>"否","1"=>"是"),

    /**
     * 担保协议类型
     */
    "assure_type"=>array("0"=>"否","1"=>"是"),

    /**
     * 货权转移证明
     */
    "cargo_transfer_type"=>array("0"=>"否","1"=>"是"),

    /**
     * 下游票款类型
     */
    "down_pay_type"=>array("1"=>"先票后款","2"=>"先款后票"),

    /**
     * 放款条件确认状态
     */
    "pay_condition_status"=>array("0"=>"未确认","1"=>"已确认"),

    /**
     * 签章合同上传状态
     */
    "contract_stamp_status"=>array("0"=>"未上传","1"=>"已上传"),

    /**
    *签章合同--上游采购合同审核条目
    */
    "stamp_up_check_type"=>array(
        "1001"=>array("display_name"=>"两份合同是否一致？","title"=>"是否一致","type"=>1001),
    ),

    /**
    *签章合同--下游销售合同审核条目
    */
    "stamp_down_check_type"=>array(
        "1051"=>array("display_name"=>"两份合同是否一致？","title"=>"是否一致","type"=>1051),
    ),

    /**
    *签章合同--下游收货确认书审核条目
    */
    "stamp_confirmation_check_type"=>array(
        "1101"=>array("display_name"=>"两份合同是否一致？","title"=>"是否一致","type"=>1101),
    ),

    /**
    *签章合同--担保合同审核条目
    */
    "stamp_warranty_check_type"=>array(
        "1151"=>array("display_name"=>"两份合同是否一致？","title"=>"是否一致","type"=>1151),
    ),

    /**
    *签章合同--履约保函
    */
    "stamp_guarantee_check_type"=>array(
        "1201"=>array("display_name"=>"两份合同是否一致？","title"=>"是否一致","type"=>1201),
    ),

    /**
    *签章合同--货权转移证明
    */
    "stamp_transfer_check_type"=>array(
        "1251"=>array("display_name"=>"两份合同是否一致？","title"=>"是否一致","type"=>1251),
    ),

    /**
    *付款条件类型
    */
    "pay_condition_type"=>array(
        "1"=>array("name"=>"下游保证金凭证","map_name"=>"first_pay_type","field"=>"is_down_first","id"=>1,"type"=>"1","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "2"=>array("name"=>"下游收货确认书","map_name"=>"confirmation_type","field"=>"is_down_receive","id"=>2,"type"=>"304","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "3"=>array("name"=>"上游采购合同","map_name"=>"contract_stamp_type","field"=>"is_contract","id"=>3,"type"=>"301","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "4"=>array("name"=>"下游销售合同","map_name"=>"contract_stamp_type","field"=>"is_contract","id"=>4,"type"=>"302","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "5"=>array("name"=>"履约保函","map_name"=>"guarantee_type","field"=>"is_bond","id"=>5,"type"=>"305","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "6"=>array("name"=>"担保合同","map_name"=>"assure_type","field"=>"is_guarantee","id"=>6,"type"=>"303","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "7"=>array("name"=>"货权转移证明","map_name"=>"cargo_transfer_type","field"=>"is_goods","id"=>7,"type"=>"306","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "8"=>array("name"=>"其他付款条件1","field"=>"pay_remark1","id"=>8,"type"=>"309","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "9"=>array("name"=>"其他付款条件2","field"=>"pay_remark2","id"=>9,"type"=>"310","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 放款条件
     */
    "pay_condition"=>array("1"=>"上游发票","2"=>"下游结清","3"=>"其他条件"),

    /**
     * 单据状态
     */
    "receipts_status"=>array("0"=>"已作废","1"=>"已确认"),

    /**
     * 商品状态
     */
    "goods_status"=>array("0"=>"未启用","1"=>"启用",),

    /**
     * 企业白名单状态
     */
    "partner_white_status" => array("0" => "失效", "1" => "生效"),

    /**
     * 合作方企业所有制
     */
    "partner_ownership" => array(
        '1' => '有限责任公司（自然人独资）',
        '2' => '有限责任公司（法人独资）',
        '3' => '有限责任公司（自然人投资或控股）',
        '4' => '有限责任公司（国有独资）',
        '5' => '有限责任公司（外商投资）',
        '6' => '有限责任公司（外商独资）',
        '7' => '股份有限公司',
        '8' => '个人独资企业',
        '9' => '合伙企业',
        '10' => '全民所有制',
        '11' => '集体所有制',
        '12' => '农民专业合作社',
    ),

    /**
     * 企业分级
     */
    "partner_level" => array(
        '1' => 'A类',
        '2' => 'B类',
        '3' => 'C类',
        '4' => 'D类',
    ),


    /************************合作方相关**************************/
    "partner_attachment_type"=>array(
        "1101"=>array("id"=>"1101","name"=>"营业执照","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "1102"=>array("id"=>"1102","name"=>"开户许可证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "1103"=>array("id"=>"1103","name"=>"机构信用代码证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "1104"=>array("id"=>"1104","name"=>"法人身份证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    "partner_apply_attachment_type"=>array(
	    "1201"=>array("id"=>"1201","name"=>"营业执照","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "1225"=>array("id"=>"1225","name"=>"上一年审计报告","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "1210"=>array("id"=>"1210","name"=>"近三年审计报告","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "1226"=>array("id"=>"1226","name"=>"上一年及最近一月财务报表","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "1215"=>array("id"=>"1215","name"=>"主要结算账户近六个月银行流水、对账单","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "1227"=>array("id"=>"1227","name"=>"重要科目明细表","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "1228"=>array("id"=>"1228","name"=>"最近一个月内企业征信报告或征信查询授权书","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "1229"=>array("id"=>"1229","name"=>"企业实际控制人个人征信报告","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "1223"=>array("id"=>"1223","name"=>"以往采购合同和销售合同若干","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1202"=>array("id"=>"1202","name"=>"开户许可证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1203"=>array("id"=>"1203","name"=>"危险化学品经营许可证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1204"=>array("id"=>"1204","name"=>"成品油经营许可证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1205"=>array("id"=>"1205","name"=>"公司简介","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1206"=>array("id"=>"1206","name"=>"法定代表人证明书","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1207"=>array("id"=>"1207","name"=>"身份证复印件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1208"=>array("id"=>"1208","name"=>"公司章程及修正案","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1209"=>array("id"=>"1209","name"=>"验资报告","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1211"=>array("id"=>"1211","name"=>"最近三个月财报","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1212"=>array("id"=>"1212","name"=>"最近一期财务报表附注","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1213"=>array("id"=>"1213","name"=>"近半年增值税纳税申报表","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1214"=>array("id"=>"1214","name"=>"主要银行账户清单","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1216"=>array("id"=>"1216","name"=>"企业长短期借款统计表","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","template"=>"\\\\172.16.1.10\\公共\\09.资产管理组\\03 石化能源公司业务系统\\07 后续功能研发计划\\01 风控准入研发文件\\风控准入评审稿\\讨论资料\\资料提交清单\\企业长短期借款统计表-模板.xlsx"),
//	    "1216"=>array("id"=>"1216","name"=>"企业长短期借款统计表","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","template"=>"../../../templates/企业长短期借款统计表-模板.xlsx"),
//	    "1217"=>array("id"=>"1217","name"=>"企业长短期借款合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1218"=>array("id"=>"1218","name"=>"固定资产明细表","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","template"=>"\\\\172.16.1.10\\公共\\09.资产管理组\\03 石化能源公司业务系统\\07 后续功能研发计划\\01 风控准入研发文件\\风控准入评审稿\\讨论资料\\资料提交清单\\固定资产明细表-模板.xlsx"),
//	    "1218"=>array("id"=>"1218","name"=>"固定资产明细表","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","template"=>"../../../templates/固定资产明细表-模板.xlsx"),
//	    "1219"=>array("id"=>"1219","name"=>"最近一个月内企业征信报告","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1220"=>array("id"=>"1220","name"=>"企业法定代表人、实际控制人和股东个人征信报告","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1221"=>array("id"=>"1221","name"=>"企业对外担保明细","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
//	    "1222"=>array("id"=>"1222","name"=>"前五大供应商和下游客户","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","template"=>"\\\\172.16.1.10\\公共\\09.资产管理组\\03 石化能源公司业务系统\\07 后续功能研发计划\\01 风控准入研发文件\\风控准入评审稿\\讨论资料\\资料提交清单\\前五大供应商和客户-模板.xlsx"),
//	    "1222"=>array("id"=>"1222","name"=>"前五大供应商和下游客户","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","template"=>"../../../templates/前五大供应商和客户-模板.xlsx"),
//	    "1224"=>array("id"=>"1224","name"=>"公司业务流程、业务提成制度","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 合作方附件必填信息，1000
     */
    /*"partner_required_attachment_config"=>array(
        "1"=>array(
            "1"=>array(),
            "2"=>array("1201", "1202", "1203", "1204", "1207", "1208", "1210", "1211", "1212", "1213", "1215", "1216", "1218", "1219", "1222",),
            "3"=>array("1201", "1202", "1203", "1204", "1205", "1207", "1208", "1209", "1210", "1211", "1212", "1213", "1214", "1215", "1216", "1217", "1218", "1219", "1220", "1221", "1222", "1223",),
            "4"=>array(),
        ),
        "2"=>array(
            "1"=>array(),
            "2"=>array("1201", "1202", "1203", "1204", "1207", "1208", "1210", "1211", "1212", "1213", "1215", "1216", "1219", "1222",),
            "3"=>array("1201", "1202", "1203", "1204", "1205", "1207", "1208", "1209", "1210", "1211", "1212", "1213", "1214", "1215", "1216", "1217", "1219", "1220", "1221", "1222", "1223",),
            "4"=>array(),
        ),
    ),*/
    "partner_required_attachment_config"=>array(
        "1"=>array(
            "1"=>array(),
            "2"=>array(
                "1"=>array("1201"),
                "2"=>array("1201","1226"),
                "3"=>array("1201","1225","1226","1227","1229"),
                "4"=>array("1201","1210","1226","1215","1227","1228","1229","1223"),
            ),
            "3"=>array(
                "1"=>array("1201"),
                "2"=>array("1201","1226"),
                "3"=>array("1201","1225","1226","1227","1229"),
                "4"=>array("1201","1210","1226","1215","1227","1228","1229","1223"),
            ),
            "4"=>array(),
        ),
        "2"=>array(
            "1"=>array(),
            "2"=>array(
                "1"=>array("1201"),
                "2"=>array("1201","1226"),
                "3"=>array("1201","1225","1226","1227","1229"),
                "4"=>array("1201","1210","1226","1215","1227","1228","1229","1223"),
            ),
            "3"=>array(
                "1"=>array("1201"),
                "2"=>array("1201","1226"),
                "3"=>array("1201","1225","1226","1227","1229"),
                "4"=>array("1201","1210","1226","1215","1227","1228","1229","1223"),
            ),
            "4"=>array(),
        ),
    ),
    /**
     * 合作方额度申请附件额度分类
     */
    /*"partner_required_attachment_amount_config"=>array(
        "1"=>array(
            "2"=>array(
                "1"=>array(0,1000),
                "2"=>array(1000,3000),
                "3"=>array(3000,5000),
                "4"=>array(5000,99999999),
            ),
            "3"=>array(
                "1"=>array(0,1000),
                "2"=>array(1000,3000),
                "3"=>array(3000,5000),
                "4"=>array(5000,99999999),
            ),
        ),
        "2"=>array(
            "2"=>array(
                "1"=>array(0,1000),
                "2"=>array(1000,3000),
                "3"=>array(3000,5000),
                "4"=>array(5000,99999999),
            ),
            "3"=>array(
                "1"=>array(0,1000),
                "2"=>array(1000,3000),
                "3"=>array(3000,5000),
                "4"=>array(5000,99999999),
            ),
        ),
    ),*/

    /**
     * 企业风控初审附件类型
     */
    "partner_check_attachment_type"=>array(
        "30001"=>array("id"=>"30001","name"=>"初审报告","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "30002"=>array("id"=>"30002","name"=>"其他附件","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "30010"=>array("id"=>"30010","name"=>"附件","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),

    /**
     * 企业风控初审附件类型
     */
    "partner_check_main_attachment_type"=>array(
        "30001"=>array("id"=>"30001","name"=>"初审报告","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","required"=>1),
        "30002"=>array("id"=>"30002","name"=>"其他附件","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),
    /**
     * 额度计算附件
     */
    "partner_check_compute_attachment_type"=>array(
        "30010"=>array("id"=>"30010","name"=>"附件","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),
    /**
     * 现场风控附件
     */
    "partner_risk_attachment_type"=>array(
        "2001"=>array("id"=>"2001","name"=>"银行流水","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "2002"=>array("id"=>"2002","name"=>"财务报表","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "2003"=>array("id"=>"2003","name"=>"风控报告","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        "2004"=>array("id"=>"2004","name"=>"其他","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),
    /**
     * 评审附件
     */
    "partner_review_attachment_type"=>array(
        "3001"=>array("id"=>"3001","name"=>"评审记录","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","required"=>1),
        "3002"=>array("id"=>"3002","name"=>"其他附件","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),
    /**
     * 评审补充资料
     */
    "partner_review_extra_attachment_type"=>array(
        "3201"=>array("id"=>"3201","name"=>"补充资料","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","required"=>1),
    ),
	/**
	 * 合作方白名单操作字段
	 */
    "partner_white_field_name" => array(
        "level" => "企业分级",
        "status" => "状态",
    ),

	/**
	 * 企业经营状态
	 */
    "runs_state" => array(
        "1" => "存续",
        "2" => "在业",
        "3" => "注销",
        "4" => "迁入",
        "5" => "吊销",
        "6" => "迁出",
        "7" => "停业",
        "8" => "清算",
        "9" => "已迁出企业",
        "10" => "开业",
    ),

    "business_type" => array(
        "1" => "生产型企业",
        "2" => "贸易型企业"
    ),

    /**
     * 现场风控状态
     */
//    "partner_risk_status" => array("1" => "未提交", "2" => "已驳回", "3" => "已通过"),
    "partner_risk_status" => array("1" => "未提交", "3" => "已提交"),

    /**
     * 评审的状态
     */
    /*"partner_review_status" => array(
        "0" => "未提交",
        "99" => "通过",
        "-1" => "否决",
        "10" => "补资料需再评审",
        "20" => "补资料无需再评",
        "25" => "资料已补待审核",
        "30" => "补充资料审核中",
        "40" => "补充资料审核通过",
        ),*/

    /**
     * 业务类型
     */
    "partner_business_type" => array("1" => "自营业务", "2" => "渠道业务", "3"=>"货转业务"),

    /**
     * 我方资金来源类型
     */
    "mine_fund_type" => array("1" => "自有资金", "2" => "保理对接", "3"=>"银行授信", "4"=>"暂未确定"),

    /**
     * 企业付款方式
     */
    "partner_pay_type" => array("1" => "TT", "2" => "银行承兑汇票", "3"=>"L/C",),

    /**
     * 会议评审补充资料状态
     */
    "partner_review_info_status" => array("-1"=>"待处理","0" => "未提交", "1"=>"审核中", "2" => "审核驳回", "3"=>"审核通过",),

    /**
     * 补充资料审核状态
     */
    "supply_info_check_type" => array("1" => "待审核", "2" => "审核通过", "4"=>"审核驳回",),

    /**
     * 是否枚举
     */
    "is_or_nor"=>array("1"=>"是","2"=>"否",),


    /**
     * 会议评审状态
     */
    "partner_review_status"=>array("1"=>"待评审","2"=>"已完成",),

    //现场风控其他信息
    "partner_risk_content_info" => array(
        "1" => array(
            "1" => array(
            	"企业概况 （生产型企业）" => array(
	                array("key" => "factory_area", "label" => "厂区面积"),
	                array("key" => "storage", "label" => "仓储能力"),
	                array("key" => "staff_num", "label" => "员工人数"),
	                array("key" => "product_quality", "label" => "产品质量"),
	                array("key" => "equipment", "label" => "生产装置"),
	                array("key" => "competition", "label" => "产品市场竞争力"),
	                array("key" => "production", "label" => "产能"),
	                array("key" => "delivery_type", "label" => "发货运输方式"),
                ),
            ),
            "2" => array(
	            "企业概况（贸易型企业）" => array(
		            array("key" => "reputation", "label" => "行业口碑"),
		            array("key" => "goods_source", "label" => "货物来源"),
		            array("key" => "trade_ability", "label" => "贸易能力"),
	            ),
            )
        ),
	    "2" => array(
		    "企业素质" => array(
			    array("key" => "impression", "label" => "企业印象"),
			    array("key" => "business_reputation", "label" => "行业口碑"),
			    array("key" => "manage_level", "label" => "管理水平"),
			    array("key" => "position", "label" => "行业地位"),
			    array("key" => "staff_quality", "label" => "员工素质"),
			    array("key" => "environment", "label" => "经营环境"),
			    array("key" => "potential", "label" => "发展潜力"),
		    ),
	    ),
        "3" => array(
	        "经营者素质" => array(
		        array("key" => "character", "label" => "品德"),
		        array("key" => "ability", "label" => "能力"),
		        array("key" => "experience", "label" => "行业经验"),
		        array("key" => "runer_manage_level", "label" => "管理水平")
	        ),
        )
    ),


    /**
     * 项目额度占用申请明细状态
     */
    "project_credit_apply_detail_status"=>array("-3"=>"已作废","-2"=>"他人已拒绝","-1"=>"已拒绝","2"=>"待确认","6"=>"已确认","9"=>"已使用",),

    //用户额外信息附件
    "user_extra_attachment_type"=>array(
	    "4001"=>array("id"=>"4001","name"=>"身份证扫描件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","required"=>true),
	    "4002"=>array("id"=>"4002","name"=>"附件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
    ),
	//个人额度信息附件
	"user_credit_attachment_type"=>array(
		"4050"=>array("id"=>"4050","name"=>"附件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
	),

	//性别
    "gender"=>array("1"=>"男", "2"=>"女",),

	//个人额度其他信息
    "user_credit_other_json" => array(
        array("key" => "stock", "label"=>"股票或债券"),
        array("key" => "equity", "label"=>"股权投资"),
        array("key" => "property", "label"=>"房产(土地使用权)"),
	    array("key" => "vehicle", "label"=>"车辆"),
	    array("key" => "liquid_assets", "label"=>"其他流动资产"),
	    array("key" => "fixed_assets", "label"=>"其他固定资产"),
    ),
	
	//合作方申请相关字段
    "partner_apply_fields_name" => array(
	    "name" => "企业名称",
	    "credit_code" => "统一社会信用代码",
	    "registration_code" => "工商注册号",
	    "corporate" => "法定代表人",
	    "start_date" => "成立日期",
	    "address" => "注册地址",
	    "registration_authority" => "登记机关",
	    "registered_capital" => "注册资本",
	    "paid_up_capital" => "实收资本",
	    "business_scope" => "经营范围",
	    "ownership" => "企业所有制", //
	    "runs_state" => "经营状态", //
	    "is_stock" => "是否上市", //
	    "stock_code" => "上市编号",
	    "stock_name" => "上市名称",
	    "stock_type" => "上市板块",
	    "contact_person" => "客户联系人",
	    "contact_phone" => "联系方式",
	    "business_type" => "企业类型", //
	    "product" => "生产产品",
	    "equipment" => "生产装置",
	    "production_scale" => "生产规模",
	    "type" => "类型",         //
	    "apply_amount" => "拟申请额度", //万元
	    "user_id" => "业务员",     //
	    "trade_info" => "历史合作情况",
	    "goods_ids" => "拟合作产品", //
	    "bank_name" => "银行名称",
	    "bank_account" => "银行账号",
	    "tax_code" => "纳税识别号",
	    "phone" => "电话",
	    "remark" => "备注",
	    "custom_level" => "商务强制分类", //
        "auto_level" => "系统分级", //
        "status" => "状态", //
        "level" => "风控分级", //
        "status_time" => "状态更新时间",
        "credit_amount" => "信用额度", //万元
        "update_time" => "更新时间",
        "update_user_id" => "更新用户", //
    ),

    "qichacha_interface_balance_email_alarm_user"=>array(
        array('address'=>'songjun.zhang@jyblife.com','name'=>'songjun.zhang@jyblife.com'),
        array('address'=>'tony.he@jyblife.com','name'=>'tony.he@jyblife.com'),
        array('address'=>'haipeng.zeng@jyblife.com','name'=>'haipeng.zeng@jyblife.com'),
        array('address'=>'wen.he@jyblife.com','name'=>'wen.he@jyblife.com'),
    ),
);
?>
