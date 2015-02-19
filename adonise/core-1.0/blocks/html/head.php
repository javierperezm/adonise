<?php
class Core_Block_Html_Head extends A_Block_Template
{
    protected $_heads;

    public function __construct()
    {
        $this->_heads = array();
    }

    public function addHeadMeta($name, $content, $type = 'name')
    {
        $this->_heads['meta'][$name] = array(
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

        $this->_heads['script'][$name] = array(
            'url'   => $url,
        );
    }

    public function addHeadCss($name, $url, $media = 'all')
    {
        if ( self::DEVELOPMENT_MODE ) {
            $separator = strpos($url, '?') === false ? '?' : '&';
            $url .= $separator . 'ts=' . time();
        }

        $this->_heads['css'][$name] = array(
            'url'   => $url,
            'media' => $media,
        );
        return $this;
    }

    public function removeHeadCss($name)
    {
        unset( $this->_heads['css'][$name] );
        return $this;
    }

    public function getHeadMeta()
    {
        return $this->_heads['meta'];
    }
    public function getHeadScript()
    {
        return $this->_heads['script'];
    }
    public function getHeadCss()
    {
        return $this->_heads['css'];
    }

}