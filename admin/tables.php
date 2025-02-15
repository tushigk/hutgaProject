<?php

$tbl_name  = "poker";	//your table name
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
$limit = 10; 	//how many items to show per page

$page  = (isset($_GET['page'])) ? $_GET['page'] : 1;
if ($page)
{
	$start = ($page - 1) * $limit; 			//first item to display on this page
}
else
{
	$start = 0;	//if no page var is given, set start to 0
}

$targetpage = "admin.php?admin=tables"; 	//your file name  (the name of this file)

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
$lastpage = ceil ($total_pages / $limit );		//lastpage is = total pages / items per page, rounded up.
$lpm1     = $lastpage - 1;						//last page minus 1

/*
Now we apply our rules and draw the pagination object.
We're actually saving the code to a variable in case we want to draw it more than once.
*/
$pagination = "";

if($lastpage > 1)
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


$opsTheme->addVariable('table_name_label', ADMIN_TABLES_NAME);
$opsTheme->addVariable('table_type_label', ADMIN_TABLES_TYPE);
$opsTheme->addVariable('table_game_label', ADMIN_TABLES_GAME);
$opsTheme->addVariable('table_min_label', ADMIN_TABLES_MIN);
$opsTheme->addVariable('table_max_label', ADMIN_TABLES_MAX);
$opsTheme->addVariable('table_sb_label', ADMIN_TABLES_SB);
$opsTheme->addVariable('table_bb_label', ADMIN_TABLES_BB);
$opsTheme->addVariable('table_style_label', ADMIN_TABLES_STYLE);
$opsTheme->addVariable('table_delete_label', ADMIN_TABLES_DELETE);

$games = array();
if (! function_exists('add_game_table_column'))
{
	function add_game_table_column($label, $value)
	{
		global $games, $gameID;
		$games[$gameID][$label] = $value;
	}
}

$rows = '';
$tableq = $pdo->prepare("select * from ".DB_POKER." order by tablename asc LIMIT $start, $limit");
$tableq->execute();

while ($tabler = $tableq->fetch(PDO::FETCH_ASSOC))
{
	$tablename =  stripslashes($tabler['tablename']);

    if ($tabler['gamestyle'] == 'o') {
        $gamestyle = "Omaha Hold 'em";
    } else {
        $gamestyle = "Texas Hold 'em";
    }

	$min 				= money_small($tabler['tablelow']);
	$tablelimit			= $tabler['tablelimit'];
	$max 				= money_small($tablelimit);
	$gameID 			= $tabler['gameID'];
	$tabletype 			= (($tabler['tabletype'] == 't')? 'Tournament' : 'Sit \'n Go');
	$tablestyle 		= (($tabler['tablestyle'] == '')? 'normal' : $tabler['tablestyle']);
	$tablemultiplier 	= 1;
	$sbamount 			= $tabler['sbamount'];
	$bbamount 			= $tabler['bbamount'];

	if ($tablelimit == 25000) $tablemultiplier = 2;
	if ($tablelimit == 50000) $tablemultiplier = 4;
	if ($tablelimit == 100000) $tablemultiplier = 8;
	if ($tablelimit == 250000) $tablemultiplier = 20;
	if ($tablelimit == 500000) $tablemultiplier = 40;
	if ($tablelimit == 1000000) $tablemultiplier = 80;	
	
	if($sbamount != 0) {
	$sb = money_small($sbamount);
	$bb = money_small($bbamount);
	} else {
	if ($tabletype == 't')
		{
		$sb = money_small(25 * $tablemultiplier) . '-' . money_small(25 * $tablemultiplier * 9);
		$bb = money_small(50 * $tablemultiplier) . '-' . money_small(50 * $tablemultiplier * 9);
		}
	  else
		{
		$sb = money_small(100 * $tablemultiplier);
		$bb = money_small(200 * $tablemultiplier);
		}
	}	
	
	$opsTheme->addVariable('gametable_id', $gameID);
	$opsTheme->addVariable('gametable_name', $tablename);
	$opsTheme->addVariable('gametable_type', $tabletype);
	$opsTheme->addVariable('gametable_gamestyle', $gamestyle);
	$opsTheme->addVariable('gametable_min', $min);
	$opsTheme->addVariable('gametable_max', $max);
	$opsTheme->addVariable('gametable_sb', $sb);
	$opsTheme->addVariable('gametable_bb', $bb);	
	$opsTheme->addVariable('gametable_style', $tablestyle);

	add_game_table_column(ADMIN_TABLES_NAME,  $tablename);
	add_game_table_column(ADMIN_TABLES_TYPE,  $tabletype);
	add_game_table_column(ADMIN_TABLES_GAME,  $gamestyle);
	add_game_table_column(ADMIN_TABLES_MIN,   $min);
	add_game_table_column(ADMIN_TABLES_MAX,   $max);
	add_game_table_column(ADMIN_TABLES_SB,    $sb);
	add_game_table_column(ADMIN_TABLES_BB,    $bb);
	add_game_table_column(ADMIN_TABLES_STYLE, $tablestyle);

	$addons->get_hooks(array(),
		array(
	    	'page'     => 'admin/tables.php',
	    	'location'  => 'admin_table_column'
		)
	);

	add_game_table_column(ADMIN_TABLES_DELETE, $opsTheme->viewPart('admin-tables-row-options'));
	
	$rows .= $opsTheme->viewPart('admin-tables-row-each');
}

