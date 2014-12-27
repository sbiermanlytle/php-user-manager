<?php
//////////// HEAD ROUTER
$uri=substr($_SERVER['REQUEST_URI'],1);
if(strrpos($uri,'?')!==FALSE){
	$args=substr($uri,strrpos($uri,'?'));
	$uri=substr($uri,0,strrpos($uri,'?'));
}

//////////// BODY ROUTER
if($uri=="send_sse"){
	header('Content-Type: text/event-stream');
	header('Cache-Control: no-cache');
	$new_data = rand(0, 1000);
	echo "data: New random number: $new_data\n\n";
	ob_flush();
	/*$handle = @fopen("../log/master.txt", "r");
	if ($handle) {
	    while (($buffer = fgets($handle, 4096)) !== false) {
	        echo '<p>'.$buffer.'</p>';
	    }
	    if (!feof($handle)) {
	        echo "Error: unexpected fgets() fail\n";
	    }
	    fclose($handle);
	}*/ 
} else if($uri=="masterlog"){
	include "../inc/head.inc";
	$handle = @fopen("../log/master.txt", "r");
	if ($handle) {
	    while (($buffer = fgets($handle, 4096)) !== false) {
	        echo '<p>'.$buffer.'</p>';
	    }
	    if (!feof($handle)) {
	        echo "Error: unexpected fgets() fail\n";
	    }
	    fclose($handle);
	} ?>
	<script type="text/javascript">
		update = function(){
			setTimeout(function(){
				if(document.hasFocus())
					location.reload();
				update();
			},2000);
		}; update();
	</script>
	<?php include "../inc/foot.inc";
} else if($uri=="updates") { ?> 
	<?php include "../inc/head.inc"; ?>
		<ul id='el'></ul>
		<script type="text/javascript">
			var eSource = new EventSource("send_sse");
			//detect message receipt
			eSource.onmessage = function(event) {
				//write the received data to the page
			  	var e = document.createElement("li");
			  	e.innerHTML = event.data;
			  	document.getElementById('el').appendChild(e);
			};
		</script>
	<?php include "../inc/foot.inc";
} else {
	$client_ip = $_SERVER['REMOTE_ADDR'];
	date_default_timezone_set('America/New_York');
	$file = fopen("../log/master.txt","a");
	fwrite($file,"ip: ".$client_ip." | access: ".date('m/d/y H:i:s')."\n");
	fclose($file);
	echo '404';
}
?>