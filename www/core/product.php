<?
//------------------------------------------------------------------------------------
// Nhap gia tu file excel - QsvProgram (30/05/2020)
//------------------------------------------------------------------------------------
function PriceParser($proID, $debug=false) {
  if(empty($proID)) return false;

  // Xoa thuoc tinh + bang gia san pham
  ProductClear($proID);

  // Doc du lieu tu excel
  global $dx;
  $file = GetField($dx,PREFIX_NAME.'product'.SUPFIX_NAME,"proID='$proID'",'Filegia');
  if(empty($file)) return false;
  $real = RealFile($file);
  if(!file_exists($real)) return false;

  $reader = new PHPExcel_Reader_Excel2007();
  $excel = $reader->load($real);

  // Du lieu dau vao
  $sheet = $excel->getSheet(0); 
  $mrow = $sheet->getHighestRow(); 
  $mcol = $sheet->getHighestColumn();
  if ($debug) SheetToHTML($sheet);

  // Xac dinh Version
  $version = 1;
  $meta = $sheet->rangeToArray("E1:F2",'',true,false);
  $version = floatval($meta[0][1]);
  if ($debug){
    echo '<h1>Meta data</h1><pre>' . print_r($meta, true) . '</pre>';
    echo '<b>Version</b>: '.$version.'<br>';
  }

  $data = $sheet->rangeToArray("A4:{$mcol}4",'',true,false);
  if ($debug) echo '<h1>Title data</h1><pre>' . print_r($data, true) . '</pre>';

  // Filter start column
  $fsc = 1;
  if($version>=2) $fsc = 2;  

  // Them cac thuoc tinh
  $feature = $data[0];
  $listFeature = [];
  for($c = $fsc; $c < count($feature); $c++ ) {
    $name = trim(safe($feature[$c]));
    if(empty($name)) continue;

    // Them thuoc tinh
    $feaID = GetField($dx,PREFIX_NAME.'feature'.SUPFIX_NAME,"Ten='$name'",'feaID');
    if(empty($feaID)) {
      $feaID	= FirstID($dx,'feaID',PREFIX_NAME.'feature'.SUPFIX_NAME);
      $s = "INSERT INTO ".PREFIX_NAME.'feature'.SUPFIX_NAME."(feaID, Ten, NgayCN)
            VALUES('$feaID', '$name', NOW())";
      if($dx->query($s)) {
        // Copy data language
        ldc([
            'n'	=> PREFIX_NAME.'feature'.SUPFIX_NAME,
            'c'	=> [
              'feaID','Ten','Danhmuc','NgayCN'
            ]
          ], [
            'n'	=> PREFIX_NAME.'feature_lg'.SUPFIX_NAME,
            'c'	=> [
              'lgID','Ten','Danhmuc','NgayCN'
            ]
          ],
          'en', "feaID='$feaID'", $debug
        );
      }
    }

    // Them thuoc tinh san pham
    $pftID = FirstID($dx,'pftID',PREFIX_NAME.'product_feature'.SUPFIX_NAME);
    $s = "INSERT INTO ".PREFIX_NAME.'product_feature'.SUPFIX_NAME."(pftID, Thutu, NgayCN, feaID, proID)
          VALUES('$pftID', '$c', NOW(), '$feaID', '$proID')";
    $dx->query($s);
    

    $listFeature[$c] = [
      'id' => $feaID,
      'name'  => $name,
      'no' => 0
    ];
  }
  if ($debug) echo '<h2>List feature</h2><pre>' . print_r($listFeature, true) . '</pre>';

  // Them gia & gia tri thuoc tinh
  if ($debug) echo '<h1>Price data</h1><ol>';
	for($r = 5; $r <= $mrow; $r++ ) {
    if ($debug) echo '<li>';

    $data = $sheet->rangeToArray("A{$r}:{$mcol}{$r}",'',true,false);
    if ($debug) echo '<h2>Row '.$r.'</h2><pre>' . print_r($data, true) . '</pre>';

    // Them gia san pham
    $row = $data[0];
    $price = intval($row[0]);
    $promo = 0;
    if($version>=2) $promo = intval($row[1]);

    $priID	= FirstID($dx,'priID',PREFIX_NAME.'price'.SUPFIX_NAME);
    $s = "INSERT INTO ".PREFIX_NAME.'price'.SUPFIX_NAME."(priID, Gia, GiaKM, Thutu, NgayCN, proID)
          VALUES('$priID', '$price', '$promo', '".($r-4)."', NOW(), '$proID')";
    $dx->query($s);


    for($c = $fsc; $c < count($row); $c++ ) {
      $feaID = $listFeature[$c]['id'];
      if(empty($feaID)) continue;

      // Them cac gia tri thuoc tinh
      $value = trim(safe($row[$c]));
      if(empty($value)) continue;

      $fitID = GetField($dx,PREFIX_NAME.'feature_item'.SUPFIX_NAME,"feaID='$feaID' AND Giatri='$value'",'fitID');
      if(empty($fitID)) {
        $fitID	= FirstID($dx,'fitID',PREFIX_NAME.'feature_item'.SUPFIX_NAME);
        $s = "INSERT INTO ".PREFIX_NAME.'feature_item'.SUPFIX_NAME."(fitID, Giatri, NgayCN, feaID)
              VALUES('$fitID', '$value', NOW(), '$feaID')";
        if($dx->query($s)) {
          // Copy data language
          ldc([
              'n'	=> PREFIX_NAME.'feature_item'.SUPFIX_NAME,
              'c'	=> [
                'fitID','Giatri','feaID','NgayCN'
              ]
            ], [
              'n'	=> PREFIX_NAME.'feature_item_lg'.SUPFIX_NAME,
              'c'	=> [
                'lgID','Giatri','feaID','NgayCN'
              ]
            ],
            'en', "fitID='$fitID'", $debug
          );
        }
      }

      // Them chi tiet gia san pham
      $s = "INSERT INTO ".PREFIX_NAME.'price_option'.SUPFIX_NAME."(priID, feaID, fitID, Thutu, NgayCN)
            VALUES('$priID', '$feaID', '$fitID', '$c', NOW())";
      $dx->query($s);

      // Them gia tri thuoc tinh
      $pftID = GetField($dx,PREFIX_NAME.'product_feature'.SUPFIX_NAME,"proID='$proID' AND feaID='$feaID'",'pftID');      
      $exist = CheckField($dx,PREFIX_NAME.'product_feature_value'.SUPFIX_NAME,"pftID='$pftID' AND fitID='$fitID'");
      if(!$exist) {
        $no = ++$listFeature[$c]['no'];
        $s = "INSERT INTO ".PREFIX_NAME.'product_feature_value'.SUPFIX_NAME."(pftID, fitID, Thutu, NgayCN)
              VALUES('$pftID', '$fitID', '$no', NOW())";
        $dx->query($s);
      }
    }

    if ($debug) echo '</li>';
  }
  echo '</ol>';
  

  // Doc du lieu filter
  $sheet = $excel->getSheet(1); 
  $mrow = $sheet->getHighestRow(); 
  $mcol = $sheet->getHighestColumn();
  if ($debug) SheetToHTML($sheet);

  $data = $sheet->rangeToArray("A1:{$mcol}1",'',true,false);
  if ($debug) echo '<h1>Filter data</h1><pre>' . print_r($data, true) . '</pre>';

  // Them cac thuoc tinh
  $feature = $data[0];
  $listFilter = [];
  for($c = 0; $c < count($feature); $c++ ) {
    $name = trim(safe($feature[$c]));
    if(empty($name)) continue;

    // Them thuoc tinh
    $feaID = GetField($dx,PREFIX_NAME.'feature'.SUPFIX_NAME,"Ten='$name'",'feaID');
    if(empty($feaID)) {
      $feaID	= FirstID($dx,'feaID',PREFIX_NAME.'feature'.SUPFIX_NAME);
      $s = "INSERT INTO ".PREFIX_NAME.'feature'.SUPFIX_NAME."(feaID, Ten, NgayCN)
            VALUES('$feaID', '$name', NOW())";
      if($dx->query($s)) {
        // Copy data language
        ldc([
            'n'	=> PREFIX_NAME.'feature'.SUPFIX_NAME,
            'c'	=> [
              'feaID','Ten','Danhmuc','NgayCN'
            ]
          ], [
            'n'	=> PREFIX_NAME.'feature_lg'.SUPFIX_NAME,
            'c'	=> [
              'lgID','Ten','Danhmuc','NgayCN'
            ]
          ],
          'en', "feaID='$feaID'", $debug
        );
      }
    }

    // Them thuoc tinh san pham
    $pftID = FirstID($dx,'pftID',PREFIX_NAME.'product_feature'.SUPFIX_NAME);
    $s = "INSERT INTO ".PREFIX_NAME.'product_feature'.SUPFIX_NAME."(pftID, Thutu, NgayCN, feaID, proID)
          VALUES('$pftID', '$c', NOW(), '$feaID', '$proID')";
    $dx->query($s);
    

    $listFilter[$c] = [
      'id' => $feaID,
      'name'  => $name,
      'no' => 0
    ];
  }
  if ($debug) echo '<h2>List filter</h2><pre>' . print_r($listFilter, true) . '</pre>';

  // Them gia & gia tri thuoc tinh
  if ($debug) echo '<h1>Filter value</h1><ol>';
	for($r = 2; $r <= $mrow; $r++ ) {
    if ($debug) echo '<li>';

    $data = $sheet->rangeToArray("A{$r}:{$mcol}{$r}",'',true,false);
    if ($debug) echo '<h2>Row '.$r.'</h2><pre>' . print_r($data, true) . '</pre>';

    // Them gia san pham
    $row = $data[0];
    for($c = 0; $c < count($row); $c++ ) {
      $feaID = $listFilter[$c]['id'];
      if(empty($feaID)) continue;

      // Them cac gia tri thuoc tinh
      $value = trim(safe($row[$c]));
      if(empty($value)) continue;
      
      $fitID = GetField($dx,PREFIX_NAME.'feature_item'.SUPFIX_NAME,"feaID='$feaID' AND Giatri='$value'",'fitID');
      if(empty($fitID)) {
        $fitID	= FirstID($dx,'fitID',PREFIX_NAME.'feature_item'.SUPFIX_NAME);
        $s = "INSERT INTO ".PREFIX_NAME.'feature_item'.SUPFIX_NAME."(fitID, Giatri, NgayCN, feaID)
              VALUES('$fitID', '$value', NOW(), '$feaID')";
        if($dx->query($s)) {
          // Copy data language
          ldc([
              'n'	=> PREFIX_NAME.'feature_item'.SUPFIX_NAME,
              'c'	=> [
                'fitID','Giatri','feaID','NgayCN'
              ]
            ], [
              'n'	=> PREFIX_NAME.'feature_item_lg'.SUPFIX_NAME,
              'c'	=> [
                'lgID','Giatri','feaID','NgayCN'
              ]
            ],
            'en', "fitID='$fitID'", $debug
          );
        }
      }

      // Them gia tri thuoc tinh
      $pftID = GetField($dx,PREFIX_NAME.'product_feature'.SUPFIX_NAME,"proID='$proID' AND feaID='$feaID'",'pftID');      
      $exist = CheckField($dx,PREFIX_NAME.'product_feature_value'.SUPFIX_NAME,"pftID='$pftID' AND fitID='$fitID'");
      if(!$exist) {
        $no = ++$listFilter[$c]['no'];
        $s = "INSERT INTO ".PREFIX_NAME.'product_feature_value'.SUPFIX_NAME."(pftID, fitID, Thutu, NgayCN)
              VALUES('$pftID', '$fitID', '$no', NOW())";
        $dx->query($s);
      }
    }

    if ($debug) echo '</li>';
  }
  echo '</ol>';
  

  // Lay gia mac dinh
  return PriceSync($proID, $debug);
}


