<?php
/**
 * @Author: Awe
 * @Date:   2016-06-13 14:45:30
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-07 15:00:45
 */
class QrImportLogModel extends AbstractModel{
    protected $table = "qr_import_log" ;
    protected $dbIndex = 0  ;
    public function saveLog( $data = [] ){
        $data['create_date']=date("Y-m-d H:i:s" , time() );
        return parent::insertData($data);
    }
}