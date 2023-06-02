<!--
SELL BOX 2
Form for entering a new sales transaction
-->
<?php
$total = 0;
$tax = 0;
$discount = 0;
$SDate = date ( "Y-m-d", $time );
$SContactId = 0;
$SItems = "";
$items2 = "";
$SShipping = 0;
$SFees = 0;
$SPaid = 0;
$SDiscountPercent = 0;
$SContactName = "";
$SChargeTax = 1;
$ccc = 0;
$ckNum = 0;
$notes = "";

if ($sellId >= 1) {
	$getInv = $db->prepare ( "SELECT * FROM $mySales WHERE id = ?" );
	$getInv->execute ( array (
			$sellId
	) );
	$gIR = $getInv->fetch ();
	$SDate = ($gIR ['time'] >= 1) ? date ( "Y-m-d", $gIR ['time'] ) : date ( "Y-m-d", $time );
	$SContactId = $gIR ['contactId'];
	$SItems = ($gIR ['items']) ? $gIR ['items'] : "";
	$SShipping = $gIR ['shipping'];
	$SFees = $gIR ['fees'];
	$SPaid = $gIR ['paid'];
	$SDiscountPercent = $gIR ['discountPercent'];
	$ccc = $gIR ['ccc'];
	$ckNum = ($gIR ['ckNum'] >= 1) ? $gIR ['ckNum'] : "";
	$notes = html_entity_decode ( $gIR ['notes'], ENT_QUOTES );

	$getCName = $db->prepare ( "SELECT name, chargeTax FROM $myContacts WHERE id = ?" );
	$getCName->execute ( array (
			$SContactId
	) );
	$getCNRow = $getCName->fetch ();
	$SContactName = html_entity_decode ( $getCNRow ['name'], ENT_QUOTES );
	$SChargeTax = $getCNRow ['chargeTax'];
	$taxRate = ($SChargeTax == 1) ? $taxRate : 0;

	$items2 = ($SItems != "") ? explode ( ";", $SItems ) : "";

	if ($items2 != "") {
		foreach ( $items2 as $var ) {
			$item2 = explode ( ",", $var );
			$lineQty = $item2 [0];
			$lineInvId = $item2 [1];

			$getInvName = $db->prepare ( "SELECT price,taxed FROM $myInventory  WHERE id = ?" );
			$getInvName->execute ( array (
					$lineInvId
			) );
			$ginRow = $getInvName->fetch ();
			$linePrice = $ginRow ['price'];
			$lineTaxed = $ginRow ['taxed'];

			if ($lineInvId != 1 && $SChargeTax == 1 && $lineTaxed == 1) {
				$tax += ((($lineQty * $linePrice) - (($lineQty * $linePrice) * ($SDiscountPercent / 100))) * $taxRate);
			}

			$total += ($lineQty * $linePrice);
			$discount += (($lineQty * $linePrice) * ($SDiscountPercent / 100));
		}
	}
}

