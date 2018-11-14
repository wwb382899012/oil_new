<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/9 16:17
 * Describe：
 */

namespace ddd\domain\entity\risk;


 use ddd\repository\risk\PartnerAmountRepository;

 abstract class PartnerAmount extends RiskAmount
{
    /**
     * @var      int
     */
    public $partner_id;

 }