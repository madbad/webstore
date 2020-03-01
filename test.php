<?php
include ('./config.inc.php');


//id fatture da trattare
//$arrayId = array(40,41,42,43,44,45,46,47,48,49);
//$arrayId = array(51,52,53,54,55,56);

$arrayId = array(64);


echo '<table style="border:1px solid; width: 100%">';

foreach ($arrayId as $idFT){
	$selectFt = new StdClass();
	$selectFt->id = $idFT; 

	$myFt = new Fattura($selectFt);
	$myFt->getFromDb();
	
	echo '<tr style="border:1px solid;">';
	echo '<td>'.$myFt->numero->getVal().'</td>';
	echo '<td>'.$myFt->data->getVal().'</td>';
	echo '<td>'.$myFt->clientefornitore_codice->getVal().'</td>';
	echo '<td>'.$myFt->totale->getVal().'</td>';
	echo '</tr>';

	//$myFt->generaTempSDIXmlFile();

	error_reporting(0);
	$myFt->inviaSDI();
	//$myFt->visualizzaXml();

}
echo '</table>'

?>
