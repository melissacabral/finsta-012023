<?php 
/**
 *  configure error display (production vs development)
 */

/* ------------------configure these values----------------- */


define('DEBUG_MODE', true);
define( 'USERNAME_MIN', 5 );
define( 'USERNAME_MAX', 30 );
define( 'PASSWORD_MIN', 8 );

//Database Credentials
define('DB_USER', 'finsta_012023');
define('DB_HOST', 'localhost');
define('DB_PASSWORD', 'Bi1N76Vr@A*aL)7-');
define('DB_NAME', 'melissa_finsta_012023');



/* -------------------------stop editing------------------------ */


/* DISPLAY ERRORS
On a development server
	error_reporting should be set to E_ALL value;
	display_errors should be set to 1
	log_errors could be set to 1

On a production server
	error_reporting should be set to E_ALL value;
	display_errors should be set to 0
	log_errors should be set to 1
*/
if(DEBUG_MODE){
	//development
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	ini_set('log_errors', 1);
}else{
	//production
	ini_set('display_errors', 0);
	ini_set('log_errors', 1);
}


/**
 * Connect to our Database
 * @link https://phpbestpractices.org/#mysql
 */
$host = DB_HOST;
$database_name = DB_NAME;
$database_user = DB_USER;
$database_password = DB_PASSWORD;

$DB = new PDO( "mysql:host=$host;dbname=$database_name;charset=utf8mb4",
                    $database_user,
                    $database_password,
                    array(
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_PERSISTENT => false
                    )
                );




