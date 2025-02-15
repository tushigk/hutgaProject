<?php

if ($valid == false)
{
    header('Location: login.php');
}

if ($ADMIN == false)
{
    header('Location: index.php');
}

$action = (isset($_POST['action'])) ? addslashes($_POST['action']) : '';

//var_dump($addons); die();

$addons->get_hooks(
    array(),
    array(
        'page'     => 'includes/inc_admin.php',
        'location'  => 'admin_post'
    )
);

if ($action == 'createtable')
{
    $usr    	= (isset($_POST['player'])) ? addslashes($_POST['player']) : '';
    $tname  	= (isset($_POST['tname']))  ? addslashes($_POST['tname']) : '';
    $tmax   	= (isset($_POST['tmax']))   ? addslashes($_POST['tmax']) : '';
    $tmin   	= (isset($_POST['tmin']))   ? addslashes($_POST['tmin']) : '';
    $tsb		= (isset($_POST['sbamount']))   ? addslashes(transfer_to($_POST['sbamount'])) : '';
    $tbb		= (isset($_POST['sbamount']))   ? addslashes(transfer_to($_POST['bbamount'])) : '';      
    $action		= (isset($_POST['action'])) ? addslashes($_POST['action']) : '';
    $tstyle		= (isset($_POST['tstyle'])) ? addslashes($_POST['tstyle']) : '';
    $ttype		= (isset($_POST['ttype']))  ? addslashes($_POST['ttype']) : '';
    $gstlye		= (isset($_POST['gstyle']))  ? addslashes($_POST['gstyle']) : '';
}

if ($action == 'createtable' && $tname != '' && $tmin <= $tmax)
{
    if ($gstlye == '')
    {
        $sql = "INSERT INTO " . DB_POKER . " SET tablename = '$tname',  tablelow = $tmin,  tablelimit = '$tmax', sbamount = '$tsb', bbamount = '$tbb',  tabletype = '$ttype',  tablestyle = '$tstyle'";
    }
    else
    {
        $sql = "INSERT INTO " . DB_POKER . " SET tablename = '$tname',  tablelow = $tmin,  tablelimit = '$tmax', sbamount = '$tsb', bbamount = '$tbb',  tabletype = '$ttype',  tablestyle = '$tstyle', gamestyle = '$gstlye'";
    }

    $stmt = $addons->get_hooks(
        array(
            'content' => $sql
        ),
        array(
            'page'     => 'includes/inc_admin.php',
            'location'  => 'create_table_sql'
        )
    );

    $result = $pdo->query($sql);
    $addons->get_hooks(
        array(
            'content' => $pdo->lastInsertId()
        ),
        array(
            'page'     => 'includes/inc_admin.php',
            'location'  => 'after_create_table'
        )
    );
}

$delete = (isset($_GET['delete'])) ? addslashes($_GET['delete']) : '';

if (is_numeric($delete) && isset($delete))
{
    $result = $pdo->exec("delete from  " . DB_POKER . " where gameID = " . $delete);

    $result = $pdo->exec("delete from " . DB_LIVECHAT . " where gameID = " . $delete);

    $result = $pdo->exec("update " . DB_PLAYERS . " set vID = 0, gID = 0 where vID = " . $delete);
}

if ($action == 'install')
{
	$dir  = getcwd();
    $path = $dir . "/images/tablelayout/";
    $ext  = pathinfo($_FILES['uploaded_file']['name'], PATHINFO_EXTENSION);
    $nam  = basename( $_FILES['uploaded_file']['name'], '.png' );
    $path = $path . basename( $_FILES['uploaded_file']['name']);

    if ($ext === 'png')
    {
        if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $path))
        {
            $msg = "<div class='alert alert-success'>The style " . basename( $_FILES['uploaded_file']['name'], '.png') . " has been added!</div>";
        }
        else
        {
            $msg = "<div class='alert alert-danger'>There was an error , please try again!</div>";
        }

        $lic = (isset($_POST['lic'])) ? addslashes($_POST['lic']) : '';
        $sq  = $pdo->prepare("select style_name from styles where style_name = '" . $nam . "' ");
        $sq->execute();

        if ($sq->rowCount() > 0)
        {
            $msg = ADMIN_MSG_STYLE_INSTALLED;
        }
        elseif ($nam == '' || $lic == '')
        {
            $msg = ADMIN_MSG_MISSING_DATA;
        }
        else
        {
            $result = $pdo->exec("insert into " . DB_STYLES . " set style_name = '$nam', style_lic = '$lic' ");
            header('Location: admin.php?admin=styles&success=true');
            die();
        }
    }
}

if (isset($usr))
{    
    $usr = (isset($_GET['delete']))  ? addslashes($_GET['delete']) : '';
}
else
{
    $usr = (isset($_POST['player'])) ? addslashes($_POST['player']) : '';	
}