//------------------------------------------------------------------------------------
// Dong bo gia chuan san pham - QsvProgram (01/06/2020)
// Xac dinh gia dau tien mac dinh - QsvProgram (03/07/2020)
//------------------------------------------------------------------------------------
function PriceSync($proID, $debug=false) {
  if(empty($proID)) return false;
  global $dx;

  /*
  // Xac dinh theo dieu kien
  $filter = [];

  // Xac dinh Ni = 10
  $feaID = GetField($dx,PREFIX_NAME.'feature'.SUPFIX_NAME,"Ten='Ni'",'feaID');
  if(!empty($feaID)) {
    $fitID = GetField($dx,PREFIX_NAME.'feature_item'.SUPFIX_NAME,"feaID='$feaID' AND Giatri='10'",'fitID');
    if(!empty($fitID)) $filter[$feaID] = $fitID;
  }
  
  // Xac dinh "Loại vàng = 18"
  $feaID = GetField($dx,PREFIX_NAME.'feature'.SUPFIX_NAME,"Ten='Loại vàng'",'feaID');
  if(!empty($feaID)) {
    $fitID = GetField($dx,PREFIX_NAME.'feature_item'.SUPFIX_NAME,"feaID='$feaID' AND Giatri='18'",'fitID');
    if(!empty($fitID)) $filter[$feaID] = $fitID;
  }

  // Xac dinh "Kim cương = 5.4"
  $feaID = GetField($dx,PREFIX_NAME.'feature'.SUPFIX_NAME,"Ten='Kim cương'",'feaID');
  if(!empty($feaID)) {
    $fitID = GetField($dx,PREFIX_NAME.'feature_item'.SUPFIX_NAME,"feaID='$feaID' AND Giatri='5.4'",'fitID');
    if(!empty($fitID)) $filter[$feaID] = $fitID;
  }

  // Xac dinh gia san pham
  $wh = '';
  foreach($filter as $ftr=>$itm) {
    $wh .= ($wh==''?'':'AND')."(priID IN (
      SELECT priID FROM ".PREFIX_NAME.'price_option'.SUPFIX_NAME."
      WHERE feaID='$ftr' AND fitID='$itm')
    )";
  }
  $wh .= ($wh==''?'':'AND')."(proID='$proID')";
  $wh = ($wh==''?'':'WHERE').$wh;

  $s = "SELECT Gia,GiaKM FROM ".PREFIX_NAME.'price'.SUPFIX_NAME." $wh ORDER BY Gia LIMIT 1";
  */
  $s = "SELECT Gia,GiaKM FROM ".PREFIX_NAME.'price'.SUPFIX_NAME."
        WHERE proID='$proID' ORDER BY Thutu LIMIT 1";
  if($r = $dx->get_row($s)){
    UpdateField($dx,PREFIX_NAME.'product'.SUPFIX_NAME,"proID='$proID'","Giaban='".$r->Gia."',GiaKM='".$r->GiaKM."'");
    UpdateField($dx,PREFIX_NAME.'product_lg'.SUPFIX_NAME,"lgID='$proID'","Giaban='".$r->Gia."',GiaKM='".$r->GiaKM."'");
    return true;
  }

  return false;
}

