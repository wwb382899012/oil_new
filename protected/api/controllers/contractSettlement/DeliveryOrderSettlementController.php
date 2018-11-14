<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/19 14:37
 * Describe：发货单结算
 */

class DeliveryOrderSettlementController extends AttachmentController
{
    public function pageInit() {
        $this->attachmentType = Attachment::C_DELIVERY_ORDER_SETTLEMENT;
    }
    /**
     * @api {GET} / [90020001-list] 列表
     * @apiName list
     * @apiParam (输入字段) {string} code 发货单编号
     * @apiParam (输入字段) {string} contract_code 合同编号
     * @apiParam (输入字段) {string} project_code 项目编号
     * @apiParam (输入字段) {string} partner_name 合作方名称
     * @apiParam (输入字段) {string} corporation_name 交易主体名称
     * @apiParam (输入字段) {string} manager_user_name 合同负责人
     * @apiParam (输入字段) {int} category 合同类型
     * @apiParam (输入字段) {int} status 状态
     * @apiParam (输入字段) {int} page 页数 <font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     *      "code":"PHP20180321-2",
     *      "contract_code":"rretert2342",
     *      "project_code":"werwer345345",
     *      "partner_name":"phpdragon合作方有限公司",
     *      "corporation_name":"公司主体phpdragon",
     *      "delivery_way":1,
     *      "category":6,
     *      "status":-2,
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
     * @apiGroup DeliveryOrderSettlement
     * @apiVersion 1.0.0
     */
   
