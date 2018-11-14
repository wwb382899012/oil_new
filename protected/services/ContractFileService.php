<?php

/**
 * Desc: 合同文本
 * User: susiehuang
 * Date: 2018/2/6 0009
 * Time: 16:03
 */
class ContractFileService {

    /**
     * @desc 检查是否可查看合同上传详情
     * @param int $project_id
     * @param int $type
     * @return bool
     */
    public static function checkIsCanViewDetail($project_id, $type = ConstantMap::FINAL_CONTRACT_FILE) {
        if(Utility::checkQueryId($project_id)) {
            /*$sql = 'select project_id from t_project where project_id = ' . $project_id . ' and status >= ' . Project::STATUS_SUBMIT . '
                    and exists(select contract_id from t_contract where project_id = ' . $project_id . ' and status >= ' . Contract::STATUS_BUSINESS_CHECKED . ')';*/
            $sql = 'select * from t_contract_file cf 
                    left join t_project p on p.project_id = cf.project_id 
                    where p.status>=' . Project::STATUS_SUBMIT . ' and cf.project_id=' . $project_id . ' and cf.type=' . $type . ' and cf.file_url is not null';
            $data = Utility::query($sql);
            if(Utility::isNotEmpty($data)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 插入合同的主合同文本
     * @param $contractId
     * @param $createUserId
     * @param $updateUserId
     * @param $remark
     * @return bool
     */
    public static function insertMainContractFile($contractId, $createUserId = 0, $updateUserId = 0, $remark = '')
    {
        $contract=Contract::model()->findByPk($contractId);
        if(empty($contract))
            return false;

        $file=ContractFile::model()->find("contract_id=".$contractId." and is_main=".ConstantMap::CONTRACT_MAIN." and type=".ConstantMap::FINAL_CONTRACT_FILE);
        if(empty($file))
        {
            $file=new ContractFile();
            $file->file_id = IDService::getContractFileId();
            $file->project_id = $contract->project_id;
            $file->contract_id = $contract['contract_id'];
            $file->is_main = ConstantMap::CONTRACT_MAIN;
            $file->type = ConstantMap::FINAL_CONTRACT_FILE;
            $file->category = $contract['category'];
            $file->code = $contract['contract_code'];
            $file->status = ContractFile::STATUS_NOT_UPLOAD;
            $file->remark = $remark;
            if(!empty($createUserId)) {
                $file->create_user_id = $createUserId;
            }
            if(!empty($updateUserId)) {
                $file->update_user_id = $updateUserId;
            }
            $res=$file->save();
            return $res;
        }
        return true;
    }

    /**
     * @desc 插入最终合同信息
     * @param int $projectId
     */
    public static function insertFinalFilesByProject($projectId) {
        if (Utility::checkQueryId($projectId)) {
            $contracts = Contract::model()->findAll('project_id = :projectId and status >= :status', array('projectId' => $projectId, 'status' => Contract::STATUS_BUSINESS_CHECKED));
            if (Utility::isNotEmpty($contracts)) {
                foreach ($contracts as $key => $row) {
                    $contractFile = ContractFile::model()->find(
                        'project_id = :projectId and contract_id = :contractId and is_main =:isMain and type = :type',
                        array('projectId' => $projectId, 'contractId' => $row['contract_id'], 'isMain' => ConstantMap::CONTRACT_MAIN, 'type' => ConstantMap::FINAL_CONTRACT_FILE)
                    );
                    if(empty($contractFile->file_id)) {
                        $contractFile = new ContractFile();
                        $contractFile->file_id = IDService::getContractFileId();
                        $contractFile->project_id = $projectId;
                        $contractFile->contract_id = $row['contract_id'];
                        $contractFile->is_main = ConstantMap::CONTRACT_MAIN;
                        $contractFile->type = ConstantMap::FINAL_CONTRACT_FILE;
                        $contractFile->category = $row['category'];
                        $contractFile->code = $row['contract_code'];
                        $contractFile->status = ContractFile::STATUS_NOT_UPLOAD;

                        $contractFile->save();
                    }
                }
            }
        }
    }

    /**
     * @desc 插入指定类型合同信息
     * @param int $file_id
     * @param int $file_type
     */
    public static function insertSignFileByFileId($file_id, $file_type = ConstantMap::ELECTRON_SIGN_CONTRACT_FILE) {
        if (Utility::checkQueryId($file_id)) {
            $type = ConstantMap::FINAL_CONTRACT_FILE;
            $status = ContractFile::STATUS_CHECK_PASS;
            if ($file_type == ConstantMap::PAPER_SIGN_CONTRACT_FILE) {
                $type = ConstantMap::ELECTRON_SIGN_CONTRACT_FILE;
                $status = ContractFile::STATUS_CHECKING;
            }
            $contractFile = ContractFile::model()->findByPk($file_id, 'type = ' . $type . ' and status = ' . $status);
            if (!empty($contractFile->file_id)) {
                $signContractFile = new ContractFile();
                $signContractFile->file_id = IDService::getContractFileId();
                $signContractFile->project_id = $contractFile->project_id;
                $signContractFile->contract_id = $contractFile->contract_id;
                $signContractFile->is_main = $contractFile->is_main;
                $signContractFile->type = $file_type;
                $signContractFile->category = $contractFile->category;
                $signContractFile->version_type = $contractFile->version_type;
                $signContractFile->code = $contractFile->code;
                $signContractFile->code_out = $contractFile->code_out;
                $signContractFile->status = ContractFile::STATUS_NOT_UPLOAD;

                $signContractFile->save();
            }
        }
    }

    /**
     * @desc 获取指定合同文本附件信息
     * @param object $obj
     * @param int $type
     * @param int $status
     * @return object
     */
    public static function getSpecialContractFileInfo($obj, $type, $status) {
        return ContractFile::model()->find(
            array(
                'select' => 'file_id,file_url,name,category,is_main',
                'condition' => 'project_id = :projectId and contract_id = :contractId and is_main = :isMain and type = :type and category = :category and version_type = :versionType and code = :code and code_out = :codeout and status = :status',
                'params' => array(
                    'projectId' => $obj['project_id'],
                    'contractId' => $obj['contract_id'],
                    'isMain' => $obj['is_main'],
                    'type' => $type,
                    'status' => $status,
                    'category' => $obj['category'],
                    'versionType' => $obj['version_type'],
                    'code' => $obj['code'],
                    'codeout' => $obj['code_out']
                )));
    }

    /**
     * @desc 根据合同文本信息更新合同状态
     * @param int $fileId
     */
    public static function updateContractStatusByFileId($fileId) {
        if (Utility::checkQueryId($fileId) && $fileId > 0) {
            $contractFile = ContractFile::model()->findByPk($fileId);
            if (!empty($contractFile)) {
                $contract = Contract::model()->findByPk($contractFile->contract_id);
                if (!empty($contract) && $contractFile->is_main == 1) {
                    if ($contractFile->type == ConstantMap::FINAL_CONTRACT_MODULE) {
                        if ($contractFile->status == ContractFile::STATUS_BACK) {
                            $contract->status = Contract::STATUS_FILE_BACK;
                            $contract->status_time = Utility::getDateTime();
                        } elseif ($contractFile->status == ContractFile::STATUS_CHECK_PASS) {
                            $contract->status = Contract::STATUS_FILE_UPLOAD;
                            $contract->status_time = Utility::getDateTime();
                        } elseif ($contractFile->status == ContractFile::STATUS_CHECKING) {
                            $contract->status = Contract::STATUS_FILE_SUBMIT;
                            $contract->status_time = Utility::getDateTime();
                        }
                    } elseif ($contractFile->type == ConstantMap::ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE) {
                        if($contractFile->status == ContractFile::STATUS_CHECKING) {
                            $contract->status = Contract::STATUS_FILE_SIGNED;
                            $contract->status_time = Utility::getDateTime();
                        }
                    } elseif ($contractFile->type == ConstantMap::PAPER_DOUBLE_SIGN_CONTRACT_MODULE) {
                        if($contractFile->status == ContractFile::STATUS_CHECKING) {
                            $contract->status = Contract::STATUS_FILE_FILED;
                            $contract->status_time = Utility::getDateTime();
                        }
                    }
                    $contract->save();
                }
            }
        }
    }
}