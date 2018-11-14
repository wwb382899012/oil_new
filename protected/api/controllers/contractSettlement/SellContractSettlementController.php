<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/19 14:37
 * Describe：销售合同结算
 */

class SellContractSettlementController extends AttachmentController
{
    public function pageInit() {
        $this->attachmentType = Attachment::C_CONTRACT_SETTLEMENT;
    }
    /**
     * @api {GET} / [90020001-List] 列表
     * @apiName List
     * @apiParam (输入字段) {string} contract_code 销售合同编号
     * @apiParam (输入字段) {string} project_code 项目编号
     * @apiParam (输入字段) {string} partner_name 合作方名称
     * @apiParam (输入字段) {string} corporation_name 交易主体名称
     * @apiParam (输入字段) {int} category 合同种类
     * @apiParam (输入字段) {string} manager_user_name 合同负责人
     * @apiParam (输入字段) {int} settle_status 结算状态
     * @apiParam (输入字段) {int} page 页数 <font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     *      "contract_code":'PHP20180321',
     *      "project_code":'ZPHP1ZJ18032101',
     *      "partner_name":"phpdragon合作方有限公司",
     *      "corporation_name":"公司主体phpdragon",
     *      "category":8,
     *      'manager_user_name'=>'admin',
     *      'settle_status'=>70,
     *      "page":2,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     * {
     *      "code":0,
     *      "data":{}
     * }
     * 失败返回：
     * {
     *      "code":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup SellContractSettlement
     * @apiVersion 1.0.0
     */
    
