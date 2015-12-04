<?php
function verifyLogin() {
	global $configArray;

	if (isset($_GET['logout'])){
		unset($_SESSION['userId']);
		unset($_SESSION['username']);
		unset($_SESSION['userPassword']);
		unset($_SESSION['userEmail']);
		unset($_SESSION['userType']);

	}
	
	if (isset($_POST['login'])){
		$email = $_POST['email'];		
		$pass = $_POST['pass'];

	 	$loginSql = "SELECT *
				FROM conturi_admin c 
				WHERE c.email = '".$email."' AND c.parola = '".$pass."' AND c.activ = '1' LIMIT 1";					
		$_USER = getQueryInArray($loginSql, $configArray['dbcnx']);
	
		//print_r('<!-- qwerty '.$loginSql.' -->');
		
		$isUser = false;
		if (!count($_USER)) { $isUser = false; } else { $isUser = true; }		
		
		if ($isUser) {

			$insertIntoLog = "INSERT INTO log SET ip = '".mySqlEscape(getIP())."', " .
					" data = '".mySqlEscape(date("Y-m-d H:i:s"))."', " .
					" query = '".mySqlEscape($loginSql)."', " .	
					" id_conturi = '".mySqlEscape($_USER[0]['id'])."', " .					
					" obs ='AUTENTIFICARE ADMINISTRATOR' ";			
			@mysql_query($insertIntoLog);
			
			$_SESSION['userId'] = intval($_USER[0]['id']); //cont_admin ID
			//$_SESSION['userId'] = intval($_USER[0]['user_id']); //conturi ID
			$_SESSION['username'] = ucwords(strtolower($_USER[0]['nume'])).' '.ucwords(strtolower($_USER[0]['prenume']));
			$_SESSION['userEmail'] = $_USER[0]['email'];
			$_SESSION['userType'] = $_USER[0]['cont_tip'];
			$_SESSION['userPassword'] = $pass;
			
		}//endif isUser
		
		if(isset($_POST['backurl'])) redirect($_POST['backurl']);
		
	}//endif $_POST['login']
	
	if (!isset($_SESSION['userId']) && (!stristr($_SERVER['SCRIPT_NAME'], 'index.php') && !stristr($_SERVER['SCRIPT_NAME'], 'forgot_pass.php') )) { redirect('index.php?msg=1&url='.$_SERVER['REQUEST_URI']); }
}

function printLoginArea() {

	global $configArray;
	
	if (isset($_SESSION['userId']) && $_SESSION['userId'] >= 0){
		?>
        <p class="authTitle"><?php echo(htmlspecialchars($_SESSION['username'], ENT_QUOTES)); ?></p>
        <p><a href="my_account.php">Contul meu</a></p>
        <p><a href="<?php echo(add_querystring_var('./','logout','1') ); ?>">Logout</a></p> 
        <?php			
		return;
	}
	
	if (isset($_SESSION['userId']) && $_SESSION['userId'] < 0){
		echo("Utilizatorul este ori inexistent, ori inactiv, ori parola introdusa este gresita!");		
		unset($_SESSION['userId']);
	}
	
	if (!isset($_SESSION['userId'])){
?>		
        <?php if (isset($_POST['login'])) { 
					?><p class="authTitleError">Incorect!</p><?php 
			  } else { ?><p class="authTitle">Autentificare:
              			<a style="float:right; font-size:12px;" href="forgot_pass.php?noheader=1" rel="lyteframe" rev="width: 600px; height: 320px; scrolling: auto;" title="Recuperare parola">recuperare parola</a>
              			 </p> <?php   
			  }
		 ?>
        <form class="noEnterSubmit" action="" method="post" enctype="multipart/form-data" name="loginForm" id="loginForm">
        	<table width="200" border="1">
              <tr>
                <td><p><input class="<?php if (isset($_POST['login'])) { echo('authError'); } else { echo('auth'); } ?>" type="text" name="email" value="Adresa e-mail" onblur="this.className='auth'; if(document.loginForm.email.value == '') { document.loginForm.email.value='Adresa e-mail'; }" onfocus="this.className='authOver'; if(document.loginForm.email.value == 'Adresa e-mail') { document.loginForm.email.value=''; }" onkeypress="submitFormOnEnter(event, document.loginForm);" /></p></td>
                <td rowspan="2"><div onclick="document.loginForm.submit();" class="authSbmtBtn" onmouseover="this.className='authSbmtBtnOver';" onmouseout="this.className='authSbmtBtn';"></div></td>
              </tr>
              <tr>
              	<td><p><input class="<?php if (isset($_POST['login'])) { echo('authError'); } else { echo('auth'); } ?>" type="password" name="pass" value="" onblur="this.className='auth'; if(document.loginForm.pass.value == '') { document.loginForm.pass.value=''; }" onfocus="this.className='authOver'; if(document.loginForm.pass.value == '') { document.loginForm.pass.value=''; }" onkeypress="submitFormOnEnter(event, document.loginForm);" /></p></td>
              </tr>
            </table>
            <input type="hidden" name="login" value="submit" />
            <?php if(strlen($_GET['url']) > 1): ?><input type="hidden" name="backurl" value="<?php echo($_GET['url']); ?>" /><?php endif; ?>
        </form>           
<?
	}
}

function getSectionRights($n) {
	$n = strtoupper($n);
	if(($n[0] == 'Y') && ($n[1] == 'Y') && ($n[2] == 'Y')) return 3;
	elseif(($n[0] == 'Y') && ($n[1] == 'Y')) return 2;
	elseif($n[0] == 'Y') return 1;
	else return 0;
}


function hasRightOn($module) {
	global $configArray;
		if (isset($_SESSION['userId']) && $_SESSION['userId'] >= 0)  {
			$getCurrentModuleRights = getQueryInArray("SELECT * FROM mm_cont_modul cm JOIN modules m ON m.module_id = cm.id_modul WHERE m.module_slug = '".$module."' AND cm.id_cont = ".$_SESSION['userId']." LIMIT 1");
			if(count($getCurrentModuleRights)) {
				$rightRead = intval($getCurrentModuleRights[0]['r']);
				$rightWrite = intval($getCurrentModuleRights[0]['w']);
				if($rightRead) return 1; else return 0;
			} else {
				return 0;
			}
		} else {
			return 0;
		}

}

function generateMenuNew() {


		?><ul><?php
		//menu utilizatori start
		if(hasRightOn('conturi_admin')) {
		?>
            <li><a href="conturi.php">Conturi Admin</a></li>        
        <?php
		} //menu utilizatori end		
	
		if(hasRightOn('sesizari')) {
		?>
            <li><a href="sesizari.php">Sesizari</a></li>        
        <?php
		} //menu proiecte end			
		
				
		//menu capacitati start
		if(hasRightOn('alerte')) {
		?>
            <li><a href="alerte.php">Alerte</a>
            </li>        
        <?php
		} //menu capacitati end		
		
		//menu structurale start
		if(hasRightOn('texte_site'))  {
		?>
            <li><a href="texte_site.php">Texte Site</a></li>        
        <?php
		} //menu structurale end




		
	

	?></ul><?php
}


function getImage($img, $alt='') {
	if(!strlen($img)) $img = 'spacer.gif';
	return '<img src="images/'.$img.'" border="0" alt="'.$alt.'" />'."\n";
}

function getStatus($status) {
	   switch($status) {
		   case 1: $st_pr = "Nepreluat"; break;
		   case 2: $st_pr = "Neeligibil"; break;
		   case 3: $st_pr = "Neeligibil"; break;
		   case 4: $st_pr = "Eligibil"; break;
		   default: $st_pr = "Eroare";
	   }
	   return $st_pr;
}
function getStatusCap($status) {
	   switch($status) {
		   case 1: $st_pr = "Documente nedepuse"; break;
		   case 2: $st_pr = "Documente depuse"; break;
		   case 3: $st_pr = "Eligibil"; break;
		   default: $st_pr = "Eroare";
	   }
	   return $st_pr;
}
function getStatusCapColoured($status) {
	   switch($status) {
		   case 1: $st_pr = "<span class=\"c_red\">Documente nedepuse</span>"; break;
		   case 2: $st_pr = "<span class=\"c_blue\">Documente depuse</span>"; break;
		   case 3: $st_pr = "<span class=\"c_green\">Eligibil</span>"; break;
		   default: $st_pr = "Eroare";
	   }
	   return $st_pr;
}
function getAriaTematica($nr) {
	switch($nr) {
		 case 1: return "Sanatate"; break;
		 case 2: return "Agricultura, siguranta si securitate alimentara"; break;
		 case 3: return "Energie"; break;
		 case 4: return "Mediu"; break;
		 case 5: return "Materiale, procese si produse inovative"; break;
		 case 6: return "Tehnologia informatiei si comunicatii"; break;
		 case 7: return "Biotehnologii"; break;
		 case 8: return "Spatiu si securitate"; break;
		 case 9: return "Cercetare socio-economica si umanista"; break;
		 case 10: return "Cercetare exploratorie si de frontiera"; break;
		 default: return "Necunoscuta";
	}
}
function getTipProiect($tip) {
	switch($tip) {
		 case 1: return "Constructii"; break;
		 case 2: return "Modernizari"; break;
		 default: return "Necunoscuta";
	}	
}

