<?php
$footer_content = $addons->get_hooks(
	array(
		'content' => $opsTheme->viewPart('footer-content')
	),
	array(
		'page'     => 'general',
		'location'  => 'footer_content'
	)
);
$opsTheme->addVariable('footer_content', $footer_content);

/**/
$body_end_tag_addons = $addons->get_hooks(array(), array(
	'page'     => 'general',
	'location'  => 'body_end'
));
/**/

$fpc = $opsTheme->viewPage('footer');
$fpc = str_ireplace('</body>', "{$body_end_tag_addons}</body>", $fpc);
echo $fpc;
?>