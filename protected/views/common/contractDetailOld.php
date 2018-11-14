<?php
/**
 * Created by youyi000.
 * DateTime: 2017/9/18 17:09
 * Describe：
 */

if(!empty($contract->relative))
{
    $this->renderPartial("/common/contractChannelInfoOld", array('contract'=>$contract));
}
else
{
    $this->renderPartial("/common/contractInfoOld", array('contract'=>$contract));
}