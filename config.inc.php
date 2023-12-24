<?php
//error_reporting( E_ALL | E_STRICT );
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_COMPILE_ERROR);
ini_set('display_errors','On');
//attiva o disattiva i messaggi di debug
$config = (object)"";
$config->debugger=-1;//1=acceso || 0=spento
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
	'nazione'		=> 'IT',
	'codfiscale'	=> 'BRNGMM79D01E349M',
	'telefono'		=> '',
	'fax'			=> '',
	'email'			=> 'gimmi.brun@gmail.com',
	'pec'			=> 'gimmi.brun@pec.it',
	'website'		=> '',
	'_autoExtend'	=> -1,
);

$config->azienda= new ClienteFornitore($params);
$config->azienda->addProp('_emailpec');
$config->azienda->addProp('_bndoo');
$config->azienda->addProp('_reaufficio');
$config->azienda->addProp('_reanumero');
$config->azienda->addProp('_capitalesociale');
$config->azienda->addProp('_registroimprese');
$config->azienda->addProp('_logo');
$config->azienda->addProp('_logobg');
$config->azienda->addProp('_ragionesocialeestesa');
$config->azienda->addProp('_titolare');
$config->azienda->addProp('_regimefiscale');
$config->azienda->addProp('_sociounicoBolean');
$config->azienda->addProp('_inliquidazioneBolean');


$config->azienda->_bndoo->setVal				('');
$config->azienda->_reaufficio->setVal			('VR');
$config->azienda->_reanumero->setVal			('288164');

$config->azienda->_capitalesociale->setVal		('');
$config->azienda->_registroimprese->setVal		('BRNGMM79D01E349M');
$config->azienda->_logo->setVal					('./dati/brungimmi/logo.png');
$config->azienda->_logobg->setVal				('./dati/brungimmi/logobg.png');
$config->azienda->_ragionesocialeestesa->setVal	('');
$config->azienda->_titolare->setVal				('Brun Gimmi');
$config->azienda->_regimefiscale->setVal('ordinario');

$config->azienda->_sociounicoBolean->setVal(FALSE);
$config->azienda->_inliquidazioneBolean->setVal(FALSE);




/*-------------------------------------
**    PEC
-------------------------------------*/
$config->pec=new stdClass();
$config->pec->Host       = "ssl://smtps.pec.aruba.it"; // SMTP server //ricordarsi di decommentare "extension=php_openssl.dll" nel file php.ini !!! per abilitare l'autenticazione SSL
$config->pec->SMTPDebug  = 2;                     // (0) disattivato (2)enables SMTP debug information (for testing)
$config->pec->SMTPAuth   = true;                  // enable SMTP authentication
$config->pec->Port       = 465;                    // set the SMTP port
$config->pec->Username   = "gimmi.brun@pec.it"; // SMTP account username
$config->pec->Password   = "";        // SMTP account password
$config->pec->From=new stdClass();
$config->pec->From->Mail ='gimmi.brun@pec.it';     //chi invia
$config->pec->From->Name ='Brun Gimmi';     //chi invia
$config->pec->ReplyTo=new stdClass();
$config->pec->ReplyTo->Mail ='gimmi.brun@pec.it';     //chi invia
$config->pec->ReplyTo->Name ='Brun Gimmi';     //chi invia     //chi invia

/*-------------------------------------
**    DATI SDI
-------------------------------------*/
//$config->SDIpec = 'sdi01@pec.fatturapa.it';
//$config->SDIpec = 'gionni.brun@gmail.com';
$config->SDIpec = 'sdi28@pec.fatturapa.it';
?>
