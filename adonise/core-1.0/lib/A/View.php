<?php
class A_View extends A_Object
{
    static protected $_templates_path;

    protected $_template;

    public function __construct( $template = null )
    {
        parent::__construct();

        if ($template !== null) $this->setTemplate($template);
    }

    static public function factory($template = null)
    {
        return new self($template);
    }

    public function setTemplate( $template )
    {
        $this->_template = $template;
    }
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * @throws Exception
     *
     * @return string
     */
    public function __toString()
    {
        return $this->html();
    }

    /**
     * Calcula la ruta a la plantilla según los módulos cargados
     *
     * @return string|null
     */
    protected function _getTemplatePath()
    {
        if ( ! isset(self::$_templates_path[$this->_template]) ) {

            self::$_templates_path[$this->_template] = processModules(function($module, $version, $path, $extra){
                $path .= "views/{$extra['template']}";
                if ( is_file( $path ) ) return $path;
                return null;
            },array(
                'template'  => $this->_template,
            ));

        }

        return self::$_templates_path[$this->_template];
    }

    /**
     * Parse template
     *
     * @throws Exception
     *
     * @return string
     */
    public function html()
    {
        $_view_path = $this->_getTemplatePath();

        if ( $_view_path === null ) {
            // No template available
            throw new Exception('View template not found: '.$this->_template);
        }

        // Parse template
        // TODO: add cache system
        ob_start();
        require $_view_path;
        return ob_get_clean();
    }
}
