<?php

function createSettings ($table)
{
	$settings = db_sfi()->prepare("CREATE TABLE $table (
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
budgetTerm INT(2) DEFAULT 0,
useMilage INT(2) UNSIGNED DEFAULT 0,
usePayroll INT(2) UNSIGNED DEFAULT 0,
timeZone VARCHAR(30),
EIN VARCHAR(30),
stateEIN VARCHAR(30),
overtimeMultiplier DECIMAL(5,2),
holidayMultiplier DECIMAL(5,2),
overtimeHours INT(4) UNSIGNED,
startOfWorkWeek INT(4) UNSIGNED,
SSRate DECIMAL(6,4),
SSBaseRate INT(8) UNSIGNED,
medicareRate DECIMAL(6,4),
medicareBaseRate INT(8) UNSIGNED,
medicareAddOn DECIMAL(6,4),
futaRate DECIMAL(6,4),
futaBaseRate INT(8) UNSIGNED,
notUsed1 INT(2) UNSIGNED DEFAULT 0,
notUsed2 INT(2) UNSIGNED DEFAULT 0,
notUsed3 INT(2) UNSIGNED DEFAULT 0
)");
	$settings->execute();
	$settings2 = db_sfi()->prepare("INSERT INTO $table VALUES(1,'name','address','city st zip','phone','email','0','0.0000','0','0','USD','0','0','0','America/Chicago','','','1.50','2.00','40','1','0.062','160200','0.0145','200000','0.009','0.06','7000','0','0','0')");
	$settings2->execute();
	return true;
}

