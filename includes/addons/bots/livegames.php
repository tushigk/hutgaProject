<?php
////
function livegames_ttq_sql ( $array = array() )
{
	global $usr;
	return "SELECT gID, timetag, isbot FROM " . DB_PLAYERS . " WHERE username = '$usr'";
}

$addons->add_hook(array(

	'page'     => 'includes/live_games.php',
	'location' => 'ttq_sql',
	'function' => 'livegames_ttq_sql',

));

////
function livegames_result3_sql ( $array = array() )
{
	global $usr, $ttr;

	if ($ttr['isbot'] == 0) return "UPDATE " . DB_PLAYERS . " SET gID = 0 WHERE username = '$usr'";

	return false;
}

$addons->add_hook(array(

	'page'     => 'includes/live_games.php',
	'location' => 'result3_sql',
	'function' => 'livegames_result3_sql',

));

////
function livegames_player_tkick ( $array = array() )
{
	global $ttr, $timekick;

	if ($ttr['isbot'] == 1)
	{
		return false;
	}

	return $array['state'];
}

$addons->add_hook(array(

	'page'     => 'includes/live_games.php',
	'location' => 'player_tkick',
	'function' => 'livegames_player_tkick',

));