<?php
require('includes/inc_rankings.php');

$addons->get_hooks(array(), array(

    'page'     => 'rankings.php',
    'location'  => 'page_start'

));

$sort     = (isset($_GET['sortby'])) ? $_GET['sortby'] : '';
$tbl_name = "stats";	//your table name

// How many adjacent pages should be shown on each side?
$adjacents = 3;

/*
First get total number of rows in data table.
If you have a WHERE clause in your query, make sure you mirror it here.
*/
$query       = "SELECT COUNT(*) as num FROM $tbl_name";

$total_query = $pdo->prepare($query);
$total_query->execute();

$total_pages = $total_query->fetch(PDO::FETCH_ASSOC);
$total_pages = $total_pages['num'];

/* Setup vars for query. */
$targetpage = 'rankings.php'; 	//your file name  (the name of this file)
$limit      = 10; 	//how many items to show per page
$page       = (isset($_GET['page'])) ? (int) $_GET['page'] : 1;

if ($page)
{
	$start = ($page - 1) * $limit; 			//first item to display on this page
}
else
{
	$start = 0;	//if no page var is given, set start to 0
}

/* Get data. */
$sql    = "SELECT * FROM $tbl_name LIMIT $start, $limit";
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
$pagination = '';
$opsTheme->addVariable('page', $page);

if ( $lastpage > 1 )
{
	$pagination .= '<nav>
	<ul class="pagination">';
	//previous button
	if ($page > 1)
	{
		$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage?page=$prev\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
	}
	else
	{
		$pagination .= "<li class=\"page-item\"><a class=\"page-link\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
	}
	
	//pages
	if ( $lastpage < 7 + ($adjacents * 2) )	//not enough pages to bother breaking it up
	{
		for ( $counter = 1; $counter <= $lastpage; $counter++ )
		{
			if ($counter == $page)
			{
				$pagination .= "<li class=\"page-item active\"><a class=\"page-link\"><span>$counter</span></a></li>";
			}
			else
			{
				$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage?page=$counter\">$counter</a></li>";
			}
		}
	}
	elseif ( $lastpage > 5 + ($adjacents * 2) )	//enough pages to hide some
	{
		//close to beginning; only hide later pages
		if ( $page < 1 + ($adjacents * 2) )
		{
			for ( $counter = 1; $counter < 4 + ($adjacents * 2); $counter++ )
			{
				if ( $counter == $page )
				{
					$pagination .= "<li class=\"page-item active\"><a class=\"page-link\"><span>$counter</span></a></li>";
				}
				else
				{
					$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage?page=$counter\">$counter</a></li>";
				}
			}

			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage?page=$lpm1\">$lpm1</a></li>";
			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage?page=$lastpage\">$lastpage</a></li>";
		}
		//in middle; hide some front and some back
		elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
		{
			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage?page=1\">1</a></li>";
			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage?page=2\">2</a></li>";

			for ($counter = ($page - $adjacents); $counter <= ($page + $adjacents); $counter++)
			{
				if ($counter == $page)
				{
					$pagination .= "<li class=\"page-item active\"><a class=\"page-link\"><span>$counter</span></a></li>";
				}
				else
				{
					$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage?page=$counter\">$counter</a></li>";
				}
			}

			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage?page=$lpm1\">$lpm1</a></li>";
			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage?page=$lastpage\">$lastpage</a></li>";
		}
		//close to end; only hide early pages
		else
		{
			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage?page=1\">1</a></li>";
			$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage?page=2\">2</a></li>";

			for ( $counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++ )
			{
				if ($counter == $page)
				{
					$pagination .= "<li class=\"page-item active\"><a class=\"page-link\"><span>$counter</span></a></li>";
				}
				else
				{
					$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage?page=$counter\">$counter</a></li>";
				}
			}
		}
	}
	
	//next button
	if ($page < $counter - 1)
	{
		$pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetpage?page=$next\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
	}
	else
	{
		$pagination .= "<li class=\"page-item\"><a class=\"page-link\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
	}

	$pagination .= "</ul>
	</nav>";
}

// Sort
if($sort == "rank")
{
	$query = "SELECT * FROM `$tbl_name` ORDER BY `$tbl_name`.`rank` ASC";
}
elseif ($sort == "gamesplayed")
{
	$query = "SELECT * FROM `$tbl_name` ORDER BY `$tbl_name`.`gamesplayed` ASC";
}
elseif ($sort == "tournamentsplayed")
{
	$query = "SELECT * FROM `$tbl_name` ORDER BY `$tbl_name`.`tournamentsplayed` ASC";
}
elseif ($sort == "tournamentswon")
{
	$query = "SELECT * FROM `$tbl_name` ORDER BY `$tbl_name`.`tournamentswon` ASC";
}
elseif ($sort == "handsplayed")
{
	$query = "SELECT * FROM `$tbl_name` ORDER BY `$tbl_name`.`handsplayed` ASC";
}
elseif ($sort == "handswon")
{
	$query = "SELECT * FROM `$tbl_name` ORDER BY `$tbl_name`.`handswon` ASC";
}
elseif ($sort == "checked")
{
	$query = "SELECT * FROM `$tbl_name` ORDER BY `$tbl_name`.`checked` ASC";
}
else
{
	$query = "SELECT * FROM `$tbl_name` ORDER BY `$tbl_name`.`rank` ASC";
} 

function stat_bind($str)
{
	return DB_STATS . '.' . $str;
}

// Getting user info from database
$statfields = array('winpot', 'rank', 'player', 'gamesplayed', 'tournamentsplayed', 'tournamentswon', 'handsplayed', 'handswon', 'checked', 'called');
$sql        = "SELECT " . implode(', ', array_map('stat_bind', $statfields)) . ", " . DB_PLAYERS . ".datecreated, " . DB_PLAYERS . ".lastlogin  from " . DB_STATS . ", " . DB_PLAYERS . " WHERE " . DB_PLAYERS . ".username = " . DB_STATS . ".player AND " . DB_PLAYERS . ".banned = 0 ORDER BY " . DB_STATS . ".rank ASC LIMIT $start, $limit";

$staq = $pdo->prepare($sql);
$staq->execute();
$rankList = '';

while ($star = $result->fetch(PDO::FETCH_ASSOC))
{
	$rank = $star['rank'];
	$name = $star['player'];
	$handsplayed = $star['handsplayed'];
	$handswon = $star['handswon'];
	$called = $star['called'];
	$checked = $star['checked'];
	$win = $star['winpot'];
	$played = $star['gamesplayed'];
	$tplayed = $star['tournamentsplayed'];
	$twon = $star['tournamentswon'];

	$opsTheme->addVariable('rank', $rank);
	$opsTheme->addVariable('name', $name);
	$opsTheme->addVariable('handsplayed', $handsplayed);
	$opsTheme->addVariable('handswon', $handswon);
	$opsTheme->addVariable('called', $called);
	$opsTheme->addVariable('checked', $checked);
	$opsTheme->addVariable('played', $played);
	$opsTheme->addVariable('tournaments_played', $tplayed);
	$opsTheme->addVariable('tournaments_won', $twon);

	$rankList .= $opsTheme->viewPart('ranking-each');
}

$opsTheme->addVariable('ranklist',   $rankList);
$opsTheme->addVariable('pagination', $pagination);

include 'templates/header.php';

echo $addons->get_hooks(array(), array(
    'page'     => 'rankings.php',
    'location'  => 'html_start'
));

echo $opsTheme->viewPage('rankings');

echo $addons->get_hooks(array(), array(

    'page'     => 'rankings.php',
    'location'  => 'html_end'

));

include 'templates/footer.php';
?>