<?php
final class A
{
    static protected $_registry;

    static public function getModel($type)
    {
        // Convertimos el tipo a nombre de clase y obtenemos ruta
        $data = A_Db_Model::loadModelData($type);

        if ( ! class_exists($data['class']) ) {
            include_once $data['path'];
        }

        $model = new $data['class']();

        return $model;
    }

    static public function register( $name, $value )
    {
        self::$_registry[$name] = $value;
    }

    static public function registry( $name, $default = null )
    {
        return isset(self::$_registry[$name]) ? self::$_registry[$name] : $default;
    }

    static public function event()
    {

    }

    static public function triggerEvent()
    {

    }

    static public function hookEvent()
    {

    }

    static public function parseEvent()
    {

    }

    static public function helper()
    {

    }

    static public function autoload( $class )
    {
        global $modules;

        // Libraries
        $class_path = str_replace('_','/',$class);
        foreach ( $modules as $module_name => $version ) {
            $path = A_ROOT_DIR . "/adonise/modules/{$module_name}-{$version}/lib/{$class_path}.php";
            if ( is_file($path) ) {
                include_once $path;
                if ( class_exists($class) ) {
                    return;
                }
            }
        }
    }
}
