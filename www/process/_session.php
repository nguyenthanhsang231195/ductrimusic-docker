<?
include "../config/config.php";
if(!CheckLogged()) exit;

if(isset($_GET['c'])) {
	unset($_SESSION[$_GET['c']]);
}

echo '<pre>'.print_r($_SESSION,true).'</pre>';
?>