<?php
$host = "127.0.0.1";
$user = "foo";
$pass = "bar";

$localFile="/home/test1.txt";
$remoteFile="/home/test2.txt";

include "class.shell2.php";

$ssh = new shell2; // init class

// connect server
if ( $ssh->login($user,$pass,$host) ) {

	
	//SSH Command
	$ssh->exec_cmd("ls -la");
	echo $ssh->get_output();

	//SFTP Send/Upload to remote server
	//$ssh->send(localFile,remoteFile,filePermission)
	if ($ssh->send_file($localFile,$remoteFile,0755)) {
		echo "File has been uploaded\n";
    } else {
		echo $ssh->error;
	}

    //SFTP Get/Download from remote server
    if ($ssh->get_file($remoteFile,$localFile)) {
		echo "File has been downloaded\n";
    } else {
		echo $ssh->error;
	}

} else {
	echo $ssh->error;
}

?>