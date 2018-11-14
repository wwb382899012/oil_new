<?php
/**
* 工具命令行，提供常用的系统维护工具
*
* 
*/
class ToolCommand extends CConsoleCommand
{

    public function actionRemind($taskId)
    {
        $params=array(
            "task_id"=>$taskId
        );
        AMQPService::publishReminder($params);
    }

    public function actionSecret()
    {
        $users=SystemUser::model()->findAll("");
        foreach ($users as $u)
        {
            $u->password=md5($u->password);
            $u->save();
        }
    }

    public function actionClear()
    {
        $obj=new SystemModule();
        $obj->clearCache();
    }

    public function actionEmail()
    {
        AMQPService::publishEmail(2,"Test","Test");
    }


    public function actionTEmail()
    {
        $toArray = array();
        $toEmail="songjun.zhang@jyblife.com";
        $toArray[] =  array('address'=>$toEmail, 'name'=>$toEmail);
        $fromArray =  array('from'=>'system@jyblife.com', 'from_name'=>'石油系统');
        Mod::app()->mail->smtp_username = "system@jyblife.com" ;
        Mod::app()->mail->smtp_password = "Mail123!";
        Mod::app()->mail->simple_send(array($toEmail),  "Test2", "test2");
        Mod::app()->mail->send($toArray, "Test1", "test1",array(),$fromArray);
    }

}

