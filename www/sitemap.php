<?
require_once('config/config.php');

$debug = false;

// Khoi tao ngon ngu
$lg = [];
$s = "SELECT * FROM ".PREFIX_NAME.'language'.SUPFIX_NAME."
      WHERE Active='1' ORDER BY Thutu";
if($rs = $dx->get_results($s)){
  foreach($rs as $r) $lg[] = $r->lang;
}

// Create sitemap
$sitemap = [];
foreach ($lg as $l) {
  ls($l);
  language($debug);

  $sitemap[] = URL_Rewrite('');
  $sitemap[] = URL_Rewrite('article');
  $sitemap[] = URL_Rewrite('kinh-nghiem');
  $sitemap[] = URL_Rewrite('contact');
  
  // San pham
  $s = "SELECT * FROM ".PREFIX_NAME."product_catalog" ;
  if($rs = $dx->get_results($s)){
    foreach($rs as $r){
      $link = URL_Rewrite($r->URL);
      $sitemap[] = $link;
    }
  }
  $s = "SELECT * FROM ".PREFIX_NAME."product".SUPFIX_NAME."
        WHERE Active='1' ORDER BY NgayCN DESC";
  if($rs = $dx->get_results($s)){
    foreach($rs as $r){
      $link = URL_Rewrite('piano',$r->URL);
      $sitemap[] = $link;
    }
  }

  // Bai viet
  $s = "SELECT * FROM ".PREFIX_NAME."article_type".SUPFIX_NAME." ORDER BY Thutu";
  if($rs = $dx->get_results($s)){
    foreach($rs as $r){
      $link = URL_Rewrite($r->URL);
      $sitemap[] = $link;
    }
  }

  $ss = "SELECT * FROM ".PREFIX_NAME."article_catalog".SUPFIX_NAME."
        WHERE chID='0' ".lw('AND')."
        ORDER BY Thutu DESC";
  if($rrs = $dx->get_results($ss)){
    foreach($rrs as $rr){
      $link = URL_Rewrite('kinh-nghiem',$rr->URL);
      $sitemap[] = $link;
    }
  }
  $ss = "SELECT * FROM ".PREFIX_NAME."article".SUPFIX_NAME."
        WHERE typeID='1' AND Active='1' ".lw('AND')."
        ORDER BY NgayCN DESC";
  if($rrs = $dx->get_results($ss)){
    foreach($rrs as $rr){
      $link = URL_Rewrite($r->URL,$rr->URL);
      $sitemap[] = $link;
    }
  }
  $sss = "SELECT * FROM ".PREFIX_NAME."article".SUPFIX_NAME."
        WHERE typeID='2' AND Active='1' ".lw('AND')."
        ORDER BY NgayCN DESC";
  if($rzs = $dx->get_results($sss)){
    foreach($rzs as $rz){
      $link = URL_Rewrite($r->URL,$rz->URL);
      $sitemap[] = $link;
    }
  }
  $sz = "SELECT * FROM ".PREFIX_NAME."article".SUPFIX_NAME."
        WHERE typeID='4' AND Active='1' ".lw('AND')."
        ORDER BY NgayCN DESC";
  if($rz = $dx->get_results($sz)){
    foreach($rz as $rw){
      $link = URL_Rewrite('goc-bao-chi',$rw->URL);
      $sitemap[] = $link;
    }
  }

  $ss = "SELECT * FROM ".PREFIX_NAME."article".SUPFIX_NAME."
          WHERE typeID='0' AND Active='1' ".lw('AND')."
          ORDER BY NgayCN DESC";
  if($rrs = $dx->get_results($ss)){
    foreach($rrs as $rr){
      $link = URL_Rewrite('article',$rr->URL);
      $sitemap[] = $link;
    }
  }


  // News
  $s = "SELECT * FROM ".PREFIX_NAME."news".SUPFIX_NAME."
        WHERE Active='1' ".lw('AND')." ORDER BY NgayCN DESC";
  if($rs = $dx->get_results($s)){
    foreach($rs as $r){
      $link = URL_Rewrite('kinh-nghiem',$r->URL);
      $sitemap[] = $link;
    }
  }

}


/* XML Sitemap Structure
<?xml version="1.0" encoding="UTF-8"?>
  <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
      <loc>http://www.example.com/</loc>
      <lastmod>2018-04-08</lastmod>
      <changefreq>daily</changefreq>
      <priority>0.8</priority>
    </url>
  </urlset>
*/
$host = Protocol().'://'.$_SERVER['HTTP_HOST'];
if(substr($_SERVER['REQUEST_URI'],-4)=='.xml') {
  $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
  foreach($sitemap as $link) $xml .= "<url><loc>$host$link</loc></url>\n";
  $xml .= "</urlset>";

  header('Content-Type: application/xml; charset=utf-8');
  echo $xml;
}
else {
  if($debug) {
    echo '<ol>';
    foreach($sitemap as $link) echo "<li>$host$link</li>";
    echo '</ol>';
  }
  else {
    header('Content-Type: text/plain');
    foreach($sitemap as $link) echo "$host$link\n";
  }
}
?>