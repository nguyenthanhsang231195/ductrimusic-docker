<?
$f = str_replace('\\','/',__FILE__);
$d = str_replace('/admin/custom/password.php','',$f);
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

$title = lg('Change password');
$id = QsvMember();

$table = PREFIX_NAME.'member'.SUPFIX_NAME;
$key = 'memID';

// Prevent conflicted id
$guid = date('His');

// Default edit data
if(empty($_GET['a'])) $_GET['a'] = 'edit';

// Edit data from database
if($_GET['a']=='edit'){?>
<div class="panel panel-default magbtmz">
  <div class="panel-heading"><?=$title?>
    <div class="control">
      <button type="button" class="btn btn-success btn-xs" onClick="Refresh()"><i class="icon-refresh"></i><?=lg('Refresh')?></button>
    </div>
  </div>
  <div class="panel-body">
    <form id="Form<?=$guid?>" class="custom" action="/admin/custom/password.php?a=update" method="post">
      <dl class="horizontal">
        <dt>
          <label for="Edit<?=$guid?>-oldpass"><?=lg('Current Password')?></label>
        </dt>
        <dd>
          <input type="password" id="Edit<?=$guid?>-oldpass" name="oldpass" value="" class="form-control">
        </dd>
        <dt>
          <label for="Edit<?=$guid?>-newpass"><?=lg('New Password')?></label>
        </dt>
        <dd>
          <input type="password" id="Edit<?=$guid?>-newpass" name="newpass" value="" class="form-control">
        </dd>
        <dt>
          <label for="Edit<?=$guid?>-repass"><?=lg('Retype New Password')?></label>
        </dt>
        <dd>
          <input type="password" id="Edit<?=$guid?>-repass" name="repass" value="" class="form-control">
        </dd>
        <dd>
          <button type="button" class="btn btn-primary btn-sm" onClick="SubmitForm('Form<?=$guid?>')"><?=lg('Update')?></button>
          <button type="reset" class="btn btn-default btn-sm"><?=lg('Reset')?></button>
        </dd>
      </dl>
    </form>
  </div>
</div>
<?
}

// Update data to database
elseif($_GET['a']=='update'){?>
<div class="panel panel-default magbtmz">
  <div class="panel-heading"><?=$title?>
    <div class="control">
      <button type="button" class="btn btn-warning btn-xs" onClick="Load('/admin/custom/password.php?a=edit')"><i class="icon-reply"></i><?=lg('Go Back')?></button>
    </div>
  </div>
  <div class="panel-body">
    <p><?
	$oldpass 	= safe($_POST['oldpass']);
	$newpass 	= safe($_POST['newpass']);
	$repass 	= safe($_POST['repass']);
	
	//Update record into database
    $alert = 'Nothing update!';
	
	if($newpass=='' || $newpass!=$repass){
		$alert = "Wrong new password!";
	}
	else{
		$current = GetField($dx,$table,"`$key`='$id'",'Pass');
		if(EncodePass($oldpass)!=$current){
			$alert = "Wrong current password!";
		}
		else{
			$s = "UPDATE $table SET Pass='".EncodePass($newpass)."'
				  WHERE `$key`='$id'";
			if($dx->query($s)) $alert = 'Update successful!';
			else $alert = 'Update faulty!';
		}
	}
	
	echo $alert;
    
    ?></p>
    <button type="button" class="btn btn-default btn-sm" onClick="Load('/admin/custom/password.php?a=edit')"><i class="icon-play"></i> <?=lg('Continue')?></button>
  </div>
</div>
<? }?> 