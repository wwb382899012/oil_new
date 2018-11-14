<?php

use ddd\infrastructure\DIService;
use ddd\infrastructure\error\BusinessError;
use ddd\Split\Application\ContractSplitService;
use ddd\Split\Domain\Model\Contract\ContractEnum;
use ddd\Split\Domain\Model\Contract\IContractRepository;
use ddd\Split\Dto\ContractSplit\ContractSplitInfoDTO;

/**
 * Desc: 合同平移相关接口
 * User: susiehuang
 * Date: 2018/7/23 0023
 * Time: 16:49
 */
class ContractSplitController extends ApiAttachmentController{

    public function pageInit(){
        parent::pageInit();
        $this->attachmentType = Attachment::C_CONTRACT_SPLIT;
        $this->filterActions = "getPartners";
        $this->rightCode = "contractSplit";
    }

//    public function actionTest(){
////        Utility::beginTransaction();
////        $contract = Contract::model()->with('project')->findByPk(1710);
////        ddd\Split\Domain\Service\SplitService::service()->generateStockBills($contract);
////        exit;
//
//        $contractEntity = DIService::getRepository(IContractRepository::class)->findByPk(1272);
//        $contractDto = new ContractDTO();
//
//        $contractDto->fromEntityForViewScene($contractEntity);
//        $this->returnJson($contractDto);
//    }

    /**
     * @api {GET} /api/split/ContractSplit/saveFile [90020001-saveFile] 附件上传
     * @apiName saveFile
     * @apiGroup ContractSplit
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} id 标志id
     * @apiParam (输入字段) {int} type 类型，1是附件
     * @apiParam (输入字段) {arr} files 文件信息
     * @apiExample {json} 输入示例:
     * {
     *      "id":779,
     *      "type"=>1,
     *      "files"=>[]
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     *      "state":0,
     *      "data":1
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
     * @api {GET} /api/split/ContractSplit/delFile [90020001-delFile] 附件删除
     * @apiName delFile
     * @apiGroup ContractSplit
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
     *      "data":1
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data":""
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回附件id
     */
    public function actionDelFile(){
        parent::actionDelFile();
    }

