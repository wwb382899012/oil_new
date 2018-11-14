<?php
/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2014/12/4
 * Time: 10:53
 */


class Utility
{

    /**
     * 系统主库
     */
    const DB = 0;
    /**
     * 操作日志库
     */
    const DB_LOG = 2;
    /**
     * 重库
     */
    const DB_SLAVE = 1;
    /**
     * 历史数据库
     */
    const DB_HISTORY = 3;

    /**
     * 获取数据库
     * @param $dbType
     * @return mixed
     */
    public static function getDb($dbType=0)
    {
        switch ($dbType)
        {
            case 1:
                $db = Mod::app()->dbSlave;
                break;
            case 2:
                $db = Mod::app()->dbLog;
                break;
            case 3:
                $db = Mod::app()->db_history;
                break;
            default :
                $db = Mod::app()->db;
                break;
        }
        return $db;
    }

    /**
     * 开始事务
     * @param int $dbType
     * @return mixed
     */
    public static function beginTransaction($dbType = Utility::DB)
    {
        $db = Utility::getDb($dbType);
        return $db->beginTransaction();
    }

    /**
     * 执行查询SQL并返回所有结果集
     * @param $sql
     * @param int $dbType
     * @param array $params 参数集
     * @return array
     */
    public static function query($sql, $dbType = 0, $params = array())
    {
        if (empty($sql))
            return 0;
        $db = Utility::getDb($dbType);
        $command = $db->createCommand($sql);
        $res = $command->query($params)->readAll();
        return $res;
    }

    /**
     * 执行返回单个数字的是查询，存在返回实际值，否则返回0
     * @param $sql
     * @param $fieldName
     * @return int
     */
    public static function queryOneNumber($sql, $fieldName)
    {
        $data = Utility::query($sql);
        if (!Utility::isEmpty($data))
            return $data[0][$fieldName];
        else
            return 0;
    }

    /**
     * 查询结果是否为空
     * @param $sql
     * @return int  0：不为空，1为空
     */
    public static function queryIsEmpty($sql)
    {
        $data = Utility::query($sql);
        if (Utility::isEmpty($data))
            return 1;
        else
            return 0;
    }

    /**
     * 强制在主库执行SQL查询并返回所有结果集
     * @param $sql
     * @return mixed
     */
    public static function queryInMaster($sql, $dbType = 0)
    {
        if (empty($sql))
            return 0;
        $db = Utility::getDb($dbType);
        $db->forceMaster = true;
        $command = $db->createCommand($sql);
        $res = $command->query()->readAll();
        return $res;
    }

    /**
     * 执行SQL命令并返回影响的行数，会自动判断是否有事务，如果没有外部事务会自动启用事务，
     * 如果无外部事务，出错则返回-1；如果有外部事务，则直接抛出异常
     * 参数$sql可以是sql语句的数组，执行启用事务模式。
     *
     * @param $sql  可以是sql语句的数组
     * @param int $dbType
     * @param array $params
     * @return array|int
     * @throws Exception
     */
    public static function execute($sql, $dbType = 0, $params = array())
    {
        if (empty($sql))
        {
            if (is_array($sql))
                return array(0);
            else
                return 0;
        }

        $db = Utility::getDb($dbType);

        $isInTrans = Utility::isInDbTrans($dbType);
        if (!$isInTrans)
        {
            $trans = $db->beginTransaction();
        }

        try
        {
            if (is_array($sql))
            {
                foreach ($sql as $v)
                {
                    if (!empty($v))
                    {
                        $command = $db->createCommand($v);
                        $rowCount[] = $command->execute($params);
                    } else
                        $rowCount[] = 0;
                }

            } else
            {
                $command = $db->createCommand($sql);
                $rowCount = $command->execute($params);
            }
            if (!$isInTrans)
            {
                $trans->commit();
            }
            return $rowCount;
        } catch (Exception $e)
        {
            Mod::log("Execute Sqls: " . $e->getMessage(), "error");
            if (!$isInTrans)
            {
                try
                {
                    $trans->rollback();
                } catch (Exception $ee)
                {
                }
                return -1;
            } else
                throw $e;
        }

    }

