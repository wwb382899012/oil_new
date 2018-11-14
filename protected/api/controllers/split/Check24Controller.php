<?php

use ddd\infrastructure\DIService;
use ddd\Split\Application\StockSplitService;
use ddd\Split\Domain\Model\StockSplit\IStockSplitApplyRepository;
use ddd\Split\Domain\Model\StockSplit\StockSplitEnum;

/**
 * 出入库平移审核
 */
class Check24Controller extends ApiCheckController{

    public $mainRightCode = "check24_";

    public function pageInit(){
        parent::pageInit();
        $this->businessId = FlowService::BUSINESS_STOCK_SPLIT_CHECK;

        $search_data = $this->getSearch();
        $type = $search_data['c.type'] ?? 1;
        $this->rightCode = $this->mainRightCode . (int)trim($type);
    }

    /**
     * @api {GET} /api/split/check24/list [90020011-list] 获取出入库拆分审核列表
     * @apiName list
     * @apiGroup Check24
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {bigint} contract_id 合同id
     * @apiParam (输入字段) {string} contract_code 合同编号
     * @apiParam (输入字段) {string} contract_out_code 外部合同编号
     * @apiParam (输入字段) {string} contract_type 合同类型
     * @apiParam (输入字段) {string} project_code 项目编码
     * @apiParam (输入字段) {string} project_type 项目类型
     * @apiParam (输入字段) {string} partner_name 合作方名称
     * @apiParam (输入字段) {string} corporation_name 交易主体名称
     * @apiParam (输入字段) {string} bill_code 出入库单编号
     * @apiParam (输入字段) {int} status 状态
     * @apiParam (输入字段) {int} check_status 平移审核状态
     * @apiParam (输入字段) {int} page 页数 <font color=red>必填</font>
     * @apiParam (输入字段) {int} pageSize 分页大小
     * @apiExample {FromData} 输入示例:
     * {
     * "page": 1,
     * "pageSize": 2,
     * "search": {
     *      "c.contract_id": "合同id",
     *      "c.contract_code": "合同编号",
     *      "c.type": "合同类型",
     *      "c.status": "状态",
     *      "cf.code_out": "外部合同编号",
     *      "pt.project_code": "项目编码",
     *      "pt.type": "项目类型",
     *      "p.name*": "合作方名称",
     *      "cn.name*": "交易主体名称",
     *      "ssa.bill_id": "出入库单编号",
     *      "check_status": "平移审核状态"
     * }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回：
     * {
     * "state": 0,
     * "data": {
     * "state": 0,
     * "data": {
     * "data": {
     * "pageCount": 1,
     * "total": 2,
     * "page": 1,
     * "rows": [
     * {
     * "check_id": "1",
     * "bill_code": "1221212",
     * "contract_id": "1",
     * "contract_code": "11221212",
     * "contract_out_code": "11221212",
     * "contract_date": "2018-04-12",
     * "corporation_id": "11221212",
     * "corporation_name": "江苏卡欧化工股份有限公司",
     * "contract_status_name": "商务确认已保存",
     * "contract_type_name": "采购合同",
     * "partner_id": "355",
     * "partner_name": "江苏卡欧化工股份有限公司",
     * "project_code": "232323232", //项目编号
     * "project_type_name": "进口渠道", //项目类型
     * "amount": "$1,965,000.00", //合同总金额
     * "amount_cny": "$1,965,000.00", //合同人民币金额
     * "manager": "管理员", //项目负责人
     * "goods_names": "乙烯焦油" //品名
     * "check_status_name": "待审核"
     * "is_can_check": true,
     * "is_can_view": true
     * },
     * {
     * "check_id": "2",
     * "bill_code": "111112",
     * "contract_id": "1",
     * "contract_code": "232323232",
     * "contract_out_code": "fg2232d",
     * "contract_date": "2018-04-12",
     * "corporation_id": "11221212",
     * "corporation_name": "江苏卡欧化工股份有限公司",
     * "contract_status_name": "商务确认已保存",
     * "contract_type_name": "采购合同",
     * "partner_id": "355",
     * "partner_name": "江苏卡欧化工股份有限公司",
     * "project_code": "232323232", //项目编号
     * "project_type_name": "进口渠道", //项目类型
     * "amount": "$1,965,000.00", //合同总金额
     * "amount_cny": "$1,965,000.00", //合同人民币金额
     * "manager": "管理员", //项目负责人
     * "goods_names": "乙烯焦油" //品名
     * "check_status_name": "审核通过"
     * "is_can_check": false,
     * "is_can_view": true
     * }
     * ]
     * }
     * }
     * }
     * }
     * 失败返回：
     * {
     *      "code":1,
     *      "data": "错误信息"
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回列表数据
     * @apiParam (输出字段-data-rows) {int} check_id  审核id
     * @apiParam (输出字段-data-rows) {string} bill_code 出入库单编号
     * @apiParam (输出字段-data-rows) {bigint} contract_id 合同id
     * @apiParam (输出字段-data-rows) {string} contract_code 合同编号
     * @apiParam (输出字段-data-rows) {string} contract_out_code 外部合同编号
     * @apiParam (输出字段-data-rows) {string} contract_date 合同签订日期
     * @apiParam (输出字段-data-rows) {string} contract_status_name 合同状态
     * @apiParam (输出字段-data-rows) {string} contract_type_name 合同类型
     * @apiParam (输出字段-data-rows) {int} corporation_id 交易主体id
     * @apiParam (输出字段-data-rows) {string} corporation_name 交易主体名称
     * @apiParam (输出字段-data-rows) {int} can_split_total 可平移数目
     * @apiParam (输出字段-data-rows) {int} partner_id 合作方id
     * @apiParam (输出字段-data-rows) {string} partner_name 合作方名称
     * @apiParam (输出字段-data-rows) {string} project_type_name 项目类型
     * @apiParam (输出字段-data-rows) {string} project_code 项目编号
     * @apiParam (输出字段-data-rows) {float} amount 合同总金额
     * @apiParam (输出字段-data-rows) {float} amount_cny 合同人民币金额
     * @apiParam (输出字段-data-rows) {string} manager 项目负责人
     * @apiParam (输出字段-data-rows) {string} goods_names 品名
     * @apiParam (输出字段-data-rows) {string} check_status_name 平移审核状态
     * @apiParam (输出字段-data-rows) {boolean} is_can_check 是否可审核
     * @apiParam (输出字段-data-rows) {boolean} is_can_view 是否可查看
     */
    public function actionList(){
        parent::actionList();
    }

