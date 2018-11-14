<?php
/**
 * Created by youyi000.
 * DateTime: 2017/12/22 15:48
 * Describe：
 */

class DataShowConfig
{
    public static $config=array(
        //采购合同
        '1' => array(
            'sql' => 'select {fields} from v_purchase_contract {where}',
            'config' => array(
                'fields' => '*, case when settle_amount>0 then settle_amount/settle_quantity else 0 end settle_price, 
                             amount_paid+amount_claim actual_paid_amount'
            ),
            "search_items"=>array(
                array('type'=>'text','key'=>'project_code','text'=>'项目编号'),
                array('type'=>'text','key'=>'contract_code','text'=>'合同编号'),
                array('type'=>'text','key'=>'code_out','text'=>'外部合同编号'),
                array('type'=>'text','key'=>'corp_name*','text'=>'交易主体'),
                array('type'=>'text','key'=>'goods_name*','text'=>'品名'),
                array('type'=>'text','key'=>'manager_name*','text'=>'负责人'),
                array('type'=>'text','key'=>'partner_name*','text'=>'上游合作方'),
                array('type' => 'select', 'key' => 'type', 'map_name' => 'project_type', 'text' => '项目类型'),
                array('type' => 'select', 'key' => 'status', 'map_name' => 'contract_status', 'text' => '合同状态')
            ),
            "columns"=>array(
                'detail_id:text:序号:width:80px;text-align:center;',
                'project_code:text:项目编号:width:120px;text-align:left;',
                'contract_code:text:采购合同编号:width:120px;text-align:left;',
                'code_out:text:外部合同编号:width:120px;text-align:left;',
                'corp_name:text:交易主体:width:120px;text-align:left;',
                array(
                    'class'=>'ZEnumColumn',
                    'key'=>'project_type',
                    'name'=>'type',
                    'header'=>'项目类型',
                ),
                array(
                    'class'=>'ZEnumColumn',
                    'key'=>'contract_status',
                    'name'=>'status',
                    'header'=>'合同状态',
                ),
                'manager_name:text:负责人:width:80px;text-align:center;',
                'goods_name:text:品名:width:120px;text-align:center;',
                'partner_name:text:上游合作方:width:120px;text-align:left;',
                'contract_date:text:合同签订日期:width:120px;text-align:center;',
                'quantity:text:采购数量:width:120px;text-align:right;',
                array(
                    'class'=>'ZEnumColumn',
                    'key'=>'goods_unit_enum',
                    'name'=>'unit',
                    'header'=>'单位',
                ),
                'price:amount:暂定价:width:120px;text-align:right;',
                'amount_cny:amount:采购金额:width:120px;text-align:right;',
                'settle_quantity:text:结算数量:width:120px;text-align:right;',
                'settle_price:amount:结算价:width:120px;text-align:right;',
                'settle_amount:amount:应付金额:width:120px;text-align:right;',
                'actual_paid_amount:amount:已付款:width:120px;text-align:right;',
                'invoice_quantity:text:已开票吨位:width:120px;text-align:right;',
                'invoice_amount:amount:已开票金额:width:120px;text-align:right;'
            ),
            'is_export'=>1,//是否导出，1为有，0为没有
            'export_fields' => 'detail_id 序号, project_code 项目编号, contract_code 采购合同编号, code_out 外部合同编号, corp_name 交易主体, type 项目类型, status 合同状态, manager_name 负责人, 
                                goods_name 品名, partner_name 上游合作方, contract_date 合同签订日期, quantity 采购数量, unit 单位, price/100 暂定价, amount_cny/100 采购金额, 
                                settle_quantity 结算数量, case when settle_amount>0 then settle_amount/settle_quantity/100 else 0 end 结算价, settle_amount/100 应付金额, 
                                (amount_paid+amount_claim)/100 已付款, invoice_quantity 已开票吨位, invoice_amount/100 已开票金额',
            'map_keys' => array(
                'status' => array(
                    'name' => '合同状态',
                    'map_key' => 'contract_status'
                ),
                'type' => array(
                    'name' => '项目类型',
                    'map_key' => 'project_type'
                ),
                'unit' => array(
                    'name' => '单位',
                    'map_key' => 'goods_unit_enum'
                )
            ),
        ),

        //销售合同
        '2' => array(
            'sql' => 'select {fields} from v_sale_contract {where}',
            'config' => array(
                'fields' => '*, case when settle_amount>0 then settle_amount/settle_quantity else 0 end settle_price'
            ),
            "search_items"=>array(
                array('type'=>'text','key'=>'project_code','text'=>'项目编号'),
                array('type'=>'text','key'=>'contract_code','text'=>'合同编号'),
                array('type'=>'text','key'=>'code_out','text'=>'外部合同编号'),
                array('type'=>'text','key'=>'corp_name*','text'=>'交易主体'),
                array('type'=>'text','key'=>'goods_name*','text'=>'品名'),
                array('type'=>'text','key'=>'manager_name*','text'=>'负责人'),
                array('type'=>'text','key'=>'partner_name*','text'=>'下游合作方'),
                array('type' => 'select', 'key' => 'type', 'map_name' => 'project_type', 'text' => '项目类型'),
                array('type' => 'select', 'key' => 'status', 'map_name' => 'contract_status', 'text' => '合同状态')
            ),
            "columns"=>array(
                'detail_id:text:序号:width:80px;text-align:center;',
                'project_code:text:项目编号:width:120px;text-align:left;',
                'contract_code:text:销售合同编号:width:120px;text-align:left;',
                'code_out:text:外部合同编号:width:120px;text-align:left;',
                'corp_name:text:交易主体:width:120px;text-align:left;',
                array(
                    'class'=>'ZEnumColumn',
                    'key'=>'project_type',
                    'name'=>'type',
                    'header'=>'项目类型',
                ),
                array(
                    'class'=>'ZEnumColumn',
                    'key'=>'contract_status',
                    'name'=>'status',
                    'header'=>'合同状态',
                ),
                'manager_name:text:负责人:width:80px;text-align:center;',
                'goods_name:text:品名:width:120px;text-align:center;',
                'partner_name:text:下游合作方:width:120px;text-align:left;',
                'contract_date:text:合同签订日期:width:120px;text-align:center;',
                'quantity:text:销售数量:width:120px;text-align:right;',
                array(
                    'class'=>'ZEnumColumn',
                    'key'=>'goods_unit_enum',
                    'name'=>'unit',
                    'header'=>'单位',
                ),
                'price:amount:暂定价:width:120px;text-align:right;',
                'amount_cny:amount:销售金额:width:120px;text-align:right;',
                'settle_quantity:text:结算数量:width:120px;text-align:right;',
                'settle_price:amount:结算价:width:120px;text-align:right;',
                'settle_amount:amount:应收金额:width:120px;text-align:right;',
                'amount_received:amount:已收款:width:120px;text-align:right;',
                'invoice_quantity:text:已开票吨位:width:120px;text-align:right;',
                'invoice_amount:amount:已开票金额:width:120px;text-align:right;',
            ),
            'is_export'=>1,//是否导出，1为有，0为没有
            'export_fields' => 'detail_id 序号, project_code 项目编号, contract_code 销售合同编号, code_out 外部合同编号, corp_name 交易主体, type 项目类型, status 合同状态, manager_name 负责人, 
                                goods_name 品名, partner_name 下游合作方, contract_date 合同签订日期, quantity 销售数量, unit 单位, price/100 暂定价, amount_cny/100 销售金额, 
                                settle_quantity 结算数量, case when settle_amount>0 then settle_amount/settle_quantity/100 else 0 end 结算价, settle_amount/100 应收金额, 
                                amount_received/100 已收款, invoice_quantity 已开票吨位, invoice_amount/100 已开票金额',
            'map_keys' => array(
                'status' => array(
                    'name' => '合同状态',
                    'map_key' => 'contract_status'
                ),
                'type' => array(
                    'name' => '项目类型',
                    'map_key' => 'project_type'
                ),
                'unit' => array(
                    'name' => '单位',
                    'map_key' => 'goods_unit_enum'
                )
            )
        ),

        //入库
        '3' => array(
            'sql' => 'select {fields} from v_in_stock {where}',
            'config' => array(
                'fields' => '*, case when settle_amount>0 then settle_amount/settle_quantity else 0 end settle_price, 
                             case when settle_amount>0 then round(quantity_in*settle_amount/settle_quantity) else round(quantity_in*price) end amount_stock'
            ),
            "search_items"=>array(
                array('type' => 'text','key' => 'project_code','text' => '项目编号'),
                array('type' => 'text','key' => 'contract_code','text' => '合同编号'),
                array('type' => 'text','key' => 'corp_name*','text' => '交易主体'),
                array('type' => 'text','key' => 'goods_name*','text' => '品名'),
                array('type' => 'text','key' => 'partner_name*','text' => '上游合作方'),
                array('type' => 'text','key' => 'store_name*','text' => '仓库'),
                array('type' => 'select', 'key' => 'type', 'map_name' => 'project_type', 'text' => '项目类型'),
            ),
            "columns"=>array(
                'stock_id:text:序号:width:80px;text-align:center;',
                'project_code:text:项目编号:width:120px;text-align:left;',
                'contract_code:text:合同编号:width:120px;text-align:left;',
                'corp_name:text:交易主体:width:120px;text-align:left;',
                array(
                    'class'=>'ZEnumColumn',
                    'key'=>'project_type',
                    'name'=>'type',
                    'header'=>'项目类型',
                ),
                'goods_name:text:品名:width:120px;text-align:center;',
                'partner_name:text:上游合作方:width:120px;text-align:left;',
                'quantity:text:采购数量:width:120px;text-align:right;',
                array(
                    'class'=>'ZEnumColumn',
                    'key'=>'goods_unit_enum',
                    'name'=>'contract_unit',
                    'header'=>'采购单位',
                ),
                'price:amount:暂定价:width:120px;text-align:right;',
                'amount_cny:amount:采购金额:width:120px;text-align:right;',
                'settle_quantity:text:结算数量:width:120px;text-align:right;',
                'settle_price:amount:结算价:width:120px;text-align:right;',
                'settle_amount:amount:结算金额:width:120px;text-align:right;',
                'entry_date:text:入库时间:width:120px;text-align:center;',
                'store_name:text:入库仓库:width:120px;text-align:left;',
                'stock_quantity:text:入库数量:width:120px;text-align:right;',
                array(
                    'class'=>'ZEnumColumn',
                    'key'=>'goods_unit_enum',
                    'name'=>'unit',
                    'header'=>'入库单位',
                ),
                'amount_stock:amount:入库金额:width:120px;text-align:right;',
                'remark:text:备注:width:120px;text-align:right;'
            ),
            'is_export'=>1,//是否导出，1为有，0为没有
            'export_fields' => 'stock_id 序号, project_code 项目编号, contract_code 合同编号, corp_name 交易主体, type 项目类型, goods_name 品名, 
                                partner_name 上游合作方, quantity 采购数量, contract_unit 采购单位, price/100 暂定价, amount_cny/100 采购金额, settle_quantity 结算数量, 
                                case when settle_amount>0 then settle_amount/settle_quantity/100 else 0 end 结算价, settle_amount/100 结算金额, 
                                entry_date 入库日期, store_name 入库仓库, stock_quantity 入库数量, unit 入库单位, 
                                case when settle_amount>0 then round(quantity_in*settle_amount/settle_quantity)/100 else round(quantity_in*price)/100 end 入库金额, 
                                remark 备注',
            'map_keys' => array(
                'type' => array(
                    'name' => '项目类型',
                    'map_key' => 'project_type'
                ),
                'contract_unit' => array(
                    'name' => '采购单位',
                    'map_key' => 'goods_unit_enum'
                ),
                'unit' => array(
                    'name' => '入库单位',
                    'map_key' => 'goods_unit_enum'
                )
            )
        ),

        //出库
        '4' => array(
            'sql' => 'select {fields} from v_out_stock {where}',
            'config' => array(
                'fields' => '*, case when settle_amount>0 then settle_amount/settle_quantity else 0 end settle_price, 
                             case when settle_amount>0 then round(quantity_out*settle_amount/settle_quantity) else round(quantity_out*price) end amount_out'
            ),
            "search_items"=>array(
                array('type' => 'text','key' => 'project_code','text' => '项目编号'),
                array('type' => 'text','key' => 'contract_code','text' => '合同编号'),
                array('type' => 'text','key' => 'corp_name*','text' => '交易主体'),
                array('type' => 'text','key' => 'goods_name*','text' => '品名'),
                array('type' => 'text','key' => 'partner_name*','text' => '下游合作方'),
                array('type' => 'text','key' => 'store_name*','text' => '仓库'),
                array('type' => 'select', 'key' => 'type', 'map_name' => 'project_type', 'text' => '项目类型'),
            ),
            "columns"=>array(
                'out_id:text:序号:width:80px;text-align:center;',
                'project_code:text:项目编号:width:120px;text-align:left;',
                'contract_code:text:合同编号:width:120px;text-align:left;',
                'corp_name:text:交易主体:width:120px;text-align:left;',
                array(
                    'class'=>'ZEnumColumn',
                    'key'=>'project_type',
                    'name'=>'type',
                    'header'=>'项目类型',
                ),
                'goods_name:text:品名:width:120px;text-align:center;',
                'partner_name:text:下游合作方:width:120px;text-align:left;',
                'quantity:text:销售数量:width:120px;text-align:right;',
                array(
                    'class'=>'ZEnumColumn',
                    'key'=>'goods_unit_enum',
                    'name'=>'contract_unit',
                    'header'=>'销售单位',
                ),
                'price:amount:暂定价:width:120px;text-align:right;',
                'amount_cny:amount:销售金额:width:120px;text-align:right;',
                'settle_quantity:text:结算数量:width:120px;text-align:right;',
                'settle_price:amount:结算价:width:120px;text-align:right;',
                'settle_amount:amount:结算金额:width:120px;text-align:right;',
                'out_date:text:出库时间:width:120px;text-align:center;',
                'store_name:text:出库仓库:width:120px;text-align:left;',
                'quantity_out:text:出库数量:width:120px;text-align:right;',
                array(
                    'class'=>'ZEnumColumn',
                    'key'=>'goods_unit_enum',
                    'name'=>'unit',
                    'header'=>'出库单位',
                ),
                'amount_out:amount:出库金额:width:120px;text-align:right;',
                'remark:text:备注:width:120px;text-align:right;'
            ),
            'is_export'=>1,//是否导出，1为有，0为没有
            'export_fields' => 'out_id 编号, project_code 项目编号, contract_code 合同编号, corp_name 交易主体, type 项目类型, goods_name 品名, 
                                partner_name 下游合作方, quantity 销售数量, contract_unit 销售单位, price/100 暂定价, amount_cny/100 销售金额, settle_quantity 结算数量, 
                                case when settle_amount>0 then settle_amount/settle_quantity/100 else 0 end 结算价, settle_amount/100 结算金额, 
                                out_date 出库时间, store_name 出库仓库, quantity_out 出库数量, unit 出库单位, 
                                case when settle_amount>0 then round(quantity_out*settle_amount/settle_quantity)/100 else round(quantity_out*price)/100 end 出库金额, 
                                remark 备注',
            'map_keys' => array(
                'type' => array(
                    'name' => '项目类型',
                    'map_key' => 'project_type'
                ),
                'contract_unit' => array(
                    'name' => '销售单位',
                    'map_key' => 'goods_unit_enum'
                ),
                'unit' => array(
                    'name' => '出库单位',
                    'map_key' => 'goods_unit_enum'
                )
            )
        ),

        //收款流水
        '5' => array(
            'sql' => 'select rc.receive_id, co.name as corp_name, bf.bank_name, bf.receive_date, fs.name as subject_name,
                      "下游" as use_symbol, rc.amount, bf.pay_partner, p.project_code, c.contract_code
                      from t_receive_confirm rc
                      left join t_bank_flow bf on bf.flow_id = rc.flow_id
                      left join t_project p on p.project_id = rc.project_id
                      left join t_contract c on c.contract_id = rc.contract_id
                      left join t_corporation co on co.corporation_id = bf.corporation_id
                      left join t_finance_subject fs on fs.subject_id = rc.subject {where} and rc.status>=1 order by rc.receive_id desc',
            "search_items"=>array(
                array('type'=>'text','key'=>'p.project_code','text'=>'项目编号'),
                array('type'=>'text','key'=>'c.contract_code','text'=>'销售合同编号'),
                array('type'=>'text','key'=>'co.name*','text'=>'交易主体'),
                array('type'=>'text','key'=>'bf.bank_name*','text'=>'银行账户'),
                array('type'=>'text','key'=>'fs.name*','text'=>'用途'),
                array('type'=>'text','key'=>'bf.pay_partner*','text'=>'公司账户名'),
            ),
            "columns"=>array(
                'receive_id:text:收款编号:width:80px;text-align:center;',
                'corp_name:text:交易主体:width:120px;text-align:left;',
                'bank_name:text:银行账户:width:120px;text-align:left;',
                'receive_date:text:收款日期:width:120px;text-align:center;',
                'subject_name:text:用途:width:80px;text-align:center;',
                'use_symbol:text:用途符号:width:80px;text-align:center;',
                'amount:amount:贷（收入）:width:120px;text-align:right;',
                'pay_partner:text:公司账户名:width:120px;text-align:left;',
                'project_code:text:项目编号:width:120px;text-align:left;',
                'contract_code:text:销售合同编号:width:120px;text-align:left;'
            ),
            'is_export'=>1,//是否导出，1为有，0为没有
            'export_fields' => 'rc.receive_id 收款编号, co.name 交易主体, bf.bank_name 银行账户, DATE_FORMAT(bf.receive_date, "%Y/%c/%e") 收款日期, fs.name 用途, "下游" 用途符号,  
                                rc.amount/100 贷（收入）, bf.pay_partner 公司账户名, p.project_code 项目编号, c.contract_code 销售合同编号',
        ),

        //付款流水
        '6' => array(
            'sql' => 'select pm.payment_id, co.name as corp_name, pa.bank as bank_name, pm.pay_date, fs.name as subject_name,
                      "上游" as use_symbol, pm.amount, pa.payee, p.project_code, c.contract_code
                      from t_payment pm
                      left join t_pay_application pa on pa.apply_id = pm.apply_id
                      left join t_project p on p.project_id = pa.project_id
                      left join t_contract c on c.contract_id = pa.contract_id
                      left join t_corporation co on co.corporation_id = pa.corporation_id
                      left join t_finance_subject fs on fs.subject_id = pa.subject_id {where} and pm.status>=2 order by pm.payment_id desc',
            "search_items"=>array(
                array('type'=>'text','key'=>'p.project_code','text'=>'项目编号'),
                array('type'=>'text','key'=>'c.contract_code','text'=>'采购合同编号'),
                array('type'=>'text','key'=>'co.name*','text'=>'交易主体'),
                array('type'=>'text','key'=>'pa.bank*','text'=>'银行账户'),
                array('type'=>'text','key'=>'fs.name*','text'=>'用途'),
                array('type'=>'text','key'=>'pa.payee*','text'=>'公司账户名'),
            ),
            "columns"=>array(
                'payment_id:text:付款编号:width:80px;text-align:center;',
                'corp_name:text:交易主体:width:120px;text-align:left;',
                'bank_name:text:银行账户:width:120px;text-align:left;',
                'pay_date:text:付款日期:width:120px;text-align:center;',
                'subject_name:text:用途:width:80px;text-align:center;',
                'use_symbol:text:用途符号:width:80px;text-align:center;',
                'amount:amount:借（支出）:width:120px;text-align:right;',
                'payee:text:公司账户名:width:120px;text-align:left;',
                'project_code:text:项目编号:width:120px;text-align:left;',
                'contract_code:text:采购合同编号:width:120px;text-align:left;'
            ),
            'is_export'=>1,//是否导出，1为有，0为没有
            'export_fields' => 'pm.payment_id 收款编号, co.name 交易主体, pa.bank 银行账户, DATE_FORMAT(pm.pay_date, "%Y/%c/%e") 付款日期, fs.name 用途, "上游" 用途符号, 
                                pm.amount/100 借（支出）, pa.payee 公司账户名, p.project_code 项目编号, c.contract_code 采购合同编号',
        ),

        //保理对接明细
        "7"=>array(
            "sql"=>"select 
                    date(b.create_time) apply_date,c.name as corp_name,p.type as project_type,p.project_code,d.contract_code,a.contract_code as factor_code,a.contract_code_fund,
                    a.actual_pay_date,a.pay_date,a.return_date,b.amount apply_amount,a.amount,e.name up_name,a.status
                    from t_factoring a 
                    left join t_pay_application b on a.apply_id=b.apply_id
                    left join t_contract d on d.contract_id=b.contract_id
                    left join t_project p on p.project_id=b.project_id
                    left join t_corporation c on b.corporation_id=c.corporation_id
                    left join t_partner e on e.partner_id=d.partner_id
                    {where} and b.apply_id is not null 
                    order by a.factor_id desc
                   ",
            "search_items"=>array(
                array('type'=>'date','key'=>'a.create_time>','id'=>'start_date','text'=>'付款开始日期'),
                array('type'=>'date','key'=>'date(a.create_time)<','id'=>'end_date','text'=>'付款截止日期'),
                array('type'=>'text','key'=>'p.project_code*','text'=>'项目编号'),
                array('type'=>'text','key'=>'d.contract_code*','text'=>'采购合同编号'),
                array('type'=>'text','key'=>'a.contract_code*','text'=>'保理项目编号'),
                array('type'=>'text','key'=>'a.contract_code_fund*','text'=>'资金对接编号'),
                array('type' => 'select', 'key' => 'a.status', 'map_name' => 'factor_status', 'text' => '保理状态'),
                array('type'=>'text','key'=>'e.name*','text'=>'上游合作方'),

            ),
            "columns"=>array(
                'apply_date:text:日期:width:120px;text-align:center;',
                'corp_name:text:交易主体:width:200px;text-align:left;',
                'contract_code:text:采购合同编号:width:150px;text-align:left;',
                array(
                    'class'=>'ZEnumColumn',
                    'key'=>'factor_status',
                    'name'=>'status',
                    'header'=>'保理状态',
                ),
                array(
                    'class'=>'ZEnumColumn',
                    'key'=>'project_type',
                    'name'=>'project_type',
                    'header'=>'项目类型',
                ),
                'project_code:text:项目编号:width:120px;text-align:left;',
                'factor_code:text:保理项目编号:width:120px;text-align:left;',
                'contract_code_fund:text:资金对接编号:width:120px;text-align:left;',
                'apply_amount:amount:付款申请总金额:width:150px;text-align:right;',
                'amount:amount:实际对接保理金额:width:150px;text-align:right;',
                'actual_pay_date:text:实际放款时间:width:120px;text-align:center;',
                'pay_date:text:放款日期:width:120px;text-align:center;',
                'end_date:text:计划回款日期:width:120px;text-align:center;',
                'up_name:text:上游合作方:width:200px;text-align:left;',
            ),
            "is_export"=>1,//是否导出，1为有，0为没有
            "export_fields"=>"DATE_FORMAT(b.create_time, '%Y/%c/%e') 付款申请日期,c.name as 交易主体,p.type as 项目类型,
                    a.status 保理状态,
                    p.project_code 项目编号,d.contract_code 采购合同编号,a.contract_code as 保理项目编号,a.contract_code_fund 资金对接编号,
                    a.actual_pay_date 实际放款时间,a.pay_date 合同放款日期,a.return_date 合同回款日期,b.amount/100 付款申请总金额,a.amount/100 保理实际对接金额,e.name 上游合作方",

            //"tableOptions"=>array(),
            'map_keys' => array(
                'type' => array(
                    'name' => '项目类型',
                    'map_key' => 'project_type'
                ),
                'status' => array(
                    'name' => '保理状态',
                    'map_key'=>"factor_status",
                ),
            )
        ),


    );
}