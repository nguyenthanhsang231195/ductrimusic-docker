<?
//==========================================================================
// Class QsvLang - use to detect language, and add string in other language
// Add shortcut to PUT & GET language - QsvProgram (21/10/2013)
//==========================================================================
class QsvLang { 
	var $data = []; 
	var $locate	= ''; 
  var $logs = [];
  var $verbose	= false;
	
	function __construct($lang,$log=true){
		$this->verbose = $log;
		$this->log("init");
    $this->detect($lang);
	}

	function __destruct(){
    $this->log("destroy");
    if($this->verbose) {
      echo 'Class <b>QsvLang</b> logs<br>';
      foreach($this->logs as $log) echo $log.'<br>';
    }

		unset($this); 
	} 
	
	// Put a key and value in data
	function put($key,$val,$lang=''){
    if(empty($lang)) $lang = $this->locate;

		if(isset($this->data[$lang][$key])){
			if($this->data[$lang][$key] != $val){
				$this->log("lp('$key', '$val'); != '".$this->data[$lang][$key]."'");
				//die();
			}
			else{
				$this->log("lp('$key', '$val'); == '$val'");
			}
		}
		else{
			$this->data[$lang][$key] = $val;
		}
	}

	// Get value in data via key
	function get($key){
    $lang = $this->locate;
    $val = $key;
    
		if(isset($this->data[$lang][$key])){
			$val = $this->data[$lang][$key];
		}
    else $this->log("lp('xyz','$key');");
    
		return $val;
  }
  
  // Copy data for language
  function copy($f, $t, $lg='en', $w='', $log=false){
    global $dx;
    if($log) echo '<h2>Copy data for language</h2>';

    $fk = array_shift($f['c']);
    $tk = array_shift($t['c']);
    
    $s = "SELECT * FROM ".$f['n'];
    if($w!='') $s.= " WHERE $w";
    if($log) echo "List: $s<br>";

    $q = mysql_query($s);
    if($log) echo '<ol>';
    while($r = mysql_fetch_assoc($q)){
      if($log) echo '<li>';

      $d = [];
      for($i=0;$i<count($f['c']);$i++){
        $d[$t['c'][$i]] = $r[$f['c'][$i]];
      }

      // Them khoa chinh
      $id = $r[$fk];
      $d = [$tk=>$id, 'lang'=>$lg] + $d;
      if($log) echo 'Data <pre>'.print_r($d,true).'</pre>';
      
      $exist = CheckField($dx,$t['n'],"`$tk`='$id' AND `lang`='$lg'");
      if(!$exist){
        $v = safeHTML(array_values($d));
        $s = "INSERT INTO ".$t['n']."(`".join("`,`",array_keys($d))."`) VALUES('".join("','",$v)."')";
        if($log) echo "Query: $s<br>";
        $dx->query($s);
      }
      elseif($log) echo "Exist: ".$t['n']." ($tk='$id', lang='$lg')";

      if($log) echo '</li>';
    }
    if($log) echo '</ol>';
  }

  // Modify data for language
  function modify($f, $t, $lg='en', $w='', $log=false){
    global $dx;
    if($log) echo '<h2>Modify data for language</h2>';

    $fk = array_shift($f['c']);
    $tk = array_shift($t['c']);
    
    $s = "SELECT * FROM ".$f['n'];
    if($w!='') $s.= " WHERE $w";
    if($log) echo "List: $s<br>";

    $q = mysql_query($s);
    if($log) echo '<ol>';
    while($r = mysql_fetch_assoc($q)){
      if($log) echo '<li>';

      $d = [];
      for($i=0;$i<count($f['c']);$i++){
        $d[$t['c'][$i]] = $r[$f['c'][$i]];
      }
      if($log) echo 'Data <pre>'.print_r($d,true).'</pre>';
      
      $pair = [];
      $val = safeHTML($d);
      foreach($val as $k=>$v) $pair[] = "`$k`='$v'";
      
      if(count($pair)>0){
        $id = $r[$fk];
        $s = "UPDATE ".$t['n']." SET ".join(",",$pair)."
              WHERE `$tk`='$id' AND `lang`='$lg'";
        if($log) echo "Query: $s<br>";
        $dx->query($s);
      }

      if($log) echo '</li>';
    }
    if($log) echo '</ol>';
  }

