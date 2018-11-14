<?php

/**
 * @Name            拆分服务
 * @DateTime        2018年5月30日 星期三 19:33:22
 * @Author          youyi000
 */

namespace ddd\Split\Domain\Service;

use ddd\Common\Domain\BaseService;
use ddd\domain\entity\contract\ContractGoods;
use ddd\domain\enum\MainEnum;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\infrastructure\MathUtility;
use ddd\Split\Domain\Model\Contract\ContractEnum;
use ddd\Split\Domain\Model\Contract\IContractRepository;
use ddd\Split\Domain\Model\ContractSplit\ContractSplit;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApply;
use ddd\domain\entity\contract\Contract;
use ddd\Split\Domain\Model\ContractSplit\IContractSplitApplyRepository;
use ddd\Split\Domain\Model\ContractSplit\IContractSplitRepository;
use ddd\Split\Domain\Model\SplitEnum;
use ddd\Split\Domain\Model\Stock\IStockInRepository;
use ddd\Split\Domain\Model\Stock\IStockOutRepository;
use ddd\Split\Domain\Model\StockSplit\StockSplitApply;
use ddd\Split\Domain\Model\StockSplit\StockSplitEnum;

class SplitService extends BaseService
{

    /**
     * 是否跳过商品为0的明细生成,拆分合同
     */
    const IS_SKIP_ZERO_QUANTITY_GOODS_REALITY = false;

    /**
     * 是否跳过商品为0的明细生成,虚拟单
     */
    const IS_SKIP_ZERO_QUANTITY_GOODS_VIRTUAL = false;

    /**
     * 取第二单位的单位换算值
     */
    const IS_DETAIL_SUB_UNIT_RATE = false;

    public function handleContractSplitApplyAfterCheckPassed(Contract $originContractEntity, ContractSplitApply $contractSplitApplyEntity) {
        $this->generateContracts($originContractEntity, $contractSplitApplyEntity);

        //设置为已经拆分
        foreach ($contractSplitApplyEntity->getEffectiveStockSplitBillIds() as $bill_id) {
            $this->setOriginalStockBillHasBeenSplit($contractSplitApplyEntity->type, $bill_id);
        }

        $this->generateVirtualNoticeOrderAndStockBillsForOriginContract($contractSplitApplyEntity);
    }

    /**
     * 处理拆分的合同，业务审核通过之后
     * @param \Contract $splitContract
     * @throws ZException
     * @throws ZModelSaveFalseException
     * @throws \CException
     */
    public function handleSplitContractAfterCheckPassed(\Contract $splitContract): void {
        //不是拆分合同直接返回
        $is_split_contract = (0 < $splitContract->original_id)
            && (ContractEnum::SPLIT_TYPE_SPLIT == $splitContract->split_type)
            && \Contract::STATUS_BUSINESS_CHECKED <= $splitContract->status;
        if (!$is_split_contract) {
            return;
        }

        //获取拆分合同对应的拆分记录
        $currContractSplitEntity = DIService::getRepository(IContractSplitRepository::class)->findByNewContractId($splitContract->contract_id);
        if (empty($currContractSplitEntity)) {
            throw new \CException('拆分合同对应的拆分记录未找到！');
            return;
        }

        //当前拆分合同记录是否为待审核通过
        if (!$currContractSplitEntity->isWaitConfirm()) {
            return;
        }
        //设置当前拆分合同记录为业务审核通过
        $currContractSplitEntity->setSplitContractEffective();

        //获取新的拆分申请
        $contractSplitApplyEntity = DIService::getRepository(IContractSplitApplyRepository::class)->findByPk($currContractSplitEntity->apply_id);
        if (empty($contractSplitApplyEntity)) {
            throw new \CException('拆分合同对应的拆分申请记录未找到！');
            return;
        }

        if (!$contractSplitApplyEntity->isCheckPass()) {
            throw new \CException('拆分合同对应的拆分申请状态不一致！');
            return;
        }

        $split_total = count($contractSplitApplyEntity->getContractSplits());
        foreach ($contractSplitApplyEntity->getContractSplits() as & $contractSplitEntity) {
            if (!$contractSplitEntity->isWaitConfirm()) {
                $split_total--;
            }
        }
        //设置该拆分合同对应的申请记录为可以进行出入库拆分
        if (0 == $split_total) {
            $contractSplitApplyEntity->setIsCanStockSplit();
        }

        //生成新的提单、发货单,出、入库单
        $this->generateNoticeOrderAndStockBillsForSplitContract($contractSplitApplyEntity, $currContractSplitEntity);
    }

    /**
     * 生成拆分合同下的虚拟发货、提单，出、入库单
     * @param ContractSplitApply $contractSplitApplyEntity
     * @param ContractSplit $currContractSplitEntity
     * @throws ZException
     * @throws ZModelSaveFalseException
     */
    private function generateNoticeOrderAndStockBillsForSplitContract(ContractSplitApply $contractSplitApplyEntity, ContractSplit $currContractSplitEntity) {
        $innerClass = $this->getDtoClassForSplitContract($contractSplitApplyEntity->isBuyContract(), $currContractSplitEntity->split_id, $contractSplitApplyEntity);

        //没有的出入库拆分数据
        $stock_split_detail_entities = $innerClass->getStockSplitDetailEntities();
        if (\Utility::isEmpty($stock_split_detail_entities)) {
            return;
        }

        //为0的商品数量不保存
        $is_skip_zero_quantity = true;

        $new_notice_order_models = [];
        //获取新合同下需要新建的虚拟通知单的商品数量数组
        $virtual_notice_order_goods_quantities = $innerClass->getNewVirtualNoticeOrderGoodsQuantities();
        //生成入库通知单、发货单
        foreach ($virtual_notice_order_goods_quantities as $notice_order_id => & $new_virtual_stock_bill_goods_quantities) {
            //获取原合同下的原通知单
            $origin_stock_notice_model = $innerClass->getOriginNoticeOrderModelByNoticeOrderId($notice_order_id);

            if ($contractSplitApplyEntity->isBuyContract()) {
                $new_stock_notice_model = $this->addStockNoticeOrderAndDetail($currContractSplitEntity->new_contract_id, $origin_stock_notice_model, $new_virtual_stock_bill_goods_quantities, $is_skip_zero_quantity);
            } else {
                $new_stock_notice_model = $this->addDeliveryOrderAndDetail($currContractSplitEntity->new_contract_id, $origin_stock_notice_model, $new_virtual_stock_bill_goods_quantities, $is_skip_zero_quantity);
            }
            $new_notice_order_models[$notice_order_id] = $new_stock_notice_model;
        }

        foreach ($stock_split_detail_entities as $bill_id => & $stockSplitDetailEntity) {
            $notice_order_id = $innerClass->getNoticeOrderIdByBillId($bill_id);

            $new_stock_notice_model = $new_notice_order_models[(string)$notice_order_id];

            //获取对应的出、入库单明细商品数量，用于生成新的虚拟出、入库单
            $new_virtual_stock_bill_goods_quantities = $innerClass->getNewVirtualStockBillGoodsQuantitiesByBillId($bill_id);

            //获取原合同下的原出、入库单
            $origin_stock_bill_model = $innerClass->getStockBillModelByBillId($bill_id);

            //生成出、出库单,明细
            if ($contractSplitApplyEntity->isBuyContract()) {
                $model = $this->addStockInAndDetail($origin_stock_bill_model, $new_virtual_stock_bill_goods_quantities, $new_stock_notice_model, $is_skip_zero_quantity);
            } else {
                $model = $this->addStockOutAndDetail($origin_stock_bill_model, $new_virtual_stock_bill_goods_quantities, $new_stock_notice_model, $is_skip_zero_quantity);
            }

            //保存出、入库拆分生成的入库单id
            $stockSplitDetailEntity->saveNewBillId($model->getPrimaryKey());
        }
    }

