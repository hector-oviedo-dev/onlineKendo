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
    $remove_str = "DELETE FROM users WHERE ID='$userid'";
		$remove = mysqli_query($connection, $remove_str);

		if ($remove) $result = array ('operation'=>'success');
		else $result = array ('error'=>'error');
	}
} else {
	$result = array ('error'=>'error');
	die();
}

mysqli_close($connection);
echo json_encode($result);

 ?>
