<?php
require ('sec_inc.php');

header('Content-Type: text/javascript');

$BUTTON_START    = BUTTON_START;
$BUTTON_FOLD     = BUTTON_FOLD;
$BUTTON_CHECK    = BUTTON_CHECK;
$BUTTON_CALL     = BUTTON_CALL;
$BUTTON_BET      = BUTTON_BET;
$BUTTON_ALLIN    = BUTTON_ALLIN;
$GAME_PLAYER_POT = GAME_PLAYER_POT;
$time            = time();
$testing         = 0;
$tpq             = $pdo->prepare("select * from " . DB_POKER . " where gameID = " . $gameID);
$tpq->execute();
$tpr             = $tpq->fetch(PDO::FETCH_ASSOC);
$lmove           = $tpr['lastmove'];
$tomove          = $tpr['move'];
$tabletype       = $tpr['tabletype'];
$tablestyle      = $tpr['tablestyle'];
$game_style = '';

if ($tpr['gamestyle'] == 't') {
    $game_style = GAME_TEXAS;
} else {
    $game_style = GAME_OMAHA;
}

require ('card_sys.php');

if ($tablestyle == '')
{
	$tablestyle = 'normal';
}

$hand   = $tpr['hand'];
$unixts = addslashes($_GET['ts']);
$hnd    = addslashes($_GET['h']);
$tmv    = addslashes($_GET['m']);
$force  = addslashes($_GET['f']);

$isPlaying = false;
for ($pi = 1; $pi < 11; $pi++)
{ 
	if ($tpr["p{$pi}name"] == $plyrname)
		$isPlaying = true;
}

if ($isPlaying)
{
	if (isset($_COOKIE['leavetable']) && $_COOKIE['leavetable'] == 'yes' && in_array($hand, array('0', '1', '2')))
	{
	?>
	document.cookie = "leavetable=no";
	parent.document.location.href = "poker.php?action=leave";
	<?php
		die();
	}

	if (in_array($hand, array('', '0', '1', '2')))
	{
	?>
	document.cookie = "leavetable=no";
	jQuery('#leaveButton').attr('href', 'poker.php?action=leave').off('click').removeAttr('data-clkd');
	<?php
	}
	else
	{
	?>
	jQuery('#leaveButton').attr('href', '#leave');
	if (typeof (jQuery('#leaveButton').attr('data-clkd')) === 'undefined')
	{
		jQuery('#leaveButton').attr('data-clkd', true).on('click', function(e)
		{
			document.cookie = "leavetable=yes";
			alert('You will be exited once this hand is over.');
		});
	}
	<?php
	}
}

if ($force == 1)
{
	$result = $pdo->exec("update " . DB_POKER . " set lastmove = " . ($time + 1) . "  where gameID = " . $gameID);
}

if ($lmove == $unixts && $lmove > 0 && $tmv == $tomove && $hnd == $hand && $force == 0)
{
	die();
}

$statsq = $pdo->prepare("select winpot from " . DB_STATS . " where player = '" . $plyrname . "' ");
$statsq->execute();
$statsr = $statsq->fetch(PDO::FETCH_ASSOC);

$winnings = $statsr['winpot'];

$tpq = $pdo->prepare("select * from " . DB_POKER . " where gameID = " . $gameID); $tpq->execute();
$tpr = $tpq->fetch(PDO::FETCH_ASSOC);

$player     = getplayerid($plyrname);
$tomove     = $tpr['move'];
$lastmove   = $tpr['lastmove'];
$dealer     = $tpr['dealer'];
$hand       = $tpr['hand'];
$tablepot   = $tpr['pot'];
$tablebet   = $tpr['bet'];
$numleft    = get_num_left();
$numplayers = get_num_players();
$playerpot  = get_pot($player);
$playerbet  = get_bet_math($player);
$tabletype  = $tpr['tabletype'];
$tablelimit = $tpr['tablelimit'];
$sbamount	= $tpr['sbamount'];
$bbamount	= $tpr['bbamount'];
$all_in     = false;


