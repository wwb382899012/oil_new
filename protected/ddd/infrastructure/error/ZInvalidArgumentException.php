<?php
/**
 * Created by youyi000.
 * DateTime: 2018/5/29 17:53
 * Describe：
 */

namespace ddd\infrastructure\error;


class ZInvalidArgumentException extends ZException
{
    /**
     * ZInvalidArgumentException constructor.
     * @param null $argumentName    参数名
     * @param null $message 错误消息
     */
    public function __construct($argumentName=null,$message=null)
    {
        if(empty($message) && !empty($argumentName))
            $message="参数".$argumentName."不得为空";
        $code=BusinessError::Argument_Invalid;
//        parent::__construct($message, $code);
        parent::__construct(BusinessError::Argument_Invalid, array('name' => $argumentName));
    }
}