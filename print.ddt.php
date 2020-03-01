<?php
/* -------------------------------------------------------------------------------------------------------
	Questa libreria esegue la stampa di un DDT
	La devo adattare per ricevere in input un DDT completo
	attualmente i campi sono tutti hardcoded
	e lei ne prepara la stampa
----------------------------------------------------------------------------------------------------------
*/

/*
TODO:
- verifica per più di una pagina
- modifica se presente solo destinatario e non destinazione
- modifica per orario di stampa
- modifica per causale ** c/commissione
*/
//error_reporting(0); //0=spento || -1=acceso
function buildEmptyModule($pdf){
	global $config;

	$style='';
	$def_font='helvetica';
	$def_size=8;
	//$def_verde= array(85,190,180);//SCURO
	//$def_verde= array(168,236,134);//CHIARO
	$def_verde= array(66, 134, 244);
	$def_bianco= array(999,999,999);
	$def_intestazione_colonne = '14, 57, 124';
	$def_intestazione_varie = '4, 19, 86';  
	/*##############################
	  #   RIQUADRI                 #  
	  ##############################*/
	//stile della linea dei riquadri
	$pdf->SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $def_verde));
	//RIQUADRO DESTINATARIO
	$pdf->RoundedRect($x=107, $y=24, $w=87, $h=32.6, 2.0, '1111', 'DF', $style, $def_bianco);

	//RIQUADRO NUMERO DOCUMENTO
	$pdf->RoundedRect($x=17.5, $y=56.5, $w=22.8, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO DATA DOCUMENTO
	$pdf->RoundedRect($x=40.3, $y=56.5, $w=43, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO TIPOMERCE
	$pdf->RoundedRect($x=83.3, $y=56.5, $w=110.7, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);

	//RIQUADRO CAUSALE DEL TRASPORTO
	$pdf->RoundedRect($x=17.5, $y=64.5, $w=50.8, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO CAUSALE A MEZZO
	$pdf->RoundedRect($x=68.3, $y=64.5, $w=51.8, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO INIZIO TRASPORTO
	$pdf->RoundedRect($x=120.1, $y=64.5, $w=59, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO PAGINA
	$pdf->RoundedRect($x=179.1, $y=64.5, $w=14.9, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);

	//RIQUADRO COD.ARTICOLO
	$pdf->RoundedRect($x=17.5, $y=72.5, $w=18.5, $h=153, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO DESCRIZIONE BENI
	$pdf->RoundedRect($x=36, $y=72.5, $w=84, $h=153, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO PREZZO
	$pdf->RoundedRect($x=120, $y=72.5, $w=14, $h=153, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO UM
	$pdf->RoundedRect($x=134, $y=72.5, $w=7, $h=153, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO COLLI
	$pdf->RoundedRect($x=141, $y=72.5, $w=11, $h=153, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO PESO LORDO
	$pdf->RoundedRect($x=152, $y=72.5, $w=16.5, $h=153, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO PESO NETTO
	$pdf->RoundedRect($x=168.5, $y=72.5, $w=16.5, $h=153, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO TARA
	$pdf->RoundedRect($x=185, $y=72.5, $w=9, $h=153, 2.0, '1111', 'DF', $style, $def_bianco);

	//RIQUADRO ASPETTO DEI BENI
	$pdf->RoundedRect($x=17.5, $y=225.5, $w=134.5, $h=7, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO TOT COLLI
	$pdf->RoundedRect($x=152, $y=225.5, $w=21, $h=7, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO TOT PESO
	$pdf->RoundedRect($x=173, $y=225.5, $w=21, $h=7, 2.0, '1111', 'DF', $style, $def_bianco);
	
	//RIQUADRO FIRMA VETTORE
	$pdf->RoundedRect($x=17.5, $y=232.5, $w=116.5, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO FIRMA DEL CONDUCENTE
	$pdf->RoundedRect($x=134, $y=232.5, $w=60, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);

	//RIQUADRO INCARICATO DEL TRASPORTO
	$pdf->RoundedRect($x=17.5, $y=240.5, $w=116.5, $h=11, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO DATA E ORA DEL RITIRO
	$pdf->RoundedRect($x=134, $y=240.5, $w=60, $h=11, 2.0, '1111', 'DF', $style, $def_bianco);

	//RIQUADRO ANNOTAZIONI
	$pdf->RoundedRect($x=17.5, $y=251.5, $w=116.5, $h=11, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO FIRMA DESTINATARIO
	$pdf->RoundedRect($x=134, $y=251.5, $w=60, $h=11, 2.0, '1111', 'DF', $style, $def_bianco);

	/*##############################
	  #   SCRITTE RIQUADRI         #  
	  ##############################*/
	$def_font='helvetica';
	$def_size=4.5;

	//
	$pdf->SetTextColor(4, 19, 86);
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->Text($x=107, $y=24+0.5, "DESTINATARIO");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(14, 57, 124);
	$pdf->Text($x=17.5, $y=56.5+0.5, "NUM.DOC.");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(14, 57, 124);
	$pdf->Text($x=40.3, $y=56.5+0.5, "DATA.DOC.");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(4, 19, 86);
	$pdf->Text($x=83.3, $y=56.5+0.5, "TIPO MERCE");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(4, 19, 86);
	$pdf->Text($x=17.5, $y=64.5+0.5, "CAUSALE DEL TRASPORTO");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(4, 19, 86);
	$pdf->Text($x=68.3, $y=64.5+0.5, "A MEZZO");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(4, 19, 86);
	$pdf->Text($x=120.1, $y=64.5+0.5, "INIZIO TRASPORTO");
	//----DIFF---
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(4, 19, 86);
	$pdf->Text($x=120.1, $y=69.5+0.5, "DATA");
	//----DIFF---
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(4, 19, 86);
	$pdf->Text($x=154.1, $y=69.5+0.5, "ORA");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(4, 19, 86);
	$pdf->Text($x=180.1, $y=64.5+0.5, "PAGINA");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(14, 57, 124);
	$pdf->Text($x=20, $y=72.5+0.5, "COD.ARTICOLO");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(14, 57, 124);
	$pdf->Text($x=60, $y=72.5+0.5, "DESCRIZIONE DEI BENI (Natura - Qualita')");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(14, 57, 124);
	$pdf->Text($x=120.5, $y=72.5+0.5, "PREZZO");

	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(14, 57, 124);
	$pdf->Text($x=134.5, $y=72.5+0.5, "U.M.");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(14, 57, 124);
	$pdf->Text($x=143, $y=72.5+0.5, "COLLI");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(14, 57, 124);
	$pdf->Text($x=153.8, $y=72.5+0.5, "PESO LORDO");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(14, 57, 124);
	$pdf->Text($x=170.5, $y=72.5+0.5, "PESO NETTO");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(14, 57, 124);
	$pdf->Text($x=186.5, $y=72.5+0.5, "TARA");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(4, 19, 86);
	$pdf->Text($x=17.5, $y=225.5+0.5, "ASPETTO ESTERIORE DEI BENI");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(4, 19, 86);
	$pdf->Text($x=152, $y=225.5+0.5, "N. COLLI");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(4, 19, 86);
	$pdf->Text($x=173, $y=225.5+0.5, "PESO");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(4, 19, 86);
	$pdf->Text($x=17.5, $y=232.5+0.5, "FIRMA DEL VETTORE");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(14, 57, 124);
	$pdf->Text($x=134, $y=232.5+0.5, "FIRMA DEL CONDUCENTE");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(4, 19, 86);
	$pdf->Text($x=17.5, $y=240.5+0.5, "INCARICATO DEL TRASPORTO: DITTA, RESIDENZA, O DOMICILIO (Comune, Via, N.)");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(4, 19, 86);
	$pdf->Text($x=134, $y=240.5+0.5, "DATA E ORA DEL RITIRO");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(4, 19, 86);
	$pdf->Text($x=17.5, $y=251.5+0.5, "ANNOTAZIONI - VARIAZIONI");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(14, 57, 124);
	$pdf->Text($x=134, $y=251.5+0.5, "FIRMA DEL DESTINATARIO");
	
	/*##############################
	  #   INTESTAZIONE         #  
	  ##############################*/
  	$azienda=$GLOBALS['config']->azienda;
	$html= '<img src="'.$azienda->_logo->getVal().'" width="202" height="87">';
	$pdf->writeHTMLCell($w=0, $h=0, $x='17.5', $y='5', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	  
	$html= '<img src="'.$azienda->_logobg->getVal().'" width="418" height="332">';
	$pdf->writeHTMLCell($w=0, $h=0, $x='35', $y='115', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

	  
	$pdf->SetFont($def_font, 'B', $def_size+4);
	$pdf->SetTextColor(0,0,0);
	$pdf->Text($x=17.5, $y=33, $config->azienda->ragionesociale->getVal()); 

	$pdf->SetFont($def_font, '', $def_size+2);
	$pdf->SetTextColor(0,0,0);
	$pdf->Text($x=17.5, $y=37, ($config->azienda->via->getVal())." - ".($config->azienda->cap->getVal())." ".($config->azienda->paese->getVal())." (".($config->azienda->provincia->getVal()).")");
	if($config->azienda->fax->getVal()!=''){
		$pdf->Text($x=17.5, $y=40, "Telefono ".$config->azienda->telefono->getVal()." - Fax ".$config->azienda->fax->getVal());
	}else{
		$pdf->Text($x=17.5, $y=40, "Telefono ".$config->azienda->telefono->getVal());
	}
	if($config->azienda->_capitalesociale->getVal() !=''){
		$pdf->Text($x=17.5, $y=43, "Capitale Sociale € ".$config->azienda->_capitalesociale->getVal()." i.v.");
	}
//	$pdf->Text($x=17.5, $y=46, "R.E.A. ".$config->azienda->_registroimprese->getVal()." - Reg.Imprese di Verona ".$config->azienda->_rea->getVal());
///	$pdf->Text($x=17.5, $y=49, "Reg.Imprese di Verona".$config->azienda->_rea->getVal().", Codice Fiscale".$config->azienda->cod_fiscale->getVal()."Partita IVA ".$config->azienda->p_iva->getVal());
	$pdf->Text($x=17.5, $y=49, "Codice Fiscale ".$config->azienda->codfiscale->getVal()." - Partita IVA ".$config->azienda->piva->getVal());
	if($config->azienda->_bndoo->getVal() != ''){
		$pdf->Text($x=17.5, $y=52, "BNDOO n° ".$config->azienda->_bndoo->getVal()." - Indirizzo PEC: ".$config->azienda->_emailpec->getVal());
	}else{
		$pdf->Text($x=17.5, $y=52, "Indirizzo PEC: ".$config->azienda->_emailpec->getVal());
	}
	
	/*##############################
	  #   SCRITTE VARIE            #  
	  ##############################*/
	// ddt
	$pdf->SetFont($def_font, 'B', $def_size+3);
	$pdf->SetTextColor(0,0,0);
	$pdf->Text($x=118, $y=18, "DOCUMENTO DI TRASPORTO");

	// ddt
	$pdf->SetFont($def_font, '', $def_size+1.5);
	$pdf->SetTextColor(4, 19, 86);
	$pdf->Text($x=158, $y=18, "(D.P.R. 472 del 18/08/96)");
	
	// reclami (sul fondo)
	$pdf->SetFont($def_font, 'B', $def_size+3);
	$pdf->SetTextColor(0,0,0);
	$pdf->Text($x=50, $y=270, "NON SI ACCETTANO RECLAMI DOPO L'USCITA DELLA MERCE DAL MAGAZZINO");
	
	// codice (sul lato sinistro
	$pdf->SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $def_bianco));
	$pdf->SetFont($def_font, 'B', $def_size+2);
	$pdf->StartTransform();
	$pdf->setXY(14,226.5);
	$pdf->Rotate(90);
	$pdf->Cell(0,0,'CODICE DI CONVERSIONE ALFA/NUMERICO D.M. 30.03.92: A=1 E=2 G=3 H=4 M=5 P=6 S=7 T=8 K=9 Z=0 ,=,',1,1,'L',0,'');
	$pdf->StopTransform();
	//$pdf->Text($x=100, $y=100, "CODICE DI CONVERSIONE ALFA/NUMERICO D.M. 30.03.92: A=1 E=2 G=3 H=4 M=5 P=6 S=7 T=8 K=9 Z=0 ,=,");
	
	/*##############################
	  #   TABELLE HACCP            #  
	  ##############################*/
	$pdf->SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
	//
	$pdf->setXY(120,182);
	$pdf->Cell ($w=24, $h=5, $txt=$config->azienda->ragionesociale->getVal(), $border=1, $ln=0, $align='C', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=FALSE, $calign='T', $valign='M');
	//
	$pdf->setXY(144,182);
	$pdf->Cell ($w=50, $h=5, $txt='TIMBRO CONTROLLO AZIEDALE', $border=1, $ln=0, $align='C', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	//
	$pdf->setXY(120,187);
	$pdf->Cell ($w=48.5, $h=5, $txt='CONTROLLO', $border=1, $ln=0, $align='C', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	//
	$pdf->setXY(168.5,187);
	$pdf->Cell ($w=12.75, $h=2, $txt='ESITO', $border=1, $ln=0, $align='C', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->setXY(168.5,190);
	$pdf->Cell ($w=6.375, $h=2.3, $txt='SI', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');

	$pdf->setXY(174.875,190);
	$pdf->Cell ($w=6.375, $h=2.3, $txt='NO', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->SetFont($def_font, 'B', $def_size+3);
	//
	$pdf->setXY(181.25,187);
	$pdf->Cell ($w=12.75, $h=5, $txt='FIRMA', $border=1, $ln=0, $align='C', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	//
	$pdf->setXY(120,192);
	$pdf->Cell ($w=48.5, $h=3, $txt='MERCE CONFORME AL\'ORDINE', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');

	//--------------------------------
	$pdf->setXY(120,195);
	$pdf->Cell ($w=48.5, $h=3, $txt='IMBALLAGGIO INTEGRO', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	//
	$pdf->setXY(120,198);
	$pdf->Cell ($w=48.5, $h=3, $txt='ETICHETTATURA CORRETTA', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	//
	$pdf->setXY(120,201);
	$pdf->Cell ($w=48.5, $h=3, $txt='LOTTO CORRETTO', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	//
	$pdf->setXY(120,204);
	$pdf->Cell ($w=48.5, $h=6, $txt="TEMPERATURA PRODOTTO", $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->Text($x=120, $y=207, "(ove applicabile)");
	//
	$pdf->setXY(120,210);
	$pdf->Cell ($w=48.5, $h=3, $txt='PULIZIA DELL\'AUTOMEZZO', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	//
	$pdf->setXY(120,213);
	$pdf->Cell ($w=48.5, $h=6, $txt='TEMPERATURA DELL\'AUTOMEZZO', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->Text($x=120, $y=216, "(ove applicabile)");
	//
	$pdf->setXY(120,219);
	$pdf->Cell ($w=48.5, $h=3, $txt='MERCE', $border=1, $ln=0, $align='C', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	
	$pdf->SetFont($def_font, 'B', $def_size+1);
	//
	$pdf->setXY(120,222);
	$pdf->Cell ($w=24.25, $h=3.5, $txt='Liberalizzata (      )', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	//
	$pdf->setXY(144.25,222);
	$pdf->Cell ($w=24.25, $h=3.5, $txt='In attesa di sblocco (      )', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	
	
	//si-no
	$pdf->setXY(168.5,192);
	$pdf->Cell ($w=6.375, $h=3, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->setXY(174.975,192);
	$pdf->Cell ($w=6.375, $h=3, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->setXY(181.35,192);
	$pdf->Cell ($w=12.75, $h=3, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	//si-no
	$pdf->setXY(168.5,195);
	$pdf->Cell ($w=6.375, $h=3, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->setXY(174.975,195);
	$pdf->Cell ($w=6.375, $h=3, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->setXY(181.35,195);
	$pdf->Cell ($w=12.75, $h=3, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	//si-no
	$pdf->setXY(168.5,198);
	$pdf->Cell ($w=6.375, $h=3, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->setXY(174.975,198);
	$pdf->Cell ($w=6.375, $h=3, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->setXY(181.35,198);
	$pdf->Cell ($w=12.75, $h=3, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	//si-no
	$pdf->setXY(168.5,201);
	$pdf->Cell ($w=6.375, $h=3, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->setXY(174.975,201);
	$pdf->Cell ($w=6.375, $h=3, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->setXY(181.35,201);
	$pdf->Cell ($w=12.75, $h=3, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->SetFont($def_font, 'B', $def_size+3);
	//°c
	$pdf->setXY(168.5,204);
	$pdf->Cell ($w=12.75, $h=6, $txt='C', $border=1, $ln=0, $align='R', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->setXY(181.35,204);
	$pdf->Cell ($w=12.75, $h=6, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	//si-no
	$pdf->setXY(168.5,210);
	$pdf->Cell ($w=6.375, $h=3, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->setXY(174.975,210);
	$pdf->Cell ($w=6.375, $h=3, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->setXY(181.35,210);
	$pdf->Cell ($w=12.75, $h=3, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	//°c
	$pdf->setXY(168.5,213);
	$pdf->Cell ($w=12.75, $h=6, $txt='°C', $border=1, $ln=0, $align='R', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->setXY(181.35,213);
	$pdf->Cell ($w=12.75, $h=6, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	//sigla
	$pdf->setXY(168.5,219);
	$pdf->Cell ($w=25.5, $h=6.5, $txt='', $border=1, $ln=0, $align='L', $fill=TRUE, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');

	
	$pdf->SetTextColor(0,0,0);
}

function addIntestazioneDdt ($pdf){
//	$html= '<img src="'.realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/'.'/img/ddt.svg" height="1040">';
//	$html= '<img src="'.realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/'.'/img/ddt.png" height="1040">';

	$pdf->writeHTMLCell($w=0, $h=0, $x='0', $y='0', $html='', $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

}

function addDestinatarioDdt ($ddt,$pdf){
	$mod=28;
	$riga=4;
	$def_font='helvetica';
	$def_size=7;
	
	$fromLeft=107;

	$destinatario = $ddt->clientefornitore_codice->extend();

	$pdf->SetFont($def_font, 'b', $def_size+3);
	$pdf->Text($fromLeft, 0*$riga+$mod, $destinatario->ragionesociale->getVal());
	$pdf->SetFont($def_font, '', $def_size+2);
	
	$pdf->Text($fromLeft, 1*$riga+$mod, $destinatario->via->getVal());
	$pdf->Text($fromLeft, 2*$riga+$mod, $destinatario->cap->getVal().' '.$destinatario->paese->getVal(). ' ('.$destinatario->provincia->getVal().')');
	//$pdf->SetFont($def_font, 'b', $def_size+1);
	//$pdf->Text($fromLeft, 3*$riga+$mod, 'Partitita IVA: '.$destinatario->p_iva->getVal());
	//$pdf->SetFont($def_font, '', $def_size);
	//$pdf->Text($fromLeft, 4*$riga+$mod, 'Codice Fiscale: '.$destinatario->cod_fiscale->getVal());

}
function addDestinazioneDdt ($ddt,$pdf){
	$mod=44;
	$riga=4;
	$def_font='helvetica';
	$def_size=7;

	$fromLeft=107;
	
	//HACK
	//$ddt->cod_destinazione->setVal('OROGE');

	$destinazione=$ddt->destinatario_codice->extend('clientefornitore_codice');

	$pdf->SetFont($def_font, '', $def_size+2);
	$html='<i style="text-align:center">- - - - - - - - - - -   DESTINAZIONE   - - - - - - - - - - -</i>';
	$pdf->writeHTMLCell($w=85, $h=4, $fromLeft, $mod-4, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='center', $autopadding=false);
	
	$pdf->SetFont($def_font, 'b', $def_size+3);
	$pdf->Text($fromLeft, 0*$riga+$mod, $destinazione->ragionesociale->getVal());
	$pdf->SetFont($def_font, '', $def_size+2);
	
	$pdf->Text($fromLeft, 1*$riga+$mod, $destinazione->via->getVal());
	$pdf->Text($fromLeft, 2*$riga+$mod, $destinazione->cap->getVal().' '.$destinazione->paese->getVal(). ' ('.$destinazione->provincia->getVal().')');
}

function generaPdfDdt($ddt){
	global $config;
	$massimoNumeroRigheCorpo=30;
	$def_font='helvetica';
	$def_size=12;

	// create new PDF document
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	//load some custom fonts
	//$pdf->addTTFfont(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/'.'/fonts/SourceSansPro-Regular.ttf', 'TrueTypeUnicode', '', 32);
	//$pdf->addTTFfont(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/'.'/fonts/ARIALN.TTF', 'TrueTypeUnicode', '', 32);
	//$pdf->AddFont('arial','',realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/fonts/ARIALN.php');
	//$pdf->AddFont('ArialNarrowB','B','ARIALNB.php');
	//$pdf->AddFont('ArialNarrowBI','BI','ARIALNBI.php');
	//$fontname = TCPDF_FONTS::addTTFfont(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/'.'/fonts/ARIALN.TTF', 'TrueTypeUnicode', '', 96);
	
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor($config->azienda->ragionesociale->getVal());
	$pdf->SetTitle('Documento di Trasporto');

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	//set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(0);
	$pdf->SetFooterMargin(0);

	// remove default footer
	$pdf->setPrintFooter(false);

	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, 0);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	//set some language-dependent strings
	@$pdf->setLanguageArray($l);

	//-----------------------------------------------------
	//   DDT PAGE
	//-----------------------------------------------------
	$pdf->AddPage();
	
buildEmptyModule($pdf);
	
	//immagine di sfondo del DDT inclusa l'intestazione
	addIntestazioneDdt($pdf);
	
	//destinatatio
	addDestinatarioDdt($ddt, $pdf);	
	
	//destinazione se diversa dal destinatario
	if($ddt->destinatario_codice->getVal()!=''){
		addDestinazioneDdt($ddt, $pdf);
	}	

	//
	$pdf->SetFont($def_font, '', $def_size);

	//numero
	$pdf->Text(18, 58, $ddt->numero->getVal());
	
	//data
	$pdf->Text(56, 58, $ddt->data->getVal());
	
	//pagina
	$pdf->Text(185, 58+8, '1/1');
	
	//causale del trasporto
	$pdf->Text(18, 58+8, $ddt->causale_codice->extend()->descrizione->getVal());

	//trasporto a mezzo
	$pdf->Text(69, 58+8, $ddt->mezzo_codice->extend()->descrizione->getVal());
	
	//data
	$printTime=time();
	
	//$pdf->Text(112, 58+8, date('d/m/Y',$printTime));//todo: rendere dinamico
	$pdf->Text(127, 58+8, $ddt->data->getVal());//todo: rendere dinamico
	
	//ora
	$pdf->Text(160, 58+8, date('H:i',$printTime));//todo: rendere dinamico
	
	//aspetto dei beni
	$pdf->Text(18, 58+8*21.2, 'VISIBILE');/*todo*/
	
	//totale colli
	$pdf->Text(155, 58+8*21.2, $ddt->getTotaleColli());
	
	//totale peso lordo
	$pdf->Text(175, 58+8*21.2, $ddt->getTotalePesoLordo());
	
	//note
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(18, 58+8*24.5, $ddt->note->getVal());
	//**********************************************************
	//**********************************************************
	$righeCorpoUsate=0;
	function MyOwnDdtRow($a1,$a2,$a3,$a4,$a5,$a6,$a7,$a8){
		global $righeCorpoUsate;
		$righeCorpoUsate++;
		$toRigth='style="text-align:rigth;" align="rigth"';

		$htmlo= "<tr>";
		$htmlo.= "<td width='70px;'>$a1</td>"; //ARTICOLO
		$htmlo.= "<td width='295px;'>$a2</td>"; //DESCRIZIONE
		$htmlo.= "<td width='40px;' $toRigth>$a3</td>"; //PREZZO
		$htmlo.= "<td width='25px;' $toRigth>$a4</td>"; //UM
		$htmlo.= "<td width='40px;' $toRigth>$a5</td>"; //COLLI
		$htmlo.= "<td width='56px;' $toRigth>$a6</td>"; //LORDO
		$htmlo.= "<td width='56px;' $toRigth>$a7</td>"; //NETTO
		$htmlo.= "<td  width='35px;' $toRigth>$a8</td>"; //TARA
		$htmlo.= "</tr>";
		return $htmlo;
	}

	$html = '<table style="border:0px solid #000000;margin:0px;padding:5px;">';
	//echo "\n\n<br><br> $html";
	//print_r($ddt->righe);
	
	/*
	//ricavo le righe
	$myddtrighe = new MyList(array( '_type'=>'Riga',
									'ddt_id'=>$ddt->id->getVal()
	));
	*/
	$myddtrighe = $ddt->getRighe();
	

	$html.=$myddtrighe->iterate(function($riga){
		$strResult.= MyOwnDdtRow($riga->articolo_codice->getVal(),
							$riga->articolo_codice->extend()->descrizione->getVal(),
							$riga->prezzo->getVal(),
							$riga->um_codice->getVal(),
							$riga->colli->getVal(),
							$riga->pesolordo->getVal(), //NETTO
							$riga->pesonetto->getVal(), //peso lordo
							$riga->tara->getVal() //todoTara
							);
		return $strResult;
	});
	$html.= '</table>';
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->writeHTMLCell($w=175, $h=10, $x=17, $y=75, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='right', $autopadding=false);

	//**********************************************************
	//**********************************************************
	//se la spedizione è con vettore stampo i suoi dati
	if ($ddt->mezzo_codice->getVal()=='03' || $ddt->mezzo_codice->getVal()=='04'){
		//$vettore=$ddt->cod_destinatario->extend()->cod_vettore->extend();
		
		//$destinatario=$ddt->destinatario_codice->extend();
		
		//MODIFICO IL VETTORE A MIO PIACIMENTO
		//$destinatario->cod_vettore->setVal('41');//02=translusia	24=facchini 14=ROCCO TRASPORTI
		
		//$vettore= $ddt->vettore_codice->extend();

		$vettore = new Clientefornitore($ddt->vettore_codice_getVal());
		$vettore->codice->setVal($ddt->vettore_codice_getVal());
		$vettore->extend();
		
		//si presenta il caso in cui la spedizione è stata fatta con vettore ma non sappiamo quale
		//perchè non ce ne è uno predefinito nel codice cliente quindi gliene assegnamo uno vuoto
		if($vettore==''){
			$vettore=new Vettore(array('_autoExtend'=>-1));
		}
		
		//imposto le dimensioni del font
		$pdf->SetFont($def_font, '', $def_size-3);

		//rag.sociale vettore
		$pdf->Text(18, 58+8*23.2, $vettore->ragionesociale->getVal());

		//indirizzo
		$pdf->Text(18, 58+8*23.7, $vettore->via->getVal().' - '.$vettore->paese->getVal());	

	}
	

	//inviamo il file pdf
	//$pdf->Output('DDT_'.$ddt->numero->getVal().'__'.$ddt->data->getVal().'.pdf', 'I');
	@$pdf->Output($GLOBALS['config']->pdfDir."/ddt/".$nomefile, 'I');
	$nomefile=$ddt->getPdfFileName();

	//@$pdf->Output($GLOBALS['config']->pdfDir."/ddt/test.pdf".$nomefile, 'F');
	//$pdf->Output("./test.pdf", 'F');
	//@$pdf->Output($GLOBALS['config']->pdfDir."/ddt/".$nomefile, 'F');
	@$pdf->Output($GLOBALS['config']->pdfDir."/ddt/".$nomefile, 'D');
}
?>
