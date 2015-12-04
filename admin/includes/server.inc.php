<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL & ~E_NOTICE);

@session_start();

require_once("config.inc.php"); 
require_once("headers.inc.php"); 
require_once("functions.inc.php"); 


function getFileExtension($str) {
	$i = strrpos($str,".");
	if (!$i) { return ""; }
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
}

function getFieldWhere($table, $field, $searchField, $searchValue) {
		$select = "SELECT '".$field."' FROM ".$table." WHERE ".$searchField."='".$searchValue."' ";
        $current_table = mysql_query($select);
        if (!$current_table){ echo("Query failed. <br />". "Error: ". mysql_error());  exit();  }
		$k = 0; //contor
        $rand = mysql_fetch_array($current_table);
        $r_field = $rand[$field];
		//if (!(isset($r_field))) $r_field = "undefined";
		return $r_field;
}

function setFieldWhere($table, $fieldName, $fieldValue, $searchField, $searchValue) {	
	$upd = "UPDATE ".$table." SET ".$fieldName."='".$fieldValue."' WHERE ".$searchField."='".$searchValue."' ";
    $change = mysql_query($upd) or die(mysql_error());
}

function deleteFieldWhere($table, $searchField, $searchValue) {	
	$del = "DELETE FROM ".$table." WHERE ".$searchField."='".$searchValue."' ";
    $change = mysql_query($del) or die(mysql_error());
}
function isFile($filename) {
	  if ( isset($filename) and ($filename != '') and (file_exists($filename)))  { return 1; } else { return 0; }
}
function getQueryInArray($select, $link='') {
	global $configArray;
	if($link == '') { $link = $configArray['dbcnx']; }
	$current_table = mysql_query($select, $link);
	if (!$current_table) { echo("Query failed. <br />". "Error: ".mysql_error().' '.$select);  exit(); }
	$totalRows = 0; //contor
	$queryResults = array();
	while ($rand = mysql_fetch_array($current_table, MYSQL_ASSOC)) {	
		$queryResults[$totalRows] = $rand;
		$totalRows++;
	}
	return $queryResults;
}

function getArrayValueWhere($array, $field, $searchField, $searchValue) {
	global $configArray;
	$return = '';
	for($i=0; $i<count($array); $i++) {
		if($array[$i][$searchField] == $searchValue) {
			$return = $array[$i][$field];
			break;
		}
	}
	return $return;
}

function checkArrayValue($array, $searchField, $searchValue) {
	global $configArray;
	$return = false;
	for($i=0; $i<count($array); $i++) {
		if($array[$i][$searchField] == $searchValue) {
			$return = true;
			break;
		}
	}
	return $return;
}

function prepareName($name) {
	return strtolower(urlencode(html_entity_decode(substr(preg_replace('/[\W]+/', '_', $name), 0, 120))));
}

function prepareFileName($name, $type='_a') {
	return substr(preg_replace('/[\W]+/', '_', $name), 0, 120).'_'.create_random_string(10, 'digits').$type;
}

function faraDiacritice($formatedText) {
	$formatedText = str_replace("ã","a",$formatedText);
	$formatedText = str_replace("&atilde;","a",$formatedText);
	$formatedText = str_replace("a","a",$formatedText);	
	$formatedText = str_replace("&acirc;","a",$formatedText);	
	$formatedText = str_replace("Ã","A",$formatedText);		
	$formatedText = str_replace("A","A",$formatedText);		
	$formatedText = str_replace("â","a",$formatedText);	
	$formatedText = str_replace("Â","A",$formatedText);			
	$formatedText = str_replace("î","i",$formatedText);
	$formatedText = str_replace("&icirc;","i",$formatedText);
	$formatedText = str_replace("Î","I",$formatedText);
	$formatedText = str_replace("&Icirc;","I",$formatedText);			
	$formatedText = str_replace("s","s",$formatedText);
	$formatedText = str_replace("º","s",$formatedText);	
	$formatedText = str_replace("S","S",$formatedText);	
	$formatedText = str_replace("ª","S",$formatedText);		
	$formatedText = str_replace("t","t",$formatedText);	
	$formatedText = str_replace("þ","t",$formatedText);		
	$formatedText = str_replace("T","T",$formatedText);		
	$formatedText = str_replace("Þ","T",$formatedText);
	$formatedText = preg_replace('/(&nbsp;)+/', ' ', $formatedText);
	return $formatedText;
}