//------------------------------------------------------------------------------------
// Dong bo thong tin filter - QsvProgram (01/06/2020)
//------------------------------------------------------------------------------------
function FilterSync($proID, $debug=false) {
  if(empty($proID)) return false;
  global $dx;

  // Xoa cac thuoc tinh
  $s = "DELETE FROM ".PREFIX_NAME.'feature'.SUPFIX_NAME."
        WHERE feaID NOT IN(
          SELECT feaID FROM ".PREFIX_NAME.'product_feature'.SUPFIX_NAME."
        )";
  if($dx->query($s)) {
    // Delete data language
    $ss = "DELETE FROM ".PREFIX_NAME.'feature_lg'.SUPFIX_NAME."
           WHERE lgID NOT IN(
              SELECT feaID FROM ".PREFIX_NAME.'feature'.SUPFIX_NAME."
           )";
    $dx->query($ss);
  }

  // Xoa cac gia tri thuoc tinh
  $s = "DELETE FROM ".PREFIX_NAME.'feature_item'.SUPFIX_NAME."
        WHERE fitID NOT IN(
          SELECT fitID FROM ".PREFIX_NAME.'product_feature_value'.SUPFIX_NAME."
        )";
  if($dx->query($s)) {
    // Delete data language
    $ss = "DELETE FROM ".PREFIX_NAME.'feature_item_lg'.SUPFIX_NAME."
           WHERE lgID NOT IN(
              SELECT fitID FROM ".PREFIX_NAME.'feature_item'.SUPFIX_NAME."
           )";
    $dx->query($ss);
  }

  return true;
}

