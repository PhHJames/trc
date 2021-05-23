<?php
/**
 * @Author: Awe
 * @Date:   2018-10-19 11:24:44
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-03-25 13:19:29
 * @desc 后台导航文件
 * 备注 其中 modular 这个节点主要是保存数据库的
 * href 这个节点主要是访问使用 【请注意 一般这个地址也是和模块这个是一样的】
 */
return array(
    array(
        'name' => "模块",
        'icon_class' => "layui-icon layui-icon-app",
        'tips' => "模块",
        'modular'=>"userManager",
        'child' => array(
            /*array(
                'name' => "店铺管理",
                'href' => Common::U('Collect_Collectshop/index'),
                'modular'=>"Collect_Collectshop/index",
                'function_node'=>array(//功能节点
                ),
            ),*/
            array(
                'name' => "币地址",
                'href' => Common::U('Collect_Qr/index'),
                'modular'=>"Collect_Qr/index",
                'function_node'=>array(//功能节点

                ),
            ),
            /*array(
                'name' => "二维码导入记录",
                'href' => Common::U('Collect_Qrimportlog/index'),
                'modular'=>"Collect_Qrimportlog/index",
                'function_node'=>array(//功能节点

                ),
            ),*/

            array(
                'name' => "订单管理",
                'href' => Common::U('Collect_Order/index'),
                'modular'=>"Collect_Order/index",
                'function_node'=>array(//功能节点

                ),
            ),

        ),
    ),

);
