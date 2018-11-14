<?php

use ddd\infrastructure\DIService;
use ddd\Split\Application\ContractSplitService;
use ddd\Split\Domain\Model\ContractSplit\IContractSplitApplyRepository;

/**
 * 合同平移审核
 */
class Check23Controller extends ApiCheckController{

    public $mainRightCode = "check23";

    public function pageInit(){
        parent::pageInit();
        $this->businessId = FlowService::BUSINESS_CONTRACT_SPLIT_CHECK;
    }

    /**
     * @api {GET} /api/split/check23/list [90020001-list] 获取合同拆分审核列表
     * @apiName list
     * @apiGroup Check23
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {string} search 查询字段，<font color=red>非必填</font>
     * @apiParam (输入字段) {int} page 页数 <font color=red>非必填</font>
     * @apiParam (输入字段) {int} pageSize 分页大小 <font color=red>非必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "page":2,
     * "pageSize":15,
     * "search"{
     *      "d.name*":'广州凯中石油化工有限公司',
     *      "f.code_out*":'YT234324JQ180614S10',
     *      "a.contract_code*":"YT234324JQ180614S10",
     *      "a.contract_id":"1132",
     *      "a.type":1,
     *      'p.project_code*'=>'ZPHP1ZJ18032101',
     *      'p.type'=>1,
     *      "co.name*":'车有邦科技服务（深圳）有限公司',
     *      "a.status":'1',
     *      "csa.apply_code":'apply-test',   //合同平移审核编号
     *      "check_status":1
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     *{
     *"state": 0,
     *"data": {
     *      "search": null,
     *      "data": {
     *          "pageCount": 124,
     *          "rows": [{
     *               "apply_code": "HJ22ND180709N02PY85",
     *               "check_id": 12,
     *               "contract_id": "1392",
     *               "contract_code": "CY666ZN180716N01",
     *               "status": "29",
     *               "type": "1",
     *               "contract_date": null,
     *               "amount": "￥10,000.00",
     *               "amount_cny": "￥10,000.00",
     *               "corporation_id": "1",
     *               "partner_id": "471",
     *               "corp_name": "车有邦科技服务（深圳）有限公司",
     *               "project_id": "20180716006",
     *               "project_code": "ZCY666ZN18071602",
     *               "project_type": "4",
     *               "name": "朱飞",
     *               "partner_name": "东营道阳石油贸易有限公司",
     *               "code_out": null,
     *               "apply_id": null,
     *               "split_status": null,
     *               "goods_list": "酒精",
     *               "is_can_check": 0,
     *               "check_status": 1,
     *               "is_can_check": true,
     *               "is_can_view": false,
     *          }],
     *          "total": "1234",
     *          "page": 1
     *          }
     *      }
     *}
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 数据信息
     * @apiParam (输出字段-data) {string} search 查询字段
     * @apiParam (输出字段-data) {array} data 列表数据
     * @apiParam (输出字段-data-data) {int} pageCount 列表总页数
     * @apiParam (输出字段-data-data) {int} total 列表总记录数
     * @apiParam (输出字段-data-data) {int} page 当前页数
     * @apiParam (输出字段-data-data) {array} rows 列表行数据信息
     * @apiParam (输出字段-data-data-rows) {int} check_id 审核id
     * @apiParam (输出字段-data-data-rows) {int} apply_code 平移申请编号
     * @apiParam (输出字段-data-data-rows) {int} is_can_check 是否可审核
     * @apiParam (输出字段-data-data-rows) {int} check_status 审核状态
     * @apiParam (输出字段-data-data-rows) {string} check_status_name 审核状态名称
     * @apiParam (输出字段-data-data-rows) {int} contract_id 合同ID
     * @apiParam (输出字段-data-data-rows) {string} contract_code 合同编号
     * @apiParam (输出字段-data-data-rows) {int} contract_status 合同状态
     * @apiParam (输出字段-data-data-rows) {string} contract_status_name 合同状态名称
     * @apiParam (输出字段-data-data-rows) {int} type 合同类型
     * @apiParam (输出字段-data-data-rows) {string} contract_date 合同签订日期
     * @apiParam (输出字段-data-data-rows) {string} amount 合同总金额
     * @apiParam (输出字段-data-data-rows) {string} amount_cny 合同人民币金额
     * @apiParam (输出字段-data-data-rows) {int} corporation_id 交易主体id
     * @apiParam (输出字段-data-data-rows) {string} corp_name 交易主体
     * @apiParam (输出字段-data-data-rows) {int} partner_id 合作方id
     * @apiParam (输出字段-data-data-rows) {string} partner_name 合作方
     * @apiParam (输出字段-data-data-rows) {int} project_id 项目id
     * @apiParam (输出字段-data-data-rows) {string} project_code 项目编号
     * @apiParam (输出字段-data-data-rows) {int} project_type 项目类型
     * @apiParam (输出字段-data-data-rows) {string} name 项目负责人
     * @apiParam (输出字段-data-data-rows) {string} code_out 外部合同编号
     * @apiParam (输出字段-data-data-rows) {string} goods_list 品名
     * @apiParam (输出字段-data-data-rows) {string} is_can_check 是否可审核
     * @apiParam (输出字段-data-data-rows) {string} is_can_view 是否可查看
     */
    public function actionList(){
        parent::actionList();
    }

