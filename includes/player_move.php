<?php
require ('sec_inc.php');

header('Content-Type: text/javascript');

echo $addons->get_hooks(array(), array(
    'page'     => 'includes/player_move.php',
    'location'  => 'page_start'
));

if ($gID == '' || $gID == 0)
{
    die();
}


$time    = time();
$tpq_sql = $addons->get_hooks(
    array(
        'content' => "SELECT * FROM " . DB_POKER . " WHERE gameID = " . $gameID
    ),
    array(
        'page'     => 'includes/player_move.php',
        'location'  => 'tpq_sql'
    )
);
$tpq = $pdo->prepare($tpq_sql);
$tpq->execute();
$tpr = $tpq->fetch(PDO::FETCH_ASSOC);
$player = getplayerid($plyrname);
$tomove = $tpr['move'];
$addons->get_hooks(
    array(),
    array(
        'page'     => 'includes/player_move.php',
        'location'  => 'tpr_variables'
    )
);

$lastmove = $tpr['lastmove'];
$dealer = $tpr['dealer'];
$hand = $tpr['hand'];
$tablepot = $tpr['pot'];
$tablebet = $tpr['bet'];
$tablelimit = $tpr['tablelimit'];
$lastbet = $tpr['lastbet'];
$numleft = get_num_left();
$playerpot = $tpr['p' . $player . 'pot'];
$playerbet = $tpr['p' . $player . 'bet'];


// Action

$action = addslashes($_GET['action']);

// All doable actions

$numactions = array();
/*$step       = 50;
$max        = ((int) $playerpot) / $step;
for ($ni = 1; $ni <= $max; $ni++)
{ 
    $numactions[] = $ni * $step;
}*/
$max = (int) $playerpot;
for ($ni = 1; $ni <= $max; $ni++)
{ 
    $numactions[] = $ni;
}


$actions_array = array_merge(array(
    'check',
    'call',
    'allin',
    'fold',
    'start'
), $numactions);

// Actions you can do after game starts

$betactions_array = array_merge(array(
    'check',
    'call',
    'allin',
    'fold'
), $numactions);

// if action is not in the list, die

if (!in_array($action, $actions_array))
{
    die();
}

// if action is a bet

$isbet = false;

if (in_array($action, $betactions_array))
{
    $isbet = true;
}

// if player doesn't exist

if ($player == '')
{
    die();
}

// get number of players

$numplayers = get_num_players();

// if action is start the game
echo $addons->get_hooks(
    array(),
    array(
        'page'     => 'includes/player_move.php',
        'location'  => 'beginning_of_action'
    )
);


if ((!isset($hand) || $hand == '') && $numplayers > 1 && $action == 'start')
{
    $msg        = GAME_STARTING;
    $result_sql = $addons->get_hooks(
        array(
            'content' => "update " . DB_POKER . " set  hand = '0', msg = '$msg' , move = '$player', dealer = '$player' where gameID = $gameID and hand = ''"
        ),
        array(
            'page'     => 'includes/player_move.php',
            'location'  => 'start_sql1'
        )
    );
    $result     = $pdo->exec($result_sql);
    $result     = $pdo->exec("update " . DB_PLAYERS . " set  lastmove = " . ($time + 1) . " where username = '$plyrname' ");
    die();
}


// if it's not player's move, die

if ($tomove != $player)
{
    die();
}

$process = false;

if (substr($playerbet, 0, 1) == 'F')
{
    die();
}

// if player's pot is empty, die

if ($playerpot == 0)
{
    die();
}

// if everything is okay

