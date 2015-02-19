<?php
class A_Dispatcher
{
    protected $_tokens;

    /** @var  A_Request_Token */
    protected $_current_token;

    static protected $_instance;

    public function __construct()
    {
        $this->_tokens = array();
    }

    /**
     *
     * @return A_Dispatcher
     */
    static public function getSingleton()
    {
        return self::$_instance ? self::$_instance : (self::$_instance = new self());
    }

    /**
     *
     * @param A_Request_Token $token
     *
     * @return $this
     */
    public function addToken( A_Request_Token $token )
    {
        $this->_tokens[] = $token;
        return $this;
    }

    /**
     * @return A_Request_Token
     */
    public function getCurrentToken()
    {
        return $this->_current_token;
    }

    public function run()
    {
        while ( count($this->_tokens) > 0 ) {
            /** @var A_Request_Token $token */
            $this->_current_token = array_shift($this->_tokens);

            $response = $this->_current_token->run();
        }

        return $response;
    }
}