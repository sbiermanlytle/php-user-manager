<?php
class App{

	//SITE SPECIFIC DATA - CHANGE THESE!!!!!!
	private $REST_AUTH_SITE_KEY = 'a4ki3h58rfhf8fk39f39fm49vfo3k4';
	private $hostname = "http://app-user.iiointeractive.com";
	//USER COLUMN LABEL
	//PLEASE do not name your users column 'users' - change it to something unique
	private $users_column = "users";
	//USER FIELDS
		private $user_id_field = "id";
		private $user_email_field = "email";
		private $user_username_field = "username";
		private $user_name_field = "name";
		private $user_role_field = "role";
		private $user_verified_field = "verified";
		private $user_created_field = "created";
		private $user_updated_field = "updated";
		private $user_password_field = "password";
		private $user_salt_field = "salt";
		private $user_code_field = "code";

	//generic failure message to be used whenever we don't
	//want the user to know specifically what went wrong
	private $fail_msg = "failed for an unknown reason. Try again and contact Customer Service if the error persists";

	//DEFINE CONSTANTS
		public $AUTHENTICATED = FALSE;
		public $FAILURE = 1;
		public $SUCCESS = 2;

	//DEFINE REGEX VALIDATION
	public $VALID_USERNAME = "/^[A-Za-z0-9!@#%-=,~:;\*\^\.\[\$\(\)\|\+\{\\_ ]{2,65}$/";
	public $VALID_EMAIL = "/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/";
	public $VALID_NAME = "/^[A-Za-z' \-]{2,65}$/";

	//SQL CONNECTION
	private $sql;

	//USER DATA
		private $user_id;
		private $user_email;
		private $user_username;
		private $user_name;
		private $user_role;
		private $user_verified;
		private $user_password;
		private $user_salt;

	//DEBUG LOGS
	private $log;

	/*CONSTRUCTOR*/
	function App( $s ){
		$this->sql = $s;
		$this->log = array();
	}

	/*DATA*/
	//retreives one field value from database
	function getData( $sqlcmd, $field ){

		//prepare
		$get = mysqli_prepare($this->sql,$sqlcmd);
		if(!$get) $this->log_sqlError();

		//bind data
	   	if(!mysqli_stmt_bind_param($get,"s",$field))
	   		$this->log_sqlError();

	   	//execute
		if(!mysqli_stmt_execute($get))
			$this->log_sqlError();

		//bind result
		if(!mysqli_stmt_bind_result($get, $result))
			$this->log_sqlError();

		//fetch result
		if(mysqli_stmt_fetch($get)===FALSE)
			$this->log_sqlError();

		//close
		if(!mysqli_stmt_close($get))
			$this->log_sqlError();
		return $result;
	}
	//retreives local user data
	function getUserData($field){
		if($field=="id") return $this->user_id;
		else if($field=="email") return $this->user_email;
		else if($field=="name") return $this->user_name;
		else if($field=="username") return $this->user_username;
		else if($field=="role") return $this->user_role;
		else if($field=="verified") return $this->user_verified;
	}
	//converts values to references of values
	function referenceValues($arr){
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
	}

