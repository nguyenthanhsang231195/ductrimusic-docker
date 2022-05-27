<?
// Using ezSQL for MySQL
require_once(__DIR__.'/ezsql/shared/ez_sql_core.php');
require_once(__DIR__.'/ezsql/mysql/ez_sql_mysql.php');

// Connect to database
function ConnectDB($cfg) {
    $ez = new ezSQL_mysql($cfg['user'], $cfg['pass'], $cfg['name'], $cfg['host']);
    return $ez;
}


//-------------------------------------------------------------------------------
// Trim "WHERE" in SQL string - QsvProgram (17/12/2014)
//-------------------------------------------------------------------------------
function TrimWhere($wh){
	$wh = trim($wh);
	if(strtoupper(substr($wh,0,5))=='WHERE'){
		$wh = ltrim(substr($wh,5));
	}
	return $wh;
}

//-------------------------------------------------------------------------------
// Return num of rows - QsvProgram (18/04/2015)
//-------------------------------------------------------------------------------
function NumOfRows($dx, $tbl, $c=''){
	$c = TrimWhere($c);
	$s = "SELECT COUNT(*) FROM $tbl".($c==''?'':" WHERE $c");
	return $dx->get_var($s);
}

//-------------------------------------------------------------------------------
// Return num of page - QsvProgram (18/04/2015)
//-------------------------------------------------------------------------------
function NumOfPages($dx, $tbl, $c='', $rowperpage=20){
	$rowperpage = intval($rowperpage);
	$numOfRows = NumOfRows($dx,$tbl,$c);
	if($rowperpage==0) $numPages = 1;
	else $numPages = ceil($numOfRows/$rowperpage);
	return $numPages;
}

//-------------------------------------------------------------------------------
// Ham lay ve chi so ID nho nhat cua table - QsvProgram (18/04/2015)
//-------------------------------------------------------------------------------
function FirstID($dx, $k, $tbl){
	$s = "SELECT $k FROM $tbl ORDER BY $k";
	$i = 1;
	if($rs = $dx->get_results($s)){
	  foreach($rs as $r){
		  if($i!=$r->$k) break;
		  $i++;
	  }
	}
	return $i;
}

//-------------------------------------------------------------------------------
// Insert a field in table - QsvProgram (18/04/2015)
//-------------------------------------------------------------------------------
function InsertField($dx, $tbl, $f, $v){	
	$s = "INSERT INTO $tbl($f) VALUES($v)";
	return $dx->query($s);
}

//-------------------------------------------------------------------------------
// Update a field on table - QsvProgram (18/04/2015)
//-------------------------------------------------------------------------------
function UpdateField($dx, $tbl, $c, $f){	
	$c = TrimWhere($c);
	$s = "UPDATE $tbl SET $f".($c==''?'':" WHERE $c");
	return $dx->query($s);
}

//-------------------------------------------------------------------------------
// Delete a field on table - QsvProgram (18/04/2015)
//-------------------------------------------------------------------------------
function DeleteField($dx, $tbl, $c=''){	
	$c = TrimWhere($c);
	$s = "DELETE FROM $tbl".($c==''?'':" WHERE $c");
	return $dx->query($s);
}

//-------------------------------------------------------------------------------
// Return one field on table - QsvProgram (18/04/2015)
//-------------------------------------------------------------------------------
function GetField($dx, $tbl, $c='', $f){	
	$c = TrimWhere($c);
	$s = "SELECT $f FROM $tbl".($c==''?'':" WHERE $c")." LIMIT 1";
	return $dx->get_var($s);
}

//-------------------------------------------------------------------------------
// Return one field on table - QsvProgram (18/04/2015)
//-------------------------------------------------------------------------------
function CheckField($dx, $tbl, $c){	
	$c = TrimWhere($c);
	$dx->query("SELECT * FROM $tbl".($c==''?'':" WHERE $c")." LIMIT 1");
	return $dx->num_rows>0;
}

//-------------------------------------------------------------------------------
// Chong SQL Injection (cho phep HTML) - QsvProgram (01/04/2016)
//-------------------------------------------------------------------------------
function _safe($var) {
	if(is_array($var)) {
		foreach($var as $k=>$v) $arr[$k] = _safe($v);
		return $arr;
	}

	// Dat chuoi thoat cac ky tu dac biet
	global $db;
	return $db->escape($var);
}
function safeHTML($var) {
	return _safe($var);
}

//-------------------------------------------------------------------------------
// Chong SQL Injection (xu ly XSS) - QsvProgram (01/04/2016)
// Cap nhat ham xu ly XSS  - QsvProgram (05/05/2016)
// Fix loi xu ly cho Array - QsvProgram (06/07/2016)
//-------------------------------------------------------------------------------
function safe($var) {
	if(is_array($var)) {
		foreach($var as $k=>$v) $arr[$k] = safe($v);
		return $arr;
	}

	$var = SacarXss($var);
	return _safe($var);
}


//-------------------------------------------------------------------------------
// Prevent Cross Site Scripting (XSS) Attacks
//-------------------------------------------------------------------------------
function SacarXss($val) {
	$val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);

	$search = 'abcdefghijklmnopqrstuvwxyz';
	$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$search .= '1234567890!@#$%^&*()';
	$search .= '~`";:?+/={}[]-_|\'\\';
	for ($i = 0; $i < strlen($search); $i++) {
		$val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
		$val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
	}

	$ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
	$ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
   	$ra = array_merge($ra1, $ra2);

	$found = true; 
	while ($found == true) {
		$val_before = $val;
		for ($i = 0; $i < sizeof($ra); $i++) {
			$pattern = '/';
			for ($j = 0; $j < strlen($ra[$i]); $j++) {
				if ($j > 0) {
					$pattern .= '(';
					$pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
					$pattern .= '|(&#0{0,8}([9][10][13]);?)?';
					$pattern .= ')?';
				}
				$pattern .= $ra[$i][$j];
			}

			$pattern .= '/i';
			$replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
			$val = preg_replace($pattern, $replacement, $val);
			if ($val_before == $val) {
				$found = false;
			}
		}
	}

	return $val;
}

?>