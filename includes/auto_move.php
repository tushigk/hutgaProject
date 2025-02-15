<?php
require('sec_inc.php');

header('Content-Type: text/javascript');
echo $addons->get_hooks(
    array(),
    array(
        'page'     => 'includes/auto_move.php',
        'location'  => 'page_start'
    )
);

$time   = time() - 1;
$result = $pdo->exec("update " . DB_PLAYERS . " set timetag = " . ($time + 1) . " where username = '$plyrname' and gID = " . $gameID);

$cq = $pdo->query("SELECT * FROM " . DB_LIVECHAT . " WHERE gameID = " . $gameID);
$cr = $cq->fetch(PDO::FETCH_ASSOC);

$lastLog = '';

if ($cr['updatescreen'] > $time)
{
    $i    = 1;
    $chat = '';

    while ($i < 6)
    {
        $cThis = $cr['c' . $i];

        if (empty($cThis))
        {
            $i++;
            continue;
        }

        $lMsg = "<log>{$cThis}</log>";
        $lXml = simplexml_load_string($lMsg);

        $opsTheme->addVariable('chatter', array(
            'id'     => (int) $lXml->user->id,
            'name'   => (string) $lXml->user->name,
            'avatar' => (string) $lXml->user->avatar,
        ));
        $opsTheme->addVariable('message', (string) $lXml->message);
        $lastLog = $opsTheme->viewPart('poker-log-message');

        $chat .= $lastLog;
        $i++;
    }
?>
var chatxt = '<?php echo $chat; ?>';
document.getElementById('chatdiv').innerHTML = chatxt;

<?php if (!empty($cThis)) { ?>
if (typeof(document.getElementById('tablelog')) != 'undefined')
{
    document.getElementById('tablelog').innerHTML = '<?php echo $lastLog; ?>';
}
<?php
    }
}

$ucq = $pdo->query("SELECT * FROM " . DB_USERCHAT . " WHERE gameID = {$gameID}");
$ucr = $ucq->fetch(PDO::FETCH_ASSOC);

if ($ucr['updatescreen'] > $time)
{
    $i    = 1;
    $chat = '';

    while ($i < 6)
    {
        $cThis = $ucr['c' . $i];

        if (empty($cThis))
        {
            $i++;
            continue;
        }

        $uMsg = "<chat>{$cThis}</chat>";
        $uXml = simplexml_load_string($uMsg);

        $opsTheme->addVariable('chatter', array(
            'id'     => (int) $uXml->user->id,
            'name'   => (string) $uXml->user->name,
            'avatar' => (string) $uXml->user->avatar,
        ));
        $opsTheme->addVariable('message', (string) $uXml->message);

        if ($uXml->user->name == $plyrname)
            $chat .= $opsTheme->viewPart('poker-chat-message-me');
        else
            $chat .= $opsTheme->viewPart('poker-chat-message-other');

        $i++;
    }

    //$chat .= '</div>';
?>
var userchatxt = '<?php echo $chat; ?>';
document.getElementById('userchatdiv').innerHTML = userchatxt;
<?php
}
$tpq        = $pdo->query("SELECT * FROM " . DB_POKER . " WHERE gameID = $gameID");
$tpr        = $tpq->fetch(PDO::FETCH_ASSOC);
$hand       = $tpr['hand'];
$autoplayer = $tpr['move'];
$autotimer  = 0;
$movetimer  = MOVETIMER;
$lmovetimer = KICKTIMER;
$distimer   = DISCONNECT;
$kick       = $time - ($lmovetimer * 60);
$timekick   = $time - $distimer;
$lastmove   = $tpr['lastmove'];
$diff       = $time - $lastmove;
$dealer     = $tpr['dealer'];
$tablepot   = $tpr['pot'];
$tabletype  = $tpr['tabletype'];
$tablelimit = $tpr['tablelimit'];
$sbamount	= $tpr['sbamount'];
$bbamount	= $tpr['bbamount'];
$tablebet   = $tpr['bet'];
$lastbet    = $tpr['lastbet'];
$game_style = '';

