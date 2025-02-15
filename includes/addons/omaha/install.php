<?php
$p1c3Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p1card3'");
if ($p1c3Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p1card3 VARCHAR(40) DEFAULT '' AFTER p1card2 ");

$p1c4Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p1card4'");
if ($p1c4Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p1card4 VARCHAR(40) DEFAULT '' AFTER p1card3 ");

$p2c3Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p2card3'");
if ($p2c3Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p2card3 VARCHAR(40) DEFAULT '' AFTER p2card2 ");

$p2c4Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p2card4'");
if ($p2c4Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p2card4 VARCHAR(40) DEFAULT '' AFTER p2card3 ");

$p3c3Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p3card3'");
if ($p3c3Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p3card3 VARCHAR(40) DEFAULT '' AFTER p3card2 ");

$p3c4Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p3card4'");
if ($p3c4Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p3card4 VARCHAR(40) DEFAULT '' AFTER p3card3 ");

$p4c3Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p4card3'");
if ($p4c3Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p4card3 VARCHAR(40) DEFAULT '' AFTER p4card2 ");

$p4c4Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p4card4'");
if ($p4c4Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p4card4 VARCHAR(40) DEFAULT '' AFTER p4card3 ");

$p5c3Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p5card3'");
if ($p5c3Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p5card3 VARCHAR(40) DEFAULT '' AFTER p5card2 ");

$p5c4Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p5card4'");
if ($p5c4Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p5card4 VARCHAR(40) DEFAULT '' AFTER p5card3 ");

$p6c3Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p6card3'");
if ($p6c3Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p6card3 VARCHAR(40) DEFAULT '' AFTER p6card2 ");

$p6c4Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p6card4'");
if ($p6c4Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p6card4 VARCHAR(40) DEFAULT '' AFTER p6card3 ");

$p7c3Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p7card3'");
if ($p7c3Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p7card3 VARCHAR(40) DEFAULT '' AFTER p7card2 ");

$p7c4Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p7card4'");
if ($p7c4Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p7card4 VARCHAR(40) DEFAULT '' AFTER p7card3 ");

$p8c3Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p8card3'");
if ($p8c3Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p8card3 VARCHAR(40) DEFAULT '' AFTER p8card2 ");

$p8c4Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p8card4'");
if ($p8c4Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p8card4 VARCHAR(40) DEFAULT '' AFTER p8card3 ");

$p9c3Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p9card3'");
if ($p9c3Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p9card3 VARCHAR(40) DEFAULT '' AFTER p9card2 ");

$p9c4Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p9card4'");
if ($p9c4Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p9card4 VARCHAR(40) DEFAULT '' AFTER p9card3 ");

$p10c3Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p10card3'");
if ($p10c3Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p10card3 VARCHAR(40) DEFAULT '' AFTER p10card2 ");

$p10c4Q = $pdo->query("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' AND TABLE_NAME = '" . DB_POKER . "' AND COLUMN_NAME = 'p10card4'");
if ($p10c4Q->rowCount() === 0)
	$pdo->exec("ALTER TABLE " . DB_POKER . " ADD p10card4 VARCHAR(40) DEFAULT '' AFTER p10card3 ");
