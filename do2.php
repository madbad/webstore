<?php
include ('./config.inc.php');
print_r($_POST);
//print_r($_GET);

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
		$params = json_decode($_POST["params"], true);
		$myObj = new $params["_type"]($params);
		$myObj->saveToDb();
		break;
	case "saveAll": /*untested*/
		$objList =  json_decode($_POST["params"], true);
		foreach ($objList as  $obj){
			$myObj = new $obj["_type"]($obj);
			$myObj->saveToDb();
		}
		break;

	case "delette":
		$params = json_decode($_POST["params"], true);
		$myObj = new $params["_type"]($params);
		$myObj->deletteFromDb();

		/*
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
		*/
		break;
	default:
		break;
}

?>
