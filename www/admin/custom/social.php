<?
$f = str_replace('\\','/',__FILE__);
$d = str_replace('/admin/custom/social.php','',$f);
define('SERVER_PATH', $d);
require_once(SERVER_PATH.'/config/config.php');
if(!CheckLogged()) exit;

// Ngon ngu, quyen truy cap
language();
require_once(SERVER_PATH.'/config/perm.php');

// Kiem tra quyen truy cap
$href = 'custom/social.php';
foreach($QSV['perm'] as $key=>$perm){
	if($perm['href']==$href) CheckKey($key);
}

$title = lg('Social connect');
$id = WebsiteID();

$table = PREFIX_NAME.'website'.SUPFIX_NAME;
$key = 'webID';
$fields = array(
	"Facebook" => array(
		"title" => lg("Facebook"),
		"create" => true,
		"edit" => true,
		"list" => true,
		"sorting" => true,
		"type" => "text",
		"visibility" => "visible",
		"width" => "20%"
	),
	"Google" => array(
		"title" => lg("Google"),
		"create" => true,
		"edit" => true,
		"list" => true,
		"sorting" => true,
		"type" => "text",
		"visibility" => "visible",
		"width" => "20%"
	),
	"Instagram" => array(
		"title" => lg("Instagram"),
		"create" => true,
		"edit" => true,
		"list" => true,
		"sorting" => true,
		"type" => "text",
		"visibility" => "visible",
		"width" => "20%"
	),
	"Youtube" => array(
		"title" => lg("Youtube"),
		"create" => true,
		"edit" => true,
		"list" => true,
		"sorting" => true,
		"type" => "text",
		"visibility" => "visible",
		"width" => "20%"
  ),
	"Other" => array(
		"title" => lg("Other"),
		"create" => true,
		"edit" => true,
		"list" => true,
		"sorting" => true,
		"type" => "text",
		"visibility" => "visible",
		"width" => "20%"
	)
);

// Prevent conflicted id
$guid = date('His');

// Default view data
if(empty($_GET['a'])) $_GET['a'] = 'view';

// View data from database
if($_GET['a']=='view'){?>
<div class="panel panel-default magbtmz">
  <div class="panel-heading"><?=$title?>
    <div class="control">
      <button type="button" class="btn btn-default btn-xs" onClick="Load('/admin/custom/social.php?a=view')"><i class="icon-list-alt"></i><?=lg('View')?></button>
      <button type="button" class="btn btn-success btn-xs" onClick="Refresh()"><i class="icon-refresh"></i><?=lg('Refresh')?></button>
      <button type="button" class="btn btn-warning btn-xs" onClick="Load('/admin/custom/social.php?a=edit')"><i class="icon-edit"></i><?=lg('Edit')?></button>
    </div>
  </div>
  <div class="panel-body">
    <dl class="horizontal">
    <?
	$s = "SELECT * FROM $table WHERE `$key`='$id'";
	if($r = $dx->get_row($s)){
      foreach($fields as $name=>$field){?>
      <dt><?=$field['title']?></dt>
      <dd><?
		$data = stripslashes($r->$name);
		switch($field['type']){
			case 'textarea':
				$data = nl2br($data);
				break;
			
			case 'file':
				$type = $field['fileType'];
				if($type=='') $type = 'image';
				
				if($data!=''){
					if($field['fileType']=='image') $data = ImageDisplay($data,50);
					else $data = FileDisplay($data);
				}
				
				break;
			
			case 'date':
				$data = date('Y-m-d',strtotime($data));
				break;
			
			case 'time':
				$data = date('H:i:s',strtotime($data));
				break;
			
			case 'datetime':
				$data = date('Y-m-d H:i:s',strtotime($data));
				break;
			
			case 'radiobutton':
			case 'combobox':
				//options: {"0":"Female", "1":"Male", "2":"Other"}
				if(is_array($field['options'])){
					$data = $field['options'][$data];
				}
				//options: /process/opt.php?t=table&d=name&o=name&p=prid
				else{
					// Get data from server
					$result = GetContent($field['options'],$guid);
					//echo "Option: ". htmlentities($result);
					
					$json = json_decode($result,true);
					if(is_array($json)){
						$opt = $json['Options'];
						foreach($opt as $o){
							if($o['Value']==$data){
								$data = $o['DisplayText'];
								break;
							}
						}
					}
				}
				
				break;
				
			case 'checkbox':
				//values: {"0": "Disable", "1": "Active"}
				$data = $field['values'][$data];
				
				break;
		}
		
		if($data=='') $data = '&nbsp;';
		echo $data;
		?></dd>
      <?
	  }
	}
	?>
    </dl>
  </div>
</div>
<?
}

