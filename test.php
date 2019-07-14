<?php
include ('./config.inc.php');

$selectFt = new StdClass();
$selectFt->id = 39; 

$myFt = new Fattura($selectFt);
$myFt->getFromDb();

//$myFt->generaTempSDIXmlFile();

//$myFt->inviaSDI();
//$myFt->visualizzaXml();
?>
