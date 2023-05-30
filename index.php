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