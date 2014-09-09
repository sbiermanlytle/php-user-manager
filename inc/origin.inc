<?php
$dbhost = "basicUserApp.db.8247988.hostedresource.com";
$dbname = "basicUserApp";
$dbuser = "basicUserApp";
$dbpass = "userApp@13";
$debug = true;

$client_ip = $_SERVER['REMOTE_ADDR'];
date_default_timezone_set('America/New_York');

//start session
session_start();

//SESSION SECURITY
//prevent session fixation
if (!isset($_SESSION['initiated'])){
    session_regenerate_id();
    $_SESSION['initiated'] = true;
}
//prevent session hijacking
if (isset($_SESSION['HTTP_USER_AGENT'])){
    if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
        $login=$VISITOR;
} else $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);

//CONNECT TO DATABASE
$sqlConn =  new mysqli($dbhost, $dbuser, $dbpass, $dbname);

//INIT APP DATA OBJECT
include '../obj/app.php';
include '../obj/msg.php';
include '../inc/emails.inc';
$app = new App($sqlConn);

//DEBUG LOG
if ($sqlConn->connect_errno)
    $app->log("SQL Error upon connect: "
    	.$sqlConn->connect_errno." ".$sqlConn->connect_error);
$app->log($sqlConn->host_info);
$app->log('ip: '.$client_ip);

?>