<!--
BUY BOX 2
Form for entering a new purchase transaction
-->
<?php
$SDate = date("Y-m-d", $time);
$SContactId = 0;
$SContactName = "";
$SItems = "";
$STaxes = 0;
$SShipping = 0;
$SFees = 0;
$SPaid = 0;
$SFromAcc = 0;
$ccc = 0;
$ckNum = 0;

$getInv = $db->prepare("SELECT * FROM $myPurchasing WHERE id = ?");
$getInv->execute(array(
		$sellId
));
$gIR = $getInv->fetch();
if ($gIR) {
	$SDate = ($gIR['time'] >= 1) ? date("Y-m-d", $gIR['time']) : date("Y-m-d", $time);
	$SContactId = $gIR['contactId'];
	$SContactName = getContact($SContactId, $myContacts);
	$SItems = ($gIR['items']) ? $gIR['items'] : "";
	$STaxes = $gIR['taxes'];
	$SShipping = $gIR['shipping'];
	$SFees = $gIR['fees'];
	$SPaid = $gIR['paid'];
	$SFromAcc = $gIR['fromAcc'];
}

$getCK = $db->prepare("SELECT cashCheckCC, checkNumber FROM $myFLedger WHERE refNumber = ? AND typeCode = ? LIMIT 1");
$getCK->execute(array(
		$sellId,
		'2'
));
$gotCK = $getCK->fetch();
if ($gotCK) {
	$ccc = $gotCK['cashCheckCC'];
	$ckNum = $gotCK['checkNumber'];
}

$total = 0;

$items = ($SItems != "") ? explode(";", $SItems) : "";

