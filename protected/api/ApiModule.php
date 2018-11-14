<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/19 14:40
 * Describe：
 */

class ApiModule extends CWebModule
{
    public function init()
    {
        parent::init();
        Mod::setPathOfAlias('api',dirname(__FILE__));
        $this->setImport(array(
                             'api.components.*',
                         ));
    }
}