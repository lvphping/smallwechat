<?php

namespace Common\Model;

use Think\Model;

class TagModel extends Model {
	var $tableName = 'user_tag';
	function addTags($uid, $tag_ids) {
		$ids = array_filter ( explode ( ',', $tag_ids ) );
		
		$data ['uid'] = $uid;
		foreach ( $ids as $id ) {
			$data ['tag_id'] = $id;
			
			$has = M ( 'user_tag_link' )->where ( $data )->getField ( 'id' );
			if (! $has) {
				M ( 'user_tag_link' )->add ( $data );
			}
		}
	}
}
