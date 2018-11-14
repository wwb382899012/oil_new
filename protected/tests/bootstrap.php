<?php
define("MOD_DEBUG", true);
date_default_timezone_set('Asia/Shanghai');

define("ROOT_DIR", dirname(__FILE__).'/../../');

require(ROOT_DIR."/protected/components/Environment.php");
$env = new Environment(null, array('life_time' => 30));

require(ROOT_DIR.$env->getModPath().'/Mod.php');
Mod::setPathOfAlias("ddd", ROOT_DIR."/protected/ddd/");

Mod::createWebApplication($env->getConfig());

//模拟登陆态
$identity=new UserIdentity('admin', '123456');
$identity->getUser();
$identity->afterAuthenticate();
$user = new WebUser();
$user->login($identity);