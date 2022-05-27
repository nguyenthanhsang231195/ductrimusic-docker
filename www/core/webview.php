<?
//-------------------------------------------------------------------------------
// View Website Module - QsvProgram (04/02/2018)
// Add canonical, paging tag
//-------------------------------------------------------------------------------
class Webview {
	var $wid = 0;
  var $data = [];
	var $mediadir = '';
	var $pager = [];
	
	function __construct($wid=0){
    // Thong tin website
    if($wid==0) $wid = WebsiteID();
		$this->wid = $wid;
    $this->WebInfo();

		// Thu muc media
		$this->mediadir = WEBSITE_DIR.'/';
		
		return $this;
	}
	
	function __destruct(){
		unset($this); 
  }


  function WebInfo() {
    global $dx;

    $qry = "SELECT * FROM ".PREFIX_NAME.'website'.SUPFIX_NAME."
            WHERE webID='".$this->wid."'";
    if($winfo = $dx->get_row($qry)){
      $this->data = [
        'title' 			=> stripslashes($winfo->Tieude),
        'description'	=> stripslashes($winfo->Mota),
        'keywords'		=> stripslashes($winfo->Tukhoa),
        'webimg'			=> $winfo->Logo,
        'siteurl'			=> CurrentURL(),
        'sitename'		=> FROM_NAME,
        'index'				=> stripslashes($winfo->Index),
        'shortcut'		=> ThumbImage($winfo->Icon),
        'logo'				=> ThumbImage($winfo->Logo),
        'nlogo'				=> ThumbImage($winfo->Nlogo),
        'slogan'		  => stripslashes($winfo->Slogan),
        
        'intro'				=> stripslashes($winfo->Intro),
        'contact'			=> stripslashes($winfo->Contact),
        'hotline'			=> $winfo->Hotline,
        'map'					=> stripslashes($winfo->Map),
        'copyright'		=> stripslashes($winfo->Copyright),
        
        'email'				=> $winfo->Email,
        'facebook'		=> stripslashes($winfo->Facebook),
        'google'			=> stripslashes($winfo->Google),
        'instagram'		=> stripslashes($winfo->Instagram),
        'youtube'			=> stripslashes($winfo->Youtube),
        'other'			  => stripslashes($winfo->Other),
        'confb'			  => stripslashes($winfo->ConFB)
      ];
    }
  }

  // Title, meta tag for SEO
  function TagSEO($title, $desc, $keyw, $icon, $img, $link='') {
    if(empty($link)) $link = CurrentURL();

    $seo = '<title>'.$title.'</title>
      <meta name="description" content="'.$desc.'">
      <meta name="keywords" content="'.$keyw.'">
      <meta name="copyright" content="QsvProgram">
      <meta name="robots" content="index, follow">

      <!-- Schema.org markup for Google+ -->
      <meta itemprop="name" content="'.$title.'">
      <meta itemprop="description" content="'.$desc.'">
      <meta itemprop="image" content="'.$img.'"> 

      <!-- Open Graph data -->
      <meta property="og:title" content="'.$title.'">
      <meta property="og:url" content="'.$link.'">
      <meta property="og:image" content="'.$img.'">
      <meta property="og:description" content="'.$desc.'">

      <!-- Twitter Card data -->
      <meta name="twitter:card" content="summary">
      <meta name="twitter:title" content="'.$title.'">
      <meta name="twitter:image" content="'.$img.'"> 
      <meta name="twitter:description" content="'.$desc.'">

      <!-- Favicons -->
      <link rel="apple-touch-icon-precomposed" sizes="144x144" href="'.$icon.'">
      <link rel="shortcut icon" href="'.$icon.'">';

    return $seo;
  }
  
  // Xu ly view page
  function Render($page, $code=200) {
    http_response_code($code);
    
    // PHP with Fast CGI
    if($code==200) header("Status: 200 OK");
    
    // Xac dinh module
    $module = $this->mediadir.$page.'.php';
    if(!file_exists($module)) Page404();

    // Du lieu toan cuc
    global $dx;
    $wview = $this;
    $web = &$this->data;

    // Chay module
    ob_start();
    include($module);
    $html = ob_get_contents();
    ob_end_clean();
    
    // Xu ly duong dan, tracker, ...
    $html = $this->ViewProcess($html);
    
    // Nen code truoc khi view
    if(defined('COMPRESS_CODE') && COMPRESS_CODE) {
      $html = CompressHTML($html);
    }
    echo $html;
  }
		
	// Chinh lai duong dan image, js, css
	function ViewProcess($html){
		// Chen code vao <head> va <body>
		$html = $this->CodeInclude($html);
		
		// Doi duong dan trong src|href|background|action
		$pattern = "#(href|src|background|action)(=\"|=')(?!/|\#|http|https|ftp|data:|\"|'|javascript:|mailto:|skype:|callto:|tel:|ymsgr:)#i";
		$mediadir = str_replace(SERVER_PATH,'',$this->mediadir);
		$html = preg_replace($pattern, '$1$2'.$mediadir, $html);
		
		// Bo dau nhay trong url()
		$quote = "#url\(['\"](.*)['\"]\)#i";
		$html = preg_replace($quote, 'url($1)', $html);
		
		// Doi duong dan trong url()
		$pattern = "#(url\()(?!\)|/|http|https|ftp|data:)#i";
		$html = preg_replace($pattern, '$1$2'.$mediadir, $html);
		
		// Xu ly robots index/onindex
		if(defined('DEVELOPMENT')){
			$robots = [
				'<meta name="robots" content="noindex">',
				'<meta name="robots" content="index, follow">'
			];
			if(DEVELOPMENT) $index = $robots[0];
			else $index = $robots[1];

			foreach($robots as $robot){
				$html = str_replace($robot, $index, $html);
			}
		}

		return $html;
	}

