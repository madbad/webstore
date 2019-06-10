<!doctype html>
<html>
<head>
	<script src="./libs/keycodes.js"></script>
	<script src="https://polygit.org/webcomponentsjs+1.0.0-rc.5/components/webcomponentsjs/webcomponents-loader.js"></script>
	<?php
	$components='';
		function loadComponentsFromDir ($dir){
		global $components;
			// create a handler for the directory
			$handler = opendir($dir);

			// open directory and walk through the filenames
			while ($file = readdir($handler)) {
				if (strpos($file, '.html')) {
					echo "\n".'<link rel="import" href="./'.$dir.'/'.$file.'">';
					//$components.= "\n<br>".$file."<br> <".str_replace('.html','',$file).'></'.str_replace('.html','',$file).'>';
				}
			}
		echo $components;
		};
		loadComponentsFromDir('components');
	?>

<style>
input {
	color:black;
	border: 1px solid #a1a1a1;
	background-color: white;
	font-size:1em;
	font-family: 'Inconsolata', sans-serif;
}
html, body{
	height: 100%;
	margin:0px;
	padding:0px;
}
body{
	font-size:1em;
	line-height: 2;
	width:100%;
}


label {
	color:black;
	font-size:1em;
	background-color: #F2F2F2;/*if I uncomment this all the label get this color...even if I style them properly*/
	padding:0.5em;
	width:0px;
}
input {
	color:blue;
}
input:focus {
	border-color: orange;
	background-color:#ffe28e;
}
input:disabled {
	color: #848484;
	background-color: #D6D6D6;
}
.modalBackgroundShadow {
	top:0px;
	left:0px;
	position:absolute;
	width: 100%;
	height: 100%;
	display: block;
	position:absolute;
	background-color:black;
	z-index: 100;
	opacity:0.5;
}
#errorDisplay{
	width: 100%;
	height: 1.5em;
	background-color:#ad0f0f;
	color:white;
	text-align: center;
	visibility: hidden;
	font-size:1.5em;
	font-family: 'Inconsolata', sans-serif;
	z-index: 10000;
	position:absolute;
	top: 0px;
	let: 0px;
}

</style>
</head>
<body>
<div id="errorDisplay">Here we will put some error</div>


<script>
function displayError(errorMsg, params){
	params={};
	params.time = 2;

	var div = document.getElementById("errorDisplay");
	div.innerHTML = '<span style="font-weight:bold; color: #ff5926;">Errore: </span>'+errorMsg;
	div.style.visibility = 'visible';
	console.log('errordiv:',div);
	//remove it after the time has passed
	
	setTimeout(function (){
		this.style.visibility = 'hidden';
	}.bind(div), params.time*1000);
}

