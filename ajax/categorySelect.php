<?php
include "../../globalFunctions.php";

$db = db_sfi();

$getNames = filter_input(INPUT_GET, 'getNames', FILTER_SANITIZE_STRING);
$myId = filter_input(INPUT_GET, 'myId', FILTER_SANITIZE_NUMBER_INT);

$myCategories = $myId . "__categories";

echo "<option value='0'></option>\n";
$getCatSelect = $db->prepare(
        "SELECT * FROM $myCategories WHERE name LIKE '%$getNames%' ORDER BY name");
$getCatSelect->execute();
while ($gcs = $getCatSelect->fetch()) {
    $gcsId = $gcs['id'];
    $gcsName = html_entity_decode($gcs['category'], ENT_QUOTES);
    $gcsSub = $gcs['subOf'];
    if ($gcsSub != "0") {
        $getP = $db->prepare("SELECT * FROM $myCategories WHERE id = ?");
        $getP->execute(array(
                $gcsSub
        ));
        $getPRow = $getP->fetch();
        $pId = $getPRow['id'];
        $pName = html_entity_decode($getPRow['category'], ENT_QUOTES);
        echo "<option value='$pId'>$pName</option>\n";
    }
    echo ($gcsSub != "0") ? "<option value='$gcsId'> -$gcsName</option>\n" : "<option value='$gcsId'>$gcsName</option>\n";
}