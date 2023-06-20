<?php
include "../../globalFunctions.php";

$area = filter_input(INPUT_GET, 'area', FILTER_SANITIZE_STRING);

echo "<select name='timeZoneCity' size='1'>";
$myTimeZoneCity = explode("/", $timeZone);
$city = (isset($myTimeZoneCity[2])) ? $myTimeZoneCity[1] . "/" .
        $myTimeZoneCity[2] : $myTimeZoneCity[1];
$TimeZoneCities = getTimeZoneCities($area);
foreach ($TimeZoneCities as $v) {
    echo "<option value='$v'";
    echo ($city == $v) ? " selected" : "";
    echo ">$v</option>\n";
}
echo "</select>";