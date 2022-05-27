<?
define('RAPNET_HOST', 'https://technet.rapaport.com/HTTP/JSON/RetailFeed/');
define('RAPNET_USER',	'giflsdqe5fegdoomuz041kszwytzce');
define('RAPNET_PASS',	'j29h0WE3');

//------------------------------------------------------------------------------------
// Lay danh sach Kim cuong - QsvProgram (10/07/2021)
//------------------------------------------------------------------------------------
function GetDiamonds($page=1, $size=20, $debug=false) {
  $client = new GuzzleHttp\Client([
    'base_uri' => RAPNET_HOST,
    'cookies' => true,
    'debug' => $debug
  ]);

  $post = [
    "request" => [
      "header" => [
        "username" => RAPNET_USER,
        "password" => RAPNET_PASS
      ],
      "body" => [
        "page_number"=> $page,
        "page_size"=> $size
      ]
    ]
  ];
  if($debug) echo 'Request:<pre>'.print_r($post,true).'</pre>';

  $response = $client->request('POST', 'GetDiamonds.aspx', [
    'headers' => [
      'Content-Type' => 'application/x-www-form-urlencoded'
    ],
    'json' => $post
  ]);
  $body = $response->getBody();
  $data = json_decode($body,true);
  if($debug) {
    echo 'Result:<pre>'.$body.'</pre>';
    echo 'Response:<pre>'.print_r($data,true).'</pre>';
  }

  return $data;
}

//------------------------------------------------------------------------------------
// Chi tiet Kim cuong - QsvProgram (10/07/2021)
//------------------------------------------------------------------------------------
function SingleDiamond($id, $debug=false) {
  $client = new GuzzleHttp\Client([
    'base_uri' => RAPNET_HOST,
    'cookies' => true,
    'debug' => $debug
  ]);

  $post = [
    "request" => [
      "header" => [
        "username" => RAPNET_USER,
        "password" => RAPNET_PASS
      ],
      "body" => [
        "diamond_id"=> $id
      ]
    ]
  ];
  if($debug) echo 'Request:<pre>'.print_r($post,true).'</pre>';

  $response = $client->request('POST', 'GetSingleDiamond.aspx', [
    'headers' => [
      'Content-Type' => 'application/x-www-form-urlencoded'
    ],
    'json' => $post
  ]);
  $body = $response->getBody();
  $data = json_decode($body,true);
  if($debug) {
    echo 'Result:<pre>'.$body.'</pre>';
    echo 'Response:<pre>'.print_r($data,true).'</pre>';
  }

  return $data;
}


//------------------------------------------------------------------------------------
// Xac dinh ty gia vang - QsvProgram (09/07/2021)
//------------------------------------------------------------------------------------
function GoldValue($debug=false) {
  // Detect cache
  if(!empty($_SESSION['gold'])) {
    $body = $_SESSION['gold'];
    if($debug) echo 'Cached:<pre>'.$body.'</pre>';
  }
  else {
    $client = new GuzzleHttp\Client([
      'cookies' => true,
      'debug' => $debug
    ]);

    // Query aata
    $get = [
      'ran'   => 0,
      'rate'  => 0,
      'gold'  => 1,
      'bank'  => 'VIETCOM',
      'date'  => 'now'
    ];
    if($debug) echo 'Query:<pre>'.print_r($get,true).'</pre>';

    // Send data to Webservice
    $response = $client->request('GET', 'https://tygia.com/json.php', [
      'query' => $get
    ]);
    $body = $response->getBody();
    if($debug) echo 'Result:<pre>'.$body.'</pre>';

    // Remove BOM char
    if (substr($body,0,3) == "\xef\xbb\xbf") {
      $body = substr($body,3);
    }

    // Save cache
    $_SESSION['gold'] = $body;
  }

  $data = json_decode($body,true);
  if($debug) echo 'Response:<pre>'.print_r($data,true).'</pre>';

  return $data['golds'][0];
}


//------------------------------------------------------------------------------------
// reCAPTCHA verify - QsvProgram (24/03/2017)
// die('Check SSL: <pre>'.print_r(openssl_get_cert_locations(),true).'</pre>');
//------------------------------------------------------------------------------------
function CheckCaptcha($token) {
	$client = new GuzzleHttp\Client([
			'base_uri' => 'https://www.google.com/recaptcha/api/',
			'timeout'  => 2.0,
	]);

	$response = $client->request('POST', 'siteverify', [
		'form_params' => [
			'secret'		=> '6Lf_7RkUAAAAAI4tDYzMr9CCeGLorNsNqDFkdutV',
			'response' 	=> $token,
			'remoteip'	=> ClientIP()
		]
	]);
	$body = $response->getBody();
	$data = json_decode($body,true);
	//echo '<pre>'.print_r($data,true).'</pre>';

	return $data['success']==true;
}
