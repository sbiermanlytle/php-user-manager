<?php
	include "../app.php";
	include "../functions.php";
	include "../controller.php";
	include "../lib/password.php";

	$client_ip = $_SERVER['REMOTE_ADDR'];

	// ROUTER
	//////////////////////////////////////////////////
	//seperate uri from args
	$uri=substr($_SERVER['REQUEST_URI'],1);
	if(strrpos($uri,'?')!==FALSE){
		$args=substr($uri,strrpos($uri,'?'));
		$uri=substr($uri,0,strrpos($uri,'?'));
	}
	// NO DATABASE CONNECTION PATHS
	//////////////////////////////////////////////////
	if ( $uri == 'masterlog' ) : include '../view/masterlog.inc';
	else : 
	// DATABASE CONNECTION PATHS
	/////////////////////////////////////////////////
		if ( !($mysqli = db_connect( $dbhost, $dbuser, $dbpass, $dbname )) ) :
			echo 'failed to connect to database'; die(); endif;
		session_start();

		// NO HEAD OR FOOT
		/////////////////////////////////////////////
		if ( $uri == 'init' && db_init($mysqli,$database) ) : ;
		elseif( $uri == 'logout' ) : user_logout();

		elseif( $uri == 'test' ) :
			echo '404';

		// REMOTE APIs
		/////////////////////////////////////////////
		elseif ( substr($uri,0,6) == 'remote' ) :
			header('Content-Type: text/plain');
			$uri = substr($uri,7);
			$del = strpos($uri,"/");
			$query = substr($uri,0,$del);
			$data = decode_uri(substr($uri,$del+1));
			if( $query != 'login' && empty($_SESSION['id']) ) :
				echo 'You must login';
			else : 
				if ( $query == 'login' ) :
					ctr_get_user_data( $mysqli, $data );

				elseif ( $query == 'register' ) : 
					ctr_remote_register( $mysqli, $data);

				elseif ( $query == 'fp' ) : 
					ctr_remote_forgot_password( $data );

				elseif ( $query == 'pc' ) : 
					ctr_remote_password_change( $mysqli, $data );

				elseif ( $query == 'edit' ) :
					ctr_remote_edit( $mysqli, $data );

				else : echo '404';
				endif;
			endif;
		else :
		// HEAD AND FOOT
		/////////////////////////////////////////////
			include "../view/head.inc";

			    if ($uri == 'profile') 			: ctr_profile($mysqli);
			elseif ($uri == 'register') 		: ctr_register($mysqli);
			elseif ($uri == 'login') 			: ctr_login($mysqli);
			elseif ($uri == 'forgot-password') 	: ctr_forgot_password();
			elseif ($uri == 'change-password') 	: ctr_change_password($mysqli, $uri, $uri_seg);

			else : $uri_seg = decrypt_uri($uri);
				// ENCODED PATHS
				/////////////////////////////////////
				    if( $uri_seg[0] == $ACTIVATE_USER ) 
				    	: ctr_activate_user( $mysqli, $uri_seg );

				elseif($uri_seg[0] == $CHANGE_PASSWORD) 
						: ctr_change_password_uri($uri);

				elseif( $uri == '' ) : ctr_homepage();
					
				else : echo '404';
				endif;
			endif;
			include "../view/foot.inc";
		endif;
		$mysqli->close();
	endif;
?>