<?
//-------------------------------------------------------------------------------
// Tu dong dang nhap - QsvProgram (05/12/2016)
//-------------------------------------------------------------------------------
function AutoLogin() {
	if(empty($_COOKIE['fwauto'])) return false;
  
  $jwt = $_COOKIE['fwauto'];
	$data = JWTDecode($jwt);
	if($data!==false) {
		// Register the session
		$_SESSION['MemID']	= $data['id'];
		$_SESSION['Guid'] 	= $data['guid'];
		JWTSave();	// Save SESSION

		// Update login time
		global $dx;
		UpdateField($dx,PREFIX_NAME.'member'.SUPFIX_NAME,"memID='".$data['id']."'","LogTime='".date("Y-m-d H:i:s")."'");

    return true;
	}
  return false;
}
AutoLogin();

//-------------------------------------------------------------------------------
// Luu du dong dang nhap - QsvProgram (05/12/2016)
//-------------------------------------------------------------------------------
function SaveLogin() {
	$data = [
		'id' 	=> $_SESSION['MemID'],
		'guid'	=> $_SESSION['Guid']
	];
  $jwt = JWTEncode($data);
  setcookie('fwauto', $jwt, time()+COOKIE_TIME, '/');
}

//-------------------------------------------------------------------------------
// Tat tu dong dang nhap - QsvProgram (05/12/2016)
//-------------------------------------------------------------------------------
function OffLogin() {
	JWTClear('fwauto');
}

?>