if ($usr != '')
{
    $uq = $pdo->prepare("select email from " . DB_PLAYERS . " where username = '" . $usr . "' ");
    $uq->execute();
    $ur = $uq->fetch(PDO::FETCH_ASSOC);
    $em = $ur['email'];

    if ($action == 'ban')
    {
        if ($em != '')
            $result = $pdo->exec("update " . DB_PLAYERS . " set banned = '1' where email = '" . $em . "' ");
        $result = $pdo->exec("update " . DB_PLAYERS . " set banned = '1' where username = '" . $usr . "' ");
    }
    elseif ($action == 'unban')
    {
        if ($em != '')
            $result = $pdo->exec("update " . DB_PLAYERS . " set banned = 0 where email = '" . $em . "' ");
        $result = $pdo->exec("update " . DB_PLAYERS . " set banned = 0 where username = '" . $usr . "' ");
    }
    elseif ($action == 'reset')
    {
        $result = $pdo->exec("update " . DB_STATS . " set winpot = 0, rank = '', gamesplayed = 0, tournamentswon = 0, tournamentsplayed = 0, handsplayed = 0, handswon = 0, bet = 0, checked = 0, called = '0', allin = '0', fold_pf = '0', fold_f = '0', fold_t = '0', fold_r = 0 where player = '" . $usr . "' ");
    }
    elseif ($action == 'approve')
    {
        $result = $pdo->exec("update " . DB_PLAYERS . " set approve = 0 where username = '" . $usr . "' ");
        
    }
    elseif ($action == 'delete')
    {
        $result = $pdo->exec("delete from " . DB_PLAYERS . " where username = '" . $usr . "' ");
        $result = $pdo->exec("delete from  " . DB_STATS . " where player = '" . $usr . "' ");

        if (file_exists('images/avatars/' . $usr . '.jpg'))
        {
            unlink('images/avatars/' . $usr . '.jpg');
        }
    }
}

if ($action == 'update')
{
    $title = (isset($_POST['title'])) ? addslashes($_POST['title']) : '';
    if ($title == '')
    {
        $title = 'Texas Holdem Poker';
    }

    $emailmode = (isset($_POST['emailmode'])) ? addslashes($_POST['emailmode']) : '';
    if ($emailmode != 1)
    {
        $emailmode = 0;
    }

    $ipcheck = (isset($_POST['ipcheck'])) ? addslashes($_POST['ipcheck']) : '';
    if ($ipcheck != 0)
    {
        $ipcheck = 1;
    }

    $renewbutton = (isset($_POST['renew'])) ? addslashes($_POST['renew']) : '';
    if ($renewbutton != 0)
    {
        $renewbutton = 1;
    }

    $appmode = (isset($_POST['appmode'])) ? addslashes($_POST['appmode']) : '';
    if (($appmode != 1) && ($appmode != 2))
    {
        $appmode = 0;
    }

    if ($appmode == 1)
    {
        $emailmode = 1;
    }

    $memmode   = (isset($_POST['memmode'])) ? addslashes($_POST['memmode']) : '';
    $deletearray = array(
        30,
        60,
        90,
        180,
        'never'
    );
    $delete      = (isset($_POST['delete'])) ? addslashes($_POST['delete']) : '';

    if (!in_array($delete, $deletearray))
    {
        $delete = 90;
    }

    $ssizearray = array(
        'tiny',
        'low',
        'med',
        'high'
    );
    $ssize      = (isset($_POST['stakesize'])) ? addslashes($_POST['stakesize']) : '';

    if (!in_array($ssize, $ssizearray))
    {
        $ssize = med;
    }

    $sess      = (isset($_POST['session']))   ? addslashes($_POST['session'])   : '';

    $result = $pdo->exec(" update " . DB_SETTINGS . " set Xvalue = '" . $title . "' where setting = 'title' ");

    $result = $pdo->exec(" update " . DB_SETTINGS . " set Xvalue = '" . $appmode . "' where setting = 'appmod' ");

    $result = $pdo->exec(" update " . DB_SETTINGS . " set Xvalue = '" . $emailmode . "' where setting = 'emailmod' ");

    $result = $pdo->exec(" update " . DB_SETTINGS . " set Xvalue = '" . $ipcheck . "' where setting = 'ipcheck' ");

    $result = $pdo->exec(" update " . DB_SETTINGS . " set Xvalue = '" . $memmode . "' where setting = 'memmod' ");

    $result = $pdo->exec(" update " . DB_SETTINGS . " set Xvalue = '" . $delete . "' where setting = 'deletetimer' ");

    $result = $pdo->exec(" update " . DB_SETTINGS . " set Xvalue = '" . $sess . "' where setting = 'session' ");

    $result = $pdo->exec(" update " . DB_SETTINGS . " set Xvalue = '" . $ssize . "' where setting = 'stakesize' ");

    $result = $pdo->exec(" update " . DB_SETTINGS . " set Xvalue = '" . $renewbutton . "' where setting = 'renew' ");

    $addons->get_hooks(
        array(),
        array(
            'page'     => 'includes/inc_admin.php',
            'location'  => 'settings_update_basic'
        )
    );

    header('Location: admin.php?admin=settings&ud=1');
}

