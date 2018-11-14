<?php

/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2016/1/13
 * Time: 17:30
 * Describe：
 */
class TestController extends Controller
{
    public function init()
    {
         $this->authorizedActions=array("index","out","fileName","error","upload","update","redis","getRedis","grid","test","clear","excel","compute","d","ui","encrypt","queryAutoPaymentStatus");
    }

    public function actionOut()
    {
        $projectId = 20180227004;
        $goodsId = 36;
        $goods = ContractGoods::model()->find(array("condition"=>"project_id=".$projectId." and goods_id=".$goodsId,"order"=>"create_time desc"));
        echo '<pre>';
        print_r($goods);
        echo '</pre>';
    }

    public function actionError()
    {
        $this->layout="new_main";
        $this->render("/layouts/new_error");
        // $this->render("/site/new_error",["error"=>["code"=>1111,"message"=>"测试"]]);
    }

    public function actionFileName()
    {
        $filePath = $_GET['file_path'];
        $fileName = $_GET['file_name'];
        $pathArr   = pathinfo($filePath); 
        $dirname   = $pathArr['dirname'];
        $extension = strtolower($pathArr['extension']);
        $fileName  = quotemeta(basename($fileName,".".$pathArr['extension']));

        $destFile  = $dirname . '/' . $title . '-' . $fileName . "." . $extension;
        echo $destFile;
        // $res = copy($filePath, $destFile);
        // if($res)
        //     echo $destFile;
        // else
        //     echo $fileName;
    }

    public function actionD()
    {
        $q1=new \ddd\Common\Domain\Value\Quantity(10);
        $q2=new \ddd\Common\Domain\Value\Quantity(20);
        $q3=(int)$q2;
        $qe=$q1+$q2;
        var_dump($qe);
        return;

        //对称加密解密测试
        $securityManager = Mod::app()->getSecurityManager();

        $data=["amount"=>10000,"name"=>"中优国聚"];
        $key="youyi000youyi000";
        $key2="youyi000youyi002";
        $encrypted = base64_encode($securityManager->encrypt(json_encode($data),$key));
        echo $encrypted;

        $data=$securityManager->decrypt(base64_decode($encrypted),$key);
        var_dump($data);
        $data=json_decode($data);
        var_dump($data);

        return;


        $ladingBillRepository=new \ddd\repository\LadingBillRepository();
        $entity=$ladingBillRepository->findByPk("201802080001");
        $entity->lading_date='2018-01-29';
        $ladingBillRepository->store($entity);

        return;

        $dto=new \application\dto\stock\LadingBillDTO();
        $dto->fromEntity($entity);
        var_dump($dto->validate());
        $dto->batch_date='2018-01-01';
        var_dump($dto->validate());
        var_dump($entity);

        return;

        $repository=new \repository\ContractRepository();
        $entity=$repository->findByPk("766");
        if($entity->validate())
            echo "OK";
        else
        {
            echo "No";
            var_dump($entity->getErrors());
        }

        var_dump($entity);
        return;




    }

    public function actionUpdate()
    {
        $projects=Project::model()->with("contracts")->findAll();
        foreach ($projects as $p)
        {
            if(empty($p->contracts))
                continue;
            foreach ($p->contracts as $c)
            {
                if($c->status>=Contract::STATUS_SUBMIT)
                {
                    $p->updateByPk($p->project_id,array(
                        "is_can_back"=>0,
                    ));
                    break;
                }
            }
        }
    }

    public function actionClear()
    {
        Subject::clearCache();
        ActionService::clearActionsCache();
        SystemUser::clearCache();
        Storehouse::clearCache('activeData');
        echo "OK";
    }

    public function actionRedis()
    {
        $this->render('redis');
    }

