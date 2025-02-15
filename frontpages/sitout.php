<?php
require('includes/inc_sitout.php');

$addons->get_hooks(array(), array(

	'page'     => 'sitout.php',
	'location'  => 'page_start'

));

include 'includes/scores.php';
$opsTheme->addVariable('scoreboard', $scoreboard);

$opsTheme->addVariable('sitout_label',       SITOUT);
$opsTheme->addVariable('sitout_timer_label', SITOUT_TIMER);
$opsTheme->addVariable('sitout_start',       $start);

include 'templates/header.php';

echo $addons->get_hooks(array(), array(
	'page'     => 'sitout.php',
	'location'  => 'html_start'
));

echo $opsTheme->viewPage('sitout');

echo $addons->get_hooks(array(), array(
	'page'     => 'sitout.php',
	'location'  => 'html_end'
));
include 'templates/footer.php';
?>