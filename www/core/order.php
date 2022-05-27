<?
//------------------------------------------------------------------------------------
// Trang thai don hang - QsvProgram (17/01/2020)
//------------------------------------------------------------------------------------
function OrderStatus(){
  $stat = [
    ''          => 'Đơn hàng mới',
    'xacnhan'   => 'Xác nhận đơn hàng',
    'sanxuat'   => 'Xác nhận sản xuất',
    'giaohang'  => 'Xác nhận giao hàng',
    'dagiao'    => 'Đã giao cho ĐVGH',
    'hoanthanh' => 'Hoàn thành',
    '0nhanhang' => 'Giao không thành công'
  ];
  return $stat;
}

//------------------------------------------------------------------------------------
// Thong tin thanh toan - QsvProgram (03/02/2018)
//------------------------------------------------------------------------------------
function Payment(){
	$pay = [
    'direct'		=> 'Thanh toán khi nhận hàng (COD)',
    'transfer'	=> 'Chuyển khoản qua ngân hàng',
    //'online'		=> 'Thẻ tín dụng Visa, Mastercard, ...'
  ];
	return $pay;
}

//------------------------------------------------------------------------------------
// Thong tin giao hang - QsvProgram (03/02/2018)
//------------------------------------------------------------------------------------
function Shipment(){
	$ship = [
    'tieuchuan'		=> 'Giao hàng tiêu chuẩn',
    'giaonhanh'	  => 'Giao hàng nhanh',
    //'taicuahang'	=> 'Nhận hàng tại cửa hàng'
  ];
	return $ship;
}

//------------------------------------------------------------------------------------
// Thuoc tinh cho san pham - QsvProgram (03/02/2018)
// Them loai san pham - QsvProgram (10/09/2020)
//------------------------------------------------------------------------------------
function ProductOpt($eid=0){
	$opt = [
    'type' => [
      'pre'	=> 't',
      'name'	=> 'Loại sản phẩm'
    ],
    'form'	=> [
      'pre'	=> 'f',
      'name'	=> 'Kiểu sản phẩm'
    ],
    'size' => [
      'pre'	=> 's',
      'name'	=> 'Kích thước'
    ],
    'color'	=> [
      'pre'	=> 'c',
      'name'	=> 'Màu sắc'
    ]
  ];
	return $opt;
}


//------------------------------------------------------------------------------------
// Phi van chuyen cho don hang - QsvProgram (03/04/2015)
//------------------------------------------------------------------------------------
function ShipCost($region, $ship=''){
	if(empty($region)) return 0;
  
  global $dx;
  $cost = 0;
	$s = "SELECT * FROM ".PREFIX_NAME.'region'.SUPFIX_NAME." WHERE regiID='$region'";
	if($r = $dx->get_row($s)){
    if($ship=='tieuchuan') $cost = floatval($r->Tieuchuan);
    else $cost = floatval($r->Giaonhanh);
  }
	return $cost;
}

//------------------------------------------------------------------------------------
// Ma san pham kem thuoc tinh - QsvProgram (03/05/2014)
//------------------------------------------------------------------------------------
function ProductID($id, $ext=[]){
  $pid = $id;
	$opt = ProductOpt();
  foreach($opt as $k=>$v){
    $pid .= empty($ext[$k]) ? '' : $v['pre'].$ext[$k];
  }
	
	return $pid;
}


//------------------------------------------------------------------------------------
// Xoa san pham trong gio hang - QsvProgram (03/05/2014)
//------------------------------------------------------------------------------------
function ProductRemove($pid){
  unset($_SESSION['cart']['list'][$pid]);
}