$opsTheme->addVariable('movetimer', $movetimer);

if ($tpr['gamestyle'] == null) {
    $game_style = GAME_TEXAS;
} else {
    if ($tpr['gamestyle'] == 't') {
        $game_style = GAME_TEXAS;
    } else {
        $game_style = GAME_OMAHA;
    }
}

echo $addons->get_hooks(
    array(),
    array(
        'page'     => 'includes/auto_move.php',
        'location' => 'tpr_variables'
    )
);

$showshort  = false;

if ($autoplayer != '')
{
    $nextup = nextplayer($autoplayer);

    if ($nextup == $autoplayer)
    {
        $end = true;
    }

    $autoname   = $tpr['p' . $autoplayer . 'name'];
    $autopot    = $tpr['p' . $autoplayer . 'pot'];
    $autobet    = $tpr['p' . $autoplayer . 'bet'];
    $autofold   = substr($autobet, 0, 1);
    $autostatus = 'live';

    if ($autopot == 0 && $autobet > 0 && ($hand > 4 && $hand < 12))
    {
        $autostatus = 'allin';
    }

    if ($autofold == 'F' && ($hand > 4 && $hand < 12))
    {
        $autostatus = 'fold';
    }

    if ($autopot == 0 && ($autobet == 0 || $autostatus == 'fold'))
    {
        $autostatus = 'bust';
    }

    $i  = 1;
    $np = 0;
    $ai = 0;
    $fo = 0;
    $bu = 0;

    while ($i < 11)
    {
        $usr   = $tpr['p' . $i . 'name'];
        $upot  = $tpr['p' . $i . 'pot'];
        $ubet  = $tpr['p' . $i . 'bet'];
        $ufold = substr($ubet, 0, 1);

        if ($usr != '')
        {
            $np++;

            if ($upot == 0 && $ubet > 0 && $ufold != 'F' && ($hand > 4 && $hand < 15))
            {
                $ai++;
            }

            if ($ufold == 'F' && $upot > 0 && ($hand > 4 && $hand < 15))
            {
                $fo++;
            }

            if (($ubet == 0 || $ufold == 'F') && $upot == 0)
            {
                $bu++;
            }

            $ttq_sql = $addons->get_hooks(
                array(
                    'content' => "SELECT gID, timetag, lastmove, banned FROM " . DB_PLAYERS . " WHERE username = '$usr'"
                ),
                array(
                    'page'     => 'includes/auto_move.php',
                    'location'  => 'ttq_sql'
                )
            );
            $ttq   = $pdo->prepare($ttq_sql);
            $ttq->execute();
            $tkick = '';
            $ttr   = $ttq->fetch(PDO::FETCH_ASSOC);

            if ($ttr['timetag'] < $timekick || ($ttr['lastmove'] < $kick && $hand > 5) || ($ttr['banned'] == 1) || ($ttr['gID'] != $gameID))
            {
                $tkick = true;
            }

            $tkick = $addons->get_hooks(
                array(
                    'state' => $tkick,
                    'content' => $tkick,
                ),
                array(
                    'page'     => 'includes/auto_move.php',
                    'location'  => 'player_tkick'
                )
            );

            if (($upot == 0 && ($ubet == 0 || $ufold == 'F')) || $tkick == true)
            {
                $result = $pdo->exec("update " . DB_POKER . " set p" . $i . "name = '', p" . $i . "bet = '', p" . $i . "pot = '' , lastmove = " . ($time + 1) . " where gameID = " . $gameID);

                $result = $pdo->exec("update " . DB_STATS . " set winpot = winpot + $upot where player  = '$usr'");

                $result = $pdo->exec("update " . DB_PLAYERS . " set gID = 0 where username = '$usr'");

                if ($tkick == true)
                {
                    poker_log($usr, GAME_MSG_LOST_CONNECTION, $gameID);

                    if ($i == $dealer)
                    {
                        $nxtdeal = nextdealer($dealer);

                        if ($nxtdeal != '')
                        {
                            poker_log( get_name($nxtdeal), GAME_MSG_DEALER_BUTTON, $gameID );
                            $pdo->exec("UPDATE " . DB_POKER . " SET lastmove = " . ($time + 1) . " , dealer = '" . $nxtdeal . "' WHERE gameID = " . $gameID);
                        }
                    }

                    if ($_SESSION['playername'] == $usr)
                    {
                        $wait    = (int) WAITIMER;
                        $setwait = $time + $wait;
                        $result  = $pdo->exec("update " . DB_PLAYERS . " set waitimer = $setwait where username = '$plyrname'");
                        $url     = 'sitout.php';

                        echo 'parent.document.location.href = "' . $url . '";';
                    }
                }
                else
                {
                    poker_log($usr, GAME_MSG_PLAYER_BUSTED, $gameID);
                    echo $addons->get_hooks(
                        array(
                            'index' => $i
                        ),
                        array(
                            'page'     => 'includes/auto_move.php',
                            'location'  => 'after_player_busted'
                        )
                    );
                }
            }
        }

        $i++;
    }

    $checkbets = check_bets();
    $showsort  = false;
    $lastman   = '';
    $allpl     = $np - $bu;

    if ($allpl == 1)
    {
        $lastman = last_player();
    }

    $nfpl       = $allpl - $fo;
    $lipl       = $nfpl - $ai;
    $gamestatus = 'live';

    if ($hand > 4 && $hand < 12)
    {
        if ($nfpl == 1 && $allpl > 1)
        {
            $gamestatus = 'allfold';
        }

        if ($lipl < 2 && $allpl > 1 && $checkbets == true && $ai > 0)
        {
            $gamestatus = 'showdown';
        }
    }
    else
    {
        if ($nfpl == 1 && $allpl > 1)
        {
            $showshort = true;
        }
    }

    if ($allpl == 1 && ($hand != '' || $move != ''))
    {
        $winpot = $tpr['p' . $lastman . 'pot'] + $tablepot;
        $hhname = '';

        if ($tabletype == 't' && $allpl == 1)
        {
            if ($autoname != '')
            {
                $hhname = $autoname;
                $msg    = GAME_MSG_WON_TOURNAMENT;
            }
            else
            {
                $msg = GAME_MSG_PLAYERS_JOINING;
            }

            $result = $pdo->exec("update " . DB_STATS . " set tournamentswon = tournamentswon + 1 where player  = '" . $autoname . "' ");
        }
        else
        {
            $msg = GAME_MSG_PLAYERS_JOINING;
        }

        $result = $pdo->exec("update " . DB_POKER . " set p" . $lastman . "bet = '0', p" . $lastman . "pot = '" . $winpot . "', move = '', lastmove = " . ($time + 1) . ", dealer = '', msg = '" . $msg . "', hand = '' , bet = 0, pot = 0 where gameID = " . $gameID);
        $addons->get_hooks(
            array(
                'content' => $hand
            ),
            array(
                'page'     => 'includes/auto_move.php',
                'location'  => 'hand_change'
            )
        );
        poker_log($hhname, $msg, $gameID);
        die();
    }

    if ($autoname == '' || $autostatus == 'bust' && $allpl > 1)
    {
        $result = $pdo->exec("update " . DB_POKER . " set move = '" . $nextup . "' , lastmove = " . ($time + 1) . " where gameID = " . $gameID);
        die();
    }
}


