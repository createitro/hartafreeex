<?php


	// include server.inc.php
	$fi = "includes/server.inc.php";
	if (file_exists($fi)) { require_once($fi); } else { echo('Nu am putut include un fisier.'); exit(); }



	$_QRY = "SELECT `email` FROM `parteneri` WHERE pos <> -1 AND proiect_id IN ";
	echo('<br><br>C2_CDI <br>');
	$_QRY .= "(292,289,404,240,213,360,228,225,246,326,384,249,413,295,251,312,279,216,230,234,377,333,259,341,220,325,387,278,362,397,408,237,250,254,273,363,352,269,307,329,394,403,270)";
	$_DATA = getQueryInArray($_QRY); 
	
	for($i=0;$i<count($_DATA);$i++) { echo($_DATA[$i]['email'].'; '); }
	
	


	$_QRY = "SELECT `email` FROM `parteneri` WHERE pos <> -1 AND proiect_id IN ";
	echo('<br><br>C2_CCTS <br>');
	$_QRY .= "(327,411,366,331,314,390,381)";
	$_DATA = getQueryInArray($_QRY); 
	
	for($i=0;$i<count($_DATA);$i++) { echo($_DATA[$i]['email'].'; '); }
	
	
	
	$_QRY = "SELECT `email` FROM `parteneri` WHERE pos <> -1 AND proiect_id IN ";
	echo('<br><br>C1_CDI <br>');	
	$_QRY .= "(77,101,172,165,188,152,197,32,123,81,153,156,170,14,35,167,186,109,116,168,200,70,193,151,73,127,128,140,171,191,59,203,67,131,169,82,85,91,132,149,184,187)";
	$_DATA = getQueryInArray($_QRY); 
	
	for($i=0;$i<count($_DATA);$i++) { echo($_DATA[$i]['email'].'; '); }	
	
	
	
	$_QRY = "SELECT `email` FROM `parteneri` WHERE pos <> -1 AND proiect_id IN ";
	echo('<br><br>C1_PS <br>');	
	$_QRY .= "(139,143,103,60,161)";
	$_DATA = getQueryInArray($_QRY); 
	
	for($i=0;$i<count($_DATA);$i++) { echo($_DATA[$i]['email'].'; '); }	
	
	
	
	
	
	
	
	
