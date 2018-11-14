
<?php

/**
*	出库单
*/
class PaytimelinessController extends Controller
{
    public function pageInit() {
       
        $this->filterActions = "index,add,list,export,indexOld";
        $this->rightCode = "paytimeliness";
        $this->authorizedActions=array("list");
    }
    
    /*
     * 显示数据
     * */
    public function actionIndexOld() {
        $attr = $_GET[search];
        $sql = 'select {col} from t_pay_timeliness a 
                 left join t_system_user as u on u.user_id=a.apply_user_id
                 left join t_finance_subject as s on a.subject_id = s.subject_id

                 left join t_system_user as u1 on u1.user_id=a.business_user_id
                 left join t_system_user as u2 on u2.user_id=a.risk_user_id
                 left join t_system_user as u3 on u3.user_id=a.energy_account_user_id
                 left join t_system_user as u4 on u4.user_id=a.factor_account_user_id
                 left join t_system_user as u5 on u5.user_id=a.factor_manager_user_id
                 left join t_system_user as u6 on u6.user_id=a.energy_cashier_user_id
                 left join t_system_user as u7 on u7.user_id=a.factor_cashier_user_id
                 left join t_system_user as u8 on u8.user_id=a.energy_cashier_payment_user_id
        		 ' . $this->getWhereSql($attr) . '
                order by a.id';
        $fieid="a.*,s.name as subject_name,u.name as username,";
        $fieid.="u1.name as business_user_name,u2.name as risk_user_name,u3.name as energy_account_user_name,";
        $fieid.="u4.name as factor_account_user_name,u5.name as factor_manager_user_name,u6.name as energy_cashier_user_name,";
        $fieid.="u7.name as factor_cashier_user_name,u8.name as energy_cashier_payment_user_name,";
        $fieid.="(ifnull(a.contract_check_value,0)+ifnull(a.business_check_value,0)+ifnull(a.risk_check_value,0)+ifnull(a.energy_account_check_value,0)
                +ifnull(a.factor_account_check_value,0)+ifnull(a.factor_manager_check_value,0)+ifnull(a.energy_cashier_check_value,0)
                +ifnull(a.factor_cashier_check_value,0)+ifnull(a.energy_cashier_payment_value,0)
                )as total_time_value";
        $data = $this->queryTablesByPage($sql,$fieid);
        $data['search'] = $attr;
        //print_r($attr);
        $this->render('index_old',$data);
    }
    public function actionIndex() {
        $attr = $_GET[search];
        $sql = 'select {fields} from t_pay_timeliness a
                 left join t_system_user as u on u.user_id=a.apply_user_id
                 left join t_finance_subject as s on a.subject_id = s.subject_id
            
                 left join t_system_user as u1 on u1.user_id=a.business_user_id
                 left join t_system_user as u2 on u2.user_id=a.risk_user_id
                 left join t_system_user as u3 on u3.user_id=a.energy_account_user_id
                 left join t_system_user as u4 on u4.user_id=a.factor_account_user_id
                 left join t_system_user as u5 on u5.user_id=a.factor_manager_user_id
                 left join t_system_user as u6 on u6.user_id=a.energy_cashier_user_id
                 left join t_system_user as u7 on u7.user_id=a.factor_cashier_user_id
                 left join t_system_user as u8 on u8.user_id=a.energy_cashier_payment_user_id
        		 ' . $this->getWhereSql($attr) . '
                order by a.apply_id desc';
        $fieid="a.*,s.name as subject_name,u.name as username,";
        $fieid.="u1.name as business_user_name,u2.name as risk_user_name,u3.name as energy_account_user_name,";
        $fieid.="u4.name as factor_account_user_name,u5.name as factor_manager_user_name,u6.name as energy_cashier_user_name,";
        $fieid.="u7.name as factor_cashier_user_name,u8.name as energy_cashier_payment_user_name,";
        $fieid.="(ifnull(a.contract_check_value,0)+ifnull(a.business_check_value,0)+ifnull(a.risk_check_value,0)+ifnull(a.energy_account_check_value,0)
                +ifnull(a.factor_account_check_value,0)+ifnull(a.factor_manager_check_value,0)+ifnull(a.energy_cashier_check_value,0)
                +ifnull(a.factor_cashier_check_value,0)+ifnull(a.energy_cashier_payment_value,0)
                )as total_time_value";
       
        $dataProvider = new ZSqlDataProvider($sql, array('fields' => $fieid, 'pagination' => array('pageSize' => 20)));
        //print_r($dataProvider);
        $this->render('index',array('dataProvider' => $dataProvider, 'search' => $attr));
    }
    /*
     * 导出excel
     * */
    public function actionExport() {
        $attr =  $_GET;
        $startApplyTime = $attr['startApplyTime'];//申请时间1
        $endApplyTime= $attr['endApplyTime'];//申请时间2
        $subject_id= $attr['subject_id'];//用途
        $apply_id= $attr['apply_id'];//付款编号
        $user_name= $attr['user_name'];//申请人
        $payee= $attr['payee'];//申请人
        $where=array();
        if(!empty($startApplyTime)) $where['a.start_apply_time>']=$startApplyTime;
        if(!empty($endApplyTime)) $where['a.start_apply_time<']=$endApplyTime." 23:59:59";
        if(!empty($subject_id)) $where['a.subject_id']=$subject_id;
        if(!empty($apply_id)) $where['a.apply_id*']=$apply_id;
        if(!empty($user_name)) $where['u.user_name*']=$user_name;
        if(!empty($payee)) $where['a.payee*']=$payee;
        $fieids="a.apply_id as 付款申请编号,u.name as 申请人,s.name as 用途,a.payee as 收款单位,a.start_apply_time as 开始申请时间,a.contract_check_value as 合同物流跟单申请,a.business_check_value as 商务主管审核,
                a.risk_check_value as 风控时效审核,a.energy_account_check_value as 能源会计审核,a.factor_account_check_value as 保理会计审核,a.factor_manager_check_value as 保理板块负责人审核,
                a.energy_cashier_check_value as 能源出纳审核,a.factor_cashier_check_value as 保理出纳审核,a.energy_cashier_payment_value as 能源出纳实付操作,
                a.reject_times as 驳回次数
            ";
       /*  $fieids="a.apply_id,s.name,a.payee,a.contract_check_value,a.business_check_value,
                a.risk_check_value,a.energy_account_check_value,a.factor_account_check_value,a.factor_manager_check_value,
                a.energy_cashier_check_value,a.factor_cashier_check_value,a.energy_cashier_payment_value,
                a.reject_times
            "; */
         $fieids.=",(ifnull(a.contract_check_value,0)+ifnull(a.business_check_value,0)+ifnull(a.risk_check_value,0)+ifnull(a.energy_account_check_value,0)
                +ifnull(a.factor_account_check_value,0)+ifnull(a.factor_manager_check_value,0)+ifnull(a.energy_cashier_check_value,0)
                +ifnull(a.factor_cashier_check_value,0)+ifnull(a.energy_cashier_payment_value,0)
                )as 总时效,";
         $fieids.="u1.name as business_user_name,u2.name as risk_user_name,u3.name as energy_account_user_name,";
         $fieids.="u4.name as factor_account_user_name,u5.name as factor_manager_user_name,u6.name as energy_cashier_user_name,";
         $fieids.="u7.name as factor_cashier_user_name,u8.name as energy_cashier_payment_user_name,";
         
         $fieids.="a.business_check_time,a.risk_check_time,a.energy_account_check_time,a.factor_account_check_time,a.factor_manager_check_time,";
         $fieids.="a.energy_cashier_check_time,a.factor_cashier_check_time,a.energy_cashier_payment_time,a.end_apply_time";
      
         $sql = 'select '.$fieids.' from t_pay_timeliness a
                 left join t_system_user as u on u.user_id=a.apply_user_id
                 left join t_finance_subject as s on a.subject_id = s.subject_id

                 left join t_system_user as u1 on u1.user_id=a.business_user_id
                 left join t_system_user as u2 on u2.user_id=a.risk_user_id
                 left join t_system_user as u3 on u3.user_id=a.energy_account_user_id
                 left join t_system_user as u4 on u4.user_id=a.factor_account_user_id
                 left join t_system_user as u5 on u5.user_id=a.factor_manager_user_id
                 left join t_system_user as u6 on u6.user_id=a.energy_cashier_user_id
                 left join t_system_user as u7 on u7.user_id=a.factor_cashier_user_id
                 left join t_system_user as u8 on u8.user_id=a.energy_cashier_payment_user_id
        		 ' . $this->getWhereSql($where) . '
                order by a.apply_id desc';
        $data = Utility::query($sql);
        //print_r($data);
        $this->exportExcelPay($data);
    }
    //导出excel
    public function exportExcelPay($data)
    {
        if(empty($data)) $this->renderError('数据为空');
        
        $objectPHPExcel = new PHPExcel();
        $objectPHPExcel->setActiveSheetIndex(0);
        $filename = date("Y-m-d",time());
        $objectPHPExcel->getActiveSheet()->setTitle($filename);
        //表头
        $head=array_keys($data[0]);
        foreach(range('A','Z') as $k=>$v){//$v：字母
            if($k<=15){
               $objectPHPExcel->getActiveSheet()->setCellValue($v.'1',$head[$k]);
               $objectPHPExcel->getActiveSheet()->getColumnDimension($v)->setWidth(20);//列宽
               $objectPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
               $objectPHPExcel->getActiveSheet()->getStyle($v.'1')->getFont()->setBold(true);
               $objectPHPExcel->getActiveSheet()->getStyle($v.'1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
        }
        //数据
        $i =   2;
        foreach ( $data as $key => $val ) {
         
            $contract_text=$this->getColText($val,'合同物流跟单申请','end_apply_time','申请人');
            $business_text=$this->getColText($val,'商务主管审核','business_check_time','business_user_name');
            $risk_text=$this->getColText($val,'风控时效审核','risk_check_time','risk_user_name');
            $energy_account_text=$this->getColText($val,'能源会计审核','energy_account_check_time','energy_account_user_name');
            $factor_account_text=$this->getColText($val,'保理会计审核','factor_account_check_time','factor_account_user_name');
            $factor_manager_text=$this->getColText($val,'保理板块负责人审核','factor_manager_check_time','factor_manager_user_name');
            $energy_cashier_text=$this->getColText($val,'能源出纳审核','energy_cashier_check_time','energy_cashier_user_name');
            $factor_cashier_text=$this->getColText($val,'保理出纳审核','factor_cashier_check_time','factor_cashier_user_name');
            $energy_cashier_payment_text=$this->getColText($val,'能源出纳实付操作','energy_cashier_payment_time','energy_cashier_payment_user_name');

             $objectPHPExcel->getActiveSheet()
            ->setCellValue('A'.$i, $val['付款申请编号'])
            ->setCellValue('B'.$i, $val['申请人'])
            ->setCellValue('C'.$i, $val['用途'])
            ->setCellValue('D'.$i, $val['收款单位'])
            ->setCellValue('E'.$i, $val['开始申请时间'])
            ->setCellValue('F'.$i, $contract_text)
            ->setCellValue('G'.$i, $business_text)
            ->setCellValue('H'.$i, $risk_text)
            ->setCellValue('I'.$i, $energy_account_text)
            ->setCellValue('J'.$i, $factor_account_text)
            ->setCellValue('K'.$i, $factor_manager_text)
            ->setCellValue('L'.$i, $energy_cashier_text)
            ->setCellValue('M'.$i, $factor_cashier_text) 
            ->setCellValue('N'.$i, $energy_cashier_payment_text)
            ->setCellValue('O'.$i, $val['驳回次数'])
            ->setCellValue('P'.$i, Utility::timeSpanToString($val['总时效'])); 
             
            $objectPHPExcel->getActiveSheet()->getStyle('F'.$i.':N'.$i)->getAlignment()->setWrapText(true);
            $objectPHPExcel->getActiveSheet()->getStyle('A'.$i.':P'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objectPHPExcel->getActiveSheet()->getStyle('A'.$i.':P'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $i++;
  
        }
        ob_end_clean();
        ob_start();
        
        header('Content-Type : application/vnd.ms-excel');
        header('Content-Disposition:attachment;filename="'.date("Y年m月j日").'.xls"');
        $objWriter= PHPExcel_IOFactory::createWriter($objectPHPExcel,'Excel5');
        $objWriter->save('php://output');
        
    }
    /*
     * 获取换行单元格
     * */
    public function getColText($val,$name,$time_name,$user_name){
        if(empty($val[$name])&&$val[$time_name]=="0000-00-00 00:00:00"){
            return '';
        }
        else{
            $text="";
            $text.=((empty($val[$user_name])||empty($user_name))?'-':$val[$user_name])."\n";
            //$text.=(empty($val[$time_name])?'-':$val[$time_name])."\n";
            $text.=empty($val[$name])?'-':Utility::timeSpanToString($val[$name]);
            return $text;
        }
    }
    /*
     * 计算时效
     * */
    public function actionAdd(){
        //TimesCommand::actionPay();
        $time= new TimesCommand(null,null);
        $status=$time->actionPay();
        if($status) 
            echo json_encode(array('state'=>'0','msg'=>'数据已同步'));
        else 
            echo json_encode(array('state'=>'-1','msg'=>'数据已同步'));
    }
    
}