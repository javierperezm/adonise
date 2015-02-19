<?php

/**
 * Class App_Session
 *
 */
class App_Session extends App_Object
{
    protected $_scope;

    public function __construct($scope)
    {
        // Inicializamos el gestor de sesiones
        App_Session_Handler::getSingleton();

        $this->_scope = $scope;
    }

    public function getFormKeyInput()
    {
        return App_Form_Element_Hidden::factory('form_key', $this->getFormKey());
    }

    public function getFormKey()
    {
        if ( ! $this->getData('form_key') ) {
            $this->setData('form_key', sha1('www.andelux.es'.microtime(true)));
        }
        return $this->getData('form_key');
    }

    public function checkFormKey()
    {
        $form_key_param = App_Request::getSingleton()->getParam('form_key');

        if ( $this->getFormKey() == $form_key_param ) {
            $this->unsetData('form_key');
            return true;
        }

        return false;
    }

    /**
     *
     * @param $scope
     *
     * @return App_Session
     */
    static public function factory($scope)
    {
        return new self($scope);
    }

    public function setData($name, $value=null)
    {
        if ( is_array($name) ) {
            $_SESSION[$this->_scope] = array_merge($_SESSION[$this->_scope], $name);
        } else {
            $_SESSION[$this->_scope][$name] = $value;
        }

        return $this;
    }
    public function getData($name = null, $default = null)
    {
        if ( $name === null ) return $_SESSION[$this->_scope];
        return isset($_SESSION[$this->_scope][$name]) ? $_SESSION[$this->_scope][$name] : $default;
    }
    public function unsetData($name)
    {
        unset($_SESSION[$this->_scope][$name]);
    }
}
