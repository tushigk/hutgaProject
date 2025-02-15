<?php
// addon hook for before sidebar
$sidebar = $addons->get_hooks(array(), array(
	'page'     => 'general',
	'location'  => 'leftbar_before'
));

$sidebarArray = array('index.php' => MENU_HOME);

if ($valid == false && MEMMOD == 0)
	$sidebarArray['login.php'] = MENU_LOGIN;

if ($valid == false)
	$sidebarArray['create.php'] = MENU_CREATE;

if ($valid == true)
{
	$sidebarArray['lobby.php'] = MENU_LOBBY;
	// $sidebarArray['rankings.php'] = MENU_RANKINGS;
	$sidebarArray['myplayer.php'] = MENU_MYPLAYER;
}

$sidebarArray = $addons->get_hooks(
	array(
		'content' => $sidebarArray
	),
	array(
		'page'     => 'general',
		'location'  => 'leftbar_array'
	)
);

// $sidebarArray['rules.php'] = 'Poker Rules';	
// $sidebarArray['faq.php']   = 'FAQ';

if ($ADMIN == true)
	$sidebarArray['admin.php'] = 'Admin';

if ($valid == true)
	$sidebarArray['logout.php'] = 'Log Out';


$sidebarContent = '';
foreach ($sidebarArray as $sidebarUrl => $sidebarLabel)
{
	$opsTheme->addVariable('sb_menu_url',   $sidebarUrl);
	$opsTheme->addVariable('sb_menu_label', $sidebarLabel);

	$sidebarContent .= $opsTheme->viewPart('sidebar-each');
}
// addon hook for sidebar content
$sidebarContent = $addons->get_hooks(
	array(
		'content' => $sidebarContent
	),
	array(
		'page'     => 'general',
		'location'  => 'leftbar_content'
	)
);
$opsTheme->addVariable('sidebar_content',   $sidebarContent);

$sidebar .= $opsTheme->viewPage('sidebar');

// addon hook for after sidebar
$sidebar .= $addons->get_hooks(array(), array(
	'page'     => 'general',
	'location'  => 'leftbar_after'
));

$opsTheme->addVariable('sidebar', $sidebar);
?>