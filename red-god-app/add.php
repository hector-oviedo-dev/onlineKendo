<?php
require "loginutil.php";

$loginuser = null;
$nameuser = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (!empty($_POST)) {
    $loginuser = $_POST["loginuser"];
		$nameuser = $_POST["nameuser"];
	}
} else {
	if (!empty($_GET)) {
    $loginuser = $_GET["loginuser"];
		$nameuser = $_GET["nameuser"];
	}
}

if ($auth && $logged) {
	$result = array ();

	if (!isset($loginuser) || !isset($nameuser)) $result = array ('error'=>'dataempty');
  else {
		$verify_str = "SELECT * FROM users WHERE LOGIN = '$loginuser'";
		$verify = mysqli_query($connection, $verify_str);
		if(mysqli_num_rows($verify)  == 1) {
				$result = array ('error'=>'duplicate');
			} else {
				$insert_str = "INSERT INTO users (ID,MID,LOGIN,PASS,NAME,TYPE,CREDITS,GAMES,GID,GFSR,GFSP,GFSW,GFSD,FECHA) VALUES (NULL,'$id','$loginuser', '123','$nameuser','0','0','DA,FR',NULL,NULL,NULL,NULL,NULL,NULL)";
				$insert = mysqli_query($connection, $insert_str);

				if ($insert) $result = array ('operation'=>'success');
				else $result = array ('error'=>'adding');
			}
	}
} else $result = array ('error'=>'privileges');

mysqli_close($connection);
echo json_encode($result);

 ?>
