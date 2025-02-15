<?php
/**/
function playerjoin_assign_botgod( $array = array() )
{
    global $pdo, $gameID, $action, $plyrname;
    
    $tpq = $pdo->prepare("SELECT p" . $action . "name FROM " . DB_POKER . " WHERE gameID = $gameID");
    $tpq->execute();
    $tpr = $tpq->fetch(PDO::FETCH_ASSOC);
    
    $botgod = $tpr['botgod'];

    if ($botgod == '' || $botgod == 0 || $botgod == '0')
    {
        $pdo->exec("UPDATE " . DB_POKER . " SET botgod = '$action' WHERE gameID = $gameID");
        return true;
    }
    else
    {
        $tpq = $pdo->prepare("SELECT p" . $botgod . "name FROM " . DB_POKER . " WHERE gameID = $gameID");
        $tpq->execute();
        $tpr = $tpq->fetch(PDO::FETCH_ASSOC);
        $pi = $tpr['p' . $botgod . 'name'];
        
        if ($pi == '' || isbotplayer($pi))
        {
            $pdo->exec("UPDATE " . DB_POKER . " SET botgod = '$action' WHERE gameID = $gameID");
            return true;
        }
    }
	
}
$addons->add_hook(array(
	'page'     => 'includes/join.php',
	'location' => 'player_joined',
	'function' => 'playerjoin_assign_botgod',
));