    /**
     * 执行SQL命令并返回影响的行数，如果出错，返回-1,
     *
     * @param $sql  需要执行的sql语句数组
     * @param int $dbType
     * @return array|int
     */
    public static function executeWithNoTransaction($sql, $dbType = 0)
    {
        if (empty($sql))
        {
            if (is_array($sql))
                return array(0);
            else
                return 0;
        }

        $db = Utility::getDb($dbType);
        try
        {
            if (is_array($sql))
            {
                foreach ($sql as $v)
                {
                    if (!empty($v))
                    {
                        $command = $db->createCommand($v);
                        $rowCount[] = $command->execute();
                    } else
                        $rowCount[] = 0;
                }

            } else
            {
                $command = $db->createCommand($sql);
                $rowCount = $command->execute();
            }
        } catch (Exception $e)
        {
            $rowCount = -1;
        }
        return $rowCount;
    }

    /**
     * 执行SQL语句，没有事务，没有try catch
     * @param $sql
     * @param int $dbType
     * @param array $params
     * @return array|int
     */
    public static function executeSql($sql, $dbType = 0, $params = array())
    {
        if (empty($sql))
        {
            if (is_array($sql))
                return array(0);
            else
                return 0;
        }

        $db = Utility::getDb($dbType);

        if (is_array($sql))
        {
            foreach ($sql as $v)
            {
                if (!empty($v))
                {
                    $command = $db->createCommand($v);
                    $rowCount[] = $command->execute($params);
                } else
                    $rowCount[] = 0;
            }

        } else
        {
            $command = $db->createCommand($sql);
            $rowCount = $command->execute($params);
        }

        return $rowCount;
    }

    /**
     * 判断目录是否存在，如果不存在，则自动创建
     *
     * @param $dir
     */
    public static function checkDirectory($dir)
    {
        if (!is_dir($dir))
            mkdir($dir, 0777, true);
    }


    /**
     * 返回指定长度的随机码，默认长度为6
     *
     * @param int $length
     * @return string
     */
    public static function getRandomKey($length = 6)
    {
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $key = "";
        for ($i = 0; $i < $length; $i++)
        {
            $key .= $pattern{mt_rand(0, 35)};    //生成php随机数
        }
        return $key;
    }

    /**
     * 判断是否为空的数据表，主要针对Sql查询出的数据集
     * @param $data
     * @return bool
     */
    public static function isEmpty($data)
    {
        if (!is_array($data) || count($data) < 1)
            return true;
        else
            return false;
    }

    /**
     * 判断是否为非空的数据表，主要针对Sql查询出的数据集
     * @param $data
     * @return bool
     */
    public static function isNotEmpty($data)
    {
        if (is_array($data) && count($data) > 0)
            return true;
        else
            return false;
    }

    /**
     * 设置Cookies
     * @param $key
     * @param $val
     */
    public static function setCookie($key, $val)
    {
        setcookie($key, $val, time() + 60 * 60, '/', Mod::app()->params['url_host']);
    }

    /**
     * 获取系统Id
     * @return mixed
     */
    public static function getSystemId()
    {
        return Mod::app()->params['systemId'];
    }

    /**
     * 获取当前用户信息
     * @return array
     */
    public static function getNowUser()
    {
        return SystemUser::getUser(Utility::getNowUserId());
        /*$prefix=Mod::app()->params['prefix'];
        if (!array_key_exists($prefix.'system_account',$_COOKIE)){
            return array("user_id"=>0);
        }
        $array = explode('|',$_COOKIE[$prefix."system_user_id"]);
        $user_id = $array[0];
        $array = explode('|',$_COOKIE[$prefix."system_account"]);
        $account = $array[0];

        $user_name = $_COOKIE[$prefix."system_user_name"];
        $res = array("user_id"=>$user_id,"account"=>$account,"user_name"=>$user_name);
        return $res;*/
    }

    /**
     * 获取当前用户ID
     * @return int
     */
    public static function getNowUserId()
    {
        /*$prefix=Mod::app()->params['prefix'];
        if (!array_key_exists($prefix.'system_account',$_COOKIE)){
            return 0;
        }
        $array = explode('|',$_COOKIE[$prefix."system_user_id"]);
        return $array[0];*/
        return Mod::app()->user->id;
    }

    /**
     * @desc 获取当前用户所属角色
     * @return array
     */
    public static function getNowUserRoles() {
        $userId = Utility::getNowUserId();
        $roles = SystemUser::getRoles($userId);
        $res = array();
        if(Utility::isNotEmpty($roles)) {
            foreach ($roles as $key => $row) {
                $res[] = $row['role_id'];
            }
        }
        return $res;
    }

    /**
     * @desc 判断当前用户是否是管理员
     * @return bool
     */
    public static function checkNowUserIsAdmin() {
        $nowUserRoles = Utility::getNowUserRoles();
        if(in_array(ConstantMap::ADMIN_ROLE_ID, $nowUserRoles)) {
            return true;
        }
        return false;
    }

