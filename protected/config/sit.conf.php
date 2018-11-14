<?php
return array(

    'params'=>array(
        "url_host"=>"oil7sit.jyblife.com",
        "weiXin_url"=>"http://oil7sit.jyblife.com",//微信页面的URL
        "oil_web_manifest" =>"/public_sit/manifest.json",
        "block_interface_url"=>"http://106.75.176.29/api/contract", //区块链接口地址
        "block_browser_url"=>"http://106.75.176.29/fisco-bcos-browser",//区块链浏览器地址
        "modPath" => "/data/www/framework_php7",
		"money_system_config" => [
            "system_flag" => "NewOil", //系统标识
            "money_url" => "http://106.75.133.50:36003/",  //资金系统接口地址
            "money_secret_key" => "f903f61a8510ae43ed00a70f3169fc0b4f03888d",  //资金系统密钥
            "money_private_key" => 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAK2cL2ih3UyjMZDyDP28oE1+3RzpSwMxhEfacr5i+Z0Z2cC6TFi2nTjo6o7/2v8DIz2ZWa/k8femiCMm/rnpIJ2oQOvwNDYPjaFIuIqyUCuN8cZt3e1ghdgJS88VWL1s+vf5v0G2kxK0/Y7wfd13MtKFr4gxeNETykh6hMG6TWNdAgMBAAECgYB6ycx+JH1whru32Hp3u8FlDiU1HYuAZrU4XLhrD3WcN3xbY2g8Fmx8o7/CBBPP6VgzaRKV5Ud98Lq4ogvnUYtOy0euyVNqgnyWF3xOuoCNRG3Y3nmN8N4CgMzJOlIEaYOyJRns7qJ0H/RCJy/6cHS/HVgl+ipCc6XL/4xmTBeSAQJBAOTulYuVsfaoVKWaQeB2kaejPgMnV7qhyWTW5LHYCm11Qarn6Oizl+EMavQSJNv2attJGq9yoQO9NDED4ue0CSkCQQDCIxgAC4pAF3HRO5aLmhxNXrgRLwyIZgbfZs9PHbwf6Ifd11Xt3YU977cN594/moS6FuD9vK9SuQs+f923IOsVAkEAjx9JhvaTR+183ftObBI0hWVdA4O5KQi0a8KdP0IdYskHwN0zkyeUMDIfO2+Mc6ferjFJ6Z30Y+4Jjwsq9Eht4QJAZP966fMW/pbz2KWGgaQwWzQO0KnIfGGP68OB0KgoifUgUhJIGxKm0f0XH73kSvSpXmKutHLoR0ILjn1ZLH+MrQJAXdc5Za2x/IVELR4YMovafS0FWBPeSXV5ULGzrB57E2wbdpDsmc8IIhcs5ApjTDJp69Ulom1oHVdTOQT0ip5WIw==',
            "money_public_key" => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCtnC9ood1MozGQ8gz9vKBNft0c6UsDMYRH2nK+YvmdGdnAukxYtp046OqO/9r/AyM9mVmv5PH3pogjJv656SCdqEDr8DQ2D42hSLiKslArjfHGbd3tYIXYCUvPFVi9bPr3+b9BtpMStP2O8H3ddzLSha+IMXjRE8pIeoTBuk1jXQIDAQAB',
            "auto_payment_currency" => [1], 
		],
        "delay_amqp_url"=>"http://10.13.56.73:19532/",//延时队列
    ),

	'components'=>array(
		'db'=>array(
			'class'=>'CDbConnection',
			'charset' => 'utf8',
			'tablePrefix' => 't_',
			'connectionString' => 'mysql:host=10.13.56.73;port=3306;dbname=db_new_oil',
			'username' => 'root',
			'password' => 'sit1234',
			'retryCount' => '1',
            'schemaCachingDuration'=>60,//metadata缓存时间，单位s
		),
		'dbSlave'=>array(
			'class'=>'CDbConnection',
			'charset' => 'utf8',
			'tablePrefix' => 't_',
			'connectionString' => 'mysql:host=10.13.56.73;port=3306;dbname=db_new_oil',
			'username' => 'root',
			'password' => 'sit1234',
			'retryCount' => '1',
		),
        'dbLog'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix' => 't_',
            'connectionString' => 'mysql:host=10.13.56.73;port=3306;dbname=db_new_oil_log',
            'username' => 'root',
            'password' => 'sit1234',
            'retryCount' => '1',
        ),
        'db_history'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix' => 't_',
            'connectionString' => 'mysql:host=10.13.56.73;port=3306;dbname=db_oil_history',
            'username' => 'root',
            'password' => 'sit1234',
            'retryCount' => '1',
        ),
		'redis'=>array(
			'class'=>'CRedisCache',
			'serverConfigs'=>array(
				// 'dev'=>array('host'=>'172.16.1.13', 'port'=>6379)
				'dev'=>array('host'=>'10.13.56.73', 'port'=>6380)
			),
			'getIDC'=>array('dev'),
			'setIDC'=>array('dev'),
			'localIDC'=>array('dev')
		),
		// 日志配置，必须预加载生效
		'log'=>array(
			'class'=>'CLogRouter', 
			'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'trace, info, warning,error',
                    'LogDir'=>LOG_DIR,//此目录可配置,在此目录下，每天一个文件夹
                    'logFileName'=>'all.log'//记录日志的文件名可配置
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error',
                    'LogDir'=>LOG_DIR,//此目录可配置,在此目录下，每天一个文件夹
                    'logFileName'=>'error.log'//记录日志的文件名可配置
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error',
                    'LogDir'=>LOG_DIR,//此目录可配置,在此目录下，每天一个文件夹p
                    'categories'=>'oil.import.log',
                    'logFileName'=>'oil.import.log'//记录日志的文件名可配置
                ),
				/*array(
					'class'=>'CFileLogRoute', // 写入
					'levels'=>'', // 记录所有级别的
					'LogDir'=>LOG_DIR,//此目录可配置,在此目录下，每天一个文件夹
					'logFileName'=>'all.log'//记录日志的文件名可配置
				)*/
			),
		),
		'amqp' => array(
			'class' => 'system.components.amqp.CAMQP',
			'host'  => '10.13.56.73',
            'login' => 'jybsit',
            'password'=>'jybsit1234',
		),
        //微信企业号消息发送
        'weiXinMsg'=>array(
            'class'=>'application.components.weixin.ZWeixinMsg',
            'corp_id'=>'ww0f15b7e86e3ad77c',
            'secret'=>'e_gM5Vu91F8T2g56idexCeN6pbubyuO27CZoAqOO0pg',
            'agent_id'=>'1000003',
        ),

	)
);
?>
