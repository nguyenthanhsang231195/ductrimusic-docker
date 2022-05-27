<?
$f = str_replace('\\','/',__DIR__);
$d = str_replace('/admin/custom','',$f);
define('SERVER_PATH', $d);
require_once(SERVER_PATH.'/config/config.php');

QsvLogout();
echo "<script>window.location='login.html'</script>";
?>