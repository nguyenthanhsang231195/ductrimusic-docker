<?
$f = str_replace('\\','/',__FILE__);
$d = str_replace('/admin/custom/statis.php','',$f);
define('SERVER_PATH', $d);
require_once(SERVER_PATH.'/config/config.php');
if(!CheckLogged()) exit;

// Ngon ngu, quyen truy cap
language();
require_once(SERVER_PATH.'/config/perm.php');

// Kiem tra quyen truy cap
$php = str_replace('\\','/',__FILE__);
$href = str_replace(SERVER_PATH.'/admin/','',$php);
foreach($QSV['perm'] as $key=>$perm){
	if($perm['href']==$href) CheckKey($key);
}


// Trang thai
$status = GetField($dx,PREFIX_NAME.'website'.SUPFIX_NAME,
  "webID='".WebsiteID()."'",'Status'
);
if($status==0) $status = '<span style="color:#00f">'.lg('Running').'</span>';
else $status = '<span style="color:#f00">'.lg('Pause').'</span>';

// Thong ke website
$left = array(
	lg('Total article')		=> NumOfRows($dx,PREFIX_NAME.'article'.SUPFIX_NAME),
	lg('Total catalog')		=> NumOfRows($dx,PREFIX_NAME.'article_catalog'.SUPFIX_NAME),
	lg('Total slideshow')		=> NumOfRows($dx,PREFIX_NAME.'slideshow'.SUPFIX_NAME),
	lg('Total menu')			=> NumOfRows($dx,PREFIX_NAME.'menu'.SUPFIX_NAME)
);
$right = array(
	lg('Total administrator')	=> NumOfRows($dx,PREFIX_NAME.'member'.SUPFIX_NAME,"`Type`='1'"),
	lg('Total staff')		=> NumOfRows($dx,PREFIX_NAME.'member'.SUPFIX_NAME,"`Type`='2'"),
	lg('Total member')	=> NumOfRows($dx,PREFIX_NAME.'member'.SUPFIX_NAME,"`Type`='3'"),
	lg('Total customer')	=> NumOfRows($dx,PREFIX_NAME.'member'.SUPFIX_NAME,"`Type`='5'")
);

?>
<div id="statis" class="panel panel-default">
  <div class="panel-heading"><?=lg('Website info')?>
    <div class="control">
      <button type="button" class="btn btn-success btn-xs" onClick="Refresh()"><i class="icon-refresh"></i><?=lg('Refresh')?></button>
    </div>
  </div>
  <div class="panel-body">
    <dl class="horizontal">
      <dt><?=lg('User')?></dt>
      <dd><?=QsvName()?></dd>
      <dt><?=lg('Status')?></dt>
      <dd><?=$status?></dd>
    </dl>
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-md-6">
    <div class="panel panel-default magbtmz">
      <div class="panel-heading"><?=lg('Page statistics')?></div>
      <ul class="list-group">
        <? foreach($left as $n=>$v){?>
        <li class="list-group-item"><span class="badge"><?=$v?></span><?=$n?></li>
        <? }?>
      </ul>
    </div>
  </div>
  <div class="col-xs-12 col-md-6">
    <div class="panel panel-default magbtmz">
      <div class="panel-heading"><?=lg('User statistics')?></div>
      <ul class="list-group">
        <? foreach($right as $n=>$v){?>
        <li class="list-group-item"><span class="badge"><?=$v?></span><?=$n?></li>
        <? }?>
      </ul>
    </div>
  </div>
</div>
