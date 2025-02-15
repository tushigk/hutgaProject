<?php

/**/
function botgod_page_start_function ( $array = array() )
{
	if (!function_exists('nextbotgod'))
    {
        function nextbotgod($player)
        {
            global $tpr, $pdo;
            $time = time();
            $playername = $tpr['p' . $player . 'name'];
            $i = $player;
            $z = 0;
            while ($z < 10)
            {
                $i++;
                $test = ots($i);
                if (($tpr['p' . $test . 'name'] != '') && ($tpr['p' . $test . 'name'] != $playername) && ($tpr['p' . $test . 'pot'] > 0))
                {
                    $pname = $tpr['p' . $test . 'name'];
                    $qq    = $pdo->prepare("SELECT isbot FROM " . DB_PLAYERS . " WHERE username = '$pname'");
                    $qq->execute();
                    $qr    = $qq->fetch(PDO::FETCH_ASSOC);

                    if ($qr['isbot'] == 0) return $test;
                }

                $i = $test;
                $z++;
            }
            
            return false;
        }
    }
    
    if (!function_exists('isbotplayer'))
    {
        function isbotplayer($player)
        {
            global $pdo;
            $qq = $pdo->prepare("SELECT isbot FROM " . DB_PLAYERS . " WHERE username = '$player'");
            $qq->execute();
            $qr = $qq->fetch(PDO::FETCH_ASSOC);

            if ($qr['isbot'] == 1) return true;
            
            return false;
        }
    }
}
$addons->add_hook(array(

	'page'     => 'includes/poker_inc.php',
	'location' => 'page_start',
	'function' => 'botgod_page_start_function',

));
