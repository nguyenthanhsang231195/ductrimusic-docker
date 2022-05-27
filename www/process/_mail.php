<?
require_once('../config/config.php');
if(!CheckLogged()) exit;

$email = 'qsv.programer@gmail.com,qsv_programer@yahoo.com';
$subject = 'Test send mail from '.MAIN_DOMAIN;
$message = date('H:i:s d/m/Y');

$result = SendMail($email, $subject, $message);
echo '<p>Sent test mail: '.($result?'SUCCESS':'FAILURE').'!</p>';
?>