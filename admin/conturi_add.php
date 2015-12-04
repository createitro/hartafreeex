<?php

	// include server.inc.php
	$fi = "includes/server.inc.php";
	if (file_exists($fi)) { require_once($fi); } else { echo('Nu am putut include un fisier.'); exit(); }

	$configArray['currentModule'] = 'conturi_admin';

	if($_SESSION['userType'] != 'superadmin') { array_shift($configArray['cont_admin_tipuri']); }

	pageHeader();
	
	//DB SELECT FROM STRUCT
	intranetDBConnect();
	


	
?>	
                <div class="subTable">
						<table cellpadding="0" cellspacing="0" class="T-top">
                        <tr>
                        	<td class="w100"><h1><img src="images/userpic_default.jpg" width="48" height="48" border="0" align="left" style="margin-right:10px;" />Adauga cont administrare</h1>

                            <div class="Dcontainer">
                            	<br />
                                <?php
								
								$submitOK = true;
								if (!isset($_POST['submit']) || $_POST['submit'] == ''){ 							
									$submitOK = false;
									$cont_tip = 'contributor';
								} else {
									$nume = trim($_POST['nume']);
									if(strlen($nume) < 1) { $error['nume'] = 'Obligatoriu.'; $submitOK = false; } 
									$prenume = trim($_POST['prenume']);
									if(strlen($prenume) < 1) { $error['prenume'] = 'Obligatoriu.'; $submitOK = false; } 
									$email = trim($_POST['email']);
									if(strlen($email) < 5) { $error['email'] = 'Obligatoriu.'; $submitOK = false; } 
									$parola = trim($_POST['parola']);
									if(strlen($parola) < 5) { $error['parola'] = 'Obligatoriu.'; $submitOK = false; } 
									$cont_tip = trim($_POST['cont_tip']);
									//if(($cont_tip == 'superadmin') && ($_SESSION['userType'] != 'superadmin')) { $cont_tip = $_POST['cont_tip'] = 'admin'; }
								} // endif post submit								
								
								
								if ($submitOK && $configArray['rightWrite']) {
										
										
										$insertUserQueryString = "INSERT INTO conturi_admin SET ".
											"nume = '". mysql_escape_string($nume)."', ".
											"prenume = '". mysql_escape_string($prenume)."', ".
											"email = '". mysql_escape_string($email)."', ".
											"parola = '". mysql_escape_string($parola)."', ".
											"cont_tip = '". mysql_escape_string($cont_tip)."' ".	
											"";
										//echo $insertUserQueryString;
										$insertErr = 0;
										if(!mysql_query($insertUserQueryString, $configArray['dbcnx'])) { $insertErr = 1; echo ' eroare la update 1: ['.mysql_error().']'; } else { echo ''; }
										//insertIntoTable($insertSesizare);
										
										$_NEW_CONT_ID = mysql_insert_id();

										
										///// DEFAULT ACCESS /////////////////////////////////////////////////////////////////////////////////////////
										$modules = getQueryInArray("SELECT * FROM modules m ORDER BY module_id ASC");
										$accesModules = getQueryInArray("SELECT * FROM mm_cont_modul cm WHERE cm.id_cont = ".intval($_NEW_CONT_ID)." ORDER BY id_modul ASC");	
										for($i=0;$i<count($modules);$i++) {
											if($cont_tip == 'superadmin') {
												$cur_r = $cur_w = 1; //DEFAULT ALL 1
												insertIntoTable("INSERT INTO mm_cont_modul SET r = ".intval($cur_r).", w = ".intval($cur_w).", id_cont = ".intval($_NEW_CONT_ID).", id_modul = ".intval($modules[$i]['module_id'])."");
											}
											if(($cont_tip == 'editor') || ($cont_tip == 'contributor')) {
												$cur_r = $cur_w = 0; //DEFAULT ADMIN
												if($modules[$i]['module_id'] == 3) { $cur_r = $cur_w = 1; } //Sesizari
												insertIntoTable("INSERT INTO mm_cont_modul SET r = ".intval($cur_r).", w = ".intval($cur_w).", id_cont = ".intval($_NEW_CONT_ID).", id_modul = ".intval($modules[$i]['module_id'])."");
											}											
										}//endfor													
										
										//////////////////////////////////////////////////////////////////////////////////////////////////////////										
										
										$insertLog = "INSERT INTO log SET ".
											"data = NOW(), ".
											"obs = 'ADAUGARE CONT - DUPA AUTENTIFICARE', ".
											"ip = '".get_ip_address()."', ".
											"query = '". mysql_real_escape_string( $insertUserQueryString )."' ".
											"";								
										if(!mysql_query($insertLog, $configArray['dbcnx'])) { $insertErr = 1; echo $insertLog.' &nbsp; eroare la adaugare 2'; } else { echo ''; }
										
										if(!$insertErr) { echo '<br />Adaugarea fost facuta cu succes!<br /><br />'; }
										
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
                                            	<td align="left" valign="top">Tip cont*:</td>
                                                <td align="left" valign="top" style="width:100%;"><?php generateRadiosControls('cont_tip', 'id', 'name', $configArray['cont_admin_tipuri'], $cont_tip, ''); ?></td>
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
                                            	<td align="left" valign="top"></td>
                                            	<td align="left" valign="top">
		                                        <input type="submit" name="submit" value="TRIMITE" />
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
                        </tr>
                        </table>
                                           
                </div>         

<?php

	pageFooter();
?>