    /**
     * @api {GET} /api/split/check23/detail [90020001-detail] 合同平移审核详情
     * @apiName detail
     * @apiGroup Check23
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} check_id 审核对象id <font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     *      "check_id":12,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回：
     * {
     * "state": 0,
     * "data": {
     *      "check_id":1,
     *      "check_id": "5632",
     *       "remark": "",
     *      "status": "0",
     *      "status_name": "待审核",
     *      "files": [],
     *      "logs": [
     *          {
     *          "result": "审核通过",
     *          "remark": "同意",
     *          "node_name": "商务主管审核",
     *          "checker": "李贤配",
     *          "check_time": "2017-11-17 17:04:37"
     *          }
     *      ],
     *      "detail":{
     *        "origin_contract": {
     *          "contract_id": 1,
     *          "type": 1,
     *          "contract_code": "Z998102NQ12",
     *          "partner_name": "无锡市中港石化集团有限公司",
     *          "goods_items": [{
     *              "goods_id": 1,
     *              "goods_name": "普通柴油",
     *              "quantity": 30000,
     *              "unit": "吨"
     *          }],
     *          "stock_bill_items": [{
     *              "bill_id": 2,
     *              "bill_code": "NO.009144-2-2",
     *              "is_virtual": true,
     *              "is_can_split": true,
     *              "goods_items": [{
     *                  "goods_id": 1,
     *                  "goods_name": "普通柴油",
     *                  "quantity": 15000,
     *                  "unit": "吨"
     *              }]
     *          }]
     *      },
     *      "contract_split_apply": {
     *          "apply_id": 1,
     *          "apply_code": "Z998102NQ12PY001",
     *          "remark": "备注",
     *          "status": 0,
     *          "status_name": "已保存",
     *          "is_can_edit": true,
     *              "files": [{
     *              "id": 1,
     *              "name": "附件1",
     *              "file_url": "http://www.xxx.com/static/attach_files/12312312.pdf"
     *          }],
     *          "contract_split_items": [{
     *              "split_id": 1,
     *              "new_contract": {
     *                  "contract_id": 2,
     *                  "contract_code": "Z998102NQ97"
     *              },
     *              "partner_name": "拆分中港石化集团有限公司--1",
     *              "goods_items": [{
     *                  "goods_id": 1,
     *                  "goods_name": "普通柴油",
     *                  "quantity": 30000,
     *                  "unit": "吨"
     *              }],
     *              "stock_split_details": [{
     *                  "bill_id": 2,
     *                  "bill_code": "NO.009144-2-2",
     *                  "goods_items": [{
     *                      "goods_id": 1,
     *                      "goods_name": "普通柴油",
     *                      "quantity": 15000,
     *                      "unit": "吨"
     *                  }],
     *              "new_stock_bill": {
     *                  "bill_id": 200,
     *                  "bill_code": "NO.009144-2-9"
     *              }
     *              }]
     *          }]
     *      }
     * }
     * }
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data": "错误信息"
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 数据信息
     * @apiParam (输出字段-data) {int} check_id 审核id
     * @apiParam (输出字段-data) {int} remark 审核信息
     * @apiParam (输出字段-data) {int} status 审核状态
     * @apiParam (输出字段-data) {int} status_name 审核状态名称
     * @apiParam (输出字段-data) {int} status_name 审核状态名称
     * @apiParam (输出字段-data) {array} files 审核附件
     * @apiParam (输出字段-data) {array} logs 审核记录
     * @apiParam (输出字段-data) {array} detail 审核对象信息
     * @apiParam (输出字段-data-logs) {string} result 审核结果
     * @apiParam (输出字段-data-logs) {string} remark 审核意见
     * @apiParam (输出字段-data-logs) {array} node_name 审核节点
     * @apiParam (输出字段-data-logs) {array} checker 审核人
     * @apiParam (输出字段-data-logs) {array} check_time 审核时间
     * @apiParam (输出字段-data-detail) {array} origin_contract 原始合同信息
     * @apiParam (输出字段-data-detail) {array} contract_split_apply 合同平移申请信息
     * @apiParam (输出字段-data-detail-origin_contract) {int} contract_id 原始合同id
     * @apiParam (输出字段-data-detail-origin_contract) {int} type 原始合同类型：1采 2销
     * @apiParam (输出字段-data-detail-origin_contract) {string} contract_code 原始合同编码
     * @apiParam (输出字段-data-detail-origin_contract) {string} partner_name 原始合同合作方
     * @apiParam (输出字段-data-detail-origin_contract) {array} goods_items 原始合同商品信息
     * @apiParam (输出字段-data-detail-origin_contract) {array} stock_bill_items 原始合同出入库信息
     * @apiParam (输出字段-data-detail-origin_contract-stock_bill_items) {int} bill_id 原始合同出入库id
     * @apiParam (输出字段-data-detail-origin_contract-stock_bill_items) {string} bill_code 原始合同出入库编号
     * @apiParam (输出字段-data-detail-origin_contract-stock_bill_items) {bool} is_virtual 原始合同出入库是否虚拟单
     * @apiParam (输出字段-data-detail-origin_contract-stock_bill_items) {bool} is_can_split 原始合同出入库是否可拆分
     * @apiParam (输出字段-data-detail-origin_contract-stock_bill_items) {array} goods_items 原始合同出入库商品信息
     * @apiParam (输出字段-data-detail) {array} contract_split_apply 合同拆分申请
     * @apiParam (输出字段-data-detail-contract_split_apply) {int} apply_id 合同拆分申请id
     * @apiParam (输出字段-data-detail-contract_split_apply) {string} apply_code 合同拆分申请编号
     * @apiParam (输出字段-data-detail-contract_split_apply) {string} remark 合同拆分申请备注
     * @apiParam (输出字段-data-detail-contract_split_apply) {int} status 合同拆分申请状态
     * @apiParam (输出字段-data-detail-contract_split_apply) {string} status_name 合同拆分申请状态名
     * @apiParam (输出字段-data-detail-contract_split_apply) {bool} is_can_edit 合同拆分申请是否可编辑
     * @apiParam (输出字段-data-detail-contract_split_apply) {array} files 合同拆分申请附件
     * @apiParam (输出字段-data-detail-contract_split_apply) {array} contract_split_items 合同拆分申请合同拆分项
     * @apiParam (输出字段-data-detail-contract_split_apply-contract_split_items) {int} split_id 合同拆分id
     * @apiParam (输出字段-data-detail-contract_split_apply-contract_split_items) {array} new_contract 合同拆分新合同信息
     * @apiParam (输出字段-data-detail-contract_split_apply-contract_split_items) {string} partner_name 合同拆分合作方
     * @apiParam (输出字段-data-detail-contract_split_apply-contract_split_items) {array} goods_items 合同拆分商品信息
     * @apiParam (输出字段-data-detail-contract_split_apply-contract_split_items) {array} stock_split_details 合同拆分出入库拆分信息
     * @apiParam (输出字段-data-detail-contract_split_apply-audit_log) {array} audit_log 合同拆分申请审核信息
     */
    public function actionDetail(){
        parent::actionDetail();
    }