    /**
     * 生成出、入库单，明细
     * 1.出入库拆分目前不能拆分原合同下虚拟的出、入库单，只能拆分未拆分过的原合同下的出、入库单
     * @param StockSplitApply $stockSplitApplyEntity
     * @throws \Exception
     */
    public function handleStockSplitApplyAfterCheckPassed(StockSplitApply & $stockSplitApplyEntity): void {
        $is_stock_in = $stockSplitApplyEntity->isStockInSplit();

        if (\Utility::isEmpty($stockSplitApplyEntity->getDetails())) {
            throw new ZException(($stockSplitApplyEntity->isStockInSplit() ? '入' : '出') . '库拆分申请信息异常!');
        }

        //原合同的出、入库单
        if ($is_stock_in) {
            $origin_stock_bill_model = \StockIn::model()->with('details', 'notice')->findByPk($stockSplitApplyEntity->bill_id);
        } else {
            $origin_stock_bill_model = \StockOutOrder::model()->with('details', 'deliveryOrder')->findByPk($stockSplitApplyEntity->bill_id);
        }

        //为0的商品数量不保存
        $is_skip_zero_quantity = true;

        //新建给原合同下的虚拟通知单，需要扣除的商品数量
        $need_deduct_quantities = [];

        foreach ($stockSplitApplyEntity->getDetails() as & $stockSplitDetailEntity) {
            //出库单明细
            $goods_quantities = [];
            foreach ($stockSplitDetailEntity->getGoodsItems() as & $tradGoods) {
                $goods_quantities[$tradGoods->goods_id] = $tradGoods->quantity->quantity;

                $last_quantity = $need_deduct_quantities[$tradGoods->goods_id] ?? 0;
                $need_deduct_quantities[$tradGoods->goods_id] = MathUtility::add($last_quantity, $tradGoods->quantity->quantity);
            }

            //生成拆分合同的发货单
            if ($is_stock_in) {
                $split_contract_stock_notice_model = $this->addStockNoticeOrderAndDetail($stockSplitDetailEntity->contract_id, $origin_stock_bill_model->notice, $goods_quantities, $is_skip_zero_quantity);
            } else {
                $split_contract_stock_notice_model = $this->addDeliveryOrderAndDetail($stockSplitDetailEntity->contract_id, $origin_stock_bill_model->deliveryOrder, $goods_quantities, $is_skip_zero_quantity);
            }

            if ($is_stock_in) {
                $stock_bill_model = $this->addStockInAndDetail($origin_stock_bill_model, $goods_quantities, $split_contract_stock_notice_model, $is_skip_zero_quantity);
            } else {
                $stock_bill_model = $this->addStockOutAndDetail($origin_stock_bill_model, $goods_quantities, $split_contract_stock_notice_model, $is_skip_zero_quantity);
            }

            //设置新的bill_id
            $stockSplitDetailEntity->type = $stockSplitApplyEntity->type;
            $stockSplitDetailEntity->new_bill_id = $stock_bill_model->getPrimaryKey();
        }

        //保存上面设置好的新bill_id
        $stockSplitApplyEntity->getStockSplitApplyRepository()->store($stockSplitApplyEntity);

        //出入库拆分对应的原出、入库单，生成对应的虚拟出、入库单
        $this->generateVirtualStockBillAndDetailForOriginContract($stockSplitApplyEntity, $origin_stock_bill_model, $need_deduct_quantities);
    }

    /**
     * 生成合同
     * @param Contract $originContractEntity
     * @param ContractSplitApply $splitApply
     * @throws ZModelSaveFalseException
     * @throws \Exception
     */
    private function generateContracts(Contract $originContractEntity, ContractSplitApply $splitApply): void {
        $contractSplitEntities = $splitApply->getContractSplits();
        if (\Utility::isEmpty($contractSplitEntities)) {
            return;
        }

        if ($originContractEntity->split_type == ContractEnum::SPLIT_TYPE_NOT_SPLIT) {//首次拆分
            $this->backupOriginalContractGoods($originContractEntity->goods_items);
            $originContractEntity->setSplit();//设置合同为已拆分
        }

        foreach ($contractSplitEntities as & $contractSplitEntity) {
            $values = $originContractEntity->getAttributes();
            $values = \Utility::unsetCommonIgnoreAttributes($values);
            $values['partner_id'] = $contractSplitEntity->partner_id;
            $values['split_type'] = ContractEnum::SPLIT_TYPE_SPLIT;
            $values['original_id'] = $values['contract_id'];
            $values['is_main'] = MainEnum::IS_NOT_MAIN;
            unset($values['contract_id']);
            unset($values['goods_items']);

            //采购合同
            if (ContractEnum::BUY_CONTRACT == $originContractEntity->type) {
                $newContractEntity = new \ddd\domain\entity\contract\BuyContract();
            } else {
                $newContractEntity = new \ddd\domain\entity\contract\SellContract();
            }
            $newContractEntity->setAttributes($values);
            $newContractEntity->goods_items = [];
            $newContractEntity->is_main = MainEnum::IS_NOT_MAIN;
            $newContractEntity->split_type = ContractEnum::SPLIT_TYPE_SPLIT; //标记为已拆分,业务审核需要用到
            $newContractEntity->contract_code = ''; //新的合同不能有合同编号
            $newContractEntity->status = \Contract::STATUS_TEMP_SAVE; //并且待商务确认
            $newContractEntity->remark = '平移生成';
            $newContractEntity->relation_contract_id = 0;//去掉关联id

            //添加合同交易明细
            if (\Utility::isNotEmpty($contractSplitEntity->goods_items)) {
                foreach ($contractSplitEntity->goods_items as $key => & $tradeGoodsEntity) {


                    if (!isset($originContractEntity->goods_items[$key])) {
                        throw new \CException('原合同不存在该平移商品');
                    }

                    $left_quantity = $originContractEntity->goods_items[$tradeGoodsEntity->goods_id]->quantity->quantity;
                    $right_quantity = $tradeGoodsEntity->quantity->quantity;

                    if (MathUtility::less($left_quantity, $right_quantity)) {
                        throw new \CException("商品" . $tradeGoodsEntity->goods_id . "拆分数量超出");
                    }

                    $item = $originContractEntity->goods_items[$tradeGoodsEntity->goods_id];
                    $goods = new ContractGoods();
                    $goods->setAttributes($item->getAttributes());
                    $goods->quantity = $tradeGoodsEntity->quantity;
                    $goods->amount = round($goods->quantity->quantity * $goods->price);
                    $goods->amount_cny = round($goods->amount * $originContractEntity->exchange_rate);
                    $newContractEntity->addGoodsItem($goods);

                    $originContractEntity->goods_items[$tradeGoodsEntity->goods_id]->quantity->quantity = MathUtility::sub($left_quantity, $right_quantity);
                    $originGoodsItems = $originContractEntity->goods_items[$tradeGoodsEntity->goods_id];
                    $originContractEntity->goods_items[$tradeGoodsEntity->goods_id]->amount = $originGoodsItems->quantity->quantity * $originGoodsItems->price;
                    $originContractEntity->goods_items[$tradeGoodsEntity->goods_id]->amount_cny = $originContractEntity->goods_items[$tradeGoodsEntity->goods_id]->amount * $originContractEntity->exchange_rate;
                }
            }

            //持久化到数据库
            $newContract = DIService::getRepository(IContractRepository::class)->store($newContractEntity);

            //更新合同拆分生成的新合同id
            $contractSplitEntity->saveNewContractId($newContract->getId());

            //添加合同条款
            if (\Utility::isNotEmpty($originContractEntity->contract_items)) {
                $content = [];
                foreach ($originContractEntity->contract_items as $item) {
                    $contentItem = [
                        'key' => $item->key,
                        'name' => $item->name,
                        'display_value' => $item->content,
                        'value' => $item->content_type,
                    ];
                    $content[] = $contentItem;
                }
                $ceModel = new \ContractExtra();
                $ceModel->contract_id = $newContract->getId();
                $ceModel->project_id = $originContractEntity->project_id;
                $ceModel->content = json_encode($content);
                if (!$ceModel->save()) {
                    throw new ZModelSaveFalseException($ceModel);
                }
            }

            //生成待商务确认的合同
            \ContractService::generateContractGroup($newContractEntity);

            //发起通知
            $new_contract_model = \Contract::model()->findByPk($newContractEntity->contract_id);
            \TaskService::addTasks(\Action::ACTION_CONTRACT_SPLIT_BUSINESS_CONFIRM, $newContractEntity->contract_id,
                \ActionService::getActionRoleIds(\Action::ACTION_10), 0, $originContractEntity->corporation_id, [
                    "project_id" => $newContractEntity->project_id,
                    "contract_id" => $newContractEntity->contract_id,
                    'projectCode' => $new_contract_model->project->project_code,
                    'partnerName' => $new_contract_model->partner->name,
                ]);
        }

        DIService::getRepository(IContractRepository::class)->store($originContractEntity);
    }

