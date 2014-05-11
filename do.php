<?php
include ('./config.inc.php');
/*
$myClasses=array('Ddt','Riga','Articolo','Imballaggio','ClienteFornitore','Iva','Causale','Mezzo','Um'); 

foreach ($myClasses as $myClass) {
	$test = new $myClass();
	$test->createDb();

}
*/
//echo "\n!!!!!!!!!!!!!!test!!!!!!!!!!!!!!!!";
/*
$mytest= new Mezzo(array(codice=>'01'));
$mytest->getFromDb();
echo '<br>'.$mytest->toJson();
*/




switch ($_POST["action"]){
	case "getOne":
		$myObj = new $_POST["params"]["_type"]($_POST["params"]);
		$myObj->getFromDb();
		echo $myObj->toJson();
		break;
	case "getAll":
		$out="[";
		$myObjList = new MyList($_POST["params"]);
		//echo $myObjList->toJson();
		
		$myObjList->iterate(function ($myObj){
			global $out;
			$out.="{";
			$out.= $myObj->toJson(1);
		});
		//remove the last ","
		$out=substr($out, 0, -1);
		$out.= "]";
		echo $out;
		
		break;
	case "save":
		//echo "\n!!!!!!!!!!!!!!decoding!!!!!!!!!!!!!!!!";
		$params = json_decode($_POST["params"], true);
		//echo "\n!!!!!!!!!!!!!!creatin object!!!!!!!!!!!!!!!!";
		$myObj = new $params["_type"]($params);
		//echo "\n!!!!!!!!!!!!!!saving object!!!!!!!!!!!!!!!!";
		$myObj->saveToDb();
		break;
	case "saveAll":
		break;
	case "deletteDdt":
		$myddt= new Ddt(array('numero'=>'1936','data'=>'16/11/2013'));
		$myddt->getFromDb();
		$myddtBACKUP = $myddt->toJson();

		$myddtrighe = new MyList(array( '_type'=>'Riga',
										'ddt_numero'=>$myddt->numero->getVal(),
										'ddt_data'=>$myddt->data->getVal()));

		$myddtRigheBACKUP = array();								
		$myddtrighe->iterate(function($riga){
			global $myddtRigheBACKUP;
			$myddtRigheBACKUP[] = $riga->toJson();
			$riga->deletteFromDb();
		});
		$myddt->deletteFromDb();

		echo '<br>BackupDDT:<br>';
		echo $myddtBACKUP;

		echo '<br>BackupRigheDDT:<br>';
		break;
	case "deletteAll":
		break;

	default:
		break;
}

?>
