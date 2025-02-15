<?php

function inc_lobby_omaha_logic ( $array = array() )
{
    return true;
}
$addons->add_hook(array(
    'page'     => 'includes/inc_lobby.php',
    'location' => 'omaha_logic',
    'function' => 'inc_lobby_omaha_logic',
));