    /**
     * 生成虚拟的入库单,扣除虚拟入库单商品明细数量
     * @param ContractSplitApply $contractSplitApplyEntity
     * @throws ZException
     * @throws ZModelSaveFalseException
     * @throws \CException
     */
    private function generateVirtualNoticeOrderAndStockBillsForOriginContract(ContractSplitApply $contractSplitApplyEntity) {
        //当前拆分合同记录是否为审核通过
        if (!$contractSplitApplyEntity->isCheckPass()) {
            throw new \CException('拆分合同对应的拆分申请状态不一致！');
            return;
        }

        //没有的拆分数据
        $effectiveStockSplits = $contractSplitApplyEntity->getEffectiveStockSplits();
        $contractSplitEntities = $contractSplitApplyEntity->getContractSplits();
        if (\Utility::isEmpty($effectiveStockSplits) || \Utility::isEmpty($contractSplitEntities)) {
            return;
        }

        $innerClass = $this->getDtoClassForContractSplitApply($contractSplitApplyEntity);
        //入库通知单、发货单需要更新的数量,剩余的数量
        $virtual_notice_order_residue_goods_quantities = $innerClass->getVirtualNoticeOrderResidueGoodsQuantities();

        //生成虚拟入库单通知单、发货单
        $new_virtual_notice_order_models = [];
        foreach ($virtual_notice_order_residue_goods_quantities as $notice_order_id => & $goods_quantity) {
            $notice_order_model = $innerClass->getCurrNoticeOrderModel($notice_order_id);
            $new_virtual_notice_order_models[(string)$notice_order_id] = $this->addOrUpdateVirtualNoticeOrderForOriginContract($contractSplitApplyEntity, $notice_order_model, $goods_quantity);
        }

        //生成虚拟出、入库单，明细
        foreach ($contractSplitApplyEntity->getEffectiveStockSplitBillIds() as $bill_id) {
            $notice_order_id = $innerClass->getNoticeOrderIdByBillId($bill_id);
            $virtual_notice_order_model = $new_virtual_notice_order_models[(string)$notice_order_id];

            //需要扣除的商品数量
            $need_deduct_quantities = $innerClass->getStockBillNeedDeductQuantitiesByBillId($bill_id);

            //当前被拆分的出、入库单
            $curr_stock_bill_model = $innerClass->getLeftStockBillModelByBillId($bill_id);

            //生成原合同下的虚拟出、入库单
            $this->generateVirtualStockBillForOriginContract($contractSplitApplyEntity->isBuyContract(), $virtual_notice_order_model, $curr_stock_bill_model, $need_deduct_quantities);
        }
    }

