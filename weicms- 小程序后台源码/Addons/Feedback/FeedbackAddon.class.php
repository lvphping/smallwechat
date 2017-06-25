<?php

namespace Addons\Feedback;
use Common\Controller\Addon;

/**
 * 用户反馈插件
 * @author 凡星
 */

    class FeedbackAddon extends Addon{

        public $info = array(
            'name'=>'Feedback',
            'title'=>'用户反馈',
            'description'=>'',
            'status'=>1,
            'author'=>'凡星',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Feedback/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Feedback/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }