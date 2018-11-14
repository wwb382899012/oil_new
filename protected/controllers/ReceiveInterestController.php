<?php
/**
 * @author 	vector
 * @date 	2018-06-11
 * @desc 	收款占用利息报表 	
 */

class ReceiveInterestController extends InterestReportController 
{
    public function pageInit() {
		$this->rightCode     = "receiveInterest";
		$this->filterActions = "";
		$this->newUIPrefix   = 'new_';
		$this->type          = ConstantMap::SALE_TYPE;
    }
    
}