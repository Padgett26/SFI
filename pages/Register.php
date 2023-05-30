<div id='mainTableBox' style="padding:10px;">
    <?php
				$msg = "";
				$errorMsg = "";
				$name = "";
				$newEmail = "";
				if (filter_input ( INPUT_GET, 'ver', FILTER_SANITIZE_STRING )) {
					$ver = filter_input ( INPUT_GET, 'ver', FILTER_SANITIZE_STRING );
					$rId = filter_input ( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
					$stmt = $db->prepare ( "SELECT name, email, verifyCode FROM users WHERE id=?" );
					$stmt->execute ( array (
							$rId
					) );
					$row = $stmt->fetch ();
					if ($row) {
						$name = $row ['name'];
						$verifyCode = $row ['verifyCode'];
						$email = $row ['email'];
						$link = hash ( 'sha512', ($verifyCode . $name . $email), FALSE );
						if ($ver == $link) {
							$stmt2 = $db->prepare ( "UPDATE users SET verifyCode=?, accessLevel = ? WHERE id=?" );
							$stmt2->execute ( array (
									'0',
									"1",
									$rId
							) );
							for($i = 0, $c = count ( $tables ); $i < $c; ++ $i) {
								$Name = $rId . $tables [$i] ['name'];
								$t1 = $db->prepare ( "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?" );
								$t1->execute ( array (
										'INV_db',
										$Name
								) );
								$t1R = $t1->fetch ();
								$c1 = $t1R [0];
								if ($c1 == 0) {
									$tables [$i] ['function'] ( $Name, $db );
								}
							}
							if (! is_dir ( "cmPics/$rId" )) {
								mkdir ( "cmPics/$rId", 0777, true );
							}
							if (! is_dir ( "cmPics/$rId/thumb" )) {
								mkdir ( "cmPics/$rId/thumb", 0777, true );
							}
							if (! is_dir ( "cmPics/$rId/backups" )) {
								mkdir ( "cmPics/$rId/backups", 0777, true );
							}
							$msg .= "Thank you for verifying your email address.<br /><br />Please sign in above.<br /><br />You can now use the site.";
						} else {
							$msg .= "The link you followed in the verification email is no longer valid.  Please try again.";
						}
					}
				}

				// My Information processing
				if (filter_input ( INPUT_POST, 'myInfoUp', FILTER_SANITIZE_STRING ) == "new") {
					$myInfoUp = filter_input ( INPUT_POST, 'myInfoUp', FILTER_SANITIZE_STRING );
					$name = filter_var ( htmlEntities ( trim ( $_POST ['name'] ), ENT_QUOTES ), FILTER_SANITIZE_STRING );
					$pwd1 = filter_input ( INPUT_POST, 'pwd1', FILTER_SANITIZE_STRING );
					$pwd2 = filter_input ( INPUT_POST, 'pwd2', FILTER_SANITIZE_STRING );

					if (filter_input ( INPUT_POST, 'email', FILTER_VALIDATE_EMAIL )) {
						$newEmail = strtolower ( filter_input ( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL ) );
						$stmt = $db->prepare ( "SELECT COUNT(*) FROM users WHERE email=? && accessLevel >= ?" );
						$stmt->execute ( array (
								$newEmail,
								"1"
						) );
						$row = $stmt->fetch ();
						$email = ($row [0] >= 1) ? '0' : $newEmail;
						if ($email == '0') {
							$errorMsg = "The email you entered seems to already be in use.";
						} else {
							$cleanStmt = $db->prepare ( "DELETE FROM users WHERE email = ?" );
							$cleanStmt->execute ( array (
									$email
							) );

							if ($pwd1 != "" && $pwd1 != " " && $pwd1 === $pwd2) {
								$salt = mt_rand ( 100000, 999999 );
								$hidepwd = hash ( 'sha512', ($salt . $pwd1), FALSE );
								$stmt = $db->prepare ( "INSERT INTO users VALUES" . "(NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" );
								$stmt->execute ( array (
										$name,
										$email,
										$hidepwd,
										$salt,
										$time,
										$time,
										"0",
										"0",
										"0",
										"0"
								) );
								$stmt2 = $db->prepare ( "SELECT id FROM users WHERE email=? && password=? ORDER BY id DESC LIMIT 1" );
								$stmt2->execute ( array (
										$email,
										$hidepwd
								) );
								$row2 = $stmt2->fetch ();
								if ($row2) {
									$myInfoUp = $row2 ['id'];
								}
								$link = hash ( 'sha512', ($time . $name . $email), FALSE );
								$mess = "$name,\n\n";
								$mess .= "As a layer of security, we ask that you verify your email address before being allowed to post on the SFaI webpage.\n";
								$mess .= "The easiest way to do this is to click on the link below, this will update your status on the webpage.\n";
								$mess .= "If clicking on the link doesn't work, usually because html isn't enabled in your email client, you can also highlight the link below, copy it (ctrl + c), and then paste it (ctrl + v) in the address field of your web browser, and then hit enter.\n\n";
								$mess .= "https://simplefinancialsandinventory.com/index.php?page=Register&id=$myInfoUp&ver=$link\n\n";
								$mess .= "Thank you,\nAdmin\nSFaI";
								$headers = "MIME-Version: 1.0" . "\r\n";
								$headers .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
								$headers .= "From: SFaI Admin <admin@simplefinancialsandinventory.com>";
								mail ( $email, 'Please verify your email address to access Simple Financials and Inventory', $mess, $headers );
								$msg .= "A verification email has been sent to the address you provided - $email.<br /><br />In it is a link for you to click on, this will verify your email address, and will allow you to use this site.";
							} else {
								$errorMsg = "There was either no password entered, or your passwords did not match.";
							}
						}
					} else {
						$errorMsg = "Please enter a valid email address.";
					}
				}
				?>
    <header class="heading">
        Register for access
    </header>
    <article style="">
        <?php
								if ($msg != "") {
									echo "<div style='text-align:center; font-weight:bold; font-size:1.25em; margin-top:20px;'>$msg</div>";
								} else {
									?>
            <div style="">
                <form action="index.php?page=Register" method="post">
                    <table cellspacing='5px'>
                        <?php
									if ($errorMsg != "") {
										?>
                            <tr>
                                <td style="border:1px solid #aaaaaa; padding:10px;" colspan="2"><div style="text-align:center; color:red;"><?php

										echo $errorMsg;
										?></div></td>
                            </tr>
                        <?php
									}
									?>
                        <tr>
                            <td style="border:1px solid #aaaaaa; padding:10px;">Name</td><td style="border:1px solid #aaaaaa; padding:10px;"><input type="text" name="name" value="<?php

									echo $name;
									?>" max-length="30" size="30" required /></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #aaaaaa; padding:10px;">Email (used as your log in)</td><td style="border:1px solid #aaaaaa; padding:10px;"><input type="email" name="email" value="<?php

									echo $newEmail;
									?>" max-length="50" size="30" style="" required /><br /></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #aaaaaa; padding:10px;">Password</td><td style="border:1px solid #aaaaaa; padding:10px;"><input type="password" name="pwd1" value="" max-length="50" style="" size="30" required /> Enter once<br /><br /><input type="password" name="pwd2" value="" max-length="50" style="" size="30" required />and enter again</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #aaaaaa; padding:10px;" colspan="2"><div style="text-align:center;"><input type="hidden" name="myInfoUp" value="new" /><input type="submit" value=" Save " /></div></td>
                        </tr>
                    </table>
                </form>
            </div>
        <?php
								}
								?>
    </article>
</div>