$blindmultiplier = (11 - $allpl);

if ($tabletype != 't')
{
    $blindmultiplier = 4;
}

$tablemultiplier = 1;

if ($tablelimit == 25000)
{
    $tablemultiplier = 2;
}

if ($tablelimit == 50000)
{
    $tablemultiplier = 4;
}

if ($tablelimit == 100000)
{
    $tablemultiplier = 8;
}

if ($tablelimit == 250000)
{
    $tablemultiplier = 20;
}

if ($tablelimit == 500000)
{
    $tablemultiplier = 40;
}

if ($tablelimit == 1000000)
{
    $tablemultiplier = 80;
}

if ($sbamount != 0) {    
$SB = $sbamount;
$BB = $bbamount;
} else {
$SB = 25 * $blindmultiplier * $tablemultiplier;
$BB = 50 * $blindmultiplier * $tablemultiplier;
}


if ((!isset($allpl) || $allpl == 0) && ($hand != '' || (isset($move) && $move != '')))
{
    $msg    = GAME_MSG_PLAYERS_JOINING;
    $result = $pdo->exec("update " . DB_POKER . " set move = '', lastmove = " . ($time + 1) . ", dealer = '', msg = '" . $msg . "', hand = '' , bet = 0, pot = 0  where gameID = " . $gameID);
    $result = $pdo->exec("delete from " . DB_LIVECHAT . " where gameID = " . $gameID);
    $addons->get_hooks(
        array(
            'content' => $hand
        ),
        array(
            'page'     => 'includes/auto_move.php',
            'location'  => 'hand_change'
        )
    );
    die();
}

