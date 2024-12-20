<?php
include('./utils.php');
include('./print.ddt.php');
include('./xml.fattura.php');
//genera pdf
include('./libs/tcpdf/tcpdf.php');
//classe per l'invio di email
require_once('./libs/phpmailer/class.phpmailer.php');


//require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/libs/tcpdf/config/lang/ita.php');
require_once('./libs/tcpdf/tcpdf.php');
// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	//Page header
	public function Header() {
		// full background image
		// store current auto-page-break status
		$bMargin = $this->getBreakMargin();
		$auto_page_break = $this->AutoPageBreak;
		$this->SetAutoPageBreak(false, 0);
//		$this->Image($GLOBALS['img_file'], 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
		// restore auto-page-break status
		$this->SetAutoPageBreak($auto_page_break, $bMargin);
	}
}



/*to fix anche il tipo andrebbe inserito nelle primary key delle fatture?? se una fattura e una nota credito hanno stesso $NUMERO e $DATA cosa succede??*/



//require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'//webContab/my/php/libs/FirePHPCore/FirePHP.class.php');
//classe per l'invio di email
//require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'//webContab/my/php/libs/phpmailer/class.phpmailer.php');

class DefaultClass {
	public function __call($method, $args)     {
		if (isset($this->$method)) {
			$func = $this->$method;
			if(is_callable($func)){
				return $func($this);
			}
		}
	}
}

class MyClass extends DefaultClass{
//la mia classe di base con propriet� e metodi aggiuntivi
	public function addProp($nome, $validatore=null) {
		$this->$nome=new Propriet�($nome, $validatore, $this);
		return $this;
	}
	public function getName(){
		return get_class($this);
	}
	public function getDbName(){
		return get_class($this);
	}
	public function getPropertiesNames(){
		$props = get_object_vars($this);
		$out=array();
		foreach ($props as $prop){
			//print_r($prop);
			if(@$prop->nome!=''){
				$out[]= $prop->nome;
			}
		}
		return $out;
	}
	public function mergeParams($params){
		//importo eventuali valori delle propriet� che mi sono passato come $params nell'oggetto principale
		$this->_params=$params;

		if (!is_array($params)){return;}//nessun paramestro di cui fare il merge}
		//else
		foreach ($params as $key => $value){
			//se si tratta delle righe devo prepararle in modo particolare
			if($key=='riga_id'){
				//creo gli oggetti riga
				if(is_array($value)){
					//se il campo righe � gia un array allora significa che contiene gi� tutti i dati che mi servono in formato json quindi lo lascio com� e lo trasformo in un oggetto riga
					$righe = $value;
					$value= array();
					
					foreach ($righe as $rkey => $rvalue ){
						$riga = new Riga($rvalue);
						//value diventa il mio array di oggetti Riga
						$value[]= $riga;
					}
				}else{
					//altrimenti significa che � solo una stringa di numeri che si riferiscono alle mie righe quindi devo ricavarmi i dati dalle righe... 
					//oppure lascio semplicmente perdere e lascio il valore cos� com'�
					$value=$value;
				}

			//print_r($value);
			}
			if($key=='ddt_id'){
				//creo gli oggetti riga
				if(is_array($value)){
					//se il campo righe � gia un array allora significa che contiene gi� tutti i dati che mi servono in formato json quindi lo lascio com� e lo trasformo in un oggetto riga
					$righe = $value;
					$value= array();
					
					foreach ($righe as $rkey => $rvalue ){
						$riga = new Ddt($rvalue);
						//value diventa il mio array di oggetti Riga
						$value[]= $riga;
					}
				}else{
					//altrimenti significa che � solo una stringa di numeri che si riferiscono alle mie righe quindi devo ricavarmi i dati dalle righe... 
					//oppure lascio semplicmente perdere e lascio il valore cos� com'�
					$value=$value;
				}

			//print_r($value);
			}
			
			if(property_exists($this,$key)){
				if($key[0]!='_' && method_exists($this->$key, 'setVal')){
					$this->$key->setVal($value);
				}
			}
		}
	}
	public function toJson($subRun=0){
		//imposto il nome dell'oggetto
		if(!$subRun){
			$out='"'.strtolower(get_class($this)).'":{';
			//aggiungo una propriet� ad uso interno che descrive il tipo di oggetto
			$out.='"_type":"'.strtolower(get_class($this)).'",';
		}else{
			$out='';
		}

		foreach($this as $key => $value) {
			//se la propriet� � un oggetto (ovvero l'ho definita io come oggetto) provo ad estenderla
			if(is_object($this->$key)){
//				$extendedObj=$this->$key->extend();
//				if ($extendedObj){
//					//se si stende chiamo il metodo json del suo oggetto
//					//echo "estendo $key<br>";
//					$out.='"'.$key.'":{';
//					$out.='"_type":"'.strtolower(get_class($extendedObj)).'",';
//					$out.=$extendedObj->toJson(1);
//				}else{
					//altrimento si tratta di una semplice propriet� e la converto io in json
					if($key[0]!='_'){
						$val=$this->$key->getVal();
						
						//se il valore contiene delle " devo convertirle in \" in quanto json altrimenti va in conflitto
						$val = str_replace('"', '\"', $val);
						/**/
						
						$out.='"'.$key.'":"'.$val.'",';
					}
//				}
			}

			//se invece � una propriet�
			if(is_array($this->$key) && $key[0]!='_'){
				$out.='"'.$key.'"'.': [';
				foreach ($this->$key as $subKey => $subValue){
					//se si stende chiamo il metodo json del suo oggetto
					//echo "estendo $key<br>";
					//$out.='"'.$subKey.'":{';
					$out.='{';
					
					$out.='"_type":"'.strtolower(get_class($subValue)).'",';
					$out.=$subValue->toJson(1);
				}
				//rimuovo la virgola dall'ultima propriet� dell'oggetto
				$out=substr($out, 0, -1);
				$out.='],';
			}

		}
		//rimuovo la virgola dall'ultima propriet� dell'oggetto
		$out=substr($out, 0, -1);
		//chiudo la definizione oggetto
		$out.='},';
		//se questa funzione � chiamata di prima istanza e non � una derivata in quanto chiamata come sotto oggetto
		if(!$subRun){
			//rimuovo l'ultima virgola
			$out=substr($out, 0, -1);
			//e aggiungo le graffea inizio e fine
			$out='{'.$out.'}';
		}
		
		//rimpiazzo gli a capo con uno spazio in quanto in json non sono consentiti
		$out=str_replace("\r", " ", $out);
		$out=str_replace("\n", " ", $out);
		
		return $out;
	}		
	
