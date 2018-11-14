<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/8 10:46
 * Describe：
 */
class ContractService {
    /**
     * 更新合同状态
     * @param $contractId
     * @param $status
     * @param null $oldStatus
     * @return int|string
     */
    public static function updateContractStatus($contractId, $status, $oldStatus = null) {
        $obj = Contract::model()->findByPk($contractId);
        if (empty($obj->contract_id)) {
            return "当前项目不存在！";
        }
        if ($oldStatus !== null && $obj->status != $oldStatus) {
            return "当前项目原状态与条件状态不一致！";
        }
        if ($obj->status != $status) {
            $obj->old_status = $obj->status;
            $obj->status = $status;
            $obj->status_time = date("Y-m-d H:i:s");
            $obj->update_user_id = Utility::getNowUserId();
            $obj->update_time = date("Y-m-d H:i:s");
            $res = $obj->save();
            if ($res === true) {
                return 1;
            } else {
                return $res;
            }
        } else {
            return 1;
        }
    }

    /**
     * 获取要发送的合同附件
     */
    /*public static function getSendContractAttachment($contractAttachments) {
        $attachArr = array();
        foreach ($contractAttachments as $key => $value) {
            $info = '';
            $suffix = basename($value['file_path']);
            $extension = strtolower(pathinfo($suffix, PATHINFO_EXTENSION));
            $prefix = strstr($suffix, '_', true);
            $prePath = substr($value['file_path'], 0, strrpos($value['file_path'], '/'));
            //$type       = substr($suffix,strpos($suffix,'_')+1,3);
            $type = $key;
            if ($checkArr['check1'] == 'on' && ($key == 101 || $key == 301)) {
                $info = '(定制化)';
            } else {
                if ($checkArr['check2'] == 'on' && ($key == 102 || $key == 302)) {
                    $info = '(定制化)';
                }
            }
            $mapValue = quotemeta(substr($value['name'], 0, strrpos($value['name'], '.')));
            $destFile = $prePath . '/' . $prefix . '_' . $mapValue . $info . '.' . $extension;
            copy($value['file_path'], $destFile);
            $attachArr[] = $destFile;
        }

        return $attachArr;
    }*/

    //获取当前要发送的合同
    public static function getContractAttachment($fileObj) {
        $map       = Map::$v;
        $title     = $fileObj->contract->contract_code . "-" . $map['contract_file_categories'][$fileObj->contract->type][$fileObj->category]['name'];
        $pathArr   = pathinfo($fileObj->file_path); 
        $dirname   = $pathArr['dirname'];
        $extension = strtolower($pathArr['extension']);
        $fileName  = quotemeta(basename($fileObj->name,".".$pathArr['extension']));
        $destFile  = $dirname . '/' . $title . '-' . trim($fileName) . "." . $extension;
        
        $res = copy($fileObj->file_path, $destFile);
        if($res)
            return $destFile;
        else
            return $fileObj->file_path;
    }


