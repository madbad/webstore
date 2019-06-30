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

/*
	case "print":
		$_POST["params"]='{"id":"16","fattura_id":"","riga_id":"","numero":"2222","data":"01/01/2001","clientefornitore_codice":"LAFAVO","causale_codice":"01","mezzo_codice":"02","vettore_codice":"","destinatario_codice":"","note":"","righe":"0","_type":"Ddt"}';
		$params = json_decode($_POST["params"], true);
		
		$myObj = new $params["_type"]($params);
		$myObj->getFromDb();
		$myObj->stampa();
		
		break;
*/
	case "print":
		//echo $_POST["params"];
		$params = json_decode($_POST["params"], true);
		$myObj = new $params["_type"]($params);
		
		
		//creo una mylist vuota
		$myddtrighe = new MyList(array( '_type'=>'Riga',
										'ddt_id'=>''
		));
		//print_r($myObj->righe->valore);
		//aggiungo le mie righe
		foreach (($myObj->righe->valore) as $key => $value){
			//echo "\n**".$key.'=>'.$value;
			$myddtrighe->add($value);
			//echo 'test';
		//print_r($myddtrighe);
		}
		$myObj->_oRighe = $myddtrighe;
		
		//print_r($myddtrighe);
		$myObj->stampa();
		break;
	case "emettift":
		//echo $_POST["params"];
		$params = json_decode($_POST["params"], true);
		$myObj = new $params["_type"]($params);
		
		
		//creo una mylist vuota
		$myddtrighe = new MyList(array( '_type'=>'Riga',
										'ddt_id'=>''
		));
		//print_r($myObj->righe->valore);
		//aggiungo le mie righe
		foreach (($myObj->righe->valore) as $key => $value){
			//echo "\n**".$key.'=>'.$value;
			$myddtrighe->add($value);
			//echo 'test';
		//print_r($myddtrighe);
		}
		$myObj->_oRighe = $myddtrighe;
		
		//print_r($myddtrighe);
		$myObj->stampa();
		break;
	default:
		break;
}

?>
