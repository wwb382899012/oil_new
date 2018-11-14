<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/7 15:27
 * Describe：
 */
class ProjectController extends ProjectBaseController {
    public function pageInit() {
        parent::pageInit();
        $this->attachmentType = Attachment::C_PROJECT;
        $this->filterActions = 'getIdByCode,getFileDownload';
        $this->rightCode = 'project';
        $this->isShowAllLink = 0;
        $this->isCanAdd = 1;
        $this->editView = '/project/add';
        $this->detailView = '/project/detail';
        $this->newUIPrefix="new_";
    }

    public function getIndexData() {
        $attr = $this->getSearch();
        $query = '';
        $user = Utility::getNowUser();
        $projectType = 0;
        if (!empty($attr['project_type'])) {
            switch ($attr["project_type"]) {
                case ConstantMap::SELF_IMPORT_FIRST_SALE_LAST_BUY: //进口自营-先销后采
                    $query .= " and a.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and c.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_IMPORT_FIRST_BUY_LAST_SALE: //进口自营-先采后销
                    $query .= " and a.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and c.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_SALE_LAST_BUY: //内贸自营-先销后采
                    $query .= " and a.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and c.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_BUY_LAST_SALE: //内贸自营-先采后销
                    $query .= " and a.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and c.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                default:
                    $query .= " and a.type = " . $attr['project_type'];
                    break;
            }
            $projectType = $attr['project_type'];
            unset($attr['project_type']);
        }

        $sql = 'select {col} from t_project a 
                left join t_corporation d on d.corporation_id = a.corporation_id 
                left join t_system_user b on b.user_id = a.manager_user_id 
                left join t_project_base c on c.project_id = a.project_id 
                left join t_partner up on up.partner_id = c.up_partner_id 
                left join t_partner dp on dp.partner_id = c.down_partner_id 
                left join t_system_user su on su.user_id = a.create_user_id ' . $this->getWhereSql($attr) . $query . ' and ' . AuthorizeService::getUserDataConditionString('a') . ' order by a.project_id desc {limit}';
        $fields = 'a.project_id, a.project_code, a.corporation_id, d.name as corp_name, a.type, a.status, a.status_time, b.name, c.buy_sell_type, 
                   c.up_partner_id, c.down_partner_id, c.goods_name,up.name as up_partner_name, dp.name as down_partner_name, su.name as creater_name, a.create_time';
        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, $fields);
        } else {
            $data = array();
        }

        if (Utility::isNotEmpty($data['data']['rows'])) {
            $map = Map::$v;
            foreach ($data['data']['rows'] as $key => $row) {
                $type_desc = $map['project_type'][$row['type']];
                if (!empty($row["buy_sell_type"])) {
                    $type_desc .= '-' . $map['purchase_sale_order'][$row["buy_sell_type"]];
                }
                $data['data']['rows'][$key]['project_type_desc'] = $type_desc;
            }
        }
        if (!empty($projectType)) {
            $attr['project_type'] = $projectType;
        }

        return $data;
    }

    public function actionGetIdByCode() {
        $projectCode = Mod::app()->request->getParam('project_code');
        if (empty($projectCode)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $data = Project::model()->find(array('select' => 'project_id', 'condition' => 'project_code = :projectCode', 'params' => array('projectCode' => $projectCode)));
        if (!empty($data->project_id)) {
            $this->returnSuccess($data->project_id);
        } else {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST_BY_CODE, array('project_code' => $projectCode)));
        }
    }
}