   /*#########################################################
		FUNZIONI RELATIVE AL $DATABASE ESTERNO SQLITE
   */#########################################################
	public function createDb(){
		//genera un $DATAbase esterno sqLite se non presente sulla base delle propriet� sqLite definite nella classe dell'oggetto
		$fields=$this->getPropertiesNames();
		$table=$this->getDbName();
		$indexes=$this->getDbKeys();
		
		$sqlite=$GLOBALS['config']->sqlite;
		
		$fieldsToAdd='';
		//campi normali
		$fieldsToAdd.=implode($fields,' TEXT, ').' TEXT,';
		//campi indice
		//$fieldsToAdd.=implode($indexes,' TEXT NOT NULL, ');
		//chiavi primarie
		$fieldsToAdd.=' PRIMARY KEY ('.implode($indexes,',').')';
		//echo $sqlite->database;
		//apro il $DATAbase
		
		if ($this->getDbType()=='interno'){
			$db = new SQLite3($sqlite->databaseInterno);
		}else{
			$db = new SQLite3($sqlite->databaseDitta);
		}
		//creo la tabella
		$query="CREATE TABLE if not exists $table($fieldsToAdd)";
		$db->exec($query) or die($query);
		return;
	}
	public function saveToDb(){
		//salva i dati nel $DATAbase sqLite
		$fields=$this->getPropertiesNames();
		$table=$this->getDbName();
		$indexes=$this->getDbKeys();
		
		$sqlite=$GLOBALS['config']->sqlite;

		//escludo il valore id se si tratta di una nuova memorizzazzione, verra aggiunto automaticamente dal database
		if($this->id->getVal() == ''){
			if ($this->getDbType()=='interno'){
				$db = new SQLite3($sqlite->databaseInterno);
			}else{
				$db = new SQLite3($sqlite->databaseDitta);
			}
			$query="INSERT INTO $table DEFAULT VALUES";
			$db->exec($query) or die($query);
			$assignedId = $db->lastInsertRowid();
			$this->id->setVal($assignedId);

//echo property_exists($this, 'data');

			if(property_exists($this, 'data')){
				//ottengo il numero documento (ddt o ft) pi� alto (del'anno relativo alla data del documento) e da li aggiungo un numero per assegnarlo al nuovo documento (ddt o ft) che vado a memorizzare
				$year = substr($sting=$this->data->getVal(), 6, 4);// gg/mm/aaaa
				$startSearchDate = $year."-01-01"; //AAAAMMGG
				$endSearchDate = $year."-12-31"; //AAAAMMGG
				/*
				$query="
					SELECT MAX(CAST(numero AS int)) as ULTIMODOCUMENTO FROM $table 
					WHERE DATE(substr(data,7,4) || '-' || substr(data,4,2) || '-'|| substr(data,1,2))
					BETWEEN DATE('$startSearchDate') AND DATE('$endSearchDate');
				";
				*/
				$query="				
				SELECT MAX(CAST(numero AS int)) as ULTIMODOCUMENTO FROM $table 
				WHERE DATE(substr(data,7,4) || '-' || substr(data,4,2) || '-'|| substr(data,1,2)) 
				BETWEEN DATE('$startSearchDate') AND DATE('$endSearchDate'); 
				";
//echo $query;
			}else{//not sure if really needed ( per le righe )
				$query="SELECT MAX(CAST(numero AS int)) as ULTIMODOCUMENTO FROM $table";
			}

			$queryResults = $db->query($query) or die($query);
			while ($row = $queryResults->fetchArray()) {
				$lastDocNumber = $row['ULTIMODOCUMENTO'];
			}
			$this->numero->setVal($lastDocNumber+1);
		}
		
		//creo l'elenco di tutti i valori da memorizzare
		$values=array();
		foreach ($fields as $field){
			echo "\n\n\n############ ".$field." : ".is_array($this->$field->getVal());
			if($field == "ddt_id"){
				print_r($this->$field->getVal());
			}
			///se � un array (probabilmente si tratta di un elenco di righe o ddt, gli passo i dati di riferimento del mio oggetto attuale (ddt o fattura)
			if(is_array($this->$field->getVal())){
				//echo "\n\n\n############ ".$field."\n\n\n";
				//this field is an array we need to treat it differently
				$itemsId = array();
				foreach ($this->$field->getVal() as $itemk => $itemv){
					//if it is a ddt
					if (get_class($this) == 'Ddt'){
						if(property_exists ( $itemv , 'ddt_numero' )){ $itemv->ddt_numero->setVal($this->numero->getVal());}
						if(property_exists ( $itemv , 'ddt_data' )){ $itemv->ddt_data->setVal($this->data->getVal());}
						if(property_exists ( $itemv , 'ddt_id' )){ $itemv->ddt_id->setVal($this->id->getVal());}
					}
					//if it is a ft
					if (get_class($this) == 'Fattura'){
						//print_r($itemv);
						if(property_exists ( $itemv , 'fattura_id' )){ $itemv->fattura_id->setVal($this->id->getVal());}
					}
					$itemv->saveToDb();
					$itemsId[] = $itemv->id->getVal();
				}
				$values[] = implode(',', $itemsId);
				
			}else{
				//do this for all the *normal* fields
				$val=$this->$field->getVal();
				$values[]=(string) $this->$field->getVal();
			}
		}
		/*TODO*/
		$values=implode($values,'","');
		$values='"'.$values.'"';
		/*
		//aggiungo le '' per evitare che il $TESTO venga trattato numericamente
		$values=implode($values,"','");
		$values="'".$values."'";
		*/

		//apro il $DATAbase
		if ($this->getDbType()=='interno'){
			$db = new SQLite3($sqlite->databaseInterno);
		}else{
			$db = new SQLite3($sqlite->databaseDitta);
		}
		//creo la tabella
		//to fix : letto su internet che se vado ad aggiornare una riga com esempio solo 3 campi su quattro il campo che non vado ad aggiornare in questo momento con i nuovi valori viene resettato al valore di default o messo a null
		$query="INSERT OR REPLACE INTO $table (".implode($fields,',').") VALUES ($values)";
//echo "\n".$query;
		$db->exec($query) or die($query);
		return;
	}
	