function html2txt($document){
	$search = array('@<script[^>]*?>.*?</script>@si',  	// Strip out javascript
               '@<[\\/\\!]*?[^<>]*?>@si',            	// Strip out HTML tags
               '@<style[^>]*?>.*?</style>@siU',    		// Strip style tags properly
               '@<![\\s\\S]*?--[ \\t\\n\\r]*>@'   		// Strip multi-line comments including CDATA 
				);
	$text = preg_replace($search, '', $document);
	return strip_tags($text);
}

function html2txtFull($document){
	$text = faraDiacritice(html2txt($document));
	$text = str_replace(array('&bull;', '&nbsp;', '&ndash;'), array(' ', ' ', ' '), $text);
	return $text;
}

function nl2brStrict($text) {
   return preg_replace("((\r\n)+)", '<br />', $text);
}

function mysql_real_escape_string_sql($string) {
	return str_replace("'", "''", $string);
	//return $string;
}

function validate_email($email) {
	if ($email != '') {
		if (preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $email)) { return true; }
	}
	return false;
}

function create_random_string($length, $type = 'mixed') {
    if (!in_array($type, array('mixed', 'chars', 'digits'))) {
      return false;
    }
    $chars_pattern = 'abcdefghijklmnopqrstuvwxyz';
    $mixed_pattern = '1234567890' . $chars_pattern;

    $rand_value = '';

    while (strlen($rand_value) < $length) {
      if ($type == 'digits') {
        $rand_value .= _rand(0,9);
      } elseif ($type == 'chars') {
        $rand_value .= substr($chars_pattern, _rand(0, 25), 1);
      } else {
        $rand_value .= substr($mixed_pattern, _rand(0, 35), 1);
      }
    }
    return $rand_value;
}


function _rand($min = null, $max = null) {
    static $seeded;
    if (!isset($seeded)) {
      if (version_compare(PHP_VERSION, '4.2', '<')) {
        mt_srand((double)microtime()*1000000);
      }

      $seeded = true;
    }

    if (is_numeric($min) && is_numeric($max)) {
      if ($min >= $max) {
        return $min;
      } else {
        return mt_rand($min, $max);
      }
    } else {
      return mt_rand();
    }
}
  
  
function encrypt_string($plain) { 
    $password = '';
    for ($i=0; $i<10; $i++) {
      $password .= _rand();
    }
    $salt = substr(md5($password), 0, 2);
    $password = md5($salt . $plain) . ':' . $salt;
    return $password;
  }
  
function get_ip_address() {
    if (isset($_SERVER)) {
      if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      } else {
        $ip = $_SERVER['REMOTE_ADDR'];
      }
    } else {
      if (getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
      } elseif (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
      } else {
        $ip = getenv('REMOTE_ADDR');
      }
    }
    return $ip;
  }
  
//this is a workaround for jsspecialchars!
function ord2($s) {
	if (strlen($s) == 2) {
		return ord(substr($s,1,1));
	} else {
		return ord($s);
	}
}

function jsspecialchars($s) {
    return preg_replace('/([^ !#$%@()*+,-.\x30-\x5b\x5d-\x7e])/e',
        "'\\x'.(ord('\\1')<16? '0': '').dechex(ord2('\\1'))",$s);
}  

function swap(&$v, $i, $j) { 
	$temp = $v[$i]; 
	$v[$i] = $v[$j]; 
	$v[$j] = $temp; 
} 


function qsort(&$int_array, $left, $right, $sortField) { 
	if ($left >= $right) 
		return; 
	swap ($int_array, $left, intval(($left+$right)/2)); 
	$last = $left; 
	for ($i = $left + 1; $i <= $right; $i++) 
		if ($int_array[$i][$sortField] > $int_array[$left][$sortField]) 
			swap($int_array, ++$last, $i); 
			swap($int_array, $left, $last); 
			
	qsort($int_array, $left, $last-1, $sortField); 
	qsort($int_array, $last+1, $right, $sortField); 
} 
function mySqlEscape($str) {
	$str =  str_replace("'", "''", $str);
	return $str;
}

