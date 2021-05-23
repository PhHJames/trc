/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50728
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50728
File Encoding         : 65001

Date: 2021-03-03 21:31:44
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `address_trans_log`
-- ----------------------------
DROP TABLE IF EXISTS `address_trans_log`;
CREATE TABLE `address_trans_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT 'from地址',
  `to` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT 'to地址',
  `amount` decimal(20,8) unsigned DEFAULT '0.00000000' COMMENT '数额',
  `remark` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '备注',
  `create_date` datetime DEFAULT NULL COMMENT '创建日期',
  `txid` varchar(200) CHARACTER SET utf8 DEFAULT NULL COMMENT '交易id',
  `channel` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '通证',
  PRIMARY KEY (`id`),
  UNIQUE KEY `txid` (`txid`),
  KEY `from` (`from`),
  KEY `to` (`to`)
) ENGINE=InnoDB AUTO_INCREMENT=1204 DEFAULT CHARSET=utf8mb4 COMMENT='地址交易记录';

-- ----------------------------
-- Records of address_trans_log
-- ----------------------------

-- ----------------------------
-- Table structure for `admin_log`
-- ----------------------------
DROP TABLE IF EXISTS `admin_log`;
CREATE TABLE `admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `member_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行会员id',
  `username` char(30) NOT NULL DEFAULT '' COMMENT '用户名',
  `ip` char(30) NOT NULL DEFAULT '' COMMENT '执行行为者ip',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '行为名称',
  `describe` varchar(300) NOT NULL COMMENT '描述',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '执行的URL',
  `create_time` datetime NOT NULL COMMENT '执行行为的时间',
  `request` text COMMENT '请求参数',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=425 DEFAULT CHARSET=utf8 COMMENT='后台的行为日志表';

-- ----------------------------
-- Records of admin_log
-- ----------------------------

-- ----------------------------
-- Table structure for `admin_privileges`
-- ----------------------------
DROP TABLE IF EXISTS `admin_privileges`;
CREATE TABLE `admin_privileges` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) unsigned DEFAULT '0' COMMENT '角色Id',
  `module_url` varchar(300) DEFAULT '0' COMMENT '模块url',
  `addtime` datetime DEFAULT NULL COMMENT '添加日期',
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=329 DEFAULT CHARSET=utf8 COMMENT='权限组对应的权限';

-- ----------------------------
-- Records of admin_privileges
-- ----------------------------

-- ----------------------------
-- Table structure for `admin_role`
-- ----------------------------
DROP TABLE IF EXISTS `admin_role`;
CREATE TABLE `admin_role` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '角色的id',
  `role_name` varchar(30) DEFAULT NULL COMMENT '权限组名称',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '1:开启2：关闭',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='总后台角色';

-- ----------------------------
-- Records of admin_role
-- ----------------------------

-- ----------------------------
-- Table structure for `admin_user`
-- ----------------------------
DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户的id',
  `username` varchar(50) DEFAULT NULL COMMENT '后台管理的用户名',
  `nick` varchar(50) DEFAULT NULL COMMENT '昵称',
  `passwd` char(32) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态',
  `addtime` datetime DEFAULT NULL COMMENT '添加日期',
  `login_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `super` tinyint(1) unsigned DEFAULT '2' COMMENT '是否是管理员 1：是 2 ：不是',
  `role_ids` varchar(100) DEFAULT NULL COMMENT '角色id 逗号分隔',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='总后台用户';

-- ----------------------------
-- Records of admin_user
-- ----------------------------
INSERT INTO admin_user VALUES ('1', 'uadmin', '那一夜', 'e10adc3949ba59abbe56e057f20f883e', '1', '2019-10-14 19:46:48', '1', '1', '');

