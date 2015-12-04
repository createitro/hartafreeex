<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL & ~E_NOTICE);
session_start();
date_default_timezone_set('Europe/Paris');

require_once('server.php');
require_once('functions.php');
require_once('headers.php');
$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__FILE__));
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/phpmailer/PHPMailerAutoload.php');

$config['dbserver'] = '____________';
$config['dbname'] = '______________';
$config['dbuser'] = '______________';
$config['dbpass'] = '______________';

$config['smtpUser'] = '______________@activewatch.ro';
$config['smtpPass'] = '_____________';
$config['smtpPort'] = '25';
$config['smtpServer'] = '_____________';
$config['smtpEmail'] = '______________@activewatch.ro';
$config['smtpName'] = 'Harta FreeEx';

/*
$config['sendValidationRequestToEmail'] = 'dan@createit.ro';
$config['sendValidationRequestToName'] = 'Dan Birtas';
$config['sendValidationRequestCC'][0]['email'] = 'dan.birtas@gmail.com';
$config['sendValidationRequestCC'][0]['name'] = 'Dan Gmail';
*/

$config['sendValidationRequestToEmail'] = '________@activewatch.ro';
$config['sendValidationRequestToName'] = 'Free Ex Activewatch';
$config['sendValidationRequestCC'][0]['email'] = 'user1@activewatch.ro';
$config['sendValidationRequestCC'][0]['name'] = 'user one';


$config['mailSignature'] = 'Echipa Activewatch FreeEx<br /><br />';


$config['siteURL'] = 'http://freeex.activewatch.ro/';
$config['filesUploadDir'] = '/path/to/uploads/';
$config['webUploadDir'] = $config['siteURL'].'uploads/';

//files where login is not required
//$config['publicFiles'] = array('index.php');    

$config['sesizare']['imgBig']['w'] = 300;
$config['sesizare']['imgBig']['h'] = 200;
$config['sesizare']['imgThumb']['w'] = 140;
$config['sesizare']['imgThumb']['h'] = 70;

$config['currentFile'] = basename(strtolower($_SERVER['SCRIPT_NAME']));
$config['currentMenuSection'] = 'index';

$config['messages'] = array();

$config['dbConnection'] = new PDO('mysql:dbname='.$config['dbname'].';host='.$config['dbserver'].';charset=utf8', $config['dbuser'], $config['dbpass'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
$config['dbConnection']->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$config['dbConnection']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$config['categs'] = getQueryInArray("SELECT * FROM categories WHERE 1 ORDER BY categ_id ASC", array());	

$config['pages'] = getQueryInArray("SELECT * FROM pages WHERE 1 ORDER BY page_id ASC", array());	

$config['time'] = time();