$i  = 1;
$np = 0;
$bu = 0;
$fd = 0;
while ($i < 11)
{
    $usr   = $tpr['p' . $i . 'name'];
    $upot  = $tpr['p' . $i . 'pot'];
    $ubet  = $tpr['p' . $i . 'bet'];
    $ufold = substr($ubet, 0, 1);

    if ($usr != '')
    {
        $np++;

        if ($ufold == 'F')
        	$fd++;

        if (($ubet == 0 || $ufold == 'F') && $upot == 0)
        {
            $bu++;
        }
    }
    $i++;
}
$allpl   = $np - $bu;
$plyngpl = $np - $fd;
$blindmultiplier = (11 - $allpl);
$tablemultiplier = 1;

if ($tabletype != 't')
    $blindmultiplier = 4;

if ($tablelimit == 25000)
    $tablemultiplier = 2;

if ($tablelimit == 50000)
    $tablemultiplier = 4;

if ($tablelimit == 100000)
    $tablemultiplier = 8;

if ($tablelimit == 250000)
    $tablemultiplier = 20;

if ($tablelimit == 500000)
    $tablemultiplier = 40;

if ($tablelimit == 1000000)
    $tablemultiplier = 80;

if ($sbamount != 0)
{    
	$SB = $sbamount;
	$BB = $bbamount;
}
else
{
	$SB = 25 * $blindmultiplier * $tablemultiplier;
	$BB = 50 * $blindmultiplier * $tablemultiplier;
}


if ($playerbet > 0 && $playerpot == 0)
{
	$all_in = true;
}

$checkround = false;
if ($player == last_bet() && check_bets() == true && ($hand == 5 || $hand == 7 || $hand == 9 || $hand == 11))
{
	$checkround = true;
}

$tablecards = array(
	decrypt_card($tpr['card1']),
	decrypt_card($tpr['card2']),
	decrypt_card($tpr['card3']),
	decrypt_card($tpr['card4']),
	decrypt_card($tpr['card5'])
);
$names = array(
	$tpr['p1name'],
	$tpr['p2name'],
	$tpr['p3name'],
	$tpr['p4name'],
	$tpr['p5name'],
	$tpr['p6name'],
	$tpr['p7name'],
	$tpr['p8name'],
	$tpr['p9name'],
	$tpr['p10name']
);
$avatars = array(
	get_ava($names[0]),
	get_ava($names[1]),
	get_ava($names[2]),
	get_ava($names[3]),
	get_ava($names[4]),
	get_ava($names[5]),
	get_ava($names[6]),
	get_ava($names[7]),
	get_ava($names[8]),
	get_ava($names[9])
);
$pots = array(
	money($tpr['p1pot']),
	money($tpr['p2pot']),
	money($tpr['p3pot']),
	money($tpr['p4pot']),
	money($tpr['p5pot']),
	money($tpr['p6pot']),
	money($tpr['p7pot']),
	money($tpr['p8pot']),
	money($tpr['p9pot']),
	money($tpr['p10pot'])
);
$make_cards = array(
	'',
	'',
	'',
	'',
	'',
	''
);
$bets = array(
	money_small($tpr['p1bet']),
	money_small($tpr['p2bet']),
	money_small($tpr['p3bet']),
	money_small($tpr['p4bet']),
	money_small($tpr['p5bet']),
	money_small($tpr['p6bet']),
	money_small($tpr['p7bet']),
	money_small($tpr['p8bet']),
	money_small($tpr['p9bet']),
	money_small($tpr['p10bet'])
);

?>
jQuery('.player-dealer').hide();
<?php

$i = 0;
while ($i < 10)
{
	$pbet = $tpr['p' . ($i + 1) . 'bet'];
	$pbetf = substr($pbet, 0, 1);

	if ($pbetf == 'F')
	{
		$bets[$i] = money_small(substr($pbet, 1, 10));
	}

	if (($i + 1) == $dealer)
	{
	?>
	jQuery('#player-<?= ($i + 1); ?>-dealer').show();
	<?php
	}

	if (($pbet > 0 || ($pbetf == 'F' && $bets[$i] > 0)) && $hand > 2)
	{
		$opsTheme->addVariable('bet_money', $bets[$i]);
	?>
		var bet<?php echo ($i + 1); ?> = '<?php echo $opsTheme->viewPart('poker-player-chips'); ?>';
	<?php
	}
	else
	{
	?>
		var bet<?php echo ($i + 1); ?> = '';
	<?php
	}

	$i++;
}

