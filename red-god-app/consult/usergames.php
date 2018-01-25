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
		$consult_str = "SELECT * FROM plays WHERE UID='$userid'";
		$consult = mysqli_query($connection, $consult_str);

		if ($consult) {
      $user_plays = array();

			$jugadas = 0;
			$pagadas = 0;
			$ganancia = 0;
			$devolucion = 0;

      while($row = mysqli_fetch_array($consult)) {

				$user_play = array();

				if ($row['GID']) $play_game = $row['GID'];
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

        $user_play = array ('GAME' => $play_game,'BET' => $play_bet,'PAY' => $play_pay,'JACKPOT' => $play_jackpot,'BEFORE' => $play_ucb,'AFTER' => $play_uca,'FECHA' => $play_fecha);

        array_push($user_plays,$user_play);
      }
			$ganancia = $jugadas - $pagadas;
			$devolucion = 100- (($ganancia *100) / $jugadas);
			$devolucion = number_format($devolucion, 2, '.', '');

      $result = array ('consult'=>'success','jugadas'=>$jugadas,'pagadas'=>$pagadas,'ganancia'=>$ganancia,'devolucion'=>$devolucion,'plays'=>$user_plays);
    } else $result = array ('error'=>'noresult');
	}
} else $result = array ('error'=>'error');

mysqli_close($connection);
echo json_encode($result);

 ?>
