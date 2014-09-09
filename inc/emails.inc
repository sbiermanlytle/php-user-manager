<?php

	//CREATE AND SEND EMAIL TO USER
	function emailUser($email,$subject,$message){
		$headers = "From: User App\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		$msg = '<html><body>';
		$msg .= $message;
		$msg .= '</body></html>';
		return mail($email,$subject,$message,$headers);
	}

	//REGISTRATION EMAIL CONTENT
	function email_registration($hostname,$email,$name,$username,$code){
		return emailUser($email,"New Account",
			'<p>Hello '.$name.',</p>'
		.'<p>You have created a new account at <a href="'.$hostname.'">'.$hostname.'</a></p><br/>'
		.'<p>Your username is: <span style="font-weight:bold">'.$username.'</span><br/><br/>'
		.'<a href="'.$hostname.'/activate?email='.$email.'&code='.$code.'">Click this link to activate your account</a>');
	}

	//PASSWORD RESET EMAIL CONTENT
	function email_passwordReset($email,$password){
		return emailUser($email,"Password Reset",
			'<p>Your password has been reset to: <span style="font-weight:bold">'.$password.'</span></p>');
	}
	
?>