if ($items != "") {
	foreach ($items as $var) {
		$item = explode(",", $var);
		$lineQty = $item[0];
		$linePrice = $item[3];

		$total += $linePrice;
	}
}
echo "<form action='receiptPrint.php' method='post'><input type='hidden' name='printId' value='$sellId'><input type='hidden' name='table' value='$myPurchasing'><input type='submit' value=' Print '></form>\n";
?>
<form action="index.php?page=buy" method="post">

    <table style="border: 1px solid #000000;">
        <?php
								echo "<tr>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='text' name='contactName' value=\"" . $SContactName . "\" oninput='getContactSelect(\"contactSelect\",this.value,$myId)'><br>\n";
								echo "<select id='contactSelect' name='contactNameSelect' size='1'>\n<option value='0'></option>\n";
								foreach ($CONTACTS as $k => $v) {
									echo "<option value='$k'";
									echo ($SContactId == $k) ? " selected" : "";
									echo ">" . $v . "</option>\n";
								}
								echo "</select></td>\n";
								echo "<td style='padding:5px; border:1px solid #000000;'>Date: <input type='date' name='date' value='$SDate' min='$dateMin'></td>\n";
								echo "<td></td>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='hidden' name='sellId' value='X$sellId'><input type='submit' value=' Update '></td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td colspan='2'></td>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Tax</td>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='number' name='taxes' step='.01' placeholder='0.00' min='0.00' value='$STaxes'></td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td colspan='2'></td>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Shipping</td>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='number' name='shipping' step='.01' placeholder='0.00' min='0.00' value='$SShipping'></td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td colspan='2'></td>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Other Fees</td>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='number' name='fees' step='.01' placeholder='0.00' min='0.00' value='$SFees'></td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td></td>\n";
								echo "<td></td>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Total Purchased Items</td>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>" . money_sfi($total, $currency, $langCode) . "</td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td colspan='2'></td>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Total</td>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>" . money_sfi($total + $SShipping + $SFees + $STaxes, $currency, $langCode) . "</td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td style='width:200px; padding:5px; border:1px solid #000000; text-align:center;'>Finalize Purchase Receipt <input type='checkbox' name='done' value='1'><br /><span style='font-size:.75em;'>This will close the purchase receipt and make changes in your Inv</span></td>\n";
								echo "<td style='width:200px; border:1px solid #000000; text-align:center;' colspan='2'>";
								echo "Paid: <input type='checkbox' name='paid' value='1'";
								echo ($SPaid == 1) ? " checked" : "";
								echo "><br />\n";
								echo "<select name='ccc' size='1'>\n";
								echo "<option value='0'";
								echo ($ccc == 0) ? " selected" : "";
								echo ">Cash / Check / Card</option>\n";
								echo "<option value='1'";
								echo ($ccc == 1) ? " selected" : "";
								echo ">Cash</option>\n";
								echo "<option value='2'";
								echo ($ccc == 2) ? " selected" : "";
								echo ">Check</option>\n";
								echo "<option value='3'";
								echo ($ccc == 3) ? " selected" : "";
								echo ">Charge</option>\n";
								echo "</select><br />\n";
								echo "<input type='text' name='ckNum' value='$ckNum' placeholder='ck num' size='6'><br />\n";
								echo "Paid from account:<br />\n";
								echo "<select name='fromAcc' size='1'>\n";
								$banks = $db->prepare("SELECT accountNumber, accountName FROM $myFAccounts WHERE (accountNumber >= ? && accountNumber <= ?) || (accountNumber >= ? && accountNumber <= ?) ORDER BY accountNumber");
								$banks->execute(array(
										'101.0',
										'101.9',
										'210.1',
										'210.9'
								));
								while ($banksR = $banks->fetch()) {
									$num = $banksR['accountNumber'];
									$name = html_entity_decode($banksR['accountName'], ENT_QUOTES);

									echo "<option value='$num'";
									echo ($SFromAcc == $num) ? " selected" : "";
									echo ">$num - $name</option>\n";
								}
								echo "</select>";
								echo "</td>\n";
								echo "<td style='width:100px; padding:5px; border:1px solid #000000; text-align:right;'>";
								echo ($sellId != 0) ? "Delete this Purchase receipt: <input type='checkbox' name='delSale' value='1'>" : "";
								echo "</td>\n";
								echo "</tr>\n";
								?>
        <tr>
            <td colspan="4" style="text-align:center; font-weight:bold;">Purchase Items
        </tr>
        <tr>
            <td style='width:200px; padding:5px; border:1px solid #000000; text-align:left;'>Item Name</td>
            <td style='width:100px; padding:5px; border:1px solid #000000; text-align:center;'>Qty</td>
            <td style='width:100px; padding:5px; border:1px solid #000000; text-align:center;'>Total Cost</td>
            <td style='width:100px; padding:5px; border:1px solid #000000; text-align:center;'></td>
        </tr>
        <?php
								echo "<tr>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='text' name='invName0' value='' onkeyup='getInvSelect(\"invSelect0\",this.value,$myId)'><br>\n";
								echo "<select id='invSelect0' name='invNameSelect0' size='1'>\n<option value='0'></option>\n";
								$getInvSelect = $db->prepare("SELECT id, name FROM $myInventory ORDER BY name");
								$getInvSelect->execute();
								while ($gis = $getInvSelect->fetch()) {
									$gisId = $gis['id'];
									$gisName = html_entity_decode($gis['name'], ENT_QUOTES);
									echo "<option value='$gisId'>$gisName</option>\n";
								}
								echo "</select></td>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='number' name='invQty0' step='.01' min='0.00' value='' size='8'> <select name='invUOM0'>";
								echo selectNewUOM('0');
								echo "</select></td>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='number' name='invCost0' step='.01' min='0.00' value=''></td>\n";
								echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='submit' value=' Add Item '></td>";
								echo "</tr>\n";

								if ($items != "") {
									foreach ($items as $key => $var) {
										$j = ($key + 1);
										$item = explode(",", $var);
										$lineQty = (float) $item[0];
										$lineUOM = $item[1];
										$showUOM = $UOM[$lineUOM];
										$lineInvId = $item[2];
										$linePrice = (float) $item[3];

										$getInvName = $db->prepare("SELECT name FROM $myInventory WHERE id = ?");
										$getInvName->execute(array(
												$lineInvId
										));
										$ginRow = $getInvName->fetch();
										$lineName = html_entity_decode($ginRow[0], ENT_QUOTES);

										echo "<tr>\n";
										echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>$lineName<input type='hidden' name='invNameSelect$j' value='$lineInvId'><br></td>\n";
										echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='number' name='invQty$j' step='.01' min='0.00' value='$lineQty' size='8'> $showUOM<input type='hidden' name='invUOM$j' value='$lineUOM'></td>\n";
										echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='number' name='invCost$j' step='.01' min='0.00' value='$linePrice'></td>\n";
										echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input type='submit' value=' Edit Item '></td>";
										echo "</tr>\n";
									}
								}
								?>
    </table>
</form>