<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    /**
     * @var string the default layout for the controller view. Defaults to 'column1',
     * meaning using a single column layout. See 'protected/views/layouts/main.php'.
     */
    public $layout="main";

    public $authorizedActions=array();
    public $guestActions=array();
    public $publicActions=array();

    public $homeUrl="/site/index";

    public $breadcrumbs;
    public $pageTitle;

    public $mainActive;

    public $moduleItem;
    public $moduleName="";
    public $moduleUrl="";
    public $moduleParentIds="";
    public $rightCode="";

    public $userActions=null;
    public $userTasks=null;

    /**
     * 树形菜单码
     * @var string
     */
    public $treeCode="";

    /**
     * 需要忽略权限强验证的action
     * @var string  不同的action以逗号（,）隔开，例如：login,loginOut
     */
    public $filterActions="";
    public $userId=0;

    /**
     * 是否是移动版视图
     * @var bool
     */
    public $isMobileView=false;

    public $mainUrl="";
    public $backUrl="";

    /**
     * 移动版视图后缀名
     * @var string
     */
    public $mobileViewSuffix="";

    /**
     * 新UI页面前缀
     * @var string
     */
    public $newUIPrefix="";

    /**
     * 是否新窗口打开
     * @var int
     */
    public $isExternal=1;


    public $map;

    public function init()
    {
        $this->backUrl=$_REQUEST["url"];
        $this->mainUrl = "/" . $this->getId() . "/";
        $this->layout = "main";
        $this->isMobileView = $this->isMobile();
        $this->pageInit();

    }

    /**
     * 获取Search查询条件
     * @return mixed
     */
    public function getSearch()
    {
        $search=$_GET["search"];
        $id = empty($this->action) ? "index" : $this->action->getId();
        $key=$this->id."_".$id."_search";
        if(empty($search))
        {
            $search=$_COOKIE[$key];
            if(!empty($search))
                $search=json_decode($search,true);
        }
        else
            setcookie($key, json_encode($search), time()+1500);
        return $search;
    }

    /**
     * 获取查询页数
     * @return int
     */
    public function getSearchPage()
    {
        $page = $_GET['page'];
        $key=$this->id."_".$this->action->getId()."_page";

        $referrer = Mod::app()->getRequest()->getUrlReferrer();
        $referrer = strpos($referrer, '?') !== false ? strstr($referrer, '?', true) : $referrer;
        $requestUrl = Utility::getRequestUrl();
        $requestUrl = strpos($requestUrl, '?') !== false ? strstr($requestUrl, '?', true) : $requestUrl;
        //从本页面代理过来，不使用page缓存，所有的查询结果都从第一页开始; 从其他页面代理过来，使用page缓存，默认当前页是缓存页面
        if ($referrer != $requestUrl) {
            $page=$_COOKIE[$key];
        }

        $page = empty($page) ? 1 : $page;
        setcookie($key, $page, time()+1500);

        return $page;
    }

    /**
     * 获取查询每页记录数
     * @return int
     */
    public function getSearchPageSize()
    {
        $pageSize=$_GET['pageSize'];
        $key=$this->id."_".$this->action->getId()."_pageSize";
        if(empty($pageSize))
        {
            $pageSize=$_COOKIE[$key];
            if(!empty($pageSize))
                $pageSize=json_decode($pageSize,true);
        }
        else
            setcookie($key, json_encode($pageSize), time()+1500);
        return $pageSize;
    }

    public function pageInit()
    {

    }

    public function goHome()
    {
        Mod::app()->request->redirect($this->homeUrl);
    }

    public function filters(){
        return array(
            'accessControl',
        );
    }

    /**
     * 获取当前模块授权的actions数组
     * @return array|mixed
     */
    public function getAuthorizedActions()
    {
        if(!empty($this->filterActions))
            $this->authorizedActions=array_merge($this->authorizedActions,explode(",",$this->filterActions));
        $actions=SystemUser::getAuthorizedActions($this->rightCode);
        if(is_array($actions) && count($actions)>0)
            $actions=array_merge($this->authorizedActions,$actions);
        else
            $actions=$this->authorizedActions;

        if(!empty($this->rightCode))
            $actions=empty($actions)?array('yii'):$actions;
        return $actions;
    }

    public function accessRules()
    {
        $this->userId=Utility::getNowUserId();
        $this->initRightCode();

        /*if(empty($this->rightCode))
            $this->rightCode=$this->id;*/
        //var_dump($this->id);
        if(empty($this->treeCode))
            $this->treeCode=$this->rightCode;

        $actions=$this->getAuthorizedActions();

        return array(
            array('allow',
                'actions'=>empty($this->publicActions)?array('yii'):$this->publicActions,
                'users'=>array('*'),
            ),
            array('allow',
                'actions'=>empty($this->guestActions)?array('yii'):$this->guestActions,
                'users'=>array('?'),
            ),
            array('allow',
                'actions'=>$actions,
                'users'=>array('@'),
            ),
            array('deny',
                'actions'=>array(),
                'users'=>array('*'),
            ),
        );
    }

    /**
     * 初始化权限码，对于同一个Controller使用多个权限码时，在此修改
     */
    public function initRightCode()
    {

    }

    /**
     * 设置新UI页面文件名
     * @param $file_name
     * @return string
     */
    protected function setNewUIPage($file_name)
    {
        $n=strrpos($file_name,"/");
        if($n==false)
            $file_name=$this->newUIPrefix.$file_name;
        else
        {
            $newF=substr($file_name,0,$n+1);
            $file_name=$newF.$this->newUIPrefix.substr($file_name,$n+1);
        }
        return $file_name;
    }

    public function render($file_name,$array=null,$return = false)
    {
        $this->isExternal=$_GET["t"];
        if($this->isExternal && $this->layout == "main")
            $this->layout="emptyMain";
        else {
            if ($this->isMobileView && $this->layout == "main")
            {
                $this->layout = "mobileMain";
            }
        }
        if(!$this->isMobileView)
        {
            $file_name=$this->setNewUIPage($file_name);
            $this->layout=$this->setNewUIPage($this->layout);
        }

        $dic=SystemModule::getModuleCodeDic();
        $this->moduleItem=$dic[strtolower($this->treeCode)];
        $this->moduleName=$this->moduleItem["name"];
        $this->moduleUrl=$this->moduleItem["page_url"];
        $this->moduleParentIds=$this->moduleItem["parent_ids"]."0";

        $this->map = Map::$v;

        $isQuotaDetail = ($this->getRoute() == '/quota/detail');
        if (in_array($this->getId(), array('check2', 'check3', 'check13')) && !$isQuotaDetail && (WeiXinService::inWeixin() || $this->isMobile()) && Mod::app()->request->getParam('__ajax') != 1) {
            include(ROOT_DIR . '/protected/views/h5.php');
            return true;
        }

        if (Mod::app()->request->getParam('__ajax') == 1) {
            $file_name =  $file_name .'_mobile';
        }

        parent::render($file_name,$array);
    }

    /**
     * 获取返回的页面，特别是错误页面的返回地址
     * @return string
     */
    public function getBackPageUrl()
    {
        if(!empty($this->backUrl))
        {
            return $this->backUrl;
        }
        if(!empty($_SERVER['HTTP_REFERER']))
            return $_SERVER['HTTP_REFERER'];
        else
            return $this->mainUrl;
    }

    /**
     * 获取修改页面返回的URl
     * @return string
     */
    public function getEditBackUrl()
    {
        if(!empty($this->backUrl))
        {
            return $this->backUrl;
        }
        else
            return $this->mainUrl;
    }

    /**
     * 判断用户权限
     * @param $userId
     * @return bool
     */
    protected function checkUserRight($userId)
    {
        $rightData=SystemUser::getFormattedRightCodes($userId);

        if(empty($this->rightCode))
            return true;

        $actionId=strtolower($this->getAction()->getId());

        //echo $this->rightCode."---".$actionId;

        //update by youyi000@2017-01-19 根据实际情况，改为不先判断权限码，直接判断actionId
        //如果在忽略的Action列表中，则直接返回，注意这段代码需要在上述代码后，先判断是否有当前模块的权限，然后再判断当前模块下的具体权限
        if(!empty($this->filterActions) && strpos("#,".strtolower($this->filterActions).",",",".$actionId.",")>0)
            return true;

        $moduleDic=SystemModule::getModuleDic();

        $module=$rightData["items"][strtolower($this->rightCode)];
        if(empty($module))
            return false;

        //var_dump($module);


        $actions="#,".strtolower($module["actions"]).",";

        $moduleActions=strtolower("#".$moduleDic[$module["id"]]["actions"].",");

        //if(strpos($moduleActions,"|".$actionId.",")==0 || (strpos($moduleActions,"|".$actionId.",")>0 && strpos($actions,",".$actionId.",")>0))
        //上述条件表示如果当前的action没有在模块权限中，则认为有权或才在模块权限中，且有授权，暂时修改判断规则，改用页面指定过滤，其他必须有授权
        //by youyi000 @2015-12-05

        //echo $moduleActions."|".$actionId;
        if(strpos($moduleActions,"|".$actionId.",")>0 && strpos($actions,",".$actionId.",")>0)
            return true;
        else
            return false;
    }

    /**
     * 获取sql语句中的条件语句
     * @param $params
     * @param bool $hasWhere
     * @return string
     */
    public function getWhereSql($params,$hasWhere=true)
    {
        if(isset($params['pageSize'])) unset($params['pageSize']);
        $where="";

        if($hasWhere)
            $where=" where 1=1";

        if(!is_array($params) || count($params)<1)
            return $where;

        $conditions=array();
        foreach ($params as $k=>$v)
        {
            $v=trim($v);
            //if($v != '')
            if(!empty($v) || $v===0 || $v==="0")
            {
                $v = addslashes($v);
                if (substr($k, strlen($k) - 1) == "*")
                {
                    $key = substr($k, 0, strlen($k) - 1);
                    $conditions[]=$key." like '%".$v."%'";
                }
                else
                    $conditions[]=$k."='".$v."'";
            }
        }
        if(count($conditions)<1)
            return $where;
        $where=implode(" and ",$conditions);
        if($hasWhere)
            $where=" where ".$where;

        return $where;
    }

    #region 新的列表数据获取及展示代码

    /**
     * 获取显示的数据
     * 返回的数组为
     * array{
     *      "data"=>array{}，//实际查询数据的返回数据
     *      "pageSize"=>20,
     *      "page"=>1,
     *      "totalRows"=>200,
     *      "totalPage"=>10,
     * }
     *
     * @param string $sql  基本格式：$sql="select {col} from table";
     * @param string $fields    默认为：*
     * @param int $totalRows    当=0时表示需要计算记录数，-1时表示不计算记录数，其他则为指定记录数
     * @param int $pageSize 每页记录数
     * @param int $dbType
     * @return array|null
     */
    public function getShowData($sql,$fields="*",$totalRows= 0,$pageSize=10, $dbType = 0)
    {
        if(empty($sql))
            return null;

        if($totalRows==0)
        {
            $countSql=str_replace("{col}","count(*) as total",$sql);
            //$countSql = 'select count(*) as total from (' . str_replace('{limit}', '', str_replace('{col}', ' 1 ', $sql) . ")cou");
            $d=Utility::query($countSql,$dbType);
            $totalRows=$d[0]["total"];
        }

        $data=array();

        if($totalRows!=0)
        {
            /*if(array_key_exists('pageSize', $_GET))
            {
                $pageSize=$_GET['pageSize'];
            }
            else
            {
                $search=$_GET['search'];
                if(is_array($search) && array_key_exists('pageSize', $search))
                    $pageSize=$search['pageSize'];
            }*/
            $pageSize = $this->getSearchPageSize();
            //$pageSize = array_key_exists('pageSize', $_GET) ? $_GET['pageSize'] : $pageSize;
            //$pageSize = array_key_exists('pageSize', $_GET['search[pageSize]']) ? $_GET['search']['pageSize'] : $pageSize;
            $pageSize=empty($pageSize)?10:$pageSize;
            if($pageSize<0 || $pageSize>1000)
                $pageSize=20;

            $data['pageSize']=$pageSize;

            $currPage = $this->getSearchPage();
//            $currPage=array_key_exists('page', $_GET) ? $_GET['page'] : 1;
            $page=!empty($currPage) ? $currPage : 1;
            $page=$page<1?1:$page;

            $totalPage = -1;

            if($totalRows>0)
            {
                $totalPage = ceil($totalRows / $pageSize);
                $page= $totalPage < $page ? $totalPage : $page;
                $data["totalRows"]=$totalRows;
                $data["totalPage"]=$totalPage;
            }

            $data["page"]=$page;
            $begin = ($page - 1) * $pageSize;

            $dataSql=str_replace("{col}",$fields,$sql);
            $dataSql.=" limit ".$begin.",".$pageSize;

            $data["data"]=Utility::query($dataSql,$dbType);
        }

        return $data;
    }


    public function showData($data,$columns,$config=array())
    {

    }


    #endregion

    /**
     * 获取页面显示的数据，同时会把search放到返回的数据中
     * @param string $sql  基本格式：$sql="select {col} from table";
     * @param string $col 默认为：*
     * @param int $totalRows 默认为0，表示计算总数，-1表示不计算总记录数，>0表示指定总记录数
     * @param int $pageSize 每页记录数
     * @param int $dbType
     * @return array
     */
    public function queryTablesByPage($sql, $col, $totalRows = 0,$pageSize=10, $dbType = 0)
    {

        $sql=str_replace("{limit}","",$sql);
        $data=$this->getShowData($sql,$col,$totalRows,$pageSize,$dbType);

        $result = array();
//        $result['search'] = $_GET['search'];
        $result['search'] = $this->getSearch();
//        $result['search']["pageSize"]=$data["pageSize"];
        $result['data']['pageCount'] = $data["totalPage"];
        $result['data']['rows'] = $data["data"];
        $result['data']['total'] = $data["totalRows"];
        $result['data']['page'] = $data["page"];
        return $result;
    }

    /**
     * 判断客户端浏览器是否是移动设备
     *
     * @return bool
     */
    function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA']))
        {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT']))
        {
            $clientkeywords = array ('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT']))
        {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            }
        }
        return false;
    }

    public function td_format($row,$key,$type,$href_text,$map_name,$map,$escape,$style,$class,$tdConfig){
        $format=Mod::app()->format;
        $key_array = explode(',',$key);
        $val = $row[$key_array[0]];
        $val=$format->formatText($val);

        if (!empty($this->newUIPrefix)) {
            $style = $style==''?'text-align:left':$style;
        } else {
            $style = $style==''?'text-align:center':$style;
        }
        if (!isset($val)||strlen(trim($val))==0){
            return "<td name='$key' style='$style' class='$class'>-</td>";
        }

        switch($type){
            case "amount":
                $val = "￥" . number_format($val/100,2);
                return "<td name='$key' value='$val' style='$style' class='$class' title='$val'>".$val."</td>";
            case "amount_number":
                $val = number_format($val/100,2);
                return "<td name='$key' value='$val' style='$style' class='$class' title='$val'>".$val."</td>";
            case "amount_cny":
                return "<td name='$key' value='$val' style='$style' class='$class' title='￥".number_format($val/100,2)."元'>￥".number_format($val/100,2)."元</td>";
            case "amountWan":
                $val = "￥" . number_format($val/1000000,2) . "万元";
                return "<td name='$key' value='$val' style='$style' class='$class' title='$val'>".$val."</td>";
            case "amountWan6":
                $val = "￥" . number_format($val/1000000,6) . "万元";
                return "<td name='$key' value='$val' style='$style' class='$class' title='$val'>".$val."</td>";
	        case "amount_wan":
                $val = number_format(round($val, 2), 2);
		        return "<td name='$key' value='$val' style='$style' class='$class' title='$val'>".$val."</td>";
            case "date":
                return "<td name='$key' value='$val' style='$style' class='$class' title='$val'>$val</td>";
            case "href":
            {
                if(is_array($href_text) || is_callable($href_text))
                {
                    $href_text = call_user_func($href_text, $row,$this,$tdConfig["params"]);
                }
                $nval = $href_text;
                for ($i=1 ; $i<=count($key_array);$i++)
                {
                    $nval = str_replace("{{$i}}",$format->formatText($row[$key_array[$i-1]]),$nval);
                }
                $str = str_replace('{escape}',urlencode(iconv("gbk", "UTF-8",$escape)),str_replace('{shortstr}',$this->substr_cut($val,25),$nval));
                $return = "<td name='$key' value='$val' style='$style' class='$class'";
                if ($str == strip_tags($str)) { //不含html标签
                    $return .= " title='$str'";
                }
                return $return . ">".$str."</td>";
            }
            case "map_val":
                if(is_array($map[$map_name][$val]) && isset($map[$map_name][$val]['name'])) {
                    $str = $map[$map_name][$val]['name'];
                } else {
                    $str = $map[$map_name][$val];
                }
                return "<td name='$key' value='$val' style='$style' class='$class' title='$str'>".$str."</td>";
            case "map_array":
                $fName=empty($tdConfig["map_text_name"])?"name":$tdConfig["map_text_name"];
                $val=$map[$map_name][$val][$fName];
                return "<td name='$key' value='$val' style='$style' title='$val' class='$class'>".$val."</td>";
            case "map_vals":
                $str = "";
                foreach($row as $array_val){
                    if (strlen($str)==0){
                        $str = $map[$map_name][$array_val];
                        continue;
                    }
                    $str = $str . ',' . $map[$map_name][$array_val];
                }
                return "<td name='$key' value='$val' style='$style' title='$str' class='$class'>".$str."</td>";
            case "html":
                return "<td name='$key' style='$style' title='$val' class='$class'>".$val."</td>";
            default :
                return "<td name='$key' value='$val' style='$style' title='$val' class='$class'>".$val."</td>";
        }
    }

    function substr_cut($str_cut,$length)
    {
        if (mb_strlen($str_cut) > $length)
        {
            $str_cut = mb_substr($str_cut,0,$length,'utf-8')."..";
        }
        return $str_cut;
    }

    /**
     *  此方法为显示列表方法
     *       $arrays为数组，描述了列表对应的表头，值名称和类型。
     *       格式为$arrays = array(array('key'=>'tx_no','type'=>'','text'=>'交易流水号','href_text'=>'www.baidu.cm{1}'));
     *       其中key对应$data数组中每行的key
     *       type对应传入的参数格式，格式化处理使用。
     *				-amount，则自动计算为金额，保留小数点后2位。
     *				-date,时间，会自动右对齐
     *				-href_text,链接字符，会自动居中
     *				-selected_map，字典值，会自动在字典"$map[$map_name]"中查找对应的值
     *				-其他，不做格式化处理。
     *	text为列头显示文本
     *	href_text只针对type为href的格式。显示时自动替换{1}为每列的值
     *	map_name只针对type为selected_map的格式，对应的字典名。
     *       $data为传入列表内容，格式为array('total'=>1,'rows'=>array(array('tx_no','123')));
     *				-total 为行数
     *				-rows 为具体列
     * page 是否展示分页，1：不展示，默认展示
     *
     * @param $arrays
     * @param $data
     * @param string $title
     */
    function show_table($arrays,$data,$title="",$tableStyle="",$tableClass="",$tableId="",$isDataTable=false){
        $tStyle="";
        if(!$isDataTable && isset($tableStyle) && $tableStyle!="")
            $tStyle="style='".$tableStyle."'";

        if(!isset($tableClass) || $tableClass=="")
            $tableClass="table-bordered";
        if($isDataTable)
            $tableClass.=" data-table";

        if (!is_array($data) || !array_key_exists("total",$data) || $data[total]==0)
            $tStyle="";
        $sectionClass='class="content-list"';
        $containerStart="<section $sectionClass><div class=\"panel panel-default\" $tStyle>";
        $containerEnd="</div></section>";
        if($isDataTable)
        {
            $sectionClass="";
            $containerStart='<div class="box box-solid">
                        <div class="box-body no-padding">';
            $containerEnd="</div></div>";
        }
        echo $containerStart;
        if (!empty($title)){
            echo <<<html
                <div class="panel-heading">$title</div>

html;
        }
        //if (!is_array($data) || !array_key_exists("total",$data)||$data[total]==0)
        if(!is_array($data) || !is_array($data["rows"]) || count($data["rows"])==0)
        {
            echo  " <p></p><p style='padding-left: 10px;'>您好，当前没有数据。</p>";
        }else{

if(!empty($tableId))
    $tableId="id='".$tableId."'";
            echo <<<html

            <table class="table table-condensed table-hover $tableClass" $tStyle $tableId>
                    <thead>
                      <tr>
html;
            foreach ($arrays as $val){
                $style=array_key_exists('style',$val)?$val['style']:'text-align:center;';
                echo <<<html
                                                                <th style='$style'>
                                                                $val[text]</th>
html;
            }
            echo <<<html
                                                         </tr>

                                                        </thead>

                                                        <tbody>
html;
            foreach ($data[rows] as $tr){
                echo <<<html
								<tr>
html;
                foreach($arrays as $val){
                    $td = $this->td_format($tr,$val[key],array_key_exists('type',$val)?$val[type]:'',array_key_exists('href_text',$val)?$val['href_text']:'',array_key_exists('map_name',$val)?$val[map_name]:'',$this->map,array_key_exists('escape',$val)?$val['escape']:'',array_key_exists('style',$val)?$val['style']:'',array_key_exists('class',$val)?$val['class']:'',$val);
                    echo <<<html
									$td
html;
                }
                echo <<<html
								</tr>
html;
            }
            echo <<<table_end_html
							</tbody>
                                                </table>
table_end_html;
            include (ROOT_DIR.'/protected/views/layouts/page.php');
        }
        echo $containerEnd;
        //</section>
    }

    /**
     *  此方法为显示列表方法
     *  @param array $columnConfig 表格字段配置，数组，描述了列表对应的表头，值名称和类型。
     *       格式为$config = array(array('key'=>'tx_no','type'=>'','text'=>'交易流水号','href_text'=>'www.baidu.cm{1}'));
     *       其中key对应$data数组中每行的key
     *       type对应传入的参数格式，格式化处理使用。
     *                -amount，则自动计算为金额，保留小数点后2位。
     *                -date,时间，会自动右对齐
     *                -href_text,链接字符，会自动居中
     *                -selected_map，字典值，会自动在字典"$map[$map_name]"中查找对应的值
     *                -其他，不做格式化处理。
     *    text为列头显示文本
     *    href_text只针对type为href的格式。显示时自动替换{1}为每列的值
     *    map_name只针对type为selected_map的格式，对应的字典名。
     *       $data为传入列表内容，格式为array('total'=>1,'rows'=>array(array('tx_no','123')));
     *                -total 为行数
     *                -rows 为具体列
     * @param array $data           #表格数据源
     * @param array $tableAttr      #表格属性数组，可添加table所有html属性键值对 如：$tableAttr = ['id' => '1', 'style' => '', 'class' => ''],
     * @param bool $isShowPage     #是否显示分页
     * @param int $floatColumns    #表格右侧浮动列数
     */
    function showTableWithNewUI($columnConfig, $data, $tableAttr=[], $isShowPage = true, $floatColumns = 1)
    {
        $tableAttr['style'] = Utility::isEmpty($tableAttr) || empty($tableAttr['style']) ? 'width: 100%' : $tableAttr['style'];
        $tableAttr['class'] = Utility::isEmpty($tableAttr) || empty($tableAttr['class']) ? 'data-table dataTable stripe hover nowrap table-fixed' : $tableAttr['class'];


        /*if (!is_array($data) || !array_key_exists("total", $data) || $data['total'] == 0)
        {
            $tableAttr['style'] = "";
        }*/
        if (!is_array($data) || !is_array($data["rows"]) || count($data["rows"]) == 0)
        {
            echo " <p></p><p>您好，当前没有数据。</p>";
        } else
        {
            $attr = '';
            if (Utility::isNotEmpty($tableAttr)) {
                foreach ($tableAttr as $attrName => $attrVal) {
                    $attr .= $attrName . '="' . $attrVal .'" ';
                }
            }
            echo <<<html

            <table $attr>
                    <thead>
                      <tr>
html;
            foreach ($columnConfig as $val)
            {
                $style = array_key_exists('style', $val) ? $val['style'] : '';
                echo <<<html
                                                                <th style='$style'>
                                                                $val[text]</th>
html;
            }
            echo <<<html
                                                         </tr>

                                                        </thead>

                                                        <tbody>
html;
            foreach ($data['rows'] as $tr)
            {
                echo <<<html
								<tr>
html;
                foreach ($columnConfig as $val)
                {
                    $td = $this->td_format($tr, $val['key'], array_key_exists('type', $val) ? $val['type'] : '', array_key_exists('href_text', $val) ? $val['href_text'] : '', array_key_exists('map_name', $val) ? $val['map_name'] : '', $this->map, array_key_exists('escape', $val) ? $val['escape'] : '', array_key_exists('style', $val) ? $val['style'] : '', array_key_exists('class', $val) ? $val['class'] : '', $val);
                    echo <<<html
									$td
html;
                }
                echo <<<html
								</tr>
html;
            }
            echo <<<table_end_html
							</tbody>
                                                </table>
table_end_html;

            if ($isShowPage) {
                include(ROOT_DIR . '/protected/views/layouts/new_page.php');
            }
            echo <<<html
                <script >page.initDatatables('', {
                	columns: {$floatColumns}
                })</script>
html;
        }
    }

    function show_table_nopage($arrays,$data,$title="",$tableStyle="",$tableClass=""){

        if (isset($tableStyle) && $tableStyle != "")
            $tStyle = "style='" . $tableStyle . "'";

        if (!isset($tableClass) || $tableClass == "")
            $tableClass = "table-bordered";

        echo <<<html
        	<section class="content-list">
        	<div class="panel panel-default"  $tStyle>
html;
        if (isset($title) && $title != "")
        {
            echo <<<html
                <div class="panel-heading">$title</div>

html;
        }

        if (!is_array($data) || !array_key_exists("total", $data) || $data[total] == 0)
        {
            echo <<<html

                	<div class="panel-body">
                    <p>您好，当前没有数据。</p>
                  </div>

html;
        }
        else
        {
            echo <<<html

        <table class="table table-condensed table-hover $tableClass" $tStyle>
                <thead>
                  <tr>
html;
            foreach ($arrays as $val){
                $style=array_key_exists('style',$val)?$val['style']:'text-align:center;';
                echo <<<html
                                                                <th style='$style'>
                                                                $val[text]</th>
html;
            }
            echo <<<html
                                                         </tr>

                                                        </thead>

                                                        <tbody>
html;
            foreach ($data[rows] as $tr)
            {
                echo <<<html
								<tr>
html;
                foreach ($arrays as $val)
                {
                    $td = $this->td_format($tr,$val[key],array_key_exists('type',$val)?$val[type]:'',array_key_exists('href_text',$val)?$val['href_text']:'',array_key_exists('map_name',$val)?$val[map_name]:'',$this->map,array_key_exists('escape',$val)?$val['escape']:'',array_key_exists('style',$val)?$val['style']:'',array_key_exists('class',$val)?$val['class']:'',$val);
                    echo <<<html
									$td
html;
                }
                echo <<<html
								</tr>
html;
            }
            echo <<<table_end_html
							</tbody>
                                                </table>
table_end_html;

        }
        echo <<<html

                                </div> <!--end panel-default-->
</section>
html;
    }

    public function getUser()
    {
        $res = array("user_id"=>$this->userId);
        return $res;
    }

    /**
     * @desc 生成列表页
     * @param $_data_ [             #列表页数据源，必传
     *      'search' => []          #查询字段数组，必传
     *      'data' => []            #表格数据，必传
     * ]
     * @param $headerArray [           #头部信息配置，非必传
     *      'menu_config' => [],        #菜单配置，详细参数见loadHeaderWithNewUI() $menuConfig参数，非必传
     *      'button_config' => [],      #按钮配置，详细参数见loadHeaderWithNewUI() $buttonConfig参数，非必传
     *      'is_show_back_bread' => true,    #是否显示返回面包屑按钮配置，详细参数见loadHeaderWithNewUI() $isShowBackBread参数，有默认值，可不传
     *      'is_show_export' => false,  #是否显示导出按钮配置，详细参数见loadHeaderWithNewUI() $isShowExport参数，有默认值，可不传
     *
     * ]
     * @param $searchArray [          #查询区域配置，非必传
     *      'search_config' => [],      #查询表单配置，详细参数见loadSearchFormWithNewUI() $searchConfig参数，必传
     *      'search_lines' => 1,      #查询区域展示行数，详细参数见loadSearchFormWithNewUI() $searchLines参数，有默认值，可不传
     * ]
     * @param $tableArray [             #表格配置信息，非必传
     *      'column_config' => [],       #表格字段配置，详细参数见showTableWithNewUI() $columnConfig参数，必传
     *      'attr' => [],               #表格属性配置，详细参数见showTableWithNewUI() $$tableAttr参数，非必传
     *      'is_show_page' => true,    #表格是否要分页，详细参数见showTableWithNewUI() $isShowPage参数，有默认值，可不传
     *      'float_columns' => 1，      #表格右侧浮动列数，详细参数见showTableWithNewUI() $floatColumns参数，有默认值，可不传
     * ]
     */
    public function showIndexViewWithNewUI($_data_, $headerArray = array(), $searchArray = array(), $tableArray = array())
    {
        $headerArray['menu_config']        = array_key_exists('menu_config', $headerArray) ? $headerArray['menu_config'] : array();
        $headerArray['button_config']      = array_key_exists('button_config', $headerArray) ? $headerArray['button_config'] : array();
        $headerArray['is_show_back_bread'] = array_key_exists('is_show_back_bread', $headerArray) ? $headerArray['is_show_back_bread'] : false;
        $headerArray['is_show_export']     = array_key_exists('is_show_export', $headerArray) ? $headerArray['is_show_export'] : false;
        $headerArray['export_action']      = array_key_exists('export_action', $headerArray) ? $headerArray['export_action'] : 'export';
        $this->loadHeaderWithNewUI($headerArray['menu_config'], $headerArray['button_config'], $headerArray['is_show_back_bread'], $headerArray['is_show_export'], $headerArray['export_action']);
        if(Utility::isNotEmpty($_data_) && array_key_exists('search', $_data_) && array_key_exists('data', $_data_)) {
            if (!array_key_exists('title', $headerArray) || empty($array["title"]))
            {
                $title = $this->moduleName;
            } else
            {
                $title = $array["title"];
            }
            echo <<<html
        <section class="el-container is-vertical">
            <div class="main-content">
html;
            if (Utility::isNotEmpty($searchArray)) {
                $searchArray['search_config'] = array_key_exists('search_config', $searchArray) ? $searchArray['search_config'] : array();
                $searchArray['search_lines'] = array_key_exists('search_lines', $searchArray) ? $searchArray['search_lines'] : 1;
                $searchArray['is_show_reset_button'] = array_key_exists('is_show_reset_button', $searchArray) ? $searchArray['is_show_reset_button'] : true;
                $this->loadSearchFormWithNewUI($searchArray['search_config'], $_data_['search'], $searchArray['search_lines'], $searchArray['is_show_reset_button']);
            }
            echo <<<html
                <div class="main-content_inner">
html;
                if (Utility::isNotEmpty($tableArray)) {
                    $tableArray['column_config'] = array_key_exists('column_config', $tableArray) ? $tableArray['column_config'] : array();
                    $tableArray['attr'] = array_key_exists('attr', $tableArray) ? $tableArray['attr'] : array();
                    $tableArray['is_show_page'] = array_key_exists('is_show_page', $tableArray) ? $tableArray['is_show_page'] : true;
                    $tableArray['float_columns'] = array_key_exists('float_columns', $tableArray) ? $tableArray['float_columns'] : 1;
                    $this->showTableWithNewUI($tableArray['column_config'], $_data_['data'], $tableArray['attr'], $tableArray['is_show_page'], $tableArray['float_columns']);
                }
                echo <<<html
                </div>
html;
            echo <<<html
            </div>
        </section>
html;
        }
    }

    /**
     * @desc 以gridview方式加载表格
     * @param $headerArray [           #头部信息配置，非必传
     *      'menu_config' => [],        #菜单配置，详细参数见loadHeaderWithNewUI() $menuConfig参数，非必传
     *      'button_config' => [],      #按钮配置，详细参数见loadHeaderWithNewUI() $buttonConfig参数，非必传
     *      'is_show_back_bread' => true,    #是否显示返回面包屑按钮配置，详细参数见loadHeaderWithNewUI() $isShowBackBread参数，有默认值，可不传
     *      'is_show_export' => false,  #是否显示导出按钮配置，详细参数见loadHeaderWithNewUI() $isShowExport参数，有默认值，可不传
     *
     * ]
     * @param $searchArray [          #查询区域配置，非必传
     *      'search_config' => [],      #查询表单配置，详细参数见loadSearchFormWithNewUI() $searchConfig参数，必传
     *      'search_lines' => 1,      #查询区域展示行数，详细参数见loadSearchFormWithNewUI() $searchLines参数，有默认值，可不传
     * ]
     * @param $widgetConfig [            #widget表格配置信息，非必传
     *      'widget_class' => ''            #widget class name.
     *      'widget_property' => [       #表格属性相关配置，必传
     *         'id'=>'data-grid',        #表格容器id
     *         'emptyText'=>'您好，当前没有数据。',       #数据为空时，提示文案
     *         'dataProvider'=> ZSqlDataProvider       #数据源
     *         'tableOptions'=>[],                     #表格配置信息
     *         'itemsCssClass' => 'show-table',         #表格css名，若存在tableOptions['class']时，会把这两个值合并
     *         'isShowSummary' => false,                #是否显示分页总数
     *         'columns'=>[],                           #表格列配置
     *         'pager' => function($data) {             #分页相关配置，如果是函数，会执行该函数
     *              $data['total'] = $data['itemCount'];
     *              include(ROOT_DIR. '/protected/views/layouts/new_page.php');
     *         }
     *      ],
     *      'float_columns' => 1，      #表格右侧浮动列数，可不传
     * ]
     * @throws Exception
     */
    public function showGridViewWithNewUI($headerArray = array(), $searchArray = array(), $widgetConfig = array())
    {
        $headerArray['menu_config']        = array_key_exists('menu_config', $headerArray) ? $headerArray['menu_config'] : array();
        $headerArray['button_config']      = array_key_exists('button_config', $headerArray) ? $headerArray['button_config'] : array();
        $headerArray['is_show_back_bread'] = array_key_exists('is_show_back_bread', $headerArray) ? $headerArray['is_show_back_bread'] : false;
        $headerArray['is_show_export']     = array_key_exists('is_show_export', $headerArray) ? $headerArray['is_show_export'] : false;
        $headerArray['export_action']      = array_key_exists('export_action', $headerArray) ? $headerArray['export_action'] : 'export';
        $this->loadHeaderWithNewUI($headerArray['menu_config'], $headerArray['button_config'], $headerArray['is_show_back_bread'], $headerArray['is_show_export'], $headerArray['export_action']);
            echo <<<html
        <section class="el-container is-vertical">
            <div class="main-content">
html;
            if (Utility::isNotEmpty($searchArray)) {
                $searchArray['search_config'] = array_key_exists('search_config', $searchArray) ? $searchArray['search_config'] : array();
                $searchArray['search_lines'] = array_key_exists('search_lines', $searchArray) ? $searchArray['search_lines'] : 1;
                $searchArray['is_show_reset_button'] = array_key_exists('is_show_reset_button', $searchArray) ? $searchArray['is_show_reset_button'] : true;
                $this->loadSearchFormWithNewUI($searchArray['search_config'], $_GET['search'], $searchArray['search_lines'], $searchArray['is_show_reset_button']);
            }
            echo <<<html
                <div class="main-content_inner">
html;
            if (Utility::isNotEmpty($widgetConfig))
            {
                $widgetClass = !empty($widgetConfig['widget_class']) ? $widgetConfig['widget_class'] : 'ZGridView';
                $widgetProperty = !empty($widgetConfig['widget_property']) ? $widgetConfig['widget_property'] : array();
                $floatColumns = array_key_exists('float_columns', $widgetConfig) ? $widgetConfig['float_columns'] : 1;
                $this->widget($widgetClass, $widgetProperty);
                echo <<<html
                <script >page.initDatatables('',{
                	columns: {$floatColumns}
                })</script>
html;
            }
            echo <<<html
                </div>
html;
            echo <<<html
            </div>
        </section>
html;
    }

    public function loadForm($array,$_data_=null,$col_array=null,$isShowExport=0, $isShowBack = false)
    {
        if(!empty($_data_) && key_exists("search",$_data_))
            $search=$_data_["search"];
        else
            $search=$this->getSearch();

        if(empty($array["title"]))
            $title = $this->moduleName;
        else
            $title = $array["title"];

        $method=empty($array["method"])?"get":$array["method"];

        $form_url = $array["form_url"];
        echo <<<html
<div class="box ">
	<div class="box-body">
                        <form method='$method' action='$form_url' class="search-form">
                                <div class="container-fluid">
html;
        $input_array = $array['input_array'];
        $index = 0;
        $hiddenInput="";
        for($i = 0;$i<count($input_array);$i++){
            if ($index%3==0){
                echo <<<html
								<div class="row">
html;
            }
            switch($input_array[$i]['type']){
                case 'text':
                    $disabled_val = $input_array[$i]['disabled']?'disabled':'';
                    echo <<<html
					<div class="col-sm-4">
                        <div class="input-group">
                          <div class="input-group-addon">{$input_array[$i]['text']}</div>
                          <input type="text" class="form-control input-sm" name="search[{$input_array[$i]['key']}]" id="{$input_array[$i][id]}" placeholder="{$input_array[$i][text]}"  value="{$search[$input_array[$i]['key']]}" $disabled_val/>
                        </div>
                      </div>
html;
                    break;
                case 'amount':
                    $disabled_val = $input_array[$i]['disabled']?'disabled':'';
                    $keyName=$input_array[$i]['key'];

                    if(!strpos($keyName,">") && !strpos($keyName,"<"))
                        $keyName=$keyName."/100";
                    else
                    {
                        $keyName=str_replace(">","/100>",$keyName);
                        $keyName=str_replace("<","/100<",$keyName);
                    }
                    echo <<<html
					<div class="col-sm-4">
                        <div class="input-group">
                          <div class="input-group-addon">{$input_array[$i][text]}</div>
                          <input type="text" class="form-control input-sm" name="search[{$keyName}]" id="{$input_array[$i][id]}" placeholder="{$input_array[$i][text]}"  value="{$search[$keyName]}" $disabled_val/>
                          <span class="input-group-addon">元</span>
                        </div>
                      </div>
html;
                    break;
                case 'select':
                    echo <<<html
					<div class="col-sm-4">
                        <div class="input-group">
                          <div class="input-group-addon">{$input_array[$i][text]}</div>
                          <select name='search[{$input_array[$i]['key']}]'  class="form-control input-sm">
html;
                    if(empty($input_array[$i]['noAll']))
                        echo "<option value='' >全部</option>";

                    foreach ($this->map[$input_array[$i]['map_name']] as $key=>$val){
                        $select = (''.$key)===$search[$input_array[$i]['key']]?'selected':'';
                        echo <<<html
											<option value='$key' $select>$val</option>
html;
                    }
                    echo <<<html
										</select>
                        </div>
                      </div>
html;
                    break;
                case 'yearSelect':
                    echo <<<html
                    <div class="col-sm-4">
                        <div class="input-group">
                          <div class="input-group-addon">{$input_array[$i][text]}</div>
                          <select name='search[{$input_array[$i]['key']}]'  class="form-control input-sm">
											<option value='' >全部</option>
html;
                    for($kk=2014;$kk<=date("Y");$kk++)
                    {
                        $select = (''.$kk)===$search[$input_array[$i]['key']]?'selected':'';
                        echo <<<html
											<option value='$kk' $select>$kk</option>
html;
                    }
                    echo <<<html
										</select>
                        </div>
                      </div>

html;
                    break;
                case 'monthSelect':
                    echo <<<html
                    <div class="col-sm-4">
                        <div class="input-group">
                          <div class="input-group-addon">{$input_array[$i][text]}</div>
                          <select name='search[{$input_array[$i]['key']}]'  class="form-control input-sm">
											<option value='' >全部</option>
html;
                    for($kk=1;$kk<=12;$kk++)
                    {
                        $select = (''.$kk)===$search[$input_array[$i]['key']]?'selected':'';
                        echo <<<html
											<option value='$kk' $select>$kk</option>
html;
                    }
                    echo <<<html
										</select>
                        </div>
                      </div>

html;
                    break;

                case 'managerUser':
                    echo <<<html
                    <div class="col-sm-4">
                        <div class="input-group">
                          <div class="input-group-addon">{$input_array[$i][text]}</div>
                          <select name='search[{$input_array[$i]['key']}]'  class="form-control input-sm">
											<option value='' >所有人员</option>
html;
                    $users=UserService::getProjectManageUsers();
                    foreach($users as $v)
                    {
                        $select = (''.$v["user_id"])==$search[$input_array[$i]['key']]?'selected':'';
                        echo '<option value="'.$v["user_id"].'" '.$select.'>'.$v["name"].'</option>';
                    }
                    echo <<<html
										</select>
                        </div>
                      </div>

html;
                    break;

                case 'mainRole':
                    echo <<<html
                    <div class="col-sm-4">
                        <div class="input-group">
                          <div class="input-group-addon">{$input_array[$i][text]}</div>
                          <select name='search[{$input_array[$i]['key']}]'  class="form-control input-sm">
                                            <option value='' >全部角色</option>
html;
                    $users=UserService::getAllRoles();
                    foreach($users as $v)
                    {
                        $select = (''.$v["role_id"])==$search[$input_array[$i]['key']]?'selected':'';
                        echo '<option value="'.$v["role_id"].'" '.$select.'>'.$v["role_name"].'</option>';
                    }
                    echo <<<html
                                        </select>
                        </div>
                      </div>

html;
                    break;

                case 'corpName':
                    echo <<<html
                    <div class="col-sm-4">
                        <div class="input-group">
                          <div class="input-group-addon">{$input_array[$i][text]}</div>
                          <select name='search[{$input_array[$i]['key']}]' id='{$input_array[$i][id]}' class="form-control input-sm">
                                            <option value='' >全部交易主体</option>
html;
                    $cors=Corporation::getActiveCorporations();
                    foreach($cors as $v)
                    {
                        $select = (''.$v["corporation_id"])==$search[$input_array[$i]['key']]?'selected':'';
                        echo '<option value="'.$v["corporation_id"].'" '.$select.'>'.$v["name"].'</option>';
                    }
                    echo <<<html
                                        </select>
                        </div>
                      </div>

html;
                    break;

                case 'date':
                    echo <<<html
					<div class="col-sm-4">
                        <div class="input-group">
                          <div class="input-group-addon">{$input_array[$i][text]}</div>
                          <input type="text" class="form-control input-sm" name='search[{$input_array[$i]['key']}]' id='{$input_array[$i][id]}' value='{$search[$input_array[$i]['key']]}'/>
                        </div>
                      </div>

html;

                    break;

                case 'dateMonth':
                    echo <<<html
                    <div class="col-sm-4">
                        <div class="input-group">
                          <div class="input-group-addon">{$input_array[$i][text]}</div>
                          <input type="text" class="form-control input-sm" name='search[{$input_array[$i]['key']}]' id='{$input_array[$i][id]}' value='{$search[$input_array[$i]['key']]}'/>
                        </div>
                      </div>

html;

                    break;

                case 'datetime':
                    echo <<<html
					<div class="col-sm-4">
                        <div class="input-group">
                          <div class="input-group-addon">{$input_array[$i][text]}</div>
                          <input type="text" class="form-control input-sm" name='search[{$input_array[$i]['key']}]' id='{$input_array[$i]['id']}'  value='{$search[$input_array[$i]['key']]}'/>
                        </div>
                      </div>

html;

                    break;
                case 'null_select':
                    echo <<<html
					<div class="col-sm-4">
                        <div class="input-group">
                          <div class="input-group-addon">{$input_array[$i][text]}</div>
                          <select name='search[{$input_array[$i]['key']}]'  class="form-control input-sm" id='{$input_array[$i]['id']}'>
										</select>
                        </div>
                      </div>

html;
                    break;

                case 'custom':
                    echo <<<html
                    <div class="col-sm-4">
                        <div class="input-group">
                          <div class="input-group-addon">{$input_array[$i][text]}</div>
html;
                    $content=$input_array[$i]["content"];
                    if (is_callable($content))
                    {
                        $content = call_user_func($content, $search, $input_array[$i],$this);
                    }
                    echo $content;
                    echo <<<html
                        </div>
                      </div>

html;

                    break;
                case 'subject':
                    echo <<<html
                    <div class="col-sm-4">
                        <div class="input-group">
                          <div class="input-group-addon">{$input_array[$i][text]}</div>
                          <select name='search[{$input_array[$i]['key']}]' id='{$input_array[$i][id]}' class="form-control input-sm">
                                            <option value='' >全部</option>
html;
                    $cors=Subject::getActiveSubjects();
                    foreach($cors as $v)
                    {
                        $select = (''.$v["subject_id"])==$_data_[search][$input_array[$i]['key']]?'selected':'';
                        echo '<option value="'.$v["subject_id"].'" '.$select.'>'.$v["name"].'</option>';
                    }
                    echo <<<html
                                        </select>
                        </div>
                      </div>
                      
html;
                    break;
                case 'hidden':
                    $inputName=empty($input_array[$i]['name'])?"search[".$input_array[$i]['key']."]":$input_array[$i]['name'];
                    $inputValue=empty($input_array[$i]['value'])?$search[$input_array[$i]['key']]:$input_array[$i]['value'];
                    $hiddenInput.='<input type="hidden" name="'.$inputName.'" id="'.$input_array[$i][id].'"  value="'.$inputValue.'"/>';
                    break;
                case 'space':
                    echo <<<html
					<div class="col-sm-4">
					</div>
html;
                    break;
                case 'info':
                    echo <<<html
                    <div class="col-sm-4">
                        <p class="form-control-static">{$input_array[$i][label]}&nbsp;:&emsp;{$input_array[$i][text]}</p>
                    </div>
html;
                    break;
                case 'textarea':
                    echo <<<html
                    <div class="col-sm-12">
                        <p class="form-control-static">{$input_array[$i][label]}&nbsp;:&emsp;{$input_array[$i][text]}</p>
                    </div>
html;
                    break;
                default:
                    break;
            }
            if ($input_array[$i][type]!='hidden'){
                $index++;
            }
            //if (($index%3==0||$i==(count($input_array)-1))&$input_array[$i][type]!='hidden'){
            if ($index%3===0 || $i==(count($input_array)-1)){
                echo <<<html
								</div>
html;
            }

        }
        echo <<<html


				</div>
				&emsp;<input type="submit" value="&nbsp查询&nbsp"   class="btn btn-success btn-sm">
html;

        $buttonArray = $array[buttonArray];
        for ($i=0;$i<count($buttonArray);$i++)
        {
            switch ($buttonArray[$i]["type"])
            {
                case "custom":
                    $content=$buttonArray[$i]["content"];
                    if (is_callable($content))
                    {
                        $content = call_user_func($content, $search, $buttonArray[$i],$this);
                    }
                    echo $content;

                    break;

                default:
                    $btnClass = !empty($buttonArray[$i]['class']) ? $buttonArray[$i]['class'] : 'btn btn-success btn-sm';
                    echo <<<html
						<input type="button" value="&nbsp{$buttonArray[$i]['text']}&nbsp" id='{$buttonArray[$i][buttonId]}'  class="{$btnClass}">
html;
                    break;

            }

        }
        if($isShowExport==1)
        {
            $exportUrl = Utility::addPageParameters("isExport=1");
            echo <<<html
                    <input type='button' value='&nbsp导出&nbsp' onclick="location.href='$exportUrl';" id='exportButton' class='btn btn-primary btn-sm'/>

html;
        }
        if (isset ($col_array))
        {
            $json_str = str_replace( '"',"'",str_replace("'","\'",json_encode($col_array)));
            echo <<<html

                    <input type='button' value='&nbsp导出&nbsp' id='exportButton' class='btn btn-primary btn-sm'/>
                    <input type='hidden' value="$json_str" id='colArray'/>
html;
        }

        if($isShowBack){
            echo <<<html
                    <input type='button' value='&nbsp返回&nbsp' onclick="history.back()" id='exportButton' class='btn btn-default btn-sm'/>
html;
        }

        echo $hiddenInput;
        echo <<<html
			</form>
</div>
</div>
html;
        echo <<<html
<script>
html;
        for ($i=0;$i<count($input_array);$i++){
            if($input_array[$i][type]=='datetime') {
                echo <<<html
$('#{$input_array[$i][id]}').datetimepicker({todayBtn: true,clearBtn:true,pickerPosition: "bottom-right"});

html;
            }
            if ($input_array[$i][type]=='date'){
                $str = !isset($input_array[$i][format])?"yyyy-mm-dd":$input_array[$i][format];
                echo <<<html
$("#{$input_array[$i][id]}").datetimepicker({format: '$str',minView: 'month',todayBtn: true,clearBtn:true,pickerPosition: "bottom-right"});

html;
            }
            if ($input_array[$i][type]=='dateMonth'){
                $str = !isset($input_array[$i][format])?"yyyy-mm":$input_array[$i][format];
                echo <<<html
$("#{$input_array[$i][id]}").datetimepicker({format: '$str',startView: 'year',minView: 'year',forceParse: false});

html;
            }
        }
        if (isset ($col_array))
        {
            $export_url = $_SERVER[REQUEST_URI];
            echo <<<html
$('#exportButton').click(function(){
    var json_str = $('#colArray').val();
    window.location.href = '$export_url' + "&export_str=" + encodeURIComponent(json_str); 
});
html;
        }
        echo <<<html
</script>
html;
    }

    /**
     * @desc 表单tab加载
     * @param $tab tab参数配置
     * @param $search 表单查询参数
     */
    public function loadFormTabsWithNewUI($tab, $search)
    {
        echo <<<html
            <div class="tabs margin-b-30">
				<div class="el-button-group">
html;
        foreach ($this->map[$tab['map_name']] as $key => $val)
        {
            $active = ('' . $key) === $search[$tab['key']] ? ' active' : '';
            echo <<<html
                    <button class="el-button el-button--default{$active}">
                        {$val}<input type="hidden" name="search[{$tab['key']}]" value="{$key}"/>
                    </button>
html;
        }
        echo <<<html
                </div>
            </div>
<script>
$(function(){
	$('.tabs').on('click', function(e) {
      var targetElem = e.target;
      if (targetElem.tagName === 'BUTTON') {
        $('.tabs button').removeClass('active');
        $(targetElem).addClass('active');
        $(this).parent('.search-form').submit();
      }
      })
      });
</script>
html;
    }

    /**
     * @desc 表单控件加载
     * @param $input input项参数配置
     * @param $search 表单查询参数
     * @param $hiddenInput 隐藏域内容
     */
    public function loadFormInputWithNewUI($input, $search, &$hiddenInput)
    {
        switch ($input['type'])
        {
            case 'text':
                $disabled_val = $input['disabled'] ? 'disabled' : '';
                echo <<<html
                    <label class="col field flex-grid align-center">
                        <span class="w-100">{$input['text']}：</span>
                        <input type="text" autocomplete="off" placeholder="{$input['text']}" class="el-input__inner" name="search[{$input['key']}]" id="{$input['id']}" value="{$search[$input['key']]}" $disabled_val/>
                    </label>
html;
                break;
            case 'select':
                echo <<<html
                    <label class="col field flex-grid align-center">
                        <span class="w-100">{$input['text']}：</span>
                        <select class="selectpicker form-control show-menu-arrow" name="search[{$input['key']}]">
html;
                if (empty($input['noAll']))
                {
                    echo "<option value='' >全部</option>";
                }
                foreach ($this->map[$input['map_name']] as $key => $val)
                {
                    $select = ('' . $key) === $search[$input['key']] ? 'selected' : '';
                    echo <<<html
											<option value='$key' $select>$val</option>
html;
                }
                        echo <<<html
                        </select>
                    </label>
html;
                break;
            case 'amount':
                $disabled_val = $input['disabled'] ? 'disabled' : '';
                $keyName = $input['key'];

                if (!strpos($keyName, ">") && !strpos($keyName, "<"))
                {
                    $keyName = $keyName . "/100";
                } else
                {
                    $keyName = str_replace(">", "/100>", $keyName);
                    $keyName = str_replace("<", "/100<", $keyName);
                }
                echo <<<html
                    <label class="col field flex-grid align-center">
                        <span class="w-100">{$input['text']}：</span>
                        <input type="text" autocomplete="off" placeholder="{$input['text']}" class="el-input__inner" name="search[{$keyName}]" id="{$input['id']}" value="{$search[$keyName]}" $disabled_val/>
                    </label>
html;
                break;
            case 'hidden':
                $inputName = empty($input['name']) ? "search[" . $input['key'] . "]" : $input['name'];
                $inputValue = empty($input['value']) ? $search[$input['key']] : $input['value'];
                $hiddenInput .= '<input type="hidden" name="' . $inputName . '" id="' . $input[id] . '"  value="' . $inputValue . '"/>';
                break;
            case 'dateMonth':
                echo <<<html
                    <label class="col field flex-grid align-center">
                        <span class="w-100">{$input['text']}：</span>
                        <input type="text" autocomplete="off" placeholder="{$input['text']}" class="el-input__inner" name="search[{$input['key']}]" id="{$input['id']}" value="{$search[$input['key']]}">
                    </label>
html;
                break;
            case 'date':
                echo <<<html
                    <label class="col field flex-grid align-center">
                        <span class="w-100">{$input['text']}：</span>
                        <input type="text" autocomplete="off" placeholder="{$input['text']}" class="el-input__inner" name="search[{$input['key']}]" id="{$input['id']}" value="{$search[$input['key']]}">
                    </label>
html;
                break;
            case 'datetime':
                echo <<<html
                    <label class="col field flex-grid align-center">
                        <span class="w-100">{$input['text']}：</span>
                        <input type="text" autocomplete="off" placeholder="{$input['text']}" class="el-input__inner form_datetime" name="search[{$input['key']}]" id="{$input['id']}" value="{$search[$input['key']]}">
                    </label>
html;
                break;
            case 'managerUser':
                echo <<<html
                    <label class="col field flex-grid align-center">
                        <span class="w-100">{$input['text']}：</span>
                        <select class="selectpicker show-menu-arrow form-control" data-live-search="true" name='search[{$input['key']}]'>
                            <option value='' >所有人员</option>
html;
                $users = UserService::getProjectManageUsers();
                foreach ($users as $v)
                {
                    $select = ('' . $v["user_id"]) == $search[$input['key']] ? 'selected' : '';
                    echo '<option value="' . $v["user_id"] . '" ' . $select . '>' . $v["name"] . '</option>';
                }
                echo <<<html
                        </select>
                    </label>
html;
                break;
            case 'corpName':
                echo <<<html
                    <label class="col field flex-grid align-center">
                        <span class="w-100">{$input['text']}：</span>
                        <select class="selectpicker show-menu-arrow form-control" data-live-search="true" name='search[{$input['key']}]'>
                            <option value='' >全部交易主体</option>
html;
                $cors = Corporation::getActiveCorporations();
                foreach ($cors as $v)
                {
                    $select = ('' . $v["corporation_id"]) == $search[$input['key']] ? 'selected' : '';
                    echo '<option value="' . $v["corporation_id"] . '" ' . $select . '>' . $v["name"] . '</option>';
                }
                echo <<<html
                        </select>
                    </label>
html;
                break;
            case 'info':
                echo <<<html
                    <label class="col field flex-grid align-center">
                        <span class="w-100">{$input['label']}：</span>
                        <span class="form-control-static ellipsis">{$input['text']}</span>
                    </label>
html;
            case 'custom':
                echo <<<html
                    <label class="col field flex-grid align-center">
                        <span class="w-100">{$input['text']}：</span>
html;
                $content = $input["content"];
                if (is_callable($content))
                {
                    $content = call_user_func($content, $search, $input, $this);
                }
                echo $content;
                echo <<<html
                    </label>
html;

                break;
            default:
                break;
        }
    }

    /**
     * @desc 新ui表单加载
     * @param array $searchConfig [  #表单配置项，根据配置项生成表单
     *      'title' => '测试',                #表单名，可不传，不传默认为moduleName
     *      'form_url' => 'project',         #表单提交处理地址，必传
     *      'method' => 'get'                #表单提交方式，可不传，不传默认为get
     *      'items' => [               #表单项
     *          array('type' => 'text', 'key' => 'id', 'text' => 'id'),     #type:表单类型   key:会被拼装成name="search[key]"的name属性  text:页面展示label
     *      ],
     *      'tabs' => [                 #tab项
     *          'key' => 'type', 'map_name' => 'type_list'
     *      ]
     * ]
     * @param array $searchFields [
     * ]
     * @param int $searchLines 查询列行数，默认为一行
     * @param bool $isShowResetButton 是否显示重置按钮
     */
    public function loadSearchFormWithNewUI($searchConfig, $searchFields = null, $searchLines = 1, $isShowResetButton = true)
    {
        if (empty($searchFields))
        {
            $searchFields = $this->getSearch();
        }
        $method = empty($searchConfig["method"]) ? "get" : $searchConfig["method"];

        $form_url = $searchConfig["form_url"];
        echo <<<html
        <form method={$method} action={$form_url} class="search-form">
	        <div class="condition-fields">
html;
        $searchItems = [];
        $hiddens = [];
        $infos = [];
        if (Utility::isNotEmpty($searchConfig['items'])) {
            foreach ($searchConfig['items'] as $searchItem) {
                if ($searchConfig['items']['type'] == 'hidden') {
                    array_push($hiddens, $searchItem);
                } elseif ($searchConfig['items']['type'] == 'info') {
                    array_push($infos, $searchItem);
                } else {
                    array_push($searchItems, $searchItem);
                }
            }
        }
        $index = 0;
        $hiddenInput = "";
        $items = $searchItems;
        $first_line_array = array_splice($searchItems, 0, 2);
        if (Utility::isNotEmpty($first_line_array))
        {
            echo <<<html
               <div class="flex-grid form-group align-center">
html;
            foreach ($first_line_array as $input)
            {
                $this->loadFormInputWithNewUI($input, $searchFields, $hiddenInput);
            }

            echo <<<html
                <div class="col flex-grid align-center">
                        <button class="oil-btn" style="width: 110px" type="submit"><span>查询</span></button>
html;
            if ($isShowResetButton) {
                echo <<<html
                        <a href="javascript: void 0" style="margin-left: 10px; width: 110px" class="o-btn o-btn-default" onclick="page.doReset()"><span>重置</span></a>
html;
            }

            if (ceil(count($searchItems) / 3) != $searchLines - 1) {
                echo <<<html
                        <a href="javascript: void 0" id="toggle-fields" onclick="page.toggleFields()"><span class="toggle-text">收起搜索</span> <img src="/img/arrow-top-o.png" style="width: 10px;vertical-align: middle;"></a>
html;
            }
            echo <<<html
                     </div>
                </div> 
html;
        }

        if (Utility::isNotEmpty($searchItems))
        {
            for ($i = 0; $i < count($searchItems); $i ++)
            {
                $currLine = ceil(($index+1) / 3);
                if ($index % 3 == 0)
                {
                    $isHidden = '';
                    if ($searchLines <= 1)
                    {
                        $isHidden = ' is-hidden';
                    } else {
                        if ($currLine > $searchLines - 1) {
                            $isHidden = ' is-hidden';
                        }
                    }
                    echo <<<html
								<div class="flex-grid form-group{$isHidden}">
html;
                }
                $this->loadFormInputWithNewUI($searchItems[$i], $searchFields, $hiddenInput);

                $index ++;
                if ($index % 3 === 0 || $i == (count($searchItems) - 1))
                {
                    echo <<<html
				    </div>
html;
                }
            }
        }

        if (Utility::isNotEmpty($hiddens)) {
            foreach ($hiddens as $hidden) {
                $this->loadFormInputWithNewUI($hidden, $searchFields, $hiddenInput);
            }
        }
        echo <<<html
        </div>
html;

        $tabs = $searchConfig['tabs'];
        if (Utility::isNotEmpty($tabs)) {
           $this->loadFormTabsWithNewUI($tabs, $searchFields);
        }
        echo $hiddenInput;
        echo <<<html
    </form>
html;
        echo <<<html
<script>
    $(function(){
    	if (!page.checkFieldHasValue('.condition-fields')) {
        // 搜索区域没有值， 隐藏指定的隐藏区域
        page.toggleFields();
        }
        
html;
        $searchItems = $items;
        for ($i = 0; $i < count($searchItems); $i ++)
        {
            if ($searchItems[$i]['type'] == 'datetime')
            {
                echo <<<html
$('#{$searchItems[$i]['id']}').datetimepicker({todayBtn: true,clearBtn:true,pickerPosition: "bottom-right"});

html;
            }
            if ($searchItems[$i]['type'] == 'date')
            {
                $str = !isset($searchItems[$i]['format']) ? "yyyy-mm-dd" : $searchItems[$i]['format'];
                echo <<<html
$("#{$searchItems[$i]['id']}").datetimepicker({format: '$str',minView: 'month',todayBtn: true,clearBtn:true,pickerPosition: "bottom-right"});

html;
            }
            if ($searchItems[$i]['type'] == 'dateMonth')
            {
                $str = !isset($searchItems[$i]['format']) ? "yyyy-mm" : $searchItems[$i]['format'];
                echo <<<html
$("#{$searchItems[$i]['id']}").datetimepicker({format: '$str',startView: 'year',minView: 'year',forceParse: false});

html;
            }
        }
        echo <<<html
     })
</script>
html;
    }

    /**
     * @desc 加载列表页面头部信息
     * @param array $menuConfig [            #菜单配置项
     *      ['text'=>'一级菜单', 'link' => '/project']
     *      ['text'=>'二级菜单', 'link' => '/project/detail']
     * ]
     * @param array $buttonConfig [          #按钮配置项
     *      ['text'=>'添加',
     *       'attr' => [                    #button标签属性数组，可添加button标签所有的html属性
     *              'id' => 'button_1',
     *              'class_abbr' => 'major-primary'   #系统UI规范class名，具体见getButtonClass()里$abbr可选值
     *              'class' => '',  //自定义class
     *              'onclick' => '',
     *              'data-bind' => '',
     *              ......
     *        ]
         * ]
         * @param bool|string $isShowBackBread  #是否显示返回面包屑按钮,false:不显示  true:显示，返回到上一页  string:返回到string指定地址
         * @param bool $isShowExport            #是否显示导出按钮，如果导出，后端必须提供export方法
         * @param string $exportAction          #显示导出按钮时，导出的action function name
         */
        public function loadHeaderWithNewUI($menuConfig, $buttonConfig, $isShowBackBread = false, $isShowExport = false, $exportAction='export')
        {
            if(!is_array($buttonConfig))
                $buttonConfig=[];
            if($isShowExport)
                $buttonConfig[]=$this->getExportButtonConfig($exportAction);
        echo <<<html
        <section class="content-header menu-path is-fixed-bread align-center">
        <div class="col flex-grid">
html;
        //新页面打开时，不展示返回按钮，返回按钮默认返回上一页，提供返回地址时，返回指定返回地址
        if ($isShowBackBread && !(array_key_exists('t', $_GET) && $_GET['t'] == 1))
        {
            if ($isShowBackBread === true) {
                $click = 'onclick="history.back()"';
            } elseif (is_string($isShowBackBread)) {
                $click='onclick="location.href=\''.$isShowBackBread.'\'"';
            }

            echo <<<html
            <a href="#" {$click} class="menu-path_back">
                返回
            </a>
html;
        }
            echo <<<html
            <ul class="flex-grid">
html;
            $menuConfig = Utility::isNotEmpty($menuConfig) ? $menuConfig : $this->getIndexMenuWithNewUI();
            $indexMenu = $this->getIndexMenuConfig();
            array_unshift($menuConfig, $indexMenu);
            if (Utility::isNotEmpty($menuConfig)) {
                foreach ($menuConfig as $key => $menu) {
                    $active = $key == count($menuConfig) - 1 ? 'active' : '';
                    echo <<<html
                    <li class="{$active}">
html;
                    if (!empty($menu['link'])) {
                        echo <<<html
                        <a href="{$menu['link']}" style="color: inherit">{$menu['text']}</a>
html;
                    } else {
                        echo <<<html
                        {$menu['text']}
html;
                    }
                    echo <<<html
                    
                    </li>
html;
                }
            }
            echo <<<html
            </ul>
        </div>
        <div class="flex-grid col menu-path_btn-group">
html;
            if (Utility::isNotEmpty($buttonConfig)) {
                foreach ($buttonConfig as $btn) {
                    $btnType = '';
                    if (!empty($btn['type'])) {
                        $btnType = $btn['type'];
                    }
                    switch ($btnType) {
                        case 'custom':
                            $content = $btn["content"];
                            if (is_callable($content))
                            {
                                $content = call_user_func($content, $this, $btn);
                            }
                            echo $content;
                            break;
                        default:
                            $attrArr = $btn['attr'];
                            $attr = '';
                            if (Utility::isEmpty($attrArr) || (!array_key_exists('class', $attrArr) && !array_key_exists('class_abbr', $attrArr))) {
                                $attrArr['class'] = $this->getButtonClass();
                            } else {
                                if (array_key_exists('class_abbr', $attrArr)) {
                                    $attrArr['class'] .= ' ' .$this->getButtonClass($attrArr['class_abbr']);
                                    unset($attrArr['class_abbr']);
                                }
                            }

                            foreach ($attrArr as $attrName=>$attrVal) {
                                /*if ($attrName == 'class' && !strstr($attrVal, 'o-btn')) {
                                    $attrVal .= ' o-btn';
                                }*/
                                $attr .= $attrName . '="' . $attrVal .'" ';
                            }

                            echo <<<html
                    <a href="javascript: void 0" {$attr}><span>{$btn['text']}</span></a>
html;
                            break;
                    }
                }
            }
        echo <<<html
        </div>
    </section>
html;
    }

    /**
     * @desc 根据缩写获取button class样式
     * @param string $abbr
     * @return string
     */
    public function getButtonClass($abbr = '')
    {
        switch ($abbr) {
            case 'major-primary':
                $class = 'o-btn o-btn-primary';
                break;
            case 'action-default':
                $class = 'o-btn o-btn-action';
                break;
            case 'action-primary':
                $class = 'o-btn o-btn-action primary';
                break;
            case 'action-default-base':
                $class = 'o-btn o-btn-action w-base';
                break;
            case 'action-primary-base':
                $class = 'o-btn o-btn-action primary w-base';
                break;
            default:
                $class = 'o-btn o-btn-primary';
                break;
        }

        return $class;
    }

    /**
     * [getExportButtonConfig 获取导出按钮]
     * @param
     * @param  [string] $exportAction [导出方法的函数名]
     * @return array
     */
    public function getExportButtonConfig($exportAction='export')
    {
        $url=$this->getId();
        return ['text'=>'导出', 'attr' => ['class_abbr' => 'action-default-base', 'id'=>'exportBtn', 'onclick' => 'page.export(\''.$url.'\',\''.$exportAction.'\')']];
    }

    /**
     * 获取首页面包屑配置
     * @return array
     */
    public function getIndexMenuConfig()
    {
        return ['text'=>'首页', 'link' => '/'];
    }

    /**
     * @desc 获取列表页菜单，该方法按模块层级关系返回菜单数组
     * @return array
     */
    public function getIndexMenuWithNewUI()
    {
        $menuConfig = array();
        $parentModules = array_filter(explode(',', $this->moduleParentIds));
        if (Utility::isNotEmpty($parentModules)) {
            foreach ($parentModules as $moduleId) {
                $module = SystemModule::model()->findByPk($moduleId);
                if(!empty($module)) {
                    $menu['text'] = $module->name;
                    $menu['link'] = $module->page_url;
                    array_push($menuConfig, $menu);
                }
            }
        }
        $currMenu['text'] = $this->moduleName;
        $currMenu['link'] = $this->moduleUrl;
        array_push($menuConfig, $currMenu);
        return $menuConfig;
    }
    //显示JSON格式的错误信息
    public function returnError($msg,$code=1)
    {
        echo json_encode(array("state"=>$code,"data"=>$msg));
        Mod::app()->end();
        //exit;
    }

    /**
     * 返回操作成功的JSON数据
     * @param string $msg
     * @param null $extra 额外返回的数据
     */
    public function returnSuccess($msg="操作成功！",$extra=null)
    {
        $data=array("state"=>0,"data"=>$msg);
        if(!empty($extra))
            $data["extra"]=$extra;
        echo json_encode($data);
        Mod::app()->end();
    }
    
    /**
     * api：返回成功状态【JSON数据】
     * @param string $msg
     * @param null $extra 额外返回的数据
     */
    public function returnJson($data=array(),$extra=null)
    {
        header("Content-Type: application/json;");
        $data=array("state"=>0,"data"=>$data);
        if(!empty($extra))
        $data["extra"]=$extra;
        echo json_encode($data,JSON_PRETTY_PRINT);
        Mod::app()->end();
    }
    /**
     * api:返回错误状态【JSON数据】
     * @param string $msg
     * @param null $extra 额外返回的数据
     */
    /*public function returnJsonError($msg='操作失败',$extra=null)
    {
        header("Content-Type: application/json;");
        $data=array("code"=>1,"msg"=>$msg);
        if(!empty($extra))
        $data["extra"]=$extra;
        echo json_encode($data,JSON_PRETTY_PRINT);
        Mod::app()->end();
    }*/

    /**
     * @param $modelErrors
     * @return string
     */
    public function formatModelErrors(array $modelErrors = []){
        $msg = [];
        if(is_array($modelErrors)){
            foreach($modelErrors as $errors){
                foreach($errors as $error){
                    $msg[] = $error;
                }
            }
        }

        return implode(";",$msg);
    }

    public function returnJsonError($message = "", $code = 1)
    {
        header("Content-Type: application/json;");
        $data=array("state"=>$code,"data"=>$message);
        if(is_array($message))
        {
            $s=$message[1];
            $c=$message[0];
            if(is_array($code))
            {  
                $s=$this->formatMessage($s,$code);
            }
            
            $data['data']=$s;
            $data['state']=$c;
        }
       
        echo json_encode($data,JSON_PRETTY_PRINT);
        Mod::app()->end();
        
    }

    /**
     * @desc 返回业务错误
     * @param $businessError
     * @param $params
     */
    public function returnJsonBusinessError($businessError, $params=array())
    {
        $this->returnJsonError(\ddd\infrastructure\error\ExceptionService::getBusinessExceptionMessage($businessError, $params));
    }

    public function formatMessage($message,$params=null)
    {           
        if(is_array($params))
        {
            $customParams=array();
            foreach ($params as $k=>$v)
            {
                $customParams["\${".$k."}"]=$v;
            }
            $message=strtr($message,$customParams);
        }

        return $message;
    }

    /**
     * api:返回验证错误【JSON数据】
     * @param string $msg
     * @param null $extra 额外返回的数据
     */
    public function returnValidateError($msgArr=array(),$code="code")
    {
        header("Content-Type: application/json;");
        $data=array($code=>1,"msg"=>'');
        $i=0;
        if(!empty($msgArr)){
            foreach ($msgArr as $key=>$value){
                if($i==0) 
                    $data['msg']=$value[0];
                $i++;
            }
        }
        echo json_encode($data,JSON_PRETTY_PRINT);
        Mod::app()->end();
    }

    /**
     * api:返回验证错误【JSON数据】 统一API返回的数据格式
     * @param string $msg
     * @param null $extra 额外返回的数据
     */
    public function returnApiValidateError($msgArr=array(),$code="code")
    {
        header("Content-Type: application/json;");
        $data=array($code=>1,"msg"=>'');
        $i=0;
        if(!empty($msgArr)){
            foreach ($msgArr as $key=>$value){
                if($i==0)
                    $data['data']=$value[0];
                $i++;
            }
        }
        echo json_encode($data,JSON_PRETTY_PRINT);
        Mod::app()->end();
    }


    /**
     * 返回对外接口成功的JSON数据
     * @param string $msg
     */
    public function returnOutSuccess($msg="操作成功！")
    {
        echo json_encode(array("code"=>0,"data"=>$msg));
        Mod::app()->end();
    }

    /**
     * 显示对外接口JSON格式的错误信息
     * @param $msg
     */
    public function returnOutError($msg)
    {
        echo json_encode(array("code"=>1,"data"=>$msg));
        Mod::app()->end();
        //exit;
    }


    /**
     * 结束当前请求，并返回操作成功的json格式
     * @param string $msg
     */
    public function endRequestSuccess($msg="操作成功！")
    {
        echo json_encode(array("state"=>0,"data"=>$msg));
        fastcgi_finish_request();
    }

    /**
     * 结束当前请求，并返回操作错误的json格式
     * @param string $msg
     */
    public function endRequestError($msg="")
    {
        echo json_encode(array("state"=>1,"data"=>$msg));
        fastcgi_finish_request();
    }



    ///导出Excel，自动分解数据到多Sheet页，每个sheet最多60000行数据，这是Excel本身的限制，每一个最大65000多。
    ///第三个参数为字符串行的列名
    ///第四个参数是文件名称
    //第五个参数是文件后缀，默认是xls表格
    public function exportExcel($data,$title="",$stringColumns=null,$fileName="",$fileType="xls")
    {
        $objectPHPExcel = new PHPExcel();
        $objectPHPExcel->setActiveSheetIndex(0);

        if($fileName=="")
            $fileName=$title;

        if(is_array($data) && count($data)>0)
        {
            $rowCount=count($data);
            $row=1;
            //每一个sheet最大行数
            $maxRowCount=60000;

            $item=$data[0];
            $colCount=count($item);

            //Sheet页的最大索引
            $sheetIndex=0;

            $j=$rowCount;
            $j=$j-$maxRowCount;
            while($j>0)
            {
                $objectPHPExcel->createSheet();
                $sheetIndex++;
                $j=$j-$maxRowCount;
            }

            if(isset($title) && $title!="")
            {
                //设置每一个Sheet页的标题
                for ($i = 0; $i <= $sheetIndex; $i++) {
                    $objectPHPExcel->setActiveSheetIndex($i);
                    $activeSheet = $objectPHPExcel->getActiveSheet();
                    $activeSheet->mergeCellsByColumnAndRow(0,$row,$colCount-1,$row);
                    $activeSheet->setCellValueByColumnAndRow(0,$row,$title);
                    $activeSheet->getStyle('A'.$row)->getFont()->setSize(24);
                    $activeSheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }
                $row=2;
            }

            for ($i = 0; $i <= $sheetIndex; $i++) {
                //设置表头
                $colIndex=0;
                $objectPHPExcel->setActiveSheetIndex($i);
                $activeSheet = $objectPHPExcel->getActiveSheet();
                foreach($item as $k=>$v)
                {
                    $activeSheet ->setCellValueByColumnAndRow($colIndex,$row,$k);
                    $activeSheet ->getStyleByColumnAndRow($colIndex,$row)->getFont()->setBold( true);
                    $colIndex++;
                }
            }

            $row++;
            //设置每一个Sheet的数据起始行
            $startRow=$row;
            $i=0;
            $j=0;
            foreach($data as $e) {
                $colIndex=0;
                foreach($e as $k=>$v)
                {
                    if(isset($stringColumns) || is_array($stringColumns))
                    {
                        if(in_array($k,$stringColumns))
                            $objectPHPExcel->setActiveSheetIndex($i)->setCellValueExplicitByColumnAndRow($colIndex,$row,$v,PHPExcel_Cell_DataType::TYPE_STRING);
                        else
                            $objectPHPExcel->setActiveSheetIndex($i)->setCellValueByColumnAndRow($colIndex,$row,$v);
                    }
                    else
                        $objectPHPExcel->setActiveSheetIndex($i)->setCellValueByColumnAndRow($colIndex,$row,$v);

                    $colIndex++;
                }
                $j++;
                if($j>=$maxRowCount)
                {
                    $j=0;
                    $row=$startRow;
                    $i++;
                }
                else
                    $row++;
            }

            $objectPHPExcel->setActiveSheetIndex(0);
        }
        else
        {
            //报表头的输出
            $objectPHPExcel->getActiveSheet()->mergeCells('A1:G1');
            $objectPHPExcel->getActiveSheet()->setCellValue('A1',$title);
            $objectPHPExcel->getActiveSheet()->mergeCells('A2:G2');
            $objectPHPExcel->getActiveSheet()->setCellValue('A2',"暂无数据");
        }


        ob_end_clean();
        ob_start();

        header('Content-Type : application/vnd.ms-excel');
        header('Content-Disposition:attachment;filename="'.$fileName.date("Y年m月j日").'.'.$fileType.'"');
        $objWriter= PHPExcel_IOFactory::createWriter($objectPHPExcel,'Excel5');
        $objWriter->save('php://output');
    }

    //读取Excel文件内容
    public function readExcel($fileName,$sheetIndex=0)
    {
        //var_dump($fileName);
        //$fileName=str_replace("/","\\",$fileName);
        $PHPExcel = new PHPExcel();
        /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
        $PHPReader = new PHPExcel_Reader_Excel2007();

        if(!$PHPReader->canRead($fileName)){
            $PHPReader = new PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($fileName)){
                echo 'no Excel';
                return ;
            }
        }

        $PHPExcel=$PHPReader->load($fileName);
        $content = $PHPExcel->getSheet($sheetIndex)->toArray();
        $data=array();
        if(count($content)>0)
        {
            $data=array();
            $header=$content[0];
            unset($content[0]);
            foreach($content as $v)
            {
                $item=array();
                foreach($v as $ek=>$ev)
                {
                    $item[$header[$ek]]=$ev;
                }
                $data[]=$item;
            }
        }

        return $data;

    }

    //显现错误信息页面
    public function renderError($msg="",$backUrl="",$ignoreAjax=false)
    {
        if(!$ignoreAjax && Mod::app()->request->isAjaxRequest)
        {
            // ajax 请求的处理方式
            $this->returnError($msg);
        }
        else {
            if(empty($backUrl))
                $backUrl="/".$this->getId()."/";
            $this->render("/layouts/error", array("msg" => $msg, "backUrl" => $backUrl));
            Mod::app()->end();
        };

    }

    /**
     * 生成目录结构
     * @param $tree
     */
    public function generateMenu($tree)
    {
        if(!is_array($tree))
            return;
        $i = 0;
        foreach($tree as $node)
        {
            $className="";
            $icon="<i class=\"fa fa-circle-thin\"></i>";
            if(strtolower($this->treeCode)==strtolower($node["code"]))
            {
                $className="active";
                $icon="<i class=\"fa fa-arrow-circle-right\"></i>";
            }

            if(Utility::isEmpty($node["children"]))
            {
                if(!empty($node["page_url"])) {
                    ?>
                    <li id="menu_item_<?php echo $node["id"] ?>" class="<?php echo $className ?>">
                        <a href="<?php echo $node["page_url"] ?>">
                            <?php echo empty($node["icon"])?$icon:$node["icon"] ?>
                            <span><?php echo $node["name"] ?></span>
                        </a>
                    </li>
                    <?php
                }
            }
            else
            {
                if($node["parent_id"]==0)
                    $icon="<i class=\"fa fa-th\"></i>" ;
                else
                    $icon="<i class=\"fa fa-th-list\"></i>";
                ?>
                <li id="menu_item_<?php echo $node["id"] ?>"  class="treeview <?php echo $className ?>">
                    <a href="javascript:void 0">
                        <?php echo empty($node["icon"])?$icon:$node["icon"] ?>
                        <span><?php echo $node["name"] ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php
                        $this->generateMenu($node["children"]);
                        ?>
                    </ul>
                </li>
                <?php
            }
        }
    }

    /**
     * 生成目录结构
     * @param $tree
     */
    public function generateMenuWithNewUI($tree)
    {
        if(!is_array($tree))
            return;
        $i = 0;
        foreach($tree as $node)
        {
            $className="";
            // $icon="<i class=\"fa fa-circle-thin\"></i>";
            if(strtolower($this->treeCode)==strtolower($node["code"]))
            {
//                $className="menu-slt";
                $className="active";
                // $icon="<i class=\"fa fa-arrow-circle-right\"></i>";
            }

            if(Utility::isEmpty($node["children"]))
            {
                if(!empty($node["page_url"])) {
                    ?>
                    <li id="menu_item_<?php echo $node["id"] ?>" class="<?php echo $className ?>">
                        <a href="<?php echo $node["page_url"] ?>">
                            <span><?php echo $node["name"] ?></span>
                        </a>
                    </li>
                    <?php
                }
            }
            else
            {
                if($node["parent_id"]==0)
                    $icon="<i class=\"fa fa-th\"></i>" ;
                else {
                    // $icon="<i class=\"fa fa-arrow-circle-right\"></i>";
                }
                ?>
                <li id="menu_item_<?php echo $node["id"] ?>"  class="treeview <?php echo $className ?>">
                    <a href="javascript:void 0">
                        <?php echo empty($node["icon"])?$icon:$node["icon"] ?>
                        <span><?php echo $node["name"] ?></span>
                        <i class="icon icon-shouqizhankai pull-right" style="margin-right: 20px;"></i>
                    </a>
                    <ul class="treeview-menu">
                        <?php
                        $this->generateMenuWithNewUI($node["children"]);
                        ?>
                    </ul>
                </li>
                <?php
            }
        }
    }

    public function generateTasks()
    {
        $data=TaskService::getUserActions(Utility::getNowUserId());
        ?>
        <li id="menu_item_tasks"  class="treeview active">
                    <a href="#">
                        <i class="fa fa-tasks"></i>
                        <span>我的待办</span>
                <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu menu-open">
        <?php
        if(count($data)<1)
        {
            ?>
            <li  class="active">
                <a>
                    <i class="fa fa-fw fa-check-square-o"></i>
                    <span>暂无待办</span>
                </a>
            </li>
            <?php
        }
        else{
            foreach($data as $v)
            {
                $icon=" fa-flag";
                ?>
                <li id="menu_item_action_<?php echo $v["action_id"] ?>">
                    <a href="<?php echo $v["list_url"] ?>">
                        <i class="fa <?php echo $icon ?>"></i>
                        <span><?php echo $v["action_name"] ?></span>
                        <span class="label bg-red pull-right"><?php echo $v["n"] ?></span>
                    </a>
                </li>
                <?php
            }
        }
    ?>
        </ul>
        </li>
        <?php
    }

    /**
     * 获取用户的待办动作
     * @return array|null
     */
    public function getUserActionModels()
    {
        if(empty($this->userActions))
            $this->userActions=TaskService::getUserActionModels();
        return $this->userActions;
    }

    /**
     * 获取当前用户的待办
     * @return array|null
     */
    public function getUserTasks()
    {
        if(empty($this->userTasks))
            $this->userTasks=TaskService::getUserAllTasks();
        return $this->userTasks;
    }

    public function generateTasksNew()
    {
        $this->getUserActionModels();
        ?>
        <li id="menu_item_tasks"  class="treeview active">
            <a href="#">
                <i class="fa fa-tasks"></i>
                <span>我的待办</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu menu-open">
                <?php
                $i=0;
                foreach ($this->userActions as $model)
                {
                    if(!empty($model->tasks))
                    {
                        $i++;
                        $icon="<i class=\"fa fa-fw fa-tasks\"></i>";
                        if(!empty($model["icon"]))
                            $icon=$model["icon"];
                        ?>
                        <li id="menu_item_action_<?php echo $model["action_id"] ?>">
                            <a href="#">
                                <?php echo $icon ?>
                                <span><?php echo $model->action_name ?></span>
                                <span class="label bg-red pull-right"><?php echo count($model->tasks) ?></span>
                            </a>
                            <ul class="treeview-menu">
                                <?php
                                foreach ($model->tasks as $v)
                                {
                                    $url=$v["action_url"];
                                    if($v["user_id"]==0)
                                        $url="/site/task?id=".$v["task_id"];
                                    $title=$v["title"];
                                    if(empty($title))
                                        $title=$model["action_name"]." ".$v["key_value"];
                                    ?>
                                    <li id="menu_item_action_<?php echo $model["action_id"]."_".$v["task_id"] ?>">
                                        <a href="<?php echo $url ?>">

                                            <i class="fa fa-fw fa-flag"></i>
                                            <span><?php echo $title ?></span>

                                        </a>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </li>
                        <?php
                    }

                }

                if($i<1)
                {
                    ?>
                    <li  class="active">
                        <a>
                            <i class="fa fa-fw fa-check-square-o"></i>
                            <span>暂无待办</span>
                        </a>
                    </li>
                    <?php
                }

                ?>
            </ul>
        </li>
        <?php
    }


    public function showAttachmentsEdit($data,$mapKey,$attachments,$idFieldName="id",$controllerName="")
    {
        include "common/attachmentsEdit.php";
    }

    public function showAttachments($mapKey,$attachments,$idFieldName="id",$controllerName="",$data="")
    {
        include "common/attachments.php";
    }

    /**
     * 显示文件上传模块
     * @param $baseId
     * @param $data
     * @param $mapKey
     * @param $attachments
     * @param string $idFieldName
     * @param string $controller
     */
    public function showAttachmentsEditMulti($baseId,$data,$mapKey,$attachments,$idFieldName="id",$controller="")
    {
        include ROOT_DIR.DIRECTORY_SEPARATOR."protected/views/layouts/attachmentsEditMulti.php";
    }

    /**
     * 显示文件上传模块
     * @param $baseId
     * @param $data
     * @param $mapKey
     * @param $attachments
     * @param string $idFieldName
     * @param string $controller
     */
    public function showAttachmentsEditMultiNew($baseId,$data,$mapKey,$attachments,$idFieldName="id",$controller="")
    {
        include ROOT_DIR.DIRECTORY_SEPARATOR."protected/views/layouts/new_attachmentsEditMulti.php";
    }

    public function renderNewWeb()
    {
        $url        = Mod::app()->params["oil_web_url"];
        $content    = file_get_contents($url);
        $this->layout = "empty";
        $this->render('/layouts/web', array('content'=>$content));
    }
    //正则匹配  htpps:/  替换为 https://
    protected function matchUrlReplace($url){
        return  preg_replace("/:\//","://",$url);
    }

    /**
     * 是否是vueJs的请求
     * @return bool
     */
    public function getIsVueJsRequest(){
        return isset($_SERVER['HTTP_X_REQUEST_FROM']) && $_SERVER['HTTP_X_REQUEST_FROM']==='Vue';
    }

    /**
     * 获取REST规范提交的数据
     * @return mixed
     */
    public function getRestParams(){
       return Mod::app()->request->getRestParams();
    }

    /**
     * 获取REST规范提交的数据
     * @param $name
     * @param null $defaultValue
     * @return mixed|null
     */
    public function getRestParam($name, $defaultValue=null){
        $data = $this->getRestParams();

        return is_array($data) && isset($data[$name]) ? $data[$name] : $defaultValue;
    }
}

