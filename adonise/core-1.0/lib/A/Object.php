<?php
class A_Object
{
    protected $_data;

    public function __construct( $data = array() )
    {
        $this->_data = $data;

        $this->_construct();
    }

    protected  function _construct()
    {

    }

    public function __set($name, $value)
    {
        $this->setData($name, $value);
        return $this;
    }
    public function __get($name)
    {
        return $this->getData($name);
    }
    public function __unset($name)
    {
        $this->unsetData($name);
    }

    public function setData($name, $value=null)
    {
        if ( is_array($name) ) {
            $this->_data = $this->_data ? array_merge($this->_data, $name) : $name;
        } else {
            $this->_data[$name] = $value;
        }

        return $this;
    }
    public function getData($name = null, $default = null)
    {
        if ( $name === null ) return $this->_data;
        return isset($this->_data[$name]) ? $this->_data[$name] : $default;
    }
    public function unsetData($name)
    {
        unset($this->_data[$name]);
    }
    public function __call($method, $args)
    {
        if ( substr($method,0,3) == 'set' ) {
            $varname = camelcase2varname(substr($method,3));
            $this->setData($varname, $args[0]);

        } else if ( substr($method,0,3) == 'get' ) {
            $varname = camelcase2varname(substr($method,3));
            return $this->getData($varname, count($args)>0 ? $args[0] : null);

        } else if ( substr($method,0,5) == 'value' ) {
            $varname = camelcase2varname(substr($method,5));

            list($key, $default, ) = $args;
            $data = $this->getData($varname, $default);
            return $data[$key];

        } else if ( substr($method,0,5) == 'unset' ) {
            $varname = camelcase2varname(substr($method,5));
            $value = $this->getData($varname, count($args)>0 ? $args[0] : null);
            $this->unsetData($varname);
            return $value;

        } else if ( substr($method,0,3) == 'add' ) {
            $varname = camelcase2varname(substr($method,3));
            $data = $this->getData($varname, array());
            $data[] = $args[0];
            $this->setData($varname, $data);
            
        }

        return $this;
    }

}
