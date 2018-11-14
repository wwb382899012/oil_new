<?php

use ddd\infrastructure\DIService;
use ddd\infrastructure\error\BusinessError;
use ddd\Split\Application\StockSplitService;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyEnum;
use ddd\Split\Domain\Model\StockSplit\IStockSplitApplyRepository;
use ddd\Split\Domain\Model\StockSplit\StockSplitEnum;
use ddd\Split\Dto\StockSplit\StockSplitApplyDTO;
use ddd\Split\Dto\StockSplit\StockSplitDetailDTO;
use ddd\Split\Repository\Contract\BuyContractRepository;

class StockSplitController extends ApiAttachmentController{

    public function init(){
        parent::pageInit();
        $this->attachmentType = Attachment::C_STOCK_SPLIT;
        $this->filterActions = "";

        $search_data = $this->getSearch();
        $type = ($search_data['c.type'] ?? 1);
        $this->rightCode = "stockSplit_" . (int)trim($type);
    }

    /**
     * @api {POST} /api/split/StockSplit/saveFile [90020011-saveFile] 附件上传
     * @apiName saveFile
     * @apiGroup StockSplit
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} id 标志id
     * @apiParam (输入字段) {int} type 类型，1是附件
     * @apiParam (输入字段) {arr} files 文件信息
     * @apiExample {FormData} 输入示例:
     * {
     *      "id":779,
     *      "type"=>1,
     *      "files"=>[]
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "state": 0,
     * "data": {
     * "id": 1,
     * "name": "test",
     * "status": 1,
     * "file_url": "/xxx/xx/test.pdf"
     * }
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回附件id
     */
    public function actionSaveFile(){
        parent::actionSaveFile();
    }

    /**
     * @api {GET} /api/split/StockSplit/delFile [90020011-delFile] 附件删除
     * @apiName delFile
     * @apiGroup StockSplit
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} id 文件id
     * @apiExample {FormData} 输入示例:
     * {
     *      "id":779,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     *      "state":0,
     *      "data": 1
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data": ""
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回附件id
     */
    public function actionDelFile(){
        parent::actionDelFile();
    }

