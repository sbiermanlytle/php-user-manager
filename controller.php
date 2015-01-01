<?php
/* WEB INTERFACE
*/////////////////////////////////////////////////////
function ctr_homepage(){
	if( !empty($_SESSION['username']) ) :
		echo 'logged in as '.$_SESSION['username'].'<br/>';
		echo '<a href="profile">profile</a>';
	else : echo '<a href="login">login</a>';
	endif;
}
function ctr_profile( $mysqli ){

	$results=array();

	if( $_POST['email']&&$_POST['email']!=$_SESSION['email']
	 || $_POST['username']&&$_POST['username']!=$_SESSION['username']
	 || $_POST['name']&&$_POST['name']!=$_SESSION['name'] )
		$results = user_update( $mysqli,
			$_SESSION['id'], 
			$_POST['email'],
			$_POST['username'],
			$_POST['name']);

	if($_POST['old']
	 ||$_POST['new']
	 ||$_POST['re-type'])
		$results = user_change_password_via_profile( $mysqli, 
			$_SESSION['id'],
			$_POST['old'],
			$_POST['new'],
			$_POST['re-type']);

	include "../view/profile.inc";
}
function ctr_register( $mysqli ){

	if($_POST['name']
	 ||$_POST['email']
	 ||$_POST['username']
	 ||$_POST['password']
	 ||$_POST['re-type'])
		$results = user_register( $mysqli, 
			$_POST['name'],
			$_POST['email'],
			$_POST['username'],
			$_POST['password'],
			$_POST['re-type']);
	else $results=array("");

	if(empty($results)) : echo 'das good';
	else : include "../view/register.inc";
	endif;
}
function ctr_login( $mysqli ){

	if($_POST['email']
	 ||$_POST['password']) :
		if( user_login( $mysqli, 
			$_POST['email'],
			$_POST['password']) ) :
			echo 'welcome '.$_SESSION['username'];
			return;
		else : $result = 'invalid credentials';
		endif;
	endif;
	include "../view/login.inc";
}
function ctr_forgot_password(){

	if($_POST['email']) :
		send_user_request_new_password_email( $_POST['email'], $_POST['k']);
		echo 'an email has been sent with instructions on how to change your password';
	else : include "../view/forgot-password.inc";
	endif;
}
function ctr_change_password( $mysqli, $uri, $uri_seg ){

	if($_POST['key']
	 ||$_POST['username']
	 ||$_POST['password']
	 ||$_POST['re-type']) : 
		$uri_seg = decode_uri($_POST['uri']);
		$results = user_change_password_via_email( $mysqli, 
			$uri_seg[1],
			$uri_seg[2],
			$uri_seg[3],
			$_POST['key'],
			$_POST['username'],
			$_POST['password'],
			$_POST['re-type']);
	else : $results=array("");
	endif;

	if($results[0]=='password changed') : 
		echo 'password changed successfully';
		$_SESSION['pass_change_attempts'] = 0;
	else : 
		if(empty($_SESSION['pass_change_attempts'])) 
			$_SESSION['pass_change_attempts'] = 1;
		else $_SESSION['pass_change_attempts']++;
		if( $_SESSION['pass_change_attempts'] > 3)
			sleep($_SESSION['pass_change_attempts']-2);

		include "../view/change-password.inc";
	endif;
}
function ctr_activate_user( $mysqli, $uri_seg ){
	if( user_activate($mysqli, 0, $uri_seg[1], 
		$uri_seg[2], $uri_seg[3], 
		substr($uri_seg[4],0,$uri_seg[5])) )
		echo 'activation successful, welcome '.$uri_seg[1];
	else echo 'account already active';
}
function ctr_change_password_uri($uri){
	$results=array("");
	include "../view/change-password.inc";
}
/* MOBILE INTERFACE
*/////////////////////////////////////////////////////
function ctr_get_user_data( $mysqli, $credentials ){
	if( user_login($mysqli, $credentials[0], $credentials[1]) == "OK" )
		echo $_SESSION['role']."/".$_SESSION['banned']."/".$_SESSION['username']."/"
			.$_SESSION['name']."/".$_SESSION['created'];
	else echo 'NO';
}
function ctr_remote_register( $mysqli, $data ){
	$results = user_register( $mysqli,
		$data[1],$data[0], $data[2],
		$data[3],$data[3] );
	if(empty($results)) echo 'OK';
	else echo encode_remote_response($results);
}
function ctr_remote_forgot_password( $data ){
	if(send_user_request_new_password_email($data[0],$data[1]))
		echo "OK";
	else echo 'The email submission'.$GLOBALS['FAILURE'];
}
function ctr_remote_password_change( $mysqli, $data ){
	$results = user_change_password_via_profile(
		$mysqli, $_SESSION['id'], $data[0],
		$data[1], $data[2]);
	if( $results[0] == "password changed" ) 
		echo 'OK';
	else echo encode_remote_response($results);
	
}
function ctr_remote_edit( $mysqli, $data ){
	$results = user_update($mysqli,$_SESSION['id'],
		$data[0],$data[1],$data[2]);
	if( $results[0] == 'profile updated' ) 
		echo 'OK';
	else echo encode_remote_response($results);
}
?>