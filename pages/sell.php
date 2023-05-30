<div class="heading">Sales</div>
<?php
if ($myId >= 1) {
	if (filter_input ( INPUT_POST, 'dateRangeStart', FILTER_SANITIZE_NUMBER_INT )) {
		$_SESSION ['dateRangeStart'] = date2mktime ( filter_input ( INPUT_POST, 'dateRangeStart', FILTER_SANITIZE_NUMBER_INT ), 'start' );
	}
	$dateRangeStart = (isset ( $_SESSION ['dateRangeStart'] )) ? $_SESSION ['dateRangeStart'] : date2mktime ( date ( "Y-m-d", ($time - 604800) ), 'start' );

	if (filter_input ( INPUT_POST, 'dateRangeEnd', FILTER_SANITIZE_NUMBER_INT )) {
		$_SESSION ['dateRangeEnd'] = date2mktime ( filter_input ( INPUT_POST, 'dateRangeEnd', FILTER_SANITIZE_NUMBER_INT ), 'end' );
	}
	$dateRangeEnd = (isset ( $_SESSION ['dateRangeEnd'] )) ? $_SESSION ['dateRangeEnd'] : date2mktime ( date ( "Y-m-d", $time ), 'end' );

	$showBox1 = "block";
	$showBox2 = "none";
	$link1Style = "cursor:pointer; font-weight:bold; text-decoration:none;";
	$link2Style = "cursor:pointer; font-weight:normal; text-decoration:underline;";
	$sellId = 0;

	if (filter_input ( INPUT_POST, 'getSellId', FILTER_SANITIZE_NUMBER_INT )) {
		$showBox1 = "none";
		$showBox2 = "block";
		$link1Style = "cursor:pointer; font-weight:normal; text-decoration:underline;";
		$link2Style = "cursor:pointer; font-weight:bold; text-decoration:none;";
		$sellId = filter_input ( INPUT_POST, 'getSellId', FILTER_SANITIZE_NUMBER_INT );
	}

	$viewId = 0;
	if (filter_input ( INPUT_POST, 'viewId', FILTER_SANITIZE_NUMBER_INT )) {
		$viewId = filter_input ( INPUT_POST, 'viewId', FILTER_SANITIZE_NUMBER_INT );
	}
	if (filter_input ( INPUT_GET, 'viewId', FILTER_SANITIZE_NUMBER_INT )) {
		$viewId = filter_input ( INPUT_GET, 'viewId', FILTER_SANITIZE_NUMBER_INT );
	}

	if (filter_input ( INPUT_POST, 'sellId', FILTER_SANITIZE_STRING )) {
		$upId = filter_input ( INPUT_POST, 'sellId', FILTER_SANITIZE_NUMBER_INT );
		$upName = filter_var ( htmlEntities ( trim ( $_POST ['contactName'] ), ENT_QUOTES ), FILTER_SANITIZE_STRING );
		$upNameSelect = filter_input ( INPUT_POST, 'contactNameSelect', FILTER_SANITIZE_NUMBER_INT );
		$upDate = date2mktime ( filter_input ( INPUT_POST, 'date', FILTER_SANITIZE_NUMBER_INT ), 'noon' );
		$upShipping = filter_input ( INPUT_POST, 'shipping', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
		$upFees = filter_input ( INPUT_POST, 'fees', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
		$upDiscountAmount = filter_input ( INPUT_POST, 'discountAmount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
		$upDone = (filter_input ( INPUT_POST, 'done', FILTER_SANITIZE_NUMBER_INT ) == 1) ? 1 : 0;
		$taxExempt = (filter_input ( INPUT_POST, 'taxExempt', FILTER_SANITIZE_STRING ) == "Y") ? 0 : 1;
		settype ( $taxExempt, "int" );
		$upTaxes = (filter_input ( INPUT_POST, 'taxes', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) * $taxExempt);
		settype ( $upShipping, "float" );
		settype ( $upFees, "float" );
		settype ( $upTaxes, "float" );
		settype ( $upDiscountAmount, "float" );
		$paid = (filter_input ( INPUT_POST, 'paid', FILTER_SANITIZE_NUMBER_INT ) >= 1) ? 1 : 0;
		$ccc = (filter_input ( INPUT_POST, 'paid', FILTER_SANITIZE_NUMBER_INT ) >= 1) ? filter_input ( INPUT_POST, 'paid', FILTER_SANITIZE_NUMBER_INT ) : 0;
		$ckNum = (filter_input ( INPUT_POST, 'ckNum', FILTER_SANITIZE_NUMBER_INT ));
		$notes = filter_var ( htmlEntities ( trim ( $_POST ['notes'] ), ENT_QUOTES ), FILTER_SANITIZE_STRING );
		$upDiscountPercent = filter_input ( INPUT_POST, 'discountPercent', FILTER_SANITIZE_NUMBER_INT );
		$delSale = (filter_input ( INPUT_POST, 'delSale', FILTER_SANITIZE_NUMBER_INT ) == 1) ? $upId : "N";

		if ($delSale == $upId) {
			$delS = $db->prepare ( "DELETE FROM $mySales WHERE id = ?" );
			$delS->execute ( array (
					$upId
			) );
		} else {
			$contactId = ($upNameSelect == 0) ? conCheck ( $upName, $db, $myContacts, $time, '0' ) : $upNameSelect;

			$updateTax = $db->prepare ( "UPDATE $myContacts SET chargeTax = ? WHERE id = ?" );
			$updateTax->execute ( array (
					$taxExempt,
					$contactId
			) );

			$upItems = array ();
			foreach ( $_POST as $key => $val ) {
				if (preg_match ( "/^invQty([0-9][0-9]*)$/", $key, $match )) {
					$upItems [$match [1]] [0] = filter_var ( $val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
				}
				if (preg_match ( "/^invName([0-9][0-9]*)$/", $key, $match )) {
					$upItems [$match [1]] [1] = filter_var ( htmlEntities ( trim ( $val ), ENT_QUOTES ), FILTER_SANITIZE_STRING );
				}
				if (preg_match ( "/^invCost([0-9][0-9]*)$/", $key, $match )) {
					$upItems [$match [1]] [2] = filter_var ( $val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
				}
				if (preg_match ( "/^invPrice([0-9][0-9]*)$/", $key, $match )) {
					$upItems [$match [1]] [3] = filter_var ( $val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
				}
				if (preg_match ( "/^invNameSelect([0-9][0-9]*)$/", $key, $match )) {
					$upItems [$match [1]] [4] = filter_var ( $val, FILTER_SANITIZE_NUMBER_INT );
				}
				if (preg_match ( "/^invUOM([0-9][0-9]*)$/", $key, $match )) {
					$upItems [$match [1]] [5] = filter_var ( $val, FILTER_SANITIZE_NUMBER_INT );
				}
			}

			$upItemsString = "";

			foreach ( $upItems as $v1 ) {
				settype ( $v1 [0], "float" );
				settype ( $v1 [2], "float" );
				settype ( $v1 [3], "float" );
				settype ( $v1 [4], "int" );
				settype ( $v1 [5], "int" );
				if ($v1 [0] >= 0.01) {
					$v1 [1] = ($v1 [4] == 0) ? invCheck ( $v1 [1], $v1 [5], $db, $myInventory, $time ) : $v1 [4];

					for($j = 0; $j < 4; ++ $j) {
						$upItemsString .= $v1 [$j];
						$upItemsString .= ($j != 3) ? "," : "";
					}
					$upItemsString .= ($j == 3) ? "" : ";";
				}
				$checkPrice = $db->prepare ( "SELECT price FROM $myInventory WHERE id = ?" );
				$checkPrice->execute ( array (
						$v1 [1]
				) );
				$cp = $checkPrice->fetch ();
				if ($cp) {
					$p = $cp ['price'];
					if ($p == 0.00) {
						$setPrice = $db->prepare ( "UPDATE $myInventory SET price = ? WHERE id = ?" );
						$setPrice->execute ( array (
								$v1 [3],
								$v1 [1]
						) );
					}
				}
			}
			if ($upItemsString != "") {
				$a = str_split ( $upItemsString );
				array_pop ( $a );
				$upItemsS = implode ( "", $a );
			} else {
				$upItemsS = "";
			}

			if ($upId == 0) {
				$upNew = $db->prepare ( "INSERT INTO $mySales VALUES(NULL,?,?,?,?,?,?,'0',?,?,?,?,?,'0')" );
				$upNew->execute ( array (
						$upDate,
						$contactId,
						$upItemsS,
						$upTaxes,
						$upShipping,
						$upFees,
						$paid,
						$upDiscountPercent,
						$ccc,
						$ckNum,
						$notes
				) );
				$upNewGetId = $db->prepare ( "SELECT id FROM $mySales WHERE items = ? ORDER BY id DESC LIMIT 1" );
				$upNewGetId->execute ( array (
						$upItemsS
				) );
				$ungi = $upNewGetId->fetch ();
				$upId = $ungi [0];
			} else {
				$update = $db->prepare ( "UPDATE $mySales SET time = ?, contactId = ?, items = ?, taxes = ?, shipping = ?, fees = ?, paid = ?, discountPercent = ?, ccc = ?, ckNum = ?, notes = ? WHERE id=?" );
				$update->execute ( array (
						$upDate,
						$contactId,
						$upItemsS,
						$upTaxes,
						$upShipping,
						$upFees,
						$paid,
						$upDiscountPercent,
						$ccc,
						$ckNum,
						$notes,
						$upId
				) );
			}

			if ($upDone == 1) {
				$showBox1 = "none";
				$showBox2 = "block";
				$link1Style = "cursor:pointer; font-weight:bold; text-decoration:none;";
				$link2Style = "cursor:pointer; font-weight:normal; text-decoration:underline;";
				$sellId = 0;
				$viewId = 0;

				$totalPriceLedger = 0.00;
				$totalCostLedger = 0.00;
				settype ( $totalPriceLedger, "float" );
				settype ( $totalCostLedger, "float" );
				foreach ( $upItems as $v2 ) {
					settype ( $v2 [0], "float" );
					settype ( $v2 [2], "float" );
					settype ( $v2 [3], "float" );
					settype ( $v2 [4], "int" );
					settype ( $v2 [5], "int" );
					$v2 [1] = ($v2 [4] == 0) ? invCheck ( $v2 [1], $v2 [5], $db, $myInventory, $time ) : $v2 [4];
					if ($v2 [0] >= .01) {
						$getQ = $db->prepare ( "SELECT quantity FROM $myInventory WHERE id = ?" );
						$getQ->execute ( array (
								$v2 [1]
						) );
						$getQR = $getQ->fetch ();
						if ($getQR) {
							$quantity = (($getQR ['quantity'] - $v2 [0]) >= 0) ? ($getQR ['quantity'] - $v2 [0]) : 0;
						} else {
							$quantity = 0;
						}

						$updateInv = $db->prepare ( "UPDATE $myInventory SET quantity = ? WHERE id = ?" );
						$updateInv->execute ( array (
								$quantity,
								$v2 [1]
						) );

						$totalPriceLedger += ($v2 [0] * $v2 [3]);
						$totalCostLedger += ($v2 [0] * $v2 [2]);
					}
				}

				$tot = ($totalPriceLedger + $upShipping + $upFees + $upTaxes - $upDiscountAmount);

				if ($paid == 1) {
					$cat = '101.0';
					$due = 0.00;
				} else {
					$cat = '110.0';
					$due = $tot;
				}
				// id, date, contact, description, cashCheckCC, checkNumber, accountNumber, debitAmount, creditAmount, refNumber, typeCode, dailyConfirm, notUsed1, notUsed2
				if ($tot >= 0.01) {
					$upLedgerTotal = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0',?,'0')" );
					$upLedgerTotal->execute ( array (
							$upDate,
							$contactId,
							"Sales Receipt",
							$ccc,
							$ckNum,
							$cat,
							$tot,
							$upId,
							'1',
							$due
					) );
				}
				if ($totalPriceLedger >= 0.01) {
					$upLedgerPrice = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0','0')" );
					$upLedgerPrice->execute ( array (
							$upDate,
							$contactId,
							"Sales Receipt",
							$ccc,
							$ckNum,
							'400.1',
							$totalPriceLedger,
							$upId,
							'1'
					) );
				}
				if ($upTaxes >= 0.01) {
					$upLedgerTax = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0','0')" );
					$upLedgerTax->execute ( array (
							$upDate,
							$contactId,
							"Sales Receipt",
							$ccc,
							$ckNum,
							'200.0',
							$upTaxes,
							$upId,
							'1'
					) );
				}
				if ($upShipping >= 0.01) {
					$upLedgerShipping = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0','0')" );
					$upLedgerShipping->execute ( array (
							$upDate,
							$contactId,
							"Sales Receipt",
							$ccc,
							$ckNum,
							'400.2',
							$upShipping,
							$upId,
							'1'
					) );
				}
				if ($upFees >= 0.01) {
					$upLedgerFees = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0','0')" );
					$upLedgerFees->execute ( array (
							$upDate,
							$contactId,
							"Sales Receipt",
							$ccc,
							$ckNum,
							'400.4',
							$upFees,
							$upId,
							'1'
					) );
				}
				if ($upDiscountAmount >= 0.01) {
					$upLedgerDiscount = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0','0')" );
					$upLedgerDiscount->execute ( array (
							$upDate,
							$contactId,
							"Sales Receipt",
							$ccc,
							$ckNum,
							'400.5',
							$upDiscountAmount,
							$upId,
							'1'
					) );
				}
				if ($totalCostLedger >= 0.01) {
					$upLedgerCostInv = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0','0')" );
					$upLedgerCostInv->execute ( array (
							$upDate,
							$contactId,
							"Sales Receipt",
							$ccc,
							$ckNum,
							'120.0',
							$totalCostLedger,
							$upId,
							'1'
					) );
					$upLedgerCostCOS = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0','0')" );
					$upLedgerCostCOS->execute ( array (
							$upDate,
							$contactId,
							"Sales Receipt",
							$ccc,
							$ckNum,
							'400.6',
							$totalCostLedger,
							$upId,
							'1'
					) );
				}

				$finalize = $db->prepare ( "UPDATE $mySales SET finalized = '1' WHERE id = ?" );
				$finalize->execute ( array (
						$upId
				) );
			} else {
				$showBox1 = "none";
				$showBox2 = "block";
				$link1Style = "cursor:pointer; font-weight:normal; text-decoration:underline;";
				$link2Style = "cursor:pointer; font-weight:bold; text-decoration:none;";
				$sellId = $upId;
			}
		}
	}
	?><div style="text-align:right;">
    <?php
	echo showHelpLeft ( 9, $db );
	?>
</div>
<div style="margin-bottom:10px;"><span id="link1" onclick="showBox1()" style="<?php

	echo $link1Style?>">Sales Transaction History</span> || <span id="link2" onclick="showBox2()" style="<?php

	echo $link2Style?>">New transaction</span></div>
        <div id="box1" style="display:<?php

	echo $showBox1;
	?>; margin:0px 10px;">
        <?php
	include "includes/sellBox1.php";
	?>
    </div>

    <div id="box2" style="display:<?php

	echo $showBox2;
	?>; margin:0px 10px;">
    <?php
	include "includes/sellBox2.php";
	?>
</div>
<?php
} else {
	echo "Please log in or check your subscription in settings to see your sales transactions";
}
