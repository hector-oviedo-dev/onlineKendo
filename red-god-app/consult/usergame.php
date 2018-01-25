<!DOCTYPE html>
<html lang="en">
<head>
  <title>Consulta - JUEGO</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body class="container" bgcolor="#cccccc">

<?php

$valid = false;
$resultvalid = false;

$checkuser = null;
$checkpass = null;
$checkuserid = null;
$checkgameid = null;
if (!empty($_GET)) {
	$checkuser = $_GET["user"];
	$checkpass = $_GET["pass"];
  $checkuserid = $_GET["userid"];
	$checkgameid = $_GET["gameid"];
}

if (isset($checkuser) && isset($checkpass) && isset($checkuserid) &&isset($checkgameid) ) $valid = true;

if ($valid) {
require "loginutil.php";

$userid = null;
$gamereq = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (!empty($_POST)) {
    $userid = $_POST["userid"];
		$gamereq = $_POST["gameid"];
	}
} else {
	if (!empty($_GET)) {
    $userid = $_GET["userid"];
		$gamereq = $_GET["gameid"];
	}
}

if ($auth && $logged) {
  ob_end_clean();
	$result = array ();

	if (!isset($userid) || !isset($gamereq)) $result = array ('error'=>'dataempty');
  else {
		//$consult_str = "SELECT * FROM plays WHERE UID='$userid' AND GID='$gamereq'";
    $consult_str = "SELECT * FROM plays WHERE UID='$userid' AND GID='$gamereq' ORDER BY id DESC";
		$consult = mysqli_query($connection, $consult_str);

		if ($consult) {
      $user_plays = array();

			$jugadas = 0;
			$pagadas = 0;
			$ganancia = 0;
			$devolucion = 0;

      $jugadasentotal = 0;
      while($row = mysqli_fetch_array($consult)) {

				$user_play = array();

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
        if ($row['DATA']) $play_data = $row['DATA'];


				$jugadas = $jugadas + $play_bet;
				$pagadas = $pagadas + $play_pay + $play_jackpot;

        $user_play = array ('BET' => $play_bet,'PAY' => $play_pay,'JACKPOT' => $play_jackpot,'BEFORE' => $play_ucb,'AFTER' => $play_uca,'FECHA' => $play_fecha, 'DATA'=> $play_data);

        array_push($user_plays,$user_play);

        $jugadasentotal++;
      }
      $ganancia = $jugadas - $pagadas;
      $devolucionwin = ($ganancia * 100) / $jugadas;
      $devolucion = 100 - $devolucionwin;
      $devolucion = number_format($devolucion, 2, '.', '');

      $result = array ('consult'=>'success','jugadas'=>$jugadas,'pagadas'=>$pagadas,'ganancia'=>$ganancia,'devolucion'=>$devolucion,'win'=>$devolucionwin,'plays'=>$user_plays);
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
	<p>Juego: ' . $gamereq . ' </p>
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
        <td>Jugado (COIN IN)</td>
        <td>' . $jugadas . '$</td>
      </tr>
      <tr class="danger">
        <td>Pagado (COIN OUT + JACKPOT)</td>
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
        <th>Ver Jugada</th>
        <th>Apostado</th>
        <th>Pago</th>
				<th>Pago en Jackpot</th>
				<th>Credito Anterior</th>
				<th>Credito final</th>
				<th>Fecha</th>
      </tr>
    </thead>
    <tbody>
		';

$actualclass = "success";
foreach ($user_plays as $rowTMP) {
	echo '<tr class="'.$actualclass.'"><td>';
  if ($rowTMP["DATA"]) {
    $json = json_decode($rowTMP["DATA"], false);

    $reels = $json->reels;
    $reelsPos = $json->reelsPositions;
    $credits = $json->count->credits;
    $creditsFinal = $json->count->creditsFinal;
    $win = $json->count->win;

    $lines = array();
    if (array_key_exists("prizes", $json)) {
      $prizes = $json->prizes;
      foreach($json->prizes as $prize) {
        if ($prize->type == "line") {
          $line = array("id"=>$prize->lineID);
          array_push($lines,$line);
        }
      }
    }
    echo '
    <form action="playviewer.php" method="post" target="_blank">
    <input type="hidden" name="reels" value='.json_encode($reels).' >
    <input type="hidden" name="reelsPos" value='.json_encode($reelsPos).'>
    <input type="hidden" name="lines" value='.json_encode($lines).'>

    <input type="hidden" name="gameid" value='.$gamereq.'>
    <input type="hidden" name="credits" value='.$credits.'>
    <input type="hidden" name="creditsFinal" value='.$creditsFinal.'>
    <input type="hidden" name="win" value='.$win.'>
    <input type="hidden" name="totalbet" value="'.$rowTMP["BET"].'"/>
    <button type="submit">Visualizar</button>
    </form>';
  }

echo '  </td>
				<td>'.$rowTMP["BET"].'</td>
				<td>'.$rowTMP["PAY"].'</td>
				<td>'.$rowTMP["JACKPOT"].'</td>
				<td>'.$rowTMP["BEFORE"].'</td>
				<td>'.$rowTMP["AFTER"].'</td>
				<td>'.$rowTMP["FECHA"].'</td>
	</tr>';
  if ($json->freespins) $actualclass = "danger";
  else {
	   if ($actualclass == "success") $actualclass = "warning";
	    else $actualclass = "success";
    }
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
 <script type="text/javascript">
 function getData(param) { return JSON.stringify(param); }
 </script>

 </body>
 </html>
