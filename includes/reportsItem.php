<?php
$sales = array ();
$i = $taxes = $shipping = $fees = 0;

$transactions = $db->prepare ( "SELECT * FROM $mySales WHERE time >= ? && time <= ? ORDER BY time" );
$transactions->execute ( array (
		$dateRangeStart,
		$dateRangeEnd
) );
while ( $transRow = $transactions->fetch () ) {
	$id = $transRow ['id'];
	$transTime = $transRow ['time'];
	$contact = $transRow ['contactId'];
	$transItems = $transRow ['items'];
	$taxes += $transRow ['taxes'];
	$shipping += $transRow ['shipping'];
	$fees += $transRow ['fees'];
	$finalized = $transRow ['finalized'];
	$paid = $transRow ['paid'];
	$discountPercent = $transRow ['discountPercent'];

	$sales [$i] [4] = $transTime;
	$sales [$i] [5] = $contact;

	$items = explode ( ";", $transItems );

	foreach ( $items as $val ) {
		$item = explode ( ",", $val );
		$lineQty = $item [0];
		$lineId = $item [1];
		$lineCost = $item [2];
		$linePrice = $item [3];

		$sales [$i] [0] = $lineId;
		$sales [$i] [1] = $lineQty;
		$sales [$i] [2] = $lineCost;
		$sales [$i] [3] = $linePrice;

		$i ++;
	}
}
?>
<table id="table1" cellspacing="5px" style="border:1px solid black;">
    <?php
				$product = array ();
				foreach ( $sales as $v3 ) {
					$product [$v3 [0]] = array (
							0,
							0,
							0,
							0
					);
				}
				foreach ( $sales as $v1 ) {
					$lid = $v1 [0];
					$product [$lid] [1] += $v1 [1]; // Qty
					$product [$lid] [2] += $v1 [2]; // Cost
					$product [$lid] [3] += $v1 [3]; // Price
				}
				?>
    <tr style="border:1px solid black; font-weight:bold;">
        <td style="text-align:left;">Product Name</td>
        <td style="text-align:center;">Qty Sold</td>
        <td style="text-align:center;">Total Cost</td>
        <td style="text-align:center;">Total Retail</td>
        <td style="text-align:center;">Avg Mark Up</td>
    </tr>
    <?php
				$Tqty = 0;
				$Tcost = 0;
				$Tprice = 0;

				foreach ( $product as $k2 => $v2 ) {
					$getInv = $db->prepare ( "SELECT name FROM $myInventory WHERE id = ?" );
					$getInv->execute ( array (
							$k2
					) );
					$getInvR = $getInv->fetch ();
					$name = html_entity_decode ( $getInvR ['name'], ENT_QUOTES );

					$Tqty += $v2 [1];
					$Tcost += $v2 [2];
					$Tprice += $v2 [3];
					?>
        <tr style="border:1px solid black;">
            <td style="text-align:left;"><a href="index.php?page=inventory&grabInvId=<?php
					echo $k2;
					?>" target="_self"><?php
					echo $name;
					?></a></td>
            <td style="text-align:right;"><?php
					echo $v2 [1];
					?></td>
            <td style="text-align:right;"><?php
					echo money_sfi ( $v2 [2], $currency, $langCode );
					?></td>
            <td style="text-align:right;"><?php
					echo money_sfi ( $v2 [3], $currency, $langCode );
					?></td>
            <td style="text-align:right;"><?php
					echo money_sfi ( ((($v2 [3] - $v2 [2]) / $v2 [2]) * 100), $currency, $langCode );
					?>%</td>
        </tr>
        <?php
				}
				?>
    <tr style="border:1px solid black; font-weight:bold;">
        <td style="text-align:left;">Total</td>
        <td style="text-align:right;"><?php
								echo $Tqty;
								?></td>
        <td style="text-align:right;"><?php
								echo money_sfi ( $Tcost, $currency, $langCode );
								?></td>
        <td style="text-align:right;"><?php
								echo money_sfi ( $Tprice, $currency, $langCode );
								?></td>
        <td style="text-align:right;"><?php
								echo ($Tcost != 0) ? money_sfi ( ((($Tprice - $Tcost) / $Tcost) * 100), $currency, $langCode ) : "0";
								?>%</td>
    </tr>
</table>