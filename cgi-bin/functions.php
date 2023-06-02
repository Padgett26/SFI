<?php

function createSettings ($table, $db)
{
    $settings = $db->prepare(
            "CREATE TABLE $table (
id INT(2) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100),
address1 VARCHAR(100),
address2 VARCHAR(100),
phone VARCHAR(20),
email VARCHAR(100),
markUp INT(4) DEFAULT 0,
taxRate DECIMAL(6,4) DEFAULT 0.0000,
purchasingCostProcessing INT(2) UNSIGNED DEFAULT 0,
fiscalYear INT(12) UNSIGNED DEFAULT 0,
currency VARCHAR(5),
budgetTerm INT(2) DEFAULT 0
)");
    $settings->execute();
    $settings2 = $db->prepare(
            "INSERT INTO $table VALUES(1,'name','address','city st zip','phone','email','0','0.0000','0','0','USD','0')");
    $settings2->execute();
    return true;
}

function createCategories ($table, $db)
{
    $categories = $db->prepare(
            "CREATE TABLE $table (
id INT(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
category VARCHAR(100),
subOf INT(4) DEFAULT 0,
notUsed1 INT(2) DEFAULT 0,
notUsed2 INT(2) DEFAULT 0
)");
    $categories->execute();
    $categories1 = $db->prepare(
            "INSERT INTO $table VALUES(1,'Labor','0','0','0')");
    $categories1->execute();
    $categories2 = $db->prepare(
            "INSERT INTO $table VALUES(2,'Uncategorized','0','0','0')");
    $categories2->execute();
    $categories3 = $db->prepare(
            "INSERT INTO $table VALUES(3,'Hidden','0','0','0')");
    $categories3->execute();
    return true;
}

function createFAccounts ($table, $db)
{
    $time = time();
    $fAccounts = $db->prepare(
            "CREATE TABLE $table (
id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
accountNumber DECIMAL(6,1) UNSIGNED UNIQUE,
accountType VARCHAR(20),
accountName VARCHAR(100),
startBalance DECIMAL(10,2) DEFAULT 0.00,
prevStartBalance DECIMAL(10,2) DEFAULT 0.00,
startDate INT(12) UNSIGNED DEFAULT 0,
budget DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
notUsed1 INT(2) DEFAULT 0,
notUsed2 INT(2) DEFAULT 0
)");
    $fAccounts->execute();
    $acc1 = $db->prepare(
            "INSERT INTO $table VALUES('1','101.0','asset','Cash Drawer','0.00','0.00',?,'0.00','0','0')");
    $acc1->execute(array(
            $time
    ));
    $acc2 = $db->prepare(
            "INSERT INTO $table VALUES('2','120.0','asset','Inventory','0.00','0.00',?,'0.00','0','0')");
    $acc2->execute(array(
            $time
    ));
    $acc3 = $db->prepare(
            "INSERT INTO $table VALUES('3','200.0','liability','Sales Tax Payable','0.00','0.00',?,'0.00','0','0')");
    $acc3->execute(array(
            $time
    ));
    $acc4 = $db->prepare(
            "INSERT INTO $table VALUES('4','300.0','capital','Owners Capital','0.00','0.00',?,'0.00','0','0')");
    $acc4->execute(array(
            $time
    ));
    $acc5 = $db->prepare(
            "INSERT INTO $table VALUES('5','400.1','income','Revenue','0.00','0.00',?,'0.00','0','0')");
    $acc5->execute(array(
            $time
    ));
    $acc6 = $db->prepare(
            "INSERT INTO $table VALUES('6','400.2','income','Shipping COS','0.00','0.00',?,'0.00','0','0')");
    $acc6->execute(array(
            $time
    ));
    $acc7 = $db->prepare(
            "INSERT INTO $table VALUES('7','500.0','expense','Sales Tax Expense','0.00','0.00',?,'0.00','0','0')");
    $acc7->execute(array(
            $time
    ));
    $acc8 = $db->prepare(
            "INSERT INTO $table VALUES('8','220.0','liability','Labor Production','0.00','0.00',?,'0.00','0','0')");
    $acc8->execute(array(
            $time
    ));
    $acc9 = $db->prepare(
            "INSERT INTO $table VALUES('9','400.3','income','Inventory Adj','0.00','0.00',?,'0.00','0','0')");
    $acc9->execute(array(
            $time
    ));
    $acc10 = $db->prepare(
            "INSERT INTO $table VALUES('10','110.0','asset','Account Receivables','0.00','0.00',?,'0.00','0','0')");
    $acc10->execute(array(
            $time
    ));
    $acc11 = $db->prepare(
            "INSERT INTO $table VALUES('11','400.4','income','Fees Revenue','0.00','0.00',?,'0.00','0','0')");
    $acc11->execute(array(
            $time
    ));
    $acc12 = $db->prepare(
            "INSERT INTO $table VALUES('12','400.5','income','Discounts','0.00','0.00',?,'0.00','0','0')");
    $acc12->execute(array(
            $time
    ));
    $acc13 = $db->prepare(
            "INSERT INTO $table VALUES('13','400.6','income','Inventory COS','0.00','0.00',?,'0.00','0','0')");
    $acc13->execute(array(
            $time
    ));
    $acc14 = $db->prepare(
            "INSERT INTO $table VALUES('14','380.0','capital','Net Income','0.00','0.00',?,'0.00','0','0')");
    $acc14->execute(array(
            $time
    ));
    $acc15 = $db->prepare(
            "INSERT INTO $table VALUES('15','101.1','asset','Petty Cash','0.00','0.00',?,'0.00','0','0')");
    $acc15->execute(array(
            $time
    ));
    $acc16 = $db->prepare(
            "INSERT INTO $table VALUES('16','210.0','liability','Accounts Payable','0.00','0.00',?,'0.00','0','0')");
    $acc16->execute(array(
            $time
    ));
    $acc17 = $db->prepare(
            "INSERT INTO $table VALUES('17','399.9','capital','Retained Earnings','0.00','0.00',?,'0.00','0','0')");
    $acc17->execute(array(
            $time
    ));
    return true;
}

function createFLedger ($table, $db)
{
    $fLedger = $db->prepare(
            "CREATE TABLE $table (
id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
date INT(12) UNSIGNED,
contact INT(10) UNSIGNED,
description VARCHAR(100),
cashCheckCC INT(4) UNSIGNED,
checkNumber INT(10) UNSIGNED,
accountNumber DECIMAL(6,1) UNSIGNED,
debitAmount DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
creditAmount DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
refNumber INT(10) UNSIGNED DEFAULT 0,
typeCode INT(4) UNSIGNED DEFAULT 0,
dailyConfirm INT(2) UNSIGNED DEFAULT 0,
balanceDue DECIMAL(10,2) DEFAULT 0.00,
reconcile INT(2) UNSIGNED DEFAULT 0
)");
    $fLedger->execute();
    return true;
}

function createFLedgerOld ($table, $db)
{
    $fLedger = $db->prepare(
            "CREATE TABLE $table (
id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
date INT(12) UNSIGNED,
contact INT(10) UNSIGNED,
description VARCHAR(100),
cashCheckCC INT(4) UNSIGNED,
checkNumber INT(10) UNSIGNED,
accountNumber DECIMAL(6,1) UNSIGNED,
debitAmount DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
creditAmount DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
refNumber INT(10) UNSIGNED DEFAULT 0,
typeCode INT(4) UNSIGNED DEFAULT 0,
dailyConfirm INT(2) UNSIGNED DEFAULT 0,
balanceDue DECIMAL(10,2) DEFAULT 0.00,
reconcile INT(2) UNSIGNED DEFAULT 0
)");
    $fLedger->execute();
    return true;
}

function createInventory ($table, $db)
{
    $inventory = $db->prepare(
            "CREATE TABLE $table (
id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
time INT(12) UNSIGNED,
name VARCHAR(100),
description TEXT,
unitOfMeasure INT(6),
quantity DECIMAL(8,2) DEFAULT 0.00,
cost DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
price DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
picture VARCHAR(20) DEFAULT '0.xxx',
contactId INT(8) UNSIGNED,
categoryId INT(6) UNSIGNED,
recipeId INT(8) UNSIGNED DEFAULT 0,
taxed INT(2) UNSIGNED DEFAULT 1
)");
    $inventory->execute();
    $inventory2 = $db->prepare(
            "INSERT INTO $table VALUES(1,?,'Labor','Labor cost per hour','3','0','0.00','0.00','','0','1','0','0')");
    $inventory2->execute(array(
            time()
    ));
    return true;
}

function createSales ($table, $db)
{
    $sales = $db->prepare(
            "CREATE TABLE $table (
id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
time INT(12) UNSIGNED,
contactId INT(8) UNSIGNED DEFAULT 0,
items VARCHAR(2000),
taxes DECIMAL(8,2) UNSIGNED DEFAULT 0.00,
shipping DECIMAL(8,2) UNSIGNED DEFAULT 0.00,
fees DECIMAL(8,2) UNSIGNED DEFAULT 0.00,
finalized INT(2) UNSIGNED DEFAULT 0,
paid INT(2) UNSIGNED DEFAULT 0,
discountPercent INT(4) DEFAULT 0,
ccc INT(4) UNSIGNED,
ckNum INT(8) UNSIGNED,
notes TEXT,
notUsed2 INT(2) UNSIGNED DEFAULT 0
)");
    $sales->execute();
    return true;
}

function createPurchasing ($table, $db)
{
    $purchasing = $db->prepare(
            "CREATE TABLE $table (
id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
time INT(12) UNSIGNED,
contactId INT(8) UNSIGNED DEFAULT 0,
items VARCHAR(2000),
taxes DECIMAL(8,2) UNSIGNED DEFAULT 0.00,
shipping DECIMAL(8,2) UNSIGNED DEFAULT 0.00,
fees DECIMAL(8,2) UNSIGNED DEFAULT 0.00,
finalized INT(2) UNSIGNED DEFAULT 0,
paid INT(2) UNSIGNED DEFAULT 0,
fromAcc DECIMAL(6,1) UNSIGNED,
notUsed2 INT(2) UNSIGNED DEFAULT 0
)");
    $purchasing->execute();
    return true;
}

function createContacts ($table, $db)
{
    $contacts = $db->prepare(
            "CREATE TABLE $table (
id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100),
address1 VARCHAR(100),
address2 VARCHAR(100),
phone VARCHAR(20),
email VARCHAR(100),
lastAccessed INT(12) UNSIGNED,
chargeTax INT(2) DEFAULT 1,
vendor INT(2) DEFAULT 0,
startingBalance DECIMAL(10,2) DEFAULT 0.00,
prevStartingBalance DECIMAL(10,2) DEFAULT 0.00,
balancePaid INT(2) UNSIGNED DEFAULT 0,
prevBalancePaid INT(2) UNSIGNED DEFAULT 0
)");
    $contacts->execute();
    $contacts1 = $db->prepare(
            "INSERT INTO $table VALUES(1,'In House','','','','',?,'1','0','0.00','0','0')");
    $contacts1->execute(array(
            time()
    ));
    return true;
}

