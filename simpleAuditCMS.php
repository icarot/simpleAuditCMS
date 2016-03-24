<?php 
/*
Simple Audit CMS: this script is intended to verify if same security configuration are applied on the CMS.

@author: Icaro Torres
@version: 0.1.0

#### Wordpress ####:

update 12-Marc-2016: 
- added the verification of readme.html file and it permission;
- added the index.html file verification in the administration directories of the Wordpress;

update 13-Marc-2016:
- added the plugin list directory;

update 14-Marc-2016:
- added the theme list directory;

update 16-Marc-2016:
- added the SSL connection verification;
- added the XML-RPC file permission verification;

update 18-Marc-2016:
- added the compressed or sql files verification on the main directory;
- added the PHP verification file in the JS directory (wp-includes and wp-admin);
- added the PHP verification file in the CSS directory (wp-includes and wp-admin);

update 19-Marc-2016:
- added the "wp-admin/install.php" file permission verification;

update 23-Marc-2016:
- added the version verification of the Wordpress;
- Wordpress instalation verification;
##################

*/

echo "<h2> #### Simple Audit CMS #### </h2> <br/>";
echo "This script is intended to verify if same security configuration are applied on the CMS. <br/>";

echo "Supported CMS: Wordpress. <br/><br/>";

//#########################################################
echo "<b>REPORT(" . strftime('%d/%B/%Y') ."): </b> <br/> <br/>";
//#########################################################

