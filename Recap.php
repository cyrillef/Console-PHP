<?php
/*
 Copyright (c) Autodesk, Inc. All rights reserved 

 PHP Autodesk ReCap Console Sample
 by Cyrille Fauvel - Autodesk Developer Network (ADN)
 August 2013

 Permission to use, copy, modify, and distribute this software in
 object code form for any purpose and without fee is hereby granted, 
 provided that the above copyright notice appears in all copies and 
 that both that copyright notice and the limited warranty and
 restricted rights notice below appear in all supporting 
 documentation.

 AUTODESK PROVIDES THIS PROGRAM "AS IS" AND WITH ALL FAULTS. 
 AUTODESK SPECIFICALLY DISCLAIMS ANY IMPLIED WARRANTY OF
 MERCHANTABILITY OR FITNESS FOR A PARTICULAR USE.  AUTODESK, INC. 
 DOES NOT WARRANT THAT THE OPERATION OF THE PROGRAM WILL BE
 UNINTERRUPTED OR ERROR FREE.
 
 */
require 'vendor/autoload.php' ;
use GuzzleHttp\Client ;
//use GuzzleHttp\Subscriber\Oauth\Oauth1 ;
require_once ('UserSettings.php') ;
require_once ('oAuth4ReCap.php') ;
require_once ('AdskReCap.php') ;

// http://www.php.net/manual/en/function.getopt.php
$options =getopt("c:i:p:rdh") ; //var_dump ($options) ;
if ( array_key_exists ('h', $options) !== false ) {
	echo "\nUsage:    ReCap [-d] [-r] [-h] [-i photosceneid] [-c command] [-p photo(s)]   \n" ;
	
	echo "\n-r\tRefresh Access token only\n" ;
	echo "-d\tDebug mode. Display the RESTful response\n" ;

	echo "-c\tCommand list\n" ;
	echo "\t   version - Displays the current ReCap server version\n" ;
	echo "\t   current - Displays the current photosceneid in use\n" ;
	echo "\t   create - Create a new Photoscene\n" ;
	echo "\t   set - Set the current Photoscene ID - requires -i option\n" ;
	echo "\t   release - Release the current photosceneid\n" ;
	echo "\t   list - List all photoscenes present on your account\n" ;
	echo "\t   properties - Displays current Photoscene properties\n" ;
	echo "\t   upload - Upload photo(s) on your current Photoscene - requires -p option (could be a single file, a folder, or a search string)\n" ;
	echo "\t   start - Launch your Photoscene\n" ;
	echo "\t   progress - Report progress on processing the Photoscene\n" ;
	echo "\t   result - Get the result\n" ;
	echo "\t   delete - Delete the Photoscene and resources from server\n" ;
	
	echo "-h\tHelp - this message\n" ;
	exit ;
}

//- Get & Refresh our access tokens (always)
$fname =realpath(dirname(__FILE__)) . '/oauth.txt' ;
if ( !file_exists ($fname) ) {
	echo "OAuth\nYou need to log first using the oAuthLoh.php script \n" ;
	exit ;
}
include ('oAuthRefresh.php') ;
if ( array_key_exists ('r', $options) !== false )
	exit ;

// Create our ReCap client
$recap =new AdskReCap (ReCapClientID, $access, array_key_exists ('d', $options)) ;
echo "\n\nReCap\n\n" ;

//- Requesting the ReCap service/date to start and check our connection/authentication
//- We always do regardless of the command line option to version our connection
GetReCapTime () ;

