<?
require_once('../config/config.php');
set_time_limit(300); // 5 minutes

// Khoi tao ngon ngu
language();

echo '<h2>Product Price Parser</h2>';
$s = "SELECT * FROM ".PREFIX_NAME."product".SUPFIX_NAME."
      WHERE PriceSync='0' AND Active='1' ORDER BY NgayCN LIMIT 1";
if($rs = $dx->get_results($s)){
  echo '<table>';
  foreach($rs as $r){?>
    <tr>
      <td><?=$r->NgayCN?></td>
      <td><?=$r->SKU?></td>
      <td><?=stripslashes($r->Ten)?></td>
      <td><a href="https://www.ductrimusic.vn/piano/<?=$r->URL?>" target="_blank">/trang-suc/<?=$r->URL?></a></td>
    </tr>
    <?
    $proID = $r->proID;
    PriceParser($proID, true);

    $sql = "UPDATE ".PREFIX_NAME.'product'.SUPFIX_NAME."
            SET PriceSync='1' WHERE `proID`='$proID'";
    $dx->query($sql);
  }
  echo '</table>';
}
?>