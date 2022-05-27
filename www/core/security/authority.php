<?
require_once(__DIR__.'/jwtcookie.php');
require_once(__DIR__.'/autologin.php');
require_once(__DIR__.'/permission.php');


// Tai khoan admin
$QSV['admin'] = [
	'admin' => 'e278019977ab1025447fb562d1f483aa',
	'qsvpro' => '1558dafa98b89ab5196c83414f1b3f8c'
];


//-------------------------------------------------------------------------------
// Encode password with md5
//-------------------------------------------------------------------------------
function EncodePass($pass){
	$temp = '#@WFsc*pro*)09qsv8Hmnv%%2bnr5';
	$encode = md5($pass);
	return md5(substr($encode,5).$temp);
}



//-------------------------------------------------------------------------------
// Username da ton tai? - QsvProgram (30-11-2016)
//-------------------------------------------------------------------------------
function UsernameExist($uname, $memID=0) {
	if($uname=='') return false;

	global $dx;
	$exist = CheckField($dx,PREFIX_NAME.'member'.SUPFIX_NAME,"Username='$uname' AND memID!='$memID'");
	return $exist;
}

//-------------------------------------------------------------------------------
// Email thanh vien da ton tai? - QsvProgram (30-11-2016)
//-------------------------------------------------------------------------------
function EmailExist($email, $memID=0) {
	if(!ValidEmail($email)) return true;

	global $dx;
	$exist = CheckField($dx,PREFIX_NAME.'member'.SUPFIX_NAME,"Email='$email' AND memID!='$memID'");
	return $exist;
}

//-------------------------------------------------------------------------------
// Tao ma thanh vien - QsvProgram (03-11-2014)
//-------------------------------------------------------------------------------
function MemberCode($type, $id){
	$md5 = strtoupper(md5($type.$id));
	$code = 'MEM'.substr($md5,0,5);
	return $code;
}

//-------------------------------------------------------------------------------
// Xac dinh trang thai online - QsvProgram (21/01/2013)
//-------------------------------------------------------------------------------
function MemberMonitor(){
	$time = time();
	$timeout = $time - 60; // 30s de cap nhat lai
	
	// Lay danh sach thanh vien bi timeout
	global $dx;
	$s = "SELECT * FROM ".PREFIX_NAME.'member'.SUPFIX_NAME." WHERE online=1 AND lasttime<$timeout";
	if($rs = $dx->get_results($s)){
	  foreach($rs as $r){
		$memID = $r->memID;
		$logout = date('Y-m-d H:i:s',$r->lasttime);
		
		// Cap nhat thanh vien offline
		$dx->query("UPDATE ".PREFIX_NAME.'member'.SUPFIX_NAME." SET online=0 WHERE memID='$memID'");
		
		// Cap nhat thoi gian thoat cua thanh vien
		$ss = "UPDATE ".PREFIX_NAME.'member_log'.SUPFIX_NAME." SET Logout='$logout',Done='1'
			   WHERE Done='0' AND memID='$memID'";
		$dx->query($ss);
	  }
	}
}

//-------------------------------------------------------------------------------
// Them thanh vien online - QsvProgram (21/01/2013)
//-------------------------------------------------------------------------------
function MemberOnline($memID){
	if(empty($memID)) return false;
	
	global $dx;
	$s = "UPDATE ".PREFIX_NAME.'member'.SUPFIX_NAME."
		  SET online=1,lasttime=".time()." WHERE memID='$memID'";
	if($dx->query($s)){
		// Neu da co luu trang thai roi thi thoat
		if(CheckField($dx,PREFIX_NAME.'member_log'.SUPFIX_NAME,"memID='$memID' AND Done='0'")){
			return true;
		}
		
		// Luu trang thai dang nhap cua nhan vien lai
		$logID = FirstID($dx,'logID',PREFIX_NAME.'member_log'.SUPFIX_NAME);
		$login = $logout = date('Y-m-d H:i:s');
		$ss = "INSERT INTO ".PREFIX_NAME.'member_log'.SUPFIX_NAME."(logID, memID, Login, Logout, Done)
			   VALUES('$logID', '$memID', '$login', '$logout', '0')";
		return $dx->query($ss);
	}
	
	// Xac dinh trang thai online
	MemberMonitor();
	
	return false;
}

