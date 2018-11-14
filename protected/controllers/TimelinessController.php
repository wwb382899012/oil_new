<?php

/**
 * Created by youyi000.
 * DateTime: 2017/4/6 10:44
 * Describe：
 */
class TimelinessController extends Controller
{
    public function pageInit()
    {
        $this->filterActions="index,detail";
        $this->rightCode = "stat_timeliness";
    }


    public function actionIndex()
    {
        $attr = $_GET[search];

        $search=$attr;

        $query = "";
        if(!empty($attr["hasBack"]))
        {
            if($attr["hasBack"]==1)
                $query .= " and b.back_times>0";
            else
                $query .= " and b.back_times<=0";

            unset($attr["hasBack"]);
        }

        $sql="select {col}"
            ." from t_project a 
            left join t_partner u on a.up_partner_id=u.partner_id
            left join t_partner d on a.down_partner_id=d.partner_id
            
             left join t_stat_timeliness b on a.project_id=b.project_id "

            .$this->getWhereSql($attr);
        $sql .= $query;
        $sql .= " and ".AuthorizeService::getUserDataConditionString('a')." order by a.project_id desc {limit}";
        $user = Utility::getNowUser();
        if(!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql,"b.*,a.project_id,a.project_name,a.status as project_status,a.create_time project_create_time
                    ,u.name as up_name,d.name as down_name,a.up_partner_id,a.down_partner_id");
        } else {
            $data = array();
        }
        $data['search'] = $search;
        $this->render('index',$data);
    }


}