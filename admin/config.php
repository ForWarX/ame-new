<?php
/************************************
 * Only used for local, do not commit
 */

// HTTP
define('HTTP_SERVER', 'http://local.ame-new.com/admin/');
define('HTTP_CATALOG', 'http://local.ame-new.com/');

// HTTPS
define('HTTPS_SERVER', 'http://local.ame-new.com/admin/');
define('HTTPS_CATALOG', 'http://local.ame-new.com/');

define('DIR_ROOT', str_replace("\\", "/", str_replace("\\admin", "", dirname(__FILE__))));
// DIR
define('DIR_APPLICATION', DIR_ROOT . '/admin/');
define('DIR_SYSTEM', DIR_ROOT . '/system/');
define('DIR_IMAGE', DIR_ROOT . '/image/');
define('DIR_LANGUAGE', DIR_ROOT . '/admin/language/');
define('DIR_TEMPLATE', DIR_ROOT . '/admin/view/template/');
define('DIR_CONFIG', DIR_ROOT . '/system/config/');
define('DIR_CACHE', DIR_ROOT . '/system/storage/cache/');
define('DIR_DOWNLOAD', DIR_ROOT . '/system/storage/download/');
define('DIR_LOGS', DIR_ROOT . '/system/storage/logs/');
define('DIR_MODIFICATION', DIR_ROOT . '/system/storage/modification/');
define('DIR_UPLOAD', DIR_ROOT . '/system/storage/upload/');
define('DIR_CATALOG', DIR_ROOT . '/catalog/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // 111111
define('DB_DATABASE', 'ame');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');
