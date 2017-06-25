<?php

namespace Addons\Feedback\Controller;
use Home\Controller\AddonsController;

class FeedbackController extends AddonsController{
	// 通用插件的列表模型
	public function lists($model = null, $page = 0) {
				// 通用表单的控制开关
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', true );
		$this->assign ( 'check_all', true );


		is_array ( $model ) || $model = $this->getModel ( $model );
		$templateFile = $this->getAddonTemplate ( $model ['template_list'] );
		parent::common_lists ( $model, $page, $templateFile );
	}   

	public function addFeedback(){
		  $data["username"] = I('username');
		  
		  $data["from"] = I('from',0,'intval');

		  $data["is_dev"] = I('is_dev');
		  //echo $data["switch"];
          $data["is_dev"] = $data["is_dev"]==true ? 1:0;

		  $data["area"] = I('area',0,'intval');
		  $data["score"] = I('score',0,'intval');

		  $data["product"] = str_replace(array('"','[',']'),'',I('product'));

		  $data['cTime'] = NOW_TIME;

		  $res = M('feedback')->add($data);
		  echo intval($res);
	}
}