    private function getDtoClassForContractSplitApply(ContractSplitApply $contractSplitApplyEntity) {
        return new class($contractSplitApplyEntity)
        {

            private $is_init = false;

            private $contractSplitApplyEntity;

            private $bill_id_and_notice_ids = [];
            private $stock_bill_need_deduct_quantities = [];
            private $notice_order_need_deduct_goods_quantities = [];
            //被拆分的入库单模型数组
            private $curr_stock_bill_models = [];
            //原始的通知单模型数组
            private $origin_notice_order_models = [];
            private $virtual_notice_order_residue_goods_quantities = [];

            public function __construct(ContractSplitApply $contractSplitApplyEntity) {
                $this->contractSplitApplyEntity = $contractSplitApplyEntity;

                $this->init();
            }

            private function init() {
                if ($this->is_init) {
                    return;
                }

                $this->is_init = true;

                $bill_id_and_notice_ids = [];
                //每一个出、入库单，被拆分的商品数量(需要扣除的商品数量)
                $stock_bill_need_deduct_quantities = [];
                //每一个入库通知、发货单，被拆分的商品数量总和(需要扣除的总和)
                $notice_order_need_deduct_goods_quantities = [];
                //原合同下的入库通知、发货单，原始的商品数量总和
                $origin_notice_order_goods_quantities = [];
                //被拆分的入库单模型数组
                $curr_stock_bill_models = [];
                //当前出入库单对应的通知单模型数组
                $curr_origin_notice_order_models = [];

                //生成虚拟出、入库单，明细
                foreach ($this->contractSplitApplyEntity->getEffectiveStockSplitBillIds() as $bill_id) {
                    if ($this->contractSplitApplyEntity->isBuyContract()) {
                        $origin_stock_bill_model = \StockIn::model()->with('details', 'notice')->findByPk($bill_id);

                        //当前被拆分的入库单
                        $curr_stock_bill_models[(string)$bill_id] = $origin_stock_bill_model;

                        $origin_notice_order_model = $origin_stock_bill_model->notice;

                        $curr_notice_order_models[(string)$bill_id] = $origin_notice_order_model;

                        $notice_order_id = $origin_notice_order_model->batch_id;
                    } else {
                        $origin_stock_bill_model = \StockOutOrder::model()->with('details', 'deliveryOrder')->findByPk($bill_id);

                        //当前被拆分的出库单
                        $curr_stock_bill_models[(string)$bill_id] = $origin_stock_bill_model;

                        $origin_notice_order_model = $origin_stock_bill_model->deliveryOrder;

                        $notice_order_id = $origin_notice_order_model->order_id;
                    }

                    $curr_origin_notice_order_models[(string)$notice_order_id] = $origin_notice_order_model;

                    //获取当前出、入库单需要扣除的商品数量
                    $need_deduct_quantities = $this->contractSplitApplyEntity->getEffectiveStockSplitGoodsQuantities($bill_id);
                    $stock_bill_need_deduct_quantities[(string)$bill_id] = $need_deduct_quantities;

                    //设置每一个入库通知、发货单，被拆分的商品数量总和(需要扣除的总和)
                    foreach ($need_deduct_quantities as $goods_id => $quantity) {
                        $tmp_quantity = $notice_order_need_deduct_goods_quantities[(string)$notice_order_id][$goods_id] ?? 0;
                        $notice_order_need_deduct_goods_quantities[(string)$notice_order_id][$goods_id] = MathUtility::add($tmp_quantity, $quantity);
                    }

                    //根据原合同的出、入库单，计算一个入库通知、发货单，原始的商品数量总和
                    foreach ($origin_stock_bill_model->details as & $detail_model) {
                        $tmp_quantity = $origin_notice_order_goods_quantities[(string)$notice_order_id][$detail_model->goods_id] ?? 0;
                        $origin_notice_order_goods_quantities[(string)$notice_order_id][$detail_model->goods_id] = MathUtility::add($tmp_quantity, $detail_model->quantity);
                    }

                    $bill_id_and_notice_ids[(string)$bill_id] = $notice_order_id;
                }

                //获取原合同下的虚拟入库单，剩余的商品数量，用于更新虚拟入库通知单、发货单
                //原始的商品数量总和 - 被拆分的商品数量总和(需要扣除的总和) = 剩余的商品数量总额
                $virtual_notice_order_residue_goods_quantities = [];
                foreach ($origin_notice_order_goods_quantities as $notice_order_id => $goods_quantity) {
                    foreach ($goods_quantity as $goods_id => $quantity) {
                        $tmp_quantity = $notice_order_need_deduct_goods_quantities[(string)$notice_order_id][$goods_id] ?? 0;
                        $virtual_notice_order_residue_goods_quantities[(string)$notice_order_id][$goods_id] = MathUtility::sub($quantity, $tmp_quantity);;
                    }
                }

                $this->bill_id_and_notice_ids = $bill_id_and_notice_ids;
                $this->stock_bill_need_deduct_quantities = $stock_bill_need_deduct_quantities;
                $this->notice_order_need_deduct_goods_quantities = $notice_order_need_deduct_goods_quantities;
                //被拆分的入库单模型数组
                $this->curr_stock_bill_models = $curr_stock_bill_models;
                //原始的通知单模型数组
                $this->origin_notice_order_models = $curr_origin_notice_order_models;
                $this->virtual_notice_order_residue_goods_quantities = $virtual_notice_order_residue_goods_quantities;
            }

            /**
             * 被拆分的入库单模型数组
             * @param $billId
             * @return mixed|null
             */
            public function getLeftStockBillModelByBillId($billId) {
                return $this->curr_stock_bill_models[(string)$billId] ?? null;
            }

            /**
             * 被拆分的入库单需要扣减的数据
             * @param $billId
             * @return array|mixed
             */
            public function getStockBillNeedDeductQuantitiesByBillId($billId) {
                return $this->stock_bill_need_deduct_quantities[(string)$billId] ?? [];
            }

            /**
             * 获取当前对应的的入库通知、发货单，第一次拆分为原始的，第二次拆分为虚拟的
             * @param $noticeOrderId
             * @return mixed|null
             */
            public function getCurrNoticeOrderModel($noticeOrderId) {
                return $this->origin_notice_order_models[$noticeOrderId] ?? null;
            }

            /**
             * 获取入库通知、发货单id
             * @param $billId
             * @return mixed|null
             */
            public function getNoticeOrderIdByBillId($billId) {
                return $this->bill_id_and_notice_ids[(string)$billId] ?? null;
            }

            /**
             * 入库通知单、发货单需要更新的数量,剩余的数量
             * @return array
             */
            public function getVirtualNoticeOrderResidueGoodsQuantities() {
                return $this->virtual_notice_order_residue_goods_quantities;
            }

        };
    }


    private function getDtoClassForSplitContract(bool $isBuyContract, $split_id, ContractSplitApply $contractSplitApplyEntity) {
        return new class($isBuyContract, $split_id, $contractSplitApplyEntity)
        {
            private $is_init = false;

            private $isBuyContract = false;
            private $split_id = 0;
            private $contractSplitApplyEntity = null;

            private $stock_split_detail_entities = [];
            private $row_origin_stock_notice_models = [];
            private $new_virtual_notice_order_goods_quantities = [];
            private $row_notice_order_id = [];
            private $row_origin_stock_bill_model = [];
            private $new_stock_bill_goods_quantities = [];

            public function __construct($isBuyContract, $split_id, $contractSplitApplyEntity) {
                $this->isBuyContract = $isBuyContract;
                $this->split_id = $split_id;
                $this->contractSplitApplyEntity = $contractSplitApplyEntity;

                $this->init();
            }

            private function init() {
                if ($this->is_init) {
                    return;
                }
                $this->is_init = true;


                //没有的出入库拆分数据
                $stock_split_detail_entities = $this->contractSplitApplyEntity->getEffectiveStockSplitDetailEntities($this->split_id);
                if (\Utility::isEmpty($stock_split_detail_entities)) {
                    return;
                }

                $row_origin_stock_notice_models = [];
                $new_virtual_notice_order_goods_quantities = [];
                $row_notice_order_id = [];
                $row_origin_stock_bill_model = [];
                $new_stock_bill_goods_quantities = [];

                foreach ($stock_split_detail_entities as $bill_id => & $stockSplitDetailEntity) {
                    if ($this->isBuyContract) {
                        $origin_stock_bill_model = \StockIn::model()->with('details', 'notice')->findByPk($bill_id);
                        $origin_notice_order_model = $origin_stock_bill_model->notice;

                        if (SplitEnum::IS_VIRTUAL == $origin_notice_order_model->is_virtual) {
                            $origin_stock_bill_model = $origin_stock_bill_model->originalOrder;
                            $origin_notice_order_model = $origin_notice_order_model->originalOrder;
                        }

                        $notice_order_id = $origin_notice_order_model->batch_id;
                    } else {
                        $origin_stock_bill_model = \StockOutOrder::model()->with('details', 'deliveryOrder')->findByPk($bill_id);
                        $origin_notice_order_model = $origin_stock_bill_model->deliveryOrder;
                        if (SplitEnum::IS_VIRTUAL == $origin_notice_order_model->is_virtual) {
                            $origin_stock_bill_model = $origin_stock_bill_model->originalOrder;
                            $origin_notice_order_model = $origin_notice_order_model->originalOrder;
                        }

                        $notice_order_id = $origin_notice_order_model->order_id;
                    }


                    $row_origin_stock_bill_model[(string)$bill_id] = $origin_stock_bill_model;
                    $row_notice_order_id[(string)$bill_id] = $notice_order_id;
                    $row_origin_stock_notice_models[(string)$notice_order_id] = $origin_notice_order_model;


                    //获取对应的出、入库单明细商品数量
                    foreach ($stockSplitDetailEntity->goods_items as $goods_id => & $tradGoodsEntity) {
                        $tmp_quantity = $new_virtual_notice_order_goods_quantities[(string)$notice_order_id][$tradGoodsEntity->goods_id] ?? 0;
                        $new_virtual_notice_order_goods_quantities[(string)$notice_order_id][$tradGoodsEntity->goods_id] = MathUtility::add($tmp_quantity, $tradGoodsEntity->quantity->quantity);

                        $tmp_bill_quantity = $new_stock_bill_goods_quantities[$bill_id][$goods_id] ?? 0;
                        $new_stock_bill_goods_quantities[$bill_id][$goods_id] = MathUtility::add($tmp_bill_quantity, $tradGoodsEntity->quantity->quantity);
                    }
                }

                $this->row_notice_order_id = $row_notice_order_id;
                $this->stock_split_detail_entities = $stock_split_detail_entities;
                $this->row_origin_stock_notice_models = $row_origin_stock_notice_models;
                $this->new_virtual_notice_order_goods_quantities = $new_virtual_notice_order_goods_quantities;
                $this->row_origin_stock_bill_model = $row_origin_stock_bill_model;
                $this->new_stock_bill_goods_quantities = $new_stock_bill_goods_quantities;
            }

            /**
             * 获取通知单id
             * @param $billId
             * @return mixed|null
             */
            public function getNoticeOrderIdByBillId($billId) {
                return $this->row_notice_order_id[(string)$billId] ?? null;
            }

            /**
             * 获取原合同下的原通知单
             * @param $noticeOrderId
             * @return mixed|null
             */
            public function getOriginNoticeOrderModelByNoticeOrderId($noticeOrderId) {
                return $this->row_origin_stock_notice_models[(string)$noticeOrderId] ?? null;
            }

            /**
             * 获取原合同下的原出、入库单
             * @param $bill_id
             * @return mixed
             */
            public function getStockBillModelByBillId($bill_id) {
                return $this->row_origin_stock_bill_model[(string)$bill_id];
            }

            /**
             * 获取新合同下需要新建的虚拟出、入库单的商品数量数组
             * @param $bill_id
             * @return array|mixed
             */
            public function getNewVirtualStockBillGoodsQuantitiesByBillId($bill_id) {
                return $this->new_stock_bill_goods_quantities[(string)$bill_id] ?? [];
            }

            public function getStockSplitDetailEntities() {
                return $this->stock_split_detail_entities;
            }

            /**
             * 获取新合同下需要新建的虚拟通知单的商品数量数组
             * @return array
             */
            public function getNewVirtualNoticeOrderGoodsQuantities() {
                return $this->new_virtual_notice_order_goods_quantities;
            }
        };
    }

