<?php
//define("MOD_DEBUG",false);
date_default_timezone_set('Asia/Shanghai');

define('ROOT_DIR', dirname(__FILE__) . '/..');
require(ROOT_DIR . "/protected/components/Environment.php");

$env = new Environment();
require_once($env->getModPath(). '/Mod.php');
$config = $env->getConfig();
Mod::setPathOfAlias("ddd",ROOT_DIR . "/protected/ddd/");
//Mod::getLogger()->autoFlush = 1;
//Mod::getLogger()->autoDump = true;
require($env->getModPath().'/modc.php');
