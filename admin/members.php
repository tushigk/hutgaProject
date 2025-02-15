<?php
$dir = (isset($_GET['dir'])) ? addslashes($_GET['dir']) : 'asc';
$col = (isset($_GET['col'])) ? addslashes($_GET['col']) : DB_PLAYERS.'.username'; 

$addons->get_hooks(array(), array(
    'page'     => 'admin/members.php',
    'location'  => 'tabs_left'
));

// How many adjacent pages should be shown on each side?
$adjacents = 3;
/*
First get total number of rows in data table.
If you have a WHERE clause in your query, make sure you mirror it here.
*/
$query       = "SELECT COUNT(*) as num FROM " . DB_PLAYERS;
$total_query = $pdo->prepare($query);
$total_query->execute();
$total_pages = $total_query->fetch(PDO::FETCH_ASSOC);
$total_pages = $total_pages['num'];

/* Setup vars for query. */
$page       = (isset($_GET['page'])) ? $_GET['page'] : 1;
$targetpage = "admin.php?admin=members"; 	//your file name  (the name of this file)
$limit      = 10; 	//how many items to show per page

if ($page)
{
	$start = ($page - 1) * $limit;      //first item to display on this page
}
else
{
	$start = 0; //if no page var is given, set start to 0
}

/* Get data. */
$sql    = "SELECT * FROM " . DB_PLAYERS . " LIMIT $start, $limit";
$result = $pdo->prepare($sql);
$result->execute();


/* Setup page vars for display. */
if ($page == 0)
{
	$page = 1;					//if no page var is given, default to 1.
}

$prev     = $page - 1;//previous page is page - 1
$next     = $page + 1;//next page is page + 1
$lastpage = ceil ( $total_pages / $limit );		//lastpage is = total pages / items per page, rounded up.
$lpm1     = $lastpage - 1;						//last page minus 1

/*
Now we apply our rules and draw the pagination object.
We're actually saving the code to a variable in case we want to draw it more than once.
*/
$pagination = "";

if ( $lastpage > 1 )
{
	$pagination .= "
	
<nav>
  <ul class=\"pagination\">	
	
	";
	//previous button
	if ($page > 1)
	{
		$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage&page=$prev\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
	}
	else
	{
		$pagination .= "<li class=\"page-item\"><a class=\"page-link\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
	}
	
	//pages
	if ( $lastpage < 7 + ($adjacents * 2) )	//not enough pages to bother breaking it up
	{
		for ($counter = 1; $counter <= $lastpage; $counter++)
		{
			if ($counter == $page)
			{
				$pagination .= "<li class=\"page-item active\"><a class=\"page-link\"><span>$counter</span></a></li>";
			}
			else
			{
				$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage&page=$counter\">$counter</a></li>";
			}
		}
	}
	elseif ( $lastpage > 5 + ($adjacents * 2) )	//enough pages to hide some
	{
		//close to beginning; only hide later pages
		if ( $page < 1 + ($adjacents * 2) )
		{
			for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
			{
				if ($counter == $page)
				{
					$pagination .= "<li class=\"page-item active\"><a class=\"page-link\"><span>$counter</span></a></li>";
				}
				else
				{
					$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage&page=$counter\">$counter</a></li>";
				}
			}

			$pagination .= "";
			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage&page=$lpm1\">$lpm1</a></li>";
			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage&page=$lastpage\">$lastpage</a></li>";
		}
		//in middle; hide some front and some back
		elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
		{
			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage&page=1\">1</a></li>";
			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage&page=2\">2</a></li>";
			$pagination .= "";

			for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
			{
				if ($counter == $page)
				{
					$pagination .= "<li class=\"page-item active\"><a class=\"page-link\"><span>$counter</span></a></li>";
				}
				else
				{
					$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage&page=$counter\">$counter</a></li>";
				}
			}

			$pagination .= "";
			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage&page=$lpm1\">$lpm1</a></li>";
			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage&page=$lastpage\">$lastpage</a></li>";
		}
		//close to end; only hide early pages
		else
		{
			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage&page=1\">1</a></li>";
			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage&page=2\">2</a></li>";
			$pagination .= "";

			for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
				{
					$pagination .= "<li class=\"page-item active\"><a class=\"page-link\"><span>$counter</span></a></li>";
				}
				else
				{
					$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage&page=$counter\">$counter</a></li>";
				}
			}
		}
	}
	
	//next button
	if ($page < $counter - 1)
	{
		$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage&page=$next\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
	}
	else
	{
		$pagination .= "<li class=\"page-item\"><a class=\"page-link\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
	}

	$pagination .= "
	
  </ul>
</nav>	
	
	";
}
$opsTheme->addVariable('pagination',  $pagination);
$opsTheme->addVariable('page',        $page);
$opsTheme->addVariable('total_pages', $total_pages);

$rows = '';
$plq = $pdo->prepare("select ".DB_PLAYERS.".*, ".DB_STATS.".rank from ".DB_PLAYERS.", ".DB_STATS." where ".DB_PLAYERS.".username = ".DB_STATS.".player order by ".$col." ".$dir. " LIMIT $start, $limit");
$plq->execute();

while ( $plr = $plq->fetch(PDO::FETCH_ASSOC) )
{
	$plr['datecreated'] = date("m-d-Y", $plr['datecreated']);
	if (strlen($plr['email']) < 1)
		$plr['email'] = '-';

	$pname = $plr['username'];
	$pban = $plr['banned'];
	$pdate = $plr['datecreated'];
	$pip = $plr['ipaddress'];
	$prank = $plr['rank'];
	$papprove = $plr['approve'];
	$pemail = $plr['email'];

	$opsTheme->addVariable('player', $plr);

	if ($papprove == 1)
		$plr['approval'] = $opsTheme->viewPart('admin-members-row-approve');
	else
		$plr['approval'] = $opsTheme->viewPart('admin-members-row-active');

	if ($plyrname != $pname)
	{
		if ($pban == 0)
			$plr['ban'] = $opsTheme->viewPart('admin-members-row-ban');
		else
			$plr['ban'] = $opsTheme->viewPart('admin-members-row-unban');

		$plr['delete'] = $opsTheme->viewPart('admin-members-row-delete');
	}

	$plr['reset'] = $opsTheme->viewPart('admin-members-row-reset');
	$opsTheme->addVariable('player', $plr);

	$rows .= $opsTheme->viewPart('admin-members-row-each');
}
$opsTheme->addVariable('rows', $rows);
add_members_tab('members', 'All Members', $opsTheme->viewPart('admin-members-tabpanel-default'));

$addons->get_hooks(array(), array(
    'page'     => 'admin/members.php',
    'location'  => 'tabs_right'
));

/**/
$navHtml      = '';
$tabpanelHtml = '';

foreach ($membersNavs as $mNavId => $mNav)
{
	$opsTheme->addVariable('tab', array(
		'id'   => $mNavId,
		'name' => $mNav['name'],
		'html' => $mNav['html']
	));

	$navHtml .= $opsTheme->viewPart('admin-members-tab');
	$tabpanelHtml .= $opsTheme->viewPart('admin-members-tabpanel');
}

$opsTheme->addVariable('members', array(
	'tabs'      => $navHtml,
	'tabpanels' => $tabpanelHtml
));
/**/

echo $opsTheme->viewPage('admin-members');

/* Functions */
function add_members_tab($id, $name, $html)
{
	if (! isset($GLOBALS['membersNavs']))
		$GLOBALS['membersNavs'] = array();

	global $membersNavs;
	$membersNavs[$id] = array(
		'name' => $name,
		'html' => $html
	);

	return true;
}
?>