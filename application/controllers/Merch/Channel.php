<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2019-03-26 14:57:33
 */
class Merch_ChannelController extends Merch_CenterController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        $Business = Common::ImportBusiness("Channel" ,"Merch" );
        $params['mid'] = $this->userInfo['mid'];
        $list = $Business->getMerchChannel($this->userInfo['mid']);
        if( $list ){
            foreach ($list as $key => $val ){
                //$list[$key]['fee'] =  Common::foramtNumber($val['fee'] * 100  , 3 );
            }
        }
        $res = array(
            'list'=> $list ,
        );
        $this->displayTemplate('channel/channel_index' , $res);
    }

}