<?php
//error_reporting( E_ALL | E_STRICT );
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_COMPILE_ERROR);
ini_set('display_errors','On');
//attiva o disattiva i messaggi di debug
$config = (object)"";
$config->debugger=0;//1=acceso || 0=spento
error_reporting(0); //0=spento || -1=acceso
set_time_limit (0); //0=nessun limite di tempo


require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/webstore/classes.php');


//creo l'oggetto che conterrà tutte le configurazioni
$config=new stdClass();
global $config;

/*-------------------------------------
**    sqLite
-------------------------------------*/
$config->sqlite=new stdClass();
$config->sqlite->databaseInterno =realpath($_SERVER["DOCUMENT_ROOT"]).'/webstore/dati/anagrafiche.sqlite3';
$config->sqlite->databaseDitta =realpath($_SERVER["DOCUMENT_ROOT"]).'/webstore/dati/ditta_brungimmi.sqlite3';

$GLOBALS['config']->pdfDir =realpath($_SERVER["DOCUMENT_ROOT"]).'/webstore/pdf';

/*-------------------------------------
**    datiDitta
-------------------------------------*/

$params= Array(
	'ragionesociale'=> 'Brun Gimmi',
	'via'			=> 'Valle n.3',
	'paese'			=> 'Isola della Scala',
	'provincia'		=> 'VR',
	'cap'			=> '37063',
	'piva'			=> '02844920237',
	'sigla_paese'	=> 'IT',
	'codfiscale'	=> 'BRNGMM79D01E349M',
	'telefono'		=> '335-5956258',
	'fax'			=> '',
	'email'			=> 'gimmi.brun@gmail.com',
//	'emailpec'		=> 'lafavorita_srl@pec.it',
	'website'		=> '',
	'_autoExtend'	=> -1,
);

$config->azienda= new ClienteFornitore($params);
$config->azienda->addProp('_emailpec');
$config->azienda->addProp('_bndoo');
$config->azienda->addProp('_rea');
$config->azienda->addProp('_capitalesociale');
$config->azienda->addProp('_registroimprese');
$config->azienda->addProp('_logo');
$config->azienda->addProp('_logobg');
$config->azienda->addProp('_ragionesocialeestesa');
$config->azienda->addProp('_titolare');

$config->azienda->_emailpec->setVal				('gimmi.brun@pec.it');
$config->azienda->_bndoo->setVal				('');
$config->azienda->_rea->setVal					('VR-288164');
$config->azienda->_capitalesociale->setVal		('');
$config->azienda->_registroimprese->setVal		('VR-1998-25814');
$config->azienda->_logo->setVal					('./dati/brungimmi/logo.png');
$config->azienda->_logobg->setVal				('./dati/brungimmi/logobg.png');
$config->azienda->_ragionesocialeestesa->setVal	('');
$config->azienda->_titolare->setVal				('Brun Gimmi');


?>
