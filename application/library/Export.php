<?php

/**
 * Export 主要是在浏览器中导出.csv格式的数据
 * 彭军
 */
class Export {

//直接导出可以阅读的数据文件
    public function export($fileName ="noName", $head = array(),$body=array()){
        header ( "Content-type:application/vnd.ms-excel" );
        header ( "Content-Disposition:filename=" . $fileName . "-" .iconv ( "UTF-8", "GBK", date("Y-m-d_H_i_s" , time())."-".rand(1,10000) ) . ".csv" );
        // 打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');

        $column_name = array();//输出标题的头

        if (!empty($head)) {
            foreach ($head as $i => $v) {
                $column_name[$i] = iconv('utf-8', 'GBK//ignore', $v);
            }
            fputcsv($fp, $column_name);
        }
        
        foreach ($body as $tkey => $tvalue ) {
            $rows = array();
            foreach ($tvalue as $newkey => $newvalue) {
                $rows[] = iconv('utf-8', 'GBK//ignore', $newvalue);
            }
            fputcsv($fp, $rows);
        }
        fclose($fp) or die("Can't close file.csv");
    
    }

}

?>
