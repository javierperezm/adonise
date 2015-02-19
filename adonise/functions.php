<?php
function isHTTPS()
{
	return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'];
}

/**
 * Check if this is run in the admin area
 * @return bool
 */
function isAdmin()
{
	return array_shift(explode('/',A_Request::getSingleton()->getUri(),2)) == (defined('A_ADMIN_URI') ? A_ADMIN_URI : 'admin');
}
function getBaseUrl( $full_url )
{
	if ( $full_url ) {
		$url = 'http' . (isHTTPS()?'s':'') . '://' . $_SERVER['SERVER_NAME'];
	} else {
		$url = '';
	}
	return $url . A_URI_BASE . '/';
}
function getUrl($uri = '', $params = null)
{
	return getBaseUrl(A_FULL_URL) . ltrim($uri,'/') . (is_array($params) ? ('?'.http_build_query($params)) : '');
}
function getAssetUrl($uri)
{
	static $assets = array();

	if ( ! isset($assets[$uri]) ) {
		$assets[$uri] = processModules(function($module,$version,$path,$extra){
			$relative = "assets/{$extra['uri']}";
			if ( is_file($path.$relative) ) return getModuleUrl($module, A_ASSET_FULL_URL) . $relative;
		},array(
			'uri'	=> $uri,
		));
	}

	return $assets[$uri];
}
function getMediaUrl($uri)
{
	return getBaseUrl(A_MEDIA_FULL_URL) . 'media/' . ltrim($uri);
}



function camelcase2varname($camelcase)
{
	return trim(strtolower(preg_replace('/([A-Z]{1})/', '_$1', $camelcase)),'_');
}
function uri2camelcase($uri)
{
	return preg_replace_callback('/[\-_]{1}[a-z]{1}/',function($M){
		return strtoupper($M[0][1]);
	}, strtolower($uri));
}

function getModuleDirectory( $module )
{
	global $modules;
	$version = $modules[$module];
	return A_ROOT_DIR . "/adonise/modules/{$module}-{$version}/";
}
function getModuleUrl( $module, $full = false )
{
	global $modules;
	$version = $modules[$module];
	return getBaseUrl( $full ) . "adonise/modules/{$module}-{$version}/";
}
function processModules( $callback, $extra = array() )
{
	global $modules;

	foreach ( $modules as $module => $version ) {
		$path = A_ROOT_DIR . "/adonise/modules/{$module}-{$version}/";
		$response = $callback($module, $version, $path, $extra);
		if ( $response !== null ) return $response;
	}

	return null;
}
