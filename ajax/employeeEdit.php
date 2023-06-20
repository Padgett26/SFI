<?php
include "../../globalFunctions.php";

$db = db_sfi();

$getId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$myId = filter_input(INPUT_GET, 'myId', FILTER_SANITIZE_NUMBER_INT);

$myEmployees = $myId . "__employees";
$myEmployeeTracking = $myId . "__employeeTracking";
$myTimeClock = $myId . "__timeClock";
$mySettings = $myId . "__settings";
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

    $getPay = $db->prepare(
            "SELECT hourlyPayRate, salaryPayRate FROM $myEmployeeTracking WHERE employeeId = ? ORDER BY date DESC LIMIT 1");
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
<table style="margin:20px auto; width:50%;" cellspacing='0px'>
<tr>
<td style="text-align:right; padding:10px; width:50%;"><label for="name">Name</label></td>
<td style="text-align:left; padding:10px; width:50%;"><input id="name" type='text' name='name' value='<?php
echo $name;
?>'></td>
</tr>
<tr>
<td style="text-align:right; padding:10px; width:50%;"><label for="ssn">SSN</label></td>
<td style="text-align:left; padding:10px; width:50%;"><input id="ssn" type='number' min='0' max='999999999' step='1' name='ssn' value='<?php
echo $ssn;
?>'></td>
</tr>
<tr>
<td style="text-align:right; padding:10px; width:50%;"><label for="hireDate">Hire Date</label></td>
<td style="text-align:left; padding:10px; width:50%;"><input id="hireDate" type='date' name='hireDate' value='<?php
echo date('Y-m-d', $hireDate);
?>'></td>
</tr>
<tr>
<td style="text-align:right; padding:10px; width:50%;"><label for="terminateDate">Termination Date</label></td>
<td style="text-align:left; padding:10px; width:50%;"><input id="terminateDate" type='date' name='terminateDate' value='<?php
echo date('Y-m-d', $terminateDate);
?>'></td>
</tr>
<tr>
<td style="text-align:right; padding:10px; width:50%;"><label for="email">Email</label></td>
<td style="text-align:left; padding:10px; width:50%;"><input id="email" type='email' name='email' value='<?php
echo $email;
?>'></td>
</tr>
<tr>
<td style="text-align:right; padding:10px; width:50%;"><label for="address">Address</label></td>
<td style="text-align:left; padding:10px; width:50%;"><input id="address" type='text' name='address' value='<?php
echo $address;
?>'></td>
</tr>
<tr>
<td style="text-align:right; padding:10px; width:50%;"><label for="cityStZip">City, St Zip</label></td>
<td style="text-align:left; padding:10px; width:50%;"><input id="cityStZip" type='text' name='cityStZip' value='<?php
echo $cityStZip;
?>'></td>
</tr>
<tr>
<td style="text-align:right; padding:10px; width:50%;"><label for="phone">Phone Number</label></td>
<td style="text-align:left; padding:10px; width:50%;"><input id="phone" type='number' min='0' max='9999999999' step='1' name='phone' value='<?php
echo $phone;
?>'></td>
</tr>
<tr>
<td style="text-align:right; padding:10px; width:50%;"><label for="hourlyPayRate">Hourly Pay Rate</label></td>
<td style="text-align:left; padding:10px; width:50%;"><input id="hourlyPayRate" type='number' min='0.00' step='0.01' name='hourlyPayRate' value='<?php
echo $hourlyPayRate;
?>'></td>
</tr>
<tr>
<td style="text-align:right; padding:10px; width:50%;"><label for="salaryPayRate">Salary Pay Rate</label></td>
<td style="text-align:left; padding:10px; width:50%;"><input id="salaryPayRate" type='number' min='0.00' step='0.01' name='salaryPayRate' value='<?php
echo $salaryPayRate;
?>'></td>
</tr>
<tr>
<td style="text-align:right; padding:10px; width:50%;"><label for="payRateDate">Pay Rate Effective Date</label></td>
<td style="text-align:left; padding:10px; width:50%;"><input id="payRateDate" type='date' name='payRateDate' value='<?php
echo date('Y-m-d', time());
?>'></td>
</tr>
<tr>
<td style="text-align:right; padding:10px; width:50%;"><label for="description">Description of pay rate change</label></td>
<td style="text-align:left; padding:10px; width:50%;"><input id="description" type='text' name='description' value=''></td>
</tr>
<tr>
<td style="text-align:center; padding:10px;" colspan='2'><input type='hidden' name='employeeUp' value='<?php
echo $getId;
?>'><button>Update Employee</button></td>
</tr>
</table>
<div style='font-weight:bold; text-align:center; cursor:pointer;' onclick='toggleview("payRateHistory")'>Show Pay Rate History</div>
<div style='display:none;' id='payRateHistory'>
<table cellspacing='0px' style="width:50%; margin:20px auto;">
<tr>
<td style='padding:10px; border-bottom:1px solid black; text-align:center;'>Date</td>
<td style='padding:10px; border-bottom:1px solid black; text-align:center;'>Hourly Pay Rate</td>
<td style='padding:10px; border-bottom:1px solid black; text-align:center;'>Salary Pay Rate</td>
<td style='padding:10px; border-bottom:1px solid black; text-align:center;'>Description</td>
</tr>
<?php
$getH = $db->prepare(
        "SELECT * FROM $myEmployeeTracking WHERE employeeId = ? ORDER BY date");