    /**
     * @api {GET} /api/split/check24/detail [90020011-detail] 详情
     * @apiName detail
     * @apiGroup Check24
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {string} check_id 审核对象id <font color=red>必填</font>
     * @apiExample {FormData} 输入示例:
     * [ "check_id"=>779]
     * @apiSuccessExample {json} 输出示例:
     * {
     * "state": 0,
     * "data": {
     * "origin_contract": {
     * "contract_id": "1282",
     * "contract_code": "KY10NQ180629N08",
     * "partner_name": "安庆市泰发能源科技有限公司",
     * "goods_items": [
     * {
     * "goods_id": "36",
     * "goods_name": "混合芳烃",
     * "quantity": "9800.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }
     * ],
     * "type": "1",
     * "stock_bill_items": [
     * {
     * "apply_id": "201808020080",
     * "bill_id": "201807020010",
     * "bill_code": "KY10NQ180629N08-2-2",
     * "status": "1",
     * "status_name": "已提交",
     * "is_virtual": false,
     * "is_saved": true,
     * "is_can_split": false,
     * "is_can_check": true,
     * "is_can_view": true,
     * "goods_items": [
     * {
     * "goods_id": "36",
     * "goods_name": "混合芳烃",
     * "quantity": "10.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }
     * ]
     * },
     * {
     * "apply_id": "1",
     * "bill_id": "201807030032",
     * "bill_code": "KY10NQ180629N08-6-1",
     * "status": "1",
     * "status_name": "已提交",
     * "is_virtual": false,
     * "is_saved": true,
     * "is_can_split": false,
     * "is_can_check": true,
     * "is_can_view": true,
     * "goods_items": [
     * {
     * "goods_id": "36",
     * "goods_name": "混合芳烃",
     * "quantity": "1000.0000",
     * "unit": "1",
     * "unit_name": "桶"
     * }
     * ]
     * },
     * {
     * "apply_id": "201808020076",
     * "bill_id": "201807020015",
     * "bill_code": "KY10NQ180629N08-4-5",
     * "status": "1",
     * "status_name": "已提交",
     * "is_virtual": false,
     * "is_saved": true,
     * "is_can_split": false,
     * "is_can_check": true,
     * "is_can_view": true,
     * "goods_items": [
     * {
     * "goods_id": "36",
     * "goods_name": "混合芳烃",
     * "quantity": "1110.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }
     * ]
     * }
     * ]
     * },
     * "contract_split_items": [
     * {
     * "new_contract": {
     * "contract_id": "1",
     * "contract_code": "KY10NQ180629N08_1"
     * },
     * "contract_id": "1282",
     * "contract_code": "KY10NQ180629N08",
     * "partner_name": "安庆市泰发能源科技有限公司",
     * "goods_items": [
     * {
     * "goods_id": "36",
     * "goods_name": "混合芳烃",
     * "quantity": "4900.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }
     * ],
     * "type": "1",
     * "stock_bill_items": [
     * {
     * "apply_id": "1",
     * "bill_id": "201807030032",
     * "bill_code": "",
     * "goods_items": [
     * {
     * "goods_id": "36",
     * "goods_name": "混合芳烃",
     * "quantity": "12.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }
     * ],
     * "new_stock_bill": {
     * "bill_id": 0,
     * "bill_code": ""
     * }
     * },
     * {
     * "apply_id": "201808020076",
     * "bill_id": "201807020015",
     * "bill_code": "",
     * "goods_items": [
     * {
     * "goods_id": "36",
     * "goods_name": "混合芳烃",
     * "quantity": "12.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }
     * ],
     * "new_stock_bill": {
     * "bill_id": 0,
     * "bill_code": ""
     * }
     * },
     * {
     * "apply_id": "201808020080",
     * "bill_id": "201807020010",
     * "bill_code": "",
     * "goods_items": [
     * {
     * "goods_id": "36",
     * "goods_name": "混合芳烃",
     * "quantity": "2.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }
     * ],
     * "new_stock_bill": {
     * "bill_id": 0,
     * "bill_code": ""
     * }
     * }
     * ]
     * },
     * {
     * "new_contract": {
     * "contract_id": "2",
     * "contract_code": "KY10NQ180629N08_2"
     * },
     * "contract_id": "1282",
     * "contract_code": "KY10NQ180629N08",
     * "partner_name": "安庆市泰发能源科技有限公司",
     * "goods_items": [
     * {
     * "goods_id": "36",
     * "goods_name": "混合芳烃",
     * "quantity": "4900.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }
     * ],
     * "type": "1",
     * "stock_bill_items": [
     * {
     * "apply_id": "1",
     * "bill_id": "201807030032",
     * "bill_code": "",
     * "goods_items": [
     * {
     * "goods_id": "36",
     * "goods_name": "混合芳烃",
     * "quantity": "123.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }
     * ],
     * "new_stock_bill": {
     * "bill_id": 0,
     * "bill_code": ""
     * }
     * },
     * {
     * "apply_id": "201808020076",
     * "bill_id": "201807020015",
     * "bill_code": "",
     * "goods_items": [
     * {
     * "goods_id": "36",
     * "goods_name": "混合芳烃",
     * "quantity": "123.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }
     * ],
     * "new_stock_bill": {
     * "bill_id": 0,
     * "bill_code": ""
     * }
     * },
     * {
     * "apply_id": "201808020080",
     * "bill_id": "201807020010",
     * "bill_code": "",
     * "goods_items": [
     * {
     * "goods_id": "36",
     * "goods_name": "混合芳烃",
     * "quantity": "8.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }
     * ],
     * "new_stock_bill": {
     * "bill_id": 0,
     * "bill_code": ""
     * }
     * }
     * ]
     * }
     * ]
     * }
     * }
     * @apiParam (输出字段) {string} state 错误码
     * @apiParam (输出字段) {array} data 成功时返回拆分详情
     */
    public function actionDetail(){
        parent::actionDetail();
    }

