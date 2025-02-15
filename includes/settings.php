<?php
$configuration_query = $pdo->prepare("SELECT Xkey AS cfgKey, Xvalue AS cfgValue FROM " . DB_SETTINGS);
$configuration_query->execute();
while ($configuration = $configuration_query->fetch(PDO::FETCH_ASSOC))
{
	// pulling configuration settings from database
    define($configuration['cfgKey'], stripslashes($configuration['cfgValue']));
}

// Adjust stakes according to admin settings
$smallbetfunc = 0;

if (STAKESIZE == 'tiny')
{
	$smallbetfunc = 1;
}

if (STAKESIZE == 'low')
{
	$smallbetfunc = 2;
}

if (STAKESIZE == 'med')
{
	$smallbetfunc = 3;
}
?>