<?
require_once('../config/config.php');
if(!CheckLogged()) exit;

// Khoi tao ngon ngu
language();


// Danh sach filter
$s = "SELECT * FROM ".PREFIX_NAME.'feature'.SUPFIX_NAME;
$list = [];
if($rs = $dx->get_results($s)){
  foreach($rs as $r){
    // Da ngon ngu
    if(MULTI_LANGUAGE && lc()!=DEFAULT_LANGUAGE){
      $ss = "SELECT * FROM ".PREFIX_NAME."feature_lg".SUPFIX_NAME."
             WHERE lgID='".$r->feaID."' ".lw('AND');
      if($rr = $dx->get_row($ss)) $r->Ten = $rr->Ten;
    }

    // Gia tri filter
    $ss = "SELECT * FROM ".PREFIX_NAME.'feature_item'.SUPFIX_NAME."
           WHERE feaID='".$r->feaID."'";
    $item = [];
    if($rrs = $dx->get_results($ss)){
      foreach($rrs as $rr){
        // Da ngon ngu
        if(MULTI_LANGUAGE && lc()!=DEFAULT_LANGUAGE){
          $ls = "SELECT * FROM ".PREFIX_NAME."feature_item_lg".SUPFIX_NAME."
                 WHERE lgID='".$rr->fitID."' ".lw('AND');
          if($lr = $dx->get_row($ls)) $rr->Giatri = $lr->Giatri;
        }

        $item[] = [
          'id'    => $rr->fitID,
          'value' => stripslashes($rr->Giatri)
        ];
      }
    }

    $list[] = [
      'id'    => $r->feaID,
      'name'  => stripslashes($r->Ten),
      'item'  => $item
    ];
  }
}
echo '<h2>Filters</h2><pre>' . print_r($list, true) . '</pre>';

?>