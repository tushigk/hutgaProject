<?php

/**/
function incpoker_tq_sql ( $array = array() )
{
	global $gameID;
	return "SELECT * FROM " . DB_POKER . " WHERE gameID = " . $gameID;
}
$addons->add_hook(array(
	'page'     => 'includes/inc_poker.php',
	'location' => 'tq_sql',
	'function' => 'incpoker_tq_sql',
));


/**/
function incpoker_tpq_sql ( $array = array() )
{
    global $gameID;
    return "SELECT p1name, p2name, p3name, p4name, p5name, p6name, p7name, p8name, p9name, p10name, p1pot, p2pot, p3pot, p4pot, p5pot, p6pot, p7pot, p8pot, p9pot, p10pot, p1bet, p2bet, p3bet, p4bet, p5bet, p6bet, p7bet, p8bet, p9bet, p10bet, hand, move, pot, bet, tablelimit, lastbet, dealer, botgod, lastmove FROM " . DB_POKER . " WHERE gameID = " . $gameID;
}
$addons->add_hook(array(
    'page'     => 'includes/inc_poker.php',
    'location' => 'tpq_sql',
    'function' => 'incpoker_tpq_sql',
));


/**/
function incpoker_change_botgod ( $array = array() )
{
    global $player, $gameID, $pdo;
    $nxtbp = nextbotgod($player);
    
    if ($nxtbp !== false)
    {
        $pdo->exec("UPDATE " . DB_POKER . " SET botgod = '" . $nxtbp . "' WHERE gameID = " . $gameID);
    }
    else
    {
        $set = array();
        for ($i = 1; $i < 11; $i++)
        {
            $set[] = "p{$i}bet = 'F'";
        }
        $setstring = implode(',', $set);
        
        $pdo->exec("UPDATE " . DB_POKER . " SET move = '', dealer = '', botgod = '', hand = '', pot = '0', bet = '0', lastbet = '', $setstring WHERE gameID = " . $gameID);
    }
}
$addons->add_hook(array(
    'page'     => 'includes/inc_poker.php',
    'location' => 'if_player_exists',
    'function' => 'incpoker_change_botgod',
));
