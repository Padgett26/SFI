<?php
include "cgi-bin/config.php";
include "cgi-bin/functions.php";

if (filter_input(INPUT_POST, 'dateRangeStart', FILTER_SANITIZE_NUMBER_INT)) {
    $_SESSION['dateRangeStart'] = date2mktime(
            filter_input(INPUT_POST, 'dateRangeStart',
                    FILTER_SANITIZE_NUMBER_INT), 'start');
}
$dateRangeStart = (isset($_SESSION['dateRangeStart'])) ? $_SESSION['dateRangeStart'] : date2mktime(
        date("Y-m-d", ($time - 604800)), 'start');

if (filter_input(INPUT_POST, 'dateRangeEnd', FILTER_SANITIZE_NUMBER_INT)) {
    $_SESSION['dateRangeEnd'] = date2mktime(
            filter_input(INPUT_POST, 'dateRangeEnd', FILTER_SANITIZE_NUMBER_INT),
            'end');
}
$dateRangeEnd = (isset($_SESSION['dateRangeEnd'])) ? $_SESSION['dateRangeEnd'] : date2mktime(
        date("Y-m-d", $time), 'end');

if (filter_input(INPUT_POST, 'quickTransUp', FILTER_SANITIZE_NUMBER_INT) == 1) {
    $qDate = date2mktime(
            filter_input(INPUT_POST, 'qDate', FILTER_SANITIZE_NUMBER_INT),
            'noon');
    $qContactName = htmlEntities(
            trim(
                    filter_input(INPUT_POST, 'qContactName',
                            FILTER_SANITIZE_STRING)));
    $qContactNameSelect = filter_input(INPUT_POST, 'qContactNameSelect',
            FILTER_SANITIZE_NUMBER_INT);
    $qCCC = filter_input(INPUT_POST, 'qCCC', FILTER_SANITIZE_NUMBER_INT);
    $qCkNm = filter_input(INPUT_POST, 'qCkNm', FILTER_SANITIZE_NUMBER_INT);
    $newId = getNext(6, $myFLedger);

    if ($qContactNameSelect >= 1) {
        $contactId = $qContactNameSelect;
    } elseif ($qContactName != " " && $qContactName != "") {
        $contactId = conCheck($qContactName, $myContacts, $time, '0');
    } else {
        $contactId = 0;
    }

    $qDesc = htmlEntities(
            trim(
                    filter_input(INPUT_POST, 'qDescription',
                            FILTER_SANITIZE_STRING)));
    $qFromAcc = filter_input(INPUT_POST, 'qFromAcc',
            FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $qToAcc = filter_input(INPUT_POST, 'qToAcc', FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION);
    $qAmount = filter_input(INPUT_POST, 'qAmount', FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION);

    if ($qAmount >= 0.01) {
        // id, date, contact, description, cashCheckCC, checkNumber,
        // accountNumber, debitAmount, creditAmount, refNumber,
        // typeCode, dailyConfirm, notUsed1, notUsed2
        $upLedger2 = $db->prepare(
                "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0','0')");
        $upLedger2->execute(
                array(
                        $qDate,
                        $contactId,
                        $qDesc,
                        $qCCC,
                        $qCkNm,
                        $qFromAcc,
                        $qAmount,
                        $newId,
                        '6'
                ));

        $upLedger1 = $db->prepare(
                "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0','0')");
        $upLedger1->execute(
                array(
                        $qDate,
                        $contactId,
                        $qDesc,
                        $qCCC,
                        $qCkNm,
                        $qToAcc,
                        $qAmount,
                        $newId,
                        '6'
                ));
    }
}
?>
<!DOCTYPE HTML>
<html manifest="includes/cache.appcache">
<head>
        <?php
        include "includes/head.php";
        ?>
    </head>
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-TKC89BPZW9"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-TKC89BPZW9');
</script>
<body <?php

echo $onload;
?>>
        <?php
        echo "<table style='width:100%;'>\n";
        echo "<tr><td>\n";
        include "includes/header.php";
        echo "</td></tr>\n";
        if ($need2Close == 1) {
            echo "<tr><td>\n";
            echo "<form id='frmClose' action='index.php?page=settings&r=close' method='post'>";
            echo "<div style='width:100%; padding:10px; background-color:#660000; cursor:pointer; color:#ffffff; font-weight:bold;' onclick='submitForm(\"Close\")'>";
            echo "Please go to settings to close out your previous fiscal year, which ended on " .
                    date("Y-m-d", $time2Close) . ".";
            echo "</div>";
            echo "</form>";
            echo "</td></tr>\n";
        }
        echo "<tr><td>\n";
        include "pages/" . $page . ".php";
        echo "</td></tr>\n";
        echo "<tr><td>\n";
        include "../familyLinks.php";
        echo "</td></tr>\n";
        echo "</table>\n";
        ?>
    </body>
</html>