<?
// Xu ly duong dan: Ap dung | Chuyen huong
function URL_Master($debug=false) {
	if($debug) echo "<b>URL Master</b><br>";
	global $dx;
	
	// Ap dung duong dan
	$uri = $_SERVER['REQUEST_URI'];
  header('Rewrite-URL: '.$uri);

  $uri = preg_replace('/\?.*/','',$uri);
	$uri = preg_replace('/\-pg[0-9]+/','',$uri);
	if(substr($uri,-1)=='/') $uri = substr($uri,0,-1);
	if($uri=='') $uri = '/';
	if($debug) echo "URI='$uri'<br>";

	// Thong tin Rewrite
	$_SESSION['urlinfo'] = [];

	$s = "SELECT * FROM ".PREFIX_NAME.'seo_rewrite'.SUPFIX_NAME."
		  	WHERE Link='$uri' AND Active='1'";
	if($debug) echo "Rewrite: $s<br>";
	if($r = $dx->get_row($s)){
		$param = $r->Param;
		if($debug) echo "Param='$param'<br>";
		
		// Cap nhat Rewrite
		$_SESSION['urlinfo'] = [
			'rewID' => $r->rewID,
			'link' => $uri,
			'param' => $param,
			'title' => stripslashes($r->TagTitle),
			'desc' => stripslashes($r->TagDesc),
			'index' => stripslashes($r->Index)
		];

		// Create $_GET data		
		$x = array_pad(explode('?',$param), 2, '');
		parse_str($x[1],$get);
		foreach($get as $k=>$v) {
		  if(!isset($_GET[$k])) $_GET[$k] = $v;
		}
		if($debug) echo '$_GET<pre>'.print_r($get,true).'</pre>';

		UpdateField($dx,PREFIX_NAME.'seo_rewrite'.SUPFIX_NAME,"rewID='".$r->rewID."'","LastTime=NOW()");
		return true;
	}
	
	// Thay doi duong dan
	$param = $uri.NormalQuery($_SERVER['QUERY_STRING'],$debug);	
	$link = URL_Change($param, $debug);
	if($link!='') {
		if($debug) echo "Rewrite: $link";
		else {
			Redirect($link);
			exit;
		}
	}
	
	return false;
}

// Thay doi duong dan
function URL_Change($param, $debug=true) {
	if($debug) echo "<b>URL Change</b><br>";
	if($param=='') return '';
	global $dx;

	$s = "SELECT * FROM ".PREFIX_NAME.'seo_rewrite'.SUPFIX_NAME."
			  WHERE Param='$param' AND Active='1'";
	if($debug) echo "SQL: $s<br>";
	if($rs = $dx->get_results($s)){
	  foreach($rs as $r){
      $link = $r->Link;
			UpdateField($dx,PREFIX_NAME.'seo_rewrite'.SUPFIX_NAME,"rewID='".$r->rewID."'","LastTime=NOW()");
      
      // Xu ly phan trang
      if(!empty($_GET['page'])) {
        $page = intval($_GET['page']);
        if($debug) echo "Page number: $page<br>";
        if($page>1) $link .= '-pg'.$page;
      }

			return $link;
	  }
	}
	
	return '';
}


// Chuyen duong dan thuong sang dang rewrite
function URL_Rewrite($content,$name1='',$name2='',$page='',$query='',$html=false){
	// Duong dan chinh
	$link = '/'.$content;
	$link .= $name1=='' ? '' : '/'.$name1;
	$link .= $name2=='' ? '' : '/'.$name2;
	$link .= $page==''||$page=='1' ? '' : '-pg'.$page;
	$link .= $html ? '.html' : '';
  
  // Link theo ngon ngu
  if(MULTI_LANGUAGE) {
    $lang = lc();

    // Ko doi link mac dinh?
    if(DEFAULT_NOLINK) {
      if($lang!=DEFAULT_LANGUAGE) $link = '/'.$lang.$link;
    }
    else $link = '/'.$lang.$link;
  }

	return $link.$query;
}

