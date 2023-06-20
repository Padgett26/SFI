<?php
session_start();

include "../globalFunctions.php";

$db = db_sfi();
$debugging = 0; // 1 for debug info showing, 0 for not showing
$beta = 0; // 1 for beta, 0 for complete

$domain = "simplefinancialsandinventory.com";
$time = time();

$visitingIP = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_STRING);

// *** Log out ***
if (filter_input(INPUT_GET, 'logout', FILTER_SANITIZE_STRING) == 'yep') {
    destroySession();
    setcookie("staySignedIn", '', $time - 1209600, "/", $domain, 0);
}

// *** Sign in ***
$loginErr = "x";
if (filter_input(INPUT_POST, 'login', FILTER_SANITIZE_NUMBER_INT) == "1") {
    $email = (filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)) ? strtolower(
            filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)) : '0';
    $hidepwd = 'x';
    $login1stmt = $db->prepare("SELECT id,salt FROM users WHERE email = ?");
    $login1stmt->execute(array(
            $email
    ));
    $login1row = $login1stmt->fetch();
    if ($login1row) {
        $salt = $login1row['salt'];
        $checkId = (isset($login1row['id']) && $login1row['id'] > 0) ? $login1row['id'] : '0';
        $pwd = filter_input(INPUT_POST, 'pwd', FILTER_SANITIZE_STRING);
        $hidepwd = hash('sha512', ($salt . $pwd), FALSE);
    }
    $login2stmt = $db->prepare(
            "SELECT id FROM users WHERE email = ? AND password = ? && accessLevel >= ?");
    $login2stmt->execute(array(
            $email,
            $hidepwd,
            "1"
    ));
    $login2row = $login2stmt->fetch();
    if ($login2row) {
        $x = $login2row['id'];
        $_SESSION['myId'] = $x;
        setcookie("staySignedIn", $_SESSION['myId'], $time + 1209600, "/",
                $domain, 0); // set for 14 days
        $lastUpdate = $db->prepare(
                "UPDATE users SET lastLogin = ? WHERE id = ?");
        $lastUpdate->execute(array(
                $time,
                $x
        ));
    } else {
        $loginErr = "Your email / password combination isn't correct, or you haven't verified your email address.";
    }
}

// *** User settings ***
$myId = (isset($_SESSION['myId']) && ($_SESSION['myId'] >= '1')) ? $_SESSION['myId'] : '0'; // are
                                                                                            // they
                                                                                            // logged
                                                                                            // in
if ($myId == '0' &&
        (empty(filter_input(INPUT_GET, 'logout', FILTER_SANITIZE_STRING)))) {
    $myId = (filter_input(INPUT_COOKIE, 'staySignedIn',
            FILTER_SANITIZE_NUMBER_INT) >= '1') ? filter_input(INPUT_COOKIE,
            'staySignedIn', FILTER_SANITIZE_NUMBER_INT) : '0'; // are they
                                                               // logged in
}

// *** page settings ***
$page = (filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING)) ? filter_input(
        INPUT_GET, 'page', FILTER_SANITIZE_STRING) : "home";
if (! file_exists("pages/" . $page . ".php")) {
    $page = "home";
}

$errorMsg = "";
$msg = "";

$myInventory = $myId . "__inventory";
$myContacts = $myId . "__contacts";
$myCategories = $myId . "__categories";
$myPurchasing = $myId . "__purchasing";
$myRecipes = $myId . "__recipes";
$mySales = $myId . "__sales";
$mySettings = $myId . "__settings";
$myFAccounts = $myId . "__fAccounts";
$myFLedger = $myId . "__fLedger";
$myFLedgerOld = $myId . "__fLedgerOld";
$myEmployees = $myId . "__employees";
$myEmployeeTracking = $myId . "__employeeTracking";
$myMilage = $myId . "__milage";
$myVehicles = $myId . "__vehicles";
$myTimeClock = $myId . "__timeClock";

