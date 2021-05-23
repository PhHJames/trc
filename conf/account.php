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
            'private_key' => 'CA7ACFA729AAAD80693ACC806466C484AF3B3C5F314C5046DE4877EA0CC3ADE4' ,//私钥
            'address' => 'TDpWhgRe9JFRQvy4tXhNptHsZcLeQmto3e' , //币地址 这个是base58格式的
            'name' => '币地址-1' ,
            'remark' => 'Trx账号主要是用于归集或者其他地方转账消耗使用' ,
        ],
    ] ,//这个账号主要是为了创建 TRC20 需要消耗trx


    'SummaryAccount' => [
        [
            'address' => 'TEu1qsULtQpngCpjBiHMH5pw65258tpGJC' ,
            'name' => '平台汇总账户' ,
            'remark' => '平台汇总账号备注' ,
            'private_key' => '35D4E535D4D3409CBA6A8E83D75FDF34D3F95AE6F521BD5B69786DF82A481FE5' ,//私钥
        ],
    ],//汇总账号  ， 这个汇总账号主要是为了 ， 最终把钱打给商户的，


];