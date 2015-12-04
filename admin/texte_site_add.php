<?php

	// include server.inc.php
	$fi = "includes/server.inc.php";
	if (file_exists($fi)) { require_once($fi); } else { echo('Nu am putut include un fisier.'); exit(); }

	
	$fi = "fckeditor/fckeditor.php";
	if (file_exists($fi))  { include("$fi"); }
	else { echo("Cannot include a file."); exit(); }
		
	$sBasePath = $_SERVER['PHP_SELF'] ;
	$sBasePath = substr( $sBasePath, 0, strpos( $sBasePath, 'texte_site' ) ) ;
	$sBasePath .= "fckeditor/";

	$configArray['currentModule'] = 'texte_site';

	pageHeader();
	
	//DB SELECT FROM STRUCT
	intranetDBConnect();


	
?>	
                <div class="subTable">
						<table cellpadding="0" cellspacing="0" class="T-top">
                        <tr>
                        	<td class="w100"><h1><img src="images/userpic_default.jpg" width="48" height="48" border="0" align="left" style="margin-right:10px;" />Adauga text site</h1>

                            <div class="Dcontainer">
                            	<br />
                                <?php
								
								$submitOK = true;
								if (!isset($_POST['submit']) || $_POST['submit'] == ''){ 							
									$submitOK = false;

								} else {
									$page_name = trim($_POST['page_name']);
									if(strlen($page_name) < 1) { $error['page_name'] = 'Obligatoriu.'; $submitOK = false; } 
									$page_text = trim($_POST['page_text']);

								} // endif post submit								
								
								
								if ($submitOK && $configArray['rightWrite']) {
										
										
										$insertSiteText = "INSERT INTO pages SET ".
											"page_name = '". mysql_escape_string($page_name)."', ".
											"page_text = '". mysql_escape_string($page_text)."', ".
											"page_active = '1', ".
											"page_date = '". date("Y-m-d H:i:s")."' ".
											"";
										//echo $insertSiteText;
										$insertErr = 0;
										if(!mysql_query($insertSiteText, $configArray['dbcnx'])) { $insertErr = 1; echo ' eroare la update 1: ['.mysql_error().']'; } else { echo ''; }
										//insertIntoTable($insertSesizare);
										
												
										
										//////////////////////////////////////////////////////////////////////////////////////////////////////////										
										
										$insertLog = "INSERT INTO log SET ".
											"data = NOW(), ".
											"obs = 'ADAUGARE TEXT SITE - DUPA AUTENTIFICARE', ".
											"ip = '".get_ip_address()."', ".
											"query = '". mysql_real_escape_string( $insertSiteText )."' ".
											"";								
										if(!mysql_query($insertLog, $configArray['dbcnx'])) { $insertErr = 1; echo $insertLog.' &nbsp; eroare la adaugare 2'; } else { echo ''; }
										
										if(!$insertErr) { echo '<br />Adaugarea fost facuta cu succes!<br /><br />'; }
										
								} else {
								?>
                                    <form method="post" id="detaliiForm" name="detaliiForm" enctype="multipart/form-data" action="<?php echo($_SERVER['REQUEST_URI']); ?>">
                                        <input type="hidden" name="frefererf" value="<?php echo($_SERVER['HTTP_REFERER']); ?>" />
                                        <table cellpadding="2" cellspacing="2" border="0" class="w100">
	                                        <tr>
                                            	<td align="left" valign="top">Titlu Pagina*:</td>
                                                <td align="left" valign="top" class="error" style="width:100%;"><input type="text" name="page_name" value="<?php echo(htmlspecialchars($page_name)); ?>" style="width:300px;" /><?php if(isset($error['page_name'])) { echo('&nbsp; '.$error['page_name']); } ?></td>
                                            </tr>
	                                        <tr>
                                            	<td align="left" valign="top">Continut pagina*:</td>
                                                <td align="left" valign="top" class="error" style="width:100%;">
                                                 <?
														$oFCKeditor = new FCKeditor('page_text') ;
														$oFCKeditor->BasePath = $sBasePath;
														$oFCKeditor->ToolbarSet = 'Default';
														$oFCKeditor->Width  = '100%' ;
														$oFCKeditor->Height = '300' ;
														$oFCKeditor->Value =  $page_text;
														$oFCKeditor->Create() ;
												?>
                                                </td>
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
								<a href="texte_site.php">&laquo; inapoi </a>
								<br /><br /><br />
                            </div>
                            
                            </td>
                        </tr>
                        </table>
                                           
                </div>         

<?php

	pageFooter();
?>