function createCategories ($table)
{
	$categories = db_sfi()->prepare("CREATE TABLE $table (
id INT(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
category VARCHAR(100),
subOf INT(4) DEFAULT 0,
notUsed1 INT(2) DEFAULT 0,
notUsed2 INT(2) DEFAULT 0
)");
	$categories->execute();
	$categories1 = db_sfi()->prepare("INSERT INTO $table VALUES(1,'Labor','0','0','0')");
	$categories1->execute();
	$categories2 = db_sfi()->prepare("INSERT INTO $table VALUES(2,'Uncategorized','0','0','0')");
	$categories2->execute();
	$categories3 = db_sfi()->prepare("INSERT INTO $table VALUES(3,'Hidden','0','0','0')");
	$categories3->execute();
	return true;
}

function createFAccounts ($table)
{
	$time = time();
	$db = db_sfi();
	$fAccounts = $db->prepare("CREATE TABLE $table (
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
	$acc1 = $db->prepare("INSERT INTO $table VALUES('1','101.0','asset','Cash Drawer','0.00','0.00',?,'0.00','0','0')");
	$acc1->execute(array(
			$time
	));
	$acc2 = $db->prepare("INSERT INTO $table VALUES('2','101.1','asset','Petty Cash','0.00','0.00',?,'0.00','0','0')");
	$acc2->execute(array(
			$time
	));
	$acc3 = $db->prepare("INSERT INTO $table VALUES('3','110.0','asset','Account Receivables','0.00','0.00',?,'0.00','0','0')");
	$acc3->execute(array(
			$time
	));
	$acc4 = $db->prepare("INSERT INTO $table VALUES('4','120.0','asset','Inventory','0.00','0.00',?,'0.00','0','0')");
	$acc4->execute(array(
			$time
	));
	$acc5 = $db->prepare("INSERT INTO $table VALUES('5','200.0','liability','Sales Tax Payable','0.00','0.00',?,'0.00','0','0')");
	$acc5->execute(array(
			$time
	));
	$acc6 = $db->prepare("INSERT INTO $table VALUES('6','210.0','liability','Accounts Payable','0.00','0.00',?,'0.00','0','0')");
	$acc6->execute(array(
			$time
	));
	$acc7 = $db->prepare("INSERT INTO $table VALUES('7','220.0','liability','Labor Production','0.00','0.00',?,'0.00','0','0')");
	$acc7->execute(array(
			$time
	));
	$acc8 = $db->prepare("INSERT INTO $table VALUES('8','250.0','liability','Federal Tax Payable','0.00','0.00',?,'0.00','0','0')");
	$acc8->execute(array(
			$time
	));
	$acc9 = $db->prepare("INSERT INTO $table VALUES('9','250.1','liability','FICA Payable','0.00','0.00',?,'0.00','0','0')");
	$acc9->execute(array(
			$time
	));
	$acc10 = $db->prepare("INSERT INTO $table VALUES('10','250.2','liability','Federal FUTA Payable','0.00','0.00',?,'0.00','0','0')");
	$acc10->execute(array(
			$time
	));
	$acc11 = $db->prepare("INSERT INTO $table VALUES('11','250.3','liability','Health Ins Payable','0.00','0.00',?,'0.00','0','0')");
	$acc11->execute(array(
			$time
	));
	$acc12 = $db->prepare("INSERT INTO $table VALUES('12','250.4','liability','Retirement Payable','0.00','0.00',?,'0.00','0','0')");
	$acc12->execute(array(
			$time
	));
	$acc13 = $db->prepare("INSERT INTO $table VALUES('13','250.5','liability','Extra Tax Witholding Payable','0.00','0.00',?,'0.00','0','0')");
	$acc13->execute(array(
			$time
	));
	$acc14 = $db->prepare("INSERT INTO $table VALUES('14','250.6','liability','Garnishment Payable','0.00','0.00',?,'0.00','0','0')");
	$acc14->execute(array(
			$time
	));
	$acc15 = $db->prepare("INSERT INTO $table VALUES('15','250.7','liability','Other Witholding Payable','0.00','0.00',?,'0.00','0','0')");
	$acc15->execute(array(
			$time
	));
	$acc16 = $db->prepare("INSERT INTO $table VALUES('16','300.0','capital','Owners Capital','0.00','0.00',?,'0.00','0','0')");
	$acc16->execute(array(
			$time
	));
	$acc17 = $db->prepare("INSERT INTO $table VALUES('17','380.0','capital','Net Income','0.00','0.00',?,'0.00','0','0')");
	$acc17->execute(array(
			$time
	));
	$acc18 = $db->prepare("INSERT INTO $table VALUES('18','399.9','capital','Retained Earnings','0.00','0.00',?,'0.00','0','0')");
	$acc18->execute(array(
			$time
	));
	$acc19 = $db->prepare("INSERT INTO $table VALUES('19','400.1','income','Revenue','0.00','0.00',?,'0.00','0','0')");
	$acc19->execute(array(
			$time
	));
	$acc20 = $db->prepare("INSERT INTO $table VALUES('20','400.2','income','Shipping COS','0.00','0.00',?,'0.00','0','0')");
	$acc20->execute(array(
			$time
	));
	$acc21 = $db->prepare("INSERT INTO $table VALUES('21','400.3','income','Inventory Adj','0.00','0.00',?,'0.00','0','0')");
	$acc21->execute(array(
			$time
	));
	$acc22 = $db->prepare("INSERT INTO $table VALUES('22','400.4','income','Fees Revenue','0.00','0.00',?,'0.00','0','0')");
	$acc22->execute(array(
			$time
	));
	$acc23 = $db->prepare("INSERT INTO $table VALUES('23','400.5','income','Discounts','0.00','0.00',?,'0.00','0','0')");
	$acc23->execute(array(
			$time
	));
	$acc24 = $db->prepare("INSERT INTO $table VALUES('24','400.6','income','Inventory COS','0.00','0.00',?,'0.00','0','0')");
	$acc24->execute(array(
			$time
	));
	$acc25 = $db->prepare("INSERT INTO $table VALUES('25','500.0','expense','Sales Tax Expense','0.00','0.00',?,'0.00','0','0')");
	$acc25->execute(array(
			$time
	));
	$acc26 = $db->prepare("INSERT INTO $table VALUES('26','550.0','expense','Employee Payroll Expense','0.00','0.00',?,'0.00','0','0')");
	$acc26->execute(array(
			$time
	));
	$acc28 = $db->prepare("INSERT INTO $table VALUES('28','550.2','expense','Payroll FICA Expense','0.00','0.00',?,'0.00','0','0')");
	$acc28->execute(array(
			$time
	));
	$acc29 = $db->prepare("INSERT INTO $table VALUES('29','550.3','expense','Payroll FUTA Expense','0.00','0.00',?,'0.00','0','0')");
	$acc29->execute(array(
			$time
	));
	$acc30 = $db->prepare("INSERT INTO $table VALUES('30','550.4','expense','Payroll Medical Expense','0.00','0.00',?,'0.00','0','0')");
	$acc30->execute(array(
			$time
	));
	$acc31 = $db->prepare("INSERT INTO $table VALUES('31','550.5','expense','Payroll Retirement Expense','0.00','0.00',?,'0.00','0','0')");
	$acc31->execute(array(
			$time
	));
	$acc32 = $db->prepare("INSERT INTO $table VALUES('32','250.8','liability','State Tax Payable','0.00','0.00',?,'0.00','0','0')");
	$acc32->execute(array(
			$time
	));
	return true;
}

function createFLedger ($table)
{
	$fLedger = db_sfi()->prepare("CREATE TABLE $table (
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

function createFLedgerOld ($table)
{
	$fLedger = db_sfi()->prepare("CREATE TABLE $table (
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

function createInventory ($table)
{
	$inventory = db_sfi()->prepare("CREATE TABLE $table (
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
	$inventory2 = db_sfi()->prepare("INSERT INTO $table VALUES(1,?,'Labor','Labor cost per hour','3','0','0.00','0.00','','0','1','0','0')");
	$inventory2->execute(array(
			time()
	));
	return true;
}

function createSales ($table)
{
	$sales = db_sfi()->prepare("CREATE TABLE $table (
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

function createPurchasing ($table)
{
	$purchasing = db_sfi()->prepare("CREATE TABLE $table (
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

function createContacts ($table)
{
	$contacts = db_sfi()->prepare("CREATE TABLE $table (
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
	$contacts1 = db_sfi()->prepare("INSERT INTO $table VALUES(1,'In House','','','','',?,'1','0','0.00','0.00','0','0')");
	$contacts1->execute(array(
			time()
	));
	return true;
}

function createRecipes ($table)
{
	$recipes = db_sfi()->prepare("CREATE TABLE $table (
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

function createVehicles ($table)
{
	$vehicles = db_sfi()->prepare("CREATE TABLE $table (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100),
licensePlate VARCHAR(10),
vin VARCHAR(20),
assignedTo INT(6),
retired INT(2) UNSIGNED DEFAULT 0,
notUsed1 INT(2) UNSIGNED DEFAULT 0,
notUsed2 INT(2) UNSIGNED DEFAULT 0
)");
	$vehicles->execute();
	$v1 = db_sfi()->prepare("INSERT INTO $table VALUES(1,'Default','','',1,'0','0','0')");
	$v1->execute();
	return true;
}

function createEmployees ($table)
{
	$employee = db_sfi()->prepare("CREATE TABLE $table (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100),
ssn INT(12) UNSIGNED,
hireDate INT(12) UNSIGNED,
terminateDate INT(12) UNSIGNED,
email VARCHAR(50),
address VARCHAR(50),
cityStZip VARCHAR(50),
phone INT(12) UNSIGNED,
w4_1c INT(2) UNSIGNED,
w4_2c INT(2) UNSIGNED,
w4_3 INT(7) UNSIGNED,
w4_4a INT(7) UNSIGNED,
w4_4b INT(7) UNSIGNED,
w4_4c INT(7) UNSIGNED,
eHealthIns DECIMAL(10,2) UNSIGNED,
cHealthIns DECIMAL(10,2) UNSIGNED,
eRetirement401k DECIMAL(10,2) UNSIGNED,
cRetirement401k DECIMAL(10,2) UNSIGNED,
garnishment DECIMAL(10,2) UNSIGNED,
otherWitholding DECIMAL(10,2) UNSIGNED,
notUsed1 INT(2) UNSIGNED DEFAULT 0,
notUsed2 INT(2) UNSIGNED DEFAULT 0
)");
	$employee->execute();
	$e1 = db_sfi()->prepare("INSERT INTO $table VALUES(1,'Default','0','0','0','','','','0','1','0','0','0','0','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0','0')");
	$e1->execute();
	return true;
}

function createMilage ($table)
{
	$milage = db_sfi()->prepare("CREATE TABLE $table (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
vehicleId INT(6) UNSIGNED,
employeeId INT(6) UNSIGNED,
usageDate INT(12) UNSIGNED,
milageBegin DECIMAL(10,1) UNSIGNED,
milageEnd DECIMAL(10,1) UNSIGNED,
notUsed1 INT(2) UNSIGNED DEFAULT 0,
notUsed2 INT(2) UNSIGNED DEFAULT 0
)");
	$milage->execute();
	return true;
}

function createEmployeeTracking ($table)
{
	$eTrack = db_sfi()->prepare("CREATE TABLE $table (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
employeeId INT(6) UNSIGNED,
date INT(12) UNSIGNED,
hourlyPayRate DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
salaryPayRate DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
otherPay DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
description TEXT,
otherPayPaid INT(2) UNSIGNED DEFAULT 0,
notUsed2 INT(2) UNSIGNED DEFAULT 0
)");
	$eTrack->execute();
	return true;
}

function createTimeClock ($table)
{
	$clock = db_sfi()->prepare("CREATE TABLE $table (
id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
employeeId INT(6) UNSIGNED,
clockIn INT(12) UNSIGNED,
clockOut INT(12) UNSIGNED DEFAULT 0,
paid INT(2) UNSIGNED DEFAULT 0,
reportedTips DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
sickOrVacation INT(2) UNSIGNED DEFAULT 0,
notUsed1 INT(2) UNSIGNED DEFAULT 0,
notUsed2 INT(2) UNSIGNED DEFAULT 0,
notUsed3 INT(2) UNSIGNED DEFAULT 0
)");
	$clock->execute();
	return true;
}

function createHolidays ($table)
{
	$holidays = db_sfi()->prepare("CREATE TABLE $table (
id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
startTime INT(12) UNSIGNED,
endTime INT(12) UNSIGNED,
holidayName VARCHAR(30),
notUsed1 INT(2) UNSIGNED DEFAULT 0,
notUsed2 INT(2) UNSIGNED DEFAULT 0,
notUsed3 INT(2) UNSIGNED DEFAULT 0
)");
	$holidays->execute();
	return true;
}

function createEmployeePayrollHistory ($table)
{
	$history = db_sfi()->prepare("CREATE TABLE $table (
id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
employeeId INT(8) UNSIGNED,
payPeriodEnd INT(12) UNSIGNED,
description VARCHAR(30),
netPay DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
fedTax_emp DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
fica_emp DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
fica_com DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
futa_com DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
healthIns_emp DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
healthIns_com DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
retirement_emp DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
retirement_com DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
extraTaxWitholding_emp DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
garnishment_emp DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
otherWitholding_emp DECIMAL(10,2) UNSIGNED DEFAULT 0.00,
paycheckNum INT(10) UNSIGNED DEFAULT 0,
notUsed1 INT(2) UNSIGNED DEFAULT 0,
notUsed2 INT(2) UNSIGNED DEFAULT 0,
notUsed3 INT(2) UNSIGNED DEFAULT 0,
notUsed4 INT(2) UNSIGNED DEFAULT 0
)");
	$history->execute();
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
	return mail($email, 'Please verify your email address to access Simple Financials and Inventory', $message, $headers);
}

function sendPWResetEmail ($toId, $name, $email, $verifyCode)
{
	$link = hash('sha512', ($verifyCode . $name . $email), FALSE);
	$mess = "$name,\r\n\r\n
        There has been a request on the SFaI website for a password reset for this account.  If you initiated this request, click the link below, and you will be sent to a page where you will be able enter a new password. If you did not initiate this password reset request, simple ignore this email, and your password will not be changed.\r\n\r\n
        https://simplefinancialsandinventory.com/index.php?page=PWReset&id=$toId&ver=$link\r\n\r\n
        Thank you,\nAdmin\nSFaI";
	$message = wordwrap($mess, 70);
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
	$headers .= "From: SFaI Admin <admin@simplefinancialsandinventory.com>";
	return mail($email, 'Simple Financials and Inventory password reset request', $message, $headers);
}

function invCheck ($name, $uom, $myInventory, $time)
{
	$db = db_sfi();
	$getItemId = $db->prepare("SELECT id FROM $myInventory WHERE name=?");
	$getItemId->execute(array(
			$name
	));
	$getItemIdRow = $getItemId->fetch();
	if (! empty($getItemIdRow['id'])) {
		return $getItemIdRow['id'];
	} else {
		$setItem = $db->prepare("INSERT INTO $myInventory VALUES(NULL,?,?,'',?,'0','','','','0','2','0','0')");
		$setItem->execute(array(
				$time,
				$name,
				$uom
		));
		$getIId = $db->prepare("SELECT id FROM $myInventory WHERE name=? AND time=? ORDER BY id DESC LIMIT 1");
		$getIId->execute(array(
				$name,
				$time
		));
		$getIIdRow = $getIId->fetch();
		return $getIIdRow['id'];
	}
}

function catCheck ($name, $catSubOf, $myCategories)
{
	$db = db_sfi();
	$getCatId = $db->prepare("SELECT id FROM $myCategories WHERE category = ?");
	$getCatId->execute(array(
			$name
	));
	$getCatIdRow = $getCatId->fetch();
	if (! empty($getCatIdRow['id'])) {
		return $getCatIdRow['id'];
	} else {
		$setCat = $db->prepare("INSERT INTO $myCategories VALUES(NULL,?,?,'0','0')");
		$setCat->execute(array(
				$name,
				$catSubOf
		));
		$getCId = $db->prepare("SELECT id FROM $myCategories WHERE category = ? AND subOf = ? ORDER BY id DESC LIMIT 1");
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

function conCheck ($upContactName, $myContacts, $time, $vendor)
{
	$db = db_sfi();
	$getContact = $db->prepare("SELECT id FROM $myContacts WHERE name = ?");
	$getContact->execute(array(
			$upContactName
	));
	$gContactR = $getContact->fetch();
	$c = (! empty($gContactR)) ? $gContactR['id'] : 0;
	if ($c >= 1) {
		return $c;
	} else {
		$newContact = $db->prepare("INSERT INTO $myContacts VALUES(NULL,?,'','','','',?,'1',?,'0.00','0.00','0','0')");
		$newContact->execute(array(
				$upContactName,
				$time,
				$vendor
		));
		$getNewContactId = $db->prepare("SELECT id FROM $myContacts WHERE name = ? AND lastAccessed = ? ORDER BY id DESC LIMIT 1");
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

function getContact ($id, $myContacts)
{
	$db = db_sfi();
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

function getNext ($type, $table)
{
	$db = db_sfi();
	$next = 0;
	$get = $db->prepare("SELECT refNumber FROM $table WHERE typeCode = ? ORDER BY refNumber DESC LIMIT 1");
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

function selectNewUOM ($unitOfMeasure)
{
	$db = db_sfi();
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
		$getU = $db->prepare("SELECT id, unitOfMeasure FROM unitsOfMeasure WHERE type = ?");
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

function showHelpLeft ($t)
{
	$db = db_sfi();
	$get = $db->prepare("SELECT writeUp FROM helpPage WHERE id = ?");
	$get->execute(array(
			$t
	));
	$getR = $get->fetch();
	$tip = nl2br($getR['writeUp']);
	$text = "<div class='tooltipLeft'><image src='images/help.png' style='max-width:50px; max-height:50px;' alt=''><span class='tooltiptextLeft'>" . $tip . "</span></div>";
	return $text;
}

function showHelpRight ($t)
{
	$db = db_sfi();
	$get = $db->prepare("SELECT writeUp FROM helpPage WHERE id = ?");
	$get->execute(array(
			$t
	));
	$getR = $get->fetch();
	$tip = nl2br($getR['writeUp']);
	$text = "<div class='tooltipRight'><image src='images/help.png' style='max-width:20px; max-height:20px;' alt=''><span class='tooltiptextRight'>" . $tip . "</span></div>";
	return $text;
}

function payRate ($id, $clockIn, $myId)
{
	$db = db_sfi();
	$table = $myId . "__employeeTracking";
	$getP = $db->prepare("SELECT hourlyPayRate FROM $table WHERE employeeId = ? AND date <= ? AND hourlyPayRate > ? ORDER BY date DESC LIMIT 1");
	$getP->execute(array(
			$id,
			$clockIn,
			'0.00'
	));
	$getR = $getP->fetch();
	if ($getR) {
		return $getR['hourlyPayRate'];
	} else {
		return 0;
	}
}

function salaryPayRate ($id, $clockIn, $myId)
{
	$db = db_sfi();
	$table = $myId . "__employeeTracking";
	$getP = $db->prepare("SELECT salaryPayRate FROM $table WHERE employeeId = ? AND date <= ? AND salaryPayRate > ? ORDER BY date DESC LIMIT 1");
	$getP->execute(array(
			$id,
			$clockIn,
			'0.00'
	));
	$getR = $getP->fetch();
	if ($getR) {
		return $getR['salaryPayRate'];
	} else {
		return 0;
	}
}

function isHoliday ($clockIn, $clockOut, $myId)
{
	$db = db_sfi();
	$table = $myId . "__holidays";
	$amt = 0.00;
	$get = $db->prepare("SELECT startTime, endTime FROM $table");
	$get->execute();
	while ($getr = $get->fetch()) {
		$s = $getr['startTime'];
		$e = $getr['endTime'];
		if ($s <= $clockIn && $e <= $clockOut) {
			return ($e - $clockIn);
		} elseif ($s >= $clockIn && $e >= $clockOut) {
			return ($clockOut - $s);
		} elseif ($s >= $clockIn && $e <= $clockOut) {
			return ($e - $s);
		} elseif ($s <= $clockIn && $e >= $clockOut) {
			return ($clockOut - $clockIn);
		}
	}
	return 0;
}