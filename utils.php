<?php
/*============================================================
UTILITY FUNCTIONS
============================================================*/
function formatDate($from,$to, $date){
	if ($from=='mm-dd-yyyy'){
		if(preg_match('/^(..)-(..)-(....)$/', $date, $match)){
			$month = $match[1]; 
			$day = $match[2];
			$year = $match[3];
		}
	}else{$month='';$day='';$year='';}
	if ($from=='dd.mm.yyyy'){
		if(preg_match('/^(..).(..).(....)$/', $date, $match)){
			$day = $match[1];
			$month = $match[2]; 
			$year = $match[3];
		}
	}else{$month='';$day='';$year='';}
	if ($from=='dd/mm/yyyy'){
		if(preg_match('/^(..)\/(..)\/(....)$/', $date, $match)){
			$day = $match[1];
			$month = $match[2];
			$year = $match[3];
		}
	}else{$month='';$day='';$year='';}
	
	if($to=='yyyy-mm-dd'){
		return $year.'-'.$month.'-'.$day;
	}
}

function formatImporto($importo){
	$posizionevirgola=strpos($importo, '.'); 
	$lunghezza= strlen($importo);
	//echo $importo.'//////'.$posizionevirgola."***".$lunghezza.'==='.($lunghezza*1-$posizionevirgola*1)."\n";
	if($posizionevirgola===false){
		return $importo=$importo.'.00';
	}

	if(($lunghezza*1-$posizionevirgola*1)<3){
		return $importo=$importo.'0';
	}
	return $importo;
}


function comma2dot($str){
	return str_replace(',', '.', $str);
}
function dot2comma($str){
	return str_replace('.', ',', $str);
}


function markFileReadOnly($fileUrl){
	chmod( $files, '0600');
}
function markFileReadAndWrite($fileUrl){
	chmod( $files, '0755');
}

?>
