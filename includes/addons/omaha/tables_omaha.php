<?php

function tables_omaha_logica ( $array = array() )
{
    $content = $array['content'];
    $content[] = array('value' => 'o', 'label' => GAME_OMAHA);
    return $content;
}
$addons->add_hook(array(

    'page'     => 'admin/tables.php',
    'location' => 'admin_table_gamestyle_logic',
    'function' => 'tables_omaha_logica',

));