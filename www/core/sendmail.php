<?
use PHPMailer\PHPMailer\PHPMailer;

//------------------------------------------------------------------------------------
// Send Error to email - QsvProgram (14-05-2016)
//------------------------------------------------------------------------------------
function SendError($subj, $msg){
	$email = 'qsv.programer@gmail.com';
	$subj .= ' from '.MAIN_DOMAIN.' at '.date('Y-m-d H:i:s');
	$msg .= '<hr>Client IP: '.ClientIP().'<br>Current URL: '.CurrentURL().'<br>
		GET Request: <pre>'.print_r($_GET,true).'</pre>
		POST Request: <pre>'.print_r($_POST,true).'</pre>';
	return SendMail($email, $subj, $msg);
}

//------------------------------------------------------------------------------------
// Gui mail thong bao - QsvProgram (29/06/2012)
// Chuyen server theo cau hinh - QsvProgram (20/10/2015)
//------------------------------------------------------------------------------------
function SendMail($to, $subj, $mess){
	if(empty($mess)) return false;
	
	// Chuyen server gui mail
	if (defined('FROM_SERVER') && FROM_SERVER!='') {
		$func = 'Send'.FROM_SERVER;
		//echo 'Send mail by "'.$func.'"<br>';
		if(function_exists($func)) return $func($to, $subj, $mess);
	}
	
	// Xac dinh email nhan thong tin
	if(empty($to)) $to = EmailAdmin();
	if(empty($to)) return false;
	
	// Tieu de email
	if(empty($subj)) $subj = 'Thông báo tự động từ '.FROM_NAME;
	
	// Bcc to email admin
	if(defined('ADMIN_MAIL') && ADMIN_MAIL!='') {
		$list = preg_split("/[,;]/",ADMIN_MAIL,-1,PREG_SPLIT_NO_EMPTY);
		foreach($list as $e) $to .= ','.$e;
	}

	// Tao header cho email
	$header = "";
	$header .= "From: ".FROM_MAIL." <".FROM_NAME.">\r\n";
	$header .= "Mime-Version: 1.0\r\n";
	$header .= "Content-Type: text/html; charset=utf-8\r\n";
	$header .= "Content-Transfer-Encoding: 7bit\n";
	
  // Luu noi dung email ra file log
  $result = false;
  $data = "SEND MAIL FAILURE!\r\n";
	if(@mail($to,$subj,$mess,$header)) {
    $result = true;
    $data = "SEND MAIL SUCCESS!\r\n";
  }
	$data .= "Date: ".date('Y/m/d H:i:s')."\r\n";
		$data .= "Email: $to\r\n";
		$data .= "Subject: $subj\r\n";
		$data .= "Message: $mess\r\n";
		$data .= "\r\n";
  
  $log =  UPLOAD_PATH.'/logs/mail-'.date('Y-m-d').'.log';
	file_put_contents($log, $data, FILE_APPEND|LOCK_EX);
	
	return $result;
}

//------------------------------------------------------------------------------------
// Gui mail dung Amazon SES - QsvProgram (28/09/2015)
//------------------------------------------------------------------------------------
function SendAmazon($to, $subj, $mess){
	if(empty($mess)) return false;
	
	// Xac dinh email nhan thong tin
	if(empty($to)) $to = EmailAdmin();
	if(empty($to)) return false;
	
	// Tieu de email
	if(empty($subj)) $subj = 'Thông báo tự động từ '.FROM_NAME;
	
	// Gui mail tu PHPMailer
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->Host       = AMAZON_HOST;
	$mail->SMTPDebug  = MAIL_DEBUG;
	$mail->SMTPAuth   = true;
	$mail->SMTPSecure = 'tls';
	$mail->Port       = 587;
	$mail->Username   = AMAZON_USER;
	$mail->Password   = AMAZON_PASS;
	
	$mail->SetFrom(AMAZON_MAIL, FROM_NAME);
	$mail->AddReplyTo(FROM_MAIL, FROM_NAME);
	$mail->CharSet = 'utf-8';
	$mail->Subject = $subj;
	$mail->MsgHTML($mess);
	
	if(strpos($to,',')===false) $mail->AddAddress($to);
	else{
		$list = preg_split("/[,;]/",$to,-1,PREG_SPLIT_NO_EMPTY);
		foreach($list as $e) $mail->AddAddress($e);
	}

	// Bcc to email admin
	if(defined('ADMIN_MAIL') && ADMIN_MAIL!='') {
		$list = preg_split("/[,;]/",ADMIN_MAIL,-1,PREG_SPLIT_NO_EMPTY);
		foreach($list as $e) $mail->AddBCC($e);
	}

  // Luu noi dung email ra file log
  $result = false;
  $data = "SEND AMAZON FAILURE!\r\n";
	if($mail->Send()) {
    $result = true;
    $data = "SEND AMAZON SUCCESS!\r\n";
  }
	$data .= "Date: ".date('Y/m/d H:i:s')."\r\n";
		$data .= "Email: $to\r\n";
		$data .= "Subject: $subj\r\n";
		$data .= "Message: $mess\r\n";
		$data .= "\r\n";
  
  $log =  UPLOAD_PATH.'/logs/amazon-'.date('Y-m-d').'.log';
	file_put_contents($log, $data, FILE_APPEND|LOCK_EX);
	
	return $result;
}

