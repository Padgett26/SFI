<?php
include "../../globalFunctions.php";

$db = db_sfi();

$getNames = filter_input(INPUT_GET, 'getNames', FILTER_SANITIZE_STRING);
$myId = filter_input(INPUT_GET, 'myId', FILTER_SANITIZE_NUMBER_INT);

$myInventory = $myId . "__inventory";

echo "<option value='0'></option>\n";
$getInvSelect = $db->prepare(
        "SELECT id,name FROM $myInventory WHERE name LIKE '$getNames%' ORDER BY name");
$getInvSelect->execute();
while ($gis = $getInvSelect->fetch()) {
    $gisId = $gis['id'];
    $gisName = html_entity_decode($gis['name'], ENT_QUOTES);

    echo "<option value='$gisId'>$gisName</option>\n";
}