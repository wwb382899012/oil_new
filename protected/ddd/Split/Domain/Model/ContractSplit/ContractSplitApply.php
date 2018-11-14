<?php
/**
 * Desc: 合同拆分申请
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 15:39
 */

namespace ddd\Split\Domain\Model\ContractSplit;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\Attachment;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\MathUtility;
use ddd\infrastructure\Utility;
use ddd\Split\Domain\Model\Contract\Contract;
use ddd\Split\Domain\Model\TradeGoods;

class ContractSplitApply extends BaseEntity implements IAggregateRoot{
    #region public property

    /**
     * 标识
     * @var   int
     */
    public $apply_id;

    /**
     * 申请编号
     * @var   string
     */
    public $apply_code;

    /**
     * 合同拆分类型,销售/采购合同
     * @var
     */
    public $type;

    /**
     * 合同id
     * @var   int
     */
    public $contract_id = 0;

    /**
     * 合同编号
     * @var   string
     */
    public $contract_code;

    /**
     * 生效时间
     * @var   datetime
     */
    public $effect_time;

    /**
     * 合同平移
     * @var   array
     */
    protected $contract_split_items = [];

    /**
     * 出入库单平移
     * @var   array
     */
    public $stock_split_items = [];

    /**
     * 状态
     * @var   int
     */
    public $status = 0;

    /**
     * 状态日期
     * @var   datetime
     */
    public $status_time;

    /**
     * 附件
     * @var   array
     */
    protected $files = [];

    /**
     * 备注
     * @var   varchar
     */
    public $remark;

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

    #endregion

    #region protected property

    /**
     * 剩余商品信息
     * @var   array
     */
    protected $balance_goods = [];

    /**
     * 所有的出入库拆分明细
     * @var array
     */
    private $all_stock_split_detail_entities = [];

    /**
     * 合同平移索引,对应 ContractSplit 的 split_id 属性
     * @var   int
     */
    public $itemKey = 0;

    use ContractSplitApplyRepository;

    #endregion

    #region Event

    /**
     * 提交事件之后
     */
    const EVENT_SUBMITTED = "onSubmitted";

    /**
     * 驳回事件之后
     */
    const EVENT_REJECTED = "onRejected";

    /**
     * 通过事件之后
     */
    const EVENT_PASSED = "onPassed";

    #endregion

    #region Event

    /**
     * 事件配置，事件名必须以on开头，否则无效
     * @return array
     */
    protected function events(){
        return [static::EVENT_SUBMITTED, static::EVENT_REJECTED, static::EVENT_PASSED,];
    }

    #endregion

    #region id

    /**
     * 获取id
     * @return int
     */
    public function getId(){
        return $this->apply_id;
    }

    /**
     * 设置id
     * @param $value
     */
    public function setId($value){
        $this->apply_id = $value;
    }

    #endregion

    #region 合同平移

    /**
     * 获取合同平移
     * @return array
     */
    public function getContractSplits(){
        return $this->contract_split_items;
    }

    #endregion

    #region 出入库平移

    /**
     * 获取出入库平移
     * @return array
     */
    public function getStockSplits():array{
        return $this->stock_split_items;
    }

    /**
     * 获取有效的出入库平移
     */
    public function getEffectiveStockSplits():array{
        $stockSplitEntities = [];
        foreach($this->stock_split_items as $key => & $stockSplitEntity){
            if($stockSplitEntity->isEffective()){
                $stockSplitEntities[$key] = $stockSplitEntity;
            }
        }

        return $stockSplitEntities;
    }

    /**
     * 获取拆分出入库ids
     * @return array
     */
    public function getAllStockSplitBillIds():array{
        $bill_ids = [];
        foreach($this->stock_split_items as & $stockSplitEntity){
            $bill_ids[(string) $stockSplitEntity->bill_id] = (string) $stockSplitEntity->bill_id;
        }
        return $bill_ids;
    }

