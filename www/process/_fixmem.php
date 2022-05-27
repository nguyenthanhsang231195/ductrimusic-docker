<?
require_once('../config/config.php');
if(!CheckLogged()) exit;

echo '<meta charset="utf-8">';

// Cap nhat ma thanh vien
echo '<h3>Cập nhật mã thành viên</h3>';
$s = "SELECT * FROM ".PREFIX_NAME.'member'.SUPFIX_NAME." WHERE Code='' OR Code IS NULL";
if($rs = $dx->get_results($s)){
  echo '<ol>';
  
  foreach($rs as $r){
	echo '<li>'.$r->Name.' &lt;'.$r->Email.'&gt;<br>';
	
	$type = $r->Type;
	$mid = $r->memID;
	$code = MemberCode($type,$mid);
	
	$ss = "UPDATE ".PREFIX_NAME.'member'.SUPFIX_NAME." SET Code='$code' WHERE memID='$mid'";
	$dx->query($ss);
	echo "<b>$ss --&gt; ".$dx->rows_affected.'</b></li>';
  }
  
  echo '</ol>';
}

// Loai thanh vien
$MemType = [
	1 => 'Quản trị viên',
	2 => 'Nhân viên',
	3 => 'Thành viên',
	4 => 'Showroom',
	5 => 'Khách hàng'
];

// Thanh vien trung email
echo '<h3>Thành viên trùng email</h3>';
$s = "SELECT * FROM ".PREFIX_NAME.'member'.SUPFIX_NAME." GROUP BY Email HAVING COUNT(Email)>1";
if($rs = $dx->get_results($s)){
  echo '<ol>';
  
  foreach($rs as $r){
	 $ss = "SELECT * FROM ".PREFIX_NAME.'member'.SUPFIX_NAME."
	 		WHERE Email='".$r->Email."' ORDER BY `Type`,memID";
	 if($rrs = $dx->get_results($ss)){
		$rr = array_shift($rrs);
		$mid = $rr->memID;
		$type = $MemType[$rr->Type];
		echo '<li>['.$mid.'] <b>'.$type.'</b> '.$rr->Name.' &lt;'.$rr->Email.'&gt; {'.$rr->Username.'}<br>';
		
	  	foreach($rrs as $rr){
			$did = $rr->memID;
			$type = $MemType[$rr->Type];
			echo '['.$did.'] <b>'.$type.'</b> '.$rr->Name.' &lt;'.$rr->Email.'&gt; {'.$rr->Username.'}';
		}
		echo '</li>';
	 }
  }
  
  echo '</ol>';
}

// Thanh vien trung Username
echo '<h3>Thành viên trùng Username</h3>';
$s = "SELECT * FROM ".PREFIX_NAME.'member'.SUPFIX_NAME." WHERE Username!=''
	  GROUP BY Username HAVING COUNT(Username)>1";
if($rs = $dx->get_results($s)){
  echo '<ol>';
  
  foreach($rs as $r){
	 $ss = "SELECT * FROM ".PREFIX_NAME.'member'.SUPFIX_NAME."
	 		WHERE Username='".$r->Username."' ORDER BY `Type`,memID";
	 if($rrs = $dx->get_results($ss)){
		$rr = array_shift($rrs);
		$mid = $rr->memID;
		$type = $MemType[$rr->Type];
		echo '<li>['.$mid.'] <b>'.$type.'</b> '.$rr->Name.' &lt;'.$rr->Email.'&gt; {'.$rr->Username.'}<br>';
		
	  	foreach($rrs as $rr){
			$did = $rr->memID;
			$type = $MemType[$rr->Type];
			echo '['.$did.'] <b>'.$type.'</b> '.$rr->Name.' &lt;'.$rr->Email.'&gt; {'.$rr->Username.'}<ul>';
			
			echo '</ul>';
		}
		echo '</li>';
	 }
  }
  
  echo '</ol>';
}

echo '<h4>Hoàn thành xử lý!</h4>';
?>