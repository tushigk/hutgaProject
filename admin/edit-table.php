<?php
$tableq = $pdo->prepare("SELECT * FROM " . DB_POKER . " WHERE gameID = '" . $_GET['table'] . "'"); $tableq->execute();
$tabler = array(
    'gameID' => '',
    'tabletype' => '',
    'tablename' => '',
    'tablelow' => '',
    'tablelimit' => '',
    'sbamount' => '',
    'bbamount' => '',    
    'tablestyle' => '',
    'gamestyle' => '',
);

if ($tableq->rowCount() > 0)
{
    $opsTheme->addVariable('is_table', true);
    $tabler = $tableq->fetch(PDO::FETCH_ASSOC);
}
else
    $opsTheme->addVariable('is_table', false);


$tabletype = ($tabler['tabletype'] == 't') ? 'Tournament' : 'Sit \'n Go';
$opsTheme->addVariable('tabletype', $tabletype);
$opsTheme->addVariable('table', $tabler);

$gameStyles = $addons->get_hooks(
    array(
        'content' => array(
            array('value' => 't', 'label' => GAME_TEXAS),
        )
    ),
    array(
        'page'     => 'admin/edit-table.php',
        'location'  => 'admin_table_gamestyle_logic'
    )
);
$opsTheme->addVariable('game_styles', $gameStyles);


$minBuyins = $addons->get_hooks(
    array(
        'content' => array(
            array('value' => '0',      'label' => money(0)),
            array('value' => '1000',   'label' => money(1000)),
            array('value' => '2500',   'label' => money(2500)),
            array('value' => '5000',   'label' => money(5000)),
            array('value' => '10000',  'label' => money(10000)),
            array('value' => '25000',  'label' => money(25000)),
            array('value' => '50000',  'label' => money(50000)),
            array('value' => '100000', 'label' => money(100000)),
            array('value' => '250000', 'label' => money(250000)),
            array('value' => '500000', 'label' => money(500000)),
        )
    ),
    array(
        'page'     => 'admin/edit-table.php',
        'location'  => 'min_buyins'
    )
);
$opsTheme->addVariable('min_buyins', $minBuyins);


$maxBuyins = $addons->get_hooks(
    array(
        'content' => array(
            array('value' => '10000',   'label' => money(10000)),
            array('value' => '25000',   'label' => money(25000)),
            array('value' => '50000',   'label' => money(50000)),
            array('value' => '100000',  'label' => money(100000)),
            array('value' => '250000',  'label' => money(250000)),
            array('value' => '500000',  'label' => money(500000)),
            array('value' => '1000000', 'label' => money(1000000)),
        )
    ),
    array(
        'page'     => 'admin/edit-table.php',
        'location'  => 'max_buyins'
    )
);
$opsTheme->addVariable('max_buyins', $maxBuyins);

$smallblinds = $tabler['sbamount'];
$sbamounts	= moneynumber($smallblinds);
$opsTheme->addVariable('sblind', $sbamounts);

$bigblinds = $tabler['bbamount'];
$bbamounts	= moneynumber($bigblinds);
$opsTheme->addVariable('bblind', $bbamounts);

$tableStyles = array(
    array('value' => 'table_green', 'label' => 'table_green')
);
$stq         = $pdo->prepare("select style_name from styles"); $stq->execute();
while ($str = $stq->fetch(PDO::FETCH_ASSOC))
{
    $tableStyles[] = array('value' => $str['style_name'], 'label' => $str['style_name']);
}
$tableStyles = $addons->get_hooks(
    array(
        'content' => $tableStyles,
    ),
    array(
        'page'     => 'admin/edit-table.php',
        'location'  => 'table_styles'
    )
);
$opsTheme->addVariable('table_styles', $tableStyles);

$inputs  = $opsTheme->viewPart('admin-edit-table-inputs');
$inputs .= $addons->get_hooks(array(), array(
    'page'     => 'admin/edit-table.php',
    'location'  => 'input_block'
));
$opsTheme->addVariable('inputs', $inputs);

echo $opsTheme->viewPage('admin-edit-table');
?>