<?php

function bots_admin_page ( $array = array() )
{
	if (! isset($array['pagetag'])) return '';

	if ($array['pagetag'] === 'bots')
	{
		global $pdo;

		$html = '<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="nav-item"><a class="nav-link active" href="#bots" aria-controls="home" role="tab" data-toggle="tab">Bots</a></li>
			<li role="presentation" class="nav-item"><a class="nav-link" href="#createbot" aria-controls="home" role="tab" data-toggle="tab">Create Bot</a></li>
		</ul>

		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="bots">
				<table class="table table-hover" border="0" cellspacing="0" cellpadding="0">
					<thead> 
						<th>Name</th>
						<th>' . ADMIN_MEMBERS_RANK . '</th>
						<th>Table / Seat</th>
						<th>Edit</th>
						<th>' . ADMIN_MEMBERS_DELETE . '</th>
						<th>' . ADMIN_MEMBERS_RESET_STATS . '</th>
					</thead>

					<tbody>';

					$btq = $pdo->prepare("SELECT username, datecreated, banned, ipaddress, approve FROM " . DB_PLAYERS . " WHERE isbot = 1 ORDER BY ID ASC");
					$btq->execute();

					while ($btr = $btq->fetch(PDO::FETCH_ASSOC))
					{
						$botname = $btr['username'];
						$stq = $pdo->prepare("SELECT rank FROM " . DB_STATS . " WHERE player = '$botname'");
						$stq->execute();
						$str = $stq->fetch(PDO::FETCH_ASSOC);

						$tableNseat = 'None';

						$aq = $pdo->prepare("SELECT gameID, tablename, p1name, p2name, p3name, p4name, p5name, p6name, p7name, p8name, p9name, p10name FROM " . DB_POKER . " WHERE (p1name = '$botname' OR p2name = '$botname' OR p3name = '$botname' OR p4name = '$botname' OR p5name = '$botname' OR p6name = '$botname' OR p7name = '$botname' OR p8name = '$botname' OR p9name = '$botname' OR p10name = '$botname')");
						$aq->execute();

						if ($aq->rowCount() == 1)
						{
							$ar = $aq->fetch(PDO::FETCH_ASSOC);
							$tableNseat = $ar['tablename'];

							for ($i = 1; $i < 11; $i++)
							{
								if ($ar['p' . $i . 'name'] == $botname)
								{
									$tableNseat .= ' / Seat ' . $i;
									break;
								}
							}
						}

						$html .= '<tr>
							<td>' . $btr['username'] . '</td>
							<td>' . $str['rank'] . '</td>
							<td>' . $tableNseat . '</td>
							<td>
								<a onclick="changeview(\'admin.php?admin=edit-bot&botname=' . $botname . '\')" class="btn btn-warning btn-sm">Edit</a>
							</td>
							<td>
								<form name="form1" method="post" action="admin.php?admin=bots">
									<input type="hidden" name="action" value="delete">
									<input type="hidden" name="player" value="' . $botname . '">
									<input type="submit" name="Button" value="' . BUTTON_DELETE . '" class="btn btn-danger btn-sm">
								</form>
							</td>
							<td>
								<form name="form1" method="post" action="admin.php?admin=bots">
									<input type="hidden" name="action" value="reset">
									<input type="hidden" name="player" value="reset">
									<input type="submit" name="Button" value="' . BUTTON_RESET . '" class="btn btn-primary btn-sm">
								</form>
							</td>
						</tr>';
					}

					$html .= '</tbody>
				</table>
			</div>

			<div role="tabpanel" class="tab-pane" id="createbot">
				<br>
				<form name="edittable" method="post" action="admin.php?admin=bots" enctype="multipart/form-data">
					
					<input type="hidden" name="action" value="createbot">

					Name:
					<input type="text" name="cbotname" class="form-control" size="60" maxlength="60" value="">
					<span class="help-block"></span>

					Table:
					<select name="ctable" class="form-control">';

					$tableq = $pdo->prepare("SELECT gameID, tablename FROM " . DB_POKER . " ORDER BY tablename ASC");
					$tableq->execute();

					while ($tabler = $tableq->fetch(PDO::FETCH_ASSOC))
					{
						$gameID = $tabler['gameID'];
						$tablename =  stripslashes($tabler['tablename']);

						$html .= '<option value="' . $gameID . '">' . $tablename . '</option>';
					}

					$html .= '</select>
					<span class="help-block"></span>

					Seat Number:
					<select name="cseatnumber" class="form-control">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
					</select>
					<span class="help-block"></span>

					Pot Balance:
					<input type="number" name="cbotpot" class="form-control" value="' . transfer_from(100000) . '" min="0">
					<span class="help-block"></span>

					Profile Picture:
					<input name="uploadedfile" type="file">
					<span class="help-block"></span>
					<br><br>
					<button type="submit" class="btn btn-success">Save</button>

				</form>
			</div>
		</div>';

		return $html;
	}
	elseif ($array['pagetag'] === 'edit-bot' && isset($_GET['botname']))
	{
		global $pdo, $bad_msg, $bad_msgs;
		$html = '';

		$botname = addslashes($_GET['botname']);
		$q = $pdo->prepare("SELECT * FROM " . DB_PLAYERS . " WHERE username = '$botname'");
		$q->execute();
		$r = $q->fetch(PDO::FETCH_ASSOC);

		$getstats = $pdo->prepare("SELECT * FROM " . DB_STATS . " WHERE player = '$botname'");
		$getstats->execute();
		$usestats = $getstats->fetch(PDO::FETCH_ASSOC);
		$botpot   = ($usestats['winpot'] > 0) ? transfer_from($usestats['winpot']) : 0;

		if ($bad_msg != false)
		{
			$html .= '<div class="alert alert-warning">' . $bad_msgs . '</div>';
		}

		$html .= '<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="nav-item"><a class="nav-link" href="admin.php?admin=bots">Bots</a></li>
			<li role="presentation" class="nav-item"><a class="nav-link active" href="#" aria-controls="home" role="tab" data-toggle="tab">Create Bot</a></li>
		</ul>
		
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="editbot">
				<br>
				<form name="edittable" method="post" action="admin.php?admin=edit-bot&botname=' . $botname . '" enctype="multipart/form-data">
					
					<input type="hidden" name="action" value="editbot">

					Name:
					<input type="text" name="ebotname" class="form-control" size="60" maxlength="60" value="' . $r['username'] . '" readonly>
					<span class="help-block"></span>
					<br>
					Table:
					<select name="etable" class="form-control">';

					$tableq = $pdo->prepare("SELECT gameID, tablename FROM " . DB_POKER . " ORDER BY tablename ASC");
					$tableq->execute();
					while ($tabler = $tableq->fetch(PDO::FETCH_ASSOC))
					{
						$gameID = $tabler['gameID'];
						$tablename =  stripslashes($tabler['tablename']);

						$yq = $pdo->prepare("SELECT gameID, p1name, p2name, p3name, p4name, p5name, p6name, p7name, p8name, p9name, p10name FROM " . DB_POKER . " WHERE gameID = $gameID AND (p1name = '$botname' OR p2name = '$botname' OR p3name = '$botname' OR p4name = '$botname' OR p5name = '$botname' OR p6name = '$botname' OR p7name = '$botname' OR p8name = '$botname' OR p9name = '$botname' OR p10name = '$botname')");
						$yq->execute();

						if ($yq->rowCount() == 1)
						{
							$yr = $yq->fetch(PDO::FETCH_ASSOC);
							$html .= '<option value="' . $gameID . '" selected>' . $tablename . '</option>';
						}
						else
						{
							$html .= '<option value="' . $gameID . '">' . $tablename . '</option>';
						}
					}

					$html .= '</select>
					<span class="help-block"></span>
					<br>
					Seat Number:
					<select name="eseatnumber" class="form-control">';

					for ($i = 1; $i < 11; $i++)
					{
						if (isset($yr) && $yr['p' . $i . 'name'] == $botname)
						{
							$html .= '<option value="' . $i . '" selected>' . $i . '</option>';
						}
						else
						{
							$html .= '<option value="' . $i . '">' . $i . '</option>';
						}
					}

					$html .= '</select>
					<span class="help-block"></span>
					<br>
					Pot Balance:
					<input type="number" name="ebotpot" class="form-control" value="' . $botpot . '" min="0">
					<span class="help-block"></span>
					<br>
					Profile Picture:
					<input name="uploadedfile" type="file">
					<span class="help-block"></span>
					<br><br>
					<button type="submit" class="btn btn-success">Save</button>

				</form>
			</div>
		</div>';

		return $html;
	}

	return '';
}


// Adding the hook to the sidebar
$addons->add_hook(array(

	'page'     => 'admin.php',
	'location' => 'admin_page',
	'function' => 'bots_admin_page',

));
