<?php
include "../../globalFunctions.php";

$db = db_sfi();

$getId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$myId = filter_input(INPUT_GET, 'myId', FILTER_SANITIZE_NUMBER_INT);

$myTimeClock = $myId . "__timeClock";
$mySettings = $myId . "__settings";
$gettz = $db->prepare("SELECT timeZone FROM $mySettings WHERE id = '1'");
$gettz->execute();
$gettzr = $gettz->fetch();
if ($gettzr) {
    date_default_timezone_set($gettzr['timeZone']);
} else {
    date_default_timezone_set("America/Chicago");
}
$time = time();

echo "<table cellspacing='0px' style='margin:10px auto;'>";

$t = 1;
$get = $db->prepare(
        "SELECT * FROM $myTimeClock WHERE employeeId = ? ORDER BY clockIn DESC");
$get->execute(array(
        $getId
));
while ($getr = $get->fetch()) {
    $id = $getr['id'];
    $clockIn = dateTime2mktime($getr['clockIn']);
    $clockOut = dateTime2mktime($getr['clockOut']);

    if ($t == 1 && $clockOut < $clockIn) {
        echo "<tr><td style='text-align:center; padding-bottom:20px;' colspan='2'>Last clock in: " .
                date("Y-m-d H:i", $clockIn) .
                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<form action='index.php' method='post'>\n";
        echo "<input type='hidden' name='timeClock' value='1'>";
        echo "<input type='hidden' name='clockOut' value='$id'>";
        echo "<input type='hidden' name='employeeId' value='0'>";
        echo "<input type='hidden' name='manDate' value='" . date("Y-m-d", $time) .
                "'>";
        echo "<input type='hidden' name='manHour' value='" . date("H", $time) .
                "'>";
        echo "<input type='hidden' name='manMinute' value='" . date("i", $time) .
                "'>";
        echo "<button>Auto Clock Out</button></form></td>\n";
        echo "</tr>";
    }
    if ($clockOut < $clockIn) {
        echo "<tr>\n";
        echo "<td style='text-align:center; font-weight:bold;' colspan='2'>Missing Clock Out</td>\n";
        echo "</tr><tr>\n";
        echo "<td style='text-align:right; padding-right:10px;'>Clock In: " .
                date("Y-m-d H:i", $clockIn) . "</td>\n";
        echo "<td style='text-align:left; padding-left:10px;'><form action='index.php' method='post'>\n";
        echo "Clock Out: <input type='date' name='manDate' value='" .
                date("Y-m-d", $time) . "'> <select name='manHour' size='1'>\n";
        for ($i = 0; $i <= 23; ++ $i) {
            echo "<option value='$i'";
            echo ($i == date("H", $time)) ? " selected" : "";
            echo ">$i</option>\n";
        }
        echo "</select>:<select name='manMinute' size='1'>\n";
        for ($j = 0; $j <= 59; ++ $j) {
            echo "<option value='$j'";
            echo ($j == date("i", $time)) ? " selected" : "";
            echo ">$j</option>\n";
        }
        echo "</select>";
        echo "<input type='hidden' name='timeClock' value='1'>";
        echo "<input type='hidden' name='clockOut' value='$id'><input type='hidden' name='employeeId' value='0'><button>Clock Out</button></form></td>\n";
        echo "</tr>\n";
    }
    $t ++;
}
echo "<tr><td style='text-align:center; padding:10px 0px;' colspan='2'>~~*~~</td></tr>";
echo "<tr><td style='text-align:center;' colspan='2'><form action='index.php' method='post'>\n";
echo "<input type='hidden' name='timeClock' value='1'>";
echo "<input type='hidden' name='clockOut' value='0'>";
echo "<input type='hidden' name='employeeId' value='$getId'>";
echo "<input type='hidden' name='manDate' value='" . date("Y-m-d", $time) . "'>";
echo "<input type='hidden' name='manHour' value='" . date("H", $time) . "'>";
echo "<input type='hidden' name='manMinute' value='" . date("i", $time) . "'>";
echo "<button>Clock In</button></form></td>\n";
echo "</tr></table>";
