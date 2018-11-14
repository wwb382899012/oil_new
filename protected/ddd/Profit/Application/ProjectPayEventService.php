<?php
/**
 * User: liyu
 * Date: 2018/8/9
 * Time: 17:58
 * Desc: PayReceiveEventService.php
 */

namespace ddd\Profit\Application;


use ddd\Common\Application\TransactionService;
use ddd\domain\entity\value\Price;
use ddd\infrastructure\DIService;
use ddd\Profit\Domain\Model\Payment\ProjectPayAmount;
use ddd\Profit\Domain\Model\Profit\IProjectProfitRepository;
use ddd\Profit\Domain\Model\Profit\ProjectProfit;
use ddd\Profit\Repository\ProjectRepository;

class ProjectPayEventService extends TransactionService
{

    /**
     * @desc 项目 下付款实付完成
     * @param $projectId 项目ID
     * @param $paymentId 付款ID
     * @return mixed|string|void
     * @throws \Exception
     */
    public function onPayConfirm($projectId, $paymentId) {
        \Mod::log('项目下付款实付完成；onPayConfirm projectId:' . $projectId . ';paymentId:' . $paymentId);
        if (empty($paymentId) || empty($paymentId))
            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);
        $entity = ProjectPayAmount::create($projectId, $paymentId);
        try {
            return $entity->payConfirm();
        } catch (\Exception $e) {
            throw $e;
        }
    }

//    /**
//     * @desc 修复项目下 付款实付利润
//     */
//    public function projectProfitDataRepair($projectId, $paymentId) {
//        if (empty($paymentId) || empty($paymentId))
//            return \BusinessError::outputError(\OilError::$PARAMS_PASS_ERROR);
//        $entity = ProjectPayAmount::create($projectId, $paymentId);
//        $amount = $entity->getPayConfirmAmount();
//        $projectProfit = DIService::getRepository(IProjectProfitRepository::class)->findByProjectId($entity->project_id);
//        if (!empty($projectProfit)) {
//            $projectProfit->pay_amount = new Price($projectProfit->pay_amount + $amount);
//        } else {
//            $project = ProjectRepository::repository()->findByPk($entity->project_id);
//            $projectProfit = ProjectProfit::create($project);
//            $projectProfit->pay_amount = new Price($amount);
//        }
//        DIService::getRepository(IProjectProfitRepository::class)->store($projectProfit);
//    }
}