    public function actionList(){
        $contract_code = Mod::app()->request->getParam('contract_code');
        $project_code = Mod::app()->request->getParam('project_code');
        $partner_name = Mod::app()->request->getParam('partner_name');
        $corporation_name = Mod::app()->request->getParam('corporation_name');
        $category = Mod::app()->request->getParam('category');
        $manager_user_name = Mod::app()->request->getParam('manager_user_name');
        $settle_status = Mod::app()->request->getParam('settle_status');//合同状态
        $page = Mod::app()->request->getParam('page');
        $attr=array(
            'a.contract_code'=>$contract_code,
            'b.project_code'=>$project_code,
            'c.name*'=>$partner_name,
            'd.name*'=>$corporation_name,
            'a.category'=>$category,
            'e.name*'=>$manager_user_name,
            'a.status'=>$settle_status,
        );
        $sql = 'select {col} from t_contract a
                left join t_project b on b.project_id = a.project_id
                left join t_contract_settlement s on s.contract_id=a.contract_id 
                left join t_partner c on c.partner_id = a.partner_id
                left join t_corporation d on d.corporation_id = a.corporation_id
                left join t_system_user as e on e.user_id = a.manager_user_id
                left join t_contract_file f on f.contract_id = a.contract_id and f.is_main=1 and f.type=1 ' . $this->getWhereSql($attr) . ' and
                exists (select * from t_stock_out_detail so where so.contract_id = a.contract_id ) and a.type=2
                and ' . AuthorizeService::getUserDataConditionString('a') . ' order by a.contract_id desc {limit}';
        $user = Utility::getNowUser();
        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, 'a.contract_id, a.contract_code, a.project_id, b.project_code,ifnull(a.settle_type,4) as settle_type, a.partner_id,
             c.name as partner_name, a.corporation_id, d.name as corporation_name, a.category , a.status , e.name as manager_user_name , f.code_out,
             s.status as settle_status,ifNull(s.settle_id,0) settle_id');
        } else {
            $data = array();
        }
        //数据处理
        if(!empty($data['data']['rows'])){
            foreach ($data['data']['rows'] as $key=>$value){
                //链接
                $links=[];
                if(empty($value["settle_id"])&&$value['settle_type']!=\ddd\domain\entity\contractSettlement\SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT)
                    $links[]=array('name'=>'结算','params'=>array(0=>array('keyName'=>'contract_id','keyValue'=>$value['contract_id'])));
                else
                {
                    if($value['settle_status']!=\ddd\domain\entity\contractSettlement\SettlementStatus::STATUS_NEW){
                        if (\ddd\application\contractSettlement\SettleService::settlementIsCanEdit($value["settle_status"]))
                            $links[] = array('name' => '修改', 'params' => array(0 => array('keyName' => 'contract_id', 'keyValue' => $value['contract_id'])));
                            
                        if(!empty($value['settle_type']))
                            $links[] = array('name' => '查看', 'type' => 2, 'params' => array(0 => array('keyName' => 'contract_id', 'keyValue' => $value['contract_id'])));
                    }else{
                        $links[]=array('name'=>'结算','params'=>array(0=>array('keyName'=>'contract_id','keyValue'=>$value['contract_id'])));
                    }
                    
                   
                }
                $value['links']=$links;
                $data['data']['rows'][$key]=$value;
            }
            
        }
        $this->returnJson($data);
    }
    /**
     * @api {GET} / [90020001-getDeliveryContractSettlement] 获取结算对象
     * @apiName getDeliveryContractSettlement
     * @apiParam (输入字段) {string} contract_id 销售合同id
     * @apiExample {json} 输入示例:
     * {
     *      "contract_id":12,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     * {
           "code": 0,
           "data": {
                "contract": {
                    "contract_id": "763",
                    "project_id": "20180122006",
                    "relation_contract_id": "0",
                    "partner_id": "277",
                    "type": "2",
                    "settle_type": "4",
                    "num": null,
                    "category": null,
                    "is_main": false,
                    "contract_code": "HH17ZN180205S02",
                    "code_out": null,
                    "contrac_name": null,
                    "corporation_id": "7",
                    "agent_id": null,
                    "agent_type": null,
                    "currency": "1",
                    "exchange_rate": "1.000000",
                    "price_type": "1",
                    "formula": "",
                    "amount_cny": "1442000000",
                    "amount": "1442000000",
                    "agent_amount": null,
                    "manager_user_id": "46",
                    "status_time": "2018-02-27 17:14:36",
                    "status": "45",
                    "old_status": null,
                    "start_date": null,
                    "end_date": null,
                    "contract_date": null,
                    "contract_status": null,
                    "flag": null,
                    "remark": null,
                    "create_user_id": null,
                    "create_time": "2018-02-02 16:10:50",
                    "update_user_id": null,
                    "update_time": "2018-04-19 19:49:59",
                    "project_code": "ZHH17ZN18012203",
                    "corporation_name": "\u4e2d\u6cb9\u6d77\u5316\u77f3\u6cb9\u5316\u5de5\uff08\u5927\u8fde\uff09\u6709\u9650\u516c\u53f8",
                    "partner_name": null,
                    "agent_name": null,
                    "manager_user_name": "\u5f20\u9e4f",
                    "items": [
                        {
                            "goods_id": "8",
                            "goods_name": "92#\u8f66\u7528\u6c7d\u6cb9\uff08V\uff09",
                            "refer_target": "",
                            "quantity": {
                                "quantity": "2000.0000",
                                "unit": "2"
                            },
                            "unit": null,
                            "price": "721000",
                            "amount": "1442000000",
                            "more_or_less_rate": "0.100000"
                        }
                    ]
                },
                "checkLogs": [
                         {
                            "id": "3002",
                            "check_id": "1982",
                            "detail_id": "3176",
                            "user_id": "84",
                            "obj_id": "201803210004",
                            "business_id": "8",
                            "flow_id": "8",
                            "node_id": "8",
                            "check_status": "1",
                            "check_time": "2018-04-25 10:19:52",
                            "is_countersign": "0",
                            "status": "1",
                            "remark": "yyy",
                            "create_user_id": "84",
                            "create_time": "2018-04-25 10:19:52",
                            "update_user_id": "84",
                            "update_time": "2018-04-25 10:19:52",
                            "name": "\u6e29\u6587\u658c",
                            "node_name": "\u7269\u6d41\u8ddf\u5355\u5ba1\u6838"
                        }
                ],
                "contractSettlement": {
                    "settle_id": "20180418427",
                    "contract_id": "763",
                    "contract_code": "HH17ZN180205S02",
                    "project_id": "20180122006",
                    "project_code": "ZHH17ZN18012203",
                    "partner_id": "277",
                    "partner_name": null,
                    "corporation_id": "7",
                    "corporation_name": "\u4e2d\u6cb9\u6d77\u5316\u77f3\u6cb9\u5316\u5de5\uff08\u5927\u8fde\uff09\u6709\u9650\u516c\u53f8",
                    "settle_date": "2018-04-19",
                    "status": "2",
                    "settle_currency": "",
                    "settle_status": "2",
                    "settle_type": "4",
                    "goods_amount": "137",
                    "other_amount": "5382",
                    "amount_settle": null,
                    "settlementGoods": [
                        {
                            "item_id": "20180419117",
                            "goods_id": "8",
                            "goods_name": "92#\u8f66\u7528\u6c7d\u6cb9\uff08V\uff09",
                            "batch_id": null,
                            "order_id": null,
                            "batch_code": null,
                            "delivery_code": null,
                            "quantity": {
                                "quantity": "2.0000",
                                "unit": "2"
                            },
                            "quantity_sub": null,
                            "quantity_loss": {
                                "quantity": 198,
                                "unit": "2"
                            },
                            "quantity_loss_sub": null,
                            "price": "0",
                            "amount": "0",
                            "in_quantity": null,
                            "in_quantity_sub": null,
                            "out_quantity": {
                                "quantity": 200,
                                "unit": "2"
                            },
                            "out_quantity_sub": null,
                            "price_cny": "274",
                            "amount_cny": "137",
                            "unit_rate": "0.000000",
                            "hasDetail": true,
                            "lading_items": [],
                            "order_items": [
                                {
                                    "item_id": "20180418378",
                                    "goods_id": "8",
                                    "goods_name": "92#\u8f66\u7528\u6c7d\u6cb9\uff08V\uff09",
                                    "batch_id": null,
                                    "order_id": "201804192439",
                                    "batch_code": null,
                                    "delivery_code": "HH180419FH182",
                                    "quantity": {
                                        "quantity": null,
                                        "unit": "2"
                                    },
                                    "quantity_sub": null,
                                    "quantity_loss": {
                                        "quantity": 200,
                                        "unit": "2"
                                    },
                                    "quantity_loss_sub": null,
                                    "price": null,
                                    "amount": null,
                                    "in_quantity": null,
                                    "in_quantity_sub": null,
                                    "out_quantity": {
                                        "quantity": "200.0000",
                                        "unit": "2"
                                    },
                                    "out_quantity_sub": null,
                                    "price_cny": null,
                                    "amount_cny": null,
                                    "unit_rate": null,
                                    "hasDetail": false,
                                    "lading_items": null,
                                    "order_items": null,
                                    "settlementGoodsDetail": null
                                }
                            ],
                            "settlementGoodsDetail": {
                                "currency": {
                                    "id": 1,
                                    "name": "\u4eba\u6c11\u5e01",
                                    "ico": "\uffe5"
                                },
                                "currency_name": null,
                                "price_goods": "1",
                                "amount_currency": "2",
                                "exchange_rate": "1.000000",
                                "amount_goods": "2",
                                "exchange_rate_tax": "44.000000",
                                "amount_goods_tax": "88",
                                "adjust_type": {
                                    "id": 1,
                                    "name": "\u8c03\u589e"
                                },
                                "amount_adjust": "5",
                                "reason_adjust": "66",
                                "quantity": {
                                    "quantity": "2.0000",
                                    "unit": "2"
                                },
                                "quantity_actual": {
                                    "quantity": "2.0000",
                                    "unit": "2"
                                },
                                "amount": "274",
                                "amount_actual": "274",
                                "price": "137",
                                "price_actual": "137",
                                "remark": "65",
                                "settleFile": [
                                  {
                                        "id": "1",
                                        "type": null,
                                        "name": "3206.48\u5428\u7ed3\u7b97\u51fd.jpg",
                                        "file_url": "\/data\/oil\/upload\/stock\/stockIn201712110001-201712110100\/201712110001\/201712110001_1_1512962325_ouiep2.jpg"
                                    }
                                ],
                               "otherFile": [
                                  {
                                        "id": "1",
                                        "type": null,
                                        "name": "3206.48\u5428\u7ed3\u7b97\u51fd.jpg",
                                        "file_url": "\/data\/oil\/upload\/stock\/stockIn201712110001-201712110100\/201712110001\/201712110001_1_1512962325_ouiep2.jpg"
                                    }
                                ],
                                "tax_detail_item": [
                                    {
                                        "subject_list": {
                                            "id": 3,
                                            "name": "\u5173\u7a0e"
                                        },
                                        "rate": "3.000000",
                                        "price": "264",
                                        "amount": "132",
                                        "remark": "4"
                                    }
                                ],
                                "other_detail_item": [
                                    {
                                        "subject_list": {
                                            "id": 2,
                                            "name": "\u6e2f\u5efa\u8d39"
                                        },
                                        "rate": null,
                                        "price": "2",
                                        "amount": "3",
                                        "remark": "4"
                                    }
                                ]
                            }
                        }
                    ],
                    "other_expense": [
                        {
                            "detail_id": "20180419159",
                            "fee": "",
                            "currency": "",
                            "amount": "234",
                            "amount_cny": "5382",
                            "exchange_rate": "23.000000",
                            "remark": "23"
                        }
                    ],
                    "delivery_orders": null
                },
                "isCanSubmit": 1
            }
     * }
     * 失败返回：
     * {
     *      "code":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup SellContractSettlement
     * @apiVersion 1.0.0
     */
    public function actionGetDeliveryContractSettlement(){
        $contract_id = Mod::app()->request->getParam('contract_id');
        if (!Utility::checkQueryId($contract_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        //合同信息
        $ContractService = new \ddd\application\contractSettlement\ContractService();
        $contract=$ContractService->getContract($contract_id);
        if(empty($contract))
            $this->returnJsonError(OilError::$PROJECT_CONTRACT_NOT_EXIST,array('contract_id'=>$contract_id));
        $data['contract']=$contract;
        //审核记录
        $checkLogs=FlowService::getCheckLog($contract_id,22);
        $data['checkLogs']=$checkLogs;
        //合同结算
        $SellContractSettlementService = new \ddd\application\contractSettlement\SellContractSettlementService();
        $sellContractSettlement=$SellContractSettlementService->getSellContractSettlement($contract_id);
        if(is_string($sellContractSettlement)) $this->returnJsonError($sellContractSettlement,'-1');//抛出异常
        $data['contractSettlement']=$sellContractSettlement;
        //是否可结算
        $sellContractSettlementEntity = \ddd\repository\contractSettlement\SaleContractSettlementRepository::repository()->find('t.contract_id='.$contract_id);
        $isCanSubmit = 0;
        if(!empty($sellContractSettlementEntity)&&$SellContractSettlementService->isCanSubmit($sellContractSettlementEntity)){
            $isCanSubmit=1;
        }
        $data['isCanSubmit']=$isCanSubmit;
        
        $this->returnJson($data);
    }
    
    /**
     * @api {POST} / [90020001-save]  暂存、保存
     * @apiName save
     * @apiParam (输入字段) {int} contract_id 合同id
     * @apiParam (输入字段) {int} status 状态类型，1是暂存，2是保存
     * @apiParam (输入字段) {string} settle_date 结算日期
     * @apiParam (输入字段) {arr} goods_arr 商品结算信息
     * @apiParam (输入字段) {arr} not_goods_arr 非货款信息
     * @apiExample {json} 输入示例:
     {
         "contract_id":778,
         "settle_date":"2018-04-01",
         "status":0,
         "goods_arr"=>[
           0 =>{
                 'goods_id':8,
                 'item_id'=>20180502321,
                 'quantity':20,
                 'quantity_loss':1,
                 'price':1999,
                 'amount':69999,
                 'unit_rate':1,
                 'price_cny':1999,
                 'amount_cny':6999,
                 'hasDetail':1,     //是否是明细录入，0没有明细录入，1有明细录入
                 'remark':'备注',
                 'settleFile':[{},{}],
                 'otherFile'=>[{},{}],
                 'order_items'=>[
                          0=>{'order_id':'201803260001','out_quantity'=>33,'quantity':20,'quantity_loss':1,'price':33,'amount':999,'price_cny':11,'amount_cny':888}
                     ]
                 'settlementGoodsDetail':{
                     'currency':2,
                     'amount_currency':888,
                     'exchange_rate':1,
                     'amount_goods':4555,
                     'price_goods':333,
                     'exchange_rate_tax':444,
                     'amount_goods_tax':4445,
                     'adjust_type'=>1,
                     'amount_adjust':777,
                     'reason_adjust':'原因',
                     'quantity':11,
                     'quantity_actual':11,
                     'amount':999,
                     'amount_actual':999,
                     'price':555,
                     'price_actual':555,
                     'tax_detail_item'=>[
                         0=>{'subject_list':2,'rate':1,'price':11,'amount':66,'remark':'ddd'},
                         1=>{'subject_list':3,'rate':1,'price':110,'amount':66,'remark':'ddd'},
                     ],
                     'other_detail_item'=>[
                         0=>{'subject_list':2,'price':22,'amount':66,'remark':'ddd'},
                         1=>{'subject_list':3,'price':220,'amount':66,'remark':'ddd'},
                     ],
                     
             },
            }
         ],
         'not_goods_arr'=>[
             0=>{
                 'detail_id':20180409929,
                 'fee':2,
                 'currency':2,
                 'amount':999,
                 'amount_cny':6544,
                 'exchange_rate':6.9,
                 'remark':'eee'
             }
         ]
     }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     * {
     *      "code":0,
     *      "data":{}
     * }
     * 失败返回：
     * {
     *      "code":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup SellContractSettlement
     * @apiVersion 1.0.0
     */
    
    public function actionSave(){
        $contract_id = Mod::app()->request->getParam('contract_id');  //合同id
        $settle_date = Mod::app()->request->getParam('settle_date'); //结算日期
        $settle_status = Mod::app()->request->getParam('status'); //状态  0是暂存，1是保存
        $goods_arr = Mod::app()->request->getParam('goods_arr'); // 商品结算信息
        $not_goods_arr = Mod::app()->request->getParam('not_goods_arr'); // 非货款结算
        $post=array(
            'settle_date'=>$settle_date,
            'settle_status'=>$settle_status,
            'goods_arr'=>$goods_arr,
            'not_goods_arr'=>$not_goods_arr
        );
        if (!Utility::checkQueryId($contract_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        //合同信息
        $ContractService = new \ddd\application\contractSettlement\ContractService();
        $contract=$ContractService->getContract($contract_id);
        if(empty($contract))
            $this->returnJsonError(OilError::$PROJECT_CONTRACT_NOT_EXIST,array('contract_id'=>$contract_id));
        //获取DTO并赋值
        $SellContractSettlementService = new \ddd\application\contractSettlement\SellContractSettlementService();
        $newSellContractSettlementDTO = $SellContractSettlementService->AssignDTO($contract_id, $post);
        if(is_string($newSellContractSettlementDTO))
            $this->returnJsonError($newSellContractSettlementDTO,'-1');
        //保存数据
        $re = $SellContractSettlementService->saveDeliveryContractSettlement($newSellContractSettlementDTO);
        //print_r($status);
        if(is_array($re)){
            $this->returnValidateError($re);
        }else{
            if(is_string($re))
                $this->returnJsonError($re);
            else
                $this->returnJson('保存成功');
                    
        }
                    
                    
    }
    public function actionSave2(){
        $contract_id = Mod::app()->request->getParam('contract_id');  //合同id
        $settle_date = Mod::app()->request->getParam('settle_date'); //结算日期
        $settle_status = Mod::app()->request->getParam('status'); //状态  0是暂存，1是保存
        $goods_arr = Mod::app()->request->getParam('goods_arr'); // 商品结算信息
        $not_goods_arr = Mod::app()->request->getParam('not_goods_arr'); // 非货款结算
        $settle_date ="2018-04-08";
        $settle_status = "2";
        $not_goods_arr=array(
            0=>array(
                'detail_id'=>20180419159,
                'fee'=>2,
                'currency'=>2,
                'amount'=>999,
                'amount_cny'=>6544,
                'exchange_rate'=>6.9,
                'remark'=>'eee'
            )
        );
        $goods_arr=array(
            0 =>array(
                'goods_id'=>8,
                'quantity'=>20,
                'quantity_loss'=>14,
                'price'=>1999,
                'amount'=>69998,
                'unit_rate'=>1,
                'price_cny'=>1999,
                'amount_cny'=>6999,
                'order_items'=>array(
                    0=>array('order_id'=>'201803210012','out_quantity'=>33,'quantity'=>23,'quantity_loss'=>13,'price'=>33,'amount'=>999,'price_cny'=>33,'amount_cny'=>999),
                    1=>array('order_id'=>'201803210013','out_quantity'=>32,'quantity'=>22,'quantity_loss'=>12,'price'=>33,'amount'=>444,'price_cny'=>11,'amount_cny'=>888)
                ),
                'settlementGoodsDetail'=>array(
                    'currency'=>2,
                    'amount_currency'=>888,
                    'exchange_rate'=>1,
                    'amount_goods'=>4555,
                    'price_goods'=>333,
                    'exchange_rate_tax'=>444,
                    'amount_goods_tax'=>4445,
                    
                    'adjust_type'=>2,
                    'amount_adjust'=>777,
                    'reason_adjust'=>'原因w',
                    'quantity'=>11,
                    'quantity_actual'=>11,
                    'amount'=>999,
                    'amount_actual'=>999,
                    'price'=>555,
                    'price_actual'=>555,
                    'remark'=>'备注',
                    
                    'settleFile'=>array(
                        //0=>array('name'=>'附件1','file_url'=>'/data/aaa.pmg')
                    ),
                    'otherFile'=>[],
                    'tax_detail_item'=>array(
                        0=>array('subject_list'=>2,'rate'=>1,'price'=>11,'amount'=>66,'remark'=>'ddd'),
                        1=>array('subject_list'=>3,'rate'=>1,'price'=>110,'amount'=>66,'remark'=>'ddd'),
                    ),
                    'other_detail_item'=>array(
                        0=>array('subject_list'=>2,'price'=>22,'amount'=>66,'remark'=>'ddd'),
                        1=>array('subject_list'=>3,'price'=>220,'amount'=>66,'remark'=>'ddd'),
                    ),
                    
                ),
            ),
/*             1 =>array(
                'goods_id'=>2,
                'quantity'=>20,
                'quantity_loss'=>1,
                'price'=>1999,
                'amount'=>69999,
                'unit_rate'=>1,
                'price_cny'=>1999,
                'amount_cny'=>6999,
                'order_items'=>array(
                        0=>array('order_id'=>'201803210012','out_quantity'=>35,'quantity'=>25,'quantity_loss'=>15,'price'=>33,'amount'=>444,'price_cny'=>11,'amount_cny'=>888),
                        1=>array('order_id'=>'201803210013','out_quantity'=>34,'quantity'=>24,'quantity_loss'=>14,'price'=>33,'amount'=>444,'price_cny'=>11,'amount_cny'=>888)
                    ),
                'settlementGoodsDetail'=>array(
                    'currency'=>2,
                    'amount_currency'=>888,
                    'exchange_rate'=>1,
                    'amount_goods'=>4555,
                    'price_goods'=>333,
                    'exchange_rate_tax'=>444,
                    'amount_goods_tax'=>4445,
                    
                    'adjust_type'=>1,
                    'amount_adjust'=>777,
                    'reason_adjust'=>'原因',
                    'quantity'=>11,
                    'quantity_actual'=>11,
                    'amount'=>999,
                    'amount_actual'=>999,
                    'price'=>555,
                    'price_actual'=>555,
                    'remark'=>'备注',
                    
                    'settleFile'=>array(
                        //0=>array('name'=>'附件1','file_url'=>'/data/aaa.pmg')
                    ),
                    'otherFile'=>[],
                    'tax_detail_item'=>array(
                        0=>array('subject_list'=>2,'rate'=>1,'price'=>11,'amount'=>66,'remark'=>'goods2'),
                        1=>array('subject_list'=>3,'rate'=>1,'price'=>110,'amount'=>66,'remark'=>'goods2'),
                    ),
                    'other_detail_item'=>array(
                        0=>array('subject_list'=>2,'price'=>22,'amount'=>66,'remark'=>'goods2'),
                        1=>array('subject_list'=>3,'price'=>220,'amount'=>66,'remark'=>'goods2'),
                    ),
                   
                    
                ),
            ) */
        );
        $post=array(
            'settle_date'=>$settle_date,
            'settle_status'=>$settle_status,
            'goods_arr'=>$goods_arr,
            'not_goods_arr'=>$not_goods_arr
        );
        if (!Utility::checkQueryId($contract_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        //合同信息
        $ContractService = new \ddd\application\contractSettlement\ContractService();
        $contract=$ContractService->getContract($contract_id);
        if(empty($contract))
            $this->returnJsonError(OilError::$PROJECT_CONTRACT_NOT_EXIST,array('contract_id'=>$contract_id));
        //获取DTO并赋值
        $SellContractSettlementService = new \ddd\application\contractSettlement\SellContractSettlementService();
        $newSellContractSettlementDTO = $SellContractSettlementService->AssignDTO($contract_id, $post);
        if(is_string($newSellContractSettlementDTO))
            $this->returnJsonError($newSellContractSettlementDTO,'-1');
        //保存数据
        $re = $SellContractSettlementService->saveDeliveryContractSettlement($newSellContractSettlementDTO);
        //print_r($status);
        if(is_array($re)){
            $this->returnValidateError($re);
        }else{
            if(is_string($re))
                $this->returnJsonError($re);
            else
                $this->returnJson('保存成功');
                    
        }
                    
    }
    /**
     * @api {GET} / [90020001-submit] 提交
     * @apiName submit
     * @apiParam (输入字段) {string} contract_id 销售合同id
     * @apiExample {json} 输入示例:
     * {
     *      "contract_id":778,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     * {
     *      "code":0,
     *      "data":{}
     * }
     * 失败返回：
     * {
     *      "code":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup SellContractSettlement
     * @apiVersion 1.0.0
     */
    public function actionSubmit(){
        $contract_id = Mod::app()->request->getParam("contract_id");
        if (!Utility::checkQueryId($contract_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        
        $contract = Contract::model()->findByPk($contract_id);
        if (empty($contract->contract_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_EXIST, array('contract_id' => $contract_id)));
        }
        
        //是否可提交
        $SaleContractSettlementEntity = \ddd\repository\contractSettlement\SaleContractSettlementRepository::repository()->find('t.contract_id='.$contract_id);
        $DeliveryContractSettlementService = new \ddd\application\contractSettlement\DeliveryContractSettlementService();
        $isCanSubmit = $DeliveryContractSettlementService->isCanSubmit($SaleContractSettlementEntity);
        if(!$isCanSubmit) {
            $this->returnJsonError(BusinessError::outputError(OilError::$STOCK_BATCH_SETTLE_NOT_ALLOW_SUBMIT));
        }
        
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            $DeliveryContractSettlementService->submit($SaleContractSettlementEntity);
            FlowService::startFlowForCheck22($contract_id);
            
            TaskService::doneTask($contract_id, Action::ACTION_DELIVERY_CONTRACT_SETTLEMENT_BACK);
            $trans->commit();
            Utility::addActionLog(null, "修改销售合同结算信息", "DeliveryContractSettlement", $contract_id);
            $this->returnJson('提交成功！');
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }
            
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);
            
            $this->returnJsonError(BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $e->getMessage())));
        }
        
        
        
    }
   
    /**
     * @api {GET} / [90020001-saveFile] 货款结算附件上传
     * @apiName saveFile
     * @apiParam (输入字段) {int} id 标志id
     * @apiParam (输入字段) {int} type 类型，1是单据附件，2是其他附件
     * @apiParam (输入字段) {arr} files 文件信息
     * @apiExample {json} 输入示例:
     * {
     *      "id":779,
     *      "type"=>1,
     *      "files"=>[]
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     * {
     *      "code":0,
     *      "data":{}
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup SellContractSettlement
     * @apiVersion 1.0.0
     */
    public function actionSaveFile(){
        parent::actionSaveFile();
    }
    /**
     * @api {GET} / [90020001-saveFileOther] 非货款结算附件上传
     * @apiName saveFileOther
     * @apiParam (输入字段) {int} id 标志id
     * @apiParam (输入字段) {int} type 类型，1是单据附件，2是其他附件
     * @apiParam (输入字段) {arr} files 文件信息
     * @apiExample {json} 输入示例:
     * {
     *      "id":779,
     *      "type"=>1,
     *      "files"=>[]
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     * {
     *      "code":0,
     *      "data":{}
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup SellContractSettlement
     * @apiVersion 1.0.0
     */
    public function actionSaveFileOther(){
        $this->attachmentType = Attachment::C_CONTRACT_OTHER_SETTLEMENT;
        parent::actionSaveFile();
    }

    
    
    
    
    
    
}