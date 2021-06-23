<?php

define('OK_LOADME', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

// --- start ---

date_default_timezone_set('Europe/Moscow');

define('DB_HOST', 'localhost');
define('DB_USER', 'Y3M4MjAyM19hcmthbg==');
define('DB_PASSWORD', 'OVFtS2J3RHE=');
define('DB_NAME', 'cs82023_arkan');
define('DB_TBLPREFIX', 'ar');

define('INSTALL_PATH', '/home/c/cs82023/arkan.network/public_html');
define('DECIMAL_POINT', 2);

define('DEFIMG_LOGO', '../assets/image/logo_defaultimage.png');
define('DEFIMG_PLAN', '../assets/image/plan_defaultimage.jpg');
define('DEFIMG_FILE', '../assets/image/file_defaultimage.jpg');
define('DEFIMG_SITE', '../assets/image/site_defaultimage.jpg');
define('DEFIMG_ADM', '../assets/image/adm_defaultimage.jpg');
define('DEFIMG_MBR', '../assets/image/mbr_defaultimage.jpg');

define('ADMFOLDER_NAME', 'admin');
define('MBRFOLDER_NAME', 'member');
define('UIDFOLDER_NAME', 'id');

//ini_set('log_errors', 'On');
//ini_set('error_log', 'ar_error.log');
