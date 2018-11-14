<?php
/**
 * 
 */
class SiteController extends Controller
{



    public function pageInit()
    {
        $this->layout = 'main';
        $this->newUIPrefix="new_";
        $this->guestActions=array("login");
        $this->publicActions=array("error", 'weixinLogin');
        $this->authorizedActions=array("login","index","logout","setRole","updatePwd","task","getMyTasks","getMyTasksWithNewUI");
    }

    public function actionIndex()
    {
        /*$weekArray = array('0' => '星期日', '1' => '星期一', '2' => '星期二', '3' => '星期三', '4' => '星期四', '5' => '星期五', '6' => '星期六');
        $weekDay = $weekArray[date('w')];*/

        $this->mainActive = "active";

        $roleId=UserService::getNowUserMainRoleId();
        //$userId=Utility::getNowUserId();

        /*$pArr = Project::model()->findAllToArray();
        $projectCount = count($pArr);*/

        //待风控审核的数目
        /*$fArr = CheckDetail::model()->findAllToArray("business_id=2 and status=0");
        $riskCount = count($fArr);*/

        //待合同审核的数目
        /*$cArr = CheckDetail::model()->findAllToArray("business_id=4 and status=0");
        $contractCount = count($cArr);*/

        //$payData=StatService::getPaymentStat();

        // $this->pageTitle = "石油项目系统 <small>" . date("Y年n月d日 H:i") . "  " . $weekDay . "</small>";
        $indexView="index";

        if($this->isMobileView)
        {
            $indexView="mobileIndex";
            $tasks = $this->getUserTasks();
        }
        else
        {
            $actions = TaskService::getUserAllRolesActions(Utility::getNowUserId());
            $tasks = TaskService::getUserAllTasksUnlimit(Utility::getNowUserId());
            $shortCuts = $this->getSiteShortcut();
            $shortCuts =$this->replaceNewShortCutIcon($shortCuts);
        }

        $this->pageTitle = "";

        $this->render($indexView,array(
            "roleId"=>$roleId,
            "actions"=>$actions,
            "tasks"=>$tasks,
            "shortCuts"=>$shortCuts
        ));
    }


    public function actionSetRole()
    {
        $id=Mod::app()->request->getParam("id");
        $roles=UserService::getUserRoles();
        $role=$roles[$id];
        if(empty($role))
            $this->returnError("您没有所选角色的权限！");
        else
        {
            UserService::setMainRoleId($id);
            $this->returnSuccess($id);
        }
    }

    /**
     * 用于编辑器直接粘贴图片上传
     */
    public function actionPasteUpload(){
        return;
        $filePath="/protected/runtime/upload/";
        try
        {
            $filePath.=date("Ym")."/";
            Utility::checkDirectory(ROOT_DIR .$filePath);
            $file=$_REQUEST["upload"];
            //$file=$_REQUEST;
            $data= base64_decode($file);
            //var_dump($data);
            $filePath = $filePath.time()."_".Utility::getRandomKey().".png";
            file_put_contents(ROOT_DIR .$filePath,$data);

            $this->returnSuccess($filePath);
        }
        catch (Exception $e)
        {
            Mod::log('文件上传失败,message:'.$e->getMessage(),'error');
            $this->returnError("上传失败：".$e->getMessage());
        }

    }

    public function actionLogin()
    {
        if (!Mod::app()->user->isGuest) {
            return $this->goHome();
        }

        // 不做微信跳转
        $noOauth = Mod::app()->request->getParam('no_oauth');
        if (WeiXinService::inWeixin() && !$noOauth && !Mod::app()->request->isPostRequest) {
            $this->doWeixinOauth();
        }

        if(Mod::app()->request->isPostRequest)
        {
            $this->doLogin();
        }

        $this->layout="empty";
        $this->render('loginNew',array("url"=>Mod::app()->user->returnUrl));
    }

    protected function doLogin()
    {
        $params = $_POST["obj"];

        $identity=new UserIdentity(Utility::filterInject($params["username"]),$params["password"]);
        if($identity->authenticate() && $res=Mod::app()->user->login($identity))
        {
            $this->returnSuccess();
        }
        else
        {
            $this->returnError(UserIdentity::$errors[$identity->errorCode]);
        }

    }

    public function actionLogout()
    {
        Mod::app()->user->logout();

        return $this->goHome();
    }