//-------------------------------------------------------------------------------
// Cap nhat thanh vien offline - QsvProgram (21/01/2013)
//-------------------------------------------------------------------------------
function MemberOffline($memID){
	if(empty($memID)) return false;
	
	// Xac dinh trang thai online
	MemberMonitor();
	
	// Cap nhat thanh vien offline
	global $dx;
	$s = "UPDATE ".PREFIX_NAME.'member'.SUPFIX_NAME." SET online=0 WHERE memID='$memID'";
	$dx->query($s);
	
	// Cap nhat thoi gian thoat cua thanh vien
	$logout = date('Y-m-d H:i:s');
	$ss = "UPDATE ".PREFIX_NAME.'member_log'.SUPFIX_NAME."
		   SET Logout='$logout',Done='1' WHERE Done='0' AND memID='$memID'";
	return $dx->query($ss);
}

//-------------------------------------------------------------------------------
// Kiem tra trang thai dang nhap - QsvProgram (02/05/2012)
// Bo kiem tra dang nhap trong ACP - QsvProgram (30/03/2016)
//-------------------------------------------------------------------------------
function IsLogin(){
	return CheckMember();
}

//-------------------------------------------------------------------------------
// Lay ten / email thanh vien - QsvProgram (30-04-2012)
//-------------------------------------------------------------------------------
function MemberInfo(){
	if(isset($_SESSION['MemID'])) {
		global $dx;
		return GetField($dx,PREFIX_NAME.'member'.SUPFIX_NAME,"memID='".$_SESSION['MemID']."'",'Name');
	}
	return '';
}

//-------------------------------------------------------------------------------
// Kiem tra trang thai login cua thanh vien - QsvProgram (23-03-2012)
//-------------------------------------------------------------------------------
function CheckMember(){
	// Neu chua dang ky SESSION thi thoat
	if(empty($_SESSION['MemID']) || empty($_SESSION['Guid'])) return false;
	
	// Kiem tra dung thong tin
	global $db;
	$mid = safe($_SESSION['MemID']);
	$guid = safe($_SESSION['Guid']);
	$s = "SELECT memID FROM ".PREFIX_NAME.'member'.SUPFIX_NAME."
			  WHERE memID='$mid' AND Guid='$guid' AND Active='1'";
	if($r = $db->get_row($s)) return $r->memID;

	return false;
}

//-------------------------------------------------------------------------------
// Register Member - QsvProgram (17/05/2016)
// Auto change Customer to Member
//-------------------------------------------------------------------------------
function MemberRegister($email, $pass, $name, $phone='', $adr='', $sex=1, $bird='000-00-00', $verify=true) {
	global $dx;

	$data = array(
		'success' => true,
		'msg' => 'Đăng ký thành công!',
		'memid' => 0
	);

	if(empty($name) || !ValidEmail($email) || empty($pass)){
		$field = [];
		if(empty($name)) $field[] = 'Tên';
		if(empty($email)) $field[] = 'Email';
		if(empty($pass)) $field[] = 'Mật khẩu';
		$field = join(' & ',$field);

		$data['success'] = false;
		$data['msg'] = "Nhập thiếu $field, vui lòng nhập lại!";
		return $data;
	}

	// Kiem tra email trung?
	$memID = 0;
	$s = "SELECT * FROM ".PREFIX_NAME.'member'.SUPFIX_NAME." WHERE Email='$email'";
	if($r = $dx->get_row($s)){
		$memID = $r->memID;

		// Dang la thanh vien
		if($r->Type!=5) {
			$data['success'] = false;
			$data['msg'] = 'Email đã tồn tại trong hệ thống, vui lòng kiểm tra lại!';
			$data['memid'] = $memID;
			return $data;
		}
	}

	// Thong tin thanh vien
	$guid	= GetGUID();
	$type	= 3;	// Loai thanh vien
	$active	= $verify ? 0 : 1;	// Xac thuc thanh vien
	$date 	= date('Y-m-d H:i:s');
	
	// Dang ky thanh vien
	if(empty($memID)){
		$memID	= FirstID($dx,'memID',PREFIX_NAME.'member'.SUPFIX_NAME);
		$URL	= str_normal($name).'-'.$memID;
    $code	= MemberCode($type,$memID);
    
		$s = "INSERT INTO ".PREFIX_NAME.'member'.SUPFIX_NAME."(memID, Email, Pass, Guid, 
              Name, URL, Sex, Birthday, Address, Phone, Code, `Type`, Perm, Active,
              RegTime, LogTime)
          VALUES('$memID', '$email', '".EncodePass($pass)."', '$guid', '$name', '$URL',
                 '$sex', '$bird', '$adr', '$phone', '$code', '$type', '', '$active',
                 '$date', '$date')";
	}
	// Tang cap thanh vien
	else {
		$URL	= str_normal($name).'-'.$memID;
		$code	= MemberCode($type,$memID);

		$s = "UPDATE ".PREFIX_NAME.'member'.SUPFIX_NAME."
          SET Pass='".EncodePass($pass)."', Guid='$guid', Name='$name', URL='$URL',
              Sex='$sex', Birthday='$bird', Address='$adr', Phone='$phone', Code='$code',
              `Type`='$type', Perm='', Active='1', LogTime='$date'
          WHERE memID='$memID'";
	}

	if($dx->query($s)){
		$data['memid'] = $memID;

		// Gui email thong bao
		if($verify) {
			$data['msg'] = 'Đăng ký thành công!\nVui lòng kiểm tra hộp thư để kích hoạt tài khoản.';
			
			$message = EmailTemplate('kichhoattaikhoan.html',array(
				'hoten'		=> $name,
				'username'	=> $email,
				'password'	=> $pass,
				'lienket'	=> Protocol().'://'.MAIN_DOMAIN.URL_Rewrite('verify',$guid)
			));
			SendMail($email,'Kích hoạt tài khoản thành viên từ '.MAIN_DOMAIN,$message);
		}
		else {
			$data['msg'] = 'Chúc mừng bạn đã đăng ký thành công!';
			
			$message = EmailTemplate('dangkytaikhoan.html',array(
				'hoten'		=> $name,
				'username'	=> $email,
				'password'	=> $pass,
				'lienket'	=> Protocol().'://'.MAIN_DOMAIN.URL_Rewrite('member')
			));
			SendMail($email,'Thông tin tài khoản thành viên từ '.MAIN_DOMAIN,$message);
		}
	}
	else{
		$data['success'] = false;
		$data['msg'] = 'Lỗi: Đăng ký thất bại. Vui lòng thử lại sau!';
	}

	return $data;
}