$getH->execute(array(
        $getId
));
while ($getHR = $getH->fetch()) {
    echo "<tr><td style='padding:10px; text-align:center;'>" .
            date("Y-m-d", $getHR['date']) .
            "</td><td style='padding:10px; text-align:center;'>" .
            money($getHR['hourlyPayRate']) .
            "</td><td style='padding:10px; text-align:center;'>" .
            money($getHR['salaryPayRate']) .
            "</td><td style='padding:10px; text-align:center;'>" .
            html_entity_decode($getHR['description'], ENT_QUOTES) . "</td></tr>";
}
?>
</table></div>
<div style='font-weight:bold; text-align:center; cursor:pointer; margin-top:20px;' onclick='toggleview("timeClockHistory")'>Time Clock History</div>
<div style='display:none;' id='timeClockHistory'>
<table cellspacing='0px' style="width:50%; margin:20px auto;">
<tr>
<td style='padding:10px; border-bottom:1px solid black; text-align:center;'>Clock In</td>
<td style='padding:10px; border-bottom:1px solid black; text-align:center;'>Clock Out</td>
<td style='padding:10px; border-bottom:1px solid black; text-align:center;'>Time</td>
<td style='padding:10px; border-bottom:1px solid black; text-align:center;'>Paid</td>
</tr>
<?php
$getT = $db->prepare(
        "SELECT * FROM $myTimeClock WHERE employeeId = ? ORDER BY clockIn DESC");
$getT->execute(array(
        $getId
));
while ($getTR = $getT->fetch()) {
    $in = dateTime2mktime($getTR['clockIn']);
    $out = dateTime2mktime($getTR['clockOut']);
    $spanTime = ($out > $in) ? ($out - $in) : (time() - $in);
    $d = floor($spanTime / 86400);
    $r = $spanTime % 86400;
    $h = floor($r / 3600);
    $r = $r % 3600;
    $m = floor($r / 60);
    echo "<tr><td style='padding:10px; text-align:center;'>" .
            date("Y-m-d H:i", $in) .
            "</td><td style='padding:10px; text-align:center;'>";
    echo ($out > $in) ? date("Y-m-d H:i", $out) : "";
    echo "</td><td style='padding:10px; text-align:center;'>";
    echo ($d >= 1) ? "$d D" : "";
    echo "$h H : $m M</td>";
    echo "<td style='padding:10px; text-align:center;'>";
    echo ($getTR['paid'] == 1) ? "Yes" : "No";
    echo "</td></tr>";
}
?>
</table></div>