<?
// Composer Packagist
require_once(__DIR__.'/vendor/autoload.php');
// Thanh vien, phan quyen, bao mat
require_once(__DIR__.'/security/authority.php');
// Compress code HTML, CSS, JS
require_once(__DIR__.'/compress/compressor.php');
// Xu ly duong dan: Rewrite, Redirect, AutoLink
require_once(__DIR__.'/rewriteurl.php');
require_once(__DIR__.'/redirect.php');
require_once(__DIR__.'/autolink.php');
// Upload file, thumbnail
require_once(__DIR__.'/storage.php');
// Xu ly cache output
require_once(__DIR__.'/fastcache.php');
// Gui email, ghi log
require_once(__DIR__.'/sendmail.php');
// Xu ly ngon ngu giao dien
require_once(__DIR__.'/language.php');
// Cac ham xu ly cho excel
require_once(__DIR__.'/phpexcel.php');
// Ket noi Webservice / API
require_once(__DIR__.'/httpclient.php');
// Xu ly san pham, bang gia
require_once(__DIR__.'/product.php');
// Xu ly gio hang, don hang
require_once(__DIR__.'/order.php');


//------------------------------------------------------------------------------------
// Extract images from HTML - QsvProgram (21/08/2018)
//------------------------------------------------------------------------------------
use Sunra\PhpSimple\HtmlDomParser;
function ExtractImage($content, $debug = false) {
  $list = [];
  if(empty($content)) return $list;

  $html = HtmlDomParser::str_get_html($content);
  if ($debug) echo '<hr>HTML: ' . htmlentities($html) . '<br>';

  // List figures
  $figures = $html->find('figure');
  foreach ($figures as $figure) {
    if ($debug) echo 'Figure: ' . htmlentities($figure) . '<br>';

    $img = $figure->find('img', 0);
    if ($img) {
      if ($img->parent()->tag == 'a') continue;
      if ($debug) echo ' --> Image: ' . htmlentities($img) . '<br>';

      $src = $img->src;
      if (strpos($src, 'base64') !== false) $src = '';

      $caption = $figure->find('figcaption', 0);
      $alt = $caption->plaintext;
      if ($alt == '') $alt = $img->alt;

      $list[] = [
        'src'  => $src,
        'alt'  => $alt,
        'html' => $figure->outertext,
      ];
    }
    $figure->outertext = '';
  }
  if ($debug) echo 'List:<pre>' . print_r($list, true) . '</pre>';

  $content = $html->save();
  $html->clear();
  $html->load($content);
  if ($debug) echo '<hr>HTML: ' . htmlentities($html) . '<br>';

  // List images
  $images = $html->find('img');
  foreach ($images as $image) {
    if ($image->parent()->tag == 'a') continue;
    if ($debug) echo 'Image: ' . htmlentities($image) . '<br>';

    $src = $image->src;
    if (strpos($src, 'base64') !== false) $src = '';

    $list[] = [
      'src'  => $src,
      'alt'  => $image->alt,
      'html' => $image->outertext,
    ];
    $image->outertext = '';
  }
  if ($debug) echo 'List:<pre>' . print_r($list, true) . '</pre>';

  // Clear memory
  $html->clear();

  return $list;
}

