<?php
include "../../globalFunctions.php";

$db = db_sfi();
$time = time();

$getId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$myId = filter_input(INPUT_GET, 'myId', FILTER_SANITIZE_NUMBER_INT);

$myEmployees = $myId . "__employees";
$myEmployeeTracking = $myId . "__employeeTracking";
$myTimeClock = $myId . "__timeClock";
$mySettings = $myId . "__settings";
$myEmployeePayrollHistory = $myId . "__employeePayrollHistory";
$gettz = $db->prepare("SELECT timeZone FROM $mySettings WHERE id = '1'");
$gettz->execute();
$gettzr = $gettz->fetch();
if ($gettzr) {
	date_default_timezone_set($gettzr['timeZone']);
} else {
	date_default_timezone_set("America/Chicago");
}

$getEmployees = $db->prepare("SELECT * FROM $myEmployees WHERE id = ?");
$getEmployees->execute(array(
		$getId
));
$getER = $getEmployees->fetch();
if ($getER) {
	$name = $getER['name'];
	$ssn = $getER['ssn'];
	$hireDate = $getER['hireDate'];
	$terminateDate = $getER['terminateDate'];
	$email = $getER['email'];
	$address = $getER['address'];
	$cityStZip = $getER['cityStZip'];
	$phone = $getER['phone'];
	$w4_1c = $getER['w4_1c'];
	$w4_2c = $getER['w4_2c'];
	$w4_3 = $getER['w4_3'];
	$w4_4a = $getER['w4_4a'];
	$w4_4b = $getER['w4_4b'];
	$w4_4c = $getER['w4_4c'];
	$eHealthIns = $getER['eHealthIns'];
	$cHealthIns = $getER['cHealthIns'];
	$eRetirement401k = $getER['eRetirement401k'];
	$cRetirement401k = $getER['cRetirement401k'];
	$garnishment = $getER['garnishment'];
	$otherWitholding = $getER['otherWitholding'];

	$getPay = $db->prepare("SELECT hourlyPayRate, salaryPayRate FROM $myEmployeeTracking WHERE employeeId = ? ORDER BY date DESC LIMIT 1");
	$getPay->execute(array(
			$getId
	));
	$getPR = $getPay->fetch();
	if ($getPR) {
		$hourlyPayRate = $getPR['hourlyPayRate'];
		$salaryPayRate = $getPR['salaryPayRate'];
	}
}