//-------------------------------------------------------------------------------
// Them khach hang vao database, tra ve memID
// Neu da ton tai thi tra ve memID - QsvProgram (06-11-2011)
// Khach hang --> Thanh vien (Type=5) - QsvProgram (31-10-2014)
//-------------------------------------------------------------------------------
function AddCustomer($email, $name, $address, $tel, $mid=0){
	if(empty($email)) return false;
	
	global $dx;
	$memID = GetField($dx,PREFIX_NAME.'member'.SUPFIX_NAME,"Email='$email'",'memID');
	if(empty($memID)){
		$memID = FirstID($dx,'memID',PREFIX_NAME.'member'.SUPFIX_NAME);
		$URL = str_normal($name).'-'.$memID;
		
		$s = "INSERT INTO ".PREFIX_NAME.'member'.SUPFIX_NAME."(memID, Email, Name, URL, 
					Address, Phone, RegTime, LogTime, `Type`, makerID)
			  VALUES('$memID', '$email', '$name', '$URL', '$address', '$tel', 
			  		 NOW(), NOW(), '5', '$mid')";
		$dx->query($s);
		if($dx->rows_affected==0) return false;
	}
	
	return $memID;
}

//-------------------------------------------------------------------------------
// Dang nhap cho thanh vien - QsvProgram (02/05/2012)
// Add auto login option - QsvProgram (05/12/2016)
//-------------------------------------------------------------------------------
function MemberLogin($user, $pass, $auto=false){
	if(empty($user) || empty($pass)) return false;
	
	// Kiem tra license
	CheckLicense();

	global $dx;
	$s = "SELECT * FROM ".PREFIX_NAME.'member'.SUPFIX_NAME." 
		  WHERE Email='".safe($user)."' AND Pass='".EncodePass($pass)."' AND Active='1'";		
	if($r = $dx->get_row($s)){
		$loginID = $r->memID;
		$guid = GetGUID();

		// Update login time va guid
		UpdateField($dx,PREFIX_NAME.'member'.SUPFIX_NAME, "memID='".$r->memID."'",
			"Guid='$guid', LogTime='".date("Y-m-d H:i:s")."'"
		);

		$_SESSION['MemID'] = $loginID;
		$_SESSION['Guid']  = $guid;
		JWTSave();	// Save SESSION
		
		// Save auto login
		if($auto) SaveLogin();

		return true;
	}
	
	return false;
}

//-------------------------------------------------------------------------------
// Login by Username - QsvProgram (12/04/2016)
// Save auto login option - QsvProgram (05/12/2016)
//-------------------------------------------------------------------------------
function UserLogin($user, $pass, $save=false){
	global $db;
	$email = GetField($db,PREFIX_NAME.'member'.SUPFIX_NAME, "Username='".safe($user)."'","Email");
	if(empty($email)) $email = $user;

	return MemberLogin($email, $pass, $save);
}

//-------------------------------------------------------------------------------
// Xu ly thoat thanh vien - QsvProgram (02/05/2012)
//-------------------------------------------------------------------------------
function MemberLogout(){
	// Xoa toan bo SESSION
	unset($_SESSION['MemID']);
	unset($_SESSION['Guid']);
	
	// Tat tu dong dang nhap
	OffLogin();

	JWTSave();	// Save SESSION
}