    /**
     * 添加、更新虚拟入库单通知单、发货单，提供给原合同
     * @param ContractSplitApply $contractSplitApplyEntity
     * @param \BaseActiveRecord $originNoticeOrderModel
     * @param array $residueGoodsQuantities 剩余商品数量
     * @return \BaseActiveRecord
     * @throws ZException
     * @throws ZModelSaveFalseException
     */
    private function addOrUpdateVirtualNoticeOrderForOriginContract(ContractSplitApply $contractSplitApplyEntity, \BaseActiveRecord $originNoticeOrderModel, array $residueGoodsQuantities): \BaseActiveRecord {
        //如果该通知单就是虚拟单，更新此次虚拟出、入库通知单明细剩余数量
        if (SplitEnum::IS_VIRTUAL == $originNoticeOrderModel->is_virtual) {
            $this->updateOriginVirtualStockNoticeOrderGoodQuantity($contractSplitApplyEntity->isBuyContract(), $originNoticeOrderModel, $residueGoodsQuantities);
            return $originNoticeOrderModel;
        }

        if ($contractSplitApplyEntity->isBuyContract()) {
            return $this->addStockNoticeOrderAndDetail($originNoticeOrderModel->contract_id, $originNoticeOrderModel, $residueGoodsQuantities);
        }

        return $this->addDeliveryOrderAndDetail($originNoticeOrderModel->contract_id, $originNoticeOrderModel, $residueGoodsQuantities);
    }

    /**
     * 生成原合同下的虚拟出、入库单
     * @param bool $isBuyContract
     * @param \BaseActiveRecord $virtualStockNoticeModel
     * @param \BaseActiveRecord $currStockBillModel
     * @param array $needDeductQuantities
     * @throws ZException
     * @throws ZModelSaveFalseException
     */
    private function generateVirtualStockBillForOriginContract(bool $isBuyContract, \BaseActiveRecord & $virtualStockNoticeModel, \BaseActiveRecord & $currStockBillModel, array $needDeductQuantities): void {
        //当前被拆分的出、入库单是虚拟，则直接减去可用数量
        if (SplitEnum::IS_VIRTUAL == $currStockBillModel->is_virtual) {
            $this->subtractVirtualStockBillQuantity($currStockBillModel, $needDeductQuantities);
            return;
        }

        //获取虚拟出、入库单明细商品剩余数量
        $virtual_goods_quantities = $this->getDetailRemainingGoodsQuantities($currStockBillModel, $needDeductQuantities);
        if (\Utility::isEmpty($virtual_goods_quantities)) {
            return;
        }

        if ($isBuyContract) {
            //新建新的虚拟入库单、明细
            $this->addStockInAndDetail($currStockBillModel, $virtual_goods_quantities, $virtualStockNoticeModel);
            return;
        }

        //新建新的虚拟出库单、明细
        $this->addStockOutAndDetail($currStockBillModel, $virtual_goods_quantities, $virtualStockNoticeModel);
    }

    /**
     * 获取明细商品剩余数量
     * @param \BaseActiveRecord | \StockIn | \StockOutOrder $originStockBillModel
     * @param $needDeductQuantities
     * @return array
     * @throws ZException
     */
    private function getDetailRemainingGoodsQuantities(\BaseActiveRecord $originStockBillModel, $needDeductQuantities): array {
        //虚拟入库单需要生成的数
        $virtual_goods_quantities = [];

        if (!isset($originStockBillModel->details) || \Utility::isEmpty($originStockBillModel->details)) {
            throw new ZException("商品明细信息异常！");
        }

        foreach ($originStockBillModel->details as & $originStockBillDetailModel) {
            if (!isset($needDeductQuantities[$originStockBillDetailModel->goods_id])) {
                continue;
            }

            //在原入库单明细基础上，减去本次平移占用的商品数量
            $quantity = MathUtility::sub($originStockBillDetailModel->quantity, $needDeductQuantities[$originStockBillDetailModel->goods_id]);

            if (0 > $quantity) {
                throw new ZException("商品id[" . $originStockBillDetailModel->goods_id . "]的拆分数量超出！");
            }

            //虚拟入库单需要生成的数量
            $virtual_goods_quantities[$originStockBillDetailModel->goods_id] = $quantity;
        }

        return $virtual_goods_quantities;
    }

    /**
     * 减去虚拟出、入库单的可用数量
     * @param \StockIn | \StockOutOrder | \BaseActiveRecord $originStockBillModel
     * @param array $needDeductQuantities 虚拟出、入库单明细商品占用的总数
     * @throws ZModelSaveFalseException
     */
    private function subtractVirtualStockBillQuantity(\BaseActiveRecord $originStockBillModel, array $needDeductQuantities): void {
        //减去虚拟出、入库单的平移商品数量
        foreach ($originStockBillModel->details as & $originDetailModel) {
            if (!isset($needDeductQuantities[$originDetailModel->goods_id])) {
                continue;
            }

            $quantity = MathUtility::sub($originDetailModel->quantity, $needDeductQuantities[$originDetailModel->goods_id]);
            if (0 > $quantity) {
                throw new \Exception("商品id[" . $originDetailModel->goods_id . "]的拆分数量超出");
            }

            //为零不能删除出、入库单明细，有数据关联关系
            if (0 == $quantity AND self::IS_SKIP_ZERO_QUANTITY_GOODS_VIRTUAL) {
                continue;
            }

            $originDetailModel->quantity = $quantity;
            $originDetailModel->quantity_actual = $quantity;
            $originDetailModel->update_time = \Utility::getDateTime();

            if (!$originDetailModel->save()) {
                throw new ZModelSaveFalseException($originDetailModel);
            }

            if (!isset($originDetailModel->subs) || \Utility::isEmpty($originDetailModel->subs)) {
                continue;
            }
            //减去虚拟入库单的第二单位可用数量
            foreach ($originDetailModel->subs as $originDetailSubModel) {
                $unit_rate = $this->getUnitRate($originDetailModel->unit_rate, $originDetailSubModel->unit_rate);
                $originDetailSubModel->quantity = MathUtility::div($quantity, $unit_rate);
                $originDetailSubModel->quantity_actual = $originDetailSubModel->quantity;

                if (!$originDetailSubModel->save()) {
                    throw new ZModelSaveFalseException('更新虚拟单明细的扩展信息失败！');
                }
            }
        }
    }

