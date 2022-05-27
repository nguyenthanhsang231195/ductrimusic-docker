<?
// Duong dan dac biet
$page = 'index';
if(!empty($_GET['content'])) {
	$url = safe($_GET['content']);
  if($debug) echo "Page URL: $url<br>";
  
  // Auto redirect
  if($url=='news') {
    if(empty($_GET['name'])) Redirect('/kinh-nghiem');
    else {
      $name = safe($_GET['name']);
      Redirect("/kinh-nghiem/$name");      
    }
    exit;
  }
  elseif($url=='product' && !empty($_GET['name'])) {
    $url = safe($_GET['name']);
    if(CheckField($dx,PREFIX_NAME.'product'.SUPFIX_NAME,"URL='$url'")) {
      Redirect("/piano/$url");      
      exit;
    }
  }

  // Loai bai viet
  $type = GetField($dx,PREFIX_NAME."article_type".SUPFIX_NAME,"URL='$url'",'typeID');
  if(!empty($type)) {
    if($type==TYPE_TECHNICAL) $_GET['content'] = 'technical';
    elseif($type==TYPE_SUPPORT) $_GET['content'] = 'support';
    elseif($type==TYPE_INTRODUCE) $_GET['content'] = 'introduce';
    elseif($type==TYPE_PRESS) $_GET['content'] = 'kien-thuc';
    else $_GET['content'] = 'article';

    // Danh sach bai viet
    if(empty($_GET['name'])) $_GET['name'] = $url;
    // Chi tiet bai viet
    else {
      $_GET['name1'] = $url;
      $_GET['name2'] = $_GET['name'];
    }
  }

  // Danh muc san pham
  $type = GetField($dx,PREFIX_NAME.'product_catalog'.SUPFIX_NAME,"URL='$url'",'catID');
  if($type==RINGMEN) $_GET['content'] = 'ringmen';
  elseif($type!='') {
    $_GET['content'] = 'piano';
    $_GET['name'] = $url;
  }

  // Landing page
  if(CheckField($dx,PREFIX_NAME.'fullpage'.SUPFIX_NAME,"URL='$url'")) {
    $_expire = 3600;
		$_GET['content'] = 'fullpage';
		$_GET['name'] = $url;
  }

	$page = safe($_GET['content']);
}
if($debug) echo 'View Param<pre>'.print_r($_GET,true).'</pre>';


// Xu ly cache, danh muc
if($page=='index') $_expire = 600;
elseif($page=='piano') {
  $page = 'product';
	$_expire = 600;

	if(!empty($_GET['name'])) {
		$path = safe($_GET['name']);
		$exist = CheckField($dx,PREFIX_NAME."product_catalog".SUPFIX_NAME,"URL='$path'");
		if(!$exist) {
			$page = 'product-detail';
			$_expire = 3600;
		}
	}
}
elseif($page=='ringmen'){
	$_expire = 600;
	if(!empty($_GET['name'])) {
    $page = 'ringmen-detail';
    $_expire = 3600;
	}
}
elseif($page=='technical'){
	$_expire = 600;
	if(!empty($_GET['name2'])) {
    $page = 'technical-detail';
    $_expire = 3600;
	}
}
elseif($page=='support'){
	$_expire = 600;
	if(!empty($_GET['name2'])) {
    $page = 'support-detail';
    $_expire = 3600;
	}
}
elseif($page=='introduce'){
	$_expire = 600;
	if(!empty($_GET['name2'])) {
    $page = 'introduce-detail';
    $_expire = 3600;
	}
}
elseif($page=='article'){
	$_expire = 600;
	if(!empty($_GET['name'])) {
    $page = 'article-detail';
    $_expire = 3600;
	}
}
elseif($page=='kinh-nghiem') {
  $page = 'news';
  $_expire = 600;

  if(!empty($_GET['name'])) {
    $page = 'news-detail';
    $_expire = 3600;
  }
}
elseif($page=='kien-thuc') {
  $page = 'press';
  $_expire = 600;

  if(!empty($_GET['name'])) {
   $path = safe($_GET['name']);
    $exist = CheckField($dx,PREFIX_NAME."article".SUPFIX_NAME,"URL='$path'");
		if($exist) {
      $page = 'press-detail';
      $_expire = 3600;
    }
  }
}
if($debug) echo "Page: $page <--> {$_expire}s<br>";


// Khoi tao Webview
require_once('core/webview.php');
$wview = new Webview($winfo->webID);

// Render page
$wview->Render($page);
?>