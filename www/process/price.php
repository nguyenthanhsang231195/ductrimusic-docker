<?
require_once('../config/config.php');

if(empty($_GET['id'])) die('Sản phẩm không hợp lệ!');
$id = safe($_GET['id']);

// Khoi tao ngon ngu
language();

// Tinh gia san pham
$wh = '';
if(!empty($_GET['f'])) {
  $opts = safe($_GET['f']);
  foreach($opts as $ftr=>$itm) {
    $wh .= ($wh==''?'':'AND')."(priID IN (
      SELECT priID FROM ".PREFIX_NAME.'price_option'.SUPFIX_NAME."
      WHERE feaID='$ftr' AND fitID='$itm')
    )";
  }
}
$wh .= ($wh==''?'':'AND')."(proID='$id')";
$wh = ($wh==''?'':'WHERE').$wh;

$s = "SELECT Gia,GiaKM FROM ".PREFIX_NAME.'price'.SUPFIX_NAME." $wh";
$rs = $dx->get_results($s);
if($dx->num_rows==0) echo lg('Contact');  //'Không có giá!';
else if($dx->num_rows==1) {
  $price = format_money($rs[0]->Gia,'đ',lg('Contact'));
  $promo = format_money($rs[0]->GiaKM,'đ',0);
  
  if($promo!=0) echo $promo.'* <small>'.$price.'</small>';
  else {
    echo $price;
    if($rs[0]->Gia != 0) echo '<span style="color: #616161; ">*</span>';
  }
}
else echo 'Có nhiều giá!'
?>