	public function getFromDb(){
//		echo $this->id->valore.'1111';
		//ricava tutti i dati presente nel $DATAbase
		$fields=$this->getPropertiesNames();
		$table=$this->getDbName();
		$indexes=$this->getDbKeys();
		
		$sqlite=$GLOBALS['config']->sqlite;
		
		$where='WHERE ';
		$order=' ORDER BY ';
		$separatore="'";
		
		foreach($indexes as $key => $property){
			if($key>0){
				$where.=' AND ';
				$order.=',';
			}
			$where.=$this->$property->nome."=".$separatore.$this->$property->getVal().$separatore;
			$order.=$this->$property->nome;
		}			
		//la stringa della query
		$query='SELECT * FROM '.$table.' '.$where.$order;
//	echo $query;
		//apro il $DATAbase ed eseguo la query
		if ($this->getDbType()=='interno'){
			$db = new SQLite3($sqlite->databaseInterno);
		}else{
			$db = new SQLite3($sqlite->databaseDitta);
		}
		$results = $db->query($query) or die($query);
		//importo i risultati nel mio oggetto
		while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
			foreach ($row as $key => $value){
				$this->$key->setVal($value);
			}
		}
		//fine estensione oggetto
		return;
	}
	public function deletteFromDb(){
		//ricava tutti i dati presente nel $DATAbase
		$fields=$this->getPropertiesNames();
		$table=$this->getDbName();
		$indexes=$this->getDbKeys();
		
		$sqlite=$GLOBALS['config']->sqlite;
		
		$where='WHERE ';
		$separatore="'";
		
		foreach($indexes as $key => $property){
			if($key>0){
				$where.=' AND ';
			}
			$where.=$this->$property->nome."=".$separatore.$this->$property->getVal().$separatore;
		}			
		//la stringa della query
		$query='DELETE FROM '.$table.' '.$where;
		//echo $query;
		//apro il $DATAbase ed eseguo la query
		if ($this->getDbType()=='interno'){
			$db = new SQLite3($sqlite->databaseInterno);
		}else{
			$db = new SQLite3($sqlite->databaseDitta);
		}
		$results = $db->query($query) or die($query);
		return;
	}
	public function stampa(){

	}
}

class Propriet� extends DefaultClass {
	function __construct($nome, $validatore='', $parent){
	 	$this->nome=$nome;
	 	$this->validatore=$validatore;
	 	$this->valore='';
		$this->_parent=$parent;
	}
	public function setVal($newVal){
		return $this->valore=$newVal;
	}
	public function getVal(){
		return $this->valore;
	}
	public function getFormatted($params=''){
	}
	public function validate(){
	}
	public function getDataType(){
	}
	public function extend($recupera_da=''){
		if ($recupera_da!=''){
			//echo 'qui'.$recupera_da;
			//non considero come origine dai dati quella che mi ricavo dalla propriet� ma quella che mi sono passato in reupera_da
			preg_match('/^([a-z]*)_([a-z]*)$/', $recupera_da, $matches);
		}else{
			//uso normale
			preg_match('/^([a-z]*)_([a-z]*)$/', $this->nome, $matches);
		}
//print_r($matches);
		if($matches!=''){
			$dbName = ucfirst($matches[1]);
			$dbKey = $matches[2];
			//echo 'Extensible property:'.$dbName.' with key: '.$dbKey;
			$newObj = new $dbName(array(
				$dbKey => $this->valore
			));
			$newObj->getFromDb();
			return $newObj;
		}else{
			echo '<br>not an extensible property:'.$this->nome; 
		}
		
	}
}
Class Validatore {
	function __construct($params) {
		$this->lunghezza='';
		$this->interi='';
		$this->decimali='';
		$this->tipo = $params['tipo']; //testo $NUMERO misto
		$this->canBeNull='';
	}
	function validate ($obj){
		return 'true/false';
	}
}
$t = (object)"validatore";
$t->NUMERODOC = 	new Validatore(array('tipo'=>'INTEGER', 'lunghezza'=>6));
$t->NUMERATORE = 	new Validatore(array('tipo'=>'INTEGER', 'lunghezza'=>6));
$t->DATA = 			new Validatore(array('tipo'=>'INTEGER', 'lunghezza'=>10));
$t->CODICE = 		new Validatore(array('tipo'=>'TEXT',   'lunghezza'=>6));
$t->IMPORTO = 		new Validatore(array('tipo'=>'REAL',   'lunghezza'=>10));
$t->TESTO = 		new Validatore(array('tipo'=>'TEXT',   'lunghezza'=>''));
$t->NUMERO = 		new Validatore(array('tipo'=>'INTEGER','lunghezza'=>6));
$t->ALBO = 			new Validatore(array('tipo'=>'TEXT', '  lunghezza'=>20));
$t->PARTITAIVA = 	new Validatore(array('tipo'=>'INTEGER','lunghezza'=>11));
$t->CODFISCALE = 	new Validatore(array('tipo'=>'TEXT',   'lunghezza'=>16));
$t->TELEFONO = 		new Validatore(array('tipo'=>'TEXT',   'lunghezza'=>11));
$t->MAIL = 			new Validatore(array('tipo'=>'TEXT',   'lunghezza'=>30));
$t->URL = 			new Validatore(array('tipo'=>'TEXT',   'lunghezza'=>50));

