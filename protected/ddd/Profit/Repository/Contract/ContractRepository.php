<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/14 15:49
 * Describe：
 */

namespace ddd\Profit\Repository\Contract;


use ddd\Common\Domain\Currency\CurrencyService;
use ddd\Common\Domain\Value\Money;
use ddd\Common\Domain\Value\Quantity;
use ddd\Common\Repository\BaseRepository;
use ddd\Profit\Domain\Contract\Contract;
use ddd\Profit\Domain\Contract\ContractGoods;
use ddd\Profit\Domain\Contract\IContractRepository;

class ContractRepository extends BaseRepository implements IContractRepository
{

    /**
     * 根据合同id查找合同信息
     * @param $contract_id
     * @return Contract|null
     */
    public function findByContractId($contract_id)
    {
        // TODO: Implement findByContractId() method.
        $model=\Contract::model()->with("goods")->findByPk($contract_id);
        if(empty($model))
            return null;
        $check = \CheckDetail::model()->find("flow_id=3 and obj_id=".$contract_id." and check_status=1");
        if(!empty($check))
            $model->check_pass_time = $check->update_time;
        return $this->dataToEntity($model);
    }

    /**
     *
     * @param \Contract $model
     * @return Contract
     */
    protected function dataToEntity(\Contract $model)
    {
        $entity =new Contract();
        $entity->setAttributes($model->getAttributes());
        if(is_array($model->goods))
        {
            foreach ($model->goods as $goods)
            {
                $item = new ContractGoods();
                $item->goods_id        = $goods->goods_id;
                $item->quantity        = new Quantity($goods->quantity,$goods->unit);
                $item->t_exchange_rate = $goods->unit_convert_rate;
                $item->price           = new Money($goods->price, $goods->currency);
                $item->price_cny           = new Money($goods->amount_cny/$goods->quantity, CurrencyService::CNY);
                $entity->addGoods($item);
            }
        }
        return $entity;
    }

}