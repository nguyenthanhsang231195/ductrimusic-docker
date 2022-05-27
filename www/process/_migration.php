<?
require_once('../config/config.php');
if(!CheckLogged()) exit;

// Khoi tao ngon ngu
language();


echo '<h1>Language Product</h1>';
ldc([
		'n'	=> PREFIX_NAME.'product'.SUPFIX_NAME,
    'c'	=> [
      'proID','Ten','SKU','Giaban','GiaKM','Noteprice','URL','Anh','Tomtat','Mota','Slide','Filegia','Mausac','Danhmuc','Bosuutap','Note','Lienquan','View','Hot','Active','NgayCN','TagTitle','TagDesc'
    ]
  ], [
    'n'	=> PREFIX_NAME.'product_lg'.SUPFIX_NAME,
    'c'	=> [
      'lgID','Ten','SKU','Giaban','GiaKM','Noteprice','URL','Anh','Tomtat','Mota','Slide','Filegia','Mausac','Danhmuc','Bosuutap','Note','Lienquan','View','Hot','Active','NgayCN','TagTitle','TagDesc'
    ]
  ],
  'en', '', true
);
/*
ldm([
		'n'	=> PREFIX_NAME.'product'.SUPFIX_NAME,
    'c'	=> [
      'proID','SKU','Giaban','GiaKM','Anh','Slide','Filegia','Mausac','Danhmuc','Bosuutap','Lienquan','View','Hot','Active','NgayCN'
    ]
  ], [
    'n'	=> PREFIX_NAME.'product_lg'.SUPFIX_NAME,
    'c'	=> [
      'lgID','SKU','Giaban','GiaKM','Anh','Slide','Filegia','Mausac','Danhmuc','Bosuutap','Lienquan','View','Hot','Active','NgayCN'
    ]
  ],
  'en', '', true
);
*/

echo '<h1>Language Product Catalog</h1>';
ldc([
		'n'	=> PREFIX_NAME.'product_catalog'.SUPFIX_NAME,
    'c'	=> [
      'catID','Ten','URL','Anh','AnhCat','Tomtat','Mota',
      'Vitri','Thutu','chID','Index','TagTitle','TagDesc'
    ]
  ], [
    'n'	=> PREFIX_NAME.'product_catalog_lg'.SUPFIX_NAME,
    'c'	=> [
      'lgID','Ten','URL','Anh','AnhCat','Tomtat','Mota',
      'Vitri','Thutu','chID','Index','TagTitle','TagDesc'
    ]
  ],
  'en', '', true
);
/*
ldm([
		'n'	=> PREFIX_NAME.'product_catalog'.SUPFIX_NAME,
    'c'	=> [
      'catID','Anh','AnhCat','Vitri','Thutu','chID'
    ]
  ], [
    'n'	=> PREFIX_NAME.'product_catalog_lg'.SUPFIX_NAME,
    'c'	=> [
      'lgID','Anh','AnhCat','Vitri','Thutu','chID'
    ]
  ],
  'en', '', true
);
*/


echo '<h1>Language Feature</h1>';
ldc([
		'n'	=> PREFIX_NAME.'feature'.SUPFIX_NAME,
    'c'	=> [
      'feaID','Ten','Danhmuc','NgayCN'
    ]
  ], [
    'n'	=> PREFIX_NAME.'feature_lg'.SUPFIX_NAME,
    'c'	=> [
      'lgID','Ten','Danhmuc','NgayCN'
    ]
  ],
  'en', '', true
);
/*
ldm([
    'n'	=> PREFIX_NAME.'feature'.SUPFIX_NAME,
    'c'	=> [
      'feaID','Danhmuc','NgayCN'
    ]
  ], [
    'n'	=> PREFIX_NAME.'feature_lg'.SUPFIX_NAME,
    'c'	=> [
      'lgID','Danhmuc','NgayCN'
    ]
  ],
  'en', '', true
);
*/


echo '<h1>Language Feature Item</h1>';
ldc([
		'n'	=> PREFIX_NAME.'feature_item'.SUPFIX_NAME,
    'c'	=> [
      'fitID','Giatri','feaID','NgayCN'
    ]
  ], [
    'n'	=> PREFIX_NAME.'feature_item_lg'.SUPFIX_NAME,
    'c'	=> [
      'lgID','Giatri','feaID','NgayCN'
    ]
  ],
  'en', '', true
);
/*
ldm([
    'n'	=> PREFIX_NAME.'feature_item'.SUPFIX_NAME,
    'c'	=> [
      'fitID','feaID','NgayCN'
    ]
  ], [
    'n'	=> PREFIX_NAME.'feature_item_lg'.SUPFIX_NAME,
    'c'	=> [
      'lgID','feaID','NgayCN'
    ]
  ],
  'en', '', true
);
*/


echo '<h1>Language Product Color</h1>';
ldc([
		'n'	=> PREFIX_NAME.'product_color'.SUPFIX_NAME,
    'c'	=> [
      'colorID','Ten','URL','Mamau','Danhmuc','Thutu'
    ]
  ], [
    'n'	=> PREFIX_NAME.'product_color_lg'.SUPFIX_NAME,
    'c'	=> [
      'lgID','Ten','URL','Mamau','Danhmuc','Thutu'
    ]
  ],
  'en', '', true
);
/*
ldm([
    'n'	=> PREFIX_NAME.'product_color'.SUPFIX_NAME,
    'c'	=> [
      'colorID','Mamau','Danhmuc','Thutu'
    ]
  ], [
    'n'	=> PREFIX_NAME.'product_color_lg'.SUPFIX_NAME,
    'c'	=> [
      'lgID','Mamau','Danhmuc','Thutu'
    ]
  ],
  'en', '', true
);
*/

echo '<h1>Language Price Range</h1>';
ldc([
		'n'	=> PREFIX_NAME.'price_range'.SUPFIX_NAME,
    'c'	=> [
      'praID','Ten','Giatu','Giaden','Danhmuc','Thutu'
    ]
  ], [
    'n'	=> PREFIX_NAME.'price_range_lg'.SUPFIX_NAME,
    'c'	=> [
      'lgID','Ten','Giatu','Giaden','Danhmuc','Thutu'
    ]
  ],
  'en', '', true
);
/*
ldm([
    'n'	=> PREFIX_NAME.'price_range'.SUPFIX_NAME,
    'c'	=> [
      'praID','Giatu','Giaden','Danhmuc','Thutu'
    ]
  ], [
    'n'	=> PREFIX_NAME.'price_range_lg'.SUPFIX_NAME,
    'c'	=> [
      'lgID','Giatu','Giaden','Danhmuc','Thutu'
    ]
  ],
  'en', '', true
);
*/


echo "Finish Migration Data!";
?>