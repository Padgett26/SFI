<!--
SELL BOX 1
Displays the sales transactions, displayable by date range
-->
<div style="">
    <form action="index.php?page=sell" method="post">
    Date Range: From: <input type="date" name="dateRangeStart" value="<?php
				echo date ( 'Y-m-d', $dateRangeStart );
				?>"> To: <input type="date" name="dateRangeEnd" value="<?php

				echo date ( 'Y-m-d', $dateRangeEnd )?>"> <input type="submit" value=" GO ">
    </form>
</div>
<table style="border: 1px solid #000000;">
<?php
$shade = 1;
if ($viewId == 0) {
	$transactions = $db->prepare ( "SELECT * FROM $mySales WHERE time >= ? && time <= ? ORDER BY time" );
	$transactions->execute ( array (
			$dateRangeStart,
			$dateRangeEnd
	) );
} else {
	$transactions = $db->prepare ( "SELECT * FROM $mySales WHERE id = ?" );
	$transactions->execute ( array (
			$viewId
	) );
}
while ( $transRow = $transactions->fetch () ) {
	$transId = $transRow ['id'];
	$transTime = $transRow ['time'];
	$transContact = $transRow ['contactId'];
	$cName = html_entity_decode ( getContact ( $transContact, $db, $myContacts ), ENT_QUOTES );
	$transItems = $transRow ['items'];
	$transTaxes = $transRow ['taxes'];
	$transShipping = $transRow ['shipping'];
	$transFees = $transRow ['fees'];
	$finalized = $transRow ['finalized'];
	$paid = $transRow ['paid'];
	$discountPercent = $transRow ['discountPercent'];
	$ccc = $transRow ['ccc'];
	$ckNum = $transRow ['ckNum'];
	$notes = nl2br ( html_entity_decode ( $transRow ['notes'], ENT_QUOTES ) );
	settype ( $transTaxes, "float" );
	settype ( $transShipping, "float" );
	settype ( $transFees, "float" );

	$showTot = ( float ) 0.00;
	$showTots = explode ( ";", $transItems );
	foreach ( $showTots as $val ) {
		$showT = explode ( ",", $val );
		settype ( $showT [0], "float" );
		settype ( $showT [3], "float" );
		$showTot += ($showT [3] * $showT [0]);
	}

	$total = ($transTaxes + $transShipping + $transFees);
	$showTotal = (($showTot - ($showTot * ($discountPercent / 100))) + $total);

	$discount = 0;
	$shaded = ($shade % 2 == 1) ? "" : "background-color:#eeeeee;";
	echo "<tr id='showTrans" . $transId . "' style='display:block; border:1px solid #000000; $shaded'>\n";
	echo "<td style='width:100px; padding:5px; border:1px solid #000000; text-align:center;'>" . date ( "m / d / Y", $transTime ) . "</td>\n";
	echo "<td style='width:300px; padding:5px; border:1px solid #000000; font-weight:bold; text-align:center; font-size:1.25em; position:relative;'><a id='link' href='index.php?page=contacts&contactId=$transContact' target='_blank'>$cName</a>";
	echo "<div id='showPaid" . $transId . "' style='position:absolute; right:5px; bottom:5px; font-size:.5em; font-weight:normal; display:block;'>";
	echo ($paid == 1) ? "Paid" : "";
	echo "</div></td>\n";
	echo "<td style='width:50px; padding:5px; border:1px solid #000000;'>" . money_sfi ( $showTotal, $currency, $langCode ) . "</td>\n";
	echo "<td style='width:50px; padding:5px; border:1px solid #000000; text-align:center;'>";
	if ($finalized == '1') {
		echo "<button onclick='transactionToggle(\"$transId\")'> View </button></td>\n";
	} else {
		echo "<form action-'index.php?page=sell' method='post'><input type='hidden' name='getSellId' value='$transId'><input type='submit' value=' Edit '></form></td>\n";
	}
	echo "</tr>\n";
	echo ($transId == $viewId) ? "<tr id='editTrans" . $transId . "' style='display:block; border:1px solid #000000;'>\n" : "<tr id='editTrans" . $transId . "' style='display:none; border:1px solid #000000;'>\n";
	echo "<td style='' colspan='4'>\n";
	echo "<table style='margin:10px;'>\n";
	echo "<tr>\n";
	echo "<td style='width:100px; padding:5px; border:1px solid #000000; text-align:center;'>" . date ( "m / d / Y", $transTime ) . "</td>\n";
	echo "<td style='width:300px; padding:5px; border:1px solid #000000; font-weight:bold; text-align:center; font-size:1.25em;'><a id='link' href='index.php?page=contacts&contactId=$transContact' target='_self'>$cName</a></td>\n";
	echo "<td style='width:50px; padding:5px; border:1px solid #000000; text-align:center;'><form action='receiptPrint.php' method='post'><input type='hidden' name='printId' value='$transId'><input type='hidden' name='table' value='$mySales'><input type='submit' value=' Print '></form></td>\n";
	echo "<td style='width:50px; padding:5px; border:1px solid #000000; text-align:center;'><button onclick='transactionToggle(\"$transId\")'> Close </button></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>Qty</td>\n";
	echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>Item Name</td>\n";
	echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>Unit of<br>Measure</td>\n";
	echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>Price</td>\n";
	echo "</tr>\n";
	$items = explode ( ";", $transItems );
	foreach ( $items as $val ) {
		$item = explode ( ",", $val );
		$lineQty = ( float ) $item [0];
		$lineInvId = $item [1];
		$linePrice = ( float ) $item [3];

		$getInvName = $db->prepare ( "SELECT name, unitOfMeasure FROM $myInventory WHERE id = ?" );
		$getInvName->execute ( array (
				$lineInvId
		) );
		$ginRow = $getInvName->fetch ();
		if ($ginRow) {
			$lineName = html_entity_decode ( $ginRow [0], ENT_QUOTES );
			$lineUOM = $UOM [$ginRow [1]];
		}

		$lineCost = ($lineQty * $linePrice);

		$discount += ($lineCost * ($discountPercent / 100));

		$total += ($lineCost - ($lineCost * ($discountPercent / 100)));

		echo "<tr>\n";
		echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>$lineQty</td>\n";
		echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>$lineName</td>\n";
		echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>$lineUOM</td>\n";
		echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>" . money_sfi ( $lineCost, $currency, $langCode ) . "</td>\n";
		echo "</tr>\n";
	}
	echo "<tr>\n";
	echo "<td colspan='2'></td>\n";
	echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Tax</td>\n";
	echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>" . money_sfi ( $transTaxes, $currency, $langCode ) . "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan='2'></td>\n";
	echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Shipping</td>\n";
	echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>" . money_sfi ( $transShipping, $currency, $langCode ) . "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan='2'></td>\n";
	echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Other Fees</td>\n";
	echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>" . money_sfi ( $transFees, $currency, $langCode ) . "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan='2'></td>\n";
	echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Discount<br />$discountPercent%</td>\n";
	echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>(" . money_sfi ( $discount, $currency, $langCode ) . ")</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td></td>\n";
	echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>";
	if ($paid == 1) {
		if ($ccc == 1) {
			echo "Paid with cash";
		} elseif ($ccc == 2) {
			echo "Paid with check #" . $ckNum;
		} else {
			echo "Paid with a card";
		}
	} else {
		echo "Not paid";
	}
	echo "</td>\n";
	echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Total</td>\n";
	echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>" . money_sfi ( $total, $currency, $langCode ) . "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	if (isset ( $notes ) && $notes != "" && $notes != " ") {
		echo "<tr>\n";
		echo "<td colspan='4'><span style='font-weight:bold;'>Notes:</span><br>$notes</td>\n";
		echo "</tr>\n";
	}
	echo "</table></td>\n";
	echo "</tr>\n";
	$shade ++;
}
?>
</table>
