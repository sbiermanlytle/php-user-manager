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

			if ( substr($uri,0,5) == 'login' ) :
				$uri = substr($uri,6);
				ctr_get_user_data( $mysqli, explode("/", $uri) );

			elseif ( substr($uri,0,8) == 'register' ) :
				$uri = substr($uri,9);
				$user_data = explode("/", $uri);
				for($i=0;$i<count($user_data);$i++)
					$user_data[$i] = rawurldecode($user_data[$i]);
				$results = user_register( $mysqli,
					$user_data[1],$user_data[0], $user_data[2],
					$user_data[3],$user_data[3] );

				if(empty($results)) :
					echo 'OK';
				else :
					$result = "";
					foreach( $results as $r )
						$result.='/'.$r;
					echo $result;
				endif;

			elseif ( substr($uri,0,2) == 'fp' ) :
				$uri = substr($uri,3);
				$user_data = explode("/", $uri);
				for($i=0;$i<count($user_data);$i++)
					$user_data[$i] = rawurldecode($user_data[$i]);
				if(send_user_request_new_password_email($user_data[0],$user_data[1])) :
					echo "OK";
				else : echo 'The email submission'.$GLOBALS['FAILURE'];
				endif;

			elseif ( substr($uri,0,4) == 'edit' ) :
				$uri = substr($uri,5);
				$user_data = explode("/", $uri);
				for($i=0;$i<count($user_data);$i++)
					$user_data[$i] = rawurldecode($user_data[$i]);

				$results = array();

				if($user_data[3] != "") :
					$results = user_change_password_via_profile(
						$mysqli, $_SESSION['id'], $user_data[3],
						$user_data[4], $user_data[5]);
					if( $results[0] != "password changed" ) :
						$result = "";
						foreach( $results as $r )
							$result.='/'.$r;
						echo $result;
						$pass_change_fail = true;
					endif;
				endif;

				$results = array_merge($results,user_update($mysqli,$_SESSION['id'],
					$user_data[0],$user_data[1],$user_data[2]));
				if($results[0]=='profile udpated'||$results[1]=='profile udpated') :
					$result = "";
					foreach( $results as $r )
						$result.='/'.$r;
					echo $result;
				else :
					if(empty($pass_change_fail)) :
						echo 'OK';
					endif;
				endif;

			else : echo '404';
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

			else : $uri_seg = decode_uri($uri);
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