<?
// Them keyword vao autolink
function AddAutoLink($kw, $link) {
	if(empty($kw) || empty($link)) return false;

	global $dx;
	$kw = trim($kw);

	$exist = CheckField($dx,PREFIX_NAME.'seo_autolink'.SUPFIX_NAME, "Keyword='$kw'");
	if($exist) {
		$s = "UPDATE ".PREFIX_NAME.'seo_autolink'.SUPFIX_NAME."
          SET Link='$link',Active='1',NgayCN=NOW()
          WHERE Keyword='$kw'";
	}	
	else {
		$s = "INSERT INTO ".PREFIX_NAME.'seo_autolink'.SUPFIX_NAME."(Link,Keyword,Active,NgayCN)
			    VALUES ('$link','$kw','1',NOW())";
	}
	return $dx->query($s);
}

// Phan cap (level) keyword
function LevelKeyword($keywords,$i=1){
	global $dx;
	$high_level = [];

	// Lan luot lay 1 kw
	foreach($keywords as $keyword){
		$kwdiffs = array_diff($keywords, array($keyword));
		$total = 0;
		// Doi chieu voi cac kw con lai
		foreach($kwdiffs as $kwdiff){
			$check = strpos($kwdiff, $keyword);
			if($check !== false){ // Tim thay ben trong kw con lai
				++$total;
				break;
			}
		}
		// Phan cap kw
		if($total == 0){ // Ko tim thay thi giu nguyen level
			$level = $i;
		} else { // Tim thay thi tang 1 level
			$level = $i + 1;
			$high_level[] = $keyword;
		}
		// Update level tuong ung voi tung kw
		UpdateField($dx, PREFIX_NAME.'seo_autolink'.SUPFIX_NAME,"Keyword='$keyword'","Level='$level'");
	}

	//echo 'High Level'.print_r($high_level,true).'</pre>';
	return $high_level;
}

// Phan ra keyword cho autolink
function ParserAutoLink(){
	global $dx;
	$s = "SELECT Keyword FROM ".PREFIX_NAME.'seo_autolink'.SUPFIX_NAME." WHERE Active='1'";
	$list = $dx->get_col($s,0);

	$l = 1;
	while(count($list)>0) $list = LevelKeyword($list,$l++);
}

//------------------------------------------------------------------------------------
// Extract link from HTML - QsvProgram (24/11/2016)
//------------------------------------------------------------------------------------
function ExtractLink($content, &$list, $debug=false) {
	$html = new simple_html_dom();
	$html->load($content);

	// List link
	$link = $html->find('a');
	foreach($link as $a) {
		$key = count($list);
		if($debug) echo "Link #$key# ".htmlentities($a).'<br>';

		$list[$key] = $a->outertext;
		$a->outertext = "#$key#";
	}

   	$content = $html->save();
   	$html->clear();

	return $content;
}

//------------------------------------------------------------------------------------
// Auto Internal Link - Qsvprogram(24/11/2016)
// Max keyword & cache - Qsvprogram(26/11/2016)
// Normalize unicode charactor - Qsvprogram(30/10/2018)
//------------------------------------------------------------------------------------
function InternalLink($html, $maxkw=3, $debug=false) {
  global $dx;
  
  // Normalize Unicode
  $html = normalizer_normalize($html); 

	// Cache AutoLink
	$cache = CACHE_ENABLE;
	$ckey = 'AutoLink:'.md5($html);
	if($cache && HasCache($ckey)) {
		$html = GetCache($ckey);
		return $html;
	}

	$list = [];
	$html = ExtractLink($html, $list, $debug);
	
	$l = 1;
	$no = 0;
	while(true) {
		if($no>$maxkw) break;
		if($debug) echo "<h4>Level $l</h4>";

		$kws = [];
		$s = "SELECT Link,Keyword FROM ".PREFIX_NAME.'seo_autolink'.SUPFIX_NAME."
			  	WHERE Active='1' AND Level='".$l++."'";
		if($rs = $dx->get_results($s)) {
		  foreach($rs as $r) {
		  	$kw = stripslashes($r->Keyword);
		  	$link = "<a href='".$r->Link."' title='$kw'>$kw</a>";
		  	$kws[$kw] = $link;

		  	// Uppercase first letter
		  	$uckw = ucfirst($kw);
		  	if($uckw!=$kw) {
		  		$link = "<a href='".$r->Link."' title='$kw'>$uckw</a>";
		 	 		$kws[$uckw] = $link;
		  	}
		  	// Lowercase first letter
		  	$lckw = lcfirst($kw);
				if($lckw!=$kw) {
		  		$link = "<a href='".$r->Link."' title='$kw'>$lckw</a>";
		 	 		$kws[$lckw] = $link;
		  	}
		  }
		}
		if(count($kws)==0) break;

		foreach($kws as $kw=>$link) {
			$key = count($list);
			if($debug) echo "Link #$key# ".htmlentities($link).'<br>';

			$list[$key] = $link;
			$count = 0;
			$html = preg_replace('~\b'.$kw.'\b~u', "#$key#", $html, 1, $count);
			$no += $count;
		}
	}

	// Change #key# to link
	foreach($list as $key=>$link) {
		$html = str_replace("#$key#",$link,$html);
	}

	// Them noi dung vao cache
	if($cache) SetCache($ckey,$html,60);

	return $html;
}

?>