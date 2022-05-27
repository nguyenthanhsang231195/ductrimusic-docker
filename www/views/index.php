<!DOCTYPE html>
<html itemscope itemtype="http://schema.org/Article">
<head>
  <? include_once('_header.php')?>
</head>
<body>
  <?
  include_once('_menu.php');
  

  $wh = lw();
  // $wh .= ($wh==''?'':'AND')."(Hot='1')";
  $wh .= ($wh==''?'':'AND')."(Active='1')";
  $wh = ($wh==''?'':'WHERE').$wh;

  $ps = "SELECT * FROM ".PREFIX_NAME."slideshow".SUPFIX_NAME." $wh";

  $sildeshow = [];
  if($ps = $dx->get_results($ps)){
    foreach($ps as $pr){
      $sildeshow[] = [
        'name'		=> stripslashes($pr->Ten),
        'image'		=> ThumbImage($pr->Anh,1200),
        'thumb'		=> ThumbImage($pr->Anh,360),
        'rias'		=> ThumbImage($pr->Anh,'{width}'),
        'link'		=> URL_Rewrite($pr->URL),
        'content'	=> stripslashes($pr->Content),
      ];
    }
  }
  if(count($sildeshow)>0) {
  ?>
  <section class="banner-sildeshow">
    <? foreach($sildeshow as $a){?>
      
        <div class="genesys-banner-sildeshow" >
          <a href="<?=$a['link']?>">
            <img src="<?=$a['thumb']?>" data-src="<?=$a['rias']?>" data-widths="[480,640,800,1280,1600,2560]" data-optimumx="1.6" data-sizes="auto" class="lazyload">
          </a>
        </div>        
    <?}?>
  </section>
  <?
  }
  

  $wh = lw();
  // $wh .= ($wh==''?'':'AND')."(Hot='1')";
  $wh .= ($wh==''?'':'AND')."(pgID='1')";
  $wh = ($wh==''?'':'WHERE').$wh;

  $pa = "SELECT * FROM ".PREFIX_NAME."pages".SUPFIX_NAME." $wh";
  // echo "$pa<br/>";
  $page = [];
  if($pas = $dx->get_results($pa)){
    foreach($pas as $pr){
      $page[] = [
        'name'		=> stripslashes($pr->Ten),
        'content' => stripslashes($pr->Noidung),
      ];
    }
  }

  if(count($page)>0) {?>
  <section class="genesys-group-intro">
    <div class="container" >
      <? foreach($page as $a){?>      
        <?=$a['content']?>
      <?}?>
    </div>
  </section>
  <?
  }
  
  $wh = '';//lw();
  $wh .= ($wh==''?'':'AND')."(Highlights='1')";
  $wh .= ($wh==''?'':'AND')."(Active='1')";
  $wh = ($wh==''?'':'WHERE').$wh;

  $ps = "SELECT * FROM ".PREFIX_NAME."product".SUPFIX_NAME." $wh
         ORDER BY SKU ASC LIMIT 4";
  $product = [];
  if($ps = $dx->get_results($ps)){
    foreach($ps as $pr){
      $info = [
        'name'		=> stripslashes($pr->Ten),
        'image'		=> ThumbImage($pr->Anh),
        'thumb'		=> ThumbImage($pr->Anh,150),
        'rias'		=> ThumbImage($pr->Anh,'{width}'),
        'price'   => format_money($pr->Giaban,'đ',lg('Contact')),
        'promo'   => format_money($pr->GiaKM,'đ',0),
        'brief'		=> CutString($pr->Tomtat,150),
        'link'		=> URL_Rewrite('piano',$pr->URL)
      ];

      // Da ngon ngu
      if(MULTI_LANGUAGE && lc()!=DEFAULT_LANGUAGE){
        $ss = "SELECT * FROM ".PREFIX_NAME."product_lg".SUPFIX_NAME."
               WHERE lgID='".$pr->proID."' ".lw('AND');
        if($rr = $dx->get_row($ss)){
          $info['name'] = stripslashes($rr->Ten);
          $info['brief'] = CutString($rr->Tomtat,150);
        }
      }

      $product[] = $info;
    }
  }
  if(count($product)>0) {
  ?>
  <section class="genesys-product-hot">
    <div class="container">
      <h2><?=lg('Product Hot')?></h2>
      <div class="row">
        <? foreach($product as $a){?>
          <div class="col-md-3 col-6">
            <a href="<?=$a['link']?>">
              <div class="box-content-product">
                <div class="box-img-product">
                  <img src="<?=$a['image']?>" alt="<?=$a['name']?>">
                  <!-- <img src="<?=$a['thumb']?>" data-src="<?=$a['rias']?>" data-widths="[250,500,800,1000,1200]" data-optimumx="1.6" data-sizes="auto" class="lazyload" alt="<?=$a['name']?>"> -->
                </div>
                <h3 style="font-weight: 500;"><?=$a['name']?></h3>
                <p><? if($a['promo']!=0) {?>
                  <?=$a['promo']?> <small style="text-decoration:line-through; display: block;"><?=$a['price']?></small>
                <? }else echo $a['price']?></p>
                <!-- <span href="#!"><?=lg('SeeMore')?></span> -->
              </div>
            </a>
          </div>
        <?}?>
      </div>
    </div>
  </section>
  <?
  }
  
  // Danh muc quan trong
  $s = "SELECT * FROM ".PREFIX_NAME."product_catalog".SUPFIX_NAME."
        WHERE chID=0 AND Loai='1' AND catID='1' ORDER BY Thutu LIMIT 4";
  $list = [];
  if($rs = $dx->get_results($s)){
    foreach($rs as $r){
      $info = [
        'name'		=> stripslashes($r->Ten),
        'image'		=> ThumbImage($r->Anh,1200),
        'thumb'		=> ThumbImage($r->Anh,360),
        'rias'		=> ThumbImage($r->Anh,'{width}'),
        'summary'	=> CutString($r->Tomtat,320),
        'brief'		=> CutString($r->Mota,320),
        'vt'	  	=> $r->Vitri,
        'link'		=> URL_Rewrite($r->URL),
        'coltet' => stripslashes($r->colorCat),
      ];

      // Da ngon ngu
      if(MULTI_LANGUAGE && lc()!=DEFAULT_LANGUAGE){
        $ss = "SELECT * FROM ".PREFIX_NAME."product_catalog_lg".SUPFIX_NAME."
               WHERE lgID='".$r->catID."' ".lw('AND');
        if($rr = $dx->get_row($ss)){
          $info['name'] = stripslashes($rr->Ten);
          $info['summary'] = CutString($rr->Tomtat,320);
          $info['brief'] = CutString($rr->Mota,320);
        }
      }

      $list[] = $info;
    }
  }

  foreach($list as $catalog){?>
    <section class="genesys-engagement-dt cover-size-new">
      <div class="genesys-catalog-index" >
        <a href="<?=$catalog['link']?>">
          <img src="<?=$catalog['thumb']?>" data-src="<?=$catalog['rias']?>" data-widths="[480,640,800,1280,1600,2560]" data-optimumx="1.6" data-sizes="auto" class="lazyload">
        </a>
        <div class="content-box marg-top <?=$catalog['coltet']?>">
          <div class="container">
            <div class="row">
              <div class="offset-lg-1 <?=$catalog['vt']?>-lg-8 col-lg-4 col-12 product-pagehome"">
                <div class="genesys-box">
                    <h2><?=$catalog['name']?></h2>
                    <p ><?=$catalog['brief']?></p>
                    <p><a href="<?=$catalog['link']?>"><?=lg('FindOut')?></a></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?
  }
  $wh = '';//lw();
  $wh .= ($wh==''?'':'AND')."(Hot='1')";
  $wh .= ($wh==''?'':'AND')."(Danhmuc LIKE '%#1#%')";
  $wh .= ($wh==''?'':'AND')."(Active='1')";
  $wh = ($wh==''?'':'WHERE').$wh;

  $ps = "SELECT * FROM ".PREFIX_NAME."product".SUPFIX_NAME." $wh
         ORDER BY SKU ASC LIMIT 4";
  $product = [];
  if($ps = $dx->get_results($ps)){
    foreach($ps as $pr){
      $info = [
        'name'		=> stripslashes($pr->Ten),
        'image'		=> ThumbImage($pr->Anh),
        'thumb'		=> ThumbImage($pr->Anh,150),
        'rias'		=> ThumbImage($pr->Anh,'{width}'),
        'price'   => format_money($pr->Giaban,'đ',lg('Contact')),
        'promo'   => format_money($pr->GiaKM,'đ',0),
        'brief'		=> CutString($pr->Tomtat,150),
        'link'		=> URL_Rewrite('piano',$pr->URL)
      ];

      // Da ngon ngu
      if(MULTI_LANGUAGE && lc()!=DEFAULT_LANGUAGE){
        $ss = "SELECT * FROM ".PREFIX_NAME."product_lg".SUPFIX_NAME."
               WHERE lgID='".$pr->proID."' ".lw('AND');
        if($rr = $dx->get_row($ss)){
          $info['name'] = stripslashes($rr->Ten);
          $info['brief'] = CutString($rr->Tomtat,150);
        }
      }

      $product[] = $info;
    }
  }
  if(count($product)>0) {
  ?>
  <section class="genesys-product-hot">
    <div class="container">
      <div class="row">
        <? foreach($product as $a){?>
          <div class="col-md-3 col-6">
            <a href="<?=$a['link']?>">
              <div class="box-content-product">
                <div class="box-img-product">
                  <img src="<?=$a['image']?>" alt="<?=$a['name']?>">
                  <!-- <img src="<?=$a['thumb']?>" data-src="<?=$a['rias']?>" data-widths="[250,500,800,1000,1200]" data-optimumx="1.6" data-sizes="auto" class="lazyload" alt="<?=$a['name']?>"> -->
                </div>
                <h3 style="font-weight: 500;"><?=$a['name']?></h3>
                <p><? if($a['promo']!=0) {?>
                  <?=$a['promo']?> <small style="text-decoration:line-through; display: block;"><?=$a['price']?></small>
                <? }else echo $a['price']?></p>
                <!-- <span href="#!"><?=lg('SeeMore')?></span> -->
              </div>
            </a>
          </div>
        <?}?>
      </div>
    </div>
  </section>
  <?
  }
  
  // Danh muc quan trong
  $s = "SELECT * FROM ".PREFIX_NAME."product_catalog".SUPFIX_NAME."
        WHERE chID=0 AND Loai='1' AND catID='2' ORDER BY Thutu LIMIT 4";
  $list = [];
  if($rs = $dx->get_results($s)){
    foreach($rs as $r){
      $info = [
        'name'		=> stripslashes($r->Ten),
        'image'		=> ThumbImage($r->Anh,1200),
        'thumb'		=> ThumbImage($r->Anh,360),
        'rias'		=> ThumbImage($r->Anh,'{width}'),
        'summary'	=> CutString($r->Tomtat,320),
        'brief'		=> CutString($r->Mota,320),
        'vt'	  	=> $r->Vitri,
        'link'		=> URL_Rewrite($r->URL),
        'coltet' => stripslashes($r->colorCat),
      ];

      // Da ngon ngu
      if(MULTI_LANGUAGE && lc()!=DEFAULT_LANGUAGE){
        $ss = "SELECT * FROM ".PREFIX_NAME."product_catalog_lg".SUPFIX_NAME."
               WHERE lgID='".$r->catID."' ".lw('AND');
        if($rr = $dx->get_row($ss)){
          $info['name'] = stripslashes($rr->Ten);
          $info['summary'] = CutString($rr->Tomtat,320);
          $info['brief'] = CutString($rr->Mota,320);
        }
      }

      $list[] = $info;
    }
  }

  foreach($list as $catalog){?>
    <section class="genesys-engagement-dt cover-size-new">
      <div class="genesys-catalog-index" >
        <a href="<?=$catalog['link']?>">
          <img src="<?=$catalog['thumb']?>" data-src="<?=$catalog['rias']?>" data-widths="[480,640,800,1280,1600,2560]" data-optimumx="1.6" data-sizes="auto" class="lazyload">
        </a>
        <div class="content-box marg-top <?=$catalog['coltet']?>">
          <div class="container">
            <div class="row">
              <div class="offset-lg-1 <?=$catalog['vt']?>-lg-8 col-lg-4 col-12 product-pagehome"">
                <div class="genesys-box">
                    <h2><?=$catalog['name']?></h2>
                    <p ><?=$catalog['brief']?></p>
                    <p><a href="<?=$catalog['link']?>"><?=lg('FindOut')?></a></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?
  }
  $wh = '';//lw();
  $wh .= ($wh==''?'':'AND')."(Hot='1')";
  $wh .= ($wh==''?'':'AND')."(Danhmuc LIKE '%#2#%')";
  $wh .= ($wh==''?'':'AND')."(Active='1')";
  $wh = ($wh==''?'':'WHERE').$wh;

  $ps = "SELECT * FROM ".PREFIX_NAME."product".SUPFIX_NAME." $wh
         ORDER BY SKU ASC LIMIT 4";
  $product = [];
  if($ps = $dx->get_results($ps)){
    foreach($ps as $pr){
      $info = [
        'name'		=> stripslashes($pr->Ten),
        'image'		=> ThumbImage($pr->Anh),
        'thumb'		=> ThumbImage($pr->Anh,150),
        'rias'		=> ThumbImage($pr->Anh,'{width}'),
        'price'   => format_money($pr->Giaban,'đ',lg('Contact')),
        'promo'   => format_money($pr->GiaKM,'đ',0),
        'brief'		=> CutString($pr->Tomtat,150),
        'link'		=> URL_Rewrite('piano',$pr->URL)
      ];

      // Da ngon ngu
      if(MULTI_LANGUAGE && lc()!=DEFAULT_LANGUAGE){
        $ss = "SELECT * FROM ".PREFIX_NAME."product_lg".SUPFIX_NAME."
               WHERE lgID='".$pr->proID."' ".lw('AND');
        if($rr = $dx->get_row($ss)){
          $info['name'] = stripslashes($rr->Ten);
          $info['brief'] = CutString($rr->Tomtat,150);
        }
      }

      $product[] = $info;
    }
  }
  if(count($product)>0) {
  ?>
  <section class="genesys-product-hot">
    <div class="container">
      <div class="row">
        <? foreach($product as $a){?>
          <div class="col-md-3 col-6">
            <a href="<?=$a['link']?>">
              <div class="box-content-product">
                <div class="box-img-product">
                  <img src="<?=$a['image']?>" alt="<?=$a['name']?>">
                  <!-- <img src="<?=$a['thumb']?>" data-src="<?=$a['rias']?>" data-widths="[250,500,800,1000,1200]" data-optimumx="1.6" data-sizes="auto" class="lazyload" alt="<?=$a['name']?>"> -->
                </div>
                <h3 style="font-weight: 500;"><?=$a['name']?></h3>
                <p><? if($a['promo']!=0) {?>
                  <?=$a['promo']?> <small style="text-decoration:line-through;display: block;"><?=$a['price']?></small>
                <? }else echo $a['price']?></p>
                <!-- <span href="#!"><?=lg('SeeMore')?></span> -->
              </div>
            </a>
          </div>
        <?}?>
      </div>
    </div>
  </section>
  <?
  }
  
  // Danh muc quan trong
  $s = "SELECT * FROM ".PREFIX_NAME."product_catalog".SUPFIX_NAME."
        WHERE chID=0 AND Loai='1' AND catID='3' ORDER BY Thutu LIMIT 4";
  $list = [];
  if($rs = $dx->get_results($s)){
    foreach($rs as $r){
      $info = [
        'name'		=> stripslashes($r->Ten),
        'image'		=> ThumbImage($r->Anh,1200),
        'thumb'		=> ThumbImage($r->Anh,360),
        'rias'		=> ThumbImage($r->Anh,'{width}'),
        'summary'	=> CutString($r->Tomtat,320),
        'brief'		=> CutString($r->Mota,320),
        'vt'	  	=> $r->Vitri,
        'link'		=> URL_Rewrite($r->URL),
        'coltet' => stripslashes($r->colorCat),
      ];

      // Da ngon ngu
      if(MULTI_LANGUAGE && lc()!=DEFAULT_LANGUAGE){
        $ss = "SELECT * FROM ".PREFIX_NAME."product_catalog_lg".SUPFIX_NAME."
               WHERE lgID='".$r->catID."' ".lw('AND');
        if($rr = $dx->get_row($ss)){
          $info['name'] = stripslashes($rr->Ten);
          $info['summary'] = CutString($rr->Tomtat,320);
          $info['brief'] = CutString($rr->Mota,320);
        }
      }

      $list[] = $info;
    }
  }

  foreach($list as $catalog){?>
    <section class="genesys-engagement-dt cover-size-new">
      <div class="genesys-catalog-index" >
        <a href="<?=$catalog['link']?>">
          <img src="<?=$catalog['thumb']?>" data-src="<?=$catalog['rias']?>" data-widths="[480,640,800,1280,1600,2560]" data-optimumx="1.6" data-sizes="auto" class="lazyload">
        </a>
        <div class="content-box marg-top <?=$catalog['coltet']?>">
          <div class="container">
            <div class="row">
              <div class="offset-lg-1 <?=$catalog['vt']?>-lg-8 col-lg-4 col-12 product-pagehome"">
                <div class="genesys-box">
                    <h2><?=$catalog['name']?></h2>
                    <p ><?=$catalog['brief']?></p>
                    <p><a href="<?=$catalog['link']?>"><?=lg('FindOut')?></a></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?
  }
  $wh = '';//lw();
  $wh .= ($wh==''?'':'AND')."(Hot='1')";
  $wh .= ($wh==''?'':'AND')."(Danhmuc LIKE '%#3#%')";
  $wh .= ($wh==''?'':'AND')."(Active='1')";
  $wh = ($wh==''?'':'WHERE').$wh;

  $ps = "SELECT * FROM ".PREFIX_NAME."product".SUPFIX_NAME." $wh
         ORDER BY SKU ASC LIMIT 4";
  $product = [];
  if($ps = $dx->get_results($ps)){
    foreach($ps as $pr){
      $info = [
        'name'		=> stripslashes($pr->Ten),
        'image'		=> ThumbImage($pr->Anh),
        'thumb'		=> ThumbImage($pr->Anh,150),
        'rias'		=> ThumbImage($pr->Anh,'{width}'),
        'price'   => format_money($pr->Giaban,'đ',lg('Contact')),
        'promo'   => format_money($pr->GiaKM,'đ',0),
        'brief'		=> CutString($pr->Tomtat,150),
        'link'		=> URL_Rewrite('piano',$pr->URL)
      ];

      // Da ngon ngu
      if(MULTI_LANGUAGE && lc()!=DEFAULT_LANGUAGE){
        $ss = "SELECT * FROM ".PREFIX_NAME."product_lg".SUPFIX_NAME."
               WHERE lgID='".$pr->proID."' ".lw('AND');
        if($rr = $dx->get_row($ss)){
          $info['name'] = stripslashes($rr->Ten);
          $info['brief'] = CutString($rr->Tomtat,150);
        }
      }

      $product[] = $info;
    }
  }
  if(count($product)>0) {
  ?>
  <section class="genesys-product-hot">
    <div class="container">
      <div class="row">
        <? foreach($product as $a){?>
          <div class="col-md-3 col-6">
            <a href="<?=$a['link']?>">
              <div class="box-content-product">
                <div class="box-img-product">
                  <img src="<?=$a['image']?>" alt="<?=$a['name']?>">
                  <!-- <img src="<?=$a['thumb']?>" data-src="<?=$a['rias']?>" data-widths="[250,500,800,1000,1200]" data-optimumx="1.6" data-sizes="auto" class="lazyload" alt="<?=$a['name']?>"> -->
                </div>
                <h3 style="font-weight: 500;"><?=$a['name']?></h3>
                <p><? if($a['promo']!=0) {?>
                  <?=$a['promo']?> <small style="text-decoration:line-through;display: block;"><?=$a['price']?></small>
                <? }else echo $a['price']?></p>
                <!-- <span href="#!"><?=lg('SeeMore')?></span> -->
              </div>
            </a>
          </div>
        <?}?>
      </div>
    </div>
  </section>
  <?
  }
  
  // Khám phá
  $wh = lw();
  $wh .= ($wh==''?'':'AND')."(Active='1')";
  $wh = ($wh==''?'':'WHERE').$wh;
  
  $s = "SELECT * FROM ".PREFIX_NAME."shop".SUPFIX_NAME." $wh
        ORDER BY Thutu DESC ";
        // echo "$s<br/>";
  $list = [];
  if($rs = $dx->get_results($s)){
    foreach($rs as $r){
      $list[] = [
        'name'		=> stripslashes($r->Ten),
        'image'		=> ThumbImage($r->Anh, 800),
        'thumb'		=> ThumbImage($r->Anh, 400),
        'rias'		=> ThumbImage($r->Anh, '{width}'),
        'link'		=> URL_Rewrite($r->Link)
      ];
    }
  }
  
  if(count($list)>0) {?>
  <section class="discover discover-home">
    <h2><?=lg('genesysShop')?></h2>
    <p class="seemore"></p>
    <section class="center slider">
      <? foreach($list as $a){?>
        <a href="<?=$a['link']?>"><img src="<?=$a['image']?>" data-src="<?=$a['rias']?>" data-widths="[400,800,1200,1600,2400]" data-optimumx="1.6" data-sizes="auto" class="lazyload"></a>
      <?}?>
    </section> 
  </section>
  <? }
  // Tin tuc
  $wh = lw();
	$wh .= ($wh==''?'':'AND')."(Active='1')";
	$wh = ($wh==''?'':'WHERE').$wh;
	
	// Danh sach bai viet
	$s = "SELECT * FROM ".PREFIX_NAME."news".SUPFIX_NAME." $wh
		    ORDER BY NgayCN DESC LIMIT 4";
	$list = [];
	if($rs = $dx->get_results($s)){
	  foreach($rs as $r){
      $list[] = [
        'name'		=> stripslashes($r->Ten),
        'image'		=> ThumbImage($r->Anh,500),
        'thumb'		=> ThumbImage($r->Anh,150),
        'rias'		=> ThumbImage($r->Anh,'{width}'),
        'brief'		=> Html2Text($r->Tomtat,150),
        'link'		=> URL_Rewrite('kinh-nghiem',$r->URL),
        'date'		=> format_date($r->NgayCN)
      ];
	  }
  }
  
  if(count($list)>0) {?>
  <section class="news-home">
    <div class="container">
      <h2>CHIA SẺ KINH NGHIỆM</h2>
      <p class="note">Nhưng chia sẻ thú vị quanh Music</p>
      <div class="row page-content-news">
        <? foreach($list as $a){?>
          <div class="col-md-4 col-6">
            <a href="<?=$a['link']?>" title="<?=$a['name']?>">
              <div class="box-img-content-news">
                <img src="<?=$a['thumb']?>" data-src="<?=$a['rias']?>" data-widths="[250,500,800,1000,1200]" data-optimumx="1.6" data-sizes="auto" class="lazyload" alt="<?=$a['name']?>">
                <h3><?=$a['name']?></h3>
                <!-- <p><?=$a['brief']?></p> -->
              </div>
            </a>
          </div>
        <? } ?>
      </div>
    </div>
  </section>
  <?
  }?>

  <section class="breadcrumb">
    <ul>
      <li><a href="<?=URL_Rewrite('')?>"><?=lg('Home')?></a></li>
    </ul>
  </section>
  <? 
    include_once('_popup.php');
    include_once('_footer.php');
  ?>
</body>
</html>