function url_exists($url) {
	//return true; 
	// fails on events.ancs.ro
	if(strlen($url) > 2):
		$handle = @fopen($url, "r");
		if ($handle === false) { return false; }
		fclose($handle);
		return true;
	else: 
		return false;
	endif;
}

function redirect($filename) {
	if (!headers_sent())
		header('Location: '.$filename);
	else {
		echo '<script type="text/javascript">';
		echo 'window.location.href="'.$filename.'";';
		echo '</script>';
		echo '<noscript>';
		echo '<meta http-equiv="refresh" content="0;url='.$filename.'" />';
		echo '</noscript>';
	}	
	exit();
}

function strip_punctuation( $text ) {
	$urlbrackets    = '\[\]\(\)';
	$urlspacebefore = ':;\'_\*%@&?!' . $urlbrackets;
	$urlspaceafter  = '\.,:;\'\-_\*@&\/\\\\\?!#' . $urlbrackets;
	$urlall         = '\.,:;\'\-_\*%@&\/\\\\\?!#' . $urlbrackets;

	$specialquotes = '\'"\*<>';

	$fullstop      = '\x{002E}\x{FE52}\x{FF0E}';
	$comma         = '\x{002C}\x{FE50}\x{FF0C}';
	$arabsep       = '\x{066B}\x{066C}';
	$numseparators = $fullstop . $comma . $arabsep;

	$numbersign    = '\x{0023}\x{FE5F}\x{FF03}';
	$percent       = '\x{066A}\x{0025}\x{066A}\x{FE6A}\x{FF05}\x{2030}\x{2031}';
	$prime         = '\x{2032}\x{2033}\x{2034}\x{2057}';
	$nummodifiers  = $numbersign . $percent . $prime;

	return preg_replace(
		array(
		// Remove separator, control, formatting, surrogate,
		// open/close quotes.
			'/[\p{Z}\p{Cc}\p{Cf}\p{Cs}\p{Pi}\p{Pf}]/u',
		// Remove other punctuation except special cases
			'/\p{Po}(?<![' . $specialquotes .
				$numseparators . $urlall . $nummodifiers . '])/u',
		// Remove non-URL open/close brackets, except URL brackets.
			'/[\p{Ps}\p{Pe}](?<![' . $urlbrackets . '])/u',
		// Remove special quotes, dashes, connectors, number
		// separators, and URL characters followed by a space
			'/[' . $specialquotes . $numseparators . $urlspaceafter .
				'\p{Pd}\p{Pc}]+((?= )|$)/u',
		// Remove special quotes, connectors, and URL characters
		// preceded by a space
			'/((?<= )|^)[' . $specialquotes . $urlspacebefore . '\p{Pc}]+/u',
		// Remove dashes preceded by a space, but not followed by a number
			'/((?<= )|^)\p{Pd}+(?![\p{N}\p{Sc}])/u',
		// Remove consecutive spaces
			'/ +/',
		),
		' ',
		$text );
}

function strip_symbols( $text ) {
	$plus   = '\+\x{FE62}\x{FF0B}\x{208A}\x{207A}';
	$minus  = '\x{2012}\x{208B}\x{207B}';

	$units  = '\\x{00B0}\x{2103}\x{2109}\\x{23CD}';
	$units .= '\\x{32CC}-\\x{32CE}';
	$units .= '\\x{3300}-\\x{3357}';
	$units .= '\\x{3371}-\\x{33DF}';
	$units .= '\\x{33FF}';

	$ideo   = '\\x{2E80}-\\x{2EF3}';
	$ideo  .= '\\x{2F00}-\\x{2FD5}';
	$ideo  .= '\\x{2FF0}-\\x{2FFB}';
	$ideo  .= '\\x{3037}-\\x{303F}';
	$ideo  .= '\\x{3190}-\\x{319F}';
	$ideo  .= '\\x{31C0}-\\x{31CF}';
	$ideo  .= '\\x{32C0}-\\x{32CB}';
	$ideo  .= '\\x{3358}-\\x{3370}';
	$ideo  .= '\\x{33E0}-\\x{33FE}';
	$ideo  .= '\\x{A490}-\\x{A4C6}';

	return preg_replace(
		array(
		// Remove modifier and private use symbols.
			'/[\p{Sk}\p{Co}]/u',
		// Remove math symbols except + - = ~ and fraction slash
			'/\p{Sm}(?<![' . $plus . $minus . '=~\x{2044}])/u',
		// Remove + - if space before, no number or currency after
			'/((?<= )|^)[' . $plus . $minus . ']+((?![\p{N}\p{Sc}])|$)/u',
		// Remove = if space before
			'/((?<= )|^)=+/u',
		// Remove + - = ~ if space after
			'/[' . $plus . $minus . '=~]+((?= )|$)/u',
		// Remove other symbols except units and ideograph parts
			'/\p{So}(?<![' . $units . $ideo . '])/u',
		// Remove consecutive white space
			'/ +/',
		),
		' ',
		$text );
}


