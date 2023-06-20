<?php
include "../../globalFunctions.php";

$db = db_sfi();

$toggle = filter_input(INPUT_GET, 'toggle', FILTER_SANITIZE_NUMBER_INT);
$myId = filter_input(INPUT_GET, 'myId', FILTER_SANITIZE_NUMBER_INT);

$mySettings = $myId . "__settings";

if ($toggle == 1) {
    $g = $db->prepare("SELECT usePayroll FROM $mySettings");
    $g->execute();
    $gr = $g->fetch();
    if ($gr) {
        $t = ($gr['usePayroll'] == 1) ? 0 : 1;
        $s = $db->prepare("UPDATE $mySettings SET usePayroll = ?");
        $s->execute(array(
                $t
        ));
    }
}