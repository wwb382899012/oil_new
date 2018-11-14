<?php

class Email 
{
    /**
     * 发送邮件
     * @param $toEmail
     * @param $subject
     * @param $content
     * @param array $attachArray
     * @throws Exception
     */
	public static function sendEmail($toEmail ,$subject, $content,$attachArray=array())
	{
        if(empty($toEmail))
            throw new Exception("收件人Email为空");

        $toArray = array();
        $emailArr = explode(",", $toEmail);
        foreach ($emailArr as $key => $value) {
            $toArray[] =  array('address'=>$value, 'name'=>$value);
        }
        Mod::log("@@@email attachment:".json_encode($attachArray), 'error');
        $fromArray =  array('from'=>'system@jyblife.com', 'from_name'=>'石油系统');
        Mod::app()->mail->smtp_username = "system@jyblife.com" ;
        Mod::app()->mail->smtp_password = "Mail123!";
        //Mod::app()->mail->simple_send(array($toEmail), $subject, $content);
        Mod::app()->mail->send($toArray, $subject, $content,$attachArray,$fromArray);

		/*try
		{

			return true;
		}
		catch (Exception $e)
		{
			Mod::log('邮件发送失败,message:'.$e->getMessage(),'error');
			return false;
		}*/
	}


    /**
     * 给指定用户发送邮件
     * @param $userId
     * @param $subject
     * @param $content
     * @param $attachArray
     * @return bool
     */
	public static function sendToUser($userId,$subject,$content,$attachArray=array())
	{
        try
        {
            Email::sendEmail(SystemUser::getEmail($userId),$subject,$content,$attachArray);
            return true;
        }
        catch (Exception $e)
        {
            Mod::log('邮件发送失败，错误:'.$e->getMessage(),'error');
            return false;
        }

	}


}


