<?php

/**
 * Desc: 历史数据导入服务
 * User: susiehuang
 * Date: 2017/12/5 0009
 * Time: 15:03
 */
class ImportService {
    /**
     * @desc 导入历史合同数据
     */
    public static function importHistoryContracts() {
        $fields = 'c.id, c.item_id, c.contract_id, c.type, c.contract_date, c.provision_price, c.quantity, c.invoiced_tonnage, c.status, cb.corp_id, cp.partner_id, bt.project_type, o.user_id, g.goods_id';
        $sql1 = 'select aa.* from (select id, item_id, contract_id, broker_id, biz_type, operator_id, prd_id, comp_id, contract_date, purchase_quantity as quantity, provision_price, invoiced_tonnage, status, 1 as type from t_contract_purchase 
                 union all 
                 select id, item_id, contract_id, broker_id, biz_type, operator_id, prd_id, comp_id, contract_date, sales_quantity as quantity, provision_price, invoiced_tonnage, status, 2 as type from t_contract_sales) aa group by aa.contract_id';

        $sql = 'select  ' . $fields . ' from (' . $sql1 . ') c  
                left join t_company_broker cb on cb.id=c.broker_id 
                left join t_biztype bt on bt.type_id=c.biz_type 
                left join t_operator o on o.id=c.operator_id 
                left join t_prd g on c.prd_id = g.prd_id 
                left join t_company cp on cp.id=c.comp_id where c.contract_id <> "" order by c.contract_id asc';

        $resource = Utility::query($sql, Utility::DB_HISTORY);