    /**
     * 获取主数据库名
     * @return mixed
     */
    public static function getMainDbName()
    {
        return Mod::app()->params['main_db_name'];
    }

    /**
     * 设置Redis缓存
     * @param $keyName
     * @param $value
     * @param int $seconds 默认值为0，没有过期时间
     */
    public static function setCache($keyName, $value, $seconds = 0)
    {
        $redis = Mod::app()->redis;
        if ($seconds > 0)
            $redis->setex($keyName, $seconds, $value);
        else
            $redis->set($keyName, $value);
    }

    /**
     * 设置Redis缓存
     * @param $keyName
     * @param $value
     * @param string $timeStr 默认:"+1 day"
     */
    public static function setCacheWithTimeSpan($keyName, $value, $timeStr = "+1 day")
    {
        $redis = Mod::app()->redis;
        if ($redis->set($keyName, $value))
        {
            if (!empty($timeStr))
                $redis->expireAt($keyName, strtotime($timeStr));
        }
    }

    /**
     * 设置Redis缓存，同时设置过期时间点
     * @param $keyName
     * @param $value
     * @param $dateTime
     */
    public static function setCacheWithExpireTime($keyName, $value, $dateTime)
    {
        $seconds = strtotime($dateTime) - strtotime("now");
        //echo $seconds;
        if ($seconds <= 0)
            $seconds = 1;
        self::setCache($keyName, $value, $seconds);
    }

    /**
     * 获取缓存内容
     * @param $keyName
     * @return string|null
     */
    public static function getCache($keyName)
    {
        $redis = Mod::app()->redis;
        if ($redis->exists($keyName))
        {
            $s = $redis->get($keyName);
            return $s;
        } else
            return null;
    }


    /**
     * 设置redis 哈希缓存值
     * @param $keyName
     * @param $fieldName
     * @param $value
     */
    public static function hSetCache($keyName, $fieldName, $value)
    {
        $redis = Mod::app()->redis;
        $redis->hSet($keyName, $fieldName, $value);
    }

    /**
     * 判断缓存是否存在
     * @param $keyName
     * @return mixed
     */
    public static function exists($keyName)
    {
        $redis = Mod::app()->redis;
        return $redis->exists($keyName);
    }

    /**
     * 判断哈希缓存是否存在
     * @param $keyName
     * @param $fieldName
     * @return mixed
     */
    public static function hExists($keyName, $fieldName)
    {
        $redis = Mod::app()->redis;
        return $redis->hExists($keyName, $fieldName);
    }


    /**
     * 获取redis哈希缓存值
     * @param $keyName
     * @param $fieldName
     * @return null
     */
    public static function hGetCache($keyName, $fieldName)
    {
        $redis = Mod::app()->redis;
        if ($redis->hExists($keyName, $fieldName))
        {
            $s = $redis->hGet($keyName, $fieldName);
            return $s;
        } else
            return null;
    }

    /**
     * 清除缓存/解锁
     * @param $keyName
     */
    public static function clearCache($keyName)
    {
        $redis = Mod::app()->redis;
        if ($redis->exists($keyName))
        {
            $redis->delete($keyName);
        }
    }

    /**
     * 删除redis哈希缓存中某一项的值
     * @param $keyName
     * @param $fieldName
     */
    public static function hDelCache($keyName, $fieldName)
    {
        $redis = Mod::app()->redis;
        $redis->hDel($keyName, $fieldName);
    }


    /**
     * SQL注入过滤
     * @param $str
     * @return mixed
     */
    public static function filterInject($str)
    {
        $res = preg_replace('/master|truncate|exec|select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|<|>|javascript|jscript|vbscript|&|\r|\t/', '', $str);
        if ($res != $str)
            return self::filterInject($res);
        else
            return trim($res);
    }

    /**
     * 判断是否自然数型字符串
     * @param $id
     * @return int
     */
    public static function isIntString($id)
    {
        return preg_match("/^\d*$/", $id);
    }

    /**
     * 判断查询的id参数
     * @param $id
     * @return bool|int
     */
    public static function checkQueryId($id)
    {
        if (empty($id))
            return false;
        return Utility::isIntString($id);
    }

    /**
     * 获取秒数，如果参数格式错误返回0
     * @param $timeStr "1 day"  "1 hour"  "1 minute"
     * @return int
     */
    public static function getSeconds($timeStr)
    {
        $arr = explode(" ", $timeStr);
        if (count($arr) != 2)
            return 0;
        switch ($arr[1])
        {
            case "day":
                return (int)(3600 * 24 * ((double)$arr[0]));
                break;
            case "hour":
                return (int)(3600 * ((double)$arr[0]));
                break;
            case "minute":
                return (int)(60 * ((double)$arr[0]));
                break;
            default:
                return (int)$arr[0];
        }
    }