function strip_html_tags( $text ) {
    $text = preg_replace(
        array(
          // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
          // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $text );
    return strip_tags( $text );
}

function strip_numbers( $text ) {
    $urlchars      = '\.,:;\'=+\-_\*%@&\/\\\\?!#~\[\]\(\)';
    $notdelim      = '\p{L}\p{M}\p{N}\p{Pc}\p{Pd}' . $urlchars;
    $predelim      = '((?<=[^' . $notdelim . '])|^)';
    $postdelim     = '((?=[^'  . $notdelim . '])|$)';
 
    $fullstop      = '\x{002E}\x{FE52}\x{FF0E}';
    $comma         = '\x{002C}\x{FE50}\x{FF0C}';
    $arabsep       = '\x{066B}\x{066C}';
    $numseparators = $fullstop . $comma . $arabsep;
    $plus          = '\+\x{FE62}\x{FF0B}\x{208A}\x{207A}';
    $minus         = '\x{2212}\x{208B}\x{207B}\p{Pd}';
    $slash         = '[\/\x{2044}]';
    $colon         = ':\x{FE55}\x{FF1A}\x{2236}';
    $units         = '%\x{FF05}\x{FE64}\x{2030}\x{2031}';
    $units        .= '\x{00B0}\x{2103}\x{2109}\x{23CD}';
    $units        .= '\x{32CC}-\x{32CE}';
    $units        .= '\x{3300}-\x{3357}';
    $units        .= '\x{3371}-\x{33DF}';
    $units        .= '\x{33FF}';
    $percents      = '%\x{FE64}\x{FF05}\x{2030}\x{2031}';
    $ampm          = '([aApP][mM])';
 
    $digits        = '[\p{N}' . $numseparators . ']+';
    $sign          = '[' . $plus . $minus . ']?';
    $exponent      = '([eE]' . $sign . $digits . ')?';
    $prenum        = $sign . '[\p{Sc}#]?' . $sign;
    $postnum       = '([\p{Sc}' . $units . $percents . ']|' . $ampm . ')?';
    $number        = $prenum . $digits . $exponent . $postnum;
    $fraction      = $number . '(' . $slash . $number . ')?';
    $numpair       = $fraction . '([' . $minus . $colon . $fullstop . ']' .
        $fraction . ')*';
 
    return preg_replace(
        array(
        // Match delimited numbers
            '/' . $predelim . $numpair . $postdelim . '/u',
        // Match consecutive white space
            '/ +/u',
        ),
        ' ',
        $text );
}

function getAgeFromCNP($cnp, $today) {
	$birthDay = substr( $cnp , 5 , 2 );
	$birthMonth = substr( $cnp , 3 , 2 );
	$birthYear = substr( $cnp , 1 , 2 );
	switch ($cnp[0]){
		case '3':
		case '4':
			$birthYear = intval('18'.$birthYear); break;
		case '5':
		case '6':
			$birthYear = intval('20'.$birthYear); break;
		default: 
			$birthYear = $birthYear = intval('19'.$birthYear);
	}	
	return floor(getMonthBetween($birthDay.'/'.$birthMonth.'/'.$birthYear, $today) / 12);
	//return $varsta; 
}

function validate_cnp($value) {
	$cnp = $value;
	$txt = array();
	for ($i=0; $i<strlen($cnp); $i++){ $txt[$i] = substr($cnp, $i, 1); } //endfor
	if(count($txt)==13){
		$s = $txt[0]*2 + $txt[1]*7 + $txt[2]*9 + $txt[3]*1 + $txt[4]*4 + $txt[5]*6 + $txt[6]*3 + $txt[7]*5 + $txt[8]*8 + $txt[9]*2 + $txt[10]*7 + $txt[11]*9;    
		$rest = ($s%11);
		if($rest == 10) $rest = 1;
		if($rest == $txt[12]){
			return 1;
		} else { 
			return 0;
		}
	} else { 
		return 0;
	}
}

/////////////////////////////////////////////////////////
// validates a string comparing it to the format dd/mm/yyyy
function validateDate($tfValue) {
	if ($tfValue[2] != '/' || $tfValue[5] != '/'){
		return false;
	}
	
	$currDay = intval(unpadStringLeft2(substr($tfValue, 0, 2)));
	$currMonth = intval(unpadStringLeft2(substr($tfValue, 3, 2)));
	$currYear = intval(substr($tfValue, 6, 4));
		
	if ($currMonth > 12 || $currYear < 1900 || $currYear > 2099 || $currDay > getLastDayFromMonth($currYear, $currMonth - 1)){
		return false;
	}
	
	return true;	
}

function getLastDayFromMonth($aYear, $aMonth) {
	//ian = 0 => dec = 11
	if ($aMonth == 0 || $aMonth == 2 || $aMonth == 4 || $aMonth == 6 || $aMonth == 7 || $aMonth == 9 || $aMonth == 11){
		return 31;
	}
	if ($aMonth == 3 || $aMonth == 5 || $aMonth == 8 || $aMonth == 10){
		return 30;
	}
	if ($aMonth == 1){
		if ($aYear % 4 == 0 && $aYear % 100 != 0 || $aYear % 400 == 0){
			return 29;
		}else{
			return 28;
		}
	}
	
	
}

function unpadStringLeft2($aString) {
	if ($aString[0] == '0'){
		return '' + $aString[1];
	}else{
		return $aString;
	}
}

function createTimestampFromDate($stringDate){
	return mktime(0, 0, 0, substr($stringDate, 3, 2), substr($stringDate, 0, 2), substr($stringDate, 6, 4));
}

function getMonthBetween($startDate, $endDate){
	$startDay = substr($startDate, 0, 2);
	$endDay = substr($endDate, 0, 2);
	$startMonth = substr($startDate, 3, 2);
	$endMonth = substr($endDate, 3, 2);
	$startYear = substr($startDate, 6, 4);
	$endYear = substr($endDate, 6, 4);
	
	$addOne = 0;
	if ($startDay <= $endDay){
		$addOne = 1;
	}
	return ($endYear - $startYear) * 12 + ($endMonth - $startMonth) + $addOne;
}
// end date functions
/////////////////////////////////////////////////////////

function textResize($text, $size) {
	return smart_trim($text, $size, false, '');
}

function smart_trim($text, $max_len, $trim_middle = false, $trim_chars = '...') {
	$text = trim($text);

	if (strlen($text) < $max_len) {

		return $text;

	} elseif ($trim_middle) {

		$hasSpace = strpos($text, ' ');
		if (!$hasSpace) {
			/**
			 * The entire string is one word. Just take a piece of the
			 * beginning and a piece of the end.
			 */
			$first_half = substr($text, 0, $max_len / 2);
			$last_half = substr($text, -($max_len - strlen($first_half)));
		} else {
			/**
			 * Get last half first as it makes it more likely for the first
			 * half to be of greater length. This is done because usually the
			 * first half of a string is more recognizable. The last half can
			 * be at most half of the maximum length and is potentially
			 * shorter (only the last word).
			 */
			$last_half = substr($text, -($max_len / 2));
			$last_half = trim($last_half);
			$last_space = strrpos($last_half, ' ');
			if (!($last_space === false)) {
				$last_half = substr($last_half, $last_space + 1);
			}
			$first_half = substr($text, 0, $max_len - strlen($last_half));
			$first_half = trim($first_half);
			if (substr($text, $max_len - strlen($last_half), 1) == ' ') {
				/**
				 * The first half of the string was chopped at a space.
				 */
				$first_space = $max_len - strlen($last_half);
			} else {
				$first_space = strrpos($first_half, ' ');
			}
			if (!($first_space === false)) {
				$first_half = substr($text, 0, $first_space);
			}
		}

		return $first_half.$trim_chars.$last_half;

	} else {

		$trimmed_text = substr($text, 0, $max_len);
		$trimmed_text = trim($trimmed_text);
		if (substr($text, $max_len, 1) == ' ') {
			/**
			 * The string was chopped at a space.
			 */
			$last_space = $max_len;
		} else {
			/**
			 * In PHP5, we can use 'offset' here -Mike
			 */
			$last_space = strrpos($trimmed_text, ' ');
		}
		if (!($last_space === false)) {
			$trimmed_text = substr($trimmed_text, 0, $last_space);
		}
		return remove_trailing_punctuation($trimmed_text).$trim_chars;

	}

}

function remove_trailing_punctuation($text) {
	return preg_replace("'[^a-zA-Z_0-9]+$'s", '', $text);
}

function insertIntoTable($ins) {	
	$change = mysql_query($ins) or die(mysql_error());
}

function updateTable($ins) {	
	$change = mysql_query($ins) or die(mysql_error());
}

function fileUpload($sourcefile, $destfile) {
// open_basedir restriction getover
//   if(file_exists($sourcefile))
//       {
	    if (move_uploaded_file($sourcefile, $destfile)) return true;
		else return false;
//       }
//       else
//       return false;
} 


function resampleImageRectangleProportion($forcedwidth = 175, $proportion1 = 0.66666666666666666, $sourcefile, $destfile, $imgcomp, $ext)
   {
   $g_imgcomp = 100 - $imgcomp;
   $g_srcfile = $sourcefile;
   $g_dstfile = $destfile;
   
   $forcedheight = round($forcedwidth/$proportion1);

   if(file_exists($g_srcfile))
       {
		   list($orig_w, $orig_h) = getimagesize($g_srcfile); 
		   $proportion0 = $orig_w/$orig_h;
			
			if($proportion0 > $proportion1) {
				$src_h = $orig_h;
				$src_w = round($orig_h*$proportion1);
				$dif = $orig_w - $src_w;
		   		$src_x = round($dif/2);
		   		$src_y = 0;				
			} else {
		   		$src_w = $orig_w;
				$src_h = round($orig_w/$proportion1);
				$dif = $orig_h - $src_h;
				$src_x = 0;
				$src_y = round($dif/2);				
			}	      

			switch ($ext) { 
			  case ".jpg": $img_src=imagecreatefromjpeg($g_srcfile); break; 
			  case ".gif": $img_src=imagecreatefromgif($g_srcfile); break; 
			  case ".png": $img_src=imagecreatefrompng($g_srcfile); break; 
			} 
		   
       $img_dst=imagecreatetruecolor($forcedwidth, $forcedheight);
       imagecopyresampled($img_dst, $img_src, 0, 0, $src_x, $src_y, $forcedwidth, $forcedheight, $src_w, $src_h);
	   
			switch ($ext) { 
			  case ".jpg": imagejpeg($img_dst, $g_dstfile, $g_imgcomp); break; 
			  case ".gif": imagegif($img_dst, $g_dstfile, $g_imgcomp); break; 
			  case ".png": imagepng($img_dst, $g_dstfile, $g_imgcomp); break; 
			} 	   
       imagedestroy($img_dst);
       return true;
       }
       else
       return false;
   }
   
function resampleImageForceWidth($forcedwidth = 175, $sourcefile, $destfile, $imgcomp, $ext)
   {
   $g_imgcomp = 100 - $imgcomp;
   $g_srcfile = $sourcefile;
   $g_dstfile = $destfile;

	if(file_exists($g_srcfile))
       {
		   list($orig_w, $orig_h) = getimagesize($g_srcfile); 
		   $proportion0 = $orig_w/$orig_h;
		   $forcedheight = round($forcedwidth/$proportion0);
			
			switch ($ext) { 
			  case ".jpg": $img_src=imagecreatefromjpeg($g_srcfile); break; 
			  case ".gif": $img_src=imagecreatefromgif($g_srcfile); break; 
			  case ".png": $img_src=imagecreatefrompng($g_srcfile); break; 
			} 
		   
       $img_dst=imagecreatetruecolor($forcedwidth, $forcedheight);
       imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $forcedwidth, $forcedheight, $orig_w, $orig_h);
	   
			switch ($ext) { 
			  case ".jpg": imagejpeg($img_dst, $g_dstfile, $g_imgcomp); break; 
			  case ".gif": imagegif($img_dst, $g_dstfile, $g_imgcomp); break; 
			  case ".png": imagepng($img_dst, $g_dstfile, $g_imgcomp); break; 
			} 	   
       imagedestroy($img_dst);
       return true;
       }
       else
       return false;
   }
   


function resampleImageOld($forcedwidth, $forcedheight, $sourcefile, $destfile, $imgcomp, $ext)
   {
   $g_imgcomp=100-$imgcomp;
   $g_srcfile=$sourcefile;
   $g_dstfile=$destfile;
   $g_fw=$forcedwidth;
   $g_fh=$forcedheight;

   if(file_exists($g_srcfile))
       {
       $g_is=getimagesize($g_srcfile);
       if(($g_is[0]-$g_fw)>=($g_is[1]-$g_fh))
           {
           $g_iw=$g_fw;
           $g_ih=($g_fw/$g_is[0])*$g_is[1];
           }
           else
           {
           $g_ih=$g_fh;
           $g_iw=($g_ih/$g_is[1])*$g_is[0];    
           }

			switch ($ext) { 
			  case ".jpg": $img_src=imagecreatefromjpeg($g_srcfile); break; 
			  case ".gif": $img_src=imagecreatefromgif($g_srcfile); break; 
			  case ".png": $img_src=imagecreatefrompng($g_srcfile); break; 
			} 
		   
       $img_dst=imagecreatetruecolor($g_iw,$g_ih);
       imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $g_iw, $g_ih, $g_is[0], $g_is[1]);
	   
			switch ($ext) { 
			  case ".jpg": imagejpeg($img_dst, $g_dstfile, $g_imgcomp); break; 
			  case ".gif": imagegif($img_dst, $g_dstfile, $g_imgcomp); break; 
			  case ".png": imagepng($img_dst, $g_dstfile, $g_imgcomp); break; 
			} 	   
       
       imagedestroy($img_dst);
       return true;
       }
       else
       return false;
   } 
   

