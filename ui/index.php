<!doctype html>
<html>
<head>
	<!--<script src="./polymer/polymer.min.js" log=""></script>-->
	<script src="http://cdnjs.cloudflare.com/ajax/libs/polymer/0.1.1/platform.js"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/polymer/0.1.1/polymer.js"></script>
	<script src="./jquery.min.js"></script>
<!--

	<script src="./autoNumeric.js"></script>
	<script type="text/javascript" src="./masked_input_1.3.js"></script>
	<script>
		
		// Recusively up-traverse the "dom" (and the "shadowdom") to get the relative position of an element
		// from the document body.
		
		var getOffset = function (el, callback, offset){
			if(offset === undefined){
				offset = {top:0,left:0}
			}
		
			offset.top += el.offsetTop;
			offset.left += el.offsetLeft;
			
			if (el.offsetParent!=null){
				getOffset(el.offsetParent, callback, offset);
			}else if(el.host!=null){
				getOffset(el.host, callback, offset);
			}else{
				callback(offset);
				return;
			}
		}

	</script>
-->
<!--
	<script>
		window.addEventListener('WebComponentsReady', function() {
			document.body.style.opacity = 1; // show body now that registration is done.
			document.body.innerHTML = '<x-ddt></x-ddt>'
		});
	</script>
-->
<style>
input {
	color:black;
	border: 1px solid #a1a1a1;
	background-color: white;
	font-size:1em;
	font-family: 'Inconsolata', sans-serif;
}

body{
	font-size:1em;
	line-height: 2;
}


label {
	color:black;
	font-size:1em;
	background-color: #F2F2F2;
	PADDING:0.5em;
	width:0px;
	/*visibility:hidden;*/
}
input {
color:blue;
}

</style>
</head>
<body>
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
					$components.= "\n<br><br><br><br><br><hr>".$file."<br> <".str_replace('.html','',$file).'></'.str_replace('.html','',$file).'>';
				}
			}
		//echo $components;
		};
		

		loadComponentsFromDir('components');
	?>


<!--
<x-menu>
	<x-menuitem>test</x-menuitem>
	<x-menuitem>test 1</x-menuitem>
	<x-menuitem>test 2</x-menuitem>
	<x-menuitem>test 3</x-menuitem>
	<x-menuitem>test 4</x-menuitem>
	<x-menuitem>test 5</x-menuitem>
	<x-menuitem>test 6</x-menuitem>
	<x-menuitem>test 7</x-menuitem>
	<x-menuitem>test 8</x-menuitem>
	<x-menuitem>test 9</x-menuitem>
	<x-menuitem>test 10</x-menuitem>
	<x-menuitem>test 11</x-menuitem>
	<x-menuitem>test 12</x-menuitem>
	<x-menuitem>test 13</x-menuitem>
	<x-menuitem>test 14</x-menuitem>
	<x-menuitem>test 15</x-menuitem>
	<x-menuitem>test 16</x-menuitem>
	<x-menuitem>test 17</x-menuitem>
	<x-menuitem>test 18</x-menuitem>
	<x-menuitem>test 19</x-menuitem>
	<x-menuitem>test 20</x-menuitem>
	<x-menuitem>test 21</x-menuitem>
	<x-menuitem>test 22</x-menuitem>
</x-menu>
-->
<x-window title="Gestione DDT">
	<x-ddt></x-ddt>
</x-window>

<!--
<x-query-menu></x-query-menu>
-->

</body>
</html>
