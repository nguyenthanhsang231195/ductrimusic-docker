<?
require_once('../config/config.php');
if(!CheckLogged()) exit;

// Khoi tao ngon ngu
language();

// Danh muc san pham
echo '<h2>Rewrite URL</h2>';

$catalog = [];
$s = "SELECT * FROM ".PREFIX_NAME."product_catalog".SUPFIX_NAME."
      WHERE chID='0' ORDER BY Thutu";
$list = [];
if($rs = $dx->get_results($s)){
  foreach($rs as $r){
    $catID = $r->catID;

    $feature = [];
    $fs = "SELECT * FROM ".PREFIX_NAME.'feature'.SUPFIX_NAME."
           WHERE Danhmuc LIKE '%#$catID#%' ORDER BY Ten";
    if($frs = $dx->get_results($fs)){
      foreach($frs as $fr){
        $ps = "SELECT DISTINCT fitID FROM ".PREFIX_NAME."product_feature_value".SUPFIX_NAME."
                WHERE pftID IN (
                  SELECT pftID FROM ".PREFIX_NAME.'product'.SUPFIX_NAME." AS P JOIN 
                                    ".PREFIX_NAME.'product_feature'.SUPFIX_NAME." AS F
                  ON P.proID = F.proID
                  WHERE F.feaID='".$fr->feaID."' AND P.Danhmuc LIKE '%#$catID#%'
                )";
        $value = [];
        if($prs = $dx->get_results($ps)){
          foreach($prs as $pr) $value[] = $pr->fitID;
        }
        //echo 'Feature value:<pre>'.print_r($value,true).'</pre>';

        // Gia tri filter
        $vs = "SELECT * FROM ".PREFIX_NAME.'feature_item'.SUPFIX_NAME."
                WHERE feaID='".$fr->feaID."' AND fitID IN('".join("','",$value)."')";
        //echo "SQL: $vs<br>";
        $item = [];
        if($vrs = $dx->get_results($vs)){
          foreach($vrs as $vr){
            $value = stripslashes($vr->Giatri);
            $item[] = [
              'id'    => $vr->fitID,
              'value' => $value,
              'url'   => str_normal($value)
            ];
          }
        }
        //echo 'Feature item:<pre>'.print_r($item,true).'</pre>';

        $name = stripslashes($fr->Ten);
        $feature[] = [
          'id'    => $fr->feaID,
          'name'  => $name,
          'url'   => str_normal($name),
          'item'  => $item
        ];
      }
    }

    /*
    // Muc gia
    $prices = [];
    $prices[] = [
      'id'	  => '',
      'name'	=> 'Tất cả giá',
      'mark'	=> !isset($_GET['pr']) || empty($_GET['pr'])
    ];

    $s = "SELECT * FROM ".PREFIX_NAME."price_range".SUPFIX_NAME."
          WHERE Danhmuc LIKE '%#$catID#%' ORDER BY Thutu";
    if($rs = $dx->get_results($s)){
      foreach($rs as $r){
        $pf = round($r->Giatu/1000000);
        $pt = round($r->Giaden/1000000);
        $id = "$pf|$pt";

        $prices[] = [
          'id'	  => $id,
          'name'  => stripslashes($r->Ten),
          'mark'	=> isset($_GET['pr']) && $_GET['pr']==$id
        ];
      }
    }
    //echo 'Price range:<pre>'.print_r($prices,true).'</pre>';
    */

    $catalog[] = [
      'id'	  => $r->catID,
      'name'  => stripslashes($r->Ten),
      'link'  => URL_Rewrite($r->URL),
      'filtr' => $feature
    ];

    if(count($feature)>0) {
      $ftr0 = $feature[0];
      foreach($ftr0['item'] as $itm0) {
        $link = URL_Rewrite($r->URL,$itm0['url']);
        $param = URL_Rewrite($r->URL).'?f['.$ftr0['id'].']='.$itm0['id'];
        $priority = 0;
        AddRewrite($link, $param, $priority);
        echo "Rewrite: $link --> $param<br>";

        if(count($feature)>1) {
          $ftr1 = $feature[1];
          foreach($ftr1['item'] as $itm1) {
            $link = URL_Rewrite($r->URL,$itm0['url'].'-'.$itm1['url']);
            $param = URL_Rewrite($r->URL).'?f['.$ftr0['id'].']='.$itm0['id'].'&f['.$ftr1['id'].']='.$itm1['id'];
            $priority = 1;
            AddRewrite($link, $param, $priority);
            echo "Rewrite: $link --> $param<br>";
          }
        }
      }
    }

    if(count($feature)>1) {
      $ftr1 = $feature[1];
      foreach($ftr1['item'] as $itm1) {
        $link = URL_Rewrite($r->URL,$itm1['url']);
        $param = URL_Rewrite($r->URL).'?f['.$ftr1['id'].']='.$itm1['id'];
        $priority = 0;
        AddRewrite($link, $param, $priority);
        echo "Rewrite: $link --> $param<br>";
      }
    }
  }
}
echo '<h2>Catalog</h2><pre>' . print_r($catalog, true) . '</pre>';

?>