<?php
$i          = 1;
$scoreboard = '';
$staq       = $pdo->prepare("select player, winpot from " . DB_STATS . " order by winpot desc");
$staq->execute();

while (($star = $staq->fetch(PDO::FETCH_ASSOC)) && $i < 9)
{
    if ($i == 1)
        $prefix = $i . PLACE_POSI_1;
    elseif ($i == 2)
        $prefix = $i . PLACE_POSI_2;
    elseif ($i == 3)
        $prefix = $i . PLACE_POSI_3;
    else
        $prefix = $i . PLACE_POSI;

    $name	= $star['player'];
    $win  	= money_small($star['winpot']);
    $chips  = $star['winpot'];
    $ava 	= display_ava_profiles($name);

    $opsTheme->addVariable('user_avatar', $ava);
    $opsTheme->addVariable('user_place',  $prefix);
    $opsTheme->addVariable('place_text',  PLACE);
    $opsTheme->addVariable('user_name',   $name);
    $opsTheme->addVariable('user_win',    $win);
    $opsTheme->addVariable('user_chips',  $chips);

    $scoreboard .= $opsTheme->viewPart('scoreboard-each');
    $i++;
}