$make_cards = array(
	'',
	'',
	'',
	'',
	'',
	''
);
$i = 1;

while ($i < 11)
{
	$testname = $tpr['p' . $i . 'name'];

	if ($testname == $plyrname && $testname != '')
	{
		if ((get_bet($i) != 'FOLD') && ($hand > 4) && ($hand < 15) && ((get_bet($i) > 0) || (get_pot($i) > 0)))
		{
		    if ($game_style == GAME_TEXAS) {
                $make_cards[$i] = decrypt_card($tpr['p' . $i . 'card1']) . '.png|' . decrypt_card($tpr['p' . $i . 'card2']) . '.png';
            } else {
                $make_cards[$i] = decrypt_card($tpr['p' . $i . 'card1']) . '.png|' . decrypt_card($tpr['p' . $i . 'card2']) . '.png|' . decrypt_card($tpr['p' . $i . 'card3']) . '.png|' . decrypt_card($tpr['p' . $i . 'card4']) . '.png';
            }
		}
		else
		{
			$make_cards[$i] = '';
		}
	}
	elseif ($tpr['p' . $i . 'name'] != '')
	{
		if ((get_bet($i) != 'FOLD') && ($hand == 14) && (get_bet($i) > 0) && ($numleft > 1))
		{
		    if ($game_style == GAME_TEXAS) {
                $make_cards[$i] = decrypt_card($tpr['p' . $i . 'card1']) . '.png|' . decrypt_card($tpr['p' . $i . 'card2']) . '.png';
            } else {
                $make_cards[$i] = decrypt_card($tpr['p' . $i . 'card1']) . '.png|' . decrypt_card($tpr['p' . $i . 'card2']) . '.png|' . decrypt_card($tpr['p' . $i . 'card3']) . '.png|' . decrypt_card($tpr['p' . $i . 'card4']) . '.png';
            }
		}
		elseif ((get_bet($i) != 'FOLD') && ($hand > 4) && ($hand < 14))
		{
			$make_cards[$i] = 'facedown.png';
		}
		else
		{
			$make_cards[$i] = '';
		}
	}
	else
	{
		$make_cards[$i] = '';
	}

	$i++;
}

$msg = $tpr['msg'];
?>
var dtxt = '<font color="#FFCC33"><?php echo $msg; ?></font>';

<?php
$sfx = '';

if ((strstr($msg, GAME_MSG_DEAL_CARDS)) && ($numleft > 1)) $sfx = 'deal.mp3';

if (strstr($msg, " " . GAME_MSG_BIG_BLIND)) $sfx = 'chips.mp3';

if (strstr($msg, " " . GAME_MSG_SMALL_BLIND)) $sfx = 'chips.mp3';

if (strstr($msg, " " . GAME_PLAYER_CALLS)) $sfx = 'chips.mp3';

if (strstr($msg, " " . GAME_PLAYER_RAISES)) $sfx = 'chips.mp3';

if (strstr($msg, " " . GAME_PLAYER_GOES_ALLIN)) $sfx = 'chips.mp3';

if ($hand == 2) $sfx = 'shuffle.mp3';

if ($hand == 14) $sfx = 'chips.mp3';

if (strstr($msg, GAME_MSG_DEAL_FLOP)) $sfx = 'flop.mp3';

if (strstr($msg, GAME_MSG_DEAL_TURN)) $sfx = 'card.mp3';

if (strstr($msg, GAME_MSG_DEAL_RIVER)) $sfx = 'card.mp3';

if (strstr($msg, " " . GAME_PLAYER_CHECKS)) $sfx = 'check.mp3';

if (strstr($msg, " " . GAME_PLAYER_FOLDS)) $sfx = 'fold.mp3'; ?>


<?php
$i = 1;

