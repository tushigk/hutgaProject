<?php
session_start();

if (isset($_GET['debug']))
{
    ini_set('display_errors', true);
    error_reporting(E_ALL);
}
else
{
    ini_set('display_errors', false);
    error_reporting(0);
}

require('configure.php');
$host    = DB_SERVER;
$ln      = DB_SERVER_USERNAME;
$pw      = DB_SERVER_PASSWORD;
$db      = DB_DATABASE;
$charset = 'utf8mb4';
try
{
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $ln, $pw, array(
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false,
    ));
}
catch (PDOException $e)
{
    echo "Unable to connect to database  - <a href='http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]install/'>Click here to install OPS V2</a>";
    die();
}

require('tables.php');

if (file_exists('sql.php'))
{
    include 'sql.php';
    unlink('sql.php');
}

require('settings.php');


/* THEME */
if (! isset($opsTheme))
{
	$themeCFN = 'Theme.class.php';
	$themeCF  = $themeCFN;
	if (!file_exists($themeCF)) $themeCF = 'includes/' . $themeCF;
	if (!file_exists($themeCF)) $themeCF = '../' . $themeCF;
	if (!file_exists($themeCF)) die();
	require($themeCF);
}
/* THEME */


if (! isset($addons))
{
	$addonClassFileName = 'Addon.class.php';
	$addonClassFile     = $addonClassFileName;
	
	if (!file_exists($addonClassFile))
	    $addonClassFile = 'includes/' . $addonClassFile;
	if (!file_exists($addonClassFile))
	    $addonClassFile = '../' . $addonClassFile;
	if (!file_exists($addonClassFile))
	    die();
	
	$addonDir = str_replace($addonClassFileName, '', $addonClassFile) . 'addons';
	
	require($addonClassFile);
	$addonSettings = array();
	$addons        = new \OPSAddon();
	require($addonDir . '/autoloader.php');
}

echo $addons->get_hooks(array(), array(
    'page'     => 'includes/sec_inc.php',
    'location'  => 'start'
));

require('poker_inc.php');

$plyrname = addslashes($_SESSION['playername']);
$SGUID    = addslashes($_SESSION['SGUID']);

if ($plyrname == '' || $SGUID == '')
{
	header('Location: login.php');
}

$valid  = false;
$gameID = '';
$gID    = '';
$idq    = $pdo->prepare("select GUID, vID, gID, banned from " . DB_PLAYERS . " where username = '" . $plyrname . "' and GUID = '" . $SGUID . "'"); $idq->execute();
$idr    = $idq->fetch(PDO::FETCH_ASSOC);

if ($idq->rowCount() == 1 && $idr['banned'] != 1)
{
    $valid  = true;
    $gameID = $idr['vID'];
    $gID    = $idr['gID'];
    
    $getstats = $pdo->prepare("select * from ".DB_STATS." where player = '{$plyrname}' ");
    $getstats->execute();
    $usestats = $getstats->fetch(PDO::FETCH_ASSOC);

    $current_chipcount = $usestats['winpot'];
    $current_money = money($usestats['winpot']);

    $opsTheme->addVariable('current_chipcount', $current_chipcount);
    $opsTheme->addVariable('current_money',     $current_money);
    $opsTheme->addVariable('username', $plyrname);
}

if ($valid == false || $gameID == '')
{
    die();
}

require('language.php');
?>