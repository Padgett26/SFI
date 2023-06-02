<div class="heading">Inventory</div>
<?php
if ($myId >= 1) {
    $msg = "";
    $invId = filter_input(INPUT_GET, 'grabInvId', FILTER_SANITIZE_NUMBER_INT) ? filter_input(
            INPUT_GET, 'grabInvId', FILTER_SANITIZE_NUMBER_INT) : 0;

    if (filter_input(INPUT_POST, 'invId', FILTER_SANITIZE_STRING)) {
        $invId = filter_input(INPUT_POST, 'invId', FILTER_SANITIZE_NUMBER_INT);
        $upName = filter_var(htmlEntities(trim($_POST['name']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);
        $upDesc = filter_var(
                htmlEntities(trim($_POST['description']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);
        $upQuantity = filter_input(INPUT_POST, 'quantity',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $upUOM = filter_input(INPUT_POST, 'unitOfMeasure',
                FILTER_SANITIZE_NUMBER_INT);
        $upCost = filter_input(INPUT_POST, 'cost', FILTER_SANITIZE_NUMBER_FLOAT,
                FILTER_FLAG_ALLOW_FRACTION);
        $upPrice = filter_input(INPUT_POST, 'price',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $upCategoryName = (filter_input(INPUT_POST, 'categoryName',
                FILTER_SANITIZE_STRING)) ? filter_var(
                htmlEntities(trim($_POST['categoryName']), ENT_QUOTES),
                FILTER_SANITIZE_STRING) : "UNCATEGORIZED";
        $upSubOf = filter_input(INPUT_POST, 'subOf', FILTER_SANITIZE_NUMBER_INT);
        $upCategorySelect = filter_input(INPUT_POST, 'categorySelect',
                FILTER_SANITIZE_NUMBER_INT);
        $upContactName = (filter_input(INPUT_POST, 'contactName',
                FILTER_SANITIZE_STRING)) ? filter_var(
                htmlEntities(trim($_POST['contactName']), ENT_QUOTES),
                FILTER_SANITIZE_STRING) : "Vendors";
        $upContactSelect = filter_input(INPUT_POST, 'contactSelect',
                FILTER_SANITIZE_NUMBER_INT);
        $upTaxed = (filter_input(INPUT_POST, 'taxed', FILTER_SANITIZE_NUMBER_INT) ==
                "1") ? "1" : "0";
        $delId = (filter_input(INPUT_POST, 'delId', FILTER_SANITIZE_NUMBER_INT) ==
                "1") ? "1" : "0";
        settype($upQuantity, "float");
        settype($upCost, "float");
        settype($upPrice, "float");
        $value = ($upQuantity * $upCost);

        $category = ($upCategorySelect == '0') ? catCheck($upCategoryName,
                $upSubOf, $myCategories) : $upCategorySelect;

        $contact = ($upContactSelect == '0') ? conCheck($upContactName,
                $myContacts, $time, '1') : $upContactSelect;

        if ($delId == "1") {
            $delInv = $db->prepare(
                    "UPDATE $myInventory SET categoryId = ? WHERE id = ?");
            $delInv->execute(array(
                    '3',
                    $invId
            ));
        } else {
            $xNum = getNext('4', $myFLedger);
            if ($invId == 0) {
                $newInv = $db->prepare(
                        "INSERT INTO $myInventory VALUES(NULL,?,?,?,?,?,?,?,'0.xxx',?,?,'0','0')");
                $newInv->execute(
                        array(
                                $time,
                                $upName,
                                $upDesc,
                                $upUOM,
                                $upQuantity,
                                $upCost,
                                $upPrice,
                                $contact,
                                $category
                        ));

                $newInvGetId = $db->prepare(
                        "SELECT id FROM $myInventory WHERE time = ? && name = ? ORDER BY id DESC LIMIT 1");
                $newInvGetId->execute(array(
                        $time,
                        $upName
                ));
                $newInvGetIdR = $newInvGetId->fetch();
                $invId = $newInvGetIdR['id'];

                if ($value >= 0.01) {
                    // id, date, contact, description, cashCheckCC, checkNumber,
                    // accountNumber, debitAmount, creditAmount, refNumber,
                    // typeCode, dailyConfirm, notUsed1, notUsed2
                    $upLedger1 = $db->prepare(
                            "INSERT INTO $myFLedger VALUES(NULL,?,'1',?,'0','0',?,?,'0.00',?,?,'0','0','0')");
                    $upLedger1->execute(
                            array(
                                    $time,
                                    "Add Inv Item - $upName",
                                    '120.0',
                                    $value,
                                    $xNum,
                                    '4'
                            ));
                    $upLedger2 = $db->prepare(
                            "INSERT INTO $myFLedger VALUES(NULL,?,'1',?,'0','0',?,'0.00',?,?,?,'0','0','0')");
                    $upLedger2->execute(
                            array(
                                    $time,
                                    "Add Inv Item - $upName",
                                    '400.3',
                                    $value,
                                    $xNum,
                                    '4'
                            ));
                }
            } else {
                $getInvQty = $db->prepare(
                        "SELECT quantity, cost FROM $myInventory WHERE id = ?");
                $getInvQty->execute(array(
                        $invId
                ));
                $giqR = $getInvQty->fetch();
                $q = $giqR['quantity'];
                $c = $giqR['cost'];
                if ($value > ($q * $c)) {
                    $diff = ($value - ($q * $c));
                    // id, date, contact, description, cashCheckCC, checkNumber,
                    // accountNumber, debitAmount, creditAmount, refNumber,
                    // typeCode
                    $upLedger1 = $db->prepare(
                            "INSERT INTO $myFLedger VALUES(NULL,?,'1',?,'0','0',?,?,'0.00',?,?,'0','0','0')");
                    $upLedger1->execute(
                            array(
                                    $time,
                                    "Edit Inv Item - $upName",
                                    '120.0',
                                    $diff,
                                    $xNum,
                                    '4'
                            ));
                    $upLedger2 = $db->prepare(
                            "INSERT INTO $myFLedger VALUES(NULL,?,'1',?,'0','0',?,'0.00',?,?,?,'0','0','0')");
                    $upLedger2->execute(
                            array(
                                    $time,
                                    "Edit Inv Item - $upName",
                                    '400.3',
                                    $diff,
                                    $xNum,
                                    '4'
                            ));
                } elseif ($value < ($q * $c)) {
                    $diff = (($q * $c) - $value);
                    $upLedger1 = $db->prepare(
                            "INSERT INTO $myFLedger VALUES(NULL,?,'1',?,'0','0',?,'0.00',?,?,?,'0','0','0')");
                    $upLedger1->execute(
                            array(
                                    $time,
                                    "Edit Inv Item - $upName",
                                    '120.0',
                                    $diff,
                                    $xNum,
                                    '4'
                            ));
                    $upLedger2 = $db->prepare(
                            "INSERT INTO $myFLedger VALUES(NULL,?,'1',?,'0','0',?,?,'0.00',?,?,'0','0','0')");
                    $upLedger2->execute(
                            array(
                                    $time,
                                    "Edit Inv Item - $upName",
                                    '400.3',
                                    $diff,
                                    $xNum,
                                    '4'
                            ));
                }
                $updateInv = $db->prepare(
                        "UPDATE $myInventory SET time = ?, name = ?, description = ?, unitOfMeasure = ?, quantity = ?, cost = ?, price = ?, contactId = ?, categoryId = ?, taxed = ? WHERE id = ?");
                $updateInv->execute(
                        array(
                                $time,
                                $upName,
                                $upDesc,
                                $upUOM,
                                $upQuantity,
                                $upCost,
                                $upPrice,
                                $contact,
                                $category,
                                $upTaxed,
                                $invId
                        ));
            }
        }

        if (isset($_FILES["image"]["tmp_name"]) &&
                $_FILES["image"]["size"] >= 1000) {
            $image = $_FILES["image"]["tmp_name"];
            list ($width, $height) = (getimagesize($image) != null) ? getimagesize(
                    $image) : null;
            if ($width != null && $height != null) {
                $imageType = getPicType($_FILES["image"]['type']);
                $imageName = $time . "." . $imageType;
                processPic("cmPic/$myId", $imageName, $image, 800, 150);
                $p1stmt = $db->prepare(
                        "UPDATE $myInventory SET picture=? WHERE id=?");
                $p1stmt->execute(array(
                        $imageName,
                        $invId
                ));
            }
        }
        $msg = "Changes saved";
    }

    $openCat = 0;
    $oc = $db->prepare("SELECT categoryId FROM $myInventory WHERE id = ?");
    $oc->execute(array(
            $invId
    ));
    $ocR = $oc->fetch();
    if ($ocR) {
        $openCat = $ocR['categoryId'];
    }

    $getRcount = $db->prepare(
            "SELECT id FROM $myInventory ORDER BY id DESC LIMIT 1");
    $getRcount->execute();
    $getRc = $getRcount->fetch();
    $topRid = $getRc['id'];
    ?>
    <table style="width:100%; border:1px solid black;">
        <tr>
            <td style="width:300px; padding:5px; border:1px solid black;">
                <form id="frm0" action="index.php?page=inventory&grabInvId=0" method="post">
                    <div style="line-height:1.5; font-weight:bold; text-decoration:none; cursor:pointer;" onclick="submitForm('0')">
                        <table style="width:300px;">
                            <tr>
                                <td style="text-align:left;">NEW INV ITEM</td>
                                <td id="invSelected0" style="text-align:right;"><?php

    echo ($invId == 0) ? " >>> " : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    ?></td>
                            </tr>
                        </table>
                    </div>
                </form>
                <?php
    $getCats = $db->prepare(
            "SELECT id, category FROM $myCategories WHERE subOf = ? ORDER BY category");
    $getCats->execute(array(
            "0"
    ));
    while ($gic = $getCats->fetch()) {
        $catId = $gic['id'];
        $catCategory = strtoupper(
                html_entity_decode($gic['category'], ENT_QUOTES));
        ?>
                    <div class="cat" style="padding:5px; font-weight:bold; text-decoration:none; cursor:pointer;" onclick="toggleview('cat<?php
        echo $catId;
        ?>')">
                        <?php
        echo $catCategory;
        ?>
                    </div>
                    <div id='cat<?php
        echo $catId;
        ?>' style='display:<?php
        echo ($catId == $openCat) ? "block" : "none";
        ?>;'>
                        <?php
        $getCats2 = $db->prepare(
                "SELECT id, category FROM $myCategories WHERE subOf = ? ORDER BY category");
        $getCats2->execute(array(
                $catId
        ));
        while ($gic2 = $getCats2->fetch()) {
            $catId2 = $gic2['id'];
            $catCategory2 = strtoupper(
                    html_entity_decode($gic2['category'], ENT_QUOTES));
            ?>
                            <div class="subcat" style="padding:10px; font-weight:bold; text-decoration:none; cursor:pointer;" onclick="toggleview('subCat<?php
            echo $catId2;
            ?>')">
                                <?php
            echo $catCategory2;
            ?>
                                </div><div id='subCat<?php
            echo $catId2;
            ?>' style='display:<?php
            echo ($catId2 == $openCat) ? "block" : "none";
            ?>;'>
                                    <?php
            $getInv1 = $db->prepare(
                    "SELECT id, name, unitOfMeasure, quantity, recipeId FROM $myInventory WHERE categoryId = ? ORDER BY name");
            $getInv1->execute(array(
                    $catId2
            ));
            while ($gi1 = $getInv1->fetch()) {
                $iId = $gi1['id'];
                $iName = html_entity_decode($gi1['name'], ENT_QUOTES);
                $iUOM = $UOM[$gi1['unitOfMeasure']];
                $iQty = $gi1['quantity'];
                $iRId = $gi1['recipeId'];

                $inInv = ($iQty > '.01') ? "bold" : "normal";
                ?>
                                        <form id="frm<?php
                echo $iId;
                ?>" action="index.php?page=inventory&grabInvId=<?php
                echo $iId;
                ?>" method="post">
                                            <div style="line-height:1.5; font-weight:<?php
                echo $inInv;
                ?>; text-decoration:none; cursor:pointer;" onclick="submitForm('<?php
                echo $iId;
                ?>')">
                                                <table style="width:300px;">
                                                    <tr>
                                                        <td style="text-align:left;"><?php
                echo ($iRId >= 1) ? "<a href='index.php?page=recipes&recipeId=$iRId' style='text-decoration:none;'>(R)</a> " : "";
                echo $iName;
                ?></td>
                                                        <td id="invSelected<?php
                echo $iId;
                ?>" style="text-align:right;"><?php
                echo $iQty . " " . $iUOM;
                echo ($invId == $iId) ? " >>> " : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </form>
                                        <?php
            }
            ?>
                            </div>
                            <?php
        }
        $getInv2 = $db->prepare(
                "SELECT id, name, unitOfMeasure, quantity, recipeId FROM $myInventory WHERE categoryId = ? ORDER BY name");
        $getInv2->execute(array(
                $catId
        ));
        while ($gi2 = $getInv2->fetch()) {
            $iId = $gi2['id'];
            $iName = html_entity_decode($gi2['name'], ENT_QUOTES);
            $iUOM = $UOM[$gi2['unitOfMeasure']];
            $iQty = $gi2['quantity'];
            $iRId = $gi2['recipeId'];

            $inInv = ($iQty > '.01') ? "bold" : "normal";
            ?>
                            <form id="frm<?php
            echo $iId;
            ?>" action="index.php?page=inventory&grabInvId=<?php
            echo $iId;
            ?>" method="post">
                                <div style="line-height:1.5; font-weight:<?php
            echo $inInv;
            ?>; text-decoration:none; cursor:pointer;" onclick="submitForm('<?php
            echo $iId;
            ?>')">
                                    <table style="width:300px;">
                                        <tr>
                                            <td style="text-align:left;"><?php
            echo ($iRId >= 1) ? "<a href='index.php?page=recipes&recipeId=$iRId' style='text-decoration:none;'>(R)</a> " : "";
            echo $iName;
            ?></td>
                                            <td id="invSelected<?php
            echo $iId;
            ?>" style="text-align:right;"><?php
            echo $iQty . " " . $iUOM;
            echo ($invId == $iId) ? " >>> " : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </form>
                            <?php
        }
        ?>
                    </div>
                    <?php
    }
    ?>
            </td>
            <td style="padding:5px; border:1px solid black;">
            <div style="text-align:right;">
    <?php
    echo showHelpLeft(6) . " " . showHelpLeft(19);
    ?>
    </div>
                <?php
    $name = "";
    $description = "";
    $unitOfMeasure = 0;
    $quantity = 0.00;
    $cost = 0.00;
    $price = 0.00;
    $picture = "0.xxx";
    $contactId = 0;
    $categoryId = 0;
    $contactName = "";
    $categoryName = "";
    $recipeId = 0;
    $taxed = 1;

    if ($invId >= 1) {
        $gi = $db->prepare("SELECT * FROM $myInventory WHERE id = ?");
        $gi->execute(array(
                $invId
        ));
        $giR = $gi->fetch();
        $name = html_entity_decode($giR['name'], ENT_QUOTES);
        $description = html_entity_decode($giR['description'], ENT_QUOTES);
        $unitOfMeasure = $giR['unitOfMeasure'];
        $quantity = $giR['quantity'];
        $cost = $giR['cost'];
        $price = $giR['price'];
        $picture = $giR['picture'];
        $contactId = $giR['contactId'];
        $contactName = getContact($contactId, $myContacts);
        $categoryId = $giR['categoryId'];
        $recipeId = $giR['recipeId'];
        $taxed = $giR['taxed'];
    }

    echo ($msg != "") ? "<div style='font-weight:bold;'>" . $msg . "</div>" : "";
    ?>
                <form action="index.php?page=inventory" method="post" enctype="multipart/form-data">
                    <table style="width:100%;">
                    <?php
    if ($recipeId >= 1) {
        ?>
                    <tr>
                            <td colspan="2" style="text-align:right;"><a href="index.php?page=recipes&recipeId=<?php
        echo $recipeId;
        ?>">Created from recipe # <?php
        echo $recipeId;
        ?></a>
                        </tr>
                        <?php
    }
    ?>
                        <tr style="border:1px solid black;">
                            <td>Inventory item picture
                            <td>
                                <?php
    if (file_exists("cmPics/$myId/$picture")) {
        echo "<image src='cmPics/$myId/$picture' style='max-width:200px; max-height:200px;'><br />";
        echo 'Delete this picture: <input type="checkbox" name="delPic" value="1"><br /><br />';
    }
    ?>
                                Upload a new picture<br />
                                <input type="file" name="image"><br />
                        </tr>
                        <tr>
                            <td>Name
                            <td>
                                <?php
    if ($invId == 1) {
        echo "$name<input type='hidden' name='name' value='Labor'>";
    } else {
        ?>
                                    <input type="text" name="name" value="<?php
        echo $name;
        ?>" size="40">
                                    <?php
    }
    ?>
                        </tr>
                        <tr>
                            <td>Description
                            <td><textarea name="description" cols="40" rows="5"><?php
    echo $description;
    ?></textarea>
                        </tr>
                        <tr>
                            <td>Quantity on hand
                            <td><input type="number" name="quantity" value="<?php
    echo $quantity;
    ?>" step='.01' placeholder='0.00'>
                        </tr>
                        <tr>
                            <td>Unit of measure
                            <td>
                                <?php
    if ($invId == 1) {
        echo "HOUR<input type='hidden' name='unitOfMeasure' value='3'>";
    } else {
        ?>
                                    <select name="unitOfMeasure" size="1"><?php
        echo selectNewUOM($unitOfMeasure);
        ?>
                                    </select>
                                    <?php
    }
    ?>
                        </tr>
                        <tr>
                            <td>Cost per unit
                            <td>
                            <?php
    if ($invId == 1) {
        echo "0.00<input type='hidden' name='cost' value='0.00'>";
    } else {
        ?>
                            <input type="number" name="cost" value="<?php

        echo $cost;
        ?>" step='.01' placeholder='0.00'>
	<?php
    }
    ?>
                        </tr>
                        <tr>
                            <td>Taxable Item
                            <td><input type="checkbox" name="taxed" value="1"<?php
    echo ($taxed == 1 && $invId != 1) ? " checked" : "";
    ?>>
	<?php
    echo ($taxed == 0 || $invId == 1) ? " <image src='images/noTax.png' style='max-height:30px; max-width:30px;'>" : "";
    ?>
                        </tr>
                        <tr>
                            <td>Retail price
                            <td>
                                <?php
    echo ($invId != 1) ? "Suggested retail based off of the " . ($markUp * 100) .
            "% mark-up in settings: " .
            money_sfi((1 + $markUp) * $cost, $currency, $langCode) . "<br />" : "";
    ?>
                                <input type="number" name="price" value="<?php
    echo $price;
    ?>" step='.01' placeholder='0.00'>
                        </tr>
                        <?php
    if ($invId != 1) {
        ?>
                            <tr>
                                <td>Category
                                <td>Select: <select id='categorySelect' name='categorySelect' size='1'>
                                        <option value='0' selected></option>
                                        <?php
        $getCategorySelect = $db->prepare(
                "SELECT * FROM $myCategories WHERE subOf = ? ORDER BY category");
        $getCategorySelect->execute(array(
                '0'
        ));
        while ($gcs = $getCategorySelect->fetch()) {
            $gcsId = $gcs['id'];
            $gcsName = html_entity_decode($gcs['category'], ENT_QUOTES);
            echo "<option value='$gcsId'";
            echo ($categoryId == $gcsId) ? " selected" : "";
            echo ">$gcsName</option>\n";

            $getCategorySelectSub = $db->prepare(
                    "SELECT * FROM $myCategories WHERE subOf = ? ORDER BY category");
            $getCategorySelectSub->execute(array(
                    $gcsId
            ));
            while ($gcsSub = $getCategorySelectSub->fetch()) {
                $gcsIdSub = $gcsSub['id'];
                $gcsNameSub = html_entity_decode($gcsSub['category'], ENT_QUOTES);
                echo "<option value='$gcsIdSub'";
                echo ($categoryId == $gcsIdSub) ? " selected" : "";
                echo "> -$gcsNameSub</option>\n";
            }
        }
        ?>
                                    </select><br />
                                    <input type='text' name='categoryName' value='<?php
        echo $categoryName;
        ?>' onkeyup='getCategorySelect("categorySelect", this.value, "<?php
        echo $myCategories;
        ?>")' placeholder="New"> Sub-category of: <select name="subOf" size="1">
                                        <option value='0'>New Primary category</option>
                                        <?php
        $newCatSelect = $db->prepare(
                "SELECT * FROM $myCategories WHERE subOf = ? ORDER BY category");
        $newCatSelect->execute(array(
                '0'
        ));
        while ($ncs = $newCatSelect->fetch()) {
            $ncsId = $ncs['id'];
            $ncsName = html_entity_decode($ncs['category'], ENT_QUOTES);
            echo "<option value='$ncsId'>$ncsName</option>\n";
        }
        ?>
                                    </select>
                            </tr>
                            <tr>
                                <td>Source
                                <td>Select: <select id='contactSelect' name='contactSelect' size='1'>
                                        <option value='0' selected></option>
                                        <?php
        foreach ($CONTACTS as $k => $v) {
            echo "<option value='$k'";
            echo ($contactId == $k) ? " selected" : "";
            echo ">$v</option>\n";
        }
        ?>
                                    </select><br />
                                    <input type='text' name='contactName' value='' onkeyup='getContactSelect("contactSelect", this.value, "<?php
        echo $myContacts;
        ?>")' placeholder="New">
                            </tr>
                            <?php
    } else {
        echo "<input type='hidden' name='categorySelect' value='1'>\n";
        echo "<input type='hidden' name='subOf' value='0'>\n";
        echo "<input type='hidden' name='contactSelect' value='0'>\n";
    }
    ?>
                        <tr>
                            <td style="text-align:center;" colspan="2">
                                <?php
    echo ($invId <= 1) ? "" : "Hide this Inv item? <input type='checkbox' name='delId' value='1'><br /><br />";
    echo ($invId >= 1) ? "<input type='submit' value=' Update Inv Item '>" : "<input type='submit' value=' Add New Inv Item '>";
    ?>
                                <input type="hidden" name="invId" value="X<?php
    echo $invId;
    ?>">
                        </tr>
                    </table>
                </form>
            </td>
        </tr>
    </table>
    <?php
} else {
    echo "Please log in to see your inventory";
}