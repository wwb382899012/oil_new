<?php

/**
* 消息事件队列服务客户端
*	 
*
* @author wilson.song
* @since 2015-01-04
*
*/ 

class MsgQClient extends CApplicationComponent
{
	
	const ACTION_PUSH = '/msgQSrv/push';
	const ACTION_POP  = '/msgQSrv/pop';
	const ACTION_PEEK = '/msgQSrv/peek';


	public $srvAddr = 'http://172.16.1.8';

	/**
	* 写入消息到chn对应的管道中
	*    如果管道不存在，创建
	* 
	* @param $chn 通道key
	* @param $msg 消息内容 
	* @return 成功true 失败false
	*/
	public function push($chn, $msg)
	{
		$curl = Mod::app()->curl ; 

		$ret = $curl->get($this->srvAddr.self::ACTION_PUSH, 
			array(
				'chn'=>$chn,
				'msg'=>$msg
			));

		return $ret === '0';  
	}


	/**
	*  从通道chn中获取队头消息，但不移除
	* 
	* @param $chn 通道key
	* @param $clid 客户端ID，为空时由服务端获取remote_addr
	* @return 成功true 失败false
	*/
	public function peek($chn, $clid=null)
	{
		$curl = Mod::app()->curl ; 

		$ret = $curl->get($this->srvAddr.self::ACTION_PEEK, 
			array(
				'chn'=>$chn,
				'clid'=>$clid
			));

		return $ret;  
	}


	/**
	*  从通道chn中获取队头消息并移除
	* 
	* @param $chn 通道key
	* @param $clid 客户端ID，为空时由服务端获取remote_addr
	* @return 成功true 失败false
	*/
	public function pop($chn, $clid=null)
	{
		$curl = Mod::app()->curl ; 

		$ret = $curl->get($this->srvAddr.self::ACTION_POP, 
			array(
				'chn'=>$chn,
				'clid'=>$clid
			));

		return $ret;  
	}



}