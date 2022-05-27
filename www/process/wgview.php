<?
require_once('../config/config.php');

if(!CheckLogged()) die('Access denied!');
if(empty($_GET['dir']) || empty($_GET['wg'])) die('Access denied!');

$debug = false;
$tpldir = safe($_GET['dir']);
$wgfile = SERVER_PATH.'/'.$tpldir.'/'.safe($_GET['wg']);
if($debug) { 
	echo 'TplDir: '.$tpldir.'</br>';
	echo 'Widget: '.$wgfile.'</br>';
}

$html = file_get_contents($wgfile);
if($debug) echo 'Content: <hr>'.$html.'<hr>';

// Xu ly duong dan widget
$mediadir = $tpldir.'/';

// Doi duong dan trong src|href|background|action
$pattern = "#(href|src|background|action)(=\"|=')(?!/|\#|http|https|ftp|\"|'|javascript:|mailto:|skype:|callto:|tel:|ymsgr:)#i";
$html = preg_replace($pattern, '$1$2'.$mediadir, $html);

// Bo dau nhay trong url()
$quote = "#url\(['\"](.*)['\"]\)#i";
$html = preg_replace($quote, 'url($1)', $html);

// Doi duong dan trong url()
$pattern = "#(url\()(?!\)|/|http|https|ftp)#i";
$html = preg_replace($pattern, '$1$2'.$mediadir, $html);

if($debug) echo 'Html: <hr>'.$html.'<hr>';
else echo $html;
?>