function deleteFile($tableName, $tablePrimaryKeyName, $tablePrimaryKeyValue, $videoName) {
	global $configArray;
	$unu = getFieldWhere($tableName, $videoName, $tablePrimaryKeyName, $tablePrimaryKeyValue);
	if ( isset($unu) and ($unu != '') )  {
		if (file_exists($configArray['uploadDir'].$unu)) 
				if(!unlink($configArray['uploadDir'].$unu)) { echo('could not delete video'); return 0; }
			setFieldWhere($tableName, $videoName, '', $tablePrimaryKeyName, $tablePrimaryKeyValue);
	}
	return 1;	
}

function is_date($date) {
        $date = str_replace(array('\'', '-', '.', ','), '/', $date);
        $date = explode('/', $date);

        if(    count($date) == 1 // No tokens
            and    is_numeric($date[0])
            and    $date[0] < 20991231 and
            (    checkdate(substr($date[0], 4, 2)
                        , substr($date[0], 6, 2)
                        , substr($date[0], 0, 4)))
        )
        {
            return true;
        }
       
        if(    count($date) == 3
            and    is_numeric($date[0])
            and    is_numeric($date[1])
            and is_numeric($date[2]) and
            (    checkdate($date[0], $date[1], $date[2]) //mmddyyyy
            or    checkdate($date[1], $date[0], $date[2]) //ddmmyyyy
            or    checkdate($date[1], $date[2], $date[0])) //yyyymmdd
        )
        {
            return true;
        }
       
        return false;
} 