    /**
     * 获取提单、发货单的明细ids
     * @param \BaseActiveRecord | \StockNotice | \DeliveryOrder $stockNoticeModel
     * @return array
     */
    private function getStockNoticeOrderDetailIds(\BaseActiveRecord $stockNoticeModel): array {
        if (!isset($stockNoticeModel->details) || \Utility::isEmpty($stockNoticeModel->details)) {
            return [];
        }

        $detail_ids = [];
        foreach ($stockNoticeModel->details as & $detailModel) {
            $detail_ids[$detailModel->goods_id] = $detailModel->detail_id;
        }

        return $detail_ids;
    }

    /**
     * 生成入库通知单，明细
     * @param $newContractId  新合同id
     * @param \StockNotice $originStockNoticeModel 原合同的原通知单
     * @param array $goodsQuantities 新的虚拟入库通知单对应的商品数量数组
     * @param bool $isSkipZeroQuantity 为0的商品数量不保存
     * @return \StockNotice
     * @throws ZException
     * @throws ZModelSaveFalseException
     */
    private function addStockNoticeOrderAndDetail($newContractId, \StockNotice & $originStockNoticeModel, array & $goodsQuantities, bool $isSkipZeroQuantity = false): \StockNotice {
        $new_stock_notice_model = new \StockNotice();
        $new_stock_notice_model->setAttributes($originStockNoticeModel->getAttributes());
        $new_stock_notice_model->batch_id = \IDService::getStockBatchId();
        $new_stock_notice_model->code = \StockNoticeService::generateStockNoticeCode($newContractId);
        //
        $new_stock_notice_model->contract_id = $newContractId;  //新合同id
        $new_stock_notice_model->original_id = $originStockNoticeModel->batch_id;  //原入通知单id
        $new_stock_notice_model->is_virtual = SplitEnum::IS_VIRTUAL;
        $new_stock_notice_model->status = \StockNotice::STATUS_SUBMIT;
        $new_stock_notice_model->status_time = \Utility::getDateTime();
        $new_stock_notice_model->remark = '拆分生成的虚拟入库通知单，原入库通知单编号：' . $originStockNoticeModel->code . '，id：' . $originStockNoticeModel->getPrimaryKey();

        if (!$new_stock_notice_model->save()) {
            throw new ZException('新建虚拟入库通知单信息失败！');
        }

        foreach ($originStockNoticeModel->details as & $originStockDetailModel) {
            $quantity = $goodsQuantities[$originStockDetailModel->goods_id] ?? 0;

            //为零是否保存
            if (0 == $quantity AND $isSkipZeroQuantity) {
                continue;
            }

            $detail_model = new \StockNoticeDetail();
            $detail_model->setAttributes($originStockDetailModel->getAttributes());
            $detail_model->detail_id = null;
            $detail_model->batch_id = $new_stock_notice_model->getPrimaryKey();
            $detail_model->contract_id = $newContractId;
            //
            $detail_model->quantity = $quantity;
            $detail_model->quantity_actual = $quantity;
            //
            $detail_model->status_time = \Utility::getDateTime();
            $detail_model->remark = '拆分生成的虚拟入库通知单明细，原明细id：' . $originStockDetailModel->getPrimaryKey();

            if (!$detail_model->save()) {
                throw new ZModelSaveFalseException('新建虚拟入库通知单明细信息失败！');
            }

            //第二单位
            if (!isset($originStockDetailModel->subs) || \Utility::isEmpty($originStockDetailModel->subs)) {
                continue;
            }
            foreach ($originStockDetailModel->subs as $oriStockDetailSubModel) {
                $detail_sub_model = new \StockNoticeDetailSub();
                $detail_sub_model->setAttributes($oriStockDetailSubModel->getAttributes());
                $detail_sub_model->detail_id = $detail_model->getPrimaryKey();
                $detail_sub_model->remark = '拆分生成的虚拟入库通知单明细扩展信息，原明细扩展id：' . $oriStockDetailSubModel->getPrimaryKey();
                //
                $unit_rate = $this->getUnitRate($detail_model->unit_rate, $oriStockDetailSubModel->unit_rate);
                $detail_sub_model->quantity = MathUtility::div($quantity, $unit_rate);
                $detail_sub_model->quantity_actual = $detail_sub_model->quantity;

                if (!$detail_sub_model->save()) {
                    throw new ZModelSaveFalseException('新建虚拟入库通知单明细扩展信息失败！');
                }
            }
        }

        return $new_stock_notice_model;
    }

    /**
     * 生成发货单，明细
     * @param $newContractId 新合同id
     * @param \DeliveryOrder $originDeliveryOrderModel 原合同的原通知单
     * @param array $goodsQuantities 新的虚拟发货单对应的商品数量数组
     * @param bool $isSkipZeroQuantity 为0的商品数量不保存
     * @return \DeliveryOrder
     * @throws ZException
     */
    private function addDeliveryOrderAndDetail($newContractId, \DeliveryOrder & $originDeliveryOrderModel, array & $goodsQuantities, bool $isSkipZeroQuantity = false): \DeliveryOrder {
        $new_delivery_order_model = new \DeliveryOrder();
        $new_delivery_order_model->setAttributes($originDeliveryOrderModel->getAttributes());
        $new_delivery_order_model->order_id = \IDService::getDeliveryOrderId();
        $codeInfo = \CodeService::getDeliveryOrderCode($originDeliveryOrderModel->corporation_id);
        if ($codeInfo['code'] == \ConstantMap::INVALID) {
            throw new ZException($codeInfo['msg']);
        }
        $new_delivery_order_model->code = $codeInfo['data'];
        //
        $new_delivery_order_model->original_id = $originDeliveryOrderModel->order_id;  //原发货单id
        $new_delivery_order_model->contract_id = $newContractId;
        //
        $new_delivery_order_model->is_virtual = SplitEnum::IS_VIRTUAL;
        $new_delivery_order_model->status = \DeliveryOrder::STATUS_PASS;
        $new_delivery_order_model->status_time = \Utility::getDateTime();
        $new_delivery_order_model->remark = '拆分生成的虚拟发货单，原发货单编号：' . $originDeliveryOrderModel->code . ',id:' . $originDeliveryOrderModel->getPrimaryKey();

        if (!$new_delivery_order_model->save()) {
            throw new ZException('新建虚拟入库通知单信息失败！');
        }

        foreach ($originDeliveryOrderModel->details as & $originDeliveryDetailModel) {
            $quantity = $goodsQuantities[$originDeliveryDetailModel->goods_id] ?? 0;

            //为零是否保存
            if (0 == $quantity AND $isSkipZeroQuantity) {
                continue;
            }

            $detail_model = new \DeliveryOrderDetail();
            $detail_model->setAttributes($originDeliveryDetailModel->getAttributes());
            $detail_model->detail_id = null;
            $detail_model->order_id = $new_delivery_order_model->getPrimaryKey();
            $detail_model->contract_id = $newContractId;
            //
            $detail_model->quantity = $quantity;
            $detail_model->quantity_actual = $quantity;
            //
            $detail_model->status_time = \Utility::getDateTime();
            $detail_model->remark = '拆分生成的虚拟发货单明细，原明细id:' . $originDeliveryDetailModel->getPrimaryKey();

            if (!$detail_model->save()) {
                throw new ZException('新建虚拟发货单明细信息失败！');
            }

            if (!isset($originDeliveryDetailModel->stockDeliveryDetails) || \Utility::isEmpty($originDeliveryDetailModel->stockDeliveryDetails)) {
                continue;
            }

            //换算率 = 拆出的数量 / 对应明细的数量
            $rate = MathUtility::div($quantity, $originDeliveryDetailModel->quantity);

            foreach ($originDeliveryDetailModel->stockDeliveryDetails as & $originStockDeliveryDetail) {
                $detail_delivery_model = new \StockDeliveryDetail();
                $detail_delivery_model->setAttributes($originStockDeliveryDetail->getAttributes());
                $detail_delivery_model->stock_detail_id = null;
                $detail_delivery_model->order_id = $detail_model->order_id;
                $detail_delivery_model->detail_id = $detail_model->getPrimaryKey();
                $detail_delivery_model->remark = '拆分生成的配货明细，原配货明细id:' . $originStockDeliveryDetail->getPrimaryKey();

                $detail_delivery_model->quantity = 0;
                $detail_delivery_model->quantity_actual = 0;

                if (!$detail_delivery_model->save()) {
                    throw new ZException('新建虚拟发货单的配货明细信息失败！');
                }
            }
        }

        return $new_delivery_order_model;
    }

