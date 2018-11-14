<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/5 15:50
 * Describe：
 */
class CheckFactory{
    public static function getInstance($businessId){


        switch($businessId)
        {
            case 1:
                return new Check1();
                break;
            case 2:
                return new Check2();
                break;
            case 3:
                return new Check3();
                break;
            case 4:
                return new Check4();
                break;

            case 30:
                return new Check30();
                break;

            case 31:
                return new Check31();
                break;

            case 7:
                return new Check7();
                break;

            case 8:
                return new Check8();
                break;

            case 9:
                return new Check9();
                break;

            case 10:
                return new Check10();
                break;
            case 11:
                return new Check11();
                break;
            case 12:
                return new Check12();
                break;

            case 13:
                return new Check13();
                break;

            case 14:
                return new Check14();
                break;
                
            case 15:
                return new Check15();
                break;

            case 16:
                return new Check16();
                break;

            case 17:
                return new Check17();
                break;

            case 18:
                return new Check18();
                break;

            case 19:
                return new Check19();
                break;

            case FlowService::BUSINESS_STOCK_OUT_CHECK:
                return new Check20();
                break;
                
            case FlowService::STOCK_CONTRACT_SETTLEMENT_CHECK:
                return new Check21();
                break;
            case FlowService::DELIVERY_CONTRACT_SETTLEMENT_CHECK:
                return new Check22();
                break;
            case FlowService::BUSINESS_CONTRACT_SPLIT_CHECK:
                return new Check23();
                break;
            case FlowService::BUSINESS_STOCK_SPLIT_CHECK:
                return new Check24();
                break;
            case FlowService::BUSINESS_CONTRACT_TERMINATE_CHECK:
                return new Check25();
                break;
        }
    }
}