if ($hand > 4 && $hand < 12)
{
    $unixts  = time();

    $counter = (($lastmove + $movetimer) - $unixts);

    if ($counter < 0)
        $counter = 0;

    $pxlength = $counter * 3;

    $opsTheme->addVariable('timer', array(
        'total' => $movetimer,
        'left'  => $counter
    ));

    $i = 1;
    while ($i < 11)
    {
        $opsTheme->addVariable('seat_number', $i);

        if ($i == $autoplayer)
        {
            echo $opsTheme->viewPart('poker-player-timer-start-js');
        }
        else
        {
            echo $opsTheme->viewPart('poker-player-timer-stop-js');
        }

        $i++;
    }

    echo $addons->get_hooks(array(), array(

        'page'     => 'includes/auto_move.php',
        'location'  => 'after_opponent_timer_js'

    ));
}

if ($hand < 4 || $hand > 11)
{
    for ($i = 1; $i < 11; $i++)
    {
        $opsTheme->addVariable('seat_number', $i);
        echo $opsTheme->viewPart('poker-player-timer-stop-js');
    }
}

if ($gID == '' || $gID == 0)
{
    die();
}

if ($hand == '')
{
    die();
}

if (!($autoplayer > 0))
{
    die();
}

$showdowntimer = ($showshort == true) ? 1 : SHOWDOWN;

if ($hand == 0 && $lastmove < $time)
{
    $nxtdeal = nextdealer($dealer);

    if ($nxtdeal == '')
    {
        die();
    }

    $msg        = get_name($nxtdeal) . ' ' . GAME_MSG_DEALER_BUTTON;
    poker_log( get_name($nxtdeal), GAME_MSG_DEALER_BUTTON, $gameID );

    $result_sql = $addons->get_hooks(
        array(
            'content' => "update " . DB_POKER . " set msg = '" . $msg . "', lastmove = " . ($time + 1) . " , dealer = '" . $nxtdeal . "', move = '" . $nxtdeal . "', bet = 0, lastbet = 0, pot = 0, p1bet = '0', p2bet = '0', p3bet = '0', p4bet = '0',p5bet = '0', p6bet = '0', p7bet = '0', p8bet = '0', p9bet = '0', p10bet = '0', hand = '1'  where gameID = " . $gameID . " and hand = '0'"
        ),
        array(
            'page'     => 'includes/auto_move.php',
            'location'  => 'hand0_sql'
    ));
    $result     = $pdo->exec($result_sql);
    $addons->get_hooks(
        array(
            'content' => $hand
        ),
        array(
            'page'     => 'includes/auto_move.php',
            'location'  => 'hand_change'
        )
    );
    die();
}

