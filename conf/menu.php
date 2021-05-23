<?php
/**
 * @Author: Awe
 * @Date:   2018-10-19 11:24:44
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-09 16:05:57
 * @desc 后台导航文件
 * 备注 其中 modular 这个节点主要是保存数据库的
 * href 这个节点主要是访问使用 【请注意 一般这个地址也是和模块这个是一样的】
*/
return array(
    array(
        'name' => "商户",
        'icon_class' => "layui-icon layui-icon-user",
        'tips' => "商户",
        'modular'=>"merchantManager",
        'child' => array(
            array(
                'name' => "商户管理",
                'href' => Common::U('Admin_Merchant/index'),
                'modular'=>"Admin_Merchant/index",
                'function_node'=>array(//功能节点
                    array('name' => "商户统计" , "href" =>Common::U("Admin_Merchant/statis" )  ,'modular'=>"Admin_Merchant/statis" ),
                    array('name' => "资金操作" , "href" =>Common::U("Admin_Merchant/money" )  ,'modular'=>"Admin_Merchant/money" ),
                    array('name' => "设置商户费率" , "href" =>Common::U("Admin_Merchant/settingFee" )  ,'modular'=>"Admin_Merchant/settingFee" ),
                    array('name' => "添加商户" , "href" =>Common::U("Admin_Merchant/add" )  ,'modular'=>"Admin_Merchant/add" ),
                    array('name' => "修改商户" , "href" =>Common::U("Admin_Merchant/edit" )  ,'modular'=>"Admin_Merchant/edit" ),
                    array('name' => "资金操作" , "href" =>Common::U("Admin_Merchant/money" )  ,'modular'=>"Admin_Merchant/money" ),
                    //array('name' => "查看码商绑定数据" , "href" =>Common::U("Admin_Merchant/bindCollectUser" )  ,'modular'=>"Admin_Merchant/bindCollectUser" ),
                    //array('name' => "绑定码商操作" , "href" =>Common::U("Admin_Merchant/doBindCollectUser" )  ,'modular'=>"Admin_Merchant/doBindCollectUser" ),
                    //array('name' => "删除绑定的码商操作" , "href" =>Common::U("Admin_Merchant/doDeleteBindCollectUser" )  ,'modular'=>"Admin_Merchant/doDeleteBindCollectUser" ),
                ),
            ),
            array(
                'name' => "商户币地址",
                'href' => Common::U('Admin_Merchantbank/index'),
                'modular'=>"Admin_Merchant/index",
                'function_node'=>array(//功能节点
                    array('name' => "操作币地址" , "href" =>Common::U("Admin_Merchantbank/auth" )  ,'modular'=>"Admin_Merchantbank/auth" ),
                ),
            ),
            array(
                'name' => "提现管理",
                'href' => Common::U('Admin_Merchwithdraw/index'),
                'modular'=>"Admin_Merchwithdraw/index",
                'function_node'=>array(//功能节点
                    array('name' => "审核提现" , "href" =>Common::U("Admin_Merchwithdraw/auth" )  ,'modular'=>"Admin_Merchwithdraw/auth" ),
                ),
            ),

            array(
                'name' => "资金记录",
                'href' => Common::U('Admin_Merchmoneylog/index'),
                'modular'=>"Admin_Merchmoneylog/index",
                'function_node'=>array(//功能节点
                ),
            ),
            /*array(
                'name' => "代理商", 
                'href' => "",
                'modular'=>"agentManager",
                'child' => array(
                    array(
                        'name' => "代理商列表", 
                        'href' => Common::U('Agent_Agent/index'),
                        'modular'=>"Agent_Agent/index",
                        'function_node'=>array(//功能节点
                            array('name' => "添加代理商" , "href" =>Common::U("Agent_Agent/add" )  ,'modular'=>"Agent_Agent/add" ),
                            array('name' => "修改代理商" , "href" =>Common::U("Agent_Agent/edit" )  ,'modular'=>"Agent_Agent/edit" ),
                        ),
                    ),
                    array(
                        'name' => "代理商统计", 
                        'href' => Common::U('Agent_statis/index'),
                        'modular'=>"Agent_statis/index",
                        'function_node'=>array(//功能节点
                            array('name' => "aaa" , "href" =>Common::U("Agent_Agent/cccc" )  ,'modular'=>"Agent_Agent/cccc" ),
                        ),
                    ),
                ),
                
            ),*/
        ),
    ),

    array(
        'name' => "代理商",
        'icon_class' => "layui-icon layui-icon-set",
        'tips' => "代理商",
        'modular'=>"agentManager",
        'child' => array(
            array(
                'name' => "代理商列表",
                'href' => Common::U('Admin_Agent/index'),
                'modular'=>"Admin_Agent/index",
                'function_node'=>array(//功能节点
                    array('name' => "添加代理商" , "href" =>Common::U("Admin_Agent/add" )  ,'modular'=>"Admin_Agent/add" ),
                    array('name' => "修改代理商" , "href" =>Common::U("Admin_Agent/edit" )  ,'modular'=>"Admin_Agent/edit" ),
                    array('name' => "查看代理商" , "href" =>Common::U("Admin_Agent/preview" )  ,'modular'=>"Admin_Agent/preview" ),
                    //array('name' => "资金操作" , "href" =>Common::U("Admin_Agent/money" )  ,'modular'=>"Admin_Agent/money" ),
                    array('name' => "设置代理商费率" , "href" =>Common::U("Admin_Agent/settingFee" )  ,'modular'=>"Admin_Agent/settingFee" ),
                    array('name' => "代理商统计" , "href" =>Common::U("Admin_Agent/statistics" )  ,'modular'=>"Admin_Agent/statistics" ),
                ),
            ),

            array(
                'name' => "币地址",
                'href' => Common::U('Admin_Agentbank/index'),
                'modular'=>"Admin_Agentbank/index",
                'function_node'=>array(//功能节点
                    array('name' => "操作币地址" , "href" =>Common::U("Admin_Agentbank/auth" )  ,'modular'=>"Admin_Agentbank/auth" ),
                ),
            ),
            array(
                'name' => "提现管理",
                'href' => Common::U('Admin_Agentwithdraw/index'),
                'modular'=>"Admin_Agentwithdraw/index",
                'function_node'=>array(//功能节点
                    array('name' => "审核提现" , "href" =>Common::U("Admin_Agentwithdraw/auth" )  ,'modular'=>"Admin_Agentwithdraw/auth" ),
                ),
            ),
            array(
                'name' => "资金记录",
                'href' => Common::U('Admin_Agentmoneylog/index'),
                'modular'=>"Admin_Agentmoneylog/index",
                'function_node'=>array(//功能节点
                ),
            ),


        ),

    ),
    array(
        'name' => "地址管理",
        'icon_class' => "layui-icon layui-icon-user",
        'tips' => "地址管理",
        'modular'=>"collectUserManager",
        'child' => array(
            /*array(
                'name' => "码商管理",
                'href' => Common::U('Admin_Collectuser/index'),
                'modular'=>"Admin_Collectuser/index",
                'function_node'=>array(//功能节点
                    array('name' => "添加码商" , "href" =>Common::U("Admin_Collectuser/add" )  ,'modular'=>"Admin_Collectuser/add" ),
                    array('name' => "修改码商" , "href" =>Common::U("Admin_Collectuser/edit" )  ,'modular'=>"Admin_Collectuser/edit" ),
                ),
            ),*/
            array(
                'name' => "币地址管理",
                'href' => Common::U('Admin_Collectqrcode/index'),
                'modular'=>"Admin_Collectqrcode/index",
                'function_node'=>array(//功能节点
                    array('name' => "添加币地址" , "href" =>Common::U("Admin_Collectqrcode/add" )  ,'modular'=>"Admin_Collectqrcode/add" ),
                    array('name' => "自动匹配" , "href" =>Common::U("Admin_Collectqrcode/auto_match" )  ,'modular'=>"Admin_Collectqrcode/auto_match" ),
                    array('name' => "币激活" , "href" =>Common::U("Admin_Collectqrcode/active" )  ,'modular'=>"Admin_Collectqrcode/active" ),
                    array('name' => "查看币地址" , "href" =>Common::U("Admin_Collectqrcode/preview" )  ,'modular'=>"Admin_Collectqrcode/preview" ),
                    //array('name' => "更新私钥" , "href" =>Common::U("Admin_Collectqrcode/updatePrivate" )  ,'modular'=>"Admin_Collectqrcode/updatePrivate" ),
                    array('name' => "获取余额" , "href" =>Common::U("Admin_Collectqrcode/get_balance" )  ,'modular'=>"Admin_Collectqrcode/get_balance" ),
                    array('name' => "批量转入TRX" , "href" =>Common::U("Admin_Collectqrcode/beatch_trans_trx" )  ,'modular'=>"Admin_Collectqrcode/beatch_trans_trx" ),
                    array('name' => "批量转出USDT" , "href" =>Common::U("Admin_Collectqrcode/beatch_trans_usdt" )  ,'modular'=>"Admin_Collectqrcode/beatch_trans_usdt" ),
                    array('name' => "批量转出TRX" , "href" =>Common::U("Admin_Collectqrcode/beatch_trans_out_trx" )  ,'modular'=>"Admin_Collectqrcode/beatch_trans_out_trx" ),
                ),
            ),

            array(
                'name' => "系统币地址",
                'href' => Common::U('Admin_Systemaddress/index'),
                'modular'=>"Admin_Systemaddress/index",
                'function_node'=>array(//功能节点
                    array('name' => "归集账号币转出" , "href" =>Common::U("Admin_Systemaddress/Transfer_out" )  ,'modular'=>"Admin_Systemaddress/Transfer_out" ),
                    array('name' => "TRX币转出" , "href" =>Common::U("Admin_Systemaddress/Transfer_trx_out" )  ,'modular'=>"Admin_Systemaddress/Transfer_trx_out" ),

                ),
            ),
            array(
                'name' => "转账记录",
                'href' => Common::U('Admin_Addresstranslog/index'),
                'modular'=>"Admin_Addresstranslog/index",
                'function_node'=>array(//功能节点
                ),
            ),
            /*array(
                'name' => "店铺管理",
                'href' => Common::U('Admin_Collectshop/index'),
                'modular'=>"Admin_Collectshop/index",
                'function_node'=>array(//功能节点
                    array('name' => "修改店铺" , "href" =>Common::U("Admin_Collectshop/edit" )  ,'modular'=>"Admin_Collectshop/edit" ),
                ),
            ),*/
        ),
    ),


    array(
        'name' => "订单",
        'icon_class' => "layui-icon layui-icon-set",
        'tips' => "订单",
        'modular'=>"orderManager",
        'child' => array(
            array(
                'name' => "订单管理",
                'href' => Common::U('Admin_Orders/index'),
                'modular'=>"Admin_Orders/index",
                'function_node'=>array(//功能节点
                    array('name' => "查看订单" , "href" =>Common::U("Admin_Orders/preview" )  ,'modular'=>"Admin_Orders/preview" ),
                    array('name' => "补单" , "href" =>Common::U("Admin_Orders/supplement" )  ,'modular'=>"Admin_Orders/supplement" ),
                ),
            ),

            array(
                'name' => "到账记录",
                'href' => Common::U('Admin_Orderspaymentlog/index'),
                'modular'=>"Admin_Orderspaymentlog/index",
                'function_node'=>array(//功能节点
                ),
            ),
        ),
    ),

    array(
        'name' => "数据统计",
        'icon_class' => "layui-icon layui-icon-set",
        'tips' => "数据统计",
        'modular'=>"statisManager",
        'child' => array(
            array(
                'name' => "平台统计",
                'href' => Common::U('Admin_Statis/index'),
                'modular'=>"Admin_Statis/index",
                'function_node'=>array(//功能节点
                ),
            ),
        ),
    ),
    array(
        'name' => "系统管理",
        'icon_class' => "layui-icon layui-icon-set fa ",
        'tips' => "通道",
        'modular'=>"channelManager",
        'child' => array(
            array(
                'name' => "通道管理",
                'href' => Common::U('Admin_Channel/index'),
                'modular'=>"Admin_Channel/index",
                'function_node'=>array(//功能节点
                    array('name' => "修改通道" , "href" =>Common::U("Admin_Channel/edit" )  ,'modular'=>"Admin_Channel/edit" ),
                ),
            ),
        ),
    ),

    array(
        'name' => "管理员",
        'icon_class' => "layui-icon layui-icon-user",
        'tips' => "设置",
        'modular'=>"settingManager",
        'child' => array(
            array(
                'name' => "后台用户", 
                'href' => Common::U("Admin_Admin/user" ),
                'modular'=>"Admin_Admin/user",
                'function_node'=>array(//功能节点
                    array('name' => "添加后台用户" , "href" =>Common::U("Admin_Admin/userAdd" )  ,'modular'=>"Admin_Admin/userAdd" ),
                    array('name' => "修改后台用户" , "href" =>Common::U("Admin_Admin/userEdit" ) ,'modular'=>"Admin_Admin/userEdit" ),
                    array('name' => "后台用户删除" , "href" =>Common::U("Admin_Admin/userDel" ) ,'modular'=>"Admin_Admin/userDel" ),
                ),
            ),
            array(
                'name' => "角色管理", 
                'href' => Common::U("Admin_Admin/role" ),
                'modular'=>"Admin_Admin/role",
                'function_node' => array(
                    array('name' => "添加角色" , "href" =>Common::U("Admin_Admin/addRole" )  ,'modular'=>"Admin_Admin/addRole"),
                    array('name' => "修改角色" , "href" =>Common::U("Admin_Admin/editRole" )   ,'modular'=>"Admin_Admin/editRole"),
                ),
            ),
            array(
                'name' => "清除缓存", 
                'href' => Common::U('Admin_cache/index'),
                'modular'=>"Admin_cache/index",
                'function_node'=>array(//功能节点
                   
                ),
            ),
            array(
                'name' => "系统设置", 
                'href' => Common::U('Admin_System_Config/index'),
                'modular'=>"Admin_System_Config/index",
                'function_node'=>array(//功能节点
                   array('name' => "添加环境变量" , "href" =>Common::U("Admin_System_Config/add" )  ,'modular'=>"Admin_System_Config/add"),
                    array('name' => "修改环境变量" , "href" =>Common::U("Admin_System_Config/update" )  ,'modular'=>"Admin_System_Config/update"),
                    array('name' => "删除环境变量" , "href" =>Common::U("Admin_System_Config/delete" )  ,'modular'=>"Admin_System_Config/delete"),
                ),
            ),

            array(
                'name' => "后台操作日志",
                'href' => Common::U('Admin_Log/index'),
                'modular'=>"Admin_Log/index",
                'function_node'=>array(//功能节点
                ),
            ),

            array(
                'name' => "平台日志",
                'href' => Common::U('Admin_Platlog/index'),
                'modular'=>"Admin_Platlog/index",
                'function_node'=>array(//功能节点
                ),
            ),


        ),
    ),    
);
