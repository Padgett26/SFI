<?php
include "../../globalFunctions.php";

$db = db_sfi();

$getId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$myId = filter_input(INPUT_GET, 'myId', FILTER_SANITIZE_NUMBER_INT);

$myVehicles = $myId . "__vehicles";
$myEmployees = $myId . "__employees";

echo "<select name='employeeId' size='1'>\n";
$a = $db->prepare("SELECT assignedTo FROM $myVehicles WHERE id = ?");
$a->execute(array(
        $getId
));
$ar = $a->fetch();
if ($ar) {
    $assigned = $ar['assignedTo'];
}
$v = $db->prepare("SELECT id,name FROM $myEmployees ORDER BY name");
$v->execute();
while ($vr = $v->fetch()) {
    $id = $vr['id'];
    $name = $vr['name'];
    echo "<option value='$id'";
    echo ($id == $assigned) ? " selected" : "";
    echo "></option>\n";
}
echo "</select>";