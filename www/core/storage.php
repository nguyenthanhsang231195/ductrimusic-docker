<?
//-------------------------------------------------------------------------------
// Get thumbnail link of images
//-------------------------------------------------------------------------------
function ThumbImage($path, $nw = null, $nh = null) {
  if ($path == '') return VIEW_PATH . '/qsvpro.jpg';

  // Remove static domain
  $path = str_replace(MAIN_DOMAIN, STATIC_DOMAIN, $path);
  $pos = strpos($path, STATIC_DOMAIN);
  if ($pos !== false) {
    $path = substr($path, $pos + strlen(STATIC_DOMAIN));
    //echo 'New Path: '.$path.'<br>';
  }

  // Resize param
  if ($nw != null || $nh != null) $size = $nw . "x" . $nh . "/";
  if (!empty($size) && strpos($path, VIEW_PATH) === 0) {
    $name = basename($path);
    $path = VIEW_PATH . '/' . $size . $name;
  }

  // Add static domain
  if (substr($path,0,5)=='/data' || substr($path,0,strlen(VIEW_PATH))==VIEW_PATH) {
    $path = Protocol().'://'.STATIC_DOMAIN.$path;
  }

  return $path;
}

//-------------------------------------------------------------------------------
// Create display name of uploaded file - QsvProgram (19-03-2012)
//-------------------------------------------------------------------------------
function GetDisplay($path, $tbl='', $col='', $memID='0') {
	if($path=="") return '';
	if(is_dir($path)) return $path;
	
	global $db, $dx;
	$path = str_replace(UPLOAD_PATH.'/','',$path);
	if($display=GetField($db,PREFIX_NAME.'files'.SUPFIX_NAME,"path='$path'",'display')) return $display;
	else {
		$pinfo = pathinfo($path);
		$name = substr($pinfo['filename'],7).'-'.RandomString();
		$ext = strtolower($pinfo['extension']);

		$display = $name.'.'.$ext;
		$sql  = "INSERT INTO ".PREFIX_NAME.'files'.SUPFIX_NAME."(fileID,path,display,viewTime,tbl,col,memID)
				 VALUES('".FirstID($dx,'fileID',PREFIX_NAME.'files'.SUPFIX_NAME)."','$path','$display',
				 		'".date("Y-m-d H:i:s")."','$tbl','$col','$memID')";
		if($dx->query($sql)) return $display;
	}
	
	return '';
}

//-------------------------------------------------------------------------------
// Return real path of file
//-------------------------------------------------------------------------------
function RealFile($path) {
	if(strpos($path,'/files/')!==0) return $path;

	global $db;
	$dp = basename($path);
	//echo "Display: $dp<br>";

	$fn = '';
	if($path=GetField($db,PREFIX_NAME.'files'.SUPFIX_NAME,"display='$dp'",'path')){		
		$real = UPLOAD_PATH.'/'.$path;
		//echo "RealPath: $real<br>";
		if(file_exists($real)) $fn = $real;
	}

	return $fn;
}

function GetGUID() {
	// The field names refer to RFC 4122 section 4.1.2
	return sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x',
		mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
		mt_rand(0, 65535), // 16 bits for "time_mid"
		mt_rand(0, 4095),  // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
		bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
			// 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
			// (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
			// 8 bits for "clk_seq_low"
		mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node" 
	); 
}

function SafeName($name) {
	$safe = preg_replace('/[^\w\.\-]+/', '-', $name);
	$safe = str_replace('--', '-', $safe);
	return strtolower($safe);
}
function SaveName($name) {
	return date('His').'_'.SafeName($name);
}


//-------------------------------------------------------------------------------
// Makes directory if not exists and return name
//-------------------------------------------------------------------------------
function MakeDir($path, $mode = 0777) {
  $path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
  $e = explode("/", ltrim($path, "/"));
  if(substr($path, 0, 1) == "/") {
      $e[0] = "/".$e[0];
  }
  $c = count($e);
  $cp = $e[0];
  for($i = 1; $i < $c; $i++) {
  $cp .= "/".$e[$i];
  //echo 'DEBUG: current directory '.$cp.'<br/>';
  if(is_dir($cp)) continue;
  //echo 'DEBUG: make directory '.$cp.'<br/>';
      mkdir($cp, $mode) or die('Can not make directory "'.$cp.'"!');
  }
  return $path;
}

//-------------------------------------------------------------------------------
// Tao duong dan upload file - QsvProgram (06/07/2013)
//-------------------------------------------------------------------------------
function UploadDir() {
	$dir = MakeDir(UPLOAD_PATH.'/data/'.date('Ymd'));
	return $dir;
}
function UploadPath($name) {
	$dir = UploadDir();
	$path = $dir.'/'.$name;
	return $path;
}

//-------------------------------------------------------------------------------
// Xu ly Hinh anh trong HTML - QsvProgram (06/11/2016)
//-------------------------------------------------------------------------------
function ImageReplace($content, $title=''){
  $html = stripslashes($content);
  $html = preg_replace(
    '#<img([^>]*) src="([^"]*)"([^>]*)>(?!</a>)#i',
    '<a class="fancybox" title="'.$title.'" href="$2" style="display:block;margin:10px auto"><img src="$2" alt="'.$title.'" width="100%"></a>',
    $html
  );

  return $html;
}


//-------------------------------------------------------------------------------
// Upload anh len server - QsvProgram (15/12/2011)
//-------------------------------------------------------------------------------
function DoUpload($fupload,$tbl='',$col='',$memID=0,$alowext='jpg,jpeg,png,gif,svg'){
	$saveName = '';
	$aFile = $_FILES[$fupload];
	
	if ($aFile["error"]==0){
		$name = strtolower($aFile['name']);
		$fileName = SaveName(basename($name));
		
		// Xu ly anh upload len server
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		$arrExt = explode(',',$alowext);
		if(in_array($ext,$arrExt)) {
			$upfile = UploadPath($fileName);
			if (move_uploaded_file($aFile['tmp_name'],$upfile)) {
				$saveName = VIEW_PATH.'/'.GetDisplay($upfile,$tbl,$col,$memID);
			}
		}
		else{
			echo '<script>alert("Chỉ được phép upload những tập tin dạng '.$alowext.'")</script>';	
		}
	}
	
	return $saveName;
}

//-------------------------------------------------------------------------------
// Upload list anh len server - QsvProgram (05-11-2011)
//-------------------------------------------------------------------------------
function UploadList($fupload,$tbl='',$col='',$memID=0,$alowext='jpg,jpeg,png,gif,svg'){
	$saveName = array();
	$aFile = $_FILES[$fupload];
	
	$arrExt = explode(',',$alowext);
	foreach($aFile['name'] as $i=>$name){
		if ($aFile["error"][$i]==0){
			$name = strtolower($name);
			$fileName = SaveName(basename($name));
			
			$ext = pathinfo($name, PATHINFO_EXTENSION);
			if(in_array($ext,$arrExt)) {
				$upfile = UploadPath($fileName);
				if (move_uploaded_file($aFile['tmp_name'][$i],$upfile)) {
					$saveName[] = VIEW_PATH.'/'.GetDisplay($upfile,$tbl,$col,$memID);
				}
			}
			else{
				echo '<script>alert("Chỉ được phép upload những tập tin dạng '.$alowext.'")</script>';	
			}
		}
	}
	
	return $saveName;
}
?>