<?php 
$addons->get_hooks(array(), array(

	'page'     => 'rules.php',
	'location'  => 'page_start'

));

$opsTheme->addVariable('rules_label', RULES);

include 'templates/header.php';

echo $addons->get_hooks(array(), array(

	'page'     => 'rules.php',
	'location'  => 'html_start'

));

echo $opsTheme->viewPage('rules');

echo $addons->get_hooks(array(), array(

	'page'     => 'rules.php',
	'location'  => 'html_end'

));

include 'templates/footer.php';
?>