<?
require_once('../config/config.php');
if(!CheckLogged()) exit;

// Khoi tao ngon ngu
language();

// Phan trang
$pageSize = empty($_GET['s']) ? 100 : safe($_GET['s']);
$pageNo = empty($_GET['p']) ? 1 : safe($_GET['p']);

echo '<h1 align="center"><a href="?p='.($pageNo+1).'&s='.$pageSize.'">Next page</a></h1>';
$list = GetDiamonds($pageNo,$pageSize,false);
//echo '<h1>List of Diamonds</h1><pre>'.print_r($list,true).'</pre>';


echo '<h2>Rapnet Diamonds</h2>';
$diamond = $list['response']['body']['diamonds'];
$time = date('Y-m-d H:i:s');
echo '<ol>';
foreach($diamond as $data){
  echo '<li>';

  // Shape
  $name = safe($data['shape']);
  $shapID = GetField($dx,PREFIX_NAME.'diamond_shape'.SUPFIX_NAME,"Ten='$name'",'shapID');
  if(empty($shapID) && $name!='') {
    $shapID	= FirstID($dx,'shapID',PREFIX_NAME.'diamond_shape'.SUPFIX_NAME);
    $s = "INSERT INTO ".PREFIX_NAME.'diamond_shape'.SUPFIX_NAME."(shapID, Ten, Active, NgayCN)
          VALUES('$shapID', '$name', 1, NOW())";
    $dx->query($s);
  }
  $data['shapID'] = $shapID;

  // Color
  $name = safe($data['color']);
  $colrID = GetField($dx,PREFIX_NAME.'diamond_color'.SUPFIX_NAME,"Ten='$name'",'colrID');
  if(empty($colrID) && $name!='') {
    $colrID	= FirstID($dx,'colrID',PREFIX_NAME.'diamond_color'.SUPFIX_NAME);
    $s = "INSERT INTO ".PREFIX_NAME.'diamond_color'.SUPFIX_NAME."(colrID, Ten, Active, NgayCN)
          VALUES('$colrID', '$name', 1, NOW())";
    $dx->query($s);
  }
  $data['colrID'] = $colrID;

  // Fancy Color
  $name = safe($data['fancy_color_dominant_color']);
  $fcolrID = GetField($dx,PREFIX_NAME.'diamond_fcolor'.SUPFIX_NAME,"Ten='$name'",'fcolrID');
  if(empty($fcolrID) && $name!='') {
    $fcolrID	= FirstID($dx,'fcolrID',PREFIX_NAME.'diamond_fcolor'.SUPFIX_NAME);
    $s = "INSERT INTO ".PREFIX_NAME.'diamond_fcolor'.SUPFIX_NAME."(fcolrID, Ten, Active, NgayCN)
          VALUES('$fcolrID', '$name', 1, NOW())";
    $dx->query($s);
  }
  $data['fcolrID'] = $fcolrID;

  // Clarity
  $name = safe($data['clarity']);
  $clarID = GetField($dx,PREFIX_NAME.'diamond_clarity'.SUPFIX_NAME,"Ten='$name'",'clarID');
  if(empty($clarID) && $name!='') {
    $clarID	= FirstID($dx,'clarID',PREFIX_NAME.'diamond_clarity'.SUPFIX_NAME);
    $s = "INSERT INTO ".PREFIX_NAME.'diamond_clarity'.SUPFIX_NAME."(clarID, Ten, Active, NgayCN)
          VALUES('$clarID', '$name', 1, NOW())";
    $dx->query($s);
  }
  $data['clarID'] = $clarID;

  // Cut
  $name = safe($data['cut']);
  $cutID = GetField($dx,PREFIX_NAME.'diamond_cut'.SUPFIX_NAME,"Ten='$name'",'cutID');
  if(empty($cutID) && $name!='') {
    $cutID	= FirstID($dx,'cutID',PREFIX_NAME.'diamond_cut'.SUPFIX_NAME);
    $s = "INSERT INTO ".PREFIX_NAME.'diamond_cut'.SUPFIX_NAME."(cutID, Ten, Active, NgayCN)
          VALUES('$cutID', '$name', 1, NOW())";
    $dx->query($s);
  }
  $data['cutID'] = $cutID;
  
  // Seller
  $sellrID = 0; // Table: diamond_seller
  $data['sellrID'] = $sellrID;

  // Diamond
  $data['minLi'] = min($data['meas_length'],$data['meas_width']);
  $data['maxLi'] = max($data['meas_length'],$data['meas_width']);
  $data['NgayCN'] = $time;
  
  $diamond = safe($data['diamond_id']);
  $diaID = GetField($dx,PREFIX_NAME.'diamond'.SUPFIX_NAME,"diamond_id='$diamond'",'diaID');
  if(empty($diaID)) {
    $diaID = FirstID($dx,'diaID',PREFIX_NAME.'diamond'.SUPFIX_NAME);
    $data['diaID'] = $diaID;
    $data['Active'] = 1;

    $data = safe($data);
    $sql = "INSERT INTO ".PREFIX_NAME.'diamond'.SUPFIX_NAME."(`".join("`,`",array_keys($data))."`)
            VALUES('".join("','",array_values($data))."')";
  }
  else {
    $pair = [];
    $data = safe($data);
    foreach($data as $k=>$v) $pair[] = "`$k`='$v'";
    
    $sql = "UPDATE ".PREFIX_NAME.'diamond'.SUPFIX_NAME." SET ".join(",",$pair)."
            WHERE `diaID`='$diaID'";
  }
  $dx->query($sql);

  echo "$sql</li>";
}
echo '</ol>';

/*
$detail = SingleDiamond(114641347,false);
echo '<h1>Diamond #114641347</h1><pre>'.print_r($detail,true).'</pre>';
*/

echo '<p>Finished! --> <a href="?p='.($pageNo+1).'&s='.$pageSize.'">Next page</a></p>';
?>