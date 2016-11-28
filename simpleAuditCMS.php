<?php
/*
Simple Audit CMS: this script is intended to verify if same security configuration are applied on the CMS.

@author: Icaro Torres
@version: 0.3.0

#### CORE ####:

update 05-June-2016:
- added the function to print the results in the printer device;
##################

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

update 27-Marc-2016:
- added a function to verify TXT files in the administrative directories.
- added the PHP verification file in the fonts and images directory (wp-includes/images, wp-includes/fonts, wp-admin/fonts and wp-admin/images);

update 12-April-2016:
- added the PHP/TXT verification file in the default themes directories (wp-content/themes/);
- fix of verification of existence directory method SearchTypeFileonDir;

update 23-April-2016:
- added index.html verification in more files;

update 17-Nov-2016:
- added the verification of the permission of the .htacess and wp-config.php files;

update 27-Nov-2016:
- added the information of the site WordPress URL in the header of the PHP script;
##################

#### JOOMLA ####:

update 30-April-2016:
- added the CMS Joomla detection;
- added the plugin list directory;
- added the templates list directory;

update 01-May-2016:
-Added the Plugin and Template list, and also the backup file verification in the webroot directory where the Joomla is installed;
##################
*/

echo "<h2> #### Simple Audit CMS #### </h2> <br/>";
echo "This script is intended to verify if same security configuration are applied on the CMS. <br/>";

echo "Supported CMS: Wordpress and Joomla. <br/><br/>";

//#########################################################
echo "<b>REPORT(" . strftime('%d/%B/%Y') ."): </b> <br/> <br/>";
//#########################################################

//GLOBAL VARIABLES

$CORRECTPERM = "0600";

//CLASS