	/*ENCRYPTION*/
	function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++)
	        $randomString .= $characters[rand(0, strlen($characters) - 1)];
	    return $randomString;
	}
	function secure_digest($a,$b,$c,$d) {
		$result = sha1($a.'--'.$b.'--'.$c.'--'.$d);
		return $result;	
	}
	function encrypt($p,$s){

		$digest = $this->REST_AUTH_SITE_KEY;

		//THIS IS THE PASSWORD HASH
		//implement a unique and complex process for hashing

		//example... CHANGE THIS!!!
		for( $i=1; $i<10; $i++)
		   $digest = $this->secure_digest( $digest, $s, $p, $this->REST_AUTH_SITE_KEY );

		return $digest;
	}

	/*DEBUG*/
	//add a message to the debug log
	function log($text,$type=0){ 
		$msg = new Msg($text,$type);
		array_push($this->log, $msg);
		return $msg; 
	}
	//print the debug log
	function print_log(){
		foreach($this->log as $msg)
			echo $msg->getMsg();
	}
	//add sql error and errno to debug log
	function log_sqlError(){
		$this->log('errno: '.$this->sql->errno.', error: '.$this->sql->error,
			$this->FAILURE);
	}
	//log sql error and add a generic failure message to results
	function sqlError($results,$action){
		$this->log('errno: '.$this->sql->errno.', error: '.$this->sql->error,
			$this->FAILURE);
		array_push($results,new Msg(
			$action." ".$this->fail_msg,
			$this->FAILURE));
		return $results;
	}

	/*INPUT RESPONSE*/
	function inputError($results,$msg){
		array_push($results,$this->log($msg,$this->FAILURE));
		return $results;
	}
	function inputSuccess($results,$msg){
		array_push($results,$this->log($msg,$this->SUCCESS));
		return $results;
	}
	function failMsg(){ return $this->fail_msg;}
	function print_msgs($msgs){
		if(!empty($msgs))
		  foreach($msgs as $m)
		    echo $m->getMsg();
	}

	/*USER*/
	function authenticate(){

		//begin debug log
		$this->log('______________');
		$this->log("AUTHENTICATION");

		//check if logged in
		if(!empty($_SESSION['Email'])){

			//user is logged in
			$this->AUTHENTICATED = TRUE;

			//retrieve user data
			$get = mysqli_prepare($this->sql,
				'SELECT '.$this->user_id_field
					.', '.$this->user_username_field
					.', '.$this->user_name_field
					.', '.$this->user_role_field
					.', '.$this->user_verified_field
					.', '.$this->user_password_field
					.', '.$this->user_salt_field
					.' FROM '.$this->users_column.' WHERE '.$this->user_email_field.'=?');
			if(!$get) $this->log_sqlError();
		   	if(!mysqli_stmt_bind_param($get,"s",$_SESSION['Email']))
		   		$this->log_sqlError();
			if(!mysqli_stmt_execute($get))
				$this->log_sqlError();
			if(!mysqli_stmt_bind_result($get, 
				$this->user_id, 
				$this->user_username, 
				$this->user_name, 
				$this->user_role, 
				$this->user_verified, 
				$this->user_password,
				$this->user_salt))
				$this->log_sqlError();
			if(!mysqli_stmt_fetch($get))
				$this->log_sqlError();
			if(!mysqli_stmt_close($get))
				$this->log_sqlError();

			//set SESSION email value
			//IMPORTANT: THIS IS THE ONLY STORED SESSION DATA!!!
			//everything else is stateless
			$this->user_email = $_SESSION['Email'];

			//log user data
			$this->log('logged in',$this->SUCCESS);
			$this->log('id: '.$this->user_id);
			$this->log('email: '.$this->user_email);
			$this->log('username: '.$this->user_username);
			$this->log('name: '.$this->user_name);
			$this->log('role: '.$this->user_role);
			$this->log('verified: '.$this->user_verified);
			$this->log('password: '.$this->user_password);
			$this->log('salt: '.$this->user_salt);
		} 
		//not logged in
		else {
			$this->AUTHENTICATED = FALSE;
			$this->log('not logged in',$this->FAILURE);
		}
	}
	function logout(){
		$_SESSION = array();
		session_destroy(); 
		echo '<meta http-equiv="refresh" content="0;/">';
	}
	function login($login,$password){

		//log header
		$this->log('_____________');
		$this->log("LOGIN ATTEMPT");

		//init results
		$results = array();

		//determine if login is valid email
		if(preg_match($this->VALID_EMAIL,$login)){
			$this->log("valid email");
			$login_label = "email";
			$field = $this->user_email_field;
		}
		//determine if login is valid username 
		else if(preg_match($this->VALID_USERNAME,$login)){
			$this->log("valid username");
			$login_label = "username";
			$field = $this->user_username_field;
		} 
		//else return invalid
		else return $this->inputError($results,
				"'".$login."' is not a valid username or email address");

		//log data
		$this->log($login_label.": ".$login);
		$this->log("password: ".$password);

		//get salt
		$salt = $this->getData("SELECT ".$this->user_salt_field." FROM ".$this->users_column." WHERE ".$field."=?",$login);
		if(empty($salt)) {
			//salt not found
			$this->log("salt not found",$this->FAILURE);
			return $this->inputError($results,
				"'".$login."' is not registered");
		}
		//log salt
		$this->log("salt: ".$salt);

		//encrypt given password
		$encrypted_password=$this->encrypt($password,$salt);
		$encrypted_password=substr($encrypted_password,0,30);
		//log encrypted password
		$this->log("encrypted password: ".$encrypted_password);

		//get saved password
		$saved_password = $this->getData(
			"SELECT ".$this->user_password_field." FROM ".$this->users_column." WHERE ".$field."=?",$login);
		if(empty($saved_password)){
			//password not found
			array_push($results,new Msg("Login ".$this->fail_msg));
			$this->log("password not found in database",$this->FAILURE);
			return $results;
		}
		//log saved password
		$this->log("saved password____: ".$saved_password);

		//validate password
		if($encrypted_password!=$saved_password)
			return $this->inputError($results,
				"incorrect password");

		//login user
		if($login_label=='email') 
			$_SESSION['Email'] = $login;
		else $_SESSION['Email'] = $this->getData(
			"SELECT ".$this->user_email_field." FROM ".$this->users_column." WHERE ".$this->user_username_field."=?",$login);

		//check if getData failed
		if(!$_SESSION['Email']){
			array_push($results,new Msg('login '.$this->fail_msg,$this->FAILURE));
			return $results;
		}

		//return success
		$this->log("login success",$this->SUCCESS);
		return;
	}
	function editUser($name,$email,$username){

		//log post data
		$this->log('________________________');
		$this->log("USER DATA CHANGE ATTEMPT");
		$this->log('name: '.$name);
		$this->log('email: '.$email);
		$this->log('username: '.$username);

		$results = array();

		//update name if changed
		if($name!=$this->user_name){

			//validate name
			if(!preg_match($this->VALID_NAME,$name)){

				//name invalid
				array_push($results,new Msg('invalid name. must be between 2-65 characters.',$this->FAILURE));
				$this->log('invalid name',$this->FAILURE);

			} else {

				//update name
				if($this->updateUserField($this->user_id_field, $this->user_id, $this->user_name_field, $name) === FALSE)
					array_push($results,new Msg('name update '.$this->fail_msg, $this->FAILURE));
				else {
					$this->user_name = $name;
					array_push($results,$this->log(
						'name update successful',
						$this->SUCCESS));
				}
			}
		}

		//update email if changed
		if($email!=$this->user_email){

			//validate email address
			if(!preg_match($this->VALID_EMAIL,$email))

				//email invalid
				array_push($results,$this->log(
					"invalid email address",
					$this->FAILURE));
			else {

				//check for duplicate
				$exists = $this->getData("SELECT ".$this->user_id_field." FROM ".$this->users_column." WHERE ".$this->user_email_field."=?",$email);
				if(!empty($exists)){
					array_push($results,$this->log(
						"email already registered",
						$this->FAILURE));

				//no duplicate
				} else {
					//update email
					if($this->updateUserField($this->user_id_field, $this->user_id, $this->user_email_field, $email) === FALSE)
						array_push($results,new Msg('email update '.$this->fail_msg,$this->FAILURE));
					else {
						$this->user_email = $email;
						$_SESSION['Email'] = $email;
						array_push($results,$this->log(
							"email address update successful",
							$this->SUCCESS));
					}
				}
			}
		}

		//update username if changed
		if( $username != $this->user_username ){

			//validate username address
			if( !preg_match( $this->VALID_USERNAME, $username )){	
				array_push($results,new Msg('invalid username. must be between 2-65 characters.',
					$this->FAILURE));
				$this->log('invalid username', $this->FAILURE);

			} else {

				//check for duplicate
				$exists = $this->getData('SELECT '.$this->user_id_field.' FROM '.$this->users_column.' WHERE '.$this->user_username_field.'=?', $username);
				if(!empty($exists))
					array_push($results,$this->log(
						"username already registered",
						$this->FAILURE));

				//no duplicate
				else {
					//update username
					if($this->updateUserField( $this->user_id_field, $this->user_id, $this->user_username_field, $username) === FALSE)
						array_push($results,new Msg('username update '.$this->fail_msg, $this->FAILURE));
					else {
						$this->user_username = $username;
						array_push($results,$this->log(
							"username update successful",
							$this->SUCCESS));
					}
				}
			}
		}
		
		return $results;
	}
	function editUserPassword($old,$new,$p_v){

		//log post data
		$this->log('_______________________');
		$this->log("PASSWORD CHANGE ATTEMPT");
		$this->log('old: '.$old);
		$this->log('new: '.$new);
		$this->log('p_v: '.$p_v);

		//init results
		$results = array();

		//check password match
		if($new!=$p_v)
			return $this->inputError( $results, "new passwords do not match" );

		//encrypt given old password
		$encrypted_password=$this->encrypt($old,$this->user_salt);
		$encrypted_password=substr($encrypted_password,0,30);

		//log passwords
		$this->log("saved password: ".$this->user_password);
		$this->log("given password: ".$encrypted_password);

		//check old password match
		if($this->user_password!=$encrypted_password)
			return $this->inputError($results,"invalid current password");

		//update password
		if($this->changePassword($this->user_email,$new))
			return $this->inputSuccess($results,"password change successful");
		else return $this->inputError($results,"password change ".$this->fail_msg);
	}
	function changePassword($email,$password){

		//init new data
		$now = date("Y-m-d H:i:s");
		$salt = $this->generateRandomString(30);
		$encrypted_password = $this->encrypt($password,$salt);

		//init query
		return $this->updateUserFields($this->user_email_field, $email,
				array('password_field'=>$this->user_password_field,'salt_field'=>$this->user_salt_field),
				array('password_value'=>$encrypted_password,'salt_value'=>$salt));
	}
	function resetPassword($email){

		$results = array();

		//check email exists
		$exists = $this->getData('SELECT '.$this->user_id_field.' FROM '.$this->users_column.' WHERE '.$this->user_email_field.'=?', $email);
		if(empty($exists))
			return $this->inputError($results,
				"'".$email."' is not registered");

		//generate new password
		$pass = $this->generateRandomString();

		//email user
 	 	if( email_passwordReset($email,$pass) ){
 	 		if($this->changePassword($email,$pass))
 	 			return;
 	 		else return array( new Msg(
				"password reset failed for an unknown reason. You will receive an email, but no changes have been made in the database. Contact Customer Service if the issue persists",
				$this->FAILURE));
 	 	}

 	 	return array( new Msg('Password reset '.$fail_msg, $this->FAILURE) );
	}
	function updateUserField($searchField,$searchValue,$updateField,$updateValue){
		return $this->updateUserFields($searchField,$searchValue,
				array('update_field'=>$updateField),
				array('update_value'=>$updateValue));
	}
	function updateUserFields($searchField, $searchValue, $updatedFields, $updatedValues){

		//init query
		$cmd = 'UPDATE '.$this->users_column.' SET ';
		$types = "ss";
		foreach($updatedFields as $f){
			$cmd.=$f."=?,";
			$types.="s";
		}
		$cmd.=$this->user_updated_field."=? WHERE ".$searchField."=?";

		//prepare query
		$query = mysqli_prepare($this->sql,$cmd);
		if(!query) $this->log_sqlError();

		//prepare params
		$params = array('query' => $query,'types' => $types);
		$now = date("Y-m-d H:i:s");
		$updatedValues['updated'] = $now;
		foreach($updatedValues as $k => $v)
			$params[$k] = $updatedValues[$k];
		$params['search'] = $searchValue;

		//bind params
		if(!call_user_func_array( 'mysqli_stmt_bind_param', $this->referenceValues($params)))
			$this->log_sqlError();

		//execute
		$response = mysqli_stmt_execute($query);

		//close
	    if(!mysqli_stmt_close($query))
	    	$this->log_sqlError();

	    //return
	    if(!$response) $this->log_sqlError();
	    return $response;
	}
	function register($name, $email, $username, $password, $p_v){

		//log post data
		$this->log('_________________________');
		$this->log("USER REGISTRATION ATTEMPT");
		$this->log('name: '.$name);
		$this->log('email: '.$email);
		$this->log('username: '.$username);
		$this->log('password: '.$password);
		$this->log('p_v: '.$p_v);

		//init results
		$results = array();

		//check password match
		if($password!=$p_v)
			array_push($results,$this->log(
				"passwords do not match",
				$this->FAILURE));

		//validate name
		if(!preg_match($this->VALID_NAME,$name)){	
			$this->log('invalid name',$this->FAILURE);
			array_push($results,new Msg(
				"invalid name. must be between 2-65 characters.",
				$this->FAILURE));
		}
		//validate email
		if(!preg_match($this->VALID_EMAIL,$email))
			array_push($results,$this->log(
				"invalid email address",
				$this->FAILURE));

		//validate username
		if(!preg_match($this->VALID_USERNAME,$username)){	
			$this->log('invalid name',$this->FAILURE);
			array_push($results,new Msg(
				"invalid username. must be between 2-65 characters.",
				$this->FAILURE));
		}

		//return errors
		if(!empty($results)) return $results;

		//check duplicate email
		$exists = $this->getData('SELECT '.$this->user_id_field.' FROM '.$this->users_column.' WHERE '.$this->user_email_field.'=?',$email);
		if(!empty($exists))
			array_push($results,$this->log(
				"email address already registered",
				$this->FAILURE));

		//check duplicate username
		$exists = $this->getData('SELECT '.$this->user_id_field.' FROM '.$this->users_column.' WHERE '.$this->user_username_field.'=?',$username);
		if(!empty($exists))
			array_push($results,$this->log(
				"username already registered",
				$this->FAILURE));

		//return error
		if(!empty($results)) return $results;

		//prepare query
		$query = mysqli_prepare($this->sql,
			'INSERT INTO '.$this->users_column.' ('
				 .$this->user_email_field
			.', '.$this->user_username_field
			.', '.$this->user_name_field
			.', '.$this->user_password_field
			.', '.$this->user_created_field
			.', '.$this->user_updated_field
			.', '.$this->user_salt_field
			.', '.$this->user_code_field
			.') VALUES(?,?,?,?,?,?,?,?)');
		//check prepare error
		if(!$query) return $this->sqlError($results,"Registration");


		//init data
		$now = date("Y-m-d H:i:s");
		$salt = $this->generateRandomString(30);
		$code = $this->generateRandomString(30);
		$encrypted_password = $this->encrypt($password,$salt);

		//bind data
		if(!mysqli_stmt_bind_param($query, "ssssssss", $email, $username, $name, $encrypted_password, $now, $now, $salt, $code)){
			mysqli_stmt_close($query);
			return $this->sqlError($results,"Registration");
		}

		//execute
		$result = mysqli_stmt_execute($query);
		if(!mysqli_stmt_close($query))
			return $this->sqlError($results,"Registration");

		//failure
		if(!$result) return $this->sqlError($results,"Registration");

		//success
		if(email_registration($this->hostname,$email,$name,$username,$code)){

			//LOG USER IN 
			//USE TO LOG USER IN UPON REGISTRATION
			//$_SESSION['Email'] = $email;

			return;
		}
		//email failure
		array_push($results,new Msg(
			"Registration succeeded but the email failed to send. Please contact Customer Service.",
			$this->FAILURE));
		return $results;
	}
	function activate($email,$code){

		//get saved key
		$key = $this->getData('SELECT '.$this->user_code_field.' FROM '.$this->users_column.' WHERE '.$this->user_email_field.'=?',$email);

		//verify key
		if($key==$code)

			//ACTIVATE 
			return $this->updateUserFields($this->user_email_field, $email,
				array('verified_field'=>$this->user_verified_field,'code_field'=>$this->user_code_field),
				array('verified_value'=>1,"code_value"=>NULL));

		return false;
	}
}
?>