//------------------------------------------------------------------------------------
// Beautiful images - QsvProgram (21/08/2018)
// Lazyload images - QsvProgram (09/12/2020)
//------------------------------------------------------------------------------------
function OptimizeImage($html, $width='lazy', $debug = false) {
  if(empty($html)) return '';

  $find = $replace = [];
  $list = ExtractImage($html, $debug);
  foreach ($list as $img) {
    $find[] = $img['html'];
    $alt = $img['alt'];
    $thumb = ThumbImage($img['src'],300);

    // Check lazyload?
    $lazy = false;
    if($width=='lazy') {
      if($thumb==$img['src']) $lazy = false;
      else {
        $image = $img['src'];
        $realimg = '';

    		// Images from /files/
		    if(substr($image,0,1)=='/' && substr($image,1,1)!=='/') {
          $realimg = RealFile($image);
          if(!file_exists($realimg)) {
            $realimg = $image = Protocol().'://'.STATIC_DOMAIN.$image;
          }
        }
    		// Images from http(s)://
        else {
          $pos = strpos($image, STL_DOMAIN);
          if ($pos !== false) {
            $image = substr($image, $pos + strlen(STL_DOMAIN));
            $realimg = $image = Protocol().'://'.STATIC_DOMAIN.'/'.$image;
          }
          else $realimg = RealFile($image);
        }
        if($realimg=='') $realimg = $image;
        list($width) = getimagesize($realimg);

        if($width>600) $lazy = true;
      }
    }

    if($lazy) {
      $rias	= ThumbImage($img['src'],'{width}');
      $replace[] = '<img src="'.$thumb.'" data-src="'.$rias.'" data-widths="[480,640,800,1280,1600,2560]" data-optimumx="1.6" data-sizes="auto" class="lazyload" alt="'.$alt.'">';
    }
    else {
      $thumb = ThumbImage($img['src'], $width);
      $replace[] = '<img src="'.$thumb.'" alt="'.$alt.'">';
    }
  }
  if (count($find) > 0) {
    if ($debug) {
      echo 'Find:<pre>' . print_r($find, true) . '</pre>';
      echo 'Replace:<pre>' . print_r($replace, true) . '</pre>';
    }
    $html = str_replace($find, $replace, $html);
  }

  return $html;
}

//-------------------------------------------------------------------------------
// Not found page - QsvProgram (03/07/2015)
//-------------------------------------------------------------------------------
function Page404(){
  ob_end_clean();
  ob_start();

  // View page 404
  require_once('webview.php');
  $wview = new Webview();
  $wview->Render('error',404);

	exit;
}

//-------------------------------------------------------------------------------
// Check Secure Connection - QsvProgram (25/01/2017)
//-------------------------------------------------------------------------------
function IsSecure() {
	if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!='off') return true;
	if(isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
		if(strpos($_SERVER['HTTP_X_FORWARDED_PROTO'],'https')!==false) return true;
	}

	return false;
}

//-------------------------------------------------------------------------------
// Network Protocol - QsvProgram (03/03/2017)
//-------------------------------------------------------------------------------
function Protocol() {
	return 'http'.(IsSecure()?'s':'');
}


//-------------------------------------------------------------------------------
// Xac dinh domain dang dung - QsvProgram (14/05/2018)
//-------------------------------------------------------------------------------
function RealDomain(){
	$domain = $_SERVER['HTTP_HOST'];
	$domain = str_replace('www.','',$domain);
	//echo "DEBUG: \$domain=$domain<br>";
	
	return $domain;
}

//-------------------------------------------------------------------------------
// Dang chay localhost? - QsvProgram (08/04/2016)
//-------------------------------------------------------------------------------
function IsLocal() {
	$ipadr = ClientIP();
	if($ipadr=='::1') $ipadr = '127.0.0.1';
	return $ipadr=='127.0.0.1';
}

//-------------------------------------------------------------------------------
// Xac dinh IP hien tai - QsvProgram (31/03/2016)
//-------------------------------------------------------------------------------
function ClientIP(){
    $ip_keys = array(
    	'HTTP_CLIENT_IP',
    	'HTTP_X_FORWARDED_FOR',
    	'HTTP_X_FORWARDED',
    	'HTTP_X_CLUSTER_CLIENT_IP',
    	'HTTP_FORWARDED_FOR',
    	'HTTP_FORWARDED',
    	'REMOTE_ADDR'
    );

    foreach ($ip_keys as $key) {
      if (array_key_exists($key, $_SERVER) === true) {
        foreach (explode(',', $_SERVER[$key]) as $ip) {
      	  $ip = trim($ip);
          if (ValidateIP($ip)) return $ip;
        }
  	  }
    }

    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
}

function ValidateIP($ip) {
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return false;
    }
    return true;
}

//-------------------------------------------------------------------------------
// Duong dan hien tai - QsvProgram (25/11/2015)
//-------------------------------------------------------------------------------
function CurrentURL(){
	$url = Protocol().'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	return $url;
}

