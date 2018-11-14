<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/30 14:46
 * Describe：
 */

namespace ddd\infrastructure\error;

class ZException extends \Exception
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        if(is_array($message))
        {
            $s=$message[1];
            $c=$message[0];
            if(is_array($code))
            {
                $s=$this->formatMessage($s,$code);
            }
            $message=$s;
            $code=$c;
        }
        parent::__construct($message, $code, $previous);
    }

    public function formatMessage($message,$params=null)
    {
        if(is_array($params))
        {
            $customParams=array();
            foreach ($params as $k=>$v)
            {
                $customParams["{".$k."}"]=$v;
            }
            $message=strtr($message,$customParams);
        }

        return $message;
    }


    public function __toString()
    {
        return $this->toString();
    }

    public function toString()
    {
        $sources=$this->getTrace();
        $s="[L:".$this->getLine()."]";
        if(is_array($sources))
        {
            $class=$sources[0]['class'];
            $function=$sources[0]['function'];
            $s.="[".$class.".".$function."]";
        }
        $s.="[".$this->getCode()."] ".$this->getMessage();
        return $s;
    }



}