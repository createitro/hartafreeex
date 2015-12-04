<?php
require_once('server.inc.php');

// Database
// OLD 
date_default_timezone_set("Europe/Helsinki"); 	
// Database
$configArray['DBhostname'] =  '___________';  
$configArray['DBuserName'] = '____________';
$configArray['DBpassword'] = '____________';
$configArray['DBname'] = '________________';


//delete access
$configArray['DBuserName'] = '_______________';
$configArray['DBpassword'] = '_______________';


//Site specific
$configArray['sitename'] = 'Free Ex Activewatch Admin';
$configArray['defaultTitle'] = 'Free Ex Activewatch Admin';
$configArray['siteURL'] = 'http://freeex.activewatch.ro/admin/';
$configArray['notifyEmail'] = 'your.email@server.com';
$configArray['adminEmail'] = 'your.email@server.com';
$configArray['SUBDIR'] = '';		// void when placing directly in root ... $SUBDIR = "/subDir_1/subDir_2"; 
$configArray['PATH'] = $_SERVER["DOCUMENT_ROOT"] . $configArray['SUBDIR'];
$configArray['uploadDir'] = $configArray['PATH'] .'/uploads/';
$configArray['uploadDirWeb'] = $configArray['siteURL'].'uploads/';
$configArray['uploadDocsDir'] = $configArray['PATH'] .'/docs/';
$configArray['uploadDocsDirWeb'] = $configArray['siteURL'].'docs/';

$configArray['starUploadDir'] = '/storage/hosting/activewatch.ro/ftp/freeexact/uploads/';
$configArray['starUploadDirWeb'] = $configArray['siteURL'].'uploads/';

$configArray['PicMainLarge']['w'] = '2000';
$configArray['PicMainLarge']['h'] = '4000';
$configArray['PicMain']['w'] = '300';
$configArray['PicMain']['h'] = '640';
$configArray['PicThumb']['w'] = '50';
$configArray['PicThumb']['h'] = '120';
$configArray['PicThumb']['proportion'] = $configArray['PicThumb']['w'] / $configArray['PicThumb']['h'];
$configArray['PicMain']['proportion'] = $configArray['PicMain']['w'] / $configArray['PicMain']['h'];


if(intval($_GET['noheader']) == 1) { $configArray['noHeader'] = 1; } else { $configArray['noHeader'] = 0; }

//DB Connect
/*/////////////////////////////// DB  CONN /////////////////////////////////////////////////////////////////////////// */  

function intranetDBConnect() {
	global $configArray;
	$configArray['dbcnx'] = @mysql_connect($configArray['DBhostname'], $configArray['DBuserName'], $configArray['DBpassword']);
	if (!$configArray['dbcnx']) { echo( "No server connection."); exit(); }
	if (!mysql_select_db($configArray['DBname']) ) { echo( "No database connection.");  exit(); }
	 mysql_query ("set character_set_client='utf8'"); 
	 mysql_query ("set character_set_results='utf8'"); 	
	 mysql_query ("set collation_connection='utf8_general_ci'");	
}



intranetDBConnect();



/*/////////////////////////////// END DB  CONN /////////////////////////////////////////////////////////////////////// */   

$configArray['lunileAnului'] = array("1" => "Ianuarie", "2" => "Februarie", "3" => "Martie", "4" => "Aprilie", "5" => "Mai", "6" => "Iunie", "7" => "Iulie", "8" => "August", "9" => "Septembrie", "10" => "Octombrie", "11" => "Noiembrie", "12" => "Decembrie" );
$configArray['lunileAnuluiScurt'] = array("1" => "ian.", "2" => "feb.", "3" => "mar.", "4" => "apr.", "5" => "mai", "6" => "iun.", "7" => "iul.", "8" => "aug.", "9" => "sept.", "10" => "oct.", "11" => "nov.", "12" => "dec." );
$configArray['zileleSapt'] = array("0" => "Duminica", "1" => "Luni", "2" => "Marti", "3" => "Miercuri", "4" => "Joi", "5" => "Vineri", "6" => "Sambata" );	


$configArray['prototypeOn'] = 0;
$configArray['jqueryOn'] = 1;

$configArray['rightRead'] = 1;
$configArray['rightWrite'] = 1;			



$configArray['cont_admin_tipuri'][0]['id'] = 'superadmin';
$configArray['cont_admin_tipuri'][0]['name'] = 'Admin';
$configArray['cont_admin_tipuri'][1]['id'] = 'editor';
$configArray['cont_admin_tipuri'][1]['name'] = 'Editor';
$configArray['cont_admin_tipuri'][2]['id'] = 'contributor';
$configArray['cont_admin_tipuri'][2]['name'] = 'Contributor';




$configArray['categs'] = getQueryInArray("SELECT * FROM categories WHERE 1 ORDER BY categ_id ASC" );	


?>