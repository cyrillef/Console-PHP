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

//- Oxygen
$token ='' ;
$access ='' ;

//- Prepare the PHP OAuth for consuming our Oxygen service
//- Disable the SSL check to avoid an exception with invalidate certificate on the server
$oauth =new OAuth (CONSUMER_KEY, CONSUMER_SECRET, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI) ;
$oauth->enableDebug () ;
$oauth->disableSSLChecks () ;

try {
	//- 1st leg: Get the 'request token'
	$token =$oauth->getRequestToken (OAUTH_REQUESTTOKEN) ;
	//- Set the token and secret for subsequent requests.
	$oauth->setToken ($token ['oauth_token'], $token ['oauth_token_secret']) ;
} catch (OAuthException $e) {
	echo "OAuth 1st leg\n", 'Caught exception: ',  $e->getMessage (), "\n" ;
	exit ;
} catch (Exception $e) {
	echo "OAuth 1st leg - OAuth/RequestToken\n", 'Caught exception: ',  $e->getMessage (), "\n" ;
	exit ;
}

try {
	//- 2nd leg: Authorize the token
	//- Currently, Autodesk Oxygen service requires you to manually log into the system, so we are using your default browser
	$url =OAUTH_AUTHORIZE . "?oauth_token=" . urlencode (stripslashes ($token ['oauth_token'])) ;
	//echo $url . "\n" ;
	exec (DEFAULT_BROWSER . $url) ;
	//- We need to wait for the user to have logged in
	echo "Press [Enter] when logged" ;
	$psLine =fgets (STDIN, 1024) ;
} catch (OAuthException $e) {
	echo "OAuth 2nd leg\n", 'Caught exception: ',  $e->getMessage (), "\n" ;
	exit ;
} catch (Exception $e) {
	echo "OAuth 2nd leg - OAuth/Authorize\n", 'Caught exception: ',  $e->getMessage (), "\n" ;
	exit ;
}

try {	
	//- 3rd leg: Get the 'access token' and session
	$access =$oauth->getAccessToken (OAUTH_ACCESSTOKEN) ;
	//- Set the token and secret for subsequent requests.
	$oauth->setToken ($access ['oauth_token'], $access ['oauth_token_secret']) ;
	
	//- To refresh the 'Access token' before it expires, just call again
	//- $access =$oauth->getAccessToken (BaseUrl . "OAuth/AccessToken") ;
	//- Note that at this time the 'Access token' never expires
	
	echo "'oauth_token' => '{$access ['oauth_token']}',\n'oauth_token_secret' => '{$access ['oauth_token_secret']}', \n" ;
	
	//- Save the Access tokens to disk
	$fname =realpath(dirname(__FILE__)) . '/oauth.txt' ;
	file_put_contents ($fname, serialize ($access)) ;
	
} catch (OAuthException $e) {
	echo "OAuth 3rd leg\n", 'Caught exception: ',  $e->getMessage (), "\n" ;
	exit ;
} catch (Exception $e) {
	echo "OAuth 3rd leg - OAuth/AccessToken\n", 'Caught exception: ',  $e->getMessage (), "\n" ;
	exit ;
}

//- Done
exit ;

?>