    /**
     * @api {GET} /api/split/ContractSplit/list [90020001-list]合同拆分列表
     * @apiName list
     * @apiGroup ContractSplit
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
     *               "is_can_edit": false,
     *               "is_can_view": false,
     *               "is_can_split": true,
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
     * @apiParam (输出字段-data-data-rows) {int} contract_id 合同ID
     * @apiParam (输出字段-data-data-rows) {string} contract_code 合同编号
     * @apiParam (输出字段-data-data-rows) {int} status 合同状态
     * @apiParam (输出字段-data-data-rows) {int} type 合同类型
     * @apiParam (输出字段-data-data-rows) {string} contract_date 合同签订日期
     * @apiParam (输出字段-data-data-rows) {string} amount 合同总金额
     * @apiParam (输出字段-data-data-rows) {string} amount_cny 合同人民币金额
     * @apiParam (输出字段-data-data-rows) {int} corporation_id 交易主体id
     * @apiParam (输出字段-data-data-rows) {string} corp_name 交易主体
     * @apiParam (输出字段-data-data-rows) {int} partner_id 合作方id
     * @apiParam (输出字段-data-data-rows) {string} partner_name 合作方
     * @apiParam (输出字段-data-data-rows) {int} apply_id 拆分申请id
     * @apiParam (输出字段-data-data-rows) {int} project_id 项目id
     * @apiParam (输出字段-data-data-rows) {string} project_code 项目编号
     * @apiParam (输出字段-data-data-rows) {int} project_type 项目类型
     * @apiParam (输出字段-data-data-rows) {string} name 项目负责人
     * @apiParam (输出字段-data-data-rows) {string} code_out 外部合同编号
     * @apiParam (输出字段-data-data-rows) {string} goods_list 品名
     * @apiParam (输出字段-data-data-rows) {string} is_can_edit 是否可修改
     * @apiParam (输出字段-data-data-rows) {string} is_can_view 是否可查看
     * @apiParam (输出字段-data-data-rows) {string} is_can_split 是否可拆分
     */
    public function actionList(){
        $user = Utility::getNowUser();
        if (empty($user['corp_ids'])) {
            $this->returnJson([]);
        }

        $attr = $this->getSearch();
        $where [] = " a.status >= ".Contract::STATUS_BUSINESS_CHECKED;
        $where [] = " a.original_id = 0";
        $where [] = AuthorizeService::getUserDataConditionString("a");
        $where = $this->getWhereSql($attr).' AND '.implode(' AND ', $where);

        $sql = "SELECT DISTINCT {col}
                FROM t_contract a
                LEFT JOIN t_project p ON a.project_id=p.project_id
                LEFT JOIN t_corporation c ON c.corporation_id=a.corporation_id
                LEFT JOIN t_system_user u ON p.manager_user_id=u.user_id
                LEFT JOIN t_partner d ON d.partner_id = a.partner_id
                LEFT JOIN t_corporation co ON co.corporation_id = a.corporation_id
                LEFT JOIN t_contract_file f ON a.contract_id = f.contract_id and f.is_main=1 and f.type=1 
                LEFT JOIN t_contract_split_apply cs ON cs.contract_id=a.contract_id 
                ".$where." ORDER BY cs.status_time DESC,a.status_time DESC {limit}";

        $fields = 'a.contract_id, a.contract_code, a.status AS contract_status, a.type, a.contract_date, a.currency,
                    a.amount, a.amount_cny, a.corporation_id, a.partner_id, c.name as corp_name,
                   p.project_id, p.project_code, p.type project_type, u.name, d.name as partner_name, co.name as corp_name, f.code_out,';
        $fields .= '(SELECT cg.goods_id FROM t_contract_goods as cg WHERE  cg.contract_id = a.contract_id AND cg.quantity > 0 LIMIT 1) AS goods_id,';
        $fields .= '(SELECT csa.status FROM t_contract_split_apply as csa WHERE  csa.contract_id = a.contract_id ORDER BY csa.status ASC LIMIT 1) AS split_status,';
        $fields .= '(SELECT csa.apply_id FROM t_contract_split_apply as csa WHERE  csa.contract_id = a.contract_id ORDER BY csa.status ASC LIMIT 1) AS apply_id';

        $data = $this->queryTablesByPage($sql, $fields);

        if(Utility::isEmpty($data['data']['rows'])){
            $this->returnJson([]);
        }


        foreach($data['data']['rows'] as & $datum){
            $datum['contract_status_name'] = Map::getStatusName('contract_status', $datum['contract_status']);
            $datum['goods_list'] = GoodsService::getSpecialGoodsNames(ContractService::getContractAllGoodsId($datum['contract_id']));
            $datum['amount'] = Map::$v['currency'][$datum['currency']]['ico'].Utility::numberFormatFen2Yuan($datum['amount_cny']);
            $datum['amount_cny'] = Map::$v['currency'][$datum['currency']]['ico'].Utility::numberFormatFen2Yuan($datum['amount_cny']);
            //
            $datum['apply_id'] = empty($datum['apply_id']) ? 0 : $datum['apply_id'];


            if(empty($datum['goods_id']) || 0 >= $datum['goods_id']){
                $datum['is_can_view'] = (null == $datum['apply_id']) ? false : true;
                $datum['is_can_edit'] = false;
                $datum['is_can_split'] = false;
            }else{
                //没有申请数据
                if(null == $datum['apply_id']){
                    $datum['is_can_view'] = false;
                    $datum['is_can_edit'] = false;
                    $datum['is_can_split'] = true;

                    $datum['apply_id'] = 0;
                }else{
                    $datum['is_can_view'] = true;
                    $datum['is_can_edit'] = $datum['split_status'] < ContractSplitApply::STATUS_SUBMIT;
                    //又可以平移商品，并且当前最新的状态大于已提交
                    $datum['is_can_split'] = !$datum['is_can_edit'] && ($datum['goods_id'] > 0) && ($datum['split_status'] > ContractSplitApply::STATUS_SUBMIT);
                    if($datum['is_can_split']){
                        $datum['apply_id'] = 0;
                    }
                }
            }

            unset($datum['goods_id'],$datum['currency']);
        }

        $this->returnJson($data);
    }

