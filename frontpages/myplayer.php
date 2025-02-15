<?php 
require('includes/inc_myplayer.php');

$addons->get_hooks(array(), array(

    'page'     => 'myplayer.php',
    'location'  => 'page_start'

));

$tabArray = array(
	'#stats'    => PLAYER_STATS,
	'#avatar'   => PLAYER_CHOOSE_AVATAR,
	'#password' => PLAYER_CHANGE_PWD
);
$tabArray = $addons->get_hooks(
	array(
		'content' => $tabArray
	),
	array(
		'page'     => 'myplayer.php',
		'location'  => 'tab_array'
	)
);


$tabActive = true;
$tabList   = '';
foreach ($tabArray as $href => $label)
{
	$opsTheme->addVariable('tab_link', $href);
	$opsTheme->addVariable('tab_label', $label);

	if ($tabActive)
	{
		$tabList  .= $opsTheme->viewPart('myplayer-tab-active');
		$tabActive = false;
	}
	else
	{
		$tabList .= $opsTheme->viewPart('myplayer-tab');
	}
}


$tabList .= $addons->get_hooks(array(), array(
	'page'     => 'myplayer.php',
	'location'  => 'add_tab'
));
$opsTheme->addVariable('tablist', $tabList);


if (isset($bad_msgs) && $bad_msgs != '')
{
	$opsTheme->addVariable('bad_msg_text', $bad_msgs);
	$opsTheme->addVariable('bad_message',  $opsTheme->viewPart('myplayer-bad-message'));
}


if ( isset($message) && $message != '' )
{
	$opsTheme->addVariable('msg_text', $message);
	$opsTheme->addVariable('message',  $opsTheme->viewPart('myplayer-message'));
}

$opsTheme->addVariable('user_name', $usr);
$opsTheme->addVariable('user_avatar', 'images/avatars/' . display_ava_profiles($usr));
$opsTheme->addVariable('user_winnings', money($winnings));

$opsTheme->addVariable('lastlogin_label', STATS_PLAYER_LOGIN);
$opsTheme->addVariable('lastlogin', $lastlogin);

$opsTheme->addVariable('player_stats_label', PLAYER_STATS);
$opsTheme->addVariable('player_stats_label', PLAYER_STATS);
$opsTheme->addVariable('stats_game_label', STATS_GAME);
$opsTheme->addVariable('stats_hand_label', STATS_HAND);

$opsTheme->addVariable('stats_gamesplayed_label', STATS_PLAYER_GAMES_PLAYED);
$opsTheme->addVariable('stats_gamesplayed', $gamesplayed);

$opsTheme->addVariable('stats_tournaments_played_label', STATS_PLAYER_TOURNAMENTS_PLAYED);
$opsTheme->addVariable('stats_tournaments_played', $tournamentsplayed);

$opsTheme->addVariable('stats_tournaments_won_label', STATS_PLAYER_TOURNAMENTS_WON);
$opsTheme->addVariable('stats_tournaments_won', $tournamentswon);

$opsTheme->addVariable('stats_tournaments_ratio_label', STATS_PLAYER_TOURNAMENTS_RATIO);
$opsTheme->addVariable('stats_tournaments_ratio', $tperc);

$opsTheme->addVariable('stats_hands_played_label', STATS_PLAYER_HANDS_PLAYED);
$opsTheme->addVariable('stats_hands_played', $handsplayed);

$opsTheme->addVariable('stats_hands_won_label', STATS_PLAYER_HANDS_WON);
$opsTheme->addVariable('stats_hands_won', $handswon);

$opsTheme->addVariable('stats_hand_ratio_label', STATS_PLAYER_HAND_RATIO);
$opsTheme->addVariable('stats_hand_ratio', $handsperc);

$opsTheme->addVariable('stats_joined_label', STATS_PLAYER_CREATED);
$opsTheme->addVariable('stats_joined', $created);

if (RENEW == 1 && $winnings == 0 && ( $gID == '' || $gID == 0 ) && $action != 'credit')
{
	$opsTheme->addVariable('player_broke_label', PLAYER_IS_BROKE);
	$opsTheme->addVariable('stats_player_credit_label', BUTTON_STATS_PLAYER_CREDIT);
	$opsTheme->addVariable('broke', $opsTheme->viewPart('myplayer-broke') );
	$opsTheme->addVariable('renew', $opsTheme->viewPart('myplayer-broke') );
}

