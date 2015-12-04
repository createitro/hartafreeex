<?php
/* ************************************************************ */
/*
*   Copyright (C) 2008 Volkan KIRIK
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
*/
/* ************************************************************ */
/*
*   SSH - SFTP (class.shell2.php)
*   v 1.0 2007-08-13
*   v 1.1 2008-05-19 -current-
*
*   Author : Volkan KIRIK
*
*   Changes in v1.1:
*
*   * Added Disconnect function
*   * Some function names changed:
*   auth to auth_pwd
*   send to send_file
*   get to get_file
*   output to get_output
*
*   Changes in v1.0:
*
*   * First release
*
*/
/* ************************************************************ */

class shell2 {
	var $conn;
	var $error;
	var $stream;
	var $sftp;

	function login($user, $pass, $host, $port=22) {
		if ($this->connect($host,$port)) {
			if ($this->auth_pwd($user,$pass)) {
				$this->sftp = ssh2_sftp($this->conn);
				//echo('login ok');
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function connect($host,$port=22) {
		if ($this->conn = ssh2_connect($host, $port)) {
			//echo('connect ok');
			
			return true;
		} else {
			$this->error = '[x] Can not connected to '.$host.':'.$port;
			return false;
		}
	}

	function auth_pwd($u,$p) {
		if (ssh2_auth_password($this->conn, $u, $p)) {
			//echo('auth ok');
			return true;
		} else {
			$this->error = 'Login Failed';
			return false;
		}
	}

	function send_file($localFile,$remoteFile,$permision) {
		$this->exec_cmd("ls -la");
//		echo $this->get_output();		
		//echo('<br />'.$localFile.'  - '.$remoteFile.'  - '.$permision.'<br />');
		
		
		$sftpStream = @fopen('ssh2.sftp://'.$this->sftp.$remoteFile, 'w');
		
		try {
		
			if (!$sftpStream) {
				throw new Exception("Could not open remote file: $remoteFile");
			}
		   
			$data_to_send = @file_get_contents($localFile);
		   
			if ($data_to_send === false) {
				throw new Exception("Could not open local file: $localFile.");
			}
		   
			if (@fwrite($sftpStream, $data_to_send) === false) {
				throw new Exception("Could not send data from file: $localFile.");
			}
		   
			fclose($sftpStream);
						   
		} catch (Exception $e) {
			error_log('Exception: ' . $e->getMessage());
			fclose($sftpStream);
		}		
		
		
	/*	
		
		
		if (ssh2_scp_send($this->conn, $localFile, $remoteFile, $permision)) {
			return true;
		} else {
			$this->error = 'Can not transfer file 2';
			return false;
		}
		*/
	}

	function get_file($remoteFile,$localFile) {
		if (ssh2_scp_recv($this->conn, $remoteFile, $localFile)) {
			return true;
		} else {
			return false;
		}
	}

	function exec_cmd($cmd) {
		$this->stream=ssh2_exec($this->conn, $cmd);
		stream_set_blocking( $this->stream, true );
	}

	function get_output() {
		$line = '';
		while ($get=fgets($this->stream)) {
			$line.=$get;
		}
		return $line;
	}
	
	function delete_file($remoteFile) {
		if (!$this->sftp) { 
			$this->error = 'Can not open SFTP (delete)'; 
			return false;
		} else {
			ssh2_sftp_unlink($this->sftp, $remoteFile);
			return true;
		}
	}	

	function disconnect() {
		// if disconnect function is available call it..
		if ( function_exists('ssh2_disconnect') ) {
			ssh2_disconnect($this->conn);
		} else { // if no disconnect func is available, close conn, unset var
			@fclose($this->conn);
			unset($this->conn);
		}
		// return null always
		return NULL;
	}
}




?>