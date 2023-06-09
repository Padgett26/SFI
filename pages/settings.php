<div class="heading">Settings</div>
<?php
if ($myId >= 1) {
	$r = (filter_input(INPUT_GET, 'r', FILTER_SANITIZE_STRING)) ? filter_input(INPUT_GET, 'r', FILTER_SANITIZE_STRING) : '0';
	$Merror = "";

	if (filter_input(INPUT_POST, 'vehicleUp', FILTER_SANITIZE_STRING)) {
		$vId = filter_input(INPUT_POST, 'vehicleUp', FILTER_SANITIZE_STRING);
		$vName = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
		$vLicensePlate = filter_input(INPUT_POST, 'licensePlate', FILTER_SANITIZE_STRING);
		$vVin = filter_input(INPUT_POST, 'vin', FILTER_SANITIZE_STRING);
		$vAssignedTo = filter_input(INPUT_POST, 'assignedTo', FILTER_SANITIZE_NUMBER_INT);
		$vRetired = (filter_input(INPUT_POST, 'retired', FILTER_SANITIZE_NUMBER_INT) == 1) ? 1 : 0;

		if ($vId == 'new') {
			$setV = $db->prepare("INSERT INTO $myVehicles VALUES(NULL,'','','','0','0','0')");
			$setV->execute();
			$getV = $db->prepare("SELECT id FROM $myVehicles ORDER BY id DESC LIMIT 1");
			$getV->execute();
			$getVR = $getV->fetch();
			if ($getVR) {
				$vId = $getVR['id'];
			}
		}
		$putV = $db->prepare("UPDATE $myVehicles SET name = ?, licensePlate = ?, vin = ?, assignedTo = ?, retired = ? WHERE id = ?");
		$putV->execute(array(
				$vName,
				$vLicensePlate,
				$vVin,
				$vAssignedTo,
				$vRetired,
				$vId
		));
	}

	if (filter_input(INPUT_POST, 'employeeUp', FILTER_SANITIZE_STRING)) {
		$eId = filter_input(INPUT_POST, 'employeeUp', FILTER_SANITIZE_STRING);
		$eName = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
		$eEmail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
		$eSsn = filter_input(INPUT_POST, 'ssn', FILTER_SANITIZE_NUMBER_INT);
		$eW4_1c = filter_input(INPUT_POST, 'w4_1c', FILTER_SANITIZE_NUMBER_INT);
		$eW4_2c = filter_input(INPUT_POST, 'w4_2c', FILTER_SANITIZE_NUMBER_INT);
		$eW4_3 = filter_input(INPUT_POST, 'w4_3', FILTER_SANITIZE_NUMBER_INT);
		$eW4_4a = filter_input(INPUT_POST, 'w4_4a', FILTER_SANITIZE_NUMBER_INT);
		$eW4_4b = filter_input(INPUT_POST, 'w4_4b', FILTER_SANITIZE_NUMBER_INT);
		$eW4_4c = filter_input(INPUT_POST, 'w4_4c', FILTER_SANITIZE_NUMBER_INT);
		$eHealthIns = filter_input(INPUT_POST, 'eHealthIns', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$cHealthIns = filter_input(INPUT_POST, 'cHealthIns', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$eRetirement401k = filter_input(INPUT_POST, 'eRetirement401k', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$cRetirement401k = filter_input(INPUT_POST, 'cRetirement401k', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$eGarnishment = filter_input(INPUT_POST, 'garnishment', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$eOtherWitholding = filter_input(INPUT_POST, 'otherWitholding', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$HD = explode("-", filter_input(INPUT_POST, 'hireDate', FILTER_SANITIZE_NUMBER_INT));
		$eHireDate = mktime(0, 0, 0, $HD[1], $HD[2], $HD[0]);
		$TD = explode("-", filter_input(INPUT_POST, 'terminateDate', FILTER_SANITIZE_NUMBER_INT));
		$eTerminateDate = ($TD[1] >= 1 && $TD[2] >= 1 && $TD[0] >= 1) ? mktime(0, 0, 0, $TD[1], $TD[2], $TD[0]) : 0;
		$eAddress = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
		$eCityStZip = filter_input(INPUT_POST, 'cityStZip', FILTER_SANITIZE_STRING);
		$ePhone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT);
		$eHourlyPayRate = filter_input(INPUT_POST, 'hourlyPayRate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$eSalaryPayRate = filter_input(INPUT_POST, 'salaryPayRate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$PD = explode("-", filter_input(INPUT_POST, 'payRateDate', FILTER_SANITIZE_NUMBER_INT));
		$ePayRateDate = mktime(0, 0, 0, $PD[1], $PD[2], $PD[0]);
		$eDescription = htmlentities(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING), ENT_QUOTES);

		if ($eId == 'new') {
			$eSet = $db->prepare("INSERT INTO $myEmployees VALUES(NULL,'','0','0','0','','','','0','0','0','0','0','0','0','0.00','0.00','0.00','0.00','0.00','0.00','0','0')");
			$eSet->execute();

			$eGet = $db->prepare("SELECT id FROM $myEmployees ORDER BY id DESC LIMIT 1");
			$eGet->execute();
			$eGetR = $eGet->fetch();
			if ($eGetR) {
				$eId = $eGetR['id'];
			}
		}
		$eUp = $db->prepare("UPDATE $myEmployees SET name = ?,ssn = ?,hireDate = ?,terminateDate = ?,email = ?,address = ?,cityStZip = ?,phone = ?,w4_1c = ?,w4_2c = ?,w4_3 = ?,w4_4a = ?,w4_4b = ?,w4_4c = ?,eHealthIns = ?,cHealthIns = ?,eRetirement401k = ?,cRetirement401k = ?,garnishment = ?,otherWitholding = ? WHERE id = ?");
		$eUp->execute(array(
				$eName,
				$eSsn,
				$eHireDate,
				$eTerminateDate,
				$eEmail,
				$eAddress,
				$eCityStZip,
				$ePhone,
				$eW4_1c,
				$eW4_2c,
				$eW4_3,
				$eW4_4a,
				$eW4_4b,
				$eW4_4c,
				$eHealthIns,
				$cHealthIns,
				$eRetirement401k,
				$cRetirement401k,
				$eGarnishment,
				$eOtherWitholding,
				$eId
		));

		$getPay = $db->prepare("SELECT hourlyPayRate, salaryPayRate FROM $myEmployeeTracking WHERE employeeId = ? ORDER BY date DESC LIMIT 1");
		$getPay->execute(array(
				$eId
		));
		$getPR = $getPay->fetch();
		if ($getPR) {
			$hpr = $getPR['hourlyPayRate'];
			$spr = $getPR['salaryPayRate'];
			if ($eHourlyPayRate != $hpr || $eSalaryPayRate != $spr) {
				$entry1 = $db->prepare("INSERT INTO $myEmployeeTracking VALUES(NULL,?,?,?,?,?,?,?,?)");
				$entry1->execute(array(
						$eId,
						$ePayRateDate,
						$eHourlyPayRate,
						$eSalaryPayRate,
						'0.00',
						$eDescription,
						'0',
						'0'
				));
			}
		} else {
			$entry2 = $db->prepare("INSERT INTO $myEmployeeTracking VALUES(NULL,?,?,?,?,?,?,?,?)");
			$entry2->execute(array(
					$eId,
					$ePayRateDate,
					$eHourlyPayRate,
					$eSalaryPayRate,
					'0.00',
					$eDescription,
					'0',
					'0'
			));
		}
	}
	if (filter_input(INPUT_POST, 'close', FILTER_SANITIZE_NUMBER_INT) == 1) {
		$retainedEarnings = filter_input(INPUT_POST, 'retainedEarnings', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

		$getAB = $db->prepare("SELECT id, startBalance FROM $myFAccounts");
		$getAB->execute();
		while ($gab = $getAB->fetch()) {
			$gabId = $gab['id'];
			$gabPrev = $gab['startBalance'];

			$setAB = $db->prepare("UPDATE $myFAccounts SET prevStartBalance = ? WHERE id = ?");
			$setAB->execute(array(
					$gabPrev,
					$gabId
			));
		}

		foreach ($_POST as $k => $v) {
			if (preg_match("/^endB([0-9][0-9]*)$/", $k, $match)) {
				$upVal = filter_var($v, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
				$updateStart = $db->prepare("UPDATE $myFAccounts SET startBalance = ? WHERE id = ?");
				$updateStart->execute(array(
						$upVal,
						$match[1]
				));
			}
		}

		$setR = $db->prepare("UPDATE $myFAccounts SET startBalance = ? WHERE accountNumber = ?");
		$setR->execute(array(
				$retainedEarnings,
				'399.9'
		));

		$setZero = $db->prepare("UPDATE $myFAccounts SET startBalance = '0.00' WHERE accountNumber >= '400.0' AND accountNumber <= '599.9'");
		$setZero->execute();
	}
	if (filter_input(INPUT_POST, 'settingsUp', FILTER_SANITIZE_STRING) == 'company') {
		$name = filter_var(htmlEntities(trim($_POST['name']), ENT_QUOTES), FILTER_SANITIZE_STRING);
		$address1 = filter_var(htmlEntities(trim($_POST['address1']), ENT_QUOTES), FILTER_SANITIZE_STRING);
		$address2 = filter_var(htmlEntities(trim($_POST['address2']), ENT_QUOTES), FILTER_SANITIZE_STRING);
		$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
		$EIN = filter_input(INPUT_POST, 'EIN', FILTER_SANITIZE_STRING);
		$stateEIN = filter_input(INPUT_POST, 'stateEIN', FILTER_SANITIZE_STRING);
		$startOfWorkWeek = filter_input(INPUT_POST, 'startOfWorkWeek', FILTER_SANITIZE_NUMBER_INT);
		$overtimeHours = filter_input(INPUT_POST, 'overtimeHours', FILTER_SANITIZE_NUMBER_INT);
		$overtimeMultiplier = filter_input(INPUT_POST, 'overtimeMultiplier', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$holidayMultiplier = filter_input(INPUT_POST, 'holidayMultiplier', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

		$SSRate = filter_input(INPUT_POST, 'SSRate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$SSBaseRate = filter_input(INPUT_POST, 'SSBaseRate', FILTER_SANITIZE_NUMBER_INT);
		$medicareRate = filter_input(INPUT_POST, 'medicareRate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$medicareBaseRate = filter_input(INPUT_POST, 'medicareBaseRate', FILTER_SANITIZE_NUMBER_INT);
		$medicareAddOn = filter_input(INPUT_POST, 'medicareAddOn', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$futaRate = filter_input(INPUT_POST, 'futaRate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$futaBaseRate = filter_input(INPUT_POST, 'futaBaseRate', FILTER_SANITIZE_NUMBER_INT);
		$currency = filter_input(INPUT_POST, 'currency', FILTER_SANITIZE_STRING);
		$f = filter_input(INPUT_POST, 'fiscalYear', FILTER_SANITIZE_NUMBER_INT);
		if ($f == 0 || $f == 1) {
			$fiscalYearNew = 0;
		} else {
			$fiscalYearNew = date2mktime($f, 'start');
		}
		$pwd1 = filter_input(INPUT_POST, 'pwd1', FILTER_SANITIZE_NUMBER_INT);
		$pwd2 = filter_input(INPUT_POST, 'pwd2', FILTER_SANITIZE_NUMBER_INT);

		if ($time < $fiscalYearNew) {
			$prevY = explode("-", $f);
			$fiscalYearNew = mktime(0, 0, 0, $prevY[1], $prevY[2], ($prevY[0] - 1));
		}

		if ($pwd1 === $pwd2 && $pwd1 != "" && $pwd1 != " ") {
			$getSalt = $db->prepare("SELECT salt FROM users WHERE id = ?");
			$getSalt->execute(array(
					$myId
			));
			$gsR = $getSalt->fetch();
			$salt = $gsR['salt'];

			$hidepwd = hash('sha512', ($salt . $pwd1), FALSE);

			$updatePwd = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
			$updatePwd->execute(array(
					$hidepwd,
					$myId
			));
		}

		$settingsUp = $db->prepare("UPDATE $mySettings SET name = ?, address1 = ?, address2 = ?, phone = ?, email = ?, currency = ?, EIN = ?, stateEIN = ?, startOfWorkWeek = ?, overtimeHours = ?, overtimeMultiplier = ?, holidayMultiplier = ?, SSRate = ?,SSBaseRate = ?,medicareRate = ?,medicareBaseRate = ?,medicareAddOn = ?,futaRate = ?,futaBaseRate = ? WHERE id = ?");
		$settingsUp->execute(array(
				$name,
				$address1,
				$address2,
				$phone,
				$email,
				$currency,
				$EIN,
				$stateEIN,
				$startOfWorkWeek,
				$overtimeHours,
				$overtimeMultiplier,
				$holidayMultiplier,
				$SSRate,
				$SSBaseRate,
				$medicareRate,
				$medicareBaseRate,
				$medicareAddOn,
				$futaRate,
				$futaBaseRate,
				'1'
		));

		if ($fiscalYearNew >= 1) {
			$settingsUp2 = $db->prepare("UPDATE $mySettings SET fiscalYear = ? WHERE id = ?");
			$settingsUp2->execute(array(
					$fiscalYearNew,
					'1'
			));
			$fiscalYear = $fiscalYearNew;
		}
	}
	if (filter_input(INPUT_POST, 'settingsUp', FILTER_SANITIZE_STRING) == 'accounts') {
		$aType = filter_input(INPUT_POST, 'accountType', FILTER_SANITIZE_NUMBER_INT);
		$aNum = filter_input(INPUT_POST, 'accountNumber', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$aName = filter_var(htmlEntities(trim($_POST['accountName']), ENT_QUOTES), FILTER_SANITIZE_STRING);

		switch ($aType) {
			case 7:
				$aType = 1;
				break;
			case 8:
				$aType = 1;
				break;
			case 9:
				$aType = 1;
				break;
			case 10:
				$aType = 2;
				break;
			case 11:
				$aType = 2;
				break;
			default:
				$aType = 1;
		}

		$insertB = $db->prepare("INSERT INTO $myFAccounts VALUES(NULL,?,?,?,'0.00','0',?,'0.00','0','0')");
		$insertB->execute(array(
				$aNum,
				$ACCOUNTTYPES[$aType],
				$aName,
				$time
		));
	}
	if (filter_input(INPUT_POST, 'settingsUp', FILTER_SANITIZE_STRING) == 'salesPurchasing') {
		$markUp = (filter_input(INPUT_POST, 'markUp', FILTER_SANITIZE_NUMBER_INT) / 100);
		$taxRate = (filter_input(INPUT_POST, 'taxRate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) / 100);
		$purchasingCostProcessing = filter_input(INPUT_POST, 'purchasingCostProcessing', FILTER_SANITIZE_NUMBER_INT);

		$settingsUp = $db->prepare("UPDATE $mySettings SET markUp = ?, taxRate = ?, purchasingCostProcessing = ? WHERE id = ?");
		$settingsUp->execute(array(
				$markUp,
				$taxRate,
				$purchasingCostProcessing,
				'1'
		));
	}
	if (filter_input(INPUT_POST, 'editA', FILTER_SANITIZE_NUMBER_INT) >= 1) {
		$editA = filter_input(INPUT_POST, 'editA', FILTER_SANITIZE_NUMBER_INT);
		$aName = filter_var(htmlEntities($_POST["accountName"], ENT_QUOTES), FILTER_SANITIZE_STRING);

		$updateB = $db->prepare("UPDATE $myFAccounts SET accountName = ? WHERE id = ?");
		$updateB->execute(array(
				$aName,
				$editA
		));
	}
	if (filter_input(INPUT_POST, 'delA', FILTER_SANITIZE_NUMBER_INT) >= 1) {
		$delA = filter_input(INPUT_POST, 'delA', FILTER_SANITIZE_NUMBER_INT);

		$updateB = $db->prepare("DELETE FROM $myFAccounts WHERE id = ?");
		$updateB->execute(array(
				$delA
		));
	}
	if (filter_input(INPUT_POST, 'createBackup', FILTER_SANITIZE_NUMBER_INT) == 1) {
		$folder = "cmPics/$myId/backups";
		if (! is_dir("$folder")) {
			mkdir("$folder", 0777, true);
		}
		$fileName = "manualBackup" . date("Y-m-d", $fiscalYear) . "to" . date("Y-m-d", $time) . ".csv";
		$myfile = fopen("$folder/$fileName", "w") or die("Unable to open file!");
		$txt = "id,date,contact,description,cashCheckCC,checkNumber,account,debitAmount,creditAmount,refNumber,typeCode,dailyConfirm\n";
		fwrite($myfile, $txt);
		$getL = $db->prepare("SELECT * FROM $myFLedger ORDER BY id");
		$getL->execute();
		while ($getLR = $getL->fetch()) {
			$id = $getLR['id'];
			$date = date("Y-m-d", $getLR['date']);
			$contact = str_replace(",", "", html_entity_decode(getContact($getLR['contact'], $myContacts), ENT_QUOTES));
			$description = str_replace(",", "", html_entity_decode($getLR['description'], ENT_QUOTES));
			$cashCheckCC = $PAYTYPES[$getLR['cashCheckCC']];
			$checkNumber = $getLR['checkNumber'];
			$accountNumber = $getLR['accountNumber'];
			$debitAmount = money_sfi($getLR['debitAmount'], $currency, $langCode);
			$creditAmount = money_sfi($getLR['creditAmount'], $currency, $langCode);
			$refNumber = $getLR['refNumber'];
			$typeCode = $TYPECODES[$getLR['typeCode']];
			$dailyConfirm = $getLR['dailyConfirm'];

			$txt = "$id,$date,$contact,$description,$cashCheckCC,$checkNumber,$accountNumber - $ACCOUNTS[$accountNumber],$debitAmount,$creditAmount,$refNumber,$typeCode,$dailyConfirm\n";
			fwrite($myfile, $txt);
		}
		$txt = ",,,,,,,,,,,\n";
		fwrite($myfile, $txt);
		$txt = "Totals,,,,,,,,,,,\n";
		fwrite($myfile, $txt);
		$txt = "Account Number,Account Name,Debit Amount,Credit Amount,Total,,,,,,,\n";
		fwrite($myfile, $txt);
		$arrayList = "";
		$t = 0;
		foreach ($ACCOUNTS as $k => $v) {
			$arrayList .= ($t != 0) ? "," : "";
			$arrayList .= "'D$k' => 0,'C$k' => 0,'T$k' => 0";
			$t ++;
		}
		$totals = array(
				$arrayList
		);
		$getL2 = $db->prepare("SELECT accountNumber,debitAmount,creditAmount FROM $myFLedger");
		$getL2->execute();
		while ($getL2R = $getL2->fetch()) {
			$accountNumber = $getL2R['accountNumber'];
			$debitAmount = money_sfi($getL2R['debitAmount'], $currency, $langCode);
			$creditAmount = money_sfi($getL2R['creditAmount'], $currency, $langCode);
			$totals["D$accountNumber"] += $debitAmount;
			$totals["C$accountNumber"] += $creditAmount;
			if (($accountNumber >= 100 && $accountNumber <= 199.9) || ($accountNumber >= 500 && $accountNumber <= 599.9)) {
				$totals["T$accountNumber"] += ($debitAmount - $creditAmount);
			} else {
				$totals["T$accountNumber"] += ($creditAmount - $debitAmount);
			}
		}
		foreach ($ACCOUNTS as $k => $v) {
			$txt = number_format($k, 1, '.', '') . "," . $v . "," . $totals["D$k"] . "," . $totals["C$k"] . "," . $totals["T$k"] . ",,,,,,,\n";
			fwrite($myfile, $txt);
		}
		fclose($myfile);
	}

	if (filter_input(INPUT_POST, 'bonusUp', FILTER_SANITIZE_NUMBER_INT) >= 1) {
		$empId = filter_input(INPUT_POST, 'bonusUp', FILTER_SANITIZE_NUMBER_INT);
		$d = explode("-", filter_input(INPUT_POST, 'bDate', FILTER_SANITIZE_NUMBER_INT));
		$empDate = mktime(0, 0, 0, $d[1], $d[2], $d[0]);
		$empAmt = filter_input(INPUT_POST, 'bAmount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$empDesc = filter_input(INPUT_POST, 'bDescription', FILTER_SANITIZE_STRING);

		$bUp = $db->prepare("INSERT INTO $myEmployeeTracking VALUES(NULL,?,?,?,?,?,?,?,?)");
		$bUp->execute(array(
				$empId,
				$empDate,
				'0.00',
				'0.00',
				$empAmt,
				$empDesc,
				'0',
				'0'
		));
	}
	if (filter_input(INPUT_POST, 'bonusDel', FILTER_SANITIZE_NUMBER_INT) >= 1) {
		$bDel = filter_input(INPUT_POST, 'bonusDel', FILTER_SANITIZE_NUMBER_INT);
		$bD = $db->prepare("DELETE FROM $myEmployeeTracking WHERE id = ?");
		$bD->execute(array(
				$bDel
		));
	}
	?>
<div style="float: left;">
	<?php
	if ($need2Close == 1) {
		?>
	<form id="frmClose" action="index.php?page=settings&r=close"
		method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer; background-color: #660000; color: #ffffff;"
			onclick="submitForm('Close')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Close Fiscal Year</td>
				</tr>
			</table>
		</div>
	</form>
	<?php
	}
	?>
	<form id="frmCompany" action="index.php?page=settings&r=company"
		method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('Company')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Company Settings</td>
				</tr>
			</table>
		</div>
	</form>
	<?php
	$oldCount = 0;
	if ($fiscalYear >= 1) {
		?>
	<form id="frmAccounts" action="index.php?page=settings&r=accounts"
		method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('Accounts')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Accounts Settings</td>
				</tr>
			</table>
		</div>
	</form>
	<form id="frmSalesPurchasing"
		action="index.php?page=settings&r=salesPurchasing" method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('SalesPurchasing')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Sales Purchasing</td>
				</tr>
			</table>
		</div>
	</form>
	<form id="frmPayroll" action="index.php?page=settings&r=payroll"
		method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('Payroll')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Payroll</td>
				</tr>
			</table>
		</div>
	</form>
	<form id="frmSalesAssociate"
		action="index.php?page=settings&r=salesAssociate" method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('SalesAssociate')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Employees</td>
				</tr>
			</table>
		</div>
	</form>
	<form id="frmMilage" action="index.php?page=settings&r=milage"
		method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('Milage')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Vehicles and Milage</td>
				</tr>
			</table>
		</div>
	</form>
	<form id="frmPrintInv" action="printInv.php" method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('PrintInv')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Print Inventory</td>
					<td style="text-align: left;"><?php
		echo showHelpRight(4);
		?></td>
				</tr>
			</table>
		</div>
	</form>
	<form id="frmBackupLedger"
		action="index.php?page=settings&r=backupLedger" method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('BackupLedger')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Save a copy of your ledger</td>
				</tr>
			</table>
		</div>
	</form>
	<?php
		if ($need2Close == 0) {
			$ucd = date("d", $fiscalYear);
			$ucm = date("m", $fiscalYear);
			$ucy = date("Y", $fiscalYear);
			$ucStart = mktime(0, 0, 0, $ucm, $ucd, ($ucy - 1));
			$ucEnd = mktime(0, 0, - 1, $ucm, $ucd, $ucy);
			$checkOld = $db->prepare("SELECT COUNT(*) FROM $myFLedgerOld WHERE date >= ? AND date <= ?");
			$checkOld->execute(array(
					$ucStart,
					$ucEnd
			));
			$co = $checkOld->fetch();
			$oldCount = $co[0];
			if ($oldCount >= 1) {
				?>
	<form id="frmUndoClose" action="index.php?page=settings&r=undoClose"
		method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('UndoClose')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Undo fiscal year closure</td>
				</tr>
			</table>
		</div>
	</form>
	<?php
			}
		}
	}
	?>
	<form id="frmContribute" action="index.php?page=settings&r=contribute"
		method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('Contribute')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Contribute to SFaI</td>
				</tr>
			</table>
		</div>
	</form>
</div>

<?php
	if ($r == 'payroll') {
		include "includes/payroll.php";
	}
	if ($r == 'undoClose') {
		if ($oldCount >= 1 && $need2Close == 0) {
			?><div style="text-align: center">
	This will undo your previous fiscal year closure, in case you need to
	go back and make any changes.<br> <br>
	<form action="index.php?page=settings" method="post">
		<input type="hidden" name="undoClose" value="1"><input type="submit"
			value=" Undo Close ">
	</form>
</div>
<?php
		} else {
			?><div style="text-align: center">There isn't a previous fiscal year
	to un-close.</div>
<?php
		}
	}
	if ($r == 'backupLedger') {
		?><div style="text-align: right;">
    	<?php
		echo showHelpLeft(5);
		?>
    </div>
<div style="font-weight: bold;">Create a backup of your ledger.</div>
<div style="">
	<form action="index.php?page=settings&r=backupLedger" method="post">
		<input type="hidden" name="createBackup" value="1"><input
			type="submit" value=" Create Backup">
	</form>
</div>
<div style="font-weight: bold; margin-top: 30px;">Current backups of
	your ledger.</div>
<div style="">
        <?php
		foreach (new DirectoryIterator("cmPics/$myId/backups") as $fileInfo) {
			if ($fileInfo->isDot())
				continue;
			$f = $fileInfo->getFilename();
			echo "<a href='cmPics/$myId/backups/$f'>$f</a><br />";
		}
		?>
        </div>
<?php
	}
	if ($r == 'close') {
		?><div style="text-align: right;">
    	<?php
		echo showHelpLeft(18);
		?>
    </div>
<form action='index.php?page=settings' method='post'>
	<table>
		<tr>
			<td
				style="text-align: left; font-weight: bold; font-size: 1.5em; background-color: #eeeeee;"
				colspan="7">If you have checked all of your numbers and they are
				correct, and you are ready to close the previous fiscal year.<br> <br>
				This action will backup your ledger into a storage file, making the
				individual transactions inaccessable. Set begining balances for all
				of your accounts for the current year.<br> <br> Create a spreadsheet
				report of all of your tranactions, including totals, which will be
				saved in your backups list.<br> <br> Sales and purchasing tickets
				will still be available to view, but not change. Inventory and
				recipes are not affected.<br> <br> You should print out your balance
				and income statements before closing. That information will be in
				your backup, but will become inaccessable through your balance and
				income statements.<br> <br>
        <?php
		$countS = $db->prepare("SELECT COUNT(*) FROM $mySales WHERE time < ? AND finalized = '0'");
		$countS->execute(array(
				$time2Close
		));
		$countSR = $countS->fetch();
		$cs = $countSR[0];
		$countP = $db->prepare("SELECT COUNT(*) FROM $myPurchasing WHERE time < ? AND finalized = '0'");
		$countP->execute(array(
				$time2Close
		));
		$countPR = $countP->fetch();
		$cp = $countPR[0];
		if (($cs + $cp) >= 1) {
			echo "<br><br>";
			if ($cs >= 1) {
				echo "There are sales tickets that need to be finalized before closing out the year:<br>";
				$sales2Finalize = $db->prepare("SELECT id, time, contactId FROM $mySales WHERE time < ? AND finalized = '0' ORDER BY time");
				$sales2Finalize->execute(array(
						$time2Close
				));
				while ($s2f = $sales2Finalize->fetch()) {
					$s2fId = $s2f['id'];
					$s2fTime = $s2f['time'];
					$s2fContactId = $s2f['contactId'];
					echo "<a href='index.php?page=sell&viewId=$s2fId'>" . date("Y-m-d", $s2fTime) . " " . getContact($s2fContactId, $myContacts) . "</a><br>";
				}
			}
			echo "<br><br>";
			if ($cp >= 1) {
				echo "There are purchasing tickets that need to be finalized before closing out the year:<br>";
				$pur2Finalize = $db->prepare("SELECT id, time, contactId FROM $myPurchasing WHERE time < ? AND finalized = '0' ORDER BY time");
				$pur2Finalize->execute(array(
						$time2Close
				));
				while ($p2f = $pur2Finalize->fetch()) {
					$p2fId = $p2f['id'];
					$p2fTime = $p2f['time'];
					$p2fContactId = $p2f['contactId'];
					echo "<a href='index.php?page=buy&viewId=$p2fId'>" . date("Y-m-d", $p2fTime) . " " . getContact($p2fContactId, $myContacts) . "</a><br>";
				}
			}
			echo "<br><br>";
		} else {
			?>
        <input type='submit' value=' Click Here '> <input type='hidden'
				name='close' value='1'> <input type='hidden' name='fy'
				value='<?php
			echo $time2Close;
			?>'>
		<?php
		}
		?>
        </td>
		</tr>
        <?php
		$gtIncome = 0;
		$LandCY = 0;
		$IandEY = 0;

		$j = "400.0";
		$k = "599.9";
		$getA5 = $db->prepare("SELECT debitAmount, creditAmount FROM $myFLedger WHERE accountNumber >= ? AND accountNumber <= ? AND date >= ? AND date < ?");
		$getA5->execute(array(
				$j,
				$k,
				$fiscalYear,
				$time2Close
		));
		while ($getAR5 = $getA5->fetch()) {
			$aDebit = $getAR5['debitAmount'];
			$aCredit = $getAR5['creditAmount'];

			$gtIncome += ($aCredit - $aDebit);
		}

		for ($i = 1; $i <= 5; ++ $i) {
			$j = $i . "00.0";
			$k = $i . "99.9";

			$type = $ACCOUNTTYPES[$i];

			$gtY = 0;
			?>
        <tr>
			<td
				style="text-align: left; font-weight: bold; font-size: 1.5em; background-color: #eeeeee;"
				colspan="7"><?php

			echo ucfirst($type);
			?></td>
		</tr>
		<tr>
			<td style="width: 100px; text-align: left; font-weight: bold;">Account
				#</td>
			<td style="width: 150px; text-align: left; font-weight: bold;">Account
				Name</td>
			<td style="width: 150px; text-align: left; font-weight: bold;">Description</td>
			<td style="width: 100px; text-align: right; font-weight: bold;">Start
				Balance</td>
			<td style="width: 100px; text-align: right; font-weight: bold;">Debits</td>
			<td style="width: 100px; text-align: right; font-weight: bold;">Credits</td>
			<td style="width: 150px; text-align: right; font-weight: bold;">Ending
				Balance</td>
		</tr>
        <?php
			$getA2 = $db->prepare("SELECT id, accountNumber, accountName, startBalance FROM $myFAccounts WHERE accountNumber >= ? AND accountNumber <= ? ORDER BY accountNumber");
			$getA2->execute(array(
					$j,
					$k
			));
			while ($getAR2 = $getA2->fetch()) {
				$start = $tY = 0;
				$aId = $getAR2['id'];
				$aNumber = $getAR2['accountNumber'];
				$aName = html_entity_decode($getAR2['accountName'], ENT_QUOTES);
				$start = $tY = $getAR2['startBalance'];

				$bd = 0;
				$bc = 0;

				if ($aNumber == '380.0') {
					$bd = 0;
					$bc = 0;
					$tY = $gtIncome;
				} else {
					$getA3 = $db->prepare("SELECT date, debitAmount, creditAmount FROM $myFLedger WHERE accountNumber = ? AND date >= ? AND date < ?");
					$getA3->execute(array(
							$aNumber,
							$fiscalYear,
							$time2Close
					));
					while ($getAR3 = $getA3->fetch()) {
						$aDate = $getAR3['date'];
						$aDebit = $getAR3['debitAmount'];
						$aCredit = $getAR3['creditAmount'];

						$bd += $aDebit;
						$bc += $aCredit;
						if ($i == 1) {
							$tY += ($aDebit - $aCredit);
						} else {
							$tY += ($aCredit - $aDebit);
						}
					}
				}
				$gtY += $tY;
				if ($i == 2 || $i == 3) {
					$LandCY += $tY;
				}
				if ($i == 4 || $i == 5) {
					$IandEY += $tY;
				}
				?>
            <tr style="cursor: pointer;"
			onclick="toggleview('show<?php
				echo $aId;
				?>')">
			<td style="text-align: left; border-top: 1px solid #dddddd;"><?php
				echo $aNumber;
				?></td>
			<td style="text-align: left; border-top: 1px solid #dddddd;"><?php
				echo $aName;
				?></td>
			<td style="text-align: left; border-top: 1px solid #dddddd;"></td>
			<td style="text-align: right; border-top: 1px solid #dddddd;"><?php
				echo money_sfi($start, $currency, $langCode);
				?></td>
			<td style="text-align: right; border-top: 1px solid #dddddd;"><?php
				echo ($bd >= 0.01) ? money_sfi($bd, $currency, $langCode) : "";
				?></td>
			<td style="text-align: right; border-top: 1px solid #dddddd;"><?php
				echo ($bc >= 0.01) ? money_sfi($bc, $currency, $langCode) : "";
				?></td>
			<td style="text-align: right; border-top: 1px solid #dddddd;"><?php
				echo money_sfi($tY, $currency, $langCode);
				?><input type='hidden' name='endB<?php
				echo $aId;
				?>'
				value='<?php
				echo money_sfi($tY, $currency, $langCode);
				?>'></td>
		</tr>
		<tr>
			<td colspan="7">
				<table style="display: none;" id="show<?php
				echo $aId;
				?>">
			<?php
				$getA3 = $db->prepare("SELECT date, contact, description, cashCheckCC, checkNumber, debitAmount, creditAmount, refNumber, typeCode FROM $myFLedger WHERE accountNumber = ? AND date >= ? AND date < ?");
				$getA3->execute(array(
						$aNumber,
						$fiscalYear,
						$time2Close
				));
				while ($getAR3 = $getA3->fetch()) {
					$date = $getAR3['date'];
					$contact = getContact($getAR3['contact'], $myContacts);
					$description = html_entity_decode($getAR3['description'], ENT_QUOTES);
					$ccc = $getAR3['cashCheckCC'];
					$checkNumber = $getAR3['checkNumber'];
					$debitAmount = $getAR3['debitAmount'];
					$creditAmount = $getAR3['creditAmount'];
					$refNumber = $getAR3['refNumber'];
					$typeCode = $getAR3['typeCode'];
					?>
				<tr>
						<td
							style="width: 100px; text-align: left; border-top: 1px solid #dddddd; background-color: #eeeeee;"><?php
					echo date("Y-m-d", $date);
					?></td>
						<td
							style="width: 150px; text-align: left; border-top: 1px solid #dddddd; background-color: #eeeeee;"><?php
					echo $contact;
					?></td>
						<td
							style="width: 150px; text-align: left; border-top: 1px solid #dddddd; background-color: #eeeeee;"><?php
					echo $description;
					?></td>
						<td
							style="width: 100px; text-align: right; border-top: 1px solid #dddddd; background-color: #eeeeee;"><?php
					switch ($ccc) {
						case 1:
							echo "Paid - Cash";
							break;
						case 2:
							echo "Paid Check #$checkNumber";
							break;
						case 3:
							echo "Paid - Card";
							break;
						default:
							echo "";
					}
					?></td>
						<td
							style="width: 100px; text-align: right; border-top: 1px solid #dddddd; background-color: #eeeeee;"><?php
					echo $debitAmount;
					?></td>
						<td
							style="width: 100px; text-align: right; border-top: 1px solid #dddddd; background-color: #eeeeee;"><?php
					echo $creditAmount;
					?></td>
						<td
							style="width: 150px; text-align: right; border-top: 1px solid #dddddd; background-color: #eeeeee;"><?php
					switch ($typeCode) {
						case 1:
							echo "<a href='index.php?page=sell&viewId=$refNumber' target='_self'>View Sale</a>";
							break;
						case 2:
							echo "<a href='index.php?page=buy&viewId=$refNumber' target='_self'>View Purchase</a>";
							break;
						case 3:
							echo "<a href='index.php?page=recipes&recipeId=$refNumber' target='_self'>View Recipe</a>";
							break;
						case 4:
							echo "<a href='index.php?page=inventory&grabInvId=$refNumber' target='_self'>View Inv</a>";
							break;
						case 5:
							echo "<a href='index.php?page=reports&r=journal&an=$refNumber' target='_self'>View Transaction</a>";
							break;
						case 6:
							echo "<a href='index.php?page=reports&r=general&an=$refNumber' target='_self'>View Transaction</a>";
							break;
					}
					?></td>
					</tr>
				<?php
				}
				?>
			</table>
			</td>
		</tr>
            <?php
			}
			?>
            <tr>
			<td style="text-align: left;"></td>
			<td style="text-align: left;"></td>
			<td style="text-align: left;"></td>
			<td
				style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;"><?php
			echo ucfirst($type);
			?></td>
			<td
				style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;">Total</td>
			<td
				style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;">></td>
			<td
				style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;"><?php
			echo money_sfi($gtY, $currency, $langCode);
			?></td>
		</tr>
            <?php
			if ($i == 3) {
				?>
            <tr>
			<td style="text-align: left;"></td>
			<td style="text-align: left;"></td>
			<td style="text-align: left;"></td>
			<td
				style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;">Liability
				+ Capital</td>
			<td
				style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;">Total</td>
			<td
				style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;"></td>
			<td
				style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;"><?php
				echo money_sfi($LandCY, $currency, $langCode);
				?></td>
		</tr>
            <?php
			}
			if ($i == 5) {
				?>
            <tr>
			<td style="text-align: left;"></td>
			<td style="text-align: left;"></td>
			<td style="text-align: left;"></td>
			<td
				style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;">Income
				- Expenses</td>
			<td
				style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;">Total</td>
			<td
				style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;"></td>
			<td
				style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;"><?php
				echo money_sfi($IandEY, $currency, $langCode);
				?><input type="hidden" name="retainedEarnings"
				value="<?php
				$getR = $db->prepare("SELECT startBalance FROM $myFAccounts WHERE accountNumber = ?");
				$getR->execute(array(
						'399.9'
				));
				$getRr = $getR->fetch();
				if ($getRr) {
					$add = $getRr['startBalance'];
					echo ($add + $IandEY);
				}
				?>"></td>
		</tr>

            <?php
			}
		}
		?>
        </table>
</form>
<?php
	}
	if ($r == 'company') {
		?><div style="text-align: right;">
    	<?php
		echo showHelpLeft(1);
		?>
    </div>
<form method='post' action='index.php?page=settings'>
	<table id="table1" cellspacing='5px'>
		<tr>
			<td colspan='2' style="font-weight: bold;">Company Information</td>
			<td colspan='2'></td>
		</tr>
		<tr>
			<td style='width: 100px;'></td>
			<td style='width: 200px; text-align: right;'>Name</td>
			<td style='width: 300px; text-align: left;'><input type="text"
				name="name" value="<?php
		echo $companyName;
		?>"></td>
			<td style='width: 100px;'></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Address</td>
			<td style='text-align: left;'><input type="text" name="address1"
				value="<?php
		echo $companyAddress1;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>City St Zip</td>
			<td style='text-align: left;'><input type="text" name="address2"
				value="<?php
		echo $companyAddress2;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Phone</td>
			<td style='text-align: left;'><input type="text" name="phone"
				value="<?php
		echo $companyPhone;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Email</td>
			<td style='text-align: left;'><input type="text" name="email"
				value="<?php
		echo $companyEmail;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Currency Symbol</td>
			<td style='text-align: left;'><select name="currency" size='1'>
		<?php
		foreach ($CURRENCIES as $v) {
			echo "<option value='$v[0]'";
			echo ($currency == $v[0]) ? " selected" : "";
			echo ">$v[0] $v[1]</option>\n";
		}
		?>
		</select></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Start of Fiscal Year</td>
	<?php
		if ($fiscalYear >= 1) {
			?>
                    <td style='text-align: left;'><?php

			echo date('F jS', $fiscalYear);
			?><input type="hidden" name="fiscalYear" value="1"></td>
                    <?php
		} else {
			?>
                    <td style='text-align: left;'><input type="date"
				name="fiscalYear"
				value="<?php
			echo date('Y', $time);
			?>-01-01"
				max="<?php
			echo date('Y-m-d', $time);
			?>"></td>
                    <?php
		}
		?>
                <td style=''></td>
		</tr>
		<tr>
			<td colspan='2' style="font-weight: bold;">Payroll Settings</td>
			<td colspan='2'></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Social Security tax rate</td>
			<td style='text-align: left;'><input type="number" name="SSRate"
				min='0.0000' step='0.0001' value="<?php
		echo $SSRate;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Social Security base rate</td>
			<td style='text-align: left;'><input type="number" name="SSBaseRate"
				min='0' step='1' value="<?php
		echo $SSBaseRate;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Medicare tax rate</td>
			<td style='text-align: left;'><input type="number" name="medicareRate"
				min='0.0000' step='0.0001' value="<?php
		echo $medicareRate;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Medicare add-on tax rate</td>
			<td style='text-align: left;'><input type="number" name="medicareAddOn"
				min='0.0000' step='0.0001' value="<?php
		echo $medicareAddOn;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Medicare base rate</td>
			<td style='text-align: left;'><input type="number" name="medicareBaseRate"
				min='0' step='1' value="<?php
		echo $medicareBaseRate;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Federal unemployment tax rate</td>
			<td style='text-align: left;'><input type="number" name="futaRate"
				min='0.0000' step='0.0001' value="<?php
		echo $futaRate;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Federal unemployment base rate</td>
			<td style='text-align: left;'><input type="number" name="futaBaseRate"
				min='0' step='1' value="<?php
		echo $futaBaseRate;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>EIN</td>
			<td style='text-align: left;'><input type="text" name="EIN"
				value="<?php
		echo $companyEIN;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>State EIN</td>
			<td style='text-align: left;'><input type="text" name="stateEIN"
				value="<?php
		echo $companyStateEIN;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Start of work week</td>
			<td style='text-align: left;'><select name="startOfWorkWeek" size='1'>
	<?php
		for ($i = 1; $i <= 7; ++ $i) {
			echo "<option value='$i'";
			echo ($i == $startOfWorkWeek) ? " selected" : "";
			echo ">" . $WEEKDAYS[$i] . "</option>\n";
		}
		?>
	</select></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Work week hours before Overtime</td>
			<td style='text-align: left;'><input type="number"
				name="overtimeHours" min='0' step='1'
				value="<?php
		echo $overtimeHours;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Overtime Pay Multiplier</td>
			<td style='text-align: left;'><input type="number"
				name="overtimeMultiplier" min='1.00' step='0.01'
				value="<?php
		echo $overtimeMultiplier;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Holiday Pay Multiplier</td>
			<td style='text-align: left;'><input type="number"
				name="holidayMultiplier" min='1.00' step='0.01'
				value="<?php
		echo $holidayMultiplier;
		?>"></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Default Time Zone</td>
			<td style='text-align: left;'><select name="timeZoneArea" size='1'
				onchange='getTimeZoneArea(this.value)'>
		<?php
		$myTimeZoneArea = explode("/", $timeZone);
		$area = $myTimeZoneArea[0];
		$city = (isset($myTimeZoneArea[2])) ? $myTimeZoneArea[1] . "/" . $myTimeZoneArea[2] : $myTimeZoneArea[1];
		$TimeZoneAreas = getTimeZoneAreas();
		foreach ($TimeZoneAreas as $v) {
			echo "<option value='$v'";
			echo ($area == $v) ? " selected" : "";
			echo ">$v</option>\n";
		}
		?>
		</select></td>
			<td style='text-align: left;' id='timeZoneCity'><?php
		echo "<select name='timeZoneCity' size='1'>";
		$TimeZoneCities = getTimeZoneCities($area);
		foreach ($TimeZoneCities as $v) {
			echo "<option value='$v'";
			echo ($city == $v) ? " selected" : "";
			echo ">$v</option>\n";
		}
		echo "</select>";
		?></td>
		</tr>
		<tr>
			<td colspan='2' style="font-weight: bold;">Reset your password</td>
			<td colspan='2'></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Enter new password - twice</td>
			<td style='text-align: left;'><input type="password" name="pwd1"
				value=""> <input type="password" name="pwd2" value=""></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'></td>
			<td style='text-align: left;'><input type="hidden" name="settingsUp"
				value="company"> <input type="submit" value=" Save Settings "></td>
			<td style=''></td>
		</tr>
	</table>
</form>
<?php
	} elseif ($r == 'accounts') {
		?><div style="text-align: right;">
    	<?php
		echo showHelpLeft(2);
		?>
    </div>
<table>
	<tr>
		<td
			style="text-align: left; font-weight: bold; font-size: 1.5em; background-color: #eeeeee;"
			colspan="6">New Account</td>
	</tr>
	<tr>
		<td style="text-align: left; font-weight: bold;">Account #</td>
		<td style="text-align: left; font-weight: bold;">Account Name</td>
		<td style="text-align: left; font-weight: bold;"></td>
		<td style="text-align: left; font-weight: bold;"></td>
	</tr>
	<tr>
		<td style="text-align: left;">
			<form action="index.php?page=settings&r=accounts" method="post">
				<select name='accountType' size='1'
					onchange='getAccSelect("accSelect", this.value, <?php
		echo $myId;
		?>)'>
					<option value='0'>Account Type</option>
					<option value='1'>Asset</option>
					<option value='6'>- Checking Account</option>
					<option value='7'>- Savings Account</option>
					<option value='8'>- Petty Cash</option>
					<option value='9'>- Funds Account</option>
					<option value='2'>Liability</option>
					<option value='10'>- Credit Card</option>
					<option value='11'>- Working Loan</option>
					<option value='3'>Capital</option>
					<option value='4'>Income</option>
					<option value='5'>Expense</option>
				</select><br /> <select id='accSelect' name='accountNumber' size='1'>
				</select>

		</td>
		<td style="text-align: left;"><input type="text" name="accountName"
			value="" placeholder='Account name'></td>
		<td style="text-align: left;"><input type="hidden" name="settingsUp"
			value="accounts"><input type="submit" value=" Add ">
			</form></td>
		<td style="text-align: left;"></td>
	</tr>
    <?php
		for ($i = 1; $i <= 5; ++ $i) {
			$j = $i . "00.0";
			$k = $i . "99.9";

			$type = $ACCOUNTTYPES[$i];
			?>
        <tr>
		<td
			style="text-align: left; font-weight: bold; font-size: 1.5em; background-color: #eeeeee;"
			colspan="6"><?php

			echo $type;
			?></td>
	</tr>
	<tr>
		<td style="text-align: left; font-weight: bold;">Account #</td>
		<td style="text-align: left; font-weight: bold;">Account Name</td>
		<td style="text-align: left; font-weight: bold;" colspan='2'></td>
	</tr>
        <?php
			$getA2 = $db->prepare("SELECT * FROM $myFAccounts WHERE accountNumber >= ? AND accountNumber <= ? ORDER BY accountNumber");
			$getA2->execute(array(
					$j,
					$k
			));
			while ($getAR2 = $getA2->fetch()) {
				$aId = $getAR2['id'];
				$aNumber = $getAR2['accountNumber'];
				$aName = $getAR2["accountName"];
				?>

            <tr>
		<td style="text-align: left;"><?php

				echo $aNumber;
				?></td>
		<td style="text-align: left;"><form
				action="index.php?page=settings&r=accounts" method="post">
				<input type="text" name="accountName"
					value="<?php

				echo html_entity_decode($aName, ENT_QUOTES);
				?>"></td>
		<td style="text-align: left;"><input type="hidden" name="editA"
			value="<?php
				echo $aId;
				?>"><input type="submit" value=" Update ">
			</form></td>
		<td style="text-align: left;">
                    <?php
				if (! in_array($aNumber, $SAVEACCOUNTS)) {
					$getO = $db->prepare("SELECT COUNT(*) FROM $myFLedger WHERE accountNumber = ?");
					$getO->execute(array(
							$aNumber
					));
					$getOR = $getO->fetch();
					$Lcount = $getOR[0];

					echo ($Lcount == 0) ? "<form action='index.php?page=settings&r=accounts' method='post'><input type='hidden' name='delA' value='$aId'><input type='submit' value=' Delete '></form>" : "";
				}
				?>
                </td>
	</tr>

            <?php
			}
		}
		?>
</table>
<?php
	} elseif ($r == 'salesPurchasing') {
		?><div style="text-align: right;">
    	<?php
		echo showHelpLeft(3);
		?>
    </div>
<form method='post' action='index.php?page=settings'>
	<table id="table1" cellspacing='5px'>
		<tr>
			<td colspan='2' style="font-weight: bold;">Sales Information</td>
			<td colspan='2'></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Mark Up</td>
			<td style='text-align: left;'><input type="number" name="markUp"
				value="<?php

		echo ($markUp * 100);
		?>" step='1'
				placeholder='0' onkeyup="markUpEx('markUpExample', this.value)">%<br />
				<div id="markUpExample" style="font-weight: bold;"></div></td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>Tax Rate</td>
			<td style='text-align: left;'><input type="number" name="taxRate"
				value="<?php

		echo ($taxRate * 100);
		?>" step='.01'
				placeholder='0.00'>%</td>
			<td style=''></td>
		</tr>
		<tr>
			<td colspan='2' style="font-weight: bold;">Purchasing Information</td>
			<td colspan='2'></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'>How to handle purchasing Fees</td>
			<td style='text-align: left;'>
				<div
					style="font-weight: bold; border: 1px solid #000000; width: 400px; padding: 10px;">
					<div style="float: left; margin: 5px 10px 10px 5px; padding: 10px;">
						<input type="radio" name="purchasingCostProcessing" value="0"
							<?php

		echo ($purchasingCostProcessing == 0) ? " checked" : "";
		?>>
					</div>
					From the purchasing receipt, any taxes, shipping, and fees are
					divided evenly among the purchased items based on value.
				</div>
				<div
					style="font-weight: bold; border: 1px solid #000000; width: 400px; padding: 10px;">
					<div style="float: left; margin: 5px 10px 10px 5px; padding: 10px;">
						<input type="radio" name="purchasingCostProcessing" value="1"
							<?php

		echo ($purchasingCostProcessing == 1) ? " checked" : "";
		?>>
					</div>
					From the purchasing receipt, any taxes, shipping and fees are kept
					separate from the cost of the items, so the cost value in your
					inventory does not include the amounts spent on taxes, shipping,
					and fees.
				</div>
			</td>
			<td style=''></td>
		</tr>
		<tr>
			<td style=''></td>
			<td style='text-align: right;'></td>
			<td style='text-align: left;'><input type="hidden" name="settingsUp"
				value="salesPurchasing"> <input type="submit"
				value=" Save Settings "></td>
			<td style=''></td>
		</tr>
	</table>
</form>
<?php
	} elseif ($r == 'salesAssociate') {
		?>
<table style="margin: 0px auto; width: 50%;" cellspacing='0px'>
	<tr>
		<td style="text-align: center; padding: 10px;" colspan='2'><span
			style='font-weight: bold; font-size: 2em;'>Employees</span></td>
	</tr>
	<tr>
		<td style="text-align: center; padding: 10px;" colspan='2'>Here you
			can manage employees.</td>
	</tr>
	<tr>
		<td style="text-align: right; padding: 10px; width: 50%;">Employees:</td>
		<td style="text-align: left; padding: 10px; width: 50%;"><select
			name="employee" size="1"
			onchange="getEmployeeEdit(this.value,'<?php
		echo $myId;
		?>')">
				<option value='0'>Add new</option>
        <?php
		$e = $db->prepare("SELECT id,name FROM $myEmployees ORDER BY name");
		$e->execute();
		while ($er = $e->fetch()) {
			echo "<option value='" . $er['id'] . "'>" . $er['name'] . "</option>\n";
		}
		?>
        </select></td>
	</tr>
	<tr>
		<td style="text-align: center; padding: 10px;" colspan='2'><a
			href='https://www.irs.gov/pub/irs-pdf/fw4.pdf' target='_blank'>Form
				W-4 at IRS.gov</a></td>
	</tr>
	<tr>
		<td style="text-align: center; padding: 10px;" colspan='2'><a
			href='https://www.irs.gov/pub/irs-pdf/p15.pdf' target='_blank'>Publication
				15</a></td>
	</tr>
	<tr>
		<td style="text-align: center; padding: 10px;" colspan='2'><a
			href='https://www.irs.gov/businesses/small-businesses-self-employed/employment-tax-forms'
			target='_blank'>Employment Tax Forms</a></td>
	</tr>
</table>
	<div id="employeeEdit" style="padding: 20px; text-align: right;">
	<form action="index.php?page=settings&r=salesAssociate" method="post">
		<table style="margin: 20px auto; width: 50%;" cellspacing='0px'>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="name">Name</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="name" type='text' name='name'></td>
			</tr>
			<tr>
				<td style="text-align: center; padding: 10px;" colspan='2'>Form W-4
					Information needed for payroll<br>Step 1
				</td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="address">Address</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="address" type='text' name='address'></td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="cityStZip">City, St Zip</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="cityStZip" type='text' name='cityStZip'></td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="ssn">SSN</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="ssn" type='number' min='0' max='999999999' step='1' name='ssn'></td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="w4_1c">Filing type</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="w4_1c" type='radio' name='w4_1c' value='1' checked> Single<br>
					<input id="w4_1c" type='radio' name='w4_1c' value='2'> Married
					filing jointly or Qualifying surviving spouse<br> <input id="w4_1c"
					type='radio' name='w4_1c' value='3'> Married filing separately<br>
					<input id="w4_1c" type='radio' name='w4_1c' value='4'> Head of
					household</td>
			</tr>
			<tr>
				<td style="text-align: center; padding: 10px;" colspan='2'>Step 2</td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="w4_2c">Working two jobs</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="w4_2c" type='checkbox' value='1' name='w4_2c'></td>
			</tr>
			<tr>
				<td style="text-align: center; padding: 10px;" colspan='2'>Step 3</td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="w4_3">Claim dependent and other credits</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="w4_3" type='number' min='0' step='1' name='w4_3'></td>
			</tr>
			<tr>
				<td style="text-align: center; padding: 10px;" colspan='2'>Step 4</td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="w4_4a">Other income (not from jobs)</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="w4_4a" type='number' min='0' step='1' name='w4_4a'></td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="w4_4b">Deductions</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="w4_4b" type='number' min='0' step='1' name='w4_4b'></td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="w4_4c">Extra tax withholding</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="w4_4c" type='number' min='0' step='1' name='w4_4c'></td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="hireDate">Hire Date</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="hireDate" type='date' name='hireDate'
					value='<?php
		echo date('Y-m-d', $time);
		?>'></td>
			</tr>
			<tr>
				<td style="text-align: center; padding: 10px;" colspan='2'>End of
					W-4 Information</td>
			</tr>
			<tr>
		<td style="text-align: right; padding: 10px; width: 50%;"><label
			for="eHealthIns">Employee: Health Ins witholding (per pay period)</label></td>
		<td style="text-align: left; padding: 10px; width: 50%;"><input
			id="eHealthIns" type='number' min='0.00' step='0.01'
			name='eHealthIns' value='0.00'></td>
	</tr>
	<tr>
		<td style="text-align: right; padding: 10px; width: 50%;"><label
			for="cHealthIns">Company: Health Ins witholding (per pay period)</label></td>
		<td style="text-align: left; padding: 10px; width: 50%;"><input
			id="cHealthIns" type='number' min='0.00' step='0.01'
			name='cHealthIns' value='0.00'></td>
	</tr>
	<tr>
		<td style="text-align: right; padding: 10px; width: 50%;"><label
			for="eRetirement401k">Employee: Retirement / 401k (per pay period)</label></td>
		<td style="text-align: left; padding: 10px; width: 50%;"><input
			id="eRetirement401k" type='number' min='0.00' step='0.01'
			name='eRetirement401k' value='0.00'></td>
	</tr>
	<tr>
		<td style="text-align: right; padding: 10px; width: 50%;"><label
			for="cRetirement401k">Company: Retirement / 401k (per pay period)</label></td>
		<td style="text-align: left; padding: 10px; width: 50%;"><input
			id="cRetirement401k" type='number' min='0.00' step='0.01'
			name='cRetirement401k' value='0.00'></td>
	</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="garnishment">Garnishment (per pay period)</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="garnishment" type='number' min='0.00' step='0.01'
					name='garnishment' value="0.00"></td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="otherWitholding">Other witholding (per pay period)</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="otherWitholding" type='number' min='0.00' step='0.01'
					name='otherWitholding' value="0.00"></td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="terminateDate">Termination Date</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="terminateDate" type='date' name='terminateDate' value='0-0-0'></td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="email">Email</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="email" type='email' name='email'></td>
			</tr>

			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="phone">Phone Number</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="phone" type='number' min='0' max='9999999999' step='1'
					name='phone'></td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="hourlyPayRate">Hourly Pay Rate</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="hourlyPayRate" type='number' min='0.00' step='0.01'
					name='hourlyPayRate' value="0.00"></td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="salaryPayRate">Salary Pay Rate</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="salaryPayRate" type='number' min='0.00' step='0.01'
					name='salaryPayRate' value="0.00"></td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="payRateDate">Pay Rate Effective Date</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="payRateDate" type='date' name='payRateDate'
					value='<?php
		echo date('Y-m-d', $time);
		?>'></td>
			</tr>
			<tr>
				<td style="text-align: right; padding: 10px; width: 50%;"><label
					for="description">Description of pay rate change</label></td>
				<td style="text-align: left; padding: 10px; width: 50%;"><input
					id="description" type='text' name='description' value=''></td>
			</tr>
			<tr>
				<td style="text-align: center; padding: 10px;" colspan='2'><input
					type='hidden' name='employeeUp' value='new'>
					<button>Add Employee</button></td>
			</tr>
		</table>
		</form>
	</div>
<?php
	} elseif ($r == 'milage') {
		?>
<table style="margin: 0px auto; width: 50%;" cellspacing='0px'>
	<tr>
		<td style="text-align: center; padding: 10px;" colspan='6'><span
			style='font-weight: bold; font-size: 2em;'>Vehicles and Milage</span></td>
	</tr>
	<tr>
		<td style="text-align: center; padding: 10px;" colspan='6'>Here you
			can add vehicles and track milage.</td>
	</tr>
	<tr>
		<td style="text-align: center; padding: 10px;" colspan='6'><?php
		echo $Merror;
		?></td>
	</tr>
	<tr>
		<td style="text-align: center; padding: 10px;" colspan='6'>Check this
			box to track milage for your vehicles. <input type='checkbox'
			name='useMilage' value='1'
			<?php
		echo ($useMilage == 1) ? " checked" : "";
		?>
			onclick="toggleMilage(<?php
		echo $myId;
		?>)">
		</td>
	</tr>
	<tr>
		<td style="text-align: center; padding: 10px;">Vehicle Name</td>
		<td style="text-align: center; padding: 10px;">Lic Plate</td>
		<td style="text-align: center; padding: 10px;">Vin</td>
		<td style="text-align: center; padding: 10px;">Assigned To</td>
		<td style="text-align: center; padding: 10px;">Retired</td>
		<td style="text-align: center; padding: 10px;"></td>
	</tr>
	<tr>
		<td style="text-align: center; padding: 10px;"><form
				action="index.php?page=settings&r=milage" method="post">
				<input type='text' name='name'></td>
		<td style="text-align: center; padding: 10px;"><input type='text'
			name='licensePlate'></td>
		<td style="text-align: center; padding: 10px;"><input type='text'
			name='vin'></td>
		<td style="text-align: center; padding: 10px;"><select
			name="assignedTo" size="1">
        <?php
		$e = $db->prepare("SELECT id,name FROM $myEmployees ORDER BY name");
		$e->execute();
		while ($er = $e->fetch()) {
			echo "<option value='" . $er['id'] . "'>" . $er['name'] . "</option>\n";
		}
		?>
        </select></td>
		<td style="text-align: center; padding: 10px;"></td>
		<td style="text-align: center; padding: 10px;"><input type='hidden'
			name='vehicleUp' value='new'>
			<button>Add new vehicle</button>
			</form></td>
	</tr>
        <?php
		$getV = $db->prepare("SELECT * FROM $myVehicles ORDER BY name");
		$getV->execute();
		while ($getVR = $getV->fetch()) {
			echo "<tr>";
			echo "<td style='text-align:center; padding:10px;'><form action='index.php?page=settings&r=milage' method='post'><input type='text' name='name' value='" . $getVR['name'] . "'></td>";
			echo "<td style='text-align:center; padding:10px;'><input type='text' name='licensePlate' value='" . $getVR['licensePlate'] . "'></td>";
			echo "<td style='text-align:center; padding:10px;'><input type='text' name='vin' value='" . $getVR['vin'] . "'></td>";
			echo "<td style='text-align:center; padding:10px;'><select name='assignedTo' size='1'>";
			$e = $db->prepare("SELECT id,name FROM $myEmployees ORDER BY name");
			$e->execute();
			while ($er = $e->fetch()) {
				echo "<option value='" . $er['id'] . "'";
				echo ($er['id'] == $getVR['assignedTo']) ? " selected" : "";
				echo ">" . $er['name'] . "</option>\n";
			}
			echo "</select></td>";
			echo "<td style='text-align:center; padding:10px;'><input type='checkbox' name='retired' value='1'";
			echo ($getVR['retired'] == 1) ? " checked" : "";
			echo "></td>";
			echo "<td style='text-align:center; padding:10px;'><input type='hidden' name='vehicleUp' value='" . $getVR['id'] . "'><button>Update vehicle</button></form></td>";
			echo "</tr>";
		}
		?>
        </table>
<?php
	} elseif ($r == 'contribute') {
		?>
<div style="text-align: center;">
	<span style='font-weight: bold;'>Thank you</span><br /> <br /> We hope
	SFaI is helping you manage your finances and inventory. And through
	your contribution to this site we can continue to make it a free
	service for those that need it.<br> <br>
	<form action="https://www.paypal.com/donate" method="post"
		target="_top">
		<input type="hidden" name="hosted_button_id" value="ARFLYDEMWR9QC" />
		<input type="image"
			src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif"
			border="0" name="submit"
			title="PayPal - The safer, easier way to pay online!"
			alt="Donate with PayPal button" /> <img alt="" border="0"
			src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1"
			height="1" />
	</form>
</div>
<?php
	} else {
		echo "";
	}
} else {
	echo "Please log in to change your settings.";
}