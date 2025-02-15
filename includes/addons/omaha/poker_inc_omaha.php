<?php

function evaluate_omaha_points ( $array = array() )
{
    if (! isset($array['player'])) return '';

    $player = $array['player'];
    $omaha_points = evaluate_omaha_hand($player);

    return $omaha_points;
}

function evaluate_omaha_hand($player)
{
    global $cardr;
    global $tablecards;
    global $best_cards;
    $points     = 0;

    $boards       = array(
        $tablecards[0],
        $tablecards[1],
        $tablecards[2],
        $tablecards[3],
        $tablecards[4]
    );

    $hands       = array(
        decrypt_card($cardr['p' . $player . 'card1']),
        decrypt_card($cardr['p' . $player . 'card2']),
        decrypt_card($cardr['p' . $player . 'card3']),
        decrypt_card($cardr['p' . $player . 'card4'])
    );

    $possible_boards = fill_boards($boards);
    $possible_hands = fill_hands($hands);

    for ($i = 0; $i < sizeof($possible_hands); $i++) {
        for ($j = 0; $j < sizeof($possible_boards); $j++)
        {
            $tmp_cards = array(
                $possible_boards[$j][0],
                $possible_boards[$j][1],
                $possible_boards[$j][2],
                $possible_hands[$i][0],
                $possible_hands[$i][1],
            );

            $tmp_points = get_omaha_points($tmp_cards);
            if ($tmp_points > $points) {
                $points = $tmp_points;
                $best_cards = $tmp_cards;
            }
        }
    }

    return $points;
}

function fill_hands($cards) {
    $possible_all_hands = array();

    $possible_all_hands[0] = array($cards[0], $cards[1]);
    $possible_all_hands[1] = array($cards[0], $cards[2]);
    $possible_all_hands[2] = array($cards[0], $cards[3]);
    $possible_all_hands[3] = array($cards[1], $cards[2]);
    $possible_all_hands[4] = array($cards[1], $cards[3]);
    $possible_all_hands[5] = array($cards[2], $cards[3]);

    return $possible_all_hands;
}

function fill_boards($cards)
{
    $possible_all_boards = array();

    $possible_all_boards[0] = array($cards[0], $cards[1], $cards[2]);
    $possible_all_boards[1] = array($cards[0], $cards[1], $cards[3]);
    $possible_all_boards[2] = array($cards[0], $cards[2], $cards[3]);
    $possible_all_boards[3] = array($cards[1], $cards[2], $cards[3]);
    $possible_all_boards[4] = array($cards[0], $cards[1], $cards[4]);
    $possible_all_boards[5] = array($cards[0], $cards[2], $cards[4]);
    $possible_all_boards[6] = array($cards[0], $cards[3], $cards[4]);
    $possible_all_boards[7] = array($cards[1], $cards[2], $cards[4]);
    $possible_all_boards[8] = array($cards[1], $cards[3], $cards[4]);
    $possible_all_boards[9] = array($cards[2], $cards[3], $cards[4]);

    return $possible_all_boards;
}

