<div class="page-nav">
    <?php if($data["total"]>-1){ ?>
        <div class="page-info pull-left" style="padding:25px 20px 10px 5px;">
            <span class="text-info">共<?php echo $data[total] ?>条 &emsp;<?php echo $data[pageCount] ?>页</span>
        </div>
    <?php } ?>
    <div class="page-nav-main  pull-left">
<ul class="pagination pagination-sm pull-right">
<?php
//开始分页逻辑
$curPage = array_key_exists('page',$_GET)?$_GET[page]:1;
$totalPage = empty($data["pageCount"])?0:$data["pageCount"];
$begin = $curPage-3>1?$curPage-3:1;
if($totalPage>=0)
    $end = $curPage+3>$totalPage?$totalPage:$curPage+3;
else
    $end = $curPage+3;
$last=$totalPage>0?$totalPage:$end+1;
$next=$curPage+1;
if($totalPage>0)
    $next=$next>$totalPage?$totalPage:$next;
$prev=$curPage-1;
$prev=$prev<1?1:$prev;

if (substr_count($_SERVER["REQUEST_URI"],'&page=')>1){
     Mod::Log(sprintf("[E] [%s] %s | [E] %s\n", date("m/d H:i:s"),'BaseController->load_page',$_SERVER["REQUEST_URI"]));
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


echo "<li class='active1'><a href='".$pageUrl."page=1' title='First Page'><span class='glyphicon glyphicon-fast-backward'></span></a></li>";
echo "<li class='active1'><a href='".$pageUrl."page=".$prev."' title='Previous Page'><span class='glyphicon glyphicon-backward'></span></a></li>";
for($i=$begin;$i<=$end;$i++){
        if ($i==$curPage){
                echo "<li class='active'><a href='".$pageUrl."page=$i'>$i</a></li>";
        }else{
                echo "<li><a href='".$pageUrl."page=$i'>$i</a></li>";
        }
}
echo "
               <li class='active1'><a href='".$pageUrl."page=".$next."' title='Next Page'><span class='glyphicon glyphicon-forward'></span></a></li>
               <li class='active1'><a href='".$pageUrl."page=$last' title='Last Page'><span class='glyphicon glyphicon-fast-forward'></span></a></li>
";

?>
</ul>
</div>
</div>