//-------------------------------------------------------------------------------
// Quan tri vien? - QsvProgram (09-04-2012)
//-------------------------------------------------------------------------------
function IsAdmin($memID){
	if($memID==0) return true;
	
	global $db;
	return GetField($db,PREFIX_NAME.'member'.SUPFIX_NAME,"memID='$memID'",'Type')=='1';
}


//-------------------------------------------------------------------------------
// Lay ten thanh vien QsvAdmin - QsvProgram (25/11/2013)
//-------------------------------------------------------------------------------
function QsvName(){
	$name = '';
	if(isset($_SESSION['QsvID'])) {
		global $dx;
		$name = GetField($dx,PREFIX_NAME.'member'.SUPFIX_NAME,"memID='".$_SESSION['QsvID']."'",'Name');
		if($name=='') $name = 'Administrator';
	}
	
	return $name;
}

//-------------------------------------------------------------------------------
// Lay ma so thanh vien QsvAdmin - QsvProgram (18/04/2015)
//-------------------------------------------------------------------------------
function QsvMember(){
	// Neu chua dang ky SESSION thi thoat
	if(empty($_SESSION['QsvID']) || empty($_SESSION['Quid'])) return false;
	
	// Kiem tra dung thong tin
	$mid = safe($_SESSION['QsvID']);
	$quid = safe($_SESSION['Quid']);

	// Kiem tra tai khoan QsvAdmin
	global $QSV;
	if(isset($QSV['admin'][$mid])) {
		$pswd = $QSV['admin'][$mid];
		if($quid==md5("$mid@$pswd#5011")) return $mid;
	}

	// Kiem tra tai khoan thanh vien
	global $db;
	$s = "SELECT memID FROM ".PREFIX_NAME.'member'.SUPFIX_NAME."
			  WHERE memID='$mid' AND Guid='$quid' AND Active='1'";
	if($r = $db->get_row($s)) return $r->memID;
	
	return false;
}

//-------------------------------------------------------------------------------
// Kiem tra dang nhap QsvAdmin - QsvProgram (18/04/2015)
// Cap nhat duong dan login - QsvProgram (06/05/2015)
//-------------------------------------------------------------------------------
function CheckLogged($url='/admin/login.html'){
	// Kiem tra trang thai dang nhap
	if(!empty($_SESSION['QsvID']) && !empty($_SESSION['Quid'])){
		// Kiem tra dung thong tin
		$mid = safe($_SESSION['QsvID']);
		$quid = safe($_SESSION['Quid']);

		// Kiem tra tai khoan QsvAdmin
		global $QSV;
		if(isset($QSV['admin'][$mid])) {
			$pswd = $QSV['admin'][$mid];
			if($quid==md5("$mid@$pswd#5011")) $memID = $mid;
		}
		// Kiem tra tai khoan thanh vien
		else{
			global $db;
			$s = "SELECT memID FROM ".PREFIX_NAME.'member'.SUPFIX_NAME."
						WHERE memID='$mid' AND Guid='$quid' AND Active='1'";
			if($r = $db->get_row($s)) $memID = $r->memID;
		}
	}
	
	// Neu khong ton tai $memID thi yeu cau dang nhap
	if(!isset($memID)){
		echo "<script>window.location='$url'</script>";
		return false;
	}
	
	// Neu da log vao thi tra ve true
	return true;
}

//-------------------------------------------------------------------------------
// Dang nhap cho QsvAdmin - QsvProgram (18/04/2015)
// Nang cap bao mat 2 lop - QsvProgram (30/01/2017)
//-------------------------------------------------------------------------------
function QsvLogin($user, $pass){
	if(empty($user) || empty($pass)) return false;

	// Website bi khoa?
	global $dx;
	$status = GetField($dx,PREFIX_NAME.'website'.SUPFIX_NAME,
    "webID='".WebsiteID()."'",'Status'
  );
	if($status==2) return false;

	// Bao mat 2 lop
	if(defined('LOGIN_2STEP') && LOGIN_2STEP) {
		return LoginMatch($user, $pass);
	}

	// Kiem tra tai khoan QsvAdmin
	global $QSV;
	if(isset($QSV['admin'][$user])){
		$pswd = $QSV['admin'][$user];
		if($pswd==EncodePass($pass)){
			$quid = md5("$user@$pswd#5011");
			QsvLoginSave($user, $quid);

			return '/admin/';
		}
	}
	
	// Kiem tra tai khoan thanh vien
	$s = "SELECT * FROM ".PREFIX_NAME.'member'.SUPFIX_NAME." 
		 		WHERE Email='".safe($user)."' AND Pass='".EncodePass($pass)."' AND Active='1'";
	if($r = $dx->get_row($s)){
		$loginID = $r->memID;
		$quid = GetGUID();
		
		// Update login time va guid
		UpdateField($dx, PREFIX_NAME.'member'.SUPFIX_NAME, "memID='$loginID'",
			"Guid='$quid', Retry='0', LogTime='".date("Y-m-d H:i:s")."'"
		);
		
		// Luu dang nhap
		QsvLoginSave($loginID, $quid);
		
		// Luu thong tin online cua thanh vien
		MemberOnline($loginID);
		
		return '/admin/';
	}

	return false;
}

