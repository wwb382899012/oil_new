<?php

/**
 * Created by youyi000.
 * DateTime: 2017/4/12 19:45
 * Describe：
 */
class UserIdentity extends CUserIdentity
{

    public static $errors=array(
        self::ERROR_USERNAME_INVALID=>"用户不存在",
        self::ERROR_PASSWORD_INVALID=>"密码不正确",
    );

    private $_id;

    public $user;

    public function authenticate()
    {
        $this->getUser();
        if(empty($this->user))
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        else if($this->user->password!==Utility::getSecretPassword($this->password))
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        else
        {
           $this->afterAuthenticate();
            $this->errorCode=self::ERROR_NONE;
        }

        return !$this->errorCode;
    }


    public function getUser()
    {
        $this->user=SystemUser::model()->findByAttributes(array('user_name'=>$this->username, 'status' => 1));
    }


    public function afterAuthenticate()
    {
        $secret = md5($this->user["user_id"].SystemUser::LoginSecretKey.time());
        Utility::hSetCache(SystemUser::RedisSessionKey,$this->user["user_id"],$secret);
        $this->_id=$this->user->user_id;

        $this->setState('name', $this->user->name);
        $this->setState('user_name', $this->user->user_name);
        $this->setState('main_role_id', $this->user->main_role_id);
        $this->setState('token', $secret);
    }

    public function getId()
    {
        return $this->_id;
    }
}
