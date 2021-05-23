<?php
/**
 * @Author: Awe
 * @Date:   2018-10-20 22:30:23
 * @Last Modified by:   Awe
 * @Last Modified time: 2019-03-26 14:57:33
 */
class Agent_ChannelController extends Agent_CenterController {
    public function init(){
        parent::init();
    }
    public function indexAction(){
        $Business = Common::ImportBusiness("Channel" ,"Agent" );
        $params['agent_id'] = $this->userInfo['agent_id'];
        $list = $Business->getAgentChannel($this->userInfo['agent_id']);
        if( $list ){
            foreach ($list as $key => $val ){
                //$list[$key]['fee'] =  COmmon::foramtNumber($val['fee'] * 100  , 3 );
            }
        }
        $res = array(
            'list'=> $list ,
        );
        $this->displayTemplate('channel/channel_index' , $res);
    }

}