// Edit data from database
elseif($_GET['a']=='edit'){?>
<div class="panel panel-default magbtmz">
  <div class="panel-heading"><?=$title?>
    <div class="control">
      <button type="button" class="btn btn-default btn-xs" onClick="Load('/admin/custom/social.php?a=view')"><i class="icon-list-alt"></i><?=lg('View')?></button>
      <button type="button" class="btn btn-success btn-xs" onClick="Refresh()"><i class="icon-refresh"></i><?=lg('Refresh')?></button>
      <button type="button" class="btn btn-warning btn-xs" onClick="Load('/admin/custom/social.php?a=view')"><i class="icon-reply"></i><?=lg('Go Back')?></button>
    </div>
  </div>
  <div class="panel-body">
    <form id="Form<?=$guid?>" class="custom" action="/admin/custom/social.php?a=update" method="post">
      <dl class="horizontal">
      <?
	  $s = "SELECT * FROM $table WHERE `$key`='$id'";
	  if($r = $dx->get_row($s)){
		foreach($fields as $name=>$field){
		  if(!$field['edit']) continue;
		  ?>
        <dt>
          <label for="Edit<?=$guid?>-<?=$name?>"><?=$field['title']?></label>
        </dt>
        <dd><?
		$data = stripslashes($r->$name);
		
		switch($field['type']){
			case 'password':
				echo '<input type="password" id="Edit'.$guid.'-'.$name.'" name="'.$name.'" value="'.$data.'" class="form-control">';
				break;
			
			case 'textarea':
				echo '<textarea id="Edit'.$guid.'-'.$name.'" name="'.$name.'" class="form-control" rows="3">'.$data.'</textarea>';
				break;
			
			case 'editor':
				echo '<textarea id="Edit'.$guid.'-'.$name.'" name="'.$name.'" class="form-control tinymce" rows="3">'.$data.'</textarea>';
				break;
			
			case 'file':
				$type = $field['fileType'];
				if($type=='') $type = 'image';
				
				$display = '';
				if($data!=''){
					if($type=='image') $display = ImageDisplay($data,50);
					else $display = FileDisplay($data);
				}
				
				echo '<div id="Edit'.$guid.'-'.$name.'" class="plupload clearfix" data-type="'.$type.'">
					<div class="pull-left">
					  <a id="Pick'.$guid.'-'.$name.'" href="#pick" class="btn btn-info btn-sm">Upload file</a>
					  <div class="status">No runtime found.</div>
					  <input type="hidden" name="'.$name.'" value="'.$data.'">
					</div>
					<div class="preview pull-right">'.$display.'</div>
				</div>';
				
				break;
			
			case 'files':
				$type = $field['fileType'];
				if($type=='') $type = 'image';
				
				echo '<div id="Edit'.$guid.'-'.$name.'" class="mupload" data-name="'.$name.'" data-type="'.$type.'">
					<a id="Pick'.$guid.'-'.$name.'" href="#pick" class="btn btn-info btn-sm">Upload file</a>
					<div class="status">No runtime found.</div>
					<div class="preview">';
				
				// View list files
				$display = '';
				if($data!=''){
					$list = explode(',',$data);
					$no = 0;
					foreach($list as $value){
						if($type=='image') $display = ImageDisplay($value,80);
						else $display = FileDisplay($value);
						echo '<div id="File'.$guid.'-'.(++$no).'">'.$display.'<input type="hidden" name="'.$name.'[]" value="'.$value.'"></div>';
					}
				}
				
				echo '</div></div>';
				
				break;
			
			case 'date':
				$data = date('Y-m-d',strtotime($data));
				echo '<input type="text" id="Edit'.$guid.'-'.$name.'" name="'.$name.'" value="'.$data.'" class="form-control date">';
				
				break;
			
			case 'time':
				$data = date('H:i:s',strtotime($data));
				echo '<input type="text" id="Edit'.$guid.'-'.$name.'" name="'.$name.'" value="'.$data.'" class="form-control time">';
				
				break;
			
			case 'datetime':
				$data = date('Y-m-d H:i:s',strtotime($data));
				echo '<input type="text" id="Edit'.$guid.'-'.$name.'" name="'.$name.'" value="'.$data.'" class="form-control datetime">';
				
				break;
			
			case 'radiobutton':
			case 'combobox':
				$options = array();
				
				//options: {"0":"Female", "1":"Male", "2":"Other"}
				if(is_array($field['options'])){
					$options = $field['options'];
				}
				//options: /process/opt.php?t=table&d=name&o=name&p=prid
				else{
					// Get data from server
					$result = GetContent($field['options'],$guid);
					//echo "Option: ". htmlentities($result);
					
					$json = json_decode($result,true);
					if(is_array($json)){
						$opt = $json['Options'];
						foreach($opt as $o){
							$options[$o['Value']] = $o['DisplayText'];
						}
					}
				}
				
				echo '<select id="Edit'.$guid.'-'.$name.'" name="'.$name.'">';
				foreach($options as $i=>$v){
				   echo '<option value="'.$i.'"';
				   if ($data==$i) echo "selected";
				   echo '>'.$v.'</option>';
				}
				echo '</select>';
				
				break;
			
			case 'list':
				$options = array();
				
				//options: {"0":"Female", "1":"Male", "2":"Other"}
				if(is_array($field['options'])){
					$options = $field['options'];
				}
				//options: /process/opt.php?t=table&d=name&o=name&p=prid
				else{
					// Get data from server
					$result = GetContent($field['options'],$guid);
					//echo "Option: ". htmlentities($result);
					
					$json = json_decode($result,true);
					if(is_array($json)){
						$opt = $json['Options'];
						foreach($opt as $o){
							$options[$o['Value']] = $o['DisplayText'];
						}
					}
				}
				
				echo '<div id="Edit'.$guid.'-'.$name.'" class="checkbox">';
				$list = explode(',',$data);
				foreach($options as $i=>$v){
					if($v=='') continue;
					echo '<label><input type="checkbox" name="'.$name.'[]" value="'.$i.'"';
					if(in_array($i, $list)) echo "checked";
					echo '> '.$v.'</label><br>';
				}
				echo '</div>';
				
				break;
			
			case 'checkbox':
				//values: {"0": "Disable", "1": "Active"}
				echo '<div id="Edit'.$guid.'-'.$name.'">';
				foreach($field['values'] as $i=>$v){
					echo '<label class="radio-inline"><input type="radio" name="'.$name.'" value="'.$i.'"';
					if ($data==$i) echo "checked";
					echo '>'.$v.'</label>';
				}
				echo '</div>';
				
				break;
			
			case 'auto':
				echo '<input type="text" id="Edit'.$guid.'-'.$name.'" name="'.$name.'" value="'.$data.'" class="form-control code" readonly>';
				
				break;
			
			case 'age':
				$year = date('Y',strtotime($data));
				$age = date('Y')-$year;
				echo '<div id="Edit'.$guid.'-'.$name.'"><input type="text" name="Age" value="'.$age.'" class="form-control age"> tuổi hoặc nhập <input type="text" name="'.$name.'" value="'.$data.'" class="form-control birthday"></div>';
				
				break;
			
			default:
				echo '<input type="text" id="Edit'.$guid.'-'.$name.'" name="'.$name.'" value="'.$data.'" class="form-control">';
		}
		?></dd>
        <?
		}
	  }
	  ?>
        <dd>
          <button type="button" class="btn btn-primary btn-sm" onClick="SubmitForm('Form<?=$guid?>')"><?=lg('Update')?></button>
          <button type="reset" class="btn btn-default btn-sm"><?=lg('Reset')?></button>
        </dd>
      </dl>
    </form>
  </div>
</div>
<script>BuildForm('Form<?=$guid?>')</script>
<?
}

