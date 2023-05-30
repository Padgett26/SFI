<table>
<tr>
<td colspan="7">
<div style="text-align:right;">
<form action="incomePrint.php" method="post">
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
Income Statement
</div>
<div style="text-align:center;"><a href='index.php?page=accountDetail&account=0'>View Income and Expense Comparison Chart</a></div>
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
            <td style="text-align:left; font-weight:bold;">Description</td>
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
								$IandEY += ($aCredit - $aDebit);
								if ($aDate >= $dateRangeStart) {
									$bd += $aDebit;
									$bc += $aCredit;
									$tP += ($aCredit - $aDebit);
									$IandEP += ($aCredit - $aDebit);
								}
							} else {
								$tY += ($aDebit - $aCredit);
								$IandEY -= ($aDebit - $aCredit);
								if ($aDate >= $dateRangeStart) {
									$bd += $aDebit;
									$bc += $aCredit;
									$tP += ($aDebit - $aCredit);
									$IandEP -= ($aDebit - $aCredit);
								}
							}
						}
						$gtY += $tY;
						$gtP += $tP;
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
						echo ($bd >= 0.01) ? money ( $bd, $currency, $langCode ) : "";
						?></td>
						<td style="width: 100px; text-align:right; border-top:1px solid #dddddd;"><?php
						echo ($bc >= 0.01) ? money ( $bc, $currency, $langCode ) : "";
						?></td>
                        <td style="width: 100px; text-align:right; border-top:1px solid #dddddd;"><?php
						echo money ( $tP, $currency, $langCode );
						?></td>
						<td style="width: 150px; text-align:right; border-top:1px solid #dddddd;"><?php
						echo money ( $tY, $currency, $langCode );
						?></td>
            </tr>
			<tr>
			<td colspan="7">
			<table style="display:none;" id="show<?php
						echo $aId;
						?>">
						<tr>
						<td style="text-align:center; border-top:1px solid #dddddd; background-color:#eeeeee;" colspan='7'><?php
						echo "<a href='index.php?page=accountDetail&account=$aId' target='_self'>View Account Detail Page</a>";
						?></td>
				</tr>
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
							echo ($bDebit >= 0.01) ? money ( $bDebit, $currency, $langCode ) : "";
							?></td>
						<td style="width: 100px; text-align:right; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php
							echo ($bCredit >= 0.01) ? money ( $bCredit, $currency, $langCode ) : "";
							?></td>
                        <td style="width:100px; text-align:right; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php
							echo "Pay Type - " . $PAYTYPES [$bccc];
							if ($bccc == 2) {
								echo " #" . $bCkNum;
							}
							?></td>
					<td style="width:150px; text-align:right; border-top:1px solid #dddddd; background-color:#eeeeee;"><?php
							echo "<a href='index.php?page=journals&r=general&je=$bRefNum&type=$bTypeCode' target='_self'>View</a>";
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
					echo money ( $gtP, $currency, $langCode );
					?></td>
					<td style="text-align:right; font-weight:bold; border-top:1px solid #dddddd;"><?php
					echo money ( $gtY, $currency, $langCode );
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
						echo money ( $IandEP, $currency, $langCode );
						?></td>
					<td style="text-align:right; font-weight:bold; border-top:1px solid #dddddd;"><?php
						echo money ( $IandEY, $currency, $langCode );
						?></td>
            </tr>

            <?php
					}
				}
				?>
</table>