<?php
include "cgi-bin/config.php";
include "cgi-bin/functions.php";
?>
<!DOCTYPE HTML>
<head>
</head>
<body>
    <div id="printArea">
        <?php
								if (filter_input ( INPUT_POST, 'printId', FILTER_SANITIZE_NUMBER_INT )) {
									$transId = filter_input ( INPUT_POST, 'printId', FILTER_SANITIZE_NUMBER_INT );
									$table = filter_input ( INPUT_POST, 'table', FILTER_SANITIZE_STRING );

									$transactions = $db->prepare ( "SELECT * FROM $table WHERE id = ?" );
									$transactions->execute ( array (
											$transId
									) );
									$transRow = $transactions->fetch ();
									if ($transRow) {
										$transTime = $transRow ['time'];
										$cId = $transRow ['contactId'];
										$transItems = $transRow ['items'];
										$transTaxes = $transRow ['taxes'];
										$transShipping = $transRow ['shipping'];
										$transFees = $transRow ['fees'];
										$transDiscount = $transRow ['discountPercent'];
									}
									$title = "";
									$cTable = "";
									if (preg_match ( "/^([1-9][0-9]*)sales$/", $table, $match )) {
										$title = "Sales Receipt";
										$cTable = $match [1] . "contacts";
										$notes = nl2br ( html_entity_decode ( $transRow ['notes'], ENT_QUOTES ) );
									}
									if (preg_match ( "/^([1-9][0-9]*)purchasing$/", $table, $match )) {
										$title = "Purchase Order";
										$cTable = $match [1] . "contacts";
									}

									$getC = $db->prepare ( "SELECT name, address1, address2, phone, email FROM $cTable WHERE id = ?" );
									$getC->execute ( array (
											$cId
									) );
									$getCR = $getC->fetch ();
									$cName = $getCR ['name'];
									$cAddress1 = $getCR ['address1'];
									$cAddress2 = $getCR ['address2'];
									$cPhone = $getCR ['phone'];
									$cEmail = $getCR ['email'];

									echo "<table style='margin:10px;'>\n";
									echo "<tr>\n";
									echo "<td style='text-align:right; padding:5px; border:1px solid #000000;' colspan='4'>";
									echo "<span style='font-weight:bold;'>Receipt # $transId</span>";
									echo "</td>\n";
									echo "</tr>\n";
									echo "<tr>\n";
									echo "<td style='text-align:center; padding:5px; border:1px solid #000000;' colspan='4'>";
									echo "<span style='font-weight:bold; font-size:1.5em;'>$companyName</span><br />";
									echo "<span style='font-size:1em;'>$companyAddress1</span><br />";
									echo "<span style='font-size:1em;'>$companyAddress2</span><br />";
									echo "<span style='font-size:1em;'>$companyPhone $companyEmail</span><br />";
									echo "</td>\n";
									echo "</tr>\n";
									echo "<tr>\n";
									echo "<td style='text-align:center; padding:5px; border:1px solid #000000;' colspan='4'>";
									echo "<span style='font-weight:bold; font-size:1.5em;'>$title</span>";
									echo "</td>\n";
									echo "</tr>\n";
									echo "<tr>\n";
									echo "<td style='width:100px; padding:5px; border:1px solid #000000; text-align:center;'>" . date ( "m / d / Y", $transTime ) . "</td>\n";
									echo "<td style='width:300px; padding:5px; border:1px solid #000000; text-align:center;'>";
									echo "<span style='font-weight:bold; font-size:1.25em;'>$cName</span><br />";
									echo "<span style='font-size:1em;'>$cAddress1</span><br />";
									echo "<span style='font-size:1em;'>$cAddress2</span><br />";
									echo "<span style='font-size:1em;'>$cPhone $cEmail</span><br />";
									echo "</td>\n";
									echo "<td style='width:50px; padding:5px; border:1px solid #000000; text-align:center;'><button onclick='window.history.back()'> Back </button></td>\n";
									echo "<td style='width:50px; padding:5px; border:1px solid #000000; text-align:center;'><button onclick='window.print()'> Print </button></td>\n";
									echo "</tr>\n";
									echo "<tr>\n";
									echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>Qty</td>\n";
									echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>Item Name</td>\n";
									echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>Unit of<br>Measure</td>\n";
									echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>Price</td>\n";
									echo "</tr>\n";
									$total = 0;
									$items = explode ( ";", $transItems );
									foreach ( $items as $val ) {
										$item = explode ( ",", $val );
										$lineQty = $item [0];
										$lineInvId = $item [1];

										$getInvName = $db->prepare ( "SELECT t1.name, t1.price, t2.unitOfMeasure FROM $myInventory AS t1 LEFT JOIN unitsOfMeasure AS t2 ON t1.unitOfMeasure = t2.id WHERE t1.id = ?" );
										$getInvName->execute ( array (
												$lineInvId
										) );
										$ginRow = $getInvName->fetch ();
										$lineName = $ginRow [0];
										$linePrice = $ginRow [1];
										$lineUOM = $ginRow [2];

										$lineCost = ($lineQty * $linePrice);

										$total += $lineCost;

										echo "<tr>\n";
										echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>$lineQty</td>\n";
										echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>$lineName</td>\n";
										echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>$lineUOM</td>\n";
										echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>" . money_sfi ( $lineCost, $currency, $langCode ) . "</td>\n";
										echo "</tr>\n";
									}
									$gTotal = $total - (($transDiscount / 100) * $total) + $transTaxes + $transShipping + $transFees;
									echo "<tr>\n";
									echo "<td colspan='2'></td>\n";
									echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Discount " . $transDiscount . "%</td>\n";
									echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>" . money_sfi ( ($transDiscount / 100) * $total, $currency, $langCode ) . "</td>\n";
									echo "</tr>\n";
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
									echo "<td style='padding:5px; border:1px solid #000000; text-align:right;'>Total</td>\n";
									echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'>" . money_sfi ( $gTotal, $currency, $langCode ) . "</td>\n";
									echo "</tr>\n";
									if (isset ( $notes ) && $notes != "" && $notes != " ") {
										echo "<tr>\n";
										echo "<td colspan='4'><span style='font-weight:bold;'>Notes:</span><br>$notes</td>\n";
										echo "</tr>\n";
									}
									echo "</table>\n";
								}
								?>
    </div>
</body>
</html>