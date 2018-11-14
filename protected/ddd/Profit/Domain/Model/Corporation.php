<?php
/**
 * Desc: 交易主体
 * User: wwb
 * Date: 2018/8/2
 * Time: 15:39
 */

namespace ddd\Profit\Domain\Model;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\Attachment;
use ddd\domain\entity\value\Price;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\Utility;



class Corporation extends BaseEntity implements IAggregateRoot
{

    /**
     * 交易主体id
     * @var      int
     */
    public $corporation_id;

    /**
     * 交易主体名称
     * @var      string
     */
    public $corporation_name;



    /**
     * 获取id
     * @return int
     */
    public function getId()
    {
        return $this->corporation_id;
    }

    /**
     * 设置id
     * @param $value
     */
    public function setId($value)
    {
        $this->corporation_id = $value;
    }


    /**
     * 创建对象
     * @param
     * @return   static
     * @throws   \Exception
     */
    public static function create()
    {

    }



}
