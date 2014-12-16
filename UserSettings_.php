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

define ('DEFAULT_BROWSER' ,'"C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" ') ;

// Hard coded consumer and secret keys and base URL.
// In real world Apps, these values need to secured.
// One approach is to encrypt and/or obfuscate these values
define ('CONSUMER_KEY', 'your consumer key') ;
define ('CONSUMER_SECRET', 'your consumer secret key') ;
define ('O2_HOST', 'https://accounts.autodesk.com/') ; // Autodesk production accounts server
//define ('O2_HOST', 'https://accounts-staging.autodesk.com/') ; // Autodesk staging accounts server

// ReCap: Fill in these macros with the correct information (only the 2 first are important)
define ('ReCapAPIURL', 'http://rc-api-adn.autodesk.com/3.1/API/') ;
define ('ReCapClientID', 'your ReCap client ID') ;
//define ('ReCapKey', 'your ReCap client key') ; // not used anymore
define ('ReCapUserID', 'your ReCap user ID') ; // Needed only for using the ReCapSceneList, otherwise bail

define ('Email', 'your email address')  ; // used for notification

// Do not edit
define ('O2_REQUESTTOKEN', O2_HOST . 'OAuth/RequestToken') ;
define ('O2_ACCESSTOKEN', O2_HOST . 'OAuth/AccessToken') ;
define ('O2_AUTHORIZE', O2_HOST . 'OAuth/Authorize') ;
define ('O2_INVALIDATETOKEN', O2_HOST . 'OAuth/InvalidateToken') ;
define ('O2_ALLOW', O2_HOST . 'OAuth/Allow') ;

?>