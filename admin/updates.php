<?php

if (isset($_GET['delete_backup']))
{
	$backup      = (int) $_GET['delete_backup'];
	$backup_file = "backups/backup-$backup.zip";

	if (file_exists($backup_file))
	{
		if (unlink($backup_file))
		{
			echo '<script type="text/javascript">window.location.href = "admin.php?admin=updates";</script>';
		}
	}
}

$updateAvailable = 0;
$updateJson      = json_decode(file_get_contents_su(base64_decode('aHR0cHM6Ly91cGRhdGVzLm9ubGluZXBva2Vyc2NyaXB0LmNvbS9jb3JlL3NjcmlwdA==')));

if (UPDATEALERT == '1' && isset($updateJson->status) && $updateJson->status === "OK")
	$updateAvailable = 1;

$opsTheme->addVariable('has_update', $updateAvailable);

$currentversion = SCRIPTVERSIO;
$opsTheme->addVariable('current_version', $currentversion);

$backups      = '';
$backup_files = glob('backups/backup-*.zip');

if (count($backup_files) > 0)
{
	rsort($backup_files);
	foreach ($backup_files as $backup_file)
	{
		if (!preg_match('/^backups\/backup-([0-9]{10,15})\.zip$/i', $backup_file, $backup_match)) continue;
		$backup_time = filemtime($backup_file);
		$backup_date = date('j M Y, h:i:s A', $backup_time);
		$backup_size = filesize($backup_file);
		$backup_mb   = formatSizeUnits($backup_size);

		$opsTheme->addVariable('backup', array(
			'id'   => $backup_match[1],
			'file' => $backup_file,
			'date' => $backup_date,
			'mb'   => $backup_mb,
		));

		$backups .= $opsTheme->viewPart('admin-backup-each');
	}

	$opsTheme->addVariable('backups', $backups);
}
else
{
	$backups = $opsTheme->viewPart('admin-backup-none');
}


if (isset($updateJson->changelog) && is_array($updateJson->changelog))
{
	$changelogs = '';

	foreach ($updateJson->changelog as $changelog_file)
	{
		$opsTheme->addVariable('changelog', $changelog_file);
		$changelogs .= $opsTheme->viewPart('admin-update-changelog-each');
	}

	$opsTheme->addVariable('changelogs', $changelogs);
}

function formatSizeUnits($bytes)
{
	if ($bytes >= 1073741824)
	{
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';
	}
	elseif ($bytes >= 1048576)
	{
		$bytes = number_format($bytes / 1048576, 2) . ' MB';
	}
	elseif ($bytes >= 1024)
	{
		$bytes = number_format($bytes / 1024, 2) . ' KB';
	}
	elseif ($bytes > 1)
	{
		$bytes = $bytes . ' bytes';
	}
	elseif ($bytes == 1)
	{
		$bytes = $bytes . ' byte';
	}
	else
	{
		$bytes = '0 bytes';
	}

	return $bytes;
}

echo $opsTheme->viewPage('admin-updates');
?>