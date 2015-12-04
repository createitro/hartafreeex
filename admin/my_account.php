<?php

	// include server.inc.php
	$fi = "includes/server.inc.php";
	if (file_exists($fi)) { require_once($fi); } else { echo('Nu am putut include un fisier.'); exit(); }

	$configArray['currentModule'] = 'conturi_admin';

	pageHeader();
	
	$_GET['id'] = $_ID = intval(intval($_SESSION['userId']));
	//if($_ID <= 0) redirect('index.php?msg=2');	
	
	//DB SELECT FROM STRUCT
	intranetDBConnect();

	$QRY = 'SELECT * FROM conturi_admin WHERE id = '.$_ID.' LIMIT 1 ';

	$_DATA = getQueryInArray($QRY, $configArray['STRUCTdbcnx']);
	if(!count($_DATA)) redirect('index.php?msg=3');
	
	$modules = getQueryInArray("SELECT * FROM modules m ORDER BY module_id ASC");
	
	

?>	
                <div class="subTable">
						<table cellpadding="0" cellspacing="0" class="T-top">
                        <tr>
                        	<td><h1><img src="images/userpic_default.jpg" width="48" height="48" border="0" align="left" style="margin-right:10px;" /><?php echo($_DATA[0]['prenume'].' '.$_DATA[0]['nume']); ?></h1>

                            <div class="Dcontainer">
                            	<br />
                                <?php
								
								$submitOK = true;
								if (!isset($_POST['submit']) || $_POST['submit'] == ''){ 
									$submitOK = false; 
									$nume = trim($_DATA[0]['nume']);
									$prenume = trim($_DATA[0]['prenume']);								
									$email = trim($_DATA[0]['email']);
									$parola = trim($_DATA[0]['parola']);
									/*
									$accesModules = getQueryInArray("SELECT * FROM mm_cont_modul cm WHERE cm.id_cont = ".intval($_ID)." ORDER BY id_modul ASC");	
								    for($i=0;$i<count($modules);$i++) {
										$cur_r = intval(getArrayValueWhere($accesModules, 'r', 'id_modul', $modules[$i]['module_id']));
										$cur_w = intval(getArrayValueWhere($accesModules, 'w', 'id_modul', $modules[$i]['module_id']));
										eval("$"."r_".$modules[$i]['module_id']." = $"."cur_r;"); 
										eval("$"."w_".$modules[$i]['module_id']." = $"."cur_w;"); 
									}
									*/
									
								} else {
									$nume = trim($_POST['nume']);
									if(strlen($nume) < 1) { $error['nume'] = 'Obligatoriu.'; $submitOK = false; } 
									$prenume = trim($_POST['prenume']);
									if(strlen($prenume) < 1) { $error['prenume'] = 'Obligatoriu.'; $submitOK = false; } 
									$email = trim($_POST['email']);
									if(strlen($email) < 5) { $error['email'] = 'Obligatoriu.'; $submitOK = false; } 
									$parola = trim($_POST['parola']);
									if(strlen($parola) < 5) { $error['parola'] = 'Obligatoriu.'; $submitOK = false; } 
									/*
									$accesModules = array();
								    for($i=0;$i<count($modules);$i++) {
										$cur_r = intval($_POST['r_'.$modules[$i]['module_id']]);
										$cur_w = intval($_POST['w_'.$modules[$i]['module_id']]);
										if($cur_w == 1) { $cur_r = 1; }
										eval("$"."r_".$modules[$i]['module_id']." = $"."cur_r;"); 
										eval("$"."w_".$modules[$i]['module_id']." = $"."cur_w;"); 
									}	
									*/								
								
								} // endif post submit								
								
								
								if ($submitOK && $configArray['rightWrite']) {
										
										$updateUserANCS = "UPDATE conturi_admin SET ".
											"nume = '". mysql_escape_string($nume)."', ".
											"prenume = '". mysql_escape_string($prenume)."', ".
											"email = '". mysql_escape_string($email)."', ".
											"parola = '". mysql_escape_string($parola)."' ".	
											"WHERE id = ".$_ID."";
										//echo $updateUserANCS; exit();
										if(!mysql_query($updateUserANCS, $configArray['dbcnx'])) { echo 'eroare la update 1'; } else { echo ''; }
										/*
										$accesModules = getQueryInArray("SELECT * FROM mm_cont_modul cm WHERE cm.id_cont = ".intval($_ID)." ORDER BY id_modul ASC");	
										for($i=0;$i<count($modules);$i++) {
											eval("$"."cur_r = $"."r_".$modules[$i]['module_id'].";"); 
											eval("$"."cur_w = $"."w_".$modules[$i]['module_id'].";"); 
																						
											if(checkArrayValue($accesModules, 'id_modul', $modules[$i]['module_id'])) {
												updateTable("UPDATE mm_cont_modul SET r = ".intval($cur_r).", w = ".intval($cur_w)." WHERE id_cont = ".intval($_ID)." AND id_modul = ".intval($modules[$i]['module_id'])."");
											} else {
												insertIntoTable("INSERT INTO mm_cont_modul SET r = ".intval($cur_r).", w = ".intval($cur_w).", id_cont = ".intval($_ID).", id_modul = ".intval($modules[$i]['module_id'])."");
											}
										}//endfor
										*/
										
										
										$insertLog = "INSERT INTO log SET ".
											"data = NOW(), ".
											"obs = 'ACTUALIZARE CONT - DUPA AUTENTIFICARE', ".
											"ip = '".get_ip_address()."', ".
											"query = '". mysql_real_escape_string( $updateUserANCS )."' ".
											"";								
										if(!mysql_query($insertLog, $configArray['dbcnx'])) { echo $insertLog.' &nbsp; eroare la adaugare 2'; } else { echo ''; }
										
										redirect('my_account.php?msg=5');
										
								} else {
								?>
                                    <form method="post" id="detaliiForm" name="detaliiForm" enctype="multipart/form-data" action="<?php echo($_SERVER['REQUEST_URI']); ?>">
                                        <input type="hidden" name="frefererf" value="<?php echo($_SERVER['HTTP_REFERER']); ?>" />
                                        <table cellpadding="2" cellspacing="2" border="0" class="w100">
	                                        <tr>
                                            	<td align="left" valign="top">Nume*:</td>
                                                <td align="left" valign="top" class="error" style="width:100%;"><input type="text" name="nume" value="<?php echo(htmlspecialchars($nume)); ?>" style="width:300px;" /><?php if(isset($error['nume'])) { echo('&nbsp; '.$error['nume']); } ?></td>
                                            </tr>
	                                        <tr>
                                            	<td align="left" valign="top">Prenume*:</td>
                                                <td align="left" valign="top" class="error" style="width:100%;"><input type="text" name="prenume" value="<?php echo(htmlspecialchars($prenume)); ?>" style="width:300px;" /><?php if(isset($error['prenume'])) { echo('&nbsp; '.$error['prenume']); } ?></td>
                                            </tr>
	                                        <tr>
                                            	<td align="left" valign="top">E-mail*:</td>
                                                <td align="left" valign="top" class="error" style="width:100%;"><input type="text" name="email" value="<?php echo(htmlspecialchars($email)); ?>" style="width:300px;" /><?php if(isset($error['email'])) { echo('&nbsp; '.$error['email']); } ?></td>
                                            </tr> 
	                                        <tr>
                                            	<td align="left" valign="top">Parola*:</td>
                                                <td align="left" valign="top" class="error" style="width:100%;"><input type="text" name="parola" value="<?php echo(htmlspecialchars($parola)); ?>" style="width:300px;" /><?php if(isset($error['parola'])) { echo('&nbsp; '.$error['parola']); } ?></td>
                                            </tr>                                                                                                                                                                                       
	                                        <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="20" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                             </tr>  

	                                        <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="1" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                             </tr>  


	                                        <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="1" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                             </tr>                                                                                                                                       
	                                        <tr>
                                            	<td align="left" valign="top"></td>
                                            	<td align="left" valign="top">
		                                        <input class="submitBtn" type="submit" name="submit" value="SALVEAZA" />
                                            	</td>
                                             </tr>
                                          </table>
                                    </form>
									<?php
                                        if(!$configArray['rightWrite']) {
                                            ?>
                                            <script language="javascript" type="text/javascript">
                                                $(':input').attr('disabled', true);	
                                            </script>
                                            <?php
                                        }
                                    ?>                                      
                                    <br /><br /><br />
                                <?php 	}//endif submit ?>
								<a href="conturi.php">&laquo; inapoi </a>
								<br /><br /><br />
                            </div>

                            </td>
							<?php /*?>                            
                            <td>
                            <!-- ME AND MY ARROW -->
                            	<img src="images/icon48_arrow_right.png" width="48" height="48" border="0" alt="" style="margin:20px 20px 0px 0px;" />
                            <!-- ME AND MY ARROW END -->
                            </td>
                            <td class="w100" style="background:url(<?php echo($mainPicBg); ?>) no-repeat 97% 10%; padding:20px 0px 0px 0px;">
                            	<!-- ALOCARE START -->
                                <?php 
									showUserObjects($_ID);
								?>
                            
                            </td>
                            <?php */?>
                        </tr>
                        </table>
                                           
                </div>         

<?php

	pageFooter();
?>