?>
<form action="index.php?page=settings&r=salesAssociate" method="post">
	<table style="margin: 20px auto; width: 50%;" cellspacing='0px'>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="name">Name</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="name" type='text' name='name'
				value='<?php
				echo $name;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: center; padding: 10px;" colspan='2'>Form W-4
				Information needed for payroll<br>Step 1
			</td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="address">Address</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="address" type='text' name='address'
				value='<?php
				echo $address;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="cityStZip">City, St Zip</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="cityStZip" type='text' name='cityStZip'
				value='<?php
				echo $cityStZip;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="ssn">SSN</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="ssn" type='number' min='0' max='999999999' step='1' name='ssn'
				value='<?php
				echo $ssn;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="w4_1c">Filing type</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="w4_1c" type='radio' name='w4_1c' value='1'
				<?php
				echo ($w4_1c == 1) ? " checked" : "";
				?>> Single<br> <input id="w4_1c" type='radio' name='w4_1c' value='2'
				<?php
				echo ($w4_1c == 2) ? " checked" : "";
				?>> Married filing jointly or Qualifying surviving spouse<br> <input
				id="w4_1c" type='radio' name='w4_1c' value='3'
				<?php
				echo ($w4_1c == 3) ? " checked" : "";
				?>> Married filing separately<br> <input id="w4_1c" type='radio'
				name='w4_1c' value='4'
				<?php
				echo ($w4_1c == 4) ? " checked" : "";
				?>> Head of household</td>
		</tr>
		<tr>
			<td style="text-align: center; padding: 10px;" colspan='2'>Step 2</td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="w4_2c">Working two jobs</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="w4_2c" type='checkbox' value='1' name='w4_2c'
				<?php
				echo ($w4_2c == 1) ? " checked" : "";
				?>></td>
		</tr>
		<tr>
			<td style="text-align: center; padding: 10px;" colspan='2'>Step 3</td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="w4_3">Claim dependent and other credits</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="w4_3" type='number' min='0' step='1' name='w4_3'
				value='<?php
				echo $w4_3;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: center; padding: 10px;" colspan='2'>Step 4</td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="w4_4a">Other income (not from jobs)</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="w4_4a" type='number' min='0' step='1' name='w4_4a'
				value='<?php
				echo $w4_4a;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="w4_4b">Deductions</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="w4_4b" type='number' min='0' step='1' name='w4_4b'
				value='<?php
				echo $w4_4b;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="w4_4c">Extra tax withholding</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="w4_4c" type='number' min='0' step='1' name='w4_4c'
				value='<?php
				echo $w4_4c;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="hireDate">Hire Date</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="hireDate" type='date' name='hireDate'
				value='<?php
				echo date('Y-m-d', $hireDate);
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: center; padding: 10px;" colspan='2'>End of W-4
				Information</td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="eHealthIns">Employee: Health Ins witholding (per pay period)</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="eHealthIns" type='number' min='0.00' step='0.01'
				name='eHealthIns' value='<?php
				echo $eHealthIns;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="cHealthIns">Company: Health Ins witholding (per pay period)</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="cHealthIns" type='number' min='0.00' step='0.01'
				name='cHealthIns' value='<?php
				echo $cHealthIns;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="eRetirement401k">Employee: Retirement / 401k (per pay period)</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="eRetirement401k" type='number' min='0.00' step='0.01'
				name='eRetirement401k'
				value='<?php
				echo $eRetirement401k;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="cRetirement401k">Company: Retirement / 401k (per pay period)</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="cRetirement401k" type='number' min='0.00' step='0.01'
				name='cRetirement401k'
				value='<?php
				echo $cRetirement401k;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="garnishment">Garnishment (per pay period)</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="garnishment" type='number' min='0.00' step='0.01'
				name='garnishment' value='<?php
				echo $garnishment;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="otherWitholding">Other Witholding (per pay period)</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="otherWitholding" type='number' min='0.00' step='0.01'
				name='otherWitholding'
				value='<?php
				echo $otherWitholding;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="terminateDate">Termination Date</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="terminateDate" type='date' name='terminateDate'
				value='<?php
				echo date('Y-m-d', $terminateDate);
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="email">Email</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="email" type='email' name='email'
				value='<?php
				echo $email;
				?>'></td>
		</tr>

		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="phone">Phone Number</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="phone" type='number' min='0' max='9999999999' step='1'
				name='phone' value='<?php
				echo $phone;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="hourlyPayRate">Hourly Pay Rate</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="hourlyPayRate" type='number' min='0.00' step='0.01'
				name='hourlyPayRate' value='<?php
				echo $hourlyPayRate;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="salaryPayRate">Salary Pay Rate (Year)</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="salaryPayRate" type='number' min='0.00' step='0.01'
				name='salaryPayRate' value='<?php
				echo $salaryPayRate;
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="payRateDate">Pay Rate Effective Date</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="payRateDate" type='date' name='payRateDate'
				value='<?php
				echo date('Y-m-d', time());
				?>'></td>
		</tr>
		<tr>
			<td style="text-align: right; padding: 10px; width: 50%;"><label
				for="description">Description of pay rate change</label></td>
			<td style="text-align: left; padding: 10px; width: 50%;"><input
				id="description" type='text' name='description' value=''></td>
		</tr>
		<tr>
			<td style="text-align: center; padding: 10px;" colspan='2'><input
				type='hidden' name='employeeUp'
				value='<?php
				echo $getId;
				?>'>
				<button>Update Employee</button></td>
		</tr>
	</table>
</form>
<div style='font-weight: bold; text-align: center; cursor: pointer;'
	onclick='toggleview("payRateHistory")'>Show Pay Rate History</div>
<div style='display: none;' id='payRateHistory'>
	<table cellspacing='0px' style="width: 50%; margin: 20px auto;">
		<tr>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center;'>Date</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center;'>Hourly
				Pay Rate</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center;'>Salary
				Pay Rate</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center;'>Description</td>
		</tr>
