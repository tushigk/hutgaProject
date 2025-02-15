<?php

function edit_table_omaha_logic ( $array = array() )
{
    $content = $array['content'];
    $content[] = array('value' => 'o', 'label' => GAME_OMAHA);
    return $content;

    return $omaha_html;
}
$addons->add_hook(array(

    'page'     => 'admin/edit-table.php',
    'location' => 'admin_table_gamestyle_logic',
    'function' => 'edit_table_omaha_logic',

));