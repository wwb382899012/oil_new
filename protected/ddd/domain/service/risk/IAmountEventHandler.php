<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/13 16:26
 * Describe：
 */

namespace ddd\domain\service\risk;


interface IAmountEventHandler
{
    function getPartnerId();

    function getAmount();

    function getCategory();

    //function getLogCategory();

    function getRelationId();

    function getContractInfo();
}