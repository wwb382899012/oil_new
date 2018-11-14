<?php
//开始分页逻辑
$curPage = array_key_exists('page',$_GET)?$_GET[page]:1;

$totalPage =(is_array($data)&&array_key_exists('pageCount',$data))?$data['pageCount']:-1;
if($totalPage>-1)
    $nextPage = ($curPage==$totalPage)?$totalPage:($curPage+1);
else
    $nextPage = $curPage+1;
if($totalPage!=0) {
    if (substr_count($_SERVER["REQUEST_URI"], '&page=') > 1) {
        Mod::Log(sprintf("[E] [%s] %s | [E] %s\n", date("m/d H:i:s"), 'BaseController->load_page', $_SERVER["REQUEST_URI"]));
        exit();
    }

    $pageUrl=$_SERVER['REQUEST_URI'];
    $pageUrl = substr($pageUrl, 0, strpos($pageUrl, "?"));
    $queryString=$_SERVER['QUERY_STRING'];
    if(!empty($queryString))
    {
        $patterns = "/&*page=(\d)+/";
        $queryString = preg_replace($patterns, "", $queryString);
        if(!empty($queryString))
        {
            $queryString.="&";
        }
    }
    $pageUrl=$pageUrl."?".$queryString;
    echo <<<html
                <div class="col-lg-12">
                    <div class="col-xs-12 text-center">
                        <a href="'.$pageUrl.'page=$nextPage"  class="next-page">下一页</a>
                    </div>
                </div>
html;
}
?>
