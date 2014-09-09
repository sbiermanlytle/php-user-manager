<?php
$title = 'Basic User App';
include "../inc/origin.inc";

$app->authenticate();

//////////// HEAD ROUTER
include "../inc/head.inc";
$uri=substr($_SERVER['REQUEST_URI'],1);
if(strrpos($uri,'?')!==FALSE){
	$args=substr($uri,strrpos($uri,'?'));
	$uri=substr($uri,0,strrpos($uri,'?'));
}
$app->log('_______');
$app->log('ROUTING');
$app->log('uri: '.$uri);
$app->log('args: '.$args);
?>
</head>
<body>

<?php
$normal_functions = array('login','edit','activate','forgot-password','register');
//////////// BODY ROUTER
foreach($normal_functions as $fn)
	if( $uri==$fn ){
		include '../ctr/'.$fn.'.ctr';
		$fn_found = TRUE;
		break;
	}
if(!$fn_found){
	if($uri=='') include "../inc/homepage.inc";
	else if($uri=="logout") $app->logout();
	else echo "404";
}

//show debug log
if($debug) include "../inc/debug.inc"; 
?>
</body>
</html>