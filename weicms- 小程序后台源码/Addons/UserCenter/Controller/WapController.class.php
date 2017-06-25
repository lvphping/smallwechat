<?php

namespace Addons\UserCenter\Controller;

use Home\Controller\AddonsController;

class WapController extends AddonsController {
	// 一键绑定
	function bind() {
		if ((defined ( 'IN_WEIXIN' ) && IN_WEIXIN) || isset ( $_GET ['is_stree'] ) || ! C ( 'USER_OAUTH' ))
			return false;
		
		$isWeixinBrowser = isWeixinBrowser ();
		if (! $isWeixinBrowser) {
			$this->error ( '请在微信里打开' );
		}
		$info = get_token_appinfo ();
		$param ['appid'] = $info ['appid'];
		$callback = U ( 'bind' );
		if ($_GET ['state'] != 'weiphp') {
			$param ['redirect_uri'] = $callback;
			$param ['response_type'] = 'code';
			$param ['scope'] = 'snsapi_userinfo';
			$param ['state'] = 'weiphp';
			$info ['is_bind'] && $param ['component_appid'] = C ( 'COMPONENT_APPID' );
			$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query ( $param ) . '#wechat_redirect';
			redirect ( $url );
		} elseif ($_GET ['state'] == 'weiphp') {
			if (empty ( $_GET ['code'] )) {
				exit ( 'code获取失败' );
			}
			
			$param ['code'] = I ( 'code' );
			$param ['grant_type'] = 'authorization_code';
			
			if ($info ['is_bind']) {
				$param ['appid'] = I ( 'appid' );
				$param ['component_appid'] = C ( 'COMPONENT_APPID' );
				$param ['component_access_token'] = D ( 'Addons://PublicBind/PublicBind' )->_get_component_access_token ();
				
				$url = 'https://api.weixin.qq.com/sns/oauth2/component/access_token?' . http_build_query ( $param );
			} else {
				$param ['secret'] = $info ['secret'];
				
				$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query ( $param );
			}
			
			$content = file_get_contents ( $url );
			$content = json_decode ( $content, true );
			if (! empty ( $content ['errmsg'] )) {
				exit ( $content ['errmsg'] );
			}
			
			$suburl = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . get_access_token () . '&openid=' . $content ['openid'] . '&lang=zh_CN';
			$data = file_get_contents ( $suburl );
			$data = json_decode ( $data, true );
			$subscribe = $data ['subscribe'];
			
			if (! empty ( $data ['errmsg'] )) {
				exit ( $data ['errmsg'] );
			}
			
			$data ['status'] = 2;
			empty ( $data ['headimgurl'] ) && $data ['headimgurl'] = ADDON_PUBLIC_PATH . '/default_head.png';
			
			$uid = D ( 'Common/Follow' )->init_follow ( $content ['openid'], $info ['token'] );
			D ( 'Common/User' )->updateInfo ( $uid, $data );
			if ($subscribe) {
				D ( 'Common/Follow' )->set_subscribe ( $content ['openid'], 1 );
			}
			
			$url = Cookie ( '__forward__' );
			if ($url) {
				Cookie ( '__forward__', null );
			} else {
				$url = U ( 'userCenter' );
			}
			
			redirect ( $url );
		}
	}
	// 绑定领奖信息
	function bind_prize_info() {
		// dump ( $this->mid );
		$map ['id'] = $this->mid;
		// dump($this->mid);
		if (IS_POST) {
			
			$data ['mobile'] = I ( 'mobile' );
			$data ['truename'] = I ( 'truename' );
			
			D ( 'Common/Follow' )->update ( $this->mid, $data );
			
			$url = Cookie ( '__forward__' );
			if ($url) {
				Cookie ( '__forward__', null );
			} else {
				$url = U ( 'userCenter' );
			}
			
			redirect ( $url );
		} else {
			$info = get_followinfo ( $this->mid );
			$this->assign ( 'info', $info );
			$this->assign ( 'meta_title', '领奖联系信息' );
			$this->display ();
		}
	}
	