if ($action == 'update2')
{
    $kickarray = array(
        3,
        5,
        7,
        10,
        15
    );
    $kick      = (isset($_POST['kick'])) ? addslashes($_POST['kick']) : '';

    if ( !in_array($kick, $kickarray) )
    {
        $kick = 5;
    }

    $movearray = array(
        10,
        15,
        20,
        27
    );
    $move      = (isset($_POST['move'])) ? addslashes($_POST['move']) : '';

    if ( !in_array($move, $movearray) )
    {
        $move = 20;
    }

    $showdownarray = array(
        3,
        4,
        5,
        7,
        10
    );
    $showdown      = (isset($_POST['showdown'])) ? addslashes($_POST['showdown']) : '';

    if ( !in_array($showdown, $showdownarray) )
    {
        $showdown = 7;
    }

    $waitarray = array(
        0,
        10,
        15,
        20,
        25
    );
    $wait      = (isset($_POST['wait'])) ? addslashes($_POST['wait']) : '';

    if (!in_array($wait, $waitarray))
    {
        $wait = 20;
    }

    $disconarray = array(
        15,
        30,
        60,
        90,
        120
    );
    $discon      = (isset($_POST['disconnect'])) ? addslashes($_POST['disconnect']) : '';

    if (!in_array($discon, $disconarray))
    {
        $discon = 60;
    }

    $sess      = (isset($_POST['session']))   ? addslashes($_POST['session'])   : '';

    $result = $pdo->exec(" update " . DB_SETTINGS . " set Xvalue = '" . $kick . "' where setting = 'kicktimer' ");

    $result = $pdo->exec(" update " . DB_SETTINGS . " set Xvalue = '" . $showdown . "' where setting = 'showtimer' ");

    $result = $pdo->exec(" update " . DB_SETTINGS . " set Xvalue = '" . $move . "' where setting = 'movetimer' ");

    $result = $pdo->exec(" update " . DB_SETTINGS . " set Xvalue = '" . $wait . "' where setting = 'waitimer' ");

    $result = $pdo->exec(" update " . DB_SETTINGS . " set Xvalue = '" . $sess . "' where setting = 'session' ");

    $result = $pdo->exec(" update " . DB_SETTINGS . " set Xvalue = '" . $discon . "' where setting = 'disconnect' ");

    $addons->get_hooks(
        array(),
        array(
            'page'     => 'includes/inc_admin.php',
            'location'  => 'settings_update_detailed'
        )
    );

    header('Location: admin.php?admin=settings&ud=1');
}

$adminview = (isset($_GET['admin'])) ? addslashes($_GET['admin']) : '';

if ( $action == 'edittable' )
{
    $sql = 'update ' . DB_POKER . ' SET ';
    $updates = array();

    $skip = array('action', 'gameID', 'tabletype', 'tablegame');
    $skipnumbers = array('tablelow', 'pot', 'bet', 'lastmove');

    $addons->get_hooks(array(), array(
        'page'     => 'includes/inc_admin.php',
        'location'  => 'edit_table_start'
    ));

    foreach ($_POST as $key => $value)
    {
        if (in_array( $key, $skip ))
        {
            continue;
        }

        if ($key == 'startdate')
        {
            $updates[] = $key . ' = TIMESTAMP("' . str_replace( 'T', ' ', $value ) . '")';
            continue;
        }
        
        if ( $key == 'sbamount' ) {
            $updates[] = $key . ' = ' . transfer_to($value);
            continue;
        }  
        
        if ( $key == 'bbamount' ) {
            $updates[] = $key . ' = ' . transfer_to($value);
            continue;
        }                

        if (in_array( $key, $skipnumbers))
        {
            $updates[] = $key . ' = ' . $value;
            continue;
        }

        $updates[] = $key . ' = "' . $value . '"';
    }

    $sql .= implode(',', $updates);
    $sql .= " where gameID = {$_POST['gameID']}";

    $result = $pdo->exec( $sql );

    header('Location: admin.php?admin=tables&ud=1');
}