    public function actionList(){
        $code = Mod::app()->request->getParam('code');
        $contract_code = Mod::app()->request->getParam('contract_code');
        $project_code = Mod::app()->request->getParam('project_code');
        $partner_name = Mod::app()->request->getParam('partner_name');
        $corporation_name = Mod::app()->request->getParam('corporation_name');
        $manager_user_name = Mod::app()->request->getParam('manager_user_name');
        $category = Mod::app()->request->getParam('category');
        $status = Mod::app()->request->getParam('status');
        $page = Mod::app()->request->getParam('page');
        $attr=array(
            'a.code'=>$code,
            'b.contract_code'=>$contract_code,
            'p.project_code'=>$project_code,
            'c.name*'=>$partner_name,
            'd.name*'=>$corporation_name,
            'b.category'=>$category,
            'status'=>$status,
            'u.name*'=>$manager_user_name
        );
       
        if(!is_array($attr) || !array_key_exists("status",$attr))
        {
            $attr["status"]="-2";
        }
        
        $query="";
        $status="";
        
        if(isset($attr["status"]) && $attr["status"]=="-2"){
            $status="-2";
            $query=" and (a.status=".DeliveryOrder::STATUS_PASS." or a.status=".DeliveryOrder::STATUS_SETTLE_BACK.")";
            unset($attr["status"]);
        }else if($attr["status"]=="-1"){
            $status="-1";
            $query=" and a.status=".DeliveryOrder::STATUS_SETTLE_BACK;
            unset($attr["status"]);
        }else if($attr["status"]=="0"){
            $status="0";
            $query=" and a.status=".DeliveryOrder::STATUS_PASS;
            unset($attr["status"]);
        }else if($attr["status"]=="1"){
            $status="1";
            $query=" and a.status=".DeliveryOrder::STATUS_SETTLE_SUBMIT;
            unset($attr["status"]);
        }else if($attr["status"]=="2"){
            $status="2";
            $query=" and a.status=".DeliveryOrder::STATUS_SETTLE_PASS;
            unset($attr["status"]);
        }
      
        $user = SystemUser::getUser(Utility::getNowUserId());
        
        $sql = 'select {col} from t_delivery_order a
                left join t_delivery_settlement as s on s.order_id = a.order_id
                left join t_contract b on b.contract_id = a.contract_id
                left join t_partner c on c.partner_id = a.partner_id
                left join t_project p on p.project_id = a.project_id
                left join t_system_user as u on u.user_id = b.manager_user_id
                left join t_stock_in e on e.stock_in_id = a.stock_in_id
                left join t_corporation d on d.corporation_id = a.corporation_id ' . $this->getWhereSql($attr) .
                $query . ' and a.corporation_id in ('.$user['corp_ids'].') and a.status>='.DeliveryOrder::STATUS_PASS.
                ' and exists(select order_id FROM t_stock_out_order e WHERE e.order_id=a.order_id )
                order by a.order_id desc {limit}';
       $data = $this->queryTablesByPage($sql,
                    'a.partner_id, c.name as partner_name, a.corporation_id,a.settle_date,a.project_id,a.contract_id,b.settle_type,p.project_code,
            a.contract_id,b.contract_code,
            a.type, d.name as corporation_name, a.order_id, a.code,
            a.status, a.stock_in_id, e.code as stock_in_code,
            s.status as settle_status,ifnull(s.settle_id,0)as settle_id,
            case when a.status='.DeliveryOrder::STATUS_PASS.' then 0
            when a.status='.DeliveryOrder::STATUS_SETTLE_BACK.' then -1
            when a.status='.DeliveryOrder::STATUS_SETTLE_SUBMIT.' then 1
            when a.status='.DeliveryOrder::STATUS_SETTLE_PASS.' then 2 end as status_desc');
                
        if($status=="-2" || $status=="-1" || $status=="0" || $status=="1" || $status=="2")
            $attr['status'] = $status;
            $data["search"]=$attr;
        //数据处理
        if(!empty($data['data']['rows'])){
            foreach ($data['data']['rows'] as $key=>$value){
                //链接
                $links=[];
                if(empty($value["settle_id"])&&$value['settle_type']!=\ddd\domain\entity\contractSettlement\SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT)
                    $links[]=array('name'=>'结算','params'=>array(0=>array('keyName'=>'order_id','keyValue'=>$value['order_id'])));
                else
                {
                    if($value['settle_status']!=\ddd\domain\entity\contractSettlement\SettlementStatus::STATUS_NEW){//已经结算过
                        if (\ddd\application\contractSettlement\SettleService::settlementIsCanEdit($value["settle_status"]))
                            $links[] = array('name' => '修改', 'params' => array(0 => array('keyName' => 'order_id', 'keyValue' => $value['order_id'])));
                        
                        if(!empty($value['settle_type'])){
                            if($value['settle_type']==\ddd\domain\entity\contractSettlement\SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT)
                                $links[] = array('name' => '查看', 'type' => 1, 'params' => array(0 => array('keyName' => 'order_id', 'keyValue' => $value['order_id'])));
                            else
                                $links[] = array('name'=>'查看','type'=>2,'params'=>array(0=>array('keyName'=>'contract_id','keyValue'=>$value['contract_id'])));
                        }
                    }
                    else{
                        $links[]=array('name'=>'结算','params'=>array(0=>array('keyName'=>'order_id','keyValue'=>$value['order_id'])));
                    }
                   
                }
               $value['links']=$links;
               $data['data']['rows'][$key]=$value;
            }
           
        }
        
        $this->returnJson($data);
    }
    
    /**
     * @api {GET} / [90020001-getDeliveryOrderSettlement] 获取发货单结算对象
     * @apiName getDeliveryOrderSettlement
     * @apiParam (输入字段) {string} order_id 发货单id
     * @apiExample {json} 输入示例:
     * {
     *      "order_id":201804030487,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     * {
           "code": 0,
           "data": {
                "deliveryOrder": {
                    "order_id": "201804030487",
                    "code": "KY180403FH14",
                    "delivery_date": "2018-04-03",
                    "settle_date": null,
                    "type": "1",
                    "corporation_id": "2",
                    "project_id": null,
                    "contract_id": "780",
                    "partner_id": "10",
                    "stock_in_id": null,
                    "status_time": "2018-04-03 19:32:11",
                    "status": "20",
                    "remark": null,
                    "create_user_id": null,
                    "create_time": null,
                    "update_user_id": null,
                    "update_time": null,
                    "settle_remark": null,
                    "currency": "1",
                    "items": [
                        {
                            "detail_id": "334",
                            "goods_id": "2",
                            "goods_name": "\u67f4\u6cb9",
                            "quantity": {
                                "quantity": "500.0000",
                                "unit": "2"
                            },
                            "stock_in_id": null,
                            "stock_delivery_quantity": {
                                "quantity": "500.0000",
                                "unit": "2"
                            },
                            "store_name": "\u5e7f\u5dde\u65b0\u9020",
                            "out_quantity": {
                                "quantity": 500,
                                "unit": "2"
                            },
                            "no_out_quantity": {
                                "quantity": 0,
                                "unit": "2"
                            },
                            "remark": "33"
                        },
                        {
                            "detail_id": "335",
                            "goods_id": "1",
                            "goods_name": "\u6210\u54c1\u6cb9",
                            "quantity": {
                                "quantity": "500.0000",
                                "unit": "2"
                            },
                            "stock_in_id": null,
                            "stock_delivery_quantity": {
                                "quantity": "500.0000",
                                "unit": "2"
                            },
                            "store_name": "\u5e7f\u5dde\u65b0\u9020",
                            "out_quantity": {
                                "quantity": 500,
                                "unit": "2"
                            },
                            "no_out_quantity": {
                                "quantity": 0,
                                "unit": "2"
                            },
                            "remark": "44"
                        }
                    ],
                    "settleItems": null,
                    "files": [
                                  {
                                        "id": "1",
                                        "type": null,
                                        "name": "3206.48\u5428\u7ed3\u7b97\u51fd.jpg",
                                        "file_url": "\/data\/oil\/upload\/stock\/stockIn201712110001-201712110100\/201712110001\/201712110001_1_1512962325_ouiep2.jpg"
                                    }
                                ],
                    "contract_code": "KY99NQ180326S02",
                    "corporation_name": "\u5764\u6e90\u80fd\u6e90\uff08\u6e56\u5317\uff09\u6709\u9650\u516c\u53f8",
                    "partner_name": null,
                    "check_remark": null
                },
                "stockOut": [
                    {
                        "out_order_id": "201804030001",
                        "order_id": "201804030487",
                        "code": "KY180403FH14-1",
                        "out_date": "2018-04-03",
                        "store_id": "22",
                        "type": null,
                        "corporation_id": null,
                        "partner_id": "10",
                        "status_time": null,
                        "status": "1",
                        "remark": null,
                        "create_user_id": null,
                        "create_time": null,
                        "update_user_id": null,
                        "update_time": null,
                        "items": [
                            {
                                "goods_id": "2",
                                "goods_name": "\u67f4\u6cb9",
                                "quantity": {
                                    "quantity": "500.0000",
                                    "unit": "2"
                                },
                                "delivery_quantity": {
                                    "quantity": "500.0000",
                                    "unit": "2"
                                },
                                "stock_in_code": "KY99NQ180326N01-1-1",
                                "remark": "34"
                            },
                            {
                                "goods_id": "1",
                                "goods_name": "\u6210\u54c1\u6cb9",
                                "quantity": {
                                    "quantity": "500.0000",
                                    "unit": "2"
                                },
                                "delivery_quantity": {
                                    "quantity": "500.0000",
                                    "unit": "2"
                                },
                                "stock_in_code": "KY99NQ180326N01-1-1",
                                "remark": "45"
                            }
                        ],
                        "files": [
                                  {
                                        "id": "1",
                                        "type": null,
                                        "name": "3206.48\u5428\u7ed3\u7b97\u51fd.jpg",
                                        "file_url": "\/data\/oil\/upload\/stock\/stockIn201712110001-201712110100\/201712110001\/201712110001_1_1512962325_ouiep2.jpg"
                                    }
                                ],
                        "store_name": "\u5e7f\u5dde\u65b0\u9020"
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
                "deliveryOrderBalance": {
                    "order_id": "201804030487",
                    "settle_id": "13",
                    "order_code": "KY180403FH14",
                    "project_id": "20180326001",
                    "project_code": "ZKY99NQ18032601",
                    "contract_id": "780",
                    "contract_code": "KY99NQ180326S02",
                    "partner_id": "10",
                    "partner_name": null,
                    "corporation_id": "2",
                    "corporation_name": "\u5764\u6e90\u80fd\u6e90\uff08\u6e56\u5317\uff09\u6709\u9650\u516c\u53f8",
                    "settle_date": "2018-04-14",
                    "settle_status": "1",
                    "settle_currency": {
                        "id": 1,
                        "name": "\u4eba\u6c11\u5e01",
                        "ico": "\uffe5"
                    },
                    "goods_amount": "13998",
                    "settlementGoods": [
                        {
                            "batch_id": "201804030487",
                            "order_id": "201804030487",
                            "item_id": "20180410409",
                            "batch_code": null,
                            "delivery_code": "KY180403FH14",
                            "goods_id": "2",
                            "goods_name": "\u67f4\u6cb9",
                            "quantity": {
                                "quantity": "30.0000",
                                "unit": "2"
                            },
                            "quantity_sub": null,
                            "quantity_loss": {
                                "quantity": 470,
                                "unit": "2"
                            },
                            "quantity_loss_sub": null,
                            "price": "1999",
                            "amount": "69999",
                            "in_quantity": null,
                            "in_quantity_sub": null,
                            "out_quantity": {
                                "quantity": "500.0000",
                                "unit": "2"
                            },
                            "out_quantity_sub": null,
                            "price_cny": "1999",
                            "amount_cny": "6999",
                            "unit_rate": "1.000000",
                            "lading_items": [],
                            "order_items": [
                                {
                                    "batch_id": null,
                                    "order_id": "201804030487",
                                    "item_id": "20180410409",
                                    "batch_code": null,
                                    "delivery_code": "KY180403FH14",
                                    "goods_id": "2",
                                    "goods_name": "\u67f4\u6cb9",
                                    "quantity": {
                                        "quantity": "30.0000",
                                        "unit": "2"
                                    },
                                    "quantity_sub": null,
                                    "quantity_loss": {
                                        "quantity": 470,
                                        "unit": "2"
                                    },
                                    "quantity_loss_sub": null,
                                    "price": "1999",
                                    "amount": "69999",
                                    "in_quantity": null,
                                    "in_quantity_sub": null,
                                    "out_quantity": {
                                        "quantity": "500.0000",
                                        "unit": "2"
                                    },
                                    "out_quantity_sub": null,
                                    "price_cny": "1999",
                                    "amount_cny": "6999",
                                    "unit_rate": "1.000000",
                                   
                                }
                            ],
                            "hasDetail": true,
                            "settlementGoodsDetail": {
                                "currency": {
                                    "id": 2,
                                    "name": "\u7f8e\u5143",
                                    "ico": "$"
                                },
                                "currency_name": null,
                                "price_goods": "333",
                                "amount_currency": "888",
                                "exchange_rate": "1.000000",
                                "amount_goods": "4555",
                                "exchange_rate_tax": "333.000000",
                                "amount_goods_tax": "333",
                                "adjust_type": {
                                    "id": 1,
                                    "name": "\u8c03\u589e"
                                },
                                "amount_adjust": "66",
                                "reason_adjust": "\u539f\u56e0444",
                                "quantity": {
                                    "quantity": "30.0000",
                                    "unit": "2"
                                },
                                "quantity_actual": {
                                    "quantity": "30.0000",
                                    "unit": "2"
                                },
                                "amount": "888",
                                "amount_actual": "888",
                                "price": "555",
                                "price_actual": "555",
                                "remark": "\u5907\u6ce8ss",
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
                                            "id": 2,
                                            "name": "\u589e\u503c\u7a0e"
                                        },
                                        "rate": "1.000000",
                                        "price": "11",
                                        "amount": "66",
                                        "remark": "ddd"
                                    },
                                    {
                                        "subject_list": {
                                            "id": 3,
                                            "name": "\u5173\u7a0e"
                                        },
                                        "rate": "1.000000",
                                        "price": "110",
                                        "amount": "66",
                                        "remark": "ddd"
                                    }
                                ],
                                "other_detail_item": [
                                    {
                                        "subject_list": {
                                            "id": 2,
                                            "name": "\u6e2f\u5efa\u8d39"
                                        },
                                        "rate": null,
                                        "price": "66",
                                        "amount": "22",
                                        "remark": "ddd"
                                    },
                                    {
                                        "subject_list": {
                                            "id": 3,
                                            "name": "\u6e2f\u52a1\u8d39"
                                        },
                                        "rate": null,
                                        "price": "66",
                                        "amount": "220",
                                        "remark": "dddsss"
                                    }
                                ]
                            }
                        },
                        {
                            "batch_id": "201804030487",
                            "order_id": "201804030487",
                            "item_id": "20180410411",
                            "batch_code": null,
                            "delivery_code": "KY180403FH14",
                            "goods_id": "1",
                            "goods_name": "\u6210\u54c1\u6cb9",
                            "quantity": {
                                "quantity": "20.0000",
                                "unit": "2"
                            },
                            "quantity_sub": null,
                            "quantity_loss": {
                                "quantity": 480,
                                "unit": "2"
                            },
                            "quantity_loss_sub": null,
                            "price": "1999",
                            "amount": "69998",
                            "in_quantity": null,
                            "in_quantity_sub": null,
                            "out_quantity": {
                                "quantity": "500.0000",
                                "unit": "2"
                            },
                            "out_quantity_sub": null,
                            "price_cny": "1999",
                            "amount_cny": "6999",
                            "unit_rate": "1.000000",
                            "lading_items": [],
                            "order_items": [
                                {
                                    "batch_id": null,
                                    "order_id": "201804030487",
                                    "item_id": "20180410411",
                                    "batch_code": null,
                                    "delivery_code": "KY180403FH14",
                                    "goods_id": "1",
                                    "goods_name": "\u6210\u54c1\u6cb9",
                                    "quantity": {
                                        "quantity": "20.0000",
                                        "unit": "2"
                                    },
                                    "quantity_sub": null,
                                    "quantity_loss": {
                                        "quantity": 480,
                                        "unit": "2"
                                    },
                                    "quantity_loss_sub": null,
                                    "price": "1999",
                                    "amount": "69998",
                                    "in_quantity": null,
                                    "in_quantity_sub": null,
                                    "out_quantity": {
                                        "quantity": "500.0000",
                                        "unit": "2"
                                    },
                                    "out_quantity_sub": null,
                                    "price_cny": "1999",
                                    "amount_cny": "6999",
                                    "unit_rate": "1.000000",
                                    
                                }
                            ],
                            "hasDetail": true,
                            "settlementGoodsDetail": {
                                "currency": {
                                    "id": 2,
                                    "name": "\u7f8e\u5143",
                                    "ico": "$"
                                },
                                "currency_name": null,
                                "price_goods": "333",
                                "amount_currency": "888",
                                "exchange_rate": "1.000000",
                                "amount_goods": "4555",
                                "exchange_rate_tax": "444.000000",
                                "amount_goods_tax": "4445",
                                "adjust_type": {
                                    "id": 2,
                                    "name": "\u8c03\u51cf"
                                },
                                "amount_adjust": "777",
                                "reason_adjust": "\u539f\u56e0w",
                                "quantity": {
                                    "quantity": "20.0000",
                                    "unit": "2"
                                },
                                "quantity_actual": {
                                    "quantity": "20.0000",
                                    "unit": "2"
                                },
                                "amount": "999",
                                "amount_actual": "999",
                                "price": "555",
                                "price_actual": "555",
                                "remark": "\u5907\u6ce8",
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
                                            "id": 2,
                                            "name": "\u589e\u503c\u7a0e"
                                        },
                                        "rate": "1.000000",
                                        "price": "11",
                                        "amount": "66",
                                        "remark": "ddd"
                                    },
                                    {
                                        "subject_list": {
                                            "id": 3,
                                            "name": "\u5173\u7a0e"
                                        },
                                        "rate": "1.000000",
                                        "price": "110",
                                        "amount": "66",
                                        "remark": "ddd"
                                    }
                                ],
                                "other_detail_item": [
                                    {
                                        "subject_list": {
                                            "id": 2,
                                            "name": "\u6e2f\u5efa\u8d39"
                                        },
                                        "rate": null,
                                        "price": "66",
                                        "amount": "22",
                                        "remark": "ddd"
                                    },
                                    {
                                        "subject_list": {
                                            "id": 3,
                                            "name": "\u6e2f\u52a1\u8d39"
                                        },
                                        "rate": null,
                                        "price": "66",
                                        "amount": "220",
                                        "remark": "dddsss"
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
     * @apiGroup DeliveryOrderSettlement
     * @apiVersion 1.0.0
     */
    public function actionGetDeliveryOrderSettlement(){
        $order_id = Mod::app()->request->getParam('order_id');
        if (!Utility::checkQueryId($order_id)) {
            $this->returnJsonError(OilError::$PARAMS_PASS_ERROR);
        }
        //发货单
        $DeliveryOrderService = new \ddd\application\stock\DeliveryOrderService();
        $deliveryOrder = $DeliveryOrderService->getDeliveryOrder($order_id);
        if(empty($deliveryOrder))
            $this->returnJsonError(OilError::$DELIVERY_ORDER_NOT_EXIST,array('order_id'=>$order_id)); 
        $data['deliveryOrder']=$deliveryOrder;
        //出库单
        $StockOutService = new \ddd\application\stock\StockOutService();
        $stockOut = $StockOutService->getStockOutByOrderId($order_id);
        $data['stockOut']=$stockOut;
        //审核记录
        $checkLogs=FlowService::getCheckLog($order_id,10);
        $data['checkLogs']=$checkLogs;
        
        //发货单商品结算
        $DeliveryOrderSettlementService = new \ddd\application\contractSettlement\DeliveryOrderSettlementService();
        $deliveryOrderSettlement=$DeliveryOrderSettlementService->getDeliveryOrderSettlement($order_id);
        if(is_string($deliveryOrderSettlement)) $this->returnJsonError($deliveryOrderSettlement,'-1');//抛出异常
        $data['deliveryOrderBalance']=$deliveryOrderSettlement;
        //是否可结算
        $deliveryOrderSettlementEntity = \ddd\repository\contractSettlement\DeliveryOrderSettlementRepository::repository()->find('t.order_id='.$order_id);
        $isCanSubmit = 0;
        if(!empty($deliveryOrderSettlementEntity)&&$DeliveryOrderSettlementService->isCanSubmit($deliveryOrderSettlementEntity)){
            $isCanSubmit=1;
        }
        $data['isCanSubmit']=$isCanSubmit;
       
        $this->returnJson($data);
        
    }
    
    /**
     * @api {POST} / [90020001-save] 暂存、保存
     * @apiName save
     * @apiParam (输入字段) {int} order_id 发货单id
     * @apiParam (输入字段) {int} status 状态类型，1是暂存，2是保存
     * @apiParam (输入字段) {string} settle_date 结算日期
     * @apiParam (输入字段) {arr} goods_arr 商品结算信息
     * @apiExample {json} 输入示例:
       {
           "order_id":201804030487,
           "settle_date":"2018-04-01",
           "status":0,
           "goods_arr"=>[
                         0 =>{
                            'goods_id':1,
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
     * @apiGroup DeliveryOrderSettlement
     * @apiVersion 1.0.0
     */
   
    public function actionSave(){
        $order_id = Mod::app()->request->getParam('order_id');  //发货单id
        $settle_date = Mod::app()->request->getParam('settle_date'); //结算日期
        $settle_status = Mod::app()->request->getParam('status'); //状态  0是暂存，1是保存
        $goods_arr = Mod::app()->request->getParam('goods_arr'); // 商品结算信息
        $post=array(
            'settle_date'=>$settle_date,
            'settle_status'=>$settle_status,
            'goods_arr'=>$goods_arr
        );
        if (!Utility::checkQueryId($order_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        } 
        //发货单
        $DeliveryOrderService = new \ddd\application\stock\DeliveryOrderService();
        $deliveryOrder = $DeliveryOrderService->getDeliveryOrder($order_id);
        if(empty($deliveryOrder))
            $this->returnJsonError(OilError::$DELIVERY_ORDER_NOT_EXIST,array('order_id'=>$order_id)); 
       //获取DTO并赋值
        $deliveryOrderSettlementService = new \ddd\application\contractSettlement\DeliveryOrderSettlementService();
        $newDeliveryOrderSettlementDTO = $deliveryOrderSettlementService->AssignDTO($order_id,$post);
        if(is_string($newDeliveryOrderSettlementDTO))
            $this->returnJsonError($newDeliveryOrderSettlementDTO,'-1');
        //保存结算单
        $re = $deliveryOrderSettlementService->saveDeliveryOrderSettlement($newDeliveryOrderSettlementDTO);
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
        $order_id = Mod::app()->request->getParam('order_id');  //发货单id
        $settle_date = Mod::app()->request->getParam('settle_date'); //结算日期
        $settle_status = Mod::app()->request->getParam('status'); //状态  0是暂存，1是保存
        $goods_arr = Mod::app()->request->getParam('goods_arr'); // 商品结算信息
        $settle_date ="2018-04-14";
        $settle_status = "2";
        $goods_arr=array(
            0 =>array(
                'goods_id'=>1,
                'quantity'=>20,
                'quantity_loss'=>14,
                'price'=>1999,
                'amount'=>69998,
                'unit_rate'=>1,
                'price_cny'=>1999,
                'amount_cny'=>6999,
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
                        1=>array('subject_list'=>3,'price'=>220,'amount'=>66,'remark'=>'dddsss'),
                    ),
                    
                ),
            ),
            1 =>array(
                'goods_id'=>2,
                'quantity'=>30,
                'quantity_loss'=>1,
                'price'=>1999,
                'amount'=>69999,
                'unit_rate'=>1,
                'price_cny'=>1999,
                'amount_cny'=>6999,
                'settlementGoodsDetail'=>array(
                    'currency'=>2,
                    'amount_currency'=>888,
                    'exchange_rate'=>1,
                    'amount_goods'=>4555,
                    'price_goods'=>333,
                    'exchange_rate_tax'=>333,
                    'amount_goods_tax'=>333,
                    
                    'adjust_type'=>1,
                    'amount_adjust'=>66,
                    'reason_adjust'=>'原因444',
                    'quantity'=>11,
                    'quantity_actual'=>11,
                    'amount'=>888,
                    'amount_actual'=>888,
                    'price'=>555,
                    'price_actual'=>555,
                    'remark'=>'备注ss',
                    
                    'settleFile'=>[],
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
            ) 
            
        );
        $post=array(
            'settle_date'=>$settle_date,
            'settle_status'=>$settle_status,
            'goods_arr'=>$goods_arr
        );
        if (!Utility::checkQueryId($order_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        //发货单
        $DeliveryOrderService = new \ddd\application\stock\DeliveryOrderService();
        $deliveryOrder = $DeliveryOrderService->getDeliveryOrder($order_id);
        if(empty($deliveryOrder))
            $this->returnJsonError(OilError::$DELIVERY_ORDER_NOT_EXIST,array('order_id'=>$order_id));
        //获取DTO并赋值
        $deliveryOrderSettlementService = new \ddd\application\contractSettlement\DeliveryOrderSettlementService();
        $newDeliveryOrderSettlementDTO = $deliveryOrderSettlementService->AssignDTO($order_id,$post);
        if(is_string($newDeliveryOrderSettlementDTO))
            $this->returnJsonError($newDeliveryOrderSettlementDTO,'-1');
        //保存结算单
        $re = $deliveryOrderSettlementService->saveDeliveryOrderSettlement($newDeliveryOrderSettlementDTO);
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
     * @apiParam (输入字段) {string} order_id 发货单id
     * @apiExample {json} 输入示例:
     * {
     *      "order_id":201804030487,
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
     * @apiGroup DeliveryOrderSettlement
     * @apiVersion 1.0.0
     */
    public function actionSubmit(){
        $id = Mod::app()->request->getParam("order_id");
        if(!Utility::checkQueryId($id))
        $this->returnJsonError("参数有误");
        $deliveryOrder = DeliveryOrder::model()->findByPk($id);
        if(empty($deliveryOrder->order_id))
            $this->returnJsonError("当前信息不存在");
        
        $oldStatus = $deliveryOrder->status;
            
        //是否可提交
        $deliveryOrderSettlementEntity = \ddd\repository\contractSettlement\DeliveryOrderSettlementRepository::repository()->find('t.order_id='.$id);
        $DeliveryOrderSettlementService = new \ddd\application\contractSettlement\DeliveryOrderSettlementService();
        $isCanSubmit = $DeliveryOrderSettlementService->isCanSubmit($deliveryOrderSettlementEntity);
        if(!$isCanSubmit) {
            $this->returnJsonError(BusinessError::outputError(OilError::$DELIVERY_ORDER_SETTLE_NOT_ALLOW_SUBMIT));
        } 
            
        $trans = Utility::beginTransaction();
        try{
            $DeliveryOrderSettlementService->submit($deliveryOrderSettlementEntity);
            //先更新任务，不然会有bug
            TaskService::doneTask($id, Action::ACTION_47);
            FlowService::startFlowForCheck10($id);
            
            $trans->commit();
            
            Utility::addActionLog(json_encode(array('oldStatus'=>$oldStatus)), "提交发货单结算", "DeliveryOrder", $deliveryOrder->order_id);
            $this->returnJson($deliveryOrder->order_id);
            
        }catch(Exception $e){
            try{ $trans->rollback(); }catch(Exception $ee){}
            $this->returnJsonError(BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $e->getMessage())));
        }
    }
    /**
     * @api {GET} / [90020001-saveFile] 货款结算附件上传
     * @apiName saveFile
     * @apiParam (输入字段) {int} id 标志id
     * @apiParam (输入字段) {int} type 类型，3是单据附件，4是其他附件
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
     * @apiGroup DeliveryOrderSettlement
     * @apiVersion 1.0.0
     */
    public function actionSaveFile(){
        parent::actionSaveFile();
    }
   
   
    
    
    

}