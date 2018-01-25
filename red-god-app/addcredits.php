<?php
require "loginutil.php";

$userid = null;
$usercredits = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (!empty($_POST)) {
    $userid = $_POST["userid"];
		$usercredits = $_POST["usercredits"];
	}
} else {
	if (!empty($_GET)) {
    $userid = $_GET["userid"];
		$usercredits = $_GET["usercredits"];
	}
}

if ($auth && $logged) {
  ob_end_clean();
	$result = array ();

	if (!isset($userid) || !isset($usercredits)) $result = array ('error'=>'dataempty');
  else {
		$verifycredits_str = "SELECT * FROM users WHERE ID='$id'";
		$verifycredits = mysqli_query($connection, $verifycredits_str);

		if(mysqli_num_rows($verifycredits)  == 1) {

      while($row = mysqli_fetch_array($query)) {
        if ($row['CREDITS']) $credits = $row['CREDITS'];
      }

      if ($usercredits < $credits) {
				$update_mov_str = "INSERT INTO movements (ID,UID,MID,TYPE,CREDITS,UCB,UCA,MCB,MCA,FECHA) VALUES (NULL,'$userid','$id','IN','$usercredits', (SELECT CREDITS FROM users WHERE ID='$userid'),((SELECT CREDITS FROM users WHERE ID='$userid')+'$usercredits'), (SELECT CREDITS FROM users WHERE ID='$id'),((SELECT CREDITS FROM users WHERE ID='$id')-'$usercredits'), NULL)";
				$update_mov = mysqli_query($connection, $update_mov_str);
				if ($update_mov) {
	        $update_str = "UPDATE users SET CREDITS = CASE ";
	        $update_str = $update_str . "WHEN ID='$id' THEN CREDITS - '$usercredits' ";
	        $update_str = $update_str . "WHEN ID='$userid' THEN CREDITS + '$usercredits' ";
					$update_str = $update_str . "ELSE CREDITS END";

	        $update = mysqli_query($connection, $update_str);

					$newcredits = $credits - $usercredits;

        	if ($update) {
						$result = array ('operation'=>'success','credits'=> $newcredits);
					} else $result = array ('error'=>'adding');
				} else $result = array ('error'=>'update');
      } else $result = array ('error'=>'nocredits');
    } else $result = array ('error'=>'verifying');
   }
} else $result = array ('error'=>'privileges');

mysqli_close($connection);
echo json_encode($result);

 ?>
