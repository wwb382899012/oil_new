<?php

class UpdateDeliverySettlementCommand extends CConsoleCommand
{
    public function actionUpdateDeliveryAmountCny() {
        $deliveries = DeliverySettlementDetail::model()->with('contract')->findAll();

        foreach ($deliveries as $delivery) {
            $delivery->amount_cny = $delivery->amount * $delivery->contract->exchange_rate;
            $delivery->update(array('amount_cny'));
        }
    }

}