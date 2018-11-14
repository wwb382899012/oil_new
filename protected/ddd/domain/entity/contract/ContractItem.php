<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/10 17:02
 * Describe：
 */

namespace ddd\domain\entity\contract;

use ddd\Common\Domain\BaseValue;
use ddd\Common\Domain\IValue;


/**
 * @Name            合同条款
 * @DateTime        2018年4月10日 15:49:34
 * @Author          youyi000
 */
class ContractItem extends BaseValue
{

    /**
     * 条款key
     * @var   string
     */
    public $key;

    /**
     * 条款名称
     * @var   string
     */
    public $name;

    /**
     * 条款内容
     * @var   string
     */
    public $content;

    /**
     * 值类型
     * @var   int
     */
    public $content_type;

    /**
     * 是否相等
     * @param IValue $value
     * @return bool
     */
    public function equals(IValue $value)
    {
        return $value->key===$this->key && $this->content==$value->content;
    }
}