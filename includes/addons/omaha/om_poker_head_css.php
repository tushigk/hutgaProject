<?php
function om_poker_head_css($value='')
{
	global $pagename, $gameID, $pdo;

	if ($pagename !== 'poker')
		return '';

	$check = $pdo->query("SELECT gamestyle FROM " . DB_POKER . " WHERE gameID = {$gameID}");

	if ($check->rowCount() !== 1)
		return '';

	$fetch = $check->fetch(PDO::FETCH_ASSOC);

	if ($fetch['gamestyle'] !== 'o')
		return '';

	return \ops_minify_html('<style type="text/css">
	.poker__user-cards-face-item:nth-child(2){
		top: 56px;
		left: 28px;
		z-index: 99999;
	}
	.poker__user-cards-face-item:nth-child(3){
		top: 50px;
		z-index: 9999;
	}
	.poker__user-cards-back img:nth-child(2){
		top: 40px;
		left: 28px;
		z-index: 99999;
	}
	.poker__user-cards-back img:nth-child(3){
		top: 35px;
		z-index: 9999;
	}
	</style>');
}
$addons->add_hook(array(
    'page'     => 'general',
    'location' => 'head_end',
    'function' => 'om_poker_head_css',
));
