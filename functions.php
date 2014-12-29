<?php
/* ENCRYPTION
*////////////////////////////////////////////////////////////
	function encrypt($p,$k){
    	$iv = create_iv();
    	$cipher = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, pack('H*', $k),
                                 $p, MCRYPT_MODE_CBC, $iv);
		$cipher = $iv.$cipher;
		return base64_encode($cipher);
	}
	function decrypt($c,$k){
		$cipher = base64_decode($c);
		$iv_size = get_iv_size();
		$iv_dec = substr($cipher, 0, $iv_size);
		$cipher = substr($cipher, $iv_size);
		if( strlen($iv_dec) != mcrypt_get_block_size("rijndael-256","cbc"))
			return false;
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, pack('H*', $k),
                                $cipher, MCRYPT_MODE_CBC, $iv_dec);
	}
	function decode_uri($l){
		$decoded = explode("/",
			decrypt($l,$GLOBALS['MK']));
		for($i=0;$i<count($decoded);$i++)
			$decoded[$i] = rawurldecode($decoded[$i]);
		return $decoded;
	}
	function create_salt(){
	    return substr(base64_encode(create_iv()), 0, 43);
	}
	function create_iv(){
		return mcrypt_create_iv(get_iv_size(), MCRYPT_RAND);
	}
	function get_iv_size(){ 
		return mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC); 
	}

/* DATABASE
*///////////////////////////////////////////////////////////
	function db_connect( $dbhost, $dbuser, $dbpass, $dbname ){
		$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		if ($mysqli->connect_errno) :
		    log_sql_error( array($mysqli,"db connect",'connect_errno: '.$mysqli->connect_errno) );
			return false;
		endif;
		return $mysqli;
	}
	function db_init( $mysqli, $config ){
		$did_initialize = false;
		foreach ($config as $table => $fields) :
			if($result = $mysqli->query("SHOW TABLES LIKE '".$table."'")) :
				$err_args = array($mysqli,'db init');
				if($result->num_rows <= 0) :
					$query = "CREATE TABLE `".$table."` (";
					foreach ($fields as $field => $type) :
						$query.="`".$field."` ".$type.",";
					endforeach;
					$query = rtrim($query, ",");
					$query.=");";
					echo '<p>'.$query.'</p>';
					if($mysqli->query($query)) :
						log_sql_new("`".$table."` table created");
					else : 
						log_sql_error( $err_args );
						echo "<p>error creating '".$table."' table: ".$mysqli->error."</p>";
					endif;
					$did_initialize = true;
				endif;
				$result->close();
			else : log_sql_error( $err_args );
			endif;
		endforeach;
		return $did_initialize;
	}
	function db_select( $mysqli, $table, $field, $search_field, $search_value, $err_title ){

		$err_args = array( $mysqli, $err_title, $search_value );

		if( $stmt = $mysqli->prepare('SELECT '.$field.' FROM '.$table.' WHERE '.$search_field.'=?') ) :

			if( !$stmt->bind_param('s', $search_value) ) :
				log_sql_error( $err_args );
			else : return db_complete_select( $stmt, $err_args );
			endif;

		else : log_sql_error( $err_args );
		endif;
		return FALSE;
	}
	function db_complete_select( $stmt, $err_args){

		if( !$stmt->execute() ) : 
			log_sql_error( $err_args );
		else : 
			if( !$stmt->bind_result( $result ) ) :
				log_sql_error( $err_args );
			else :
				if( $stmt->fetch() === FALSE) :
					log_sql_error( $err_args );
				endif;
			endif;
		endif;

		if( !$stmt->close() ) :
			log_sql_error( $err_args );
		endif;

		return $result;
	}

