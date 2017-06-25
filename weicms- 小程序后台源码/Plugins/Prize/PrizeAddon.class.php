<?php

namespace Plugins\Prize;

use Common\Controller\Plugin;

/**
 * 动态多选菜单插件
 *
 * @author 凡星
 */
class PrizeAddon extends Plugin {
	public $info = array (
			'name' => 'Prize',
			'title' => '奖品选择',
			'description' => '支持多种奖品选择',
			'status' => 1,
			'author' => '凡星',
			'version' => '0.1',
			'has_adminlist' => 0,
			'type' => 0 
	);
	public function install() {
		return true;
	}
	public function uninstall() {
		return true;
	}
	
	/**
	 * 编辑器挂载的后台文档模型文章内容钩子
	 *
	 * table=addons&type=1&value_field=name&title_field=title&order=id desc
	 */
	public function prize($data) {
		$key = 'prize_' . $data ['name'] . '_' . get_token ();
		$res = S ( $key );
		if ($res === false || true) {
			$manager_id = $GLOBALS ['uid'];
			$token = get_token ();
			$data ['extra'] = str_replace ( array (
					'[manager_id]',
					'[token]' 
			), array (
					$manager_id,
					$token 
			), $data ['extra'] );
			
			parse_str ( $data ['extra'], $arr );
			
			$table = ! empty ( $arr ['table'] ) ? $arr ['table'] : 'common_category';
			$value_field = ! empty ( $arr ['value_field'] ) ? $arr ['value_field'] : 'id';
			$title_field = ! empty ( $arr ['title_field'] ) ? $arr ['title_field'] : 'title';
			$order = ! empty ( $arr ['order'] ) ? $arr ['order'] : $value_field . ' asc';
			
			unset ( $arr ['table'], $arr ['value_field'], $arr ['title_field'], $arr ['order'] );
			// dump($arr);
			$arr ['token'] = get_token ();
			$list = M ( $table )->where ( $arr )->field ( $value_field . ',' . $title_field )->order ( $order )->select ();
			
			$res = array ();
			foreach ( $list as $v ) {
				$res [$v [$value_field]] = $v [$title_field];
			}
			
			S ( $key, $res, 86400 );
		}
		// dump ( $json );
		$this->assign ( 'list', $res );
		
// 		$data ['default_value'] = $data ['value'] = is_array ( $data ['value'] ) ? $data ['value'] : explode ( ',', $data ['value'] );
		$data ['default_value'] = $data ['value'] ;
		$data ['prize_detail'] = get_prize_detail($data['value']);
		$this->assign ( $data );
		$this->display ( 'content' );
	}
}