if ($myId >= 1) {
    if (filter_input(INPUT_POST, 'settingsUp', FILTER_SANITIZE_STRING) ==
            'company') {
        $timeZoneArea = filter_input(INPUT_POST, 'timeZoneArea',
                FILTER_SANITIZE_STRING);
        $timeZoneCity = filter_input(INPUT_POST, 'timeZoneCity',
                FILTER_SANITIZE_STRING);
        $timeZone = $timeZoneArea . "/" . $timeZoneCity;

        $settingsUp = $db->prepare(
                "UPDATE $mySettings SET timeZone = ? WHERE id = ?");
        $settingsUp->execute(array(
                $timeZone,
                '1'
        ));
    }

    $getSettings = $db->prepare("SELECT * FROM $mySettings WHERE id = ?");
    $getSettings->execute(array(
            '1'
    ));
    $gsRow = $getSettings->fetch();
    if ($gsRow) {
        $companyName = html_entity_decode($gsRow['name'], ENT_QUOTES);
        $companyAddress1 = html_entity_decode($gsRow['address1'], ENT_QUOTES);
        $companyAddress2 = html_entity_decode($gsRow['address2'], ENT_QUOTES);
        $companyPhone = $gsRow['phone'];
        $companyEmail = $gsRow['email'];
        $markUp = $gsRow['markUp'];
        $taxRate = $gsRow['taxRate'];
        $purchasingCostProcessing = $gsRow['purchasingCostProcessing'];
        $fiscalYear = $gsRow['fiscalYear'];
        $currency = $gsRow['currency'];
        $budgetTerm = $gsRow['budgetTerm'];
        $useMilage = $gsRow['useMilage'];
        $usePayroll = $gsRow['usePayroll'];
        $timeZone = $gsRow['timeZone'];
        date_default_timezone_set($timeZone);
        $getlangCode = $db->prepare(
                "SELECT langCode FROM currencies WHERE symbol = ?");
        $getlangCode->execute(array(
                $currency
        ));
        $glc = $getlangCode->fetch();
        if ($glc) {
            $langCode = $glc['langCode'];
        }
    } else {
        $timeZone = "America/Chicago";
        date_default_timezone_set($timeZone);
        $currency = "USD";
        $langCode = "en-US";
    }
}

$sfaiInfo = $db->prepare("SELECT * FROM company");
$sfaiInfo->execute();
$sfaiInfoR = $sfaiInfo->fetch();
$sfaiEmail = $sfaiInfoR['contactEmail'];
$legalText = nl2br($sfaiInfoR['legal']);

if (filter_input(INPUT_POST, 'sendFeedback', FILTER_SANITIZE_NUMBER_INT) == 1) {
    $fromEmail = (filter_input(INPUT_POST, 'fromEmail', FILTER_VALIDATE_EMAIL)) ? filter_input(
            INPUT_POST, 'fromEmail', FILTER_SANITIZE_EMAIL) : 1;
    $emailBody = filter_input(INPUT_POST, 'emailBody', FILTER_SANITIZE_STRING);
    if ($fromEmail != 1) {
        $message = wordwrap($emailBody, 70);
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
        $headers .= "From: $fromEmail" . "\r\n";
        mail($sfaiEmail, 'Feedback from the SFaI website', $message, $headers);
    }
}

$grabInvId = filter_input(INPUT_GET, 'grabInvId', FILTER_SANITIZE_NUMBER_INT) ? filter_input(
        INPUT_GET, 'grabInvId', FILTER_SANITIZE_NUMBER_INT) : 0;
$onload = ($grabInvId > 0) ? " onload = 'editInvItem(\"$grabInvId\")'" : "";
$onload = ($page == 'contacts') ? "onload = 'totalIt()'" : $onload;

$WEEKDAYS = array(
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
        "Sunday"
);
$MONTHS = array(
        1 => "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
);

