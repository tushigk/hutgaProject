<?php
ini_set('display_errors', false);
error_reporting(0);

function pow_db_connect($server, $username, $password, $link = 'db_link')
{
    global $$link, $db_error;
    $db_error = false;

    if (!$server)
    {
        $db_error = 'No Server selected.';
        return false;
    }

    $$link = @mysqli_connect($server, $username, $password) or $db_error = mysqli_connect_error();
    return $$link;
}

function pow_db_error($link = 'db_link')
{
    global $$link;
    return mysqli_error($$link);
}

function pow_db_select_db($database, $link = 'db_link')
{
    global $$link;
    return mysqli_select_db($$link, $database);
}

function pow_db_close($link = 'db_link')
{
    global $$link;
    return mysqli_close($$link);
}

function pow_db_query($query, $link = 'db_link')
{
    global $$link;
    return mysqli_query($$link, $query);
}

function pow_db_fetch_array($db_query)
{
    return mysqli_fetch_array($db_query);
}

function pow_db_num_rows($db_query)
{
    return mysqli_num_rows($db_query);
}

function pow_db_data_seek($db_query, $row_number)
{
    return mysqli_data_seek($db_query, $row_number);
}

function pow_db_insert_id()
{
    return mysqli_insert_id();
}

function pow_db_free_result($db_query)
{
    return mysqli_free_result($db_query);
}

