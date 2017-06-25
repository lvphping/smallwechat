<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
error_reporting ( E_ERROR );
// /调试、找错时请去掉///前空格
//ini_set ( 'display_errors', true );
//error_reporting ( E_ALL );
//set_time_limit ( 0 );

date_default_timezone_set ( 'PRC' );
if (version_compare ( PHP_VERSION, '5.3.0', '<' ))
	die ( 'Your PHP Version is ' . PHP_VERSION . ', But WeiPHP require PHP > 5.3.0 !' );

define ( 'SYSTEM_TOKEN', 'weiphp' );
/**
 * 微信接入验证
 * 在入口进行验证而不是放到框架里验证，主要是解决验证URL超时的问题
 */
if (! empty ( $_GET ['echostr'] ) && ! empty ( $_GET ["signature"] ) && ! empty ( $_GET ["nonce"] )) {
	$signature = $_GET ["signature"];
	$timestamp = $_GET ["timestamp"];
	$nonce = $_GET ["nonce"];
	
	$tmpArr = array (
			SYSTEM_TOKEN,
			$timestamp,
			$nonce 
	);
	sort ( $tmpArr, SORT_STRING );
	$tmpStr = sha1 ( implode ( $tmpArr ) );
	
	if ($tmpStr == $signature) {
		echo $_GET ["echostr"];
	}
	exit ();
}
/**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */
define ( 'APP_DEBUG', true );
define ( 'SHOW_ERROR', false );

define ( 'IN_WEIXIN', false );
define ( 'DEFAULT_TOKEN', '-1' ); 

/**
 * 官方远程同步服务器地址
 * 应用于后台应用商店、在线升级，配置教程等功能
 */
define ( 'REMOTE_BASE_URL', 'http://www.weiphp.cn' );

// 网站根路径设置
define ( 'SITE_PATH', dirname ( __FILE__ ) );
/**
 * 应用目录设置
 * 安全期间，建议安装调试完成后移动到非WEB目录
 */
define ( 'APP_PATH', './Application/' );

if (! is_file ( SITE_PATH . '/Data/install.lock' )) {
	header ( 'Location: ./install.php' );
	exit ();
}

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ( 'RUNTIME_PATH', './Runtime/' );

if(isset($_GET['PHPSESSID']) && !empty($_GET['PHPSESSID'])){
	session_id($_GET['PHPSESSID']);
}

/**
 * 引入核心入口
 * ThinkPHP亦可移动到WEB以外的目录
 */
require './ThinkPHP/ThinkPHP.php';