	function CodeInclude($html){
		global $dx;
		
		if(strpos($html,'</body>')!==false) {
			$headinc = '';
			$bodyinc = '';
      
      // Title, meta tag
      $web = $this->data;
      $headinc .= $this->TagSEO(
        $web['title'],
        $web['description'],
        $web['keywords'],
        $web['shortcut'],
        $web['webimg']
      );
      
      // Canonical link
      $cano = Canonical();
      if($cano!='') {
        $headinc .= "<link rel=\"canonical\" href=\"$cano\" />\n";
      }

      // Pagination: prev / next
      if(count($this->pager)>0) {
        $host = Protocol().'://'.$_SERVER['HTTP_HOST'];
        if(count($this->pager['prev'])>0) {
          $prev = $host.$this->pager['prev']['link'];
          $headinc .= "<link rel=\"prev\" href=\"$prev\" />\n";
        }
        if(count($this->pager['next'])>0) {
          $next = $host.$this->pager['next']['link'];
          $headinc .= "<link rel=\"next\" href=\"$next\" />\n";
        }
      }

			// Head include & code tracker
			$s = "SELECT * FROM ".PREFIX_NAME."website".SUPFIX_NAME." WHERE webID='".$this->wid."'";
			if($r = $dx->get_row($s)){
				$headinc .= stripslashes($r->Headinc)."\n";
				$bodyinc .= stripslashes($r->Tracker)."\n";
			}
			
			// Xu ly rieng cho Apple, BlackBerry
			if(IsMobile()) {
				if(IsApple()) $headinc .= '<meta name="format-detection" content="telephone=no">';
				if(IsBlackBerry()) $headinc .= '<meta http-equiv="x-rim-auto-match" content="none">';
				$headinc .= "\n";
			}

			$html = str_replace('</head>',"$headinc</head>",$html);
			$html = str_replace('</body>',"$bodyinc</body>",$html);
		}
		
		return $html;
	}
	
	// Pagination with Bootstrap style
	function Pagination($numPages, $curPage=1, $content='', $URL='', $view=true, $qry=true){
		// Tinh lai trang bat dau va ket thuc cho hop ly
		$startPage = ($curPage-3)<1 ? 1 : $curPage-3;
		$endPage = $startPage + 6;
		if($endPage>$numPages){
			$endPage = $numPages; 
			$startPage = ($endPage-6)<1 ? 1 : $endPage-6;
		}
		
		// Them cac thong so GET vao link
		$query = '';
		if($qry) {
			$query = $_SERVER['QUERY_STRING'];
			$query = ($query==''?'':'?').$query;
		}

		// Xuat du lieu ra website
		if($numPages>1) {
			$rs = '<nav aria-label="Navigation">
        <ul class="pagination justify-content-center">';
			
			// Trang truoc
			if($curPage==1) {
        $rs .= '<li class="page-item disabled">
          <span class="page-link">&laquo;</span>
        </li>';
      }
			else {
				$this->pager['prev'] = [
					'link' => URL_Rewrite($content,$URL,'',$curPage-1).$query,
					'name' => '&laquo;'
				];
        $rs .= '<li class="page-item">
          <a class="page-link" href="'.URL_Rewrite($content,$URL,'',$curPage-1).$query.'" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span>
            <span class="sr-only">Previous</span>
          </a>
        </li>';
			}
			
			// Danh sach cac trang
			$this->pager['list'] = [];
			for($i=$startPage; $i<=$endPage;$i++){
				$this->pager['list'][] = [
					'link' => URL_Rewrite($content,$URL,'',$i).$query,
					'name' => $i,
					'current' => $i==$curPage
				];
				
				if($i==$curPage) {
          $rs .= '<li class="page-item active">
            <span class="page-link">'.$i.' <span class="sr-only">(current)</span></span>
          </li>';
        }
				else {
          $rs .= '<li class="page-item">
            <a class="page-link" href="'.URL_Rewrite($content,$URL,'',$i).$query.'">'.$i.'</a></li>';
        }
			}
			
			// Trang sau
			if($curPage==$numPages) {
        $rs .= '<li class="page-item disabled">
          <span class="page-link">&raquo;</span>
        </li>';
      }
			else {
				$this->pager['next'] = [
					'link' => URL_Rewrite($content,$URL,'',$curPage+1).$query,
					'name' => '&raquo;'
				];
				$rs .= '<li class="page-item">
          <a class="page-link" href="'.URL_Rewrite($content,$URL,'',$curPage+1).$query.'" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
            <span class="sr-only">Next</span>
          </a>
        </li>';
			}

			$rs .= '</ul></nav>';
			if($view) echo $rs;
		}
		
		return $this->pager;
	}
	
	// Next page link
	function NextPage($numPages, $curPage=1, $content='', $URL=''){
		// Them cac thong so GET vao link
		$qry = $_SERVER['QUERY_STRING'];
		$qry = ($qry==''?'':'?').$qry;
		
		// Xac dinh trang tiep theo
    $next = [];
		if($curPage<$numPages){
			$next = [
				'link'	=> URL_Rewrite($content,$URL,'',$curPage+1).$qry,
				'name'	=> '&raquo;'
			];
			echo '<a class="btn btn-primary" href="'.URL_Rewrite($content,$URL,'',$curPage+1).$qry.'">'.lg('View more').' <i class="fas fa-angle-double-down"></i></a>';
    }
    
		return $next;
	}
}

?>