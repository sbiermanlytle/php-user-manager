<?php 
//DOMAIN INFO
$SITE_NAME = 'my site';
$DOMAIN = 'mysite.com';
$DOMAIN_URL = 'http://mysite.com/';
date_default_timezone_set('America/New_York');

//ENCRYPTION KEYS
//MK = 64 char hexadecimal
$MK = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
//DL = 20 char hexadecimal
$DL = "xxxxxxxxxxxxxxxxxxxx";

//PATH KEYS (20 char hexadecimal)
$ACTIVATE_USER = 'xxxxxxxxxxxxxxxxxxxx';
$BAN_IP = 'xxxxxxxxxxxxxxxxxxxx';
$CHANGE_PASSWORD = 'xxxxxxxxxxxxxxxxxxxx';

//CONSTANTS
$PASSWORD_KEY_LIFETIME = 5; //in minutes

//DATABASE CREDENTIALS
$dbhost = "xxxx";
$dbname = "xxxx";
$dbuser = "xxxx";
$dbpass = "xxxx";

//REGEX VALIDATION
$VALID_EMAIL = "/.+@.+\..{1,254}/";

//DATABASE CONFIG
$database = array(
	"users" => array(
			"id" => "INT(25) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
			"role" => "TINYINT(1) DEFAULT 0",
			"banned" => "TINYINT(1) DEFAULT 0",
			"email" => "VARCHAR(254) NOT NULL",
			"username" => "VARCHAR(70) NOT NULL",
			"name" => "VARCHAR(70) NOT NULL",
			"created" => "DATETIME NOT NULL",
			"updated" => "DATETIME NOT NULL",
			"password" => "VARCHAR(255) NOT NULL"
		)
);

//EMAIL CONFIG
$EMAIL_HEADER = 
'<body style="width:100%;max-width:600px;background-color:gainsboro;">
	<div style="width:100%;height:100px;background-color:#00baff;">
		<img style="margin:20px 0 20px 40px;" src="'.$DOMAIN_URL.'img/email-logo.png" />
	</div>
	<div style="width:80%;margin:0 auto;background-color:white;padding:20px;font-family:Verdana;border-bottom:10px solid gainsboro">
';
$EMAIL_FOOT = '</div></body></html>';

//STRINGS
$FAILURE = ' failed for an unknown reason, try again and contact customer service if the issue persists';
?>