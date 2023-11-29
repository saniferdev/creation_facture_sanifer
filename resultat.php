<?php

include('config.php');

require_once("classes/api.php");

$api   		= new API();

$api->link 	= $link;
$api->url 	= $url;
$api->key 	= $key;

$numero     = "";
if(isset($_POST['num']) && !empty($_POST['num'])){
  $num      = $_POST['num'];
  $data     = $api->getData($url,$key,$num);

  foreach ($data as $value) {
    $numero   = $value->receipt_number;
  } 
  if(!isset($numero) && empty($numero)) echo "";
  else echo json_encode($data);
}
else exit;

?>