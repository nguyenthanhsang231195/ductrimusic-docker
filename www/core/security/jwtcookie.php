<?
use \Firebase\JWT\JWT;

define('SECRET_KEY', 'a*Y2Om6*9uM?AGSu8Vq#RUMA@Tc');
define('ALGORITHM', 'HS512');

function JWTEncode($data) {
  if(empty($data)) return false;

  $tokenId    = base64_encode(mcrypt_create_iv(32));
  $issuedAt   = time();
  $notBefore  = $issuedAt;
  $expire     = $notBefore + COOKIE_TIME;
  $serverName = Protocol().'://'.MAIN_DOMAIN;

  // Token data
  $token = [
    'iat'  => $issuedAt,    // Issued at: time when the token was generated
    'jti'  => $tokenId,     // Json Token Id: an unique identifier for the token
    'iss'  => $serverName,  // Issuer
    'nbf'  => $notBefore,   // Not before
    'exp'  => $expire,      // Expire
    'data' => $data         // Your data
  ];
  $jwt = JWT::encode($token, SECRET_KEY, ALGORITHM);

  return $jwt;
}

function JWTDecode($jwt) {
  if (empty($jwt)) return false;

  try {
    $token = JWT::decode($jwt, SECRET_KEY, [ALGORITHM]);
    return Object2Array($token->data);
  }
  catch (Exception $e) {
    //echo 'JWTDecode: '.$e;
    return false;
  }
}

function JWTClear($cookie=COOKIE_NAME) {
  setcookie ($cookie, '', time()-3600, '/');
}

function Object2Array($obj) {
  if(!is_object($obj) && !is_array($obj)) return $obj;
  return array_map(__FUNCTION__, (array) $obj);
}

// Save SESSION
$jwtsave = 0;


function JWTStart() {
  ob_start();
  $jwt = $_COOKIE[COOKIE_NAME];
  $data = JWTDecode($jwt);

  if($data!==false) {
    $_SESSION['MemID'] = isset($data['MemID']) ? $data['MemID'] : 0;
    $_SESSION['Guid'] = isset($data['Guid']) ? $data['Guid'] : '';
    $_SESSION['QsvID'] = isset($data['QsvID']) ? $data['QsvID'] : 0;
    $_SESSION['Quid'] = isset($data['Quid']) ? $data['Quid'] : '';      
    //echo '<b>JWT Start</b><br>';
    //echo '<pre>'.print_r($data,true).'</pre>';
  }
}

function JWTSave() {
  global $jwtsave;
  $jwtsave++;
}

function JWTEnd() {
  global $jwtsave;
  if($jwtsave>0) {
    $data = [
      'MemID' => isset($_SESSION['MemID']) ? $_SESSION['MemID'] : 0,
      'Guid' => isset($_SESSION['Guid']) ? $_SESSION['Guid'] : '',
      'QsvID' => isset($_SESSION['QsvID']) ? $_SESSION['QsvID'] : 0,
      'Quid' => isset($_SESSION['Quid']) ? $_SESSION['Quid'] : ''
    ];
    $jwt = JWTEncode($data);
    setcookie(COOKIE_NAME, $jwt, 0, '/');

    //echo '<b>JWT Save</b><br>';
    //echo '<pre>'.print_r($data,true).'</pre>';
  }

  //echo '<b>JWT End</b>';
  ob_end_flush();
  exit;
}

register_shutdown_function('JWTEnd');
JWTStart();

?>