<?php
/**/
function automove_tpr_variables ( $array = array() )
{
	global $plyrname, $tpr, $autoplayer, $dealer, $pdo;

	$GLOBALS['player']    = getplayerid($plyrname);
    $GLOBALS['botgod']    = $tpr['botgod'];
    $GLOBALS['botplayer'] = false;
    $GLOBALS['botdealer'] = false;
    
    $autoplayername       = $tpr['p' . $autoplayer . 'name'];

    if (! empty($GLOBALS['botgod']))
    {
        if ($GLOBALS['botgod'] == $GLOBALS['player'])
        {
            $tmplayer = $autoplayer;
            $tmplyrname = $autoplayername;

            if ($tmplyrname != '')
            {
                $tmq = $pdo->prepare("SELECT isbot FROM " . DB_PLAYERS . " WHERE username = '$tmplyrname'");
                $tmq->execute();
                $tmr = $tmq->fetch(PDO::FETCH_ASSOC);

                if ($tmr['isbot'] == 1)
                {
                    $GLOBALS['botplayer'] = true;
                }
            }

            $dlplayer   = $dealer;
            $dlplyrname = $tpr['p' . $dlplayer . 'name'];

            if ($dlplyrname != '')
            {
                $dlq = $pdo->prepare("SELECT isbot FROM " . DB_PLAYERS . " WHERE username = '$dlplyrname'");
                $dlq->execute();
                $dlr = $dlq->fetch(PDO::FETCH_ASSOC);

                if ($dlr['isbot'] == 1)
                {
                    $GLOBALS['botdealer'] = true;
                }
            }
        }
    }
    
    if ($autoplayername == $plyrname && !isbotplayer($autoplayername))
    {
        setcookie('justmadeamove', 'false', (time() - 1));
    }
}
$addons->add_hook(array(

	'page'     => 'includes/auto_move.php',
	'location' => 'tpr_variables',
	'function' => 'automove_tpr_variables',

));



/**/
function automove_ttq_sql ( $array = array() )
{
	global $usr;
	return "SELECT gID, timetag, lastmove, banned, isbot FROM " . DB_PLAYERS . " WHERE username = '$usr'";
}
$addons->add_hook(array(

	'page'     => 'includes/auto_move.php',
	'location' => 'ttq_sql',
	'function' => 'automove_ttq_sql',

));



/**/
function automove_after_opponent_timer_js( $array = array() )
{
	global $botgod, $player, $autoplayer, $pdo, $tpr, $gameID, $autoname, $tablebet, $addons;

    if ($botgod != '' && $botgod == $player && $autoname != '')
    {
        $mvq = $pdo->prepare("SELECT isbot FROM " . DB_PLAYERS . " WHERE username = '$autoname'");
        $mvq->execute();
        $mvr = $mvq->fetch(PDO::FETCH_ASSOC);

        if ($mvr['isbot'] == 1)
        {
            $botplayerbet  = get_bet_math($autoplayer);
            $botplayerpot  = get_pot($autoplayer);

            $botactions = array('raise');
            if ($tablebet > $botplayerbet) $botactions[] = 'fold';

            if ($tablebet > $botplayerbet && $botplayerpot >= ($tablebet - $botplayerbet))
                $botactions = array_merge($botactions, array('call', 'call', 'call'));
            elseif ($botplayerpot >= ($tablebet - $botplayerbet))
                $botactions = array_merge($botactions, array('check', 'check', 'check'));

            $randaction = $botactions[mt_rand(0, (count($botactions) - 1))];

            if ($randaction == 'raise')
            {
                $botactions = array(1, 1, 1, 2, 2, 4);

                global $BB;
                $autoplayerbet  = get_bet_math($autoplayer);
                $autowinpot     = get_pot($autoplayer);

                $initialRaise = $BB;
                if ($tablebet > $autoplayerbet && $autowinpot >= ($tablebet - $autoplayerbet))
                {
                    $initialRaise = ($tablebet - $autoplayerbet) * 2;
                }

                $randaction = $initialRaise * $botactions[mt_rand(0, (count($botactions) - 1))];
            }

            $randaction = $addons->get_hooks(
                array(
                    'content' => $randaction
                ),
                array(
                    'page'     => 'addons/bots',
                    'location'  => 'bot_action'
                )
            );

            return 'var cname = "justmadeamove=";
var decodedCookie = decodeURIComponent(document.cookie);
var ca = decodedCookie.split(";");
var cvalue = "";
for(var i = 0; i < ca.length; i++)
{
    var c = ca[i];
    while (c.charAt(0) == " ")
    {
        c = c.substring(1);
    }
    if (c.indexOf(cname) == 0)
    {
        cvalue = c.substring(cname.length, c.length);
    }
}

if (cvalue == "")
{
    var rurl = document.location.href;
    var rxend = rurl.lastIndexOf("/") + 1;
    var rbase_url = rurl.substring(0, rxend);
    $.get(rbase_url + "includes/player_move.php?action=' . $randaction . '&bot=1", {});
}';
        }
    }
}
$addons->add_hook(array(

	'page'     => 'includes/auto_move.php',
	'location' => 'after_opponent_timer_js',
	'function' => 'automove_after_opponent_timer_js',

));



