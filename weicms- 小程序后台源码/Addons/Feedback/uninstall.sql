DELETE FROM `wp_attribute` WHERE `model_name`='feedback';
DELETE FROM `wp_model` WHERE `name`='feedback' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_feedback`;


