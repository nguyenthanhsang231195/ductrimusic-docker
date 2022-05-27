<?
require_once('../config/config.php');

if(!CheckLogged()) die('Access denied!');
if(empty($_GET['t'])) die('Access denied!');

try{
	$tbl = PREFIX_NAME.safe($_GET['t']).SUPFIX_NAME;
	$d = safe($_GET['d']);
	$o = safe($_GET['o']);
	$p = safe($_GET['p']);
	$f = safe($_GET['f']);

	$rs = OptionFromURL($tbl, $d, $o, $p, $f);
	echo json_encode($rs);
}
catch(Exception $ex){
	$rs = array(
		'Result'	=> 'ERROR',
		'Message'	=> $ex->getMessage()
	);
	echo json_encode($rs);
}
?>