$opsTheme->addVariable('rows', $rows);

/* --- Table Header */
$gameHead  = '';
foreach (array_keys($games[$gameID]) as $label)
{
	$opsTheme->addVariable('text', $label);
	$gameHead .= $opsTheme->viewPart('admin-table-head-col');
}
/* Table Header --- */

/* --- Table Rows */
$gameRows = '';
foreach ($games as $rowId => $row)
{
	$columns = '';
	foreach ($row as $col)
	{
		$opsTheme->addVariable('text', $col);
		$columns .= $opsTheme->viewPart('admin-table-each-col');
	}

	$rowArray = $addons->get_hooks(
		array(
			'content' => array(
				'id'      => $rowId,
				'onclick' => "open_game({$rowId});",
				'columns' => $columns
			)
		),
		array(
			'page'     => 'admin/tables.php',
			'location'  => 'each_game_row_info'
		)
	);
	$opsTheme->addVariable('row', $rowArray);
	$gameRows .= $opsTheme->viewPart('admin-table-each-row');
}
/* Table Rows --- */

$opsTheme->addVariable('game', array(
	'head' => $gameHead,
	'rows' => $gameRows
));



/* Create Table Section */
$tableTypes = $addons->get_hooks(
	array(
		'content' => array(
			array('value' => 's', 'label' => SITNGO),
			array('value' => 't', 'label' => TOURNAMENT)
		)
	),
	array(
    	'page'     => 'admin/tables.php',
    	'location'  => 'admin_table_tabletype_logic'
	)
);
$opsTheme->addVariable('table_types', $tableTypes);


$gameStyles = $addons->get_hooks(
	array(
		'content' => array(
			array('value' => 't', 'label' => GAME_TEXAS),
		)
	),
	array(
    	'page'     => 'admin/tables.php',
    	'location'  => 'admin_table_gamestyle_logic'
	)
);
$opsTheme->addVariable('game_styles', $gameStyles);


$minBuyins = $addons->get_hooks(
	array(
		'content' => array(
			array('value' => '0',      'label' => money(0)),
			array('value' => '1000',   'label' => money(1000)),
			array('value' => '2500',   'label' => money(2500)),
			array('value' => '5000',   'label' => money(5000)),
			array('value' => '10000',  'label' => money(10000)),
			array('value' => '25000',  'label' => money(25000)),
			array('value' => '50000',  'label' => money(50000)),
			array('value' => '100000', 'label' => money(100000)),
			array('value' => '250000', 'label' => money(250000)),
			array('value' => '500000', 'label' => money(500000)),
		)
	),
	array(
    	'page'     => 'admin/tables.php',
    	'location'  => 'min_buyins'
	)
);
$opsTheme->addVariable('min_buyins', $minBuyins);


$maxBuyins = $addons->get_hooks(
	array(
		'content' => array(
			array('value' => '10000',   'label' => money(10000)),
			array('value' => '25000',   'label' => money(25000)),
			array('value' => '50000',   'label' => money(50000)),
			array('value' => '100000',  'label' => money(100000)),
			array('value' => '250000',  'label' => money(250000)),
			array('value' => '500000',  'label' => money(500000)),
			array('value' => '1000000', 'label' => money(1000000)),
		)
	),
	array(
    	'page'     => 'admin/tables.php',
    	'location'  => 'max_buyins'
	)
);
$opsTheme->addVariable('max_buyins', $maxBuyins);


$tableStyles = array();
$stq         = $pdo->prepare("select style_name from styles"); $stq->execute();
while ($str = $stq->fetch(PDO::FETCH_ASSOC))
{
	$tableStyles[] = array('value' => $str['style_name'], 'label' => $str['style_name']);
}
$tableStyles = $addons->get_hooks(
	array(
		'content' => $tableStyles,
	),
	array(
    	'page'     => 'admin/tables.php',
    	'location'  => 'table_styles'
	)
);
$opsTheme->addVariable('table_styles', $tableStyles);

$inputs = $opsTheme->viewPart('admin-tables-inputs');
$inputs .= $addons->get_hooks(array(), array(
    'page'     => 'admin/tables.php',
    'location'  => 'input_block'
));
$opsTheme->addVariable('inputs', $inputs);

echo $opsTheme->viewPage('admin-tables');
?>