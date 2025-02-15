<?php
$cardNumbers = array('A', 'K', 'Q', 'J');
for ($i = 2; $i < 11; $i++)
	$cardNumbers[] = $i;

$tableCard  = array();
$playerCard = array();
foreach ($cardNumbers as $cNum)
{
	foreach (array('diamond', 'spade', 'heart', 'club') as $cType)
	{
		$cTypeShort   = substr($cType, 0, 1);
		$cTypeShortUc = strtoupper(substr($cType, 0, 1));
		$opsTheme->addVariable('card', array(
			'type'          => $cType,
			'type_uc'       => strtoupper($cType),
			'type_short'    => $cTypeShort,
			'type_short_uc' => $cTypeShortUc,
			'number'        => $cNum,
			'number_lc'     => strtolower($cNum),
		));

		$playerCard[$cNum . $cTypeShortUc]          = $opsTheme->viewPart('poker-card-visible');
		$playerCard[$cNum . $cTypeShortUc . '.png'] = $opsTheme->viewPart('poker-card-visible');

		$tableCard[$cNum . $cTypeShortUc]          = $opsTheme->viewPart('poker-card-table-visible');
		$tableCard[$cNum . $cTypeShortUc . '.png'] = $opsTheme->viewPart('poker-card-table-visible');
	}
}

if ($game_style == GAME_TEXAS)
{
    $playerCard['facedown']     = $opsTheme->viewPart('poker-card-hidden');
    $playerCard['facedown.png'] = $opsTheme->viewPart('poker-card-hidden');
}
else
{
    $playerCard['facedown']     = $opsTheme->viewPart('poker-card-hidden') . $opsTheme->viewPart('poker-card-hidden');
    $playerCard['facedown.png'] = $opsTheme->viewPart('poker-card-hidden') . $opsTheme->viewPart('poker-card-hidden');
}

$tableCard['facedown']     = $opsTheme->viewPart('poker-card-table-hidden');
$tableCard['facedown.png'] = $opsTheme->viewPart('poker-card-table-hidden');