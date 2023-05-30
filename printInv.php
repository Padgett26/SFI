<?php
include "cgi-bin/config.php";
include "cgi-bin/functions.php";

foreach ( $_POST as $key => $val ) {
	$upVal = filter_var ( $val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
	if (preg_match ( "/^invQty([0-9][0-9]*)$/", $key, $match )) {
		$i = $match [1];
		$v = $upVal;

		$getInvQty = $db->prepare ( "SELECT quantity, cost FROM $myInventory WHERE id = ?" );
		$getInvQty->execute ( array (
				$i
		) );
		$giqR = $getInvQty->fetch ();
		$q = $giqR ['quantity'];
		$c = $giqR ['cost'];

		if ($q != $v) {
			$update = $db->prepare ( "UPDATE $myInventory SET quantity = ?, time = ? WHERE id = ?" );
			$update->execute ( array (
					$v,
					$time,
					$i
			) );

			if ($v > $q) {
				$diff = (($v - $q) * $c);
				// id, date, contact, description, cashCheckCC, checkNumber, accountNumber, debitAmount, creditAmount, refNumber, typeCode
				$upLedger1 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,'1',?,'0','0',?,?,'0.00',?,?,'0','0','0')" );
				$upLedger1->execute ( array (
						$time,
						"Edit Inventory Item",
						'120.0',
						$diff,
						$i,
						'4'
				) );
				$upLedger2 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,'1',?,'0','0',?,'0.00',?,?,?,'0','0','0')" );
				$upLedger2->execute ( array (
						$time,
						"Edit Inventory Item",
						'400.3',
						$diff,
						$i,
						'4'
				) );
			} elseif ($v < $q) {
				$diff = (($q - $v) * $c);
				$upLedger1 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,'1',?,'0','0',?,'0.00',?,?,?,'0','0','0')" );
				$upLedger1->execute ( array (
						$time,
						"Edit Inventory Item",
						'120.0',
						$diff,
						$i,
						'4'
				) );
				$upLedger2 = $db->prepare ( "INSERT INTO $myFLedger VALUES(NULL,?,'1',?,'0','0',?,?,'0.00',?,?,'0','0','0')" );
				$upLedger2->execute ( array (
						$time,
						"Edit Inventory Item",
						'400.3',
						$diff,
						$i,
						'4'
				) );
			}
		}
	}
}
?>
<!DOCTYPE HTML>
<head>
    <?php
				include "includes/head.php";
				?>
