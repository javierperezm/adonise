<?php
class A_Block_Text extends A_Block
{
    protected $_text;

    public function setText( $text )
    {
        $this->_text = $text;
        return $this;
    }

    public function html()
    {
        return $this->_text;
    }
}