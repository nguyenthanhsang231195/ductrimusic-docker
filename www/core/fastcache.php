<?
use phpFastCache\CacheManager;
use phpFastCache\Core\phpFastCache;

// Default cache config
$xcache = null;
CacheManager::setDefaultConfig([
	'path'			=> CACHE_PATH,
	'defaultTtl' 	=> 300,	// 5 minutes
	'default_chmod' => 0755,
	'memcache'		=> [
		[CACHE_HOST, CACHE_PORT]
	],
	'fallback' 		=> 'files'
]);

function InitCache() {
	if(!CACHE_ENABLE) return false;

	global $xcache;
	if(is_object($xcache)) return $xcache;

	$xcache = CacheManager::getInstance(CACHE_DRIVER);
	return $xcache;
}

function HasCache($key) {
	if(empty($key)) return false;

	if($cache = InitCache()) {
	    $item = $cache->getItem($key);
		return $item->isHit();
	}
	return false;
}

function SetCache($key, $value, $expire=0, $tag='html') {
	if(empty($key) || empty($value)) return false;

	if($cache = InitCache()) {
	    $item = $cache->getItem($key);
	    $item->set($value)->expiresAfter($expire);
	    if($tag!='') $item->addTag($tag);

	    $cache->save($item);
	    return true;
	}
	return false;
}

function GetCache($key) {
	if(empty($key)) return false;

	if($cache = InitCache()) {
	    $item = $cache->getItem($key);
	    return $item->get();
	}
	return false;
}

function RemoveCache($key) {
	if(empty($key)) return false;

	if($cache = InitCache()) return $cache->deleteItem($key);
	return false;
}

function ClearCache() {
	if($cache = InitCache()) return $cache->deleteItemsByTag('html');
	return false;
}

?>