    private function actionListX(){
        $attr = $this->getSearch();
        $where [] = " a.status >= ".Contract::STATUS_BUSINESS_CHECKED;
        $where [] = " a.original_id = 0";
        $where [] = AuthorizeService::getUserDataConditionString("a");
        $where = $this->getWhereSql($attr).' AND '.implode(' AND ', $where);

        $sql = "SELECT DISTINCT {col}
                FROM t_contract a
                LEFT JOIN t_project p ON a.project_id=p.project_id
                LEFT JOIN t_corporation c ON c.corporation_id=a.corporation_id
                LEFT JOIN t_system_user u ON p.manager_user_id=u.user_id
                LEFT JOIN t_partner d ON d.partner_id = a.partner_id
                LEFT JOIN t_corporation co ON co.corporation_id = a.corporation_id
                LEFT JOIN t_contract_file f ON a.contract_id = f.contract_id and f.is_main=1 and f.type=1 
                LEFT JOIN t_contract_split_apply cs ON cs.contract_id=a.contract_id 
                ".$where." ORDER BY cs.status_time DESC,a.update_time DESC {limit}";

        $fields = 'a.contract_id, a.contract_code, a.status AS contract_status, a.type, a.contract_date, a.currency,
                    a.amount, a.amount_cny, a.corporation_id, a.partner_id, c.name as corp_name,
                   p.project_id, p.project_code, p.type project_type, u.name, d.name as partner_name, co.name as corp_name, f.code_out';
        $data = $this->queryTablesByPage($sql, $fields);

        if(Utility::isEmpty($data['data']['rows'])){
            $this->returnJson([]);
        }


        $contract_ids = [];
        $apply_id_status_arr = [];
        foreach($data['data']['rows'] as $datum){
            $contract_ids[$datum['contract_id']] = $datum['contract_id'];
        }

        $contract_apply_ids = [];
        if(Utility::isNotEmpty($contract_ids)){
            $sql = 'SELECT contract_id,apply_id,status FROM t_contract_split_apply WHERE contract_id IN ('.implode(',', $contract_ids).') ORDER BY status ASC;';
            $apply_data = Utility::query($sql);
            foreach($apply_data as & $row){
                $apply_id_status_arr[$row['contract_id']][$row['apply_id']] = $row['status'];
            }

            foreach($apply_id_status_arr as $contract_id => $item){
                asort($item, SORT_NUMERIC);
                $apply_id = key($item);
                $status = current($item);

                $contract_apply_ids[$contract_id] = ['apply_id' => $apply_id, 'status' => $status,];
            }
        }

        foreach($data['data']['rows'] as & $datum){
            $datum['contract_status_name'] = Map::getStatusName('contract_status', $datum['contract_status']);
            $datum['goods_list'] = GoodsService::getSpecialGoodsNames(ContractService::getContractAllGoodsId($datum['contract_id']));
            $datum['amount'] = Map::$v['currency'][$datum['currency']]['ico'].Utility::numberFormatFen2Yuan($datum['amount_cny']);
            $datum['amount_cny'] = Map::$v['currency'][$datum['currency']]['ico'].Utility::numberFormatFen2Yuan($datum['amount_cny']);

            $datum['is_can_edit'] = false;
            $datum['is_can_view'] = false;
            $datum['is_can_split'] = true;
            if(isset($contract_apply_ids[$datum['contract_id']])){
                $datum['apply_id'] = $contract_apply_ids[$datum['contract_id']]['apply_id'];
                $datum['is_can_edit'] = $contract_apply_ids[$datum['contract_id']]['status'] < ContractSplitApply::STATUS_SUBMIT;
                $datum['is_can_split'] = !$datum['is_can_edit'] && $contract_apply_ids[$datum['contract_id']]['status'] < ContractSplitApply::STATUS_SUBMIT;
                if($datum['is_can_split'] && !$datum['is_can_edit']){
                    $datum['apply_id'] = 0;
                }
                $datum['is_can_view'] = true;
            }else{
                $datum['apply_id'] = 0;
            }
            unset($datum['currency']);
        }

        $this->returnJson($data);
    }

