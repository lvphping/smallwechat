<?php

namespace Addons\Cms;
use Common\Controller\Addon;

/**
 * 微信小程序CMS插件
 * @author 凡星
 */

    class CmsAddon extends Addon{

        public $info = array(
            'name'=>'Cms',
            'title'=>'微信小程序CMS',
            'description'=>'微信小程序CMS内容管理系统',
            'status'=>1,
            'author'=>'凡星',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Cms/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Cms/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }