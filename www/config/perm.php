<?
// Cau hinh chung
if(!isset($QSV)) $QSV = [];

// Danh sach quyen
$perm = [
  'PRODUCT' => [
    'name'	=> 'Sản phẩm',
    'href'	=> 'qsvpro/product.php',
    'type'	=> 'mgr'
  ],
  'PRODUCT_ACTIVE' => [
    'name'	=> 'Duyệt sản phẩm',
    'href'	=> 'qsvpro/product.php',
    'type'	=> 'active'
  ],
  'PRODUCT_PRICE' => [
    'name'	=> 'Xem bảng giá',
    'href'	=> 'qsvpro/product.php',
    'type'	=> 'price'
  ],
  'COLLECTION' => [
    'name'	=> 'Bộ sưu tập',
    'href'	=> 'qsvpro/collection.php'
  ],
  'RANGE' => [
    'name'	=> lg('Price range'),
    'href'	=> 'qsvpro/range.php'
  ],
  'PRODUCT_COLOR' => [
    'name'	=> 'Màu sắc',
    'href'	=> 'qsvpro/product_color.php'
  ],
  'PRODUCT_CATALOG' => [
    'name'	=> 'Danh mục',
    'href'	=> 'qsvpro/product_catalog.php'
  ],
  'FEATURE' => [
    'name'	=> 'Thuộc tính',
    'href'	=> 'qsvpro/feature.php'
  ],

  'NEWS' => [
    'name'	=> lg('News'),
    'href'	=> 'qsvpro/news.php'
  ],
  'NEWS_CATALOG' => [
    'name'	=> lg('News Catalog'),
    'href'	=> 'qsvpro/news_catalog.php'
	],
	'PRESS' => [
    'name'	=> lg('Press'),
    'href'	=> 'qsvpro/press.php'
  ],
  'TECHNICAL' => [
    'name'	=> lg('Technical'),
    'href'	=> 'qsvpro/technical.php'
  ],
	'SUPPORT' => [
		'name'	=> 'Tuyển dụng',
		'href'	=> 'qsvpro/support.php'
	],
	'ARTICLE' => [
		'name'	=> lg('View Article'),
		'href'	=> 'qsvpro/article.php',
		'type'	=> 'view'
	],
	'ARTICLE_ADD' => [
		'name'	=> lg('Add Article'),
		'href'	=> 'qsvpro/article.php',
		'type'	=> 'add'
	],
	'ARTICLE_EDIT' => [
		'name'	=> lg('Edit Article'),
		'href'	=> 'qsvpro/article.php',
		'type'	=> 'edit'
	],
	'ARTICLE_DEL' => [
		'name'	=> lg('Delete Article'),
		'href'	=> 'qsvpro/article.php',
		'type'	=> 'del'
	],
	'ARTICLE_ACTIVE' => [
		'name'	=> lg('Active Article'),
		'href'	=> 'qsvpro/article.php',
		'type'	=> 'active'
	],
	'ARTICLE_CATALOG' => [
		'name'	=> lg('Article Catalog'),
		'href'	=> 'qsvpro/article_catalog.php'
  ],
  'ARTICLE_TYPE' => [
		'name'	=> lg('Article Type'),
		'href'	=> 'qsvpro/article_type.php'
  ],
  'FULLPAGE' => [
    'name'	=> lg('Landing page'),
    'href'	=> 'qsvpro/fullpage.php'
  ],
  'PAGES' => [
		'name'	=> lg('Pages'),
		'href'	=> 'qsvpro/pages.php'
	],
	'PARTNER' => [
		'name'	=> lg('Partner'),
		'href'	=> 'qsvpro/partner.php'
  ],
  'SHOP' => [
    'name'	=> lg('Shop'),
    'href'	=> 'qsvpro/shop.php'
  ],

  'REQUEST' => [
    'name'	=> 'Yêu cầu, lịch hẹn',
    'href'	=> 'qsvpro/request.php'
  ],
	'CUSTOMER' => [
		'name'	=> lg('Customer'),
		'href'	=> 'qsvpro/customer.php'
  ],
  'MEMBER' => [
		'name'	=> lg('Member'),
		'href'	=> 'qsvpro/member.php'
	],
  'FEEL' => [
    'name'	=> 'Cảm nhận',
    'href'	=> 'qsvpro/feel.php'
  ],
  'SOURCE' => [
    'name'	=> 'Danh sách nguồn',
    'href'	=> 'qsvpro/source.php'
  ],
  /*
	'DEALER' => [
		'name'	=> lg('Dealer'),
		'href'	=> 'qsvpro/dealer.php',
  ],
  */
  
  'WEBSITE' => [
		'name'	=> lg('Website Config'),
		'href'	=> 'custom/website.php'
	],
	'SLIDESHOW' => [
		'name'	=> lg('Slideshow'),
		'href'	=> 'qsvpro/slideshow.php'
	],
	'BANNERSALES' => [
		'name'	=> lg('Banner Sales'),
		'href'	=> 'qsvpro/banner_sales.php'
	],
	'POPUP' => [
		'name'	=> lg('Popup'),
		'href'	=> 'qsvpro/popup.php'
	],
	'MENU' => [
		'name'	=> lg('Menu'),
		'href'	=> 'qsvpro/menu.php'
	],
	'TOP' => [
		'name'	=> lg('Top'),
		'href'	=> 'qsvpro/top.php'
	],
	'BOTTOM' => [
		'name'	=> lg('Bottom'),
		'href'	=> 'qsvpro/bottom.php'
	],
	'INTRO' => [
		'name'	=> lg('Introduce'),
		'href'	=> 'custom/intro.php'
	],
	'CONTACT' => [
		'name'	=> lg('Contact'),
		'href'	=> 'custom/contact.php'
	],
	'MAP' => [
		'name'	=> lg('Map'),
		'href'	=> 'custom/map.php'
  ],
	'SOCIAL' => [
		'name'	=> lg('Social connect'),
		'href'	=> 'custom/social.php'
	],
	'EMAIL' => [
		'name'	=> lg('Email received'),
		'href'	=> 'custom/email.php'
  ],
  /*
	'PAYMENT' => [
		'name'	=> lg('Payment'),
		'href'	=> 'custom/payment.php'
  ],
  */
	'TRACKER' => [
		'name'	=> lg('Embedded code'),
		'href'	=> 'custom/tracker.php'
  ],
	'REWRITE' => [
		'name'	=> lg('Rewrite URL'),
		'href'	=> 'qsvpro/rewrite.php'
	],
	'REDIRECT' => [
		'name'	=> lg('Redirect Table'),
		'href'	=> 'qsvpro/redirect.php'
	],
	'AUTOKW' => [
		'name'	=> lg('Auto Link'),
		'href'	=> 'qsvpro/autolink.php'
	],
	'404PAGE' => [
		'name'	=> lg('Error Pages'),
		'href'	=> 'qsvpro/404page.php'
	],
	'COPYRIGHT' => [
		'name'	=> lg('Copyright'),
		'href'	=> 'custom/copyright.php'
  ],

  'LANGUAGE' => [
    'name'	=> lg('Language'),
    'href'	=> 'qsvpro/language.php'
  ],
  'LANGDEF' => [
    'name'	=> lg('Translate text'),
    'href'	=> 'qsvpro/langdef.php'
  ],

	'ADMIN' => [
		'name'	=> lg('Administrator'),
		'href'	=> 'qsvpro/admin.php'
	],
	'ADMIN_ACCESS' => [
		'name'	=> lg('Admin Access'),
		'href'	=> 'qsvpro/admin_access.php'
	],
	'STAFF' => [
		'name'	=> lg('Staff'),
		'href'	=> 'qsvpro/staff.php'
	],
	'STAFF_ACCESS' => [
		'name'	=> lg('Staff Access'),
		'href'	=> 'qsvpro/staff_access.php'
	],
	'ADMIN_TYPE' => [
		'name'	=> lg('Change Type'),
		'href'	=> 'qsvpro/admin_type.php'
	]
];
$QSV['perm'] = $perm;

// Chia cac nhom quyen
$role = [];

// Quan tri vien
$role[1] = array_keys($perm); //echo "'".join("','",$role[1])."'";
// Nhan vien
$role[2] = [
  'PRODUCT','PRODUCT_ACTIVE','PRODUCT_PRICE',
  'COLLECTION','RANGE','PRODUCT_COLOR','PRODUCT_CATALOG','FEATURE',
  'NEWS','NEWS_CATALOG','PRESS','TECHNICAL','SUPPORT','ARTICLE','ARTICLE_ADD','ARTICLE_EDIT','ARTICLE_CATALOG',
  'FULLPAGE','PAGES','PARTNER','SHOP',
  'REQUEST','CUSTOMER','FEEL','SOURCE',
  'WEBSITE','SLIDESHOW','BANNERSALES','POPUP','MENU','TOP','BOTTOM','INTRO','CONTACT','MAP','SOCIAL',
  'TRACKER','REWRITE','REDIRECT','AUTOKW','404PAGE',
  'LANGUAGE', 'LANGDEF'
];
// Thanh vien
$role[3] = [];
// Dai ly
$role[4] = [];
// Khach hang
$role[5] = [];

$QSV['role'] = $role;
?>