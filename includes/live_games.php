<?php
require('gen_inc.php');

header('Content-Type: text/javascript');

// get list of lobbies live

$elementId = 'gamelist';

if (isset($_GET['l']) && strlen($_GET['l']) > 0)
	$elementId = preg_replace('/[^A-Za-z0-9_-]/i', '', $_GET['l']);

if ($valid == false)
	die();

$sql = $addons->get_hooks(
    array(
        'content' => "SELECT * FROM " . DB_POKER . " ORDER BY tablename ASC"
    ),
    array(
        'page'     => 'includes/live_games.php',
        'location'  => 'tableq_sql'
    )
);
$tableq = $pdo->query($sql);
$table_html = '';
$rows = '';

$games = array();
if (! function_exists('add_game_table_column'))
{
	function add_game_table_column($label, $value)
	{
		global $games, $gamID;
		$games[$gamID][$label] = $value;
	}
}

while ($tabler = $tableq->fetch(PDO::FETCH_ASSOC))
{
	$i = 1;
	$x = 0;
	$time = time();
	$ktimer = DISCONNECT;
	$timekick = $time - $ktimer;
	$gamID = $tabler['gameID'];

	$addons->get_hooks(

	    array(),

	    array(
	        'page'     => 'includes/live_games.php',
	        'location'  => 'after_gameid_var'
	    )
	);

	while ($i < 11)
	{
		if (strlen($tabler['p' . $i . 'name']) != '')
		{
			$usr     = $tabler['p' . $i . 'name'];
			$pot     = $tabler['p' . $i . 'pot'];
			$ttq_sql = $addons->get_hooks(
                array(
                    'content' => "SELECT gID, timetag FROM " . DB_PLAYERS . " WHERE username = '$usr'"
                ),
                array(
                    'page'     => 'includes/live_games.php',
                    'location'  => 'ttq_sql'
                )
            );
			$ttq     = $pdo->prepare($ttq_sql);
			$ttq->execute();
			$ttr     = $ttq->fetch(PDO::FETCH_ASSOC);

			$tkick = false;
			if ($ttr['timetag'] < $timekick) $tkick = true;

			$tkick = $addons->get_hooks(
                array(
                    'state' => $tkick,
                    'content' => $tkick,
                ),
                array(
                    'page'     => 'includes/live_games.php',
                    'location'  => 'player_tkick'
                )
            );

			if ($tkick || ($ttr['gID'] != $gamID))
			{
				$result = $pdo->exec("update " . DB_POKER . " set p" . $i . "name = '', p" . $i . "bet = '', p" . $i . "pot = '' , lastmove = " . ($time + 1) . " where gameID = " . $gamID);
				$result = $pdo->exec("update " . DB_STATS . " set winpot = winpot + " . $pot . " where player  = '" . $usr . "'  ");

				$result3_sql = $addons->get_hooks(
                    array(
                        'content' => "UPDATE " . DB_PLAYERS . " SET gID = 0 WHERE username = '$usr'"
                    ),
                    array(
                        'page'     => 'includes/live_games.php',
                        'location'  => 'result3_sql'
                    )
                );
				$result3     = $pdo->exec($result3_sql);
			}

			$x++;
		}

		$i++;
	}

	$tablename       = $tabler['tablename'];
	$min             = money_small($tabler['tablelow']);
	$tablelimit      = $tabler['tablelimit'];
	$max             = money_small($tablelimit);
	$gID             = $tabler['gameID'];
	$sbamount		 = $tabler['sbamount'];
	$bbamount		 = $tabler['bbamount'];	
	$tablemultiplier = 1;

	if ($tablelimit == 25000) $tablemultiplier = 2;
	if ($tablelimit == 50000) $tablemultiplier = 4;
	if ($tablelimit == 100000) $tablemultiplier = 8;
	if ($tablelimit == 250000) $tablemultiplier = 20;
	if ($tablelimit == 500000) $tablemultiplier = 40;
	if ($tablelimit == 1000000) $tablemultiplier = 80;

	if($sbamount != 0) {
	$SB = money_small($tabler['sbamount']);
	$BB = money_small($tabler['bbamount']);
	} else {
	if ($tabler['tabletype'] == 't')
		{
		$BB = money_small(50 * $tablemultiplier) . '-' . money_small(50 * $tablemultiplier * 9);
		$SB = money_small(25 * $tablemultiplier) . '-' . money_small(25 * $tablemultiplier * 9);
		}
	  else
		{
		$BB = money_small(200 * $tablemultiplier);
		$SB = money_small(100 * $tablemultiplier);
		}
	}

	$NEW_GAME     = addslashes(NEW_GAME);
	$PLAYING      = addslashes(PLAYING);
	$tablestatus  = (($tabler['hand'] == '') ? $NEW_GAME : $PLAYING);
	$TOURNAMENT   = addslashes(TOURNAMENT);
	$SITNGO       = addslashes(SITNGO);
	$tabletype    = ($tabler['tabletype'] == 't') ? $TOURNAMENT : $SITNGO;
	$buyin        = ($tabler['tabletype'] == 't') ? $max : $min . '/' . $max;
	$gamestyle    = '';

    if ($tabler['gamestyle'] != null)
    {
        if ($tabler['gamestyle'] == 't')
        {
        	$gamestyle = GAME_TEXAS;
        }
        else
        {
        	$gamestyle = GAME_OMAHA;
        }
    }
    else
    {
    	$gamestyle = GAME_TEXAS . 'b';
    }

    
    $tablename = $addons->get_hooks(array('content' => $tablename), array(
		'page'     => 'includes/live_games.php',
		'location'  => 'game_table_name'
	));
	$games[$gamID][TABLE_HEADING_NAME] = $tablename;

	$tableplayers = $addons->get_hooks(
	    array(
	    	'content' => $x . '/10'
	    ),
	    array(
	        'page'     => 'includes/live_games.php',
	        'location'  => 'tableplayers_var'
	    )
	);
	$games[$gamID][TABLE_HEADING_PLAYERS] = $tableplayers;

	$tabletype = $addons->get_hooks(array('content' => $tabletype), array(
		'page'     => 'includes/live_games.php',
		'location'  => 'game_table_type'
	));
	$games[$gamID][TABLE_HEADING_TYPE] = $tabletype;

	$gamestyle = $addons->get_hooks(array('content' => $gamestyle), array(
		'page'     => 'includes/live_games.php',
		'location'  => 'game_style'
	));
	$games[$gamID][ADMIN_TABLES_GAME] = $gamestyle;

	$buyin = $addons->get_hooks(array('content' => $buyin), array(
		'page'     => 'includes/live_games.php',
		'location'  => 'game_table_buyin'
	));
	$games[$gamID][TABLE_HEADING_BUYIN] = $buyin;

	$SB = $addons->get_hooks(array('content' => $SB), array(
		'page'     => 'includes/live_games.php',
		'location'  => 'game_table_small_blinds'
	));
	$games[$gamID][TABLE_HEADING_SMALL_BLINDS] = $SB;

	$BB = $addons->get_hooks(array('content' => $BB), array(
		'page'     => 'includes/live_games.php',
		'location'  => 'game_table_big_blinds'
	));
	$games[$gamID][TABLE_HEADING_BIG_BLINDS] = $BB;

	$tablestatus = $addons->get_hooks(array('content' => $tablestatus), array(
		'page'     => 'includes/live_games.php',
		'location'  => 'game_table_status'
	));
	$games[$gamID][TABLE_HEADING_STATUS] = $tablestatus;

	$addons->get_hooks(array(), array(
		'page'     => 'includes/live_games.php',
		'location'  => 'each_game_column'
	));


	$opsTheme->addVariable('gameID',       $gID);
	$opsTheme->addVariable('tablename',    $tablename);
	$opsTheme->addVariable('tableplayers', $tableplayers);
	$opsTheme->addVariable('tabletype',    $tabletype);
	$opsTheme->addVariable('gamestyle',    $gamestyle);
	$opsTheme->addVariable('buyin',        $buyin);
	$opsTheme->addVariable('SB',           $SB);
	$opsTheme->addVariable('BB',           $BB);
	$opsTheme->addVariable('tablestatus',  $tablestatus);

	$rows .= $opsTheme->viewPart('live-games-each');
}

