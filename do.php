<?php
include ('./config.inc.php');
//print_r($_POST);
//print_r($_GET);

//todo:temp workaroud to be fixed

if(array_key_exists("action",$_GET)){
	$_POST=$_GET;
}

switch ($_POST["action"]){
	case "getOne":
		$myparams = json_decode($_POST["params"], true);
		$myObj = new $myparams["_type"]($myparams);
		$myObj->getFromDb();
		echo $myObj->toJson();
		break;
		
	case "getAll":
		$out="[";
		$myparams = json_decode($_POST["params"], true);
		//print_r($myparams);
		$myObjList = new MyList($myparams);
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
		echo $_POST["params"];
		$params = json_decode($_POST["params"], true);
		//echo get_object_vars(json_decode$params));
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

		break;

	case "visuallizzapdf":
		//echo $_POST["params"];
		$params = json_decode($_POST["params"], true);
		$myObj = new $params["_type"]($params);
		$myObj->visualizzaPdf();
		break;

	case "stampa":
		//echo $_POST["params"];
		$params = json_decode($_POST["params"], true);
		$myObj = new $params["_type"]($params);

		$myObj->stampa();

		break;

	default:
		break;
}

?>
