<?php

/**
 * Created by youyi000.
 * DateTime: 2017/4/5 14:40
 * Describe：
 *      操作时效性相关的统计
 */
class StatTimelinessService
{

    public static $config=array(
        "1"=>"10","2"=>"25","3"=>"35","4"=>"45","5"=>"50","6"=>"60",
        //"10"=>"1","25"=>"2","30"=>"3","45"=>"4","50"=>"5","55"=>"6",
    );

    /**
     * 环节时效性判断
     * @var array
     */
    public static $config_time=array(
        "1"=>0,"2"=>60,"3"=>60,"4"=>60,"5"=>30,"6"=>30,
    );

    /**
     * 驳回状态与实际统计状态对应表
     * @var array
     */
    public static $config_back_status=array(
        "25"=>"30","45"=>"50",
    );

    public static $config_stat_detail=array(
        "13"=>array("4"=>"301",)
    );

    /**
     * 统计项与业务id及角色对应关系
     * @var array
     */
    public static $config_check_detail=array(
        "3"=>array("13"=>array("1"=>"6","2"=>"8","3"=>"4"),),
        "5"=>array("14"=>array("1"=>"4","2"=>"6",),),
    );

    /**
     * 驳回时间单独计算的环节
     * @var array
     */
    public static $config_back=array("3","5");

    public static function generateData()
    {
        $sql="select project_id,create_time,status from t_project order by project_id asc";
        $data=Utility::query($sql);
        foreach ($data as $v)
        {
            self::generateProjectData($v["project_id"],$v["create_time"],$v["status"]);
            self::generateCheckData($v["project_id"]);
        }
    }

    public static function generateProjectData($projectId,$createTime,$status)
    {

        $model=StatTimeliness::model()->find("project_id=".$projectId);
        if(empty($model->id))
        {
            $model=new StatTimeliness();
            $model->project_id=$projectId;
            $model->create_time=date('Y-m-d H:i:s');
        }
        $sql=" select * from t_project_log where project_id=".$projectId." order by create_time asc";
        $logs=Utility::query($sql);
        $d=array();
        $upTime=$createTime;
        foreach ($logs as $k=>$v)
        {
            $timeSpan=strtotime($v["create_time"])-strtotime($upTime);
            $logs[$k]["time_span"]=$timeSpan;
            $upTime=$v["create_time"];
        }

        $contractDone=array();
        //var_dump($logs);

        foreach ($logs as $v)
        {
            if($v["new_value"]>$v["old_value"])
            {
                if(key_exists($v["new_value"],$d))
                {
                    $d[$v["new_value"]]["create_time"]=$v["create_time"];
                    $d[$v["new_value"]]["time_span"]=$d[$v["new_value"]]["time_span"]+$v["time_span"];
                }
                else
                {
                    $d[$v["new_value"]]=array(
                        "create_time"=>$v["create_time"],
                        "time_span"=>$v["time_span"],
                        "back_times"=>0,
                        "back_span"=>0,
                    );
                }
                if($v["old_value"]==Project::STATUS_OUR_CONTRACT_UPLOAD)
                {
                    $contractDone=array(
                        "create_time"=>$v["create_time"],
                        "time_span"=>$v["time_span"],
                        "back_times"=>0,
                        "back_span"=>0,
                    );
                }
            }
            else
            {
                if(!empty(self::$config_back_status[$v["old_value"]]))
                {
                    if(key_exists($v["old_value"],$d))
                    {
                        $d[$v["old_value"]]["back_span"]=$d[$v["old_value"]]["back_span"]+$v["time_span"];
                    }
                    else
                    {
                        $d[$v["old_value"]]=array(
                            "create_time"=>"",
                            "time_span"=>"",
                            "back_times"=>1,
                            "back_span"=>$v["time_span"],
                        );
                    }
                }
            }
        }

        //var_dump($d);

        foreach (self::$config as $k=>$c)
        {
            if(!empty($d[$c]["create_time"]))
            {
                $model["step".$k."_time"]=$d[$c]["create_time"];
                $model["step".$k."_span"]=$d[$c]["time_span"];

                if(in_array($k,self::$config_back))
                {
                    $model["step".$k."_back_times"]=$d[$c]["back_times"];
                    $model["step".$k."_back_span"]=$d[$c]["back_span"];
                }
            }
        }

        //if(empty($d["60"]["create_time"]) && !empty($d["61"]["create_time"]))
        if(empty($d["60"]["create_time"]) && !empty($contractDone["create_time"]))
        {
            $model["step6_time"]=$contractDone["create_time"];
            $model["step6_span"]=$contractDone["time_span"];
        }

        if($status>=Project::STATUS_PAY_CONFIRM)
        {
            $model->time_span=strtotime($model["step6_time"])-strtotime($createTime);
        }
        else
            $model->time_span=time()-strtotime($createTime);

        $model->update_time=date('Y-m-d H:i:s');
        $model->save();
    }