function createRecipes ($table, $db)
{
    $recipes = $db->prepare(
            "CREATE TABLE $table (
id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100),
description TEXT,
instructions TEXT,
ingredients VARCHAR(200),
picture VARCHAR(20),
categoryId INT(6) UNSIGNED,
cost DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
price DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
invId INT(8) UNSIGNED DEFAULT 0,
notUsed1 INT(2) UNSIGNED DEFAULT 0,
notUsed2 INT(2) UNSIGNED DEFAULT 0
)");
    $recipes->execute();
    return true;
}

function sendVerificationEmail ($toId, $firstName, $email, $verifyCode)
{
    $link = hash('sha512', ($verifyCode . $firstName . $email), FALSE);
    $mess = "$firstName,\r\n\r\n
        As a layer of security, we ask that you verify your email address before being allowed to post on the SFaI webpage.  The easiest way to do this is to click on the link below, this will update your status on the webpage.  If clicking on the link doesn't work, usually because html isn't enabled in your email client, you can also highlight the link below, copy it (ctrl + c), and then paste it (ctrl + v) in the address field of your web browser, and then hit enter.\r\n\r\n
        https://simplefinancialsandinventory.com/index.php?page=Register&id=$toId&ver=$link\r\n\r\n
        Thank you,\nAdmin\nSFaI";
    $message = wordwrap($mess, 70);
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
    $headers .= "From: SFaI Admin <admin@simplefinancialsandinventory.com>";
    return mail($email,
            'Please verify your email address to access Simple Financials and Inventory',
            $message, $headers);
}

