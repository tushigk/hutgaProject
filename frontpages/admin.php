<?php
require('includes/inc_admin.php');

$addons->get_hooks(array(), array(

	'page'     => 'admin.php',
	'location'  => 'page_start'

));

include 'templates/header.php';

echo $addons->get_hooks(array(), array(

	'page'     => 'admin.php',
	'location'  => 'html_start'

));


ob_start();
if ( file_exists( 'admin/' . $adminview . '.php' ) )
{
	include 'admin/' . $adminview . '.php';
}
else
{
	$addonPage = $addons->get_hooks(
		array(
			'pagetag' => $adminview
		),
		array(
			'page'     => 'admin.php',
			'location'  => 'admin_page'
		)
	);

	if (empty($addonPage))
		include 'admin/tables.php';
	else
		echo $addonPage;
}
$content = ob_get_contents();
ob_end_clean();
$opsTheme->addVariable('content', $content);


echo $opsTheme->viewPage('admin');

echo $addons->get_hooks(array(), array(

	'page'     => 'admin.php',
	'location'  => 'html_end'

));

include 'templates/footer.php';
?>