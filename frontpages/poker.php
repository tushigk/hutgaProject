<?php
require('includes/inc_poker.php');

$addons->get_hooks(array(), array(

    'page'     => 'poker.php',
    'location'  => 'page_start'

));

if (! isset($tablestyle)) $tablestyle = 'table_green';

// Table type
$ttype = ($tabletype != 't')  ? SITNGO : TOURNAMENT; 

// Blinds amount
if($sbamount != 0) { 
$getblinds = money_small($sbamount) . '/' . money_small($bbamount);
} else {

if ($tabletype != 't')
{
    $blindmultiplier = 4;
}

$tablemultiplier = 1;

if ($tablelimit == 25000)
{
    $tablemultiplier = 2;
}

if ($tablelimit == 50000)
{
    $tablemultiplier = 4;
}

if ($tablelimit == 100000)
{
    $tablemultiplier = 8;
}

if ($tablelimit == 250000)
{
    $tablemultiplier = 20;
}

if ($tablelimit == 500000)
{
    $tablemultiplier = 40;
}

if ($tablelimit == 1000000)
{
    $tablemultiplier = 80;
}

if ($tabletype == 't')
{
	$SB = money_small(25 * $tablemultiplier) . '-' . money_small(25 * $tablemultiplier * 9);
	$BB = money_small(50 * $tablemultiplier) . '-' . money_small(50 * $tablemultiplier * 9);
}
else
{
	$SB = money_small(100 * $tablemultiplier);	
	$BB = money_small(200 * $tablemultiplier);
}

$getblinds = $SB . '/' . $BB;
}

// Buy in amount
$buyin = (($tabletype == 't') ? money_small($tablelimit) : money_small($min) . '/' . money_small($tablelimit));	


// Page title
$title  = stripslashes($tablename) . ' - ';
$title .= $ttype.' - '.$buyin;
$title .= ($tabletype == 't') ? ' + ' . money_small( $tr['rake'] ) : '';


$opsTheme->addVariable('tablename', stripslashes($tablename));
$opsTheme->addVariable('tabletype', $ttype);
$opsTheme->addVariable('tableid', stripslashes($tableid));
$opsTheme->addVariable('tablestyle', $tablestyle);
$opsTheme->addVariable('blinds', $getblinds);
$opsTheme->addVariable('buyinamount', $buyin);

$seatHtml = '';
for ($i = 1; $i < 11; $i++)
{
    $opsTheme->addVariable('seat_number', $i);

    $seatCircle = poker_seat_circle_html($i);
    $opsTheme->addVariable('seat_circle', $seatCircle);

    $seatHtml .= $addons->get_hooks(
        array(
            'index'   => $i,
            'content' => $opsTheme->viewPart('poker-player-each'),
        ),

        array(
            'page'      => 'poker.php',
            'location'  => 'each_seat',
        )
    );
}
$opsTheme->addVariable('players', $seatHtml);

//$opsTheme->addVariable('initial_raise', 50);
// $opsTheme->addVariable('initial_raise_label', transfer_from(50));


$dealerchat = '';
$cq    = $pdo->prepare("SELECT * FROM " . DB_LIVECHAT . " WHERE gameID = " . $tableid);
$cq->execute();
$cr    = $cq->fetch(PDO::FETCH_ASSOC);
$time  = time();

if ($cr['updatescreen'] < $time)
{
    $i    = 1;
    $chat = '';

    while ($i < 6)
    {
        $cThis = $cr['c' . $i];

        if (!empty($cThis))
        {
            $i++;
            continue;
        }

        $lMsg = "<log>{$cThis}</log>";
        $lXml = simplexml_load_string($lMsg);

        $opsTheme->addVariable('chatter', array(
            'id'     => (int) $lXml->user->id,
            'name'   => (string) $lXml->user->name,
            'avatar' => (string) $lXml->user->avatar,
        ));
        $opsTheme->addVariable('message', (string) $lXml->message);

        $chat .= $opsTheme->viewPart('poker-log-message');
        $i++;
    }

    $dealerchat .= $chat;
}
$opsTheme->addVariable('dealerchat', $dealerchat);


$userchat = '';
$ucq      = $pdo->prepare("SELECT * FROM " . DB_USERCHAT . " WHERE gameID = " . $tableid);
$ucq->execute();
$ucr      = $ucq->fetch(PDO::FETCH_ASSOC);
$time     = time();

if ($ucr['updatescreen'] < $time)
{
    $i    = 1;
    $chat = '';

    while ($i < 6)
    {
        $cThis = $ucr['c' . $i];

        if (empty($cThis))
        {
            $i++;
            continue;
        }

        $uMsg = "<chat>{$cThis}</chat>";
        $uXml = simplexml_load_string($uMsg);

        $opsTheme->addVariable('chatter', array(
            'id'     => (int) $uXml->user->id,
            'name'   => (string) $uXml->user->name,
            'avatar' => (string) $uXml->user->avatar,
        ));
        $opsTheme->addVariable('message', (string) $uXml->message);

        if ($uXml->user->name == $plyrname)
            $chat .= $opsTheme->viewPart('poker-chat-message-me');
        else
            $chat .= $opsTheme->viewPart('poker-chat-message-other');
        
        $i++;
    }

    $userchat .= $chat;
}
$opsTheme->addVariable('userchat', $userchat);


include 'templates/header.php';

echo $addons->get_hooks(array(), array(

    'page'     => 'poker.php',
    'location'  => 'html_start'

));

echo $opsTheme->viewPage('poker');

echo $addons->get_hooks(array(), array(

    'page'     => 'poker.php',
    'location'  => 'html_end'

));

include 'templates/footer.php';
?>