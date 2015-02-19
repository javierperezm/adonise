<?php
set_time_limit(0);

// FUNCTIONS ///////////////////////////////////////////////////////////////////////////////////////////////////////////
include_once 'functions.php';

// BASIC CONSTANTS /////////////////////////////////////////////////////////////////////////////////////////////////////

define('A_ROOT_DIR', realpath(dirname(__FILE__).'/..'));
define('A_ROOT_URI', substr(A_ROOT_DIR, strlen(realpath($_SERVER['DOCUMENT_ROOT']))));

define('A_MEDIA_DIR', realpath(A_ROOT_DIR.'/media'));
define('A_MEDIA_URI', A_ROOT_URI.'/media');

// GENERAL CONFIG //////////////////////////////////////////////////////////////////////////////////////////////////////
// 1.- config-www.myhost.com.php
// 2.- config-myhost.com.php
// 3.- config-com.php
// 4.- config.php
// Stop on the first file exists, and load configuration
$domain = explode('.',$_SERVER['SERVER_NAME']);
do {
    $path = A_ROOT_DIR . '/' . rtrim('config-'.implode('.',$domain),'-') . '.php';
    if ( is_file($path) ) {
        include $path;
        break;
    }
    array_shift($domain);
} while ( count($domain) >= 0 );


// DEFAULT CONSTANTS CONFIG ////////////////////////////////////////////////////////////////////////////////////////////
defined('A_FULL_URL') || define('A_FULL_URL', true);
defined('A_MEDIA_FULL_URL') || define('A_MEDIA_FULL_URL', true);
defined('A_ASSET_FULL_URL') || define('A_ASSET_FULL_URL', true);
defined('A_SCRIPT_FILE') || define('A_SCRIPT_FILE','index.php');

define('A_URI_BASE', rtrim(A_ROOT_URI . '/' . A_SCRIPT_FILE, '/'));

// MODULES CONFIG //////////////////////////////////////////////////////////////////////////////////////////////////////
// Load ALL adonise modules files, overriding previous values on each iteration
// 1.- adonise/modules.php
// 2.- modules-com.php
// 3.- modules-myhost.com.php
// 4.- modules-www.myhost.com.php
include_once A_ROOT_DIR . '/adonise/modules.php';
$domain = array();
foreach ( array_reverse(explode('.',$_SERVER['SERVER_NAME'])) as $item ) {
    array_unshift($domain, $item);
    $path = A_ROOT_DIR . '/modules-' . implode('.',$domain) . '.php';
    if ( is_file($path) ) include $path;
}
$modules = array_reverse($modules);

// A class path ////////////////////////////////////////////////////////////////////////////////////////////////////////
include_once getModuleDirectory('core') . 'lib/A.php';
// Autoload
spl_autoload_register('A::autoload');


// INIT ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$request = A_Request::getSingleton();
$response = A_Response::getSingleton();
$dispatcher = A_Dispatcher::getSingleton();

// ROUTER //////////////////////////////////////////////////////////////////////////////////////////////////////////////
processModules(function($module,$version,$path,$extra){
    /** @var A_Request $request */
    $request = $extra['request'];

    $path .= 'router.php';
    if ( is_file($path) ) {
        include_once $path;
        $class = uri2camelcase($module) . 'Router';
        $router = new $class();
        if ( $router->match($request->getUri(), $request->getParams()) ) return true;
    }
},array(
    'request'   => $request,
));

// PREDISPATCH /////////////////////////////////////////////////////////////////////////////////////////////////////////
// if we have not a database connection data, then add the install-module to the module list
if ( ! defined('DB_NAME') ) {
    $modules['install'] = 'HEAD';

// if we are in admin URL, then enter the admin app
} else if ( isAdmin() ) {
    $modules['admin'] = 'HEAD';
}



$response->addHeadMeta('utf-8', null, 'charset');
$response->addHeadMeta('Content-Type', 'text/html; charset=utf-8', 'http-equiv');
$response->addHeadMeta('X-UA-Compatible', 'IE=edge', 'http-equiv');
$response->addHeadMeta('viewport', 'width=device-width, initial-scale=1');

$response->addHeadCss('bootstrap', getAssetUrl('css/bootstrap.css') );
$response->addHeadCss('bootstrap-theme', getAssetUrl('css/bootstrap-theme.css') );
$response->addHeadCss('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css');
$response->addHeadCss('main', getAssetUrl('css/main.css') );


$response->addHeadScript('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js');
$response->addHeadScript('bootstrap', getAssetUrl('js/bootstrap.js'));
$response->addHeadScript('form.validate', getAssetUrl('js/form/validate.js'));
$response->addHeadScript('form.protect', getAssetUrl('js/form/protect.js'));
$response->addHeadScript('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js');


// Ejecutamos el dispatcher para que procese todos los tokens
$output = $dispatcher->run();

// FIXME: dale una vuelta a esto
$response->addToBody( $output );


// Enviamos cabeceras HTTP, generamos contenido y lo enviamos
$response->output();