//##############
class CheckDirFile {
//Verify if the passed file has the correct permission.
        public function CheckPerm($correctperm, $dirpath){
                if(file_exists($dirpath)){
                        echo "The file $dirpath exists. ";
                        $currentperm = substr(sprintf('%o', fileperms($dirpath)), -4);
                                if($currentperm !== $correctperm){
                                        echo "The permission is not correct, $currentperm. Please, put the $correctperm permission. (OFF) <br/>";
                                } else {
                                        echo "The permission is correct, $currentperm. (OK) <br/>";
                                }
                } else {
                        echo "The file $dirpath doesn't exists (OK) <br/>";
                        }
        }

//Verify some file type in a directory.
        public function SearchTypeFileonDir ($typefile, $dirpath){
                if(file_exists($dirpath)){
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
                } else {
                        echo "The file $dirpath doesn't exists (OK) <br/>";
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

echo "<script>
function PrintPage(){
        window.print();
}
</script>
";

//##### WORDPRESS ##### BEGIN
//Wordpress instalation test.
if(file_exists('wp-admin/') and file_exists('wp-includes/') and file_exists('wp-content/')){
echo "<b> CMS: WORDPRESS </b> <br/> <br/>";


//Print Page
echo "<button onclick='PrintPage()'> Print the results </button> <br/> <br/>";

//SPECIFIC VARIABLES

$HTACCESSFILE = ".htaccess";
$WPCONFIGFILE = "wp-config.php";
$WPREADMEFILE = "readme.html";
$WPINSTALLFILE = "wp-admin/install.php";
$WPXMLRPCFILE = "xmlrpc.php";
$WPINCLUDESJSDIR = "./wp-includes/js";
$WPINCLUDESCSSDIR = "./wp-includes/css";
$WPADMINJSDIR = "./wp-admin/js";
$WPADMINCSSDIR = "./wp-admin/css";
$WPPLUGINDIR = "./wp-content/plugins/";
$WPTHEMEDIR = "./wp-content/themes/";
$WPINCLUDESIMAGESDIR = "./wp-includes/images";
$WPINCLUDESFONTSDIR = "./wp-includes/fonts";
$WPADMINIMAGESDIR = "./wp-admin/images";
$WPADMINFONTSDIR = "./wp-admin/fonts";
$WPTHEMESTWENTYTHIRTEENCSS = "./wp-content/themes/twentythirteen/css";
$WPTHEMESTWENTYTHIRTEENIMAGES = "./wp-content/themes/twentythirteen/images";
$WPTHEMESTWENTYTHIRTEENJS = "./wp-content/themes/twentythirteen/js";
$WPTHEMESTWENTYFOURTEENCSS = "./wp-content/themes/twentyfourteen/css";
$WPTHEMESTWENTYFOURTEENIMAGES = "./wp-content/themes/twentyfourteen/images";
$WPTHEMESTWENTYFOURTEENJS = "./wp-content/themes/twentyfourteen/js";
$WPTHEMESTWENTYFIFTEENCSS = "./wp-content/themes/twentyfifteen/css";
$WPTHEMESTWENTYFIFTEENJS = "./wp-content/themes/twentyfifteen/js";
$WPTHEMESTWENTYSIXTEENCSS = "./wp-content/themes/twentysixteen/css";
$WPTHEMESTWENTYSIXTEENJS = "./wp-content/themes/twentysixteen/js";
//#########################################################

//Display the current version of the Wordpress analised.

require( dirname( __FILE__ ) . '/wp-blog-header.php' );

echo "<strong>Website URL analised:</strong> " . get_site_url() . "<br/><br/>";

echo "The current version of the Wordpress is: ";
bloginfo('version');
echo ". Please, verify the latest version on the Wordpress website: <a href='https://wordpress.org/download/'> Click Here </a> <br/> <br/>";
//#########################################################

//Verify if the permission of the file "readme.html" allows to access the page by third party.

$WPauditCheck = new CheckDirFile();
$WPauditCheck->CheckPerm($CORRECTPERM, $WPREADMEFILE);

//#########################################################

//Verify if the permission of the file  "./wp-admin/install.php" allows to access the page by third party.

echo "This file ($WPINSTALLFILE) is no longer necessary, because your WordPress already been installed. ";
$WPauditCheck->CheckPerm($CORRECTPERM, $WPINSTALLFILE);

//#########################################################

//Verify if the permission of the file "xmlrpc.php" allows to access the page by third party.

$WPauditCheck->CheckPerm($CORRECTPERM, $WPXMLRPCFILE);
//#########################################################

// Verify if the permission of the file ".htaccess" allows to access the page by third party.

$WPauditCheck->CheckPerm("400", $HTACCESSFILE);

//#########################################################

// Verify if the permission of the file "wp-config.php" allows to access the page by third party.

$WPauditCheck->CheckPerm("400", $WPCONFIGFILE);

//#########################################################

//Verify if the blank index.html are in the directories to avoid directory browsing.

$indexes = array( $WPADMINJSDIR, $WPADMINCSSDIR, $WPADMINIMAGESDIR, $WPADMINFONTSDIR, $WPINCLUDESCSSDIR, $WPINCLUDESJSDIR, $WPINCLUDESIMAGESDIR, $WPINCLUDESFONTSDIR, $WPPLUGINDIR, $WPTHEMEDIR);

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
echo "<h4> Directory WP-INCLUDES: </h4>";
echo "<br/> <b> PHP type file: </b> <br/>";
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPINCLUDESJSDIR);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPINCLUDESCSSDIR);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPINCLUDESIMAGESDIR);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPINCLUDESFONTSDIR);
//#########################################################
echo "<br/> <b> TXT type file: </b> <br/>";
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPINCLUDESJSDIR);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPINCLUDESCSSDIR);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPINCLUDESIMAGESDIR);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPINCLUDESFONTSDIR);
//#########################################################

