<?php
$currentUpdNum = (int) ADDONUPDATEA;

// Install new addon
if (isset($_POST['install_addon']) && is_uploaded_file($_FILES['addon_zip_file']['tmp_name']))
{
	$file = $_FILES['addon_zip_file'];
	$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
	$done = false;

	if ($extension === 'zip')
	{
		$addonDir = 'includes/addons/';
		$zip = new ZipArchive;
		
		if ($zip->open($file['tmp_name']) === TRUE)
		{
			$zip->extractTo($addonDir);
			$zip->close();
			$done = true;

			header('Location: ?admin=addons&install=success');
		}
		else
		{
			header('Location: ?admin=addons&install=failed');
		}
	}
}


// Activate / Deactivate Addons
if (isset($_POST['change_addon_status'], $_POST['addon']))
{
	$addonDir = 'includes/addons/' . str_replace('/[^A-Za-z0-9_-]/i', '', $_POST['addon']);
	$activateFile = $addonDir . '/activated.html';

	if (file_exists($addonDir . '/init.php'))
	{
		if (isset($_POST['activate']))
		{
			file_put_contents($activateFile, true);

			if (! file_exists('includes/addons/settings')) mkdir('includes/addons/settings');

			$addonSettingsFile = str_replace('includes/addons', 'includes/addons/settings', $addonDir) . '.json';
			if (! file_exists($addonSettingsFile))
			{
				$addonConfigFile   = $addonDir   . '/config.json';
				if (file_exists($addonConfigFile))
				{
					$settings = array();
					$configs  = json_decode(file_get_contents($addonConfigFile), true);

					foreach ($configs as $config)
					{
						$settings[$config['name']] = (isset($config['default'])) ? $config['default'] : '';
					}

					if (! file_exists('includes/addons/settings')) mkdir('includes/addons/settings');
					file_put_contents($addonSettingsFile, json_encode($settings));
				}
			}
		}
		elseif (isset($_POST['deactivate']))
		{
			unlink($activateFile);
		}
	}
}


// Update Addon
if (isset($_GET['update_addon']))
{
	$addon     = $_GET['update_addon'];
	$addonDir  = 'includes/addons/' . str_replace('/[^A-Za-z0-9_-]/i', '', $addon);
	$infoFile  = $addonDir . '/info.json';
	$zipFile   = $addonDir . '/update.zip';
	$addonPath = realpath($addonDir);

	if (file_exists($infoFile))
	{
		$addonInfo       = json_decode(file_get_contents($infoFile));
		$addonUpdateUrl  = $addonInfo->update_url;
		$addonUpdateJson = json_decode(file_get_contents_ssl($addonUpdateUrl, array(
			'ip'       => get_user_ip_addr(),
	        'domain'   => preg_replace('/[^A-Za-z0-9-.]/i', '', $_SERVER['SERVER_NAME']),
	        'license'  => LICENSEKEY,
	        'version'  => $addonInfo->version,
	        'download' => true
		)));

		if (isset($addonUpdateJson->status) && $addonUpdateJson->status === "OK")
		{
			if (file_put_contents($zipFile, file_get_contents_ssl($addonUpdateJson->url)))
			{
				$zip = new ZipArchive;
    			
    			if ($zip->open($zipFile) === true)
				{
					$addonInfo->version = $addonUpdateJson->version;
					file_put_contents($infoFile, json_encode($addonInfo));

					$zip->extractTo($addonPath);
					$zip->close();

					unlink($zipFile);

					$currentUpdNum--;
					$pdo->exec("UPDATE " . DB_SETTINGS . " SET Xvalue = '{$currentUpdNum}' WHERE setting = 'addonupdatea'");
					header('Location: admin.php?admin=addons');
				}
			}
		}
	}
}

if (class_exists('ZipArchive'))
	$install = $opsTheme->viewPart('admin-addon-install');
else
	$install = '<p>You need to enable Zip extension in your server</p>';

$opsTheme->addVariable('install', $install);

$rows         = '';
$addonFolders = glob('includes/addons/*', GLOB_ONLYDIR);

foreach ($addonFolders as $addonFolder)
{
	$infos			 = (object) array();
	$addonName		 = str_replace('includes/addons/', '', $addonFolder);
	$addonInitFile	 = $addonFolder . '/init.php';
	$addonInfoFile	 = $addonFolder . '/info.json';
	$addonConfigFile = $addonFolder . '/config.json';

	if (! file_exists($addonInitFile))
		continue;

	if (file_exists($addonInfoFile))
	{
		$addonInfo = file_get_contents($addonInfoFile);
		$infos     = json_decode($addonInfo);
	}

	$empty = '<small>empty</small>';

	$infos->id          = $addonName;
	$infos->name	    = (isset($infos->name))		   ? $infos->name		 : $empty;
	$infos->description = (isset($infos->description)) ? $infos->description : $empty;
	$infos->author	    = (isset($infos->author))	   ? $infos->author	     : $empty;
	$infos->version     = (isset($infos->version))	   ? $infos->version	 : $empty;

	$opsTheme->addVariable('addon', $infos);

	$activated = false;
	$update    = false;

	if (file_exists($addonFolder . '/activated.html'))
		$activated = true;

	if ($currentUpdNum > 0 && isset($infos->update_url))
	{
		$addonUpdateUrl  = $infos->update_url;
		$addonUpdateJson = json_decode(file_get_contents_ssl($addonUpdateUrl, array(
			'ip'      => get_user_ip_addr(),
	        'domain'  => preg_replace('/[^A-Za-z0-9-.]/i', '', $_SERVER['SERVER_NAME']),
	        'license' => LICENSEKEY,
	        'version' => $infos->version,
		)));

		if (isset($addonUpdateJson->status) && $addonUpdateJson->status === "OK")
			$update = true;
	}

	if ($activated)
		$infos->activate = $opsTheme->viewPart('admin-addon-deactivate-button');
	else
		$infos->activate = $opsTheme->viewPart('admin-addon-activate-button');

	if ($activated && file_exists($addonConfigFile))
		$infos->settings = $opsTheme->viewPart('admin-addon-settings-button');

	if ($update)
		$infos->update = $opsTheme->viewPart('admin-addon-update-button');
	else
		$infos->update = '<span>Up to date</span>';

	$opsTheme->addVariable('addon', $infos);
	$rows .= $opsTheme->viewPart('admin-addon-row-each');
}

$opsTheme->addVariable('rows', $rows);
echo $opsTheme->viewPage('admin-addons');
?>