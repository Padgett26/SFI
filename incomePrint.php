<?php
include "cgi-bin/config.php";
include "cgi-bin/functions.php";
$dateRangeStart = filter_input ( INPUT_POST, 'dateRangeStart', FILTER_SANITIZE_NUMBER_INT );
$dateRangeEnd = filter_input ( INPUT_POST, 'dateRangeEnd', FILTER_SANITIZE_NUMBER_INT );
?>
<div id="printArea">
<table>
<tr>
<td colspan="7">
<div style="text-align:left; font-weight:bold;">
<?php
echo $companyName . "<br />" . $companyAddress1 . "<br />" . $companyAddress2 . "<br />" . $companyPhone . "<br />" . $companyEmail;
?>
</div>
</td>
</tr>
<tr>
<td colspan="7">
<div style="text-align:center; font-weight:bold; font-size:1.25em;">
Income Statement
</div>
</td>
</tr>
<tr>
<td colspan="7">
<div style="text-align:center; font-weight:bold; font-size:1em;">
Selected date range from <?php
echo date ( "Y-m-d", $dateRangeStart );
?> to <?php
echo date ( "Y-m-d", $dateRangeEnd );
?>
</div>
</td>
</tr>
    <?php
				$IandEP = 0;
				$IandEY = 0;

				for($i = 4; $i <= 5; ++ $i) {
					$j = $i . "00.0";
					$k = $i . "99.9";

					$type = $ACCOUNTTYPES [$i];

					$gtP = 0;
					$gtY = 0;
					?>
        <tr>
            <td style="text-align:left; font-weight:bold; font-size:1.5em; background-color:#eeeeee;" colspan="7"><?php

					echo ucfirst ( $type );
					?></td>
        </tr>
        <tr>
            <td style="text-align:left; font-weight:bold;">Account #</td>
            <td style="text-align:left; font-weight:bold;">Account Name</td>
            <td style="text-align:left; font-weight:bold;"></td>
            <td style="text-align:right; font-weight:bold;">Debits</td>
            <td style="text-align:right; font-weight:bold;">Credits</td>
            <td style="text-align:right; font-weight:bold;">Period Total</td>
            <td style="text-align:right; font-weight:bold;">Fiscal Year to Date</td>
        </tr>
        <?php
					$getA2 = $db->prepare ( "SELECT id, accountNumber, accountName, startBalance FROM $myFAccounts WHERE accountNumber >= ? AND accountNumber <= ? ORDER BY accountNumber" );
					$getA2->execute ( array (
							$j,
							$k
					) );
					while ( $getAR2 = $getA2->fetch () ) {
						$aId = $getAR2 ['id'];
						$aNumber = $getAR2 ['accountNumber'];
						$aName = html_entity_decode ( $getAR2 ['accountName'], ENT_QUOTES );
						$tY = $getAR2 ['startBalance'];

						$bd = 0;
						$bc = 0;
						$tP = 0;

						$getA3 = $db->prepare ( "SELECT date, debitAmount, creditAmount FROM $myFLedger WHERE accountNumber = ? AND date >= ? AND date <= ?" );
						$getA3->execute ( array (
								$aNumber,
								$fiscalYear,
								$dateRangeEnd
						) );
						while ( $getAR3 = $getA3->fetch () ) {
							$aDate = $getAR3 ['date'];
							$aDebit = $getAR3 ['debitAmount'];
							$aCredit = $getAR3 ['creditAmount'];

							if ($i == 4) {
								$tY += ($aCredit - $aDebit);
								if ($aDate >= $dateRangeStart) {
									$bd += $aDebit;
									$bc += $aCredit;
									$tP += ($aCredit - $aDebit);
								}
							} else {
								$tY += ($aDebit - $aCredit);
								if ($aDate >= $dateRangeStart) {
									$bd += $aDebit;
									$bc += $aCredit;
									$tP += ($aDebit - $aCredit);
								}
							}
						}
						$gtY += $tY;
						$gtP += $tP;
						if ($i == 4) {
							$IandEP += $tP;
							$IandEY += $tY;
						} else {
							$IandEP -= $tP;
							$IandEY -= $tY;
						}
						?>
            <tr style="cursor: pointer;" onclick="toggleview('show<?php
						echo $aId;
						?>')">
                <td style="width: 100px; text-align:left; border-top:1px solid #dddddd;"><?php

						echo $aNumber;
						?></td>
                <td style="width: 150px; text-align:left; border-top:1px solid #dddddd;"><?php

						echo $aName;
						?></td>
            <td style="width: 150px; text-align:left; border-top:1px solid #dddddd;"></td>
                <td style="width: 100px; text-align:right; border-top:1px solid #dddddd;"><?php
						echo ($bd >= 0.01) ? money_sfi ( $bd, $currency, $langCode ) : "";
						?></td>
						<td style="width: 100px; text-align:right; border-top:1px solid #dddddd;"><?php
						echo ($bc >= 0.01) ? money_sfi ( $bc, $currency, $langCode ) : "";
						?></td>
                        <td style="width: 100px; text-align:right; border-top:1px solid #dddddd;"><?php
						echo money_sfi ( $tP, $currency, $langCode );
						?></td>
						<td style="width: 150px; text-align:right; border-top:1px solid #dddddd;"><?php
						echo money_sfi ( $tY, $currency, $langCode );
						?></td>
            </tr>
			<tr>
			<td colspan="7">
			<table style="display:none;" id="show<?php
						echo $aId;
						?>">
            <?php
						$getA4 = $db->prepare ( "SELECT * FROM $myFLedger WHERE accountNumber = ? AND date >= ? AND date <= ? ORDER BY date" );
						$getA4->execute ( array (
								$aNumber,
								$dateRangeStart,
								$dateRangeEnd
						) );
						while ( $getAR4 = $getA4->fetch () ) {
							$bDate = date ( "Y-m-d", $getAR4 ['date'] );
							$bContact = html_entity_decode ( getContact ( $getAR4 ['contact'], $db, $myContacts ), ENT_QUOTES );
							$bccc = $getAR4 ['cashCheckCC'];
							$bCkNum = $getAR4 ['checkNumber'];
							$bDescription = html_entity_decode ( $getAR4 ['description'], ENT_QUOTES );
							$bDebit = $getAR4 ['debitAmount'];
							$bCredit = $getAR4 ['creditAmount'];
							$bRefNum = $getAR4 ['refNumber'];
							$bTypeCode = $getAR4 ['typeCode'];

							?>
            <tr>
                <td style="width: 100px; text-align:left; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php

							echo $bDate;
							?></td>
                <td style="width: 150px; text-align:left; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php

							echo $bContact;
							?></td>
            <td style="width: 150px; text-align:left; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php

							echo $bDescription;
							?></td>
                <td style="width: 100px; text-align:right; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php
							echo ($bDebit >= 0.01) ? money_sfi ( $bDebit, $currency, $langCode ) : "";
							?></td>
						<td style="width: 100px; text-align:right; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php
							echo ($bCredit >= 0.01) ? money_sfi ( $bCredit, $currency, $langCode ) : "";
							?></td>
                        <td style="width:100px; text-align:right; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php
							switch ($bccc) {
								case 1 :
									echo "Paid - Cash";
									break;
								case 2 :
									echo "Paid Check #$checkNumber";
									break;
								case 3 :
									echo "Paid - Card";
									break;
								default :
									echo "";
							}
							?></td>
					<td style="width:150px; text-align:right; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php
							switch ($bTypeCode) {
								case 1 :
									echo "<a href='index.php?page=sell&viewId=$bRefNum' target='_self'>View Sale</a>";
									break;
								case 2 :
									echo "<a href='index.php?page=buy&viewId=$bRefNum' target='_self'>View Purchase</a>";
									break;
								case 3 :
									echo "<a href='index.php?page=recipes&recipeId=$bRefNum' target='_self'>View Recipe</a>";
									break;
								case 4 :
									echo "<a href='index.php?page=inventory&grabInvId=$bRefNum' target='_self'>View Inv</a>";
									break;
								case 5 :
									echo "<a href='index.php?page=reports&r=journal&an=$bRefNum' target='_self'>View Transaction</a>";
									break;
								case 6 :
									echo "<a href='index.php?page=reports&r=general&an=$bRefNum' target='_self'>View Transaction</a>";
									break;
							}
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
                <td style="text-align:left;"></td>
                <td style="text-align:left;"></td>
                <td style="text-align:left;"></td>
                <td style="text-align:right; font-weight:bold; border-top:1px solid #dddddd;"><?php
					echo ucfirst ( $type );
					?></td>
						<td style="text-align:right; font-weight:bold; border-top:1px solid #dddddd;">Total</td>
                        <td style="text-align:right; font-weight:bold; border-top:1px solid #dddddd;"><?php
					echo money_sfi ( $gtP, $currency, $langCode );
					?></td>
					<td style="text-align:right; font-weight:bold; border-top:1px solid #dddddd;"><?php
					echo money_sfi ( $gtY, $currency, $langCode );
					?></td>
            </tr>
            <?php
					if ($i == 5) {
						?>
            <tr>
                <td style="text-align:left;"></td>
                <td style="text-align:left;"></td>
                <td style="text-align:left;"></td>
                <td style="text-align:right; font-weight:bold; border-top:1px solid #dddddd;">Income - Expenses</td>
						<td style="text-align:right; font-weight:bold; border-top:1px solid #dddddd;">Total</td>
                        <td style="text-align:right; font-weight:bold; border-top:1px solid #dddddd;"><?php
						echo money_sfi ( $IandEP, $currency, $langCode );
						?></td>
					<td style="text-align:right; font-weight:bold; border-top:1px solid #dddddd;"><?php
						echo money_sfi ( $IandEY, $currency, $langCode );
						?></td>
            </tr>

            <?php
					}
				}
				?>
				<tr>
                    <td style="text-align:left; font-weight:bold;" colspan="4"><button onclick='window.history.back()'> Back </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="index.php?page=home" style="text-decoration:none; color:#000000;">SFaI Home</a></td>
                    <td style="text-align:left; font-weight:bold;" colspan="3"><button onclick='window.print()'> Print </button></td>
                </tr>
</table>
</div>