</head>
<body>
    <?php
				if ($myId >= 1) {
					?>
        <form action="printInv.php" method="post">
            <table id="printArea" class="table1" cellspacing="5px">
                <tr>
                    <td style="text-align: center; font-weight:bold; font-size:2em;" colspan="6">Inventory</td>
                </tr>
                <tr>
                    <td style="text-align:left; font-weight:bold;"><button onclick='window.history.back()'> Back </button></td>
                    <td style="text-align:right; font-weight:bold;"><a href="index.php?page=home" style="text-decoration:none; color:#000000;">Simple Inv Home</a></td>
                    <td colspan="2"></td>
                    <td style="text-align:right; font-weight:bold;"><button onclick='window.print()'> Print </button></td>
                    <td style="text-align:center; font-weight:bold;"><input type='submit' value=' Save Often '></td>
                </tr>
                <?php
					$totalCost = 0;
					$totalQty = 0;
					$totalItems = 0;

					$getCat = $db->prepare ( "SELECT * FROM $myCategories WHERE subOf = ? AND id != ? AND id != ? ORDER BY category" );
					$getCat->execute ( array (
							'0',
							'1',
							'3'
					) );
					while ( $getC = $getCat->fetch () ) {
						$cId = $getC ['id'];
						$cCategory = $getC ['category'];

						echo "<tr><td colspan='6' style='text-align:center; font-weight:bold; border:1px solid black; background-color:#dddddd;'>$cCategory</td></tr>\n";
						?>
                    <tr>
                        <td style="text-align:left; font-weight:bold;">Name</td>
                        <td style="text-align:center; font-weight:bold;">Cost</td>
                        <td style="text-align:center; font-weight:bold;">Price</td>
                        <td style="text-align:center; font-weight:bold;">Qty</td>
                        <td style="text-align:center; font-weight:bold;" colspan="2">On Hand</td>
                    </tr>
                    <?php
						$getItems1 = $db->prepare ( "SELECT id, time, name, quantity, cost, price, unitOfMeasure FROM $myInventory WHERE categoryId = ? ORDER BY name" );
						$getItems1->execute ( array (
								$cId
						) );
						while ( $gi1 = $getItems1->fetch () ) {
							$id = $gi1 [0];
							$lastChanged = $gi1 [1];
							$name = $gi1 [2];
							$quantity = $gi1 [3];
							$cost = $gi1 [4];
							$price = $gi1 [5];
							$unit = $UOM [$gi1 [6]];

							$totalCost += ($cost * $quantity);
							$totalQty += $quantity;
							$totalItems ++;
							?>
                        <tr>
                            <td><div style="text-align:left;"><?php
							echo $name;
							?></div><div style="text-align:right; font-size:.75em;">Last change: <?php
							echo date ( "m-d-Y", $lastChanged );
							?></div></td>
                            <td style="text-align:center;">$<?php
							echo money ( $cost, $currency, $langCode ) . " " . $unit;
							?></td>
                            <td style="text-align:center;">$<?php
							echo money ( $price, $currency, $langCode );
							?></td>
                            <td style="text-align:center;"><?php
							echo $quantity . " " . $unit;
							?></td>
                            <td style="text-align:center; "><input type='number' name='invQty<?php
							echo $id;
							?>' step='0.01' value='<?php
							echo $quantity;
							?>' size='8'></td>
                            <td style="border:1px solid #000000;">&nbsp;</td>
                        </tr>
                        <?php
						}
						$getCat2 = $db->prepare ( "SELECT * FROM $myCategories WHERE subOf = ? ORDER BY category" );
						$getCat2->execute ( array (
								$cId
						) );
						while ( $getC2 = $getCat2->fetch () ) {
							$cId2 = $getC2 ['id'];
							$cCategory2 = $getC2 ['category'];

							echo "<tr><td colspan='6' style='text-align:center; font-weight:bold; border:1px solid black; background-color:#dddddd;'>$cCategory2 sub of $cCategory</td></tr>\n";
							?>
                        <tr>
                            <td style="text-align:left; font-weight:bold;">Name</td>
                            <td style="text-align:center; font-weight:bold;">Cost</td>
                            <td style="text-align:center; font-weight:bold;">Price</td>
                            <td style="text-align:center; font-weight:bold;">Qty</td>
                            <td style="text-align:center; font-weight:bold;" colspan="2">On Hand</td>
                        </tr>
                        <?php
							$getItems2 = $db->prepare ( "SELECT id, time, name, quantity, cost, price, unitOfMeasure FROM $myInventory WHERE categoryId = ? ORDER BY name" );
							$getItems2->execute ( array (
									$cId2
							) );
							while ( $gi2 = $getItems2->fetch () ) {
								$id2 = $gi2 [0];
								$lastChanged2 = $gi2 [1];
								$name2 = $gi2 [2];
								$quantity2 = $gi2 [3];
								$cost2 = $gi2 [4];
								$price2 = $gi2 [5];
								$unit2 = $UOM [$gi2 [6]];

								$totalCost += ($cost2 * $quantity2);
								$totalQty += $quantity2;
								$totalItems ++;
								?>
                            <tr>
                                <td style="text-align:left;"><div style="text-align:left;"><?php
								echo $name2;
								?></div><div style="text-align:right; font-size:.75em;">Last change: <?php
								echo date ( "m-d-Y", $lastChanged2 );
								?></div></td>
                                <td style="text-align:center;"><?php

								echo money ( $cost2, $currency, $langCode ) . " " . $unit2;
								?></td>
                                <td style="text-align:center;">$<?php

								echo money ( $price2, $currency, $langCode );
								?></td>
                                <td style="text-align:center;"><?php

								echo $quantity2 . " " . $unit2;
								?></td>
                                <td style="text-align:center;"><input type='number' name='invQty<?php

								echo $id2;
								?>' step='0.01' value='<?php

								echo $quantity2;
								?>' size='8'></td>
                                <td style="border:1px solid #000000;">&nbsp;</td>
                            </tr>
                            <?php
							}
						}
					}
					?>
                <tr>
                    <td style="text-align:center; font-weight:bold; border:1px solid black; background-color:#dddddd;" colspan="6">Totals</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-align:center; font-weight:bold;">Total Cost</td>
                    <td style="text-align:center; font-weight:bold;">Total Items</td>
                    <td style="text-align:center; font-weight:bold;">Total Qty</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td style="text-align:left; font-weight:bold;">Totals</td>
                    <td style="text-align:center; font-weight:bold;">$<?php

					echo money ( $totalCost, $currency, $langCode );
					?></td>
                    <td style="text-align:center; font-weight:bold;"><?php
					echo $totalItems;
					?></td>
                    <td style="text-align:center; font-weight:bold;"><?php
					echo money ( $totalQty, $currency, $langCode );
					?></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td style="text-align:left; font-weight:bold;"><button onclick='window.history.back()'> Back </button></td>
                    <td style="text-align:right; font-weight:bold;"><a href="index.php?page=home" style="text-decoration:none; color:#000000;">Simple Inv Home</a></td>
                    <td colspan="2"></td>
                    <td style="text-align:right; font-weight:bold;"><button onclick='window.print()'> Print </button></td>
                    <td style="text-align:center; font-weight:bold;"><input type='submit' value=' Save Often '></td>
                </tr>
            </table>
        </form>
    <?php
				}
				?>
</body>
</html>