<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2018/5/29
 * Time: 16:18
 */

namespace ddd\Split\Domain\Model\StockSplit;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\Attachment;
use ddd\infrastructure\error\ZException;
use Utility;

/**
 * 出入库拆分申请实体基类
 * Class StockSplitApply
 * @package ddd\Split\Domain\Model\StockSplitApply
 */
class StockSplitApply extends BaseEntity implements IAggregateRoot{

    /**
     * 提交事件
     */
    const EVENT_AFTER_SUBMIT = "onAfterSubmit";
    /**
     * 驳回事件
     */
    const EVENT_AFTER_BACK = "onAfterBack";
    /**
     * 提交事件
     */
    const EVENT_AFTER_PASS = "onAfterPass";


    #region property

    /**
     * 标识
     * @var   int
     */
    public $apply_id = 0;

    /**
     * 源合同id
     * @var
     */
    public $contract_id = 0;

    /**
     * 出入库单id
     * @var   int
     */
    public $bill_id = 0;

    /**
     * 出入库单编号
     * @var
     */
    public $bill_code = '';

    /**
     * 出入库类型
     * @var
     */
    public $type;

    /**
     * 附件
     * @var   array
     */
    protected $files = [];

    /**
     * 状态
     * @var   int
     */
    public $status = 0;

    /**
     * 状态日期
     * @var   datetime
     */
    public $status_time = '';

    /**
     * 备注
     * @var   string
     */
    public $remark = '';

    /**
     * 创建日期
     * @var   datetime
     */
    public $create_time;

    /**
     * 更新日期
     * @var   datetime
     */
    public $update_time;

    /**
     * 拆分明细项
     * @var   array
     */
    protected $details = [];

    /**
     * 剩余商品信息
     * @var   array
     */
    protected $balance_goods = [];

    #endregion

    use StockSplitApplyRepository;

    /**
     * 事件配置，事件名必须以on开头，否则无效
     * @return array
     */
    protected function events(){
        return [
            static::EVENT_AFTER_SUBMIT,
            static::EVENT_AFTER_BACK,
            static::EVENT_AFTER_PASS,
        ];
    }

    /**
     * 创建对象
     * @param BaseEntity $stockBillEntity 出入库单实体
     * @param $contractId  源合同ID
     * @return StockSplitApply
     * @throws \Exception
     */
    public static function create(BaseEntity $stockBillEntity, $contractId){
        $entity = new static();
        $entity->bill_id = $stockBillEntity->bill_id;
        $entity->bill_code = $stockBillEntity->bill_code;
        $entity->contract_id = $contractId;
        $entity->status = StockSplitEnum::STATUS_NEW;
        $entity->status_time = Utility::getDateTime();
        $entity->type = $stockBillEntity->type;

        $entity->initBalanceGoods($stockBillEntity);

        return $entity;
    }

    /**
     * 初始化可拆分商品数量
     * @param BaseEntity|StockIn|StockOut $stockBillEntity 出入库单实体
     */
    public function initBalanceGoods(BaseEntity $stockBillEntity){
        $this->balance_goods = [];
        if(is_array($stockBillEntity->items)){
            foreach($stockBillEntity->items as $g){
                $this->balance_goods[$g->goods_id] = $g->quantity->quantity;
            }
        }
    }

    public function getId(){
        return $this->apply_id;
    }

    public function setId($value){
        $this->apply_id = $value;
    }

    public function getDetails(){
        return $this->details;
    }

    public function clearDetails(){
        $this->details = [];
    }

    public function getFiles(){
        return $this->files;
    }

    public function clearFiles(){
        $this->files = [];
    }

    /**
     * 增加入库平移明细
     * @param StockSplitDetail $stockSplitDetail
     * @param bool $checkBalanceGoods
     * @return bool
     * @throws ZException
     */
    public function addSplitDetail(StockSplitDetail $stockSplitDetail,$checkBalanceGoods = true){
        if(\Utility::isNotEmpty($stockSplitDetail)){
            ExceptionService::throwArgumentNullException("StockSplitDetail对象", array('class' => get_class($this), 'function' => __FUNCTION__));
        }

        if(is_array($stockSplitDetail->getGoodsItems())){
            foreach($stockSplitDetail->getGoodsItems() as & $g){
                if($checkBalanceGoods && $this->balance_goods[$g->goods_id] < $g->quantity->quantity){
                    throw new ZException("商品".$g->goods_id."拆分数量超出");
                }
            }

            //商品数量减去拆分数量
            foreach($stockSplitDetail->getGoodsItems() as $g){
                $this->balance_goods[$g->goods_id] -= $g->quantity->quantity;
            }
        }

        $this->details[$stockSplitDetail->contract_id] = $stockSplitDetail;

        return true;
    }

