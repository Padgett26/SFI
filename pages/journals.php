<div class="heading">Bank Registers</div>
<?php
if ($myId >= 1 && $SA == 0) {
    $r = (filter_input(INPUT_GET, 'r', FILTER_SANITIZE_STRING)) ? filter_input(
            INPUT_GET, 'r', FILTER_SANITIZE_STRING) : '0';
    $an = (filter_input(INPUT_GET, 'an', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(
            INPUT_GET, 'an', FILTER_SANITIZE_NUMBER_INT) : '0'; // grab account
    $je = (filter_input(INPUT_GET, 'je', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(
            INPUT_GET, 'je', FILTER_SANITIZE_NUMBER_INT) : '0'; // grab journal
                                                                // entry
    $type = (filter_input(INPUT_GET, 'type', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(
            INPUT_GET, 'type', FILTER_SANITIZE_NUMBER_INT) : '0'; // grab
                                                                  // typeCode

    if (filter_input(INPUT_POST, 'delA', FILTER_SANITIZE_NUMBER_INT)) {
        $aid = filter_input(INPUT_POST, 'delA', FILTER_SANITIZE_NUMBER_INT);
        $delA = $db->prepare("DELETE FROM $myFAccounts WHERE id = ?");
        $delA->execute(array(
                $aid
        ));
    }

    if (filter_input(INPUT_POST, 'editA', FILTER_SANITIZE_STRING)) {
        $aid = filter_input(INPUT_POST, 'editA', FILTER_SANITIZE_STRING);
        $upAccountType = filter_input(INPUT_POST, 'accountType',
                FILTER_SANITIZE_NUMBER_INT);
        $upAccountNumber = filter_input(INPUT_POST, 'accountNumber',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $upAccountName = filter_var(
                htmlEntities(trim($_POST['accountName']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);
        $upStartBalance = filter_input(INPUT_POST, 'startBalance',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $upTime = date2mktime(
                filter_input(INPUT_POST, 'startDate', FILTER_SANITIZE_NUMBER_INT),
                'start');

        if ($aid == "new" && $upAccountType != "0") {
            $newA = $db->prepare(
                    "INSERT INTO $myFAccounts VALUES(NULL,?,?,?,?,?)");
            $newA->execute(
                    array(
                            $upAccountNumber,
                            $ACCOUNTTYPES[$upAccountType],
                            $upAccountName,
                            $upStartBalance,
                            $upTime
                    ));
        } else {
            $a = filter_var($aid, FILTER_SANITIZE_NUMBER_INT);
            $upA = $db->prepare(
                    "UPDATE $myFAccounts SET startBalance = ? WHERE id = ?");
            $upA->execute(array(
                    $upStartBalance,
                    $a
            ));
        }
    }

    if (filter_input(INPUT_POST, 'jUp', FILTER_SANITIZE_NUMBER_INT)) {
        $jUp = filter_input(INPUT_POST, 'jUp', FILTER_SANITIZE_NUMBER_INT);
        $je = filter_input(INPUT_POST, 'je', FILTER_SANITIZE_NUMBER_INT);
        $jDate = date2mktime(
                filter_input(INPUT_POST, 'jDate', FILTER_SANITIZE_NUMBER_INT),
                'noon');
        $_SESSION['jDate'] = $jDate;
        $jContactName = htmlEntities(
                trim(
                        filter_input(INPUT_POST, 'jContactName',
                                FILTER_SANITIZE_STRING)));
        $jContactNameSelect = filter_input(INPUT_POST, 'jContactNameSelect',
                FILTER_SANITIZE_NUMBER_INT);
        $jDescription = filter_var(
                htmlEntities(trim($_POST['jDescription']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);
        $jccc = filter_input(INPUT_POST, 'jccc', FILTER_SANITIZE_NUMBER_INT);
        $jCkNum = filter_input(INPUT_POST, 'jCkNum', FILTER_SANITIZE_NUMBER_INT);
        $jAccNumber = filter_input(INPUT_POST, 'jAccNumber',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $jTypeCode = filter_input(INPUT_POST, 'jTypeCode',
                FILTER_SANITIZE_NUMBER_INT);
        $jDebit = filter_input(INPUT_POST, 'jDebit',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $jCredit = filter_input(INPUT_POST, 'jCredit',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $delJ = (filter_input(INPUT_POST, 'delJ', FILTER_SANITIZE_NUMBER_INT) >=
                1) ? filter_input(INPUT_POST, 'delJ', FILTER_SANITIZE_NUMBER_INT) : 0;

        $dropJ = $db->prepare(
                "DELETE FROM $myFLedger WHERE refNumber = ? AND typeCode = '5'");
        $dropJ->execute(array(
                $je
        ));
        if ($delJ >= 1) {
            $je = 0;
        } else {
            $newId = ($je == 0) ? getNext(5, $myFLedger) : $je;

            if ($jContactNameSelect >= 1) {
                $contactId = $jContactNameSelect;
            } elseif ($jContactName != " " && $jContactName != "") {
                $contactId = conCheck($jContactName, $myContacts, $time, '0');
            } else {
                $contactId = 0;
            }

            $getJAcc = $db->prepare(
                    "SELECT accountNumber FROM $myFAccounts WHERE id = ?");
            $getJAcc->execute(array(
                    $jUp
            ));
            $getJAccR = $getJAcc->fetch();
            $bankAccNum = $getJAccR['accountNumber'];

            if ($jDebit >= 0.01) {
                // id, date, contact, description, cashCheckCC, checkNumber,
                // accountNumber, debitAmount, creditAmount, refNumber, typeCode
                $upLedger1 = $db->prepare(
                        "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0','0')");
                $upLedger1->execute(
                        array(
                                $jDate,
                                $contactId,
                                $jDescription,
                                $jccc,
                                $jCkNum,
                                $bankAccNum,
                                $jDebit,
                                $newId,
                                $jTypeCode
                        ));
                $upLedger2 = $db->prepare(
                        "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0','0')");
                $upLedger2->execute(
                        array(
                                $jDate,
                                $contactId,
                                $jDescription,
                                $jccc,
                                $jCkNum,
                                $jAccNumber,
                                $jDebit,
                                $newId,
                                $jTypeCode
                        ));
            }
            if ($jCredit >= 0.01) {
                $upLedger1 = $db->prepare(
                        "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0','0')");
                $upLedger1->execute(
                        array(
                                $jDate,
                                $contactId,
                                $jDescription,
                                $jccc,
                                $jCkNum,
                                $bankAccNum,
                                $jCredit,
                                $newId,
                                $jTypeCode
                        ));
                $upLedger2 = $db->prepare(
                        "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0','0')");
                $upLedger2->execute(
                        array(
                                $jDate,
                                $contactId,
                                $jDescription,
                                $jccc,
                                $jCkNum,
                                $jAccNumber,
                                $jCredit,
                                $newId,
                                $jTypeCode
                        ));
            }
        }
    }

    if (filter_input(INPUT_POST, 'gTypeCode', FILTER_SANITIZE_NUMBER_INT)) {
        $gTypeCode = filter_input(INPUT_POST, 'gTypeCode',
                FILTER_SANITIZE_NUMBER_INT);
        $je = filter_input(INPUT_POST, 'je', FILTER_SANITIZE_NUMBER_INT);
        $gDate = date2mktime(
                filter_input(INPUT_POST, 'gDate', FILTER_SANITIZE_NUMBER_INT),
                'noon');
        $gContactName = htmlEntities(
                trim(
                        filter_input(INPUT_POST, 'gContactName',
                                FILTER_SANITIZE_STRING)));
        $gContactNameSelect = filter_input(INPUT_POST, 'gContactNameSelect',
                FILTER_SANITIZE_NUMBER_INT);
        $gCCC = filter_input(INPUT_POST, 'gCCC', FILTER_SANITIZE_NUMBER_INT);
        $gCkNm = filter_input(INPUT_POST, 'gCkNm', FILTER_SANITIZE_NUMBER_INT);
        $delG = (filter_input(INPUT_POST, 'delG', FILTER_SANITIZE_NUMBER_INT) >=
                1) ? filter_input(INPUT_POST, 'delG', FILTER_SANITIZE_NUMBER_INT) : 0;

        $dropG = $db->prepare(
                "DELETE FROM $myFLedger WHERE refNumber = ? AND typeCode = ?");
        $dropG->execute(array(
                $je,
                $gTypeCode
        ));
        if ($delG >= 1) {
            if ($gTypeCode == 1) {
                $delS = $db->prepare("DELETE FROM $mySales WHERE id = ?");
                $delS->execute(array(
                        $je
                ));
            }
            if ($gTypeCode == 2) {
                $delP = $db->prepare("DELETE FROM $myPurchasing WHERE id = ?");
                $delP->execute(array(
                        $je
                ));
            }
            $je = 0;
        } else {
            $newId = ($je == 0) ? getNext(6, $myFLedger) : $je;
            $je = 0;

            if ($gContactNameSelect >= 1) {
                $contactId = $gContactNameSelect;
            } elseif ($gContactName != " " && $gContactName != "") {
                $contactId = conCheck($gContactName, $myContacts, $time, '0');
            } else {
                $contactId = 0;
            }

            $general = array();
            foreach ($_POST as $key => $val) {
                if (preg_match("/^accNumber([1-9][0-9]*)$/", $key, $match)) {
                    $v = filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT,
                            FILTER_FLAG_ALLOW_FRACTION);
                    $general[$match[1]][0] = $v;
                }
                if (preg_match("/^gDescription([1-9][0-9]*)$/", $key, $match)) {
                    $v = filter_var(htmlEntities(trim($val), ENT_QUOTES),
                            FILTER_SANITIZE_STRING);
                    $general[$match[1]][1] = $v;
                }
                if (preg_match("/^gDebit([1-9][0-9]*)$/", $key, $match)) {
                    $v = filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT,
                            FILTER_FLAG_ALLOW_FRACTION);
                    $general[$match[1]][2] = $v;
                }
                if (preg_match("/^gCredit([1-9][0-9]*)$/", $key, $match)) {
                    $v = filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT,
                            FILTER_FLAG_ALLOW_FRACTION);
                    $general[$match[1]][3] = $v;
                }
            }

            foreach ($general as $v) {
                if ($v[2] >= 0.01) {
                    // id, date, contact, description, cashCheckCC, checkNumber,
                    // accountNumber, debitAmount, creditAmount, refNumber,
                    // typeCode, dailyConfirm, notUsed1, notUsed2
                    $upLedger1 = $db->prepare(
                            "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'0','0','0')");
                    $upLedger1->execute(
                            array(
                                    $gDate,
                                    $contactId,
                                    $v[1],
                                    $gCCC,
                                    $gCkNm,
                                    $v[0],
                                    $v[2],
                                    $newId,
                                    $gTypeCode
                            ));
                }
                if ($v[3] >= 0.01) {
                    $upLedger2 = $db->prepare(
                            "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'0','0','0')");
                    $upLedger2->execute(
                            array(
                                    $gDate,
                                    $contactId,
                                    $v[1],
                                    $gCCC,
                                    $gCkNm,
                                    $v[0],
                                    $v[3],
                                    $newId,
                                    $gTypeCode
                            ));
                }
            }
            $msg = "Your transaction has been entered.";
        }
    }
    ?>

<div style="margin: 20px 0px;">
	<form action="index.php?page=journals" method="post">
		Date Range: From: <input type="date" name="dateRangeStart"
			value="<?php

    echo date('Y-m-d', $dateRangeStart);
    ?>"> To: <input
			type="date" name="dateRangeEnd"
			value="<?php

    echo date('Y-m-d', $dateRangeEnd);
    ?>"> <input
			type="submit" value=" GO ">
	</form>
</div>

<div style="float: left;">
	<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none;">
			<table style="width: 300px;">
				<tr>
					<td class="cat" style="text-align: left;">Bank Registers</td>
				</tr>
			</table>
		</div>
        <?php
    $getBanks = $db->prepare(
            "SELECT id, accountName FROM $myFAccounts WHERE (accountNumber >= ? AND accountNumber <= ?) OR (accountNumber >= ? AND accountNumber <= ?) ORDER BY accountName");
    $getBanks->execute(array(
            '101.0',
            '101.9',
            '210.1',
            '210.9'
    ));
    while ($getBr = $getBanks->fetch()) {
        $jId = $getBr['id'];
        $jName = html_entity_decode($getBr['accountName'], ENT_QUOTES);
        ?>
            <form id="frmJournal<?php
        echo $jId;
        ?>"
		action="index.php?page=journals&r=journal&an=<?php
        echo $jId;
        ?>"
		method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('Journal<?php
        echo $jId;
        ?>')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;"><?php
        echo $jName;
        ?></td>
				</tr>
			</table>
		</div>
	</form>
        <?php
    }
    ?>
	<form id="frmPay" action="index.php?page=journals&r=general" method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('Pay')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">General Journal</td>
				</tr>
			</table>
		</div>
	</form>
    </div>
<div style="float: left;">
        <?php
    if ($r == "general") {
        ?><div style="text-align:right;">
    	<?php
        echo showHelpLeft(15);
        ?>
    </div><?php
        include "includes/reportsGeneral.php";
    } elseif ($r == "journal") {
        ?><div style="text-align:right;">
    	<?php
        echo showHelpLeft(16);
        ?>
    </div><?php
        include "includes/reportsJournal.php";
    }
    ?>
    </div>
<?php
} else {
    echo "Please log in or check your subscription in settings to see your reports";
}