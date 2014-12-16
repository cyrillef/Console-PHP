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
 
 This sample is a modified version of the Autodesk oAuth sample that you can find here:
 https://github.com/ADN-DevTech/AutodeskOAuthSamples/tree/master/AdskOAuth%20PHP
 
 */
require_once ('UserSettings.php') ;

$access =null ;
try {
	$fname =realpath(dirname(__FILE__)) . '/oauth.txt' ;
	$access =unserialize (file_get_contents ($fname)) ;

	//- Refresh the token
	
	//- Prepare the PHP OAuth for consuming our Oxygen service
	//- Disable the SSL check to avoid an exception with invalidate certificate on the server
	$oauth =new OAuth (CONSUMER_KEY, CONSUMER_SECRET, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI) ;
	$oauth->enableDebug () ;
	$oauth->disableSSLChecks () ;
	
	$oauth->setToken ($access ['oauth_token'], $access ['oauth_token_secret']) ;
	$access =$oauth->getAccessToken (OAUTH_ACCESSTOKEN, $access ['oauth_session_handle']) ;

	echo "'oauth_token' => '{$access ['oauth_token']}',\n'oauth_token_secret' => '{$access ['oauth_token_secret']}', \n" ;

	file_put_contents ($fname, serialize ($access)) ;
}  catch (OAuthException $e) {
	echo "OAuth\n", 'Caught exception: ',  $e->getMessage (), "\n" ;
	exit ;
} catch (Exception $e) {
	echo "OAuth/AccessToken\n", 'Caught exception: ',  $e->getMessage (), "\n" ;
	exit ;
}

//- Done
//exit ; // do not exit as other scripts may include us

?>