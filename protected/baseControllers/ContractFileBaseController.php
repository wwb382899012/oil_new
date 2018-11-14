<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/15 17:25
 * Describe：
 */
class ContractFileBaseController extends AttachmentController {
    public $moduleType = 0;
    public $indexView = "/contractFile/index";
    public $editView = "/contractFile/edit";
    public $detailView = "/contractFile/detail";

    public function pageInit() {
        parent::pageInit();
        $this->attachmentType = Attachment::C_CONTRACTFILE;
        $this->filterActions = 'getFileId';
        $this->rightCode = 'doubleSign';
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex() {
//        $attr = $_GET['search'];
        $attr = $this->getSearch();

        $query = '';
        if ($this->moduleType == ConstantMap::FINAL_CONTRACT_MODULE) {
            $this->pageTitle = '合同上传';
        } else {
            $contractFileType = ConstantMap::FINAL_CONTRACT_FILE;
            $contractFileStatus = ContractFile::STATUS_CHECK_PASS;
            if ($this->moduleType == ConstantMap::ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE) { //电子双签
                $this->pageTitle = '电子双签合同上传';
            } elseif ($this->moduleType == ConstantMap::PAPER_DOUBLE_SIGN_CONTRACT_MODULE) { //纸质双签
                $contractFileType = ConstantMap::ELECTRON_SIGN_CONTRACT_FILE;
                $contractFileStatus = ContractFile::STATUS_CHECKING;
                $this->pageTitle = '纸质双签合同上传';
            }

            $query .= ' and exists(select file_id from t_contract_file where type = ' . $contractFileType . ' and status = ' . $contractFileStatus . ' and project_id = a.project_id) ';
        }

        $projectType = 0;
        if (!empty($attr['project_type'])) {
            switch ($attr["project_type"]) {
                case ConstantMap::SELF_IMPORT_FIRST_SALE_LAST_BUY: //进口自营-先销后采
                    $query .= " and a.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and c.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_IMPORT_FIRST_BUY_LAST_SALE: //进口自营-先采后销
                    $query .= " and a.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and c.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_SALE_LAST_BUY: //内贸自营-先销后采
                    $query .= " and a.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and c.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_BUY_LAST_SALE: //内贸自营-先采后销
                    $query .= " and a.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and c.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                default:
                    $query .= " and a.type = " . $attr['project_type'];
                    break;
            }
            $projectType = $attr['project_type'];
            unset($attr['project_type']);
        }

        $user = SystemUser::getUser(Utility::getNowUserId());
        $sql = 'select {col} from t_project a 
                left join t_system_user b on b.user_id = a.manager_user_id 
                left join t_project_base c on c.project_id = a.project_id 
                left join t_partner up on up.partner_id = c.up_partner_id 
                left join t_partner dp on dp.partner_id = c.down_partner_id 
                left join t_corporation co on co.corporation_id = a.corporation_id 
                left join t_system_user su on su.user_id = a.create_user_id ' . $this->getWhereSql($attr) . $query . ' 
                and a.status >= ' . Project::STATUS_SUBMIT . ' and a.corporation_id in (' . $user['corp_ids'] . ') 
                and exists(select contract_id from t_contract where status >= ' . Contract::STATUS_BUSINESS_CHECKED . ' and project_id = a.project_id) 
                order by a.project_id desc {limit}';
        $fields = 'a.project_id, a.project_code, a.type, a.status, b.name, c.buy_sell_type, c.up_partner_id, up.name as up_partner_name, 
                   c.down_partner_id, dp.name as down_partner_name, a.create_time, a.corporation_id, co.name as corp_name, su.name as create_name';
        $data = $this->queryTablesByPage($sql, $fields);

        if (!empty($projectType))
            $attr['project_type'] = $projectType;

        $data["search"] = $attr;
        $this->render($this->indexView, $data);
    }

    public function actionEdit() {
        $contractData = $this->getContractFileData();
        $contracts = $contractData['contracts'];
        $projectInfo = $contractData['projectInfo'];

        $this->render($this->editView, array('contracts' => $contracts, 'project' => $projectInfo));
    }

    protected function getFileExtras() {
        $fileId = Mod::app()->request->getParam("id");
        $projectId = Mod::app()->request->getParam("project_id");
        $contractId = Mod::app()->request->getParam("contract_id");
        $isMain = Mod::app()->request->getParam("is_main");
        $category = Mod::app()->request->getParam("category");
        $code = Mod::app()->request->getParam("code");

        return array("file_id" => $fileId, 'project_id' => $projectId, 'contract_id' => $contractId, 'is_main' => $isMain, 'category' => $category, 'code' => $code);
    }

    public function actionSave() {
        $params = Mod::app()->request->getParam('data');
        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' pass params are:' . json_encode($params));
        if (!Utility::checkQueryId($params['file_id']) || !Utility::checkQueryId($params['project_id']) || !Utility::checkQueryId($params['contract_id'])) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        //项目是否存在
        /*if (!ProjectService::checkProjectExist($params['project_id'])) {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $params['project_id'])));
        }*/
        $project = Project::model()->findByPk($params['project_id']);
        if (empty($project)) {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $params['project_id'])));
        }

        //合同是否存在
        $contractModel = Contract::model()->with("project")->findByPk($params['contract_id']);
        if (empty($contractModel->contract_id)) {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $params['contract_id'])));
        }

        //最终合同参数校验
        if ($this->moduleType == ConstantMap::FINAL_CONTRACT_MODULE) {
            $requiredParams = array('category', 'version_type');
            if (!Utility::checkRequiredParamsNoFilterInject($params, $requiredParams)) {
                BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR);
            }
        } elseif ($this->moduleType == ConstantMap::ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE) {
            if (empty($params['sign_date'])) {
                BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR);
            }
        }

        //附件是否上传
        $contractFileModel = ContractFile::model()->findByPk($params['file_id']);
        if (empty($contractFileModel->file_id) || empty($contractFileModel->file_url)) {
            $this->returnError(BusinessError::outputError(OilError::$CONTRACT_FILE_ATTACH_NOT_UPLOAD));
        }

        if ($contractFileModel->status >= ContractFile::STATUS_CHECKING) {
            $this->returnError(BusinessError::outputError(OilError::$SIGN_UPLOAD_SUBMIT_STATUS_NOT_ALLOW));
        }

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();

        try {
            //合同上传信息保存
            unset($params['file_id']);
            if(empty($params['sign_date'])){
                unset($params['sign_date']);
            }
            $contractFileModel->setAttributes($params, false);
            $contractFileModel->save();

            if ($this->moduleType == ConstantMap::FINAL_CONTRACT_MODULE) { //最终合同
                //审批流生成
                if ($contractFileModel->status == ContractFile::STATUS_CHECKING) {
                    FlowService::startFlowForCheck4($contractFileModel->file_id);
                    if (ContractService::isMainContractFileAllUploaded($contractFileModel->project_id, ConstantMap::FINAL_CONTRACT_MODULE)) {
                        //合同上传task关闭
                        TaskService::doneTask($contractFileModel->project_id, Action::ACTION_14, ActionService::getActionRoleIds(Action::ACTION_14), 0);
                    }
                }
            }

            TaskService::doneTask($contractFileModel->file_id, Action::ACTION_31);

            //生成纸质合同上传初始数据
            if ($this->moduleType == ConstantMap::ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE) {
                //更新主合同文本合同签订日期
                if ($contractFileModel->is_main == ConstantMap::CONTRACT_MAIN) {
                    ContractService::updateSignDateByContractId($contractFileModel->contract_id, $params['sign_date']);
                }

                if ($contractFileModel->status == ContractFile::STATUS_CHECKING) {
                    ContractFileService::insertSignFileByFileId($contractFileModel->file_id, ConstantMap::PAPER_SIGN_CONTRACT_FILE);
                    $taskPfileExist = TaskService::checkTaskExist(Action::ACTION_17, $contractFileModel->project_id, ActionService::getActionRoleIds(Action::ACTION_17));
                    if (!$taskPfileExist) {
                        // action 17 title 参数 : project code, contract code, contract type
                        TaskService::addTasks(Action::ACTION_17, $contractFileModel->project_id, ActionService::getActionRoleIds(Action::ACTION_17), 0, $contractModel->corporation_id, array('projectCode'=>$contractModel->project->project_code, 'contractCode'=>$contractModel->contract_code, 'contractType'=>$contractModel->getContractType()));
                    }

                    if (ContractService::isMainContractFileAllUploaded($contractFileModel->project_id, ConstantMap::ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE)) {
                        TaskService::doneTask($contractFileModel->project_id, Action::ACTION_16, ActionService::getActionRoleIds(Action::ACTION_16), 0);
                    }
                }
            }

            if ($this->moduleType == ConstantMap::PAPER_SIGN_CONTRACT_FILE) {
                if ($contractFileModel->status == ContractFile::STATUS_CHECKING) {
                    if (ContractService::isMainContractFileAllUploaded($contractFileModel->project_id, ConstantMap::PAPER_SIGN_CONTRACT_FILE)) {
                        TaskService::doneTask($contractFileModel->project_id, Action::ACTION_17, ActionService::getActionRoleIds(Action::ACTION_17), 0);
                    }
                }
            }
            ContractFileService::updateContractStatusByFileId($contractFileModel->file_id);
            $trans->commit();

            Utility::addActionLog(null, "上传合同附件", "ContractFile", $contractFileModel->file_id);
            $this->returnSuccess($contractFileModel->file_id);
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $e->getMessage())));
        }
    }

    protected function getContractFileData() {
        $project_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($project_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        /*if (!ProjectService::checkProjectExist($project_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $project_id)));
        }*/

        $project = Project::model()->findByPk($project_id);
        if (empty($project)) {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $project_id)));
        }

        $moduleName = $this->moduleType == ConstantMap::ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE ? '电子双签合同' : ($this->moduleType == ConstantMap::PAPER_DOUBLE_SIGN_CONTRACT_MODULE ? '纸质双签合同' : '合同');
        if (!ProjectService::checkIsCanContractUpload($project_id, $this->moduleType)) {
            $this->renderError(BusinessError::outputError(OilError::$CONTRACT_FILE_UPLOAD_NOT_ALLOW, array('module_name' => $moduleName)));
        }

        $project = Project::model()->with('base')->findByPk($project_id);
        $projectInfo = $project->getAttributes(array('project_id', 'project_code', 'type', 'corporation_id'));
        $projectInfo['project_type_desc'] = Map::$v['project_type'][$projectInfo['type']];
        if (!empty($project->base->buy_sell_type)) {
            $projectInfo['project_type_desc'] .= '-' . Map::$v['purchase_sale_order'][$project->base->buy_sell_type];
        }

        if ($this->moduleType == ConstantMap::FINAL_CONTRACT_MODULE) { //最终合同
            $condition = 't.project_id = :projectId and t.status >= :cStatus and ((files.status != :fStatus and files.type = :fType) or not exists(select file_id from t_contract_file where contract_id = t.contract_id))';
            $params = array('projectId' => $project_id, 'cStatus' => Contract::STATUS_BUSINESS_CHECKED, 'fStatus' => ContractFile::STATUS_DELETED, 'fType' => ConstantMap::FINAL_CONTRACT_FILE);
        } else { //双签合同
            $contractFileType = ConstantMap::FINAL_CONTRACT_FILE;
            $contractFileStatus = ContractFile::STATUS_CHECK_PASS;
            $fileType = ConstantMap::ELECTRON_SIGN_CONTRACT_FILE;
            if ($this->moduleType == ConstantMap::PAPER_DOUBLE_SIGN_CONTRACT_MODULE) { //纸质双签
                $contractFileType = ConstantMap::ELECTRON_SIGN_CONTRACT_FILE;
                $contractFileStatus = ContractFile::STATUS_CHECKING;
                $fileType = ConstantMap::PAPER_SIGN_CONTRACT_FILE;
            }
            $condition = 't.project_id = :projectId and t.status >= :cStatus and files.type = :fType and files.status >= :status';
            $params = array('projectId' => $project_id, 'fType' => $contractFileType, 'cStatus' => Contract::STATUS_BUSINESS_CHECKED, 'status' => $contractFileStatus);
        }

        //获取合同信息
        $contracts = Contract::model()->with('files', 'partner')->findAll(array('condition' => $condition, 'params' => $params, 'order' => 't.type asc, t.contract_id asc, files.file_id asc'));
        $res = array();
        if (Utility::isNotEmpty($contracts)) {
            foreach ($contracts as $key => $row) {
                $res[$key]['project_id'] = $row['project_id'];
                $res[$key]['contract_id'] = $row['contract_id'];
                $res[$key]['type'] = $row['type'];
                $res[$key]['contract_code'] = $row['contract_code'];
                $res[$key]['partner_id'] = $row['partner_id'];
                $res[$key]['partner_name'] = $row->partner->name;
                $res[$key]['amount'] = Map::$v['currency'][$row['currency']]['ico'] . Utility::numberFormatFen2Yuan($row['amount']);
                $res[$key]['goods'] = GoodsService::getSpecialGoodsNames(ContractService::getContractAllGoodsId($row['contract_id']),' | ');
                if (Utility::isNotEmpty($row['files'])) {
                    if ($this->moduleType == ConstantMap::FINAL_CONTRACT_MODULE) { //最终合同
                        foreach ($row['files'] as $k => $v) {
                            $res[$key]['files'][$k] = $v->getAttributes(true, array("start_date", "end_date", "create_user_id", "create_time", "update_user_id", "update_time"));
                        }
                    } else { //双签合同
                        //获取双签合同信息
                        $signContractFiles = ContractFile::model()->with('contract')->findAll(array('condition' => 't.type = :type and t.project_id = :projectId and t.contract_id = :contractId', 'params' => array('type' => $fileType, 'projectId' => $project_id, 'contractId' => $row['contract_id']), 'order' => 't.contract_id asc'));
                        if (Utility::isNotEmpty($signContractFiles)) {
                            foreach ($signContractFiles as $index => $v) {
                                $res[$key]['files'][$index] = $v->getAttributes(true, array("start_date", "end_date", "create_user_id", "create_time", "update_user_id", "update_time"));
                                $res[$key]['files'][$index]['contract_name'] = Map::$v['contract_file_categories'][$row['type']][$v['category']]['name'] . '&nbsp;&nbsp;<span class="text-red" style="font-size: 12px">' . Map::$v['contract_standard_type'][$v['version_type']]['name'] . '</span>';
                                //对应最终合同附件信息
                                //$contractFile = ContractFile::model()->find(array('select' => 'file_id,file_url,name,category,is_main', 'condition' => 'project_id = :projectId and contract_id = :contractId and type = :type and status = :status', 'params' => array('projectId' => $v->project_id, 'contractId' => $v->contract_id, 'type' => ConstantMap::FINAL_CONTRACT_FILE, 'status' => ContractFile::STATUS_CHECK_PASS)));
                                $contractFile = ContractFileService::getSpecialContractFileInfo($v, ConstantMap::FINAL_CONTRACT_FILE, ContractFile::STATUS_CHECK_PASS);
                                $res[$key]['files'][$index]['final_file_url'] = !empty($contractFile->file_url) ? $contractFile->file_url : '';
                                $res[$key]['files'][$index]['final_file_name'] = !empty($contractFile->name) ? $contractFile->name : '';
                                $res[$key]['files'][$index]['final_file_id'] = !empty($contractFile->file_id) ? $contractFile->file_id : 0;
                                //对应电子双签合同附件信息
                                //$eSignContractFile = ContractFile::model()->find(array('select' => 'file_id,file_url,name,category,is_main', 'condition' => 'project_id = :projectId and contract_id = :contractId and type = :type and status = :status', 'params' => array('projectId' => $v->project_id, 'contractId' => $v->contract_id, 'type' => ConstantMap::ELECTRON_SIGN_CONTRACT_FILE, 'status' => ContractFile::STATUS_CHECKING)));
                                $eSignContractFile = ContractFileService::getSpecialContractFileInfo($v, ConstantMap::ELECTRON_SIGN_CONTRACT_FILE, ContractFile::STATUS_CHECKING);
                                $res[$key]['files'][$index]['esign_file_url'] = !empty($eSignContractFile->file_url) ? $eSignContractFile->file_url : '';
                                $res[$key]['files'][$index]['esign_file_name'] = !empty($eSignContractFile->name) ? $eSignContractFile->name : '';
                                $res[$key]['files'][$index]['esign_file_id'] = !empty($eSignContractFile->file_id) ? $eSignContractFile->file_id : 0;
                            }
                        } else {
                            foreach ($row['files'] as $k => $file) {
                                $res[$key]['files'][$k]['file_id'] = IDService::getContractFileId();
                                $res[$key]['files'][$k]['project_id'] = $file->project_id;
                                $res[$key]['files'][$k]['contract_id'] = $file->contract_id;
                                $res[$key]['files'][$k]['is_main'] = $file->is_main;
                                $res[$key]['files'][$k]['type'] = $fileType;
                                $res[$key]['files'][$k]['category'] = $file->category;
                                $res[$key]['files'][$k]['version_type'] = $file->version_type;
                                $res[$key]['files'][$k]['code'] = $file->code;
                                $res[$key]['files'][$k]['code_out'] = $file->code_out;
                                $res[$key]['files'][$k]['contract_name'] = Map::$v['contract_file_categories'][$row['type']][$file->category]['name'] . '&nbsp;&nbsp;' . Map::$v['contract_standard_type'][$file->version_type]['name'];
                                //对应最终合同附件信息
                                //$contractFile = ContractFile::model()->find(array('select' => 'file_id,file_url,name,category,is_main', 'condition' => 'project_id = :projectId and contract_id = :contractId and type = :type and status = :status', 'params' => array('projectId' => $file->project_id, 'contractId' => $file->contract_id, 'type' => ConstantMap::FINAL_CONTRACT_FILE, 'status' => ContractFile::STATUS_CHECK_PASS)));
                                $contractFile = ContractFileService::getSpecialContractFileInfo($file, ConstantMap::FINAL_CONTRACT_FILE, ContractFile::STATUS_CHECK_PASS);
                                $res[$key]['files'][$k]['final_file_url'] = !empty($contractFile->file_url) ? $contractFile->file_url : '';
                                $res[$key]['files'][$k]['final_file_name'] = !empty($contractFile->name) ? $contractFile->name : '';
                                $res[$key]['files'][$k]['final_file_id'] = !empty($contractFile->file_id) ? $contractFile->file_id : 0;
                                //对应电子双签合同附件信息
                                //$eSignContractFile = ContractFile::model()->find(array('select' => 'file_id,file_url,name,category,is_main', 'condition' => 'project_id = :projectId and contract_id = :contractId and type = :type and status = :status', 'params' => array('projectId' => $file->project_id, 'contractId' => $file->contract_id, 'type' => ConstantMap::ELECTRON_SIGN_CONTRACT_FILE, 'status' => ContractFile::STATUS_CHECKING)));
                                $eSignContractFile = ContractFileService::getSpecialContractFileInfo($file, ConstantMap::ELECTRON_SIGN_CONTRACT_FILE, ContractFile::STATUS_CHECKING);
                                $res[$key]['files'][$k]['esign_file_url'] = !empty($eSignContractFile->file_url) ? $eSignContractFile->file_url : '';
                                $res[$key]['files'][$k]['esign_file_name'] = !empty($eSignContractFile->name) ? $eSignContractFile->name : '';
                                $res[$key]['files'][$k]['esign_file_id'] = !empty($eSignContractFile->file_id) ? $eSignContractFile->file_id : 0;
                            }
                        }
                    }
                } else {
                    if ($this->moduleType == ConstantMap::FINAL_CONTRACT_MODULE) {
                        $res[$key]['files'][0]['file_id'] = IDService::getContractFileId();
                        $res[$key]['files'][0]['project_id'] = $row['project_id'];
                        $res[$key]['files'][0]['contract_id'] = $row['contract_id'];
                        $res[$key]['files'][0]['is_main'] = ConstantMap::CONTRACT_MAIN;
                        $res[$key]['files'][0]['type'] = ConstantMap::FINAL_CONTRACT_FILE;
                        $res[$key]['files'][0]['category'] = $row['category'];
                        $res[$key]['files'][0]['code'] = $row['contract_code'];
                    } else {
                        $preModuleName = $this->moduleType == ConstantMap::ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE ? '合同' : ($this->moduleType == ConstantMap::PAPER_DOUBLE_SIGN_CONTRACT_MODULE ? '电子双签合同' : '');
                        $this->renderError(BusinessError::outputError(OilError::$CONTRACT_FILE_UPLOAD_NOT_FINISH, array('pre_module_name' => $preModuleName)));
                    }
                }
            }
        } else {
            $this->renderError(BusinessError::outputError(OilError::$PROJECT_NOT_HAVE_CONTRACT));
        }

        $data = array(ConstantMap::BUY_TYPE => array(), ConstantMap::SALE_TYPE => array());
        if (!empty($res)) {
            foreach ($res as $val) {
                if ($val['type'] == ConstantMap::BUY_TYPE) {
                    array_push($data[ConstantMap::BUY_TYPE], $val);
                } else {
                    array_push($data[ConstantMap::SALE_TYPE], $val);
                }
            }
        }

        $this->pageTitle = $moduleName . '上传';

        return array('contracts' => $data, 'projectInfo' => $projectInfo);
    }

    public function actionDetail() {
        $project_id = Mod::app()->request->getParam('id');
        if (!ContractFileService::checkIsCanViewDetail($project_id)) {
            $this->renderError(BusinessError::outputError(OilError::$CONTRACT_FILE_VIEW_DETAIL_NOT_ALLOW));
        }
        $contractData = $this->getContractFileData();
        $contracts = $contractData['contracts'];
        $projectInfo = $contractData['projectInfo'];
        if (Utility::isNotEmpty($contracts)) {
            foreach ($contracts as $key => $row) {
                if (Utility::isNotEmpty($row)) {
                    foreach ($row as $index => $r) {
                        if (Utility::isNotEmpty($r['files'])) {
                            foreach ($r['files'] as $k => $v) {
                                //对应电子双签合同附件信息
                                $eSignContractFile = ContractFileService::getSpecialContractFileInfo($v, ConstantMap::ELECTRON_SIGN_CONTRACT_FILE, ContractFile::STATUS_CHECKING);
                                $contracts[$key][$index]['files'][$k]['esign_file_url'] = !empty($eSignContractFile->file_url) ? $eSignContractFile->file_url : '';
                                $contracts[$key][$index]['files'][$k]['esign_file_name'] = !empty($eSignContractFile->name) ? $eSignContractFile->name : '';
                                $contracts[$key][$index]['files'][$k]['esign_file_id'] = !empty($eSignContractFile->file_id) ? $eSignContractFile->file_id : 0;

                                //对应纸质双签合同附件信息
                                $eSignContractFile = ContractFileService::getSpecialContractFileInfo($v, ConstantMap::PAPER_SIGN_CONTRACT_FILE, ContractFile::STATUS_CHECKING);
                                $contracts[$key][$index]['files'][$k]['psign_file_url'] = !empty($eSignContractFile->file_url) ? $eSignContractFile->file_url : '';
                                $contracts[$key][$index]['files'][$k]['psign_file_name'] = !empty($eSignContractFile->name) ? $eSignContractFile->name : '';
                                $contracts[$key][$index]['files'][$k]['psign_file_id'] = !empty($eSignContractFile->file_id) ? $eSignContractFile->file_id : 0;
                            }
                        }
                    }
                }
            }
        }

        $this->pageTitle = '查看合同上传信息';
        $this->render($this->detailView, array('contracts' => $contracts, 'project' => $projectInfo));
    }

    public function actionGetFileId() {
        $this->returnSuccess(IDService::getContractFileId());
    }
}