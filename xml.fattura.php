<?PHP
/*
include ('./config.inc.php');
include ('./utils.php');

$selectFt = new StdClass();
$selectFt->id = 22; 

$myFt = new Fattura($selectFt);
$myFt->getFromDb();
$myFt->generaTempSDIXmlFile();

$dati= estrapolaDatiPerXmlFt($myFt);
generaXmlDaDatiFattura($dati);
*/

/*============================================================
MAIN FUNCTION THAT GENERATE THE XML
============================================================*/
function estrapolaDatiPerXmlFt($myFt){
	/*
	$dati conterrà le informazioni che mi servono per generare la fattura
	la sua struttura sarà quella che il programma si aspetta per generare correttamente il file xml
	 * */
	
	global $config;
	
	//ricavo i dati
	$dati =  new stdClass();
	
	/* DATI GENERALI FATTURA */
	$dati->fattura = new stdClass();
	/* tipo:
	TD01 = Fattura 
	TD02 = Acconto/Anticipo su fattura 
	TD03 = Acconto/Anticipo su parcella 
	TD04 = Nota di Credito 
	TD05 = Nota di Debito 
	TD06 = Parcella 
 	*/
	$dati->fattura->tipo = $myFt->tipofattura_codice->extend()->codiceSDI->getVal();
	$anno=explode('/',$myFt->data->getFormatted());
	$dati->fattura->divisa = $myFt->valuta->getVal(); //'EUR'
	$dati->fattura->numero = $myFt->numero->getVal(); //todo-aggiungo la stringa "/anno"
	$dati->fattura->data = formatDate('dd/mm/yyyy','yyyy-mm-dd',$myFt->data->getVal());
	$dati->fattura->importo = formatImporto(abs($myFt->totale->valore)); //mi salvo il valore assoluto** le note di accredito mi davano un valore negativo
	//$dati->fattura->causale = 'vendita'; //non obbligatorio/necessario
	
	/* PROGRESSIVO INVIO */
		//'00001';/*todo: massimo 10 caratteri:: ma il nome file ne contiene massimo 5*/
	$dati->ProgressivoInvio = $myFt->getProgressivoInvioSDI();
	
	/* TIPO "DESTINATARIO" FATTURA */
	/*
	FPR12 = fattura tra privati (sia ditte che persone fisiche)  
	FPA12 = fattura verso pubblica amministrazione
	*/
	$dati->FormatoTrasmissione = 'FPR12';
	
	/* EMITTENTE */
	$dati->emittente = new stdClass();
	$dati->emittente->partitaIvaNazione 		= $config->azienda->nazione->getVal();;
	$dati->emittente->partitaIvaCodice 			= $config->azienda->piva->getVal();
	$dati->emittente->codiceFiscale				= $config->azienda->codfiscale->getVal();
	$dati->emittente->ragioneSociale 			= htmlentities($config->azienda->ragionesociale->getVal());
	$dati->emittente->regimeFiscale 			= 'RF01'; //(RF01 = regime ordinario)
	$dati->emittente->sede = new stdClass();
	$dati->emittente->sede->via 				= htmlentities($config->azienda->via->getVal());
	$dati->emittente->sede->paese 				= htmlentities($config->azienda->paese->getVal());
	$dati->emittente->sede->citta				= htmlentities($config->azienda->provincia->getVal());
	$dati->emittente->sede->cap 				= $config->azienda->cap->getVal();
	$dati->emittente->sede->nazione 			= $config->azienda->nazione->getVal();;
	$dati->emittente->datiREA = new stdClass();
	$dati->emittente->datiREA->Ufficio 			= "VR";
	$dati->emittente->datiREA->NumeroREA 		= "185024";
	$dati->emittente->datiREA->CapitaleSociale 	= $config->azienda->_capitalesociale->getVal();
	$dati->emittente->datiREA->SocioUnicoBolean 		= $config->azienda->_sociounicoBolean->getVal();//"SU";
	$dati->emittente->datiREA->StatoLiquidazioneBolean = $config->azienda->_inliquidazioneBolean->getVal();//"LN";
	
	/* DESTINATARIO */
	$cliente = $myFt->clientefornitore_codice->extend();
	//print_r($cliente);
	$dati->destinatario 						=  new stdClass();
	$dati->destinatario->codiceSDI 				= $cliente->codiceSDI->getVal();
	$dati->destinatario->pec 					= $cliente->pec->getVal();

	$dati->destinatario->partitaIvaNazione 		= $cliente->nazione->getVal();
	$dati->destinatario->partitaIvaCodice 		= $cliente->piva->getVal();
	$dati->destinatario->codiceFiscale 			= $cliente->codfiscale->getVal();
	$dati->destinatario->ragioneSociale 		= htmlentities($cliente->ragionesociale->getVal());
	$dati->destinatario->sede = new stdClass();
	$dati->destinatario->sede->via 				= htmlentities($cliente->via->getVal());
	$dati->destinatario->sede->paese 			= htmlentities($cliente->paese->getVal());
	$dati->destinatario->sede->citta 			= htmlentities($cliente->provincia->getVal());
	$dati->destinatario->sede->cap 				= $cliente->cap->getVal();
	$dati->destinatario->sede->nazione 			= $cliente->nazione->getVal();
	
	/* RIFERIMENTI PER NOTE DI ACCREDITO */
	$dati->riferientinotacredito =  new stdClass();
	$dati->riferientinotacredito->rifNsDDT = array();
	$dati->riferientinotacredito->rifNsFT = array();
	$dati->riferientinotacredito->rifLoroDDT = array();
	
	/* DATI PAGAMENTO */ 
	/*
	MP01 = contanti 
	MP02 = assegno 
	MP03 = assegno circolare 
	MP04 = contanti presso Tesoreria 
	MP05 = bonifico 
	MP06 = vaglia cambiario 
	MP07 = bollettino bancario 
	MP08 = carta di pagamento 
	MP09 = RID 
	MP10 = RID utenze 
	MP11 = RID veloce 
	MP12 = Riba 
	MP13 = MAV 
	MP14 = quietanza erario stato 
	MP15 = giroconto su conti di contabilità speciale 
	MP16 = domiciliazione bancaria 
	MP17 = domiciliazione postale  
	MP18 = bollettino di c/c postale  
	MP19 = SEPA Direct Debit 
	MP20 = SEPA Direct Debit CORE 
	MP21 = SEPA Direct Debit B2B 
	MP22 = Trattenuta su somme già riscosse 
	*/
	$dati->fattura->pagamento = new stdClass();
	$dati->fattura->pagamento->modalita=$myFt->pagamentomodalita_codice->extend()->codiceSDI->getVal();
	$dati->fattura->pagamento->iban=$myFt->banca_codice->extend()->descrizione->getVal();
	
	/* RIGHE */ 
	$dati->fattura->righe = array();
	/*
	$dati->fattura->righe[0] = new stdClass();
	$dati->fattura->righe[0]->numero ='';
	$dati->fattura->righe[0]->cod_articolo = '';
	$dati->fattura->righe[0]->descrizione = '';
	$dati->fattura->righe[0]->unita_misura = '';
	$dati->fattura->righe[0]->prezzo = '';
	$dati->fattura->righe[0]->imponibile = '';
	$dati->fattura->righe[0]->importo_iva = '';
	$dati->fattura->righe[0]->importo_totale = '';
	$dati->fattura->righe[0]->colli = '';
	$dati->fattura->righe[0]->quantita = '';
	$dati->fattura->righe[0]->cod_iva = '';
	*/
	$dati->riferimentoDdt = array();
	$dati->castellettoiva = array();

	$dati->contaRighe = 0;

	$righe = $myFt->getRighe();
	$righe->iterate(function($riga, $dati){
		//$riga=$myFt->riga[$key];
		$dati->contaRighe++;
		$contaRighe = $dati->contaRighe;
		$codiva = $riga->iva_codice->extend();
		$imponibile = (comma2dot($riga->prezzo->valore)) * (comma2dot($riga->pesonetto->getVal()));
		
		//si tratta di una normale riga appartenente ad un ddt
		$dati->fattura->righe[$contaRighe] = new stdClass();
		$dati->fattura->righe[$contaRighe]->numero =$contaRighe;
		$dati->fattura->righe[$contaRighe]->cod_articolo = $riga->articolo_codice->getVal();
		$dati->fattura->righe[$contaRighe]->descrizione = htmlentities($riga->articolo_codice->extend()->descrizione->getVal());
		$dati->fattura->righe[$contaRighe]->unita_misura = $riga->um_codice->extend()->descrizione->getVal();
		$dati->fattura->righe[$contaRighe]->prezzo = formatImporto(comma2dot($riga->prezzo->valore));
		$dati->fattura->righe[$contaRighe]->imponibile = formatImporto($imponibile);
		$dati->fattura->righe[$contaRighe]->importo_iva = $imponibile * comma2dot($codiva->aliquota->getVal());
		$dati->fattura->righe[$contaRighe]->importo_totale = $dati->fattura->righe[$contaRighe]->imponibile + $dati->fattura->righe[$contaRighe]->importo_iva;
		$dati->fattura->righe[$contaRighe]->colli = ($riga->colli->getVal()*1>0 ? $riga->colli->getFormatted(0) : '');
		$dati->fattura->righe[$contaRighe]->quantita = formatImporto(str_replace(',','.',$riga->pesonetto->valore));
		$dati->fattura->righe[$contaRighe]->cod_iva = formatImporto($riga->iva_codice->getVal());
		
		//dati iva
		$codiva = $riga->iva_codice->extend();
		
		if (!property_exists($dati, 'castellettoiva')){
			$dati->castellettoiva = array();
		}
		
		if (!array_key_exists($codiva->codiceSDI->getVal(), $dati->castellettoiva)){
			$dati->castellettoiva[$codiva->codiceSDI->getVal()] = new stdClass();
		}
		$dati->castellettoiva[$codiva->codiceSDI->getVal()]->codiceiva = $codiva->codiceSDI->getVal();
		$dati->castellettoiva[$codiva->codiceSDI->getVal()]->aliquota = comma2dot($codiva->aliquota->getVal())*100;
		@$dati->castellettoiva[$codiva->codiceSDI->getVal()]->imponibile +=  $imponibile;
		@$dati->castellettoiva[$codiva->codiceSDI->getVal()]->imposta = round($dati->castellettoiva[$codiva->codiceSDI->getVal()]->imponibile*comma2dot($codiva->aliquota->getVal()), 2);
		
		
		//riferimento ddt
		$ddt = $riga->ddt_id->extend();
		$arrKey = '#'.$ddt->numero->getVal().'#'.$ddt->data->getVal().'#';
		if(!array_key_exists($arrKey, $dati->riferimentoDdt)){
			$dati->riferimentoDdt[$arrKey] = new StdClass();
			$dati->riferimentoDdt[$arrKey]->numero = $ddt->numero->getVal();
			$dati->riferimentoDdt[$arrKey]->data = formatDate('dd/mm/yyyy','yyyy-mm-dd', $ddt->data->getVal());
			$dati->riferimentoDdt[$arrKey]->riferimentoRighe = array();
			$dati->riferimentoDdt[$arrKey]->riferimentoRighe[]=$contaRighe;
		}else{
			$dati->riferimentoDdt[$arrKey]->riferimentoRighe[]=$contaRighe;
		}

		//se si tratta di una riga di sconto
			//questa riga (di sconto, non si riferisce ad alcun ddt)
			/*
			SC = sconto
			PR = premio
			AB = abbuono
			AC = spesa accessori
			*/
			//$dati->fattura->righe[$contaRighe]->tipocessioneprestazione ='SC';
		/*
		}else if (strpos($riga->descrizione->getVal(), 'PROVVIGIONE') !== false) {
		}else if (strpos($riga->descrizione->getVal(), 'COMMISSIONE') !== false) {
		$dati->fattura->righe[$contaRighe]->tipocessioneprestazione ='AC';
		*/
		//print_r($dati);

	}, $dati);
	
	//$myFt->saveUsedProgressivoInvioSDI();
	
	return $dati;
}

