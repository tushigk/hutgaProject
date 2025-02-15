<?php
/**/
function playermove_page_start( $array = array() )
{
	if (isset($_COOKIE['justmadeamove'])) die();
}
$addons->add_hook(array(
	'page'     => 'includes/player_move.php',
	'location' => 'page_start',
	'function' => 'playermove_page_start',
));


/**/
function playermove_tpq_sql( $array = array() )
{
	global $gameID;
	return "SELECT p1name, p2name, p3name, p4name, p5name, p6name, p7name, p8name, p9name, p10name, p1pot, p2pot, p3pot, p4pot, p5pot, p6pot, p7pot, p8pot, p9pot, p10pot, p1bet, p2bet, p3bet, p4bet, p5bet, p6bet, p7bet, p8bet, p9bet, p10bet, hand, move, pot, bet, tablelimit, lastbet, dealer, botgod, lastmove FROM " . DB_POKER . " WHERE gameID = " . $gameID;
}
$addons->add_hook(array(
	'page'     => 'includes/player_move.php',
	'location' => 'tpq_sql',
	'function' => 'playermove_tpq_sql',
));


/**/
function playermove_start_sql1( $array = array() )
{
	global $msg, $player, $gameID;
	return "UPDATE " . DB_POKER . " SET hand = '0', msg = '$msg', move = '$player', dealer = '$player', botgod = '$player' WHERE gameID = $gameID AND hand = ''";
}
$addons->add_hook(array(
	'page'     => 'includes/player_move.php',
	'location' => 'start_sql1',
	'function' => 'playermove_start_sql1',
));


/**/
function playermove_action_begin( $array = array() )
{
	if (isset($_GET['bot']))
	{
		global $tpr, $tomove, $player, $plyrname, $pdo;

	    $rhnn = false;
	    if ($tpr['botgod'] != '' && $tomove != '' && $tomove != $player)
	    {
	        $botgod = $tpr['botgod'];

	        if ($botgod == $player)
	        {
	            $GLOBALS['player'] = $tomove;
	            $GLOBALS['plyrname'] = $tpr['p' . $tomove . 'name'];
	            $GLOBALS['playerpot'] = $tpr['p' . $tomove . 'pot'];
	            $GLOBALS['playerbet'] = $tpr['p' . $tomove . 'bet'];

	            if ($GLOBALS['plyrname'] != '')
	            {
	                $tmq = $pdo->prepare("SELECT isbot FROM " . DB_PLAYERS . " WHERE username = '" . $GLOBALS['plyrname'] . "'");
	                $tmq->execute();
	                $tmr = $tmq->fetch(PDO::FETCH_ASSOC);

	                if ($tmr['isbot'] == 1)
	                {
	                    $rhnn = true;
	                }
	            }
	        }
	    }

	    if (!$rhnn) die();
	}
}
$addons->add_hook(array(
	'page'     => 'includes/player_move.php',
	'location' => 'beginning_of_action',
	'function' => 'playermove_action_begin',
));


/**/
function playermove_everything_okay_logic( $array = array() )
{
	global $hand, $numplayers, $isbet;
	$state = ($hand > 4 && $hand < 12 && $numplayers > 1 && $isbet == true) ? true : false;
	return $state;
}
$addons->add_hook(array(
	'page'     => 'includes/player_move.php',
	'location' => 'everything_okay_logic',
	'function' => 'playermove_everything_okay_logic',
));


/**/
function playermove_after_move( $array = array() )
{
	/*if (! isset($_GET['bot']))
	{*/
		$secs = array(3); // array(2, 3, 4, 5, 6, 7);
	    $sec  = $secs[mt_rand(0, (count($secs) - 1))];
	    setcookie('justmadeamove', 'true', (time() + $sec));
	//}
}
$addons->add_hook(array(
	'page'     => 'includes/player_move.php',
	'location' => 'after_move',
	'function' => 'playermove_after_move',
));