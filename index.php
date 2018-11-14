<?php
define("MOD_DEBUG",true);
date_default_timezone_set('Asia/Shanghai');


define("ROOT_DIR", dirname(__FILE__));
require(ROOT_DIR . "/protected/components/Environment.php");

$env = new Environment(null, array('life_time'=>30));

header('Content-type:text/html;charset='.$env->get('charset'));
require($env->getModPath().'/Mod.php');
Mod::setPathOfAlias("ddd",ROOT_DIR . "/protected/ddd/");
//Mod::getLogger()->autoFlush = 1;
//Mod::getLogger()->autoDump = true;

Mod::createWebApplication($env->getConfig())->run();