    /**
     * 获取有效的拆分出入库ids
     * @return array
     */
    public function getEffectiveStockSplitBillIds():array {
        $bill_ids = [];
        foreach($this->stock_split_items as & $stockSplitEntity){
            if($stockSplitEntity->isEffective()){
                $bill_ids[(string) $stockSplitEntity->bill_id] = (string)$stockSplitEntity->bill_id;
            }
        }

        return $bill_ids;
    }

    /**
     * 是否有效的出入库拆分信息
     */
    public function isEffectiveStockSplitBill(string $billId):bool{
        $bill_ids = $this->getEffectiveStockSplitBillIds();

        return isset($bill_ids[(string)$billId]);
    }

    /**
     * 获取所有出、入库单的拆分商品总数量
     * @return array
     */
    public function getAllEffectiveStockSplitGoodsQuantities(){
        $effectiveStockSplits = $this->getEffectiveStockSplits();
        if (\Utility::isEmpty($effectiveStockSplits)) {
            return [];
        }

        $goods_quantities = [];

        //获取各商品总量
        foreach ($effectiveStockSplits as $splitId => & $stockSplitEntity) {
            foreach ($stockSplitEntity->getDetails() as & $splitDetailEntity) {
                foreach($splitDetailEntity->getGoodsQuantities() as $goods_id => $quantity){
                    $tmp_quantity = $goods_quantities[$goods_id] ?? 0;
                    $goods_quantities[$goods_id] = MathUtility::add($tmp_quantity, $quantity);
                }
            }
        }

        return $goods_quantities;
    }

    /**
     * 获取某个出、入库单的总拆分数量
     * @param $billId
     * @return array
     */
    public function getEffectiveStockSplitGoodsQuantities($billId):array{
        $effectiveStockSplits = $this->getEffectiveStockSplits();
        if (\Utility::isEmpty($effectiveStockSplits)) {
            return [];
        }

        $need_deduct_quantities = [];
        foreach ($effectiveStockSplits as $splitId => & $stockSplitEntity) {
            foreach ($stockSplitEntity->getDetails() as & $splitDetailEntity) {
                $goodsQuantities = $splitDetailEntity->getGoodsQuantities();
                foreach($goodsQuantities as $goods_id => $quantity){
                    $tmp_quantity = $need_deduct_quantities[$splitDetailEntity->bill_id][$goods_id] ?? 0;
                    $need_deduct_quantities[(string)$splitDetailEntity->bill_id][$goods_id] = MathUtility::add($tmp_quantity, $quantity);
                }
            }
        }

        return $need_deduct_quantities[(string) $billId] ?? [];
    }

    #endregion

    public function rules(){
        return array(array('apply_code', 'required'), array("contract_id", 'numerical', 'integerOnly' => 'true', 'min' => 1), array('contract_code', 'length', 'max' => 32),);
    }

    #region 编号

    /**
     * 生成编号
     */
    public function generateCode(){
        $serial = \IDService::getSerialNum(__CLASS__.'.'.$this->contract_id);
        $this->apply_code = $this->contract_code.'-PY'.$serial;
    }

    #endregion

    #region 对象

