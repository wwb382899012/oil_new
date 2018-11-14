<?php
/**
 * Created by youyi000.
 * DateTime: 2017/12/21 14:45
 * Describe：
 */

class AutoController extends Controller
{
    public function pageInit()
    {
        parent::pageInit();
        $this->rightCode = '';
    }

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionCreate()
    {

        $params=$_POST["data"];


        $controllerName=$params["controller"];

        $controllerFile=ROOT_DIR.DIRECTORY_SEPARATOR."protected".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."".$controllerName."Controller.php";
        if(file_exists($controllerFile))
        {
            $this->returnError("当前Controller已经存在");
        }

        $sql=$params["sql"];
        $sql=str_replace("{where}",'".$this->getWhereSql($search)."',$sql);
        $title="";
        $searchItems=$params["search"];
        $gridColumns=$params["columns"];

        //view
        $directory=ROOT_DIR.DIRECTORY_SEPARATOR."protected".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."auto".DIRECTORY_SEPARATOR."";
        $file=$directory."template_view.txt";
        $content=file_get_contents($file);
        $content=str_replace("##datetime##",date("Y-m-d H:i:s"),$content);
        $content=str_replace("##searchItems##",$searchItems,$content);
        $content=str_replace("##gridColumns##",$gridColumns,$content);
        $newFile=$directory.$controllerName."_index.php";
        file_put_contents($newFile,$content);

        //controller
        $file=$directory."template_controller.txt";
        $content=file_get_contents($file);
        $content=str_replace("##datetime##",date("Y-m-d H:i:s"),$content);
        $content=str_replace("##sql##",$sql,$content);
        $content=str_replace("##controller##",$controllerName,$content);
        $content=str_replace("##viewName##","/auto/".$controllerName."_index",$content);

        if(!empty($params["export"]))
        {
            $pattern='/^select\s(.*?)\s+from\s+/is';
            preg_match($pattern, $sql,$matches);
            if(is_array($matches) && count($matches)>0)
                $exportSql=str_replace($matches[1],$params["export"] ,$sql);

            $content=str_replace("##exportSql##",$exportSql,$content);
        }

        //$newFile=ROOT_DIR.DIRECTORY_SEPARATOR."protected".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."".$controllerName."Controller.php";
        file_put_contents($controllerFile,$content);

        $this->returnSuccess();
    }

}