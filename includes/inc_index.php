<?php

if (isset($action) && $action == 'logout' && $plyrname != '')
{
    if (isset($_SESSION['playername']))
    {
    	unset($_SESSION['playername']);
    }

    if (isset($_SESSION['SGUID']))
    {
    	unset($_SESSION['SGUID']);
    }

    header('Location: index.php');
}
?>