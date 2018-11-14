<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/19 14:56
 * Describe：
 */

namespace ddd\application\contractSettlement;


use ddd\Common\Application\BaseService;
use ddd\domain\entity\contractSettlement\SettlementStatus;

class SettleService extends BaseService
{

    /**
     * 判断结算单是否可以修改，临时性业务逻辑，列表用
     * @param $status
     * @return bool
     */
    public static function settlementIsCanEdit($status)
    {
        return ($status>SettlementStatus::STATUS_STOP && $status<SettlementStatus::STATUS_SUBMIT);
    }
    /**
     * 合同结算DTO赋值
     * @param $DTO  目标DTO
     * @param $post 数据来源
     * @return bool
     */
    public function createNewDTO($BuyContractSettlementDTO,$post,$isFirstSettle)
    {
        //赋值
        $BuyContractSettlementDTO->settle_date = $post['settle_date'];
        $BuyContractSettlementDTO->settle_status = $post['settle_status'];
        $BuyContractSettlementDTO->remark = $post['remark'];
        $BuyContractSettlementDTO->goods_amount = $post['goods_amount'];//货款结算金额
        //print_r($BuyContractSettlementDTO);exit;
        if (!empty($post['goods_arr'])) {
            foreach ($post['goods_arr'] as $key => $value) {
                $goods_arr_dto = $BuyContractSettlementDTO->settlementGoods;
                foreach ($goods_arr_dto as $k => $v) {
                    if ($value['goods_id'] == $v->goods_id) {
                        //属性：商品结算
                        $settlementGoods = $value;
                        unset($settlementGoods['settlementGoodsDetail']);
                        unset($settlementGoods['lading_items']);
                        unset($settlementGoods['order_items']);
                        unset($settlementGoods['settleFile']);
                        unset($settlementGoods['otherFile']);
                        $settlementGoods['quantity'] = new \ddd\domain\entity\value\Quantity($settlementGoods['quantity'], $v->quantity->unit);
                        $settlementGoods['quantity_loss'] = new \ddd\domain\entity\value\Quantity($settlementGoods['quantity_loss'], $v->quantity_loss->unit);
                        if (!$isFirstSettle) unset($settlementGoods['item_id']);
                        $v->setAttributes($settlementGoods);
                        //属性：计算明细
                        $settlementGoodsDetail = $value['settlementGoodsDetail'];
                        unset($settlementGoodsDetail['tax_detail_item']);
                        unset($settlementGoodsDetail['other_detail_item']);

                        $currency_list = \ddd\domain\entity\value\Currency::getConfigs();
                        $adjust_list = \ddd\domain\entity\value\AdjustMode::getConfigs();
                        $settlementGoodsDetail['quantity'] = new \ddd\domain\entity\value\Quantity($settlementGoodsDetail['quantity'], $v->settlementGoodsDetail->quantity->unit);
                        $settlementGoodsDetail['quantity_actual'] = new \ddd\domain\entity\value\Quantity($settlementGoodsDetail['quantity_actual'], $v->settlementGoodsDetail->quantity_actual->unit);
                        $v->settlementGoodsDetail->setAttributes($settlementGoodsDetail);
                        $v->settlementGoodsDetail->currency = $currency_list[$settlementGoodsDetail['currency']];
                        $v->settlementGoodsDetail->adjust_type = $adjust_list[$settlementGoodsDetail['adjust_type']];
                        //属性：税收
                        $subject_list = \ddd\domain\entity\value\Tax::getConfigs();
                        if (!empty($value['settlementGoodsDetail']['tax_detail_item'])) {
                            foreach ($value['settlementGoodsDetail']['tax_detail_item'] as $m => $n) {
                                $DTO = new \ddd\application\dto\contractSettlement\SettlementGoodsDetailItemDTO();
                                $DTO->setAttributes($n);
                                $DTO->subject_list = $subject_list[$DTO->subject_list];
                                $v->settlementGoodsDetail->tax_detail_item[$m] = $DTO;
                            }
                        } else {
                            $v->settlementGoodsDetail->tax_detail_item = array();
                        }
                        //属性：其他费用
                        $subject_list2 = \ddd\domain\entity\value\Expense::getConfigs();
                        if (!empty($value['settlementGoodsDetail']['other_detail_item'])) {
                            foreach ($value['settlementGoodsDetail']['other_detail_item'] as $m => $n) {
                                $DTO = new \ddd\application\dto\contractSettlement\SettlementGoodsDetailItemDTO();
                                $DTO->setAttributes($n);
                                $DTO->subject_list = $subject_list2[$DTO->subject_list];
                                $v->settlementGoodsDetail->other_detail_item[$m] = $DTO;
                            }
                        } else {
                            $v->settlementGoodsDetail->other_detail_item = array();
                        }
                        //lading_items
                        //print_r($v->lading_items);die;
                        if (!empty($v->lading_items)) {
                            foreach ($v->lading_items as $kk => $vv) {

                                $vv_new = array();
                                if (!empty($value['lading_items'])) {
                                    foreach ($value['lading_items'] as $m => $n) {
                                        if ($n['batch_id'] == $vv->batch_id) {
                                            $DTO = new \ddd\application\dto\contractSettlement\SettlementGoodsDTO();
                                            $vv_new = $vv->getAttributes();
                                            $n['in_quantity'] = new \ddd\domain\entity\value\Quantity($n['in_quantity'], $vv_new['in_quantity']->unit);
                                            $n['in_quantity_sub'] = new \ddd\domain\entity\value\Quantity($n['in_quantity_sub'], $vv_new['in_quantity_sub']->unit);
                                            $n['quantity'] = new \ddd\domain\entity\value\Quantity($n['quantity'], $vv_new['quantity']->unit);
                                            $n['quantity_sub'] = new \ddd\domain\entity\value\Quantity($n['quantity_sub'], $vv_new['quantity_sub']->unit);
                                            $n['quantity_loss'] = new \ddd\domain\entity\value\Quantity($n['quantity_loss'], $vv_new['quantity_loss']->unit);
                                            $n['quantity_loss_sub'] = new \ddd\domain\entity\value\Quantity($n['quantity_loss_sub'], $vv_new['quantity_loss_sub']->unit);
                                            $vv_new = array_merge($vv_new, $n);
                                            $DTO->setAttributes($vv_new);
                                            $v->lading_items[$kk] = $DTO;
                                        }
                                    }
                                }

                            }
                        } else {
                            $v->lading_items = array();
                        }
                        //print_r($v->lading_items);die;
                        //order_items
                        if (!empty($v->order_items)) {
                            foreach ($v->order_items as $kk => $vv) {

                                $vv_new = array();
                                if (!empty($value['order_items'])) {
                                    foreach ($value['order_items'] as $m => $n) {
                                        if ($n['order_id'] == $vv->order_id) {
                                            $DTO = new \ddd\application\dto\contractSettlement\SettlementGoodsDTO();
                                            $vv_new = $vv->getAttributes();
                                            $n['out_quantity'] = new \ddd\domain\entity\value\Quantity($n['out_quantity'], $vv_new['out_quantity']->unit);
                                            $n['out_quantity_sub'] = new \ddd\domain\entity\value\Quantity($n['out_quantity_sub'], $vv_new['out_quantity_sub']->unit);
                                            $n['quantity'] = new \ddd\domain\entity\value\Quantity($n['quantity'], $vv_new['quantity']->unit);
                                            $n['quantity_sub'] = new \ddd\domain\entity\value\Quantity($n['quantity_sub'], $vv_new['quantity_sub']->unit);
                                            $n['quantity_loss'] = new \ddd\domain\entity\value\Quantity($n['quantity_loss'], $vv_new['quantity_loss']->unit);
                                            $n['quantity_loss_sub'] = new \ddd\domain\entity\value\Quantity($n['quantity_loss_sub'], $vv_new['quantity_loss_sub']->unit);
                                            $vv_new = array_merge($vv_new, $n);
                                            $DTO->setAttributes($vv_new);
                                            $v->order_items[$kk] = $DTO;
                                        }
                                    }
                                }
                            }
                        } else {
                            $v->order_items = array();
                        }
                        //结算附件
                        /*if(!empty($value['settleFiles'])){
                            foreach ($value['settleFiles'] as $m=>$n){
                                $DTO = new \ddd\application\dto\AttachmentDTO();
                                $DTO->setAttributes($n);
                                $v->settleFiles[$m]=$DTO;
                            }
                        }else{
                            $v->settleFiles=array();
                        }*/
                        //其他附件
                        /*if(!empty($value['goodsOtherFiles'])){
                            foreach ($value['goodsOtherFiles'] as $m=>$n){
                                $DTO = new \ddd\application\dto\AttachmentDTO();
                                $DTO->setAttributes($n);
                                $v->goodsOtherFiles[$m]=$DTO;
                            }
                        }else{
                            $v->goodsOtherFiles=array();
                        }*/

                    }
                }
            }


        }

        //非货款结算
        if (isset($BuyContractSettlementDTO['other_amount'])) {
            $BuyContractSettlementDTO->other_amount = $post['other_amount'];
        }

        if(!empty($post['not_goods_arr'])){
            $new_other_expense=array();
            foreach ($post['not_goods_arr'] as $k=>$v){
                $DTO = new \ddd\application\dto\contractSettlement\SettlementGoodsSubjectDTO();
                $DTO->setAttributes($v);
                $fee_list= \ddd\domain\entity\value\OtherFee::getConfigs();
                $DTO->fee = $fee_list[$v['fee']];
                $currency_list= \ddd\domain\entity\value\Currency::getConfigs();
                $DTO->currency = $currency_list[$v['currency']];
                //单据附件
                if(!empty($v['otherFiles'])){
                    foreach ($v['otherFiles'] as $m=>$n){
                        $attachment = new \ddd\application\dto\AttachmentDTO();
                        $attachment->setAttributes($n);
                        $DTO->otherFiles[$m]=$attachment;
                    }
                }else{
                    $DTO->otherFiles=array();
                }
                $new_other_expense[$k]=$DTO;
                $BuyContractSettlementDTO->other_expense=$new_other_expense;
            }

        }else {
            if(isset($BuyContractSettlementDTO['other_expense']))
            $BuyContractSettlementDTO->other_expense = array();
        }

        return $BuyContractSettlementDTO;
    }
}