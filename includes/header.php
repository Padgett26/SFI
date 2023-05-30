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
		if ($SA == 0) {
			?>
        <a href="index.php?page=journals" class="menu">Bank Registers</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <a href="index.php?page=reports" class="menu">Reports</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <a href="index.php?page=budget" class="menu">Budget</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <?php
		}
		?>
        <a href="index.php?page=inventory" class="menu">INV</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <?php
		if ($SA == 0) {
			?>
        <a href="index.php?page=recipes" class="menu">Recipes</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <a href="index.php?page=buy" class="menu">Buy</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <?php
		}
		?>
        <a href="index.php?page=sell" class="menu">Sell</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <a href="index.php?page=contacts" class="menu">Contacts</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <?php
	}
	if ($SA == 0) {
		?>
        <a href="index.php?page=settings" class="menu">Settings</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <?php
	}
	?>
        <a href="index.php?page=help" class="menu">Help</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <a id="feedbackLink" onclick="toggleview('feedbackBox')" class="menu">Feedback</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <a id="legalLink" onclick="toggleview('legalBox')" class="menu">Legal</a>&nbsp;&nbsp;<span class="mcat">|</span>&nbsp;&nbsp;
        <a href="index.php?logout=yep" class="menu">Log out</a>
    </div>
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
    <?php
	}
}
?>
    <div id='legalBox' style='display:none; text-align:justify; background-color: #ffffff; color: #000000; width:75%; padding:20px; margin:0px auto;'>
    <span style="font-size:1em;"><?php
				echo $legalText;
				?></span>
    </div>