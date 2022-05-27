<footer>
  <div class="container">
    <div class="row">
      <?
      $s = "SELECT * FROM ".PREFIX_NAME."bottom".SUPFIX_NAME."
            WHERE chID='0' ".lw('AND')." ORDER BY Thutu LIMIT 1";
      if($r = $dx->get_row($s)){?>
      <div class="col-md-3 col-12 list-menu-footer">
        <h5><a href="<?=$r->Link?>"><?=stripslashes($r->Ten)?></a></h5>
        <?
        $ss = "SELECT * FROM ".PREFIX_NAME."bottom".SUPFIX_NAME."
               WHERE chID='".$r->btmID."' ORDER BY Thutu";
        if($rrs = $dx->get_results($ss)){?>
        <ul class="links">
          <? foreach($rrs as $rr){?>
          <li>
            <a href="<?=$rr->Link?>"><span class="material-icons">keyboard_arrow_right</span> <?=stripslashes($rr->Ten)?></a>
          </li>
          <?}?>
        </ul>
        <? }?>
      </div>
      <? }?>
      <?
      $s = "SELECT * FROM ".PREFIX_NAME."bottom".SUPFIX_NAME."
            WHERE chID='0' ".lw('AND')." ORDER BY Thutu LIMIT 1,10";
      if($rs = $dx->get_results($s)){
        foreach($rs as $r){?>
      <div class="col-md-3 col-12 list-menu-footer">
        <h5><a href="<?=$r->Link?>"><?=stripslashes($r->Ten)?></a></h5>
        <?
        $ss = "SELECT * FROM ".PREFIX_NAME."bottom".SUPFIX_NAME."
               WHERE chID='".$r->btmID."' ".lw('AND')." ORDER BY Thutu";
        if($rrs = $dx->get_results($ss)){?>
        <ul class="links">
          <? foreach($rrs as $rr){?>
          <li>
            <a href="<?=$rr->Link?>"><span class="material-icons">keyboard_arrow_right</span> <?=stripslashes($rr->Ten)?></a>
          </li>
          <?}?>
        </ul>
        <? }?>
      </div>
      <?
      }
    }
    ?>
    <div class="col-md-3 col-12 follow">
        <div class="follow-us">
          <h5 class="social">FOLLOW US</h5>
          <p>
            <a href="<?=$web['facebook']?>" target="_blank">
              <img src="img/facebook.png" alt="">
            </a>
            <a href="<?=$web['instagram']?>" target="_blank">
              <img src="img/instagram.png" alt="">
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>
  <div class="info-company">
    <p>
      <a href="<?=URL_Rewrite('')?>"><img src="<?=$web['nlogo']?>" alt="genesys"></a>
    </p>
    <ul class="info-contact-footer">
      <li>
        <span class="material-icons">phone</span> <?=$web['hotline']?>
      </li>
      <li>
        <span class="material-icons">email</span> support@ductrimusic.vn
      </li>
      <li>
        <span class="material-icons">public</span> www.ductrimusic.vn
      </li>
    </ul>
    <ul class="info-contact-footer">
      <li>
        <span class="material-icons">place</span> 590/2 Phan Văn Trị, Phường 7, Gò Vấp, TP. Hồ Chí Minh.
      </li>
      <li>
        <span class="material-icons">place</span> 658/13 Cách Mạng Tháng Tám, Phường 11, Quận 3, TP. Hồ Chí Minh.
      </li>
  </div>
</footer>
<? 
    include_once('_popup.php');
  ?>
<seciton class="cta-scroll-all">
  <div class="container">
    <div class="row">
      <div class="offset-md-3 col-md-3 col-6">
        <a href="tel:<?=$web['hotline']?>" class="secondary-btn"><?=$web['hotline']?></a>
      </div>
      <div class="col-md-3 col-6">
        <button class="primary-btn genesys-contact-show-popup">TƯ VẤN NGAY</button>
      </div>
    </div>
  </div>
</seciton>

<!-- jQuery -->
<script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
<script>window.jQuery || document.write('<script src="ext/jquery/jquery.min.js"><\/script>')</script>

<!-- Scripts -->
<script src="dist/bundle.js"></script>