//-------------------------------------------------------------------------------
// Luu dang nhap cho QsvAdmin - QsvProgram (01/02/2017)
//-------------------------------------------------------------------------------
function QsvLoginSave($user, $quid){
	$_SESSION['QsvID'] = $user;
	$_SESSION['Quid']	 = $quid;		
	JWTSave();	// Save SESSION
}

//-------------------------------------------------------------------------------
// Login Step 1 for QsvAdmin - QsvProgram (31/01/2017)
// Check match Email & Password -> Send token email
//-------------------------------------------------------------------------------
function LoginMatch($user, $pass){
	global $dx;
	$match = false;
	$memID = 0;
	$email = $name = '';

	// Kiem tra tai khoan QsvAdmin
	global $QSV;
	if(isset($QSV['admin'][$user])){
		$pswd = $QSV['admin'][$user];
		if($pswd==EncodePass($pass)) {
			$match = true;
			$memID = 1;
			$email = GetField($dx,PREFIX_NAME.'member'.SUPFIX_NAME,"memID='$memID'",'Email');
			$name = 'Administrator';
		}
	}
	
	// Kiem tra tai khoan thanh vien
	if(!$match) {
		$s = "SELECT * FROM ".PREFIX_NAME.'member'.SUPFIX_NAME." 
					WHERE Email='".safe($user)."' AND Pass='".EncodePass($pass)."' AND Active='1'";
		if($r = $dx->get_row($s)) {
			$match = true;
			$memID = $r->memID;
			$email = $r->Email;
			$name = stripslashes($r->Name);
		}
	}

	// Neu dung Email & Password
	if($match) {
		// Tạo token lưu vào database
		$token = GetGUID();
		$next15m = strtotime('+15 minutes');
		$expire = date('Y-m-d H:i:s', $next15m);
		UpdateField($dx, PREFIX_NAME.'member'.SUPFIX_NAME, "memID='$memID'",
			"Token='$token', Expire='$expire', Retry='0'"
		);

		if(VERIFY_BY=='email') {
			// Gửi token vào email user
			$message = EmailTemplate('mabaomat2lop.html', [
				'hoten'	=> $name,
				'username' => $user,
				'token' => $token,
				'lienket' => 'http://'.MAIN_DOMAIN.'/admin/verify.php?token='.$token
			]);
			SendMail($email,'Mã bí mật tài khoản '.$user.' từ '.MAIN_DOMAIN,$message);
		}
		elseif(VERIFY_BY=='sms') {
			// Gui SMS cho SĐT user
			//...
		}

		return '/admin/verify.php';
	}

	return false;
}

//-------------------------------------------------------------------------------
// Login Step 2 for QsvAdmin - QsvProgram (01/02/2017)
// Verify token & create SESSION login
//-------------------------------------------------------------------------------
function LoginToken($token){
	if(empty($token)) return 'empty';
	
	global $dx;
	$memID = GetField($dx,PREFIX_NAME.'member'.SUPFIX_NAME,"Token='$token'",'memID');

	// Kiem tra hop le
	if(empty($memID)) return 'wrong';

	// Kiem tra het han
	$now = date('Y-m-d H:i:s');
	$expire = CheckField($dx,PREFIX_NAME.'member'.SUPFIX_NAME,"memID='$memID' AND Expire<'$now'");
	if($expire) return 'expire';

	// Kiem tra tai khoan QsvAdmin
	global $QSV;
	if($memID==1){
		$pswd = $QSV['admin']['admin'];
		$quid = md5("admin@$pswd#5011");
		QsvLoginSave('admin', $quid);

		return true;
	}
	
	// Kiem tra tai khoan thanh vien
	$s = "SELECT * FROM ".PREFIX_NAME.'member'.SUPFIX_NAME."
				WHERE memID='$memID' AND Active='1'";
	if($r = $dx->get_row($s)){
		$loginID = $r->memID;
		$quid = GetGUID();
		
		// Update login status
		UpdateField($dx, PREFIX_NAME.'member'.SUPFIX_NAME, "memID='$loginID'",
			"Guid='$quid', Token='', Retry='0', Expire='$now', LogTime='$now'"
		);

		// Luu dang nhap
		QsvLoginSave($loginID, $quid);
		
		// Luu thong tin online cua thanh vien
		MemberOnline($loginID);
		
		return true;
	}

	return false;
}

