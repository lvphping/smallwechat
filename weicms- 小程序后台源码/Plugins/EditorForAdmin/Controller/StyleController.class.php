<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------
namespace Plugins\EditorForAdmin\Controller;

use Home\Controller\AddonsController;
use Think\Upload;

class StyleController extends AddonsController {
	function get_article_style(){
	    $p = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
	    $row = 15;
		$group_id = I('group_id',0,intval);
		$groupList = M('article_style_group') -> select();
		if($groupList){
			if($group_id==0){
				$groupList[0]['class'] = "current";
				$group_id = $groupList[0]['id'];
			}else{
				foreach($groupList as &$v){
					if($v['id']==$group_id){
						$v['class'] = "current";
					}
				}
			}
			$data = M('article_style') -> where(array('group_id'=>$group_id))->order ( 'id desc' )->page ( $p, $row )->select();
		}
		/* 查询记录总数 */
		$count = M ( 'article_style' )->where ( array('group_id'=>$group_id) )->count ();
		
		$list ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
		    $page = new \Think\Page ( $count, $row );
		    $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
		    $list ['_page'] = $this->show ($count,$row);
		}
		
		$this -> assign('group_list',$groupList);
		$this -> assign('list',$list);
		$this -> display('article_style_list');
	}
	
	public function show($totalRows,$listRows) {
	    $p='p';
	    if(0 == $totalRows) return '';
	    $nowPage    = empty($_GET[$p]) ? 1 : intval($_GET[$p]);
	    $nowPage    = $nowPage>0 ? $nowPage : 1;
	    $rollPage=11;
	    $lastSuffix = true;
	    $config  = array(
	        'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
	        'prev'   => '<<',
	        'next'   => '>>',
	        'first'  => '1...',
	        'last'   => '...%TOTAL_PAGE%',
	        'theme'  => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
	    );
	    
	    /* 生成URL */
	    $parameter[$p] = '[PAGE]';
	    $url = U('plugin/EditorForAdmin/Style/get_article_style',$parameter);
	    /* 计算分页信息 */
	    $totalPages = ceil($totalRows / $listRows); //总页数
	    if(!empty($totalPages) && $nowPage > $totalPages) {
	        $nowPage = $totalPages;
	    }
	
	    /* 计算分页零时变量 */
	    $now_cool_page      = $rollPage/2;
	    $now_cool_page_ceil = ceil($now_cool_page);
	    $lastSuffix && $config['last'] = $totalPages;
	
	    //上一页
	    $up_row  = $nowPage - 1;
	    $up_page = $up_row > 0 ? '<a class="prev" href="' . $this->url($up_row,$url) . '">' . $config['prev'] . '</a>' : '';
	
	    //下一页
	    $down_row  = $nowPage + 1;
	    $down_page = ($down_row <= $totalPages) ? '<a class="next" href="' . $this->url($down_row,$url) . '">' . $config['next'] . '</a>' : '';
	    
	    //第一页
	    $the_first = '';
	    if($totalPages > $rollPage && ($nowPage - $now_cool_page) >= 1){
	        $the_first = '<a class="first" href="' . $this->url(1,$url) . '">' . $config['first'] . '</a>';
	    }
	
	    //最后一页
	    $the_end = '';
	    if($totalPages > $rollPage && ($nowPage + $now_cool_page) < $totalPages){
	        $the_end = '<a class="end" href="' . $this->url($totalPages,$url) . '">' . $config['last'] . '</a>';
	    }
	
	    //数字连接
	    $link_page = "";
	    for($i = 1; $i <= $rollPage; $i++){
	        if(($nowPage - $now_cool_page) <= 0 ){
	            $page = $i;
	        }elseif(($nowPage + $now_cool_page - 1) >= $totalPages){
	            $page = $totalPages - $rollPage + $i;
	        }else{
	            $page = $nowPage - $now_cool_page_ceil + $i;
	        }
	        if($page > 0 && $page != $nowPage){
	
	            if($page <= $totalPages){
	                $link_page .= '<a class="num" href="' . $this->url($page,$url) . '">' . $page . '</a>';
	            }else{
	                break;
	            }
	        }else{
	            if($page > 0 && $totalPages != 1){
	                $link_page .= '<span class="current">' . $page . '</span>';
	            }
	        }
	    }
	
	    //替换分页内容
	    $page_str = str_replace(
	        array('%HEADER%', '%NOW_PAGE%', '%UP_PAGE%', '%DOWN_PAGE%', '%FIRST%', '%LINK_PAGE%', '%END%', '%TOTAL_ROW%', '%TOTAL_PAGE%'),
	        array($config['header'], $nowPage, $up_page, $down_page, $the_first, $link_page, $the_end, $totalRows, $totalPages),
	        $config['theme']);
	    return "<div>{$page_str}</div>";
	}
	
	private function url($page,$url){
	    return str_replace(urlencode('[PAGE]'), $page, $url);
	}
}