$everythingOkayLogic = ($hand > 4 && $hand < 12 && $player == $tomove && $numplayers > 1 && $isbet == true) ? true : false;
$everythingOkayLogic = $addons->get_hooks(
    array(
        'state' => $everythingOkayLogic,
        'content' => $everythingOkayLogic,
    ),
    array(
        'page'     => 'includes/player_move.php',
        'location'  => 'everything_okay_logic'
    )
);
if ($everythingOkayLogic)
{
    $goallin = false;
    $nextup = nextplayer($player);
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

    if ($action == 'allin')
    {
        $result = $pdo->exec("update " . DB_STATS . " set allin = allin+1 where player  = '$plyrname' ");
        $goallin = true;
    }
    elseif ($action == 'fold')
    {
        if ($hand < 6)
        {
            $result = $pdo->exec("update " . DB_STATS . " set fold_pf = fold_pf+1 where player  = '$plyrname' ");
        }
        elseif ($hand < 8)
        {
            $result = $pdo->exec("update " . DB_STATS . " set fold_f = fold_f+1 where player  = '$plyrname' ");
        }
        elseif ($hand < 10)
        {
            $result = $pdo->exec("update " . DB_STATS . " set fold_t = fold_t+1 where player  = '$plyrname' ");
        }
        else
        {
            $result = $pdo->exec("update " . DB_STATS . " set fold_r = fold_r+1 where player  = '$plyrname' ");
        }

        $msg = '<span class="chatName">' . $plyrname . '</span> ' . GAME_PLAYER_FOLDS;

        $result = $pdo->exec("update " . DB_POKER . " set  msg = '$msg', p" . $player . "bet = 'F$playerbet', move = '$nextup' $newr , lastmove = " . ($time + 1) . "  where gameID = " . $gameID);
        $result = $pdo->exec("update " . DB_PLAYERS . " set  lastmove = " . ($time + 1) . " where username = '$plyrname' ");

        poker_log($plyrname, GAME_PLAYER_FOLDS, $gameID);
    }
    elseif ($action == 'check')
    {
        $msg = '<span class="chatName">' . $plyrname . '</span> ' . GAME_PLAYER_CHECKS;

        $result = $pdo->exec("update " . DB_STATS . " set checked = checked+1 where player  = '$plyrname' ");
        $result = $pdo->exec("update " . DB_POKER . " set msg = '$msg', move = '$nextup' $newr , lastmove = " . ($time + 1) . " where gameID = " . $gameID);
        $result = $pdo->exec("update " . DB_PLAYERS . " set  lastmove = " . ($time + 1) . " where username = '$plyrname' ");

        poker_log($plyrname, GAME_PLAYER_CHECKS, $gameID);
    }
    elseif ($action == 'call')
    {
        $result = $pdo->exec("update " . DB_STATS . " set called = called+1 where player  = '$plyrname' ");
        $process = true;
        $callbet = $tablebet - $playerbet;
        if ($playerpot <= $callbet)
        {
            $goallin = true;
        }
        else
        {
            $potleft = $playerpot - $callbet;
            $tablepot = $tablepot + $callbet;
            $pbet = $tablebet;
            $tablebet2 = $tablebet;
        }

        $msg = '<span class="chatName">' . $plyrname . '</span> ' . GAME_PLAYER_CALLS . ' ' . money_small($callbet);
        poker_log($plyrname, GAME_PLAYER_CALLS . ' ' . money_small($callbet), $gameID);
    }
    elseif ($action >= $playerpot)
    {
        $result = $pdo->exec("update " . DB_STATS . " set allin = allin+1 where player  = '$plyrname' ");
        $goallin = true;
    }
    else
    {
        $result = $pdo->exec("update " . DB_STATS . " set bet = bet+1 where player  = '$plyrname' ");
        $diff = ($tablebet - $playerbet);
        $checkbet = ($diff + $action);
        if ($checkbet >= $playerpot)
        {
            $goallin = true;
        }
        else
        {
            $process = true;
            $pbet = $tablebet + $action;
            $tablepot = $tablepot + $checkbet;
            $potleft = $playerpot - $checkbet;
            $tablebet2 = $tablebet + $action;
        }

        $msg = '<span class="chatName">' . $plyrname . '</span> ' . GAME_PLAYER_RAISES . ' ' . money_small($action);
        poker_log($plyrname, GAME_PLAYER_RAISES . ' ' . money_small($action), $gameID);
    }

    if ($goallin == true)
    {
        $process = true;
        $diff = ($tablebet - $playerbet);
        $raise = $playerpot - $diff;
        $tablepot = $tablepot + $playerpot;
        $tablebet2 = (($raise > 0) ? ($tablebet + $raise) : $tablebet);
        $pbet = $playerbet + $playerpot;
        $potleft = 0;

        $msg = '<span class="chatName">' . $plyrname . '</span> ' . GAME_PLAYER_GOES_ALLIN;
        poker_log($plyrname, GAME_PLAYER_GOES_ALLIN, $gameID);
    }

    if ($process == true)
    {
        $lastbet = ($tablebet2 > $tablebet || $lastbet == 0) ? $player . '|' . $tablebet : $lastbet;
        $result = $pdo->exec("update " . DB_POKER . " set msg = '$msg', pot = $tablepot, bet = $tablebet2, lastbet = '$lastbet', p" . $player . "bet = '$pbet', move = '$nextup', lastmove = " . ($time + 1) . " , p" . $player . "pot = '$potleft' " . $newr . "where gameID = " . $gameID);
        $result = $pdo->exec("update " . DB_PLAYERS . " set  lastmove = $time where username = '$plyrname' ");
    }

    $addons->get_hooks(
        array(),
        array(
            'page'     => 'includes/player_move.php',
            'location'  => 'after_move'
        )
    );
}

?>