window.addEventListener('WebComponentsReady', function() {
	console.log("MAIN DOCUMENT: WebComponents are ready to be used!");
/*================================
  CREATE THE MAIN MENU
=================================*/
	//create the main app menu
	console.log("MAIN DOCUMENT: create an x-menu");
	var menu = document.createElement('x-menu');
	menu.title = "Menu principale"
	var list= [
		{label:'Elenca DDT',_action:function (){
								console.log('test');
								//elencaDdt().bind(this);
								elencaDdt();
							}.bind(this)},
		{label:'Inserisci DDT di vendita',_action:function (){
								var ddtApp = document.createElement('x-ddt');
								document.body.appendChild(ddtApp);
								//inizializzo un nuovo ddt
								ddtApp.inizializzaNuovoDdt();
								//imposto la causale vendita
								ddtApp.set('ddt.causale','02');
								ddtApp.shadowRoot.querySelector('x-codicecausale').disabled = true;
								//ddtApp.set('ddt.numero','99');
								ddtApp.shadowRoot.querySelector('x-numerodoc').disabled = true;
								//select the second input
								//ddtApp.getFocusableElements()[1].shadowRoot.querySelector('input').focus();
								ddtApp.onQuit = function(){
									console.log('quitting...');
									menu.$.searchfield.focus();
								}.bind(menu)
							}.bind(this)},
		{label:'Inserisci DDT di acquisto',_action:function (){
								var ddtApp = document.createElement('x-ddt');
								document.body.appendChild(ddtApp);
								//inizializzo un nuovo ddt
								ddtApp.inizializzaNuovoDdt();
								ddtApp.onQuit = function(){
									console.log('quitting...');
									menu.$.searchfield.focus();
								}.bind(menu)
							}.bind(this)},
		{label:'Elenca Fatture',_action:function (){
								console.log('test');
								//elencaDdt().bind(this);
								elencaFt();
							}.bind(this)},
		{label:'Emetti Fattura',_action:function (){
								var ftApp = document.createElement('x-ft');
								document.body.appendChild(ftApp);
								//inizializzo un nuovo ddt
								ftApp.inizializzaNuovoDdt();
								ftApp.onQuit = function(){
									console.log('quitting...');
									menu.$.searchfield.focus();
								}.bind(menu)
							}.bind(this)},
	];

	menu.keepAliveOnConfirm = true;

	console.log("MAIN DOCUMENT: appending x-menu to the page body");
	document.body.appendChild(menu);

	menu.set('items', list);
	
	menu.oncancel = function (){
		//this.$.numeroriga.$.field.focus();
	}.bind(this);
	
	//sovrascrivo la funzione di uscita in modo che non sia possibile uscire
	menu.cancel = function(){
		console.log("Mi spiace non si esce da qui! Questo e' il main menu")
	};

	//wait a little and then position the help window in the middle of the body
	//and remember his width so that it does not change during future modifcation of contents
	//and show us
	setTimeout(function (){
		var modifier = {
			top: 50,
			left: 50,
			}
		this.setPosition('tl','tl',document.body, modifier);
		this.show();
		this.focus();
	}.bind(menu), 150);

	/*================================
	  LIST DDT
	=================================*/
	function elencaDdt(params){
		console.log('create the menu');
		var ddtList = document.createElement('x-menu');
		ddtList.title="Seleziona DDT";
		/*
		ddtList.params = {
			_type: 'Ddt',
			data: ['!=',''],
			cliente_codice: ['!=','']
		}
		*/
		var params =  {
			_type: 'Ddt',
			_select: 'numero,data,clientefornitore_codice',	
			data: ['!=',''],
			clientefornitore_codice: ['!=','']
		};
		
		ddtList.params = JSON.stringify(params);
		console.log(ddtList.params);
		console.log(ddtList.params);
		ddtList.onconfirm = function (selection){
			//when the selection is confirmed start editing the ddt
			//console.log("selezionato il ddt", selection);
			//console.log("Procedo alla modifica...");
			modificaDdt(selection);
		}.bind(this);
		ddtList.oncancel = function (selection){
			console.log('XINPUT: HELP CANCELLED, claim focus back from the help menu');
			//get the focus back
			menu.$.searchfield.focus();
		}.bind(this);
		document.body.appendChild(ddtList);

		//wait a little and then position the help window in the middle of the body
		//and remember his width so that it does not change during future modifcation of contents
		//and show us
		setTimeout(function (){
			this.addModalBackground();
			var modifier = {
				top: 50,
				left: 0,
				}
			this.setPosition('tc','tc',document.body, modifier);
			console.log(this.offsetWidth);
			this.style.width = this.offsetWidth+'px';
			this.show();
			this.focus();
		}.bind(ddtList), 150);
	}
	/*================================
	  EDIT DDT
	=================================*/
	function modificaDdt(ddt){
	var ddtApp = document.createElement('x-ddt');
		ddtApp.set('ddt',ddt);
		ddtApp.getDdtFromServer();
		ddtApp.onQuit = function (){
			menu.focus();
		}
		document.body.appendChild(ddtApp);
	}
	
	/*================================
	  LIST FATTURE
	=================================*/
	function elencaFt(params){
		console.log('create the menu');
		var ftList = document.createElement('x-menu');
		ftList.title="Seleziona FATTURA";
		/*
		ftList.params = {
			_type: 'Ddt',
			data: ['!=',''],
			cliente_codice: ['!=','']
		}
		*/
		var params =  {
			_type: 'Ft',
			_select: 'numero,data,clientefornitore_codice',	
			data: ['!=',''],
			clientefornitore_codice: ['!=','']
		};
		
		ftList.params = JSON.stringify(params);
		ftList.onconfirm = function (selection){
			//when the selection is confirmed start editing the ft
			//console.log("selezionata la ft", selection);
			//console.log("Procedo alla modifica...");
			modificaFt(selection);
		}.bind(this);
		ftList.oncancel = function (selection){
			console.log('XINPUT: HELP CANCELLED, claim focus back from the help menu');
			//get the focus back
			menu.$.searchfield.focus();
		}.bind(this);
		document.body.appendChild(ftList);

		//wait a little and then position the help window in the middle of the body
		//and remember his width so that it does not change during future modifcation of contents
		//and show us
		setTimeout(function (){
			this.addModalBackground();
			var modifier = {
				top: 50,
				left: 0,
				}
			this.setPosition('tc','tc',document.body, modifier);
			console.log(this.offsetWidth);
			this.style.width = this.offsetWidth+'px';
			this.show();
			this.focus();
		}.bind(ftList), 150);
	}
},false);


</script>

<!--
<x-window title="Gestione DDT">
	<x-ddt></x-ddt>
</x-window>

-->
</body>
</html>