    /**
     * @api {GET} /api/split/StockSplit/list [90020011-list] 获取出入库拆分列表
     * @apiName list
     * @apiGroup StockSplit
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {bigint} contract_id 合同id
     * @apiParam (输入字段) {string} contract_code 合同编号
     * @apiParam (输入字段) {string} contract_out_code 外部合同编号
     * @apiParam (输入字段) {string} contract_type 合同类型
     * @apiParam (输入字段) {string} project_code 项目编码
     * @apiParam (输入字段) {string} project_type 项目类型
     * @apiParam (输入字段) {string} partner_name 合作方名称
     * @apiParam (输入字段) {string} corporation_name 交易主体名称
     * @apiParam (输入字段) {int} page 页数 <font color=red>必填</font>
     * @apiParam (输入字段) {int} pageSize 分页大小
     * @apiExample {FormData} 输入示例:
     * {
     * "page": 1,
     * "pageSize": 2,
     * "search": {
     *      "c.contract_id": "合同id",
     *      "c.contract_code": "合同编号",
     *      "cf.code_out": "外部合同编号",
     *      "c.type": "合同类型",
     *      "pc.project_code": "项目编码",
     *      "pc.type": "项目类型",
     *      "p.name*": "合作方名称",
     *      "cn.name*": "交易主体名称"
     * }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回：
     * {
     * "state": 0,
     * "data": {
     * "pageCount": 1,
     * "rows": [
     * {
     * "id": "可平移记录id",
     * "contract_id": "合同id",
     * "contract_code": "合同编号1",
     * "contract_out_code": "外部合同编号1",
     * "contract_date": "2018-04-12",
     * "corporation_id": "交易主体id",
     * "corporation_name": "交易主体名称",
     * "contract_status_name": "商务确认已保存",
     * "contract_type_name": "采购合同", //合同类型名称
     * "can_split_total": 12, //可平移数目
     * "partner_id": 12, //合作方id
     * "partner_name": "合作方名称1",
     * "project_type_name": "进口渠道", //项目类型
     * "amount": "$1,965,000.00", //合同总金额
     * "amount_cny": "$1,965,000.00", //合同人民币金额
     * "manager": "管理员", //项目负责人
     * "goods_names": "乙烯焦油" //品名
     * "is_can_split": true, //是否可平移
     * "is_can_view": true
     * },
     * {
     * "id": "可平移记录id",
     * "contract_id": "合同id",
     * "contract_code": "合同编号2",
     * "contract_out_code": "外部合同编号2",
     * "contract_date": "2018-04-12",
     * "corporation_id": "交易主体id",
     * "corporation_name": "交易主体名称",
     * "contract_status_name": "商务确认已保存",
     * "contract_type_name": "采购合同", //合同类型名称
     * "can_split_total": 12, //可平移数目
     * "partner_id": 12, //合作方id
     * "partner_name": "合作方名称2",
     * "project_code": "232323232", //项目编号
     * "project_type_name": "进口渠道", //项目类型
     * "amount": "$1,965,000.00", //合同总金额
     * "amount_cny": "$1,965,000.00", //合同人民币金额
     * "manager": "管理员", //项目负责人
     * "goods_names": "乙烯焦油" //品名
     * "is_can_split": true, //是否可平移
     * "is_can_view": true
     * }
     * ],
     * "total": 2,
     * "page": 1
     * }
     * }
     * 失败返回：
     * {
     *      "code":1,
     *      "data": "错误信息"
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回列表数据
     * @apiParam (输出字段-data-rows) {bigint} id 可平移记录id
     * @apiParam (输出字段-data-rows) {bigint} contract_id 合同id
     * @apiParam (输出字段-data-rows) {string} contract_code 合同编号
     * @apiParam (输出字段-data-rows) {string} contract_out_code 外部合同编号
     * @apiParam (输出字段-data-rows) {string} contract_date 合同签订日期
     * @apiParam (输出字段-data-rows) {string} contract_status_name 合同状态
     * @apiParam (输出字段-data-rows) {string} contract_type_name 合同类型名称
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
     * @apiParam (输出字段-data-rows) {boolean} is_can_split 是否可平移
     * @apiParam (输出字段-data-rows) {boolean} is_can_view 是否可查看
     */
    public function actionList(){
        $user = Utility::getNowUser();
        if (empty($user['corp_ids'])) {
            $this->returnJson([]);
        }

        $search_data = $this->getSearch();
        $is_stock_in = (1 == ($search_data['c.type'] ?? 1));
        $listSql = $is_stock_in ? $this->getStockInListSql($search_data) : $this->getStockOutListSql($search_data);

        $data = $this->queryTablesByPage($listSql[0], $listSql[1]);

        if(Utility::isEmpty($data['data']['rows'])){
            $this->returnJson([]);
        }

        foreach($data['data']['rows'] as & $datum){
            $datum['contract_status_name'] = Map::getStatusName('contract_status', $datum['contract_status']);
            $datum['contract_type_name'] = Map::getStatusName('contract_category', $datum['contract_type']);
            $datum['project_type_name'] = Map::getStatusName('project_type', $datum['project_type']);
            $datum['goods_names'] = GoodsService::getSpecialGoodsNames(ContractService::getContractAllGoodsId($datum['contract_id']));
            $datum['amount'] = Map::$v['currency'][$datum['currency']]['ico'].Utility::numberFormatFen2Yuan($datum['amount_cny']);
            $datum['amount_cny'] = Map::$v['currency'][$datum['currency']]['ico'].Utility::numberFormatFen2Yuan($datum['amount_cny']);
            //
            $datum['can_split_total'] = 0;
            $datum['is_can_view'] = false;
            $datum['is_can_edit'] = false;
            $datum['is_can_split'] = false;
            if(ContractSplitApplyEnum::STATUS_CAN_STOCK_SPLIT == $datum['apply_status']){
                $datum['can_split_total'] = $this->getCanSplitTotal($is_stock_in,$datum['contract_id']);
                $is_can_split = ($datum['can_split_total'] > 0);

                //没有申请数据
                if(null == $datum['split_status']){
                    $datum['is_can_view'] = false;
                    $datum['is_can_edit'] = false;
                    $datum['is_can_split'] = $is_can_split;
                }else{
                    $datum['is_can_edit'] = $datum['split_status'] < StockSplitEnum::STATUS_SUBMIT;
                    $datum['is_can_split'] = !$datum['is_can_edit'] && $is_can_split;
                    $datum['is_can_view'] = true;
                }
            }

            unset($datum['contract_type'],$datum['contract_status'],$datum['project_type'],$datum['currency']);
        }

        $this->returnJson($data);
    }

