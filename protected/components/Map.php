<?php
/**
 * Created by youyi000.
 * DateTime: 2017/8/21 15:31
 * Describe：
 */

use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyEnum;
use ddd\Split\Domain\Model\StockSplit\StockSplitEnum;

class Map
{
    public static $v = array(

        /**
         * 系统
         */
        "system_id" => array("11" => "石油管理系统V2.0"),

        /**
         * 系统模块状态
         */
        "module_status" => array("0" => "未启用", "1" => "已启用"),
        /**
         * 系统模块是否公开，即不需要判断权限
         */
        "module_is_public" => array("0" => "不公开", "1" => "公开"),
        /**
         * 系统模块的链接是否外部的，即直接新窗口打开
         */
        "module_is_external" => array("0" => "内部", "1" => "外部"),
        "module_is_menu" => array("0" => "不是菜单", "1" => "是菜单"),

        /**
         * 系统用户状态
         */
        "user_status" => array("0" => "未启用", "1" => "启用"),
        "corporation_status"=>array("0"=>"待确认","1"=>"正常"),
        /**
         * 系统角色状态
         */
        "role_status" => array("0" => "未启用", "1" => "启用"),

        "account_status"=>array("0"=>"失效","1"=>"正常"),

        /**
         * 合作方企业所有制
         */
        "ownership"=>array("1"=>"国有","2"=>"民营"),

        "partner_status" => array(
            "0" => "已保存",
            "9" => "风控初审驳回",
            "10" => "风控初审中",
            // "15"=>"现场风控驳回",
            "25" => "现场风控中",
            "30" => "会议评审中",
            "40" => "补充资料需再评审",
            "45" => "补充资料无需再评",
            "-1" => "评审否决",
            "99" => "评审通过"
        ),

        "partner_status_log" => array(
            "0" => "已保存",
            "9" => "风控初审驳回",
            "10" => "风控初审中",
            // "15"=>"现场风控驳回",
            "25" => "现场风控中",
            "30" => "会议评审中",
            "40" => "补充资料需再评审",
            "45" => "补充资料无需再评",
            "-1" => "评审否决",
            "99" => "评审通过"
        ),

        /**
         * 合作方类别
         */
        "partner_type" => array("1" => "上游", "2" => "下游", "3" => "代理商"),

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
         * 业务类型
         */
        "partner_business_type" => array("1" => "自营业务", "2" => "渠道业务", "3" => "货转业务"),

        /**
         * 我方资金来源类型
         */
        "mine_fund_type" => array("1" => "自有资金", "2" => "保理对接", "3" => "银行授信", "4" => "暂未确定"),

        /**
         * 企业分级
         */
        "partner_level" => array(
            '1' => 'A类',
            '2' => 'B类',
            '3' => 'C类',
            '4' => 'D类',
        ),


        //商品的计量单位
        "goods_unit" => array(
            "2"=>array("id"=>2,"name"=>"吨"),
            "1"=>array("id"=>1,"name"=>"桶"),
            "9"=>array("id"=>9,"name"=>"升"),
            "3"=>array("id"=>3,"name"=>"个"),
            "4"=>array("id"=>4,"name"=>"只"),
            "5"=>array("id"=>5,"name"=>"条"),
            "6"=>array("id"=>6,"name"=>"千克"),
            "7"=>array("id"=>7,"name"=>"立方"),
            "8"=>array("id"=>8,"name"=>"台")
        ),

        //商品的计量单位
        "goods_unit_enum" => array(
            "2"=>"吨",
            "1"=>"桶",
            "9"=>"升",
            "3"=>"个",
            "4"=>"只",
            "5"=>"条",
            "6"=>"千克",
            "7"=>"立方",
            "8"=>"台"
        ),

        "goods_status"=>array("0"=>"未启用","1"=>"启用",),
        //币种及符号
        "currency" => array(
            "1"=>array("id" => 1, "name" => "人民币","ico" => "￥"),
            "2"=>array("id" => 2, "name" => "美元","ico" => "$"),
        ),

        /**************************** 项目相关 ****************************/
        //项目类型
        "project_type" => array(
            "1" => "进口自营",
            "2" => "进口代采",
            "3" => "进口渠道",
            "4" => "内贸自营",
            "5" => "内贸代采",
            "6" => "内贸渠道",
            "7" => "仓单质押"
        ),
        //项目类型
        "project_detail_type" => array(
            "11" => "进口自营-先销后采",
            "12" => "进口自营-先采后销",
            "2" => "进口代采",
            "3" => "进口渠道",
            "41" => "内贸自营-先销后采",
            "42" => "内贸自营-先采后销",
            "5" => "内贸代采",
            "6" => "内贸渠道",
            "7" => "仓单质押"
        ),

        /**
         * 项目配置
         */
        "project_config"=>array(
            "1"=>array("id"=>1,"name"=>"进口自营","is_channel"=>0),
            "2"=>array("id"=>2,"name"=>"进口代采","is_channel"=>1),
            "3"=>array("id"=>3,"name"=>"进口渠道","is_channel"=>1),
            "4"=>array("id"=>4,"name"=>"内贸自营","is_channel"=>0),
            "5"=>array("id"=>5,"name"=>"内贸代采","is_channel"=>1),
            "6"=>array("id"=>6,"name"=>"内贸渠道","is_channel"=>1),
            "7"=>array("id"=>7,"name"=>"仓单质押","is_channel"=>1),
        ),

        //项目状态
        /*"project_status"=>array(
            "-9" => "项目终止",
            "-1" => "审核拒绝",
            "0" => "未提交",
            "1" => "项目撤回",
            "10" => "商务确认中",
            "23" => "合同初审驳回",
            "24" => "合同初审拒绝",
            "25" => "合同初审中",
            "30" => "最终合同",
            "35" => "上下游合同签章",
            "43" => "签章合同审核驳回",
            "44" => "签章合同审核拒绝",
            "45" => "签章合同审核中",
            "50" => "我方合同上传",
            "55" => "双签合同上传",
            "60" => "保证金确认中",
            "61" => "预付款处理中",
            "70" => "预付款已完成",
            "80" => "结算中",
            "81" => "结算审核退回",
            "83" => "结算审核中",
            "85" => "结算审核完成",
            "90" => "项目结清",
            "71" => "上游开票中",
            "72" => "下游开票中",
            "73" => "下游开票审核中",
            "74" => "下游开票审核通过",
            "75" => "税票条件反馈中",
            "76" => "税票条件审核中",
            "77" => "税票条件审核通过",
            "80" => "发票已开具",
            "81" => "发票已发快递",
            "82" => "下游确认收票",
            "99" => "项目完成",
        ),*/

        "project_status"=>array(
            "0" => "未提交",
            "1" => "商务驳回",
            "10" => "已提交",
        ),

        /**
         * 流程节点对应actionID
         */
        "node_id_map_action_id"=>array("1"=>"2","2"=>"6","3"=>"7","4"=>"11","5"=>"13","6"=>"15",),

        //无需判断交易主体的任务actionID
        "not_corporation_map_action_id"=>array(1,2,3,4,5,6,7,8),

        //价格方式
        "price_type" => array("1" => "死价", "2" => "活价（价格为暂估价）"),

        //币种
        "currency_type" => array("1" => "人民币", "2" => "美元"),
        //币种ico
        "currency_ico" => array("1" => "￥", "2" => "$"),

        //项目发起附件信息
        "project_launch_attachment_type"=>array(
            "1"=>array("id"=>"1", "name"=>"立项会决议", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|", 'fileTypeDesc'=>'图片，Excel、word、pdf，压缩包'),
            "2"=>array("id"=>"2", "name"=>"项目预算表", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","required"=>true, 'fileTypeDesc'=>'图片，Excel、word、pdf，压缩包'),
            "3"=>array("id"=>"3", "name"=>"业务说明书", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|", 'fileTypeDesc'=>'图片，Excel、word、pdf，压缩包'),
            "4"=>array("id"=>"4", "name"=>"其他附件", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|", 'fileTypeDesc'=>'图片，Excel、word、pdf，压缩包'),
        ),

        //商务确认附件信息
        "project_budget_attachment_type"=>array(
            "21"=>array("id"=>"21", "name"=>"项目预算表", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),

        //购销顺序
        "purchase_sale_order" => array("1" => "先销后采", "2" => "先采后销"),

        "project_buy_sell_type" => array("2" => '销售', '1' => '采购'),

        //是否主合同
        "is_main_type"=>array("0"=>"否", "1"=>"是"),

        "buy_sell_desc_type"=>array(
            "0"=>array("1"=>"子采购合同", "2"=>"子销售合同"),
            "1"=>"项目主合同"
        ),

        //采销合同类型

        "buy_sell_type"=>array("1"=>"采购合同", "2"=>"销售合同"),


        /**
         * 企业白名单状态
         */
        "partner_white_status" => array("0" => "失效", "1" => "生效"),

        /**
         * 企业准入风控初审状态
         */
        "partner_check_status"=>array("1"=>"待审核","2"=>"审核通过","3"=>"审核拒绝","4"=>"审核驳回","5"=>"需现场风控","6"=>"需评审",),

        /**
         * 现场风控状态
         */
        "partner_risk_status" => array("1" => "未提交", "3" => "已提交"),


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
         * 是否枚举
         */
        "isNor"=>array("1"=>"是","0"=>"否",),

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
            /*"1202"=>array("id"=>"1202","name"=>"开户许可证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1203"=>array("id"=>"1203","name"=>"危险化学品经营许可证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1204"=>array("id"=>"1204","name"=>"成品油经营许可证","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1205"=>array("id"=>"1205","name"=>"公司简介","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1206"=>array("id"=>"1206","name"=>"法定代表人证明书","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1207"=>array("id"=>"1207","name"=>"身份证复印件","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1208"=>array("id"=>"1208","name"=>"公司章程及修正案","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1209"=>array("id"=>"1209","name"=>"验资报告","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1211"=>array("id"=>"1211","name"=>"最近三个月财报","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1212"=>array("id"=>"1212","name"=>"最近一期财务报表附注","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1213"=>array("id"=>"1213","name"=>"近半年增值税纳税申报表","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1214"=>array("id"=>"1214","name"=>"主要银行账户清单","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1216"=>array("id"=>"1216","name"=>"企业长短期借款统计表","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","template"=>"\\\\172.16.1.10\\公共\\09.资产管理组\\03 石化能源公司业务系统\\07 后续功能研发计划\\01 风控准入研发文件\\风控准入评审稿\\讨论资料\\资料提交清单\\企业长短期借款统计表-模板.xlsx"),
            "1216"=>array("id"=>"1216","name"=>"企业长短期借款统计表","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","template"=>"../../../templates/企业长短期借款统计表-模板.xlsx"),
            "1217"=>array("id"=>"1217","name"=>"企业长短期借款合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1218"=>array("id"=>"1218","name"=>"固定资产明细表","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","template"=>"\\\\172.16.1.10\\公共\\09.资产管理组\\03 石化能源公司业务系统\\07 后续功能研发计划\\01 风控准入研发文件\\风控准入评审稿\\讨论资料\\资料提交清单\\固定资产明细表-模板.xlsx"),
            "1218"=>array("id"=>"1218","name"=>"固定资产明细表","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","template"=>"../../../templates/固定资产明细表-模板.xlsx"),
            "1219"=>array("id"=>"1219","name"=>"最近一个月内企业征信报告","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1220"=>array("id"=>"1220","name"=>"企业法定代表人、实际控制人和股东个人征信报告","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1221"=>array("id"=>"1221","name"=>"企业对外担保明细","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "1222"=>array("id"=>"1222","name"=>"前五大供应商和下游客户","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","template"=>"\\\\172.16.1.10\\公共\\09.资产管理组\\03 石化能源公司业务系统\\07 后续功能研发计划\\01 风控准入研发文件\\风控准入评审稿\\讨论资料\\资料提交清单\\前五大供应商和客户-模板.xlsx"),
            "1222"=>array("id"=>"1222","name"=>"前五大供应商和下游客户","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","template"=>"../../../templates/前五大供应商和客户-模板.xlsx"),
            "1224"=>array("id"=>"1224","name"=>"公司业务流程、业务提成制度","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),*/
        ),

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

        // 仓库类型
        // "storehouse_type"=>array(
        //     array("id" => 1, "name" => "自用库"),
        //     array("id" => 2, "name" => "第三方独立库"),
        //     ),
        "storehouse_type"=>array(
            0=>'自用库',
            1=>'第三方独立库',
        ),

        "storehouse_status"=>array(
            -1=>'已驳回',
            0=>'未提交',
            10=>'审批中',
            20=>'审批通过',
        ),

        "storehouse_checkitems_config"=>array(
            array(
                'key'=>'isThirdParty',
                'name'=>'是否第三方库<span class="text-red">*</span>',
                'type'=>'koSelectButtons',
                'required'=>true,
                'options'=>array(
                    array('text'=>'是', 'value'=>'1'),
                    array('text'=>'否', 'value'=>'0'),
                )
            ),
            array(
                'key'=>'isLargeStorehouse',
                'name'=>'库容是否大于十万方<span class="text-red">*</span>',
                'type'=>'koSelectButtons',
                'required'=>true,
                'options'=>array(
                    array('text'=>'是', 'value'=>'1'),array('text'=>'否', 'value'=>'0'),
                )
            ),
            array(
                'key'=>'isTrade',
                'name'=>'仓库是否参与贸易<span class="text-red">*</span>',
                'type'=>'koSelectButtons',
                'required'=>true,
                'options'=>array(
                    array('text'=>'是', 'value'=>'1'),array('text'=>'否', 'value'=>'0'),
                )
            ),
            array(
                'key'=>'hasErp',
                'name'=>'ERP操作系统<span class="text-red">*</span>',
                'type'=>'koSelectButtons',
                'required'=>true,
                'options'=>array(
                    array('text'=>'有', 'value'=>'1'),array('text'=>'无', 'value'=>'0'),
                )
            ),
            array(
                'key'=>'erpCheckable',
                'name'=>'ERP系统查看货权<span class="text-red">*</span>',
                'type'=>'koSelectButtons',
                'required'=>true,
                'options'=>array(
                    array('text'=>'可以', 'value'=>'1'),array('text'=>'不可以', 'value'=>'0'),
                )
            ),
            array(
                'key'=>'checkPort',
                'name'=>'开放ERP系统查看监管端口<span class="text-red">*</span>',
                'type'=>'koSelectButtons',
                'required'=>true,
                'options'=>array(
                    array('text'=>'可以', 'value'=>'1'),array('text'=>'不可以', 'value'=>'0'),
                )
            ),
            array(
                'key'=>'isReliable',
                'name'=>'失信与重大诉讼',
                'type'=>'koTextArea',
                'required'=>false, ),
            array(
                'key'=>'others',
                'name'=>'其他说明',
                'type'=>'koTextArea',
                'required'=>false, ),
        ),

        "riskmanagement_checkitems_config"=>array(),

        "transaction_checkitems_config"=>array(
                array(
                    'id'=>'1',
                    'key'=>'item1',
                    'name'=>'以上业务模式及相关业务标准描述是否清晰准确',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'2',
                    'key'=>'item2',
                    'name'=>'以上业务内容是否真实准确',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'3',
                    'key'=>'item3',
                    'name'=>'本项目内容按要求阐述，予以确认且对以上所述无异议',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
        ),

        /**
         * 商务确认状态
         */
        "business_confirm_status" => array("-2"=>"待处理",  "0" => "未保存", "1"=>"已保存", "10"=>"审核中", "-1" => "已驳回", "19"=>"已审核",),

        /**
         * 风控审核状态
         */
        "risk_management_status" => array("1"=>"待审核", "2"=>"审核通过", "3" => "已驳回"),

        /**
         * 额度管理状态
         */
        "quota_status" => array("19"=>"未填写", "21"=>"已填写"),

        /**
         * 业务审核状态
         */
        "transection_status" => array("1"=>"待审核", "2"=>"审核通过", "3" => "已驳回"),

        //合同回显状态
        "contract_display_status" => array('-1' => '审核驳回', '0' => '已暂存', '1' => '已保存', '10'=>'商务确认', '19' => '风控审核通过', '21' => '额度管理', '29' => '业务审核通过'),

        //额度查询状态
        "contract_quota_search_status" => array('21' => '额度管理', '29' => '业务审核通过'),

        //业务类型
        "project_business_type" => array(
            "1" => array("id"=>"1" ,"name" => "进口自营", "code" => "ZJ","default_contract_category"=>array("1"=>"2","2"=>"4"),"default_currency"=>array("1"=>"2","2"=>"1")),
            "2" => array("id"=>"2" ,"name" => "进口代采", "code" => "JD","default_contract_category"=>array("1"=>"2","2"=>"4"),"default_currency"=>array("1"=>"2","2"=>"1")),
            "3" => array("id"=>"3" ,"name" => "进口渠道", "code" => "JQ","default_contract_category"=>array("1"=>"2","2"=>"4"),"default_currency"=>array("1"=>"2","2"=>"1")),
            "4" => array("id"=>"4" ,"name" => "内贸自营", "code" => "ZN","default_contract_category"=>array("1"=>"3","2"=>"4"),"default_currency"=>array("1"=>"1","2"=>"1")),
            "5" => array("id"=>"5" ,"name" => "内贸代采", "code" => "ND","default_contract_category"=>array("1"=>"3","2"=>"4"),"default_currency"=>array("1"=>"1","2"=>"1")),
            "6" => array("id"=>"6" ,"name" => "内贸渠道", "code" => "NQ","default_contract_category"=>array("1"=>"3","2"=>"4"),"default_currency"=>array("1"=>"1","2"=>"1")),
            "7" => array("id"=>"7" ,"name" => "仓单质押", "code" => "CD","default_contract_category"=>array("1"=>"3","2"=>"4"),"default_currency"=>array("1"=>"1","2"=>"1")),
            "8" => array("id"=>"8" ,"name" => "转口贸易", "code" => "ZK","default_contract_category"=>array("1"=>"3","2"=>"4"),"default_currency"=>array("1"=>"1","2"=>"1")),
            "9" => array("id"=>"9" ,"name" => "内贸套利", "code" => "NT","default_contract_category"=>array("1"=>"3","2"=>"4"),"default_currency"=>array("1"=>"1","2"=>"1")),
        ),
        //合同条件配置
        "contract_config"=>array(
            "1"=>array( //采购合同
                "1"=>array(
                    "id"=>"1" ,"name" => "直接进口合同","code"=>"J",
                    "extra"=>array(
                        "1"=>array("key"=>"start_port", "name"=>"起运港", "type" =>"koInput", "required"=>false,"value" => ""),
                        "2"=>array("key"=>"end_port", "name"=>"目的港", "type" =>"koInput", "required"=>false,"value" => ""),
                        "3"=>array("key"=>"transport_type", "name"=>"运输方式", "type" => "koSelect", "required"=>false,"value" => "0", "options_caption"=>"请选择运输方式", 'items' => array(
                            "1" => array("id"=>"1","name"=>"轨道（铁路）",),
                            "2" => array("id"=>"2","name"=>"海洋",),
                            "3" => array("id"=>"3","name"=>"航空",),
                            "4" => array("id"=>"4","name"=>"公路",),
                            "5" => array("id"=>"5","name"=>"管道",),
                            "6" => array("id"=>"6","name"=>"内河水运",),
                            "7" => array("id"=>"7","name"=>"多式联运",),
                        )),
                        "4"=>array("key"=>"declare_port", "name"=>"报关口岸", "type" =>"koInput", "required"=>false,"value" => ""),
                        //"5"=>array("key"=>"delivery_period", "name"=>"交货期限", "required"=>true, "type" =>"koInput", "value" => ""),

                        "6"=>array("key"=>"delivery_type", "name"=>"交货方式", "required"=>true, "type" => "koSelect", "options_caption"=>"请选择交货方式", "value" => "0", "items"=>array(
                            "1" => array("id"=>"1","name"=>"FOB",),
                            "2" => array("id"=>"2","name"=>"CIF",),
                            "3" => array("id"=>"3","name"=>"CFR",),
                            "4" => array("id"=>"4","name"=>"EXW",),
                            "5" => array("id"=>"5","name"=>"FCA",),
                            "6" => array("id"=>"6","name"=>"FAS",),
                            "7" => array("id"=>"7","name"=>"CPT",),
                            "8" => array("id"=>"8","name"=>"DES",),
                            "9" => array("id"=>"9","name"=>"DEQ",),
                            "10" => array("id"=>"10","name"=>"DDU",),
                            "11" => array("id"=>"11","name"=>"DDP",),
                            "12" => array("id"=>"12","name"=>"CIP",),
                            "13" => array("id"=>"13","name"=>"DAF",),
                        )),

                        "7"=>array("key"=>"pay_type", "name"=>"付款方式", "required"=>true, "type" => "koMultipleSelect", "options_caption"=>"请选择付款方式", "multi"=>true, "value" => "0","items"=>array(
                            "1" => array("id"=>"1","name"=>"L/C",),
                            "2" => array("id"=>"2","name"=>"T/T",),
                            "3" => array("id"=>"3","name"=>"D/P",),
                        )),
                        "8"=>array("key"=>"pay_period", "name"=>"结算方式", "required"=>false, "type" =>"koInput", "value" => ""),
                        "9"=>array("key"=>"insurance_type", "name"=>"保费承担方", "required"=>false, "type" => "koSelect", "value" => "0", "options_caption"=>"请选择保费承担方", "items"=>array(
                            "1" => array("id"=>"1","name"=>"上游",),
                            "2" => array("id"=>"2","name"=>"我方",),
                        )),
                        "10"=>array("key"=>"other", "name"=>"其他事项说明", "type" => "koTextArea", "required"=>false, "value" => ""),
                    )
                ),
                "2"=>array(
                    "id"=>"2" ,"name" => "代理进口合同","code"=>"D",
                    "extra"=>array(
                        "1"=>array("key"=>"start_port", "name"=>"起运港", "type" =>"koInput", "required"=>false, "value" => ""),
                        "2"=>array("key"=>"end_port", "name"=>"目的港", "type" =>"koInput", "required"=>false, "value" => ""),
                        "3"=>array("key"=>"transport_type", "name"=>"运输方式", "type" => "koSelect", "required"=>false, "value" => "0","options_caption"=>"请选择运输方式",'items' => array(
                            "1" => array("id"=>"1", "name"=>"轨道（铁路）",),
                            "2" => array("id"=>"2", "name"=>"海洋",),
                            "3" => array("id"=>"3", "name"=>"航空",),
                            "4" => array("id"=>"4", "name"=>"公路",),
                            "5" => array("id"=>"5", "name"=>"管道",),
                            "6" => array("id"=>"6", "name"=>"内河水运",),
                            "7" => array("id"=>"7", "name"=>"多式联运",),
                        )),
                        "4"=>array("key"=>"declare_port", "name"=>"报关口岸", "type" =>"koInput", "required"=>false, "value" => ""),
                        //"5"=>array("key"=>"delivery_period", "name"=>"交货期限", "required"=>true, "type" =>"koInput", "value" => ""),
                        "6"=>array("key"=>"delivery_type", "name"=>"交货方式", "required"=>true, "type" => "koSelect", "value" => "0", "options_caption"=>"请选择交货方式", "min_value" => "1","items" => array(
                            "1" => array("id"=>"1","name"=>"FOB",),
                            "2" => array("id"=>"2","name"=>"CIF",),
                            "3" => array("id"=>"3","name"=>"CFR",),
                            "4" => array("id"=>"4","name"=>"EXW",),
                            "5" => array("id"=>"5","name"=>"FCA",),
                            "6" => array("id"=>"6","name"=>"FAS",),
                            "7" => array("id"=>"7","name"=>"CPT",),
                            "8" => array("id"=>"8","name"=>"DES",),
                            "9" => array("id"=>"9","name"=>"DEQ",),
                            "10" => array("id"=>"10","name"=>"DDU",),
                            "11" => array("id"=>"11","name"=>"DDP",),
                            "12" => array("id"=>"12","name"=>"CIP",),
                            "13" => array("id"=>"13","name"=>"DAF",),
                        )),

                        "7"=>array("key"=>"pay_type", "name"=>"付款方式", "required"=>true, "type" => "koMultipleSelect", "multi"=>true, "options_caption"=>"请选择付款方式", "value" => "0","items"=>array(
                            "1" => array("id"=>"1","name"=>"L/C",),
                            "2" => array("id"=>"2","name"=>"T/T",),
                            "3" => array("id"=>"3","name"=>"D/P",),
                        )),
                        "8"=>array("key"=>"pay_period", "name"=>"结算方式", "required"=>false, "type" =>"koInput", "value" => ""),
                        "9"=>array("key"=>"insurance_type", "name"=>"保费承担方", "required"=>false, "type" => "koSelect", "options_caption"=>"请选择保费承担方", "value" => "0","items"=>array(
                            "1" => array("id"=>"1","name"=>"上游",),
                            "2" => array("id"=>"2","name"=>"我方",),
                        )),
                        "10"=>array("key"=>"other", "name"=>"其他事项说明", "type" => "koTextArea", "required"=>false, "value" => ""),
                    )
                ),
                "3"=>array(
                    "id"=>"3" ,"name" => "国内采购合同","code"=>"N",
                    "extra"=>array(
                        //"1"=>array("key"=>"delivery_period", "name"=>"交货期限", "required"=>true, "type" =>"koInput", "value" => ""),
                        "2"=>array("key"=>"delivery_type", "name"=>"交货方式", "required"=>true, "type" => "koSelect", "options_caption"=>"请选择交货方式", "value" => "0","items"=>array(
                            "1" => array("id"=>"1","name"=>"上游送货",),
                            "2" => array("id"=>"2","name"=>"我方自提",),
                            "3" => array("id"=>"3","name"=>"货权转移",),
                        )),
                        "3"=>array("key"=>"pay_type", "name"=>"付款方式", "required"=>true, "type" => "koMultipleSelect", "options_caption"=>"请选择付款方式", "value" => "1","items"=>array(
                            "1" => array("id"=>"1","name"=>"银行转款",),
                            "2" => array("id"=>"2","name"=>"支票",),
                            "3" => array("id"=>"3","name"=>"现金",),
                            "4" => array("id"=>"4","name"=>"汇款",),
                            "5" => array("id"=>"5","name"=>"L/C",),
                            "6" => array("id"=>"6","name"=>"T/T",),
                            "7" => array("id"=>"7","name"=>"D/P",),
                        )),
                        "4"=>array("key"=>"pay_period", "name"=>"结算方式", "required"=>false, "type" =>"koInput", "value" => ""),
                        "5"=>array("key"=>"insurance_fee_type", "name"=>"保费承担方", "required"=>false, "type" => "koSelect", "options_caption"=>"请选择保费承担方", "value" => "0","items"=>array(
                            "1" => array("id"=>"1","name"=>"上游",),
                            "2" => array("id"=>"2","name"=>"我方",),
                        )),
                        "6"=>array("key"=>"delivery_fee_type", "name"=>"运费承担方", "required"=>false, "type" => "koSelect", "options_caption"=>"请选择运费承担方", "value" => "0","items"=>array(
                            "1" => array("id"=>"1","name"=>"上游",),
                            "2" => array("id"=>"2","name"=>"我方",),
                        )),
                        "7"=>array("key"=>"quality_check", "name"=>"质量验收依据", "required"=>false, "type" => "koInput", "value" => ""),
                        "8"=>array("key"=>"quantity_settle", "name"=>"数量结算依据", "required"=>false, "type" => "koInput", "value" => ""),
                        "9"=>array("key"=>"other", "name"=>"其他事项说明", "type" => "koTextArea", "required"=>false, "value" => ""),
                    )
                ),
                /*"5"=>array(
                    "id"=>"5" ,"name" => "自动生成的采购合同","code"=>"A",
                ),*/
            ),
            "2"=>array( //销售合同
                "4"=>array(
                    "id"=>"4" ,"name" => "国内销售合同","code"=>"S",
                    "extra"=>array(
                        //"1"=>array("key"=>"delivery_period", "name"=>"交货期限", "required"=>true, "type" =>"koInput", "value" => ""),
                        "2"=>array("key"=>"delivery_type", "name"=>"交货方式", "required"=>true, "type" => "koSelect", "options_caption"=>"请选择交货方式", "value" => "0","items"=>array(
                            "1" => array("id"=>"1","name"=>"我方送货",),
                            "2" => array("id"=>"2","name"=>"下游自提",),
                            "3" => array("id"=>"3","name"=>"货权转移",),
                        )),

                        "3"=>array("key"=>"pay_period", "name"=>"结算方式", "required"=>false, "type" =>"koInput", "value" => ""),
                        "4"=>array("key"=>"insurance_fee_type", "name"=>"保费承担方", "required"=>false, "type" => "koSelect", "options_caption"=>"请选择保费承担方", "value" => "0","items"=>array(
                            "1" => array("id"=>"1","name"=>"下游",),
                            "2" => array("id"=>"2","name"=>"我方",),
                        )),

                        "5"=>array("key"=>"delivery_fee_type", "name"=>"运费承担方", "required"=>false, "type" => "koSelect", "options_caption"=>"请选择运费承担方", "value" => "0","items"=>array(
                            "1" => array("id"=>"1","name"=>"下游",),
                            "2" => array("id"=>"2","name"=>"我方",),
                        )),

                        "6"=>array("key"=>"quality_check", "name"=>"质量验收依据", "required"=>false, "type" => "koInput", "value" => ""),
                        "7"=>array("key"=>"quantity_settle", "name"=>"数量结算依据", "required"=>false, "type" => "koInput", "value" => ""),
                        "8"=>array("key"=>"other", "name"=>"其他事项说明", "type" => "koTextArea", "required"=>false, "value" => ""),
                    )
                ),
                /*"6"=>array(
                    "id"=>"6" ,"name" => "国内销售合同","code"=>"A",
                ),*/
            ),
        ),

        //采购合同类型
        "buy_contract_type"=>array("1"=>"直接进口合同","2"=>"代理进口合同"),

        //采购子合同类型
        "buy_sub_contract_type"=>array("1"=>"直接进口合同","2"=>"代理进口合同","3"=>"国内采购合同" /*, "5"=>"自动生成的采购合同"*/),

        //销售子合同类型
//        "sale_sub_contract_type"=>array("4"=>"国内销售合同", "6"=>"自动生成的销售合同"),
        "sale_sub_contract_type"=>array("4"=>"国内销售合同"),

        //代理模式类型
        "buy_agent_type"=>array("1"=>"购销模式","2"=>"纯代理模式"),

        //代理费计价类型
        "agent_fee_pay_type" => array("1"=>"从量", "2"=>"从价"),

        //上游条款
        "up_item_type"=>array(
            "1"=>array("key"=>"start_port", "name"=>"起运港", "type" =>"text"),
            "2"=>array("key"=>"end_port", "name"=>"目的港", "type" =>"text"),
            "3"=>array("key"=>"transport_type", "name"=>"运输方式", "type" => "select", "map_name" => "transport_type_list",),
            "4"=>array("key"=>"declare_port", "name"=>"报关口岸", "type" =>"text"),
            "5"=>array("key"=>"delivery_type", "name"=>"交货方式", "required"=>true, "type" => "select", "map_name" => "import_delivery_type_list",),
            //"6"=>array("key"=>"delivery_period", "name"=>"交货期限", "required"=>true, "type" =>"text",),
            "7"=>array("key"=>"pay_type", "name"=>"付款方式", "required"=>true, "type" => "select", "multi"=>true, "map_name" => "import_pay_type_list",),
            "8"=>array("key"=>"pay_period", "name"=>"付款期限", "required"=>true, "type" =>"text",),
            "9"=>array("key"=>"insurance_type", "name"=>"保费承担方", "required"=>true, "type" => "select", "map_name" => "fee_assum_type_list",),
            "10"=>array("key"=>"other", "name"=>"其他事项说明", "type" => "textarea"),
        ),

        //下游游条款
        "down_item_type"=>array(
            //"1"=>array("key"=>"delivery_period", "name"=>"交货期限","required"=>true,"type"=>"text",),
            "2"=>array("key"=>"insurance_type", "name"=>"保费承担方","required"=>true,"type"=>"select", "map_name" => "down_fee_type_list",),
            "3"=>array("key"=>"quality_check", "name"=>"质量验收依据","required"=>true,"type"=>"text",),
            "4"=>array("key"=>"quantity_settle", "name"=>"数量结算依据","required"=>true,"type"=>"text",),
            "5"=>array("key"=>"delivery_type", "name"=>"交货方式","required"=>true,"type"=>"select", "map_name" => "delivery_type_list",),
            "6"=>array("key"=>"receive_period", "name"=>"收款期限","required"=>true,"type"=>"text",),
            "7"=>array("key"=>"fee_type", "name"=>"运费承担方","required"=>true,"type"=>"select", "map_name" => "down_fee_type_list",),
            "8"=>array("key"=>"other", "name"=>"其他事项说明","type"=>"textarea",),
        ),

        //下游保费承担&运费承担
        "down_fee_type_list" => array(
            "1" => "下游",
            "2" => "我方",
        ),

        //收支类型
        "receive_pay_type" => array("1" => "收", "2" =>"付"),

        //付款类别
        "pay_type" => array(
            "1"=>array("id" => 1, "name" => "履约保证金"),
            "2"=>array("id" => 2, "name" => "预付款"),
            "3"=>array("id" => 3, "name" => "货款"),
            "4"=>array("id" => 4, "name" => "进口关税增值税保证金"),
            "5"=>array("id" => 5, "name" => "其他","type"=>"input"),
        ),

        //收款类别
        "proceed_type" => array(
            "1"=>array("id" => 1, "name" => "履约保证金"),
            "2"=>array("id" => 2, "name" => "预付款"),
            "3"=>array("id" => 3, "name" => "货款"),
            "5"=>array("id" => 5, "name" => "其他","type"=>"input"),
        ),

        /**
         * 采购合同附件类型
         */
        "contract_file_attachment_type"=>array(
            "1"=>array("id"=>"1","name"=>"合同文本","maxSize"=>30,"fileType"=>"|doc|docx|pdf|"),
            "11"=>array("id"=>"11","name"=>"电子双签合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "21"=>array("id"=>"21","name"=>"纸质双签合同","maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),

        "contract_file_categories"=>array(
            "1" => array( //采购
                "1"=>array("id"=>"1","name"=>"直接进口合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "2"=>array("id"=>"2","name"=>"代理进口合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "3"=>array("id"=>"3","name"=>"国内采购合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "5"=>array("id"=>"5","name"=>"仓储合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "6"=>array("id"=>"6","name"=>"担保合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "7"=>array("id"=>"7","name"=>"保险合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "8"=>array("id"=>"8","name"=>"租赁合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "9"=>array("id"=>"9","name"=>"运输合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "10"=>array("id"=>"10","name"=>"补充协议","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "11"=>array("id"=>"11","name"=>"代理委托合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "12"=>array("id"=>"12","name"=>"商检合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            ),
            "2" => array( //销售
                "4"=>array("id"=>"4","name"=>"国内销售合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "5"=>array("id"=>"5","name"=>"仓储合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "6"=>array("id"=>"6","name"=>"担保合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "7"=>array("id"=>"7","name"=>"保险合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "8"=>array("id"=>"8","name"=>"租赁合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "9"=>array("id"=>"9","name"=>"运输合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "10"=>array("id"=>"10","name"=>"补充协议","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "11"=>array("id"=>"11","name"=>"代理委托合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
                "12"=>array("id"=>"12","name"=>"商检合同","multi"=>1,"maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            ),
        ),

        //合同上传标准类型
        "contract_standard_type"=>array(
            "1"=>array("id"=>"1","name"=>"我方标准"),
            "2"=>array("id"=>"2","name"=>"对方标准"),
            "3"=>array("id"=>"3","name"=>"双方通过"),
            "4"=>array("id"=>"3","name"=>"都不是"),
        ),

        //合同上传状态
        "contract_upload_status" => array("-2" => "审核驳回", "-1" => "已删除", "0"=>"未上传", "1"=>"未提交", "3"=>"待审核", "6"=>"审核通过",),

        //双签合同状态
        "sign_contract_status" => array("0"=>"未上传", "1"=>"未提交", "3"=>"已提交"),

        //合同审核状态
        "contract_check_status" => array("1" => "待审核", "2" => "审核通过", "4"=>"审核驳回",),

        //业务审核状态
        "transection_check_status" => array("1" => "审核通过", "-1"=>"审核驳回",),

        //合同状态
        /*"contract_status" => array('-9' => '合同作废',
                                   '-1' => '风控审核驳回',
                                   '0' => '商务确认已暂存',
                                   '1' => '商务确认已保存',
                                   '2' => '合同撤回',
                                   '10' => '风控审核中',
                                   '19' => '待额度确认提交',
                                   '20' => '业务审核驳回',
                                   '21' => '业务审核中',
                                   '29' => '业务审核通过',
                                   '40' => '最终文件审核中',
                                   '41' => '最终文件审核驳回',
                                   '45' => '最终文件已上传',
                                   '50' => '电子双签文件已上传',
                                   '59' => '纸质双签文件已上传',
                                   '80' => '已结算',
                                   '99' => '合同完成',
        ),*/
        "contract_status" => array('-9' => '合同作废',
                                   '-1' => '审核驳回',
                                   '0' => '商务确认已暂存',
                                   '1' => '商务确认已保存',
                                   '2' => '合同撤回',
                                   '10' => '风控审核中',
                                   '19' => '待额度确认提交',
                                   '21' => '业务审核中',
                                   '29' => '业务审核通过',
                                   '40' => '最终文件审核中',
                                   '41' => '最终文件审核驳回',
                                   '45' => '最终文件已上传',
                                   '50' => '电子双签文件已上传',
                                   '59' => '纸质双签文件已上传',
                                   '70' => '合同结算驳回',
                                   '75' => '合同结算中',
                                   '80' => '合同已结算',
                                   '99' => '合同完成',
        ),

        //合同审核条款
        "contract_check_items_old"=>array(
            "1"=>array(
                array(
                    'id'=>'1',
                    'key'=>'item1',
                    'name'=>'合同编号是否齐备？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'2',
                    'key'=>'item2',
                    'name'=>'合同基本信息是否全面（各方主体信息是否清晰明确、联系人）？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'3',
                    'key'=>'item3',
                    'name'=>'如为委托代理人签署，确认是否有授权书等授权文件，并明确授权权限有权代为签署合同等文本协议？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'4',
                    'key'=>'item4',
                    'name'=>'交付标的(权益)、合同目的、数量、质量标准、验收标准是否齐备明确，不存在模糊、意义不明之描述。销售合同质量指标是否与采购合同质量指标匹配？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'5',
                    'key'=>'item5',
                    'name'=>'是否需要明确在合同中明确对接联系人、联系方式？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'6',
                    'key'=>'item6',
                    'name'=>'交付安排、运输方式、允许损耗、物流地点、物流相关费用支付等条款是否齐备明确？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'7',
                    'key'=>'item7',
                    'name'=>'上下游交付货物数量、质量规格、交付时间是否逻辑顺畅，不存在约定不一致、时间逻辑颠倒等约定？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'8',
                    'key'=>'item8',
                    'name'=>'结算方式是否约定清楚明确，具体打结算工具和方式（如电汇、转账、L/C等）？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'9',
                    'key'=>'item9',
                    'name'=>'付款及开票时间是否明确，付款要件前提是否合理？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'10',
                    'key'=>'item10',
                    'name'=>'点价及锁价要件条款是否齐备明确（时间是否精确到小时）？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'11',
                    'key'=>'item11',
                    'name'=>'标的货物及交付物的风险转移条款是否清晰明确（是否精确到交付方式、地点或时间）？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'12',
                    'key'=>'item12',
                    'name'=>'交易双方权义是否相符。不存在加大我方义务或明确条款不公平之处？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'13',
                    'key'=>'item13',
                    'name'=>'合同约定是否允许向第三方转让合同项下的权利义务（如无明确约定禁止，则为默认可行）？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'14',
                    'key'=>'item14',
                    'name'=>'违约责任条款是否明确（违约后承担违约金、诉讼费、律师费、评估费以及鉴定费的要求）？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'15',
                    'key'=>'item15',
                    'name'=>'是否约定货物留置或义务抵消条款，留置或抵消前提是否合理？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'16',
                    'key'=>'item16',
                    'name'=>'是否约定购买保险条款，保险条款是否指定险种、保额、保费承担方是否明确？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'17',
                    'key'=>'item17',
                    'name'=>'不可抗力条款是否合理，不存在肆意扩大不可抗力范围之情况？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'18',
                    'key'=>'item18',
                    'name'=>'附随义务是否清晰（如货物合格证的交付、检验报告的提供、提供验货盘点便利、相关权益登记协助义务）？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'19',
                    'key'=>'item19',
                    'name'=>'争议解决：诉讼管辖是否在原告所在地，是否约定诉讼解决而非贸易仲裁，仲裁地是否属于我方地点，仲裁不能为终局仲裁？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'20',
                    'key'=>'item20',
                    'name'=>'法律适用是否清晰明确，如涉及国际贸易优先适用哪国法律是否约定清晰合理？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'21',
                    'key'=>'item21',
                    'name'=>'合同是否为附件生效，生效条件是否合理、原件、扫描件、传真件有效？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
            ),
            "2"=>array(
                array(
                    'id'=>'201',
                    'key'=>'item201',
                    'name'=>'法律风险是否可控？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
            ),
        ),

        //合同审核条款--new
        "contract_check_items"=>array(
            "1"=>array(
                array(
                    'id'=>'1',
                    'key'=>'item1',
                    'name'=>'合同编号是否齐备？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'2',
                    'key'=>'item2',
                    'name'=>'合同基本信息是否完善？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'3',
                    'key'=>'item3',
                    'name'=>'交付标的(权益)、合同目的、数量、质量标准、验收标准是否齐备明确，不存在模糊、意义不明之描述。销售合同质量指标是否与采购合同质量指标匹配？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'4',
                    'key'=>'item4',
                    'name'=>'上下游交付货物数量、质量规格、交付时间是否逻辑顺畅，不存在约定不一致、时间逻辑颠倒等约定？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'5',
                    'key'=>'item5',
                    'name'=>'贸易链条是否符合业务模式？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'6',
                    'key'=>'item6',
                    'name'=>'发票由谁开具、开具时间是否合理、税费由谁承担？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'7',
                    'key'=>'item7',
                    'name'=>'违约责任条款是否平等合理？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'8',
                    'key'=>'item8',
                    'name'=>'争议解决条款是否齐备？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
            ),
            "2"=>array(
                array(
                    'id'=>'201',
                    'key'=>'item201',
                    'name'=>'法律风险是否可控？',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'通过', 'value'=>'1'),
                        array('text'=>'拒绝', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
            ),
        ),

        //入库通知单附件信息
        "stock_notice_attachment_type"=>array(
            "1"=>array("id"=>"1", "name"=>"入库通知单", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),

        //入库单附件信息
        "stock_in_attachment_type"=>array(
            "1"=>array("id"=>"1", "name"=>"仓库入库单", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),

        //入库通知单发货方式
        "stock_notice_delivery_type" => array("1" => "经仓", "2" => "直调"),
        //仓库出库单附件信息
        "stock_out_attachment_type"=>array(
            "1"=>array("id"=>"1", "name"=>"仓库出库单", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),
        //锁价维度
        "lock_type" => array("1"=>"按采购合同锁价", "2"=>"按入库通知单锁价"),

        //入库单状态
        "stock_in_status" => array('-5'=>'已作废','-3'=>'已撤回',"-1" => "审核驳回", "0" => "未提交", "10" => "待审核", "20" => "审核通过", "30" => "已结算"),

        //入库通知结算附件信息
        "stock_batch_settlement_type"=>array(
            "1"=>array("id"=>"1", "name"=>"结算单据", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "11"=>array("id"=>"11", "name"=>"其他附件", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),

        //入库单审核状态
        "stock_in_check_status" => array("1" => "待审核", "2" => "审核通过", "4"=>"审核驳回"),

        //入库通知单结算审批状态
        "stock_batch_settle_check_status" => array("1" => "待审核", "2" => "审核通过", "3"=>"审核驳回"),

        "stock_in_settlement_checkitems_config"=>array(),

        //发货单状态
        "delivery_order_status" => array("-1" => "审核驳回", "0" => "未提交", "10" => "待审核", "20" => "审核通过", "30" => "发货单结算", "40" => "结算驳回", "50" => "结算审核通过"),

        //调货单状态
        "cross_order_status"=>array("-1"=>"审核驳回", "1"=>"已保存", "2"=>"待审核", "3"=>"审核通过", "4"=>"审核通过"),
        "cross_list_status"=>array("3"=>"审核通过", "4"=>"已完成"),

        //调货单审核状态
        "cross_order_check_status"=>array("1" => "待审核", "2" => "审核通过", "4"=>"审核驳回"),

        //调货处理描述详情
        "cross_done_desc"=>array("2"=>"变成采购合同", "3"=>"还货"),

        //调货处理方式
        "cross_method_type"=>array("2"=>"变成采购合同", "3"=>"还货"),

        //发货单审核状态
        "delivery_order_check_status" => array("1" => "待审核", "2" => "审核通过", "4"=>"审核驳回"),

        //调货处理单审核状态
        "cross_order_return_check_status"=>array("1" => "待审核", "2" => "审核通过", "4"=>"审核驳回"),

        "cross_order_return_checkitems_config"=>array(),

        //保理状态
        /*"factor_status" => array(
            //"-2" => "已止付",
            "-1" => "已驳回（财务会计）",
            "0" => "未提交",
            "2" => "待确认（财务会计）",
            "5" => "待审核（板块财务负责人）",
            "6" => "已驳回（板块财务负责人）",
            "7" => "待审核（财务负责人）",
            "8" => "已驳回（财务负责人）",
            "9" => "待审核（出纳）",
            "10" => "已驳回（出纳）",
            "20" => "审批通过（待回款）",
            "25" => "回款中",
            "30" => "已回款",
        ),*/

        "factor_detail_status" => array(
            "-1" => "已驳回",
            "0" => "未提交",
            "10" => "待审核",
            "20" => "审核通过",
            "25" => "回款中",
            "30" => "已回款",
        ),

        "factor_status" => array(
            "-10" => "已作废",
            "-1" => "已驳回",
            "0" => "未提交",
            "2" => "待确认",
            "3" => "已确认",
        ),

        //付款申请状态
        "pay_application_status" => array(
            "-20" => '已撤回',
            "-10" => "已作废",
            "0"=>"未提交",
            "1"=>"驳回",
            "10"=>"审核中",
            "20" => "自动实付中",
//            "25" => "自动实付失败",
            "30"=>"审核通过",
            "32" => "手动实付中",
            "35"=>"终止付款",
            "40"=>"已付款",
            "99"=>"完成付款",
        ),
        //付款申请类别
        "pay_application_type" => array(
            "11"=>"合同下付款",
            "13"=>"多合同合并付款",
            "14"=>"项目下付款",
            "15"=>"交易主体下付款",
            "20"=>"后补项目合同付款",
        ),

        //保理附件信息
        "factor_attachment_type"=>array(
            "1"=>array("id"=>"1", "name"=>"保理融资申请书", "multi"=>0, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","required"=>true),
            "2"=>array("id"=>"2", "name"=>"应收账款债权转让通知书", "multi"=>0, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","required"=>true),
            "3"=>array("id"=>"3", "name"=>"收款确认书", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|","required"=>true),
            "4"=>array("id"=>"4", "name"=>"保理服务合同", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "5"=>array("id"=>"5", "name"=>"其他附件", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),
        //付款申请附件
        "pay_application_attachment_type"=>array(
            "1"=>array("id"=>"1", "name"=>"附件", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),

        //发票类型
        "invoice_type"=>array("1"=>"进项票", "2"=>"销项票"),

        //发票进项票类型
        "invoice_input_type"=>array("1"=>"货款进项票", "2"=>"非货款进项票"),

        //发票销项票类型
        "invoice_output_type"=>array("1"=>"货款销项票", "2"=>"非货款销项票"),

        //货款合同类型
        "goods_contract_type"=>array("1"=>"采购合同", "2"=>"销售合同"),

        //合同类型（付款申请、开票等）
        "contract_category"=>array(
            "1"=>"直接进口合同",
            "2"=>"代理进口合同",
            "3"=>"国内采购合同",
            "4"=>"国内销售合同",
            "5"=>"仓储合同",
            "6"=>"担保合同",
            "7"=>"保险合同",
            "8"=>"租赁合同",
            "9"=>"运输合同",
            "10"=>"补充协议",
            "11"=>"代理委托合同",
            "12"=>"其他",
        ),

        //合同类型（付款申请、开票等）采购类型
        "contract_category_buy_type"=>array(
            "1"=>"直接进口合同",
            "2"=>"代理进口合同",
            "3"=>"国内采购合同",
        ),

        //合同类型（付款申请、开票等）销售类型
        "contract_category_sell_type"=>array(
            "4"=>"国内销售合同",
        ),

        //发票申请状态
        "invoice_apply_status"=>array("-1"=>"审核驳回", "1"=>"已保存", "2"=>"待审核", "3"=>"审核通过"),

        //发票申请审核状态
        "invoice_apply_check_status"=>array("1" => "待审核", "2" => "审核通过", "4"=>"审核驳回"),

        //银行流水导入信息
        "bank_flow_file_type"=>array(
            "1"=>array("id"=>"1", "name"=>"excel数据模板", "multi"=>0, "maxSize"=>30,"fileType"=>"|xls|xlsx|"),
        ),

        //保理单审核状态
        "factor_check_status" => array("1" => "待审核", "2" => "审核通过", "4"=>"审核驳回"),

        //付款申请审核状态
        "pay_application_check_status" => array("1" => "待审核", "2" => "审核通过", "4"=>"审核驳回"),

        //增值税发票类型 --货款类
        "vat_invoice_type"=>array("1"=>"常规增值税专用票", "2"=>"海关增值税专用票", "3"=>"常规增值税普通发票", "4"=>"海关进口关税专用缴款书"),
        //--非货款类
        "non_vat_invoice_type"=>array("1"=>"常规增值税专用票", "3"=>"常规增值税普通发票"),

        //销项票税票类型
        "output_invoice_type"=>array("1"=>"增值税专用发票", "2"=>"增值税普通发票"),

        //发票申请附件
        "invoice_application_attachment_type"=>array(
            "1"=>array("id"=>"1", "name"=>"附件", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),

        //商品的发票税率
        "goods_invoice_rate" => array(
            "1"=>array("id"=>1,"name"=>"3%",  "value"=>"0.03"),
            "2"=>array("id"=>2,"name"=>"6%",  "value"=>"0.06"),
            "5"=>array("id"=>5,"name"=>"16%", "value"=>"0.16"),
            "3"=>array("id"=>3,"name"=>"17%", "value"=>"0.17"),
            "4"=>array("id"=>4,"name"=>"其他","type"=>"input"),
        ),

        // 流水认领用途
        "receive_confirm_usage_type"=>array("1"=>"预收保证金", "2"=>"收货款", "3"=>"收保理款", "4"=>"运费", "5"=>"仓储费", "6"=>"代理费", "7"=>"杂费", "8"=>"税款保证金", "9"=>"收履约保证金", "10"=>"收预付款"),

        "receive_confirm_file_type"=>array(
            "1"=>array("id"=>"1", "name"=>"附件", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),

        //保理回款状态
        "factor_return_status" => array("20" => "待回款", "25" => "回款中", "30" => "已回款"),

        /**
         * 付款申请额外信息
         */
        "pay_application_extra"=>array(
            "1"=>array("id"=>1,"name"=>"无项目付款","value"=>""),
            "2"=>array("id"=>2,"name"=>"无合同付款","value"=>""),
            "3"=>array("id"=>3,"name"=>"收款单位第一次付款","value"=>""),
            "4"=>array("id"=>4,"name"=>"付款金额＞未付款金额10%","value"=>""),
            "5"=>array("id"=>5,"name"=>"未付金额≤0","value"=>""),

        ),


        //审核结果枚举
        "check_status" => array("1" => "审核通过", "-1"=>"审核驳回", "0"=>"审核退回","-2" => "已撤回"),
        //审核列表页面查询条件的审核状态枚举
        "search_check_status" => array("1" => "待审核", "2" => "审核通过", "4"=>"审核驳回"),

        //付款申请额外审核项
        "pay_application_check_extra"=>array(
            "11"=>array(
                array(
                    'id'=>'1',
                    'key'=>'item1',
                    'name'=>'付款计划是否按要求操作',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'2',
                    'key'=>'item2',
                    'name'=>'付款金额填写是否准确',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'3',
                    'key'=>'item3',
                    'name'=>'付款原因是否清晰',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
            ),
            "14"=>array(
                array(
                    'id'=>'4',
                    'key'=>'item4',
                    'name'=>'付款申请附件是否齐全',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'5',
                    'key'=>'item5',
                    'name'=>'收款单位与合同合作方是否一致',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'6',
                    'key'=>'item6',
                    'name'=>'与合同约定付款时间、金额、币种是否一致',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
            ),
            "6"=>array(
                array(
                    'id'=>'7',
                    'key'=>'item7',
                    'name'=>'此次付款金额是否在交易主体信用额度内',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'8',
                    'key'=>'item8',
                    'name'=>'付款条件是否与合同条款匹配',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'9',
                    'key'=>'item9',
                    'name'=>'付款申请附件是否齐全',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
            ),
            "12"=>array(
                array(
                    'id'=>'10',
                    'key'=>'item10',
                    'name'=>'付款申请附件是否齐全',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'11',
                    'key'=>'item11',
                    'name'=>'收款单位与合同合作方是否一致',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'12',
                    'key'=>'item12',
                    'name'=>'与合同约定付款时间、金额、币种是否一致',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
            ),
            "8"=>array(
                array(
                    'id'=>'13',
                    'key'=>'item13',
                    'name'=>'收款单位是否与合同一致',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'14',
                    'key'=>'item14',
                    'name'=>'账户信息是否与合同一致',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'15',
                    'key'=>'item15',
                    'name'=>'本次付款金额是否与合同一致',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
            ),
            "3"=>array(
                array(
                    'id'=>'16',
                    'key'=>'item16',
                    'name'=>'付款金额是否符合合同规定并达到付款条件',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'17',
                    'key'=>'item17',
                    'name'=>'付款合同是否与真实货物流通匹配',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
                array(
                    'id'=>'18',
                    'key'=>'item18',
                    'name'=>'货物质量、指标是否符合合同及附件要求',
                    'type'=>'koSelectButtonsWithRemark',
                    'required'=>true,
                    'options'=>array(
                        array('text'=>'是', 'value'=>'1'),
                        array('text'=>'否', 'value'=>'0','css'=>"btn-danger"),
                    ),
                    'remark_required'=>array('0'),
                ),
            ),
        ),


        'bank_flow_status' =>array (
            '0'=>'新建',
            '1'=>'待认领',
            '2'=>'认领完成',
        ),

        'receive_confirm_status' =>array (
            '-1'=>'作废',
            '0'=>'新建',
            '1'=>'已提交',
        ),

        'pay_claim_status' =>array (
            '-1'=>'作废',
            '0'=>'新建',
            '1'=>'已提交',
        ),


        //付款实付附件
        "payment_attachment_type"=>array(
            "11"=>array("id"=>"11", "name"=>"放款凭证", "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),

        //付款止付附件
        "pay_stop_attachment_type"=>array(
            "21"=>array("id"=>"21", "name"=>"附件", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),

        'factor_returned_status' => array('0'=>'未提交', '1'=>'已提交'),

        //付款申请币种
        "pay_currency" => array("1"=>"人民币", "2"=>"美元"),

        //实付状态
        "pay_confirm_status"=>array("0"=>"未实付", "1"=>"已实付", "2" => '止付审核中', '3' => '已止付'),

        //止付状态
        "pay_stop_status" => array("-9" => "已作废", "-1" => "已驳回", "1"=>"审核中", "2"=>"已止付"),

        //审核状态
        "pay_stop_check_status" => array("1" => "待审核", "2" => "审核通过", "4"=>"审核驳回"),

        //开票状态
        "invoice_open_status"=>array("0"=>"未开票", "1"=>"已开票"),

        //库存盘点证明
        "stock_inventory_attachment" => array(
            "1"=>array("id"=>"1", "name"=>"盘点证明", "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),

        //库存盘点状态
        "stock_inventory_status" => array("-1" => "审核驳回","0"=>"未提交","10"=>"待审核","20"=>"审核通过"),

        //审核状态
        "business_flow_check_status" => array("1" => "待审核", "2" => "审核通过", "4"=>"审核驳回"),

        //出库单状态
        "stock_out_status"=>array('-5'=>'已作废','-3'=>'已撤回', "-1" => "审核驳回","0"=>"未提交", '10'=>'待审核', "1"=>"审核通过", "30" => "已结算"),

        //出库单审核状态
        "stock_out_check_status" => array('1'=>'待审核',"2"=>"审核通过", "4" => "审核驳回"),

        //保理对接款状态
        "factor_amount_status" => array("2" => "待确认", "3" => "已确认"),

        //保理类型
        "factor_code_type" => array("0" => "内部保理", "1" => "外部保理"),

        //出库单结算状态
        "delivery_settlement_status" => array("-2" => "待处理", "-1" => "已驳回", "0"=>"未提交", "1"=>"审核中", "2"=>"审核通过"),

        //发货单附件、出库单附件
        "stock_delivery_attachment" => array(
            "1"=>array("id"=>"1", "name"=>"发货单", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "2"=>array("id"=>"2", "name"=>"仓库出库单", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),
        //发货单结算附件
        "delivery_settlement_attachment" => array(
            "3"=>array("id"=>"3", "name"=>"结算单据", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "4"=>array("id"=>"4", "name"=>"其他附件", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),

        //合同结算附件  货款
        "contract_settlement_attachment" => array(
            "1"=>array("id"=>"1", "name"=>"结算单据", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "2"=>array("id"=>"2", "name"=>"其他附件", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
            "101"=>array("id"=>"101", "name"=>"单据", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),
        //合同结算附件 非货款
        "contract_settlement_other_attachment" => array(
            "101"=>array("id"=>"101", "name"=>"结算单据", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),
        //认领状态
        "pay_claim_status" => array("1" => "待认领", "2" => "已认领"),

        "finance_subjects" => array("1" => '货款', '2' => '保理款', '3' => '服务费', '4' => '运费', '5' => '仓储费', '6' => '税款保证金', '7' => '履约保证金', '8' => '预付款', '9' => '杂费', '10' => '代理费'),
        //合同结算币种
        "contract_settlement_currency"=>array(
            1=>array("id"=>1,"name"=>"人民币","sign"=>"￥"),
            2=>array("id"=>2,"name"=>"美元","sign"=>"$"),
        ),
        //合同结算税收科目
        "contract_settlement_tax"=>array(
            0=>array('id'=>1,'name'=>'消费税'),
            1=>array('id'=>2,'name'=>'增值税'),
            2=>array('id'=>3,'name'=>'关税')
        ),
        //合同结算其他费用科目
        "contract_settlement_other"=>array(
            0=>array('id'=>1,'name'=>'仓储费'),
            1=>array('id'=>2,'name'=>'港建费'),
            2=>array('id'=>3,'name'=>'港务费'),
            3=>array('id'=>4,'name'=>'港口设施保安费'),
            4=>array('id'=>5,'name'=>'滞港费'),
            5=>array('id'=>6,'name'=>'滞期费'),
            6=>array('id'=>7,'name'=>'报关费'),
            7=>array('id'=>8,'name'=>'运费'),
            8=>array('id'=>9,'name'=>'保险费'),
            9=>array('id'=>10,'name'=>'开证费'),
            10=>array('id'=>11,'name'=>'承兑费'),
            11=>array('id'=>12,'name'=>'代理费'),
            12=>array('id'=>13,'name'=>'消费税（从量征）'),
        ),
        //合同结算非货款科目
        "contract_settlement_subject"=>array(
            0=>array('id'=>1,'name'=>'银行手续费'),
            1=>array('id'=>2,'name'=>'代理费'),
            2=>array('id'=>3,'name'=>'杂费'),
        ),
        //合同结算调整方式
        "contract_settlement_adjust"=>array(
            1=>array('id'=>1,'name'=>'调增'),
            2=>array('id'=>2,'name'=>'调减'),
        ),
        //风控额度预警报表 状态
        'risk_amount_warning_status'=>array(
            0=>'正常',
            -1=>'催收'
        ),
        //合同最终交货/收货日期类型
        'contract_delivery_mode'=>array(
            0=>'系统默认',
            1=>'合同约定'
        ),

        //合同拆分附件
        "contract_split_attachment"=>array(
            "1"=>array("id"=>"1", "name"=>"附件", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),
        //出入库拆分附件
        "stock_split_attachment"=>array(
            "1"=>array("id"=>"1", "name"=>"附件", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),
        'payment_interest_status'=>array(0=>'正常', 3=>'已停息'),


        //审核流的状态map
        "flow_check_status_map"=> [1=>'待审核',2=>'审核通过',4=>"审核驳回"],
        //是否平移生成
        "split_type_enum" => array("0" => "否", "1" => "是"),
        //平移状态
        "stock_bill_split_status" => array(StockSplitEnum::STATUS_INVALID =>'',StockSplitEnum::STATUS_NEW=>'已保存',StockSplitEnum::STATUS_SUBMIT=>"已提交",StockSplitEnum::STATUS_BACK=>'已驳回',StockSplitEnum::STATUS_PASS=>"已审核"),
        //平移审核状态
        "stock_bill_split_check_status" => [StockSplitEnum::STATUS_SUBMIT=>"待审核",StockSplitEnum::STATUS_BACK=>'已提交',StockSplitEnum::STATUS_PASS=>"已审核"],

        //合同终止状态
        "contract_terminate_status" => array(-10=>'未编辑',-1=>'审核驳回',0=>'待提交',1=>"已提交",10=>"审核通过"),
        "contract_terminate_check_status" => array(1=>'待审核',2=>'审核通过',3=>"审核驳回"),
        //合同终止附件
        "contract_terminate_attachment"=>array(
            "1"=>array("id"=>"1", "name"=>"附件", "multi"=>1, "maxSize"=>30,"fileType"=>"|jpg|png|jpeg|bmp|gif|doc|docx|xls|xlsx|pdf|zip|rar|"),
        ),

        "contract_split_apply_status" => [
            ContractSplitApplyEnum::STATUS_BACK=>'审核驳回',
            ContractSplitApplyEnum::STATUS_NEW=>'未提交',
            ContractSplitApplyEnum::STATUS_SUBMIT=>"待审核",
            ContractSplitApplyEnum::STATUS_PASS=>"审核通过"
        ],
        "contract_split_apply_check_status" => [
            ContractSplitApplyEnum::STATUS_BACK=>'审核驳回',
            ContractSplitApplyEnum::STATUS_NEW=>'未提交',
            ContractSplitApplyEnum::STATUS_SUBMIT=>"待审核",
            ContractSplitApplyEnum::STATUS_PASS=>"审核通过",
            ContractSplitApplyEnum::STATUS_CAN_STOCK_SPLIT =>"审核通过"
        ],
    );

    public static function getMaps(Array $keys) {
        $data = array();
        foreach ($keys as $key) {
            $data[$key] = self::$v[$key];
        }

        return $data;
    }

    public static function getStatusName($key,$status){
        return self::$v[$key][$status] ?? "";
    }
}
