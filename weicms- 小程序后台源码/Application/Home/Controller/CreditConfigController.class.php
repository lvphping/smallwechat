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
class CreditConfigController extends HomeController {
	function _initialize() {
		$act = strtolower ( CONTROLLER_NAME );
		$nav = array ();
		$res ['title'] = '积分配置';
		$res ['url'] = U ( 'CreditConfig/lists' );
		$res ['class'] = $act == 'creditconfig' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '积分记录';
		$res ['url'] = U ( 'CreditData/lists' );
		$res ['class'] = $act == 'creditdata' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	public function lists() {
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'search_button', false );
		$this->assign ( 'check_all', false );
		
		$model = $this->getModel ( 'credit_config' );
		
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		// dump ( $list_data );
		
		$list_data ['list_data'] = D ( 'Common/Credit' )->getCreditByName ();
		
		$this->assign ( $list_data );
		
		$this->display ( 'Addons/lists' );
	}
	public function edit($id = 0) {
		$model = $this->getModel ( 'credit_config' );
		$id || $id = I ( 'id' );
		
		// 获取数据
		$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
		$data || $this->error ( '数据不存在！' );
		
		if (IS_POST) {
			$act = 'save';
			if ($data ['token'] == 0) {
				$data ['token'] = $_POST ['token'] = get_token ();
				unset ( $_POST ['id'] );
				$act = 'add';
			}
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->$act ()) {
				if ($_POST ['name'] == 'subscribe') {
					$credit ['score'] = I ( 'score' );
					$credit ['experience'] = I ( 'experience' );
					D ( 'Common/Credit' )->updateSubscribeCredit ( $data ['token'], $credit, 0 );
				}
				D ( 'Common/Credit' )->clear ();
				// dump($Model->getLastSql());
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				// dump($Model->getLastSql());
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			$fields ['name'] ['is_show'] = $fields ['title'] ['is_show'] = 4;
			
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $model ['title'];
			
			$this->display ( 'Addons/edit' );
		}
	}
}