function getTipProiectExtended($obj) {
   switch($obj) {
	 case 1:  return "Constructie/Extindere cladiri";break;
	 case 2:  return "Modernizare";break;
	 default: return "Necunoscuta";
	}
}
function getTipProiectExtended_2($obj) {
   switch($obj) {
	 case 1:  return "Spin-off";break;
	 case 2:  return "Start-up";break;
	 default: return "Necunoscuta";
	}
}
function getTipProiectExtended_3($obj) {
   switch($obj) {
	 case 1:  return "Centre GRID";break;
	 case 2:  return "RoEduNet";break;
	 default: return "Necunoscuta";
	}
}
function getTipProiectExtended_4($obj) {
   switch($obj) {
	 case 1:  return "Pilon 1";break;
	 case 2:  return "Pilon 2";break;
	 case 3:  return "Pilon 3";break;
	 default: return "Necunoscuta";
	}
}
function getTipProiectExtended_5($obj) {
   switch($obj) {
	 case 1:  return "Proiect tehnologic inovativ";break;
	 case 2:  return "Proiect pentru intreprinderi nou-create inovative";break;
	 default: return "Necunoscuta";
	}
}

function getTipProiectExtended_6($obj) {
   switch($obj) {
	 case 1:  return "Modulul 1";break;
	 case 2:  return "Modulul 2";break;
	 default: return "Necunoscuta";
	}
}

function getTipProiectExtended_7($obj) {
   switch($obj) {
	 case 1:  return "Proiect de tip 1";break;
	 case 2:  return "Proiect de tip 2";break;
	 default: return "Necunoscuta";
	}
}

function getFormaProprietate($obj) {
   switch($obj) {
	 case 1:  return "Privata";break;
	 case 2:  return "De stat privata";break;
	 case 3:  return "De stat publica";break;
	 case 4:  return "Mixta";break;	 
	 default: return "Necunoscuta";
	}
}

function getImpact($obj) {
   switch($obj) {
	 case 1:  return "DA";break;
	 case 2:  return "NU";break;
	 default: return "NU";
	}
}


function getSiteMsg($msg) {
	switch($msg) {
		 case 1: $m = '<div class="errMsgBox"> Autentificarea este necesara pentru a acesa aceasta zona! </div>'; break;
		 case 2: $m = '<div class="errMsgBox"> Acest link nu este valid. Va rugam reveniti. </div>'; break;
		 case 3: $m = '<div class="errMsgBox"> Articolul nu a fost gasit. </div>'; break;
		 case 4: $m = '<div class="okMsgBox"> Poza a fost stearsa. </div>'; break;
		 case 5: $m = '<div class="okMsgBox"> Modificarea fost facuta cu succes! </div>'; break;
		 case 6: $m = '<div class="okMsgBox"> Parola dvs a fost trimisa prin e-mail la adresa specificata! </div>'; break;
		 case 7: $m = '<div class="errMsgBox"> Nu aveti acces pentru aceasta zona. </div>'; break;
		 default: $m = '';
	}
	if(strlen($m)) { echo($m); }
}

function getJudet($obj) {
   switch($obj) {
     case 1:  return "Alba";break;
     case 2:  return "Arad";break;
     case 3:  return "Arges";break;
     case 4:  return "Bacau";break;
     case 5:  return "Bihor";break;
     case 6:  return "Bistrita-Nasaud";break;
     case 7:  return "Botosani";break;
     case 8:  return "Brasov";break;
     case 9:  return "Braila";break;
     case 10: return "Bucuresti";break;
     case 11: return "Buzau";break;
     case 12: return "Caras-Severin";break;
     case 13: return "Calarasi";break;
     case 14: return "Cluj";break;
     case 15: return "Constanta";break;
     case 16: return "Covasna";break;
     case 17: return "Dambovita";break;
     case 18: return "Dolj";break;
     case 19: return "Galati";break;
     case 20: return "Giurgiu";break;
     case 21: return "Gorj";break;
     case 22: return "Harghita";break;
     case 23: return "Hunedoara";break;
     case 24: return "Ialomita";break;
     case 25: return "Iasi";break;
     case 26: return "Ilfov";break;
     case 27: return "Maramures";break;
     case 28: return "Mehedinti";break;
     case 29: return "Mures";break;
     case 30: return "Neamt";break;
     case 31: return "Olt";break;
     case 32: return "Prahova";break;
     case 33: return "Salaj";break;
     case 34: return "Satu Mare";break;
     case 35: return "Sibiu";break;
     case 36: return "Suceava";break;
     case 37: return "Teleorman";break;
     case 38: return "Timis";break;
     case 39: return "Tulcea";break;
     case 40: return "Valcea";break;
     case 41: return "Vrancea";break;
     case 42: return "Vaslui";break;
	 default: return "Necunoscuta";
    }
}

function getTipSesizareIT($nr) {
	switch($nr) {
		case 1: return "Problema email";break;
		case 2: return "Problema internet";break;
		case 3: return "Problema calculator";break;
		case 4: return "Problema imprimanta";break;
		case 5: return "Problema software";break;
		case 6: return "Solicitare deblocare adresa email";break;
		case 7: return "Solicitare deblocare adresa internet";break;
		case 8: return "Problema hardware";break;
		case 9: return "Problema website www.ancs.ro";break;
		case 10: return "Alta problema";break;
		default: return "Necunoscuta";	
	}	
}

function getCerereURLbyID($id) {
	return 'getfile.php?id='.intval($id);
}

function getCapacitatiCerereURLbyID($id) {
	return 'getfile_capacitati.php?id='.intval($id);
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
  
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
  
    $bytes /= pow(1024, $pow);
  
    return round($bytes, $precision) . ' ' . $units[$pow];
} 

function getDocumentLink($url, $name, $type = 'pdf') {
	if(strlen($url) && ($url != '#')) $return .= '<a href="'.$url.'" target="_blank">';
	if(strlen($type)) { $return .= '<img src="images/b_'.$type.'.gif" width="16" height="16" border="0" alt="Download" align="left" style="margin-right:5px;" />'; }
	$return .= $name.'';
	if(strlen($url) && ($url != '#')) $return .= '</a>';
	return $return;
}

function projectStatusFormManagement($_DATA) {
	global $configArray;	
						// VERIFICARE FORMALA SI ELIGIBILITATE MANAGEMENT //////////////////////////////////////////////////////////////////////////////
						
						if(($_POST['btnSalveaza'] == 1) && ($configArray['rightWrite'])) {

							if(isset($_POST['status_vf'])) { $status_vf = $_POST['status_vf']; } else { $status_vf = $_POST['status_vf'] = -1; }
							if(isset($_POST['status_ea'])) { $status_ea = $_POST['status_ea']; } else { $status_ea = $_POST['status_ea'] = -1; }
							$obs_vf = trim($_POST['obs_vf']);
							$obs_ea = trim($_POST['obs_ea']);
							
							if(($status_vf == 0) && ($status_ea == 0)) { $statusProiect = 2; }
							if(($status_vf == 0) && ($status_ea == 1)) { $statusProiect = 2; }
							if(($status_vf == 1) && ($status_ea == 0)) { $statusProiect = 3; }
							if(($status_vf == 1) && ($status_ea == 1)) { $statusProiect = 4; }
							
							updateTable("UPDATE proiecte SET status = ".$statusProiect.", status_vf = ".intval($status_vf).", status_ea = ".intval($status_ea).", obs_vf = '".mysql_escape_string($obs_vf)."', obs_ea = '".mysql_escape_string($obs_ea)."' WHERE id = ".intval($_DATA[0]['id'])."");
							//print_r($_POST); print $statusProiect; exit();
							redirect($_SERVER['REQUEST_URI']);
							
						}//endif btnSalveaza submited 
						else {
							//READ VALUES
							$status_vf = intval($_DATA[0]['status_vf']);
							$status_ea = intval($_DATA[0]['status_ea']);
							$obs_vf = $_DATA[0]['obs_vf'];
							$obs_ea = $_DATA[0]['obs_ea'];							
						}
					?>
                    <div class="container">
	                    <form method="post" id="detaliiForm" name="detaliiForm" enctype="multipart/form-data" action="<?php echo($_SERVER['REQUEST_URI']); ?>">
                        	<?php
								if((!$configArray['rightWrite'])/* || (intval($_DATA[0]['status']) == 4)*/) {
									$verificareFormalaDisabled = $eligibilitateDisabled = ' disabled="disabled"';
								}
							?>
                            <input type="hidden" name="btnSalveaza" value="1" />
                        	<table cellpadding="0" cellspacing="0" class="F">
                            	<tr>
                                	<td align="left" class="label"></td>
                                    <td align="center" class="label">DA</td>
                                    <td align="center" class="label">NU</td>
                                    <td align="left" class="label">Observatii</td>
                                </tr>
                            	<tr>
                                	<td align="left" class="label" valign="middle">Verificare formala a completitudinii ofertei de proiect (formularistica)</td>
                                    <td align="center" class="data" valign="middle"><input<?php echo($verificareFormalaDisabled); ?><?php if($status_vf == 1) { echo(' checked="checked"'); } ?> class="radio" type="radio" name="status_vf" value="1" /></td>
                                    <td align="center" class="data" valign="middle"><input<?php echo($verificareFormalaDisabled); ?><?php if($status_vf == 0) { echo(' checked="checked"'); } ?> class="radio" type="radio" name="status_vf" value="0" /></td>
                                    <td valign="top" align="left" class="data"><textarea<?php echo($verificareFormalaDisabled); ?> name="obs_vf" class="obs"><?php if(strlen($_DATA[0]['obs_vf'])) { echo(htmlspecialchars($_DATA[0]['obs_vf'], ENT_QUOTES)); } ?></textarea></td>
                                </tr>            
                            	<tr>
                                	<td align="left" class="label" valign="middle">Eligibilitate</td>
                                    <td align="center" class="data" valign="middle"><input<?php echo($eligibilitateDisabled); ?><?php if($status_ea == 1) { echo(' checked="checked"'); } ?> class="radio" type="radio" name="status_ea" value="1" /></td>
                                    <td align="center" class="data" valign="middle"><input<?php echo($eligibilitateDisabled); ?><?php if($status_ea == 0) { echo(' checked="checked"'); } ?> class="radio" type="radio" name="status_ea" value="0" /></td>
                                    <td valign="top" align="left" class="data"><textarea<?php echo($eligibilitateDisabled); ?> name="obs_ea" class="obs"><?php if(strlen($_DATA[0]['obs_ea'])) { echo(htmlspecialchars($_DATA[0]['obs_ea'], ENT_QUOTES)); } ?></textarea></td>
                                </tr>                                                                
                            	<tr>
                                	<td colspan="4" align="left" class="salveaza" valign="middle"><img class="salveaza"<?php if($configArray['rightWrite']) { echo(' onclick="document.detaliiForm.submit();"'); } ?> src="images/b_salveaza.png" width="128" height="48" border="0" alt="Salveaza" /></td>
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
                    </div>
                    <?php
					// END VERIFICARE FORMALA SI ELIGIBILITATE MANAGEMENT //////////////////////////////////////////////////////////////////////////////	
}



