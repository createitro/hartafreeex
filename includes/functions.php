<?php
require_once('server.php');
require_once('functions.php');
require_once('headers.php');

function LogOut() {
	unset($_SESSION['userId']);
	unset($_SESSION['userEmail']);
	unset($_SESSION['userName']);
	unset($_SESSION['userType']);
	//unset($_SESSION['pageViews']);
}

function printMessages() {
	global $config;
	?>
    	<div class="container_12">
        <div class="grid_12">
        	<?php for($i=0;$i<count($config['messages']);$i++) { 
					$type = $config['messages'][$i]['type'];
					$msg = $config['messages'][$i]['text'];
				?>
				<div class="messageBox">
					<div class="<?php echo( (($type == 1)?'successImg':'failImg') ); ?>"></div><h3 class="<?php echo( (($type == 1)?'green':'red') ); ?>"><?php echo($msg); ?></h3>
					<div class="clear"></div>
				</div>
            <?php } ?>
        </div>    
        </div>
    <?php
}

$config['globalMessages'][101] = 'Sesizarea a fost trimisa spre validare!';
$config['globalMessages'][102] = 'Pagina cautata nu exista!';
$config['globalMessages'][103] = 'Sesizarea cautata nu exista';
$config['globalMessages'][104] = 'Sesizarea a fost deja validata';
$config['globalMessages'][105] = 'Sesizarea validata cu succes';
$config['globalMessages'][106] = 'Cererea de alerta a fost adaugata cu succes pentru categoriile selectate';

function queueParamMessage() {
	global $config;
	if(isset($_GET['m'])) {
		$m = intval($_GET['m']);
		if($m > 0) {
			queueMessage($config['globalMessages'][$m], $type = 1);
		}
	}
	
}

function queueMessage($msg, $type = 1) {
	if(!strlen($msg)) { return false; }
	global $config;
	$len = count($config['messages']);
	$config['messages'][$len]['text'] = $msg;
	$config['messages'][$len]['type'] = $type;
}