    /**
     * @api {GET} /api/split/ContractSplit/uncommittedDetail [90020001-uncommittedDetail] 详情，编辑
     * @apiName uncommittedDetail
     * @apiGroup ContractSplit
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} contract_id 合同id <font color=red>必填</font>
     * @apiParam (输入字段) {int} apply_id 合同拆分申请id
     * @apiExample {FormData} 输入示例:
     * {
     *      "contract_id":1,
     *      "apply_id":1,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回：
     * {
     * "state": 0,
     * "data": {
     *      "origin_contract": {
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
     *          }],
     *          "audit_log": [{
     *              "result": "审核通过",
     *              "remark": "同意",
     *              "node_name": "商务主管审核",
     *              "checker": "李贤配",
     *              "check_time": "2017-11-17 17:04:37"
     *          }]
     *      }
     *   }
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data": "错误信息"
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 数据信息
     * @apiParam (输出字段-data) {array} origin_contract 原始合同信息
     * @apiParam (输出字段-data-origin_contract) {int} contract_id 原始合同id
     * @apiParam (输出字段-data-origin_contract) {int} contract_id 原始合同类型：1采 2销
     * @apiParam (输出字段-data-origin_contract) {string} contract_code 原始合同编码
     * @apiParam (输出字段-data-origin_contract) {string} partner_name 原始合同合作方
     * @apiParam (输出字段-data-origin_contract) {array} goods_items 原始合同商品信息
     * @apiParam (输出字段-data-origin_contract) {array} stock_bill_items 原始合同出入库信息
     * @apiParam (输出字段-data-origin_contract-stock_bill_items) {int} bill_id 原始合同出入库id
     * @apiParam (输出字段-data-origin_contract-stock_bill_items) {string} bill_code 原始合同出入库编号
     * @apiParam (输出字段-data-origin_contract-stock_bill_items) {bool} is_virtual 原始合同出入库是否虚拟单
     * @apiParam (输出字段-data-origin_contract-stock_bill_items) {bool} is_can_split 原始合同出入库是否可拆分
     * @apiParam (输出字段-data-origin_contract-stock_bill_items) {array} goods_items 原始合同出入库商品信息
     * @apiParam (输出字段-data) {array} contract_split_apply 合同拆分申请
     * @apiParam (输出字段-data-contract_split_apply) {int} apply_id 合同拆分申请id
     * @apiParam (输出字段-data-contract_split_apply) {string} apply_code 合同拆分申请编号
     * @apiParam (输出字段-data-contract_split_apply) {string} remark 合同拆分申请备注
     * @apiParam (输出字段-data-contract_split_apply) {int} status 合同拆分申请状态
     * @apiParam (输出字段-data-contract_split_apply) {string} status_name 合同拆分申请状态名
     * @apiParam (输出字段-data-contract_split_apply) {bool} is_can_edit 合同拆分申请是否可编辑
     * @apiParam (输出字段-data-contract_split_apply) {array} files 合同拆分申请附件
     * @apiParam (输出字段-data-contract_split_apply) {array} contract_split_items 合同拆分申请合同拆分项
     * @apiParam (输出字段-data-contract_split_apply-contract_split_items) {int} split_id 合同拆分id
     * @apiParam (输出字段-data-contract_split_apply-contract_split_items) {array} new_contract 合同拆分新合同信息
     * @apiParam (输出字段-data-contract_split_apply-contract_split_items) {string} partner_name 合同拆分合作方
     * @apiParam (输出字段-data-contract_split_apply-contract_split_items) {array} goods_items 合同拆分商品信息
     * @apiParam (输出字段-data-contract_split_apply-contract_split_items) {array} stock_split_details 合同拆分出入库拆分信息
     * @apiParam (输出字段-data-contract_split_apply-audit_log) {array} audit_log 合同拆分申请审核信息
     */
    public function actionUncommittedDetail(){
        $contractId = Mod::app()->request->getParam('contract_id');
        $applyId = Mod::app()->request->getParam('apply_id');


        if(!Utility::checkQueryId($contractId) || empty($contractId)){
            $this->returnJsonBusinessError(BusinessError::Argument_Required, array('name' => 'contract_id'));
        }
        if(!empty($applyId) && !Utility::checkQueryId($applyId)){
            $this->returnJsonBusinessError(BusinessError::Argument_Required, array('name' => 'apply_id'));
        }

        try{
            $dto = ContractSplitService::service()->getContractSplitInfoDtoForEditScene($contractId,$applyId);
            $this->returnJson($dto);
        }catch(Exception $e){
            $this->returnJsonBusinessError(BusinessError::Operate_Error, array('reason' => $e->getMessage()));
        }
    }