function projectStatusFormManagement_OLD($_DATA) {
	global $configArray;	
						// VERIFICARE FORMALA SI ELIGIBILITATE MANAGEMENT //////////////////////////////////////////////////////////////////////////////
						
						if(($_POST['btnSalveaza'] == 1) && ($configArray['rightWrite'])) {
							if(intval($_POST['verificareFormalaRadio']) != 1) { $_POST['verificareFormalaRadio'] = 0; }
							if(intval($_POST['eligibilitateRadio']) != 1) { $_POST['eligibilitateRadio'] = 0; }
						    if(intval($_POST['verificareFormalaRadio']) == 0 && intval($_POST['eligibilitateRadio']) == 0 ) { $statusProiect = 2; }
						    if(intval($_POST['verificareFormalaRadio']) == 0 && intval($_POST['eligibilitateRadio']) == 1 ) { $statusProiect = 2; }
 						    if(intval($_POST['verificareFormalaRadio']) == 1 && intval($_POST['eligibilitateRadio']) == 0 ) { $statusProiect = 3; }							
						    if(intval($_POST['verificareFormalaRadio']) == 1 && intval($_POST['eligibilitateRadio']) == 1 ) { $statusProiect = 4; }							
							
							updateTable("UPDATE proiecte SET status = ".intval($statusProiect).", obs_vf = '".mysql_escape_string($_POST['verificareFormalaObservatii'])."', obs_ea = '".mysql_escape_string($_POST['eligibilitateObservatii'])."' WHERE id = ".intval($_DATA[0]['id'])."");
							redirect($_SERVER['REQUEST_URI']);
							
							unset($_POST['btnSalveaza']);
							
						}//endif btnSalveaza submited
						
						$verificareFormalaDisabled = $verificareFormalaChecked = $verificareFormalaHiddenValue = $verificareFormalaHiddenObservatii = '';				
						$eligibilitateDisabled = $eligibilitateChecked = $eligibilitateHiddenValue = $eligibilitateHiddenObservatii = '';				
							
						if((intval($_DATA[0]['status']) == 3) || (intval($_DATA[0]['status']) == 4)) {
							$verificareFormalaDisabled = ' disabled="disabled"';
							$verificareFormalaChecked = ' checked="checked"';
							$verificareFormalaHiddenValue = '<input type="hidden" name="verificareFormalaRadio" value="1" />'."\n";
							$verificareFormalaHiddenObservatii = '<input type="hidden" name="verificareFormalaObservatii" value="'.htmlspecialchars($_DATA[0]['obs_vf'], ENT_QUOTES).'" />'."\n";
						}
						if(intval($_DATA[0]['status']) == 4) {
							$eligibilitateDisabled = ' disabled="disabled"';
							$eligibilitateChecked = ' checked="checked"';
							$eligibilitateHiddenValue = '<input type="hidden" name="eligibilitateRadio" value="1" />'."\n";
							$eligibilitateHiddenObservatii = '<input type="hidden" name="eligibilitateObservatii" value="'.htmlspecialchars($_DATA[0]['obs_ea'], ENT_QUOTES).'" />'."\n";
						}						
					?>
                    <div class="container">
	                    <form method="post" id="detaliiForm" name="detaliiForm" enctype="multipart/form-data" action="<?php echo($_SERVER['REQUEST_URI']); ?>">
                        	<?php
							if(0 == 1) {
								print ($verificareFormalaHiddenValue."\n");
								print ($verificareFormalaHiddenObservatii."\n");
								print ($eligibilitateHiddenValue."\n");
								print ($eligibilitateHiddenObservatii."\n");
							}
							?>
                            <input type="hidden" name="btnSalveaza" value="1" />
                        	<table cellpadding="0" cellspacing="0" class="F">
                            	<tr>
                                	<td align="left" class="label"></td>
                                    <td align="center" class="label">DA</td>
                                    <td align="center" class="label">NU</td>
                                    <td align="left" class="label">Observatii</td>
                                </tr>
                            	<tr>
                                	<td align="left" class="label" valign="middle">Verificare formala a completitudinii ofertei de proiect (formularistica)</td>
                                    <td align="center" class="data" valign="middle"><input<?php echo($verificareFormalaDisabled); ?><?php echo($verificareFormalaChecked); ?> class="radio" type="radio" name="verificareFormalaRadio" value="1" /></td>
                                    <td align="center" class="data" valign="middle"><input<?php echo($verificareFormalaDisabled); ?> class="radio" type="radio" name="verificareFormalaRadio" value="0" /></td>
                                    <td valign="top" align="left" class="data"><textarea<?php echo($verificareFormalaDisabled); ?> name="verificareFormalaObservatii" class="obs"><?php if(strlen($_DATA[0]['obs_vf'])) { echo(htmlspecialchars($_DATA[0]['obs_vf'], ENT_QUOTES)); } ?></textarea></td>
                                </tr>            
                            	<tr>
                                	<td align="left" class="label" valign="middle">Eligibilitate</td>
                                    <td align="center" class="data" valign="middle"><input<?php echo($eligibilitateDisabled); ?><?php echo($eligibilitateChecked); ?> class="radio" type="radio" name="eligibilitateRadio" value="1" /></td>
                                    <td align="center" class="data" valign="middle"><input<?php echo($eligibilitateDisabled); ?> class="radio" type="radio" name="eligibilitateRadio" value="0" /></td>
                                    <td valign="top" align="left" class="data"><textarea<?php echo($eligibilitateDisabled); ?> name="eligibilitateObservatii" class="obs"><?php if(strlen($_DATA[0]['obs_ea'])) { echo(htmlspecialchars($_DATA[0]['obs_ea'], ENT_QUOTES)); } ?></textarea></td>
                                </tr>                                                                
                            	<tr>
                                	<td colspan="4" align="left" class="salveaza" valign="middle"><img class="salveaza"<?php if(intval($_DATA[0]['status']) != 4) { echo(' onclick="document.detaliiForm.submit();"'); } ?> src="images/b_salveaza.png" width="128" height="48" border="0" alt="Salveaza" /></td>
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
                    </div>
                    <?php
					// END VERIFICARE FORMALA SI ELIGIBILITATE MANAGEMENT //////////////////////////////////////////////////////////////////////////////	
}