    public static function generateCheckData($projectId)
    {
        $sql="select 
                a.business_id,a.check_id,a.obj_id,a.check_status,a.create_time,a.user_id,b.role_id,b.create_time as start_time
             from t_check_log a,t_check_detail b
             where a.business_id in(13,14) and a.detail_id=b.detail_id
             and a.obj_id in(select contract_id from t_contract where project_id=".$projectId.")
             order by a.business_id,b.role_id ,a.create_time asc ";

        $data=Utility::query($sql);

        $d=array();

        foreach ($data as $v)
        {
            $timeSpan=strtotime($v["create_time"])-strtotime($v["start_time"]);
            if($v["check_status"]<1)
            {
                if (!empty($d[$v["business_id"]]) && key_exists($v["role_id"], $d[$v["business_id"]]))
                {
                    $d[$v["business_id"]][$v["role_id"]]["n"]++;
                    $d[$v["business_id"]][$v["role_id"]]["time_span"] += $timeSpan;
                }
                else
                {
                    $d[$v["business_id"]][$v["role_id"]]=array(
                        "check_time"=>"",
                        "time_span"=>0,
                        "n"=>1,
                        "back_span"=>$timeSpan,
                    );
                }
            }
            else
            {
                if (!empty($d[$v["business_id"]]) && key_exists($v["role_id"], $d[$v["business_id"]]))
                {
                    $d[$v["business_id"]][$v["role_id"]]["check_time"] = $v["create_time"];
                    $d[$v["business_id"]][$v["role_id"]]["time_span"] = $timeSpan;
                }
                else
                {
                    $d[$v["business_id"]][$v["role_id"]]=array(
                        "check_time"=>$v["create_time"],
                        "time_span"=>$timeSpan,
                        "n"=>0,
                        "back_span"=>0,
                    );
                }

            }

        }

        //var_dump($d);

        $model=StatTimeliness::model()->find("project_id=".$projectId);
        if(empty($model->id))
        {
            $model=new StatTimeliness();
            $model->project_id=$projectId;
            $model->create_time=date('Y-m-d H:i:s');
        }

        $backTimes=0;

        foreach (self::$config_check_detail as $k=>$v)
        {
            foreach ($v as $ek=>$ev)
            {
                foreach ($ev as $eek=>$eev)
                {

                    $model["step".$k."_back_times".$eek]=empty($d[$ek][$eev]["n"])?0:$d[$ek][$eev]["n"];
                    $model["step".$k."_back_span".$eek]=empty($d[$ek][$eev]["back_span"])?0:$d[$ek][$eev]["back_span"];

                    /*$obj=StatTimelinessDetail::model()->find("project_id=".$projectId." and step=".$k.$eek);
                    if(empty($obj->id))
                    {
                        $obj=new StatTimelinessDetail();
                        $obj->project_id=$projectId;
                        $obj->create_time=date('Y-m-d H:i:s');
                        $obj->step=$k.$eek;
                    }

                    $obj["back_times"]= empty($d[$ek][$eev]["n"])?0:$d[$ek][$eev]["n"];
                    $obj["back_span"]= empty($d[$ek][$eev]["back_span"])?0:$d[$ek][$eev]["back_span"];
                    $backTimes+=$obj["back_times"];*/

                    $backTimes+=$model["step".$k."_back_times".$eek];

                    if(!empty($d[$ek][$eev]["check_time"]))
                    {
                        $model["step" . $k . "_time" . $eek] = $d[$ek][$eev]["check_time"];
                        $model["step" . $k . "_span" . $eek] = $d[$ek][$eev]["time_span"];

                        /*$obj["done_time"]= $d[$ek][$eev]["check_time"];
                        $obj["time_span"]= $d[$ek][$eev]["time_span"];*/
                    }

                    /*$obj->update_time=date('Y-m-d H:i:s');
                    $obj->save();*/

                }
            }
        }

        $model->back_times=$backTimes;

        $model->update_time=date('Y-m-d H:i:s');
        $model->save();

    }


}