    /**
     * @api {GET} /api/split/ContractSplit/getPartners [90020001-getPartners] 获取合作方列表
     * @apiName getPartners
     * @apiGroup ContractSplit
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {string} contract_id 合同id <font color=red>必填</font>
     * @apiExample {FormData} 输入示例:
     * [ "contract_id"=>779]
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回：
     * {
     * "state": 0,
     * "data": [
     * {
     * "partner_id": "548",
     * "name": "加油宝金融科技服务（深圳）有限公司",
     * "code": null
     * },
     * {
     * "partner_id": "520",
     * "name": "广西丰硕石油有限公司",
     * "code": null
     * },
     * {
     * "partner_id": "513",
     * "name": "福建中源新能源股份有限公司",
     * "code": null
     * },
     * {
     * "partner_id": "494",
     * "name": "中山市丰硕石油化工有限公司",
     * "code": null
     * },
     * {
     * "partner_id": "449",
     * "name": "深圳前海泰丰能源有限公司",
     * "code": null
     * },
     * {
     * "partner_id": "225",
     * "name": "亚太能源（深圳）有限公司",
     * "code": null
     * },
     * {
     * "partner_id": "214",
     * "name": "青岛中联油国际贸易有限公司",
     * "code": null
     * },
     * {
     * "partner_id": "87",
     * "name": "湖南新华联国际石油贸易有限公司",
     * "code": null
     * }
     * ]
     * }
     * 失败返回：
     * {
     *      "code":1,
     *      "data": "错误信息"
     * }
     * @apiParam (输出字段) {string} state 错误码
     * @apiParam (输出字段) {array} data 信息
     */
    public function actionGetPartners(){
        $contractId = Mod::app()->request->getParam('contract_id');
        if(!Utility::checkQueryId($contractId)){
            $this->returnJsonBusinessError(BusinessError::Argument_Required, array('name' => 'contract_id'));
        }

        //获取原始合同
        $contract_repository = DIService::getRepository(IContractRepository::class);
        $contract_entity = $contract_repository->findByPk($contractId);
        if(empty($contract_entity)){
            throw new \ddd\infrastructure\error\ZEntityNotExistsException($contractId, Contract::class);
        }

        if(ContractEnum::BUY_CONTRACT == $contract_entity->type){
            $partners = PartnerService::getUpPartners();
        }else{
            $partners = PartnerService::getDownPartners();
        }

        $this->returnJson($partners);
    }

