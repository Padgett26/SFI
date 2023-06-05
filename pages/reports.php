<div class="heading">Reports</div>
<?php
if ($myId >= 1) {
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

    if (filter_input(INPUT_POST, 'closeDay', FILTER_SANITIZE_NUMBER_INT) == 1) {
        $c = filter_input(INPUT_POST, 'confirm', FILTER_SANITIZE_STRING);
        $confirm = explode(",", $c);

        foreach ($_POST as $key => $val) {
            $v = filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT,
                    FILTER_FLAG_ALLOW_FRACTION);
            if (preg_match("/^amt([0-9][0-9]*)$/", $key, $match)) {

                if ($v >= 0.01) {
                    $n = getNext('5', $myFLedger);
                    $ID = $match[1];
                    $getAccount = $db->prepare(
                            "SELECT accountNumber FROM $myFAccounts WHERE id = ?");
                    $getAccount->execute(array(
                            $ID
                    ));
                    $gar = $getAccount->fetch();
                    $acc = $gar['accountNumber'];

                    // id, date, contact, description, cashCheckCC, checkNumber,
                    // accountNumber, debitAmount, creditAmount, refNumber,
                    // typeCode, dailyConfirm, notUsed1, notUsed2
                    $upLedger1 = $db->prepare(
                            "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,'0.00',?,?,?,'1','0','0')");
                    $upLedger1->execute(
                            array(
                                    $time,
                                    '1',
                                    'Bank Deposit',
                                    '0',
                                    '0',
                                    '101.0',
                                    $v,
                                    $n,
                                    '5'
                            ));
                    $upLedger2 = $db->prepare(
                            "INSERT INTO $myFLedger VALUES(NULL,?,?,?,?,?,?,?,'0.00',?,?,'1','0','0')");
                    $upLedger2->execute(
                            array(
                                    $time,
                                    '1',
                                    'Bank Deposit',
                                    '0',
                                    '0',
                                    $acc,
                                    $v,
                                    $n,
                                    '5'
                            ));
                }
            }
        }
        foreach ($confirm as $value) {
            $setConfirm = $db->prepare(
                    "UPDATE $myFLedger SET dailyConfirm = '1' WHERE id = ?");
            $setConfirm->execute(array(
                    $value
            ));
        }
    }
    ?>

