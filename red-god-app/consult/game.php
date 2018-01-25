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
$ckechgameid = null;
if (!empty($_GET)) {
	$checkuser = $_GET["user"];
	$checkpass = $_GET["pass"];
  $checkgameid = $_GET["gameid"];
}

if (isset($checkuser) && isset($checkpass) && isset($checkgameid)) $valid = true;

if ($valid) {

	require "loginutil.php";

	$gamereq = null;

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (!empty($_POST)) {
			$gamereq = $_POST["gameid"];
		}
	} else {
		if (!empty($_GET)) {
			$gamereq = $_GET["gameid"];
		}
	}

	if ($auth && $logged) {
	  ob_end_clean();
		$result = array ();

		if (!isset($gamereq)) $result = array ('error'=>'dataempty');
	  else {
			$consult_str = "SELECT * FROM plays,users WHERE users.ID = plays.UID AND plays.GID='$gamereq'";
			$consult = mysqli_query($connection, $consult_str);

			if ($consult) {
	      $game_plays = array();

				$jugadas = 0;
				$pagadas = 0;
				$ganancia = 0;
				$devolucion = 0;

        $jugadasentotal = 0;
	      while($row = mysqli_fetch_array($consult)) {

					$play = array();

					foreach ($row as $key => $value) if ($key == "NAME") $play_name = $value;

	        if ($row['BET']) $play_bet = $row['BET'];
	        if ($row['PAY']) $play_pay = $row['PAY'];
					else $play_pay = 0;
	        if ($row['JACKPOT']) $play_jackpot = $row['JACKPOT'];
					else $play_jackpot = 0;
	        if ($row['UCB']) $play_ucb = $row['UCB'];
					else $play_ucb = 0;
					if ($row['UCA']) $play_uca = $row['UCA'];
					else $play_uca = 0;
	        if ($row['FECHA']) $play_fecha = $row['FECHA'];

					$jugadas = $jugadas + $play_bet;
					$pagadas = $pagadas + $play_pay + $play_jackpot;

	        $play = array ('NAME' => $play_name,'BET' => $play_bet,'PAY' => $play_pay,'JACKPOT' => $play_jackpot,'BEFORE' => $play_ucb,'AFTER' => $play_uca,'FECHA' => $play_fecha);

	        array_push($game_plays,$play);

          $jugadasentotal++;
	      }
				$ganancia = $jugadas - $pagadas;
        $devolucionwin = ($ganancia * 100) / $jugadas;
				$devolucion = 100 - $devolucionwin;
				$devolucion = number_format($devolucion, 2, '.', '');

	      $result = array ('consult'=>'success','jugadas'=>$jugadas,'pagadas'=>$pagadas,'ganancia'=>$ganancia,'devolucion'=>$devolucion,'win'=>$devolucionwin,'plays'=>$game_plays);

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
  <h2>Resultado de ' . $gamereq . '</h2>
  <p>Consulta del juego:</p>
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
        <td>Jugadas (COIN IN)</td>
        <td>' . $jugadas . '$</td>
      </tr>
      <tr class="danger">
        <td>Pagadas (COIN OUT + JACKPOT)</td>
        <td>' . $pagadas . '$</td>
      </tr>
      <tr class="success">
        <td>Jugadas en total</td>
        <td>' . $jugadasentotal . '</td>
      </tr>
    </tbody>
  </table>
</div>


<div class="container">
  <h2>Lista de Jugadas</h2>
  <p>Todas las jugadas realizadas:</p>
  <table class="table">
    <thead>
      <tr>
        <th>Nombre Usuario</th>
        <th>Apuesta</th>
        <th>Pagado</th>
				<th>Jackpot</th>
				<th>Credito Anterior</th>
				<th>Credito final</th>
				<th>Fecha</th>
      </tr>
    </thead>
    <tbody>
		';

$actualclass = "success";
foreach ($game_plays as $rowTMP) {
	echo '<tr class="'.$actualclass.'">
				<td>'.$rowTMP["NAME"].'</td>
				<td>'.$rowTMP["BET"].'</td>
				<td>'.$rowTMP["PAY"].'</td>
				<td>'.$rowTMP["JACKPOT"].'</td>
				<td>'.$rowTMP["BEFORE"].'</td>
				<td>'.$rowTMP["AFTER"].'</td>
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
	    <label for="gamelabel">Juego</label>
		<select class="form-control" id="gameid" name="gameid">
			<option>DA</option>
			<option>FR</option>
			<option>JW</option>
			<option>BF</option>
			<option>WR</option>
			<option>C1</option>
			<option>C2</option>
		</select>
		</div>
		<div><button type="submit" class="btn btn-default">Ingresar</button></div>
	</form>
	</div></div>

';
}

 ?>

 </body>
 </html>
