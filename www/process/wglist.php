<?
require_once('../config/config.php');
if(!CheckLogged()) die('Access denied!');

$debug = false;

// Duong dan webview
$tpldir = WEBSITE_DIR;
$viewdir = str_replace(SERVER_PATH, '', $tpldir);

// Danh sach widget
$widget = [
	'style' => [],
	'list' => []
];
$wglist = $tpldir.'/widget.inc';


if($debug) echo 'TplDir: '.$tpldir.'</br>';
if(file_exists($wglist)) {
	$list = file_get_contents($wglist);
	if($debug) echo 'WgList: '.$list.'<br>';

	$data = json_decode($list, true);
	if($debug) echo 'Data: <pre>'.print_r($data,true).'</pre>';

	foreach($data['style'] as $css){
		$widget['style'][] = $viewdir.'/'.$css;
	}
	foreach($data['list'] as $wg){
		$widget['list'][] = [
			'name' => $wg['name'],
			'path' => '/process/wgview.php?dir='.$viewdir.'&wg='.$wg['path']
		];
	}
}

// Tu dong chen style
if(count($widget['style'])==0) {
	$material = false;
	$check = array(
		'css/materialize.css',
		'css/materialize.min.css',
		'sass/materialize.scss'
	);
	foreach($check as $chk){
		if(file_exists($tpldir.'/'.$chk)) {
			$material = true;
			break;
		}
	}

	if($material) {
		$widget['style'][] = $viewdir.'/css/materialize.css';
	}
	else {
		$bootstrap = false;
		$check = array(
			'css/bootstrap.css',
			'css/bootstrap.min.css'
		);
		foreach($check as $chk){
			if(file_exists($tpldir.'/'.$chk)) {
				$bootstrap = true;
				break;
			}
		}
		if($bootstrap) {
			$widget['style'][] = $viewdir.'/css/bootstrap.min.css';
		}
	}

}

if($debug) echo 'Widget:<pre>'.print_r($widget,true).'</pre>';
else echo json_encode($widget);
?>