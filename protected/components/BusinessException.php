<?php
require 'Error.php';

class BusinessException extends Exception {
    /*
     * @desc 异常处理
     * @param $key | array 错误信息数组 ，key[0]:err_code  key[1]:msg
     * @param $values | array 替换信息数组
     * @return Exception
     */
    public static function throw_exception($key, $values = array()) {
        //错误码数据校验
        if (!(is_array($key) && isset($key[0]) && isset($key[1]))) {
            throw new Exception('BaseError::throw_exception($key) key must be array type', SYS_ERROR);
        }
        //int校验，key[0]为error code
        if (!is_numeric($key[0])) {
            throw new Exception('error code must be int type >> ' . $key[0], SYS_ERROR);
        }

        /*$msg = TemplateUtil::parseTemplate($key[1], $values);
        throw new Exception($msg, $key[0]);*/
        TemplateUtil::parseExceptionTemplate($key[1], $values);
        throw new Exception($key[1], $key[0]);
    }

    public static function throwException($error,$values=null)
    {
        self::throw_exception($error,$values);
    }




}
