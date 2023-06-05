<div class="heading">Budgeting</div>
<?php
if ($myId >= 1) {
    $accId = (filter_input(INPUT_GET, 'account', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(
            INPUT_GET, 'account', FILTER_SANITIZE_NUMBER_INT) : 0;
    ?>
<div style="margin: 20px 0px;">
	<form action="index.php?page=budget" method="post">
		Date Range: From: <input type="date" name="dateRangeStart"
			value="<?php

    echo date('Y-m-d', $dateRangeStart);
    ?>"> To: <input
			type="date" name="dateRangeEnd"
			value="<?php

    echo date('Y-m-d', $dateRangeEnd);
    ?>"> <input
			type="submit" value=" GO ">
	</form>
</div>
<?php
    if (filter_input(INPUT_POST, 'budgetTermUp', FILTER_SANITIZE_NUMBER_INT) ==
            '1') {
        $budgetTerm = (filter_input(INPUT_POST, 'budgetTerm',
                FILTER_SANITIZE_NUMBER_INT)) ? filter_input(INPUT_POST,
                'budgetTerm', FILTER_SANITIZE_NUMBER_INT) : 0;
        $bt = $db->prepare("UPDATE $mySettings SET budgetTerm = ?");
        $bt->execute(array(
                $budgetTerm
        ));
    }
    if (filter_input(INPUT_POST, 'bUp', FILTER_SANITIZE_NUMBER_INT) == '1') {
        foreach ($_POST as $key => $val) {
            if (preg_match("/^budget([0-9][0-9]*)$/", $key, $match)) {
                $aId = $match[1];
                $v = filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT,
                        FILTER_FLAG_ALLOW_FRACTION);
                $budgetUp = $db->prepare(
                        "UPDATE $myFAccounts SET budget = ? WHERE id = ?");
                $budgetUp->execute(array(
                        $v,
                        $aId
                ));
            }
        }
    }

    if ($budgetTerm == 0) {
        echo "<div style='text-align:left; font-weight:bold;'>Lets get you started on your budget:<br><br>";
        echo "What time frame would you like to budget?<br><br>";
        echo "<form action='index.php?page=budget' method='post'>";
        echo "<input type='radio' name='budgetTerm' value='0'";
        echo ($budgetTerm == 0) ? " selected>" : ">";
        echo " Do not use a budget<br>";
        echo "<input type='radio' name='budgetTerm' value='1'";
        echo ($budgetTerm == 1) ? " selected>" : ">";
        echo " Weekly Budget<br>";
        echo "<input type='radio' name='budgetTerm' value='2'";
        echo ($budgetTerm == 2) ? " selected>" : ">";
        echo " Monthly Budget<br>";
        echo "<input type='radio' name='budgetTerm' value='3'";
        echo ($budgetTerm == 3) ? " selected>" : ">";
        echo " Fiscal Year Budget<br><br>";
        echo "<input type='submit' value=' Select '><input type='hidden' name='budgetTermUp' value='1'>";
        echo "</form></div>";
    } else {
        switch ($budgetTerm) {
            case 1:
                $term = "Week";
                $est = 52;
                $w = date("w", $dateRangeStart);
                $i = $dateRangeStart - (86400 * $w);
                break;
            case 2:
                $term = "Month";
                $est = 12;
                $w = date("d", $dateRangeStart);
                $i = $dateRangeStart - (86400 * ($w - 1));
                break;
            case 3:
                $term = "Fiscal Year";
                $est = 1;
                $w = date("z", $dateRangeStart);
                $i = $dateRangeStart - (86400 * ($w - 1));
                break;
        }
        $ranges = array(
                array(
                        0,
                        0
                )
        );
        $t = 0;
        while ($i <= $dateRangeEnd) {
            if ($budgetTerm == 1) {
                $ranges[$t][0] = $i;
                $i = strtotime("+ 1 week", $i);
                $ranges[$t][1] = $i;
                $t ++;
            } elseif ($budgetTerm == 2) {
                $ranges[$t][0] = $i;
                $i = strtotime("+ 1 month", $i);
                $ranges[$t][1] = $i;
                $t ++;
            } elseif ($budgetTerm == 3) {
                $ranges[$t][0] = $i;
                $i = strtotime("+ 1 year", $i);
                $ranges[$t][1] = $i;
                $t ++;
            }
        }
        echo "<form action='index.php?page=budget' method='post'>\n";
        for ($k = 100; $k <= 500; $k = $k + 100) {
            switch ($k) {
                case 100:
                    $title = "Assets";
                    $beginAcc = 100.0;
                    $endAcc = 199.9;
                    break;
                case 200:
                    $title = "Liabilities";
                    $beginAcc = 200.0;
                    $endAcc = 299.9;
                    break;
                case 300:
                    $title = "Capital";
                    $beginAcc = 300.0;
                    $endAcc = 399.9;
                    break;
                case 400:
                    $title = "Income";
                    $beginAcc = 400.0;
                    $endAcc = 499.9;
                    break;
                case 500:
                    $title = "Expenses";
                    $beginAcc = 500.0;
                    $endAcc = 599.9;
                    break;
            }
            echo "<div style='font-weight:bold; font-size:1.25em; margin:20px 0px; cursor:pointer;' onclick='toggleview(\"div$title\")'>$title</div>\n";
            echo "<div style='display:none;' id='div$title'>";
            echo "<table cellspacing='0px' style='border:1px solid black;'>\n";
            echo "<tr>\n";
            echo "<td style='border:1px solid black;'>Account</td>\n";
            foreach ($ranges as $v) {
                switch ($budgetTerm) {
                    case 1:
                        $labelms = date("m", $v[0]);
                        $labelds = date("d", $v[0]);
                        $labelme = date("m", $v[1]);
                        $labelde = date("d", $v[1]);
                        $label = "$labelms/$labelds-$labelme/$labelde";
                        break;
                    case 2:
                        $label = date("M", $v[0]);
                        break;
                    case 3:
                        $label = date("Y", $v[0]);
                        break;
                }
                echo "<td style='border:1px solid black; text-align:center;'>$label</td>\n";
            }
            echo "<td style='border:1px solid black;'>$term Avg</td>\n";
            echo "<td style='border:1px solid black;'>Year Estimated</td>\n";
            echo "<td style='border:1px solid black;'>$term Budget Amount</td>\n";
            echo "</tr>\n";
            $runningTerm = 0.00;
            $runningYear = 0.00;
            $runningBudget = 0.00;
            $getA = $db->prepare(
                    "SELECT id, accountNumber, accountName, budget from $myFAccounts WHERE accountNumber >= ? AND accountNumber <= ? ORDER BY accountNumber");
            $getA->execute(array(
                    $beginAcc,
                    $endAcc
            ));
            while ($ga = $getA->fetch()) {
                if ($ga) {
                    $accId = $ga['id'];
                    $accNum = $ga['accountNumber'];
                    $accName = $ga['accountName'];
                    $accBudget = $ga['budget'];
                    $avg = array(
                            0,
                            0.00
                    );
                    echo "<tr>\n";
                    echo "<td style='border:1px solid black;'><a href='index.php?page=accountDetail&account=$accId'>$accName</a></td>\n";
                    $c = 0;
                    foreach ($ranges as $v) {
                        $c ++;
                        $tot = 0.00;
                        $start = $v[0];
                        $end = $v[1];
                        $currentLedger = $db->prepare(
                                "SELECT debitAmount, creditAmount FROM $myFLedger WHERE date >= ? AND date <= ? AND accountNumber = ?");
                        $currentLedger->execute(array(
                                $start,
                                $end,
                                $accNum
                        ));
                        while ($cl = $currentLedger->fetch()) {
                            if ($cl) {
                                if ($k == 100 || $k == 500) {
                                    $tot -= $cl['creditAmount'];
                                    $tot += $cl['debitAmount'];
                                } else {
                                    $tot += $cl['creditAmount'];
                                    $tot -= $cl['debitAmount'];
                                }
                            }
                        }
                        $oldLedger = $db->prepare(
                                "SELECT debitAmount, creditAmount FROM $myFLedgerOld WHERE date >= ? AND date <= ? AND accountNumber = ?");
                        $oldLedger->execute(array(
                                $start,
                                $end,
                                $accNum
                        ));
                        while ($ol = $oldLedger->fetch()) {
                            if ($ol) {
                                if ($k == 100 || $k == 500) {
                                    $tot -= $ol['creditAmount'];
                                    $tot += $ol['debitAmount'];
                                } else {
                                    $tot += $ol['creditAmount'];
                                    $tot -= $ol['debitAmount'];
                                }
                            }
                        }
                        $avg[1] += $tot;
                        $avg[0] ++;
                        echo "<td style='border:1px solid black; text-align:right;'>" .
                                money_sfi($tot, $currency, $langCode) . "</td>\n";
                    }
                    $termAvg = ($avg[0] >= 1) ? ($avg[1] / $avg[0]) : 0.00;
                    $yearAvg = ($termAvg * $est);
                    $runningTerm += $termAvg;
                    $runningYear += $yearAvg;
                    $runningBudget += $accBudget;
                    echo "<td style='border:1px solid black; text-align:right;'>" .
                            money_sfi($termAvg, $currency, $langCode) . "</td>\n";
                    echo "<td style='border:1px solid black; text-align:right;'>" .
                            money_sfi($yearAvg, $currency, $langCode) . "</td>\n";
                    echo "<td style='border:1px solid black;'><input type='number' name='budget$accId' value='$accBudget' step='.01'></td>\n";
                    echo "</tr>\n";
                }
            }
            echo "<tr>\n";
            echo "<td style='border:1px solid black; text-align:center;'><input type='submit' value=' Set $title Budget '><input type='hidden' name='bUp' value='1'></td>\n";
            echo "<td style='border:1px solid black;' colspan='$c'>&nbsp;</td>\n";
            echo "<td style='border:1px solid black; text-align:right;'>" .
                    money_sfi($runningTerm, $currency, $langCode) . "</td>\n";
            echo "<td style='border:1px solid black; text-align:right;'>" .
                    money_sfi($runningYear, $currency, $langCode) . "</td>\n";
            echo "<td style='border:1px solid black; text-align:right;'>" .
                    money_sfi($runningBudget, $currency, $langCode) . "</td>\n";
            echo "</tr>\n";
            echo "</table></div>\n";
        }
        echo "</form>\n";
    }
} else {
    echo "You must log in / register to use this page.";
}