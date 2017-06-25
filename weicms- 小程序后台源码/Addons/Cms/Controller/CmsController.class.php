<?php

namespace Addons\Cms\Controller;
use Home\Controller\AddonsController;

class CmsController extends AddonsController{
    function getList(){    
    	set_time_limit(0);

        $limit = I('limit', 10, 'intval');

        $lastid = I('lastid', 0, 'intval');
        if($lastid>0){
        	$map['id'] = array('lt', $lastid);
        }

    	$list = M('cms')->where($map)->order('id desc')->field('id,title,img,cTime')->limit($limit)->select();

    	foreach ($list as &$vo) {
    		$vo['img'] = get_cover_url($vo['img']);
    		$vo['cTime'] = time_format($vo['cTime']);
    	}

    	//dump($list);
    	$this->ajaxReturn($list);
    }
    function getDetail(){
    	$map['id'] = I('id', 0, 'intval');
    	$info = M('cms')->where($map)->find();

    	$info['img'] = get_cover_url($info['img']);
    	$info['cTime'] = time_format($info['cTime']);
    	//dump($info);
    	$this->ajaxReturn($info);
    }

    //code 换取 session_key
    function sendCode(){
    	$code = I('code');

        //这里要配置你的小程序appid和secret
    	$url = 'https://api.weixin.qq.com/sns/jscode2session?appid=你的appid&secret=你的secret&js_code='.$code.'&grant_type=authorization_code';

    	$data = post_data($url);

    	if((isset($data['errcode']) && $data['errcode']>0) || !isset($data['session_key']) || !isset($data['openid'])){
    		$this->ajaxReturn(array('status'=>0,'msg'=>'获取信息失败'));
    	}

    	session('session_key', $data['session_key']);
    	session('openid', $data['openid']);

    	$map['openid'] = $data['openid'];
    	//用户自动注册
    	if(!M('public_follow')->where($map)->find()){
    		$uid = M('user')->add(array('reg_time'=>NOW_TIME));
    		M('public_follow')->add(array('openid'=>$data['openid'], 'uid'=>$uid, 'token'=>'gh_6d3bf5d72981'));

    		session('mid', $uid);
    	}

    	$PHPSESSID = session_id();
    	$this->ajaxReturn(array('status'=>1, 'openid'=>$data['openid'], 'PHPSESSID'=>$PHPSESSID));
    }

    //保存用户数据
    function saveUserInfo(){
    	$encryptedData = I('encryptedData');
    	$iv = I('iv');
    	if(empty($encryptedData) || empty($iv)){
    		$this->ajaxReturn(array('status'=>0,'msg'=>'传递信息不全'));
    	}

    	include_once "aes/wxBizDataCrypt.php";


$appid = '';  //这里配置你的小程序appid
$sessionKey = session('session_key');

$pc = new \WXBizDataCrypt($appid, $sessionKey);
$errCode = $pc->decryptData($encryptedData, $iv, $data );

if ($errCode == 0) {
    $data = json_decode($data, true);
    session('myinfo', $data);

    $save['nickname'] = $data['nickName'];
    $save['sex'] = $data['gender'];
    $save['city'] = $data['city'];
    $save['province'] = $data['province'];
    $save['country'] = $data['country'];
    $save['headimgurl'] = $data['avatarUrl'];
    !empty($data['unionId']) && $save['unionId'] = $data['unionId'];

    $uid = session('mid');
    if(empty($uid)){
    	$this->ajaxReturn(array('status'=>0,'msg'=>'用户ID异常'.$uid));
    }

    $res = D('Common/User')->updateInfo($uid, $save);
    if($res!==false){
    	$this->ajaxReturn(array('status'=>1));
    }else{
    	$this->ajaxReturn(array('status'=>0,'msg'=>'用户信息保存失败'));
    }

} else {
    $this->ajaxReturn(array('status'=>0,'msg'=>'错误编号：'.$errCode));
}
    }

    //测试session
    function testLogin(){
    	$mid = session('mid');
    	$user = getUserInfo($mid);
    	$this->ajaxReturn($user);
    }
}