while ($i < 11)
{
	$cards = explode('|', $make_cards[$i]);

	if ($game_style == GAME_TEXAS) {
        if (isset($cards[1]) && $cards[1] != '')
        {
            ?>
            var cards<?php echo $i; ?> = '<?php echo $playerCard[$cards[0]]; ?>';
            cards<?php echo $i; ?> += '<?php echo $playerCard[$cards[1]]; ?>';
            <?php
        }
        elseif (isset($cards[0]) && $cards[0] != '')
        {
            ?>
            var cards<?php echo $i; ?> = '<?php echo $playerCard[$cards[0]]; ?>';
            <?php
        }
        else
        {
            ?>
            var cards<?php echo $i; ?> = '';
            <?php
        }
    } else {
        if (isset($cards[3]) && $cards[3] != '')
        {
            ?>
            var cards<?php echo $i; ?> = '<?php echo $playerCard[$cards[0]]; ?>';
            cards<?php echo $i; ?> += '<?php echo $playerCard[$cards[1]]; ?>';
            cards<?php echo $i; ?> += '<?php echo $playerCard[$cards[2]]; ?>';
            cards<?php echo $i; ?> += '<?php echo $playerCard[$cards[3]]; ?>';
            <?php
        }
        elseif (isset($cards[2]) && $cards[2] != '')
        {
            ?>
            var cards<?php echo $i; ?> = '<?php echo $playerCard[$cards[0]]; ?>';
            cards<?php echo $i; ?> += '<?php echo $playerCard[$cards[1]]; ?>';
            cards<?php echo $i; ?> += '<?php echo $playerCard[$cards[2]]; ?>';
            <?php
        }
        elseif (isset($cards[1]) && $cards[1] != '')
        {
            ?>
            var cards<?php echo $i; ?> = '<?php echo $playerCard[$cards[0]]; ?>';
            cards<?php echo $i; ?> += '<?php echo $playerCard[$cards[1]]; ?>';
            <?php
        }
        elseif (isset($cards[0]) && $cards[0] != '')
        {
            ?>
            var cards<?php echo $i; ?> = '<?php echo $playerCard[$cards[0]]; ?>';
            <?php
        }
        else
        {
            ?>
            var cards<?php echo $i; ?> = '';
            <?php
        }
    }

	$i++;
}
?>


<?php
$i = 0;

while ($i < 10)
{
	if (isset($names[$i]) && $names[$i] != '')
	{
		$opsTheme->addVariable('player', array(
			'name'   => $names[$i],
			'pot'    => $pots[$i],
			'avatar' => $avatars[$i]
		));
	?>
		var info<?php echo ($i + 1); ?> = '<?php echo $opsTheme->viewPart('poker-player-info'); ?>';
		var ava<?php echo ($i + 1); ?>  = '<?php echo $opsTheme->viewPart('poker-player-avatar'); ?>';	
	<?php
	}
	else
	{
		if ($tabletype == 't' && $hand != '')
		{
		?>
			var info<?php echo ($i + 1); ?> = '';
			var ava<?php echo ($i + 1); ?>  = '';
		<?php
		}
		else
		{
		?>
			var info<?php echo ($i + 1); ?> = '';
			var ava<?php echo ($i + 1); ?>  = '<?php echo poker_seat_circle_html(($i + 1), true); ?>';
	<?php }
	}

	$i++;
}

if (((($hand > 5) && ($hand < 12)) || ($hand == 13) || ($hand == 14)) && ($numleft > 1))
{
?>
	var ccard1 = '<?php echo $tableCard[$tablecards[0]]; ?>';
<?php
}
else
{
?>
	var ccard1 = '<?php echo $tableCard['facedown']; ?>';
<?php
}

if (((($hand > 5) && ($hand < 12)) || ($hand == 13) || ($hand == 14)) && ($numleft > 1))
{
?>
	var ccard2 = '<?php echo $tableCard[$tablecards[1]]; ?>';
<?php
}
else
{
?>
	var ccard2 = '<?php echo $tableCard['facedown']; ?>';
<?php
}