    public function actionGetRedis()
    {
        $params=$_POST["obj"];
        $action=ActionService::getAction(1);

        if(!empty($params["key"]))
        {
            //$redis = Mod::app()->redis;
            if(!empty($params["field"]))
                $res=Utility::hGetCache($params["key"],$params["field"]);
            else
                $res=Utility::getCache($params["key"]);


            $this->returnSuccess(json_encode($res));
        }

        $this->returnError("缺少必要参数");
    }

    public function testEvent($event)
    {
        echo "OK";
        //var_dump($event);
        echo $event->sender->contract_id;
    }

    public function actionIndex()
    {
        $s_params = [
            'version' => '2.0',
            'system_flag' => Mod::app()->params['system_flag'],
            'secret' => "ebokTXvd2dcBkZy1sPHxtoA4HiLYR\/7Fxg9XRMyES\/d6XkPjWmo6FkBZfpmwvxbbMblbpD5AxXGVmUI6ZCGsyeWvmL+IEfrtJFnJMgXiLnC0vvH\/Wt2ai4aVJdPiTBz6RJaAdD9iHvoG6aZRYCwfMS1LBz5yaPvejdKwEWE\/llY=h4\/lw5aEPTlLa2HvHHjbrcundPCWw3vy\/5qQX2Qpy3Wj19uELkPCXJnWWz6outQ7D9FQR9FBant2o6iOfl7c3Lx8PLxwEwPaxj6j8o7nIiP46RK9u+FHk4MRHsNRQr0ZhD+9xhTTedVlYZ1TWufJkytvpyw1AMnT3TvhEi98P\/Y=RGim75nkgImf4wMPj4cLThlA40vKKIti4uRvJGakjO94nmvKvoWS0YChJScafrmrO7LRwe1ye+2yALptEoYuZBLndVPNoeHTuuSzjdyZ6Q9nnlfGr1pdDYbCQkC6eQfhd3ZhAgdfAisxDpgS+SWHayfdhN3MMIczuEmh1YnkTBM=cWj3fkBPMXXk9K0Tp+dbnpNHskeKYAGcGou9n7XwQtVgb8IXA5J+ecXXMJjhRR9OlOoIu7Whx6MvDflt6943s1T7ZLDorkVt7IzMNMX2xOIYjRJHjq1wpYEy2Boa\/Z5ADaYcbRCWhHATv0i1wTU1E7JrOqjtfaBE1tBugIScPog=ToAOZCMl4eTRg0norCAiDwIoGKzb7MrOHz0+l5eOy5+UOcTrJ\/yLIrNf\/VLHpICYneduJmnIyD+aiLKLfE28OphFzyngWeIrTfY9bdlc9II98baX27kscq55mRLoUMRZtVtqJ0gopCXxaWephOvo273gy+pR5C6ROSpDbFviANY=WaHV8vyzCFo7xHYteDnJRk7617mjm3EBF\/OhxUFFdCo292alvmpinMG0DliD1NG1Jb29eTe0LlmH1A3l4d0Gb+JuawocuZCfZgCU2lmP+ZKDK7FEpATo\/viJdIPacjC290Q5mku1NCnp9vJ9ctcPbtGnEzpm2IsYtgnJnd4szRA=MJb5oPa9cM7fAWGifVOLuUBqMNdoQRCWn+kTe82ZR97E8GEOHAcbIGQdB6ArqkQuzEvw1BthMNk\/0ww9Fv53f8v54erb0DhRnL+2eJzOW6IDFzoVflfDj+gcP8dh1CcUzxI5XI6J9zlNbhy4j7kHrn5pBNATFGSlS1mAg72N2QQ=",
        ];

        $req = [
            "service" => "com.jyblife.logic.bg.layer.HttpAccessLayer",
            'targetService' => 'com.jyblife.logic.bg.order.PayOrder',
            'env' => 'dev',
            'group' => '*',
            'method' => 'access',
            'params' => $s_params,
            'version' => '1.0.0',
            'set' => ''
        ];

        $url = Mod::app()->params['money_url'];
        $curl = curl_init();
        $headers = ['Frame-type:JMF'];
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($req));
        $res = curl_exec($curl);
        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ', 资金系统接口调用url：' . $url . '，调用参数：' . json_encode($req) . '，结果：' . $res, CLogger::LEVEL_ERROR);
        curl_close($curl);
        die;

        $this->layout="new_main";
        $this->render("newUI");
        return;

        $entity=\ddd\domain\entity\project\ProjectDetail::create();
        echo get_class($entity);

        return;
        echo \ddd\domain\iRepository\contract\ITradeGoodsRepository::class;
        $r=\ddd\infrastructure\Utility::getDIContainer()->get(\ddd\repository\contract\TradeGoodsRepository::class);
        $model=$r->findByPk(1);

        var_dump($model);

        return;

        //Mod::$container->set('T1',"T1");
        $t=Mod::$container->get("T1");
        $t->echoDB();
        return;

         var_dump(Contract::class);
         echo \ddd\domain\entity\contract\Contract::class;
         return;

        try{
            \ddd\infrastructure\error\ExceptionService::throwModelDataNotExistsException(1,"Test");
            //throw new \ddd\infrastructure\error\ZException("Test");
        }
        catch (Exception $e)
        {
            /*echo $e->toString();
            $t=(string)$e;
            var_dump($t);*/
            var_dump($e);
        }

        return;
       /* $obj=new \ddd\domain\entity\stock\LadingBill();
        var_dump($obj->getAttributes());
        return;*/


        /*$s="a";
        echo is_string($s);
        return;*/

        $contract=\ddd\repository\contract\ContractRepository::repository()->findByPk("183");
        var_dump($contract->getAttributes());