/*########################################################################################*/
class Ddt  extends MyClass {
	function __construct($params) {
		$this->addProp('id', 'NUMERATORE');
		$this->addProp('fattura_id', 'NUMERATORE');
		$this->addProp('riga_id', 'NUMERATORE');
		
		$this->addProp('numero', 'NUMERATORE');
		$this->addProp('data', 'DATA');
		$this->addProp('clientefornitore_codice', 'CODICE');
		$this->addProp('causale_codice', 'CODICE');
		$this->addProp('mezzo_codice', 'CODICE');
		$this->addProp('vettore_codice', 'CODICE');
		$this->addProp('destinatario_codice', 'CODICE');
		$this->addProp('note');
		
//		$this->addProp('riga_id', 'ARRAY');
		//$this->righe/**/
		//$this->righe=array();
		
		//importo eventuali valori delle propriet� che mi sono passato come $params
		$this->mergeParams($params);
	}
	function getDbType(){
		return 'ditta';
	}
	function getDbKeys(){
		//return array('numero','data');
		return array('id');
	}
	//ovverride the default "deletteFromDb" function since we need to delette "rows" too
	function deletteFromDb(){
		$myddtrighe = new MyList(array( '_type'=>'Riga',
										'ddt_id'=>$this->id->getVal()
		));

		$myddtrighe->iterate(function($riga){
			global $myddtRigheBACKUP;
			$myddtRigheBACKUP[] = $riga->toJson();
			$riga->deletteFromDb();
		});
		parent::deletteFromDb();
	}
	function visualizzaPdf(){
		$pdf = generaPdfDdt($this);
		@$pdf->Output($this->getPdfFileUrl(), 'D');//DOWNLOAD THE FILE
		return;
	}
	function stampa(){
		generaPdfDdt($this);

		$sumatrapdfexe = 'C:\Programmi\SumatraPDF\SumatraPDF.exe';
		$filename = '"'.$this->getPdfFileUrl().'"';
		$printername = '"HPNUOVA"';
		$printCommand = $sumatrapdfexe.' -print-to '.$printername.' -print-settings "1x,fit" -silent -exit-when-done '.$filename;
echo $printCommand;
		exec($printCommand);
		return;
	}
	function getPdfFileName(){
		$numero=str_replace(" ", "0", $this->numero->getVal());
		$tipo=$this->causale_codice->getVal();
		
		$arr=explode("/", $this->data->getVal());
		//20/12/2024
								//mese   //giorno //anno
		$newVal=mktime(0, 0, 0, $arr[1], $arr[0], $arr[2]);
		$newVal=date ( 'Ymd' , $newVal);
		$data=$newVal;
		
		$nomefile=$data.'_'.$tipo.'_'.$numero.'.pdf';
		return $nomefile;
	}
	function getPdfFileUrl(){
		//il nome del file esempio: 20120121_N00000001.pdf
		$filename=$this->getPdfFileName();
		//la cartella principale delle stampe
		$dirDelleStampe=$GLOBALS['config']->pdfDir;
		//l'url completo del file esempio: c:/Program%20Files/EasyPHP-5.3.6.0/www/webcontab/my/php/stampe/ft/20120121_N00000001.pdf
		$fileUrl=$dirDelleStampe.'/ddt/'.$filename;
		
		//verifichiamo che il file esista prima di comunicarlo
		//altrimenti lo generiamo "al volo"
		if(!file_exists($fileUrl)){
			//echo 'il file non esiste devo generarlo!!';
			$this->generaPdf();
		}
		return $fileUrl;
	}
	function getRighe(){
		/*todo we commented out this part, are we rights?*/
//		if($this->_oRighe){
//		if(property_exists($this, 'oRighe')){
			//do nothing we already have what we need
			//echo 'Im fine!';
			
//		}else{
			//echo 'I need righe!';
			//get them from the db
			$this->_oRighe = new MyList(array(
				'_type'=>'Riga',
				'ddt_id'=>$this->id->getVal()
			));
//		}
		return $this->_oRighe;
	}
	function getTotaleColli(){
		$GLOBALS['tempColliTot']=0;
		$this->getRighe()->iterate(function($riga){
			$GLOBALS['tempColliTot'] +=$riga->colli->getVal();
		});
		return $GLOBALS['tempColliTot'];
	}
	function getTotalePesoLordo(){
		$GLOBALS['tempPesoLordoTot']=0;
		$this->getRighe()->iterate(function($riga){
			$GLOBALS['tempPesoLordoTot'] +=$riga->pesolordo->getVal();
		});
		return $GLOBALS['tempPesoLordoTot'];
	}
	
}

class Riga extends MyClass {
	function __construct($params) {
		$this->addProp('id', 'NUMERATORE');
		
		$this->addProp('ddt_data', 'DATA');
		$this->addProp('ddt_numero', 'NUMERATORE');
		$this->addProp('ddt_id', 'NUMERATORE');
		$this->addProp('fattura_id', 'NUMERATORE');
		$this->addProp('numero', 'NUMERATORE');
		$this->addProp('articolo_codice', 'CODICE');
		$this->addProp('um_codice', 'CODICE');
		$this->addProp('prezzo', 'IMPORTO');
		$this->addProp('colli', 'NUMERO');
		$this->addProp('imballaggio_codice', 'CODICE');
		$this->addProp('pesolordo', 'NUMERO');
		$this->addProp('tara', 'NUMERO');
		$this->addProp('pesonetto', 'NUMERO');
		$this->addProp('lotto', 'TESTO');
		$this->addProp('iva_codice', 'CODICE');
		
		//importo eventuali valori delle propriet� che mi sono passato come $params
		$this->mergeParams($params);
	}
	function getDbType(){
		return 'ditta';
	}
	function getDbKeys(){
		//return array('ddt_data','ddt_numero','numero');
		return array('id');
	}
}

class Articolo extends MyClass {
	function __construct($params) {
		$this->addProp('codice', 'CODICE');
		$this->addProp('descrizione', 'TESTO');
		$this->addProp('um_codice', 'CODICE');
		$this->addProp('iva_codice', 'CODICE');
		
		//importo eventuali valori delle propriet� che mi sono passato come $params
		$this->mergeParams($params);
	}
	function getDbType(){
		return 'interno';
	}
	function getDbKeys(){
		return array('codice');
	}
}

class Imballaggio extends MyClass {
	function __construct($params) {
		$this->addProp('codice', 'CODICE');
		$this->addProp('descrizione', 'TESTO');
		$this->addProp('taraacquisto', 'NUMERO');
		$this->addProp('taravendita', 'NUMERO');
		
		//importo eventuali valori delle propriet� che mi sono passato come $params
		$this->mergeParams($params);
	}
	function getDbType(){
		return 'interno';
	}
	function getDbKeys(){
		return array('codice');
	}
}

