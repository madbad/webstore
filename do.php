<?php
include ('./config.inc.php');
/*
$myClasses=array('Ddt','Riga','Articolo','Imballaggio','ClienteFornitore','Iva','Causale','Mezzo','Um'); 

foreach ($myClasses as $myClass) {
	$test = new $myClass();
	$test->createDb();

}
*/
echo "\n!!!!!!!!!!!!!!test!!!!!!!!!!!!!!!!";

switch ($_POST["action"]){
	case "getOne":
		$myObj = new $_POST["params"]["_type"]($_POST["params"]);
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
			//$out.="}";
			//$out.= ',';
		});
		//remove the last ","
		$out=substr($out, 0, -1);
		$out.= "]";
		echo $out;
		
		break;
	case "save":
		echo "\n!!!!!!!!!!!!!!decoding!!!!!!!!!!!!!!!!";
print_r($_POST["params"]);
		$params = json_decode($_POST["params"], true);
		echo "\n!!!!!!!!!!!!!!creatin object!!!!!!!!!!!!!!!!";
		$myObj = new $params["_type"]($params);
		print_r($myObj);

		echo "\n!!!!!!!!!!!!!!saving object!!!!!!!!!!!!!!!!";
		$myObj->saveToDb();
		break;

	default:
		break;
}
?>
