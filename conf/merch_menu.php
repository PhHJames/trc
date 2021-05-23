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
                'name' => "码商管理",
                'href' => Common::U('Merch_Collectuser/index'),
                'modular'=>"Merch_Collectuser/index",
                'function_node'=>array(//功能节点
                ),
            ),*/

            array(
                'name' => "通道管理",
                'href' => Common::U('Merch_Channel/index'),
                'modular'=>"Merch_Channel/index",
                'function_node'=>array(//功能节点
                ),
            ),





            array(
                'name' => "收款地址",
                'href' => Common::U('Merch_Bank/index'),
                'modular'=>"Merch_Bank/index",
                'function_node'=>array(//功能节点
                ),
            ),

            array(
                'name' => "资金提现管理",
                'href' => Common::U('Merch_Withdraw/index'),
                'modular'=>"Merch_Withdraw/index",
                'function_node'=>array(//功能节点
                ),
            ),

            array(
                'name' => "订单管理",
                'href' => Common::U('Merch_Order/index'),
                'modular'=>"Merch_Order/index",
                'function_node'=>array(//功能节点
                ),
            ),
            array(
                'name' => "资金记录",
                'href' => Common::U('Merch_Moneylog/index'),
                'modular'=>"Merch_Moneylog/index",
                'function_node'=>array(//功能节点
                ),
            )
        ),
    ),

);