    public function getSplitDetail($splitContractId){
        return $this->details[$splitContractId] ?? null;
    }

    /**
     * 移除入库平移明细
     * @param $contractId    合同ID
     */
    public function removeSplitDetail($contractId){
        if(empty($this->details[$contractId])){
            return;
        }

        //加回出入库拆分数量
        foreach($this->details[$contractId]->getGoodsItems() as & $g){
            $this->balance_goods[$g->goods_id] += $g->quantity->quantity;
        }

        unset($this->details[$contractId]);
    }

    /**
     * 添加附件
     * @param Attachment $file
     */
    public function addFile(Attachment $file){
        if(empty($file)){
            ExceptionService::throwArgumentNullException("Attachment对象", array('class' => get_class($this), 'function' => __FUNCTION__));
        }
        $this->files[$file->id] = $file;
    }

    /**
     * 删除附件
     * @param Attachment $file
     */
    public function removeFile(Attachment $file){
        unset($this->files[$file->id]);
    }

    /**
     * 保存
     * @param bool $persistent
     * @return $this
     * @throws \Exception
     */
    public function save($persistent = true){
        if(StockSplitEnum::STATUS_NEW < $this->status){
            $this->status = StockSplitEnum::STATUS_NEW;
        }

        $this->status_time = Utility::getDateTime();
        $this->create_time = $this->status_time;

        if($persistent){
            $this->getStockSplitApplyRepository()->store($this);
        }

        return $this;
    }

    /**
     * 提交
     * @param bool $persistent
     * @return $this
     * @throws \Exception
     */
    public function submit($persistent = true){
        $this->status = StockSplitEnum::STATUS_SUBMIT;
        $this->status_time = Utility::getDateTime();
        $this->update_time = $this->status_time;

        if($persistent){
            $this->getStockSplitApplyRepository()->store($this);
        }

        $this->publishEvent(static::EVENT_AFTER_PASS, new StockSplitSubmitEvent($this));

        return $this;
    }

    /**
     * 审批驳回
     * @param bool $persistent
     * @return $this
     * @throws \Exception
     */
    public function checkBack($persistent = true){
        $this->status = StockSplitEnum::STATUS_BACK;
        $this->status_time = Utility::getDateTime();
        $this->update_time = $this->status_time;

        if($persistent){
            $this->getStockSplitApplyRepository()->store($this);
        }

        $this->publishEvent(static::EVENT_AFTER_BACK, new StockSplitCheckBackEvent($this));

        return $this;
    }

    /**
     * 审批通过
     * @param bool $persistent
     * @return $this
     * @throws \Exception
     */
    public function checkPass($persistent = true){
        $this->status = StockSplitEnum::STATUS_PASS;
        $this->status_time = Utility::getDateTime();
        $this->update_time = $this->status_time;

        if($persistent){
            $this->getStockSplitApplyRepository()->store($this);
        }

        $this->publishEvent(static::EVENT_AFTER_PASS, new StockSplitCheckPassEvent($this));

        return $this;
    }

    /**
     * 是否可编辑
     * @return   boolean
     */
    public function isCanEdit():bool{
        return in_array($this->status, [StockSplitEnum::STATUS_INVALID, StockSplitEnum::STATUS_NEW, StockSplitEnum::STATUS_BACK]);
    }

    /**
     * 是否可提交
     * @return   boolean
     */
    public function isCanSubmit():bool{
        return in_array($this->status, [StockSplitEnum::STATUS_NEW, StockSplitEnum::STATUS_BACK]);
    }

    /**
     * 是否可驳回
     * @return   boolean
     */
    public function isCanBack():bool{
        return in_array($this->status, [StockSplitEnum::STATUS_SUBMIT]);
    }

    /**
     * 是否可通过
     * @return   boolean
     */
    public function isCanPass():bool{
        return in_array($this->status, [StockSplitEnum::STATUS_SUBMIT]);
    }

    /**
     * 是否在审核中
     * @return bool
     */
    public function isOnChecking():bool{
        return in_array($this->status, [StockSplitEnum::STATUS_SUBMIT]);
    }

    /**
     * 是否可查看详情
     * @return bool
     */
    public function isCanView():bool{
        return $this->status >= StockSplitEnum::STATUS_NEW;
    }

    /**
     * 是否未勾选
     */
    public function isSplit():bool{
        return $this->status != StockSplitEnum::STATUS_INVALID;
    }

    public function isStockInSplit():bool{
        return StockSplitEnum::TYPE_STOCK_IN == $this->type;
    }
}