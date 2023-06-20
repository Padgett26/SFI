<?php
if ($myId >= 1 && $companyName != "" && $companyName != " ") {
    ?>
<div style="width:100%; padding-top:40px; text-align: center; font-weight:bold; font-size:3em;"><span style="color: #bd4a11;">SFaI</span> || <?php
    echo $companyName;
    ?></div>
<?php
    if ($beta == 1) {
        echo "<div style='width:100%; padding-top:10px; text-align: center;'><a href='index.php?page=changeLog' style='text-decoration:none; color: #bd4a11; font-weight:bold; font-size:1.5em;'>Beta</a></div>";
    }
    echo "<div style='width:100%; padding:20px 0px;'>&nbsp;</div>";
} else {
    ?>
    <div style="width:100%; padding-top:40px; text-align:center; font-weight:bold; font-size:3em;"><span style="color: #bd4a11;">S</span>imple <span style="color: #bd4a11;">F</span>inancials and <span style="color: #bd4a11;">I</span>nventory</div>
    <?php
    if ($beta == 1) {
        echo "<div style='width:100%; padding-top:10px; text-align: center;'><a href='index.php?page=changeLog' style='text-decoration:none; color: #bd4a11; font-weight:bold; font-size:1.5em;'>Beta</a></div>";
    }
    echo "<div style='width:100%; padding:20px 0px;'>&nbsp;</div>";
}
if ($myId >= 1) {
    ?>
    <div style="text-align:center; font-weight:bold;">
        <a href="index.php?page=home" class="menu">Home</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <?php
    if ($fiscalYear != 0) {
        ?>
        <a href="index.php?page=inventory" class="menu">INV</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <a href="index.php?page=journals" class="menu">Bank Registers</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <a href="index.php?page=reports" class="menu">Reports</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <a href="index.php?page=budget" class="menu">Budget</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <a href="index.php?page=recipes" class="menu">Recipes</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <a href="index.php?page=buy" class="menu">Buy</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <a href="index.php?page=sell" class="menu">Sell</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <a href="index.php?page=contacts" class="menu">Contacts</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <?php
    }
    ?>
    <a href="index.php?page=settings" class="menu">Settings</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
    <a href="index.php?page=help" class="menu">Help</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
    <a id="feedbackLink" onclick="toggleview('feedbackBox')" class="menu">Feedback</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
    <a id="legalLink" onclick="toggleview('legalBox')" class="menu">Legal</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
    <a href="index.php?logout=yep" class="menu">Log out</a>
    </div>
    <div style="text-align:center; font-weight:bold; margin-top:20px;"><a id="quickEntryLink" onclick="toggleview('quickEntryBox')" class="menu">Quick Entry</a></div>
    <?php
} else {
    ?>
    <div style="text-align:center; font-weight:bold;">
    <a id="legalLink" onclick="toggleview('legalBox')" class="menu">Legal</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <a id="signInLink" onclick="toggleview('signInBox')" class="menu">Sign&nbsp;in&nbsp;/&nbsp;Register</a>
    </div>
    <?php
}
if ($loginErr != "x") {
    echo "<div id='signInBox' style='display:block; text-align:center; background-color: #ffffff; color: #000000; width:100%; padding:20px;'>";
    echo "<span style='color:red; font-weight:bold; font-size:1em; text-align:center;'>$loginErr</span><br /><br />";
} else {
    ?>
    <div id='signInBox' style='display:none; text-align:center; background-color: #ffffff; color: #000000; width:100%; padding:20px 0px;'>
    <form method='post' action='index.php'>
    <span class="heading" style="font-size:1.25em;">Sign&nbsp;In</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span style='margin-top:20px;'>Email</span>&nbsp;&nbsp;
    <input name='email' value='' type='email' autocomplete='on' placeholder='Email' required style='margin-left:10px;' />&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;
    <span style='margin-top:20px;'>Password</span>&nbsp;&nbsp;
    <input name='pwd' value='' type='password' placeholder='Password' required style='margin-left:10px; margin-top:10px;' />&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;
    <input type='hidden' name='login' value='1' />
    <input type='submit' style='margin-top:10px;' value=' Sign in ' />
    </form><br /><br />
    <a href='index.php?page=Register'>Register</a>&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;
    <a href='index.php?page=PWReset'>Forgot your password?</a>
    </div>
<?php
    if ($myId >= 1) {
        ?>
        <div id='feedbackBox' style='display:none; text-align:center; background-color: #ffffff; color: #000000; width:100%; padding:20px;'>
        <form method='post' action='index.php'>
        <span class="heading" style="font-size:1.25em;">Any questions, comments, or feedback?</span><br />
    	Email: <input type="email" name="fromEmail" required><br />
    	<textarea name="emailBody" cols="40" rows="5"></textarea><br />
    	<input type="hidden" name="sendFeedback" value="1"><input type="submit" value=" Send Feeback ">
        </form>
        </div>

        <div id='quickEntryBox' style='display:none; text-align:center; background-color: #ffffff; color: #000000; width:100%; padding:20px;'>
        <?php
        if ($usePayroll == 1) {
            ?>
            <form method='post' action='index.php'>
			<table style='border:1px solid black; margin:10px auto;'>
			<tr>
			<td style='text-align:center; font-weight:bold;' colspan='2'>Time Clock</td>
			</tr><tr>
			<td style='text-align:center;' colspan='2'>Employee&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php
            echo "<select name='employeeId' size='1' onchange='quickEmployee(this.value, $myId)'>\n";
            echo "<option value='0'></option>\n";
            $v = $db->prepare("SELECT id,name FROM $myEmployees ORDER BY name");
            $v->execute();
            while ($vr = $v->fetch()) {
                $id = $vr['id'];
                $name = $vr['name'];
                echo "<option value='$id'>$name</option>\n";
            }
            echo "</select>";
            ?></td></tr>
            <tr><td colspan='2'><div id='quickEmployee' style='text-align:center;'></div></td></tr>
			</table>
            </form>
            <?php
        }
        if ($useMilage == 1) {
            ?>
            <form method='post' action='index.php'>
			<table style='border:1px solid black; margin:10px auto;'>
			<tr>
			<td style='text-align:center; font-weight:bold;' colspan='3'>Record Milage</td>
			</tr><tr>
			<td style='text-align:center;'>Vehicle</td>
			<td style='text-align:center;'>Employee</td>
			<td style='text-align:center;'>Date</td>
			</tr><tr>
			<td style='text-align:center;'><select name='vehicleId' size='1' onchange="quickVehicle(this.value,<?php
            echo $myId;
            ?>)"><?php
            $v = $db->prepare(
                    "SELECT id,name FROM $myVehicles WHERE retired = '0' ORDER BY name");
            $v->execute();
            while ($vr = $v->fetch()) {
                echo "<option value='" . $vr['id'] . "'>" . $vr['name'] .
                        "</option>\n";
            }
            ?></select></td>
			<td style='text-align:center;'><div id='quickAssigned'><?php
            echo "<select name='employeeId' size='1'>\n";
            $v = $db->prepare("SELECT id,name FROM $myEmployees ORDER BY name");
            $v->execute();
            while ($vr = $v->fetch()) {
                $id = $vr['id'];
                $name = $vr['name'];
                echo "<option value='$id'>$name</option>\n";
            }
            echo "</select>";
            ?></div></td>
			<td style='text-align:center;'><input type='date' name='usageDate' value='<?php
            echo date("Y-m-d", $time);
            ?>'></td>
			</tr><tr>
			<td style='text-align:center;'>Milage Start</td>
			<td style='text-align:center;'>Milage End</td>
			<td style='text-align:center;'></td>
			</tr><tr>
			<td style='text-align:center;'><input type='number' name='milageBegin' min='0.0' step='0.1'></td>
			<td style='text-align:center;'><input type='number' name='milageEnd' min='0.0' step='0.1'></td>
			<td style='text-align:center;'><input type="hidden" name="quickMilageUp" value="1"><button>Record Milage</button></td>
			</tr>
			</table>
            </form>
            <?php
        }
        ?>
        <form method='post' action='index.php'>
		<table style='border:1px solid black; margin:10px auto;'>
		<tr>
		<td style='text-align:center; font-weight:bold;' colspan='2'>Ledger Entry</td>
        </tr><tr>
		<td style='text-align:right; padding-right:10px;'>Date</td>
		<td style='text-align:left; padding-left:10px;'><input type='date' name='qDate' value='<?php
        echo date("Y-m-d", $time);
        ?>'></td>
        </tr><tr>
		<td style='text-align:right; padding-right:10px;'>Contact</td>
		<td style='text-align:left; padding-left:10px;'><input type='text' name='qContactName' value='' placeholder='contact' onchange='getContactSelect("contactSelect", this.value, <?php
        echo $myId;
        ?>)'>
            <br />
            <select id='contactSelect' name='qContactNameSelect' size='1'>
            <?php
        echo "<option value='0'>Select Contact</option>";
        foreach ($CONTACTS as $k => $v) {
            echo "<option value='$k'>$v</option>\n";
        }
        ?>
        </select></td>
		</tr><tr>
		<td style=''text-align:right; padding-right:10px;''>Description</td>
		<td style='text-align:left; padding-left:10px;'><input type="text" name="qDescription" value=""></td>
		</tr><tr>
		<td style=''text-align:right; padding-right:10px;''>From Account</td>
		<td style='text-align:left; padding-left:10px;'><select name="qFromAcc" size="1">
        <?php
        foreach ($ACCOUNTS as $k => $v) {
            echo "<option value='" . $k . "'>" . $k . " - " . $v . "</option>\n";
        }
        ?>
    	</select></td>
		</tr><tr>
		<td style='text-align:right; padding-right:10px;'>To Account</td>
		<td style='text-align:left; padding-left:10px;'><select name="qToAcc" size="1">
        <?php
        foreach ($ACCOUNTS as $k => $v) {
            echo "<option value='" . $k . "'>" . $k . " - " . $v . "</option>\n";
        }
        ?>
    	</select></td>
    	</tr><tr>
		<td style='text-align:right; padding-right:10px;'>Amount</td>
		<td style='text-align:left; padding-left:10px;'><input type="number" name="qAmount" value="0.00" min="0.00" step="0.01"></td>
		</tr><tr>
		<td style='text-align:right; padding-right:10px;'>Pay Type</td>
		<td style='text-align:left; padding-left:10px;'><select name='qCCC' size='1'>
    	<?php
        for ($i = 0; $i < 4; ++ $i) {
            echo "<option value='$i'>$PAYTYPES[$i]</option>\n";
        }
        ?>
		</select></td>
		</tr><tr>
		<td style='text-align:right; padding-right:10px;'>Check #</td>
		<td style='text-align:left; padding-left:10px;'><input type='text' name='qCkNm' value='' placeholder='ck num' size='6'></td>
        </tr><tr>
		<td style='text-align:center;' colspan='2'><input type="hidden" name="quickTransUp" value="1"><button>Record Transaction</button></td>
		</tr>
		</table>
        </form>
        </div>
        <?php
    }
}
?>
    <div id='legalBox' style='display:none; text-align:justify; background-color: #ffffff; color: #000000; width:75%; padding:20px; margin:0px auto;'>
    <span style="font-size:1em;"><?php
    echo $legalText;
    ?></span>
    </div>