//-------------------------------------------------------------------------------
// Moi truong chay PHP - QsvProgram (02/11/2015)
//-------------------------------------------------------------------------------
function IsCLI(){
	$cli = PHP_SAPI == 'cli' || (is_numeric($_SERVER['argc']) && $_SERVER['argc'] > 0);
	if($cli) {
		$_SERVER['HTTP_HOST'] = MAIN_DOMAIN;
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		$_SERVER['REQUEST_URI'] = '/';
		$_SERVER['QUERY_STRING'] = '';
	}
	return $cli;
}


//-------------------------------------------------------------------------------
// Check device is mobile - QsvProgram (01/10/2015)
//-------------------------------------------------------------------------------
function IsMobile(){
	$device = DeviceType();
	$mobile = [
		'phone',
		'tablet'
	];

	return in_array($device, $mobile);
}

//-------------------------------------------------------------------------------
// Device type: computer | tablet | phone  - QsvProgram (01/10/2015)
//-------------------------------------------------------------------------------
function DeviceType(){
    // Set the layout type.
	if(isset($_GET['device'])) {
		$device = safe($_GET['device']);

		switch($device){
			case 'mobi':
			case 'mobile':
				$device = 'phone';
				break;
			case 'pc':
			case 'desktop':
				$device = 'computer';
				break;
		}
	}
	else {
		if(empty($_SESSION['DeviceType'])) {
			$detect = new Mobile_Detect;
			$device = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
		}
		else {
			$device = $_SESSION['DeviceType'];
		}
	}
	
    // Fallback. If everything fails choose computer layout.
	$DeviceType = array('computer','phone','tablet');
    if (!in_array($device, $DeviceType) ) $device = 'computer';
	
	// Store the layout type for future use.
	$_SESSION['DeviceType'] = $device;
	
	return $device;
}

//-------------------------------------------------------------------------------
// Check Apple device - QsvProgram (18/11/2015)
//-------------------------------------------------------------------------------
function IsApple(){
	if(empty($_SESSION['Apple'])) {
		$detect = new Mobile_Detect;
		$_SESSION['Apple'] = $detect->isiOS();
	}

	$apple = $_SESSION['Apple'];
	return $apple;
}

//-------------------------------------------------------------------------------
// Check BlackBerry device - QsvProgram (18/11/2015)
//-------------------------------------------------------------------------------
function IsBlackBerry(){
	if(empty($_SESSION['BlackBerry'])) {
		$detect = new Mobile_Detect;
		$_SESSION['BlackBerry'] = $detect->isBlackBerry();
	}

	$blackberry = $_SESSION['BlackBerry'];
	return $blackberry;
}


//-------------------------------------------------------------------------------
// Tao chuoi ngau nhien gom so va chu - QsvProgram (28-10-2013)
//-------------------------------------------------------------------------------
function RandomString($length=10) {
  $key = '';
$keys = array_merge(range(0,9), range('a','z'), range('A','Z'));
for($i=0; $i<$length; $i++) $key .= $keys[array_rand($keys)];
  return $key;
}

//-------------------------------------------------------------------------------
// HTML to Text (UTF-8) - QsvProgram (02-07-2013)
//-------------------------------------------------------------------------------
function Html2Text($html,$len=128){
	$content = stripslashes($html);
	$text = trim(strip_tags($content));
	return CutString($text,$len);
}

//-------------------------------------------------------------------------------
// Ham cat chuoi UTF8: CutString($text, $limit=25)
//-------------------------------------------------------------------------------
function CutString($str, $len=25) {
  $str = stripslashes($str);
	if(strlen($str)<=$len) return $str;
	
	$str = trim($str);
	$pos = $len;
	while($str[$len]!=' '){
		$len--;
		if($len==0) {
			$len = $pos;
			break;
		}
	}
	
	$str = substr($str,0,$len);
	return $str.' ...';	
}