    /**
     * 解码，用于处理包含Unicode码的字符串
     * @param $str
     * @return mixed
     */
    public static function decodeUnicode($str)
    {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
            create_function(
                '$matches',
                'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
            ),
            $str);
        //return preg_replace("#\\\u([0-9a-f]+)#ie","iconv('UCS-2','UTF-8', pack('H4', '\\1'))",$str);
    }

    /**
     * 对变量进行 JSON 编码
     * @param mixed value 待编码的 value ，除了resource 类型之外，可以为任何数据类型，该函数只能接受 UTF-8 编码的数据
     * @return string 返回 value 值的 JSON 形式
     */
    public static function json_encode($value)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<'))
        {
            $str = json_encode($value);
            $str = preg_replace_callback(
                "#\\\u([0-9a-f]{4})#i",
                function ($matchs)
                {
                    return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
                },
                $str
            );
            return $str;
        } else
        {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 获取指定路径文件的MIME类型
     * @param $filePath
     * @return string
     */
    public static function getFileMIME($filePath)
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        switch ($extension)
        {
            case "docx":
                return "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
                break;
            case "xlsx":
                return "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                break;
            case "pptx":
                return "application/vnd.openxmlformats-officedocument.presentationml.presentation";
                break;
        }
        if (version_compare(PHP_VERSION, '5.3.0', '<'))
        {
            return mime_content_type($filePath);
        } else
        {
            $fi = new finfo(FILEINFO_MIME_TYPE);
            //print_r($fi->file($filePath)); die;
            return $fi->file($filePath);
        }
    }

    /**
     * 输出文件
     * @param $filePath
     * @param string $fileName
     * @param string $type 区分pdf是否在线打开
     */
    public static function outputFile($filePath, $fileName = "", $type = "")
    {
        //$filePath=iconv('UTF-8','GB2312',$filePath);
        if (file_exists($filePath))
        {
            try
            {
                //$file_path = iconv('GB2312','UTF-8',$filePath);
                $mime_type = Utility::getFileMIME($filePath);
                //echo $mime_type;return;
                header("Content-type:" . $mime_type);

                if (!empty($fileName))
                {
                    $extension = strtolower(pathinfo(basename($filePath), PATHINFO_EXTENSION));
                    $prefix = strstr(basename($filePath), '_', true);
                    if (strtolower(pathinfo(basename($fileName), PATHINFO_EXTENSION)))
                    {
                        $fileName = $prefix . '_' . $fileName;
                    } else
                    {
                        $fileName = $prefix . '_' . $fileName . '.' . $extension;
                    }
                } else
                {
                    $fileName = basename($filePath);
                }
                //$fileName=empty($fileName) ? basename($filePath) : $fileName;

                $mime = "";
                $mArr = array("image/jpeg", "image/png", "image/gif", "image/bmp");
                if (empty($type))
                {
                    //$mime = ($mime_type== 'image/jpeg' || $mime_type=='image/png' || $mime_type=='image/gif' || $mime_type=='image/bmp' || $mime_type=='application/pdf') ? true : false;
                    $mime = (in_array($mime_type, $mArr) || $mime_type == 'application/pdf') ? true : false;
                } else
                {
                    //$mime = ($mime_type== 'image/jpeg' || $mime_type=='image/png' || $mime_type=='image/gif' || $mime_type=='image/bmp') ? true : false;
                    $mime = in_array($mime_type, $mArr) ? true : false;
                }
                //var_dump($mime);die;
                if ($mime)
                {
                    header("filename=" . $fileName);
                } else
                {
                    header("Content-Disposition: attachment; filename=" . $fileName);
                }

                //header("Content-Disposition: attachment; filename=".basename($filePath));
                echo file_get_contents($filePath);
            } catch (Exception $e)
            {
                Mod::log("获取文件“" . $filePath . "”出错：" . $e->getMessage(), "error");
                echo "获取文件出错";
            }
        } else
            echo "文件不存在";
    }

    /**
     * 判断是否在事务中
     * @param int $dbType
     * @return bool
     */
    public static function isInDbTrans($dbType = 0)
    {
        $db = Utility::getDb($dbType);
        $res = $db->getCurrentTransaction();
        if (!empty($res))
            return true;
        else
            return false;
    }

    /**
     * 根据服务器编码判断是否需要转码
     * @param $str
     * @return string
     */
    public static function checkServerEncode($str)
    {
        $serverEncode = Mod::app()->params['serverEncode'];
        if ($serverEncode == "UTF-8")
            return $str;
        else
            return iconv('UTF-8', $serverEncode, $str);
    }

    /**
     * 获取日期
     * @param string $dateStr ，默认为当前日期
     * @return string
     */
    public static function getDate($dateStr = "")
    {
        if (empty($dateStr))
            return date("Y-m-d");
        else
            return date("Y-m-d", strtotime($dateStr));
    }

    /**
     * 获取当前时间
     * @param string $timeStr ，默认为当前时间
     * @return string
     */
    public static function getDateTime($timeStr = "")
    {
        if (empty($timeStr))
            return date("Y-m-d H:i:s");
        else
            return date("Y-m-d H:i:s", strtotime($timeStr));
    }

    /**
     * word转PDF
     * @param $wordFile
     * @param string $pdfFile
     */
    public static function wordToPdf($wordFile, $pdfFile = "")
    {
        if (empty($pdfFile))
        {
            $pdfFile = substr($wordFile, 0, strrpos($wordFile, ".")) . ".pdf";
        }
        exec("java -jar /usr/local/jodconverter/lib/jodconverter-cli-2.2.2.jar $wordFile $pdfFile >/dev/null 2>&1", $a, $b);
        //var_dump($a);
    }

    public static function getIP()
    {
        return Mod::app()->request->userHostAddress;
    }

    /**
     * 增加操作日志
     * @param $content               json格式的数据内容或整个参数数组
     * @param string $remark 备注说明
     * @param string $modelName 模型类名
     * @param int $objectId 数据id
     */
    public static function addActionLog($content, $remark = "", $modelName = "", $objectId = 0)
    {
        try
        {
            $isSaveLog = Mod::app()->params["isSaveActionLog"];
            if (empty($isSaveLog))
                return;
            if (is_array($content))
            {
                $params = $content;
                $content = $params["content"];
                $modelName = $params["model_name"];
                $objectId = $params["object_id"];
                $remark = $params["remark"];
            }
            $data = array(
                "user_id" => Utility::getNowUserId(),
                "controller_name" => Mod::app()->getController()->id,
                "action_name" => Mod::app()->getController()->getAction()->id,
                "model_name" => $modelName,
                "object_id" => $objectId,
                "content" => $content,
                "remark" => $remark,
                "create_user_id" => Utility::getNowUserId(),
                "create_ip" => Utility::getIP(),
                "create_time" => date("Y-m-d H:i:s"),
            );

            if ($isSaveLog == 1)
                AMQPService::publishActionLog($data);
            else
            {
                $model = new ActionLog();
                $model->setAttributes($data, false);
                $res = $model->save();
                if (!$res)
                    Mod::log("Save action log error, the data is:" . json_encode($data), "error");
            }
        }
        catch (Exception $ee)
        {}

    }

    /**
     * 增加模型变更记录
     * @param $model
     * @param string $remark
     */
    public static function addActionModelLog($model,$remark = "")
    {
        if(empty($model))
            return;
        $class=get_class($model);
        $remark=empty($remark)?$class." update":$remark;
        self::addActionLog(json_encode($model->oldAttributes),$remark,$class,$model->getPrimaryKey());
    }

    /**
     * @desc 必传参数校验(含注入过滤)
     * @param $params |array 前台传过来的参数数组
     * @param $required |array 必传参数数组
     * @return boolean
     */
    public static function checkRequiredParams($params = array(), $required = array())
    {
        $filterInjectParams = array();
        if (count($params) > 0)
        {
            //参数注入过滤
            foreach ($params as $key => $val)
            {
                if (is_string($val) && !empty($val))
                {
                    $filterInjectParams[$key] = Utility::filterInject($val);
                } else
                    $filterInjectParams[$key] = $val;
            }

            //必填字段校验
            if (count($required) > 0)
            {
                foreach ($required as $k => $v)
                {
                    if (!array_key_exists($v, $params) || empty($params[$v]))
                    {
                        return array('isValid' => false, 'params' => $filterInjectParams);
                    }
                }
            }
        }

        return array('isValid' => true, 'params' => $filterInjectParams);;
    }

    /**
     * @desc 必传参数校验
     * @param $params |array 前台传过来的参数数组
     * @param $required |array 必传参数数组
     * @return boolean
     */
    public static function checkRequiredParamsNoFilterInject($params = array(), $required = array())
    {
        if (Utility::isNotEmpty($params)) {
            if (Utility::isNotEmpty($required))
            {
                foreach ($required as $k => $v)
                {
                    if (!array_key_exists($v, $params) || empty($params[$v]))
                    {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * 把秒时间差转换成友好显示
     * @param $seconds
     * @return string
     */
    public static function timeSpanToString($seconds)
    {
        $seconds = (int)$seconds;
        if ($seconds < 86400)
        {//如果不到一天
            $format_time = gmstrftime('%H时%M分%S秒', $seconds);
        } else
        {
            $time = explode(' ', gmstrftime('%j %H %M %S', $seconds));//Array ( [0] => 04 [1] => 14 [2] => 14 [3] => 35 )
            $format_time = ($time[0] - 1) . '天' . $time[1] . '时' . $time[2] . '分' . $time[3] . '秒';
        }
        return $format_time;
    }

    /**
     * 获取常用的忽略字段值数组
     * @return array
     */
    public static function getIgnoreFields()
    {
        return array("create_user_id", "create_time", "update_user_id", "update_time",);
    }

    /**
     * @desc 检查必传附件是否上传
     * @param $attachmentType
     * @param $base_id | int 对象id
     * @param $related_field | string 附件关联id
     * @return string | bool
     */
    public static function checkRequiredAttachments($attachmentType, $base_id, $related_field = 'base_id')
    {
        $attachmentObj = AttachmentFactory::getInstance($attachmentType);
        $modelName = str_replace(" ", "", ucwords(str_replace('_', ' ', substr($attachmentObj->config['tableName'], 2))));
        if (Utility::isNotEmpty($attachmentObj->typeConfig))
        {
            foreach ($attachmentObj->typeConfig as $key => $row)
            {
                if (!empty($row['required']))
                {
                    $obj = $modelName::model()->find($related_field."=" . $base_id . " and type=" . $row['id'] . " and status=1");
                    if (empty($obj->id))
                    {
                        return "*标注附件必传，请上传" . $row['name'];
                    }
                }
            }
            return true;
        }
    }

    /**
     * 添加系统告警
     * @param $content
     */
    public static function addSystemAlarm($content)
    {

    }

    /**
     * @desc 格式化数字属性，保留两位小数
     * @param $data | array 属性数组
     * @param $attributes | array 需要格式化的属性数组
     * @return array
     */
    public static function numberFormatAttributes($data, $attributes = array())
    {
        if (count($attributes) > 0)
        {
            if (count($data) > 0)
            {
                foreach ($attributes as $k)
                {
                    if (array_key_exists($k, $data) && !empty($data[$k]))
                    {
                        $data[$k] = number_format($data[$k], 2, ".", "");
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @desc 复制指定文件到指定位置
     * @param $sourcefile |string 源文件路径
     * @param $dir |string 指定路径
     * @param $filename |目标文件名
     * @return bool
     */
    public static function file2dir($sourcefile, $dir, $filename = "")
    {
        if (!file_exists($sourcefile))
        {
            return false;
        }
        return copy($sourcefile, $dir . '' . $filename);
    }

    /**
     * 根据字段排序，写法类似于SQL
     * @param $dataSet
     * @param $orderBy
     * @param bool $isZh 排序字段是否是中文
     * @return mixed
     */
    public static function sortByFields($dataSet,$orderBy,$isZh=false)
    {
        $orderBy=preg_replace("/\s{2,}|　/"," ",$orderBy);
        $s=explode(",",$orderBy);
        //$sorts=array();
        $sortType=array("DESC"=>"SORT_DESC","ASC"=>"SORT_ASC");
        $params='';
        foreach ($s as $v)
        {
            if(!empty($v))
            {
                $arr=explode(" ",$v);
                $params.='$arrSort[\''.$arr[0].'\'],'.$sortType[strtoupper($arr[1])].',';
                $sorts[$arr[0]]=$sortType[strtoupper($arr[1])];
            }
        }
        if(empty($params))
            return $dataSet;

        $arrSort = array();
        foreach ($dataSet AS $index => $row)
        {
            foreach ($row AS $key => $value)
            {
                if ($isZh && key_exists($key, $sorts))
                    $arrSort[$key][$index] = iconv("UTF-8", "GB2312", $value);
                else
                    $arrSort[$key][$index] = $value;

            }
        }

        eval('array_multisort('.$params.' $dataSet);');
        return $dataSet;
    }


    /**
     * 获取加密的密码
     * @param $password
     * @return string
     */
    public static function getSecretPassword($password)
    {
        return md5($password);
    }

    /**
     * @desc 分转化为元
     * @params number $fen
     */
    public static function fen2yuan($fen) {
        if (!is_numeric($fen)) {
            return 0;
        }
        return sprintf("%.2f", $fen / 100);
        /*if (is_int($fen) || ctype_digit($fen)) {
            $fen = intval($fen);
            if ($fen % 100 === 0) {
                return sprintf("%d", $fen / 100);
            } else {
                return sprintf("%d.%02d", ($fen - $fen % 100) / 100, $fen % 100);
            }
        } else {
            BusinessException::throw_exception(OilError::$FEN_TO_YUAN_ERR, array('fen' => $fen));
        }*/
    }

    public static function numberFormatFen2Yuan($fen, $scale = 2) {
        return number_format(Utility::fen2yuan($fen), $scale);
    }

    /**
     * @desc 元转化为分
     * @param float|int $yuan
     * @return int
     */
    public static function yuan2fen($yuan) {
        if (is_float($yuan)) {
            $yuan = strval($yuan);
        }
        if (is_int($yuan) || ctype_digit($yuan)) {
            $y = intval($yuan);
            $f = 0;
        } else {
            if (preg_match('/^[0-9]+.[0-9]$/', $yuan)) {
                $y = intval(substr($yuan, 0, - 2));
                $f = intval(substr($yuan, - 1)) * 10;
            } else {
                if (preg_match('/^[0-9]+.[0-9][0-9]$/', $yuan)) {
                    $y = intval(substr($yuan, 0, - 3));
                    $f = intval(substr($yuan, - 2));
                } else {
                    if (!is_string($yuan)) {//非字符串
                        BusinessException::throw_exception(OilError::$NOT_STANDARD_YUAN, array('yuan' => $yuan));
                    } else {
                        BusinessException::throw_exception(OilError::$NOT_STANDARD_YUAN, array('yuan' => $yuan));
                    }
                }
            }
        }
        //转化为分
        if (PHP_INT_SIZE === 4) {//32位机器
            if ($y < 21474836) {
                return $y * 100 + $f;
            } else {
                if ($y == 21474836 && $f <= 47) {
                    return $y * 100 + $f;
                } else {
                    BusinessException::throw_exception(OilError::$YUAN_TO_FEN_ERR, array('yuan' => $yuan, 'num' => '2147483647L'));
                }
            }
        } else {
            if (PHP_INT_SIZE === 8) {//64位机器
                if ($y < 92233720368547758) {
                    return $y * 100 + $f;
                } else {
                    if ($y == 92233720368547758 && $f <= 07) {
                        return $y * 100 + $f;
                    } else {
                        BusinessException::throw_exception(OilError::$YUAN_TO_FEN_ERR, array('yuan' => $yuan, 'num' => '9223372036854775807'));
                    }
                }
            } else {
                BusinessException::throw_exception(OilError::$UNSUPPORT_COMPUTER);
            }
        }
    }


    /**
     * 获取通用忽略属性名数组
     * @param array
     * @return array
     */
    public static function getCommonIgnoreAttributes($ignoreAttr = array())
    {
        $commonIgnoreAttr = array("create_user_id","create_time","update_user_id","update_time");
        $ignoreAttributes = array_merge($commonIgnoreAttr, $ignoreAttr);
        return $ignoreAttributes;
    }

    /**
     * @param $values
     * @return array
     */
    public static function unsetCommonIgnoreAttributes(&$values)
    {
        $names=self::getCommonIgnoreAttributes();
        foreach ($names as $n)
            unset($values[$n]);
        return $values;
    }

    /**
     * @desc days360计算日期相差天数
     * @param string $start 开始日期
     * @param string $end 结束日期
     * @return int
     */
    public static function diffDays360($start, $end) {
        if (strtotime($start) > strtotime($end)) {
            $temp = $start;
            $start = $end;
            $end = $temp;
        }

        $start = new DateTime($start);
        $end = new DateTime($end);
        $startDay = $start->format('d');
        $startMonth = $start->format('m');
        $startYear = $start->format('y');

        $endDay = $end->format('d');
        $endMonth = $end->format('m');
        $endYear = $end->format('y');
        if ($startDay == 31) {
            -- $startDay;
        } elseif (($startMonth == 2 && ($startDay == 29 || ($startDay == 28 && !Utility::isLeapYear($startYear))))) {
            $startDay = 30;
        }
        if ($endDay == 31) {
            if ($startDay != 30) {
                $endDay = 1;
                if ($endMonth == 12) {
                    ++ $endYear;
                    $endMonth = 1;
                } else {
                    ++ $endMonth;
                }
            } else {
                $endDay = 30;
            }
        }

        return $endDay + $endMonth * 30 + $endYear * 360 - $startDay - $startMonth * 30 - $startYear * 360;
    }

    /**
     * @desc 坚持是否是闰年
     * @param string $year
     * @return bool
     */
    public static function isLeapYear($year) {
        return ((($year % 4) == 0) && (($year % 100) != 0) || (($year % 400) == 0));
    }

    /**
     * 调用接口命令
     * @param $params
     * @param $url
     * @return array|mixed
     */
    public static function cmd($params,$url)
    {
        $curl = Mod::app()->curl;
        try{
            $res = $curl->post($url, json_encode($params));
            Mod::log("CMD Log, params is ".json_encode($params).", and result is ".$res);
            if(empty($res))
                return array("code"=>-1,"msg"=>"无返回值");
            else
                return json_decode($res, true);
        }
        catch(Exception $e)
        {
            Mod::log("CMD Log, params is ".json_encode($params).", and error message is ".$e->getMessage(),"error");
            return array("code"=>-1,"msg"=>$e->getMessage());
        }

    }

    /**
     * @desc 数字格式化
     * @param float $num
     * @param int $n 位数
     * @return float
     */
    public static function numberFormatToDecimal($num, $n = 2) {
        return sprintf("%.".$n."f", $num);
    }

    /**
     * @desc 二维数组排序 按照指定的key,对数组进行排序
     * @param array $arr 将要排序的数组
     * @param string $key 指定排序的key
     * @param string $type 排序类型 asc | desc
     * @return array
     */
    public static function arraySort($arr, $key, $type = 'asc') {
        $keysvalue = $new_array = array();
        if(Utility::isNotEmpty($arr)) {
            foreach ($arr as $k => $v){
                $keysvalue[$k] = $v[$key];
            }
            $type == 'asc' ? asort($keysvalue) : arsort($keysvalue);
            reset($keysvalue);
            if(Utility::isNotEmpty($keysvalue)) {
                foreach ($keysvalue as $k => $v) {
                    $new_array[$k] = $arr[$k];
                }
            }
            return $new_array;
        }
        return $arr;
    }

    /**
     * @desc 二维数组排序 按照指定条件,对数组进行排序
     * @param array $arr 将要排序的数组
     * @param array $condition 指定条件 eg: array(array('key'=>'name', 'type'=>'asc'), array('key'=>'age', 'type'=>'desc'))
     * @return array
     */
    public static function arrayMultiSort($arr, $condition) {
        if (Utility::isNotEmpty($arr) && Utility::isNotEmpty($condition)) {
            foreach ($condition as $key => $row) {
                if(!empty($row['key'])) {
                    $row['type'] = !empty($row['type']) ? $row['type'] : 'asc';
                    $arr = Utility::arraySort($arr, $row['key'], $row['type']);
                }
            }
        }
        return $arr;
    }

    /**
     * @desc 检查必填但可为空的参数
     * @param array $params
     * @param array $required
     * @return bool
     */
    public static function checkMustExistParams($params = array(), $required = array())
    {
        if (Utility::isNotEmpty($params)) {
            if (Utility::isNotEmpty($required))
            {
                foreach ($required as $k => $v)
                {
                    if (!array_key_exists($v, $params))
                    {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * @desc 加锁
     * @param string $redisKeyName
     * @param int $expire
     * @return bool
     */
    public static function lock($redisKeyName, $expire = 60) {
        $redis = Mod::app()->redis;
        if(!$redis->exists($redisKeyName)) {
            if($redis->setnx($redisKeyName, 1)) {
                if($expire > 0) {
                    $redis->expireAt($redisKeyName, strtotime("+" . $expire . " seconds")); //设置过期时间
                }
                return true;
            }
            else
                return false;
        }
        else
        {
            return false;
        }
    }

    /**
     * @desc 解锁
     * @param $redisKeyName string
     * @return bool
     */
    public static function unlock($redisKeyName) {
        $redis = Mod::app()->redis ;
        if($redis->exists($redisKeyName)) {
            $redis->delete($redisKeyName);
        }
    }

    /**
     * @desc 获取当前请求地址
     * @param 
     * @return string
     */
    public static function getRequestUrl()
    {
        $request = Mod::app()->getRequest();

        return $request->getHostInfo() . $request->getUrl();
    }

    /**
     * 获取当前 Unix 时间戳的秒数
     * @return string
     */
    public static function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}