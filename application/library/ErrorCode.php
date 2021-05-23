<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2019/10/19 0019
 * Time: 21:42
 */

class ErrorCode{

    public static $commonError        = [API_CODE_NAME => -3, API_MSG_NAME => '参数错误'];

    public static $systemError        = [API_CODE_NAME =>  0 , API_MSG_NAME => '服务器错误或者没有相关信息'];

    public static $noAppid              = [API_CODE_NAME => 1000, API_MSG_NAME => '缺失appid'];

    public static $noSign               = [API_CODE_NAME => 1001, API_MSG_NAME => '缺失签名'];

    public static $merchError           = [API_CODE_NAME => 1003, API_MSG_NAME => '没有查询到商户或者是商户禁用或者关闭'];

    public static $signError            = [API_CODE_NAME => 1004, API_MSG_NAME => '签名错误'];

    public static $merchOrderNo            = [API_CODE_NAME => 1005, API_MSG_NAME => '缺失商户订单号'];

    public static $merchOrderFormatError            = [API_CODE_NAME => 1006, API_MSG_NAME => '商户订单号格式错误必须是 6-32位'];

    public static $noChannelError            = [API_CODE_NAME => 1007, API_MSG_NAME => '通道错误没有相关通道'];

    public static $channelCloseError            = [API_CODE_NAME => 1008, API_MSG_NAME => '通道已经关闭'];

    public static $merchFeeError            =   [API_CODE_NAME => 1009, API_MSG_NAME => '没有相关商户费率'];

    public static $payMoneyError            =   [API_CODE_NAME => 1100, API_MSG_NAME => '支付金额错误'];

    public static $payOrderError            =   [API_CODE_NAME => 1101, API_MSG_NAME => '下单失败！'];

    public static $payMerchOrderSameError            =   [API_CODE_NAME => 1102, API_MSG_NAME => '商户订单重复'];
}