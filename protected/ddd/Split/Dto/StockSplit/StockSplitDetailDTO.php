<?php

/**
 * 出入库拆分详情
 */
namespace ddd\Split\Dto\StockSplit;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\DIService;
use ddd\Split\Domain\Model\ICheckLog;
use ddd\Split\Dto\CheckLogDTO;
use FlowService;

class StockSplitDetailDTO extends BaseDTO{

    public $apply_id = 0;

    public $bill_id = 0;

    public $bill_code = '';

    public $remark = '';

    public $attachments = [];

    public $logs = [];

    public function fromEntity(BaseEntity $entity){
        $this->setAttributes($entity->getAttributes());

        $checkLogs = DIService::getRepository(ICheckLog::class)->findAllByObjIdAndBusinessId($entity->apply_id, FlowService::BUSINESS_STOCK_SPLIT_CHECK);

        $this->logs = [];
        foreach($checkLogs as & $log){
            $log_dto =new CheckLogDTO();
            $log_dto->fromEntity($log);
            $this->logs[] = $log_dto;
        }

        foreach($entity->getFiles() as & $file){
            $this->attachments[] = $file;
        }


    }

}
