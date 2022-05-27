<?php
include "../config/config.php";
if(!CheckLogged()) exit;

if(!empty($_GET['id'])){
	echo '$_GET["id"] = '.$_GET['id'].'<br />';
	$view = '/files/'.safe($_GET['id']);
	$filename = RealFile($view);
}
if($filename=='') {
	$filename = UPLOAD_PATH.'/noimages.jpg';
}

echo '$filename = '.$filename.'<br />';
?>