<?php
class A_Db_Model extends A_Object
{
    protected $_db;
    protected $_res;

    protected $_id_field;
    protected $_table_name;

    protected $_origData;

    /* Models data :: datos de cada tipo de modelo */
    static protected $_models = array();

    public function __construct()
    {
        parent::__construct();

        $this->_db = A_Db::getSingleton();

        // Nombre de la tabla
        if ( ! $this->_table_name ) {
            $this->_table_name = strtolower(substr(get_class(),0,strlen(get_class())-6));
        }

        // PKs
        if ( ! $this->_id_field ) {
            $this->_id_field = 'ID';
        }
    }

    public function getId()
    {
        if ( is_string($this->_id_field) ) {
            return $this->getData($this->_id_field);
        }

        return null;
    }

    public function load( $id, $fieldname = null )
    {
        $select = A_Db_Select::factory();

        $select->setFrom( $this->_table_name );

        if ( $fieldname === null ) {
            $fieldname = $this->_id_field;
        }

        if ( is_array($fieldname) ) {
            // TODO
        } else {
            $select->addWhere( $fieldname, $id );
        }

        $select->setLimit( 1 );

        $this->_query($select);

        $this->_loadBefore();
        $this->_data = $this->_origData = $this->_fetch();
        $this->_loadAfter();

        return $this;
    }
    protected function _loadBefore(){}
    protected function _loadAfter(){}


    /**
     * @return App_Model_Collection
     */
    public function getCollectionModel()
    {
        $collection_model_classname = get_class($this) . '_Collection';
        $path = explode('_', strtolower($collection_model_classname));
        array_shift($path);
        $path = APP_ROOT_DIR . '/models/' . implode('/',$path) . '.php';
        if ( is_file($path) ) {
            include_once $path;
            return new $collection_model_classname();
        }
        return null;
    }

    protected function _query( $sql )
    {
        $this->_res = $this->_db->query( $sql );
    }

    protected function _fetch()
    {
        return $this->_db->fetch( $this->_res );
    }

    public function save()
    {
        // 1. Filtrar por los campos que efectivamente han sido modificados (comparar con _origData)
        // 2. Filtrar por los campos de la tabla (obtener listado cacheado de campos de la tabla)
        // 3. Hacer insert/update según si hay identificador o no
    }
    protected function _saveBefore(){}
    protected function _saveAfter(){}

    static public function loadModelData($type)
    {
        if ( ! isset(self::$_models[$type]) ) {

            // type = core/session
            // class = Core_Model_Session
            // path = /adonise/modules/core-1.0/models/session.php

            list($module_name, $model_name) = explode('/', $type, 2);

            $path = getModuleDirectory($module_name) . "models/".str_replace('_','/',$module_name).'.php';
            $class = ucfirst(strtolower($module_name)) . '_Model';
            $tokens = explode('_', $model_name);
            foreach ( $tokens as $token ) $class .= '_' . ucfirst(strtolower($token));

            self::$_models[$type] = array(
                'type'  => $type,
                'class' => $class,
                'path'  => $path,
            );
        }

        return self::$_models[$type];
    }

    /**
     * Rewrite model type
     *
     * @param $from_type    core/session
     * @param $to_type      my_module/my_model_session
     */
    static public function rewrite($from_type, $to_type)
    {
        $old_type_data = A_Db_Model::loadModelData($from_type);
        self::$_models[$from_type] = A_Db_Model::loadModelData($to_type);
        self::$_models[$from_type]['rewrite'] = $old_type_data;
    }
}