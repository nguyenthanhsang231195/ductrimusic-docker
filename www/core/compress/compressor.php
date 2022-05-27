<?
//==========================================================================
// Compress HTML, JS, CSS - QsvProgram (30-03-2016)
//==========================================================================

require_once(__DIR__.'/min-css.php');
require_once(__DIR__.'/min-js.php');
require_once(__DIR__.'/htmlCompress.php');


function CompressHTML($code, $inline=true){
    $html = new Tinyfier_HTML_Tool();
    $minified = $html->process($code, array(
        "compress_all" => $inline
    ));

    return $minified;
}

function CompressJS($code){
   $jSqueeze = new JSqueeze();
   $minified = $jSqueeze->squeeze($code, true, false);

   return $minified;
}

function CompressCSS($code){
   $minifier = new CssMinifier($code, array(), array(
      "Variables"                => true,
      "ConvertFontWeight"        => true,
      "ConvertHslColors"         => true,
      "ConvertRgbColors"         => true,
      "ConvertNamedColors"       => true,
      "CompressColorValues"      => true,
      "CompressUnitValues"       => true,
      "CompressExpressionValues" => true
   ));
   $minified = $minifier->getMinified();

   return $minified;
}

?>