CREATE TABLE IF NOT EXISTS `wp_feedback` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`username`  varchar(50) NULL  COMMENT '姓名',
`product`  varchar(100) NULL  COMMENT '关注的产品',
`from`  char(10) NULL  COMMENT '来源渠道',
`area`  varchar(100) NULL  COMMENT '你所在地区',
`score`  int(10) NULL  DEFAULT 0 COMMENT '打分',
`is_dev`  tinyint(2) NULL  DEFAULT 0 COMMENT '是否是前端开发人员',
`cTime`  int(10) NULL  COMMENT '反馈时间',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('feedback','用户反馈表','0','','1','["username","product","from","area","score","is_dev"]','1:基础','','','','','username:姓名\r\nproduct|get_name_by_status:产品\r\nfrom|get_name_by_status:渠道\r\narea|get_name_by_status:地区\r\nscore:打分\r\nis_dev|get_name_by_status:前端\r\ncTime|time_format:反馈时间','10','username:请输入姓名搜索','','1475997729','1476002440','1','MyISAM','Feedback');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('username','姓名','varchar(50) NULL','string','','','1','','0','','0','1','1475997850','1475997850','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('product','关注的产品','varchar(100) NULL','checkbox','','','1','0:微商城\r\n1:微社区\r\n2:乐摇','0','','0','1','1475998834','1475997894','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('from','来源渠道','char(10) NULL','radio','','','1','0:百度搜索来的\r\n1:朋友介绍\r\n2:公众号推送','0','','0','1','1475998910','1475997931','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('area','你所在地区','varchar(100) NULL','dynamic_select','','','1','0:美国\r\n1:中国\r\n2:巴西\r\n3:日本','0','','0','1','1476002411','1475998014','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('score','打分','int(10) NULL','num','0','','1','','0','','0','1','1475998197','1475998197','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('is_dev','是否是前端开发人员','tinyint(2) NULL','bool','0','','1','0:否\r\n1:是','0','','0','1','1475998967','1475998244','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','反馈时间','int(10) NULL','datetime','','','0','','0','','0','1','1475998666','1475998316','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


