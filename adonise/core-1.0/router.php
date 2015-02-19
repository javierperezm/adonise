<?php
class CoreRouter extends A_Router
{
	public function match( $uri, $params )
	{
		$uri = explode('/', $uri);

		$module = array_shift($uri);
		$controller = array_shift($uri);
		$action = array_shift($uri);

		if ( ! $module ) $module = 'core';
		if ( ! $controller ) $controller = 'index';
		if ( ! $action ) $action = 'index';

		$token = new A_Request_Token($module, $controller, $action);
		if ( $token->isExecutable() ) {
			// Añadimos el token al loop del dispatcher
			A_Dispatcher::getSingleton()->addToken( $token );

			// Configuramos adecuadamente el objeto $request
			$request = A_Request::getSingleton();
			$request->setModule( $module );
			$request->setController( $controller );
			$request->setAction( $action );

			while ( count($uri) > 0 ) {
				$var_name = array_shift($uri);
				$var_value = array_shift($uri);

				$request->setParam($var_name, $var_value);
			}
		}








		list($module, $controller, $action, ) = explode('/', $uri);

		$request = A_Request::getSingleton();
		$request->setModule($module ? $module : 'core');
		$request->setController($controller ? $controller : 'index');
		$request->setAction($action ? $action : 'index');

		return false;
	}
}
