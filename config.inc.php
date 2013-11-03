<?php
//error_reporting( E_ALL | E_STRICT );
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_COMPILE_ERROR);
ini_set('display_errors','On');
//attiva o disattiva i messaggi di debug
$config->debugger=0;//1=acceso || 0=spento
error_reporting(-1); //0=spento || -1=acceso
set_time_limit (0); //0=nessun limite di tempo


require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/webstore/classes.php');


//creo l'oggetto che conterrà tutte le configurazioni
$config=new stdClass();
global $config;

/*-------------------------------------
**    sqLite
-------------------------------------*/
$config->sqlite=new stdClass();
$config->sqlite->database =realpath($_SERVER["DOCUMENT_ROOT"]).'/webstore/magazzino.sqlite3';


?>