function projectAlocaEvaluatori($_DATA) {
	global $configArray;	
						// ADD REMOVE EVALUATORS  //////////////////////////////////////////////////////////////////////////////
					
					if($_GET['cmd'] == 'add') {
						if(intval($_GET['id_evaluator'])) {
							updateTable("INSERT mm_evaluator_proiect_competitie SET id_cont = '".intval($_GET['id_evaluator'])."', id_proiect = '".intval($_DATA[0]['id'])."', id_competitie = '".intval($_DATA[0]['id_competitie'])."', alocat_la = '".date("Y-m-d H:i:s")."' "); 
							redirect(remove_querystring_var(remove_querystring_var($_SERVER['REQUEST_URI'],'id_evaluator'),'cmd')); 
						}		
					}	
					
					if($_GET['cmd'] == 'remove') {
						
						if(intval($_GET['id_evaluator'])) {
							mysql_query("DELETE FROM mm_evaluator_proiect_competitie WHERE id_cont = ".intval($_GET['id_evaluator'])." AND id_proiect = ".intval($_DATA[0]['id'])." AND id_competitie = ".intval($_DATA[0]['id_competitie'])." ",$configArray['dbcnx']); 
							redirect(remove_querystring_var(remove_querystring_var($_SERVER['REQUEST_URI'],'id_evaluator'),'cmd')); 
						}		
					}											
						
					$EVALUATORI = getQueryInArray("SELECT * FROM conturi_evaluatori c WHERE 1=1 AND activ = 1 AND id NOT IN (SELECT id_cont FROM mm_evaluator_proiect_competitie epc WHERE epc.id_proiect = ".intval($_DATA[0]['id']).") ORDER BY evaluator_strain DESC");
					$EVALUATORI_ALOCATI = getQueryInArray("SELECT c.*, epc.* FROM mm_evaluator_proiect_competitie epc JOIN conturi_evaluatori c ON c.id = epc.id_cont WHERE epc.id_proiect = ".intval($_DATA[0]['id'])." ORDER BY epc.activ DESC");
					
					?>
  
                    <div class="container">

                    	<?php if(count($EVALUATORI_ALOCATI)): ?>
                        <h2>Evaluatori Alocati:</h2>
                    	<table cellpadding="0" cellspacing="0" class="F">
                        	<?php
								for($i=0;$i<count($EVALUATORI_ALOCATI);$i++) {
							?>
                            	<tr <?php if(($EVALUATORI_ALOCATI[$i]['activ'] == 0)) { echo(' class="red"'); } ?>>
                                	<td align="left" class="label"><?php echo($EVALUATORI_ALOCATI[$i]['evaluator_nume'].' '.$EVALUATORI_ALOCATI[$i]['evaluator_prenume']); ?></td>
                                    <td align="left" class="label">
                                    	<?php if($EVALUATORI_ALOCATI[$i]['accepted'] == 0): ?>
                                        	<?php if($EVALUATORI_ALOCATI[$i]['status'] == 'rejected'): ?>
                                    			<span class="red" title="<?php echo( htmlspecialchars($EVALUATORI_ALOCATI[$i]['reject_reason']) ); ?>">Rejected<br />[<?php echo( htmlspecialchars($EVALUATORI_ALOCATI[$i]['reject_reason']) ); ?>]</span>
                                            <?php else: ?>
                                            	<a href="<?php echo(add_querystring_var(add_querystring_var($_SERVER['REQUEST_URI'],'id_evaluator',$EVALUATORI_ALOCATI[$i]['id']),'cmd','remove')); ?>">Sterge</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                        	<span class="green">Acceptat</span>
                                        <?php endif; ?>
                                    </td>
                                     <td align="left" class="label"><?php echo($EVALUATORI_ALOCATI[$i]['status']);  ?></td>
                                     <td align="left" class="label"><?php if($EVALUATORI_ALOCATI[$i]['evaluator_strain'] == 1) { print('strain'); } else { print('roman'); } ?></td>
                                     <td align="left" class="label" style="width:120px;"><?php echo($EVALUATORI_ALOCATI[$i]['evaluator_institutie']);  ?></td>
                                </tr>	
                                <?php	
									
								}//endfor
							?>
                        </table>

                        <?php else: ?>
                        <h2>Nici un evaluator alocat</h2>
                        <?php endif; ?>                    
                    
                    	<?php if(count($EVALUATORI)): ?>
                        <h2>Evaluatori disponibili:</h2>
                    	<table cellpadding="0" cellspacing="0" class="F">
                           <tr><td colspan="4" style="text-align:left;"><strong>STRAINI:</strong></td></tr>                        
                        	<?php
								$currentStrain = $EVALUATORI[0]['evaluator_strain'];
								for($i=0;$i<count($EVALUATORI);$i++) {
									if($currentStrain != $EVALUATORI[$i]['evaluator_strain']) {
										?>
                                        <tr><td colspan="4" style="text-align:left;"><strong>ROMANI:</strong></td></tr>                        
                                        <?php
										$currentStrain = $EVALUATORI[$i]['evaluator_strain'];
									}
								?>
                            	<tr>
                                	<td align="left" class="label"><?php echo($EVALUATORI[$i]['evaluator_nume'].' '.$EVALUATORI[$i]['evaluator_prenume']); ?></td>
                                    <td align="left" class="label"><a href="<?php echo(add_querystring_var(add_querystring_var($_SERVER['REQUEST_URI'],'id_evaluator',$EVALUATORI[$i]['id']),'cmd','add')); ?>">Aloca</a></td>
                                    <td align="left" class="label"><?php if($EVALUATORI[$i]['evaluator_strain'] == 1) { print('strain'); } else { print('roman'); } ?></td>
                                    <td align="left" class="label" style="width:120px;"><?php echo($EVALUATORI[$i]['evaluator_institutie']);  ?></td>
                                </tr>
                                <?php								
								}
							?>
                        </table>
                        <?php else: ?>
                        <h2>Nici un evaluator disponbil</h2>                                                
                        <?php endif; ?>
	                    
                    </div>
                    <?php
					// END ADD REMOVE EVALUATORS //////////////////////////////////////////////////////////////////////////////	
}


function projectEvaluatoriAlocati($_DATA) {
	global $configArray;	
											
					$EVALUATORI_ALOCATI = getQueryInArray("SELECT c.*, epc.* FROM mm_evaluator_proiect_competitie epc JOIN conturi_admin c ON c.id = epc.id_cont WHERE epc.id_proiect = ".intval($_DATA[0]['id'])." ORDER BY evaluare_id ASC ");

					?>
                        
                        
  
                    <div class="container">

                    	<?php if(count($EVALUATORI_ALOCATI)): ?>
                        <h2>Evaluatori Alocati:</h2>
                    	<table cellpadding="0" cellspacing="0" class="F">
                        	<?php
								for($i=0;$i<count($EVALUATORI_ALOCATI);$i++) {
								?>
                            	<tr>
                                	<td align="left" class="label"><?php echo($EVALUATORI_ALOCATI[$i]['evaluator_nume'].' '.$EVALUATORI_ALOCATI[$i]['evaluator_prenume']); ?></td>
                                    <td align="left" class="label"><?php if($EVALUATORI_ALOCATI[$i]['evaluator_strain'] == 1) { print('strain'); } else { print('roman'); } ?></td>
                                </tr>	
                                <?php								
								}
							?>
                        </table>
                        <?php else: ?>
                        <h2>Nici un evaluator alocat</h2>
                        <?php endif; ?>                    

	                    
                    </div>
                    <?php
}

