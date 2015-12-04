<?
// Logtype defs
//require_once("log_defs.inc") ;

// FTP CLIENT WRAPPER
class FTP {
		VAR $server 		= "" ;
		VAR $port 			= 21 ;
		VAR $timeout 		= 90 ;
		VAR $user 			= "" ;
		VAR $pwd 				= "" ;
		VAR $type 			= 0 ;
		VAR $mode				= true ;
		
		VAR $ftpstream 		= 0 ;
		VAR $logfile    	= "ftplog" ;
		VAR $filehdl    	= 0 ;
		VAR $msg					= "" ;
		VAR $debuglv			= 0 ;
		VAR $connected		= false ;
	
	// CREATE AND INIT THE FTP CLIENT
	function FTP($server,$user,$pass,$debug=0,$port=21,$timeout=90) {
		$this->server 		= $server ;
		$this->user				= $user ;
		$this->pass				= $pass ;
		$this->debuglv 		= $debug ;
		$this->port 			= $port ;
		$this->timeout		= $timeout ;
		$this->connected 	= $this->connect() ;
	  }
	
	// OPENING AND LOGGIN
	function connect() {
		$res = false ;
		$this->logfile_init() ;
		$this->ftpstream = @ftp_connect($this->server,$this->port,$this->timeout) ;
		if ($this->ftpstream) {
		  // Server Found
			$this->msg = "SERVER FOUND: Attempting login operation as " . $this->user ;
			$this->debug() ;
			if (@ftp_login($this->ftpstream,$this->user,$this->pass)) {
				// Login OK
				$this->msg = "Login OK: Connected to server " . $this->server . " as " . $this->user ;
				// Set passive mode on
				ftp_pasv($this->ftpstream,$this->mode) ;
				$res = true ;
			} else {
				// Login FAILED
				$this->msg = "Login incorrect" ;
				$res = false ;
			  }
		} else {
			// Server not found
			$this->msg = "Server " . $this->server . " could not be found" ;
			$res = false ;
		  }
		$this->debug() ;
		return $res ;
	  }
		
	// CLOSING
	function destroy() {
		if ($this->ftpstream) ftp_close($this->ftpstream) ;
		$this->msg = " ===== FTP SESSION CLOSED =====" .  "\r\n" ;
		$this->debug() ;
		if ($this->filehdl) fclose($this->filehdl) ;
	  }
		
	// CHANGE DIRECTORY (LOCAL)
	function lcd($dirname) {
		if ($res = chdir($dirname)) {
			$this->msg = "CHDIR OK: Current directory is " . $dirname ;
		} else {
			$this->msg = "CHDIR FAILED: Could not move to dirctory " . $dirname ;
		  }
		$this->debug() ;
		return $res ;
	  }

	// CHANGE DIRECTORY (REMOTE)
	function cd($dirname) {
		if ($res = ftp_chdir($this->ftpstream,$dirname)) {
			$this->msg = "CHDIR OK: Current directory is " . $dirname ;
		} else {
			$this->msg = "CHDIR FAILED: Could not move to dirctory " . $dirname ;
		  }
		$this->debug() ;
		return $res ;
	  }
		
	// MOVE DIRECTORY UP
	function cdup() {
		if ($res = ftp_cdup($this->ftpstream)) {
			$this->msg = "CDUP OK: Moved to parent directory" ;
		} else {
			$this->msg = "CDUP FAILED: Could not move to parent dirctory" ;
		  }
		$this->debug() ;
		return $res ;
	  }
	
	// CREATE DIRECTORY (LOCAL)
	function lmd($dirname,$mode=0755) {
		if ($res = mkdir($dirname,$mode)) {
			$this->msg = "LOCAL MKDIR OK: Created directory " . $dirname ;
		} else {
			$this->msg = "LOCAL MKDIR FAILED: Could not create dirctory " . $dirname ;
		  } ;
		$this->debug() ;
		return $res ;
	  }
	
	// CREATE DIRECTORY
	function md($dirname) {
		if ($res = ftp_mkdir($this->ftpstream,$dirname)) {
			$this->msg = "MKDIR OK: Created directory " . $dirname ;
		} else {
			$this->msg = "MKDIR FAILED: Could not create dirctory " . $dirname ;
		  }
		$this->debug() ;
		return $res ;
	  }
	
	// REMOVE DIRECTORY (LOCAL)
	function lrd($dirname) {
		if ($res = rmdir($dirname)) {
			$this->msg = "LOCAL RMDIR OK: Deleted directory " . $dirname ;
			$res = true ;
		} else {
			$this->msg = "LOCAL RMDIR FAILED: Could not delete dirctory " . $dirname ;
			$res = false ;
		  }
		$this->debug() ;
		return $res ;
	  }

