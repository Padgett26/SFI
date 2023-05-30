<?php
include "cgi-bin/config.php";
include "cgi-bin/functions.php";
?>
<!DOCTYPE HTML>
<head>
    <?php
				include "includes/head.php";
				?>
</head>
<body>
    <div id="printArea">
        <div style="width:100%; padding-top: 20px; padding-bottom: 20px; text-align: center; font-weight:bold; font-size:2em;">Recipes</div>
        <?php
								if ($myId >= 1) {
									$upId = (filter_input ( INPUT_GET, 'recipeId', FILTER_SANITIZE_NUMBER_INT )) ? filter_input ( INPUT_GET, 'recipeId', FILTER_SANITIZE_NUMBER_INT ) : 0;
									$qty = (filter_input ( INPUT_POST, 'qty', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) >= 0.01) ? filter_input ( INPUT_POST, 'qty', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : 1;

									$name = "";
									$description = "";
									$instructions = "";
									$ingredients = "";
									$picture = "";
									$categoryName = "";
									$cost = "";
									$price = "";
									$suggestedPrice = 0;
									$totalCost = 0;

									if ($upId >= 1) {
										$gi = $db->prepare ( "SELECT * FROM $myRecipes WHERE id = ?" );
										$gi->execute ( array (
												$upId
										) );
										$giR = $gi->fetch ();
										$name = $giR ['name'];
										$description = $giR ['description'];
										$instructions = $giR ['instructions'];
										$ingredients = $giR ['ingredients'];
										$picture = $giR ['picture'];
										$categoryName = $CATEGORIES [$giR ['categoryId']];
										$cost = $giR ['cost'];
										$price = ($giR ['price'] * $qty);
									}

									if ($ingredients != "") {
										$items = explode ( ";", $ingredients );

										foreach ( $items as $v ) {
											$i = explode ( ",", $v );
											$getC = $db->prepare ( "SELECT price, cost FROM $myInventory WHERE id = ?" );
											$getC->execute ( array (
													$i [1]
											) );
											$getCR = $getC->fetch ();
											$p = $getCR [0];
											$c = $getCR [1];

											$suggestedPrice += ($p * $i [0] * $qty);
											$totalCost += ($c * $i [0] * $qty);
										}
									} else {
										$items = array ();
									}
									?>

            <table style="width:100%;">
            <tr>
                    <td colspan="2" style="text-align:right; font-weight:bold;">Recipe # <?php
									echo $upId;
									?>
                </tr>
                <?php
									if (file_exists ( "cmPics/$myId/$picture" )) {
										?>
                    <tr style="border:1px solid black;">
                        <td>Recipe picture
                        <td><image src='<?php
										echo "cmPics/$myId/$picture";
										?>' alt="" style='max-width:200px; max-height:200px;'>
                    </tr>
                    <?php
									}
									?>
                <tr>
                    <td>Name
                    <td><?php
									echo $name;
									?>
                </tr>
                <tr>
                    <td>Description
                    <td><?php
									echo $description;
									?>
                </tr>
                <tr>
                    <td>Instructions
                    <td><?php
									echo $instructions;
									?>
                </tr>
                <tr>
                    <td>Total Cost
                    <td><div id="cost"><?php
									echo money ( $totalCost, $currency, $langCode );
									?></div>
                </tr>
                <tr>
                    <td>Retail price
                    <td><?php
									echo $price;
									?><br />
                        <?php
									echo "<div id='suggestedPrice'>Suggested retail based off of the price amounts in your inventory: " . money ( $suggestedPrice, $currency, $langCode ) . ".</div>";
									?>
                </tr>
                <tr>
                    <td>Category
                    <td><?php
									echo $categoryName;
									?>
                </tr>
                <tr>
                    <td colspan='2' style='text-align:center;'>
                    <form action='recipePrint.php?recipeId=<?php
									echo $upId;
									?>' method='post'>Adjust quanties to create <input type='number' name='qty' value='<?php
									echo $qty;
									?>' step='0.01' min='0.01'> <input type='submit' value=' Update '></form>
                </tr>
                <tr>
                    <td>Ingredients
                    <td>
                        <table cellspacing="5px">
                            <tr>
                                <td style='width:200px; padding:5px; text-align:left;'>Item Name</td>
                                <td style='width:100px; padding:5px; text-align:center;'>Qty</td>
                                <td style='width:100px; padding:5px; text-align:center;'>Total Cost</td>
                            </tr>
                            <?php
									foreach ( $items as $k => $v ) {
										$i = explode ( ",", $v );
										$getInvName = $db->prepare ( "SELECT t1.name, t1.cost, t1.quantity, t2.unitOfMeasure FROM $myInventory AS t1 LEFT JOIN unitsOfMeasure AS t2 ON t1.unitOfMeasure = t2.id WHERE t1.id = ?" );
										$getInvName->execute ( array (
												$i [1]
										) );
										$ginRow = $getInvName->fetch ();
										$name = $ginRow [0];
										$cost = $ginRow [1];
										$onhand = $ginRow [2];
										$showUOM = $ginRow [3];
										$total = ($i [0] * $cost * $qty);
										?>
                                <tr>
                                    <td style='width:200px; padding:5px; border-top:1px solid #000000; text-align:left;'><?php
										echo $name;
										?></td>
                                    <td style='width:100px; padding:5px; border-top:1px solid #000000; text-align:center;'><?php
										echo ($i [0] * $qty) . " " . $showUOM;
										?><br><span style="font-size:.75em;"><?php
										echo $onhand . " in Inv";
										?></span></td>
                                    <td style='width:100px; padding:5px; border-top:1px solid #000000; text-align:center;'><?php
										echo money ( $total, $currency, $langCode );
										?></td>
                                </tr>
                                <?php
									}
									?>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:left; font-weight:bold;"><button onclick='window.history.back()'> Back </button><br /><a href="index.php?page=home" style="text-decoration:none; color:#000000;">SFaI Home</a></td>
                    <td style="text-align:left; font-weight:bold;"><button onclick='window.print()'> Print </button></td>
                </tr>
            </table>
            <?php
								} else {
									echo "Please log in to view your recipes";
								}
								?>
    </div>
</body>
</html>