function sendPWResetEmail ($toId, $firstName, $email, $verifyCode)
{
    $link = hash('sha512', ($verifyCode . $firstName . $email), FALSE);
    $mess = "$firstName,\r\n\r\n
        There has been a request on the SFaI website for a password reset for this account.  If you initiated this request, click the link below, and you will be sent to a page where you will be able enter a new password. If you did not initiate this password reset request, simple ignore this email, and your password will not be changed.\r\n\r\n
        https://simplefinancialsandinventory.com/index.php?page=PWReset&id=$toId&ver=$link\r\n\r\n
        Thank you,\nAdmin\nSFaI";
    $message = wordwrap($mess, 70);
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
    $headers .= "From: SFaI Admin <admin@simplefinancialsandinventory.com>";
    return mail($email, 'Simple Financials and Inventory password reset request',
            $message, $headers);
}

function invCheck ($name, $uom, $db, $myInventory, $time)
{
    $getItemId = $db->prepare("SELECT id FROM $myInventory WHERE name=?");
    $getItemId->execute(array(
            $name
    ));
    $getItemIdRow = $getItemId->fetch();
    if (! empty($getItemIdRow['id'])) {
        return $getItemIdRow['id'];
    } else {
        $setItem = $db->prepare(
                "INSERT INTO $myInventory VALUES(NULL,?,?,'',?,'0','','','','0','2','0','0')");
        $setItem->execute(array(
                $time,
                $name,
                $uom
        ));
        $getIId = $db->prepare(
                "SELECT id FROM $myInventory WHERE name=? AND time=? ORDER BY id DESC LIMIT 1");
        $getIId->execute(array(
                $name,
                $time
        ));
        $getIIdRow = $getIId->fetch();
        return $getIIdRow['id'];
    }
}