$checkround = false;
$hand5_7_9_11_logic = ($autoplayer == last_bet() && $checkbets == true && $gamestatus == 'live' && ($hand == 5 || $hand == 7 || $hand == 9 || $hand == 11)) ? true : false;
$hand5_7_9_11_logic = $addons->get_hooks(
    array(
        'content' => $hand5_7_9_11_logic
    ),
    array(
        'page'     => 'includes/auto_move.php',
        'location'  => 'hand5_7_9_11_logic'
));
if ($hand5_7_9_11_logic)
{
    $nextup = nextplayer($dealer);
    $lbet   = $nextup . '|' . $tablebet;

    if ($hand == 5)
    {
        $msg    = GAME_MSG_DEAL_FLOP;
        $result = $pdo->exec("update " . DB_POKER . " set msg = '" . $msg . "', lastbet = '" . $lbet . "', move = '" . $nextup . "', hand = '6' , lastmove = '" . ($time + 1) . "' where gameID = '" . $gameID . "' ");
    }
    elseif ($hand == 7)
    {
        $msg    = GAME_MSG_DEAL_TURN;
        $result = $pdo->exec("update " . DB_POKER . " set msg = '" . $msg . "', lastbet = '" . $lbet . "', move = '" . $nextup . "', hand = '8' , lastmove = " . ($time + 1) . "  where gameID = " . $gameID);
    }
    elseif ($hand == 9)
    {
        $msg    = GAME_MSG_DEAL_RIVER;
        $result = $pdo->exec("update " . DB_POKER . " set msg = '" . $msg . "', lastbet = '" . $lbet . "', move = '" . $nextup . "', hand = '10' , lastmove = " . ($time + 1) . " where gameID = " . $gameID);
    }
    elseif ($hand == 11)
    {
        $msg    = GAME_MSG_SHOWDOWN;
        $result = $pdo->exec("update " . DB_POKER . " set msg = '" . $msg . "', lastbet = '" . $lbet . "', move = '" . $nextup . "', hand = '13' , lastmove = " . ($time + 1) . "  where gameID = " . $gameID);
    }
    $addons->get_hooks(
        array(
            'content' => $hand
        ),
        array(
            'page'     => 'includes/auto_move.php',
            'location'  => 'hand_change'
        )
    );
    poker_log('', $msg, $gameID);
    die();
}
else
{
    if ($diff < $autotimer && $hand < 14 && $hand != 0)
    {
        die();
    }

    if ($hand > 4 && $hand < 12 && $diff < $movetimer && $gamestatus == 'live' && $autostatus == 'live')
    {
        die();
    }

    if ($hand == 14 && $diff < $showdowntimer)
    {
        die();
    }
}

if ($gamestatus == 'allfold')
{
    $msg    = $autoname . ' ' . GAME_MSG_ALLFOLD;
    $result = $pdo->exec("update " . DB_POKER . " set msg = '" . $msg . "', hand = '14', lastmove = " . ($time + 1) . "  where gameID = " . $gameID . " and hand = '" . $hand . "'");
    $addons->get_hooks(
        array(
            'content' => $hand
        ),
        array(
            'page'     => 'includes/auto_move.php',
            'location'  => 'hand_change'
        )
    );
    poker_log($autoname, GAME_MSG_ALLFOLD, $gameID);
    die();
}

if ($gamestatus == 'showdown' && ($checkbets == true || $lipl == 0))
{
    $msg    = GAME_MSG_SHOWDOWN;
    $result = $pdo->exec("update " . DB_POKER . " set msg = '" . $msg . "', hand = '13' , lastmove = " . ($time + 1) . " where gameID = " . $gameID . " and hand = '" . $hand . "'");
    $addons->get_hooks(
        array(
            'content' => $hand
        ),
        array(
            'page'     => 'includes/auto_move.php',
            'location'  => 'hand_change'
        )
    );
    die();
}

if ($autostatus == 'allin')
{
    $msg    = $autoname . ' ' . GAME_MSG_PLAYER_ALLIN;
    $result = $pdo->exec("update " . DB_POKER . " set msg = '" . $msg . "', move = '" . $nextup . "' , lastmove = " . ($time + 1) . " where gameID = " . $gameID);
    poker_log($autoname, GAME_MSG_PLAYER_ALLIN, $gameID);
    die();
}

