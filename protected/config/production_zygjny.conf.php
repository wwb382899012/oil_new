<?php
return array(
    'params'=>array(
        "url_host"=>"oil.zygjny.com",//
        "weiXin_url"=>"http://oil.zygjny.com",//微信页面的URL
        "modPath" => "/data/www/framework",
        "block_interface_url"=>"http://10.23.93.160/api/contract", //区块链接口地址
        "block_browser_url"=>"http://106.75.230.70/fisco-bcos-browser",//区块链浏览器地址
        "money_system_config" => [
            "system_flag" => "NewOil", //系统标识
            "money_url" => "http://106.75.133.50:36003/",  //资金系统接口地址
            "money_secret_key" => "f903f61a8510ae43ed00a70f3169fc0b4f03888d",  //资金系统密钥
            "money_private_key" => 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAK2cL2ih3UyjMZDyDP28oE1+3RzpSwMxhEfacr5i+Z0Z2cC6TFi2nTjo6o7/2v8DIz2ZWa/k8femiCMm/rnpIJ2oQOvwNDYPjaFIuIqyUCuN8cZt3e1ghdgJS88VWL1s+vf5v0G2kxK0/Y7wfd13MtKFr4gxeNETykh6hMG6TWNdAgMBAAECgYB6ycx+JH1whru32Hp3u8FlDiU1HYuAZrU4XLhrD3WcN3xbY2g8Fmx8o7/CBBPP6VgzaRKV5Ud98Lq4ogvnUYtOy0euyVNqgnyWF3xOuoCNRG3Y3nmN8N4CgMzJOlIEaYOyJRns7qJ0H/RCJy/6cHS/HVgl+ipCc6XL/4xmTBeSAQJBAOTulYuVsfaoVKWaQeB2kaejPgMnV7qhyWTW5LHYCm11Qarn6Oizl+EMavQSJNv2attJGq9yoQO9NDED4ue0CSkCQQDCIxgAC4pAF3HRO5aLmhxNXrgRLwyIZgbfZs9PHbwf6Ifd11Xt3YU977cN594/moS6FuD9vK9SuQs+f923IOsVAkEAjx9JhvaTR+183ftObBI0hWVdA4O5KQi0a8KdP0IdYskHwN0zkyeUMDIfO2+Mc6ferjFJ6Z30Y+4Jjwsq9Eht4QJAZP966fMW/pbz2KWGgaQwWzQO0KnIfGGP68OB0KgoifUgUhJIGxKm0f0XH73kSvSpXmKutHLoR0ILjn1ZLH+MrQJAXdc5Za2x/IVELR4YMovafS0FWBPeSXV5ULGzrB57E2wbdpDsmc8IIhcs5ApjTDJp69Ulom1oHVdTOQT0ip5WIw==',
            "money_public_key" => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCtnC9ood1MozGQ8gz9vKBNft0c6UsDMYRH2nK+YvmdGdnAukxYtp046OqO/9r/AyM9mVmv5PH3pogjJv656SCdqEDr8DQ2D42hSLiKslArjfHGbd3tYIXYCUvPFVi9bPr3+b9BtpMStP2O8H3ddzLSha+IMXjRE8pIeoTBuk1jXQIDAQAB',
            "auto_payment_currency" => [],
        ],
        "delay_amqp_url"=>"http://10.23.16.111:19532/",//延时队列
    ),
    'components'=>array(
        'db'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix'=>'t_',
            'connectionString' => 'mysql:host=10.23.62.172;port=3306;dbname=db_new_oil',
            'username' => 'jyb',
            'password' => 'YHNBGT',
        ),
        'dbSlave'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix'=>'t_',
            'connectionString' => 'mysql:host=10.23.62.172;port=3306;dbname=db_new_oil',
            'username' => 'jyb',
            'password' => 'YHNBGT',
        ),
        'dbLog'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix'=>'t_',
            'connectionString' => 'mysql:host=10.23.62.172;port=3306;dbname=db_new_oil_log',
            'username' => 'jyb',
            'password' => 'YHNBGT',
        ),
        'db_history'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix' => 't_',
            'connectionString' => 'mysql:host=10.23.62.172;port=3306;dbname=db_oil_history',
            'username' => 'jyb',
            'password' => 'YHNBGT',
        ),
        'redis'=>array(
            'class'=>'CRedisCache',
            'serverConfigs'=>array(
                'dev'=>array('host'=>'10.23.217.239', 'port'=>6379, 'password'=>''),
            ),
            'getIDC'=>array('dev'),
            'setIDC'=>'dev',
            'localIDC'=>'dev',
            'getRetryCount'=>2,
            'getRetryInterval'=>0,
            'setRetryCount'=>3,
            'setRetryInterval'=>0.2
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'info, warning,error',
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
                    'LogDir'=>LOG_DIR,//此目录可配置,在此目录下，每天一个文件夹
                    'categories'=>'oil.import.log',
                    'logFileName'=>'oil.import.log'//记录日志的文件名可配置
                ),
            ),
        ),
        'amqp' => array(
            'class' => 'system.components.amqp.CAMQP',
            'host'  => '10.23.90.195',
            'login' => 'jyb',
            'password'=>'Root)(*&',
        ),
        //微信企业号用户管理
        'weiXinUser'=>array(
            'class'=>'application.components.weixin.ZWeixinUser',
            'corp_id'=>'ww0f15b7e86e3ad77c',
            'secret'=>'Rdbsjpww9FvR-mypu-lG8M03fkdoeoH3I4rZuG-8Yok',
        ),
        //微信企业号消息发送
        'weiXinMsg'=>array(
            'class'=>'application.components.weixin.ZWeixinMsg',
            'corp_id'=>'ww0f15b7e86e3ad77c',
            'secret'=>'Ys78-X8qxCwEtIHynNYJzTEMX0B4HDOkSPBeV58pLZg',
            'agent_id'=>'1000002',
        ),

    )
);
?>
