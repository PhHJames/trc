<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2019/12/10 0010
 * Time: 16:48
 */
class Log_Db {
    public static function  WriteLog($type = ''  , $message = ''  , $level = 'info'){
        $LogModel = new LogModel();
        if( is_array($message)){
            $message = json_encode($message,JSON_UNESCAPED_UNICODE);
        }
        $message = htmlspecialchars($message);
        return $LogModel->saveLog($type  , $message  , $level );
    }
}