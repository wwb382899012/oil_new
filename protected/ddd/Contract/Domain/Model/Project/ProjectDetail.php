<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/30 11:16
 * Describe：
 */

namespace ddd\Contract\Domain\Model\Project;


use ddd\Contract\Domain\Model\Project\ProjectGoods;
use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ZException;

/**
 * @Name            项目概况
 * @DateTime        2018年3月30日 11:52:48
 * @Author          youyi000
 */
class ProjectDetail extends BaseEntity
{
    #region public property

    /**
     * 上游合作方
     * @var      int
     */
    public $up_partner_id;

    /**
     * 下游合作方
     * @var      int
     */
    public $down_partner_id;

    /**
     * 代理商
     * @var      int
     */
    public $agent_id;

    /**
     * 0：同时采销
     * 1：先采后销
     * 2：先销后采
     * 购销顺序
     * @var      int
     */
    public $buy_sell_type;

    /**
     * 价格方式
     * @var      int
     */
    public $price_type;

    /**
     * 采购币种
     * @var      int
     */
    public $buy_currency;

    /**
     * 销售币种
     * @var      int
     */
    public $sell_currency;

    /**
     * 仓库
     * @var      int
     */
    public $store_id;

    /**
     * 交易商品  [goodsId=>ProjectTradeGoods]
     * ProjectTradeGoods 数组
     * @var      array
     */
    public $goods_items;

    #endregion

    /**
     * 新创建对象
     * @param array $params
     * @return ProjectDetail
     * @throws \Exception
     */
    public static function create($params=[])
    {
        // TODO: implement
        return new static($params);
    }


    /**
     *
     * @param ProjectGoods $goodsItem
     * @throws \Exception
     */
    public function addGoodsItem(ProjectGoods $goodsItem)
    {
        // TODO: implement
        if(empty($goodsItem))
            throw new ZException("参数ProjectTradeGoods对象为空");

        $goodsId=$goodsItem->goods_id;
        if($this->goodsIsExists($goodsId))
        {
            throw new ZException(BusinessError::Project_Goods_Is_Exists,
                                  array("goods_id"=>$goodsId,));
        }
        $this->goods_items[$goodsId]=$goodsItem;

    }

    /**
     * @param    int $goodsId
     */
    public function removeGoods($goodsId)
    {
        // TODO: implement
        unset($this->goods_items[$goodsId]);
    }

    /**
     * 判断当前商品项是否已经存在
     * @param $goodsId
     * @return bool
     */
    public function goodsIsExists($goodsId)
    {
        return isset($this->goods_items[$goodsId]);
    }
}