if (((($hand > 5) && ($hand < 12)) || ($hand == 13) || ($hand == 14)) && ($numleft > 1))
{
?>
	var ccard3 = '<?php echo $tableCard[$tablecards[2]]; ?>';
<?php
}
else
{
?>
	var ccard3 = '<?php echo $tableCard['facedown']; ?>';
<?php
}

if (((($hand > 7) && ($hand < 12)) || ($hand == 13) || ($hand == 14)) && ($numleft > 1))
{
?>
	var ccard4 = '<?php echo $tableCard[$tablecards[3]]; ?>';
<?php
}
else
{
?>
	var ccard4 = '<?php echo $tableCard['facedown']; ?>';
<?php
}

if (((($hand > 9) && ($hand < 12)) || ($hand == 13) || ($hand == 14)) && ($numleft > 1))
{
?>
	var ccard5 = '<?php echo $tableCard[$tablecards[4]]; ?>';
<?php
}
else
{
?>
	var ccard5 = '<?php echo $tableCard['facedown']; ?>';
<?php
}
?>

var dealertxt = '<?php echo $msg; ?>';
var tablepot = '<?php echo money($tablepot); ?>';
var startbtnhtml = '';

<?php
if ($hand == '' && $numplayers > 1)
{
?>
document.getElementById('buttonContainer').style.cssText = 'display: none !important';
startbtnhtml = '<?php echo $opsTheme->viewPart('poker-game-start'); ?>';
<?php
}
elseif ($hand > 4 && $hand < 12 && $player == $tomove && $numplayers > 1 && $all_in == false && $numleft > 1 && $checkround == false)
{
	$button_limit = array(
		'5',
		'100',
		'150',
		'250',
		'500',
		'15000',
		'25000',
		'50000',
		'100000',
		'250000',
		'500000',
		'1000000',
		'2500000',
		'5000000'
	);
	$button_array = array(
		'50',
		'100',
		'150',
		'250',
		'500',
		'1000',
		'2500',
		'5000',
		'10000',
		'25000',
		'50000',
		'100000',
		'250000',
		'500000'
	);
	$button_display = array();
	$winpot = get_pot($player);
	$i = 13;
	$x = 4;
	while (($i >= 0) && ($x >= 0))
	{
		if (($winpot > $button_limit[$i]) && ($winpot >= (($tablebet - $playerbet) + $button_array[$i])))
		{
			$button_display[$x] = $button_array[$i];
			$x--;
		}

		$i--;
	}

	$divider = 1;
	if ($smallbetfunc == 1)
		$divider = 1000;
	elseif ($smallbetfunc == 2)
		$divider = 100;
	elseif ($smallbetfunc == 3)
		$divider = 10;
	$opsTheme->addVariable('divider', $divider);

	$multiplier = 1;
	if ($smallbetfunc == 1)
		$multiplier = 1000;
	elseif ($smallbetfunc == 2)
		$multiplier = 100;
	elseif ($smallbetfunc == 3)
		$multiplier = 10;
	$opsTheme->addVariable('multiplier', $multiplier);

	$initialRaise = $BB;
	if ($tablebet > $playerbet && $winpot >= ($tablebet - $playerbet))
		$initialRaise = ($tablebet - $playerbet) * 2;

	$initialRaise = $addons->get_hooks(
		array(
			'content' => $initialRaise
		),
		array(
	    	'page'     => 'includes/push_poker.php',
	    	'location' => 'initial_raise'
		)
	);
	$opsTheme->addVariable('initial_raise', $initialRaise);
	$opsTheme->addVariable('initial_raise_label', transfer_from($initialRaise));

	$raiseStep = $addons->get_hooks(
		array(
			'content' => $initialRaise
		),
		array(
	    	'page'     => 'includes/push_poker.php',
	    	'location' => 'raise_step'
		)
	);
	$opsTheme->addVariable('raise_step', $raiseStep);
	$opsTheme->addVariable('raise_step_label', transfer_from($raiseStep));

	$minRaise  = $addons->get_hooks(
		array(
			'content' => $raiseStep
		),
		array(
	    	'page'     => 'includes/push_poker.php',
	    	'location' => 'min_raise'
		)
	);
	$opsTheme->addVariable('min_raise', $minRaise);
	$opsTheme->addVariable('min_raise_label', transfer_from($minRaise));

	$maxRaise  = $addons->get_hooks(
		array(
			'content' => $winpot
		),
		array(
	    	'page'     => 'includes/push_poker.php',
	    	'location' => 'max_raise'
		)
	);
	$opsTheme->addVariable('max_raise', $maxRaise);
	$opsTheme->addVariable('max_raise_label', transfer_from($maxRaise));

	$displayFoldButton  = $addons->get_hooks(
		array(
			'content' => ($tablebet > $playerbet) ? true : false
		),
		array(
	    	'page'     => 'includes/push_poker.php',
	    	'location' => 'display_fold_button'
		)
	);
	$displayCallButton  = $addons->get_hooks(
		array(
			'content' => ($tablebet > $playerbet && $winpot >= ($tablebet - $playerbet)) ? true : false
		),
		array(
	    	'page'     => 'includes/push_poker.php',
	    	'location' => 'display_call_button'
		)
	);
	$displayCheckButton = $addons->get_hooks(
		array(
			'content' => ($displayCallButton == false && $winpot >= ($tablebet - $playerbet)) ? true : false
		),
		array(
	    	'page'     => 'includes/push_poker.php',
	    	'location' => 'display_check_button'
		)
	);
	$displayAllInButton = $addons->get_hooks(
		array(
			'content' => true
		),
		array(
	    	'page'     => 'includes/push_poker.php',
	    	'location' => 'display_allin_button'
		)
	);
	$displayRaiseButton = $addons->get_hooks(
		array(
			'content' => true
		),
		array(
	    	'page'     => 'includes/push_poker.php',
	    	'location' => 'display_raise_button'
		)
	);
	?>
		var betbuttons = '';

		<?php if ($displayFoldButton) { ?>
		betbuttons += '<?php echo $opsTheme->viewPart('poker-button-fold'); /* Fold Button */ ?>';
		<?php } ?>

		<?php if ($displayCallButton) {
			$opsTheme->addVariable('call_money', money_small($tablebet - $playerbet)); /* Call Button */
		?>
		betbuttons += '<?php echo $opsTheme->viewPart('poker-button-call'); ?>';
		<?php } ?>

		<?php if ($displayCheckButton) { ?>
		betbuttons += '<?php echo $opsTheme->viewPart('poker-button-check'); /* Check Button */ ?>';
		<?php } ?>
		
		<?php if ($displayAllInButton) { ?>
		betbuttons += '<?php echo $opsTheme->viewPart('poker-button-allin'); /* All-In Button */ ?>';
		<?php } ?>
		
		<?php
		if ($displayRaiseButton)
		{
			$raiseLevelsHtml = '';
			$raiseLevels = $addons->get_hooks(
				array(
					'content' => array(
						($BB * 2) => '2 big blinds',
						$BB       => '1 big blind',
						$tablepot => 'Pot',
					)
				),
				array(
			    	'page'     => 'includes/push_poker.php',
			    	'location' => 'raise_levels'
				)
			);

			foreach ($raiseLevels as $rlValue => $rlLabel)
			{
				$opsTheme->addVariable('level', array(
					'value' => $rlValue,
					'text'  => $rlLabel,
				));
				$raiseLevelsHtml .= $opsTheme->viewPart('poker-raise-level');
			}

			$opsTheme->addVariable('raise_levels', $raiseLevelsHtml);
		?>
		betbuttons += '<?php echo $opsTheme->viewPart('poker-button-raise'); /* Raise Button */ ?>';
		<?php } ?>

		document.getElementById('buttons').innerHTML = betbuttons;
		
		<?php
		if ($displayRaiseButton)
		{
			$sliderHtml = $opsTheme->viewPart('poker-raise-slider');
			$opsTheme->addVariable('slider_html', $sliderHtml);
			echo $opsTheme->viewPart('poker-raise-js');
		}
		else
		{
			echo $opsTheme->viewPart('poker-raise-js-none');
		}
		?>

		document.getElementById('buttonContainer').style.cssText = 'display: flex !important';
<?php
}
else
{
?>
	document.getElementById('buttonContainer').style.cssText = 'display: none !important';
	document.getElementById('buttons').innerHTML = '';
<?php
}
?>