/* VALIDATION
*///////////////////////////////////////////////////////////
	function validate_new_password( $results, $pass, $pass_retype ){
		if(empty($pass)) : array_push($results,'provide a new password');
		else : 
			if(empty($pass_retype)) array_push($results,'re-type your new password');
			elseif($pass!=$pass_retype) array_push($results,'new passwords do not match');
		endif;
		return $results;
	}
	function validate_email( $results, $mysqli, $email ){
		if(empty($email)) array_push($results,'provide an email');
		elseif(!preg_match($GLOBALS['VALID_EMAIL'],$email))
			array_push($results,'invalid email address');
		elseif(user_exists( $mysqli, 'email', $email ))
			array_push($results,'email address already registered');
		return $results;
	}
	function validate_username( $results, $mysqli, $username  ){
		if(empty($username)) array_push($results,'provide a username');
		elseif(user_exists( $mysqli, 'username', $username ))
			array_push($results,'username already in use');
		return $results;
	}

/* EMAIL
*////////////////////////////////////////////////////////////
	function email_head( $subject ){
		return '<html>
		<head>
		  <title>'.$subject.'</title>
		</head>';
	}
	function uri_prepare( $params ){
		$uri = $params[0];
		for( $i=1; $i<count($params); $i++ )
			$uri .= "/".rawurlencode($params[$i]);
		return $GLOBALS['DOMAIN_URL'].encrypt($uri,$GLOBALS['MK']);
	}
	function uri_ban_ip(){ 
		//NOT CURRENTLY FUNCTIONAL *12/24/14
		return encrypt($GLOBALS['BAN_IP']."/".$GLOBALS['client_ip'],$GLOBALS['MK']);
	}
	function send_email( $to, $subject, $msg ){

		$headers   = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/html; charset=iso-8859-1";
		$headers[] = "From: iio interactive <mailer@".$GLOBALS['DOMAIN'].">";
		$headers[] = "Subject: {$subject}";
		$headers[] = "X-Mailer: PHP/".phpversion();

		return mail($to, $subject, $msg, implode("\r\n", $headers));
	}
	function send_email_default_format( $email, $subject, $html ){
		$msg = email_head( $subject ).$GLOBALS['EMAIL_HEADER'].$html.$GLOBALS['EMAIL_FOOT'];
		return send_email( $email, $subject, $msg );
	}
	function send_user_registration_email( $email, $name, $username, $hash ){

		$uri_activation = uri_prepare( array( 
			$GLOBALS['ACTIVATE_USER'],
			$name,
			$email,
			$username,
			$hash,
			strlen($hash)
		));

		$subject = "New Account at ".$GLOBALS['DOMAIN'];
		$html = '<p>Hello '.$name.',</p>
	  		<p>Welcome to '.$GLOBALS['SITE_NAME'].'.</p>
	  		<p>To activate your new account and gain access to all of our website features, click the following link: <a href="'.$uri_activation.'">activate account</a></p>
	  		<p>Best Regards,</p>
	  		<p>The '.$GLOBALS['SITE_NAME'].' Team</p>
	  		</br>
	  		<p style="font-size:.8em">If you did not register for an account, click this link to help ensure that your email  does not get used illicitly again: <a href="'.uri_ban_ip().'">did not register</a></p>';

		if(send_email_default_format( $email, $subject, $html ))
			log_sql_new( "user registration", $email.' | '.$username.' | '.$name );
		else log_error( "user registration", $email.' | '.$username.' | '.$name.' | '.$hash );
	}
	function send_user_request_new_password_email( $email, $key ){

		$uri_change_password = uri_prepare( array( 
			$GLOBALS['CHANGE_PASSWORD'],
			time()+(60*$GLOBALS['PASSWORD_KEY_LIFETIME']),
			$email,
			$key
		));

		$subject = "Password Change at ".$GLOBALS['DOMAIN'];
		$html = '<p>Hello,</p>
	  		<p>You have requested to change your password.</p>
	  		<p>To continue the password change process, click the following link: <a href="'.$uri_change_password.'">change password</a></p>
	  		<p>Best Regards,</p>
	  		<p>The '.$GLOBALS['SITE_NAME'].' Team</p>
	  		</br>
	  		<p style="font-size:.8em">If you did not request to change your password, click this link to help ensure that your email does not get used illicitly again: <a href="'.uri_ban_ip().'">did not request to change my password</a></p>';

		if(send_email_default_format( $email, $subject, $html ))
			log_sql_request( "password change", $email );
		else log_sql_error( array($mysqli, "password change", $email.' | '.$key) );
	}