//------------------------------------------------------------------------------------
// Don sach Bang gia, thuoc tinh san pham - QsvProgram (01/06/2020)
//------------------------------------------------------------------------------------
function ProductClear($proID, $debug=false) {
  if(empty($proID)) return false;
  global $dx;

  // Xoa bang gia san pham
  $s = "SELECT priID FROM ".PREFIX_NAME.'price'.SUPFIX_NAME." WHERE proID='$proID'";
  if($rs = $dx->get_results($s)){
    foreach($rs as $r){
      $priID = $r->priID;
      DeleteField($dx,PREFIX_NAME.'price'.SUPFIX_NAME,"priID='$priID'");
      DeleteField($dx,PREFIX_NAME.'price_option'.SUPFIX_NAME,"priID='$priID'");
    }
  }

  // Xoa thuoc tinh san pham
  $s = "SELECT pftID FROM ".PREFIX_NAME.'product_feature'.SUPFIX_NAME." WHERE proID='$proID'";
  if($rs = $dx->get_results($s)){
    foreach($rs as $r){
      $pftID = $r->pftID;
      DeleteField($dx,PREFIX_NAME.'product_feature'.SUPFIX_NAME,"pftID='$pftID'");
      DeleteField($dx,PREFIX_NAME.'product_feature_value'.SUPFIX_NAME,"pftID='$pftID'");
    }
  }

  // Dong bo filter
  return FilterSync($proID, $debug);
}
?>