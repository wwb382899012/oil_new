<?php

class TemplateUtil {
    /**
     * @desc 模板参数替换,将$template里${key}替换为$values[key]
     * @param $template | string
     * @param $values | array
     * @return string
     */
    public static function parseTemplate($template, $values) {
        $result = '';
        $start_pos = 0;
        $template_len = strlen($template);
        TemplateUtil::parseTemplateRecursion($template, $template_len, $values, $result, $start_pos);

        return $result;
    }

    //异常信息模板参数${key}内容替换（字符串替换）
    public static function parseTemplateRecursion($template, $template_len, $values, &$result, &$start_pos) {
        while (true) {
            $pos = strpos($template, '${', $start_pos);
            if ($pos === false) {
                $result .= substr($template, $start_pos);
                $start_pos = $template_len;

                return $start_pos;
            }
            //把中间这段值加入结果
            $result .= substr($template, $start_pos, $pos - $start_pos);
            $start_pos = $pos;

            //找到一个${
            $pos_key_end = strpos($template, '}', $pos + 2);
            if ($pos_key_end === false) {
                BusinessException::throw_exception(OilError::$NOT_FIND, array('key' => '}', 'msg' => ''));
            }
            $key = substr($template, $pos + 2, $pos_key_end - ($pos + 2));
            if (!array_key_exists($key, $values)) {
                BusinessException::throw_exception(OilError::$NOT_IN_VALUE, array('key' => $key));
            }
            $result .= $values[$key];
            $start_pos = $pos_key_end + 1;
        }
    }

    /**
     * @desc 模板参数替换（正则替换）,将$template里${key}替换为$values[key]
     * @param $template | string
     * @param $values | array
     * @return string
     */
    public static function parseExceptionTemplate(&$template, $values) {
        $regex = '/\$\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/';
        preg_match_all($regex, $template, $matches);
        if (Utility::isNotEmpty($matches[1])) {
            foreach ($matches[1] as $key => $row) {
                $pattern = '/\$\{' . $row . '\}/';
                if (array_key_exists($row, $values)) {
                    $template = preg_replace($pattern, $values[$row], $template);
                } else {
                    BusinessException::throw_exception(OilError::$NOT_IN_VALUE, array('key' => $row));
                }
            }
        }
    }
}