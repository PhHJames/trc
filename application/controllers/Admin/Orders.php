<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-11-08 11:15:41
 */
class Admin_OrdersController extends Admin_BaseAuthController {
    public function init(){
        parent::init();
    }
    
       public function retryAction()
    {
        $params = $this->getParams();
        $order_sn =  isset($params['order_sn']) ? trim($params['order_sn']) : ""  ;
        $Source_Merch = new Source_Merch();
        $ordersModel = new OrdersModel();
        $Source_Merch->sendRsyncSupplement($order_sn , function($ret) use ($ordersModel , $order_sn) {
            if(strtolower($ret) == 'success' ){
                $updateOrder = array(
                    'rsync_status' => 2 ,
                    'rsync_message'=>$ret,
                );
            }else{
                $updateOrder = array(
                    'rsync_status' => 3 ,
                    'rsync_message'=>$ret,
                );
            }
            $ordersModel->updateData($updateOrder,array('order_sn' =>  $order_sn  ) );
        echo "$ret";

        });
    }
    public function indexAction(){
        if ($this->getRequest()->isXmlHttpRequest() OR $this->getRequest()->get("inajax") == 1) {
            //如果是ajax提交
            $this->getAjaxOrdersList();
            exit;
        }
        $res = array(
            'pageSize'=> 10 ,
            'statusData' => Enum::$orderStatus
        );

        $this->displayTemplate('orders/orders_index' , $res);
    }
    private function getAjaxOrdersList(){
        $params = $this->getParams();
        $pageSize =  isset($params['limit']) ? intval($params['limit']):10;
        $Business = Common::ImportBusiness("Orders" ,"Admin" );
        $data = $Business->getOrdersList($params,$pageSize);
        $list = $data['list'];
        if( !empty($list) ){
            $orderStatus = Enum::$orderStatus ;
            $rsyncOrderStatus = Enum::$rsyncOrderStatus ;
            foreach ($list as $key => $value) {
                $list[$key]['r_money'] = "￥". $value['r_money']  ;
                $list[$key]['money'] =  $value['money'] ;
                $list[$key]['real_money'] = $value['real_money']  ;
                $statusData = Enum::getVal($value['status'] ,$orderStatus )  ;
                $list[$key]['statusString'] =  isset($statusData['value']) ? $statusData['value'] : ""  ;
                $rsyncStatusData = Enum::getVal($value['rsync_status'] ,$rsyncOrderStatus )  ;
                $list[$key]['rsync_status'] =  isset($rsyncStatusData['value']) ? $rsyncStatusData['value'] : ""  ;
                $order_str = "系统单号:{$value['order_sn']}<br>";
                $order_str .="商户单号:{$value['merch_order_sn']}<br>";
                $order_str .="交易单号:{$value['trans_order_sn']}";
                $list[$key]['order_str'] = $order_str ;
                $list[$key]['complete_date'] = $value['complete_date']?$value['complete_date']:"----" ;
                $list[$key]['txid'] = $value['txid']?$value['txid']:"" ;
            }
        }
        $this->echoListJson(0,"OK" ,$data['total'] ,$list );
    }
    public function  previewAction(){
        $params = $this->getParams();
        $order_sn =  isset($params['order_sn']) ? trim($params['order_sn']) : ""  ;
        $OrdersModel = new OrdersModel();
        $info  = $OrdersModel->getInfo( array('order_sn' => $order_sn ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        $OrdersSupplementsModel = new OrdersSupplementsModel();
        $supplement = $OrdersSupplementsModel->getInfo( array('order_sn' => $info['order_sn'] ) );
        $orderStatus = Enum::$orderStatus;
        $orderData = Enum::getVal($info['status'] ,$orderStatus );
        $info['status_string'] = isset($orderData['value']) ? $orderData['value'] :"" ;

        $rsyncStatus = Enum::$rsyncOrderStatus;
        $rsyncOrderData = Enum::getVal($info['rsync_status'] ,$rsyncStatus );
        $info['rsync_status_string'] = isset($rsyncOrderData['value']) ? $rsyncOrderData['value'] :"" ;
        $ChannelModel = new ChannelModel();
        $channel = $ChannelModel->getInfo( array('channel' => $info['channel'] ) );
        $res = array(
            'info' => $info,
            'channel' =>$channel,
            'supplement'=>$supplement
        );
        $this->displayTemplate('orders/preview' , $res);
    }

    public function supplementAction(){
        if($this->getRequest()->isPost()){
            $this->doSupplementh();
            exit;
        }
        $params = $this->getParams();
        $id = isset($params['id']) ? intval($params['id']) : 0 ;
        $OrdersModel = new OrdersModel();
        $info  = $OrdersModel->getInfo( array('id' => $id  ) );
        if( empty($info )){
            $this->showError("温馨提示" , "暂无数据");
        }
        $Source_Exchange = new Source_Exchange();
        $rate = Tools::number_format($Source_Exchange->CnyUSDTHuansuan(2 , $info['channel']) , 3 ) ;
        $res = array(
            'info' => $info ,
            'rate' => $rate
        );
        $this->displayTemplate('orders/order_supplement' , $res);
    }
    private function doSupplementh(){
        try{
            $Business = Common::ImportBusiness("Orders" ,"Admin" );
            $Business->doSupplementh($_POST);
            $this->writeActionLog("补单操作" , "补单操作" );
            Common::EchoResult(1, "补单成功");
        }catch(Exception $e ){
            Common::EchoResult($e->getCode(), $e->getMessage());
        }
    }



}