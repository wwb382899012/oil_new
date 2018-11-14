<?php
return array(

    'params'=>array(
        "url_host"=>"oil2.jtjr.com/",
        //"modPath" => "../framework",
    ),

    'components'=>array(
        'db'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix' => 't_',
            'connectionString' => 'mysql:host=172.16.1.141;port=3306;dbname=db_new_oil_20180120',
            'username' => 'jiayoubao',
            'password' => 'root1234',
        ),
        'dbSlave'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix' => 't_',
            'connectionString' => 'mysql:host=172.16.1.13;port=3306;dbname=db_new_oil_20180120',
            'username' => 'jiayoubao',
            'password' => 'root1234',
        ),
        'dbLog'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix' => 't_',
            'connectionString' => 'mysql:host=172.16.1.13;port=3306;dbname=db_new_oil_log',
            'username' => 'jiayoubao',
            'password' => 'root1234',
        ),
        'db_history'=>array(
            'class'=>'CDbConnection',
            'charset' => 'utf8',
            'tablePrefix' => 't_',
            'connectionString' => 'mysql:host=172.16.1.13;port=3306;dbname=db_oil_history_20180118',
            'username' => 'jiayoubao',
            'password' => 'root1234',
        ),

        'redis'=>array(
            'class'=>'CRedisCache',
            'serverConfigs'=>array(
                'dev'=>array('host'=>'172.16.1.35', 'port'=>6379)
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
                    'levels'=>'trace, info, warning',
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
            'host'  => '172.16.1.8',
            'login' => 'jyb',
            'password'=>'root',

        )

    )
);
