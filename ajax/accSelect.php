<?php
include "../../globalFunctions.php";

$db = db_sfi();

$getAcc = filter_input(INPUT_GET, 'getAcc', FILTER_SANITIZE_NUMBER_INT);
$myId = filter_input(INPUT_GET, 'myId', FILTER_SANITIZE_NUMBER_INT);

$myFAccounts = $myId . "__fAccounts";

if ($getAcc >= 1 && $getAcc <= 5) {

    $s = ($getAcc * 100);
    $e = ($getAcc * 100 + 99.9);

    $dontUse = array();
    $getLast = $db->prepare(
            "SELECT accountNumber FROM $myFAccounts WHERE accountNumber >= ? AND accountNumber <= ? ORDER BY accountNumber");
    $getLast->execute(array(
            $s,
            $e
    ));
    while ($glR = $getLast->fetch()) {
        $dontUse[] = $glR['accountNumber'];
    }

    for ($s; $s <= $e; $s = $s + 0.1) {
        $s = number_format($s, 1, ".", "");
        if ($s >= 102.0) {
            echo (in_array($s, $dontUse)) ? "" : "<option value='$s'>$s</option>\n";
        }
    }
} elseif ($getAcc >= 6 && $getAcc <= 9) {
    $dontUse = array();
    $getLast = $db->prepare(
            "SELECT accountNumber FROM $myFAccounts WHERE accountNumber >= ? AND accountNumber <= ? ORDER BY accountNumber");
    $getLast->execute(array(
            '100.0',
            '101.9'
    ));
    while ($glR = $getLast->fetch()) {
        $dontUse[] = $glR['accountNumber'];
    }
    for ($x = 100.0; $x <= 101.9; $x = $x + 0.1) {
        $x = number_format($x, 1, ".", "");
        echo (in_array($x, $dontUse)) ? "" : "<option value='$x'>$x</option>\n";
    }
} elseif ($getAcc >= 10 && $getAcc <= 11) {
    $dontUse = array();
    $getLast = $db->prepare(
            "SELECT accountNumber FROM $myFAccounts WHERE accountNumber >= ? AND accountNumber <= ? ORDER BY accountNumber");
    $getLast->execute(array(
            '210.1',
            '211.9'
    ));
    while ($glR = $getLast->fetch()) {
        $dontUse[] = $glR['accountNumber'];
    }
    for ($x = 210.1; $x <= 211.9; $x = $x + 0.1) {
        $x = number_format($x, 1, ".", "");
        echo (in_array($x, $dontUse)) ? "" : "<option value='$x'>$x</option>\n";
    }
}