	// REMOVE DIRECTORY
	function rd($dirname) {
		if ($res = ftp_rmdir($this->ftpstream,$dirname)) {
			$this->msg = "RMDIR OK: Deleted directory " . $dirname ;
		} else {
			$this->msg = "RMDIR FAILED: Could not delete dirctory " . $dirname ;
		  }
		$this->debug() ;
		return $res ;
	  }
	
	// LIST CURRENT DIRECTORY (LOCAL)
	function ldirlist() {
		$d = opendir('.') ;
		while ($f = readdir($d))
		  echo $f . "<br>" ;
		closedir($d) ;
	  }
	
	// LIST CURRENT DIRECTORY (NO FILE INFO)
	function dirlist() {
		//list current dir
		$l = ftp_nlist($this->ftpstream,"./viatjar") ;
		if ($l===false)
			$this->msg = "DIRLIST FAILED: Could not get the file list" ;
		else {
			$this->msg = "DIRLIST OK: Listing files" ;
			foreach ($l as $f)
				echo $f . "<br>" ;
			}
		$this->debug() ;
	  }
	
	// DELETE A FILE
	function del($filename) {
		$res = ftp_delete($this->ftpstream,$filename) ;
		if ($res) $this->msg = "DELETE OK: File " . $filename . " was successfully deleted" ;
		else $this->msg = "DELETE FAILED: File " . $filename . " could not be deleted" ;
		$this->debug() ;
		return $res ;
	  }
	
	// DELETE A FILE (LOCAL)
	function ldel($filename) {
		$res = @unlink($filename) ;
		if ($res) {
			$this->msg = "LOCAL DELETE OK: File " . $filename . " was successfully deleted" ;
			$res = true ;
		}	else {
			$this->msg = "LOCAL DELETE FAILED: File " . $filename . " could not be deleted" ;
			$res = false ;
			}
		$this->debug() ;
		return $res ;
	  }
	
	// UPLOAD A FILE
	function put($filename, $mode=0) {
		$res = false ;
		switch ($mode) {
			case 0:
				$m = FTP_BINARY ;
			break ;
			case 1:
				$m = FTP_ASCII ;
			break ;
		  }
		$res = ftp_put($this->ftpstream,$filename,$filename,$m) ;
		if ($res) $this->msg = "PUT OK: File " . $filename . " was successfully stored" ;
		else $this->msg = "PUT FAILED: File " . $filename . " could not be sotred" ;
		$this->debug() ;

		return $res ;
	  }
	
	// RENAME A FILE/DIR
	function ren($orig,$dest) {
		$res = ftp_rename($this->ftpstream,$orig,$dest) ;
		return $res ;
	  }
		
	// GET A FILE
	function get($filename,$mode) {
		$res = false ;
		switch ($mode) {
			case 0:
				$m = FTP_BINARY ;
			break ;
			case 1:
				$m = FTP_ASCII ;
			break ;
		  }
		$res = ftp_get($this->ftpstream,$filename,$filename,$m) ;
		if ($res) $this->msg = "GET OK: File " . $filename . " was successfully recovered" ;
		else $this->msg = "GET FAILED: File " . $filename . " could not be recovered" ;
		$this->debug() ;
		return $res ;
	  }
	
	// LOGFILE INITIALIZATION
  function logfile_init()
	  {
		$fechagm = gmmktime() ;
	  $fecha = getdate($fechagm) ;
    $this->msg = date("d/m/Y - H:i:s") . " ===== FTP SESSION STARTED =====" .  "\r\n" ;
    switch ($this->debuglv) {
      case 0: // NO LOG OPERATIONS
              break ;
      case 1: // SCREEN OUTPUT
              break ;
      case 2: // SILENT OUTPUT (<!-- -->)
              break ;
      case 3: // FILE OUTPUT
		          $this->logfile = $this->logfile . "-" . $fecha["mon"] . "-" . $fecha["year"] . ".txt" ;
              $this->filehdl = fopen($this->logfile,'a') ;
              if (!$this->filehdl) {
           		  echo "<!-- UNABLE TO OPEN SPECIFIED LOG FILE " . $this->logfile . " -->" ;
                $this->debuglv-- ;
                $this->logfile_init() ;
                }
              break ;
		  }
		$this->debug() ;
		}
	
	// LOGFILE CLOSE
	function logfile_close()
	  {
		if ($this->filehdl) {
      // If we opened a file to log operations need to close it
		  fclose($this->filehdl) ;
			}
		}
	
	// LOGGING
	function debug()
	  {
    switch ($this->debuglv) {
      case 0: // NO LOG OPERATIONS
              break ;
      case 1: // SCREEN OUTPUT
              echo '<br>DEBUG: ' . $this->msg . '<BR>' ;
              break ;
      case 2: // SILENT OUTPUT (<!-- -->)
              echo "\n<!-- DEBUG: " . $this->msg . "-->\n" ;
              break ;
      case 3: // FILE OUTPUT
              fwrite($this->filehdl,$this->msg) ;
              break ;
      }
		}
  }
?>
