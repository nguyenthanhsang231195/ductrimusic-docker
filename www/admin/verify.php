<?
require_once('../config/config.php');

// Xu ly token dang nhap
if (!empty($_GET['token'])){
	$token = safe($_GET['token']);
	$status = LoginToken($token);

	// Thanh cong
	if($status===true) {
		echo "<script>window.location='/admin/'</script>";
		exit;
	}

	// That bai
	echo '<meta charset="utf-8">';
	if($status=='expire') echo "<script>alert('Mã bí mật đã hết hạn. Vui lòng đăng nhập lại!');window.location='login.html'</script>";
	else echo "<script>alert('Mã bí mật không hợp lệ. Vui lòng kiểm tra lại!');window.location='verify.php'</script>";
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>Verify &lt;S&F&gt;</title>
<meta name='description' content=''/>
<meta name='keywords' content=''/>
<meta name="author" content="QsvProgram">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap core CSS -->
<link href="/admin/css/bootstrap.css" rel="stylesheet">
<link href="/admin/css/bootstrap-theme.css" rel="stylesheet">

<!-- Font Awesome 3.2.1 -->
<link href="/admin/css/font-awesome.css" rel="stylesheet">
<!--[if IE 7]>
  <link href="/admin/css/font-awesome-ie7.css" rel="stylesheet">
<![endif]-->

<!-- Custom styles for this template -->
<link href="/admin/css/style.css" rel="stylesheet">
<link href="/admin/css/responsive.css" rel="stylesheet">

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
  <script src="/admin/js/html5shiv.js"></script>
  <script src="/admin/js/respond.js"></script>
<![endif]-->
</head>
<body>
<div class="login">
  <form id="frmVerify" name="frmVerify" action="" method="get">
    <h2 class="login-heading">Login / Đăng nhập</h2>
		<p>Vui lòng nhập <b>Mã bí mật</b> hệ thống mới gửi cho bạn!</p>
    <input type="text" id="token" name="token" class="form-control" placeholder="Mã bí mật" onKeyPress="EnterLogin(event)" autocomplete="off" autofocus>
    <button class="btn btn-primary btn-block" type="button" onclick="return DoVerify()">Xác nhận</button>
  </form>
</div>

<!-- jQuery & Bootstrap --> 
<script src="//code.jquery.com/jquery-1.11.2.js"></script> 
<script>window.jQuery || document.write('<script src="/admin/js/jquery-1.11.2.js"><\/script>')</script> 
<script src="/admin/js/bootstrap.js"></script> 

<script>
function EnterVerify(e) {
	var keyCode = e.keyCode || e.which;
	if (keyCode==13) DoVerify();
}

function DoVerify(){	
	if($('#token').val()=="")	alert('Bạn chưa nhập mã bí mật!');
	else $('#frmVerify').submit();
	return false;
}

// Center login form
function AutoCenter(){
	var frm = $('.login form'),
			top = $(window).height()/2-frm.height()/2-50;
	if(top<0) top = 0;
	frm.css({'padding-top': top+'px'});
}

$(document).ready(AutoCenter);
$(window).resize(AutoCenter);
</script>
</body>
</html>