    /**
     * @api {POST} /api/split/ContractSplit/save [90020001-save] 合同拆分暂存、保存
     * @apiName save
     * @apiGroup ContractSplit
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} apply_id 合同拆分申请id <font color=red>非必填</font>
     * @apiParam (输入字段) {int} contract_id 合同id <font color=red>必填</font>
     * @apiParam (输入字段) {string} remark 备注 <font color=red>非必填</font>
     * @apiParam (输入字段) {string} is_submit 是否提交，不传或为0：保存  1：提交 <font color=red>非必填</font>
     * @apiParam (输入字段) {array} contract_split_items 合同拆分明细 <font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     *      "apply_id": 1,
     *      "contract_id": 1,
     *      "remark": "备注",
     *      "is_submit": 1,
     *      "contract_split_items": [{
     *          "partner_id": 1,
     *          "goods_items": [{
     *              "goods_id": 1,
     *              "quantity": 50,
     *              "unit": 1
     *          }],
     *          "stock_split_details": [{
     *              "bill_id": "201806150001",
     *              "goods_items": [{
     *                  "goods_id": 1,
     *                  "quantity": 25,
     *                  "unit": 1
     *              }],
     *          }]
     *      }]
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回：
     * {
     *      "state":0,
     *      "data":"成功信息"
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data": "错误信息"
     * }
     * @apiParam (输出字段) {string} state 错误码
     * @apiParam (输出字段) {array} data 操作信息
     */
    public function actionSave(){
        $params = $this->getRestParams();
        $dto = new ContractSplitInfoDTO();
        $dto->assignDTO($params);

        if(!$dto->validate()){
            $this->returnJsonBusinessError(BusinessError::Operate_Error, ['reason' => $this->formatModelErrors($dto->getErrors())]);
        }

        try{
            $applyDto = $dto->contract_split_apply;
            $entity = $applyDto->toEntity();
            $res = ContractSplitService::service()->save($entity);

            if(is_array($res)){
                $this->returnValidateError($res[0]);
            }
            if(is_string($res)){
                $this->returnJsonBusinessError(\ddd\infrastructure\error\BusinessError::Operate_Error, array('reason' => $res));
            }

            if($this->getRestParam('is_submit')){
                $submit_res = ContractSplitService::service()->submit($entity);
                if(true !== $submit_res){
                    $this->returnJsonError($submit_res);
                }
            }

            $this->returnJson($res);
        }catch(Exception $e){
            $this->returnJsonBusinessError(\ddd\infrastructure\error\BusinessError::Operate_Error, array('reason' => $e->getMessage()));
        }
    }

    /**
     * @api {POST} /api/split/ContractSplit/submit [90020001-submit] 提交
     * @apiName submit
     * @apiGroup ContractSplit
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} apply_id 合同拆分申请id
     * @apiExample {json} 输入示例:
     * {
     *      "apply_id":1,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回：
     * {
     *      "state":0,
     *      "data":"成功信息"
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data": "错误信息"
     * }
     * @apiParam (输出字段) {string} state 错误码
     * @apiParam (输出字段) {array} data 操作信息
     */
    public function actionSubmit(){
        $applyId = $this->getRestParam('apply_id');
        if(Utility::checkQueryId($applyId) && $applyId > 0){
            $contractSplitApplyEntity = \ddd\infrastructure\DIService::getRepository(\ddd\Split\Domain\Model\ContractSplit\IContractSplitApplyRepository::class)->findByPk($applyId);
            if(empty($contractSplitApplyEntity)){
                $this->returnJsonBusinessError(\ddd\infrastructure\error\BusinessError::Contract_split_Apply_Not_Exists, array('apply_id' => $applyId));
            }

            if(!$contractSplitApplyEntity->isCanSubmit()){
                $this->returnJsonBusinessError(\ddd\infrastructure\error\BusinessError::Contract_split_Apply_Not_Allow_Submit);
            }

            try{
                $res = ContractSplitService::service()->submit($contractSplitApplyEntity);
                if($res !== true){
                    if(is_array($res)){
                        $this->returnValidateError($res);
                    }
                    if(is_string($res)){
                        $this->returnJsonBusinessError(\ddd\infrastructure\error\BusinessError::Operate_Error, array('reason' => $res));
                    }
                }

                $this->returnJson();
            }catch(Exception $e){
                $this->returnJsonBusinessError(\ddd\infrastructure\error\BusinessError::Operate_Error, array('reason' => $e->getMessage()));
            }
        }else{
            $this->returnJsonBusinessError(\ddd\infrastructure\error\BusinessError::Argument_Required, array('name' => 'apply_id'));
        }
    }

