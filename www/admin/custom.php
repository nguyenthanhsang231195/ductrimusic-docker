<?
require_once('../config/config.php');

// Only allow from localhost & admin
if(!IsLocal()) die('Access denied!');
if(!CheckLogged()) exit;
if(QsvMember()!==0) die('Access denied!');

$tbl = isset($_GET['tbl']) ? safe($_GET['tbl']) : 'website';

$module = '/admin/custom/_'.$tbl.'.php';
$table = PREFIX_NAME.$tbl.SUPFIX_NAME;
$infos = TableInfo($dx,$table);
$title = $infos['title'];

$fields = TableField($dx,$table);
$key = TableKey($dx,$table);
unset($fields[$key]);
//echo 'Fields: <pre>'.print_r($fields,true).'</pre>';

ob_start();
?>
<?="<?\n"?>
$f = str_replace('\\','/',__FILE__);
$d = str_replace('<?=$module?>','',$f);
define('SERVER_PATH', $d);
require_once(SERVER_PATH.'/config/config.php');
if(!CheckLogged()) exit;

// Ngon ngu, quyen truy cap
language();
require_once(SERVER_PATH.'/config/perm.php');

// Kiem tra quyen truy cap
$href = '<?=str_replace('/admin/','',$module)?>';
foreach($QSV['perm'] as $key=>$perm){
	if($perm['href']==$href) CheckKey($key);
}

$title = lg('<?=$title?>');
$id = WebsiteID();

$table = PREFIX_NAME.'<?=$tbl?>'.SUPFIX_NAME;
$key = '<?=$key?>';
$fields = array(<?
  echo "\n";
  $view = array();
  foreach($fields as $name=>$field){
	$list = array();
	foreach($field as $k=>$v){
		if(is_bool($v)) $v = $v?'true':'false';
		elseif(is_array($v)){
			$a = '';
			foreach($v as $i=>$n){
				$a .= '"'.$i.'" => "'.$n.'",';
			}
			$v = 'array('.trim($a,',').')';
		}
		elseif(is_string($v)){
			if($k=='title') $v = 'lg("'.$v.'")';
			else $v = '"'.$v.'"';
		}
		
		$list[] = "\t\t".'"'.$k.'" => '.$v;
		$value = join(",\n",$list);
	}
	$view[] = "\t".'"'.$name.'" => array('."\n".$value."\n\t".')';
  }
  echo join(",\n",$view)."\n";
  ?>
);

// Prevent conflicted id
$guid = date('His');

// Default view data
if(empty($_GET['a'])) $_GET['a'] = 'view';

