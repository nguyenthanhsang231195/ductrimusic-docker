<?
include('config/config.php');
ini_set('memory_limit', "256M");

$debug = false;
if($debug) {
	echo "<h2>Input data</h2>";
	echo '$_GET[id] = '.$_GET['id'].'<br>';
}

$file = '';
if(!empty($_GET['id'])){
	$view = '/files/'.safe($_GET['id']);
	if($debug) echo '$view = '.$view.'<br>';

	$real = RealFile($view);
	if($debug) echo '$real = '.$real.'<br>';

  if($real!='' && file_exists($real)) $file = $real;
  elseif($_SERVER['HTTP_HOST']!=STATIC_DOMAIN) {
    $remote = Protocol().'://'.STATIC_DOMAIN.$_SERVER['REQUEST_URI'];
    header("Location: $remote", true, 301);
    exit;
  }
}
if($file=='') $file = UPLOAD_PATH.'/noimages.jpg';
if($debug) echo '$file = '.$file.'<br>';

// Xac dinh file extension
$ext = pathinfo($file, PATHINFO_EXTENSION);

// Tao duong dan thumbnail
$nw = empty($_GET['nw'])?'':safe($_GET['nw']);
$nh = empty($_GET['nh'])?'':safe($_GET['nh']);
if(($nw!='' || $nh!='') && $ext!='gif'){
	list($width,$height) = getimagesize($file);
	if($nw=='') $nw = round(($width/$height)*$nh);
	if($nh=='') $nh = round(($height/$width)*$nw);

	if($nw>=$width || $nh>=$height) $thumbnails = $file;
	else {
		$thumbnails = UPLOAD_PATH."/thumb/".$nw."x".$nh."_".basename($file);
		
		// Xoa cache - QsvProgram (07/02/2015)
		if (file_exists($thumbnails) && filemtime($thumbnails)<=filemtime($file)){
			unlink($thumbnails);
		}
	}
}
else $thumbnails = $file;
if($debug) echo '$thumbnails = '.$thumbnails.'<br>';

// Xu ly tao thumbnails bang GD library
if (!file_exists($thumbnails)){
	// Resize PNG - QsvProgram (13/03/2015)
	if($ext=='png' && file_exists('core/resizepng.php')) {
		require_once('core/resizepng.php');
		$obj = new maximg;
		$obj->load_img($file);
		$obj->resize($nw,$nh);
		$obj->export($thumbnails);
	}
	else {
		// Load data
		$thumb = imagecreatetruecolor($nw, $nh);
		if($ext=='jpg' || $ext=='jpeg')	$src = imagecreatefromjpeg($file);
		if($ext=='gif')	$src = imagecreatefromgif($file);
		if($ext=='png')	$src = imagecreatefrompng($file);
		   
		// ==============================================
		// Crop and Resize	(19-07-2009)
		// ==============================================
		$ratio_x = $width / $nw;		// Ty le chieu rong
		$ratio_y = $height / $nh;		// Ty le chieu cao
		if($ratio_x > $ratio_y ) {
			$ratio = $ratio_y;
			$tget_width = $nw * $ratio;
			$tget_height = $nh * $ratio;
			$tget_x = round(($width - $tget_width)/2);
			$tget_y = 0;
		}
		else {
			$ratio = $ratio_x ;
			$tget_width = $nw * $ratio;
			$tget_height = $nh * $ratio;
			$tget_x = 0;
			$tget_y = round(($height - $tget_height)/2);
		}
			
		// Making transparent background for png
		if($ext=='png'){
			imageantialias($thumb,true);
			imagecolortransparent($thumb, imagecolorallocate($thumb, 0, 0, 0));
			imagealphablending($thumb, true);
		}
			
		// Tien hanh crop va resize anh goc thanh thumnail
		imagecopyresampled ($thumb, $src, 0, 0, $tget_x, $tget_y, $nw, $nh, $tget_width, $tget_height);
	
		// Output
		if($ext=='jpg' || $ext=='jpeg') imagejpeg($thumb,$thumbnails);
		if($ext=='gif') imagegif($thumb,$thumbnails);
		if($ext=='png') imagepng($thumb,$thumbnails);
			
		imagedestroy($src);
		imagedestroy($thumb);
	}
}

switch ($ext){
    case "jpg"  : $type = "jpeg";	break;
    case "png"  : $type = "x-png";	break;
    default  :    $type = $ext;		break;
}

if($debug) {
	echo "<h2>Image data</h2>";
}
else {
	header('Pragma: public');
	header('Cache-control: max-age=86400, public');
	header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
	header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($thumbnails)).' GMT', true, 200);
	header('Content-type: image/'.$type);
	header('Content-transfer-encoding: binary');
	header('Content-length: '.filesize($thumbnails));
}
echo file_get_contents($thumbnails);
?>