function catCheck ($name, $catSubOf, $db, $myCategories)
{
    $getCatId = $db->prepare("SELECT id FROM $myCategories WHERE category = ?");
    $getCatId->execute(array(
            $name
    ));
    $getCatIdRow = $getCatId->fetch();
    if (! empty($getCatIdRow['id'])) {
        return $getCatIdRow['id'];
    } else {
        $setCat = $db->prepare(
                "INSERT INTO $myCategories VALUES(NULL,?,?,'0','0')");
        $setCat->execute(array(
                $name,
                $catSubOf
        ));
        $getCId = $db->prepare(
                "SELECT id FROM $myCategories WHERE category = ? AND subOf = ? ORDER BY id DESC LIMIT 1");
        $getCId->execute(array(
                $name,
                $catSubOf
        ));
        $getCIdRow = $getCId->fetch();
        if (! empty($getCIdRow)) {
            return $getCIdRow['id'];
        } else {
            return 0;
        }
    }
}

function conCheck ($upContactName, $db, $myContacts, $time, $vendor)
{
    $getContact = $db->prepare("SELECT id FROM $myContacts WHERE name = ?");
    $getContact->execute(array(
            $upContactName
    ));
    $gContactR = $getContact->fetch();
    $c = (! empty($gContactR)) ? $gContactR['id'] : 0;
    if ($c >= 1) {
        return $c;
    } else {
        $newContact = $db->prepare(
                "INSERT INTO $myContacts VALUES(NULL,?,'','','','',?,'1',?,'0.00','0.00','0','0')");
        $newContact->execute(array(
                $upContactName,
                $time,
                $vendor
        ));
        $getNewContactId = $db->prepare(
                "SELECT id FROM $myContacts WHERE name = ? AND lastAccessed = ? ORDER BY id DESC LIMIT 1");
        $getNewContactId->execute(array(
                $upContactName,
                $time
        ));
        $gncR = $getNewContactId->fetch();
        return $gncR['id'];
    }
}

