<?php
include "../../globalFunctions.php";

$db = db_sfi();

$id = filter_input(INPUT_GET, 'ledgerId', FILTER_SANITIZE_NUMBER_INT);
$myId = filter_input(INPUT_GET, 'myId', FILTER_SANITIZE_NUMBER_INT);

$myFLedger = $myId . "__fLedger";

$getR = $db->prepare("SELECT reconcile FROM $myFLedger WHERE id = ?");
$getR->execute(array(
        $id
));
$getRR = $getR->fetch();
$r = 0;
if ($getRR) {
    $r = $getRR['reconcile'];
}
;
$r = ($r == 1) ? 0 : 1;

$setR = $db->prepare("UPDATE $myFLedger SET reconcile = ? WHERE id = ?");
$setR->execute(array(
        $r,
        $id
));

