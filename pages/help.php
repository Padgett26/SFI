<?php
$helpId = (filter_input ( INPUT_GET, 'helpId', FILTER_SANITIZE_NUMBER_INT )) ? filter_input ( INPUT_GET, 'helpId', FILTER_SANITIZE_NUMBER_INT ) : 1;
?>
<table style="border:1px solid #000000;" cellspacing="5px">
    <tr>
        <td style="width:300px; padding:5px; border:1px solid #000000;">
        <?php
								$getPage = $db->prepare ( "SELECT DISTINCT page FROM helpPage ORDER BY page" );
								$getPage->execute ();
								while ( $getPageR = $getPage->fetch () ) {
									$page = $getPageR ['page'];
									$upage = strtoupper ( $page );
									$sectionCount = $db->prepare ( "SELECT COUNT(*) FROM helpPage WHERE page = ?" );
									$sectionCount->execute ( array (
											$page
									) );
									$sectionCountR = $sectionCount->fetch ();
									$sections = $sectionCountR [0];
									if ($sections == 1) {
										$getInfo = $db->prepare ( "SELECT id FROM helpPage WHERE page = ?" );
										$getInfo->execute ( array (
												$page
										) );
										$getInfoR = $getInfo->fetch ();
										$id = $getInfoR ['id'];
										?>
                <form id="frm<?php
										echo $id;
										?>" action="index.php?page=help&helpId=<?php
										echo $id;
										?>" method="post">
                <div style="line-height:1.5; font-weight:bold; cursor:pointer;" onclick="submitForm('<?php
										echo $id;
										?>')">
                    <table style="width:300px;">
                        <tr>
                        <td style="text-align:left; text-decoration:underline; padding-left:0px;"><?php
										echo $upage;
										?></td>
                        <td style="text-align:right; text-decoration:none;"><?php
										echo ($helpId == $id) ? " >>> " : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										?></td>
                    </tr>
                </table>
            </div>
        </form>
        <?php
									} else {
										?>
                <div style="line-height:1.5; font-weight:bold; padding-left:2px;">
                    <?php
										echo "<span style='text-decoration:underline;'>$upage</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&#8595;";
										?>
                </div>
                <div>
                <?php
										$getInfo = $db->prepare ( "SELECT id, section FROM helpPage WHERE page = ?" );
										$getInfo->execute ( array (
												$page
										) );
										while ( $getInfoR = $getInfo->fetch () ) {
											$id = $getInfoR ['id'];
											$section = $getInfoR ['section'];
											$usection = strtoupper ( $section );
											?>
                    <form id="frm<?php
											echo $id;
											?>" action="index.php?page=help&helpId=<?php
											echo $id;
											?>" method="post">
                    <div style="line-height:1.5; font-weight:bold; cursor:pointer; text-decoration:none;" onclick="submitForm('<?php
											echo $id;
											?>')">
                        <table style="width:300px;">
                            <tr>
                            <td style="text-align:left; padding-left:15px;"><?php
											echo $usection;
											?></td>
                            <td style="text-align:right;"><?php
											echo ($helpId == $id) ? " >>> " : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
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
								}
								?>
    </td>
    <td style="padding:5px; border:1px solid #000000; font-weight:bold;">
    <?php
				$getWriteUp = $db->prepare ( "SELECT writeUp FROM helpPage WHERE id = ?" );
				$getWriteUp->execute ( array (
						$helpId
				) );
				$getWriteUpR = $getWriteUp->fetch ();
				if ($getWriteUpR) {
					$writeUp = nl2br ( html_entity_decode ( $getWriteUpR ['writeUp'], ENT_QUOTES ) );
					echo $writeUp;
				}
				?>
</td>
</tr>
</table>