function generateCategoriesCheckboxes($selected_categs) {
	global $config;
	if(!is_array($selected_categs)) { $selected_categs = array(); }
	$level_0 = returnArrayWhere($config['categs'], 'parent_id', 0);
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
		$level_1 = returnArrayWhere($config['categs'], 'parent_id', $currentId);	
		
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



function printSesizareInList($_SESIZARE) {
	global $config;
	//url 
	$url = getSesizareURL($_SESIZARE['sesizare_id']);
	//image
	if(is_file($config['filesUploadDir'].$_SESIZARE['file_input_thumb'])) {
		$imgTag = '<a href="'.$url.'"><img class="reportImg" src="'.$config['webUploadDir'].$_SESIZARE['file_input_thumb'].'" width="140" border="0" /></a>';
	} else {
		$imgTag = '<a href="'.$url.'"><img class="reportImg" src="images/sesizare_thumb_default.jpg" width="140" border="0" /></a>';
	}
	//descriere
	$_SESIZARE['sesizare_descriere'] = textResize(htmlspecialchars_decode($_SESIZARE['sesizare_descriere']), 150);
	//data ora
	$data_ora_str = getDataOraNice($_SESIZARE['data_ora']);
	//location 
	$location_str = getLocationNice($_SESIZARE['location_search'], $_SESIZARE['location_reverse']);
	?>
        	<div class="report">
            	<?php echo($imgTag); ?>
                <div class="reportInfo">
                	<h2><?php echo('<a href="'.$url.'">'.$_SESIZARE['sesizare_titlu'].'</a>'); ?></h2>
                    <p><?php echo('<a href="'.$url.'">'.strip_tags($_SESIZARE['sesizare_descriere']).'</a>'); ?></p>
                    <div class="metaInfo"><?php echo($data_ora_str); ?> - <?php echo($location_str); ?></div>
                </div>
            </div>
            <div class="clear"></div>    
    <?php
}

function getDataOraNice($str) {
	$currentTime = strtotime($str);
	$data_ora_str = date("j M Y", $currentTime);
	return $data_ora_str;	
}

function getLocationNice($location_search, $location_reverse) {
	if(strlen($location_search) > 2) return ucwords($location_search);	
	else return $location_reverse;
}

function getSesizareURL($s) {
	global $config;	
	return $config['siteURL'].'sesizare.php?s='.intval($s);
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

function shareThisOnPage() {
	?>
        <span class='st_facebook_large' displayText='Facebook'></span>
        <span class='st_twitter_large' displayText='Tweet'></span>
        <span class='st_linkedin_large' displayText='LinkedIn'></span>
        <span class='st_pinterest_large' displayText='Pinterest'></span>
        <span class='st_digg_large' displayText='Digg'></span>
        <span class='st_email_large' displayText='Email'></span>   
    <?php	
}



function sendSesizareToApprover($_SESIZARE_ID) {
		global $config;
		
		//get sesizare by ID
		$_SEARCH_SESIZARE = getQueryInArray("SELECT * FROM sesizari WHERE deleted = 0 AND sesizare_id = :sesizare_id LIMIT 1", array("sesizare_id" => $_SESIZARE_ID) );
		if(count($_SEARCH_SESIZARE)) {
			$_SESIZARE = $_SEARCH_SESIZARE[0];
		} else {
			return false;
		}
		
		//categorii
		$_CATEGORII_SESIZARE = getQueryInArray("SELECT c.* FROM mm_categs_sesizari mc JOIN categories c ON mc.categ_id = c.categ_id WHERE mc.sesizare_id = :sesizare_id", array('sesizare_id' => $_SESIZARE_ID));
		$categsStr = '';
		for($i=0;$i<count($_CATEGORII_SESIZARE);$i++) {
			$categsStr .= $_CATEGORII_SESIZARE[$i]['categ_name'].'';
			if($i<count($_CATEGORII_SESIZARE)-1) { $categsStr .= ' &nbsp; | &nbsp; '; }
		}//endfor categorii
		
		//linkuri
		$_LINKURI_SESIZARE = getQueryInArray("SELECT l.* FROM mm_sesizari_linkuri l WHERE l.sesizare_id = :sesizare_id AND l.link_sursa <> '' ", array('sesizare_id' => $_SESIZARE_ID));
		$linkuriStr = '';
		for($i=0;$i<count($_LINKURI_SESIZARE);$i++) {
			$linkURL = $_LINKURI_SESIZARE[$i]['link_sursa'];
			$linkuriStr .= '<a href="'.$linkURL.'" target="_blank">'.$linkURL.'</a><br />';
		}

		$validate_code = $_SESIZARE['validation_code'];
		$validate_code_url = $config['siteURL'].'validate.php?h='.$validate_code;		
		
		//from
		$senderName = $config['smtpName'];
		$senderEmail = $config['smtpEmail'];
		
		//subject
		$subject = 'Sesizare Harta FreeEx';
		
		//to
		$email = $config['sendValidationRequestToEmail'];
		$name = $config['sendValidationRequestToName'];
		
		//compose mail
		$mailHtml = '';
		$mailHtml .= '<p>Titlu: <strong>'.$_SESIZARE['sesizare_titlu'].'</strong></p>';		
		$mailHtml .= '<p>Descriere: <br />'.nl2br($_SESIZARE['sesizare_descriere']).'</p>';		
		$mailHtml .= '<p>Categorii: &nbsp; <strong><em>'.$categsStr.'</em></strong></p>';		
		if(count($_LINKURI_SESIZARE)) {	$mailHtml .= '<p>Linkuri sesizare: <br />'.$linkuriStr.'</p>';	}
		$mailHtml .= '<p>Data: '.$_SESIZARE['data_ora'].'</p>';		
		if(strlen($_SESIZARE['location_search'])) { $locationSearchStr = ' <em>['.$_SESIZARE['location_search'].']</em>'; } else { $locationSearchStr = ''; }
		$mailHtml .= '<p>Localizare: '.$_SESIZARE['location_reverse'].' ('.$_SESIZARE['coord_lon'].', '.$_SESIZARE['coord_lat'].')</p>';		
		if(strlen($_SESIZARE['file_input'])) { 
			$img_file_url = $config['webUploadDir'].$_SESIZARE['file_input'];
			$mailHtml .= '<p>Imagine: <a href="'.$img_file_url.'" target="_blank">'.$img_file_url.'</a></p>';
		}
		$mailHtml .= '<p>&nbsp;</p>';
		if(strlen($_SESIZARE['personal_nume'])) { $mailHtml .= '<p>Nume: '.$_SESIZARE['personal_nume'].'</p>'; }
		if(strlen($_SESIZARE['personal_prenume'])) { $mailHtml .= '<p>Prenume: '.$_SESIZARE['personal_prenume'].'</p>'; }
		if(strlen($_SESIZARE['personal_email'])) { $mailHtml .= '<p>E-mail: '.$_SESIZARE['personal_email'].'</p>'; }
		if(strlen($_SESIZARE['personal_telefon'])) { $mailHtml .= '<p>Telefon: '.$_SESIZARE['personal_telefon'].'</p>'; }
		$mailHtml .= '<p>Adaugat la: '/*.$_SESIZARE['added_by_ip'].' la '*/.$_SESIZARE['added_at'].'</p>';		
		
		$mailHtml .= '<p>&nbsp;</p>';
		$mailHtml .= '<p><strong>VALIDARE SESIZARE: </strong><a href="'.$validate_code_url.'" target="_blank">'.$validate_code_url.'</a></p>';
		
		$mailHtml .= '<p>&nbsp;</p>';
		$mailHtml .= $config['mailSignature'];

		//send mail
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		$mail->Host = $config['smtpServer'];
		$mail->Port = $config['smtpPort'];
		$mail->SMTPAuth = true;
		$mail->CharSet = 'UTF-8';
		$mail->Encoding = '8bit';
		$mail->Timeout = 10;
		$mail->addAddress($email, $name);
		for($i=0;$i<count($config['sendValidationRequestCC']);$i++) {
			//addCC
			$mail->addAddress($config['sendValidationRequestCC'][$i]['email'], $config['sendValidationRequestCC'][$i]['name']);
		}		
		$mail->Subject = $subject;
		$mail->AltBody = 'URL: '.$validate_code_url;					
		$mail->Username = $config['smtpUser'];
		$mail->Password = $config['smtpPass'];
		$mail->setFrom($config['smtpEmail'], $config['smtpName']);
		$mail->msgHTML($mailHtml);
		$mail->IsHTML(true);
		//send the message, check for errors
		if (!$mail->send()) {
			die($mail->ErrorInfo);
		} else {
		}

}

function sendAlertsForSesizare($_SESIZARE) {
		global $config;	
		
		$_SESIZARE_ID = $_SESIZARE['sesizare_id'];
		
		//get categorii		
		$_CATEGORII_SESIZARE = getQueryInArray("SELECT c.* FROM mm_categs_sesizari mc JOIN categories c ON mc.categ_id = c.categ_id WHERE mc.sesizare_id = :sesizare_id", array('sesizare_id' => $_SESIZARE_ID));
		$categIdsStr = generateCommaSeparatedStringFromArrayColumn($_CATEGORII_SESIZARE, 'categ_id');
		
		//get alert emails for categories in $_CATEGORII_SESIZARE
		if(count($_CATEGORII_SESIZARE)) {
			$_DESTINATARI_ALERTE = getQueryInArray("SELECT * FROM mm_categs_alerte ca JOIN alerte a ON ca.alerta_id = a.alerta_id WHERE ca.categ_id IN (".$categIdsStr.") GROUP BY a.alerta_email", array() );			
			for($i=0;$i<count($_DESTINATARI_ALERTE);$i++) {
				sendSesizareAlertaToUser($_SESIZARE, $_DESTINATARI_ALERTE[$i]);
			}//endfor _DESTINATARI_ALERTE			
			
		}		
}


function sendSesizareAlertaToUser($_SESIZARE, $_DESTINATAR_ALERTA) {
		global $config;
		
		//check data
		if(!is_array($_SESIZARE)) {	return false; }
		if(!is_array($_DESTINATAR_ALERTA)) {	return false; }
		
		//prepare data for emails
		$_SESIZARE_ID = $_SESIZARE['sesizare_id'];
		$sesizare_url = getSesizareURL($_SESIZARE_ID);		
		
		//from
		$senderName = $config['smtpName'];
		$senderEmail = $config['smtpEmail'];
		
		//subject
		$subject = 'Sesizare noua in Harta FreeEx';
		
		//to
		$email = $_DESTINATAR_ALERTA['alerta_email'];
		$name = $_DESTINATAR_ALERTA['alerta_nume'];
		
		//compose mail
		$mailHtml = '';
		$mailHtml .= '<p>O noua sesizare a fost adaugata in Harta FreeEx:</p>';		
		$mailHtml .= '<p><strong>&quot;'.$_SESIZARE['sesizare_titlu'].'&quot;</strong><br /><strong>Click aici: </strong><a href="'.$sesizare_url.'" target="_blank">'.$sesizare_url.'</a></p>';
		$mailHtml .= '<p>&nbsp;</p>';
		$mailHtml .= '<p><em>Primiti acest e-mail deoarece v-ati abonat la primirea alertelor pe site-ul Harta FreeEx.<br />
					  Daca nu mai doriti primirea acestor mesaje, va rugam sa ne contactati la adresa office@activewatch.ro<br />
					  E-mail trimis automat. Nu dati Reply la acest e-mail.</em></p>';
		$mailHtml .= '<p>&nbsp;</p>';
		$mailHtml .= $config['mailSignature'];

		//send mail
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		$mail->Host = $config['smtpServer'];
		$mail->Port = $config['smtpPort'];
		$mail->SMTPAuth = true;
		$mail->CharSet = 'UTF-8';
		$mail->Encoding = '8bit';
		$mail->Timeout = 10;
		$mail->addAddress($email, $name);
		$mail->Subject = $subject;
		$mail->AltBody = 'URL: '.$validate_code_url;					
		$mail->Username = $config['smtpUser'];
		$mail->Password = $config['smtpPass'];
		$mail->setFrom($config['smtpEmail'], $config['smtpName']);
		$mail->msgHTML($mailHtml);
		$mail->IsHTML(true);
		//send the message, check for errors

		if (!$mail->send()) {
			die($mail->ErrorInfo);
		} else {
		}

}


function getPageText($page_id = 0) {
			global $config;
			$PAGE = returnArrayWhere($config['pages'], 'page_id', $page_id);
			if(count($PAGE)) { 
				return ($PAGE[0]['page_text']);
			} else {
				return '';
			}
}


