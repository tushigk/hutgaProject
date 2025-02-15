<?php 
require('sec_inc.php'); 

header('Content-Type: text/javascript');

// ip address
$ip = $_SERVER['REMOTE_ADDR'];  

// if ip check is on
if (IPCHECK == 1)
{ 
	$ipq = $pdo->prepare("select ipaddress from " . DB_PLAYERS . " where ipaddress = '$ip' and gID = " . $gameID); $ipq->execute(); 

	// if user with the same ip exists, die
	if ($ipq->rowCount() > 0)
	{
		die();
	} 
} 
	
$time = time(); 
$action = addslashes($_GET['action']); 

// if it is a proper action
if ($action > 0 && $action < 11)
{
	$zq = $pdo->prepare("select gameID from " . DB_POKER . " where gameID = $gameID and (p1name = '$plyrname' or p2name = '$plyrname' or p3name = '$plyrname' or p4name = '$plyrname' or p5name = '$plyrname' or p6name = '$plyrname' or p7name = '$plyrname' or p8name = '$plyrname' or p9name = '$plyrname' or p10name = '$plyrname')"); $zq->execute();
	
	// if player is already joined, die
	if ($zq->rowCount() == 1)
	{
		die();
	}
	
	$cq = $pdo->prepare("select p".$action."name, tablelimit, tablelow, tabletype, hand from " . DB_POKER . " where gameID = " . $gameID . " and p".$action."name = ''"); $cq->execute(); 
	
	// if player hasn't joined yet
	if ($cq->rowCount() == 1)
	{
		$cr 	= $cq->fetch(PDO::FETCH_ASSOC); 
		$statsq = $pdo->prepare("select winpot from " . DB_STATS . " where player = '$plyrname'"); $statsq->execute(); 
		$statsr = $statsq->fetch(PDO::FETCH_ASSOC);
		 
		$winnings = $statsr['winpot']; 
		$tablelow = $cr['tablelow']; 
		
		$tablelimit = $cr['tablelimit']; 
		$tabletype  = $cr['tabletype']; 
		
		$hand = $cr['hand']; 
		
		if ($tabletype == 't' && $hand != '') die();
		
		$stake = ($winnings > $tablelimit) ? $tablelimit : $winnings;
		$stake = $addons->get_hooks(
			array(
				'content' => $stake
			),
			array(
        		'page'     => 'includes/join.php',
        		'location'  => 'stake_variable'
        	)
		);
		
		if ($tabletype == 't') $tablelow = $tablelimit;
		
		if ($stake >= $tablelow && $stake > 0)
		{
			$result = $pdo->exec("update " . DB_POKER . " set p".$action."name = '$plyrname', p".$action."bet = 'F', p".$action."pot = '$stake' where gameID = " . $gameID);
			$bank   = $winnings-$stake; 
			
			if ($tabletype == 't')
			{
				$result = $pdo->exec("update " . DB_STATS . " set tournamentsplayed = tournamentsplayed + 1, winpot = $bank  where player  = '$plyrname'");
			}
			else
			{
				$result = $pdo->exec("update " . DB_STATS . " set gamesplayed = gamesplayed + 1, winpot = $bank  where player  = '$plyrname'"); 
			} 
			
			$result = $pdo->exec("update " . DB_PLAYERS . " set gID = $gameID, lastmove = " . ($time+3) . ", timetag = " . ($time+3) . "  where username  = '$plyrname'");
			
			$addons->get_hooks(array(), array(
            	'page'     => 'includes/join.php',
            	'location'  => 'player_joined'
            ));
			
			poker_log($plyrname, GAME_PLAYER_BUYS_IN . ' ' . money($stake), $gameID); 
?>
document.getElementById('player-<?php echo $action; ?>-image').innerHTML = '<img src="themes/<?php echo THEME; ?>/images/13.gif">';
<?php }else{ ?>
<?php if($tabletype == 't'){ ?>
alert('<?php echo INSUFFICIENT_BANKROLL_TOURNAMENT;?>');
<?php }else{ ?>
alert('<?php echo INSUFFICIENT_BANKROLL_SITNGO;?>');
<?php } ?>
<?php } } } $result = $pdo->exec("update ".DB_POKER." set lastmove = ".($time+2)."  where gameID = ".$gameID); ?>