-- ----------------------------
-- Table structure for `admin_user_special_power`
-- ----------------------------
DROP TABLE IF EXISTS `admin_user_special_power`;
CREATE TABLE `admin_user_special_power` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT '0' COMMENT '用户的id',
  `modules_str` varchar(1000) DEFAULT NULL COMMENT '特殊权限的url模块地址,json存储',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_site_id` (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='总后台=用户的特殊权限';

-- ----------------------------
-- Records of admin_user_special_power
-- ----------------------------

-- ----------------------------
-- Table structure for `agent`
-- ----------------------------
DROP TABLE IF EXISTS `agent`;
CREATE TABLE `agent` (
  `agent_id` int(10) NOT NULL AUTO_INCREMENT,
  `account` varchar(64) DEFAULT NULL COMMENT '代理商账号',
  `name` varchar(30) DEFAULT NULL COMMENT '代理商名称',
  `money` decimal(20,8) unsigned DEFAULT '0.00000000' COMMENT '代理商金额 精确到分',
  `freez_money` decimal(20,8) DEFAULT '0.00000000' COMMENT '冻结金额',
  `password` char(32) DEFAULT NULL COMMENT '密码',
  `phone` varchar(20) DEFAULT NULL COMMENT '代理商的手机号码',
  `status` tinyint(1) DEFAULT '1' COMMENT '数据状态:''-1''=>''禁用'',''0''=>''待审核'',''1''=>''正常''',
  `update_time` datetime DEFAULT NULL COMMENT '更新日期',
  `create_time` datetime DEFAULT NULL COMMENT '创建日期',
  `fixed_poundage` decimal(20,8) unsigned DEFAULT '1.00000000' COMMENT '代理商提现的固定费率',
  `min_withdraw_money` decimal(20,8) unsigned DEFAULT '100.00000000' COMMENT '最低提现金额',
  `max_address_count` int(10) unsigned DEFAULT '10' COMMENT '最大添加的币数量限制',
  PRIMARY KEY (`agent_id`),
  UNIQUE KEY `account` (`account`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='代理商数据表';

-- ----------------------------
-- Records of agent
-- ----------------------------

-- ----------------------------
-- Table structure for `agent_action_log`
-- ----------------------------
DROP TABLE IF EXISTS `agent_action_log`;
CREATE TABLE `agent_action_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `agent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '代理商的id',
  `username` char(30) NOT NULL DEFAULT '' COMMENT '用户名',
  `ip` char(30) NOT NULL DEFAULT '' COMMENT '执行行为者ip',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '执行的URL',
  `create_time` datetime NOT NULL COMMENT '执行行为的时间',
  `request` text COMMENT '请求参数',
  `method` varchar(30) DEFAULT NULL COMMENT '请求方式',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `agent_id` (`agent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=446 DEFAULT CHARSET=utf8 COMMENT='代理商行为日志数据表';

-- ----------------------------
-- Records of agent_action_log
-- ----------------------------