    /**
     * @api {GET} /api/split/ContractSplit/getApplyList [90020001-getApplyList] 获取合同平移申请列表
     * @apiName getApplyList
     * @apiGroup ContractSplit
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} contract_id 合同id
     * @apiExample {json} 输入示例:
     * {
     *      "contract_id":1,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回：
     * {
     * "state": 0,
     * "data": {
     *     "origin_contract": {
     *         "contract_id": "1354",
     *         "type": "1",
     *         "contract_code": "HJ22ND180709N02",
     *         "project_code": "ZHJ22ND18070902",
     *         "partner_name": "东营市福润达商贸有限责任公司",
     *         "corp_name": "浙江自贸区中优海聚能源有限公司"
     *     },
     *     "apply_list": [
     *         {
     *             "apply_id": "12",
     *             "contract_id": "1354",
     *             "apply_code": "HJ22ND180709N02PY85",
     *             "remark": "合同拆分测试",
     *             "status": "0",
     *             "status_time": null,
     *             "effect_time": null,
     *             "status_name": "未提交",
     *             "new_contract": [{
     *                 "contract_id": "0",
     *                 "partner_id": "478",
     *                 "partner_name": "上海天源石油化工有限公司",
     *                 "contract_code": null
     *             }]
     *         },
     *         {
     *             "apply_id": "13",
     *             "contract_id": "1354",
     *             "apply_code": "HJ22ND180709N02PY86",
     *             "remark": "合同拆分测试",
     *             "status": "0",
     *             "status_time": null,
     *             "effect_time": null,
     *             "status_name": "未提交",
     *             "new_contract": [{
     *                 "contract_id": "0",
     *                 "partner_id": "478",
     *                 "partner_name": "上海天源石油化工有限公司",
     *                 "contract_code": null
     *             }]
     *         }
     *     ]
     * }
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data": "错误信息"
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 数据信息
     * @apiParam (输出字段-data) {int} origin_contract 原始合同信息
     * @apiParam (输出字段-data) {int} apply_list 平移申请数组
     * @apiParam (输出字段-data-origin_contract) {int} contract_id 原始合同id
     * @apiParam (输出字段-data-origin_contract) {string} contract_code 原始合同编码
     * @apiParam (输出字段-data-origin_contract) {string} partner_name 原始合同合作方
     * @apiParam (输出字段-data-origin_contract) {string} project_code 原始合同项目编码
     * @apiParam (输出字段-data-origin_contract) {string} corp_name 原始合同主体
     * @apiParam (输出字段-data-origin_contract) {int} type 原始合同类型
     * @apiParam (输出字段-data-apply_list) {string} effect_time 生效时间，即提交时间
     */
    public function actionGetApplyList(){
        $contractId = Mod::app()->request->getParam('contract_id');
        if(!Utility::checkQueryId($contractId) || empty($contractId)){
            $this->returnJsonBusinessError(\ddd\infrastructure\error\BusinessError::Argument_Required, array('name' => 'contract_id'));
        }

        try{
            $dto = ContractSplitService::service()->getApplyList($contractId);
            $this->returnJson($dto);
        }catch(Exception $e){
            $this->returnJsonBusinessError(BusinessError::Operate_Error, array('reason' => $e->getMessage()));
        }
    }

