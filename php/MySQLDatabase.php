<?php
class MySQLDatabase
{
    // property declaration
    private $db_host;
    private $db_user; 
    private $db_password; 
    private $db_instance;

    // constructor declaration
    public function __construct () {
        $this->db_host = "127.0.0.1";
        $this->db_user = "scuchatuser";
        $this->db_password = "scuchatuser";
        $this->db_instance = "scuchat";
    }

    // connects to MySQL database
	public function dbConnect() {
		$ch = mysqli_connect($this->db_host, $this->db_user, $this->db_password, $this->db_instance);
		if (!$ch) {
			die('DB Error: ' . mysqli_connect_errno() . " " . mysqli_connect_error());
		}
		return $ch;
     }
}
?>