    /**
     * @api {POST} /api/split/check23/check [90020001-check] 合同平移审核：通过/驳回
     * @apiName check
     * @apiGroup Check23
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} check_id 审核项id <font color=red>必填</font>
     * @apiParam (输入字段) {int} check_status 审核目标状态，1是审核通过，-1是审核驳回 <font color=red>必填</font>
     * @apiParam (输入字段) {string} remark 审核意见 <font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     *      "check_id":932,
     *      "check_status":1,
     *      "remark":'同意',
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     * {
     *      "code":0,
     *      "data":"审核成功!"
     * }
     * 失败返回：
     * {
     *      "code":1,
     *      "data":"失败原因"
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 消息
     */
    public function actionCheck(){
        parent::actionCheck();
    }

    public function getMainFieldsAndSql():array{
        $fields = 'cd.check_id , csa.apply_id, csa.apply_code, a.contract_id, a.contract_code, a.status as contract_status, a.type, a.contract_date, a.amount, a.amount_cny, a.corporation_id, a.partner_id, 
                   c.name as corp_name, a.currency, p.project_id, p.project_code, p.type project_type, u.name, d.name as partner_name, co.name as corp_name, f.code_out';

       $mainSql = <<<SQL
SELECT {col} 
FROM t_check_detail cd
LEFT JOIN t_contract_split_apply csa on csa.apply_id=cd.obj_id 
LEFT JOIN t_contract a on csa.contract_id=a.contract_id 
LEFT JOIN t_project p on a.project_id=p.project_id
LEFT JOIN t_corporation c on c.corporation_id=a.corporation_id
LEFT JOIN t_system_user u on p.manager_user_id=u.user_id
LEFT JOIN t_partner d on d.partner_id = a.partner_id
LEFT JOIN t_corporation co on co.corporation_id = a.corporation_id
LEFT JOIN t_contract_file f on a.contract_id = f.contract_id and f.is_main=1 and f.type=1 
SQL;

       //设置审核明细表别名
       $this->check_detail_table_alias = 'cd';
       //设置交易主体字段前缀
       $this->corporation_field_prefix = "a";

        return [$fields,$mainSql,"csa.status=".ContractSplitApply::STATUS_SUBMIT];
    }

