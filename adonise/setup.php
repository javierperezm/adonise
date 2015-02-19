<?php
set_time_limit(0);

define('A_ROOT_DIR', realpath(dirname(__FILE__).'/..'));
define('A_ROOT_URI', substr(A_ROOT_DIR, strlen(realpath($_SERVER['DOCUMENT_ROOT']))));

define('A_MEDIA_DIR', realpath(A_ROOT_DIR.'/media'));
define('A_MEDIA_URI', A_ROOT_URI.'/media');

$config_file_path = A_ROOT_DIR . '/config.php';
if ( ! is_file($config_file_path) ) {
    define('A_MODE', 'INSTALL');
} else {
    define('A_MODE', 'RUN');
    include_once $config_file_path;

}

defined('A_FULL_URL') || define('A_FULL_URL', true);
defined('A_MEDIA_FULL_URL') || define('A_MEDIA_FULL_URL', true);
defined('A_ASSET_FULL_URL') || define('A_ASSET_FULL_URL', true);
defined('A_SCRIPT_FILE') || define('A_SCRIPT_FILE','index.php');

define('A_URI_BASE', rtrim(A_ROOT_URI . '/' . A_SCRIPT_FILE, '/'));

// MODULES CONFIG //////////////////////////////////////////////////////////////////////////////////////////////////////
// Cargamos todos los módulos de Adonise
include_once 'modules.php';
// Cargamos los módulos de cada host
$domain = array();
foreach ( array_reverse(explode('.',$_SERVER['SERVER_NAME'])) as $item ) {
    array_unshift($domain, $item);
    $path = A_ROOT_DIR . '/modules-' . implode('.',$domain) . '.php';
    if ( is_file($path) ) include $path;
}
$modules = array_reverse($modules);

// FUNCTIONS ///////////////////////////////////////////////////////////////////////////////////////////////////////////
include_once 'functions.php';

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

// Ejecutamos el dispatcher para que procese todos los tokens
$output = $dispatcher->run();

// FIXME: dale una vuelta a esto
$response->addToBody( $output );

// Enviamos cabeceras HTTP, generamos contenido y lo enviamos
$response->output();














$request = App_Request::getSingleton();
$response = App_Response::getSingleton();
$dispatcher = App_Dispatcher::getSingleton();

$response->addHeader('X-Turpentine-Cache', '0');

$response->addHeadMeta('utf-8', null, 'charset');
$response->addHeadMeta('Content-Type', 'text/html; charset=utf-8', 'http-equiv');
$response->addHeadMeta('X-UA-Compatible', 'IE=edge', 'http-equiv');
$response->addHeadMeta('viewport', 'width=device-width, initial-scale=1');
//$response->addHeadMeta('ZF2_SERVICES_URL', ZF2_SERVICES_URL);

$response->addHeadCss('bootstrap', getAssetUrl('css/bootstrap.css') );
$response->addHeadCss('bootstrap-theme', getAssetUrl('css/bootstrap-theme.css') );
$response->addHeadCss('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css');
$response->addHeadCss('main', getAssetUrl('css/main.css') );


$response->addHeadScript('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js');
$response->addHeadScript('bootstrap', getAssetUrl('js/bootstrap.js'));
$response->addHeadScript('form.validate', getAssetUrl('js/form/validate.js'));
$response->addHeadScript('form.protect', getAssetUrl('js/form/protect.js'));
$response->addHeadScript('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js');

// Bloque desde el cual colgarán todos los demás
$ROOT = A_Block::create('core/root');

$page = new App_Block( $request, $response );
$page->setTemplate('page');
$page->setTitle('App');

$token = $dispatcher->route();
$token->getObject()->setPageView( $page );
