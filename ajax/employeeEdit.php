<?php
include "../cgi-bin/config.php";

$getId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

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
            $i
    ));
    $getPR = $getPay->fetch();
    if ($getPR) {
        $hourlyPayRate = $getER['hourlyPayRate'];
        $salaryPayRate = $getER['salaryPayRate'];
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
<form action="index.php?page=settings&r=salesAssociate" method="post">
        <label for="name">Name</label>
        <input id="name" type='text' name='name' value='<?php
        echo $name;
        ?>'>
        <label for="ssn">SSN</label>
        <input id="ssn" type='number' min='0' max='999999999' step='1' name='ssn' value='<?php
        echo $ssn;
        ?>'>
        <label for="hireDate">Hire Date</label>
        <input id="hireDate" type='date' name='hireDate' value='<?php
        echo date('Y-m-d', $hireDate);
        ?>'>
        <label for="terminateDate">Termination Date</label>
        <input id="terminateDate" type='date' name='terminateDate' value='<?php
        echo date('Y-m-d', $terminateDate);
        ?>'>
        <label for="email">Email</label>
        <input id="email" type='email' name='email' value='<?php
        echo $email;
        ?>'>
        <label for="address">Address</label>
        <input id="address" type='text' name='address' value='<?php
        echo $address;
        ?>'>
        <label for="cityStZip">City, St Zip</label>
        <input id="cityStZip" type='text' name='cityStZip' value='<?php
        echo $cityStZip;
        ?>'>
        <label for="phone">Phone Number</label>
        <input id="phone" type='number' min='0' max='9999999999' step='1' name='phone' value='<?php
        echo $phone;
        ?>'>
        <label for="hourlyPayRate">Hourly Pay Rate</label>
        <input id="hourlyPayRate" type='number' min='0.00' step='0.01' name='hourlyPayRate' value='<?php
        echo $hourlyPayRate;
        ?>'>
        <label for="salaryPayRate">Salary Pay Rate</label>
        <input id="salaryPayRate" type='number' min='0.00' step='0.01' name='salaryPayRate' value='<?php
        echo $salaryPayRate;
        ?>'>
        <label for="payRateDate">Pay rate effective Date</label>
        <input id="payRateDate" type='date' name='payRateDate' value='<?php
        echo date('Y-m-d', $time);
        ?>'>
        <label for="description">Description of pay rate change</label>
        <input id="description" type='text' name='description' value=''>
        <label for="siteAccess">SFI site access:</label>
        <input id="siteAccess" type='radio' name='siteAccess' value='0'<?php
        echo ($access == 0) ? " checked" : "";
        ?>> No access to the SFI site
        <input id="siteAccess" type='radio' name='siteAccess' value='1'<?php
        echo ($access == 1) ? " checked" : "";
        ?>> Sales access only would be able to open: Sell, Inv, Contacts, Milage, and Help. There would be no access to your financial information.
        <label for="pwd">To log in as a sales associate, this employee will need a password.</label>
        <input id="pwd" type='password' name='pwd'>
        echo "<input type='hidden' name='employeeUp' value='<?php
        echo $getId;
        ?>'>";
        <button>Add Employee</button>
        </form>