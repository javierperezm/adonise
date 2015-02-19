<?php

/**
 * Class A_Request_Token
 *
 * @method A_Request_Token setModule( string )
 * @method A_Request_Token setController( string )
 * @method A_Request_Token setAction( string )
 *
 * @method string getModule()
 * @method string getController()
 * @method string getAction()
 */
class A_Request_Token extends A_Object
{
	/** @var A_Controller_Action */
	protected $_object;

	public function __construct( $module, $controller, $action )
	{
		$this->setModule( $module );
		$this->setController( $controller );
		$this->setAction( $action );
	}

	public function getObject()
	{
		if ( $this->_object === null ) {

			$this->_object = false;

			$path = getModuleDirectory( $this->getModule() ) . "controllers/" . uri2camelcase( $this->getController() ) . 'Controller.php';
			$class = uri2camelcase( $this->getModule() ) . '_Controller_' . uri2camelcase( $this->getController() );

			if ( is_file( $path ) ) {
				include_once $path;
				if ( class_exists( $class ) ) {
					$object = new $class();
					if ( $object instanceof A_Controller_Action ) {
						$this->_object = $object;
					}
				}
			}
		}

		return $this->_object;
	}

	public function getMethod()
	{
		$method = uri2camelcase( $this->getAction() ) . 'Action';
		$method[0] = strtolower( $method[0] );

		return $method;
	}

	public function isExecutable()
	{
		return ($object = $this->getObject()) !== false && method_exists($object,$this->getMethod()) && is_callable(array($object,$this->getMethod()));
	}

	public function run()
	{
		if ( $this->isExecutable() ) {
			$method = $this->getMethod();
			return $this->getObject()->$method();
		}

		return null;
	}
}