//function to display the contents of an array in a "tree" format
function display_array($array, $array_index=0) {
    //create a global variable to hold our variable
    //during the recursion
    global $array_tree;
    $display="";

    //loop through the size of the array
    for ($i=0;$i<$array_index;$i++)
    {
        //increse our space so as to display 
        //the array in a tree display
        $display .= "     ";
    }

    //check to make sure we're dealing with an array
    if(gettype($array)=="array")
    {
        //increment our index
        $array_index++;
        //now we ill use list to loop while each item
        //in our list matches items in our array
        while (list ($key, $value) = each ($array))
        {
            //tack the value to our global variable
            $array_tree .= $display."$key => $value\n";
            //call function to recurse through the array
            display_array($value, $array_index);
        }
    }
}

function getArrayValueWhereCond($array, $field, $searchField, $searchValue, $searchField2, $searchValue2) {
	global $configArray;
	$return = '';
	for($i=0; $i<count($array); $i++) {
		if(($array[$i][$searchField] == $searchValue) && ($array[$i][$searchField2] == $searchValue2)) {
			$return = $array[$i][$field];
			break;
		}
	}
	return $return;
}


function enum_select( $table , $field ) {
	$query = " SHOW COLUMNS FROM `$table` LIKE '$field' ";
	$result = mysql_query( $query ) or die( 'error getting enum field ' . mysql_error() );
	$row = mysql_fetch_array( $result , MYSQL_NUM );
	#extract the values
	#the values are enclosed in single quotes
	#and separated by commas
	$regex = "/'(.*?)'/";
	preg_match_all( $regex , $row[1], $enum_array );
	$enum_fields = $enum_array[1];
	return( $enum_fields );
} 