//document.getElementById('dealertxt').innerHTML = dealertxt;
document.getElementById('tablepot').innerHTML = tablepot;

// document.getElementById('playerup').innerHTML = playerup;

document.getElementById('startButton').innerHTML = startbtnhtml;


document.getElementById('player-1-info').innerHTML = info1;
document.getElementById('player-2-info').innerHTML = info2;
document.getElementById('player-3-info').innerHTML = info3;
document.getElementById('player-4-info').innerHTML = info4;
document.getElementById('player-5-info').innerHTML = info5;
document.getElementById('player-6-info').innerHTML = info6;
document.getElementById('player-7-info').innerHTML = info7;
document.getElementById('player-8-info').innerHTML = info8;
document.getElementById('player-9-info').innerHTML = info9;
document.getElementById('player-10-info').innerHTML = info10;

document.getElementById('player-1-bet').innerHTML = bet1;
document.getElementById('player-2-bet').innerHTML = bet2;
document.getElementById('player-3-bet').innerHTML = bet3;
document.getElementById('player-4-bet').innerHTML = bet4;
document.getElementById('player-5-bet').innerHTML = bet5;
document.getElementById('player-6-bet').innerHTML = bet6;
document.getElementById('player-7-bet').innerHTML = bet7;
document.getElementById('player-8-bet').innerHTML = bet8;
document.getElementById('player-9-bet').innerHTML = bet9;
document.getElementById('player-10-bet').innerHTML = bet10;