if ($hand == 1 && $lastmove < $time)
{
    $i   = 1;
    $z   = 0;
    $err = true;

    while ($i < 11)
    {
        $pl    = $tpr['p' . $i . 'name'];
        $chips = $tpr['p' . $i . 'pot'];

        if ($pl != '')
        {
            if ($chips > $z)
            {
                $z          = $chips;
                $chipleader = $pl;
                $err        = false;
            }
            elseif ($chips == $z)
            {
                $err = true;
            }

            $result = $pdo->exec("update " . DB_STATS . " set handsplayed = handsplayed + 1 where player  = '" . $pl . "' ");
        }

        $i++;
    }

    if ($err == true)
        poker_log('', GAME_MSG_LETS_GO, $gameID);
    else
        poker_log($chipleader, GAME_MSG_CHIP_LEADER, $gameID);

    $result = $pdo->exec("update " . DB_POKER . " set msg = '" . $msg . "', move = '" . $nextup . "', hand = '2' , lastmove = " . ($time + 1) . " where gameID = " . $gameID . " and hand = '1' ");
    $addons->get_hooks(
        array(
            'content' => $hand
        ),
        array(
            'page'     => 'includes/auto_move.php',
            'location'  => 'hand_change'
        )
    );
    die();
}

if ($hand == 2 && $lastmove < $time)
{
    if ($autopot > $SB)
    {
        $msg  = $autoname . ' ' . GAME_MSG_SMALL_BLIND . ' ' . money_small($SB);
        $npot = $autopot - $SB;
        $nbet = $SB;
        poker_log($autoname, GAME_MSG_SMALL_BLIND . ' ' . money_small($SB), $gameID);
    }
    else
    {
        $msg  = $autoname . ' ' . GAME_PLAYER_GOES_ALLIN;
        $npot = 0;
        $nbet = $autopot;
        poker_log($autoname, GAME_PLAYER_GOES_ALLIN, $gameID);
    }

    $result = $pdo->exec("UPDATE " . DB_POKER . " SET pot = " . $nbet . ", msg = '" . $msg . "', move = '" . $nextup . "', p" . $autoplayer . "pot = '" . $npot . "', p" . $autoplayer . "bet = '" . $nbet . "', hand = '3' , lastmove = " . ($time + 1) . " WHERE gameID = " . $gameID . " and hand = '2'  ");
    $addons->get_hooks(
        array(
            'content' => $hand
        ),
        array(
            'page'     => 'includes/auto_move.php',
            'location'  => 'hand_change'
        )
    );
    die();
}

if ($hand == 3 && $lastmove < $time)
{
    if ($autopot > $BB)
    {
        $msg   = $autoname . ' ' . GAME_MSG_BIG_BLIND . ' ' . money_small($BB);
        $npot  = $autopot - $BB;
        $nbet  = $BB;
        $lbet  = $autoplayer . '|' . $BB;
        $ntpot = $tablepot + $nbet;
        poker_log($autoname, GAME_MSG_BIG_BLIND . ' ' . money_small($BB), $gameID);
    }
    else
    {
        $msg   = $autoname . ' ' . GAME_PLAYER_GOES_ALLIN;
        $npot  = 0;
        $nbet  = $BB;
        $lbet  = '';
        $ntpot = $tablepot + $nbet;
        poker_log($autoname, GAME_PLAYER_GOES_ALLIN, $gameID);
    }
    
    $result = $pdo->exec("update " . DB_POKER . " set pot = " . $ntpot . ", bet = " . $nbet . ", msg = '" . $msg . "', p" . $autoplayer . "pot = '" . $npot . "', p" . $autoplayer . "bet = '" . $nbet . "', hand = '4' , lastmove = " . ($time + 1) . " where gameID = " . $gameID . " and hand = '3'  ");
    $addons->get_hooks(
        array(
            'content' => $hand
        ),
        array(
            'page'     => 'includes/auto_move.php',
            'location'  => 'hand_change'
        )
    );
    die();
}

