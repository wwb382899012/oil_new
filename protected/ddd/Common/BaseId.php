<?php
/**
 * Created by youyi000.
 * DateTime: 2018/5/21 17:38
 * Describe：
 *  ID的统一基类
 */

namespace ddd\Common;


use system\components\base\Object;

class BaseId extends Object
{
    /**
     * 只读的id
     * @var int
     */
    public $id;

    /**
     * 获取Id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct($id=0)
    {
        $this->id=$id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }

    /**
     * 生成新的id
     * @return BaseId
     */
    public static function generate()
    {
        return new static();
    }

    /**
     * 是否和另一个id相同
     * @param BaseId $other
     * @return bool
     */
    public function equals(BaseId $other )
    {
        return $other instanceof static && $this->id===$other->id;
    }

}