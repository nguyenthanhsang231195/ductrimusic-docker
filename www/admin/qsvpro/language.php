<?
$f = str_replace('\\','/',__FILE__);
$d = str_replace('/admin/qsvpro/language.php','',$f);
define('SERVER_PATH', $d);
require_once(SERVER_PATH.'/config/config.php');
if(!CheckLogged()) exit;

// Ngon ngu, quyen truy cap
language();
require_once(SERVER_PATH.'/config/perm.php');

// Kiem tra quyen truy cap
$href = 'qsvpro/language.php';
foreach($QSV['perm'] as $key=>$perm){
	if($perm['href']==$href) CheckKey($key);
}

$title = lg('Language');
$table = PREFIX_NAME.'language'.SUPFIX_NAME;
$key = 'lgID';
$fields = array(
	"Ten" => array(
		"title" => lg("Language name"),
		"create" => true,
		"edit" => true,
		"list" => true,
		"sorting" => true,
		"type" => "text",
		"visibility" => "fixed",
		"width" => "30%"
	),
  "lang" => array(
		"title" => lg("Language code"),
		"create" => true,
		"edit" => false,
		"list" => true,
		"defaultValue" => "vn",
		"sorting" => true,
		"type" => "text",
		"visibility" => "visible",
		"width" => "20%"
	),
	"Anh" => array(
		"title" => lg("Image"),
		"create" => true,
		"edit" => true,
		"list" => true,
		"sorting" => false,
		"type" => "file",
		"visibility" => "visible",
		"width" => "10%",
		"fileType" => "image"
  ),
  "Thutu" => array(
		"title" => lg("Order"),
		"create" => true,
		"edit" => true,
		"list" => true,
		"sorting" => true,
		"type" => "text",
		"visibility" => "visible",
		"width" => "10%"
	),
	"Active" => array(
		"title" => lg("Active"),
		"create" => true,
		"edit" => true,
		"list" => true,
		"defaultValue" => "1",
		"sorting" => true,
		"type" => "checkbox",
		"visibility" => "visible",
		"width" => "10%",
		"values" => array("0" => "Disable","1" => "Active")
  ),
  "NgayCN" => array(
		"title" => lg("Update"),
		"create" => false,
		"edit" => true,
		"list" => true,
		"sorting" => true,
		"type" => "datetime",
		"visibility" => "visible",
		"width" => "15%"
	)
);

// Prevent conflicted id
$guid = date('His');

// Default view data
if(empty($_GET['a'])) $_GET['a'] = 'list';

if($_GET['a']=='list'){
  $wh = "";
  $ob = "ORDER BY Thutu ASC";
  
  // Pagination
  $rows = 20;
  $page = isset($_POST['page'])?safe($_POST['page']):1;
  $offset = ($page-1) * $rows;
  $limit = "LIMIT $offset,$rows";
  ?>
<div class="panel panel-default magbtmz">
  <div class="panel-heading"><?=$title?>
    <div class="control">
      <button type="button" class="btn btn-success btn-xs" onClick="Refresh()"><i class="icon-refresh"></i><?=lg('Refresh')?></button>
      <button type="button" class="btn btn-warning btn-xs" onClick="Load('/admin/qsvpro/language.php?a=add')"><i class="icon-plus"></i><?=lg('Add')?></button>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>#</th>
          <?
          foreach($fields as $field){
			if(!$field['list']) continue;
			?>
          <th width="<?=$field['width']?>"><?=$field['title']?></th>
          <? }?>
          <th></th>
        </tr>
      </thead>
      <tbody>
      <?
	  $s = "SELECT * FROM $table $wh $ob $limit";
	  $no = 0;
      if($rs = $dx->get_results($s)){
		foreach($rs as $r){
		  $id = $r->$key;
		  ?>
        <tr>
          <td><?=++$no?></td>
          <?
          foreach($fields as $name=>$field){
			if(!$field['list']) continue;
			?>
          <td><?
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
                
			case 'money':
				$data = format_money($data);
				break;
		  }
		  
		  if($data=='') $data = '&nbsp;';
		  elseif($name=='URL') $data = '/'.$field['display'].'/'.$data;
		  echo $data;
		  ?></td>
          <? }?>
    <td><div class="action"><a href="#view" onClick="return Load('/admin/qsvpro/language.php?a=view',{'id':<?=$id?>})" title="View"><i class="icon-eye-open"></i> <?=lg('View')?></a> <a href="#edit" onClick="return Load('/admin/qsvpro/language.php?a=edit',{'id':<?=$id?>})" title="Edit"><i class="icon-edit"></i> <?=lg('Modify')?></a> <? if($id>1){?><a href="#del" onClick="return Delete('/admin/qsvpro/language.php?a=del',<?=$id?>)" title="Delete"><i class="icon-trash"></i> <?=lg('Delete')?></a><? }?></div></td>
        </tr>
        <?
        }
	  }
	  ?>
      </tbody>
    </table>
  </div>
  <?
  $total = NumOfPages($dx,$table,$wh,$rows);
  LoadPage($total,$page,'/admin/qsvpro/language.php?a=list');
  ?>
