<table>
<tr>
<td colspan="7">
<div style="text-align:right;">
<form action="balancePrint.php" method="post">
<input type="hidden" name="dateRangeStart" value="<?php
echo $dateRangeStart;
?>">
<input type="hidden" name="dateRangeEnd" value="<?php
echo $dateRangeEnd;
?>">
<input type="submit" value=" Print ">
</form>
</div>
</td>
</tr>
<tr>
<td colspan="7">
<div class="heading">
Balance Sheet
</div>
</td>
</tr>
    <?php
    $periodIncome = 0;
    $gtIncome = 0;
    $LandCP = 0;
    $LandCY = 0;

    $j = "400.0";
    $k = "599.9";
    $getA5 = $db->prepare(
            "SELECT date, debitAmount, creditAmount FROM $myFLedger WHERE accountNumber >= ? AND accountNumber <= ? AND date >= ? AND date <= ?");
    $getA5->execute(array(
            $j,
            $k,
            $fiscalYear,
            $dateRangeEnd
    ));
    while ($getAR5 = $getA5->fetch()) {
        $aDate = $getAR5['date'];
        $aDebit = $getAR5['debitAmount'];
        $aCredit = $getAR5['creditAmount'];

        $gtIncome += ($aCredit - $aDebit);
        if ($aDate >= $dateRangeStart) {
            $periodIncome += ($aCredit - $aDebit);
        }
    }

    for ($i = 1; $i <= 3; ++ $i) {
        $j = $i . "00.0";
        $k = $i . "99.9";

        $type = $ACCOUNTTYPES[$i];

        $gtP = 0;
        $gtY = 0;
        ?>
        <tr>
		<td
			style="text-align: left; font-weight: bold; font-size: 1.5em; background-color: #eeeeee;"
			colspan="7"><?php

        echo ucfirst($type);
        ?></td>
	</tr>
	<tr>
		<td style="width: 100px; text-align: left; font-weight: bold;">Account
			#</td>
		<td style="width: 150px; text-align: left; font-weight: bold;">Account
			Name</td>
			<td style="width: 150px; text-align: left; font-weight: bold;">Description</td>
		<td style="width: 100px; text-align: right; font-weight: bold;">Debits</td>
		<td style="width: 100px; text-align: right; font-weight: bold;">Credits</td>
		<td style="width: 100px; text-align: right; font-weight: bold;">Period
			Total</td>
		<td style="width: 150px; text-align: right; font-weight: bold;">Fiscal Year	to Date</td>
	</tr>
        <?php
        $getA2 = $db->prepare(
                "SELECT id, accountNumber, accountName, startBalance FROM $myFAccounts WHERE accountNumber >= ? AND accountNumber <= ? ORDER BY accountNumber");
        $getA2->execute(array(
                $j,
                $k
        ));
        while ($getAR2 = $getA2->fetch()) {
            $aId = $getAR2['id'];
            $aNumber = $getAR2['accountNumber'];
            $aName = html_entity_decode($getAR2['accountName'], ENT_QUOTES);
            $tY = $getAR2['startBalance'];

            $bd = 0;
            $bc = 0;
            $tP = 0;

            if ($aNumber == '380.0') {
                $bd = 0;
                $bc = 0;
                $tP = $periodIncome;
                $tY = $gtIncome;
            } else {
                $getA3 = $db->prepare(
                        "SELECT date, debitAmount, creditAmount FROM $myFLedger WHERE accountNumber = ? AND date >= ? AND date <= ?");
                $getA3->execute(array(
                        $aNumber,
                        $fiscalYear,
                        $dateRangeEnd
                ));
                while ($getAR3 = $getA3->fetch()) {
                    $aDate = $getAR3['date'];
                    $aDebit = $getAR3['debitAmount'];
                    $aCredit = $getAR3['creditAmount'];

                    if ($i == 1) {
                        $tY += ($aDebit - $aCredit);
                        if ($aDate >= $dateRangeStart) {
                            $bd += $aDebit;
                            $bc += $aCredit;
                            $tP += ($aDebit - $aCredit);
                        }
                    } else {
                        $tY += ($aCredit - $aDebit);
                        if ($aDate >= $dateRangeStart) {
                            $bd += $aDebit;
                            $bc += $aCredit;
                            $tP += ($aCredit - $aDebit);
                        }
                    }
                }
            }
            $gtY += $tY;
            $gtP += $tP;
            if ($i == 2 || $i == 3) {
                $LandCP += $tP;
                $LandCY += $tY;
            }
            ?>
            <tr style="cursor: pointer;"
		onclick="toggleview('show<?php
            echo $aId;
            ?>')">
		<td style="text-align: left; border-top:1px solid #dddddd;"><?php
            echo $aNumber;
            ?></td>
		<td style="text-align: left; border-top:1px solid #dddddd;"><?php
            echo $aName;
            ?></td>
            <td style="text-align: left; border-top:1px solid #dddddd;"></td>
		<td style="text-align: right; border-top:1px solid #dddddd;"><?php
            echo ($bd >= 0.01) ? money_sfi($bd, $currency, $langCode) : "";
            ?></td>
		<td style="text-align: right; border-top:1px solid #dddddd;"><?php
            echo ($bc >= 0.01) ? money_sfi($bc, $currency, $langCode) : "";
            ?></td>
		<td style="text-align: right; border-top:1px solid #dddddd;"><?php
            echo money_sfi($tP, $currency, $langCode);
            ?></td>
		<td style="text-align: right; border-top:1px solid #dddddd;"><?php
            echo money_sfi($tY, $currency, $langCode);
            ?></td>
	</tr>
	<tr>
		<td colspan="7">
			<table style="display:none;"
		id="show<?php
            echo $aId;
            ?>">
						<tr>
						<td style="text-align:center; border-top:1px solid #dddddd; background-color:#eeeeee;" colspan='7'><?php
            echo "<a href='index.php?page=accountDetail&account=$aId' target='_self'>View Account Detail Page</a>";
            ?></td>
				</tr>
			<?php
            $getA3 = $db->prepare(
                    "SELECT * FROM $myFLedger WHERE accountNumber = ? AND date >= ? AND date <= ? ORDER BY date");
            $getA3->execute(array(
                    $aNumber,
                    $dateRangeStart,
                    $dateRangeEnd
            ));
            while ($getAR3 = $getA3->fetch()) {
                $date = $getAR3['date'];
                $contact = html_entity_decode(
                        getContact($getAR3['contact'], $myContacts), ENT_QUOTES);
                $description = html_entity_decode($getAR3['description'],
                        ENT_QUOTES);
                $ccc = $getAR3['cashCheckCC'];
                $checkNumber = $getAR3['checkNumber'];
                $debitAmount = $getAR3['debitAmount'];
                $creditAmount = $getAR3['creditAmount'];
                $refNumber = $getAR3['refNumber'];
                $typeCode = $getAR3['typeCode'];
                ?>
				<tr>
					<td style="width:100px; text-align:left; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php
                echo date("Y-m-d", $date);
                ?></td>
					<td style="width:150px; text-align:left; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php
                echo $contact;
                ?></td>
                <td style="width:150px; text-align:left; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php
                echo $description;
                ?></td>
					<td style="width:100px; text-align:right; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php
                echo ($debitAmount >= 0.01) ? money_sfi($debitAmount, $currency,
                        $langCode) : "";
                ?></td>
					<td style="width:100px; text-align:right; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php
                echo ($creditAmount >= 0.01) ? money_sfi($creditAmount,
                        $currency, $langCode) : "";
                ?></td>
					<td style="width:100px; text-align:right; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php
                echo "Pay Type - " . $PAYTYPES[$ccc];
                if ($ccc == 2) {
                    echo " #" . $checkNumber;
                }
                ?></td>
					<td style="width:150px; text-align:right; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php
                echo "<a href='index.php?page=journals&r=general&je=$refNumber&type=$typeCode' target='_self'>View Transaction</a>";
                ?></td>
				</tr>
				<?php
            }
            ?>
			</table>
		</td>
	</tr>
            <?php
        }
        ?>
            <tr>
		<td style="text-align: left;"></td>
		<td style="text-align: left;"></td>
		<td style="text-align: left;"></td>
		<td
			style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;"><?php
        echo ucfirst($type);
        ?></td>
		<td
			style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;">Total</td>
		<td
			style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;"><?php
        echo money_sfi($gtP, $currency, $langCode);
        ?></td>
		<td
			style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;"><?php
        echo money_sfi($gtY, $currency, $langCode);
        ?></td>
	</tr>
            <?php
        if ($i == 3) {
            ?>
            <tr>
		<td style="text-align: left;"></td>
		<td style="text-align: left;"></td>
		<td style="text-align: left;"></td>
		<td
			style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;">Liability
			+ Capital</td>
		<td
			style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;">Total</td>
		<td
			style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;"><?php
            echo money_sfi($LandCP, $currency, $langCode);
            ?></td>
		<td
			style="text-align: right; font-weight: bold; border-top: 1px solid #dddddd;"><?php
            echo money_sfi($LandCY, $currency, $langCode);
            ?></td>
	</tr>
            <?php
        }
    }
    ?>
</table>