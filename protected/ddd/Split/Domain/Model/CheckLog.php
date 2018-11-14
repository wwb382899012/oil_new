<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/5/29 0029
 * Time: 10:13
 */

namespace ddd\Split\Domain\Model;


use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\value\Quantity;

class CheckLog extends BaseEntity{


    public $detail_id;

    public $obj_id;

    public $check_id;

    /**
     * 审核节点
     * @var
     */
    public $node_name;

    /**
     * 审核人ID
     * @var
     */
    public $user_id;

    /**
     * 审核人
     * @var
     */
    public $checker = '系统操作员';

    /**
     * 审核意见
     * @var
     */
    public $remark;

    /**
     *
     * @var
     */
    public $check_status;

    /**
     * 结果
     * @var
     */
    public $result;

    /**
     * 审核时间
     * @var
     */
    public $check_time;

}