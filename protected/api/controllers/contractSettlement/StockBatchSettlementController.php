<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/19 14:37
 * Describe：入库通知单结算
 */

class StockBatchSettlementController extends AttachmentController
{
    public function pageInit() {
        $this->attachmentType = Attachment::C_STOCK_BATCH_SETTLEMENT;
    }
    
    /**
     * @api {GET} / [90020001-list]列表
     * @apiName list
     * @apiParam (输入字段) {string} batch_code 入库通知单编号
     * @apiParam (输入字段) {string} contract_code 采购合同编号
     * @apiParam (输入字段) {string} code_out 外部合同编号
     * @apiParam (输入字段) {string} project_code 项目编号
     * @apiParam (输入字段) {string} partner_name 合作方名称
     * @apiParam (输入字段) {string} corporation_name 交易主体名称
     * @apiParam (输入字段) {int} delivery_way 发货方式
     * @apiParam (输入字段) {int} page 页数 <font color=red>必填</font>
     * @apiExample {json} 输入示例:
     *  "search":search[
     *      "code":"PHP20180321-2",
     *      "contract_code":'PHP20180321',
     *      "code_out":PHP20180321,
     *      "project_code":'ZPHP1ZJ18032101',
     *      "partner_name":"phpdragon合作方有限公司",
     *      "corporation_name":"公司主体phpdragon",
     *      "delivery_way":1,
     * ],
     *  "page":2,
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
     * @apiGroup StockBatchSettlement
     * @apiVersion 1.0.0
     */
   
    public function actionList(){
        $search=Mod::app()->request->getParam('search'); 
        $attr=array(
            'a.code'=>$search['code'],
            'e.contract_code'=>$search['contract_code'],
            'f.code_out*'=>$search['code_out'],
            'b.project_code'=>$search['project_code'],
            'c.name*'=>$search['partner_name'],
            'd.name*'=>$search['corporation_name'],
            'a.type'=>$search['delivery_way']
        );
        $sql = 'select {col} from t_stock_in_batch a
                left join t_lading_settlement as s on s.lading_id = a.batch_id
                left join t_contract e on a.contract_id = e.contract_id
                left join t_project b on e.project_id = b.project_id
                left join t_partner c on c.partner_id = e.partner_id
                left join t_corporation d on d.corporation_id = e.corporation_id 
                left join t_contract_file f on e.contract_id = f.contract_id and f.is_main=1 and f.type=1 ' . $this->getWhereSql($attr) . ' 
                and a.status >=' . StockNotice::STATUS_SUBMIT . ' and
                exists (select * from t_stock_in si where si.batch_id = a.batch_id )
                and ' . AuthorizeService::getUserDataConditionString('e') . ' order by e.contract_id desc, a.batch_id desc {limit}';
        $user = Utility::getNowUser();
        
        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, 'e.contract_id, settle_type,e.contract_code, b.project_id, b.project_code, e.partner_id, c.name as partner_name, e.corporation_id,
             d.name as corporation_name, a.batch_id, a.code, a.type, a.status, f.code_out,
             s.status as settle_status,ifnull(s.settle_id,0) as settle_id ');
        } else {
            $data = array();
        }
        