//-------------------------------------------------------------------------------
// Xu ly thoat cho QsvAdmin - QsvProgram (20/11/2013)
//-------------------------------------------------------------------------------
function QsvLogout(){
	// Luu thong tin offline cua thanh vien
	MemberOffline($_SESSION['QsvID']);
	
	// Xoa toan bo SESSION
	unset($_SESSION['QsvID']);
	unset($_SESSION['Quid']);

	JWTSave();	// Save SESSION
}


//-------------------------------------------------------------------------------
// Fix Json UTF-8 (Convert HEX to DEC) - QsvProgram (20/10/2013)
// Convert UTF8-HTML sign &#301; to UTF8 - QsvProgram (01/12/2014)
//-------------------------------------------------------------------------------
function FixJsonUTF8($json){
	if(empty($json)) return '';
	
	$fixed = preg_replace_callback('/\\\u(\w\w\w\w)/', 'HtmlUTF8', $json);
	$fixed = str_replace("\/","/",$fixed);
	$fixed = str_replace("'","&prime;",$fixed);
	return $fixed;
}
function HtmlUTF8($m){
	$dec = hexdec($m[1]);
	if ($dec < 128) {
		$utf = chr($dec);
	}
	else if ($dec < 2048) {
		$utf = chr(192 + (($dec - ($dec % 64)) / 64));
		$utf .= chr(128 + ($dec % 64));
	}
	else {
		$utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
		$utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
		$utf .= chr(128 + ($dec % 64));
	}
	return $utf;
}

//-------------------------------------------------------------------------------
// Remove invalid UTF-8 characters for XML - QsvProgram (04/05/2016)
// Allow input array string - QsvProgram (09/05/2016)
//-------------------------------------------------------------------------------
function RemoveInvalidUTF8($utf8) {
	if(is_array($utf8)) {
		foreach($utf8 as $k=>$v) $utf8[$k] = RemoveInvalidUTF8($v);
		return $utf8;
	}

	// Strip invalid UTF-8 byte sequences
	$utf8 = mb_convert_encoding($utf8, 'UTF-8', 'UTF-8');

	// Remove various characters not allowed in XML
	$utf8 = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '?', $utf8);

	return $utf8;
}


//-------------------------------------------------------------------------------
// Display file for QsvAdmin - QsvProgram (20/09/2013)
// Add remove button - QsvProgram (18/09/2014)
//-------------------------------------------------------------------------------
function FileDisplay($file,$rm=true){
	if(empty($file)) return '';
	
	$view = '<a href="'.$file.'" target="_blank">Download</a>';
	if($rm) {
		$view .= '<div class="removef" onclick="RemoveImage(this)"><i class="icon-remove"></i> '.lg('Delete').'</div>';
		$view .= '<div class="handler"><i class="icon-move"></i></div>';
	}
	return $view;
}

//-------------------------------------------------------------------------------
// Display image for QsvAdmin - QsvProgram (20/09/2013)
// Add remove button - QsvProgram (18/09/2014)
//-------------------------------------------------------------------------------
function ImageDisplay($img,$size=50,$rm=true){
	if(empty($img)) $img = VIEW_PATH.'/qsvpro.jpg';
	
	$thumb = ThumbImage($img,$size,$size);
	$display = '<img src="'.$thumb.'" width="'.$size.'" height="'.$size.'" alt="" />';
	$view = '<a href="#display" onclick="return Fancybox(\''.$img.'\')">'.$display.'</a>';
	if($rm) {
		$view .= '<div class="removei" onclick="RemoveImage(this)"><i class="icon-remove"></i></div>';
		$view .= '<div class="handler"><i class="icon-move"></i></div>';
	}
	return $view;
}

