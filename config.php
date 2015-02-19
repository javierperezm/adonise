<?php
/* Generar la URL completa para los enlaces a los contenidos media */
define('A_MEDIA_FULL_URL', true);

/* Usar index.php en la URL o usar mod-rewrite */
//define('A_SCRIPT_FILE', 'index.php'); // http://host.com/index.php/index/index/index
define('A_SCRIPT_FILE', ''); // mod-rewrite: http://host.com/index/index/index

define('DB_NAME', 'adonise');
define('DB_USER', 'adonise');
define('DB_PASSWORD', 'adonise');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
