<?php
abstract class A_Block extends A_View
{
    /** @var string Nombre identificador del bloque */
    protected $_name;
    /** @var string Tipo del bloque (module/class_name) */
    protected $_type;

    /** @var array Bloques colgando de este */
    protected $_blocks;
    /** @var A_Block Bloque padre de este */
    protected $_parent;

    static protected $_blocks_data;

    public function __construct( $type = null, $name = null )
    {
        $this->_name = $name;
        $this->_type = $type;

        $this->_blocks = array();
        $this->_parent = null;
    }

    /**
     * Obtiene información de configuración de un tipo de bloque
     *
     * @param $type
     *
     * @return mixed
     */
    static public function getBlockData( $type )
    {
        if ( ! isset(self::$_blocks_data[$type]) ) {
            list($module, $class_name) = explode('/', $type, 2);

            $class = ucfirst($module).'_Block';
            foreach ( explode('_',$type) as $token ) $class .= '_'.ucfirst($token);
            $path = getModuleDirectory($module) . "blocks/".str_replace('_','/',$class_name).'.php';

            self::$_blocks_data[$type] = array(
                'path'  => $path,
                'class' => $class,
                'type'  => $type,
            );
        }

        return self::$_blocks_data[$type];
    }

    /**
     * Instancia un bloque del tipo indicado
     *
     * @param string $type
     * @param string|null $name
     *
     * @return A_Block
     *
     * @throws Exception
     */
    static public function create( $type, $name = null )
    {
        // Obtenemos configuración del tipo de bloque
        $config = A_Block::getBlockData( $type );

        // Cargamos el fichero con la clase
        if ( ! is_file($config['path']) ) {
            throw new Exception('Block file not found! '.$config['path']);
        }
        include_once $config['path'];

        // Comprobamos que exista la clase
        if ( ! class_exists($config['class']) ) {
            throw new Exception('Block class not found! '.$config['class']);
        }

        // Instanciamos un objeto
        /** @var A_Block $block */
        $block = new $config['class']( $type, $name );

        // Comprobamos que la instancia es un A_Block
        if ( ! $block instanceof A_Block ) {
            throw new Exception('Block instance is not a A_Block class! '.$type);
        }

        return $block;
    }

    public function setType( $type )
    {
        $this->_type = $type;
        return $this;
    }
    public function getType() { return $this->_type; }

    /**
     * Establece el nombre-identificador del bloque
     *
     * @param $name
     *
     * @return $this
     */
    public function setName( $name )
    {
        $this->_name = $name;
        return $this;
    }
    public function getName() { return $this->_name; }

    /**
     *
     * @param A_Block $block
     *
     * @return $this
     */
    public function addBlock( A_Block $block )
    {
        $block->parent( $this );

        $this->_blocks[] = $block;

        // Block name check
        if ( $block->getName() ) {
            // Check if block name is used
            // TODO: ...
        } else {
            // Generate block name
            // Get root node
            $root = $this->getRoot();
            // Algoritmo de generación de nombre
            $prefix = strtoupper($block->getType());
            if ( ! $prefix ) $prefix = 'BLOCK';
            $counter = 1;
            do {
                $name = $prefix .'-'. str_pad($counter++, 4, '0', STR_PAD_LEFT);
            } while ( $root->find($name) );
            $block->setName($name);
        }

        return $this;
    }

    /**
     * Establece u obtiene el bloque padre
     *
     * @param A_Block $block
     *
     * @return A_Block
     */
    public function parent( A_Block $block = null )
    {
        if ( $block === null ) return $this->_parent;

        $this->_parent = $block;
        return $this;
    }

    /**
     * Busca un bloque dentro del árbol buscando por el nombre del bloque.
     *
     * @param $name
     * @return A_Block|null
     */
    public function find( $name )
    {
        if ( $this->getData($this->_name) == $name ) {
            return $this;
        }

        /** @var A_Block $block */
        foreach ( $this->_blocks as $block ) {
            if ( ($result = $block->find($name)) !== null ) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Devuelve el bloque root (layout) de este bloque
     *
     * @return A_Block
     */
    public function getRoot()
    {
        $block = $this;
        while ( $block->_parent ) $block = $block->_parent;
        return $block;
    }
}
