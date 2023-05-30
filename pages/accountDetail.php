<?php
$accId = (filter_input ( INPUT_GET, 'account', FILTER_SANITIZE_NUMBER_INT )) ? filter_input ( INPUT_GET, 'account', FILTER_SANITIZE_NUMBER_INT ) : 0;
?>
<div style="margin: 20px 0px;">
	<form action="index.php?page=accountDetail&account=<?php
	echo $accId;
	?>
	" method="post">
		Date Range: From: <input type="date" name="dateRangeStart"
			value="<?php

			echo date ( 'Y-m-d', $dateRangeStart );
			?>"> To: <input
			type="date" name="dateRangeEnd"
			value="<?php

			echo date ( 'Y-m-d', $dateRangeEnd );
			?>"> <input
			type="submit" value=" GO ">
	</form>
</div>
<?php

if ($accId != 0) {
	$getAcc = $db->prepare ( "SELECT accountNumber, accountName, budget FROM $myFAccounts WHERE id = ?" );
	$getAcc->execute ( array (
			$accId
	) );
	$ga = $getAcc->fetch ();
	$acc = ($ga) ? $ga ['accountNumber'] : 0;
	$accName = ($ga) ? $ga ['accountName'] : "";
	$budget = ($ga) ? $ga ['budget'] : 0.00;
	setType ( $budget, "float" );

	if ($budget >= 0.01) {
		$bTotals = array ();
		$count = 0;
		switch ($budgetTerm) {
			case 1 :
				$start = date ( "w", $dateRangeStart );
				if ($start >= 2) {
					$s = $dateRangeStart - (86400 * ($start - 1));
				} elseif ($start == 0) {
					$s = $dateRangeStart - (86400 * 6);
				}
				$interval = "+1 week";
				$e = $dateRangeEnd;
				break;
			case 2 :
				$startY = date ( "Y", $dateRangeStart );
				$startM = date ( "m", $dateRangeStart );
				$endY = date ( "Y", $dateRangeEnd );
				$endM = date ( "m", $dateRangeEnd );
				$s = mktime ( 0, 0, 0, $startM, 1, $startY );
				$interval = "+1 month";
				$e = mktime ( 23, 59, 59, $endM + 1, 0, $endY );
				break;
			case 3 :
				$s = $fiscalYear;
				$interval = "+1 month";
				$total = 0.00;
				$e = strtotime ( "+1 year", $s );
				break;
			default :
				$s = $dateRangeStart;
				$interval = "+1 week";
				$e = $dateRangeEnd;
				break;
		}
		for($l = $s; $l <= $e; $l = strtotime ( $interval, $l )) {
			$end = strtotime ( $interval, $l );
			if ($budgetTerm != 3) {
				$total = 0.00;
			}
			$getB = $db->prepare ( "SELECT debitAmount, creditAmount FROM $myFLedger WHERE accountNumber = ? AND date >= ? AND date <= ?" );
			$getB->execute ( array (
					$acc,
					$l,
					$end
			) );
			while ( $gcB = $getB->fetch () ) {
				if ($gcB) {
					if (($acc >= 100 && $acc <= 199.9) || ($acc >= 500 && $acc <= 599.9)) {
						$total += $gcB ['debitAmount'];
						$total -= $gcB ['creditAmount'];
					} else {
						$total -= $gcB ['debitAmount'];
						$total += $gcB ['creditAmount'];
					}
				}
			}
			$getBO = $db->prepare ( "SELECT debitAmount, creditAmount FROM $myFLedgerOld WHERE accountNumber = ? AND date >= ? AND date <= ?" );
			$getBO->execute ( array (
					$acc,
					$l,
					$end
			) );
			while ( $gcBO = $getBO->fetch () ) {
				if ($gcBO) {
					if (($acc >= 100 && $acc <= 199.9) || ($acc >= 500 && $acc <= 599.9)) {
						$total += $gcBO ['debitAmount'];
						$total -= $gcBO ['creditAmount'];
					} else {
						$total -= $gcBO ['debitAmount'];
						$total += $gcBO ['creditAmount'];
					}
				}
			}
			$d = date ( "M", $l );
			$bTotals [$count] [0] = $total;
			$bTotals [$count] [1] = $d;
			$count ++;
		}
		$dataPointsb1 = array ();
		$dataPointsb2 = array ();
		foreach ( $bTotals as $v ) {
			$dataPointsb1 [] = array (
					"y" => $budget,
					"label" => $v [1]
			);
			$dataPointsb2 [] = array (
					"y" => $v [0],
					"label" => $v [1]
			);
		}
	}

	$weekTotals = array ();
	$culTotals = array ();
	$culT = 0.00;
	$yearTotal = 0.00;
	$count = 0;
	for($l = $dateRangeStart; $l <= $dateRangeEnd; $l = $l + 604800) {
		$total = 0.00;
		$end = ($l + 604799);
		$getC = $db->prepare ( "SELECT debitAmount, creditAmount FROM $myFLedger WHERE accountNumber = ? AND date >= ? AND date <= ?" );
		$getC->execute ( array (
				$acc,
				$l,
				$end
		) );
		while ( $gc = $getC->fetch () ) {
			if ($gc) {
				if (($acc >= 100 && $acc <= 199.9) || ($acc >= 500 && $acc <= 599.9)) {
					$total += $gc ['debitAmount'];
					$total -= $gc ['creditAmount'];
					$culT += $gc ['debitAmount'];
					$culT -= $gc ['creditAmount'];
				} else {
					$total -= $gc ['debitAmount'];
					$total += $gc ['creditAmount'];
					$culT -= $gc ['debitAmount'];
					$culT += $gc ['creditAmount'];
				}
			}
		}
		$getCO = $db->prepare ( "SELECT debitAmount, creditAmount FROM $myFLedgerOld WHERE accountNumber = ? AND date >= ? AND date <= ?" );
		$getCO->execute ( array (
				$acc,
				$l,
				$end
		) );
		while ( $gcO = $getCO->fetch () ) {
			if ($gcO) {
				if (($acc >= 100 && $acc <= 199.9) || ($acc >= 500 && $acc <= 599.9)) {
					$total += $gcO ['debitAmount'];
					$total -= $gcO ['creditAmount'];
					$culT += $gcO ['debitAmount'];
					$culT -= $gcO ['creditAmount'];
				} else {
					$total -= $gcO ['debitAmount'];
					$total += $gcO ['creditAmount'];
					$culT -= $gcO ['debitAmount'];
					$culT += $gcO ['creditAmount'];
				}
			}
		}
		$d = date ( "m/d", $l );
		$weekTotals [$count] [0] = $total;
		$weekTotals [$count] [1] = $d;
		$culTotals [$count] [0] = $culT;
		$culTotals [$count] [1] = $d;
		$count ++;
	}
	foreach ( $weekTotals as $v ) {
		$yearTotal += $v [0];
	}
	$avgChange = ($yearTotal / $count);

	$dataPoints1 = array ();
	foreach ( $weekTotals as $v ) {
		$dataPoints1 [] = array (
				"y" => $v [0],
				"label" => $v [1]
		);
	}
	$dataPoints2 = array ();
	foreach ( $culTotals as $v ) {
		$dataPoints2 [] = array (
				"y" => $v [0],
				"label" => $v [1]
		);
	}
}
$ie1Totals = array ();
$count = 0;
for($l = $dateRangeStart; $l <= $dateRangeEnd; $l = $l + 604800) {
	$total = 0.00;
	$end = ($l + 604799);
	$get1 = $db->prepare ( "SELECT debitAmount, creditAmount FROM $myFLedger WHERE accountNumber >= ? AND accountNumber <= ? AND date >= ? AND date <= ?" );
	$get1->execute ( array (
			'400.0',
			'499.9',
			$l,
			$end
	) );
	while ( $gc1 = $get1->fetch () ) {
		if ($gc1) {
			$total -= $gc1 ['debitAmount'];
			$total += $gc1 ['creditAmount'];
		}
	}
	$get1O = $db->prepare ( "SELECT debitAmount, creditAmount FROM $myFLedgerOld WHERE accountNumber >= ? AND accountNumber <= ? AND date >= ? AND date <= ?" );
	$get1O->execute ( array (
			'400.0',
			'499.9',
			$l,
			$end
	) );
	while ( $gc1O = $get1O->fetch () ) {
		if ($gc1O) {
			$total -= $gc1O ['debitAmount'];
			$total += $gc1O ['creditAmount'];
		}
	}
	$d = date ( "m/d", $l );
	$ie1Totals [$count] [0] = $total;
	$ie1Totals [$count] [1] = $d;
	$count ++;
}
$ie2Totals = array ();
$count = 0;
for($l = $dateRangeStart; $l <= $dateRangeEnd; $l = $l + 604800) {
	$total = 0.00;
	$end = ($l + 604799);
	$get2 = $db->prepare ( "SELECT debitAmount, creditAmount FROM $myFLedger WHERE accountNumber >= ? AND accountNumber <= ? AND date >= ? AND date <= ?" );
	$get2->execute ( array (
			'500.0',
			'599.9',
			$l,
			$end
	) );
	while ( $gc2 = $get2->fetch () ) {
		if ($gc2) {
			$total += $gc2 ['debitAmount'];
			$total -= $gc2 ['creditAmount'];
		}
	}
	$get2O = $db->prepare ( "SELECT debitAmount, creditAmount FROM $myFLedgerOld WHERE accountNumber >= ? AND accountNumber <= ? AND date >= ? AND date <= ?" );
	$get2O->execute ( array (
			'500.0',
			'599.9',
			$l,
			$end
	) );
	while ( $gc2O = $get2O->fetch () ) {
		if ($gc2O) {
			$total += $gc2O ['debitAmount'];
			$total -= $gc2O ['creditAmount'];
		}
	}
	$d = date ( "m/d", $l );
	$ie2Totals [$count] [0] = $total;
	$ie2Totals [$count] [1] = $d;
	$count ++;
}
$dataPointsp1 = array ();
foreach ( $ie1Totals as $v ) {
	$dataPointsp1 [] = array (
			"y" => $v [0],
			"label" => $v [1]
	);
}
$dataPointsp2 = array ();
foreach ( $ie2Totals as $v ) {
	$dataPointsp2 [] = array (
			"y" => $v [0],
			"label" => $v [1]
	);
}
if ($accId != 0) {
	if ($budget >= 0.01) {
		echo "<div id='chartContainerb' style='height: 300px; width: 100%; margin:50px 0px;'></div>";
	}
	echo "<div id='chartContainer1' style='height: 300px; width: 100%; margin:50px 0px;'></div>";
	echo "<div id='chartContainer2' style='height: 300px; width: 100%; margin:50px 0px;'></div>";
}
echo "<div id='chartContainerp' style='height: 300px; width: 100%; margin:50px 0px;'></div>";
echo "<script src='https://canvasjs.com/assets/script/canvasjs.min.js'></script>";
?>
	<script>
	window.onload = function () {
	<?php
	if ($accId != 0) {
		?>
				var chart = new CanvasJS.Chart("chartContainer1", {
					title: {
						text: "<?php
		echo $accName . " ( Activity by week )";
		?>"
					},
					axisY: {
						title: ""
					},
					data: [{
						type: "line",
						xValueType: "dateTime",
		xValueFormatString: "mm/dd",
		yValueFormatString: "$#,##0.##",
						dataPoints: <?php
		echo json_encode ( $dataPoints1, JSON_NUMERIC_CHECK );
		?>
				}]
			});
			chart.render();

				var chart = new CanvasJS.Chart("chartContainer2", {
					title: {
						text: "<?php
		echo $accName . " ( Culminative )";
		?>"
					},
					axisY: {
						title: ""
					},
					data: [{
						type: "line",
						xValueType: "dateTime",
		xValueFormatString: "mm/dd",
		yValueFormatString: "$#,##0.##",
						dataPoints: <?php
		echo json_encode ( $dataPoints2, JSON_NUMERIC_CHECK );
		?>
				}]
			});
			chart.render();
			<?php
	}
	?>

			var chart = new CanvasJS.Chart("chartContainerp", {
	animationEnabled: true,
	title:{
		text: "Income and Expense Comparison"
	},
	axisY: {
		prefix: "$"
	},
	legend:{
		cursor: "pointer",
		itemclick: toggleDataSeries
	},
	toolTip: {
		shared: true
	},
	data: [
	{
		type: "area",
		name: "Income",
		showInLegend: "true",
		xValueType: "dateTime",
		xValueFormatString: "mm/dd",
		yValueFormatString: "$#,##0.##",
		dataPoints: <?php
		echo json_encode ( $dataPointsp1 );
		?>
	},
	{
		type: "area",
		name: "Expense",
		showInLegend: "true",
		xValueType: "dateTime",
		xValueFormatString: "mm/dd",
		yValueFormatString: "$#,##0.##",
		dataPoints: <?php
		echo json_encode ( $dataPointsp2 );
		?>
	}
	]
});

chart.render();

<?php
if ($budget >= 0.01) {
	?>

var chart = new CanvasJS.Chart("chartContainerb", {
	animationEnabled: false,
	title:{
		text: "<?php
	echo $accName . " ( Budget ) ";
	?>"
	},
	axisY: {
		prefix: "$"
	},
	legend:{
		cursor: "pointer",
		itemclick: toggleDataSeries
	},
	toolTip: {
		shared: true
	},
	data: [
	{
		type: "area",
		name: "Budgeted",
		showInLegend: "true",
		xValueType: "dateTime",
		xValueFormatString: "MMM",
		yValueFormatString: "$#,##0.##",
		dataPoints: <?php
	echo json_encode ( $dataPointsb1 );
	?>
	},
	{
		type: "area",
		name: "Activity",
		showInLegend: "true",
		xValueType: "dateTime",
		xValueFormatString: "MMM",
		yValueFormatString: "$#,##0.##",
		dataPoints: <?php
	echo json_encode ( $dataPointsb2 );
	?>
	}
	]
});

chart.render();
<?php
}
?>

function toggleDataSeries(e){
	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
		e.dataSeries.visible = false;
	}
	else{
		e.dataSeries.visible = true;
	}
	chart.render();
}
		}
    </script>