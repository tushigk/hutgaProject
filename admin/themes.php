<?php
// Install new theme
if (isset($_POST['install_theme']) && is_uploaded_file($_FILES['theme_zip_file']['tmp_name']))
{
	$file = $_FILES['theme_zip_file'];
	$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
	$done = false;

	if ($extension === 'zip')
	{
		$themeDir = 'themes/';
		$zip = new ZipArchive;
		
		if ($zip->open($file['tmp_name']) === TRUE)
		{
			$zip->extractTo($themeDir);
			$zip->close();
			$done = true;

			header('Location: ?admin=themes&install=success');
		}
		else
		{
			header('Location: ?admin=themes&install=failed');
		}
	}
}


// Activate / Deactivate Themes
if (isset($_POST['change_theme_status'], $_POST['theme'], $_POST['activate']))
{
	$theme = str_replace('/[^A-Za-z0-9_-]/i', '', $_POST['theme']);
	$themeDir = "themes/{$theme}";

	if (file_exists($themeDir))
	{
		$pdo->query("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$theme}' WHERE setting = 'theme' AND Xkey = 'THEME'");
		echo '<script type="text/javascript">parent.document.location.href = "?admin=themes&activated=yes";</script>';
		die();
	}
}


// Update Theme
if (isset($_GET['update_theme']))
{
	$theme     = $_GET['update_theme'];
	$themeDir  = 'themes/' . str_replace('/[^A-Za-z0-9_-]/i', '', $theme);
	$infoFile  = $themeDir . '/info.json';
	$zipFile   = $themeDir . '/update.zip';
	$themePath = realpath($themeDir);

	if (file_exists($infoFile))
	{
		$themeInfo       = json_decode(file_get_contents($infoFile));
		$themeUpdateUrl  = $themeInfo->update_url;
		$themeUpdateJson = json_decode(file_get_contents_ssl($themeUpdateUrl, array(
			'ip'       => get_user_ip_addr(),
	        'domain'   => preg_replace('/[^A-Za-z0-9-.]/i', '', $_SERVER['SERVER_NAME']),
	        'license'  => LICENSEKEY,
	        'version'  => $themeInfo->version,
	        'download' => true
		)));

		if (isset($themeUpdateJson->status) && $themeUpdateJson->status === "OK")
		{
			if (file_put_contents($zipFile, file_get_contents_ssl($themeUpdateJson->url)))
			{
				$zip = new ZipArchive;
    			
    			if ($zip->open($zipFile) === true)
				{
					$themeInfo->version = $themeUpdateJson->version;
					file_put_contents($infoFile, json_encode($themeInfo));

					$zip->extractTo($themePath);
					$zip->close();

					unlink($zipFile);

					$pdo->exec("UPDATE " . DB_SETTINGS . " SET Xvalue = '0' WHERE setting = 'themeupdatea' AND Xkey = 'THEMEUPDATEA'");
					header('Location: admin.php?admin=themes');
				}
			}
		}
	}
}


if (class_exists('ZipArchive'))
	$install = $opsTheme->viewPart('admin-theme-install');
else
	$install = '<p>You need to enable Zip extension in your server</p>';

$opsTheme->addVariable('install', $install);


$rows         = '';
$themeFolders = glob('themes/*', GLOB_ONLYDIR);

foreach ($themeFolders as $themeFolder)
{
	$infos			 = (object) array();
	$themeName		 = str_replace('themes/', '', $themeFolder);
	$themeInfoFile	 = $themeFolder . '/info.json';
	
	if (file_exists($themeInfoFile))
	{
		$themeInfo		= file_get_contents($themeInfoFile);
		$infos			= json_decode($themeInfo);
	}

	$empty = '<small>empty</small>';

	$infos->id          = $themeName;
	$infos->name	    = (isset($infos->name))		   ? $infos->name		 : $empty;
	$infos->description = (isset($infos->description)) ? $infos->description : $empty;
	$infos->author	    = (isset($infos->author))	   ? $infos->author	     : $empty;
	$infos->version     = (isset($infos->version))	   ? $infos->version	 : $empty;

	$opsTheme->addVariable('theme', $infos);

	$activated = false;
	$update    = false;

	if ($themeName == THEME)
		$activated = true;

	if (THEMEUPDATEA == '1' && isset($infos->update_url))
	{
		$themeUpdateUrl  = $infos->update_url;
		$themeUpdateJson = json_decode(file_get_contents_ssl($themeUpdateUrl, array(
			'ip'      => get_user_ip_addr(),
	        'domain'  => preg_replace('/[^A-Za-z0-9-.]/i', '', $_SERVER['SERVER_NAME']),
	        'license' => LICENSEKEY,
	        'version' => $infos->version,
		)));

		if (isset($themeUpdateJson->status) && $themeUpdateJson->status === "OK")
			$update = true;
	}

	if ($activated)
		$infos->activate = $opsTheme->viewPart('admin-theme-activated-button');
	else
		$infos->activate = $opsTheme->viewPart('admin-theme-activate-button');

	if ($update)
		$infos->update = $opsTheme->viewPart('admin-theme-update-button');
	else
		$infos->update = '<span>Up to date</span>';

	$opsTheme->addVariable('theme', $infos);

	$rows .= $opsTheme->viewPart('admin-theme-row-each');
}

$opsTheme->addVariable('rows', $rows);

echo $opsTheme->viewPage('admin-themes');
?>