function get_omaha_points($hand)
{
    $points     = 0;

    $flush      = array();
    $values     = array();
    $sortvalues = array();
    $hcs        = array();
    $orig       = array(
        'J',
        'Q',
        'K',
        'A'
    );
    $change     = array(
        11,
        12,
        13,
        14
    );
    $i          = 0;
    while ($hand[$i] != '') {
        if (strlen($hand[$i]) == 2) {
            $flush[$i]      = substr($hand[$i], 1, 1);
            $values[$i]     = str_replace($orig, $change, substr($hand[$i], 0, 1));
            $sortvalues[$i] = $values[$i];
        } else {
            $flush[$i]      = substr($hand[$i], 2, 1);
            $values[$i]     = str_replace($orig, $change, substr($hand[$i], 0, 2));
            $sortvalues[$i] = $values[$i];
        }
        $i++;
    }
    sort($sortvalues);
    $pairmatch = '';
    $ispair    = array_count_values($values);
    $results   = array_count_values($ispair);
    $i         = 0;
    if ($results['2'] == 1)
        $res = '1pair';
    if ($results['2'] > 1)
        $res = '2pair';
    if ($results['3'] > 0)
        $res = '3s';
    if ($results['4'] > 0)
        $res = '4s';
    if ((($results['3'] > 0) && ($results['2'] > 0)) || ($results['3'] > 1))
        $res = 'FH';
    $i         = 2;
    $z         = 0;
    $y         = 0;
    $multipair = array();
    while ($i < 15) {
        if ($ispair[$i] == 2) {
            $multipair[$z] = $i;
            $highpair      = $i;
            $z++;
        }
        if ($ispair[$i] == 3) {
            $threepair[$y] = $i;
            $high3pair     = $i;
            $y++;
        }
        $i++;
    }
    $bw = 6;
    $n  = 0;
    while (($sortvalues[$bw] != '') && ($n < 5)) {
        if (!in_array($sortvalues[$bw], $multipair)) {
            $hcs[$n] = $sortvalues[$bw];
            $n++;
        }
        $bw--;
    }
    $h1    = $hcs[0];
    $h2    = $hcs[1] / 10;
    $h3    = $hcs[2] / 100;
    $h4    = $hcs[3] / 1000;
    $h5    = $hcs[4] / 10000;
    $high1 = $h1;
    $high2 = $h1 + $h2;
    $high3 = $h1 + $h2 + $h3;
    $high5 = $h1 + $h2 + $h3 + $h4 + $h5;
    if (($res == '1pair') || ($res == '2pair') || ($res == 'FH')) {
        if ($res == '1pair') {
            $points = (($highpair * 10) + ($high3));
        }
        if ($res == '2pair') {
            sort($multipair);
            $pairs = count($multipair);
            if ($pairs == 3) {
                $pr1 = $multipair[2];
                $pr2 = $multipair[1];
            } else {
                $pr1 = $multipair[1];
                $pr2 = $multipair[0];
            }
            $points = ((($pr1 * 100) + ($pr2 * 10)) + $high1);
        }
        if ($res == 'FH') {
            sort($multipair);
            sort($threepair);
            $pairs  = count($multipair);
            $threes = count($threepair);
            if ($pairs == 1) {
                $pr1 = $multipair[0];
            } else {
                $pr1 = $multipair[1];
            }
            if ($threes == 1) {
                $kry1 = $threepair[0];
            } else {
                $kry1 = $threepair[1];
                $kry2 = $threepair[0];
            }
            if ($kry2 > $pr1)
                $pr1 = $kry2;
            $points = (($kry1 * 1000000) + ($pr1 * 100000));
        }
    }
    if ($res == '3s') {
        $i = 2;
        while ($i < 15) {
            if ($ispair[$i] == 3) {
                $points = ($i * 1000) + $high2;
            }
            $i++;
        }
    }
    if ($res == '4s') {
        $i = 2;
        while ($i < 15) {
            if ($ispair[$i] == 4) {
                $points = $i * 10000000 + $high1;
            }
            $i++;
        }
    }
    $flushsuit = '';
    $isflush   = array_count_values($flush);
    if ($isflush['D'] > 4)
        $flushsuit = 'D';
    if ($isflush['C'] > 4)
        $flushsuit = 'C';
    if ($isflush['H'] > 4)
        $flushsuit = 'H';
    if ($isflush['S'] > 4)
        $flushsuit = 'S';
    if ($flushsuit != '') {
        $res        = $flushsuit . ' FLUSH DETECTED';
        $i          = 0;
        $x          = 0;
        $flusharray = array();
        while ($i < 7) {
            if ($flush[$i] == $flushsuit) {
                $flusharray[$x] = $values[$i];
                $x++;
            }
            $i++;
        }
        sort($flusharray);
        $basic    = 250000;
        $z        = count($flusharray) - 1;
        $c1       = $flusharray[$z] * 1000;
        $s1       = $flusharray[$z];
        $c2       = $flusharray[$z - 1] * 100;
        $s2       = $flusharray[$z - 1];
        $c3       = $flusharray[$z - 2] * 10;
        $s3       = $flusharray[$z - 2];
        $c4       = $flusharray[$z - 3];
        $s4       = $flusharray[$z - 3];
        $c5       = $flusharray[$z - 4] / 10;
        $s5       = $flusharray[$z - 4];
        $points   = $basic + $c1 + $c2 + $c3 + $c4 + $c5;
        $flushstr = false;
        $i        = 0;
        $x        = 0;
        while ($flusharray[$i] != '') {
            if ($flusharray[$i] == ($flusharray[$i + 1] - 1)) {
                $x++;
                $h = $flusharray[$i] + 1;
            }
            $i++;
        }
        if ($x > 3)
            $points = $h * 100000000;
        if (($x > 3) && ($h == 14))
            $points = $h * 1000000000;
    }
    if ($flushsuit == '') {
        $straight = false;
        $i        = 0;
        $count    = 0;
        if (($sortvalues[6] == 14) && ($sortvalues[0] == 2))
            $count = 1;
        while ($sortvalues[$i] != '') {
            if (($sortvalues[$i]) == ($sortvalues[$i + 1] - 1)) {
                $count++;
                if ($count > 3) {
                    $straight = true;
                    $res      = 'STRAIGHT';
                    $h        = $sortvalues[$i] + 1;
                    $points   = $h * 10000;
                }
            } elseif (($sortvalues[$i]) != ($sortvalues[$i + 1])) {
                $count = 0;
            }
            $i++;
        }
    }
    if ($res == '') {
        $points = $high5;
    }

    return $points;
}

$addons->add_hook(array(

    'page'     => 'includes/poker_inc.php',
    'location' => 'omaha_logic',
    'function' => 'evaluate_omaha_points',

));