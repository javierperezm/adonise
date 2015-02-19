<?php
class A_Object_Tree extends A_Object
{
	protected $_name_field = 'name';

	protected $_children;
	protected $_parent;

	/**
	 * Añade un objeto a este nodo
	 *
	 * @param A_Object_Tree $object
	 * @return $this
	 */
	public function add( A_Object_Tree $object )
	{
		// El padre del objeto es este objeto
		$object->parent( $this );

		// Añadimos el objeto a este nodo
		$this->_children[] = $object;

		return $this;
	}

	/**
	 * Establece u obtiene el objeto padre
	 *
	 * @param A_Object_Tree $object
	 *
	 * @return A_Object_Tree
	 */
	public function parent( A_Object_Tree $object = null )
	{
		if ( $object === null ) return $this->_parent;

		$this->_parent = $object;
		return $this;
	}

	/**
	 * Busca un objeto dentro del árbol buscando por el nombre del objeto.
	 *
	 * @param $name
	 * @return A_Object_Tree|null
	 */
	public function find( $name )
	{
		if ( $this->getData($this->_name_field) == $name ) {
			return $this;
		}

		/** @var A_Object_Tree $object */
		foreach ( $this->_children as $object ) {
			if ( ($result = $object->find($name)) !== null ) {
				return $result;
			}
		}

		return null;
	}
}