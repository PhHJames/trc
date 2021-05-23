<?php
/**
 * Created by 不要复制我的代码.
 * User: 不要复制我的代码
 * Date: 2019/5/14 0014
 * Time: 上午 11:45
 */
class ChannelModel extends AbstractModel{
    protected $table = "channel" ;
    protected $dbIndex = 0  ;
    public function getChannelConf(){
        return include_once APP_PATH . "/conf/channel_extra.php";
    }
    //根据通道名称获取通道配置的字段
    public function getChannelField($chanel){
        $data = $this->getChannelConf();
        return isset($data[$chanel])?$data[$chanel]:array();
    }
    public function getChannel($where = '' ){
        $sql = "select * from channel where 1 = 1 {$where} order by id desc ";
        return $this->db(0)->find($sql);
    }
}