function projectStatusCAPACITATIFormManagement($_DATA) {
	global $configArray;	
						// VERIFICARE FORMALA SI ELIGIBILITATE MANAGEMENT //////////////////////////////////////////////////////////////////////////////
						
						if(($_POST['btnSalveaza'] == 1) && ($configArray['rightWrite'])) {
							if(intval($_POST['verificareFormalaRadio']) != 1) { $_POST['verificareFormalaRadio'] = 0; }
							if(intval($_POST['eligibilitateRadio']) != 1) { $_POST['eligibilitateRadio'] = 0; }
						    if(intval($_POST['verificareFormalaRadio']) == 0 && intval($_POST['eligibilitateRadio']) == 0 ) { $statusProiect = 2; }
						    if(intval($_POST['verificareFormalaRadio']) == 0 && intval($_POST['eligibilitateRadio']) == 1 ) { $statusProiect = 2; }
 						    if(intval($_POST['verificareFormalaRadio']) == 1 && intval($_POST['eligibilitateRadio']) == 0 ) { $statusProiect = 3; }							
						    if(intval($_POST['verificareFormalaRadio']) == 1 && intval($_POST['eligibilitateRadio']) == 1 ) { $statusProiect = 4; }							
							
							updateTable("UPDATE ".$configArray['CAPACITATIname'].".proiecte SET status = ".intval($statusProiect).", obs_vf = '".mysql_escape_string($_POST['verificareFormalaObservatii'])."', obs_ea = '".mysql_escape_string($_POST['eligibilitateObservatii'])."' WHERE id = ".intval($_DATA[0]['id'])."");
							redirect($_SERVER['REQUEST_URI']);
							
							unset($_POST['btnSalveaza']);
							
						}//endif btnSalveaza submited
						
						$verificareFormalaDisabled = $verificareFormalaChecked = $verificareFormalaHiddenValue = $verificareFormalaHiddenObservatii = '';				
						$eligibilitateDisabled = $eligibilitateChecked = $eligibilitateHiddenValue = $eligibilitateHiddenObservatii = '';				
							
						if((intval($_DATA[0]['status']) == 3) || (intval($_DATA[0]['status']) == 4)) {
							$verificareFormalaDisabled = ' disabled="disabled"';
							$verificareFormalaChecked = ' checked="checked"';
							$verificareFormalaHiddenValue = '<input type="hidden" name="verificareFormalaRadio" value="1" />'."\n";
							$verificareFormalaHiddenObservatii = '<input type="hidden" name="verificareFormalaObservatii" value="'.htmlspecialchars($_DATA[0]['obs_vf'], ENT_QUOTES).'" />'."\n";
						}
						if(intval($_DATA[0]['status']) == 4) {
							$eligibilitateDisabled = ' disabled="disabled"';
							$eligibilitateChecked = ' checked="checked"';
							$eligibilitateHiddenValue = '<input type="hidden" name="eligibilitateRadio" value="1" />'."\n";
							$eligibilitateHiddenObservatii = '<input type="hidden" name="eligibilitateObservatii" value="'.htmlspecialchars($_DATA[0]['obs_ea'], ENT_QUOTES).'" />'."\n";
						}						
					?>
                    <div class="container">
	                    <form method="post" id="detaliiForm" name="detaliiForm" enctype="multipart/form-data" action="<?php echo($_SERVER['REQUEST_URI']); ?>">
                        	<?php
								print ($verificareFormalaHiddenValue."\n");
								print ($verificareFormalaHiddenObservatii."\n");
								print ($eligibilitateHiddenValue."\n");
								print ($eligibilitateHiddenObservatii."\n");
							?>
                            <input type="hidden" name="btnSalveaza" value="1" />
                        	<table cellpadding="0" cellspacing="0" class="F">
                            	<tr>
                                	<td align="left" class="label"></td>
                                    <td align="center" class="label">DA</td>
                                    <td align="center" class="label">NU</td>
                                    <td align="left" class="label">Observatii</td>
                                </tr>
                            	<tr>
                                	<td align="left" class="label" valign="middle">Verificare formala</td>
                                    <td align="center" class="data" valign="middle"><input<?php echo($verificareFormalaDisabled); ?><?php echo($verificareFormalaChecked); ?> class="radio" type="radio" name="verificareFormalaRadio" value="1" /></td>
                                    <td align="center" class="data" valign="middle"><input<?php echo($verificareFormalaDisabled); ?> class="radio" type="radio" name="verificareFormalaRadio" value="0" /></td>
                                    <td valign="top" align="left" class="data"><textarea<?php echo($verificareFormalaDisabled); ?> name="verificareFormalaObservatii" class="obs"><?php if(strlen($_DATA[0]['obs_vf'])) { echo(htmlspecialchars($_DATA[0]['obs_vf'], ENT_QUOTES)); } ?></textarea></td>
                                </tr>            
                            	<tr>
                                	<td align="left" class="label" valign="middle">Eligibilitate</td>
                                    <td align="center" class="data" valign="middle"><input<?php echo($eligibilitateDisabled); ?><?php echo($eligibilitateChecked); ?> class="radio" type="radio" name="eligibilitateRadio" value="1" /></td>
                                    <td align="center" class="data" valign="middle"><input<?php echo($eligibilitateDisabled); ?> class="radio" type="radio" name="eligibilitateRadio" value="0" /></td>
                                    <td valign="top" align="left" class="data"><textarea<?php echo($eligibilitateDisabled); ?> name="eligibilitateObservatii" class="obs"><?php if(strlen($_DATA[0]['obs_ea'])) { echo(htmlspecialchars($_DATA[0]['obs_ea'], ENT_QUOTES)); } ?></textarea></td>
                                </tr>                                                                
                            	<tr>
                                	<td colspan="4" align="left" class="salveaza" valign="middle"><img class="salveaza"<?php if(intval($_DATA[0]['status']) != 4) { echo(' onclick="document.detaliiForm.submit();"'); } ?> src="images/b_salveaza.png" width="128" height="48" border="0" alt="Salveaza" /></td>
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
                    </div>
                    <?php
					// END VERIFICARE FORMALA SI ELIGIBILITATE MANAGEMENT //////////////////////////////////////////////////////////////////////////////	
}

function getDomeniuEconomic($nr) {
   switch($nr) {
     case 1: return "Aeronautica. Spatiu";break;
     case 2: return "Silvicultura";break;
     case 3: return "Tehnologii marine";break;
     case 4: return "Biotehnologii. Genomica";break;
     case 5: return "Chimie. Petrochimie";break;
     case 6: return "Constructii. Cladiri";break;
     case 7: return "Electronica/Industria electrica";break;
     case 8: return "Energie";break;
     case 9: return "Mediu/Deseuri";break;
     case 10: return "Industria alimentara";break;
     case 11: return "Sanatate. Medicamente";break;
     case 12: return "Tehnologia informatiei si comunicatiei";break;
     case 13: return "Masini, echipamente si senzori";break;
     case 14: return "Prelucrarea materialelor";break;
     case 15: return "Masuratori. Testare";break;
     case 16: return "Echipamente medicale/biomedicale";break;
     case 17: return "Nanotehnologii";break;
     case 18: return "Fotonica";break;
     case 19: return "Securitate";break;
     case 20: return "Textile/Pielarie/Lemn";break;
     case 21: return "Transport";break;
     case 22: return "Autovehicule";break;
     case 23: return "Constructii navale";break;
     case 24: return "Metalurgie";break;
     case 25: return "Petrol. Gaze";break;
     case 26: return "Altceva";break;

    }
   return "Necunoscuta";
}

function getDomeniuCap($nr) {
   switch($nr) {
     case 1: return "1.1.Informatica teoretica si stiinta calculatoarelor";break;
     case 2: return "1.2.Sisteme informatice avansate pentru e-servicii";break;
     case 3: return "1.3.Tehnologii, sisteme si infrastructuri de comunicatii";break;
     case 4: return "1.4.Inteligenta artificiala, robotica si sistemele autonome avansate";break;
     case 5: return "1.5.Securitatea si accesibilitatea sistemelor informatice";break;
     case 6: return "1.6.Tehnologii pentru sisteme distribuite si sisteme incorporate";break;
     case 7: return "1.7.Nanoelectronica, fotonica si micro-nanosisteme integrate";break;
     case 8: return "2.1.Sisteme si tehnologii energetice durabile; securitatea energetice";break;
     case 9: return "3.1.Modalitati si mecanisme pentru reducerea poluarii mediului";break;
     case 10: return "3.2.Sisteme de gestionare si valorificare a deseurilor; analiza ciclului de viata al produselor si ecoeficienta";break;
     case 11: return "3.3.Protectia si reconstructia ecologica a zonelor critice si conservarea ariilor protejate";break;
     case 12: return "3.4.Amenajarea teritoriului. Infrastructura si utilitati";break;
     case 13: return "3.5.Constructii";break;
     case 14: return "4.Sanatate";break;
     case 15: return "5.Agricultura, siguranta si securitate alimentara";break;
     case 16: return "6.Biotehnologii , biologie si genetica";break;
     case 17: return "7.1.Materiale avansate";break;
     case 18: return "7.2.Tehnologii avansate de conducere a proceselor industriale";break;
     case 19: return "7.3.Tehnologii si produse mecanice de inalta precizie si sisteme mecatronice";break;
     case 20: return "7.4.Tehnologii nucleare";break;
     case 21: return "7.5.Produse si tehnologii inovative destinate transporturilor si productiei de automobile";break;
     case 22: return "8.1.Explorari spatiale";break;
     case 23: return "8.2.Aplicatii spatiale";break;
     case 24: return "8.3.Tehnologii si infrastructuri aerospatiale";break;
     case 25: return "8.4.Tehnici pentru securitate";break;
     case 26: return "8.5.Sisteme si infrastrutura de securitate";break;
     case 27: return "9.1.Noi metode manageriale, de marketing si dezvoltare antreprenoriala pentru competitivitate organizationala";break;
     case 28: return "9.2.Calitatea educatiei";break;
     case 29: return "9.3.Calitatea  ocuparii";break;
     case 30: return "9.4.Capitalul uman, cultural si social";break;
     case 31: return "9.5.Patrimoniul material / nonmaterial, turismul cultural; industriile creative";break;
     case 32: return "9.6.Inegalitati socio-umane; disparitati regionale.";break;
     case 33: return "9.7.Tehnologie, organizatie si schimbare culturala";break;
     case 34: return "9.8.Locuirea";break;
     case 35: return "10.1.Matematica";break;
     case 36: return "10.2.Chimie, mediu si stiinta materialelor";break;
     case 37: return "10.3.Fizica si fizica tehnologica";break;
     case 38: return "10.4.Fizica nucleara (fuziune, fisiune)";break;
     case 39: return "10.5.Geologia si fizica atmosferei";break;
     case 40: return "10.6.Domenii de granita (modelarea proiectelor fizice, chimice, biologice si geologice, monocompozite, fizica interiorului pamantului a mediului si spatiului cosmic";break;
    }
   return "Necunoscuta";
}


function getAddProiectCerereURL($id) {
	return "proiect_adauga_cerere.php?idp=".intval($id);
}

function getStatusITColoured($status) {
	   switch($status) {
		   case 1: $st_pr = "<span class=\"c_red\">Nepreluata</span>"; break;
		   case 2: $st_pr = "<span class=\"c_blue\">Preluata</span>"; break;
		   case 3: $st_pr = "<span class=\"c_orange\">In rezolvare</span>"; break;
		   case 4: $st_pr = "<span class=\"c_green\">Finalizata</span>"; break;
		   default: $st_pr = "Eroare";
	   }
	   return $st_pr;
}

function getRegistruIO($nr) {
   switch($nr) {
     case 0: return "Intrare";break;
     case 1: return "Iesire";break;
    }
   return "Necunoscuta";
}

function getTipUserANCS($nr) {
   switch($nr) {
     case 1: return "Utilizator";break;
     case 2: return "Imprimanta";break;
	 case 3: return "Altele";break;
    }
   return "Necunoscuta";
}

function getDA_NU($nr) {
   switch(intval($nr)) {
     case 0: return "NU"; break;
     default: return "DA";
    }
}

