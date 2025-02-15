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
    echo "Unable to connect to database  - <a href='http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "install/'>Click here to install OPS V2</a>";
    die();
}

require('tables.php');

if (file_exists('sql.php'))
{
    include 'sql.php';
    unlink('sql.php');
}

require('settings.php');

$activation_status = ACTIVATION;

// Check if the activation status is not 'active'
if ($activation_status !== 'active') {
    // Include the license.php file if activation is not active
    require_once('license.php');
}

/* THEME */
$themeCFN = 'Theme.class.php';
$themeCF  = $themeCFN;
if (!file_exists($themeCF)) $themeCF = 'includes/' . $themeCF;
if (!file_exists($themeCF)) $themeCF = '../' . $themeCF;
if (!file_exists($themeCF)) die();
require($themeCF);
/* THEME */


$addonClassFileName = 'Addon.class.php';
$addonClassFile     = $addonClassFileName;

if (!file_exists($addonClassFile))
    $addonClassFile = 'includes/' . $addonClassFile;
    
if (!file_exists($addonClassFile))
    $addonClassFile = '../' . $addonClassFile;
    
if (!file_exists($addonClassFile))
    die();

require($addonClassFile);
$addonDir      = str_replace($addonClassFileName, '', $addonClassFile) . 'addons';
$addonSettings = array();
$addons        = new \OPSAddon();
require($addonDir . '/autoloader.php');

echo $addons->get_hooks(array(), array(
    'page'     => 'includes/gen_inc.php',
    'location'  => 'start'
));

require('poker_inc.php');
require('language.php');

$plyrname  = (isset($_SESSION['playername'])) ? addslashes($_SESSION['playername']) : '';
$SGUID     = (isset($_SESSION['SGUID']))      ? addslashes($_SESSION['SGUID'])      : '';
$valid     = false;
$ADMIN     = false;
$gID       = '';

$opsTheme->addVariable('is_admin', 0);
$opsTheme->addVariable('is_logged', 0);

if ($plyrname != '' && $SGUID != '')
{
    $idq    = $pdo->prepare("select GUID, banned, gID, ID, vID from " . DB_PLAYERS . " where username = '" . $plyrname . "' and GUID = '" . $SGUID . "' "); $idq->execute();
    $idr    = $idq->fetch(PDO::FETCH_ASSOC);
    $gID    = $idr['gID'];
    $pID    = $idr['ID'];
    $gameID = $idr['vID'];

    if ($idq->rowCount() == 1 && $idr['banned'] != 1)
    {
        $valid = true;
    }

    $siteadmin = ADMIN_USERS;
    $sitecurrency	= MONEY_PREFIX;

    if ($plyrname != '')
    {
        $time     = time();
        $admins   = array();
        $adminraw = explode(',', $siteadmin);
        $i        = 0;

        foreach ($adminraw as $i => $value)
        {
            $admins[$i] = $value;
        }

        if (in_array($plyrname, $admins))
        {
            $ADMIN = true;
        }

        $getstats = $pdo->prepare("select * from ".DB_STATS." where player = '{$plyrname}' ");
        $getstats->execute();
        $usestats = $getstats->fetch(PDO::FETCH_ASSOC);

        $current_chipcount = $usestats['winpot'];
        $current_money = money($usestats['winpot']);

        $opsTheme->addVariable('current_chipcount', $current_chipcount);
        $opsTheme->addVariable('current_money',     $current_money);
        $opsTheme->addVariable('username', $plyrname);
        $opsTheme->addVariable('is_logged', 1);
        
        $opsTheme->addVariable('sitecurrency', $sitecurrency);
    } 
}


if ($ADMIN == true)
{
    $opsTheme->addVariable('is_admin', 1);
    $now               = time();
    $last_update_check = LASTUPDATECH + (60 * 60 * 3);

    if ($now > $last_update_check)
    {
        $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '$now' WHERE setting = 'lastupdatech'");
        $updateJson = json_decode(file_get_contents_su(base64_decode('aHR0cHM6Ly91cGRhdGVzLm9ubGluZXBva2Vyc2NyaXB0LmNvbS9jb3JlL3NjcmlwdA==')));

        if (isset($updateJson->status) && $updateJson->status === "OK")
            $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '1' WHERE setting = 'updatealert'");
        else
            $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '0' WHERE setting = 'updatealert'");


        $newAddonUpdates = 0;
        foreach (glob('includes/addons/*', GLOB_ONLYDIR) as $addonDir)
        {
            $addonInfoFile = "{$addonDir}/info.json";

            if (! file_exists($addonInfoFile))
                continue;

            $addonInfo = json_decode(file_get_contents($addonInfoFile));

            if (! isset($addonInfo->version, $addonInfo->update_url))
                continue;

            $addonJson = json_decode(file_get_contents_ssl($addonInfo->update_url, array(
                'ip'      => get_user_ip_addr(),
                'domain'  => preg_replace('/[^A-Za-z0-9-.]/i', '', $_SERVER['SERVER_NAME']),
                'license' => LICENSEKEY,
                'version' => $addonInfo->version,
            )));

            if (isset($addonJson->status) && $addonJson->status === "OK")
                $newAddonUpdates++;
        }
        $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$newAddonUpdates}' WHERE setting = 'addonupdatea'");


        $newThemeUpdates = 0;
        foreach (glob('themes/*', GLOB_ONLYDIR) as $themeDir)
        {
            $themeInfoFile = "{$themeDir}/info.json";

            if (! file_exists($themeInfoFile))
                continue;

            $themeInfo = json_decode(file_get_contents($themeInfoFile));

            if (! isset($themeInfo->version, $themeInfo->update_url))
                continue;

            $themeJson = json_decode(file_get_contents_ssl($themeInfo->update_url, array(
                'ip'      => get_user_ip_addr(),
                'domain'  => preg_replace('/[^A-Za-z0-9-.]/i', '', $_SERVER['SERVER_NAME']),
                'license' => LICENSEKEY,
                'version' => $themeInfo->version,
            )));

            if (isset($themeJson->status) && $themeJson->status === "OK")
                $newThemeUpdates++;
        }
        $pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$newThemeUpdates}' WHERE setting = 'themeupdatea'");

        header('Refresh: 0');
    }
}


if (isset($_SESSION[SESSNAME]) && $_SESSION[SESSNAME] != '' && MEMMOD == 1 && $plyrname == '')
{
    $time     = time();
    $sessname = addslashes($_SESSION[SESSNAME]);
    $usrq     = $pdo->prepare("select username from " . DB_PLAYERS . " where sessname = '" . $sessname . "' "); $usrq->execute();

    if ($usrq->rowCount() == 1)
    {
        $usrr = $usrq->fetch(PDO::FETCH_ASSOC);
        $usr  = $usrr['username'];
        $GUID = randomcode(32);

        $_SESSION['playername'] = $usr;
        $_SESSION['SGUID'] = $GUID;

        $ip     = $_SERVER['REMOTE_ADDR'];
        $result = $pdo->exec("update " . DB_PLAYERS . " set ipaddress = '" . $ip . "', lastlogin = " . $time . " , GUID = '" . $GUID . "' where username = '" . $usr . "' ");
        $valid  = true;
    } 
}

$time     = time();
$tq       = $pdo->prepare("select waitimer from " . DB_PLAYERS . " where username = '" . $plyrname . "' "); $tq->execute();
$tr       = $tq->fetch(PDO::FETCH_ASSOC);
$waitimer = $tr['waitimer'];

/*if ($waitimer > $time)
{
    header('Location sitout.php');
}*/
?>