<?php
$addons->get_hooks(array(), array(

	'page'     => 'create.php',
	'location'  => 'page_start'

));

if ( $valid == true )
{
	header('Location: index.php');
}

$time    = time();
$message = '';

$action  = (isset($_POST['action']))     ? addslashes($_POST['action'])    : '';
$usr     = (isset($_POST['user']))       ? addslashes($_POST['user'])      : '';
$pwd     = (isset($_POST['password']))   ? addslashes($_POST['password'])  : '';
$pwd2    = (isset($_POST['password2']))  ? addslashes($_POST['password2']) : '';
$avatar  = (isset($_POST['av']))         ? addslashes($_POST['av'])        : '';
$avatar  = ($avatar > 0 && $avatar < 17) ? 'avatar' . $avatar . '.jpg'     : '';
$email   = '';

// Create new player/account
if ($action == 'createplayer')
{
	$sessname  = (isset($_SESSION['SESSNAME'])) ? addslashes($_SESSION['SESSNAME']) : '';
    $email     = (isset($_POST['email']))       ? addslashes($_POST['email'])       : '';
	$Semail    = ($email == '')                 ? 99                                : $email;

    $ip        = $_SERVER['REMOTE_ADDR']; 
	$ws        = substr_count($usr, 'w');
	$ms        = substr_count($usr, 'm');
	$Ws        = substr_count($usr, 'M');
	$Ms        = substr_count($usr, 'W');
	$longchars = $ws + $ms + $Ws + $Ms;
	$error     = false;

	// Check if there is an account already with ip address/email
	$banq = $pdo->prepare("select ipaddress from " . DB_PLAYERS . " where (ipaddress = '" . $ip . "' or email = '" . $Semail . "' ) and banned = '1' ");
	$banq->execute();

	// Check if there is an account already with username
	$userq = $pdo->prepare("select username from " . DB_PLAYERS . " where username = '".$usr."' ");
	$userq->execute();

	// Check if account is already logged in elsewhere
	$sessq = $pdo->prepare("select username from " . DB_PLAYERS . " where sessname = '".$sessname."' ");
	$sessq->execute();


	// check for errors
	if ( $banq->rowCount() > 0 && $ADMIN == false )
	{
		$error = true;
	    $message = CREATE_MSG_IP_BANNED;
	}
	elseif (( $pwd == '' || $pwd2 == '' || $usr == '' || ( $email == '' && EMAILMOD == 1)) && MEMMOD != 1)
	{
		$error = true;
	    $message = CREATE_MSG_MISSING_DATA;
	}
	elseif (strlen($sessname) < 2 && MEMMOD == 1)
	{
	    $error = true;
	    $message = CREATE_MSG_AUTHENTICATION_ERROR;
	}
	elseif ($sessq->rowCount() > 0 && MEMMOD == 1)
	{
	    $error = true;
	    $message = CREATE_MSG_ALREADY_CREATED;
	}
	else
	{
		if (( !preg_match("(^[-\w\.]+@([-a-z0-9]+\.)+[a-z]{2,4}$)i", $email) || strlen($email) < 8) && ( APPMOD == 1 || EMAILMOD == 1 ))
		{
		    $error = true;
		    $message .= CREATE_MSG_INVALID_EMAIL . '<br>';
		} 

		if ($userq->rowCount() != 0)
		{
			$error = true;
		    $message .= CREATE_MSG_USERNAME_TAKEN . '<br>';
		}

		if ($longchars  > 4 && strlen($usr) > 6)
		{ 
			$error = true;
		    $message .= CREATE_MSG_USERNAME_MWCHECK . '<br>';
		} 

		if (preg_match('/[^a-zA-Z0-9_]/i', $usr))
		{
			$error = true;
		    $message .= CREATE_MSG_USERNAME_CHARS . '<br>';
		}

		if (preg_match('/[^a-zA-Z0-9_]/i', $pwd) && MEMMOD != 1)
		{
			$error = true;
			$pwd = '';
			$pwd2 = '';
		    $message .= CREATE_MSG_PASSWORD_CHARS . '<br>';
		} 

		if ($pwd != $pwd2 && MEMMOD != 1)
		{
			$error = true;
			$pwd = '';
			$pwd2 = '';
		    $message .= CREATE_MSG_PASSWORD_CHECK . '<br>';
		} 

		if ( strlen($usr) < 5 || strlen($usr) > 10 )
		{
		    $error = true;
		    $message .= CREATE_MSG_USERNAME_LENGTH . '<br>';
		}

		if (( strlen($pwd) < 5 || strlen($pwd) > 10 )  && MEMMOD != 1)
		{
		    $error .= true;
			$pwd = '';
			$pwd2 = '';
		    $message = CREATE_MSG_PASSWORD_LENGTH . '<br>';
		}
	}


	// if no error exists
	if ($error == false)
	{
		$GUID    = randomcode(32);
		$pwd     = (MEMMOD == 1) ? ''    : encrypt_password($pwd);
		$approve = (APPMOD != 0) ? 1     : 0;
		$winpot  = (RENEW == 1)  ? 10000 : 0;

		$addons->get_hooks(
			array(),
			array(
				'page'     => 'create.php',
				'location'  => 'before_register'
			)
		);

		// create player account
		$result = $pdo->exec("insert into " . DB_PLAYERS . " set banned = '0', username = '".$usr."', approve = '".$approve."', email = '".$email."', GUID = '".$GUID."', lastlogin = '".$time."' , datecreated = '".$time."' , password = '".$pwd."', sessname = '".$sessname."', avatar = 'avatar.jpg', ipaddress = '".$ip."' ");

		if ($pdo->query("SELECT ID FROM " . DB_STATS . " WHERE player = '" . $usr . "'")->rowCount() === 0)
			$pdo->exec("insert into ".DB_STATS." set player = '".$usr."', winpot = '".$winpot."' ");

		$addons->get_hooks(
			array(),
			array(
				'page'     => 'create.php',
				'location'  => 'after_register'
			)
		);

		if (APPMOD == 1) // REQUIRE EMAIL VERIFICATION
		{
			$appcode = randomcode(16);
			
			$result = $pdo->exec("update " . DB_PLAYERS . " set code = '".$appcode."' where username = '".$usr."' ");

			// send email
			$mailfrom    = $_SERVER['HTTP_HOST'];
			$mailfrom    = str_replace("www.","",$mailfrom);
			$mailf       = explode('/',$mailfrom);
			$mail        = 'support@'.$mailf[0];

			$from_header = 'From: '.$mail. "\r\n" .
			   'Reply-To: '.$mail . "\r\n" .
			   'X-Mailer: PHP/' . phpversion();

			$subject  = 'Player Activation Email';
			$url      = str_replace('create.php', "approve.php?&approval=$appcode", 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);
			$contents = CREATE_APPROVAL_EMAIL_CONTENT . ' ' . $url;
			$to       = $email;

			mail($to, $subject, $contents, $from_header);

			$url = 'index.php';
			echo '<script type="text/javascript">';
			echo 'alert(\'' . CREATE_APPROVAL_EMAIL_ALERT . '\'); ';
			echo 'parent.document.location.href = "' . $url . '";'; 
			echo '</script>';
			die(); 
		}
		elseif (APPMOD == 2) // REQUIRE ADMIN VERIFICATION
		{
			header('Location: login.php');
			die(); 			
		} else
		{
			$_SESSION['SGUID']      = $GUID;
			$_SESSION['playername'] = $usr;
			header('Location: lobby.php');
			die(); 
		}
	}
}


