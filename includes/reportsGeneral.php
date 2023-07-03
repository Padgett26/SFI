<div class="heading">General Journal</div>

<?php
if ($msg != "") {
	echo "<div style='font-weight:bold; text-align:center;'>$msg</div>";
}
if ($je == 0) {
	$gDate = date("Y-m-d", $time);
	$gContactId = 0;
	$gContactName = "";
	$gCCC = 0;
	$gCheckNumber = "";
} else {
	$getG1 = $db->prepare("SELECT date, contact, cashCheckCC, checkNumber FROM $myFLedger WHERE refNumber = ? AND typeCode = ? LIMIT 1");
	$getG1->execute(array(
			$je,
			$type
	));
	$getG1R = $getG1->fetch();
	$gDate = date("Y-m-d", $getG1R['date']);
	$gContactId = $getG1R['contact'];
	$gContactName = getContact($gContactId, $myContacts);
	$gCCC = $getG1R['cashCheckCC'];
	$gCheckNumber = $getG1R['checkNumber'];
}
?>
<div style="">
    <form action='index.php?page=journals&r=general' method='post'>
        <table>
            <tr>
                <td colspan='1'>Date</td>
                <td colspan='2'>Contact</td>
                <td></td>
            </tr>
            <tr>
            <td colspan='1'><input type="date" name="gDate" value="<?php
												echo $gDate;
												?>" min='<?php
												echo $dateMin;
												?>'></td>
            <td colspan='1'><input type='text' name='gContactName' value='<?php
												echo $gContactName;
												?>' placeholder='contact' oninput='getContactSelect("contactSelect", this.value, <?php
												echo $myId;
												?>)'>
            <br />
            <select id='contactSelect' name='gContactNameSelect' size='1'>
            <?php
												echo "<option value='0'";
												echo ($gContactId == 0) ? " selected" : "";
												echo ">Select Contact</option>";
												foreach ($CONTACTS as $k => $v) {
													echo "<option value='$k'";
													echo ($gContactId == $k) ? " selected" : "";
													echo ">$v</option>\n";
												}
												?>
        </select>
    </td>
    <td id='showDebit' style="font-weight:bold; font-size:1.25em;"></td>
    <td id='showCredit' style="font-weight:bold; font-size:1.25em;"></td>
    <td>
    <input type="hidden" name="gTypeCode" value="<?php
				echo ($type == 0) ? 6 : $type;
				?>">
    <input type="hidden" name="je" value="<?php
				echo $je;
				?>">
    <input id="genSubmit" type="submit" value=" Add Entry ">
    </td>
</tr>
<tr>
    <td colspan='1'><select name='gCCC' size='1'>
    <?php
				for ($i = 0; $i < 4; ++ $i) {
					echo "<option value='$i'";
					echo ($i == $gCCC) ? " selected" : "";
					echo ">$PAYTYPES[$i]</option>\n";
				}
				?>
</select></td>
<td><input type='text' name='gCkNm' value='<?php
echo $gCheckNumber;
?>' placeholder='ck num' size='6'></td>
<td colspan='2'></td>
</tr>
<?php
if ($je >= 1) {
	?>
    <tr>
        <td style="font-weight:bold;"></td>
        <td style="font-weight:bold; text-align:right;" colspan='2'>Delete this transaction ->
        <?php
	if ($type == 1 || $type == 2) {
		echo "<br>This will delete the " . $PAYTYPES[$type] . " receipt";
	}
	?>
    </td>
    <td style="font-weight:bold;"><input type="checkbox" name="delG" value="<?php
	echo $je;
	?>"></td>
    <td><input type="submit" value=" Delete "></td>
    </tr>
    <?php
}
?>
<tr>
    <td style="font-weight:bold;">Description</td>
    <td style="font-weight:bold;">Account #</td>
    <td style="font-weight:bold;">Debit</td>
    <td style="font-weight:bold;">Credit</td>
    <td></td>
</tr>
<?php
$i = 1;
if ($je >= 1) {
	$getG2 = $db->prepare("SELECT description, accountNumber, debitAmount, creditAmount FROM $myFLedger WHERE refNumber = ? AND typeCode = ?");
	$getG2->execute(array(
			$je,
			$type
	));
	while ($getG2R = $getG2->fetch()) {
		$gDescription = $getG2R['description'];
		$accNumber = $getG2R['accountNumber'];
		$gDebit = $getG2R['debitAmount'];
		$gCredit = $getG2R['creditAmount'];
		?>
        <tr>
        <td><input type="text" name="gDescription<?php
		echo $i;
		?>" value="<?php
		echo $gDescription;
		?>"></td>
        <td>
        <select name="accNumber<?php
		echo $i;
		?>" size="1">
        <?php

		foreach ($ACCOUNTS as $k => $v) {
			echo "<option value='" . $k . "'";
			echo ($accNumber == $k) ? " selected" : "";
			echo ">" . $k . " - " . $v . "</option>\n";
		}
		?>
    </select>
</td>
<td><input id="D<?php
		echo $i;
		?>" type="number" name="gDebit<?php
		echo $i;
		?>" value="<?php
		echo $gDebit;
		?>" step="0.01" oninput="generalBalanceCheck()"></td>
<td><input id="C<?php
		echo $i;
		?>" type="number" name="gCredit<?php
		echo $i;
		?>" value="<?php
		echo $gCredit;
		?>" step="0.01" oninput="generalBalanceCheck()"></td>
<td></td>
</tr>
<?php
		$i ++;
	}
}
for ($i; $i <= 10; ++ $i) {
	?>
    <tr>
    <td><input type="text" name="gDescription<?php
	echo $i;
	?>" value=""></td>
    <td>
    <select name="accNumber<?php
	echo $i;
	?>" size="1">
    <?php
	foreach ($ACCOUNTS as $k => $v) {
		echo "<option value='" . $k . "'>" . $k . " - " . $v . "</option>\n";
	}
	?>
</select>
</td>
<td><input id="D<?php
	echo $i;
	?>" type="number" name="gDebit<?php
	echo $i;
	?>" value="0.00" step="0.01" oninput="generalBalanceCheck()"></td>
<td><input id="C<?php
	echo $i;
	?>" type="number" name="gCredit<?php
	echo $i;
	?>" value="0.00" step="0.01" oninput="generalBalanceCheck()"></td>
<td></td>
</tr>
<?php
}
?>
</table>
</form>
</div>