        //数据处理
        if(!empty($data['data']['rows'])){
            foreach ($data['data']['rows'] as $key=>$value){
                //链接
                $links=[];
                if(empty($value["settle_id"]))
                    $links[]=array('name'=>'结算','params'=>array(0=>array('keyName'=>'batch_id','keyValue'=>$value['batch_id'])));
                else
                {
                    if($value['settle_status']!=\ddd\domain\entity\contractSettlement\SettlementStatus::STATUS_NEW){//已结算过
                        if (\ddd\application\contractSettlement\SettleService::settlementIsCanEdit($value["settle_status"]))
                            $links[] = array('name' => '修改', 'params' => array(0 => array('keyName' => 'batch_id', 'keyValue' => $value['batch_id'])));
                        
                        if(!empty($value['settle_type'])){
                            if($value['settle_type']==\ddd\domain\entity\contractSettlement\SettlementMode::LADING_BILL_MODE_SETTLEMENT)
                                $links[] = array('name' => '查看', 'type' => 1, 'params' => array(0 => array('keyName' => 'batch_id', 'keyValue' => $value['batch_id'])));
                            else
                                $links[] = array('name'=>'查看','type'=>2,'params'=>array(0=>array('keyName'=>'contract_id','keyValue'=>$value['contract_id'])));
                        }
                    }else{
                        $links[]=array('name'=>'结算','params'=>array(0=>array('keyName'=>'batch_id','keyValue'=>$value['batch_id'])));
                    }
                    
                }
               $value['links']=$links;
               $data['data']['rows'][$key]=$value;
            }
           
        }
        $this->returnJson($data);
    }

     /**
     * @api {GET} / [90020001-getStockBatchSettlement] 获取入库通知单结算对象
     * @apiName getStockBatchSettlement
     * @apiParam (输入字段) {string} batch_id 入库通知单id
     * @apiExample {json} 输入示例:
     * {
     *      "batch__id":201712150002,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     * {
        "code": 0,
        "data": {
            "stockInBatch": {
                "batch_code": "PHP20180321-2",
                "contract_id": "778",
                "contract_code": "PHP20180321",
                "project_code": "ZPHP1ZJ18032101",
                "partner_id": "451",
                "partner_name": null,
                "corporation_id": "10",
                "corporation_name": "\u516c\u53f8\u4e3b\u4f53phpdragon",
                "remark": "555",
                "status": "20",
                "files": [
                    {
                        "id": "1",
                        "type": null,
                        "name": "\u7ed3\u7b97\u51fd\uff08\u53cc\u7b7e\uff09\u8d85\u701a-\u4e9a\u592a-100\u5428 YT201712002.pdf",
                        "file_url": "\/data\/oil\/upload\/stock\/stockNotice201712180001-201712180100\/201712180003\/201712180003_1_1513606209_2w1sze.pdf"
                    }
                ],
                "items": [
                    {
                        "goods_id": "1",
                        "goodsName": "\u6210\u54c1\u6cb9",
                        "quantity": {
                            "quantity": "10000.0000",
                            "unit": "2"
                        },
                        "quantity_sub": {
                            "quantity": null,
                            "unit": null
                        },
                        "in_quantity": {
                            "quantity": "100.0000",
                            "unit": "2"
                        },
                        "in_quantity_sub": {
                            "quantity": null,
                            "unit": null
                        },
                        "quantity_not": {
                            "quantity": 9900,
                            "unit": "2"
                        },
                        "quantity_not_sub": {
                            "quantity": 0,
                            "unit": null
                        },
                        "remark": "",
                        "store_name": "\u6d4b\u8bd5\u4ed3\u5e93phpdragon",
                        "unit_rate": 1
                    },
                    {
                        "goods_id": "2",
                        "goodsName": "\u67f4\u6cb9",
                        "quantity": {
                            "quantity": "20000.0000",
                            "unit": "2"
                        },
                        "quantity_sub": {
                            "quantity": null,
                            "unit": null
                        },
                        "in_quantity": {
                            "quantity": "220.0000",
                            "unit": "2"
                        },
                        "in_quantity_sub": {
                            "quantity": null,
                            "unit": null
                        },
                        "quantity_not": {
                            "quantity": 19780,
                            "unit": "2"
                        },
                        "quantity_not_sub": {
                            "quantity": 0,
                            "unit": null
                        },
                        "remark": "",
                        "store_name": "\u6d4b\u8bd5\u4ed3\u5e93phpdragon",
                        "unit_rate": 1
                    }
                ],
                "settleItems": null,
                "batch_id": "201803210004",
                "batch_date": "2018-03-21"
            },
            "stockIn": [
                {
                    "stock_in_id": "201803210008",
                    "project_id": null,
                    "contract_id": "778",
                    "batch_id": null,
                    "code": "PHP20180321-2-1",
                    "store_id": "25",
                    "type": "1",
                    "entry_date": "2018-03-21",
                    "order_index": null,
                    "currency": null,
                    "exchange_rate": null,
                    "amount_cny": null,
                    "amount": null,
                    "status_time": null,
                    "status": "-5",
                    "remark": "1111",
                    "create_user_id": null,
                    "create_time": null,
                    "update_user_id": null,
                    "update_time": null,
                    "items": [
                        {
                            "goods_id": "1",
                            "goods_name": "\u6210\u54c1\u6cb9",
                            "quantity": {
                                "quantity": "100.0000",
                                "unit": "2"
                            },
                            "unit": "2",
                            "unit_rate": "1.000000",
                            "remark": ""
                        },
                        {
                            "goods_id": "2",
                            "goods_name": "\u67f4\u6cb9",
                            "quantity": {
                                "quantity": "200.0000",
                                "unit": "2"
                            },
                            "unit": "2",
                            "unit_rate": "1.000000",
                            "remark": ""
                        }
                    ],
                    "check_remark": null,
                    "store_name": "\u6d4b\u8bd5\u4ed3\u5e93phpdragon",
                    "files": [
                                  {
                                        "id": "1",
                                        "type": null,
                                        "name": "3206.48\u5428\u7ed3\u7b97\u51fd.jpg",
                                        "file_url": "\/data\/oil\/upload\/stock\/stockIn201712110001-201712110100\/201712110001\/201712110001_1_1512962325_ouiep2.jpg"
                                    }
                                ],
                }
            ],
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
            "stockInBatchBalance": {
                "batch_id": "201803210004",
                "settle_id": "201804230002",
                "batch_code": "PHP20180321-2",
                "project_id": "20180321009",
                "project_code": "ZPHP1ZJ18032101",
                "contract_id": "778",
                "contract_code": "PHP20180321",
                "partner_id": "451",
                "partner_name": null,
                "corporation_id": "10",
                "corporation_name": "\u516c\u53f8\u4e3b\u4f53phpdragon",
                "settle_date": "2018-04-25",
                "settle_status": "10",
                "settle_currency": {
                    "id": 1,
                    "name": "\u4eba\u6c11\u5e01",
                    "ico": "\uffe5"
                },
                "goods_amount": "71",
                "settlementGoods": [
                    {
                        "batch_id": "201803210004",
                        "order_id": "201803210004",
                        "item_id": "20180425897",
                        "batch_code": "PHP20180321-2",
                        "delivery_code": null,
                        "goods_id": "1",
                        "goods_name": "\u6210\u54c1\u6cb9",
                        "quantity": {
                            "quantity": "23.0000",
                            "unit": "2"
                        },
                        "quantity_sub": null,
                        "quantity_loss": {
                            "quantity": 77,
                            "unit": "2"
                        },
                        "quantity_loss_sub": null,
                        "price": "0",
                        "amount": "0",
                        "in_quantity": {
                            "quantity": "100.0000",
                            "unit": "2"
                        },
                        "in_quantity_sub": null,
                        "out_quantity": null,
                        "out_quantity_sub": null,
                        "price_cny": "402",
                        "amount_cny": "17",
                        "unit_rate": "0.000000",
                        "lading_items": [
                            {
                                "batch_id": "201803210004",
                                "order_id": null,
                                "item_id": "20180425897",
                                "batch_code": "PHP20180321-2",
                                "delivery_code": null,
                                "goods_id": "1",
                                "goods_name": "\u6210\u54c1\u6cb9",
                                "quantity": {
                                    "quantity": null,
                                    "unit": "2"
                                },
                                "quantity_sub": null,
                                "quantity_loss": {
                                    "quantity": 100,
                                    "unit": "2"
                                },
                                "quantity_loss_sub": null,
                                "price": null,
                                "amount": null,
                                "in_quantity": {
                                    "quantity": "100.0000",
                                    "unit": "2"
                                },
                                "in_quantity_sub": null,
                                "out_quantity": null,
                                "out_quantity_sub": null,
                                "price_cny": null,
                                "amount_cny": null,
                                "unit_rate": null,
                                
                            }
                        ],
                        "order_items": [],
                        "hasDetail": true,
                        "settlementGoodsDetail": {
                            "currency": {
                                "id": 1,
                                "name": "\u4eba\u6c11\u5e01",
                                "ico": "\uffe5"
                            },
                            "currency_name": null,
                            "price_goods": "0",
                            "amount_currency": "3",
                            "exchange_rate": "1.000000",
                            "amount_goods": "3",
                            "exchange_rate_tax": "44.000000",
                            "amount_goods_tax": "132",
                            "adjust_type": {
                                "id": 1,
                                "name": "\u8c03\u589e"
                            },
                            "amount_adjust": "3",
                            "reason_adjust": "44",
                            "quantity": {
                                "quantity": "23.0000",
                                "unit": "2"
                            },
                            "quantity_actual": {
                                "quantity": "23.0000",
                                "unit": "2"
                            },
                            "amount": "402",
                            "amount_actual": "402",
                            "price": "17",
                            "price_actual": "17",
                            "remark": "2323",
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
                                    "subject_name": null,
                                    "type_name": null,
                                    "rate": "3.000000",
                                    "price": "396",
                                    "amount": "17",
                                    "remark": "4"
                                }
                            ],
                            "other_detail_item": [
                                {
                                    "subject_list": {
                                        "id": 3,
                                        "name": "\u5173\u7a0e"
                                    },
                                    "subject_name": null,
                                    "type_name": null,
                                    "rate": "3.000000",
                                    "price": "396",
                                    "amount": "17",
                                    "remark": "4"
                                }
                            ],
                        }
                    },
                    {
                        "batch_id": "201803210004",
                        "order_id": "201803210004",
                        "item_id": "20180425899",
                        "batch_code": "PHP20180321-2",
                        "delivery_code": null,
                        "goods_id": "2",
                        "goods_name": "\u67f4\u6cb9",
                        "quantity": {
                            "quantity": "23.0000",
                            "unit": "2"
                        },
                        "quantity_sub": null,
                        "quantity_loss": {
                            "quantity": 197,
                            "unit": "2"
                        },
                        "quantity_loss_sub": null,
                        "price": "0",
                        "amount": "0",
                        "in_quantity": {
                            "quantity": "220.0000",
                            "unit": "2"
                        },
                        "in_quantity_sub": null,
                        "out_quantity": null,
                        "out_quantity_sub": null,
                        "price_cny": "1228",
                        "amount_cny": "53",
                        "unit_rate": "0.000000",
                        "lading_items": [
                            {
                                "batch_id": "201803210004",
                                "order_id": null,
                                "item_id": "20180425899",
                                "batch_code": "PHP20180321-2",
                                "delivery_code": null,
                                "goods_id": "2",
                                "goods_name": "\u67f4\u6cb9",
                                "quantity": {
                                    "quantity": null,
                                    "unit": "2"
                                },
                                "quantity_sub": null,
                                "quantity_loss": {
                                    "quantity": 220,
                                    "unit": "2"
                                },
                                "quantity_loss_sub": null,
                                "price": null,
                                "amount": null,
                                "in_quantity": {
                                    "quantity": "220.0000",
                                    "unit": "2"
                                },
                                "in_quantity_sub": null,
                                "out_quantity": null,
                                "out_quantity_sub": null,
                                "price_cny": null,
                                "amount_cny": null,
                                "unit_rate": null,
                               
                            }
                        ],
                        "order_items": [],
                        "hasDetail": true,
                        "settlementGoodsDetail": {
                            "currency": {
                                "id": 1,
                                "name": "\u4eba\u6c11\u5e01",
                                "ico": "\uffe5"
                            },
                            "currency_name": null,
                            "price_goods": "1",
                            "amount_currency": "34",
                            "exchange_rate": "1.000000",
                            "amount_goods": "34",
                            "exchange_rate_tax": "0.000000",
                            "amount_goods_tax": "34",
                            "adjust_type": {
                                "id": 1,
                                "name": "\u8c03\u589e"
                            },
                            "amount_adjust": "34",
                            "reason_adjust": "4",
                            "quantity": {
                                "quantity": "23.0000",
                                "unit": "2"
                            },
                            "quantity_actual": {
                                "quantity": "23.0000",
                                "unit": "2"
                            },
                            "amount": "1228",
                            "amount_actual": "1228",
                            "price": "53",
                            "price_actual": "53",
                            "remark": "245",
                            "settleFile": [
                                  {
                                        "id": "1",
                                        "type": null,
                                        "name": "3206.48\u5428\u7ed3\u7b97\u51fd.jpg",
                                        "file_url": "\/data\/oil\/upload\/stock\/stockIn201712110001-201712110100\/201712110001\/201712110001_1_1512962325_ouiep2.jpg"
                                    }
                                ],
                            "otherFile":[
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
                                    "rate": "34.000000",
                                    "price": "1156",
                                    "amount": "50",
                                    "remark": ""
                                }
                            ],
                            "other_detail_item": [
                                {
                                    "subject_list": {
                                        "id": 2,
                                        "name": "\u6e2f\u5efa\u8d39"
                                    },
                                    "rate": null,
                                    "price": "0",
                                    "amount": "4",
                                    "remark": "3"
                                }
                            ]
                        }
                    }
                ]
            },
            "isCanSubmit": 0
        }
    }
     * 失败返回：
     * {
     *      "code":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup StockBatchSettlement
     * @apiVersion 1.0.0
     */
    public function actionGetStockBatchSettlement(){
        $batch_id = Mod::app()->request->getParam('batch_id');
        if (!Utility::checkQueryId($batch_id)) {
            $this->returnJsonError(OilError::$PARAMS_PASS_ERROR);
        }
        //入库通知单
        $StockInBatchService = new \ddd\application\stock\LadingBillService();
        $stockInBatch=$StockInBatchService->getLadingBill($batch_id);
        if(empty($stockInBatch))
            $this->returnJsonError(OilError::$STOCK_BATCH_NOT_EXIST,array('batch_id'=>$batch_id)); 
        $data['stockInBatch']=$stockInBatch;
        //入库单
        $StockInService = new \ddd\application\stock\StockInService();
        $data['stockIn']=$StockInService->getStockInByBatchId($batch_id); 
        //审核记录
        $checkLogs=FlowService::getCheckLog($batch_id,8);
        $data['checkLogs']=$checkLogs;
        //入库通知单商品结算
        $stockBatchSettlementService = new \ddd\application\contractSettlement\StockBatchSettlementService();
        $stockBatchSettlement=$stockBatchSettlementService->getStockBatchSettlement($batch_id);
        if(is_string($stockBatchSettlement)) $this->returnJsonError($stockBatchSettlement,'-1');//抛出异常 
        $data['stockInBatchBalance']=$stockBatchSettlement;
        //是否可结算
        $stockBatchSettlementEntity = \ddd\repository\contractSettlement\LadingBillSettlementRepository::repository()->find('t.lading_id='.$batch_id);
        $isCanSubmit = 0;
        if(!empty($stockBatchSettlementEntity)&&$stockBatchSettlementService->isCanSubmit($stockBatchSettlementEntity)){
         $isCanSubmit=1;
        }  
        $data['isCanSubmit']=$isCanSubmit;
        
        $this->returnJson($data);
       
    }
    /**
     * @api {POST} / [90020001-save] 暂存、保存
     * @apiName save
     * @apiParam (输入字段) {int} batch_id 入库通知单id
     * @apiParam (输入字段) {int} status 状态类型，1是暂存，2是保存
     * @apiParam (输入字段) {string} settle_date 结算日期
     * @apiParam (输入字段) {arr} goods_arr 商品结算信息
     * @apiExample {json} 输入示例:
       {
           "batch_id":201712110001,    //入库通知单id
           "settle_date":"2018-04-01", //结算日期
           "status":0,   //状态类型，1是暂存，2是保存 
           "goods_arr"=>[
                         0 =>{
                            'goods_id':8,   //商品id
                            'item_id'=>20180502321,
                            'quantity':20,  //结算数量，必填
                            'quantity_loss':1, //损耗量，必填
                            'price':1999,      //结算单价，必填
                            'amount':69999,    //结算金额，必填
                            'unit_rate':1,     //结算汇率，必填
                            'price_cny':1999,  //人民币结算单价，必填
                            'amount_cny':6999, //人民币结算金额，必填
                            'hasDetail':1,     //是否是明细录入，0没有明细录入，1有明细录入
                            'remark':'备注',
                            'settleFile':[{},{}],
                            'otherFile'=>[{},{}],
                            'settlementGoodsDetail':{//录入明细
                                'currency':2,  //结算币种id
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
                       ]
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
     * @apiGroup StockBatchSettlement
     * @apiVersion 1.0.0
     */
   
    public function actionSave(){
        $batch_id = Mod::app()->request->getParam('batch_id');  //入库通知单id
        $settle_date = Mod::app()->request->getParam('settle_date'); //结算日期
        $settle_status = Mod::app()->request->getParam('status'); //状态  0是暂存，1是保存
        $goods_arr = Mod::app()->request->getParam('goods_arr'); // 商品结算信息
        $post=array(
            'settle_date'=>$settle_date,
            'settle_status'=>$settle_status,
            'goods_arr'=>$goods_arr
        );
        if (!Utility::checkQueryId($batch_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        } 
        //入库通知单
        $StockInBatchService = new \ddd\application\stock\LadingBillService();
        $batchInBatchEntity=$StockInBatchService->getLadingBill($batch_id);
        if(empty($batchInBatchEntity))
            $this->returnJsonError(OilError::$STOCK_BATCH_NOT_EXIST,array('batch_id'=>$batch_id)); 
        //获取DTO并赋值
        $stockBatchSettlementService = new \ddd\application\contractSettlement\StockBatchSettlementService();
        $newStockInBatchSettlementDTO = $stockBatchSettlementService->AssignDTO($batch_id, $post);
        if(is_string($newStockInBatchSettlementDTO))
            $this->returnJsonError($newStockInBatchSettlementDTO,'-1');
        //保存结算单
        $re = $stockBatchSettlementService->saveLadingBillSettlement($newStockInBatchSettlementDTO);
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
        $batch_id = Mod::app()->request->getParam('batch_id');  //入库通知单id
        $settle_date = Mod::app()->request->getParam('settle_date'); //结算日期
        $settle_status = Mod::app()->request->getParam('status'); //状态  0是暂存，1是保存
        $goods_arr = Mod::app()->request->getParam('goods_arr'); // 商品结算信息
        $settle_date ="2018-04-20";
        $settle_status = "2";
        $goods_arr=array(
             0 =>array(
                 'goods_id'=>8,
                 'quantity'=>2,
                 'quantity_loss'=>0,
                 'price'=>'',
                 'amount'=>'',
                 'unit_rate'=>'',
                 'price_cny'=>33,
                 'amount_cny'=>88,
                 'settlementGoodsDetail'=>array(
                 'currency'=>2,
                 'amount_currency'=>'',
                 'exchange_rate'=>'',
                 'amount_goods'=>555,
                 'price_goods'=>'',
                 'exchange_rate_tax'=>'',
                 'amount_goods_tax'=>'',
                 
                 'adjust_type'=>1,
                 'amount_adjust'=>2,
                 'reason_adjust'=>'调整原因啊',
                 'quantity'=>11,
                 'quantity_actual'=>11,
                 'amount'=>45,
                 'amount_actual'=>999,
                 'price'=>555,
                 'price_actual'=>555,
                 'remark'=>'备注',
                 
                 'settleFile'=>array(
                 //0=>array('name'=>'附件1','file_url'=>'/data/aaa.pmg')
                 ),
                 'otherFile'=>[],
                 'tax_detail_item'=>array(
                 0=>array('subject_list'=>'','rate'=>1,'price'=>11,'amount'=>66,'remark'=>'ddd'),
                 1=>array('subject_list'=>3,'rate'=>1,'price'=>110,'amount'=>66,'remark'=>'ddd'),
                 ),
                 'other_detail_item'=>array(
                 0=>array('subject_list'=>2,'price'=>22,'amount'=>66,'remark'=>'ddd'),
                 1=>array('subject_list'=>3,'price'=>220,'amount'=>66,'remark'=>'ddd'),
                 ),
                 
                 ),
             ),
             
         );   
        $post=array(
            'settle_date'=>$settle_date,
            'settle_status'=>$settle_status,
            'goods_arr'=>$goods_arr
        );
        if (!Utility::checkQueryId($batch_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        //入库通知单
        $StockInBatchService = new \ddd\application\stock\LadingBillService();
        $batchInBatchEntity=$StockInBatchService->getLadingBill($batch_id);
        if(empty($batchInBatchEntity))
            $this->returnJsonError(OilError::$STOCK_BATCH_NOT_EXIST,array('batch_id'=>$batch_id));
        //获取DTO并赋值
        $stockBatchSettlementService = new \ddd\application\contractSettlement\StockBatchSettlementService();
        $newStockInBatchSettlementDTO = $stockBatchSettlementService->AssignDTO($batch_id, $post);
        if(is_string($newStockInBatchSettlementDTO))
            $this->returnJsonError($newStockInBatchSettlementDTO,'-1');
        //保存结算单
        $re = $stockBatchSettlementService->saveLadingBillSettlement($newStockInBatchSettlementDTO);
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
     * @api {GET} / [90020001-submit]提交
     * @apiName submit  
     * @apiParam (输入字段) {string} batch_id 入库通知单id
     * @apiExample {json} 输入示例:
     * {
     *      "batch_id":201712110001,
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
     * @apiGroup StockBatchSettlement
     * @apiVersion 1.0.0
     */
    public function actionSubmit() {
        $batchId = Mod::app()->request->getParam("batch_id");
        if (!Utility::checkQueryId($batchId)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        
        $stockNotice = StockNotice::model()->findByPk($batchId);
        if (empty($stockNotice->batch_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_EXIST, array('batch_id' => $batchId)));
        }
       
        //是否可提交
        $stockBatchSettlementEntity = \ddd\repository\contractSettlement\LadingBillSettlementRepository::repository()->find('t.lading_id='.$batchId);
        $stockBatchSettlementService = new \ddd\application\contractSettlement\StockBatchSettlementService();
        if(empty($stockBatchSettlementEntity))
            $this->returnJsonError('该入库通知单没有结算信息');
        $isCanSubmit = $stockBatchSettlementService->isCanSubmit($stockBatchSettlementEntity);
        if(!$isCanSubmit) {
            $this->returnJsonError(BusinessError::outputError(OilError::$STOCK_BATCH_SETTLE_NOT_ALLOW_SUBMIT));
        } 
        
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            $stockBatchSettlementService->submit($stockBatchSettlementEntity);
            FlowService::startFlowForCheck8($batchId);
            TaskService::doneTask($batchId, Action::ACTION_STOCK_BATCH_SETTLE_BACK);
            $trans->commit();
            Utility::addActionLog(null, "提交入库通知单结算审核", "StockBatchSettlement", $batchId);
            $this->returnJson('提交成功');
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
     * @apiParam (输入字段) {int} type 类型，1是单据附件，11是其他附件
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
     * @apiGroup StockBatchSettlement
     * @apiVersion 1.0.0
     */
    public function actionSaveFile(){
        parent::actionSaveFile();
    }
    
    

}