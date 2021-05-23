<?php
/**
 * Created by 不要复制我的代码.
 * User: 不要复制我的代码
 * Date: 2019/5/14 0014
 * Time: 上午 11:45
 */
class LogModel extends AbstractModel{
    protected $table = "log" ;
    protected $dbIndex = 0  ;
    /**
     * @desc 保存log
     * @params @type 日志的类型
     * @params $message 日志的内容
     * @params $level 日志的级别  info error waring 3个级别
     * @return integer
     */
    public function saveLog( $type = ''  , $message = ''  , $level = 'info' ){
        $data = [
            'level' => $level ,
            'type'=> $type ,
            'message'=>$message ,
            'create_date'=>date("Y-m-d H:i:s" , time() )
        ];
        return parent::insertData($data);
    }
}