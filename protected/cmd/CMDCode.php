<?php

/**
 * Created by youyi000.
 * DateTime: 2016/10/9 14:59
 * Describe：
 */
class CMDCode {
    const CODE_METHOD_PORT_INVALID = "1001"; //请求方法或端口异常
    const CODE_NO_POST_DATA = "1002"; //没有请求内容
    const CODE_CMD_INVALID = "1003"; //命令字不存在
    const CODE_CMD_ERROR = "1009"; //命令执行出错

    protected function none() {
    }
}