<?php

	// include server.inc.php
	$fi = "includes/server.inc.php";
	if (file_exists($fi)) { require_once($fi); } else { echo('Nu am putut include un fisier.'); exit(); }

	
	$fi = "fckeditor/fckeditor.php";
	if (file_exists($fi))  { include("$fi"); }
	else { echo("Cannot include a file."); exit(); }
		
	$sBasePath = $_SERVER['PHP_SELF'] ;
	$sBasePath = substr( $sBasePath, 0, strpos( $sBasePath, 'alerte' ) ) ;
	$sBasePath .= "fckeditor/";

	$configArray['currentModule'] = 'alerte';

	
	$_GET['id'] = $_ID = intval($_GET['id']);
	if($_ID <= 0) redirect('index.php?msg=2');	

	pageHeader();
	
	//DB SELECT FROM STRUCT
	intranetDBConnect();


	$QRY = 'SELECT * FROM alerte WHERE alerta_id = '.$_ID.' LIMIT 1 ';

	$_DATA = getQueryInArray($QRY, $configArray['STRUCTdbcnx']);
	if(!count($_DATA)) redirect('index.php?msg=3');
	

	
?>	
                <div class="subTable">
						<table cellpadding="0" cellspacing="0" class="T-top">
                        <tr>
                        	<td class="w100"><h1><img src="images/userpic_default.jpg" width="48" height="48" border="0" align="left" style="margin-right:10px;" />Modifica Alerta</h1>

                            <div class="Dcontainer">
                            	<br />
                                <?php
								
								$submitOK = true;
								if (!isset($_POST['submit']) || $_POST['submit'] == ''){ 							
									$submitOK = false;
									
									$alerta_nume = trim($_DATA[0]['alerta_nume']);
									$alerta_email = trim($_DATA[0]['alerta_email']);
									
									//Alerte Categorii
									$selected_categs = array();
									$_CATEGS = getQueryInArray("SELECT categ_id FROM mm_categs_alerte WHERE alerta_id = ".$_ID."");
									for($i=0;$i<count($_CATEGS);$i++) { $selected_categs[] = $_CATEGS[$i]['categ_id'];  }


								} else {
									$alerta_nume = trim($_POST['alerta_nume']);
									if(strlen($alerta_nume) < 1) { $error['alerta_nume'] = 'Obligatoriu.'; $submitOK = false; } 
									
									$alerta_email = trim($_POST['alerta_email']);
									if(validate_email($alerta_email)) { $error['alerta_email'] = 'Obligatoriu.'; $submitOK = false; } 
															
									//Alerte Categorii
									$selected_categs = array();
									foreach ($_POST as $key => $value) {
									  //just checkboxes
									  if(begins_with($key,'cb')) { $selected_categs[] = $value;  }
									}	
									if(!count($selected_categs)) { $error['selected_categs'] = 'Cel putin o categorie.'; $submitOK = false; } 			

								} // endif post submit								
								
								
								if ($submitOK && $configArray['rightWrite']) {
										
										$updateAlertaStr = "UPDATE alerte SET ".
											"alerta_nume = '". mysql_escape_string($alerta_nume)."', ".
											"alerta_email = '". mysql_escape_string($alerta_email)."', ".
											"modified_at = '". date("Y-m-d H:i:s")."' ".
											"WHERE alerta_id = ".$_ID."";
										//echo $updateAlertaStr;
										$insertErr = 0;
										if(!mysql_query($updateAlertaStr, $configArray['dbcnx'])) { $insertErr = 1; echo ' eroare la update 1: ['.mysql_error().']'; } else {  }
										
										//insert $selected_categs
										deleteFieldWhere('mm_categs_alerte', 'alerta_id', $_ID);
										for($i=0;$i<count($selected_categs);$i++) {
											insertIntoTable("INSERT INTO mm_categs_alerte SET alerta_id = ".$_ID.", categ_id = ".intval($selected_categs[$i])." ");
										}//endfor $selected_categs												
																						
										
										//////////////////////////////////////////////////////////////////////////////////////////////////////////										
										
										$insertLog = "INSERT INTO log SET ".
											"data = NOW(), ".
											"obs = 'ACTUALIZARE SESIZARE - DUPA AUTENTIFICARE', ".
											"ip = '".get_ip_address()."', ".
											"query = '". mysql_real_escape_string( $updateAlertaStr )."' ".
											"";								
										if(!mysql_query($insertLog, $configArray['dbcnx'])) { $insertErr = 1; echo $insertLog.' &nbsp; eroare la adaugare 2'; } else { echo ''; }
										
										if(!$insertErr) { echo '<br />Modificarea fost facuta cu succes!<br /><br />'; }
										redirect('alerte_edit.php?id='.intval($_ID).'&msg=5');
										
								} else {
								?>
                                    <form method="post" id="detaliiForm" name="detaliiForm" enctype="multipart/form-data" action="<?php echo($_SERVER['REQUEST_URI']); ?>">
                                        <input type="hidden" name="frefererf" value="<?php echo($_SERVER['HTTP_REFERER']); ?>" />
                                        <table cellpadding="2" cellspacing="2" border="0" class="w100">
	                                        <tr>
                                            	<td align="left" valign="top">Nume*:<?php if(isset($error['alerta_nume'])) { echo('<span class="error blink"><br /> '.$error['alerta_nume'].'</span>'); } ?></td>
                                                <td align="left" valign="top" class="error" style="width:100%;"><input type="text" name="alerta_nume" value="<?php echo(htmlspecialchars($alerta_nume)); ?>" style="width:300px;" /></td>
                                            </tr>
	                                        <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="20" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                            </tr>                                             
	                                        <tr>
                                            	<td align="left" valign="top">E-mail*:<?php if(isset($error['alerta_email'])) { echo('<span class="error blink"><br /> '.$error['alerta_email'].'</span>'); } ?></td>
                                                <td align="left" valign="top" class="error" style="width:100%;"><input type="text" name="alerta_email" value="<?php echo(htmlspecialchars($alerta_email)); ?>" style="width:300px;" /></td>
                                            </tr>    
	                                        <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="20" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                            </tr>                                            

                                            
                                            <tr>
                                            	<td align="left" valign="top">Categorii*:<?php if(isset($error['selected_categs'])) { echo('<span class="error blink"><br /> '.$error['selected_categs'].'</span>'); } ?></td>
                                                <td align="left" valign="top" class="" style="width:100%;">
                                                	<div class="formBoxInput" style="width:500px;">
                                                    
													<?php
                                                        generateCategoriesCheckboxes($selected_categs);
                                                    ?>
                                                    </div>
                                                </td>
                                            </tr>                                       
                                            <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="20" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                            </tr>
                                            
                                          
	                                        <tr>
                                            	<td align="left" valign="top">Adaugat la:</td>
                                                <td align="left" valign="top" class="" style="width:100%;"><?php echo(/*'IP: '.$_DATA[0]['alerta_added_by_ip'].' la '.*/ $_DATA[0]['alerta_added_at'] ); ?> </td>
                                            </tr>                                                                                                                                                                                                                                                                                                
                                                                                                                                                                                                                          	                                   
	                                        <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="1" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                             </tr>                                                                                          
	                                        <tr>
                                            	<td align="left" valign="top"></td>
                                            	<td align="left" valign="top">
		                                        <input type="submit" name="submit" class="submitBtn" value="SALVEAZA" />
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
								<a href="alerte.php">&laquo; inapoi </a>
								<br /><br /><br />
                            </div>
                            
                            </td>
                        </tr>
                        </table>
                                           
                </div>         

<?php

	pageFooter();
?>