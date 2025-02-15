<?php
/**/
function botgod_update_bot_timetag ( $array = array() )
{
    global $pdo;
	$pdo->exec("UPDATE " . DB_PLAYERS . " SET timetag = " . (time() + 1) . " WHERE isbot = 1");
}
$addons->add_hook(array(

	'page'     => 'includes/gen_inc.php',
	'location' => 'start',
	'function' => 'botgod_update_bot_timetag',

));


/**/
function botgod_update_bot_timetag_sec ( $array = array() )
{
    global $pdo;
	$pdo->exec("UPDATE " . DB_PLAYERS . " SET timetag = " . (time() + 1) . " WHERE isbot = 1");
}
$addons->add_hook(array(

	'page'     => 'includes/sec_inc.php',
	'location' => 'start',
	'function' => 'botgod_update_bot_timetag_sec',

));