    public function formatListData(array & $data):void{
        if(Utility::isNotEmpty($data)){
            return;
        }

        foreach($data as $row){
            $row['goods_list'] = GoodsService::getSpecialGoodsNames(ContractService::getContractAllGoodsId($row['contract_id']));
            $row['amount_cny'] = '￥'.Utility::numberFormatFen2Yuan($row['amount_cny']);
            $row['amount'] = Map::$v['currency'][$row['currency']]['ico'].Utility::numberFormatFen2Yuan($row['amount']);
            $row['contract_status_name'] = Map::getStatusName('contract_status', $row['contract_status']);
            $row['is_can_check'] = (boolean) $row['is_can_check'];
            $row['is_can_view'] = false;

            unset($row['currency']);
        }
    }

    public function getDetailData(\CheckDetail & $checkDetail):array{
        $contractSplitApplyEntity = DIService::getRepository(IContractSplitApplyRepository::class)->findByPk($checkDetail->obj_id);
        if(empty($contractSplitApplyEntity)){
            $this->returnJsonBusinessError(\ddd\infrastructure\error\BusinessError::Contract_split_Apply_Not_Exists, array('apply_id' => $checkDetail->obj_id));
        }

        try{
            $dto = ContractSplitService::service()->getContractSplitInfoDtoForViewScene($checkDetail->obj_id);
            return $dto->getAttributes();
        }catch(Exception $e){
            $this->returnJsonBusinessError(\ddd\infrastructure\error\BusinessError::Operate_Error, array('reason' => $e->getMessage()));
        }
    }
}