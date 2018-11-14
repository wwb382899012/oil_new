<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/7 19:54
 * Describe：
 */

class ContractSearch
{
    public static function search($params=array(),$pageSize=25)
    {

        $condition="";

        if(key_exists("status",$params))
        {
            if($params["status"]!="-1")
            {
                $condition="";
                //$query->andWhere(["status"=>$params["status"]]);
            }
            unset($params["status"]);
        }


        $dataProvider = new CActiveDataProvider('Contract', array(
            'pagination'=>array(
                'pageSize'=>$pageSize,
            ),
            //'totalItemCount'=>10000,
            'criteria'=>array(
                'condition'=>$condition,
                //'order'=>'create_time DESC',
                //'with'=>array('author'),
            ),

            'sort'=>array(
                'defaultOrder'=> array('contract_id'=> CSort::SORT_DESC),
            )
        ));

        return $dataProvider;
    }

}