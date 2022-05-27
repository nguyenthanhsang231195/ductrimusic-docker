<?
require_once('../config/config.php');
if(!CheckLogged()) exit;

// Phan trang
$rowsPerPage = empty($_GET['s']) ? 1000 : safe($_GET['s']);
$curPage = empty($_GET['p']) ? 1 : safe($_GET['p']);
$offset = ($curPage - 1) * $rowsPerPage;

echo '<h1 align="center"><a href="?p='.($curPage+1).'&s='.$rowsPerPage.'">Next page</a></h1>';

// Cache link
echo '<h2>Redirect link</h2>';
$s = "SELECT * FROM ".PREFIX_NAME.'seo_redirect'.SUPFIX_NAME."
  	  ORDER BY OldLink LIMIT $offset, $rowsPerPage";
if($rs = $dx->get_results($s)){
  $no = 0;
  echo '<table border="1" cellpadding="5" style="border-collapse:collapse">';
  foreach($rs as $r){
    $no++;
  	$uri = stripslashes($r->OldLink);
  	$link = stripslashes($r->Newlink);
  	echo "<tr>
      <td width='30'>$no</td>
      <td width='50%'>$uri</td>
      <td>$link</td>
    </tr>";
  }
  echo '</table>';
}

echo '<p>Finished! --> <a href="?p='.($curPage+1).'&s='.$rowsPerPage.'">Next page</a></p>';
?>