function getTipDeplasariANCS($nr) {
   switch($nr) {
	case 1: return "Conferinta"; break;
	case 2: return "Seminar"; break;
	case 3: return "InfoDay"; break;
	case 4: return "Workshop"; break;
	case 5: return "Targ"; break;
	case 6: return "Comitet Program FP7"; break;
	case 7: return "Intalnire NCP"; break;
	case 8: return "Training"; break;
	case 9: return "Concediu"; break;
	case 10: return "Concediu medical"; break;
	case 11: return "Comitet director"; break;
	case 12: return "Grup de lucru"; break;
	case 13: return "Comisie mixta"; break;
	case 14: return "Evaluare"; break;
	case 15: return "Comitet CA"; break;
	case 16: return "Protocol"; break;
	case 18: return "Intalnire proiect"; break;
	case 17: return "Alte"; break;
    }
   return "Necunoscuta";
}
function getTipDeplasariColoursANCS($nr) {
   switch($nr) {	
       case 1: return "#00ffff"; break;
       case 2: return "#66ff33"; break;
       case 3: return "#ffff99"; break;
       case 4: return "#cc99cc"; break;
       case 5: return "#cc9966"; break;
       case 6: return "#cc0066"; break;
       case 7: return "#ff9999"; break;
       case 8: return "#99cc00"; break;
       case 9: return "#ff9900"; break;
       case 10: return "#ff6600"; break;
       case 11: return "#666666"; break;
       case 12: return "#3300ff"; break;
       case 13: return "#9966ff"; break;
       case 14: return "#ccff33"; break;
       case 15: return "#00ff99"; break;
       case 16: return "#999966"; break;
       case 17: return "#ff3300"; break;
       case 18: return "#669999"; break;
    }
   return "#FFF";
}
function getDeplasariIO($nr) {
   switch($nr) {
     case 1: return "Intern";break;
     case 2: return "Extern";break;
    }
   return "Necunoscuta";
}
function getTipTransport($nr) {
   switch($nr) {
     case 1: return "Avion";break;
     case 2: return "Tren";break;
     case 3: return "Masina";break;	 
    }
   return "Necunoscuta";
}




function getDomeniuCap2010($nr) {
   switch($nr) {
     case 1: return "1.Nanotehnologii";break;
     case 2: return "2.Stiinta servicilor";break;
     case 3: return "3.Energia verde";break;
     case 4: return "4.Terapii celulare";break;
    }
   return "Necunoscuta";
}

function printPageLinks($totalRecords, $pageLength, $pageNbr, $getParams) {
	//echo $totalRecords. ' ' . $pageLength. ' '. $pageNbr . ' '. $getParams;
	$getParams = remove_querystring_var($getParams, 'page');
	echo('<div id="pageNavLinks"> Total: <strong>'.$totalRecords.' inregistrari</strong>. Pagini: ');
	$nbrShownPages = 5;
	$nbrPages = intval((($totalRecords > 0) ? ($totalRecords - 1) : 0) / $pageLength) + 1;

	if ($pageNbr > 0){ 	
		echo('<a href="'.$_SERVER['PHP_SELF'].$getParams.'&page='.$pageNbr.'">&laquo;</a>');
	}
	
	$startPage = $pageNbr - $nbrShownPages;
	if ($startPage < 0) {
		$startPage = 0;
	}
	
	$stopPage = $pageNbr + $nbrShownPages + 1;
	if ($stopPage > $nbrPages) {
		$stopPage = $nbrPages;
	}
	
	for ($i = $startPage; $i < $stopPage; $i++){
		if ($i == $pageNbr){
			echo(' | <strong>' . ($pageNbr + 1) . '</strong>');
		}else{
			echo(' | <a href="'.$_SERVER['PHP_SELF'].$getParams.'&page='.($i + 1).'">'.($i + 1).'</a>');
		}
	}
	
	if ($pageNbr + 1 < $nbrPages){
		echo(' | <a href="'.$_SERVER['PHP_SELF'].$getParams.'&page='.($pageNbr + 2).'">&raquo;</a>');
	}
	echo('</div>');
	
}

function remove_querystring_var($url, $key) {
	$url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
	$url = substr($url, 0, -1);
	return ($url);
}

function add_querystring_var($url, $key, $value) {
	$url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
	$url = substr($url, 0, -1);
	if (strpos($url, '?') === false) {
		return ($url . '?' . $key . '=' . $value);
	} else {
		return ($url . '&' . $key . '=' . $value);
	}
}

function getDocNextNr($year = '2011', $sub = 'pres') {
	$N = getQueryInArray("SELECT doc_nr_inreg FROM documente WHERE year = ".$year." AND sub = '".$sub."' ORDER BY doc_nr_inreg DESC LIMIT 1");
	if(($sub == 'memo') && !count($N)) { $N[0]['doc_nr_inreg'] = 2500; }
	if(($sub == 'sg') && !count($N)) { $N[0]['doc_nr_inreg'] = 3000; }
	if(($sub == 'dgen') && !count($N)) { $N[0]['doc_nr_inreg'] = 9000; }	
	if(($sub == 'ddep') && !count($N)) { $N[0]['doc_nr_inreg'] = 9500; }
	return intval(intval($N[0]['doc_nr_inreg']) + 1);
}

function xlsBOF() {
    echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);  
    return;
}

function xlsEOF() {
    echo pack("ss", 0x0A, 0x00);
    return;
}

function xlsWriteNumber($Row, $Col, $Value) {
    echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
    echo pack("d", $Value);
    return;
}

function xlsWriteLabel($Row, $Col, $Value ) {
    $L = strlen($Value);
    echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
    echo $Value;
return; 
}

$lunileAnului = array("1" => "Ianuarie", "2" => "Februarie", "3" => "Martie", "4" => "Aprilie", "5" => "Mai", "6" => "Iunie", "7" => "Iulie", "8" => "August", "9" => "Septembrie", "10" => "Octombrie", "11" => "Noiembrie", "12" => "Decembrie" );
$lunileAnuluiScurt = array("1" => "ian.", "2" => "feb.", "3" => "mar.", "4" => "apr.", "5" => "mai", "6" => "iun.", "7" => "iul.", "8" => "aug.", "9" => "sept.", "10" => "oct.", "11" => "nov.", "12" => "dec." );
$zileleSapt = array("0" => "Duminica", "1" => "Luni", "2" => "Marti", "3" => "Miercuri", "4" => "Joi", "5" => "Vineri", "6" => "Sambata" );	
$ziSapt = array("0" => "DUMINICA", "1" => "LUNI", "2" => "MARTI", "3" => "MIERCURI", "4" => "JOI", "5" => "VINERI", "6" => "SAMBATA" );			
	
function showRODate($filter) {
	global $lunileAnului;
	global $lunileAnuluiScurt;
	list($DDAY, $HHOUR) = split(' ', $filter);
	list($filterYear, $filterMonth, $filterDay) = split('[/.-]', $DDAY);
	return $filterDay.' '.$lunileAnului[intval($filterMonth)].' '.$filterYear;
}
function getDateMonthYear($filter) {
	list($DDAY, $HHOUR) = split(' ', $filter);
	list($filterYear, $filterMonth, $filterDay) = split('[/.-]', $DDAY);
	return $filterYear.'-'.$filterMonth;
}
function getDateMonthYearName($filter) {
	global $lunileAnului;
	list($DDAY, $HHOUR) = split(' ', $filter);
	list($filterYear, $filterMonth, $filterDay) = split('[/.-]', $DDAY);
	return $filterYear.' '.$lunileAnului[intval($filterMonth)];
}

function moveFileToEVENTS_FTP($sourceFile='') {
	global $configArray;
	$fi = "classes/ftp_object.class.php";
	if (file_exists($fi))  { require_once($fi); }
	else { echo("Nu am putut include un fisier. (ftp)"); exit(); }	
	
	//define ("LOG_NONE", 0) ;  // No log
	//define ("LOG_ECHO", 1) ;	// Echo to screen
	//define ("LOG_HIDE", 2) ;	// Echo to HTML comment <!-- -->
	//define ("LOG_FILE", 3) ;	// Echo to file	
	
	$ftp = new FTP($configArray['EventsFtpHost'], $configArray['EventsFtpUser'], $configArray['EventsFtpPass'], 0, 21, 1);
	
	$ftp->lcd($configArray['uploadDir']);
	$ftp->cd('events/uploads/');
	
	if ($ftp->connected) {
		if(strlen($sourceFile) > 3) {
			$success = $ftp->put($sourceFile, 0) ;
		}
	} //endif ftp->conected
	
	return $success;
	
}

