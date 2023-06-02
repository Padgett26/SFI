<?php
$getAcc = $db->prepare ( "SELECT * FROM $myFAccounts WHERE id = ?" );
$getAcc->execute ( array (
		$an
) );
$getAccR = $getAcc->fetch ();
if ($getAccR) {
	$accNumber = $getAccR ['accountNumber'];
	$accName = html_entity_decode ( $getAccR ['accountName'], ENT_QUOTES );
	$accStartBalance = $getAccR ['startBalance'];
}
?>
<div class="heading"><?php
echo $accName;
?></div>
<table class='table1'>
    <tr>
        <td></td>
        <td></td>
        <td>Date</td>
        <td>Contact</td>
        <td>Description</td>
        <td>Paid With</td>
        <td>Check #</td>
        <td>Account #</td>
        <td>$ Received</td>
        <td>$ Spent</td>
        <td>Balance</td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td><form action='index.php?page=journals&r=journal&an=<?php
								echo $an;
								?>&je=<?php
								echo $je;
								?>' method='post'><input type="hidden" name="jTypeCode" value="5"></td>
        <td><input type="date" name="jDate" value="<?php
								echo (isset ( $_SESSION ['jDate'] )) ? date ( "Y-m-d", $_SESSION ['jDate'] ) : date ( "Y-m-d", $time );
								?>" min='<?php
								echo $dateMin;
								?>'></td>
        <td style=''><input type='text' name='jContactName' value='' placeholder='contact' onchange='getContactSelect("contactSelect", this.value)'><br>
            <select id='contactSelect' name='jContactNameSelect' size='1'>
                <option value='0'>Select Contact</option>
                <?php
																foreach ( $CONTACTS as $k => $v ) {
																	echo "<option value='$k'>$v</option>\n";
																}
																?>
            </select>
        </td>
        <td><input type="text" name="jDescription" value=""></td>
            <td style=''>
                <select name='jccc' size='1'>
                    <option value='0'>Cash / Check / Card</option>
                    <option value='1'>Cash</option>
                    <option value='2'>Check</option>
                    <option value='3'>Charge</option>
                </select>
            </td>
            <td><input type='text' name='jCkNum' value='' placeholder='ck num' size='6'></td>
                <td>
                    <select name="jAccNumber" size="1">
                    <?php
																				foreach ( $ACCOUNTS as $k => $v ) {
																					echo "<option value='" . $k . "'>" . $k . " - " . $v . "</option>\n";
																				}
																				?>
                </select>
            </td>
            <td><input type="number" name="jCredit" value="0.00" step="0.01"></td>
                <td><input type="number" name="jDebit" value="0.00" step="0.01"></td>
                <td><input type="hidden" name="jUp" value="<?php

																echo $an;
																?>"><input type="submit" value=" Add Entry "></form></td>
                <td></td>
            </tr>
            <?php
												$t = 0;
												$s = 1;
												$getL = $db->prepare ( "SELECT * FROM $myFLedger WHERE accountNumber = ? AND date >= ? AND date <= ? ORDER BY date" );
												$getL->execute ( array (
														$accNumber,
														$fiscalYear,
														$dateRangeEnd
												) );
												while ( $getLR = $getL->fetch () ) {
													$id = $getLR ['id'];
													$date = $getLR ['date'];
													$debitAmount = $getLR ['debitAmount'];
													$creditAmount = $getLR ['creditAmount'];
													$refNumber = $getLR ['refNumber'];
													$type = $getLR ['typeCode'];

													$shade = ($s % 2 == 1) ? "#ffffff" : "#eeeeee";
													$s ++;

													if ($date >= $dateRangeStart && $date <= $dateRangeEnd && $t == 0) {
														echo "<tr>\n";
														echo "<td colspan='9'></td>\n";
														echo "<td style='text-align:right;'>Start Balance</td>\n";
														echo "<td style='text-align:right;'>" . money_sfi ( $accStartBalance, $currency, $langCode ) . "</td>\n";
														echo "<td>Reconciled</td>\n";
														echo "</tr>\n";
														$t ++;
													}

													$accStartBalance += $debitAmount;
													$accStartBalance -= $creditAmount;

													if ($date >= $dateRangeStart && $date <= $dateRangeEnd) {
														$getJ = $db->prepare ( "SELECT * FROM $myFLedger WHERE refNumber = ? AND typeCode = ? AND id != ?" );
														$getJ->execute ( array (
																$refNumber,
																$type,
																$id
														) );
														while ( $getJR = $getJ->fetch () ) {
															$xId = $getJR ['id'];
															$xdate = $getJR ['date'];
															$xcontact = html_entity_decode ( getContact ( $getJR ['contact'], $db, $myContacts ), ENT_QUOTES );
															$xdescription = html_entity_decode ( $getJR ['description'], ENT_QUOTES );
															$xcashCheckCC = $PAYTYPES [$getJR ['cashCheckCC']];
															$xcheckNumber = $getJR ['checkNumber'];
															$xdebitAmount = $getJR ['debitAmount'];
															$xcreditAmount = $getJR ['creditAmount'];
															$xrefNumber = $getJR ['refNumber'];
															$xaccountNumber = $getJR ['accountNumber'];
															$xtype = $getJR ['typeCode'];
															$xreconcile = $getJR ['reconcile'];

															echo "<tr style='background-color:$shade;'>\n";
															echo "<td colspan='2'><a href='index.php?page=journals&r=general&je=$xrefNumber&type=$xtype' target='_self'>Edit</a></td>\n";
															echo "<td>" . date ( "Y-m-d", $xdate ) . "</td>\n";
															echo "<td>$xcontact</td>\n";
															echo "<td>$xdescription</td>\n";
															echo "<td>$xcashCheckCC</td>\n";
															echo "<td>$xcheckNumber</td>\n";
															echo "<td>" . $xaccountNumber . " - " . $ACCOUNTS [$xaccountNumber] . "</td>\n";
															echo "<td style='text-align:right;'>";
															echo ($xcreditAmount > 0.00) ? money_sfi ( $xcreditAmount, $currency, $langCode ) : "";
															echo "</td>\n";
															echo "<td style='text-align:right;'>";
															echo ($xdebitAmount > 0.00) ? money_sfi ( $xdebitAmount, $currency, $langCode ) : "";
															echo "</td>\n";
															echo "<td style='text-align:right;'>" . money_sfi ( $accStartBalance, $currency, $langCode ) . "</td>\n";
															echo "<td style='text-align:center;'><input type='checkbox' name='reconciled' value='1'";
															echo ($xreconcile == 1) ? " checked" : "";
															echo " onclick='reconcile($xId)'></td>";
															echo "</tr>\n";
														}
														echo "</div>";
													}
												}
												?>
        </table>