function pow_db_test_create_db_permission($database)
{
    global $db_error;

    $message    = 'Setup has detected that the database already contains information. If you have run this installer before, then the database may already be setup. If this is the case you must rerun the installer and uncheck the database setup check box. <BR><BR>If this is a new installation or a complete reinstall then you must use your MySQL configuration tool on your Web Server Control Panel to empty the database of all tables and data. If you are unsure how to do this then please contact your Web Hosting Company for assistance';
    $db_created = false;
    $db_error   = false;

    if (!$database)
    {
        $db_error = 'No Database selected.';
        return false;
    }

    if (!$db_error)
    {
        if ( !@pow_db_select_db($database) )
        {
            $db_created = true;

            if (!@pow_db_query('create database ' . $database))
            {
                $db_error = pow_db_error();
            }
        }
        else
        {
            $db_error = pow_db_error();
        }

        if ( !$db_error )
        {
            if ( @pow_db_select_db($database) )
            {
                if ( @pow_db_query('create table temp ( temp_id int(5) )') )
                {
                    if ( @pow_db_query('drop table temp') )
                    {
                        if ( $db_created )
                        {
                            if ( @pow_db_query('drop database ' . $database) )
                            {
                                //
                            }
                            else
                            {
                                $db_error = $message;
                            }
                        }
                    }
                    else
                    {
                        $db_error = $message;
                    }
                }
                else
                {
                    $db_error = $message;
                }
            }
            else
            {
                $db_error = pow_db_error();
            }
        }
    }

    if ($db_error)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function pow_db_test_connection($database)
{
    global $db_error;
    $db_error = false;

    if ( !$db_error )
    {
        if ( !@pow_db_select_db($database) )
        {
            $db_error = pow_db_error();
        }
        else
        {
            if (!@pow_db_query('select count(*) from configuration'))
            {
                $db_error = pow_db_error();
            }
        }
    }

    if ($db_error)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function pow_db_install($database, $sql_file)
{
    global $db_error;
    $db_error = false;

    if ( !@pow_db_select_db($database) )
    {
        if (@pow_db_query('create database ' . $database))
        {
            pow_db_select_db($database);
        }
        else
        {
            $db_error = pow_db_error();
        }
    }

    if ( !$db_error )
    {
        if (file_exists($sql_file))
        {
            $fd            = fopen($sql_file, 'rb');
            $restore_query = fread($fd, filesize($sql_file));
            fclose($fd);
        }
        else
        {
            $db_error = 'SQL file does not exist: ' . $sql_file;
            return false;
        }

        $sql_array  = array();
        $sql_length = strlen($restore_query);
        $pos        = strpos($restore_query, ';');

        for ($i = $pos; $i < $sql_length; $i++)
        {
            if ($restore_query[0] == '#')
            {
                $restore_query = ltrim(substr($restore_query, strpos($restore_query, "\n")));
                $sql_length    = strlen($restore_query);
                $i             = strpos($restore_query, ';') - 1;
                continue;
            }

            if ($restore_query[($i + 1)] == "\n")
            {
                for ($j = ($i + 2); $j < $sql_length; $j++)
                {
                    if (trim($restore_query[$j]) != '')
                    {
                        $next = substr($restore_query, $j, 6);

                        if ($next[0] == '#')
                        {
                            for ($k = $j; $k < $sql_length; $k++)
                            {
                                if ($restore_query[$k] == "\n")
                                {
                                    break;
                                }
                            }

                            $query         = substr($restore_query, 0, $i + 1);
                            $restore_query = substr($restore_query, $k);
                            $restore_query = $query . $restore_query;
                            $sql_length    = strlen($restore_query);
                            $i             = strpos($restore_query, ';') - 1;

                            continue 2;
                        }

                        break;
                    }
                }

                if ($next == '')
                {
                    $next = 'insert';
                }

                if (preg_match('/create/i', $next) || preg_match('/insert/i', $next) || preg_match('/drop t/i', $next))
                {
                    $next          = '';
                    $sql_array[]   = substr($restore_query, 0, $i);
                    $restore_query = ltrim(substr($restore_query, $i + 1));
                    $sql_length    = strlen($restore_query);
                    $i             = strpos($restore_query, ';') - 1;
                }
            }
        }

        for ($i = 0; $i < sizeof($sql_array); $i++)
        {
            pow_db_query($sql_array[$i]);
        }
    }
    else
    {
        return false;
    }
}

$dbs      = '';
$dbname   = '';
$dbusr    = '';
$dbpwd    = '';
$adminusr = '';

$action   = (isset($_GET['action'])) ? addslashes($_GET['action']) : '';

if ($action == 'install')
{
    $error    = false;
    $dbs      = (isset($_POST['dbserver'])) ? addslashes($_POST['dbserver']) : '';
    $dbname   = (isset($_POST['dbname'])) ? addslashes($_POST['dbname']) : '';
    $dbusr    = (isset($_POST['dbusr'])) ? addslashes($_POST['dbusr']) : '';
    $dbpwd    = (isset($_POST['dbpwd'])) ? addslashes($_POST['dbpwd']) : '';
    $adminusr = (isset($_POST['plyr'])) ? addslashes($_POST['plyr']) : '';

    if ($dbs == '' || $dbname == '' || $dbusr == '' || $adminusr == '')
    {
        $msg   = 'Missing fields! Please try again.<br>';
        $error = true;
    }

    $ws        = substr_count($adminusr, 'w');
    $ms        = substr_count($adminusr, 'm');
    $Ws        = substr_count($adminusr, 'M');
    $Ms        = substr_count($adminusr, 'W');
    $longchars = $ws + $ms + $Ws + $Ms;

    if ($longchars > 4 && strlen($adminusr) > 6)
    {
        $error = true;
        $msg  .= 'Player name has too many m\'s or w\'s in it!.<BR>';
    }
    elseif (preg_match('/[^a-zA-Z0-9_]/i', $adminusr))
    {
        $error = true;
        $msg  .= 'Player names can contain letters, numbers and underscores.<BR>';
    }
    elseif (strlen($adminusr) > 10 || strlen($adminusr) < 5)
    {
        $error = true;
        $msg  .= 'Player names must be 5-10 characters long.<BR>';
    }

    if ($error == false)
    {
        $action          = 'process';
        $script_filename = getenv('PATH_TRANSLATED');

        if (empty($script_filename))
        {
            $script_filename = getenv('SCRIPT_FILENAME');
        }

        $script_filename       = str_replace('\\', '/', $script_filename);
        $script_filename       = str_replace('//', '/', $script_filename);
        $dir_fs_www_root_array = explode('/', dirname($script_filename));
        $dir_fs_www_root       = array();

        for ($i = 0, $n = sizeof($dir_fs_www_root_array) - 1; $i < $n; $i++)
        {
            $dir_fs_www_root[] = $dir_fs_www_root_array[$i];
        }

        $dir_fs_www_root          = implode('/', $dir_fs_www_root) . '/';
        $db                       = array();
        $db['DB_SERVER']          = $dbs;
        $db['DB_SERVER_USERNAME'] = $dbusr;
        $db['DB_SERVER_PASSWORD'] = $dbpwd;
        $db['DB_DATABASE']        = $dbname;

        pow_db_connect($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);
        $db_error = false;
        $sql_file = $dir_fs_www_root . 'install/poker.sql';
        pow_db_install($db['DB_DATABASE'], $sql_file);

        $file_contents = '<?php 
' . "\n" . '  
define(\'DB_SERVER\', \'' . $dbs . '\');
' . "\n" . '
define(\'DB_SERVER_USERNAME\', \'' . $dbusr . '\');
' . "\n" . '
define(\'DB_SERVER_PASSWORD\', \'' . $dbpwd . '\');
' . "\n" . '
define(\'DB_DATABASE\', \'' . $dbname . '\');
' . "\n" . '
define(\'ADMIN_USERS\', \'' . $adminusr . '\');
' . "\n" . '
// Additional administrators can be added by seperating admin usernames with commas.
' . "\n" . '
define(\'MONEY_PREFIX\', \'$\');
' . "\n" . '
define(\'MONEY_DECIMAL\', \'.\');
' . "\n" . '
define(\'MONEY_THOUSAND\', \'.\');
' . "\n" . '
?>';
        $fp            = fopen($dir_fs_www_root . 'includes/configure.php', 'w');
        fputs($fp, $file_contents);
        fclose($fp);
    }
    else
    {
        $action = 'setup';
    }
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
	<title>OnlinePokerScript V2 Installation</title>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	<style>
	body {
		padding-top: 20px;
		padding-bottom: 20px;
	}

	.navbar {
		margin-bottom: 20px;
	}
	</style>
</head>

<body>

	<div class="container">

		<!-- Static navbar -->
		<nav class="navbar navbar-default" role="navigation">

			<div class="container-fluid">

				<div class="navbar-header">

					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">

						<span class="sr-only">Toggle navigation</span>

						<span class="icon-bar"></span>

						<span class="icon-bar"></span>

						<span class="icon-bar"></span>

					</button>

					<a class="navbar-brand" href="#">OnlinePokerScript V2 - Installation</a>

				</div>

				<div id="navbar" class="navbar-collapse collapse"></div><!--/.nav-collapse -->

			</div><!--/.container-fluid -->

		</nav>

	</div> <!-- /container -->        

	<?php if ($action == '') { ?> 

	<div class="container">
		Before you begin installation, please check that the file includes/configure.php is CHMOD to <b>0777</b> (writable).
		<p>
			<form name="form1" method="post" action="index.php?action=setup">
				<input type="submit" name="Submit" value="Continue" class="btn btn-success">
			</form>
		</p>
	</div>

	<?php } elseif ($action == 'setup') { ?>

	<div class="container">

		<div class="alert alert-info">Please supply the following installation information.</div>

		<div class="col-md-6">

			<form name="form1" method="post" action="index.php?action=install">

				<input class="form-control" placeholder="Database Server" type="text" name="dbserver" size="30" maxlength="40" value="<?php echo $dbs; ?>">
				Either localhost, the url or the IP Address of your database server.<br><br>

				<input class="form-control" placeholder="Database Name" type="text" name="dbname" size="30" maxlength="40" value="<?php echo $dbname; ?>">
				The name of your database.<br><br>

				<input class="form-control" placeholder="Database Username" type="text" name="dbusr" size="30" maxlength="40" value="<?php echo $dbusr; ?>">
				Your database access username.<br><br>

				<input class="form-control" placeholder="Database Password" type="password" name="dbpwd" size="30" maxlength="40" value="<?php echo $dbpwd; ?>">
				Your database access password.<br><br>

				<input class="form-control" placeholder="Your Player Name" type="text" name="plyr" size="20" maxlength="10" value="<?php echo $adminusr; ?>">
				Your chosen player name (5-10 alphanumeric chars) will inserted 
				as site administrator. 
				<br><br>

				<p>When the game is installed, create a new player using this administrator name and this player will have sole admin access.<br>Additional administrators can be added by editing the configure.php file</p>

				<input type="submit" name="Submit" value="Continue" class="btn btn-success">

			</form>

		</div>

	</div>

	<?php } elseif ($action == 'process') {

		if ($db_error != false) { ?>

		<p>The installation of the database data was <font color="#990000"><b>NOT</b> 
		<b>successful</b></font>. <br>
		<br>
		The following error has occurred:</p>

		<p class="alert alert-danger"><?php echo $db_error; ?></p>

		<input type=button value="Back" onClick="history.go(-1)" class="btn btn-danger">

		<br>

		<p>&nbsp;</p>

		<?php } else { ?>

		<div class="container">

			<form name="install" action="../index.php" method="post">

				<p>The installation of the database data was <b><span class="label label-success">successful</span></b></p>

				<p>Please click the continue button to proceed to continue to the game 
				and then make sure you delete the install folder from your server and 
				CHMOD the includes/configure.php file to <b>0644</b>.</p>

				<input type="hidden" name="hid" value="4">

				<input type="submit" name="Submit" value="Continue" class="btn btn-success">

			</form>

		</div>

	<?php }
	}
	?>

</body>
</html>
