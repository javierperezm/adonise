<?php
class A_Request extends A_Object
{
    static protected $_singleton;

    protected $_uri;

    protected $_module;
    protected $_controller;
    protected $_action;

    public function __construct()
    {
        parent::__construct();

        $this->_uri = array_shift( explode('?',trim(substr($_SERVER['REQUEST_URI'], strlen(A_URI_BASE)),'/'), 2) );
        //$tokens = explode('/', $this->_uri);

        //if ( ! defined('APP_CONTROLLER_DEFAULT') ) define('APP_CONTROLLER_DEFAULT', 'index');
        //if ( ! defined('APP_CONTROLLER_ACTION_DEFAULT') ) define('APP_CONTROLLER_ACTION_DEFAULT', 'index');

        //if ( ! ($this->_controller = array_shift($tokens)) ) $this->_controller = APP_CONTROLLER_DEFAULT;
        //if ( ! ($this->_action = array_shift($tokens)) ) $this->_action = APP_CONTROLLER_ACTION_DEFAULT;

        $this->_data = $_REQUEST;
        //while (count($tokens) > 0) {
        //    $token = array_shift($tokens);
        //    $this->_data[$token] = array_shift($tokens);
        //}
    }

    /**
     * @return A_Request
     */
    static public function getSingleton(){return self::$_singleton ? self::$_singleton : (self::$_singleton = new self());}

    public function setModule($module) { $this->_module = $module; return $this; }
    public function setController($controller) { $this->_controller = $controller; return $this; }
    public function setAction($action) { $this->_action = $action; return $this; }
    public function getModule(){ return $this->_module; }
    public function getController(){ return $this->_controller; }
    public function getAction(){ return $this->_action; }
    public function getUri(){ return $this->_uri; }
    public function getParam($name, $default = null) { return $this->getData($name, $default); }
    public function getParams() { return $this->getData(); }
    public function setParam($name, $value) { $this->setData($name, $value); return $this; }
    public function getPost( $name = null, $default = null ) {
        return $name === null ? $_POST : (isset($_POST[$name]) ? $_POST[$name] : $default);
    }
}