  function langdef() {
    global $dx;

    $s = "SELECT * FROM ".PREFIX_NAME."langdef".SUPFIX_NAME."
          WHERE lang='".$this->locate."'";
    if($rs = $dx->get_results($s)){
      foreach($rs as $r) {
        $key = stripslashes($r->key);
        $val = stripslashes($r->value);
        $this->data[$this->locate][$key] = $val;
        $this->log("define '$key' --> '$val'");
      }
    }
  }

	function detect($lang){
    // Internationalization 
    if(!isset($_SESSION['i18n'])) $_SESSION['i18n'] = [];

    // Neu co chuyen vao language thi thay doi
		if($lang!="") {
      $this->locate = $lang;
      $_SESSION['i18n']['locate'] = $this->locate;
      
      $this->log("apply '".$this->locate."'");
    }
    // Neu ko co SESSION thi lay mac dinh
		elseif(empty($_SESSION['i18n']['locate'])) {
      $this->locate = DEFAULT_LANGUAGE;
      $_SESSION['i18n']['locate'] = $this->locate;

      $this->log("default ".$this->locate);
    }
    // Xac dinh theo SESSION
		else {
      $this->locate = $_SESSION['i18n']['locate'];
      $this->log("detect '".$this->locate."'");
    }
    
    if(!isset($this->data[$this->locate])){
      $this->data[$this->locate] = [];
    }
	}
	
	function log($str){
    $this->logs[] = $str;
	}

	function show(){
    $lang = $this->locate;
    echo "Language '$lang'";
    echo '<pre>'.print_r($this->data[$lang],true).'</pre>';
	}
}

// Cau hinh chung
if(!isset($QSV)) $QSV = [];

// Init language
function language($log=false) {
  global $QSV;

  $lg = isset($_GET['lang'])?$_GET['lang']:'';
  $QSV['l10n'] = $L = new QsvLang($lg,$log);
  $lang = $L->locate;

  // Internationalization
	if(!isset($_SESSION['i18n'])) $_SESSION['i18n'] = [];
  if(!isset($_SESSION['i18n'][$lang])) {
    if($lang=='vn') {
      $file = SERVER_PATH.'/core/vietnamese.php';
      require_once($file);
      $L->log("file '".basename($file)."'");
    }

    // Load langdef
    $L->langdef();

    // Save language define
    $L->log("cacheit '$lang'");
    $_SESSION['i18n'][$lang] = $L->data[$lang];
  }
  else {
    $L->log("cached '$lang'");
    $L->data[$lang] = $_SESSION['i18n'][$lang];
  }
  //$L->show();

  return $L;
}

// Get language code
function lc(){
  global $QSV;
	return $QSV['l10n']->locate;
}

// SQL language where
function lw($prefix='') {
  if(MULTI_LANGUAGE) {
    return $prefix."(lang='".lc()."')";
  }
  return '';
}

// Set language
function ls($lang){
  // Internationalization 
  if(!isset($_SESSION['i18n'])) $_SESSION['i18n'] = [];
  $_SESSION['i18n']['locate'] = $lang;
}

// Put a key and value in data
function lp($k,$v,$l=''){
	global $QSV;
	$QSV['l10n']->put($k,$v,$l);
}

// Get value in data via key
function lg($k){
	global $QSV;
	return $QSV['l10n']->get($k);
}

// Copy data
function ldc($f, $t, $lg='en', $w='', $log=false){
  global $QSV;
	return $QSV['l10n']->copy($f, $t, $lg, $w, $log);
}

// Modify data
function ldm($f, $t, $lg='en', $w='', $log=false){
	global $QSV;
	return $QSV['l10n']->modify($f, $t, $lg, $w, $log);
}
?>