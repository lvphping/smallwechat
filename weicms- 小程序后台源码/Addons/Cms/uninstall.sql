DELETE FROM `wp_attribute` WHERE `model_name`='cms';
DELETE FROM `wp_model` WHERE `name`='cms' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_cms`;