echo "<h4> Directory WP-ADMIN: </h4>";
echo "<br/> <b> PHP type file: </b> <br/>";
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPADMINJSDIR);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPADMINCSSDIR);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPADMINIMAGESDIR);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPADMINFONTSDIR);
//#########################################################
echo "<br/> <b> TXT type file: </b><br/>";
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPADMINJSDIR);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPADMINCSSDIR);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPADMINIMAGESDIR);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPADMINFONTSDIR);
//#########################################################
echo "<h4> Default Directory WP-CONTENT/THEMES: </h4>";
echo "<br/> <b> PHP type file: </b> <br/>";

//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPTHEMESTWENTYTHIRTEENCSS);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPTHEMESTWENTYTHIRTEENIMAGES);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPTHEMESTWENTYTHIRTEENJS);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPTHEMESTWENTYFOURTEENCSS);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPTHEMESTWENTYFOURTEENIMAGES);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPTHEMESTWENTYFOURTEENJS);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPTHEMESTWENTYFIFTEENCSS);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPTHEMESTWENTYFIFTEENJS);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPTHEMESTWENTYSIXTEENCSS);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("php", $WPTHEMESTWENTYSIXTEENJS);
//#########################################################

echo "<br/> <b> TXT type file: </b> <br/>";
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPTHEMESTWENTYTHIRTEENCSS);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPTHEMESTWENTYTHIRTEENIMAGES);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPTHEMESTWENTYTHIRTEENJS);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPTHEMESTWENTYFOURTEENCSS);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPTHEMESTWENTYFOURTEENIMAGES);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPTHEMESTWENTYFOURTEENJS);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPTHEMESTWENTYFIFTEENCSS);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPTHEMESTWENTYFIFTEENJS);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPTHEMESTWENTYSIXTEENCSS);
//#########################################################
$WPauditCheck->SearchTypeFileonDir("txt", $WPTHEMESTWENTYSIXTEENJS);
//#########################################################


echo "<br/> <b> Additional Recommendations: </b> <br/> <br/>";

echo "You should create your own salt hash to customize the wp-config.php file, just access the following page: <a href='https://api.wordpress.org/secret-key/1.1/salt'> Click Here </a> <br/>";

echo "<br/> Is recommended to access the following link to see the complete hardening checklist in the Wordpress: " . "<a href='https://www.owasp.org/index.php/OWASP_Wordpress_Security_Implementation_Guideline'> Click Here </a> <br/>";

echo "<br/> Is recommended also to access the following link to see the complete hardening checklist in the Wordpress: " . "<a href='https://wordpress.org/about/security/'> Click Here </a> <br/> <br/>";

//##### WORDPRESS ##### END

//##### JOOMLA ##### BEGIN
//Joomla instalation test.
} elseif (file_exists('administrator/') and file_exists('configuration.php') and file_exists('cli/')){
echo "<b> CMS: JOOMLA </b> <br/> <br/>";

//Print Page
echo "<button onclick='PrintPage()'> Print the results </button> <br/> <br/>";

//SPECIFIC VARIABLES
$JOOMLAREADME = "README.txt";
$JOOMLAPLUGINDIR = "./plugins/";
$JOOMLATEMPLATESDIR = "./administrator/templates/";

$JoomlaCheck = new CheckDirFile();

//#########################################################
//Verify if the permission of the file "README.txt" allows to access the page by third party.

$JoomlaCheck->CheckPerm($CORRECTPERM, $JOOMLAREADME);
//#########################################################
//Lists all the plugins installed in the wordpress.

echo "<br/> <br/> <b> ### Installed Plugins ### </b> <br/>";
$JoomlaCheck->ListFilesonDir($JOOMLAPLUGINDIR);
//#########################################################
//Lists all the themes installed in the wordpress.

echo "<br/> <br/> <b> ### Installed Templates ### </b> <br/>";
$JoomlaCheck->ListFilesonDir($JOOMLATEMPLATESDIR);
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
//##### JOOMLA ##### END
        } else {
        echo "<b> [WARNING] This script doesn't support this CMS installation or put this script in the web root directory of this web service/account. [WARNING] </b>";
}

?>
