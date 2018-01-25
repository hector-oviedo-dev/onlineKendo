<?php
require "../../connect.php";
require "../../login.php";

$auth = false;
$result = array ();

//Error (comming from connect.php or login.php)
if ($error != "false")
{
  $result = array ('error'=>$error);
} else {
  //Wrong user or password (comming from login.php)
  if (!$logged) {
    $result = array ('error'=>'login');
  } else {
    //Wrong privilege level (coming from login.php, "type")
    if ($type != "2") {
      $result = array ('error'=>'privilege');
    } else {
      $auth = true;
      $result = array ('login'=>'success','privilege'=>'ok','credits'=>$credits,'id'=>$id);
    }
  }
}
 ?>