// View data from database
if($_GET['a']=='view'){?>
<div class="panel panel-default magbtmz">
  <div class="panel-heading"><?="<?=\$title?>\n"?>
    <div class="control">
      <button type="button" class="btn btn-default btn-xs" onClick="Load('<?=$module?>?a=view')"><i class="icon-list-alt"></i><?="<?=lg('View')?>"?></button>
      <button type="button" class="btn btn-success btn-xs" onClick="Refresh()"><i class="icon-refresh"></i><?="<?=lg('Refresh')?>"?></button>
      <button type="button" class="btn btn-warning btn-xs" onClick="Load('<?=$module?>?a=edit')"><i class="icon-edit"></i><?="<?=lg('Edit')?>"?></button>
    </div>
  </div>
  <div class="panel-body">
    <dl class="horizontal">
      <?="<?\n"?>
	$s = "SELECT * FROM $table WHERE `$key`='$id'";
	if($r = $dx->get_row($s)){
      foreach($fields as $name=>$field){
		if(!$field['create'] && !$field['edit']) continue;
		?>
      <dt><?="<?=\$field['title']?>"?></dt>
      <dd><?="<?\n"?>
		// Xu ly lai loi UTF-8 cho data
		if($field['type']=='custom') $data = FixJsonUTF8($r->$name);
		else $data = stripslashes($r->$name);
		
		switch($field['type']){
			case 'textarea':
				$data = nl2br($data);
				break;
			
			case 'file':
				$type = $field['fileType'];
				if($type=='') $type = 'image';
				
				if($data!=''){
					if($field['fileType']=='image') $data = ImageDisplay($data,70,false);
					else $data = FileDisplay($data,false);
				}
				
				break;
			
			case 'files':
				$type = $field['fileType'];
				if($type=='') $type = 'image';
				
				if($data!=''){
					$list = explode(',',$data);
					
					$data = '<div class="display">';
					foreach($list as $value){
						if($type=='image') $display = ImageDisplay($value,70,false);
						else $display = FileDisplay($value,false);
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
				$view = array();
				$list = preg_split("/[#,]/",$data,-1,PREG_SPLIT_NO_EMPTY);
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
				
				$data = '';
				if(count($view)>0){
					$data = '<ul class="list"><li>';
					$data .= join('</li><li>',$view);
					$data .= '</li></ul>';
				}
				
				break;
			
			case 'checkbox':
				//values: {"0": "Disable", "1": "Active"}
				$data = $field['values'][$data];
				
				break;
            
			case 'money':
				$data = format_money($data,'VNĐ','0 VNĐ');
				break;
			
			case 'percent':
				$data = format_num($data).'%';
				break;
			
			case 'weight':
				$data = format_num($data,'KG','0 KG');
				break;
			
			case 'number':
				$data = format_num($data);
				break;
			
			case 'html':
		        $data = Html2Text($data);
				break;

			case 'custom':
		        $list = json_decode($data, true);
                if(count($list)>1) {
				  $data = '<table class="table table-bordered" style="width:auto">
					  <thead>
						<tr>
						  <th>#</th>
						  <th>Giá trị</th>
						</tr>
					  </thead>
					  <tbody>';
				
				  $no = 0;
				  foreach($list as $val){
					$data .= '<tr>
						<td>'.++$no.'</td>
					  	<td>'.$val.'</td>
					  </tr>';
				  }
				
				  $data .= '</tbody></table>';
				}
				else {
				  $data = $list[0];
				}
				
				break;
		}
		
		if($data=='') $data = '&nbsp;';
		elseif($name=='URL') $data = "/$data";
		echo $data;
		?></dd>
      <?="<?\n"?>
	  }
	}
	?>\n"?>
    </dl>
  </div>
</div>
<?="<?\n"?>
}

// Edit data from database
elseif($_GET['a']=='edit'){?>
<div class="panel panel-default magbtmz">
  <div class="panel-heading"><?="<?=\$title?>\n"?>
    <div class="control">
      <button type="button" class="btn btn-default btn-xs" onClick="Load('<?=$module?>?a=view')"><i class="icon-list-alt"></i><?="<?=lg('View')?>"?></button>
      <button type="button" class="btn btn-success btn-xs" onClick="Refresh()"><i class="icon-refresh"></i><?="<?=lg('Refresh')?>"?></button>
      <button type="button" class="btn btn-warning btn-xs" onClick="Load('<?=$module?>?a=view')"><i class="icon-reply"></i><?="<?=lg('Go Back')?>"?></button>
    </div>
  </div>
  <div class="panel-body">
    <form id="Form<?="<?=\$guid?>"?>" class="custom" action="<?=$module?>?a=update" method="post">
      <dl class="horizontal">
        <?="<?\n"?>
	  $s = "SELECT * FROM $table WHERE `$key`='$id'";
	  if($r = $dx->get_row($s)){
		foreach($fields as $name=>$field){
		  if(!$field['edit']) continue;
		  ?>
        <dt>
          <label for="Edit<?="<?=\$guid?>"?>-<?="<?=\$name?>"?>"><?="<?=\$field['title']?>"?></label>
        </dt>
        <dd><?="<?\n"?>
		// Xu ly lai loi UTF-8 cho data
		if($field['type']=='custom') $data = FixJsonUTF8($r->$name);
		else $data = stripslashes($r->$name);
		
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
				
				echo '<select id="Edit'.$guid.'-'.$name.'" name="'.$name.'" class="form-control">';
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
				
				echo '<div id="Edit'.$guid.'-'.$name.'" class="checkbox">
				  <div class="check"><button type="button" class="btn btn-info btn-xs" onClick="CheckAll(\''.$name.'\',true)">'.lg('Check all').'</button> <button type="button" class="btn btn-warning btn-xs" onClick="CheckAll(\''.$name.'\',false)">'.lg('Check none').'</button></div>
				  <ul class="list">';
				
				$list = preg_split("/[#,]/",$data,-1,PREG_SPLIT_NO_EMPTY);
				foreach($options as $i=>$v){
					if($v=='') continue;
					echo '<li><label><input type="checkbox" name="'.$name.'[]" value="'.$i.'"';
					if(in_array($i, $list)) echo "checked";
					echo '> '.$v.'</label></li>';
				}
				echo '</ul></div>';
                
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
			
			case 'money':
				echo '<input type="text" id="Edit'.$guid.'-'.$name.'" name="'.$name.'" value="'.format_money($data,'',0).'" class="form-control" style="width:100px"> VNĐ';	
				break;
            
			case 'percent':
				echo '<input type="text" id="Edit'.$guid.'-'.$name.'" name="'.$name.'" value="'.format_num($data,'',0).'" class="form-control" style="width:100px"> %';	
				break;
			
			case 'weight':
				echo '<input type="text" id="Edit'.$guid.'-'.$name.'" name="'.$name.'" value="'.format_num($data,'',0).'" class="form-control" style="width:100px"> KG';	
				break;
			
			case 'number':
				echo '<input type="text" id="Edit'.$guid.'-'.$name.'" name="'.$name.'" value="'.format_num($data).'" class="form-control" style="width:100px">';	
				break;
			
			case 'html':
				echo '<textarea id="Edit'.$guid.'-'.$name.'" name="'.$name.'" class="form-control" rows="3">'.$data.'</textarea>';
				break;
			
			case 'custom':
			  	$list = json_decode($data, true);
			  	?>
          <table id="Edit<?="<?=\$guid?>"?>-<?="<?=\$name?>"?>" class="table table-bordered" style="width:90%">
            <thead>
              <tr>
                <th width="30">#</th>
                <th>Giá trị</th>
              </tr>
            </thead>
            <tbody>
              <?="<?\n"?>
              $max = 10;
              for($no=1;$no<=$max;$no++){
                if(isset($list[$no-1])) $val = $list[$no-1];
                else $val = '';
                ?>
              <tr <?="<?=\$no>2&&\$val==''?'style=\"display:none\"':''?>"?>>
                <td><?="<?=\$no?>"?></td>
                <td><input name="<?="<?=\$name?>"?>[]" value="<?="<?=\$val?>"?>" style="width:100%"></td>
              </tr>
              <?="<? }?>\n"?>
            </tbody>
          </table>
          <a onclick="return AddRow('Edit<?="<?=\$guid?>"?>-<?="<?=\$name?>"?>')" href="#add" style="display:block; margin:5px 0 10px"><i class="icon-plus"></i> Thêm giá trị</a>
          <?="<?\n"?>
				break;
			
			default:
				echo '<input type="text" id="Edit'.$guid.'-'.$name.'" name="'.$name.'" value="'.$data.'" class="form-control">';
		}
		?></dd>
        <?="<?\n"?>
		}
	  }
	  ?>
        <dd>
          <button type="button" class="btn btn-primary btn-sm" onClick="SubmitForm('Form<?="<?=\$guid?>"?>')"><?="<?=lg('Update')?>"?></button>
          <button type="reset" class="btn btn-default btn-sm"><?="<?=lg('Reset')?>"?></button>
        </dd>
      </dl>
    </form>
  </div>
</div>
<script>BuildForm('Form<?="<?=\$guid?>"?>')</script> 
<?="<?\n"?>
}

// Update data to database
elseif($_GET['a']=='update'){?>
<div class="panel panel-default magbtmz">
  <div class="panel-heading"><?="<?=\$title?>\n"?>
    <div class="control">
      <button type="button" class="btn btn-default btn-xs" onClick="Load('<?=$module?>?a=view')"><i class="icon-list-alt"></i><?="<?=lg('View')?>"?></button>
      <button type="button" class="btn btn-warning btn-xs" onClick="Load('<?=$module?>?a=edit')"><i class="icon-reply"></i><?="<?=lg('Go Back')?>"?></button>
    </div>
  </div>
  <div class="panel-body">
    <p><?="<?\n"?>
	$val = array();
	foreach(array_keys($fields) as $f){
		// Lay gia tri duoc truyen vao
		if(isset($_POST[$f])){
			if(in_array($fields[$f]['type'], array('editor','html','custom'))) {
				$val[$f] = safeHTML($_POST[$f]);
			}
			elseif(in_array($fields[$f]['type'], array('money','percent','weight','number'))) {
				$val[$f] = get_money($_POST[$f]);
			}
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
		
		// Xu ly danh sach du lieu
		if($fields[$f]['type']=='list'){
			if(!isset($val[$f])) $val[$f] = '';
			if(is_array($val[$f])) $val[$f] = '#'.join('#',$val[$f]).'#';
		}
		
		if(is_array($val[$f])){
			// Xu ly cho upload nhieu file
			if($fields[$f]['type']=='files') $val[$f] = join(',',$val[$f]);
            
			// Xu ly cho custom data
			elseif($fields[$f]['type']=='custom'){
				$fdata = $val[$f];
				//echo '<pre>'.print_r($fdata,true).'</pre>';
				
				$save = array();
				foreach($fdata as $v){
					if($v!='') $save[] = $v;
				}
				//echo '<pre>'.print_r($save,true).'</pre>';
				
				$data = json_encode($save);
				$val[$f] = addslashes($data);
				//echo 'Data: '.$data,' Safe: '.$val[$f].'<br>';
			}
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
    <button type="button" class="btn btn-default btn-sm" onClick="Load('<?=$module?>?a=view')"><i class="icon-play"></i> <?="<?=lg('Continue')?>"?></button>
  </div>
</div>
  <?="<?\n"?>
  // Auto refresh if success
  if($dx->rows_affected>0){
	echo '<script>Load("<?=$module?>?a=list")</script>';
  }
}
?>
<?
// Save PHP file
$php = ob_get_clean();
file_put_contents(SERVER_PATH.$module,$php);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>Config Module &lt;S&F&gt;</title>
<meta name='description' content=''/>
<meta name='keywords' content=''/>
<meta name="author" content="QsvProgram">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap core CSS -->
<link href="/admin/css/bootstrap.css" rel="stylesheet">
<link href="/admin/css/bootstrap-theme.css" rel="stylesheet">

<!-- Font Awesome 3.2.1 -->
<link href="/admin/css/font-awesome.css" rel="stylesheet">
<!--[if IE 7]>
  <link href="/admin/css/font-awesome-ie7.css" rel="stylesheet">
<![endif]-->

<!-- Custom styles for this template -->
<link href="/admin/css/style.css" rel="stylesheet">
<link href="/admin/css/responsive.css" rel="stylesheet">

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
  <script src="/admin/js/html5shiv.js"></script>
  <script src="/admin/js/respond.js"></script>
<![endif]-->

</head>
<body>
<div id="content" style="margin:20px auto;width:1030px"></div>

<!-- jQuery & Bootstrap --> 
<script src="//code.jquery.com/jquery-1.11.2.js"></script> 
<script>window.jQuery || document.write('<script src="/admin/js/jquery-1.11.2.js"><\/script>')</script>  
<script src="/admin/js/bootstrap.js"></script> 

<!-- jQuery UI -->
<link href="/admin/css/jquery-ui-1.9.2.bootstrap.css" rel="stylesheet">
<!--[if lt IE 9]>
  <link href="/admin/css/jquery.ui.1.9.2.ie.css" rel="stylesheet">
<![endif]--> 
<script src="/admin/js/jquery-ui-1.9.2.js"></script> 

<!-- Extensions -->
<script src="/admin/ext/js-cookie/js.cookie.js"></script> 
<script src="/admin/ext/tinymce/tinymce.js"></script> 
<script src="/admin/ext/plupload/plupload.full.js"></script> 
<link href="/admin/ext/timepicker-addon/jquery-ui-timepicker-addon.css" rel="stylesheet">
<script src="/admin/ext/timepicker-addon/jquery-ui-timepicker-addon.js"></script> 
<link href="/admin/ext/fancybox/jquery.fancybox.css" rel="stylesheet">
<script src="/admin/ext/fancybox/jquery.fancybox.js"></script> 
<link href="/admin/ext/responsive/responsive-tables.css" rel="stylesheet">
<script src="/admin/ext/responsive/responsive-tables.js"></script>

<!-- Main application -->
<script src="/admin/js/application.js"></script>

<script>Load('<?=$module?>')</script>
</body>
</html>