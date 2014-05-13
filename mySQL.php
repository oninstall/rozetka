<?php
    /**
     * Get PDO instance. 
     * 
     * @return PDO
     */
final class MySQL {

	private $dbh;
  	public function connect() {
		try {
			$connect_str = DB_DRIVER . ':host='. DB_HOSTNAME . ';dbname=' . DB_DATABASE;
			$options = array( PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
			$this->dbh = new PDO($connect_str,DB_USERNAME,DB_PASSWORD,$options);
			$this->dbh->exec("SET CHARACTER SET utf8");
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} 
		catch (PDOException $e) {
			echo "Error: " . $e->getMessage() . "<br/>";
			die;
		}
		return $this->dbh;
  	}
}
?>