<?php
$getH = $db->prepare("SELECT * FROM $myEmployeeTracking WHERE employeeId = ? ORDER BY date");
$getH->execute(array(
		$getId
));
while ($getHR = $getH->fetch()) {
	echo "<tr><td style='padding:10px; text-align:center;'>" . date("Y-m-d", $getHR['date']) . "</td><td style='padding:10px; text-align:center;'>" . money($getHR['hourlyPayRate']) . "</td><td style='padding:10px; text-align:center;'>" . money($getHR['salaryPayRate']) . "</td><td style='padding:10px; text-align:center;'>" . html_entity_decode($getHR['description'], ENT_QUOTES) . "</td></tr>";
}
?>
</table>
</div>
<div
	style='font-weight: bold; text-align: center; cursor: pointer; margin-top: 20px;'
	onclick='toggleview("bonusHistory")'>Bonuses and other pay</div>
<div style='display: none;' id='bonusHistory'>
	<table cellspacing='0px' style="width: 50%; margin: 20px auto;">
		<tr>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center;'>Date</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center;'>Amount</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center;'>Description</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center;'>Paid</td>
			<td></td>
		</tr>
		<tr>
			<td style='padding: 10px; text-align: center;'><form
					action='index.php?page=settings&r=salesAssociate' method='post'>
					<input type='date' name='bDate'
						value='<?php
						echo date("Y-m-d", $time);
						?>'></td>
			<td style='padding: 10px; text-align: center;'><input type='number'
				name='bAmount' value='0.00' min='0.00' step='0.01'></td>
			<td style='padding: 10px; text-align: center;'><input type='text'
				name='bDescription'></td>
			<td style='padding: 10px; text-align: center;'><input type='hidden'
				name='bonusUp' value='<?php
				echo $getId;
				?>'>
				<button>Submit</button>
				</form></td>
		</tr>
<?php
$getH = $db->prepare("SELECT * FROM $myEmployeeTracking WHERE employeeId = ? AND otherPay > '0.00' ORDER BY date");
$getH->execute(array(
		$getId
));
while ($getHR = $getH->fetch()) {
	echo "<tr><td style='padding:10px; text-align:center;'>" . date("Y-m-d", $getHR['date']) . "</td><td style='padding:10px; text-align:center;'>" . money($getHR['otherPay']) . "</td>";
	echo "<td style='padding:10px; text-align:center;'>" . html_entity_decode($getHR['description'], ENT_QUOTES) . "</td><td style='padding:10px; text-align:center;'>";
	echo ($getHR['otherPayPaid'] == 1) ? "Yes" : "<form action='index.php?page=settings&r=salesAssociate' method='post'><input type='hidden' name='bonusDel' value='" . $getHR['id'] . "'><button>Delete</button></form>";
	echo "</td></tr>";
}
?>
</table>
</div>
<div
	style='font-weight: bold; text-align: center; cursor: pointer; margin-top: 20px;'
	onclick='toggleview("timeClockHistory")'>Time Clock History</div>
<div style='display: none;' id='timeClockHistory'>
	<table cellspacing='0px' style="width: 50%; margin: 20px auto;">
		<tr>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center;'>Clock
				In</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center;'>Clock
				Out</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center;'>Time</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center;'>Paid</td>
		</tr>
<?php
echo "<tr><td style='padding:10px; text-align:center;' colspan='3'></td>";
echo "<td style='padding:10px; text-align:center;'><span style='cursor:pointer;' onclick='toggleview(\"clockNew\")'>Add new entry</span></td></tr>";
echo "<tr>";
echo "<td style='text-align:center;' colspan='4'>";
echo "<div id='clockNew' style='display:none; width:100%; text-align:center; border:1px solid black;'>";
echo "<form action='index.php?page=settings&r=salesAssociate' method='post'>Clock In: ";
echo "<input type='date' name='manDateIn' value='" . date("Y-m-d", $time) . "'> <select name='manHourIn' size='1'>\n";
for ($i = 0; $i <= 23; ++ $i) {
	echo "<option value='$i'>$i</option>\n";
}
echo "</select>:<select name='manMinuteIn' size='1'>\n";
for ($j = 0; $j <= 59; ++ $j) {
	echo "<option value='$j'>$j</option>\n";
}
echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Clock Out: ";
echo "<input type='date' name='manDate' value='" . date("Y-m-d", $time) . "'> <select name='manHour' size='1'>\n";
for ($k = 0; $k <= 23; ++ $k) {
	echo "<option value='$k'>$k</option>\n";
}
echo "</select>:<select name='manMinute' size='1'>\n";
for ($l = 0; $l <= 59; ++ $l) {
	echo "<option value='$l'>$l</option>\n";
}
echo "</select><br><br>";
echo "Is this sick or vacation leave? No <input type='radio' name='sickOrVacation' value='0' checked> / Yes <input type='radio' name='sickOrVacation' value='1'><br><br>";
echo "Report tips&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='number' name='reportedTips' min='0.00' step='0.01' value='0.00'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<input type='hidden' name='timeClock' value='1'>";
echo "<input type='hidden' name='clockOut' value='new'><input type='hidden' name='employeeId' value='$getId'><button>Update Clock</button></form></div></td>\n";
echo "</tr>\n";

