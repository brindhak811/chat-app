<?php
    
/*
 * Contains common utility functions
 */
    
include_once 'MySQLDatabase.php';

// checks if the session is valid/active
// if not, redirects to login page
function is_valid_session() {
	if (!isset($_SESSION['user'])) {
		header('location:login.php');
	}
}

// creates MySQL database connection
// and returns connection string to perform queries
function dbConnect() {
	$mysql_database = new MySQLDatabase();
	$ch = $mysql_database->dbConnect();
    
	return  $ch;
}

?>