<?
//-------------------------------------------------------------------------------
// Xac dinh loai thanh vien - QsvProgram (18/04/2015)
//-------------------------------------------------------------------------------
function MemberType($memID=0){
	if(empty($memID)) $memID = QsvMember();
	
	// Tai khoan QsvAdmin
	global $QSV;
	if(isset($QSV['admin'][$memID])) return 0;
	
	global $db;
	$type = GetField($db,PREFIX_NAME.'member'.SUPFIX_NAME,"memID='$memID'",'Type');
	return $type;
}

//-------------------------------------------------------------------------------
// Kiem tra quyen thanh vien - QsvProgram (16/01/2014)
//-------------------------------------------------------------------------------
function CheckKey($key, $memID=0){
	if(ValidKey($key,$memID)) return true;
	die('Access denied!');
}
function ValidKey($key, $memID=0){
	if($key=='') return true;
	
	// Xac dinh thanh vien
	if(empty($memID)) $memID = QsvMember();
	
	// Tai khoan QsvAdmin
	global $QSV;
	if(isset($QSV['admin'][$memID])) return true;
	
	// Kiem tra quyen cua thanh vien
	$memkey = MemberKey($memID);
	if(isset($memkey[$key])) return true;
	return false;
}

//-------------------------------------------------------------------------------
// Xac dinh danh sach quyen thanh vien - QsvProgram (18/04/2015)
//-------------------------------------------------------------------------------
function MemberKey($memID=0){
	$rs = array();
	if(empty($memID)) return $rs;
	
	global $db;
	$perm = $db->get_var("SELECT Perm FROM ".PREFIX_NAME.'member'.SUPFIX_NAME." WHERE memID='$memID'");
	if($perm!=''){
		$arr = preg_split("/[#,]/",$perm,-1,PREG_SPLIT_NO_EMPTY);
		foreach($arr as $k) $rs[$k] = $memID;
	}
	return $rs;
}

//-------------------------------------------------------------------------------
// Cap nhat quyen cho thanh vien - QsvProgram (18/04/2015)
//-------------------------------------------------------------------------------
function MemberRole($memID, $role, $ignore=array()){
	// Xoa toan bo quyen cu cua thanh vien
	global $dx;
	UpdateField($dx,PREFIX_NAME.'member'.SUPFIX_NAME, "memID='$memID'","Perm=''");
	
	global $QSV;
	if(isset($QSV['role'][$role])){
		// Cap nhat danh sach quyen duoc su dung
		$list = array();
		foreach($QSV['role'][$role] as $key){
			if(in_array($key,$ignore)) continue;
			$list[] = $key;
		}
		
		// Cap nhat quyen moi cho thanh vien
		$perm = join(',',$list);
		UpdateField($dx,PREFIX_NAME.'member'.SUPFIX_NAME, "memID='$memID'","Perm='$perm'");		
	}
}
?>