/*==============================================================================
 GENERAZIONE DELLA FATTURA VERA E PROPRIA
==============================================================================*/
function generaXmlDaDatiFattura($dati, $urlFileSaving=''){

	$xml = new SimpleXMLElement('<p:p:FatturaElettronica/>');
	//$xml = new SimpleXMLElement('<p:FatturaElettronica/>');
	//$xml = new SimpleXMLElement('<FatturaElettronica/>');
	$xml->addAttribute('xmlns:xmlns:ds',"http://www.w3.org/2000/09/xmldsig#");
	$xml->addAttribute('xmlns:xmlns:p',"http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2");
	$xml->addAttribute('xmlns:xmlns:xsi',"http://www.w3.org/2001/XMLSchema-instance");
	$xml->addAttribute('versione',"FPR12");
	$xml->addAttribute('xsi:xsi:schemaLocation',"http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2/Schema_del_file_xml_FatturaPA_versione_1.2.xsd");

	/*
	$xml->addAttribute('version', '1.0');
	$xml->addAttribute('encoding', 'UTF-8');
	*/
	$last = $xml->addChild('FatturaElettronicaHeader');
		$last = $last->addChild('DatiTrasmissione');
			$last = $last->addChild('IdTrasmittente');
				$last->addChild('IdPaese',$dati->emittente->partitaIvaNazione);
				$last->addChild('IdCodice',$dati->emittente->codiceFiscale);

	$last = $xml->FatturaElettronicaHeader->DatiTrasmissione;
		$last->addChild('ProgressivoInvio',$dati->ProgressivoInvio);
		$last->addChild('FormatoTrasmissione',$dati->FormatoTrasmissione);
		
		//mi sa che è un controllo duplicato con quello del blocco "if/elseif" qua sotto
		if($dati->destinatario->codiceSDI == '' && $dati->destinatario->pec ==''){
			exit("Non trovo ne un codice ne un INDIRIZZO PEC da utilizzare");
		}
		
		if($dati->destinatario->codiceSDI != ''){
			//se ho specificato un codice destinatario lo uso
			$last->addChild('CodiceDestinatario',$dati->destinatario->codiceSDI);
		}else if($dati->destinatario->pec!=''){
			//se non ho specificato un codice destinatario ma ho un indirizzo pec uso il codice destinatario "0000000"
			$last->addChild('CodiceDestinatario','0000000');
			$last->addChild('PECDestinatario',$dati->destinatario->pec);
		}else{
			exit("Non trovo ne un codice ne un INDIRIZZO PEC da utilizzare");
		}
		
		/*
		if($dati->destinatario->pec!=''){
			if($dati->destinatario->codiceSDI==''){
			}
		}
		*/

		
		
	$last = $xml->FatturaElettronicaHeader->addChild('CedentePrestatore');
		$last = $last->addChild('DatiAnagrafici');
			$last = $last->addChild('IdFiscaleIVA');
					$last->addChild('IdPaese',$dati->emittente->partitaIvaNazione);
					$last->addChild('IdCodice',$dati->emittente->partitaIvaCodice);
			
			$xml->FatturaElettronicaHeader->CedentePrestatore->DatiAnagrafici->addChild('CodiceFiscale', $dati->emittente->codiceFiscale);
			
			$last = $xml->FatturaElettronicaHeader->CedentePrestatore->DatiAnagrafici->addChild('Anagrafica');
				$last->addChild('Denominazione',  $dati->emittente->ragioneSociale); 
			$last = $xml->FatturaElettronicaHeader->CedentePrestatore->DatiAnagrafici->addChild('RegimeFiscale', $dati->emittente->regimeFiscale);
		$last = $xml->FatturaElettronicaHeader->CedentePrestatore->addChild('Sede');
			$last->addChild('Indirizzo', $dati->emittente->sede->via);
			$last->addChild('CAP', $dati->emittente->sede->cap);
			$last->addChild('Comune', $dati->emittente->sede->paese);
			$last->addChild('Provincia', $dati->emittente->sede->citta);
			$last->addChild('Nazione', $dati->emittente->sede->nazione);

			if(false){
				$last = $xml->FatturaElettronicaHeader->CedentePrestatore->addChild('IscrizioneREA');
					$last->addChild('Ufficio',$dati->emittente->datiREA->Ufficio);
					$last->addChild('NumeroREA',$dati->emittente->datiREA->NumeroREA);
					if($dati->emittente->datiREA->CapitaleSociale != ''){
						$last->addChild('CapitaleSociale',$dati->emittente->datiREA->CapitaleSociale);
					}
					if($dati->emittente->datiREA->SocioUnicoBolean == TRUE){
						$last->addChild('SocioUnico',"SU");
					}
					if($dati->emittente->datiREA->StatoLiquidazioneBolean == TRUE){
						$last->addChild('StatoLiquidazione',"LN");
					}
			}
			
	$last = $xml->FatturaElettronicaHeader->addChild('CessionarioCommittente');
		$last = $last->addChild('DatiAnagrafici');
		
		//se azienda
			$last = $last->addChild('IdFiscaleIVA');
				$last->addChild('IdPaese',$dati->destinatario->partitaIvaNazione);
				$last->addChild('IdCodice',$dati->destinatario->partitaIvaCodice);
			if($dati->destinatario->codiceFiscale!=''){
				$xml->FatturaElettronicaHeader->CessionarioCommittente->DatiAnagrafici->addChild('CodiceFiscale', $dati->destinatario->codiceFiscale);
			}

			$last = $xml->FatturaElettronicaHeader->CessionarioCommittente->DatiAnagrafici->addChild('Anagrafica');
				$last->addChild('Denominazione',  $dati->destinatario->ragioneSociale); 
		$last = $xml->FatturaElettronicaHeader->CessionarioCommittente->addChild('Sede');
			$last->addChild('Indirizzo', $dati->destinatario->sede->via);
			$last->addChild('CAP', $dati->destinatario->sede->cap);
			$last->addChild('Comune', $dati->destinatario->sede->paese);
			$last->addChild('Provincia', $dati->destinatario->sede->citta);
			$last->addChild('Nazione', $dati->destinatario->sede->nazione);


	$last = $xml->addChild('FatturaElettronicaBody');
		$last = $last->addChild('DatiGenerali')->addChild('DatiGeneraliDocumento');
			$last->addChild('TipoDocumento',$dati->fattura->tipo);
			$last->addChild('Divisa',$dati->fattura->divisa);
			$last->addChild('Data',$dati->fattura->data);
			$last->addChild('Numero',$dati->fattura->numero);
			$last->addChild('ImportoTotaleDocumento',$dati->fattura->importo); //per il totale documente considero il valore assoluto (nelle note di accredito mi uscia a meno)
			$last->addChild('Causale','Contributo CONAI assolto ove dovuto.');
			$last->addChild('Causale',"Assolve gli obblighi di cui all'articolo 62, comma 1, del decreto legge 24 gennaio 2012, n. 1, convertito, con modificazioni, dalla legge 24 marzo 2012, n. 27");
			/* non abbligatorio, lasciamo stare?
			$last->addChild('Causale',$dati->fattura->causale);
			*/
			
			/*va aggiunto il riferimento ad altre fatture*/
			/*DatiFattureCollegate*/

	
		//SE NOTA DI ACCREDITO
		if($dati->fattura->tipo=='TD04'){
		//print_r($dati->riferientinotacredito->rifNsDDT);
		//print_r($dati->riferientinotacredito->rifLoroDDT);
		//print_r($dati->riferientinotacredito->rifNsFT);
		
			//ns fattura
			$last = $xml->FatturaElettronicaBody->DatiGenerali;
			foreach ($dati->riferientinotacredito->rifNsFT as $key => $ft){
				$last =  $xml->FatturaElettronicaBody->DatiGenerali->addChild('DatiFattureCollegate');
				$last->addChild('IdDocumento', $ft->numero);
				$last->addChild('Data', formatDate('dd.mm.yyyy','yyyy-mm-dd',$ft->data));
				foreach ($ft->riferimentoRighe as $key2 => $riferimentoRiga){
					$last->addChild('RiferimentoNumeroLinea', $riferimentoRiga); /*non va valorizato se c'è solo 1 ddt*/
				}
			}
			//ns ddt
			$last = $xml->FatturaElettronicaBody->DatiGenerali;
			foreach ($dati->riferientinotacredito->rifNsDDT as $key => $ddt){
				$last =  $xml->FatturaElettronicaBody->DatiGenerali->addChild('DatiDDT');
				$last->addChild('NumeroDDT', $ddt->numero);
				$last->addChild('DataDDT', formatDate('dd.mm.yyyy','yyyy-mm-dd',$ddt->data));
				foreach ($ddt->riferimentoRighe as $key2 => $riferimentoRiga){
					$last->addChild('RiferimentoNumeroLinea', $riferimentoRiga); /*non va valorizato se c'è solo 1 ddt*/
				}
			}
			//loro ddt
			$last = $xml->FatturaElettronicaBody->DatiGenerali;
			foreach ($dati->riferientinotacredito->rifLoroDDT as $key => $ddt){
				$last =  $xml->FatturaElettronicaBody->DatiGenerali->addChild('DatiDDT');
				$last->addChild('NumeroDDT', $ddt->numero);
				$last->addChild('DataDDT', formatDate('dd.mm.yyyy','yyyy-mm-dd',$ddt->data));
				foreach ($ddt->riferimentoRighe as $key2 => $riferimentoRiga){
					$last->addChild('RiferimentoNumeroLinea', $riferimentoRiga); /*non va valorizato se c'è solo 1 ddt*/
				}
			}
		}
		
		//da ripetere per ogni ddt
		foreach ($dati->riferimentoDdt as $key => $ddt){
			$last =  $xml->FatturaElettronicaBody->DatiGenerali->addChild('DatiDDT');
					$last->addChild('NumeroDDT', $ddt->numero);
					$last->addChild('DataDDT', $ddt->data);
					foreach ($ddt->riferimentoRighe as $key2 => $riferimentoRiga){
						$last->addChild('RiferimentoNumeroLinea', $riferimentoRiga); /*non va valorizato se c'è solo 1 ddt*/
					}
		}
		
		
		
		$last = $xml->FatturaElettronicaBody->addChild('DatiBeniServizi');
		
		
		//da ripetere per ogni riga
		foreach ($dati->fattura->righe as $key => $riga){
				$last = $xml->FatturaElettronicaBody->DatiBeniServizi->addChild('DettaglioLinee');
				$last->addChild('NumeroLinea',		$riga->numero);

				if(property_exists ($riga,'tipocessioneprestazione')){
					$last->addChild('TipoCessionePrestazione',	$riga->tipocessioneprestazione);
				}

				$last->addChild('Descrizione',		$riga->descrizione);
				$last->addChild('Quantita',			$riga->quantita);
				$last->addChild('UnitaMisura',		$riga->unita_misura);
				$last->addChild('PrezzoUnitario',	$riga->prezzo);
				$last->addChild('PrezzoTotale',		$riga->imponibile);
				$last->addChild('AliquotaIVA',		$riga->cod_iva);

		}
		
		


		//riepilogo dati fattura (per ogni aliquota)

		//$imponibili=$myFt->calcolaTotaliImponibiliIva();
	//print_r($imponibili);
		
		foreach ($dati->castellettoiva as $rigaiva){
			/*
			$iva=new CausaleIva(array('codice'=>(string)$codIva));
			//$codIva
			$descrizioneIva=$iva->descrizione->getVal();
			$imponibileIva=$val['imponibile'];
			$importoIva=$val['importo_iva'];
			*/

			$last = $xml->FatturaElettronicaBody->DatiBeniServizi->addChild('DatiRiepilogo');
			$last->addChild('AliquotaIVA',formatImporto($rigaiva->aliquota));
			$last->addChild('ImponibileImporto',formatImporto(round($rigaiva->imponibile,2)));
			$last->addChild('Imposta',formatImporto($rigaiva->imposta));
			$last->addChild('EsigibilitaIVA','I');//immediata... potrebbe essere differita
			
		}
		
		//
		$last = $xml->FatturaElettronicaBody->addChild('DatiPagamento');
			/*
			TP01   pagamento a rate 
			TP02   pagamento completo 
			TP03   anticipo 
			*/
			$last->addChild('CondizioniPagamento','TP02');
			$last = $last->addChild('DettaglioPagamento');
				/*
				$ft->cod_pagamento->extend()->descrizione->getVal()
				

				*/
				if($dati->fattura->pagamento->modalita !=''){
					$last->addChild('ModalitaPagamento',$dati->fattura->pagamento->modalita);
//todo					$last->addChild('DataScadenzaPagamento',formatDate('dd/mm/yyyy','yyyy-mm-dd',$myFt->getScadenzaPagamento())); /*todo : siamo sicuri di volerla inserire?*/
					$last->addChild('ImportoPagamento',formatImporto($dati->fattura->importo));
					//se bonifico mostro le coordinate
					if($dati->fattura->pagamento->modalita=='MP05'){
						if($dati->fattura->pagamento->iban==''){
							exit("Modalita di pagamento 'bonifico' ma nessuna coordinata specificata");
						}
						$last->addChild('IBAN',$dati->fattura->pagamento->iban);
					}
				}else{
					//$log->warn("Nessuna modalita di pagamento indicata");	
				}

	$xmlDocument = new DOMDocument('1.0');
	$xmlDocument->preserveWhiteSpace = false;
	$xmlDocument->formatOutput = true;
	$xmlDocument->loadXML($xml->asXML());

	//validate the XML file before doing something else ith it
	if (!$xmlDocument->schemaValidate(realpath($_SERVER["DOCUMENT_ROOT"]).'/webstore/externalData/Schema_del_file_xml_FatturaPA_versione_1.2.xsd')) {
		error_reporting(-1);
		$xmlDocument->schemaValidate(realpath($_SERVER["DOCUMENT_ROOT"]).'/webstore/externalData/Schema_del_file_xml_FatturaPA_versione_1.2.xsd');
		print '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
	}else{
		//if I specified a fileURL save all into that file
		if($urlFileSaving!=''){
			$xmlDocument->save($urlFileSaving);
		}else{
			//or else I suppose I only want to see the content
			//display it
	//		$filename=$myFt->getXmlFileName();
			header("Content-disposition: inline; filename=".$urlFileSaving);
			header('Content-type: text/xml');
			echo $xmlDocument->saveXML();
		}
	}
	//no error so far we are good ?
	return true;
}
?>
