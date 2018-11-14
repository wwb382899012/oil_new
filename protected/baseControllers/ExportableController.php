
<?php
/**
*	导出功能
*/
class ExportableController extends AttachmentController {
    public function export($sql, $col, $export_str, $fileName='') {
        ini_set('memory_limit','1024M');
        set_time_limit(0);
        $map = Map::$v;
        $file_path  = "/tmp/csv_".time().'.csv';
        // $file_path = "runtime/csv_".time().'.csv';
        $file = fopen($file_path,"w");
        fwrite($file,chr(0xEF).chr(0xBB).chr(0xBF));

        $col_array = json_decode(str_replace("'",'"', $export_str));

        $array = array();

        for ($i = 0 ; $i<count($col_array) ; $i++)
        {
            /*if ($col_array[$i]->type=='href')
            {
                continue;
            }*/
            $array[] = $col_array[$i]->text;
        }
        fputcsv($file,$array);
        $db = Mod::app()->db;
        $res = $db->createCommand(str_replace('{limit}'," ",str_replace('{col}',$col,$sql)))->query()->readAll();
        for ($i = 0 ; $i<count($res);  $i++)
        {
            $array = array();
            for ($j = 0 ; $j<count($col_array) ; $j++)
            {
                /*if ($col_array[$j]->type=='href')
                {
                    continue;
                }
                $text = $this->excel_format($res[$i] , $col_array[$j]->key, $col_array[$j]->type,$col_array[$j]->map_name,$map, $col_array[$j]);
                }*/
                $text = $this->excel_format($res[$i] , $col_array[$j]->key, $col_array[$j]->type,$col_array[$j]->map_name,$map, $col_array[$j]);
                $array[] = $text;
            }
            fputcsv($file,$array);
            unset($array);
        }
        // print_r($array);die;
        unset($res);
        fclose($file);
        // 从浏览器直接输出$filename
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type: application/vnd.ms-excel;");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        // header("Content-Disposition:attachment;filename=csv_".time().'.csv');
        header('Content-Disposition:attachment;filename="'.$fileName.'_'.date("Y年m月j日").'.csv"');
        header("Content-Transfer-Encoding:binary");
        ob_clean();
        $file = fopen($file_path,'r');
        echo fread($file,filesize($file_path));
        fclose($file);
        Mod::app()->end();
        return;
    }


    /**
    *   excel 导出格式化方法  
    *   @author sun
    *   @since 2016-06-21
    */
    public function excel_format($val_array,$key,$type,$map_name,$map, $col_config)
    {
        $key_array = explode(',',$key);
        if($type=='href')
            unset($key_array[0]);
        $val = "";
        foreach($key_array as $row)
        {
            $value = $val_array[$row];
            if($type=='href' && strpos($row, 'amount')!==false){
                $value = number_format($val_array[$row]/100,2);
            }

            $val = $val.$value;
        }
        if (!isset($val)||strlen(trim($val))==0)
        {
            return "-";
        }
        switch($type)
        {
            case 'amount':
                return number_format($val/100,2);
            case 'amount_map_key':
            	if($col_config->map_key) {
            		$mapValue = $val_array[$col_config->map_key];
            		$mapStr = '';
	                if(is_array($map[$map_name]) && is_array($map[$map_name][$mapValue]) && !empty($map[$map_name][$mapValue]['name'])) {
	                    $mapStr = $map[$map_name][$mapValue]['name'];
	                } else {
	                    $mapStr = $map[$map_name][$mapValue];
	                }
	                return $mapStr . number_format($val/100,2);
            	}
                return number_format($val/100,2);
            case 'text_map_key':
            	if($col_config->map_key) {
            		$mapValue = $val_array[$col_config->map_key];
            		$mapStr = '';
	                if(is_array($map[$map_name]) && is_array($map[$map_name][$mapValue]) && !empty($map[$map_name][$mapValue]['name'])) {
	                    $mapStr = $map[$map_name][$mapValue]['name'];
	                } else {
	                    $mapStr = $map[$map_name][$mapValue];
	                }
	                return $mapStr . $val;
            	}
                return $val;
            case 'date':
                return $val;
            case 'number':
                return $val;
            case "map_val":
                if(is_array($map[$map_name][$val]) && isset($map[$map_name][$val]['name'])) {
                    return $map[$map_name][$val]['name'];
                } else {
                    return $map[$map_name][$val];
                }

            case "map_vals":
                $str = "";
                foreach($array as $array_val){
                    if (strlen($str)==0){
                        $str = $map[$map_name][$array_val];
                        continue;
                    }
                    $str = $str . ',' . $map[$map_name][$array_val];
                }
                return $str;
            default:
                return is_numeric($val)?$val."\t":$val;
        }
    }

}