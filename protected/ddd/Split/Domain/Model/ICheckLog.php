<?php

namespace  ddd\Split\Domain\Model;

use ddd\Common\Domain\IRepository;

interface ICheckLog extends IRepository{
    function findAllByObjIdAndBusinessId($objId,$businessId);
}