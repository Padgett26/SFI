<div class="heading">Recipes</div>
<?php
if ($myId >= 1) {
    $done = 0;
    $upUOM = 0;
    $upId = (filter_input(INPUT_GET, 'recipeId', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(
            INPUT_GET, 'recipeId', FILTER_SANITIZE_NUMBER_INT) : 0;
    if (filter_input(INPUT_POST, 'recipeId', FILTER_SANITIZE_STRING)) {
        $upId = filter_input(INPUT_POST, 'recipeId', FILTER_SANITIZE_NUMBER_INT);
        $upName = filter_var(htmlEntities(trim($_POST['name']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);
        $upDescription = filter_var(
                htmlEntities(trim($_POST['description']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);
        $upInstructions = filter_var(
                htmlEntities(trim($_POST['instructions']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);
        $upUOM = filter_input(INPUT_POST, 'upUOM', FILTER_SANITIZE_NUMBER_INT);
        $upCost = filter_input(INPUT_POST, 'cost', FILTER_SANITIZE_NUMBER_FLOAT,
                FILTER_FLAG_ALLOW_FRACTION);
        $upPrice = filter_input(INPUT_POST, 'price',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $upCatName = filter_var(
                htmlEntities(trim($_POST['categoryName']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);
        $upCatSelect = filter_input(INPUT_POST, 'categorySelect',
                FILTER_SANITIZE_NUMBER_INT);
        $upCatSubOf = filter_input(INPUT_POST, 'subOf',
                FILTER_SANITIZE_NUMBER_INT);
        $upCreate = filter_input(INPUT_POST, 'create',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $delId = (filter_input(INPUT_POST, 'delId', FILTER_SANITIZE_NUMBER_INT) ==
                1) ? $upId : "N";

        if ($delId == $upId) {
            $delS = $db->prepare("DELETE FROM $myRecipes WHERE id = ?");
            $delS->execute(array(
                    $upId
            ));
        } else {
            $catId = ($upCatSelect == 0) ? catCheck($upCatName, $upCatSubOf,
                    $myCategories) : $upCatSelect;

            $upItems = array();
            foreach ($_POST as $key => $val) {
                if (preg_match("/^invQty([0-9][0-9]*)$/", $key, $match)) {
                    $a = $match[1];
                    $upItems[$a][0] = filter_var($val,
                            FILTER_SANITIZE_NUMBER_FLOAT,
                            FILTER_FLAG_ALLOW_FRACTION);
                }
                if (preg_match("/^invName([0-9][0-9]*)$/", $key, $match)) {
                    $b = $match[1];
                    $upItems[$b][1] = filter_var(
                            htmlEntities(trim($val), ENT_QUOTES),
                            FILTER_SANITIZE_STRING);
                }
                if (preg_match("/^invNameSelect([0-9][0-9]*)$/", $key, $match)) {
                    $c = $match[1];
                    $upItems[$c][2] = filter_var($val,
                            FILTER_SANITIZE_NUMBER_INT);
                }
                if (preg_match("/^invUOM([0-9][0-9]*)$/", $key, $match)) {
                    $c = $match[1];
                    $upItems[$c][3] = filter_var($val,
                            FILTER_SANITIZE_NUMBER_INT);
                }
            }

            $upItemsString = "";

            foreach ($upItems as $v1) {
                if ($v1[0] > 0) {
                    $v1[1] = ($v1[2] == 0) ? invCheck($v1[1], $v1[3],
                            $myInventory, $time) : $v1[2];
                    for ($j = 0; $j < 2; ++ $j) {
                        $upItemsString .= $v1[$j];
                        $upItemsString .= ($j != 1) ? "," : ";";
                    }
                }
            }

            echo ($debugging == 1) ? "upItemsString " . $upItemsString . "<br/>" : "";

            if ($upItemsString != "") {
                $a = str_split($upItemsString);
                array_pop($a);
                $upItemsS = implode("", $a);
            } else {
                $upItemsS = "";
            }

            echo ($debugging == 1) ? "upItemsS " . $upItemsS . "<br/>" : "";

            if ($upId == 0) {
                $upNew = $db->prepare(
                        "INSERT INTO $myRecipes VALUES(NULL,?,?,?,?,'0.xxx',?,?,?,'0','0','0')");
                $upNew->execute(
                        array(
                                $upName,
                                $upDescription,
                                $upInstructions,
                                $upItemsS,
                                $catId,
                                $upCost,
                                $upPrice
                        ));
                $upNewGetId = $db->prepare(
                        "SELECT id FROM $myRecipes WHERE ingredients = ? ORDER BY id DESC LIMIT 1");
                $upNewGetId->execute(array(
                        $upItemsS
                ));
                $ungi = $upNewGetId->fetch();
                $upId = $ungi[0];
            } else {
                $update = $db->prepare(
                        "UPDATE $myRecipes SET name = ?, description = ?, instructions = ?, ingredients = ?, categoryId = ?, cost = ?, price = ? WHERE id = ?");
                $update->execute(
                        array(
                                $upName,
                                $upDescription,
                                $upInstructions,
                                $upItemsS,
                                $catId,
                                $upCost,
                                $upPrice,
                                $upId
                        ));
            }

            if ($debugging == 1) {
                echo (! empty($_FILES['image']['tmp_name'])) ? "image 1" : "image 0";
            }

            $imageName = "0.xxx";
            if (! empty($_FILES['image']['tmp_name'])) {
                $image = $_FILES["image"]["tmp_name"];
                list ($width, $height) = (getimagesize($image) != null) ? getimagesize(
                        $image) : null;
                if ($width != null && $height != null) {
                    $imageType = getPicType($_FILES["image"]['type']);
                    $imageName = $time . "." . $imageType;
                    processPic("cmPic/$myId", $imageName, $image, 800, 150);
                    $p1stmt = $db->prepare(
                            "UPDATE $myRecipes SET picture=? WHERE id=?");
                    $p1stmt->execute(array(
                            $imageName,
                            $upId
                    ));
                }
            }

            if ($upCreate > 0) {
                $getPic = $db->prepare(
                        "SELECT picture FROM $myRecipes WHERE id = ?");
                $getPic->execute(array(
                        $upId
                ));
                $getPicR = $getPic->fetch();
                $pic = $getPicR['picture'];
                $upInvId = invCheck($upName, $upUOM, $myInventory, $time);
                if ($upInvId >= 2) {
                    $upDone1 = $db->prepare(
                            "UPDATE $myInventory SET description = ?, unitOfMeasure = ?, quantity = quantity + ?, cost = ?, price = ?, picture = ?, categoryId = ?, recipeId = ? WHERE id = ?");
                    $upDone1->execute(
                            array(
                                    $upDescription,
                                    $upUOM,
                                    $upCreate,
                                    $upCost,
                                    $upPrice,
                                    $pic,
                                    $catId,
                                    $upId,
                                    $upInvId
                            ));
                } else {
                    $upDone3 = $db->prepare(
                            "INSERT INTO $myInventory VALUES(NULL,?,?,?,?,?,?,?,?,?,?,?,?)");
                    $upDone3->execute(
                            array(
                                    $time,
                                    $upName,
                                    $upDescription,
                                    $upUOM,
                                    $upCreate,
                                    $upCost,
                                    $upPrice,
                                    $pic,
                                    '0',
                                    $catId,
                                    $upId,
                                    '1'
                            ));
                    $upDone4 = $db->prepare(
                            "SELECT id FROM $myInventory WHERE name = ? ORDER BY id DESC LIMIT 1");
                    $upDone4->execute(array(
                            $upName
                    ));
                    $upDone4R = $upDone4->fetch();
                    $upInvId = $upDone4R['id'];
                }
                $upDone2 = $db->prepare(
                        "UPDATE $myRecipes SET invId = ? WHERE id = ?");
                $upDone2->execute(array(
                        $upInvId,
                        $upId
                ));

                foreach ($upItems as $v2) {
                    if ($v2[0] > 0) {
                        $sub = ($v2[0] * $upCreate);
                        $getQ = $db->prepare(
                                "SELECT quantity FROM $myInventory WHERE id = ?");
                        $getQ->execute(array(
                                $v2[1]
                        ));
                        $getQR = $getQ->fetch();
                        $quantity = ($getQR && ($getQR[0] - $sub) >= 0) ? ($getQR[0] -
                                $sub) : 0;

                        $updateInv = $db->prepare(
                                "UPDATE $myInventory SET quantity = ? WHERE id = ?");
                        $updateInv->execute(array(
                                $quantity,
                                $v2[1]
                        ));
                    }
                    if ($v2[1] == 1) {
                        $getP = $db->prepare(
                                "SELECT price FROM $myInventory WHERE id = ?");
                        $getP->execute(array(
                                '1'
                        ));
                        $getPR = $getP->fetch();
                        $laborPrice = $getPR['price'];
                        $value = ($laborPrice * $v2[0] * $upCreate);
                        $refN = getNext('3', $myFLedger);
                        // id, date, contact, description, cashCheckCC,
                        // checkNumber, accountNumber, debitAmount,
                        // creditAmount, refNumber, typeCode
                        $upLedger1 = $db->prepare(
                                "INSERT INTO $myFLedger VALUES(NULL,?,'1',?,'0','0',?,?,'0.00',?,?,'0','0','0')");
                        $upLedger1->execute(
                                array(
                                        $time,
                                        "Recipe Creation - Labor",
                                        '120.0',
                                        $value,
                                        $refN,
                                        '3'
                                ));
                        $upLedger2 = $db->prepare(
                                "INSERT INTO $myFLedger VALUES(NULL,?,'1',?,'0','0',?,'0.00',?,?,?,'0','0','0')");
                        $upLedger2->execute(
                                array(
                                        $time,
                                        "Recipe Creation - Labor",
                                        '220.0',
                                        $value,
                                        $refN,
                                        '3'
                                ));
                    }
                }

                $done = 1;
            }
        }
    }
    ?>
    <table style="width:100%; border:1px solid black;">
        <tr>
            <td style="width:300px; padding:5px; border:1px solid black;">
                <form id="frm0" action="index.php?page=recipes&recipeId=0" method="post">
                    <div style="line-height:1.5; font-weight:bold; text-decoration:none; cursor:pointer;" onclick="submitForm('0')">
                        <table style="width:300px;">
                            <tr>
                                <td style="text-align:left;">NEW RECIPE</td>
                                <td id="selected0" style="text-align:right;"><?php

    echo ($upId == 0) ? "<span style='font-weight:bold;'> >>> </span>" : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    ?></td>
                            </tr>
                        </table>
                    </div>
                </form>
                <?php
    $getRecipes = $db->prepare("SELECT id, name FROM $myRecipes ORDER BY name");
    $getRecipes->execute();
    while ($getR = $getRecipes->fetch()) {
        $rId = $getR['id'];
        $rName = html_entity_decode($getR['name'], ENT_QUOTES);
        ?>
                    <form id="frm<?php
        echo $rId;
        ?>" action="index.php?page=recipes&recipeId=<?php
        echo $rId;
        ?>" method="post">
                        <div style="line-height:1.5; font-weight:bold; text-decoration:none; cursor:pointer;" onclick="submitForm('<?php
        echo $rId;
        ?>')">
                            <table style="width:300px;">
                                <tr>
                                    <td style="text-align:left;"><?php
        echo $rName;
        ?></td>
                                    <td id="selected<?php
        echo $rId;
        ?>" style="text-align:right;"><?php
        echo ($upId == $rId) ? "<span style='font-weight:bold;'> >>> </span>" : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        ?></td>
                                </tr>
                            </table>
                        </div>
                    </form>
                    <?php
    }
    ?>
            </td>
            <td id="recipeEdit" style="padding:5px; border:1px solid black;">
            <div style="text-align:right;">
    <?php
    echo showHelpLeft(7);
    ?>
    </div>
                <?php
    if ($done == 1) {
        echo "<div style='text-align:center; font-weight:bold;'>" . $upCreate .
                " " . $UOM[$upUOM] . " of " . $upName .
                " have been added to your inventory, and the ingredients have been adjusted by how much of each you used.</div>";
    } else {
        $name = "";
        $description = "";
        $instructions = "";
        $ingredients = "";
        $picture = "0.xxx";
        $categoryId = "";
        $categoryName = "";
        $cost = "";
        $price = "";
        $invId = 0;
        $suggestedPrice = 0.00;
        $totalCost = 0;

        if ($upId >= 1) {
            $gi = $db->prepare("SELECT * FROM $myRecipes WHERE id = ?");
            $gi->execute(array(
                    $upId
            ));
            $giR = $gi->fetch();
            $name = html_entity_decode($giR['name'], ENT_QUOTES);
            $description = html_entity_decode($giR['description'], ENT_QUOTES);
            $instructions = html_entity_decode($giR['instructions'], ENT_QUOTES);
            $ingredients = $giR['ingredients'];
            $picture = $giR['picture'];
            $categoryId = $giR['categoryId'];
            $cost = $giR['cost'];
            $price = $giR['price'];
            $invId = $giR['invId'];

            $getcat = $db->prepare(
                    "SELECT category FROM $myCategories WHERE id = ?");
            $getcat->execute(array(
                    $categoryId
            ));
            $getcatR = $getcat->fetch();
            $categoryName = (! empty($getcatR)) ? html_entity_decode(
                    $getcatR['category'], ENT_QUOTES) : "";
        }

        if ($ingredients != "") {
            $items = explode(";", $ingredients);

            foreach ($items as $v) {
                $i = explode(",", $v);
                settype($i[0], "float");
                settype($i[1], "int");
                $getC = $db->prepare(
                        "SELECT price, cost FROM $myInventory WHERE id = ?");
                $getC->execute(array(
                        $i[1]
                ));
                $getCR = $getC->fetch();
                $p = $getCR[0];
                $c = $getCR[1];

                $suggestedPrice += ($p * $i[0]);
                $totalCost += ($i[1] != 1) ? ($c * $i[0]) : ($p * $i[0]);
                settype($suggestedPrice, "float");
                settype($totalCost, "float");
            }
        } else {
            $items = array();
        }
        if ($debugging == 1) {
            var_dump($items);
        }
        ?>
                    <form id="frm0" action="recipePrint.php?recipeId=<?php
        echo $upId;
        ?>" method="post"><button onclick="submitForm('0')"> Print </button></form>
                    <form action="index.php?page=recipes" method="post" enctype="multipart/form-data">
                        <table style="width:100%;">
                        <?php
        if ($upId >= 1) {
            ?>
                        <tr>
                                <td colspan="2" style="text-align:right; font-weight:bold;">Recipe # <?php

            echo $upId;
            ?>
                            </tr>
                        <?php
        }
        if ($invId >= 1) {
            ?>
                        <tr>
                                <td colspan="2" style="text-align:right; font-weight:bold;"><a href="index.php?page=inventory&grabInvId=<?php
            echo $invId;
            ?>">Creates Inventory # <?php

            echo $invId;
            ?></a>
                            </tr>
                        <?php
        }
        ?>
                            <tr style="border:1px solid black;">
                                <td>Recipe picture
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
                                    <input type="text" name="name" value="<?php

        echo $name;
        ?>" size="40">
                            </tr>
                            <tr>
                                <td>Description
                                <td><textarea name="description" cols="40" rows="5"><?php

        echo $description;
        ?></textarea>
                            </tr>
                            <tr>
                                <td>Instructions
                                <td><textarea name="instructions" cols="40" rows="5"><?php

        echo $instructions;
        ?></textarea>
                            </tr>
                            <tr>
                                <td>Total Cost
                                <td><div id="cost"><?php

        echo money_sfi($totalCost, $currency, $langCode);
        ?> each</div>
                                    <input id="costUp" type="hidden" name="cost" value="<?php

        echo $totalCost;
        ?>">
                            </tr>
                            <tr>
                                <td>Retail price
                                <td>
                                    <input type="number" name="price" value="<?php

        echo $price;
        ?>" step='.01' placeholder='0.00'><br />
                                    <?php
        echo "<div id='suggestedPrice'>Suggested retail based off of the price amounts in your inventory: " .
                money_sfi($suggestedPrice, $currency, $langCode) . " each.</div>";
        ?>

                            </tr>
                            <tr>
                                <td>Category
                                <td>Select: <select id='categorySelect' name='categorySelect' size='1'>
                                <option value="0"></option>
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
        echo $myId;
        ?>")' placeholder="New"> Sub-category of: <select name="subOf" size="1">
                                        <option value='0'>New Primary category</option>
                                        <?php
        foreach ($CATEGORIES as $k => $v) {
            echo "<option value='$k'>$v</option>\n";
        }
        ?>
                                    </select>
                            </tr>
                            <tr>
                                <td style="text-align:center;">
                                    <?php
        echo ($upId >= 1) ? "Delete this Recipe? <input type='checkbox' name='delId' value='1'>" : "";
        ?>
                                </td>
                                <td style="text-align:center;">
                                    <input id="id" type="hidden" name="recipeId" value="X<?php

        echo $upId;
        ?>">
                                    Create <input type='number' name='create' step='.01' size="8" value='0.00'> <select name="upUOM" size="1"><?php
        echo selectNewUOM('0');
        ?></select> of this recipe<br />
                                    <span style="font-size:.75em;">This will affect your inventory</span><br />
                                    <input type='submit' value=' Update Recipe '>
                            </tr>
                            <tr>
                                <td>Ingredients
                                <td>
                                    <table cellspacing="5px">
                                        <tr>
                                            <td style='width:200px; padding:5px; border:1px solid #000000; text-align:left;'>Item Name</td>
                                            <td style='width:100px; padding:5px; border:1px solid #000000; text-align:center;'>Qty</td>
                                            <td style='width:100px; padding:5px; border:1px solid #000000; text-align:center;'>Unit of Measure</td>
                                            <td style='width:100px; padding:5px; border:1px solid #000000; text-align:center;'></td>
                                        </tr>
                                        <?php
        echo "<tr>\n";
        echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input id='invName0' type='text' name='invName0' value='' onkeyup='getInvSelect(\"invSelect0\",this.value,$myId)'><br>\n";
        echo "<select id='invSelect0' name='invNameSelect0' size='1'>\n<option value='0'></option>\n";
        $getInvSelect = $db->prepare(
                "SELECT id, name FROM $myInventory ORDER BY name");
        $getInvSelect->execute();
        while ($gis = $getInvSelect->fetch()) {
            $gisId = $gis['id'];
            $gisName = html_entity_decode($gis['name'], ENT_QUOTES);
            echo "<option value='$gisId'>$gisName</option>\n";
        }
        echo "</select></td>\n";
        echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><input id='invQty0' type='number' name='invQty0' step='.01' value='' size='5'></td>\n";
        echo "<td style='padding:5px; border:1px solid #000000; text-align:center;'><select name='invUOM0'>";
        echo selectNewUOM('0');
        echo "</select></td>\n";
        echo "<td style='padding:5px; border:1px solid #000000; text-align:left;'><input type='submit' value=' Add Ingredient '></td>\n";
        echo "</tr>\n";
        if ($debugging == 1) {
            var_dump($items);
        }
        $k = 1;
        foreach ($items as $v) {
            if ($debugging == 1) {
                var_dump($v);
                echo "<br>";
            }
            $i = explode(",", $v);
            if ($debugging == 1) {
                var_dump($i);
                echo "<br>";
            }
            $getInvName = $db->prepare(
                    "SELECT name, cost, price, unitOfMeasure, quantity FROM $myInventory WHERE id = ?");
            $getInvName->execute(array(
                    $i[1]
            ));
            $ginRow = $getInvName->fetch();
            if ($ginRow) {
                $name = html_entity_decode($ginRow['name'], ENT_QUOTES);
                $cost = $ginRow['cost'];
                $price = $ginRow['price'];
                $showUOM = $ginRow['unitOfMeasure'];
                $quantity = $ginRow['quantity'];
                $total = ($i[0] * $cost);
                $tLabor = ($i[0] * $price);
                ?>
                                            <tr>
                                                <td style='width:200px; padding:5px; border:1px solid #000000; text-align:left;'><?php

                echo $name;
                ?><input id="id" type="hidden" name="invNameSelect<?php

                echo $k;
                ?>" value="<?php

                echo $i[1];
                ?>"></td>
                                                <td style='width:100px; padding:5px; border:1px solid #000000; text-align:center;'><input type='number' name='invQty<?php

                echo $k;
                ?>' step='.01' value='<?php

                echo $i[0];
                ?>' size='5'><?php

                echo " " . $UOM[$showUOM];
                ?><input type="hidden" name="invUOM<?php

                echo $k;
                ?>" value="<?php

                echo $showUOM;
                ?>"><br /><?php

                echo $quantity . " in Inv";
                ?></td>
                                                <td style='width:100px; padding:5px; border:1px solid #000000; text-align:center;'><?php

                echo ($i[1] != 1) ? money($total, $currency, $langCode) : money_sfi(
                        $tLabor, $currency, $langCode);
                ?></td>
                                                <td style='width:100px; padding:5px; border:1px solid #000000; text-align:center;'><input type='submit' value=' Edit Ingredient '></td>
                                            </tr>
                                            <?php
                $k ++;
            }
        }
        ?>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </form>
                <?php
    }
    ?>
            </td>
        </tr>
    </table>
    <?php
} else {
    echo "Please log in to view your recipes";
}