</div>
<?
}

// View data from database
elseif($_GET['a']=='view'){
  $id = safe($_POST['id']);
?>
<div class="panel panel-default magbtmz">
  <div class="panel-heading"><?=$title?>
    <div class="control">
      <button type="button" class="btn btn-default btn-xs" onClick="Load('/admin/qsvpro/language.php?a=list')"><i class="icon-list-alt"></i><?=lg('List')?></button>
      <button type="button" class="btn btn-success btn-xs" onClick="Refresh()"><i class="icon-refresh"></i><?=lg('Refresh')?></button>
    </div>
  </div>
  <div class="panel-body">
    <dl class="horizontal">
      <?
	$s = "SELECT * FROM $table WHERE `$key`='$id'";
	if($r = $dx->get_row($s)){
      foreach($fields as $name=>$field){
		if(!$field['create'] && !$field['edit']) continue;
		?>
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
					if($field['fileType']=='image') $data = ImageDisplay($data,70);
					else $data = FileDisplay($data);
				}
				
				break;
			
			case 'files':
				$type = $field['fileType'];
				if($type=='') $type = 'image';
				
				if($data!=''){
					$list = explode(',',$data);
					
					$data = '<div class="display">';
					foreach($list as $value){
						if($type=='image') $display = ImageDisplay($value,70);
						else $display = FileDisplay($value);
						$data .= $display;
					}
					$data .= '</div>';
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
			
			case 'list':
				if(empty($data)) $data = '';
				else{
					$view = array();
					$list = explode(',',$data);
					foreach($list as $val){
						//options: {"0":"Female", "1":"Male", "2":"Other"}
						if(is_array($field['options'])){
							$view[] = $field['options'][$val];
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
									if($o['Value']==$val){
										$view[] = $o['DisplayText'];
										break;
									}
								}
							}
						}
					}
					$data = join('<br>',$view);
				}
				
				break;
			
			case 'checkbox':
				//values: {"0": "Disable", "1": "Active"}
				$data = $field['values'][$data];
				
				break;
            
			case 'money':
				$data = format_money($data);
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

// Add data to database
elseif($_GET['a']=='add'){?>
<div class="panel panel-default magbtmz">
  <div class="panel-heading"><?=$title?>
    <div class="control">
      <button type="button" class="btn btn-default btn-xs" onClick="Load('/admin/qsvpro/language.php?a=list')"><i class="icon-list-alt"></i><?=lg('List')?></button>
      <button type="button" class="btn btn-success btn-xs" onClick="Refresh()"><i class="icon-refresh"></i><?=lg('Refresh')?></button>
    </div>
  </div>
  <div class="panel-body">
    <form id="Form<?=$guid?>" class="custom" action="/admin/qsvpro/language.php?a=insert" method="post">
      <dl class="horizontal">
        <?
		foreach($fields as $name=>$field){
		  if(!$field['create']) continue;
		  ?>
        <dt>
          <label for="Add<?=$guid?>-<?=$name?>"><?=$field['title']?></label>
        </dt>
        <dd><?
		switch($field['type']){
			case 'password':
				echo '<input type="password" id="Add'.$guid.'-'.$name.'" name="'.$name.'" value="" class="form-control">';
				break;
			
			case 'textarea':
				echo '<textarea id="Add'.$guid.'-'.$name.'" name="'.$name.'" class="form-control" rows="3"></textarea>';
				break;
			
			case 'editor':
				echo '<textarea id="Add'.$guid.'-'.$name.'" name="'.$name.'" class="form-control tinymce" rows="3"></textarea>';
				break;
			
			case 'file':
				$type = $field['fileType'];
				if($type=='') $type = 'image';
				
				echo '<div id="Add'.$guid.'-'.$name.'" class="plupload clearfix" data-type="'.$type.'">
					<div class="pull-left">
					  <a id="Pick'.$guid.'-'.$name.'" href="#pick" class="btn btn-info btn-sm">Upload file</a>
					  <div class="status">No runtime found.</div>
					  <input type="hidden" name="'.$name.'" value="">
					</div>
					<div class="preview pull-right"></div>
				</div>';
				
				break;
			
			case 'files':
				$type = $field['fileType'];
				if($type=='') $type = 'image';
				
				echo '<div id="Add'.$guid.'-'.$name.'" class="mupload" data-name="'.$name.'" data-type="'.$type.'">
					<a id="Pick'.$guid.'-'.$name.'" href="#pick" class="btn btn-info btn-sm">Upload file</a>
					<div class="status">No runtime found.</div>
					<div class="preview"></div>
				</div>';
				
				break;
			
			case 'date':
				echo '<input type="text" id="Add'.$guid.'-'.$name.'" name="'.$name.'" value="'.date('Y-m-d').'" class="form-control date">';
				
				break;
			
			case 'time':
				echo '<input type="text" id="Add'.$guid.'-'.$name.'" name="'.$name.'" value="'.date('H:i:s').'" class="form-control time">';
				
				break;
			
			case 'datetime':
				echo '<input type="text" id="Add'.$guid.'-'.$name.'" name="'.$name.'" value="'.date('Y-m-d H:i:s').'" class="form-control datetime">';
				
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
				
				echo '<select id="Add'.$guid.'-'.$name.'" name="'.$name.'">';
				foreach($options as $i=>$v){
				   echo '<option value="'.$i.'">'.$v.'</option>';
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
				
				echo '<div id="Add'.$guid.'-'.$name.'" class="checkbox">';
				foreach($options as $i=>$v){
					if($v=='') continue;
					echo '<label><input type="checkbox" name="'.$name.'[]" value="'.$i.'"> '.$v.'</label><br>';
				}
				echo '</div>';
				
				break;
			
			case 'checkbox':
				//values: {"0": "Disable", "1": "Active"}
				echo '<div id="Add'.$guid.'-'.$name.'">';
				foreach($field['values'] as $i=>$v){
					echo '<label class="radio-inline"><input type="radio" name="'.$name.'" value="'.$i.'">'.$v.'</label>';
				}
				echo '</div>';
				
				break;
			
			case 'auto':
				echo '<input type="text" id="Add'.$guid.'-'.$name.'" name="'.$name.'" value="" class="form-control code" readonly>';
				
				break;
			
			case 'age':
				echo '<div id="Add'.$guid.'-'.$name.'"><input type="text" name="Age" value="" class="form-control age"> tu???i ho???c nh???p <input type="text" name="'.$name.'" value="" class="form-control birthday"></div>';
				
				break;
			
			case 'money':
				echo '<input type="text" id="Edit'.$guid.'-'.$name.'" name="'.$name.'" value="" class="form-control" style="width:100px"> VN??';	
				break;
			
			default:
				echo '<input type="text" id="Add'.$guid.'-'.$name.'" name="'.$name.'" value="" class="form-control">';
		}
		?></dd>
        <? }?>
        <dd>
          <button type="button" class="btn btn-primary btn-sm" onClick="SubmitForm('Form<?=$guid?>')"><?=lg('Insert')?></button>
          <button type="reset" class="btn btn-default btn-sm"><?=lg('Reset')?></button>
        </dd>
      </dl>
    </form>
  </div>
</div>
<script>BuildForm('Form<?=$guid?>')</script> 
<?
}

// Edit data from database
elseif($_GET['a']=='edit'){
  $id = safe($_POST['id']);
  ?>
<div class="panel panel-default magbtmz">
  <div class="panel-heading"><?=$title?>
    <div class="control">
      <button type="button" class="btn btn-default btn-xs" onClick="Load('/admin/qsvpro/language.php?a=list')"><i class="icon-list-alt"></i><?=lg('List')?></button>
      <button type="button" class="btn btn-success btn-xs" onClick="Refresh()"><i class="icon-refresh"></i><?=lg('Refresh')?></button>
    </div>
  </div>
  <div class="panel-body">
    <form id="Form<?=$guid?>" class="custom" action="/admin/qsvpro/language.php?a=update" method="post">
      <input type="hidden" name="id" value="<?=$id?>">
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
				echo '<div id="Edit'.$guid.'-'.$name.'"><input type="text" name="Age" value="'.$age.'" class="form-control age"> tu???i ho???c nh???p <input type="text" name="'.$name.'" value="'.$data.'" class="form-control birthday"></div>';
				
				break;
			
			case 'money':
				echo '<input type="text" id="Edit'.$guid.'-'.$name.'" name="'.$name.'" value="'.format_money($data,'',0).'" class="form-control" style="width:100px"> VN??';	
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

// Insert data to database
elseif($_GET['a']=='insert'){?>
<div class="panel panel-default magbtmz">
  <div class="panel-heading"><?=$title?>
    <div class="control">
      <button type="button" class="btn btn-default btn-xs" onClick="Load('/admin/qsvpro/language.php?a=list')"><i class="icon-list-alt"></i><?=lg('List')?></button>
    </div>
  </div>
  <div class="panel-body">
    <p><?
	$val = array();
	$id = FirstID($dx,$key,$table);
	$val[$key] = $id;
	
	foreach(array_keys($fields) as $f){
		// Lay gia tri duoc truyen vao
		if(isset($_POST[$f])){
			if($fields[$f]['type']=='editor') $val[$f] = safeHTML($_POST[$f]);
			elseif($fields[$f]['type']=='money') $val[$f] = get_money($_POST[$f]);
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
		elseif($fields[$f]['create']==false){
			if($fields[$f]['type']=='date') $val[$f] = date('Y-m-d');
			elseif($fields[$f]['type']=='time') $val[$f] = date('H:i:s');
			elseif($fields[$f]['type']=='datetime') $val[$f] = date('Y-m-d H:i:s');
		}
		
		// Tu dong sinh ma
		if($fields[$f]['type']=='auto') $val[$f] = sprintf('%04d',$id);
		
		// Xu ly ma hoa password
		if($fields[$f]['type']=='password') $val[$f] = EncodePass($val[$f]);
		
		if(is_array($val[$f])){
			// Xu ly cho danh sach du lieu
			if($fields[$f]['type']=='list') $val[$f] = join(',',$val[$f]);
			
			// Xu ly cho upload nhieu file
			elseif($fields[$f]['type']=='files') $val[$f] = join(',',$val[$f]);
		}
	}
	//echo 'VALUES: <pre>'.print_r($val,true).'</pre>';
	
	//Insert record into database
  $s = "INSERT INTO $table(`".join("`,`",array_keys($val))."`) VALUES('".join("','",array_values($val))."')";
	//echo "SQL: $s<br>";
	if($dx->query($s)) {
    $langcode = $val['lang'];
    $langdef = DEFAULT_LANGUAGE;
    $memID = QsvMember();

    // Them cau hinh website
    if(!CheckField($dx,PREFIX_NAME.'website'.SUPFIX_NAME,"lang='$langcode'")) {
      $s = "SELECT * FROM ".PREFIX_NAME.'website'.SUPFIX_NAME."
            WHERE lang='$langdef'";
	    if($r = $dx->get_row($s)) {
        $webID = FirstID($dx,'webID',PREFIX_NAME.'website'.SUPFIX_NAME);
        $title = 'Website is under construction';
        $contact = $intro = 'Under construction';

        $ss = "INSERT INTO ".PREFIX_NAME.'website'.SUPFIX_NAME."(webID, Domain,
                  Tieude, Logo, Nlogo, Icon, Slogan,
                  Email, Hotline, Copyright, Contact, Map, Intro, 
                  Headinc, Tracker, Account, Baokim, Nganluong,
                  Facebook, Google, Instagram, Youtube, Other,
                  Ngaydang, Status, Active, memID, lang, lgID)
               VALUES('$webID', '$r->Domain', '$title',
                  '$r->Logo', '$r->Nlogo', '$r->Icon', '$r->Slogan',
                  '$r->Email', '$r->Hotline', '$r->Copyright',
                  '$contact', '$r->Map', '$intro', '$r->Headinc',
                  '$r->Tracker', '$r->Account', '$r->Baokim', '$r->Nganluong',
                  '$r->Facebook', '$r->Google', '$r->Instagram','$r->Youtube', 
                  '$r->Other', NOW(), '1', '1', '$memID',
                  '$langcode', '$r->webID')";
        //echo "$ss<br>";
        $dx->query($ss);
      }
    }

    // Them tat ca cac ban dich
    $s = "SELECT * FROM ".PREFIX_NAME."langdef".SUPFIX_NAME."
          WHERE lang='$langdef' ORDER BY `key`";
    if($rs = $dx->get_results($s)){
      foreach($rs as $r) {
        $key = stripslashes($r->key);
        
        // Kiem tra neu ton tai thi bo qua
        if(CheckField($dx,PREFIX_NAME.'langdef'.SUPFIX_NAME,"`key`='$key' AND lang='$langcode'")) continue;
        
        // Them vao database
        $id = FirstID($dx,'txtID',PREFIX_NAME.'langdef'.SUPFIX_NAME);
        $val = $key;	
        $ss = "INSERT INTO ".PREFIX_NAME.'langdef'.SUPFIX_NAME."(txtID, lang, 
                  `key`, `value`, addtime, uptime, memID)
               VALUES('$id', '$langcode', '".safe($key)."', '".safe($val)."',
                  NOW(), NOW(), '$memID')";
        //echo "$ss<br>";
        $dx->query($ss);
      }
    }

    // Clear cache
    unset($_SESSION['i18n'][$langcode]);

    $alert = 'Insert successful!';
  }
	else $alert = 'Insert faulty!';
	
	echo $alert;
    ?></p>
    <button type="button" class="btn btn-default btn-sm" onClick="Load('/admin/qsvpro/language.php?a=list')"><i class="icon-play"></i> <?=lg('Continue')?></button>
  </div>
</div>
  <?
  // Auto refresh if success
  if($dx->rows_affected>0){
	echo '<script>Load("/admin/qsvpro/language.php?a=list")</script>';
  }
}

// Update data to database
elseif($_GET['a']=='update'){
  $id = safe($_POST['id']);
  ?>
<div class="panel panel-default magbtmz">
  <div class="panel-heading"><?=$title?>
    <div class="control">
      <button type="button" class="btn btn-default btn-xs" onClick="Load('/admin/qsvpro/language.php?a=list')"><i class="icon-list-alt"></i><?=lg('List')?></button>
    </div>
  </div>
  <div class="panel-body">
    <p><?
	$val = array();
	foreach(array_keys($fields) as $f){
		// Lay gia tri duoc truyen vao
		if(isset($_POST[$f])){
			if($fields[$f]['type']=='editor') $val[$f] = safeHTML($_POST[$f]);
			elseif($fields[$f]['type']=='money') $val[$f] = get_money($_POST[$f]);
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
    <button type="button" class="btn btn-default btn-sm" onClick="Load('/admin/qsvpro/language.php?a=list')"><i class="icon-play"></i> <?=lg('Continue')?></button>
  </div>
</div>
  <?
  // Auto refresh if success
  if($dx->rows_affected>0){
	echo '<script>Load("/admin/qsvpro/language.php?a=list")</script>';
  }
}

// Delete data from database
elseif($_GET['a']=='del'){
  $id = safe($_POST['id']);
  ?>
<div class="panel panel-default magbtmz">
  <div class="panel-heading"><?=$title?>
    <div class="control">
      <button type="button" class="btn btn-default btn-xs" onClick="Load('/admin/qsvpro/language.php?a=list')"><i class="icon-list-alt"></i><?=lg('List')?></button>
    </div>
  </div>
  <div class="panel-body">
    <p><?
	//Delete from database
	$s = "DELETE FROM $table WHERE `$key`='$id'";
	if($dx->query($s)) $alert = 'Delete successful!';
	else $alert = 'Delete faulty!';
	
	echo $alert;
    ?></p>
    <button type="button" class="btn btn-default btn-sm" onClick="Load('/admin/qsvpro/language.php?a=list')"><i class="icon-play"></i> <?=lg('Continue')?></button>
  </div>
</div>
  <?
  // Auto refresh if success
  if($dx->rows_affected>0){
	echo '<script>Load("/admin/qsvpro/language.php?a=list")</script>';
  }
}
?>
