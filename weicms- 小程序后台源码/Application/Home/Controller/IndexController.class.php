<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Home\Controller;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends HomeController {
	var $sReqTimeStamp, $sReqNonce, $sEncryptMsg;
	// 系统首页
	public function index() {
		if ($this->mid <= 0) {
			redirect ( U ( 'Home/User/login', array (
					'from' => 1
			) ) );
		}
		$map ['uid'] = $this->mid;
		$user = M ( 'user' )->where ( $map )->find ();
		if ($user ['is_audit'] == 0 && ! C ( 'REG_AUDIT' )) {
			redirect ( U ( 'home/Public/waitAudit' ) );
		} else {
			//$menus = D ( 'Common/ManagerMenu' )->get ( $this->mid );
			//redirect ( $menus ['init_url'] );
			redirect ( U ( 'home/index/main' ) );
		}
	}
	// 通用清缓存
	function generalClean() {
		// getModelByName_comment
		$key = I ( 'key' );
		dump ( $key );
		$d = S ( $key );
		dump ( $d );
		if (isset ( $_GET ['do_clean'] ))
			dump ( S ( $key, null ) );
	}
	// 管理员预览时初始化粉丝信息
	function bind_follow() {
		$publicid = $map ['publicid'] = I ( 'publicid' );
		$uid = $map ['uid'] = I ( 'uid' );
		$this->assign ( $map );

		$info = M ( 'user_follow' )->where ( $map )->find ();

		$is_ajax = I ( 'is_ajax', 0 );
		if ($is_ajax) {
			if ($info ['follow_id'] > 0) {
				session ( 'follow_id', $info ['follow_id'] );
			}
			echo $info ['follow_id'];
			exit ();
		} elseif ($info ['follow_id']) {
			// $url = Cookie ( '__forward__' );
			// Cookie ( '__forward__', null );
			// if (strpos ( $url, SITE_DOMAIN ) === false) {
			// $url = HTTP_PREFIX . SITE_DOMAIN . $url;
			// }
			// redirect ( $url );
			// exit ();
		}

		$data ['url'] = Cookie ( '__preview_url__' );
		if ($info) {
			M ( 'user_follow' )->where ( $map )->save ( $data );
		} else {
			$data ['uid'] = $this->mid;
			$data ['publicid'] = $publicid;
			$info ['id'] = M ( 'user_follow' )->add ( $data );
		}

		$url = addons_url ( 'Wecome/Wap/bind_follow', array (
				'publicid' => $publicid,
				'uid' => $uid
		) );

		$this->assign ( 'url', $url );
		$this->display ();
	}
	function bind_follow_after() {
		$url = Cookie ( '__forward__' );
		Cookie ( '__forward__', null );
		if (strpos ( $url, SITE_DOMAIN ) === false) {
			$url = HTTP_PREFIX . SITE_DOMAIN . $url;
		}
		redirect ( $url );
	}
	// 接入指引
	public function lead() {
		$token = get_token ();
		if ($token) {
			$app_info_id = get_token_appinfo ( $token, 'id' );
			$this->assign ( 'id', $app_info_id );
		}
		$this->display ();
	}

	// 系统帮助
	public function help() {
		$this->display ();
	}
	// 系统关于
	public function about() {
		$this->display ();
	}
	// 问答及说明
	public function question() {
		$this->display ();
	}
	// 授权协议
	public function license() {
		$this->display ();
	}
	// 下载weiphp
	public function downloadFile() {
		M ( 'config' )->where ( 'name="DOWNLOAD_COUNT"' )->setInc ( 'value' );
		redirect ( 'http://down.weiphp.cn/weiphp.zip' );
	}
	// 远程获取最新版本号
	public function update_version() {
		die ( M ( 'update_version' )->getField ( "max(`version`)" ) );
	}
	// 远程获取升级包信息
	public function update_json() {
		$old_version = intval ( $_REQUEST ['version'] );
		$new_version = M ( 'update_version' )->getField ( "max(`version`)" );

		$res = array ();
		if ($old_version < $new_version) {
			$res = M ( 'update_version' )->field ( 'version,title,description,create_date' )->where ( 'version>' . $old_version )->select ();
		}

		die ( json_encode ( $res ) );
	}
	// 下载升级包
	public function download_update_package() {
		$map ['version'] = intval ( $_REQUEST ['version'] );
		$package = M ( 'update_version' )->where ( $map )->getField ( 'package' );
		if (empty ( $package )) {
			$this->error ( '下载的文件不存在或已被移除' );
		}
		M ( 'update_version' )->where ( $map )->setInc ( 'download_count' );

		redirect ( $package );
	}
	public function main() {
		if (! is_login () && IS_GET) {
			Cookie ( '__forward__', $_SERVER ['REQUEST_URI'] );
			$url = U ( 'home/user/login', array (
					'from' => 3
			) );
			redirect ( $url );
		} 
		$map ['status'] = 1;
		$data = M ( 'addons' )->where ( $map )->order ( 'id DESC' )->select ();
		$token_status = D ( 'Common/AddonStatus' )->getList ( true );
		foreach ( $data as $k => &$vo ) {
			if ($token_status [$vo ['name']] === '-1') {
				unset ( $data [$k] );
				// $vo ['status_title'] = '无权限';
				// $vo ['action'] = '';
				// $vo ['color'] = '#CCC';
				// $vo ['status'] = 0;
			} elseif ($token_status [$vo ['name']] === 0) {
				// $vo ['status_title'] = '已停用';
				// $vo ['action'] = '启用';
				// $vo ['color'] = '#CCC';
				// $vo ['status'] = 0;
			} else {
				// $vo ['status_title'] = '已启用';
				// $vo ['action'] = '停用';
				// $vo ['color'] = '';
				// $vo ['status'] = 1;
			}
			$app_icon = SITE_PATH . '/Addons/' . $vo ['name'] . '/icon.png';
			if (file_exists ( $app_icon )) {
				$vo ['app_icon'] = SITE_URL . '/Addons/' . $vo ['name'] . '/icon.png';
			} else {
				$vo ['app_icon'] = SITE_URL . '/Public/Home/images/app_no_pic.png';
			}
			// 连接
			if ($vo ['has_adminlist']) {
				$vo ['addons_url'] = addons_url ( $vo ['name'] . '://' . $vo ['name'] . '/lists' );
			} elseif (file_exists ( ONETHINK_ADDON_PATH . $vo ['name'] . '/config.php' )) {
				$vo ['addons_url'] = addons_url ( $vo ['name'] . '://' . $vo ['name'] . '/config' );
			} else {
				$vo ['addons_url'] = addons_url ( $vo ['name'] . '://' . $vo ['name'] . '/nulldeal' );
			}
		}
		$this->assign ( 'list_data', $data );

		// 自动同步微信用户
		C ( 'USER_LIST' ) && $this->_autoUpdateUser ();
		C ( 'USER_GROUP' ) && $this->_updateWechatGroup ();

		// 用户统计
		$px = C ( 'DB_PREFIX' );
		$map ['f.token'] = get_token ();
		$map ['f.has_subscribe'] = 1;
//		$count ['total'] = M ()->where ( $map )->table ( $px . 'public_follow as f' )->join ( $px . 'user as u ON f.uid=u.uid' )->count ();
		$count ['total'] = M ()->where ( $map )->table ( $px . 'public_follow as f' )->count ();
		$time = strtotime ( date ( 'Y-m-d' ) );
		$map ['u.reg_time'] = array (
				'gt',
				$time
		);
		$count ['today'] = M ()->table ( $px . 'public_follow as f' )->join ( $px . 'user as u ON f.uid=u.uid' )->where ( $map )->count ();
		$map ['u.reg_time'] = array (
				'gt',
				$time - 86400
		);
		$count ['yestoday'] = M ()->table ( $px . 'public_follow as f' )->join ( $px . 'user as u ON f.uid=u.uid' )->where ( $map )->count ();
		$count ['yestoday'] = $count ['yestoday'] - $count ['today'];
		$this->assign ( 'count', $count );

		// 官方公告
		$notices = M('system_notice')->order('id desc')->limit(5)->select();
		$this->assign ( 'notices', $notices );

		$this->display ();
	}
	function union() {
		$this->display ();
	}
	function _autoUpdateUser() {
		// 获取openid列表
		$url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . get_access_token (); // 只取第一页数据
		$data = wp_file_get_contents ( $url );
		$data = json_decode ( $data, true );
		if (! isset ( $data ['count'] ) || $data ['count'] == 0) {
			return false;
		}

		$map ['openid'] = array (
				'in',
				$data ['data'] ['openid']
		);
		$map ['token'] = $save ['token'] = get_token ();

		$openids = M ( 'public_follow' )->where ( $map )->getFields ( 'openid' );

		$diff = array_diff ( ( array ) $data ['data'] ['openid'], ( array ) $openids );
		if (empty ( $diff )) { // 没有需要同步的用户
			return false;
		}

		foreach ( $diff as $oid ) {
			$param ['user_list'] [] = array (
					'openid' => $oid,
					'lang' => 'zh-CN'
			);
			$openids [] = $oid;
		}

		$url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=' . get_access_token ();
		$data = post_data ( $url, $param );
		if (empty ( $data ['user_info_list'] )) {
			return false;
		}

		$userDao = D ( 'Common/User' );
		$config = getAddonConfig ( 'UserCenter' );
		if (isset ( $_GET ['test'] )) {
			dump ( $config );
			exit ();
		}
		foreach ( $data ['user_info_list'] as $u ) {
			if ($u ['subscribe'] == 0) {
				continue;
			}

			$u ['experience'] = intval ( $config ['experience'] );
			$u ['score'] = intval ( $config ['score'] );
			$u ['reg_time'] = $u ['subscribe_time'];
			$u ['status'] = 1;
			$u ['is_init'] = 1;
			$u ['is_audit'] = 1;

			$uid = D ( 'Common/User' )->addUser ( $u );

			$save ['openid'] = $u ['openid'];
			$save ['uid'] = $uid;
			$save ['syc_status'] = 2;
			$save ['has_subscribe'] = 1;
			$res = M ( 'public_follow' )->add ( $save );
		}
	}
	// 与微信的用户组保持同步
	function _updateWechatGroup() {
		// 先取当前用户组数据
		$map ['token'] = get_token ();
		$map ['manager_id'] = $this->mid;
		$map ['type'] = 1;
		$group_list = M ( 'auth_group' )->where ( $map )->getField ( 'wechat_group_id,id' );

		$url = 'https://api.weixin.qq.com/cgi-bin/groups/get?access_token=' . get_access_token ();
		$data = wp_file_get_contents ( $url );
		$data = json_decode ( $data, true );
		foreach ( $data ['groups'] as $d ) {
			if (isset ( $group_list [$d ['id']] ))
				continue;

			$map ['wechat_group_id'] = $d ['id'];
			$map ['wechat_group_name'] = $d ['name'];
			$map ['wechat_group_count'] = $d ['count'];

			// 增加本地数据
			$map ['title'] = $d ['name'];
			$map ['qr_code'] = '';

			M ( 'auth_group' )->add ( $save );
		}
	}
	function notice_detail() {
		$map['id'] = I('id', 0, 'intval');
		$info = M('system_notice')->where($map)->find();
		$this->assign ( 'info', $info );
		$this->display();
	}
	function setStatus() {
		$addon = I ( 'addon' );
		$token_status = D ( 'Common/AddonStatus' )->getList ();

		if ($token_status [$addon] === '-1') {
			$this->success ( '无权限设置' );
		}

		$status = 1 - I ( 'status' );
		$res = D ( 'Common/AddonStatus' )->set ( $addon, $status );
		$this->success ( '设置成功' );
	}

	// 宣传页面
	function leaflets() {
		$name = 'Leaflets';
		$config = array ();
		$map ['token'] = I ( 'get.token' );
		$addon_config = M ( 'public' )->where ( $map )->getField ( 'addon_config' );
		$addon_config = json_decode ( $addon_config, true );
		if (isset ( $addon_config [$name] )) {
			$config = $addon_config [$name];
			$config ['img'] = is_numeric ( $config ['img'] ) ? get_cover_url ( $config ['img'] ) : SITE_URL . '/Addons/Leaflets/View/default/Public/qrcode_default.jpg';
			$this->assign ( 'config', $config );
		} else {
			$this->error ( '请先保存宣传页的配置' );
		}
		define ( 'ADDON_PUBLIC_PATH', ONETHINK_ADDON_PATH . 'Leaflets/View/default/Public' );
		$this->display ( SITE_PATH . '/Addons/Leaflets/View/default/Leaflets/show.html' );
	}
	// 定时任务调用入口
	function cron() {
		D ( 'Home/Cron' )->run ();
		echo date ( 'Y-m-d H:i:s' ) . "\r\n";
	}

	
	function getFooterHtml() {
		$token = $map ['token'] = I ( 'token' );
		$temp = I ( 'temp' );

		$config = getAddonConfig ( 'WeiSite', $token );
		$config ['cover_url'] = get_cover_url ( $config ['cover'] );
		$config ['background_id'] = $config ['background'];
		$config ['background'] = get_cover_url ( $config ['background'] );
		$this->config = $config;
		$this->assign ( 'config', $config );
		// dump ( $config );
		// dump(get_token());

		// 定义模板常量
		define ( 'CUSTOM_TEMPLATE_PATH', ONETHINK_ADDON_PATH . 'WeiSite/View/default/Template' );

		$list = D ( 'Addons://WeiSite/Footer' )->get_list ( $map );

		foreach ( $list as $k => $vo ) {
			if ($vo ['pid'] != 0)
				continue;

			$one_arr [$vo ['id']] = $vo;
			unset ( $list [$k] );
		}

		foreach ( $one_arr as &$p ) {
			$two_arr = array ();
			foreach ( $list as $key => $l ) {
				if ($l ['pid'] != $p ['id'])
					continue;

				$two_arr [] = $l;
				unset ( $list [$key] );
			}

			$p ['child'] = $two_arr;
		}
		$this->assign ( 'footer', $one_arr );
		$html = $this->fetch ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateFooter/' . $temp . '/footer.html' );
		echo $html;
	}
	//跳转页面
	function jump(){
		$this->display ();

	}
}