    /**
     * @api {GET} /api/split/ContractSplit/detail [90020001-detail] 详情，查看用
     * @apiName detail
     * @apiGroup ContractSplit
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} apply_id 合同拆分申请id
     * @apiExample {FormData} 输入示例:
     * {
     *      "apply_id":1,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回：
     * {
     * "state": 0,
     * "data": {
     *      "origin_contract": {
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
     *          }],
     *          "audit_log": [{
     *              "result": "审核通过",
     *              "remark": "同意",
     *              "node_name": "商务主管审核",
     *              "checker": "李贤配",
     *              "check_time": "2017-11-17 17:04:37"
     *          }]
     *      }
     *   }
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data": "错误信息"
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 数据信息
     * @apiParam (输出字段-data) {array} origin_contract 原始合同信息
     * @apiParam (输出字段-data-origin_contract) {int} contract_id 原始合同id
     * @apiParam (输出字段-data-origin_contract) {int} contract_id 原始合同类型：1采 2销
     * @apiParam (输出字段-data-origin_contract) {string} contract_code 原始合同编码
     * @apiParam (输出字段-data-origin_contract) {string} partner_name 原始合同合作方
     * @apiParam (输出字段-data-origin_contract) {array} goods_items 原始合同商品信息
     * @apiParam (输出字段-data-origin_contract) {array} stock_bill_items 原始合同出入库信息
     * @apiParam (输出字段-data-origin_contract-stock_bill_items) {int} bill_id 原始合同出入库id
     * @apiParam (输出字段-data-origin_contract-stock_bill_items) {string} bill_code 原始合同出入库编号
     * @apiParam (输出字段-data-origin_contract-stock_bill_items) {bool} is_virtual 原始合同出入库是否虚拟单
     * @apiParam (输出字段-data-origin_contract-stock_bill_items) {bool} is_can_split 原始合同出入库是否可拆分
     * @apiParam (输出字段-data-origin_contract-stock_bill_items) {array} goods_items 原始合同出入库商品信息
     * @apiParam (输出字段-data) {array} contract_split_apply 合同拆分申请
     * @apiParam (输出字段-data-contract_split_apply) {int} apply_id 合同拆分申请id
     * @apiParam (输出字段-data-contract_split_apply) {string} apply_code 合同拆分申请编号
     * @apiParam (输出字段-data-contract_split_apply) {string} remark 合同拆分申请备注
     * @apiParam (输出字段-data-contract_split_apply) {int} status 合同拆分申请状态
     * @apiParam (输出字段-data-contract_split_apply) {string} status_name 合同拆分申请状态名
     * @apiParam (输出字段-data-contract_split_apply) {bool} is_can_edit 合同拆分申请是否可编辑
     * @apiParam (输出字段-data-contract_split_apply) {array} files 合同拆分申请附件
     * @apiParam (输出字段-data-contract_split_apply) {array} contract_split_items 合同拆分申请合同拆分项
     * @apiParam (输出字段-data-contract_split_apply-contract_split_items) {int} split_id 合同拆分id
     * @apiParam (输出字段-data-contract_split_apply-contract_split_items) {array} new_contract 合同拆分新合同信息
     * @apiParam (输出字段-data-contract_split_apply-contract_split_items) {string} partner_name 合同拆分合作方
     * @apiParam (输出字段-data-contract_split_apply-contract_split_items) {array} goods_items 合同拆分商品信息
     * @apiParam (输出字段-data-contract_split_apply-contract_split_items) {array} stock_split_details 合同拆分出入库拆分信息
     * @apiParam (输出字段-data-contract_split_apply-audit_log) {array} audit_log 合同拆分申请审核信息
     */
    public function actionDetail(){
        $applyId = Mod::app()->request->getParam('apply_id');

        if(!Utility::checkQueryId($applyId) || empty($applyId)){
            $this->returnJsonBusinessError(BusinessError::Argument_Required, array('name' => 'apply_id'));
        }

        try{
            $dto = ContractSplitService::service()->getContractSplitInfoDtoForViewScene($applyId);
            $this->returnJson($dto);
        }catch(Exception $e){
            $this->returnJsonBusinessError(BusinessError::Operate_Error, array('reason' => $e->getMessage()));
        }
    }

}