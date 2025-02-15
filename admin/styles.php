<?php
function generateRandomString ($length = 10)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';

	for ($i = 0; $i < $length; $i++)
	{
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}

	return $randomString;
}
$opsTheme->addVariable('lic', generateRandomString());

$styq = $pdo->prepare("select style_name from styles"); $styq->execute();
$styles = '';
while($styr = $styq->fetch(PDO::FETCH_ASSOC))
{
	$name = $styr['style_name'];
	$sname = $styr['style_name'];
	$spreview = '<img src="images/tablelayout/'.$name.'.png" border="0" width="250" class="img-fluid">';

	$opsTheme->addVariable('style_name',    $sname);
	$opsTheme->addVariable('style_preview', $spreview);

	$styles .= $opsTheme->viewPart('admin-style-each');
}
$opsTheme->addVariable('styles',  $styles);

if (isset($msg))
	$opsTheme->addVariable('message', $msg);

echo $opsTheme->viewPage('admin-styles');
?>
