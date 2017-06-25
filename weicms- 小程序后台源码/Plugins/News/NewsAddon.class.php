<?php

namespace Plugins\News;

use Common\Controller\Plugin;

/**
 * 图文素材选择器插件
 *
 * @author 凡星
 */
class NewsAddon extends Plugin {
	public $info = array (
			'name' => 'News',
			'title' => '图文素材选择器',
			'description' => '',
			'status' => 1,
			'author' => '凡星',
			'version' => '0.1',
			'has_adminlist' => 0 
	);
	public function install() {
		$install_sql = './Addons/News/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/News/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}
	
	/**
	 * 编辑器挂载的后台文档模型文章内容钩子
	 *
	 * table=addons&type=1&value_field=name&title_field=title&order=id desc&first_option=请选择
	 */
	public function news($data) {
		if (! empty ( $data ['value'] )) {
			$map ['group_id'] = safe ( $data ['value'] );
			$field = 'id,title,cover_id,intro,group_id';
			$list = M ( 'material_news' )->where ( $map )->field ( $field )->order ( 'id asc' )->select ();
			$count = count ( $list );
			$main = $list [0];
			if ($count > 1) {
				unset ( $list [0] );
			}
			
			// dump ( $list );
			$this->assign ( 'count', $count );
			$this->assign ( 'main', $main );
			$this->assign ( 'list', $list );
		}
		$this->assign ( $data );
		
		$this->display ( 'content' );
	}
}