<?php
$emailModes = array(
	array('value' => '0', 'label' => 'Off'),
	array('value' => '1', 'label' => 'On'),
);
$opsTheme->addVariable('email_modes', $emailModes);


$appModes = array(
	array('value' => '0', 'label' => 'Automatic'),
	array('value' => '1', 'label' => 'Email Approval'),
	array('value' => '2', 'label' => 'Administrator Approval'),
);
$opsTheme->addVariable('app_modes', $appModes);


$ipChecks = array(
	array('value' => '0', 'label' => 'Off'),
	array('value' => '1', 'label' => 'On'),
);
$opsTheme->addVariable('ip_checks', $ipChecks);


$memModes = array(
	array('value' => '0', 'label' => 'Off'),
	array('value' => '1', 'label' => 'On'),
);
$opsTheme->addVariable('mem_modes', $memModes);


$deletes = array(
	array('value' => '30',    'label' => 'After 30 days of inactivity'),
	array('value' => '60',    'label' => 'After 60 days of inactivity'),
	array('value' => '90',    'label' => 'After 90 days of inactivity'),
	array('value' => '180',   'label' => 'After 180 days of inactivity'),
	array('value' => 'never', 'label' => 'Never'),
);
$opsTheme->addVariable('deletes', $deletes);


$stakeSizes = array(
	array('value' => 'tiny', 'label' => 'Tiny Stakes [$10+]'),
	array('value' => 'low',  'label' => 'Low Stakes [$100+]'),
	array('value' => 'med',  'label' => 'Medium Stakes [$1000+]'),
	array('value' => 'high', 'label' => 'High Rollers [$10k+]'),
);
$opsTheme->addVariable('stakesizes', $stakeSizes);


$renews = array(
	array('value' => '0', 'label' => 'Off'),
	array('value' => '1', 'label' => 'On'),
);
$opsTheme->addVariable('renews', $renews);


$kickTimers = array(
	array('value' => '3',  'label' => '3 mins'),
	array('value' => '5',  'label' => '5 mins'),
	array('value' => '7',  'label' => '7 mins'),
	array('value' => '10', 'label' => '10 mins'),
	array('value' => '15', 'label' => '15 mins'),
);
$opsTheme->addVariable('kicktimers', $kickTimers);


$moveTimers = array(
	array('value' => '10', 'label' => 'Turbo'),
	array('value' => '15', 'label' => 'Fast'),
	array('value' => '20', 'label' => 'Normal'),
	array('value' => '27', 'label' => 'Slow'),
);
$opsTheme->addVariable('movetimers', $moveTimers);


$showdowns = array(
	array('value' => '3',  'label' => '3 secs'),
	array('value' => '5',  'label' => '5 secs'),
	array('value' => '7',  'label' => '7 secs'),
	array('value' => '10', 'label' => '10 secs'),
);
$opsTheme->addVariable('showdowns', $showdowns);


$waitTimers = array(
	array('value' => '0',  'label' => 'None'),
	array('value' => '10', 'label' => '10 secs'),
	array('value' => '15', 'label' => '15 secs'),
	array('value' => '20', 'label' => '20 secs'),
	array('value' => '25', 'label' => '25 secs'),
);
$opsTheme->addVariable('waittimers', $waitTimers);


$disconnects = array(
	array('value' => '15',  'label' => '15 secs'),
	array('value' => '30',  'label' => '30 secs'),
	array('value' => '60',  'label' => '60 secs'),
	array('value' => '90',  'label' => '90 secs'),
	array('value' => '120', 'label' => '120 secs'),
);
$opsTheme->addVariable('disconnects', $disconnects);


$inputsBasic  = $opsTheme->viewPart('admin-settings-basics');
$inputsBasic .= $addons->get_hooks(
    array(),
    array(
        'page'     => 'admin/settings.php',
        'location'  => 'basic_inputs'
    )
);

$inputsDetailed  = $opsTheme->viewPart('admin-settings-detailed');
$inputsDetailed .= $addons->get_hooks(
    array(),
    array(
        'page'     => 'admin/settings.php',
        'location'  => 'detailed_inputs'
    )
);

$opsTheme->addVariable('settings', array(
	'inputs' => array(
		'basic'    => $inputsBasic,
		'detailed' => $inputsDetailed
	),
));


echo $opsTheme->viewPage('admin-settings');
?>