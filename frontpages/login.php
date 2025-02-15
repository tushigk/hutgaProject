<?php 
$addons->get_hooks(array(), array(

    'page'     => 'login.php',
    'location'  => 'page_start'

));

$action = (isset($_GET['action'])) ? addslashes($_GET['action']) : (isset($_POST['action'])) ? $_POST['action'] : '';
$usr    = (isset($_POST['usr']))   ? addslashes($_POST['usr'])   : '';
$pwd    = (isset($_POST['pwd']))   ? addslashes($_POST['pwd'])   : '';

$time   = time();
$ip     = $_SERVER['REMOTE_ADDR'];
$msg    = '';

// log in
if ($action == 'process' && $usr != '' && $pwd != '')
{
	$stmt = $pdo->prepare("SELECT password, banned, approve FROM " . DB_PLAYERS . " WHERE username = '$usr'");
	$stmt->execute();
	
	$pwdq    = $stmt->fetch(PDO::FETCH_ASSOC);
	$orig    = $pwdq['password'];
	$banned  = $pwdq['banned'];
	$approve = $pwdq['approve'];
	$GUID    = randomcode(32);

	// if approval needed
	if ($approve == 1)
	{
		$msg = LOGIN_MSG_APPROVAL;
	}
	// if banned
	elseif ($banned == 1)
	{
		$msg = LOGIN_MSG_BANNED;
	}
	// if password is correct
	elseif (validate_password($pwd,$orig) == true)
	{
		$_SESSION['playername'] = $usr; 
		$_SESSION['SGUID']      = $GUID;
		
		// update ip address, last login time of logged user
		$result = $pdo->exec("update " . DB_PLAYERS . " set ipaddress = '" . $ip . "', lastlogin = " . $time . " , GUID = '" . $GUID . "' where username = '" . $usr . "'");

		header('Location: lobby.php');
	}
	else
	{
		// invalid login details
		$msg = LOGIN_MSG_INVALID;
	}
}

include 'templates/header.php';

echo $addons->get_hooks(array(), array(

    'page'     => 'login.php',
    'location'  => 'html_start'

));


$fpError   = '';
$fpSuccess = '';
// Forgot password
if (isset($_POST['submit']))
{
	if (!isset($_POST['forgotpassword']) || $_POST['forgotpassword'] == '')
	{
		$fpError = 'Please enter your email address.';
	}

	if ($fpError == '' && isset($_POST['forgotpassword']))
	{
		if (get_magic_quotes_gpc())
		{
			$forgotpassword = htmlspecialchars(stripslashes($_POST['forgotpassword']));
		}
		else
		{
			$forgotpassword = htmlspecialchars($_POST['forgotpassword']);
		}

		//Make sure it's a valid email address, last thing we want is some sort of exploit!
		if (! check_email_address($_POST['forgotpassword']))
		{
	  		$fpError = 'Email Not Valid - Must be in format of name@domain.tld';
		}

	    // Lets see if the email exists
	    $sql = "SELECT COUNT(*) AS cid FROM players WHERE email = '$forgotpassword'";
	    $result = $pdo->prepare($sql);
	    $result->execute();
	    $fetch = $result->fetch(PDO::FETCH_ASSOC);

	    if ($fetch['cid'] == 0)
	    {
	        $fpError = 'Email Not Found!';
	    }

		//Generate a RANDOM MD5 Hash for a password
		$random_password = md5(uniqid(rand()));
		
		//Take the first 8 digits and use them as the password we intend to email the user
		$emailpassword = substr($random_password, 0, 8);
		
		//Encrypt $emailpassword in MD5 format for the database
		$newpassword = encrypt_password($emailpassword);

	    // Make a safe query
	   	$query = "UPDATE `players` SET `password` = ? WHERE `email` = ?";
	    $pdo->prepare($query)->execute(array($newpassword, $forgotpassword));

		//Email out the infromation
		$subject = "Your New Password"; 
		$message = "Your new password is as follows:
	---------------------------- 
	Password: {$emailpassword}

	---------------------------- 
	Please change your password when you login via your account page.

	This email was automatically generated."; 
	                       
		if (! mail($forgotpassword, $subject, $message,  "FROM: OnlinePokerScript.com"))
		{ 
			$fpError = "Sending Email Failed, Please Contact Site Admin! ({$site_email})";
		}
		else
		{ 
			$fpSuccess = 'New Password Sent!';
		}
	}
}
$opsTheme->addVariable('fp_error', $fpError);
$opsTheme->addVariable('fp_success', $fpSuccess);

$opsTheme->addVariable('login_label',            LOGIN);
$opsTheme->addVariable('login_user_label',       LOGIN_USER);
$opsTheme->addVariable('login_password_label',   LOGIN_PWD);
$opsTheme->addVariable('login_button_label',     BUTTON_LOGIN);
$opsTheme->addVariable('login_new_player_label', LOGIN_NEW_PLAYER);

if ($msg != '')
{
	$opsTheme->addVariable('login_msg', $msg);
	$opsTheme->addVariable('login_message', $opsTheme->viewPart('login-message'));
}

echo $opsTheme->viewPage('login');

echo $addons->get_hooks(array(), array(
    'page'     => 'login.php',
    'location' => 'html_end'
));

include 'templates/footer.php';
?>
