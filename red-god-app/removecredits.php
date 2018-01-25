<?php
require "loginutil.php";

$userid = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (!empty($_POST)) {
    $userid = $_POST["userid"];
	}
} else {
	if (!empty($_GET)) {
    $userid = $_GET["userid"];
	}
}

if ($auth && $logged) {
  ob_end_clean();
	$result = array ();

	if (!isset($userid)) $result = array ('error'=>'dataempty');
  else {
		$update_mov_str = "INSERT INTO movements (ID,UID,MID,TYPE,CREDITS,UCB,UCA,MCB,MCA,FECHA) VALUES (NULL,'$userid','$id','OUT',(SELECT CREDITS FROM users WHERE ID = '$userid'),(SELECT CREDITS FROM users WHERE ID = '$userid'),'0','0','0',NULL)";
		$update_mov = mysqli_query($connection, $update_mov_str);

		if ($update_mov) {
	    $removecredits_str = "UPDATE users SET CREDITS=0 WHERE ID='$userid'";
			$removecredits = mysqli_query($connection, $removecredits_str);

			if ($removecredits) {
				$result = array ('operation'=>'success');
			}
		} else $result = array ('error'=>'error');
	}
} else {
	$result = array ('error'=>'error');
	die();
}

mysqli_close($connection);
echo json_encode($result);

 ?>