$photosceneid =null ;
if ( isset ($options ['c']) ) {
	$rname =realpath(dirname(__FILE__)) . '/recap.txt' ;
	if ( file_exists ($rname) )
		$photosceneid =file_get_contents ($rname) ;
	switch ( $options ['c'] ) {
		case 'version':
			GetReCapVersion () ;
			break ;
		case 'current':
			echo "Your current Photosceneid is: {$photosceneid}\n" ;
			break ;
		case 'release':
			if ( file_exists ($rname) )
				unlink ($rname) ;
			echo "Your current Photosceneid is now released\n" ;
			break ;
		case 'create':
			$photosceneid =CreateReCapPhotoscene () ;
			file_put_contents ($rname, $photosceneid) ;
			echo "Your new current Photosceneid is: {$photosceneid}\n" ;
			break ;
		case 'set':
			if ( !isset ($options ['i']) ) {
				echo "Error: missing argument -i, see help for details\n" ;
				exit ;
			}
			$photosceneid =$options ['i'] ;
			file_put_contents ($rname, $photosceneid) ;
			echo "Your current Photosceneid is now: {$photosceneid}\n" ;
			break ;
			
		case 'list':
			ListScenes () ;
			break ;
		case 'properties':
			if ( empty ($photosceneid) ) {
				echo "You need to specify a Photoscene ID\n" ;
				exit ;
			}
			echo "Working with ReCap Photoscene: {$photosceneid}\n" ;
			GetSceneProperties ($photosceneid) ;
			break ;
		case 'upload':
			if ( !isset ($options ['p']) ) {
				echo "Error: missing argument -p, see help for details\n" ;
				exit ;
			}
			if ( empty ($photosceneid) ) {
				echo "You need to specify a Photoscene ID\n" ;
				exit ;
			}
			echo "Working with ReCap Photoscene: {$photosceneid}\n" ;
			UploadPhotos ($photosceneid, $options ['p']) ;
			break ;
		case 'start':
			echo "Working with ReCap Photoscene: {$photosceneid}\n" ;
			if ( empty ($photosceneid) ) {
				echo "You need to specify a Photoscene ID\n" ;
				exit ;
			}
			LaunchScene ($photosceneid) ;
			break ;
		case 'progress':
			echo "Working with ReCap Photoscene: {$photosceneid}\n" ;
			if ( empty ($photosceneid) ) {
				echo "You need to specify a Photoscene ID\n" ;
				exit ;
			}
			GetSceneProgress ($photosceneid) ;
			break ;
		case 'result':
			echo "Working with ReCap Photoscene: {$photosceneid}\n" ;
			if ( empty ($photosceneid) ) {
				echo "You need to specify a Photoscene ID\n" ;
				exit ;
			}
			GetSceneResult ($photosceneid) ;
			break ;
		case 'delete':
			echo "Working with ReCap Photoscene: {$photosceneid}\n" ;
			if ( empty ($photosceneid) ) {
				echo "You need to specify a Photoscene ID\n" ;
				exit ;
			}
			if ( DeleteScene ($photosceneid) ) {
				unlink ($rname) ;
				echo "Your current Photosceneid is now released\n" ;
			}
			break ;
			
		default:
			echo "Invalid command\n" ;
	}
}

//- Done
echo "\n\nDone.\n" ;
exit ;

function GetReCapTime () {
	global $recap ;
	//- Requesting the ReCap service/date to start and check our connection/authentication
	echo "Verifying ReCap Server connection - " ;
	if ( $recap->ServerTime () == false ) {
		echo "Connection to ReCap Server failed!\n" ;
		exit ;
	}
	$xml =$recap->xml () ;
	echo "service/date response: {$xml->date}\n" ;
}

function GetReCapVersion () {
	global $recap ;
	//- Requesting the ReCap version
	if ( $recap->Version () == false ) {
		echo "version - Failed to get a valid response from the ReCap server!\n" ;
		exit ;
	}
	$xml =$recap->xml () ;
	echo "version response: {$xml->version}\n" ;
}

function CreateReCapPhotoscene () {
	global $recap ;
	if ( $recap->CreateSimplePhotoscene ("obj", "7") == false ) {
		echo "photoscene - Failed to get a valid response from the ReCap server!\n" ;
		exit ;
	}
	$xml =$recap->xml () ;
	$photosceneid =$xml->Photoscene->photosceneid ;
	//echo "Your new ReCap PhotoScene: {$photosceneid}\n" ;
	return ($photosceneid) ;
}

function GetSceneProperties ($photosceneid) {
	global $recap ;
	if ( $recap->SceneProperties ($photosceneid) == false ) {
		echo "photoscene/.../properties - Failed to get a valid response from the ReCap server!\n" ;
		exit ;
	}
	$xml =$recap->xml () ;  //print_r ($xml) ;
	$ps =$xml->Photoscenes->Photoscene ; //print_r ($ps) ;
	echo "PhotoScene: {$photosceneid}\n" ;
	echo "\tName: {$ps->name}\n" ;
	echo "\tFormat: {$ps->convertFormat}\n" ;
	if ( isset ($ps->callback) && !empty ($ps->callback) ) {
		$ps->callback =urldecode ($ps->callback) ;
		echo "\tcallback: {$ps->callback}\n" ;
	}
	if ( !isset ($ps->Files) || $ps->Files->children ()->count () == 0 ) {
		echo "\tFiles: 0 files loaded\n" ;
	} else {
		$nb =$ps->Files->children ()->count () ;
		echo "\tFiles already loaded: $nb file(s)\n" ;
		foreach ( $ps->Files->children () as $f ) {
			if ( !isset ($f->filename) )
				continue ;
			echo "\t\t{$f ['pos']}: {$f->filename} [{$f->fileid}]\n" ;
			$scenefiles [] =$f->filename ;
		}
	}
	$clientStatus =$ps->clientStatus ;
	echo "\tclientStatus: {$ps->clientStatus}\n" ; // [CREATED / SENT]
	echo "\tconvertStatus: {$ps->convertStatus}\n" ; // [CREATED / ERROR]
	echo "\tstatus: {$ps->status}\n" ; // [PROCESSING / ERROR]
}

