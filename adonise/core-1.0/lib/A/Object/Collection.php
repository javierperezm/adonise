<?php
class A_Object_Collection implements Iterator
{
    private $_position;
    private $_elements;

    /**
     *
     * @param $data
     *
     * @return A_Object_Collection
     */
    static public function factoryArray( $data )
    {
        $objects = array();
        foreach ( $data as $item ) $objects[] = new A_Object($item);
        return new self( $objects );
    }

    public function __construct( $elements = array() )
    {
        $this->_position = 0;
        $this->_elements = $elements;
    }

    public function rewind()
    {
        $this->_position = 0;
    }

    public function current()
    {
        return $this->_elements[$this->_position];
    }

    public function key()
    {
        return $this->_position;
    }

    public function next()
    {
        ++$this->_position;
    }

    public function valid()
    {
        return isset($this->_elements[$this->_position]);
    }

    public function add($element)
    {
        $this->_elements[] = $element;
    }

    public function count()
    {
        return count($this->_elements);
    }
}
