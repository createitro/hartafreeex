<?php

	// include server.inc.php
	$fi = "includes/server.inc.php";
	if (file_exists($fi)) { require_once($fi); } else { echo('Nu am putut include un fisier.1'); exit(); }
	$fi = "classes/class_mail.php";
	if (file_exists($fi)) { require_once($fi); } else { echo('Nu am putut include un fisier.2'); exit(); }	
	
	pageHeader();
	
	$submitOK = true;

	if (!isset($_POST['submit']) || $_POST['submit'] == '') { 
			$submitOK = false; 
			$email = trim($_POST['email']);
	}else{


			$email = trim($_POST['email']);
			if (isset($email) && $email != ''){
				if ( !validate_email($email) ) {
					$submitOK = false;
					$error_msg['email'] = 'Adresa de email invalida!';

				} else {
					$checkEmail = getQueryInArray("SELECT * FROM conturi_admin c WHERE email='".mysql_escape_string($email)."' AND c.activ=1 LIMIT 1");
					if(!count($checkEmail)) {
						$submitOK = false;
						$error_msg['email'] = 'Aceasta adresa de email nu exista in baza noastra de date.';
					}
				}
			}else{
				$submitOK = false;
				$error_msg['email'] = 'Adresa de email este obligatorie!';
			}
			
			
		
	} // endif post submit	
	
?>	
    
                <div class="subTable">
                	<h1>Recuperare parola</h1>	
                    <div class="container" style="">
            				        	
                        <?php 	
								if (isset($_SESSION['userId']) && $_SESSION['userId'] >= 0) { 
						
                        			echo ('<strong>'.$_SESSION['username'].'</strong>, sunteti deja autentificat. Nu aveti nevoie de recuperarea parolei.<br />');
									
                        	  } else { 
						
										if ($submitOK){
											// insert into db	
											
											// user confirmation needed
											$sendToMail = $checkEmail[0]['email'];
											$sendToName = $checkEmail[0]['prenume'].' '.$checkEmail[0]['nume'];
											if(!validate_email($sendToMail)) $sendToMail = $checkEmail[0]['user_mct'];
											$loginEmail = $checkEmail[0]['email'];
											$loginPass = $checkEmail[0]['parola'];

											// send confirmation email
											$mail = new eMail("Free Ex Admin", $configArray['notifyEmail']); 
											// subiect = titlu
											$mail->subject("Free Ex Admin - Recuperare Parola"); 
											// to ... 
											$mail->to($sendToName, $sendToMail);
												// HTML ...
												$doc = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
												$articolHTML_font =  $doc . "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><title></title></head> ";
												$articolHTML_font .= "<body><br /> ";
												$articolHTML_font .= "<p>Recuperare Parola</p>
									
												<p>Ati primit acest email pentru ca dvs (sau cineva in numele dvs) ati completat formularul de recuperare parola de pe http://freeex.activewatch.ro/admin/.</p>
												<p>In cazul in care nu dvs ati cerut parola, va rugam sa ignorati acest mail.</p>
													Datele dvs de autentificare pe http://freeex.activewatch.ro/admin/ sunt:<br />
													---------------------------------------------------
													<p><table>
													<tr><td width='30%'>Email: </td><td><strong>".$loginEmail."</strong></td></tr>
													<tr><td width='30%'>Parola: </td><td><strong>".$loginPass."</strong></td></tr>
													<tr><td width='30%'>URL: </td><td><a href=\"http://freeex.activewatch.ro/admin/\">http://freeex.activewatch.ro/admin/</td></tr>
													</table></p>
													---------------------------------------------------
													<p>Va puteti autentifica acum folosind datele de mai sus.</p>
												<p>Va multumim!</p>
												---------------------------------------------------
												<p>Echipa Free Ex</p>";
												$articolHTML_font .= "</body></html>";
												$mail->html($articolHTML_font); 
												$mail->send(); 


												echo('<p>Un email cu datele de acces a fost trimis la adresa completata de dvs. <br />
													 Verificati contul de email <strong>'.$sendToMail.'</strong> pentru datele de acces.<br /><br />Va multumim!</p>');
									//////////////////////////////////////////////////////////////////////////////////////////////////////////////		
										} else { // print registration form	
																											  
												?>
												<form method="post" id="detaliiForm" name="detaliiForm" enctype="multipart/form-data" action="forgot_pass.php?noheader=1">
													<input type="hidden" name="frefererf" value="<?php echo($_SERVER['HTTP_REFERER']); ?>" /><br />
                                                    Introduceti adresa de email cu care va autenficati in sistemul de administrare:
                                                    <br /><br />													
													<table cellpadding="2" cellspacing="2" border="0" class="w100">
														<tr>
															<td align="left" valign="top">E-mail*:</td>
															<td align="left" valign="top" class="error" style="width:100%; font-size:14px; line-height:20px;"><input type="text" name="email" value="<?php echo(htmlspecialchars($email)); ?>" style="width:300px;" />
															<?php if(strlen($error_msg['email'])) { echo('&nbsp; <br />'.$error_msg['email']); } 
															?></td>
														</tr>                                                  
														
														<tr>
															<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="1" border="0" alt="" /></td>
															<td align="left" valign="top" class="error"></td>
														 </tr>                                                                                          
														<tr>
															<td align="left" valign="top"></td>
															<td align="left" valign="top">
															<input class="submitBtn" type="submit" name="submit" value="TRIMITE" />
															</td>
														 </tr>
                                                         <tr>
                                                         	<td class="error" style="font-size:14px;" colspan="2"><br />
																<!-- <strong>ATENTIE!!<br /></strong>
																conturile <span style="color:#777"><strong><em>nume@mct.ro</em></strong></span> nu vor functiona.<br />
                                                                <span style="color:#333">Va rugam folositi adresa dvs de mail obisnuita</span> <strong>prenume.nume@ancs.ro</strong> <span style="color:#333"> pentru autentificare si/sau recuperare parola.</span>
                                                                -->
                                                            </td>
                                                         </tr>
													  </table>
												</form>
                                    <?php } //endif submitOK ?>

                        <?php } //endif logged in ?>
                    </div>
                    
                </div>         

<?php
	pageFooter();
?>