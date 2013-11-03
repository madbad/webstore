<!doctype html>
<html>
<head>
	
 <script src="./polymer/polymer.min.js" log=""></script>
 <script src="./jquery.min.js"></script>
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

	<script>
		window.addEventListener('WebComponentsReady', function() {
			document.body.style.opacity = 1; // show body now that registration is done.
			document.body.innerHTML = '<x-ddt></x-ddt>'
		});
	</script>
	
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

</body>
</html>
