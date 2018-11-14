<?php
/**
 * User: liyu
 * Date: 2018/8/9
 * Time: 17:33
 * Desc: PayConfirmEventHandlerService.php
 */

namespace ddd\Profit\Domain\Service\Payment;


use ddd\domain\entity\value\Price;
use ddd\infrastructure\DIService;
use ddd\Profit\Domain\Model\Payment\IProjectPaymentRepository;
use ddd\Profit\Domain\Model\Payment\ProjectPayConfirmEvent;
use ddd\Profit\Domain\Model\Payment\ProjectPayment;
use ddd\Profit\Domain\Model\Profit\IProjectProfitRepository;
use ddd\Profit\Domain\Model\Profit\ProjectProfit;
use ddd\Profit\Repository\ProjectRepository;

class ProjectPayConfirmEventHandlerService
{
    /**
     * @desc 项目下付款实付成功
     * @param ProjectPayConfirmEvent $event
     * @throws \Exception
     */
    public function onProjectPayConfirm(ProjectPayConfirmEvent $event) {
        $entity = $event->sender;
        $amount = $entity->getPayConfirmAmount();
        $currency = 0;
        $projectPayment = DIService::getRepository(IProjectPaymentRepository::class)->findByProjectId($entity->project_id);
        //项目下的杂费miscellaneous_fee
        $miscellaneousFee = $entity->getPayConfirmAmount(true);
        if (!empty($projectPayment)) {
            $projectPayment->pay_amount = new Price($projectPayment->pay_amount->price + $amount);
            $projectPayment->miscellaneous_fee = new Price($projectPayment->miscellaneous_fee->price + $miscellaneousFee);
        } else {
            $project = ProjectRepository::repository()->findByPk($entity->project_id);
            $projectPayment = ProjectPayment::create($project);
            $projectPayment->pay_amount = new Price($amount);
            $projectPayment->miscellaneous_fee = new Price($miscellaneousFee);
        }
        DIService::getRepository(IProjectPaymentRepository::class)->store($projectPayment);
    }
}