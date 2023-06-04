
<div style="text-align:left; font-weight:bold; font-size:1.25em; margin:10px 0px 10px 10px; cursor:pointer;" onclick="toggleview('vehicles')">Milage by Vehicle</div>
<table id='vehicles' style='margin:10px 0px 10px 20px; display:none;'>
<?php
$v = $db->prepare("SELECT id,name,licensePlate FROM $myVehicles ORDER BY name");
$v->execute();
while ($vr = $v->fetch()) {
    $vId = $vr['id'];
    $vName = $vr['name'];
    $vLic = $vr['licensePlate'];
    $count = 0;

    $mc = $db->prepare("SELECT COUNT(*) FROM $myMilage WHERE vehicleId = ?");
    $mc->execute(array(
            $vId
    ));
    $mcr = $mc->fetch();
    if ($mcr) {
        $count = $mcr[0];
    }

    if ($count >= 1) {
        echo "<tr><td style='width:10px;'>&nbsp;</td><td style='font-weight:bold; font-size:1em; padding:10px;' colspan='6'>$vName - Plate# $vLic</td></tr>";
        echo "<tr>";
        echo "<td>&nbsp;</td>";
        echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>Date</td>";
        echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>Driver</td>";
        echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>Milage Start</td>";
        echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>Milage End</td>";
        echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>Distance</td>";
        echo "<td style='width:10px;'></td>";
        echo "</tr>";

        $m = $db->prepare(
                "SELECT * FROM $myMilage WHERE vehicleId = ? ORDER BY usageDate DESC");
        $m->execute(array(
                $vId
        ));
        while ($mr = $m->fetch()) {
            $mId = $mr['id'];
            $mEmployee = $mr['employeeId'];
            $mDate = date("Y-m-d", $mr['usageDate']);
            $mBegin = $mr['milageBegin'];
            $mEnd = $mr['milageEnd'];

            $e = $db->prepare("SELECT name FROM $myEmployees WHERE id = ?");
            $e->execute(array(
                    $mEmployee
            ));
            $er = $e->fetch();
            if ($er) {
                $eEmployee = $er['name'];
            }
            echo "<tr>";
            echo "<td>&nbsp;</td>";
            echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>$mDate</td>";
            echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>$eEmployee</td>";
            echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>$mBegin</td>";
            echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>$mEnd</td>";
            echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>" .
                    ($mEnd - $mBegin) . "</td>";
            echo "<td style='width:10px;'></td>";
            echo "</tr>";
        }
        echo "<tr><td style='font-size:1em; padding:20px;' colspan='7'>&nbsp;</td></tr>";
    }
}
?>
</table>
<div style="text-align:left; font-weight:bold; font-size:1.25em; margin:10px 0px 10px 10px; cursor:pointer;" onclick="toggleview('employees')">Milage by Employee</div>
<table id='employees' style='margin:10px 0px 10px 20px; display:none;'>
<?php
$v = $db->prepare("SELECT id,name FROM $myEmployees ORDER BY name");
$v->execute();
while ($vr = $v->fetch()) {
    $vId = $vr['id'];
    $vName = $vr['name'];
    $count = 0;

    $mc = $db->prepare("SELECT COUNT(*) FROM $myMilage WHERE employeeId = ?");
    $mc->execute(array(
            $vId
    ));
    $mcr = $mc->fetch();
    if ($mcr) {
        $count = $mcr[0];
    }

    if ($count >= 1) {
        echo "<tr><td style='width:10px;'>&nbsp;</td><td style='font-weight:bold; font-size:1em; padding:10px;' colspan='6'>$vName</td></tr>";
        echo "<tr>";
        echo "<td>&nbsp;</td>";
        echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>Date</td>";
        echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>Vehicle</td>";
        echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>Milage Start</td>";
        echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>Milage End</td>";
        echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>Distance</td>";
        echo "<td style='width:10px;'></td>";
        echo "</tr>";

        $m = $db->prepare(
                "SELECT * FROM $myMilage WHERE employeeId = ? ORDER BY usageDate DESC");
        $m->execute(array(
                $vId
        ));
        while ($mr = $m->fetch()) {
            $mId = $mr['id'];
            $mVehicle = $mr['vehicleId'];
            $mDate = date("Y-m-d", $mr['usageDate']);
            $mBegin = $mr['milageBegin'];
            $mEnd = $mr['milageEnd'];

            $e = $db->prepare("SELECT name FROM $myVehicles WHERE id = ?");
            $e->execute(array(
                    $mVehicle
            ));
            $er = $e->fetch();
            if ($er) {
                $eVehicle = $er['name'];
            }
            echo "<tr>";
            echo "<td>&nbsp;</td>";
            echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>$mDate</td>";
            echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>$eVehicle</td>";
            echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>$mBegin</td>";
            echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>$mEnd</td>";
            echo "<td style='text-align:center; padding:10px; border-bottom:1px solid black;'>" .
                    ($mEnd - $mBegin) . "</td>";
            echo "<td style='width:10px;'></td>";
            echo "</tr>";
        }
        echo "<tr><td style='font-size:1em; padding:20px;' colspan='7'>&nbsp;</td></tr>";
    }
}
?>
</table>