    /**
     * 生成入库单，明细
     * @param \StockIn $originStockInModel 原合同的入库单
     * @param array $goodsQuantities 新的虚拟入库单对应的商品数量数组
     * @param \StockNotice $newStockNoticeModel 新的虚拟入库通知单
     * @param bool $isSkipZeroQuantity 为0的商品数量不保存
     * @return \StockIn
     * @throws ZModelSaveFalseException
     */
    private function addStockInAndDetail(\StockIn & $originStockInModel, array & $goodsQuantities, \StockNotice & $newStockNoticeModel, bool $isSkipZeroQuantity = false): \StockIn {
        //新的虚拟入库通知单的明细id数组
        $stockNoticeDetailIds = $this->getStockNoticeOrderDetailIds($newStockNoticeModel);

        $new_contract_id = $newStockNoticeModel->contract_id;  //新的合同id
        $old_stock_in_id = $originStockInModel->getPrimaryKey();  //原入库单id
        $new_stock_notice_id = $newStockNoticeModel->getPrimaryKey(); //新的入库通知单id

        //生成新的入库单
        $model = new \StockIn();
        $model->setAttributes($originStockInModel->getAttributes());
        $model->stock_in_id = \IDService::getStockInId();
        $model->code = \StockInService::generateStockInCode($new_stock_notice_id);
        //
        $model->original_id = $old_stock_in_id;  //原入库单id
        $model->batch_id = $new_stock_notice_id; //关联到新的入库通知单
        $model->contract_id = $new_contract_id;  //新的合同id
        $model->split_status = StockSplitEnum::SPLIT_STATUS_DEFAULT;  //拆分状态
        $model->is_virtual = SplitEnum::IS_VIRTUAL; //是拆分生成
        //
        $model->status = \StockIn::STATUS_PASS;
        $model->status_time = \Utility::getDateTime();
        $model->remark = '拆分生成虚拟的入库单，原入库单编号：' . $originStockInModel->code . '，ID：' . $old_stock_in_id;
        //
        if (!$model->save()) {
            throw new ZModelSaveFalseException($model);
        }

        //保存新的入库单明细
        foreach ($originStockInModel->details as & $originStockInDetailModel) {
            //新的入库通知单明细id
            $stock_notice_detail_id = $stockNoticeDetailIds[$originStockInDetailModel->goods_id] ?? 0;

            $quantity = $goodsQuantities[$originStockInDetailModel->goods_id] ?? 0;
            //明细的商品数量为0的跳过
            if (0 >= $quantity AND $isSkipZeroQuantity) {
                continue;
            }

            $detail_model = new \StockInDetail();
            $detail_model->setAttributes($originStockInDetailModel->getAttributes());
            $detail_model->stock_id = null;
            $detail_model->contract_id = $new_contract_id;
            $detail_model->detail_id = $stock_notice_detail_id;
            $detail_model->stock_in_id = $model->getPrimaryKey();
            $detail_model->quantity = $quantity;
            $detail_model->quantity_actual = $quantity;
            $detail_model->status_time = \Utility::getDateTime();
            $detail_model->remark = '拆分生成虚拟的入库单明细，原入库单明细ID：' . $originStockInDetailModel->getPrimaryKey();

            if (!$detail_model->save()) {
                throw new ZModelSaveFalseException($detail_model);
            }

            if (!isset($originStockInDetailModel->subs) || \Utility::isEmpty($originStockInDetailModel->subs)) {
                continue;
            }

            //保存第二单位
            foreach ($originStockInDetailModel->subs as & $originStockInDetailSubModel) {
                $detail_sub_model = new \StockInDetailSub();
                $detail_sub_model->setAttributes($originStockInDetailSubModel->getAttributes());
                $detail_sub_model->stock_id = $detail_model->getPrimaryKey();
                $detail_sub_model->remark = '拆分生成虚拟的入库单明细扩展信息，原明细扩展信息ID：' . $originStockInDetailSubModel->getPrimaryKey();
                //
                $unit_rate = $this->getUnitRate($detail_model->unit_rate, $originStockInDetailSubModel->unit_rate);
                $detail_sub_model->quantity = MathUtility::div($quantity, $unit_rate);
                $detail_sub_model->quantity_actual = $detail_sub_model->quantity;

                if (!$detail_sub_model->save()) {
                    throw new ZModelSaveFalseException('新建虚拟入库通知单明细扩展信息失败！');
                }
            }
        }

        return $model;
    }