class Clientefornitore extends MyClass {
	function __construct($params) {
		$this->addProp('codice', 'CODICE');
		$this->addProp('ragionesociale', 'TESTO');
		$this->addProp('via', 'TESTO');
		$this->addProp('paese', 'TESTO');
		$this->addProp('provincia', 'TESTO');
		$this->addProp('cap', 'NUMERO');
		$this->addProp('nazione', 'TESTO');
		$this->addProp('alboautotrasportatori', 'ALBO');

		$this->addProp('mezzo_codice', 'CODICE');
		$this->addProp('vettore_codice', 'CODICE');
		$this->addProp('piva', 'PARTITAIVA');
		$this->addProp('codfiscale', 'CODICEFISCALE');
		$this->addProp('iva_codice', 'CODICE');
		$this->addProp('telefono', 'TELEFONO');
		$this->addProp('cellulare', 'TELEFONO');
		$this->addProp('fax', 'TELEFONO');
		$this->addProp('email', 'MAIL');
		$this->addProp('web', 'URL');
		$this->addProp('valuta', 'TESTO');
		
		$this->addProp('banca_codice', '');
		$this->addProp('pagamentoscadenza_codice', '');
		$this->addProp('pagamentomodalita_codice', '');
		
		$this->addProp('nome', 'TESTO');
		$this->addProp('vettore', 'TESTO');
		$this->addProp('codifica', 'TESTO');
		
		$this->addProp('pec', 'TESTO');
		$this->addProp('codiceSDI', 'TESTO');
		
		//importo eventuali valori delle propriet� che mi sono passato come $params
		$this->mergeParams($params);
	}
	function getDbType(){
		return 'interno';
	}
	function getDbKeys(){
		return array('codice');
	}
}

class Iva extends MyClass {
	function __construct($params) {
		$this->addProp('codice', 'CODICE');
		$this->addProp('descrizione', 'TESTO');
		$this->addProp('aliquota', 'NUMERO');
		$this->addProp('codiceSDI', 'TESTO');
		
		//importo eventuali valori delle propriet� che mi sono passato come $params
		$this->mergeParams($params);
	}
	function getDbType(){
		return 'interno';
	}
	function getDbKeys(){
		return array('codice');
	}
}

class Causale extends MyClass {
	function __construct($params) {
		$this->addProp('codice', 'CODICE');
		$this->addProp('descrizione', 'TESTO');
		$this->addProp('segno', 'SEGNO');
		
		//importo eventuali valori delle propriet� che mi sono passato come $params
		$this->mergeParams($params);
	}
	function getDbType(){
		return 'interno';
	}
	function getDbKeys(){
		return array('codice');
	}
}
class Mezzo extends MyClass { //mittente / destinatario / vettore carico mittente / vettore carico destinatario
	function __construct($params) {
		$this->addProp('codice', 'CODICE');
		$this->addProp('descrizione', 'TESTO');
		
		//importo eventuali valori delle propriet� che mi sono passato come $params
		$this->mergeParams($params);
	}
	function getDbType(){
		return 'interno';
	}
	function getDbKeys(){
		return array('codice');
	}
}
class Um extends MyClass {
	function __construct($params) {
		$this->addProp('codice', 'CODICE');
		$this->addProp('descrizione', 'TESTO');
		
		//importo eventuali valori delle propriet� che mi sono passato come $params
		$this->mergeParams($params);
	}
	function getDbType(){
		return 'interno';
	}
	function getDbKeys(){
		return array('codice');
	}
}



class Fattura  extends MyClass {
	function __construct($params) {
		$this->addProp('id', 'NUMERATORE');
		
		$this->addProp('numero', 'NUMERATORE');
		$this->addProp('data', 'DATA');
		$this->addProp('clientefornitore_codice', 'CODICE');
		$this->addProp('tipofattura_codice', ''); //vendta o nota credito
		
		$this->addProp('banca_codice', '');
		$this->addProp('pagamentoscadenza_codice', '');
		$this->addProp('pagamentomodalita_codice', '');
		
		$this->addProp('valuta', '');
		$this->addProp('imponibile', '');
		$this->addProp('iva', '');
		$this->addProp('totale', '');
		
		$this->addProp('ddt_id', 'ARRAY');
		$this->addProp('riga_id', 'ARRAY');
		
		$this->addProp('progressivoSDI', '');
		
		//importo eventuali valori delle propriet� che mi sono passato come $params
		$this->mergeParams($params);
	}
	function getDbType(){
		return 'ditta';
	}
	function getDbKeys(){
		//return array('numero','data');
		return array('id');
	}
	//ovverride the default "deletteFromDb" function since we need to delette "rows" too
	function deletteFromDb(){
		/*
		$myddtrighe = new MyList(array( '_type'=>'Riga',
										'ddt_id'=>$this->id->getVal()
		));

		$myddtrighe->iterate(function($riga){
			global $myddtRigheBACKUP;
			$myddtRigheBACKUP[] = $riga->toJson();
			$riga->deletteFromDb();
		});
		parent::deletteFromDb();
		*/
	}
	function visualizzaXml(){
		$dati= estrapolaDatiPerXmlFt($this);
		generaXmlDaDatiFattura($dati);
	}

