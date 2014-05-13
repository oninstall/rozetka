<?php
/**
* Model
* 
* Save the data in the database.
* 
* @author Patenko patenkoss@gmail.com
*/
class Model {
	/**
     * @var $dbh is PDO for connect to database.
     */
    private $dbh;
	 /**
     * Connect to the database and store the data
     *
     * @param string $keyword is string for searching at Rozetka.
	 * @param string $data is string with items for save in database.
     */
    public function add_item($keyword, $data){
		$newConnect = new MySQL();
		$this->dbh = $newConnect->connect();
        $stm = $this->dbh->prepare("INSERT INTO Items (keyword, data) VALUES ('{$keyword}', '{$data}');");
        $stm->execute(array($keyword, $data));
		$this->dbh = null;
    }
}
?>