-- ----------------------------
-- Table structure for `agent_bank`
-- ----------------------------
DROP TABLE IF EXISTS `agent_bank`;
CREATE TABLE `agent_bank` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `agent_id` int(10) unsigned DEFAULT '0' COMMENT '代理商的id',
  `address` varchar(100) DEFAULT NULL COMMENT '币地址',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '1:待审核2:审核通过3:拒绝',
  `create_time` datetime DEFAULT NULL COMMENT '添加时间',
  `remark` varchar(200) DEFAULT NULL COMMENT '备注',
  `channel` varchar(100) DEFAULT NULL COMMENT '币类型',
  `name` varchar(30) DEFAULT NULL COMMENT '名称',
  PRIMARY KEY (`id`),
  KEY `agent_id` (`agent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='商户的币地址管理';

-- ----------------------------
-- Records of agent_bank
-- ----------------------------

-- ----------------------------
-- Table structure for `agent_channel_fee`
-- ----------------------------
DROP TABLE IF EXISTS `agent_channel_fee`;
CREATE TABLE `agent_channel_fee` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `channel` varchar(20) DEFAULT NULL COMMENT '关联通道表channel的channel字段',
  `agent_id` bigint(20) DEFAULT '0' COMMENT '商户ID',
  `fee` decimal(10,4) DEFAULT NULL COMMENT '通道费率, 按照千为单位',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '2' COMMENT '通道状态1：开启 2：关闭',
  PRIMARY KEY (`id`),
  UNIQUE KEY `c_mid` (`channel`,`agent_id`),
  KEY `channel` (`channel`),
  KEY `agent_id` (`agent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='代理商通道费率表';

-- ----------------------------
-- Records of agent_channel_fee
-- ----------------------------

-- ----------------------------
-- Table structure for `agent_money_log`
-- ----------------------------
DROP TABLE IF EXISTS `agent_money_log`;
CREATE TABLE `agent_money_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `agent_id` bigint(20) unsigned DEFAULT '0' COMMENT '商户id',
  `money` decimal(20,8) NOT NULL DEFAULT '0.00000000' COMMENT '操作金额 分为单位 可以为负数',
  `account_money` decimal(20,8) unsigned DEFAULT '0.00000000' COMMENT '变动前账户余额',
  `now_money` decimal(20,8) unsigned DEFAULT '0.00000000' COMMENT '现在的钱',
  `remark` varchar(300) DEFAULT NULL COMMENT '操作备注',
  `type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '类型1.商户提现,2.提现审核驳回,3:订单付款确认4.平台操作5系统补单',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `mid` (`agent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='代理商资金流水表';

-- ----------------------------
-- Records of agent_money_log
-- ----------------------------

-- ----------------------------
-- Table structure for `agent_withdraw`
-- ----------------------------
DROP TABLE IF EXISTS `agent_withdraw`;
CREATE TABLE `agent_withdraw` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `agent_id` bigint(20) NOT NULL COMMENT '商户ID',
  `money` decimal(20,8) unsigned DEFAULT '0.00000000' COMMENT '提现金额',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `percent_poundage` decimal(20,8) DEFAULT '0.00000000' COMMENT '百分比手续费',
  `fixed_poundage` decimal(20,8) unsigned NOT NULL DEFAULT '0.00000000' COMMENT '固定手续费 精确到分为单位，按照每一笔进行收',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '数据状态 ''1''=>''审核中'',''2''=>''审核失败'',''3''=>''成功''',
  `order_no` varchar(64) DEFAULT NULL COMMENT '提现单号',
  `remark` varchar(300) DEFAULT NULL COMMENT '平台备注',
  `bank_id` int(10) unsigned DEFAULT '0' COMMENT '关联银行卡',
  `txid` varchar(200) DEFAULT NULL COMMENT '交易id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no` (`order_no`) USING BTREE,
  KEY `mid` (`agent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=482 DEFAULT CHARSET=utf8 COMMENT='代理提现表';

-- ----------------------------
-- Records of agent_withdraw
-- ----------------------------

-- ----------------------------
-- Table structure for `channel`
-- ----------------------------
DROP TABLE IF EXISTS `channel`;
CREATE TABLE `channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `channel` varchar(20) DEFAULT NULL COMMENT '通道类型字符串',
  `name` varchar(30) DEFAULT NULL COMMENT '通道名称',
  `alias` varchar(20) DEFAULT NULL COMMENT '通道别名',
  `status` tinyint(1) DEFAULT '1' COMMENT '1:开启2:关闭',
  `remark` varchar(300) DEFAULT NULL COMMENT '通道备注',
  `fee` decimal(10,4) DEFAULT '0.0000' COMMENT '通道费率',
  `update_time` datetime DEFAULT NULL COMMENT '最后修改日期',
  `min_money` int(10) unsigned DEFAULT '0' COMMENT '最低金额',
  `max_money` int(10) unsigned DEFAULT '0' COMMENT '最大金额',
  `extra` text COMMENT '其他配置json信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `channel` (`channel`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='通道表';

-- ----------------------------
-- Records of channel
-- ----------------------------
INSERT INTO channel VALUES ('1', 'USDT_TRC20', 'USDT_TRC20', 'USDT_TRC20', '1', 'USDT_TRC20', '0.0030', '2021-02-07 02:14:22', '0', '2000000', '{\"mix_money\":{\"field\":\"mix_money\",\"name\":\"固定金额配置，一行一个，单位是元为单位\",\"type\":\"textarea\",\"value\":\"100\\r\\n200\\r\\n300\"}}');

-- ----------------------------
-- Table structure for `collect_qrcode`
-- ----------------------------
DROP TABLE IF EXISTS `collect_qrcode`;
CREATE TABLE `collect_qrcode` (
  `qr_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `user_id` int(11) DEFAULT '0' COMMENT '码商的id',
  `content` varchar(255) DEFAULT NULL COMMENT '二维码解析之后的原始数据这个是币地址 base58 格式的',
  `path` varchar(255) DEFAULT NULL COMMENT '用户上传的二维码',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '数据状态: -2 审核未通过  -1 禁用或者删除   0 待审核  1 正常  2 锁定 3：关闭',
  `match_index` int(10) unsigned DEFAULT '5000' COMMENT '二维码匹配指数',
  `chain_name` varchar(100) DEFAULT NULL COMMENT '链名称,这个一般和通道名称对应起来',
  `success_money` decimal(20,3) unsigned DEFAULT '0.000' COMMENT '成功收款金额',
  `error_money` decimal(20,3) unsigned DEFAULT '0.000' COMMENT '失败金额',
  `auto_match` tinyint(1) unsigned DEFAULT '1' COMMENT '是否开启匹配',
  `privatekey` varchar(1000) DEFAULT NULL COMMENT '私钥',
  `remark` varchar(100) DEFAULT NULL COMMENT '备注',
  `publicKey` varchar(1000) DEFAULT NULL COMMENT '公钥',
  `txid` varchar(500) DEFAULT NULL COMMENT 'txid创建账号广播交易的id',
  `active` tinyint(1) unsigned DEFAULT '0' COMMENT '是否激活',
  `trx` decimal(20,8) NOT NULL DEFAULT '0.00000000' COMMENT 'trx数',
  `usdt` decimal(20,8) NOT NULL DEFAULT '0.00000000' COMMENT 'usdt数',
  PRIMARY KEY (`qr_id`),
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=437 DEFAULT CHARSET=utf8 COMMENT='二维码表';

-- ----------------------------
-- Records of collect_qrcode
-- ----------------------------

-- ----------------------------
-- Table structure for `collect_user`
-- ----------------------------
DROP TABLE IF EXISTS `collect_user`;
CREATE TABLE `collect_user` (
  `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `account` varchar(64) DEFAULT NULL COMMENT '码商账号',
  `name` varchar(30) DEFAULT NULL COMMENT '码商名称',
  `password` char(32) DEFAULT NULL COMMENT '码商登陆密码',
  `phone` varchar(20) DEFAULT '' COMMENT '码商手机号',
  `status` tinyint(2) DEFAULT '1' COMMENT '数据状态:''-1''=>''禁用'',''0''=>''待审核'',''1''=>''正常''',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `mid` int(10) NOT NULL DEFAULT '0' COMMENT '商户的id',
  `auto_match` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否开启自动匹配',
  `day_limit_money` int(10) NOT NULL DEFAULT '0' COMMENT '当日限额分',
  `day_success_money` int(10) NOT NULL DEFAULT '0' COMMENT '当日成功订单金额',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `account` (`account`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `phone` (`phone`) USING BTREE,
  KEY `mid` (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='码商表';

-- ----------------------------
-- Records of collect_user
-- ----------------------------

-- ----------------------------
-- Table structure for `log`
-- ----------------------------
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) DEFAULT NULL COMMENT '日志类型',
  `level` varchar(20) DEFAULT NULL COMMENT '日志级别',
  `message` text COMMENT '具体的信息',
  `create_date` datetime DEFAULT NULL COMMENT '创建日期',
  PRIMARY KEY (`id`),
  KEY `type` (`type`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1390 DEFAULT CHARSET=utf8 COMMENT='平台的日志信息存储';

-- ----------------------------
-- Records of log
-- ----------------------------

-- ----------------------------
-- Table structure for `merchants`
-- ----------------------------
DROP TABLE IF EXISTS `merchants`;
CREATE TABLE `merchants` (
  `mid` bigint(20) UNSIGNED NOT NULL COMMENT '编号',
  `account` varchar(64) NOT NULL COMMENT '商户账号',
  `name` varchar(30) DEFAULT NULL COMMENT '商户名称',
  `money` decimal(20,8) UNSIGNED DEFAULT '0.00000000' COMMENT '商户金额 ',
  `freez_money` decimal(20,8) UNSIGNED DEFAULT '0.00000000' COMMENT '冻结金额',
  `password` char(32) DEFAULT NULL COMMENT '商户登陆密码',
  `bank_card` varchar(30) DEFAULT NULL COMMENT '商户银行卡号',
  `bank_type` int(11) DEFAULT '0' COMMENT '银行类型',
  `phone` varchar(20) DEFAULT '' COMMENT '商户手机号',
  `appid` varchar(32) DEFAULT NULL COMMENT 'appID',
  `appsecret` varchar(128) DEFAULT NULL COMMENT 'appSecret',
  `status` tinyint(2) DEFAULT '1' COMMENT '数据状态:''-1''=>''禁用'',''0''=>''待审核'',''1''=>''正常''',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `white_list_ip` varchar(255) DEFAULT NULL COMMENT 'ip白名单',
  `notify_url` varchar(300) DEFAULT NULL COMMENT '异步回调地址',
  `supplement_url` varchar(300) DEFAULT NULL COMMENT '补单的地址商户那边',
  `chain_name` varchar(100) DEFAULT NULL COMMENT '链名称',
  `c_url` varchar(200) DEFAULT NULL COMMENT '币地址',
  `min_withdraw_money` decimal(20,8) DEFAULT '100.00000000' COMMENT '最低提现金额',
  `fixed_poundage` decimal(20,8) UNSIGNED DEFAULT '1.00000000' COMMENT '固定手续费',
  `agent_id` int(10) UNSIGNED DEFAULT '0' COMMENT '代理商id',
  `num_place_order` int(10) NOT NULL DEFAULT '60' COMMENT '默认定义1分钟可以下多少个订单'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商户表';

-- ----------------------------
-- Records of merchants
-- ----------------------------

-- ----------------------------
-- Table structure for `merchants_action_log`
-- ----------------------------
DROP TABLE IF EXISTS `merchants_action_log`;
CREATE TABLE `merchants_action_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `mid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '代理商的id',
  `username` char(30) NOT NULL DEFAULT '' COMMENT '用户名',
  `ip` char(30) NOT NULL DEFAULT '' COMMENT '执行行为者ip',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '执行的URL',
  `create_time` datetime NOT NULL COMMENT '执行行为的时间',
  `request` text COMMENT '请求参数',
  `method` varchar(30) DEFAULT NULL COMMENT '请求方式',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `mid` (`mid`) USING BTREE,
  KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商户的行为日志数据表';

-- ----------------------------
-- Records of merchants_action_log
-- ----------------------------

-- ----------------------------
-- Table structure for `merchants_bank`
-- ----------------------------
DROP TABLE IF EXISTS `merchants_bank`;
CREATE TABLE `merchants_bank` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mid` int(10) unsigned DEFAULT '0' COMMENT '商户id',
  `address` varchar(100) DEFAULT NULL COMMENT '币地址',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '1:待审核2:审核通过3:拒绝',
  `create_time` datetime DEFAULT NULL COMMENT '添加时间',
  `remark` varchar(200) DEFAULT NULL COMMENT '备注',
  `channel` varchar(100) DEFAULT NULL COMMENT '币类型',
  `name` varchar(30) DEFAULT NULL COMMENT '名称',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='商户的币地址管理';

-- ----------------------------
-- Records of merchants_bank
-- ----------------------------

-- ----------------------------
-- Table structure for `merchants_channel_fee`
-- ----------------------------
DROP TABLE IF EXISTS `merchants_channel_fee`;
CREATE TABLE `merchants_channel_fee` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `channel` varchar(20) DEFAULT NULL COMMENT '关联通道表channel的channel字段',
  `mid` bigint(20) DEFAULT '0' COMMENT '商户ID',
  `fee` decimal(10,4) DEFAULT NULL COMMENT '通道费率, 按照千为单位',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '2' COMMENT '通道状态1：开启 2：关闭',
  `address` varchar(500) DEFAULT NULL COMMENT '商户的币地址',
  PRIMARY KEY (`id`),
  UNIQUE KEY `c_mid` (`channel`,`mid`),
  KEY `mid` (`mid`),
  KEY `channel` (`channel`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8 COMMENT='商户通道费率表';

-- ----------------------------
-- Records of merchants_channel_fee
-- ----------------------------

-- ----------------------------
-- Table structure for `merchants_money_log`
-- ----------------------------
DROP TABLE IF EXISTS `merchants_money_log`;
CREATE TABLE `merchants_money_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `mid` bigint(20) NOT NULL COMMENT '关联商户',
  `money` decimal(20,8) DEFAULT '0.00000000' COMMENT '操作金额',
  `account_money` decimal(20,8) DEFAULT '0.00000000' COMMENT '变动前账户余额',
  `now_money` decimal(20,8) DEFAULT '0.00000000' COMMENT '变动后金额',
  `remark` text COMMENT '操作备注',
  `type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '类型 1.商户提现,2.订单付款,3.平台操作',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `mer_id` (`mid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COMMENT='商户资金流水表';

-- ----------------------------
-- Records of merchants_money_log
-- ----------------------------

-- ----------------------------
-- Table structure for `merchants_withdraw`
-- ----------------------------
DROP TABLE IF EXISTS `merchants_withdraw`;
CREATE TABLE `merchants_withdraw` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `mid` bigint(20) NOT NULL COMMENT '商户ID',
  `money` decimal(20,8) unsigned DEFAULT '0.00000000' COMMENT '提现金额',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `percent_poundage` decimal(20,8) DEFAULT '0.00000000' COMMENT '百分比手续费',
  `fixed_poundage` decimal(20,8) unsigned NOT NULL DEFAULT '0.00000000' COMMENT '固定手续费 精确到分为单位，按照每一笔进行收',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '数据状态 ''1''=>''审核中'',''2''=>''审核失败'',''3''=>''成功''',
  `order_no` varchar(64) DEFAULT NULL COMMENT '提现单号',
  `remark` varchar(300) DEFAULT NULL COMMENT '平台备注',
  `bank_id` int(10) unsigned DEFAULT '0' COMMENT '关联银行卡',
  `txid` varchar(200) DEFAULT NULL COMMENT '交易id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no` (`order_no`) USING BTREE,
  KEY `mid` (`mid`)
) ENGINE=InnoDB AUTO_INCREMENT=476 DEFAULT CHARSET=utf8 COMMENT='商户提现表';

-- ----------------------------
-- Records of merchants_withdraw
-- ----------------------------

-- ----------------------------
-- Table structure for `orders`
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(32) DEFAULT NULL COMMENT '系统订单号',
  `merch_order_sn` varchar(32) DEFAULT NULL COMMENT '商户订单号',
  `trans_order_sn` varchar(64) DEFAULT NULL COMMENT '交易单号',
  `r_money` decimal(20,3) unsigned DEFAULT '0.000' COMMENT '下单的人民币',
  `e_rate` decimal(20,8) unsigned DEFAULT '0.00000000' COMMENT '人民币到币种的汇率',
  `f_rate` decimal(20,8) unsigned DEFAULT '0.00000000' COMMENT '币种到人民币的汇率',
  `money` decimal(20,8) unsigned DEFAULT '0.00000000' COMMENT '订单支付金额',
  `real_money` decimal(20,8) DEFAULT '0.00000000' COMMENT '实际到账金额（到账商户多少）',
  `m_fee` decimal(10,8) DEFAULT '0.00000000' COMMENT '商户开通的费率',
  `agent_real_money` decimal(20,8) unsigned DEFAULT '0.00000000' COMMENT '实际到代理商账户的钱',
  `a_fee` decimal(10,8) unsigned DEFAULT '0.00000000' COMMENT '代理商的费率',
  `p_real_money` decimal(20,8) unsigned NOT NULL DEFAULT '0.00000000' COMMENT '平台实际赚多少钱',
  `mid` int(10) unsigned DEFAULT '0' COMMENT '商户id',
  `create_date` datetime DEFAULT NULL COMMENT '创建日期',
  `complete_date` datetime DEFAULT NULL COMMENT '完成时间',
  `status` tinyint(4) DEFAULT '1' COMMENT '1：待付款 2:付款成功3:失败',
  `channel` varchar(20) DEFAULT NULL COMMENT '支付类型',
  `rsync_status` tinyint(4) DEFAULT '1' COMMENT '下游异步回调响应结果 1:未知2:成功3:失败',
  `rsync_message` text COMMENT '异步回调下游返回的结果',
  `notify_url` varchar(300) DEFAULT NULL COMMENT '下游商户异步回调地址',
  `ip` varchar(30) DEFAULT NULL COMMENT '下单IP',
  `m_client_ip` varchar(30) DEFAULT NULL COMMENT '商户那边的用户的IP地址',
  `notify_num` tinyint(1) unsigned DEFAULT '0' COMMENT '通知的次数',
  `is_supplement` tinyint(1) unsigned DEFAULT '2' COMMENT '是否补单 1：补单成功 2：未补单',
  `qr_id` int(10) unsigned DEFAULT '0' COMMENT '二维码的id',
  `user_id` int(10) unsigned DEFAULT '0' COMMENT '码商的id',
  `qr_pic` varchar(500) DEFAULT NULL COMMENT '二维码图片',
  `txid` varchar(500) DEFAULT NULL COMMENT '区块链交易的id',
  `channel_name` varchar(100) DEFAULT NULL COMMENT '通道',
  `address` varchar(200) DEFAULT NULL COMMENT '收款地址',
  `agent_id` int(10) unsigned DEFAULT '0' COMMENT '代理商的id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_sn` (`order_sn`),
  UNIQUE KEY `merch_order_sn` (`merch_order_sn`,`mid`),
  UNIQUE KEY `trans_order_sn` (`trans_order_sn`),
  KEY `create_date` (`create_date`),
  KEY `qr_id` (`qr_id`) USING BTREE,
  KEY `user_id` (`user_id`),
  KEY `txid` (`txid`),
  KEY `address` (`address`)
) ENGINE=InnoDB AUTO_INCREMENT=294 DEFAULT CHARSET=utf8 COMMENT='订单表';

-- ----------------------------
-- Records of orders
-- ----------------------------

-- ----------------------------
-- Table structure for `orders_payment_log`
-- ----------------------------
DROP TABLE IF EXISTS `orders_payment_log`;
CREATE TABLE `orders_payment_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(64) DEFAULT NULL COMMENT '平台订单号',
  `money` decimal(20,8) DEFAULT '0.00000000' COMMENT '到账金额',
  `create_date` datetime DEFAULT NULL COMMENT '系统收到的时间',
  `extra` text COMMENT 'json 字符串',
  `user_id` int(10) unsigned DEFAULT '0' COMMENT '码商的id',
  `message` varchar(300) DEFAULT NULL COMMENT '上报的消息内容',
  `to_address` varchar(100) DEFAULT NULL COMMENT '收款人的收款地址',
  `from_address` varchar(100) DEFAULT NULL COMMENT '付款人的地址',
  `transaction_id` varchar(100) DEFAULT NULL COMMENT '区块链的id',
  `client_ip` varchar(30) DEFAULT NULL COMMENT '客户端IP',
  `match_order` text COMMENT '匹配的订单信息',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `order_sn` (`order_sn`) USING BTREE,
  UNIQUE KEY `transaction_id` (`transaction_id`),
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=75398 DEFAULT CHARSET=utf8 COMMENT='到账记录';

-- ----------------------------
-- Records of orders_payment_log
-- ----------------------------

-- ----------------------------
-- Table structure for `orders_supplement`
-- ----------------------------
DROP TABLE IF EXISTS `orders_supplement`;
CREATE TABLE `orders_supplement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(32) DEFAULT NULL COMMENT '系统订单号',
  `create_date` datetime DEFAULT NULL COMMENT '补单时间',
  `remark` varchar(300) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_sn` (`order_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COMMENT='补单数据表';

-- ----------------------------
-- Records of orders_supplement
-- ----------------------------

-- ----------------------------
-- Table structure for `sysconfig`
-- ----------------------------
DROP TABLE IF EXISTS `sysconfig`;
CREATE TABLE `sysconfig` (
  `varname` varchar(100) DEFAULT NULL,
  `value` text,
  `info` varchar(100) DEFAULT NULL COMMENT '说明',
  `groupid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `type` varchar(10) NOT NULL DEFAULT 'string' COMMENT '变量类型',
  `disorder` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  UNIQUE KEY `varname` (`varname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统配置信息表';

-- ----------------------------
-- Records of sysconfig
-- ----------------------------
INSERT INTO sysconfig VALUES ('ALI_APPCODE', '3a4c80507822463da3ea105adc', '阿里云AppCode', '1', 'string', '8');
INSERT INTO sysconfig VALUES ('agent_max_card_number', '10', '代理商允许添加的银行卡', '3', 'string', '0');
INSERT INTO sysconfig VALUES ('agent_min_withdraw_money', '100', '代理商最低提现金额', '3', 'string', '0');
INSERT INTO sysconfig VALUES ('merch_allow_place', '1\n2\n3', '哪些商户允许后台测试下单', '2', 'textarea', '0');
INSERT INTO sysconfig VALUES ('is_close_plat', 'N', '平台是否关闭', '1', 'boolean', '0');
INSERT INTO sysconfig VALUES ('plat_close_message', '系统维护中....', '平台关闭的提示语', '1', 'textarea', '0');
INSERT INTO sysconfig VALUES ('max_card_number', '5', '最大添加的币数量', '2', 'number', '0');
INSERT INTO sysconfig VALUES ('order_expire', '500', '订单过期时间(秒单位)', '1', 'string', '7');
INSERT INTO sysconfig VALUES ('rsync_white_ip', '192.168.1.21\n127.0.0.1', '回调订单的白名单IP', '1', 'textarea', '0');

-- ----------------------------
-- Function structure for `SPLIT_STR`
-- ----------------------------
DROP FUNCTION IF EXISTS `SPLIT_STR`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `SPLIT_STR`(
  x VARCHAR(255),
  delim VARCHAR(12),
  pos INT
) RETURNS varchar(255) CHARSET utf8
RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(x, delim, pos),
       LENGTH(SUBSTRING_INDEX(x, delim, pos -1)) + 1),
       delim, '')
;;
DELIMITER ;
