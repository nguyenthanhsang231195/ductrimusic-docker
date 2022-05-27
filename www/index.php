<?
require_once('config/config.php');

// Xu ly duong dan
$uri = $_SERVER['REQUEST_URI'];
if($uri=='/index.php') $uri = '/';
if($uri=='/index') $uri = '/';

// Bat buoc dung HTTPS
if(FORCE_SECURE && !IsSecure()) {
	$link = 'https://'.MAIN_DOMAIN.$uri;
	header('Location: '.$link);
	exit;
}

// Bat buoc domain chinh
if($_SERVER['HTTP_HOST']!=MAIN_DOMAIN){	
	$link = Protocol().'://'.MAIN_DOMAIN.$uri;
	header('Location: '.$link);
	exit;
}


// Xu ly redirect
$debug = false;

// Cache redirect
$key = 'Redirect:'.$uri;
if(HasCache($key)) {
	$link = GetCache($key);

	if($debug) echo "Cache Redirect: $uri --&gt; $link<br>";
	else Redirect($link,true,false);
	exit;
}
// Auto redirect
AutoRedirect();

// Cache webpage
$_cache = CACHE_ENABLE;
$_expire = -1;
$post = $_POST;
unset($post['keystring']);
if(count($post)>0) $_cache = false;

if($_cache) {
	$_key = CurrentURL();
	if(IsMobile()) $_key = 'Mobile:'.$_key;
	//echo 'Cache key: '.$_key.'<br>';

	if(HasCache($_key)) {
		//echo 'Cache Hit<br>';
		echo GetCache($_key);
		exit;
	}
	else ob_start();
}


// Tien xu ly duong dan
$url = preg_replace('/\?.*/','',$uri);	// Loc bo query string
$url = trim(str_replace('.html','',$url),'/');
$params = explode('/',$url);
URL_Decode($params);
if($debug) {
  echo 'URL Decode<pre>'.print_r($params,true).'</pre>';
  echo 'GET<pre>'.print_r($_GET,true).'</pre>';
  echo 'POST<pre>'.print_r($_POST,true).'</pre>';
}

// Xu ly duong dan
URL_Master($debug);

// Khoi tao ngon ngu
language(false);


// Xac dinh website nao?
$qry = "SELECT * FROM ".PREFIX_NAME.'website'.SUPFIX_NAME."
        WHERE webID='".WebsiteID()."'";
if($winfo = $db->get_row($qry)){
	// Xu ly dong website
	if($winfo->Status==1){
		echo '<meta charset="utf-8">';
		echo 'Website tạm ngừng hoạt động!';
		exit;	
  }
  if($winfo->Status==2){
		echo '<meta charset="utf-8">';
		echo 'Website đã bị khóa!';
		exit;	
	}

	// Xu ly chua kich hoat
	if($winfo->Active==0){
		echo '<meta charset="utf-8">';
		echo 'Website chưa kích hoạt!';
		exit;	
	}
  
	// Xu ly router, view
	include_once('router.php');

	// Finish cache
	if($_expire==-1) $_cache = false;
	if($_cache) {
		$html = ob_get_contents();

		//echo 'Save cache! Expire:'.$_expire;
		SetCache($_key, $html, $_expire);
	}

	exit;
}

// Not found!
Page404();
?>