function ListScenes () {
	global $recap ;
	if ( $recap->SceneList ("userID", ReCapUserID)  == false ) {
		echo "photoscene/properties - Failed to get a valid response from the ReCap server!\n" ;
		exit ;
	}
	$xml =$recap->xml () ;
	if ( !isset ($xml->Photoscenes) || $xml->Photoscenes->children ()->count () == 0 ) {
		echo "\t0 Photoscene present on your account\n" ;
	} else {
		$nb =$xml->Photoscenes->children ()->count () ;
		echo "\tPhotoscenes already created: $nb Photoscene(s)\n" ;
		foreach ( $xml->Photoscenes->children () as $f ) {
			if ( empty ($f->deleted) || $f->deleted != "true" )
				echo "\t\t{$f->photosceneid} [{$f->status}]\n" ;
		}
		echo "\tDeleted Photoscenes\n" ;
		foreach ( $xml->Photoscenes->children () as $f ) {
			if ( !empty ($f->deleted) && $f->deleted == "true" )
				echo "\t\t{deleted} - {$f->photosceneid} [{$f->status}]\n" ;
		}
	}
}

function UploadPhotos ($photosceneid, $filesref) {
	global $recap ;
	$files =array () ;
	if ( is_dir ($filesref) ) {
		$file_ext =array ( 'jpg', 'jpeg', 'png', 'gif', 'bmp', ) ;
		foreach ( $file_ext as $ext ) {
			$images =glob ("{$filesref}/*.{$ext}"); //var_dump ($images) ;
			foreach ( $images as $img )
				$files ['file[' . count ($files) . ']'] =$img ; //- Local files
		}
	} else if ( file_exists ($filesref) ) {
		$files ['file[0]'] =$filesref ; //- Local files
	} else {
		// Assume this is the search string
		$images =glob ($filesref); //var_dump ($images) ;
		foreach ( $images as $img )
			$files ['file[' . count ($files) . ']'] =$img ; //- Local files
		if ( empty ($files) ) {
			echo "Error - folder has no image, or image does not exist!\n" ;
			return (false) ;
		}
	}
	//var_dump ($files) ;
	if ( $recap->UploadFiles ($photosceneid, $files)  == false ) {
		echo "file - Failed to get a valid response from the ReCap server!\n" ;
		exit ;
	}
	echo "File(s) uploaded:\n" ;
	$xml =$recap->xml () ;
	$xpath =$xml->xpath ("Files/file") ;
	foreach ( $xpath as $node ) {
		echo "\t{$node->filename} [{$node->fileid}]\n" ;
	}
}

function LaunchScene ($photosceneid) {
	global $recap ;
	if ( $recap->ProcessScene ($photosceneid) == false ) {
		echo "photoscene/... - Failed to get a valid response from the ReCap server!\n" ;
		exit ;
	}
	//$xml =$recap->xml () ;
	echo "Photoscene processing request sent\n" ;
}

function GetSceneProgress ($photosceneid) {
	global $recap ;
	if ( $recap->SceneProgress ($photosceneid) == false ) {
		echo "photoscene/.../progress - Failed to get a valid response from the ReCap server!\n" ;
		exit ;
	}
	$xml =$recap->xml () ; //- Photoscene->progressmsg = [Created / Processing / ERROR] / Photoscene->progress = [0..100]
	echo "photoscene/.../progress response: {$xml->Photoscene->progressmsg} {$xml->Photoscene->progress}%\n" ;
}

function GetSceneResult ($photosceneid) {
	global $recap ;
	if ( $recap->GetPointCloudArchive ($photosceneid, "obj") == false ) {
		echo "photoscene/... - Failed to get a valid response from the ReCap server!\n" ;
		exit ;
	}
	$xml =$recap->xml () ; //- Photoscene->progressmsg = [Created / Processing / ERROR] / Photoscene->progress = [0..100]
	echo "photoscene/... response: {$xml->Photoscene->scenelink} - {$xml->Photoscene->filesize}b\n" ;
	$report =file_get_contents ($xml->Photoscene->scenelink) ;
	file_put_contents ($photosceneid . ".zip", $report) ;
	echo "PhotoScene saved into: ${photosceneid}.zip\n" ;
}

function DeleteScene ($photosceneid) {
	global $recap ;
	if ( $recap->DeleteScene ($photosceneid) == false ) {
		echo "photoscene/... - Failed to get a valid response from the ReCap server!\n" ;
		exit ;
	}
	try {
		$xml =$recap->xml () ;
		if ( isset ($xml->Photoscene->deleted) ) {
			echo "My ReCap PhotoScene is now deleted\n" ;
			return (true) ;
		} else {
			echo "Failed deleting the PhotoScene and resources!\n" ;
			return (false) ;
		}
	} catch (Exception $e) {
	}
	return (false) ;
}

?>