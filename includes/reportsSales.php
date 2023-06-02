<div class="heading">Cash Drawer</div>
<?php
$getBal = $db->prepare(
        "SELECT startBalance FROM $myFAccounts WHERE accountNumber = ?");
$getBal->execute(array(
        '101.0'
));
$getBalR = $getBal->fetch();
$startBal = $getBalR['startBalance'];

$newBal = $db->prepare(
        "SELECT debitAmount, creditAmount FROM $myFLedger WHERE accountNumber = '101.0' && dailyConfirm = '1' && date >= ? ORDER BY date");
$newBal->execute(array(
        $fiscalYear
));
while ($newBalR = $newBal->fetch()) {
    $startBal += $newBalR['debitAmount'];
    $startBal -= $newBalR['creditAmount'];
}
?>

<table>
<tr>
<td colspan='5'></td>
<td style='border-bottom: 1px solid #dddddd;'>
Amount
</td>
<td style='border-bottom: 1px solid #dddddd;'>
Start Balance<br />
<?php

echo money_sfi($startBal, $currency, $langCode);
?>
</td>
</tr>

<?php
$totCredit = 0;
$getPur1 = $db->prepare(
        "SELECT creditAmount FROM $myFLedger WHERE accountNumber = '101.0' && creditAmount >= '0.01' && dailyConfirm = '0' ORDER BY date");
$getPur1->execute();
while ($getPur1R = $getPur1->fetch()) {
    $startBal -= $getPur1R['creditAmount'];
    $totCredit += $getPur1R['creditAmount'];
}

?>
<tr style='font-weight:bold;'>
<td style='font-weight: bold; font-size: 1.5em; border-bottom: 1px solid #dddddd;' colspan='5'>Money Spent</td>
<td style='border-bottom: 1px solid #dddddd;text-align:right;'>
<?php
echo money_sfi($totCredit, $currency, $langCode);
?>
</td>
<td style='border-bottom: 1px solid #dddddd; text-align:right;'>
<?php
echo money_sfi($startBal, $currency, $langCode);
?>
</td>
</tr>
<tr>
	<td>Date</td>
	<td>Contact</td>
	<td>Description</td>
	<td>Paid With</td>
	<td>Check Number</td>
	<td>Credit Amount</td>
	<td></td>
	</tr>
<?php
$confirm = array();
$getPur = $db->prepare(
        "SELECT * FROM $myFLedger WHERE accountNumber = '101.0' && creditAmount >= '0.01' && dailyConfirm = '0' ORDER BY date");
$getPur->execute();
while ($getPurR = $getPur->fetch()) {
    $pId = $getPurR['id'];
    $pDate = $getPurR['date'];
    $pContact = html_entity_decode(
            getContact($getPurR['contact'], $myContacts), ENT_QUOTES);
    $pDescription = html_entity_decode($getPurR['description'], ENT_QUOTES);
    $pccc = $PAYTYPES[$getPurR['cashCheckCC']];
    $pCheckNumber = $getPurR['checkNumber'];
    $pCreditAmount = $getPurR['creditAmount'];
    $pRefNumber = $getPurR['refNumber'];
    $pTypeCode = $getPurR['typeCode'];
    $pTypeCodeText = $TYPECODES[$pTypeCode];
    $confirm[] = $pId;
    ?>
	<tr style='background-color:#eeeeee;'>
	<td><?php
    echo date("Y-m-d", $pDate)?></td>
	<td><?php
    echo $pContact;
    ?></td>
	<td><?php
    echo $pDescription;
    ?></td>
	<td><?php
    echo $pccc;
    ?></td>
	<td><?php
    echo $pCheckNumber;
    ?></td>
	<td style='text-align:right;'><?php
    echo money_sfi($pCreditAmount, $currency, $langCode);
    ?></td>
	<td><?php
    if ($pTypeCode == 2 && $pRefNumber >= 1) {
        ?>
		<form action='index.php?page=buy' method='post'>
		<input type='hidden' name='viewId' value='<?php
        echo $pRefNumber;
        ?>'><input type='submit' value=' View '>
		</form>
		<?php
    }
    ?></td>
	</tr>
	<?php
}
$totDebit = 0;
$getSale1 = $db->prepare(
        "SELECT debitAmount FROM $myFLedger WHERE accountNumber = '101.0' && debitAmount >= '0.01' && dailyConfirm = '0' ORDER BY date");
$getSale1->execute();
while ($getSale1R = $getSale1->fetch()) {
    $startBal += $getSale1R['debitAmount'];
    $totDebit += $getSale1R['debitAmount'];
}