if ( $action == 'editmember' )
{
    $sql = 'UPDATE ' . DB_PLAYERS . ' SET ';
    $updates = array();
    $skip = array('ID', 'action');
    $skipnumbers = array('datecreated', 'lastlogin', 'banned', 'approve', 'lastmove', 'waitimer', 'vID', 'gID', 'timetag');

    $addons->get_hooks(
        array(),
        array(
            'page'     => 'includes/inc_admin.php',
            'location'  => 'editmember_before_update'
        )
    );

    foreach ($_POST as $key => $value) {
        if ( in_array( $key, $skip ) )
            continue;

        if ( $key == 'startdate' ) {
            $updates[] = $key . ' = TIMESTAMP("' . str_replace( 'T', ' ', $value ) . '")';
            continue;
        }

        if ( in_array( $key, $skipnumbers ) )
        {
            $updates[] = $key . ' = ' . $value;
            continue;
        }

        $updates[] = $key . ' = "' . $value . '"';
    }

    $sql .= implode(',', $updates);
    $sql .= " WHERE username = '" . $_POST['username'] . "'";

    $result = $pdo->exec( $sql );

    $addons->get_hooks(
        array(),
        array(
            'page'     => 'includes/inc_admin.php',
            'location'  => 'editmember_after_update'
        )
    );

    header('Location: admin.php?admin=members&ud=1');
}

if ( $action == 'editmemberchips' )
{
    $sql = 'update ' . DB_STATS . ' SET ';
    $updates = array();
    $skip = array('ID', 'action');
    $skipnumbers = array('winpot', 'gamesplayed', 'tournamentsplayed', 'tournamentswon', 'handsplayed', 'handswon', 'bet', 'checked', 'fold_r');

    foreach ($_POST as $key => $value) {
        if ( in_array( $key, $skip ) )
            continue;

        if ( $key == 'startdate' ) {
            $updates[] = $key . ' = TIMESTAMP("' . str_replace( 'T', ' ', $value ) . '")';
            continue;
        }

        if ( in_array( $key, $skipnumbers ) )
        {
            $updates[] = $key . ' = ' . $value;
            continue;
        }

        $updates[] = $key . ' = "' . $value . '"';
    }

    $sql .= implode(',', $updates);
    $sql .= " where player = '" . $_POST['player'] . "'";

    $result = $pdo->exec( $sql );

    header('Location: admin.php?admin=members&ud=1');
}



// updates
if (isset($_GET['download_update']))
{
    $updateJson = json_decode(file_get_contents_su(base64_decode('aHR0cHM6Ly91cGRhdGVzLm9ubGluZXBva2Vyc2NyaXB0LmNvbS9jb3JlL3NjcmlwdA=='), true));

    if (! isset($updateJson->status) || $updateJson->status !== "OK")
        return false;
    
    if (file_put_contents('update-' . $updateJson->version . '.zip', file_get_contents_ssl($updateJson->url)))
    {
        header("Content-type: application/json; charset=utf-8");
        echo json_encode(array(
            'status' => 'OK'
        ));
        exit();
    }
}

if (isset($_GET['extract_update']))
{
    $updateJson = json_decode(file_get_contents_su(base64_decode('aHR0cHM6Ly91cGRhdGVzLm9ubGluZXBva2Vyc2NyaXB0LmNvbS9jb3JlL3NjcmlwdA==')));

    if (! isset($updateJson->status) || $updateJson->status !== "OK")
        return false;

    $version = $updateJson->version;
    $zipFile = "update-$version.zip";
    
    if ( file_exists($zipFile) )
    {
        $dir = realpath('');
        $zip = new ZipArchive;
        
        if ($zip->open($zipFile) === true)
        {
            $zip->extractTo($dir);
            $zip->close();

            $pdo->exec("UPDATE " . DB_SETTINGS . " SET Xvalue = '$version' WHERE setting = 'scriptversio' AND Xkey = 'SCRIPTVERSIO'");
            $pdo->exec("UPDATE " . DB_SETTINGS . " SET Xvalue = '0' WHERE setting = 'updatealert' AND Xkey = 'UPDATEALERT'");

            unlink($zipFile);

            header("Content-type: application/json; charset=utf-8");
            echo json_encode(array(
                'status' => 'OK'
            ));
            exit();
        }
    }
}

if (isset($_GET['create_backup']))
{
    $dir = realpath('');
    $zip = new ZipArchive;

    if (! file_exists('backups')) mkdir('backups');

    if ($zip->open('backups/backup-' . time() . '.zip', ZipArchive::CREATE) === true)
    {
        foreach (rglob($dir . '/*') as $file)
        {
            $zip->addFile($file);
        }
        $zip->close();

        header('Location: admin.php?admin=updates');
    }
}


function rglob($pattern, $flags = 0)
{
    $files = array_filter(glob($pattern, $flags), 'is_file');

    foreach ( glob(dirname($pattern) . '/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
    {
        if (preg_match('/^.*?\/backups$/i', $dir)) continue;
        $files = array_merge($files, rglob($dir . '/' . basename($pattern), $flags));
    }

    return $files;
}

?>