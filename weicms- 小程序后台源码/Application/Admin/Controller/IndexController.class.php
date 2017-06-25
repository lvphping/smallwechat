<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;

/**
 * 后台首页控制器
 * 
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class IndexController extends AdminController {
	
	/**
	 * 后台首页
	 * 
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function index() {
		redirect ( U ( 'Admin/Config/group' ) );
		
		$this->meta_title = '管理首页';
		$this->display ();
	}
}
