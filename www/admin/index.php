<?
require_once('../config/config.php');

// Xu ly duong dan
$uri = $_SERVER['REQUEST_URI'];
if($uri=='/admin/index.php') $uri = '/admin';
if($uri=='/admin/index') $uri = '/admin';

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

// Ngon ngu, menu admin
language();
require_once(SERVER_PATH.'/config/menu.php');


// Xu ly dang nhap
if (isset($_POST['lEmail']) && isset($_POST['lPass'])){
	$keystring 	= safe($_POST['keystring']);
	$captcha 	= $_SESSION['captcha'];
	unset($_SESSION['captcha']);
	
	echo '<meta charset="utf-8">';
	if($captcha!=$keystring){
		echo "<script>alert('Sai mã bảo vệ, vui lòng kiểm tra lại!');window.location='login.html'</script>";
		exit;
	}
  
	// Check login status
	$status = QsvLogin(safe($_POST['lEmail']), safe($_POST['lPass']));
	if($status===false){
		echo "<script>alert('Bạn sai email hoặc mật khẩu. Vui lòng đăng nhập lại!');window.location='login.html'</script>";
		exit;
	}
	
	echo "<script>window.location='$status'</script>";
	exit;
}

if(CheckLogged()) {
  $s = "SELECT * FROM ".PREFIX_NAME.'website'.SUPFIX_NAME."
        WHERE webID='".WebsiteID()."'";
  if($r = $dx->get_row($s)){
	$web = [
		'title' 		=> stripslashes($r->Tieude),
		'description'	=> stripslashes($r->Mota),
		'keywords'		=> stripslashes($r->Tukhoa),
		'shortcut'		=> ThumbImage($r->Icon),
		'logo'			=> ThumbImage($r->Logo),
		'copyright'		=> stripslashes($r->Copyright)
  ];

  // View module
  $module = $QSV['menu'][0]['href'];
  $get = $post = [];
  if(!empty($_GET['u'])) {
    $module = $_GET['u'];
    $module = str_replace('/admin/','',$module);

    $post = safe($_GET['p']);
    unset($_GET['u']);
    unset($_GET['p']);
    $get = safe($_GET);

    // Change title by module
    $title = '';
    foreach($QSV['top'] as $m){
      if($m['href']==$module) $title = $m['name'];
    }
    foreach($QSV['menu'] as $m){
      // Sub menu
      if(isset($m['sub'])){
        foreach($m['sub'] as $s){
          if($s['href']==$module) $title = $s['name'];
        }
      }
      else {
        if($m['href']==$module) $title = $m['name'];
      }
    }

    if($title!='') $web['title'] = $title.' | '.$web['title'];
  }
  ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title><?=$web['title']?> &lt;S&F&gt;</title>
<meta name='description' content='<?=$web['description']?>'/>
<meta name='keywords' content='<?=$web['keywords']?>'/>
<meta name="author" content="QsvProgram">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap core CSS -->
<link href="/admin/css/bootstrap.css" rel="stylesheet">
<link href="/admin/css/bootstrap-theme.css" rel="stylesheet">

<!-- Font Awesome 3.2.1 -->
<link href="/admin/css/font-awesome.css" rel="stylesheet">
<!--[if IE 7]>
  <link href="/admin/css/font-awesome-ie7.css" rel="stylesheet">
<![endif]-->

<!-- Custom styles for this template -->
<link href="/admin/css/style.css" rel="stylesheet">
<link href="/admin/css/responsive.css" rel="stylesheet">

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
  <script src="/admin/js/html5shiv.js"></script>
  <script src="/admin/js/respond.js"></script>
<![endif]-->

<!-- Favicons -->
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?=$web['shortcut']?>">
<link rel="shortcut icon" href="<?=$web['shortcut']?>">
</head>
<body>
<header>
  <a id="toggler" class="visible-xs" href="#toggle"><i class="icon-reorder"></i></a>
  <div class="logo"><a href="/" target="_blank"><?=$QSV['name']?></a></div>
  <? if(MULTI_LANGUAGE){?>
  <div class="language"><?=lg('Language')?>:
    <form id="frmLang" method="get" action="">
      <input type="hidden" name="u" value="<?=$module?>">
      <?
      $s = "SELECT * FROM ".PREFIX_NAME.'language'.SUPFIX_NAME."
            WHERE Active='1' ORDER BY Thutu";
      if($rs = $dx->get_results($s)){ ?>
      <select name="lang">
        <? foreach($rs as $r){?>
        <option value="<?=$r->lang?>" <?=$r->lang==lc()?'selected':''?>><?=stripslashes($r->Ten)?></option>
        <? }?>
      </select>
      <? }?>
    </form>
  </div>
  <? }?>
  <ul class="welcome">
    <li><?=lg('Welcome')?> <strong><?=QsvName()?></strong></li>
    <?
    foreach($QSV['top'] as $m){
      if(ValidKey($m['key'])){?>
        <li><a href="?u=<?=$m['href']?>"><i class="<?=$m['icon']?>"></i> <span><?=$m['name']?></span></a></li>
      <?
      }
    }
    ?>
  </ul>
</header>
<div id="menu">
  <ul id="sidebar">
  <?
  $no = 0;
  foreach($QSV['menu'] as $m){
  	if(isset($m['sub'])){
      $activ = false;

  	  $sub = $m['sub'];
  	  foreach($sub as $i=>$s) {
        if($module==$s['href']) $activ = true;
        if(!ValidKey($s['key'])) unset($sub[$i]);
      }

  	  if(count($sub)==0) continue;
  	  ?>
    <li class="panel"> <a href="#sm<?=++$no?>" data-toggle="collapse" data-parent="#sidebar"> <i class="<?=$m['icon']?>"></i> <span> <?=$m['name']?> </span> <b class="icon-angle-down"></b> </a>
      <ul id="sm<?=$no?>" class="collapse <?=$activ?'in':''?>">
      <? foreach($sub as $s){?>
        <li <?=$module==$s['href']?'class="active"':''?>> <a href="?u=<?=$s['href']?>"> <i class="<?=$s['icon']?>"></i> <?=$s['name']?> </a> </li>
      <? }?>
      </ul>
    </li>
    <? 
    }
    elseif(ValidKey($m['key'])){?>
    <li> <a href="?u=<?=$m['href']?>"> <i class="<?=$m['icon']?>"></i> <span> <?=$m['name']?> </span> </a> </li>
    <?
    }
  }
  ?>
  </ul>
  <div id="sidebar-collapse"><i class="icon-double-angle-left"></i></div>
</div>
<div id="body">
  <div id="content"></div>
</div>
<footer>
  <p class="text-muted"><?=$web['copyright']?></p>
</footer>

<!-- jQuery & Bootstrap --> 
<script src="//code.jquery.com/jquery-1.11.2.js"></script> 
<script>window.jQuery || document.write('<script src="/admin/js/jquery-1.11.2.js"><\/script>')</script>  
<script src="/admin/js/bootstrap.js"></script>

<!-- jQuery UI -->
<link href="/admin/css/jquery-ui-1.9.2.bootstrap.css" rel="stylesheet">
<!--[if lt IE 9]>
  <link href="/admin/css/jquery.ui.1.9.2.ie.css" rel="stylesheet">
<![endif]--> 
<script src="/admin/js/jquery-ui-1.9.2.js"></script> 

<!-- Extensions -->
<script src="/admin/ext/js-cookie/js.cookie.js"></script> 
<script src="/admin/ext/tinymce/tinymce.min.js"></script> 
<script src="/admin/ext/plupload/plupload.full.js"></script> 
<script src="/admin/ext/sortable/Sortable.min.js"></script>
<link href="/admin/ext/timepicker-addon/jquery-ui-timepicker-addon.css" rel="stylesheet">
<script src="/admin/ext/timepicker-addon/jquery-ui-timepicker-addon.js"></script> 
<link href="/admin/ext/fancybox/jquery.fancybox.css" rel="stylesheet">
<script src="/admin/ext/fancybox/jquery.fancybox.js"></script> 
<link href="/admin/ext/responsive/responsive-tables.css" rel="stylesheet">
<script src="/admin/ext/responsive/responsive-tables.js"></script>

<!-- Main application -->
<script src="/admin/js/application.js"></script>

<?
$link = $module;
if(count($get)>0) {
  $qry = http_build_query($get, null, '&', PHP_QUERY_RFC3986);
  $qry = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $qry);
  $link .= ($qry==''?'':'?').$qry;
}
$post = json_encode($post);
echo "<script>Router('$link',$post)</script>";
?>
</body>
</html>
<? 
  }
}
?>