/* USER 
*///////////////////////////////////////////////////////////
	function user_exists( $mysqli, $field, $value ){
		$id = db_select( $mysqli, 'users', 'id', $field, $value, 'verify unique user' );
		if( empty($id) ) return false;
		return true;
	}
	function user_register( $mysqli, $name, $email, $username, $pass, $pass2 ){
		
		$results = array();

		//validate inputs
		if(empty($name)) array_push($results,'provide a name');
		$results = validate_email( $results, $mysqli, $email);
		$results = validate_username( $results, $mysqli, $username);
		$results = validate_new_password( $results, $pass, $pass2 );

		if( empty($results) ) :
			$hash = password_hash($pass, PASSWORD_DEFAULT);
			send_user_registration_email( $email, $name, $username, $hash );
		endif;

		return $results;
	}
	function user_activate( $mysqli, $role, $name, $email, $username, $hash ){

		$err_args = array( $mysqli, 'user activate', $role.' | '.$email.' | '.$username.' | '.$name.' | '.$hash );

		if( $stmt = $mysqli->prepare('SELECT id FROM users WHERE email=? OR username=?') ) :

			if( !$stmt->bind_param('ss', $email, $username ) ) :
				log_sql_error( $err_args );
			else : 
				$result = db_complete_select( $stmt, $err_args );
				if( !empty($result) ) :
					return FALSE;
				endif;
			endif;

		else : log_sql_error( $err_args );
		endif;

		if( $stmt = $mysqli->prepare('INSERT INTO users ('
			.'role, email, username, name, created, updated, password)'
			.' VALUES(?,?,?,?,?,?,?)') ) :

			$now = date("Y-m-d H:i:s");

			if( !$stmt->bind_param('issssss', $role, $email, $username, $name, $now, $now, $hash ) )
				log_sql_error( $err_args );

			if( !$stmt->execute() ) : log_sql_error( $err_args );
			else : log_sql_new( $err_title, $err_data );
				return true;
			endif;

			if( !$stmt->close() ) : log_sql_error( $err_args );
			endif;

		else : log_sql_error( $err_args );
		endif;
	}
	function user_login( $mysqli, $email, $pass ){

		if( empty($email) || empty($pass) ) : return false;
		else :

			$err_args = array( $mysqli, 'login', $email );

			if( $stmt = $mysqli->prepare('SELECT id,role,banned,username,name,created,updated,password FROM users WHERE email=?') ) :

				if( !$stmt->bind_param('s', $email) ) log_sql_error( $err_args );
				if( !$stmt->execute() ) log_sql_error( $err_args );
				if( !$stmt->bind_result(  $id, $role, $banned, $username, $name, $created, $updated, $hash ) ) log_sql_error( $err_args );
				if( $stmt->fetch() === FALSE ) log_sql_error( $err_args );
				if( !$stmt->close() ) log_sql_error( $err_args );

				if(password_verify($pass,$hash)) :
					$_SESSION['form_attempts'] = 0;
					$_SESSION['id'] = $id;
					$_SESSION['role'] = $role;
					$_SESSION['banned'] = $banned;
					$_SESSION['email'] = $email;
					$_SESSION['username'] = $username;
					$_SESSION['name'] = $name;
					$_SESSION['created'] = $created;
					$_SESSION['updated'] = $updated;
					return true;
				else : 
					if(empty($_SESSION['form_attempts'])) 
						$_SESSION['form_attempts'] = 1;
					else $_SESSION['form_attempts']++;
					if( $_SESSION['form_attempts'] > 3)
						sleep($_SESSION['form_attempts']-2);
					return false;
				endif;
			else : log_sql_error( $err_args );
			endif;
		endif;

		return $results;
	}
	function user_logout(){
		$_SESSION = array();
		session_destroy(); 
		echo '<meta http-equiv="refresh" content="0;/">';
	}
	function user_change_password( $mysqli, $new_password, $search_field, $search_value ){

		$err_args = array( $mysqli, "user change password", $search_value );

		if( $stmt = $mysqli->prepare('UPDATE users SET password=?,updated=? WHERE '.$search_field.'=?') ) :

			$hash = password_hash($new_password, PASSWORD_DEFAULT);
			$now = date("Y-m-d H:i:s");

			if( !$stmt->bind_param( 'sss', $hash, $now, $search_value ) ) 
				log_sql_error( $err_args );

			if( !$stmt->execute() ) : 
				log_sql_error( $err_args );
			else : 
				log_sql_alter( "password update", $search_value );
				$_SESSION['updated'] = $now;
				return 'password changed';
			endif;

			if( !$stmt->close() ) :
				log_sql_error( $err_args );
			endif;

		else : 
			return 'password change '.$GLOBALS['FAILURE'];
			log_sql_error( $err_args );
		endif;
		return 'password change '.$GLOBALS['FAILURE'];
	}
	function user_change_password_via_email( $mysqli, $expiration, $email, $k, $uk, $username, $pass, $pass2 ){

		if( time() - $expiration > 0 )
			return array('key has expired');

		$results = array();

		if(empty($uk)) array_push($results,'provide the key');
		elseif(substr($k,0,43)!=$uk) array_push($results,'invalid key or username');
		if(empty($username)) array_push($results,'provide your username');
		$results = validate_new_password($results,$pass,$pass2);

		if( empty($results) ) :

			if ( FALSE !== $db_username = 
				db_select( $mysqli, 'users', 'username', 'email', $email, 'change password' ) ) :

				if( $db_username != $username ) :
					array_push($results,'invalid key or username');
				else : 
					array_push($results, user_change_password( $mysqli, $pass, 'email', $email ));
				endif;
			else :
				array_push($results,'password change '.$GLOBALS['FAILURE']);
			endif;
		endif;

		return $results;
	}
	function user_change_password_via_profile( $mysqli, $id, $old, $new, $new2 ){

		$results = array();

		if(empty($old)) array_push($results,'provide your old password');

		if( empty($results) ) :

			if ( FALSE !== $hash = 
				db_select( $mysqli, 'users', 'password', 'id', $id, 'update password' ) ) :

				if( password_verify($old,$hash) ) : 
					$results = validate_new_password($results,$new,$new2);
					if( empty($results) ) :
						array_push($results, user_change_password( $mysqli, $new, 'id', $id ));
					endif;
				else : 
					array_push($results, 'invalid old password');
				endif;
			else : 
				array_push($results,'password change '.$GLOBALS['FAILURE']);
			endif;
		endif;

		return $results;
	}
	function user_update( $mysqli, $id, $email, $username, $name ){

		$results = array();

		if(empty($name)) array_push($results,'provide a name');
		if ($email != $_SESSION['email'])
			$results = validate_email( $results, $mysqli, $email);
		if ($username != $_SESSION['username'])
			$results = validate_username( $results, $mysqli, $username);

		if( empty($results) ) :

			if( $stmt = $mysqli->prepare('UPDATE users SET email=?,username=?,name=?,updated=? WHERE id=?') ) :

				$err_args = array( $mysqli, 'user update', $id.' | '.$email.' | '.$username.' | '.$name );

				$now = date("Y-m-d H:i:s");

				if( !$stmt->bind_param( 'sssss', $email, $username, $name, $now, $id ) ) 
					log_sql_error( $err_args );
				if( !$stmt->execute() ) : log_sql_error( $err_args );
				else : 
					log_sql_alter( "profile updated", $email );
					$_SESSION['email'] = $email;
					$_SESSION['username'] = $username;
					$_SESSION['name'] = $name;
					$_SESSION['updated'] = $now;
					array_push($results,'profile updated');
				endif;
				if( !$stmt->close() ) : 
					log_sql_error( $err_args );
				endif;
			else :
				array_push($results,'profile update '.$GLOBALS['FAILURE']);
			endif;
		endif;

		return $results;
	}