//        $service=new \ddd\domain\service\contract\ContractService();
//        $service->contractSubmit($contract);


        //\domain\event\BaseEvent::unSerialize("");
        /*
         * 测试事件
        $obj=new Contract();

        $obj->onAfterSave= array($this, 'testEvent');

        $obj=Contract::model()->findByPk(766);
        $obj->onAfterSave= array($this, 'testEvent');
        $obj->save();
        echo $obj->contract_id;*/

        $s=9.800000;
        echo number_format($s,2);


        return;

        $stat=array();
        for ($i=6;$i>=0;$i--)
        {
            $d=Utility::getDate("-".$i." days");
            echo $d."# ";
            $stat[$d]=0;
        }

        $d=strtotime("-0 days");
        echo date("Y-m-d",$d);
        //echo date("Y-m-d",$d+date('Z'));
        //echo date('Z');

        //$file=ROOT_DIR."/201712130047.pdf";
        /*$file="/data/oil/upload/contractFile/201711170001-201711170100/201711170005/201711170005_11_1510903604_crr4yd.pdf";
        $mime=Utility::getFileMIME($file);

        echo $mime;*/

        //Utility::outputFile($file);
        return;

        $config=array(
            array(
                "next_node_id"=>46,
                "field"=>array("key1"=>"amount_factoring"),
                "condition"=>"#key1# >500000000"
            ),
        );

        echo json_encode($config);
        return;


        $str="[{\"next_node_id\":33,\"field\":{\"key1\":\"extra['items']['1']\",\"key2\":\"extra['items']['2']\",\"key3\":\"extra['items']['3']\",\"key4\":\"extra['items']['4']\",\"key5\":\"extra['items']['5']\"},\"condition\":\"(#key1# == 1 || #key2#==1 || #key3#==1 || #key4#==1 || #key5#==1)\"}]";
        $config=json_decode($str,true);
        var_dump($config);
        $config=array(

  array(
      "next_node_id"=>"33",

    "field"=>
    array(
                "key1"=> "extra['items']['1']"
    ),
    "condition"=> "(#key1# == 1 || #key2#==1 || #key3#==1 || #key4#==1 || #key5#==1)"
    )
    );
        /*$config=array(
            array(
                "next_node_id"=>14,
                "field"=>array("key1"=>"extra['items']['1']",
                               "key2"=>"extra['items']['2']",
                               "key3"=>"extra['items']['3']",
                               "key4"=>"extra['items']['4']",
                               "key5"=>"extra['items']['5']",
                ),
                "condition"=>array(
                    "(#key1# == 1 || #key2#==1 || #key3#==1 || #key4#==1 || #key5#==1)"
                )
            ),
        );*/

        //echo json_encode($config);