//-------------------------------------------------------------------------------
// Pagination for QsvAdmin - QsvProgram (13/08/2013)
//-------------------------------------------------------------------------------
function LoadPage($total, $page=1, $url='', $filter=array()){
	// Tinh lai trang bat dau va ket thuc cho hop ly
	$start = ($page-3)<1 ? 1 : $page-3;
	$end = $start + 6;
	if($end>$total){
		$end = $total; 
		$start = ($end-6)<1 ? 1 : $end-6;
	}
	
	// Them filter
	$param = array();
	if(count($filter)>0) $param['f'] = $filter;
	
	// Xuat du lieu ra website
	if ($total >1) {
		$rs = '<div class="page"><ul class="pagination pagination-sm">';
		
		// Trang truoc
		if($page==1) $rs .= '<li class="disabled"><span>&laquo;</span></li>';
		else{
			$param['page'] = 1;
			$post = json_encode($param);
			$rs .= "<li><a href=\"#prev\" onclick='return Load(\"$url\",$post)'>&laquo;</a></li>";
		}
		
		// Danh sach cac trang
		for($i=$start; $i<=$end;$i++){
			if($i==$page) $rs .= "<li class=\"active\"><span>$i</span></li>";
			else{
				$param['page'] = $i;
				$post = json_encode($param);
				$rs .= "<li><a href=\"#page$i\" onclick='return Load(\"$url\",$post)'>$i</a></li>";
			}
		}
		
		// Trang sau
		if($page==$total) $rs .= '<li class="disabled"><span>&raquo;</span></li>';
		else{
			$param['page'] = $page+1;
			$post = json_encode($param);
			$rs .= "<li><a href=\"#next\" onclick='return Load(\"$url\",$post)'>&raquo;</a></li>";
		}
		
		$rs .= '</ul></div>';
		echo $rs;
	}
}

//-------------------------------------------------------------------------------
// Parser query string from url - QsvProgram (19-12-2015)
// Fix error for URL with filter: &f=chID=0
// Originally written by xellisx
//-------------------------------------------------------------------------------
function ParseQuery($url) {
	$var  = parse_url($url, PHP_URL_QUERY);
	$var  = html_entity_decode($var);
	$var  = explode('&', $var);

	$arr  = array();
	foreach($var as $get) {
		$p = strpos($get, '=');
		$k = substr($get, 0, $p);
		$v = substr($get, $p+1);
		$arr[$k] = $v;
	}
	//echo 'ParseQuery: <pre>'.print_r($arr,true).'</pre>';

	return $arr;
}

//-------------------------------------------------------------------------------
// View option from input - QsvProgram (19-12-2015)
//-------------------------------------------------------------------------------
function OptionFromURL($tbl, $d, $o='', $p='', $f=''){
	global $db;

	$key = TableKey($db, $tbl);
	$opt = array();
	
	// Choose catalog
	$opt[] = array(
		'Value'			=> 0,
		'DisplayText'	=> ''
	);
	
	// Condition
	$wh = '';
	if(!empty($p)) $wh .= ($wh==''?'WHERE ':' AND ')."$p='0'";
	if(!empty($f)) $wh .= ($wh==''?'WHERE ':' AND ').$f;
	
	// Order by
	$ob = '';
	if(!empty($o)) $ob = "ORDER BY $o";
  
  // Query
	$s = "SELECT * FROM $tbl $wh $ob";
	//echo "DEBUG: $s<br>";
	if($rs = $db->get_results($s)){
	  foreach($rs as $r){
      $name = RemoveInvalidUTF8(stripslashes($r->$d));
      $opt[] = array(
        'Value'			=> $r->$key,
        'DisplayText'	=> $name
      );
      
      if(!empty($p)){
        $ww = "WHERE $p='".$r->$key."'";
        if(!empty($f)) $ww .= " AND $f";

        $ss = "SELECT * FROM $tbl $ww $ob";
        if($rrs = $db->get_results($ss)){
          foreach($rrs as $rr){
            $text = $name.' / '.RemoveInvalidUTF8(stripslashes($rr->$d));
            $opt[] = array(
              'Value'			=> $rr->$key,
              'DisplayText'	=> $text
            );
          }
        }
      }
	  }
	}
	
	$rs = array(
		'Result'	=> 'OK',
		'Options'	=> $opt
	);
	//echo 'Option<pre>'.print_r($rs,true).'</pre>';

	$js = json_encode($rs, JSON_PARTIAL_OUTPUT_ON_ERROR);
	//echo 'Json<pre>'.print_r($rs,true).'</pre>';

	return $js;
}

//-------------------------------------------------------------------------------
// Get content from url - QsvProgram (19-12-2015)
//  - Luu cache 1h de tang toc do
//  - Them cache rieng theo key (neu co)
//  - Fix loi url co khoang trang
//  - Xu ly goi ham OptionFromURL
//-------------------------------------------------------------------------------
function GetContent($url, $guid=''){
	$url = str_replace('+',' ',$url);
	//echo "<b>GetContent: </b>$url<br>";

	$key = 'Content:'.$url.$guid;
	if(HasCache($key)) {
		$content = GetCache($key);
		return $content;
	}

	$data = ParseQuery($url);
	//echo '<pre>'.print_r($data,true).'</pre>';

	$tbl = PREFIX_NAME.safe($data['t']).SUPFIX_NAME;
	$content = OptionFromURL($tbl, $data['d'], $data['o'], $data['p'], $data['f']);
	
	// Them noi dung vao cache
	SetCache($key,$content,3600);
	
	return $content;
}