    public function actionError()
    {
        $error=Mod::app()->errorHandler->error;

        if(Mod::app()->request->isAjaxRequest && $this->getIsVueJsRequest()){
            if($error["code"]=="403"){
                $msg = "你无权操作当前页面及功能！";
            }else{
                $msg = "Error Code: " . $error['code'] . " - " .$error['message'];
            }
            $this->returnJsonError($msg);
        }

        if($error["code"]=="403")
        {
            //throw new CHttpException(200,"你无权操作当前页面及功能");
            $this->layout="main";
            if(Mod::app()->request->isAjaxRequest)
            {
                $this->layout="empty";
                echo "你无权操作当前页面及功能！";
            }
            else
                $this->renderError("你无权操作当前页面及功能！");
        }
        else
        {
            // 微信接口错误处理
            if (Mod::app()->request->getParam('__ajax') == 1) {
                if (!empty($error['traces'][0]['args'])) {
                    $errMsg = $error['traces'][0]['args'][1]['msg'];
                    $this->returnError($errMsg);
                }
                $this->returnError("您无权访问当前页面");
            }
            if(Mod::app()->request->isAjaxRequest)
            {
                $this->layout="empty";
                echo "Error Code: " . $error['code'] . " - " .$error['message'];
                Mod::app()->end();
            }
            $this->layout="emptyMain";
            $this->render("error", array("error"=>$error));
        }
    }

    public function actionUpdatePwd()
    {
        if (Mod::app()->user->isGuest) {
            return $this->goHome();
        }
        if(Mod::app()->request->isPostRequest)
        {
            $this->updatePwd();
        }

        //$this->layout="empty";
        $this->render('updatePwd',array("url"=>Mod::app()->user->returnUrl));
    }

    protected function updatePwd()
    {
        $params = $_POST["data"];

        if(empty($params["password"]))
            $this->returnError("原密码不得为空！");
        if(empty($params["newPassword"]))
            $this->returnError("新密码不得为空！");
        if($params["newPassword"]!=$params["confirmPassword"])
            $this->returnError("新密码与确认密码不一致！");

        $model=SystemUser::model()->findByPk(Utility::getNowUserId());
        if(empty($model->user_id))
            $this->returnError("当前用户不存在！");
        if($model->password!=Utility::getSecretPassword($params["password"]))
            $this->returnError("原密码不正确！");

        $model->password=Utility::getSecretPassword($params["newPassword"]);
        $model->update_time=new CDbExpression("now()");
        $res=$model->save(true,array("password","update_time"));
        if($res)
            $this->returnSuccess();
        else
            $this->returnError("操作失败！");
    }

    public function actionTask()
    {
        $id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($id))
        {
            $this->renderError("参数错误！");
        }
        $task = Task::model()->with("action")->findByPk($id);
        if (empty($task))
            $this->renderError("任务不存在！");