    /**
     * 获取可以平移总数
     * @param bool $isStockIn
     * @param $contractId
     * @return int
     */
    private function getCanSplitTotal(bool $isStockIn, $contractId){
        $status =  $isStockIn ? \StockIn::STATUS_PASS : \StockOutOrder::STATUS_SUBMITED;

        $stock_in_count_sql = <<<SQL
SELECT COUNT(*) AS count FROM (SELECT DISTINCT
	si.stock_in_id
FROM
	t_stock_in AS si,
	t_stock_in_detail AS sid
WHERE
	si.contract_id = sid.contract_id
AND si.contract_id = $contractId AND si.status >= $status AND sid.quantity > 0 AND si.original_id = 0 AND si.split_status = 0 ) AS tmp;
SQL;

        $stock_out_count_sql = <<<SQL
SELECT COUNT(*) AS count FROM (SELECT DISTINCT
	soo.out_order_id
FROM
	t_stock_out_order AS soo,
	t_stock_out_detail AS sod
WHERE
	soo.contract_id = sod.contract_id
AND soo.contract_id = $contractId AND soo.status >= $status AND sod.quantity > 0 AND soo.original_id = 0 AND soo.split_status = 0 ) AS tmp;
SQL;

        $sql = $isStockIn ? $stock_in_count_sql : $stock_out_count_sql;

        return \Utility::queryOneNumber($sql,'count');
    }

    private function getStockInListSql($search_data = []){
        $where_sql = $this->getWhereSql($search_data) . ' AND  ' . AuthorizeService::getUserDataConditionString('c')
            .' AND csa.`status` >= '.ContractSplitApplyEnum::STATUS_PASS;

        $fileds = <<<FIELDS
c.contract_id AS id,
c.contract_id,
c.contract_code,
c.type,
IFNULL(c.code_out,'') AS contract_out_code,
IFNULL(c.contract_date,'') AS contract_date,
c.type AS contract_type,
c.status AS contract_status,
c.corporation_id,
c.amount,
c.amount_cny,
c.currency,
pc.project_code,
pc.type AS project_type,
cn.`name` AS corporation_name,
p.partner_id,
p.`name` AS partner_name,
su.`name` AS manager,
csa.status AS apply_status,
(SELECT ssa.status FROM t_stock_split_apply AS ssa WHERE ssa.contract_id = csa.contract_id ORDER BY ssa.status ASC LIMIT 1) AS split_status
FIELDS;


        $list_sql = <<<SQL
SELECT DISTINCT
    {col}
FROM
	`t_contract_split_apply` AS csa 
LEFT JOIN t_contract AS c ON c.contract_id = csa.contract_id 
LEFT JOIN t_project AS pc ON c.project_id = pc.project_id 
LEFT JOIN t_partner AS p ON c.partner_id = p.partner_id 
LEFT JOIN t_corporation AS cn ON c.corporation_id = cn.corporation_id 
LEFT JOIN t_contract_file cf ON cf.contract_id = c.contract_id AND cf.is_main=1 AND cf.type=1 
LEFT JOIN t_system_user AS su ON pc.manager_user_id = su.user_id 
$where_sql
GROUP BY c.contract_id ORDER BY csa.update_time DESC {limit}
SQL;

        return [$list_sql,$fileds];
    }

