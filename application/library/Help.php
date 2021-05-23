<?php
/**
 * @Author: 不要复制我的代码
 * @Date:   2016-06-20 14:53:50
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2017-04-21 11:07:03
 * @des 后台的一些辅助性的方法
 */
class Help{
    //显示中转页面 提示页面
    //$title 成功的标题
    /*
    调用：
    $jumpData = array(
            array('url' => "" , 'name' =>"继续选择"),
            array('url'=>"" , 'name' =>"发布帖子"),
    );
    Help::showPage("发表帖子成功" , $jumpData);
    */
    public static function showPage($title = ''  , $jumpData = array() ){
        global $_G ;
        $_G['viewObject']->assign("title" , ($title) ? $title : '操作完成'  );
        $_G['viewObject']->assign("jumpData" , $jumpData  );
        $_G['viewObject']->display("public/middle.htm");
    }
    public static function  getPageList(){
        return array(
            10 => '每一页10条数据',
            15 => '每一页15条数据',
            20 => '每一页20条数据',
            25 => '每一页25条数据',
            30 => '每一页30条数据',
            50 => '每一页50条数据',
            100 => '每一页100条数据',
            150 => '每一页150条数据',
            200 => '每一页200条数据',
            250 => '每一页250条数据',
            300 => '每一页300条数据',
            350 => '每一页350条数据',
            450 => '每一页450条数据',
            550 => '每一页550条数据',
            650 => '每一页650条数据',
            750 => '每一页750条数据',
            850 => '每一页850条数据',
            950 => '每一页950条数据',
            1000 => '每一页1000条数据',
        );
    }
}