$getT = $db->prepare("SELECT * FROM $myTimeClock WHERE employeeId = ? ORDER BY clockIn DESC");
$getT->execute(array(
		$getId
));
while ($getTR = $getT->fetch()) {
	$tId = $getTR['id'];
	$in = $getTR['clockIn'];
	$out = ($getTR['clockOut'] == 0) ? $time : $getTR['clockOut'];
	$sickOrVacation = $getTR['sickOrVacation'];
	$reportedTips = $getTR['reportedTips'];
	$spanTime = ($out > $in) ? ($out - $in) : (time() - $in);
	$d = floor($spanTime / 86400);
	$r = $spanTime % 86400;
	$h = floor($r / 3600);
	$r = $r % 3600;
	$m = floor($r / 60);
	echo "<tr><td style='padding:10px; text-align:center;'>" . date("Y-m-d H:i", $in) . "</td><td style='padding:10px; text-align:center;'>";
	echo ($out > $in) ? date("Y-m-d H:i", $out) : "";
	echo "</td><td style='padding:10px; text-align:center;'>";
	echo ($d >= 1) ? "$d D" : "";
	echo "$h H : $m M</td>";
	echo "<td style='padding:10px; text-align:center;'>";
	echo ($getTR['paid'] == 1) ? "Yes" : "<span style='cursor:pointer;' onclick='toggleview(\"clock$tId\")'>Edit</span>";
	echo "</td></tr>";
	echo "<tr>";
	echo "<td style='text-align:center;' colspan='4'>";
	echo "<div id='clock$tId' style='display:none; width:100%; text-align:center; border:1px solid black;'>";
	echo "<form action='index.php?page=settings&r=salesAssociate' method='post'>Clock In: ";
	echo "<input type='date' name='manDateIn' value='" . date("Y-m-d", $in) . "'> <select name='manHourIn' size='1'>\n";
	for ($i = 0; $i <= 23; ++ $i) {
		echo "<option value='$i'";
		echo ($i == date("H", $in)) ? " selected" : "";
		echo ">$i</option>\n";
	}
	echo "</select>:<select name='manMinuteIn' size='1'>\n";
	for ($j = 0; $j <= 59; ++ $j) {
		echo "<option value='$j'";
		echo ($j == date("i", $in)) ? " selected" : "";
		echo ">$j</option>\n";
	}
	echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Clock Out: ";
	echo "<input type='date' name='manDate' value='" . date("Y-m-d", $out) . "'> <select name='manHour' size='1'>\n";
	for ($k = 0; $k <= 23; ++ $k) {
		echo "<option value='$k'";
		echo ($k == date("H", $out)) ? " selected" : "";
		echo ">$k</option>\n";
	}
	echo "</select>:<select name='manMinute' size='1'>\n";
	for ($l = 0; $l <= 59; ++ $l) {
		echo "<option value='$l'";
		echo ($l == date("i", $out)) ? " selected" : "";
		echo ">$l</option>\n";
	}
	echo "</select><br><br>";
	echo "Is this sick or vacation leave? No <input type='radio' name='sickOrVacation' value='0'";
	echo ($sickOrVacation == 0) ? " checked" : "";
	echo "> / Yes <input type='radio' name='sickOrVacation' value='1'";
	echo ($sickOrVacation == 1) ? " checked" : "";
	echo "><br><br>Report tips&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='number' name='reportedTips' min='0.00' step='0.01' value='$reportedTips'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "Delete Entry? <input type='checkbox' name='delEntry' value='1'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<input type='hidden' name='timeClock' value='1'>";
	echo "<input type='hidden' name='clockOut' value='$tId'><input type='hidden' name='employeeId' value='$getId'><button>Update Clock</button></form></div></td>\n";
	echo "</tr>\n";
}
?>
</table>
</div>
<div
	style='font-weight: bold; text-align: center; cursor: pointer; margin-top: 20px;'
	onclick='toggleview("payrollHistory")'>Payroll history</div>
