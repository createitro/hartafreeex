<?
/*
    Name:           eMail
    Description:    Simple sending eMail in text and HTML with CC, BCC and attachment
    Version:        1.0
    last modified:  2004-05-14

    Autor:          Daniel Käfer
    Homepage:       http://www.danielkaefer.de

    Leave this header in this file!
*/

class eMail
{
    var $to = '';
    var $cc = array();
    var $bcc = array();
    var $attachment = array();
    var $boundary = "";
    var $header = "";
    var $subject = "";
    var $body = "";
	var $htmlContent = "";
	var $from = "";

    function eMail($name,$mail) {
		ini_set('include_path', '/usr/share/pear');
		ini_set('Harta FreeEx', 'harta-freeex@activewatch.ro'); 
		require_once('Mail.php');
 	    require_once('Mail/mime.php');
		require_once('Net/SMTP.php');		
		require_once('Net/Socket.php');		
		
//      $this->boundary = md5(uniqid(time()));
//        $this->from = "From: ".$name." <".$mail.">\n";
		$this->from = $name." <harta-freeex@activewatch.ro>";
		
    }

    function to($name, $mail) { 
		//$name = filter_var($name, FILTER_SANITIZE_EMAIL);
		//$mail = filter_var($mail, FILTER_SANITIZE_EMAIL);
		$this->to = $name." <".$mail.">"; 
	}
    function cc($mail) { $this->cc[] = $mail; }
    function bcc($mail) { $this->bcc[] = $mail; }
    function attachment($file) { $this->attachment[] = $file; }
    function subject($subject) { $this->subject = $subject; }
    function text($text) { }
    function html($html) { $this->htmlContent = $html; }

	function send() {
		
		 $messageMIME = new Mail_mime();
		 $messageMIME->setTXTBody(strip_tags($this->htmlContent));
		 $messageMIME->setHTMLBody($this->htmlContent);
		 $body = $messageMIME->get();
		 
		 $host = "mail.mma.ro"; 
		 $port = "25";
		 $username = "harta-freeex@activewatch.ro";
		 $password = "GhT410n1aP";
		 

		 $headersArray = array ('From' => $this->from,  'To' => $this->to, 'Subject' => $this->subject);
		 $headers = $messageMIME->headers($headersArray);
		 
		 $smtp = Mail::factory('smtp', array ('host' => $host, 'port' => $port, 'auth' => true, 'username' => $username, 'password' => $password, 'html' => true));
		 //$smtp = Mail::factory('smtp', array ('host' => $host, 'port' => $port, 'auth' => TRUE, 'USERNAME' => $username, 'PASSWORD' => $password, 'html' => true));
		 
		 $mail = $smtp->send($this->to, $headers, $body);

		 if (PEAR::isError($mail)) {
				echo("<p>  EROARE: --->  " . $mail->getMessage() . "</p>");
				return 0;
		  } else {
		    	//echo("<p>Message successfully sent to ".htmlspecialchars($this->to)."! at ".date("Y-m-d H:i:s")."</p>");
				return 1;
		  }

    }
}
?>