// Phan tich duong dan ve dang dung
function URL_Decode($params){
  // Xu ly da ngon ngu
  if(MULTI_LANGUAGE) {
    global $dx;

    // Doi ngon ngu theo URL
    $lang = $params[0];

    if(CheckField($dx,PREFIX_NAME.'language'.SUPFIX_NAME,"lang='$lang'")){
      array_shift($params);
      ls($lang);

      // Chuyen ve link goc
      if(DEFAULT_NOLINK && $lang==DEFAULT_LANGUAGE){
        $uri = $_SERVER['REQUEST_URI'];
        $nolg = substr(trim($uri,'/'),2);
        if($nolg=='') $nolg = '/';

        header('Location: '.$nolg);
        exit;
      }
    }
    // Doi URL theo ngon ngu
    else {
      $Lg = new QsvLang('',false);
      $dflg = $Lg->locate;

      // Ko doi link mac dinh?
      if(DEFAULT_NOLINK) {
        if($dflg!=DEFAULT_LANGUAGE && $dflg!=$lang) {
          $uri = $_SERVER['REQUEST_URI'];
          header('Location: /'.$dflg.$uri);
          exit;
        }
      }
      elseif($dflg!=$lang) {
        $uri = $_SERVER['REQUEST_URI'];
        header('Location: /'.$dflg.$uri);
        exit;
      }
    }
  }

  $content='';
  $count = count($params);
  
	if($count==1) {
		$content = $params[0];
		if(strpos($content,'-pg')!==false){
			$a = explode('-pg',$content);
			$content = $a[0];
			$page = $a[1];
		}
	}
	elseif($count==2) {
		$content = $params[0];
		$name = $params[1];
		if(strpos($name,'-pg')!==false){
			$a = explode('-pg',$name);
			$name = $a[0];
			$page = $a[1];
		}
	}
	elseif($count>=3) {
		$content = $params[0];
		$name1 = $params[1];
		$name2 = $params[2];
		if(strpos($name2,'-pg')!==false){
			$a = explode('-pg',$name2);
			$name2 = $a[0];
			$page = $a[1];
		}
	}
	
	$_GET['content'] = $content;
	if(!empty($page)) $_GET['page'] = $page;
	if(!empty($name)) $_GET['name'] = $name;
	if(!empty($name1)) {
		$_GET['name1'] = $name1;
		$_GET['name2'] = $name2;
	}
}


//------------------------------------------------------------------------------------
// Add url rewrite rule - QsvProgram (02/10/2015)
//------------------------------------------------------------------------------------
function AddRewrite($link, $param, $priority, $debug=false){
	if(empty($link) || empty($param)) return false;
	if($link==$param) return false;

	// Normal Link & Param
	if(substr($link,-1)=='/') $link = substr($link,0,-1);
	if(substr($param,-1)=='/') $param = substr($param,0,-1);
	
	// Build sorted param
	$x = array_pad(explode('?',$param), 2, '');
	$param = $x[0].NormalQuery($x[1],$debug);

	global $dx;
  $rewID = GetField($dx,PREFIX_NAME.'seo_rewrite'.SUPFIX_NAME,"Link='$link' OR Param='$param'",'rewID');
  
	if(empty($rewID)){
		$rewID = FirstID($dx,'rewID',PREFIX_NAME.'seo_rewrite'.SUPFIX_NAME);
		$s = "INSERT INTO ".PREFIX_NAME.'seo_rewrite'.SUPFIX_NAME."(`rewID`, `Link`, `Param`,
						`Priority`, `Active`, `NgayCN`)
					VALUES('".$rewID."', '".$link."', '".$param."', '".$priority."', '1', NOW())";
	}
	else{
		$s = "UPDATE ".PREFIX_NAME.'seo_rewrite'.SUPFIX_NAME."
				  SET Link='$link', Param='$param', Priority='$priority', NgayCN=NOW()
				  WHERE rewID='$rewID'";
	}
	if($debug) echo "Rewrite: $s<br>";
	if($dx->query($s)) return $rewID;

	return false;
}

//------------------------------------------------------------------------------------
// Build Sorted Query String - QsvProgram (24/12/2016)
//------------------------------------------------------------------------------------
function NormalQuery($query, $debug=false) {
	if(empty($query)) return '';
	if($debug) echo 'Origin: '.$query.'<br>';

  if(is_array($query)) $qry = $query;
  else parse_str($query,$qry);
	if($debug) echo 'Query<pre>'.print_r($qry,true).'</pre>';

	foreach($qry as $k=>$v) {
		if(is_array($v)) {
			foreach($v as $sk=>$sv) if(empty($sv)) unset($qry[$k][$sk]);
		}
		if($v==='') unset($qry[$k]);
	}
	if($debug) echo 'Clean<pre>'.print_r($qry,true).'</pre>';

	ksort($qry);
	if($debug) echo 'Sorted<pre>'.print_r($qry,true).'</pre>';

	$query = http_build_query($qry, null, '&', PHP_QUERY_RFC3986);
	if($query!='') $query = '?'.$query;

	return $query;
}
?>