$hand4logic = ($hand == 4 && $lastmove < ($time - $autotimer) && $dealer == getplayerid($plyrname)) ? true : false;
$hand4logic = $addons->get_hooks(
    array(
        'state' => $hand4logic,
        'content' => $hand4logic,
    ),
    array(
        'page'     => 'includes/auto_move.php',
        'location'  => 'hand4_logic'
));

if ($hand4logic)
{
    poker_log('', GAME_MSG_DEAL_CARDS, $gameID);
    deal(10, $gameID, $game_style);
    $result = $pdo->exec("update " . DB_POKER . " set msg = '" . $msg . "', move = '" . $nextup . "', hand = '5' , lastmove = " . ($time + 1) . " where gameID = " . $gameID . " and hand = '4'  ");
    $addons->get_hooks(
        array(
            'content' => $hand
        ),
        array(
            'page'     => 'includes/auto_move.php',
            'location'  => 'hand_change'
        )
    );
    die();
}

if ($hand > 4 && $hand < 12 && $lastmove < ($time - $autotimer))
{
    $newr = '';

    if ($hand == 6)
    {
        $newr = ", hand = '7' ";
    }

    if ($hand == 8)
    {
        $newr = ", hand = '9' ";
    }

    if ($hand == 10)
    {
        $newr = ", hand = '11' ";
    }

    if ($tablebet > $autobet)
    {
        if ($hand < 6)
        {
            $result = $pdo->exec("update " . DB_STATS . " set fold_pf = fold_pf+1 where player  = '" . $autoname . "' ");
        }
        elseif ($hand < 8)
        {
            $result = $pdo->exec("update " . DB_STATS . " set fold_f = fold_f+1 where player  = '" . $autoname . "' ");
        }
        elseif ($hand < 10)
        {
            $result = $pdo->exec("update " . DB_STATS . " set fold_t = fold_t+1 where player  = '" . $autoname . "' ");
        }
        else
        {
            $result = $pdo->exec("update " . DB_STATS . " set fold_r = fold_r+1 where player  = '" . $autoname . "' ");
        }

        $msg    = $autoname . ' ' . GAME_PLAYER_FOLDS;
        $result = $pdo->exec("update " . DB_POKER . " set  msg = '" . $msg . "', p" . $autoplayer . "bet = 'F" . $autobet . "', move = '" . $nextup . "' " . $newr . " , lastmove = " . ($time + 1) . "  where gameID = " . $gameID);
    }
    else
    {
        $msg    = $autoname . ' ' . GAME_PLAYER_CHECKS;
        $result = $pdo->exec("update " . DB_STATS . " set checked = checked+1 where player  = '" . $autoname . "' ");
        $result = $pdo->exec("update " . DB_POKER . " set msg = '" . $msg . "', move = '" . $nextup . "' " . $newr . " , lastmove = " . ($time + 1) . " where gameID = " . $gameID);
    }

    $addons->get_hooks(
        array(
            'content' => $hand
        ),
        array(
            'page'     => 'includes/auto_move.php',
            'location'  => 'hand_change'
        )
    );
    die();
}