        if($task->user_id==0)
        {
            $roleId = UserService::getNowUserMainRoleId();

            if ($roleId != $task->role_id)
            {
                $roles = SystemUser::getRoles(Utility::getNowUserId());
                if (!isset($roles[$task->role_id]))
                    $this->renderError("无权操作当前任务！");
                UserService::setMainRoleId($task->role_id);
            }
        }
        $this->redirect($task->action_url);
    }

    public function actionGetMyTasks() {
        $isActive = Mod::app()->request->getParam('is_active');
        $class = "treeview active";
        if($isActive) {
            $class .= " menu-open";
        }
        $style = "display: block;";
        if(!$isActive) {
            $style = "display: none;";
        }
        $data=TaskService::getUserActions(Utility::getNowUserId());
        $container = '<li id="menu_item_tasks" class="'.$class.'">
                        <a href="#">
                            <i class="fa fa-tasks"></i>
                            <span>我的待办</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                      <ul class="treeview-menu menu-open" style="'.$style.'">';
        if(count($data) < 1) {
            $container .= '
                        <li  class="active">
                            <a>
                                <i class="fa fa-fw fa-check-square-o"></i>
                                <span>暂无待办</span>
                            </a>
                        </li>
                        ';
        } else {
            foreach($data as $v)
            {
                $icon=" fa-flag";
                $container .='<li id="menu_item_action_' . $v["action_id"] . '">
                                <a href="'.$v["list_url"] .'">
                                    <i class="fa '.$icon .'"></i>
                                    <span>'.$v["action_name"].'</span>
                                <span class="label bg-red pull-right">'.$v["n"].'</span>
                                </a>
                              </li>';
            }
        }
        $container .= '</ul></li>';
        $this->returnSuccess($container);
    }

    public function actionGetMyTasksWithNewUI() {
        $data=TaskService::getUserActions(Utility::getNowUserId());
        $container = '';
        if(count($data) < 1) {
            $container .= '<a style="line-height:34px;height:34px;padding-left:20px;"><span>暂无待办</span></a>';
        } else {
            $container .= '<div class="div-role-slt"><li><ul class="menu">';
            foreach($data as $v)
            {
                $container .= '<li style="line-height:34px;height:34px;padding-left:20px;cursor:pointer;">
                                    <p style="display:flex;align-items:center;;width:100%;height:100%;" onclick="location.href=\''.$v["list_url"].'\'"><span style="display:inline-block;max-width:10em;">'.
                                        $v["action_name"].
                              "</span><span style='margin-left: 10px'>（" .$v["n"].'）</span> 
                                    </p>
                               </li>';
            }
        }
        $container .= '</ul></li></div>';
        $this->returnSuccess($container);
    }

    /**
     * 替换旧的快捷入口icon为新UI
     * @param $shortCuts
     * @return mixed
     */
    private function replaceNewShortCutIcon($shortCuts) {
        $shortCutIcons = [
            'ico-project' => 'icon-tianjia',
            'ico-subContract' => 'icon-tianjia',
            'ico-deliveryorder' => 'icon-tianjia',
            'ico-pay_application' => 'icon-kaipiaoshenqing',
            'ico-inputinvoice' => 'icon-kaipiaoshenqing',
            'ico-outputinvoice' => 'icon-kaipiaoshenqing',
            'ico-bankflow' => 'icon-bianji',
            'ico-partnerapply' => 'icon-bianji',
            'ico-storehouse' => 'icon-cangku',
        ];
        if (!empty($this->newUIPrefix) && $this->newUIPrefix == 'new_') {
            foreach ($shortCuts as &$val) {
                if (isset($shortCutIcons[$val['ico']])) {
                    $val['ico'] = $shortCutIcons[$val['ico']];
                }
            }
        }
        return $shortCuts;
    }

    private function getSiteShortcut() {
        $shortCuts = array();
        if(UserService::checkActionRight("project","add")) {
            $shortCuts[] = array(
                    'name'=>'发起项目',
                    'ico'=>'ico-project',
                    'url'=>'/project/add/',
                    'color'=>'#11a5fa',
                    'subMenu' => null,
                );
        }
        if(UserService::checkActionRight("subContract","add")) {
            $shortCuts[] = array(
                    'name'=>'增加合同',
                    'ico'=>'ico-subContract',
                    'url'=>'/subContract/',
                    'color'=>'#ffe50b',
                    'subMenu' => null,
                );
        }
        if(UserService::checkActionRight("deliveryorder","add")) {
            $shortCuts[] = array(
                    'name'=>'新建发货单',
                    'ico'=>'ico-deliveryorder',
                    'url'=>'/deliveryOrder/',
                    'color'=>'#0bc8ff',
                    'subMenu' => array(
                            array(
                            'url'=>'/deliveryOrder/selectContracts',
                            'name'=>'新建经仓发货单'
                            ),
                            array(
                            'url'=>'/deliveryOrder/selectStockIns/',
                            'name'=>'新建直调发货单'
                            ),
                        )
                );
        }
        if(UserService::checkActionRight("pay_application","add")) {
            $shortCuts[] = array(
                    'name'=>'付款申请',
                    'ico'=>'ico-pay_application',
                    'url'=>'/pay/',
                    'color'=>'#0dec22',
                    'subMenu' => array(
                            array(
                            'url'=>'/pay/route?type=11',
                            'name'=>'合同下付款'
                            ),
                            array(
                            'url'=>'/pay/route?type=13',
                            'name'=>'多合同合并付款'
                            ),
                            array(
                            'url'=>'/pay/route?type=14',
                            'name'=>'项目下付款'
                            ),
                            array(
                            'url'=>'/pay/route?type=15',
                            'name'=>'交易主体下付款'
                            ),
                            array(
                            'url'=>'/pay/route?type=20',
                            'name'=>'后补项目合同付款'
                            ),
                        ),
                );
        }
        if(UserService::checkActionRight("inputinvoice","add")) {
            $shortCuts[] = array(
                    'name'=>'进项票申请',
                    'ico'=>'ico-inputinvoice',
                    'url'=>'/inputInvoice/',
                    'color'=>'#ec7b0d',
                    'subMenu' => array(
                            array(
                            'url'=>'/inputInvoice/add?type=1',
                            'name'=>'货款类发票申请'
                            ),
                            array(
                            'url'=>'/inputInvoice/add?type=2',
                            'name'=>'非货款类发票申请'
                            ),
                        ),
                );
        }
        if(UserService::checkActionRight("outputinvoice","add")) {
            $shortCuts[] = array(
                    'name'=>'销项票申请',
                    'ico'=>'ico-outputinvoice',
                    'url'=>'/outputInvoice/',
                    'color'=>'#fc6142',
                    'subMenu' => array(
                            array(
                            'url'=>'/outputInvoice/add?type=1',
                            'name'=>'货款类发票申请'
                            ),
                            array(
                            'url'=>'/outputInvoice/add?type=2',
                            'name'=>'非货款类发票申请'
                            ),
                        ),
                );
        }
        if(UserService::checkActionRight("bankflow","add")) {
            $shortCuts[] = array(
                    'name'=>'银行流水录入',
                    'ico'=>'ico-bankflow',
                    'url'=>'/bankFlow/add',
                    'color'=>'#da8a3b',
                    'subMenu' => null,
                );
        }
        if(UserService::checkActionRight("partnerapply","add")) {
            $shortCuts[] = array(
                    'name'=>'合作方录入',
                    'ico'=>'ico-partnerapply',
                    'url'=>'/partnerApply/add/',
                    'color'=>'#02d57e',
                    'subMenu' => null,
                );
        }
        if(UserService::checkActionRight("storehouse","add")) {
            $shortCuts[] = array(
                    'name'=>'添加仓库',
                    'ico'=>'ico-storehouse',
                    'url'=>'/storehouse/add',
                    'color'=>'#c969f6',
                    'subMenu' => null,
                );
        }
        return $shortCuts;
    }

    /**
     * 跳转到微信授权页面
     */
    public function doWeixinOauth() {
        $baseUrl = Mod::app()->request->getHostInfo();
        $returnUrl = $baseUrl.Mod::app()->user->returnUrl;
        $loginUrl = $baseUrl.'/site/weixinLogin';
//        $loginUrl .= '?returnUrl=' . base64_encode($returnUrl);
        $loginUrl .= '?returnUrl=' . $returnUrl;
        $oauthUrl = WeiXinService::getOauthUrl($loginUrl);
        Mod::log(__METHOD__ . "oauthUrl:" . $oauthUrl);

        return $this->redirect($oauthUrl);
    }

    /**
     * 授权回调页面 auth, set session & etc
     */
    public function actionWeixinLogin() {
	    $code = Mod::app()->request->getParam('code');
	    if (empty($code)) {
	        $this->redirectHomeLogin();
        }
        $userInfo = WeiXinService::getUserInfo($code);
	    if ($userInfo == false) {
            $this->redirectHomeLogin();
        }

        $systemUser = SystemUser::model()->find('identity="'.$userInfo['UserId'].'"');
        if (empty($systemUser->user_id)) {
            $this->redirectHomeLogin();
        }

        $identity=new UserIdentity($systemUser->user_name,"");
        $identity->getUser();
        $identity->afterAuthenticate();
        if($res=Mod::app()->user->login($identity))
        {
//            $url = base64_decode(Mod::app()->request->getParam('returnUrl'));
            $url = Mod::app()->request->getParam('returnUrl');
            if (empty($url)) {
                $url = Mod::app()->request->getHostInfo(); 
            }
            $this->redirect($url);
        }

        $this->redirectHomeLogin();
    }

    protected function redirectHomeLogin() {
        $baseUrl = Mod::app()->request->getHostInfo();
        $returnUrl = Mod::app()->request->getParam('returnUrl');
        if (empty($returnUrl))
            $returnUrl = $baseUrl;

        return $this->redirect($baseUrl . '/site/login?no_oauth=1&returnUrl='.$returnUrl);
    }
}