    private function getStockOutListSql(array $search_data){
        $where_sql = $this->getWhereSql($search_data) . ' AND  ' . AuthorizeService::getUserDataConditionString('c')
            .' AND csa.`status` >= '.ContractSplitApplyEnum::STATUS_PASS;

        $fileds = <<<FIELDS
c.contract_id AS id,
c.contract_id,
c.contract_code,
c.type,
IFNULL(c.code_out,'') AS contract_out_code,
IFNULL(c.contract_date,'') AS contract_date,
c.type AS contract_type,
c.status AS contract_status,
c.corporation_id,
c.amount,
c.amount_cny,
c.currency,
pc.project_code,
pc.type AS project_type,
cn.`name` AS corporation_name,
p.partner_id,
p.`name` AS partner_name,
su.`name` AS manager,
csa.status AS apply_status,
(SELECT ssa.status FROM t_stock_split_apply AS ssa WHERE ssa.contract_id = csa.contract_id ORDER BY ssa.status ASC LIMIT 1) AS split_status
FIELDS;


        $list_sql = <<<SQL
SELECT DISTINCT
    {col}
FROM
	`t_contract_split_apply` AS csa 
LEFT JOIN t_contract AS c ON c.contract_id = csa.contract_id 
LEFT JOIN t_project AS pc ON c.project_id = pc.project_id 
LEFT JOIN t_partner AS p ON c.partner_id = p.partner_id 
LEFT JOIN t_corporation AS cn ON c.corporation_id = cn.corporation_id 
LEFT JOIN t_contract_file cf ON cf.contract_id = c.contract_id AND cf.is_main=1 AND cf.type=1 
LEFT JOIN t_system_user AS su ON pc.manager_user_id = su.user_id 
$where_sql
GROUP BY c.contract_id ORDER BY csa.update_time DESC {limit}
SQL;

        return [$list_sql,$fileds];
    }

