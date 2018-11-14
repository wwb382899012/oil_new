<?php
class TestCommand extends CConsoleCommand
{

    public function actionToPdf($file)
    {
        Utility::wordToPdf($file);
    }

    public function actionEmail()
    {
        Email::sendEmail("songjun.zhang@jyblife.com","test","test");
    }


	public function actionTest()
	{

	    $s=getenv('MOD_ENV');
	    var_dump($s);
	    return;

		/*echo Mod::getLocalIp();
		echo round(4000,-3);*/

        //bind($queue, $exchange, $routingKey = "")
        $queue=Mod::app()->amqp->bind("risk.test","user.direct","regist");

        $data=$queue->get();//参数为AMQP_AUTOACK是默认自动移动队列
        var_dump($data);
        //$data2=$queue->get();
        //var_dump($data2);

        //移除队列
        $queue->ack($data->delivery_tag);
        //$data3=$queue->get();
        //var_dump($data3);
        //$queue->ack($data2->delivery_tag);
	}

    /*
     * get后的对象，不是数组
     * object(AMQPEnvelope)#16 (18) {
  ["body"]=>
  string(9) "test09883"
  ["content_type"]=>
  string(10) "text/plain"
  ["routing_key"]=>
  string(6) "regist"
  ["delivery_tag"]=>
  int(1)
  ["delivery_mode"]=>
  int(1)
  ["exchange_name"]=>
  string(11) "user.direct"
  ["is_redelivery"]=>
  int(0)
  ["content_encoding"]=>
  string(0) ""
  ["type"]=>
  string(0) ""
  ["timestamp"]=>
  int(0)
  ["priority"]=>
  int(0)
  ["expiration"]=>
  string(0) ""
  ["user_id"]=>
  string(0) ""
  ["app_id"]=>
  string(0) ""
  ["message_id"]=>
  string(0) ""
  ["reply_to"]=>
  string(0) ""
  ["correlation_id"]=>
  string(0) ""
  ["headers"]=>
  array(0) {
  }
}
     * */

    public function actionPublish($message)
    {
        $name="user.direct";
        $exchange=Mod::app()->amqp->exchange($name, $type = AMQP_EX_TYPE_DIRECT);

        //$exchange->publish($message, $routingKey=null, $flags = AMQP_NOPARAM);
        $exchange->publish($message, "regist", $flags = AMQP_NOPARAM);
    }

   /* public function actionUpdateBroke($org_id)
    {
        //$org_id=123;
        $sql="select tc.id"
            ." from t_agent_org_mb taom,t_cust tc"
            ." where taom.cust_id=tc.id and tc.type=1 ";
        if(isset($org_id) && $org_id!="" && $org_id!=0)
            $sql .=" and exists(select org_id from t_agent_org where org_id=taom.org_id and (pids like '%," . $org_id . ",%' or org_id=" . $org_id . "))";
        //$sql .=" order by id desc";

        $db=Mod::app()->db ;
        $command = $db->createCommand($sql);
        $data= $command->query()->readAll();
        foreach ($data as $v) {
            sleep(1);
            TaskFactory::addSettleCust($v["id"]);
        }

    }*/

    public function actionTestMsgQ()
    {
        $msgq = Mod::app()->MsgQ;
        $msgq->push("test","a1");
        $msgq->push("test","a2");
        $msgq->push("test","a3");
        $msgq->push("test","a4");
        $msgq->push("test","a5");

        sleep(10);

        /*$msg1 = $msgq->peek("test");
        $msg2=$msgq->pop("test");*/

        //echo $msg1." ---- ".$msg2."\r\n";
        echo $msgq->pop("test")."\r\n";
    }

    /**
     * @desc 修复合同表中交易金额
     */
    /*public function actionRepairContractAmount() {
        $contracts = Contract::model()->findAll();
        if(Utility::isNotEmpty($contracts)) {
            foreach ($contracts as $key => $row) {
                $contract = Contract::model()->findByPk($row['contract_id']);
                $sql = 'select sum(amount) as total_amount, sum(amount_cny) as total_amount_cny from t_contract_goods where contract_id = ' . $row['contract_id'];
                $res = Utility::query($sql);
                if(Utility::isNotEmpty($res)) {
                    $contract->amount_cny = $res[0]['total_amount_cny'];
                    $contract->amount = $res[0]['total_amount'];
                    $contract->save();
                }
                unset($contract);
            }
        }
    }*/

    /**
     * @desc 修复合同组表数据
     */
    /*public function actionRepairContractGroup() {
        $projects = Project::model()->findAll('status >= :status', array('status' => Project::STATUS_SUBMIT));
        if(Utility::isNotEmpty($projects)) {
           foreach ($projects as $project) {
               ContractService::initContractGroupByProject($project);
           }
        }
//        $contracts = Contract::model()->findAll('is_main = 0 or (is_main = 1 and type = 1)');
        $mainContracts = Contract::model()->findAll('is_main = 1 group by project_id');
        $subContracts = Contract::model()->findAll('is_main = 0');
        $contracts = array_merge_recursive($mainContracts, $subContracts);
        if(Utility::isNotEmpty($contracts)) {
            foreach ($contracts as $contract) {
                if($contract->is_main == 1) {
                    $contract->relative = ContractService::getRelatedContract($contract->contract_id);
                }
                ContractService::generateContractGroup($contract);
            }
        }
    }*/

    /**
     * @desc 生成分配数据
     * @param string $date
     */
    public function actionComputeProfit($date = '')
    {
        if(empty($date)) {
            $date=Utility::getDate("-1 days");
        }
        ProfitService::computeProfit($date);
    }
}