$UOM = array();
$getUOM = $db->prepare("SELECT * FROM unitsOfMeasure");
$getUOM->execute();
while ($uomR = $getUOM->fetch()) {
    $UOM[$uomR['id']] = $uomR['unitOfMeasure'];
}
$UOM[0] = "EA";

$CONTACTS = array();
if ($myId >= 1) {
    $getContacts = $db->prepare("SELECT id, name FROM $myContacts");
    $getContacts->execute();
    while ($contactsR = $getContacts->fetch()) {
        $CONTACTS[$contactsR['id']] = html_entity_decode($contactsR['name'],
                ENT_QUOTES);
    }
    asort($CONTACTS, SORT_STRING);
}

$TYPECODES = array();
$getTypes = $db->prepare("SELECT id, ledgerType FROM LedgerTypeCode");
$getTypes->execute();
while ($typesR = $getTypes->fetch()) {
    $TYPECODES[$typesR['id']] = $typesR['ledgerType'];
}

$ACCOUNTS = array();
if ($myId >= 1) {
    $getAcc = $db->prepare(
            "SELECT accountNumber, accountName FROM $myFAccounts ORDER BY accountNumber");
    $getAcc->execute();
    while ($accR = $getAcc->fetch()) {
        $ACCOUNTS[$accR['accountNumber']] = html_entity_decode(
                $accR['accountName'], ENT_QUOTES);
    }
}

$CATEGORIES = array();
if ($myId >= 1) {
    $getcat = $db->prepare("SELECT * FROM $myCategories");
    $getcat->execute();
    while ($getcatR = $getcat->fetch()) {
        $CATEGORIES[$getcatR['id']] = html_entity_decode($getcatR['category'],
                ENT_QUOTES);
    }
}

$SAVEACCOUNTS = array(
        '101.0',
        '101.1',
        '110.0',
        '120.0',
        '200.0',
        '210.0',
        '210.1',
        '210.2',
        '210.3',
        '210.4',
        '210.5',
        '210.6',
        '210.7',
        '210.8',
        '210.9',
        '211.0',
        '211.1',
        '211.2',
        '211.3',
        '211.4',
        '211.5',
        '211.6',
        '211.7',
        '211.8',
        '211.9',
        '220.0',
        '300.0',
        '380.0',
        '399.9',
        '400.1',
        '400.2',
        '400.3',
        '400.4',
        '400.5',
        '400.6',
        '500.0'
);

$ACCOUNTTYPES = array(
        1 => "asset",
        2 => "liability",
        3 => "capital",
        4 => "income",
        5 => "expense"
);

$PAYTYPES = array(
        0 => "none",
        1 => "Cash",
        2 => "Check",
        3 => "Credit Card",
        4 => "Debit Card",
        5 => "Working Loan"
);

$tables = array(
        array(
                'name' => 'settings',
                'function' => 'createSettings'
        ),
        array(
                'name' => 'fAccounts',
                'function' => 'createFAccounts'
        ),
        array(
                'name' => 'fLedger',
                'function' => 'createFLedger'
        ),
        array(
                'name' => 'fLedgerOld',
                'function' => 'createFLedgerOld'
        ),
        array(
                'name' => 'inventory',
                'function' => 'createInventory'
        ),
        array(
                'name' => 'contacts',
                'function' => 'createContacts'
        ),
        array(
                'name' => 'categories',
                'function' => 'createCategories'
        ),
        array(
                'name' => 'sales',
                'function' => 'createSales'
        ),
        array(
                'name' => 'purchasing',
                'function' => 'createPurchasing'
        ),
        array(
                'name' => 'recipes',
                'function' => 'createRecipes'
        ),
        array(
                'name' => 'vehicles',
                'function' => 'createVehicles'
        ),
        array(
                'name' => 'employees',
                'function' => 'createEmployees'
        ),
        array(
                'name' => 'milage',
                'function' => 'createMilage'
        ),
        array(
                'name' => 'employeeTracking',
                'function' => 'createEmployeeTracking'
        ),
        array(
                'name' => 'timeClock',
                'function' => 'createTimeClock'
        )
);