//##### WORDPRESS ##### BEGIN
//Wordpress instalation test.
if(file_exists('wp-admin/') and file_exists('wp-includes/') and file_exists('wp-content/')){

//#########################################################

//Display the current version of the Wordpress analised.

require( dirname( __FILE__ ) . '/wp-blog-header.php' );

echo "The current version of the Wordpress is: "; 
bloginfo('version');
echo ". Please, verify the latest version on the Wordpress website: <a href='https://wordpress.org/download/'> Click Here </a> <br/>";
//#########################################################

//Verify if the permission of the file  readme.html allows to access the page by third party.

$readme = 'readme.html';
if(file_exists($readme)){
	echo "<br/><br/> The file $readme exists. ";
	$permreadme = substr(sprintf('%o', fileperms($readme)), -4);
		if ($permreadme !== "0600"){
			echo "The permission is not correct, $permreadme. Please, put the 0600 permission. (OFF)";
		
		} else {
			echo "The permission is correct, $permreadme. (OK)";
		}
} else {
	echo "The file $readme doesn't exists (OK)";
}

//#########################################################

//Verify if the permission of the file  "./wp-admin/install.php" allows to access the page by third party.

$installfile = 'wp-admin/install.php';
if(file_exists($installfile)){
	echo "<br/> The file $installfile exists. ";
	$perminstallfile = substr(sprintf('%o', fileperms($installfile)), -4);
		if ($perminstallfile !== "0600"){
			echo "The permission is not correct, $perminstallfile. Please, put the 0600 permission. (OFF)";
		
		} else {
			echo "The permission is correct, $perminstallfile. (OK)";
		}
} else {
	echo "The file $readme doesn't exists (OK)";
}

//#########################################################

//Verify if the blank index.html are in the directories to avoid directory browsing.

$indexes = array("wp-includes/", "wp-content/", "wp-content/themes/", "wp-content/uploads/", "wp-content/plugins/");
echo "<br/><br/> <b> ### Browsing Directory ### </b> <br/>";
foreach ($indexes as $dirs){
	$currentdir = $dirs."index.html";
	if(!file_exists($currentdir)){
		echo "<br/>Create a index.hml file in the directory '".$dirs."'. (OFF)";
	} else {
		echo "<br/> The index.html file has already been created in the directory '". $dirs."'. (OK)";
	}
}

echo "<br/>";
//#########################################################

//Check if the Wordpress uses SSL connection.
echo "<br/> <b> It uses SSL Connection? </b> <br/>";
	if (is_ssl()){
		echo "Uses SSL connection (HTTPS). (OK)";
	} else {
		echo "Doesn't use SSL connection (HTTPS). (OFF)";
	}

//#########################################################

//Lists all the plugins installed in the wordpress.
$plugindir = './wp-content/plugins/';
$pluginlist = scandir($plugindir);
echo "<br/> <br/> <b> ### Installed Plugins ### </b> <br/>";

foreach( $pluginlist as $plugin ){
	$plugin_full_path = "$plugindir$plugin/";
	if(is_dir($plugin_full_path) and $plugin != "." and $plugin != ".."){
		echo '- ' . $plugin . '<br/>';
	}
}

//#########################################################

//Lists all the themes installed in the wordpress.
$themedir = './wp-content/themes/';
$themelist = scandir($themedir);
echo "<br/> <b> ### Installed Themes ### </b> <br/>";

foreach( $themelist as $theme ){
	$theme_full_path = "$themedir$theme/";
	if(is_dir($theme_full_path) and $theme != "." and $theme != ".."){
		echo '- ' . $theme . '<br/>';
	}
}

//#########################################################

//Verify if the permission of the file xmlrpc.php allows to access the page by third party.
$xmlrpc = 'xmlrpc.php';
echo "<br/>";
if(file_exists($xmlrpc)){
	echo "The file $xmlrpc exists. ";
	$permxmlrpc = substr(sprintf('%o', fileperms($xmlrpc)), -4);
		if ($permxmlrpc !== "0600"){
			echo "The $xmlrpc file is publicly available (for everyone), $permxmlrpc. Please, put the 0600 permission, if you don't use it at all, because it allows XML-RPC Brute Force Attack. (OFF)";
		
		} else {
			echo "The permission is correct, $permxmlrpc. This file isn't publicly available. (OK)";
		}
} else {
	echo "The file $xmlrpc doesn't exists";
}

//#########################################################

//Verify if exists backup file "tar.gz" and ".zip" (etc) or backup of the database. 
$filelistwebrootdir = scandir("./");
echo "<br/> <br/> <b> Have compressed or SQL files in the main directory? </b> <br/> <br/>";
$negativeanswer = 0;

foreach( $filelistwebrootdir as $files ){
	if(!is_dir($files) and $files != "." and $files != ".."){
		$ext = pathinfo($files, PATHINFO_EXTENSION);
		if($ext == "sql" or $ext == "zip" or $ext == "gz" or $ext == "bz2" or $ext == "tar" or $ext == "7z"){
			echo $files ." (OFF) <br/>";
			$negativeanswer++;
		}
	}
}
if ($negativeanswer == 0){
	echo "No! (OK) <br/>";
}

//#########################################################

//Verify if exists ".php" files in the JS directory. 
$phpfilelistdirjs = scandir("./wp-includes/js");

echo "<br/> <b> Exists PHP file in the JS directory (./wp-includes/js)? </b> <br/>";
$negativeanswer = 0;
foreach( $phpfilelistdirjs as $files ){
	if(!is_dir($files) and $files != "." and $files != ".."){
		$ext = pathinfo($files, PATHINFO_EXTENSION);
		if($ext == "php"){
			echo "Please, verify this PHP file in the JS diretory: " . $files . " (OFF) <br/>";
		$negativeanswer++;
		}
	}
}
if ($negativeanswer == 0){
	echo "No! (OK) <br/>";
}

//#########################################################

//Verify if exists ".php" files in the CSS directory. 
$phpfilelistdircss = scandir("./wp-includes/css");

echo "<br/> <b> Exists PHP file in the CSS directory (./wp-includes/css)? </b> <br/>";
$negativeanswer = 0;
foreach( $phpfilelistdircss as $files ){
	if(!is_dir($files) and $files != "." and $files != ".."){
		$ext = pathinfo($files, PATHINFO_EXTENSION);
		if($ext == "php"){
			echo "Please, verify this PHP file in the CSS directory: " . $files ." (OFF) <br/>";
		$negativeanswer++;
		} 
	}
}
if ($negativeanswer == 0){
	echo "No! (OK) <br/>";
}
//#########################################################

//Verify if exists ".php" files in the JS directory. 
$phpfilelistdirjs = scandir("./wp-admin/js");

echo "<br/> <b> Exists PHP file in the JS directory (./wp-admin/js)? </b> <br/>";
$negativeanswer = 0;
foreach( $phpfilelistdirjs as $files ){
	if(!is_dir($files) and $files != "." and $files != ".."){
		$ext = pathinfo($files, PATHINFO_EXTENSION);
		if($ext == "php"){
			echo "Please, verify this PHP file in the JS diretory: " . $files . " (OFF) <br/>";
		$negativeanswer++;
		}
	}
}
if ($negativeanswer == 0){
	echo "No! (OK) <br/>";
}

//#########################################################

//Verify if exists ".php" files in the CSS directory. 
$phpfilelistdircss = scandir("./wp-admin/css");

echo "<br/> <b> Exists PHP file in the CSS directory (./wp-admin/css)? </b> <br/>";
$negativeanswer = 0;
foreach( $phpfilelistdircss as $files ){
	if(!is_dir($files) and $files != "." and $files != ".."){
		$ext = pathinfo($files, PATHINFO_EXTENSION);
		if($ext == "php"){
			echo "Please, verify this PHP file in the CSS directory: " . $files ." (OFF) <br/>";
		$negativeanswer++;
		} 
	}
}
if ($negativeanswer == 0){
	echo "No! (OK) <br/> <br/>";
}
//#########################################################
echo "<br/> <b> Recommendations: </b> <br/> <br/>";

echo "You should create your own salt hash to customize the wp-config.php file, just access the following page: <a href='https://api.wordpress.org/secret-key/1.1/salt'> Click Here </a> <br/>";

echo "<br/> Is recommended to access the following link to see the complete hardening checklist in the Wordpress: " . "<a href='https://www.owasp.org/index.php/OWASP_Wordpress_Security_Implementation_Guideline'> Click Here </a> <br/> <br/>";

//##### WORDPRESS ##### END
} else {
	echo "This script doesn't support this CMS installation or put this script in the web root directory of this web service/account.";
}
?>