if ($debugging == 1) {
	echo "sellId " . $sellId . "<br />SItems " . $SItems . "<br />";
}
echo "<form action='receiptPrint.php' method='post'><input type='hidden' name='printId' value='$sellId'><input type='hidden' name='table' value='$mySales'><input type='submit' value=' Print '></form>";
?>
<form action="index.php?page=sell" method="post">

	<table style="border: 1px solid #000000;">
    <?php
				echo ($sellId != 0) ? "<tr><td colspan='3' style='padding:5px; border:1px solid #000000; text-align:right;'>Delete this Sales receipt: <input type='checkbox' name='delSale' value='1'></td></tr>\n" : "";
				echo "<tr>\n";
				echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='text' placeholder='Customer Name' name='contactName' value=\"" . $SContactName . "\" onchange='getContactSelect(\"contactSelect\",this.value)'><br>\n";
				echo "<select id='contactSelect' name='contactNameSelect' size='1'>\n<option value='0'></option>\n";
				foreach ( $CONTACTS as $k => $v ) {
					echo "<option value='$k'";
					echo ($SContactId == $k) ? " selected" : "";
					echo ">$v</option>\n";
				}
				echo "</select></td>\n";
				echo "<td></td>";
				echo "<td style='padding:5px; border:1px solid #000000;'>Date: <input type='date' name='date' value='$SDate' min='$dateMin'></td>\n";
				echo "</tr>\n";
				?>
				<tr>
			<td colspan="3" style="text-align: center; font-weight: bold;">Sales
				Items</td>
		</tr>
				<?php
				echo "<tr>\n";
				echo "<td style='width:200px; padding:5px; border:1px solid #000000; text-align:left;'>Item Name</td>\n";
				echo "<td style='width:100px; padding:5px; border:1px solid #000000; text-align:center;'>Qty</td>\n";
				echo "<td style='width:100px; padding:5px; border:1px solid #000000; text-align:center;'>Total Price</td>\n";
				echo "</tr>\n";
				$j = 1;
				if ($items2 != "") {
					foreach ( $items2 as $var ) {
						$item2 = explode ( ",", $var );
						$lineQty = $item2 [0];
						settype ( $lineQty, "float" );
						$lineInvId = $item2 [1];

						$getInvName = $db->prepare ( "SELECT name, cost, price, unitOfMeasure, taxed FROM $myInventory WHERE id = ?" );
						$getInvName->execute ( array (
								$lineInvId
						) );
						$ginRow = $getInvName->fetch ();
						$lineName = html_entity_decode ( $ginRow [0], ENT_QUOTES );
						$lineCost = $ginRow [1];
						$linePrice = $ginRow [2];
						$intUOM = $ginRow [3];
						$lineUOM = $UOM [$intUOM];
						$isTaxed = $ginRow [4];
						settype ( $lineCost, "float" );
						settype ( $linePrice, "float" );
						echo "<tr>\n";
						echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>$lineName<input type='hidden' name='invName$j' value='$lineInvId'><input type='hidden' name='invNameSelect$j' value='$lineInvId'></td>\n";
						echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='number' name='invQty$j' step='.01' min='0.00' value='$lineQty' size='8'><br />$lineUOM<input type='hidden' name='invUOM$j' value='$intUOM'></td>\n";
						echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>";
						if ($linePrice >= .01) {
							echo money_sfi ( $lineQty * $linePrice, $currency, $langCode ) . "<input type='hidden' name='invPrice$j' value='$linePrice'>";
						} else {
							echo "Price per $lineUOM:<br /><input type='number' name='invPrice$j' step='.01' min='0.00'>";
						}
						echo ($isTaxed == 0 || $lineInvId == 1) ? " <a href='index.php?page=inventory&grabInvId=$lineInvId' style='text-decoration:none;'><image src='images/noTax.png' style='max-height:20px; max-width:20px;'></a>" : "";
						echo "<input type='hidden' name='invCost$j' value='$lineCost'></td>\n";
						echo "</tr>\n";
						$j ++;
					}
				}
				echo "<tr>\n";
				echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='text' name='invName0' value='' onkeyup='getInvSelect(\"invSelect0\",this.value)'><br>\n";
				echo "<select id='invSelect0' name='invNameSelect0' size='1'>\n<option value='0'></option>\n";
				$getInvSelect = $db->prepare ( "SELECT id, name FROM $myInventory ORDER BY name" );
				$getInvSelect->execute ();
				while ( $gis = $getInvSelect->fetch () ) {
					$gisId = $gis ['id'];
					$gisName = html_entity_decode ( $gis ['name'], ENT_QUOTES );
					echo "<option value='$gisId'>$gisName</option>\n";
				}
				echo "</select></td>\n";
				echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='number' name='invQty0' step='.01' min='0.00' value='' size='8'> <select name='invUOM0' size='1'>";
				echo selectNewUOM ( '0', $db );
				echo "</select></td>\n";
				echo "<td></td>\n";
				echo "</tr><tr>";
				echo "<td colspan='3' style='padding:5px; border:1px solid #000000; text-align:center;'><input type='submit' value=' Update Items '></td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td></td>\n";
				echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Tax</td>\n";
				echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>" . money_sfi ( $tax, $currency, $langCode ) . "<input type='hidden' name='taxes' value='$tax'><br />Tax Exempt? <input type='checkbox' name='taxExempt' value='Y'";
				echo ($SChargeTax == 0) ? " checked" : "";
				echo "></td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td></td>\n";
				echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Shipping</td>\n";
				echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='number' name='shipping' step='.01' placeholder='0.00' min='0.00' value='$SShipping'></td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td></td>\n";
				echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Other Fees</td>\n";
				echo "<td style='padding:5px; border:1px solid #000000; text-align:center; font-size:.75em;'><input type='number' name='fees' step='.01' placeholder='0.00' min='0.00' value='$SFees'></td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td></td>\n";
				echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Total Sales Items</td>\n";
				echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>" . money_sfi ( $total, $currency, $langCode ) . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td></td>\n";
				echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Discount % <select name='discountPercent'>\n";
				for($p = 0; $p <= 100; $p = $p + 5) {
					echo "<option value='$p'";
					echo ($p == $SDiscountPercent) ? " selected" : "";
					echo ">$p</option>\n";
				}
				echo "</select></td>\n";
				echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>(" . money_sfi ( $discount, $currency, $langCode ) . ")<input type='hidden' name='discountAmount' value='$discount'></td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td></td>\n";
				echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Total</td>\n";
				$totalBill = ($total + $tax + $SShipping + $SFees - $discount);
				echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>" . money_sfi ( $totalBill, $currency, $langCode ) . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td colspan='3' style='padding:5px; border:1px solid #000000; text-align:left;'><input type='radio' name='paid' value='0'";
				echo ($SPaid == 0) ? " checked" : "";
				echo "> Charge to account<br />\n";
				echo "<input type='radio' name='paid' value='1'";
				echo ($SPaid == 1 && $ccc == 1) ? " checked" : "";
				echo "> Paid Cash <input type='number' name='cash' value='' min='0.00' step='.01' onkeyup='makeChange(this.value, \"$totalBill\")'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id='change'></span><br />\n";
				echo "<input type='radio' name='paid' value='2'";
				echo ($SPaid == 1 && $ccc == 2) ? " checked" : "";
				echo "> Paid Check <input type='text' name='ckNum' value='$ckNum' placeholder='ck num' size='6'><br />\n";
				echo "<input type='radio' name='paid' value='3'";
				echo ($SPaid == 1 && $ccc == 3) ? " checked" : "";
				echo "> Paid Credit<br />\n";
				echo "</td>\n";
				echo "</tr>\n";
				?>
    <tr>
			<td colspan="3" style="text-align: left; font-weight: bold;">Notes:<br>
				<textarea name="notes" style="width: 100%; height: 40px;"><?php
				echo $notes;
				?></textarea>
			</td>
		</tr>
		<?php
		echo "<tr>";
		echo "<td colspan='2' style='padding:5px; border:1px solid #000000; text-align:center;'>Finalize Sales Receipt <input type='checkbox' name='done' value='1'><br /><span style='font-size:.75em;'>This will close the sales receipt and make changes in your Inv</span></td>\n";
		echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='submit' value=' Update '><input type='hidden' name='sellId' value='X$sellId'></td></tr>\n";
		?>
	</table>
</form>
