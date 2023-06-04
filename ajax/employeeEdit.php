<?php
include "../../globalFunctions.php";

$db = db_sfi();

$getId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$myId = filter_input(INPUT_GET, 'myId', FILTER_SANITIZE_NUMBER_INT);

$myEmployees = $myId . "__employees";
$myEmployeeTracking = $myId . "__employeeTracking";

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

    $getSub = $db->prepare(
            "SELECT COUNT(*) FROM users WHERE email = ? AND subOf = ?");
    $getSub->execute(array(
            $email,
            $myId
    ));
    $getSR = $getSub->fetch();
    $access = ($getSR) ? $getSR[0] : 0;
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
<td style="text-align:center; padding:10px;" colspan='2'><label for="siteAccess">SFI site access:</label></td>
</tr>
<tr>
<td style="text-align:right; padding:10px; width:50%;"><input id="siteAccess" type='radio' name='siteAccess' value='0'<?php
echo ($access == 0) ? " checked" : "";
?>></td>
<td style="text-align:left; padding:10px; width:50%;">No access to the SFI site</td>
</tr>
<tr>
<td style="text-align:right; padding:10px; width:50%;"><input id="siteAccess" type='radio' name='siteAccess' value='1'<?php
echo ($access == 1) ? " checked" : "";
?>></td>
<td style="text-align:left; padding:10px; width:50%;">Sales access only would be able to open: Sell, Inv, Contacts, Milage, and Help. There would be no access to your financial information.</td>
</tr>
<tr>
<td style="text-align:right; padding:10px; width:50%;"><label for="pwd">To log in as a sales associate, this employee will need a password.</label></td>
<td style="text-align:left; padding:10px; width:50%;"><input id="pwd" type='password' name='pwd' autocomplete="off"></td>
</tr>
<tr>
<td style="text-align:center; padding:10px;" colspan='2'><input type='hidden' name='employeeUp' value='<?php
echo $getId;
?>'><button>Update Employee</button></td>
</tr>
</table>
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
</table>