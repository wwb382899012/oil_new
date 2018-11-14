<?php

/**
 * Created by youyi000.
 * DateTime: 2017/3/21 11:55
 * Describe：
 */
class GoodsService
{
    /**
     * 获取所有有效的商品信息
     * @return mixed
     */
    public static function getAllActiveGoods()
    {
        return Goods::getAllActiveData();//Goods::model()->findAllToArray(array("condition"=>"status=1","order"=>"goods_id desc"));
    }

	public static function getSpecialGoods($goods_ids) {
		$sql="select goods_id,name from t_goods where goods_id in (" . $goods_ids .")";
		$data=Utility::query($sql);

		return $data;
    }

	/**
	 * @desc 根据商品ids获取商品名
	 * @param string $goods_ids | string 以逗号隔开的商品id序列
     * @param string $separator 分隔字符串
     * @return string eg: 混合三甲苯 | 稳定轻烃 | 二甲苯
	 */
	public static function getSpecialGoodsNames($goods_ids,$separator="&nbsp;|&nbsp;") {
        $names = '';
        if(!empty($goods_ids)) {
            $goodsArr = self::getSpecialGoods($goods_ids);
            $goodsNames = array();
            if(count($goodsArr) > 0) {
                foreach ($goodsArr as $key => $row) {
                    $goodsNames[]= $row['name'];
                }
            }
            if(is_array($goodsNames) && count($goodsNames) > 0) {
                $names = implode($goodsNames,$separator);
            }
        }

		return $names;
    }
}