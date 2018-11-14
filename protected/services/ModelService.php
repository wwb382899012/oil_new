<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/21 15:15
 * Describe：
 */

class ModelService
{

    /**
     * 模型对象数组转成key-value数组
     * @param $models
     * @param null $keyField
     * @return array
     */
    public static function modelsToKeyModels($models,$keyField=null)
    {
        $map=array();
        if(!is_array($models) && count($models)==0)
            return $map;

        foreach ($models as $m)
        {
            if(empty($keyField))
                $map[$m->getPrimaryKey()]=$m;
            else
                $map[$m->getAttribute($keyField)]=$m;
        }
        return $map;
    }
}