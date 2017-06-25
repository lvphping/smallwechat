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
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */

class FileController extends HomeController {
	/* 文件上传 */
	public function upload(){
		$return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$File = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		
		$info = $File->upload(
			$_FILES,
			C('DOWNLOAD_UPLOAD'),
			C('DOWNLOAD_UPLOAD_DRIVER'),
			C("UPLOAD_{$file_driver}_CONFIG")
		);
        /* 记录附件信息 */
        if($info){
        	$return['status'] = 1;
        	$return = array_merge($info['download'], $return);
        } else {
            $return['status'] = 0;
            $return['info']   = $File->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }

    /* 下载文件 */
    public function download($id = null){
        if(empty($id) || !is_numeric($id)){
            $this->error('参数错误！');
        }

        $logic = D('Download', 'Logic');
        if(!$logic->download($id)){
            $this->error($logic->getError());
        }

    }

    /**
     * 上传图片
     * @author huajie <banhuajie@163.com>
     */
    public function uploadPicture(){
        //TODO: 用户登录检测

        /* 返回标准数据 */
        $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

        /* 调用文件上传组件上传文件 */
        $Picture = D('Picture');
        $pic_driver = C('PICTURE_UPLOAD_DRIVER');
        $info = $Picture->upload(
            $_FILES,
            C('PICTURE_UPLOAD'),
            C('PICTURE_UPLOAD_DRIVER'),
            C("UPLOAD_{$pic_driver}_CONFIG")
        ); //TODO:上传到远程服务器

        /* 记录图片信息 */
        if($info){
            $return['status'] = 1;
            $return = array_merge($info['download'], $return);
        } else {
            $return['status'] = 0;
            $return['info']   = $Picture->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }
	//图片选择器
	function uploadDialog(){
		$this->display();
	}
	function userPics(){
		$map['token'] = get_token();
		$picList = D('picture')->where($map)->select();
		$this->assign('picList',$picList);
		$this->display();
	}
	//系统图标
	function systemPics(){
		$dir = $_GET['dir'];
		$cateList = $this->_getLocalCate();
		if(!$dir){
			$dir = $cateList[0]['dir'];
		}
		foreach($cateList as &$ca){
			if($dir==$ca['dir']){
				$ca['current'] = 1;
				break;
			}
		}
		$picList = $this->_getCatePicList($dir);
		foreach($picList as &$p){
			$pInfo = D('picture')->where(array('path'=>$p['path']))->find();
			if($pInfo){
				$p['id'] = $pInfo['id'];
				continue;
			}else{
				$data['path'] = $p['path'];
				$data['system'] = 1;
				$data['status'] = 1;
				$data['create_time'] = time();
				$id =  D('picture')->add($data);
				if($id){
					$p['id'] = $id;
				}else{
					unset($p);
				}
			}
		}
		$this->assign('cateList',$cateList);
		$this->assign('picList',$picList);
		$this->display();
	}
	// 获取系统图标分类
	function _getLocalCate(){
		$dir = SITE_PATH . '/Public/static/icon';
		$dirObj = opendir ( $dir );
		while ( $file = readdir ( $dirObj ) ) {
			if ($file === '.' || $file == '..' || $file == '.svn' || is_file ( $dir . '/' . $file )){
				continue;
			}
			$res ['cate'] = $file;
			$res ['dir'] = $file;
			// 获取配置文件
			if (file_exists ( $dir . '/' . $file . '/info.php' )) {
				$info = require_once $dir . '/' . $file . '/info.php';
				$res = array_merge ( $res, $info );
			}
			$cateList [] = $res;
			unset ( $res );
		}
		closedir ( $dir );
		return $cateList;
	}
	function _getCatePicList($dirName){
		$dir = SITE_PATH . '/Public/static/icon/'.$dirName;
		$dirObj = opendir ( $dir );
		while ( $file = readdir ( $dirObj ) ) {
			if ($file === '.' || $file == '..' || $file == '.svn' || $file == 'info.php'){
				continue;
			}
			$res ['path'] = '/Public/static/icon/'.$dirName.'/'.$file;
			$res ['url'] = SITE_URL.'/Public/static/icon/'.$dirName.'/'.$file;
			$picList [] = $res;
			
			unset ( $res );
			
		}
		closedir ( $dir );
		return $picList;
	}
	//图片管理
	public function picLists(){
		$picModel = $this->getModel ( 'picture' );
		$token = get_token();
		$map['token'] = array('in',$token.',1');
		$add_url = U('editPic',array('mdm'=>I('mdm')));
		$del_url = U('delPic',array('mdm'=>I('mdm')));
		$this->assign('add_url',$add_url);
		$this->assign('del_url',$del_url);
		$map ['token'] = get_token ();
		$list = D('picture')->where($map)->selectPage(30);
		$list['fields'] = array('id','pic','cate');
		$list['list_grids']['id']['field'] = 'id';
		$list['list_grids']['id']['title'] = '图片编号';
		$list['list_grids']['pic']['field'] = 'pic';
		$list['list_grids']['pic']['title'] = '图片';
		$list['list_grids']['cate']['field'] = 'cate';
		$list['list_grids']['cate']['title'] = '分类';
		$list['list_grids']['ids']['field'] = 'ids';
		$list['list_grids']['ids']['title'] = '操作';
		$list['list_grids']['ids']['href'] = "editPic&id=[id]|编辑,delPic&id=[id]|删除";
		foreach($list['list_data'] as &$v){
			$v['pic'] = '<img src="'.get_cover_url($v['id']).'" width="100" height="100" />';
			$v['cate'] = $this->_getCateName($v['category_id']);
		}
		$this->assign ( $list );
		$this->display (SITE_PATH.'/Application/Home/View/default/Addons/lists.html');
	}
	public function _getCateName($cate_id){
		$res = D('picture_category')->find($cate_id);
		if($res){
			return $res['name'];
		}else{
			return '无分类';
		}
	}
	public function delPic(){
		! empty ( $ids ) || $ids = I ( 'id' );
		! empty ( $ids ) || $ids = array_filter ( array_unique ( ( array ) I ( 'ids', 0 ) ) );
		! empty ( $ids ) || $this->error ( '请选择要操作的数据!' );

		$map ['id'] = array (
				'in',
				$ids
		);
		$res = D('picture')->where($map)->delete();
		if($res){
			$this->success('删除成功',U('/Home/File/picLists'));
		}else{
			$this->success('删除失败');
		}
	}
	//新增图片
	public function editPic(){
		$fields['id']['type'] = 'picture';
		$fields['id']['name'] = 'id';
		$fields['id']['title'] = '图片';
		$fields['id']['is_show'] = 1;
		
		$fields['category_id']['type'] = 'dynamic_select';
		$fields['category_id']['name'] = 'category_id';
		$fields['category_id']['title'] = '所属分类';
		$fields['category_id']['is_show'] = 1;
		$fields['category_id']['extra'] = 'table=picture_category&value_field=id&title_field=name';
		$id = $_GET['id'];
		if($id){
			$info = D('picture')->find($id);
			$this->assign('data',$info);
		}
		if(IS_POST){
			$data['id'] = $_POST['id'];
			$data['category_id'] = $_POST['category_id'];
			$res = D('picture')->where(array('id'=>$data['id']))->save($data);
			if($res){
				$this->success('保存成功',U('/Home/File/picLists'));
			}else{
				$this->success('保存失败');
			}
		}
		$this->assign('fields',$fields);
		$this->assign('post_url',U('/Home/File/editPic',array('id'=>$id,'mdm'=>I('mdm'))));
		$this->display (SITE_PATH.'/Application/Home/View/default/Addons/edit.html');
	}
	//图片分类
	public function categoryList(){
		$picModel = $this->getModel ( 'picture' );
		$token = get_token();
		$map['token'] = array('in',$token.',1');
		$add_url = U('editCategory',array('mdm'=>I('mdm')));
		$del_url = U('delCategory',array('mdm'=>I('mdm')));
		$this->assign('add_url',$add_url);
		$this->assign('del_url',$del_url);
		$map ['token'] = get_token ();
		$list_data = D('picture_category')->where($map)->select();
		$list['fields'] = array('id','pic','cate');
		$list['list_grids']['id']['field'] = 'id';
		$list['list_grids']['id']['title'] = '分类编号';
		$list['list_grids']['name']['field'] = 'name';
		$list['list_grids']['name']['title'] = '名称';
		$list['list_grids']['ids']['field'] = 'ids';
		$list['list_grids']['ids']['title'] = '操作';
		$list['list_grids']['ids']['href'] = "editCategory&id=[id]|编辑,delCategory&id=[id]|删除";
		$list['list_data'] = $list_data;
		$this->assign ( $list );
		$this->display (SITE_PATH.'/Application/Home/View/default/Addons/lists.html');
	}
	//新增图片分
	public function editCategory(){
		$token = get_token();
		$id = $_GET['id'];
		$fields['name']['type'] = 'text';
		$fields['name']['name'] = 'name';
		$fields['name']['title'] = '分类标题';
		$fields['name']['is_show'] = 1;
		if($id){
			$info = D('picture_category')->find($id);
			$this->assign('data',$info);
		}
		if(IS_POST){
			$data['name'] = $_POST['name'];
			$data['token'] = get_token();
			if(!$id){
				$data['ctime'] = time();
				$res = D('picture_category')->add($data);
			}else{
				$res = D('picture_category')->where(array('id'=>$id))->save($data);
			}
			if($res){
				$this->success('保存成功',U('/Home/File/categoryList'));
			}else{
				$this->success('保存失败');
			}
		}
		$this->assign('fields',$fields);
		$this->assign('post_url',U('/Home/File/editCategory',array('id'=>$id,'mdm'=>I('mdm'))));
		$this->display (SITE_PATH.'/Application/Home/View/default/Addons/edit.html');
	}
	public function delCategory(){
		! empty ( $ids ) || $ids = I ( 'id' );
		! empty ( $ids ) || $ids = array_filter ( array_unique ( ( array ) I ( 'ids', 0 ) ) );
		! empty ( $ids ) || $this->error ( '请选择要操作的数据!' );

		$map ['id'] = array (
				'in',
				$ids
		);
		$res = D('picture_category')->where($map)->delete();
		if($res){
			$this->success('删除成功',U('/Home/File/categoryList'));
		}else{
			$this->success('删除失败');
		}
	}
}
