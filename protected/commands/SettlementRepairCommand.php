<?php
/**
 * Desc:
 * User:  vector
 * Date: 2018/4/17
 * Time: 14:29
 */

class SettlementRepairCommand extends CConsoleCommand{

    public function actionRepairSettlement()
    {
        echo "===开始修复===\n";
        SettlementRepairService::repairDeliveryActualOutQuantity();
        echo "修复发货单实际出库数量数据完成。。。\n";

        SettlementRepairService::repairLadingSettlement();
        echo "修复提单结算相关数据完成。。。\n";

        SettlementRepairService::repairLadingGoodsSettlement();
        echo "修复提单及货款结算明细相关数据完成。。。\n";

        SettlementRepairService::repairDeliverySettlement();
        echo "修复发货单结算相关数据完成。。。\n";

        SettlementRepairService::repairDeliveryGoodsSettlement();
        echo "修复发货单及货款结算明细相关数据完成。。。\n";

        SettlementRepairService::repairLadingSettlementAttachment();
        echo "修复提单结算附件数据完成。。。\n";

        SettlementRepairService::repairDeliverySettlementAttachment();
        echo "修复发货单附件数据完成。。。\n";

        echo "===修复完成===\n";

    }

    public function actionRepairDeliveryActualOutQuantity()
    {
        SettlementRepairService::repairDeliveryActualOutQuantity();
        echo "修复发货单实际出库数量数据完成。。。\n";
    }

    public function actionRepairLadingSettlement()
    {
        SettlementRepairService::repairLadingSettlement();
        echo "修复提单结算相关数据完成。。。\n";
    }

    public function actionRepairLadingGoodsSettlement()
    {
        SettlementRepairService::repairLadingGoodsSettlement();
        echo "修复提单及货款结算明细相关数据完成。。。\n";
    }

    public function actionRepairDeliverySettlement()
    {
        SettlementRepairService::repairDeliverySettlement();
        echo "修复发货单结算相关数据完成。。。\n";
    }

    public function actionRepairDeliveryGoodsSettlement()
    {
        SettlementRepairService::repairDeliveryGoodsSettlement();
        echo "修复发货单及货款结算明细相关数据完成。。。\n";
    }

    public function actionRepairLadingSettlementAttachment()
    {
        SettlementRepairService::repairLadingSettlementAttachment();
        echo "修复提单结算附件数据完成。。。\n";
    }

    public function actionRepairDeliverySettlementAttachment()
    {
        SettlementRepairService::repairDeliverySettlementAttachment();
        echo "修复发货单附件数据完成。。。\n";
    }
}