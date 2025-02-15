<?php
$tableq   = $pdo->prepare("select * from ".DB_PLAYERS." where username = '{$_GET['username']}' ");
$tableq->execute();
$tabler   = $tableq->fetch(PDO::FETCH_ASSOC);

$tabler['lastlogin'] = date('d-m-Y', $tabler['lastlogin']);
$tabler['datecreated'] = date('d-m-Y', $tabler['datecreated']);

$opsTheme->addVariable('member', $tabler);

$inputs = $opsTheme->viewPart('admin-edit-member-inputs');
$inputs .= $addons->get_hooks(array(),
    array(
        'page'     => 'admin/edit-member.php',
        'location'  => 'inputs'
    )
);
$opsTheme->addVariable('inputs', $inputs);

$getstats = $pdo->prepare("select * from ".DB_STATS." where player = '{$_GET['username']}' ");
$getstats->execute();
$usestats = $getstats->fetch(PDO::FETCH_ASSOC);

$usestats['money'] = money($usestats['winpot']);

$opsTheme->addVariable('memberstats', $usestats);

$user_ip  = $tabler['ipaddress'];
$geo      = json_decode(file_get_contents("http://extreme-ip-lookup.com/json/$user_ip"));
$country  = (isset($geo->country)) ? $geo->country : '';
$city     = (isset($geo->city))    ? $geo->city : '';

$opsTheme->addVariable('country', $country);
$opsTheme->addVariable('city',    $city);

echo $opsTheme->viewPage('admin-edit-member');
?>