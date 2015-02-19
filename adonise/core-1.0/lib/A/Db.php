<?php
class A_Db extends mysqli
{
    /** @var mysqli_result */
    protected $_last_result;

    static protected $_instance;
    static protected $_tables;

    /**
     * @return A_Db
     */
    static public function getSingleton()
    {
        return self::$_instance === null ? (self::$_instance = new self()) : self::$_instance;
    }

    public function __construct()
    {
        parent::__construct( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

        if ( $this->connect_errno > 0 ) {
            throw new Exception("Unable to connect to database: {$this->connect_error}");
        }

        register_shutdown_function(array($this,'shutdown'));
    }

    public function query( $sql )
    {
        $this->_last_result = parent::query( $sql );
        if ( ! $this->_last_result ) {
            throw new Exception("Query error: {$this->error}");
        }
        return $this->_last_result;
    }

    public function fetch( $res = null )
    {
        if ( $res === null ) $res = $this->_last_result;

        $data = $res->fetch_assoc();

        return $data;
    }

    public function insert( $table, $data )
    {
        $struct = $this->getTableDescription($table);

        $fields = implode(',',array_keys($data));
        $queries = implode(',',array_fill(0,count($fields),'?'));
        $types = '';

        $stm = $this->prepare("INSERT INTO `{$table}` ({$fields}) VALUES ({$queries})");

        foreach ( $data as $field => $value ) {
            $types .= $struct[$field]['type_id'];
        }
        $params = array_values($data);
        array_unshift($params, $types);
        call_user_func_array(array($stm,'bind_param'), $params);

    }

    public function free( $res = null )
    {
        if ( $res === null ) $res = $this->_last_result;
        $res->free();
    }

    public function shutdown()
    {
        $this->close();
    }

    public function getTableDescription( $table )
    {
        if ( ! isset(self::$_tables[$table]) ) {
            self::$_tables[$table] = null;
            $res = parent::query( "SHOW COLUMNS FROM `{$table}`;" );
            if ( $res ) {
                while ( $row = $res->fetch_assoc() ) {
                    $field = $row['Field'];
                    $type = $row['Type'];
                    if ( preg_match('/^(bigint|int)\([0-9]\)$/i', $type) ) {
                        $type_id = 'i';
                    } else if ( preg_match('/^(varchar|char)\([0-9]\)$/i', $type) ) {
                        $type_id = 's';
                    } else if ( preg_match('/^(text|longtext)$/i', $type) ) {
                        $type_id = 's';
                    } else if ( preg_match('/^(blob)$/i', $type) ) {
                        $type_id = 'b';
                    } else {
                        $type_id = '?';
                    }
                    self::$_tables[$table][$field] = array(
                        'field'         => $row['Field'],
                        'type'          => $row['Type'],
                        'type_id'       => $type_id,
                        'key'           => $row['Key'],
                        'default'       => $row['Default'],
                        'index'         => strpos($row['Key'], 'MUL') !== false,
                        'unique'        => strpos($row['Key'], 'UNI') !== false,
                        'primary'       => strpos($row['Key'], 'PRI') !== false,
                        'autoincrement' => strpos($row['Extra'], 'auto_increment') !== false,
                        'null'          => $row['Null'] == 'YES',
                    );
                }
            }
        }

        return self::$_tables[$table];
    }
}