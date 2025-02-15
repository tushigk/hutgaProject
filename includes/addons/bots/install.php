<?php
$prbQ = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_PLAYERS . "' AND COLUMN_NAME = 'isbot'");
if ($prbQ->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_PLAYERS . " ADD isbot TINYINT(1) NOT NULL DEFAULT '0' AFTER timetag;");

$prbQ = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'botgod'");
if ($prbQ->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD botgod VARCHAR(2) NULL AFTER dealer;");
