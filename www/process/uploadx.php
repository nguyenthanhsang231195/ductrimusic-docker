<?
require_once('../config/config.php');

// Kiem tra dang nhap
$login = false;
if(($memID=QsvMember())!==false) $login = true;
elseif(($memID=IsLogin())!==false) $login = true;
if(!$login){
  header("HTTP/1.0 403 Access Denied");
  return;
}

// Allow extension
$allowext = array("jpg","jpeg","png","gif","svg");
// Accepted origins
$origins = array(Protocol().'://'.MAIN_DOMAIN);


reset($_FILES);
$temp = current($_FILES);
if (is_uploaded_file($temp['tmp_name'])){
  if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Same-origin requests won't set an origin. If the origin is set, it must be valid.
    if (in_array($_SERVER['HTTP_ORIGIN'], $origins)) {
      header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    }
    else {
      header("HTTP/1.0 403 Origin Denied");
      return;
    }
  }

  header('Access-Control-Allow-Credentials: true');
  header('P3P: CP="There is no P3P policy."');

  // Sanitize input
  if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
      header("HTTP/1.0 500 Invalid file name.");
      return;
  }

  // Verify extension
  $ext = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));
  if (!in_array($ext, $allowext)) {
      header("HTTP/1.0 500 Invalid extension.");
      return;
  }

  // Accept upload if there was no origin, or if it is an accepted origin
  $fname = SaveName($temp['name']);
  $upfile = UploadPath($fname);
  if (move_uploaded_file($temp['tmp_name'],$upfile)) {
    $image = VIEW_PATH.'/'.GetDisplay($upfile);
    
    // Respond to the successful upload with JSON.
    // Use a location key to specify the path to the saved image resource.
    // { location : '/your/uploaded/image/file'}
    $thumb = ThumbImage($image, 800);
    echo json_encode(array('location' => $thumb));
    return;
  }
}

// Notify editor that the upload failed
header("HTTP/1.0 500 Server Error");
?>