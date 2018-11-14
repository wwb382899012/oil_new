<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/23 16:21
 * Describe：
 */

class GoodsPriceSearch
{
    public static function search($params=array(),$pageSize=25)
    {

        $condition="";
        if(empty($params))
            $params=array();

        if(key_exists("status",$params))
        {
            if($params["status"]!="-1")
            {
                $condition="";
                //$query->andWhere(["status"=>$params["status"]]);
            }
            unset($params["status"]);
        }

        if(!empty($params["goods_name"]))
        {
            $condition="goods.name like'%".$params["goods_name"]."%'";
        }

        $dataProvider = new CActiveDataProvider('GoodsPrice', array(
            'pagination'=>array(
                'pageSize'=>$pageSize,
            ),
            'criteria'=>array(
                'with'=>"goods",
                'condition'=>$condition,
            ),
            'sort'=>array(
                'defaultOrder'=> array('price_id'=> CSort::SORT_DESC),
            )
        ));

        return $dataProvider;
    }

}