?>
<tr>
<td colspan='7' style='height:30px;'>&nbsp;</td>
</tr>
<tr style='font-weight:bold;'>
<td style='font-weight: bold; font-size: 1.5em; border-bottom: 1px solid #dddddd;' colspan='5'>Money Taken In</td>
<td style='border-bottom: 1px solid #dddddd; text-align:right;'>
<?php
echo money_sfi($totDebit, $currency, $langCode);
?>
</td>
<td style='border-bottom: 1px solid #dddddd; text-align:right;'>
<?php
echo money_sfi($startBal, $currency, $langCode);
?>
</td>
</tr>
<tr>
	<td>Date</td>
	<td>Contact</td>
	<td>Description</td>
	<td>Paid With</td>
	<td>Check Number</td>
	<td>Debit Amount</td>
	<td></td>
	</tr>
<?php
$cash = 0;
$check = 0;
$cc = 0;
$getPur = $db->prepare(
        "SELECT * FROM $myFLedger WHERE accountNumber = '101.0' && debitAmount >= '0.01' && dailyConfirm = '0' ORDER BY date");
$getPur->execute();
while ($getPurR = $getPur->fetch()) {
    $pId = $getPurR['id'];
    $pDate = $getPurR['date'];
    $pContact = html_entity_decode(
            getContact($getPurR['contact'], $myContacts), ENT_QUOTES);
    $pDescription = html_entity_decode($getPurR['description'], ENT_QUOTES);
    $pc = $getPurR['cashCheckCC'];
    $pccc = $PAYTYPES[$pc];
    $pCheckNumber = $getPurR['checkNumber'];
    $pDebitAmount = $getPurR['debitAmount'];
    $pRefNumber = $getPurR['refNumber'];
    $pTypeCode = $getPurR['typeCode'];
    $pTypeCodeText = $TYPECODES[$pTypeCode];

    $confirm[] = $pId;

    if ($pc == 1) {
        $cash += $pDebitAmount;
    }
    if ($pc == 2) {
        $check += $pDebitAmount;
    }
    if ($pc == 3) {
        $cc += $pDebitAmount;
    }
    ?>
	<tr style='background-color:#eeeeee;'>
	<td><?php
    echo date("Y-m-d", $pDate)?></td>
	<td><?php
    echo $pContact;
    ?></td>
	<td><?php
    echo $pDescription;
    ?></td>
	<td><?php
    echo $pccc;
    ?></td>
	<td><?php
    echo ($pCheckNumber != 0) ? $pCheckNumber : "";
    ?></td>
	<td style='text-align:right;'><?php
    echo money_sfi($pDebitAmount, $currency, $langCode);
    ?></td>
	<td><?php
    if ($pTypeCode == 1 && $pRefNumber >= 1) {
        ?>
		<form action='index.php?page=sell' method='post'>
		<input type='hidden' name='viewId' value='<?php
        echo $pRefNumber;
        ?>'><input type='submit' value=' View '>
		</form>
		<?php
    }
    ?></td>
	</tr>
	<?php
}
$close = implode(",", $confirm);
?>
<tr>
<td colspan='5'></td>
<td style="border-top:1px solid #dddddd; font-weight:bold;">Cash received</td>
<td style="border-top:1px solid #dddddd; font-weight:bold; text-align:right;"><?php
echo money_sfi($cash, $currency, $langCode);
?></td>
</tr>
<tr>
<td colspan='5'></td>
<td style="font-weight:bold;">Checks Received</td>
<td style="font-weight:bold; text-align:right;"><?php
echo money_sfi($check, $currency, $langCode);
?></td>
</tr>
<tr>
<td colspan='5'></td>
<td style="font-weight:bold;">CC Received</td>
<td style="font-weight:bold; text-align:right;"><?php
echo money_sfi($cc, $currency, $langCode);
?></td>
</tr>
<tr>
<td colspan='7' style='height:40px;'>&nbsp;</td>
</tr>
<tr>
<td colspan='5'></td>
<td style='font-weight:bold'>Deposit Into</td>
<td style='font-weight:bold'>Amount</td>
</tr>
<form action='index.php?page=reports' method='post'>
<?php
$getA = $db->prepare(
        "SELECT id, accountName FROM $myFAccounts WHERE accountNumber >= '100.0' && accountNumber <= '101.9' AND accountNumber != '101.0' ORDER BY accountNumber");
$getA->execute();
while ($getAR = $getA->fetch()) {
    $idAcc = $getAR['id'];
    $nameAcc = html_entity_decode($getAR['accountName'], ENT_QUOTES);
    ?>
<tr>
<td colspan='5'></td>
<td><?php
    echo $nameAcc;
    ?></td>
<td><input type='number' name='amt<?php
    echo $idAcc;
    ?>' value='0.00' step='0.01'></td>
</tr>
<?php
}
?>
<tr>
<td colspan='5'></td>
<td>Record Deposits<br />
and Close Tickets</td>
<td><input type='submit' value=' Close '><input type='hidden' name='closeDay' value='1'><input type='hidden' name='confirm' value='<?php
echo $close;
?>'></td>
</tr>
</form>
</table>