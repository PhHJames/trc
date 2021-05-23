<?php
/**
 * @Author: Awe
 * @Date:   2018-10-19 11:24:44
 * @Last Modified by:   Awe
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
            array(
                'name' => "数据统计",
                'href' => Common::U('Agent_Statistics/index'),
                'modular'=>"Merch_Statistics/index",
                'function_node'=>array(//功能节点

                ),
            ),
            array(
                'name' => "商户管理",
                'href' => Common::U('Agent_Merch/index'),
                'modular'=>"Agent_Merch/index",
                'function_node'=>array(//功能节点

                ),
            ),
            array(
                'name' => "订单管理",
                'href' => Common::U('Agent_Order/index'),
                'modular'=>"Agent_Order/index",
                'function_node'=>array(//功能节点

                ),
            ),
            array(
                'name' => "通道管理",
                'href' => Common::U('Agent_Channel/index'),
                'modular'=>"Agent_Channel/index",
                'function_node'=>array(//功能节点
                ),
            ),

            array(
                'name' => "币地址管理",
                'href' => Common::U('Agent_Bank/index'),
                'modular'=>"Agent_Bank/index",
                'function_node'=>array(//功能节点
                ),
            ),

            array(
                'name' => "提现管理",
                'href' => Common::U('Agent_Withdraw/index'),
                'modular'=>"Agent_Withdraw/index",
                'function_node'=>array(//功能节点
                ),
            ),
            /*array(
                'name' => "商户提现管理",
                'href' => Common::U('Agent_Merchwithdraw/index'),
                'modular'=>"Agent_Merchwithdraw/index",
                'function_node'=>array(//功能节点
                ),
            ),*/
            array(
                'name' => "资金记录",
                'href' => Common::U('Agent_Moneylog/index'),
                'modular'=>"Agent_Moneylog/index",
                'function_node'=>array(//功能节点
                ),
            ),
        ),
    ),

);
