<?php
/**
 * Created by youyi000.
 * DateTime: 2018/7/20 17:56
 * Describe：
 */

namespace ddd\Common\Domain\Value;


class Unit extends BaseCommonValue
{
    #region property

    /**
     * 标识或id
     * @var   int
     */
    public $id;

    /**
     * 名称
     * @var   string
     */
    public $name;

    /**
     * 符号
     * @var   string
     */
    public $symbol;

    #endregion


    public function __construct($id,$name,$symbol="")
    {
        parent::__construct();
        $this->id=$id;
        $this->name=$name;
        $this->symbol=$symbol;
    }


    #region Factory methods

    public static function createT()
    {
        return new Unit(2,"吨","t");
    }

    #endregion


}