<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/29 15:15
 * Describe：
 */

class ContractController extends Controller
{
    public function pageInit()
    {
        parent::pageInit();
        $this->newUIPrefix="new_";
        $this->rightCode = 'contract';
    }


    public function actionIndex()
    {
        $attr=$this->getSearch();//$_GET["search"];
        $sql="select {col}
              from t_contract a
              left join t_project p on a.project_id=p.project_id
              left join t_corporation c on c.corporation_id=a.corporation_id
              left join t_system_user u on p.manager_user_id=u.user_id
              left join t_partner d on d.partner_id = a.partner_id
              left join t_corporation co on co.corporation_id = a.corporation_id
              left join t_contract_file f on a.contract_id = f.contract_id and f.is_main=1 and f.type=1
              ".$this->getWhereSql($attr)." and ".AuthorizeService::getUserDataConditionString("a")." order by a.contract_id desc {limit}";
        $fields='a.*,c.name as corp_name,p.project_code,p.type project_type,u.name,d.name as partner_name,co.name as corp_name,f.code_out';

        /*$dataProvider=new ZSqlDataProvider($sql,
                                           array(
                                                'fields'=>$fields,
                                                'pagination'=>array(
                                                    'pageSize'=>25,
                                                ),
                                            ));*/
        // $this->render('grid', array(
        //     'dataProvider' => $dataProvider,
        // ));
        $data=$this->queryTablesByPage($sql,$fields);
        $this->render("index",$data);
    }


    public function actionDetail()
    {
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("参数错误");

        $contract = ProjectService::getContractDetailModel($id);
        if(empty($contract) || empty($contract->project)) {
            $this->renderError(BusinessError::outputError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id'=>$contract->project_id)), "/riskManagement/");
        }
        $this->render('detail', array('contract'=>$contract));
    }
}
