<?php
include "cgi-bin/config.php";
include "cgi-bin/functions.php";
?>
<!DOCTYPE HTML>
<head>
    <?php
				include "includes/head.php";
				?>
</head>
<body>
<div style="text-align:left;"><button onclick='window.print()'> Print </button> <button onclick='window.history.back()'> Back </button></div>
    <div id="printArea">
    <?php
				foreach ( $CONTACTS as $k => $v ) {
					$getS = $db->prepare ( "SELECT * FROM $myContacts WHERE id = ?" );
					$getS->execute ( array (
							$k
					) );
					$getSR = $getS->fetch ();
					$sb = $startBalance = $getSR ['startingBalance'];
					$address1 = $getSR ['address1'];
					$address2 = $getSR ['address2'];
					$phone = $getSR ['phone'];
					$email = $getSR ['email'];

					$getPay = $db->prepare ( "SELECT debitAmount, creditAmount FROM $myFLedger WHERE contact = ? AND accountNumber = ?" );
					$getPay->execute ( array (
							$k,
							'210.0'
					) );
					while ( $getPR = $getPay->fetch () ) {
						$sb += ($getPR ['creditAmount'] - $getPR ['debitAmount']);
					}
					if ($sb != 0.00) {
						?>
<table cellspacing="0px">
<tr>
<td colspan="5" style="text-align:center; font-weight:bold; padding:30px 0px;">
<span style="font-size:2.5em;">Account Statement</span>
</td>
</tr>
<tr>
<td colspan="5" style="text-align:left; font-weight:bold; padding:20px;">
<span style="font-size:1.25em;"><?php
						echo $companyName;
						?></span><br />
<span style="font-size:1em;"><?php
						echo $companyAddress1;
						?></span><br />
<span style="font-size:1em;"><?php
						echo $companyAddress2;
						?></span><br />
<span style="font-size:1em;"><?php
						echo $companyPhone;
						?></span><br />
<span style="font-size:1em;"><?php
						echo $companyEmail;
						?></span>
</td>
</tr>
<tr>
<td></td>
<td colspan="2" style="text-align:left; font-weight:bold; padding:30px; border:1px solid #000000;">
<span style="font-size:1.25em;"><?php
						echo $v;
						?></span><br />
<span style="font-size:1em;"><?php
						echo $address1;
						?></span><br />
<span style="font-size:1em;"><?php
						echo $address2;
						?></span><br />
<span style="font-size:1em;"><?php
						echo $phone;
						?></span><br />
<span style="font-size:1em;"><?php
						echo $email;
						?></span>
</td>
<td colspan="2"></td>
</tr>
<tr>
<td colspan="5" style="height:30px;">&nbsp;</td>
</tr>
<tr>
<td style="text-align:center; font-weight:bold; border:2px solid #000000;">
Date
</td>
<td style="text-align:center; font-weight:bold; border:2px solid #000000;">
Transaction Type
</td>
<td style="text-align:center; font-weight:bold; border:2px solid #000000;">
Amount
</td>
<td style="text-align:center; font-weight:bold; border:2px solid #000000;">
Payment
</td>
<td style="text-align:center; font-weight:bold; border:2px solid #000000;">
Balance
</td>
</tr>
<tr>
<td style="text-align:left; font-weight:bold; border-left:2px solid #000000; border-right:1px solid #000000;">
<?php
						echo date ( "Y-m-d", $fiscalYear );
						?>
</td>
<td style="text-align:left; font-weight:bold; border-left:1px solid #000000; border-right:1px solid #000000;">
Start Balance
</td>
<td style="text-align:right; font-weight:bold; border-left:1px solid #000000; border-right:2px solid #000000;" colspan="3">
<?php
						echo money ( $startBalance, $currency, $langCode );
						?>
</td>
</tr>
<?php
						$getX = $db->prepare ( "SELECT * FROM $myFLedger WHERE contact = ? AND ((accountNumber >= ? AND accountNumber <= ?) OR accountNumber = ?) ORDER BY date" );
						$getX->execute ( array (
								$k,
								'101.0',
								'109.9',
								'210.0'
						) );
						while ( $getXR = $getX->fetch () ) {
							$date = $getXR ['date'];
							$description = $getXR ['description'];
							$accountNumber = $getXR ['accountNumber'];
							$debitAmount = $getXR ['debitAmount'];
							$creditAmount = $getXR ['creditAmount'];
							$refNumber = $getXR ['refNumber'];
							$typeCode = $getXR ['typeCode'];
							$balanceDue = $getXR ['balanceDue'];

							if ($creditAmount >= .01 || $debitAmount >= .01) {
								?>
	<tr>
<td style="text-align:left; font-weight:bold; border-left:2px solid #000000; border-right:1px solid #000000;">
<?php
								echo date ( "Y-m-d", $date );
								?>
</td>
<td style="text-align:left; font-weight:bold; border-left:1px solid #000000; border-right:1px solid #000000;">
<?php
								switch ($typeCode) {
									case 1 :
										echo "Sales Inv #" . $refNumber . " " . $description;
										break;
									case 2 :
										echo "Purchasing Inv #" . $refNumber . " " . $description;
										break;
									case 5 :
										echo "Cash Transaction #" . $refNumber . " " . $description;
										break;
									case 6 :
										echo "General Entry #" . $refNumber . " " . $description;
										break;
								}
								?>
</td>
<td style="text-align:right; font-weight:bold; border-left:1px solid #000000; border-right:1px solid #000000;">
<?php
								switch ($accountNumber) {
									case '210.0' :
										// account payable
										echo money ( $creditAmount, $currency, $langCode );
										break;
								}
								?>
</td>
<td style="text-align:right; font-weight:bold; border-left:1px solid #000000; border-right:1px solid #000000;">
<?php
								switch ($accountNumber) {
									case '210.0' :
										// account payable
										echo money ( $debitAmount, $currency, $langCode );
										break;
								}
								?>
</td>
<td style="text-align:right; font-weight:bold; border-left:1px solid #000000; border-right:2px solid #000000;">
<?php
								switch ($accountNumber) {
									case '210.0' :
										// account payable
										$startBalance -= $debitAmount;
										$startBalance += $creditAmount;
										break;
								}
								echo money ( $startBalance, $currency, $langCode );
								?>
</td>
</tr>
	<?php
							}
						}
						?>
						<tr>
<td style="text-align:right; font-weight:bold; border-left:2px solid #000000; border-bottom:2px solid #000000; border-top:2px solid #000000;" colspan="4">
<?php
						echo ($startBalance < 0.00) ? "Amt " . $companyName . " owes " . $v : "Amt " . $v . " owes " . $companyName;
						?>
</td>
<td style="text-align:right; font-weight:bold; border-right:2px solid #000000; border-bottom:2px solid #000000; border-top:2px solid #000000;">
<?php
						echo money ( abs ( $startBalance ), $currency, $langCode );
						?>
</td>
</tr>
</table>
    <footer>
    </footer>
    <?php
					}
				}
				?>
    </div>