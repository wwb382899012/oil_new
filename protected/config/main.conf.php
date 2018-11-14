<?php
/*
 *CWebapplication的配置文件,所有的配置都在此配置
 *
 */
define('LOG_DIR', realpath(dirname(__FILE__).'/../runtime'));
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'石油管理系统V2.0',
	'id'=>'NewOil',
	'charset'=>'utf-8',	
	'language'=>'zh_cn',
	'preload'=>array('log'),

	'import'=>array(
		'application.models.*',
        //'application.models.domain.*',
		'application.components.*',
	    'application.commands.*',
        'application.components.weixin.*', 
		'application.services.*',
        'application.flow.*',
        'application.task.*',
        'application.baseControllers.*',
        'application.extensions.PHPExcel.*',
        'application.attachment.*',
        //'application.components.grid.*',
        'application.search.*',
        //'application.ddd.*',
        'application.cmd.*',
	),

    'modules'=>[
        'api'=>['class'=>'application.api.ApiModule',],
        /*'gii'=>[
                'class'=>'system.gii.GiiModule',
                'password'=>'123456'
            ]*/
    ],

	// 组件配置, 通过key引用（如：Mod::app()->bootstrap);
	'components'=>array(
		//url管理组件
		'urlManager'=>array(
			'urlFormat'=>'path',
			//要不要显示url中的index.php
			'showScriptName' => false,
			//url对应的解析规则,类似于nginx和apache的rewite,支持正则
			'rules'=>array(
                '<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>'=>'<module>/<controller>',
                '<module:\w+>'=>'<module>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
        'errorHandler'=>array(
            'errorAction'=>'site/error',
        ),
        'user' => array(
            'class'=>'WebUser',
        ),
		'curl'=>array(
			'class'=>'system.components.curl.CUrl',
			'options'=>array(
        		CURLOPT_TIMEOUT => 60,
			)
		),
		'mail'=>array(
			'class'=>'system.components.mailer.CWaeMailer',
		),
		//微信企业号用户管理
        'weiXinUser'=>array(
            'class'=>'application.components.weixin.ZWeixinUser',
            'corp_id'=>'ww0f15b7e86e3ad77c',
            'secret'=>'Rdbsjpww9FvR-mypu-lG8M03fkdoeoH3I4rZuG-8Yok',
        ),
        'weiXinOauth'=>array(
            'class'=>'application.components.weixin.ZWeixinOauth',
            'corp_id'=>'ww0f15b7e86e3ad77c',
            'secret'=>'Ys78-X8qxCwEtIHynNYJzTEMX0B4HDOkSPBeV58pLZg',
        ),
        //微信企业号消息发送
        'weiXinMsg'=>array(
            'class'=>'application.components.weixin.ZWeixinMsg',
            'corp_id'=>'ww0f15b7e86e3ad77c',
            'secret'=>'Ys78-X8qxCwEtIHynNYJzTEMX0B4HDOkSPBeV58pLZg',
            'agent_id'=>'1000002',
        ),
        'format'=>array(
            'class'=>'application.components.grid.ZFormatter',
        ),
	),
	"params"=>array(
        "modPath" => "/data/www/framework",
		//"modPath" => "../framework_php7",
        "url_host"=>"oil.cyblive.com",//
        "oil_web_url"=>"http://oil.web.jtjr.com/dist/public_dev/index.html",
		// "oil_web_url"=>"http://172.16.22.110:8097/",
        "oil_web_manifest" =>"/public/manifest.json",
        "systemId"=>"11",//系统类别id，主要是针对系统模块表中
        "prefix"=>"new_oil_",//cookies等的前缀名
        "wx_agent_id"=>"1000002",//微信企业号agentId
        "weiXin_url"=>"http://172.16.1.16:9001",
        "serverEncode"=>"UTF-8",
        "isSaveActionLog"=>"1",//是否记录系统操作日志，1为异常记录，2为同步实时记录，0为不记录
        "updatePartnerInfoDays"=>"60",//更新合作方信息天数
        "alarmChaChaBalance"=>"100",//企查查剩余查询次数小于或等于100次发邮件告警
        "isAutoUpdateWeixinCorp"=>"1",//是否自动更新微信企业号用户
        "factor_code_server_id" => "2", //保理编号服务器区分
	),
    'di'=>require(dirname(__FILE__).'/di.config.php'),
);