function removeFileFromEVENTS_FTP($sourceFile='') {
	global $configArray;
	
	//define ("LOG_NONE", 0) ;  // No log
	//define ("LOG_ECHO", 1) ;	// Echo to screen
	//define ("LOG_HIDE", 2) ;	// Echo to HTML comment <!-- -->
	//define ("LOG_FILE", 3) ;	// Echo to file	
	
	$ftp = new FTP($configArray['EventsFtpHost'], $configArray['EventsFtpUser'], $configArray['EventsFtpPass'], 0, 21, 1);
	
	$ftp->lcd($configArray['uploadDir']);
	$ftp->cd('events/uploads/');	
	
	if ($ftp->connected) {
		if(strlen($sourceFile) > 3) {
			$success = $ftp->del($sourceFile) ;
		}
	} //endif ftp->conected
	

	return $success;
	
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function moveFileToEVENTS($sourceFile='') {
	global $configArray;
	$fi = "classes/class.shell2.php";
	if (file_exists($fi))  { require_once($fi); }
	else { echo("Nu am putut include un fisier. (ssh2)"); exit(); }	
	
	$ssh = new shell2; 
	if ( $ssh->login($configArray['EventsFtpUser'],$configArray['EventsFtpPass'],$configArray['EventsFtpHost']) ) {
	
		//SFTP Send/Upload to remote server
		//$ssh->send(localFile,remoteFile,filePermission)
		if ($ssh->send_file('/var/web/intranet.ancs.ro/uploads/'.$sourceFile, '/var/web/events.ancs.ro/uploads/'.$sourceFile.'', 755)) {
			return true;
			//echo "File has been uploaded\n";
		} else {
			echo $ssh->error;
		}

	
	} else {
		echo $ssh->error;
	}	
	$ssh->disconnect();
	
}

function removeFileFromEVENTS($remoteFile='') {
	global $configArray;
	$fi = "classes/class.shell2.php";
	if (file_exists($fi))  { require_once($fi); }
	else { echo("Nu am putut include un fisier. (ssh2)"); exit(); }		
	
	//define ("LOG_NONE", 0) ;  // No log
	//define ("LOG_ECHO", 1) ;	// Echo to screen
	//define ("LOG_HIDE", 2) ;	// Echo to HTML comment <!-- -->
	//define ("LOG_FILE", 3) ;	// Echo to file	
	

	$ssh = new shell2; 
	if ( $ssh->login($configArray['EventsFtpUser'],$configArray['EventsFtpPass'],$configArray['EventsFtpHost']) ) {
		
		//SFTP Send/Upload to remote server
		//$ssh->send(localFile,remoteFile,filePermission)
		if ($ssh->delete_file('/var/web/events.ancs.ro/uploads/'.$remoteFile)) {
			//echo "File has been deleted\n"; 
			return true;
		} else {
			echo $ssh->error;
		}

	
	} else {
		echo $ssh->error;
	}
	
}


function getTipInventoryObjectANCS($nr) {
   switch($nr) {
     case 1: return "Sistem";break;
     case 2: return "Imprimanta";break;
	 case 3: return "Router / Switch";break;
	 case 4: return "Altele";break;
	 //case 5: return "Telefonie";break;
    }
   return "Necunoscuta";
}


function getTipInventoryObjectStatus($nr) {
   switch($nr) {
     case 1: return "Activ";break;
     case 2: return "Stricat";break;
	 case 3: return "Iesit din uz";break;
    }
   return "Necunoscuta";
}



function getXML($xmlURL) {
	global $configArray;
	
	/*
		$xmlFile = fopen($xmlURL, "r");
		stream_set_blocking($xmlFile,true);  
		stream_set_timeout($xmlFile, 10);  // 5-second timeout  
		$status = stream_get_meta_data($xmlFile);  
		if($status['timed_out']){
			echo "Time out";  
			die;
		}

		$xmlContent = '';
		if ($xmlFile == FALSE){
			echo('<p>We are experiencing some database problems! Please try again later.</p>');
		}else{
			while (!feof($xmlFile)) {
 				$xmlContent .= fread($xmlFile, 8192);
			}
		}
	*/
		//echo '<a href="'.$xmlURL.'" target="_blank">'.$xmlURL.'</a><br />';	
//		echo('<div><a href="'.$xmlURL.'" target="_blank">XML Check</a></div>');
//		echo('<!-- '.$xmlURL.' -->');
		//$enc = mb_detect_encoding($xmlURL);
		//$xmlURL = mb_convert_encoding($xmlURL, 'UTF-8', $enc);
		try{
			$xml = @new SimpleXMLElement($xmlURL, NULL, TRUE);
		}catch(Exception $e) {}
		//print_r($xml);
		//$xml = @new SimpleXMLElement($xmlContent);
		//exit;
		return $xml;
}


function getXMLPageItem($xml, $categ = 'network', $pageTitle = 'Network Information', $pageItem = 'IP Address', $returnProperty = '') {
	global $configArray;
	switch ($categ):
		case 'software': $_CATEG = $xml->software; break;
		case 'hardware': $_CATEG = $xml->hardware; break;
		case 'network': $_CATEG = $xml->network; break;
		default: return '';
	endswitch;	
	foreach ($_CATEG->page as $page) {
		if($page['Title'] == $pageTitle) { // <page>
				$H1 = $page['H1']; // Property
				$H2 = $page['H2']; // Value
				foreach ($page->item as $item) { // <item>
					if($item[$H1] == $pageItem) {
						if(strlen($returnProperty)) { $H2 = $returnProperty; }
						return $item[$H2];
					}
				}//endforeach
		}//endif
	} //endforeach	
}

function getXMLPageItemsList($xml, $categ = 'software', $pageTitle = 'Installed Programs', $itemAttribute = 'Program') {
	global $configArray;
	$return = '';
	switch ($categ):
		case 'software': $_CATEG = $xml->software; break;
		case 'hardware': $_CATEG = $xml->hardware; break;
		case 'network': $_CATEG = $xml->network; break;
		default: return '';
	endswitch;	
	foreach ($_CATEG->page as $page) {
		if($page['Title'] == $pageTitle) { // <page>
				$H1 = $page['H1']; // Program
				$H2 = $page['H2']; // Publisher
				foreach ($page->item as $item) { // <item>
						if(strlen($itemAttribute)) { $H1 = $itemAttribute; }
						$return .= $item[$H1]."\n";

				}//endforeach
		}//endif
	} //endforeach	
	
	return $return;
}

function getXMLcountRAM($xml, $categ = 'hardware', $pageTitle = 'Memory', $pageItem = 'Device Locator') {
	global $configArray;
	$capacity = 0;
	switch ($categ):
		case 'software': $_CATEG = $xml->software; break;
		case 'hardware': $_CATEG = $xml->hardware; break;
		case 'network': $_CATEG = $xml->network; break;
		default: return '';
	endswitch;	
	foreach ($_CATEG->page as $page) {
		if($page['Title'] == $pageTitle) { // <page>
				$H1 = $page['H1']; // Property
				$H2 = $page['H2']; // Value
				foreach ($page->item as $item) { // <item>
					if($item[$H1] == $pageItem) {
						foreach ($item->item as $subitem) { // <subitem>
							if($subitem[$H1] == 'Capacity') {
								$capacity = $capacity + intval($subitem[$H2]);
							}
						}
					}
				}//endforeach
		}//endif
	} //endforeach	
	
	return formatBytes($capacity*1024*1024);
}



function getXMLDevices($xml, $categ = 'hardware', $pageTitle = 'Devices', $pageItem = 'Display adapters') {
	global $configArray;
	$return = '';
	switch ($categ):
		case 'software': $_CATEG = $xml->software; break;
		case 'hardware': $_CATEG = $xml->hardware; break;
		case 'network': $_CATEG = $xml->network; break;
		default: return '';
	endswitch;	
	foreach ($_CATEG->page as $page) {
		if($page['Title'] == $pageTitle) { // <page>
				$H1 = $page['H1']; // Property
				$H2 = $page['H2']; // Value
				foreach ($page->item as $item) { // <item>
					if($item[$H1] == $pageItem) {
						return $item->item[$H1];
					}
				}//endforeach
		}//endif
	} //endforeach	
	
	return formatBytes($capacity*1024*1024);
}

function generateDetaliiHistory($_ID = 0, $type = 1) {
	global $configArray;
?>	

						
                        <?php
						$QRY3 = 'SELECT * FROM desc_type_archive_'.intval($type).' WHERE obiect_id = '.$_ID.' ORDER BY data DESC';
						$_DATA3 = getQueryInArray($QRY3, $configArray['STRUCTdbcnx']);
							
						if(count($_DATA3)):						
						?>
                        <h2 style="color:#999;">Istoric Detalii</h2>
                        <table cellspacing="4" border="0" style="width:500px; font-size:11px; padding:0px;">                                   
						<?php
                            for($i=0;$i<count($_DATA3);$i++) {
                                ?>
                                    <tr<?php if(!($i%2)) { print(' class="TRgrey"'); } ?>>
                                        <td align="left" valign="top" style="padding:2px;"><a href="inventar_desc_view_type_<?php echo(intval($type)); ?>.php?noheader=1&id=<?php echo($_DATA3[$i]['desc_id']); ?>" rev="width: 900px; height: 500px; scrolling: auto;" rel="lyteframe" class="oldDesc"><img src="images/icon16_calendar.png" width="16" height="16" border="0" /> <?php echo($_DATA3[$i]['data']); ?></a></td>
                                        <td align="left" valign="top" style="width:100%; padding:2px 2px 2px 5px;">
                                        	<?php if(file_exists($configArray['uploadDir'].'xml/'.$_DATA3[$i]['file_xml']) && strlen($_DATA3[$i]['file_xml'])): ?>
                                        		<a href="<?php echo($configArray['uploadDirWeb'].'xml/'.$_DATA3[$i]['file_xml']); ?>" class="oldDesc" target="_blank"><img src="images/icon16_xml.png" width="16" height="16" border="0" /> <?php echo($_DATA3[$i]['file_xml']); ?></a>
                                            <?php else: ?>
                                            not avaialble
                                            <?php endif; ?>
                                        </td>
                                    </tr>                                                                             
								<?php
                            }//endfor
                        ?>           
                                    <tr>
                                        <td align="left" valign="top"><img src="images/spacer.gif" width="150" height="1" border="0" alt="" /></td>
                                        <td align="left" valign="top" class="error"></td>
                                     </tr>                                         
                        </table>  
                        <?php endif; ?> 
<?php
}




function generateMultipleSelectionCheckboxes($controlName = 'ckname', $idTitle = 'id', $nameTitle = 'name', $valuesArray = array(), $selectedValuesArray = array(), $divClass = '') {
	global $error_msg;
	for($i=0;$i<count($valuesArray);$i++) {
		$optId = $valuesArray[$i][$idTitle];
		$optName = $valuesArray[$i][$nameTitle];
		if(in_array($optId, $selectedValuesArray)) $checked = ' checked="checked"'; else $checked = '';
		?><div class="<?php echo($divClass); ?>"><input class="checkBox" type="checkbox" name="<?php echo($controlName); ?>[]"<?php echo($checked); ?> value="<?php echo($optId); ?>" /> <?php echo($optName); ?></div><?php 
	}
	?>&nbsp;<span class="error"> <?=$error_msg[$controlName]?> </span><?php
}

function generateRadiosControls($name, $idTitle = 'id', $nameTitle = 'name', $disp = array(), $current = '', $divClass = '') {
	global $error_msg;
	for($i=0;$i<count($disp);$i++) {
		$radioId = $disp[$i][$idTitle];
		$radioName = $disp[$i][$nameTitle];
		?><div class="<?php echo($divClass); ?>">
			<input <?php if($i != count($disp)-1) {} ?> class="radio" type="radio" name="<?php echo($name); ?>" value="<?php echo($radioId); ?>"<?php if($radioId == $current) { print(' checked="checked"'); } ?> /> <?php echo($radioName); ?>
		   </div> <?php
	}//endfor
	?>&nbsp;<span class="error"><?=$error_msg[$name]?></span><?php
}

function generateRadios012TD($name, $current = NULL, $divClass = '') {
	global $error_msg;
	$radioNames = array(0 => '', 1 => '', 2 => '');	
	for($i=0;$i<count($radioNames);$i++) {
		?><div class="<?php echo($divClass); ?>">
			<input class="radio" type="radio" name="<?php echo($name); ?>" value="<?php echo($i); ?>"<?php if($i === $current) { print(' checked="checked"'); } ?> /> <?php echo($radioNames[$i]); ?>
		   </div> <?php
	}//endfor
	?>&nbsp;<span class="error"><?=$error_msg[$name]?></span><?php
}

function generateRadiosDaNuCheckbox($name, $current = NULL, $divClass = '') {
	global $error_msg;
	$radioNames = array(0 => 'Nu', 1 => 'Da');	
	//echo intval($current);
	for($i=1;$i>=0;$i--) {
		?><div class="<?php echo($divClass); ?>">
			<input<?php if(($current < 0 || $current > 1) && ($_POST[$name.'_cb'] != 1)) print(' disabled="disabled"'); ?> class="radio" type="radio" name="<?php echo($name); ?>" value="<?php echo($i); ?>"<?php if($i === $current) { print(' checked="checked"'); } ?> /> <?php echo($radioNames[$i]); ?>
		   </div> <?php
	}//endfor
	?>&nbsp;<span class="error"><?=$error_msg[$name]?></span><?php
}

function generateRadiosInputOutputCheckbox($name, $current = NULL, $divClass = '') {
	global $error_msg;
	$radioNames = array(0 => 'Intrare', 1 => 'Iesire');	
	
	for($i=0;$i<=1;$i++) {
		?><div class="<?php echo($divClass); ?>">
			<input<?php if($current < 0 || $current > 1) print(' disabled="disabled"'); ?> class="radio" type="radio" name="<?php echo($name); ?>" value="<?php echo($i); ?>"<?php if(intval($i) == intval($current)) { print(' checked="checked"'); } ?> /> <?php echo($radioNames[$i]); ?>
		   </div> <?php
	}//endfor
	?>&nbsp;<span class="error"><?=$error_msg[$name]?></span><?php
}

function generateSelectControl($controlName = 'name', $idTitle = 'id', $nameTitle = 'name', $valuesArray = array(), $selectedValue = 0, $class = '', $alegeti = false) {
	global $error_msg;
	?><select name="<?php echo($controlName); ?>" class="<?php echo($class); ?>"><?php
	if($alegeti) { ?><option value="">Alegeti ...</option><?php }
	for($i=0;$i<count($valuesArray);$i++) {
		$optId = $valuesArray[$i][$idTitle];
		$optName = $valuesArray[$i][$nameTitle];
		if($optId == $selectedValue) $checked = ' selected="selected"'; else $checked = '';
		?><option value="<?php echo($optId); ?>" <?php echo($checked); ?>><?php echo($optName); ?></option><?php
	}
	?></select>
    &nbsp;<span class="error"> <?=$error[$controlName]?> </span><?php
}


function generateCategoriesCheckboxes($selected_categs) {
	global $configArray;
	
	if(!is_array($selected_categs)) { $selected_categs = array(); }
	$level_0 = returnArrayWhere($configArray['categs'], 'parent_id', 0);
	
	//LEVEL 0
	print '<ul>'."\n";
	for($i=0;$i<count($level_0);$i++) {
		$currentId = $level_0[$i]['categ_id'];
		$currentName = $level_0[$i]['categ_name'];
		$currentCheckBoxId = 'cb'.$level_0[$i]['categ_id'];
		$currentCheckBoxName = 'cb'.$level_0[$i]['categ_id'];
		if(in_array($currentId,$selected_categs)) { $isSelected = ' checked="checked"'; } else { $isSelected = ''; }
		print '<li>'."\n";

		//LEVEL 1
		$level_1 = returnArrayWhere($configArray['categs'], 'parent_id', $currentId);	
		
		//check if at least one sibling is checked
		$checkedSiblingsCount = 0;
		for($j=0;$j<count($level_1);$j++) { if(in_array($level_1[$j]['categ_id'], $selected_categs)) { $checkedSiblingsCount++; } }
		if(($checkedSiblingsCount > 0) && ($checkedSiblingsCount < count($level_1))) { $isIntermediate = true; } else { $isIntermediate = false; }
		if(count($level_1) && $isIntermediate) { $level_0_extra_class = ' indeterminate'; } else { $level_0_extra_class = ''; }
		
        print "\t".'<input'.$isSelected.' class="css-checkbox'.$level_0_extra_class.'" type="checkbox" name="'.$currentCheckBoxId.'" id="'.$currentCheckBoxId.'" value="'.$currentId.'">'."\n";
        print "\t".'<label class="css-label" for="'.$currentCheckBoxName.'">'.($currentName).'</label>'."\n";

		if(count($level_1)) {

			print "\t".'<div id="plusminus_'.$currentCheckBoxName.'" class="plusminus plus"></div>'."\n";
			print "\t".'<ul id="plusminus_content_'.$currentCheckBoxName.'" class="plusminus_content">'."\n";
				for($j=0;$j<count($level_1);$j++) {
					$currentSubId = $level_1[$j]['categ_id'];
					$currentSubName = $level_1[$j]['categ_name'];
					$currentSubCheckBoxId = 'cb'.$level_1[$j]['categ_id'];
					$currentSubCheckBoxName = 'cb'.$level_1[$j]['categ_id'];
					if(in_array($currentSubId,$selected_categs)) { $isSubSelected = ' checked="checked"'; } else { $isSubSelected = ''; }
					print "\t".'<li>'."\n";
					print "\t\t".'<input'.$isSubSelected.' class="css-checkbox" type="checkbox" name="'.$currentSubCheckBoxName.'" id="'.$currentSubCheckBoxId.'" value="'.$currentSubId.'">'."\n";
					print "\t\t".'<label class="css-label" for="'.$currentSubCheckBoxName.'">'.$currentSubName.'</label>'."\n";
					print "\t".'</li>'."\n";
				}//endfor j	
			print "\t".'</ul>'."\n";	

		}//endif level_1
		
		print '</li>'."\n";
	}//endfor i
	print '</ul>'."\n";
}




function getReverseGeocode($lat,$lon) {
	$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lon.'';
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$response = json_decode(curl_exec($ch), true);
	if ($response['status'] != 'OK') {
	  	return 'An error has occured: ' . print_r($response, true);
	} else {
		$returnStr = '';
		$aaa = $response['results'][0]['address_components'];
		for($i=0;$i<count($aaa);$i++) {
			if($aaa[$i]['types'][0] == 'administrative_area_level_2') { $returnStr .= $aaa[$i]['long_name'].', '; }
			if($aaa[$i]['types'][0] == 'administrative_area_level_1') { $returnStr .= $aaa[$i]['long_name'].', '; }
			if($aaa[$i]['types'][0] == 'country') { $returnStr .= $aaa[$i]['long_name'].''; }
		}
		return ($returnStr);
	}
	
}


