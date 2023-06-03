<?php
include "../../globalFunctions.php";

$db = db_sfi();

$table = filter_input(INPUT_GET, 'table', FILTER_SANITIZE_STRING);
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$update1 = $db->prepare("SELECT paid FROM $table WHERE id = ?");
$update1->execute(array(
        $id
));
$up1Row = $update1->fetch();
$paid = $up1Row['paid'];

$val = ($paid == 1) ? 0 : 1;

$update2 = $db->prepare("UPDATE $table SET paid = ? WHERE id = ?");
$update2->execute(array(
        $val,
        $id
));

echo ($paid == 1) ? "" : "Paid";