$opsTheme->addVariable('stats_move_label', STATS_MOVE);
$opsTheme->addVariable('stats_fold_label', STATS_FOLD);

$opsTheme->addVariable('stats_fold_ratio_label', STATS_PLAYER_FOLD_RATIO);
$opsTheme->addVariable('stats_fold_ratio', $foldperc);

$opsTheme->addVariable('stats_check_ratio_label', STATS_PLAYER_CHECK_RATIO);
$opsTheme->addVariable('stats_check_ratio', $checkperc);

$opsTheme->addVariable('stats_call_ratio_label', STATS_PLAYER_CALL_RATIO);
$opsTheme->addVariable('stats_call_ratio', $callperc);

$opsTheme->addVariable('stats_allin_ratio_label', STATS_PLAYER_ALLIN_RATIO);
$opsTheme->addVariable('stats_allin_ratio', $allinperc);

$opsTheme->addVariable('stats_fold_preflop_label', STATS_PLAYER_FOLD_PREFLOP);
$opsTheme->addVariable('stats_fold_preflop', $foldpfperc);

$opsTheme->addVariable('stats_fold_flop_label', STATS_PLAYER_FOLD_FLOP);
$opsTheme->addVariable('stats_fold_flop', $foldfperc);

$opsTheme->addVariable('stats_fold_turn_label', STATS_PLAYER_FOLD_TURN);
$opsTheme->addVariable('stats_fold_turn', $foldtperc);

$opsTheme->addVariable('stats_fold_river_label', STATS_PLAYER_FOLD_RIVER);
$opsTheme->addVariable('stats_fold_river', $foldrperc);

$opsTheme->addVariable('player_choose_avatar_label', PLAYER_CHOOSE_AVATAR);
$opsTheme->addVariable('player_avatar_upload_label', BUTTON_UPLOAD);


if ( MEMMOD != 1 )
{
	$opsTheme->addVariable('player_change_password_label',  PLAYER_CHANGE_PWD);
	$opsTheme->addVariable('player_old_password_label',     STATS_PLAYER_OLD_PWD);
	$opsTheme->addVariable('player_new_password_label',     STATS_PLAYER_NEW_PWD);
	$opsTheme->addVariable('player_confirm_password_label', STATS_PLAYER_CONFIRM_PWD);
	$opsTheme->addVariable('player_password_char_limit',    STATS_PLAYER_PWD_CHAR_LIMIT);
	$opsTheme->addVariable('player_submit_button_label',    CREATE_PLAYER_SUBMIT_LABEL);
}

$opsTheme->addVariable('panel_stats', $opsTheme->viewPart('myplayer-panel-stats'));
$opsTheme->addVariable('panel_avatar', $opsTheme->viewPart('myplayer-panel-avatar'));
$opsTheme->addVariable('panel_password', $opsTheme->viewPart('myplayer-panel-password'));

$panels  = $opsTheme->viewPart('myplayer-panel-stats');
$panels .= $opsTheme->viewPart('myplayer-panel-avatar');
$panels .= $opsTheme->viewPart('myplayer-panel-password');
$panels .= $addons->get_hooks(array(), array(
	'page'     => 'myplayer.php',
	'location'  => 'add_tab_content'
));

$opsTheme->addVariable('panels', $panels);

include 'templates/header.php';

echo $addons->get_hooks(array(), array(
    'page'     => 'myplayer.php',
    'location'  => 'html_start'
));

echo $opsTheme->viewPage('myplayer');

echo $addons->get_hooks(array(), array(

    'page'     => 'myplayer.php',
    'location'  => 'PayPal_button'

));

echo $addons->get_hooks(array(), array(

    'page'     => 'myplayer.php',
    'location'  => 'bank_button'

));

echo $addons->get_hooks(array(), array(

    'page'     => 'myplayer.php',
    'location'  => 'html_end'

));

include 'templates/footer.php';
?>