/**/
function automove_hand0_sql ( $array = array() )
{
	global $dealer, $msg, $time, $nxtdeal, $gameID;

	$nxtbotgod = nextbotgod($dealer);
	$bgsql = '';
	
	if ($nxtbotgod !== false)
	{
	    $bgsql = ", botgod = '" . $nxtbotgod . "'";
	}
	
    return "UPDATE " . DB_POKER . " SET msg = '" . $msg . "', lastmove = " . ($time + 1) . " , dealer = '" . $nxtdeal . "' $bgsql, move = '" . $nxtdeal . "', bet = 0, lastbet = 0, pot = 0, p1bet = '0', p2bet = '0', p3bet = '0', p4bet = '0', p5bet = '0', p6bet = '0', p7bet = '0', p8bet = '0', p9bet = '0', p10bet = '0', hand = '1'  WHERE gameID = " . $gameID . " and hand = '0'";
}
$addons->add_hook(array(

	'page'     => 'includes/auto_move.php',
	'location' => 'hand0_sql',
	'function' => 'automove_hand0_sql',

));



/**/
function automove_hand4_logic ( $array = array() )
{
	global $hand, $lastmove, $time, $autotimer, $dealer, $player, $botdealer;
	
	$state = ($hand == 4 && $lastmove < ($time - $autotimer) && ($dealer == $player || $botdealer)) ? true : false;
	
	return $state;
}
$addons->add_hook(array(

	'page'     => 'includes/auto_move.php',
	'location' => 'hand4_logic',
	'function' => 'automove_hand4_logic',

));



/**/
function automove_hand5_7_9_11_logic ( $array = array() )
{
	global $autoname, $autoplayer, $checkbets, $gamestatus, $hand, $lastbet;
	
	$state = $array['content'];
	
	$state = (($autoplayer == last_bet() || ($lastbet == 0 && isbotplayer($autoname))) && $checkbets == true && $gamestatus == 'live' && ($hand == 5 || $hand == 7 || $hand == 9 || $hand == 11)) ? true : false;
	
	return $state;
}
$addons->add_hook(array(

	'page'     => 'includes/auto_move.php',
	'location' => 'hand5_7_9_11_logic',
	'function' => 'automove_hand5_7_9_11_logic',

));



/**/
function automove_hand15_logic ( $array = array() )
{
	global $hand, $autoplayer, $player, $botplayer;
	$state = ($hand == 15 && ($autoplayer == $player || $botplayer)) ? true : false;
	return $state;
}
$addons->add_hook(array(

	'page'     => 'includes/auto_move.php',
	'location' => 'hand15_logic',
	'function' => 'automove_hand15_logic',

));



/**/
function automove_result3_sql ( $array = array() )
{
	global $usr, $ttr;

	if ($ttr['isbot'] == 0) return "UPDATE " . DB_PLAYERS . " SET gID = 0 WHERE username = '$usr'";

	return false;
}
$addons->add_hook(array(

	'page'     => 'includes/auto_move.php',
	'location' => 'result3_sql',
	'function' => 'automove_result3_sql',

));



/**/
function automove_player_tkick ( $array = array() )
{
	global $ttr, $gameID;

	if ($ttr['isbot'] == 1)
	{
		if ($ttr['gID'] != $gameID)
			return true;
		else
			return false;
	}

	return $array['state'];
}
$addons->add_hook(array(

	'page'     => 'includes/auto_move.php',
	'location' => 'player_tkick',
	'function' => 'automove_player_tkick',

));