	function stampa(){
		/*
		generaPdfDdt($this);
		//url completo del file pdf
		$pdfUrl=$this->getPdfFileUrl();
		// impostiamo l'header di un file pdf
		header('Content-type: application/pdf');
		// e inviamolo al browser
		readfile($pdfUrl);
		return;
		*/
	}
	function getPdfFileName(){
		/*
		$numero=str_replace(" ", "0", $this->numero->getVal());
		$tipo=$this->causale_codice->getVal();
		
		$arr=explode("/", $this->data->getVal());
								//mese   //giorno //anno
		$newVal=mktime(0, 0, 0, $arr[0], $arr[1], $arr[2]);
		$newVal=date ( 'Ymd' , $newVal);
		$data=$newVal;
		
		$nomefile=$data.'_'.$tipo.$numero.'.pdf';
		return $nomefile;
		*/
	}
	function getPdfFileUrl(){
		/*
		//il nome del file esempio: 20120121_N00000001.pdf
		$filename=$this->getPdfFileName();
		//la cartella principale delle stampe
		$dirDelleStampe=$GLOBALS['config']->pdfDir;
		//l'url completo del file esempio: c:/Program%20Files/EasyPHP-5.3.6.0/www/webcontab/my/php/stampe/ft/20120121_N00000001.pdf
		$fileUrl=$dirDelleStampe.'/ft/'.$filename;
		
		//verifichiamo che il file esista prima di comunicarlo
		//altrimenti lo generiamo "al volo"
		if(!file_exists($fileUrl)){
			//echo 'il file non esiste devo generarlo!!';
			$this->generaPdf();
		}
		return $fileUrl;
		*/
	}
	function getRighe(){
		if(property_exists($this,'_oRighe')){
			//do nothing we already have what we need
			//echo 'Im fine!';
			
		}else{
			//echo 'I need righe!';
			//get them from the db
			$this->_oRighe = new MyList(array(
				'_type'=>'Riga',
				'fattura_id'=>$this->id->getVal()
			));
		}
		return $this->_oRighe;
	}
	function getTotaleColli(){
		/*
		$GLOBALS['tempColliTot']=0;
		$this->getRighe()->iterate(function($riga){
			$GLOBALS['tempColliTot'] +=$riga->colli->getVal();
		});
		return $GLOBALS['tempColliTot'];
		*/
	}
	function getTotalePesoLordo(){
		/*
		$GLOBALS['tempPesoLordoTot']=0;
		$this->getRighe()->iterate(function($riga){
			$GLOBALS['tempPesoLordoTot'] +=$riga->pesolordo->getVal();
		});
		return $GLOBALS['tempPesoLordoTot'];
		*/
	}
	function getProgressivoInvioSDI(){
		$myfile = fopen("./dati/brungimmi/progressivoSDI.txt", "r") or die("Unable to open file!");
		$ultimoProgressivoSDIUtilizzato = fgets($myfile);
		//echo "\n precedente: ".$ultimoProgressivoSDIUtilizzato;
		$prossimoProgresivoSDI = (int)ltrim($ultimoProgressivoSDIUtilizzato, "0")+1;
		fclose($myfile);
		$stringaProgressivoSDI = str_pad ($prossimoProgresivoSDI, 5, "0", STR_PAD_LEFT); 
		//echo "\n uso: ".$stringaProgressivoSDI;
		return $stringaProgressivoSDI;
	}
	function saveUsedProgressivoInvioSDI(){
		$stringaProgressivoSDI = $this->getProgressivoInvioSDI();
		$myfile = fopen("./dati/brungimmi/progressivoSDI.txt", "w") or die("Unable to open file!");
		//echo "\n ho usato: ".$stringaProgressivoSDI;
		//echo "\n";
		fwrite($myfile, $stringaProgressivoSDI);
		fclose($myfile);
	}
	function getSDIXmlFileName(){
		if(property_exists($this,'nomeFileXml')){
			if($this->nomeFileXml!=''){
				return $this->nomeFileXml;
			}
		}
		
		$nomeFile = $GLOBALS['config']->azienda->nazione->getVal();
		$nomeFile .= $GLOBALS['config']->azienda->piva->getVal();
		$nomeFile .= '_';
		$nomeFile .= $this->getProgressivoInvioSDI();
		$nomeFile .= '.xml';

		$this->nomeFileXml = $nomeFile;
		return $this->nomeFileXml;
	}
	function getSDIXmlFileUrl(){
		$urlFile = './dati/brungimmi/fattureVenditaXML/'.$this->getSDIXmlFileName();
		return $urlFile;
	}
	function getSDITempFileUrl(){
		$urlFile = './dati/brungimmi/temp/'.$this->getSDIXmlFileName();
		return $urlFile;
	}
	function getSDIOfficialFileUrl(){
		$urlFile = './dati/brungimmi/fattureVenditaXML/'.$this->getSDIXmlFileName();
		return $urlFile;
	}
	function generaTempSDIXmlFile(){
		$dati= estrapolaDatiPerXmlFt($this);
		$urlFileSaving = $this->getSDITempFileUrl();
		generaXmlDaDatiFattura($dati, $urlFileSaving);
	}

	public function inviaSDI(){
		//rigenero il file pdf della fattura

		$this->generaTempSDIXmlFile();

		//importo i dati di configurazione della pec
		$pec=$GLOBALS['config']->pec;
		//$cliente=$this->cod_cliente->extend();
		//var_dump($cliente);
		$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

		$mail->IsSMTP(); // telling the class to use SMTP

		try {
			$mail->Host       = $pec->Host;
			$mail->SMTPDebug  = $pec->SMTPDebug;
			$mail->SMTPAuth   = $pec->SMTPAuth;
			$mail->Port       = $pec->Port;
			$mail->Username   = $pec->Username;
			$mail->Password   = $pec->Password;

			//indirizzo mail PEC
			$mail->AddAddress($GLOBALS['config']->SDIpec, 'SDI'); //destinatario
			
			//mi faccio mandare la ricevuta di lettura
			$mail->ConfirmReadingTo=$pec->ReplyTo->Mail;
			$mail->SetFrom($pec->From->Mail, $pec->From->Name);
			$mail->AddReplyTo($pec->ReplyTo->Mail, $pec->ReplyTo->Name);
			//Invio a SDI - 20190115_F00000001 - File IT01588530236_00001.xml - FT n.1 del 15-01-2019 - Primo Invio
			$mail->Subject = 'Invio a SDI - File '.$this->getSDIXmlFileName().' - '.$this->tipofattura_codice->extend()->descrizione->getVal().' Nr. '.$this->numero->getVal().' del '.$this->data->getVal().' - Primo Invio'; //oggetto
			
			/*
			Si invia in allegato file relativo alla Fattura Elettronica
			FT n.1 del 15-01-2019
			File IT01588530236_00001.xml
			Primo invio 
			*/
			//$message="[Messaggio automatizzato] <br><br>\n\n Si invia in allegato file relativo alla Fattura Elettronica";
			$message="<br>\n".$this->tipofattura_codice->extend()->descrizione->getVal().' Nr. '.$this->numero->getVal().' del '.$this->data->getVal();
			$message.="<br>\n"."File ".$this->getSDIXmlFileName();
			$message.="<br>\n"."Primo invio";


			$mail->MsgHTML($message);
			//$mail->Body($message); 

			//allego l'xml della fattura
			$mail->AddAttachment($this->getSDITempFileUrl()); 
			
			//var_dump($mail);
			
			if($mail->Send()){
			//	$html= '<h2 style="color:green">Messaggio Inviato</h2>';
			//	$html.= '<br>Il messaggio con oggetto: ';
			//	$html.= '<b>'.$mail->Subject.'</b>';
			//	$html.='<br>E\' stato inviato a: <b>'.$cliente->ragionesociale->getVal().'</b>';
			//	$html.='<br>all\'indirizzo: <b>'.$cliente->__pec->getVal().'</b>';
			//	$html.='<br>con allegato il file: <b>'.$this->getPdfFileUrl().'</b>';
				
				//memorizzo la data di invio
	//			$this->__datainviopec->setVal(date("d/m/Y"));
	//			$this->saveSqlDbData();
				//mostro il messaggio di avvenuto invio
			//	echo $html;
			//	var_dump($message);
				//all seems ok
				
				//mi ricordo il progressivo utilizzato
				$this->progressivoSDI->setVal($this->getProgressivoInvioSDI());
				
				//mi ricordo di aver utilizzato questo id per questa fattura
				$this->saveUsedProgressivoInvioSDI();
				
				//salvo id utilizzato nel databse con la fattura
				$this->saveToDb();
				
				//sposto il file temporaneo nella cartella ufficiale
				rename($this->getSDITempFileUrl(),$this->getSDIOfficialFileUrl());
				
				return true;
			}
		} catch (phpmailerException $e) {
			echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
			echo $e->getMessage(); //Boring error messages from anything else!
		}
		return false;
	}
}