        if (Utility::isNotEmpty($resource)) {
            foreach ($resource as $row) {
                $project = Project::model()->findByCode($row['item_id']);
                $contract = Contract::model()->findByCode($row['contract_id']);
                if (empty($project)) {
                    continue;
                }
                //合同
                $amount = $row['quantity'] * $row['provision_price'] * 100;
                if (empty($contract)) {
                    $contract = new Contract();
                    $contract->project_id = $project->project_id;
                    $contract->partner_id = $row['partner_id'];
                    $contract->type = $row['type'];
                    $contract->category = Map::$v['project_business_type'][$row['project_type']]['default_contract_category'][$row['type']];
                    $contract->is_main = 0;
                    $contract->num = ContractService::getSubContractNum($project->project_id, $row['type']);
                    $contract->contract_code = $row['contract_id'];
                    $contract->corporation_id = $row['corp_id'];
                    $contract->currency = 1;
                    $contract->exchange_rate = 1;
                    $contract->price_type = 1;
                    $contract->manager_user_id = $row['user_id'];
                    $contract->contract_date = $row['contract_date'];
                    $contract->remark = '历史数据系统导入';
                    $contract->status = $row['status'] == 1 ? Contract::STATUS_FILE_FILED : Contract::STATUS_BUSINESS_CHECKED;
                    $contract->status_time = Utility::getDateTime();
                    $contract->create_user_id = - 1;
                    $contract->create_time = Utility::getDateTime();
                    $contract->update_user_id = - 1;
                    $contract->update_time = Utility::getDateTime();
                }
                $contract->amount_cny = (!empty($contract->amount_cny) ? $contract->amount_cny : 0) + $amount;
                $contract->amount = (!empty($contract->amount) ? $contract->amount : 0) + $amount;
                $res = $contract->save();
                if ($res !== true) {
                    Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' 合同类型:' . $row['type'] . ', id:' . $row['id'] . ', contract save error:' . $res, CLogger::LEVEL_ERROR, 'oil.import.log');
                }

                //交易明细
                $contractGoods = ContractGoods::model()->find('project_id = :projectId and contract_id = :contractId and type = :type and goods_id = :goods_id', array('projectId' => $contract->project_id, 'contractId' => $contract->contract_id, 'type' => $row['type'], 'goods_id' => $row['goods_id']));
                if (empty($contractGoods)) {
                    $contractGoods = new ContractGoods();
                    $contractGoods->contract_id = $contract->contract_id;
                    $contractGoods->project_id = $contract->project_id;
                    $contractGoods->type = $row['type'];
                    $contractGoods->goods_id = $row['goods_id'];
                    $contractGoods->price = $row['provision_price'] * 100;
                    $contractGoods->quantity = $row['quantity'];
                    $contractGoods->quantity_actual = $row['invoiced_tonnage'];
                    $contractGoods->unit = 2;
                    $contractGoods->amount_cny = $amount;
                    $contractGoods->amount = $amount;
                    $contractGoods->currency = 1;
                    $contractGoods->unit_price = 2;
                    $contractGoods->unit_store = 2;
                    $contractGoods->remark = '历史数据系统导入';
                    $contractGoods->create_user_id = - 1;
                    $contractGoods->create_time = Utility::getDateTime();
                    $contractGoods->update_user_id = - 1;
                    $contractGoods->update_time = Utility::getDateTime();
                    $result = $contractGoods->save();

                    if ($result !== true) {
                        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' contract type:' . $row['type'] . ', contract_id:' . $contract->contract_id . ', project_id:' . $contract->project_id . ', goods_id:' . $row['goods_id'] . ' contractGoods save error:' . $result, CLogger::LEVEL_ERROR, 'oil.import.log');
                    }
                }
            }
        }

        ImportService::updateHistoryContracts();
        ImportService::importHistoryContractGroups();
        ImportService::generateContractFile();
    }

    /**
     * @desc 更新历史合同信息
     */
    public static function updateHistoryContracts() {
        $contracts = Contract::model()->findAll('create_user_id = -1');
        if (Utility::isNotEmpty($contracts)) {
            foreach ($contracts as $row) {
                if (in_array($row->project->type, ConstantMap::$self_support_project_type)) { //自营-单边
                    continue;
                } else {
                    //查找关联合同
                    $relatedContrcts = Contract::model()->findAll('project_id=:projectId and type<>:type and contract_id<>:contractId', array('projectId' => $row->project_id, 'type' => $row->type, 'contractId' => $row->contract_id));
                    if (is_array($relatedContrcts) && count($relatedContrcts) == 1) { //该合同只有一个关联合同-双边合同
                        $res = $row->updateByPk($row->contract_id, array("is_main" => 1));
                        if ($res != 1) {
                            Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' contract_id:' . $row->contract_id . ', project_id:' . $row->project_id . ', update contract is_main error:' . $res, CLogger::LEVEL_ERROR, 'oil.import.log');
                        }
                    }
                }
            }
        }
    }

    /**
     * @desc 导入历史合同组
     */
    public static function importHistoryContractGroups() {
        $contracts = Contract::model()->findAll('create_user_id = -1');
        if (Utility::isNotEmpty($contracts)) {
            foreach ($contracts as $contract) {
                $contract->findRelative();
                $group = ContractGroup::model()->findByContractId($contract->contract_id);
                if (empty($group) && $contract->is_main) {
                    $group = ContractGroup::model()->findMainByProjectId($contract->project_id);
                }
                if (empty($group)) {
                    $group = new ContractGroup();
                    $group->project_id = $contract->project_id;
                    $group->corporation_id = $contract->corporation_id;
                    $group->is_main = $contract->is_main;
                    $group->type = $contract->type;
                    $group->remark = '历史数据系统导入';
                    $group->create_user_id = - 1;
                    $group->create_time = Utility::getDateTime();
                    $group->update_user_id = - 1;
                }
                $group->update_time = Utility::getDateTime();
                if (empty($group->contract_id)) {
                    $group->contract_id = $contract->contract_id;
                }
                if ($contract->type == ConstantMap::BUY_TYPE) {
                    $group->up_partner_id = $contract->partner_id;
                    $group->down_contract_id = 0;
                    $group->down_partner_id = 0;

                    if ($contract->is_main == 1 && !empty($contract->relative)) {
                        $group->down_contract_id = $contract->relative->contract_id;
                        $group->down_partner_id = $contract->relative->partner_id;
                    }
                } else {
                    $group->down_partner_id = $contract->partner_id;
                }

                $r = $group->save();
                if ($r !== true) {
                    Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' contract_id:' . $contract->contract_id . ', project_id:' . $contract->project_id . ' contractGroup save error:' . $r, CLogger::LEVEL_ERROR, 'oil.import.log');
                }
            }
        }
    }

    /**
     * @desc 历史合作方数据导入
     */
    public static function importHistoryPartners() {
        $sql = 'select id, comp_name, is_up, is_down from t_company';
        $resource = Utility::query($sql, Utility::DB_HISTORY);
        if (Utility::isNotEmpty($resource)) {
            foreach ($resource as $row) {
                $type = '';
                if ($row['is_up']) {
                    $type .= '1';
                }
                if ($row['is_down']) {
                    $type .= empty($type) ? '2' : ',2';
                }
                $partner = Partner::model()->find('name = "' . trim($row['comp_name']) . '"');
                if (empty($partner)) {
                    $partnerApply = PartnerApply::model()->find('name = "' . trim($row['comp_name']) . '"');
                    if (empty($partnerApply)) {
                        $partnerApply = new PartnerApply();
                        $partnerApply->name = trim($row['comp_name']);
                        $partnerApply->type = $type;
                        $partnerApply->remark = '历史数据系统导入';
                        $partnerApply->status = Partner::STATUS_PASS;
                        $partnerApply->status_time = Utility::getDateTime();
                        $partnerApply->create_user_id = - 1;
                        $partnerApply->create_time = Utility::getDateTime();
                        $partnerApply->update_user_id = - 1;
                        $partnerApply->update_time = Utility::getDateTime();
                        $res = $partnerApply->save();
                        if ($res !== true) {
                            Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' id:' . $row['id'] . ' company save to PartnerApply error:' . $res, CLogger::LEVEL_ERROR, 'oil.import.log');
                        }
                    } else {
                        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' 合作方：' . $row['comp_name'] . ' 未准入完成！', CLogger::LEVEL_ERROR, 'oil.import.log');
                        continue;
                    }

                    $partner = new Partner();
                    $partner->partner_id = $partnerApply->partner_id;
                    $partner->name = trim($row['comp_name']);
                    $partner->type = $type;
                    $partner->remark = '历史数据系统导入';
                    $partner->status = Partner::STATUS_PASS;
                    $partner->status_time = Utility::getDateTime();
                    $partner->create_user_id = - 1;
                    $partner->create_time = Utility::getDateTime();
                    $partner->update_user_id = - 1;
                    $partner->update_time = Utility::getDateTime();
                    $result = $partner->save();
                    if ($result !== true) {
                        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' id:' . $row['id'] . ' company save to Partner error:' . $result, CLogger::LEVEL_ERROR, 'oil.import.log');
                    }
                }
                $sql1 = 'update t_company set partner_id = ' . $partner->partner_id . ' where id=' . $row['id'];
                $res = Utility::execute($sql1, Utility::DB_HISTORY);
                if ($res === - 1) {
                    Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' id:' . $row['id'] . ', partner_id:' . $partner->partner_id . ' company update error:' . $res, CLogger::LEVEL_ERROR, 'oil.import.log');
                }
            }
        }
    }

    /**
     * @desc 导入历史业务员id
     */
    public static function importHistoryManagerIds() {
        $sql = 'select id,name from t_operator';
        $resource = Utility::query($sql, Utility::DB_HISTORY);
        if (Utility::isNotEmpty($resource)) {
            foreach ($resource as $row) {
                $user = SystemUser::model()->find('user_name = :userName or name = :name', array('userName' => $row['name'], 'name' => $row['name']));
                if (empty($user)) {
                    $user = new SystemUser();
                    $user->user_name = $row['name'];
                    $user->name = $row['name'];
                    $user->password = Utility::getSecretPassword(md5('123456'));
                    $user->remark = '历史数据系统导入';
                    $user->role_ids = '3';
                    $user->create_user_id = - 1;
                    $user->create_time = Utility::getDateTime();
                    $user->update_user_id = - 1;
                    $user->update_time = Utility::getDateTime();
                    $res = $user->save();
                    if ($res !== 1) {
                        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' id:' . $row['id'] . ' operator save to SystemUser error:' . $res, CLogger::LEVEL_ERROR, 'oil.import.log');
                    }
                }
                $sql1 = 'update t_operator set user_id = ' . $user->user_id . ' where id = ' . $row['id'];
                $res1 = Utility::execute($sql1, Utility::DB_HISTORY);
                if ($res1 === - 1) {
                    Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' id:' . $row['id'] . ', user_id:' . $user->user_id . ' operator update error:' . $res1, CLogger::LEVEL_ERROR, 'oil.import.log');
                }
            }
        }
    }

    /**
     * @desc 导入历史合同数据
     */
    public static function importHistoryProject() {
        $sql = "select a.*,b.corp_id,bt.project_type,o.user_id from (
                select aa.* from (
                select item_id,broker_id,operator_id,biz_type from t_contract_purchase 
                union all
                select item_id,broker_id,operator_id,biz_type from t_contract_sales 
                ) aa group by broker_id,item_id
                ) a left join t_company_broker b on a.broker_id=b.id
                left join t_biztype bt on bt.type_id=a.biz_type 
                left join t_operator o on o.id=a.operator_id 
                ";
        $data = Utility::query($sql, Utility::DB_HISTORY);

        if (Utility::isNotEmpty($data)) {
            $projectIds = array();
            foreach ($data as $v) {
                $p = Project::model()->findByCode($v["item_id"]);
                if (!empty($p)) {
                    array_push($projectIds, $p->project_id);

                    continue;
                }
                $p = new Project();
                $p->project_code = $v["item_id"];
                $p->corporation_id = $v["corp_id"];
                $p->manager_user_id = $v['user_id'];
                $p->type = $v['project_type'];
                $p->is_can_back = 0;
                $p->status = Project::STATUS_SUBMIT;
                $p->status_time = new CDbExpression("now()");
                $p->remark = "历史数据系统导入";
                $p->create_user_id = - 1;
                $p->create_time = Utility::getDateTime();
                $p->update_user_id = - 1;
                $p->update_time = Utility::getDateTime();
                $res = $p->save();
                if ($res !== true) {
                    Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' item_id:' . $v["item_id"] . ' contract save to Project error:' . $res, CLogger::LEVEL_ERROR, 'oil.import.log');
                    continue;
                }

                array_push($projectIds, $p->project_id);
            }
            if (Utility::isNotEmpty($projectIds)) {
                foreach ($projectIds as $val) {
                    $project = Project::model()->findByPk($val);
                    if (empty($project)) {
                        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' project is not exist:' . $val, CLogger::LEVEL_ERROR, 'oil.import.log');

                        continue;
                    }

                    $sqlbuy = 'select a.*, b.partner_id from t_contract_purchase a 
                               left join t_company b on a.comp_id = b.id 
                               where a.item_id="' . $project->project_code . '" order by a.contract_date asc limit 1';
                    $buyContract = Utility::query($sqlbuy, Utility::DB_HISTORY);

                    $sqlsale = 'select a.*, b.partner_id from t_contract_sales a 
                                left join t_company b on a.comp_id = b.id 
                                where a.item_id="' . $project->project_code . '" order by a.contract_date asc limit 1';
                    $saleContract = Utility::query($sqlsale, Utility::DB_HISTORY);

                    $buySaleType = 0;
                    $up_partner_id = 0;
                    $down_partner_id = 0;
                    if (in_array($project->type, ConstantMap::$self_support_project_type)) { //单边业务
                        if (Utility::isNotEmpty($buyContract)) {
                            $up_partner_id = $buyContract[0]['partner_id'];
                            $buySaleType = ConstantMap::FIRST_BUY_LAST_SALE;
                        } else {
                            if (Utility::isNotEmpty($saleContract)) {
                                $down_partner_id = $saleContract[0]['partner_id'];
                                $buySaleType = ConstantMap::FIRST_SALE_LAST_BUY;
                            }
                        }
                    } else {
                        if (Utility::isEmpty($buyContract) || Utility::isEmpty($saleContract)) {
                            Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' 渠道业务, 项目编号:' . $project->project_code . ',缺少采购或销售合同！', CLogger::LEVEL_ERROR, 'oil.import.log');
                            //continue;
                        }

                        $up_partner_id = !empty($buyContract[0]['partner_id']) ? $buyContract[0]['partner_id'] : 0;
                        $down_partner_id = !empty($saleContract[0]['partner_id']) ? $saleContract[0]['partner_id'] : 0;
                    }

                    $projectBase = ProjectBase::model()->find('project_id=' . $val);
                    if (empty($projectBase)) {
                        $projectBase = new ProjectBase();
                        $projectBase->project_id = $val;
                        $projectBase->create_user_id = - 1;
                        $projectBase->create_time = Utility::getDateTime();
                        $projectBase->update_user_id = - 1;
                        $projectBase->update_time = Utility::getDateTime();
                    }
                    $projectBase->up_partner_id = $up_partner_id;
                    $projectBase->down_partner_id = $down_partner_id;
                    $projectBase->corporation_id = $project->corporation_id;
                    $projectBase->buy_sell_type = $buySaleType;
                    $projectBase->manager_user_id = $project->manager_user_id;
                    $projectBase->price_type = !empty($buyContract[0]['settle_price']) || !empty($saleContract[0]['settle_price']) ? 2 : 1;
                    $projectBase->purchase_currency = 1;
                    $projectBase->sell_currency = 1;
                    $projectBase->status = 0;
                    $projectBase->remark = '历史数据系统导入';
                    $projectBase->update_user_id = - 1;
                    $projectBase->update_time = Utility::getDateTime();
                    $ret = $projectBase->save();
                    if ($ret !== true) {
                        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' item_id:' . $v["item_id"] . ' project base save error:' . $ret, CLogger::LEVEL_ERROR, 'oil.import.log');
                    }
                }
            }
        }
    }

    /**
     * @desc 导入历史仓库
     */
    public static function importHistoryStorehouses() {
        $sql = 'select sid,storehouse from t_storehouse';
        $resource = Utility::query($sql, Utility::DB_HISTORY);
        if (Utility::isNotEmpty($resource)) {
            foreach ($resource as $row) {
                $store = Storehouse::model()->find('name = :name', array('name' => $row['storehouse']));
                if (empty($store)) {
                    $store = new Storehouse();
                    $store->name = $row['storehouse'];
                    $store->remark = '历史数据系统导入';
                    $store->status = Storehouse::STATUS_PASS;
                    $store->create_user_id = - 1;
                    $store->create_time = Utility::getDateTime();
                    $store->update_user_id = - 1;
                    $store->update_time = Utility::getDateTime();
                    $res = $store->save();
                    if ($res !== true) {
                        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' sid:' . $row['sid'] . ' save to Storehouse error:' . $res, CLogger::LEVEL_ERROR, 'oil.import.log');
                    }
                }
                $sql1 = 'update t_storehouse set store_id = ' . $store->store_id . ' where sid = ' . $row['sid'];
                $res1 = Utility::execute($sql1, Utility::DB_HISTORY);
                if ($res1 === - 1) {
                    Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' sid:' . $row['sid'] . ', store_id:' . $store->store_id . ' Storehouse update error:' . $res1, CLogger::LEVEL_ERROR, 'oil.import.log');
                }
            }
        }
    }

    /**
     * @desc 导入历史出库信息
     */
    public static function importHistoryStockOuts() {
        $sql = 'select o.id, o.contract_id, o.settle_quantity, o.remove_date, o.flowout_quantity, 
                c.settle_price, c.status, sh.store_id, g.goods_id  
                from t_storage_flow_out o 
                left join t_contract_sales c on c.contract_id = o.contract_id 
                left join t_prd g on g.prd_id = c.prd_id 
                left join t_storehouse sh on sh.sid = o.storehouse_id 
                where o.flowout_quantity > 0';
        $resource = Utility::query($sql, Utility::DB_HISTORY);
        if (Utility::isNotEmpty($resource)) {
            foreach ($resource as $key => $row) {
                $isSettled = 0;
                if (!empty($row['settle_quantity']) && !empty($row['settle_price'])) {
                    $isSettled = 1;
                }
                $contract = Contract::model()->findByCode($row['contract_id']);
                if (!empty($contract)) {
                    $stocks = self::getStockInfo($contract->project_id, $row['goods_id'], $row['flowout_quantity'], $row['id'], $row['contract_id']);
                    if (Utility::isEmpty($stocks)) {

                        continue;
                    }

                    $type = ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE;
                    $deliveryOrder = array();
                    $deliveryOrder['order_id'] = 0;
                    $deliveryOrder['corporation_id'] = $contract->corporation_id;
                    $deliveryOrder['delivery_date'] = $row['remove_date'];
                    $deliveryOrder['partner_id'] = $contract->partner_id;
                    $deliveryOrder['stock_in_id'] = 0;
                    $deliveryOrder['type'] = $type;
                    $codeInfo = CodeService::getDeliveryOrderCode($contract->corporation_id, $row['remove_date']);
                    if ($codeInfo['code'] == ConstantMap::INVALID) {
                        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' id:' . $row['id'] . ' 生成发货单编码出错 error:' . $codeInfo['msg'], CLogger::LEVEL_ERROR, 'oil.import.log');
                        continue;
                    }
                    $deliveryOrder['code'] = $codeInfo['data'];
                    $deliveryOrder['status'] = $isSettled ? DeliveryOrder::STATUS_SETTLE_PASS : DeliveryOrder::STATUS_PASS;
                    $deliveryOrder['status_time'] = Utility::getDateTime($row['remove_date']);
                    $deliveryOrder['remark'] = '历史数据系统导入';
                    $deliveryOrder['create_user_id'] = - 1;
                    $deliveryOrder['create_time'] = Utility::getDateTime();
                    $deliveryOrder['update_user_id'] = - 1 * $row['id'];
                    $deliveryOrder['update_time'] = Utility::getDateTime();
                    if ($deliveryOrder['status'] == DeliveryOrder::STATUS_SETTLE_PASS) {
                        $deliveryOrder['settle_date'] = $deliveryOrder['delivery_date'];
                    }

                    $orderDetail = array();
                    $orderDetail['detail_id'] = 0;
                    $orderDetail['project_id'] = $contract->project_id;
                    $orderDetail['contract_id'] = $contract->contract_id;
                    $orderDetail['goods_id'] = $row['goods_id'];
                    $orderDetail['quantity'] = $row['flowout_quantity'];
                    $orderDetail['type'] = $type;
                    $orderDetail['remark'] = '历史数据系统导入';
                    $orderDetail['create_user_id'] = - 1;
                    $orderDetail['create_time'] = Utility::getDateTime();
                    $orderDetail['update_user_id'] = - 1 * $row['id'];
                    $orderDetail['update_time'] = Utility::getDateTime();

                    /*if ($type == ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER) {
                        if (count($stocks) != 1) {
                            Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' 出库id:' . $row['id'] . '，销售合同：' . $row['contract_id'] . '直调出库信息对应入库数量不为1', CLogger::LEVEL_ERROR, 'oil.import.log');

                            continue;
                        }
                        $deliveryOrder['stock_in_id'] = $stocks[0]->stock_in_id;
                    }*/

                    $balanceFlowoutQuantity = $row['flowout_quantity'];
                    foreach ($stocks as $index => $value) {
                        if ($balanceFlowoutQuantity > 0) {
                            $deliveryDetail = array();
                            $deliveryDetail['stock_detail_id'] = 0;
                            $deliveryDetail['project_id'] = $contract->project_id;
                            $deliveryDetail['contract_id'] = $contract->contract_id;
                            $deliveryDetail['goods_id'] = $row['goods_id'];
                            $deliveryDetail['stock_id'] = $value->stock_id;
                            $deliveryDetail['quantity'] = $value['quantity_balance'] >= $balanceFlowoutQuantity ? $balanceFlowoutQuantity : $value['quantity_balance'];
                            $deliveryDetail['store_id'] = $row['store_id'];
                            $deliveryDetail['type'] = ConstantMap::DISTRIBUTED_NORMAL;
                            $deliveryDetail['remark'] = '历史数据系统导入';
                            $deliveryDetail['create_user_id'] = - 1;
                            $deliveryDetail['create_time'] = Utility::getDateTime();
                            $deliveryDetail['update_user_id'] = - 1 * $row['id'];
                            $deliveryDetail['update_time'] = Utility::getDateTime();

                            $balanceFlowoutQuantity = $value['quantity_balance'] >= $balanceFlowoutQuantity ? 0 : $balanceFlowoutQuantity - $value['quantity_balance'];
                            $orderDetail['stock_delivery_detail'][] = $deliveryDetail;
                        }
                    }

                    $deliveryOrder['deliveryOrderDetail'][] = $orderDetail;

                    //保存发货相关信息 & 出库相关信息 & 发货单结算相关信息
                    $res = DeliveryOrderService::saveHistoryStockOutInfo($deliveryOrder, $row);
                    if ($res !== true) {
                        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' id:' . $row['id'] . ' save delivery order & stock out & delivery settlement error:' . $res, CLogger::LEVEL_ERROR, 'oil.import.log');
                    }
                } else {
                    Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' id:' . $row['id'] . ' 合同不存在, 合同编号:' . $row['contract_id'], CLogger::LEVEL_ERROR, 'oil.import.log');
                    continue;
                }
            }
        }
    }

    /**
     * @desc 获取销售合同对应项目的库存信息
     * @param $projectId int
     * @param $goodsId int
     * @param $outQuantity float
     * @param $flowOutId int
     * @param $contractCode string
     * @return array
     */
    private static function getStockInfo($projectId, $goodsId, $outQuantity, $flowOutId, $contractCode) {
        $res = array();
        if (Utility::checkQueryId($projectId) && Utility::checkQueryId($goodsId) && Utility::checkQueryId($flowOutId)) {
            $project = Project::model()->findByPk($projectId);
            if (empty($project)) {
                Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' 项目:' . $projectId . '不存在', CLogger::LEVEL_ERROR, 'oil.import.log');

                return $res;
            }
            $goods = Goods::model()->findByPk($goodsId);
            if (empty($goods)) {
                Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' 品名:' . $goodsId . '不存在', CLogger::LEVEL_ERROR, 'oil.import.log');

                return $res;
            }

            if (bccomp($outQuantity, 0, 6) != 1) {
                Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' 出库id:' . $flowOutId . ',销售合同:' . $contractCode . ',出库数量不大于0！', CLogger::LEVEL_ERROR, 'oil.import.log');

                return $res;
            }

            $stocks = Stock::model()->findAll('t.project_id=' . $projectId . ' and t.goods_id=' . $goodsId . ' and t.quantity_balance>0');
            if (Utility::isEmpty($stocks)) {
                Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' 项目:' . $project->project_code . ',销售合同:' . $contractCode . ',无入库信息，无法进行出库！', CLogger::LEVEL_ERROR, 'oil.import.log');

                return $res;
            }

            $totalFlowInQuantity = 0;
            foreach ($stocks as $key => $row) {
                $totalFlowInQuantity += $row->quantity_balance;
            }

            if (bccomp($totalFlowInQuantity, $outQuantity, 6) == - 1) {
                Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' 出库id:' . $flowOutId . ',项目:' . $project->project_code . ',销售合同:' . $contractCode . ',品名:' . $goods->name . ',的出库数量:' . $outQuantity . '大于该项目总的剩余可用入库数量:' . $totalFlowInQuantity, CLogger::LEVEL_ERROR, 'oil.import.log');

                return $res;
            }

            return $stocks;
        }

        return $res;
    }

    public static function rollback() {
        try {
            $sql = 'delete from t_system_user where create_user_id=-1;
                    delete from t_partner_apply where create_user_id=-1;
                    delete from t_partner where create_user_id=-1;
                    delete from t_storehouse where create_user_id=-1;
                    delete from t_project where create_user_id=-1;
                    delete from t_contract where create_user_id=-1;
                    delete from t_contract_goods where create_user_id=-1;
                    delete from t_contract_group where create_user_id=-1;
                    delete from t_delivery_order where create_user_id=-1;
                    delete from t_delivery_order_detail where create_user_id=-1;
                    delete from t_stock_delivery_detail where create_user_id=-1;
                    delete from t_delivery_settlement where create_user_id=-1;
                    delete from t_stock_out_order where create_user_id=-1;
                    delete from t_stock_out_detail where create_user_id=-1;
                    delete from t_stock where create_user_id=-1;
                    delete from t_stock_log where create_user_id=-1;';
            Utility::executeSql($sql);
        } catch (Exception $e) {
            Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' import rollback error:' . $e->getMessage(), CLogger::LEVEL_ERROR, 'oil.import.log');
        }
    }

    /**
     * @desc 生成项目主合同文本
     */
    public static function generateContractFile() {
        $resource = Contract::model()->findAll('create_user_id=-1');
        if(Utility::isNotEmpty($resource)) {
            foreach ($resource as $row) {
                if(!empty($row->contract_id)) {
                    ContractFileService::insertMainContractFile($row->contract_id, -1, -1, '历史数据系统导入');
                }
            }
        }
    }
}