<?
//-------------------------------------------------------------------------------
// Build canonical link
//-------------------------------------------------------------------------------
function Canonical($debug=false) {
  $cano = CurrentURL();
  if($debug) echo "Current URL: $cano<br>";

  $cano = preg_replace('/\?.*/','',$cano);
  $cano = preg_replace('/\-pg[0-9]+/','',$cano);
  $cano = trim(str_replace('.html','',$cano),'/');
  if($debug) echo "Canonical: $cano<br>";

  return $cano;
}

//------------------------------------------------------------------------------------
// Auto redirect link - QsvProgram (26/12/2016)
//------------------------------------------------------------------------------------
function AutoRedirect() {
	// Normal link
	$uri = $_SERVER['REQUEST_URI'];
	$shortcut = trim($uri,'/');
  $shortcut = preg_replace('%(/?)\?.*%','',$shortcut);
  $shortcut = preg_replace('%(/?)#.*%','',$shortcut);
	$shortcut = preg_replace('%\.htm(l?)$%','',$shortcut);
	$shortcut = preg_replace('%\-pg\d+$%','',$shortcut);

	$oldlink = '/'.$shortcut;
  //echo "Old Link: $oldlink<br>";

  
	global $dx;
	$s = "SELECT * FROM ".PREFIX_NAME.'seo_redirect'.SUPFIX_NAME."
		  	WHERE Active='1' AND OldLink='$oldlink'";
	if($r = $dx->get_row($s)){
		$newlink = $r->Newlink;
		//echo "New Link: $newlink<br>";

		$key = 'Redirect:'.$uri;
		SetCache($key, $newlink, 86400, 'link');

		// Save time redirect
		UpdateField($dx,PREFIX_NAME.'seo_redirect'.SUPFIX_NAME, 
			"rediID='".$r->rediID."'", "Viewed=Viewed+1, LastTime=NOW()"
		);

		header("HTTP/1.1 301 Moved Permanently" );
		header('Location: '.$newlink);
		exit;
	}
}

//------------------------------------------------------------------------------------
// Redirect to link - QsvProgram (26/09/2016)
//------------------------------------------------------------------------------------
function Redirect($link, $save=true, $cache=true){
	if (is_string($link) && $link!='') {
		// Current URI
		$uri = $_SERVER['REQUEST_URI'];
		if($uri=='' || $uri=='/') return false;

		// Save redirect
		if($save) AddRedirect($uri, $link);

		// Cache redirect
		if($cache) {
			$key = 'Redirect:'.$uri;
			SetCache($key, $link, 86400, 'link');
		}

		header("HTTP/1.1 301 Moved Permanently" );
		header('Location: '.$link);
		return true;
	}

	$msg = 'Redirect Link: <pre>'.print_r($link,true).'</pre>';
	SendError('Redirect Invalid Link', $msg);
}

//------------------------------------------------------------------------------------
// Add redirect to link - QsvProgram (26/09/2016)
//------------------------------------------------------------------------------------
function AddRedirect($old, $new, $force=false){
	if(empty($old) || empty($new)) return false;
	if($old==$new) return false;
	
	global $dx;
	$old = safe(str_replace(
		['http://'.MAIN_DOMAIN,'https://'.MAIN_DOMAIN],
		['',''], $old
	));
	$new = safe(str_replace(
		['http://'.MAIN_DOMAIN,'https://'.MAIN_DOMAIN],
		['',''], $new
	));

  // Normal all link
  $old = '/'.trim($old,'/');
  $new = '/'.trim($new,'/');

	$rediID = GetField($dx,PREFIX_NAME.'seo_redirect'.SUPFIX_NAME,"OldLink='$old'",'rediID');
	if(empty($rediID)){
		$rediID = FirstID($dx,'rediID',PREFIX_NAME.'seo_redirect'.SUPFIX_NAME);
		$active = $force ? 1 : 0;

		$s = "INSERT INTO ".PREFIX_NAME.'seo_redirect'.SUPFIX_NAME."(`rediID`, `OldLink`,
						`Newlink`, `Viewed`, `LastTime`, `Active`, `NgayCN`)
					VALUES('$rediID', '$old', '$new', '1', NOW(), '$active', NOW())";
	}
	else {
		$s = "UPDATE ".PREFIX_NAME.'seo_redirect'.SUPFIX_NAME."
			    SET Newlink='$new', Viewed=Viewed+1, LastTime=NOW() WHERE rediID='$rediID'";
	}
	//echo "Redirect: $s<br>";
	if($dx->query($s)) return $rediID;
	return false;
}
?>