// Update data to database
elseif($_GET['a']=='update'){?>
<div class="panel panel-default magbtmz">
  <div class="panel-heading"><?=$title?>
    <div class="control">
      <button type="button" class="btn btn-default btn-xs" onClick="Load('/admin/custom/social.php?a=view')"><i class="icon-list-alt"></i><?=lg('View')?></button>
      <button type="button" class="btn btn-warning btn-xs" onClick="Load('/admin/custom/social.php?a=edit')"><i class="icon-reply"></i><?=lg('Go Back')?></button>
    </div>
  </div>
  <div class="panel-body">
    <p><?
	$val = array();
	foreach(array_keys($fields) as $f){
		// Lay gia tri duoc truyen vao
		if(isset($_POST[$f])){
			if($fields[$f]['type']=='editor') $val[$f] = safeHTML($_POST[$f]);
			else $val[$f] = safe($_POST[$f]);
		}
		
		// Tu dong xac dinh gia tri
		elseif($f=='URL'){
			$title = '';
			if(isset($_POST['Ten'])) $title = safe($_POST['Ten']);
			elseif(isset($_POST['Name'])) $title = safe($_POST['Name']);
			elseif(isset($_POST['Tuade'])) $title = safe($_POST['Tuade']);
			
			$url = str_normal($title).'-'.$id;
			$val[$f] = $url;
		}
		
		// Xu ly ma hoa password
		if($fields[$f]['type']=='password'){
			$newpass = $val[$f];
			$oldpass = GetField($dx,$table,"`$key`='$id'",$f);
			if($newpass!=$oldpass) $val[$f] = EncodePass($newpass);
		}
		
		if(is_array($val[$f])){
			// Xu ly cho danh sach du lieu
			if($fields[$f]['type']=='list') $val[$f] = join(',',$val[$f]);
			
			// Xu ly cho upload nhieu file
			elseif($fields[$f]['type']=='files') $val[$f] = join(',',$val[$f]);
		}
	}
	//echo 'VALUES: <pre>'.print_r($val,true).'</pre>';
	
	$pair = array();
	foreach($val as $k=>$v) $pair[] = "`$k`='$v'";
	
	//Update record into database
    $alert = 'Nothing update!';
	if(count($pair)>0){
		$s = "UPDATE $table SET ".join(",",$pair)." WHERE `$key`='$id'";
		//echo "SQL: $s<br>";
		if($dx->query($s)) $alert = 'Update successful!';
		else $alert = 'Update faulty!';
	}
	
	echo $alert;
    ?></p>
    <button type="button" class="btn btn-default btn-sm" onClick="Load('/admin/custom/social.php?a=view')"><i class="icon-play"></i> <?=lg('Continue')?></button>
  </div>
</div>
<? }?>
