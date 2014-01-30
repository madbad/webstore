<?php
include ('./config.inc.php');
echo 'reached this';
$jsonTxt = implode('', file('./dati/anagrafica.json'));
echo $jsonTxt;

$mylist = json_decode($jsonTxt, true);
print_r($mylist);

foreach ($mylist as $listNum => $cliente){
	$myObj = new Clientefornitore($cliente);
	$myObj->saveToDb();
}
?>