if ($hand == 13)
{
    if ($game_style == GAME_TEXAS) {
        $cardq = $pdo->prepare("select card1, card2, card3, card4, card5, p1card1, p1card2, p2card1, p2card2, p3card1, p3card2, p4card1, p4card2, p5card1, p5card2, p6card1, p6card2, p7card1, p7card2, p8card1, p8card2, p9card1, p9card2, p10card1, p10card2 from " . DB_POKER . " where gameID = " . $gameID);
    } else {
        $cardq = $pdo->prepare("select card1, card2, card3, card4, card5, p1card1, p1card2, p1card3, p1card4, p2card1, p2card2, p2card3, p2card4, p3card1, p3card2, p3card3, p3card4, p4card1, p4card2, p4card3, p4card4, p5card1, p5card2, p5card3, p5card4, p6card1, p6card2, p6card3, p6card4, p7card1, p7card2, p7card3, p7card4, p8card1, p8card2, p8card3, p8card4, p9card1, p9card2, p9card3, p9card4, p10card1, p10card2, p10card3, p10card4 from " . DB_POKER . " where gameID = " . $gameID);
    }

    $cardq->execute();
    $cardr = $cardq->fetch(PDO::FETCH_ASSOC);
    $tablecards = array(
        decrypt_card($cardr['card1']),
        decrypt_card($cardr['card2']),
        decrypt_card($cardr['card3']),
        decrypt_card($cardr['card4']),
        decrypt_card($cardr['card5'])
    );

    $multiwin   = find_winners($game_style);
    $winners    = (($multiwin[1] == '') ? 1 : 2);
    $thiswin    = evaluatewin($multiwin[0], $game_style);

    $thiswin    = addslashes($thiswin);

    if ($winners > 1)
    {
        $msg = GAME_MSG_SPLIT_POT . ' ' . $thiswin;
        poker_log('', $msg, $gameID);
    }
    else
    {
        $msg = GAME_MSG_WINNING_HAND . ' ' . $thiswin;
        poker_log( get_name($multiwin[0]), 'wins the hand with a' . $thiswin, $gameID );
    }

    $result = $pdo->exec("update " . DB_POKER . " set msg = '" . $msg . "', hand = '14', lastmove = " . ($time + 1) . "  where gameID = " . $gameID . " and hand = '13' ");
    $addons->get_hooks(
        array(
            'content' => $hand
        ),
        array(
            'page'     => 'includes/auto_move.php',
            'location'  => 'hand_change'
        )
    );
    die();
}

if ($hand == 14)
{
    echo $addons->get_hooks(array(), array(
        'page'     => 'includes/auto_move.php',
        'location'  => 'hand_14'
    ));
    
    $proc   = GAME_MSG_PROCESSING;
    $result = $pdo->exec("update " . DB_POKER . " set msg = '" . $proc . "', move = '" . $nextup . "', hand = '15', lastmove = " . ($time + 1) . " where gameID = " . $gameID . " and hand = '14' ");
    $addons->get_hooks(
        array(
            'content' => $hand
        ),
        array(
            'page'     => 'includes/auto_move.php',
            'location'  => 'hand_change'
        )
    );
    die();
}

$hand15logic = ($hand == 15 && $autoplayer == getplayerid($plyrname)) ? true : false;
$hand15logic = $addons->get_hooks(
    array(
        'state' => $hand15logic,
        'content' => $hand15logic,
    ),
    array(
        'page'     => 'includes/auto_move.php',
        'location'  => 'hand15_logic'
));
if ($hand15logic)
{
    $cardq = $pdo->prepare("select * from " . DB_POKER . " where gameID = " . $gameID);

    $cardq->execute();
    $cardr      = $cardq->fetch(PDO::FETCH_ASSOC);
    $tablecards = array(
        decrypt_card($cardr['card1']),
        decrypt_card($cardr['card2']),
        decrypt_card($cardr['card3']),
        decrypt_card($cardr['card4']),
        decrypt_card($cardr['card5'])
    );

    $multiwin   = find_winners($game_style);
    $i          = 0;

    while ($multiwin[$i] != '')
    {
        $usr    = get_name($multiwin[$i]);
        $result = $pdo->exec("update " . DB_STATS . " set handswon = handswon+1 where player  = '" . $usr . "' ");
        $i++;
    }

    distpot($game_style);
    $result = $pdo->exec("update " . DB_POKER . " set hand = '0' , pot = 0, lastmove = " . ($time + 1) . " where gameID = " . $gameID . " and hand = '15' ");
    $addons->get_hooks(
        array(
            'content' => $hand
        ),
        array(
            'page'     => 'includes/auto_move.php',
            'location'  => 'hand_change'
        )
    );
}

echo $addons->get_hooks(
    array(),
    array(
        'page'     => 'includes/auto_move.php',
        'location'  => 'page_end'
    )
);
?>