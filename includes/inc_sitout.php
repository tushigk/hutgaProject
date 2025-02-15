<?php
$time     = time();
$tq       = $pdo->prepare("select waitimer from " . DB_PLAYERS . " where username = '$plyrname'"); $tq->execute();
$tr       = $tq->fetch(PDO::FETCH_ASSOC);
$waitimer = $tr['waitimer'];

if (($waitimer - 1) <= $time)
{
    header('Location: lobby.php');
    die();
}

$start = $waitimer - $time;
?>