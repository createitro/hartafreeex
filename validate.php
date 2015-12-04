<?php
	require_once('includes/config.php');	

	//get params
	$h = filter_var($_GET['h'], FILTER_SANITIZE_STRING);
	if(!strlen($h)) { redirect($config['siteURL']); }
	
	$_SESIZARE = getQueryInArray("SELECT * FROM sesizari WHERE deleted = 0 AND validation_code = :validation_code LIMIT 1", array('validation_code' => $h));
	if(!count($_SESIZARE)) { redirect(add_querystring_var($config['siteURL'],'m','103')); }
	
	if($_SESIZARE[0]['validated'] == 1) {
		redirect(add_querystring_var(getSesizareURL($_SESIZARE[0]['sesizare_id']),'m','104'));
	} else {
		//validare
		$updateTableStr = "UPDATE sesizari SET validated = :validated WHERE sesizare_id = :sesizare_id ";
		$updateTableArray = array('validated' => intval(1), 'sesizare_id' => $_SESIZARE[0]['sesizare_id'] );  		
		updateTable($updateTableStr, $updateTableArray);
		//trimitere e-mail alerta catre abonati
		sendAlertsForSesizare($_SESIZARE[0]);
		//redirectare in pagina de sesizare + mesaj succes
		redirect(add_querystring_var(getSesizareURL($_SESIZARE[0]['sesizare_id']),'m','105'));
	}