//------------------------------------------------------------------------------------
// Them san pham vao gio hang - QsvProgram (03/05/2014)
//------------------------------------------------------------------------------------
function ProductAdd($id, $qty=1, $ext=[]){
	if(empty($id)) return false;
	if(empty($qty)) $qty = 1;
	
	$s = "SELECT * FROM ".PREFIX_NAME.'product'.SUPFIX_NAME." WHERE proID='$id'";
	$q = mysql_query($s);
	if($r = mysql_fetch_assoc($q)){		
		$pid = ProductID($id, $ext);
		$qty = intval($qty);
		
		if(isset($_SESSION['cart']['list'][$pid])){
			$qty += $_SESSION['cart']['list'][$pid]['no'];
			$_SESSION['cart']['list'][$pid]['no'] = $qty;
		}
		else{
			$opt = ProductOpt();
			
			$prod = [
        'id'		=> $r['proID'],
        'sku'   => $r['SKU'],
				'name'	=> stripslashes($r['Ten']),
				'image'	=> ThumbImage($r['Anh'],100,100),
				'link'	=> URL_Rewrite('trang-suc',$r['URL']),
				'no'		=> $qty
      ];
			
			foreach($opt as $k=>$v){
				$prod[$k] = empty($ext[$k]) ? '' : $ext[$k];
			}
			$_SESSION['cart']['list'][$pid] = $prod;
		}
		
		// Cap nhat gia san pham
    $_SESSION['cart']['list'][$pid]['price'] = ProductPrice($id, $qty, $ext);
	}
}

//------------------------------------------------------------------------------------
// Tinh gia san pham - QsvProgram (10/09/2020)
//------------------------------------------------------------------------------------
function ProductPrice($id, $qty=1, $ext=[]){
	if(empty($id)) return 0;
  
  global $dx;
  $wh = "proID='$id'";
  /*
  // Tinh theo thuoc tinh
  if(!empty($ext['color'])) $wh .= " AND colorID='".$ext['color']."'";
  if(!empty($ext['form'])) $wh .= " AND formID='".$ext['form']."'";
  if(!empty($ext['size'])) $wh .= " AND Kichthuoc LIKE '%#".$ext['size']."#%'";
  if(!empty($ext['type'])) $wh .= " AND Danhmuc LIKE '%#".$ext['type']."#%'";
  */
  $s = "SELECT Giaban,GiaKM FROM ".PREFIX_NAME.'product'.SUPFIX_NAME." $wh";
  if($r = $dx->get_row($s)) {
    if($r->GiaKM>0) $price = intval($r->GiaKM);
    else $price = intval($r->Giaban);

    return $price;
  }

  return 0;
}


//------------------------------------------------------------------------------------
// Cap nhat gio hang - QsvProgram (03/05/2014)
//------------------------------------------------------------------------------------
function OrderUpdate($list, $opt=[]){
	if(count($list)==0) return false;
	
	// Xac dinh thuoc tinh them
	$option = ProductOpt();
	
	// Cap nhat san pham
	foreach($list as $pid=>$qty){
		if(empty($qty)) unset($_SESSION['cart']['list'][$pid]);
		else{
			$qty = intval($qty);
			$prod = $_SESSION['cart']['list'][$pid];
			
			$ext = [];
			foreach($option as $k=>$v){
				if(isset($opt[$k][$pid])) $ext[$k] = $opt[$k][$pid];
			}
			
			// Xac dinh san pham moi
			$nid = ProductID($prod['id'],$ext);
			if($nid!=$pid){
				foreach($ext as $k=>$v) $prod[$k] = $v;
				
				// Xoa san pham cu
				unset($_SESSION['cart']['list'][$pid]);
				// Cap nhat san pham moi
				if(isset($_SESSION['cart']['list'][$nid])){
					$qty += $_SESSION['cart']['list'][$nid]['no'];
				}
				$pid = $nid;		
			}
			
			// Cap nhat san pham
			$prod['no'] = $qty;
			$prod['price'] = ProductPrice($prod['id'], $qty, $ext);
			$_SESSION['cart']['list'][$pid] = $prod;
		}
  }
}

