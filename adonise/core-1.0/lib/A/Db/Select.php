<?php
class A_Db_Select
{
    protected $_parts;

    public function __construct()
    {
        $this->_parts = array(
            'from'      => array(),
            'where'     => array(),
        );
    }

    static public function factory()
    {
        return new self();
    }

    public function setFrom()
    {

    }

    public function addWhere( $field, $value )
    {

    }

    public function setLimit()
    {

    }

    public function toSQL()
    {
        $sql = '';
        return $sql;
    }

    public function __toString()
    {
        return $this->toSQL();
    }
}