<div style="margin: 20px 0px;">
	<form action="index.php?page=reports" method="post">
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
					<td class="cat" style="text-align: left;">Reports</td>
				</tr>
			</table>
		</div>
	<form id="frmSales" action="index.php?page=reports&r=sales"
		method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('Sales')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Daily Sales Report</td>
				</tr>
			</table>
		</div>
	</form>
	<form id="frmItem" action="index.php?page=reports&r=item" method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('Item')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Inventory by Item</td>
				</tr>
			</table>
		</div>
	</form>
	<form id="frmFReport" action="index.php?page=reports&r=fReport" method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('FReport')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Balance Sheet</td>
				</tr>
			</table>
		</div>
	</form>
	<form id="frmIncome" action="index.php?page=reports&r=income" method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('Income')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Income Statement</td>
				</tr>
			</table>
		</div>
	</form>
	<?php
    if ($useMilage == 1) {
        ?>
    	<form id="frmMilage" action="index.php?page=reports&r=milage" method="post">
    		<div
    			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
    			onclick="submitForm('Milage')">
    			<table style="width: 300px;">
    				<tr>
    					<td style="text-align: left;">Vehicle Milage</td>
    				</tr>
    			</table>
    		</div>
    	</form>
	<?php
    }
    ?>
	<table><tr>
	<td style="cursor:pointer; font-weight:bold; line-heoght:1.5; padding:5px;" onclick="toggleview('whoOwesWho')">
	Outstanding funds
	</td>
	<td style="text-align:right;">
    	<?php
    echo showHelpRight(4);
    ?>
    </td>
    </tr></table>
	<div style="font-weight:bold; line-heoght:1.5; display:none; padding:10px;" id="whoOwesWho">
	<table>
	<?php
    foreach ($CONTACTS as $k => $v) {
        $sb = 0;
        $getS = $db->prepare(
                "SELECT startingBalance FROM $myContacts WHERE id = ?");
        $getS->execute(array(
                $k
        ));
        $getSR = $getS->fetch();
        $sb = $getSR['startingBalance'];

        $getPay1 = $db->prepare(
                "SELECT debitAmount, creditAmount FROM $myFLedger WHERE contact = ? AND accountNumber = ?");
        $getPay1->execute(array(
                $k,
                '110.0'
        ));
        while ($getPR1 = $getPay1->fetch()) {
            $sb += ($getPR1['debitAmount'] - $getPR1['creditAmount']);
        }
        $getPay2 = $db->prepare(
                "SELECT debitAmount, creditAmount FROM $myFLedger WHERE contact = ? AND accountNumber = ?");
        $getPay2->execute(array(
                $k,
                '210.0'
        ));
        while ($getPR2 = $getPay2->fetch()) {
            $sb += ($getPR2['creditAmount'] - $getPR2['debitAmount']);
        }
        echo ($sb != 0) ? "<tr><td style='text-align:left;'><a href='index.php?page=contacts&contactId=$k'>$v</a></td><td style='text-align:right;'>" .
                money_sfi($sb, $currency, $langCode) . "</td></tr>" : "";
    }
    ?>
	</table>
	</div>
	<?php
    $c = 0;
    foreach ($CONTACTS as $k => $v) {
        $s = 0;
        $getsb = $db->prepare(
                "SELECT startingBalance FROM $myContacts WHERE id = ?");
        $getsb->execute(array(
                $k
        ));
        $getsbR = $getsb->fetch();
        $s += $getsbR['startingBalance'];
        $getPay = $db->prepare(
                "SELECT debitAmount, creditAmount FROM $myFLedger WHERE contact = ? AND accountNumber = ?");
        $getPay->execute(array(
                $k,
                '110.0'
        ));
        while ($getPR = $getPay->fetch()) {
            $s += ($getPR['debitAmount'] - $getPR['creditAmount']);
        }
        if ($s >= 0.01) {
            $c ++;
        }
    }
    ?>
	<form id="frmStatements110" action="stmtPrint110.php" method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('Statements110')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Print Customer Statements (<?php
    echo $c;
    ?>)</td>
				</tr>
			</table>
		</div>
	</form>
	<?php
    $d = 0;
    foreach ($CONTACTS as $k => $v) {
        $s = 0;
        $getsb = $db->prepare(
                "SELECT startingBalance FROM $myContacts WHERE id = ?");
        $getsb->execute(array(
                $k
        ));
        $getsbR = $getsb->fetch();
        $s += $getsbR['startingBalance'];
        $getPay = $db->prepare(
                "SELECT debitAmount, creditAmount FROM $myFLedger WHERE contact = ? AND accountNumber = ?");
        $getPay->execute(array(
                $k,
                '210.0'
        ));
        while ($getPR = $getPay->fetch()) {
            $s += ($getPR['creditAmount'] - $getPR['debitAmount']);
        }
        if ($s >= 0.01) {
            $d ++;
        }
    }
    ?>
	<form id="frmStatements210" action="stmtPrint210.php" method="post">
		<div
			style="line-height: 1.5; font-weight: bold; text-decoration: none; cursor: pointer;"
			onclick="submitForm('Statements210')">
			<table style="width: 300px;">
				<tr>
					<td style="text-align: left;">Print Vendor Statements (<?php
    echo $d;
    ?>)</td>
				</tr>
			</table>
		</div>
	</form>
    </div>
<div style="float: left;">
        <?php
    if ($r == '0') {
        echo "";
    } elseif ($r == "sales") {
        ?><div style="text-align:right;">
    	<?php
        echo showHelpLeft(10);
        ?>
    </div><?php
        include "includes/reportsSales.php";
    } elseif ($r == "fReport") {
        ?><div style="text-align:right;">
    	<?php
        echo showHelpLeft(12);
        ?>
    </div><?php
        include "includes/reportsFReport.php";
    } elseif ($r == "income") {
        ?><div style="text-align:right;">
    	<?php
        echo showHelpLeft(13);
        ?>
    </div><?php
        include "includes/reportsIncome.php";
    } elseif ($r == "milage") {
        include "includes/reportsMilage.php";
    } elseif ($r == "item") {
        ?><div style="text-align:right;">
    	<?php
        echo showHelpLeft(11);
        ?>
    </div><?php
        include "includes/reportsItem.php";
    }
    ?>
    </div>
<?php
} else {
    echo "Please log in or check your subscription in settings to see your reports";
}