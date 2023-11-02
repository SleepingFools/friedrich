<?php
class DB{
	static $conn = null;

	static function getConnection(){
		if(self::$conn == null){
			$db = new PDO('mysql:host=localhost;dbname=friedrich', 'root', '', 
				array(
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
					PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				)
			);
			self::$conn = $db;
		}
		else{
			$db = self::$conn;
		}
		
		return $db;
	}
}
?>