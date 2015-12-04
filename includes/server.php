<?php
require_once('server.php');
require_once('functions.php');
require_once('headers.php');

function getQueryInArray($str, $columns = array()) {
	global $config;
	$results = array();
	$preparedStatement = $config['dbConnection']->prepare($str);
	$preparedStatement->setFetchMode(PDO::FETCH_ASSOC);
	$preparedStatement->execute($columns);
	$i = 0;
	while($row = $preparedStatement->fetch()) {
		$results[$i] = $row;
		$i++;
	}	
	return $results;
}

function insertIntoTable($insertStr = '', $columns = array()) {
	global $config;
	//print_nice($columns);
	$preparedStatement = $config['dbConnection']->prepare($insertStr);
	$preparedStatement->execute($columns);
}

function updateTable($updateStr = '', $columns = array()) {
	global $config;
	$preparedStatement = $config['dbConnection']->prepare($updateStr);
	$preparedStatement->execute($columns);
}
function deleteFromTable($deleteStr = '', $columns = array()) {
	global $config;
	$preparedStatement = $config['dbConnection']->prepare($deleteStr);
	$preparedStatement->execute($columns);
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

function validate_email($email) {
	if ($email != '') {
		if (preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$/is', $email)) { return true; }
	}
	return false;
}

function hasDuplicateFieldInArray($ARR, $col = 'col') {
	for($i=0;$i<(count($ARR)-1);$i++) {
		$val = $ARR[$i][$col];
		for($j=$i+1;$j<count($ARR);$j++) {
			if($ARR[$i][$col] == $ARR[$j][$col]) { return true; }
		}
	}
	return false;
}


function getIP() {
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if (isset($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];
	else $ip = "UNKNOWN";
	return $ip;
}



function print_nice($elem,$max_level=10,$print_nice_stack=array()){
    if(is_array($elem) || is_object($elem)){
        if(in_array($elem,$print_nice_stack,true)){
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

function redirect($filename) {
	if (!headers_sent())
		header('Location: '.$filename);
	else {
		echo '<script type="text/javascript">';
		echo 'window.location.href = \''.$filename.'\';';
		echo '</script>';
		echo '<noscript>';
		echo '<meta http-equiv="refresh" content="0;url=\''.$filename.'\'" />';
		echo '</noscript>';
	}
	exit();
}


function getFileExtension($str) {
	$i = strrpos($str,".");
	if (!$i) { return ""; }
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
}

function fileUpload($sourcefile, $destfile) {
	    if (move_uploaded_file($sourcefile, $destfile)) return true;
		else return false;
}

function url(){ 
   return sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI']
  );
}

function prepareFileName($name, $type='') {
	return substr(preg_replace('/[\W]+/', '_', $name), 0, 120).'_'.create_random_string(10, 'digits').$type;
}

function isJson($string) {
	json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
}

function getDaysUntilDate($date) {
	
	$future = strtotime($date);
	$now = time();
	if($future <= $now) { return 0; }
	$timeleft = $future - $now;
	$daysleft = round((($timeleft/24)/60)/60);
	return $daysleft;
}

function callUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 200);
    curl_exec($ch);
    curl_close($ch);
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

function array_prepend_assoc($AA, $EE) {
	//EE added to begining of AA array
	return array_merge(array(0 => $EE), $AA);
} 


/* backup the db OR just a table */
function backup_tables($host,$user,$pass,$name,$tables = '*') {
	
	$link = mysql_connect($host,$user,$pass);
	mysql_select_db($name,$link);
	
	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = mysql_query('SHOW TABLES');
		while($row = mysql_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	
	//cycle through
	foreach($tables as $table)
	{
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);
		
		$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	
	//save file
	$handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);
}

function begins_with($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
}


function textResize($text, $size) {
	return smart_trim($text, $size, false, '');
}
function remove_trailing_punctuation($text) {
	return preg_replace("'[^a-zA-Z_0-9]+$'s", '', $text);
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

function generateCommaSeparatedStringFromArrayColumn($ARR, $col) {
	$str = '';
	for($i=0;$i<count($ARR);$i++) {
		$str .= $ARR[$i][$col];
		if($i < (count($ARR)-1)) { $str .= ', '; }
	}
	return $str;	
}