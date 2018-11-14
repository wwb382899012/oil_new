<?php
/**
 * Desc: 项目
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



class Project extends BaseEntity implements IAggregateRoot
{

    /**
     * 项目id
     * @var      int
     */
    public $project_id;

    /**
     * 项目编号
     * @var      string
     */
    public $project_code;

    /**
     * 交易主体id
     * @var      int
     */
    public $corporation_id;

    /**
     * 项目类型
     * @var      int
     */
    public $type;

    /**
     * 项目负责人id
     * @var      int
     */
    public $manager_user_id;

    /**
     * 项目负责人
     * @var      string
     */
    public $manager_user_name;

    /**
     * 获取id
     * @return int
     */
    public function getId()
    {
        return $this->project_id;
    }

    /**
     * 设置id
     * @param $value
     */
    public function setId($value)
    {
        $this->project_id = $value;
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
