<?php

ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);

//define('MAX_FILE_SIZE', 600000);


$key    			= "184565ial6lZJjb5SeaZmWm2tu";
$url    			= "https://talys.clic-till.com/wsRest/1_4/wsServerReceipt/getReceipt/";

$sqlServerHost 		= '192.168.130.71';
$sqlServerDatabase 	= 'JANUS';
$sqlServerUser 		= 'janus_v2';
$sqlServerPassword 	= 'WesoKhu640Rfz0Yi';

$connectionInfo 	= array("Database" => $sqlServerDatabase, "UID" => $sqlServerUser, "PWD" => $sqlServerPassword, "CharacterSet" => "UTF-8");
$link 		     = sqlsrv_connect($sqlServerHost, $connectionInfo);
if (!$link) {
     die( print_r( sqlsrv_errors(), true));
}

