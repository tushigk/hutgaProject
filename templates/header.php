<?php

$title = (isset($title)) ? $title : TITLE;
$opsTheme->addVariable('title', $title);
$opsTheme->addVariable('site_title', TITLE);
$opsTheme->addVariable('menu_lobby', MENU_LOBBY);


$opsTheme->addVariable('day', date('d'));
$opsTheme->addVariable('month', date('m'));
$opsTheme->addVariable('year', date('Y'));

include 'sidebar.php';


if ($ADMIN == true)
	$opsTheme->addVariable('admin_nav', $opsTheme->viewPart('admin-nav'));


if ($ADMIN == true)
{
	$updateBadge   = '<span class="badge badge-pill badge-primary">!</span>';

	$addonNavLabel = ADMIN_MANAGE_ADDONS;
	if (ADDONUPDATEA !== '0')
		$addonNavLabel .= $updateBadge;

	$updatesNavLabel = 'Updates';
	if (UPDATEALERT !== '0')
		$updatesNavLabel .= $updateBadge;

	$logDropdowns = array();
	$navArray = array(
		'admin.php?admin=addons'    => $addonNavLabel,
		'admin.php?admin=tables'    => ADMIN_MANAGE_TABLES,
		'admin.php?admin=members'   => ADMIN_MANAGE_MEMBERS,
		'admin.php?admin=themes'    => ADMIN_MANAGE_THEMES,		
		'admin.php?admin=styles'    => ADMIN_MANAGE_STYLES,
		'log'                       => array(
			'label' => 'Logs',
			'dropdowns' => $logDropdowns
		),
		'admin.php?admin=settings'  => ADMIN_MANAGE_SETTINGS,
		'admin.php?admin=updates'   => $updatesNavLabel,
	);
	$logDropdowns = $addons->get_hooks(
		array(
			'content' => $logDropdowns
		),
		array(
			'page'        => 'general',
			'location'    => 'nav_log',
			'merge_array' => true
		)
	);
	$navArray['log']['dropdowns'] = $logDropdowns;
	$navArray = $addons->get_hooks(
		array(
			'content' => $navArray
		),
		array(
			'page'        => 'general',
			'location'    => 'nav_array'
		)
	);

	if (count($navArray['log']['dropdowns']) == 0)
		unset($navArray['log']);

	$navContent = $addons->get_hooks(
		array(
			'content' => ''
		),
		array(
			'page'     => 'general',
			'location'  => 'nav_start'
		)
	);

	foreach ($navArray as $navUrl => $navLabel)
	{
		$opsTheme->addVariable('nav_url', $navUrl);

		if (is_array($navLabel))
		{
			$dropdownArray = $navLabel;
			$dropdowns     = '';

			foreach ($dropdownArray['dropdowns'] as $ddUrl => $ddLabel)
			{
				$opsTheme->addVariable('dropdown', array(
					'url'   => $ddUrl,
					'label' => $ddLabel
				));
				$dropdowns .= $opsTheme->viewPart('nav-each-dropdown-each');
			}

			$opsTheme->addVariable('nav', array(
				'url'       => $navUrl,
				'label'     => $dropdownArray['label'],
				'dropdowns' => $dropdowns
			));
			$opsTheme->addVariable('nav_label', $dropdownArray['label']);

			$navContent .= $opsTheme->viewPart('nav-each-dropdown');
		}
		else
		{
			$opsTheme->addVariable('nav', array(
				'url'   => $navUrl,
				'label' => $navLabel
			));

			$opsTheme->addVariable('nav_label', $navLabel);
			$navContent .= $opsTheme->viewPart('nav-each');
		}
	}

	$navContent = $addons->get_hooks(
		array(
			'content' => $navContent
		),
		array(
			'page'     => 'general',
			'location'  => 'nav_content'
		)
	);
	$navContent .= $addons->get_hooks(
		array(
			'content' => ''
		),
		array(
			'page'     => 'general',
			'location'  => 'nav_end'
		)
	);

	$opsTheme->addVariable('nav_content', $navContent);
}

/**/
$head_start_tag_addons = $addons->get_hooks(array(), array(
	'page'     => 'general',
	'location'  => 'head_start'
));
$head_end_tag_addons = $addons->get_hooks(array(), array(
	'page'     => 'general',
	'location'  => 'head_end'
));
$body_start_tag_addons = $addons->get_hooks(array(), array(
	'page'     => 'general',
	'location'  => 'body_start'
));
/**/

$themeInit = 'themes/' . THEME . '/init.php';
if (file_exists($themeInit))
	include $themeInit;

$hpc = $opsTheme->viewPage('header');

$jsPokerScript = '<script src="js/poker.php?t=' . date('YmdHi') . '"></script><script type="text/javascript">function open_game(gameId){window.location.href = "lobby.php?gameID=" + gameId;}</script>';

$hpc = str_ireplace('<head>', "<head>{$head_start_tag_addons}", $hpc);
$hpc = str_ireplace('</head>', "{$jsPokerScript}{$head_end_tag_addons}</head>", $hpc);
$hpc = str_ireplace('<body>', "<body>{$body_start_tag_addons}", $hpc);

echo $hpc;
?>