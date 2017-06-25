<?php
        	
namespace Addons\Feedback\Model;
use Home\Model\WeixinModel;
        	
/**
 * Feedback的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Feedback' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	