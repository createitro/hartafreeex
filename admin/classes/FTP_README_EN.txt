===== BASIC DOCUMENTATION FOR FTP_OBJECT CLASS =====

0.- INTRODUCTION AND EXTENTS

This class is designed as an easy wrapper for FTP connection management, allowing 
for encapsulated management of FTP commands and results.

1.- BASICS OF OPERATION

The class is a wrapper for all common (non blocking support not yet there, but i'll be adding it someday)

PUBLIC INTERFACE:

VARIABLES:

* $connected : boolean
	This variable determines if connection was successful or not

METHODS:

* function FTP($server,$user,$pass,$debug=0,$port=21,$timeout=90) ;
  The constructor:
		$server-> string with ftp server ip or name
		$user-> string with a valid ftp user name
		$pass-> string with a valid pass for the given user
		$debug-> debug mode, see log_defs.inc for predefined values
		$port-> port for the server
		$timeout-> seconds for the timeout 
	This function attempts a connection to the server and sets coonected to true if connection
	was successful.  This function also initializes logging operations if needed.
	
* function destroy() ;
	The destructor:
		NO PARAMS
	This function closses the FTP channel and the log file if needed.
	
* function lcd($dirname) ;
	Changing local directory:
		$dirname-> the new directory to switch to
	This function returns TRUE on success or FALSE otherwise

* function cd($dirname) ;
	Changing remote directory:
		$dirname-> the new directory to switch to
	This function returns TRUE on success or FALSE otherwise
	
* function cdup() ;
  Moving remote directory up:
		NO PARAMS
	This function is equivalent to cd("../") with same results
	
* function lmd($dirname,$mode=0755) ;
	Creating a local directory:
	  $dirname-> the name for the new directory to create
		$mode-> octal value for the mode, defaults to xrw x-- x--
	This function creates a new directory, returns TRUE on succes or FALSE otherwise

* function md($dirname) ;
	Creating a remote directory:
	  $dirname-> the name for the new directory to create
	This function creates a new directory, returns TRUE on succes or FALSE otherwise

* function lrd($dirname) ;
	Removing a local directory:
		$dirname-> the name for the directory to remove
	This function removes a directory, returns TRUE on success or FALSE otherwise.  To 
	succed the directory must be empty.
	
* function rd($dirname) ;
	Removing a remote directory:
		$dirname-> the name for the directory to remove
	This function removes a directory, returns TRUE on success or FALSE otherwise.  To 
	succed the directory must be empty.
	
* function ldirlist() ;
	Listing local directory contents:
		NO PARAMS
	This function just lists every file/dir (no distinction yet...) in the current directory

* function dirlist() ;
	Listing remote directory contents:
		NO PARAMS
	This function just lists every file/dir (no distinction yet...) in the current directory

* function del($filename) ;
  Delete a remote file:
		$filename-> file to delete
	This function deletes the remote file, returns TRUE on success or FALSE otherwise.

* function ldel($filename) ;
  Delete a local file:
		$filename-> file to delete
	This function deletes the local file, returns TRUE on success or FALSE otherwise.
	
* function put($filename,$mode=0)
	Putting a file in the remote server:
		$filename-> filename (in local drive and will be used as well on remote)
		$mode-> Transfer mode 0->binary 1->ascii
	Copy $filename to server with the same name, returning TRUE on success or FALSE otherwise
	
* function get($filename,$mode=0)
	Retrieving a file from the remote server:
		$filename-> filename (in remote drive and will be used as well on local)
		$mode-> Transfer mode 0->binary 1->ascii
	Copy $filename to local directory with the same name, returning TRUE on success or FALSE otherwise

* function ren($orig,$dest)	
	Changing a file/directory name in the remote server:
		$orig-> original name
		$dest-> new name
	Change the name, returning TRUE on success or FALSE otherwise
	  
2.- Future releases and WIP

	I'll be adding new features, like non-blocking transfers and extras for dirlists... I have also 
	a small JavaScript based IDE for ftp management, but I don't think I'll be working too actively
	on it, as I have plenty of work to do... any suggestions and comments will be appreciated.

3.- Additional notes

	This class requires various levels of permissions on both filesystems, local and remote, to work
	fully, so check permissions if strange errors ocurr.
	
	CHMOD commands have not been added but will be possibly added soon.
	
4.- Contact information

  Carlos Falo Hervás
  carles@bisinteractive.net
  http://www.bisinteractive.net

  C/Manila 54-56 Esc. A Ent. 4ª
  08034 Barcelona Spain

  Phone: +34 9 3 2063652
  Fax:	 +34 9 3 2063689
