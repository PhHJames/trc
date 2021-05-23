<?php
/**
 * @Author: wangjian
 * @Date:   2017-08-28 12:27:35
 * @Last 不要复制我的代码不然后果自负
 * @Last Modified time: 2018-10-11 12:21:28
 * @枚举 数据定义--------(只是定义下 常用的 固定 数据 放在这里)
 */
class Enum{
    public static $userStatus = array(
        1 => array('value' => "正常" , 'color' => ""  ) ,
        2 =>array('value' => "封禁" , 'color' => "red"  )
    );

    public static $orderStatus = array(
        1 => array('value' => "待付款" , 'color' => ""  ) ,
        2 =>array('value' => "付款成功" , 'color' => "red"  ),
        3 =>array('value' => "付款失败" , 'color' => "red"  )
    );

    public static $rsyncOrderStatus = array(
        1 => array('value' => "未知" , 'color' => ""  ) ,
        2 =>array('value' => "成功" , 'color' => ""  ),
        3 =>array('value' => "失败" , 'color' => ""  )
    );


    public static $merchBankStatus = array(
        1 => array('value' => "待审核" , 'color' => ""  ) ,
        2 =>array('value' => "审核通过" , 'color' => ""  ),
        3 =>array('value' => "拒绝" , 'color' => ""  )
    );

    public static $merchWithDrawStatus = array(
        1 => array('value' => "审核中" , 'color' => ""  ) ,
        2 =>array('value' => "审核驳回" , 'color' => ""  ),
        3 =>array('value' => "审核成功" , 'color' => ""  )
    );
    public static $merchWithComeFrom = array(
        0 => array('value' => "商户手动发起" , 'color' => ""  ) ,
        1 =>array('value' => "API接口发起" , 'color' => ""  ),
    );
    public static $merchMoneyLogType = array(
        1 => array('value' => "商户申请提现" , 'color' => ""  ) ,
        2 => array('value' => "提现审核驳回" , 'color' => ""  ) ,
        3 =>array('value' => "订单付款确认" , 'color' => ""  ),
        4 =>array('value' => "平台操作" , 'color' => ""  ) ,
        5 =>array('value' => "系统补单" , 'color' => ""  ) ,
    );

    //补单状态
    public static $OrderSupplementStatus = array(
        1 => array('value' => "是" , 'color' => ""  ) ,
        2 => array('value' => "否" , 'color' => ""  ) ,
    );

    //二维码状态
    public static $QrStatus = array(
        -2 => array('value' => "审核未通过" , 'color' => ""  ) ,
        -1 => array('value' => "禁用或者删除" , 'color' => ""  ) ,
        0 => array('value' => "待审核" , 'color' => ""  ) ,
        1 => array('value' => "正常" , 'color' => ""  ) ,
        2 => array('value' => "锁定" , 'color' => ""  ) ,
        3 => array('value' => "关闭" , 'color' => ""  ) ,
    );

    //导入二维码状态
    public static $ImportQrStatus = array(
        1 => array('value' => "成功" , 'color' => ""  ) ,
        2 => array('value' => "处理中" , 'color' => ""  ) ,
        3 => array('value' => "失败" , 'color' => ""  ) ,
    );

    public static $agentBankStatus = array(
        1 => array('value' => "待审核" , 'color' => ""  ) ,
        2 =>array('value' => "审核通过" , 'color' => ""  ),
        3 =>array('value' => "拒绝" , 'color' => ""  )
    );
    public static $agentWithDrawStatus = array(
        1 => array('value' => "审核中" , 'color' => ""  ) ,
        2 =>array('value' => "审核失败" , 'color' => ""  ),
        3 =>array('value' => "审核成功" , 'color' => ""  )
    );
    public static $agentMoneyLogType = array(
        1 => array('value' => "代理商申请提现" , 'color' => ""  ) ,
        2 => array('value' => "提现审核驳回" , 'color' => ""  ) ,
        3 =>array('value' => "订单付款确认" , 'color' => ""  ),
        4 =>array('value' => "平台操作" , 'color' => ""  ) ,
        5 =>array('value' => "系统补单" , 'color' => ""  ) ,
    );
    #########################
    //根据key 获取value值
    public static function getVal($key = ''  , $data ){
        return  isset($data[$key]) ? $data[$key] : '' ;
    }
}
