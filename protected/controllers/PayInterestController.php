<?php
/**
 * @author 	vector
 * @date 	2018-06-07
 * @desc 	付款占用利息报表 	
 */

class PayInterestController extends InterestReportController 
{
    public function pageInit() {
		$this->rightCode     = "payInterest";
		$this->filterActions = "createData";
		$this->newUIPrefix   = 'new_';
		$this->type          = ConstantMap::BUY_TYPE;
    }
    
}