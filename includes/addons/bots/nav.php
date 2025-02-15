<?php

function bots_header_nav_link ( $array = array() )
{
	return '<li class="nav-item"><a class="nav-link" href="admin.php?admin=bots">Bots</a></li>';
}


// Adding the hook to the sidebar
$addons->add_hook(array(

	'page'     => 'general',
	'location' => 'nav_start',
	'function' => 'bots_header_nav_link',

));
