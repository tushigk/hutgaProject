<?php
/* Styles --- */
$html = '';

if ($_GET['pagename'] === 'poker')
{
	$styles = array(
		$opsTheme->getVariable('theme.url') . '/css/libs.min.css',
		$opsTheme->getVariable('theme.url') . '/css/poker.min.css',
		$opsTheme->getVariable('theme.url') . '/css/modal.css',
		$opsTheme->getVariable('theme.url') . '/css/buttons.css',
		'//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/fontawesome.min.css',	
		'//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/solid.min.css',		
	);
}
else
{
	$styles = array(
		$opsTheme->getVariable('theme.url') . '/css/bootstrap.css',
		'//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/fontawesome.min.css',
		'//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/solid.min.css',
		$opsTheme->getVariable('theme.url') . '/css/custom.css',
		$opsTheme->getVariable('theme.url') . '/css/style.min.css',
	);
}

foreach ($styles as $style)
{
	$mtime = (substr($style, 0, 2) == '//') ? date('dmYH') : filemtime($style);
	$html .= '<link rel="stylesheet" href="' . $style . '?t=' . $mtime . '">';
}
$opsTheme->addVariable('styles', $html);
/* --- Styles */


/* Scripts --- */
$scriptsHtml = array(
	'header' => '',
	'footer' => '',
);
$scripts = array();
	
	if ($_GET['pagename'] === 'poker')
	{
		/* Header */
		$scripts['header'] = array(
			$opsTheme->getVariable('theme.url') . '/js/jquery-3.4.1.js'
		);
		/* Footer */
		$scripts['footer'] = array(
			$opsTheme->getVariable('theme.url') . '/js/libs.min.js',
			$opsTheme->getVariable('theme.url') . '/js/poker.js',
			$opsTheme->getVariable('theme.url') . '/js/bootstrap.js',
		);
	}
	else
	{
		/* Header */
		$scripts['header'] = array(
			$opsTheme->getVariable('theme.url') . '/js/jquery-3.4.1.js',
			$opsTheme->getVariable('theme.url') . '/js/bootstrap.js',
		);
		/* Footer */
		$scripts['footer'] = array();
	}

	/* Header */
	foreach ($scripts['header'] as $script)
	{
		$mtime = (substr($script, 0, 2) === '//') ? date('dmYH') : filemtime($script);
		$scriptsHtml['header'] .= '<script type="text/javascript" src="' . $script . '?t=' . $mtime . '"></script>';
	}
	/* Footer */
	foreach ($scripts['footer'] as $script)
	{
		$mtime = (substr($script, 0, 2) === '//') ? date('dmYH') : filemtime($script);
		$scriptsHtml['footer'] .= '<script type="text/javascript" src="' . $script . '?t=' . $mtime . '"></script>';
	}

$opsTheme->addVariable('scripts', array(
	'header' => $scriptsHtml['header'],
	'footer' => $scriptsHtml['footer'],
));
/* --- Scripts */
?>