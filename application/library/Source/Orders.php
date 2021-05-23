<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2020/8/8 0008
 * Time: 12:24
 */

class Source_Orders{
    /**
     * 订单成功之后 ， 处理统计数据写入
     * $sorder_sn 	string  系统订单号
     * @return	array
     */
    public function orderSuccessStatis( $order_sn ){
        $OrdersModel = new OrdersModel();
        $CollectShopModel = new CollectShopModel();
        $CollectUserModel = new CollectUserModel();
        $order = $OrdersModel->getInfo(['order_sn' => $order_sn]);
        if( empty($order) ){
            return false;
        }

        $user_id = $order['user_id'];
        $money = $order['money'];
        $money_fen = $money  ;
        $shop_id = $order['shop_id'];
        //修改店铺的当日统计数据
        $sql_shop = " update collect_shop set day_success_money = day_success_money + {$money_fen}  , day_success_order_num = day_success_order_num + 1  where shop_id = '{$shop_id}' ";
        $CollectShopModel->execSql($sql_shop);
        //修改码商的统计数据
        $sql_user = " update collect_user set day_success_money = day_success_money + {$money_fen}  where user_id = '{$user_id}' ";
        $CollectUserModel->execSql($sql_user);
    }

    /**
     * 订单失败之后 ， 处理统计数据写入
     * $sorder_sn 	string  系统订单号
     * @return	array
     */

    public function orderErrorStatis( $order_sn ){
        $OrdersModel = new OrdersModel();
        $CollectShopModel = new CollectShopModel();
        $order = $OrdersModel->getInfo(['order_sn' => $order_sn]);
        if( empty($order) ){
            return false;
        }
        $user_id = $order['user_id'];
        $money = $order['money'];
        $money_fen = $money  ;
        $shop_id = $order['shop_id'];
        //修改店铺的当日统计数据
        $sql_shop = " update collect_shop set day_error_money = day_error_money + {$money_fen} , day_error_order_num = day_error_order_num + 1  where shop_id = '{$shop_id}' ";
        $CollectShopModel->execSql($sql_shop);
    }

}