/* HTML 
*///////////////////////////////////////////////////////////
	function html_form( $action, $inputs, $submit=NULL, $config=NULL ){

		echo '<form method="'
			.($config['method'] ? $config['method'] : 'post').'" '
			.'action="'.$action.'" '
			.'name="'.($config['name'] ? $config['name'] : $action.'_form').'" '
			.'id="'.($config['id'] ? $config['id'] : $action.'_form').'">'
			.'<fieldset>';

		foreach($inputs as $input) :
			if(is_array($input))
				html_input( $input[0], $input[1] );
			else html_input( $input );
		endforeach;

		if(empty($submit)) : html_submit($action);
		else : 
			if(is_array($submit)) :
				html_submit( $submit[0], $submit[1] );
			else : html_submit( $submit );
			endif;
		endif;

		echo '</fieldset></form>';
	}
	function html_input( $name, $config=NULL ){

		if( !$config['no_label'] )
			echo '<label for="'.$name.'">'
				.($config['title'] ? $config['title'] : $name).':'
				.'</label>';

		echo '<input type="'
			.($config['type'] ? $config['type'] : 'text').'" '
			.'name="'.$name.'" '
			.'id="'.($config['id'] ? $config['id'] : $name).'"'
			.($config['value'] ? ' value="'.$config['value'].'">' : '>');
	}
	function html_submit( $name, $config=NULL ){
		echo '<input type="submit" name="'.$name.'" '
			.'id="'.($config['id'] ? $config['id'] : $name).'" '
			.'value="'.($config['value'] ? $config['value'] : $name).'">';
	}
	function jq_fill_post_val( $name, $config=NULL ){
		if(!empty($_POST[$name]))
			echo "$('#".($config['id'] ? $config['id'] : $name)
				."').val('".$_POST[$name]."');";
	}
	function jq_fill_post_or_session_val( $name, $config=NULL ){
		if(!empty($_POST[$name])&&$_POST[$name]!=$_SESSION[$name]) 
			echo "$('#".($config['id'] ? $config['id'] : $name)
				."').val('".$_POST[$name]."');";
		else echo "$('#".($config['id'] ? $config['id'] : $name)
				."').val('".$_SESSION[$name]."');";
	}
	function show_messages($msgs){
		foreach ($msgs as $msg) :
			echo '<p>'.$msg.'</p>';
		endforeach; 
	}