//------------------------------------------------------------------------------------
// Khu vuc giao hang - QsvProgram (12/10/2020)
//------------------------------------------------------------------------------------
function OrderRegion($region){
  $_SESSION['cart']['region'] = $region;
}

//------------------------------------------------------------------------------------
// Dich vu giao hang - QsvProgram (12/10/2020)
//------------------------------------------------------------------------------------
function OrderShipment($ship){
  $_SESSION['cart']['ship'] = $ship;
}

//------------------------------------------------------------------------------------
// Phuong thuc thanh toan - QsvProgram (12/10/2020)
//------------------------------------------------------------------------------------
function OrderPayment($pay){
  $_SESSION['cart']['pay'] = $pay;
}


//------------------------------------------------------------------------------------
// Thong tin gio hang - QsvProgram (03/05/2014)
// Them don vi tien VNĐ - QsvProgram (10/09/2020)
//------------------------------------------------------------------------------------
function OrderInfo($sign='', $vat=0){
	// View thong tin gio hang
	$no = $price = 0;
	$list = [];
	if(is_array($_SESSION['cart']['list'])){
		$stt = 0;
		foreach($_SESSION['cart']['list'] as $pid=>$r){
			$total = $r['price']*$r['no'];
			
			// Tinh gia tong cong
			$tmp = $r;
			$tmp['order'] = ++$stt;
			$tmp['money'] = $r['price'];
			$tmp['price'] = format_money($r['price'],$sign);
			$tmp['total'] = format_money($total,$sign);
			$list[$pid] = $tmp;
			
			$no	+= $r['no'];
			$price += $total;
			//echo 'no = '.$no.'<br>';
		}
	}
	//echo '<pre>'.print_r($list,true).'</pre>';
	
	// Phi van chuyen
	$region = $_SESSION['cart']['region'];
	$ship = $_SESSION['cart']['ship'];
	$fee = ShipCost($region,$ship);
	$total = $price + $fee;
	
	// Khuyen mai
	$code = $_SESSION['cart']['code'];
	$info = PromoInfo($code, $price);
	$discount = 0;
	if($info['value']==0) {
		$code = '';
		$_SESSION['cart']['code'] = $code;
	}
	else {
		if($info['type']==0) $discount += $info['value'];
		else $discount += $price*$info['value']/100;
	}
	$total -= $discount;
	$promo = format_money($discount,$sign,'Không có');
	
	// Dich vu don hang
	$service = 0;
	$total += $service;
	
	// Tinh them thue VAT
	$pvat = $total*$vat/100;
	$total += $pvat;
	
	return [
		'sec'		  => $_SESSION['cart']['sec'],
		'list'		=> $list,
		'no'		  => $no,
		'price'		=> format_money($price,$sign,'Miễn phí'),
		'code'		=> $code,
		'promo'		=> $promo,
		'fee'		  => format_money($fee,$sign,'Miễn phí'),
		'service'	=> format_money($service,$sign,'Miễn phí'),
		'vat'		  => $vat.'% = '.format_money($pvat,$sign,'Miễn phí'),
		'money'		=> $total,
		'total'		=> format_money($total,$sign,'Miễn phí')
  ];
}

//------------------------------------------------------------------------------------
// Tao ma don hang, khong trung lap - QsvProgram (08-10-2020)
//------------------------------------------------------------------------------------
function OrderCode() {
	global $dx;
  $code = RandomString(8);
	while(CheckField($dx,PREFIX_NAME.'order'.SUPFIX_NAME,"sec='$code'")){
    $code = RandomString(8);
  }
	return $code;
}

//------------------------------------------------------------------------------------
// Khoi tao gio hang - QsvProgram (29/01/2013)
//------------------------------------------------------------------------------------
function OrderCreate(){
	// Tao ma don hang neu chua co
	if(!isset($_SESSION['cart'])){
		$_SESSION['cart'] = [
			'sec'	  => OrderCode(),
      'list'	=> [],
      'region' => 0,
      'ship'  => 'tieuchuan',
			'pay'	  => 'direct',
			'code'	=> ''
    ];
	}
}
OrderCreate();