//------------------------------------------------------------------------------------
// Gui mail dung SMTP tren server - QsvProgram (07/05/2013)
//------------------------------------------------------------------------------------
function SendSMTP($to, $subj, $mess){
	if(empty($mess)) return false;
	
	// Xac dinh email nhan thong tin
	if(empty($to)) $to = EmailAdmin();
	if(empty($to)) return false;
	
	// Tieu de email
	if(empty($subj)) $subj = 'Thông báo tự động từ '.FROM_NAME;
	
	// Gui mail tu PHPMailer
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->Host       = SMTP_HOST;
	$mail->SMTPDebug  = MAIL_DEBUG;
  $mail->SMTPAuth   = true;
  $mail->SMTPAutoTLS = false;
	$mail->Username   = SMTP_USER;
	$mail->Password   = SMTP_PASS;
	
	$mail->SetFrom(FROM_MAIL, FROM_NAME);
	$mail->AddReplyTo(FROM_MAIL, FROM_NAME);
	$mail->CharSet = 'utf-8';
	$mail->Subject = $subj;
	$mail->MsgHTML($mess);
	
	if(strpos($to,',')===false) $mail->AddAddress($to);
	else{
		$list = preg_split("/[,;]/",$to,-1,PREG_SPLIT_NO_EMPTY);
		foreach($list as $e) $mail->AddAddress($e);
	}
	
	// Bcc to email admin
	if(defined('ADMIN_MAIL') && ADMIN_MAIL!='') {
		$list = preg_split("/[,;]/",ADMIN_MAIL,-1,PREG_SPLIT_NO_EMPTY);
		foreach($list as $e) $mail->AddBCC($e);
	}

  // Luu noi dung email ra file log
  $result = false;
  $data = "SEND SMTP FAILURE!\r\n";
	if($mail->Send()) {
    $result = true;
    $data = "SEND SMTP SUCCESS!\r\n";
  }
	$data .= "Date: ".date('Y/m/d H:i:s')."\r\n";
		$data .= "Email: $to\r\n";
		$data .= "Subject: $subj\r\n";
		$data .= "Message: $mess\r\n";
		$data .= "\r\n";

  $log =  UPLOAD_PATH.'/logs/smtp-'.date('Y-m-d').'.log';
	file_put_contents($log, $data, FILE_APPEND|LOCK_EX);
	
	return $result;
}

//------------------------------------------------------------------------------------
// Gui mail dung SMTP cua Gmail - QsvProgram (07/05/2013)
//------------------------------------------------------------------------------------
function SendGmail($to, $subj, $mess){
	if(empty($mess)) return false;
	
	// Xac dinh email nhan thong tin
	if(empty($to)) $to = EmailAdmin();
	if(empty($to)) return false;
	
	// Tieu de email
	if(empty($subj)) $subj = 'Thông báo tự động từ '.FROM_NAME;
	
	// Gui mail tu PHPMailer
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->Host       = GMAIL_HOST;
	$mail->SMTPDebug  = MAIL_DEBUG;
	$mail->SMTPAuth   = true;
	$mail->SMTPSecure = 'ssl';
	$mail->Port       = 465;
	$mail->Username   = GMAIL_USER;
	$mail->Password   = GMAIL_PASS;
	
	$mail->SetFrom(FROM_MAIL, FROM_NAME);
	$mail->AddReplyTo(FROM_MAIL, FROM_NAME);
	$mail->CharSet = 'utf-8';
	$mail->Subject = $subj;
	$mail->MsgHTML($mess);
	
	if(strpos($to,',')===false) $mail->AddAddress($to);
	else{
		$list = preg_split("/[,;]/",$to,-1,PREG_SPLIT_NO_EMPTY);
		foreach($list as $e) $mail->AddAddress($e);
	}
	
	// Bcc to email admin
	if(defined('ADMIN_MAIL') && ADMIN_MAIL!='') {
		$list = preg_split("/[,;]/",ADMIN_MAIL,-1,PREG_SPLIT_NO_EMPTY);
		foreach($list as $e) $mail->AddBCC($e);
	}
	
  // Luu noi dung email ra file log
  $result = false;
  $data = "SEND GMAIL FAILURE!\r\n";
	if($mail->Send()) {
    $result = true;
    $data = "SEND GMAIL SUCCESS!\r\n";
  }
	$data .= "Date: ".date('Y/m/d H:i:s')."\r\n";
		$data .= "Email: $to\r\n";
		$data .= "Subject: $subj\r\n";
		$data .= "Message: $mess\r\n";
		$data .= "\r\n";

  $log =  UPLOAD_PATH.'/logs/gmail-'.date('Y-m-d').'.log';
	file_put_contents($log, $data, FILE_APPEND|LOCK_EX);
	
	return $result;
}


//------------------------------------------------------------------------------------
// Kiem tra email hop le khong? - QsvProgram (30/12/2012)
//------------------------------------------------------------------------------------
function ValidEmail($email) {
  $pattern = "/^[a-zA-Z0-9]+[a-zA-Z0-9._-]*@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";  
  return preg_match($pattern,$email);
}

//------------------------------------------------------------------------------------
// Email Template - QsvProgram (19/07/2012)
// Bo sung thong tin lien he - QsvProgram (30/08/2015)
//------------------------------------------------------------------------------------
function EmailTemplate($name,$data){
	$path = SERVER_PATH.'/mail/'.$name;
	if(!file_exists($path)) return '';
	
	// Thong tin lien he
	$data['congty']	= FROM_NAME;
	$data['email'] = FROM_MAIL;
	$data['website'] = MAIN_DOMAIN;
	
	$html = file_get_contents($path);
	foreach($data as $n=>$v) $html = str_replace('{$'.$n.'}',$v,$html);
	return $html;
}

//-------------------------------------------------------------------------------
// Email for receiving errors - QsvProgram (22/05/2018)
//-------------------------------------------------------------------------------
function EmailAdmin() {
  global $dx;

  $qry = "SELECT Email FROM ".PREFIX_NAME.'website'.SUPFIX_NAME."
          WHERE webID='".WebsiteID()."'";
  return $dx->get_var($qry);
}
?>