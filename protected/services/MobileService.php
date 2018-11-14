<?php
/**
 * Created by PhpStorm.
 * User: shengwu
 * Date: 2018/2/2
 * Time: 18:41
 */
class MobileService {

    /**
     * @desc 获取为移动端格式化的电子双签合同
     * @param $files
     * @return array
     */
    public static function getESignContractFiles($files) {
        $contractFiles = array();
        if (empty($files)) return $contractFiles;

        foreach ($files as $k => $v) {
            //对应电子双签合同附件信息
            $eSignContractFile = ContractFileService::getSpecialContractFileInfo($v, ConstantMap::ELECTRON_SIGN_CONTRACT_FILE, ContractFile::STATUS_CHECKING);
            if (empty($eSignContractFile->file_id)) continue;
            $item['download_path'] = !empty($eSignContractFile->file_id) ? '/electronSign/getFile/?id='.$eSignContractFile->file_id.'&fileName='.$eSignContractFile->name : '';
            $item['file_url'] = !empty($eSignContractFile->file_url) ? $eSignContractFile->file_url : '';
            $item['name'] = !empty($eSignContractFile->name) ? $eSignContractFile->name : '';
            $item['esign_file_id'] = !empty($eSignContractFile->file_id) ? $eSignContractFile->file_id : 0;
            $contractFiles[$item['esign_file_id']] = $item;
        }
        $contractFiles = array_values($contractFiles);
        return $contractFiles;
    }
}