$zindex=10;
//$MyTimer=0.3; //eseguo un ciclo ogni $MyTimer secondi
//timerID='';

/*--------------------------------------------------------------
-- MyWindowF
$opt['name']			-> Il nome della variabile che contiene l'oggetto finestra creato | valore [testo]
$opt['title']			-> Il titolo della finestra | valore [testo]
$opt['txt']				-> Il contenuto della finestra | valore [testo]
$opt['isVisible']		-> Indica se a finestra deve essere visualizzata o meno alla creazione | valore [true|false]
$opt['isTip']			-> Indica se a finestra deve avere o meno la freccetta tips | valore [true|false]
$opt['isPermanent']	-> Se impostato a true la finestra non viene chiusa ma solo nascosta | valore [true|false]
$opt['width']			-> La larghezza della finestra | valore [numero]
$opt['height']			-> L'altezza della finestra | valore [numero]
$opt['top']				-> Posizionamento dall'alto | valore [numero]
$opt['left']			-> Posizionamento dal sinistra | valore [numero]
$opt['autoCloseTime']-> Chiusura automatica onmouseout | valore [millisecondi]
*/
function MyWindow($opt){//name deve essere uguale al nome della variabile che contiene il nuovo oggetto
	this.opt=$opt;
	var test=$opt['name'];
	var _self=this;
	$zindex++;
	this.name=$opt['name'];
	this.chiudi= function(){
		this.windows.innerHTML="";
		return;
	};
	this.hide= function(){
		this.windows.style.visibility="hidden";
		return;
	};
	this.show= function(){
		this.windows.style.visibility="visible";
		return;
	};
	this.centra= function(){
		var MyWindowsHeight=this.windows.offsetHeight;
		var MyWindowsWidth=this.windows.offsetWidth;
		var browserHeight=getTop(document.getElementById('SpaceCalculator'));
		var browserWidth=getLeft(document.getElementById('SpaceCalculator'));
		var MyTop=(browserHeight-MyWindowsHeight)/2;
		var MyLeft=(browserWidth-MyWindowsWidth)/2;

		this.windows.style.top=MyTop;
		this.windows.style.left=MyLeft;
		return;
	};
	this.fadeOut= function(){
		this.windows.style.opacity=0.9;
		return;
	};
	this.fadeIn=function($to, $time){
		if($time){//se ho impostato un tempo calcolo quanti cicli servono
			var $numCicli=Math.round($time/$MyTimer)
		}else{//se non l'ho impostato allora vuole dire che voglio sia fatto subito
			var $numCicli=1;
		}
		var $step=$differenza/$numCicli;
		var $resta=$to-$step;
		for($ciclo=1;$ciclo<=$numCicli;$ciclo++){
			// opacity = (opacity == 100)?99.999:h;
			// IE/Win
			//this.windows.style.filter = "alpha(opacity:"+h+")";
			// Safari<1.2, Konqueror
			//this.windows.style.KHTMLOpacity = (this.windows.style.KHTMLOpacity+$step/100);
			// Older Mozilla and Firefox
			//this.windows.style.MozOpacity = (this.windows.style.MozOpacity+$step/100);
			// Safari 1.2, newer Firefox and Mozilla, CSS3
			//this.windows.style.opacity = $step*$ciclo;
			//h=h-Conf['DecrementoOpacita'];
			//alert($ciclo+'->'+this.windows.style.opacity);
		}
		if(timerID!=''){
			clearTimeout(timerID);
		}
		var timerID=setTimeout(this.fadeIn(),$MyTimer,$resta,$time-$MyTimer)
		return;
	};
	var $id='MyWindowsID_'+$opt['name'];

	/*Definisco tutti i tag html che mi serviranno per costruire la mia finestrella*/
	//contorno superore
	var MyTopSx= document.createElement("td");
	MyTopSx.setAttribute("class","TopSx");
	
	var MyTopDx= document.createElement("td");
	MyTopDx.setAttribute("class","TopDx");
	
	var MyBgTop= document.createElement("td");
	MyBgTop.setAttribute("class","BgTop");
	
	var MyTopTr=document.createElement("tr");
	MyTopTr.appendChild(MyTopSx);
	MyTopTr.appendChild(MyBgTop);
	MyTopTr.appendChild(MyTopDx);
	
	//contorno testo
	var MyBgDx= document.createElement("td");
	MyBgDx.setAttribute("class","BgDx");
	
	var MyBgSx= document.createElement("td");
	MyBgSx.setAttribute("class","BgSx");
	
	var MyBgMainContainer=document.createElement("div");
	MyBgMainContainer.setAttribute("class","main");
	
	MyBgMainContainer.innerHTML=$opt['txt'];

	//Eventuale barra del tittolo
	if($opt['title']!=null){
		var MyTitle=document.createElement("div");
		MyTitle.setAttribute("class","TitleBarTxt");
		MyTitle.innerHTML=$opt['title'];
		
		var MyCloseButtonImage=document.createElement("img");
		MyCloseButtonImage.setAttribute("src","./../oLibs/MyWindow/img/chiudi.png");
		MyCloseButtonImage.setAttribute("alt","X");
		
		var MyCloseButtonLink=document.createElement("a");
		MyCloseButtonLink.appendChild(MyCloseButtonImage);
		if($opt['isPermanent']==true){
			MyCloseButtonLink.onclick=function(){
				_self.hide();
			}
		}else{
			MyCloseButtonLink.onclick=function(){
				_self.chiudi();
			}
		}

		var MyCloseButton=document.createElement("div");
		MyCloseButton.appendChild(MyCloseButtonLink);
		MyCloseButton.setAttribute("class","closeButton");
		
		var MyTitleBar=document.createElement("div");
		MyTitleBar.setAttribute("class","TitleBar");
		MyTitleBar.appendChild(MyTitle);
		MyTitleBar.appendChild(MyCloseButton);
	}
	//fine barra del titolo continuo con la finestrella
	var MyBgMain= document.createElement("td");
	MyBgMain.setAttribute("class","mainBG");
	if($opt['title']){
		//se ho creato la barra del titolo la mostro
		MyBgMain.appendChild(MyTitleBar);
	}
	MyBgMain.appendChild(MyBgMainContainer);
	
	var MyBgTr=document.createElement("tr");
	MyBgTr.appendChild(MyBgSx);
	MyBgTr.appendChild(MyBgMain);
	MyBgTr.appendChild(MyBgDx);
	
	//contorno inferiore
	var MyBottomSx= document.createElement("td");
	MyBottomSx.setAttribute("class","BottomSx");
	
	var MyBottomDx= document.createElement("td");
	MyBottomDx.setAttribute("class","BottomDx");
	
	var MyBgBottom= document.createElement("td");
	MyBgBottom.setAttribute("class","BgBottom ");
	
	var MyBottomTr=document.createElement("tr");
	MyBottomTr.appendChild(MyBottomSx);
	MyBottomTr.appendChild(MyBgBottom);
	MyBottomTr.appendChild(MyBottomDx);
	
	//Tabella
	var MyTable=document.createElement("table");
	MyTable.setAttribute("class","MyTable");
	MyTable.setAttribute("cellspacing","0px");
	MyTable.appendChild(MyTopTr);
	MyTable.appendChild(MyBgTr);
	MyTable.appendChild(MyBottomTr);

	MyTable.style.zIndex=$zindex;
	if($opt['width']){
		MyTable.width=$opt['width']+'px';
	}
	if($opt['height']){
		MyTable.height=$opt['height']+'px';
		MyTable.style.maxheight=$opt['height']+'px';
	}

	//La aggiungo al body tenendola nascosta
	MyTable.style.visibility='hidden';
	MyTable.style.top=0;//imposto a zero per evitare sfarfallamenti con la barra di scorrimento su firefox
	MyTable.style.left=0;//idem come sopra
	document.body.appendChild(MyTable);

	//posiziono la finestrella al centro del browser
	var MyWindowsHeight=MyTable.offsetHeight;
	var MyWindowsWidth=MyTable.offsetWidth;
	//se mi sono passato qualche parametro per il posizionamento mi ricavo i dati
	if($opt['position']){
		switch ($opt['position']){
			case 'top-left':
				$opt['top']=getTop($opt['this']);
				$opt['left']=getLeft($opt['this']);
			break;
			case 'top-right':
				$opt['top']=getTop($opt['this']);
				$opt['left']=getLeft($opt['this'])+$opt['this'].offsetWidth-MyWindowsWidth;
			break;
			case 'bottom-left':
				$opt['top']=getTop($opt['this'])+$opt['this'].offsetHeight+MyWindowsHeight;
				$opt['left']=getLeft($opt['this']);
			break;
			case 'bottom-right':
				$opt['top']=getTop($opt['this'])+$opt['this'].offsetHeight+MyWindowsHeight;
				$opt['left']=getLeft($opt['this'])+$opt['this'].offsetWidth-MyWindowsWidth;
			break;
			default:
				$opt['top']=getTop($opt['this']);
				$opt['left']=getLeft($opt['this']);
			break;
		}
	}

	if(isNaN($opt['top']) || isNaN($opt['left'])){

		var browserHeight=getTop(document.getElementById('SpaceCalculator'));
		var browserWidth=getLeft(document.getElementById('SpaceCalculator'));

		var MyTop=(browserHeight-MyWindowsHeight)/2;
		var MyLeft=(browserWidth-MyWindowsWidth)/2;

	}else{
		var MyTop=$opt['top']-MyWindowsHeight;
		var MyLeft=$opt['left'];
	}
/*---------------------------------------------------------------------------*/
	if($opt['isTip']){//se si tratta di un tips
		//MyTop-=18; //Altezza
		//MyLeft+=54;//larghezza
	}
	MyTable.style.top=MyTop+'px';
	MyTable.style.left=MyLeft+'px';

	//finito di posizionarla posso finalmente mostrarla
	if($opt['isVisible']!=false){
		MyTable.style.visibility='visible';
	}

	if($opt['title']){//se c'è un titolo e quindi una barra del titolo allora probabilmente voglio che la finestra sia draggabile
		//aggiongo il drag and drop
		var theHandle = MyTitleBar;
		var theRoot   = MyTable;
		Drag.init(theHandle, theRoot);
	}
	//----------------
	this.windows=MyTable;

	if($opt['autoCloseTime']){
		this.timer='';
		//programmo già una chiusura se non vado sopra col mouse
		/*
		$opt['this'].onmouseout=function(){
				this.timer=setTimeout(function(){_self.hide();},$opt['autoCloseTime'])
		}
*/
		this.windows.onmouseout=function(){
			//var _self=this;
			//if(this.timer==''){
				//alert('test');
				this.timer=setTimeout(function(){_self.hide();},$opt['autoCloseTime'])
			//}
		}
		this.windows.onmouseover=function(){
		if(this.timer){
				clearTimeout(this.timer);
				this.timer='';
			}
		}
		return;
	};
	return this;
}
function MyDialog($opt){
	$opt['title']='Dialog';
	eval($opt['name']+"=new MyWindow($opt)");
}

