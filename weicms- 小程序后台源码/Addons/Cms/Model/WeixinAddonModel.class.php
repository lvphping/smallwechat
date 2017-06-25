<?php
        	
namespace Addons\Cms\Model;
use Home\Model\WeixinModel;
        	
/**
 * Cms的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Cms' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	