<div style='display: none;' id='payrollHistory'>
	<table cellspacing='0px' style="margin: 20px auto;">
		<tr>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center; vertical-align: bottom;'>Date</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center; vertical-align: bottom;'>Gross pay</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center; vertical-align: bottom;'>Federal Tax</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center; vertical-align: bottom;'>State Tax</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center; vertical-align: bottom;'>FICA</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center; vertical-align: bottom;'>FUTA</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center; vertical-align: bottom;'>Health Ins</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center; vertical-align: bottom;'>Retirement</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center; vertical-align: bottom;'>Extra Witholding</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center; vertical-align: bottom;'>Garnishment</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center; vertical-align: bottom;'>Other Witholding</td>
			<td
				style='padding: 10px; border-bottom: 1px solid black; text-align: center; vertical-align: bottom;'>Net pay</td>
		</tr>
<?php
$historyStyle1 = "style='padding:10px; text-align:center;'";
$historyStyle2 = "style='padding:10px; text-align:center; border-bottom: 1px solid black;'";

$geteph = $db->prepare("SELECT * FROM $myEmployeePayrollHistory WHERE employeeId = ? ORDER BY payPeriodEnd DESC");
$geteph->execute(array(
		$getId
));
while ($getephR = $geteph->fetch()) {
	$gross = $getephR['netPay'] + $getephR['fedTax_emp'] + $getephR['stateTax_emp'] + $getephR['fica_emp'] + $getephR['healthIns_emp'] + $getephR['retirement_emp'] + $getephR['extraTaxWitholding_emp'] + $getephR['garnishment_emp'] + $getephR['otherWitholding_emp'];
	echo "<tr><td style='padding:28px 10px; text-align:center; border-bottom: 1px solid black; font-weight:bold;' rowspan='2'>" . date("Y-m-d", $getephR['payPeriodEnd']) . "</td>";
	echo "<td $historyStyle1>" . money($gross) . "</td>";
	echo "<td $historyStyle1>" . money($getephR['fedTax_emp']) . "</td>";
	echo "<td $historyStyle1>" . money($getephR['stateTax_emp']) . "</td>";
	echo "<td $historyStyle1>" . money($getephR['fica_emp']) . "</td>";
	echo "<td $historyStyle1>&nbsp;</td>";
	echo "<td $historyStyle1>" . money($getephR['healthIns_emp']) . "</td>";
	echo "<td $historyStyle1>" . money($getephR['retirement_emp']) . "</td>";
	echo "<td $historyStyle1>" . money($getephR['extraTaxWitholding_emp']) . "</td>";
	echo "<td $historyStyle1>" . money($getephR['garnishment_emp']) . "</td>";
	echo "<td $historyStyle1>" . money($getephR['otherWitholding_emp']) . "</td>";
	echo "<td $historyStyle1>" . money($getephR['netPay']) . "</td>";
	echo "</tr>";

	echo "<tr><td $historyStyle2 colspan='2'>Company responsibility</td>";
	echo "<td $historyStyle2>&nbsp;</td>";
	echo "<td $historyStyle2>" . money($getephR['fica_com']) . "</td>";
	echo "<td $historyStyle2>" . money($getephR['futa_com']) . "</td>";
	echo "<td $historyStyle2>" . money($getephR['healthIns_com']) . "</td>";
	echo "<td $historyStyle2>" . money($getephR['retirement_com']) . "</td>";
	echo "<td $historyStyle2>&nbsp;</td>";
	echo "<td $historyStyle2>&nbsp;</td>";
	echo "<td $historyStyle2>&nbsp;</td>";
	echo "<td $historyStyle2>&nbsp;</td>";
	echo "</tr>";
}
?>
</table>
</div>