    /**
     * 创建对象
     * @param    Contract $contract
     * @param    array $stockBills
     * @return   static
     * @throws   \Exception
     */
    public static function create(Contract $contract, array $stockBills = array()){
        if(empty($contract)){
            ExceptionService::throwArgumentNullException("Contract对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }

        if(!$contract->isCanContractSplit()){
            ExceptionService::throwBusinessException(BusinessError::Contract_Cannot_Split_Apply, array('contract_code' => $contract->contract_code));
        }

        $entity = new static();
        $entity->generateCode();
        $entity->contract_id = $contract->contract_id;
        $entity->contract_code = $contract->contract_code;
        $entity->status = ContractSplitApplyEnum::STATUS_NEW;

        if(\Utility::isNotEmpty($contract->goods_items)){
            $entity->initBalanceGoods($contract);
            $contractSplit = ContractSplit::create($contract);
            $entity->addContractSplit($contractSplit);
        }

        if(\Utility::isNotEmpty($stockBills)){
            foreach($stockBills as $stockBill){
                $stockSplit = StockSplit::create($stockBill, $entity->itemKey);

                $entity->addStockSplit($entity->itemKey, $stockSplit);
            }
        }

        return $entity;
    }

    #endregion

    #region 附件

    /**
     *
     */
    public function clearFiles(){
        $this->files = [];
    }

    public function getFiles(){
        return $this->files;
    }

    /**
     * 添加附件
     * @param    Attachment $file
     * @return   bool
     * @throws   \Exception
     */
    public function addFile(Attachment $file){
        if(empty($file)){
            ExceptionService::throwArgumentNullException("Attachment对象", array('class' => get_class($this), 'function' => __FUNCTION__));
        }
        $this->files[$file->id] = $file;

        return true;
    }

    public function removeFile($id){
        unset($this->files[$id]);
    }

    #endregion

    #region 合同平移

    /**
     * 添加合同平移信息
     * @param ContractSplit $contractSplit
     * @param $splitId
     * @param bool $checkBalanceGoods
     * @throws ZException
     */
    public function addContractSplit(ContractSplit $contractSplit, $splitId, $checkBalanceGoods= true){
        if(empty($contractSplit)){
            ExceptionService::throwArgumentNullException("ContractSplit对象", array('class' => get_class($this), 'function' => __FUNCTION__));
        }

        if ($this->status != ContractSplitApplyEnum::STATUS_PASS) {//已经审核通过  无需再判断
            if (is_array($contractSplit->goods_items)) {
                if($checkBalanceGoods){
                    foreach ($contractSplit->goods_items as $g) {
                        if ($this->balance_goods[$g->goods_id] < $g->quantity->quantity) {
                            throw new ZException("商品" . $g->goods_id . "拆分数量超出");
                        }
                    }
                }

                //商品数量减去拆分数量
                foreach ($contractSplit->goods_items as $g) {
                    $this->balance_goods[$g->goods_id] -= $g->quantity->quantity;
                }
            }
        }

        $this->contract_split_items[$splitId] = $contractSplit;
    }

    /**
     * 清空合同平移
     */
    public function clearContractSplitItems(){
        $this->contract_split_items = [];
    }

    /**
     * 清空出入库平移
     */
    public function clearStockSplitItems(){
        $this->stock_split_items = [];
    }

    /**
     * 移除合同平移信息
     * @param    int $splitId
     */
    public function removeContractSplit($splitId){
        if(empty($this->contract_split_items[$splitId])){
            return;
        }

        //加回合同拆分数量
        if(\Utility::isNotEmpty($this->balance_goods)){
            foreach($this->contract_split_items[$splitId]->goods_items as $g){
                $this->balance_goods[$g->goods_id] += $g->quantity->quantity;
            }
        }

        //移除对应的出入库拆分明细
        if(is_array($this->stock_split_items)){
            unset($this->stock_split_items[$splitId]);
        }
        unset($this->contract_split_items[$splitId]);
    }

    #endregion

    #region 出入库单平移

    /**
     * 出入库平移信息是否已存在
     * @param $billId
     * @return bool
     */
    public function stockSplitIsExists($billId){
        return isset($this->stock_split_items[$billId]);
    }

    /**
     * 添加出入库单平移信息
     * @param StockSplit $stockSplit
     * @throws \Exception
     */
    public function addStockSplit(StockSplit $stockSplit){
        if(empty($stockSplit)){
            ExceptionService::throwArgumentNullException("StockSplit对象", array('class' => get_class($this), 'function' => __FUNCTION__));
        }

        if($this->stockSplitIsExists($stockSplit->bill_id)){
            ExceptionService::throwBusinessException(BusinessError::Stock_Split_Is_Exists, array('bill_code' => $stockSplit->bill_code));
        }

        $this->stock_split_items[$stockSplit->bill_id] = $stockSplit;
    }

    /**
     * 获取拆分合同下的所有出入库拆分明细
     * @param $splitId
     * @return array|mixed
     */
    public function getEffectiveStockSplitDetailEntities($splitId):array {
        if(\Utility::isNotEmpty($this->all_stock_split_detail_entities)){
            return $this->all_stock_split_detail_entities[$splitId] ?? [];
        }

        $stockSplitDetailEntities = [];
        foreach($this->stock_split_items as $bill_id => & $stockSplitEntity){
            if(!$stockSplitEntity->isEffective()){
                continue;
            }

            foreach($stockSplitEntity->getDetails() as $split_id => $stockSplitDetailEntity){
                $stockSplitDetailEntities[$stockSplitDetailEntity->split_id][$stockSplitDetailEntity->bill_id] = $stockSplitDetailEntity;
            }
        }
        $this->all_stock_split_detail_entities = $stockSplitDetailEntities;

        return $stockSplitDetailEntities[$splitId] ?? [];
    }

    /**
     * 获取拆分合同下的出入库拆分明细商品数量总和
     * @param $splitId
     * @return array
     */
    public function getEffectiveContractSplitGoodsQuantities($splitId):array{
        $stockSplitDetailEntities = $this->getEffectiveStockSplitDetailEntities($splitId);
        if(\Utility::isEmpty($stockSplitDetailEntities)){
            return [];
        }

        $goods_quantities = [];
        foreach($stockSplitDetailEntities as & $stockSplitDetailEntity){
            foreach($stockSplitDetailEntity->goods_items as $goods_id => & $tradeGoodsEntity){
                $goods_quantities[$goods_id] = ($goods_quantities[$goods_id] ?? 0) + $tradeGoodsEntity->quantity->quantity;
            }
        }

        return $goods_quantities;
    }

    /**
     * 移除出入库平移信息
     * @param    int $billId
     * @return   bool
     */
    public function removeStockSplit($billId){
        unset($this->stock_split_items[$billId]);

        return true;
    }

    #endregion

    #region 是否可操作

    /**
     * 是否可编辑
     * @return   boolean
     */
    public function isCanEdit(){
        return $this->status < ContractSplitApplyEnum::STATUS_SUBMIT;
    }

    /**
     * 是否可提交
     * @return   boolean
     */
    public function isCanSubmit(){
        return in_array($this->status, array(ContractSplitApplyEnum::STATUS_BACK, ContractSplitApplyEnum::STATUS_NEW));
    }

    #endregion

    #region 操作

    /**
     *
     * @throws   \Exception
     */
    public function save(){
        if(!$this->isCanEdit()){
            ExceptionService::throwBusinessException(BusinessError::Contract_Split_Apply_Cannot_Edit);
        }

        $this->effect_time = Utility::getNow();
        $this->status = ContractSplitApplyEnum::STATUS_NEW;
        $this->status_time = Utility::getNow();

        return $this->getContractSplitApplyRepository()->store($this);
    }

    /**
     * 提交
     * @param    boolean $persistent
     * @throws   \Exception
     */
    public function submit($persistent = true){
        if(!$this->isCanSubmit()){
            ExceptionService::throwBusinessException(BusinessError::Contract_Split_Apply_Cannot_Submit);
        }

        $this->effect_time = Utility::getNow();
        $this->status = ContractSplitApplyEnum::STATUS_SUBMIT;
        $this->status_time = Utility::getNow();
        if($persistent){
            $this->getContractSplitApplyRepository()->submit($this);
        }
        $this->publishEvent(static::EVENT_SUBMITTED, new ContractSplitApplySubmittedEvent($this));
    }

    /**
     * 审批驳回
     * @param    boolean $persistent
     * @throws   \Exception
     */
    public function reject($persistent = true){
        $this->status = ContractSplitApplyEnum::STATUS_BACK;
        $this->status_time = Utility::getNow();
        if($persistent){
            $this->getContractSplitApplyRepository()->reject($this);
        }

        $this->publishEvent(static::EVENT_REJECTED, new ContractSplitApplyRejectedEvent($this));
    }

    /**
     * 审批通过
     * @param    boolean $persistent
     * @throws   \Exception
     */
    public function checkPass($persistent = true){
        $this->status = ContractSplitApplyEnum::STATUS_PASS;
        $this->status_time = Utility::getNow();
        if($persistent){
            $this->getContractSplitApplyRepository()->checkPass($this);
        }

        $this->publishEvent(static::EVENT_PASSED, new ContractSplitApplyPassedEvent($this));
    }

    /**
     * 设置为可进行出入库拆分
     * @param bool $persistent
     * @throws \Exception
     */
    public function setIsCanStockSplit($persistent = true){
        $this->status = ContractSplitApplyEnum::STATUS_CAN_STOCK_SPLIT;
        $this->status_time = Utility::getNow();
        if($persistent){
            $this->getContractSplitApplyRepository()->store($this);
        }
    }

    /**
     * 废弃
     * @param bool $persistent
     * @throws \Exception
     */
    public function trash($persistent = true){
        $this->status = ContractSplitApplyEnum::STATUS_TRASH;
        $this->status_time = Utility::getNow();
        if($persistent){
            $this->getContractSplitApplyRepository()->store($this);
        }
    }

    #endregion

    #region 剩余商品数

    public function getBalanceGoods(){
        return $this->balance_goods;
    }

    /**
     * 初始化可拆分商品数量
     * @param Contract $contract
     */
    public function initBalanceGoods(Contract $contract){
        $this->balance_goods = [];
        if(is_array($contract->goods_items)){
            foreach($contract->goods_items as $g){
                $this->balance_goods[$g->goods_id] = $g->quantity->quantity;
            }
        }
    }

    #endregion

    /**
     * 增加合同拆分的商品
     * @param TradeGoods $goods
     * @param $splitId
     * @param bool $checkBalanceGoods
     * @throws ZException
     */
    public function addContractSplitGoods(TradeGoods $goods, $splitId, $checkBalanceGoods = true){
        if(!isset($this->contract_split_items[$splitId])){
            throw new ZException("合同平移不存在");
        }

        $this->contract_split_items[$splitId]->addGoodsItem($goods);

        if(\Utility::isNotEmpty($this->balance_goods)){
            if(!key_exists($goods->goods_id, $this->balance_goods)){
                throw new ZException("合同拆分申请中不存在商品".$goods->goods_id);
            }

            if($checkBalanceGoods && ($goods->quantity->quantity > $this->balance_goods[$goods->goods_id])){
                throw new ZException("商品".$goods->goods_id."拆分数量超出");
            }

            $this->balance_goods[$goods->goods_id] -= $goods->quantity->quantity;

        }
    }

    /**
     * 增加出入库拆分商品
     * @param $billId
     * @param TradeGoods $goods
     * @param $splitId
     * @throws ZException
     */
    public function addStockSplitGoods($billId, $splitId, TradeGoods $goods){
        if(!isset($this->contract_split_items[$splitId])){
            throw new ZException("合同平移不存在");
        }

        if(!isset($this->stock_split_items[$splitId][$billId])){
            throw new ZException("出入库平移:".$billId."不存在");
        }

        $this->stock_split_items[$splitId][$billId]->addStockSplitGoods($splitId, $goods);
    }

    /**
     * 拆分申请是否在审核中
     * @return bool
     */
    public function isOnChecking(){
        return $this->status == ContractSplitApplyEnum::STATUS_SUBMIT;
    }

    /**
     * 拆分申请是否审核通过
     */
    public function isCheckPass(){
        return $this->status == ContractSplitApplyEnum::STATUS_PASS;
    }

    /**
     * 是否能废弃
     * @return bool
     */
    public function isCanTrash(){
        return $this->status < ContractSplitApplyEnum::STATUS_SUBMIT;
    }

    /**
     * 是采购合同
     * @return bool
     */
    public function isBuyContract(){
        return ContractSplitApplyEnum::CONTRACT_TYPE_BUY == $this->type;
    }
}