return;

        $contract=Contract::model()->findByPk("44");
        if($contract->is_main)
            $contract->findRelative();
        echo $contract->contract_code;
        echo $contract->relative->contract_code;

        return;

        $task=Task::model()->with("action")->findByPk(294);
        $users=UserService::getUserByRoleId($task->role_id,$task->corporation_id);

        var_dump($users);

        return;

        $modelClass="PayApplication";
        $obj=$modelClass::model()->with("extra")->findByPk("2017110300002");


        var_dump( $obj->extra->items);
        $name="extra['items']['1']";
        //$a="\$obj->".$name;
        //echo $$a;
        //$res=call_user_func($a);
        $res=eval('return $obj->'.$name.";");
        echo $res;
        return;
        //echo $obj->$name;
        echo $obj->extra['items']['1'];
        echo $obj->apply_id;

        $config=array(
            array(
                "next_node_id"=>14,
                "field"=>array("key1"=>"extra['items']['1']",
                               "key2"=>"extra['items']['2']",
                               "key3"=>"extra['items']['3']",
                               "key4"=>"extra['items']['4']",
                               "key5"=>"extra['items']['5']",
                               ),
                "condition"=>array(
                    "(#key1# == 1 || #key2#==1 || #key3#==1 || #key4#==1 || #key5#==1)"
                )
            ),
        );

        //echo json_encode($config);


       /*$v=array( "field"=>"ab.ee" ,"type"=>"number/string","symbol"=>"==","value"=>"1");
       $value=10;
        $str="".$value." ".$v['symbol']." ".$v['value'].";";
        eval('$res='.$str);

        echo $str;
        echo $res;*/

        return;


        $obj=new StockNoticeDetail();
        $r=$obj->getActiveRelation("sub");
        var_dump($r);

        /*$obj=new StockNoticeDetail();
        $obj->unit=1;
        $obj->quantity=100;
        $obj->quantity_sub=200;
        $obj->unit_sub=2;
        $obj->goods_id=1;
        $obj->save();

        return;*/


        $str="price_sub";
        $b=strpos("_sub",$str);
        echo $b;
        $a=substr($str,strpos($str,"_sub"));
        echo $a;


        array(
            array("id"=>1,"name"=>"XXX","value"=>""),
            array("id"=>2,"name"=>"XXX","value"=>""),
        );
        $model=SystemUser::model()->findByPk(2);
        echo $model->primaryKey;
        $this->render('index');
    }

    public function actionUpload()
    {

        $this->render('file');
    }

    public function actionExcel()
    {
        $this->render('index');
    }

    public function actionSaveFile()
    {
        $file=$_FILES["files"];
        $file2=$_FILES["files2"];


        if (empty($file) && empty($file2))
        {
            $this->returnError("文件不能为空！");
        }

        echo $file["name"][0]."#########1";
        echo $file2["name"][0]."#########2";
    }


    function myFunction($a,$b)
    {
        echo $a;
        echo $b;
        if ($a===$b)
        {
            return 0;
        }
        return ($a>$b)?1:-1;
    }

    public function actionTest()
    {
        $this->render("t2");
        /*$map= include(ROOT_DIR . "/protected/components/Map_old.php");
        $busContracts=$map["business_contract_attachment_type"];
        $filePath   = "/data/oil/upload/contract/20170118001-20170118100/20170118021/20170118021_103_1484747379_hlbit2.docx";
        // /data/oil/upload/contract/20170118001-20170118100/20170118021/20170118021_101_1484747379_hlbit2.docx
        // /data/oil/upload/contract/20170118001-20170118100/20170118021/20170118021_88.docx
        $suffix     = basename($filePath);
        $extension  = strtolower(pathinfo($suffix, PATHINFO_EXTENSION));
        $prefix     = strstr($suffix,'_',true);
        $prePath    = substr($filePath,0,strrpos($filePath,'/'));
        $type       = substr($suffix,strpos($suffix,'_')+1,3);
        $mapName    = $busContracts[$type]['name'];
        echo $prePath.'/'.$prefix.'_'.$mapName.'.'.$extension; */
    }


    public function actionSummary()
    {
        $map= include(ROOT_DIR . "/protected/components/Map_old.php");
        $sql = "select a.project_name,b.* 
                from t_project a 
                left join t_project_log b on a.project_id=b.project_id order by a.project_id,b.create_time asc";
        $data = Utility::query($sql);
        $tmpArr = array();
        $timeArr = array();
        $dArr = array();
        $backArr = array(23,43,81);
        $i=1;
        foreach ($data as $key => $value) {
            $tmpArr[$value['project_id']]['project_id'] = $value['project_id'];
            $tmpArr[$value['project_id']]['project_name'] = $value['project_name'];
            if(empty($tmpArr[$value['project_id']]['back'])){
                $tmpArr[$value['project_id']]['back']='';
            }
            if(in_array($value['old_value'],$backArr)){
                $tmpArr[$value['project_id']]['back']='驳回';
            }
            //$tmpArr[$value['project_id']]['old_value'.$i]= $map['project_status'][$value['old_value']];
           
            $tmpArr[$value['project_id']]['状态'.$i]= $map['project_status'][$value['new_value']];
            //$timeArr[$value['project_id']][] = "";
            $timeArr[$value['project_id']]['project_id'] = "";
            $timeArr[$value['project_id']]['project_name'] = "";
            $timeArr[$value['project_id']]['back'] = "";
            $timeArr[$value['project_id']]['time'.$i] = $value['create_time'];
            if($data[$key]['project_id']==$data[$key+1]['project_id']){
                $i++;
            }else{
                $i=1;
            }
        }

        foreach ($tmpArr as $key => $value) {
            $dArr[] = $value;
            $dArr[] = $timeArr[$key];
        }
        //print_r($dArr);die;

        $this->exportExcel($dArr,"石油系统项目统计明细");
    }

    /*public function actionQuery()
    {
        $context    = $_REQUEST['keyword'];
        $apiKey     = 'ae7b7645d0784635b734d39758ca168b';
        // $url        = "http://i.yjapi.com/ECISimple/Search?key=$apiKey&keyword=$context";
        $url        = "http://i.yjapi.com/ECI/GetDetailsByName?key=$apiKey&keyword=$context";
        //$url = Mod::app()->params["alarm_url"]."/collect?id=$alarmId&txt=$context";

        $curl = Mod::app()->curl;
        try{
            $res = $curl->get($url);
            print_r($res);die;
            Mod::log("Alarm Log, params is ".$context.", and result is ".$res);
            $result = json_decode($res, true);
            foreach ($result as $key => $value) {
                $data[$key]['name'] = $value['Result']['Name'];
                $data[$key]['credit_code'] = $value['Result']['CreditCode'];
                $data[$key]['registration_code'] = $value['Result']['No'];
                $data[$key]['corporate'] = $value['Result']['OperName'];
                $data[$key]['start_date'] = date_format(date_create($value['Result']['StartDate']),'Y-m-d');
                $data[$key]['address'] = $value['Result']['Address'];
                $data[$key]['registration_authority'] = $value['Result']['BelongOrg'];
                $data[$key]['registered_capital'] = $value['Result']['RegistCapi'];
                $data[$key]['business_scope'] = $value['Result']['Scope'];
                $data[$key]['registered_capital'] = $value['Result']['RegistCapi'];
                $data[$key]['runs_state'] = $value['Result']['Status'];
                $data[$key]['ownership'] = $value['Result']['EconKind'];
                $values[]= "('". implode("','", $data[$key]) . "')";
            }
        }
        catch(Exception $e)
        {
            Mod::log("Alarm error, params is ".$context.", and error message is ".$e->getMessage(),"error");
            return array("code"=>-1,"msg"=>$e->getMessage());
        }
        
    }*/

    public function actionFind()
    {
        $name = $_REQUEST['name'];
        $res = Partner::model()->find("name='".$name."'");
        print_r($res->attributes);
    }

    public function actionDate()
    {
        $start_date = "2005-09-02T00:00:00+08:00";
        $result = date_format(date_create($start_date),'Y-m-d');
        print_r($result);
        
    }

    public function actionStatus()
    {
        $map    = include(ROOT_DIR . "/protected/components/Map_old.php");
        $runArr = $map["run_status"];
        $status = '吊销';
        if(!empty($status)){
            foreach ($runArr as $key => $value) {
                if($value==$status)
                    $run_status = $key;
            }
        }
        print_r($run_status);
    }


    public function actionGetPartner()
    {
        $name = trim($_REQUEST['name']);
        $pInfo = PartnerService::getPartnersInfo($name);
        print_r($pInfo);
    }

    public function actionGetDays()
    {
        //$time   = $_REQUEST['time'];
        $time = '2017-02-20 18:30:13';
        //echo $time;die;
        $result = self::calcDays($time);
        var_dump($result);
    }

    public function calcDays($create_time)
    {
        $updateDays = Mod::app()->params["updatePartnerInfoDays"];
        $datetime1  = new DateTime(date_format(date_create($create_time),'Y-m-d'));
        $datetime2  = new DateTime(date('Y-m-d'));
        $interval   = $datetime1->diff($datetime2);
        $diffDays   = $interval->format('%a');
        // echo $diffDays;
        if($diffDays > $updateDays){
            return true;
        }else{
            return false;
        }
        /*$d1     = strtotime(date_format(date_create($create_time),'Y-m-d'));
        $d2     = strtotime(date('Y-m-d'));
        $diffDays   = round(($d2-$d1)/3600/24);
        if($diffDays > $updateDays){
            return true;
        }else{
            return false;
        }*/
    }

    public function actionWx()
    {
        //$user=SystemUser::model()->findByPk(2);
        //WeiXinService::createWeiXinUser($user);
        WeiXinService::send("2","测试");

        //$wx = Mod::app()->weiXinUser;
        //$wx->delete(1);

        /*$wx = Mod::app()->weiXin;
        $wx->getUsers();*/
        /*$wx = Mod::app()->weiXin;
        echo($wx->corp_id);*/
        /*$value['name']='债权转让合同_2016-02-18.docx';
        $mapValue   = substr($value['name'],0,strrpos($value['name'],'.'));
        print_r($mapValue);*/
        //print_r(pathinfo('债权转让合同(2016)-签订版.docx',PATHINFO_FILENAME));
    }


    public function actionClearUserCache(){
        SystemUser::clearUserCache();
    }

    /**
     * 复制t_partner表数据到t_partner_apply
     */
    public function actionCopyData()
    {
        $sql = "select * from t_partner order by partner_id asc";
        $data = Utility::query($sql);
        foreach ($data as $key => $value) {
            $partner = PartnerApply::model()->findByPk($value['partner_id']);
            if(empty($partner->partner_id)){
                $partner = new PartnerApply();
            }
            $partner->setAttributes($value,false);
            $partner->save();
        }
        echo "数据复制完成！";
        // print_r($data);die;
    }

    public function actionTestWX()
    {
        TaskService::addTasks(3,'20170509002',4,0,2);
    }

    public function actionTrans()
    {
        return 1;
        $isInDbTrans = Utility::isInDbTrans();
        if (!$isInDbTrans) {
            $trans = Utility::beginTransaction();
        }

        try {
            $sql = "insert into t_task(action_id,key_value,status,status_time) values(3,'20170510008',0,now())";
            Utility::executeSql($sql);
            $keyValue = '20170510008';
            $taskArr = Task::Model()->findAll('key_value=' . $keyValue);
            Mod::log('测试#####：' . json_encode($taskArr));
            echo "完成了啦啦啦啦啦";
            if (!$isInDbTrans) {
                $trans->commit();
            }
        } catch (Exception $e) {
            if (!$isInDbTrans)
                try {
                    $trans->rollback();
                } catch (Exception $ee) {
                }

            Mod::log("生成借款合同：" . $supOrdId . "的资产标的出错", "error");
            // Utility::alarm("生成借款合同：".$supOrdId."的资产标的出错",Utility::ALARM_LEVEL_ERROR);
            return Utility::getReturnError("插入数据库出错");
        }
    }


    public function actionCopyPartnerToCredit()
    {
        return 1;
        $sql = "select * from t_partner order by partner_id asc";
        $data = Utility::query($sql);
        foreach ($data as $key => $value) {
            $credit = PartnerCredit::model()->findByPk($value['partner_id']);
            if (empty($partner->partner_id)) {
                $credit = new PartnerCredit();
            }
            $credit->partner_id = $value['partner_id'];
            $credit->credit_amount = 0;
            $credit->use_amount = 0;
            $credit->frozen_amount = 0;
            $credit->status = 1;
            $credit->status_time = date("Y-m-d H:i:s");
            $credit->create_user_id = 0;
            $credit->create_time = date("Y-m-d H:i:s");
            $credit->update_user_id = 0;
            $credit->update_time = date("Y-m-d H:i:s");
            // $partner->setAttributes($value,false);
            $credit->save();
        }
        echo "Credit数据复制完成！";
    }


    public function actionMap()
    {
        $map = include(ROOT_DIR . "/protected/components/Map.php");
        print_r(Map::$v);die;
    }

    public function actionTest001()
    {
        print_r(SystemUser::getAllEmail(3));
    }

    public function actionTest002()
    {
        phpinfo();
    }

    public function actionTest003()
    {
        ActionService::clearActionsCache();
    }


    public function actionTest004()
    {
        $obj = ContractFile::model()->updateByPk(201709210003, 
        array(
            'status' => 3, 
            'update_user_id'=> Utility::getNowUserId(),
            'update_time'=> new CDbExpression('now()')
            )
        );
        print_r($obj);die;
        // print_r(33333);die;
        // Email::sendEmail("wen.he@jyblife.com,yueyun00@163.com","testing....","hello world!!!");
    }


    public function actionTest005()
    {
        $result = Goods::getActiveTreeTable();
        print_r($result);
    }

    public function actionTest006()
    {
        ActionService::clearActionsCache();
    }


    public function actionGrid()
    {

        /*$dataProvider = new CActiveDataProvider('SystemUser', array(
            'pagination'=>array(
                'pageSize'=>10,
            ),
            'sort'=>array(
                'defaultOrder'=> array('user_id'=> CSort::SORT_DESC),
            )
        ));*/

        /*$dataProvider = new CActiveDataProvider('Contract', array(
            'pagination'=>array(
                'pageSize'=>10,
            ),
            'criteria'=>array(
                //'condition'=>$condition,
                //'order'=>'create_time DESC',
                //'with'=>array('author'),
            ),
            'totalItemCount'=>10000,
            'sort'=>array(
                'defaultOrder'=> array('contract_id'=> CSort::SORT_DESC),
            )
        ));*/

        $contractSearch=new ContractSearch();
        $dataProvider=ContractSearch::search(array());

        $this->render('grid', array(
            'dataProvider' => $dataProvider,
        ));
    }

    public function actionTest007()
    {   
        /*$d=ContractSettlementGoodsDetail::model()->find(121);
        var_dump($d);die;
        $lading = new \ddd\domain\entity\stock\LadingBill();
        $settle = \ddd\domain\entity\contractSettlement\LadingBillSettlement::create($lading);
        print_r($settle);die;*/
        /*$id = '201712110001';
        $with = array('stockIn','stockIn.files');
        $model = StockNotice::model()->findByPk($id);*/
        /*$id = 244;
        $with = array('project');
        $model = Contract::model()->with($with)->findByPk($id);
        print_r($model);die;
        // print_r($model);die;
        foreach ($model->stockIn as $s) {
            foreach ($s->files as $f) {
                print($f->type);
            }
        }*/

        /*$id = '201802050009';
        $lading = DeliverySettlementDetail::model()->find('t.order_id='.$id);
        print_r($lading);die;*/


        /*$currency = array("id" => 2,"name"=>"美元", "ico"=>"$");
        print_r($currency[id]);die;*/

        print_r(\ddd\domain\IRepository\contract\IContractRepository::class);die;

        $bill = new \ddd\domain\entity\stock\LadingBill();
        $isCan = \ddd\domain\service\stock\LadingBillService::isCanSettle($bill);
        var_dump($isCan);die;
        // $lading = \ddd\domain\entity\contractSettlement\LadingBillSettlement::create($bill);

        // $item = \ddd\domain\entity\contractSettlement\OtherExpenseSettlementItem::create();
        // print_r($item);die;
    }


    public  function  actionTest008()
    {
       /*$arr = SettlementRepairService::repairLadingSettlementAttachment();
       echo '<pre/>';
       print_r($arr);
       echo '<pre/>';*/
        BlockChainService::contractBlock(1210);
    }


    public function actionKO()
    {
        $class = new ReflectionClass(\ddd\domain\entity\contractSettlement\OtherExpenseSettlementItem::class);
        $names = array();
        foreach ($class->getProperties() as $property)
        {
            $name = $property->getName();
            if ($property->isPublic() && !$property->isStatic())
                $names[] = $name;
        }
        $defaults=[];
        $lines=[];
        foreach ($names as $n)
        {
            $defaults[]='"'.$n.'":"",';
            $lines[]="self.".$n."=ko.observable(o.".$n.");";
        }
        echo implode("\r\n",$defaults);
        echo "\r\n\r\n\r\n";
        echo implode("\r\n",$lines);
    }

    public function actionUi()
    {

        $this->layout = "new_main";
        $this->render("newUI");
    }

    public function actionEncrypt()
    {
        $securityManager = Mod::app()->getSecurityManager();
        $data = json_encode([
            "system_flag"=> "test",
            "out_order_num"=> "finan20180302000001",
            "order_pay_type"=> 1,
            "pay_main_body_uuid"=> "buss1",
            "pay_bank_name"=> "招商银行",
            "pay_bank_account"=> "1111111111111111",
            "collect_main_body"=> "加油宝科技平台",
            "amount"=> 111,
            "timestamp"=> 1519981745,
            "secret"=> "aaabbadsfadsfadfasdfadsf"
        ]);
        $key = "aaabbadsfadsfadfasdfadsf";
        $encrypted = base64_encode($securityManager->encrypt($data, $key));
        echo $encrypted;
        echo "\n";
        $decrypted = $securityManager->decrypt(base64_decode($encrypted), $key);
        print_r(json_decode($decrypted, true));
        die;
    }

    public function actionQueryAutoPaymentStatus()
    {
        $applyId = Mod::app()->request->getParam('id');
        if (Utility::checkQueryId($applyId) && $applyId > 0) {
           $apply = PayApplication::model()->findByPk($applyId);
           if (!empty($apply)) {
               $params = ['out_order_num' => $applyId];
               AMQPService::publishForQueryAutoPayStatus($params);
           }
        }
    }
}
