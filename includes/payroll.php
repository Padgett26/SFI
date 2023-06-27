<?php
if ($myId >= 1) {
	include "includes/payrollProcessing.php";
	?><div style="text-align: center; padding: 10px;">
	<span style='font-weight: bold; font-size: 2em;'>Payroll</span>
</div>

<div style="text-align: center; padding: 10px;">
	Check this box to add a time clock to the Quick Entry box. <input
		type='checkbox' name='usePayroll' value='1'
		<?php
	echo ($usePayroll == 1) ? " checked" : "";
	?>
		onclick="togglePayroll(<?php
	echo $myId;
	?>)">
</div>
<div style="text-align: center; padding: 10px;">
	<a href='https://taxfoundation.org/publications/state-individual-income-tax-rates-and-brackets/' target='_blank'>Find your state income tax rates.</a>
</div>

<?php
	$style = "style='padding:10px; text-align:center; border:1px solid black;'";
	$endDay = date("N", $time);
	$ts = explode("-", date("Y-m-d", $time));
	$todayStart = mktime(0, 0, 0, $ts[1], $ts[2], $ts[0]);
	$ppSpan = ($endDay >= $startOfWorkWeek) ? ($endDay - $startOfWorkWeek) * 86400 : (($endDay + 7) - $startOfWorkWeek) * 86400;
	$ppEnd = ($todayStart - $ppSpan - 1);
	$getF = $db->prepare("SELECT clockIn FROM $myTimeClock WHERE paid = '0' ORDER BY clockIn LIMIT 1");
	$getF->execute();
	$getFR = $getF->fetch();
	$first = ($getFR) ? $getFR['clockIn'] : ($ppEnd - 604800);
	$ppYear = date("Y", $first);
	echo "<div style='font-weight:bold; font-size:1.25em;'>Employees</div>";

	$salaryEmp = array();
	$checkS = $db->prepare("SELECT DISTINCT employeeId FROM $myEmployeeTracking WHERE salaryPayRate > '0.00'");
	$checkS->execute();
	while ($csr = $checkS->fetch()) {
		$sEmployeeId = $csr['employeeId'];
		$salaryEmp[] = $sEmployeeId;
		$checkHD = $db->prepare("SELECT hireDate FROM $myEmployees WHERE id = ?");
		$checkHD->execute(array(
				$sEmployeeId
		));
		$chdr = $checkHD->fetch();
		if ($chdr) {
			$hireDate = $chdr['hireDate'];
		}
		$checkTC = $db->prepare("SELECT clockOut FROM $myTimeClock WHERE employeeId = ? ORDER BY clockOut DESC LIMIT 1");
		$checkTC->execute(array(
				$sEmployeeId
		));
		$ctcr = $checkTC->fetch();
		if ($ctcr) {
			$clo = $ctcr['clockOut'];
			if ($clo < $ppEnd) {
				if ($clo < $hireDate) {
					$ci = $hireDate;
					$co = $ppEnd;
				} else {
					$ci = $clo + 1;
					$co = $ppEnd;
				}
				$setTC = $db->prepare("INSERT INTO $myTimeClock VALUES(NULL,?,?,?,?,?,?,?,?,?)");
				$setTC->execute(array(
						$sEmployeeId,
						$ci,
						$co,
						'0',
						'0.00',
						'0',
						'0',
						'0',
						'0'
				));
			}
		} else {
			if ($hireDate < $fiscalYear) {
				$ci = $fiscalYear;
				$co = $ppEnd;
			} else {
				$ci = $hireDate;
				$co = $ppEnd;
			}
			$setTC = $db->prepare("INSERT INTO $myTimeClock VALUES(NULL,?,?,?,?,?,?,?,?,?)");
			$setTC->execute(array(
					$sEmployeeId,
					$ci,
					$co,
					'0',
					'0.00',
					'0',
					'0',
					'0',
					'0'
			));
		}
	}

	$getE = $db->prepare("SELECT DISTINCT employeeId FROM $myTimeClock WHERE paid = ?");
	$getE->execute(array(
			'0'
	));
	while ($getER = $getE->fetch()) {
		$eId = $getER['employeeId'];
		$getN = $db->prepare("SELECT name FROM $myEmployees WHERE id = ?");
		$getN->execute(array(
				$eId
		));
		$getNR = $getN->fetch();
		if ($getNR) {
			$name = $getNR['name'];
			echo "<div style='font-weight:bold; margin-top:20px; cursor:pointer;' onclick='toggleview(\"payroll$eId\")'>$name</div>";
		}
		$clockSpanStart = 9000000000;
		$clockSpanEnd = 0;
		$getSpan = $db->prepare("SELECT clockIn, clockOut FROM $myTimeClock WHERE paid = '0' AND employeeId = ?");
		$getSpan->execute(array(
				$eId
		));
		while ($getSpanr = $getSpan->fetch()) {
			$clockSpanStart = ($clockSpanStart > $getSpanr['clockIn']) ? $getSpanr['clockIn'] : $clockSpanStart;
			$clockSpanEnd = ($clockSpanEnd < $getSpanr['clockOut']) ? $getSpanr['clockOut'] : $clockSpanEnd;
		}
		$clockSpan = ($clockSpanEnd - $clockSpanStart);
		$ppRegTime = 0;
		$ppOTime = 0;
		$ppHolTime = 0;
		$ppRegPay = 0;
		$ppOPay = 0;
		$ppHolPay = 0;
		$ppSVTime = 0;
		$ppSVPay = 0;
		$ppTips = 0;

		echo "<form action='index.php?page=settings&r=payroll' method='post'>";

		if (in_array($eId, $salaryEmp)) {
			// Salary only

			$ppRegTime = ($clockSpan / 31536000);
			$ppRegPay = round(($ppRegTime * salaryPayRate($eId, $clockSpanStart, $myId)), 2, PHP_ROUND_HALF_UP);

			echo "<table style='margin:10px 20px; display:none;' id='payroll$eId'>";
			echo "<tr><td style='height:10px;' colspan='12'></td></tr>";
			echo "<tr><td $style>From</td>\n";
			echo "<td $style>To</td>\n";
			echo "<td $style>% of<br>year</td>\n";
			echo "<td $style>Salary</td>\n";
			echo "<td colspan='7'>&nbsp;</td>\n";
			echo "<td $style>Gross pay</td></tr>";

			echo "<tr><td $style>" . date("Y-m-d", $clockSpanStart) . "</td>\n";
			echo "<td $style>" . date("Y-m-d", $clockSpanEnd) . "</td>\n";
			echo "<td $style>" . round(($ppRegTime * 100), 2, PHP_ROUND_HALF_UP) . "%</td>\n";
			echo "<td $style>" . salaryPayRate($eId, $clockSpanStart, $myId) . "</td>\n";
			echo "<td colspan='7'>&nbsp;</td>\n";
			echo "<td $style>$ppRegPay</td></tr>";
		} else {
			// Hourly only

			echo "<table style='margin:10px 20px; display:none;' id='payroll$eId'>";
			for ($i = $ppEnd; $i >= $first; $i = $i - 604800) {
				$getPC = $db->prepare("SELECT COUNT(*) FROM $myTimeClock WHERE paid = '0' AND employeeId = ? AND clockIn <= ? AND clockIn > ?");
				$getPC->execute(array(
						$eId,
						$i,
						$i - 604800
				));
				$getPCr = $getPC->fetch();
				if ($getPCr && $getPCr[0] >= 1) {
					echo "<tr><td style='height:10px;' colspan='12'></td></tr>";
					echo "<tr><td $style colspan='12'><div style='font-weight:bold;'>Week of: " . date("Y-m-d", $i - 604799) . " to " . date("Y-m-d", $i) . "</div></td></tr>\n";
					echo "<tr><td $style>Clock in</td>\n";
					echo "<td $style>Clock out</td>\n";
					echo "<td $style>Regular<br>time</td>\n";
					echo "<td $style>Regular<br>pay</td>\n";
					echo "<td $style>Overtime</td>\n";
					echo "<td $style>Overtime<br>pay</td>\n";
					echo "<td $style>Holiday<br>time</td>\n";
					echo "<td $style>Holiday<br>pay</td>\n";
					echo "<td $style>Sick or<br>Vaca time</td>\n";
					echo "<td $style>Sick or<br>Vaca pay</td>\n";
					echo "<td $style>Reported<br>Tips</td>\n";
					echo "<td $style>Gross pay</td></tr>";

					$regTime = 0;
					$oTime = 0;
					$holTime = 0;
					$regPay = 0;
					$oPay = 0;
					$holPay = 0;
					$SVTime = 0;
					$SVPay = 0;
					$tips = 0;

					$getP = $db->prepare("SELECT * FROM $myTimeClock WHERE paid = '0' AND employeeId = ? AND clockIn <= ? AND clockIn > ? ORDER BY clockIn");
					$getP->execute(array(
							$eId,
							$i,
							$i - 604800
					));
					while ($getPr = $getP->fetch()) {
						$pId = $getPr['id'];
						$pClockIn = $getPr['clockIn'];
						$pClockOut = $getPr['clockOut'];
						$pSV = $getPr['sickOrVacation'];
						$pTips = $getPr['reportedTips'];
						$pPayRate = payRate($eId, $pClockIn, $myId);
						$amtHoliday = isHoliday($pClockIn, $pClockOut, $myId);
						$h = round($amtHoliday / 3600, 2, PHP_ROUND_HALF_UP);
						$rt = $regTime;
						$ot = $oTime;
						$ht = $holTime;
						$svt = $SVTime;

						if ($pSV == 1) {
							$SVTime += (round(($pClockOut - $pClockIn) / 3600, 2, PHP_ROUND_HALF_UP));
						} else {
							$regTime += (round(($pClockOut - $pClockIn) / 3600, 2, PHP_ROUND_HALF_UP) - $h);
							$holTime += $h;
						}
						if ($regTime > $overtimeHours) {
							$oTime += ($regTime - $overtimeHours);
							$regTime = $overtimeHours;
						}
						$regPay += (($regTime - $rt) * $pPayRate);
						$oPay += (($oTime - $ot) * $pPayRate * $overtimeMultiplier);
						$holPay += (($holTime - $ht) * $pPayRate * $holidayMultiplier);
						$SVPay += (($SVTime - $svt) * $pPayRate);
						$tips += $pTips;
						echo "<tr><td $style>" . date("Y-m-d G:i", $pClockIn) . "</td>\n";
						echo "<td $style>" . date("Y-m-d G:i", $pClockOut) . "</td>\n";
						echo "<td $style>";
						echo ($regTime - $rt > 0.00) ? ($regTime - $rt) : "&nbsp;";
						echo "</td>\n";
						echo "<td $style>";
						echo (($regTime - $rt) * $pPayRate > 0.00) ? round((($regTime - $rt) * $pPayRate), 2, PHP_ROUND_HALF_UP) : "&nbsp;";
						echo "</td>\n";
						echo "<td $style>";
						echo ($oTime - $ot > 0.00) ? ($oTime - $ot) : "&nbsp;";
						echo "</td>\n";
						echo "<td $style>";
						echo (($oTime - $ot) * $pPayRate * $overtimeMultiplier > 0.00) ? round((($oTime - $ot) * $pPayRate * $overtimeMultiplier), 2, PHP_ROUND_HALF_UP) : "&nbsp;";
						echo "</td>\n";
						echo "<td $style>";
						echo ($holTime - $ht > 0.00) ? ($holTime - $ht) : "&nbsp;";
						echo "</td>\n";
						echo "<td $style>";
						echo (($holTime - $ht) * $pPayRate * $holidayMultiplier > 0.00) ? round((($holTime - $ht) * $pPayRate * $holidayMultiplier), 2, PHP_ROUND_HALF_UP) : "&nbsp;";
						echo "</td>\n";
						echo "<td $style>";
						echo ($SVTime - $svt > 0.00) ? ($SVTime - $svt) : "&nbsp;";
						echo "</td>\n";
						echo "<td $style>";
						echo (($SVTime - $svt) * $pPayRate > 0.00) ? round((($SVTime - $svt) * $pPayRate), 2, PHP_ROUND_HALF_UP) : "&nbsp;";
						echo "</td>\n";
						echo "<td $style>";
						echo ($pTips > 0.00) ? $pTips : "&nbsp;";
						echo "</td>\n";
						echo "<td>&nbsp;</td></tr>";
					}
					$regPay = round($regPay, 2, PHP_ROUND_HALF_UP);
					$oPay = round($oPay, 2, PHP_ROUND_HALF_UP);
					$holPay = round($holPay, 2, PHP_ROUND_HALF_UP);
					$SVPay = round($SVPay, 2, PHP_ROUND_HALF_UP);
					echo "<tr><td $style colspan='2'>Totals</td>\n";
					echo "<td $style>";
					echo ($regTime > 0.00) ? $regTime : "&nbsp;";
					echo "</td>\n";
					echo "<td $style>";
					echo ($regPay > 0.00) ? $regPay : "&nbsp;";
					echo "</td>\n";
					echo "<td $style>";
					echo ($oTime > 0.00) ? $oTime : "&nbsp;";
					echo "</td>\n";
					echo "<td $style>";
					echo ($oPay > 0.00) ? $oPay : "&nbsp;";
					echo "</td>\n";
					echo "<td $style>";
					echo ($holTime > 0.00) ? $holTime : "&nbsp;";
					echo "</td>\n";
					echo "<td $style>";
					echo ($holPay > 0.00) ? $holPay : "&nbsp;";
					echo "</td>\n";
					echo "<td $style>";
					echo ($SVTime > 0.00) ? $SVTime : "&nbsp;";
					echo "</td>\n";
					echo "<td $style>";
					echo ($SVPay > 0.00) ? $SVPay : "&nbsp;";
					echo "</td>\n";
					echo "<td $style>";
					echo ($tips > 0.00) ? $tips : "&nbsp;";
					echo "</td>\n";
					echo "<td $style>" . $regPay + $oPay + $holPay + $SVPay + $tips . "</td></tr>";

					$ppRegTime += $regTime;
					$ppOTime += $oTime;
					$ppHolTime += $holTime;
					$ppSVTime += $SVTime;
					$ppRegPay += $regPay;
					$ppOPay += $oPay;
					$ppHolPay += $holPay;
					$ppSVPay += $SVPay;
					$ppTips += $tips;
				}
			}
		}

		// Hourly and Salary

		$bonusTotal = 0.00;

		$getB1 = $db->prepare("SELECT COUNT(*) FROM $myEmployeeTracking WHERE employeeId = ? AND otherPayPaid = '0' AND otherPay > ?");
		$getB1->execute(array(
				$eId,
				'0.00'
		));
		$getB1r = $getB1->fetch();
		if ($getB1r && $getB1r[0] > 0) {
			echo "<tr><td style='height:10px;' colspan='12'></td></tr>";
			echo "<tr><td $style colspan='12'><div style='font-weight:bold;'>Bonuses and other pay</div></td></tr>\n";
			echo "<tr>";
			echo "<td $style colspan='2'>Date</td>\n";
			echo "<td $style colspan='8'>Description</td>\n";
			echo "<td $style>Amount</td>\n";
			echo "<td $style>Gross pay</td></tr>";

			$getB2 = $db->prepare("SELECT date,otherPay,description FROM $myEmployeeTracking WHERE employeeId = ? AND otherPayPaid = '0' AND otherPay > ? ORDER BY date");
			$getB2->execute(array(
					$eId,
					'0.00'
			));
			while ($getB2r = $getB2->fetch()) {
				$bDate = $getB2r['date'];
				$bOtherPay = $getB2r['otherPay'];
				$bDescription = $getB2r['description'];
				$bonusTotal += $bOtherPay;

				echo "<tr>";
				echo "<td $style colspan='2'>" . date("Y-m-d G:i", $bDate) . "</td>\n";
				echo "<td $style colspan='8'>$bDescription</td>\n";
				echo "<td $style>$bOtherPay</td>\n";
				echo "<td $style>$bonusTotal</td></tr>";
			}
		}

		echo "<tr><td style='height:10px;' colspan='12'></td></tr>";
		echo "<tr><td $style colspan='12'><div style='font-weight:bold;'>Pay Period Totals</div></td></tr>\n";
		echo "<tr><td colspan='2'>&nbsp;</td>\n";
		echo "<td $style>Regular<br>time</td>\n";
		echo "<td $style>Regular<br>pay</td>\n";
		echo "<td $style>Overtime</td>\n";
		echo "<td $style>Overtime<br>pay</td>\n";
		echo "<td $style>Holiday<br>time</td>\n";
		echo "<td $style>Holiday<br>pay</td>\n";
		echo "<td $style>Sick or<br>Vaca time</td>\n";
		echo "<td $style>Sick or<br>Vaca pay</td>\n";
		echo "<td $style>Reported<br>Tips</td>\n";
		echo "<td $style>Gross pay</td></tr>";

		echo "<tr><td $style colspan='2'>Pay Period Totals</td>\n";
		echo "<td $style>";
		echo ($ppRegTime > 0.00) ? $ppRegTime : "&nbsp;";
		echo "</td>\n";
		echo "<td $style>";
		echo ($ppRegPay > 0.00) ? $ppRegPay : "&nbsp;";
		echo "</td>\n";
		echo "<td $style>";
		echo ($ppOTime > 0.00) ? $ppOTime : "&nbsp;";
		echo "</td>\n";
		echo "<td $style>";
		echo ($ppOPay > 0.00) ? $ppOPay : "&nbsp;";
		echo "</td>\n";
		echo "<td $style>";
		echo ($ppHolTime > 0.00) ? $ppHolTime : "&nbsp;";
		echo "</td>\n";
		echo "<td $style>";
		echo ($ppHolPay > 0.00) ? $ppHolPay : "&nbsp;";
		echo "</td>\n";
		echo "<td $style>";
		echo ($ppSVTime > 0.00) ? $ppSVTime : "&nbsp;";
		echo "</td>\n";
		echo "<td $style>";
		echo ($ppSVPay > 0.00) ? $ppSVPay : "&nbsp;";
		echo "</td>\n";
		echo "<td $style>";
		echo ($ppTips > 0.00) ? $ppTips : "&nbsp;";
		echo "</td>\n";
		echo "<td $style>" . $ppRegPay + $ppOPay + $ppHolPay + $ppSVPay + $ppTips + $bonusTotal . "</td></tr>";

		$tot = $running = $ppRegPay + $ppOPay + $ppHolPay + $ppSVPay + $ppTips;
		$tot *= (31536000 / $clockSpan);
		$running += $bonusTotal;

		$filingType = 1;
		$HealthInsEmployee = 0.00;
		$HealthInsCompany = 0.00;
		$Retirement401kEmployee = 0.00;
		$Retirement401kCompany = 0.00;
		$TaxWitholding = 0.00;
		$Garnishment = 0.00;
		$OtherWitholding = 0.00;
		$getD = $db->prepare("SELECT w4_1c,w4_4c,eHealthIns,cHealthIns,eRetirement401k,cRetirement401k,garnishment,otherWitholding FROM $myEmployees WHERE id = ?");
		$getD->execute(array(
				$eId
		));
		$getDR = $getD->fetch();
		if ($getDR) {
			$filingType = $getDR['w4_1c'];
			$HealthInsEmployee = $getDR['eHealthIns'];
			$HealthInsCompany = $getDR['cHealthIns'];
			$Retirement401kEmployee = $getDR['eRetirement401k'];
			$Retirement401kCompany = $getDR['cRetirement401k'];
			$TaxWitholding = $getDR['w4_4c'];
			$Garnishment = $getDR['garnishment'];
			$OtherWitholding = $getDR['otherWitholding'];
		}

		$running -= ($HealthInsEmployee + $Retirement401kEmployee + $TaxWitholding + $Garnishment + $OtherWitholding);

		switch ($filingType) {
			case 1:
				$fType = "single";
				break;
			case 2:
				$fType = "marriedJointly";
				break;
			case 3:
				$fType = "marriedSeparately";
				break;
			case 4:
				$fType = "headOfHousehold";
				break;
			default:
				$fType = "single";
		}

		$ftt = $db->prepare("SELECT * FROM fedTaxTables WHERE year = ? AND tableName = ?");
		$ftt->execute(array(
				$ppYear,
				$fType
		));
		$fttr = $ftt->fetch();
		if ($fttr) {
			$tot -= $fttr['stdDeduction'];

			$income = 0;
			$tax = 0.00;
			$perOnExcess = 0.1;

			for ($a = 1; $a <= 7; ++ $a) {
				$b = "income" . $a;
				if ($tot > $fttr[$b]) {
					$in = "income" . $a;
					$ta = "tax" . $a;
					$pe = "perOnExcess" . $a;
					$income = $fttr[$in];
					$tax = $fttr[$ta];
					$perOnExcess = $fttr[$pe];
					break;
				}
			}
		}

		if ($tot > 0) {
			$fedTax = round((($tax + $perOnExcess * ($tot - $income)) * ($clockSpan / 31536000) + ($bonusTotal * $perOnExcess)), 2, PHP_ROUND_HALF_UP);
			$running -= $fedTax;

			$SSb = (($tot + $bonusTotal) >= $SSBaseRate) ? $SSBaseRate : $tot;
			$SSc = $SSe = round((($SSb * $SSRate) * ($clockSpan / 31536000) + ($bonusTotal * $SSRate)), 2, PHP_ROUND_HALF_UP);
			$running -= $SSe;

			if (($tot + $bonusTotal) > $medicareBaseRate) {
				$Mc = round(($tot * $medicareRate * ($clockSpan / 31536000) + ($bonusTotal * $medicareRate)), 2, PHP_ROUND_HALF_UP);
				$Me = round($tot * ($medicareRate + $medicareAddOn) * ($clockSpan / 31536000) + ($bonusTotal * $medicareRate + $medicareAddOn), 2, PHP_ROUND_HALF_UP);
			} else {
				$Mc = $Me = round($tot * $medicareRate * ($clockSpan / 31536000) + ($bonusTotal * $medicareRate), 2, PHP_ROUND_HALF_UP);
			}
			$running -= $Me;

			$Fc = ((($tot + $bonusTotal) - $futaBaseRate) * $futaRate >= 0) ? round(($tot - $futaBaseRate) * $futaRate * ($clockSpan / 31536000) + ($bonusTotal * $futaRate), 2, PHP_ROUND_HALF_UP) : 0.00;
		} else {
			$fedTax = $SSc = $SSe = $Mc = $Me = $Fc = 0.00;
		}
		echo "<tr>";
		echo "<td colspan='9'>&nbsp;</td>\n";
		echo "<td $style>Company<br>Responsibility</td>\n";
		echo "<td $style>Employee<br>Responsibility</td>\n";
		echo "<td>&nbsp;</td></tr>";

		echo "<tr>";
		echo "<td colspan='6'>&nbsp;</td>\n";
		echo "<td $style colspan='3'>Federal Income Tax</td>\n";
		echo "<td $style>&nbsp;</td>\n";
		echo "<td $style>$fedTax</td>\n";
		echo "<td>&nbsp;</td></tr>";

		echo "<tr>";
		echo "<td colspan='6'>&nbsp;</td>\n";
		echo "<td $style colspan='3'>State Income Tax</td>\n";
		echo "<td $style>&nbsp;</td>\n";
		echo "<td $style><input type='number' name='stateTax' value='0.00' min='0.00' step='0.01' id='state$eId' oninput='addST(\"$eId\")'>";
		echo "<input type='hidden' name='running' value='$running' id='running$eId'></td>\n";
		echo "<td>&nbsp;</td></tr>";

		echo "<tr>";
		echo "<td colspan='6'>&nbsp;</td>\n";
		echo "<td $style colspan='3'>Social Security</td>\n";
		echo "<td $style>$SSc</td>\n";
		echo "<td $style>$SSe</td>\n";
		echo "<td>&nbsp;</td></tr>";

		echo "<tr>";
		echo "<td colspan='6'>&nbsp;</td>\n";
		echo "<td $style colspan='3'>Medicare</td>\n";
		echo "<td $style>$Mc</td>\n";
		echo "<td $style>$Me</td>\n";
		echo "<td>&nbsp;</td></tr>";

		echo "<tr>";
		echo "<td colspan='6'>&nbsp;</td>\n";
		echo "<td $style colspan='3'>Futa</td>\n";
		echo "<td $style>$Fc</td>\n";
		echo "<td $style>&nbsp;</td>\n";
		echo "<td>&nbsp;</td></tr>";

		echo "<tr>";
		echo "<td colspan='6'>&nbsp;</td>\n";
		echo "<td $style colspan='3'>Health Ins</td>\n";
		echo "<td $style>$HealthInsCompany</td>\n";
		echo "<td $style>$HealthInsEmployee</td>\n";
		echo "<td>&nbsp;</td></tr>";

		echo "<tr>";
		echo "<td colspan='6'>&nbsp;</td>\n";
		echo "<td $style colspan='3'>Retirement 401k</td>\n";
		echo "<td $style>$Retirement401kCompany</td>\n";
		echo "<td $style>$Retirement401kEmployee</td>\n";
		echo "<td>&nbsp;</td></tr>";

		echo "<tr>";
		echo "<td colspan='6'>&nbsp;</td>\n";
		echo "<td $style colspan='3'>Extra Tax Witholding<br>From W4 form line 4c</td>\n";
		echo "<td $style>&nbsp;</td>\n";
		echo "<td $style>$TaxWitholding</td>\n";
		echo "<td>&nbsp;</td></tr>";

		echo "<tr>";
		echo "<td colspan='6'>&nbsp;</td>\n";
		echo "<td $style colspan='3'>Garnishment</td>\n";
		echo "<td $style>&nbsp;</td>\n";
		echo "<td $style>$Garnishment</td>\n";
		echo "<td>&nbsp;</td></tr>";

		echo "<tr>";
		echo "<td colspan='6'>&nbsp;</td>\n";
		echo "<td $style colspan='3'>Other Witholding</td>\n";
		echo "<td $style>&nbsp;</td>\n";
		echo "<td $style>$OtherWitholding</td>\n";
		echo "<td>&nbsp;</td></tr>";

		echo "<tr>";
		echo "<td colspan='6'>&nbsp;</td>\n";
		echo "<td $style colspan='5'>Net Pay</td>\n";
		echo "<td $style class='netPay$eId'>$running</td></tr>";

		echo "<tr><td style='height:10px;' colspan='12'></td></tr>";

		echo "<tr>";
		echo "<td colspan='4'>Pay employee and mark all time as paid.<br>This will make all necessary changes to the ledger.</td>\n";
		echo "<td $style colspan='3'>Pay from account:<br><select name='fromAcc' size='1'>";
		foreach ($ACCOUNTS as $key => $val) {
			if ($key >= 100.0 && $key <= 199.9) {
				echo "<option value='$key'>$val</option>\n";
			}
		}
		echo "</select></td>\n";
		echo "<td $style colspan='2'>Net Pay<br><span  class='netPay$eId'>$running</span></td>\n";
		echo "<td $style colspan='2'>Check #: <input type='number' name='checkNum' value='0' min='0' step='1'></td>\n";
		echo "<td $style>";
		echo "<input type='hidden' name='fedTax' value='$fedTax'>";
		echo "<input type='hidden' name='netPay' value='$running'>";
		echo "<input type='hidden' name='SSe' value='$SSe'>";
		echo "<input type='hidden' name='SSc' value='$SSc'>";
		echo "<input type='hidden' name='Me' value='$Me'>";
		echo "<input type='hidden' name='Mc' value='$Mc'>";
		echo "<input type='hidden' name='Fc' value='$Fc'>";
		echo "<input type='hidden' name='HealthInsEmployee' value='$HealthInsEmployee'>";
		echo "<input type='hidden' name='HealthInsCompany' value='$HealthInsCompany'>";
		echo "<input type='hidden' name='Retirement401kEmployee' value='$Retirement401kEmployee'>";
		echo "<input type='hidden' name='Retirement401kCompany' value='$Retirement401kCompany'>";
		echo "<input type='hidden' name='TaxWitholding' value='$TaxWitholding'>";
		echo "<input type='hidden' name='Garnishment' value='$Garnishment'>";
		echo "<input type='hidden' name='OtherWitholding' value='$OtherWitholding'>";
		echo "<input type='hidden' name='ppEnd' value='$ppEnd'>";
		echo "<input type='hidden' name='empName' value='$name'>";
		echo "<input type='hidden' name='processPay' value='$eId'>";
		echo "<button> Pay Employee </button></td><td></td></tr>";

		echo "</table></form>";

		$fedTax = $SSc = $SSe = $Mc = $Me = $Fc = $ppRegTime = $ppOTime = $ppHolTime = $ppRegPay = $ppOPay = $ppHolPay = $ppSVTime = $ppSVPay = $ppTips = $regTime = $oTime = $holTime = $regPay = $oPay = $holPay = $SVTime = $SVPay = $tips = 0;
	}
}