<?php
require "loginutil.php";

$username = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (!empty($_POST)) {
    $username = $_POST["username"];
	}
} else {
	if (!empty($_GET)) {
    $username = $_GET["username"];
	}
}

if ($auth && $logged) {
  ob_end_clean();
	$result = array ();

	if (!isset($username)) $result = array ('error'=>'dataempty');
  else {
    $list_str = "SELECT * FROM users WHERE NAME LIKE '%$username%' AND MID = '$id'";
		$list = mysqli_query($connection, $list_str);

		if ($list) {
      $users = array();

      while($row = mysqli_fetch_array($list)) {
        $user = array();

        if ($row['ID']) $userid = $row['ID'];
        if ($row['NAME']) $username = $row['NAME'];
        if ($row['LOGIN']) $userlogin = $row['LOGIN'];
        if ($row['CREDITS']) $usercredits = $row['CREDITS'];
				else $usercredits = 0;
        if ($row['FECHA']) $userfecha = $row['FECHA'];

        $user = array ('ID' => $userid,'NAME' => $username,'LOGIN' => $userlogin,'CREDITS' => $usercredits,'FECHA' => $userfecha);

        array_push($users,$user);
      }
      $result = array ('listuser'=>'success','users'=>$users);
    } else $result = array ('error'=>'noresult');
	}
} else $result = array ('error'=>'error');


mysqli_close($connection);
echo json_encode($result);

 ?>