// ***Undo Close Fiscal Year***
if (filter_input(INPUT_POST, 'undoClose', FILTER_SANITIZE_NUMBER_INT) == 1) {
    $ucd = date("d", $fiscalYear);
    $ucm = date("m", $fiscalYear);
    $ucy = date("Y", $fiscalYear);
    $ucStart = mktime(0, 0, 0, $ucm, $ucd, ($ucy - 1));
    $ucEnd = mktime(0, 0, - 1, $ucm, $ucd, $ucy);
    $cOld = $db->prepare(
            "SELECT * FROM $myFLedgerOld WHERE date >= ? AND date <= ?");
    $cOld->execute(array(
            $ucStart,
            $ucEnd
    ));
    while ($coR = $cOld->fetch()) {
        $oId = $coR['id'];
        $oDate = $coR['date'];
        $oContact = $coR['contact'];
        $oDescription = $coR['description'];
        $oCashCheckCC = $coR['cashCheckCC'];
        $oCheckNumber = $coR['checkNumber'];
        $oAccountNumber = $coR['accountNumber'];
        $oDebitAmount = $coR['debitAmount'];
        $oCreditAmount = $coR['creditAmount'];
        $oRefNumber = $coR['refNumber'];
        $oTypeCode = $coR['typeCode'];
        $oDailyConfirm = $coR['dailyConfirm'];
        $oBalanceDue = $coR['balanceDue'];
        $oReconcile = $coR['reconcile'];

        $setOld = $db->prepare(
                "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $setOld->execute(
                array(
                        $oDate,
                        $oContact,
                        $oDescription,
                        $oCashCheckCC,
                        $oCheckNumber,
                        $oAccountNumber,
                        $oDebitAmount,
                        $oCreditAmount,
                        $oRefNumber,
                        $oTypeCode,
                        $oDailyConfirm,
                        $oBalanceDue,
                        $oReconcile
                ));

        $delOld = $db->prepare("DELETE FROM $myFLedgerOld WHERE id = ?");
        $delOld->execute(array(
                $oId
        ));
    }
    $setFY = $db->prepare("UPDATE $mySettings SET fiscalYear = ?");
    $setFY->execute(array(
            $ucStart
    ));
    $fiscalYear = $ucStart;

    $getCBal = $db->prepare(
            "SELECT id, prevStartingBalance, prevBalancePaid FROM $myContacts");
    $getCBal->execute();
    while ($gcb = $getCBal->fetch()) {
        $gcbId = $gcb['id'];
        $gcbPrev = $gcb['prevStartingBalance'];
        $gcbPBP = $gcb['prevBalancePaid'];

        $setCBal = $db->prepare(
                "UPDATE $myContacts SET startingBalance = ?, balancePaid = ?, prevStartingBalance = ?, prevBalancePaid = ? WHERE id = ?");
        $setCBal->execute(array(
                $gcbPrev,
                $gcbPBP,
                '0.00',
                '0',
                $gcbId
        ));
    }
    $getABal = $db->prepare("SELECT id, prevStartBalance FROM $myFAccounts");
    $getABal->execute();
    while ($gab = $getABal->fetch()) {
        $gabId = $gab['id'];
        $gabPrev = $gab['prevStartBalance'];

        $setABal = $db->prepare(
                "UPDATE $myFAccounts SET startBalance = ? WHERE id = ?");
        $setABal->execute(array(
                $gabPrev,
                $gabId
        ));
    }
}

