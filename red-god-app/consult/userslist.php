<!DOCTYPE html>
<html lang="en">
<head>
  <title>Consulta - JUEGO</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body class="container" bgcolor="#cccccc">

<?php

$valid = false;
$resultvalid = false;

$checkuser = null;
$checkpass = null;
if (!empty($_GET)) {
	$checkuser = $_GET["user"];
	$checkpass = $_GET["pass"];
}

if (isset($checkuser) && isset($checkpass)) $valid = true;

if ($valid) {
	require "loginutil.php";

$username = null;

$username = "";

if ($auth && $logged) {
  ob_end_clean();
	$result = array ();

	if (!isset($username)) $result = array ('error'=>'dataempty');
  else {
    $list_str = "SELECT * FROM users";
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
			$resultvalid = true;
    } else $result = array ('error'=>'noresult');
	}
} else $result = array ('error'=>'error');

mysqli_close($connection);

if (!$resultvalid) echo json_encode($result);
else {
echo '


<div class="container">
  <h2>Lista de usuarios</h2>
  <table class="table">
    <thead>
      <tr>
				<th>ID</th>
        <th>Nombre Usuario</th>
        <th>Creditos</th>
				<th>Telefono/Login</th>
				<th>Fecha de alta</th>
      </tr>
    </thead>
    <tbody>
		';

$actualclass = "success";
foreach ($users as $rowTMP) {
	echo '<tr class="'.$actualclass.'">
				<td>'.$rowTMP["ID"].'</td>
				<td>'.$rowTMP["NAME"].'</td>
				<td>'.$rowTMP["CREDITS"].'</td>
				<td>'.$rowTMP["LOGIN"].'</td>
				<td>'.$rowTMP["FECHA"].'</td>
	</tr>';
	if ($actualclass == "success") $actualclass = "warning";
	else $actualclass = "success";
}
echo '
  </table>
</div>
';
}



} else {
 echo '
 <div class="row"><div class="jumbotron">
 <form name="form" method="get">
	<div class="form-group">
		<label for="userlabel">USUARIO</label>
		<input type="user" class="form-control" id="user" name="user" placeholder="Usuario">
	</div>

	<div class="form-group">
		<label for="passlabel">PASS</label>
		<input type="password" class="form-control" id="pass" name="pass" placeholder="Password">
	</div>

	<div><button type="submit" class="btn btn-default">Ingresar</button></div>
 </form>
 </div></div>

';
}

 ?>

 </body>
 </html>