    /**
     * 生成出库单、明细
     * @param \StockOutOrder $originStockOutModel 原合同的出库单
     * @param array $goodsQuantities 新的虚拟出库单对应的商品数量数组
     * @param \DeliveryOrder $newDeliveryOrderModel 新的虚拟发货单
     * @param bool $isSkipZeroQuantity 为0的商品数量不保存
     * @return \StockOutOrder
     * @throws ZException
     */
    private function addStockOutAndDetail(\StockOutOrder & $originStockOutModel, array & $goodsQuantities, \DeliveryOrder & $newDeliveryOrderModel, bool $isSkipZeroQuantity = false): \StockOutOrder {
        //新的虚拟发货单的明细id数组
        $originStockNoticeDetailIds = $this->getStockNoticeOrderDetailIds($newDeliveryOrderModel);

        $new_contract_id = $newDeliveryOrderModel->contract_id; //新的合同id
        $origin_stock_out_id = $originStockOutModel->getPrimaryKey();  //原出库单id
        $new_delivery_order_id = $newDeliveryOrderModel->getPrimaryKey(); //新的发货单id

        $model = new \StockOutOrder();
        $model->setAttributes($originStockOutModel->getAttributes());
        $model->out_order_id = \IDService::getStoreOutOrderId();
        $model->code = \StockOutService::generateStockOutCode($new_delivery_order_id);
        //
        $model->original_id = $originStockOutModel->out_order_id;  //原出库单id
        $model->contract_id = $new_contract_id; //新的合同id
        $model->order_id = $new_delivery_order_id; //新的发货单id
        //
        $model->is_virtual = SplitEnum::IS_VIRTUAL; //是拆分生成
        $model->split_status = StockSplitEnum::SPLIT_STATUS_DEFAULT;
        $model->status = \StockOutOrder::STATUS_SUBMITED;
        $model->status_time = \Utility::getDateTime();
        $model->remark = '拆分生成虚拟的出库单，原出库单编号：' . $originStockOutModel->code . '，ID：' . $origin_stock_out_id;

        if (!$model->save()) {
            throw new ZException("保存虚拟出库单失败！");
        }

        //减去原出库单的平移商品数量
        foreach ($originStockOutModel->details as & $originStockOutDetailModel) {
            //新的发货单明细id
            $stock_notice_detail_id = $originStockNoticeDetailIds[$originStockOutDetailModel->goods_id] ?? 0;

            $quantity = $goodsQuantities[$originStockOutDetailModel->goods_id] ?? 0;
            //明细的商品数量为0的跳过
            if (0 >= $quantity AND $isSkipZeroQuantity) {
                continue;
            }

            $detail_model = new \StockOutDetail();
            $detail_model->setAttributes($originStockOutDetailModel->getAttributes());
            $detail_model->quantity = $quantity;
            $detail_model->quantity_actual = $quantity;
            $detail_model->out_id = null;
            $detail_model->contract_id = $new_contract_id;
            $detail_model->out_order_id = $model->getPrimaryKey(); //新出库单id
            $detail_model->order_id = $new_delivery_order_id; //新的发货单id
            $detail_model->detail_id = $stock_notice_detail_id; //新的发货单明细id
            $detail_model->stock_detail_id = 0; //没有配货明细id
            $detail_model->status_time = \Utility::getDateTime();
            $detail_model->remark = '拆分生成虚拟的入库单明细，原入库单明细ID：' . $originStockOutDetailModel->getPrimaryKey();

            if (!$detail_model->save()) {
                throw new ZException("保存虚拟出库单明细失败！");
            }
        }

        return $model;
    }

    /**
     * 初始化合同的原始商品信息
     * @param $goodsItems
     * @throws ZModelSaveFalseException
     */
    private function backupOriginalContractGoods($goodsItems): void {
        if (\Utility::isEmpty($goodsItems)) {
            return;
        }

        foreach ($goodsItems as $item) {
            $model = new \OriginalContractGoods();
            $values = $item->getAttributes();
            $model->setAttributes($values);
            $model->quantity = $item->quantity->quantity;
            $model->unit = $item->quantity->unit;
            $model->quantity_actual = $item->quantity->quantity;
            $model->amount_cny = $item->amount;
            if (!$model->save()) {
                throw new ZModelSaveFalseException($model);
            }
        }
    }

    /**
     * 设置正在被拆分中
     * @param $type
     * @param $billId
     * @throws \Exception
     */
    public function setOriginalStockBillIsSplitting($type, $billId): void {
        $class = (StockSplitEnum::TYPE_STOCK_IN == $type) ? IStockInRepository::class : IStockOutRepository::class;

        $stockBillEntity = DIService::getRepository($class)->findByPk($billId);
        $stockBillEntity->setIsSplitting();
        if (!$stockBillEntity->save()) {
            throw new ZModelSaveFalseException($stockBillEntity);
        }
    }

    /**
     * 取消正在被拆分中
     * @param $type
     * @param $billId
     * @throws \Exception
     */
    public function cancelOriginalStockBillIsSplitting($type, $billId): void {
        $class = (StockSplitEnum::TYPE_STOCK_IN == $type) ? IStockInRepository::class : IStockOutRepository::class;

        $stockBillEntity = DIService::getRepository($class)->findByPk($billId);
        $stockBillEntity->cancelIsSplitting();
        if (!$stockBillEntity->save()) {
            throw new ZModelSaveFalseException($stockBillEntity);
        }
    }

    /**
     * 设置正在已被拆分
     * @param $type
     * @param $billId
     * @throws \Exception
     */
    public function setOriginalStockBillHasBeenSplit($type, $billId): void {
        $class = (StockSplitEnum::TYPE_STOCK_IN == $type) ? IStockInRepository::class : IStockOutRepository::class;

        $stockBillEntity = DIService::getRepository($class)->findByPk($billId);
        $stockBillEntity->setHasBeenSplit();
        if (!$stockBillEntity->save()) {
            throw new ZModelSaveFalseException($stockBillEntity);
        }
    }

    /**
     * 出入库拆分对应的原出、入库单，生成对应的虚拟出、入库单
     * @param StockSplitApply $stockSplitApplyEntity
     * @param \StockIn | \StockOutOrder | \BaseActiveRecord $originStockBillModel
     * @param array $needDeductQuantities 出库单明细商品占用的总数
     * @throws \Exception
     */
    private function generateVirtualStockBillAndDetailForOriginContract(StockSplitApply & $stockSplitApplyEntity, \BaseActiveRecord & $originStockBillModel, array $needDeductQuantities): void {
        $is_stock_in = $stockSplitApplyEntity->isStockInSplit();

        //虚拟通知单和出、入库单的数量是一样的，对一对一关系
        $virtual_goods_quantities = $this->getDetailRemainingGoodsQuantities($originStockBillModel, $needDeductQuantities);

        if ($is_stock_in) {
            //获取虚拟出、入库单明细商品剩余数量
            $virtual_stock_notice_model = $this->addStockNoticeOrderAndDetail($originStockBillModel->contract_id, $originStockBillModel->notice, $virtual_goods_quantities);
        } else {
            $virtual_stock_notice_model = $this->addDeliveryOrderAndDetail($originStockBillModel->contract_id, $originStockBillModel->deliveryOrder, $virtual_goods_quantities);
        }

        //新建新的虚拟出库单,给原合同
        if ($is_stock_in) {
            $this->addStockInAndDetail($originStockBillModel, $virtual_goods_quantities, $virtual_stock_notice_model);
            return;
        }
        $this->addStockOutAndDetail($originStockBillModel, $virtual_goods_quantities, $virtual_stock_notice_model);
    }

    /**
     * 减少原合同虚拟入库通知、发货单数量
     * @param bool $isStockInSplit
     * @param \BaseActiveRecord $originVirtualStockNoticeModel
     * @param array $goodsQuantities
     * @throws ZException
     */
    private function updateOriginVirtualStockNoticeOrderGoodQuantity(bool $isStockInSplit, \BaseActiveRecord & $originVirtualStockNoticeModel, array & $goodsQuantities) {
        foreach ($originVirtualStockNoticeModel->details as $detailModel) {
            ;
            $quantity = $goodsQuantities[$detailModel->goods_id] ?? 0;

            $detailModel->quantity = $quantity;
            $detailModel->quantity_actual = $quantity;

            if (!$detailModel->save()) {
                throw new ZException('减少原合同虚拟' . ($isStockInSplit ? '入库通知' : '发货') . '单商品数量失败！');
            }

            if (!isset($detailModel->subs) || empty($detailModel->subs)) {
                continue;
            }
            foreach ($detailModel->subs as & $detailSubModel) {
                $unit_rate = $this->getUnitRate($detailModel->unit_rate, $detailSubModel->unit_rate);
                $detailSubModel->quantity = MathUtility::div($detailModel->quantity, $unit_rate);
                $detailSubModel->quantity_actual = $detailSubModel->quantity;

                if (!$detailSubModel->save()) {
                    throw new ZException('减少原合同虚拟' . ($isStockInSplit ? '入库通知' : '发货') . '单扩展单位数量失败！');
                }
            }
        }
    }

    private function getUnitRate($detailUnitRate, $detailSubUnitRate) {
        return self::IS_DETAIL_SUB_UNIT_RATE ? $detailSubUnitRate : $detailUnitRate;
    }
}
