<?php

if ($valid == false)
{
    header('Location: login.php');
}

$lim  = 99999999999999999999;
$posi = 1;
$staq = $pdo->prepare("select " . DB_STATS . ".winpot, " . DB_PLAYERS . ".banned from " . DB_STATS . ", " . DB_PLAYERS . " where " . DB_PLAYERS . ".username = " . DB_STATS . ".player and " . DB_PLAYERS . ".banned = 0 order by " . DB_STATS . ".winpot desc"); $staq->execute();

while ($star = $staq->fetch(PDO::FETCH_ASSOC))
{
    if ($star['winpot'] < $lim)
    {
        $lastdig    = substr($posi, -1, 1);
        $prelastdig = ((strlen($posi) < 2) ? '' : substr($posi, -2, 1));
        $rank       = $posi . 'th';

        if ($lastdig == 1 && $prelastdig != 1)
        {
            $rank = $posi . 'st';
        }
        elseif ($lastdig == 2)
        {
            $rank = $posi . 'nd';
        }
        elseif ($lastdig == 3)
        {
            $rank = $posi . 'rd';
        }
        elseif ($star['winpot'] < 10500)
        {
            $rank = 'unranked';
        }

        $lim    = $star['winpot'];
        $result = $pdo->exec("update " . DB_STATS . " set rank = '$rank' where winpot = " . $star['winpot']);
        $posi++;
    }
}

function display_ava_rankings($usr)
{
    global $pdo;
    $time   = time();
    $usrq   = $pdo->prepare("select avatar from " . DB_PLAYERS . " where username = '$usr'"); $usrq->execute();
    $usrr   = $usrq->fetch(PDO::FETCH_ASSOC);
    $avatar = '<img class="img-circle img-responsive" src="images/avatars/' . $usrr['avatar'] . '?x=' . $time . '" border="0">';
    return $avatar;
}
?>