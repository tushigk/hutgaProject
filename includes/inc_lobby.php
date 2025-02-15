<?php

if ($valid == false)
{
	header('Location: login.php');
}

$is_omaha = false;
$is_omaha = $addons->get_hooks(
    array(
        'state' => $is_omaha,
        'content' => $is_omaha,
    ),
    array(
        'page'     => 'includes/inc_lobby.php',
        'location'  => 'omaha_logic'
    ));

$gameID = (isset($_GET['gameID']))  ? addslashes($_GET['gameID'])  : '';
$gameID = (isset($_POST['gameID'])) ? addslashes($_POST['gameID']) : $gameID;

if ($gameID != '')
{
    $gq = $pdo->prepare("SELECT * FROM " . DB_POKER . " WHERE gameID = ?");
    $gq->execute(array($gameID));

    if ($gq->rowCount() == 1)
    {
        $tabler = $gq->fetch(PDO::FETCH_ASSOC);

        if ($tabler['gamestyle'] == 'o' && $is_omaha == false)
        {
            echo "<script type='text/javascript'>alert('Please install Omaha hold em add-on.');</script>";
            return;
        }

        $enterGame = $addons->get_hooks(
            array(
                'content' => true
            ),
            array(
                'page'     => 'includes/inc_lobby.php',
                'location'  => 'enter_game_logic'
            )
        );
        if ($enterGame)
        {
            $result = $pdo->exec("update " . DB_PLAYERS . " set vID = " . $gameID . " where username = '" . $plyrname . "' ");
            header('Location: poker.php');
            die();
        }
    }

    header('Location: lobby.php');
    die();
}
?>