<?php
require ('sec_inc.php');
header('Content-Type: text/javascript');

if ($gID == '' || $gID == 0)
    die();

if (! isset($_POST['msg']) && ! isset($_GET['msg']))
    die();

// Add to chat

$chtq  = $pdo->query("select * from " . DB_USERCHAT . " where gameID = " . $gameID);
$chtr  = $chtq->fetch(PDO::FETCH_ASSOC);
$time  = time();
$time += 2;
$c2   = addslashes($chtr['c2']);
$c3   = addslashes($chtr['c3']);
$c4   = addslashes($chtr['c4']);
$c5   = addslashes($chtr['c5']);
$msg  = (isset($_POST['msg'])) ? $_POST['msg'] : $_GET['msg'];
$msg  = strip_tags(str_ireplace(array('"', "'"), array('&quot;', '&apos;'), $msg));
$msg  = preg_replace('/([^\s]{16})(?=[^\s])/m', '$1 ', $msg);
$msg  = substr($msg, 0, 100);
if (strlen($msg) > 0)
{
    $plyrQ = $pdo->query("SELECT ID, avatar FROM " . DB_PLAYERS . " WHERE username = '$plyrname'");
    $plyrF = $plyrQ->fetch(PDO::FETCH_ASSOC);
    $msg = '<user>
    <id>' . $plyrF['ID'] . '</id>
    <name>' . $plyrname . '</name>
    <avatar>' . $plyrF['avatar'] . '</avatar>
</user>
<message>' . $msg . '</message>';

    $msg  = addslashes($msg);
    $chtq = $pdo->query("select * from " . DB_USERCHAT . " where gameID = " . $gID);
    
    if ($chtq->rowCount() > 0)
        $result = $pdo->exec("update " . DB_USERCHAT . " set updatescreen = $time, c1 = '$c2', c2 = '$c3', c3 = '$c4', c4 = '$c5', c5  = '$msg' where gameID = " . $gID);
    else
        $result = $pdo->exec("insert into " . DB_USERCHAT . " set updatescreen = '$time', c5 = '$msg', gameID = '$gID' ");
}

?>