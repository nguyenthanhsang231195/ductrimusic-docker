<?
session_start();

// Che do DEVELOPMENT
define('DEVELOPMENT', true);
define('MAIL_DEBUG', false);
define('COMPRESS_CODE', false);
define('FORCE_SECURE', false);

// Ten mien chinh
define('MAIN_DOMAIN', 'www.ductrimusic.qsv');
define('STATIC_DOMAIN', 'www.ductrimusic.vn');
define('STL_DOMAIN', 'ductrimusic.qsv');

// Multi language
define('MULTI_LANGUAGE', true);
define('DEFAULT_LANGUAGE', 'vn');
define('DEFAULT_NOLINK', true);


// San pham 
define('RINGMEN', '17');

// Loai bai viet
define('TYPE_TECHNICAL', '3');
define('TYPE_SUPPORT', '1');
define('TYPE_INTRODUCE', '2');
define('TYPE_PRESS', '4');
define('TYPE_NEWS', '5');

// Thong so database
define('PREFIX_NAME','qsv_');
define('SUPFIX_NAME','');
$cfg = [
  'user' => 'root',
  'pass' => 'docker',
  'name' => 'ductri_data',
  'host' => 'mysql'
];

// Thong tin email va server
define('ADMIN_MAIL', '');
define('FROM_MAIL', 'noreply@alphacircle.vn');
define('FROM_NAME', 'Duc Tri Music');
define('FROM_SERVER', 'SMTP'); // SMTP | Gmail | Amazon
// Server alphacircle.vn
define('SMTP_HOST', 'mail.alphacircle.vn');
define('SMTP_USER', 'noreply@alphacircle.vn');
define('SMTP_PASS', '12345678');
// Server google.com
define('GMAIL_HOST', 'smtp.gmail.com');
define('GMAIL_USER', 'info@ductrimusic.vn');
define('GMAIL_PASS', '12345678');
// Server amazonses.com
define('AMAZON_MAIL', 'info@ductrimusic.vn');
define('AMAZON_HOST', 'email.amazonaws.com');
define('AMAZON_USER', 'ABCDEFGHIJKLMNOP');
define('AMAZON_PASS', '1234567890');

// Bao mat 2 buoc
define('LOGIN_2STEP', false);
define('VERIFY_BY', 'email');

// Duong dan tuyet doi tren server
$f = str_replace('\\','/',__DIR__);
$d = str_replace('/config','',$f);
if(!defined('SERVER_PATH')) define('SERVER_PATH', $d);

// Cac duong dan Upload, Webview
define('VIEW_PATH',	'/files');
define('UPLOAD_PATH', SERVER_PATH.'/upload');
define('WEBSITE_DIR', SERVER_PATH.'/views');

// Cau hinh cho Cache
define('CACHE_ENABLE', false);
define('CACHE_DRIVER', 'files'); // files | memcache | memcached
define('CACHE_PATH', UPLOAD_PATH.'/cache');
define('CACHE_HOST', '127.0.0.1');
define('CACHE_PORT', '11211');

// Ten cookie & Thoi gian ton tai
define('COOKIE_NAME',	'fwjwt');		// Ten cookie
define('COOKIE_TIME',	86400*30); 		// 30 ngay

// Dinh dang tien hien thi
define('THOUSAND_SEP',	'.');		// phan cach hang nghin
define('DECIMAL_POINT',	',');		// phan cach phan thap phan
define('PRECISION',		0);			// so chu so thap phan

// Connect Database
require_once(SERVER_PATH.'/core/database.php');
$dx = ConnectDB($cfg);
$db = $de = $dx; // Fallback

// Cac ham tien ich
require_once(SERVER_PATH.'/core/utils.php');
?>