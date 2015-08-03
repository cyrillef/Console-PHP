 The PHP Console sample
=====================

This sample is a command line sample where you control the various ReCap stage individually using one of the command below.

<b>Note:</b> For using those samples you need a valid oAuth credential and a ReCap client ID. Visit this [page](http://developer-recap-autodesk.github.io/) for instructions to get on-board.


Dependencies
--------------------
This sample is dependent of the following 3rd party extensions:

* The PHP [oAuth extension](http://php.net/manual/en/book.oauth.php)
* The [Guzzle PHP](https://github.com/guzzle/guzzle) extension, document [here](http://guzzle.readthedocs.org/en/latest/)


Setup Instructions
-------------------------

1. Install PHP 5.4.0+ 
  1. make sure to include the openssl and curl extensions in your php.ini
  2. after installing PHP on your system, you may need to install the php_oauth extension if your 
      distribution does not yet include it. Copy the dll into your PHP extension folder (I.e.: <PHP folder>\ext)
      and add the following lines in your php.ini 
      ```
      Windows
		[PHP_OAUTH]
		extension=php_oauth.dll
		
      Linux and OSX hosts
		[PHP_OAUTH]
		extension=oauth.so
      ```
      Getting the PHP oauth extension:
      * Windows - You can get precompiled php_oauth.dll for Windows from: <br />
        http://windows.php.net/downloads/pecl/releases/oauth/1.2.3/
      * Linux - On Debian host console (or remotely using putty/ssh)
        ```
        pecl install oauth
     
        <may need to install pcre headers (debian based)>
        <apt-get install libpcre3-dev>
        service apache2 restart
        ```
      * Linux - On Fedora host console (or remotely using putty/ssh)
        ```
        pecl install -R /usr/lib/php oauth-0.99.9
      
        restart apache
        ```
      * Linux - On CentOS host console (or remotely using putty/ssh)
        ```
        pecl install oauth
      
        server httpd restart
        ```
      * OSX <br />
        Assuming you got PEAR, the XCode5 Command Line Tools, pecl and autoconf installed 
        ```
        brew install pcre
      
        sudo pecl install oauth
        ```

2. Install Composer
  1. go to your 'PHP' install directory,
  2. create a Composer directory (i.e. mkdir Composer)
  3. go in your Composer directory (i.e. cd Composer)
  4. execute 'php -r "readfile('https://getcomposer.org/installer');" | php'
  
3. Install Guzzle in your project
  1. go to your project directory (i.e. the 'PHP Console' directory)
  2. execute 'php composer.phar install'
  
4. Configure the sample
  1. copy UserSettings_.php into UserSettings.php
  2. edit UserSettings.php, and replace the following key strings with appropriate value
     * CONSUMER_KEY
	 * CONSUMER_SECRET
	 * ReCapClientID
	 * Email

5. If you wish to use Fiddler to debug Guzzle Curl requests, add the following option to each Guzzle call
```
   [ 'config' => [ 'curl' => [ CURLOPT_PROXY => '127.0.0.1:8888' ] ] ]
```
   an example is provided in AdskReCap.php, line #176  
   http://guzzle.readthedocs.org/en/latest/faq.html#how-can-i-add-custom-curl-options  
   http://docs.telerik.com/fiddler/configure-fiddler/tasks/configurephpcurl
	 
Usage Instructions
-------------------------

Log on the Autodesk oAuth server using the oAuthLog.php script, i.e:

	php -f oAuthLog.php
	
this command needs to be ran only once, unless your credential has expired. The command saves your access token into a file named oauth.txt that the ReCap sample will refresh and consume later.
```
Usage:    ReCap [-d] [-r] [-h] [-i photosceneid] [-c command] [-p photo(s)]

	-r	Refresh Access token only
	-d	Debug mode. Display the RESTful response
	-c	Command list
			version - Displays the current ReCap server version
			current - Displays the current photosceneid in use
			create - Create a new Photoscene
			set - Set the current Photoscene ID - requires -i option
			release - Release the current photosceneid
			list - List all photoscenes present on your account
			properties - Displays current Photoscene properties
			upload - Upload photo(s) on your current Photoscene - requires -p option (could be a single file, a folder, or a search string)
			start - Launch your Photoscene
			progress - Launch your Photoscene
			result - Get the result
			delete - Delete the Photoscene and resources from server
	-h	Help - this message
```

Typical scenario:
```
php -f oAuthLog.php
php -f ReCap.php -- -c create
php -f ReCap.php -- -c upload -p ../Examples/Tirelire
php -f ReCap.php -- -c properties
php -f ReCap.php -- -c start
php -f ReCap.php -- -c progress

once 'progress' reports no error and completion at 100%

php -f ReCap.php -- -c properties
php -f ReCap.php -- -c result
```

--------

## License

This sample is licensed under the terms of the [MIT License](http://opensource.org/licenses/MIT). Please see the [LICENSE](LICENSE) file for full details.


## Written by

Cyrille Fauvel (Autodesk Developer Network)  
http://www.autodesk.com/adn  
http://around-the-corner.typepad.com/  
