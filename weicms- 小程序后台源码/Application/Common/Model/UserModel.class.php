<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Common\Model;

use Think\Model;

/**
 * 用户模型
 *
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class UserModel extends Model {
	protected $_validate = array (
			array (
					'nickname',
					'1,16',
					'昵称长度为1-16个字符',
					self::EXISTS_VALIDATE,
					'length' 
			),
			array (
					'nickname',
					'',
					'昵称被占用',
					self::EXISTS_VALIDATE,
					'unique' 
			) 
	); // 用户名被占用
	
	/* 用户模型自动完成 */
	protected $_auto = array (
			array (
					'password',
					'think_weiphp_md5',
					self::MODEL_BOTH,
					'function' 
			),
			array (
					'reg_time',
					NOW_TIME,
					self::MODEL_INSERT 
			),
			array (
					'reg_ip',
					'get_client_ip',
					self::MODEL_INSERT,
					'function',
					1 
			),
			array (
					'update_time',
					NOW_TIME 
			) 
	);
	public function lists($status = 1, $order = 'uid DESC', $field = true) {
		$map = array (
				'status' => $status 
		);
		return $this->field ( $field )->where ( $map )->order ( $order )->select ();
	}
	/**
	 * 注册一个新用户
	 *
	 * @param string $username
	 *        	用户名
	 * @param string $password
	 *        	用户密码
	 * @param string $email
	 *        	用户邮箱
	 * @param string $mobile
	 *        	用户手机号码
	 * @return integer 注册成功-用户信息，注册失败-错误编号
	 */
	public function register($username, $password, $email, $mobile, $truename) {
		$data = array (
				'nickname' => $username,
				'login_name' => $username,
				'password' => $password,
				'email' => $email,
				'mobile' => $mobile,
				'truename' => $truename,
				'is_audit' => C ( 'REG_AUDIT' ) 
		);
		
		// 验证手机
		empty ( $mobile ) || $data ['mobile'] = $mobile;
		$data = $this->_deal_nickname ( $data );
		/* 添加用户 */
		if ($this->create ( $data )) {
			$uid = $this->add ();
			return $uid ? $uid : 0; // 0-未知错误，大于0-注册成功
		} else {
			return $this->getError (); // 错误详情见自动验证注释
		}
	}
	function addUser($data) {
		$data = $this->_deal_nickname ( $data );
		$res = $this->add ( $data );
		return $res;
	}
	/**
	 * 登录指定用户
	 *
	 * @param integer $uid
	 *        	用户ID
	 * @return boolean ture-登录成功，false-登录失败
	 */
	public function login($username, $password, $from = 'user_login', $type = 1) {
		$username = safe ( $username, 'text' );
		$password = safe ( $password, 'text' );
		
		/* 检测是否在当前应用注册 */
		$map = array ();
		switch ($type) {
			case 1 :
				$map ['login_name'] = $username;
				break;
			case 2 :
				$map ['email'] = $username;
				break;
			case 3 :
				$map ['mobile'] = $username;
				break;
			case 4 :
				$map ['id'] = $username;
				break;
			default :
				return 0; // 参数错误
		}
		
		/* 获取用户数据 */
		
		$user = $this->field ( true )->where ( $map )->find ();
		
		if (! $user) {
			unset ( $map );
			$map ['login_name'] = $username;
			$user = $this->field ( true )->where ( $map )->find ();
		}
		if ($from == 'admin_login' && intval ( $user ['uid'] ) !== C ( 'USER_ADMINISTRATOR' )) {
			$this->error = '没有超级管理员权限，不能登录！';
			return false;
		}
		if (is_array ( $user ) && intval ( $user ['status'] ) > 0) {
			/* 验证用户密码 */
			if (think_weiphp_md5 ( $password ) === $user ['password']) {
				// 记录行为
				action_log ( $from, 'user', $user ['uid'], $user ['uid'] );
				
				/* 登录用户 */
				$this->autoLogin ( $user );
				
				// 登录成功，返回用户ID
				return $user ['uid'];
			} else {
				$this->error = '密码错误！';
				return false;
			}
		} else {
			$this->error = '用户不存在或已被禁用！'; // 应用级别禁用
			return false;
		}
	}
	
	/**
	 * 注销当前用户
	 *
	 * @return void
	 */
	public function logout() {
		$token = get_token ();
		session ( 'manager_menu_default', null );
		session ( 'mid', null );
		session ( 'user_auth', null );
		session ( 'user_auth_sign', null );
		session ( 'token', null );
		session ( 'openid_' . $token, null );
		session ( 'manager_id', null );
	}
	
	/**
	 * 自动登录用户
	 *
	 * @param integer $user
	 *        	用户信息数组
	 */
	public function autoLogin($user) {
		/* 更新登录信息 */
		$data = array (
				'uid' => $user ['uid'],
				'login_count' => array (
						'exp',
						'`login_count`+1' 
				),
				'last_login_time' => NOW_TIME,
				'last_login_ip' => get_client_ip ( 1 ) 
		);
		$this->save ( $data );
		
		/* 记录登录SESSION和COOKIES */
		$auth = array (
				'uid' => $user ['uid'],
				'username' => $user ['nickname'],
				'last_login_time' => $user ['last_login_time'] 
		);
		session ( 'manager_id', $user ['uid'] );
		session ( 'mid', $user ['uid'] );
		session ( 'user_auth', $auth );
		session ( 'user_auth_sign', data_auth_sign ( $auth ) );
	}
	public function getNickName($uid) {
		$info = $this->getUserInfo ( $uid );
		return $info ['nickname'];
	}
	/**
	 * 获取用户全部信息
	 */
	public function getUserInfo($uid, $update = false) {
		if (! ($uid > 0))
			return false;
		
		$key = 'getUserInfo_' . $uid;
		$userInfo = S ( $key );
		
		if ($userInfo === false || $update) {
			// 获取用户基本信息
			$userInfo = ( array ) $this->find ( $uid );
			
			// 公众号管理员信息
			$manager = ( array ) M ( 'manager' )->where ( "uid='$uid'" )->field ( true )->find ();
			$userInfo = array_merge ( $userInfo, $manager );
			
			// 获取用户组信息
			$userInfo ['groups'] = array ();
			$prefix = C ( 'DB_PREFIX' );
			$groups = M ()->table ( $prefix . 'auth_group_access a' )->where ( "a.uid='$uid' and g.status='1'" )->join ( $prefix . "auth_group g on a.group_id=g.id" )->field ( 'a.group_id,g.title,g.type,g.icon' )->select ();
			foreach ( $groups as $g ) {
				$g ['icon'] = get_cover_url ( $g ['icon'] );
				$userInfo ['groups'] [$g ['group_id']] = $g;
			}
			
			// 公众号粉丝信息
			$userInfo ['tokens'] = array ();
			$tokens = M ( 'public_follow' )->where ( "uid='$uid'" )->field ( true )->select ();
			foreach ( $tokens as $t ) {
				$userInfo ['tokens'] [$t ['token']] = $t ['openid'];
				$userInfo ['remarks'] [$t ['token']] = $t ['remark'];
				$userInfo ['has_subscribe'] [$t ['token']] = $t ['has_subscribe'];
			}
			
			// 是否为系统管理员
			$userInfo ['is_root'] = is_administrator ( $uid );
			$userInfo ['headimgurl'] = empty ( $userInfo ['headimgurl'] ) ? SITE_URL . '/Public/static/face/default_head_50.png' : $userInfo ['headimgurl'];
			
			$sexArr = array (
					0 => '保密',
					1 => '男',
					2 => '女' 
			);
			$sexArr2 = array (
					0 => 'Ta',
					1 => '他',
					2 => '她' 
			);
			$userInfo ['sex_name'] = $sexArr [$userInfo ['sex']];
			$userInfo ['sex_alias'] = $sexArr2 [$userInfo ['sex']];
			$userInfo = $this->_deal_nickname ( $userInfo, 1 );
			
			// 获取标签信息
			$tag_map ['uid'] = $uid;
			$userInfo ['tag_ids'] = M ( 'user_tag_link' )->where ( $tag_map )->getFields ( 'tag_id' );
			if (! empty ( $userInfo ['tag_ids'] )) {
				$tag_map2 ['id'] = array (
						'in',
						$userInfo ['tag_ids'] 
				);
				$titles = M ( 'user_tag' )->where ( $tag_map2 )->getFields ( 'title' );
				$userInfo ['tag_titles'] = implode ( ',', $titles );
			}
			
			S ( $key, $userInfo, 86400 );
		}
		
		$token = session ( 'token' );
		if ($token && ! empty ( $userInfo ['remarks'] [$token] )) {
			$userInfo ['nickname'] = $userInfo ['remarks'] [$token];
		}
		
		return $userInfo;
	}
	function getUserInfoByOpenid($openid, $update = false) {
		$map ['token'] = get_token ();
		$map ['openid'] = $openid;
		$uid = M ( 'public_follow' )->where ( $map )->getField ( 'uid' );
		return $this->getUserInfo ( $uid, $update );
	}
	function updateInfo($uid, $save) {
		if (empty ( $uid ))
			return false;
		
		$save = $this->_deal_nickname ( $save );
		
		$map ['uid'] = $uid;
		$res = $this->where ( $map )->save ( $save );
		if ($res) {
			$this->getUserInfo ( $uid, true );
		}
		return $res;
	}
	/**
	 * 更新用户信息
	 *
	 * @param int $uid
	 *        	用户id
	 * @param string $password
	 *        	密码，用来验证
	 * @param array $data
	 *        	修改的字段数组
	 * @return true 修改成功，false 修改失败
	 * @author huajie <banhuajie@163.com>
	 */
	public function updateUserFields($uid, $password, $data) {
		if (empty ( $uid ) || empty ( $password ) || empty ( $data )) {
			$this->error = '参数错误！';
			return false;
		}
		
		// 更新前检查用户密码
		if (! $this->verifyUser ( $uid, $password )) {
			$this->error = '验证出错：密码不正确！';
			return false;
		}
		
		$data = $this->_deal_nickname ( $data );
		
		// 更新用户信息
		$data = $this->create ( $data );
		if ($data) {
			$res = $this->where ( array (
					'uid' => $uid 
			) )->save ( $data );
			$this->getUserInfo ( $uid, true );
			return $res;
		}
		return false;
	}
	
	/**
	 * 验证用户密码
	 *
	 * @param int $uid
	 *        	用户id
	 * @param string $password_in
	 *        	密码
	 * @return true 验证成功，false 验证失败
	 * @author huajie <banhuajie@163.com>
	 */
	protected function verifyUser($uid, $password_in) {
		// $password = $this->getFieldById ( $uid, 'password' );
		$map ['uid'] = $uid;
		$password = $this->where ( $map )->getField ( 'password' );
		if (think_weiphp_md5 ( $password_in ) === $password) {
			return true;
		}
		return false;
	}
	function _deal_nickname($data, $type = 0) {
		if (isset ( $data ['nickname'] )) {
			$data ['nickname'] = deal_emoji ( $data ['nickname'], $type );
		}
		
		return $data;
	}
	function searchUser($key) {
		if (empty ( $key ))
			return 0;
		
		$key2 = str_replace ( '\u', '\\\\\\\\u', trim ( deal_emoji ( $key, 0 ), '"' ) );
		
		// 搜索用户表
		$where = "nickname LIKE '%$key%' OR nickname LIKE '%$key2%' OR truename LIKE '%$key%' OR truename LIKE '%$key2%'";
		$uids = ( array ) $this->where ( $where )->getFields ( 'uid' );
		// 搜索用户名备注
		$where2 = "remark LIKE '%$key%'";
		$uids2 = ( array ) M ( 'public_follow' )->where ( $where2 )->getFields ( 'uid' );
		
		// 搜索标签
		$where3 = "title LIKE '%$key%'";
		$tag_ids = ( array ) M ( 'user_tag' )->where ( $where3 )->getFields ( 'id' );
		$uids3 = array ();
		if (! empty ( $tag_ids )) {
			$map ['tag_id'] = array (
					'in',
					$tag_ids 
			);
			$uids3 = ( array ) M ( 'user_tag_link' )->where ( $map )->getFields ( 'uid' );
		}
		
		$uids = array_unique ( array_merge ( $uids, $uids2, $uids3 ) );
		
		if (empty ( $uids )) {
			return 0;
		} else {
			return implode ( ',', $uids );
		}
	}
	function clear($uid) {
		$this->getUserInfo ( $uid, true );
	}
}
