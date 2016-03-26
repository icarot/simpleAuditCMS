<?php 
/*
Simple Audit CMS: this script is intended to verify if same security configuration are applied on the CMS.

@author: Icaro Torres
@version: 0.2.0

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

update 25-Marc-2016:
- Created a class with a method to verify a file permission based in the security recommendation.
- Created a method in a class to verify some kind of file on a directory. 
- Created a method in a class to list files in a directory. Cleaning up the code, avoiding to repeat the same code in similar functions that test different files and directories;
##################

TODO:

- Function to verify TXT files in the administrative directories of Wordpress;
- Change all the function repeated into PHP OOP class;
*/

echo "<h2> #### Simple Audit CMS #### </h2> <br/>";
echo "This script is intended to verify if same security configuration are applied on the CMS. <br/>";

echo "Supported CMS: Wordpress. <br/><br/>";

//#########################################################
echo "<b>REPORT(" . strftime('%d/%B/%Y') ."): </b> <br/> <br/>";
//#########################################################

//GLOBAL VARIABLES

$CORRECTPERM = "0600";
$READMEFILE = "readme.html";
$INSTALLWPFILE = "wp-admin/install.php";
$XMLRPCFILE = "xmlrpc.php";
$WPINCLUDESJSDIR = "./wp-includes/js";
$WPINCLUDESCSSDIR = "./wp-includes/css";
$WPADMINJSDIR = "./wp-admin/js";
$WPADMINCSSDIR = "./wp-admin/css";
$WPPLUGINDIR = "./wp-content/plugins/";
$WPTHEMEDIR = "./wp-content/themes/";

//CLASS

class CheckDirFile {
//Verify if the passed file has the correct permission.
//##############
	public function CheckPerm($correctperm, $dirpath){
		if(file_exists($dirpath)){
			echo "The file $dirpath exists. ";
			$currentperm = substr(sprintf('%o', fileperms($dirpath)), -4);
				if($currentperm !== $correctperm){
					echo "The permission is not correct, $currentperm. Please, put the 0600 permission. (OFF) <br/>";
				} else {
					echo "The permission is correct, $currentperm. (OK) <br/>";
				}
		} else {
			echo "The file $dirpath doesn't exists (OK) <br/>";
			}
	}

//Verify some file type in a directory.	
	public function SearchTypeFileonDir ($typefile, $dirpath){
		$filelistdir = scandir("$dirpath");

		echo "<br/> <b> Exists " . strtoupper($typefile) . " file in the directory ($dirpath)? </b> <br/>";
		$negativeanswer = 0;
		foreach( $filelistdir as $files ){
			if(!is_dir($files) and $files != "." and $files != ".."){
			$ext = pathinfo($files, PATHINFO_EXTENSION);
			if($ext == $typefile){
				echo "Please, verify this " . strtoupper($typefile) . " file in the diretory: " . $files . " (OFF) <br/>";
				$negativeanswer++;
			}
			}
		}
		if ($negativeanswer == 0){
			echo "No! (OK) <br/>";
		}
	}
	
//List all files in a directory.
	public function ListFilesonDir ($dirpath){
		
		$listfile = scandir($dirpath);
		foreach( $listfile as $file ){
			$file_full_path = "$dirpath$file/";
			if(is_dir($file_full_path) and $file != "." and $file != ".."){
				echo '- ' . $file . '<br/>';
			}	
		}
	}
//##############
	
}


//##### WORDPRESS ##### BEGIN
//Wordpress instalation test.
if(file_exists('wp-admin/') and file_exists('wp-includes/') and file_exists('wp-content/')){

//#########################################################

//Display the current version of the Wordpress analised.

require( dirname( __FILE__ ) . '/wp-blog-header.php' );

echo "The current version of the Wordpress is: "; 
bloginfo('version');
echo ". Please, verify the latest version on the Wordpress website: <a href='https://wordpress.org/download/'> Click Here </a> <br/> <br/>";
//#########################################################

//Verify if the permission of the file "readme.html" allows to access the page by third party.

$WPauditCheck = new CheckDirFile();
$WPauditCheck->CheckPerm($CORRECTPERM, $READMEFILE);

//#########################################################

//Verify if the permission of the file  "./wp-admin/install.php" allows to access the page by third party.

$WPauditCheck->CheckPerm($CORRECTPERM, $INSTALLWPFILE);
//#########################################################

//Verify if the permission of the file xmlrpc.php allows to access the page by third party.

$WPauditCheck->CheckPerm($CORRECTPERM, $XMLRPCFILE);
//#########################################################

//Verify if the blank index.html are in the directories to avoid directory browsing.

$indexes = array("wp-includes/", "wp-content/", "wp-content/themes/", "wp-content/uploads/", "wp-content/plugins/");
echo "<br/> <b> ### Browsing Directory ### </b> <br/>";
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


echo "<br/> <br/> <b> ### Installed Plugins ### </b> <br/>";
$WPauditCheck->ListFilesonDir($WPPLUGINDIR);
//#########################################################

//Lists all the themes installed in the wordpress.

echo "<br/> <b> ### Installed Themes ### </b> <br/>";
$WPauditCheck->ListFilesonDir($WPTHEMEDIR);
//#########################################################

//Verify if exists backup file "tar.gz" and ".zip" (etc) or backup of the database. 
$filelistwebrootdir = scandir("./");
echo "<br/> <b> Have compressed or SQL files in the main directory? </b> <br/> <br/>";
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

$WPauditCheck->SearchTypeFileonDir("php", $WPINCLUDESJSDIR);
//#########################################################

//Verify if exists ".php" files in the CSS directory. 

$WPauditCheck->SearchTypeFileonDir("php", $WPINCLUDESCSSDIR);
//#########################################################

//Verify if exists ".php" files in the JS directory. 

$WPauditCheck->SearchTypeFileonDir("php", $WPADMINJSDIR);
//#########################################################

//Verify if exists ".php" files in the CSS directory. 

$WPauditCheck->SearchTypeFileonDir("php", $WPADMINCSSDIR);
//#########################################################

echo "<br/> <b> Additional Recommendations: </b> <br/> <br/>";

echo "You should create your own salt hash to customize the wp-config.php file, just access the following page: <a href='https://api.wordpress.org/secret-key/1.1/salt'> Click Here </a> <br/>";

echo "<br/> Is recommended to access the following link to see the complete hardening checklist in the Wordpress: " . "<a href='https://www.owasp.org/index.php/OWASP_Wordpress_Security_Implementation_Guideline'> Click Here </a> <br/> <br/>";

//##### WORDPRESS ##### END
} else {
	echo "This script doesn't support this CMS installation or put this script in the web root directory of this web service/account.";
}

?>