//-------------------------------------------------------------------------------
// Dinh dang lai ngay thang theo Viet Nam
//-------------------------------------------------------------------------------
function format_date($date,$h=0){
	$time = strtotime($date);
	if($h==0) return date('d/m/Y',$time);
	return date('H:i:s d/m/Y', $time);
}
function format_time($date){
	$time = strtotime($date);
	return date('H:i', $time);
}

//-------------------------------------------------------------------------------
// Dinh dang ngay thang luu Database - QsvProgram (06/06/2012)
//-------------------------------------------------------------------------------
function get_date($date){
	$rs = preg_replace('@(\d{2})[-/](\d{2})[-/](\d{4})@','$3-$2-$1 00:00:00',$date);
	return $rs;
}
function get_datetime($dt){
	$rs = preg_replace('@(\d{2}):(\d{2}) (\d{2})[-/](\d{2})[-/](\d{4})@','$5-$4-$3 $1:$2:00',$dt);
	return $rs;
}

//-------------------------------------------------------------------------------
// So luong, neu ko co thi de N/A
//-------------------------------------------------------------------------------
function format_num($num, $sign='', $zero='0'){
	if(empty($num)) return $zero;
	$view = number_format($num, PRECISION, DECIMAL_POINT, THOUSAND_SEP);
	if($sign!='') $view .= ' '.$sign;
	return $view;
}

//-------------------------------------------------------------------------------
// Dinh dang 1 so thuc theo so le thap phan
//-------------------------------------------------------------------------------
function format_float($float, $decimal='1'){
	if(empty($float)) return 0;
	$view = number_format($float, $decimal, DECIMAL_POINT, THOUSAND_SEP);
	return $view;
}

//-------------------------------------------------------------------------------
// Dinh dang lai tien VNĐ
//-------------------------------------------------------------------------------
function format_money($number=0, $sign='VNĐ', $zero='N/A'){
	if(empty($number)) return $zero;
	
	// string number_format ( float $number [, int $decimals [, string $dec_point, string $thousands_sep]] )
	$decimals = PRECISION; //round($number)==$number ? 0 : PRECISION;
	$money = number_format($number, $decimals, DECIMAL_POINT, THOUSAND_SEP);
	if($sign!='') $money .= ' '.$sign;
	return $money;
}

//-------------------------------------------------------------------------------
// Lay so tien tu chuoi da dinh dang
//-------------------------------------------------------------------------------
function get_money($strmoney,$round=false){
	// Xoa bo hang nghin de tranh loi
	$strmoney = str_replace(THOUSAND_SEP,'',$strmoney);
	if(DECIMAL_POINT==',') { // Neu dau cham thap phan la ',' thi doi thanh '.'
		$strmoney = str_replace(DECIMAL_POINT,'.',$strmoney);
	}
	
	$number = floatval($strmoney);
	if($round) $number = round($number);
	return $number;
}
//-------------------------------------------------------------------------------
// Lay day so chuoi da dinh dang
//-------------------------------------------------------------------------------
function get_number($number,$round=false){
	return get_money($number,$round);
}

//-------------------------------------------------------------------------------
// Chuan hoa chuoi (bo dau, chuyen thanh url)
//-------------------------------------------------------------------------------
function str_normal($str, $sign=false, $url=true) {
	// Chuyen thanh chu thuong
	$str = mb_strtolower($str,'UTF-8');
	
	// Chuyen sang khong dau
	if(!$sign || $url) {
		$str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/','a', $str);
		$str = preg_replace('/(đ)/', 								'd', $str);
		$str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 			'e', $str);
		$str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 						'i', $str);
		$str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/','o', $str);
		$str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 			'u', $str);
		$str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 						'y', $str);
	}

	// Bo khoang trang du
	$str = preg_replace('/^\s+|\s+$/', '', $str);
	$str = preg_replace('/\s{2,}/', ' ', $str);
		
	// Xu ly cho URL
	if($url) {		
		// Loai bo cac tu nhieu
		$str = preg_replace('/[^\w\- ]/', '', $str);
		
		// Chuyen khoang trang thanh dau -
		$str = str_replace(' ', '-', $str);
		$str = str_replace('--', '-', $str);
	}

	return $str;
}

?>