$opsTheme->addVariable('rows', $rows);

/* --- Table Header */
$gameHead  = '';

foreach (array_keys($games[$gamID]) as $label)
{
	$opsTheme->addVariable('text', $label);
	$gameHead .= $opsTheme->viewPart('gametable-head-col');
}
/* Table Header --- */

/* --- Table Rows */
$gameRows = '';

foreach ($games as $rowId => $row)
{
	$columns = '';

	foreach ($row as $col)
	{
		$opsTheme->addVariable('text', $col);
		$columns .= $opsTheme->viewPart('gametable-each-col');
	}

	$rowArray = $addons->get_hooks(
		array(
			'content' => array(
				'id'      => $rowId,
				'onclick' => "open_game({$rowId});",
				'columns' => $columns
			)
		),
		array(
			'page'     => 'includes/live_games.php',
			'location'  => 'each_game_row_info'
		)
	);
	$opsTheme->addVariable('row', $rowArray);

	$gameRows .= $opsTheme->viewPart('gametable-each-row');
}
/* Table Rows --- */

$opsTheme->addVariable('game', array(
	'head' => $gameHead,
	'rows' => $gameRows
));
?>

gameslist = '<?php echo $opsTheme->viewPage('live-games'); ?>';
document.getElementById('<?= $elementId; ?>').innerHTML = gameslist;