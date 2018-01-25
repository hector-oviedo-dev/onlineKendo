<!DOCTYPE html>
<html lang="en">
<head>
  <title>Consulta - JUEGO</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body class="container">

<?php

$valid = false;
$resultvalid = false;

$checkuser = null;
$checkpass = null;
$checkuserid = null;
if (!empty($_GET)) {
	$checkuser = $_GET["user"];
	$checkpass = $_GET["pass"];
  $checkuserid = $_GET["userid"];
}

if (isset($checkuser) && isset($checkpass) && isset($checkuserid)) $valid = true;

if ($valid) {

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
		$consult_str = "SELECT movements.*,users.ID,users.NAME FROM movements,users WHERE users.ID = movements.UID AND movements.UID='$userid'";
		$consult = mysqli_query($connection, $consult_str);

		if ($consult) {
      $user_plays = array();

			$coinin = 0;
			$coinout = 0;
			$ganancia = 0;
			$devolucion = 0;

      while($row = mysqli_fetch_array($consult)) {

				$mov_type = null;
				foreach ($row as $key => $value) if ($key == "TYPE") {
					if ($value == "IN" || $value == "OUT") $mov_type = $value;
				}

				if (isset($mov_type)) {
					$user_play = array();

					foreach ($row as $key => $value) if ($key == "NAME") $play_name = $value;

					if ($mov_type == "IN") $user_coinin = $row['CREDITS'];
					else $user_coinin = 0;

					if ($mov_type == "OUT") $user_coinout = $row['CREDITS'];
					else $user_coinout = 0;

	        if ($row['FECHA']) $play_fecha = $row['FECHA'];

					$coinin = $coinin + $user_coinin;
					$coinout = $coinout + $user_coinout;

	        $user_play = array ('CREDITS' => $row['CREDITS'],'COININ' => $user_coinin,'COINOUT' => $user_coinout,'TYPE' => $mov_type, 'FECHA' => $play_fecha);

	        array_push($user_plays,$user_play);
				}
      }

      $ganancia = $coinin - $coinout;
      $devolucionwin = ($ganancia * 100) / $coinin;
      $devolucion = 100 - $devolucionwin;
      $devolucion = number_format($devolucion, 2, '.', '');

      $result = array ('consult'=>'success','coinin'=>$user_coinin,'coinout'=>$user_coinout,'ganancia'=>$ganancia,'devolucion'=>$devolucion,'win'=>$devolucionwin,'plays'=>$user_plays);
			$resultvalid = true;
    } else $result = array ('error'=>'noresult');
	}
} else $result = array ('error'=>'error');

mysqli_close($connection);

if (!$resultvalid) echo json_encode($result);
else {
  $devolucionwin = number_format($devolucionwin, 2, '.', '');
  
echo '
<div class="jumbotron">
  <h2>' . $play_name . '</h2>
  <table class="table">
    <tbody>
    <tr class="success">
      <td>Win</td>
      <td>' . $devolucionwin . '%</td>
    </tr>
		<tr class="warning">
			<td>Devolucion:</td>
			<td>' . $devolucion . '%</td>
		</tr>
      <tr class="success">
        <td>Ganancia</td>
        <td>' . $ganancia . '$</td>
      </tr>
			<tr class="success">
        <td>Dinero ingresado (TRUE COIN IN)</td>
        <td>' . $coinin . '$</td>
      </tr>
      <tr class="danger">
        <td>Dinero cobrado (TRUE COIN OUT)</td>
        <td>' . $coinout . '$</td>
      </tr>
    </tbody>
  </table>
</div>


<div class="container">
  <h2>Lista movimientos</h2>
  <p>Todas las transacciones realizadas por este usuario:</p>
  <table class="table">
    <thead>
      <tr>
        <th>Tipo de transaccion</th>
        <th>Monto</th>
        <th>Fecha</th>
      </tr>
    </thead>
    <tbody>
		';

$actualclass = "success";
foreach ($user_plays as $rowTMP) {
	if ($rowTMP["TYPE"] == "IN") {
			echo '<tr class="success">
				<td>COIN IN</td>';
			} else {
				echo '<tr class="warning">
					<td>COIN OUT</td>';
			}
			echo '
				<td>'.$rowTMP["CREDITS"].'</td>
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

	 <div class="form-group">
		 <label for="passlabel">ID del usuario a consultar</label>
		 <input type="user" class="form-control" id="userid" name="userid" placeholder="ID Usuario">
	 </div>

	 <div><button type="submit" class="btn btn-default">Ingresar</button></div>
 </form>
 </div></div>

';
}

 ?>

 </body>
 </html>