    /**
     * @desc 参数校验
     * @param array $params
     * @return bool|string
     */
    public static function checkParamsValid($params) {
        if (Utility::isNotEmpty($params)) {
            $requiredParams = array('type', 'corporation_id', 'price_type', 'exchange_rate', 'currency', 'manager_user_id', 'partner_id');
            if ($params['type'] == ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT && !empty($params['agent_id'])) {
                array_push($requiredParams, 'agent_type');
            }
            if (!empty($params['price_type']) && $params['price_type'] == ConstantMap::PRICE_TYPE_TEMPORARY) {
                array_push($requiredParams, 'formula');
            }
            //必填参数校验
            if (Utility::checkRequiredParamsNoFilterInject($params, $requiredParams)) {
                return true;
            }
        }

        return BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR);
    }


    /**
     * @desc 合同参数校验
     * @param array $params
     * @param array $type
     * @return bool|string
     */
    public static function checkBuyOrSellParamsValid($params, $type) {
        if (Utility::isNotEmpty($params)) {
            if ($type == ConstantMap::BUY_TYPE) {
                $requiredParams = array('up_partner_id', 'corporation_id', 'buy_price_type', 'buy_manager_user_id', 'purchase_currency', 'buy_exchange_rate');
                if (!empty($params['agent_id']) && $params['type'] == ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT) {
                    array_push($requiredParams, 'agent_type');
                }
            } else {
                $requiredParams = array('down_partner_id', 'corporation_id', 'sell_price_type', 'sell_manager_user_id', 'sell_currency', 'sell_exchange_rate');
            }

            //必填参数校验
            if (Utility::checkRequiredParamsNoFilterInject($params, $requiredParams)) {
                return true;
            }
        }

        return BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR);
    }

    //检查当前项目状态和合同状态是否可以操作合同
    public static function isCanOperateContract($project_status, $contract_status) {
        if ($project_status == Project::STATUS_SUBMIT && $contract_status < Contract::STATUS_SUBMIT) {
            return true;
        }

        return false;
    }

    /**
     * @desc 获取子合同编号
     * @param int $project_id
     * @param int $type
     * @return int
     */
    public static function getSubContractNum($project_id, $type) {
        $maxNum = 0;
        if (Utility::checkQueryId($project_id)) {
            $sql = 'select max(num) as max_num from t_contract where project_id = ' . $project_id . ' and type=' . $type . ' and is_main = 0';
            $res = Utility::query($sql);
            if (Utility::isNotEmpty($res)) {
                $maxNum = $res[0]['max_num'];
            }
        }

        return $maxNum + 1;
    }

    /**
     * 生成合同编号
     * @param $contractId
     * @throws Exception
     */
    public static function generateContractCode($contractId) {
        if (!Utility::checkQueryId($contractId)) {
            BusinessException::throw_exception(OilError::$PARAMS_PASS_ERROR);
        }

        $contractModel = Contract::model()->with('project')->findByPk($contractId);
        if (empty($contractModel->contract_id)) {
            BusinessException::throw_exception(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contractId));
        }

        $contractCodeInfo = CodeService::getContractCode($contractModel->project->corporation_id, $contractModel->manager_user_id, $contractModel->project->type, $contractModel->category);
        if ($contractCodeInfo['code'] == ConstantMap::VALID) {
            $contractModel->contract_code = $contractCodeInfo['contract_code'];
            $contractModel->save();
        } else {
            BusinessException::throw_exception(OilError::$CONTRACT_CODE_GENERATE_ERROR, array('reason' => $contractCodeInfo['msg']));
        }

        return;
    }


    //获取当前项目下所有合同
    public static function getAllContractFile($project_id) {
        $data = array();
        if (!Utility::checkQueryId($project_id)) {
            return $data;
        }

        $sql = "select f.file_id,f.name,f.file_url,f.category,f.is_main,c.type,c.num,c.contract_code 
                from t_contract_file f 
                left join t_contract c on f.contract_id=c.contract_id 
                where f.type=1 and f.status !=" . ContractFile::STATUS_DELETED . " and f.project_id=" . $project_id . " and f.status>=" . ContractFile::STATUS_CHECKING . " order by c.type,c.num,f.file_id asc ";
        $data = Utility::query($sql);

        if (Utility::isNotEmpty($data)) {
            $map = Map::$v;
            foreach ($data as $key => $value) {
                $data[$key]['contract_name'] = $value['contract_code'] . "-" . $map['contract_file_categories'][$value['type']][$value['category']]['name'];
            }
        }

        return $data;
    }


    /**
     * @desc 获取项目合同信息
     * @param int $projectId
     * @param int $type
     * @return array
     */
    public static function getContractsByProjectId($projectId, $type = 1) {
        if (Utility::checkQueryId($projectId)) {
            //$data = Contract::model()->with('files')->findAllToArray(array('select'=>'t.contract_id, t.status, files.code, files.code_out','condition' => 't.project_id = :projectId and t.type=:type and t.status >= :status and files.is_main = :isMain', 'params' => array('projectId' => $projectId, 'type' => $type, 'status' => Contract::STATUS_SUBMIT, 'isMain' => ConstantMap::CONTRACT_MAIN)));
            $sql = 'select a.contract_id, a.partner_id, a.status,a.contract_code as code,ifnull(b.status, 0) file_status,ifnull(b.file_id,0) file_id, ifnull(b.code_out,"") as code_out, c.name as partner_name, a.contract_date  
                    from t_contract a 
                    left join t_contract_file b on b.contract_id = a.contract_id and b.is_main=1 
                    left join t_partner c on c.partner_id = a.partner_id
                    where a.project_id = ' . $projectId . ' and a.type = ' . $type . ' 
                    and a.status >=' . Contract::STATUS_SUBMIT . ' 
                    and b.type = '. ConstantMap::FINAL_CONTRACT_MODULE .'
                    order by a.contract_id desc';
            $data = Utility::query($sql);

            return $data;
        }

        return array();
    }


    /**
     * @desc 判断指定项目下指定类型合同所有主文本是否全部上传
     * @param int $projectId
     * @param int $type
     * @return bool
     */
    public static function isMainContractFileAllUploaded($projectId, $type) {
        $sql = 'select * from t_contract_file where project_id = '.$projectId.' and type = '.$type.' and is_main = 1 and status < '.ContractFile::STATUS_CHECKING;
        $data = Utility::query($sql);
        if(Utility::isNotEmpty($data)) {
            return false;
        }

        return true;
    }

    /**
     * @desc 根据合同id获取合同交易信息
     * @param int $contract_id
     * @return array
     */
    public static function getContractGoodsInfo($contract_id) {
        $res = array();
        if (Utility::checkQueryId($contract_id)) {
            $sql = 'select a.detail_id as contract_detail_id, a.contract_id, a.project_id, a.goods_id, b.name as goods_name, a.goods_describe, a.quantity, a.unit, a.more_or_less_rate 
                    from t_contract_goods a 
                    left join t_goods b on b.goods_id = a.goods_id 
                    where a.contract_id = ' . $contract_id . ' order by a.detail_id';
            $res = Utility::query($sql);
        }

        return $res;
    }

    /**
     * @desc 检查合同是否可添加入库通知单
     * @param int $contract_id
     * @return bool
     */
    public static function checkCanAddStockNotice($contract_id) {
        return true;

        if(Utility::checkQueryId($contract_id)) {
            $contractFiles = ContractFile::model()->findAll('contract_id = :contractId and type = :type and status >= :status', array('contractId' => $contract_id, 'type' => ConstantMap::ELECTRON_SIGN_CONTRACT_FILE, 'status' => ContractFile::STATUS_CHECKING));
            if(Utility::isNotEmpty($contractFiles)) {
                return true;
            }
        }

        return false;
    }


    //判断交易明细品名是否重复
    public static function isHaveSameGoods($goodsItems)
    {
        $items = array();
        if(Utility::isNotEmpty($goodsItems)){
            foreach ($goodsItems as $key => $value) {
                $k = $value['goods_id'];
                if(!empty($value['type']))
                    $k .='_'.$value['type'];
                $items[$k] = $value;
            }
            if(count($items) != count($goodsItems)) {
                return true;
            }
        }
        return false;
    }


    public static function getSubContracts($contractId)
    {
        $models=ContractFile::model()->findAll("contract_id=".$contractId."");
    }

    /**
     * 判断当前用户是否有权限查看合同
     * @param $contractModel
     * @return bool
     */
    public static function isCanRead($contractModel)
    {
        $userId=Utility::getNowUserId();
        $res=AuthorizeService::checkUserCorpRight($contractModel->corporation_id,$userId);
        return ($res || $contractModel->manager_user_id==$userId || $contractModel->create_user_id==$userId);
    }

    /**
     * 获取合同商品交易明细id
     * @param $contractId
     * @param $goodsId
     * @return int|mixed
     */
    public static function getContractGoodsDetailId($contractId,$goodsId)
    {
        $model=ContractGoods::model()->find("contract_id=".$contractId." and goods_id=".$goodsId."");
        if(empty($model))
            return 0;
        return $model->detail_id;

    }

    /**
     * @desc 获取合同下所有商品id
     * @param int $contract_id
     * @return string
     */
    public static function getContractAllGoodsId($contract_id) {
        $res = '';
        if (Utility::checkQueryId($contract_id)) {
            $contractGoods = ContractGoods::model()->findAll('contract_id = :contractId', array('contractId' => $contract_id));
            $goodsIds = array();
            if (Utility::isNotEmpty($contractGoods)) {
                foreach ($contractGoods as $row) {
                    array_push($goodsIds, $row['goods_id']);
                }
            }
            $res = implode(',', $goodsIds);
        }
        return $res;
    }

    /**
     * @desc 获取关联合同
     * @param int $contractId
     * @return object
     */
    public static function getRelatedContract($contractId) {
        if(Utility::checkQueryId($contractId)) {
            $obj = Contract::model()->findByPk($contractId);
            $contract = Contract::model()->with('partner')->find("t.project_id=".$obj->project_id." and is_main=1 and t.contract_id<>".$contractId);
            return $contract;
        }
        return null;
    }

    /**
     * 生成合同组信息
     * @param $contract
     * @return bool
     * @throws Exception
     */
    public static function generateContractGroup($contract)
    {
        if(empty($contract) || empty($contract->contract_id))
            return false;

        $group=ContractGroup::model()->findByContractId($contract->contract_id);
        if(empty($group) && $contract->is_main) {
            $group = ContractGroup::model()->findMainByProjectId($contract->project_id);
        }
        if(empty($group))
        {
            $group=new ContractGroup();
        }
        $group->corporation_id=$contract->corporation_id;
        $group->project_id=$contract->project_id;
        $group->contract_id=$contract->contract_id;
        $group->is_main=$contract->is_main;
        $group->type=$contract->type;
        if($contract->type==ConstantMap::BUY_TYPE)
        {
            $group->up_partner_id=$contract->partner_id;
            $group->down_contract_id=0;
            $group->down_partner_id=0;

            if($contract->is_main==1 && !empty($contract->relative))
            {
                $group->down_contract_id=$contract->relative->contract_id;
                $group->down_partner_id=$contract->relative->partner_id;
            }
        }
        else
        {
            $group->down_partner_id=$contract->partner_id;
        }

        return $group->save();

    }

    /**
     * @desc 根据项目信息初始化合同组信息
     * @param object $project
     * @return bool
     */
    public static function initContractGroupByProject($project) {
        if(empty($project) || empty($project->project_id)) {
            return false;
        }

        $group = ContractGroup::model()->findMainByProjectId($project->project_id);
        if(empty($group)) {
            $group = new ContractGroup();
            $group->contract_id = 0;
            $group->down_contract_id = 0;
            $group->type = 0;
        }
        $group->corporation_id = $project->corporation_id;
        $group->project_id = $project->project_id;
        $group->is_main = 1;
        $group->up_partner_id = !empty($project->base->up_partner_id) ? $project->base->up_partner_id : 0;
        $group->down_partner_id = !empty($project->base->down_partner_id) ? $project->base->down_partner_id : 0;
        return $group->save();
    }

    /**
     * @desc 获取合同相关业务ID
     */
    public static function getContractBusinessIds() {
        return '2,3';
    }

    /**
     * @desc 获取合同
     */
    public static function getStockInBatchTotal($contractId, $goodsId, $unit) {
        $sql = "select ifnull(sum(quantity, 0)) as total_quantity from t_stock_in_batch_detail where contract_id=$contractId and goods_id=$goodsId and unit=$unit";
        $res = Utility::query($sql);
        if (!empty($res))
            return $res[0]['total_quantity'];

        return 0;
    }

    /**
     * @desc 获取交易主体下所有合同
     * @param int $corpId
     * @return array
     */
    public static function getCorporationContracts($corpId) {
        $res = array();
        if(Utility::checkQueryId($corpId) && $corpId > 0) {
            // $contracts = Contract::model()->orderBy('create_time desc')->findAll('corporation_id = :corpId and status >= :status', array('corpId' => $corpId, 'status' => Contract::STATUS_BUSINESS_CHECKED));
            $contracts = Contract::model()->findAll(array("condition"=>"corporation_id=".$corpId." and status>=".Contract::STATUS_BUSINESS_CHECKED,"order"=>"create_time desc"));
            if(Utility::isNotEmpty($contracts)) {
                foreach ($contracts as $key => $row) {
                    $res[$key]['contract_id'] = $row['contract_id'];
                    $res[$key]['contract_code'] = $row['contract_code'];
                }
            }
        }
        return $res;
    }

    /**
     * @desc 双边项目更新合同对应的关联合同id
     * @param int $projectId
     * @return bool
     */
    public static function updateRelationContractId($projectId) {
        if (Utility::checkQueryId($projectId) && $projectId > 0) {
            $sql = 'select contract_id from t_contract a left join t_project b on b.project_id=a.project_id where a.is_main=1 and b.type in (2,3,5,6,7) and a.project_id=' . $projectId;
            $contracts = Utility::query($sql);
            if (Utility::isNotEmpty($contracts) && count($contracts) == 2) {
                $rows1 = Contract::model()->updateByPk($contracts[0]['contract_id'], array('relation_contract_id'=>$contracts[1]['contract_id']));
                $rows2 = Contract::model()->updateByPk($contracts[1]['contract_id'], array('relation_contract_id'=>$contracts[0]['contract_id']));
                if($rows1 >= 0 && $rows2 >= 0) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    /**
     * @desc 获取合同入库金额（未结算金额）
     * @param int $contractId
     * @return int
     */
    public static function getContractStockInAmount($contractId)
    {
        $stockInAmount = 0;
        if(Utility::checkQueryId($contractId)) {
            $contract = Contract::model()->findByPk($contractId);
            if(!empty($contract)) {
                if(Utility::isNotEmpty($contract->goods)) {
                    foreach ($contract->goods as $v) {
                        $inQuantity = ContractGoodsService::getStockInGoodsQuantity($contractId, $v->goods_id, $v->unit);
                        $stockInAmount += $inQuantity['quantity'] * $v->price * $contract->exchange_rate;
                    }
                }
            }
        }
        return $stockInAmount;
    }

    /**
     * @desc 获取合同出库金额（未结算金额）
     * @param int $contractId
     * @return int
     */
    public static function getContractStockOutAmount($contractId)
    {
        $stockOutAmount = 0;
        if(Utility::checkQueryId($contractId)) {
            $contract = Contract::model()->findByPk($contractId);
            if(!empty($contract)) {
                if(Utility::isNotEmpty($contract->goods)) {
                    foreach ($contract->goods as $v) {
                        $outQuantity = ContractGoodsService::getStockOutGoodsQuantity($contractId, $v->goods_id);
                        $stockOutAmount += $outQuantity * $v->price * $contract->exchange_rate;
                    }
                }
            }
        }
        return $stockOutAmount;
    }

    /**
     * @desc 根据合同id获取货款进项票金额
     * @param int $contractId
     * @return int
     */
    public static function getContractInputInvoiceAmount($contractId)
    {
        $amount = 0;
        if(Utility::checkQueryId($contractId)) {
            $sql = 'select ifnull(sum(ia.amount*ia.exchange_rate),0) as total_amount
                    from t_invoice_application ia
                    left join t_contract c on c.contract_id = ia.contract_id
                    where ia.type=1 and ia.type_sub=1 and ia.status>=3 and ia.contract_id = ' . $contractId;

            $res = Utility::query($sql);
            if(Utility::isNotEmpty($res)) {
                $amount = $res[0]['total_amount'];
            }
        }
        return $amount;
    }

    /**
     * @desc 根据合同id获取货款销项票金额
     * @param int $contractId
     * @return int
     */
    public static function getContractOutputInvoiceAmount($contractId)
    {
        $amount = 0;
        if(Utility::checkQueryId($contractId)) {
            $sql = 'select ifnull(sum(i.amount),0) as total_amount
                    from t_invoice i 
                    left join t_contract c on c.contract_id = i.contract_id 
                    left join t_invoice_application ia on ia.apply_id = i.apply_id 
                    where i.status>=3 and i.contract_id = ' . $contractId . ' and ia.type=2 and ia.type_sub=1';

            $res = Utility::query($sql);
            if(Utility::isNotEmpty($res)) {
                $amount = $res[0]['total_amount'];
            }
        }
        return $amount;
    }

    /**
     * @desc 获取合同货款实付金额  该方法主要用于额度计算时统计合同货款的实付金额
     * @param int $contractId
     * @return float
     */
    public static function getContractGoodsActualPaidAmount($contractId) {
        if (Utility::checkQueryId($contractId)) {
            $sql1 = 'select ifnull(sum(a.amount_cny), 0) sum_amount_cny from t_payment a 
                     left join t_pay_application b on b.apply_id = a.apply_id 
                     left join t_contract c on c.contract_id = b.contract_id 
                     where b.contract_id=' . $contractId . ' and a.status>=' . Payment::STATUS_SUBMITED . ' and b.subject_id in (' . ConstantMap::GOODS_FEE_SUBJECT_ID . ')';
            $sql2 = 'select ifnull(sum(pc.amount_cny), 0) sum_amount_cny from t_pay_claim pc 
                     left join t_contract co on co.contract_id = pc.contract_id 
                     left join t_pay_application pa on pa.apply_id = pc.apply_id 
                     where pc.contract_id=' . $contractId . ' and pc.status>=' . PayClaim::STATUS_SUBMITED . ' and pa.subject_id in (' . ConstantMap::GOODS_FEE_SUBJECT_ID . ')';
            $sql = 'select sum(sum_amount_cny) as sum_amount_cny from (' . $sql1 . ' union ' . $sql2 . ') p';
            $res = Utility::query($sql);
            if (Utility::isNotEmpty($res)) {
                $taxAmount = self::getContractTaxActualPaidAmount($contractId);
                return $res[0]['sum_amount_cny'] - $taxAmount;
            }
        }

        return 0;
    }

    /**
     * @desc 获取合同关税实付金额  该方法主要用于额度计算时统计合同关税的实付金额（代理进口合同-纯代理模式 税款保证金计入关税）
     * @param int $contractId
     * @return float
     */
    public static function getContractTaxActualPaidAmount($contractId) {
        if (Utility::checkQueryId($contractId)) {
            $sql1 = 'select ifnull(sum(a.amount_cny), 0) sum_amount_cny from t_payment a 
                     left join t_pay_application b on b.apply_id = a.apply_id 
                     left join t_contract c on c.contract_id = b.contract_id 
                     where b.contract_id=' . $contractId . ' and a.status>=' . Payment::STATUS_SUBMITED . ' and c.agent_type=' . ConstantMap::AGENT_TYPE_PURE . ' and b.subject_id=' . ConstantMap::TAX_DEPOSIT_SUBJECT_ID;
            $sql2 = 'select ifnull(sum(pc.amount_cny), 0) sum_amount_cny from t_pay_claim pc 
                     left join t_contract co on co.contract_id = pc.contract_id 
                     left join t_pay_application pa on pa.apply_id = pc.apply_id 
                     where pc.contract_id=' . $contractId . ' and pc.status>=' . PayClaim::STATUS_SUBMITED . ' and co.agent_type=' . ConstantMap::AGENT_TYPE_PURE . ' and pa.subject_id=' . ConstantMap::TAX_DEPOSIT_SUBJECT_ID;
            $sql = 'select sum(sum_amount_cny) as sum_amount_cny from (' . $sql1 . ' union ' . $sql2 . ') p';
            $res = Utility::query($sql);
            if (Utility::isNotEmpty($res)) {
                return $res[0]['sum_amount_cny'];
            }
        }

        return 0;
    }

    /**
     * @desc 获取合同关税实收金额  该方法主要用于额度计算时统计合同关税的实收额（代理进口合同-纯代理模式 税款保证金计入关税）
     * @param int $contractId
     * @return float
     */
    public static function getContractTaxActualReceivedAmount($contractId)
    {
        if (Utility::checkQueryId($contractId)) {
            $sql = 'select ifnull(sum(rc.amount),0) as total_amount from t_receive_confirm rc 
                    left join t_contract c on c.contract_id = rc.contract_id where rc.contract_id = ' . $contractId . ' and rc.status>=1 and rc.subject=' . ConstantMap::TAX_DEPOSIT_SUBJECT_ID . ' and c.agent_type=' . ConstantMap::AGENT_TYPE_PURE;
            $res = Utility::query($sql);
            if (Utility::isNotEmpty($res)) {
                return $res[0]['total_amount'];
            }
        }

        return 0;
    }

    /**
     * @desc 获取合同结算金额
     * @param int $contractId
     * @param object $contractModel
     * @return int
     */
    public static function getContractGoodsSettlementAmount($contractId, $contractModel = null)
    {
        if (Utility::checkQueryId($contractId))
        {
            if (empty($contractModel))
            {
                $contractModel = Contract::model()->findByPk($contractId);
            }
            $condition = '';
            if (!empty($contractModel))
            {
                if (in_array($contractModel->settle_type, array(Contract::SETTLE_TYPE_BUY_CONTRACT, Contract::SETTLE_TYPE_SALE_CONTRACT)))
                {
                    $condition .= ' and status=' . ContractSettlement::STATUS_PASS;
                }

                $contractSettlementModel = ContractSettlement::model()->findByContractId($contractId, $condition);
                if (!empty($contractSettlementModel))
                {
                    return $contractSettlementModel->amount_goods;
                }
            }
        }

        return 0;
    }

    /**
     * @desc 获取交易商品入库未结算金额
     * @param int $contractId
     * @param object $contractModel
     * @return int
     */
    public static function getTradeGoodsInUnsettledAmount($contractId, $contractModel = null)
    {
        $inUnsettledAmount = 0;
        if (Utility::checkQueryId($contractId))
        {
            if (empty($contractModel))
            {
                $contractModel = Contract::model()->findByPk($contractId);
            }
            if (!empty($contractModel))
            {
                if (Utility::isNotEmpty($contractModel->goods))
                {
                    foreach ($contractModel->goods as $v)
                    {
                        $inUnsettledQuantity = ContractGoodsService::getTradeGoodsInUnsettledQuantity($contractId, $v->goods_id, $v->unit);
                        $inUnsettledAmount += $inUnsettledQuantity['quantity'] * $v->price * $contractModel->exchange_rate;
                    }
                }
            }
        }

        return $inUnsettledAmount;
    }

    /**
     * @desc 获取交易商品入库未结算金额
     * @param int $contractId
     * @param object $contractModel
     * @return int
     */
    public static function getTradeGoodsOutUnsettledAmount($contractId, $contractModel = null)
    {
        $outUnsettledAmount = 0;
        if (Utility::checkQueryId($contractId))
        {
            if (empty($contractModel))
            {
                $contractModel = Contract::model()->findByPk($contractId);
            }
            if (!empty($contractModel))
            {
                if (Utility::isNotEmpty($contractModel->goods))
                {
                    foreach ($contractModel->goods as $v)
                    {
                        $outUnsettledQuantity = ContractGoodsService::getTradeGoodsOutUnsettledQuantity($contractId, $v->goods_id);
                        $outUnsettledAmount += $outUnsettledQuantity * $v->price * $contractModel->exchange_rate;
                    }
                }
            }
        }

        return $outUnsettledAmount;
    }

    /**
     * @desc 获取合同入库结算差额
     * @param int $contractId
     * @param object $contractModel
     * @return int
     */
    public static function getTradeGoodsInSettleDiffAmount($contractId, $contractModel = null)
    {
        $diffAmount = 0;
        if (Utility::checkQueryId($contractId))
        {
            if (empty($contractModel))
            {
                $contractModel = Contract::model()->findByPk($contractId);
            }
            if (!empty($contractModel))
            {
                //已结算的入库通知单
                $ladingSettlements = LadingSettlement::model()->findAll('contract_id=:contractId and status=:status', array('contractId' => $contractId, 'status' => LadingSettlement::STATUS_PASS));
                if (Utility::isNotEmpty($ladingSettlements))
                {
                    foreach ($ladingSettlements as $ladingSettlement)
                    {
                        $ladingBillSettlementEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\contractSettlement\ILadingBillSettlementRepository::class)->findByPk($ladingSettlement->settle_id);
                        if (empty($ladingBillSettlementEntity))
                        {
                            Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ', lading bill settlement entity not exits!, settle_id is:' . $ladingSettlement->settle_id);
                            continue;
                        }

                        $settleItems = $ladingBillSettlementEntity->goods_expense;
                        if (\Utility::isNotEmpty($settleItems))
                        {
                            foreach ($settleItems as $goodsId => $entity)
                            {
                                $tradeGoods = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\contract\ITradeGoodsRepository::class)->findByContractIdAndGoodsId($contractId, $goodsId);
                                if ($tradeGoods->unit == $entity->settle_quantity->unit)
                                {
                                    $diffAmount += ($entity->in_quantity->quantity * $tradeGoods->price * $contractModel->exchange_rate - $entity->settle_amount_cny);
                                } elseif ($tradeGoods->unit == $entity->settle_quantity_sub->unit)
                                {
                                    $diffAmount += (($entity->in_quantity_sub->quantity * $tradeGoods->price - $entity->settle_quantity_sub->quantity * $entity->settle_price) * $contractModel->exchange_rate);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $diffAmount;
    }

    /**
     * @desc 获取合同出库结算差额
     * @param int $contractId
     * @param object $contractModel
     * @return int
     */
    public static function getTradeGoodsOutSettleDiffAmount($contractId, $contractModel = null)
    {
        $diffAmount = 0;
        if (Utility::checkQueryId($contractId))
        {
            if (empty($contractModel))
            {
                $contractModel = Contract::model()->findByPk($contractId);
            }
            if (!empty($contractModel))
            {
                //已结算的发货单
                $deliverySettlements = DeliverySettlement::model()->findAll('contract_id=:contractId and status=:status', array('contractId' => $contractId, 'status' => LadingSettlement::STATUS_PASS));
                if (Utility::isNotEmpty($deliverySettlements))
                {
                    foreach ($deliverySettlements as $deliverySettlement)
                    {
                        $deliverySettlementEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\contractSettlement\IDeliveryOrderSettlementRepository::class)->findByPk($deliverySettlement->settle_id);
                        if (empty($deliverySettlementEntity))
                        {
                            Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ', lading bill settlement entity not exits!, settle_id is:' . $deliverySettlement->settle_id);
                            continue;
                        }

                        $settleItems = $deliverySettlementEntity->goods_expense;
                        if (\Utility::isNotEmpty($settleItems))
                        {
                            foreach ($settleItems as $goodsId => $entity)
                            {
                                $tradeGoods = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\contract\ITradeGoodsRepository::class)->findByContractIdAndGoodsId($contractId, $goodsId);

                                if ($tradeGoods->unit == $entity->settle_quantity->unit)
                                {
                                    $diffAmount += ($entity->settle_amount_cny - $entity->out_quantity->quantity * $tradeGoods->price * $contractModel->exchange_rate);
                                } elseif ($tradeGoods->unit == $entity->settle_quantity_sub->unit)
                                {
                                    $diffAmount += (($entity->settle_quantity_sub->quantity * $entity->settle_price - $entity->out_quantity_sub->quantity * $tradeGoods->price) * $contractModel->exchange_rate);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $diffAmount;
    }

    /**
     * @desc 获取合同入库金额，该接口计算错误，主要用于数据修复，数据修复完删除该接口，正确获取使用getContractStockInAmount方法!!!
     * @param int $contractId
     * @return int
     */
    /*public static function oldGetContractStockInAmount($contractId)
    {
        $stockInAmount = 0;
        if(Utility::checkQueryId($contractId)) {
            $contract = Contract::model()->findByPk($contractId);
            if(!empty($contract)) {
                if(Utility::isNotEmpty($contract->goods)) {
                    foreach ($contract->goods as $v) {
                        $inQuantity = ContractGoodsService::getStockInGoodsQuantity($contractId, $v->goods_id, $v->unit);
                        $stockInAmount = $inQuantity['quantity'] * $v->price * $contract->exchange_rate;
                    }
                }
            }
        }
        return $stockInAmount;
    }*/

    /**
     * @desc 获取合同出库金额，该接口计算错误，主要用于数据修复，数据修复完删除该接口，正确获取使用getContractStockOutAmount方法!!!
     * @param int $contractId
     * @return int
     */
    /*public static function oldGetContractStockOutAmount($contractId)
    {
        $stockOutAmount = 0;
        if(Utility::checkQueryId($contractId)) {
            $contract = Contract::model()->findByPk($contractId);
            if(!empty($contract)) {
                if(Utility::isNotEmpty($contract->goods)) {
                    foreach ($contract->goods as $v) {
                        $outQuantity = ContractGoodsService::getStockOutGoodsQuantity($contractId, $v->goods_id);
                        $stockOutAmount = $outQuantity * $v->price * $contract->exchange_rate;
                    }
                }
            }
        }
        return $stockOutAmount;
    }*/

	/**
     * @desc 根据合同id获取合同文本信息
     * @param int $contractId
     * @return array
     */
    public static function getContractFiles($contractId)
    {
        $res = array();
        if (Utility::checkQueryId($contractId) && $contractId > 0)
        {
            $sql = 'select * from t_contract_file where contract_id=' . $contractId . ' and type=' . ConstantMap::FINAL_CONTRACT_FILE . ' and status>=' . ContractFile::STATUS_CHECK_PASS . ' order by file_id asc';
            $data = Utility::query($sql);
            if (Utility::isNotEmpty($data))
            {
                foreach ($data as $key => $row)
                {
                    $res[$key]['is_main'] = $row['is_main'];
                    $res[$key]['category'] = $row['category'];
                    $res[$key]['version_type'] = $row['version_type'];
                    $res[$key]['code'] = $row['code'];
                    $res[$key]['code_out'] = $row['code_out'];
                    $res[$key]['final_file_id'] = !empty($row['file_id']) ? $row['file_id'] : 0;
                    $res[$key]['final_file_url'] = !empty($row['file_url']) ? $row['file_url'] : '';
                    $res[$key]['final_file_name'] = !empty($row['name']) ? $row['name'] : '';
                    $eContractFile = ContractFileService::getSpecialContractFileInfo($row, ConstantMap::ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE, ContractFile::STATUS_CHECKING);
                    if (!empty($eContractFile))
                    {
                        $res[$key]['esign_file_id'] = !empty($eContractFile->file_id) ? $eContractFile->file_id : 0;
                        $res[$key]['esign_file_url'] = !empty($eContractFile->file_url) ? $eContractFile->file_url : '';
                        $res[$key]['esign_file_name'] = !empty($eContractFile->name) ? $eContractFile->name : '';
                    }
                    $pContractFile = ContractFileService::getSpecialContractFileInfo($row, ConstantMap::PAPER_DOUBLE_SIGN_CONTRACT_MODULE, ContractFile::STATUS_CHECKING);
                    if (!empty($pContractFile))
                    {
                        $res[$key]['psign_file_id'] = !empty($pContractFile->file_id) ? $pContractFile->file_id : 0;
                        $res[$key]['psign_file_url'] = !empty($pContractFile->file_url) ? $pContractFile->file_url : '';
                        $res[$key]['psign_file_name'] = !empty($pContractFile->name) ? $pContractFile->name : '';
                    }
                }
            }
        }

        return $res;
    }

    /**
     * @desc 更新合同签订时间
     * @param int $contractId
     * @param string $signDate
     * @throws
     */
    public static function updateSignDateByContractId($contractId, $signDate) {
        if (Utility::checkQueryId($contractId) && !empty($signDate)) {
            Contract::model()->updateByPk($contractId,array(
                "contract_date"=>$signDate,
                "update_time"=>new CDbExpression("now()"),
                "update_user_id"=>Utility::getNowUserId()
            ));
        }
    }

    /**
     * 判断当前合同下是否可以添加入库单通知单
     * @param array $rowData  合同记录
     * @return bool
     */
    public static function isCanAddStockInNoticeOrder(array $rowData){
        $status = $rowData['contract_status'];
        if($status >= Contract::STATUS_SETTLED_SUBMIT || Contract::SPLIT_TYPE_SPLIT == $rowData['split_type'] || $rowData['original_id'] > 0){
            return false;
        }
        return  true;
    }

}