function MyAlert($opt){
	$opt['title']='Alert';
	$opt['txt']='<table><tr><td valign="top"><img src="./../oLibs/MyWindow/img/alert.png" border="0px"></td><td valign="top" width="100%">'+$opt['txt'];
	$opt['txt']+='<br><br><br><center><a class="input" href="javascript:'+$opt['name']+'.chiudi()">Ok</a></center>';
	$opt['txt']+='</td></tr></table>';
	eval($opt['name']+"=new MyWindow($opt)");
}

function MyInfo($opt){
	$opt['title']='Info';
	$opt['txt']='<table ><tr><td valign="top"><img src="./../oLibs/MyWindow/img/info.png" border="0px"></td><td valign="top" width="100%">'+$opt['txt'];
	$opt['txt']+='<br><br><br><center><a class="input" href="javascript:'+$opt['name']+'.chiudi()">Ok</a></center>';
	$opt['txt']+='</td></tr></table>';
	eval($opt['name']+"=new MyWindow($opt)");
}

function MyConfirm($opt){
	$opt['txt']='<table><tr><td valign="top"><img src="./../oLibs/MyWindow/img/alert.png" border="0px"></td><td valign="top" width="100%">'+$opt['txt'];
	$opt['txt']+='<br><br><br><center><a class="input" href="javascript:'+$opt['name']+'.chiudi()">Cancel</a> <a class="input" href="javascript:'+$opt['name']+'.chiudi()">Ok</a></center>';
	$opt['txt']+='</td></tr></table>';
	eval($opt['name']+"=new MyWindow($opt)");
}
function MyTips($opt){
	$opt['position']='top-left';
	eval($opt['name']+"=new MyWindow($opt);");
	$opt['this'].setAttribute("onmouseout",$opt['name']+".hide()");
	$opt['this'].setAttribute("onmouseover",$opt['name']+".show()");
}
function MyButtonTips($opt){
	eval($opt['name']+"=new MyWindow($opt);");
	//$opt['this'].setAttribute("onmouseout",$opt['name']+".hide()");
	$opt['this'].setAttribute("onclick",$opt['name']+".show()");
}
/*----------------------------------------------------
Funzioni per il calcolo della dimensione dello schermo
------------------------------------------------------*/
function getLeft($this) {
	var oNode = $this;
	var iLeft = 0;
	while(oNode.tagName != "BODY") {
		iLeft += oNode.offsetLeft;
		oNode = oNode.offsetParent;
	}
	return iLeft; 
};

function getTop($this) {
	var oNode = $this;
	var iTop = 0;
	while(oNode.tagName != "BODY") {
		iTop += oNode.offsetTop;
		oNode = oNode.offsetParent;
	}
	return iTop;
};