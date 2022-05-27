<?
// Cau hinh chung
if(!isset($QSV)) $QSV = [];

// Ten he thong
$QSV['name'] = 'Ductri Administrator';

// Da ngon ngu
$product = 'qsvpro/product.php';
$pcatalog = 'qsvpro/product_catalog.php';
$pcolor = 'qsvpro/product_color.php';
$feature = 'qsvpro/feature.php';
$fitem = 'qsvpro/feature_item.php';
$range = 'qsvpro/range.php';

if(MULTI_LANGUAGE && lc()!=DEFAULT_LANGUAGE){
  $product = 'qsvpro/product_lg.php';
  $pcatalog = 'qsvpro/product_catalog_lg.php';
  $pcolor = 'qsvpro/product_color_lg.php';
  $feature = 'qsvpro/feature_lg.php';
  $fitem = 'qsvpro/feature_item_lg.php';
  $range = 'qsvpro/range_lg.php';
}


// Menu chinh
$mn = [];
$mn[] = [
	'name'	=> lg('Statistic'),
	'icon'	=> 'icon-bar-chart',
	'href'	=> 'custom/statis.php',
	'key'	=> ''
];

$mn[] = [
	'name'	=> lg('Product manager'),
	'icon'	=> 'icon-picture',
	'sub'	=> [
		1 => [
			'name'	=> lg('Product'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> $product,
			'key'	=> 'PRODUCT'
    ],
    2 => [
			'name'	=> lg('Collection'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/collection.php',
			'key'	=> 'COLLECTION'
		],
		3 => [
			'name'	=> lg('Price range'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> $range,
			'key'	=> 'RANGE'
    ],
		4 => [
			'name'	=> lg('Color'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> $pcolor,
			'key'	=> 'PRODUCT_COLOR'
		],
		5 => [
			'name'	=> lg('Catalog'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> $pcatalog,
			'key'	=> 'PRODUCT_CATALOG'
    ],
		6 => [
			'name'	=> lg('Feature'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> $feature,
			'key'	=> 'FEATURE'
		],
		7 => [
			'name'	=> lg('Feature Item'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> $fitem,
			'key'	=> 'FEATURE'
		]
  ]
];

$mn[] = [
	'name'	=> lg('Info Mananger'),
	'icon'	=> 'icon-magic',
	'sub'	=> [
    1 => [
			'name'	=> lg('News'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/news.php',
			'key'	=> 'NEWS'
    ],
		2 => [
			'name'	=> lg('News Catalog'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/news_catalog.php',
			'key'	=> 'NEWS_CATALOG'
		],
		3 => [
			'name'	=> lg('Press'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/press.php',
			'key'	=> 'PRESS'
		],
		4 => [
			'name'	=> lg('Technical'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/technical.php',
			'key'	=> 'TECHNICAL'
    ],
		5 => [
			'name'	=> 'Tuyển dụng',
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/support.php',
			'key'	=> 'SUPPORT'
    ],
    
		6 => [
			'name'	=> lg('Article Catalog'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/article_catalog.php',
			'key'	=> 'ARTICLE_CATALOG'
		],
		7 => [
			'name'	=> lg('Article Type'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/article_type.php',
			'key'	=> 'ARTICLE_TYPE'
		],
		8 => [
			'name'	=> lg('Landing page'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/fullpage.php',
			'key'	=> 'FULLPAGE'
		],
		9 => [
			'name'	=> lg('Pages'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/pages.php',
			'key'	=> 'PAGES'
		],
		10 => [
			'name'	=> lg('Partner'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/partner.php',
			'key'	=> 'PARTNER'
		],
		11 => [
			'name'	=> lg('Shop'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/shop.php',
			'key'	=> 'SHOP'
		]
		// 12 => [
		// 	'name'	=> lg('Article'),
		// 	'icon'	=> 'icon-double-angle-right',
		// 	'href'	=> 'qsvpro/article.php',
		// 	'key'	=> 'ARTICLE'
		// ],
	]
];

$mn[] = [
	'name'	=> lg('Customer Manager'),
	'icon'	=> 'icon-group',
	'sub'	=> [
    1 => [
			'name'	=> lg('Requirement, schedule'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/request.php',
			'key'	=> 'REQUEST'
    ],
    2 => [
			'name'	=> lg('Customer'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/customer.php',
			'key'	=> 'CUSTOMER'
		],
		3 => [
			'name'	=> lg('Member'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/member.php',
			'key'	=> 'MEMBER'
    ],
    4 => [
			'name'	=> lg('Feel'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/feel.php',
			'key'	=> 'FEEL'
    ],
    5 => [
			'name'	=> lg("Source"),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/source.php',
			'key'	=> 'SOURCE'
    ],
    /*
		6 => [
			'name'	=> lg('Dealer'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/dealer.php',
			'key'	=> 'DEALER'
    ]
    */
	]
];

$mn[] = [
	'name'	=> lg('Website Config'),
	'icon'	=> 'icon-cogs',
	'sub'	=> [
		1 => [
			'name'	=> lg('Website Config'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'custom/website.php',
			'key'	=> 'WEBSITE'
    ],
    2 => [
			'name'	=> lg('Slideshow'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/slideshow.php',
			'key'	=> 'SLIDESHOW'
		],
		3 => [
			'name'	=> lg('Popup'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/popup.php',
			'key'	=> 'POPUP'
		],
		4 => [
			'name'	=> lg('Banner Sales'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/banner_sales.php',
			'key'	=> 'BANNERSALES'
		],
		5 => [
			'name'	=> lg('Menu'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/menu.php',
			'key'	=> 'MENU'
		],
		6 => [
			'name'	=> lg('Top'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/top.php',
			'key'	=> 'TOP'
    ],
		7 => [
			'name'	=> lg('Bottom'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/bottom.php',
			'key'	=> 'BOTTOM'
    ],
		8 => [
			'name'	=> lg('Introduce'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'custom/intro.php',
			'key'	=> 'INTRO'
		],
		9 => [
			'name'	=> lg('Contact'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'custom/contact.php',
			'key'	=> 'CONTACT'
		],
		10 => [
			'name'	=> lg('Map'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'custom/map.php',
			'key'	=> 'MAP'
		],
		11 => [
			'name'	=> lg('Social connect'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'custom/social.php',
			'key'	=> 'SOCIAL'
		],
		12 => [
			'name'	=> lg('Email received'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'custom/email.php',
			'key'	=> 'EMAIL'
		],
    /*
		12 => [
			'name'	=> lg('Payment'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'custom/payment.php',
			'key'	=> 'PAYMENT'
    ],
    */
		13 => [
			'name'	=> lg('Embedded code'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'custom/tracker.php',
			'key'	=> 'TRACKER'
    ],
		14 => [
			'name'	=> lg('Rewrite URL'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/rewrite.php',
			'key'	=> 'REWRITE'
		],
		15 => [
			'name'	=> lg('Redirect Table'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/redirect.php',
			'key'	=> 'REDIRECT'
		],
		16 => [
			'name'	=> lg('Auto Link'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/autolink.php',
			'key'	=> 'AUTOKW'
		],
		17 => [
			'name'	=> lg('Error Pages'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/404page.php',
			'key'	=> '404PAGE'
		],
		18 => [
			'name'	=> lg('Copyright'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'custom/copyright.php',
			'key'	=> 'COPYRIGHT'
		]
	]
];

if(MULTI_LANGUAGE){
  $mn[] = [
    'name'	=> lg('Language Manager'),
    'icon'	=> 'icon-globe',
    'sub'	=> [
      1 => [
        'name'	=> lg('Language'),
        'icon'	=> 'icon-double-angle-right',
        'href'	=> 'qsvpro/language.php',
        'key'	=> 'LANGUAGE'
      ],
      2 => [
        'name'	=> lg('Translate text'),
        'icon'	=> 'icon-double-angle-right',
        'href'	=> 'qsvpro/langdef.php',
        'key'	=> 'LANGDEF'
      ]
    ]
  ];
}

$mn[] = [
	'name'	=> lg('Access Manager'),
	'icon'	=> 'icon-legal',
	'sub'	=> [
		1 => [
			'name'	=> lg('Administrator'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/admin.php',
			'key'	=> 'ADMIN'
		],
		2 => [
			'name'	=> lg('Admin Access'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/admin_access.php',
			'key'	=> 'ADMIN_ACCESS'
		],
		3 => [
			'name'	=> lg('Staff'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/staff.php',
			'key'	=> 'STAFF'
		],
		4 => [
			'name'	=> lg('Staff Access'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/staff_access.php',
			'key'	=> 'STAFF_ACCESS'
		],
		5 => [
			'name'	=> lg('Change Type'),
			'icon'	=> 'icon-double-angle-right',
			'href'	=> 'qsvpro/admin_type.php',
			'key'	=> 'ADMIN_TYPE'
		]
	]
];
$QSV['menu'] = $mn;


// Menu phia tren
$mt = [];
$mt[] = [
	'name'	=> lg('Profile'),
	'icon'	=> 'icon-user',
	'href'	=> 'custom/profile.php',
	'key'	=> ''
];
$mt[] = [
	'name'	=> lg('Change password'),
	'icon'	=> 'icon-key',
	'href'	=> 'custom/password.php',
	'key'	=> ''
];
$mt[] = [
	'name'	=> lg('Logout'),
	'icon'	=> 'icon-off',
	'href'	=> 'custom/logout.php',
	'key'	=> ''
];
$QSV['top'] = $mt;
?>