// ***Close Fiscal Year***
if (filter_input(INPUT_POST, 'close', FILTER_SANITIZE_NUMBER_INT) == 1) {
    $fy = filter_input(INPUT_POST, 'fy', FILTER_SANITIZE_NUMBER_INT);

    foreach ($CONTACTS as $k => $v) {
        $getS = $db->prepare(
                "SELECT startingBalance, balancePaid FROM $myContacts WHERE id = ?");
        $getS->execute(array(
                $k
        ));
        $getSR = $getS->fetch();
        $sb = $getSR['startingBalance'];
        $bp = $getSR['balancePaid'];

        $setPrevS = $db->prepare(
                "UPDATE $myContacts SET prevStartingBalance = ?, prevBalancePaid = ? WHERE id = ?");
        $setPrevS->execute(array(
                $sb,
                $bp,
                $k
        ));

        // Accounts Payable
        $getPay = $db->prepare(
                "SELECT debitAmount, creditAmount FROM $myFLedger WHERE contact = ? AND accountNumber = ? AND date < ?");
        $getPay->execute(array(
                $k,
                '210.0',
                $fy
        ));
        while ($getPR = $getPay->fetch()) {
            $sb += ($getPR['creditAmount'] - $getPR['debitAmount']);
        }

        // Accounts receivables
        $getR = $db->prepare(
                "SELECT debitAmount, creditAmount FROM $myFLedger WHERE contact = ? AND accountNumber = ? AND date < ?");
        $getR->execute(array(
                $k,
                '110.0',
                $fy
        ));
        while ($getRR = $getR->fetch()) {
            $sb += ($getRR['debitAmount'] - $getRR['creditAmount']);
        }
        $setS = $db->prepare(
                "UPDATE $myContacts SET startingBalance = ?, balancePaid = ? WHERE id = ?");
        $setS->execute(array(
                $sb,
                '0',
                $k
        ));
    }

    $folder = "cmPics/$myId/backups";
    if (! is_dir("$folder")) {
        mkdir("$folder", 0777, true);
    }
    $toClear = array();
    $showFileFY = ($fy - 1);
    $fileName = "fiscalYearBackup" . date("Y-m-d", $fiscalYear) . "to" .
            date("Y-m-d", $showFileFY) . ".csv";
    $myfile = fopen("$folder/$fileName", "w") or die("Unable to open file!");
    $txt = "id,date,contact,description,cash Check CC,check Number,account,debit Amount,credit Amount,ref Number,type,reconciled\n";
    fwrite($myfile, $txt);
    $getL = $db->prepare("SELECT * FROM $myFLedger WHERE date < ? ORDER BY id");
    $getL->execute(array(
            $fy
    ));
    while ($getLR = $getL->fetch()) {
        $id = $getLR['id'];
        $date = date("Y-m-d", $getLR['date']);
        $contactId = $getLR['contact'];
        $description = str_replace(",", "",
                html_entity_decode($getLR['description'], ENT_QUOTES));
        $cashCheckCC = $PAYTYPES[$getLR['cashCheckCC']];
        $checkNumber = $getLR['checkNumber'];
        $accountNumber = $getLR['accountNumber'];
        $debitAmount = $getLR['debitAmount'];
        $creditAmount = $getLR['creditAmount'];
        $refNumber = $getLR['refNumber'];
        $typeCode = $TYPECODES[$getLR['typeCode']];
        $reconcile = ($getLR['reconcile'] == 1) ? "Reconciled" : " ";
        $toClear[] = $id;

        $backup = $db->prepare(
                "INSERT INTO $myFLedgerOld VALUES(NULL,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $backup->execute(
                array(
                        $getLR['date'],
                        $getLR['contact'],
                        $getLR['description'],
                        $getLR['cashCheckCC'],
                        $getLR['checkNumber'],
                        $getLR['accountNumber'],
                        $getLR['debitAmount'],
                        $getLR['creditAmount'],
                        $getLR['refNumber'],
                        $getLR['typeCode'],
                        $getLR['dailyConfirm'],
                        $getLR['balanceDue'],
                        $getLR['reconcile']
                ));

        $get = $db->prepare("SELECT name FROM $myContacts WHERE id = ?");
        $get->execute(array(
                $contactId
        ));
        $getR = $get->fetch();
        if ($getR) {
            $contact = str_replace(",", "",
                    html_entity_decode($getR['name'], ENT_QUOTES));
        }

        $txt = "$id,$date,$contact,$description,$cashCheckCC,$checkNumber,$accountNumber - $ACCOUNTS[$accountNumber]," .
                $debitAmount . "," . $creditAmount .
                ",$refNumber,$typeCode,$reconcile\n";
        fwrite($myfile, $txt);
    }
    $txt = ",,,,,,,,,,,\n";
    fwrite($myfile, $txt);
    $txt = "Totals,,,,,,,,,,,\n";
    fwrite($myfile, $txt);
    $txt = "Account Number,Account Name,Debit Amount,Credit Amount,Total,,,,,,,\n";
    fwrite($myfile, $txt);
    $arrayList = "";
    $t = 0;
    foreach ($ACCOUNTS as $k => $v) {
        $arrayList .= ($t != 0) ? "," : "";
        $arrayList .= "'D$k' => 0,'C$k' => 0,'T$k' => 0";
        $t ++;
    }
    $totals = array(
            $arrayList
    );
    $getL2 = $db->prepare(
            "SELECT accountNumber,debitAmount,creditAmount FROM $myFLedger WHERE date < ?");
    $getL2->execute(array(
            $fy
    ));
    while ($getL2R = $getL2->fetch()) {
        $accountNumber = $getL2R['accountNumber'];
        $debitAmount = $getL2R['debitAmount'];
        $creditAmount = $getL2R['creditAmount'];
        $totals["D$accountNumber"] += $debitAmount;
        $totals["C$accountNumber"] += $creditAmount;
        if (($accountNumber >= 100 && $accountNumber <= 199.9) ||
                ($accountNumber >= 500 && $accountNumber <= 599.9)) {
            $totals["T$accountNumber"] += ($debitAmount - $creditAmount);
        } else {
            $totals["T$accountNumber"] += ($creditAmount - $debitAmount);
        }
    }
    foreach ($ACCOUNTS as $k => $v) {
        $txt = number_format($k, 1, '.', '') . "," . $v . "," . $totals["D$k"] .
                "," . $totals["C$k"] . "," . $totals["T$k"] . ",,,,,,,\n";
        fwrite($myfile, $txt);
    }
    fclose($myfile);

    foreach ($toClear as $v) {
        $delL = $db->prepare("DELETE FROM $myFLedger WHERE id = ?");
        $delL->execute(array(
                $v
        ));
    }

    $updateFY = $db->prepare(
            "UPDATE $mySettings SET fiscalYear = ? WHERE id = '1'");
    $updateFY->execute(array(
            $fy
    ));
    $fiscalYear = $fy;
}

// ***Check Fiscal Year***
$dateMin = 0;
if ($myId >= 1) {
    if ($fiscalYear >= 1) {
        $fycheck = explode("-", date("Y-m-d", $fiscalYear));
        $time2Close = mktime(0, 0, 0, $fycheck[1], $fycheck[2],
                ($fycheck[0] + 1));
        $need2Close = ($time >= $time2Close) ? 1 : 0;
        $dateMin = date("Y-m-d", $fiscalYear);
    } else {
        $need2Close = 0;
    }
} else {
    $need2Close = 0;
}

$CURRENCIES = array();
$t = 0;
$getCurr = $db->prepare("SELECT * FROM currencies");
$getCurr->execute();
while ($gcr = $getCurr->fetch()) {
    $CURRENCIES[$t][0] = $gcr['symbol'];
    $CURRENCIES[$t][1] = $gcr['name'];
    $CURRENCIES[$t][2] = $gcr['langCode'];
    $t ++;
}