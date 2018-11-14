<?php
namespace PHPSTORM_META {                                // we want to avoid the pollution

    use ddd\infrastructure\DIService;

    override(DIService::get(0),         // method signature //argument number is ALWAYS 0 now.
             map( [ //map of argument value -> return type
                    //"special" => \Exception::class,                //Reference target classes by ::class constant
                    '' =>  '@',
                  ]));

    override(DIService::getRepository(0),         // method signature //argument number is ALWAYS 0 now.
             map( [ //map of argument value -> return type
                    //"special" => \Exception::class,                //Reference target classes by ::class constant
                    '' =>  '@',
                  ]));
    /*override(DIService::getRepository(0),         // method signature //argument number is ALWAYS 0 now.
             map( [//map of argument value -> return type
                    '' =>  '@',
                  ]));*/
}