class Banca extends MyClass {
	function __construct($params) {
		$this->addProp('codice', 'CODICE');
		$this->addProp('descrizione', 'TESTO');//iban
		
		//importo eventuali valori delle propriet� che mi sono passato come $params
		$this->mergeParams($params);
	}
	function getDbType(){
		return 'ditta';
	}
	function getDbKeys(){
		return array('codice');
	}
}


class Pagamentoscadenza extends MyClass {
	function __construct($params) {
		$this->addProp('codice', 'CODICE');
		$this->addProp('descrizione', 'TESTO');//iban
		
		//importo eventuali valori delle propriet� che mi sono passato come $params
		$this->mergeParams($params);
	}
	function getDbType(){
		return 'interno';
	}
	function getDbKeys(){
		return array('codice');
	}
}
class Pagamentomodalita extends MyClass {
	function __construct($params) {
		$this->addProp('codice', 'CODICE');
		$this->addProp('descrizione', 'TESTO');
		$this->addProp('codiceSDI', 'TESTO');
		
		//importo eventuali valori delle propriet� che mi sono passato come $params
		$this->mergeParams($params);
	}
	function getDbType(){
		return 'interno';
	}
	function getDbKeys(){
		return array('codice');
	}
}
class Tipofattura extends MyClass {
	function __construct($params) {
		$this->addProp('codice', 'CODICE');
		$this->addProp('descrizione', 'TESTO');
		$this->addProp('codiceSDI', 'TESTO');
		
		//importo eventuali valori delle propriet� che mi sono passato come $params
		$this->mergeParams($params);
	}
	function getDbType(){
		return 'interno';
	}
	function getDbKeys(){
		return array('codice');
	}
}

