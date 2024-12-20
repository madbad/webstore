<?php
include ('./config.inc.php');


//id fatture da trattare
//$arrayId = array(40,41,42,43,44,45,46,47,48,49);
//$arrayId = array(51,52,53,54,55,56);

$arrayId = array(44);


//echo '<table style="border:1px solid; width: 100%">';

foreach ($arrayId as $idFT){
	$selectFt = new StdClass();
//	$selectFt->id = $idFT; 
//echo $idFT;
//echo $selectFt->id; 
//print_r($myFt);
	$myFt = new Fattura($selectFt);
	$myFt->id->valore = $idFT;
	$myFt->getFromDb();
//print_r($myFt);
/*
	echo '<tr style="border:1px solid;">';
	echo '<td>'.$myFt->numero->getVal().'</td>';
	echo '<td>'.$myFt->data->getVal().'</td>';
	echo '<td>'.$myFt->clientefornitore_codice->getVal().'</td>';
	echo '<td>'.$myFt->totale->getVal().'</td>';
	echo '</tr>';
*/
//	error_reporting(0);
//$myFt->generaTempSDIXmlFile();


	$myFt->inviaSDI();
//	$myFt->visualizzaXml();


/*
	$dati= estrapolaDatiPerXmlFt($myFt);
	generaXmlDaDatiFattura($dati,'./temp.xml');
*/
#	http://192.168.1.110/webContab/my/php/visualizzaFattureXml.php?fileUrl=/webstore/temp.xml
#   http://192.168.1.110/webContab/my/php/visualizzaFattureXml1.php?fileUrl=./../../../webstore/temp.xml

}
//echo '</table>'

?>