    /**
     * @api {POST} /api/split/StockSplit/delFile [90020011-save] 保存
     * @apiName save
     * @apiParam (输入字段) {int} apply_id 出入库科拆分申请id <font color=red>必填</font>
     * @apiParam (输入字段) {boolean} is_split 是否勾选平移 <font color=red>必填</font>
     * @apiParam (输入字段) {bigint} contract_id 原合同ID <font color=red>必填</font>
     * @apiParam (输入字段) {bigint} bill_id 原出/入库ID <font color=red>必填</font>
     * @apiParam (输入字段) {int} type 出/入库类型(0:出库单，1：入库单) <font color=red>必填</font>
     * @apiParam (输入字段) {string} remark 备注
     * @apiParam (输入字段) {array} files 附件
     * @apiParam (输入字段-files) {int} id 附件id
     * @apiParam (输入字段-files) {string} name 附件名称
     * @apiParam (输入字段-files) {int} status 附件状态
     * @apiParam (输入字段-files) {string} file_url 附件id
     * @apiParam (输入字段) {array} split_items 拆分明细数组 <font color=red>必填</font>
     * @apiParam (输入字段-split_items) {bigint} contract_id 拆分合同id <font color=red>必填</font>
     * @apiParam (输入字段-split_items) {array} goods_items 拆分商品明细 <font color=red>必填</font>
     * @apiParam (输入字段-split_items-goods_items) {int} goods_id 商品ID  <font color=red>必填</font>
     * @apiParam (输入字段-split_items-goods_items) {float} quantity 商品数量 <font color=red>必填</font>
     * @apiParam (输入字段-split_items-goods_items) {int} unit 商品单位 <font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "apply_id": 1,
     * "is_split": true,
     * "contract_id": 895,
     * "bill_id": 201803070006,
     * "type": 0,
     * "remark": "备注",
     * "files": [
     * {
     * "id": 1,
     * "name": "附件1",
     * "status": 1,
     * "file_url": "/xxx/xx/test.pdf"
     * },
     * {
     * "id": 2,
     * "name": "附件2",
     * "status": 1,
     * "file_url": "/xxx/xx/test.pdf"
     * }
     * ],
     * "split_items": [
     * {
     * "contract_id": 10000,
     * "goods_items": [
     * {
     * "goods_id": "13",
     * "quantity": "50.0000",
     * "unit": "1"
     * }
     * ]
     * },
     * {
     * "contract_id": 10001,
     * "goods_items": [
     * {
     * "goods_id": "13",
     * "quantity": "50.0000",
     * "unit": "1"
     * }
     * ]
     * }
     * ]
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回：
     * {
     *      "code":0,
     *      "data": "201808020075"
     * }
     * 失败返回：
     * {
     *      "code":1,
     *      "data": "错误信息"
     * }
     * @apiParam (输出字段) {string} state 错误码
     * @apiParam (输出字段) {string} data 信息,成功返回申请id
     * @apiGroup StockSplit
     * @apiVersion 1.0.0
     */
    public function actionSave(){
        $postData = $this->getRestParams();
        if (Utility::isEmpty($postData)) {
            $this->returnJsonError(\BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        if (Utility::isEmpty($this->getRestParam('split_items'))) {
            $this->returnJsonError(\BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $check_keys = ['contract_id','bill_id'];
        foreach($check_keys as & $check_key){
            if(!Utility::checkQueryId($this->getRestParam($check_key))){
                $this->returnJsonError(\BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
            }
        }

        $dto = new StockSplitApplyDTO();
        $dto = $dto->assignDTO($postData);

        if($dto->is_split && !$dto->validate()){
            $this->returnJsonBusinessError(BusinessError::Operate_Error, ['reason' => $this->formatModelErrors($dto->getErrors())]);
        }

        try{
            $entity = $dto->toEntity();
            $res = StockSplitService::service()->save($entity);

            if (true === $res)
                $this->returnJson($entity->apply_id);
            else{
                $this->returnJsonError($res);
            }
        }catch(Exception $e){
            $this->returnJsonError($e->getMessage());
        }
    }

    /**
     * @api {POST} /api/split/StockSplit/submit [90020011-submit] 提交
     * @apiName submit
     * @apiParam (输入字段) {int} apply_id 出入库科拆分申请id <font color=red>必填</font>
     * @apiParam (输入字段) {boolean} is_split 是否勾选平移 <font color=red>必填</font>
     * @apiParam (输入字段) {bigint} contract_id 原合同ID <font color=red>必填</font>
     * @apiParam (输入字段) {bigint} bill_id 原出/入库ID <font color=red>必填</font>
     * @apiParam (输入字段) {int} type 出/入库类型(0:出库单，1：入库单) <font color=red>必填</font>
     * @apiParam (输入字段) {string} remark 备注
     * @apiParam (输入字段) {array} files 附件
     * @apiParam (输入字段-files) {int} id 附件id
     * @apiParam (输入字段-files) {string} name 附件名称
     * @apiParam (输入字段-files) {int} status 附件状态
     * @apiParam (输入字段-files) {string} file_url 附件id
     * @apiParam (输入字段) {array} split_items 拆分明细数组 <font color=red>必填</font>
     * @apiParam (输入字段-split_items) {bigint} contract_id 拆分合同id <font color=red>必填</font>
     * @apiParam (输入字段-split_items) {array} goods_items 拆分商品明细 <font color=red>必填</font>
     * @apiParam (输入字段-split_items-goods_items) {int} goods_id 商品ID  <font color=red>必填</font>
     * @apiParam (输入字段-split_items-goods_items) {float} quantity 商品数量 <font color=red>必填</font>
     * @apiParam (输入字段-split_items-goods_items) {int} unit 商品单位 <font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "apply_id": 1,
     * "is_split" :true,
     * "contract_id": 895,
     * "bill_id": 201803070006,
     * "type": 0,
     * "remark": "备注",
     * "files": [
     * {
     * "id": 1,
     * "name": "附件1",
     * "status": 1,
     * "file_url": "/xxx/xx/test.pdf"
     * },
     * {
     * "id": 2,
     * "name": "附件2",
     * "status": 1,
     * "file_url": "/xxx/xx/test.pdf"
     * }
     * ],
     * "split_items": [
     * {
     * "contract_id": 10000,
     * "goods_items": [
     * {
     * "goods_id": "13",
     * "quantity": "50.0000",
     * "unit": "1"
     * }
     * ]
     * },
     * {
     * "contract_id": 10001,
     * "goods_items": [
     * {
     * "goods_id": "13",
     * "quantity": "50.0000",
     * "unit": "1"
     * }
     * ]
     * }
     * ]
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回：
     * {
     *      "code":0,
     *      "data": "提交成功!"
     * }
     * 失败返回：
     * {
     *      "code":1,
     *      "data": "错误信息"
     * }
     * @apiParam (输出字段) {string} state 错误码
     * @apiParam (输出字段) {array} data 信息
     * @apiGroup StockSplit
     * @apiVersion 1.0.0
     */
    public function actionSubmit(){
        $postData = $this->getRestParams();
        if (Utility::isEmpty($postData)) {
            $this->returnJsonError(\BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        if (Utility::isEmpty($this->getRestParam('split_items'))) {
            $this->returnJsonError(\BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $check_keys = ['contract_id','bill_id'];
        foreach($check_keys as & $check_key){
            if(!Utility::checkQueryId($this->getRestParam($check_key))){
                $this->returnJsonError(\BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
            }
        }

        $dto = new StockSplitApplyDTO();
        $dto = $dto->assignDTO($postData);

        if($dto->is_split && !$dto->validate()){
            $this->returnJsonBusinessError(BusinessError::Operate_Error, ['reason' => $this->formatModelErrors($dto->getErrors())]);
        }

        try{
            $res = StockSplitService::service()->submit($dto->toEntity());
            if (true === $res)
                $this->returnJson('提交成功！');
            else{
                $this->returnJsonError($res);
            }
        }catch(Exception $e){
            $this->returnJsonError($e->getMessage());
        }
    }

    /**
     * @api {GET} /api/split/StockSplit/uncommittedDetail [90020011-uncommittedDetail] 详情-编辑用
     * @apiName uncommittedDetail
     * @apiParam (输入字段) {string} id 详情id <font color=red>必填</font>
     * @apiExample {FormData} 输入示例:
     * [ "id"=>779]
     * @apiSuccessExample {json} 输出示例:
     * {
     * "state": 0,
     * "data": {
     * "origin_contract": {
     * "contract_id": "895",
     * "contract_code": "WD122JQ180306D07",
     * "partner_name": "江苏卡欧化工股份有限公司",
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "乙烯焦油",
     * "quantity": "1000.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }],
     * "type": "1", //1:入库,2:出库
     * "stock_bill_items": [{
     * "apply_id": "1",  //申请id
     * "bill_id": "201803070006",
     * "bill_code": "WD122JQ180306D07-1-1",
     * "status": 7,
     * "status_name": "",
     * "is_virtual": true, //是否虚拟单
     * "is_can_submit": true, //是否可提交
     * "is_can_split": false, //是否可平移
     * "is_can_view": true, //是否可查看详情
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "150.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * },
     * {
     * "apply_id": "2",
     * "bill_id": "201803070036",
     * "bill_code": "WD122JQ180306D07-1-2",
     * "status_name": "保存",
     * "status": 5,
     * "is_virtual": true,
     * "is_can_submit": true,
     * "is_can_split": true,
     * "is_can_view": true,
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "100.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * },
     * {
     * "apply_id": 0,
     * "bill_id": "201803070040",
     * "bill_code": "WD122JQ180306D07-1-3",
     * "status": 0,
     * "status_name": "",
     * "is_virtual": true,
     * "is_can_submit": false,
     * "is_can_split": true,
     * "is_can_view": false,
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "10.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * },
     * {
     * "apply_id": 0,
     * "bill_id": "201803070047",
     * "bill_code": "WD122JQ180306D07-1-4",
     * "status": 0,
     * "status_name": "",
     * "is_virtual": true,
     * "is_can_submit": false,
     * "is_can_split": true,
     * "is_can_view": false,
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "10.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * },
     * {
     * "apply_id": 0,
     * "bill_id": "201803070048",
     * "bill_code": "WD122JQ180306D07-1-5",
     * "status": 0,
     * "status_name": "",
     * "is_virtual": true,
     * "is_can_submit": false,
     * "is_can_split": true,
     * "is_can_view": false,
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "20.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * }
     * ]
     * },
     * "contract_split_items": [{
     * "new_contract": {
     * "contract_id": 10000,
     * "contract_code": "WD122JQ180306D07_1"
     * },
     * "partner_name": "江苏卡欧化工股份有限公司",
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "500.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }],
     * "type": "1",
     * "stock_bill_items": [{
     * "apply_id": "1",
     * "bill_id": "201803070006",
     * "bill_code": "WD122JQ180306D07-1-1",
     * "new_stock_bill": {
     * "bill_id": "201803070106",
     * "bill_code": "WD122JQ180306D07-4-1"
     * },
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "50.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * },
     * {
     * "apply_id": "2",
     * "bill_id": "201803070036",
     * "bill_code": "WD122JQ180306D07-1-2",
     * "new_stock_bill": {
     * "bill_id": "",
     * "bill_code": ""
     * },
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "25.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * }
     * ]
     * },
     * {
     * "new_contract": {
     * "contract_id": 10001,
     * "contract_code": "WD122JQ180306D07_2"
     * },
     * "partner_name": "江苏卡欧化工股份有限公司",
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "500.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }],
     * "type": "1",
     * "stock_bill_items": [{
     * "apply_id": "1",
     * "bill_id": "201803070006",
     * "bill_code": "WD122JQ180306D07-1-1",
     * "new_stock_bill": {
     * "bill_id": "",
     * "bill_code": ""
     * },
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "50.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * },
     * {
     * "apply_id": "2",
     * "bill_id": "201803070036",
     * "bill_code": "WD122JQ180306D07-1-2",
     * "new_stock_bill": {
     * "bill_id": "",
     * "bill_code": ""
     * },
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "25.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * }
     * ]
     * }
     * ]
     * }
     * }
     * @apiParam (输出字段) {string} state 错误码
     * @apiParam (输出字段) {array} data 成功时返回拆分详情
     * @apiGroup StockSplit
     * @apiVersion 1.0.0
     */
    public function actionUncommittedDetail(){
        $contractId = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($contractId)) {
            $this->returnJsonError(\BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        try{
            $dto = StockSplitService::service()->getStockSplitInfoDtoForEditScene($contractId);
            $this->returnJson($dto);
        }catch(Exception $e){
            $this->returnJsonError($e->getMessage());
        }
    }

    /**
     * @api {GET} /api/split/StockSplit/applyDetail [90020011-applyDetail] 详情,提供给列表用
     * @apiName applyDetail
     * @apiGroup StockSplit
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {string} id 详情id <font color=red>必填</font>
     * @apiExample {FormData} 输入示例:
     * [ "id"=>779]
     * @apiSuccessExample {json} 输出示例:
     * {
     * "state": 0,
     * "data": {
     * "origin_contract": {
     * "contract_id": "895",
     * "contract_code": "WD122JQ180306D07",
     * "partner_name": "江苏卡欧化工股份有限公司",
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "乙烯焦油",
     * "quantity": "1000.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }],
     * "type": "1", //1:入库,2:出库
     * "stock_bill_items": [{
     * "apply_id": "1",  //申请id
     * "bill_id": "201803070006",
     * "bill_code": "WD122JQ180306D07-1-1",
     * "status": 7,
     * "status_name": "",
     * "is_virtual": true, //是否虚拟单
     * "is_can_submit": true, //是否已经保存
     * "is_can_split": false, //是否可平移
     * "is_can_view": true, //是否可查看详情
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "150.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * },
     * {
     * "apply_id": "2",
     * "bill_id": "201803070036",
     * "bill_code": "WD122JQ180306D07-1-2",
     * "status_name": "保存",
     * "status": 5,
     * "is_virtual": true,
     * "is_can_submit": true,
     * "is_can_split": true,
     * "is_can_view": true,
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "100.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * },
     * {
     * "apply_id": 0,
     * "bill_id": "201803070040",
     * "bill_code": "WD122JQ180306D07-1-3",
     * "status": 0,
     * "status_name": "",
     * "is_virtual": true,
     * "is_can_submit": false,
     * "is_can_split": true,
     * "is_can_view": false,
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "10.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * },
     * {
     * "apply_id": 0,
     * "bill_id": "201803070047",
     * "bill_code": "WD122JQ180306D07-1-4",
     * "status": 0,
     * "status_name": "",
     * "is_virtual": true,
     * "is_saved": false,
     * "is_can_split": true,
     * "is_can_view": false,
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "10.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * },
     * {
     * "apply_id": 0,
     * "bill_id": "201803070048",
     * "bill_code": "WD122JQ180306D07-1-5",
     * "status": 0,
     * "status_name": "",
     * "is_virtual": true,
     * "is_saved": false,
     * "is_can_split": true,
     * "is_can_view": false,
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "20.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * }
     * ]
     * },
     * "contract_split_items": [{
     * "new_contract": {
     * "contract_id": 10000,
     * "contract_code": "WD122JQ180306D07_1"
     * },
     * "partner_name": "江苏卡欧化工股份有限公司",
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "500.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }],
     * "type": "1",
     * "stock_bill_items": [{
     * "apply_id": "1",
     * "bill_id": "201803070006",
     * "bill_code": "WD122JQ180306D07-1-1",
     * "new_stock_bill": {
     * "bill_id": "201803070106",
     * "bill_code": "WD122JQ180306D07-4-1"
     * },
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "50.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * },
     * {
     * "apply_id": "2",
     * "bill_id": "201803070036",
     * "bill_code": "WD122JQ180306D07-1-2",
     * "new_stock_bill": {
     * "bill_id": "",
     * "bill_code": ""
     * },
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "25.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * }
     * ]
     * },
     * {
     * "new_contract": {
     * "contract_id": 10001,
     * "contract_code": "WD122JQ180306D07_2"
     * },
     * "partner_name": "江苏卡欧化工股份有限公司",
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "500.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }],
     * "type": "1",
     * "stock_bill_items": [{
     * "apply_id": "1",
     * "bill_id": "201803070006",
     * "bill_code": "WD122JQ180306D07-1-1",
     * "new_stock_bill": {
     * "bill_id": "",
     * "bill_code": ""
     * },
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "50.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * },
     * {
     * "apply_id": "2",
     * "bill_id": "201803070036",
     * "bill_code": "WD122JQ180306D07-1-2",
     * "new_stock_bill": {
     * "bill_id": "",
     * "bill_code": ""
     * },
     * "goods_items": [{
     * "goods_id": "13",
     * "goods_name": "",
     * "quantity": "25.0000",
     * "unit": "2",
     * "unit_name": "吨"
     * }]
     * }
     * ]
     * }
     * ]
     * }
     * }
     * @apiParam (输出字段) {string} state 错误码
     * @apiParam (输出字段) {array} data 成功时返回拆分详情
     */
    public function actionApplyDetail(){
        $contractId = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($contractId)) {
            $this->returnJsonError(\BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        try{
            $dto = StockSplitService::service()->getStockSplitInfoDtoForViewScene($contractId);
            $this->returnJson($dto);
        }catch(Exception $e){
            $this->returnJsonError($e->getMessage());
        }
    }

    /**
     * @api {GET} /api/split/StockSplit/detail [90020011-detail] 查看详情，给弹窗使用
     * @apiName detail
     * @apiParam (输入字段) {string} id 出入库平移申请id <font color=red>必填</font>
     * @apiExample {FormData} 输入示例:
     * ["id"=>779]
     * @apiSuccessExample {json} 输出示例:
     * {
     * "state": 0,
     * "data": {
     * "apply_id": "1",
     * "bill_id": "201803070006",
     * "bill_code": "WD122JQ180306D07-1-1",
     * "remark": "",
     * "attachments": [
     * {
     * "id": 1,
     * "name": "某某附件",
     * "file_url": "http://www.text.com/test.jpg"
     * }
     * ],
     * "logs": [
     * {
     * "result": "审核驳回",
     * "remark": "驳回",
     * "node_name": "风控审核",
     * "checker": "张星明",
     * "check_time": "2017-11-30 15:02:27"
     * },
     * {
     * "result": "审核通过",
     * "remark": "去问清楚",
     * "node_name": "风控审核",
     * "checker": "张星明",
     * "check_time": "2017-11-30 15:03:33"
     * },
     * {
     * "result": "审核通过",
     * "remark": "为全文",
     * "node_name": "风控审核",
     * "checker": "张星明",
     * "check_time": "2017-11-30 15:05:38"
     * }
     * ]
     * }
     * }
     * @apiParam (输出字段) {string} state 错误码
     * @apiParam (输出字段) {array} data 成功时返回拆分详情
     * @apiGroup StockSplit
     * @apiVersion 1.0.0
     */
    public function actionDetail(){
        $apply_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($apply_id)) {
            $this->returnJsonError(\BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $stock_split_apply = DIService::getRepository(IStockSplitApplyRepository::class)->findByApplyId($apply_id);

        if(empty($stock_split_apply)){
            //TODO: xxx
        }

        $dto = new StockSplitDetailDTO();
        $dto->fromEntity($stock_split_apply);

        $this->returnJson($dto);
    }
}