function redirectToHTTPS() {
	if($_SERVER['HTTPS'] != "on") {
		$redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		header("Location:".$redirect);
	}
}


function getIP() {
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if (isset($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];
	else $ip = "UNKNOWN";
	return $ip;
}



function print_nice($elem,$max_level=10,$print_nice_stack=array()){
    if(is_array($elem) || is_object($elem)){
        if(in_array(&$elem,$print_nice_stack,true)){
            echo "<font color=red>RECURSION</font>";
            return;
        }
        $print_nice_stack[]=&$elem;
        if($max_level<1){
            echo "<font color=red>nivel maximo alcanzado</font>";
            return;
        }
        $max_level--;
        echo "<table border=1 cellspacing=0 cellpadding=3 width=100%>";
        if(is_array($elem)){
            echo '<tr><td colspan=2 style="background-color:#333333;"><strong><font color=white>ARRAY</font></strong></td></tr>';
        }else{
            echo '<tr><td colspan=2 style="background-color:#333333;"><strong>';
            echo '<font color=white>OBJECT Type: '.get_class($elem).'</font></strong></td></tr>';
        }
        $color=0;
        foreach($elem as $k => $v){
            if($max_level%2){
                $rgb=($color++%2)?"#888888":"#BBBBBB";
            }else{
                $rgb=($color++%2)?"#8888BB":"#BBBBFF";
            }
            echo '<tr><td valign="top" style="width:40px;background-color:'.$rgb.';">';
            echo '<strong>'.$k."</strong></td><td>";
            print_nice($v,$max_level,$print_nice_stack);
            echo "</td></tr>";
        }
        echo "</table>";
        return;
    }
    if($elem === null){
        echo "<font color=green>NULL</font>";
    }elseif($elem === 0){
        echo "0";
    }elseif($elem === true){
        echo "<font color=green>TRUE</font>";
    }elseif($elem === false){
        echo "<font color=green>FALSE</font>";
    }elseif($elem === ""){
        echo "<font color=green>EMPTY STRING</font>";
    }else{
        echo str_replace("\n","<strong><font color=red>*</font></strong><br>\n",$elem);
    }
} 



function returnArrayWhere($ARR, $col, $val) {
	$NEW_ARR = array();
	$k = 0;
	for($i=0;$i<count($ARR);$i++) {
		if($ARR[$i][$col] == $val) { $NEW_ARR[$k] = $ARR[$i]; $k++; }
	}
	return $NEW_ARR;
	$NEW_ARR = NULL;		
}

function returnArrayWhereCond($ARR, $col, $cond = '==', $val = 0) {
	$NEW_ARR = array();
	$k = 0;
	for($i=0;$i<count($ARR);$i++) {
		eval("$"."condIf = ("."$"."ARR["."$"."i]["."$"."col] ".$cond." "."$"."val);");
		if($condIf) { $NEW_ARR[$k] = $ARR[$i]; $k++; }
	}
	return $NEW_ARR;
	$NEW_ARR = NULL;		
}

function begins_with($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
}



