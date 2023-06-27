<?php
if (filter_input(INPUT_POST, 'processPay', FILTER_SANITIZE_NUMBER_INT) >= 1) {
	$pId = filter_input(INPUT_POST, 'processPay', FILTER_SANITIZE_NUMBER_INT);
	$pName = filter_input(INPUT_POST, 'empName', FILTER_SANITIZE_STRING);
	$pEnd = filter_input(INPUT_POST, 'ppEnd', FILTER_SANITIZE_NUMBER_INT);
	$desc = date("Y-m-d", $pEnd) . " $pName payroll ";
	$pfedTax = filter_input(INPUT_POST, 'fedTax', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$pstateTax = filter_input(INPUT_POST, 'stateTax', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$pNetPay = filter_input(INPUT_POST, 'netPay', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) - $pstateTax;
	$pSSe = filter_input(INPUT_POST, 'SSe', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$pSSc = filter_input(INPUT_POST, 'SSc', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$pMe = filter_input(INPUT_POST, 'Me', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$pMc = filter_input(INPUT_POST, 'Mc', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$pFICAe = ($pSSe + $pMe);
	$pFICAc = ($pSSc + $pMc);
	$pFc = filter_input(INPUT_POST, 'Fc', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$pHealthInse = filter_input(INPUT_POST, 'HealthInsEmployee', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$pHealthInsc = filter_input(INPUT_POST, 'HealthInsCompany', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$pRetirement401ke = filter_input(INPUT_POST, 'Retirement401kEmployee', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$pRetirement401kc = filter_input(INPUT_POST, 'Retirement401kCompany', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$pTaxWitholding = filter_input(INPUT_POST, 'TaxWitholding', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$pGarnishment = filter_input(INPUT_POST, 'Garnishment', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$pOtherWitholding = filter_input(INPUT_POST, 'OtherWitholding', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$pFromAcc = filter_input(INPUT_POST, 'fromAcc', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$pCheckNum = filter_input(INPUT_POST, 'checkNum', FILTER_SANITIZE_NUMBER_INT);

	$payHistory = $db->prepare("INSERT INTO $myEmployeePayrollHistory VALUES(NULL,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
	$payHistory->execute(array(
			$pId,
			$pEnd,
			$desc,
			$pNetPay,
			$pfedTax,
			$pstateTax,
			$pFICAe,
			$pFICAc,
			$pFc,
			$pHealthInse,
			$pHealthInsc,
			$pRetirement401ke,
			$pRetirement401kc,
			$pTaxWitholding,
			$pGarnishment,
			$pOtherWitholding,
			$pCheckNum,
			'0',
			'0',
			'0',
			'0'
	));
	$phId = $db->prepare("SELECT id FROM $myEmployeePayrollHistory WHERE employeeId = ? ORDER BY id DESC LIMIT 1");
	$phId->execute(array(
			$pId
	));
	$phIdr = $phId->fetch();
	$historyId = ($phIdr) ? $phIdr['id'] : 0;

	// ***** Employee side processing *****

	if ($pNetPay >= 0.01) {
		// id, date, contact, description, cashCheckCC, checkNumber,
		// accountNumber, debitAmount, creditAmount, refNumber, typeCode
		$upLedger1 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0.00','0')");
		$upLedger1->execute(array(
				$time,
				'1',
				$desc . 'Net Pay',
				'1',
				$pCheckNum,
				$pFromAcc,
				$pNetPay,
				$historyId,
				'8'
		));
		$upLedger2 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0.00','0')");
		$upLedger2->execute(array(
				$time,
				'1',
				$desc . 'Net Pay',
				'1',
				$pCheckNum,
				'550.0',
				$pNetPay,
				$historyId,
				'8'
		));
	}
	if ($pfedTax >= 0.01) {
		// id, date, contact, description, cashCheckCC, checkNumber,
		// accountNumber, debitAmount, creditAmount, refNumber, typeCode
		$upLedger1 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0.00','0')");
		$upLedger1->execute(array(
				$time,
				'1',
				$desc . 'Fed tax',
				'0',
				'0',
				'250.0',
				$pfedTax,
				$historyId,
				'8'
		));
		$upLedger2 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0.00','0')");
		$upLedger2->execute(array(
				$time,
				'1',
				$desc . 'Fed tax',
				'0',
				'0',
				'550.0',
				$pfedTax,
				$historyId,
				'8'
		));
	}
	if ($pstateTax >= 0.01) {
		// id, date, contact, description, cashCheckCC, checkNumber,
		// accountNumber, debitAmount, creditAmount, refNumber, typeCode
		$upLedger1 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0.00','0')");
		$upLedger1->execute(array(
				$time,
				'1',
				$desc . 'State tax',
				'0',
				'0',
				'250.8',
				$pstateTax,
				$historyId,
				'8'
		));
		$upLedger2 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0.00','0')");
		$upLedger2->execute(array(
				$time,
				'1',
				$desc . 'State tax',
				'0',
				'0',
				'550.0',
				$pstateTax,
				$historyId,
				'8'
		));
	}
	if ($pFICAe >= 0.01) {
		// id, date, contact, description, cashCheckCC, checkNumber,
		// accountNumber, debitAmount, creditAmount, refNumber, typeCode
		$upLedger1 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0.00','0')");
		$upLedger1->execute(array(
				$time,
				'1',
				$desc . 'Emp FICA',
				'0',
				'0',
				'250.1',
				$pFICAe,
				$historyId,
				'8'
		));
		$upLedger2 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0.00','0')");
		$upLedger2->execute(array(
				$time,
				'1',
				$desc . 'Emp FICA',
				'0',
				'0',
				'550.0',
				$pFICAe,
				$historyId,
				'8'
		));
	}
	if ($pHealthInse >= 0.01) {
		// id, date, contact, description, cashCheckCC, checkNumber,
		// accountNumber, debitAmount, creditAmount, refNumber, typeCode
		$upLedger1 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0.00','0')");
		$upLedger1->execute(array(
				$time,
				'1',
				$desc . 'Emp Health Ins',
				'0',
				'0',
				'250.3',
				$pHealthInse,
				$historyId,
				'8'
		));
		$upLedger2 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0.00','0')");
		$upLedger2->execute(array(
				$time,
				'1',
				$desc . 'Emp Health Ins',
				'0',
				'0',
				'550.0',
				$pHealthInse,
				$historyId,
				'8'
		));
	}
	if ($pRetirement401ke >= 0.01) {
		// id, date, contact, description, cashCheckCC, checkNumber,
		// accountNumber, debitAmount, creditAmount, refNumber, typeCode
		$upLedger1 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0.00','0')");
		$upLedger1->execute(array(
				$time,
				'1',
				$desc . 'Emp Retirement',
				'0',
				'0',
				'250.4',
				$pRetirement401ke,
				$historyId,
				'8'
		));
		$upLedger2 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0.00','0')");
		$upLedger2->execute(array(
				$time,
				'1',
				$desc . 'Emp Retirement',
				'0',
				'0',
				'550.0',
				$pRetirement401ke,
				$historyId,
				'8'
		));
	}
	if ($pTaxWitholding >= 0.01) {
		// id, date, contact, description, cashCheckCC, checkNumber,
		// accountNumber, debitAmount, creditAmount, refNumber, typeCode
		$upLedger1 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0.00','0')");
		$upLedger1->execute(array(
				$time,
				'1',
				$desc . 'Emp Extra Tax',
				'0',
				'0',
				'250.0',
				$pTaxWitholding,
				$historyId,
				'8'
		));
		$upLedger2 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0.00','0')");
		$upLedger2->execute(array(
				$time,
				'1',
				$desc . 'Emp Extra Tax',
				'0',
				'0',
				'550.0',
				$pTaxWitholding,
				$historyId,
				'8'
		));
	}
	if ($pGarnishment >= 0.01) {
		// id, date, contact, description, cashCheckCC, checkNumber,
		// accountNumber, debitAmount, creditAmount, refNumber, typeCode
		$upLedger1 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0.00','0')");
		$upLedger1->execute(array(
				$time,
				'1',
				$desc . 'Emp Garnishment',
				'0',
				'0',
				'250.6',
				$pGarnishment,
				$historyId,
				'8'
		));
		$upLedger2 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0.00','0')");
		$upLedger2->execute(array(
				$time,
				'1',
				$desc . 'Emp Garnishment',
				'0',
				'0',
				'550.0',
				$pGarnishment,
				$historyId,
				'8'
		));
	}
	if ($pOtherWitholding >= 0.01) {
		// id, date, contact, description, cashCheckCC, checkNumber,
		// accountNumber, debitAmount, creditAmount, refNumber, typeCode
		$upLedger1 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0.00','0')");
		$upLedger1->execute(array(
				$time,
				'1',
				$desc . 'Emp Other Witholding',
				'0',
				'0',
				'250.7',
				$pOtherWitholding,
				$historyId,
				'8'
		));
		$upLedger2 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0.00','0')");
		$upLedger2->execute(array(
				$time,
				'1',
				$desc . 'Emp Other Witholding',
				'0',
				'0',
				'550.0',
				$pOtherWitholding,
				$historyId,
				'8'
		));
	}

	// ***** Employer Side Processing *****

	if ($pFICAc >= 0.01) {
		// id, date, contact, description, cashCheckCC, checkNumber,
		// accountNumber, debitAmount, creditAmount, refNumber, typeCode
		$upLedger1 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0.00','0')");
		$upLedger1->execute(array(
				$time,
				'1',
				$desc . 'Company FICA',
				'0',
				'0',
				'250.1',
				$pFICAc,
				$historyId,
				'8'
		));
		$upLedger2 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0.00','0')");
		$upLedger2->execute(array(
				$time,
				'1',
				$desc . 'Company FICA',
				'0',
				'0',
				'550.2',
				$pFICAc,
				$historyId,
				'8'
		));
	}
	if ($pFc >= 0.01) {
		// id, date, contact, description, cashCheckCC, checkNumber,
		// accountNumber, debitAmount, creditAmount, refNumber, typeCode
		$upLedger1 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0.00','0')");
		$upLedger1->execute(array(
				$time,
				'1',
				$desc . 'Company FUTA',
				'0',
				'0',
				'250.2',
				$pFc,
				$historyId,
				'8'
		));
		$upLedger2 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0.00','0')");
		$upLedger2->execute(array(
				$time,
				'1',
				$desc . 'Company FUTA',
				'0',
				'0',
				'550.3',
				$pFc,
				$historyId,
				'8'
		));
	}
	if ($pHealthInsc >= 0.01) {
		// id, date, contact, description, cashCheckCC, checkNumber,
		// accountNumber, debitAmount, creditAmount, refNumber, typeCode
		$upLedger1 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0.00','0')");
		$upLedger1->execute(array(
				$time,
				'1',
				$desc . 'Company Health Ins',
				'0',
				'0',
				'250.3',
				$pHealthInsc,
				$historyId,
				'8'
		));
		$upLedger2 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0.00','0')");
		$upLedger2->execute(array(
				$time,
				'1',
				$desc . 'Company Health Ins',
				'0',
				'0',
				'550.4',
				$pHealthInsc,
				$historyId,
				'8'
		));
	}
	if ($pRetirement401kc >= 0.01) {
		// id, date, contact, description, cashCheckCC, checkNumber,
		// accountNumber, debitAmount, creditAmount, refNumber, typeCode
		$upLedger1 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0.00','0')");
		$upLedger1->execute(array(
				$time,
				'1',
				$desc . 'Company Retirement',
				'0',
				'0',
				'250.4',
				$pRetirement401kc,
				$historyId,
				'8'
		));
		$upLedger2 = $db->prepare("INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0.00','0')");
		$upLedger2->execute(array(
				$time,
				'1',
				$desc . 'Company Retirement',
				'0',
				'0',
				'550.5',
				$pRetirement401kc,
				$historyId,
				'8'
		));
	}
	$setPaid1 = $db->prepare("UPDATE $myEmployeeTracking SET otherPayPaid = '1' WHERE otherPay > '0.00' AND employeeId = ? AND date <= ?");
	$setPaid1->execute(array(
			$pId,
			$pEnd
	));

	$setPaid2 = $db->prepare("UPDATE $myTimeClock SET paid = '1' WHERE employeeId = ? AND clockIn <= ?");
	$setPaid2->execute(array(
			$pId,
			$pEnd
	));
}