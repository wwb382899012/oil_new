<?php
/**
 * Created by youyi000.
 * DateTime: 2018/1/10 11:41
 * Describe：
 */

class FileCommand extends AMQPCommand
{
    /**
     * 需要监听的队列信息
     * @var array
     */
    protected $queueConfig = array(
        "new.oil.file.word.to.pdf"=>array(
            "fn"=>"wordToPDF",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"file.word.to.pdf",
        ),
    );

    public function init()
    {
        $this->sleepTime = 0.5;
        //$this->isAutoAck=true;
        parent::init();
    }

    /**
     * @param $msg
     * @throws Exception
     */
    public function wordToPDF($msg)
    {
        $params=json_decode($msg,true);
        Utility::wordToPdf($params["filePath"]);
    }

}