function date2mktime ($date, $when)
{
    switch ($when) {
        case 'start':
            $h = 0;
            $m = 0;
            $s = 0;
            break;
        case 'noon':
            $h = 12;
            $m = 0;
            $s = 0;
            break;
        case 'end':
            $h = 23;
            $m = 59;
            $s = 59;
            break;
    }
    $a = explode("-", $date);
    return mktime($h, $m, $s, $a[1], $a[2], $a[0]);
}

function getContact ($id, $db, $myContacts)
{
    $name = "";
    $get = $db->prepare("SELECT name FROM $myContacts WHERE id = ?");
    $get->execute(array(
            $id
    ));
    $getR = $get->fetch();
    if ($getR) {
        $name = html_entity_decode($getR['name'], ENT_QUOTES);
    }
    return $name;
}

function getNext ($type, $db, $table)
{
    $next = 0;
    $get = $db->prepare(
            "SELECT refNumber FROM $table WHERE typeCode = ? ORDER BY refNumber DESC LIMIT 1");
    $get->execute(array(
            $type
    ));
    $getR = $get->fetch();
    if ($getR) {
        $next = ($getR['refNumber']);
    }
    $next ++;
    return $next;
}

function selectNewUOM ($unitOfMeasure, $db)
{
    $r = "<option value='0'></option>\n";
    for ($i = 1; $i <= 6; ++ $i) {
        switch ($i) {
            case 1:
                $r .= "<option style='background-color:#eeeeee;'>Count</option>\n";
                break;
            case 2:
                $r .= "<option style='background-color:#eeeeee;'>Weight</option>\n";
                break;
            case 3:
                $r .= "<option style='background-color:#eeeeee;'>Liquid</option>\n";
                break;
            case 4:
                $r .= "<option style='background-color:#eeeeee;'>Time</option>\n";
                break;
            case 5:
                $r .= "<option style='background-color:#eeeeee;'>Distance</option>\n";
                break;
            case 6:
                $r .= "<option style='background-color:#eeeeee;'>Storage</option>\n";
                break;
        }
        $getU = $db->prepare(
                "SELECT id, unitOfMeasure FROM unitsOfMeasure WHERE type = ?");
        $getU->execute(array(
                $i
        ));
        while ($getUR = $getU->fetch()) {
            $r .= "<option value='" . $getUR['id'] . "'";
            $r .= ($getUR['id'] == $unitOfMeasure) ? " selected" : "";
            $r .= ">" . $getUR['unitOfMeasure'] . "</option>\n";
        }
    }
    return $r;
}

function money_sfi ($amt, $symbol, $code)
{
    settype($amt, "float");
    $a = new NumberFormatter($code, NumberFormatter::CURRENCY);
    return $a->formatCurrency($amt, $symbol);
}

function showHelpLeft ($t, $db)
{
    $get = $db->prepare("SELECT writeUp FROM helpPage WHERE id = ?");
    $get->execute(array(
            $t
    ));
    $getR = $get->fetch();
    $tip = nl2br($getR['writeUp']);
    $text = "<div class='tooltipLeft'><image src='images/help.png' style='max-width:50px; max-height:50px;' alt=''><span class='tooltiptextLeft'>" .
            $tip . "</span></div>";
    return $text;
}

function showHelpRight ($t, $db)
{
    $get = $db->prepare("SELECT writeUp FROM helpPage WHERE id = ?");
    $get->execute(array(
            $t
    ));
    $getR = $get->fetch();
    $tip = nl2br($getR['writeUp']);
    $text = "<div class='tooltipRight'><image src='images/help.png' style='max-width:20px; max-height:20px;' alt=''><span class='tooltiptextRight'>" .
            $tip . "</span></div>";
    return $text;
}