	// 第一步绑定手机号
	function bind_mobile() {
		if (IS_POST) {
			
			$map ['mobile'] = I ( 'mobile' );
			$dao = D ( 'Common/Follow' );
			// 判断是否已经注册过
			$user = $dao->where ( $map )->find ();
			if (! $user) {
				$uid = $dao->init_follow_by_mobile ( $map ['mobile'] );
				$dao->where ( $map )->setField ( 'status', 0 );
				
				$user = $dao->where ( $map )->find ();
			}
			
			$map2 ['openid'] = get_openid ();
			if ($map2 ['openid'] != - 1) {
				$map2 ['token'] = get_token ();
				$uid2 = M ( 'public_follow' )->where ( $map2 )->getField ( 'uid' );
				if (! $uid2) {
					$map2 ['mobile'] = $map ['mobile'];
					$map2 ['uid'] = $user ['id'];
					M ( 'public_follow' )->add ( $map2 );
				}
			} else {
				$uid = M ( 'public_follow' )->where ( $map )->getField ( 'uid' );
				if (! $uid) {
					$data ['mobile'] = $map ['mobile'];
					$data ['uid'] = $user ['id'];
					M ( 'public_follow' )->add ( $data );
				}
			}
			
			session ( 'mid', $user ['id'] );
			
			if ($user ['status'] == 1) {
				$url = Cookie ( '__forward__' );
				if ($url) {
					Cookie ( '__forward__', null );
				} else {
					$url = U ( 'userCenter' );
				}
			} else {
				$url = U ( 'bind_info' );
			}
			
			$this->success ( '绑定成功', $url );
		} else {
			if ($this->mid > 0) {
				redirect ( U ( 'userCenter' ) );
			}
			$this->assign ( 'meta_title', '绑定手机' );
			$this->display ();
		}
	}
	// 第二步初始化资料
	function bind_info() {
		$model = $this->getModel ( 'user' );
		
		if (IS_POST) {
			$map ['id'] = $this->mid;
			$url = Cookie ( '__forward__' );
			if ($url) {
				Cookie ( '__forward__', null );
			} else {
				$url = U ( 'userCenter' );
			}
			
			$save ['nickname'] = I ( 'nickname' );
			$save ['sex'] = I ( 'sex' );
			$save ['email'] = I ( 'email' );
			$save ['status'] = 2;
			
			$res = D ( 'Common/User' )->updateInfo ( $this->mid, $save );
			if ($res) {
				$this->success ( '保存成功！', $url );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			// dump($fields);
			$fieldArr = array (
					'nickname',
					'sex',
					'email' 
			); // headimgurl
			foreach ( $fields as $k => $vo ) {
				if (! in_array ( $vo ['name'], $fieldArr ))
					unset ( $fields [$k] );
			}
			
			// 获取数据
			$data = M ( get_table_name ( $model ['id'] ) )->find ( $this->mid );
			
			// 自动从微信接口获取用户信息
			empty ( $openid ) || $info = getWeixinUserInfo ( $openid, $token );
			if (is_array ( $info )) {
				if (empty ( $data ['headimgurl'] ) && ! empty ( $info ['headimgurl'] )) {
					// 把微信头像转到WeiPHP的通用图片ID保存 TODO
					$data ['headimgurl'] = $info ['headimgurl'];
				}
				$data = array_merge ( $info, $data );
			}
			
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			
			$this->assign ( 'meta_title', '填写资料' );
			$this->display ();
		}
	}
	
	/**
	 * 显示微信用户列表数据
	 */
	function userCenter() {
		// dump ( $this->mid );
		if ($this->mid < 0) {
			Cookie ( '__forward__', $_SERVER ['REQUEST_URI'] );
			redirect ( U ( 'bind' ) );
		}
		// 商城版的直接在商城个人中心里
		if (is_install ( 'Shop' )) {
			redirect ( addons_url ( 'Shop://Wap/user_center' ) );
		}
		
		$info = get_followinfo ( $this->mid );
		$this->assign ( 'info', $info );
		// dump ( $info );
		
		// 插件扩展
		$dirs = array_map ( 'basename', glob ( ONETHINK_ADDON_PATH . '*', GLOB_ONLYDIR ) );
		if ($dirs === FALSE || ! file_exists ( ONETHINK_ADDON_PATH )) {
			$this->error ( '插件目录不可读或者不存在' );
		}
		
		$arr = array ();
		$_REQUEST ['doNotInit'] = 1;
		foreach ( $dirs as $d ) {
			require_once ONETHINK_ADDON_PATH . $d . '/Model/WeixinAddonModel.class.php';
			$model = D ( 'Addons://' . $d . '/WeixinAddon' );
			if (! method_exists ( $model, 'personal' ))
				continue;
			
			$lists = $model->personal ( $data, $keywordArr );
			if (empty ( $lists ) || ! is_array ( $lists ))
				continue;
			
			if (isset ( $lists ['url'] )) {
				$arr [] = $lists;
			} else {
				$arr = array_merge ( $arr, $lists );
			}
		}
		
		foreach ( $arr as $vo ) {
			if (empty ( $vo ['group'] )) {
				$default_link [] = $vo;
			} else {
				$list_data [$vo ['group']] [] = $vo;
			}
		}
		$this->assign ( 'default_link', $default_link );
		$this->assign ( 'list_data', $list_data );
		
		// 会员页
		$this->display ();
	}
	
	// 积分记录
	function credit() {
		$model = $this->getModel ( 'credit_data' );
		
		$map ['token'] = get_token ();
		session ( 'common_condition', $map );
		
		parent::common_lists ( $model, 0, 'Addons/lists' );
	}
	function storeCenter() {
		if (! is_login ()) {
			Cookie ( '__forward__', $_SERVER ['REQUEST_URI'] );
			redirect ( U ( 'home/user/login', array (
					'from' => 2 
			) ) );
		}
		
		$this->mid = 382;
		$info = get_userinfo ( $this->mid );
		$this->assign ( 'info', $info );
		// dump ( $info );
		
		// 取优惠券
		$map ['shop_uid'] = $this->mid;
		$list = M ( 'coupon' )->where ( $map )->select ();
		$this->assign ( 'coupons', $list );
		// dump($list);
		
		// 商家中心
		$this->display ();
	}
	
	// 检查公众号基础功能
	function check() {
		$map ['token'] = I ( 'token' );
		$info = M ( 'public' )->where ( $map )->find ();
		$type = $info ['type'];
		
		// 获取微信权限节点
		$map2 ['type_' . $type] = 1;
		$auth = M ( 'public_auth' )->where ( $map2 )->getFields ( 'name,title' );
		
		$res ['msg'] = '';
		// 获取access_token
		$access_token = get_access_token ( $token );
		if (empty ( $access_token )) {
			addAutoCheckLog ( 'access_token', 'access_token获取失败', $info ['token'] );
		} else {
			addAutoCheckLog ( 'access_token', '', $info ['token'] );
		}
		
		$url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=' . $access_token;
		$res = wp_file_get_contents ( $url );
		$res = json_decode ( $res, true );
		if ($res ['errcode'] == '40001') {
			addAutoCheckLog ( 'access_token_check', $res ['errcode'] . ': ' . $res ['errmsg'], $info ['token'] );
		} else {
			addAutoCheckLog ( 'access_token_check', '', $info ['token'] );
		}
		
		// 收发消息
		$xml = '<xml><ToUserName><![CDATA[' . $info ['token'] . ']]></ToUserName>
<FromUserName><![CDATA[oikassyZe6bdupvJ2lq-majc_rUg]]></FromUserName>
<CreateTime>1464254617</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[自动检测]]></Content>
<MsgId>6288925693437624122</MsgId>
</xml>';
		
		$param ['id'] = $info ['id'];
		$param ['signature'] = 'd3e1ce50d26db638e6a03e1c8bf6b23b4fdbdd87';
		$param ['timestamp'] = '1464254754';
		$param ['nonce'] = '407622025';
		$url = U ( 'home/weixin/index', $param );
		
		$res = $this->curl_data ( $url, $xml );
		if (strpos ( $res, 'auto_check' )) {
			addAutoCheckLog ( 'massage', '', $info ['token'] );
		} else {
			addAutoCheckLog ( 'massage', '收发消息失败', $info ['token'] );
		}
		
		$nextUrl = U ( 'check2', array (
				'token' => $info ['token'] 
		) );
		$this->assign ( 'nextUrl', $nextUrl );
		$this->display ();
	}
	function check2() {
		$token = I ( 'token' );
		
		// get_openid
		$callback = GetCurUrl ();
		$openid = OAuthWeixin ( $callback, $token, true );
		if (empty ( $openid ) || $openid == '-1' || $openid == '-2') {
			addAutoCheckLog ( 'openid', '获取openid失败', $token );
		} else {
			addAutoCheckLog ( 'openid', '', $token );
		}
		
		addAutoCheckLog ( 'jsapi', '', $token );
		
		$this->display ();
	}
	function check3() {
		$token = I ( 'token' );
		$msg = I ( 'msg' );
		addAutoCheckLog ( 'jsapi', $msg, $token );
	}
	function curl_data($url, $param) {
		set_time_limit ( 0 );
		$ch = curl_init ();
		if (class_exists ( '/CURLFile' )) { // php5.5跟php5.6中的CURLOPT_SAFE_UPLOAD的默认值不同
			curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, true );
		} else {
			if (defined ( 'CURLOPT_SAFE_UPLOAD' )) {
				curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, false );
			}
		}
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
		curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)' );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $param );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$res = curl_exec ( $ch );
		$flat = curl_errno ( $ch );
		if ($flat) {
			$data = curl_error ( $ch );
			addWeixinLog ( $flat, 'post_data flat' );
			addWeixinLog ( $data, 'post_data msg' );
		}
		
		curl_close ( $ch );
		
		return $res;
	}
	function check_res_ajax() {
		$map ['token'] = I ( 'token' );
		$list = M ( 'public_check' )->where ( $map )->order ( 'id asc' )->select ();
		foreach ( $list as $vo ) {
			$res [$vo ['na']] ['msg'] = $vo ['msg'];
		}
		
		if (empty ( $res )) {
			echo 0;
		} else {
			echo json_encode ( $res );
		}
	}
}