if ( $message != '' ) {
	$opsTheme->addVariable('message_text', $message);
	$opsTheme->addVariable('message', $opsTheme->viewPart('register-message'));
}

$opsTheme->addVariable('create_player_label', BOX_CREATE_NEW_PLAYER);
$opsTheme->addVariable('create_player_name_label', CREATE_PLAYER_NAME);
$opsTheme->addVariable('create_player_name', $usr);
$opsTheme->addVariable('create_player_password_charlimit', CREATE_PLAYER_CHAR_LIMIT);
$opsTheme->addVariable('create_player_password_label', CREATE_PLAYER_PWD);
$opsTheme->addVariable('create_player_password_confirm_label', CREATE_PLAYER_CONFIRM);
$opsTheme->addVariable('create_player_submit_label', BUTTON_SUBMIT);

if ( EMAILMOD == 1 ) {
	$opsTheme->addVariable('create_player_email_label', CREATE_PLAYER_EMAIL);
	$opsTheme->addVariable('create_player_email', $email);
	$opsTheme->addVariable('email_input', $opsTheme->viewPart('register-email-input'));
}

include 'templates/header.php';

echo $addons->get_hooks(array(), array(

	'page'     => 'create.php',
	'location'  => 'html_start'

));

echo $opsTheme->viewPage('register');

echo $addons->get_hooks(array(), array(

	'page'     => 'create.php',
	'location'  => 'html_end'

));

include 'templates/footer.php';
?>

</body>
</html>
