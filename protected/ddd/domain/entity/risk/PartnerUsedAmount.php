<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/12 9:46
 * Describe：
 */

namespace ddd\domain\entity\risk;

use ddd\domain\entity\Partner;
use ddd\Common\IAggregateRoot;
use ddd\repository\risk\PartnerUsedAmountRepository;

class PartnerUsedAmount extends PartnerAmount implements IAggregateRoot
{
    function getId()
    {
        // TODO: Implement getId() method.
        return $this->id;
    }

    function getIdName()
    {
        // TODO: Implement getIdName() method.
        return "id";
    }

    function setId($value)
    {
        $this->id=$value;
    }

    public static function create(Partner $partner=null)
    {
        $obj= new PartnerUsedAmount();
        if(!empty($partner))
            $obj->partner_id=$partner->getId();

        return $obj;
    }

    public function __construct()
    {
        $this->repositoryClassName=PartnerUsedAmountRepository::class;
        parent::__construct();
    }

}