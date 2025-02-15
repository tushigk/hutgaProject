<?php

if ($valid == false)
    header('Location: login.php');

if ($gameID == '')
    header('Location: lobby.php');

$time       = time();
$sql        = $addons->get_hooks(
    array(
        'content' => "SELECT * FROM " . DB_POKER . " WHERE gameID = " . $gameID
    ),
    array(
        'page'      => 'includes/inc_poker.php',
        'location'  => 'tq_sql',
    )
);
$tq         = $pdo->prepare($sql); $tq->execute();
$tr         = $tq->fetch(PDO::FETCH_ASSOC);
$tablehand  = $tr['hand'];
$tableid    = $tr['gameID'];
$tablename  = $tr['tablename'];
$tabletype  = $tr['tabletype'];
$tablelimit = $tr['tablelimit'];
$tablestyle = $tr['tablestyle'];
$sbamount	= $tr['sbamount'];
$bbamount	= $tr['bbamount'];

$addons->get_hooks(array(),
    array(
        'page'     => 'includes/inc_poker.php',
        'location' => 'after_tablestyle_var',
    )
);

$tomove = $tr['move'];
$min    = $tr['tablelow'];
$sq     = $pdo->prepare("select style_name, style_lic from styles where style_name = '$tablestyle'"); $sq->execute();

$officialstylepack = 'normal';
    
if (isset($_GET['action']) && $_GET['action'] == 'leave' && ($tablehand == '' || $tablehand == '0' || $tablehand == '1' || $tablehand == '2'))
{
    $tpq_sql   = "SELECT * FROM " . DB_POKER . " WHERE gameID = " . $gameID;
    $tpq       = $pdo->prepare($tpq_sql); $tpq->execute();
    $tpr       = $tpq->fetch(PDO::FETCH_ASSOC);
    $i         = 1;
    $player    = '';
    $playernum = 0;

    while ($i < 11)
    {
        if (strlen($tpr['p' . $i . 'name']) > 0)
            $playernum++;

        if ($tpr['p' . $i . 'name'] == $plyrname)
        {
            $player = $i;
            $pot    = $tpr['p' . $i . 'pot'];
        }
        $i++;
    }

    if ($player != '')
    {
        poker_log($plyrname, 'leaves the table', $gameID);

        $winpot = $pot;
        $winpot = $addons->get_hooks(
            array(
                'content' => $winpot,
            ),
            array(
                'page'     => 'includes/inc_poker.php',
                'location' => 'winpot_var',
            )
        );
        
        if ($tpr['tabletype'] !== 't' || $playernum === 1)
        {
            $statsq   = $pdo->prepare("select winpot from " . DB_STATS . " where player = '" . $plyrname . "' "); $statsq->execute();
            $statsr   = $statsq->fetch(PDO::FETCH_ASSOC);
            $winnings = $statsr['winpot'];
            $winpot  += $winnings;
            $result   = $pdo->exec("update " . DB_STATS . " set winpot = " . $winpot . " where player  = '" . $plyrname . "' ");
        }
        
        if ($tomove == $player)
        {
            $nxtp   = nextplayer($player);
            $result = $pdo->exec("update " . DB_POKER . " set p" . $player . "name = '', p" . $player . "bet = '', p" . $player . "pot = '', move = '" . $nxtp . "', lastmove = " . $time . "  where gameID = " . $gameID);
        }
        else
        {
            $result = $pdo->exec("update " . DB_POKER . " set p" . $player . "name = '', p" . $player . "bet = '', p" . $player . "pot = '', lastmove = " . $time . " where gameID = " . $gameID);
        }

        $addons->get_hooks(
            array(),
            array(
                'page'      => 'includes/inc_poker.php',
                'location'  => 'if_player_exists',
            )
        );
    }
    $wait    = (int) WAITIMER;
    $setwait = $time + $wait;
    $result  = $pdo->exec("update " . DB_PLAYERS . " set waitimer = " . $setwait . " where username = '" . $plyrname . "' ");
    $result  = $pdo->exec("update " . DB_PLAYERS . " set gID = 0, vID = 0 where username  = '" . $plyrname . "' ");
    header('Location: sitout.php');
}

?>