//------------------------------------------------------------------------------------
// Lam rong gio hang - QsvProgram (13/05/2013)
//------------------------------------------------------------------------------------
function OrderEmpty(){
	unset($_SESSION['cart']);
	OrderCreate();
}



//------------------------------------------------------------------------------------
// Thong tin khuyen mai - QsvProgram (12/11/2014)
//------------------------------------------------------------------------------------
function PromoInfo($code, $price=0){
	$p = [
		'type'	=> 0,	// Loai: 0 -> VNĐ, 1 -> %
		'value'	=> 0,	// Gia tri khuyen mai
		'msg'	=> ''
  ];
	if(empty($code)){
		$p['msg'] = 'Mã khuyến mãi không hợp lệ';
		return $p;
	}
	
	// Xac dinh thong tin khuyen mai
	$eid = GetEstore();
	$s = "SELECT * FROM ".PREFIX_NAME.'promo'.SUPFIX_NAME."
		  WHERE Code='$code' AND Kichhoat='1'";
	$q = mysql_query($s);
	if($r=mysql_fetch_assoc($q)){		
		// Neu so lan su dung da het thi thoat luon
		if($r['Gioihan']==1 && $r['Solan']<1){
			$p['msg'] = 'Mã khuyến mãi hết số lần sử dụng';
			return $p;
		}
		
		// Neu het thoi gian hieu luc thi thoat luon
		if($r['Thoigian']==1){
			$now	= time();
			$start	= strtotime($r['Batdau']);
			$end	= strtotime($r['Ketthuc']);
			if($now<$start || $now>$end){ 
				$p['msg'] = 'Mã khuyến mãi không đúng thời gian sử dụng';
				return $p;
			}
		}
		
		// Kiem tra gia tri thap nhat cua don hang
		if($price<$r['Toithieu']){
			$p['msg'] = 'Giá trị của đơn hàng không hợp lệ';
			return $p;
		}
		
		// Gia tri khuyen mai
		$p['type'] = $r['Loai'];
		$p['value'] = floatval($r['Giatri']);
	}
	
	return $p;
}

//------------------------------------------------------------------------------------
// Cap nhat da dung ma khuyen mai - QsvProgram (12/11/2014)
//------------------------------------------------------------------------------------
function PromoUpdate($code){
	if(empty($code)) return false;
  
  global $dx;

	// Xac dinh thong tin khuyen mai
	$eid = GetEstore();
	$s = "SELECT * FROM ".PREFIX_NAME.'promo'.SUPFIX_NAME."
		  WHERE Code='$code' AND Kichhoat='1'";
	$q = mysql_query($s);
	if($r=mysql_fetch_assoc($q)){
		$id = $r['prmID'];
		$active = $r['Kichhoat'];
		
		// Giam so lan su dung
		if($r['Gioihan']==1){
			$usetime = intval($r['Solan'])-1;
			if($usetime<1) $active = 0;
			
			// Cap nhat so lan su dung
			UpdateField($dx,PREFIX_NAME.'promo'.SUPFIX_NAME,"prmID='$id'","Solan='$usetime'");
		}
		
		// Kiem tra thoi gian hieu luc
		if($r['Thoigian']==1){
			$now	= time();
			$start	= strtotime($r['Batdau']);
			$end	= strtotime($r['Ketthuc']);
			if($now<$start || $now>$end) $active = 0;
		}
		
		// Cap nhat trang thai kich hoat
		UpdateField($dx,PREFIX_NAME.'promo'.SUPFIX_NAME,"prmID='$id'","Kichhoat='$active'");
		return true;
	}
	
	return false;
}

//------------------------------------------------------------------------------------
// Cap nhat ma khuyen mai - QsvProgram (03/04/2015)
//------------------------------------------------------------------------------------
function PromoApply($code){
  $_SESSION['cart']['code'] = $code;
}

?>