/* LOGS
    append a log to the master log and sometimes another log
	format
		mm/dd/yy hh:ii:ss | type | ip | msg
  	parameters
  		msg : string
*/////////////////////////////////////////////////////////////
	function log_sql_new( $title, $data ){ log_sql_msg( $title, $data, 2 ); }
	function log_sql_request( $title, $data ){ log_sql_msg( $title, $data, 4 ); }
	function log_sql_alter( $title, $data ){ log_sql_msg( $title, $data, 3 ); }
	function log_sql_msg( $title, $data, $type ){
		log_special_msg( 'sql', $title.' | '.$data, $type );
	}
	//args = ( $mysqli, $title, $data=NULL )
	function log_sql_error( $args ){
		$m = $args[1].' | '.$args[0]->error;
		if($args[2]) $m.=' | '.$args[2];
		log_special_msg( 'sql_error', $m, 1 );
	}
	function log_error( $title, $data ){
		log_special_msg( 'error', $title.' | '.$data, 1);
	}
	function log_special_msg( $file, $msg, $type ){
		$m = prepare_msg($msg, $type);
		append_msg( "../log/master.txt", $m );
		append_msg( "../log/".$file.".txt", $m );
	}	
	function log_msg( $msg, $type=0 ){
		append_msg( "../log/master.txt", 
			prepare_msg( $msg, $type ) );
	}
	function prepare_msg( $msg, $type=0 ){
		return date('m/d/y H:i:s')." | ".$type." | "
			.$GLOBALS['client_ip']." | ".$msg."\n";
	}
	function append_msg( $filename, $msg ){
		$f = fopen($filename,"a");
		fwrite($f,$msg);
		fclose($f);
	}
?>