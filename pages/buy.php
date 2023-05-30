<div class="heading">Purchasing</div>
<?php
use function queryFactoryResult\count;

if ($myId >= 1 && $SA == 0) {
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
	$link1Style = "font-weight:bold; text-decoration:none;";
	$link2Style = "cursor:pointer; font-weight:normal; text-decoration:underline;";
	$sellId = 0;
	$viewId = 0;

	if (filter_input ( INPUT_POST, 'viewId', FILTER_SANITIZE_NUMBER_INT )) {
		$viewId = filter_input ( INPUT_POST, 'viewId', FILTER_SANITIZE_NUMBER_INT );
	}
	if (filter_input ( INPUT_GET, 'viewId', FILTER_SANITIZE_NUMBER_INT )) {
		$viewId = filter_input ( INPUT_GET, 'viewId', FILTER_SANITIZE_NUMBER_INT );
	}

	if (filter_input ( INPUT_POST, 'getSellId', FILTER_SANITIZE_NUMBER_INT )) {
		$showBox1 = "none";
		$showBox2 = "block";
		$link1Style = "cursor:pointer; font-weight:normal; text-decoration:underline;";
		$link2Style = "font-weight:bold; text-decoration:none;";
		$sellId = filter_input ( INPUT_POST, 'getSellId', FILTER_SANITIZE_NUMBER_INT );
	} else {
		$sellId = 0;
	}

	$ccc = 0;
	$ckNum = 0;
	if (filter_input ( INPUT_POST, 'sellId', FILTER_SANITIZE_STRING )) {
		$upId = filter_input ( INPUT_POST, 'sellId', FILTER_SANITIZE_NUMBER_INT );
		$upName = filter_var ( htmlEntities ( trim ( $_POST ['contactName'] ), ENT_QUOTES ), FILTER_SANITIZE_STRING );
		$upNameSelect = filter_input ( INPUT_POST, 'contactNameSelect', FILTER_SANITIZE_NUMBER_INT );
		$upDate = date2mktime ( filter_input ( INPUT_POST, 'date', FILTER_SANITIZE_NUMBER_INT ), 'noon' );
		$upShipping = filter_input ( INPUT_POST, 'shipping', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
		$upFees = filter_input ( INPUT_POST, 'fees', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
		$upDone = (filter_input ( INPUT_POST, 'done', FILTER_SANITIZE_NUMBER_INT ) == 1) ? 1 : 0;
		$upTaxes = (filter_input ( INPUT_POST, 'taxes', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ));
		$paid = (filter_input ( INPUT_POST, 'paid', FILTER_SANITIZE_NUMBER_INT ) == 1) ? 1 : 0;
		$ccc = (filter_input ( INPUT_POST, 'ccc', FILTER_SANITIZE_NUMBER_INT ));
		$ckNum = (filter_input ( INPUT_POST, 'ckNum', FILTER_SANITIZE_NUMBER_INT ));
		$fromAcc = (filter_input ( INPUT_POST, 'fromAcc', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ));
		$delSale = (filter_input ( INPUT_POST, 'delSale', FILTER_SANITIZE_NUMBER_INT ) == 1) ? $upId : "N";

		if ($delSale == $upId) {
			$delS = $db->prepare ( "DELETE FROM $myPurchasing WHERE id = ?" );
			$delS->execute ( array (
					$upId
			) );
		} else {
			$contactId = ($upNameSelect == 0) ? conCheck ( $upName, $db, $myContacts, $time, '1' ) : $upNameSelect;

			$upItems = array ();
			foreach ( $_POST as $key => $val ) {
				if (preg_match ( "/^invQty([0-9][0-9]*)$/", $key, $match )) {
					$upItems [$match [1]] [0] = filter_var ( $val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
				}
				if (preg_match ( "/^invUOM([0-9][0-9]*)$/", $key, $match )) {
					$upItems [$match [1]] [1] = filter_var ( $val, FILTER_SANITIZE_NUMBER_INT );
				}
				if (preg_match ( "/^invName([0-9][0-9]*)$/", $key, $match )) {
					$upItems [$match [1]] [2] = filter_var ( $val, FILTER_SANITIZE_STRING );
				}
				if (preg_match ( "/^invCost([0-9][0-9]*)$/", $key, $match )) {
					$upItems [$match [1]] [3] = filter_var ( $val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
				}
				if (preg_match ( "/^invNameSelect([0-9][0-9]*)$/", $key, $match )) {
					$upItems [$match [1]] [4] = filter_var ( $val, FILTER_SANITIZE_NUMBER_INT );
				}
			}

			$totalItems = 0;
			$upItemsString = "";

			$uiCount = 0;
			foreach ( $upItems as $v1 ) {
				if ($v1 [0] >= .01) {
					settype ( $v1 [0], "float" );
					settype ( $v1 [1], "int" );
					settype ( $v1 [3], "float" );
					settype ( $v1 [4], "int" );
					$v1 [2] = ($v1 [4] == 0) ? invCheck ( $v1 [2], $v1 [1], $db, $myInventory, $time ) : $v1 [4];

					$totalItems += $v1 [3];

					for($j = 0; $j < 4; ++ $j) {
						$upItemsString .= $v1 [$j];
						$upItemsString .= ($j != 3) ? "," : ";";
					}
				}
				$uiCount ++;
			}

			if ($upItemsString != "") {
				$a = str_split ( $upItemsString );
				array_pop ( $a );
				$upItemsS = implode ( "", $a );
			} else {
				$upItemsS = "";
			}

			if ($upId == 0) {
				$upNew = $db->prepare ( "INSERT INTO $myPurchasing VALUES(NULL,?,?,?,?,?,?,'0',?,?,'0')" );
				$upNew->execute ( array (
						$upDate,
						$contactId,
						$upItemsS,
						$upTaxes,
						$upShipping,
						$upFees,
						$paid,
						$fromAcc
				) );
				$upNewGetId = $db->prepare ( "SELECT id FROM $myPurchasing WHERE items = ? ORDER BY id DESC LIMIT 1" );
				$upNewGetId->execute ( array (
						$upItemsS
				) );
				$ungi = $upNewGetId->fetch ();
				$upId = $ungi [0];
			} else {
				$update = $db->prepare ( "UPDATE $myPurchasing SET time = ?, contactId = ?, items = ?, taxes = ?, shipping = ?, fees = ?, paid = ?, fromAcc = ? WHERE id=?" );
				$update->execute ( array (
						$upDate,
						$contactId,
						$upItemsS,
						$upTaxes,
						$upShipping,
						$upFees,
						$paid,
						$fromAcc,
						$upId
				) );
			}

			if ($upDone == 1) {
				$showBox1 = "block";
				$showBox2 = "none";
				$link1Style = "font-weight:bold; text-decoration:none;";
				$link2Style = "cursor:pointer; font-weight:normal; text-decoration:underline;";
				$sellId = 0;

				$size = 0;
				foreach ( $upItems as $v ) {
					$size ++;
				}
				for($i = 0; $i < $size; ++ $i) {
					settype ( $upItems [$i] [0], "float" );
					settype ( $upItems [$i] [1], "int" );
					settype ( $upItems [$i] [3], "float" );
					settype ( $upItems [$i] [4], "int" );
					$upItems [$i] [2] = ($upItems [$i] [4] == 0) ? invCheck ( $upItems [$i] [2], $upItems [$i] [1], $db, $myInventory, $time ) : $upItems [$i] [4];
				}

				if ($purchasingCostProcessing == 0) {
					$percentItems = array ();
					for($i = 0; $i < $uiCount; ++ $i) {
						$percentItems [$i] [0] = ($upItems [$i] [3] / $totalItems);
					}

					$extraItems = ($upTaxes + $upShipping + $upFees);
					for($i = 0; $i < $uiCount; ++ $i) {
						if ($upItems [$i] [0] >= .01) {
							$upItems [$i] [3] = (($percentItems [$i] [0] * $extraItems + $upItems [$i] [3]) / $upItems [$i] [0]);

							$updateInv = $db->prepare ( "UPDATE $myInventory SET quantity = quantity + ?, cost = ? WHERE id = ?" );
							$updateInv->execute ( array (
									$upItems [$i] [0],
									$upItems [$i] [3],
									$upItems [$i] [2]
							) );
						}
					}

					$tot = ($totalItems + $upShipping + $upFees + $upTaxes);

					if ($paid == 1) {
						$cat = $fromAcc;
						$due = 0.00;
					} else {
						$cat = '210.0';
						$due = $tot;
					}
					// id, date, contact, description, cashCheckCC, checkNumber, accountNumber, debitAmount, creditAmount, refNumber, typeCode, dailyConfirm, balanceDue, notUsed2
					if ($tot >= 0.01) {
						$upLedgerTotal = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0','0')" );
						$upLedgerTotal->execute ( array (
								$upDate,
								$contactId,
								"Purchasing Receipt",
								$ccc,
								$ckNum,
								'120.0',
								$tot,
								$upId,
								'2'
						) );
					}
					if ($totalItems >= 0.01) {
						$upLedgerPrice = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0',?,'0')" );
						$upLedgerPrice->execute ( array (
								$upDate,
								$contactId,
								"Purchasing Receipt",
								$ccc,
								$ckNum,
								$cat,
								$tot,
								$upId,
								'2',
								$due
						) );
					}
				} else {
					for($i = 0; $i < count ( $upItems ); ++ $i) {
						$updateInv = $db->prepare ( "UPDATE $myInventory SET quantity = quantity + ?, cost = ? WHERE id = ?" );
						$updateInv->execute ( array (
								$upItems [$i] [0],
								$upItems [$i] [3],
								$upItems [$i] [2]
						) );
					}

					$tot = ($totalItems + $upShipping + $upFees + $upTaxes);
					if ($paid == 1) {
						$cat = $fromAcc;
						$due = 0.00;
					} else {
						$cat = '210.0';
						$due = $tot;
					}
					// id, date, contact, description, cashCheckCC, checkNumber, accountNumber, debitAmount, creditAmount, refNumber, typeCode
					if ($totalItems >= 0.01) {
						$upLedgerTotal = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0','0')" );
						$upLedgerTotal->execute ( array (
								$upDate,
								$contactId,
								"Purchasing Receipt - Inventory",
								$ccc,
								$ckNum,
								'120.0',
								$totalItems,
								$upId,
								'2'
						) );
					}
					if ($upShipping >= 0.01) {
						$upLedgerTotal = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0','0')" );
						$upLedgerTotal->execute ( array (
								$upDate,
								$contactId,
								"Purchasing Receipt - Shipping",
								$ccc,
								$ckNum,
								'400.2',
								$upShipping,
								$upId,
								'2'
						) );
					}
					if ($upFees >= 0.01) {
						$upLedgerTotal = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0','0')" );
						$upLedgerTotal->execute ( array (
								$upDate,
								$contactId,
								"Purchasing Receipt - Fees",
								$ccc,
								$ckNum,
								'400.7',
								$upFees,
								$upId,
								'2'
						) );
					}
					if ($upTaxes >= 0.01) {
						$upLedgerTotal = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0','0')" );
						$upLedgerTotal->execute ( array (
								$upDate,
								$contactId,
								"Purchasing Receipt - Taxes",
								$ccc,
								$ckNum,
								'400.8',
								$upTaxes,
								$upId,
								'2'
						) );
					}
					if ($tot >= 0.01) {
						$upLedgerPrice = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0',?,'0')" );
						$upLedgerPrice->execute ( array (
								$upDate,
								$contactId,
								"Purchasing Receipt",
								$ccc,
								$ckNum,
								$cat,
								$tot,
								$upId,
								'2',
								$due
						) );
					}
				}

				$finalize = $db->prepare ( "UPDATE $myPurchasing SET finalized = '1' WHERE id = ?" );
				$finalize->execute ( array (
						$upId
				) );
			} else {
				$showBox1 = "none";
				$showBox2 = "block";
				$link1Style = "cursor:pointer; font-weight:normal; text-decoration:underline;";
				$link2Style = "font-weight:bold; text-decoration:none;";
				$sellId = $upId;
			}
		}
	}
	?>
    <div style="text-align:right;">
    <?php
	echo showHelpLeft ( 8, $db );
	?>
</div>
<div style="margin-bottom:10px;"><span id="link1" onclick="showBox1()" style="<?php

	echo $link1Style?>">Purchase Transaction History</span> || <span id="link2" onclick="showBox2()" style="<?php

	echo $link2Style?>">New transaction</span></div>
        <div id="box1" style="display:<?php

	echo $showBox1;
	?>; margin:0px 10px;">
        <?php
	include "includes/buyBox1.php";
	?>
    </div>

    <div id="box2" style="display:<?php

	echo $showBox2;
	?>; margin:0px 10px;">
    <?php
	include "includes/buyBox2.php";
	?>
</div>
<?php
} else {
	echo "Please log in or check your subscription in settings to see your purchase transactions";
}