class MyList {
/*
example usage
$test=new MyList(
	array(
		'_type'=>'Ddt',
		'_select'=>'numero,data',
		'data'=>array('=','17/02/12'),
		'data'=>array('>','28/03/09'),
		'data'=>array('<','17/02/12'),
		'data'=>array('<>','01/01/09','01/01/11'),
		'data'=>'28/03/09',	
		'numero'=>'784'
	)
);
*/
	function __construct($params) {
/* how to fix date queries:		
SELECT * FROM Ddt where substr(data,7)||substr(data,4,2)||substr(data,1,2) 
      between '20200101' and '20201231' AND (clientefornitore_codice<>'') ORDER BY id 
*/

//	print_r($params);
	
		$this->_params=$params;
		$numeroDiValori=0;
		//inizializzo larray che conterr� gli oggetti della lista
		$this->arr=array();
	
		$objType=$params['_type'];
		$fakeObj=new $objType(array('_autoExtend'=>'-1'
									));
		$condition=array();
		$i=0;		
		$operator=null;
		$newVal=null;
		$newKey=null;
		
		foreach ($params as $key => $value) {
			//se non si tratta di una propriet� interna
			if($key['0']!='_'){
				//se c'� un operatore '=' '<' '>' '<>' '>=' '<=' '!='
				//il primo valore della variabile $value sar� la stringa dell'operatore
				//altrimenti � solo un "valore/array di possibili valori" per la $key
				
				
				if($key=='data'){

					for ($a = 1; $a < count($value); $a++) {
					//echo "\n<br>*".$value[$a]."*\n<br>";
					//echo count($value[$a]);
						//echo $value;
						//make a date 16/01/2020 to 20200116
						if (strlen($value[$a]) == 10 && strpos($value[$a], '/')){
							//01/01/2020
							$value[$a]=substr($value[$a],6,4).substr($value[$a],3,2).substr($value[$a],0,2); 
						}else if(strlen($value[$a]) == 8 && strpos($value[$a], '/')){
							//01/01/20
							$value[$a]='20'.substr($value[$a],6,2).substr($value[$a],3,2).substr($value[$a],0,2);
						}else if(strlen($value[$a]) == 8){
							//20200101
							$value[$a]=$value[$a];	
						}
					}
					
					//$key = ' substr(data,7)||substr(data,4,2)||substr(data,1,2) ';
				}
				
				
				switch ($value[0]){
					case '=':
						$tOperator='=';
						array_shift($value);//rimuovo la condizione e lascio il valore/valori
						$numeroDiValori=count($value);
						break;
					case '<':
						$tOperator='<';
						array_shift($value);//rimuovo la condizione e lascio il valore/valori
						break;
					case '>':
						$tOperator='>';
						array_shift($value);//rimuovo la condizione e lascio il valore/valori
						break;
					case '<=':
						$tOperator='<=';
						array_shift($value);//rimuovo la condizione e lascio il valore/valori
						break;
					case '>=':
						$tOperator='>=';
						array_shift($value);//rimuovo la condizione e lascio il valore/valori
						break;
					case '<>'://compreso tra
						//inverto i simboli per mia comodita
					//	$tOperator=array('>=','<=');
						$tOperator=array('between','and');
						array_shift($value);//rimuovo la condizione e lascio il valore/valori
						break;
					case '!='://diverso da
						$tOperator='<>';
						array_shift($value);//rimuovo la condizione e lascio il valore/valori
						//$numeroDiValori=count($value);
						break;
					default:
						//se non � nessuno dei precedenti vuol dire che ho passato solo uno /dei valori da confrontare
						//e quindi presumo che l'operatore sia '='
						$tOperator='=';
						//$numeroDiValori=count($value);
						break;
				}
/*				
				print_r($key);

				print_r($value);
				
				
				print_r($tOperator);
*/
				
				//se ho un array di valori e un arrai di operatori (caso del '<>' compreso tra)
				if (is_array($value) && is_array($tOperator)){
						$operator[]=$tOperator[0];
						$newVal[]=$value[0];
						$newKey[]=$key;
						
						$operator[]=$tOperator[1];
						$newVal[]=$value[1];
						$newKey[]=$key;
				//altrimenti si ho un array di valori ma un solo operatore allora presumo che l'operatore sia lo stesso per tutti i valori
				}else if (is_array($value) && !is_array($tOperator)){
					//echo "\n --$key-- ".'sono qui!: ';
					//print_r($value);
					//print_r($params[$key]); 
					foreach ($value as $tVal){
					//foreach ($params[$key] as $tVal){
						//echo $tVal."\n";
						$operator[]=$tOperator;
						$newVal[]=$tVal;
						$newKey[]=$key;
					}
				}else{
				//se innfino ho un solo valore e un solo operatore allora � tutto semplice 
					$operator[]=$tOperator;
					//$newVal[]=$value;
					$newVal[]=$params[$key];
					$newKey[]=$key;
				}
			}
		}

		//trasferisco il tutto dentro l'array conditions
		for ($h=0; $h<count($operator); $h++){
			/*
			echo 'index   :'.$h."##\n";
			echo 'operator:'.$operator[$h]."##\n";
			echo 'keY     :'.$newKey[$h]."##\n";
			echo 'value   :'.$newVal[$h]."##\n";
			echo 'value   :'.$newVal[$h]."##\n-----------\n";
			*/
				$val=$fakeObj->{$newKey[$h]}->setVal($newVal[$h]);
				//echo $operator;
				$condition[$newKey[$h]][$operator[$h]][]=$val;
		}


		$where='WHERE ';
		$order=' ORDER BY ';

		//recupero i campi di ordinamento // clausola order
		$indexes=$fakeObj->getDbKeys();
		foreach($indexes as $key => $property){
			if($key>0){
				$order.=',';
			}
			$order.=$fakeObj->$property->nome;
		}
		//e creo la clausola where
		//per ogni chiave
		$c1=0;
		foreach($condition as $key => $operator){
			$myKey=$key;
			//per ogni operatore della chiave
			if ($c1>0){
				$where.=' AND (';
			}
			$c2=0;
			foreach($operator as $operatorKey => $operatorValue){
				$myOperatorKey=$operatorKey;
				//per ogni valore dell'operatore
				if ($c2>0){
					$where.=' AND ';
				}
				$c3=0;
				foreach ($operatorValue as $val){
									
					//echo $c3.' '.$myOperatorKey.'<br>';
					if ($c3>0){
						if($myOperatorKey=='='){
							$where.=' OR ';
						}else{
							$where.=' AND ';
						}
					}	
			
					$property=$myKey;
					$val=$val;
					$operator=$myOperatorKey;
					
					$info=$fakeObj->$property->getDataType();
					switch($info['type']){
						case 'Date': $separatore="#";break;
						case 'Numeric': $separatore="";break;
						default: $separatore="'";break;
			
					}

					//so sqlite use a different way of handling searching values in a range
					//the next 10/15 lines are for this case
					if(@$nextShouldBeAnd){
						$nextShouldBeAnd = false;
						$property=$myKey;
						$val=$val;
						$operator=$myOperatorKey;
						$where.=''.$separatore.$val.$separatore;

					}else{
						$where.=$fakeObj->$property->nome.$operator.$separatore.$val.$separatore;
					}
					//set the flag so we knon next value will be the second aprt of between in 'between x and y'
					if($myOperatorKey=='between'){
						$nextShouldBeAnd = true;
					}
					
					$c3++;
				}
				$c2++;
			}
			if ($c1>0){
				$where.=') ';
			}
			$c1++;
		}	
		
		/*compose the select statement
			if nothing is specified just select all
		*/
		$select="";
		//if($params['_select']){
		if(in_array('_select', $params)){//this should fix a "notice error"
			$indexes = explode(",", $params['_select']);
			foreach($indexes as $key => $property){
				if($key>0){
					$select.=',';
				}
				$select.=$fakeObj->$property->nome;
			}
		}else{
			$select = '*';
		}

		$sqlite=$GLOBALS['config']->sqlite;
		$table=$fakeObj->getDbName();
	
		$query='SELECT '.$select.' FROM '.$table.' '.$where.$order;
		$query = str_replace('data',' substr(data,7)||substr(data,4,2)||substr(data,1,2) ', $query);
		//echo $query."\n<br>";
		
		//apro il $DATAbase ed eseguo la query
		if ($fakeObj->getDbType()=='interno'){
			$db = new SQLite3($sqlite->databaseInterno);
		}else{
			$db = new SQLite3($sqlite->databaseDitta);
		}
		$results = $db->query($query) or die($query);
		//importo i risultati nel mio oggetto
		while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
			//$obj=new $objType(array(
			//		'_result'=>$row,
			//));
			$obj=new $objType($row);
			$this->add($obj);
		}
	}
	function sum($propName){
		//restituisce la somma della propriet� indicata degli oggetti della lista
		$out=0;
		foreach ($this->arr as $key => $value){
			$out+=$value->$propName->getVal();
		}
		return $out;
	}
	function add($newObj){
		//add a new object to the current array
		array_push($this->arr, $newObj);
	}
	function remove(){
	}
	function iterate($function,$args=''){
		$strResult='';
		//esegue una funzione su ogni riga
		foreach ($this->arr as $key => $value){
			$strResult.=$function($value,$args);
		}
		return $strResult;
	}
}

?>
