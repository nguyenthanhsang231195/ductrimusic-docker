<?
require_once('../config/config.php');
if(!CheckLogged()) exit;

echo "<h2>Clear all cache</h2>";
ClearCache();
echo '<p>Finished!</p>';
?>