    /**
     * @api {POST} /api/split/check24/check [90020011-check] 出入库平移审核：通过/驳回
     * @apiName check
     * @apiGroup Check24
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
        $fields = <<<FIELDS
    cd.check_id,
    ssa.bill_id,
    ssa.type,
	c.contract_id,
	c.contract_code,
	IFNULL(c.code_out, '') AS contract_out_code,
	IFNULL(c.contract_date, '') AS contract_date,
	c.type AS contract_type,
	c.status AS contract_status,
	c.corporation_id,
	c.amount,
	c.amount_cny,
	c.currency,
	pt.project_code,
	pt.type AS project_type,
	cn.`name` AS corporation_name,
	p.partner_id,
	p.`name` AS partner_name,
	su.`name` AS manager
FIELDS;

        $mainSql = <<<SQL
SELECT 
	{col}
FROM
 t_check_detail AS cd
LEFT JOIN	`t_stock_split_apply` AS ssa ON cd.obj_id = ssa.apply_id
LEFT JOIN t_contract AS c ON c.contract_id = ssa.contract_id
LEFT JOIN t_project AS pt ON c.project_id = pt.project_id
LEFT JOIN t_partner AS p ON c.partner_id = p.partner_id
LEFT JOIN t_corporation AS cn ON c.corporation_id = cn.corporation_id
LEFT JOIN t_contract_file cf ON cf.contract_id=c.contract_id AND cf.is_main=1 AND cf.type=1
LEFT JOIN t_system_user AS su ON pt.manager_user_id = su.user_id
SQL;

        //设置审核明细表别名
        $this->check_detail_table_alias = 'cd';
        //设置交易主体字段前缀
        $this->corporation_field_prefix = "c";

        $sub_where_sql_for_check_pending = "ssa.status=" . StockSplitEnum::STATUS_SUBMIT;

        return [$fields,$mainSql,$sub_where_sql_for_check_pending];
    }

    public function formatListData(array & $data):void{
        if (Utility::isEmpty($data)) {
            return;
        }

        $bill_ids = [];
        foreach($data as & $datum){
            $bill_ids[$datum['type']][$datum['check_id']] = $datum['bill_id'];
        }

        $bill_id_codes = [];
        foreach($bill_ids as $type=> $item){
            if(StockSplitEnum::TYPE_STOCK_IN ==$type){
                $stock_bill_models = StockIn::model()->findAll('t.stock_in_id IN('.implode('',$item).')');
                foreach($stock_bill_models as $stock_bill_model){
                    $bill_id_codes[$type][(string)$stock_bill_model->stock_in_id] = $stock_bill_model->code;
                }
            }else{
                $stock_bill_models = StockOutOrder::model()->findAll('t.out_order_id IN('.implode('',$item).')');
                foreach($stock_bill_models as $stock_bill_model){
                    $bill_id_codes[$type][(string)$stock_bill_model->out_order_id] = $stock_bill_model->code;
                }
            }
        }

        //数据处理
        foreach($data as & $datum){
            $datum['bill_code'] = $bill_id_codes[$datum['type']][$datum['bill_id']];
            $datum['contract_status_name'] = Map::getStatusName('contract_status', $datum['contract_status']);
            $datum['contract_type_name'] = Map::getStatusName('contract_category', $datum['contract_type']);
            $datum['project_type_name'] = Map::getStatusName('project_type', $datum['project_type']);
            $datum['goods_names'] = GoodsService::getSpecialGoodsNames(ContractService::getContractAllGoodsId($datum['contract_id']));
            $datum['amount'] = Map::$v['currency'][$datum['currency']]['ico'].Utility::numberFormatFen2Yuan($datum['amount_cny']);
            $datum['amount_cny'] = Map::$v['currency'][$datum['currency']]['ico'].Utility::numberFormatFen2Yuan($datum['amount_cny']);
            $datum['is_can_check'] = (boolean)$datum['is_can_check'];
            $datum['is_can_view'] = true;

            unset($datum['contract_type'],$datum['project_type'],$datum['currency']);
        }
    }

    public function getDetailData(\CheckDetail & $checkDetail):array {
        //获取原始合同
        $applyEntity = DIService::getRepository(IStockSplitApplyRepository::class)->findByPk($checkDetail->obj_id);
        if(empty($applyEntity)){
            throw new \ddd\infrastructure\error\ZEntityNotExistsException($checkDetail->obj_id, StockSplitApply::class);
        }

        $dto = StockSplitService::service()->getStockSplitInfoDtoForViewScene($applyEntity->contract_id);
        $data = $dto->getAttributes();

        if(\Utility::isEmpty($data)){
            return [];
        }

        //TODO: 需要转成DTO
        foreach($data as & $contract_split_items){
            if(\Utility::isEmpty($contract_split_items['stock_bill_items'])){
                continue;
            }

            foreach($contract_split_items['stock_bill_items'] as & $stock_bill_items){
                if($checkDetail->obj_id == $stock_bill_items['apply_id']){
                    $stock_bill_items['check_id'] = $checkDetail->check_id;
                }else{
                    $stock_bill_items['check_id'] = 0;
                    $stock_bill_items['is_can_check'] = false;
                }
            }
        }

        return $data;
    }
}