<?php
class A_Response
{
    const DEVELOPMENT_MODE = true;

    static protected $_singleton;

    protected $_headers;
    protected $_head;
    protected $_body;

    public function __construct()
    {
        $this->_headers = array(
            'Content-Type'  => 'text/html; charset=utf-8',
        );

        $this->_head = array();

        $this->_body = '';
    }

    /**
     * @return A_Response
     */
    static public function getSingleton()
    {
        return self::$_singleton ? self::$_singleton : (self::$_singleton = new self());
    }

    public function addToBody( $content )
    {
        $this->_body .= $content;
        return $this;
    }

    public function addHeadMeta($name, $content, $type = 'name')
    {
        $this->_head['meta'][$name] = array(
            'content'   => $content,
            'type'      => $type,
        );
    }

    public function addHeadScript($name, $url)
    {
        if ( self::DEVELOPMENT_MODE ) {
            $separator = strpos($url, '?') === false ? '?' : '&';
            $url .= $separator . 'ts=' . time();
        }

        $this->_head['script'][$name] = array(
            'url'   => $url,
        );
    }

    public function addHeadCss($name, $url, $media = 'all')
    {
        if ( self::DEVELOPMENT_MODE ) {
            $separator = strpos($url, '?') === false ? '?' : '&';
            $url .= $separator . 'ts=' . time();
        }

        $this->_head['css'][$name] = array(
            'url'   => $url,
            'media' => $media,
        );
        return $this;
    }

    public function removeHeadCss($name)
    {
        unset( $this->_head['css'][$name] );
        return $this;
    }

    public function getHeadMeta()
    {
        return $this->_head['meta'];
    }
    public function getHeadScript()
    {
        return $this->_head['script'];
    }
    public function getHeadCss()
    {
        return $this->_head['css'];
    }

    public function addHeader($name, $value)
    {
        $this->_headers[$name] = $value;
        return $this;
    }

    public function sendHeaders()
    {
        foreach ( $this->_headers as $hname => $hvalue ) {
            header( "$hname: $hvalue" );
        }
    }

    public function output()
    {
        // Enviamos cabeceras
        $this->sendHeaders();

        // TODO: ...
        /** @var A_Block $layout */
        $layout = A::registry('layout');

        // Añadimos contenido al final del <body/>
        /** @var A_Block_Text $body_end */
        if ( $body_end = $layout->find('body_end') ) {
            $body_end->setText( $this->_body );
        }

        // Parseamos todos los bloques desde su ROOT (layout)
        echo $layout->html();
    }

    public function redirect($url)
    {
        header('Location: '.$url);
        exit();
    }
}