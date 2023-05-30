<div class="heading">Contacts</div>
<?php
if ($myId >= 1) {
	$upId = (filter_input ( INPUT_GET, 'contactId', FILTER_SANITIZE_NUMBER_INT )) ? filter_input ( INPUT_GET, 'contactId', FILTER_SANITIZE_NUMBER_INT ) : 0;

	if (filter_input ( INPUT_POST, 'payment', FILTER_SANITIZE_NUMBER_INT ) == 1) {
		$dueAmt = filter_input ( INPUT_POST, 'dueAmt', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
		$cccd = filter_input ( INPUT_POST, 'cccd', FILTER_SANITIZE_NUMBER_INT );
		$ckNumd = (filter_input ( INPUT_POST, 'ckNumd', FILTER_SANITIZE_NUMBER_INT ) >= 1) ? filter_input ( INPUT_POST, 'ckNumd', FILTER_SANITIZE_NUMBER_INT ) : 0;
		$oweAmt = filter_input ( INPUT_POST, 'oweAmt', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
		$ccco = filter_input ( INPUT_POST, 'ccco', FILTER_SANITIZE_NUMBER_INT );
		$ckNumo = (filter_input ( INPUT_POST, 'ckNumo', FILTER_SANITIZE_NUMBER_INT ) >= 1) ? filter_input ( INPUT_POST, 'ckNumo', FILTER_SANITIZE_NUMBER_INT ) : 0;
		$acc = filter_input ( INPUT_POST, 'acc', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );

		if ($dueAmt >= 0.01) {
			foreach ( $_POST as $key => $val ) {
				if (preg_match ( "/^ticket([0-9][0-9]*)$/", $key, $match ) && $val >= .01) {
					$rcvId = $match [1];
					if ($rcvId == 0 && $val >= 0.01) {
						$bget = $db->prepare ( "SELECT startingBalance, balancePaid FROM $myContacts WHERE id = ?" );
						$bget->execute ( array (
								$upId
						) );
						$bgetR = $bget->fetch ();
						if ($bgetR) {
							$startingBalance = $bgetR ['startingBalance'];
							$balancePaid = $bgetR ['balancePaid'];
							if ($balancePaid == 0 && $dueAmt >= $startingBalance) {
								$bset1 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0',?,'0')" );
								$bset1->execute ( array (
										$time,
										$upId,
										"Pay previous years balance",
										$cccd,
										$ckNumd,
										'101.0',
										$startingBalance,
										'0',
										'5',
										'0.00'
								) );
								$bset2 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0',?,'0')" );
								$bset2->execute ( array (
										$time,
										$upId,
										"Pay previous years balance",
										$cccd,
										$ckNumd,
										'110.0',
										$startingBalance,
										'0',
										'5',
										'0.00'
								) );
								$bset3 = $db->prepare ( "UPDATE $myContacts SET balancePaid = ? WHERE id = ?" );
								$bset3->execute ( array (
										'1',
										$upId
								) );
								$dueAmt -= $startingBalance;
							}
						}
					} else {
						$old1 = $db->prepare ( "SELECT description, refNumber, typeCode, balanceDue FROM $myFLedger WHERE id = ?" );
						$old1->execute ( array (
								$rcvId
						) );
						while ( $old1R = $old1->fetch () ) {
							$description = $old1R ['description'];
							$refNumber = $old1R ['refNumber'];
							$typeCode = $old1R ['typeCode'];
							$z = $balanceDue = $old1R ['balanceDue'];

							if ($dueAmt >= $balanceDue) {
								$dueAmt -= $balanceDue;
								$b = $balanceDue;
								$z = 0.00;
							} else {
								$z = $balanceDue -= $dueAmt;
								$b = $dueAmt;
								$dueAmt = 0.00;
							}
							if ($b >= 0.01) {
								$old2 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0',?,'0')" );
								$old2->execute ( array (
										$time,
										$upId,
										$description,
										$cccd,
										$ckNumd,
										'101.0',
										$b,
										$refNumber,
										$typeCode,
										'0.00'
								) );
								$old3 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0',?,'0')" );
								$old3->execute ( array (
										$time,
										$upId,
										$description,
										$cccd,
										$ckNumd,
										'110.0',
										$b,
										$refNumber,
										$typeCode,
										'0.00'
								) );

								$old4 = $db->prepare ( "UPDATE $myFLedger SET balanceDue = ? WHERE id = ?" );
								$old4->execute ( array (
										$z,
										$rcvId
								) );
								if ($z == 0.00 && $typeCode == 1) {
									$setSalePaid = $db->prepare ( "UPDATE $mySales SET paid = ?, ccc = ?, ckNum = ? WHERE id = ?" );
									$setSalePaid->execute ( array (
											'1',
											$cccd,
											$ckNumd,
											$refNumber
									) );
								} elseif ($z == 0.00 && $typeCode == 2) {
									$setPurchasingPaid = $db->prepare ( "UPDATE $myPurchasing SET paid = ? WHERE id = ?" );
									$setPurchasingPaid->execute ( array (
											'1',
											$refNumber
									) );
								}
							}
						}
					}
				}
			}
			if ($dueAmt >= 0.01) {
				$r = getNext ( '5', $db, $myFLedger );
				$old12 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0',?,'0')" );
				$old12->execute ( array (
						$time,
						$upId,
						'Credit to customer',
						$cccd,
						$ckNumd,
						'101.0',
						$dueAmt,
						$r,
						'5',
						'0.00'
				) );
				$old13 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0',?,'0')" );
				$old13->execute ( array (
						$time,
						$upId,
						'Credit to customer',
						$cccd,
						$ckNumd,
						'110.0',
						$dueAmt,
						$r,
						'5',
						'0.00'
				) );
				$dueAmt = 0.00;
			}
		}
		if ($oweAmt >= 0.01) {
			if ($acc == '110.0') {
				$getRcv = $db->prepare ( "SELECT id, balanceDue, refNumber FROM $myFLedger WHERE contact = ? AND accountNumber = '110.0' AND balanceDue >= '0.01' ORDER BY date" );
				$getRcv->execute ( array (
						$upId
				) );
				while ( $getRcvR = $getRcv->fetch () ) {
					$getRId = $getRcvR ['id'];
					$getRB = $getRcvR ['balanceDue'];
					$getRR = $getRcvR ['refNumber'];
					if ($oweAmt >= $getRB) {
						$oweAmt -= $getRB;
						$upR1 = $db->prepare ( "UPDATE $myFLedger SET balanceDue = ? WHERE id = ?" );
						$upR1->execute ( array (
								'0.00',
								$getRId
						) );
						$setSalePaid = $db->prepare ( "UPDATE $mySales SET paid = ?, ccc = ?, ckNum = ? WHERE id = ?" );
						$setSalePaid->execute ( array (
								'1',
								$ccco,
								$ckNumo,
								$getRR
						) );
					} else {
						$getRB -= $oweAmt;
						$upR2 = $db->prepare ( "UPDATE $myFLedger SET balanceDue = ? WHERE id = ?" );
						$upR2->execute ( array (
								$getRB,
								$getRId
						) );
						$oweAmt = 0.00;
					}
				}
			}

			foreach ( $_POST as $key => $val ) {
				if (preg_match ( "/^ticket([0-9][0-9]*)$/", $key, $match )) {
					$payId = $match [1];
					if ($payId == 0 && $val >= 0.01) {
						$bget = $db->prepare ( "SELECT startingBalance, balancePaid FROM $myContacts WHERE id = ?" );
						$bget->execute ( array (
								$upId
						) );
						$bgetR = $bget->fetch ();
						if ($bgetR) {
							$startingBalance = abs ( $bgetR ['startingBalance'] );
							$balancePaid = $bgetR ['balancePaid'];
							if ($balancePaid == 0 && $oweAmt >= $startingBalance) {
								$bset1 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0',?,'0')" );
								$bset1->execute ( array (
										$time,
										$upId,
										"Pay previous years balance",
										$ccco,
										$ckNumo,
										'210.0',
										$startingBalance,
										'0',
										'5',
										'0.00'
								) );
								$bset2 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0',?,'0')" );
								$bset2->execute ( array (
										$time,
										$upId,
										"Pay previous years balance",
										$ccco,
										$ckNumo,
										$acc,
										$startingBalance,
										'0',
										'5',
										'0.00'
								) );
								$bset3 = $db->prepare ( "UPDATE $myContacts SET balancePaid = ? WHERE id = ?" );
								$bset3->execute ( array (
										'1',
										$upId
								) );
								$dueAmt -= $startingBalance;
							}
						}
					}

					$old6 = $db->prepare ( "SELECT description, refNumber, typeCode, balanceDue FROM $myFLedger WHERE id = ?" );
					$old6->execute ( array (
							$payId
					) );
					while ( $old6R = $old6->fetch () ) {
						$description = $old6R ['description'];
						$refNumber = $old6R ['refNumber'];
						$typeCode = $old6R ['typeCode'];
						$z = $balanceDue = $old6R ['balanceDue'];

						if ($oweAmt >= $balanceDue) {
							$oweAmt -= $balanceDue;
							$b = $balanceDue;
							$z = 0.00;
						} else {
							$z = $balanceDue -= $oweAmt;
							$b = $oweAmt;
							$oweAmt = 0.00;
						}
						if ($b >= 0.01) {
							$old7 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0',?,'0')" );
							$old7->execute ( array (
									$time,
									$upId,
									$description,
									$ccco,
									$ckNumo,
									$acc,
									$b,
									$refNumber,
									$typeCode,
									'0.00'
							) );
							$old8 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0',?,'0')" );
							$old8->execute ( array (
									$time,
									$upId,
									$description,
									$ccco,
									$ckNumo,
									'210.0',
									$b,
									$refNumber,
									$typeCode,
									'0.00'
							) );

							$old9 = $db->prepare ( "UPDATE $myFLedger SET balanceDue = ? WHERE id = ?" );
							$old9->execute ( array (
									$z,
									$payId
							) );

							if ($z == 0.00 && $typeCode == 2) {
								$setPurchasingPaid = $db->prepare ( "UPDATE $myPurchasing SET paid = ? WHERE id = ?" );
								$setPurchasingPaid->execute ( array (
										'1',
										$refNumber
								) );
							}
						}
					}
				}
			}
			if ($oweAmt >= 0.01) {
				$r = getNext ( '5', $db, $myFLedger );
				$old14 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0',?,'0')" );
				$old14->execute ( array (
						$time,
						$upId,
						'Overpayment to vendor',
						$ccco,
						$ckNumo,
						$acc,
						$oweAmt,
						$r,
						'5',
						'0.00'
				) );
				$old15 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0',?,'0')" );
				$old15->execute ( array (
						$time,
						$upId,
						'Overpayment to vendor',
						$ccco,
						$ckNumo,
						'210.0',
						$oweAmt,
						$r,
						'5',
						'0.00'
				) );
				$oweAmt = 0.00;
			}
		}
	}

	if (filter_input ( INPUT_POST, 'contactId', FILTER_SANITIZE_STRING )) {
		$upId = filter_input ( INPUT_POST, 'contactId', FILTER_SANITIZE_NUMBER_INT );
		$upName = filter_var ( htmlEntities ( trim ( $_POST ['name'] ), ENT_QUOTES ), FILTER_SANITIZE_STRING );
		$upAddress1 = filter_var ( htmlEntities ( trim ( $_POST ['address1'] ), ENT_QUOTES ), FILTER_SANITIZE_STRING );
		$upAddress2 = filter_var ( htmlEntities ( trim ( $_POST ['address2'] ), ENT_QUOTES ), FILTER_SANITIZE_STRING );
		$upPhone = filter_input ( INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT );
		$upEmail = filter_input ( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );
		$upChargeTax = filter_input ( INPUT_POST, 'chargeTax', FILTER_SANITIZE_NUMBER_INT );
		$upVendor = filter_input ( INPUT_POST, 'vendor', FILTER_SANITIZE_NUMBER_INT );
		$delId = (filter_input ( INPUT_POST, 'delId', FILTER_SANITIZE_NUMBER_INT ) == 1) ? $upId : "N";

		if ($delId == $upId) {
			$delS = $db->prepare ( "DELETE FROM $myContacts WHERE id = ?" );
			$delS->execute ( array (
					$upId
			) );
		} else {
			if ($upId == '0') {
				$upNew = $db->prepare ( "INSERT INTO $myContacts VALUES(NULL,?,?,?,?,?,?,?,?,'0.00','0.00','0','0')" );
				$upNew->execute ( array (
						$upName,
						$upAddress1,
						$upAddress2,
						$upPhone,
						$upEmail,
						$time,
						$upChargeTax,
						$upVendor
				) );
				$upNewGetId = $db->prepare ( "SELECT id FROM $myContacts WHERE lastAccessed = ? ORDER BY id DESC LIMIT 1" );
				$upNewGetId->execute ( array (
						$time
				) );
				$ungi = $upNewGetId->fetch ();
				$upId = $ungi ['id'];
			} else {
				$update = $db->prepare ( "UPDATE $myContacts SET name = ?, address1 = ?, address2 = ?, phone = ?, email = ?, chargeTax = ?, vendor = ? WHERE id = ?" );
				$update->execute ( array (
						$upName,
						$upAddress1,
						$upAddress2,
						$upPhone,
						$upEmail,
						$upChargeTax,
						$upVendor,
						$upId
				) );
			}
		}
	}
	?>
    <table style="width:100%; border:1px solid black;">
        <tr>
            <td style="width:300px; padding:5px; border:1px solid black;">
                <form id="frm0" action="index.php?page=contacts&contactId=0" method="post">
                    <div style="line-height:1.5; font-weight:bold; text-decoration:none; cursor:pointer;" onclick="submitForm('0')">
                        <table style="width:300px;">
                            <tr>
                                <td style="text-align:left;">NEW CONTACT</td>
                                <td id="selected0" style="text-align:right;"><?php

	echo ($upId == 0) ? " >>> " : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	?></td>
                            </tr>
                        </table>
                    </div>
                </form>
                <div style=" margin-top:20px;">
                    <table style="width:300px;">
                        <tr>
                            <td class="cat" style="text-align:left; line-height:1.5; font-weight:bold; text-decoration:none; font-size:1.25em;">Vendors</td>
                            <td>   </td>
                        </tr>
                    </table>
                </div>
                <?php
	$getContacts1 = $db->prepare ( "SELECT id, name FROM $myContacts WHERE vendor = '1' ORDER BY name" );
	$getContacts1->execute ();
	while ( $getC1 = $getContacts1->fetch () ) {
		$rId = $getC1 ['id'];
		$rName = html_entity_decode ( $getC1 ['name'], ENT_QUOTES );
		?>
                    <form id="frm<?php
		echo $rId;
		?>" action="index.php?page=contacts&contactId=<?php

		echo $rId;
		?>" method="post">
                    <div style="line-height:1.5; font-weight:bold; text-decoration:none; cursor:pointer;" onclick="submitForm('<?php

		echo $rId;
		?>')">
                        <table style="width:300px;">
                            <tr>
                            <td style="text-align:left;"><?php

		echo $rName;
		?></td>
                            <td id="selected<?php

		echo $rId;
		?>" style="text-align:right;"><?php

		echo ($upId == $rId) ? " >>> " : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		?></td>
                        </tr>
                    </table>
                </div>
            </form>
            <?php
	}
	?>
                <div style="margin-top:20px;">
                    <table style="width:300px;">
                        <tr>
                            <td class="cat" style="text-align:left; line-height:1.5; font-weight:bold; text-decoration:none; font-size:1.25em;">Customers</td>
                            <td>   </td>
                        </tr>
                    </table>
                </div>
                <?php
	$getContacts2 = $db->prepare ( "SELECT id, name FROM $myContacts WHERE vendor = '0' ORDER BY name" );
	$getContacts2->execute ();
	while ( $getC2 = $getContacts2->fetch () ) {
		$rId = $getC2 ['id'];
		$rName = html_entity_decode ( $getC2 ['name'], ENT_QUOTES );
		?>
                    <form id="frm<?php

		echo $rId;
		?>" action="index.php?page=contacts&contactId=<?php

		echo $rId;
		?>" method="post">
                    <div style="line-height:1.5; font-weight:bold; text-decoration:none; cursor:pointer;" onclick="submitForm('<?php

		echo $rId;
		?>')">
                        <table style="width:300px;">
                            <tr>
                            <td style="text-align:left;"><?php

		echo $rName;
		?></td>
                            <td id="selected<?php

		echo $rId;
		?>" style="text-align:right;"><?php

		echo ($upId == $rId) ? " >>> " : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		?></td>
                        </tr>
                    </table>
                </div>
            </form>
            <?php
	}
	?>
            </td>
            <td id="contactEdit" style="padding:5px; border:1px solid black;">
                <div style="text-align:right;">
                <?php
	echo showHelpLeft ( 17, $db );
	?>
            </div>
            <?php
	$name = "";
	$address1 = "";
	$address2 = "";
	$phone = "";
	$email = "";
	$lastAccessed = "";
	$chargeTax = "";
	$vendor = "";

	if ($upId >= 1) {
		$gi = $db->prepare ( "SELECT * FROM $myContacts WHERE id = ?" );
		$gi->execute ( array (
				$upId
		) );
		$giR = $gi->fetch ();
		if ($giR) {
			$name = html_entity_decode ( $giR ['name'], ENT_QUOTES );
			$address1 = html_entity_decode ( $giR ['address1'], ENT_QUOTES );
			$address2 = html_entity_decode ( $giR ['address2'], ENT_QUOTES );
			$phone = $giR ['phone'];
			$email = $giR ['email'];
			$lastAccessed = $giR ['lastAccessed'];
			$chargeTax = $giR ['chargeTax'];
			$vendor = $giR ['vendor'];
		}
	}
	?>

            <form action="index.php?page=contacts" method="post">
                <table style="width:100%;">
                    <tr>
                        <td>Name
                            <td>
                            <input type="text" name="name" value="<?php

	echo $name;
	?>" size="40">
                        </tr>
                        <tr>
                            <td>Address
                            <td><input type="text" name="address1" value="<?php

	echo $address1;
	?>" placeholder="Street / Unit"><br />
                            <input type="text" name="address2" value="<?php

	echo $address2;
	?>" placeholder="City, St Zip">
                        </tr>
                        <tr>
                            <td>Phone
                            <td><input type="text" name="phone" value="<?php

	echo $phone;
	?>">
                        </tr>
                        <tr>
                            <td>Email
                            <td><input type="text" name="email" value="<?php

	echo $email;
	?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Tax Exempt
                        <td><input type="radio" name="chargeTax" value="0"<?php

	echo ($chargeTax == '0') ? " checked" : "";
	?>> Tax Exempt<br />
                        <input type="radio" name="chargeTax" value="1"<?php

	echo ($chargeTax == '1') ? " checked" : "";
	?>> Charge Tax

                    </td>
                </tr>
                <tr>
                    <td>Vendor
                    <td><input type="radio" name="vendor" value="0"<?php

	echo ($vendor == '0') ? " checked" : "";
	?>> Customer<br />
                    <input type="radio" name="vendor" value="1"<?php

	echo ($vendor == '1') ? " checked" : "";
	?>> Vendor
                </td>
            </tr>
            <tr>
                <td style="text-align:center;" colspan="2">
                <?php
	$checkCount1 = $db->prepare ( "SELECT COUNT(*) FROM $myFLedger WHERE contact = ?" );
	$checkCount1->execute ( array (
			$upId
	) );
	$checkCount1R = $checkCount1->fetch ();
	$a = $checkCount1R [0];

	$checkCount2 = $db->prepare ( "SELECT COUNT(*) FROM $myInventory WHERE contactId = ?" );
	$checkCount2->execute ( array (
			$upId
	) );
	$checkCount2R = $checkCount2->fetch ();
	$b = $checkCount2R [0];

	$checkCount3 = $db->prepare ( "SELECT COUNT(*) FROM $myPurchasing WHERE contactId = ?" );
	$checkCount3->execute ( array (
			$upId
	) );
	$checkCount3R = $checkCount3->fetch ();
	$c = $checkCount3R [0];

	$checkCount4 = $db->prepare ( "SELECT COUNT(*) FROM $mySales WHERE contactId = ?" );
	$checkCount4->execute ( array (
			$upId
	) );
	$checkCount4R = $checkCount4->fetch ();
	$d = $checkCount4R [0];

	$x = ($a + $b + $c + $d);

	echo ($upId >= 1 && $x == 0) ? "Delete this Contact? <input type='checkbox' name='delId' value='1'><br /><br />" : "";
	echo ($upId >= 1) ? "<input type='submit' value=' Update Contact '>" : "<input type='submit' value=' Add New Contact '>";
	?>
                <input type="hidden" name="contactId" value="X<?php

	echo $upId;
	?>">
            </td>
        </tr>
    </table>
</form>
<form action="index.php?page=contacts&contactId=<?php
	echo $upId;
	?>" method="post">
<table>
    <tr>
        <td style="font-weight:bold; font-size:1.25em;" colspan="7">Transactions</td>
    </tr>
    <tr>
        <td>Date</td>
        <td>Type</td>
        <td>Debit</td>
        <td>Credit</td>
        <td></td>
        <td style="text-align:center;">Starting Balance<br>
        </td>
        <td>Include<br>due / owe</td>
    </tr>
    <tr>
    <td colspan='5'></td>
    <td style="text-align:center;">
    <?php
	$getS = $db->prepare ( "SELECT startingBalance, balancePaid FROM $myContacts WHERE id = ?" );
	$getS->execute ( array (
			$upId
	) );
	$getSR = $getS->fetch ();
	$ss = $s = ($getSR) ? $getSR ['startingBalance'] : 0.00;
	$bpaid = ($getSR) ? $getSR ['balancePaid'] : 0;
	echo money ( $s, $currency, $langCode );
	?>
	</td>
    <td style="text-align:center;">
    <?php
	if (($ss >= 0.01 || $ss <= - 0.01) && $bpaid == 0) {
		echo "<input class='due' type='checkbox' name='ticket0' value='$ss' onclick='totalIt()' checked>";
	} else {
		echo "<input type='hidden' name='ticket0' value='0'>";
	}
	?>
	</td>
    </tr>
    <?php
	$trans = array ();
	$getS = $db->prepare ( "SELECT id, date, debitAmount, creditAmount, refNumber, typeCode, balanceDue FROM $myFLedger WHERE (accountNumber = ? OR accountNumber = ?) AND contact = ? ORDER BY date" );
	$getS->execute ( array (

			'110.0',
			'210.0',
			$upId
	) );
	while ( $gs = $getS->fetch () ) {
		$t = $gs ['id'];
		$trans [$t] [0] = $gs ['date'];
		$trans [$t] [1] = $gs ['debitAmount'];
		$trans [$t] [2] = $gs ['creditAmount'];
		$trans [$t] [3] = $gs ['refNumber'];
		$trans [$t] [4] = $gs ['typeCode'];
		$trans [$t] [5] = $gs ['balanceDue'];
	}

	foreach ( $trans as $k => $v ) {
		?>
        <tr>
        <td><?php
		echo date ( "Y-m-d", $v [0] );
		?></td>
        <td>
        <?php
		switch ($v [4]) {
			case 1 :
				echo "<a href='index.php?page=sell&viewId=" . $v [3] . "'>Sales invoice #" . $v [3] . "</a>";
				break;
			case 2 :
				echo "<a href='index.php?page=buy&viewId=" . $v [3] . "'>Purchasing invoice #" . $v [3] . "</a>";
				break;
			case 5 :
				echo "<a href='index.php?page=reports&r=journal&je=" . $v [3] . "'>Cash Transaction #" . $v [3] . "</a>";
				break;
			case 6 :
				echo "<a href='index.php?page=reports&r=general&an=" . $v [3] . "'>General Entry #" . $v [3] . "</a>";
				break;
		}
		?>
    </td>
    <td><?php
		echo ($v [1] >= 0.01) ? money ( $v [1], $currency, $langCode ) : "";
		?></td>
    <td><?php
		echo ($v [2] >= 0.01) ? money ( $v [2], $currency, $langCode ) : "";
		?></td>
    <td></td>
    <td style="text-align:center;">
    <?php
		$tAmt = ($v [1] - $v [2]);
		$s += $tAmt;
		echo money ( $s, $currency, $langCode );
		?>
	</td>
    <td style="text-align:center;">
    <?php
		echo "<input class='due' type='checkbox' name='ticket" . $k . "' value='" . $tAmt . "' onclick='totalIt()' checked>";
		?>
    </td>
</tr>

<?php
	}
	?>
    <tr>
        <td colspan="7" style="text-align:center; height:2px; background-color:#cccccc;"></td>
    </tr>
    <tr>
        <td colspan="7" style="text-align:center; font-weight:bold;">Receive a payment from Customer:</td>
    </tr>
<tr>
    <td colspan="4" style="text-align:right; margin:20px 0px;">Payment Method: <select name='cccd' size='1'>
    <option value='0'>Cash / Check / Card</option>
    <option value='1'>Cash</option>
    <option value='2'>Check</option>
    <option value='3'>Charge</option>
    </select> <input type='text' name='ckNumd' value='' placeholder='ck num' size='6'>
</td>
<td colspan="2" style="text-align:left;"><input id='dueTotal' type="number" name="dueAmt" step='.01' min='0.00' value="0.00"></td>
    <td><input type="submit" value=" Save Payment "></td>
    </tr>
    <tr>
        <td colspan="7" style="text-align:center; height:2px; background-color:#cccccc;"></td>
    </tr>
    <tr>
        <td colspan="7" style="text-align:center; font-weight:bold;">Make a payment to Customer:</td>
    </tr>
<tr>
    <td colspan="4" style="text-align:right; margin:20px 0px;">Payment Account: <select name='acc' size='1'>
    <?php
	$acc = $db->prepare ( "SELECT accountNumber, accountName FROM $myFAccounts WHERE accountNumber >= '100.0' AND accountNumber <= '109.9' ORDER BY accountNumber" );
	$acc->execute ();
	while ( $accR = $acc->fetch () ) {
		$an = $accR ['accountNumber'];
		$aname = $accR ['accountName'];

		echo "<option value='$an'>$aname</option>\n";
	}
	echo "<option value='110.0'>Apply Credit Amt</option>\n";
	?>
    </select> Payment Method: <select name='ccco' size='1'>
    <option value='0'>Cash / Check / Card</option>
    <option value='1'>Cash</option>
    <option value='2'>Check</option>
    <option value='3'>Charge</option>
    </select> <input type='text' name='ckNumo' value='' placeholder='ck num' size='6'>
</td>
<td colspan="2" style="text-align:left;"><input id='oweTotal' type="number" name="oweAmt" step='.01' min='0.00' value="0.00"></td>
    <td><input type="submit" value=" Save Payment "><input type="hidden" name="payment" value="1"></td>
    </tr>
</table>
</form>
</td>
</tr>
</table>
<?php
} else {
	echo "Please log in or check your subscription in settings to view your contacts";
}
