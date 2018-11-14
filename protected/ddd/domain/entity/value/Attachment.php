<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/27 9:56
 * Describe：
 */

namespace ddd\domain\entity\value;


use ddd\Common\Domain\BaseValue;

class Attachment extends BaseValue
{
    /**
     * id
     * @var      int
     */
    public $id;

    /**
     * 类型
     * @var      float
     */
    public $type;

    /**
     * 文件名
     * @var      int
     */
    public $name;

    /**
     * 路径
     * @var      int
     */
    public $file_url;
}