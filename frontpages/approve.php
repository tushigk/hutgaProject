<?php 
require('includes/configure.php');
require('includes/tables.php');

$host    = DB_SERVER;
$ln      = DB_SERVER_USERNAME;
$pw      = DB_SERVER_PASSWORD;
$db      = DB_DATABASE;
$charset = 'utf8mb4';

// Connection database
$pdo = new PDO("mysql:host=$host;charset=$charset", $ln, $pw) or die('Unable to connect to database  - <a href="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . 'install/">Click here to install OPS V2</a>');
$pdo->query("USE $db") or die('Unable to select database');

// Approval code
$code = (isset($_GET['approval'])) ? addslashes($_GET['approval']) : '';
if ($code == '')
{
	die();
}

// Update players table, set approve to 0 where code matches
$result = $pdo->exec("update " . DB_PLAYERS . " set approve = 0 where code = '$code'");

$url = 'login.php';

echo '<script type="text/javascript">parent.document.location.href = "' . $url . '";</script>';
die();
?>