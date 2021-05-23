<?php
/**
 * Created by 不要复制我的代码.
 * User: Administrator
 * Date: 2021/1/20 0020
 * Time: 16:08
 */

//系统内置的账号配置

//这个文件非常的危险 千万不要泄露出去了

return [
    'trx' => [
        [
            'private_key' => '' ,//私钥
            'address' => '' , //币地址 这个是base58格式的
            'name' => '币地址-1' ,
            'remark' => 'Trx账号主要是用于归集或者其他地方转账消耗使用' ,
        ],
    ] ,//这个账号主要是为了创建 TRC20 需要消耗trx


    'SummaryAccount' => [
        [
            'address' => '' ,
            'name' => '平台汇总账户' ,
            'remark' => '平台汇总账号备注' ,
            'private_key' => '' ,//私钥
        ],
    ],//汇总账号  ， 这个汇总账号主要是为了 ， 最终把钱打给商户的，


];