<?php
//if (($hand == '') || ($hand == 15))
if (true)
{
?>
document.getElementById('player-1-image').innerHTML = ava1;
document.getElementById('player-2-image').innerHTML = ava2;
document.getElementById('player-3-image').innerHTML = ava3;
document.getElementById('player-4-image').innerHTML = ava4;
document.getElementById('player-5-image').innerHTML = ava5;
document.getElementById('player-6-image').innerHTML = ava6;
document.getElementById('player-7-image').innerHTML = ava7;
document.getElementById('player-8-image').innerHTML = ava8;
document.getElementById('player-9-image').innerHTML = ava9;
document.getElementById('player-10-image').innerHTML = ava10;
<?php
}
?>
document.getElementById('card1').innerHTML = ccard1;
document.getElementById('card2').innerHTML = ccard2;
document.getElementById('card3').innerHTML = ccard3;
document.getElementById('card4').innerHTML = ccard4;
document.getElementById('card5').innerHTML = ccard5;

document.getElementById('player-1-cards').innerHTML = cards1;
document.getElementById('player-2-cards').innerHTML = cards2;
document.getElementById('player-3-cards').innerHTML = cards3;
document.getElementById('player-4-cards').innerHTML = cards4;
document.getElementById('player-5-cards').innerHTML = cards5;
document.getElementById('player-6-cards').innerHTML = cards6;
document.getElementById('player-7-cards').innerHTML = cards7;
document.getElementById('player-8-cards').innerHTML = cards8;
document.getElementById('player-9-cards').innerHTML = cards9;
document.getElementById('player-10-cards').innerHTML = cards10;

<?php if (! empty($sfx)) { ?>
var flashHtml = '<audio id="playaudio" autoplay><source src="themes/<?php echo THEME; ?>/sounds/<?php echo $sfx; ?>" type="audio/mpeg"></audio><script>function playaudio() { var x = document.getElementById("playaudio").autoplay; }</script>';
document.getElementById('flashObject').innerHTML = flashHtml;
<?php } ?>


//document.getElementById('dealertxt').innerHTML = dtxt;

document.forms['checkmov']['lastmove'].value = '<?php echo $lastmove; ?>';
document.forms['checkmov']['hand'].value = '<?php echo $hand; ?>';
document.forms['checkmov']['tomove'].value = '<?php echo $tomove; ?>';

<?php
if (!empty($button_display[0]))
{
?>
/* Runs when controls are visible to place bets, raise, fold. Add JS below. */
<?php
}
?>