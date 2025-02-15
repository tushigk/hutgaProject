<?php

function bots_admin_create ( $array = array() )
{
	global $action, $pdo;
	
	$isbotCheck = $pdo->prepare("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PLAYERS . "' AND COLUMN_NAME = 'isbot'");
	$isbotCheck->execute();
	
	if ($isbotCheck->rowCount() == 0)
	{
	    $pdo->exec("ALTER TABLE `" . DB_PLAYERS . "` ADD `isbot` TINYINT(1) NOT NULL DEFAULT '0' AFTER `timetag`;");
	}
	
	
	$botgodCheck = $pdo->prepare("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'botgod'");
	$botgodCheck->execute();
	
	if ($botgodCheck->rowCount() == 0)
    {
        $pdo->exec("ALTER TABLE `" . DB_POKER . "` ADD `botgod` VARCHAR(2) NULL AFTER `dealer`;");
    }

	if ($action === 'createbot' && isset($_POST['cbotname'], $_POST['ctable'], $_POST['cseatnumber'], $_POST['cbotpot']))
	{
	    $botname  = addslashes(preg_replace('/[^A-Za-z0-9_]/', '', $_POST['cbotname']));
	    $tableid  = (int) $_POST['ctable'];
	    $seatno   = (int) $_POST['cseatnumber'];
	    $email    = $botname . '@virtual.poker';
	    $GUID     = randomcode(32);
	    $pwd      = randomcode(7);
	    $approve  = 0;
	    $winpot   = (is_numeric($_POST['cbotpot']) && $_POST['cbotpot'] > 0) ? (int) $_POST['cbotpot'] : transfer_to(100000);
	    $winpot   = transfer_to($winpot);
	    $time     = time();
	    $sessname = (isset($_SESSION['SESSNAME'])) ? addslashes($_SESSION['SESSNAME']) : '';
	    $ip       = $_SERVER['REMOTE_ADDR'];

	    // create bot account
	    $result = $pdo->exec("INSERT INTO " . DB_PLAYERS . " SET isbot = '1', banned = '0', username = '$botname', approve = '$approve', email = '$email', GUID = '$GUID', lastlogin = '$time' , datecreated = '$time' , password = '$pwd', sessname = '$sessname', avatar = 'avatar.jpg', ipaddress = '$ip' ");

	    $result = $pdo->exec("INSERT INTO " . DB_STATS . " SET player = '$botname', winpot = '$winpot' ");

	    $appcode = randomcode(16);
	    $result = $pdo->exec("UPDATE " . DB_PLAYERS . " SET code = '$appcode' WHERE username = '$botname' ");
	    $GLOBALS['bad_msg'] = false;

	    if (is_uploaded_file($_FILES['uploadedfile']['tmp_name']))
	    {
	        $maxsize = 300000;
	        $target_path = "images/avatars/";
	        $target_path = $target_path . basename($_FILES['uploadedfile']['name']);
	        $remote_img  = $_FILES['uploadedfile']['name'];
	        $tmp_img     = $_FILES['uploadedfile']['tmp_name'];
	        $filesize    = filesize($_FILES['uploadedfile']['tmp_name']);
	        
	        list($width, $height, $type, $attr) = getimagesize($tmp_img);
	        if (isset($_FILES['uploadedfile']['tmp_name']) && $type != 2)
	        {
	            $GLOBALS['bad_msg'] = true;
	            $GLOBALS['bad_msgs'] = STATS_MSG_FILE_FORMAT;
	        }

	        if ($filesize > $maxsize)
	        {
	            $GLOBALS['bad_msg'] = true;
	            $GLOBALS['bad_msgs'] = STATS_MSWG_FILE_SIZE;
	        }

	        if ($GLOBALS['bad_msg'] == false)
	        {
	            $target_file = $botname . '.jpg';
	            $target_path = $tmp_img;
	            $result      = $pdo->exec("UPDATE " . DB_PLAYERS . " SET avatar = '" . $target_file . "' WHERE username = '" . $botname . "'");
	            $add         = $target_path;
	            $tsrc        = 'images/avatars/';
	            $im          = ImageCreateFromJPEG($add);
	            $n_width     = 200;
	            $n_height    = 200;
	            $newimage    = imagecreatetruecolor($n_width, $n_height);
	            imageCopyResized($newimage, $im, 0, 0, 0, 0, $n_width, $n_height, $width, $height);
	            ImageJpeg($newimage, $tsrc . $target_file);
	            if (file_exists($tmp_img)) unlink($tmp_img);
	        }
	    }


	    $dq = $pdo->prepare("SELECT p" . $seatno . "name FROM " . DB_POKER . " WHERE gameID = $tableid");
	    $dq->execute();
	    $dr = $dq->fetch(PDO::FETCH_ASSOC);

	    if ($dr['p' . $seatno . 'name'] != '')
	    {
	        $GLOBALS['bad_msg']  = true;
	        $GLOBALS['bad_msgs'] = 'Seat is already taken';
	    }


	    if ($GLOBALS['bad_msg'] == false)
	    {
	        $zq = $pdo->prepare("SELECT * FROM " . DB_POKER . " WHERE gameID = $tableid and (p1name = '$botname' OR p2name = '$botname' OR p3name = '$botname' OR p4name = '$botname' OR p5name = '$botname' OR p6name = '$botname' OR p7name = '$botname' OR p8name = '$botname' OR p9name = '$botname' OR p10name = '$botname')");
	        $zq->execute();

	        // if player is already joined, die
	        if ($zq->rowCount() == 1) return false;

	        $cq = $pdo->prepare("SELECT p".$seatno."name, tablelimit, tablelow, tabletype, hand FROM " . DB_POKER . " WHERE gameID = " . $tableid . " and p".$seatno."name = ''");
	        $cq->execute();

	        // if player hasn't joined yet
	        if ($cq->rowCount() == 1)
	        {
	            $cr     = $cq->fetch(PDO::FETCH_ASSOC);

	            $tablelow   = $cr['tablelow'];
	            $tablelimit = $cr['tablelimit'];
	            $tabletype  = $cr['tabletype'];

	            $hand = $cr['hand'];

	            if ($tabletype == 't' && $hand != '') return false;

	            //mbi $stake = ($winpot > $tablelimit) ? $tablelimit : $winpot;
	            $stake = $winpot;

	            if ($tabletype == 't') $tablelow = $tablelimit;

	            if ($stake >= $tablelow && $stake > 0)
	            {
	                $result = $pdo->exec("UPDATE " . DB_POKER . " SET p".$seatno."name = '$botname', p".$seatno."bet = 'F', p".$seatno."pot = '$stake' WHERE gameID = " . $tableid);
	                $bank   = $winpot-$stake;

	                if ($tabletype == 't')
	                {
	                    $result = $pdo->exec("UPDATE " . DB_STATS . " SET tournamentsplayed = tournamentsplayed + 1, winpot = $bank WHERE player  = '$botname'");
	                }
	                else
	                {
	                    $result = $pdo->exec("UPDATE " . DB_STATS . " SET gamesplayed = gamesplayed + 1, winpot = $bank WHERE player = '$botname'");
	                } 

	                $result = $pdo->exec("UPDATE " . DB_PLAYERS . " SET gID = $tableid, vID = $tableid, lastmove = " . ($time+3) . ", timetag = " . ($time+3) . " WHERE username = '$botname'");
	            }
	        }
	    }
	}
}
// Adding the hook to the sidebar
$addons->add_hook(array(
	'page'     => 'includes/inc_admin.php',
	'location' => 'admin_post',
	'function' => 'bots_admin_create',
));



/**/
function bots_admin_edit( $array = array() )
{
	global $action, $pdo, $addons;

	if ($action === 'editbot' && isset($_POST['ebotname'], $_POST['etable'], $_POST['eseatnumber'], $_POST['ebotpot']))
	{
	    $botname  = addslashes(preg_replace('/[^A-Za-z0-9_]/', '', $_POST['ebotname']));
	    $tableid  = (int) $_POST['etable'];
	    $seatno   = (int) $_POST['eseatnumber'];
	    $email    = $botname . '@virtual.poker';
	    $GUID     = randomcode(32);
	    $pwd      = randomcode(7);
	    $approve  = 0;
	    $time     = time();
	    $sessname = (isset($_SESSION['SESSNAME'])) ? addslashes($_SESSION['SESSNAME']) : '';
	    $ip       = $_SERVER['REMOTE_ADDR'];
	    $GLOBALS['bad_msg']  = false;

	    if (is_uploaded_file($_FILES['uploadedfile']['tmp_name']))
	    {
	        $maxsize     = 300000;
	        $target_path = "images/avatars/";
	        $target_path = $target_path . basename($_FILES['uploadedfile']['name']);
	        $remote_img  = $_FILES['uploadedfile']['name'];
	        $tmp_img     = $_FILES['uploadedfile']['tmp_name'];
	        $filesize    = filesize($_FILES['uploadedfile']['tmp_name']);
	        
	        list($width, $height, $type, $attr) = getimagesize($tmp_img);
	        if (isset($_FILES['uploadedfile']['tmp_name']) && $type != 2)
	        {
	            $GLOBALS['bad_msg']  = true;
	            $GLOBALS['bad_msgs'] = STATS_MSG_FILE_FORMAT;
	        }

	        if ($filesize > $maxsize)
	        {
	            $GLOBALS['bad_msg']  = true;
	            $GLOBALS['bad_msgs'] = STATS_MSWG_FILE_SIZE;
	        }

	        if ($GLOBALS['bad_msg'] == false)
	        {
	            $target_file = $botname . '.jpg';
	            $target_path = $tmp_img;
	            $result      = $pdo->exec("UPDATE " . DB_PLAYERS . " SET avatar = '$target_file' WHERE username = '$botname'");
	            $add         = $target_path;
	            $tsrc        = 'images/avatars/';
	            $im          = ImageCreateFromJPEG($add);
	            $n_width     = 200;
	            $n_height    = 200;
	            $newimage    = imagecreatetruecolor($n_width, $n_height);
	            imageCopyResized($newimage, $im, 0, 0, 0, 0, $n_width, $n_height, $width, $height);
	            ImageJpeg($newimage, $tsrc . $target_file);
	            if (file_exists($tmp_img)) unlink($tmp_img);
	        }
	    }

	    if ($GLOBALS['bad_msg'] == false)
	    {
	        $dq = $pdo->prepare("SELECT p" . $seatno . "name FROM " . DB_POKER . " WHERE gameID = $tableid");
	        $dq->execute();
	        $dr = $dq->fetch(PDO::FETCH_ASSOC);

	        if ($dr['p' . $seatno . 'name'] != '' && $dr['p' . $seatno . 'name'] != $botname)
	        {
	            $GLOBALS['bad_msg']  = true;
	            $GLOBALS['bad_msgs'] = 'Seat is already taken';
	        }

	        if ($GLOBALS['bad_msg'] == false)
	        {
	        	$tpq = $pdo->prepare("SELECT * FROM " . DB_POKER . " WHERE (p1name = '$botname' OR p2name = '$botname' OR p3name = '$botname' OR p4name = '$botname' OR p5name = '$botname' OR p6name = '$botname' OR p7name = '$botname' OR p8name = '$botname' OR p9name = '$botname' OR p10name = '$botname')");
	            $tpq->execute();

	            // if bot is already joined in a table, force it to leave
	            if ($tpq->rowCount() == 1)
	            {
	                $tpr    = $tpq->fetch(PDO::FETCH_ASSOC);
	                $yGid   = $tpr['gameID'];
	                $tomove = $tpr['move'];

	                for ($zi = 1; $zi < 11; $zi++)
	                {
	                    if ($tpr['p' . $zi . 'name'] == $botname)
	                    {
	                    	if ($yGid != $tableid) sys_msg('<span class="chatName">' . $botname . '</span> leaves the table', $yGid);
	                        $pot      = $tpr['p' . $zi . 'pot'];
	                        $statsq   = $pdo->prepare("select winpot from " . DB_STATS . " where player = '" . $botname . "' "); $statsq->execute();
	                        $statsr   = $statsq->fetch(PDO::FETCH_ASSOC);
	                        $winnings = $statsr['winpot'];
	                        $winpot   = $pot;
	                        $winpot  += $winnings;

	                        $winpot   = $addons->get_hooks(
	                            array(
	                                'content' => $winpot,
	                            ),

	                            array(
	                                'page'     => 'includes/inc_poker.php',
	                                'location'  => 'winpot_var',
	                            )
	                        );

	                        $result   = $pdo->exec("update " . DB_STATS . " set winpot = " . $winpot . " where player  = '" . $botname . "' ");

	                        if ($tomove == $zi)
	                        {
	                            $nxtp   = nextplayer($zi);
	                            $result = $pdo->exec("update " . DB_POKER . " set p" . $zi . "name = '', p" . $zi . "bet = '', p" . $zi . "pot = '', move = '" . $nxtp . "', lastmove = " . $time . "  where gameID = " . $yGid);
	                        }
	                        else
	                        {
	                            $result = $pdo->exec("update " . DB_POKER . " set p" . $zi . "name = '', p" . $zi . "bet = '', p" . $zi . "pot = '', lastmove = " . $time . " where gameID = " . $yGid);
	                        }

	                        break;
	                    }
	                }
	            }

	            $cq = $pdo->prepare("SELECT p".$seatno."name, tablelimit, tablelow, tabletype, hand FROM " . DB_POKER . " WHERE gameID = " . $tableid . " and p".$seatno."name = ''");
	            $cq->execute();

	            // if player hasn't joined yet
	            if ($cq->rowCount() == 1)
	            {
	                $cr     = $cq->fetch(PDO::FETCH_ASSOC);

	                $winpot     = (is_numeric($_POST['ebotpot']) && $_POST['ebotpot'] > 0) ? (int) $_POST['ebotpot'] : transfer_to(100000);
	                $winpot     = transfer_to($winpot);
	                $tablelow   = $cr['tablelow'];
	                $tablelimit = $cr['tablelimit'];
	                $tabletype  = $cr['tabletype'];

	                $hand = $cr['hand'];

	                if ($tabletype == 't' && $hand != '') return false;

	                //mbi $stake = ($winpot > $tablelimit) ? $tablelimit : $winpot;
	                $stake = $winpot;

	                if ($tabletype == 't') $tablelow = $tablelimit;

	                if ($stake >= $tablelow && $stake > 0)
	                {
	                    $result = $pdo->exec("UPDATE " . DB_POKER . " SET p".$seatno."name = '$botname', p".$seatno."bet = 'F', p".$seatno."pot = '$stake' WHERE gameID = " . $tableid);
	                    $bank   = $winpot-$stake;

	                    if ($tabletype == 't')
	                    {
	                        $result = $pdo->exec("UPDATE " . DB_STATS . " SET tournamentsplayed = tournamentsplayed + 1, winpot = $bank WHERE player  = '$botname'");
	                    }
	                    else
	                    {
	                        $result = $pdo->exec("UPDATE " . DB_STATS . " SET gamesplayed = gamesplayed + 1, winpot = $bank WHERE player = '$botname'");
	                    } 

	                    $result = $pdo->exec("UPDATE " . DB_PLAYERS . " SET gID = $tableid, vID = $tableid, lastmove = " . ($time+3) . ", timetag = " . ($time+3) . " WHERE username = '$botname'");
	                }
	            }
	        }
	    }
	}
}
// Adding the hook to the sidebar
$addons->add_hook(array(
	'page'     => 'includes/inc_admin.php',
	'location' => 'admin_post',
	'function' => 'bots_admin_edit',
));