//-------------------------------------------------------------------------------
// Xac dinh webID - QsvProgram (15-05-2018)
// Xac dinh theo ngon ngu - QsvProgram (18-05-2018)
//-------------------------------------------------------------------------------
function WebsiteID($lang=''){
  $wh = "Active='1'";
  if(MULTI_LANGUAGE) {
    if(empty($lang)) $lang = lc();
    $wh .= " AND lang='$lang'";
  }
  
  global $dx;
  $webID = GetField($dx,PREFIX_NAME.'website'.SUPFIX_NAME, $wh, 'webID');
	return $webID;
}


//-------------------------------------------------------------------------------
// Xac dinh key cua table - QsvProgram (04/07/2013)
//-------------------------------------------------------------------------------
function TableKey($dx,$tbl){
	// Chi tiet tung fields
	$key = '';
	$s = "SHOW FULL COLUMNS FROM $tbl";
	if($rs = $dx->get_results($s)){
	  foreach($rs as $r){
		if($r->Key=='PRI'){
			$key = $r->Field;
			break;
		}
	  }
	}
	
	return $key;
}

//-------------------------------------------------------------------------------
// Chi tiet tung fields cua table - QsvProgram (18/04/2015)
//-------------------------------------------------------------------------------
function TableField($dx,$tbl){
	// Chi tiet tung fields
	$fields = array();
	$s = "SHOW FULL COLUMNS FROM $tbl";
	if($rs = $dx->get_results($s)){
	  foreach($rs as $r){
		if($r->Key=='PRI'){
			$fields[$r->Field] = array(
				'key'	=> true,
				'create'=> false,
				'edit'	=> false,
				'list'	=> false
			);
		}
		else{
			$field = array(
				'title'			=> $r->Field,
				'create'		=> false,
				'edit'			=> false,
				'list'			=> false,
				'sorting'		=> true,
				'defaultValue'	=> $r->Default,
				'display'		=> null,
				'type'			=> 'text',
					// password
					// textarea
					// editor
					// file, fileType: "image"
					// date, displayFormat: "yy-mm-dd"
					// time, timeFormat: "HH:mm:ss"
					// datetime, displayFormat: "yy-mm-dd", timeFormat: "HH:mm:ss"
					// radiobutton
					// combobox
					// checkbox, values: {"0": "Disable", "1": "Active"}
					// hidden
				'options'		=> null,
					// {"0":"Female", "1":"Male", "2":"Other"}
					// /process/opt.php?t=table&d=name&o=name&p=prid
				'width'			=> '10%',
			);
			
			// Default value for text
			if($r->Type=='date') $field['type'] = 'date';
			elseif($r->Type=='time') $field['type'] = 'time';
			elseif($r->Type=='datetime') $field['type'] = 'datetime';
			elseif($r->Type=='text') $field['type'] = 'textarea';
			elseif($r->Type=='longtext') $field['type'] = 'editor';
			
			if($r->Comment!=''){
			  $comnt = json_decode(stripslashes($r->Comment),true);
			  if(is_array($comnt)){
				foreach($comnt as $k=>$v) $field[$k] = $v;
			  }
			}
			foreach($field as $k=>$v){
				if($v===null) unset($field[$k]);
			}
			
			$fields[$r->Field] = $field;
		}
	  }
	}
	
	return $fields;
}

//-------------------------------------------------------------------------------
// Thong tin cua table - QsvProgram (18/04/2015)
//-------------------------------------------------------------------------------
function TableInfo($dx,$tbl){
	// Tieu de table
	$title = str_replace(array(PREFIX_NAME,SUPFIX_NAME),array('',''),$tbl);
	$title[0] = strtoupper($title[0]);
	
	$table = array(
		'title'					=> $title,
		'paging'				=> true,
		'pageSize'				=> 20,
		'filter'				=> true,
		'defaultSorting'		=> null,
		'sorting'				=> true,
		'multiSorting'			=> false,
		'selecting'				=> true,
		'multiselect'			=> true,
		'selectingCheckboxes'	=> true,
		'selectOnRowClick'		=> false,
	);
	
	$s = "SHOW TABLE STATUS WHERE Name='$tbl'";
	if($r = $dx->get_row($s)) $comnt = stripslashes($r->Comment);
	if(!empty($comnt)){
		$note = json_decode($comnt,true);
		if(is_array($note)){
			foreach($note as $k=>$v) $table[$k] = $v;
		}
	}
	
	foreach($table as $k=>$v){
		if($v===null) unset($table[$k]);
	}
	
	return $table;
}
?>